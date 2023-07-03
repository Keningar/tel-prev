
/* Funcion que sirve para cargar pantalla y llamada ajax
 * para activacion del servicio Switch PoE SafeCity
 * 
 * @author      Felix Caicedo <facaicedo@telconet.ec>
 * @version     1.0 11/10/2021
 * 
 * @param Array data        Informacion que fue cargada en el grid
 * @param       gridIndex
 */
function activarServicioSwPoeSafeCity(data, gridIndex)
{
    var esSatelital                 = false;
    var strEsCpeExistente           = "NO";
    var strEsRadioExistente         = "NO";
    var strEsTransceiverExistente   = "NO";
    var nombreElemento              = ""; 
    var idOnt                       = data.idOnt;
    var serieOnt                    = data.serieOnt;
    var macOnt                      = data.macOnt;
    var modeloOnt                   = data.modeloOnt;

    if(data.estadoDatosSafecity == "Activo")
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
                                      elementoId:      idOnt,
                                      productoId:      data.productoId
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
                                        height: 120
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
                                        height: 120
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
                                    title: 'Informacion de los Elementos del Cliente',
                                    items: [
                                        comboEmpleadoSafeCity(data),
                                        //Bloque Ont del Datos Safecity
                                        {
                                            id: 'OntDatosSafecity',
                                            xtype: 'fieldset',
                                            title: 'Ont',
                                            defaultType: 'textfield',
                                            visible: true,
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
                                        //nueva SW POE
                                        {
                                            id: 'nuevoCamara',
                                            xtype: 'fieldset',
                                            title: 'Switch PoE',
                                            defaultType: 'textfield',
                                            visible: false,
                                            items: [

                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        type: 'table',
                                                        columns: 2,
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        //---------------------------------------
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'serieElemento',
                                                            name:           'serieElemento',
                                                            fieldLabel:     'Serie:',
                                                            displayField:   "",
                                                            value:          "",
                                                            width:          '40%',
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
                                                                            var modeloCpe   = mensaje[2];

                                                                            Ext.getCmp('modeloElemento').setValue = '';
                                                                            Ext.getCmp('modeloElemento').setRawValue('');

                                                                            Ext.getCmp('macElemento').setValue = '';
                                                                            Ext.getCmp('macElemento').setRawValue('');

                                                                            if(status=="OK")
                                                                            {
                                                                                Ext.getCmp('modeloElemento').setValue = modeloCpe;
                                                                                Ext.getCmp('modeloElemento').setRawValue(modeloCpe);

                                                                                Ext.getCmp('macElemento').setValue = macCpe;
                                                                                Ext.getCmp('macElemento').setRawValue(macCpe);
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
                                                            id:             'nombreElemento',
                                                            name:           'nombreElemento',
                                                            fieldLabel:     'Nombre Switch PoE',
                                                            displayField:   "",
                                                            value:          "",
                                                            width:          '40%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'modeloElemento',
                                                            name:           'modeloElemento',
                                                            fieldLabel:     'Modelo',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '40%'
                                                        },
                                                        {
                                                            xtype:          'textfield',
                                                            id:             'macElemento',
                                                            name:           'macElemento',
                                                            fieldLabel:     'Mac',
                                                            displayField:   "",
                                                            value:          "",
                                                            readOnly:       true,
                                                            width:          '40%'
                                                        }
                                                        //---------------------------------------

                                                    ]//items container
                                                }//items panel
                                            ]//items panel
                                        }

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
                                text: 'Activar',
                                formBind: true,
                                handler: function()
                                {
                                    //datos sw poe safecity
                                    var nombreOnt           = Ext.getCmp('nombreOnt').getValue();
                                    var puertosOnt          = Ext.getCmp('puertosOnt').getRawValue();
                                    var nombreNuevoElemento = Ext.getCmp('nombreElemento').getValue();
                                    var serieElemento       = Ext.getCmp('serieElemento').getValue();
                                    var modeloElemento      = Ext.getCmp('modeloElemento').getValue();
                                    var macElemento         = Ext.getCmp('macElemento').getValue();

                                    var validacion  = true;
                                    var flag        = 0;
                                    
                                    if(serieElemento == "" || modeloElemento == "" || puertosOnt == "-Seleccione-")
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

                                                //datos sw poe y ont 
                                                idOnt:                  idOnt,
                                                nombreOnt:              nombreOnt,
                                                serieOnt:               serieOnt,
                                                macOnt:                 macOnt,
                                                modeloOnt:              modeloOnt,
                                                puertosOnt:             puertosOnt,
                                                nombreNuevoCpe:         nombreNuevoElemento,
                                                serieNuevoCpe:          serieElemento,
                                                modeloNuevoCpe:         modeloElemento,
                                                macNuevoCpe:            macElemento,

                                                //Datos para WS
                                                vlan        : data.vlan,
                                                anillo      : data.anillo,
                                                capacidad1  : data.capacidadUno,
                                                capacidad2  : data.capacidadDos,

                                                strEsRadioExistente       : strEsRadioExistente,
                                                strEsCpeExistente         : strEsCpeExistente,
                                                strEsTransceiverExistente : strEsTransceiverExistente ,
                                                
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
                        nombreElemento = "swpoe-" + data.login;
                        Ext.getCmp('nombreElemento').setValue = nombreElemento;
                        Ext.getCmp('nombreElemento').setRawValue(nombreElemento);  
                        storeInterfacesPorEstadoYElemento.load();

                        var win = Ext.create('Ext.window.Window', {
                            title: 'Activar Servicio - ' + data.nombreProducto,
                            modal: true,
                            width: 1100,
                            closable: true,
                            layout: 'fit',
                            items: [formPanel]
                        }).show();
                    }
                }//cierre response
            });
    }
    else
    {
        Ext.Msg.alert('Validacion ','El servicio principal Datos Safecity tiene que estar en estado Activo');
    }
}