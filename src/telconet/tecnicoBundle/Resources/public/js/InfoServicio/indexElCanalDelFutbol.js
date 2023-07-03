

function crearFormularioSoporteEcdf(data){
    var usuarioServicio;
    Ext.get(gridServicios.getId()).mask('Abriendo Formulario...');
    Ext.Ajax.request({
        url: getUsuarioServicio,
        method: 'post',
        timeout: 900000,
        params:
            {
                descripcion: 'USUARIO_CANAL_DEL_FUTBOL',
                idServicio:  data.idServicio
            },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            var json = Ext.JSON.decode(response.responseText);
            usuarioServicio = json.encontrados[0]['usuarioServicio'];
            if(usuarioServicio != '')
            {
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
                        title: '<b>FORMULARIO</b>',
                        defaultType: 'textfield',
                        defaults: {
                            width: 450
                        },
                        items: [
                            //Información del Ticket
                            {
                                xtype: 'fieldset',
                                title: '<b>Información del Ticket</b>',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 400
                                },
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
                                                xtype: 'textfield',
                                                id:'ticket',
                                                name: 'ticket',
                                                fieldLabel: 'Ticket/Id Netlife(*)',
                                                allowBlank: false,
                                                blankText:'Este campo es requerido',
                                                displayField: "",
                                                value: "",
                                                width: 250,
                                                enforceMaxLength:true,
                                                maxLength: 20
                                            },
                                            {
                                                xtype: 'textfield',
                                                name: 'nombreCompleto',
                                                id:'nombreCompleto',
                                                fieldLabel: 'Nombre Cliente',
                                                displayField: data.nombreCompleto,
                                                value: data.nombreCompleto,
                                                readOnly: true,
                                                width: 400
                                            },
                                            {
                                                xtype: 'textfield',
                                                regex: /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/, 
                                                regexText: 'Ingresar un correo electrónico valido',
                                                id:'correoElectronico',
                                                allowBlank: false,
                                                blankText:'Este campo es requerido',
                                                name: 'correoElectronico',
                                                fieldLabel: 'Correo Electrónico(*)',
                                                displayField: "",
                                                value: "",
                                                width: 400,
                                                enforceMaxLength:true,
                                                maxLength: 45
                                            }
                                        ]
                                    }
                
                                ]
                            },
                            //Resumen del Problema
                            {
                                xtype: 'fieldset',
                                title: '<b>Resumen del Problema</b>',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 400,
                                    height: "100%"
                                },
                                items: [
                
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 1,
                                            align: 'stretch'
                                        },
                                        items: [
                                                //USUARIO DEL SERVICIO
                                                {
                                                    xtype: 'textfield',
                                                    id:'usuarioECDF',
                                                    name: 'usuarioECDF',
                                                    allowBlank: false,
                                                    fieldLabel: 'Usuario del Servicio',
                                                    displayField: usuarioServicio,
                                                    value: usuarioServicio,
                                                    height: 22,
                                                    blankText:'Este campo es requerido',
                                                    readOnly: true
                                                },
                                                //TIPO
                                                {
                                                    xtype: 'textfield',
                                                    name: 'tipo',
                                                    id:'tipo',
                                                    allowBlank: false,
                                                    blankText:'Este campo es requerido',
                                                    fieldLabel: 'Tipo de Dispositivo(*)',
                                                    displayField: '',
                                                    value: '',
                                                    height: 22,
                                                },
                                                //MARCA
                                                {
                                                    xtype: 'textfield',
                                                    name: 'marca',
                                                    id:'marca',
                                                    allowBlank: false,
                                                    blankText:'Este campo es requerido',
                                                    fieldLabel: 'Marca de Dispositivo(*)',
                                                    displayField: '',
                                                    value: '',
                                                    height: 22,
                                                },
                                                //MODELO
                                                {
                                                    xtype: 'textfield',
                                                    name: 'modelo',
                                                    id:'modelo',
                                                    allowBlank: false,
                                                    blankText:'Este campo es requerido',
                                                    fieldLabel: 'Modelo de Dispositivo(*)',
                                                    displayField: '',
                                                    value: '',
                                                    height: 22,
                                                },
                                                //DESCRIPCION PROBLEMA
                                                {
                                                xtype: 'textareafield',
                                                id:'descripcionProblema',
                                                name: 'descripcionProblema',
                                                fieldLabel: 'Descripción Problema(*)',
                                                allowBlank: false,
                                                blankText:'Este campo es requerido',
                                                value: "",
                                                cols: 55,
                                                rows: 3,
                                                anchor: '100%',
                                                enforceMaxLength:true,
                                                maxLength: 1000
                                                }
                                        ]
                                    }
                
                                ]
                            }
                        ]
                    }],
                    buttons: [{
                        text: 'Crear',
                        formBind: true,
                        handler: function(){
                            
                            var ticket               = Ext.getCmp('ticket').getValue();
                            var nombreCompleto       = Ext.getCmp('nombreCompleto').getValue();
                            var correoElectronico    = Ext.getCmp('correoElectronico').getValue();
                            var usuarioECDF          = Ext.getCmp('usuarioECDF').getValue();
                            var tipo                 = Ext.getCmp('tipo').getValue();
                            var marca                = Ext.getCmp('marca').getValue();
                            var modelo               = Ext.getCmp('modelo').getValue();
                            var descripcionProblema  = Ext.getCmp('descripcionProblema').getValue();
                            var form = this.up('form').getForm();
                            if (form.isValid())
                            {
                                Ext.get(formPanel.getId()).mask('Creando formulario,enviando correo y generando tarea...');
                                Ext.Ajax.request({
                                    method: 'post',
                                    timeout: 600000,
                                    url: crearFormulario,
                                    params: {
                                        ticket              : ticket,
                                        nombreCompleto      : nombreCompleto,
                                        correoElectronico   : correoElectronico,
                                        usuarioECDF         : usuarioECDF,
                                        tipo                : tipo,
                                        marca               : marca,
                                        modelo              : modelo,
                                        descripcionProblema : descripcionProblema,
                                        idServicio          : data.idServicio,
                                        nombreProducto      : data.descripcionProducto
                                    },                            
                                    success: function(respon)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        var respuestaJson = Ext.JSON.decode(respon.responseText);
                                        Ext.Msg.alert("Mensaje", respuestaJson.respuesta, function(btn) {
                                            if (btn == 'ok')
                                            {
                                                store.load();
                                                win.destroy();
                                            }
                                            else
                                            {
                                                Ext.Msg.alert('Error', respuestaJson.respuesta);
                                            }
                                        });
                                    },
                                    failure: function()
                                    {   
                                        Ext.Msg.alert('Error', 'Error al Generar el Formuario, Favor Notificar a Sistemas');
                                        Ext.get(formPanel.getId()).unmask();
                                        win.destroy();
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
                    }]
                });
                
                var win = Ext.create('Ext.window.Window', {
                    title: 'Soporte de 2do. Nivel - '+data.descripcionProducto,
                    modal: true,
                    width: 500,
                    height: 530,
                    closable: true,
                    items: [formPanel]
                }).show();
            }
            else
            {
                Ext.Msg.alert('Error', 'Error al buscar Usuario del Servicio, Favor Notificar a Sistemas');
            }
        },
        failure: function()
        {
            Ext.Msg.alert('Error', 'Error al buscar Usuario del Servicio, Favor Notificar a Sistemas');
            Ext.get(gridServicios.getId()).unmask();
        }
    });
    
}
