/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var winVerMapa;
var winVerCroquis;
var winRechazarOrden_Factibilidad;
var winFactibilidad;
var winFactibilidadMateriales;
var winPreFactibilidad;

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

function rechazarFactibilidadNodoCliente(rec)
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

    formPanelRechazarOrden_Factibilidad = Ext.create('Ext.form.Panel', {
        buttonAlign: 'center',
        BodyPadding: 10,
        bodyStyle: "background: white; padding:10px; border: 0px none;",
        frame: true,
        items: [
            {
                xtype: 'fieldset',
                title: 'Datos del Rechazo de ' + rec.get('nombreElemento'),
                defaultType: 'textfield',
                style: "font-weight:bold; margin-bottom: 15px;",
                defaults: {
                    width: '350px'
                },
                items: [
                    {
                        xtype: 'combobox',
                        id: 'cmbMotivo',
                        fieldLabel: 'Motivo',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField: 'nombre_motivo',
                        valueField: 'id_motivo',
                        selectOnTab: true,
                        store: storeMotivos,
                        lazyRender: true,
                        queryMode: "local",
                        listClass: 'x-combo-list-small'
                    }
                    , {
                        xtype: 'textarea',
                        fieldLabel: 'Observacion',
                        name: 'info_observacion',
                        id: 'info_observacion',
                        allowBlank: false
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
                            params: {
                                id_motivo: cmbMotivo,
                                observacion: txtObservacion,
                                idSolicitud: rec.get('idSolicitud'),
                                idElemento: rec.get('idElemento')},
                            url: url_rechazar,
                            success: function(response) {
                                var text = response.responseText;
                                cierraVentanaRechazarOrden_Factibilidad();
                                if (text == "OK")
                                {
                                    Ext.Msg.alert('Mensaje', 'Se rechazó correctamente.', function(btn) {
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
        title: 'Rechazo de factibilidad',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: false,
        items: [formPanelRechazarOrden_Factibilidad]
    });


    winRechazarOrden_Factibilidad.show();
}


function showPreFactibilidad(rec)
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
                title: 'Datos de Factibilidad de ' + rec.get('nombreElemento'),
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
                handler: function() {
                    var fechaProgramacion = Ext.getCmp('fechaProgramacion').value;
                    var txtObservacion = Ext.getCmp('info_observacion').value;

                    var boolError = false;
                    var mensajeError = "";
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
                            url: url_fechaFactibilidad,
                            timeout: 120000,
                            method: 'post',
                            params: {
                                idSolicitud: rec.get('idSolicitud'),
                                fechaProgramacion: fechaProgramacion,
                                observacion: txtObservacion
                            },
                            success: function(response) {
                                var text = response.responseText;
                                if (text == "OK")
                                {

                                    Ext.Msg.alert('Mensaje', 'Se modifico Correctamente el detalle de la Solicitud de Factibilidad', function(btn) {
                                        if (btn == 'ok') {
                                            store.load();
                                        }
                                    });
                                    cierraVentanaPreFactibilidad();
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


    winPreFactibilidad.show();
}

function cierraVentanaPreFactibilidad() {
    winPreFactibilidad.close();
    winPreFactibilidad.destroy();
}


function showIngresoFactibilidadTN(rec)
{
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
    
    storeElementos = new Ext.data.Store({
        total: 'total',
        autoDestroy: true,
        autoLoad: false,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            url: url_getElementosPseudoPe,
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
                nombre: this.nombreElemento,
                administra: rec.get('administra'),
                idCanton: rec.get('idCanton')
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
            ],
    });
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        width: 400,
        height: 125,
        waitMsgTarget: true,
        items: [
            {
                xtype: 'textfield',
                id: 'administra',
                fieldLabel: 'Administra',
                width: 350,
                disabled: true,
                value: rec.get('administra')
            },
            {
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                xtype: 'combobox',
                name: 'elementoPseudo',
                id: 'elementoPseudo',
                typeAhead: true,
                triggerAction: 'all',
                fieldLabel: 'Elemento',
                displayField: 'nombreElemento',
                queryMode: "remote",
                valueField: 'idElemento',
                selectOnTab: true,
                store: storeElementos,
                width: 350,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                forceSelection: true,
                emptyText: 'Ingrese el nombre del elemento..',
                minChars: 3,
                listeners: {
                    select: function(combo) {
                        
                        if (rec.get('administra') == 'CLIENTE')
                        {
                            storeInterfacesElemento.proxy.extraParams = {idElemento: combo.getValue(), estado: 'not connect'};
                            storeInterfacesElemento.load({params: {}});
                            Ext.getCmp('interfaceElemento').setVisible(true);
                        }
                    }
                },
            },
            {
                queryMode: 'local',
                xtype: 'combobox',
                id: 'interfaceElemento',
                name: 'interfaceElemento',
                fieldLabel: 'Puerto Elemento',                
                hidden: true,
                displayField: 'nombreInterface',
                valueField: 'idInterface',
                loadingText: 'Buscando...',
                store: storeInterfacesElemento,
                width: 350,
            },
        ],
                buttons: [
            {
                text: 'Guardar',
                handler: function() {
                    
                    var idElementoPseudo = Ext.getCmp('elementoPseudo').getValue();
                    var idInterface = Ext.getCmp('interfaceElemento').getValue();
                    
                    var texto = '';                    
                    
                    if(idElementoPseudo == null)
                    {
                        texto =  'Debe seleccionar el elemento. <br>';
                    }
                    
                    if (rec.get('administra') == 'CLIENTE')
                    {
                        if(idInterface == null)
                        {
                            texto = texto + 'Debe ingresar la interface del elemento. <br>';                        
                        }
                    }
                    
                    if (texto == '')
                    {
                    
                    connFactibilidad.request({
                        url: url_factibilidadPseudoPe,
                        method: 'post',
                        timeout: 120000,
                        params: {
                            idSolicitud: rec.get('idSolicitud'),
                            idElemento: rec.get('idElemento'),
                            idElementoPseudo: idElementoPseudo,
                            idInterface: idInterface,
                            tipo : 'PSEUDOPE'
                            
                        },
                        success: function(response) {
                            var text = response.responseText;
                            closeVentanaIngresoFactibilidad();
                            if (text == "OK")
                            {
                                Ext.Msg.alert('Mensaje', 'Transacción Exitosa', function(btn) {
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
                                closeVentanaIngresoFactibilidad();
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
                else
                {
                    Ext.Msg.alert('Error', texto, function(btn) {

                    });
                }

                }
            },
            {
                text: 'Cerrar',
                handler: function() {
                    closeVentanaIngresoFactibilidad();
                }
            }
        ]        
    });    

    winIngresoFactibilidad = Ext.widget('window', {
        title: 'Ingreso equipos a Pseudo PE '+rec.get('nombreElemento'),        
        resizable: true,
        modal: true,
        closable: false,
        items: [formPanel]
    });
    
    winIngresoFactibilidad.show();
}

function showTercerizadoras(tipo,rec)
{
    var storeTercerizadora = new Ext.data.Store({
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url: url_getTercerizadoras,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idTercerizadora',     mapping: 'idTercerizadora'},
                {name: 'nombreTercerizadora', mapping: 'nombreTercerizadora'}
            ]
    });
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        width: 400,
        height: 70,
        waitMsgTarget: true,
        items: [
            {
                selectOnFocus: true,
                loadingText: 'Buscando ...',
                hideTrigger: false,
                xtype: 'combobox',
                name: 'cmbTercerizadoras',
                id: 'cmbTercerizadoras',
                fieldLabel: '<b>Tercerizadora</b>',
                displayField: 'nombreTercerizadora',
                queryMode: "local",
                valueField: 'idTercerizadora',
                selectOnTab: true,
                store: storeTercerizadora,
                width: 350,
                lazyRender: true,
                listClass: 'x-combo-list-small',
                forceSelection: true,
                emptyText: 'Escoja el nombre de la Tercerizadora..'
            }
        ],
                buttons: [
            {
                text: 'Guardar Tercerizadora',
                handler: function() {
                    
                    var idTercerizadora = Ext.getCmp('cmbTercerizadoras').getValue();
                    var texto = '';                    
                    
                    if(idTercerizadora == null)
                    {
                        texto =  'Debe seleccionar la Tercerizadora';
                    }
                    
                    if (texto === '')
                    {
                        connFactibilidad.request({
                            url: url_guardarTercerizadora,
                            method: 'post',
                            timeout: 120000,
                            params: 
                            {
                                idElemento      : rec.get('idElemento'),
                                idTercerizadora : idTercerizadora
                            },
                            success: function(response) {
                                var text = response.responseText;
                                closeVentanaIngresoFactibilidad();
                                if (text == "OK")
                                {
                                    factibilidadPseudoPe(tipo,rec.get('idSolicitud'),rec.get('idElemento'));
                                }
                                else {
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: text,
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                    closeVentanaIngresoFactibilidad();
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
                    else
                    {
                        Ext.Msg.alert('Error', texto);
                    }
                }
            },
            {
                text: 'Cerrar',
                handler: function() {
                    closeVentanaIngresoFactibilidad();
                }
            }
        ]        
    });    

    winIngresoFactibilidad = Ext.widget('window', {
        title: 'Ingreso de Tercerizadora relacionada a : <b>'+rec.get('nombreElemento')+'</b>',        
        resizable: true,
        modal: true,
        closable: false,
        items: [formPanel]
    });
    
    winIngresoFactibilidad.show();
}

function factibilidadPseudoPe(tipoAdministracion,idSolicitud,idElemento)
{
    connFactibilidad.request({
        url: url_flujoPseudoPe,
        method: 'post',
        timeout: 120000,
        params: {
            administra        : 'CLIENTE',
            tipoAdministracion: tipoAdministracion,
            idSolicitud       : idSolicitud,
            idElemento        : idElemento
        },
        success: function(response) {
            var text = response.responseText;
            if(text == "OK")
            {
                Ext.Msg.alert('Mensaje', 'Transacción Exitosa', function(btn) {
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
            Ext.MessageBox.show({
                title: 'Error',
                msg: result.responseText,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });
}


function showIngresoFactibilidad(rec)
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
        autoDestroy: true,
        autoLoad: false,
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
                nombre: this.nombreElemento,
                nivel: '2',
                idCanton: rec.get('idCanton'),
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
    });


    storeInformacionEspacio = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            autoLoad: false,
            model: 'UbicacionModelo',
            proxy: {
                type: 'ajax',
                url: '',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    estado: 'Activo'
                }
            }
        });

    Ext.define('cajaElemento', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idElemento', mapping: 'idElemento'},
            {name: 'nombreElemento', mapping: 'nombreElemento'}
        ]
    });

    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1,
        listeners: {
            edit: function() {
                gridInformacionEspacio.getView().refresh();
            }
        }
    });

    var selEspacioModelo = Ext.create('Ext.selection.CheckboxModel', {
        listeners: {
            selectionchange: function(sm, selections) {
                gridInformacionEspacio.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });

    gridInformacionEspacio = Ext.create('Ext.grid.Panel', {
        id: 'gridInformacionEspacio',
        store: storeInformacionEspacio,
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombreElemento',
                header: 'Caja',
                dataIndex: 'nombreElemento',
                width: 350,
                sortable: true,
                renderer: function(value, metadata, record, rowIndex, colIndex, store)
                {
                    record.data.idElemento = record.data.nombreElemento;
                    for (var i = 0; i < storeElementos.data.items.length; i++)
                    {

                        if (storeElementos.data.items[i].data.idElemento === record.data.idElemento)
                        {
                            record.data.nombreElemento = storeElementos.data.items[i].data.nombreElemento;
                            break;
                        }
                    }

                    return record.data.nombreElemento;
                },
                editor:
                    {
                        selectOnFocus: true,
                        loadingText: 'Buscando ...',
                        hideTrigger: false,
                        xtype: 'combobox',
                        name: 'cmb_CAJA',
                        typeAhead: true,
                        triggerAction: 'all',
                        displayField: 'nombreElemento',
                        queryMode: "remote",
                        valueField: 'idElemento',
                        selectOnTab: true,
                        store: storeElementos,
                        width: 350,
                        lazyRender: true,
                        listClass: 'x-combo-list-small',
                        forceSelection: true,
                        emptyText: 'Ingrese un nombre de Caja..',
                        minChars: 3
                    }
            }
        ],
        buttons: [
            {
                text: 'Guardar',
                handler: function() {

                    var array = new Object();
                    var contador;
                    var grid = gridInformacionEspacio;

                    array['total'] = grid.getStore().getCount();
                    array['data'] = new Array();

                    if (grid.getStore().getCount() !== 0)
                    {
                        var array_data = new Array();

                        for (var i = 0; i < grid.getStore().getCount(); i++)
                        {
                            if (grid.getStore().getAt(i).data.nombreElemento)
                            {
                                array_data.push(grid.getStore().getAt(i).data.nombreElemento);

                                //valido que no se repitan las
                                contador = 0;
                                for (var j = 0; j < grid.getStore().getCount(); j++)
                                {
                                    if (grid.getStore().getAt(j).data.nombreElemento === grid.getStore().getAt(i).data.nombreElemento)
                                    {
                                        contador++;
                                        if (contador > 1) {
                                            Ext.Msg.alert("Advertencia", "Las cajas no se deben repetir.");
                                            return false;
                                        }
                                    }
                                }
                            } else
                            {
                                Ext.Msg.alert("Advertencia", "El campo no debe ir en blanco");
                                return false;
                            }

                        }
                        array['data'] = array_data;

                        datosCajas = Ext.JSON.encode(array);
                    }
                    else
                    {
                        Ext.Msg.alert("Advertencia", "No ha ingresado la Informacion de espacio del Nodo");
                        return false;
                    }

                    connFactibilidad.request({
                        url: url_FactibilidadPuntoCliente,
                        method: 'post',
                        timeout: 120000,
                        params: {
                            datosCajas: datosCajas,
                            idSolicitud: rec.get('idSolicitud'),
                            idElemento: rec.get('idElemento')
                        },
                        success: function(response) {
                            var text = response.responseText;
                            closeVentanaIngresoFactibilidad();
                            if (text == "OK")
                            {
                                Ext.Msg.alert('Mensaje', 'Transacción Exitosa', function(btn) {
                                    if (btn == 'ok') {
                                        window.location.reload();
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
                                closeVentanaIngresoFactibilidad();
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
            },
            {
                text: 'Cerrar',
                handler: function() {
                    closeVentanaIngresoFactibilidad();
                }
            }
        ],
        selModel: selEspacioModelo,
        viewConfig: {
            stripeRows: true
        },
        tbar: [{
                xtype: 'toolbar',
                items: [{
                        itemId: 'removeButton',
                        text: 'Eliminar',
                        tooltip: 'Elimina el item seleccionado',
                        iconCls: 'remove',
                        disabled: true,
                        handler: function() {
                            eliminarSeleccion(gridInformacionEspacio);
                        }
                    }, '-', {
                        text: 'Agregar',
                        tooltip: 'Agrega un item a la lista',
                        iconCls: 'add',
                        handler: function() {

                            var grid = gridInformacionEspacio;

                            if (grid.getStore().getCount() !== 0)
                            {
                                for (var i = 0; i < grid.getStore().getCount(); i++)
                                {
                                    if (!grid.getStore().getAt(i).data.nombreElemento)
                                    {
                                        Ext.Msg.alert("Advertencia", "Favor ingrese datos de la caja");
                                        return false;
                                    } else
                                    {
                                        //valido que no se repitan las
                                        contador = 0;
                                        for (var j = 0; j < grid.getStore().getCount(); j++)
                                        {
                                            if (grid.getStore().getAt(j).data.nombreElemento === grid.getStore().getAt(i).data.nombreElemento)
                                            {
                                                contador++;
                                                if (contador > 1) {
                                                    Ext.Msg.alert("Advertencia", "Las cajas no se deben repetir.");
                                                    return false;
                                                }
                                            }
                                        }

                                    }
                                }


                            }
                            var r = Ext.create('cajaElemento', {
                                idElemento: '',
                                nombreElemento: ''
                            });

                            storeInformacionEspacio.insert(0, r);
                            cellEditing.startEditByPosition({row: 0, column: 1});
                        }
                    }]
            }],
        width: 400,
        height: 200,
        title: 'Agregue Cajas a la edificación ' + rec.get('nombreElemento'),
        renderTo: Ext.get('informacionEspacio'),
        plugins: [cellEditing]
    });

    winIngresoFactibilidad = Ext.widget('window', {
        title: 'Ingreso de Factibilidad',
        layout: 'fit',
        resizable: false,
        modal: true,
        closable: false,
        items: [gridInformacionEspacio]
    });


    winIngresoFactibilidad.show();
}

function eliminarSeleccion(datosSelect)
{
  for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
  {
	datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
  }
}

function closeVentanaIngresoFactibilidad() {
    winIngresoFactibilidad.close();
    winIngresoFactibilidad.destroy();
}

function cierraVentanaRechazarOrden_Factibilidad() {
    winRechazarOrden_Factibilidad.close();
    winRechazarOrden_Factibilidad.destroy();
}

/************************************************************************ */
/**************************** FACTIBILIDAD ****************************** */
/************************************************************************ */

function cierraVentanaFactibilidad() {
    winFactibilidad.close();
    winFactibilidad.destroy();
}


function obtienInfoPersonaContacto(arrayParametros) {
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
} //obtienInfoPersonaContacto
