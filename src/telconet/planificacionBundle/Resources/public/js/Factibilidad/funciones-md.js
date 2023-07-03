/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var winVerMapa;
var winVerCroquis;
var winRechazarOrden_Factibilidad;
var winFactibilidad;
var winPreFactibilidad;
var winFactibilidadMateriales;

var connFactibilidad = new Ext.data.Connection({
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
                //Ext.get(document.body).mask('Loading...');
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
                //Ext.get(document.body).unmask();
            },
            scope: this
        }
    }
});

/************************************************************************ */
/************************** VER MAPA ************************************ */
/************************************************************************ */
function showVerMapa(rec) {
    winVerMapa = "";

    if (rec.get("latitud") != 0 && rec.get("longitud") != 0)
    {
        if (!winVerMapa)
        {
            formPanelMapa = Ext.create('Ext.form.Panel', {
//                width:600,
//                height:450,
                BodyPadding: 10,
                frame: true,
                items: [
                    {
                        html: "<div id='map_canvas' style='width:575px; height:450px'></div>"
                    }
                ]
            });

            winVerMapa = Ext.widget('window', {
                title: 'Mapa del Punto',
//                width: 600,
//                height: 450,
//                minHeight: 380,
                layout: 'fit',
                resizable: false,
                modal: true,
                closable: true,
                items: [formPanelMapa]
            });
        }

        winVerMapa.show();
        muestraMapa(rec.get("latitud"), rec.get("longitud"));
    }
    else
    {
        alert('Estas coordenadas son incorrectas!!')
    }
}

function muestraMapa(vlat, vlong) {
    var mapa;
    var ciudad = "";
    var markerPto;

    if ((vlat) && (vlong)) {
        var latlng = new google.maps.LatLng(vlat, vlong);
        //var latlng = new google.maps.LatLng(-2.176963, -79.883673);
        var myOptions = {
            zoom: 14,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }

        if (mapa) {
            mapa.setCenter(latlng);
        } else {
            mapa = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        }

        if (ciudad == "gye")
            layerCiudad = 'http://157.100.3.122/Coberturas.kml';
        else
            layerCiudad = 'http://157.100.3.122/COBERTURAQUITONETLIFE.kml';

        //google.maps.event.addListener(mapa, 'dblclick', function(event) {
        if (markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: latlng,
            map: mapa
        });
        mapa.setZoom(17);
        //  dd2dms(event.latLng.lat(),event.latLng.lng());
        //});
    }
}

function cierraVentanaMapa() {
    winVerMapa.close();
    winVerMapa.destroy();

}

/************************************************************************ */
/************************** VER CROQUIS ********************************* */
/************************************************************************ */
function showVerCroquis(idDetalleSolicitud, rutaImagen) {
    winVerCroquis = "";

    if (!winVerCroquis)
    {
        formPanelCroquis = Ext.create('Ext.form.Panel', {
//            width:600,
//            height:450,
            BodyPadding: 10,
            frame: true,
            items: [
                {
                    html: rutaImagen
                }
            ]
        });

        winVerCroquis = Ext.widget('window', {
            title: 'Croquis del Punto',
//            width: 600,
//            height:450,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: true,
            items: [formPanelCroquis]
        });
    }

    winVerCroquis.show();
}

function cierraVentanaCroquis() {
    winVerCroquis.close();
    winVerCroquis.destroy();
}

/************************************************************************ */
/************************* RECHAZAR ORDEN ******************************* */
/************************************************************************ */
function showRechazarOrden_Factibilidad(rec)
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
                url: urlMotivosRechazo,
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
//            width: 600,
            padding: 1,
            layout: 'anchor',
            style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
        });

        formPanelRechazarOrden_Factibilidad = Ext.create('Ext.form.Panel', {
//            width:600,
//            height:800,
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
//                         {
//                             xtype: 'textfield',
//                             fieldLabel: 'Coordenadas',
//                             name: 'info_coordenadas',
//                             id: 'info_coordenadas',
//                             value: rec.get("coordenadas"),
//                             allowBlank: false,
//                             readOnly : true
//                         },
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
                                timeout: 120000,
                                params: {id: id_factibilidad, id_motivo: cmbMotivo, observacion: txtObservacion},
                                url: urlRechazar,
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
//                                                                         Ext.Msg.alert('Alerta', 'Error: ' + text);
                                    }
                                },
                                failure: function(result) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
