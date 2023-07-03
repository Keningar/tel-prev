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
function activarServicioL3MPLS(data, gridIndex)
{
    var estadoInterfaceCpeExistente;
    var seActiva   = true;
    var esPseudoPe = false;
    var esSatelital= false;
    var strEsRadioExistente       = "NO";
    var strEsCpeExistente         = "NO";
    var strEsTransceiverExistente = "NO";

    const boolActivarWifi = ValidarConcentradoresInternetWifi(data, gridIndex);

    if(data.capacidadUno === null || data.capacidadDos === null)
    {
        Ext.Msg.alert('Error ','Error: No Existen los valores de bandwidth, Favor Notificar a Sistemas!');
    }
    else
    {
        var tituloPanel = "Activar Servicio " + data.strTipoRed;
        var tituloElementoConector = "Nombre Cassette";
        var booleanRedGpon = false;
        if (data.ultimaMilla == "Radio" )
        {
            tituloElementoConector = "Nombre Radio";
        }
        if(data.booleanTipoRedGpon)
        {
            tituloElementoConector = "Splitter Elemento";
            booleanRedGpon = true;
        }
        
        var height = 170;
        
        if(data.ultimaMilla === 'SATELITAL')
        {
            esSatelital = true;
            height      = 200;
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
                                        if(data.usaUltimaMillaExistente === 'SI')
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
                                  {name:'nombreElemento',   mapping:'nombreElemento'},
                                  {name:'tipoElemento',     mapping:'tipoElemento'}
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
                            bodyPadding  : 2,
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
                                        height: height
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
                                                { width: '15%', border: false},
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
                                                    name: 'protocolo',
                                                    fieldLabel: 'Protocolo',
                                                    displayField: data.protocolo,
                                                    value: data.protocolo,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'asPrivado',
                                                    fieldLabel: 'AS Privado',
                                                    displayField: data.asPrivado,
                                                    value: data.asPrivado,
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
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'edificio',
                                                    name: 'edificio',
                                                    fieldLabel: 'Edificio',
                                                    displayField: datos[0].nombreEdificio,
                                                    value: datos[0].nombreEdificio,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},
                                                
                                                 //---------------------------------------------                                                                                               
                                                
                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'subredBb',
                                                    name: 'subredBb',
                                                    fieldLabel: 'Subred (Pe-Hub)',
                                                    displayField: data.subredVsatBackbone ,
                                                    value: data.subredVsatBackbone,
                                                    readOnly: true,
                                                    width: '30%',
                                                    hidden:!esSatelital
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'subredCli',
                                                    name: 'subredCli',
                                                    fieldLabel: 'Subred (Vsat-Cliente)',
                                                    displayField: data.subredVsatCliente ,
                                                    value: data.subredVsatCliente ,
                                                    readOnly: true,
                                                    width: '30%',
                                                    hidden:!esSatelital
                                                },
                                                { width: '10%', border: false}
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
                                        height: height
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
                                                    displayField: data.descripcionPresentaFactura,
                                                    value: data.descripcionPresentaFactura,
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
                                                    readOnly: true,
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
                                                    id:'ipServicio',
                                                    name: 'ipServicio',
                                                    fieldLabel: 'Ip WAN',
                                                    displayField: data.ipServicio,
                                                    value: data.ipServicio,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------
                                                { width: '10%', border: false},
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
                                                { width: '10%', border: false},
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
                                            id        : 'radioCpeOpcion',
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
                                                                Ext.getCmp('serieNuevoCpe').setRawValue(objCpe['serie']);
                                                                Ext.getCmp('serieNuevoCpe').setReadOnly(true);

                                                                Ext.getCmp('serieNuevoTransciever').setValue(objTransceiver['serie']);
                                                                Ext.getCmp('serieNuevoTransciever').setRawValue(objTransceiver['serie']);
                                                                Ext.getCmp('serieNuevoTransciever').setReadOnly(true);

                                                            }
                                                            if (nv)
                                                            {
                                                                Ext.getCmp('comboFilterTecnico').setVisible(true);
                                                                Ext.getCmp('selecDispositivoCpe').setVisible(true);
                                                                Ext.getCmp('selectDispositivoRadio').setVisible(true);
                                                                Ext.getCmp('nuevoDispositivos').setVisible(true);
                                                                Ext.getCmp('nuevoDispositivosNodo').setVisible(true);

                                                                Ext.getCmp('nuevoCpe').setVisible(true);

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

                                                                if (data.ultimaMilla === "Radio")
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

                                                                if ( data.ultimaMilla === "UTP")
                                                                {
                                                                    Ext.getCmp('nuevoCpeRoseta').setVisible(false);
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

                                                                seActiva = true;
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
                                                        { width: 10, border: false},
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
                                                            xtype:        'combobox',
                                                            fieldLabel:   'Propiedad de',
                                                            id:           'propiedadNuevoRadio',
                                                            name:         'propiedadNuevoRadio',
                                                            value:        '-Seleccione-',
                                                            store:         [['TELCONET','TELCONET']],
                                                            labelWidth:    110,
                                                            width:        '60%',
                                                            listeners:
                                                            {
                                                                select: function(combo)
                                                                {
                                                                    var propiedad = combo.getValue();
                                                                    Ext.getCmp('selectDispositivoRadio').setVisible(true);

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
                                                            xtype:        'hidden',
                                                            id:           'validacionMacNuevoRadio',
                                                            name:         'validacionMacNuevoRadio',
                                                            value:        '',
                                                        },
                                                        {
                                                            xtype:        'textfield',
                                                            id:           'nombreNuevoRadio',
                                                            name:         'nombreNuevoRadio',
                                                            fieldLabel:   'Nombre Radio',
                                                            displayField: '',
                                                            value:        '',
                                                            labelWidth:    110,
                                                            width:        '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:        'textfield',
                                                            id:           'descripcionNuevoRadio',
                                                            name:         'descripcionNuevoRadio',
                                                            fieldLabel:   'Descripción Radio',
                                                            displayField: '',
                                                            value:        '',
                                                            readOnly:      true,
                                                            labelWidth:    110,
                                                            width:        '70%'
                                                        },

                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:        'textfield',
                                                            id:           'serieNuevoRadio',
                                                            name:         'serieNuevoRadio',
                                                            fieldLabel:   'Serie Radio',
                                                            displayField: '',
                                                            value:        '',
                                                            readOnly:      true,
                                                            labelWidth:    110,
                                                            width:        '70%',
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
                                                                                serieCpe:                serie.getValue(),
                                                                                modeloElemento:          '',
                                                                                estado:                  'PI',
                                                                                bandera:                 'ActivarServicio',
                                                                                permiteReutilizarEquipo: 'SI',
                                                                                idServicio:               data.idServicio
                                                                            },
                                                                            success: function(response){
                                                                                var respuesta            = response.responseText.split("|");
                                                                                var status               = respuesta[0];
                                                                                var mensaje              = respuesta[1].split(",");
                                                                                var strEsExistente       = respuesta[2];
                                                                                var intInterfaceEleClie  = respuesta[3];
                                                                                if (data.tipoOrden == "T")
                                                                                {
                                                                                    strEsRadioExistente = respuesta[2];
                                                                                }
                                                                                var descripcion = mensaje[0];
                                                                                var macRadio     = mensaje[1];
                                                                                var modeloRadio  = mensaje[2];
                                                                                
                                                                                Ext.getCmp('descripcionNuevoRadio').setValue = '';
                                                                                Ext.getCmp('descripcionNuevoRadio').setRawValue('');

                                                                                Ext.getCmp('macNuevoRadio').setValue = '';
                                                                                Ext.getCmp('macNuevoRadio').setRawValue('');

                                                                                Ext.getCmp('modeloNuevoRadio').setValue = '';
                                                                                Ext.getCmp('modeloNuevoRadio').setRawValue('');
                                                                                
                                                                                Ext.getCmp('strEsExistente').setValue = '';
                                                                                Ext.getCmp('strEsExistente').setRawValue('');
                                                                                
                                                                                Ext.getCmp('intInterfaceEleClie').setValue = '';
                                                                                Ext.getCmp('intInterfaceEleClie').setRawValue('');
                                                                                
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
                                                                                        
                                                                                        if (strEsExistente == "SI")
                                                                                        {
                                                                                            Ext.getCmp('strEsExistente').setValue = strEsExistente;
                                                                                            Ext.getCmp('strEsExistente').setRawValue(strEsExistente);

                                                                                            Ext.getCmp('intInterfaceEleClie').setValue = intInterfaceEleClie;
                                                                                            Ext.getCmp('intInterfaceEleClie').setRawValue(intInterfaceEleClie);
                                                                                        }
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
                                                            xtype:        'textfield',
                                                            id:           'modeloNuevoRadio',
                                                            name:         'modeloNuevoRadio',
                                                            fieldLabel:   'Modelo Radio',
                                                            displayField: '',
                                                            value:        '',
                                                            readOnly:      true,
                                                            labelWidth:    110,
                                                            width:        '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:        'textfield',
                                                            id:           'macNuevoRadio',
                                                            name:         'macNuevoRadio',
                                                            fieldLabel:   'Mac',
                                                            displayField: '',
                                                            value:        '',
                                                            readOnly:      true,
                                                            labelWidth:    110,
                                                            width:        '70%',
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
                                                            xtype:        'textfield',
                                                            id:           'versionIosNuevoRadio',
                                                            name:         'versionIosNuevoRadio',
                                                            fieldLabel:   'Versión IOS Radio',
                                                            displayField: '',
                                                            value:        '',
                                                            labelWidth:    110,
                                                            width:        '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:      'combobox',
                                                            fieldLabel: 'Gestion Remota',
                                                            id:         'gestionRemotaNuevoRadio',
                                                            value:      '-Seleccione-',
                                                            labelWidth:  110,
                                                            width:      '60%',
                                                            store:       [['SI','SI'],['NO','NO']]
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:      'combobox',
                                                            fieldLabel: 'Administra Radio',
                                                            id:         'administraNuevoRadio',
                                                            value:      '-Seleccione-',
                                                            labelWidth:  110,
                                                            width:      '60%',
                                                            store: 
                                                            [
                                                                ['TN-CONF', 'TN-CONF'],
                                                                ['TN-CONF Y CLIENTE-CONF','TN-CONF Y CLIENTE-CONF'],
                                                                ['TN-CONF Y CLIENTE-MON' ,'TN-CONF Y CLIENTE-MON'],
                                                                ['TN-MON Y CLIENTE-CONF' ,'TN-MON Y CLIENTE-CONF'],
                                                                ['CLIENTE-CONF','CLIENTE-CONF'],
                                                                ['TN-NO SCRIPT','TN-NO SCRIPT']
                                                            ]
                                                        },

                                                        //---------------------------------------
                                                        {
                                                            xtype:          'hidden',
                                                            id:             'strEsExistente',
                                                            name:           'strEsExistente',
                                                            value:          "",
                                                            width:          '20%'
                                                        },
                                                        {
                                                            xtype:          'hidden',
                                                            id:             'intInterfaceEleClie',
                                                            name:           'intInterfaceEleClie',
                                                            value:          "",
                                                            width:          '30%'
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
                                                            hidden :  true,
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
                                                                        Ext.getCmp('descripcionNuevoCpe').setReadOnly(true);
                                                                        Ext.getCmp('serieNuevoCpe').setReadOnly(true);
                                                                        Ext.getCmp('modeloNuevoCpe').setReadOnly(true);
                                                                        Ext.getCmp('macNuevoCpe').setReadOnly(false);
                                                                    }

                                                                    //Precargamos la información del cpe.
                                                                    if (!Ext.isEmpty(data.strSerieCpeCliente)) {
                                                                        Ext.getCmp('serieNuevoCpe').setValue = data.strSerieCpeCliente;
                                                                        Ext.getCmp('serieNuevoCpe').setRawValue(data.strSerieCpeCliente);
                                                                        Ext.getCmp('serieNuevoCpe').focus();
                                                                        Ext.getCmp('serieNuevoCpe').blur();
                                                                    }

                                                                    //Precargamos la información del Transciever.
                                                                    if (!Ext.isEmpty(data.strSerieTransceiverCliente)) {
                                                                        Ext.getCmp('serieNuevoTransciever').setValue = data.strSerieTransceiverCliente;
                                                                        Ext.getCmp('serieNuevoTransciever').setRawValue(data.strSerieTransceiverCliente);
                                                                        Ext.getCmp('serieNuevoTransciever').focus();
                                                                        Ext.getCmp('serieNuevoTransciever').blur();
                                                                    }
                                                                }
                                                            }
                                                        },
                                                        {
                                                            xtype: 'hiddenfield',
                                                            id:    'validacionMacNuevoCpe',
                                                            name:  'validacionMacNuevoCpe',
                                                            value: '',
                                                        },
                                                        {
                                                            xtype:        'textfield',
                                                            id:           'nombreNuevoCpe',
                                                            name:         'nombreNuevoCpe',
                                                            fieldLabel:   'Nombre Cpe',
                                                            displayField: '',
                                                            value:        '',
                                                            labelWidth:    110,
                                                            width:        '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:        'textfield',
                                                            id:           'descripcionNuevoCpe',
                                                            name:         'descripcionNuevoCpe',
                                                            fieldLabel:   'Descripción Cpe',
                                                            displayField: '',
                                                            value:        '',
                                                            readOnly:      true,
                                                            labelWidth:    110,
                                                            width:        '70%'
                                                        },

                                                        //---------------------------------------
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:        'textfield',
                                                            id:           'serieNuevoCpe',
                                                            name:         'serieNuevoCpe',
                                                            fieldLabel:   'Serie Cpe',
                                                            displayField: '',
                                                            value:        '',
                                                            readOnly:      true,
                                                            labelWidth:    110,
                                                            width:        '70%',
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
                                                            xtype:        'textfield',
                                                            id:           'modeloNuevoCpe',
                                                            name:         'modeloNuevoCpe',
                                                            fieldLabel:   'Modelo Cpe',
                                                            displayField: '',
                                                            value:        '',
                                                            readOnly:      true,
                                                            labelWidth:    110,
                                                            width:        '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:        'textfield',
                                                            id:           'macNuevoCpe',
                                                            name:         'macNuevoCpe',
                                                            fieldLabel:   'Mac',
                                                            displayField:  data.mac,
                                                            value:         data.mac,
                                                            readOnly:      false,
                                                            labelWidth:    110,
                                                            width:        '70%',
                                                            listeners:
                                                            {
                                                                blur: function(text){
                                                                    var mac = text.getValue();
                                                                    if(!(mac === 'NO EXISTE ELEMENTO')) {
                                                                        if(mac.match("[a-fA-f0-9]{4}[\.]+[a-fA-f0-9]{4}[\.]+[a-fA-F0-9]{4}$")){
                                                                            Ext.getCmp('validacionMacNuevoCpe').setValue = "correcta";
                                                                            Ext.getCmp('validacionMacNuevoCpe').setRawValue("correcta") ;
                                                                        } else {
                                                                            Ext.Msg.alert('Mensaje','Formato de Mac Incorrecto (xxxx.xxxx.xxxx), Favor Revisar');
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
                                                            xtype:        'textfield',
                                                            id:           'versionIosNuevoCpe',
                                                            name:         'versionIosNuevoCpe',
                                                            fieldLabel:   'Versión IOS Cpe',
                                                            displayField: '',
                                                            value:        '',
                                                            labelWidth:    110,
                                                            width:        '70%'
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:      'combobox',
                                                            fieldLabel: 'Gestion Remota',
                                                            id:         'gestionRemotaNuevoCpe',
                                                            value:      '-Seleccione-',
                                                            labelWidth:  110,
                                                            width:      '60%',
                                                            store:      [['SI','SI'],['NO','NO']]
                                                        },
                                                        {width:'20%',border:false},
                                                        {
                                                            xtype:      'combobox',
                                                            fieldLabel: 'Administra Cpe',
                                                            id:         'administraNuevoCpe',
                                                            value:      '-Seleccione-',
                                                            labelWidth:  110,
                                                            width:      '60%',
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
                                                            fieldLabel:     'Descripcion Transciever',
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
                                    var strEsExistente                = Ext.getCmp('strEsExistente').getValue();
                                    var intInterfaceEleClie           = Ext.getCmp('intInterfaceEleClie').getValue();

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
                                    var comboElementoExiste         = Ext.getCmp('elementoCliente').getValue();
                                    var existeServicioId            = Ext.getCmp('existeServicioId').getValue();
                                    var propiedadExisteCpe          = Ext.getCmp('existePropiedadDe').getValue();
                                    var nombreExisteCpe             = Ext.getCmp('existeNombreCpe').getValue();
                                    var serieExisteCpe              = Ext.getCmp('existeSerieCpe').getValue();
                                    var modeloExisteCpe             = Ext.getCmp('existeModeloCpe').getValue();
                                    var macExisteCpe                = Ext.getCmp('existeMacCpe').getValue();
                                    var iosExisteCpe                = Ext.getCmp('existeVersionIosCpe').getValue();
                                    var gestionExisteCpe            = Ext.getCmp('existeGestionRemotaCpe').getValue();
                                    var administraExisteCpe         = Ext.getCmp('existeAdministraCpe').getValue();

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
                                            if (serieNuevoCpe === "" || peElemento === "" || macNuevoCpe === "" ||
                                                propiedadNuevoCpe === "-Seleccione-" || iosNuevoCpe === "-Seleccione-" ||
                                                gestionNuevoCpe === "-Seleccione-" || administraNuevoCpe === "-Seleccione-" ||
                                                (data.ultimaMilla != "Radio" && (serieNuevoTransciever === "" || modeloNuevoTransciever === "") &&
                                                !booleanRedGpon) ||
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

                                                if (data.ultimaMilla == "Radio" && (descripcionNuevoRadio === ""))
                                                {
                                                    validacion = false;
                                                    flag = 5;
                                                }

                                            if (descripcionNuevoCpe === "")
                                            {
                                                validacion = false;
                                                flag = 3;
                                            }

                                            if (data.ultimaMilla != "Radio" && (data.ultimaMilla === "Fibra Optica" && 
                                                descripcionNuevoTransciever === ""))
                                            {
                                                validacion = false;
                                                flag = 2;
                                            }

                                            if (validacionNuevoCpe === "incorrecta")
                                            {
                                                validacion = false;
                                                flag = 1;
                                            }

                                            if (data.ultimaMilla == "Radio" && (validacionNuevoRadio === "incorrecta"))
                                            {
                                                validacion = false;
                                                flag = 6;
                                            }

                                            if (data.ultimaMilla === "UTP")
                                            {
                                                if (serieNuevoCpe === "" || peElemento === "" || macNuevoCpe === "" ||
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
                                        if(comboElementoExiste === null)
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

                                    if(validacion && seActiva)
                                    {
                                        Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');

                                        if(!Ext.isEmpty(datos[0].esPseudoPe) && datos[0].esPseudoPe === 'S')
                                        {
                                            elementoPadre = datos[0].nombreElementoPadre;
                                        }
                                        else
                                        {
                                            elementoPadre = data.elementoPadre;
                                        }

                                        Ext.Ajax.request({
                                            url: activarClienteBoton,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: {

                                                //Datos de los dispositivos en el nodo.
                                               'jsonDipositivosNodo'   : jsonDipositivosNodo,
                                               'idTecnicoEncargado'    : idTecnicoEncargado,

                                                idServicio:              data.idServicio,
                                                tipoEnlace:              data.tipoEnlace,
                                                interfaceElementoId:     data.interfaceElementoId,
                                                idProducto:              data.productoId,
                                                login:                   data.login,
                                                flagCpe:                 flagCpe,
                                                boolActivarWifi:         boolActivarWifi,
                                                idServicioWifi:          JSON.stringify(data.idServicioWifi),
                                                idIntWifiSim:            JSON.stringify(data.idIntWifiSim),
                                                tipoRed:                 data.strTipoRed,
                                                //datos l3mpls
                                                arrayZeroTouch:          datos[0].hasOwnProperty('zeroTouchData') ?
                                                                         JSON.stringify(datos[0]['zeroTouchData']) :
                                                                         null,

                                                //datos l3mpls
                                                loginAux:                data.loginAux,
                                                elementoPadre:           elementoPadre,
                                                elementoNombre:          data.elementoNombre,
                                                interfaceElementoNombre: data.interfaceElementoNombre,
                                                ipServicio:              data.ipServicio,
                                                subredServicio:          data.subredServicio,
                                                gwSubredServicio:        data.gwSubredServicio,
                                                mascaraSubredServicio:   data.mascaraSubredServicio,
                                                protocolo:               data.protocolo,
                                                defaultGateway:          data.defaultGateway,
                                                asPrivado:               data.asPrivado,
                                                vrf:                     data.vrf,
                                                rdId:                    data.rdId,

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
                                                strEsExistente:            strEsExistente,
                                                intInterfaceEleClie:       intInterfaceEleClie,

                                                //datos ont
                                                serieOnt:               serieOnt,
                                                modeloOnt:              modeloOnt,
                                                macOnt:                 macOnt,

                                                //datos cpe
                                                nombreNuevoCpe:          nombreNuevoCpe,
                                                serieNuevoCpe:           serieNuevoCpe,
                                                modeloNuevoCpe:          modeloNuevoCpe,
                                                macNuevoCpe:             macNuevoCpe,
                                                propiedadNuevoCpe:       propiedadNuevoCpe,
                                                iosNuevoCpe:             iosNuevoCpe,
                                                gestionNuevoCpe:         gestionNuevoCpe,
                                                administraNuevoCpe:      administraNuevoCpe,

                                                //datos roseta
                                                nombreNuevoRoseta:       nombreRoseta,

                                                //datos transciever
                                                serieNuevoTransciever:   serieNuevoTransciever,
                                                modeloNuevoTransciever:  modeloNuevoTransciever,

                                                //datos cpe existente
                                                idServicioExisteCpe:     existeServicioId,
                                                interfaceCpeExistente:   interfaceCpeExistente,
                                                estadoInterfaceCpe     : estadoInterfaceCpeExistente,
                                                macCpeExistente:         macCpeExistente,

                                                propiedadExisteCpe:     propiedadExisteCpe,
                                                nombreExisteCpe:        nombreExisteCpe,
                                                serieExisteCpe:         serieExisteCpe,
                                                modeloExisteCpe:        modeloExisteCpe,
                                                macExisteCpe:           macExisteCpe,
                                                iosExisteCpe:           iosExisteCpe,
                                                gestionExisteCpe:       gestionExisteCpe,
                                                administraExisteCpe:    administraExisteCpe,

                                                //Datos para WS
                                                vlan        : data.vlan,
                                                anillo      : data.anillo,
                                                capacidad1  : data.capacidadUno,
                                                capacidad2  : data.capacidadDos,

                                                //PseudoPe
                                                esPseudoPe  : datos[0].esPseudoPe,

                                                strEsRadioExistente       : strEsRadioExistente,
                                                strEsCpeExistente         : strEsCpeExistente,
                                                strEsTransceiverExistente : strEsTransceiverExistente,

                                                booleanEsMigracionSDWAN: data.booleanEsMigracionSDWAN, 
                                                booleanEsSDWAN:          data.booleanEsSDWAN
                                            },
                                            success: function(response)
                                            {
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
                                                    Ext.get(formPanel.getId()).unmask();
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

function activarServiciosSafeCityGpon(data, gridIndex)
{
    var esSatelital                 = false;
    var strEsCpeExistente           = "NO";
    var strEsRadioExistente         = "NO";
    var strEsTransceiverExistente   = "NO";
    var nombreCamara                = "";
    var modeloCpe                   = "";
    var serieCpe                   = "";
    var seActivaOnt                 = true;
    var strTipoElementoActivar      = "ONT";
    var idOnt                       = data.idOnt;
    var serieOnt                    = data.serieOnt;
    var macOnt                      = data.macOnt;
    var modeloOnt                   = data.modeloOnt;
    var idSwPoeGpon                 = data.idSwPoeGpon;
    var idElementoReqGpon           = data.idElementoReqGpon;
    let productoId                  = data.productoId;
    var booleanTieneElementoCliente = false;
    if(data.serieElementoCliente != "" && data.serieElementoCliente != null)
    {
        booleanTieneElementoCliente = true;
    }

    if(data.estadoDatosSafecity == "Activo" && data.booleanActivarSwPoeGpon != "S" && data.requiereSerActivarSafecity != "S")
    {
        var tituloElementoConector = "Nombre Cassette";
        var height                 = 170;

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
                        var  storeInterfacesPorEstadoYElemento = new Ext.data.Store({
                          pageSize: 100,
                          proxy: {
                              type: 'ajax',
                              url : getInterfacesPoEstadoYElemento,
                                extraParams: {
                                    estadoInterface: "not connect",
                                    elementoId:       idOnt,
                                    productoId:       productoId
                                },                              
                              reader: {
                                      type: 'json',
                                      totalProperty: 'total',
                                      root: 'encontrados'
                              }
                          },
                          fields:
                              [
                                {name:'idInterface', mapping:'nombreInterface'}
                              ]
                          });                                             
                        var  storeInterfacesPorEstadoYElementoSwPoe = new Ext.data.Store({
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url : getInterfacesPoEstadoYElemento,
                                extraParams: {
                                    estadoInterface: "not connect",
                                    elementoId:      idSwPoeGpon
                                },
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                            [
                                {name:'idInterface', mapping:'nombreInterface'}
                            ]
                        });                                            
                        var  storeInterfacesPorEstadoYEleReqGpon = new Ext.data.Store({
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url : getInterfacesPoEstadoYElemento,
                                extraParams: {
                                    estadoInterface: "not connect",
                                    elementoId:      idElementoReqGpon
                                },
                                reader: {
                                    type: 'json',
                                    totalProperty: 'total',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                            [
                                {name:'idInterface', mapping:'nombreInterface'}
                            ]
                        });
                        
                        //Esta variable se la utiliza en el combobox (actualmente sin uso) donde se podia selecinar el tipo de resolucion
                        // var  storeResolucionCamara = new Ext.data.Store({
                        //   pageSize: 100,
                        //   proxy: {
                        //       type: 'ajax',
                        //       url : getResolucionCamara,
                        //       reader: {
                        //               type: 'json',
                        //               totalProperty: 'total',
                        //               root: 'encontrados'
                        //       }
                        //   },
                        //   fields:
                        //       [
                        //         {name:'resolucionCam', mapping:'resolucionCam'}
                        //       ]
                        //   });   
                          
                        var  storeCodecCamara = new Ext.data.Store({
                          pageSize: 100,
                          proxy: {
                              type: 'ajax',
                              url : getCodecCamara,
                              reader: {
                                      type: 'json',
                                      totalProperty: 'total',
                                      root: 'encontrados'
                              }
                          },
                          fields:
                              [
                                {name:'codecCam', mapping:'codecCam'}
                              ]
                          });   
                          
                        var  storeFpsCamara = new Ext.data.Store({
                          pageSize: 100,
                          proxy: {
                              type: 'ajax',
                              url : getFpsCamara,
                              reader: {
                                      type: 'json',
                                      totalProperty: 'total',
                                      root: 'encontrados'
                              }
                          },
                          fields:
                              [
                                {name:'fpsCam', mapping:'fpsCam'}
                              ]
                          });   

                        var storeControladora = new Ext.data.Store ({
                            proxy    : {
                                type   : 'ajax',
                                method : 'post',
                                url    :  getIpsControladora,
                                  extraParams: {
                                }, 
                                reader: {
                                    type: 'json',
                                    root: 'encontrados',
                                    totalProperty: 'json'
                                }
                            },
                            fields: [
                                {name: 'idIpControladora', mapping: 'idIpControladora'},
                                {name: 'ipControladora', mapping: 'ipControladora'}
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

                                //informacion de backbone
                                {
                                    colspan: 2,
                                    rowspan:2,
                                    xtype: 'panel',
                                    title: 'Informacion de backbone',
                                    defaults: { 
                                        height: height
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
                                                { width: '15%', border: false},
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

                                                { width: '10%', border: false, hidden: data.esServicioCamaraVpnSafeCity === "S"},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vlan',
                                                    fieldLabel: data.esServicioWifiSafeCity === 'S' ? 'Vlan SSID' : 'Vlan',
                                                    displayField: data.vlan,
                                                    value: data.vlan,
                                                    readOnly: true,
                                                    hidden: data.esServicioCamaraVpnSafeCity === "S",
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false, hidden: data.esServicioCamaraVpnSafeCity === "S"},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vrf',
                                                    fieldLabel: data.esServicioWifiSafeCity === 'S' ? 'Vrf SSID' : 'Vrf',
                                                    displayField: data.vrf,
                                                    value: data.vrf,
                                                    readOnly: true,
                                                    hidden: data.esServicioCamaraVpnSafeCity === "S",
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false, hidden: data.esServicioCamaraVpnSafeCity === "S"},

                                                //---------------------------------------------

                                                { width: '10%', border: false, hidden: data.esServicioWifiSafeCity === 'S' ? false : true},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vlanAdmin',
                                                    fieldLabel: 'Vlan Admin',
                                                    displayField: data.vlanAdmin,
                                                    value: data.vlanAdmin,
                                                    hidden: data.esServicioWifiSafeCity === 'S' ? false : true,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false, hidden: data.esServicioWifiSafeCity === 'S' ? false : true},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'vrfAdmin',
                                                    fieldLabel: 'Vrf Admin',
                                                    displayField: data.vrfAdmin,
                                                    value: data.vrfAdmin,
                                                    hidden: data.esServicioWifiSafeCity === 'S' ? false : true,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false, hidden: data.esServicioWifiSafeCity === 'S' ? false : true},

                                                //---------------------------------------------

                                                { width: '10%', border: false, hidden: data.esServicioCamaraVpnSafeCity === "S"},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'protocolo',
                                                    fieldLabel: 'Protocolo',
                                                    displayField: data.protocolo,
                                                    value: data.protocolo,
                                                    readOnly: true,
                                                    hidden: data.esServicioCamaraVpnSafeCity === "S",
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false, hidden: data.esServicioCamaraVpnSafeCity === "S"},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'asPrivado',
                                                    fieldLabel: 'AS Privado',
                                                    displayField: data.asPrivado,
                                                    value: data.asPrivado,
                                                    readOnly: true,
                                                    hidden: data.esServicioCamaraVpnSafeCity === "S",
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false, hidden: data.esServicioCamaraVpnSafeCity === "S"},

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
                                                },
                                                { width: '15%', border: false},

                                                { width: '10%', border: false},

                                                 //---------------------------------------------                                                                                               

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'subredBb',
                                                    name: 'subredBb',
                                                    fieldLabel: 'Subred (Pe-Hub)',
                                                    displayField: data.subredVsatBackbone ,
                                                    value: data.subredVsatBackbone,
                                                    readOnly: true,
                                                    width: '30%',
                                                    hidden:!esSatelital
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    id: 'subredCli',
                                                    name: 'subredCli',
                                                    fieldLabel: 'Subred (Vsat-Cliente)',
                                                    displayField: data.subredVsatCliente ,
                                                    value: data.subredVsatCliente ,
                                                    readOnly: true,
                                                    width: '30%',
                                                    hidden:!esSatelital
                                                },
                                                { width: '10%', border: false}
                                            ]
                                        }

                                    ]
                                },//cierre de info de backbone

                                //informacion del servicio/producto
                                {
                                    colspan: 2,
                                    rowspan:2,
                                    xtype: 'panel',
                                    title: 'Informacion del Servicio',
                                    defaults: { 
                                        height: height
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
                                                    xtype: 'textfield',
                                                    name: 'producto',
                                                    fieldLabel: 'Producto',
                                                    displayField: data.descripcionPresentaFactura,
                                                    value: data.descripcionPresentaFactura,
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
                                                    readOnly: true,
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
                                                    id:'ipServicio',
                                                    name: 'ipServicio',
                                                    fieldLabel: 'Ip WAN',
                                                    displayField: data.ipServicio,
                                                    value: data.ipServicio,
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------
                                                { width: '10%', border: false},
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
                                                { width: '10%', border: false},
                                            ]
                                        }

                                    ]
                                },//cierre de la informacion servicio/producto

                                //informacion de los elementos del cliente
                                {
                                    colspan: 3,
                                    xtype: 'panel',
                                    title: 'Información de los Elementos del Cliente',
                                    items: [
                                        //Selección Técnico Responsable
                                        comboEmpleadoSafeCity(data),
                                        //Bloque Ont del Datos Safecity
                                        {
                                            id: 'OntDatosSafecity',
                                            xtype: 'fieldset',
                                            title: 'Ont',
                                            defaultType: 'textfield',
                                            hidden: false,
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 3,
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'nombreOnt',
                                                            name:           'nombreOnt',
                                                            fieldLabel:     'Nombre',
                                                            displayField:   "",
                                                            value:          data.nombreOnt,
                                                            readOnly:       true,
                                                            width:          '40%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'marcaOnt',
                                                            name:           'marcaOnt',
                                                            fieldLabel:     'Marca',
                                                            displayField:   "",
                                                            value:          data.marcaOnt,
                                                            readOnly:       true,
                                                            width:          '40%'
                                                        },
                                                        { width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'serieOnt',
                                                            name:           'serieOnt',
                                                            fieldLabel:     'Serie',
                                                            displayField:   "",
                                                            value:          data.serieOnt,
                                                            readOnly:       true,
                                                            width:          '30%'
                                                        },   
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'modeloOnt',
                                                            name:           'modeloOnt',
                                                            fieldLabel:     'Modelo',
                                                            displayField:   "",
                                                            value:          data.modeloOnt,
                                                            readOnly:       true,
                                                            width:          '30%'
                                                        },                                                                                                                
                                                        {
                                                            queryMode:      'local',
                                                            xtype:          'combobox',
                                                            id:             'puertosOnt',
                                                            name:           'puertosOnt',
                                                            fieldLabel:     'Puertos',
                                                            displayField:   'idInterface',
                                                            value:          '-Seleccione-',
                                                            valueField:     'nombreInterface',                                                            
                                                            store:          storeInterfacesPorEstadoYElemento,
                                                            width:          '30%'
                                                        },                                                                                                         
                                                        { width: '20%', border: false},
                                                        //---------------------------------------

                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        },
                                        //Bloque Sw Poe del Datos Safecity
                                        {
                                            id: 'SwPoeDatosSafecity',
                                            xtype: 'fieldset',
                                            title: 'Switch PoE',
                                            defaultType: 'textfield',
                                            hidden: true,
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 3,
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'nombreSwPoeGpon',
                                                            name:           'nombreSwPoeGpon',
                                                            fieldLabel:     'Nombre',
                                                            displayField:   "",
                                                            value:          data.nombreSwPoeGpon,
                                                            readOnly:       true,
                                                            width:          '40%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'marcaSwPoeGpon',
                                                            name:           'marcaSwPoeGpon',
                                                            fieldLabel:     'Marca',
                                                            displayField:   "",
                                                            value:          data.marcaSwPoeGpon,
                                                            readOnly:       true,
                                                            width:          '40%'
                                                        },
                                                        { width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'serieSwPoeGpon',
                                                            name:           'serieSwPoeGpon',
                                                            fieldLabel:     'Serie',
                                                            displayField:   "",
                                                            value:          data.serieSwPoeGpon,
                                                            readOnly:       true,
                                                            width:          '30%'
                                                        },   
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'modeloSwPoeGpon',
                                                            name:           'modeloSwPoeGpon',
                                                            fieldLabel:     'Modelo',
                                                            displayField:   "",
                                                            value:          data.modeloSwPoeGpon,
                                                            readOnly:       true,
                                                            width:          '30%'
                                                        },                                                                                                                
                                                        {
                                                            queryMode:      'local',
                                                            xtype:          'combobox',
                                                            id:             'puertosSwPoe',
                                                            name:           'puertosSwPoe',
                                                            fieldLabel:     'Puertos',
                                                            displayField:   'idInterface',
                                                            value:          '-Seleccione-',
                                                            valueField:     'nombreInterface',
                                                            store:          storeInterfacesPorEstadoYElementoSwPoe,
                                                            width:          '30%'
                                                        },                                                                                                         
                                                        { width: '20%', border: false},
                                                        //---------------------------------------

                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        },
                                        //Bloque Elemento
                                        {
                                            id: 'ElementoReqSafecity',
                                            xtype: 'fieldset',
                                            title: 'Elemento',
                                            defaultType: 'textfield',
                                            hidden: true,
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 3,
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'nombreEleReqGpon',
                                                            name:           'nombreEleReqGpon',
                                                            fieldLabel:     'Nombre',
                                                            displayField:   "",
                                                            value:          data.nombreElementoReqGpon,
                                                            readOnly:       true,
                                                            width:          '40%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'marcaEleReqGpon',
                                                            name:           'marcaEleReqGpon',
                                                            fieldLabel:     'Marca',
                                                            displayField:   "",
                                                            value:          data.marcaElementoReqGpon,
                                                            readOnly:       true,
                                                            width:          '40%'
                                                        },
                                                        { width: '20%', border: false},
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'serieEleReqGpon',
                                                            name:           'serieEleReqGpon',
                                                            fieldLabel:     'Serie',
                                                            displayField:   "",
                                                            value:          data.serieElementoReqGpon,
                                                            readOnly:       true,
                                                            width:          '30%'
                                                        },   
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'modeloEleReqGpon',
                                                            name:           'modeloEleReqGpon',
                                                            fieldLabel:     'Modelo',
                                                            displayField:   "",
                                                            value:          data.modeloElementoReqGpon,
                                                            readOnly:       true,
                                                            width:          '30%'
                                                        },                                                                                                                
                                                        {
                                                            queryMode:      'local',
                                                            xtype:          'combobox',
                                                            id:             'puertosEleReqGpon',
                                                            name:           'puertosEleReqGpon',
                                                            fieldLabel:     'Puertos',
                                                            displayField:   'idInterface',
                                                            value:          '-Seleccione-',
                                                            valueField:     'nombreInterface',
                                                            store:          storeInterfacesPorEstadoYEleReqGpon,
                                                            width:          '30%'
                                                        },                                                                                                         
                                                        { width: '20%', border: false},
                                                        //---------------------------------------

                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        },
                                        //nueva CAMARA
                                        {
                                            id: 'nuevoCamara',
                                            xtype: 'fieldset',
                                            title: 'Camara',
                                            defaultType: 'textfield',
                                            hidden: false,
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 3,
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        //---------------------------------------
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'serieNuevoCamara',
                                                            name:           'serieNuevoCamara',
                                                            fieldLabel:     'Serie:',
                                                            displayField:   booleanTieneElementoCliente ? data.serieElementoCliente : "",
                                                            value:          booleanTieneElementoCliente ? data.serieElementoCliente : "",
                                                            width:          '25%',
                                                            disabled:       booleanTieneElementoCliente,
                                                            listeners: {
                                                                blur: function(serie){
                                                                    Ext.Ajax.request({
                                                                        url: buscarCpeNaf,
                                                                        method: 'post',
                                                                        params: { 
                                                                            serieCpe:          serie.getValue(),
                                                                            modeloElemento:    '',
                                                                            estado:            'PI',
                                                                            bandera:           'ActivarServicio',
                                                                            comprobarInterfaz: 'SI',
                                                                            tipoElemento:      '',
                                                                            idServicio:        data.idServicio
                                                                        },
                                                                        success: function(response){                                                                                
                                                                            var respuesta     = response.responseText.split("|");
                                                                            var status        = respuesta[0];
                                                                            var mensaje       = respuesta[1].split(",");
                                                                            strEsCpeExistente = respuesta[2];
                                                                            var macCpe      = mensaje[1];
                                                                            var modeloCamara   = mensaje[2];
                                                                            $.ajax({
                                                                                type:"GET",
                                                                                async: false,
                                                                                data:{
                                                                                    modeloElemento: modeloCamara,
                                                                                },
                                                                                url:getResolucionAlternaCamara,
                                                                                success: function (parametros){
                                                                                //Selecionamos por defecto el valor corespondiente
                                                                                Ext.getCmp('formatoResCamara').setValue = parametros.resolucionCam;
                                                                                Ext.getCmp('formatoResCamara').setRawValue(parametros.resolucionCam);

                                                                                Ext.getCmp('resolucionCamara').setValue = parametros.resolucion;
                                                                                Ext.getCmp('resolucionCamara').setRawValue(parametros.resolucion);

                                                                                Ext.getCmp('tipoCamara').setValue = parametros.tipoCamara;
                                                                                Ext.getCmp('tipoCamara').setRawValue(parametros.tipoCamara);

                                                                                parametros.length==0&&Ext.Msg.alert('Atencion','El modelo de camara seleccionado no cuenta con las caracteristicas necesarias');
                                                                                },
                                                                                
                                                                                
                                                                            });

                                                                            Ext.getCmp('modeloCamara').setValue = '';
                                                                            Ext.getCmp('modeloCamara').setRawValue('');

                                                                            Ext.getCmp('macCamara').setValue = '';
                                                                            Ext.getCmp('macCamara').setRawValue('');

                                                                            if(status=="OK")
                                                                            {
                                                                                Ext.getCmp('modeloCamara').setValue = modeloCamara;
                                                                                Ext.getCmp('modeloCamara').setRawValue(modeloCamara);

                                                                                Ext.getCmp('macCamara').setValue = macCpe;
                                                                                Ext.getCmp('macCamara').setRawValue(macCpe);
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
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'nombreNuevoCamara',
                                                            name:           'nombreNuevoCamara',
                                                            fieldLabel:     'Nombre Camara',
                                                            disabled:       booleanTieneElementoCliente,
                                                            displayField:   booleanTieneElementoCliente ? data.nombreElementoCliente : "",
                                                            value:          booleanTieneElementoCliente ? data.nombreElementoCliente : "",
                                                            width:          '25%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'modeloCamara',
                                                            name:           'modeloCamara',
                                                            fieldLabel:     'Modelo',
                                                            disabled:       booleanTieneElementoCliente,
                                                            displayField:   booleanTieneElementoCliente ? data.modeloElementoCliente : "",
                                                            value:          booleanTieneElementoCliente ? data.modeloElementoCliente : "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'macCamara',
                                                            name:           'macCamara',
                                                            fieldLabel:     'Mac',
                                                            disabled:       booleanTieneElementoCliente,
                                                            displayField:   booleanTieneElementoCliente ? data.macElementoCliente : "",
                                                            value:          booleanTieneElementoCliente ? data.macElementoCliente : "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },
                                                        {
                                                            //Este componente no es visible, sin embargo guarda un valor importante
                                                            // para los parametros de activacion
                                                            xtype:          'textfield',
                                                            id:             'resolucionCamara',
                                                            name:           'resolucionCamara',
                                                            disabled:       booleanTieneElementoCliente,
                                                            displayField:   "",
                                                            value:          "",
                                                            hidden:         true,
                                                            readOnly:       true,
                                                         },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'formatoResCamara',
                                                            name:           'formatoResCamara',
                                                            fieldLabel:     'Resolucion',
                                                            disabled:       booleanTieneElementoCliente,
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'tipoCamara',
                                                            name:           'tipoCamara',
                                                            fieldLabel:     'Tipo Camara',
                                                            disabled:       booleanTieneElementoCliente,
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        }, 
                                                        //Se desabilita este combobox por que ahora se seleciona automaticamente, segun el modelo
                                                        //de la camara        
                                                        // {
                                                        //     queryMode:      'local',
                                                        //     xtype:          'combobox',
                                                        //     id:             'resolucionCamara',
                                                        //     name:           'resolucionCamara',
                                                        //     fieldLabel:     'Resolucion',
                                                        //     displayField:   'resCamText',
                                                        //     value:          '-Seleccione-',
                                                        //     valueField:     'resCamText',                                                                
                                                        //     store:           storeResolucionCamara,
                                                        //     width: '20%'
                                                        // },                                                    
                                                        {
                                                            queryMode:      'local',
                                                            xtype:          'combobox',
                                                            id:             'codecCamara',
                                                            name:           'codecCamara',
                                                            fieldLabel:     'Codec',
                                                            displayField:   'codecCam',
                                                            value:          '-Seleccione-',
                                                            valueField:     'codecCam',                                                                
                                                            store:           storeCodecCamara,
                                                            hidden:          booleanTieneElementoCliente,
                                                            width: '20%'
                                                        },                                                             

                                                        {
                                                            
                                                            queryMode:      'local',
                                                            xtype:          'combobox',
                                                            id:             'fpsCamara',
                                                            name:           'fpsCamara',
                                                            fieldLabel:     'FPS',
                                                            displayField:   'fpsCam',
                                                            value:          '-Seleccione-',
                                                            valueField:     'fpsCam',                                                                
                                                            store:           storeFpsCamara,
                                                            hidden:          booleanTieneElementoCliente,
                                                            width: '20%'                                                            

                                                        }
                                                        //---------------------------------------

                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        },
                                        //nueva WIFI
                                        {
                                            id: 'nuevoWifiAP',
                                            xtype: 'fieldset',
                                            title: 'WIFI AP',
                                            defaultType: 'textfield',
                                            hidden: true,
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 3,
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        //---------------------------------------
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'serieWifi',
                                                            name:           'serieWifi',
                                                            fieldLabel:     'Serie:',
                                                            displayField:   "",
                                                            value:          "",
                                                            width:          '25%',
                                                            listeners: {
                                                                blur: function(serie){
                                                                    Ext.Ajax.request({
                                                                        url: buscarCpeNaf,
                                                                        method: 'post',
                                                                        params: {
                                                                            serieCpe:          serie.getValue(),
                                                                            modeloElemento:    '',
                                                                            estado:            'PI',
                                                                            bandera:           'ActivarServicio',
                                                                            comprobarInterfaz: 'SI',
                                                                            tipoElemento:      '',
                                                                            idServicio:        data.idServicio
                                                                        },
                                                                        success: function(response){
                                                                            var respuesta     = response.responseText.split("|");
                                                                            var status        = respuesta[0];
                                                                            var mensaje       = respuesta[1].split(",");
                                                                            strEsCpeExistente = respuesta[2];
                                                                            var macCpe      = mensaje[1];
                                                                            modeloCpe   = mensaje[2];
                                                                            serieCpe    = serie.getValue();
                                                                            Ext.getCmp('modeloWifi').setValue = '';
                                                                            Ext.getCmp('modeloWifi').setRawValue('');
                                                                            Ext.getCmp('macWifi').setValue = '';
                                                                            Ext.getCmp('macWifi').setRawValue('');
                                                                            if(status=="OK")
                                                                            {
                                                                                Ext.getCmp('modeloWifi').setValue = modeloCpe;
                                                                                Ext.getCmp('modeloWifi').setRawValue(modeloCpe);
                                                                                Ext.getCmp('macWifi').setValue = macCpe;
                                                                                Ext.getCmp('macWifi').setRawValue(macCpe);
                                                                                Ext.getCmp('comboIpControladora').reset();
                                                                                storeControladora.load({params: {modeloAp: modeloCpe,serieAp: serieCpe}});
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
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'nombreWifi',
                                                            name:           'nombreWifi',
                                                            fieldLabel:     'Nombre AP',
                                                            displayField:   "",
                                                            value:          "",
                                                            width:          '25%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'modeloWifi',
                                                            name:           'modeloWifi',
                                                            fieldLabel:     'Modelo',
                                                            displayField:   "",
                                                            value:          "",
                                                            width:          '25%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'macWifi',
                                                            name:           'macWifi',
                                                            fieldLabel:     'Mac',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '25%'
                                                        },
                                                        {
                                                            xtype:          'combobox',
                                                            id:             'comboIpControladora',
                                                            fieldLabel:     'Ip Controladora',
                                                            displayField:   'ipControladora',
                                                            value:          '-Seleccione-',
                                                            valueField:     'idIpControladora', 
                                                            typeAhead: true,
                                                            allowBlank: false,
                                                            queryMode: "local",
                                                            triggerAction: 'all', 
                                                            selectOnTab: true,
                                                            editable: false,                                                              
                                                            store:           storeControladora,
                                                            labelWidth:      88,
                                                            width: '30%'
                                                        } 
                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        },                                   
                                    ]
                                },//cierre informacion de los elementos del cliente
                                //informacion de los elementos adicioanles en cliente
                                {
                                    colspan: 2,
                                    rowspan:2,
                                    xtype: 'panel',
                                    title: 'Dispositivos en Cliente',
                                    defaults: { 
                                        height: 130
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
                                                {//informacion de elemento de cliente adicional
                                                    id    : 'nuevoDispositivosClienteSC',
                                                    layout: {type:'table',pack:'center',columns:1},
                                                    items : [getDispositivosClienteSafeCity(data)]
                                                },//cierre de informacion de elemento de cliente adiciona        
                                            ]
                                        }

                                    ]
                                },//cierre informacion de los elementos adicionales en cliente
                                //informacion de los elementos adicioanles en nodo
                                {
                                    colspan: 2,
                                    rowspan:2,
                                    xtype: 'panel',
                                    title: 'Dispositivos en Nodo',
                                    defaults: { 
                                        height: 130
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
                                                {//informacion de elemento de nodo adicional
                                                    id    : 'nuevoDispositivosNodoSC',
                                                    layout: {type:'table',pack:'center',columns:1},
                                                    items : [getDispositivosNodoSafeCity(data)]
                                                }//cierre de informacion de elemento de nodo adicional
                                            ]
                                        }

                                    ]
                                }//cierre informacion de los elementos adicionales en nodo
                            ],
                            buttons: 
                            [{
                                text: data.strMigrarSwPoe == 'S' ? 'Migrar' : 'Activar',
                                formBind: true,
                                handler: function()
                                {
                                    //datos camara safecity
                                    var nombreOnt         = Ext.getCmp('nombreOnt').getValue();
                                    var puertosOnt        = Ext.getCmp('puertosOnt').getRawValue();
                                    var nombreNuevoCamara = Ext.getCmp('nombreNuevoCamara').getValue();
                                    var serieNuevoCamara  = Ext.getCmp('serieNuevoCamara').getValue();
                                    var modeloCamara      = Ext.getCmp('modeloCamara').getValue();
                                    var macCamara         = Ext.getCmp('macCamara').getValue();
                                    var tipoCamara        = Ext.getCmp('tipoCamara').getValue();
                                    var resolucionCamara  = Ext.getCmp('resolucionCamara').getValue();
                                    var formatoResCamara  = Ext.getCmp('formatoResCamara').getValue();
                                    var codecCamara       = Ext.getCmp('codecCamara').getValue();
                                    var fpsCamara         = Ext.getCmp('fpsCamara').getValue();
                                    //datos sw poe safecity
                                    var nombreSwPoeGpon   = Ext.getCmp('nombreSwPoeGpon').getValue();
                                    var puertosSwPoe      = Ext.getCmp('puertosSwPoe').getRawValue();
                                    var nombreWifi        = Ext.getCmp('nombreWifi').getValue();
                                    var serieWifi         = Ext.getCmp('serieWifi').getValue();
                                    var modeloWifi        = Ext.getCmp('modeloWifi').getValue();
                                    var macWifi           = Ext.getCmp('macWifi').getValue();
                                    var ipControladora    = Ext.getCmp('comboIpControladora').getValue();
                                    //datos elemento requerido safecity
                                    var nombreEleReqGpon  = Ext.getCmp('nombreEleReqGpon').getValue();
                                    var puertosEleReqGpon = Ext.getCmp('puertosEleReqGpon').getRawValue();

                                    var validacion  = true;
                                    var flag        = 0;
                                    
                                    if( (strTipoElementoActivar == "ONT" && puertosOnt == "-Seleccione-") || 
                                        (strTipoElementoActivar == "SWPOE" && puertosSwPoe == "-Seleccione-") ||
                                        (strTipoElementoActivar == "ELE" && puertosEleReqGpon == "-Seleccione-") )
                                    {
                                        validacion = false;
                                        flag       = 1;
                                    }
                                    if( data.esServicioCamaraSafeCity === 'S' && (nombreNuevoCamara == "" || serieNuevoCamara == "" || modeloCamara == ""
                                        || macCamara == "" || ( !booleanTieneElementoCliente &&
                                            (resolucionCamara == "-Seleccione-" || codecCamara == "-Seleccione-"|| fpsCamara == "-Seleccione-")) 
                                        ) )
                                    {
                                        validacion = false;
                                        flag       = 1;
                                    }
                                    if( data.esServicioCamaraVpnSafeCity === 'S' &&
                                        ( nombreNuevoCamara == "" || serieNuevoCamara == "" || modeloCamara == "" || macCamara == "" ||
                                          ( !booleanTieneElementoCliente &&
                                            (resolucionCamara == "-Seleccione-" || codecCamara == "-Seleccione-"|| fpsCamara == "-Seleccione-"))
                                        )
                                    )
                                    {
                                        validacion = false;
                                        flag       = 1;
                                    }
                                    if( data.esServicioWifiSafeCity === 'S' && (nombreWifi == "" || serieWifi == "" || modeloWifi == "" || macWifi == "" || ipControladora == "-Seleccione-") )
                                    {
                                        validacion = false;
                                        flag       = 1;
                                    }

                                    var storeDispositivosNodoSafeCity  = null;
                                    var arrayDispositivosNodo  = [];
                                    var jsonDipositivosNodo    = "";
                                    var idTecnicoEncargado     = Ext.getCmp('comboFilterTecnico').getValue();
        
                                    if (typeof Ext.getCmp("gridDispositivosNodoSafeCity") !== 'undefined') {
                                        storeDispositivosNodoSafeCity = Ext.getCmp("gridDispositivosNodoSafeCity").getStore();
                                        if (storeDispositivosNodoSafeCity.data.items.length > 0) {
                                            $.each(storeDispositivosNodoSafeCity.data.items, function(i, item) {
                                                arrayDispositivosNodo.push(item.data);
                                            });
                                            jsonDipositivosNodo = Ext.JSON.encode(arrayDispositivosNodo);
                                        }
                                    }
        
                                    var storeDispositivosClienteSafeCity  = null;
                                    var arrayDispositivosCliente  = [];
                                    var jsonDipositivosCliente    = "";
        
                                    if (typeof Ext.getCmp("gridDispositivosClienteSafeCity") !== 'undefined') {
                                        storeDispositivosClienteSafeCity = Ext.getCmp("gridDispositivosClienteSafeCity").getStore();
                                        if (storeDispositivosClienteSafeCity.data.items.length > 0) {
                                            $.each(storeDispositivosClienteSafeCity.data.items, function(i, item) {
                                                arrayDispositivosCliente.push(item.data);
                                            });
                                            jsonDipositivosCliente = Ext.JSON.encode(arrayDispositivosCliente);
                                        }
                                    }

                                    if (validacion)
                                    {
                                        Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');

                                        Ext.Ajax.request({
                                            url: activarClienteBoton,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: { 
                                                idServicio:              data.idServicio,
                                                tipoEnlace:              data.tipoEnlace,
                                                interfaceElementoId:     data.interfaceElementoId,
                                                idProducto:              data.productoId,
                                                login:                   data.login,
                                                tipoRed:                 data.strTipoRed,
                                                strActivarOnt:           seActivaOnt ? "S" : "N",
                                                strExisteSwPoeGpon:      data.strExisteSwPoeGpon,

                                                //datos l3mpls
                                                loginAux:                data.loginAux,
                                                elementoPadre:           data.elementoPadre,
                                                elementoNombre:          data.elementoNombre,                                            
                                                interfaceElementoNombre: data.interfaceElementoNombre,
                                                ipServicio:              data.ipServicio,
                                                subredServicio:          data.subredServicio,
                                                gwSubredServicio:        data.gwSubredServicio,
                                                mascaraSubredServicio:   data.mascaraSubredServicio,
                                                protocolo:               data.protocolo,
                                                defaultGateway:          data.defaultGateway,
                                                asPrivado:               data.asPrivado,
                                                vrf:                     data.vrf,
                                                rdId:                    data.rdId,
                                                ultimaMilla:             data.ultimaMilla,
                                                vrfAdmin:                data.vrfAdmin,
                                                vlanAdmin:               data.vlanAdmin,

                                                //datos camara y ont 
                                                idOnt:                  idOnt,
                                                idInterfaceOnt:         data.idInterfaceOnt,
                                                nombreOnt:              nombreOnt,
                                                serieOnt:               serieOnt,
                                                macOnt:                 macOnt,
                                                modeloOnt:              modeloOnt,
                                                puertosOnt:             puertosOnt,
                                                banderaCamaraSafecity:  data.esServicioCamaraSafeCity,
                                                nombreNuevoCamara:      nombreNuevoCamara,
                                                serieNuevoCamara:       serieNuevoCamara,
                                                modeloCamara:           modeloCamara,
                                                macCamara:              macCamara,
                                                tipoCamara:             tipoCamara,
                                                resolucionCamara:       resolucionCamara,
                                                formatoResCamara:       formatoResCamara,
                                                codecCamara:            codecCamara,
                                                fpsCamara:              fpsCamara,
                                                //camara vpn
                                                banderaCamaraVpnSafecity: data.esServicioCamaraVpnSafeCity,

                                                //datos sw poe
                                                idServicioSwPoe:        data.idServicioSwPoe,
                                                idInterfaceOntSwPoe:    data.idInterfaceOntSwPoe,
                                                idSwPoe:                idSwPoeGpon,
                                                nombreSwPoe:            nombreSwPoeGpon,
                                                puertosSwPoe:           puertosSwPoe,
                                                strMigrarSwPoe:         data.strMigrarSwPoe,

                                                //datos elemento requerido gpon
                                                idServicioEleReqGpon:   data.idServicioEleReqGpon,
                                                idInterfaceOntEleReq:   data.idInterfaceOntEleReq,
                                                idElementoReqGpon:      idElementoReqGpon,
                                                nombreEleReqGpon:       nombreEleReqGpon,
                                                puertosEleReqGpon:      puertosEleReqGpon,

                                                //datos ap wifi
                                                banderaWifiSafecity:    data.esServicioWifiSafeCity,
                                                nombreWifi:             nombreWifi,
                                                serieWifi:              serieWifi,
                                                modeloWifi:             modeloWifi,
                                                macWifi:                macWifi,
                                                ipControladora:         ipControladora,
                                                //Datos para WS
                                                vlan        : data.vlan,
                                                anillo      : data.anillo,
                                                capacidad1  : data.capacidadUno,
                                                capacidad2  : data.capacidadDos,

                                                strEsRadioExistente       : strEsRadioExistente,
                                                strEsCpeExistente         : strEsCpeExistente,
                                                strEsTransceiverExistente : strEsTransceiverExistente,
                                                
                                                //Elementos adicionales safe city
                                                'jsonDipositivosNodo'       : jsonDipositivosNodo,
                                                'jsonDipositivosCliente'    : jsonDipositivosCliente,
                                                'idTecnicoEncargado'        : idTecnicoEncargado
                                            },
                                            success: function(response)
                                            {
                                                if(response.responseText === "OK")
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
                                                else if(response.responseText === "CANTIDAD CERO"){
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Mensaje ','CAMARAS Agotadas, favor revisar' );
                                                }
                                                else if(response.responseText === "NO EXISTE PRODUCTO"){
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                                }
                                                else if(response.responseText === "NO EXISTE CPE"){
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Mensaje ','No existe la CAMARA indicada, favor revisar' );
                                                }
                                                else if(response.responseText === "CPE NO ESTA EN ESTADO"){
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.Msg.alert('Mensaje ','Equipo no esta en PENDIENTE INSTALACION/RETIRADO, favor revisar' );
                                                }
                                                else{
                                                    Ext.get(formPanel.getId()).unmask();
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
                                        if (flag===1)
                                        {
                                            Ext.Msg.alert('Validacion ','Debe llenar todos los campos para Activar el Servicio');
                                        }
                                    }
                                }//handler
                            },
                            {
                                text: 'Cancelar',
                                handler: function()
                                {
                                    win.destroy();
                                }
                            }]
                    });
                        nombreCamara = "cam-" + data.loginAux;
                        if(data.esServicioCamaraVpnSafeCity === 'S')
                        {
                            nombreCamara = data.loginAux;
                        }
                        Ext.getCmp('nombreNuevoCamara').setValue = nombreCamara;
                        Ext.getCmp('nombreNuevoCamara').setRawValue(nombreCamara);
                        Ext.getCmp('nombreWifi').setValue = "ap-" + data.loginAux;
                        Ext.getCmp('nombreWifi').setRawValue("ap-" + data.loginAux);
                        storeInterfacesPorEstadoYElemento.load();
                        storeInterfacesPorEstadoYElementoSwPoe.load();
                        storeInterfacesPorEstadoYEleReqGpon.load();
                        //storeResolucionCamara.load(); //combo que utiliza esto esta deshabilitado
                        storeCodecCamara.load();
                        storeFpsCamara.load();

                        tituloPanel = 'Activar Servicio - ' + data.nombreProducto;
                        if(data.strMigrarSwPoe == 'S'){
                            tituloPanel = 'Migrar Servicio - ' + data.nombreProducto;
                        }
                        var win = Ext.create('Ext.window.Window', {
                            title: tituloPanel,
                            modal: true,
                            width: 1100,
                            closable: true,
                            layout: 'fit',
                            items: [formPanel]
                        }).show();

                        Ext.getCmp('OntDatosSafecity').setVisible(true);
                        Ext.getCmp('SwPoeDatosSafecity').setVisible(false);
                        Ext.getCmp('ElementoReqSafecity').setVisible(false);
                        if(data.esServicioCamaraSafeCity === 'S' || data.esServicioCamaraVpnSafeCity === 'S')
                        {
                            Ext.getCmp('nuevoCamara').setVisible(true);
                            Ext.getCmp('nuevoWifiAP').setVisible(false);
                        }
                        else if(data.esServicioWifiSafeCity === 'S')
                        {
                            Ext.getCmp('nuevoCamara').setVisible(false);
                            Ext.getCmp('nuevoWifiAP').setVisible(true);
                        }
                        //verificar
                        if(data.strMigrarSwPoe === "S" || (data.strExisteSwPoeGpon === "S" && data.esServicioCamaraSafeCity === 'S'
                            && data.strExisteCamaraPtzGpon === "S")
                            || (data.strExisteSwPoeGpon === "S" && data.esServicioCamaraVpnSafeCity === 'S'))
                        {
                            seActivaOnt = false;
                            strTipoElementoActivar = "SWPOE";
                            Ext.getCmp('OntDatosSafecity').setVisible(false);
                            Ext.getCmp('ElementoReqSafecity').setVisible(false);
                            Ext.getCmp('SwPoeDatosSafecity').setVisible(true);
                            Ext.getCmp('puertosOnt').setReadOnly(true);
                            Ext.getCmp('puertosEleReqGpon').setReadOnly(true);
                            Ext.getCmp('puertosSwPoe').setReadOnly(false);
                        }
                        else if(data.idElementoReqGpon != null && data.idServicioEleReqGpon != null)
                        {
                            seActivaOnt = false;
                            strTipoElementoActivar = "ELE";
                            Ext.getCmp('OntDatosSafecity').setVisible(false);
                            Ext.getCmp('SwPoeDatosSafecity').setVisible(false);
                            Ext.getCmp('ElementoReqSafecity').setVisible(true);
                            Ext.getCmp('puertosOnt').setReadOnly(true);
                            Ext.getCmp('puertosSwPoe').setReadOnly(true);
                            Ext.getCmp('puertosEleReqGpon').setReadOnly(false);
                        }
                    }
                }//cierre response
            });
    }
    else if(data.requiereSerActivarSafecity === "S")
    {
        Ext.Msg.alert('Validacion ','El servicio '+data.nombreSerRequiereSafecity+' tiene que estar en estado Activo');
    }
    else if(data.strActivarSwPoeGpon === "S")
    {
        Ext.Msg.alert('Validacion ','El servicio Switch PoE GPON tiene que estar en estado Activo');
    }
    else
    {
        Ext.Msg.alert('Validacion ','El servicio principal '+data.nombreServicioSafecity+' tiene que estar en estado Activo');
    }
}


