/**
 * Función que sirve para mostrar la pantalla de asignación de recursos de red para Internet Residencial
 * 
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.0 09-11-2018
 * 
 */
function showRecursoDeRedInternetResidencial(rec)
{
    const idServicio = rec.data.id_servicio;
    winRecursoDeRedResi = "";
    formPanelRecursosDeRed = "";

    if (!winRecursoDeRedResi)
    {
        boolCargaCmbs = true;
        
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        storeElementosSplitters = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            autoLoad: boolCargaCmbs,
            listeners: {
                load: function() {
                    if(rec.data.idSplitter )
                    {
                        Ext.getCmp("cmbSplitter").setValue(rec.data.idSplitter);
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: strUrlAjaxComboElementosByPadre,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    popId: rec.data.idCaja,
                    elemento: 'SPLITTER',
                    idServicio: idServicio
                }
            },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'}
                ]
        });

        storeInterfacesBySplitter = new Ext.data.Store({
            autoLoad: boolCargaCmbs,
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: 'getJsonInterfacesByElemento',
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    idElemento: rec.get("idSplitter"),
                    interfaceSplitter: rec.data.interfaceSplitter
                }
            },
            fields:
                [
                    {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                    {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
                ]
        });


        formPanelRecursosDeRed = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [
                CamposRequeridos,
                {
                    xtype: 'panel',
                    border: false,
                    layout: {type: 'hbox', align: 'stretch'},
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
                                    fieldLabel: 'Tipo Orden',
                                    name: 'tipo_orden_servicio',
                                    id: 'tipo_orden_servicio',
                                    value: rec.get("tipo_orden"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Servicio',
                                    name: 'info_servicio',
                                    id: 'info_servicio',
                                    value: rec.get("producto"),
                                    allowBlank: false,
                                    readOnly: true,
                                    listeners: {
                                        render: function(c) {
                                            Ext.QuickTips.register({
                                                target: c.getEl(),
                                                text: rec.get("items_plan")
                                            });
                                        }
                                    }
                                }
                            ]
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: 'Datos de Recursos de Red',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Ultima Milla',
                            name: 'txt_um',
                            id: 'txt_um',
                            value: rec.get("ultimaMilla"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'OLT',
                            name: 'txt_olt',
                            id: 'txt_olt',
                            value: rec.get("pop"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'MARCA OLT',
                            name: 'txt_marca_olt',
                            id: 'txt_marca_olt',
                            value: rec.get("marcaOlt"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Linea',
                            name: 'txt_linea',
                            id: 'txt_linea',
                            value: rec.get("intElemento"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Caja',
                            name: 'txt_caja',
                            width: 450,
                            id: 'txt_caja',
                            value: rec.get("caja"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmbSplitter',
                            name: 'cmbSplitter',
                            fieldLabel: '* Splitter',
                            typeAhead: true,
                            allowBlank: false,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            selectOnTab: true,
                            width: 450,
                            store: storeElementosSplitters,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;",
                            listeners: {
                                select: {fn: function(combo, value) {
                                        Ext.getCmp('cmbInterfaceSplitter').reset();
                                        storeInterfacesBySplitter.proxy.extraParams = {idElemento: combo.getValue()};
                                        storeInterfacesBySplitter.load({params: {}});
                                    }}
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmbInterfaceSplitter',
                            name: 'cmbInterfaceSplitter',
                            fieldLabel: '* Interface',
                            width: 200,
                            typeAhead: true,
                            allowBlank: false,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreInterfaceElemento',
                            valueField: 'idInterfaceElemento',
                            selectOnTab: true,
                            store: storeInterfacesBySplitter,
                            listClass: 'x-combo-list-small',
                            emptyText: 'Seleccione',
                            labelStyle: "color:red;",
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var tipoSolicitud   = "";
                        var idDetSolPlanif  = rec.get("id_factibilidad");
                        tipoSolicitud = "planificación";
                        var idSplitter          = Ext.getCmp('cmbSplitter').value;
                        if (!idSplitter || idSplitter === "" || idSplitter === 0)
                        {
                            Ext.Msg.alert('Alerta', 'Por favor seleccione el splitter!');
                            return;
                        }
                        var idInterfaceSplitter = Ext.getCmp('cmbInterfaceSplitter').value;
                        if (!idInterfaceSplitter || idInterfaceSplitter === "" || idInterfaceSplitter === 0)
                        {
                            Ext.Msg.alert('Alerta', 'Por favor seleccione la interface!');
                            return;
                        }
                        if (!idDetSolPlanif || idDetSolPlanif == "" || idDetSolPlanif == 0)
                        {
                            Ext.Msg.alert('Alerta', 'No existe una solicitud de '+tipoSolicitud+' asociada a este servicio');
                            return;
                        }
                        
                        connRecursoDeRed.request({
                            url: strUrlGuardaRecursosRedInternetResidencial,
                            timeout: 12000000,
                            method: 'post',
                            params: {
                                        idDetSolPlanif:         idDetSolPlanif, 
                                        idSplitter:             idSplitter, 
                                        idInterfaceSplitter:    idInterfaceSplitter,
                                        marcaOlt:               rec.get("marcaOlt")
                            },
                            success: function(response) {
                                var text = response.responseText;
                                var datos = Ext.JSON.decode(response.responseText);
                                cierraVentanaRecursoDeRedInternetResidencial();
                                if (datos.status === "OK")
                                {
                                    Ext.Msg.alert('Mensaje', datos.mensaje, function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                        }
                                    });
                                }
                                else
                                {
                                    Ext.Msg.alert('Error ', datos.mensaje);
                                }
                            },
                            failure: function(result) {
                                Ext.Msg.alert('Error ', result.responseText);
                            }
                        });
                    }
                }
                , {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaRecursoDeRedInternetResidencial();
                    }
                }
            ]
        });
        
        Ext.getCmp('cmbInterfaceSplitter').setValue(rec.data.interfaceSplitter);
        winRecursoDeRedResi = Ext.widget('window', {
            title: 'Ingreso de Recursos de Red',
            layout: 'fit',
            resizable: false,
            modal: true,
            items: [formPanelRecursosDeRed]
        });
    }
    winRecursoDeRedResi.show();
}

function cierraVentanaRecursoDeRedInternetResidencial() {
    winRecursoDeRedResi.close();
    winRecursoDeRedResi.destroy();
}
