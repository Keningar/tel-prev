var connValidaEquipos = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Valida equipos entregados, Por favor espere!!',
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

/* Funcion que sirve para cargar pantalla y llamada ajax
 * para activacion de puerto para empresa TTCO 
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * @param       gridIndex
 */
function activarCliente(data, gridIndex){
    if(data.tipoOrden=='R'){
        Ext.Msg.alert('Mensaje','Puerto: Habilitado <br> Tipo Servicio: Reubicacion <br> Desea Continuar?', function(btn){
            if(btn=='ok'){
                Ext.get("grid").mask('Cargando...');
                Ext.Ajax.request({
                    url: activarClienteBoton,
                    method: 'post',
                    timeout: 400000,
                    params: { 
                        idServicio: data.idServicio,
                        idProducto: data.productoId,
                        perfil: data.perfilDslam,
                        login: data.login,
                        capacidad1: data.capacidadUno,
                        capacidad2: data.capacidadDos,
                        interfaceElementoId: data.interfaceElementoId,
                        ultimaMilla: data.ultimaMilla,
                        mac: "",
                        ipCpeRadio: "",
                        plan: data.nombrePlan
                    },
                    success: function(response){
                        Ext.get("grid").unmask();
                        if(response.responseText == "OK"){
            //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                            Ext.Msg.alert('Mensaje','Se Activo el Cliente: '+data.login, function(btn){
                                if(btn=='ok'){
                                    store.load();
                                    win.destroy();
                                    
                                }
                            });
                        }
                        else if(response.responseText == "NO ADMINISTRACION"){
                            Ext.Msg.alert('Mensaje ','No se tiene administracion sobre ese Elemento.' );
                        }
                        else if(response.responseText == "SIN CONEXION"){
                            Ext.Msg.alert('Mensaje ','Elemento no responde, favor revisar!' );
                        }
                        else if(response.responseText == "ERROR SCE"){
                            Ext.Msg.alert('Mensaje ','No se pudo Agregar la IP en el SCE!' );
                        }
                        else if(response.responseText == "NO EXISTE TAREA"){
                            Ext.Msg.alert('Mensaje ','No Existe la Tarea, favor revisar!' );
                        }
                        else{
                            Ext.Msg.alert('Mensaje ','No se Activo el Cliente, problemas en la Ejecucion del Script!' );
                        }
                    },
                    failure: function(result)
                    {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                }); 
            }
        });
    }
    else{
        var comboInterfaces = new Ext.data.Store({ 
            total: 'total',
            autoLoad:true,
            proxy: {
                type: 'ajax',
                url : getInterfacesElemento,
                extraParams: {idElemento: data.elementoId},
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                  [
                    {name:'idInterfaceElemento', mapping:'idInterfaceElemento'},
                    {name:'nombreInterfaceElemento', mapping:'nombreInterfaceElemento'}
                  ]
        });

        if(data.ultimaMilla=="Radio"){
            var formPanel = Ext.create('Ext.form.Panel', {

                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },

                items: [

                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Informacion Servicio',
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

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadUno',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'plan',
                                        fieldLabel: 'Plan',
                                        displayField: data.nombrePlan,
                                        value: data.nombrePlan,
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
                                        width: '30%'
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
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadUno',
                                        name: 'capacidadTres',
                                        fieldLabel: 'Capacidad Tres',
                                        displayField: data.capacidadTres,
                                        value: data.capacidadTres,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'capacidadCuatro',
                                        fieldLabel: 'Capacidad Cuatro',
                                        displayField: data.capacidadCuatro,
                                        value: data.capacidadCuatro,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'perfilDslam',
                                        fieldLabel: 'Perfil',
                                        displayField: data.perfilDslam,
                                        value: data.perfilDslam,
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
                    },//cierre de la informacion servicio/producto

                    //informacion dslam
                    {
                        xtype: 'fieldset',
                        title: 'Informacion BackBone',
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
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'dslam',
                                        fieldLabel: 'Elemento',
                                        displayField: data.elementoNombre,
                                        value: data.elementoNombre,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'ipDslam',
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
                                        xtype: 'combo',
                                        id:'comboInterfaces',
                                        name: 'comboInterfaces',
                                        store: comboInterfaces,
                                        fieldLabel: 'Interface',
                                        displayField: 'nombreInterfaceElemento',
                                        valueField: 'idInterfaceElemento',
                                        queryMode: 'local',
        //                                listeners:{
        //                                    load: function(){
        //                                        console.log("onLoad");
        //                                        var combo = Ext.getCmp('comboInterfaces');
        //                                        combo.setValue(data.interfaceElementoId);
        //                                    }
        //                                },

                                        value: data.interfaceElementoId,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'ultimaMilla',
                                        fieldLabel: 'Ultima Milla',
                                        displayField: data.ultimaMilla,
                                        value: data.ultimaMilla,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'mac',
                                        name: 'mac',
                                        fieldLabel: 'Mac',
                                        displayField: "",
                                        value: "",
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'ipCpeRadio',
                                        name: 'ipCpeRadio',
                                        fieldLabel: 'Ip Wan',
                                        displayField: "",
                                        value: "",
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                ]
                            }

                        ]
                    }//cierre informacion dslam

                ],//cierre items

                buttons: [{
                    text: 'Activar',
                    formBind: true,
                    handler: function(){
                        Ext.get(formPanel.getId()).mask('Esperando Respuesta del Elemento...');
                        var interfaceElemento = Ext.getCmp('comboInterfaces').getValue();
                        var mac = Ext.getCmp('mac').getValue();
                        var ipCpeRadio = Ext.getCmp('ipCpeRadio').getValue();

                        var validacion = false;
                        if(mac=="" || ipCpeRadio==""){
                            validacion= false;
                        }
                        else{
                            validacion= true;
                        }
        //                alert(interfaceElemento);
                        if(validacion){
                            Ext.Ajax.request({
                                url: activarClienteBoton,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    idServicio: data.idServicio,
                                    idProducto: data.productoId,
                                    perfil: data.perfilDslam,
                                    login: data.login,
                                    capacidad1: data.capacidadUno,
                                    capacidad2: data.capacidadDos,
                                    interfaceElementoId: interfaceElemento,
                                    ultimaMilla: data.ultimaMilla,
                                    mac: mac,
                                    ipCpeRadio: ipCpeRadio,
                                    plan: data.nombrePlan
                                },
                                success: function(response){
                                    Ext.get(formPanel.getId()).unmask();
                                    if(response.responseText == "OK"){
                        //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                        Ext.Msg.alert('Mensaje','Se Activo el Cliente: '+data.login, function(btn){
                                            if(btn=='ok'){
                                                win.destroy();
                                                store.load();
                                            }
                                        });
                                    }
                                    else if(response.responseText == "NO ADMINISTRACION"){
                                        Ext.Msg.alert('Mensaje ','No se tiene administracion sobre ese Elemento.' );
                                    }
                                    else if(response.responseText == "SIN CONEXION"){
                                        Ext.Msg.alert('Mensaje ','Elemento no responde, favor revisar!' );
                                    }
                                    else if(response.responseText == "ERROR SCE"){
                                        Ext.Msg.alert('Mensaje ','No se pudo Agregar la IP en el SCE!' );
                                    }
                                    else if(response.responseText == "ERROR TIPO SERVICIO"){
                                        Ext.Msg.alert('Mensaje ','El servicio no tiene TIPO SERVICIO, favor revisar!' );
                                    }
                                    else if(response.responseText == "NO EXISTE TAREA"){
                                        Ext.Msg.alert('Mensaje ','No Existe la Tarea, favor revisar!' );
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ','No se Activo el Cliente, problemas en la Ejecucion del Script!' );
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
                            Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                                    if(btn=='ok'){
                                        Ext.get(formPanel.getId()).unmask();
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
        }
        else{
            var formPanel = Ext.create('Ext.form.Panel', {

                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },

                items: [

                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Informacion Servicio',
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

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadUno',
                                        name: 'login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'plan',
                                        fieldLabel: 'Plan',
                                        displayField: data.nombrePlan,
                                        value: data.nombrePlan,
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
                                        width: '30%'
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
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadUno',
                                        name: 'capacidadTres',
                                        fieldLabel: 'Capacidad Tres',
                                        displayField: data.capacidadTres,
                                        value: data.capacidadTres,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'capacidadCuatro',
                                        fieldLabel: 'Capacidad Cuatro',
                                        displayField: data.capacidadCuatro,
                                        value: data.capacidadCuatro,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'perfilDslam',
                                        fieldLabel: 'Perfil',
                                        displayField: data.perfilDslam,
                                        value: data.perfilDslam,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'ultimaMilla',
                                        fieldLabel: 'Ultima Milla',
                                        displayField: data.ultimaMilla,
                                        value: data.ultimaMilla,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------
                                ]
                            }

                        ]
                    },//cierre de la informacion servicio/producto

                    //informacion dslam
                    {
                        xtype: 'fieldset',
                        title: 'Informacion BackBone',
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
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'dslam',
                                        fieldLabel: 'Dslam',
                                        displayField: data.elementoNombre,
                                        value: data.elementoNombre,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'ipDslam',
                                        fieldLabel: 'Ip Dslam',
                                        displayField: data.ipElemento,
                                        value: data.ipElemento,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combo',
                                        id:'comboInterfaces',
                                        name: 'comboInterfaces',
                                        store: comboInterfaces,
                                        fieldLabel: 'Interface',
                                        displayField: 'nombreInterfaceElemento',
                                        valueField: 'idInterfaceElemento',
                                        queryMode: 'local',
        //                                listeners:{
        //                                    load: function(){
        //                                        console.log("onLoad");
        //                                        var combo = Ext.getCmp('comboInterfaces');
        //                                        combo.setValue(data.interfaceElementoId);
        //                                    }
        //                                },

                                        value: data.interfaceElementoId,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'modeloELemento',
                                        fieldLabel: 'Modelo Dslam',
                                        displayField: data.modeloElemento,
                                        value: data.modeloElemento,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                ]
                            }

                        ]
                    }//cierre informacion dslam

                ],//cierre items

                buttons: [{
                    text: 'Activar',
                    formBind: true,
                    handler: function(){
                        Ext.get(formPanel.getId()).mask('Esperando Respuesta del Elemento...');
                        var interfaceElemento = Ext.getCmp('comboInterfaces').getValue();

        //                alert(interfaceElemento);
                        if(true){
                            Ext.Ajax.request({
                                url: activarClienteBoton,
                                method: 'post',
                                timeout: 800000,
                                params: { 
                                    idServicio: data.idServicio,
                                    idProducto: data.productoId,
                                    perfil: data.perfilDslam,
                                    login: data.login,
                                    capacidad1: data.capacidadUno,
                                    capacidad2: data.capacidadDos,
                                    interfaceElementoId: interfaceElemento,
                                    ultimaMilla: data.ultimaMilla
                                },
                                success: function(response){
                                    Ext.get(formPanel.getId()).unmask();
                                    if(response.responseText == "OK"){
                        //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                        Ext.Msg.alert('Mensaje','Se Activo el Cliente: '+data.login, function(btn){
                                            if(btn=='ok'){
                                                win.destroy();
                                                store.load();
                                            }
                                        });
                                    }
                                    else if(response.responseText == "NO EXISTE TAREA"){
                                        Ext.Msg.alert('Mensaje ','No Existe la Tarea, favor revisar!' );
                                    }
                                    else if(response.responseText == "ERROR TIPO SERVICIO"){
                                        Ext.Msg.alert('Mensaje ','El servicio no tiene TIPO SERVICIO, favor revisar!' );
                                    }
                                    else if(response.responseText == "SIN CONEXION"){
                                        Ext.Msg.alert('Mensaje ','No existe conexion con el Dslam, favor revisar!' );
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
                            Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                                    if(btn=='ok'){
                                        Ext.get(formPanel.getId()).unmask();
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
        }



        var win = Ext.create('Ext.window.Window', {
            title: 'Activar Cliente',
            modal: true,
            width: 620,
            closable: false,
            layout: 'fit',
            items: [formPanel]
        }).show();
    }
    
    
}

/* Funcion que sirve para cargar pantalla y llamada ajax
 * para activacion de puerto para empresa MD 
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * @param       gridIndex
 */
function activarServicioMD(data,gridIndex){
    if(data.tipoOrden=='T'){
        activarServicioTraslado(data,gridIndex);
    }
    else if(data.tipoOrden=='R'){
        Ext.Msg.alert('Mensaje','Puerto: Habilitado <br> Tipo Servicio: Reubicacion <br> Desea Continuar?', function(btn){
            if(btn=='ok'){
                Ext.get("grid").mask('Cargando...');
                Ext.Ajax.request({
                    url: activarClienteBoton,
                    method: 'post',
                    timeout: 10000000,
                    params: { 
                        idServicio: data.idServicio,
                        idProducto: data.productoId,
                        perfil: data.perfilDslam,
                        login: data.login,
                        capacidad1: data.capacidadUno,
                        capacidad2: data.capacidadDos,
                        interfaceElementoId: data.interfaceElementoId,
                        ultimaMilla: data.ultimaMilla,
                        plan: data.nombrePlan
                    },
                    success: function(response){
                        Ext.get("grid").unmask();
                        if(response.responseText == "OK"){
            //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                            Ext.Msg.alert('Mensaje','Se Activo el Cliente: '+data.login, function(btn){
                                if(btn=='ok'){
                                    store.load();
                                    win.destroy();
                                    
                                }
                            });
                        }
                        else if(response.responseText == "NO ADMINISTRACION"){
                            Ext.Msg.alert('Mensaje ','No se tiene administracion sobre ese Elemento.' );
                        }
                        else if(response.responseText == "SIN CONEXION"){
                            Ext.Msg.alert('Mensaje ','Elemento no responde, favor revisar!' );
                        }
                        else if(response.responseText == "ERROR SCE"){
                            Ext.Msg.alert('Mensaje ','No se pudo Agregar la IP en el SCE!' );
                        }
                        else if(response.responseText == "NO EXISTE TAREA"){
                            Ext.Msg.alert('Mensaje ','No Existe la Tarea, favor revisar!' );
                        }
                        else{
                            Ext.Msg.alert('Mensaje ','No se Activo el Cliente, problemas en la Ejecucion del Script!' );
                        }
                    },
                    failure: function(result)
                    {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                }); 
            }
        });
    }
    else{
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
                //console.log(datos[0].idSplitter);
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
    //                autoLoad: true,
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

                var storeModelosCpeWifi = new Ext.data.Store({  
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
    //                autoLoad: true,
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
                var strNombrePlanProd           = data.nombrePlan;
                var strEtiquetaPlanProd         = 'Plan';
                var strEtiquetaPerfilVelocidad  = 'Perfil';
                var strNombreTxtPerfilVelocidad = 'perfil';
                var strValorPerfilVelocidad     = data.perfilDslam;
                if(datos[0].strEsInternetLite === 'SI')
                {
                    strNombrePlanProd           = data.nombreProducto;
                    strEtiquetaPlanProd         = 'Producto';
                }

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
                        //informacion del servicio/producto
                        {
                            xtype: 'fieldset',
                            title: 'Informacion del Servicio',
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
            //                                id:'capacidadUno',
                                            name: 'plan',
                                            fieldLabel: strEtiquetaPlanProd,
                                            displayField: strNombrePlanProd,
                                            value: strNombrePlanProd,
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
                                            width: '30%'
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
                                            width: '30%'
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
                                            width: '30%'
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
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'ultimaMilla',
                                            name: 'ultimaMilla',
                                            fieldLabel: 'Ultima Milla',
                                            displayField: data.ultimaMilla,
                                            value: data.ultimaMilla,
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: strNombreTxtPerfilVelocidad,
                                            name: strNombreTxtPerfilVelocidad,
                                            fieldLabel: strEtiquetaPerfilVelocidad,
                                            displayField: strValorPerfilVelocidad,
                                            value: strValorPerfilVelocidad,
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '10%', border: false}
                                    ]
                                }

                            ]
                        },//cierre de la informacion servicio/producto

                        //informacion de backbone
                        {
                            xtype: 'fieldset',
                            title: 'Informacion de backbone',
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
            //                                id:'perfilDslam',
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
                                            fieldLabel: 'Splitter Elemento',
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
                                            fieldLabel: 'Splitter Interface',
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
                                            fieldLabel: 'Caja Elemento',
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
                            title: 'Informacion de los Elementos del Cliente',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 540
                            },
                            items: [
                                {
                                    xtype: 'fieldset',
                                    title: 'Informacion del Wifi',
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
                                                    queryMode: 'local',
                                                    xtype: 'textfield',
                                                    id:'serieWifi',
                                                    name: 'serieWifi',
                                                    fieldLabel: 'Serie Wifi',
                                                    displayField: "",
                                                    value: "",
                                                    loadingText: 'Buscando...',
                                                    width: '25%',
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
                                                                    var macWifi = mensaje[1];
                                                                    var modeloWifi   = mensaje[2];

                                                                    Ext.getCmp('descripcionWifi').setValue = '';
                                                                    Ext.getCmp('descripcionWifi').setRawValue('');

                                                                    Ext.getCmp('macWifi').setValue = '';
                                                                    Ext.getCmp('macWifi').setRawValue('');

                                                                    Ext.getCmp('modeloWifi').setValue = '';
                                                                    Ext.getCmp('modeloWifi').setRawValue('');

                                                                    if(status=="OK")
                                                                    {
                                                                        Ext.getCmp('descripcionWifi').setValue = descripcion;
                                                                        Ext.getCmp('descripcionWifi').setRawValue(descripcion);

                                                                        Ext.getCmp('macWifi').setValue = macWifi;
                                                                        Ext.getCmp('macWifi').setRawValue(macWifi);

                                                                        Ext.getCmp('modeloWifi').setValue = modeloWifi;
                                                                        Ext.getCmp('modeloWifi').setRawValue(modeloWifi);
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
                                                { width: '20%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'modeloWifi',
                                                    name: 'modeloWifi',
                                                    fieldLabel: 'Modelo Wifi',
                                                    displayField: "",
                                                    value: "",
                                                    width: '25%',
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'macWifi',
                                                    name: 'macWifi',
                                                    fieldLabel: 'Mac Wifi',
                                                    displayField: data.mac,
                                                    value: data.mac,
                                                    width: '25%',
                                                    listeners: {
                                                        blur: function(text){
                                                            var mac           = text.getValue();
                                                            var strModeloWifi = Ext.getCmp('modeloWifi').getValue();
                                                            
                                                            if(!Ext.isEmpty(strModeloWifi))
                                                            {
                                                                if(datos[0].strEsInternetLite === 'SI')
                                                                {
                                                                    if(mac.match("^[a-fA-F0-9]{4}[\.][a-fA-F0-9]{4}[\.]+[a-fA-F0-9]{4}$"))
                                                                    {
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert('Validacion',
                                                                                      'Mac Wifi Incorrecta (aaaa.bbbb.cccc), favor revisar!');
                                                                        Ext.getCmp('validacionMacWifi').setValue = "incorrecta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("incorrecta") ;
                                                                    }
                                                                }
                                                                else if (strModeloWifi != "RV-130")
                                                                {
                                                                    if(mac.match("c8b3.73+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("0014.d1+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("000e.dc+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("d8eb.97+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("ccb2.55+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("84c9.b2+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("fc75.16+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("20aa.4b+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("c8d7.19+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("0026.5a+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("48f8.b3+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else if(mac.match("b475.0e+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$"))
                                                                    {
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else{
                                                                        Ext.Msg.alert('Validacion','Mac Wifi Incorrecta (aaaa.bbbb.cccc), favor revisar!');
                                                                        Ext.getCmp('validacionMacWifi').setValue = "incorrecta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("incorrecta") ;
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    if(mac.match("^[a-fA-F0-9]{4}[\.][a-fA-F0-9]{4}[\.]+[a-fA-F0-9]{4}$"))
                                                                    {
                                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert('Validacion','Mac Wifi Incorrecta (aaaa.bbbb.cccc), favor revisar!');
                                                                        Ext.getCmp('validacionMacWifi').setValue = "incorrecta";
                                                                        Ext.getCmp('validacionMacWifi').setRawValue("incorrecta") ;
                                                                    }
                                                                }
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Validacion','Mac Wifi Incorrecta (aaaa.bbbb.cccc), favor revisar!');
                                                                Ext.getCmp('validacionMacWifi').setValue = "incorrecta";
                                                                Ext.getCmp('validacionMacWifi').setRawValue("incorrecta") ;
                                                            }
                                                        }
                                                    }
                                                },
                                                {
                                                    xtype: 'hidden',
                                                    id:'validacionMacWifi',
                                                    name: 'validacionMacWifi',
                                                    value: "",
                                                    width: '20%'
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    id:'descripcionWifi',
                                                    name: 'descripcionWifi',
                                                    fieldLabel: 'Descripcion Wifi',
                                                    displayField: "",
                                                    value: "",
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------
                                            ]//cierre del container table
                                        }
                                    ]//cierre del fieldset
                                },
                                {
                                    xtype: 'fieldset',
                                    title: 'Informacion del ONT',
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
                                                    queryMode: 'local',
                                                    xtype: 'textfield',
                                                    id:'serieCpe',
                                                    name: 'serieCpe',
                                                    fieldLabel: 'Serie ONT',
                                                    displayField: "",
                                                    value: "",
                                                    width: '25%',
                                                    loadingText: 'Buscando...',
                                                    listeners: {
                                                        blur: function(serie){
                                                            Ext.Ajax.request({
                                                                url: buscarCpeHuaweiNaf,
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
                                                                    var macOntNaf = mensaje[1];
                                                                    var modeloCpe   = mensaje[2];

                                                                    Ext.getCmp('descripcionCpe').setValue = '';
                                                                    Ext.getCmp('descripcionCpe').setRawValue('');

                                                                    Ext.getCmp('macCpe').setValue = '';
                                                                    Ext.getCmp('macCpe').setRawValue('');

                                                                    Ext.getCmp('modeloCpe').setValue = '';
                                                                    Ext.getCmp('modeloCpe').setRawValue('');

                                                                    if(status=="OK")
                                                                    {
                                                                        Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                                        Ext.getCmp('descripcionCpe').setRawValue(descripcion);

                                                                        Ext.getCmp('macCpe').setValue = macOntNaf;
                                                                        Ext.getCmp('macCpe').setRawValue(macOntNaf);

                                                                        Ext.getCmp('modeloCpe').setValue = modeloCpe;
                                                                        Ext.getCmp('modeloCpe').setRawValue(modeloCpe);
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
                                                { width: '20%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'modeloCpe',
                                                    name: 'modeloCpe',
                                                    fieldLabel: 'Modelo ONT',
                                                    displayField: "",
                                                    value: "",
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'macCpe',
                                                    name: 'macCpe',
                                                    fieldLabel: 'Mac ONT',
                                                    displayField: "",
                                                    value: "",
                                                    width: '25%',
                                                    listeners: {
                                                        blur: function(text){
                                                            var mac = text.getValue();
                                                            if(mac.match("c8b3.73+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("0014.d1+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("000e.dc+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("d8eb.97+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("ccb2.55+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("84c9.b2+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("fc75.16+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("20aa.4b+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("c8d7.19+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("0026.5a+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("48f8.b3+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else if(mac.match("b475.0e+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$"))
                                                            {
                                                                Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                            }
                                                            else{
                                                                Ext.Msg.alert('Validacion','Mac Ont Incorrecta (aaaa.bbbb.cccc), favor revisar!');
                                                                Ext.getCmp('validacionMacOnt').setValue = "incorrecta";
                                                                Ext.getCmp('validacionMacOnt').setRawValue("incorrecta") ;
                                                            }
                                                        }
                                                    }
                                                },
                                                {
                                                    xtype: 'hidden',
                                                    id:'validacionMacOnt',
                                                    name: 'validacionMacOnt',
                                                    value: "",
                                                    width: '20%'
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    id:'descripcionCpe',
                                                    name: 'descripcionCpe',
                                                    fieldLabel: 'Descripcion ONT',
                                                    displayField: "",
                                                    value: "",
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                            ]//cierre del container table
                                        }                


                                    ]//cierre del fieldset
                                },
                                {
                                    id: 'informacionSmartWifi',
                                    xtype: 'fieldset',
                                    title: 'Información del SmartWifi',
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
                                                    id:'serieSmartWifi',
                                                    name: 'serieSmartWifi',
                                                    fieldLabel: 'Serie SmartWifi',
                                                    displayField: "",
                                                    value: "",
                                                    width: '25%'
                                                },
                                                { width: '20%', border: false},
                                                {
                                                    queryMode: 'local',
                                                    xtype: 'combobox',
                                                    id: 'modeloSmartWifi',
                                                    name: 'modeloSmartWifi',
                                                    fieldLabel: 'Modelo SmartWifi',
                                                    displayField:'modelo',
                                                    valueField: 'modelo',
                                                    loadingText: 'Buscando...',
                                                    store: storeModelosCpe,
                                                    width: '25%',
                                                    listeners: {
                                                        blur: function(combo) {
                                                            Ext.Ajax.request({
                                                                url: buscarCpeNaf,
                                                                method: 'post',
                                                                params: {
                                                                    serieCpe: Ext.getCmp('serieSmartWifi').getValue(),
                                                                    modeloElemento: combo.getValue(),
                                                                    estado: 'PI',
                                                                    bandera: 'ActivarServicio'
                                                                },
                                                                success: function(response) 
                                                                {
                                                                    var respuesta = response.responseText.split("|");
                                                                    var status = respuesta[0];
                                                                    var mensaje = respuesta[1];

                                                                    if (status == "OK")
                                                                    {
                                                                        Ext.getCmp('descripcionSmartWifi').setValue = mensaje;
                                                                        Ext.getCmp('descripcionSmartWifi').setRawValue(mensaje);
                                                                        var arrayInformacionWifi       = mensaje.split(",");
                                                                        Ext.getCmp('macSmartWifi').setValue = arrayInformacionWifi[1];
                                                                        Ext.getCmp('macSmartWifi').setRawValue(arrayInformacionWifi[1]);
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert('Mensaje ', mensaje);
                                                                        Ext.getCmp('descripcionSmartWifi').setValue = status;
                                                                        Ext.getCmp('descripcionSmartWifi').setRawValue(status);
                                                                        Ext.getCmp('macSmartWifi').setValue = "";
                                                                        Ext.getCmp('macSmartWifi').setRawValue("");
                                                                    }
                                                                },
                                                                failure: function(result)
                                                                {
                                                                    Ext.getCmp('macSmartWifi').setValue = "";
                                                                    Ext.getCmp('macSmartWifi').setRawValue("");
                                                                    Ext.get(formPanel.getId()).unmask();
                                                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'macSmartWifi',
                                                    name: 'macSmartWifi',
                                                    fieldLabel: 'Mac SmartWifi',
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'descripcionSmartWifi',
                                                    name: 'descripcionSmartWifi',
                                                    fieldLabel: 'Descripción SmartWifi',
                                                    displayField: "",
                                                    value: "",
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                            ]//cierre del container table
                                        }                


                                    ]//cierre del fieldset
                                }
                            ]

                        },//cierre informacion de los elementos del cliente

                        //informacion del Cliente
                        {
                            xtype: 'fieldset',
                            title: 'Informacion del Cliente',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 540,
                                height: 230
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
                                            xtype: 'textareafield',
                                            id:'observacionCliente',
                                            name: 'observacionCliente',
                                            fieldLabel: 'Observacion',
                                            displayField: "",
                                            labelPad: -45,
                                            //html: '4,1', 
                                            colspan: 4,
                                            value: "",
                                            width: '87%'

                                        }
                                    ]//cierre del container table
                                }                


                            ]//cierre del fieldset
                        }//cierre informacion ont

                    ],
                    buttons: [{
                            
                        text: 'Activar',
                        formBind: true,
                        handler: function(){
                            var strSerieSmartWifi       = "";
                            var strModeloSmartWifi      = "";
                            var strMacSmartWifi         = "";
                            var strDescripcionSmartWifi = "";
                            var modeloCpe               = Ext.getCmp('modeloCpe').getValue();
                            var serieCpe                = Ext.getCmp('serieCpe').getValue();
                            var descripcionCpe          = Ext.getCmp('descripcionCpe').getValue();
                            var macCpe                  = Ext.getCmp('macCpe').getValue();

                            var modeloWifi              = Ext.getCmp('modeloWifi').getValue();
                            var serieWifi               = Ext.getCmp('serieWifi').getValue();
                            var descripcionWifi         = Ext.getCmp('descripcionWifi').getValue();
                            var macWifi                 = Ext.getCmp('macWifi').getValue();
                            
                            var modoOperacion           = Ext.getCmp('modoOperacion').getValue();
                            var numPc                   = Ext.getCmp('numeroPc').getValue();
                            var ssid                    = Ext.getCmp('ssid').getValue();
                            var password                = Ext.getCmp('password').getValue();
                            var observacion             = Ext.getCmp('observacionCliente').getValue();

                            var interfaceSplitter       = Ext.getCmp('splitterInterfaceElemento').getRawValue();
                            var validacionWifi          = Ext.getCmp('validacionMacWifi').getRawValue();
                            var validacionOnt           = Ext.getCmp('validacionMacOnt').getRawValue();

                            var validacion=false;
                            flag = 0;
                            if(serieCpe=="" || macCpe=="" || serieWifi=="" || macWifi==""){
                                validacion=false;
                            }
                            else{
                                validacion=true;
                            }
                            
                            if(descripcionCpe=="ELEMENTO ESTADO INCORRECTO" || 
                               descripcionCpe=="ELMENTO CON SALDO CERO" || 
                               descripcionCpe=="NO EXISTE ELEMENTO")
                            {
                                validacion=false;
                                flag=3;
                            }
                            if(descripcionWifi=="ELEMENTO ESTADO INCORRECTO" || 
                               descripcionWifi=="ELMENTO CON SALDO CERO" || 
                               descripcionWifi=="NO EXISTE ELEMENTO")
                            {
                                validacion=false;
                                flag=4;
                            }
                            
                            if(validacionWifi=="incorrecta" || validacionOnt=="incorrecta"){
                                validacion=false;
                                flag=1;
                            }
                            
                            if(macCpe == macWifi){
                                validacion=false;
                                flag=2;
                            }
                            
                            if (datos[0].strTieneSmartWifiRenta == "SI")
                            {
                                strSerieSmartWifi       = Ext.getCmp('serieSmartWifi').getValue();
                                strModeloSmartWifi      = Ext.getCmp('modeloSmartWifi').getValue();
                                strMacSmartWifi         = Ext.getCmp('macSmartWifi').getValue();
                                strDescripcionSmartWifi = Ext.getCmp('descripcionSmartWifi').getValue();
                                
                                if(Ext.isEmpty(strMacSmartWifi))
                                {
                                    validacion = false;
                                    flag = 5;
                                }
                                else if( strDescripcionSmartWifi == "ELEMENTO ESTADO INCORRECTO" || 
                                         strDescripcionSmartWifi == "ELMENTO CON SALDO CERO"     || 
                                         strDescripcionSmartWifi == "NO EXISTE ELEMENTO" )
                                {
                                    validacion  = false;
                                    flag = 6;
                                }
                                else if(Ext.isEmpty(strSerieSmartWifi))
                                {
                                    validacion = false;
                                    flag = 7;
                                }  
                                else if(Ext.isEmpty(strModeloSmartWifi))
                                {
                                    validacion = false;
                                    flag = 8;
                                }
                            }

                            if(validacion){
                                Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');


                                Ext.Ajax.request({
                                    url: activarClienteBoton,
                                    method: 'post',
                                    timeout: 1000000,
                                    params: { 
                                        idServicio                  : data.idServicio,
                                        idProducto                  : data.productoId,
                                        perfil                      : data.perfilDslam,
                                        login                       : data.login,
                                        capacidad1                  : data.capacidadUno,
                                        capacidad2                  : data.capacidadDos,
                                        interfaceElementoId         : data.interfaceElementoId,
                                        interfaceElementoSplitterId : interfaceSplitter,
                                        ultimaMilla                 : data.ultimaMilla,
                                        plan                        : data.planId,
                                        serieOnt                    : serieCpe,
                                        modeloOnt                   : modeloCpe,
                                        macOnt                      : macCpe,
                                        serieWifi                   : serieWifi,
                                        modeloWifi                  : modeloWifi,
                                        macWifi                     : macWifi,
                                        numPc                       : numPc,
                                        ssid                        : ssid,
                                        password                    : password,
                                        modoOperacion               : modoOperacion,
                                        observacionCliente          : observacion,
                                        strSerieSmartWifi           : strSerieSmartWifi,
                                        strModeloSmartWifi          : strModeloSmartWifi,
                                        strMacSmartWifi             : strMacSmartWifi,
                                        strTieneSmartWifiRenta      : datos[0].strTieneSmartWifiRenta,
                                        strEsInternetLite           : datos[0].strEsInternetLite
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText == "OK"){
                            //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                            Ext.Msg.alert('Mensaje','Se Activo el Cliente', function(btn){
                                                if(btn=='ok'){
                                                    win.destroy();
                                                    store.load();
                                                }
                                            });
                                        }
                                        else if(response.responseText == "NO ID CLIENTE"){
                                            Ext.Msg.alert('Mensaje ','Slot no existe, favor revise la Linea Pon donde debe enganchar el cliente!' );
                                        }
                                        else if(response.responseText == "MAX ID CLIENTE"){
                                            Ext.Msg.alert('Mensaje ','Limite de clientes por Puerto esta en el maximo, <br> Favor comunicarse con el departamento de GEPON' );
                                        }
                                        else if(response.responseText == "CANTIDAD CERO"){
                                            Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                                        }
                                        else if(response.responseText == "NO EXISTE PRODUCTO"){
                                            Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                        }
                                        else if(response.responseText == "NO EXISTE CPE"){
                                            Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                                        }
                                        else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                                            Ext.Msg.alert('Mensaje ','Equipo no esta en PENDIENTE INSTALACION/RETIRADO, favor revisar!' );
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
                                    Ext.Msg.alert("Validacion","Alguna Mac esta incorrecta, favor revisar!", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==2){
                                    Ext.Msg.alert("Validacion","Macs no pueden ser iguales, favor revisar!", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==3){
                                    Ext.Msg.alert("Validacion","Datos del Ont incorrectos, favor revisar!", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==4){
                                    Ext.Msg.alert("Validacion","Datos del Wifi incorrectos, favor revisar!", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if( flag == 5 )
                                {
                                    Ext.Msg.alert("Validación","No existe valor de Mac, favor revisar!");
                                }
                                else if( flag == 6 )
                                {
                                    Ext.Msg.alert("Validación","Datos del Wifi incorrectos, favor revisar!");
                                }
                                else if( flag == 7 )
                                {
                                    Ext.Msg.alert("Validación","Por favor ingrese la serie correspondiente!");
                                }
                                else if( flag == 8 )
                                {
                                    Ext.Msg.alert("Validación","Por favor ingrese el modelo correspondiente!");
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
                    title: 'Activar Puerto',
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
                        storeModelosCpeWifi.load({});
                    }
                });
                if (datos[0].strTieneSmartWifiRenta == "NO")
                {
                    Ext.getCmp('informacionSmartWifi').setVisible(false);
                }

                if(data.strSerieOntCliente != "")
                {
                    Ext.getCmp('serieCpe').setValue = data.strSerieOntCliente;
                    Ext.getCmp('serieCpe').setRawValue(data.strSerieOntCliente);
                    Ext.getCmp('serieCpe').focus();
                    Ext.getCmp('serieCpe').blur();
                }

                if(data.strSerieWifiCliente != "")
                {
                    Ext.getCmp('serieWifi').setValue = data.strSerieWifiCliente;
                    Ext.getCmp('serieWifi').setRawValue(data.strSerieWifiCliente);
                    Ext.getCmp('serieWifi').focus();
                    Ext.getCmp('serieWifi').blur();
                }

            }//cierre response
        });   
    }
    
}

/* Funcion que sirve para cargar pantalla y llamada ajax
 * para activacion de puerto en un olt Huawei para empresa MD 
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     4-03-2015
 * 
 * @author      Jesus Bozada <jbozada@telconet.ec>
 * @version     1.1     19-07-2016  Se agrega codigo para procesar reubicación de servicios HW
 * 
 * @param Array data        Informacion que fue cargada en el grid
 * @param       gridIndex
 */
function activarServicioHuaweiMD(data, gridIndex){
    var tipoOrden = data.tipoOrden;
        
    if(tipoOrden==="T"){
        activarServicioTraslado(data,gridIndex);
    }
    else if(tipoOrden==="R"){
          Ext.Msg.alert('Mensaje','Puerto: Habilitado <br> Tipo Servicio: Reubicacion <br> Desea Continuar?', function(btn){
            if(btn=='ok'){
                Ext.get("grid").mask('Cargando...');
                Ext.Ajax.request({
                    url: activarClienteBoton,
                    method: 'post',
                    timeout: 10000000,
                    params: { 
                        idServicio: data.idServicio,
                        idProducto: data.productoId,
                        perfil: data.perfilDslam,
                        login: data.login,
                        capacidad1: data.capacidadUno,
                        capacidad2: data.capacidadDos,
                        interfaceElementoId: data.interfaceElementoId,
                        ultimaMilla: data.ultimaMilla,
                        plan: data.nombrePlan
                    },
                    success: function(response){
                        Ext.get("grid").unmask();
                        if(response.responseText == "OK"){
                            Ext.Msg.alert('Mensaje','Se Activo el Cliente: '+data.login, function(btn){
                                if(btn=='ok'){
                                    store.load();
                                    win.destroy();
                                    
                                }
                            });
                        }
                        else if(response.responseText == "NO ADMINISTRACION"){
                            Ext.Msg.alert('Mensaje ','No se tiene administracion sobre ese Elemento.' );
                        }
                        else if(response.responseText == "SIN CONEXION"){
                            Ext.Msg.alert('Mensaje ','Elemento no responde, favor revisar!' );
                        }
                        else if(response.responseText == "ERROR SCE"){
                            Ext.Msg.alert('Mensaje ','No se pudo Agregar la IP en el SCE!' );
                        }
                        else if(response.responseText == "NO EXISTE TAREA"){
                            Ext.Msg.alert('Mensaje ','No Existe la Tarea, favor revisar!' );
                        }
                        else{
                            Ext.Msg.alert('Mensaje ','No se Activo el Cliente, problemas en la Ejecucion del Script!' );
                        }
                    },
                    failure: function(result)
                    {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                    }
                }); 
            }
        });
    }
    else if(tipoOrden==="N"){
        var tituloPanel = "Activar Servicio";
        var tipoRed     = data.strTipoRed;
        var flagclienteNodo = true
        var descripcionProducto = data.descripcionProducto;
        var booleanTipoRedGpon = false;
        if (typeof data.booleanTipoRedGpon !== "undefined"){
            booleanTipoRedGpon = data.booleanTipoRedGpon;
        }
        if(booleanTipoRedGpon){
            tituloPanel = "Activar Servicio " + data.strTipoRed;
        }
        if(tipoRed === "GPON_MPLS" && descripcionProducto === "DATOS SAFECITY"){
            flagclienteNodo = false;
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
                var strNombrePlanProd           = data.nombrePlan;
                var strEtiquetaPlanProd         = 'Plan';
                var strEtiquetaPerfilVelocidad  = 'Perfil';
                var strNombreTxtPerfilVelocidad = 'perfil';
                var strValorPerfilVelocidad     = data.perfilDslam;
                if(datos[0].strEsInternetLite === 'SI')
                {
                    strNombrePlanProd           = data.nombreProducto;
                    strEtiquetaPlanProd         = 'Producto';
                    strEtiquetaPerfilVelocidad  = 'Velocidad';
                    strNombreTxtPerfilVelocidad = 'velocidad';
                    strValorPerfilVelocidad     = data.velocidadISB + " MB";
                }
                
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
                            title: 'Informacion del Servicio',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 565,
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
                                            name: 'plan',
                                            fieldLabel: strEtiquetaPlanProd,
                                            displayField: strNombrePlanProd,
                                            value: strNombrePlanProd,
                                            hidden: booleanTipoRedGpon,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {
                                            xtype: 'textfield',
                                            id:'strTipoRed',
                                            name: 'strTipoRed',
                                            fieldLabel: 'Tipo Red',
                                            displayField: data.strTipoRed,
                                            value: data.strTipoRed,
                                            hidden: !booleanTipoRedGpon,
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
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
                                            name: 'capacidadUno',
                                            fieldLabel: 'Capacidad Uno',
                                            displayField: booleanTipoRedGpon ? data.capacidadUno + " MB" : data.capacidadUno,
                                            value: booleanTipoRedGpon ? data.capacidadUno + " MB" : data.capacidadUno,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidadDos',
                                            fieldLabel: 'Capacidad Dos',
                                            displayField: booleanTipoRedGpon ? data.capacidadDos + " MB" : data.capacidadDos,
                                            value: booleanTipoRedGpon ? data.capacidadDos + " MB" : data.capacidadDos,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidadTres',
                                            fieldLabel: 'Capacidad Int/Prom Uno',
                                            displayField: data.capacidadTres,
                                            value: data.capacidadTres,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidadCuatro',
                                            fieldLabel: 'Capacidad Int/Prom Dos',
                                            displayField: data.capacidadCuatro,
                                            value: data.capacidadCuatro,
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
                                            fieldLabel: 'Ultima Milla',
                                            displayField: data.ultimaMilla,
                                            value: data.ultimaMilla,
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id: strNombreTxtPerfilVelocidad,
                                            name: strNombreTxtPerfilVelocidad,
                                            fieldLabel: strEtiquetaPerfilVelocidad,
                                            displayField: strValorPerfilVelocidad,
                                            value: strValorPerfilVelocidad,
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '10%', border: false}
                                    ]
                                }

                            ]
                        },//cierre de la informacion servicio/producto

                        //informacion de backbone
                        {
                            xtype: 'fieldset',
                            title: 'Informacion de backbone',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 565,
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
                                            fieldLabel: 'Splitter Elemento',
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
                                            fieldLabel: 'Splitter Interface',
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
                                            fieldLabel: 'Caja Elemento',
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
                            title: 'Informacion de los Elementos del Cliente',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 565
                            },
                            items: [

                                {
                                    xtype: 'fieldset',
                                    title: 'Informacion del ONT',
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
                                                    queryMode: 'local',
                                                    xtype: 'textfield',
                                                    id:'serieCpe',
                                                    name: 'serieCpe',
                                                    fieldLabel: 'Serie ONT',
                                                    displayField: "",
                                                    value: "",
                                                    loadingText: 'Buscando...',
                                                    width: '25%',
                                                    listeners: {
                                                        blur: function(serie){
                                                            Ext.Ajax.request({
                                                                url: buscarCpeHuaweiNaf,
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
                                                                    var macOntNaf = mensaje[1];
                                                                    var modeloCpe   = mensaje[2];

                                                                    Ext.getCmp('descripcionCpe').setValue = '';
                                                                    Ext.getCmp('descripcionCpe').setRawValue('');

                                                                    Ext.getCmp('macCpe').setValue = '';
                                                                    Ext.getCmp('macCpe').setRawValue('');

                                                                    Ext.getCmp('modeloCpe').setValue = '';
                                                                    Ext.getCmp('modeloCpe').setRawValue('');

                                                                    if(status=="OK")
                                                                    {
                                                                        Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                                        Ext.getCmp('descripcionCpe').setRawValue(descripcion);

                                                                        Ext.getCmp('macCpe').setValue = macOntNaf;
                                                                        Ext.getCmp('macCpe').setRawValue(macOntNaf);

                                                                        Ext.getCmp('modeloCpe').setValue = modeloCpe;
                                                                        Ext.getCmp('modeloCpe').setRawValue(modeloCpe);
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
                                                { width: '20%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'modeloCpe',
                                                    name: 'modeloCpe',
                                                    fieldLabel: 'Modelo ONT',
                                                    displayField: "",
                                                    value: "",
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'macCpe',
                                                    name: 'macCpe',
                                                    fieldLabel: 'Mac ONT',
                                                    displayField: "",
                                                    value: "",
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                {
                                                    xtype: 'hidden',
                                                    id:'validacionMacOnt',
                                                    name: 'validacionMacOnt',
                                                    value: "",
                                                    width: '20%'
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    id:'descripcionCpe',
                                                    name: 'descripcionCpe',
                                                    fieldLabel: 'Descripcion ONT',
                                                    displayField: "",
                                                    value: "",
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                            ]//cierre del container table
                                        }                


                                    ]//cierre del fieldset
                                },
                                
                                {
                                    id: 'informacionSmartWifi',
                                    xtype: 'fieldset',
                                    title: 'Información del SmartWifi',
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
                                                    id:'serieSmartWifi',
                                                    name: 'serieSmartWifi',
                                                    fieldLabel: 'Serie SmartWifi',
                                                    displayField: "",
                                                    value: "",
                                                    width: '25%'
                                                },
                                                { width: '20%', border: false},
                                                {
                                                    queryMode: 'local',
                                                    xtype: 'combobox',
                                                    id: 'modeloSmartWifi',
                                                    name: 'modeloSmartWifi',
                                                    fieldLabel: 'Modelo SmartWifi',
                                                    displayField:'modelo',
                                                    valueField: 'modelo',
                                                    loadingText: 'Buscando...',
                                                    store: storeModelosCpe,
                                                    width: '25%',
                                                    listeners: {
                                                        blur: function(combo) {
                                                            Ext.Ajax.request({
                                                                url: buscarCpeNaf,
                                                                method: 'post',
                                                                params: {
                                                                    serieCpe: Ext.getCmp('serieSmartWifi').getValue(),
                                                                    modeloElemento: combo.getValue(),
                                                                    estado: 'PI',
                                                                    bandera: 'ActivarServicio'
                                                                },
                                                                success: function(response) 
                                                                {
                                                                    var respuesta = response.responseText.split("|");
                                                                    var status = respuesta[0];
                                                                    var mensaje = respuesta[1];

                                                                    if (status == "OK")
                                                                    {
                                                                        Ext.getCmp('descripcionSmartWifi').setValue = mensaje;
                                                                        Ext.getCmp('descripcionSmartWifi').setRawValue(mensaje);
                                                                        var arrayInformacionWifi       = mensaje.split(",");
                                                                        Ext.getCmp('macSmartWifi').setValue = arrayInformacionWifi[1];
                                                                        Ext.getCmp('macSmartWifi').setRawValue(arrayInformacionWifi[1]);
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert('Mensaje ', mensaje);
                                                                        Ext.getCmp('descripcionSmartWifi').setValue = status;
                                                                        Ext.getCmp('descripcionSmartWifi').setRawValue(status);
                                                                        Ext.getCmp('macSmartWifi').setValue = "";
                                                                        Ext.getCmp('macSmartWifi').setRawValue("");
                                                                    }
                                                                },
                                                                failure: function(result)
                                                                {
                                                                    Ext.getCmp('macSmartWifi').setValue = "";
                                                                    Ext.getCmp('macSmartWifi').setRawValue("");
                                                                    Ext.get(formPanel.getId()).unmask();
                                                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'macSmartWifi',
                                                    name: 'macSmartWifi',
                                                    fieldLabel: 'Mac SmartWifi',
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'descripcionSmartWifi',
                                                    name: 'descripcionSmartWifi',
                                                    fieldLabel: 'Descripción SmartWifi',
                                                    displayField: "",
                                                    value: "",
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                            ]//cierre del container table
                                        }                


                                    ]//cierre del fieldset
                                }
                            ]

                        },//cierre informacion de los elementos del cliente

                        //informacion del Cliente
                        {
                            xtype: 'fieldset',
                            title: 'Informacion del Cliente',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 565
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


                            ]//cierre del fieldset
                        },//cierre informacion ont

                        // inicio seccion combo tecnico
                        {
                            xtype: 'container',
                            hidden: flagclienteNodo,
                            defaults: { 
                                width: 565,
                                height: 30
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
                                        comboEmpleadoSafeCity(data),      
                                    ]
                                }

                            ]
                        },
                        {
                            xtype: 'container',
                            hidden: flagclienteNodo,
                            name: 'nuevoTecnico',
                            id         : 'nuevoTecnico',
                            items: [
                               {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                    }
                                }
                            ]
                        },
                        // fin seccion combo tecnico

                        //informacion de los elementos adicioanles en cliente
                        {
                            xtype: 'fieldset',
                            title: 'Dispositivos en Cliente',
                            hidden: flagclienteNodo,
                            defaultType: 'textfield',
                            defaults: { 
                                width: 565,
                                height: 145
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
                                        {//informacion de elemento de cliente adicional   
                                            layout: {type:'table',pack:'center',columns:2},
                                            items : [getDispositivosClienteSafeCityOnt(data)]
                                        },//cierre de informacion de elemento de cliente adiciona        
                                    ]
                                }

                            ]
                        }, //cierre informacion de los elementos adicionales en cliente
                        //informacion de los elementos adicioanles en nodo
                        {
                            xtype: 'fieldset',
                            title: 'Dispositivos en Nodo',
                            hidden: flagclienteNodo,
                            defaultType: 'textfield',
                            defaults: { 
                                width: 565,
                                height: 145
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
                                        {//informacion de elemento de nodo adicional
                                            layout: {type:'table',pack:'center',columns:2},
                                            items : [getDispositivosNodoSafeCityOnt(data)]
                                        }//cierre de informacion de elemento de nodo adicional
                                    ]
                                }

                            ]
                        } //cierre informacion de los elementos adicionales en nodo

                    ],
                    buttons: [{
                        text: 'Activar',
                        formBind: true,
                        handler: function(){
                            var strSerieSmartWifi       = "";
                            var strModeloSmartWifi      = "";
                            var strMacSmartWifi         = "";
                            var strDescripcionSmartWifi = "";
                            var modeloCpe               = Ext.getCmp('modeloCpe').getValue();
                            var serieCpe                = Ext.getCmp('serieCpe').getValue();
                            var descripcionCpe          = Ext.getCmp('descripcionCpe').getValue();
                            var macCpe                  = Ext.getCmp('macCpe').getValue();
                            var modoOperacion           = Ext.getCmp('modoOperacion').getValue();
                            var numPc                   = Ext.getCmp('numeroPc').getValue();
                            var ssid                    = Ext.getCmp('ssid').getValue();
                            var password                = Ext.getCmp('password').getValue();
                            var observacion             = Ext.getCmp('observacionCliente').getValue();

                            var interfaceSplitter =Ext.getCmp('splitterInterfaceElemento').getRawValue();

                            var validacion=false;
                            flag = 0;
                            if(serieCpe=="" || macCpe==""){
                                validacion=false;
                            }
                            else{
                                validacion=true;
                            }

                            if(descripcionCpe=="ELEMENTO ESTADO INCORRECTO" || 
                               descripcionCpe=="ELMENTO CON SALDO CERO" || 
                               descripcionCpe=="NO EXISTE ELEMENTO")
                            {
                                validacion=false;
                                flag=2;
                            }
                            
                            if (datos[0].strTieneSmartWifiRenta == "SI")
                            {
                                strSerieSmartWifi       = Ext.getCmp('serieSmartWifi').getValue();
                                strModeloSmartWifi      = Ext.getCmp('modeloSmartWifi').getValue();
                                strMacSmartWifi         = Ext.getCmp('macSmartWifi').getValue();
                                strDescripcionSmartWifi = Ext.getCmp('descripcionSmartWifi').getValue();
                                
                                if(Ext.isEmpty(strMacSmartWifi))
                                {
                                    validacion = false;
                                    flag = 3;
                                }
                                else if( strDescripcionSmartWifi == "ELEMENTO ESTADO INCORRECTO" || 
                                    strDescripcionSmartWifi == "ELMENTO CON SALDO CERO"    || 
                                    strDescripcionSmartWifi == "NO EXISTE ELEMENTO" )
                                {
                                    validacion  = false;
                                    flag = 4;
                                }
                                else if(Ext.isEmpty(strSerieSmartWifi))
                                {
                                    validacion = false;
                                    flag = 5;
                                }  
                                else if(Ext.isEmpty(strModeloSmartWifi))
                                {
                                    validacion = false;
                                    flag = 6;
                                }
                            }
                            
                            var storeDispositivosNodoSafeCity  = null;
                            var arrayDispositivosNodo  = [];
                            var jsonDipositivosNodo    = "";
                            var idTecnicoEncargado     = Ext.getCmp('comboFilterTecnico').getValue();

                            if (typeof Ext.getCmp("gridDispositivosNodoSafeCity") !== 'undefined') {
                                storeDispositivosNodoSafeCity = Ext.getCmp("gridDispositivosNodoSafeCity").getStore();
                                if (storeDispositivosNodoSafeCity.data.items.length > 0) {
                                    $.each(storeDispositivosNodoSafeCity.data.items, function(i, item) {
                                        arrayDispositivosNodo.push(item.data);
                                    });
                                    jsonDipositivosNodo = Ext.JSON.encode(arrayDispositivosNodo);
                                }
                            }

                            var storeDispositivosClienteSafeCity  = null;
                            var arrayDispositivosCliente  = [];
                            var jsonDipositivosCliente    = "";

                            if (typeof Ext.getCmp("gridDispositivosClienteSafeCity") !== 'undefined') {
                                storeDispositivosClienteSafeCity = Ext.getCmp("gridDispositivosClienteSafeCity").getStore();
                                if (storeDispositivosClienteSafeCity.data.items.length > 0) {
                                    $.each(storeDispositivosClienteSafeCity.data.items, function(i, item) {
                                        arrayDispositivosCliente.push(item.data);
                                    });
                                    jsonDipositivosCliente = Ext.JSON.encode(arrayDispositivosCliente);
                                }
                            }
                            if(validacion){
                                Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');

                                Ext.Ajax.request({
                                    url: activarClienteBoton,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio                  : data.idServicio,
                                        idProducto                  : data.productoId,
                                        tipoRed                     : data.strTipoRed,
                                        perfil                      : data.perfilDslam,
                                        login                       : data.login,
                                        capacidad1                  : data.capacidadUno,
                                        capacidad2                  : data.capacidadDos,
                                        interfaceElementoId         : data.interfaceElementoId,
                                        interfaceElementoSplitterId : interfaceSplitter,
                                        ultimaMilla                 : data.ultimaMilla,
                                        plan                        : data.planId,
                                        serieOnt                    : serieCpe,
                                        modeloOnt                   : modeloCpe,
                                        macOnt                      : macCpe,
                                        numPc                       : numPc,
                                        ssid                        : ssid,
                                        password                    : password,
                                        modoOperacion               : modoOperacion,
                                        observacionCliente          : observacion,
                                        strSerieSmartWifi           : strSerieSmartWifi,
                                        strModeloSmartWifi          : strModeloSmartWifi,
                                        strMacSmartWifi             : strMacSmartWifi,
                                        strTieneSmartWifiRenta      : datos[0].strTieneSmartWifiRenta,
                                        strEsInternetLite           : datos[0].strEsInternetLite,
                                        idIntCouSim                 : data.idIntCouSim,
                                        'jsonDipositivosNodo'       : jsonDipositivosNodo,
                                        'jsonDipositivosCliente'    : jsonDipositivosCliente,
                                        'idTecnicoEncargado'        : idTecnicoEncargado 
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
                                            Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                                        }
                                        else if(response.responseText == "NO EXISTE PRODUCTO"){
                                            Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                        }
                                        else if(response.responseText == "NO EXISTE CPE"){
                                            Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                                        }
                                        else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                                            Ext.Msg.alert('Mensaje ','Equipo no esta en PENDIENTE INSTALACION/RETIRADO, favor revisar!' );
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
                                    Ext.Msg.alert("Validación","Alguna Mac esta incorrecta, favor revisar!", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==2){
                                    Ext.Msg.alert("Validación","Datos del Ont incorrectos, favor revisar!", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if( flag == 3 )
                                {
                                    Ext.Msg.alert("Validación","No existe valor de Mac, favor revisar!");
                                }
                                else if( flag == 4 )
                                {
                                    Ext.Msg.alert("Validación","Datos del Wifi incorrectos, favor revisar!");
                                }
                                else if( flag == 5 )
                                {
                                    Ext.Msg.alert("Validación","Por favor ingrese la serie correspondiente!");
                                }
                                else if( flag == 6 )
                                {
                                    Ext.Msg.alert("Validación","Por favor ingrese el modelo correspondiente!");
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
                    title: tituloPanel,
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
                
                if (datos[0].strTieneSmartWifiRenta == "NO")
                {
                    Ext.getCmp('informacionSmartWifi').setVisible(false);
                }

                if(data.strSerieOntCliente != "")
                {
                    Ext.getCmp('serieCpe').setValue = data.strSerieOntCliente;
                    Ext.getCmp('serieCpe').setRawValue(data.strSerieOntCliente);
                    Ext.getCmp('serieCpe').focus();
                    Ext.getCmp('serieCpe').blur();
                }

            }//cierre response
        });
    }//cierre tipoOrden==="N"
}

/* Funcion que sirve para cargar pantalla y llamada ajax
 * para activacion de puerto en un olt Zte para empresa MD 
 * 
 * @author      Jesus Bozada <jbozada@telconet.ec>
 * @version     1.0     03/07/2018
 * @since       1.0
 * 
 * @author      Lizbeth Cruz <mlcruz@telconet.ec>
 * @version     1.1     08/02/2022 Se corrige nombres intercambiados de etiquetas y nombre de plan al agregar programación de activación de 
 *                                 servicios Zte para Small Business
 * 
 * @param Array data        Informacion que fue cargada en el grid
 */
function activarServicioZteMD(data){
    var tipoOrden = data.tipoOrden;
    
    if(tipoOrden==="N")
    {
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
                var strNombrePlanProd       = data.nombrePlan;
                var strEtiquetaPlanProd     = 'Plan';
                //-------------------------------------------------------------------------------------------
               if(datos[0].strEsInternetLite === 'SI')
                {
                     strNombrePlanProd           = data.nombreProducto;
                     strEtiquetaPlanProd         = 'Producto';
                }
                
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
                            title: 'Informacion del Servicio',
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
                                            name: 'plan',
                                            fieldLabel: strEtiquetaPlanProd,
                                            displayField: strNombrePlanProd,
                                            value: strNombrePlanProd,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
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
                                            name: 'capacidadUno',
                                            fieldLabel: 'Capacidad Uno',
                                            displayField: data.capacidadUno,
                                            value: data.capacidadUno,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidadDos',
                                            fieldLabel: 'Capacidad Dos',
                                            displayField: data.capacidadDos,
                                            value: data.capacidadDos,
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
                                            fieldLabel: 'Ultima Milla',
                                            displayField: data.ultimaMilla,
                                            value: data.ultimaMilla,
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            width: '35%',
                                            border: false
                                        },
                                        { width: '10%', border: false}
                                    ]
                                }

                            ]
                        },//cierre de la informacion servicio/producto

                        //informacion de backbone
                        {
                            xtype: 'fieldset',
                            title: 'Informacion de backbone',
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
                                            fieldLabel: 'Splitter Elemento',
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
                                            fieldLabel: 'Splitter Interface',
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
                                            fieldLabel: 'Caja Elemento',
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
                            title: 'Informacion de los Elementos del Cliente',
                            defaultType: 'textfield',
                            defaults: { 
                                width: 540
                            },
                            items: [

                                {
                                    xtype: 'fieldset',
                                    title: 'Informacion del ONT',
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
                                                    queryMode: 'local',
                                                    xtype: 'textfield',
                                                    id:'serieCpe',
                                                    name: 'serieCpe',
                                                    fieldLabel: 'Serie ONT',
                                                    displayField: "",
                                                    value: "",
                                                    loadingText: 'Buscando...',
                                                    width: '25%',
                                                    listeners: {
                                                        blur: function(serie){
                                                            Ext.Ajax.request({
                                                                url: buscarCpeHuaweiNaf,
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
                                                                    var macOntNaf = mensaje[1];
                                                                    var modeloCpe   = mensaje[2];

                                                                    Ext.getCmp('descripcionCpe').setValue = '';
                                                                    Ext.getCmp('descripcionCpe').setRawValue('');

                                                                    Ext.getCmp('macCpe').setValue = '';
                                                                    Ext.getCmp('macCpe').setRawValue('');

                                                                    Ext.getCmp('modeloCpe').setValue = '';
                                                                    Ext.getCmp('modeloCpe').setRawValue('');

                                                                    if(status=="OK")
                                                                    {
                                                                        Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                                        Ext.getCmp('descripcionCpe').setRawValue(descripcion);

                                                                        Ext.getCmp('macCpe').setValue = macOntNaf;
                                                                        Ext.getCmp('macCpe').setRawValue(macOntNaf);

                                                                        Ext.getCmp('modeloCpe').setValue = modeloCpe;
                                                                        Ext.getCmp('modeloCpe').setRawValue(modeloCpe);
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
                                                { width: '20%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'modeloCpe',
                                                    name: 'modeloCpe',
                                                    fieldLabel: 'Modelo ONT',
                                                    displayField: "",
                                                    value: "",
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'macCpe',
                                                    name: 'macCpe',
                                                    fieldLabel: 'Mac ONT',
                                                    displayField: "",
                                                    value: "",
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                {
                                                    xtype: 'hidden',
                                                    id:'validacionMacOnt',
                                                    name: 'validacionMacOnt',
                                                    value: "",
                                                    width: '20%'
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    id:'descripcionCpe',
                                                    name: 'descripcionCpe',
                                                    fieldLabel: 'Descripcion ONT',
                                                   displayField: "",
                                                    value: "",
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                            ]//cierre del container table
                                        }                


                                    ]//cierre del fieldset
                                },
                                
                                {
                                    id: 'informacionSmartWifi',
                                    xtype: 'fieldset',
                                    title: 'Información del SmartWifi',
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
                                                    id:'serieSmartWifi',
                                                    name: 'serieSmartWifi',
                                                    fieldLabel: 'Serie SmartWifi',
                                                    displayField: "",
                                                    value: "",
                                                    width: '25%'
                                                },
                                                { width: '20%', border: false},
                                                {
                                                    queryMode: 'local',
                                                    xtype: 'combobox',
                                                    id: 'modeloSmartWifi',
                                                    name: 'modeloSmartWifi',
                                                    fieldLabel: 'Modelo SmartWifi',
                                                    displayField:'modelo',
                                                    valueField: 'modelo',
                                                    loadingText: 'Buscando...',
                                                    store: storeModelosCpe,
                                                    width: '25%',
                                                    listeners: {
                                                        blur: function(combo) {
                                                            Ext.Ajax.request({
                                                                url: buscarCpeNaf,
                                                                method: 'post',
                                                                params: {
                                                                    serieCpe: Ext.getCmp('serieSmartWifi').getValue(),
                                                                    modeloElemento: combo.getValue(),
                                                                    estado: 'PI',
                                                                    bandera: 'ActivarServicio'
                                                                },
                                                                success: function(response) 
                                                                {
                                                                    var respuesta = response.responseText.split("|");
                                                                    var status = respuesta[0];
                                                                    var mensaje = respuesta[1];

                                                                    if (status == "OK")
                                                                    {
                                                                        Ext.getCmp('descripcionSmartWifi').setValue = mensaje;
                                                                        Ext.getCmp('descripcionSmartWifi').setRawValue(mensaje);
                                                                        var arrayInformacionWifi       = mensaje.split(",");
                                                                        Ext.getCmp('macSmartWifi').setValue = arrayInformacionWifi[1];
                                                                        Ext.getCmp('macSmartWifi').setRawValue(arrayInformacionWifi[1]);
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert('Mensaje ', mensaje);
                                                                        Ext.getCmp('descripcionSmartWifi').setValue = status;
                                                                        Ext.getCmp('descripcionSmartWifi').setRawValue(status);
                                                                        Ext.getCmp('macSmartWifi').setValue = "";
                                                                        Ext.getCmp('macSmartWifi').setRawValue("");
                                                                    }
                                                                },
                                                                failure: function(result)
                                                                {
                                                                    Ext.getCmp('macSmartWifi').setValue = "";
                                                                    Ext.getCmp('macSmartWifi').setRawValue("");
                                                                    Ext.get(formPanel.getId()).unmask();
                                                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'macSmartWifi',
                                                    name: 'macSmartWifi',
                                                    fieldLabel: 'Mac SmartWifi',
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'descripcionSmartWifi',
                                                    name: 'descripcionSmartWifi',
                                                    fieldLabel: 'Descripción SmartWifi',
                                                    displayField: "",
                                                    value: "",
                                                    readOnly: true,
                                                    width: '25%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------

                                            ]//cierre del container table
                                        }                


                                    ]//cierre del fieldset
                                }
                            ]

                        },//cierre informacion de los elementos del cliente

                        //informacion del Cliente
                        {
                            xtype: 'fieldset',
                            title: 'Informacion del Cliente',
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


                            ]//cierre del fieldset
                        }//cierre informacion ont

                    ],
                    buttons: [{
                        text: 'Activar',
                        formBind: true,
                        handler: function(){
                            var strSerieSmartWifi       = "";
                            var strModeloSmartWifi      = "";
                            var strMacSmartWifi         = "";
                            var strDescripcionSmartWifi = "";
                            var modeloCpe               = Ext.getCmp('modeloCpe').getValue();
                            var serieCpe                = Ext.getCmp('serieCpe').getValue();
                            var descripcionCpe          = Ext.getCmp('descripcionCpe').getValue();
                            var macCpe                  = Ext.getCmp('macCpe').getValue();
                            var modoOperacion           = Ext.getCmp('modoOperacion').getValue();
                            var numPc                   = Ext.getCmp('numeroPc').getValue();
                            var ssid                    = Ext.getCmp('ssid').getValue();
                            var password                = Ext.getCmp('password').getValue();
                            var observacion             = Ext.getCmp('observacionCliente').getValue();

                            var interfaceSplitter =Ext.getCmp('splitterInterfaceElemento').getRawValue();

                            var validacion=false;
                            flag = 0;
                            if(serieCpe === "" || macCpe === ""){
                                validacion=false;
                            }
                            else{
                                validacion=true;
                            }

                            if(descripcionCpe === "ELEMENTO ESTADO INCORRECTO" || 
                               descripcionCpe === "ELMENTO CON SALDO CERO" || 
                               descripcionCpe === "NO EXISTE ELEMENTO")
                            {
                                validacion=false;
                                flag=2;
                            }
                            
                            if (datos[0].strTieneSmartWifiRenta === "SI")
                            {
                                strSerieSmartWifi       = Ext.getCmp('serieSmartWifi').getValue();
                                strModeloSmartWifi      = Ext.getCmp('modeloSmartWifi').getValue();
                                strMacSmartWifi         = Ext.getCmp('macSmartWifi').getValue();
                                strDescripcionSmartWifi = Ext.getCmp('descripcionSmartWifi').getValue();
                                
                                if(Ext.isEmpty(strMacSmartWifi))
                                {
                                    validacion = false;
                                    flag = 3;
                                }
                                else if( strDescripcionSmartWifi === "ELEMENTO ESTADO INCORRECTO" || 
                                    strDescripcionSmartWifi === "ELMENTO CON SALDO CERO"    || 
                                    strDescripcionSmartWifi === "NO EXISTE ELEMENTO" )
                                {
                                    validacion  = false;
                                    flag = 4;
                                }
                                else if(Ext.isEmpty(strSerieSmartWifi))
                                {
                                    validacion = false;
                                    flag = 5;
                                }  
                                else if(Ext.isEmpty(strModeloSmartWifi))
                                {
                                    validacion = false;
                                    flag = 6;
                                }
                            }

                            if(validacion){
                                Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');


                                Ext.Ajax.request({
                                    url: activarClienteBoton,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio                  : data.idServicio,
                                        idProducto                  : data.productoId,
                                        perfil                      : data.perfilDslam,
                                        login                       : data.login,
                                        capacidad1                  : data.capacidadUno,
                                        capacidad2                  : data.capacidadDos,
                                        interfaceElementoId         : data.interfaceElementoId,
                                        interfaceElementoSplitterId : interfaceSplitter,
                                        ultimaMilla                 : data.ultimaMilla,
                                        plan                        : data.planId,
                                        serieOnt                    : serieCpe,
                                        modeloOnt                   : modeloCpe,
                                        macOnt                      : macCpe,
                                        numPc                       : numPc,
                                        ssid                        : ssid,
                                        password                    : password,
                                        modoOperacion               : modoOperacion,
                                        observacionCliente          : observacion,
                                        strSerieSmartWifi           : strSerieSmartWifi,
                                        strModeloSmartWifi          : strModeloSmartWifi,
                                        strMacSmartWifi             : strMacSmartWifi,
                                        strTieneSmartWifiRenta      : datos[0].strTieneSmartWifiRenta,
                                        strEsInternetLite           : datos[0].strEsInternetLite
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText === "OK"){
                                            Ext.Msg.alert('Mensaje','Se Activo el Cliente', function(btn){
                                                if(btn === 'ok'){
                                                    win.destroy();
                                                    store.load();
                                                }
                                            });
                                        }
                                        else if(response.responseText === "SERIAL YA EXISTE"){
                                            Ext.Msg.alert('Mensaje ','Serial de ONT, ya existe en el OLT!' );
                                        }
                                        else if(response.responseText === "NO ID CLIENTE"){
                                            Ext.Msg.alert('Mensaje ','Serial de Ont erroneo!' );
                                        }
                                        else if(response.responseText === "CANTIDAD CERO"){
                                            Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
                                        }
                                        else if(response.responseText === "NO EXISTE PRODUCTO"){
                                            Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                        }
                                        else if(response.responseText === "NO EXISTE CPE"){
                                            Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
                                        }
                                        else if(response.responseText === "CPE NO ESTA EN ESTADO"){
                                            Ext.Msg.alert('Mensaje ','Equipo no esta en PENDIENTE INSTALACION/RETIRADO, favor revisar!' );
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
                                if(flag === 1){
                                    Ext.Msg.alert("Validación","Alguna Mac esta incorrecta, favor revisar!", function(btn){
                                            if(btn === 'ok'){
                                            }
                                    });
                                }
                                else if(flag === 2){
                                    Ext.Msg.alert("Validación","Datos del Ont incorrectos, favor revisar!", function(btn){
                                            if(btn === 'ok'){
                                            }
                                    });
                                }
                                else if( flag === 3 )
                                {
                                    Ext.Msg.alert("Validación","No existe valor de Mac, favor revisar!");
                                }
                                else if( flag === 4 )
                                {
                                    Ext.Msg.alert("Validación","Datos del Wifi incorrectos, favor revisar!");
                                }
                                else if( flag === 5 )
                                {
                                    Ext.Msg.alert("Validación","Por favor ingrese la serie correspondiente!");
                                }
                                else if( flag === 6 )
                                {
                                    Ext.Msg.alert("Validación","Por favor ingrese el modelo correspondiente!");
                                }
                                else{
                                    Ext.Msg.alert("Validación","Favor Revise los campos", function(btn){
                                            if(btn === 'ok'){
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
                    title: 'Activar Servicio',
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
                
                if (datos[0].strTieneSmartWifiRenta === "NO")
                {
                    Ext.getCmp('informacionSmartWifi').setVisible(false);
                }

                if(data.strSerieOntCliente != "")
                {
                    Ext.getCmp('serieCpe').setValue = data.strSerieOntCliente;
                    Ext.getCmp('serieCpe').setRawValue(data.strSerieOntCliente);
                    Ext.getCmp('serieCpe').focus();
                    Ext.getCmp('serieCpe').blur();
                }

                if(data.strSerieWifiCliente != "")
                {
                    Ext.getCmp('serieSmartWifi').setValue = data.strSerieWifiCliente;
                    Ext.getCmp('serieSmartWifi').setRawValue(data.strSerieWifiCliente);
                    Ext.getCmp('serieSmartWifi').focus();
                    Ext.getCmp('serieSmartWifi').blur();
                }

            }//cierre response
        });
    }//cierre tipoOrden==="N"
    else if (tipoOrden==="T")
    {
        activarServicioTraslado(data);
    }
}

/**
 * Funcion que sirve para realizar los traslados
 * */
function activarServicioTraslado(data, gridIndex){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
        Ext.Ajax.request({ 
            url: getDatosBackbone,
            method: 'post',
            timeout: 10000000,
            params: { 
                idServicio: data.idServicio
            },
            success: function(response){
                Ext.get(gridServicios.getId()).unmask();

                var json = Ext.JSON.decode(response.responseText);
                var datos = json.encontrados;
                console.log(datos[0].marcaElemento);
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
                
                if(datos[0].diferenteTecnologia == "SI" 
                    || (datos[0].strEsInternetLite === 'SI' && data.descripcionProducto === "INTERNET SMALL BUSINESS"))
                {
                    //pedir datos cliente (ont - wifi)
                    if(datos[0].marcaElemento === "HUAWEI" || datos[0].marcaElemento === "ZTE")
                    {
                        panelTrasladoHuawei(datos, data, storeInterfacesSplitter, storeModelosCpe);
                    }
                    else if(datos[0].marcaElemento === "TELLION")
                    {
                        var storeModelosCpeWifi = new Ext.data.Store({  
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
                        panelTrasladoTellion(datos, data, storeInterfacesSplitter, storeModelosCpe, storeModelosCpeWifi);
                    } 
                    
                    storeInterfacesSplitter.load({
                        callback:function(){        
                            storeModelosCpe.load({

                            });
                            if(datos[0].marcaElemento === "TELLION")
                            {
                                storeModelosCpeWifi.load({});
                            }
                        }
                    });
                }
                else
                {
                    panelMismosRecursosTraslado(datos, data, storeInterfacesSplitter);
                    
                    storeInterfacesSplitter.load({
                        callback:function(){        

                        }
                    });
                }
            }//cierre response
        });
}

/**
 * Funcion que sirve para realizar los traslados
 * */
function trasladarExtenderDualBand(data, gridIndex){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
        Ext.Ajax.request({ 
            url: getDatosTrasladoExtender,
            method: 'post',
            timeout: 10000000,
            params: { 
                intIdServicio: data.idServicio,
                strTipoServicio: 'Plan'
            },
            success: function(response){
                Ext.get(gridServicios.getId()).unmask();

                var json = Ext.JSON.decode(response.responseText);
                var datos = json.encontrados;
                if (datos[0].strStatus === "OK")
                {
                    //-------------------------------------------------------------------------------------------
                    Ext.define('tipoCaracteristica', {
                        extend: 'Ext.data.Model',
                        fields: [
                            {name: 'tipo', type: 'string'}
                        ]
                    });
                    //-------------------------------------------------------------------------------------------
                    panelTrasladoExtenderDualBand(datos, data);
                }
                else
                {
                    Ext.Msg.alert('Mensaje','Se presentaron problemas al recuperar la información del equipo, por favor consulte a sistemas.');
                }
                //panelTrasladoExtenderDualBand
            }//cierre response
        });
}

/**
 * Funcion que sirve para realizar la sincronización de equipo Extender Dual Band
 * */
function sincronizarExtenderDualBand(data, gridIndex){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
        Ext.Ajax.request({ 
            url: getDatosSincronizarExtender,
            method: 'post',
            timeout: 10000000,
            params: { 
                intIdServicio: data.idServicio,
                strTipoServicio: 'Plan'
            },
            success: function(response){
                Ext.get(gridServicios.getId()).unmask();

                var json = Ext.JSON.decode(response.responseText);
                var datos = json.encontrados;
                if (datos[0].strStatus === "OK")
                {
                    //-------------------------------------------------------------------------------------------
                    Ext.define('tipoCaracteristica', {
                        extend: 'Ext.data.Model',
                        fields: [
                            {name: 'tipo', type: 'string'}
                        ]
                    });
                    //-------------------------------------------------------------------------------------------
                    panelSincronizarExtenderDualBand(datos, data);
                }
                else
                {
                    Ext.Msg.alert('Mensaje','Se presentaron problemas al recuperar la información del equipo, por favor consulte a sistemas.');
                }
                //panelTrasladoExtenderDualBand
            }//cierre response
        });
}

function panelTrasladoTellion(datos, data, storeInterfacesSplitter, storeModelosCpe, storeModelosCpeWifi)
{
    var strNombrePlanProd           = data.nombrePlan;
    var strEtiquetaPlanProd         = 'Plan';
    var strEtiquetaPerfilVelocidad  = 'Perfil';
    var strNombreTxtPerfilVelocidad = 'perfil';
    var strValorPerfilVelocidad     = data.perfilDslam;
    if(datos[0].strEsInternetLite === 'SI')
    {
        strNombrePlanProd           = data.nombreProducto;
        strEtiquetaPlanProd         = 'Producto';
        strEtiquetaPerfilVelocidad  = 'Velocidad';
        strNombreTxtPerfilVelocidad = 'velocidad';
        strValorPerfilVelocidad     = data.velocidadISB + " MB";
    }

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
                //informacion del servicio/producto
                {
                    xtype: 'fieldset',
                    title: 'Informacion del Servicio',
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
                                    name: 'plan',
                                    fieldLabel: strEtiquetaPlanProd,
                                    displayField: strNombrePlanProd,
                                    value: strNombrePlanProd,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false},
                                {
                                    xtype: 'textfield',
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
                                    name: 'capacidadUno',
                                    fieldLabel: 'Capacidad Uno',
                                    displayField: data.capacidadUno,
                                    value: data.capacidadUno,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false},
                                {
                                    xtype: 'textfield',
                                    name: 'capacidadDos',
                                    fieldLabel: 'Capacidad Dos',
                                    displayField: data.capacidadDos,
                                    value: data.capacidadDos,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '10%', border: false},

                                //---------------------------------------------

                                { width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    name: 'capacidadTres',
                                    fieldLabel: 'Capacidad Int/Prom Uno',
                                    displayField: data.capacidadTres,
                                    value: data.capacidadTres,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false},
                                {
                                    xtype: 'textfield',
                                    name: 'capacidadCuatro',
                                    fieldLabel: 'Capacidad Int/Prom Dos',
                                    displayField: data.capacidadCuatro,
                                    value: data.capacidadCuatro,
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
                                    fieldLabel: 'Ultima Milla',
                                    displayField: data.ultimaMilla,
                                    value: data.ultimaMilla,
                                    readOnly: true,
                                    width: '35%'
                                },
                                { width: '15%', border: false},
                                {
                                    xtype: 'textfield',
                                    id: strNombreTxtPerfilVelocidad,
                                    name: strNombreTxtPerfilVelocidad,
                                    fieldLabel: strEtiquetaPerfilVelocidad,
                                    displayField: strValorPerfilVelocidad,
                                    value: strValorPerfilVelocidad,
                                    readOnly: true,
                                    width: '35%'
                                },
                                { width: '10%', border: false}
                            ]
                        }

                    ]
                },//cierre de la informacion servicio/producto

                //informacion de backbone
                {
                    xtype: 'fieldset',
                    title: 'Informacion de backbone Anterior',
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
                                    displayField: datos[0].nombreElementoAnterior,
                                   value: datos[0].nombreElementoAnterior,

                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false},
                                {
                                    xtype: 'textfield',
                                    name: 'ipElemento',
                                    fieldLabel: 'Ip Elemento',
                                    displayField: datos[0].ipElementoAnterior,
                                    value: datos[0].ipElementoAnterior,
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
                                    displayField: datos[0].puertoElementoAnterior,
                                    value: datos[0].puertoElementoAnterior,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false},
                                {
                                    xtype: 'textfield',
                                    name: 'modeloELemento',
                                    fieldLabel: 'Modelo Elemento',
                                    displayField: datos[0].modeloElementoAnterior,
                                    value: datos[0].modeloElementoAnterior,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '10%', border: false},

                                //---------------------------------------------

                                { width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    name: 'splitterElemento',
                                    fieldLabel: 'Splitter Elemento',
                                    displayField: datos[0].nombreSplitterAnterior,
                                    value: datos[0].nombreSplitterAnterior,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false},
                                {
                                    xtype: 'textfield',
                                    name: 'splitterInterfaceElemento',
                                    fieldLabel: 'Splitter Interface',
                                    displayField: datos[0].puertoSplitterAnterior,
                                    value: datos[0].puertoSplitterAnterior,
                                    readOnly: true,
                                    width: '25%'
                                },
                                { width: '10%', border: false},

                                //---------------------------------------------

                                { width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    name: 'cajaElemento',
                                    fieldLabel: 'Caja Elemento',
                                    displayField: datos[0].nombreCajaAnterior,
                                    value: datos[0].nombreCajaAnterior,
                                    readOnly: true,
                                    width: '30%'
                                },
                                { width: '15%', border: false},
                                { width: '10%', border: false},
                                { width: '10%', border: false}

                            ]
                        }

                    ]
                },//cierre de info de backbone anterior

                //informacion de backbone
                {
                    xtype: 'fieldset',
                    title: 'Informacion de backbone',
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
                                    fieldLabel: 'Splitter Elemento',
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
                                    fieldLabel: 'Splitter Interface',
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
                                    fieldLabel: 'Caja Elemento',
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
                    title: 'Informacion de los Elementos del Cliente',
                    defaultType: 'textfield',
                    defaults: { 
                        width: 540
                    },
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Informacion del Wifi',
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
                                            id:'serieWifi',
                                            name: 'serieWifi',
                                            fieldLabel: 'Serie Wifi',
                                            displayField: "",
                                            value: "",
                                            width: '25%'
                                        },
                                        { width: '20%', border: false},
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'modeloWifi',
                                            name: 'modeloWifi',
                                            fieldLabel: 'Modelo Wifi',
                                            displayField:'modelo',
                                            valueField: 'modelo',
                                            loadingText: 'Buscando...',
                                            store: storeModelosCpeWifi,
                                            width: '25%',
                                            listeners: {
                                                blur: function(combo){
                                                    Ext.Ajax.request({
                                                        url: buscarCpeNaf,
                                                        method: 'post',
                                                        params: { 
                                                            serieCpe: Ext.getCmp('serieWifi').getValue(),
                                                            modeloElemento: combo.getValue(),
                                                            estado: 'PI',
                                                            bandera: 'ActivarServicio'
                                                        },
                                                        success: function(response){
                                                            var respuesta = response.responseText.split("|");
                                                            var status = respuesta[0];
                                                            var mensaje = respuesta[1];

                                                            if(status=="OK")
                                                            {
                                                                Ext.getCmp('descripcionWifi').setValue = mensaje;
                                                                Ext.getCmp('descripcionWifi').setRawValue(mensaje);
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('descripcionWifi').setValue = status;
                                                                Ext.getCmp('descripcionWifi').setRawValue(status);
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

                                        //---------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'macWifi',
                                            name: 'macWifi',
                                            fieldLabel: 'Mac Wifi',
                                            displayField: data.mac,
                                            value: data.mac,
                                            width: '25%',
                                            listeners: {
                                                blur: function(text){
                                                    var mac = text.getValue();
                                                    if(datos[0].strEsInternetLite === 'SI')
                                                    {
                                                        if(mac.match("^[a-fA-F0-9]{4}[\.][a-fA-F0-9]{4}[\.]+[a-fA-F0-9]{4}$"))
                                                        {
                                                            Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                            Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                        }
                                                        else
                                                        {
                                                            Ext.Msg.alert('Validación',
                                                                          'Mac Wifi Incorrecta (aaaa.bbbb.cccc), favor revisar!');
                                                            Ext.getCmp('validacionMacWifi').setValue = "incorrecta";
                                                            Ext.getCmp('validacionMacWifi').setRawValue("incorrecta") ;
                                                        }
                                                    }
                                                    else if(mac.match("c8b3.73+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("0014.d1+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("000e.dc+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("d8eb.97+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("ccb2.55+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("84c9.b2+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("fc75.16+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("20aa.4b+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("c8d7.19+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("0026.5a+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("48f8.b3+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("b475.0e+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$"))
                                                    {
                                                        Ext.getCmp('validacionMacWifi').setValue = "correcta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("correcta") ;
                                                    }
                                                    else{
                                                        Ext.Msg.alert('Validacion','Mac Wifi Incorrecta (aaaa.bbbb.cccc), favor revisar!');
                                                        Ext.getCmp('validacionMacWifi').setValue = "incorrecta";
                                                        Ext.getCmp('validacionMacWifi').setRawValue("incorrecta") ;
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'hidden',
                                            id:'validacionMacWifi',
                                            name: 'validacionMacWifi',
                                            value: "",
                                            width: '20%'
                                        },
                                        {
                                            xtype: 'textfield',
                                            id:'descripcionWifi',
                                            name: 'descripcionWifi',
                                            fieldLabel: 'Descripcion Wifi',
                                            displayField: "",
                                            value: "",
                                            readOnly: true,
                                            width: '25%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------
                                    ]//cierre del container table
                                }
                            ]//cierre del fieldset
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Informacion del ONT',
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
                                            id:'serieCpe',
                                            name: 'serieCpe',
                                            fieldLabel: 'Serie ONT',
                                            displayField: "",
                                            value: "",
                                            width: '25%'
                                        },
                                        { width: '20%', border: false},
                                        {
                                            queryMode: 'local',
                                            xtype: 'combobox',
                                            id: 'modeloCpe',
                                            name: 'modeloCpe',
                                            fieldLabel: 'Modelo ONT',
                                            displayField:'modelo',
                                            valueField: 'modelo',
                                            loadingText: 'Buscando...',
                                            store: storeModelosCpe,
                                            width: '25%',
                                            listeners: {
                                                blur: function(combo){
                                                    Ext.Ajax.request({
                                                        url: buscarCpeNaf,
                                                        method: 'post',
                //                                        timeout: 400000,
                                                        params: { 
                                                            serieCpe: Ext.getCmp('serieCpe').getValue(),
                                                            modeloElemento: combo.getValue(),
                                                            estado: 'PI',
                                                            bandera: 'ActivarServicio'
                                                        },
                                                        success: function(response){
                                                            var respuesta = response.responseText.split("|");
                                                            var status = respuesta[0];
                                                            var mensaje = respuesta[1];

                                                            if(status=="OK")
                                                            {
                                                                Ext.getCmp('descripcionCpe').setValue = mensaje;
                                                                Ext.getCmp('descripcionCpe').setRawValue(mensaje);
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('descripcionCpe').setValue = status;
                                                                Ext.getCmp('descripcionCpe').setRawValue(status);
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

                                        //---------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            id:'macCpe',
                                            name: 'macCpe',
                                            fieldLabel: 'Mac ONT',
                                            displayField: "",
                                            value: "",
                                            width: '25%',
                                            listeners: {
                                                blur: function(text){
                                                    var mac = text.getValue();
                                                    if(mac.match("c8b3.73+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("0014.d1+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("000e.dc+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("d8eb.97+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("ccb2.55+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("84c9.b2+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("fc75.16+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("20aa.4b+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("c8d7.19+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("0026.5a+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("48f8.b3+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$")){
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else if(mac.match("b475.0e+[a-fA-f0-9]{2}[\.]+[a-fA-F0-9]{4}$"))
                                                    {
                                                        Ext.getCmp('validacionMacOnt').setValue = "correcta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("correcta") ;
                                                    }
                                                    else{
                                                        Ext.Msg.alert('Validacion','Mac Ont Incorrecta (aaaa.bbbb.cccc), favor revisar!');
                                                        Ext.getCmp('validacionMacOnt').setValue = "incorrecta";
                                                        Ext.getCmp('validacionMacOnt').setRawValue("incorrecta") ;
                                                    }
                                                }
                                            }
                                        },
                                        {
                                            xtype: 'hidden',
                                            id:'validacionMacOnt',
                                            name: 'validacionMacOnt',
                                            value: "",
                                            width: '20%'
                                        },
                                        {
                                            xtype: 'textfield',
                                            id:'descripcionCpe',
                                            name: 'descripcionCpe',
                                            fieldLabel: 'Descripcion ONT',
                                            displayField: "",
                                            value: "",
                                            readOnly: true,
                                            width: '25%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------

                                    ]//cierre del container table
                                }                


                            ]//cierre del fieldset
                        }
                    ]

                }//cierre informacion de los elementos del cliente
            ],
            buttons: [{
                text: 'Activar',
                formBind: true,
                handler: function(){
                    var modeloCpe = Ext.getCmp('modeloCpe').getValue();
                    var serieCpe = Ext.getCmp('serieCpe').getValue();
                    var descripcionCpe = Ext.getCmp('descripcionCpe').getValue();
                    var macCpe = Ext.getCmp('macCpe').getValue();

                    var modeloWifi = Ext.getCmp('modeloWifi').getValue();
                    var serieWifi = Ext.getCmp('serieWifi').getValue();
                    var descripcionWifi = Ext.getCmp('descripcionWifi').getValue();
                    var macWifi = Ext.getCmp('macWifi').getValue();

                    var interfaceSplitter =Ext.getCmp('splitterInterfaceElemento').getRawValue();
                    var validacionWifi =Ext.getCmp('validacionMacWifi').getRawValue();
                    var validacionOnt =Ext.getCmp('validacionMacOnt').getRawValue();

                    var validacion=false;
                    flag = 0;
                    if(serieCpe=="" || macCpe=="" || serieWifi=="" || macWifi==""){
                        validacion=false;
                    }
                    else{
                        validacion=true;
                    }

                    if(descripcionCpe=="ELEMENTO ESTADO INCORRECTO" || 
                       descripcionCpe=="ELMENTO CON SALDO CERO" || 
                       descripcionCpe=="NO EXISTE ELEMENTO")
                    {
                        validacion=false;
                        flag=3;
                    }
                    if(descripcionWifi=="ELEMENTO ESTADO INCORRECTO" || 
                       descripcionWifi=="ELMENTO CON SALDO CERO" || 
                       descripcionWifi=="NO EXISTE ELEMENTO")
                    {
                        validacion=false;
                        flag=4;
                    }

                    if(validacionWifi=="incorrecta" || validacionOnt=="incorrecta"){
                        validacion=false;
                        flag=1;
                    }

                    if(macCpe == macWifi){
                        validacion=false;
                        flag=2;
                    }

                    if(validacion){
                        Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');
                        arrayInfo = new Array();
                        arrayInfo["idServicio"]   = data.idServicio;
                        arrayInfo["productoId"]   = data.productoId;
                        arrayInfo["perfilDslam"]  = data.perfilDslam;
                        arrayInfo["login"]        = data.login;
                        arrayInfo["capacidadUno"] = data.capacidadUno;
                        arrayInfo["capacidadDos"] = data.capacidadDos;
                        arrayInfo["interfaceElementoId"]    = data.interfaceElementoId;
                        arrayInfo["interfaceSplitter"]      = interfaceSplitter;
                        arrayInfo["ultimaMilla"]  = data.ultimaMilla;
                        arrayInfo["planId"]       = data.planId;
                        arrayInfo["serieCpe"]     = serieCpe;
                        arrayInfo["modeloCpe"]    = modeloCpe;
                        arrayInfo["macCpe"]       = macCpe;
                        arrayInfo["serieWifi"]    = serieWifi;
                        arrayInfo["modeloWifi"]   = modeloWifi;
                        arrayInfo["macWifi"]      = macWifi;
                        arrayInfo["win"]          = win;
                        arrayInfo["store"]        = store;
                        arrayInfo["strTieneSmartWifiRenta"] = datos[0].strTieneSmartWifiRenta;
                        arrayInfo["strEsInternetLite"]      = datos[0].strEsInternetLite;
                        arrayInfo["formPanel"]    = formPanel;
                        if (prefijoEmpresa === 'MD'|| prefijoEmpresa === 'EN')
                        {
                            connValidaEquipos.request({
                                url: urlValidaEleOrigenTraslado,
                                method: 'post',
                                timeout: 400000,
                                params: {idServicio: data.idServicio},
                                success: function(responseValida) {
                                    var datosValidaTrasladoMd = Ext.JSON.decode(responseValida.responseText);
                                    if (datosValidaTrasladoMd.strEstado == "OK")
                                    {
                                        if (datosValidaTrasladoMd.strExistenEquiposFacturar == "SI")
                                        {
                                            Ext.MessageBox.show({
                                                title: "Información",
                                                cls: 'msg_floating',
                                                msg: datosValidaTrasladoMd.strMensaje,
                                                icon: Ext.MessageBox.INFO,
                                                buttons: Ext.Msg.OK,
                                                fn: function(buttonId)
                                                {
                                                    if (buttonId === "ok")
                                                    {
                                                        ejecutaTrasladoTellion(arrayInfo);
                                                    }
                                                }
                                            });
                                        }
                                        else
                                        {
                                            ejecutaTrasladoTellion(arrayInfo);
                                        }
                                    }
                                    else
                                    {
                                        Ext.Msg.alert('Mensaje', datosValidaTrasladoMd.strMensaje, function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                            }
                                        });
                                    }
                                },
                                failure: function(result) {
                                    Ext.Msg.alert('Alerta', result.responseText);
                                    store.load();
                                }
                            });
                        }
                        else
                        {
                            ejecutaTrasladoTellion(arrayInfo);
                        }
                    }
                    else{
                        if(flag==1){
                            Ext.Msg.alert("Validacion","Alguna Mac esta incorrecta, favor revisar!", function(btn){
                                    if(btn=='ok'){
                                    }
                            });
                        }
                        else if(flag==2){
                            Ext.Msg.alert("Validacion","Macs no pueden ser iguales, favor revisar!", function(btn){
                                    if(btn=='ok'){
                                    }
                            });
                        }
                        else if(flag==3){
                            Ext.Msg.alert("Validacion","Datos del Ont incorrectos, favor revisar!", function(btn){
                                    if(btn=='ok'){
                                    }
                            });
                        }
                        else if(flag==4){
                            Ext.Msg.alert("Validacion","Datos del Wifi incorrectos, favor revisar!", function(btn){
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
        title: 'Activar Puerto',
        modal: true,
        width: 1200,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function panelTrasladoHuawei(datos, data, storeInterfacesSplitter, storeModelosCpe)
{
    var strNombrePlanProd           = data.nombrePlan;
    var strEtiquetaPlanProd         = 'Plan';
    var strEtiquetaPerfilVelocidad  = 'Perfil';
    var strNombreTxtPerfilVelocidad = 'perfil';
    var strValorPerfilVelocidad     = data.perfilDslam;
    if(datos[0].strEsInternetLite === 'SI')
    {
        strNombrePlanProd           = data.nombreProducto;
        strEtiquetaPlanProd         = 'Producto';
        strEtiquetaPerfilVelocidad  = 'Velocidad';
        strNombreTxtPerfilVelocidad = 'velocidad';
        strValorPerfilVelocidad     = data.velocidadISB + " MB";
    }
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
                title: 'Informacion del Servicio',
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
                                name: 'plan',
                                fieldLabel: strEtiquetaPlanProd,
                                displayField: strNombrePlanProd,
                                value: strNombrePlanProd,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
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
                                name: 'capacidadUno',
                                fieldLabel: 'Capacidad Uno',
                                displayField: data.capacidadUno,
                                value: data.capacidadUno,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'capacidadDos',
                                fieldLabel: 'Capacidad Dos',
                                displayField: data.capacidadDos,
                                value: data.capacidadDos,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'capacidadTres',
                                fieldLabel: 'Capacidad Int/Prom Uno',
                                displayField: data.capacidadTres,
                                value: data.capacidadTres,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'capacidadCuatro',
                                fieldLabel: 'Capacidad Int/Prom Dos',
                                displayField: data.capacidadCuatro,
                                value: data.capacidadCuatro,
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
                                fieldLabel: 'Ultima Milla',
                                displayField: data.ultimaMilla,
                                value: data.ultimaMilla,
                                readOnly: true,
                                width: '35%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id: strNombreTxtPerfilVelocidad,
                                name: strNombreTxtPerfilVelocidad,
                                fieldLabel: strEtiquetaPerfilVelocidad,
                                displayField: strValorPerfilVelocidad,
                                value: strValorPerfilVelocidad,
                                readOnly: true,
                                width: '35%'
                            },
                            { width: '10%', border: false}
                        ]
                    }

                ]
            },//cierre de la informacion servicio/producto
            
            //informacion de backbone
            {
                xtype: 'fieldset',
                title: 'Informacion de backbone Anterior',
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
                                displayField: datos[0].nombreElementoAnterior,
                               value: datos[0].nombreElementoAnterior,

                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'ipElemento',
                                fieldLabel: 'Ip Elemento',
                                displayField: datos[0].ipElementoAnterior,
                                value: datos[0].ipElementoAnterior,
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
                                displayField: datos[0].puertoElementoAnterior,
                                value: datos[0].puertoElementoAnterior,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'modeloELemento',
                                fieldLabel: 'Modelo Elemento',
                                displayField: datos[0].modeloElementoAnterior,
                                value: datos[0].modeloElementoAnterior,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'splitterElemento',
                                fieldLabel: 'Splitter Elemento',
                                displayField: datos[0].nombreSplitterAnterior,
                                value: datos[0].nombreSplitterAnterior,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'splitterInterfaceElemento',
                                fieldLabel: 'Splitter Interface',
                                displayField: datos[0].puertoSplitterAnterior,
                                value: datos[0].puertoSplitterAnterior,
                                readOnly: true,
                                width: '25%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'cajaElemento',
                                fieldLabel: 'Caja Elemento',
                                displayField: datos[0].nombreCajaAnterior,
                                value: datos[0].nombreCajaAnterior,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            { width: '10%', border: false},
                            { width: '10%', border: false}

                        ]
                    }

                ]
            },//cierre de info de backbone anterior

            //informacion de backbone
            {
                xtype: 'fieldset',
                title: 'Informacion de backbone',
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
                                fieldLabel: 'Splitter Elemento',
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
                                fieldLabel: 'Splitter Interface',
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
                                fieldLabel: 'Caja Elemento',
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
                title: 'Informacion de los Elementos del Cliente',
                defaultType: 'textfield',
                defaults: { 
                    width: 540
                },
                items: [

                    {
                        xtype: 'fieldset',
                        title: 'Informacion del ONT',
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
                                        id:'serieCpe',
                                        name: 'serieCpe',
                                        fieldLabel: 'Serie ONT',
                                        displayField: "",
                                        value: "",
                                        width: '25%'
                                    },
                                    { width: '20%', border: false},
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'modeloCpe',
                                        name: 'modeloCpe',
                                        fieldLabel: 'Modelo ONT',
                                        displayField:'modelo',
                                        valueField: 'modelo',
                                        loadingText: 'Buscando...',
                                        store: storeModelosCpe,
                                        width: '25%',
                                        listeners: {
                                            blur: function(combo){
                                                Ext.Ajax.request({
                                                    url: buscarCpeHuaweiNaf,
                                                    method: 'post',
                                                    params: { 
                                                        serieCpe: Ext.getCmp('serieCpe').getValue(),
                                                        modeloElemento: combo.getValue(),
                                                        estado: 'PI',
                                                        bandera: 'ActivarServicio'
                                                    },
                                                    success: function(response){
                                                        var respuesta = response.responseText.split("|");
                                                        var status = respuesta[0];
                                                        var mensaje = respuesta[1].split(",");
                                                        var descripcion = mensaje[0];
                                                        var macOntNaf = mensaje[1];
                                                        console.log(status);
                                                        if(status=="OK")
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
                                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                    }
                                                });
                                            }
                                        }
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'macCpe',
                                        name: 'macCpe',
                                        fieldLabel: 'Mac ONT',
                                        displayField: "",
                                        value: "",
                                        readOnly: true,
                                        width: '25%'
                                    },
                                    {
                                        xtype: 'hidden',
                                        id:'validacionMacOnt',
                                        name: 'validacionMacOnt',
                                        value: "",
                                        width: '20%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        id:'descripcionCpe',
                                        name: 'descripcionCpe',
                                        fieldLabel: 'Descripcion ONT',
                                        displayField: "",
                                        value: "",
                                        readOnly: true,
                                        width: '25%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------

                                ]//cierre del container table
                            }                


                        ]//cierre del fieldset
                    }
                ]

            },//cierre informacion de los elementos del cliente

        ],
        buttons: [{
                        text: 'Activar',
                        formBind: true,
                        handler: function(){
                            var modeloCpe = Ext.getCmp('modeloCpe').getValue();
                            var serieCpe = Ext.getCmp('serieCpe').getValue();
                            var descripcionCpe = Ext.getCmp('descripcionCpe').getValue();
                            var macCpe = Ext.getCmp('macCpe').getValue();

                            var interfaceSplitter =Ext.getCmp('splitterInterfaceElemento').getRawValue();

                            var validacion=false;
                            flag = 0;
                            if(serieCpe=="" || macCpe==""){
                                validacion=false;
                            }
                            else{
                                validacion=true;
                            }

                            if(descripcionCpe=="ELEMENTO ESTADO INCORRECTO" || 
                               descripcionCpe=="ELMENTO CON SALDO CERO" || 
                               descripcionCpe=="NO EXISTE ELEMENTO")
                            {
                                validacion=false;
                                flag=2;
                            }

                            if(validacion){
                                arrayInfo = new Array();
                                arrayInfo["macCpe"]       = macCpe;
                                arrayInfo["login"]        = data.login;
                                arrayInfo["planId"]       = data.planId;
                                arrayInfo["serieCpe"]     = serieCpe;
                                arrayInfo["modeloCpe"]    = modeloCpe;
                                arrayInfo["formPanel"]    = formPanel;
                                arrayInfo["win"]          = win;
                                arrayInfo["store"]        = store;
                                arrayInfo["idServicio"]   = data.idServicio;
                                arrayInfo["productoId"]   = data.productoId;
                                arrayInfo["ultimaMilla"]  = data.ultimaMilla;
                                arrayInfo["perfilDslam"]  = data.perfilDslam;
                                arrayInfo["capacidadUno"] = data.capacidadUno;
                                arrayInfo["capacidadDos"] = data.capacidadDos;
                                arrayInfo["strEsInternetLite"]      = datos[0].strEsInternetLite;
                                arrayInfo["interfaceSplitter"]      = interfaceSplitter;
                                arrayInfo["interfaceElementoId"]    = data.interfaceElementoId;
                                arrayInfo["strTieneSmartWifiRenta"] = datos[0].strTieneSmartWifiRenta;
                                if (prefijoEmpresa === 'MD'|| prefijoEmpresa === 'EN')
                                {
                                    connValidaEquipos.request({
                                        url: urlValidaEleOrigenTraslado,
                                        method: 'post',
                                        timeout: 400000,
                                        params: {idServicio: data.idServicio},
                                        success: function(responseValida) {
                                            var datosValidaTrasladoMd = Ext.JSON.decode(responseValida.responseText);
                                            if (datosValidaTrasladoMd.strEstado == "OK")
                                            {
                                                if (datosValidaTrasladoMd.strExistenEquiposFacturar == "SI")
                                                {
                                                    Ext.MessageBox.show({
                                                        title: "Información",
                                                        cls: 'msg_floating',
                                                        msg: datosValidaTrasladoMd.strMensaje,
                                                        icon: Ext.MessageBox.INFO,
                                                        buttons: Ext.Msg.OK,
                                                        fn: function(buttonId)
                                                        {
                                                            if (buttonId === "ok")
                                                            {
                                                                ejecutaTrasladoHuawei(arrayInfo);
                                                            }
                                                        }
                                                    });
                                                }
                                                else
                                                {
                                                    ejecutaTrasladoHuawei(arrayInfo);
                                                }
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Mensaje', datosValidaTrasladoMd.strMensaje, function(btn) {
                                                    if (btn == 'ok') {
                                                        store.load();
                                                    }
                                                });
                                            }
                                        },
                                        failure: function(result) {
                                            Ext.Msg.alert('Alerta', result.responseText);
                                            store.load();
                                        }
                                    });
                                }
                                else
                                {
                                    ejecutaTrasladoHuawei(arrayInfo);
                                }
                            }
                            else{
                                if(flag==1){
                                    Ext.Msg.alert("Validacion","Alguna Mac esta incorrecta, favor revisar!", function(btn){
                                            if(btn=='ok'){
                                            }
                                    });
                                }
                                else if(flag==2){
                                    Ext.Msg.alert("Validacion","Datos del Ont incorrectos, favor revisar!", function(btn){
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
        title: 'Activar Servicio',
        modal: true,
        width: 1200,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function ejecutaTrasladoHuawei(arrayInfo)
{
    Ext.get(arrayInfo["formPanel"].getId()).mask('Guardando datos y Ejecutando Scripts!');
    Ext.Ajax.request({
        url: activarClienteBoton,
        method: 'post',
        timeout: 800000,
        params: { 
            idServicio                  : arrayInfo["idServicio"],
            idProducto                  : arrayInfo["productoId"],
            perfil                      : arrayInfo["perfilDslam"],
            login                       : arrayInfo["login"],
            capacidad1                  : arrayInfo["capacidadUno"],
            capacidad2                  : arrayInfo["capacidadDos"],
            interfaceElementoId         : arrayInfo["interfaceElementoId"],
            interfaceElementoSplitterId : arrayInfo["interfaceSplitter"],
            ultimaMilla                 : arrayInfo["ultimaMilla"],
            plan                        : arrayInfo["planId"],
            serieOnt                    : arrayInfo["serieCpe"],
            modeloOnt                   : arrayInfo["modeloCpe"],
            macOnt                      : arrayInfo["macCpe"],
            strTieneSmartWifiRenta      : arrayInfo["strTieneSmartWifiRenta"],
            strEsInternetLite           : arrayInfo["strEsInternetLite"]
        },
        success: function(response){
            Ext.get(arrayInfo["formPanel"].getId()).unmask();
            if(response.responseText == "OK"){
                Ext.Msg.alert('Mensaje','Se Activo el Cliente', function(btn){
                    if(btn=='ok'){
                        arrayInfo["win"].destroy();
                        arrayInfo["store"].load();
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
                Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
            }
            else if(response.responseText == "NO EXISTE PRODUCTO"){
                Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
            }
            else if(response.responseText == "NO EXISTE CPE"){
                Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
            }
            else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                Ext.Msg.alert('Mensaje ','Equipo no esta en PENDIENTE INSTALACION/RETIRADO, favor revisar!' );
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
            Ext.get(arrayInfo["formPanel"].getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

function ejecutaTrasladoTellion(arrayInfo)
{
    Ext.get(arrayInfo["formPanel"].getId()).mask('Guardando datos y Ejecutando Scripts!');
    Ext.Ajax.request({
        url: activarClienteBoton,
        method: 'post',
        timeout: 1000000,
        params: { 
            idServicio                  : arrayInfo["idServicio"],
            idProducto                  : arrayInfo["productoId"],
            perfil                      : arrayInfo["perfilDslam"],
            login                       : arrayInfo["login"],
            capacidad1                  : arrayInfo["capacidadUno"],
            capacidad2                  : arrayInfo["capacidadDos"],
            interfaceElementoId         : arrayInfo["interfaceElementoId"],
            interfaceElementoSplitterId : arrayInfo["interfaceSplitter"],
            ultimaMilla                 : arrayInfo["ultimaMilla"],
            plan                        : arrayInfo["planId"],
            serieOnt                    : arrayInfo["serieCpe"],
            modeloOnt                   : arrayInfo["modeloCpe"],
            macOnt                      : arrayInfo["macCpe"],
            serieWifi                   : arrayInfo["serieWifi"],
            modeloWifi                  : arrayInfo["modeloWifi"],
            macWifi                     : arrayInfo["macWifi"],
            strTieneSmartWifiRenta      : arrayInfo["strTieneSmartWifiRenta"],
            strEsInternetLite           : arrayInfo["strEsInternetLite"]
        },
        success: function(response){
            Ext.get(arrayInfo["formPanel"].getId()).unmask();
            if(response.responseText == "OK"){
                Ext.Msg.alert('Mensaje','Se Activo el Cliente', function(btn){
                    if(btn=='ok'){
                        arrayInfo["win"].destroy();
                        arrayInfo["store"].load();
                    }
                });
            }
            else if(response.responseText == "NO ID CLIENTE"){
                Ext.Msg.alert('Mensaje ','Slot no existe, favor revise la Linea Pon donde debe enganchar el cliente!' );
            }
            else if(response.responseText == "MAX ID CLIENTE"){
                Ext.Msg.alert('Mensaje ','Limite de clientes por Puerto esta en el maximo, <br> Favor comunicarse con el departamento de GEPON' );
            }
            else if(response.responseText == "CANTIDAD CERO"){
                Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar!' );
            }
            else if(response.responseText == "NO EXISTE PRODUCTO"){
                Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
            }
            else if(response.responseText == "NO EXISTE CPE"){
                Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar!' );
            }
            else if(response.responseText == "CPE NO ESTA EN ESTADO"){
                Ext.Msg.alert('Mensaje ','Equipo no esta en PENDIENTE INSTALACION/RETIRADO, favor revisar!' );
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
            Ext.get(arrayInfo["formPanel"].getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

function panelMismosRecursosTraslado(datos, data, storeInterfacesSplitter)
{
    var strNombrePlanProd           = data.nombrePlan;
    var strEtiquetaPlanProd         = 'Plan';
    var strEtiquetaPerfilVelocidad  = 'Perfil';
    var strNombreTxtPerfilVelocidad = 'perfil';
    var strValorPerfilVelocidad     = data.perfilDslam;
    if(datos[0].strEsInternetLite === 'SI')
    {
        strNombrePlanProd           = data.nombreProducto;
        strEtiquetaPlanProd         = 'Producto';
        strEtiquetaPerfilVelocidad  = 'Velocidad';
        strNombreTxtPerfilVelocidad = 'velocidad';
        strValorPerfilVelocidad     = data.velocidadISB + " MB";
    }
    //mostrar datos
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
        items:
        [
            //informacion del servicio/producto
            {
                xtype: 'fieldset',
                title: 'Informacion del Servicio',
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
                                name: 'plan',
                                fieldLabel: strEtiquetaPlanProd,
                                displayField: strNombrePlanProd,
                                value: strNombrePlanProd,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
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
                                name: 'capacidadUno',
                                fieldLabel: 'Capacidad Uno',
                                displayField: data.capacidadUno,
                                value: data.capacidadUno,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'capacidadDos',
                                fieldLabel: 'Capacidad Dos',
                                displayField: data.capacidadDos,
                                value: data.capacidadDos,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'capacidadTres',
                                fieldLabel: 'Capacidad Int/Prom Uno',
                                displayField: data.capacidadTres,
                                value: data.capacidadTres,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'capacidadCuatro',
                                fieldLabel: 'Capacidad Int/Prom Dos',
                                displayField: data.capacidadCuatro,
                                value: data.capacidadCuatro,
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
                                fieldLabel: 'Ultima Milla',
                                displayField: data.ultimaMilla,
                                value: data.ultimaMilla,
                                readOnly: true,
                                width: '35%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:strNombreTxtPerfilVelocidad,
                                name: strNombreTxtPerfilVelocidad,
                                fieldLabel: strEtiquetaPerfilVelocidad,
                                displayField: strValorPerfilVelocidad,
                                value: strValorPerfilVelocidad,
                                readOnly: true,
                                width: '35%'
                            },
                            { width: '10%', border: false}
                        ]
                    }

                ]
            },//cierre de la informacion servicio/producto

            //informacion de backbone
            {
                xtype: 'fieldset',
                title: 'Informacion de backbone Anterior',
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
                                displayField: datos[0].nombreElementoAnterior,
                               value: datos[0].nombreElementoAnterior,

                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'ipElemento',
                                fieldLabel: 'Ip Elemento',
                                displayField: datos[0].ipElementoAnterior,
                                value: datos[0].ipElementoAnterior,
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
                                displayField: datos[0].puertoElementoAnterior,
                                value: datos[0].puertoElementoAnterior,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'modeloELemento',
                                fieldLabel: 'Modelo Elemento',
                                displayField: datos[0].modeloElementoAnterior,
                                value: datos[0].modeloElementoAnterior,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'splitterElemento',
                                fieldLabel: 'Splitter Elemento',
                                displayField: datos[0].nombreSplitterAnterior,
                                value: datos[0].nombreSplitterAnterior,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'splitterInterfaceElemento',
                                fieldLabel: 'Splitter Interface',
                                displayField: datos[0].puertoSplitterAnterior,
                                value: datos[0].puertoSplitterAnterior,
                                readOnly: true,
                                width: '25%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'cajaElemento',
                                fieldLabel: 'Caja Elemento',
                                displayField: datos[0].nombreCajaAnterior,
                                value: datos[0].nombreCajaAnterior,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            { width: '10%', border: false},
                            { width: '10%', border: false}

                        ]
                    }

                ]
            },//cierre de info de backbone anterior

            //informacion de backbone
            {
                xtype: 'fieldset',
                title: 'Informacion de backbone',
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
//                                id:'perfilDslam',
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
                                fieldLabel: 'Splitter Elemento',
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
                                fieldLabel: 'Splitter Interface',
                                displayField: 'nombreInterface',
                                valueField:'idInterface',
                                value: datos[0].nombrePuertoSplitter,
                                loadingText: 'Buscando...',
                                store: storeInterfacesSplitter,
                                width: '25%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'cajaElemento',
                                fieldLabel: 'Caja Elemento',
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
            }//cierre de info de backbone
        ],
        buttons: 
        [
            {
                text: 'Activar',
                formBind: true,
                handler: function(){
                    validacion= true;
                    if(validacion){
                        Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');
                        var interfaceSplitter =Ext.getCmp('splitterInterfaceElemento').getRawValue();

                        Ext.Ajax.request({
                            url: activarClienteBoton,
                            method: 'post',
                            timeout: 400000,
                            params: { 
                                idServicio                  : data.idServicio,
                                idProducto                  : data.productoId,
                                perfil                      : data.perfilDslam,
                                login                       : data.login,
                                capacidad1                  : data.capacidadUno,
                                capacidad2                  : data.capacidadDos,
                                interfaceElementoId         : data.interfaceElementoId,
                                interfaceElementoSplitterId : interfaceSplitter,
                                ultimaMilla                 : data.ultimaMilla,
                                plan                        : data.planId,
                                strTieneSmartWifiRenta      : datos[0].strTieneSmartWifiRenta,
                                strEsInternetLite           : datos[0].strEsInternetLite
                            },
                            success: function(response){
                                Ext.get(formPanel.getId()).unmask();
                                if(response.responseText == "OK"){
                                    Ext.Msg.alert('Mensaje','Se Activo Puerto', function(btn){
                                        if(btn=='ok'){
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

                    }
                    else{
                        Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }

                }
            },
            {
                text: 'Cancelar',
                handler: function(){
                    win.destroy();
                }
            }
        ]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Activar Puerto / Traslado',
        modal: true,
        width: 580,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function panelTrasladoExtenderDualBand(datos, data)
{
    //mostrar datos
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
        items:
        [
            //informacion del servicio/producto
            {
                xtype: 'fieldset',
                title: 'Información del Servicio',
                defaultType: 'textfield',
                defaults: { 
                    width: 540,
                    height: 60
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
                                name: 'plan',
                                fieldLabel: 'Plan/Producto',
                                displayField: data.nombrePlan?data.nombrePlan:data.nombreProducto,
                                value: data.nombrePlan?data.nombrePlan:data.nombreProducto,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
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
                                id:'ultimaMilla',
                                name: 'ultimaMilla',
                                fieldLabel: 'Ultima Milla',
                                displayField: data.ultimaMilla,
                                value: data.ultimaMilla,
                                readOnly: true,
                                width: '35%'
                            },
                            { width: '15%', border: false},
                            {
                                border: false,
                                width: '35%'
                            },
                            { width: '10%', border: false}
                        ]
                    }

                ]
            },//cierre de la informacion servicio/producto

            //informacion de extender
            {
                xtype: 'fieldset',
                title: 'Información de equipo Extender Dual Band',
                defaultType: 'textfield',
                defaults: { 
                    width: 540,
                    height: 90
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
                                name: 'nombreElemento',
                                fieldLabel: 'Nombre Elemento',
                                displayField: datos[0].strNombreElemento,
                                value: datos[0].strNombreElemento,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                border: false,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'serie',
                                fieldLabel: 'Serie Elemento',
                                displayField: datos[0].strSerieElemento,
                                value: datos[0].strSerieElemento,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'modeloELemento',
                                fieldLabel: 'Modelo Elemento',
                                displayField: datos[0].strModeloElemento,
                                value: datos[0].strModeloElemento,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'mac',
                                fieldLabel: 'Mac Elemento',
                                displayField: datos[0].strMacElemento,
                                value: datos[0].strMacElemento,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'tipoElemento',
                                fieldLabel: 'Tipo Elemento',
                                displayField: datos[0].strTipoElemento,
                                value: datos[0].strTipoElemento,
                                readOnly: true,
                                width: '25%'
                            },
                            { width: '10%', border: false}

                        ]
                    }

                ]
            }//cierre de info de backbone anterior
         ],
        buttons: 
        [
            {
                text: 'Trasladar',
                formBind: true,
                handler: function(){
                    validacion= true;
                    if(validacion){
                        Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');
                        Ext.Ajax.request({
                            url: trasladarExtenderDualBandAjax,
                            method: 'post',
                            timeout: 400000,
                            params: { 
                                intIdServicio    : data.idServicio,
                                intIdServicioRef : data.idServicioRefIpFija,
                                strSerieElemento : datos[0].strSerieElemento,
                                strMacElemento   : datos[0].strMacElemento
                            },
                            success: function(response){
                                Ext.get(formPanel.getId()).unmask();
                                var objData = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.status;
                                var strMensaje = objData.mensaje;
                                if(strStatus == "OK"){
                                    Ext.Msg.alert('Mensaje','Se Trasladó el equipo Extender Dual Band', function(btn){
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
                            failure: function(result)
                            {
                                Ext.get(formPanel.getId()).unmask();
                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                            }
                        });

                    }
                    else{
                        Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }

                }
            },
            {
                text: 'Cancelar',
                handler: function(){
                    win.destroy();
                }
            }
        ]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Trasladar Extender Dual Band',
        modal: true,
        width: 580,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function panelSincronizarExtenderDualBand(datos, data)
{
    //mostrar datos
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
        items:
        [
            //informacion del servicio/producto
            {
                xtype: 'fieldset',
                title: 'Información del Servicio',
                defaultType: 'textfield',
                defaults: { 
                    width: 540,
                    height: 60
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
                                name: 'plan',
                                fieldLabel: 'Plan/Producto',
                                displayField: data.nombrePlan?data.nombrePlan:data.nombreProducto,
                                value: data.nombrePlan?data.nombrePlan:data.nombreProducto,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
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
                                id:'ultimaMilla',
                                name: 'ultimaMilla',
                                fieldLabel: 'Ultima Milla',
                                displayField: data.ultimaMilla,
                                value: data.ultimaMilla,
                                readOnly: true,
                                width: '35%'
                            },
                            { width: '15%', border: false},
                            {
                                border: false,
                                width: '35%'
                            },
                            { width: '10%', border: false}
                        ]
                    }

                ]
            },//cierre de la informacion servicio/producto

            //informacion de extender
            {
                xtype: 'fieldset',
                title: 'Información de equipo Extender Dual Band',
                defaultType: 'textfield',
                defaults: { 
                    width: 540,
                    height: 90
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
                                name: 'nombreElemento',
                                fieldLabel: 'Nombre Elemento',
                                displayField: datos[0].strNombreElemento,
                                value: datos[0].strNombreElemento,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                border: false,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'serie',
                                fieldLabel: 'Serie Elemento',
                                displayField: datos[0].strSerieElemento,
                                value: datos[0].strSerieElemento,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'modeloELemento',
                                fieldLabel: 'Modelo Elemento',
                                displayField: datos[0].strModeloElemento,
                                value: datos[0].strModeloElemento,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'mac',
                                fieldLabel: 'Mac Elemento',
                                displayField: datos[0].strMacElemento,
                                value: datos[0].strMacElemento,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'tipoElemento',
                                fieldLabel: 'Tipo Elemento',
                                displayField: datos[0].strTipoElemento,
                                value: datos[0].strTipoElemento,
                                readOnly: true,
                                width: '25%'
                            },
                            { width: '10%', border: false}

                        ]
                    }

                ]
            }//cierre de info de backbone anterior
         ],
        buttons: 
        [
            {
                text: 'Sincronizar',
                formBind: true,
                handler: function(){
                    validacion= true;
                    if(validacion){
                        Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');
                        Ext.Ajax.request({
                            url: sincronizarExtenderDualBandAjax,
                            method: 'post',
                            timeout: 400000,
                            params: { 
                                intIdServicio    : data.idServicio,
                                intIdServicioRef : data.idServicioRefIpFija,
                                strSerieElemento : datos[0].strSerieElemento,
                                strMacElemento   : datos[0].strMacElemento
                            },
                            success: function(response){
                                Ext.get(formPanel.getId()).unmask();
                                var objData = Ext.JSON.decode(response.responseText);
                                var strStatus = objData.status;
                                var strMensaje = objData.mensaje;
                                if(strStatus == "OK"){
                                    Ext.Msg.alert('Mensaje','Se Sincronizó el equipo Extender Dual Band', function(btn){
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
                            failure: function(result)
                            {
                                Ext.get(formPanel.getId()).unmask();
                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                            }
                        });

                    }
                    else{
                        Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }

                }
            },
            {
                text: 'Cancelar',
                handler: function(){
                    win.destroy();
                }
            }
        ]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Sincronizar Extender Dual Band',
        modal: true,
        width: 580,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function subirActaRecepcion(data){
  
      var conn = new Ext.data.Connection({
	      listeners: {
		  'beforerequest': {
		      fn: function (con, opt) {
			  Ext.get(document.body).mask('Procesando...');
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
              
     var formPanel = Ext.create('Ext.form.Panel', 
     {      
        width: 500,
        frame: true,        
        bodyPadding: '10 10 0',

        defaults: {
            anchor: '100%',
            allowBlank: false,
            msgTarget: 'side',
            labelWidth: 50
        },

        items: [{
            xtype: 'filefield',
            id: 'form-file',
            name: 'archivo',
            emptyText: 'Seleccione una Archivo',
            buttonText: 'Browse',
            buttonConfig: {
                iconCls: 'upload-icon'
            }
        }],
				
        buttons: [{
            text: 'Subir',
            handler: function(){
                 var form = this.up('form').getForm();
                 if(form.isValid())
		 {		   
		      form.submit({		    
			    url: '/soporte/gestion_documentos/fileUpload',
			    params :{
				  servicio: data.idServicio,
                  codigo: 'ACT'
			    },
			    waitMsg: 'Procesando Archivo...',
			    success: function(fp, o) 
			    {   				  
				  Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn){
					if(btn=='ok')
					{
					      win.destroy();					      
					      //se abre una ventana con la encuesta
					      Ext.MessageBox.wait("Abriendo Encuesta...");					      
					      window.location = "../../tecnico/clientes/"+data.idServicio+"/encuesta";					      
					}				 	      				
				  });
			    },
			    failure: function(fp, o) {
				  Ext.Msg.alert("Alerta",o.result.respuesta);
			    }
			});
                }
            }
        },{
            text: 'Cancelar',
            handler: function() {
	        this.up('form').getForm().reset();
		store.load();		
	        win.destroy();  
		
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Subir Acta Recepcion',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function reintentarPromoBw(data,gridIndex, accion){
    Ext.Msg.show({
        title:'Confirmar',
        msg: 'Desea ejecutar el reintento del proceso de validación y aplicación de promoción?',
        buttons: Ext.Msg.YESNO,
        icon: Ext.MessageBox.QUESTION,
        buttonText: {
            yes: 'Si', no: 'No'
        },
        fn: function(btn){
            if(btn=='yes'){
                Ext.get(gridServicios.getId()).mask('Reintentando proceso de validación y aplicación de promociones BW...');

                    Ext.Ajax.request({
                        url: strUrlReintentarPromoBw,
                        method: 'post',
                        timeout: 400000,
                        params: { 
                            idServicio: data.idServicio
                        },
                        success: function(response)
                        {
                            Ext.get(gridServicios.getId()).unmask();
                            var objData = Ext.JSON.decode(response.responseText);
                            var strMensaje = objData.mensaje;
                            store.load();
                            Ext.Msg.alert('Mensaje ',strMensaje );
                        },
                        failure: function(result)
                        {
                            store.load();
                            Ext.get(gridServicios.getId()).unmask();
                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                        }
                    }); 
            }
        }
    });
}

//Inicio Bloque de funciones para servicos safe city
function getDispositivosNodoSafeCityOnt(data) {

    //Precargamos el json con los dispositivos que seran instalados.
    var arrayDispositivosNodo = [];
    if (!Ext.isEmpty(data.strJsonDipositivosNodo)) {
        arrayDispositivosNodo = Ext.JSON.decode(data.strJsonDipositivosNodo);
        if (Ext.isEmpty(arrayDispositivosNodo)) {
            arrayDispositivosNodo = [];
        }
    }

    var storeDispositivosNodoSC = new Ext.data.Store ({
        autoDestroy: true,
        data       : arrayDispositivosNodo,
        proxy      : {type: 'memory'},
        fields     : [
            'idPersonaRol',
            'idControl',
            'serieElemento',
            'modeloElemento',
            'tipoElemento',
            'descripcionElemento',
            'macElemento'
        ]
    });

    var smSeleccion = new Ext.selection.CheckboxModel({
        mode: 'MULTI',
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridDispositivosNodoSafeCity");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("btnRemover").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosNodoSafeCity");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("btnRemover").setDisabled(true);
                }
            }
        }
    });

    var gridDispositivosNodoSafeCity = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosNodoSafeCity',
        width    :  550,
        height   :  130,
        store    :  storeDispositivosNodoSC,
        selModel :  smSeleccion,
        loadMask :  true,
        frame    :  false,
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj) {
                var position = view.getPositionByEvent(eventobj);
                var value    = record.data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : "Copiar texto",
                    msg    : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        dockedItems: [{
            xtype : 'toolbar',
            dock  : 'top',
            align : '<-',
            items : [{
                id  : 'btnAgregar',
                text: '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>'+
                      '&nbsp;&nbsp;Agregar Dipositivo',
                scope: this,
                handler: function() {
                    //1: Nodo
                    agregarDispositivosOntNodo(data,1);
                }
            },
            {
                id  : 'btnRemover',
                text: '<label style="color:red;"><i class="fa fa-trash" aria-hidden="true"></i></label>'+
                      '&nbsp;&nbsp;Remover Dipositivo',
                scope: this,
                disabled: true,
                handler: function() {
                    var arraySelection = Ext.getCmp("gridDispositivosNodoSafeCity").getSelectionModel().getSelection();
                    $.each(arraySelection, function(i, item) {
                        var index = storeDispositivosNodoSC.findBy(function (record) {
                            return record.data.serieElemento === item.data.serieElemento;
                        });
                        if (index >= 0) {
                            storeDispositivosNodoSC.removeAt(index);
                            Ext.Msg.alert('Mensaje ',`Elemento removido con éxito`);
                        }
                    });
                }
            }]
        }],
        columns:
        [
            {
                dataIndex : 'idControl',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                dataIndex : 'idPersonaRol',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : 'Serie',
                dataIndex : 'serieElemento',
                width     :  125,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Modelo',
                dataIndex : 'modeloElemento',
                width     :  125,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Descripción',
                dataIndex : 'descripcionElemento',
                width     :  180,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Mac',
                dataIndex : 'macElemento',
                width     :  100,
                sortable  :  false,
                hideable  :  false
            }
        ]
    });

    return gridDispositivosNodoSafeCity;
}

function getDispositivosClienteSafeCityOnt(data) {

    //Precargamos el json con los dispositivos que seran instalados.
    var arrayDispositivosCliente = [];
    if (!Ext.isEmpty(data.strJsonDipositivosNodo)) {
        arrayDispositivosCliente = Ext.JSON.decode(data.strJsonDipositivosNodo);
        if (Ext.isEmpty(arrayDispositivosCliente)) {
            arrayDispositivosCliente = [];
        }
    }

    var storeDispositivosClienteSC = new Ext.data.Store ({
        autoDestroy: true,
        data       : arrayDispositivosCliente,
        proxy      : {type: 'memory'},
        fields     : [
            'idPersonaRol',
            'idControl',
            'serieElemento',
            'modeloElemento',
            'tipoElemento',
            'descripcionElemento',
            'macElemento'
        ]
    });

    var smSeleccionb = new Ext.selection.CheckboxModel({
        mode: 'MULTI',
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridDispositivosClienteSafeCity");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("btnRemoverb").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosClienteSafeCity");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("btnRemoverb").setDisabled(true);
                }
            }
        }
    });

    var gridDispositivosClienteSafeCity = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosClienteSafeCity',
        width    :  550,
        height   :  130,
        store    :  storeDispositivosClienteSC,
        selModel :  smSeleccionb,
        loadMask :  true,
        frame    :  false,
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj) {
                var position = view.getPositionByEvent(eventobj);
                var value    = record.data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : "Copiar texto",
                    msg    : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        dockedItems: [{
            xtype : 'toolbar',
            dock  : 'top',
            align : '<-',
            items : [{
                id  : 'btnAgregarb',
                text: '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>'+
                      '&nbsp;&nbsp;Agregar Dipositivo',
                scope: this,
                handler: function() {
                    //2: cliente
                    agregarDispositivosOntCliente(data,2);
                }
            },
            {
                id  : 'btnRemoverb',
                text: '<label style="color:red;"><i class="fa fa-trash" aria-hidden="true"></i></label>'+
                      '&nbsp;&nbsp;Remover Dipositivo',
                scope: this,
                disabled: true,
                handler: function() {
                    var arraySelection = Ext.getCmp("gridDispositivosClienteSafeCity").getSelectionModel().getSelection();
                    $.each(arraySelection, function(i, item) {
                        var index = storeDispositivosClienteSC.findBy(function (record) {
                            return record.data.serieElemento === item.data.serieElemento;
                        });
                        if (index >= 0) {
                            storeDispositivosClienteSC.removeAt(index);
                            Ext.Msg.alert('Mensaje ',`Elemento removido con éxito`);
                        }
                    });
                }
            }]
        }],    
        columns:
        [
            {
                dataIndex : 'idControl',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                dataIndex : 'idPersonaRol',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : 'Serie',
                dataIndex : 'serieElemento',
                width     :  125,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Modelo',
                dataIndex : 'modeloElemento',
                width     :  125,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Descripción',
                dataIndex : 'descripcionElemento',
                width     :  180,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : 'Mac',
                dataIndex : 'macElemento',
                width     :  100,
                sortable  :  false,
                hideable  :  false
            }
        ]
    });

    return gridDispositivosClienteSafeCity;
}

function agregarDispositivosOntNodo(data,accion) {

    var stylebutton  = '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>&nbsp;&nbsp;Agregar';
    var idEmpleado   =  Ext.getCmp('comboFilterTecnico').getValue();
    var empleado     =  Ext.getCmp('comboFilterTecnico').getRawValue();
    var titulo       =  null;

    if (Ext.isEmpty(idEmpleado)) {
        Ext.Msg.show({
            title: 'Alerta',msg: 'Por favor seleccione el Técnico Encargado.!',
            icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
            buttonText: {cancel: 'Cerrar'}
        });
        return;
    }

    if (accion === 1) {
        titulo       = 'Nodo';
    }

    var smSeleccion = new Ext.selection.CheckboxModel({
        mode: 'MULTI',
        allowDeselect: true,
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridDispositivosAsignadosTecnico");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("idBtnAgregarSafeCity").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosAsignadosTecnico");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("idBtnAgregarSafeCity").setDisabled(true);
                }
            }
        }
    });

    var storeEquiposAsignados = new Ext.data.Store ({
        autoLoad :  true,
        pageSize :  2000,
        total    : 'total',
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    :  url_equipoAdicionales,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'result',
                totalProperty: 'total'
            },
            extraParams: {
                'intIdPersona'    : idEmpleado,
                'intPerteneceElemento' : 0
            }
        },
        fields: [
            {name: 'idPersona'          , mapping: 'idPersona'},
            {name: 'idPersonaRol'       , mapping: 'idPersonaRol'},
            {name: 'idControl'          , mapping: 'idControl'},
            {name: 'serieElemento'      , mapping: 'serieElemento'},
            {name: 'modeloElemento'     , mapping: 'modeloElemento'},
            {name: 'tipoElemento'       , mapping: 'tipoElemento'},
            {name: 'descripcionElemento', mapping: 'descripcionElemento'},
            {name: 'macElemento'        , mapping: 'macElemento'},
            {name: 'feAsignacion'       , mapping: 'feAsignacion'},
        ]
    });

    var filterPanelTecnico = Ext.create('Ext.panel.Panel', {
        buttonAlign : 'center',
        border      :  false,
        width       :  700,
        layout: {
            type    : 'table',
            align   : 'center',
            columns :  5
        },
        bodyStyle: {background: '#fff'},
        defaults : {bodyStyle: 'padding:15px'},
        items:
        [
            {width:'20%',border:false},
            {
                xtype      : 'displayfield',
                fieldLabel : '<b>Técnico</b>',
                value      :  empleado,
                allowBlank :  true,
                readOnly   :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {
                xtype      : 'textfield',
                id         : 'fltSerie',
                fieldLabel : '<b>Serie</b>',
                allowBlank :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {width:'20%',border:false},
            {
                xtype      : 'textfield',
                id         : 'fltModelo',
                fieldLabel : '<b>Modelo</b>',
                allowBlank :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {
                xtype      : 'textfield',
                id         : 'fltDescripcion',
                fieldLabel : '<b>Descripción</b>',
                allowBlank :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {width:'20%',border:false},
            {
                xtype: 'datefield',
                width: 300,
                id: 'fechaDesde',
                fieldLabel: '<b>Desde:</b>',
                format: 'd-m-Y',
                allowBlank :  true,
                editable: false
            },
            {width:'20%',border:false},
            {
                xtype: 'datefield',
                width: 300,
                id: 'fechaHasta',
                fieldLabel: '<b>Hasta:</b>',
                format: 'd-m-Y',
                allowBlank :  true,
                editable: false
            },
            {width:'20%',border:false}
        ],
        buttons: [
            {
                text   : 'Buscar',
                iconCls: 'icon_search',
                handler: function() {
                    var serie             = Ext.getCmp('fltSerie').getValue();
                    var modelo            = Ext.getCmp('fltModelo').getValue();
                    var descripcion       = Ext.getCmp('fltDescripcion').getValue();
                    var strFechaDesde = Ext.getCmp('fechaDesde').getRawValue()  ?  Ext.getCmp('fechaDesde').getRawValue() : "";
                    var strFechaHasta = Ext.getCmp('fechaHasta').getRawValue()  ?  Ext.getCmp('fechaHasta').getRawValue() : "";

                    storeEquiposAsignados.load({params:{
                        'intIdPersona'    : idEmpleado,
                        'strTipoElemento' : null,
                        'strTiposElementos' : null,
                        'strNumeroSerie'  : serie,
                        'strModelo'       : modelo,
                        'strDescripcion'  : descripcion,
                        'strFechaDesde'   : strFechaDesde,
                        'strFechaHasta'   : strFechaHasta
                    }});
                }
            },
            {
                text   : 'Limpiar',
                iconCls: 'icon_limpiar',
                handler:  function(){
                    Ext.getCmp('fltSerie').setValue('');
                    Ext.getCmp('fltModelo').setValue('');
                    Ext.getCmp('fltDescripcion').setValue('');
                    Ext.getCmp('fechaDesde').setValue('');
                    Ext.getCmp('fechaHasta').setValue('');
                    storeEquiposAsignados.load();
                }
            }
        ]
    });

    var gridDispositivosAsignadosTecnico = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosAsignadosTecnico',
        width    :  740,
        height   :  230,
        store    :  storeEquiposAsignados,
        selModel :  smSeleccion,
        loadMask :  true,
        frame    :  false,
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj) {
                var position = view.getPositionByEvent(eventobj);
                var value    = record.data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : "Copiar texto",
                    msg    : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeEquiposAsignados,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}'
        }),
        columns: [
            {
                header    : 'idControl',
                dataIndex : 'idControl',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : 'idPersonaRol',
                dataIndex : 'idPersonaRol',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : 'noArticulo',
                dataIndex : 'noArticulo',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Serie</b>',
                dataIndex : 'serieElemento',
                width     :  155,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Tipo Elemento</b>',
                dataIndex : 'tipoElemento',
                width     :  110,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento',
                width     :  130,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Descripción</b>',
                dataIndex : 'descripcionElemento',
                width     :  255,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Mac</b>',
                dataIndex : 'macElemento',
                width     :  110,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Fecha Asignacion</b>',
                dataIndex : 'feAsignacion',
                width     :  110,
                sortable  :  false,
                hideable  :  false
            }
        ]
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        frame: false,
        defaults: {
            bodyStyle : 'padding:15px',
            height    :  400
        },
        items:
        [
            {
                xtype  : 'panel',
                title  : 'Filtro Técnico',
                layout : {
                    pack    : 'center',
                    type    : 'table',
                    columns :  1
                },
                items:[filterPanelTecnico,gridDispositivosAsignadosTecnico]
            }
        ]
    });

    var btnCancelarSafeCity = Ext.create('Ext.Button', {
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        handler: function() {
            winAgregarDispositivosNodoSC.close();
            winAgregarDispositivosNodoSC.destroy();
        }
    });

    var btnAgregarSafeCity = Ext.create('Ext.Button', {
        id      : 'idBtnAgregarSafeCity',
        text    :  stylebutton,
        disabled:  true,
        handler: function() {
            if (Ext.getCmp('gridDispositivosNodoSafeCity')){
                var storeDispositivosNodo = Ext.getCmp("gridDispositivosNodoSafeCity").getStore();
            }
            var arraySelection        = Ext.getCmp("gridDispositivosAsignadosTecnico").getSelectionModel().getSelection();
            if (arraySelection.length > 0)
            {
                $.each(arraySelection, function(i, item)
                {
                    var index = null;
                    var serieElemento = item.data.serieElemento;
                    if (Ext.getCmp('gridDispositivosNodoSafeCity')){
                        index = storeDispositivosNodo.findBy(function (record) {
                            return record.data.serieElemento === serieElemento;
                        });
                    }

                    if (accion === 1) {
                        if (index < 0) {
                            storeDispositivosNodo.add({
                                "idControl"           : item.data.idControl,
                                "idPersonaRol"        : item.data.idPersonaRol,
                                "serieElemento"       : serieElemento,
                                "modeloElemento"      : item.data.modeloElemento,
                                "descripcionElemento" : item.data.descripcionElemento,
                                "macElemento"         : item.data.macElemento});
                                Ext.Msg.alert('Mensaje ',"Elemento agregado con éxito");
                        }else{
                            Ext.Msg.alert('Mensaje ',`El elemento con serie ${serieElemento} ya fue agregado`);
                        }
                    }
                });

                winAgregarDispositivosNodoSC.close();
                winAgregarDispositivosNodoSC.destroy();
            }
        }
    });

    var winAgregarDispositivosNodoSC = new Ext.Window ({
        id          : 'winAgregarDispositivosNodoSC',
        title       : 'Filtro de Dispositivos <b style="color:green;">('+titulo+')</b>',
        layout      : 'fit',
        y           :  35,
        buttonAlign : 'center',
        resizable   :  false,
        modal       :  true,
        closable    :  false,
        items       :  [formPanel],
        buttons     :  [btnAgregarSafeCity,btnCancelarSafeCity]
    }).show();
}

function agregarDispositivosOntCliente(data,accion) {

    var stylebutton  = '<label style="color:#3a87ad;"><i class="fa fa-plus-square" aria-hidden="true"></i></label>&nbsp;&nbsp;Agregar';
    var idEmpleado   =  Ext.getCmp('comboFilterTecnico').getValue();
    var empleado     =  Ext.getCmp('comboFilterTecnico').getRawValue();
    var titulo       =  null;

    if (Ext.isEmpty(idEmpleado)) {
        Ext.Msg.show({
            title: 'Alerta',msg: 'Por favor seleccione el Técnico Encargado.!',
            icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
            buttonText: {cancel: 'Cerrar'}
        });
        return;
    }

    if (accion === 2) {
        titulo       = 'Cliente';
    }

    var smSeleccion = new Ext.selection.CheckboxModel({
        mode: 'MULTI',
        allowDeselect: true,
        listeners: {
            select  : function() {
                var grid = Ext.getCmp("gridDispositivosAsignadosTecnico");
                if (grid.getSelectionModel().getSelection().length > 0) {
                    Ext.getCmp("idBtnAgregarClienteSafeCity").setDisabled(false);
                }
            },
            deselect  : function(){
                var grid = Ext.getCmp("gridDispositivosAsignadosTecnico");
                if (grid.getSelectionModel().getSelection().length < 1) {
                    Ext.getCmp("idBtnAgregarClienteSafeCity").setDisabled(true);
                }
            }
        }
    });

    var storeEquiposAsignados = new Ext.data.Store ({
        autoLoad :  true,
        pageSize :  2000,
        total    : 'total',
        proxy    : {
            type   : 'ajax',
            method : 'post',
            url    :  url_equipoAdicionales,
            timeout:  60000,
            reader: {
                type: 'json',
                root: 'result',
                totalProperty: 'total'
            },
            extraParams: {
                'intIdPersona'          : idEmpleado,
                'intPerteneceElemento' : 1
            }
        },
        fields: [
            {name: 'idPersona'          , mapping: 'idPersona'},
            {name: 'idPersonaRol'       , mapping: 'idPersonaRol'},
            {name: 'idControl'          , mapping: 'idControl'},
            {name: 'serieElemento'      , mapping: 'serieElemento'},
            {name: 'modeloElemento'     , mapping: 'modeloElemento'},
            {name: 'tipoElemento'       , mapping: 'tipoElemento'},
            {name: 'descripcionElemento', mapping: 'descripcionElemento'},
            {name: 'macElemento'        , mapping: 'macElemento'},
            {name: 'feAsignacion'       , mapping: 'feAsignacion'},
        ]
    });

    var filterPanelTecnico = Ext.create('Ext.panel.Panel', {
        buttonAlign : 'center',
        border      :  false,
        width       :  700,
        layout: {
            type    : 'table',
            align   : 'center',
            columns :  5
        },
        bodyStyle: {background: '#fff'},
        defaults : {bodyStyle: 'padding:15px'},
        items:
        [
            {width:'20%',border:false},
            {
                xtype      : 'displayfield',
                fieldLabel : '<b>Técnico</b>',
                value      :  empleado,
                allowBlank :  true,
                readOnly   :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {
                xtype      : 'textfield',
                id         : 'fltSerie',
                fieldLabel : '<b>Serie</b>',
                allowBlank :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {width:'20%',border:false},
            {
                xtype      : 'textfield',
                id         : 'fltModelo',
                fieldLabel : '<b>Modelo</b>',
                allowBlank :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {
                xtype      : 'textfield',
                id         : 'fltDescripcion',
                fieldLabel : '<b>Descripción</b>',
                allowBlank :  true,
                width      :  300
            },
            {width:'20%',border:false},
            {width:'20%',border:false},
            {
                xtype: 'datefield',
                width: 300,
                id: 'fechaDesde',
                fieldLabel: '<b>Desde:</b>',
                format: 'd-m-Y',
                allowBlank :  true,
                editable: false
            },
            {width:'20%',border:false},
            {
                xtype: 'datefield',
                width: 300,
                id: 'fechaHasta',
                fieldLabel: '<b>Hasta:</b>',
                format: 'd-m-Y',
                allowBlank :  true,
                editable: false
            },
            {width:'20%',border:false}
        ],
        buttons: [
            {
                text   : 'Buscar',
                iconCls: 'icon_search',
                handler: function() {
                    var serie         = Ext.getCmp('fltSerie').getValue();
                    var modelo        = Ext.getCmp('fltModelo').getValue();
                    var descripcion   = Ext.getCmp('fltDescripcion').getValue();
                    var strFechaDesde = Ext.getCmp('fechaDesde').getRawValue()  ?  Ext.getCmp('fechaDesde').getRawValue() : "";
                    var strFechaHasta = Ext.getCmp('fechaHasta').getRawValue()  ?  Ext.getCmp('fechaHasta').getRawValue() : "";

                    storeEquiposAsignados.load({params:{
                        'intIdPersona'    : idEmpleado,
                        'strTipoElemento' : null,
                        'strTiposElementos' : null,
                        'strNumeroSerie'  : serie,
                        'strModelo'       : modelo,
                        'strDescripcion'  : descripcion,
                        'strFechaDesde'   : strFechaDesde,
                        'strFechaHasta'   : strFechaHasta
                    }});
                }
            },
            {
                text   : 'Limpiar',
                iconCls: 'icon_limpiar',
                handler:  function(){
                    Ext.getCmp('fltSerie').setValue('');
                    Ext.getCmp('fltModelo').setValue('');
                    Ext.getCmp('fltDescripcion').setValue('');
                    Ext.getCmp('fechaDesde').setValue('');
                    Ext.getCmp('fechaHasta').setValue('');
                    storeEquiposAsignados.load();
                }
            }
        ]
    });

    var gridDispositivosAsignadosTecnico = Ext.create('Ext.grid.Panel', {
        id       : 'gridDispositivosAsignadosTecnico',
        width    :  740,
        height   :  230,
        store    :  storeEquiposAsignados,
        selModel :  smSeleccion,
        loadMask :  true,
        frame    :  false,
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj) {
                var position = view.getPositionByEvent(eventobj);
                var value    = record.data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title  : "Copiar texto",
                    msg    : "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons:  Ext.Msg.OK,
                    icon   :  Ext.Msg.INFORMATION
                });
            }
        },
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeEquiposAsignados,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}'
        }),
        columns: [
            {
                header    : 'idControl',
                dataIndex : 'idControl',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : 'idPersonaRol',
                dataIndex : 'idPersonaRol',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : 'noArticulo',
                dataIndex : 'noArticulo',
                hidden    :  true,
                hideable  :  false,
                sortable  :  false
            },
            {
                header    : '<b>Serie</b>',
                dataIndex : 'serieElemento',
                width     :  155,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Tipo Elemento</b>',
                dataIndex : 'tipoElemento',
                width     :  110,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Modelo</b>',
                dataIndex : 'modeloElemento',
                width     :  130,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Descripción</b>',
                dataIndex : 'descripcionElemento',
                width     :  255,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Mac</b>',
                dataIndex : 'macElemento',
                width     :  110,
                sortable  :  false,
                hideable  :  false
            },
            {
                header    : '<b>Fecha Asignacion</b>',
                dataIndex : 'feAsignacion',
                width     :  110,
                sortable  :  false,
                hideable  :  false
            }
        ]
    });

    var formPanel = Ext.create('Ext.form.Panel', {
        frame: false,
        defaults: {
            bodyStyle : 'padding:15px',
            height    :  400
        },
        items:
        [
            {
                xtype  : 'panel',
                title  : 'Filtro Técnico',
                layout : {
                    pack    : 'center',
                    type    : 'table',
                    columns :  1
                },
                items:[filterPanelTecnico,gridDispositivosAsignadosTecnico]
            }
        ]
    });

    var btnCancelarClienteSafeCity = Ext.create('Ext.Button', {
        text: '<label style="color:red;"><i class="fa fa-times" aria-hidden="true"></i></label>'+
              '&nbsp;<b>Cerrar</b>',
        handler: function() {
            winAgregarDispositivosClienteSC.close();
            winAgregarDispositivosClienteSC.destroy();
        }
    });

    var btnAgregarClienteSafeCity = Ext.create('Ext.Button', {
        id      : 'idBtnAgregarClienteSafeCity',
        text    :  stylebutton,
        disabled:  true,
        handler: function() {
            if (Ext.getCmp('gridDispositivosClienteSafeCity')){
                var storeDispositivosCliente = Ext.getCmp("gridDispositivosClienteSafeCity").getStore();
            }
            var arraySelection        = Ext.getCmp("gridDispositivosAsignadosTecnico").getSelectionModel().getSelection();
            if (arraySelection.length > 0)
            {
                $.each(arraySelection, function(i, item)
                {
                    var index = null;
                    var serieElemento = item.data.serieElemento;
                    if (Ext.getCmp('gridDispositivosClienteSafeCity')){
                        index = storeDispositivosCliente.findBy(function (record) {
                            return record.data.serieElemento === serieElemento;
                        });
                    }

                    if (accion === 2) {
                        if (index < 0) {
                            storeDispositivosCliente.add({
                                "idControl"           : item.data.idControl,
                                "idPersonaRol"        : item.data.idPersonaRol,
                                "serieElemento"       : serieElemento,
                                "modeloElemento"      : item.data.modeloElemento,
                                "descripcionElemento" : item.data.descripcionElemento,
                                "macElemento"         : item.data.macElemento});
                                Ext.Msg.alert('Mensaje ',"Elemento agregado con éxito");
                        }else{
                            Ext.Msg.alert('Mensaje ',`El elemento con serie ${serieElemento} ya fue agregado`);
                        }
                    }
                });
                winAgregarDispositivosClienteSC.close();
                winAgregarDispositivosClienteSC.destroy();
            }
        }
    });

    var winAgregarDispositivosClienteSC = new Ext.Window ({
        id          : 'winAgregarDispositivosClienteSC',
        title       : 'Filtro de Dispositivos <b style="color:green;">('+titulo+')</b>',
        layout      : 'fit',
        y           :  35,
        buttonAlign : 'center',
        resizable   :  false,
        modal       :  true,
        closable    :  false,
        items       :  [formPanel],
        buttons     :  [btnAgregarClienteSafeCity,btnCancelarClienteSafeCity]
    }).show();
}
//Fin Bloque de funciones para servicos safe city