//									Ext.Msg.alert('Alerta', result.responseText);
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
//                                                                         Ext.Msg.alert('Alerta','Error: ' + mensajeError);
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
//            width: 640,
//            height:630,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelRechazarOrden_Factibilidad]
        });
    }

    winRechazarOrden_Factibilidad.show();
}

function cierraVentanaRechazarOrden_Factibilidad() {
    winRechazarOrden_Factibilidad.close();
    winRechazarOrden_Factibilidad.destroy();
}

/************************************************************************ */
/**************************** FACTIBILIDAD ****************************** */
/************************************************************************ */
function showPreFactibilidad(rec)
{
    winPreFactibilidad = "";
    formPanelFactibilidad = "";
    // store.load();
    if (!winPreFactibilidad)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
//            width: 600,
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
                //anchor : '65%',
                //layout: 'anchor'
        });

        formPanelFactibilidad = Ext.create('Ext.form.Panel', {
//            width:600,
//            height:800,
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
//                         {
//                             xtype: 'textfield',
//                             fieldLabel: 'Coordenadas',
//                             name: 'info_coordenadas',
//                             id: 'info_coordenadas',
//                             value: rec.get("coordenadas"),
//                             allowBlank: false,
//                             readOnly : true
//                         },
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
                                        cierraVentanaPreFactibilidad();
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
                                        //                                             Ext.Msg.alert('Alerta', 'Error: ' + text);
                                    }
                                },
                                failure: function(result) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                    //                                                                                 Ext.Msg.alert('Alerta', result.responseText);
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
//                            Ext.Msg.alert('Error' ,'Error: ' + mensajeError);
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaPreFactibilidad();
                    }
                }
            ]
        });

        winPreFactibilidad = Ext.widget('window', {
            title: 'Solicitud de Factibilidad',
//            width: 640,
//            height:630,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelFactibilidad]
        });
    }

    winPreFactibilidad.show();
}

function cierraVentanaPreFactibilidad() {
    winPreFactibilidad.close();
    winPreFactibilidad.destroy();
}

