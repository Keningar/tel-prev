
var ciudad       = '';
var idServicio   = 0;
var arrayChecked = [];

function showFactibilidadPac(data)
{    
    winIngresoFactibilidad = "";

    if(!winIngresoFactibilidad)
    {                           
        var arrayItems = [];
        //Cargar el listado de valores a ser checkeados para confirmar Factibilidad
        $.each(Ext.JSON.decode(arrayChecklist), function(i,item)
        {            
            var json = {};
            json['boxLabel']   = item['valor'];
            json['name']       = 'rb_checklist';
            json['inputValue'] = item['valor'];
            
            arrayItems.push(json);
        });
        
        var arrayModelosPdu = [];
        
        $.each(Ext.JSON.decode(arrayModelosPdus), function(i,item)
        {            
            var json = {};
            json['id']   = item.valor;
            json['valor']   = item.valor;
            
            arrayModelosPdu.push(json);
        });
        
        var storeModelosPdu = new Ext.data.Store({
            fields: ['id','valor'],
            data: arrayModelosPdu
        });                
        
        var storeInformacionEspacio = new Ext.data.Store({
            pageSize: 50,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlGetInformacionEspacioHousing,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    idServicio         : '',
                    idServicioAlquiler : data.get("id_servicio"),
                    subgrupo           : 'HOUSING'
                }
            },
            fields:
                [
                    {name: 'nombreFila', mapping: 'nombreFila'},
                    {name: 'nombreRack', mapping: 'nombreRack'},
                    {name: 'reservados', mapping: 'reservados'},
                    {name: 'tipoEspacio',mapping: 'tipoEspacio'}
                ]
        });
        
        var gridInformacionEspacio  = Ext.create('Ext.grid.Panel',
        {                    
            id: 'gridInformacionEspacio',
            store: storeInformacionEspacio,
            columnLines: true,                    
            columns:
                [
                    {
                        header: '<b>Número de Fila</b>',
                        dataIndex: 'nombreFila',
                        width: 100,
                        sortable: true
                    }, 
                    {
                        header: '<b>Nombre de Rack</b>',
                        dataIndex: 'nombreRack',
                        width: 120
                    },
                    {
                        header: '<i class="fa fa-hashtag" aria-hidden="true"></i>&nbsp;<b>Cantidad (US) Contratadas</b>',
                        dataIndex: 'reservados',
                        width: 170
                    },
                    {
                        header: '<b>Tipo Espacio Contratado</b>',
                        dataIndex: 'tipoEspacio',
                        width: 150
                    }
                ],
            viewConfig:
                {
                    stripeRows: true,
                    enableTextSelection: true,
                    loadingText: "Cargando Información de HOUSING..."
                },
            frame: true,
            height: "auto"
        });
        
        var formPanelIngresoFactibilidad = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding: 5px; border: 0px none;",
            frame: true,
            items: [
                //Resumen del cliente
                {
                    xtype: 'fieldset',
                    title: '<b>Resumen</b>',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 5,
                        pack: 'center'
                    },
                    items: [
                        //Datos del Cliente
                        { width: '10%', border: false},
                        {
                            xtype: 'fieldset',
                            title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos del Cliente</b>',
                            style: "font-weight:bold; margin-bottom: 5px;",
                            layout: {
                                type: 'table',
                                columns: 5,
                                align: 'stretch'
                            },                          
                            items: [
                                { width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: data.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                { width: '10%', border: false, html:'&nbsp;'},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: data.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                { width: '10%', border: false},
                                //--------------------------------------
                                { width: '10%', border: false},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: data.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true,
                                    width:300
                                },
                                { width: '10%', border: false, html:'&nbsp;'},
                                {
                                    xtype: 'textfield',
                                    fieldLabel: '<b>Servicio</b>',
                                    name: 'info_servicio',
                                    id: 'info_servicio',
                                    value: data.get("producto"),
                                    fieldStyle:'font-weight:bold;color:green;',
                                    allowBlank: false,
                                    readOnly: true,
                                    width:400
                                },
                                { width: '10%', border: false}
                            ]
                        },
                        { width: '10%', border: false},
                        { width: '10%', border: false},
                        { width: '10%', border: false},
                        
                        //Datos de Factibilidad BOC generada
                        
                        { width: '10%', border: false},
                        {
                            xtype: 'fieldset',
                            title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos de Factibilidad Housing</b>',
                            defaultType: 'textfield',
                            layout: 
                            {
                                type: 'table',
                                columns: 2,
                                pack: 'center'
                            },
                            items: [
                                {
                                    xtype: 'fieldset',
                                    style: "border:0;align:center;margin-left: 10%;",
                                    items: [                
                                        gridInformacionEspacio
                                    ]
                                }
                            ]
                        },
                        { width: '10%', border: false},
                        { width: '10%', border: false},
                        { width: '10%', border: false},
                        //-------------------------------------------------------
                        { width: '10%', border: false},
                        {
                            xtype: 'fieldset',
                            title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Checklist de factibilidad</b>',
                            defaultType: 'textfield',
                            layout: 
                            {
                                type: 'table',
                                columns: 1,
                                pack: 'center'
                            },
                            items: [
                                {
                                    xtype: 'checkboxgroup',
                                    id:'checklist_rb',
                                    name:'cbgroup',
                                    fieldLabel: '<i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;<b>CheckList</b>',
                                    columns: 1,
                                    vertical: true,
                                    items: arrayItems
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'cmbModelosPdu',
                                    store: storeModelosPdu,
                                    fieldLabel: '<i class="fa fa-plug" aria-hidden="true"></i>&nbsp;<b>PDU</b>',
                                    displayField: 'valor',
                                    valueField: 'id',
                                    emptyText: 'Seleccione PDU'
                                }
                            ]
                        },
                        { width: '10%', border: false},
                        { width: '10%', border: false},
                        { width: '10%', border: false} 
                    ]
                }
            ],                
            buttons: [
                {
                    text: '<i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;<b>Guardar</b>',
                    handler: function() 
                    {
                        var arraySuccessValues = Ext.getCmp('checklist_rb').getChecked(); 
                        var modeloPdu          = Ext.getCmp('cmbModelosPdu').getValue();
                        var boolContinua       = true;
                        
                        if(arraySuccessValues.length === 0)
                        {
                            Ext.Msg.alert('Alerta', 'Debe seleccionar valores dentro del checklist de verificación para continuar.');
                            boolContinua = false;
                            return false;
                        }
                        else
                        {
                            var contadorDefault = 0;
                            //Verificar que al menos se encuentre seleccionados los tipos que se encuentren como default
                            $.each(arraySuccessValues, function(i,item)
                            {
                                $.each(arrayChecked, function(i,item1)
                                {
                                    if(item1 === item.inputValue)
                                    {
                                        contadorDefault++;
                                    }
                                });                                
                            });
                            
                            if(contadorDefault !== arrayChecked.length)
                            {
                                Ext.Msg.alert('Alerta', 'Debe seleccionar el menos los valores establecidos como default en el CheckList');
                                boolContinua = false;
                                return false;
                            }
                        }
                        
                        if(boolContinua)                        
                        {
                            var arrayRb = [];
                            
                            $.each(arraySuccessValues, function(i, item)
                            {
                                var json = {};
                                json['valor'] = item.boxLabel;
                                arrayRb.push(json);
                            });
                            
                            //pdu
                            var json = {};
                            json['valor'] = 'Modelo PDU: '+modeloPdu;
                            arrayRb.push(json);
                            
                            $.ajax({
                                type: "POST",
                                url: urlGuardarFactibilidad,
                                data:
                                    {
                                        'idServicio' : data.get('id_servicio'),
                                        'idSolicitud': data.get('id_factibilidad'),
                                        'data'       : Ext.JSON.encode(arrayRb)
                                    },
                                beforeSend: function()
                                {
                                    Ext.get(winIngresoFactibilidad.getId()).mask('Confirmando datos de Factibilidad eléctrica...');
                                },
                                complete: function()
                                {
                                    Ext.get(winIngresoFactibilidad.getId()).unmask();
                                },
                                success: function(data)
                                {
                                    if (data.status === "OK")
                                    {
                                        Ext.Msg.alert('Mensaje', data.mensaje, function(btn) {
                                            if (btn == 'ok') 
                                            {
                                                winIngresoFactibilidad.close();
                                                winIngresoFactibilidad.destroy();
                                                store.load();
                                            }
                                        });                                    
                                    }
                                    else 
                                    {
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: data.mensaje,
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                }
                            });  
                        }
                    }
                },
                {
                    text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;<b>Cerrar</b>',
                    handler: function() 
                    {
                        winIngresoFactibilidad.close();
                    }
                }
            ]
        });

        var winIngresoFactibilidad = Ext.widget('window', {
            title: 'Ingreso de Factibilidad Eléctrica PAC',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelIngresoFactibilidad]
        });
    }
    
    //Set valor default en el checklist
    
    $.each(Ext.JSON.decode(arrayChecklist), function(i,item)
    {
        if(item['default'] === 'SI')
        {
            arrayChecked.push(item['valor']);
        }
    });
    
    Ext.getCmp('checklist_rb').setValue({rb_checklist: arrayChecked});
 
    winIngresoFactibilidad.show();   
}

