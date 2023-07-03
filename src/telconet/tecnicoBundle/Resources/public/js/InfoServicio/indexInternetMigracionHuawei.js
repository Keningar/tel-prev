/* Funcion que sirve para cargar pantalla y llamada ajax
 * para activacion de puerto para empresa TTCO 
 * 
 * @author      Jesus Bozada <jbozada@telconet.ec>
 * @version     1.0     13-03-2015
 * @param Array data        Informacion que fue cargada en el grid
 * @param       gridIndex
 */
function reversarSolicitudMigracion(data, gridIndex) {
    
    Ext.Msg.confirm('Alerta', 'Se procedera a realizar el reverso de la solicitud de migración, Desea Continuar?', function(btn) {
        if (btn == 'yes') {
                Ext.get(gridServicios.getId()).mask('Procesando...');
                Ext.Ajax.request({
                url: reversarSolicitudMigracionBoton,
                method: 'post',
                timeout: 400000,
                params: {
                    idServicio: data.idServicio
                },
                success: function(response) {
                    Ext.get(gridServicios.getId()).unmask();
                    if (response.responseText == "OK") {
                        Ext.Msg.alert('Mensaje', 'Se reverso la solicitud de migracion correctamente.', function(btn) {
                            if (btn == 'ok') {
                                store.load();
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Mensaje ', 'No se reverso correctamente, favor notificar a sistemas!');
                    }
                },
                failure: function(result)
                {
                    Ext.get(gridServicios.getId()).unmask();
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
    Ext.get(gridServicios.getId()).unmask();
}

/* Funcion que sirve para mostrar la pantalla para realizar la
 * migracion a la plataforma huawei
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     08-03-2015
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.1 07-07-2019 Se eliminan validaciones innecesarias, ya que el método nunca retornará los valores comparados en dichas validaciones.
 * 
 * @param data
 */
function migracionHuawei(data){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({ 
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados;
            //-------------------------------------------------------------------------------------------
            
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

            var storeInterfacesSplitter = new Ext.data.Store({  
                pageSize: 100,
                proxy: {
                    type: 'ajax',
                    timeout: 400000,
                    url : getInterfacesPorElemento,
                    extraParams: {
                        idElemento: datos[0].idSplitter,
                        estado: 'not connect'
                    },
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    }
                },
                fields:
                    [
                      {name:'idInterface', mapping:'idInterface'},
                      {name:'nombreInterface', mapping:'nombreInterface'}
                    ]
            });

            //-------------------------------------------------------------------------------------------
            var strNombrePlanProd           = data.nombrePlan;
            var strEtiquetaPlanProd         = 'Plan';
            if(datos[0].strEsInternetLite === 'SI')
            {
                strNombrePlanProd           = data.nombreProducto;
                strEtiquetaPlanProd         = 'Producto';
            }

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
                    columns: 1
                },
                defaults: {
                    bodyStyle: 'padding:20px'
                },
                items: [
                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Informacion del Servicio',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 540,
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

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'plan',
                                        fieldLabel: strEtiquetaPlanProd,
                                        displayField: strNombrePlanProd,
                                        value: strNombrePlanProd,
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
                                        name: 'capacidadTres',
                                        fieldLabel: 'Capacidad Int/Prom Uno',
                                        displayField: data.capacidadTres,
                                        value: data.capacidadTres,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'capacidadCuatro',
                                        fieldLabel: 'Capacidad Int/Prom Dos',
                                        displayField: data.capacidadCuatro,
                                        value: data.capacidadCuatro,
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
                                        id:'perfil',
                                        name: 'perfil',
                                        fieldLabel: 'Perfil',
                                        displayField: data.perfilDslam,
                                        value: data.perfilDslam,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    { width: '10%', border: false}
                                ]
                            }

                        ]
                    },//cierre de la informacion servicio/producto

                    //informacion de backbone
                    {
                        xtype: 'fieldset',
                        title: 'Informacion de backbone',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 540,
                            height: 130
                        },
                        items: [

                            //gridInfoBackbone

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
                                        name: 'splitterElemento',
                                        fieldLabel: 'Splitter Elemento',
                                        displayField: datos[0].nombreSplitter,
                                        value: datos[0].nombreSplitter,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        queryMode: 'local',
                                        xtype: 'combobox',
                                        id: 'splitterInterfaceElemento',
                                        name: 'splitterInterfaceElemento',
                                        fieldLabel: 'Splitter Interface',
                                        displayField: 'nombreInterface',
                                        valueField:'idInterface',
                                        value: datos[0].nombrePuertoSplitter,
                                        loadingText: 'Buscando...',
                                        store: storeInterfacesSplitter,
                                        width: '25%',

                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'cajaElemento',
                                        fieldLabel: 'Caja Elemento',
                                        displayField: datos[0].nombreCaja,
                                        value: datos[0].nombreCaja,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    { width: '10%', border: false},
                                    { width: '10%', border: false}

                                ]
                            }

                        ]
                    },//cierre de info de backbone

                    //informacion de los elementos del cliente
                    {
                        xtype: 'fieldset',
                        title: 'Informacion de los Elementos del Cliente',
                        defaultType: 'textfield',
                        defaults: { 
                            width: 540
                        },
                        items: [

                            {
                                xtype: 'fieldset',
                                title: 'Informacion del ONT',
                                defaultType: 'textfield',
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
                                                xtype: 'textfield',
                                                id:'serieCpe',
                                                name: 'serieCpe',
                                                fieldLabel: 'Serie ONT',
                                                displayField: "",
                                                value: "",
                                                width: '25%'
                                            },
                                            { width: '20%', border: false},
                                            {
                                                queryMode: 'local',
                                                xtype: 'combobox',
                                                id: 'modeloCpe',
                                                name: 'modeloCpe',
                                                fieldLabel: 'Modelo ONT',
                                                displayField:'modelo',
                                                valueField: 'modelo',
                                                loadingText: 'Buscando...',
                                                store: storeModelosCpe,
                                                width: '25%',
                                                listeners: {
                                                    blur: function(combo){
                                                        Ext.Ajax.request({
                                                            url: buscarCpeHuaweiNaf,
                                                            method: 'post',
                                                            params: { 
                                                                serieCpe: Ext.getCmp('serieCpe').getValue(),
                                                                modeloElemento: combo.getValue(),
                                                                estado: 'PI',
                                                                bandera: 'ActivarServicio'
                                                            },
                                                            success: function(response){
                                                                var respuesta = response.responseText.split("|");
                                                                var status = respuesta[0];
                                                                var mensaje = respuesta[1].split(",");
                                                                var descripcion = mensaje[0];
                                                                var macOntNaf = mensaje[1];
                                                                console.log(status);
                                                                if(status=="OK")
                                                                {
                                                                    Ext.getCmp('descripcionCpe').setValue = descripcion;
                                                                    Ext.getCmp('descripcionCpe').setRawValue(descripcion);

                                                                    Ext.getCmp('macCpe').setValue = macOntNaf;
                                                                    Ext.getCmp('macCpe').setRawValue(macOntNaf);
                                                                }
                                                                else
                                                                {
                                                                    Ext.Msg.alert('Mensaje ', mensaje);
                                                                    Ext.getCmp('descripcionCpe').setValue = status;
                                                                    Ext.getCmp('descripcionCpe').setRawValue(status);

                                                                    Ext.getCmp('macCpe').setValue = status;
                                                                    Ext.getCmp('macCpe').setRawValue(status);
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
                                            { width: '10%', border: false},

                                            //---------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                id:'macCpe',
                                                name: 'macCpe',
                                                fieldLabel: 'Mac ONT',
                                                displayField: "",
                                                value: "",
                                                readOnly: true,
                                                width: '25%'
                                            },
                                            {
                                                xtype: 'hidden',
                                                id:'validacionMacOnt',
                                                name: 'validacionMacOnt',
                                                value: "",
                                                width: '20%'
                                            },
                                            {
                                                xtype: 'textfield',
                                                id:'descripcionCpe',
                                                name: 'descripcionCpe',
                                                fieldLabel: 'Descripcion ONT',
                                                displayField: "",
                                                value: "",
                                                readOnly: true,
                                                width: '25%'
                                            },
                                            { width: '10%', border: false},

                                            //---------------------------------------
                                            { width: '10%', border: false},
                                            {
                                                xtype:          'combobox',
                                                fieldLabel:     'Mantiene Equipo Wifi',
                                                id:             'mantieneEquipoWifi',
                                                value:          'NO',
                                                store: 
                                                [
                                                    ['SI', 'SI'],
                                                    ['NO', 'NO']
                                                ],
                                                width: '25%'
                                            },
                                            { width: '20%', border: false},
                                            { width: '25%', border: false},
                                            { width: '10%', border: false}

                                        ]//cierre del container table
                                    }                


                                ]//cierre del fieldset
                            }
                        ]

                    },//cierre informacion de los elementos del cliente

                ],
                buttons: [{
                    text: 'Migrar',
                    formBind: true,
                    handler: function(){
                        var modeloCpe          = Ext.getCmp('modeloCpe').getValue();
                        var serieCpe           = Ext.getCmp('serieCpe').getValue();
                        var descripcionCpe     = Ext.getCmp('descripcionCpe').getValue();
                        var macCpe             = Ext.getCmp('macCpe').getValue();
                        var mantieneEquipoWifi = Ext.getCmp('mantieneEquipoWifi').getValue();
                        
                        var interfaceSplitter =Ext.getCmp('splitterInterfaceElemento').getRawValue();

                        var validacion=false;
                        flag = 0;
                        if(serieCpe=="" || macCpe==""){
                            validacion=false;
                        }
                        else{
                            validacion=true;
                        }

                        if(descripcionCpe=="ELEMENTO ESTADO INCORRECTO" || 
                           descripcionCpe=="ELMENTO CON SALDO CERO" || 
                           descripcionCpe=="NO EXISTE ELEMENTO")
                        {
                            validacion=false;
                            flag=2;
                        }

                        if(validacion){
                            Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts!');


                            Ext.Ajax.request({
                                url: migrarServicio,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    idServicio:                     data.idServicio,
                                    idProducto:                     data.productoId,
                                    perfil:                         data.perfilDslam,
                                    login:                          data.login,
                                    capacidad1:                     data.capacidadUno,
                                    capacidad2:                     data.capacidadDos,
                                    interfaceElementoId:            data.interfaceElementoId,
                                    interfaceElementoSplitterId:    interfaceSplitter,
                                    ultimaMilla:                    data.ultimaMilla,
                                    plan:                           data.planId,
                                    serieOnt:                       serieCpe,
                                    modeloOnt:                      modeloCpe,
                                    macOnt:                         macCpe,
                                    solicitudMigracion:             data.tieneSolicitudMigracion,
                                    mantieneEquipoWifi:             mantieneEquipoWifi,
                                    esIsb:                          data.esISB
                                },
                                success: function(response){
                                    Ext.get(formPanel.getId()).unmask();
                                    var objData = Ext.JSON.decode(response.responseText);
                                    var strStatus = objData.status;
                                    var strMensaje = objData.mensaje;
                                    if (strStatus == "OK") 
                                    {
                                        Ext.Msg.alert('Mensaje', strMensaje, function (btn) {
                                            if (btn == 'ok') {
                                                win.destroy();
                                                store.load();
                                            }
                                        });
                                    }
                                    else 
                                    {
                                        Ext.Msg.alert('Mensaje ', 'Error:' + strMensaje);
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get(formPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            });

                        }
                        else{
                            if(flag==1){
                                Ext.Msg.alert("Validacion","Alguna Mac esta incorrecta, favor revisar!", function(btn){
                                        if(btn=='ok'){
                                        }
                                });
                            }
                            else if(flag==2){
                                Ext.Msg.alert("Validacion","Datos del Ont incorrectos, favor revisar!", function(btn){
                                        if(btn=='ok'){
                                        }
                                });
                            }
                            else{
                                Ext.Msg.alert("Validacion","Favor Revise los campos", function(btn){
                                        if(btn=='ok'){
                                        }
                                });
                            }

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
                title: 'Migrar Servicio',
                modal: true,
                width: 600,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();

            storeInterfacesSplitter.load({
                callback:function(){        
                    storeModelosCpe.load({

                    });
                }
            });
        }//cierre response
    });
}