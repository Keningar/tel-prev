/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var winMenuAsignacion;
var winAsignacion;
var winAsignacionIndividual;
var winRecursoDeRed;
var gridIp;
var gridIpPublica;
var gridIpMonitoreo;
var tareasJS;

var connImportRecursosDeRed = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Cargando Recursos de Red',
                    progressText: 'loading...',
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

var connTrasladarRecursosDeRed = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
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

var connRecursoDeRed = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
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

var connValidaIp = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Validando Ip, Por favor espere!!',
                    progressText: 'validando...',
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
/************************************************************************ */
/************************* TRASLADAR RECURSO DE RED ******************** */
/************************************************************************ */
function showTrasladarRecursosDeRed(rec, id, origen)
{
    winTrasladarRecursosDeRed = "";
    formPanelTrasladarRecursosDeRed = "";

    if (!winTrasladarRecursosDeRed)
    {

        connImportRecursosDeRed.request({
            url: "importarRecursosDeRed",
            timeout: 120000,
            method: "POST",
            params: {idServicioTrasladado: rec.data.id_servicio_trasladado},
            success: function(response) {
                var datosImportados = Ext.decode(response.responseText);

                if (rec.get("tercerizadora")) {
                    itemTercerizadora = new Ext.form.TextField({
                        xtype: 'textfield',
                        fieldLabel: 'Tercerizadora',
                        name: 'fieldtercerizadora',
                        id: 'fieldtercerizadora',
                        value: rec.get("tercerizadora"),
                        allowBlank: false,
                        readOnly: true
                    });
                } else {
                    itemTercerizadora = Ext.create('Ext.Component', {
                        html: "<br>",
                    });
                }
                var storeIps = new Ext.data.Store({
                    pageSize: 50,
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url: '../../../tecnico/clientes/getIpPublicas',
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        extraParams: {
                            idServicio: rec.data.id_servicio_trasladado
                        }
                    },
                    fields:
                            [
                                {name: 'ip', mapping: 'ip'},
                                {name: 'mascara', mapping: 'mascara'},
                                {name: 'gateway', mapping: 'gateway'},
                                {name: 'tipo', mapping: 'tipo'}
                            ]
                });


                Ext.define('Ips', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'ip', mapping: 'ip'},
                        {name: 'mascara', mapping: 'mascara'},
                        {name: 'gateway', mapping: 'gateway'},
                        {name: 'tipo', mapping: 'tipo'}
                    ]
                });

                //grid de ips
                gridIps = Ext.create('Ext.grid.Panel', {
                    id: 'gridIps',
                    store: storeIps,
                    columnLines: true,
                    columns: [{
                            header: 'Tipo',
                            dataIndex: 'tipo',
                            width: 100,
                            sortable: true,
                        }, {
                            header: 'Ip',
                            dataIndex: 'ip',
                            width: 150,
                        },
                        {
                            header: 'Mascara',
                            dataIndex: 'mascara',
                            width: 150,
                        },
                        {
                            header: 'Gateway',
                            dataIndex: 'gateway',
                            width: 150,
                        }],
                    viewConfig: {
                        stripeRows: true
                    },
                    frame: true,
                    height: 200,
                    title: 'Ips del Cliente',
                });

                formPanelTrasladarRecursosDeRed = Ext.create('Ext.form.Panel', {
                    buttonAlign: 'center',
                    BodyPadding: 10,
                    bodyStyle: "background: white; padding:10px; border: 0px none;",
                    frame: true,
                    items: [
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
                                            fieldLabel: 'Servicio',
                                            name: 'info_servicio',
                                            id: 'info_servicio',
                                            value: rec.get("producto"),
                                            allowBlank: false,
                                            readOnly: true,
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Tipo Orden',
                                            name: 'tipo_orden_servicio',
                                            id: 'tipo_orden_servicio',
                                            value: rec.get("tipo_orden"),
                                            allowBlank: false,
                                            readOnly: true
                                        }, itemTercerizadora
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
                                    fieldLabel: 'Radio',
                                    name: 'txt_radio',
                                    id: 'txt_radio',
                                    value: datosImportados.elemento,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Pop',
                                    name: 'txt_pop',
                                    id: 'txt_pop',
                                    value: datosImportados.pop,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Dslam',
                                    name: 'txt_dslam',
                                    id: 'txt_dslam',
                                    value: datosImportados.elemento,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'VCI',
                                    name: 'txt_vci',
                                    id: 'txt_vci',
                                    value: datosImportados.vci,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Interface',
                                    name: 'txt_interface',
                                    id: 'txt_interface',
                                    value: datosImportados.interface,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Olt',
                                    name: 'txt_olt',
                                    id: 'txt_olt',
                                    value: datosImportados.elemento,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Linea',
                                    name: 'txt_linea',
                                    id: 'txt_linea',
                                    value: datosImportados.interface,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Caja',
                                    name: 'txt_caja',
                                    id: 'txt_caja',
                                    width: 500,
                                    value: datosImportados.caja,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Splitter',
                                    name: 'txt_splitter',
                                    id: 'txt_splitter',
                                    width: 500,
                                    value: datosImportados.splitter,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Interface',
                                    name: 'txt_interface_splitter',
                                    id: 'txt_interface_splitter',
                                    value: datosImportados.intSplitter,
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'panel',
                                    BodyPadding: 10,
                                    bodyStyle: "background: white; padding:10px; border: 0px none;",
                                    frame: true,
                                    items: [gridIps]
                                }
                            ]
                        }
                    ],
                    buttons: [
                        {
                            text: 'Guardar',
                            handler: function() {

                                var boolError = false;


                                if (!boolError)
                                {
                                    connTrasladarRecursosDeRed.request({
                                        url: "trasladarRecursosDeRed",
                                        timeout: 120000,
                                        method: 'post',
                                        params: {idDetalleSolicitud: rec.data.id_factibilidad, 
                                                 idServicio: rec.data.id_servicio, 
                                                 idServicioTrasladado: rec.data.id_servicio_trasladado,
                                                 nombreTecnico: rec.data.nombreTecnico,
                                                },
                                        success: function(response) {
                                            var text = response.responseText;
                                            if (text == "Se trasladaron correctamente los Recursos de Red")
                                            {
                                                cierraVentanaTrasladarRecursosDeRed();
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
                        }
                        , {
                            text: 'Cerrar',
                            handler: function() {
                                cierraVentanaTrasladarRecursosDeRed();
                            }
                        }
                    ]
                });

                if (rec.get('ultimaMilla') == "Radio") {
                    Ext.getCmp('txt_pop').setVisible(false);
                    Ext.getCmp('txt_dslam').setVisible(false);
                    Ext.getCmp('txt_interface').setVisible(false);
                    Ext.getCmp('txt_vci').setVisible(false);

                    Ext.getCmp('txt_olt').setVisible(false);
                    Ext.getCmp('txt_linea').setVisible(false);
                    Ext.getCmp('txt_caja').setVisible(false);
                    Ext.getCmp('txt_splitter').setVisible(false);
                    Ext.getCmp('txt_interface_splitter').setVisible(false);
                }
                if (rec.get('ultimaMilla') == "Cobre") {
                    Ext.getCmp('txt_radio').setVisible(false);

                    Ext.getCmp('txt_olt').setVisible(false);
                    Ext.getCmp('txt_linea').setVisible(false);
                    Ext.getCmp('txt_caja').setVisible(false);
                    Ext.getCmp('txt_splitter').setVisible(false);
                    Ext.getCmp('txt_interface_splitter').setVisible(false);
                }
                if (rec.get('ultimaMilla') == "Fibra Optica") {
                    Ext.getCmp('txt_radio').setVisible(false);
                    Ext.getCmp('txt_pop').setVisible(false);
                    Ext.getCmp('txt_dslam').setVisible(false);
                    Ext.getCmp('txt_interface').setVisible(false);
                    Ext.getCmp('txt_vci').setVisible(false);
                }
                winTrasladarRecursosDeRed = Ext.widget('window', {
                    title: 'Trasladar Recursos de Red',
                    layout: 'fit',
                    resizable: false,
                    modal: true,
                    items: [formPanelTrasladarRecursosDeRed]
                });

                winTrasladarRecursosDeRed.show();

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

function cierraVentanaTrasladarRecursosDeRed() {
    winTrasladarRecursosDeRed.close();
    winTrasladarRecursosDeRed.destroy();
}

/************************************************************************ */
/**************************** RECURSO DE RED ****************************** */
/************************************************************************ */

function showRecursoDeRedWifi(rec, id, origen)
{

    const idServicio = rec.data.id_servicio;
    winRecursoDeRed = "";
    formPanelRecursosDeRed = "";


    if (!winRecursoDeRed)
    {
        //******** html campos requeridos...
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
            autoLoad: true,
            listeners: {
                load: function() {
                    if (Ext.getCmp("cmb_SPLITTER")) {
                        var valor = Ext.getCmp("cmb_SPLITTER").getValue();

                        if (valor > 0) {
                        } else {
                            Ext.getCmp("cmb_SPLITTER").setValue(rec.data.splitter);
                        }
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: '../../factibilidad/factibilidad_instalacion/ajaxComboElementosByPadre',
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
            autoLoad: true,
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
                            fieldLabel: 'Switch',
                            name: 'txt_olt',
                            id: 'txt_olt',
                            value: rec.get("pop"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Marca',
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
                            fieldLabel: 'Elemento Contenedor',
                            name: 'txt_caja',
                            width: 450,
                            id: 'txt_caja',
                            value: rec.get("caja"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            id: 'cmb_SPLITTER',
                            name: 'cmb_SPLITTER',
                            fieldLabel: 'Elemento Conector',
                            typeAhead: true,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            selectOnTab: true,
                            width: 450,
                            store: storeElementosSplitters,
                            lazyRender: true,
                            readOnly: true,
                            listClass: 'x-combo-list-small',
                            listeners: {
                                select: {fn: function(combo, value) {
                                        Ext.getCmp('cmb_Interface_splitter').reset();
                                        storeInterfacesBySplitter.proxy.extraParams = {idElemento: combo.getValue()};
                                        storeInterfacesBySplitter.load({params: {}});
                                    }}
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_Interface_splitter',
                            name: 'cmb_Interface_splitter',
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
                        var id_factibilidad = rec.get("id_factibilidad");
                        var boolError = false;
                        var mensajeError = "";
                        var id_splitter = Ext.getCmp('cmb_SPLITTER').value;
                        var id_interface_splitter = Ext.getCmp('cmb_Interface_splitter').value;

                        if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                        {
                            boolError = true;
                            mensajeError += "El id del Detalle Solicitud no existe.\n";
                        } else {
                            if (rec.get('ultimaMilla') == "Radio") {

                            }
                            if (rec.get('ultimaMilla') == "Cobre") {
                                if (!id_interface || id_interface == "" || id_interface == 0)
                                {
                                    boolError = true;
                                    mensajeError += "La Interface no fue escogida, por favor seleccione.\n";
                                }
                            }

                            if (rec.get('ultimaMilla') == "Fibra Optica") {

                                if (!id_interface_splitter || id_interface_splitter == "" || id_interface_splitter == 0)
                                {
                                    boolError = true;
                                    mensajeError += "La Interface no fue escogida, por favor seleccione.\n";
                                }
                            }
                        }

                        if (!boolError)
                        {

                            var paramsRecursosRed = {
                                idSolicitud: id_factibilidad, 
                                producto: rec.data.producto,
                                servicioId: rec.data.id_servicio,
                                interfaceConectorId: id_interface_splitter
                            };

                            connRecursoDeRed.request({
                                url: url_guardaRecursosDeRedWifi,
                                timeout: 120000,
                                method: 'post',
                                timeout: 120000,
                                    params: paramsRecursosRed,
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "OK")
                                    {
                                        cierraVentanaRecursoDeRed();
                                        Ext.Msg.alert('Mensaje', 'Transacción Exitosa.', function(btn) {
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
                }
                , {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaRecursoDeRed();
                    }
                }
            ]
        });

        Ext.getCmp('cmb_Interface_splitter').setValue(rec.data.interfaceSplitter);

        winRecursoDeRed = Ext.widget('window', {
            title: 'Ingreso de Recursos de Red',
            layout: 'fit',
            resizable: false,
            modal: true,
            items: [formPanelRecursosDeRed]
        });
    }

    winRecursoDeRed.show();

}


function showRecursoDeRed(rec, id, origen)
{
    const idServicio = rec.data.id_servicio;
    winRecursoDeRed = "";
    formPanelRecursosDeRed = "";
    if (!winRecursoDeRed)
    {

        if (rec.data.nombreTecnico === 'IP') {
            boolCargaCmbs = false;
        } else {
            nroIp = rec.data.cantidadIp;
            boolCargaCmbs = true;
        }


        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        Ext.define('tipoCaracteristica', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'tipo', type: 'string'}
            ]
        });

        if (rec.data.nombreTecnico == "IP") {
            var comboCaracteristica = new Ext.data.Store({
                model: 'tipoCaracteristica',
                data: [
                    {tipo: 'PUBLICA'}
                ]
            });
        } else {
            if (rec.data.tieneIp) {
                var comboCaracteristica = new Ext.data.Store({
                    model: 'tipoCaracteristica',
                    data: [
                        {tipo: 'MONITOREO'},
                        {tipo: 'WAN'},
                        {tipo: 'LAN'},
                        {tipo: 'PUBLICA'}
                    ]
                });
            } else {
                var comboCaracteristica = new Ext.data.Store({
                    model: 'tipoCaracteristica',
                    data: [
                        {tipo: 'MONITOREO'},
                        {tipo: 'WAN'},
                        {tipo: 'LAN'}
                    ]
                });
            }

        }

        storeElementosRadio = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            listeners: {
                load: function() {
                    if (Ext.getCmp("cmb_RADIO")) {
                        var valor = Ext.getCmp("cmb_RADIO").getValue();

                        if (valor > 0) {
                        } else {
                            Ext.getCmp("cmb_RADIO").setValue(rec.data.radio);
                        }
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: '../../factibilidad/factibilidad_instalacion/ajaxComboElementos',
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
                    nombre: this.nombreElemento,
                    estado: 'ACTIVE',
                    elemento: 'RADIO'
                }
            },
            fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ],
            autoLoad: boolCargaCmbs
        });

        storeElementos = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            autoLoad: boolCargaCmbs,
            listeners: {
                load: function() {
                    if (rec.data.idPop) {
                        if (Ext.getCmp("cmb_POP")) {
                            Ext.getCmp("cmb_POP").setValue(rec.data.pop);
                            storeElementosByPadre2.proxy.extraParams = {
                                popId: rec.data.idPop, 
                                elemento: 'DSLAM',
                                idServicio: idServicio
                            };
                            storeElementosByPadre2.load({params: {}});
                        }
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: '../../factibilidad/factibilidad_instalacion/ajaxComboElementos',
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
                    nombre: this.nombreElemento,
                    estado: 'ACTIVE',
                    elemento: 'POP'
                }
            },
            fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ],
        });

        storeElementosSplitters = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            autoLoad: boolCargaCmbs,
            listeners: {
                load: function() {
                    if (Ext.getCmp("cmb_SPLITTER")) {
                        var valor = Ext.getCmp("cmb_SPLITTER").getValue();

                        if (valor > 0) {
                        } else {
                            Ext.getCmp("cmb_SPLITTER").setValue(rec.data.splitter);
                        }
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: '../../factibilidad/factibilidad_instalacion/ajaxComboElementosByPadre',
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
        storeElementosByPadre2 = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            listeners: {
                load: function() {
                    var valor = Ext.getCmp("cmb_POP").getValue();

                    if (valor > 0) {
                    } else {
                        Ext.getCmp("cmb_DSLAM").setValue(rec.data.dslam);
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: '../../factibilidad/factibilidad_instalacion/ajaxComboElementosByPadre',
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
                    popId: '',
                    elemento: 'DSLAM'
                }
            },
            fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
        });

        //dms
        if (rec.data.cantidadIp !== 0 && prefijoEmpresa == "MD" && rec.data.ultimaMilla == "Fibra Optica") 
        {
            var nroIp = 0;
            var plan = 0;
            if (rec.data.nombreTecnico === 'IP') {
                nroIp = rec.data.cantidad;
            } else {
                nroIp = rec.data.cantidadIp;
                plan = rec.data.idPlan;
            }

            var storeIps = new Ext.data.Store({
                id: 'idPoolStore',
                total: 'total',
                pageSize: 10,
                autoLoad: true,
                listeners: {
                    'load': function(store, records, successful) {
                        if (successful) {
                            if (store.getProxy().getReader().rawData.error) {
                                Ext.Msg.show({
                                    title: 'Importante',
                                    msg: store.getProxy().getReader().rawData.error,
                                    width: 300,
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                            else if (store.getProxy().getReader().rawData.faltantes) {
                                if (store.getProxy().getReader().rawData.faltantes !== 0) {
                                    Ext.Msg.show({
                                        title: 'Importante',
                                        msg: 'No se encontraron disponibles el número de ips requeridas. <br /> Ips faltantes: '
                                                + store.getProxy().getReader().rawData.faltantes + '<br /> Por favor solicitar a GEPON crear un nuevo pool de ip.',
                                        width: 300,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        }
                    }
                },
                proxy: {
                    type: 'ajax',
                    url: nroIp + '/' + rec.data.elementoId + '/' + rec.data.id_servicio + '/' + rec.data.id_punto + '/' + rec.data.esPlan + 
                         '/' + plan + '/' + rec.data.marcaOlt + '/getips',
                    timeout: 300000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'ips',
                        messageProperty: 'message'
                    }
                },
                fields:
                        [
                            {name: 'ip', mapping: 'ip'},
                            {name: 'mascara', mapping: 'mascara'},
                            {name: 'gateway', mapping: 'gateway'},
                            {name: 'tipo', mapping: 'tipo'},
                            {name: 'scope', mapping: 'scope'}
                        ]
            });

        } 
        else 
        {//dms

            var storeIps = new Ext.data.Store({
                fields:
                        [
                            {name: 'ip', mapping: 'ip'},
                            {name: 'mascara', mapping: 'mascara'},
                            {name: 'gateway', mapping: 'gateway'},
                            {name: 'tipo', mapping: 'tipo'},
                            {name: 'scope', mapping: 'scope'}
                        ]
            });

        }

        Ext.define('Ips', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'ip', mapping: 'ip'},
                {name: 'mascara', mapping: 'mascara'},
                {name: 'gateway', mapping: 'gateway'},
                {name: 'tipo', mapping: 'tipo'}
            ]
        });

        var cellEditingIps = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, object) {
                    var rowIdx = object.rowIdx;
                    var column = object.field;
                    var currentIp = object.value;
                    var storeIps = gridIps.getStore().getAt(rowIdx);

                    if (typeof storeIps != 'undefined') {
                        var tipo = storeIps.get('tipo');

                        if (column == "ip" && tipo != 'LAN' && tipo != 'MONITOREO') {
                            if (esIpValida(currentIp)) {
                                if (!existeRecordIp(rowIdx, currentIp))
                                {
                                    connValidaIp.request({
                                        url: "validarIp",
                                        timeout: 120000,
                                        method: 'post',
                                        params: {tipo: tipo, ip: currentIp, idServicio: rec.data.id_servicio},
                                        success: function(response) {
                                            var text = response.responseText;

                                            if (text == "No existe Ip")
                                            {

                                            }
                                            else {
                                                cierraVentanaRecursosDeRed();
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: text,
                                                    buttons: Ext.MessageBox.OK,
                                                    icon: Ext.MessageBox.ERROR,
                                                    fn: function(btn, text) {
                                                        muestraVentanaRecursosDeRed();
                                                        eliminarSeleccion(gridIps);
                                                    }
                                                });
                                            }
                                        },
                                        failure: function(result) {
                                            cierraVentanaRecursosDeRed();
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: result.responseText,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR,
                                                fn: function(btn, text) {
                                                    muestraVentanaRecursosDeRed();
                                                    eliminarSeleccion(gridIps);
                                                }
                                            });

                                        }
                                    });
                                }
                                else
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Ip ya ingresada. Por favor ingrese una distinta.",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                    eliminarSeleccion(gridIps);
                                }
                            } else {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "Ingrese una Ip valida",
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                                eliminarSeleccion(gridIps);
                            }
                        }
                        // refresh summaries
                        gridIps.getView().refresh();
                    }
                }
            }
        });

        var selIps = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) {
                    gridIps.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        });

        if (prefijoEmpresa == "MD" || prefijoEmpresa == "EN") {

            if (rec.data.ultimaMilla == "Fibra Optica") 
            {
                if (rec.data.tieneIp) 
                {
                    //grid de ips
                    gridIps = Ext.create('Ext.grid.Panel', {
                        id: 'gridIps',
                        store: storeIps,
                        columnLines: true,
                        columns: [{
                                header: 'Tipo',
                                dataIndex: 'tipo',
                                width: 100,
                                sortable: true,
                                editor: {
                                    queryMode: 'local',
                                    xtype: 'combobox',
                                    displayField: 'tipo',
                                    valueField: 'tipo',
                                    loadingText: 'Buscando...',
                                    store: comboCaracteristica
                                }
                            }, {
                                header: 'Ip',
                                dataIndex: 'ip',
                                width: 150,
                                editor: {
                                    id: 'ip',
                                    name: 'ip',
                                    xtype: 'textfield',
                                    valueField: ''
                                }
                            },
                            {
                                header: 'Mascara',
                                dataIndex: 'mascara',
                                width: 150,
                                editor: {
                                    id: 'mascara',
                                    name: 'mascara',
                                    xtype: 'textfield',
                                    valueField: ''
                                }
                            },
                            {
                                header: 'Gateway',
                                dataIndex: 'gateway',
                                width: 150,
                                editor: {
                                    id: 'gateway',
                                    name: 'gateway',
                                    xtype: 'textfield',
                                    valueField: ''
                                }
                            },
                            {
                                header: 'Scope',
                                dataIndex: 'scope',
                                hidden: true,
                                hideable: false
                            }],
                        viewConfig: {
                            stripeRows: true
                        },
                        frame: true,
                        height: 200,
                        title: 'Ips del Cliente'
                    });


                } 
                else
                {

                    gridIps = Ext.create('Ext.Component', {
                        html: "<br>",
                    });
                }

            } 
            else 
            {
                //grid de ips
                gridIps = Ext.create('Ext.grid.Panel', {
                    id: 'gridIps',
                    store: storeIps,
                    columnLines: true,
                    columns: [{
                            header: 'Tipo',
                            dataIndex: 'tipo',
                            width: 100,
                            sortable: true,
                            editor: {
                                queryMode: 'local',
                                xtype: 'combobox',
                                displayField: 'tipo',
                                valueField: 'tipo',
                                loadingText: 'Buscando...',
                                store: comboCaracteristica
                            }
                        }, {
                            header: 'Ip',
                            dataIndex: 'ip',
                            width: 150,
                            editor: {
                                id: 'ip',
                                name: 'ip',
                                xtype: 'textfield',
                                valueField: ''
                            }
                        },
                        {
                            header: 'Mascara',
                            dataIndex: 'mascara',
                            width: 150,
                            editor: {
                                id: 'mascara',
                                name: 'mascara',
                                xtype: 'textfield',
                                valueField: ''
                            }
                        },
                        {
                            header: 'Gateway',
                            dataIndex: 'gateway',
                            width: 150,
                            editor: {
                                id: 'gateway',
                                name: 'gateway',
                                xtype: 'textfield',
                                valueField: ''
                            }
                        },
                        {
                            header: 'Scope',
                            dataIndex: 'scope',
                            hidden: true,
                            hideable: false
                        }],
                    selModel: selIps,
                    viewConfig: {
                        stripeRows: true
                    },
                    // inline buttons
                    dockedItems: [{
                            xtype: 'toolbar',
                            items: [{
                                    itemId: 'removeButton',
                                    text: 'Eliminar',
                                    tooltip: 'Elimina el item seleccionado',
                                    iconCls: 'remove',
                                    disabled: true,
                                    handler: function() {
                                        eliminarSeleccion(gridIps);
                                    }
                                }, '-', {
                                    text: 'Agregar',
                                    tooltip: 'Agrega un item a la lista',
                                    iconCls: 'add',
                                    handler: function() {
                                        // Create a model instance
                                        var r = Ext.create('Ips', {
                                            ip: '',
                                            mascara: '',
                                            gateway: '',
                                            tipo: ''

                                        });
                                        if (!existeRecordIp(1000, ''))
                                        {
                                            storeIps.insert(0, r);
                                            cellEditingIps.startEditByPosition({row: 0, column: 1});
                                        }
                                        else
                                        {
                                            alert('Ya existe un registro vacio.');
                                        }
                                    }
                                }]
                        }],
                    frame: true,
                    height: 200,
                    title: 'Ips del Cliente',
                    plugins: [cellEditingIps]
                });

            }
        }
        else if(prefijoEmpresa == "TNP")
        {
            gridIps = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }

        Ext.define('IpPublica', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'ipPublica', mapping: 'ipPublica'}
            ]
        });

        Ext.define('IpMonitoreo', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'ipMonitoreo', mapping: 'ipMonitoreo'}
            ]
        });


        storeIpsPublicas = Ext.create('Ext.data.Store', {
            model: 'IpPublica',
        });

        storeIpsMonitoreo = Ext.create('Ext.data.Store', {
            model: 'IpMonitoreo',
        });

        var cellEditingIpPublica = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, object) {
                    // refresh summaries
                    var rowIdx = object.rowIdx;
                    var currentIpPublica = object.value;
                    if (esIpValida(currentIpPublica)) {
                        if (!existeRecordIpPublica(rowIdx, currentIpPublica, gridIpPublica))
                        {
                            $('input[name="ipPublica_text"]').val('');
                            $('input[name="ipPublica_text"]').val(currentIpPublica);
                        }
                        else
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: "Ip ya existente. Por favor ingrese otra.",
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                            eliminarSeleccion(gridIpPublica);
                        }
                    } else {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: "Ingrese una Ip valida",
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        eliminarSeleccion(gridIpPublica);
                    }
                }
            }
        });

        var selIpsPublicas = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) {
                    gridIpPublica.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        });

        var cellEditingIpMonitoreo = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, object) {
                    // refresh summaries
                    var rowIdx = object.rowIdx;
                    var currentIpMonitoreo = object.value;
                    if (esIpValida(currentIpMonitoreo)) {
                        if (!existeRecordIpMonitoreo(rowIdx, currentIpMonitoreo, gridIpMonitoreo))
                        {
                            $('input[name="ipMonitoreo_text"]').val('');
                            $('input[name="ipMonitoreo_text"]').val(currentIpMonitoreo);
                        }
                        else
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: "Ip ya existente. Por favor ingrese otra.",
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                            eliminarSeleccion(gridIpMonitoreo);
                        }
                    } else {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: "Ingrese una Ip valida",
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        eliminarSeleccion(gridIpMonitoreo);
                    }
                }
            }
        });

        var selIpsMonitoreo = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) {
                    gridIpMonitoreo.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        });

        storeInterfacesByElemento = new Ext.data.Store({
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
                    idElemento: rec.get("elementoId")
                }
            },
            fields:
                    [
                        {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                        {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
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

        //grid Ip Publica
        gridIpPublica = Ext.create('Ext.grid.Panel', {
            id: '	gridIpPublica',
            store: storeIpsPublicas,
            columnLines: true,
            columns: [{
                    id: 'ipPublica',
                    header: 'Ip Publica',
                    dataIndex: 'ipPublica',
                    width: 290,
                    editor: {
                        id: 'ipPublica_text',
                        name: 'ipPublica_text',
                        xtype: 'textfield',
                    }
                }],
            selModel: selIpsPublicas,
            viewConfig: {
                stripeRows: true
            },
            // inline buttons
            dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                            text: 'Agregar',
                            tooltip: 'Agrega un item a la lista',
                            iconCls: 'add',
                            handler: function() {
                                if (!existeRecordIpPublica("", "", gridIpPublica))
                                {
                                    // Create a model instance
                                    var r = Ext.create('IpPublica', {
                                        ipPublica: ''
                                    });
                                    storeIpsPublicas.insert(0, r);
                                }
                                else
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Ya existe un registro vacio para que sea llenado.",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        }, '-', {
                            itemId: 'removeButton',
                            text: 'Eliminar',
                            tooltip: 'Elimina el item seleccionado',
                            iconCls: 'remove',
                            disabled: true,
                            handler: function() {
                                eliminarSeleccion(gridIpPublica);
                            }
                        }]
                }],
            frame: true,
            title: 'Ips Publicas',
            plugins: [cellEditingIpPublica]
        });

        function existeRecordIp(rowIdx, currentIp)
        {
            var existe = false;
            var num = gridIps.getStore().getCount();

            for (var i = 0; i < num; i++)
            {
                if (rowIdx != i) {
                    var ipGrid = gridIps.getStore().getAt(i).get('ip');

                    if ((ipGrid == currentIp))
                    {
                        existe = true;
                        break;
                    }
                }
            }
            return existe;
        }

        function existeRecordIpPublica(rowIdx, ip, grid)
        {
            var existe = false;
            var num = grid.getStore().getCount();

            for (var i = 0; i < num; i++)
            {
                if (i != rowIdx) {
                    var ipPublica = grid.getStore().getAt(i).get('ipPublica');

                    if ((ipPublica == ip))
                    {
                        existe = true;
                        break;
                    }
                }
            }
            return existe;
        }

        //grid Ip Monitoreo
        gridIpMonitoreo = Ext.create('Ext.grid.Panel', {
            id: '	gridIpMonitoreo',
            store: storeIpsMonitoreo,
            columnLines: true,
            columns: [{
                    id: 'ipMonitoreo',
                    header: 'Ip Monitoreo',
                    dataIndex: 'ipMonitoreo',
                    width: 290,
                    editor: {
                        id: 'ipMonitoreo_cmp',
                        xtype: 'textfield',
                    }
                }],
            selModel: selIpsMonitoreo,
            viewConfig: {
                stripeRows: true
            },
            // inline buttons
            dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                            text: 'Agregar',
                            tooltip: 'Agrega un item a la lista',
                            iconCls: 'add',
                            handler: function() {
                                if (!existeRecordIpMonitoreo("", "", gridIpMonitoreo))
                                {
                                    // Create a model instance
                                    var r = Ext.create('IpMonitoreo', {
                                        ipPublica: ''
                                    });
                                    storeIpsMonitoreo.insert(0, r);
                                }
                                else
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Ya existe un registro vacio para que sea llenado.",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        }, '-', {
                            itemId: 'removeButton',
                            text: 'Eliminar',
                            tooltip: 'Elimina el item seleccionado',
                            iconCls: 'remove',
                            disabled: true,
                            handler: function() {
                                eliminarSeleccion(gridIpMonitoreo);
                            }
                        }]
                }],
            frame: true,
            title: 'Ips Monitoreo',
            plugins: [cellEditingIpMonitoreo]
        });

        function existeRecordIpMonitoreo(rowIdx, ip, grid)
        {
            var existe = false;
            var num = grid.getStore().getCount();

            for (var i = 0; i < num; i++)
            {
                if (i != rowIdx) {
                    var ipMonitoreo = grid.getStore().getAt(i).get('ipMonitoreo');

                    if ((ipMonitoreo == ip))
                    {
                        existe = true;
                        break;
                    }
                }
            }
            return existe;
        }

        function eliminarSeleccion(datosSelect)
        {
            for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
            {
                datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
            }
        }

        function obtenerDatosIps()
        {
            if (gridIps.getStore().getCount() >= 1) {
                var array_relaciones = new Object();
                array_relaciones['total'] = gridIps.getStore().getCount();
                array_relaciones['caracteristicas'] = new Array();
                var array_data = new Array();
                for (var i = 0; i < gridIps.getStore().getCount(); i++)
                {
                    array_data.push(gridIps.getStore().getAt(i).data);
                }
                array_relaciones['caracteristicas'] = array_data;
                return Ext.JSON.encode(array_relaciones);
            } else {
                return "";
            }
        }

        function obtenerDatosIpsPublicas(cantidad, tipo)
        {
            if (gridIps.getStore().getCount() >= 1) {
                var array_relaciones = new Object();
                array_relaciones['total'] = gridIps.getStore().getCount();
                array_relaciones['caracteristicas'] = new Array();

                var array_data = new Array();
                var numIps = 0;

                for (var i = 0; i < gridIps.getStore().getCount(); i++)
                {
                    array_data.push(gridIps.getStore().getAt(i).data);
                    if (gridIps.getStore().getAt(i).data.tipo == tipo)
                        numIps++;
                }

                if (numIps > cantidad || numIps < cantidad) {
                    return "";
                }

                array_relaciones['caracteristicas'] = array_data;
                return Ext.JSON.encode(array_relaciones);
            } else {
                return "";
            }
        }

        function obtenerIpsPublicas()
        {
            if (gridIpPublica.getStore().getCount() >= 1) {
                var ips = "";
                for (var i = 0; i < gridIpPublica.getStore().getCount(); i++)
                {
                    if (i == 0) {
                        ips = gridIpPublica.getStore().getAt(i).data.ipPublica;
                    } else {
                        ips = ips + "@" + gridIpPublica.getStore().getAt(i).data.ipPublica;
                    }
                }
                return ips;
            } else {
                return "";
            }
        }
        function obtenerIpsMonitoreo()
        {
            if (gridIpMonitoreo.getStore().getCount() >= 1) {
                var ips = "";
                for (var i = 0; i < gridIpMonitoreo.getStore().getCount(); i++)
                {
                    if (i == 0) {
                        ips = gridIpMonitoreo.getStore().getAt(i).data.ipMonitoreo;
                    } else {
                        ips = ips + "@" + gridIpMonitoreo.getStore().getAt(i).data.ipMonitoreo;
                    }
                }
                return ips;
            } else {
                return "";
            }
        }
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }

        if (rec.get("esPlan") == "si") {
            storeItemsPlan = new Ext.data.Store({
                pageSize: 14,
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url: 'ajaxGetDetallePlan',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'listado'
                    },
                    actionMethods: {
                        create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                    },
                    extraParams: {
                        planId: rec.get("idPlan")
                    }
                },
                fields:
                        [
                            {name: 'nombreProducto', mapping: 'nombreProducto'},
                            {name: 'cantidad', mapping: 'cantidad'},
                        ],
                autoLoad: true
            });
            //grid items plan
            gridItemsPlan = Ext.create('Ext.grid.Panel', {
                id: 'gridItemsPlan',
                store: storeItemsPlan,
                height: 150,
                width: 350,
                columnLines: true,
                columns: [new Ext.grid.RowNumberer(),
                    {
                        header: 'Producto',
                        dataIndex: 'nombreProducto',
                        width: 215
                    }, {
                        header: 'Cantidad',
                        dataIndex: 'cantidad',
                    }

                ],
                frame: true,
                title: 'Items Plan',
            });
        } else {
            gridItemsPlan = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }

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
                                }, itemTercerizadora,
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
                                }, gridItemsPlan

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
                            fieldLabel: 'Cantidad',
                            name: 'txt_cantidad_ip',
                            id: 'txt_cantidad_ip',
                            value: rec.get("cantidad"),
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
                            id: 'cmb_RADIO',
                            name: 'cmb_RADIO',
                            fieldLabel: '* RADIO',
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
                            emptyText: 'Seleecione..',
                            minChars: 3,
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_POP',
                            name: 'cmb_POP',
                            fieldLabel: '* POP',
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
                            minChars: 3,
                            listeners: {
                                select: {fn: function(combo, value) {
                                        Ext.getCmp('cmb_DSLAM').reset();

                                        storeElementosByPadre2.proxy.extraParams = {popId: combo.getValue(), elemento: 'DSLAM'};
                                        storeElementosByPadre2.load({params: {}});
                                    }},
                                change: {fn: function(combo, newValue, oldValue) {
                                        if (combo) {
                                            if (combo.getValue() > 0) {
                                            } else {
                                                if (combo.getValue().match(/[a-zA-Z]/)) {
                                                    storeElementos.proxy.extraParams = {nombre: combo.getValue()};
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
                            fieldLabel: '* DSLAM',
                            typeAhead: true,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            selectOnTab: true,
                            store: storeElementosByPadre2,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;",
                            listeners: {
                                select: {fn: function(combo, value) {
                                        Ext.getCmp('cmb_Interface').reset();
                                        storeInterfacesByElemento.proxy.extraParams = {idElemento: combo.getValue()};
                                        storeInterfacesByElemento.load({params: {}});
                                    }}
                            }
                        }, {
                            xtype: 'combobox',
                            id: 'cmb_SPLITTER',
                            name: 'cmb_SPLITTER',
                            fieldLabel: '* Splitter',
                            typeAhead: true,
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
                                        Ext.getCmp('cmb_Interface_splitter').reset();
                                        storeInterfacesBySplitter.proxy.extraParams = {idElemento: combo.getValue()};
                                        storeInterfacesBySplitter.load({params: {}});
                                    }}
                            }
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: '* VCI',
                            name: 'vci',
                            id: 'vci',
                            allowNegative: false,
                            value: 1,
                            width: 200,
                            emptyText: 'Ingrese un numero',
                            labelStyle: "color:red;",
                            validator: function(val) {
                                if (!Ext.isEmpty(val)) {
                                    if (val >= 1 && val <= 100)
                                        return true;
                                    else {
                                        Ext.getCmp('vci').setValue("1");
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: "VCI debe ser entre 1 y 100",
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                        return "VCI debe ser entre 1 y 100";
                                    }
                                } else {
                                    Ext.getCmp('vci').setValue("1");
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "VCI debe ser entre 1 y 100",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                    return "VCI debe ser entre 1 y 100";
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_Interface_splitter',
                            name: 'cmb_Interface_splitter',
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
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_Interface',
                            name: 'cmb_Interface',
                            fieldLabel: '* Interface',
                            width: 200,
                            typeAhead: true,
                            allowBlank: false,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreInterfaceElemento',
                            valueField: 'idInterfaceElemento',
                            selectOnTab: true,
                            store: storeInterfacesByElemento,
                            listClass: 'x-combo-list-small',
                            emptyText: 'Seleccione',
                            labelStyle: "color:red;",
                        }, {
                            xtype: 'panel',
                            BodyPadding: 10,
                            bodyStyle: "background: white; padding:10px; border: 0px none;",
                            frame: true,
                            items: [gridIps]
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        var id_factibilidad = rec.get("id_factibilidad");
                        var boolError = false;
                        var mensajeError = "";
                        var tipoIp = "";
                        var datosIps = "";
                        if (prefijoEmpresa == "MD" || prefijoEmpresa == "TNP")
                        {
                            if (rec.get('ultimaMilla') == "Fibra Optica")
                            {
                                tipoIp = "FIJA";
                            }
                            else
                            {    
                                tipoIp = "PUBLICA";
                            }
                        }
                        if (rec.data.nombreTecnico == "IP") {
                            var datosIps = obtenerDatosIpsPublicas(rec.data.cantidad, tipoIp);

                            if (datosIps == "") {
                                boolError = true;
                                mensajeError = "Favor ingresar el numero de Ips requeridos: " + rec.data.cantidad;
                            } else {
                                var paramsRecursosRed = {id: id_factibilidad, nombreTecnico: rec.data.nombreTecnico, 
                                                         producto: rec.data.producto, datosIps: datosIps, 
                                                         marcaOlt: rec.get("marcaOlt"), esPlan: rec.data.esPlan
                                                        };
                            }
                        } else {
                            var vci = $('input[name="vci"]').val();
                            var id_elemento = Ext.getCmp('cmb_DSLAM').value;
                            var id_interface = Ext.getCmp('cmb_Interface').value;
                            var id_splitter = Ext.getCmp('cmb_SPLITTER').value;
                            var id_interface_splitter = Ext.getCmp('cmb_Interface_splitter').value;

                            if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                            {
                                boolError = true;
                                mensajeError += "El id del Detalle Solicitud no existe.\n";
                            } else {
                                if (rec.get('ultimaMilla') == "Radio") {

                                }
                                if (rec.get('ultimaMilla') == "Cobre") {
                                    if (!id_interface || id_interface == "" || id_interface == 0)
                                    {
                                        boolError = true;
                                        mensajeError += "La Interface no fue escogida, por favor seleccione.\n";
                                    }
                                }

                                if (rec.get('ultimaMilla') == "Fibra Optica") {

                                    if (!id_interface_splitter || id_interface_splitter == "" || id_interface_splitter == 0)
                                    {
                                        boolError = true;
                                        mensajeError += "La Interface no fue escogida, por favor seleccione.\n";
                                    }
                                }

                            }

                            if (!boolError) {
                                if (rec.data.tieneIp) {
                                    var datosIps = obtenerDatosIpsPublicas(rec.data.cantidadIp, tipoIp);

                                    if (datosIps == "") {
                                        boolError = true;
                                        mensajeError = "Favor ingresar el numero de Ips Publicas requeridas: " + rec.data.cantidad;
                                    }

                                } else {
                                    if (prefijoEmpresa == "MD" && rec.get('ultimaMilla') != "Fibra Optica")
                                    {
                                        var datosIps = obtenerDatosIps();
                                    }
                                }

                                if (!boolError) {
                                    var paramsRecursosRed = {id: id_factibilidad, producto: rec.data.producto, 
                                                             splitter_id: id_splitter, interface_splitter_id: id_interface_splitter, 
                                                             elemento_id: id_elemento, interface_id: id_interface, vci: vci, 
                                                             datosIps: datosIps, marcaOlt: rec.get("marcaOlt"),
                                                             esPlan: rec.data.esPlan
                                                            };
                                }
                            }
                        }

                        if (!boolError)
                        {
                            connRecursoDeRed.request({
                                url: "guardaRecursosDeRed",
                                timeout: 120000,
                                method: 'post',
                                timeout: 120000,
                                        params: paramsRecursosRed,
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "Se guardo correctamente los Recursos de Red")
                                    {
                                        cierraVentanaRecursoDeRed();
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
                }
                , {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaRecursoDeRed();
                    }
                }
            ]
        });
        Ext.getCmp('cmb_Interface_splitter').setValue(rec.data.interfaceSplitter);

        if (rec.data.nombreTecnico == "IP") {
            Ext.getCmp('cmb_POP').setVisible(false);
            Ext.getCmp('cmb_DSLAM').setVisible(false);
            Ext.getCmp('cmb_Interface').setVisible(false);
            Ext.getCmp('vci').setVisible(false);
            Ext.getCmp('cmb_RADIO').setVisible(false);

            Ext.getCmp('txt_caja').setVisible(false);
            Ext.getCmp('txt_olt').setVisible(false);
            Ext.getCmp('txt_marca_olt').setVisible(false);
            Ext.getCmp('txt_linea').setVisible(false);
            Ext.getCmp('cmb_SPLITTER').setVisible(false);
            Ext.getCmp('cmb_Interface_splitter').setVisible(false);
        } else {
            Ext.getCmp('txt_cantidad_ip').setVisible(false);
            if (rec.get('ultimaMilla') == "Radio") {
                Ext.getCmp('cmb_POP').setVisible(false);
                Ext.getCmp('cmb_DSLAM').setVisible(false);
                Ext.getCmp('cmb_Interface').setVisible(false);
                Ext.getCmp('vci').setVisible(false);
                Ext.getCmp('txt_caja').setVisible(false);
                Ext.getCmp('txt_olt').setVisible(false);
                Ext.getCmp('txt_linea').setVisible(false);
                Ext.getCmp('cmb_SPLITTER').setVisible(false);
                Ext.getCmp('cmb_Interface_splitter').setVisible(false);
            }
            if (rec.get('ultimaMilla') == "Cobre") {
                Ext.getCmp('cmb_RADIO').setVisible(false);
                Ext.getCmp('txt_caja').setVisible(false);
                Ext.getCmp('txt_olt').setVisible(false);
                Ext.getCmp('txt_linea').setVisible(false);
                Ext.getCmp('cmb_SPLITTER').setVisible(false);
                Ext.getCmp('cmb_Interface_splitter').setVisible(false);
            }
            if (rec.get('ultimaMilla') == "Fibra Optica") {
                Ext.getCmp('cmb_POP').setVisible(false);
                Ext.getCmp('cmb_Interface').setVisible(false);
                Ext.getCmp('cmb_DSLAM').setVisible(false);
                Ext.getCmp('vci').setVisible(false);
                Ext.getCmp('cmb_RADIO').setVisible(false);
            }
        }

        winRecursoDeRed = Ext.widget('window', {
            title: 'Ingreso de Recursos de Red',
            layout: 'fit',
            resizable: false,
            modal: true,
            items: [formPanelRecursosDeRed]
        });
    }

    winRecursoDeRed.show();
}

function muestraVentanaRecursosDeRed() {
    winRecursoDeRed.show();
}
function cierraVentanaRecursosDeRed() {
    winRecursoDeRed.hide();
}
function cierraVentanaRecursoDeRed() {
    winRecursoDeRed.close();
    winRecursoDeRed.destroy();
}

function esIpValida(ip) {
    var RegExPattern = /^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/;
    ;
    if ((ip.match(RegExPattern)) && (ip != '')) {
        return true;
    } else {
        return false;
    }
}

/************************************************************************ */
/*********************** ASIGNACION RESPONSABLE ************************* */
/************************************************************************ */
function showMenuAsignacion(origen, id, panelAsignados)
{
    winMenuAsignacion = "";
    formPanelMenuAsignacion = "";

    if (!winMenuAsignacion)
    {
        //******** html vacio...
        var iniHtmlVacio1 = '';
        Vacio1 = Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            width: 300,
            padding: 4,
            layout: 'anchor',
            style: {color: '#000000'}
        });

        //******** HtmlDesc
        var iniHtml = 'Por favor escoja algun boton';
        HtmlDesc = Ext.create('Ext.Component', {
            html: iniHtml,
            width: 300,
            padding: 10,
            style: {color: '#000000'}
        });

        //******** html vacio...
        var iniHtmlVacio = '';
        Vacio = Ext.create('Ext.Component', {
            html: iniHtmlVacio,
            width: 300,
            padding: 8,
            layout: 'anchor',
            style: {color: '#000000'}
        });

        formPanelMenuAsignacion = Ext.create('Ext.form.Panel', {
            width: 380,
            height: 150,
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [Vacio1, HtmlDesc, Vacio1, Vacio],
            buttons: [
                {
                    text: 'Asignacion Global',
                    handler: function() {
                        cierraVentanaMenuAsignacion();
                        showAsignacion(origen, id, panelAsignados);
                    }
                },
                {
                    text: 'Asignacion Individual',
                    handler: function() {
                        cierraVentanaMenuAsignacion();
                        showAsignacionIndividual(origen, id, panelAsignados);
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaMenuAsignacion();
                    }
                }
            ]
        });

        winMenuAsignacion = Ext.widget('window', {
            title: 'Menu Asignacion',
            width: 400,
            height: 170,
            minHeight: 170,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelMenuAsignacion]
        });
    }

    winMenuAsignacion.show();
}

