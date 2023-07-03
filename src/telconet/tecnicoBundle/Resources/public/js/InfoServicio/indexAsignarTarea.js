/* Funcion que sirve para mostrar la pantalla de confirmacion de
 * servicio y realizar la llamada ajax para poner en Activo
 * el servicio para la empresa TTCO
 * 
 * @author      Josue Valencia <ajvalencia@telconet.ec>
 * @version     1.0     18-11-2022
 * @param int   idAccion    id de accion de la credencial
 */ 

function confirmarAsignacionTarea(data){

    Ext.MessageBox.show({
        title      : 'Mensaje',
        msg        : 'Se debe haber culminado con la gestión de pre factibilidad, y se habilitará la OS para solicitar el ingreso de recursos de red. ¿Desea Continuar?',
        closable   : false,
        multiline  : false,
        icon       : Ext.Msg.QUESTION,
        buttons    : Ext.Msg.YESNO,
        buttonText : {yes: 'Si', no: 'No'},
        fn: function (buttonValue)
        {
            if(buttonValue === 'yes')
            {
                Ext.get("grid").mask('Ejecutando proceso...');
                Ext.Ajax.request({
                    url     : urlGetAsignarTarea,
                    method  : 'post',
                    timeout : 400000,
                    params: {
                        intIdServicio: data
                    },
                    success: function(response){
                        Ext.get("grid").unmask();
                        var objData    = Ext.JSON.decode(response.responseText);
                        var strStatus  = objData.strStatus;
                        var strMensaje = objData.strMensaje;
                        if(strStatus === "OK") {
                            store.load();
                            Ext.Msg.alert('Mensaje','Se ejecutó el proceso correctamente!');
                        } else {
                            Ext.Msg.alert('Error ',strMensaje );
                        }
                    },
                    failure: function(result)
                    {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        }
    });
}

/**
 * Función que sirve para confirmar los servicios de Clear a Channel Punto a Punto de TN
 *
 * @author Josue Valencia <ajvalencia@telconet.ec>
 * @version 1.0 27-11-2022
 * @param data
 * @param idAccion
 * */
 function confirmarServicioClearChannelPaP(data, idAccion)
 {
     var idOnt = null;
     if(data.idOnt != "" && data.idOnt != null)
     {
         idOnt = data.idOnt;
     }
     
   

    var boolBackUpClearChannel = (data.tipoEnlace==='BACKUP'?true:false); 
    var boolPrincipalClearChannel = (data.tipoEnlace==='PRINCIPAL'?true:false); 
    var boolBackUpUM = (data.strTipoModeloBackUp ==='Última Milla'?false:true);
    var boolbanderaClienteInterface=false;


    if(boolBackUpClearChannel)
    {
      
            Ext.Ajax.request({
                url: buscarSerieEquipo,
                method: 'post',
                params: {
                    idServicio:data.idServicio,
                    modelo:"PI",
                    bandera:"ActivarServicio",
                    idProducto: data.productoId,
                },
                success: function(response){
                    var respuesta     = response.responseText.split("|");
                    
                    var status        = respuesta[0];
                    var mensaje       = respuesta[1].split(",");
                    var serie       = mensaje[0];
                    var strInterface       = mensaje[1];
                    var descripcion = mensaje[2];
                    var macCpe      = mensaje[3];
                    var modeloCpe   = mensaje[4];
                    var propiedad  = mensaje[5];
                  

                    Ext.getCmp('descEquipo').setValue = '';
                    Ext.getCmp('descEquipo').setRawValue('');

                    Ext.getCmp('modeloEquipo').setValue = '';
                    Ext.getCmp('modeloEquipo').setRawValue('');

                    Ext.getCmp('macEquipo').setValue = '';
                    Ext.getCmp('macEquipo').setRawValue('');

                    Ext.getCmp('serieEquipo').setValue = "";
                    Ext.getCmp('serieEquipo').setRawValue("");
                    Ext.getCmp('serieEquipo').setDisabled(false);
                    Ext.getCmp('propiedadEquipo').setDisabled(false);
                    Ext.getCmp('interface').setVisible(true);
                    Ext.getCmp('interfaceCRegistrada').setVisible(false);
                    
                 
                    if(status=="OK")
                    {
                        Ext.getCmp('serieEquipo').setValue = serie;
                        Ext.getCmp('serieEquipo').setRawValue(serie);

                        Ext.getCmp('propiedadEquipoRegistrada').setValue = propiedad;
                        Ext.getCmp('propiedadEquipoRegistrada').setRawValue(propiedad);

                        if(propiedad=="TELCONET")
                        {
                            Ext.getCmp('interfaceCRegistrada').setVisible(false);
                            Ext.getCmp('interface').setVisible(true);
                            storeInterface.proxy.extraParams = {
                                serieCpe: serie,
                            };
                            storeInterface.load();
                        }
                        else
                        {
                            Ext.getCmp('interface').setVisible(false);

                            Ext.getCmp('interfaceCRegistrada').setVisible(true);
                            Ext.getCmp('interfaceCRegistrada').setValue = strInterface;
                            Ext.getCmp('interfaceCRegistrada').setRawValue(strInterface);
    
                        }
                        Ext.getCmp('serieEquipo').setDisabled(true);
                        Ext.getCmp('propiedadEquipo').setDisabled(true);
                       
                        Ext.getCmp('descEquipo').setValue = descripcion;
                        Ext.getCmp('descEquipo').setRawValue(descripcion);

                        Ext.getCmp('modeloEquipo').setValue = modeloCpe;
                        Ext.getCmp('modeloEquipo').setRawValue(modeloCpe);

                        Ext.getCmp('macEquipo').setValue = macCpe;
                        Ext.getCmp('macEquipo').setRawValue(macCpe);
                     
                    }
                    else
                    {
                        Ext.Msg.alert('Mensaje ', mensaje);
                    }
                  

                },
                failure: function(result)
                {
                    Ext.get(panelSeguridadLogica.getId()).unmask();
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            });
        
    }
 
    var storeInterface = new Ext.data.Store({  
        pageSize: 100,
        proxy: {
            type: 'ajax',
            url : buscarInterfaceporserie,
            extraParams: { 
                serieCpe:  '',
            },
            reader: {
                type: 'json',
                root: 'encontrados'
            },
        listeners: {
            exception: function(proxy, response, operation, eOpts) {
                if(Ext.getCmp('modeloEquipo').getValue()!="")
                {
                    Ext.get(panelSeguridadLogica.getId()).unmask();
                    Ext.Msg.alert('Error ','Error:<br>No existen interfaces para el modelo de la serie o el equipo no es el correcto');
                    Ext.getCmp('descEquipo').setValue = '';
                    Ext.getCmp('descEquipo').setRawValue('');

                    Ext.getCmp('modeloEquipo').setValue = '';
                    Ext.getCmp('modeloEquipo').setRawValue('');

                    Ext.getCmp('macEquipo').setValue = '';
                    Ext.getCmp('macEquipo').setRawValue('');

                    Ext.getCmp('serieEquipo').setValue = "";
                    Ext.getCmp('serieEquipo').setRawValue("");
                }
                
            }
        }
        },
        fields:
        [
            {name: 'id', mapping:  'id'},
            {name: 'valor', mapping: 'valor'}
        ]
    });

     var storeInterfacesPorEstadoYElementoOnt = new Ext.data.Store({
             pageSize: 100,
             proxy: {
                 type: 'ajax',
                 url : getInterfacesPoEstadoYElemento,
                 extraParams: {
                     estadoInterface: "connected",
                     elementoId:       idOnt
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
     var panelSeguridadLogica = Ext.create('Ext.form.Panel', {
                 bodyPadding: 1,
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
                     columns: 1
                 },
                 defaults: {
                     // applied to each contained panel
                     bodyStyle: 'padding:20px'
                 },
                 items: [
                     //informacion del servicio/producto
                     {
                         xtype: 'fieldset',
                         title: 'Datos del Servicio',
                         defaultType: 'textfield',
                         defaults: {
                             width: 300,
                             height: 150
                         },
                         items: [
                             {
                                 xtype: 'container',
                                 layout: {
                                     type: 'table',
                                     columns: 1,
                                     align: 'stretch'
                                 },
                                 items: [
                                     // Campo Servicio
                                     { width: '10%', border: false},
                                     {
                                         xtype: 'textfield',
                                         id: 'servicioSeguridad',
                                         name: 'servicioSeguridad',
                                         fieldLabel: 'Servicio',
                                         value: data.login,
                                         readOnly: true
                                     },
                                     // Fin Campo Servicio
                                     //-------------------------------
                                     // Campo Estado
                                     { width: '10%', border: false},
                                     {
                                         xtype: 'textfield',
                                         id: 'estadoSeguridad',
                                         name: 'estadoSeguridad',
                                         fieldLabel: 'Estado',
                                         value: data.estado,
                                         readOnly: true
                                     },
                                     // Fin Campo Estado
                                     //-------------------------------
                                     // Campo Producto
                                     { width: '10%', border: false},
                                     {
                                         xtype: 'textfield',
                                         id: 'productoSeguridad',
                                         name: 'productoSeguridad',
                                         width: 300,
                                         fieldLabel: 'Producto',
                                         value: data.nombreProducto,
                                         readOnly: true
                                     },
                                     // Fin Campo Producto
                                     //-------------------------------
                                     // Campo Observación
                                     { width: '10%', border: false},
                                     {
                                         id : 'observacionSeguridad',
                                         xtype: 'textarea',
                                         name: 'observacionSeguridad',
                                         width: 300,
                                         fieldLabel: '* Observación',
                                         value: "",
                                         readOnly: false,
                                         required : true,
                                     }
                                     // Fin Campo Observación
                                 ]
                             }
 
                         ]
                     },//cierre de la informacion servicio/producto
                     {
                         xtype: 'fieldset',
                         title: 'Ont',
                         hidden: data.esServicioRequeridoSafeCity != "S",
                         defaultType: 'textfield',
                         defaults: {
                             width: 300,
                             height: 80
                         },
                         items: [
                             {
                                 xtype: 'container',
                                 layout: {
                                     type: 'table',
                                     columns: 1,
                                     align: 'stretch'
                                 },
                                 items: [
                                     // Campo Ont
                                     { width: '10%', border: false},
                                     {
                                         xtype:          'textfield',
                                         id:             'nombreOnt',
                                         name:           'nombreOnt',
                                         fieldLabel:     'Nombre',
                                         displayField:   "",
                                         value:          data.nombreOnt,
                                         readOnly:       true,
                                         width:          300
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
                                         width:          300
                                     },
                                     { width: '20%', border: false},
                                     {
                                         queryMode:      'local',
                                         xtype:          'combobox',
                                         id:             'puertosOnt',
                                         name:           'puertosOnt',
                                         fieldLabel:     '* Puertos',
                                         editable: false,
                                         displayField:   'idInterface',
                                         value:          '-Seleccione-',
                                         valueField:     'nombreInterface',
                                         store:          storeInterfacesPorEstadoYElementoOnt,
                                         required:       data.esServicioRequeridoSafeCity === "S",
                                         width:          300
                                     }
                                 ]
                             }
 
                         ]
                     },//cierre de la informacion ont
                      //Datos del Equipo
                     {
                        xtype: 'fieldset',
                        title: 'Datos del Equipo',
                        defaultType: 'textfield',
                        defaults: {
                            width: 260,
                            height: 195
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    align: 'stretch'
                                },
                                items: [
                                    // Campo Servicio
                                    
                                    { width: '10%', border: false},
                                    {
                                        hidden: !boolBackUpClearChannel,
                                        xtype: 'textfield',
                                        id: 'propiedadEquipoRegistrada',
                                        name: 'propiedadEquipoRegistrada',
                                        width: 300,
                                        fieldLabel: 'Propiedad equipo',
                                        value: '',
                                        readOnly: true,
                                        maxLength: 100
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: 'Propiedad equipo',
                                        id: 'propiedadEquipo',
                                        hidden: boolBackUpClearChannel,
                                        editable: false,
                                        value: 'T',
                                        store: [
                                            ['T', 'TELCONET'],
                                            ['C', 'CLIENTE']
                                        ],
                                        width: 300,
                                        listeners: {
                                            select: function(combo)
                                            {
                                                Ext.getCmp('serieEquipo').setValue = "";
                                                Ext.getCmp('descEquipo').setValue = "";
                                                Ext.getCmp('modeloEquipo').setValue = "";
                                                Ext.getCmp('macEquipo').setValue = "";
                                                Ext.getCmp('ipEquipo').setValue = "";
                                                Ext.getCmp('serieEquipo').setRawValue("");
                                                Ext.getCmp('descEquipo').setRawValue("");
                                                Ext.getCmp('modeloEquipo').setRawValue("");
                                                Ext.getCmp('macEquipo').setRawValue("");
                                                Ext.getCmp('ipEquipo').setRawValue("");
                                                Ext.getCmp('interfaceCliente').setVisible(true);
                                                
                                                if(combo.getValue() === "C")
                                                {
                                                    Ext.getCmp('descEquipo').setReadOnly(false);
                                                    Ext.getCmp('modeloEquipo').setReadOnly(false);
                                                    Ext.getCmp('macEquipo').setReadOnly(false);
                                                    Ext.getCmp('interface').setVisible(false);
                                                    Ext.getCmp('interfaceCliente').setVisible(true);
                                                    boolbanderaClienteInterface=true;
                                                    return;
                                                }
                                                else
                                                {
                                                    Ext.getCmp('descEquipo').setReadOnly(true);
                                                    Ext.getCmp('modeloEquipo').setReadOnly(true);
                                                    Ext.getCmp('macEquipo').setReadOnly(true);
                                                    Ext.getCmp('interface').setVisible(true);
                                                    Ext.getCmp('interfaceCliente').setVisible(false);
                                                    boolbanderaClienteInterface=false;
                                                    return;
                                                }
                                            }
                                        }
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'serieEquipo',
                                        width: 300,
                                        id: 'serieEquipo',
                                        fieldLabel: '* Serie',
                                       
                                        value: '',
                                        readOnly: false,
                                        maxLength: 40,
                                        listeners: {
                                            blur: function(serie){

                                                if(Ext.getCmp('propiedadEquipo').getRawValue() === "TELCONET"&& !boolBackUpClearChannel)
                                                {
                                                    Ext.Ajax.request({
                                                        url: buscarCpeNaf,
                                                        method: 'post',
                                                        params: {
                                                            serieCpe:          serie.getValue(),
                                                            modeloElemento:    '',
                                                            estado:            'PI',
                                                            bandera:           'ActivarServicio',
                                                            //comprobarInterfaz: 'SI',
                                                            //tipoElemento:      'CPE',
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

                                                            Ext.getCmp('descEquipo').setValue = '';
                                                            Ext.getCmp('descEquipo').setRawValue('');

                                                            Ext.getCmp('modeloEquipo').setValue = '';
                                                            Ext.getCmp('modeloEquipo').setRawValue('');

                                                            Ext.getCmp('macEquipo').setValue = '';
                                                            Ext.getCmp('macEquipo').setRawValue('');

                                                            if(status=="OK")
                                                            {
                                                                storeInterface.proxy.extraParams = {
                                                                    serieCpe: serie.getValue(),
                                                                };
                                                                storeInterface.load();

                                                               
                                                                Ext.getCmp('descEquipo').setValue = descripcion;
                                                                Ext.getCmp('descEquipo').setRawValue(descripcion);

                                                                Ext.getCmp('modeloEquipo').setValue = modeloCpe;
                                                                Ext.getCmp('modeloEquipo').setRawValue(modeloCpe);

                                                                Ext.getCmp('macEquipo').setValue = macCpe;
                                                                Ext.getCmp('macEquipo').setRawValue(macCpe);

                                                         
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.get(panelSeguridadLogica.getId()).unmask();
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        }//clear
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'descEquipo',
                                        name: 'descEquipo',
                                        width: 300,
                                        fieldLabel: '* Descripción',
                                        value: '',
                                        readOnly: true,
                                        maxLength: 1500
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'modeloEquipo',
                                        name: 'modeloEquipo',
                                        width: 300,
                                        fieldLabel: '* Modelo',
                                        value: '',
                                        readOnly: true,
                                        maxLength: 100
                                    },
                                    {
                                        xtype: 'combobox',
                                        
                                        fieldLabel: '* Interface',
                                        id: 'interface',
                                        name:'interface',
                                        hidden: false,
                                        editable: false,
                                        displayField: 'valor',
                                        valueField: 'id',
                                        store: storeInterface,
                                        width: 300
                                       
                                    },
                                    { width: '10%', border: false},
                                    {
                                      
                                        xtype: 'textfield',
                                        id: 'interfaceCliente',
                                        name: 'interfaceCliente',
                                        width: 300,
                                        fieldLabel: '* Interface',
                                        value: '',
                                        maxLength: 100,
                                        hidden:true
                                    },
                                    { width: '10%', border: false},
                                    {
                                        hidden: !boolBackUpClearChannel,
                                        xtype: 'textfield',
                                        id: 'interfaceCRegistrada',
                                        name: 'interfaceCRegistrada',
                                        width: 300,
                                        fieldLabel: '* Interface',
                                        value: '',
                                        readOnly: true,
                                        maxLength: 100
                                    },

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'macEquipo',
                                        name: 'macEquipo',
                                        width: 300,
                                        fieldLabel: '* Mac',
                                        value: '',
                                        readOnly: true,
                                        maxLength: 100
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'ipEquipo',
                                        name: 'ipEquipo',
                                        width: 300,
                                        fieldLabel: '* Ip',
                                        value: '',
                                        readOnly: false,
                                        maxLength: 16
                                    },
                                    { width: '10%', border: false},
                                    {
                                        height: 10,
                                        layout: 'form',
                                        border: false,
                                        items:
                                        [
                                            {
                                                xtype: 'displayfield'
                                            }
                                        ]
                                    }
                                ]
                            }

                        ]
                     },//Fin de Datos del Equipo
                     //LAN
                     {
                        xtype: 'fieldset',
                        title: 'LAN',
                        defaultType: 'textfield',
                        hidden: boolBackUpClearChannel,
                        defaults: {
                            width: 260,
                            height: 60
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 1,
                                    align: 'stretch'
                                },
                                items: [
                                    // Campo Servicio
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: 'Propiedad',
                                        id: 'propiedadLan',
                                        hidden: false,
                                        editable: false,
                                        value: 'T',
                                        store: [
                                            ['T', 'TELCONET'],
                                            ['C', 'CLIENTE']
                                        ],
                                        width: 300,
                                        listeners: {
                                            select: function(combo)
                                            {
                                                Ext.getCmp('subred').setValue = "";
                                                Ext.getCmp('subred').setRawValue("");

                                            }
                                        }
                                    },
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'subred',
                                        width: 300,
                                        id: 'subred',
                                        fieldLabel: '* Subred',
                                        value: '',
                                        readOnly: false,
                                        maxLength: 40,
                                        listeners: {
                                            blur: function(subred){
                                                var strsubred= subred.getValue();
                                                if(Ext.getCmp('propiedadLan').getRawValue() === "TELCONET"&&strsubred!="")
                                                {
                                                    Ext.get(panelSeguridadLogica.getId()).mask('Validando subred...');
                                                  
                                                    Ext.Ajax.request({
                                                        url: buscarCSubred,
                                                        method: 'post',
                                                        params: {
                                                            subred:            strsubred,
                                                            tipo:              'LAN',
                                                            uso:               'CLEAR CHANNEL',
                                                            idServicio:         data.idServicio
                                                        },
                                                        success: function(response){
                                                            var respuesta     = response.responseText.split("|");                                                            
                                                            var status        = respuesta[0];
                                                            var mensaje       = respuesta[1].split(",");
                                                            var id_subred = mensaje[0];


                                                            if(status=="OK")
                                                            {
                                                                data.id_subred=id_subred
                                                                
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('subred').setValue = '';
                                                                Ext.getCmp('subred').setRawValue('');
                                                            }
                                                            Ext.get(panelSeguridadLogica.getId()).unmask();
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.get(panelSeguridadLogica.getId()).unmask();
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    } 
                                ]
                            }

                        ]
                     },//Fin LAN

                     { 
                        xtype: 'fieldset', 
                        title: 'Login Monitoreo', 
                        defaultType: 'textfield', 
                        hidden: boolBackUpUM, 
                        defaults: { 
                            width: 260, 
                            height: 70 
                        }, 
                        items: [ 
                            { 
                                xtype: 'container', 
                                layout: { 
                                    type: 'table', 
                                    columns: 1, 
                                    align: 'stretch' 
                                }, 
                                items: [ 
                                    // Campo Servicio 
                                    { width: '10%', border: false}, 
                                    { 
                                        xtype: 'textfield', 
                                        name: 'loginMonitoreoUno', 
                                        width: 300, 
                                        id: 'loginMonitoreoUno', 
                                        fieldLabel: '*Login Monitoreo /1', 
                                        value: '', 
                                        readOnly: false, 
                                        maxLength: 40 ,
                                        listeners: {
                                            blur: function(loginMonitoreo1){
                                                
                                                if(Ext.getCmp('propiedadEquipo').getRawValue() === "TELCONET")
                                                {
                                                    Ext.get(panelSeguridadLogica.getId()).mask('Validando Login de monitoreo 1...');
                                                    var strloginMonitoreo1= loginMonitoreo1.getValue();
                                                    Ext.Ajax.request({
                                                        url: buscarLoginMonitoreo,
                                                        method: 'post',
                                                        params: {
                                                            loginMonitoreo:   strloginMonitoreo1,
                                                            idServicio:         data.idServicio
                                                        },
                                                        success: function(response){
                                                            var respuesta     = response.responseText.split("|");                                                            
                                                            var status        = respuesta[0];
                                                            var mensaje       = respuesta[1].split(",");
                                                            var respuestaLogin1 = mensaje[1];


                                                            if(status=="OK")
                                                            {
                                                                data.respuestaLogin1=respuestaLogin1;
                                                                
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('loginMonitoreoUno').setValue = '';
                                                                Ext.getCmp('loginMonitoreoUno').setRawValue('');
                                                            }
                                                            Ext.get(panelSeguridadLogica.getId()).unmask();
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.get(panelSeguridadLogica.getId()).unmask();
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    }, 
                                    { width: '10%', border: false}, 
                                    { 
                                        xtype: 'textfield', 
                                        name: 'loginMonitoreoDos', 
                                        width: 300, 
                                        id: 'loginMonitoreoDos', 
                                        fieldLabel: '*Login Monitoreo /2', 
                                        value: '', 
                                        readOnly: false, 
                                        maxLength: 40 ,
                                        listeners: {
                                            blur: function(loginMonitoreo2){
                                                
                                                if(Ext.getCmp('propiedadEquipo').getRawValue() === "TELCONET")
                                                {
                                                    Ext.get(panelSeguridadLogica.getId()).mask('Validando Login de monitoreo 2...');
                                                    var strloginMonitoreo2= loginMonitoreo2.getValue();
                                                    Ext.Ajax.request({
                                                        url: buscarLoginMonitoreo,
                                                        method: 'post',
                                                        params: {
                                                            loginMonitoreo:   strloginMonitoreo2,
                                                            idServicio:         data.idServicio
                                                        },
                                                        success: function(response){
                                                            var respuesta     = response.responseText.split("|");                                                            
                                                            var status        = respuesta[0];
                                                            var mensaje       = respuesta[1].split(",");
                                                            var respuestaLogin2 = mensaje[1];


                                                            if(status=="OK")
                                                            {
                                                                data.respuestaLogin2=respuestaLogin2;
                                                                
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Mensaje ', mensaje);
                                                                Ext.getCmp('loginMonitoreoDos').setValue = '';
                                                                Ext.getCmp('loginMonitoreoDos').setRawValue('');
                                                            }
                                                            Ext.get(panelSeguridadLogica.getId()).unmask();
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.get(panelSeguridadLogica.getId()).unmask();
                                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                        }
                                                    });
                                                }
                                            }
                                        }
                                    }, 
                                    { width: '10%', border: false},  
                                ] 
                            } 
 
                        ] 
                     },//Fin Login Monitoreo

                     {
                        xtype: 'label',
                        forId: 'labelCamposObligatorios',
                        text: '* Campos obligatorios'
                    }

                 ],
                 buttons: [{
                     text: 'Confirmar',
                     formBind: true,
                     handler: function()
                     {
 
                         var observacion          = Ext.getCmp('observacionSeguridad').getValue();
                         var strSerieEquipo       = Ext.getCmp('serieEquipo').getValue()
                         var strDescripcionEquipo = Ext.getCmp('descEquipo').getValue();
                         var strModeloEquipo      = Ext.getCmp('modeloEquipo').getValue();
                         var strMacEquipo         = Ext.getCmp('macEquipo').getValue();
                         var ipEquipo             = Ext.getCmp('ipEquipo').getValue();
                         var strInterface         = "";
                         var strSubred            = Ext.getCmp('subred').getValue();
                         var strloginUno          = Ext.getCmp('loginMonitoreoUno').getValue();
                         var strloginDos          = Ext.getCmp('loginMonitoreoDos').getValue();
                         var strPropiedadEquipo   = Ext.getCmp('propiedadEquipo').getValue();
                         var validacion=true;
                         var ms_error="";
                         if(boolbanderaClienteInterface)
                         {
                            strInterface=Ext.getCmp('interfaceCliente').getValue()
                         }
                         else 
                         {
                            if(!boolBackUpClearChannel)
                            {
                                strInterface=Ext.getCmp('interface').getValue()
                            }
                            else
                            {
                                if(Ext.getCmp('propiedadEquipoRegistrada').setValue=='TELCONET')
                                {
                                    strInterface=Ext.getCmp('interface').getValue()==null?"":Ext.getCmp('interface').getValue();
                                }
                                else
                                {
                                    strInterface=Ext.getCmp('interfaceCRegistrada').getValue();
                                }
                                
                            }
                         }
                         if(observacion == "" || strDescripcionEquipo == "" || strModeloEquipo == "" || strMacEquipo == "" || strSerieEquipo == ""
                            || ipEquipo == "" || strInterface == ""  )
                         {
                           if(Ext.getCmp('propiedadEquipo').getRawValue() === "TELCONET")
                           {
                            validacion=false;
                            ms_error="Debe llenar todos los campos obligatorios";
                           }
                           
                          
                         }
                        
                         if(!boolBackUpUM){
                             if(strloginUno== "" ||strloginDos== ""){
                                validacion=false;
                                ms_error="Debe llenar todos los campos obligatorios";
                             }

                         }
                         if(!boolBackUpClearChannel&& strSubred == "")
                         {
                           
                            validacion=false;
                            ms_error="Debe llenar todos los campos obligatorios";
                            
                         }

                           // Patron para validar la ip

                         const patronIp=new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/gm);

                         if (ipEquipo.search(patronIp)!=0&&ipEquipo != "") {

                            // Ip no es correcta
                            validacion=false;
                            ms_error=ms_error+"<br/>La ip ingresada no cumple con el formato correcto";
                         } 
                         if(Ext.getCmp('propiedadEquipo').getValue() != "T" &&strMacEquipo!="")
                         {
                             
                            
                                let regex = /^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;
                                if( !regex.test(strMacEquipo) )
                                 {
                                    validacion=false;
                                    ms_error+="<br/>'Error', 'Formato de Mac Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)'";
                                }

                         }
                         if(strSubred!="")
                         {
                             var arrayDivisionSubred=strSubred.split("/");
                             var boolValidarSubred=false;
                             if(arrayDivisionSubred.length==2)
                             {
                                if (arrayDivisionSubred[0].search(patronIp))
                                {
                                    boolValidarSubred=true;
                                } 
                                if(isNaN(arrayDivisionSubred[1]))
                                {
                                    boolValidarSubred=true;
                                }

                             }
                             else
                             {
                                boolValidarSubred=true;
                             }
                             if( boolValidarSubred)
                             {
                                validacion=false;
                                ms_error+="<br/>'Error', 'Formato de Subred Incorrecta, favor ingrese con el formato (aaaa.bbb.ccc.ddd/ee)'";
                            }
                         }
                               
                         if(boolBackUpClearChannel)
                         {
                            strPropiedadEquipo=Ext.getCmp('propiedadEquipoRegistrada').getValue()=="TELCONET"?"T":"C";
                         }

                         
                         
                         if(validacion){
 
                             Ext.get(panelSeguridadLogica.getId()).mask('Confirmando servicio...');
                             
                             Ext.Ajax.request({
                                 url: confirmarActivacionBoton,
                                 method: 'post',
                                 timeout: 400000,
                                 params: {
                                     idServicio                 : data.idServicio,
                                     idProducto                 : data.productoId,
                                     idEmpresa                  : data.idEmpresa,
                                     prefijoEmpresa             : "TN",
                                     observacionActivarServicio : observacion,
                                     serieEquipo                : strSerieEquipo,
                                     descEquipo                 : strDescripcionEquipo,
                                     modeloEquipo               : strModeloEquipo,
                                     macEquipo                  : strMacEquipo,
                                     ipEquipo                   : ipEquipo,
                                     strNombreTecnico           : data.descripcionProducto,
                                     registroEquipo             : data.registroEquipo,
                                     idAccion                   : idAccion,
                                     propiedadEquipo            : strPropiedadEquipo,
                                     propiedadLan               : Ext.getCmp('propiedadLan').getValue(),
                                     strInterface               : strInterface,
                                     strSubred                  : Ext.getCmp('subred').getValue(),
                                     idSubred                   : data.id_subred,
                                     boolBackUpUM               :boolBackUpUM,
                                     loginMonitoreo1            :data.respuestaLogin1,
                                     loginMonitoreo2            :data.respuestaLogin2,
                                     boolPrincipalClearChannel  :boolPrincipalClearChannel,
                                    boolBackUpClearChannel      :boolBackUpClearChannel,
                                    requiereTransporte          :data.strClearChannelPuntoAPuntoTransporte


                                     
                                 },
                                 success: function(response){
                                     
                                     Ext.get(panelSeguridadLogica.getId()).unmask();
                                     if(response.responseText == "OK"){
                                         win.destroy();
                                         Ext.Msg.alert('Mensaje','Se confirmó el servicio: '+data.login, function(btn){
                                             if(btn=='ok'){
                                                 store.load();
                                             }
                                         });
                                     }
                                     else{
                                         Ext.Msg.alert('Mensaje ',response.responseText);
                                     }
                                 },
                                 failure: function(result)
                                 {
                                     Ext.get(panelSeguridadLogica.getId()).unmask();
                                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                 }
                             });
                         }
                        else
                        {
                           Ext.Msg.alert("Alerta",ms_error);
                        }   
                    }
                 },{
                     text: 'Cancelar',
                     handler: function(){
                         win.destroy();
                     }
                 }]
             });
 
             var win = Ext.create('Ext.window.Window', {
                 title: 'Confirmar Servicio',
                 modal: true,
                 width: 350,
                 closable: true,
                 layout: 'fit',
                 items: [panelSeguridadLogica]
             }).show();
 
             if(data.esServicioRequeridoSafeCity === "S")
             {
                 storeInterfacesPorEstadoYElementoOnt.load();
             }
 }
