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

/**
 * Función para validar un objeto JSON.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 09-04-2019 | Versión Inicial.
 * @return {boolean}
 *
 */
function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

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
        Ext.Msg.confirm('Alerta','Estas coordenadas son incorrectas!!');
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
        };

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
                        },
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
                            if(rec.get('esSolucion') === 'S')
                            {
                                $.ajax({
                                    type   : "POST",
                                    url    : urlAnularRechazarSol,
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
                                               msg: 'Rechazando Servicio de la Solución',
                                               progressText: 'Rechazando...',
                                               width:300,
                                               wait:true,
                                               waitConfig: {interval:200}
                                            });
                                    },
                                    success: function(data)
                                    {
                                        if(data.status === 'OK')
                                        {
                                            cierraVentanaRechazarOrden_Factibilidad();

                                            var html = '';

                                            if(data.arrayServiciosEliminados.length > 0)
                                            {
                                                html += '<br><br>Los siguientes Servicios fueron rechazados por acción realizada.';
                                                html += '<br><ul>';
                                                $.each(data.arrayServiciosEliminados, function(i, item)
                                                {
                                                    html += '<li><i class="fa fa-long-arrow-right" aria-hidden="true"></i>&nbsp'+item+'</li>';
                                                });
                                                html += '</ul>';
                                            }

                                            var text = "Servicio Rechazado correctamente"+html;

                                            Ext.Msg.alert('Mensaje', text, function(btn)
                                            {
                                                if (btn == 'ok')
                                                {
                                                    store.load();
                                                }
                                            });
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Error', data.mensaje);
                                        }
                                    }
                                });
                            }
                            else
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