function cierraVentanaMenuAsignacion() {
    winMenuAsignacion.close();
    winMenuAsignacion.destroy();
}

/************************************************************************ */
/*********************** ASIGNACION RESPONSABLE ************************* */
/************************************************************************ */
function showAsignacion(origen, id, panelAsignados)
{
    winAsignacion = "";
    formPanelAsignacion = "";

    if (!winAsignacion)
    {
        //******** html vacio...
        var iniHtmlVacio1 = '';
        Vacio1 = Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            width: 600,
            padding: 4,
            layout: 'anchor',
            style: {color: '#000000'}
        });

        var i = 1;

        //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
        var iniHtml = '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" checked="" value="empleado" name="tipoResponsable_' + i + '">&nbsp;Empleado' +
                '&nbsp;&nbsp;' +
                '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="cuadrilla" name="tipoResponsable_' + i + '">&nbsp;Cuadrilla' +
                '&nbsp;&nbsp;' +
                '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="empresaExterna" name="tipoResponsable_' + i + '">&nbsp;Contratista' +
                '';
        RadiosTiposResponsable = Ext.create('Ext.Component', {
            html: iniHtml,
            width: 600,
            padding: 10,
            style: {color: '#000000'}
        });

        // **************** EMPLEADOS ******************
        Ext.define('EmpleadosList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_empleado', type: 'int'},
                {name: 'nombre_empleado', type: 'string'}
            ]
        });
        eval("var storeEmpleados_" + i + "= Ext.create('Ext.data.Store', { " +
                "  id: 'storeEmpleados_" + i + "', " +
                "  model: 'EmpleadosList', " +
                "  autoLoad: false, " +
                " proxy: { " +
                "   type: 'ajax'," +
                "    url : '../../planificar/asignar_responsable/getEmpleados'," +
                "   reader: {" +
                "        type: 'json'," +
                "       totalProperty: 'total'," +
                "        root: 'encontrados'" +
                "  }" +
                "  }" +
                " });    ");
        combo_empleados = new Ext.form.ComboBox({
            id: 'cmb_empleado_' + i,
            name: 'cmb_empleado_' + i,
            fieldLabel: "Empleados",
            anchor: '100%',
            queryMode: 'remote',
            width: 300,
            emptyText: 'Seleccione Empleado',
            store: eval("storeEmpleados_" + i),
            displayField: 'nombre_empleado',
            valueField: 'id_empleado',
            layout: 'anchor',
            disabled: false
        });


        // ****************  EMPRESA EXTERNA  ******************
        Ext.define('EmpresaExternaList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_empresa_externa', type: 'int'},
                {name: 'nombre_empresa_externa', type: 'string'}
            ]
        });

        eval("var storeEmpresaExterna_" + i + "= Ext.create('Ext.data.Store', { " +
                "  id: 'storeEmpresaExterna_" + i + "', " +
                "  model: 'EmpresaExternaList', " +
                "  autoLoad: false, " +
                " proxy: { " +
                "   type: 'ajax'," +
                "    url : '../../planificar/asignar_responsable/getEmpresasExternas'," +
                "   reader: {" +
                "        type: 'json'," +
                "       totalProperty: 'total'," +
                "        root: 'encontrados'" +
                "  }" +
                "  }" +
                " });    ");
        combo_empresas_externas = new Ext.form.ComboBox({
            id: 'cmb_empresa_externa_' + i,
            name: 'cmb_empresa_externa_' + i,
            fieldLabel: "Contratista",
            anchor: '100%',
            queryMode: 'remote',
            width: 300,
            emptyText: 'Seleccione Contratista',
            store: eval("storeEmpresaExterna_" + i),
            displayField: 'nombre_empresa_externa',
            valueField: 'id_empresa_externa',
            layout: 'anchor',
            disabled: true
        });


        // **************** CUADRILLAS ******************
        Ext.define('CuadrillasList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id_cuadrilla', type: 'int'},
                {name: 'nombre_cuadrilla', type: 'string'}
            ]
        });
        eval("var storeCuadrillas_" + i + "= Ext.create('Ext.data.Store', { " +
                "  id: 'storeCuadrillas_" + i + "', " +
                "  model: 'CuadrillasList', " +
                "  autoLoad: false, " +
                " proxy: { " +
                "   type: 'ajax'," +
                "    url : '../../planificar/asignar_responsable/getCuadrillas'," +
                "   reader: {" +
                "        type: 'json'," +
                "       totalProperty: 'total'," +
                "        root: 'encontrados'" +
                "  }" +
                "  }" +
                " });    ");
        combo_cuadrillas = new Ext.form.ComboBox({
            id: 'cmb_cuadrilla_' + i,
            name: 'cmb_cuadrilla_' + i,
            fieldLabel: "Cuadrilla",
            anchor: '100%',
            queryMode: 'remote',
            width: 300,
            emptyText: 'Seleccione Cuadrilla',
            store: eval("storeCuadrillas_" + i),
            displayField: 'nombre_cuadrilla',
            valueField: 'id_cuadrilla',
            layout: 'anchor',
            disabled: true
        });


        //******** html vacio...
        var iniHtmlVacio = '';
        Vacio = Ext.create('Ext.Component', {
            html: iniHtmlVacio,
            width: 600,
            padding: 8,
            layout: 'anchor',
            style: {color: '#000000'}
        });

        formPanelAsignacion = Ext.create('Ext.form.Panel', {
            width: 700,
            height: 150,
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [Vacio1, RadiosTiposResponsable, Vacio1, combo_empleados, combo_cuadrillas, combo_empresas_externas, Vacio],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        asignarResponsable(origen, id);
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAsignacion();
                    }
                }
            ]
        });

        Ext.getCmp('cmb_empleado_' + i).setVisible(true);
        Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
        Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);

        winAsignacion = Ext.widget('window', {
            title: 'Formulario Asignacion',
            width: 740,
            height: 200,
            minHeight: 200,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelAsignacion]
        });
    }

    winAsignacion.show();
}

