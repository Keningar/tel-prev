function showRechazarOrden_FactibilidadRadio(rec)
{
    winRechazarOrden_Factibilidad = "";
    formPanelRechazarOrden_Factibilidad = "";

    if (!winRechazarOrden_Factibilidad)
    {
        storeMotivos = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: url_getMotivosRechazo,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    nombre: '',
                    estado: 'ACTIVE'
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
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        formPanelRechazarOrden_Factibilidad = Ext.create('Ext.form.Panel', {
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
                    handler: function() {
                        var txtObservacion = Ext.getCmp('info_observacion').value;
                        var cmbMotivo = Ext.getCmp('cmbMotivo').value;
                        var id_factibilidad = rec.get("id_factibilidad");

                        var boolError = false;
                        var mensajeError = "";
                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }
                        if (!cmbMotivo || cmbMotivo == "" || cmbMotivo == 0)
                        {
                            boolError = true;
                            mensajeError += "El motivo no fue escogido, por favor seleccione.\n";
                        }
                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }

                        if (!boolError)
                        {
                            connFactibilidad.request({
                                method: 'POST',
                                params: {id: id_factibilidad, id_motivo: cmbMotivo, observacion: txtObservacion},
                                url: url_rechazar,
                                success: function(response) {
                                    var text = response.responseText;
                                    cierraVentanaRechazarOrden_Factibilidad();
                                    if (text == "Se rechazo la Solicitud de Factibilidad")
                                    {

                                        Ext.Msg.alert('Mensaje', text, function(btn) {
                                            if (btn == 'ok') {
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
                                },
                                failure: function(result) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        }
                        else {
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
                    handler: function() {
                        cierraVentanaRechazarOrden_Factibilidad();
                    }
                }
            ]
        });

        winRechazarOrden_Factibilidad = Ext.widget('window', {
            title: 'Rechazo de Orden de Servicio',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelRechazarOrden_Factibilidad]
        });
    }

    winRechazarOrden_Factibilidad.show();
}


/*
 * Funcion utilizada para mostrar ventana de ingreso de fecha  de factibilidad de servicios radio TN
 * 
 * @author Edgar Holguin <eholguin@telconet.ec>
 * @version 1.0  
 * 
 */
function showPreFactibilidadRadio(rec)
{
    var arrayParametros = [];
    arrayParametros['strPrefijoEmpresa']      = rec.get("strPrefijoEmpresa");
    arrayParametros['strDescripcionTipoRol']  = 'Contacto';
    arrayParametros['strEstadoTipoRol']       = 'Activo';
    arrayParametros['strDescripcionRol']      = 'Contacto Tecnico';
    arrayParametros['strEstadoRol']           = 'Activo';
    arrayParametros['strEstadoIER']           = 'Activo';
    arrayParametros['strEstadoIPER']          = 'Activo';
    arrayParametros['strEstadoIPC']           = 'Activo';
    arrayParametros['intIdPersonaEmpresaRol'] = rec.get("intIdPersonaEmpresaRol");
    arrayParametros['intIdPunto']             = rec.get("id_punto");
    arrayParametros['idStore']                = 'storeContactoPreFac';
    arrayParametros['strJoinPunto']           = '';
    
    strDefaultType        = "hiddenfield";
    strXtype              = "hiddenfield";
    strNombres            = '';
    strApellidos          = '';
    storeRolContacto      = '';
    storeRolContactoPunto = '';
    
    //Informacion del contacto
  
    var arrayPersonaContacto = [];
    storeRolContacto         = getInfoPersonaContacto(arrayParametros);
    
    arrayParametros['intIdPersonaEmpresaRol'] = '';
    arrayParametros['idStore']                = 'storeContactoPuntoShowPreFac';
    arrayParametros['strJoinPunto']           = 'PUNTO';
    storeRolContactoPunto                     = getInfoPersonaContacto(arrayParametros);
    strNombres                                = arrayPersonaContacto['strNombres'];
    strApellidos                              = arrayPersonaContacto['strApellidos'];
    strDefaultType                            = "textfield";
    strXtype                                  = "fieldset";
    
    var boolEsEdificio        = true;
    var boolDependeDeEdificio = true;
    var boolNombreEdificio    = true;

    if ("S" === rec.get("strEsEdificio")) 
    {
        boolEsEdificio = false;
    }
    if ("S" === rec.get("strDependeDeEdificio")) 
    {
        boolDependeDeEdificio = false;
    }
    if (false === boolDependeDeEdificio || false === boolEsEdificio) 
    {
        boolNombreEdificio = false;
    }
    winPreFactibilidad    = "";
    formPanelFactibilidad = "";
    
    if (!winPreFactibilidad)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        DTFechaProgramacion = new Ext.form.DateField({
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

        formPanelFactibilidad = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'fieldset',
                    title: '',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 2,
                        pack: 'center'
                    },
                    items: [
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
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Latitud',
                                    name: 'strLatitud',
                                    id: 'intIdLatitud',
                                    value: rec.get("latitud"),
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Longitud',
                                    name: 'strLongitud',
                                    id: 'intIdLongitud',
                                    value: rec.get("longitud"),
                                    readOnly: true
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Es Edificio',
                                    checked: true,
                                    readOnly: true,
                                    hidden: boolEsEdificio
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Depende de Edificio',
                                    readOnly: true,
                                    checked: true,
                                    hidden: boolDependeDeEdificio
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Nombre Edificio',
                                    name: 'strNombreEdificio',
                                    id: 'intIdNombreEdificio',
                                    value: rec.get("strNombreEdificio"),
                                    readOnly: true,
                                    hidden: boolNombreEdificio
                                }
                            ]
                        },
                        {
                            xtype: strXtype,
                            title: 'Datos Contacto Tecnico',
                            style: "font-weight:bold; margin-bottom: 15px;",
                            layout: 'anchor',
                            defaults: {
                                width: '350px'
                            },
                            items: [
                                {
                                    xtype: 'tabpanel',
                                    activeTab: 0,
                                    autoScroll: false,
                                    layoutOnTabChange: true,
                                    items: [
                                        {
                                            xtype: 'grid',
                                            title: 'Contacto nivel cliente',
                                            store: storeRolContacto,
                                            id: 'gridRolContacto',
                                            height: 80,
                                            columns: [
                                                {
                                                    header: "Nombres",
                                                    dataIndex: 'strNombres',
                                                    width: 174
                                                },
                                                {
                                                    header: "Apellidos",
                                                    dataIndex: 'strApellidos',
                                                    width: 174
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'grid',
                                            title: 'Contacto nivel punto',
                                            store: storeRolContactoPunto,
                                            id: 'gridRolContactoPunto',
                                            height: 80,
                                            columns: [
                                                {
                                                    header: "Nombres",
                                                    dataIndex: 'strNombres',
                                                    width: 174
                                                },
                                                {
                                                    header: "Apellidos",
                                                    dataIndex: 'strApellidos',
                                                    width: 174
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
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
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Tipo Enlace',
                            name: 'tipoEnlace',
                            id: 'tipoEnlace',
                            value: rec.get("strTipoEnlace"),
                            allowBlank: false,
                            readOnly: true
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos de Nuevo Tramo',
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
                            labelStyle: "color:red;"
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var fechaProgramacion = Ext.getCmp('fechaProgramacion').value;
                        var id_factibilidad = rec.get("id_factibilidad");
                        var txtObservacion = Ext.getCmp('info_observacion').value;

                        var boolError = false;
                        var mensajeError = "";

                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }

                        if (!fechaProgramacion || fechaProgramacion == "" || fechaProgramacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La fecha de creacion del nuevo Tramo no fue seleccionada, por favor seleccione.\n";
                        }

                        if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                        {
                            boolError = true;
                            mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                        }

                        if (!boolError)
                        {
                            connFactibilidad.request({
                                url: urlFactibilidadTramo,
                                timeout: 120000,
                                method: 'post',
                                params: {
                                    id: id_factibilidad,
                                    fechaProgramacion: fechaProgramacion,
                                    observacion: txtObservacion
                                },
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "Se modifico Correctamente el detalle de la Solicitud de Factibilidad")
                                    {
                                        cierraVentanaPreFactibilidadRadio();
                                        Ext.Msg.alert('Mensaje', text, function(btn) {
                                            if (btn == 'ok') {
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
                                },
                                failure: function(result) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        }
                        else {
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
                    handler: function() {
                        cierraVentanaPreFactibilidadRadio();
                    }
                }
            ]
        });

        winPreFactibilidadRadio = Ext.widget('window', 
                                              {
                                                    title: 'Solicitud de Factibilidad',
                                                    layout: 'fit',
                                                    resizable: false,
                                                    modal: true,
                                                    closable: false,
                                                    items: [formPanelFactibilidad]
                                               });
    }

    winPreFactibilidadRadio.show();
}

function showFactibilidadRadioMd(rec)
{
    const idServicio = rec.data.id_servicio;
    winFactibilidad = "";
    formPanelFactibilidad = "";
    // store.load();
    if (!winFactibilidad)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        Ext.define('esTercerizada', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'valor', type: 'string'}
            ]
        });

        storeTercerizada = new Ext.data.Store({
            model: 'esTercerizada',
            data: [
                {valor: 'N'},
                {valor: 'S'},
            ]
        });

        storeTercerizadoras = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: url_getEmpresasExternas,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                }
            },
            fields:
                    [
                        {name: 'id_empresa_externa', mapping: 'id_empresa_externa'},
                        {name: 'nombre_empresa_externa', mapping: 'nombre_empresa_externa'}
                    ],
            autoLoad: true
        });

        storeUM = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            autoLoad: true,
            listeners: {
                load: function() {
                    var combo = Ext.getCmp("cmb_UM");
                    if (combo)
                        combo.setValue(rec.data.ultimaMilla);
                    if (rec.data.ultimaMilla == "Cobre") {
                        Ext.getCmp('cmb_POP').setVisible(true);
                        Ext.getCmp('cmb_DSLAM').setVisible(true);
                        Ext.getCmp('puertos_disponibles').setVisible(true);
                    }
                    if (rec.data.ultimaMilla == "Radio") {
                        Ext.getCmp('cmb_RADIO').setVisible(true);
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: url_ajaxComboTiposMedio,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    nombre: this.nombreTipoMedio
                }
            },
            fields:
                    [
                        {name: 'idTipoMedio', mapping: 'idTipoMedio'},
                        {name: 'nombreTipoMedio', mapping: 'nombreTipoMedio'}
                    ]
        });

        storeElementos = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: url_ajaxComboElementos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    nombre: this.nombreElemento,
                    modelo: '',
                    elemento: 'POP'
                }
            },
            fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ],
            autoLoad: true
        });

        storeElementosRadio = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: url_ajaxComboElementos,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    nombre: this.nombreElemento,
                    modelo: '',
                    elemento: 'RADIO'
                }
            },
            fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ],
            autoLoad: true
        });

        storeElementosByPadre = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: url_ajaxComboElementosByPadre,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    popId: '',
                    elemento: 'DSLAM',
                    estado: 'ACTIVE'
                }
            },
            fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
        });

        formPanelFactibilidad = Ext.create('Ext.form.Panel', {
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
                    title: 'Datos de Factibilidad',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [
                        {
                            xtype: 'combobox',
                            id: 'cmb_ESTERCERIZADA',
                            name: 'cmb_ESTERCERIZADA',
                            fieldLabel: '* Es Tercerizada',
                            displayField: 'valor',
                            valueField: 'valor',
                            labelStyle: "color:red;",
                            value: 'N',
                            queryMode: 'local',
                            store: storeTercerizada,
                            listeners: {
                                select: {
                                    fn: function(combo, value) {
                                        if (combo.value == "S") {
                                            Ext.getCmp('cmb_TERCERIZADORA').setVisible(true);
                                        } else {
                                            Ext.getCmp('cmb_TERCERIZADORA').setVisible(false);
                                        }

                                        var esTercerizada = Ext.getCmp('cmb_ESTERCERIZADA').value;
                                        var modelo = '';

                                        if (esTercerizada == "S") {
                                            modelo = 'TERCERIZADO';
                                        }

                                        storeElementosRadio.proxy.extraParams = {nombre: '', elemento: 'RADIO', modelo: modelo};
                                        storeElementosRadio.load({params: {}});

                                        storeElementos.proxy.extraParams = {nombre: '', elemento: 'POP', modelo: modelo};
                                        storeElementos.load({params: {}});

                                        Ext.getCmp('cmb_DSLAM').reset();
                                        Ext.getCmp('cmb_DSLAM').setDisabled(false);
                                        $('input[name="puertos_disponibles"]').val(0);

                                    }
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_TERCERIZADORA',
                            name: 'cmb_TERCERIZADORA',
                            fieldLabel: '* Tercerizadora',
                            hidden: true,
                            typeAhead: true,
                            triggerAction: 'all',
                            displayField: 'nombre_empresa_externa',
                            queryMode: "local",
                            valueField: 'id_empresa_externa',
                            selectOnTab: true,
                            store: storeTercerizadoras,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;",
                            forceSelection: true,
                            emptyText: 'Seleccione..',
                            minChars: 3,
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_UM',
                            name: 'cmb_UM',
                            fieldLabel: 'UM',
                            typeAhead: true,
                            triggerAction: 'all',
                            queryMode: "local",
                            displayField: 'nombreTipoMedio',
                            valueField: 'idTipoMedio',
                            selectOnTab: true,
                            store: storeUM,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            listeners: {
                                select: {fn: function(combo, value) {
                                        if (Ext.getCmp('cmb_UM').getRawValue() == "Cobre") {
                                            Ext.getCmp('cmb_RADIO').setVisible(false);
                                            Ext.getCmp('cmb_POP').setVisible(true);
                                            Ext.getCmp('cmb_DSLAM').setVisible(true);
                                            Ext.getCmp('puertos_disponibles').setVisible(true);
                                        } else if (Ext.getCmp('cmb_UM').getRawValue() == "Radio") {
                                            Ext.getCmp('cmb_POP').setVisible(false);
                                            Ext.getCmp('cmb_DSLAM').setVisible(false);
                                            Ext.getCmp('puertos_disponibles').setVisible(false);
                                            Ext.getCmp('cmb_RADIO').setVisible(true);
                                        } else {
                                            Ext.getCmp('cmb_POP').setVisible(false);
                                            Ext.getCmp('cmb_DSLAM').setVisible(false);
                                            Ext.getCmp('puertos_disponibles').setVisible(false);
                                            Ext.getCmp('cmb_RADIO').setVisible(false);
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: "La Ultima Milla no tiene parametros ingresados",
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    }}
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_POP',
                            name: 'cmb_POP',
                            fieldLabel: '* POP',
                            hidden: true,
                            typeAhead: true,
                            triggerAction: 'all',
                            displayField: 'nombreElemento',
                            queryMode: "local",
                            valueField: 'idElemento',
                            selectOnTab: true,
                            store: storeElementos,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;",
                            forceSelection: true,
                            emptyText: 'Ingrese un POP..',
                            minChars: 3,
                            listeners: {
                                select: {fn: function(combo, value) {
                                        Ext.getCmp('cmb_DSLAM').reset();
                                        Ext.getCmp('cmb_DSLAM').setDisabled(false);
                                        $('input[name="puertos_disponibles"]').val(0);

                                        storeElementosByPadre.proxy.extraParams = {
                                            popId: combo.getValue(),
                                            elemento: 'DSLAM',
                                            estado: 'Activo',
                                            idServicio: idServicio
                                        };
                                        storeElementosByPadre.load({params: {}});
                                    }},
                                change: {fn: function(combo, newValue, oldValue) {
                                        if (combo) {
                                            if (combo.getValue() > 0) {
                                            } else {
                                                if (combo.getValue()) {
                                                    if (combo.getValue().match(/[a-zA-Z]/)) {
                                                        var esTercerizada = Ext.getCmp('cmb_ESTERCERIZADA').value;
                                                        var modelo = '';

                                                        if (esTercerizada == "S") {
                                                            modelo = 'TERCERIZADO';
                                                        }
                                                        storeElementos.proxy.extraParams = {
                                                            nombre: combo.getValue(),
                                                            elemento: 'POP',
                                                            modelo: modelo
                                                        };
                                                    }
                                                }
                                            }
                                        }
                                    }}
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_DSLAM',
                            name: 'cmb_DSLAM',
                            hidden: true,
                            fieldLabel: '* DSLAM',
                            typeAhead: true,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            selectOnTab: true,
                            store: storeElementosByPadre,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            emptyText: 'Seleccione un DSLAM',
                            labelStyle: "color:red;",
                            disabled: true,
                            editable: false,
                            listeners: {
                                select: {fn: function(combo, value) {
                                        Ext.Ajax.request({
                                            url: url_ajaxDisponibilidadElemento,
                                            method: 'post',
                                            params: {idElemento: combo.getValue()},
                                            success: function(response) {
                                                var ContDisponibilidad = response.responseText;
                                                $('input[name="puertos_disponibles"]').val(ContDisponibilidad);
                                            },
                                            failure: function(result)
                                            {
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: result.statusText,
                                                    buttons: Ext.MessageBox.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            }
                                        });
                                    }}
                            }
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Disponibilidad',
                            name: 'puertos_disponibles',
                            hidden: true,
                            id: 'puertos_disponibles',
                            maxLength: 3,
                            value: 0,
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_RADIO',
                            name: 'cmb_RADIO',
                            fieldLabel: '* RADIO',
                            hidden: true,
                            typeAhead: true,
                            triggerAction: 'all',
                            displayField: 'nombreElemento',
                            queryMode: "local",
                            valueField: 'idElemento',
                            selectOnTab: true,
                            store: storeElementosRadio,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;",
                            forceSelection: true,
                            emptyText: 'Seleecione..',
                            minChars: 3,
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var esTercerizada = Ext.getCmp('cmb_ESTERCERIZADA').value;
                        var tercerizadora = Ext.getCmp('cmb_TERCERIZADORA').value;

                        var cmb_UM = Ext.getCmp('cmb_UM').value;
                        var UM = Ext.getCmp('cmb_UM').getRawValue();
                        var cmb_POP = Ext.getCmp('cmb_POP').value;
                        var cmb_DSLAM = Ext.getCmp('cmb_DSLAM').value;
                        var cmb_RADIO = Ext.getCmp('cmb_RADIO').value;
                        var puertosDisponibles = $('input[name="puertos_disponibles"]').val();
                        var id_factibilidad = rec.get("id_factibilidad");

                        var boolError = false;
                        var parametros;
                        var mensajeError = "";

                        if (esTercerizada == "S" && !tercerizadora)
                        {
                            boolError = true;
                            mensajeError += "Por favor escoger una Tercerizadora.\n";
                        }
                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }
                        if (UM == "Cobre") {
                            if (!cmb_POP || cmb_POP == "" || cmb_POP == 0)
                            {
                                boolError = true;
                                mensajeError += "El Elemento POP no fue escogido, por favor seleccione.\n";
                            }
                            if (!cmb_DSLAM || cmb_DSLAM == "" || cmb_DSLAM == 0)
                            {
                                boolError = true;
                                mensajeError += "El Elemento DSLAM no fue escogido, por favor seleccione.\n";
                            }
                            if (boolError && puertosDisponibles == "" && puertosDisponibles < 0)
                            {
                                boolError = true;
                                mensajeError += "Los puertos disponibles no fue cargada. \n";
                            }
                            if (!boolError && puertosDisponibles == 0)
                            {
                                boolError = true;
                                mensajeError += "El DLSAM no tiene puertos disponibles. \n";
                            }
                            parametros = {esTercerizada: esTercerizada, tercerizadora: tercerizadora, id: id_factibilidad, elemento_id: cmb_DSLAM, um_id: cmb_UM};
                        } else if (UM == "Radio") {
                            if (!cmb_RADIO || cmb_RADIO == "" || cmb_RADIO == 0)
                            {
                                boolError = true;
                                mensajeError += "El Elemento RADIO no fue escogido, por favor seleccione.\n";
                            }
                            parametros = {esTercerizada: esTercerizada, tercerizadora: tercerizadora, id: id_factibilidad, elemento_id: cmb_RADIO, um_id: cmb_UM};
                        } else {
                            boolError = true;
                            mensajeError += "La Ultima Milla no tiene parametros ingresados para poder ingresar la Factibilidad.\n";
                        }
                        if (!boolError)
                        {
                            connFactibilidad.request({
                                url: url_ajaxGuardaFactibilidad,
                                method: 'post',
                                params: parametros,
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "Se modifico Correctamente el detalle de la Solicitud de Factibilidad")
                                    {
                                        cierraVentanaFactibilidad();
                                        Ext.Msg.alert('Mensaje', text, function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                                showFactibilidadMateriales(rec, "factibilidad");
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
                                },
                                failure: function(result) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        }
                        else {
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
                    handler: function() {
                        cierraVentanaFactibilidad();
                    }
                }
            ]
        });

        winFactibilidad = Ext.widget('window', {
            title: 'Solicitud de Factibilidad',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelFactibilidad]
        });
    }

    winFactibilidad.show();
}

function showFactibilidadRadioTn(rec)
{
    const idServicio            = rec.data.id_servicio;
    var intIdElemento           = 0;
    var intIdInterfaceElemento  = 0;
    var arrayParametros         = [];
    strPrefijoEmpresa           = rec.get("strPrefijoEmpresa");
    arrayParametros['strPrefijoEmpresa']        = strPrefijoEmpresa;
    arrayParametros['strDescripcionTipoRol']    = 'Contacto';
    arrayParametros['strEstadoTipoRol']         = 'Activo';
    arrayParametros['strDescripcionRol']        = 'Contacto Tecnico';
    arrayParametros['strEstadoRol']             = 'Activo';
    arrayParametros['strEstadoIER']             = 'Activo';
    arrayParametros['strEstadoIPER']            = 'Activo';
    arrayParametros['strEstadoIPC']             = 'Activo';
    arrayParametros['intIdPersonaEmpresaRol']   = rec.get("intIdPersonaEmpresaRol");
    strDefaultType                              = "hiddenfield";
    strXtype                                    = "hiddenfield";
    strNombres                                  = '';
    strApellidos                                = '';
    boolCheckObraCivil                          = false;
    boolCheckObservacionRegeneracion            = false;
    
    if ("TN" === strPrefijoEmpresa) {
        var arrayPersonaContacto    = [];
        arrayPersonaContacto        = obtienInfoPersonaContacto(arrayParametros);
        strNombres                  = arrayPersonaContacto['strNombres'];
        strApellidos                = arrayPersonaContacto['strApellidos'];
        strDefaultType              = "textfield";
        strXtype                    = "fieldset";
    }
    
    var boolEsEdificio          = true;
    var boolDependeDeEdificio   = true;
    var boolNombreEdificio      = true;
    if ("S" === rec.get("strEsEdificio")) {
        boolEsEdificio = false;
    }
    if ("S" === rec.get("strDependeDeEdificio")) {
        boolDependeDeEdificio = false;
    }
    if (false === boolDependeDeEdificio || false === boolEsEdificio) {
        boolNombreEdificio = false;
    }
    var strNombreTipoElemento = "SPLITTER";
    var strNombreElementoPadre = "Olt";
    
    
    Ext.define('esTercerizada', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'valor', type: 'string'}
            ]
        });
    
    storeTercerizada = new Ext.data.Store({
            model: 'esTercerizada',
            data: [
                {valor: 'N'},
                {valor: 'S'},
            ]
        });
    
    storeTercerizadoras = new Ext.data.Store({
        total: 'total',
        pageSize: 10000,
        proxy: {
            type: 'ajax',
            url: url_getEmpresasExternas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: {
            }
        },
        fields:
                [
                    {name: 'id_empresa_externa', mapping: 'id_empresa_externa'},
                    {name: 'nombre_empresa_externa', mapping: 'nombre_empresa_externa'}
                ],
        autoLoad: true
    });
    
    storeElementosRadio = new Ext.data.Store({
                                                total: 'total',
                                                pageSize: 10000,
                                                proxy: {
                                                    timeout: 60000,
                                                    type: 'ajax',
                                                    url: url_ajaxComboElementos,
                                                    reader: {
                                                        type: 'json',
                                                        totalProperty: 'total',
                                                        root: 'encontrados'
                                                    },
                                                    actionMethods: {
                                                        create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                                                    },
                                                    extraParams: {
                                                        nombre: this.nombreElemento,
                                                        modelo: '',
                                                        elemento: 'RADIO',
                                                        tipoElementoRed: 'BACKBONE'
                                                    }
                                                },
                                                fields:
                                                        [
                                                            {name: 'idElemento', mapping: 'idElemento'},
                                                            {name: 'nombreElemento', mapping: 'nombreElemento'}
                                                        ],
                                                autoLoad: true
                                            });

        
    
    winIngresoFactibilidad = "";
    formPanelInresoFactibilidad = "";
    if (!winIngresoFactibilidad)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        DTFechaProgramacion = new Ext.form.DateField({
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

        formPanelIngresoFactibilidad = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding: 5px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'fieldset',
                    title: '',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 2,
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 5px;",
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
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Latitud',
                                    name: 'strLatitud',
                                    id: 'intIdLatitud',
                                    value: rec.get("latitud"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Longitud',
                                    name: 'strLongitud',
                                    id: 'intIdLongitud',
                                    value: rec.get("longitud"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Es Edificio',
                                    checked: true,
                                    readOnly: true,
                                    hidden: boolEsEdificio
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Depende de Edificio',
                                    readOnly: true,
                                    checked: true,
                                    hidden: boolDependeDeEdificio
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Nombre Edificio',
                                    name: 'strNombreEdificio',
                                    id: 'intIdNombreEdificio',
                                    value: rec.get("strNombreEdificio"),
                                    allowBlank: false,
                                    readOnly: true,
                                    hidden: boolNombreEdificio
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: '',
                            layout: {
                                tdAttrs: {style: 'padding: 5px;'},
                                type: 'table',
                                columns: 1,
                                pack: 'center'
                            },
                            items: [
                                {
                                    xtype: 'fieldset',
                                    title: 'Datos del Servicio',
                                    defaultType: 'textfield',
                                    style: "font-weight:bold; margin-bottom: 5px;",
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
                                    xtype: strXtype,
                                    title: 'Datos Contacto Tecnico',
                                    defaultType: strDefaultType,
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    layout: 'anchor',
                                    defaults: {
                                        width: '350px'
                                    },
                                    items: [
                                        {
                                            fieldLabel: 'Nombres',
                                            name: 'strNombreContacto',
                                            id: 'intIdNombreContacto',
                                            value: strNombres,
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            fieldLabel: 'Apellidos',
                                            name: 'strApellidoContacto',
                                            id: 'intIdApellidoContacto',
                                            value: strApellidos,
                                            allowBlank: false,
                                            readOnly: true
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos de Factibilidad',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 5px;",
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 2,
                        pack: 'center',
                    },
                    items: [
                        {
                            xtype: 'fieldset',
                            style: "border:0",
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ultima Milla',
                                    name: 'txtNameUltimaMilla',
                                    id: 'txtIdUltimaMilla',
                                    value: rec.data.ultimaMilla,
                                    readOnly: true,
                                    hidden: false
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'cmb_ESTERCERIZADA',
                                    name: 'cmb_ESTERCERIZADA',
                                    fieldLabel: '* Es Tercerizada',
                                    displayField: 'valor',
                                    valueField: 'valor',
                                    labelStyle: "color:red;",
                                    value: 'N',
                                    queryMode: 'local',
                                    store: storeTercerizada,
                                    listeners: {
                                        select: {
                                            fn: function(combo, value) {
                                                if (combo.value == "S") {
                                                    Ext.getCmp('cmb_TERCERIZADORA').setVisible(true);
                                                } else {
                                                    Ext.getCmp('cmb_TERCERIZADORA').setVisible(false);
                                                }
                                                Ext.getCmp('txtTipoElementoPadre')    .setValue("");
                                                Ext.getCmp('txtElementoPadre')        .setValue("");
                                                Ext.getCmp('txtPuertoElementoPadre')  .setValue("");
                                                Ext.getCmp('txtIdElementoPadre')      .setValue("");
                                                Ext.getCmp('txtIdPuertoElementoPadre').setValue("");

                                                Ext.getCmp('txtTipoElementoPadre')   .setVisible(false);
                                                Ext.getCmp('txtElementoPadre')       .setVisible(false);
                                                Ext.getCmp('txtPuertoElementoPadre') .setVisible(false);
                                                var esTercerizada = Ext.getCmp('cmb_ESTERCERIZADA').value;
                                                var modelo = '';

                                                if (esTercerizada == "S") {
                                                    modelo = 'TERCERIZADO';
                                                }

                                                storeElementosRadio.proxy.extraParams = { nombre: '', 
                                                                                          elemento: 'RADIO', 
                                                                                          modelo: modelo,
                                                                                          tipoElementoRed: 'BACKBONE'};
                                                storeElementosRadio.load({params: {}});
                                            }
                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'cmb_TERCERIZADORA',
                                    name: 'cmb_TERCERIZADORA',
                                    fieldLabel: '* Tercerizadora',
                                    hidden: true,
                                    typeAhead: true,
                                    triggerAction: 'all',
                                    displayField: 'nombre_empresa_externa',
                                    queryMode: "local",
                                    valueField: 'id_empresa_externa',
                                    selectOnTab: true,
                                    store: storeTercerizadoras,
                                    lazyRender: true,
                                    listClass: 'x-combo-list-small',
                                    labelStyle: "color:red;",
                                    forceSelection: true,
                                    emptyText: 'Seleccione..',
                                    minChars: 3,
                                    width: '350px',
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'cmb_RADIO',
                                    name: 'cmb_RADIO',
                                    fieldLabel: '* RADIO',
                                    hidden: false,
                                    typeAhead: true,
                                    triggerAction: 'all',
                                    displayField: 'nombreElemento',
                                    queryMode: "local",
                                    valueField: 'idElemento',
                                    selectOnTab: true,
                                    store: storeElementosRadio,
                                    lazyRender: true,
                                    listClass: 'x-combo-list-small',
                                    labelStyle: "color:red;",
                                    forceSelection: true,
                                    emptyText: 'Seleecione..',
                                    minChars: 3,
                                    width: '350px',
                                     listeners: {
                                        select: {fn: function(combo, value) {
                                                var idRadio          = Ext.getCmp('cmb_RADIO').value;
                                                Ext.MessageBox.wait("Cargando datos de Red...");
                                                Ext.Ajax.request({
                                                    url: urlInfoSwitch,
                                                    method: 'post',
                                                    async: false,
                                                    timeout: 120000,
                                                    params: {
                                                        intIdElementoRadio:    idRadio
                                                    },
                                                    success: function(response) {
                                                        Ext.MessageBox.close();
                                                        var infoSwitch = Ext.JSON.decode(response.responseText);

                                                        if (infoSwitch.error) {
                                                            Ext.getCmp('txtTipoElementoPadre')    .setValue("");
                                                            Ext.getCmp('txtElementoPadre')        .setValue("");
                                                            Ext.getCmp('txtPuertoElementoPadre')  .setValue("");
                                                            Ext.getCmp('txtIdElementoPadre')      .setValue("");
                                                            Ext.getCmp('txtIdPuertoElementoPadre').setValue("");
                                                            
                                                            Ext.getCmp('txtTipoElementoPadre')   .setVisible(false);
                                                            Ext.getCmp('txtElementoPadre')       .setVisible(false);
                                                            Ext.getCmp('txtPuertoElementoPadre') .setVisible(false);
                                                            Ext.getCmp('cmb_RADIO').reset();
                                                            closeVentanaIngresoFactRadio();
                                                            Ext.MessageBox.show({
                                                                title: 'Error',
                                                                msg: infoSwitch.msg ,
                                                                buttons: Ext.MessageBox.OK,
                                                                icon: Ext.MessageBox.ERROR
                                                            });
                                                        } else {
                                                            Ext.getCmp('txtTipoElementoPadre')    .setValue(infoSwitch.tipoElementoPadre);
                                                            Ext.getCmp('txtElementoPadre')        .setValue(infoSwitch.nombreElemento);
                                                            Ext.getCmp('txtPuertoElementoPadre')  .setValue(infoSwitch.linea);
                                                            Ext.getCmp('txtIdElementoPadre')      .setValue(infoSwitch.idElemento);
                                                            Ext.getCmp('txtIdPuertoElementoPadre').setValue(infoSwitch.idLinea);
                                                            
                                                            Ext.getCmp('txtTipoElementoPadre')   .setVisible(true);
                                                            Ext.getCmp('txtElementoPadre')       .setVisible(true);
                                                            Ext.getCmp('txtPuertoElementoPadre') .setVisible(true);
                                                        }
                                                    },
                                                    failure: function(result)
                                                    {
                                                        Ext.MessageBox.close();
                                                        Ext.getCmp('txtTipoElementoPadre')    .setValue("");
                                                        Ext.getCmp('txtElementoPadre')        .setValue("");
                                                        Ext.getCmp('txtPuertoElementoPadre')  .setValue("");
                                                        Ext.getCmp('txtIdElementoPadre')      .setValue("");
                                                        Ext.getCmp('txtIdPuertoElementoPadre').setValue("");

                                                        Ext.getCmp('txtTipoElementoPadre')   .setVisible(false);
                                                        Ext.getCmp('txtElementoPadre')       .setVisible(false);
                                                        Ext.getCmp('txtPuertoElementoPadre') .setVisible(false);
                                                        closeVentanaIngresoFactRadio();
                                                        Ext.MessageBox.show({
                                                            title: 'Error',
                                                            msg: result.statusText,
                                                            buttons: Ext.MessageBox.OK,
                                                            icon: Ext.MessageBox.ERROR
                                                        });
                                                    }
                                                });
                                            }
                                        }
                                    }
                                }
                            ]
                        },
                        { 
                            xtype: 'fieldset',
                            style: "border:0",
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Elemento',
                                    name: 'txtTipoElementoPadre',
                                    id: 'txtTipoElementoPadre',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Nombre Elemento',
                                    name: 'txtElementoPadre',
                                    id: 'txtElementoPadre',
                                    readOnly: true,
                                    hidden: true,
                                    width: '350px',
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Puerto Elemento',
                                    name: 'txtPuertoElementoPadre',
                                    id: 'txtPuertoElementoPadre',
                                    readOnly: true,
                                    hidden: true,
                                },
                                {
                                    xtype: 'textfield',
                                    name: 'txtIdElementoPadre',
                                    id: 'txtIdElementoPadre',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    name: 'txtIdPuertoElementoPadre',
                                    id: 'txtIdPuertoElementoPadre',
                                    readOnly: true,
                                    hidden: true
                                },
                            ]
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        
                        var boolError = false;
                        var parametros;
                        var mensajeError = "";
                        
                        var esTercerizada    = Ext.getCmp('cmb_ESTERCERIZADA').value;
                        var tercerizadora    = Ext.getCmp('cmb_TERCERIZADORA').value;
                        var idElementoSwitch = Ext.getCmp('txtIdElementoPadre').value;
                        var idPuertoSwitch   = Ext.getCmp('txtIdPuertoElementoPadre').value;
                        var cmb_RADIO        = Ext.getCmp('cmb_RADIO').value;
                        var um               = Ext.getCmp('txtIdUltimaMilla').value;
                        
                        var id_factibilidad = rec.get("id_factibilidad");
                        
                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }
                        
                        if (esTercerizada == "S" && !tercerizadora)
                        {
                            boolError = true;
                            mensajeError += "Por favor escoger una Tercializadora.\n";
                        }
                        
                        if (!idPuertoSwitch || idPuertoSwitch == "" || idPuertoSwitch == 0)
                        {
                            boolError = true;
                            mensajeError += "Información ingresada incompleta. Por favor seleccion la radio deseada.\n";
                        }
                        
                        var procesoEjecucion = "factibilidadManual";
                        if (rec.get("estadoFactibilidad") == "Asignar-factibilidad")
                        {
                            procesoEjecucion = "factibilidadManualAnticipada";
                        }
                        parametros = {esTercerizada: esTercerizada, 
                                      tercerizadora: tercerizadora, 
                                      id: id_factibilidad, 
                                      elemento_radio_id: cmb_RADIO, 
                                      elemento_switch_id: idElementoSwitch, 
                                      interface_elemento_switch_id: idPuertoSwitch,
                                      procesoFactibilidad: procesoEjecucion
                                     };
                        
                        if (!boolError)
                        {
                            connFactibilidad.request({
                                url: url_ajaxGuardaFactibilidadRadioTn,
                                method: 'post',
                                params: parametros,
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "Se modifico Correctamente el detalle de la Solicitud de Factibilidad")
                                    {
                                        closeVentanaIngresoFactRadio();
                                        Ext.Msg.alert('Mensaje', text, function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                                showFactibilidadMaterialesRadio(rec, "factibilidad");
                                            }
                                        });
                                    }
                                    else {
                                        closeVentanaIngresoFactRadio();
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: text,
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                },
                                failure: function(result) {
                                    closeVentanaIngresoFactRadio();
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        }
                        else {
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
                    handler: function() {
                        closeVentanaIngresoFactRadio();
                    }
                }
            ]
        });

        winIngresoFactibilidad = Ext.widget('window', {
            title: 'Ingreso de Factibilidad',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelIngresoFactibilidad]
        });
    }

    winIngresoFactibilidad.show();
}

function showFactibilidadAnticipadaRadioTn(rec)
{
    var arrayParametros         = [];
    strPrefijoEmpresa           = rec.get("strPrefijoEmpresa");
    arrayParametros['strPrefijoEmpresa']        = strPrefijoEmpresa;
    arrayParametros['strDescripcionTipoRol']    = 'Contacto';
    arrayParametros['strEstadoTipoRol']         = 'Activo';
    arrayParametros['strDescripcionRol']        = 'Contacto Tecnico';
    arrayParametros['strEstadoRol']             = 'Activo';
    arrayParametros['strEstadoIER']             = 'Activo';
    arrayParametros['strEstadoIPER']            = 'Activo';
    arrayParametros['strEstadoIPC']             = 'Activo';
    arrayParametros['intIdPersonaEmpresaRol']   = rec.get("intIdPersonaEmpresaRol");
    strDefaultType                              = "hiddenfield";
    strXtype                                    = "hiddenfield";
    strNombres                                  = '';
    strApellidos                                = '';
    boolCheckObraCivil                          = false;
    boolCheckObservacionRegeneracion            = false;
    
    if ("TN" === strPrefijoEmpresa) {
        var arrayPersonaContacto    = [];
        arrayPersonaContacto        = obtienInfoPersonaContacto(arrayParametros);
        strNombres                  = arrayPersonaContacto['strNombres'];
        strApellidos                = arrayPersonaContacto['strApellidos'];
        strDefaultType              = "textfield";
        strXtype                    = "fieldset";
    }
    
    storeSwitch = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url : getElementoSwitch,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                marcaElemento:  '',
                modeloElemento: '',
                canton:         '',
                jurisdiccion:   '',
                tipoElemento:   'SWITCH',
                estado:         'Todos'
            }
        },
        fields:
                  [
                    {name:'idElemento',         mapping:'idElemento'},
                    {name:'nombreElemento',     mapping:'nombreElemento'},
                    {name:'ipElemento',         mapping:'ipElemento'},
                    {name:'cantonNombre',       mapping:'cantonNombre'},
                    {name:'jurisdiccionNombre', mapping:'jurisdiccionNombre'},
                    {name:'marcaElemento',      mapping:'marcaElemento'},
                    {name:'modeloElemento',     mapping:'modeloElemento'},
                    {name:'longitud',           mapping:'longitud'},
                    {name:'latitud',            mapping:'latitud'},
                    {name:'estado',             mapping:'estado'},
                    {name:'action1',            mapping:'action1'},
                    {name:'action2',            mapping:'action2'},
                    {name:'action3',            mapping:'action3'}
                  ]
    });
    
    combo_switch = new Ext.form.ComboBox({
        id: 'combo_switch',
        name: 'combo_switch',
        fieldLabel: 'Switch',
        anchor: '100%',
        queryMode: 'remote',
        width: 350,
        emptyText: 'Seleccione Switch',
        store: storeSwitch,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        listeners: {
            select: {fn: function(combo, value) {
                    Ext.getCmp('combo_intSwitch').reset();
                    Ext.getCmp('combo_intSwitch').setDisabled(false);
                    storeIntSwitch.proxy.extraParams = {idElemento: combo.getValue()};
                    storeIntSwitch.load({
                        callback: function() {
                            storeIntSwitch.filter(function(r) {
                                var value = r.get('estado');
                                return (value == 'not connect' || value == 'reserved');
                            });
                        }

                    });

                }},
            change: function(object, newValue, odlValue, eOpts)
            {
                Ext.getCmp('combo_intSwitch').reset();
                Ext.getCmp('combo_intSwitch').setDisabled(true);
            }
        }
    });
    
    storeIntSwitch = new Ext.data.Store({  
        pageSize: 500,

        proxy: {
            type: 'ajax',
            timeout: 400000,
            url : getInterfaceElemento,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
              {name:'idInterface', mapping:'idInterface'},
              {name:'nombreInterfaceElemento', mapping:'nombreInterfaceElemento'},
              {name:'estado', mapping:'estado'},
            ]
    });
    
    combo_intSwitch = new Ext.form.ComboBox({
        id: 'combo_intSwitch',
        name: 'combo_intSwitch',
        fieldLabel: 'Puerto Switch',
        disabled: true,
        anchor: '100%',
        queryMode:'local',
        width: 200,
        emptyText: 'Seleccione Interface Switch',
        store:storeIntSwitch,
        displayField: 'nombreInterfaceElemento',
        valueField: 'idInterface'
    });
    
    winIngresoFactibilidadAnt = "";
    formPanelInresoFactibilidadAnt = "";
    if (!winIngresoFactibilidadAnt)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        formPanelIngresoFactibilidadAnt = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding: 5px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'fieldset',
                    title: '',
                    layout: {
                        tdAttrs: {style: 'padding: 5px;'},
                        type: 'table',
                        columns: 2,
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 5px;",
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
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Latitud',
                                    name: 'strLatitud',
                                    id: 'intIdLatitud',
                                    value: rec.get("latitud"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Longitud',
                                    name: 'strLongitud',
                                    id: 'intIdLongitud',
                                    value: rec.get("longitud"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: '',
                            layout: {
                                tdAttrs: {style: 'padding: 5px;'},
                                type: 'table',
                                columns: 1,
                                pack: 'center'
                            },
                            items: [
                                {
                                    xtype: 'fieldset',
                                    title: 'Datos del Servicio',
                                    defaultType: 'textfield',
                                    style: "font-weight:bold; margin-bottom: 5px;",
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
                                    xtype: strXtype,
                                    title: 'Datos Contacto Tecnico',
                                    defaultType: strDefaultType,
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    layout: 'anchor',
                                    defaults: {
                                        width: '350px'
                                    },
                                    items: [
                                        {
                                            fieldLabel: 'Nombres',
                                            name: 'strNombreContacto',
                                            id: 'intIdNombreContacto',
                                            value: strNombres,
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            fieldLabel: 'Apellidos',
                                            name: 'strApellidoContacto',
                                            id: 'intIdApellidoContacto',
                                            value: strApellidos,
                                            allowBlank: false,
                                            readOnly: true
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    border: false,
                    title: '',
                    layout: {
                        tdAttrs: {style: 'padding-left: 190px;'},
                        type: 'table',
                        columns: 1,
                        pack: 'center'
                    },
                    
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos de Factibilidad Anticipada',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 5px;",
                            layout: 'anchor',
                            defaults: {
                                width: '375px'
                            },
                            items: [
                                {
                                    xtype: 'fieldset',
                                    style: "border:0",
                                    items: [
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Ultima Milla',
                                            name: 'txtNameUltimaMilla',
                                            id: 'txtIdUltimaMilla',
                                            value: rec.data.ultimaMilla,
                                            readOnly: true,
                                            hidden: false
                                        },
                                        combo_switch,
                                        combo_intSwitch
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        
                        var boolError = false;
                        var parametros;
                        var mensajeError = "";
                        
                        var cmb_switch_id    = Ext.getCmp('combo_switch').value;
                        var cmb_intSwitch_id = Ext.getCmp('combo_intSwitch').value;
                        
                        var id_factibilidad = rec.get("id_factibilidad");
                        
                        if (Ext.isEmpty(cmb_switch_id) || Ext.isEmpty(cmb_intSwitch_id))
                        {
                            boolError = true;
                            mensajeError += "Es obligatorio seleccionar el switch y el puerto del switch a reservar para generar esta factibilidad anticipada.\n";
                        }
                        
                        parametros = {id: id_factibilidad, 
                                      elemento_switch_id: cmb_switch_id, 
                                      interface_elemento_switch_id: cmb_intSwitch_id
                                     };
                        
                        if (!boolError)
                        {
                            connFactibilidad.request({
                                url: url_ajaxGuardaFactibilidadAnticipadaRadioTn,
                                method: 'post',
                                params: parametros,
                                success: function(response) {
                                    var respuesta = Ext.decode(response.responseText);
                                    if(respuesta.success)
                                    {
                                        closeVentanaIngresoFactAntRadio();
                                        store.load();
                                        Ext.Msg.alert('Mensaje', respuesta.msg);
                                    }
                                    else
                                    {
                                        closeVentanaIngresoFactAntRadio();
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: respuesta.msg,
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                },
                                failure: function(result) {
                                    closeVentanaIngresoFactAntRadio();
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        }
                        else {
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
                    handler: function() {
                        closeVentanaIngresoFactAntRadio();
                    }
                }
            ]
        });

        winIngresoFactibilidadAnt = Ext.widget('window', {
            title: 'Ingreso de Factibilidad Anticipada',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelIngresoFactibilidadAnt]
        });
    }

    winIngresoFactibilidadAnt.show();
}

function showFactibilidadMaterialesRadio(rec, origen)
{
    winFactibilidadMateriales = "";
    const idServicio          = rec.data.id_servicio;

    if (!winFactibilidadMateriales)
    {
        var id_factibilidad = rec.get("id_factibilidad");
        var id_servicio = rec.get("id_servicio");

        storeTareasByProcesoAndTarea = new Ext.data.Store({
            total: 'total',
            pageSize: 100000,
            proxy: {
                type: 'ajax',
                url: url_getTareasByProcesoAndTareaSinModem,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    servicioId: id_servicio,
                    nombreTarea: 'instalacion',
                    estado: 'Activo'
                }
            },
            fields:
                    [
                        {name: 'idTarea', mapping: 'idTarea'},
                        {name: 'nombreTarea', mapping: 'nombreTarea'}
                    ],
            autoLoad: true
        });

        storeFactibilidadMateriales = new Ext.data.Store({
            pageSize: 40,
            total: 'total',
            proxy: {
                type: 'ajax',
                timeout: 400000,
                url: url_gridMaterialesByTarea,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    idTarea: '',
                    id_detalle_solicitud: id_factibilidad,
                    estado: 'Activo'
                }
            },
            fields:
                    [
                        {name: 'id_detalle_solicitud', mapping: 'id_detalle_solicitud'},
                        {name: 'id_detalle_sol_material', mapping: 'id_detalle_sol_material'},
                        {name: 'id_tarea', mapping: 'id_tarea'},
                        {name: 'id_tarea_material', mapping: 'id_tarea_material'},
                        {name: 'cod_material', mapping: 'cod_material'},
                        {name: 'nombre_material', mapping: 'nombre_material'},
                        {name: 'costo_material', mapping: 'costo_material'},
                        {name: 'precio_venta_material', mapping: 'precio_venta_material'},
                        {name: 'cantidad_empresa', mapping: 'cantidad_empresa'},
                        {name: 'cantidad_estimada', mapping: 'cantidad_estimada'},
                        {name: 'cantidad_cliente', mapping: 'cantidad_cliente'}
                    ]
        });

        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function() {
                    // refresh summaries
                    gridFactibilidadMateriales.getView().refresh();
                }
            }
        });

        gridFactibilidadMateriales = Ext.create('Ext.grid.Panel', {
            width: 1030,
            height: 250,
            store: storeFactibilidadMateriales,
            loadMask: true,
            frame: false,
            columns: [
                {
                    id: 'id_detalle_solicitud',
                    header: 'IdDetalleSolicitud',
                    dataIndex: 'id_detalle_solicitud',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_detalle_sol_material',
                    header: 'IdDetalleSolMaterial',
                    dataIndex: 'id_detalle_sol_material',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_tarea',
                    header: 'IdTarea',
                    dataIndex: 'id_tarea',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_tarea_material',
                    header: 'IdTareaMaterial',
                    dataIndex: 'id_tarea_material',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'cod_material',
                    header: 'Cod Material',
                    dataIndex: 'cod_material',
                    width: 100,
                    sortable: true
                },
                {
                    id: 'nombre_material',
                    header: 'Nombre Material',
                    dataIndex: 'nombre_material',
                    width: 300,
                    sortable: true
                },
                {
                    id: 'costo_material',
                    header: 'Costo Material',
                    dataIndex: 'costo_material',
                    width: 90,
                    align: 'right',
                    sortable: true
                },
                {
                    id: 'precio_venta_material',
                    header: 'Precio Venta Material',
                    dataIndex: 'precio_venta_material',
                    width: 125,
                    align: 'right',
                    sortable: true
                },
                {
                    id: 'cantidad_empresa',
                    header: 'Cantidad (empresa)',
                    dataIndex: 'cantidad_empresa',
                    width: 120,
                    align: 'right',
                    sortable: true
                },
                {
                    id: 'cantidad_estimada',
                    header: 'Cantidad (estimada)',
                    dataIndex: 'cantidad_estimada',
                    width: 130,
                    align: 'right',
                    sortable: true,
                    tdCls: 'custom-azul',
                    editor: new Ext.form.NumberField(
                            {
                                allowBlank: false,
                                allowNegative: false
                            })
                },
                {
                    id: 'cantidad_cliente',
                    header: 'Cantidad (cliente)',
                    dataIndex: 'cantidad_cliente',
                    width: 110,
                    align: 'right',
                    sortable: true,
                    renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                        var valor_empresa = parseInt(record.data.cantidad_empresa);
                        var valor_estimado = parseInt(record.data.cantidad_estimada);
                        var valor_cliente = parseInt(record.data.cantidad_cliente);
                        var subgrupo = parseInt(record.data.subgrupo_material);

                        if (valor_estimado > valor_empresa)
                        {
                            valor_cliente = parseInt(valor_estimado - valor_empresa);
                        }
                        else
                        {
                            record.data.cantidad_estimada = 0;
                            valor_cliente = 0;
                        }

                        if (valor_cliente > 0)
                        {
                            metaData.tdCls = 'custom-rojo';
                        }

                        return valor_cliente;
                    }
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeFactibilidadMateriales,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            plugins: [cellEditing]
        });

        formPanelMateriales = Ext.create('Ext.form.Panel', {
            border: false,
            buttonAlign: 'center',
            width: 1050,
            BodyPadding: 10,
            layout: {
                type: "vbox",
                align: "center"
            },
            defaults: {
                margin: "10 0 0 0"  // Same as CSS (top right bottom left)
            },
            frame: true,
            items: [{
                    xtype: 'button',
                    id: 'btn_guardar',
                    name: 'btn_guardar',
                    text: 'Solicitar Excedentes de Materiales',
                    handler: function() {
                        var id_factibilidad = rec.get("id_factibilidad");

                        var boolError = false;
                        var mensajeError = "";

                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        }

                        if (!boolError)
                        {
                            var boolError2 = false;
                            var jsonMateriales = validarFactibilidadMateriales();
                            if (!jsonMateriales)
                            {
                                boolError2 = true;
                            }

                            if (!boolError2)
                            {
                                connFactibilidad.request({
                                    url: url_guardaFactMateriales,
                                    method: 'post',
                                    params: {id: id_factibilidad, materiales: jsonMateriales},
                                    success: function(response) {
                                        var text = response.responseText;
                                        if (text == "Se registro la Solicitud de los excedentes de los Materiales")
                                        {
                                            cierraVentanaFactibilidadMateriales();
                                            Ext.Msg.alert('Mensaje', text, function(btn) {
                                                if (btn == 'ok') {
                                                    store.load();
                                                    if (origen == "factibilidad")
                                                    {
                                                        showFactibilidadMaterialesRadio(rec, "factibilidad");

                                                    }
                                                }
                                            });
                                        }
                                        else {
                                            alert(text);

                                        }
                                    },
                                    failure: function(result) {
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: result.responseText,
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                });
                            }
                        }
                        else {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: mensajeError,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    }
                },
                gridFactibilidadMateriales
            ]
        });

        formPanelFactibilidadMateriales = Ext.create('Ext.form.Panel', {
            border: false,
            buttonAlign: 'center',
            BodyPadding: 10,
            defaults: {
                margin: "10 0 0 10"  // Same as CSS (top right bottom left)
            },
            frame: true,
            items: [{
                    xtype: 'combobox',
                    id: 'cmb_INST',
                    name: 'cmb_INST',
                    fieldLabel: '* Tarea ',
                    typeAhead: true,
                    triggerAction: 'all',
                    displayField: 'nombreTarea',
                    valueField: 'idTarea',
                    selectOnTab: true,
                    store: storeTareasByProcesoAndTarea,
                    lazyRender: true,
                    listClass: 'x-combo-list-small',
                    labelStyle: "color:red;",
                    queryMode: "local",
                    forceSelection: true,
                    editable: false,
                    emptyText: 'Elija una Tarea..',
                    minChars: 3,
                    listeners: {
                        select: {fn: function(combo, value) {

                                storeFactibilidadMateriales.proxy.extraParams = {idTarea: combo.getValue()};
                                storeFactibilidadMateriales.load({params: {}});

                                gridFactibilidadMateriales.getView().refresh();
                            }}
                    }
                },
                formPanelMateriales],
            buttons: [
                {
                    text: 'Finalizar Excedentes de Materiales',
                    handler: function() {
                        cierraVentanaFactibilidadMateriales();
                    }
                }
            ]
        });

        winFactibilidadMateriales = Ext.widget('window', {
            title: 'Factibilidad de Excedentes de Materiales',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelFactibilidadMateriales]
        });
    }

    winFactibilidadMateriales.show();
}

function cierraVentanaPreFactibilidadRadio() {
    winPreFactibilidadRadio.close();
    winPreFactibilidadRadio.destroy();
}

function cierraVentanaFactibilidadMateriales() {
    winFactibilidadMateriales.close();
    winFactibilidadMateriales.destroy();
}

function closeVentanaIngresoFactRadio() {
    winIngresoFactibilidad.close();
    winIngresoFactibilidad.destroy();
}

function closeVentanaIngresoFactAntRadio() {
    winIngresoFactibilidadAnt.close();
    winIngresoFactibilidadAnt.destroy();
}

/**
 * getInfoPersonaContacto, obtiene la informacion del contacto
 * 
 * @author Edgar Holguin <eholguin@telconet.ec>
 * @version 1.0 17-04-2016
 * 
 * @param array arrayParametros[
 *                              strPrefijoEmpresa       => Recibe el prefijo de la empresa
 *                              strDescripcionTipoRol   => Recibe el tipo de rol
 *                              strEstadoTipoRol        => Recibe el estado de tipo de rol
 *                              strDescripcionRol       => Recibe la descripcion del rol
 *                              strEstadoIER            => Recibe el estado de la info empresa rol
 *                              strEstadoIPER           => Recibe el estado de la info persona empresa rol
 *                              strEstadoIPC            => Recibe el estado de la info persona contacto
 *                              intIdPersona            => Recibe el id de la persona
 *                              ]
 *                              
 * @returns array arrayReturn[
 *                              strNombres    => Contiene el nombre del contacto
 *                              strApellidos  => Contiene el apellido del contacto
 *                           ]
 */
function getInfoPersonaContacto(arrayParametros) {
    
    Ext.define('ContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdPersona', type: 'int'},
            {name: 'strNombres', type: 'string'},
            {name: 'strApellidos', type: 'string'},
            {name: 'intIdTitulo', type: 'int'},
            {name: 'strTitulo', type: 'string'},
            {name: 'strTitulo', type: 'string'},
            {name: 'strTipoContacto', type: 'string'},
            {name: 'strIdentificacionCliente', type: 'string'},
            {name: 'dateFeCreacion', type: 'string'},
            {name: 'strUsuarioCreacion', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUrlShow', type: 'string'},
            {name: 'strUrlEdit', type: 'string'},
            {name: 'strUrlDelet', type: 'string'}
        ]
    });

    return new Ext.create('Ext.data.Store', {
        id: arrayParametros['idStore'],
        model: 'ContactoModel',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: urlContactoClienteByTipoRolAjax,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams: {
                strDescripcionTipoRol: arrayParametros['strDescripcionTipoRol'],
                strEstadoTipoRol: arrayParametros['strEstadoTipoRol'],
                strDescripcionRol: arrayParametros['strDescripcionRol'],
                strEstadoRol: arrayParametros['strEstadoRol'],
                strEstadoIER: arrayParametros['strEstadoIER'],
                strEstadoIPER: arrayParametros['strEstadoIPER'],
                strEstadoIPC: arrayParametros['strEstadoIPC'],
                intIdPersonaEmpresaRol: arrayParametros['intIdPersonaEmpresaRol'],
                intIdPunto: arrayParametros['intIdPunto'],
                strJoinPunto: arrayParametros['strJoinPunto']
            },
            simpleSortMode: true
        }
    });
} 

