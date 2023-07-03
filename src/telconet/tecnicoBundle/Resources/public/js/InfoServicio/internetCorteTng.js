/* Funcion que sirve para mostrar la pantalla de corte 
 * y realizar la llamada ajax para la inactivacion del servicio
 * para la empresa TNG.
 * 
 * @author Jesús Banchen <jbanchen@telconet.ec>
 * @version 1.0 30-04-2019
 * @param data
 * @param idAccion
 */
function cortarServicioTng(data,idAccion){
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


                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textareafield',
                                        name: 'direccion',
                                        fieldLabel: 'Dirección',
                                        displayField: datos[0].direccion,
                                        value: datos[0].direccion,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    { width: '30%', border: false},
                                    { width: '10%', border: false},
                                ]
                            }

                        ]
                    },

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

                                ]
                            }

                        ]
                    }

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
                        
                         Ext.MessageBox.confirm('Confirmacion ',
                            '¿Esta seguro que desea cortar el servicio?', function (btn) {
                                if (btn == 'yes') {
                        
                                Ext.get(gridServicios.getId()).mask('Esperando Respuesta ...');
                                Ext.Ajax.request({
                                    url: cortarClienteBoton,
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
                                        Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                    }
                                }); 
                    
                    } }); }
                    
                    
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
            
        }
    }); 
}
