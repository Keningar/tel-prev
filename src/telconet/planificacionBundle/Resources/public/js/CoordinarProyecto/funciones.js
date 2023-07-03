var winVerGoogleMapa;
var winVerCroquis;

/************************************************************************ */
/************************** VER MAPA ************************************ */
/************************************************************************ */
   
/**
 * Funciones, muestra la ubicacion de google map 
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 10-03-2023
 * @since 1.0
 * 
 * @param intLogintud
 * @param intLatitud
 */
function showViewGoogleMap(rec) {
    winVerGoogleMapa = "";

    if (rec.get("latitud") != 0 && rec.get("longitud") != 0)
    {
        if (!winVerGoogleMapa)
        {
            formPanelGoogleMapa = Ext.create('Ext.form.Panel', {
                BodyPadding: 10,
                frame: true,
                items: [
                    {
                        html: "<div id='map_canvas' style='width:575px; height:450px'></div>"
                    }
                ]
            });

            winVerGoogleMapa = Ext.widget('window', {
                title: 'Mapa del Punto',
                layout: 'fit',
                resizable: false,
                modal: true,
                closable: true,
                items: [formPanelGoogleMapa]
            });
        }

        winVerGoogleMapa.show();
        viewGoogleMapa(rec.get("latitud"), rec.get("longitud"));
    }
    else
    {
        alert('Estas coordenadas son incorrectas!!')
    }
}

function viewGoogleMapa(vlat, vlong) {
    var mapa = "";
    var ciudad = "";
    var markerPto = "";

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

        /*markerPto = new google.maps.Marker({
            position: latlng,
            map: mapa
        });*/
        mapa.setZoom(17);
    }
}

function cierraVentanaMapa() {
    winVerGoogleMapa.close();
    winVerGoogleMapa.destroy();

}

/************************************************************************ */
/************************** VER CROQUIS ********************************* */
/************************************************************************ */
/**
 * Muestra la del croquis 
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 10-03-2023
 * @since 1.0
 * 
 * @param strRutaImagen
 */
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
/********************** VER TAREAS CLIENTE ****************************** */
/************************************************************************ */
/**
 * Ver tarea del cliente 
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 10-03-2023
 * @since 1.0
 * 
 * @param strLogin
 */