/************************************************************************ */
/********************* FACTIBILIDAD MATERIALES ************************** */
/************************************************************************ */
function validarFactibilidadMateriales()
{
    var materiales = gridFactibilidadMateriales.getStore().getCount();
    if (materiales > 0)
    {
        var boolVacio = true;
        var cont = 0;
        for (var i = 0; i < gridFactibilidadMateriales.getStore().getCount(); i++)
        {
            var cantidad_estimada = parseInt(gridFactibilidadMateriales.getStore().getAt(i).data.cantidad_estimada);
            if (cantidad_estimada > 0)
            {
                break;
            }
            cont++;
        }

        if (cont == materiales)
        {
            boolVacio = false;
        }

        if (boolVacio)
        {
            var materialesJson = retornaMateriales();
            return materialesJson;
        }
        else
        {
            Ext.MessageBox.show({
                title: 'Error',
                msg: "Para solicitar excedente de Materiales por lo menos debe ingresar un excedente en un Material.",
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
            return false;
        }
    }
    else
    {
        Ext.MessageBox.show({
            title: 'Error',
            msg: "Escoja una tarea para poder Visualizar los Materiales",
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }

}

function retornaMateriales()
{
    var materiales_json = false;
    var array_materiales = new Object();
    array_materiales['total'] = gridFactibilidadMateriales.getStore().getCount();
    array_materiales['materiales'] = new Array();

    var array_data = new Array();
    for (var i = 0; i < gridFactibilidadMateriales.getStore().getCount(); i++)
    {
        array_data.push(gridFactibilidadMateriales.getStore().getAt(i).data);
    }

    array_materiales['materiales'] = array_data;
    materiales_json = Ext.JSON.encode(array_materiales);

    return materiales_json;
}
