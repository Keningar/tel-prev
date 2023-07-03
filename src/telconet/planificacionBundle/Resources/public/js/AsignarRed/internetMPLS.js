/**
 * Funcion que sirve para mostrar la pantalla para la asignacion 
 * de recursos de red para Internet MPLS
 * 
 * @author Juan Lafuente <jlafuente@telconet.ec>
 * @version 1.0 15-12-2015
 * 
 * Se añade parametro login en array para validar con
 * asignarRecursosInternetMPLS
 * 
 * @author Jonathan Montece <jmontece@telconet.ec>
 * @version 2.0 12-08-2021
 * */
function showRecursoRedInternetMPLS(data)
{
    var idInterfaceElementoOutAsignado = 0;
    var nombreElemento                 = "";
    var nombreElementoConector         = "";
    var anillo                         = "";
    var esPseudoPe                     = data.get('esPseudoPe');
    var boolHidenAnillo                = false;
    var esMigracionSDWAN               = false;
    var esSDWAN                        = false;
    var strProductoSDWAN               = 'Asignar Recursos de Red' + (data.get('producto') ? ' - ' + data.get('producto') : '')

    var numeroColorHiloSeleccionado    = "";
    Ext.get(grid.getId()).mask('Consultando Datos...');
    var tituloElementoConector = "Elemento Conector";
    if (data.get('ultimaMilla')=="Radio")
    {
        tituloElementoConector = "Elemento Radio";
    }

    if(data.get('booleanTipoRedGpon'))
    {
        boolHidenAnillo = true;
    }

    Ext.Ajax.request({
        url: getDatosFactibilidad,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.get('id_servicio'),
            ultimaMilla: data.get('ultimaMilla'),
            tipoSolicitud: data.get('descripcionSolicitud'),
            idSolicitud  : data.get('id_factibilidad'),
            tipoRed : data.get('strTipoRed')
        },
        success: function(response) {
            Ext.get(grid.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            idInterfaceElementoOutAsignado = json.idInterfaceElementoConector;
            numeroColorHiloSeleccionado    = json.numeroColorHilo;
            anillo                         = json.anillo;          
            nombreElemento                 = json.nombreElemento;
            nombreElementoConector         = json.nombreElementoConector;
            esMigracionSDWAN               = json.EsMigracionSDWAN;
            esSDWAN                        = json.EsSDWAN;

            if(data.get('booleanTipoRedGpon'))
            {
                numeroColorHiloSeleccionado    = data.get('idInterfaceConector');
                nombreElemento                 = data.get('pop');
                nombreElementoConector         = data.get('splitter');
            }
            //-------------------------------------------------------------------------------------------
            if(json.status=="OK" || data.get('ultimaMilla') === "FTTx")
            {
                var storeHilosDisponibles = new Ext.data.Store({
                    pageSize: 100,
                    proxy: {
                        type: 'ajax',
                        url: getHilosDisponibles,
                        extraParams: {
                            idElemento: json.idElementoConector,
                            estadoInterface: 'connected',
                            estadoInterfaceNotConect: 'not connect',
                            estadoInterfaceReserved: 'Factible'
                        },
                        reader: {
                            type: 'json',
                            root: 'encontrados'
                        }
                    },
                    fields:
                            [
                                {name: 'idInterfaceElementoOut',        mapping: 'idInterfaceElementoOut'},
                                {name: 'nombreInterfaceElementoOut',    mapping: 'nombreInterfaceElementoOut'},
                                {name:'colorHilo',                      mapping:'colorHilo'},
                                {name:'numeroHilo',                     mapping:'numeroHilo'},
                                {name:'numeroColorHilo',                mapping:'numeroColorHilo'}
                            ]
                });

                // The data store containing the list of states
                var storeTipoSubred = Ext.create('Ext.data.Store', {
                    fields: ['value', 'name'],
                    data: [
                        {"value": "WAN", "name": "WAN"},
                        {"value": "LAN", "name": "LAN"}
                    ]
                });

                var storeSubredMigracionSDWAN = Ext.create('Ext.data.Store', {
                    fields: ['value', 'name'],
                    data: [
                        { "value": json.objIp?.subredId, "name": json.objIp?.subred },
                    ]
                });

                var storeVrfInternetMigracionSDWAN = new Ext.data.Store({
                    fields: ['value', 'name'],
                    data: [
                        { "value": json.objIp?.vrfId, "name": json.objIp?.vrf },
                    ]
                });




                var storeSubredDisponibles = new Ext.data.Store({
                    pageSize: 100,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: getSubredDisponible,
                        reader: {
                            type: 'json',
                            root: 'encontrados'
                        }
                    },
                    fields:
                            [
                                {name: 'idSubred', mapping: 'idSubred'},
                                {name: 'subred', mapping: 'subred'}
                            ]
                });

                var storeVrfInternet = new Ext.data.Store({
                    pageSize: 100,
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: getVrfInternet,
                        reader: {
                            type: 'json',
                            root: 'encontrados'
                        }
                    },
                    fields:
                            [
                                {name: 'id', mapping:  'id'},
                                {name: 'valor', mapping: 'valor'}
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
                            rowspan: 2,
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
                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoOrden',
                                            fieldLabel: 'Tipo Orden',
                                            displayField: data.get("tipo_orden"),
                                            value: data.get("tipo_orden"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'producto',
                                            fieldLabel: 'Producto',
                                            displayField: data.get("producto"),
                                            value: data.get("producto"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------

                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad1',
                                            fieldLabel: 'Capacidad1',
                                            displayField: (data.get('ultimaMilla') === "FTTx"?data.get('capacidad1'):json.capacidad1),
                                            value: (data.get('ultimaMilla') === "FTTx"?data.get('capacidad1'):json.capacidad1),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad2',
                                            fieldLabel: 'Capacidad2',
                                            displayField: (data.get('ultimaMilla') === "FTTx"?data.get('capacidad2'):json.capacidad2),
                                            value: (data.get('ultimaMilla') === "FTTx"?data.get('capacidad2'):json.capacidad2),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
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
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoRed',
                                            fieldLabel: 'Tipo de Red',
                                            displayField: data.get('strTipoRed'),
                                            value: data.get('strTipoRed'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        { width: '10%', border: false}
                                    ]
                                }

                            ]
                        }, //cierre de informacion del servicio

                        //informacion del cliente
                        {
                            colspan: 2,
                            rowspan: 2,
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
                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'cliente',
                                            fieldLabel: 'Cliente',
                                            displayField: data.get('cliente'),
                                            value: data.get('cliente'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.get('login2'),
                                            value: data.get('login2'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------

                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'ciudad',
                                            fieldLabel: 'Ciudad',
                                            displayField: data.get('ciudad'),
                                            value: data.get('ciudad'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'direccion',
                                            fieldLabel: 'Direccion',
                                            displayField: data.get('direccion'),
                                            value: data.get('direccion'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------

                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'sector',
                                            fieldLabel: 'Sector',
                                            displayField: data.get('nombreSector'),
                                            value: data.get('nombreSector'),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'esRecontratacion',
                                            fieldLabel: 'Es Recontratacion',
                                            displayField: data.get("esRecontratacion"),
                                            value: data.get("esRecontratacion"),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        {width: '10%', border: false}

                                        //---------------------------------------------

                                    ]
                                }

                            ]
                        }, //cierre de la informacion del cliente

                        //informacion tecnica (generada en la factibilidad)
                        {
                            colspan: 3,
                            xtype: 'panel',
                            title: 'Información técnica asignada por la Factibilidad',
                            layout: 'anchor',
                            items: [
                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    items: [
                                        // ===========================================
                                        // Elemento Padre 
                                        // ===========================================
                                        {
                                            xtype: 'fieldset',
                                            title: 'Elemento Padre',
                                            collapsible: false,
                                            collapsed: false,
                                            defaultType: 'textfield',
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch',
                                                        type: 'hbox'
                                                    },
                                                    items: [
                                                        {
                                                            id: 'txtNombreElementoPadre',
                                                            name: 'txtNombreElementoPadre',
                                                            xtype: 'textfield',
                                                            fieldLabel: 'Nombre',
                                                            displayField: json.nombreElementoPadre,
                                                            value: json.nombreElementoPadre,
                                                            readOnly: true,
                                                            width: '40%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtAnilloElementoPadre',
                                                            name: 'txtAnilloElementoPadre',
                                                            fieldLabel: 'Anillo',
                                                            displayField: json.anillo,
                                                            value: json.anillo,
                                                            hidden: boolHidenAnillo,
                                                            readOnly: true,
                                                            width: '10%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtVlan',
                                                            name: 'txtVlan',
                                                            fieldLabel: 'Vlan',
                                                            displayField: (esPseudoPe==='S'?'':json.vlan),
                                                            value: (esPseudoPe==='S'?'':json.vlan),
                                                            readOnly: (esPseudoPe==='S'?false:true),
                                                            width: '10%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            id: 'cbxVrf',
                                                            name: 'cbxVrf',
                                                            xtype: 'combobox',
                                                            fieldLabel: 'VRF',
                                                            store: storeVrfInternet,
                                                            queryMode: 'local',
                                                            displayField: 'valor',
                                                            valueField: 'id',
                                                            editable: false,
                                                            width: '20%',
                                                            labelAlign: 'top'
                                                        }
                                                    ]
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'hbox',
                                                align: 'stretch'
                                            },
                                            items: [
                                                // ===========================================
                                                // Elemento Asignado 
                                                // ===========================================
                                                {
                                                    flex: 10,
                                                    xtype: 'fieldset',
                                                    height: "auto",
                                                    title: 'Elemento',
                                                    collapsible: false,
                                                    defaultType: 'textfield',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreElemento',
                                                            name: 'txtNombreElemento',
                                                            fieldLabel: 'Nombre',
                                                            displayField: (data.get('ultimaMilla') === "FTTx"?data.get('pop'):json.nombreElemento),
                                                            value: (data.get('ultimaMilla') === "FTTx"?data.get('pop'):json.nombreElemento),
                                                            readOnly: true,
                                                            width: '100%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreInterfaceElemento',
                                                            name: 'txtNombreInterfaceElemento',
                                                            fieldLabel: 'Interface',
                                                            displayField: (data.get('ultimaMilla') === "FTTx"?
                                                                            data.get('intElemento'):json.nombreInterfaceElemento),
                                                            value: (data.get('ultimaMilla') === "FTTx"?
                                                                        data.get('intElemento'):json.nombreInterfaceElemento),
                                                            readOnly: true,
                                                            width: '50%',
                                                            labelAlign: 'top'
                                                        }
                                                    ]
                                                },
                                                {
                                                    flex: 1,
                                                    border: false
                                                },
                                                // ===========================================
                                                // Elemento Conectaro Asignado 
                                                // ===========================================
                                                {
                                                    flex: 10,
                                                    xtype: 'fieldset',
                                                    height: "auto",
                                                    title: tituloElementoConector,
                                                    collapsible: false,
                                                    defaultType: 'textfield',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreElementoConector',
                                                            name: 'txtNombreElementoConector',
                                                            fieldLabel: 'Nombre',
                                                            displayField: (data.get('ultimaMilla') === "FTTx"?
                                                                            data.get('splitter'):json.nombreElementoConector),
                                                            value: (data.get('ultimaMilla') === "FTTx"?
                                                                            data.get('splitter'):json.nombreElementoConector),
                                                            readOnly: true,
                                                            width: '100%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            xtype: 'container',
                                                            layout: {
                                                                align: 'stretch',
                                                                type: 'hbox'
                                                            },
                                                            items: [
                                                                {
                                                                    id: 'txtNombreInterfaceElementoConector',
                                                                    name: 'txtNombreInterfaceElementoConector',
                                                                    xtype: 'textfield',
                                                                    fieldLabel: 'Interface',
                                                                    displayField: json.nombreInterfaceElementoConector,
                                                                    value: json.nombreInterfaceElementoConector,
                                                                    readOnly: true,
                                                                    width: '50%',
                                                                    labelAlign: 'top'
                                                                },
                                                                {
                                                                    id: 'interfaceElementoConector',
                                                                    name: 'interfaceElementoConector',
                                                                    xtype: 'textfield',
                                                                    fieldLabel: 'Interface',
                                                                    displayField: data.get('idInterfaceConector'),
                                                                    value: data.get('idInterfaceConector'),
                                                                    readOnly: true,
                                                                    hidden: (data.get('ultimaMilla') === "FTTx"?false:true),
                                                                    width: '50%',
                                                                    labelAlign: 'top'
                                                                },
                                                                {
                                                                    id: 'cbxHilosDisponibles',
                                                                    name: 'cbxHilosDisponibles',
                                                                    xtype: 'combobox',
                                                                    fieldLabel: 'Hilos Disponibles',
                                                                    store: storeHilosDisponibles,
                                                                    queryMode: 'local',
                                                                    displayField: 'numeroColorHilo',
                                                                    value: json.numeroColorHilo,
                                                                    valueField: 'idInterfaceElementoOut',
                                                                    width: '40%',
                                                                    labelAlign: 'top',
                                                                    readOnly: true,
                                                                    hidden: (data.get('ultimaMilla') === "FTTx"?true:false),
                                                                    editable: false,
                                                                    listeners:
                                                                    {
                                                                        select: function(combo) {                                               
                                                                            var value = Ext.getCmp('cbxHilosDisponibles').getValue();
                                                                            var inteface = Ext.getCmp('cbxHilosDisponibles')
                                                                                            .findRecordByValue(value)
                                                                                            .get('nombreInterfaceElementoOut');
                                                                            Ext.getCmp('txtNombreInterfaceElementoConector').setValue(inteface);
                                                                            
                                                                            var storeHilosDisponibles = Ext.getCmp('cbxHilosDisponibles').getStore();
                                                                            var index = storeHilosDisponibles.find('idInterfaceElementoOut', value);
                                                                            if(index != -1)//the record has been found
                                                                            {
                                                                                var record = storeHilosDisponibles.getAt(index);
                                                                                numeroColorHiloSeleccionado=record.get('numeroColorHilo');
                                                                            }
                                                                            else
                                                                            {
                                                                                numeroColorHiloSeleccionado="";
                                                                            }
                                                                            //....
                                                                            Ext.MessageBox.wait('Cambiando Interfaces...');
                                                                            var objeto = combo.valueModels[0].raw;
                                                                            Ext.Ajax.request
                                                                            ({
                                                                                url: ajaxGetPuertoSwitchByHilo,
                                                                                method: 'post',
                                                                                params: { idInterfaceElementoConector : objeto.idInterfaceElemento },
                                                                                success: function(response)
                                                                                {
                                                                                    Ext.MessageBox.hide();
                                                                                    var json    = Ext.JSON.decode(response.responseText);
                                                                                    var objJson = json;
                                                                                    Ext.getCmp('txtNombreInterfaceElemento').setValue = objJson.idInterfaceElemento;
                                                                                    Ext.getCmp('txtNombreInterfaceElemento').setRawValue(objJson.nombreInterfaceElemento);
                                                                                    idInterfaceElementoOutAsignado = objeto.idInterfaceElementoOut;
                                                                                },
                                                                                failure: function(result)
                                                                                {
                                                                                    Ext.MessageBox.hide();
                                                                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                                                                }
                                                                            });
                                                                        }
                                                                    }
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'fieldset',
                                            title: 'Asignación de Subred',
                                            collapsible: false,
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch',
                                                        type: 'hbox'
                                                    },
                                                    items: [
                                                        {width: '30%', border: false},
                                                        {
                                                            id: 'cbxTipoSubred',
                                                            name: 'cbxTipoSubred',
                                                            xtype: 'combobox',
                                                            fieldLabel: 'Tipo de Subred',
                                                            store: storeTipoSubred,
                                                            queryMode: 'local',
                                                            displayField: 'name',
                                                            valueField: 'value',
                                                            width: '15%',
                                                            labelAlign: 'top',
                                                            editable: false,
                                                            listeners:
                                                            {
                                                                select: function()
                                                                {
                                                                    storeSubredDisponibles.getProxy()
                                                                                        .extraParams.tipo = Ext.getCmp('cbxTipoSubred').value;
                                                                    storeSubredDisponibles.getProxy()
                                                                                        .extraParams.idElemento = json.idElementoPadre;
                                                                    storeSubredDisponibles.getProxy()
                                                                                        .extraParams.anillo = json.anillo;
                                                                    storeSubredDisponibles.getProxy()
                                                                                        .extraParams.uso = 'INTMPLS';
                                                                    storeSubredDisponibles.getProxy().extraParams.idServicio = json.idServicio;
                                                                    storeSubredDisponibles.getProxy().extraParams.idElementoOlt = json.idElemento;
                                                                    storeSubredDisponibles.getProxy().extraParams.login = data.get('login2');
                                                                    storeSubredDisponibles.getProxy().extraParams.tipoRed = data.get('strTipoRed');
                                                                    storeSubredDisponibles.load();
                                                                }
                                                            }
                                                        },
                                                        {width: '5%', border: false},
                                                        {
                                                            id: 'cbxSubred',
                                                            name: 'cbxSubred',
                                                            xtype: 'combobox',
                                                            fieldLabel: 'Subred',
                                                            store: storeSubredDisponibles,
                                                            queryMode: 'local',
                                                            displayField: 'subred',
                                                            valueField: 'idSubred',
                                                            width: '20%',
                                                            labelAlign: 'top',
                                                            editable: false
                                                        },
                                                        {width: '30%', border: false}
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }// cierre informacion tecnica
                    ],
                    buttons:
                            [{
                                    id: 'btnGuardar',
                                    text: 'Grabar',
                                    formBind: true,
                                    handler: function() {
                                        var txtVlan                     = Ext.getCmp('txtVlan');
                                        var cbxVrf                      = Ext.getCmp('cbxVrf');
                                        var cbxSubred                   = Ext.getCmp('cbxSubred');
                                        var cbxTipoSubred               = Ext.getCmp('cbxTipoSubred');
                                        var cbxHilosDisponibles         = Ext.getCmp('cbxHilosDisponibles');
                                        var txtNombreInterfaceElemento  = Ext.getCmp('txtNombreInterfaceElemento');
                                        // =============================================================
                                        // Validaciones de los datos requeridos
                                        // =============================================================
                                        if (cbxTipoSubred.getValue() === "0")
                                        {
                                            cbxTipoSubred.markInvalid('Seleccione el tipo de subred');
                                            return;
                                        }
                                        if (cbxSubred.getValue() === "0")
                                        {
                                            cbxSubred.markInvalid('Seleccione la subred');
                                            return;
                                        }
                                        if (cbxVrf.getValue() === "0")
                                        {
                                            cbxVrf.markInvalid('Seleccione la VRF');
                                            return;
                                        }

                                        Ext.get(formPanel.getId()).mask('Guardando datos!');
                                        Ext.getCmp('btnGuardar').disable();

                                        Ext.Ajax.request({
                                            url: asignarRecursosInternetMPLS,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: {
                                                idServicio:                 data.get('id_servicio'),
                                                idDetalleSolicitud:         data.get('id_factibilidad'),
                                                tipoSolicitud:              data.get('descripcionSolicitud'),
                                                vlan:                       txtVlan.getValue(),
                                                vrf:                        cbxVrf.getValue(),
                                                subred:                     cbxSubred.getValue(),
                                                tipoSubred:                 cbxTipoSubred.getValue(),
                                                idElementoPadre:            json.idElementoPadre,
                                                hiloSeleccionado:           idInterfaceElementoOutAsignado,
                                                numeroColorHiloSeleccionado:numeroColorHiloSeleccionado,
                                                tipoRed:                    data.get('strTipoRed'),
                                                anillo:                     anillo,
                                                nombreInterfaceElemento:    txtNombreInterfaceElemento.getValue(),
                                                nombreElemento:             nombreElemento,
                                                nombreElementoConector:     nombreElementoConector,
                                                ultimaMilla:                data.get('ultimaMilla'),
                                                esPseudoPe:                 esPseudoPe,
                                                login:                      data.get('login2')
                                            },
                                            success: function(response) {
                                                Ext.get(formPanel.getId()).unmask();
                                                if (response.responseText === "OK")
                                                {
                                                    Ext.Msg.show({
                                                        title: 'Información',
                                                        msg: 'Se Asignaron los Recursos de Red!',
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.MessageBox.INFO,
                                                        fn: function(btn, text) {
                                                            if (btn === 'ok') {
                                                                win.destroy();
                                                                store.load();
                                                            }
                                                        }
                                                    });
                                                }
                                                //Ext.get(formPanel.getId()).unmask();
                                                else if (response.responseText != "OK")
                                                {
                                                    var text = Ext.decode(response.responseText);
                                                    
                                                        Ext.Msg.show(
                                                           {
                                                            title: 'Informacion',
                                                            msg: text.strMensaje + text.strConfirmacion,
                                                            buttons: Ext.Msg.OKCANCEL,
                                                            icon: Ext.MessageBox.INFO,
                                                           
                                                            fn: function(btn, text) {
                                                                if (btn === 'ok') {
                                                                    //Ext.getCmp('btnGuardar').click();
                                                                setTimeout(response,10000);
                                                                Ext.getCmp('btnGuardar').enable();
                                                                //store.load();
                                                                document.getElementById('btnGuardar').click();        
                                                                }
                                                                if (btn === 'cancel'){
                                                                    Ext.getCmp('btnGuardar').enable();
                                                                }
                                                            }
                                                        });
                                                    
                                                }
                                                else {
                                                    Ext.get(formPanel.getId()).unmask();
                                                    Ext.getCmp('btnGuardar').enable();
                                                    Ext.Msg.show({
                                                        title: 'Error',
                                                        msg: response.responseText,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.MessageBox.ERROR
                                                    });
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.get(formPanel.getId()).unmask();
                                                Ext.getCmp('btnGuardar').enable();
                                                Ext.Msg.show({
                                                    title: 'Error',
                                                    msg: 'Error: ' + result.statusText,
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            }
                                        });
                                    }// Fin de Funcionalidad del Boton Guardar
                                },
                                {
                                    text: 'Cancelar',
                                    handler: function () 
                                    {
                                        win.destroy();
                                    }
                                }]
                });
                //-------------------------------------------------------------------------------------------

                //INICIO: FormPanel Migracion SDWAN-----------------------------------------------------------------
                var formPanelMigracionSDWAN = Ext.create('Ext.form.Panel', {
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
                            rowspan: 2,
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
                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoOrden',
                                            fieldLabel: 'Tipo Orden',
                                            displayField: data.get("tipo_orden"),
                                            value: data.get("tipo_orden"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'producto',
                                            fieldLabel: 'Producto',
                                            displayField: data.get("producto"),
                                            value: data.get("producto"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },
                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad1',
                                            fieldLabel: 'Capacidad1',
                                            displayField: (data.get('ultimaMilla') === "FTTx" ? data.get('capacidad1') : json.capacidad1),
                                            value: (data.get('ultimaMilla') === "FTTx" ? data.get('capacidad1') : json.capacidad1),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad2',
                                            fieldLabel: 'Capacidad2',
                                            displayField: (data.get('ultimaMilla') === "FTTx" ? data.get('capacidad2') : json.capacidad2),
                                            value: (data.get('ultimaMilla') === "FTTx" ? data.get('capacidad2') : json.capacidad2),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },
                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoEnlace',
                                            fieldLabel: 'Tipo Enlace',
                                            displayField: data.get('tipo_enlace'),
                                            value: data.get('tipo_enlace'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoRed',
                                            fieldLabel: 'Tipo de Red',
                                            displayField: data.get('strTipoRed'),
                                            value: data.get('strTipoRed'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        { width: '10%', border: false }
                                    ]
                                }

                            ]
                        }, //cierre de informacion del servicio

                        //informacion del cliente
                        {
                            colspan: 2,
                            rowspan: 2,
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
                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'cliente',
                                            fieldLabel: 'Cliente',
                                            displayField: data.get('cliente'),
                                            value: data.get('cliente'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.get('login2'),
                                            value: data.get('login2'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },
                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'ciudad',
                                            fieldLabel: 'Ciudad',
                                            displayField: data.get('ciudad'),
                                            value: data.get('ciudad'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'direccion',
                                            fieldLabel: 'Direccion',
                                            displayField: data.get('direccion'),
                                            value: data.get('direccion'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },
                                        //---------------------------------------------

                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'sector',
                                            fieldLabel: 'Sector',
                                            displayField: data.get('nombreSector'),
                                            value: data.get('nombreSector'),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '15%', border: false },
                                        {
                                            xtype: 'textfield',
                                            name: 'esRecontratacion',
                                            fieldLabel: 'Es Recontratacion',
                                            displayField: data.get("esRecontratacion"),
                                            value: data.get("esRecontratacion"),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '10%', border: false }

                                        //---------------------------------------------

                                    ]
                                }

                            ]
                        }, //cierre de la informacion del cliente

                        //informacion tecnica (generada en la factibilidad)
                        {
                            colspan: 3,
                            xtype: 'panel',
                            title: 'Información técnica asignada por la Factibilidad',
                            layout: 'anchor',
                            items: [
                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    items: [
                                        // ===========================================
                                        // Elemento Padre 
                                        // ===========================================
                                        {
                                            xtype: 'fieldset',
                                            title: 'Elemento Padre',
                                            collapsible: false,
                                            collapsed: false,
                                            defaultType: 'textfield',
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch',
                                                        type: 'hbox'
                                                    },
                                                    items: [
                                                        {
                                                            id: 'txtNombreElementoPadre',
                                                            name: 'txtNombreElementoPadre',
                                                            xtype: 'textfield',
                                                            fieldLabel: 'Nombre',
                                                            displayField: json.nombreElementoPadre,
                                                            value: json.nombreElementoPadre,
                                                            readOnly: true,
                                                            width: '40%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtAnilloElementoPadre',
                                                            name: 'txtAnilloElementoPadre',
                                                            fieldLabel: 'Anillo',
                                                            displayField: json.anillo,
                                                            value: json.anillo,
                                                            hidden: boolHidenAnillo,
                                                            readOnly: true,
                                                            width: '10%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtVlan',
                                                            name: 'txtVlan',
                                                            fieldLabel: 'Vlan',
                                                            displayField: (esPseudoPe === 'S' ? '' : json.vlan),
                                                            value: (esPseudoPe === 'S' ? '' : json.vlan),
                                                            readOnly: (esPseudoPe === 'S' ? false : true),
                                                            width: '10%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            width: '5%',
                                                            border: false
                                                        },
                                                        {
                                                            id: 'cbxVrfSDWAN',
                                                            name: 'cbxVrfSDWAN',
                                                            xtype: 'combobox',
                                                            fieldLabel: 'VRF',
                                                            store: storeVrfInternetMigracionSDWAN,
                                                            queryMode: 'local',
                                                            displayField: 'name',
                                                            valueField: 'value',
                                                            editable: false,
                                                            width: '20%',
                                                            labelAlign: 'top',
                                                            readOnly: true,
                                                            value: json.objIp?.vrfId
                                                        }
                                                    ]
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'hbox',
                                                align: 'stretch'
                                            },
                                            items: [
                                                // ===========================================
                                                // Elemento Asignado 
                                                // ===========================================
                                                {
                                                    flex: 10,
                                                    xtype: 'fieldset',
                                                    height: "auto",
                                                    title: 'Elemento',
                                                    collapsible: false,
                                                    defaultType: 'textfield',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreElemento',
                                                            name: 'txtNombreElemento',
                                                            fieldLabel: 'Nombre',
                                                            displayField: (data.get('ultimaMilla') === "FTTx" ? data.get('pop') : json.nombreElemento),
                                                            value: (data.get('ultimaMilla') === "FTTx" ? data.get('pop') : json.nombreElemento),
                                                            readOnly: true,
                                                            width: '100%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreInterfaceElemento',
                                                            name: 'txtNombreInterfaceElemento',
                                                            fieldLabel: 'Interface',
                                                            displayField: (data.get('ultimaMilla') === "FTTx" ?
                                                                data.get('intElemento') : json.nombreInterfaceElemento),
                                                            value: (data.get('ultimaMilla') === "FTTx" ?
                                                                data.get('intElemento') : json.nombreInterfaceElemento),
                                                            readOnly: true,
                                                            width: '50%',
                                                            labelAlign: 'top'
                                                        }
                                                    ]
                                                },
                                                {
                                                    flex: 1,
                                                    border: false
                                                },
                                                // ===========================================
                                                // Elemento Conectaro Asignado 
                                                // ===========================================
                                                {
                                                    flex: 10,
                                                    xtype: 'fieldset',
                                                    height: "auto",
                                                    title: tituloElementoConector,
                                                    collapsible: false,
                                                    defaultType: 'textfield',
                                                    items: [
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreElementoConector',
                                                            name: 'txtNombreElementoConector',
                                                            fieldLabel: 'Nombre',
                                                            displayField: (data.get('ultimaMilla') === "FTTx" ?
                                                                data.get('splitter') : json.nombreElementoConector),
                                                            value: (data.get('ultimaMilla') === "FTTx" ?
                                                                data.get('splitter') : json.nombreElementoConector),
                                                            readOnly: true,
                                                            width: '100%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            xtype: 'container',
                                                            layout: {
                                                                align: 'stretch',
                                                                type: 'hbox'
                                                            },
                                                            items: [
                                                                {
                                                                    id: 'txtNombreInterfaceElementoConector',
                                                                    name: 'txtNombreInterfaceElementoConector',
                                                                    xtype: 'textfield',
                                                                    fieldLabel: 'Interface',
                                                                    displayField: json.nombreInterfaceElementoConector,
                                                                    value: json.nombreInterfaceElementoConector,
                                                                    readOnly: true,
                                                                    width: '50%',
                                                                    labelAlign: 'top'
                                                                },
                                                                {
                                                                    id: 'interfaceElementoConector',
                                                                    name: 'interfaceElementoConector',
                                                                    xtype: 'textfield',
                                                                    fieldLabel: 'Interface',
                                                                    displayField: data.get('idInterfaceConector'),
                                                                    value: data.get('idInterfaceConector'),
                                                                    readOnly: true,
                                                                    hidden: (data.get('ultimaMilla') === "FTTx" ? false : true),
                                                                    width: '50%',
                                                                    labelAlign: 'top'
                                                                },
                                                                {
                                                                    id: 'cbxHilosDisponibles',
                                                                    name: 'cbxHilosDisponibles',
                                                                    xtype: 'combobox',
                                                                    fieldLabel: 'Hilos Disponibles',
                                                                    store: storeHilosDisponibles,
                                                                    queryMode: 'local',
                                                                    displayField: 'numeroColorHilo',
                                                                    value: json.numeroColorHilo,
                                                                    valueField: 'idInterfaceElementoOut',
                                                                    width: '40%',
                                                                    labelAlign: 'top',
                                                                    readOnly: true,
                                                                    hidden: (data.get('ultimaMilla') === "FTTx" ? true : false),
                                                                    editable: false
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'fieldset',
                                            title: 'Asignación de Subred',
                                            collapsible: false,
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch',
                                                        type: 'hbox'
                                                    },
                                                    items: [
                                                        { width: '30%', border: false },
                                                        {
                                                            id: 'cbxTipoSubredSDWAN',
                                                            name: 'cbxTipoSubredSDWAN',
                                                            xtype: 'combobox',
                                                            fieldLabel: 'Tipo de Subred',
                                                            store: storeTipoSubred,
                                                            queryMode: 'local',
                                                            displayField: 'name',
                                                            valueField: 'value',
                                                            width: '15%',
                                                            labelAlign: 'top',
                                                            editable: false,
                                                            readOnly: true,
                                                            value: json.objIp?.tipoIp.toUpperCase(),
                                                        },
                                                        { width: '5%', border: false },
                                                        {
                                                            id: 'cbxSubredSDWAN',
                                                            name: 'cbxSubredSDWAN',
                                                            xtype: 'combobox',
                                                            fieldLabel: 'Subred',
                                                            store: storeSubredMigracionSDWAN,
                                                            queryMode: 'local',
                                                            displayField: 'name',
                                                            valueField: 'value',
                                                            width: '20%',
                                                            labelAlign: 'top',
                                                            editable: false,
                                                            readOnly: true,
                                                            value: json.objIp?.subredId
                                                        },
                                                        { width: '30%', border: false }
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }// cierre informacion tecnica
                    ],
                    buttons:
                        [{
                            id: 'btnGuardar',
                            text: 'Grabar',
                            formBind: true,
                            handler: function () {
                                var txtVlan = Ext.getCmp('txtVlan');
                                var cbxVrfSDWAN = Ext.getCmp('cbxVrfSDWAN');
                                var cbxSubredSDWAN = Ext.getCmp('cbxSubredSDWAN');
                                var cbxTipoSubredSDWAN = Ext.getCmp('cbxTipoSubredSDWAN');
                                var txtNombreInterfaceElemento = Ext.getCmp('txtNombreInterfaceElemento');
                                // =============================================================
                                // Validaciones de los datos requeridos
                                // =============================================================
                                if (cbxTipoSubredSDWAN.getValue() === "0") {
                                    cbxTipoSubredSDWAN.markInvalid('Seleccione el tipo de subred');
                                    return;
                                }
                                if (cbxSubredSDWAN.getValue() === "0") {
                                    cbxSubredSDWAN.markInvalid('Seleccione la subred');
                                    return;
                                }
                                if (cbxVrfSDWAN.getValue() === "0") {
                                    cbxVrfSDWAN.markInvalid('Seleccione la VRF');
                                    return;
                                }

                                Ext.get(formPanelMigracionSDWAN.getId()).mask('Guardando datos!');
                                Ext.getCmp('btnGuardar').disable();

                                Ext.Ajax.request({
                                    url: asignarRecursosInternetMPLS,
                                    method: 'post',
                                    timeout: 1000000,
                                    params: {
                                        idServicio: data.get('id_servicio'),
                                        idDetalleSolicitud: data.get('id_factibilidad'),
                                        tipoSolicitud: data.get('descripcionSolicitud'),
                                        vlan: txtVlan.getValue(),
                                        vrf: cbxVrfSDWAN.getValue(),
                                        subred: cbxSubredSDWAN.getValue(),
                                        tipoSubred: cbxTipoSubredSDWAN.getValue(),
                                        idElementoPadre: json.idElementoPadre,
                                        hiloSeleccionado: idInterfaceElementoOutAsignado,
                                        numeroColorHiloSeleccionado: numeroColorHiloSeleccionado,
                                        tipoRed: data.get('strTipoRed'),
                                        anillo: anillo,
                                        nombreInterfaceElemento: txtNombreInterfaceElemento.getValue(),
                                        nombreElemento: nombreElemento,
                                        nombreElementoConector: nombreElementoConector,
                                        ultimaMilla: data.get('ultimaMilla'),
                                        esPseudoPe: esPseudoPe,
                                        login: data.get('login2'),
                                        migracionSDWAM: (esSDWAN === true && esMigracionSDWAN === true) ? 'SI': 'NO'
                                    },
                                    success: function (response) {
                                        Ext.get(formPanelMigracionSDWAN.getId()).unmask();
                                        if (response.responseText === "OK") {
                                            Ext.Msg.show({
                                                title: 'Información',
                                                msg: 'Se Asignaron los Recursos de Red!',
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.INFO,
                                                fn: function (btn, text) {
                                                    if (btn === 'ok') {
                                                        win.destroy();
                                                        store.load();
                                                    }
                                                }
                                            });
                                        }
                                        //Ext.get(formPanel.getId()).unmask();
                                        else if (response.responseText != "OK") {
                                            var text = Ext.decode(response.responseText);

                                            Ext.Msg.show(
                                                {
                                                    title: 'Informacion',
                                                    msg: text.strMensaje + text.strConfirmacion,
                                                    buttons: Ext.Msg.OKCANCEL,
                                                    icon: Ext.MessageBox.INFO,

                                                    fn: function (btn, text) {
                                                        if (btn === 'ok') {
                                                            //Ext.getCmp('btnGuardar').click();
                                                            setTimeout(response, 10000);
                                                            Ext.getCmp('btnGuardar').enable();
                                                            //store.load();
                                                            document.getElementById('btnGuardar').click();
                                                            document.getElementById('btnGuardar').click();        
                                                            document.getElementById('btnGuardar').click();
                                                        }
                                                        if (btn === 'cancel') {
                                                            Ext.getCmp('btnGuardar').enable();
                                                        }
                                                    }
                                                });

                                        }
                                        else {
                                            Ext.get(formPanelMigracionSDWAN.getId()).unmask();
                                            Ext.getCmp('btnGuardar').enable();
                                            Ext.Msg.show({
                                                title: 'Error',
                                                msg: response.responseText,
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    },
                                    failure: function (result) {
                                        Ext.get(formPanelMigracionSDWAN.getId()).unmask();
                                        Ext.getCmp('btnGuardar').enable();
                                        Ext.Msg.show({
                                            title: 'Error',
                                            msg: 'Error: ' + result.statusText,
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                });
                            }// Fin de Funcionalidad del Boton Guardar
                        },
                        {
                            text: 'Cancelar',
                            handler: function () {
                                win.destroy();
                            }
                        }]
                });
                //FIN: FormPanel Migracion SDWAN-----------------------------------------------------------------

                var win = Ext.create('Ext.window.Window', {
                    title: (esMigracionSDWAN && esSDWAN) ? strProductoSDWAN :'Asignar Recurso de Red - Internet MPLS',
                    modal: true,
                    width: 1100,
                    closable: true,
                    layout: 'fit',
                    items: [(esMigracionSDWAN && esSDWAN) ? formPanelMigracionSDWAN : formPanel]
                }).show();

                Ext.getCmp('txtNombreInterfaceElementoConector').hide();
                
                if (data.get('ultimaMilla')=="Radio")
                {
                    Ext.getCmp('cbxHilosDisponibles').hide();
                }
                

                storeHilosDisponibles.load({
                    callback: function() {
                        storeTipoSubred.load({
                            callback: function() {
                                storeTipoSubred.insert(0, {value: '0', name: 'Seleccione...'});
                                Ext.getCmp("cbxTipoSubred").setValue("0");
                                //...
                                storeSubredDisponibles.insert(0, {idSubred: '0', subred: 'Seleccione...'});
                                Ext.getCmp('cbxSubred').setValue('0');

                                storeVrfInternet.load({
                                    callback: function(){
                                        storeVrfInternet.insert(0, {id: '0', valor: 'Seleccione...'});
                                        Ext.getCmp('cbxVrf').setValue('0');
                                    }
                                });
                            }
                        });

                        storeSubredDisponibles.on('load', function()
                        {
                            storeSubredDisponibles.insert(0, {idSubred: '0', subred: 'Seleccione...'});
                            Ext.getCmp('cbxSubred').setValue('0');
                        });

                    }
                });
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