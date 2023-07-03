Ext.define('Ext.view.util.MultiuploadParamountNoggin', {
    extend: 'Ext.form.Panel',
    border: 0,
    alias: 'widget.multiuploadParamountNoggin',
    margins: '2 2 2 2',
    fileslist: [],
    frame: false,
    items: [
        {
            name:'archivos[]',
            xtype: 'filefield',
            buttonOnly: true,
            listeners: {
                change: function (view, value, eOpts) {
                    var parent = this.up('form');
                    parent.onFileChange(view, value, eOpts);
                }
            }

        }

    ],
    onFileChange: function (view, value, eOpts) {
        var fileNameIndex = value.lastIndexOf("/") + 1;
        if (fileNameIndex == 0) {
            fileNameIndex = value.lastIndexOf("\\") + 1;
        }
        var filename = value.substr(fileNameIndex);
        
        var ext = filename.split(".").pop().toLowerCase();

        if(($.inArray(ext, ["png"]) === 0) || ($.inArray(ext, ["jpg"]) === 0) || ($.inArray(ext, ["jpeg"]) === 0))
        {
            var IsValid = this.fileValidiation(view, filename);
            if (!IsValid) {
                return;
            }
            this.fileslist.push(filename);
            numArchivosSubidos++;
            var addedFilePanel = Ext.create('Ext.form.Panel', {
                frame: false,
                border: 0,
                padding: 2,
                margin: '0 10 0 0',
                layout: {
                    type: 'hbox',
                    align: 'middle'
                },
                items: [
                    {
                        xtype: 'button',
                        text: null,
                        border: 0,
                        width: 30,
                        margin:0,
                        padding:0,
                        frame: false,
                        iconCls: 'button-grid-delete',
                        tooltip: 'Eliminar',
                        listeners: {
                            click: function (me, e, eOpts) {
                                var currentform = me.up('form');
                                var mainform = currentform.up('form');
                                var lbl = currentform.down('label');
                                mainform.fileslist.pop(lbl.text);
                                mainform.remove(currentform);
                                currentform.destroy();
                                mainform.doLayout();
                                numArchivosSubidos--;
                            }
                        }
                    },
                    {
                        xtype: 'label',
                        padding: 5,
                        listeners: {
                            render: function (me, eOpts) {
                                me.setText(filename);
                            }
                        }
                    },
                    {
                        xtype: 'image',
                        src: '/public/images/attach.png',
                        width: 17

                    }
                ]
            });

            var newUploadControl = Ext.create('Ext.form.FileUploadField', {
                buttonOnly: true,
                name:'archivos[]',
                listeners: {
                    change: function (view, value, eOpts) {
                        var parent = this.up('form');
                        parent.onFileChange(view, value, eOpts);
                    }
                }
            });
            view.hide();
            addedFilePanel.add(view);
            this.insert(0, newUploadControl);
            this.add(addedFilePanel);
        }
        else
        {
            Ext.MessageBox.show({
                title: 'Error',
                msg: 'Archivo con extensión <b>'+ext+'</b> no permitida, intente nuevamente con otro archivo.',
                buttons: Ext.Msg.OK,
                icon: Ext.Msg.ERROR
            });
        }
    },

    fileValidiation: function (me, filename) {
        var isValid = true;
        var indexofPeriod = me.getValue().lastIndexOf("."),
            uploadedExtension = me.getValue().substr(indexofPeriod + 1, me.getValue().length - indexofPeriod);
        
        if (Ext.Array.contains(this.fileslist, filename)) {
            isValid = false;
            me.setActiveError('El archivo ' + filename + ' ya está agregado!');
            Ext.MessageBox.show({
                title: 'Error',
                msg: 'El archivo ' + filename + ' ya está agregado!',
                buttons: Ext.Msg.OK,
                icon: Ext.Msg.ERROR
            });
            /* Se setea a null porque no es válido el archivo a subir, en este caso la única validación que se tiene es que el o los archivos
             * no se encuentren en el listado de los archivos a subir y por ende no se pueden subir los archivos
             */
            me.setRawValue(null);
            me.reset();
        }
        return isValid;
    }
});