/**
 * obtienInfoPersonaContacto, obtiene la informacion del contacto
 *
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 17-04-2016
 * @since 1.0
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
function obtienInfoPersonaContacto(arrayParametros) {

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
} //obtienInfoPersonaContacto
/************************************************************************ */
/**************************** FACTIBILIDAD ****************************** */
/************************************************************************ */
function showPreFactibilidad(rec)
{
    var arrayParametros = [];
    arrayParametros['strPrefijoEmpresa'] = rec.get("strPrefijoEmpresa");
    arrayParametros['strDescripcionTipoRol'] = 'Contacto';
    arrayParametros['strEstadoTipoRol'] = 'Activo';
    arrayParametros['strDescripcionRol'] = 'Contacto Tecnico';
    arrayParametros['strEstadoRol'] = 'Activo';
    arrayParametros['strEstadoIER'] = 'Activo';
    arrayParametros['strEstadoIPER'] = 'Activo';
    arrayParametros['strEstadoIPC'] = 'Activo';
    arrayParametros['intIdPersonaEmpresaRol'] = rec.get("intIdPersonaEmpresaRol");
    arrayParametros['intIdPunto'] = rec.get("id_punto");
    arrayParametros['idStore'] = 'storeContactoPreFac';
    arrayParametros['strJoinPunto'] = '';
    strDefaultType = "hiddenfield";
    strXtype = "hiddenfield";
    strNombres = '';
    strApellidos = '';
    storeRolContacto = '';
    storeRolContactoPunto = '';
    //Si la empresa es TN envia a buscar la informacion del contacto
    if ("TN" === rec.get("strPrefijoEmpresa")) {
        var arrayPersonaContacto = [];
        storeRolContacto = obtienInfoPersonaContacto(arrayParametros);
        arrayParametros['intIdPersonaEmpresaRol'] = '';
        arrayParametros['idStore'] = 'storeContactoPuntoShowPreFac';
        arrayParametros['strJoinPunto'] = 'PUNTO';
        storeRolContactoPunto = obtienInfoPersonaContacto(arrayParametros);
        strNombres = arrayPersonaContacto['strNombres'];
        strApellidos = arrayPersonaContacto['strApellidos'];
        strDefaultType = "textfield";
        strXtype = "fieldset";
    }
    var boolEsEdificio = true;
    var boolDependeDeEdificio = true;
    var boolNombreEdificio = true;

    if ("S" === rec.get("strEsEdificio")) {
        boolEsEdificio = false;
    }
    if ("S" === rec.get("strDependeDeEdificio")) {
        boolDependeDeEdificio = false;
    }
    if (false === boolDependeDeEdificio || false === boolEsEdificio) {
        boolNombreEdificio = false;
    }
    winPreFactibilidad = "";
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
                //anchor : '65%',
                //layout: 'anchor'
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
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Tipo de Red',
                            name: 'tipoRed',
                            id: 'tipoRed',
                            value: rec.get("strTipoRed"),
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
                                    cierraVentanaPreFactibilidad();
                                    if (text == "Se modifico Correctamente el detalle de la Solicitud de Factibilidad")
                                    {
                                        Ext.Msg.alert('Mensaje', text, function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                            }
                                        });
                                    }
                                    else 
                                    {
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

/**
 * Funcion que permite programar y asignar responsable a RADIO desde el grid técnico.
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 23-05-2019 | Versión Inicial.
 *
 */

function showProgramarRadio(rec) {

    let tareasJS;

    var connAsignarResponsable = new Ext.data.Connection({
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

    var connTareas = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function(con, opt) {
                    Ext.MessageBox.show({
                        msg: 'Cargando Tareas',
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

    var winAsignacionIndividual = "";
    var formPanelAsignacionIndividual = "";

    if (!winAsignacionIndividual)
    {
        var id_servicio     = rec.get("idServicio");
        var id_factibilidad = rec.get("idSolicitud");

        //******** html vacio...
        var iniHtmlVacio1 = '';
        let Vacio1 = Ext.create('Ext.Component', {
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
            items:
                [
                    {
                        xtype: 'panel',
                        border: false,
                        layout: {type: 'hbox', align: 'stretch'},
                        items: [
                            {
                                xtype: 'fieldset',
                                id: 'client-data-fieldset',
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
                                        fieldLabel: 'Login',
                                        name: 'info_login',
                                        id: 'info_login',
                                        value: rec.get("login"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Descripción Factura',
                                        name: 'descripcion_factura',
                                        id: 'descripcion_factura',
                                        value: rec.get("descripcionPresentaFactura"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Ciudad',
                                        name: 'info_ciudad',
                                        id: 'info_ciudad',
                                        value: rec.get("nombreCanton"),
                                        allowBlank: false,
                                        readOnly: true
                                    }
                                ]
                            }
                        ]
                    },
                    Vacio1
                ],
            buttons: [
                {
                    text: 'Guardar',
                    handler: function() {
                        let param;
                        let id;
                        var boolError = true;
                        var boolErrorTecnico = false;
                        var idPerTecnico = 0;
                        const origen = 'local';

                        if (origen == "local")
                        {
                            id = rec.data.idSolicitud;
                            param = rec.data.idSolicitud;

                            if (prefijoEmpresa == "TN")
                            {
                                rec.data.descripcionSolicitud = "Solicitud Planificacion";
                                idPerTecnico = Ext.getCmp('cmb_tecnico').value;
                                //FIXME: Aqui podria darse error.
                                if (!idPerTecnico)
                                {
                                    boolErrorTecnico = true;
                                }
                            }
                        } else
                        {
                            boolError = false;
                            Ext.Msg.alert('Alerta', 'No hay opcion escogida');
                        }

                        if (boolErrorTecnico)
                        {
                            Ext.Msg.alert('Alerta', 'Por favor seleccione el técnico asignado<br><br>');
                        } else
                        {
                            if (boolError)
                            {
                                var paramResponsables = '';
                                var mensajeError = "";
                                for (let i in tareasJS)
                                {
                                    var banderaEscogido = "empleado";
                                    var codigoEscogido = "";
                                    var tituloError = "";
                                    var idPersona = "0";
                                    var idPersonaEmpresaRol = "0";
                                    if (banderaEscogido === "empleado")
                                    {
                                        tituloError = "Empleado ";
                                        codigoEscogido = Ext.getCmp('cmb_empleado_' + i).value;
                                    }

                                    if (codigoEscogido && codigoEscogido != "")
                                    {
                                        paramResponsables = paramResponsables + +tareasJS[i]['idTarea'] + "@@" + banderaEscogido + "@@" + codigoEscogido + "@@" +
                                            idPersona + "@@" + idPersonaEmpresaRol;
                                        if (i < (tareasJS.length - 1))
                                        {
                                            paramResponsables = paramResponsables + '|';
                                        }
                                    } else
                                    {
                                        mensajeError += "Tarea:" + tareasJS[i]['nombreTarea'] + " -- Combo: " + tituloError + "<br>";
                                    }
                                }//FIN FOR
                                var txtObservacion = Ext.getCmp('txtObservacionPlanf').value;
                                var fechaProgramacion = Ext.getCmp('fechaProgramacion').value;
                                var ho_inicio = Ext.getCmp('ho_inicio_value').value;
                                var ho_fin = Ext.getCmp('ho_fin_value').value;
                                var id_factibilidad = rec.get("idSolicitud");
                                boolError = false;
                                mensajeError = "";

                                if (!id_factibilidad || id_factibilidad == "" || id_factibilidad == 0)
                                {
                                    boolError = true;
                                    mensajeError += "El id del Detalle Solicitud no existe.\n";
                                }
                                if (!fechaProgramacion || fechaProgramacion == "" || fechaProgramacion == 0)
                                {
                                    boolError = true;
                                    mensajeError += "La fecha de Programacion no fue seleccionada, por favor seleccione.\n";
                                }
                                if (!ho_inicio || ho_inicio == "" || ho_inicio == 0)
                                {
                                    boolError = true;
                                    mensajeError += "La hora de inicio no fue seleccionada, por favor seleccione.\n";
                                }
                                if (!ho_fin || ho_fin == "" || ho_fin == 0)
                                {
                                    boolError = true;
                                    mensajeError += "La hora de fin no fue seleccionada, por favor seleccione.\n";
                                }
                                if (!txtObservacion || txtObservacion == "" || txtObservacion == 0)
                                {
                                    boolError = true;
                                    mensajeError += "La observacion no fue ingresada, por favor ingrese.\n";
                                }

                                if (!boolError)
                                {
                                    let strMensaje = "Se asignará el responsable. Desea continuar?";
                                    if (paramResponsables == "")
                                    {
                                        strMensaje = "Se Coordinará la planificación. Desea continuar?";
                                    }

                                    Ext.Msg.confirm('Alerta', strMensaje, function(btn) {
                                        if (btn === 'yes')
                                        {
                                            connAsignarResponsable.request({
                                                url: "../../planificacion/planificar/coordinar/programar",
                                                method: 'post',
                                                params: {
                                                    origen: origen,
                                                    id: id,
                                                    param: param,
                                                    paramResponsables: paramResponsables,
                                                    idPerTecnico: idPerTecnico,
                                                    fechaProgramacion: fechaProgramacion,
                                                    ho_inicio: ho_inicio,
                                                    ho_fin: ho_fin,
                                                    observacion: txtObservacion,
                                                    opcion: 0,
                                                    tipoEsquema: 1,
                                                    idIntWifiSim: JSON.stringify(rec.data.idIntWifiSim),
                                                    idIntCouSim: JSON.stringify(rec.data.idIntCouSim),
                                                    arraySimultaneos: JSON.stringify(rec.data.arraySimultaneos)
                                                },
                                                success: function(response) {
                                                    var text        = response.responseText;
                                                    var intPosicion = text.indexOf("Correctamente");

                                                    if (text == "Se asignaron la(s) Tarea(s) Correctamente." ||
                                                        text == "Se coordino la solicitud" || intPosicion !== -1)
                                                    {
                                                        cierraVentanaAsignacionIndividual(winAsignacionIndividual);
                                                        Ext.Msg.alert('Mensaje', text, function(btn) {
                                                            if (btn == 'ok') {
                                                                store.load();
                                                            }
                                                        });
                                                    } else {
                                                        var mm = Ext.Msg.alert('Alerta', text);
                                                        Ext.defer(function() {
                                                            mm.toFront();
                                                        }, 50);
                                                    }
                                                },
                                                failure: function(result) {
                                                    Ext.Msg.alert('Alerta', result.responseText);
                                                }
                                            });
                                        }
                                    });
                                } else
                                {
                                    Ext.Msg.alert('Alerta', mensajeError);
                                }
                            }
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAsignacionIndividual(winAsignacionIndividual);
                    }
                }
            ]
        });

        var combo_tecnicos = Ext.create('Ext.Component', {
            html: ""
        });

        if (prefijoEmpresa == "TN" || prefijoEmpresa == "TNP")
        {
            var storeTecnicos = new Ext.data.Store
            ({
                total: 'total',
                pageSize: 25,
                listeners: {
                },
                proxy:
                    {
                        type: 'ajax',
                        method: 'post',
                        url: '../../planificacion/planificar/asignar_responsable/getTecnicos',
                        reader:
                            {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'encontrados'
                            },
                        extraParams: {
                            query: '',
                            'tipo_esquema': 1
                        },
                        actionMethods:
                            {
                                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                            }
                    },
                fields:
                    [
                        {name: 'id_tecnico', mapping: 'idPersonaEmpresaRol'},
                        {name: 'nombre_tecnico', mapping: 'info_adicional'},
                    ],
                autoLoad: true
            });

            combo_tecnicos = new Ext.form.ComboBox({
                id: 'cmb_tecnico',
                name: 'cmb_tecnico',
                fieldLabel: `Ingeniero Radio`,
                anchor: '100%',
                queryMode: 'remote',
                emptyText: `Seleccione Ingeniero Radio`,
                width: 350,
                store: storeTecnicos,
                displayField: 'nombre_tecnico',
                valueField: 'id_tecnico',
                layout: 'anchor',
                disabled: false,
                tabIndex: 5
            });
        }

        connTareas.request({
            method: 'POST',
            url: "../../planificacion/factibilidad/factibilidad_instalacion/getTareasByProcesoAndTarea",
            params: {servicioId: id_servicio, id_solicitud: id_factibilidad, nombreTarea: 'todas', estado: 'Activo'},
            success: function(response) {
                let data = JSON.parse(response.responseText.trim());
                if (data)
                {
                    let totalTareas = data.total;
                    if (totalTareas > 0)
                    {
                        tareasJS = data.encontrados;
                        for (let i in tareasJS)
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
                                "    url : '../../planificacion/planificar/asignar_responsable/getEmpleados'," +
                                "   reader: {" +
                                "        type: 'json'," +
                                "       totalProperty: 'total'," +
                                "        root: 'encontrados'" +
                                "  }" +
                                "  }" +
                                " });    ");

                            var combo_empleados = new Ext.form.ComboBox({
                                id: 'cmb_empleado_' + i,
                                name: 'cmb_empleado_' + i,
                                fieldLabel: "Empleados",
                                anchor: '100%',
                                queryMode: 'remote',
                                width: 350,
                                emptyText: 'Seleccione Empleado',
                                store: eval("storeEmpleados_" + i),
                                displayField: 'nombre_empleado',
                                valueField: 'id_empleado',
                                layout: 'anchor',
                                disabled: false,
                                tabIndex: 4
                            });

                            //******** html vacio...
                            var iniHtmlVacio = '';
                            var Vacio = Ext.create('Ext.Component', {
                                html: iniHtmlVacio,
                                width: 350,
                                padding: 8,
                                layout: 'anchor',
                                style: {color: '#000000'}
                            });

                            var feIni = new Date();
                            var hoIni = "00:00";
                            var hoFin = "00:30";

                            DTFechaProgramacion = Ext.create('Ext.data.fecha', {
                                id: 'fechaProgramacion',
                                name: 'fechaProgramacion',
                                fieldLabel: '* Fecha',
                                minValue: new Date(),
                                value: feIni,
                                labelStyle: "color:red;"
                            });
                            var THoraInicio = Ext.create('Ext.form.TimeField', {
                                fieldLabel: '* Hora Inicio',
                                format: 'H:i',
                                id: 'ho_inicio_value',
                                name: 'ho_inicio_value',
                                minValue: '00:01 AM',
                                maxValue: '22:59 PM',
                                increment: 30,
                                value: hoIni,
                                editable: false,
                                labelStyle: "color:red;",
                                listeners: {
                                    select: {fn: function(valorTime, value) {
                                            var valueEscogido = valorTime.getValue();
                                            var valueEscogido2 = new Date(valueEscogido);
                                            var valueEscogidoAumentMili = valueEscogido2.setMinutes(valueEscogido2.getMinutes() + 30);
                                            var horaTotal = new Date(valueEscogidoAumentMili);
                                            var h = (horaTotal.getHours() < 10 ? "0" + horaTotal.getHours() : horaTotal.getHours());
                                            var m = (horaTotal.getMinutes() < 10 ? "0" + horaTotal.getMinutes() : horaTotal.getMinutes());
                                            var horasTotalFormat = h + ":" + m;
                                            Ext.getCmp('ho_fin_value').setMinValue(horaTotal);
                                            $('input[name="ho_fin_value"]').val(horasTotalFormat);
                                        }}
                                }
                            });
                            var THoraFin = Ext.create('Ext.form.TimeField', {
                                fieldLabel: '* Hora Fin',
                                format: 'H:i',
                                id: 'ho_fin_value',
                                name: 'ho_fin_value',
                                minValue: '00:30 AM',
                                maxValue: '23:59 PM',
                                increment: 30,
                                value: hoFin,
                                editable: false,
                                labelStyle: "color:red;"
                            });
                            var txtObservacionPlanf = Ext.create('Ext.form.TextArea',
                                {
                                    fieldLabel: '',
                                    name: 'txtObservacionPlanf',
                                    id: 'txtObservacionPlanf',
                                    value: rec.get("observacion"),
                                    allowBlank: false,
                                    width: 300,
                                    height: 150,
                                    tabIndex: 6
                                });
                            var container = Ext.create('Ext.container.Container',
                                {
                                    layout: {
                                        type: 'hbox'
                                    },
                                    width: 725,
                                    items: [
                                        {
                                            xtype: 'panel',
                                            border: false,
                                            layout: {type: 'hbox', align: 'stretch'},
                                            items: [
                                                {
                                                    xtype: 'fieldset',
                                                    defaultType: 'textfield',
                                                    style: "font-weight:bold; margin-bottom: 15px; border-right:none",
                                                    layout: 'anchor',
                                                    defaults:
                                                        {
                                                            width: '350px',
                                                            border: false,
                                                            frame: false
                                                        },
                                                    items: [DTFechaProgramacion,
                                                        THoraInicio,
                                                        THoraFin,
                                                        hidden_tarea,
                                                        text_tarea,
                                                        combo_empleados,
                                                        formPanel,
                                                        Vacio,
                                                        combo_tecnicos,
                                                        Vacio]
                                                },
                                                {
                                                    xtype: 'fieldset',
                                                    style: "margin-bottom: 15px; border-left:none",
                                                    defaults:
                                                        {
                                                            width: '350px',
                                                            border: true,
                                                            frame: false

                                                        },
                                                    items: [
                                                        {html: "Observación de Planificación:", border: false, width: 325},
                                                        txtObservacionPlanf]
                                                }]
                                        }]
                                });
                            formPanelAsignacionIndividual.items.add(container);
                            combo_tecnicos.setVisible(true);

                            Ext.getCmp('cmb_empleado_' + i).setVisible(true);

                            container.doLayout();
                            formPanelAsignacionIndividual.doLayout();
                        }

                        winAsignacionIndividual = Ext.widget('window', {
                            title: 'Formulario Asignacion Individual - RADIO',
                            layout: 'fit',
                            resizable: false,
                            modal: true,
                            closable: false,
                            items: [formPanelAsignacionIndividual]
                        });
                        winAsignacionIndividual.show();
                    } else
                    {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: "No se han podido obtener tareas asociadas a este servicio. Por favor informe a Sistemas.",
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                } else {
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

function cierraVentanaAsignacionIndividual(winAsignacionIndividual) {
    winAsignacionIndividual.close();
    winAsignacionIndividual.destroy();
}

/**
 * Funcion que permite generar factibilidad a IPCCL2 desde el grid técnico
 *
 * @author Pablo Pin <ppin@telconet.ec>
 * @version 1.0 23-05-2019 | Versión Inicial.
 *
 */

function showIngresoFactibilidadWifiL2(rec)
{
    if (rec.get('arraySolicitudWifi')) 
    {
        const idSolicitudWifi = parseInt(rec.get('arraySolicitudWifi').intIdDetalleSolicitud);

        $.ajax({
            type: "POST",
            url: url_ajaxGridL2,
            data: {
                txtEstado        : 'PreFactibilidad',
                txtLogin         : rec.get('login')
            },
            success: function (res) {
    
                const objNodoWifi = res.encontrados.filter((el) => parseInt(el.idSolicitud) === idSolicitudWifi)[0];
                var idElementoWifi = objNodoWifi.idElemento;
                const strPrefijoEmpresa = rec.get('prefijoEmpresa');
                var arrayParametros = {};
                var interfaceOdf;
                arrayParametros.strPrefijoEmpresa       = strPrefijoEmpresa;
                arrayParametros.strDescripcionTipoRol   = 'Contacto';
                arrayParametros.strEstadoTipoRol        = 'Activo';
                arrayParametros.strDescripcionRol       = 'Contacto Tecnico';
                arrayParametros.strEstadoRol            = 'Activo';
                arrayParametros.strEstadoIER            = 'Activo';
                arrayParametros.strEstadoIPER           = 'Activo';
                arrayParametros.strEstadoIPC            = 'Activo';
                arrayParametros.intIdPersonaEmpresaRol  = objNodoWifi.idPersonaEmpresaRol;
                arrayParametros.intIdPunto              = objNodoWifi.idPunto;
                arrayParametros.idStore                 = 'storeContactoIngFac';
                arrayParametros.strJoinPunto            = '';
    
                strDefaultType                          = "hiddenfield";
                strXtype                                = "hiddenfield";
                strNombres                              = '';
                strApellidos                            = '';
                strErrorMetraje                         = '';
                boolCheckObraCivil                      = false;
                boolCheckObservacionRegeneracion        = false;
                storeRolContacto                        = '';
                storeRolContactoPunto                   = '';

                if ("TN" === strPrefijoEmpresa) {

                    storeRolContacto = obtienInfoPersonaContacto(arrayParametros);
                    arrayParametros.intIdPersonaEmpresaRol = '';
                    arrayParametros.idStore = 'storeContactoPuntoIngFac';
                    arrayParametros.strJoinPunto = 'PUNTO';
                    storeRolContactoPunto = obtienInfoPersonaContacto(arrayParametros);
                    strDefaultType = "textfield";
                    strXtype = "fieldset";
                }
                var boolEsEdificio = true;
                var boolDependeDeEdificio = true;
                var boolNombreEdificio = true;
                if ("S" === objNodoWifi.strEsEdificio) {
                    boolEsEdificio = false;
                }
                if ("S" === objNodoWifi.strDependeDeEdificio) {
                    boolDependeDeEdificio = false;
                }
                if (false === boolDependeDeEdificio || false === boolEsEdificio) {
                    boolNombreEdificio = false;
                }
                var strNombreTipoElemento = "SPLITTER";
                var strNombreElementoPadre = "Olt";
                if ("TN" === strPrefijoEmpresa) {
                    strNombreTipoElemento = "CASSETTE";
                    strNombreElementoPadre = "Switch";
                    if ("true" == objNodoWifi.strObraCivil)
                    {
                        boolCheckObraCivil = true;
                    }
                    if ("true" == objNodoWifi.strPermisosRegeneracion)
                    {
                        boolCheckObservacionRegeneracion = true;
                    }
                }
    
                winIngresoFactibilidad = "";
                formPanelInresoFactibilidad = "";
                if (!winIngresoFactibilidad)
                {
                    //******** html campos requeridos...
                    var iniHtmlCamposRequeridos = '<p style="text-align: right; color:#ff0000; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
                    CamposRequeridos = Ext.create('Ext.Component', {
                        html: iniHtmlCamposRequeridos,
                        padding: 1,
                        layout: 'anchor',
                        style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
                    });
    
                    var storeElementosFactibles = new Ext.data.Store
                    ({
                        total: 'total',
                        pageSize: 200,
                        proxy:
                            {
                                timeout: 60000,
                                type: 'ajax',
                                method: 'post',
                                url: getElementosFactibles,
                                reader:
                                    {
                                        type: 'json',
                                        totalProperty: 'total',
                                        root: 'encontrados'
                                    },
                                extraParams:
                                    {
                                        idServicio : objNodoWifi.idServicio
                                    }
                            },
                        fields:
                            [
                                {name: 'idElementoFactible', mapping: 'idElementoFactible'},
                                {name: 'nombreElementoFactible', mapping: 'nombreElementoFactible'}
                            ],
                        listeners:
                            {
                                load: function(store, records)
                                {
                                    let mensaje = store.proxy.reader.jsonData.mensaje.trim();
                                    let strNotificar = '';
                                    const objDept = {
                                        radio:  "<b><br/>NOTIFICAR A RADIO</b>",
                                        l2:     "<b><br/>NOTIFICAR A IPCCL2</b>"
                                    };
    
                                    if (rec.get('tipoEsquema'))
                                    {
                                        strNotificar = parseInt(objNodoWifi.tipoEsquema) === 1 ? objDept.radio : objDept.l2;
    
                                        if (mensaje.startsWith('NO EXISTE DISPONIBILIDAD')
                                            || mensaje.startsWith('NO EXISTEN PUERTOS')
                                            || mensaje.startsWith('EL ELEMENTO NO TIENE'))
                                        {
                                            mensaje = mensaje + strNotificar;
                                        }
                                    }
    
                                    if (mensaje != 'OK')
                                    {
                                        Ext.MessageBox.alert('Notificación', mensaje);
                                    }
                                }
                            },
                    });
    
    
                    var storeInterfacesElemento = new Ext.data.Store({
                        pageSize: 100,
                        proxy: {
                            type: 'ajax',
                            timeout: 400000,
                            url: getInterfacesPorElemento,
                            reader: {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'encontrados'
                            }
                        },
                        fields:
                            [
                                {name: 'idInterface', mapping: 'idInterface'},
                                {name: 'nombreInterface', mapping: 'nombreInterface'}
                            ]
                    });
    
                    var storeElementosNodoWifi = new Ext.data.Store({
                        total: 'total',
                        autoDestroy: true,
                        autoLoad: false,
                        listeners: {
                            load: function() {
    
                            }
                        },
                        proxy: {
                            type: 'ajax',
                            url: url_getElementosWifi,
                            timeout: 120000,
                            reader: {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'encontrados'
                            },
                            listeners: {
                                exception: function(proxy, response, options) {
                                    Ext.MessageBox.alert('Error', "Favor ingrese el nombre");
                                }
                            },
                            actionMethods: {
                                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                            },
                            extraParams: {
                                nombre: this.nombreElemento,
                                tipoElemento: 'NODO WIFI',
                                idCanton: objNodoWifi.idCanton
                            }
                        },
                        fields:
                            [
                                {name: 'idElemento', mapping: 'idElemento'},
                                {name: 'nombreElemento', mapping: 'nombreElemento'},
                                {name: 'modelo', mapping: 'modelo'}
                            ],
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
                                    Ext.MessageBox.alert('Error', "Favor ingrese un nombre de caja");
                                }
                            },
                            actionMethods: {
                                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                            },
                            extraParams: {
                                nombre: this.strNombreElemento
                            }
                        },
                        fields:
                            [
                                {name: 'intIdElemento', mapping: 'intIdElemento'},
                                {name: 'strNombreElemento', mapping: 'strNombreElemento'}
                            ]
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
                                elemento: strNombreTipoElemento
                            }
                        },
                        fields:
                            [
                                {name: 'idElemento', mapping: 'idElemento'},
                                {name: 'nombreElemento', mapping: 'nombreElemento'}
                            ]
                    });
    
                    /*modelClaseTipoMedio*/
                    Ext.define('modelClaseTipoMedio', {
                        extend: 'Ext.data.Model',
                        fields: [
                            {name: 'idClaseTipoMedio', type: 'int'},
                            {name: 'nombreClaseTipoMedio', type: 'string'}
                        ]
                    });
    
                    //Store de storeClaseTipoMedio
                    storeClaseTipoMedio = Ext.create('Ext.data.Store', {
                        autoLoad: true,
                        model: "modelClaseTipoMedio",
                        proxy: {
                            type: 'ajax',
                            url: urlGetClaseTipoMedio,
                            reader: {
                                type: 'json',
                                root: 'encontrados'
                            },
                            extraParams: {
                                tipoMedioId: objNodoWifi.txtIdUltimaMilla,
                                estado: 'Activo'
                            }
                        }
                    });
    
                    Ext.define('modelInterfaceElemento', {
                        extend: 'Ext.data.Model',
                        fields: [
                            {name: 'intIdInterfaceElemento', type: 'int'},
                            {name: 'strNombreInterfaceElemento', type: 'string'}
                        ]
                    });
    
                    storePuertos = Ext.create('Ext.data.Store', {
                        model: "modelInterfaceElemento",
                        autoLoad: false,
                        proxy: {
                            type: 'ajax',
                            url: urlInterfacesByElemento,
                            reader: {
                                type: 'json',
                                root: 'encontrados'
                            },
                            extraParams: {
                                intIdElemento: '',
                                strEstado: ''
                            }
                        }
                    });
    
                    var storeHilosDisponibles = new Ext.data.Store({
                        autoLoad: false,
                        proxy: {
                            type: 'ajax',
                            url: getHilosDisponibles,
                            extraParams: {
                                idElemento: '',
                                estadoInterface: 'connected',
                                estadoInterfaceNotConect: 'not connect',
                                estadoInterfaceReserved: 'not connect',
                                strBuscaHilosServicios: 'BUSCA_HILOS_SERVICIOS',
                                intIdPunto: objNodoWifi.idPunto
                            },
                            reader: {
                                type: 'json',
                                root: 'encontrados'
                            }
                        },
                        fields:
                            [
                                {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                                {name: 'idInterfaceElementoOut', mapping: 'idInterfaceElementoOut'},
                                {name: 'colorHilo', mapping: 'colorHilo'},
                                {name: 'numeroHilo', mapping: 'numeroHilo'},
                                {name: 'numeroColorHilo', mapping: 'numeroColorHilo'}
                            ]
                    });
    
                    cbxPuertos = Ext.create('Ext.form.ComboBox', {
                        id: 'cbxPuertos',
                        name: 'cbxPuertos',
                        store: storeHilosDisponibles,
                        queryMode: 'local',
                        fieldLabel: 'Hilos Disponibles',
                        displayField: 'numeroColorHilo',
                        valueField: 'idInterfaceElementoOut',
                        editable: false,
                        hidden: true,
                        listeners:
                            {
                                select: function(combo)
                                {
                                    var objeto = combo.valueModels[0].raw;
                                    Ext.Ajax.request({
                                        url: ajaxJsonPuertoOdfByHilo,
                                        method: 'post',
                                        async: false,
                                        params: {idInterfaceElementoConector: objeto.idInterfaceElemento},
                                        success: function(response)
                                        {
                                            var json = Ext.JSON.decode(response.responseText);
                                            interfaceOdf = json.idInterfaceElemento;
                                            var arrayParamDistribucionTN = [];
                                            arrayParamDistribucionTN.strUrlInfoCaja = urlInfoCaja;
                                            arrayParamDistribucionTN.intIdElementoContenedor = Ext.getCmp('cbxIdElementoCaja').value;
                                            arrayParamDistribucionTN.strIdElementoDistribucion = objeto.idInterfaceElemento;
                                            arrayParamDistribucionTN.strTipoBusqueda = 'INTERFACE';
                                            arrayParamDistribucionTN.strNombreElementoPadre = strNombreElementoPadre.toUpperCase();
                                            arrayParamDistribucionTN.strNombreCaja = Ext.getCmp('cbxIdElementoCaja').getRawValue();
                                            arrayParamDistribucionTN.strNombreElemento = Ext.getCmp('cbxElementoPNivel').getRawValue();
                                            arrayParamDistribucionTN.strNombreTipoElemento = strNombreTipoElemento;
                                            arrayParamDistribucionTN.strNombreTipoMedio = objNodoWifi.strNombreTipoMedio;
                                            arrayParamDistribucionTN.strPrefijoEmpresa = strPrefijoEmpresa;
                                            arrayParamDistribucionTN.strUrlDisponibilidadElemento = urlDisponibilidadElemento;
                                            arrayParamDistribucionTN.strUrlCalculaMetraje = urlCalculaMetraje;
                                            arrayParamDistribucionTN.intIdPunto = objNodoWifi.idPunto;
                                            arrayParamDistribucionTN.winIngresoFactibilidad = winIngresoFactibilidad;
    
                                        },
                                        failure: function(result)
                                        {
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
    
    
                                }
                            }
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
                                                value: objNodoWifi.nombrePunto,
                                                allowBlank: false,
                                                readOnly: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Login',
                                                name: 'info_login',
                                                id: 'info_login',
                                                value: objNodoWifi.login,
                                                allowBlank: false,
                                                readOnly: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Ciudad',
                                                name: 'info_ciudad',
                                                id: 'info_ciudad',
                                                value: objNodoWifi.nombreCanton,
                                                allowBlank: false,
                                                readOnly: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Direccion',
                                                name: 'info_direccion',
                                                id: 'info_direccion',
                                                value: objNodoWifi.direccion,
                                                allowBlank: false,
                                                readOnly: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Sector',
                                                name: 'info_nombreSector',
                                                id: 'info_nombreSector',
                                                value: objNodoWifi.nombreSector,
                                                allowBlank: false,
                                                readOnly: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Latitud',
                                                name: 'strLatitud',
                                                id: 'intIdLatitud',
                                                value: objNodoWifi.latitud,
                                                readOnly: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Longitud',
                                                name: 'strLongitud',
                                                id: 'intIdLongitud',
                                                value: objNodoWifi.longitud,
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
                                                value: objNodoWifi.strNombreEdificio,
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
                                                        value: objNodoWifi.nombreProducto,
                                                        allowBlank: false,
                                                        readOnly: true
                                                    },
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: 'Tipo Orden',
                                                        name: 'tipo_orden_servicio',
                                                        id: 'tipo_orden_servicio',
                                                        value: objNodoWifi.tipoOrden,
                                                        allowBlank: false,
                                                        readOnly: true
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: strXtype,
                                                title: 'Datos Contacto Tecnico',
                                                style: "font-weight:bold; margin-bottom: 5px;",
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
                                    pack: 'center'
                                },
                                items: [
                                    {
                                        xtype: 'fieldset',
                                        style: "border:0",
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Elemento',
                                                name: 'txtNameElementoPadre',
                                                id: 'txtIdNameElementoPadre',
                                                readOnly: true,
                                                hidden: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Tipo Elemento',
                                                name: 'txtNameTipoElemento',
                                                id: 'txtIdTipoElemento',
                                                readOnly: true,
                                                hidden: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Ultima Milla',
                                                name: 'txtNameUltimaMilla',
                                                id: 'txtIdUltimaMilla',
                                                readOnly: true,
                                                hidden: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Puerto',
                                                name: 'txtPuerto',
                                                id: 'txtIdPuerto',
                                                readOnly: true,
                                                hidden: true
                                            },
                                            {
                                                xtype: 'combobox',
                                                id: 'idCbxClaseFibra',
                                                fieldLabel: 'Clase Fibra',
                                                store: storeClaseTipoMedio,
                                                triggerAction: 'all',
                                                displayField: 'nombreClaseTipoMedio',
                                                valueField: 'idClaseTipoMedio',
                                                loadingText: 'Seleccione ...',
                                                listClass: 'x-combo-list-small',
                                                queryMode: 'local',
                                                hidden: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'MODELO NODO',
                                                name: 'txtModeloNodoWifi',
                                                id: 'txtModeloNodoWifi',
                                                readOnly: true,
                                                hidden: true
                                            },
                                            {
                                                loadingText: 'Buscando ...',
                                                xtype: 'combobox',
                                                name: 'cmbNodoWifi',
                                                id: 'cmbNodoWifi',
                                                fieldLabel: '* NODO WIFI',
                                                labelStyle: "color:red;",
                                                displayField: 'nombreElemento',
                                                queryMode: "remote",
                                                valueField: 'idElemento',
                                                store: storeElementosNodoWifi,
                                                lazyRender: true,
                                                forceSelection: true,
                                                width: 300,
                                                minChars: 3,
    
                                                listeners: {
                                                    select: function(combo) {
                                                        var objeto = combo.valueModels[0].raw;
    
                                                        storeElementosFactibles.proxy.extraParams = {idElemento: combo.getValue(), idServicio : objNodoWifi.idServicio};
                                                        storeElementosFactibles.load({params: {}});
                                                        Ext.getCmp('cmbElementoFactible').setVisible(true);
                                                        Ext.getCmp('txtModeloNodoWifi').setValue('');
                                                        Ext.getCmp('txtModeloNodoWifi').setVisible(true);
                                                        Ext.getCmp('txtModeloNodoWifi').setValue(objeto.nombreModeloElemento);
                                                        Ext.getCmp('cbxIdElementoCaja').disable();
    
                                                        Ext.getCmp('cbxIdElementoCaja').setValue('');
                                                        Ext.getCmp('cbxElementoPNivel').setValue('');
                                                        Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                        Ext.getCmp('cbxPuertos').setValue("");
                                                        Ext.getCmp('txtIdPuerto').setValue("");
    
                                                        Ext.getCmp('cbxIdElementoCaja').setVisible(false);
                                                        Ext.getCmp('cbxElementoPNivel').setVisible(false);
                                                        Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                        Ext.getCmp('cbxPuertos').setVisible(false);
                                                        Ext.getCmp('txtIdPuerto').setVisible(false);
                                                        if(objeto.nombreModeloElemento == 'BACKBONE')
                                                        {
                                                            Ext.getCmp('cbxIdElementoCaja').setVisible(true);
                                                        }
                                                    }
                                                }
                                            },
                                            {
                                                queryMode: 'local',
                                                xtype: 'combobox',
                                                id: 'cmbElementoFactible',
                                                name: 'cmbElementoFactible',
                                                fieldLabel: '* ELEMENTO',
                                                labelStyle: "color:red;",
                                                hidden: true,
                                                displayField: 'nombreElementoFactible',
                                                valueField: 'idElementoFactible',
                                                loadingText: 'Buscando...',
                                                store: storeElementosFactibles,
                                                width: 300,
                                                listeners: {
                                                    select: function(combo) {
                                                        storeInterfacesElemento.proxy.extraParams = {idElemento: combo.getValue() , estado : 'not connect'};
                                                        storeInterfacesElemento.load({params: {}});
                                                        Ext.getCmp('cbxIdElementoCaja').enable();
                                                        Ext.getCmp('interfaceElementoNuevo').setVisible(true);
                                                    }
                                                },
    
                                            },
                                            {
                                                queryMode: 'local',
                                                xtype: 'combobox',
                                                id: 'interfaceElementoNuevo',
                                                name: 'interfaceElementoNuevo',
                                                fieldLabel: '* PUERTO ELEMENTO',
                                                labelStyle: "color:red;",
                                                hidden: true,
                                                displayField: 'nombreInterface',
                                                valueField: 'idInterface',
                                                loadingText: 'Buscando...',
                                                store: storeInterfacesElemento,
                                                width: 300,
                                            },
                                            {
                                                xtype: 'combobox',
                                                id: 'cbxIdElementoCaja',
                                                name: 'cbxElementoCaja',
                                                fieldLabel: '* CAJA',
                                                typeAhead: true,
                                                triggerAction: 'all',
                                                displayField: 'strNombreElemento',
                                                queryMode: "remote",
                                                valueField: 'intIdElemento',
                                                selectOnTab: true,
                                                hidden: true,
                                                store: storeElementos,
                                                width: 470,
                                                lazyRender: true,
                                                listClass: 'x-combo-list-small',
                                                labelStyle: "color:red;",
                                                forceSelection: true,
                                                disabled: true,
                                                minChars: 3,
                                                listeners: {
                                                    select: {fn: function(combo, value) {
                                                            Ext.getCmp('cbxElementoPNivel').setVisible(true);
                                                            Ext.getCmp('txtIdInterfacesDisponibles').setValue(0);
    
                                                            Ext.getCmp('txtIdNameElementoPadre').setValue("");
                                                            Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                                                            Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                            Ext.getCmp('txtIdTipoElemento').setValue("");
                                                            Ext.getCmp('txtIdModeloElemento').setValue("");
                                                            Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                            Ext.getCmp('cbxPuertos').setValue("");
                                                            Ext.getCmp('txtIdPuerto').setValue("");
    
                                                            Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                            Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                                                            Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                            Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                            Ext.getCmp('txtIdModeloElemento').setVisible(false);
                                                            Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                            Ext.getCmp('cbxPuertos').setVisible(false);
                                                            Ext.getCmp('txtIdPuerto').setVisible(false);
    
                                                            storeElementosByPadre.proxy.extraParams = {
                                                                popId: combo.getValue(),
                                                                elemento: strNombreTipoElemento,
                                                                estado: 'Activo'
                                                            };
                                                            storeElementosByPadre.load({params: {}});
                                                        }}
                                                }
                                            },
                                            {
                                                xtype: 'combobox',
                                                id: 'cbxElementoPNivel',
                                                name: 'cbxElementoPNivel',
                                                fieldLabel: '* ' + strNombreTipoElemento,
                                                hidden: true,
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
                                                emptyText: 'Seleccione un ' + strNombreTipoElemento,
                                                labelStyle: "color:red;",
                                                editable: false,
                                                listeners: {
                                                    select: {fn: function(combo, value) {
    
                                                            Ext.getCmp('txtIdNameElementoPadre').setValue("");
                                                            Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                                                            Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                            Ext.getCmp('txtIdTipoElemento').setValue("");
                                                            Ext.getCmp('txtIdModeloElemento').setValue("");
                                                            Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                            Ext.getCmp('cbxPuertos').setValue("");
                                                            Ext.getCmp('txtIdPuerto').setValue("");
    
                                                            Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                            Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                                                            Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                            Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                            Ext.getCmp('txtIdModeloElemento').setVisible(false);
                                                            Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                            Ext.getCmp('cbxPuertos').setVisible(false);
                                                            Ext.getCmp('txtIdPuerto').setVisible(false);
    
                                                            var arrayParamInfoElemDist = [];
                                                            arrayParamInfoElemDist.strPrefijoEmpresa = strPrefijoEmpresa;
                                                            arrayParamInfoElemDist.strIdElementoDistribucion = combo.getValue();
                                                            arrayParamInfoElemDist.strNombreCaja = Ext.getCmp('cbxIdElementoCaja').getRawValue();
                                                            arrayParamInfoElemDist.intIdElementoContenedor = Ext.getCmp('cbxIdElementoCaja').value;
                                                            arrayParamInfoElemDist.strUrlInfoCaja = urlInfoCaja;
                                                            arrayParamInfoElemDist.strTipoBusqueda = 'ELEMENTO';
                                                            arrayParamInfoElemDist.strNombreElementoPadre = strNombreElementoPadre;
                                                            arrayParamInfoElemDist.strNombreElemento = combo.getRawValue();
                                                            arrayParamInfoElemDist.strNombreTipoElemento = strNombreTipoElemento;
                                                            arrayParamInfoElemDist.strNombreTipoMedio = objNodoWifi.strNombreTipoMedio;
                                                            arrayParamInfoElemDist.strUrlDisponibilidadElemento = urlDisponibilidadElemento;
                                                            arrayParamInfoElemDist.strUrlCalculaMetraje = urlCalculaMetraje;
                                                            arrayParamInfoElemDist.intIdPunto = objNodoWifi.idPunto;
                                                            arrayParamInfoElemDist.winIngresoFactibilidad = winIngresoFactibilidad;
                                                            if ("TN" === strPrefijoEmpresa) {
                                                                arrayParamInfoElemDist.storeHilosDisponibles = storeHilosDisponibles;
                                                                var objResponseHiloMetraje = buscaHiloCalculaMetraje(arrayParamInfoElemDist);
                                                                Ext.getCmp('chbxIdObraCivil').setVisible(false);
                                                                Ext.getCmp('chbxIdObservacionRegeneracion').setVisible(false);
    
                                                                if ("100" !== objResponseHiloMetraje.strStatus) {
                                                                    strErrorMetraje = objResponseHiloMetraje.strMessageStatus;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Metraje',
                                                name: 'txtNamefloatMetraje',
                                                hidden: true,
                                                id: 'txtIdfloatMetraje',
                                                regex: /^(?:\d*\.\d{1,2}|\d+)$/,
                                                value: 0,
                                                readOnly: false
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Disponibilidad',
                                                name: 'txtInterfacesDisponibles',
                                                hidden: true,
                                                id: 'txtIdInterfacesDisponibles',
                                                maxLength: 3,
                                                value: 0,
                                                readOnly: true
                                            },
                                            cbxPuertos
                                        ]
                                    },
                                    {
                                        xtype: 'fieldset',
                                        style: "border:0",
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Modelo Elemento',
                                                name: 'txtNameModeloElemento',
                                                id: 'txtIdModeloElemento',
                                                readOnly: true,
                                                hidden: true
                                            },
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Marca Elemento',
                                                name: 'txtMarcaElementoPadre',
                                                id: 'txtIdMarcaElementoPadre',
                                                readOnly: true,
                                                hidden: true
                                            },
                                            {
                                                xtype: 'checkboxfield',
                                                fieldLabel: 'Obra Civil',
                                                name: 'chbxObraCivil',
                                                checked: boolCheckObraCivil,
                                                id: 'chbxIdObraCivil',
                                                hidden: true
                                            },
                                            {
                                                xtype: 'checkboxfield',
                                                fieldLabel: 'Requiere permisos regeneración',
                                                name: 'chbxObservacionRegeneracion',
                                                id: 'chbxIdObservacionRegeneracion',
                                                checked: boolCheckObservacionRegeneracion,
                                                hidden: true
                                            },
                                            {
                                                xtype: 'textarea',
                                                fieldLabel: 'Observacion',
                                                name: 'txtObservacionRegeneracion',
                                                id: 'txtIdObservacionRegeneracion',
                                                value: objNodoWifi.strObservacionPermiRegeneracion,
                                                hidden: true
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
                                    var txtModelo = Ext.getCmp('txtModeloNodoWifi').value;
    
                                    var intIdElementoCaja = Ext.getCmp('cbxIdElementoCaja').value;//caja
                                    var intElementoPNivel = Ext.getCmp('cbxElementoPNivel').value;//casette
                                    var intInterfaceElementoDistribucion = Ext.getCmp('cbxPuertos').value;//interfaceCasette
                                    var strObservacionRegeneracion = Ext.getCmp('txtIdObservacionRegeneracion').value;
                                    var floatMetraje = Ext.getCmp('txtIdfloatMetraje').value;
                                    var idSolicitud = objNodoWifi.idSolicitud;
                                    var boolError = false;
                                    var parametros;
                                    var mensajeError = "";
    
                                    var idNodoWifi = Ext.getCmp('cmbNodoWifi').value;
                                    var idElementoWifi = Ext.getCmp('cmbElementoFactible').value;
                                    var idInterfaceElementoWifi = Ext.getCmp('interfaceElementoNuevo').value;
    
                                    if (!idNodoWifi || idNodoWifi == "" || idNodoWifi == 0)
                                    {
                                        boolError = true;
                                        mensajeError += "Favor ingrese el nodo wifi \n";
                                    }
    
                                    if (!idElementoWifi || idElementoWifi == "" || idElementoWifi == 0)
                                    {
                                        boolError = true;
                                        mensajeError += " Favor ingrese el elemento . \n";
                                    }
    
                                    if (!idInterfaceElementoWifi || idInterfaceElementoWifi == "" || idInterfaceElementoWifi == 0)
                                    {
                                        boolError = true;
                                        mensajeError += " Favor ingrese el puerto del elemento.\n";
                                    }
    
    
                                    if (txtModelo == 'BACKBONE')
                                    {
                                        if (interfaceOdf == 0 || interfaceOdf == "")
                                        {
                                            boolError = true;
                                            mensajeError += "El casette no tiene relacionado un ODF, favor corregir.\n";
                                        }
    
                                        if (!intIdElementoCaja || intIdElementoCaja == "" || intIdElementoCaja == 0)
                                        {
                                            boolError = true;
                                            mensajeError += "El Elemento Caja no fue escogido, por favor seleccione.\n";
                                        }
                                        if (!intElementoPNivel || intElementoPNivel == "" || intElementoPNivel == 0)
                                        {
                                            boolError = true;
                                            mensajeError += "El Elemento " + strNombreTipoElemento + " no fue escogido, por favor seleccione.\n";
                                        }
                                        if (null == intInterfaceElementoDistribucion) {
                                            boolError = true;
                                            mensajeError += "No ha seleccionado un hilo para el " + strNombreTipoElemento + ". \n";
                                        }
                                    }
    
    
                                    parametros = {
                                        modeloNodoWifi: txtModelo,
                                        idNodoWifi: idNodoWifi,
                                        idElementoWifi: idElementoWifi,
                                        idInterfaceElementoWifi: idInterfaceElementoWifi,
                                        intInterfaceOdf: interfaceOdf,
                                        idSolicitud: idSolicitud,
                                        intIdElementoCaja: intIdElementoCaja,
                                        idCasette: intElementoPNivel,
                                        idInterfaceCasette: intInterfaceElementoDistribucion,
    
                                        strObservacion: strObservacionRegeneracion,
                                        floatMetraje: floatMetraje,
                                        strErrorMetraje: strErrorMetraje,
                                        strNombreTipoElemento: strNombreTipoElemento,
                                        factibilidadIPCCL2: true
                                    };
    
                                    if (!boolError)
                                    {
                                        connFactibilidad.request({
                                            url: url_asignarFactibilidad,
                                            method: 'post',
                                            timeout: 120000,
                                            params: parametros,
                                            success: function(response) {
                                                cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                                                if (IsJsonString(response.responseText))
                                                {
                                                    const text = JSON.parse(response.responseText);
                                                    if (text.status == "OK")
                                                    {
                                                        Ext.Msg.alert('Mensaje', `Transacción Exitosa${text.mensaje}`, function (btn) {
                                                            
                                                        });
                                                    }
                                                    else
                                                    {
                                                        Ext.MessageBox.show({
                                                            title: 'Error',
                                                            msg: text.mensaje,
                                                            buttons: Ext.MessageBox.OK,
                                                            icon: Ext.MessageBox.ERROR
                                                        });
                                                    }
                                                }else
                                                {
                                                    var text = response.responseText;
                                                    if (text == "OK")
                                                    {
                                                        Ext.Msg.alert('Mensaje', `Transacción Exitosa`, function (btn) {
                                                            
                                                        });
                                                    }
                                                    else
                                                    {
                                                        Ext.MessageBox.show({
                                                            title: 'Error',
                                                            msg: text,
                                                            buttons: Ext.MessageBox.OK,
                                                            icon: Ext.MessageBox.ERROR
                                                        });
                                                    }
                                                }
                                                store.load();
                                            },
                                            failure: function(result) {
                                                cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
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
                                    cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                                }
                            }
                        ]
                    });
    
                    winIngresoFactibilidad = Ext.widget('window', {
                        title: 'Factibilidad Wifi IPCCL2',
                        layout: 'fit',
                        resizable: false,
                        modal: true,
                        closable: false,
                        items: [formPanelIngresoFactibilidad]
                    });
                }
    
                winIngresoFactibilidad.show();
    
                //valido q si ya tiene un nodo wifi q le aparezca ese mismo
                if (idElementoWifi)
                {
                    //asignar valores a un combo
                    if (storeElementosNodoWifi !== undefined)
                    {
                        storeElementosNodoWifi.add({
                            idElemento: idElementoWifi,
                            nombreElemento: objNodoWifi.nombreElemento,
                            modelo: objNodoWifi.modeloElementoWifi
                        });
                    }
                    Ext.getCmp('cmbNodoWifi').setValue(idElementoWifi);

                    Ext.getCmp('cmbNodoWifi').display = idElementoWifi;
                    Ext.getCmp('cmbNodoWifi').value = idElementoWifi;
                    Ext.getCmp('cmbNodoWifi').disable();

                    if (storeElementosFactibles !== undefined)
                    {
                        storeElementosFactibles.proxy.extraParams = {
                            idElemento: idElementoWifi,
                            idServicio: objNodoWifi.idServicio
                        };
                        storeElementosFactibles.load({params: {}});
                    }


                    Ext.getCmp('cmbElementoFactible').setVisible(true);
                    Ext.getCmp('txtModeloNodoWifi').setValue('');
                    Ext.getCmp('txtModeloNodoWifi').setVisible(true);
                    Ext.getCmp('txtModeloNodoWifi').setValue(objNodoWifi.modeloElementoWifi);
                    Ext.getCmp('cbxIdElementoCaja').disable();

                    Ext.getCmp('cbxIdElementoCaja').setValue('');
                    Ext.getCmp('cbxElementoPNivel').setValue('');
                    Ext.getCmp('txtIdfloatMetraje').setValue("");
                    Ext.getCmp('cbxPuertos').setValue("");
                    Ext.getCmp('txtIdPuerto').setValue("");

                    Ext.getCmp('cbxIdElementoCaja').setVisible(false);
                    Ext.getCmp('cbxElementoPNivel').setVisible(false);
                    Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                    Ext.getCmp('cbxPuertos').setVisible(false);
                    Ext.getCmp('txtIdPuerto').setVisible(false);
                    if (objNodoWifi.modeloElementoWifi== 'BACKBONE')
                    {
                        Ext.getCmp('cbxIdElementoCaja').setVisible(true);
                    }
                }

            },
            fail: function (err) {
                Ext.Msg.show({
                    title: 'ERROR',
                    msg: 'Ha ocurrido un error al obtener los elementos, notificar a Sistemas',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });
    }else
    {
        Ext.Msg.show({
            title: 'ERROR',
            msg: 'Ha ocurrido un error, notificar a Sistemas',
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.ERROR
        });
    }

    store.load();

}

/************************************************************************ */
/********************** INGRESAR FACTIBILIDAD *************************** */
/************************************************************************ */
function showIngresoFactibilidad(rec)
{
    const idServicio           = rec.data.id_servicio;
    var intIdElemento          = 0;
    var intIdInterfaceElemento = 0;
    var arrayParametros        = [];
    var strPrefijoEmpresa      = '';
    var strEsFttx              = '';
    var strTipoRed             = rec.get("strTipoRed");
    var booleanTipoRedGpon     = rec.get("booleanTipoRedGpon");
    if(rec.get('ultimaMilla') === 'FTTx')
    {
        strEsFttx = 'SI';
        strPrefijoEmpresa = 'MD';
    }
    else
    {
        strEsFttx = 'NO';
        strPrefijoEmpresa = rec.get("strPrefijoEmpresa");
    }

    arrayParametros['strPrefijoEmpresa'] = strPrefijoEmpresa;
    arrayParametros['strDescripcionTipoRol'] = 'Contacto';
    arrayParametros['strEstadoTipoRol'] = 'Activo';
    arrayParametros['strDescripcionRol'] = 'Contacto Tecnico';
    arrayParametros['strEstadoRol'] = 'Activo';
    arrayParametros['strEstadoIER'] = 'Activo';
    arrayParametros['strEstadoIPER'] = 'Activo';
    arrayParametros['strEstadoIPC'] = 'Activo';
    arrayParametros['intIdPersonaEmpresaRol'] = rec.get("intIdPersonaEmpresaRol");
    arrayParametros['intIdPunto'] = rec.get("id_punto");
    arrayParametros['idStore'] = 'storeContactoIngFac';
    arrayParametros['strJoinPunto'] = '';
    strDefaultType = "hiddenfield";
    strXtype = "hiddenfield";
    strNombres = '';
    strApellidos = '';
    strErrorMetraje = '';
    boolCheckObraCivil = false;
    boolCheckObservacionRegeneracion = false;
    storeRolContacto = '';
    storeRolContactoPunto = '';

    if ("TN" === strPrefijoEmpresa) {
        var arrayPersonaContacto = [];
        storeRolContacto = obtienInfoPersonaContacto(arrayParametros);
        arrayParametros['intIdPersonaEmpresaRol'] = '';
        arrayParametros['idStore'] = 'storeContactoPuntoIngFac';
        arrayParametros['strJoinPunto'] = 'PUNTO';
        storeRolContactoPunto = obtienInfoPersonaContacto(arrayParametros);
        strDefaultType = "textfield";
        strXtype = "fieldset";
    }
    var boolEsEdificio = true;
    var boolDependeDeEdificio = true;
    var boolNombreEdificio = true;
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
    if ("TN" === strPrefijoEmpresa) {
        strNombreTipoElemento = "CASSETTE";
        strNombreElementoPadre = "Switch";
        if ("true" == rec.get("strObraCivil"))
        {
            boolCheckObraCivil = true;
        }
        if ("true" == rec.get("strPermisosRegeneracion"))
        {
            boolCheckObservacionRegeneracion = true;
        }
    }

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
                        Ext.MessageBox.alert('Error', "Favor ingrese un nombre de caja");
                    }
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    nombre: this.strNombreElemento,
                    intIdPunto: rec.get('id_punto'),
                    esFttx: strEsFttx
                }
            },
            fields:
                [
                    {name: 'intIdElemento', mapping: 'intIdElemento'},
                    {name: 'strNombreElemento', mapping: 'strNombreElemento'},
                ]
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
                    elemento: strNombreTipoElemento,
                    esFttx: strEsFttx
                }
            },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'}
                ]
        });

        /*modelClaseTipoMedio*/
        Ext.define('modelClaseTipoMedio', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idClaseTipoMedio', type: 'int'},
                {name: 'nombreClaseTipoMedio', type: 'string'}
            ]
        });

        //Store de storeClaseTipoMedio
        storeClaseTipoMedio = Ext.create('Ext.data.Store', {
            autoLoad: true,
            model: "modelClaseTipoMedio",
            proxy: {
                type: 'ajax',
                url: urlGetClaseTipoMedio,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                },
                extraParams: {
                    tipoMedioId: rec.get('txtIdUltimaMilla'),
                    estado: 'Activo'
                }
            }
        });

        Ext.define('modelInterfaceElemento', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdInterfaceElemento', type: 'int'},
                {name: 'strNombreInterfaceElemento', type: 'string'}
            ]
        });

        storePuertos = Ext.create('Ext.data.Store', {
            model: "modelInterfaceElemento",
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: urlInterfacesByElemento,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                },
                extraParams: {
                    intIdElemento: '',
                    strEstado: ''
                }
            }
        });

        var storeHilosDisponibles = new Ext.data.Store({
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: getHilosDisponibles,
                extraParams: {
                    idElemento: '',
                    estadoInterface: 'connected',
                    estadoInterfaceNotConect: 'not connect',
                    estadoInterfaceReserved: 'not connect',
                    strBuscaHilosServicios: 'NO',
                    intIdPunto: rec.get('id_punto'),
                    strTipoEnlace: rec.get("strTipoEnlace")
                },
                reader: {
                    type: 'json',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                    {name: 'idInterfaceElementoOut', mapping: 'idInterfaceElementoOut'},
                    {name: 'colorHilo', mapping: 'colorHilo'},
                    {name: 'numeroHilo', mapping: 'numeroHilo'},
                    {name: 'numeroColorHilo', mapping: 'numeroColorHilo'}
                ]
        });

        cbxPuertos = Ext.create('Ext.form.ComboBox', {
            id: 'cbxPuertos',
            name: 'cbxPuertos',
            store: storeHilosDisponibles,
            queryMode: 'local',
            fieldLabel: 'Hilos Disponibles',
            displayField: 'numeroColorHilo',
            valueField: 'idInterfaceElementoOut',
            editable: false,
            hidden: true,
            listeners:
                {
                    select: function(combo)
                    {

                        var objeto = combo.valueModels[0].raw;
                        Ext.Ajax.request({
                            url: ajaxGetPuertoSwitchByHilo,
                            method: 'post',
                            async: false,
                            params: {idInterfaceElementoConector: objeto.idInterfaceElemento},
                            success: function(response)
                            {
                                var json = Ext.JSON.decode(response.responseText);
                                Ext.getCmp('txtIdPuerto').setValue(json.nombreInterfaceElemento);
                                Ext.getCmp('txtIdPuerto').setVisible(true);
                                intIdInterfaceElemento = json.idInterfaceElemento;
                                var arrayParamDistribucionTN = [];
                                arrayParamDistribucionTN['strUrlInfoCaja'] = urlInfoCaja;
                                arrayParamDistribucionTN['intIdElementoContenedor'] = Ext.getCmp('cbxIdElementoCaja').value;
                                arrayParamDistribucionTN['strIdElementoDistribucion'] = objeto.idInterfaceElemento;
                                arrayParamDistribucionTN['strTipoBusqueda'] = 'INTERFACE';
                                arrayParamDistribucionTN['strNombreElementoPadre'] = strNombreElementoPadre.toUpperCase();
                                arrayParamDistribucionTN['strNombreCaja'] = Ext.getCmp('cbxIdElementoCaja').getRawValue();
                                arrayParamDistribucionTN['strNombreElemento'] = Ext.getCmp('cbxElementoPNivel').getRawValue();
                                arrayParamDistribucionTN['strNombreTipoElemento'] = strNombreTipoElemento;
                                arrayParamDistribucionTN['strNombreTipoMedio'] = rec.get("strNombreTipoMedio");
                                arrayParamDistribucionTN['strPrefijoEmpresa'] = strPrefijoEmpresa;
                                arrayParamDistribucionTN['strUrlDisponibilidadElemento'] = urlDisponibilidadElemento;
                                arrayParamDistribucionTN['strUrlCalculaMetraje'] = urlCalculaMetraje;
                                arrayParamDistribucionTN['intIdPunto'] = rec.get("id_punto");
                                arrayParamDistribucionTN['winIngresoFactibilidad'] = winIngresoFactibilidad;
                                objInformacionElemento = getInformacionByElementoDistribucion(arrayParamDistribucionTN);
                                intIdElemento = objInformacionElemento.idOlt;
                                intIdInterfaceElemento = objInformacionElemento.idLinea;
                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });


                    }
                }
        });
        
  var  storeSwitch = new Ext.data.Store({ 
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
                    nombreElemento:  '',
                    marcaElemento:   '',
                    modeloElemento:  '',
                    canton:          '',
                    jurisdiccion:    '',
                    tipoElemento:    'SWITCH',
                    estado:          'Todos',
                    procesoBusqueda: 'limitado',
                    idServicio:      rec.data.id_servicio
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

        var storeInterfacesElemento = new Ext.data.Store({
            pageSize: 100,
            proxy: {
                type: 'ajax',
                timeout: 400000,
                url: getInterfacesPorElemento,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'idInterface', mapping: 'idInterface'},
                    {name: 'nombreInterface', mapping: 'nombreInterface'},
                    {name: 'nombreEstadoInterface', mapping: 'nombreEstadoInterface'}
                ]
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
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Tipo Enlace',
                                            name: 'strTipoEnlace',
                                            id: 'strTipoEnlace',
                                            value: rec.get("strTipoEnlace"),
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Tipo de Red',
                                            name: 'tipoRed',
                                            id: 'tipoRed',
                                            value: strTipoRed,
                                            allowBlank: false,
                                            readOnly: true
                                        }
                                    ]
                                },
                                {
                                    xtype: strXtype,
                                    title: 'Datos Contacto Tecnico',
                                    style: "font-weight:bold; margin-bottom: 5px;",
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
                        pack: 'center'
                    },
                    items: [
                        {
                            xtype: 'fieldset',
                            style: "border:0",
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Backbone',
                                    name: 'txtTipoBackBone',
                                    id: 'txtTipoBackBone',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'TIPO BACKBONE',
                                    id: 'tipoBackone',
                                    name: 'tipoBackone',
                                    value: 'RUTA',
                                    hidden: true,
                                    store: [
                                        ['DIRECTO','DIRECTO'],
                                        ['RUTA','RUTA']
                                    ],
                                    listeners: {
                                        select: {fn: function(combo, value) {
                                                if(combo.getValue()=='DIRECTO')
                                                {
                                                    Ext.getCmp('cbxIdElementoCaja').setVisible(false);
                                                    Ext.getCmp('cbxElementoPNivel').setVisible(false);                                                    
                                                    Ext.getCmp('cmdElementoDirecto').setVisible(true);
                                                    Ext.getCmp('cmbUltimaMilla').setVisible(true);
                                                    Ext.getCmp('metrajeDirecto').setVisible(true);
                                                    Ext.getCmp('observacionDirecto').setVisible(true);
                                                }
                                                else
                                                {
                                                    Ext.getCmp('cbxIdElementoCaja').setVisible(true);
                                                    Ext.getCmp('cbxElementoPNivel').setVisible(true);
                                                    Ext.getCmp('cmdElementoDirecto').setVisible(false);
                                                    Ext.getCmp('cmbInterfaceDirecto').setVisible(false);
                                                    Ext.getCmp('cmbUltimaMilla').setVisible(false);                                                    
                                                    Ext.getCmp('metrajeDirecto').setVisible(false);
                                                    Ext.getCmp('observacionDirecto').setVisible(false);
                                                    
                                                    Ext.getCmp('cmdElementoDirecto').setValue('');
                                                    Ext.getCmp('cmbInterfaceDirecto').setValue('');                                                   
                                                    
                                                }

                                            }
                                        }
                                    }
                                },                                 
                                {
                                    xtype: 'combobox',
                                    fieldLabel: '* ULTIMA MILLA',
                                    id: 'cmbUltimaMilla',
                                    name: 'cmbUltimaMilla',
                                    value: 'UTP',
                                    hidden: true,
                                    store: [
                                        ['UTP','UTP'],
                                        ['FO','Fibra Optica']
                                    ],
                                },
                                {   
                                    loadingText: 'Buscando ...',
                                    xtype: 'combobox',
                                    name: 'cmdElementoDirecto',
                                    id: 'cmdElementoDirecto',
                                    fieldLabel: 'ELEMENTO',
                                    displayField: 'nombreElemento',
                                    queryMode: "remote",
                                    valueField: 'idElemento',
                                    store: storeSwitch,
                                    lazyRender: true,
                                    forceSelection: true,
                                    width: 300,
                                    minChars: 3,                                    
                                    hidden: true,
                                    listeners: {
                                        select: function(combo) {
                                            var intIdCliente = null;
                                            if( rec.get('intIdPersonaEmpresaRol') !== null ) {
                                                intIdCliente = rec.get('intIdPersonaEmpresaRol');
                                            }
                                            storeInterfacesElemento.proxy.extraParams = {intIdCliente: intIdCliente,
                                                idElemento: combo.getValue() , estado : 'Todos'};
                                            storeInterfacesElemento.load({params: {}});
                                            Ext.getCmp('cmbInterfaceDirecto').setVisible(true);
                                        }
                                    },
                                },
                                {
                                    queryMode: 'local',
                                    xtype: 'combobox',
                                    id: 'cmbInterfaceDirecto',
                                    name: 'cmbInterfaceDirecto',
                                    fieldLabel: 'PUERTO ELEMENTO',
                                    hidden: true,
                                    displayField: 'nombreEstadoInterface',
                                    valueField: 'idInterface',
                                    loadingText: 'Buscando...',
                                    store: storeInterfacesElemento,
                                    width: 300,
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'METRAJE',
                                    name: 'metrajeDirecto',
                                    id: 'metrajeDirecto',
                                    value: 0,
                                    hidden: true
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'OBSERVACIÓN',
                                    name: 'observacionDirecto',
                                    id: 'observacionDirecto',
                                    value: '',
                                    hidden: true
                                },                                
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Elemento',
                                    name: 'txtNameElementoPadre',
                                    id: 'txtIdNameElementoPadre',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Elemento',
                                    name: 'txtNameTipoElemento',
                                    id: 'txtIdTipoElemento',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ultima Milla',
                                    name: 'txtNameUltimaMilla',
                                    id: 'txtIdUltimaMilla',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Puerto',
                                    name: 'txtPuerto',
                                    id: 'txtIdPuerto',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'idCbxClaseFibra',
                                    fieldLabel: 'Clase Fibra',
                                    store: storeClaseTipoMedio,
                                    triggerAction: 'all',
                                    displayField: 'nombreClaseTipoMedio',
                                    valueField: 'idClaseTipoMedio',
                                    loadingText: 'Seleccione ...',
                                    listClass: 'x-combo-list-small',
                                    queryMode: 'local',
                                    hidden: true
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'cbxIdElementoCaja',
                                    name: 'cbxElementoCaja',
                                    fieldLabel: '* CAJA',
                                    typeAhead: true,
                                    triggerAction: 'all',
                                    displayField: 'strNombreElemento',
                                    queryMode: "remote",
                                    valueField: 'intIdElemento',
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
                                                Ext.getCmp('cbxElementoPNivel').reset();
                                                Ext.getCmp('cbxElementoPNivel').setDisabled(false);
                                                Ext.getCmp('txtIdInterfacesDisponibles').setValue(0);

                                                Ext.getCmp('txtIdNameElementoPadre').setValue("");
                                                Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                                                Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                Ext.getCmp('txtIdTipoElemento').setValue("");
                                                Ext.getCmp('txtIdModeloElemento').setValue("");
                                                Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                Ext.getCmp('cbxPuertos').setValue("");
                                                Ext.getCmp('txtIdPuerto').setValue("");

                                                Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                Ext.getCmp('txtIdModeloElemento').setVisible(false);
                                                Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                Ext.getCmp('cbxPuertos').setVisible(false);
                                                Ext.getCmp('txtIdPuerto').setVisible(false);

                                                storeElementosByPadre.proxy.extraParams = {
                                                    popId: combo.getValue(),
                                                    elemento: strNombreTipoElemento,
                                                    estado: 'Activo',
                                                    idServicio: idServicio
                                                };
                                                storeElementosByPadre.load({params: {esFttx: strEsFttx}});
                                            }}
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    id: 'cbxElementoPNivel',
                                    name: 'cbxElementoPNivel',
                                    fieldLabel: '* ' + strNombreTipoElemento,
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
                                    emptyText: 'Seleccione un ' + strNombreTipoElemento,
                                    labelStyle: "color:red;",
                                    disabled: true,
                                    editable: false,
                                    listeners: {
                                        select: {fn: function(combo, value) {

                                                Ext.getCmp('txtIdNameElementoPadre').setValue("");
                                                Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                                                Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                Ext.getCmp('txtIdTipoElemento').setValue("");
                                                Ext.getCmp('txtIdModeloElemento').setValue("");
                                                Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                Ext.getCmp('cbxPuertos').setValue("");
                                                Ext.getCmp('txtIdPuerto').setValue("");

                                                Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                Ext.getCmp('txtIdModeloElemento').setVisible(false);
                                                Ext.getCmp('txtIdfloatMetraje').setVisible(false);
                                                Ext.getCmp('cbxPuertos').setVisible(false);
                                                Ext.getCmp('txtIdPuerto').setVisible(false);

                                                var arrayParamInfoElemDist = [];
                                                arrayParamInfoElemDist['strPrefijoEmpresa'] = strPrefijoEmpresa;
                                                arrayParamInfoElemDist['strIdElementoDistribucion'] = combo.getValue();
                                                arrayParamInfoElemDist['strNombreCaja'] = Ext.getCmp('cbxIdElementoCaja').getRawValue();
                                                arrayParamInfoElemDist['intIdElementoContenedor'] = Ext.getCmp('cbxIdElementoCaja').value;
                                                arrayParamInfoElemDist['strUrlInfoCaja'] = urlInfoCaja;
                                                arrayParamInfoElemDist['strTipoBusqueda'] = 'ELEMENTO';
                                                arrayParamInfoElemDist['strNombreElementoPadre'] = strNombreElementoPadre;
                                                arrayParamInfoElemDist['strNombreElemento'] = combo.getRawValue();
                                                arrayParamInfoElemDist['strNombreTipoElemento'] = strNombreTipoElemento;
                                                arrayParamInfoElemDist['strNombreTipoMedio'] = rec.get("strNombreTipoMedio");
                                                arrayParamInfoElemDist['strUrlDisponibilidadElemento'] = urlDisponibilidadElemento;
                                                arrayParamInfoElemDist['strUrlCalculaMetraje'] = urlCalculaMetraje;
                                                arrayParamInfoElemDist['intIdPunto'] = rec.get("id_punto");
                                                arrayParamInfoElemDist['winIngresoFactibilidad'] = winIngresoFactibilidad;
                                                arrayParamInfoElemDist['strTipoRed'] = rec.get("strTipoRed");

                                                if(booleanTipoRedGpon)
                                                {
                                                    Ext.getCmp('txtTipoBackBone').setValue("RUTA");
                                                    Ext.getCmp('txtTipoBackBone').setVisible(true);
                                                }
                                                if ("TN" !== strPrefijoEmpresa) {
                                                    Ext.MessageBox.show({
                                                        msg: 'Obteniendo informacion de ' + strNombreTipoElemento,
                                                        title: 'Buscando',
                                                        progressText: 'Buscando.',
                                                        progress: true,
                                                        closable: false,
                                                        width: 300,
                                                        wait: true,
                                                        waitConfig: {interval: 200}
                                                    });
                                                    objInformacionElemento = getInformacionByElementoDistribucion(arrayParamInfoElemDist);
                                                    intIdElemento = objInformacionElemento.idOlt;
                                                    intIdInterfaceElemento = objInformacionElemento.idLinea;
                                                }
                                                if ("TN" === strPrefijoEmpresa) {
                                                    arrayParamInfoElemDist['storeHilosDisponibles'] = storeHilosDisponibles;
                                                    arrayParamInfoElemDist['strTipoEnlace']         = rec.get("strTipoEnlace");
                                                    var objResponseHiloMetraje = buscaHiloCalculaMetraje(arrayParamInfoElemDist);
                                                    if ("100" !== objResponseHiloMetraje.strStatus) {
                                                        strErrorMetraje = objResponseHiloMetraje.strMessageStatus;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Metraje',
                                    name: 'txtNamefloatMetraje',
                                    hidden: true,
                                    id: 'txtIdfloatMetraje',
                                    regex: /^(?:\d*\.\d{1,2}|\d+)$/,
                                    value: 0,
                                    readOnly: false
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Disponibilidad',
                                    name: 'txtInterfacesDisponibles',
                                    hidden: true,
                                    id: 'txtIdInterfacesDisponibles',
                                    maxLength: 3,
                                    value: 0,
                                    readOnly: true
                                },
                                cbxPuertos
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            style: "border:0",
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Modelo Elemento',
                                    name: 'txtNameModeloElemento',
                                    id: 'txtIdModeloElemento',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Marca Elemento',
                                    name: 'txtMarcaElementoPadre',
                                    id: 'txtIdMarcaElementoPadre',
                                    readOnly: true,
                                    hidden: true
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Obra Civil',
                                    name: 'chbxObraCivil',
                                    checked: boolCheckObraCivil,
                                    id: 'chbxIdObraCivil',
                                    hidden: true
                                },
                                {
                                    xtype: 'checkboxfield',
                                    fieldLabel: 'Requiere permisos regeneración',
                                    name: 'chbxObservacionRegeneracion',
                                    id: 'chbxIdObservacionRegeneracion',
                                    checked: boolCheckObservacionRegeneracion,
                                    hidden: true
                                },
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Observacion',
                                    name: 'txtObservacionRegeneracion',
                                    id: 'txtIdObservacionRegeneracion',
                                    value: rec.get("strObservacionPermiRegeneracion"),
                                    hidden: true
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
                            
                        var tipoBackbone = Ext.getCmp('tipoBackone').value;
                        var intIdSolFactibilidad = rec.get("id_factibilidad");

                        if(booleanTipoRedGpon)
                        {
                            tipoBackbone = "RUTA";
                        }

                        if (strPrefijoEmpresa === "TN"   && tipoBackbone == 'DIRECTO')
                        {
                            var intElementoDirecto = Ext.getCmp('cmdElementoDirecto').value;
                            var intInterfaceDirecto = Ext.getCmp('cmbInterfaceDirecto').value;
                            
                            var strUltimaMilla  = Ext.getCmp('cmbUltimaMilla').value;
                            var strMetrajeDirecto = Ext.getCmp('metrajeDirecto').value;
                            var strObservacionDirecto = Ext.getCmp('observacionDirecto').value;                            
                            
                            if (intElementoDirecto == null || intElementoDirecto == "")
                            {
                                boolError = true;
                                mensajeError += "Seleccione el elemento.\n";
                            }
                            if (intInterfaceDirecto == null || intInterfaceDirecto == "")
                            {
                                boolError = true;
                                mensajeError += "Seleccione la interface del elemento.\n";
                            }
                            if (strMetrajeDirecto == 0 || strMetrajeDirecto == "")
                            {
                                boolError = true;
                                mensajeError += "Ingrese el metraje.\n";
                            }

                            parametros = {
                                intElementoDirecto: intElementoDirecto,
                                intInterfaceDirecto: intInterfaceDirecto,
                                strUltimaMilla: strUltimaMilla,
                                strMetrajeDirecto: strMetrajeDirecto,
                                strObservacionDirecto: strObservacionDirecto,
                                strTipoBackone: tipoBackbone,
                                intIdSolFactibilidad: intIdSolFactibilidad,
                            };
                        }
                        else
                        {
                            var intIdElementoCaja = Ext.getCmp('cbxIdElementoCaja').value;
                            var intElementoPNivel = Ext.getCmp('cbxElementoPNivel').value;
                            var intInterfaceElementoDistribucion = Ext.getCmp('cbxPuertos').value;
                            var intPuertosDisponibles = Ext.getCmp('txtIdInterfacesDisponibles').value;
                            var chbxObraCivil = Ext.getCmp('chbxIdObraCivil').value;
                            var chbxObservacionRegeneracion = Ext.getCmp('chbxIdObservacionRegeneracion').value;
                            var strObservacionRegeneracion = Ext.getCmp('txtIdObservacionRegeneracion').value;
                            var floatMetraje = Ext.getCmp('txtIdfloatMetraje').value;
                            
                            if (intIdElemento == 0 || intIdElemento == "")
                            {
                                boolError = true;
                                mensajeError += "Caja sin " + strNombreElementoPadre + " no se puede ingresar Factibilidad.\n";
                            }
                            if (intIdInterfaceElemento == 0 || intIdInterfaceElemento == "")
                            {
                                boolError = true;
                                mensajeError += strNombreElementoPadre + " sin puerto no se puede ingresar Factibilidad.\n";
                            }
                            if (!intIdElementoCaja || intIdElementoCaja == "" || intIdElementoCaja == 0)
                            {
                                boolError = true;
                                mensajeError += "El Elemento Caja no fue escogido, por favor seleccione.\n";
                            }
                            if (!intElementoPNivel || intElementoPNivel == "" || intElementoPNivel == 0)
                            {
                                boolError = true;
                                mensajeError += "El Elemento " + strNombreTipoElemento + " no fue escogido, por favor seleccione.\n";
                            }
                            if (strPrefijoEmpresa  !== "TN") {
                                if (boolError && intPuertosDisponibles == "" && intPuertosDisponibles < 0)
                                {
                                    boolError = true;
                                    mensajeError += "Las interfaces disponibles no fueron cargadas. \n";
                                }
                                if (!boolError && intPuertosDisponibles == 0)
                                {
                                    boolError = true;
                                    mensajeError += "El " + strNombreTipoElemento + " no tiene interfaces disponibles. \n";
                                }
                            }

                            if (strPrefijoEmpresa === "TN") {
                                if (null == intInterfaceElementoDistribucion) {
                                    boolError = true;
                                    mensajeError += "No ha seleccionado un hilo para el " + strNombreTipoElemento + ". \n";
                                }
                            }
                            parametros = {
                                intIdSolFactibilidad: intIdSolFactibilidad,
                                intIdElemento: intIdElemento,
                                intIdInterfaceElemento: intIdInterfaceElemento,
                                intIdElementoCaja: intIdElementoCaja,
                                intElementoPNivel: intElementoPNivel,
                                intInterfaceElementoDistribucion: intInterfaceElementoDistribucion,
                                chbxObraCivil: chbxObraCivil,
                                chbxObservacionRegeneracion: chbxObservacionRegeneracion,
                                strObservacionRegeneracion: strObservacionRegeneracion,
                                floatMetraje: floatMetraje,
                                strErrorMetraje: strErrorMetraje,
                                strNombreTipoElemento: strNombreTipoElemento,
                                strTipoBackone: tipoBackbone,
                                strTipoRed:strTipoRed,
                                strUltimaMilla: rec.get('ultimaMilla')
                            };

                        }

                        if (!boolError)
                        {
                            urlNuevaFactibilidad = ( rec.get('ultimaMilla') == 'FTTx') ? urlNuevaFactibilidadFTTx : urlNuevaFactibilidad;

                            connFactibilidad.request({
                                url: urlNuevaFactibilidad,
                                method: 'post',
                                timeout: 120000,
                                params: parametros,
                                success: function(response) {
                                    var text = response.responseText;                                    
                                    cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);                                   
                                    if (text == "Se modifico Correctamente el detalle de la Solicitud de Factibilidad" ||  
                                        text.toString().indexOf("Se crea orden de trabajo automáticamente por factibilidad manual") != -1 ||
                                    (   rec.get('ultimaMilla') === 'FTTx' && 
                                        text.toString().indexOf("Se modifico Correctamente el detalle de la Solicitud de Factibilidad") != -1))
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
                                    cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
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
                        cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
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

     if ( strPrefijoEmpresa === "TN") 
     {
         Ext.getCmp('tipoBackone').setVisible(true);
         //validacion para cargar por defecto DIRECTO cuando sea UTP
         var tipoMedio = rec.get("strNombreTipoMedio");
         if(tipoMedio == 'UTP')
         {
             Ext.getCmp('tipoBackone').setValue('DIRECTO');
             Ext.getCmp('cbxIdElementoCaja').setVisible(false);
             Ext.getCmp('cbxElementoPNivel').setVisible(false);                                                    
             Ext.getCmp('cmdElementoDirecto').setVisible(true);
             Ext.getCmp('cmbUltimaMilla').setVisible(true);
             Ext.getCmp('metrajeDirecto').setVisible(true);
             Ext.getCmp('observacionDirecto').setVisible(true);
         }
     }

    winIngresoFactibilidad.show();
}

function getInformacionByElementoDistribucion(arrayParamDistribucion) {
    var objInformacionElemento;
    var objMessageBoxElementoDist;
    var async = false;
    Ext.Ajax.on("beforerequest", function() {
        objMessageBoxElementoDist = Ext.MessageBox.show({
            msg: 'Obtienedo informacion',
            progressText: 'Cargando...',
            width: 300,
            wait: true,
            waitConfig: {interval: 300}
        });
    });
    Ext.Ajax.on("requestcomplete", function() {
        objMessageBoxElementoDist.hide();
    });
    Ext.Ajax.request({
        url: arrayParamDistribucion['strUrlInfoCaja'],
        method: 'post',
        async: async,
        timeout: 120000,
        params: {
            intIdElementoContenedor: arrayParamDistribucion['intIdElementoContenedor'],
            intIdElementoDistribucion: arrayParamDistribucion['strIdElementoDistribucion'],
            strTipoBusqueda: arrayParamDistribucion['strTipoBusqueda'],
            strNombreElementoPadre: arrayParamDistribucion['strNombreElementoPadre'],
            strTipoRed: arrayParamDistribucion['strTipoRed']
        },
        success: function(response) {

            objInformacionElemento = Ext.JSON.decode(response.responseText);

            if (objInformacionElemento.error) {
                Ext.getCmp('txtIdNameElementoPadre').setValue("");
                Ext.getCmp('txtIdPuerto').setValue("");
                Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                Ext.getCmp('txtIdPuerto').setVisible(false);
                Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                Ext.getCmp('cbxIdElementoCaja').reset();
                cierraVentanaIngresoFactibilidad(arrayParamDistribucion['winIngresoFactibilidad']);
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: objInformacionElemento.msg + "<br><br>Datos Ingresados<br>Caja: <b>" + arrayParamDistribucion['strNombreCaja'] + "</b><br>" +
                        arrayParamDistribucion['strNombreTipoElemento'] + ": <b>" + arrayParamDistribucion['strNombreElemento'] + "</b>",
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            } else {

                Ext.getCmp('txtIdNameElementoPadre').setValue(objInformacionElemento.olt);
                Ext.getCmp('txtIdMarcaElementoPadre').setValue(objInformacionElemento.marcaOlt);
                Ext.getCmp('txtIdUltimaMilla').setValue(arrayParamDistribucion['strNombreTipoMedio']);
                Ext.getCmp('txtIdTipoElemento').setValue(objInformacionElemento.strNombreTipoElemento);
                Ext.getCmp('txtIdModeloElemento').setValue(objInformacionElemento.strNombreModeloElemento);
                Ext.getCmp('txtIdNameElementoPadre').setVisible(true);
                Ext.getCmp('txtIdMarcaElementoPadre').setVisible(true);
                Ext.getCmp('txtIdUltimaMilla').setVisible(true);
                Ext.getCmp('txtIdTipoElemento').setVisible(true);
                Ext.getCmp('txtIdModeloElemento').setVisible(true);

                if ("TN" !== arrayParamDistribucion['strPrefijoEmpresa']) {
                    Ext.getCmp('txtIdInterfacesDisponibles').setVisible(true);
                    Ext.getCmp('txtIdPuerto').setValue(objInformacionElemento.linea);
                    Ext.getCmp('txtIdPuerto').setVisible(true);
                    Ext.Ajax.request({
                        url: arrayParamDistribucion['strUrlDisponibilidadElemento'],
                        method: 'post',
                        timeout: 120000,
                        async: true,
                        params: {
                            idElemento: arrayParamDistribucion['strIdElementoDistribucion']
                        },
                        success: function(response) {
                            Ext.MessageBox.close();
                            var ContDisponibilidad = response.responseText;
                            Ext.getCmp('txtIdInterfacesDisponibles').setValue(ContDisponibilidad);
                        },
                        failure: function(result)
                        {
                            Ext.getCmp('txtIdNameElementoPadre').setValue("");
                            Ext.getCmp('txtIdPuerto').setValue("");
                            Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
                            Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                            Ext.getCmp('txtIdPuerto').setVisible(false);
                            Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
                            cierraVentanaIngresoFactibilidad(arrayParamDistribucion['winIngresoFactibilidad']);
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
        },
        failure: function(result)
        {
            Ext.getCmp('txtIdNameElementoPadre').setValue("");
            Ext.getCmp('txtIdPuerto').setValue("");
            Ext.getCmp('txtIdMarcaElementoPadre').setValue("");
            Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
            Ext.getCmp('txtIdPuerto').setVisible(false);
            Ext.getCmp('txtIdMarcaElementoPadre').setVisible(false);
            cierraVentanaIngresoFactibilidad(arrayParamDistribucion['winIngresoFactibilidad']);
            Ext.MessageBox.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
    return objInformacionElemento;
}

function buscaHiloCalculaMetraje(arrayCalculaMetraje) {
    var objResponse;
    Ext.MessageBox.show({
            msg: 'Buscando hilos disponibles y calculando metraje...',
            progressText: 'buscando...',
            width: 300,
            wait: true,
            waitConfig: {interval: 400}
        });
    Ext.Ajax.request({
        url: arrayCalculaMetraje['strUrlCalculaMetraje'],
        method: 'post',
        timeout: 120000,
        async: false,
        params: {
            intIdElemento: arrayCalculaMetraje['strIdElementoDistribucion'],
            intIdPunto: arrayCalculaMetraje['intIdPunto']
        },
        success: function(response) {

            objResponse = Ext.JSON.decode(response.responseText);
            Ext.getCmp('txtIdfloatMetraje').setVisible(true);
            Ext.getCmp('txtIdfloatMetraje').setValue(objResponse.registros);

            if ("100" !== objResponse.strStatus) {
                Ext.Msg.alert(Utils.arrayTituloMensajeBox[objResponse.strStatus], objResponse.strMessageStatus);
            }

            arrayCalculaMetraje['storeHilosDisponibles'].proxy.extraParams = {
                idElemento: Ext.getCmp('cbxElementoPNivel').value,
                estadoInterface: 'connected',
                estadoInterfaceNotConect: 'not connect',
                estadoInterfaceReserved: 'not connect',
                intIdPunto: arrayCalculaMetraje['intIdPunto'],
                strBuscaHilosServicios: 'NO',
                strTipoEnlace: arrayCalculaMetraje['strTipoEnlace']
            };
            arrayCalculaMetraje['storeHilosDisponibles'].load();
            Ext.MessageBox.close();
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

    Ext.getCmp('txtIdInterfacesDisponibles').setVisible(false);
    Ext.getCmp('txtIdInterfacesDisponibles').setValue(1);
    intIdInterfaceElemento = 0;
    Ext.getCmp('chbxIdObraCivil').setVisible(true);
    Ext.getCmp('chbxIdObservacionRegeneracion').setVisible(true);
    Ext.getCmp('txtIdObservacionRegeneracion').setVisible(true);
    Ext.getCmp('cbxPuertos').setVisible(true);
    Ext.getCmp('cbxPuertos').reset();
    return objResponse;
}

function cierraVentanaIngresoFactibilidad(winIngresoFactibilidad) {
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
    var strPrefijoEmpresa = rec.get("strPrefijoEmpresa");
    strUltimaMilla    = rec.get('ultimaMilla');
    
    if( strUltimaMilla == 'FTTx')
    {
        strPrefijoEmpresa = 'MD';
    }    
    
    var arrayParametros = [];
    arrayParametros['strPrefijoEmpresa'] = strPrefijoEmpresa;
    arrayParametros['strDescripcionTipoRol'] = 'Contacto';
    arrayParametros['strEstadoTipoRol'] = 'Activo';
    arrayParametros['strDescripcionRol'] = 'Contacto Tecnico';
    arrayParametros['strEstadoRol'] = 'Activo';
    arrayParametros['strEstadoIER'] = 'Activo';
    arrayParametros['strEstadoIPER'] = 'Activo';
    arrayParametros['strEstadoIPC'] = 'Activo';
    arrayParametros['intIdPersonaEmpresaRol'] = rec.get("intIdPersonaEmpresaRol");
    arrayParametros['intIdPunto'] = rec.get("id_punto");
    arrayParametros['idStore'] = 'storeContactoShowFac';
    arrayParametros['strJoinPunto'] = '';
    strDefaultType = "hiddenfield";
    strXtype = "hiddenfield";
    boolHideTnField = true;
    boolCheckObraCivil = false;
    boolCheckObservacionRegeneracion = false;
    storeRolContacto = '';
    storeRolContactoPunto = '';
    if ("TN" === strPrefijoEmpresa) {
        if ("true" == rec.get("strObraCivil"))
        {
            boolCheckObraCivil = true;
        }
        if ("true" == rec.get("strPermisosRegeneracion"))
        {
            boolCheckObservacionRegeneracion = true;
        }
        boolHideTnField = false;
        storeRolContacto = obtienInfoPersonaContacto(arrayParametros);
        arrayParametros['intIdPersonaEmpresaRol'] = '';
        arrayParametros['idStore'] = 'storeContactoPuntoShowFac';
        arrayParametros['strJoinPunto'] = 'PUNTO';
        storeRolContactoPunto = obtienInfoPersonaContacto(arrayParametros);
        strDefaultType = "textfield";
        strXtype = "fieldset";
    }
    var boolEsEdificio = true;
    var boolDependeDeEdificio = true;
    var boolNombreEdificio = true;
    if ("S" === rec.get("strEsEdificio")) {
        boolEsEdificio = false;
    }
    if ("S" === rec.get("strDependeDeEdificio")) {
        boolDependeDeEdificio = false;
    }
    if (false === boolDependeDeEdificio || false === boolEsEdificio) {
        boolNombreEdificio = false;
    }
    winFactibilidad = "";
    formPanelFactibilidad = "";
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
                            xtype: 'fieldset',
                            layout: {
                                tdAttrs: {style: 'padding: 5px;'},
                                type: 'table',
                                columns: 1,
                                pack: 'center'
                            },
                            items: [
                                {
                                    xtype: strXtype,
                                    title: 'Datos Contacto Tecnico',
                                    style: "font-weight:bold; margin-bottom: 5px;",
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
                                            name: 'strTipoEnlace',
                                            id: 'strTipoEnlace',
                                            value: rec.get("strTipoEnlace"),
                                            allowBlank: false,
                                            readOnly: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            fieldLabel: 'Tipo de Red',
                                            name: 'tipoRed',
                                            id: 'tipoRed',
                                            value: rec.get("strTipoRed"),
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
                        pack: 'center'
                    },
                    defaults: {
                        width: '350px'
                    },
                    items: [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Modelo ' + rec.get("strNombreTipoElementoPadre"),
                            name: 'txtNombreModeloElementoPadre',
                            id: 'txtIdNombreModeloElementoPadre',
                            value: rec.get("strNombreModeloElementoPadre"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Marca ' + rec.get("strNombreTipoElementoPadre"),
                            name: 'txtNombreMarcaElementoPadre',
                            id: 'txtIdNombreMarcaElementoPadre',
                            value: rec.get("strNombreMarcaElementoPadre"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            colspan: 2,
                            fieldLabel: 'Nombre ' + rec.get("strNombreTipoElementoPadre"),
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
                            fieldLabel: 'Modelo ' + rec.get("strNombreTipoElementoDist"),
                            name: 'txtNombreTipoElementoDist',
                            id: 'txtIdNombreTipoElementoDist',
                            value: rec.get("strNombreTipoElementoDist"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Marca ' + rec.get("strNombreTipoElementoDist"),
                            name: 'txtNombreMarcaElementoDist',
                            id: 'txtIdNombreMarcaElementoDist',
                            value: rec.get("strNombreMarcaElementoDist"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Nombre ' + rec.get("strNombreTipoElementoDist"),
                            name: 'splitter_servicio',
                            id: 'splitter_servicio',
                            value: rec.get("splitter"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Puerto ' + rec.get("strNombreTipoElementoDist"),
                            name: 'txtNombreInfoInterfaceElementoDist',
                            id: 'txtIdNombreInfoInterfaceElementoDist',
                            value: rec.get("strNombreInfoInterfaceElementoDist"),
                            allowBlank: false,
                            readOnly: true
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Metraje',
                            name: 'txtNamefloatMetrajeShow',
                            hidden: boolHideTnField,
                            value: rec.get("strMetraje"),
                            id: 'txtIdfloatMetrajeShow',
                            readOnly: true
                        },
                        {
                            xtype: 'checkboxfield',
                            fieldLabel: 'Obra Civil',
                            name: 'chbxObraCivilShow',
                            id: 'chbxIdObraCivilShow',
                            checked: boolCheckObraCivil,
                            readOnly: true,
                            hidden: boolHideTnField
                        },
                        {
                            xtype: 'checkboxfield',
                            fieldLabel: 'Requiere permisos regeneración',
                            name: 'chbxObservacionRegeneracionShow',
                            id: 'chbxIdObservacionRegeneracionShow',
                            checked: boolCheckObservacionRegeneracion,
                            readOnly: true,
                            hidden: boolHideTnField
                        },
                        {
                            xtype: 'textarea',
                            fieldLabel: 'Observacion',
                            name: 'txtObservacionRegeneracionShow',
                            id: 'txtIdObservacionRegeneracionShow',
                            value: rec.get("strObservacionPermiRegeneracion"),
                            readOnly: true,
                            hidden: boolHideTnField
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

/*
 * Funcion utilizada para realizar la edicion de la factibilidad de servicios radio TN
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0  
 * 
 * @since 1.0
 */
function showEditaFactibilidadRadioTn(rec)
{
    var intIdElemento           = 0;
    var intIdInterfaceElemento  = 0;
    var arrayParametros         = [];
    var strPrefijoEmpresa       = rec.get("strPrefijoEmpresa");
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
        arrayPersonaContacto        = obtienInfoPersonaContactoRadio(arrayParametros);
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
                                                            cierraVentanaIngresoFactibilidad();
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
                        
                        parametros = {esTercerizada:                esTercerizada, 
                                      tercerizadora:                tercerizadora, 
                                      id:                           id_factibilidad, 
                                      elemento_radio_id:            cmb_RADIO, 
                                      elemento_switch_id:           idElementoSwitch, 
                                      interface_elemento_switch_id: idPuertoSwitch,
                                      procesoFactibilidad:          'edicionFactibilidad'
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
                                        cierraVentanaEditaFactibilidadRadio();
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
                        cierraVentanaEditaFactibilidadRadio();
                    }
                }
            ]
        });

        winEditaFactibilidad = Ext.widget('window', {
            title: 'Ingreso de Nuevos Datos de Factibilidad',
            layout: 'fit',
            resizable: false,
            modal: true,
            closable: false,
            items: [formPanelIngresoFactibilidad]
        });
    }

    winEditaFactibilidad.show();
}

function obtienInfoPersonaContactoRadio(arrayParametros) {
    var arrayReturn = [];
    arrayReturn['strNombres']   = 'No encontrado';
    arrayReturn['strApellidos'] = 'No encontrado';

    Ext.Ajax.request({
        url: urlContactoClienteByTipoRolAjax,
        method: 'POST',
        timeout: 60000,
        async: false,
        params: {
            strDescripcionTipoRol: arrayParametros['strDescripcionTipoRol'],
            strEstadoTipoRol: arrayParametros['strEstadoTipoRol'],
            strDescripcionRol: arrayParametros['strDescripcionRol'],
            strEstadoRol: arrayParametros['strEstadoRol'],
            strEstadoIER: arrayParametros['strEstadoIER'],
            strEstadoIPER: arrayParametros['strEstadoIPER'],
            strEstadoIPC: arrayParametros['strEstadoIPC'],
            intIdPersonaEmpresaRol: arrayParametros['intIdPersonaEmpresaRol']
        },
        success: function(response) {
            var text = Ext.decode(response.responseText);
            if ("100" !== text.strStatus) {
                Ext.Msg.alert('Error', text.strMessageStatus);
            }
            if (0 !== text.total) {
                arrayReturn['strNombres'] = text.encontrados[0].nombres;
                arrayReturn['strApellidos'] = text.encontrados[0].apellidos;
            }
        },
        failure: function(result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText.trim());
        }
    });
    return arrayReturn;
} //obtienInfoPersonaContactoRadio

function cierraVentanaEditaFactibilidadRadio() {
    winEditaFactibilidad.close();
    winEditaFactibilidad.destroy();
}

function showIngresoFactibilidadDC(rec)
{
    if(rec.get("continuaFlujoDC") === 'N')
    {
        Ext.MessageBox.show({
            title: 'Alerta',
            msg: `Para Generar Factibilidad del producto <b>${rec.get('producto')}</b>, 
                  es necesario tener Factibilidad ingresada para los siguientes Servicios:
                  </br><b>${rec.get("productosCoreAsociados")}</b>`,
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
        });
    }
    else
    {
        //Mostrar Pantalla de Factibilidad para Servicios con flujo DC
        var boolEsHousing            = true;
        var boolContieneAlquilerServ = false;
        
        //Se Valida si el producto INTERNET viene de HOSTING
        if(rec.get('nombreTecnico')==='HOSTING')
        {
            boolEsHousing = false;
        }
                
        if(rec.get('contieneAlquilerServidor') === 'S')
        {
            boolContieneAlquilerServ = true;
        }
            
        winIngresoFactibilidad = "";
        formPanelInresoFactibilidad = "";
        if(!winIngresoFactibilidad)
        {
            //******** html campos requeridos...
            var iniHtmlCamposRequeridos = `<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">
                                           <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;* Campos requeridos</p>`;
            var CamposRequeridos = Ext.create('Ext.Component', {
                html: iniHtmlCamposRequeridos,
                padding: 1,
                layout: 'anchor',
                style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
            });
            
            var storeSwitch = new Ext.data.Store({
                pageSize: 10,
                total: 'total',
                proxy: {
                    timeout: 3000000,
                    type: 'ajax',
                    url: getElementoSwitch,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombreElemento: '',
                        marcaElemento: '',
                        modeloElemento: '',
                        canton: '',
                        jurisdiccion: '',
                        tipoElemento: 'SWITCH',
                        estado: 'Todos',
                        busquedaDetalle: 'SI',
                        nombreDetalle:'ES_SWITCH_DC',
                        valorDetalle:'SI',
                        idElemento :''
                    }
                },
                fields:
                    [
                        {name: 'idElemento',     mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
            });
            
            var storeSwitch2k = new Ext.data.Store({
                pageSize: 10,
                total: 'total',
                proxy: {
                    timeout: 3000000,
                    type: 'ajax',
                    url: getElementoSwitch,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombreElemento: '',
                        marcaElemento: '',
                        modeloElemento: '',
                        canton: '',
                        jurisdiccion: '',
                        tipoElemento: 'SWITCH',
                        estado: 'Todos',
                        busquedaDetalle: 'SI',
                        nombreDetalle:'ES_SWITCH_DC_2_NIVEL',
                        valorDetalle:'SI',
                        idElemento:''
                    }
                },
                fields:
                    [
                        {name: 'idElemento', mapping: 'idElemento'},
                        {name: 'nombreElemento', mapping: 'nombreElemento'}
                    ]
            });

            var storeInterfacesElemento = new Ext.data.Store({
                pageSize: 100,
                proxy: {
                    type: 'ajax',
                    timeout: 400000,
                    url: getInterfacesPorElemento,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                    [
                        {name: 'idInterface', mapping: 'idInterface'},
                        {name: 'nombreInterface', mapping: 'nombreInterface'},
                        {name: 'nombreEstadoInterface', mapping: 'nombreEstadoInterface'}
                    ]
            });
            
            if(!boolContieneAlquilerServ)
            {            
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
                            idServicio         : rec.get("id_servicio"),
                            idServicioAlquiler : rec.get("idServicioAlqEspacioDC"),
                            subgrupo           : rec.get('subgrupo')
                        }
                    },
                    fields:
                        [
                            {name: 'nombreFila', mapping: 'nombreFila'},
                            {name: 'nombreRack', mapping: 'nombreRack'},
                            {name: 'reservados', mapping: 'reservados'},
                            {name: 'tipoEspacio',mapping: 'tipoEspacio'},
                            {name: 'storage',    mapping: 'storage'},
                            {name: 'memoria',    mapping: 'memoria'},
                            {name: 'procesador', mapping: 'procesador'},
                            {name: 'vcenter',    mapping: 'vcenter'},
                            {name: 'cluster',    mapping: 'cluster'}
                        ]
                });

                var gridInformacionEspacio = Ext.create('Ext.grid.Panel',
                    {                    
                        id: 'gridInformacionEspacio',
                        store: storeInformacionEspacio,
                        columnLines: true,                    
                        columns:
                            [
                                {
                                    header: boolEsHousing?'<b>Número de Fila</b>':'<b>Storage</b>',
                                    dataIndex: boolEsHousing?'nombreFila':'storage',
                                    width: boolEsHousing?100:70,
                                    sortable: true
                                }, 
                                {
                                    header: boolEsHousing?'<b>Nombre de Rack</b>':'<b>Memoria Ram</b>',
                                    dataIndex: boolEsHousing?'nombreRack':'memoria',
                                    width: boolEsHousing?120:90
                                },
                                {
                                    header: boolEsHousing?'<i class="fa fa-hashtag" aria-hidden="true"></i>&nbsp;<b>Cantidad (US) Contratadas</b>':
                                                           '<b>Procesador</b>',
                                    dataIndex: boolEsHousing?'reservados':'procesador',
                                    width: boolEsHousing?170:90
                                },
                                {
                                    header: boolEsHousing?'<b>Tipo Espacio Contratado</b>':'<b>VCenter</b>',
                                    dataIndex: boolEsHousing?'tipoEspacio':'vcenter',
                                    width: boolEsHousing?150:170
                                },
                                {
                                    header: '<b>Cluster</b>',
                                    dataIndex: 'cluster',
                                    width: 120,
                                    hidden:boolEsHousing
                                }
                            ],
                        viewConfig:
                            {
                                stripeRows: true,
                                enableTextSelection: true,
                                loadingText: "Cargando Información de "+rec.get('subgrupo')+"..."
                            },
                        frame: true,
                        height: "auto"
                    });
            }
            else
            {
                var storeServidores = new Ext.data.Store({
                    pageSize: 14,
                    total: 'total',
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url: urlGetServidoresAlquiler,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        },
                        extraParams: {
                            idServicio: rec.get("id_servicio")
                        }
                    },
                    fields:
                        [
                            {name: 'idElemento', mapping: 'idElemento'},
                            {name: 'idServicio', mapping: 'idServicio'},
                            {name: 'nombreElemento', mapping: 'nombreElemento'},
                            {name: 'modelo', mapping: 'modelo'},
                            {name: 'storage', mapping: 'storage'},
                            {name: 'datastore', mapping: 'datastore'}
                        ]
                });

                var gridServidores = Ext.create('Ext.grid.Panel', {
                    width: 550,
                    height: 120,
                    store: storeServidores,
                    loadMask: true,
                    frame: false,
                    columns: [
                        {
                            id: 'idServicio',
                            header: 'idServicio',
                            dataIndex: 'idServicio',
                            hidden: true,
                            hideable: false
                        },
                        {
                            id: 'idElemento',
                            header: 'idElemento',
                            dataIndex: 'idElemento',
                            hidden: true,
                            hideable: false
                        },
                        {
                            id: 'servidor',
                            header: 'Servidor',
                            dataIndex: 'nombreElemento',
                            width: 100,
                            sortable: true
                        },
                        {
                            id: 'modelo',
                            header: 'Modelo',
                            dataIndex: 'modelo',
                            width: 200,
                            sortable: true
                        },
                        {
                            id: 'storage',
                            header: 'Storage',
                            dataIndex: 'storage',
                            width: 80,
                            renderer: function(val)
                            {                    
                                return '<label style="color:green;font-weight: bold;">' + val + '</label>';
                            }
                        },
                        {
                            id: 'datastore',
                            header: 'DataStore',
                            dataIndex: 'datastore',
                            width: 150,
                            renderer: function(val)
                            {
                                return '<label style="color:green;font-weight: bold;">' + val + '</label>';
                            }
                        }           
                    ]
                });  
            }

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
                                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos del Cliente</b>',
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
                                    }
                                ]
                            },
                            {
                                xtype: 'fieldset',
                                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos del Servicio</b>',
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
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Tipo Enlace',
                                        name: 'strTipoEnlace',
                                        id: 'strTipoEnlace',
                                        value: rec.get("strTipoEnlace"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Capacidad 1',                                        
                                        value: rec.get("capacidad1"),
                                        allowBlank: false,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Capacidad 2',
                                        value: rec.get("capacidad2"),
                                        allowBlank: false,
                                        readOnly: true
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'fieldset',
                        title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos de Factibilidad Housing</b>',
                        defaultType: 'textfield',
                        style: "font-weight:bold; margin-bottom: 5px;",
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 2,
                            pack: 'center'
                        },
                        items: [
                            {
                                xtype: 'fieldset',
                                style: "border:0;align:center;"+boolEsHousing?"margin-left: 15%;":"margin-right: 25%;",
                                items: [                
                                    boolContieneAlquilerServ ? gridServidores : gridInformacionEspacio
                                ]
                            }
                        ]
                    },
                    //Datos de Factibilidad NEXUS/SWITCH DATACENTER ( IT )
                    {
                        xtype: 'fieldset',
                        title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Datos de Factibilidad</b>',
                        defaultType: 'textfield',
                        style: "font-weight:bold; margin-bottom: 5px;",
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 3,
                            pack: 'center'
                        },
                        items: [
                            {
                                xtype: 'fieldset',
                                style: "border:0",
                                items: [      
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: '<i class="fa fa-exchange" aria-hidden="true"></i>&nbsp;<b>* Medio Transmisión</b>',
                                        id: 'cmbUltimaMilla',
                                        name: 'cmbUltimaMilla',
                                        value: 'Seleccione',
                                        width: 300,
                                        store: [
                                            ['Seleccione','Seleccione'],
                                            ['UTP','UTP'],
                                            ['FO','Fibra Optica']
                                        ],
                                        listeners: {
                                            select: function(combo) 
                                            {
                                                Ext.getCmp('cmbElementoSwitch').setDisabled(false);
                                                Ext.getCmp('cmbInterfaceSwitch').setDisabled(true);
                                                Ext.getCmp('cmbElementoNexus2k').setDisabled(true);
                                                
                                                Ext.getCmp('cmbElementoNexus2k').setValue("");
                                                Ext.getCmp('cmbElementoNexus2k').setRawValue("");
                                                
                                                Ext.getCmp('cmbInterfaceSwitch').setValue("");
                                                Ext.getCmp('cmbInterfaceSwitch').setRawValue("");
                                            }
                                        }
                                    },
                                    {
                                        loadingText: 'Buscando ...',
                                        xtype: 'combobox',
                                        name: 'cmbElementoSwitch',
                                        id: 'cmbElementoSwitch',
                                        fieldLabel: '<i class="fa fa-server" aria-hidden="true"></i>&nbsp;<b>* Nexus (5k)</b>',
                                        displayField: 'nombreElemento',
                                        queryMode: "remote",
                                        valueField: 'idElemento',
                                        store: storeSwitch,
                                        disabled:true,
                                        width: 300,
                                        minChars: 3,                                        
                                        listeners: {
                                            select: function(combo) 
                                            {
                                                Ext.getCmp('cmbInterfaceSwitch').setValue("");
                                                Ext.getCmp('cmbInterfaceSwitch').setRawValue("");
                                                //Si es FO ( va directo al Nexus 5k)
                                                if(Ext.getCmp("cmbUltimaMilla").getValue() === 'FO')
                                                {
                                                    storeInterfacesElemento.proxy.extraParams = {idElemento: combo.getValue(), estado: 'not connect'};
                                                    storeInterfacesElemento.load({params: {}});
                                                    Ext.getCmp('cmbInterfaceSwitch').setDisabled(false);
                                                }
                                                else
                                                {
                                                    Ext.getCmp('cmbElementoNexus2k').setDisabled(false);
                                                    //Carga el store de los nexus atachados
                                                    storeSwitch2k.proxy.extraParams = {idElemento: combo.getValue(), 
                                                                                       busquedaDetalle: 'SI',
                                                                                       nombreDetalle:'ES_SWITCH_DC_2_NIVEL'};
                                                    storeSwitch2k.load({params: {}});
                                                }
                                            }
                                        }
                                    },
                                    {
                                        loadingText: 'Buscando ...',
                                        xtype: 'combobox',
                                        name: 'cmbElementoNexus2k',
                                        id: 'cmbElementoNexus2k',
                                        fieldLabel: '<i class="fa fa-server" aria-hidden="true"></i>&nbsp;<b>* Nexus (2k)</b>',
                                        displayField: 'nombreElemento',
                                        valueField: 'idElemento',
                                        store: storeSwitch2k,
                                        disabled:true,
                                        width: 300,
                                        minChars: 3,                                        
                                        listeners: {
                                            select: function(combo) 
                                            {                            
                                                Ext.getCmp('cmbInterfaceSwitch').setValue("");
                                                Ext.getCmp('cmbInterfaceSwitch').setRawValue("");
                                                
                                                storeInterfacesElemento.proxy.extraParams = {idElemento: combo.getValue(), estado: 'not connect'};
                                                storeInterfacesElemento.load({params: {}});
                                                Ext.getCmp('cmbInterfaceSwitch').setDisabled(false);
                                            }
                                        }
                                    },
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'cmbInterfaceSwitch',
                                        name: 'cmbInterfaceSwitch',
                                        fieldLabel: '<i class="fa fa-plug" aria-hidden="true"></i>&nbsp;<b>* Puerto</b>',
                                        disabled:true,
                                        displayField: 'nombreEstadoInterface',
                                        valueField: 'idInterface',
                                        loadingText: 'Buscando Puertos...',
                                        store: storeInterfacesElemento,
                                        width: 300
                                    },                                    
                                    {
                                        xtype: 'textarea',
                                        fieldLabel: '<i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;<b>Observación</b>',
                                        name: 'observacionDirecto',
                                        id: 'observacionDirecto',
                                        value: '',
                                        width: 500
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

                            var boolError    = false;
                            var parametros;
                            var mensajeError = "";
                            
                            var intIdSolFactibilidad  = rec.get("id_factibilidad");
                            var intElementoDirecto    = Ext.getCmp('cmbElementoSwitch').value;
                            var intInterfaceDirecto   = Ext.getCmp('cmbInterfaceSwitch').value;
                            var strUltimaMilla        = rec.get('strCodigoTipoMedio');                            
                            var strObservacionDirecto = Ext.getCmp('observacionDirecto').value;

                            if(intElementoDirecto == null || intElementoDirecto == "")
                            {
                                boolError = true;
                                mensajeError += "Seleccione el elemento.\n";
                            }
                            if(intInterfaceDirecto == null || intInterfaceDirecto == "")
                            {
                                boolError = true;
                                mensajeError += "Seleccione la interface del elemento.\n";
                            }

                            parametros = {
                                intElementoDirecto   : intElementoDirecto,
                                intInterfaceDirecto  : intInterfaceDirecto,
                                strUltimaMilla       : strUltimaMilla,
                                strMetrajeDirecto    : '',
                                strObservacionDirecto: strObservacionDirecto,
                                strTipoBackone       : 'DIRECTO',
                                intIdSolFactibilidad : intIdSolFactibilidad
                            };

                            if(!boolError)
                            {
                                connFactibilidad.request({
                                    url: urlNuevaFactibilidad,
                                    method: 'post',
                                    timeout: 120000,
                                    params: parametros,
                                    success: function(response) {
                                        var text = response.responseText;
                                        cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                                        if(text === "Se modifico Correctamente el detalle de la Solicitud de Factibilidad")
                                        {
                                            Ext.Msg.alert('Mensaje', text, function(btn) {
                                                if(btn == 'ok') {
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
                                        cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
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
                            cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
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
}
