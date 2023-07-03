function confirmarServicioCorreo(data, idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Confirmar el Servicio de Correo', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Confirmando el Servicio...');
            Ext.Ajax.request({
                url: confirmarServicioCorreoBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se confirmó el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo confirmar el servicio!' );
                    }

                }

            });
        }
    });
}

function verInformacionCorreo(data){
    storeCorreos = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getCorreos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio,
                producto: data.descripcionProducto
            }
        },
        fields:
            [
              {name:'valor', mapping:'valor'},
              {name:'estado', mapping:'estado'},
              {name:'id', mapping:'id'}
            ]
    });
    
    Ext.define('Correos', {
        extend: 'Ext.data.Model',
        fields: [
              {name:'valor', mapping:'valor'},
              {name:'estado', mapping:'estado'},
              {name:'id', mapping:'id'}
        ]
    });
    
    //grid de usuarios
    gridCorreos = Ext.create('Ext.grid.Panel', {
        id:'gridCorreos',
        store: storeCorreos,
        columnLines: true,
        columns: [{
            //id: 'nombreDetalle',
            header: 'Correo',
            dataIndex: 'valor',
            width: 350,
            sortable: true
        },
        {
            header: 'Estado',
            dataIndex: 'estado',
            width: 60
        },
        {
            xtype: 'actioncolumn',
            header: 'Accion',
            width: 100,
            items: [
                {
                    getClass: function(v, meta, rec) {
                        var permiso = $("#ROLE_151-855");
                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                        //alert(typeof permiso);
                        if(!boolPermiso){ 
                            return 'button-grid-invisible';
                        }
                        else{
                            if(rec.get('estado') == "Activo"){
                                return 'button-grid-show';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }

                    },
                    tooltip: 'Ver Contrasenia',
                    handler: function(grid, rowIndex, colIndex) {
                        if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                            verContraseniaDeCorreo(grid.getStore().getAt(rowIndex).data);
                        }
                        
                    }
                },
                {
                    getClass: function(v, meta, rec) {
                        var permiso = $("#ROLE_151-856");
                        boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                        //alert(typeof permiso);
                        
                        if(!boolPermiso){ 
                            return 'button-grid-invisible';
                        }
                        else{
                            if(rec.get('estado') == "Activo"){
                                return 'button-grid-delete';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }

                    },
                    tooltip: 'Eliminar Correo',
                    handler: function(grid, rowIndex, colIndex) {
                        if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                            eliminarCorreo(grid.getStore().getAt(rowIndex).data);
                        }
                        
                    }
                },
                {
                    getClass: function(v, meta, rec) {
                        var permiso = $("#ROLE_151-1377");
                        
                        //console.log(permiso.val());
                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
                        //alert(typeof permiso);
                        if(!boolPermiso){ 
                            return 'button-grid-invisible';
                        }
                        else{
                            if(rec.get('estado') == "Activo"){
                                return 'button-grid-cambioClave';
                            }
                            else{
                                return 'button-grid-invisible';
                            }
                        }

                    },
                    tooltip: 'Cambiar Clave',
                    handler: function(grid, rowIndex, colIndex) {
                        if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                            cambiarClave(grid.getStore().getAt(rowIndex).data);
                        }
                        
                    }
                }
            ]
        }
        ],
        viewConfig:{
            stripeRows:true
        },

        frame: true,
        height: 200
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
                width: 530
            },
            items: [

                gridCorreos

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
                store.reload();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Correos',
        modal: true,
        width: 580,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function eliminarCorreo(data){
    Ext.Msg.alert('Mensaje','Esta seguro que desea eliminar el Correo?', function(btn){
        if(btn=='ok'){
            Ext.get(gridCorreos.getId()).mask('Eliminando Correo...');
            Ext.Ajax.request({
                url: eliminarCorreoBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    spc: data.id
                },
                success: function(response){
                    Ext.get(gridCorreos.getId()).unmask();

                    //jvera 06/05/2014

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se elimino el Correo', function(btn){
                            if(btn=='ok'){
                                
                                storeCorreos.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{

                        Ext.Msg.alert('Mensaje',response.responseText); 

                    }

                }

            });
        }
    });
    
}

function cambiarClave(data)
{
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
                width: 200
            },
            items: [
                {
                    xtype: 'textfield',
                    id:'clave',
                    name: 'clave',
                    fieldLabel: 'Nueva Clave ',
                    displayField: "",
                    value: "",
                    allowBlank: false,
                    blankText:  "Clave no puede ser vacia",
                    width: '30%'
                }
            ]
        }
        ],
        buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function(){
                var clave = Ext.getCmp('clave').getValue();
                //validacion de clave 
                function validarCodigo(codigo)
                { 
                        var regularExpression = /^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/;
                        return (regularExpression.test(codigo)); 
                } 

        if (!validarCodigo(clave)) {
            Ext.Msg.alert('Mensaje','Su clave debe contener minimo 8 caracteres alfanumericos,\n\
                                     una letra mayuscula, una minuscula y un caracter especial', function(btn){
                if(btn=='ok'){
                   storeCorreos.load();
               
                }
            });              
        }           
        else{
            
            Ext.Msg.alert('Mensaje','Esta seguro que desea cambiar la clave?', function(btn){
            if(btn=='ok'){
                //console.log(storeCorreos);
                Ext.get(formPanel.getId()).mask('Cambiando clave...');
                Ext.Ajax.request({
                    url: cambiarClaveCorreoBoton,
                    method: 'post',
                    timeout: 400000,
                    params: { 
                        spc: data.id,
                        clave: clave
                    },
                    success: function(response){
                        Ext.get(formPanel.getId()).unmask();
                        
                        if(response.responseText == "OK"){
                            Ext.Msg.alert('Mensaje','Se cambio la clave correctamente.', function(btn){
                                if(btn=='ok'){
                                    storeCorreos.load();
                                    win.destroy();
                                }
                            });
                        }
                        else{

                            Ext.Msg.alert('Error', response.responseText);

                        }

                    }

                });
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
        title: 'Cambiar clave de correo.',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function verContraseniaDeCorreo(data){
console.log(data);    
Ext.get(gridCorreos.getId()).mask('Consultando Clave...');
    
    Ext.Ajax.request({
        url: getContraseniaDelCorreo,
        method: 'post',
        timeout: 400000,
        params: { 
            spc: data.id
        },
        success: function(response){
            Ext.get(gridCorreos.getId()).unmask();
            
            
            var datos = response.responseText;
            
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
                        width: 200
                    },
                    items: [

                        {
                            xtype: 'textfield',
                            fieldLabel: 'Clave',
                            displayField: datos,
                            value: datos,
                            readOnly: true,
                            width: '30%'
                        }

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
                title: 'Clave del Correo',
                modal: true,
                width: 300,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
        }
        
    });

    
    
    
}

function agregarCorreo(data){
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
                width: 200
            },
            items: [
                {
                    xtype: 'textfield',
                    id:'usuario',
                    name: 'usuario',
                    fieldLabel: 'Usuario',
                    displayField: "",
                    value: "",
                    width: '30%',
                    allowBlank: false,
                    blankText: 'Usuario no puede ser vacio'
                },
                {
                    xtype: 'textfield',
                    id: 'clave',
                    name: 'clave',
                    fieldLabel: 'Clave',
                    displayField: "",
                    value: "",
                    width: '30%',
                    allowBlank: false,
                    blankText: 'Clave no puede ser vacia'
                }

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function(){
                    var usuario = Ext.getCmp('usuario').getValue();
                    var clave = Ext.getCmp('clave').getValue();
                    
                    //validacion de clave 
                    function validarClave(codigo)
                    { 
                        var regularExpression = /^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/;
                        return (regularExpression.test(codigo)); 
                    } 

                    if (!validarClave(clave)) {
                        Ext.Msg.alert('Mensaje','Su clave debe contener minimo 8 caracteres alfanumericos,\n\
                                                una letra mayuscula, una minuscula y un caracter especial', function(btn){
                            if(btn=='ok'){
                                store.load();
                            }
                        });              
                    }                       
                    

                    else{
                        Ext.Msg.alert('Mensaje','Esta seguro que desea Agregar el Correo al Servicio?', function(btn){
                            if(btn=='ok'){
                                Ext.get(formPanel.getId()).mask('Agregando el Correo...');
                                Ext.Ajax.request({
                                    url: agregarCorreoServicio,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio: data.idServicio,
                                        usuario: usuario,
                                        clave: clave
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Agrego el correo!', function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else{

                                            Ext.Msg.alert('Mensaje ', response.responseText); //jvera 06/05/2014 fin
                                        }
                                      
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
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
        title: 'Agregar Correo',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

/*Agregar Correos para TN*/
function agregarCorreoTn(data){
    storeDominios = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getDominiosCorreos,
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
              {name:'valor', mapping:'valor'},
              {name:'estado', mapping:'estado'}
            ]
    });
    
    Ext.define('Dominios', {
        extend: 'Ext.data.Model',
        fields: [
              {name:'valor', mapping:'valor'},
              {name:'estado', mapping:'estado'}
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
        items: [

        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',
    //                checkboxToggle: true,
    //                collapsed: true,
            defaults: {
                width: 200
            },
            items: [
                {
                    xtype: 'textfield',
                    id:'usuario',
                    name: 'usuario',
                    fieldLabel: 'Usuario',
                    displayField: "",
                    value: "",
                    width: '30%',
                    allowBlank: false,
                    blankText: 'Usuario no puede ser vacio'
                },
                {
                    xtype: 'textfield',
                    id: 'clave',
                    name: 'clave',
                    fieldLabel: 'Clave',
                    displayField: "",
                    value: "",
                    width: '30%',
                    allowBlank: false,
                    blankText: 'Clave no puede ser vacia'
                },
                {
                            xtype: 'combobox',
                            fieldLabel: 'Dominios',
                            id: 'dominio',
                            store: storeDominios,
                            displayField: 'valor',
                            valueField: 'id',
                            loadingText: 'Buscando ...',
                            listClass: 'x-combo-list-small',
                            queryMode: 'local',
                            width: '30%',
                            selectOnFocus:true,
                            allowBlank: false,
                            blankText: 'Debe seleccionar un dominio',
                            forceSelection : true
                        },

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function(){
                    var usuario = Ext.getCmp('usuario').getValue();
                    var clave = Ext.getCmp('clave').getValue();
                    var dominio = Ext.getCmp('dominio').getValue();
                    
                    //validacion de clave 
                    function validarClave(codigo)
                    { 
                        var regularExpression = /^(?=.*[0-9])(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/;
                        return (regularExpression.test(codigo)); 
                    } 

                    if (!validarClave(clave)) {
                        Ext.Msg.alert('Mensaje','Su clave debe contener minimo 8 caracteres alfanumericos,\n\
                                                una letra mayuscula, una minuscula y un caracter especial', function(btn){
                            if(btn=='ok'){
                                store.load();
                            }
                        });              
                    }
                    
                    
                    

                    else{
                        Ext.Msg.alert('Mensaje','Esta seguro que desea Agregar el Correo al Servicio?', function(btn){
                            if(btn=='ok'){
                                Ext.get(formPanel.getId()).mask('Agregando el Correo...');
                                Ext.Ajax.request({
                                    url: agregarCorreoServicio,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio: data.idServicio,
                                        usuario: usuario,
                                        clave: clave,
                                        dominio:dominio
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Agrego el correo!', function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else{

                                            Ext.Msg.alert('Mensaje ', response.responseText); //jvera 06/05/2014 fin
                                        }
                                      
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
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
        title: 'Agregar Correo',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}
/*Fin Agragar Correos para TN*/

function cancelarServicioCorreo(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cancelar el Servicio de Correo?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cancelando el Servicio...');
            Ext.Ajax.request({
                url: cancelarServicioCorreoBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Cancelo el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ', response.responseText );
                    }

                }

            });
        }
    });
}

function cortarServicioCorreo(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cortar el Servicio de Correo?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cortando el Servicio...');
            Ext.Ajax.request({
                url: cortarServicioCorreoBoton,
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
                        Ext.Msg.alert('Mensaje ','No se pudo Cortar el servicio!' );
                    }

                }

            });
        }
    });
}

function reconectarServicioCorreo(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Reconectar el Servicio de Correo?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Reconectando el Servicio...');
            Ext.Ajax.request({
                url: reconectarServicioCorreoBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Reconectar el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo reconectar el servicio!' );
                    }

                }

            });
        }
    });
}