function crearFormularioSoporte(data){  
    
    var panelMultiupload = Ext.create('widget.multiuploadParamountNoggin',{ fileslist: [] });
    formPanelArchivos = Ext.create('Ext.form.Panel',
    {
       width: 480,
       frame: true,
       defaults: {
           anchor: '100%',
           allowBlank: false,
           msgTarget: 'side',
           labelWidth: 50
       },
       items: [panelMultiupload]
   });    
    
    
   var storeCategoriaFormulario = new Ext.data.Store({  
                pageSize: 50,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url : getValoresLista,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        descripcion: 'CATEGORIA-FORMULARIO'
                    }
                },
                fields:
                    [
                      {name:'idValor', mapping:'idValor'},
                      {name:'nombreValor', mapping:'nombreValor'}
                    ]
            });
            
    var storeGravedadProblema = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getValoresLista,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                descripcion: 'GRAVEDAD-PROBLEMA'
            }
        },
        fields:
            [
              {name:'idValor', mapping:'idValor'},
              {name:'nombreValor', mapping:'nombreValor'}
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
        title: '<b>FORMULARIO</b>',
        defaultType: 'textfield',
        defaults: {
            width: 520
        },
        items: [
            //Información del Ticket
            {
                xtype: 'fieldset',
                title: '<b>Información del Ticket</b>',
                defaultType: 'textfield',
                defaults: {
                    width: 500
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
                                xtype: 'combo',
                                id:'comboCategoria',
                                name: 'comboCategoria',
                                store: storeCategoriaFormulario,
                                fieldLabel: 'Categoría(*)',
                                allowBlank: false,
                                displayField: 'nombreValor',
                                valueField: 'idValor',
                                queryMode: 'local',
                                width: 250
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
                    width: 500,
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
                                {
                                xtype: 'textareafield',
                                id:'descripcionProblema',
                                name: 'descripcionProblema',
                                fieldLabel: 'Descripción Problema(*)',
                                allowBlank: false,
                                value: "",
                                cols: 55,
                                rows: 3,
                                anchor: '100%',
                                enforceMaxLength:true,
                                maxLength: 1000
                                },
                                {
                                xtype: 'textareafield',
                                id:'medidasSolucion',
                                name: 'medidasSolucion',
                                fieldLabel: 'Medidas para solucionar problema',
                                value: "",
                                cols: 55,
                                rows: 3,
                                anchor: '100%',
                                enforceMaxLength:true,
                                maxLength: 1000
                                },
                                {
                                    xtype: 'textfield',
                                    maskRe: /^[0-9]+(?:\.[0-9][0-9])?$/,
                                    regex: /^[0-9]+(?:\.[0-9][0-9])?$/,
                                    regexText: 'Solo numeros',
                                    id:'clientesAfectados',
                                    allowBlank: false,
                                    name: 'clientesAfectados',
                                    fieldLabel: '# de clientes afectados(*)',
                                    displayField: "",
                                    value: "",
                                    width: 200,
                                    enforceMaxLength:true,
                                    maxLength: 10                                    
                                },
                                {
                                    xtype: 'combo',
                                    id:'gravedadProblema',
                                    allowBlank: false,
                                    name: 'gravedadProblema',
                                    store: storeGravedadProblema,
                                    fieldLabel: 'Gravedad Problema(*)',
                                    displayField: 'nombreValor',
                                    valueField: 'idValor',
                                    queryMode: 'local',
                                    width: 175
                                },
                                {
                                xtype: 'textareafield',
                                id:'sugerenciasProblema',
                                name: 'sugerenciasProblema',
                                fieldLabel: 'Sugerencias sobre la fuente del problema',
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
            },
            //Adjuntar Imagenes
            {
                xtype: 'fieldset',
                title: '<b>Adjuntar Archivos</b><legend>Permitidos: .PNG,.JPG,.JPEG</legend>',
                defaultType: 'textfield',
                defaults: {
                    width: 500,
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
                                formPanelArchivos
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
            
                var nombreCompleto       = Ext.getCmp('nombreCompleto').getValue();
                var descripcionProblema  = Ext.getCmp('descripcionProblema').getValue();
                var medidasSolucion      = Ext.getCmp('medidasSolucion').getValue();
                var clientesAfectados    = Ext.getCmp('clientesAfectados').getValue();
                var sugerenciasProblema  = Ext.getCmp('sugerenciasProblema').getValue();            
                
                var form = this.up('form').getForm();
                if (form.isValid())
                {
                    Ext.get(formPanel.getId()).mask('Creando formulario,enviando correo y generando tarea...');
                    form.submit({
                        url: crearFormularioL1ParamountNoggin,
                        params: {
                            comboCategoria      : Ext.getCmp('comboCategoria').getRawValue(),
                            nombreCompleto      : nombreCompleto,
                            descripcionProblema : descripcionProblema,
                            medidasSolucion     : medidasSolucion,
                            clientesAfectados   : clientesAfectados,
                            gravedadProblema    : Ext.getCmp('gravedadProblema').getRawValue(),
                            sugerenciasProblema : sugerenciasProblema,
                            idServicio          : data.idServicio,
                            nombreProducto      : data.descripcionProducto
                        },                            
                        success: function(fp, o)
                        {
                            Ext.get(formPanel.getId()).unmask();
                            Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn) {
                                if (btn == 'ok')
                                {
                                    store.load();
                                    win.destroy();
                                }
                            });
                        },
                        failure: function(fp, o)
                        {
                            Ext.get(formPanel.getId()).unmask();
                            Ext.Msg.alert("Error", o.result.respuesta);
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
        title: 'Soporte de 1er. Nivel - '+data.descripcionProducto,
        autoScroll: true,
        width: 580,
        height: 600,
        closable: true,
        items: [formPanel]
    }).show();
            
}

function crearFormularioSoporteGtv(data)
{
    var storeFormaContrato = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getValoresProductos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'valores'
            },
            extraParams: {
                descripcion: 'FORMA-CONTRATO',
                producto: data.descripcionProducto
            }
        },
        fields:
            [
              {name:'idValor', mapping:'idValor'},
              {name:'nombreValor', mapping:'nombreValor'}
            ]
    });

    var storeRecurrente = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getValoresProductos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'valores'
            },
            extraParams: {
                descripcion: 'VALOR-RECURRENTE',
                producto: data.descripcionProducto
            }
        },
        fields:
            [
              {name:'idValor', mapping:'idValor'},
              {name:'nombreValor', mapping:'nombreValor'}
            ]
    });

    var storePlanContratado = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getValoresProductos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'valores'
            },
            extraParams: {
                descripcion: 'PLAN-CONTRATO',
                producto: data.descripcionProducto
            }
        },
        fields:
            [
              {name:'idValor', mapping:'idValor'},
              {name:'nombreValor', mapping:'nombreValor'}
            ]
    });

    var storeDispositivo = new Ext.data.Store({  
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getValoresProductos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'valores'
            },
            extraParams: {
                descripcion: 'DISPOSITIVOS',
                producto: data.descripcionProducto
            }
        },
        fields:
            [
              {name:'idValor', mapping:'idValor'},
              {name:'nombreValor', mapping:'nombreValor'}
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
        //Panel principal del formulario
        xtype: 'fieldset',
        title: '<b>FORMULARIO</b>',
        defaultType: 'textfield',
        defaults: {
            width: 480,
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
                    {
                        xtype: 'textfield',
                        id:'numeroTicket',
                        name: 'numeroTicket',
                        fieldLabel: 'Numero de ticket o identificador de Netlife',
                        allowBlank: false,
                        value: "",
                        cols: 55,
                        rows: 3,
                        width: 450,
                        anchor: '100%',
                        enforceMaxLength:true,
                        maxLength: 1000
                    },
                    {
                        xtype: 'combo',
                        id:'contratoServicio',
                        name: 'contratoServicio',
                        allowBlank: false,
                        store: storeFormaContrato,
                        fieldLabel: 'Contratación del servicio',
                        displayField: 'nombreValor',
                        valueField: 'idValor',
                        queryMode: 'local',
                        width: 250
                    },
                    {
                        xtype: 'textfield',
                        id:'nombreCliente',
                        name: 'nombreCliente',
                        fieldLabel: 'Nombre completo del cliente',
                        value: "",
                        cols: 55,
                        rows: 3,
                        width: 450,
                        anchor: '100%',
                        enforceMaxLength:true,
                        maxLength: 1000
                    },
                    {
                        xtype: 'textfield',
                        id:'correo',
                        name: 'correo',
                        fieldLabel: 'Correo',
                        value: "",
                        cols: 55,
                        rows: 3,
                        width: 450,
                        anchor: '100%',
                        enforceMaxLength:true,
                        maxLength: 1000
                    },
                    {
                        xtype: 'textfield',
                        maskRe: /^[0-9]+(?:\.[0-9][0-9])?$/,
                        regex: /^[0-9]+(?:\.[0-9][0-9])?$/,
                        regexText: 'Solo numeros',
                        id:'celular',
                        allowBlank: false,
                        name: 'celular',
                        fieldLabel: 'Celular',
                        displayField: "",
                        value: "",
                        width: 200,
                        enforceMaxLength:true,
                        maxLength: 10                                    
                    },
                    {
                        xtype: 'combo',
                        id:'planContratado',
                        name: 'planContratado',
                        allowBlank: false,
                        store: storePlanContratado,
                        fieldLabel: 'Plan contratado',
                        displayField: 'nombreValor',
                        valueField: 'idValor',
                        queryMode: 'local',
                        width: 250
                    },
                    {
                        xtype: 'combo',
                        id:'recurrente',
                        name: 'recurrente',
                        allowBlank: false,
                        store: storeRecurrente,
                        fieldLabel: 'Recurrente o no recurrente',
                        displayField: 'nombreValor',
                        valueField: 'idValor',
                        queryMode: 'local',
                        width: 150
                    },
                    {
                        xtype: 'textfield',
                        id:'pais',
                        name: 'pais',
                        fieldLabel: 'País',
                        value: "",
                        cols: 55,
                        rows: 3,
                        width: 300,
                        anchor: '100%',
                        enforceMaxLength:true,
                        maxLength: 1000
                    },
                    {
                        xtype: 'textareafield',
                        id:'contenido',
                        name: 'contenido',
                        fieldLabel: 'Contenido',
                        value: "",
                        cols: 55,
                        rows: 3,
                        width: 450,
                        anchor: '100%',
                        enforceMaxLength:true,
                        maxLength: 1000
                    },
                    {
                        xtype: 'combo',
                        id:'dispositivo',
                        name: 'dispositivo',
                        allowBlank: false,
                        store: storeDispositivo,
                        fieldLabel: 'Dispositivo',
                        displayField: 'nombreValor',
                        valueField: 'idValor',
                        queryMode: 'local',
                        width: 300
                    },
                    {
                        xtype: 'textareafield',
                        id:'resumen',
                        name: 'resumen',
                        fieldLabel: 'Resumen del problema',
                        value: "",
                        cols: 55,
                        rows: 3,
                        width: 450,
                        anchor: '100%',
                        enforceMaxLength:true,
                        maxLength: 1000
                    }                            
                ]
            }
        ]
    }],
    buttons: [{
        text: 'Crear',
        formBind: true,
        handler: function()
            {
                var numeroTicket     = Ext.getCmp('numeroTicket').getValue();
                var nombreCliente    = Ext.getCmp('nombreCliente').getValue();
                var correo           = Ext.getCmp('correo').getValue();
                var celular          = Ext.getCmp('celular').getValue();
                var pais             = Ext.getCmp('pais').getValue();
                var contenido        = Ext.getCmp('contenido').getValue();
                var resumen          = Ext.getCmp('resumen').getValue();

                var form = this.up('form').getForm();
                if (form.isValid())
                {
                    Ext.get(formPanel.getId()).mask('Creando formulario, enviando correo y generando tarea...');
                    form.submit({
                        url: crearFormularioL2GolTv,
                        params: {
                            numeroTicket     : numeroTicket,
                            contratoServicio : Ext.getCmp('contratoServicio').getRawValue(),
                            nombreCliente    : nombreCliente,
                            correo           : correo,
                            celular          : celular,
                            planContratado   : Ext.getCmp('planContratado').getRawValue(),
                            recurrente       : Ext.getCmp('recurrente').getRawValue(),
                            pais             : pais,
                            contenido        : contenido,
                            dispositivo      : Ext.getCmp('dispositivo').getRawValue(),
                            resumen          : resumen,
                            idServicio       : data.idServicio,
                            nombreProducto   : data.descripcionProducto
                        },
                        success: function(fp, o)
                        {
                            Ext.get(formPanel.getId()).unmask();
                            Ext.Msg.alert("Mensaje", o.result.respuesta, function(btn) {
                                if (btn == 'ok')
                                {
                                    store.load();
                                    win.destroy();
                                }
                            });
                        },
                        failure: function(fp, o)
                        {
                            Ext.get(formPanel.getId()).unmask();
                            Ext.Msg.alert("Error", o.result.respuesta);
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
        autoScroll: true,
        width: 500,
        height: 590,
        closable: true,
        items: [formPanel]
    }).show();       
}

function validarEmail(email)
{
    expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!expr.test(email))
    {
        Ext.Msg.alert("Error","La dirección de correo " + email + " es incorrecta.");
        return false;
    }
    else 
    {
        return true;
    }
}