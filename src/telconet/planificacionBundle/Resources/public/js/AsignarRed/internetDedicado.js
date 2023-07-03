/**
 * Funcion que sirve para mostrar la pantalla para la asignacion 
 * de recursos de red para Internet Dedicado
 * 
 * @author Franciso Adum <fadum@telconet.ec>
 * @version 1.0 15-12-2015
 * 
 * Se modifica la validacion del anillo para que siga el flujo de 
 * aprovisionamiento de ip para provincias
 *
 * @author Jonathan Montece <jmontece@telconet.ec>
 * @version 2.0 12-08-2021
 * */
function showRecursoRedInternetDedicado(data)
{
    Ext.get(grid.getId()).mask('Consultando Datos...');
    var tituloElementoConector = "Nombre Elemento Conector";
    
    var esPseudoPe = data.get('esPseudoPe');
    
    if (data.get('ultimaMilla')=="Radio")
    {
        tituloElementoConector = "Nombre Elemento Radio";
    }
        Ext.Ajax.request({ 
            url: getDatosFactibilidad,
            method: 'post',
            timeout: 400000,
            params: { 
                idServicio:  data.get('id_servicio'),
                ultimaMilla: data.get('ultimaMilla'),
                tipoSolicitud: data.get('descripcionSolicitud'),
                idSolicitud  : data.get('id_factibilidad')
            },
            success: function(response){
                Ext.get(grid.getId()).unmask();

                var json = Ext.JSON.decode(response.responseText);
                //Activar o desactivar  bandera para ejecución manual de ventana de asignación de recursos de red para provincias
                //-------------------------------------------------------------------------------------------
                if(json.anillo > 0 || json.anillo == 0 && (json.banderaVentanaManualAsignarRecursosDeRed == "N") &&  
                (json.banderaVentanaManualAsignarRecursosDeRedPorPe == "N"))
                //if(json.anillo > 0 || json.anillo == 0 )
                { 
                    if(data.get('ultimaMilla') === 'SATELITAL')
                    {
                        showRecursosRedIntMplsPseudoPe(data);
                    }
                    else
                    {
                        showRecursoRedInternetMPLS(data);
                    }
                }
                else
                {    
                    if(json.status=="OK")
                    {
                        //-------------------------------------------------------------------------------------------
                        
                        var storeHilosDisponibles = new Ext.data.Store({  
                            pageSize: 100,
                            proxy: {
                                type: 'ajax',
                                url : getHilosDisponibles,
                                extraParams: {
                                    idElemento:                 json.idElementoConector,
                                    estadoInterface:            'connected',
                                    estadoInterfaceNotConect:   'not connect',
                                    estadoInterfaceReserved:    'Factible'
                                },
                                reader: {
                                    type: 'json',
                                    root: 'encontrados'
                                }
                            },
                            fields:
                                [
                                {name:'idInterfaceElemento',      mapping:'idInterfaceElemento'},
                                {name:'idInterfaceElementoOut',   mapping:'idInterfaceElementoOut'},
                                {name:'colorHilo',                mapping:'colorHilo'},
                                {name:'numeroHilo',               mapping:'numeroHilo'},
                                {name:'numeroColorHilo',          mapping:'numeroColorHilo'}
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
                                
                                //informacion del servicio
                                {
                                    colspan: 2,
                                    rowspan:2,
                                    xtype: 'panel',
                                    title: 'Informacion del Servicio',
                                    defaults: { 
                                        height: 100
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
                                                    name: 'tipoOrden',
                                                    fieldLabel: 'Tipo Orden',
                                                    displayField: data.get("tipo_orden"),
                                                    value: data.get("tipo_orden"),
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'producto',
                                                    fieldLabel: 'Producto',
                                                    displayField: data.get("producto"),
                                                    value: data.get("producto"),
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidad1',
                                                    fieldLabel: 'Capacidad1',
                                                    displayField: json.capacidad1,
                                                    value: json.capacidad1,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidad2',
                                                    fieldLabel: 'Capacidad2',
                                                    displayField: json.capacidad2,
                                                    value: json.capacidad2,
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------
                                                
                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'tipoEnlace',
                                                    fieldLabel: 'Tipo Enlace',
                                                    displayField: data.get('tipo_enlace'),
                                                    value: data.get('tipo_enlace'),
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                { width: '15%', border: false},
                                                { width: '10%', border: false}
                                                
                                            ]
                                        }

                                    ]
                                },//cierre de informacion del servicio
                                
                                //informacion del cliente
                                {
                                    colspan: 2,
                                    rowspan:2,
                                    xtype: 'panel',
                                    title: 'Informacion del Cliente',
                                    defaults: { 
                                        height: 100
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
                                                    name: 'cliente',
                                                    fieldLabel: 'Cliente',
                                                    displayField: data.get('cliente'),
                                                    value: data.get('cliente'),
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'login',
                                                    fieldLabel: 'Login',
                                                    displayField: data.get('login2'),
                                                    value: data.get('login2'),
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'ciudad',
                                                    fieldLabel: 'Ciudad',
                                                    displayField: data.get('ciudad'),
                                                    value: data.get('ciudad'),
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'direccion',
                                                    fieldLabel: 'Direccion',
                                                    displayField: data.get('direccion'),
                                                    value: data.get('direccion'),
                                                    readOnly: true,
                                                    width: '30%'
                                                },
                                                { width: '10%', border: false},

                                                //---------------------------------------------

                                                { width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'sector',
                                                    fieldLabel: 'Sector',
                                                    displayField: data.get('nombreSector'),
                                                    value: data.get('nombreSector'),
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'esRecontratacion',
                                                    fieldLabel: 'Es Recontratacion',
                                                    displayField: data.get("esRecontratacion"),
                                                    value: data.get("esRecontratacion"),
                                                    readOnly: true,
                                                    width: '35%'
                                                },
                                                { width: '10%', border: false}
                                                
                                                //---------------------------------------------

                                            ]
                                        }

                                    ]
                                },//cierre de la informacion del cliente
                                                        
                                //informacion de los elementos del cliente
                                {
                                    colspan: 3,
                                    xtype: 'panel',
                                    title: 'Elementos dados por Factibilidad',
                                    items: [
                                                                        
                                        //informacion del elemento backbone y distribucion
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
                                                    id:             'nombreElemento',
                                                    name:           'nombreElemento',
                                                    fieldLabel:     'Nombre Elemento',
                                                    displayField:   json.nombreElemento,
                                                    value:          json.nombreElemento,
                                                    width:          '25%',
                                                    readOnly: true
                                                },
                                                { width: '20%', border: false},
                                                {
                                                    xtype:          'textfield',
                                                    id:             'nombreElementoConector',
                                                    name:           'nombreElementoConector',
                                                    fieldLabel:     tituloElementoConector,
                                                    displayField:   json.nombreElementoConector,
                                                    value:          json.nombreElementoConector,
                                                    readOnly:       true,
                                                    width:          '25%'
                                                },
                                                {
                                                    xtype:          'hidden',
                                                    id:             'idElementoPadre',
                                                    name:           'idElementoPadre',
                                                    value:          json.idElementoPadre,
                                                    width:          '20%'
                                                },
                                                {
                                                    xtype:          'textfield',
                                                    id:             'nombreElementoContenedor',
                                                    name:           'nombreElementoContenedor',
                                                    fieldLabel:     'Nombre Elemento Contenedor',
                                                    displayField:   json.nombreElementoContenedor,
                                                    value:          json.nombreElementoContenedor,
                                                    width:          '25%',
                                                    readOnly:       true
                                                },

                                                //---------------------------------------

                                                { width: '20%', border: false},
                                                {
                                                    xtype:          'textfield',
                                                    id:             'nombreInterfaceElemento',
                                                    name:           'nombreInterfaceElemento',
                                                    fieldLabel:     'Nombre Interface Elemento',
                                                    displayField:   json.nombreInterfaceElemento,
                                                    value:          json.nombreInterfaceElemento,
                                                    width:          '25%',
                                                    readOnly: true
                                                },
                                                { width: '20%', border: false},
                                                {
                                                    queryMode:      'local',
                                                    xtype:          'combobox',
                                                    id:             'hilosDisponibles',
                                                    name:           'hilosDisponibles',
                                                    fieldLabel:     'Hilos Disponibles',
                                                    displayField:   'numeroColorHilo',
                                                    valueField:     'idInterfaceElementoOut',
                                                    value:          json.numeroColorHilo,
                                                    loadingText:    'Buscando...',
                                                    store:          storeHilosDisponibles,
                                                    readOnly:       true,
                                                    width: '25%',
                                                    listeners: 
                                                    {   
                                                        select: function(combo)
                                                        {
                                                            var objeto = combo.valueModels[0].raw;
                                                            Ext.Ajax.request
                                                            ({
                                                                url: ajaxGetPuertoSwitchByHilo,
                                                                method: 'post',
                                                                params: { idInterfaceElementoConector : objeto.idInterfaceElemento },
                                                                success: function(response)
                                                                {
                                                                    var json    = Ext.JSON.decode(response.responseText);
                                                                    var objJson = json;
                                                                    Ext.getCmp('nombreInterfaceElemento').setValue = objJson.idInterfaceElemento;
                                                                    Ext.getCmp('nombreInterfaceElemento').setRawValue(objJson.nombreInterfaceElemento);
                                                                },
                                                                failure: function(result)
                                                                {
                                                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                                }
                                                            });
                                                        }
                                                    }
                                                },
                                                {   width: '20%', border: false},
                                                {
                                                    xtype:          'textfield',
                                                    id:             'vlan',
                                                    name:           'vlan',
                                                    fieldLabel:     'Vlan',
                                                    displayField:   (esPseudoPe==='S'?"":"1"),
                                                    value:          (esPseudoPe==='S'?"":"1"),
                                                    width:          '25%',
                                                    readOnly:       (esPseudoPe==='S'?false:true)
                                                },

                                                //---------------------------------------

                                                {   width: '20%', border: false},
                                                {
                                                    xtype:          'textfield',
                                                    id:             'ipPublica',
                                                    name:           'ipPublica',
                                                    fieldLabel:     'Ip Publica',
                                                    displayField:   "",
                                                    value:          "",
                                                    width:          '25%',
                                                    listeners: {
                                                        blur: function(text){
                                                            var ip = text.getValue();
                                                            if(ip.match("^([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})$"))
                                                            {
                                                                Ext.getCmp('validacionIpPublica').setValue = "correcta";
                                                                Ext.getCmp('validacionIpPublica').setRawValue("correcta");
                                                            }
                                                            else
                                                            {
                                                                Ext.getCmp('validacionIpPublica').setValue = "incorrecta";
                                                                Ext.getCmp('validacionIpPublica').setRawValue("incorrecta");
                                                                Ext.Msg.alert('Mensaje ','Formato de Ip Incorrecto (xxx.xxx.xxx.xxx), Favor Revisar!' );
                                                            }
                                                        }
                                                    }
                                                },
                                                {
                                                    width:          '20%',
                                                    border:         false
                                                },
                                                {
                                                    xtype:          'textfield',
                                                    id:             'mascaraPublica',
                                                    name:           'mascaraPublica',
                                                    fieldLabel:     'Mascara',
                                                    displayField:   "",
                                                    value:          "",
                                                    width:          '25%',
                                                    listeners: {
                                                        blur: function(text){
                                                            var mac = text.getValue();
                                                            if(mac.match("^([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})$"))
                                                            {
                                                                Ext.getCmp('validacionMascaraPublica').setValue = "correcta";
                                                                Ext.getCmp('validacionMascaraPublica').setRawValue("correcta");
                                                            }
                                                            else
                                                            {
                                                                Ext.getCmp('validacionMascaraPublica').setValue = "incorrecta";
                                                                Ext.getCmp('validacionMascaraPublica').setRawValue("incorrecta");
                                                                
                                                                Ext.Msg.alert('Mensaje ','Formato de Mascara Incorrecto (xxx.xxx.xxx.xxx), Favor Revisar!' );
                                                            }
                                                        }
                                                    }
                                                },
                                                {
                                                    xtype:          'hidden',
                                                    id:             'validacionIpPublica',
                                                    name:           'validacionIpPublica',
                                                    value:          "",
                                                    width:          '20%'
                                                },
                                                {
                                                    xtype:          'hidden',
                                                    id:             'validacionMascaraPublica',
                                                    name:           'validacionMascaraPublica',
                                                    value:          "",
                                                    width:          '20%'
                                                }
                                                //---------------------------------------

                                            ]//items container
                                        }//items panel
                                        
                                    ]

                                },//cierre informacion de los elementos del cliente
                            ],
                            buttons: 
                            [{
                                text: 'Grabar',
                                formBind: true,
                                handler: function(){
                                    var interfaceElemento   = "";
                                    var validacionIpPublica = Ext.getCmp('validacionIpPublica').getValue();
                                    var validacionMascara   = Ext.getCmp('validacionMascaraPublica').getValue();
                                    var hilosDisponibles    = Ext.getCmp('hilosDisponibles').getValue();
                                    var vlan                = Ext.getCmp('vlan').getValue();
                                    var ipPublica           = Ext.getCmp('ipPublica').getValue();
                                    var mascaraPublica      = Ext.getCmp('mascaraPublica').getValue();
                                    var idElementoPadre     = Ext.getCmp('idElementoPadre').getValue();
                                    if(!isNaN(hilosDisponibles)){
                                        interfaceElemento   = Ext.getCmp('nombreInterfaceElemento').setValue;
                                    }
                                    
                                    if(Ext.isEmpty(idElementoPadre) || idElementoPadre === 'No definido')
                                    {
                                        idElementoPadre = json.idElemento;
                                    }
                                    
                                    if(hilosDisponibles === "" || validacionIpPublica === "incorrecta" || 
                                    validacionMascara === "incorrecta" )
                                    {
                                        validacion = false;
                                    }
                                    else
                                    {
                                        validacion = true;
                                    }

                                    if(validacion)
                                    {
                                        Ext.get(formPanel.getId()).mask('Guardando datos!');                                    
                                        Ext.Ajax.request({
                                            url: asignarRecursosInternetDedicado,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: { 
                                                idServicio:             data.get('id_servicio'),
                                                idDetalleSolicitud:     data.get('id_factibilidad'),
                                                tipoSolicitud:          data.get('descripcionSolicitud'),
                                                hiloDisponible:         hilosDisponibles,
                                                vlan:                   vlan,
                                                ipPublica:              ipPublica,
                                                mascaraPublica:         mascaraPublica,
                                                idInterfaceElemento:    interfaceElemento,
                                                idElementoPadre:        idElementoPadre,
                                                ultimaMilla:            data.get('ultimaMilla'),
                                                esPseudoPe:             esPseudoPe
                                            },
                                            success: function(response){
                                                Ext.get(formPanel.getId()).unmask();
                                                if(response.responseText == "OK")
                                                {
                                                    Ext.Msg.alert('Mensaje','Se Asignaron los Recursos de Red!', function(btn){
                                                        if(btn=='ok')
                                                        {
                                                            win.destroy();
                                                            store.load();
                                                        }
                                                    });
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
                                        Ext.Msg.alert("Validacion","Favor Revise los campos", function(btn){
                                            if(btn=='ok'){
                                            }
                                        });
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

                        var win = Ext.create('Ext.window.Window', {
                            title: 'Asignar Recurso de Red - Internet Dedicado'+(esPseudoPe==='S'?' (PSEUDO PE)':''),
                            modal: true,
                            width: 1100,
                            closable: true,
                            layout: 'fit',
                            items: [formPanel]
                        }).show();

                        storeHilosDisponibles.load({
                            callback:function(){        
                                
                            }
                        });
                        
                        if (data.get('ultimaMilla')=="Radio")
                        {
                            Ext.getCmp('hilosDisponibles').setDisabled(true);
                            Ext.getCmp('nombreElementoContenedor').setDisabled(true);
                        }
                    }// if(json.status=="OK")
                    else
                    {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: json.msg,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });   
                    }
                }
                //-------------------------------------------------------------------------------------------
                    
            },//cierre response
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