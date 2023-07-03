
var storeInterfacesCpe ='';

/**
 * Funcion que sirve para cargar la pantalla y llamada ajax para activacion de
 * un servicio mpls para la empresa TN
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     3-12-2015
 * 
 * @author      Jesus Bozada <jbozada@telconet.ec>
 * @version     1.1     23-05-2016       Se agrega validaciones para soportar asignacion de servicios UM Radio
 * 
 * @author      Jesus Bozada <jbozada@telconet.ec>
 * @version     1.2     22-01-2018       Se agrega programación para poder reutilizar equipos en activación de traslados TN
 * @since       1.1
 * 
 * @param Array data        Informacion que fue cargada en el grid
 * @param       gridIndex
 * */
function activarServicioINTMPLS(data, gridIndex)
{
    var tituloPanel = "Activar Servicio " + data.strTipoRed;
    var estadoInterfaceCpeExistente;    
    var strEsRadioExistente       = "NO";
    var strEsCpeExistente         = "NO";
    var strEsTransceiverExistente = "NO";
    var seActiva = true;
    var booleanRedGpon = false;
    if(data.booleanTipoRedGpon)
    {
        booleanRedGpon = true;
    }
    if(data.capacidadUno === null || data.capacidadDos === null)
    {
        Ext.Msg.alert('Error ','Error: No Existen los valores de bandwidth, Favor Notificar a Sistemas!');
    }
    else
    {
        var tituloElementoConector = 'Nombre Cassette';
        if (data.ultimaMilla == "Radio" )
        {
            tituloElementoConector = "Nombre Radio";
        }
        if(booleanRedGpon)
        {
            tituloElementoConector = "Splitter Elemento";
        }
        Ext.get(gridServicios.getId()).mask('Consultando Datos...');
            Ext.Ajax.request({ 
                url: getDatosBackbone,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    tipoElementoPadre: 'ROUTER'
                },
                success: function(response){
                    Ext.get(gridServicios.getId()).unmask();

                    var json = Ext.JSON.decode(response.responseText);
                    var datos = json.encontrados;
                    //-------------------------------------------------------------------------------------------

                    if(datos[0].idElementoPadre == 0)
                    {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: datos[0].nombreElementoPadre,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                    else
                    {
                        var storeModelosRadio = new Ext.data.Store({
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url : getModelosElemento,
                                extraParams: {
                                    tipo:   'RADIO',
                                    forma:  'Igual que',
                                    estado: "Activo"
                                },
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                                [
                                  {name:'modelo', mapping:'modelo'},
                                  {name:'codigo', mapping:'codigo'}
                                ]
                        });

                        var storeModelosCpe = new Ext.data.Store({
                            pageSize: 1000,
                            proxy: {
                                type: 'ajax',
                                url : getModelosElemento,
                                extraParams: {
                                    tipo:   'CPE',
                                    forma:  'Empieza con',
                                    estado: "Activo"
                                },
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                                [
                                  {name:'modelo', mapping:'modelo'},
                                  {name:'codigo', mapping:'codigo'}
                                ]
                        });

                        var storeModelosTransciever = new Ext.data.Store({
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url : getModelosElemento,
                                extraParams: {
                                    tipo:   'TRANSCEIVER',
                                    forma:  'Empieza con',
                                    estado: "Activo"
                                },
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                                [
                                  {name:'modelo', mapping:'modelo'},
                                  {name:'codigo', mapping:'codigo'}
                                ]
                        });

                        var storeElementosClientesPorPunto = new Ext.data.Store({
                            pageSize: 100,
                            listeners: {
                                load: function(store,records,options) {

                                    if (datos[0].hasOwnProperty('zeroTouchData'))
                                    {
                                        Ext.getCmp('existePropiedadDe')
                                        Ext.getCmp('rbCpeExiste').setDisabled(true);

                                        Ext.getCmp('propiedadNuevoCpe').select(datos[0].zeroTouchData.ELEMENTOS_INSTALACION.cpeCliente.propiedad);
                                        // Ext.getCmp('propiedadNuevoCpe').setValue(datos[0].zeroTouchData.ELEMENTOS_INSTALACION.cpeCliente.propiedad);
                                        Ext.getCmp('propiedadNuevoCpe').setDisabled(true);

                                        if (datos[0].zeroTouchData.FLUJO_ZEROTOUCH == "F") 
                                        {
                                            if (datos[0].zeroTouchData.ELEMENTOS_INSTALACION.cpeCliente.propiedad == "CLIENTE")
                                            {
                                                Ext.getCmp('descripcionNuevoCpe').setReadOnly(false);
                                                Ext.getCmp('modeloNuevoCpe').setReadOnly(false);
                                                Ext.getCmp('descripcionNuevoCpe').setValue("dispositivo cliente");
                                            }

                                            Ext.getCmp('serviceInfoPanel').add([
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Flujo Zero Touch',
                                                    name: 'flujoZeroTouch',
                                                    id: 'flujoZeroTouch',
                                                    value: '« SI »',
                                                    width: '35%',
                                                    readOnly: true
                                                },
                                                { width: '15%', border: false},
                                                { width: '15%', border: false},
                                                { width: '10%', border: false}
                                            ]);
                                        }

                                    }

                                    if(store.totalCount===0)
                                    {
                                        Ext.getCmp('rbCpeExiste').setDisabled(true);
                                        Ext.getCmp('rbCpeNuevo').checked = true;
                                    }
                                    else
                                    {
                                        //UM EXISTENTE
                                        if(data.usaUltimaMillaExistente === 'SI' && data.esSdwan == 'N')
                                        {
                                            Ext.getCmp('rbCpeNuevo').setDisabled(true);
                                            Ext.getCmp('rbCpeExiste').checked = true;
                                        }
                                    }
                                    if(data.tipoOrden == 'C')
                                    {
                                        Ext.getCmp('rbCpeNuevo').setDisabled(true);
                                        Ext.getCmp('elementoCliente').setValue(records[0].raw.nombreElemento);
                                        CargarInformacionCPE(data, records[0].raw);
                                    }
                                }
                            },
                            proxy: {
                                type: 'ajax',
                                url : ajaxGetElementosClientesPorPunto,
                                extraParams: {
                                    idServicio: data.idServicio,
                                    estado:     "Activo"
                                },
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                                [
                                  {name:'idElemento',       mapping:'idElemento'},
                                  {name:'nombreElemento',   mapping:'nombreElemento'}
                                ]
                        });

                        storeInterfacesCpe = new Ext.data.Store({
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url : getInterfacesCpe,
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                                [
                                  {name:'idInterfaceElemento',       mapping:'idInterfaceElemento'},
                                  {name:'nombreInterfaceElemento',   mapping:'nombreInterfaceElemento'},
                                  {name:'estado',   mapping:'estado'},
                                  {name:'servicioId',   mapping:'servicioId'},
                                  {name:'mac',   mapping:'mac'}
                                ]
                        });

                        //-------------------------------------------------------------------------------------------

                        var formPanel = Ext.create('Ext.form.Panel', {
                            bodyPadding: 2,
                            waitMsgTarget: true,
                            fieldDefaults: {
                                labelAlign: 'left',
                                labelWidth: 85,
                                msgTarget: 'side',
                                bodyStyle: 'padding:20px'
                            },
                            layout: {
                                type: 'table',
                                // The total column count must be specified here
                                columns: 3
                            },
                            defaults: {
                                // applied to each contained panel
                                bodyStyle: 'padding:20px'
                            },
                            items: [

                                //Información de backbone
                                {
                                    colspan :  2,
                                    rowspan :  2,
                                    xtype   : 'panel',
                                    title   : 'Información de backbone',
                                    defaults: {
                                        height: 130
                                    },
                                    items:
                                    [
                                       {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 5,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {   width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'Elemento',
                                                    fieldLabel: 'Elemento',
                                                    displayField: data.elementoNombre,
                                                    value: data.elementoNombre,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'ipElemento',
                                                    fieldLabel: 'Ip Elemento',
                                                    displayField: data.ipElemento,
                                                    value: data.ipElemento,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'interfaceElemento',
                                                    fieldLabel: 'Puerto Elemento',
                                                    displayField: data.interfaceElementoNombre,
                                                    value: data.interfaceElementoNombre,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'modeloELemento',
                                                    fieldLabel: 'Modelo Elemento',
                                                    displayField: data.modeloElemento,
                                                    value: data.modeloElemento,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vlan',
                                                    fieldLabel: 'Vlan',
                                                    displayField: data.vlan,
                                                    value: data.vlan,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vrf',
                                                    fieldLabel: 'Vrf',
                                                    displayField: data.vrf,
                                                    value: data.vrf,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'pe',
                                                    name: 'pe',
                                                    fieldLabel: 'PE',
                                                    displayField: datos[0].nombreElementoPadre,
                                                    value: datos[0].nombreElementoPadre,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'anillo',
                                                    name: 'anillo',
                                                    fieldLabel: 'Anillo',
                                                    displayField: datos[0].anillo,
                                                    value: datos[0].anillo,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'umExistente',
                                                    name: 'umExistente',
                                                    fieldLabel: 'Utiliza UM Existente',
                                                    displayField: data.usaUltimaMillaExistente,
                                                    value: data.usaUltimaMillaExistente,
                                                    readOnly: true,
                                                    width: '30%'
                                                }
                                            ]
                                        }
                                    ]
                                },//cierre de info de backbone

                                //Información del servicio/producto
                                {
                                    colspan :  2,
                                    rowspan :  2,
                                    xtype   : 'panel',
                                    title   : 'Información del Servicio',
                                    defaults: {
                                        height: 130
                                    },
                                    items:
                                    [
                                        {
                                            xtype: 'container',
                                            id: 'serviceInfoPanel',
                                            layout: {
                                                type: 'table',
                                                columns: 5,
                                                align: 'stretch'
                                            },
                                            items: [

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'producto',
                                                    fieldLabel: 'Producto',
                                                    displayField: data.nombreProducto,
                                                    value: data.nombreProducto,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'login',
                                                    fieldLabel: 'Login',
                                                    displayField: data.login,
                                                    value: data.login,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadUno',
                                                    fieldLabel: 'Capacidad Uno',
                                                    displayField: data.capacidadUno,
                                                    value: data.capacidadUno,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadDos',
                                                    fieldLabel: 'Capacidad Dos',
                                                    displayField: data.capacidadDos,
                                                    value: data.capacidadDos,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'ultimaMilla',
                                                    name: 'ultimaMilla',
                                                    fieldLabel: 'Ultima Milla',
                                                    displayField: data.ultimaMilla,
                                                    value: data.ultimaMilla,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'elementoContenedor',
                                                    name: 'elementoContenedor',
                                                    fieldLabel: 'Caja',
                                                    displayField: datos[0].nombreCaja,
                                                    value: datos[0].nombreCaja,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'nombreElementoConector',
                                                    name: 'nombreElementoConector',
                                                    fieldLabel: tituloElementoConector,
                                                    displayField: datos[0].nombreSplitter,
                                                    value: datos[0].nombreSplitter,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'hilo',
                                                    name: 'hilo',
                                                    fieldLabel: 'Color Hilo',
                                                    displayField: datos[0].colorHilo,
                                                    value: datos[0].colorHilo,
                                                    hidden: booleanRedGpon,
                                                    width: '35%'
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    id:'splitterInterfaceElemento',
                                                    name: 'splitterInterfaceElemento',
                                                    fieldLabel: "Splitter Interface",
                                                    displayField: datos[0].nombrePuertoSplitter,
                                                    value: datos[0].nombrePuertoSplitter,
                                                    readOnly: true,
                                                    hidden: !booleanRedGpon,
                                                    width: '35%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id:'tipoEnlace',
                                                    name: 'tipoEnlace',
                                                    fieldLabel: 'Tipo Enlace',
                                                    displayField: data.tipoEnlace,
                                                    value: data.tipoEnlace,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'tipoRed',
                                                    name: 'tipoRed',
                                                    fieldLabel: 'Tipo de Red',
                                                    displayField: data.strTipoRed,
                                                    value: data.strTipoRed,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                { width: '10%', border: false}
                                            ]
                                        }
                                    ]
                                },//cierre de la informacion servicio/producto

                                //Información de los elementos del cliente
                                {
                                    xtype  : 'panel',
                                    title  : 'Información de los Elementos del Cliente',
                                    colspan:  3,
                                    items:
                                    [
                                        //grupo de radio botones
                                        {
                                            xtype     : 'radiogroup',
                                            fieldLabel: 'Cpe',
                                            labelWidth:  120,
                                            columns   :  1,
                                            width     : '50%',
                                            items:
                                            [
                                                {
                                                    boxLabel  : 'Nuevo Cpe',
                                                    id        : 'rbCpeNuevo',
                                                    name      : 'rbCpe',
                                                    inputValue: 'nuevo',
                                                    disabled: data.booleanEsSDWAN && data.booleanEsMigracionSDWAN,
                                                    listeners :
                                                    {
                                                        change: function (cb, nv, ov)
                                                        {
                                                            if (datos[0].hasOwnProperty('zeroTouchData'))
                                                            {
                                                                let objCpe = datos[0]['zeroTouchData']
                                                                    ['ELEMENTOS_INSTALACION']['cpeCliente'];
                                                                let objTransceiver = datos[0]['zeroTouchData']
                                                                    ['ELEMENTOS_INSTALACION']['transceiverCliente'];

                                                                Ext.getCmp('serieNuevoCpe').setValue(objCpe['serie']);
                                                                Ext.getCmp('serieNuevoCpe').setReadOnly(true);
                                                                Ext.getCmp('serieNuevoCpe').setRawValue(objCpe['serie']);
                                                                Ext.getCmp('serieNuevoTransciever').setValue(objTransceiver['serie']);
                                                                Ext.getCmp('serieNuevoTransciever').setReadOnly(true);
                                                                Ext.getCmp('serieNuevoTransciever').setRawValue(objTransceiver['serie']);
                                                                
                                                            }
                                                            if (nv)
                                                            {
                                                                if(booleanRedGpon)
                                                                {
                                                                    Ext.getCmp('nuevoCpeRoseta').setVisible(false);
                                                                    Ext.getCmp('nuevoCpeTransciever').setVisible(false);
                                                                }
                                                                else
                                                                {
                                                                    Ext.getCmp('nuevoCpeRoseta').setVisible(true);
                                                                    Ext.getCmp('nuevoCpeTransciever').setVisible(true);
                                                                }

                                                                Ext.getCmp('comboFilterTecnico').setVisible(true);
                                                                Ext.getCmp('selecDispositivoCpe').setVisible(true);
                                                                Ext.getCmp('selectDispositivoRadio').setVisible(true);
                                                                Ext.getCmp('nuevoDispositivos').setVisible(true);
                                                                Ext.getCmp('nuevoDispositivosNodo').setVisible(true);

                                                                Ext.getCmp('nuevoCpe').setVisible(true);
                                                                Ext.getCmp('existeCpe').setVisible(false);
                                                                Ext.getCmp('existeDatosCpe').setVisible(false);

                                                                var nombreRadio = "radio-" + data.login + ".telconet.net";
                                                                Ext.getCmp('nombreNuevoRadio').setValue = nombreRadio;
                                                                Ext.getCmp('nombreNuevoRadio').setRawValue(nombreRadio);

                                                                if(datos[0].hasOwnProperty('zeroTouchData'))
                                                                {
                                                                    Ext.getCmp('serieNuevoCpe').focus();
                                                                    Ext.getCmp('serieNuevoCpe').blur();

                                                                    Ext.getCmp('serieNuevoTransciever').focus();
                                                                    Ext.getCmp('serieNuevoTransciever').blur();
                                                                }

                                                                //Deshabilitamos la selección del dispositivo Cpe
                                                                if (Ext.isEmpty(data.strPropietarioCpeCliente) ||
                                                                    data.strPropietarioCpeCliente === 'CLIENTE') {
                                                                    Ext.getCmp('selecDispositivoCpe').setVisible(false);
                                                                }

                                                                //Habilitamos la selección del dispositivo Cpe
                                                                if (Ext.getCmp('propiedadNuevoCpe').getValue() === 'TELCONET') {
                                                                    Ext.getCmp('selecDispositivoCpe').setVisible(true);
                                                                }

                                                                //Habilitamos los textfield del cpe, cuando el propietario es el cliente
                                                                if (data.strPropietarioCpeCliente === 'CLIENTE') {
                                                                    Ext.getCmp('descripcionNuevoCpe').setReadOnly(false);
                                                                    Ext.getCmp('serieNuevoCpe').setReadOnly(false);
                                                                    Ext.getCmp('modeloNuevoCpe').setReadOnly(false);
                                                                    Ext.getCmp('macNuevoCpe').setReadOnly(false);
                                                                } else {
                                                                    Ext.getCmp('descripcionNuevoCpe').setReadOnly(true);
                                                                    Ext.getCmp('serieNuevoCpe').setReadOnly(true);
                                                                    Ext.getCmp('modeloNuevoCpe').setReadOnly(true);
                                                                    Ext.getCmp('macNuevoCpe').setReadOnly(false);
                                                                }

                                                                //Deshabilitamos la selección del dispositivo Radio
                                                                if (Ext.isEmpty(data.strPropietarioRadioCliente) ||
                                                                    data.strPropietarioRadioCliente === 'CLIENTE') {
                                                                    Ext.getCmp('selectDispositivoRadio').setVisible(false);
                                                                }

                                                                //Habilitamos la selección del dispositivo Radio
                                                                if (Ext.getCmp('propiedadNuevoRadio').getValue() === 'TELCONET') {
                                                                    Ext.getCmp('selectDispositivoRadio').setVisible(true);
                                                                }

                                                                //Habilitamos los textfield del radio, cuando el propietario es el cliente
                                                                if (data.strPropietarioRadioCliente === 'CLIENTE') {
                                                                    Ext.getCmp('descripcionNuevoRadio').setReadOnly(false);
                                                                    Ext.getCmp('serieNuevoRadio').setReadOnly(false);
                                                                    Ext.getCmp('modeloNuevoRadio').setReadOnly(false);
                                                                    Ext.getCmp('macNuevoRadio').setReadOnly(false);
                                                                } else {
                                                                    Ext.getCmp('descripcionNuevoRadio').setReadOnly(true);
                                                                    Ext.getCmp('serieNuevoRadio').setReadOnly(true);
                                                                    Ext.getCmp('modeloNuevoRadio').setReadOnly(true);
                                                                    Ext.getCmp('macNuevoRadio').setReadOnly(true);
                                                                }

                                                                if(data.strPropietarioCpeCliente != "" && !datos[0].hasOwnProperty('zeroTouchData'))
                                                                {
                                                                    Ext.getCmp('propiedadNuevoCpe').select(data.strPropietarioCpeCliente);
                                                                    if(data.strSerieCpeCliente != "")
                                                                    {
                                                                        Ext.getCmp('serieNuevoCpe').setValue = data.strSerieCpeCliente;
                                                                        Ext.getCmp('serieNuevoCpe').setRawValue(data.strSerieCpeCliente);
                                                                        Ext.getCmp('serieNuevoCpe').focus();
                                                                        Ext.getCmp('serieNuevoCpe').blur();
                                                                    }
                                                                }

                                                                if(data.strSerieTransceiverCliente != "" && !datos[0].hasOwnProperty('zeroTouchData'))
                                                                {
                                                                    Ext.getCmp('serieNuevoTransciever').setValue = data.strSerieTransceiverCliente;
                                                                    Ext.getCmp('serieNuevoTransciever').setRawValue(data.strSerieTransceiverCliente);
                                                                    Ext.getCmp('serieNuevoTransciever').focus();
                                                                    Ext.getCmp('serieNuevoTransciever').blur();
                                                                }

                                                                var nombreCpe = "cpe-" + data.login + ".telconet.net";
                                                                Ext.getCmp('nombreNuevoCpe').setValue = nombreCpe;
                                                                Ext.getCmp('nombreNuevoCpe').setRawValue(nombreCpe);

                                                                var nombreRoseta = "ros-"+data.idServicio;
                                                                Ext.getCmp('codigoNuevaRoseta').setValue = nombreRoseta;
                                                                Ext.getCmp('codigoNuevaRoseta').setRawValue(nombreRoseta);

                                                                if (data.ultimaMilla == "Radio" )
                                                                {
                                                                    Ext.getCmp('nuevoCpeRoseta').setVisible(false);
                                                                    Ext.getCmp('nuevoRadio').setVisible(true);
                                                                    Ext.getCmp('nuevoCpeTransciever').setVisible(false);

                                                                    if(data.strPropietarioRadioCliente != "")
                                                                    {
                                                                        Ext.getCmp('propiedadNuevoRadio').select(data.strPropietarioRadioCliente);
                                                                        if(data.strSerieRadioCliente != "")
                                                                        {
                                                                            Ext.getCmp('serieNuevoRadio').setValue = data.strSerieRadioCliente;
                                                                            Ext.getCmp('serieNuevoRadio').setRawValue(data.strSerieRadioCliente);
                                                                            Ext.getCmp('serieNuevoRadio').focus();
                                                                            Ext.getCmp('serieNuevoRadio').blur();
                                                                        }
                                                                    }
                                                                }

                                                                if (data.ultimaMilla === "UTP" )
                                                                {
                                                                    Ext.getCmp('nuevoCpeRoseta').setVisible(false);
                                                                    Ext.getCmp('nuevoRadio').setVisible(false);
                                                                    Ext.getCmp('nuevoCpeTransciever').setVisible(false);
                                                                }

                                                                if(!Ext.isEmpty(datos[0].esPseudoPe) && datos[0].esPseudoPe === 'S')
                                                                {
                                                                    Ext.getCmp('nuevoCpeRoseta').setVisible(false);
                                                                    Ext.getCmp('nuevoRadio').setVisible(false);
                                                                    Ext.getCmp('nuevoCpeTransciever').setVisible(false);
                                                                }

                                                                if(booleanRedGpon){
                                                                    Ext.getCmp('nuevoONT').setVisible(true);
                                                                }

                                                                win.center();
                                                            }
                                                        }
                                                    }
                                                },
                                                {
                                                    boxLabel  : 'Cpe Existente de Punto',
                                                    id        : 'rbCpeExiste',
                                                    name      : 'rbCpe',
                                                    inputValue: 'existe',
                                                    listeners :
                                                    {
                                                        change: function (cb, nv, ov)
                                                        {
                                                            if (nv)
                                                            {
                                                                Ext.getCmp('comboFilterTecnico').setVisible(true);
                                                                Ext.getCmp('selecDispositivoCpe').setVisible(true);
                                                                Ext.getCmp('selectDispositivoRadio').setVisible(true);
                                                                Ext.getCmp('nuevoCpe').setVisible(false);
                                                                Ext.getCmp('nuevoCpeRoseta').setVisible(false);

                                                                //Deshabilitamos la selección del dispositivo Cpe
                                                                if (Ext.isEmpty(data.strPropietarioCpeCliente) ||
                                                                    data.strPropietarioCpeCliente === 'CLIENTE') {
                                                                    Ext.getCmp('selecDispositivoCpe').setVisible(false);
                                                                }

                                                                //Habilitamos la selección del dispositivo Cpe
                                                                if (Ext.getCmp('propiedadNuevoCpe').getValue() === 'TELCONET') {
                                                                    Ext.getCmp('selecDispositivoCpe').setVisible(true);
                                                                }

                                                                //Deshabilitamos la selección del dispositivo Radio
                                                                if (Ext.isEmpty(data.strPropietarioRadioCliente) ||
                                                                    data.strPropietarioRadioCliente === 'CLIENTE') {
                                                                    Ext.getCmp('selectDispositivoRadio').setVisible(false);
                                                                }

                                                                //Habilitamos la selección del dispositivo Radio
                                                                if (Ext.getCmp('propiedadNuevoRadio').getValue() === 'TELCONET') {
                                                                    Ext.getCmp('selectDispositivoRadio').setVisible(true);
                                                                }

                                                                if(Ext.isEmpty(datos[0].esPseudoPe) || datos[0].esPseudoPe !== 'S')
                                                                {
                                                                    if(data.ultimaMilla === 'Fibra Optica')
                                                                    {
                                                                        if(data.usaUltimaMillaExistente === 'NO')
                                                                        {
                                                                            Ext.getCmp('nuevoCpeTransciever').setVisible(true);
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.getCmp('nuevoCpeTransciever').setVisible(false);
                                                                        }
                                                                    }

                                                                    if(data.ultimaMilla === 'Radio')
                                                                    {
                                                                        if(data.usaUltimaMillaExistente === 'NO')
                                                                        {
                                                                            Ext.getCmp('nuevoRadio').setVisible(true);
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.getCmp('nuevoRadio').setVisible(false);
                                                                        }
                                                                    }
                                                                }

                                                                if(booleanRedGpon){
                                                                    Ext.getCmp('nuevoONT').setVisible(false);
                                                                }

                                                                Ext.getCmp('nuevoDispositivos').setVisible(true);
                                                                Ext.getCmp('nuevoDispositivosNodo').setVisible(true);
                                                                Ext.getCmp('existeCpe').setVisible(true);
                                                                Ext.getCmp('existeDatosCpe').setVisible(true);
                                                                win.center();
                                                            }
                                                        }
                                                    }
                                                }
                                            ]
                                        },

                                        //Selección Técnico Responsable
                                        comboEmpleado(data),

                                        //escoger cpe de otro servicio
                                        {
                                            id:'existeCpe',
                                            xtype: 'fieldset',
                                            title: 'Cpe Existente',
                                            defaultType: 'textfield',
                                            visible: false,
                                            defaults: {
                                                width: 300
                                            },
                                            items: [

                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 7,
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        { width: '10%', border: false},
                                                        {
                                                            queryMode:      'local',
                                                            xtype:          'combobox',
                                                            id:             'elementoCliente',
                                                            name:           'elementoCliente',
                                                            fieldLabel:     'Cpe',
                                                            displayField:   'nombreElemento',
                                                            valueField:     'idElemento',
                                                            loadingText:    'Buscando...',
                                                            store:          storeElementosClientesPorPunto,
                                                            width:          300,
                                                            listeners: {
                                                                select: function(combo, record, index) {
                                                                    CargarInformacionCPE(data,Ext.getCmp('elementoCliente').valueModels[0].raw);
                                                                }
                                                            }
                                                        },
                                                        { width: 30, border: false},
                                                        {
                                                            queryMode:      'local',
                                                            xtype:          'combobox',
                                                            id:             'cmbInterfaceElemento',
                                                            name:           'cmbInterfaceElemento',
                                                            fieldLabel:     'Puertos Cpe',
                                                            displayField:   'nombreInterfaceElemento',
                                                            valueField:     'idInterfaceElemento',
                                                            store:          storeInterfacesCpe,
                                                            width:          200,
                                                            disabled:       true,
                                                            listeners: {
                                                                select: function(combo){
                                                                    var objeto = combo.valueModels[0].raw;

                                                                    if(objeto.mac===null)
                                                                    {
                                                                        Ext.getCmp('existeMacCpe').setReadOnly(false);
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.getCmp('existeMacCpe').setReadOnly(true);
                                                                    }
                                                                    Ext.getCmp('existeMacCpe').setRawValue(objeto.mac);
                                                                    Ext.getCmp('existeMacCpe').setValue = objeto.mac;

                                                                    estadoInterfaceCpeExistente = objeto.estado;

                                                                    Ext.getCmp('estadoInterface').setDisabled(false);
                                                                    Ext.getCmp('estadoInterface').setValue = objeto.tipoEnlace;
                                                                    Ext.getCmp('estadoInterface').setRawValue(objeto.tipoEnlace);
                                                                }
                                                            }
                                                        },
                                                        { width: 30, border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'estadoInterface',
                                                            name:           'estadoInterface',
                                                            fieldLabel:     'Tipo Enlace Puerto',
                                                            displayField:   "",
                                                            fieldStyle:     "font-weight: bold",
                                                            value:          "",
                                                            readOnly:       true,
                                                            disabled:       true,
                                                            width:          250
                                                        }

                                                        //---------------------------------------


                                                        //---------------------------------------

                                                    ]//cierre del container table
                                                }

                                            ]//cierre del fieldset
                                        },

                                        //nuevo radio - radio
                                        {
                                            xtype      : 'fieldset',
                                            defaultType: 'textfield',
                                            id         : 'nuevoRadio',
                                            title      : 'Radio',
                                            visible    :  false,
                                            items:
                                            [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type   : 'table',
                                                        align  : 'stretch',
                                                        columns:  6
                                                    },
                                                    items:
                                                    [
                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype  : 'container',
                                                            id     : 'selectDispositivoRadio',
                                                            padding: '0 0 10 0',
                                                            layout : {
                                                                type    : 'table',
                                                                pack    : 'left',
                                                                columns :  2
                                                            },
                                                            items:
                                                            [
                                                                {
                                                                    xtype : 'component',
                                                                    html  : '<label style="color:green;">'+
                                                                                '<b>Seleccione el dispositivo</b>'+
                                                                            '</label>&nbsp;&nbsp;'+
                                                                            '<label style="color:black;">'+
                                                                                '<i class="fa fa-long-arrow-right" aria-hidden="true"></i>' +
                                                                            '</label>'
                                                                },
                                                                //4: Radio
                                                                seleccionarDispositivo(null,4)
                                                            ]
                                                        },
                                                        {width:'20%',border:false},
                                                        {width:'20%',border:false},
                                                        {width:'20%',border:false},
                                                        {width:'20%',border:false},

                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'combobox',
                                                            fieldLabel:     'Propiedad de',
                                                            id:             'propiedadNuevoRadio',
                                                            name:           'propiedadNuevoRadio',
                                                            value:          '-Seleccione-',
                                                            store:           [['TELCONET','TELCONET']],
                                                            labelWidth:      110,
                                                            width:          '60%',
                                                            listeners:
                                                            {
                                                                select: function(combo)
                                                                {
                                                                    Ext.getCmp('selectDispositivoRadio').setVisible(true);
                                                                    var propiedad = combo.getValue();

                                                                    if (propiedad === 'CLIENTE')
                                                                    {
                                                                        Ext.getCmp('selectDispositivoRadio').setVisible(false);
                                                                        Ext.getCmp('descripcionNuevoRadio').setValue = "";
                                                                        Ext.getCmp('descripcionNuevoRadio').setRawValue("");
                                                                        Ext.getCmp('serieNuevoRadio').setValue = "";
                                                                        Ext.getCmp('serieNuevoRadio').setRawValue("");
                                                                        Ext.getCmp('modeloNuevoRadio').setValue = "";
                                                                        Ext.getCmp('modeloNuevoRadio').setRawValue("");
                                                                        Ext.getCmp('macNuevoRadio').setValue = "";
                                                                        Ext.getCmp('macNuevoRadio').setRawValue("");
                                                                        Ext.getCmp('descripcionNuevoRadio').setReadOnly(false);
                                                                        Ext.getCmp('serieNuevoRadio').setReadOnly(false);
                                                                        Ext.getCmp('modeloNuevoRadio').setReadOnly(false);
                                                                        Ext.getCmp('macNuevoRadio').setReadOnly(false);
                                                                    }
                                                                    else
                                                                    {
                                                                        if (data.strSerieRadioCliente != "")
                                                                        {
                                                                            Ext.getCmp('serieNuevoRadio').setValue = data.strSerieRadioCliente;
                                                                            Ext.getCmp('serieNuevoRadio').setRawValue(data.strSerieRadioCliente);
                                                                            Ext.getCmp('serieNuevoRadio').focus();
                                                                            Ext.getCmp('serieNuevoRadio').blur();
                                                                        }
                                                                        Ext.getCmp('descripcionNuevoRadio').setReadOnly(true);
                                                                        Ext.getCmp('serieNuevoRadio').setReadOnly(true);
                                                                        Ext.getCmp('modeloNuevoRadio').setReadOnly(true);
                                                                        Ext.getCmp('macNuevoRadio').setReadOnly(true);
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        {
                                                            xtype:          'hidden',
                                                            id:             'validacionMacNuevoRadio',
                                                            name:           'validacionMacNuevoRadio',
                                                            value:          ''
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'nombreNuevoRadio',
                                                            name:           'nombreNuevoRadio',
                                                            fieldLabel:     'Nombre Radio',
                                                            displayField:   '',
                                                            value:          '',
                                                            labelWidth:      110,
                                                            width:          '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'descripcionNuevoRadio',
                                                            name:           'descripcionNuevoRadio',
                                                            fieldLabel:     'Descripción Radio',
                                                            displayField:   '',
                                                            value:          '',
                                                            readOnly:        true,
                                                            labelWidth:      110,
                                                            width:          '70%'
                                                        },

                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'serieNuevoRadio',
                                                            name:           'serieNuevoRadio',
                                                            fieldLabel:     'Serie Radio',
                                                            readOnly:        true,
                                                            labelWidth:      110,
                                                            width:          '70%',
                                                            listeners:
                                                            {
                                                                blur: function(serie)
                                                                {
                                                                    if(Ext.getCmp('propiedadNuevoRadio').getValue() === '-Seleccione-')
                                                                    {
                                                                        Ext.Msg.alert('Advertencia','Debe Seleccionar de quien es propiedad el Radio' );
                                                                    }
                                                                    else if(Ext.getCmp('propiedadNuevoRadio').getValue() === 'TELCONET')
                                                                    {
                                                                        Ext.Ajax.request({
                                                                            url: buscarCpeNaf,
                                                                            method: 'post',
                                                                            params: { 
                                                                                serieCpe:       serie.getValue(),
                                                                                modeloElemento: '',
                                                                                estado:         'PI',
                                                                                bandera:        'ActivarServicio',
                                                                                idServicio:     data.idServicio
                                                                            },
                                                                            success: function(response){
                                                                                var respuesta       = response.responseText.split("|");
                                                                                var status          = respuesta[0];
                                                                                var mensaje         = respuesta[1].split(",");
                                                                                strEsRadioExistente = respuesta[2];
                                                                                var descripcion = mensaje[0];
                                                                                var macRadio     = mensaje[1];
                                                                                var modeloRadio  = mensaje[2];

                                                                                Ext.getCmp('descripcionNuevoRadio').setValue = '';
                                                                                Ext.getCmp('descripcionNuevoRadio').setRawValue('');

                                                                                Ext.getCmp('macNuevoRadio').setValue = '';
                                                                                Ext.getCmp('macNuevoRadio').setRawValue('');

                                                                                Ext.getCmp('modeloNuevoRadio').setValue = '';
                                                                                Ext.getCmp('modeloNuevoRadio').setRawValue('');
                                                                                if(status=="OK")
                                                                                {
                                                                                    if(storeModelosRadio.find('modelo',modeloRadio)==-1)
                                                                                    {
                                                                                        var strMsj = 'El Elemento con: <br>'+
                                                                                        'Modelo: <b>'+modeloRadio+' </b><br>'+
                                                                                        'Descripcion: <b>'+descripcion+' </b><br>'+
                                                                                        'No corresponde a un Radio, <br>'+
                                                                                        'No podrá continuar con el proceso, Favor Revisar <br>';
                                                                                        Ext.Msg.alert('Advertencia', strMsj);
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        Ext.getCmp('descripcionNuevoRadio').setValue = descripcion;
                                                                                        Ext.getCmp('descripcionNuevoRadio').setRawValue(descripcion);

                                                                                        Ext.getCmp('macNuevoRadio').setValue = macRadio;
                                                                                        Ext.getCmp('macNuevoRadio').setRawValue(macRadio);

                                                                                        Ext.getCmp('modeloNuevoRadio').setValue = modeloRadio;
                                                                                        Ext.getCmp('modeloNuevoRadio').setRawValue(modeloRadio);
                                                                                    }

                                                                                }
                                                                                else
                                                                                {
                                                                                    Ext.Msg.alert('Mensaje ', mensaje);
                                                                                }
                                                                            },
                                                                            failure: function(result)
                                                                            {
                                                                                Ext.get(formPanel.getId()).unmask();
                                                                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                                            }
                                                                        });//Ext.Ajax.request
                                                                    }//if
                                                                }//blur
                                                            }
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'modeloNuevoRadio',
                                                            name:           'modeloNuevoRadio',
                                                            fieldLabel:     'Modelo Radio',
                                                            displayField:   '',
                                                            value:          '',
                                                            readOnly:        true,
                                                            labelWidth:      110,
                                                            width:          '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'macNuevoRadio',
                                                            name:           'macNuevoRadio',
                                                            fieldLabel:     'Mac',
                                                            displayField:   '',
                                                            value:          '',
                                                            readOnly:        true,
                                                            labelWidth:      110,
                                                            width:          '70%',
                                                            listeners:
                                                            {
                                                                blur: function(text){
                                                                    var mac = text.getValue();

                                                                    if(!(mac === 'NO EXISTE ELEMENTO')){
                                                                        if(mac.match("[a-fA-f0-9]{4}[\.]+[a-fA-f0-9]{4}[\.]+[a-fA-F0-9]{4}$"))
                                                                        {
                                                                            Ext.getCmp('validacionMacNuevoRadio').setValue = "correcta";
                                                                            Ext.getCmp('validacionMacNuevoRadio').setRawValue("correcta") ;
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.Msg.alert('Mensaje ', 'Formato de Mac Incorrecto (xxxx.xxxx.xxxx), '+
                                                                                                      'Favor Revisar');
                                                                            Ext.getCmp('validacionMacNuevoRadio').setValue = "incorrecta";
                                                                            Ext.getCmp('validacionMacNuevoRadio').setRawValue("incorrecta") ;
                                                                        }

                                                                    }
                                                                }
                                                            }
                                                        },

                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'versionIosNuevoRadio',
                                                            name:           'versionIosNuevoRadio',
                                                            fieldLabel:     'Versión IOS Radio',
                                                            displayField:   '',
                                                            value:          '',
                                                            labelWidth:      110,
                                                            width:          '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'combobox',
                                                            fieldLabel:     'Gestion Remota',
                                                            id:             'gestionRemotaNuevoRadio',
                                                            value:          '-Seleccione-',
                                                            labelWidth:      110,
                                                            width:          '60%',
                                                            store:           [['SI','SI'],['NO','NO']]

                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'combobox',
                                                            fieldLabel:     'Administra Radio',
                                                            id:             'administraNuevoRadio',
                                                            value:          '-Seleccione-',
                                                            labelWidth:      110,
                                                            width:          '60%',
                                                            store:
                                                            [
                                                                ['TN-CONF','TN-CONF'],
                                                                ['TN-CONF Y CLIENTE-CONF','TN-CONF Y CLIENTE-CONF'],
                                                                ['TN-CONF Y CLIENTE-MON' ,'TN-CONF Y CLIENTE-MON'],
                                                                ['TN-MON Y CLIENTE-CONF' ,'TN-MON Y CLIENTE-CONF'],
                                                                ['CLIENTE-CONF','CLIENTE-CONF'],
                                                                ['TN-NO SCRIPT','TN-NO SCRIPT']
                                                            ]
                                                        }

                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        },

                                        //nuevo cpe - cpe
                                        {
                                            xtype      : 'fieldset',
                                            defaultType: 'textfield',
                                            id         : 'nuevoCpe',
                                            title      : 'Cpe',
                                            visible    :  false,
                                            items:
                                            [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type   : 'table',
                                                        align  : 'stretch',
                                                        columns:  6
                                                    },
                                                    items: [

                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype  : 'container',
                                                            id     : 'selecDispositivoCpe',
                                                            padding: '0 0 10 0',
                                                            layout : {
                                                                type    : 'table',
                                                                pack    : 'left',
                                                                columns :  2
                                                            },
                                                            items: [
                                                                {
                                                                    xtype : 'component',
                                                                    id    : 'lblSelectDispCpe',
                                                                    html  : '<label style="color:green;">'+
                                                                                '<b>Seleccione el dispositivo</b>'+
                                                                            '</label>&nbsp;&nbsp;'+
                                                                            '<label style="color:black;">'+
                                                                                '<i class="fa fa-long-arrow-right" aria-hidden="true"></i>' +
                                                                            '</label>'
                                                                },
                                                                //1: Cpe
                                                                seleccionarDispositivo(null,1)
                                                            ]
                                                        },
                                                        {width:'20%',border:false},
                                                        {width:'20%',border:false},
                                                        {width:'20%',border:false},
                                                        {width:'20%',border:false},

                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:      'combobox',
                                                            fieldLabel: 'Propiedad de',
                                                            id:         'propiedadNuevoCpe',
                                                            name:       'propiedadNuevoCpe',
                                                            value:      '-Seleccione-',
                                                            store:       [['CLIENTE','CLIENTE'],['TELCONET','TELCONET']],
                                                            labelWidth:  110,
                                                            width:      '60%',

                                                            listeners:
                                                            {
                                                                select: function(combo)
                                                                {
                                                                    Ext.getCmp('selecDispositivoCpe').setVisible(true);
                                                                    var propiedad = combo.getValue();

                                                                    if (propiedad === 'CLIENTE')
                                                                    {
                                                                        Ext.getCmp('selecDispositivoCpe').setVisible(false);
                                                                        Ext.getCmp('descripcionNuevoCpe').setValue = "";
                                                                        Ext.getCmp('descripcionNuevoCpe').setRawValue("");
                                                                        Ext.getCmp('serieNuevoCpe').setValue = "";
                                                                        Ext.getCmp('serieNuevoCpe').setRawValue("");
                                                                        Ext.getCmp('modeloNuevoCpe').setValue = "";
                                                                        Ext.getCmp('modeloNuevoCpe').setRawValue("");
                                                                        Ext.getCmp('macNuevoCpe').setValue = "";
                                                                        Ext.getCmp('macNuevoCpe').setRawValue("");
                                                                        Ext.getCmp('descripcionNuevoCpe').setReadOnly(false);
                                                                        Ext.getCmp('serieNuevoCpe').setReadOnly(false);
                                                                        Ext.getCmp('modeloNuevoCpe').setReadOnly(false);
                                                                        Ext.getCmp('macNuevoCpe').setReadOnly(false);
                                                                    }
                                                                    else
                                                                    {
                                                                        if (data.strSerieCpeCliente != "")
                                                                        {
                                                                            Ext.getCmp('serieNuevoCpe').setValue = data.strSerieCpeCliente;
                                                                            Ext.getCmp('serieNuevoCpe').setRawValue(data.strSerieCpeCliente);
                                                                            Ext.getCmp('serieNuevoCpe').focus();
                                                                            Ext.getCmp('serieNuevoCpe').blur();
                                                                        }

                                                                        Ext.getCmp('descripcionNuevoCpe').setReadOnly(true);
                                                                        Ext.getCmp('serieNuevoCpe').setReadOnly(true);
                                                                        Ext.getCmp('modeloNuevoCpe').setReadOnly(true);
                                                                        Ext.getCmp('macNuevoCpe').setReadOnly(false);
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        {
                                                            xtype:          'hidden',
                                                            id:             'validacionMacNuevoCpe',
                                                            name:           'validacionMacNuevoCpe',
                                                            value:          ''
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'nombreNuevoCpe',
                                                            name:           'nombreNuevoCpe',
                                                            fieldLabel:     'Nombre Cpe',
                                                            displayField:   '',
                                                            value:          '',
                                                            labelWidth:      110,
                                                            width:          '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'descripcionNuevoCpe',
                                                            name:           'descripcionNuevoCpe',
                                                            fieldLabel:     'Descripción Cpe',
                                                            displayField:   '',
                                                            value:          '',
                                                            readOnly:        true,
                                                            labelWidth:      110,
                                                            width:          '70%'
                                                        },

                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'serieNuevoCpe',
                                                            name:           'serieNuevoCpe',
                                                            fieldLabel:     'Serie Cpe',
                                                            displayField:   '',
                                                            value:          '',
                                                            readOnly:        true,
                                                            labelWidth:      110,
                                                            width:          '70%',
                                                            listeners:
                                                            {
                                                                blur: function(serie){
                                                                    if(Ext.getCmp('propiedadNuevoCpe').getValue() === '-Seleccione-')
                                                                    {
                                                                        Ext.Msg.alert('Advertencia','Debe Seleccionar de quien es propiedad el Cpe' );
                                                                    }
                                                                    else if(Ext.getCmp('propiedadNuevoCpe').getValue() === 'TELCONET')
                                                                    {
                                                                        Ext.Ajax.request({
                                                                            url: buscarCpeNaf,
                                                                            method: 'post',
                                                                            params: {
                                                                                serieCpe:          serie.getValue(),
                                                                                modeloElemento:    '',
                                                                                estado:            datos[0].hasOwnProperty('zeroTouchData') ? 'IN' : 'PI',
                                                                                bandera:           'ActivarServicio',
                                                                                comprobarInterfaz: 'SI',
                                                                                tipoElemento:      'CPE',
                                                                                idServicio:        data.idServicio
                                                                            },
                                                                            success: function(response){
                                                                                var respuesta     = response.responseText.split("|");
                                                                                var status        = respuesta[0];
                                                                                var mensaje       = respuesta[1].split(",");
                                                                                strEsCpeExistente = respuesta[2];
                                                                                var descripcion = mensaje[0];
                                                                                var macCpe      = mensaje[1];
                                                                                var modeloCpe   = mensaje[2];

                                                                                Ext.getCmp('descripcionNuevoCpe').setValue = '';
                                                                                Ext.getCmp('descripcionNuevoCpe').setRawValue('');

                                                                                Ext.getCmp('macNuevoCpe').setValue = '';
                                                                                Ext.getCmp('macNuevoCpe').setRawValue('');

                                                                                Ext.getCmp('modeloNuevoCpe').setValue = '';
                                                                                Ext.getCmp('modeloNuevoCpe').setRawValue('');

                                                                                if(status=="OK")
                                                                                {
                                                                                    if(storeModelosCpe.find('modelo',modeloCpe)==-1)
                                                                                    {
                                                                                        var strMsj = 'El Elemento con: <br>'+
                                                                                        'Modelo: <b>'+modeloCpe+' </b><br>'+
                                                                                        'Descripcion: <b>'+descripcion+' </b><br>'+
                                                                                        'No corresponde a un CPE, <br>'+
                                                                                        'No podrá continuar con el proceso, Favor Revisar <br>';
                                                                                        Ext.Msg.alert('Advertencia', strMsj);
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        Ext.getCmp('descripcionNuevoCpe').setValue = descripcion;
                                                                                        Ext.getCmp('descripcionNuevoCpe').setRawValue(descripcion);

                                                                                        Ext.getCmp('macNuevoCpe').setValue = macCpe;
                                                                                        Ext.getCmp('macNuevoCpe').setRawValue(macCpe);

                                                                                        Ext.getCmp('modeloNuevoCpe').setValue = modeloCpe;
                                                                                        Ext.getCmp('modeloNuevoCpe').setRawValue(modeloCpe);
                                                                                    }
                                                                                }
                                                                                else
                                                                                {
                                                                                    Ext.Msg.alert('Mensaje ', mensaje);
                                                                                }
                                                                            },
                                                                            failure: function(result)
                                                                            {
                                                                                Ext.get(formPanel.getId()).unmask();
                                                                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                                            }
                                                                        });//Ext.Ajax.request
                                                                    }//if
                                                                }//blur
                                                            }
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'modeloNuevoCpe',
                                                            name:           'modeloNuevoCpe',
                                                            fieldLabel:     'Modelo Cpe',
                                                            displayField:   '',
                                                            value:          '',
                                                            readOnly:        true,
                                                            labelWidth:      110,
                                                            width:          '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'macNuevoCpe',
                                                            name:           'macNuevoCpe',
                                                            fieldLabel:     'Mac',
                                                            displayField:    data.mac,
                                                            value:           data.mac,
                                                            readOnly:        false,
                                                            labelWidth:      110,
                                                            width:          '70%',
                                                            listeners:
                                                            {
                                                                blur: function(text){
                                                                    var mac = text.getValue();

                                                                    if(!(mac === 'NO EXISTE ELEMENTO')){
                                                                        if(mac.match("[a-fA-f0-9]{4}[\.]+[a-fA-f0-9]{4}[\.]+[a-fA-F0-9]{4}$"))
                                                                        {
                                                                            Ext.getCmp('validacionMacNuevoCpe').setValue = "correcta";
                                                                            Ext.getCmp('validacionMacNuevoCpe').setRawValue("correcta") ;
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.Msg.alert('Mensaje ', 'Formato de Mac Incorrecto (xxxx.xxxx.xxxx), Favor Revisar');
                                                                            Ext.getCmp('validacionMacNuevoCpe').setValue = "incorrecta";
                                                                            Ext.getCmp('validacionMacNuevoCpe').setRawValue("incorrecta") ;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        },

                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'versionIosNuevoCpe',
                                                            name:           'versionIosNuevoCpe',
                                                            fieldLabel:     'Versión IOS Cpe',
                                                            displayField:   '',
                                                            value:          '',
                                                            labelWidth:      110,
                                                            width:          '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'combobox',
                                                            fieldLabel:     'Gestión Remota',
                                                            id:             'gestionRemotaNuevoCpe',
                                                            value:          '-Seleccione-',
                                                            labelWidth:      110,
                                                            width:          '60%',
                                                            store:          [['SI','SI'],['NO','NO']]

                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:          'combobox',
                                                            fieldLabel:     'Administra Cpe',
                                                            id:             'administraNuevoCpe',
                                                            value:          '-Seleccione-',
                                                            labelWidth:      110,
                                                            width:          '60%',
                                                            store: 
                                                            [
                                                                ['TN-CONF','TN-CONF'],
                                                                ['TN-CONF Y CLIENTE-CONF','TN-CONF Y CLIENTE-CONF'],
                                                                ['TN-CONF Y CLIENTE-MON' ,'TN-CONF Y CLIENTE-MON'],
                                                                ['TN-MON Y CLIENTE-CONF' ,'TN-MON Y CLIENTE-CONF'],
                                                                ['CLIENTE-CONF','CLIENTE-CONF'],
                                                                ['TN-NO SCRIPT','TN-NO SCRIPT']
                                                            ]
                                                        }

                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        },

                                        //nuevo cpe - transciever / Dispositivos Nodo
                                        {
                                            xtype : 'fieldset',
                                            id    : 'nuevoDispositivos',
                                            hidden:  true,
                                            layout: {
                                                type   : 'table',
                                                pack   : 'center',
                                                columns:  2
                                            },
                                            items:
                                            [
                                                {
                                                    xtype : 'fieldset',
                                                    id    : 'nuevoCpeTransciever',
                                                    title : 'Transciever Cliente',
                                                    hidden:  true,
                                                    layout: {
                                                        tdAttrs: {style: 'padding:1px;'},
                                                        type   : 'table',
                                                        pack   : 'stretch',
                                                        columns:  1
                                                    },
                                                    items:
                                                    [
                                                        {
                                                            xtype  : 'container',
                                                            id     : 'seleccioneDispositivoTransciever',
                                                            padding: '0 0 10 0',
                                                            layout : {
                                                                type    : 'table',
                                                                pack    : 'left',
                                                                columns :  2
                                                            },
                                                            items: [
                                                                {
                                                                    xtype: 'component',
                                                                    html : '<label style="color:green;">'+
                                                                                '<b>Seleccione el dispositivo</b>'+
                                                                           '</label>&nbsp;&nbsp;'+
                                                                           '<label style="color:black;">'+
                                                                                '<i class="fa fa-long-arrow-right" aria-hidden="true"></i>' +
                                                                           '</label>'
                                                                },
                                                                //2: Transciever
                                                                seleccionarDispositivo(null,2)
                                                            ]
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'serieNuevoTransciever',
                                                            name:           'serieNuevoTransciever',
                                                            fieldLabel:     'Serie Transciever',
                                                            displayField:   '',
                                                            value:          '',
                                                            labelWidth:     150,
                                                            width:          330,
                                                            readOnly:       true,
                                                            listeners: {
                                                                blur: function(serie){
                                                                    Ext.Ajax.request({
                                                                        url: buscarCpeNaf,
                                                                        method: 'post',
                                                                        params: {
                                                                            serieCpe:       serie.getValue(),
                                                                            modeloElemento: '',
                                                                            estado:         datos[0].hasOwnProperty('zeroTouchData') ? 'IN' : 'PI',
                                                                            bandera:        'ActivarServicio',
                                                                            idServicio:     data.idServicio
                                                                        },
                                                                        success: function(response){
                                                                            var respuesta             = response.responseText.split("|");
                                                                            var status                = respuesta[0];
                                                                            var mensaje               = respuesta[1].split(",");
                                                                            strEsTransceiverExistente = respuesta[2];
                                                                            var descripcion = mensaje[0];
                                                                            var modelo      = mensaje[2];

                                                                            Ext.getCmp('descripcionNuevoTransciever').setValue = '';
                                                                            Ext.getCmp('descripcionNuevoTransciever').setRawValue('');

                                                                            Ext.getCmp('modeloNuevoTransciever').setValue = '';
                                                                            Ext.getCmp('modeloNuevoTransciever').setRawValue('');
                                                                            if(status=="OK")
                                                                            {
                                                                                if(storeModelosTransciever.find('modelo',modelo)==-1)
                                                                                {
                                                                                    var strMsj = 'El Elemento con: <br>'+
                                                                                    'Modelo: <b>'+modelo+' </b><br>'+
                                                                                    'Descripcion: <b>'+descripcion+' </b><br>'+
                                                                                    'No corresponde a un Transciever, <br>'+
                                                                                    'No podrá continuar con el proceso, Favor Revisar <br>';
                                                                                    Ext.Msg.alert('Advertencia', strMsj);
                                                                                }
                                                                                else
                                                                                {
                                                                                    Ext.getCmp('descripcionNuevoTransciever').setValue = descripcion;
                                                                                    Ext.getCmp('descripcionNuevoTransciever').setRawValue(descripcion);

                                                                                    Ext.getCmp('modeloNuevoTransciever').setValue = modelo;
                                                                                    Ext.getCmp('modeloNuevoTransciever').setRawValue(modelo);
                                                                                }
                                                                            }
                                                                            else
                                                                            {
                                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                            }
                                                                        },
                                                                        failure: function(result)
                                                                        {
                                                                            Ext.get(formPanel.getId()).unmask();
                                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                                        }
                                                                    });//Ext.Ajax.request
                                                                }//blur
                                                            }
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'modeloNuevoTransciever',
                                                            name:           'modeloNuevoTransciever',
                                                            fieldLabel:     'Modelo Transciever',
                                                            displayField:   '',
                                                            valueField:     '',
                                                            labelWidth:     150,
                                                            width:          330,
                                                            readOnly:       true
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'descripcionNuevoTransciever',
                                                            name:           'descripcionNuevoTransciever',
                                                            fieldLabel:     'Descripción Transciever',
                                                            displayField:   '',
                                                            value:          '',
                                                            labelWidth:     150,
                                                            width:          330,
                                                            readOnly:       true
                                                        }
                                                    ]
                                                },
                                                {
                                                    xtype : 'fieldset',
                                                    id    : 'nuevoDispositivosNodo',
                                                    title : 'Dispositivos en Nodo',
                                                    hidden:  true,
                                                    layout: {type:'table',pack:'center',columns:1},
                                                    items : [dispositivosNodo(data)]
                                                }
                                            ]
                                        },

                                        //nuevo cpe - roseta
                                        {
                                            id: 'nuevoCpeRoseta',
                                            xtype: 'fieldset',
                                            title: 'Roseta',
                                            defaultType: 'textfield',
                                            visible: false,
                                            items: [

                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 6,
                                                        align: 'stretch'
                                                    },
                                                    items: [

                                                        { width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'codigoNuevaRoseta',
                                                            name:           'codigoNuevaRoseta',
                                                            fieldLabel:     'Codigo Roseta',
                                                            displayField:   "",
                                                            value:          "",
                                                            width:          '25%'
                                                        },
                                                        { width: '20%', border: false},
                                                        { width: '20%', border: false},
                                                        { width: '20%', border: false},
                                                        { width: '20%', border: false}

                                                        //---------------------------------------


                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        },

                                        //ont
                                        {
                                            id:'nuevoONT',
                                            xtype: 'fieldset',
                                            title: 'Informacion del ONT',
                                            defaultType: 'textfield',
                                            visible: false,
                                            defaults: {
                                                width: 540
                                            },
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 5,
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        { width: '10%', border: false},
                                                        {
                                                            queryMode: 'local',
                                                            xtype: 'textfield',
                                                            id:'serieOnt',
                                                            name: 'serieOnt',
                                                            fieldLabel: 'Serie ONT',
                                                            displayField: "",
                                                            value: "",
                                                            width: '25%',
                                                            loadingText: 'Buscando...',
                                                            listeners: {
                                                                blur: function(serie){
                                                                    Ext.Ajax.request({
                                                                        url: buscarCpeHuaweiNaf,
                                                                        method: 'post',
                                                                        params: {
                                                                            serieCpe: serie.getValue(),
                                                                            modeloElemento: '',
                                                                            estado: 'PI',
                                                                            bandera: 'ActivarServicio'
                                                                        },
                                                                        success: function(response){
                                                                            var respuesta = response.responseText.split("|");
                                                                            var status = respuesta[0];
                                                                            var mensaje = respuesta[1].split(",");
                                                                            var descripcion = mensaje[0];
                                                                            var macOntNaf = mensaje[1];
                                                                            var modeloCpe   = mensaje[2];
                                                                            Ext.getCmp('descripcionOnt').setValue = '';
                                                                            Ext.getCmp('descripcionOnt').setRawValue('');
                                                                            Ext.getCmp('macOnt').setValue = '';
                                                                            Ext.getCmp('macOnt').setRawValue('');
                                                                            Ext.getCmp('modeloOnt').setValue = '';
                                                                            Ext.getCmp('modeloOnt').setRawValue('');
                                                                            if(status=="OK")
                                                                            {
                                                                                Ext.getCmp('descripcionOnt').setValue = descripcion;
                                                                                Ext.getCmp('descripcionOnt').setRawValue(descripcion);
                                                                                Ext.getCmp('macOnt').setValue = macOntNaf;
                                                                                Ext.getCmp('macOnt').setRawValue(macOntNaf);
                                                                                Ext.getCmp('modeloOnt').setValue = modeloCpe;
                                                                                Ext.getCmp('modeloOnt').setRawValue(modeloCpe);
                                                                            }
                                                                            else
                                                                            {
                                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                            }
                                                                        },
                                                                        failure: function(result)
                                                                        {
                                                                            Ext.get(formPanel.getId()).unmask();
                                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                                        }
                                                                    });
                                                                }
                                                            }
                                                        },
                                                        { width: '20%', border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'modeloOnt',
                                                            name: 'modeloOnt',
                                                            fieldLabel: 'Modelo ONT',
                                                            displayField: "",
                                                            value: "",
                                                            width: '25%'
                                                        },
                                                        { width: '10%', border: false},
                                                        //---------------------------------------
                                                        { width: '10%', border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            id:'macOnt',
                                                            name: 'macOnt',
                                                            fieldLabel: 'Mac ONT',
                                                            displayField: "",
                                                            value: "",
                                                            width: '25%'
                                                        },
                                                        { width: '20%', border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            id:'descripcionOnt',
                                                            name: 'descripcionOnt',
                                                            fieldLabel: 'Descripcion ONT',
                                                            displayField: "",
                                                            value: "",
                                                            readOnly: true,
                                                            width: '25%'
                                                        },
                                                        { width: '10%', border: false},
                                                        //---------------------------------------
                                                    ]//cierre del container table
                                                }
                                            ]//cierre del fieldset
                                        },

                                        //existente cpe - cpe
                                        {
                                            id: 'existeDatosCpe',
                                            xtype: 'fieldset',
                                            title: 'Datos Cpe',
                                            defaultType: 'textfield',
                                            visible: false,
                                            items: [

                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 6,
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {   width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'existePropiedadDe',
                                                            name:           'existePropiedadDe',
                                                            fieldLabel:     'Propiedad De',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },
                                                        { width:          '20%' },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'existeNombreCpe',
                                                            name:           'existeNombreCpe',
                                                            fieldLabel:     'Nombre Cpe',
                                                            displayField:   "",
                                                            value:          "",
                                                            width:          '25%',
                                                            readOnly:       true
                                                        },
                                                        { width: '20%', border: false},
                                                        {
                                                            xtype:          'hidden',
                                                            id:             'existeServicioId',
                                                            name:           'existeServicioId',
                                                            displayField:   "",
                                                            value:          "",
                                                            width:          '25%',
                                                        },

                                                        //---------------------------------------

                                                        { width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'existeSerieCpe',
                                                            name:           'existeSerieCpe',
                                                            fieldLabel:     'Serie Cpe',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },
                                                        { width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'existeModeloCpe',
                                                            name:           'existeModeloCpe',
                                                            fieldLabel:     'Modelo Cpe',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },
                                                        {   width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'existeMacCpe',
                                                            name:           'existeMacCpe',
                                                            fieldLabel:     'Mac Cpe',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },

                                                        //---------------------------------------

                                                        {   width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'existeVersionIosCpe',
                                                            name:           'existeVersionIosCpe',
                                                            fieldLabel:     'Version IOS Cpe',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },
                                                        {
                                                            width:          '20%',
                                                            border:         false
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'existeGestionRemotaCpe',
                                                            name:           'existeGestionRemotaCpe',
                                                            fieldLabel:     'Gestion Remota Cpe',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },
                                                        { width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'existeAdministraCpe',
                                                            name:           'existeAdministraCpe',
                                                            fieldLabel:     'Administra Cpe',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        }
                                                        //---------------------------------------

                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        }
                                    ]
                                },
                            ],
                            buttons:
                            [{
                                text    : 'Activar',
                                formBind: true,
                                handler : function()
                                {
                                    //datos radio
                                    var modeloNuevoRadio              = Ext.getCmp('modeloNuevoRadio').getValue();
                                    var serieNuevoRadio               = Ext.getCmp('serieNuevoRadio').getValue();
                                    var descripcionNuevoRadio         = Ext.getCmp('descripcionNuevoRadio').getValue();
                                    var macNuevoRadio                 = Ext.getCmp('macNuevoRadio').getValue();
                                    var propiedadNuevoRadio           = Ext.getCmp('propiedadNuevoRadio').getValue();
                                    var iosNuevoRadio                 = Ext.getCmp('versionIosNuevoRadio').getValue();
                                    var gestionNuevoRadio             = Ext.getCmp('gestionRemotaNuevoRadio').getValue();
                                    var administraNuevoRadio          = Ext.getCmp('administraNuevoRadio').getValue();
                                    var validacionNuevoRadio          = Ext.getCmp('validacionMacNuevoRadio').getRawValue();
                                    var nombreNuevoRadio              = Ext.getCmp('nombreNuevoRadio').getValue();

                                    //datos ont
                                    var serieOnt                    = Ext.getCmp('serieOnt').getValue();
                                    var modeloOnt                   = Ext.getCmp('modeloOnt').getValue();
                                    var macOnt                      = Ext.getCmp('macOnt').getValue();
                                    var descripcionOnt              = Ext.getCmp('descripcionOnt').getValue();

                                    //datos cpe
                                    var modeloNuevoCpe              = Ext.getCmp('modeloNuevoCpe').getValue();
                                    var serieNuevoCpe               = Ext.getCmp('serieNuevoCpe').getValue();
                                    var descripcionNuevoCpe         = Ext.getCmp('descripcionNuevoCpe').getValue();
                                    var macNuevoCpe                 = Ext.getCmp('macNuevoCpe').getValue();
                                    var propiedadNuevoCpe           = Ext.getCmp('propiedadNuevoCpe').getValue();
                                    var iosNuevoCpe                 = Ext.getCmp('versionIosNuevoCpe').getValue();
                                    var gestionNuevoCpe             = Ext.getCmp('gestionRemotaNuevoCpe').getValue();
                                    var administraNuevoCpe          = Ext.getCmp('administraNuevoCpe').getValue();
                                    var validacionNuevoCpe          = Ext.getCmp('validacionMacNuevoCpe').getRawValue();
                                    var nombreNuevoCpe              = Ext.getCmp('nombreNuevoCpe').getValue();

                                    //interface cpe existente
                                    var interfaceCpeExistente       = Ext.getCmp('cmbInterfaceElemento').getValue();
                                    var macCpeExistente             = Ext.getCmp('existeMacCpe').getValue();

                                    //datos roseta
                                    var nombreRoseta                = Ext.getCmp('codigoNuevaRoseta').getValue();

                                    //datos transciever
                                    var serieNuevoTransciever       = Ext.getCmp('serieNuevoTransciever').getValue();
                                    var modeloNuevoTransciever      = Ext.getCmp('modeloNuevoTransciever').getValue();
                                    var descripcionNuevoTransciever = Ext.getCmp('descripcionNuevoTransciever').getValue();

                                    //datos cpe existente
                                    var propiedadExisteCpe          = Ext.getCmp('existePropiedadDe').getValue();
                                    var nombreExisteCpe             = Ext.getCmp('existeNombreCpe').getValue();
                                    var serieExisteCpe              = Ext.getCmp('existeSerieCpe').getValue();
                                    var modeloExisteCpe             = Ext.getCmp('existeModeloCpe').getValue();
                                    var macExisteCpe                = Ext.getCmp('existeMacCpe').getValue();
                                    var iosExisteCpe                = Ext.getCmp('existeVersionIosCpe').getValue();
                                    var gestionExisteCpe            = Ext.getCmp('existeGestionRemotaCpe').getValue();
                                    var administraExisteCpe         = Ext.getCmp('existeAdministraCpe').getValue();
                                    var comboElementoExiste         = Ext.getCmp('elementoCliente').getValue();
                                    var existeServicioId            = Ext.getCmp('existeServicioId').getValue();

                                    var peElemento                  = Ext.getCmp('pe').getValue();

                                    var flagCpe = Ext.ComponentQuery.query('[name=rbCpe]')[0].getGroupValue();

                                    var validacion  = false;
                                    var flag        = 0;

                                    if(booleanRedGpon){
                                        if(flagCpe === "nuevo" && (serieOnt=="" || macOnt=="")){
                                            validacion=false;
                                            flag=-1;
                                        }
                                        else{
                                            validacion=true;
                                        }
                                        if(descripcionOnt=="ELEMENTO ESTADO INCORRECTO" ||
                                           descripcionOnt=="ELMENTO CON SALDO CERO" ||
                                           descripcionOnt=="NO EXISTE ELEMENTO")
                                        {
                                            validacion=false;
                                            flag=7;
                                        }
                                    }

                                    //Obtenemos los dispositivos en nodo;
                                    var idTecnicoEncargado     = Ext.getCmp('comboFilterTecnico').getValue();
                                    var storeDispositivosNodo  = null;
                                    var arrayDispositivosNodo  = [];
                                    var jsonDipositivosNodo    = "";

                                    if (typeof Ext.getCmp("gridDispositivosNodo") !== 'undefined') {
                                        storeDispositivosNodo = Ext.getCmp("gridDispositivosNodo").getStore();
                                        if (storeDispositivosNodo.data.items.length > 0) {
                                            $.each(storeDispositivosNodo.data.items, function(i, item) {
                                                arrayDispositivosNodo.push(item.data);
                                            });
                                            jsonDipositivosNodo = Ext.JSON.encode(arrayDispositivosNodo);
                                        }
                                    }

                                    if (Ext.isEmpty(idTecnicoEncargado) && flagCpe === "nuevo" && propiedadNuevoCpe !== "CLIENTE") {
                                        Ext.Msg.show({
                                            title: 'Alerta',msg: 'Por favor seleccione el Técnico Encargado.!',
                                            icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                                            buttonText: {cancel: 'Cerrar'}
                                        });
                                        return;
                                    }

                                    if (flagCpe === "nuevo" && flag == 0)
                                    {
                                        if (!Ext.isEmpty(datos[0].esPseudoPe) && datos[0].esPseudoPe === 'S')
                                        {
                                            if (serieNuevoCpe      === "" ||
                                                macNuevoCpe        === "" ||
                                                propiedadNuevoCpe  === "-Seleccione-" ||
                                                gestionNuevoCpe    === "-Seleccione-" ||
                                                administraNuevoCpe === "-Seleccione-")
                                            {
                                                validacion = false;
                                            }
                                            else
                                            {
                                                validacion = true;
                                            }
                                        }
                                        else
                                        {
                                            if(serieNuevoCpe === "" || peElemento === "" || macNuevoCpe === "" ||
                                                propiedadNuevoCpe === "-Seleccione-" || iosNuevoCpe === "-Seleccione-" ||
                                                gestionNuevoCpe === "-Seleccione-" || administraNuevoCpe === "-Seleccione-" ||
                                                (data.ultimaMilla != "Radio" && (serieNuevoTransciever === "" || modeloNuevoTransciever === "")
                                                 && !booleanRedGpon) ||
                                                (data.ultimaMilla == "Radio" && (serieNuevoRadio === "" || macNuevoRadio === "" ||
                                                    propiedadNuevoRadio === "-Seleccione-" || iosNuevoRadio === "-Seleccione-" ||
                                                    gestionNuevoRadio === "-Seleccione-" || administraNuevoRadio === "-Seleccione-")))
                                            {
                                                validacion = false;
                                            }
                                            else
                                            {
                                                validacion = true;
                                            }

                                            if((data.ultimaMilla == "Radio") && (descripcionNuevoRadio === ""))
                                            {
                                                validacion = false;
                                                flag = 5;
                                            }

                                            if(descripcionNuevoCpe === "")
                                            {
                                                validacion = false;
                                                flag = 3;
                                            }

                                            if(data.ultimaMilla !== "Radio" && (data.ultimaMilla === "Fibra Optica" && descripcionNuevoTransciever === ""))
                                            {
                                                validacion = false;
                                                flag = 2;
                                            }

                                            if(validacionNuevoCpe === "incorrecta")
                                            {
                                                validacion = false;
                                                flag = 1;
                                            }
                                            if (datos[0].hasOwnProperty('zeroTouchData'))
                                            {
                                                let objCpe = datos[0]['zeroTouchData']
                                                    ['ELEMENTOS_INSTALACION']['cpeCliente'];
                                                let objTransceiver = datos[0]['zeroTouchData']
                                                    ['ELEMENTOS_INSTALACION']['transceiverCliente'];

                                                let strNuevoCpe = Ext.getCmp('serieNuevoCpe').getValue();
                                                let strNuevoTransceiver = Ext.getCmp('serieNuevoTransciever').getValue();

                                                if (strNuevoCpe !== objCpe['serie'])
                                                {
                                                    validacion = false;
                                                    Ext.Msg.alert(
                                                        "Validacion",
                                                        "Los datos del CPE no coinciden con los ingresados en el flujo ZeroTouch");
                                                }
                                                if (strNuevoTransceiver !== objTransceiver['serie'])
                                                {
                                                    validacion = false;
                                                    Ext.Msg.alert(
                                                        "Validacion",
                                                        "Los datos del TRANSCEIVER no coinciden con los ingresados en el flujo ZeroTouch");
                                                }
                                            }
                                            if((data.ultimaMilla == "Radio") && (validacionNuevoRadio === "incorrecta"))
                                            {
                                                validacion = false;
                                                flag = 6;
                                            }

                                            if(data.ultimaMilla === "UTP")
                                            {
                                                if(serieNuevoCpe === "" || peElemento === "" || macNuevoCpe === "" ||
                                                    propiedadNuevoCpe === "-Seleccione-" || iosNuevoCpe === "-Seleccione-" ||
                                                    gestionNuevoCpe === "-Seleccione-" || administraNuevoCpe === "-Seleccione-")
                                                {
                                                    validacion = false;
                                                    flag = 3;//Validacion referente a CPE
                                                }
                                                else
                                                {
                                                    validacion = true;
                                                }
                                            }
                                        }
                                    }
                                    else if (flag == 0)
                                    {
                                        if (comboElementoExiste === null)
                                        {
                                            validacion=false;
                                            flag=4;
                                        }
                                        else
                                        {
                                            validacion = true;
                                        }

                                        macNuevoCpe = Ext.getCmp('existeMacCpe').getValue();
                                    }

                                    if (validacion && seActiva)
                                    {
                                        Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');

                                        Ext.Ajax.request({
                                            url    :  activarClienteBoton,
                                            timeout:  1000000,
                                            method : 'post',
                                            params: {

                                                //Datos de los dispositivos en el nodo.
                                               'jsonDipositivosNodo'   : jsonDipositivosNodo,
                                               'idTecnicoEncargado'    : idTecnicoEncargado,

                                                idServicio:             data.idServicio,
                                                interfaceElementoId:    data.interfaceElementoId,
                                                idProducto:             data.productoId,
                                                flagCpe:                flagCpe,
                                                login:                  data.login,
                                                idServicioWifi:         JSON.stringify(data.idServicioWifi),
                                                idIntWifiSim:           JSON.stringify(data.idIntWifiSim),
                                                tipoRed:                data.strTipoRed,
                                                arrayZeroTouch:         datos[0].hasOwnProperty('zeroTouchData') ?
                                                                        JSON.stringify(datos[0]['zeroTouchData']) :
                                                                        null,

                                                //datos radio
                                                nombreNuevoRadio:          nombreNuevoRadio,
                                                serieNuevoRadio:           serieNuevoRadio,
                                                modeloNuevoRadio:          modeloNuevoRadio,
                                                macNuevoRadio:             macNuevoRadio,
                                                propiedadNuevoRadio:       propiedadNuevoRadio,
                                                iosNuevoRadio:             iosNuevoRadio,
                                                gestionNuevoRadio:         gestionNuevoRadio,
                                                administraNuevoRadio:      administraNuevoRadio,
                                                ultimaMilla:               data.ultimaMilla,

                                                //datos ont
                                                serieOnt:               serieOnt,
                                                modeloOnt:              modeloOnt,
                                                macOnt:                 macOnt,

                                                //datos cpe
                                                nombreNuevoCpe:         nombreNuevoCpe,
                                                serieNuevoCpe:          serieNuevoCpe,
                                                modeloNuevoCpe:         modeloNuevoCpe,
                                                macNuevoCpe:            macNuevoCpe,
                                                propiedadNuevoCpe:      propiedadNuevoCpe,
                                                iosNuevoCpe:            iosNuevoCpe,
                                                gestionNuevoCpe:        gestionNuevoCpe,
                                                administraNuevoCpe:     administraNuevoCpe,

                                                //datos roseta
                                                nombreNuevoRoseta:      nombreRoseta,

                                                //datos transciever
                                                serieNuevoTransciever:  serieNuevoTransciever,
                                                modeloNuevoTransciever: modeloNuevoTransciever,

                                                //datos cpe existente
                                                propiedadExisteCpe:     propiedadExisteCpe,
                                                nombreExisteCpe:        nombreExisteCpe,
                                                serieExisteCpe:         serieExisteCpe,
                                                modeloExisteCpe:        modeloExisteCpe,
                                                macExisteCpe:           macExisteCpe,
                                                iosExisteCpe:           iosExisteCpe,
                                                gestionExisteCpe:       gestionExisteCpe,
                                                administraExisteCpe:    administraExisteCpe,
                                                idServicioExisteCpe:    existeServicioId,
                                                interfaceCpeExistente:   interfaceCpeExistente,
                                                estadoInterfaceCpe     : estadoInterfaceCpeExistente,
                                                macCpeExistente:         macCpeExistente,

                                                //Datos para WS
                                                vlan        : data.vlan,
                                                anillo      : data.anillo,
                                                capacidad1  : data.capacidadUno,
                                                capacidad2  : data.capacidadDos,

                                                esPseudoPe  : data.esPseudoPe,

                                                strEsRadioExistente       : strEsRadioExistente,
                                                strEsCpeExistente         : strEsCpeExistente,
                                                strEsTransceiverExistente : strEsTransceiverExistente,
                                                booleanEsMigracionSDWAN: data.booleanEsMigracionSDWAN, 
                                                booleanEsSDWAN:          data.booleanEsSDWAN
                                            },
                                            success: function(response){
                                                Ext.get(formPanel.getId()).unmask();
                                                if(response.responseText === "OK")
                                                {
                                                    /*Se valida que el servicio posea un Internet Wifi para proceder a crear el router wifi*/
                                                    if (data.idIntWifiSim)
                                                    {
                                                        Ext.Ajax.request({
                                                            url: crearRouterWifiExistente,
                                                            method: 'post',
                                                            timeout: 1000000,
                                                            params: {
                                                                idServicio:              data.idServicio,
                                                                tipoEnlace:              data.tipoEnlace,
                                                                interfaceElementoId:     data.interfaceElementoId,
                                                                idProducto:              data.productoId,
                                                                login:                   data.login,
                                                                idServicioWifi:          JSON.stringify(data.idServicioWifi),
                                                                idIntWifiSim:            JSON.stringify(data.idIntWifiSim)
                                                            },
                                                            success: function(response)
                                                            {
                                                                const jsonResponse = JSON.parse(response.responseText);
                                                                if(jsonResponse.status == "OK")
                                                                {
                                                                    Ext.get(formPanel.getId()).unmask();
                                                                    Ext.Msg.alert('Transacción Exitosa',
                                                                        `Se activó el cliente y se creó el router wifi.<br>Favor continue con la asignación de recursos de red a los L3MPLS de Administración y Navegación.`, function(btn){
                                                                            if(btn==='ok')
                                                                            {
                                                                                win.destroy();
                                                                                store.load();
                                                                            }
                                                                        });
                                                                }
                                                            },
                                                            failure: function(result)
                                                            {
                                                                Ext.get(formPanel.getId()).unmask();
                                                                Ext.Msg.alert('Mensaje','Se Activo el Cliente, pero ocurrió un error al crear el Router WIFI', function(btn){
                                                                    if(btn==='ok')
                                                                    {
                                                                        win.destroy();
                                                                        store.load();
                                                                    }
                                                                });

                                                            }
                                                        });
                                                    } else
                                                    {
                                                        Ext.get(formPanel.getId()).unmask();
                                                        Ext.Msg.alert('Mensaje','Se Activo el Cliente', function(btn){
                                                            if(btn==='ok')
                                                            {
                                                                win.destroy();
                                                                store.load();
                                                            }
                                                        });
                                                    }
                                                }
                                                else if(response.responseText === "CANTIDAD CERO"){
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Mensaje ','CPEs Agotados, favor revisar' );
                                                }
                                                else if(response.responseText === "NO EXISTE PRODUCTO"){
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                                }
                                                else if(response.responseText === "NO EXISTE CPE"){
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Mensaje ','No existe el CPE indicado, favor revisar' );
                                                }
                                                else if(response.responseText === "CPE NO ESTA EN ESTADO"){
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Mensaje ','Equipo no esta en PENDIENTE INSTALACION/RETIRADO, favor revisar' );
                                                }
                                                else{
                                                    Ext.Msg.alert('Mensaje ',response.responseText );
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                            }
                                        });

                                    }
                                    else
                                    {
                                        if(flag===1)
                                        {
                                            Ext.Msg.alert("Validacion","La Mac esta incorrecta, favor revisar", function(btn){
                                                    if(btn==='ok'){
                                                    }
                                            });
                                        }
                                        else if(flag===2)
                                        {
                                            Ext.Msg.alert("Validacion","Datos de Transciever incorrectos, favor revisar", function(btn){
                                                    if(btn==='ok'){
                                                    }
                                            });
                                        }
                                        else if(flag===3)
                                        {
                                            Ext.Msg.alert("Validacion","Datos de CPE incorrectos, favor revisar", function(btn){
                                                    if(btn==='ok'){
                                                    }
                                            });
                                        }
                                        else if(flag===4)
                                        {
                                            Ext.Msg.alert("Validacion","Debe Seleccionar un CPE, favor revisar", function(btn){
                                                    if(btn==='ok'){
                                                    }
                                            });
                                        }
                                        else if(flag===5)
                                        {
                                            Ext.Msg.alert("Validacion","Datos de RADIO incorrectos, favor revisar", function(btn){
                                                    if(btn==='ok'){
                                                    }
                                            });
                                        }
                                        else if (flag===6)
                                        {
                                            Ext.Msg.alert("Validacion","La Mac esta incorrecta, favor revisar", function(btn){
                                                    if(btn==='ok'){
                                                    }
                                            });
                                        }
                                        else if (flag===7)
                                        {
                                            Ext.Msg.alert("Validación","Datos del Ont incorrectos, favor revisar!", function(btn){
                                                    if(btn === 'ok'){
                                                        console.log("");
                                                    }
                                            });
                                        }
                                        else
                                        {
                                            Ext.Msg.alert("Validacion","Debe llenar todos los campos para Activar el Servicio", function(btn){
                                                    if(btn==='ok'){
                                                    }
                                            });
                                        }
                                    }
                                }
                            },
                            {
                                text   : 'Cancelar',
                                handler: function()
                                {
                                    win.destroy();
                                }
                            }]
                        });

                        Ext.getCmp('nuevoCpe').setVisible(false);
                        Ext.getCmp('nuevoCpeRoseta').setVisible(false);
                        Ext.getCmp('nuevoCpeTransciever').setVisible(false);
                        Ext.getCmp('nuevoRadio').setVisible(false);
                        Ext.getCmp('existeCpe').setVisible(false);
                        Ext.getCmp('existeDatosCpe').setVisible(false);
                        Ext.getCmp('nuevoONT').setVisible(false);

                        var win = Ext.create('Ext.window.Window', {
                            title    : tituloPanel,
                            modal    : true,
                            width    : 1100,
                            closable : true,
                            layout   : 'fit',
                            items    : [formPanel]
                        }).show();

                        storeElementosClientesPorPunto.load({
                            callback:function(){
                                storeModelosCpe.load({
                                    callback:function(){
                                        storeModelosTransciever.load({
                                            callback: function(){
                                                storeModelosRadio.load({});
                                            }
                                        });
                                    }
                                });
                            }
                        });

                        if (data.ultimaMilla == "Radio" )
                        {
                            Ext.getCmp('nuevoCpeRoseta').setVisible(false);
                            Ext.getCmp('hilo').setDisabled(true);
                            Ext.getCmp('elementoContenedor').setDisabled(true);
                            Ext.getCmp('nuevoCpeTransciever').setVisible(false);
                        }
                    }
                }//cierre response
            }
        );
    }
}

function limpiarCamposInternetMpls()
{
    Ext.getCmp('existePropiedadDe').setRawValue("");
    Ext.getCmp('existePropiedadDe').setValue = "";

    Ext.getCmp('existeGestionRemotaCpe').setRawValue("");
    Ext.getCmp('existeGestionRemotaCpe').setValue = "";

    Ext.getCmp('existeAdministraCpe').setRawValue("");
    Ext.getCmp('existeAdministraCpe').setValue = "";

    Ext.getCmp('existeNombreCpe').setRawValue("");
    Ext.getCmp('existeNombreCpe').setValue = "";

    Ext.getCmp('existeSerieCpe').setRawValue("");
    Ext.getCmp('existeSerieCpe').setValue = "";

    Ext.getCmp('existeModeloCpe').setRawValue("");
    Ext.getCmp('existeModeloCpe').setValue = "";

    Ext.getCmp('existeServicioId').setRawValue("");
    Ext.getCmp('existeServicioId').setValue = "";

    Ext.getCmp('existeVersionIosCpe').setRawValue("");
    Ext.getCmp('existeVersionIosCpe').setValue = "";  
    
    //--TX
    
    Ext.getCmp('descripcionNuevoTransciever').setValue = '';
    Ext.getCmp('descripcionNuevoTransciever').setRawValue('');

    Ext.getCmp('modeloNuevoTransciever').setValue = '';
    Ext.getCmp('modeloNuevoTransciever').setRawValue('');
    
    Ext.getCmp('serieNuevoTransciever').setValue = '';
    Ext.getCmp('serieNuevoTransciever').setRawValue('');      
    
    // -- Radio
    
    Ext.getCmp('descripcionNuevoRadio').setValue = '';
    Ext.getCmp('descripcionNuevoRadio').setRawValue('');

    Ext.getCmp('macNuevoRadio').setValue = '';
    Ext.getCmp('macNuevoRadio').setRawValue('');

    Ext.getCmp('modeloNuevoRadio').setValue = '';
    Ext.getCmp('modeloNuevoRadio').setRawValue('');
    
    Ext.getCmp('serieNuevoRadio').setValue = "";
    Ext.getCmp('serieNuevoRadio').setRawValue("");
}

function CargarInformacionCPE(data, modelo){
    var objeto = modelo;

    var UsaUMExistente = data.usaUltimaMillaExistente;

    Ext.MessageBox.wait("Verificando puertos disponibles...", 'Porfavor espere');

    Ext.getCmp('cmbInterfaceElemento').setRawValue("");

    storeInterfacesCpe.proxy.extraParams = {
                                             idElemento:objeto.idElemento,
                                             interface :"wan",
                                             usaUMExistente : UsaUMExistente,
                                             tipoOrden :data.tipoOrden
                                           };
    storeInterfacesCpe.load({
        callback:function()
        {
            Ext.MessageBox.hide();
            //Si el servicio a instalar es NUEVA UM y no trae interfaces disponibles
            //el cpe no debe permitir continuar
            if(UsaUMExistente ==='NO' && storeInterfacesCpe.getCount()===0)
            {
                Ext.MessageBox.show({
                    title: "Alerta",
                    msg: "Cpe no tiene puertos \n\
                          disponibles para activar Servicio",
                    icon: Ext.MessageBox.WARNING,
                    closable: true
                });                               

                limpiarCamposInternetMpls();

                Ext.getCmp('estadoInterface').setValue = "";
                Ext.getCmp('estadoInterface').setRawValue("");

                Ext.getCmp('cmbInterfaceElemento').setRawValue("");                                                                                
                Ext.getCmp('cmbInterfaceElemento').setDisabled(true);

                seActiva = false;
            }
            else
            {
                Ext.getCmp('existePropiedadDe').setRawValue(objeto.propiedad);
                Ext.getCmp('existePropiedadDe').setValue = objeto.propiedad;

                Ext.getCmp('existeGestionRemotaCpe').setRawValue(objeto.gestion);
                Ext.getCmp('existeGestionRemotaCpe').setValue = objeto.gestion;

                Ext.getCmp('existeAdministraCpe').setRawValue(objeto.administra);
                Ext.getCmp('existeAdministraCpe').setValue = objeto.administra;

                Ext.getCmp('existeNombreCpe').setRawValue(objeto.nombreElemento);
                Ext.getCmp('existeNombreCpe').setValue = objeto.nombreElemento;

                Ext.getCmp('existeSerieCpe').setRawValue(objeto.serieElemento);
                Ext.getCmp('existeSerieCpe').setValue = objeto.serieElemento;

                Ext.getCmp('existeModeloCpe').setRawValue(objeto.modeloElemento);
                Ext.getCmp('existeModeloCpe').setValue = objeto.modeloElemento;

                Ext.getCmp('existeServicioId').setRawValue(objeto.servicioId);
                Ext.getCmp('existeServicioId').setValue = objeto.servicioId;

                Ext.getCmp('existeVersionIosCpe').setRawValue(objeto.versionElemento);
                Ext.getCmp('existeVersionIosCpe').setValue = objeto.versionElemento;

                Ext.getCmp('cmbInterfaceElemento').setValue("");
                Ext.getCmp('cmbInterfaceElemento').setDisabled(false);

                seActiva = true;
            }
        }
    });
}