function showPreFactibilidadPac(rec)
{
    var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
    var CamposRequeridos = Ext.create('Ext.Component', {
        html: iniHtmlCamposRequeridos,
        padding: 1,
        layout: 'anchor',
        style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });

    var DTFechaProgramacion = new Ext.form.DateField({
        id: 'fechaProgramacion',
        name: 'fechaProgramacion',
        fieldLabel: '* Fecha',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        editable: false,
        minValue: new Date(),
        value: new Date(),
        labelStyle: "color:red;"

    });

    var formPanelFactibilidad = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        bodyStyle: "background: white; padding:10px; border: 0px none;",
        frame: true,
        items: [
            CamposRequeridos,
            {
                xtype: 'fieldset',
                title: 'Datos de Factibilidad de HOUSING ( PAC )',
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                defaults: {
                    width: '350px'
                },
                items: [
                    DTFechaProgramacion,
                    {
                        xtype: 'textarea',
                        fieldLabel: '* Observacion',
                        name: 'info_observacion',
                        id: 'info_observacion',
                        allowBlank: false,
                    }
                ]
            }
        ],
        buttons: [
            {
                text: 'Guardar',
                handler: function() 
                {
                    var fechaProgramacion = Ext.getCmp('fechaProgramacion').value;
                    var txtObservacion    = Ext.getCmp('info_observacion').value;

                    var boolError = false;
                    var mensajeError = "";
                    if (!fechaProgramacion || fechaProgramacion == "" || fechaProgramacion == 0)
                    {
                        boolError = true;
                        mensajeError += "La fecha de factibilidad no fue seleccionada, por favor seleccione.\n";
                    }

                    if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                    {
                        boolError = true;
                        mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                    }
                    
                    var fecha = fechaProgramacion.getDate() + '-' + (fechaProgramacion.getMonth() + 1)+'-' +  fechaProgramacion.getFullYear();

                    if (!boolError)
                    {
                        $.ajax({
                            type: "POST",
                            timeout: 120000,
                            url: urlFechaFactibilidad,
                            data:
                                {
                                    idSolicitud:       rec.get('id_factibilidad'),
                                    fecha:             fecha,
                                    observacion:       txtObservacion
                                },
                            beforeSend: function()
                            {
                                Ext.get(winPreFactibilidad.getId()).mask('Editando Fecha de Factibilidad eléctrica...');
                            },
                            complete: function()
                            {
                                Ext.get(winPreFactibilidad.getId()).unmask();
                            },
                            success: function(text)
                            {
                                if (text === "OK")
                                {
                                    Ext.Msg.alert('Mensaje', 'Se modificó Correctamente el detalle de la Solicitud de Factibilidad', function(btn) {
                                        if (btn == 'ok') 
                                        {
                                            winPreFactibilidad.close();
                                            store.load();
                                        }
                                    });                                    
                                }
                                else {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: text,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        });
                    }
                    else 
                    {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: mensajeError,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                }
            },
            {
                text: 'Cerrar',
                handler: function() 
                {
                    winPreFactibilidad.close();
                }
            }
        ]
    });

    var winPreFactibilidad = Ext.widget('window', {
        title: 'Solicitud de Factibilidad',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: false,
        items: [formPanelFactibilidad]
    });

    winPreFactibilidad.show();
}

function rechazarFactibilidadPac(rec)
{
    var storeMotivos = new Ext.data.Store({
        total: 'total',
        pageSize: 10000,
        proxy: {
            type: 'ajax',
            url: urlMotivosRechazo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                modulo: 'factibilidad_pac',
                accion: 'index'
            }
        },
        fields:
            [
                {name: 'id_motivo', mapping: 'id_motivo'},
                {name: 'nombre_motivo', mapping: 'nombre_motivo'}
            ],
        autoLoad: true
    });
    //******** html campos requeridos...
    var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
    var CamposRequeridos = Ext.create('Ext.Component', {
        html: iniHtmlCamposRequeridos,
        padding: 1,
        layout: 'anchor',
        style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });

    var formPanelRechazarOrden_Factibilidad = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        bodyStyle: "background: white; padding:10px; border: 0px none;",
        frame: true,
        items: [
            CamposRequeridos,
            {
                xtype: 'fieldset',
                title: 'Datos del Cliente',
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                layout: 'anchor',
                defaults: {
                    width: '350px'
                },
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Cliente',
                        name: 'info_cliente',
                        id: 'info_cliente',
                        value: rec.get("cliente"),
                        allowBlank: false,
                        readOnly: true
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Login',
                        name: 'info_login',
                        id: 'info_login',
                        value: rec.get("login2"),
                        allowBlank: false,
                        readOnly: true
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Ciudad',
                        name: 'info_ciudad',
                        id: 'info_ciudad',
                        value: rec.get("ciudad"),
                        allowBlank: false,
                        readOnly: true
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Direccion',
                        name: 'info_direccion',
                        id: 'info_direccion',
                        value: rec.get("direccion"),
                        allowBlank: false,
                        readOnly: true
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Sector',
                        name: 'info_nombreSector',
                        id: 'info_nombreSector',
                        value: rec.get("nombreSector"),
                        allowBlank: false,
                        readOnly: true
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Es Recontratacion',
                        name: 'es_recontratacion',
                        id: 'es_recontratacion',
                        value: rec.get("esRecontratacion"),
                        allowBlank: false,
                        readOnly: true
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Datos del Servicio',
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                defaults: {
                    width: '350px'
                },
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Servicio',
                        name: 'info_servicio',
                        id: 'info_servicio',
                        value: rec.get("producto"),
                        allowBlank: false,
                        readOnly: true
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Tipo Orden',
                        name: 'tipo_orden_servicio',
                        id: 'tipo_orden_servicio',
                        value: rec.get("tipo_orden"),
                        allowBlank: false,
                        readOnly: true
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Datos del Rechazo',
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                defaults: {
                    width: '350px'
                },
                items: [
                    {
                        xtype: 'combobox',
                        id: 'cmbMotivo',
                        fieldLabel: '* Motivo',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField: 'nombre_motivo',
                        valueField: 'id_motivo',
                        selectOnTab: true,
                        store: storeMotivos,
                        lazyRender: true,
                        queryMode: "local",
                        listClass: 'x-combo-list-small',
                        labelStyle: "color:red;"
                    }
                    , {
                        xtype: 'textarea',
                        fieldLabel: '* Observacion',
                        name: 'info_observacion',
                        id: 'info_observacion',
                        allowBlank: false,
                        labelStyle: "color:red;"
                    }
                ]
            }
        ],
        buttons: [
            {
                text: 'Rechazar',
                handler: function() 
                {
                    var txtObservacion  = Ext.getCmp('info_observacion').value;
                    var cmbMotivo       = Ext.getCmp('cmbMotivo').value;
                    var id_factibilidad = rec.get("id_factibilidad");

                    var boolError = false;
                    var mensajeError = "";

                    if (Ext.isEmpty(id_factibilidad))
                    {
                        boolError = true;
                        mensajeError += "El id del Detalle Solicitud no existe.\n";
                    }
                    if (Ext.isEmpty(cmbMotivo))
                    {
                        boolError = true;
                        mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                    }
                    if (Ext.isEmpty(txtObservacion))
                    {
                        boolError = true;
                        mensajeError += "La observación no fue ingresada, por favor ingrese.\n";
                    }

                    if (!boolError)
                    {
                        $.ajax({
                            type   : "POST",
                            url    : urlRechazarFactibilidadPac,
                            timeout: 900000,
                            data   : 
                            {
                              'idServicio'     : rec.get("id_servicio"),
                              'idSolicitud'    : id_factibilidad,
                              'idMotivo'       : cmbMotivo, 
                              'observacion'    : txtObservacion,
                              'accion'         : 'Rechazar',
                              'origen'         : 'planificacion'
                            },
                            beforeSend: function() 
                            {            
                                Ext.MessageBox.show({
                                       msg: 'Rechazando Factibilidad del PAC',
                                       progressText: 'Rechazando...',
                                       width:300,
                                       wait:true,
                                       waitConfig: {interval:200}
                                    });                     
                            },
                            success: function(data)
                            {                  
                                if (data.status === "OK")
                                {
                                    Ext.Msg.alert('Mensaje', data.mensaje, function(btn) {
                                        if (btn == 'ok') 
                                        {
                                            winRechazarOrden_Factibilidad.close();
                                            store.load();
                                        }
                                    });                                    
                                }
                                else 
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: data.mensaje,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        });                           
                    }
                    else 
                    {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: mensajeError,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                }
            },
            {
                text: 'Cerrar',
                handler: function() 
                {
                    winRechazarOrden_Factibilidad.close();
                }
            }
        ]
    });

    var winRechazarOrden_Factibilidad = Ext.widget('window', {
        title: 'Rechazo de Orden de Factibilidad Eléctrica',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: false,
        items: [formPanelRechazarOrden_Factibilidad]
    });    

    winRechazarOrden_Factibilidad.show();
}