/************************************************************************ */
/********************** INGRESAR FACTIBILIDAD *************************** */
/************************************************************************ */
function showIngresoFactibilidad(rec)
{
    const idServicio           = rec.data.id_servicio;
    winIngresoFactibilidad = "";
    formPanelInresoFactibilidad = "";
    // store.load();
    if (!winIngresoFactibilidad)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
//            width: 600,
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
                //anchor : '65%',
                //layout: 'anchor'
        });

        storeElementos = new Ext.data.Store({
            total: 'total',
            listeners: {
                load: function() {

                }
            },
            proxy: {
                type: 'ajax',
                url: urlComboCajas,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                listeners: {
                    exception: function(proxy, response, options) {
// 			    Ext.MessageBox.alert('Error', response.status + ": " + response.statusText); 
                        Ext.MessageBox.alert('Error', "Favor ingrese un nombre de caja");
                    }
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    nombre: this.nombreElemento,
                    nivel: '2',
                }
            },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'},
                    {name: 'idOlt', mapping: 'idOlt'},
                    {name: 'olt', mapping: 'olt'},
                    {name: 'idLinea', mapping: 'idLinea'},
                    {name: 'linea', mapping: 'linea'}
                ],
//                 autoLoad: true
        });

        storeElementosByPadre = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: urlComboElementosByPadre,
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
                    elemento: 'SPLITTER',
                    estado: 'ACTIVE'
                }
            },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'}
                ]
        });

        formPanelIngresoFactibilidad = Ext.create('Ext.form.Panel', {
//            width:600,
//            height:800,
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
                    title: 'Datos de Nuevo Tramo',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-bottom: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Olt',
                            name: 'olt_nueva_factibilidad',
                            id: 'olt_nueva_factibilidad',
                            readOnly: true,
                            hidden: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Marca Olt',
                            name: 'marcaOlt_nueva_factibilidad',
                            id: 'marcaOlt_nueva_factibilidad',
                            readOnly: true,
                            hidden: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Linea',
                            name: 'linea_nueva_factibilidad',
                            id: 'linea_nueva_factibilidad',
                            readOnly: true,
                            hidden: true
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_CAJA',
                            name: 'cmb_CAJA',
                            fieldLabel: '* CAJA',
                            typeAhead: true,
                            triggerAction: 'all',
                            displayField: 'nombreElemento',
                            queryMode: "remote",
                            valueField: 'idElemento',
                            selectOnTab: true,
                            store: storeElementos,
                            width: 470,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            labelStyle: "color:red;",
                            forceSelection: true,
                            emptyText: 'Ingrese un nombre de Caja..',
                            minChars: 3,
                            listeners: {
                                select: {fn: function(combo, value) {
                                        Ext.getCmp('cmb_SPLITTER').reset();
                                        Ext.getCmp('cmb_SPLITTER').setDisabled(false);
                                        $('input[name="puertos_disponibles"]').val(0);

                                        storeElementosByPadre.proxy.extraParams = {
                                            popId: combo.getValue(),
                                            elemento: 'SPLITTER',
                                            estado: 'Activo',
                                            idServicio: idServicio
                                        };
                                        storeElementosByPadre.load({params: {}});
                                    }}
                            }
                        },
                        {
                            xtype: 'combobox',
                            id: 'cmb_SPLITTER',
                            name: 'cmb_SPLITTER',
                            fieldLabel: '* SPLITTER',
                            typeAhead: true,
                            width: 470,
                            queryMode: "local",
                            triggerAction: 'all',
                            displayField: 'nombreElemento',
                            valueField: 'idElemento',
                            selectOnTab: true,
                            store: storeElementosByPadre,
                            lazyRender: true,
                            listClass: 'x-combo-list-small',
                            emptyText: 'Seleccione un Splitter',
                            labelStyle: "color:red;",
                            disabled: true,
                            editable: false,
                            listeners: {
                                select: {fn: function(combo, value) {
                                        var idCaja = Ext.getCmp('cmb_CAJA').value;
                                        var nombreCaja = Ext.getCmp('cmb_CAJA').getRawValue();
                                        var nombreSplitter = combo.getRawValue();

                                        Ext.MessageBox.wait("Cargando datos de Caja...");

                                        Ext.Ajax.request({
                                            url: urlInfoCaja,
                                            method: 'post',
                                            timeout: 120000,
                                            params: {idCaja: idCaja, idSplitter: combo.getValue()},
                                            success: function(response) {
                                                Ext.MessageBox.close();
                                                var infoCaja = Ext.JSON.decode(response.responseText);

                                                if (infoCaja.error) {
                                                    Ext.getCmp('olt_nueva_factibilidad').setValue("");
                                                    Ext.getCmp('linea_nueva_factibilidad').setValue("");
                                                    Ext.getCmp('marcaOlt_nueva_factibilidad').setValue("");
                                                    Ext.getCmp('olt_nueva_factibilidad').setVisible(false);
                                                    Ext.getCmp('linea_nueva_factibilidad').setVisible(false);
                                                    Ext.getCmp('marcaOlt_nueva_factibilidad').setVisible(false);
                                                    Ext.getCmp('cmb_CAJA').reset();
                                                    cierraVentanaIngresoFactibilidad();
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: infoCaja.msg + "<br><br>Datos Ingresados<br>Caja: <b>" + nombreCaja + "</b><br>Splitter: <b>" + nombreSplitter + "</b>",
                                                        buttons: Ext.MessageBox.OK,
                                                        icon: Ext.MessageBox.ERROR
                                                    });

                                                } else {
                                                    Ext.getCmp('olt_nueva_factibilidad').setValue(infoCaja.olt);
                                                    Ext.getCmp('linea_nueva_factibilidad').setValue(infoCaja.linea);
                                                    Ext.getCmp('marcaOlt_nueva_factibilidad').setValue(infoCaja.marcaOlt);
                                                    Ext.getCmp('olt_nueva_factibilidad').setVisible(true);
                                                    Ext.getCmp('linea_nueva_factibilidad').setVisible(true);
                                                    Ext.getCmp('marcaOlt_nueva_factibilidad').setVisible(true);

                                                    setInfoCaja(idCaja, infoCaja);

                                                    Ext.MessageBox.wait("Verificando Disponibilidad de Puerto");
                                                    Ext.Ajax.request({
                                                        url: urlDisponibilidadElemento,
                                                        method: 'post',
                                                        timeout: 120000,
                                                        params: {idElemento: combo.getValue()},
                                                        success: function(response) {
                                                            Ext.MessageBox.close();
                                                            var ContDisponibilidad = response.responseText;
                                                            Ext.getCmp('puertos_disponibles').setVisible(true);
                                                            $('input[name="puertos_disponibles"]').val(ContDisponibilidad);
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.MessageBox.close();
                                                            Ext.getCmp('olt_nueva_factibilidad').setValue("");
                                                            Ext.getCmp('linea_nueva_factibilidad').setValue("");
                                                            Ext.getCmp('marcaOlt_nueva_factibilidad').setValue("");
                                                            Ext.getCmp('olt_nueva_factibilidad').setVisible(false);
                                                            Ext.getCmp('linea_nueva_factibilidad').setVisible(false);
                                                            Ext.getCmp('marcaOlt_nueva_factibilidad').setVisible(false);
                                                            cierraVentanaIngresoFactibilidad();

                                                            Ext.MessageBox.show({
                                                                title: 'Error',
                                                                msg: result.statusText,
                                                                buttons: Ext.MessageBox.OK,
                                                                icon: Ext.MessageBox.ERROR
                                                            });
                                                        }
                                                    });
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.MessageBox.close();
                                                Ext.getCmp('olt_nueva_factibilidad').setValue("");
                                                Ext.getCmp('linea_nueva_factibilidad').setValue("");
                                                Ext.getCmp('marcaOlt_nueva_factibilidad').setValue("");
                                                Ext.getCmp('olt_nueva_factibilidad').setVisible(false);
                                                Ext.getCmp('linea_nueva_factibilidad').setVisible(false);
                                                Ext.getCmp('marcaOlt_nueva_factibilidad').setVisible(false);
                                                cierraVentanaIngresoFactibilidad();

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
                        }
                    ]
                }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {

                        var cmb_CAJA = Ext.getCmp('cmb_CAJA').value;
                        var cmb_SPLITTER = Ext.getCmp('cmb_SPLITTER').value;
                        var puertosDisponibles = $('input[name="puertos_disponibles"]').val();
                        var id_factibilidad = rec.get("id_factibilidad");
                        var infoCaja = Ext.JSON.decode(getInfoCaja(cmb_CAJA));
                        var id_olt = 0;
                        var id_linea = 0;

                        if (infoCaja) {
                            id_olt = infoCaja.idOlt;
                            id_linea = infoCaja.idLinea;
                        }

                        var boolError = false;
                        var parametros;
                        var mensajeError = "";

                        if (id_olt == 0 || id_olt == "")
                        {
                            boolError = true;
                            mensajeError += "Caja sin Olt no se puede ingresar Factibilidad.\n";
                        }
                        if (id_linea == 0 || id_linea == "")
                        {
                            boolError = true;
                            mensajeError += "Caja sin Linea no se puede ingresar Factibilidad.\n";
                        }
                        if (!cmb_CAJA || cmb_CAJA == "" || cmb_CAJA == 0)
                        {
                            boolError = true;
                            mensajeError += "El Elemento Caja no fue escogido, por favor seleccione.\n";
                        }
                        if (!cmb_SPLITTER || cmb_SPLITTER == "" || cmb_SPLITTER == 0)
                        {
                            boolError = true;
                            mensajeError += "El Elemento SPLITTER no fue escogido, por favor seleccione.\n";
                        }
                        if (boolError && puertosDisponibles == "" && puertosDisponibles < 0)
                        {
                            boolError = true;
                            mensajeError += "Las interfaces disponibles no fueron cargadas. \n";
                        }
                        if (!boolError && puertosDisponibles == 0)
                        {
                            boolError = true;
                            mensajeError += "El Splitter no tiene interfaces disponibles. \n";
                        }

                        parametros = {id: id_factibilidad, id_olt: id_olt, id_linea: id_linea, id_caja: cmb_CAJA, id_splitter: cmb_SPLITTER};
//                        boolError = true; 
                        if (!boolError)
                        {
                            connFactibilidad.request({
                                url: urlNuevaFactibilidad,
                                method: 'post',
                                timeout: 120000,
                                params: parametros,
                                success: function(response) {
                                    var text = response.responseText;
                                    if (text == "Se modifico Correctamente el detalle de la Solicitud de Factibilidad")
                                    {
                                        cierraVentanaIngresoFactibilidad();
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
//                                             Ext.Msg.alert('Alerta', 'Error: ' + text);
                                    }
                                },
                                failure: function(result) {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: result.responseText,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
//                                                                                 Ext.Msg.alert('Alerta', result.responseText);
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
//                            Ext.Msg.alert('Error' ,'Error: ' + mensajeError);
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaIngresoFactibilidad();
                    }
                }
            ]
        });

        winIngresoFactibilidad = Ext.widget('window', {
            title: 'Ingreso de Factibilidad',
//            width: 640,
//            height:630,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelIngresoFactibilidad]
        });
    }

    winIngresoFactibilidad.show();
}

function cierraVentanaIngresoFactibilidad() {
    winIngresoFactibilidad.close();
    winIngresoFactibilidad.destroy();
}

function setInfoCaja(idCaja, infoCaja)
{
    var idElemento = 0;
    var nombreElemento = "";
    var idOlt = 0;
    var olt = "";
    var idLinea = 0;
    var linea = "";

    if (infoCaja.idElemento > 0) {
        idElemento = infoCaja.idElemento;
        nombreElemento = infoCaja.nombreElemento;
        idOlt = infoCaja.idOlt;
        olt = infoCaja.olt;
        idLinea = infoCaja.idLinea;
        linea = infoCaja.linea;
    }

    for (var i = 0; i < storeElementos.getCount(); i++)
    {
        if (storeElementos.getAt(i).data.idElemento == idCaja) {
            storeElementos.getAt(i).data.idElemento = idElemento;
            storeElementos.getAt(i).data.nombreElemento = nombreElemento;
            storeElementos.getAt(i).data.idOlt = idOlt;
            storeElementos.getAt(i).data.olt = olt;
            storeElementos.getAt(i).data.idLinea = idLinea;
            storeElementos.getAt(i).data.linea = linea;
            console.log(storeElementos.getAt(i).data);
            break;
        }
    }
}

function getInfoCaja(idCaja)
{
    if (storeElementos.getCount() >= 1) {
        var infoCaja = new Object();

        for (var i = 0; i < storeElementos.getCount(); i++)
        {
            if (storeElementos.getAt(i).data.idElemento == idCaja) {
                console.log(storeElementos.getAt(i).data);
                infoCaja['idElemento'] = storeElementos.getAt(i).data.idElemento;
                infoCaja['nombreElemento'] = storeElementos.getAt(i).data.nombreElemento;
                infoCaja['idOlt'] = storeElementos.getAt(i).data.idOlt;
                infoCaja['olt'] = storeElementos.getAt(i).data.olt;
                infoCaja['idLinea'] = storeElementos.getAt(i).data.idLinea;
                infoCaja['linea'] = storeElementos.getAt(i).data.linea;
                break;
            }
        }

        return Ext.JSON.encode(infoCaja);
    } else {
        return "";
    }
}


/************************************************************************ */
/**************************** FACTIBILIDAD ****************************** */
/************************************************************************ */
function showFactibilidad(rec)
{
    winFactibilidad = "";
    formPanelFactibilidad = "";
    // store.load();
    if (!winFactibilidad)
    {
        //******** html campos requeridos...
        var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
        CamposRequeridos = Ext.create('Ext.Component', {
            html: iniHtmlCamposRequeridos,
//            width: 600,
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
                //anchor : '65%',
                //layout: 'anchor'
        });

        formPanelFactibilidad = Ext.create('Ext.form.Panel', {
//            width:600,
//            height:800,
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
//                         {
//                             xtype: 'textfield',
//                             fieldLabel: 'Coordenadas',
//                             name: 'info_coordenadas',
//                             id: 'info_coordenadas',
//                             value: rec.get("coordenadas"),
//                             allowBlank: false,
//                             readOnly : true
//                         },
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
                            xtype: 'textfield',
                            fieldLabel: 'Olt',
                            name: 'olt_servicio',
                            id: 'olt_servicio',
                            value: rec.get("olt"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Linea',
                            name: 'linea_servicio',
                            id: 'linea_servicio',
                            value: rec.get("linea"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Caja',
                            name: 'caja_servicio',
                            id: 'caja_servicio',
                            value: rec.get("caja"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Splitter',
                            name: 'splitter_servicio',
                            id: 'splitter_servicio',
                            value: rec.get("splitter"),
                            allowBlank: false,
                            readOnly: true
                        }
                    ]
                }
            ],
            buttons: [
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
//            width: 640,
//            height:630,
//            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelFactibilidad]
        });
    }

    winFactibilidad.show();
}

function cierraVentanaFactibilidad() {
    winFactibilidad.close();
    winFactibilidad.destroy();
}
