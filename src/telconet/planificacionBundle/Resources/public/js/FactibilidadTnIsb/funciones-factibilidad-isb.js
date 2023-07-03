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
/************************** VER MAPA ************************************ */
/************************************************************************ */
function showVerMapa(rec) {
    winVerMapa = "";

    if (rec.get("latitud") != 0 && rec.get("longitud") != 0)
    {
        if (!winVerMapa)
        {
            formPanelMapa = Ext.create('Ext.form.Panel', {
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

        if (markerPto)
            markerPto.setMap(null);

        markerPto = new google.maps.Marker({
            position: latlng,
            map: mapa
        });
        mapa.setZoom(17);
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
    boolContactos         = true;

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

    if (rec.get("booleanTipoRedGpon")) {
        boolContactos = false;
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
                            hidden: boolContactos,
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
                            readOnly: true,
                            hidden: !rec.get("booleanTipoRedGpon")
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
    var intIdElemento          = 0;
    var intIdInterfaceElemento = 0;
    var arrayParametros        = [];
    var strEsFttx              = '';
    var strPrefijoEmpresa      = '';
    if(rec.get('ultimaMilla') === 'FTTx')
    {
        strEsFttx = 'SI';
        if (rec.get("strPrefijoEmpresa") == 'TNP')
        {
            strPrefijoEmpresa = rec.get("strPrefijoEmpresa");
        }
        else
        {
            strPrefijoEmpresa = 'MD';
        }
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
    strNombres = '';
    strApellidos = '';
    strErrorMetraje = '';
    boolCheckObraCivil = false;
    boolCheckObservacionRegeneracion = false;
    boolContactos         = true;
    storeRolContacto = '';
    storeRolContactoPunto = '';
    strXtype = "hiddenfield";

    if (rec.get("booleanTipoRedGpon"))
    {
        boolContactos = false;
        storeRolContacto = obtienInfoPersonaContacto(arrayParametros);
        arrayParametros['intIdPersonaEmpresaRol'] = '';
        arrayParametros['idStore'] = 'storeContactoPuntoIngFac';
        arrayParametros['strJoinPunto'] = 'PUNTO';
        storeRolContactoPunto = obtienInfoPersonaContacto(arrayParametros);
        strDefaultType        = "textfield";
        strXtype              = "fieldset";
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

    winIngresoFactibilidad = "";
    formPanelInresoFactibilidad = "";
    if (!winIngresoFactibilidad)
    {
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
                    procesoBusqueda: 'limitado'
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
                                            value: rec.get("strTipoRed"),
                                            allowBlank: false,
                                            readOnly: true,
                                            hidden: !rec.get("booleanTipoRedGpon")
                                        }
                                    ]
                                },
                                {
                                    xtype: strXtype,
                                    title: 'Datos Contacto Tecnico',
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    layout: 'anchor',
                                    hidden: boolContactos,
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
                                                Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                Ext.getCmp('txtIdTipoElemento').setValue("");
                                                Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                Ext.getCmp('txtIdPuerto').setValue("");

                                                Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                Ext.getCmp('txtIdfloatMetraje').setVisible(false);
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
                                                Ext.getCmp('txtIdUltimaMilla').setValue("");
                                                Ext.getCmp('txtIdTipoElemento').setValue("");
                                                Ext.getCmp('txtIdfloatMetraje').setValue("");
                                                Ext.getCmp('txtIdPuerto').setValue("");

                                                Ext.getCmp('txtIdNameElementoPadre').setVisible(false);
                                                Ext.getCmp('txtIdUltimaMilla').setVisible(false);
                                                Ext.getCmp('txtIdTipoElemento').setVisible(false);
                                                Ext.getCmp('txtIdfloatMetraje').setVisible(false);
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
                                                if ("TN" !== strPrefijoEmpresa) {
                                                    Ext.getCmp('txtTipoBackBone').setValue("RUTA");
                                                    Ext.getCmp('txtTipoBackBone').setVisible(true);
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
                                }
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
                        var tipoBackbone         = "RUTA";
                        var intIdSolFactibilidad = rec.get("id_factibilidad");                                                
                        var intIdElementoCaja = Ext.getCmp('cbxIdElementoCaja').value;
                        var intElementoPNivel = Ext.getCmp('cbxElementoPNivel').value;
                        var intInterfaceElementoDistribucion = "";
                        var chbxObraCivil = false;

                        var intPuertosDisponibles = Ext.getCmp('txtIdInterfacesDisponibles').value;

                        var chbxObservacionRegeneracion = false;
                        var strObservacionRegeneracion  = "";
                        var floatMetraje                = "";

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
                            strUltimaMilla: rec.get('ultimaMilla'),
                            strTipoRed: rec.get("strTipoRed")
                        };
                        
                        if (!boolError)
                        {
                            connFactibilidad.request({
                                url: urlNuevaFactibilidadFttx,
                                method: 'post',
                                timeout: 120000,
                                params: parametros,
                                success: function(response) {
                                    var text = response.responseText;
                                    cierraVentanaIngresoFactibilidad(winIngresoFactibilidad);
                                    if (text == "Se modifico Correctamente el detalle de la Solicitud de Factibilidad"
                                        || ( rec.get('ultimaMilla') === 'FTTx' 
                                            && text.toString().indexOf("Se modifico Correctamente el detalle de la Solicitud de Factibilidad") != -1))
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
    arrayParametros['idStore'] = 'storeContactoShowFac';
    arrayParametros['strJoinPunto'] = '';
    strDefaultType = "hiddenfield";
    strXtype = "hiddenfield";
    boolHideTnField = true;
    boolCheckObraCivil = false;
    boolCheckObservacionRegeneracion = false;
    storeRolContacto = '';
    storeRolContactoPunto = '';
    if ("TN" === rec.get("strPrefijoEmpresa")) {
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
