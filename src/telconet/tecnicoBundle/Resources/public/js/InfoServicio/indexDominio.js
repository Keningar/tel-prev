function confirmarServicioDominio(data, idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Confirmar el Servicio de Dominio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Confirmando el Servicio...');
            Ext.Ajax.request({
                url: confirmarServicioDominioBoton,
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

function verInformacionDominio(data){
    storeDominios = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getDominios,
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
    
    //grid de usuarios
    gridDominios = Ext.create('Ext.grid.Panel', {
        id:'gridDominios',
        store: storeDominios,
        columnLines: true,
        columns: [{
            //id: 'nombreDetalle',
            header: 'Dominio',
            dataIndex: 'valor',
            width: 250,
            sortable: true
        },
        {
            header: 'Estado',
            dataIndex: 'estado',
            width: 100
        },
        {
            xtype: 'actioncolumn',
            header: 'Accion',
            width: 50,
            items: [
                {
                    getClass: function(v, meta, rec) {
                        var permiso = $("#ROLE_151-852");
                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);          
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
                    tooltip: 'Eliminar Dominio',
                    handler: function(grid, rowIndex, colIndex) {
                        if(grid.getStore().getAt(rowIndex).data.estado=="Activo"){
                            eliminarDominio(grid.getStore().getAt(rowIndex).data);
                        }
                        
                    }
                }
            ]
        }],
        viewConfig:{
            stripeRows:true
        },

        frame: true,
        height: 150
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
                width: 470
            },
            items: [

                gridDominios

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
                store.load();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Dominios',
        modal: true,
        width: 520,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function agregarDominio(data){
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
                    id:'dominio',
                    name: 'dominio',
                    fieldLabel: 'Dominio',
                    displayField: "",
                    allowBlank: false,
                    blankText: "Campo no puede ser vacio",
                    regex: /^((?:(?:(?:\w[\.\-\+]?)*)\w)+)((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$/,
                    regexText: "Dominio ingresado no tiene un correcto formato.", 
                    value: "",
                    width: '30%'
                }

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
                text: 'Guardar',
                formBind: true,
                handler: function(){
                    var dominio = Ext.getCmp('dominio').getValue();
                    
                    var str = 0;
                    if(dominio=="" || dominio==" "){
                        str=-1;
                    }
                    
                    if(str!=-1){
                        Ext.Msg.alert('Mensaje','Esta seguro que desea Agregar el Dominio al Servicio?', function(btn){
                            if(btn=='ok'){
                                Ext.get(formPanel.getId()).mask('Agregando el Dominio...');
                                Ext.Ajax.request({
                                    url: agregarDominioServicio,
                                    method: 'post',
                                    timeout: 400000,
                                    params: { 
                                        idServicio: data.idServicio,
                                        dominio: dominio
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText == "OK"){
                                            Ext.Msg.alert('Mensaje','Se Agrego el dominio!', function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else{
                                            Ext.Msg.alert('Mensaje ','No se pudo agregar el dominio al Servicio!' );
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
                    else{
                        alert("Favor revisar los campos!");
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
        title: 'Agregar Dominio',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function eliminarDominio(data){
    Ext.Msg.alert('Mensaje','Esta seguro que desea eliminar el Dominio?', function(btn){
        if(btn=='ok'){
            Ext.get(gridDominios.getId()).mask('Eliminando Dominio...');
            Ext.Ajax.request({
                url: eliminarDominioBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    spc: data.id
                },
                success: function(response){
                    Ext.get(gridDominios.getId()).unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se elimino el Dominio', function(btn){
                            if(btn=='ok'){
                                
                                storeDominios.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo eliminar el dominio!' );
                    }

                }

            });
        }
    });
    
}

function cancelarServicioDominio(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cancelar el Servicio de Dominio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cancelando el Servicio...');
            Ext.Ajax.request({
                url: cancelarServicioDominioBoton,
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
                        Ext.Msg.alert('Mensaje ','No se pudo cancelar el servicio!' );
                    }

                }

            });
        }
    });
}

function cortarServicioDominio(data, idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cortar el Servicio de Dominio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cortando el Servicio...');
            Ext.Ajax.request({
                url: cortarServicioDominioBoton,
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

function reconectarServicioDominio(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Reconectar el Servicio de Dominio?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Reconectando el Servicio...');
            Ext.Ajax.request({
                url: reconectarServicioDominioBoton,
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