function verTareasClientes(login) 
{
    
    btncancelar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function () {
            winTareasClientes.destroy();
        }
    });

    storeTareasClientes = new Ext.data.Store({
        //pageSize: 1000,
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: urlVerTareasClientes,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'result'
            },
            extraParams: 
            {
                login: login
            }
        },
        fields:
            [               
                { name: 'numeroTarea', mapping: 'numeroTarea' },
                { name: 'nombreProceso', mapping: 'nombreProceso' },
                { name: 'nombreTarea', mapping: 'nombreTarea' },
                { name: 'estado', mapping: 'estado' },
                { name: 'fechaCreacion', mapping: 'fechaCreacion' },
                { name: 'fechaEstado', mapping: 'fechaEstado' },
                { name: 'nombreDepartamento', mapping: 'nombreDepartamento' },
                { name: 'empleado', mapping: 'empleado' },
                { name: 'observacion', mapping: 'observacion' }
            ]
    });
    gridTareasCliente = Ext.create('Ext.grid.Panel', {
        id: 'gridTareasCliente',
        store: storeTareasClientes,
        columnLines: true,
        columns: [
            {
                id: 'numeroTarea',
                header: 'Número Tarea',
                dataIndex: 'numeroTarea',
                width: 80,
                sortable: true
            },
            {
                id: 'nombreProceso',
                header: 'Nombre Proceso',
                dataIndex: 'nombreProceso',
                width: 260,
                sortable: true
            },
            {
                id: 'nombreTarea',
                header: 'Nombre Tarea',
                dataIndex: 'nombreTarea',
                width: 260,
                sortable: true
            },
            {
                id: 'observacionTarea',
                header: 'Observación',
                dataIndex: 'observacion',
                width: 330,
                sortable: true
            },
            {
                id: 'estadoTarea',
                header: 'Estado',
                dataIndex: 'estado',
                width: 80,
                sortable: true,
                renderer: function (value, p, r) {
                    return value.charAt(0).toUpperCase() + value.slice(1);
                }
            },
            {
                id: 'fechaCreacion',
                header: 'Fecha Creación',
                dataIndex: 'fechaCreacion',
                width: 100,
                sortable: true
            },
            {
                id: 'fechaEstado',
                header: 'Fecha Gestión',
                dataIndex: 'fechaEstado',
                width: 100,
                sortable: true
            },
            {
                id: 'nombreDepartamento',
                header: 'Departamento',
                dataIndex: 'nombreDepartamento',
                width: 150,
                sortable: true
            }
            ,
            {
                id: 'empleadoAsignado',
                header: 'Empleado Asignado',
                dataIndex: 'empleado',
                width: 250,
                sortable: true
            }
        ],
        width: 1200,
        height: 300,
        listeners: {
            itemdblclick: function (view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            },
            viewready: function (grid) {
                var view = grid.view;

                // record the current cellIndex
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
                        grid.cellIndex = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip', {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    renderTo: Ext.getBody(),
                    listeners: {
                        beforeshow: function updateTipBody(tip) {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                            }
                        }
                    }
                });

            }
        }
    });

    formPanelTareasClientes = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        height: 300,
        width: 1200,
        layout: 'fit',
        fieldDefaults: {
            labelAlign: 'left',            
            msgTarget: 'side'
        },

        items: [{
            xtype: 'fieldset',
            defaultType: 'textfield',
            items: [
                gridTareasCliente
            ]
        }]
    });

    winTareasClientes = Ext.create('Ext.window.Window', {
        title: 'Tareas Cliente : '+'<b>'+login+'</b>',
        modal: true,
        width: 1250,
        height: 400,
        resizable: true,
        layout: 'fit',
        items: [formPanelTareasClientes],
        buttonAlign: 'center',
        buttons: [btncancelar]
    }).show();
} 

/************************************************************************ */
/********************** VALIDAR EXCEDENTE DE MATERIAL ****************************** */
/************************************************************************ */
/**
 * Validar excedente de material 
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 10-03-2023
 * @since 1.0
 */