function cierraVentanaAsignacion() {
    winAsignacion.close();
    winAsignacion.destroy();
}

/************************************************************************ */
/***************** ASIGNACION INDIVIDUAL RESPONSABLE ******************** */
/************************************************************************ */
function showAsignacionIndividual(rec, origen, id, panelAsignados)
{
    winAsignacionIndividual = "";
    formPanelAsignacionIndividual = "";

    if (!winAsignacionIndividual)
    {
        var id_servicio = rec.get("id_servicio");
        //******** html vacio...
        var iniHtmlVacio1 = '';
        Vacio1 = Ext.create('Ext.Component', {
            html: iniHtmlVacio1,
            padding: 4,
            layout: 'anchor',
            style: {color: '#000000'}
        });

        formPanelAsignacionIndividual = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            bodyStyle: "background: white; padding:10px; border: 0px none;",
            frame: true,
            items: [Vacio1],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        asignarResponsableIndividual(rec, origen, id);
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAsignacionIndividual();
                    }
                }
            ]
        });

        connTareas.request({
            method: 'GET',
            url: "../../factibilidad/factibilidad_instalacion/getTareasByProcesoAndTarea",
            timeout: 120000,
            params: {servicioId: id_servicio, nombreTarea: 'todas', estado: 'Activo'},
            success: function(response) {
                var data = Ext.JSON.decode(response.responseText.trim());

                if (data)
                {
                    tareasJS = data.encontrados;
                    for (i in tareasJS)
                    {
                        //******** hidden id tarea
                        var hidden_tarea = new Ext.form.Hidden({
                            id: 'hidden_id_tarea_' + i,
                            name: 'hidden_id_tarea_' + i,
                            value: tareasJS[i]["idTarea"]
                        });
                        //******** text nombre tarea
                        var text_tarea = new Ext.form.Label({
                            forId: 'txt_nombre_tarea_' + i,
                            style: "font-weight:bold; font-size:14px; color:red; margin-bottom: 15px;",
                            layout: 'anchor',
                            text: tareasJS[i]["nombreTarea"]
                        });

                        //******** RADIOBUTTONS -- TIPOS DE RESPONSABLES
                        var iniHtml = '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" checked="" value="empleado" name="tipoResponsable_' + i + '">&nbsp;Empleado' +
                                '&nbsp;&nbsp;' +
                                '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="cuadrilla" name="tipoResponsable_' + i + '">&nbsp;Cuadrilla' +
                                '&nbsp;&nbsp;' +
                                '<input type="radio" onchange="cambiarTipoResponsable_Individual(' + i + ', this.value);" value="empresaExterna" name="tipoResponsable_' + i + '">&nbsp;Contratista' +
                                '';

                        RadiosTiposResponsable = Ext.create('Ext.Component', {
                            html: iniHtml,
                            width: 600,
                            padding: 10,
                            style: {color: '#000000'}
                        });

                        // **************** EMPLEADOS ******************
                        Ext.define('EmpleadosList', {
                            extend: 'Ext.data.Model',
                            fields: [
                                {name: 'id_empleado', type: 'int'},
                                {name: 'nombre_empleado', type: 'string'}
                            ]
                        });
                        eval("var storeEmpleados_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpleados_" + i + "', " +
                                "  model: 'EmpleadosList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpleados'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                        combo_empleados = new Ext.form.ComboBox({
                            id: 'cmb_empleado_' + i,
                            name: 'cmb_empleado_' + i,
                            fieldLabel: "Empleados",
                            anchor: '100%',
                            queryMode: 'remote',
                            width: 300,
                            emptyText: 'Seleccione Empleado',
                            store: eval("storeEmpleados_" + i),
                            displayField: 'nombre_empleado',
                            valueField: 'id_empleado',
                            layout: 'anchor',
                            disabled: false
                        });


                        // ****************  EMPRESA EXTERNA  ******************
                        Ext.define('EmpresaExternaList', {
                            extend: 'Ext.data.Model',
                            fields: [
                                {name: 'id_empresa_externa', type: 'int'},
                                {name: 'nombre_empresa_externa', type: 'string'}
                            ]
                        });

                        eval("var storeEmpresaExterna_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeEmpresaExterna_" + i + "', " +
                                "  model: 'EmpresaExternaList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getEmpresasExternas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                        combo_empresas_externas = new Ext.form.ComboBox({
                            id: 'cmb_empresa_externa_' + i,
                            name: 'cmb_empresa_externa_' + i,
                            fieldLabel: "Contratista",
                            anchor: '100%',
                            queryMode: 'remote',
                            width: 300,
                            emptyText: 'Seleccione Contratista',
                            store: eval("storeEmpresaExterna_" + i),
                            displayField: 'nombre_empresa_externa',
                            valueField: 'id_empresa_externa',
                            layout: 'anchor',
                            disabled: true
                        });


                        // **************** CUADRILLAS ******************
                        Ext.define('CuadrillasList', {
                            extend: 'Ext.data.Model',
                            fields: [
                                {name: 'id_cuadrilla', type: 'int'},
                                {name: 'nombre_cuadrilla', type: 'string'}
                            ]
                        });
                        eval("var storeCuadrillas_" + i + "= Ext.create('Ext.data.Store', { " +
                                "  id: 'storeCuadrillas_" + i + "', " +
                                "  model: 'CuadrillasList', " +
                                "  autoLoad: false, " +
                                " proxy: { " +
                                "   type: 'ajax'," +
                                "    url : '../../planificar/asignar_responsable/getCuadrillas'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");
                        combo_cuadrillas = new Ext.form.ComboBox({
                            id: 'cmb_cuadrilla_' + i,
                            name: 'cmb_cuadrilla_' + i,
                            fieldLabel: "Cuadrilla",
                            anchor: '100%',
                            queryMode: 'remote',
                            width: 300,
                            emptyText: 'Seleccione Cuadrilla',
                            store: eval("storeCuadrillas_" + i),
                            displayField: 'nombre_cuadrilla',
                            valueField: 'id_cuadrilla',
                            layout: 'anchor',
                            disabled: true
                        });


                        //******** html vacio...
                        var iniHtmlVacio = '';
                        Vacio = Ext.create('Ext.Component', {
                            html: iniHtmlVacio,
                            width: 600,
                            padding: 8,
                            layout: 'anchor',
                            style: {color: '#000000'}
                        });

                        formPanelAsignacionIndividual.items.add(hidden_tarea);
                        formPanelAsignacionIndividual.items.add(text_tarea);
                        formPanelAsignacionIndividual.items.add(RadiosTiposResponsable);
                        formPanelAsignacionIndividual.items.add(combo_empleados);
                        formPanelAsignacionIndividual.items.add(combo_cuadrillas);
                        formPanelAsignacionIndividual.items.add(combo_empresas_externas);
                        formPanelAsignacionIndividual.items.add(Vacio);
                        formPanelAsignacionIndividual.doLayout();


                        Ext.getCmp('cmb_empleado_' + i).setVisible(true);
                        Ext.getCmp('cmb_cuadrilla_' + i).setVisible(false);
                        Ext.getCmp('cmb_empresa_externa_' + i).setVisible(false);
                    }

                    winAsignacionIndividual = Ext.widget('window', {
                        title: 'Formulario Asignacion Individual',
                        layout: 'fit',
                        resizable: false,
                        modal: true,
                        closable: false,
                        items: [formPanelAsignacionIndividual]
                    });

                    winAsignacionIndividual.show();
                }
                else {
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: "Ocurrio un Error en la Obtencion de las Tareas",
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

}

function cierraVentanaAsignacionIndividual() {
    winAsignacionIndividual.close();
    winAsignacionIndividual.destroy();
}   


function showRecursoDeRedMigracion(rec, id, origen)
{
    const idServicio = rec.data.id_servicio;
    winRecursoDeRed = "";
    formPanelRecursosDeRed = "";
    var cantidadRegistrosGridIps = 0;
    if (!winRecursoDeRed)
    {

        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        Ext.define('tipoCaracteristica', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'tipo', type: 'string'}
            ]
        });

        if (rec.data.nombreTecnico == "IP") 
        {
            var comboCaracteristica = new Ext.data.Store({
                model: 'tipoCaracteristica',
                data: [
                    {tipo: 'PUBLICA'}
                ]
            });
        } 
        else 
        {
            if (rec.data.tieneIp) 
            {
                var comboCaracteristica = new Ext.data.Store({
                    model: 'tipoCaracteristica',
                    data: [
                        {tipo: 'MONITOREO'},
                        {tipo: 'WAN'},
                        {tipo: 'LAN'},
                        {tipo: 'PUBLICA'}
                    ]
                });
            } 
            else 
            {
                var comboCaracteristica = new Ext.data.Store({
                    model: 'tipoCaracteristica',
                    data: [
                        {tipo: 'MONITOREO'},
                        {tipo: 'WAN'},
                        {tipo: 'LAN'}
                    ]
                });
            }

        }

        storeElementosRadio = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            listeners: {
                load: function() {
                    if (Ext.getCmp("cmb_RADIO")) {
                        var valor = Ext.getCmp("cmb_RADIO").getValue();

                        if (valor > 0) {
                        } else {
                            Ext.getCmp("cmb_RADIO").setValue(rec.data.radio);
                        }
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: '../../factibilidad/factibilidad_instalacion/ajaxComboElementos',
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
                    nombre: this.nombreElemento,
                    estado: 'ACTIVE',
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

        storeElementos = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            autoLoad: true,
            listeners: {
                load: function() {
                    if (rec.data.idPop) {
                        if (Ext.getCmp("cmb_POP")) {
                            Ext.getCmp("cmb_POP").setValue(rec.data.pop);
                            storeElementosByPadre2.proxy.extraParams = {popId: rec.data.idPop, elemento: 'DSLAM'};
                            storeElementosByPadre2.load({params: {}});
                        }
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: '../../factibilidad/factibilidad_instalacion/ajaxComboElementos',
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
                    nombre: this.nombreElemento,
                    estado: 'ACTIVE',
                    elemento: 'POP'
                }
            },
            fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ],
        });

        storeElementosSplitters = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            autoLoad: true,
            listeners: {
                load: function() {
                    if (Ext.getCmp("cmb_SPLITTER")) {
                        var valor = Ext.getCmp("cmb_SPLITTER").getValue();

                        if (valor > 0) {
                        } else {
                            Ext.getCmp("cmb_SPLITTER").setValue(rec.data.splitter);
                        }
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: '../../factibilidad/factibilidad_instalacion/ajaxComboElementosByPadre',
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
        storeElementosByPadre2 = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            listeners: {
                load: function() {
                    var valor = Ext.getCmp("cmb_POP").getValue();

                    if (valor > 0) {
                    } else {
                        Ext.getCmp("cmb_DSLAM").setValue(rec.data.dslam);
                    }
                }
            },
            proxy: {
                type: 'ajax',
                url: '../../factibilidad/factibilidad_instalacion/ajaxComboElementosByPadre',
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
                    popId: '',
                    elemento: 'DSLAM'
                }
            },
            fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
        });

        //dms
        if (rec.data.cantidadIp !== 0 && prefijoEmpresa == "MD" && rec.data.ultimaMilla == "Fibra Optica") {
            var nroIp = 0;
            var plan = 0;
            if (rec.data.nombreTecnico === 'IP') {
                nroIp = rec.data.cantidad;
            } else {
                nroIp = rec.data.cantidadIp;
                plan = rec.data.idPlan;
            }

            var storeIps = new Ext.data.Store({
                id: 'idPoolStore',
                total: 'total',
                pageSize: 10,
                autoLoad: true,
                listeners: {
                    'load': function(store, records, successful) {
                        if (successful) {
                            if (store.getProxy().getReader().rawData.error) {
                                Ext.Msg.show({
                                    title: 'Importante',
                                    msg: store.getProxy().getReader().rawData.error,
                                    width: 300,
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                            else if (store.getProxy().getReader().rawData.faltantes) {
                                if (store.getProxy().getReader().rawData.faltantes !== 0) {
                                    Ext.Msg.show({
                                        title: 'Importante',
                                        msg: 'No se encontraron disponibles el número de ips requeridas. <br /> Ips faltantes: '
                                                + store.getProxy().getReader().rawData.faltantes + '<br /> Por favor solicitar a GEPON crear un nuevo pool de ip.',
                                        width: 300,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        }
                    }
                },
                proxy: {
                    type: 'ajax',
                    url: nroIp + '/' + rec.data.idPop + '/' + rec.data.id_servicio + '/' + rec.data.id_punto + '/' + rec.data.esPlan + 
                         '/' + plan + '/' + 'MIGRACION' + '/getips',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'ips',
                        messageProperty: 'message'
                    }
                },
                fields:
                        [
                            {name: 'ip', mapping: 'ip'},
                            {name: 'mascara', mapping: 'mascara'},
                            {name: 'gateway', mapping: 'gateway'},
                            {name: 'tipo', mapping: 'tipo'}
                        ]
            });
   
        } else {//dms

            var storeIps = new Ext.data.Store({
                fields:
                        [
                            {name: 'ip', mapping: 'ip'},
                            {name: 'tipo', mapping: 'tipo'}
                        ]
            });

        }

        Ext.define('Ips', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'ip', mapping: 'ip'},
                {name: 'tipo', mapping: 'tipo'}
            ]
        });

        var cellEditingIps = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, object) {
                    var rowIdx = object.rowIdx;
                    var column = object.field;
                    var currentIp = object.value;
                    var storeIps = gridIps.getStore().getAt(rowIdx);

                    if (typeof storeIps != 'undefined') {
                        var tipo = storeIps.get('tipo');

                        if (column == "ip" && tipo != 'LAN' && tipo != 'MONITOREO') {
                            if (esIpValida(currentIp)) {
                                if (!existeRecordIp(rowIdx, currentIp))
                                {
                                    connValidaIp.request({
                                        url: "validarIp",
                                        timeout: 120000,
                                        method: 'post',
                                        params: {tipo: tipo, ip: currentIp, idServicio: rec.data.id_servicio},
                                        success: function(response) {
                                            var text = response.responseText;

                                            if (text == "No existe Ip")
                                            {

                                            }
                                            else {
                                                cierraVentanaRecursosDeRed();
                                                Ext.MessageBox.show({
                                                    title: 'Error',
                                                    msg: text,
                                                    buttons: Ext.MessageBox.OK,
                                                    icon: Ext.MessageBox.ERROR,
                                                    fn: function(btn, text) {
                                                        muestraVentanaRecursosDeRed();
                                                        eliminarSeleccion(gridIps);
                                                    }
                                                });
                                            }
                                        },
                                        failure: function(result) {
                                            cierraVentanaRecursosDeRed();
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: result.responseText,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR,
                                                fn: function(btn, text) {
                                                    muestraVentanaRecursosDeRed();
                                                    eliminarSeleccion(gridIps);
                                                }
                                            });

                                        }
                                    });
                                }
                                else
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Ip ya ingresada. Por favor ingrese una distinta.",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                    eliminarSeleccion(gridIps);
                                }
                            } else {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "Ingrese una Ip valida",
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                                eliminarSeleccion(gridIps);
                            }
                        }
                        // refresh summaries
                        gridIps.getView().refresh();
                    }
                }
            }
        });

        var selIps = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) {
                    gridIps.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        });

        if (prefijoEmpresa == "MD") {

            if (rec.data.ultimaMilla == "Fibra Optica") {
                if (rec.data.tieneIp) {
                    //grid de ips
                    gridIps = Ext.create('Ext.grid.Panel', {
                        id: 'gridIps',
                        store: storeIps,
                        columnLines: true,
                        columns: [{
                                header: 'Tipo',
                                dataIndex: 'tipo',
                                width: 100,
                                sortable: true,
                                editor: {
                                    queryMode: 'local',
                                    xtype: 'combobox',
                                    displayField: 'tipo',
                                    valueField: 'tipo',
                                    loadingText: 'Buscando...',
                                    store: comboCaracteristica
                                }
                            }, {
                                header: 'Ip',
                                dataIndex: 'ip',
                                width: 150,
                                editor: {
                                    id: 'ip',
                                    name: 'ip',
                                    xtype: 'textfield',
                                    valueField: ''
                                }
                            },
                            {
                                header: 'Mascara',
                                dataIndex: 'mascara',
                                width: 150,
                                editor: {
                                    id: 'mascara',
                                    name: 'mascara',
                                    xtype: 'textfield',
                                    valueField: ''
                                }
                            },
                            {
                                header: 'Gateway',
                                dataIndex: 'gateway',
                                width: 150,
                                editor: {
                                    id: 'gateway',
                                    name: 'gateway',
                                    xtype: 'textfield',
                                    valueField: ''
                                }
                            }],
                        viewConfig: {
                            stripeRows: true
                        },
                        frame: true,
                        height: 200,
                        title: 'Ips del Cliente'
                    });
                } else {

                    gridIps = Ext.create('Ext.Component', {
                        html: "<br>",
                    });
                }

            } else {
                //grid de ips
                gridIps = Ext.create('Ext.grid.Panel', {
                    id: 'gridIps',
                    store: storeIps,
                    columnLines: true,
                    columns: [{
                            header: 'Tipo',
                            dataIndex: 'tipo',
                            width: 100,
                            sortable: true,
                            editor: {
                                queryMode: 'local',
                                xtype: 'combobox',
                                displayField: 'tipo',
                                valueField: 'tipo',
                                loadingText: 'Buscando...',
                                store: comboCaracteristica
                            }
                        }, {
                            header: 'Ip',
                            dataIndex: 'ip',
                            width: 150,
                            editor: {
                                id: 'ip',
                                name: 'ip',
                                xtype: 'textfield',
                                valueField: ''
                            }
                        },
                        {
                            header: 'Mascara',
                            dataIndex: 'mascara',
                            width: 150,
                            editor: {
                                id: 'mascara',
                                name: 'mascara',
                                xtype: 'textfield',
                                valueField: ''
                            }
                        },
                        {
                            header: 'Gateway',
                            dataIndex: 'gateway',
                            width: 150,
                            editor: {
                                id: 'gateway',
                                name: 'gateway',
                                xtype: 'textfield',
                                valueField: ''
                            }
                        }],
                    selModel: selIps,
                    viewConfig: {
                        stripeRows: true
                    },
                    // inline buttons
                    dockedItems: [{
                            xtype: 'toolbar',
                            items: [{
                                    itemId: 'removeButton',
                                    text: 'Eliminar',
                                    tooltip: 'Elimina el item seleccionado',
                                    iconCls: 'remove',
                                    disabled: true,
                                    handler: function() {
                                        eliminarSeleccion(gridIps);
                                    }
                                }, '-', {
                                    text: 'Agregar',
                                    tooltip: 'Agrega un item a la lista',
                                    iconCls: 'add',
                                    handler: function() {
                                        // Create a model instance
                                        var r = Ext.create('Ips', {
                                            ip: '',
                                            mascara: '',
                                            gateway: '',
                                            tipo: ''

                                        });
                                        if (!existeRecordIp(1000, ''))
                                        {
                                            storeIps.insert(0, r);
                                            cellEditingIps.startEditByPosition({row: 0, column: 1});
                                        }
                                        else
                                        {
                                            alert('Ya existe un registro vacio.');
                                        }
                                    }
                                }]
                        }],
                    frame: true,
                    height: 200,
                    title: 'Ips del Cliente',
                    plugins: [cellEditingIps]
                });

            }
        }


        Ext.define('IpPublica', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'ipPublica', mapping: 'ipPublica'}
            ]
        });

        Ext.define('IpMonitoreo', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'ipMonitoreo', mapping: 'ipMonitoreo'}
            ]
        });


        storeIpsPublicas = Ext.create('Ext.data.Store', {
            model: 'IpPublica',
        });

        storeIpsMonitoreo = Ext.create('Ext.data.Store', {
            model: 'IpMonitoreo',
        });

        var cellEditingIpPublica = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, object) {
                    // refresh summaries
                    var rowIdx = object.rowIdx;
                    var currentIpPublica = object.value;
                    if (esIpValida(currentIpPublica)) {
                        if (!existeRecordIpPublica(rowIdx, currentIpPublica, gridIpPublica))
                        {
                            $('input[name="ipPublica_text"]').val('');
                            $('input[name="ipPublica_text"]').val(currentIpPublica);
                        }
                        else
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: "Ip ya existente. Por favor ingrese otra.",
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                            eliminarSeleccion(gridIpPublica);
                        }
                    } else {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: "Ingrese una Ip valida",
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        eliminarSeleccion(gridIpPublica);
                    }
                }
            }
        });

        var selIpsPublicas = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) {
                    gridIpPublica.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        });

        var cellEditingIpMonitoreo = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, object) {
                    // refresh summaries
                    var rowIdx = object.rowIdx;
                    var currentIpMonitoreo = object.value;
                    if (esIpValida(currentIpMonitoreo)) {
                        if (!existeRecordIpMonitoreo(rowIdx, currentIpMonitoreo, gridIpMonitoreo))
                        {
                            $('input[name="ipMonitoreo_text"]').val('');
                            $('input[name="ipMonitoreo_text"]').val(currentIpMonitoreo);
                        }
                        else
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: "Ip ya existente. Por favor ingrese otra.",
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                            eliminarSeleccion(gridIpMonitoreo);
                        }
                    } else {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: "Ingrese una Ip valida",
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        eliminarSeleccion(gridIpMonitoreo);
                    }
                }
            }
        });

        var selIpsMonitoreo = Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                selectionchange: function(sm, selections) {
                    gridIpMonitoreo.down('#removeButton').setDisabled(selections.length == 0);
                }
            }
        });

        storeInterfacesByElemento = new Ext.data.Store({
            autoLoad: true,
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
                    idElemento: rec.get("elementoId")
                }
            },
            fields:
                    [
                        {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                        {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
                    ]
        });

        storeInterfacesBySplitter = new Ext.data.Store({
            autoLoad: true,
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
                    interfaceSplitter: rec.data.interfaceSplitter,
                    estado: "reserved"
                }
            },
            fields:
                    [
                        {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                        {name: 'nombreInterfaceElemento', mapping: 'nombreInterfaceElemento'}
                    ]
        });

        //grid Ip Publica
        gridIpPublica = Ext.create('Ext.grid.Panel', {
            id: '	gridIpPublica',
            store: storeIpsPublicas,
            columnLines: true,
            columns: [{
                    id: 'ipPublica',
                    header: 'Ip Publica',
                    dataIndex: 'ipPublica',
                    width: 290,
                    editor: {
                        id: 'ipPublica_text',
                        name: 'ipPublica_text',
                        xtype: 'textfield',
                    }
                }],
            selModel: selIpsPublicas,
            viewConfig: {
                stripeRows: true
            },
            // inline buttons
            dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                            text: 'Agregar',
                            tooltip: 'Agrega un item a la lista',
                            iconCls: 'add',
                            handler: function() {
                                if (!existeRecordIpPublica("", "", gridIpPublica))
                                {
                                    // Create a model instance
                                    var r = Ext.create('IpPublica', {
                                        ipPublica: ''
                                    });
                                    storeIpsPublicas.insert(0, r);
                                }
                                else
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Ya existe un registro vacio para que sea llenado.",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        }, '-', {
                            itemId: 'removeButton',
                            text: 'Eliminar',
                            tooltip: 'Elimina el item seleccionado',
                            iconCls: 'remove',
                            disabled: true,
                            handler: function() {
                                eliminarSeleccion(gridIpPublica);
                            }
                        }]
                }],
            frame: true,
            title: 'Ips Publicas',
            plugins: [cellEditingIpPublica]
        });

        function existeRecordIp(rowIdx, currentIp)
        {
            var existe = false;
            var num = gridIps.getStore().getCount();

            for (var i = 0; i < num; i++)
            {
                if (rowIdx != i) {
                    var ipGrid = gridIps.getStore().getAt(i).get('ip');

                    if ((ipGrid == currentIp))
                    {
                        existe = true;
                        break;
                    }
                }
            }
            return existe;
        }

        function existeRecordIpPublica(rowIdx, ip, grid)
        {
            var existe = false;
            var num = grid.getStore().getCount();

            for (var i = 0; i < num; i++)
            {
                if (i != rowIdx) {
                    var ipPublica = grid.getStore().getAt(i).get('ipPublica');

                    if ((ipPublica == ip))
                    {
                        existe = true;
                        break;
                    }
                }
            }
            return existe;
        }

        //grid Ip Monitoreo
        gridIpMonitoreo = Ext.create('Ext.grid.Panel', {
            id: '	gridIpMonitoreo',
            store: storeIpsMonitoreo,
            columnLines: true,
            columns: [{
                    id: 'ipMonitoreo',
                    header: 'Ip Monitoreo',
                    dataIndex: 'ipMonitoreo',
                    width: 290,
                    editor: {
                        id: 'ipMonitoreo_cmp',
                        xtype: 'textfield',
                    }
                }],
            selModel: selIpsMonitoreo,
            viewConfig: {
                stripeRows: true
            },
            // inline buttons
            dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                            text: 'Agregar',
                            tooltip: 'Agrega un item a la lista',
                            iconCls: 'add',
                            handler: function() {
                                if (!existeRecordIpMonitoreo("", "", gridIpMonitoreo))
                                {
                                    // Create a model instance
                                    var r = Ext.create('IpMonitoreo', {
                                        ipPublica: ''
                                    });
                                    storeIpsMonitoreo.insert(0, r);
                                }
                                else
                                {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "Ya existe un registro vacio para que sea llenado.",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        }, '-', {
                            itemId: 'removeButton',
                            text: 'Eliminar',
                            tooltip: 'Elimina el item seleccionado',
                            iconCls: 'remove',
                            disabled: true,
                            handler: function() {
                                eliminarSeleccion(gridIpMonitoreo);
                            }
                        }]
                }],
            frame: true,
            title: 'Ips Monitoreo',
            plugins: [cellEditingIpMonitoreo]
        });

        function existeRecordIpMonitoreo(rowIdx, ip, grid)
        {
            var existe = false;
            var num = grid.getStore().getCount();

            for (var i = 0; i < num; i++)
            {
                if (i != rowIdx) {
                    var ipMonitoreo = grid.getStore().getAt(i).get('ipMonitoreo');

                    if ((ipMonitoreo == ip))
                    {
                        existe = true;
                        break;
                    }
                }
            }
            return existe;
        }

        function eliminarSeleccion(datosSelect)
        {
            for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
            {
                datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
            }
        }

        function obtenerDatosIps()
        {
            if (gridIps.getStore().getCount() >= 1) {
                var array_relaciones = new Object();
                array_relaciones['total'] = gridIps.getStore().getCount();
                array_relaciones['caracteristicas'] = new Array();
                var array_data = new Array();
                for (var i = 0; i < gridIps.getStore().getCount(); i++)
                {
                    array_data.push(gridIps.getStore().getAt(i).data);
                }
                array_relaciones['caracteristicas'] = array_data;
                return Ext.JSON.encode(array_relaciones);
            } else {
                return "";
            }
        }

        function obtenerDatosIpsPublicas(cantidad, tipo)
        {
            if (gridIps.getStore().getCount() >= 1) {
                var array_relaciones = new Object();
                array_relaciones['total'] = gridIps.getStore().getCount();
                array_relaciones['caracteristicas'] = new Array();

                var array_data = new Array();
                var numIps = 0;

                for (var i = 0; i < gridIps.getStore().getCount(); i++)
                {
                    array_data.push(gridIps.getStore().getAt(i).data);
                    if (gridIps.getStore().getAt(i).data.tipo == tipo)
                    {
                        numIps++;
                    }
                    if (gridIps.getStore().getAt(i).data.ip == '')
                    {
                        return "";
                    }
                    
                }

                if (numIps > cantidad || numIps < cantidad) {
                    return "";
                }

                array_relaciones['caracteristicas'] = array_data;
                return Ext.JSON.encode(array_relaciones);
            } else {
                return "";
            }
        }

        function obtenerIpsPublicas()
        {
            if (gridIpPublica.getStore().getCount() >= 1) {
                var ips = "";
                for (var i = 0; i < gridIpPublica.getStore().getCount(); i++)
                {
                    if (i == 0) {
                        ips = gridIpPublica.getStore().getAt(i).data.ipPublica;
                    } else {
                        ips = ips + "@" + gridIpPublica.getStore().getAt(i).data.ipPublica;
                    }
                }
                return ips;
            } else {
                return "";
            }
        }
        function obtenerIpsMonitoreo()
        {
            if (gridIpMonitoreo.getStore().getCount() >= 1) {
                var ips = "";
                for (var i = 0; i < gridIpMonitoreo.getStore().getCount(); i++)
                {
                    if (i == 0) {
                        ips = gridIpMonitoreo.getStore().getAt(i).data.ipMonitoreo;
                    } else {
                        ips = ips + "@" + gridIpMonitoreo.getStore().getAt(i).data.ipMonitoreo;
                    }
                }
                return ips;
            } else {
                return "";
            }
        }
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }

        if (rec.get("esPlan") == "si") {
            storeItemsPlan = new Ext.data.Store({
                pageSize: 14,
                total: 'total',
                proxy: {
                    type: 'ajax',
                    url: 'ajaxGetDetallePlan',
                    timeout: 120000,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'listado'
                    },
                    actionMethods: {
                        create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                    },
                    extraParams: {
                        planId: rec.get("idPlan")
                    }
                },
                fields:
                        [
                            {name: 'nombreProducto', mapping: 'nombreProducto'},
                            {name: 'cantidad', mapping: 'cantidad'},
                        ],
                autoLoad: true
            });
            //grid items plan
            gridItemsPlan = Ext.create('Ext.grid.Panel', {
                id: 'gridItemsPlan',
                store: storeItemsPlan,
                height: 150,
                width: 350,
                columnLines: true,
                columns: [new Ext.grid.RowNumberer(),
                    {
                        header: 'Producto',
                        dataIndex: 'nombreProducto',
                        width: 215
                    }, {
                        header: 'Cantidad',
                        dataIndex: 'cantidad',
                    }

                ],
                frame: true,
                title: 'Items Plan',
            });
        } else {
            gridItemsPlan = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }

        var boolHabilitaGuardar = false;
        if (rec.get("marcaOlt") == 'TELLION')
        {
            boolHabilitaGuardar = true;
        }
        
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
                                }, itemTercerizadora,
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
                                }, gridItemsPlan

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
                            fieldLabel: 'Cantidad',
                            name: 'txt_cantidad_ip',
                            id: 'txt_cantidad_ip',
                            value: rec.get("cantidad"),
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
                            id: 'cmb_RADIO',
                            name: 'cmb_RADIO',
                            fieldLabel: '* RADIO',
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
                            emptyText: 'Seleecione..',
                            minChars: 3,
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_POP',
                            name: 'cmb_POP',
                            fieldLabel: '* POP',
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
                            minChars: 3,
                            listeners: {
                                select: {fn: function(combo, value) {
                                        Ext.getCmp('cmb_DSLAM').reset();

                                        storeElementosByPadre2.proxy.extraParams = {popId: combo.getValue(), elemento: 'DSLAM'};
                                        storeElementosByPadre2.load({params: {}});
                                    }},
                                change: {fn: function(combo, newValue, oldValue) {
                                        if (combo) {
                                            if (combo.getValue() > 0) {
                                            } else {
                                                if (combo.getValue().match(/[a-zA-Z]/)) {
                                                    storeElementos.proxy.extraParams = {nombre: combo.getValue()};
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
                            fieldLabel: '* DSLAM',
                            typeAhead: true,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            selectOnTab: true,
                            store: storeElementosByPadre2,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;",
                            listeners: {
                                select: {fn: function(combo, value) {
                                        Ext.getCmp('cmb_Interface').reset();
                                        storeInterfacesByElemento.proxy.extraParams = {idElemento: combo.getValue()};
                                        storeInterfacesByElemento.load({params: {}});
                                    }}
                            }
                        }, 
                        {
                            xtype: 'combobox',
                            id: 'cmb_SPLITTER',
                            name: 'cmb_SPLITTER',
                            fieldLabel: '* Splitter',
                            typeAhead: true,
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
                                        Ext.getCmp('cmb_Interface_splitter').reset();
                                        storeInterfacesBySplitter.proxy.extraParams = {idElemento: combo.getValue(),estado:"reserved"};
                                        storeInterfacesBySplitter.load({params: {}});
                                    }}
                            }
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: '* VCI',
                            name: 'vci',
                            id: 'vci',
                            allowNegative: false,
                            value: 1,
                            width: 200,
                            emptyText: 'Ingrese un numero',
                            labelStyle: "color:red;",
                            validator: function(val) {
                                if (!Ext.isEmpty(val)) {
                                    if (val >= 1 && val <= 100)
                                        return true;
                                    else {
                                        Ext.getCmp('vci').setValue("1");
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: "VCI debe ser entre 1 y 100",
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                        return "VCI debe ser entre 1 y 100";
                                    }
                                } else {
                                    Ext.getCmp('vci').setValue("1");
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: "VCI debe ser entre 1 y 100",
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                    return "VCI debe ser entre 1 y 100";
                                }
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_Interface_splitter',
                            name: 'cmb_Interface_splitter',
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
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_Interface',
                            name: 'cmb_Interface',
                            fieldLabel: '* Interface',
                            width: 200,
                            typeAhead: true,
                            allowBlank: false,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreInterfaceElemento',
                            valueField: 'idInterfaceElemento',
                            selectOnTab: true,
                            store: storeInterfacesByElemento,
                            listClass: 'x-combo-list-small',
                            emptyText: 'Seleccione',
                            labelStyle: "color:red;",
                        }, {
                            xtype: 'panel',
                            BodyPadding: 10,
                            bodyStyle: "background: white; padding:10px; border: 0px none;",
                            frame: true,
                            items: [gridIps]
                        }
                    ]
                }
            ],
            buttons: [
                {
                    disabled: boolHabilitaGuardar,
                    text: 'Guardar',
                    handler: function() {
                        var id_factibilidad = rec.get("id_factibilidad");
                        var boolError = false;
                        var mensajeError = "";
                        var tipoIp = "";
                        var datosIps = "";
                        if (prefijoEmpresa == "MD")
                        {
                            if (rec.get('ultimaMilla') == "Fibra Optica")
                            {
                                tipoIp = "FIJA";
                            }
                            else
                            {    
                                tipoIp = "PUBLICA";
                            }
                        }
                        
                        if (rec.data.nombreTecnico == "IP") {
                            var datosIps = obtenerDatosIpsPublicas(rec.data.cantidad, tipoIp);

                            if (datosIps == "") {
                                boolError = true;
                                mensajeError = "Favor ingresar el numero de Ips requeridos: " + rec.data.cantidad;
                            } else {
                                var paramsRecursosRed = {id: id_factibilidad, nombreTecnico: rec.data.nombreTecnico, producto: rec.data.producto, datosIps: datosIps, tipoSolicitud: rec.data.descripcionSolicitud, id_splitter:rec.data.idSplitter,id_olt:rec.get("idPop"),id_interface_olt:rec.get("intElementoInterface"), cantidadRegistrosIps : rec.data.intCantidadIpsReservadas};
                            }
                        } else {
                            var vci = $('input[name="vci"]').val();
                            var id_elemento = Ext.getCmp('cmb_DSLAM').value;
                            var id_interface = Ext.getCmp('cmb_Interface').value;
                            var id_splitter = Ext.getCmp('cmb_SPLITTER').value;
                            var id_interface_splitter = Ext.getCmp('cmb_Interface_splitter').value;

                            if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                            {
                                boolError = true;
                                mensajeError += "El id del Detalle Solicitud no existe.\n";
                            } else {
                                if (rec.get('ultimaMilla') == "Radio") {

                                }
                                if (rec.get('ultimaMilla') == "Cobre") {
                                    if (!id_interface || id_interface == "" || id_interface == 0)
                                    {
                                        boolError = true;
                                        mensajeError += "La Interface no fue escogida, por favor seleccione.\n";
                                    }
                                }

                                if (rec.get('ultimaMilla') == "Fibra Optica") {

                                    if (!id_interface_splitter || id_interface_splitter == "" || id_interface_splitter == 0)
                                    {
                                        boolError = true;
                                        mensajeError += "La Interface no fue escogida, por favor seleccione.\n";
                                    }
                                }

                            }

                            if (!boolError) {
                                if (rec.data.tieneIp) {
                                    var datosIps = obtenerDatosIpsPublicas(rec.data.cantidadIp, tipoIp);
                                    if (datosIps == "") {
                                        boolError = true;
                                        mensajeError = "Favor ingresar el numero de Ips Publicas requeridas: " + rec.data.cantidad;
                                    }

                                } else {
                                    if (prefijoEmpresa == "MD" && rec.get('ultimaMilla') != "Fibra Optica")
                                    {
                                        var datosIps = obtenerDatosIps();
                                    }
                                }

                                if (!boolError) {
                                    var paramsRecursosRed = {id: id_factibilidad, producto: rec.data.producto, splitter_id: id_splitter, interface_splitter_id: id_interface_splitter, elemento_id: id_elemento, interface_id: id_interface, vci: vci, datosIps: datosIps, 
                                                             tipoSolicitud: rec.data.descripcionSolicitud,id_splitter:rec.data.idSplitter,id_olt:rec.get("idPop"),id_interface_olt:rec.get("intElementoInterface"),cantidadRegistrosIps : rec.data.intCantidadIpsReservadas,marcaOlt: rec.get("marcaOlt")};
                                }
                            }
                        }

                        if (!boolError)
                        {
                            connRecursoDeRed.request({
                                url: "guardaRecursosDeRed",
                                timeout: 120000,
                                method: 'post',
                                timeout: 120000,
                                        params: paramsRecursosRed,
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "Se guardo correctamente los Recursos de Red")
                                    {
                                        cierraVentanaRecursoDeRed();
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
                }
                , {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaRecursoDeRed();
                    }
                }
            ]
        });
        Ext.getCmp('cmb_Interface_splitter').setValue(rec.data.interfaceSplitter);
        
        if (rec.data.nombreTecnico == "IP") {
            Ext.getCmp('cmb_POP').setVisible(false);
            Ext.getCmp('cmb_DSLAM').setVisible(false);
            Ext.getCmp('cmb_Interface').setVisible(false);
            Ext.getCmp('vci').setVisible(false);
            Ext.getCmp('cmb_RADIO').setVisible(false);

            Ext.getCmp('txt_caja').setVisible(false);
            Ext.getCmp('txt_olt').setVisible(false);
            Ext.getCmp('txt_linea').setVisible(false);
            Ext.getCmp('cmb_SPLITTER').setVisible(false);
            Ext.getCmp('cmb_Interface_splitter').setVisible(false);
        } else {
            Ext.getCmp('txt_cantidad_ip').setVisible(false);
            if (rec.get('ultimaMilla') == "Radio") {
                Ext.getCmp('cmb_POP').setVisible(false);
                Ext.getCmp('cmb_DSLAM').setVisible(false);
                Ext.getCmp('cmb_Interface').setVisible(false);
                Ext.getCmp('vci').setVisible(false);
                Ext.getCmp('txt_caja').setVisible(false);
                Ext.getCmp('txt_olt').setVisible(false);
                Ext.getCmp('txt_linea').setVisible(false);
                Ext.getCmp('cmb_SPLITTER').setVisible(false);
                Ext.getCmp('cmb_Interface_splitter').setVisible(false);
            }
            if (rec.get('ultimaMilla') == "Cobre") {
                Ext.getCmp('cmb_RADIO').setVisible(false);
                Ext.getCmp('txt_caja').setVisible(false);
                Ext.getCmp('txt_olt').setVisible(false);
                Ext.getCmp('txt_linea').setVisible(false);
                Ext.getCmp('cmb_SPLITTER').setVisible(false);
                Ext.getCmp('cmb_Interface_splitter').setVisible(false);
            }
            if (rec.get('ultimaMilla') == "Fibra Optica") {
                Ext.getCmp('cmb_POP').setVisible(false);
                Ext.getCmp('cmb_Interface').setVisible(false);
                Ext.getCmp('cmb_DSLAM').setVisible(false);
                Ext.getCmp('vci').setVisible(false);
                Ext.getCmp('cmb_RADIO').setVisible(false);
                Ext.getCmp('cmb_SPLITTER').setDisabled(true);
            }
        }

        winRecursoDeRed = Ext.widget('window', {
            title: 'Migración de Recursos de Red',
            layout: 'fit',
            resizable: false,
            modal: true,
            items: [formPanelRecursosDeRed]
        });
        
    }

    winRecursoDeRed.show();
    //Valida que la marca del olt, en case de ser TELLION nuestra el mensaje por pantalla
    if ( (rec.get("marcaOlt") == 'TELLION') )
    {
        alert ('Enlace no corresponde a la migración (OLT es TELLION). Favor pedir a GIS que regularice.');
    }
}