function limpiarCamposL3mpls()
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
    
    Ext.getCmp('strEsExistente').setValue = '';
    Ext.getCmp('strEsExistente').setRawValue('');

    Ext.getCmp('intInterfaceEleClie').setValue = '';
    Ext.getCmp('intInterfaceEleClie').setRawValue('');
}

function ValidarConcentradoresInternetWifi(arraySelected, objGrid)
{
    let boolResponse = false;
    let strValidate = '';
    const objDescFact = {
        strConAdmi: 'Concentrador L3MPLS Administracion',
        strConNav: 'Concentrador L3MPLS Navegacion'
    };

    if (arraySelected.descripcionPresentaFactura === objDescFact.strConAdmi || arraySelected.descripcionPresentaFactura === objDescFact.strConNav )
    {
        strValidate = arraySelected.descripcionPresentaFactura === objDescFact.strConAdmi ? objDescFact.strConNav : objDescFact.strConAdmi;
        const objGridNew =  objGrid.getStore().data.items;
        const objGridFiltered = objGridNew.filter(function (el) {
            return el.data.descripcionPresentaFactura == strValidate && 
            el.data.estado == 'Activo' && 
            el.data.idServicioWifi == arraySelected.idServicioWifi;
        });
        boolResponse = objGridFiltered.length > 0 && typeof objGridFiltered[0].data === 'object' && objGridFiltered[0].data !== null;
    }
    return boolResponse;
}

function CargarInformacionCPE(data, modelo){
    var objeto = modelo;
    var UsaUMExistente = data.usaUltimaMillaExistente;

    Ext.MessageBox.wait("Verificando puertos disponibles...", 'Porfavor espere');

    Ext.getCmp('cmbInterfaceElemento').setRawValue("");                                                                    

    storeInterfacesCpe.proxy.extraParams = { 
                                             idElemento:objeto.idElemento,
                                             interface :"Wan",
                                             usaUMExistente : UsaUMExistente,
                                             tipoElemento: objeto.tipoElemento,
                                             tipoOrden :data.tipoOrden,
                                             idServicioCpe :objeto.servicioId,
                                             idServicio :data.idServicio
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
                    msg: `Cpe no tiene puertos
                    disponibles para activar Servicio`,                                                                                    
                    icon: Ext.MessageBox.WARNING,
                    closable: true
                });                               

                limpiarCamposL3mpls();

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