function getValidadorExcedenteMaterial(rec)
{
    var intIdServicio                   = rec.data.id_servicio;
    var strModulo                       = rec.raw.strModulo;
    var strValorMetraje                 = rec.data.strMetraje;
    var strSolExcedenteMaterial         = rec.raw.solExcedenteMaterial;
    var intMetrosDeDistancia            = rec.raw.metrosDeDistancia ;
    var intPrecioFibra                  = rec.raw.precioFibra;
    var floatValorCaractOCivil          = rec.raw.floatValorCaractOCivil;
    var floatValorCaractOtrosMateriales = rec.raw.floatValorCaractOtrosMateriales;
    var floatValorCaractCancPorCli      = rec.raw.floatValorCaractCancPorCli;
    var floatValorCaractAsumeCli        = rec.raw.floatValorCaractAsumeCli;
    var floatValorCaractAsumeEmpresa    = rec.raw.floatValorCaractAsumeEmpresa;
    var floatSubtotalOtrosClientes      = parseFloat(floatValorCaractOCivil) + parseFloat(floatValorCaractOtrosMateriales);

    var strBotonModulo                = '';
    var winValidadorExcedente         = "";
    var resultado1                    = 0;
    var PrecioObraCivil               = 0;
    var PrecioOtrosMate               = 0;
    var suma                          = 0;

    if (!strValorMetraje)
    {
    Ext.Msg.alert('Alerta', 'Por favor verificar el ingreso correcto de datos, no existe metraje');
    }

    if(strModulo=='PLANIFICACION')
    {
    strBotonModulo = 'Enviar a comercial';
    }
    else if(strModulo=='COMERCIAL')
    {
    strBotonModulo = 'Validar';
    }

    var formPanelCreacionTarea = Ext.create('Ext.form.Panel', {
    buttonAlign: 'center',
    BodyPadding: 10,
    bodyStyle: "background: white; padding: 15px; border: 0px none;",
    height: 600,
    width: 350,
    frame: true,
    items: [

        //Resumen del cliente (muestra el numero de la solicitud y el estado)
        { width: '10%', border: false },
        {
            xtype: 'label',
            forId: 'lbl_InfoSolExcedente',
            style: "font-weight:bold; color:blue;",
            text: strSolExcedenteMaterial + '\n ',
            margin: '0 0 30 0'
        },
        //-------------------PROYECTOS/CLIENTES EXCEPCIÒN-------------  
        { width: '10%', border: false },
        {
            xtype: 'panel',
            border: false,
            frame: true,
            layout: { type: 'hbox', align: 'stretch' },
            hidden : true,
            items: [
                {
                    xtype: 'label',
                    forId: 'lbl_clientes_excepcion',
                    text: 'PROYECTOS/CLIENTES EXCEPCIÒN :',
                    margin: '0 0 0 15'
                }
            ]
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Modulo:',
            name: 'txt_Modulo',
            id: 'txt_Modulo',
            value: strModulo,
            allowBlank: false,
            readOnly: true,
            style: "width:75%",
            hidden:true
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Valor Predeterminado (metros):',
            name: 'txt_ValorPredeterminado',
            id: 'txt_ValorPredeterminado',
            value: '0',
            allowBlank: false,
            readOnly: true,
            style: "width:75%",
            hidden : true
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Metraje Inspecciòn FO:',
            name: 'txt_MetrajeInpeccion',
            id: 'txt_MetrajeInpeccion',
            value: '0',
            allowBlank: false,
            readOnly: false,
            maskRe: /[0-9.]/,
            style: "width:75%",
            hidden : true
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Diferencia de FO (metros):',
            name: 'txt_DiferenciaDeFibra',
            id: 'txt_DiferenciaDeFibra',
            value: '0',
            allowBlank: false,
            readOnly: false,
            maskRe: /[0-9.]/,
            style: "width:75%",
            hidden : true
        },
        {
            xtype: 'textfield',
            fieldLabel: '<b> Sub Total: </b>',
            name: 'txt_SubTotalProyectos',
            id: 'txt_SubTotalProyectos',
            value: '0',
            allowBlank: false,
            readOnly: true,
            style: "width:75%",
            hidden : true
        },
        // -----------OTROS CLIENTES-------------    
        { width: '10%', border: false },
        {
            xtype: 'panel',
            border: false,
            frame: true,
            layout: { type: 'hbox', align: 'stretch' },
            items: [
                {
                    xtype: 'label',
                    forId: 'lbl_OtrosClientes',
                    text: 'OTROS CLIENTES :',
                    margin: '0 0 0 15'
                }
            ]
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Fibra (metros)',
            name: 'txt_FibraMetros',
            id: 'txt_FibraMetros',
            value: parseFloat(strValorMetraje),
            allowBlank: false,
            readOnly: false,
            maskRe: /[0-9.]/,
            style: "width:75%",
            listeners: {
                change: {
                    element: 'el',
                    fn: function () {
                        Ext.getCmp('txt_PrecioFibra').setValue
                        (
                            //le da el valor a al precio de fibra
                            parseFloat(resultado1 = Ext.getCmp("txt_FibraMetros").getValue() > parseFloat(intMetrosDeDistancia) ?
                                (Ext.getCmp("txt_FibraMetros").getValue() - parseFloat(intMetrosDeDistancia)) * parseFloat(intPrecioFibra) : 0)
                        );
                        
                        parseFloat(PrecioObraCivil = Ext.getCmp('txt_PrecioObraCivil').value);
                        parseFloat(PrecioOtrosMate = Ext.getCmp('txt_PrecioOtrosMate').value);

                        parseFloat(suma = parseFloat(resultado1) + parseFloat(PrecioObraCivil) + parseFloat(PrecioOtrosMate));
                        parseFloat(Ext.getCmp('txt_SubTotalOtrosClientes').setValue(suma));
                        parseFloat(Ext.getCmp('txt_Total').setValue(suma))
                        parseFloat(Ext.getCmp('txt_CanceladoPorCliente').setValue(0))
                        parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(0))
                        parseFloat(Ext.getCmp('txt_AsumeEmpresa').setValue(0))
                    }
                }
            }
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Precio Fibra',
            name: 'txt_PrecioFibra',
            id: 'txt_PrecioFibra',
            value: (parseFloat(strValorMetraje) > parseFloat(intMetrosDeDistancia)?
                    (parseFloat(strValorMetraje) - parseFloat(intMetrosDeDistancia)) * parseFloat(intPrecioFibra) : 0),
            allowBlank: false,
            readOnly: true,
            style: "width:75%"
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Precio Obra Civil',
            name: 'txt_PrecioObraCivil',
            id: 'txt_PrecioObraCivil',
            value: parseFloat(floatValorCaractOCivil),
            allowBlank: false,
            readOnly: false,
            maskRe: /[0-9.]/,
            style: "width:75%",
            listeners: {
                change: {
                    element: 'el',
                    fn: function () {
                        parseFloat(resultado1 = Ext.getCmp("txt_PrecioFibra").value);
                        parseFloat(PrecioObraCivil = Ext.getCmp('txt_PrecioObraCivil').value);
                        parseFloat(PrecioOtrosMate = Ext.getCmp('txt_PrecioOtrosMate').value);

                        parseFloat(suma = parseFloat(resultado1) + parseFloat(PrecioObraCivil) + parseFloat(PrecioOtrosMate))
                        parseFloat(Ext.getCmp('txt_SubTotalOtrosClientes').setValue(suma))
                        parseFloat(Ext.getCmp('txt_Total').setValue(suma))

                        //Resetear los copago
                        parseFloat(Ext.getCmp('txt_CanceladoPorCliente').setValue(0))
                        parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(0))
                        parseFloat(Ext.getCmp('txt_AsumeEmpresa').setValue(0))
                    }
                }
            }
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Precio Otros Materiales',
            name: 'txt_PrecioOtrosMate',
            id: 'txt_PrecioOtrosMate',
            value: parseFloat(floatValorCaractOtrosMateriales),
            allowBlank: false,
            readOnly: false,
            maskRe: /[0-9.]/,
            style: "width:75%",
            listeners: {
                change: {
                    element: 'el',
                    fn: function () {
                        parseFloat(resultado1 = Ext.getCmp("txt_PrecioFibra").value);
                        parseFloat(PrecioObraCivil = Ext.getCmp('txt_PrecioObraCivil').value);
                        parseFloat(PrecioOtrosMate = Ext.getCmp('txt_PrecioOtrosMate').value);

                        parseFloat(suma = parseFloat(resultado1) + parseFloat(PrecioObraCivil) + parseFloat(PrecioOtrosMate))
                        parseFloat(Ext.getCmp('txt_SubTotalOtrosClientes').setValue(suma))
                        parseFloat(Ext.getCmp('txt_Total').setValue(suma))

                        //Resetear los copago
                        parseFloat(Ext.getCmp('txt_CanceladoPorCliente').setValue(0))
                        parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(0))
                        parseFloat(Ext.getCmp('txt_AsumeEmpresa').setValue(0))
                    }
                }
            }
        },
        {
            xtype: 'numberfield',
            fieldLabel: '<b> Sub Total: </b>',
            name: 'txt_SubTotalOtrosClientes',
            id: 'txt_SubTotalOtrosClientes',
            allowBlank: false,
            value: (parseFloat(strValorMetraje) > parseFloat(intMetrosDeDistancia) ?
                            ( (parseFloat(strValorMetraje) - parseFloat(intMetrosDeDistancia) ) * parseFloat(intPrecioFibra) 
                                    + parseFloat(floatSubtotalOtrosClientes)   )
                                : floatSubtotalOtrosClientes),
            readOnly: true,
            style: "width:75%",
            maskRe: /[0-9.]/
        },
        // -----------COPAGOS-------------    
        { width: '10%', border: false },
        {
            xtype: 'panel',
            border: false,
            frame: true,
            layout: { type: 'hbox', align: 'stretch' },
            items: [
                {
                    xtype: 'label',
                    forId: 'lbl_copagos',
                    text: 'COPAGOS :',
                    margin: '0 0 0 15'
                }
            ]
        },
        {
            xtype: 'numberfield',
            fieldLabel: '% Cancelado por el cliente:',
            name: 'txt_CanceladoPorCliente',
            id: 'txt_CanceladoPorCliente',
            value: floatValorCaractCancPorCli,
            allowBlank: false,
            readOnly : strModulo == "PLANIFICACION" ? true : false,
            style: "width:75%",
            maskRe: /[0-9.]/,
            listeners: {
                change: {
                    element: 'el',
                    fn: function () {

                        parseFloat(SubTotalOtrosClientes = Ext.getCmp("txt_SubTotalOtrosClientes").value);
                        parseFloat(PorcentajeCanceladoPorCliente = Ext.getCmp('txt_CanceladoPorCliente').value);
                        parseFloat(AsumeCliente = Ext.getCmp('txt_AsumeCliente').value);
                        parseFloat(CalculoAsumeCliente = ((SubTotalOtrosClientes * PorcentajeCanceladoPorCliente) / 100));
                        parseFloat(Ext.getCmp('txt_AsumeCliente').setValue(CalculoAsumeCliente));
                        parseFloat(CalculoAsumeEmpresa = SubTotalOtrosClientes - CalculoAsumeCliente);
                        parseFloat(Ext.getCmp('txt_Total').setValue(CalculoAsumeEmpresa));
                        parseFloat((Ext.getCmp('txt_AsumeEmpresa').setValue(CalculoAsumeEmpresa)) )
                    }
                }
            }
        },
        {
            xtype: 'numberfield',
            fieldLabel: 'Asume el cliente:',
            name: 'txt_AsumeCliente',
            id: 'txt_AsumeCliente',
            value: parseFloat(floatValorCaractAsumeCli),
            allowBlank: false,
            readOnly : strModulo == "PLANIFICACION" ? true : false,
            style: "width:75%",
            maskRe: /[0-9.]/
        },
        {
            xtype: 'numberfield',
            fieldLabel: 'Asume la empresa:',
            name: 'txt_AsumeEmpresa',
            id: 'txt_AsumeEmpresa',
            value: parseFloat(floatValorCaractAsumeEmpresa),
            allowBlank: false,
            readOnly : strModulo == "PLANIFICACION" ? true : false,
            style: "width:75%",
            maskRe: /[0-9.]/
        },
        {
            xtype: 'numberfield',
            fieldLabel: '<b>Total:</b>',
            name: 'txt_Total',
            id: 'txt_Total',
            value: (floatValorCaractCancPorCli >0 ? floatValorCaractAsumeEmpresa:
                parseFloat(strValorMetraje) > parseFloat(intMetrosDeDistancia) ?
                            ( (parseFloat(strValorMetraje) - parseFloat(intMetrosDeDistancia) ) * parseFloat(intPrecioFibra) 
                                    + parseFloat(floatSubtotalOtrosClientes)   )
                                : floatSubtotalOtrosClientes),
            style: "width:75%",
            maskRe: /[0-9.]/,
            readOnly : strModulo == "PLANIFICACION" ? true : false
        },        
        // -----------OBSERVACIÒN-------------
        { width: '10%', border: false },
        {
            xtype: 'label',
            forId: 'lbl_observacion',
            text: 'Observación :',
            margin: '0 0 0 15'
        },
        {
            xtype: 'textareafield',
            hideLabel: true,
            name: 'txt_Observacion',
            id: 'txt_Observacion',
            value: " ",
            width: 315,
            heigth: 200,
            readOnly: false
        },
        { width: '10%', border: false },
    ],
    buttons: [
        {
            text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;' + strBotonModulo,
            handler: function () {
                $.ajax({
                    url: strUrlValidadorMaterial,
                    type: "POST",
                    timeout: 600000,
                    data:
                    {
                        intIdServicio: intIdServicio,
                        precioMetroFibra: rec.raw.precioFibra,
                        //-------------------PROYECTOS/CLIENTES EXCEPCIÒN------------- 
                        valorPredeterminado: Ext.getCmp("txt_ValorPredeterminado").getValue() == '' ? 0 : Ext.getCmp("txt_ValorPredeterminado").getValue(),
                        metrajeInpeccion: Ext.getCmp("txt_MetrajeInpeccion").getValue() == '' ? 0 : Ext.getCmp("txt_MetrajeInpeccion").getValue(),
                        diferenciaDeFibra: Ext.getCmp("txt_DiferenciaDeFibra").getValue() == '' ? 0 : Ext.getCmp("txt_DiferenciaDeFibra").getValue(),
                        subTotalProyectos: Ext.getCmp("txt_SubTotalProyectos").getValue() == '' ? 0 : Ext.getCmp("txt_SubTotalProyectos").getValue(),
                        // -----------OTROS CLIENTES-------------
                        metrosFibra: Ext.getCmp("txt_FibraMetros").getValue() == '' ? 0 : Ext.getCmp("txt_FibraMetros").getValue(),
                        precioFibra: Ext.getCmp("txt_PrecioFibra").getValue(),
                        precioObraCivil: Ext.getCmp("txt_PrecioObraCivil").getValue() == '' ? 0 : Ext.getCmp("txt_PrecioObraCivil").getValue(),
                        precioOtrosMate: Ext.getCmp("txt_PrecioOtrosMate").getValue() == '' ? 0 : Ext.getCmp("txt_PrecioOtrosMate").getValue(),
                        subTotalOtrosClientes: Ext.getCmp("txt_SubTotalOtrosClientes").getValue() == '' ? 0 : Ext.getCmp("txt_SubTotalOtrosClientes").getValue(),
                            // -----------COPAGOS-------------    
                        canceladoPorCliente: Ext.getCmp("txt_CanceladoPorCliente").getValue() == '' ? 0 : Ext.getCmp("txt_CanceladoPorCliente").getValue(),
                        asumeCliente: Ext.getCmp("txt_AsumeCliente").getValue() == '' ? 0 : Ext.getCmp("txt_AsumeCliente").getValue(),
                        asumeEmpresa: Ext.getCmp("txt_AsumeEmpresa").getValue() == '' ? 0 : Ext.getCmp("txt_AsumeEmpresa").getValue(),
                        observacion:  Ext.getCmp("txt_Observacion").getValue()  == '' ? 0 : Ext.getCmp("txt_Observacion").getValue(),

                        //------ COMPROBAR DE QUE MODULO ENVÍA EL FORMULARIO
                        modulo:       Ext.getCmp("txt_Modulo").getValue()  == '' ? 0 : Ext.getCmp("txt_Modulo").getValue(),
                        
                        detalleSolId: rec.data.id_factibilidad,
                        totalPagar: Ext.getCmp("txt_Total").getValue(),
                    },
                    beforeSend: function () {
                        Ext.get(winValidadorExcedente.getId()).mask('Enviando datos...');
                    },
                    complete: function () {
                        Ext.get(winValidadorExcedente.getId()).unmask();
                    },
                    success: function (data) {
                        Ext.Msg.alert('Mensaje', data.mensaje, function (btn) {
                            if (btn == 'ok') {
                                winValidadorExcedente.close();
                                store.load();
                            }
                        });

                    },
                    failure: function (result) {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        },
        {
            text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
            handler: function () {
                winValidadorExcedente.close();
                winValidadorExcedente.destroy();
            }
        },
    ]
    });

    winValidadorExcedente = Ext.widget('window', {
        title: 'Validador de Excedente de Material '+ strModulo,
        layout: 'fit',
        resizable: true,
        modal: true,
        closable: false,
        items: [formPanelCreacionTarea]
    });
    winValidadorExcedente.show();

}

/**
 * Ver documento excedente de material 
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 10-03-2023
 * @since 1.0
 */
function getDocumento(rec)
{
    var id_servicio = rec.raw.id_servicio;
    var cantidadDocumentos = 1;
    var connDocumentos = new Ext.data.Connection
    ({
        listeners: 
        {
            'beforerequest': 
            {
                fn: function (con, opt) 
                { 
                    Ext.MessageBox.show
                    ( {
                        msg: 'Consultando documentos, Por favor espere!!',
                        progressText: 'Consultando...',
                        width: 300,
                        wait: true,
                        waitConfig: {interval: 200}
                        }
                    );
                },
                scope: this
            },
            'requestcomplete':
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            },
            'requestexception': 
            {
                fn: function (con, res, opt)
                {
                    Ext.MessageBox.hide();
                },
                scope: this
            }
        }
    });

    connDocumentos.request
    ( {
        url: url_verifica_documentos,
        method: 'post',
        params:{ idServicio: id_servicio },
        success: function (response)
        {
            var text           = Ext.decode(response.responseText);
            cantidadDocumentos = text.total;

            if (cantidadDocumentos > 0)
            {
                var storeDocumentos = new Ext.data.Store
                (
                    {
                    pageSize: 1000,
                    autoLoad: true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: url_verDocumentos,
                        reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        extraParams:
                        {
                            idServicio: id_servicio
                        }
                    },
                    fields:
                    [
                        {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                        {name: 'feCreacion', mapping: 'feCreacion'},
                        {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                        {name: 'idDocumento', mapping: 'idDocumento'}
                    ]
                }
                );

                Ext.define('Documentos', 
                {
                    extend: 'Ext.data.Model',
                    fields:
                    [
                        {name: 'ubicacionLogica', mapping: 'ubicacionLogica'},
                        {name: 'feCreacion', mapping: 'feCreacion'},
                        {name: 'linkVerDocumento', mapping: 'linkVerDocumento'},
                        {name: 'idDocumento', mapping: 'idDocumento'}
                    ]
                });

                //grid de documentos
                gridDocumentos = Ext.create('Ext.grid.Panel',
                {
                    id: 'gridMaterialesPunto',
                    store: storeDocumentos,
                    columnLines: true,
                    columns: 
                    [
                        {
                            header: 'Nombre Archivo',
                            dataIndex: 'ubicacionLogica',
                            width: 260
                        },
                        {
                            header: 'Fecha de Carga',
                            dataIndex: 'feCreacion',
                            width: 120
                        },
                        {
                            xtype: 'actioncolumn',
                            header: 'Acciones',
                            width: 100,
                            items:
                            [
                                {
                                    iconCls: 'button-grid-show',
                                    tooltip: 'Ver Archivo Digital',
                                    handler: function (grid, rowIndex, colIndex) 
                                    {
                                        var rec = storeDocumentos.getAt(rowIndex);
                                        verArchivoDigital(rec);
                                    }
                                }
                            ]
                        }
                    ],
                    viewConfig:
                    {
                        stripeRows: true,
                        enableTextSelection: true
                    },
                    frame: true,
                    height: 200
                }
                );

                function verArchivoDigital(rec)
                {
                    var idDocumento = rec.get('idDocumento');
                    window.location = url_descargaDocumentos + '?idDocumento=' + idDocumento;
                }

                var formPanel = Ext.create('Ext.form.Panel',
                {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: 
                    {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items:
                    [
                        {
                            xtype: 'fieldset',
                            title: '',
                            defaultType: 'textfield',
                            defaults: 
                            {
                                width: 510
                            },
                            items: 
                            [
                                gridDocumentos
                            ]
                        }
                    ],
                    buttons: 
                    [{
                        text: 'Cerrar',
                        handler: function ()
                        {
                            win.destroy();
                        }
                    }]
                });

                var win = Ext.create('Ext.window.Window',
                {
                    title: 'Documentos Cargados',
                    modal: true,
                    width: 550,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
                
            } 
            else
            {
                Ext.Msg.show
                ({
                    title: 'Mensaje',
                    msg: 'El servicio seleccionado no posee archivos adjuntos.',
                    buttons: Ext.Msg.OK,
                    animEl: 'elId',
                });
            }

        },
        failure: function (result)
        {
            Ext.Msg.show
            ( {
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            }
            );
        }
    });
}