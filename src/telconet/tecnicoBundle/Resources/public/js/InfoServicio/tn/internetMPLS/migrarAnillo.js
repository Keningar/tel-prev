/**
 * Funcion que sirve para mostrar la pantalla para la asignacion 
 * de recursos de red para Internet MPLS
 * 
 * @author Juan Lafuente <jlafuente@telconet.ec>
 * @version 1.0 15-12-2015
 * */
function showMigrarAnilloInternetMPLS(data)
{
    var idInterfaceElementoOutAsignado = 0;
    var nombreElemento                 = "";
    var nombreElementoConector         = "";
    var anillo                         = "";

    var numeroColorHiloSeleccionado    = "";
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    var tituloElementoConector = "Elemento Conector";
    
    if (data.ultimaMilla =="Radio")
    {
        tituloElementoConector = "Elemento Radio";
    }
    Ext.Ajax.request({
        url: getDatosFactibilidad,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio,
            ultimaMilla: data.ultimaMilla
        },
        success: function(response) {
            Ext.get(gridServicios.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            idInterfaceElementoOutAsignado = json.idInterfaceElementoConector;
            numeroColorHiloSeleccionado    = json.numeroColorHilo;
            anillo                         = json.anillo;          
            nombreElemento                 = json.nombreElemento;
            nombreElementoConector         = json.nombreElementoConector;
            
            //-------------------------------------------------------------------------------------------
            if(json.status=="OK")
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
                                            name: 'cliente',
                                            fieldLabel: 'Cliente',
                                            displayField: data.nombreCompleto,
                                            value: data.nombreCompleto,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '20%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.login,
                                            value: data.login,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------
                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoOrden',
                                            fieldLabel: 'Tipo Orden',
                                            displayField: data.tipoOrdenCompleto,
                                            value: data.tipoOrdenCompleto,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '20%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'producto',
                                            fieldLabel: 'Producto',
                                            displayField: data.nombreProducto,
                                            value: data.nombreProducto,
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
                                            displayField: data.capacidadUno,
                                            value: data.capacidadUno,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '20%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad2',
                                            fieldLabel: 'Capacidad2',
                                            displayField: data.capacidadDos,
                                            value: data.capacidadDos,
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
                                            displayField: data.tipoEnlace,
                                            value: data.tipoEnlace,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '20%', border: false},
                                        { width: '30%', border: false},
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
                            title: 'Informacion del Elemento Padre',
                            defaults: {
                                height: 100
                            },
                            items: [
                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5
                                    },
                                    items: [
                                        { width: '10%', border: false },
                                        {
                                            id: 'txtNombreElementoPadre',
                                            name: 'txtNombreElementoPadre',
                                            xtype: 'textfield',
                                            fieldLabel: 'Nombre',
                                            displayField: json.nombreElementoPadre,
                                            value: json.nombreElementoPadre,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '20%', border: false },
                                        {
                                            xtype: 'textfield',
                                            id: 'txtAnilloElementoPadre',
                                            name: 'txtAnilloElementoPadre',
                                            fieldLabel: 'Anillo',
                                            displayField: json.anillo,
                                            value: json.anillo,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false },
                                        //---------------------------------------------
                                        { width: '10%', border: false },
                                        {
                                            xtype: 'textfield',
                                            id: 'txtVlan',
                                            name: 'txtVlan',
                                            fieldLabel: 'Vlan',
                                            displayField: json.vlan,
                                            value: json.vlan,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '20%', border: false },
                                        { width: '30%', border: false },
                                        { width: '10%', border: false },
                                        //---------------------------------------------
                                        { width: '10%', border: false },
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
                                            width: '30%'
                                        },
                                        { width: '20%', border: false },
                                        { width: '30%', border: false },
                                        { width: '10%', border: false },
                                    ]
                                }

                            ]
                        }, //cierre de la informacion del cliente

                        //informacion tecnica (generada en la factibilidad)
                        {
                            colspan: 3,
                            xtype: 'panel',
                            title: 'Información técnica para la Migración a Anillo',
                            layout: 'anchor',
                            items: [
                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'vbox',
                                        align: 'stretch'
                                    },
                                    items: [
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
                                                            displayField: json.nombreElemento,
                                                            value: json.nombreElemento,
                                                            readOnly: true,
                                                            width: '100%',
                                                            labelAlign: 'top'
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            id: 'txtNombreInterfaceElemento',
                                                            name: 'txtNombreInterfaceElemento',
                                                            fieldLabel: 'Interface',
                                                            displayField: json.nombreInterfaceElemento,
                                                            value: json.nombreInterfaceElemento,
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
                                                            displayField: json.nombreElementoConector,
                                                            value: json.nombreElementoConector,
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
                                                                    xtype: 'textfield',
                                                                    id: 'txtHiloAsignado',
                                                                    name: 'txtHiloAsignado',
                                                                    fieldLabel: 'Hilo Asignado:',
                                                                    displayField: json.numeroColorHilo,
                                                                    value: json.numeroColorHilo,
                                                                    valueField: 'idInterfaceElementoOut',
                                                                    readOnly: true,
                                                                    width: '50%',
                                                                    labelAlign: 'top'
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
                                            url: crearSolicitudMigracionAnillo,
                                            method: 'post',
                                            timeout: 1000000,
                                            params: {
                                                idServicio              : data.idServicio, 
                                                idPersonaEmpresaRol     : data.idPersonaEmpresaRol ,
                                                capacidadUno            : data.capacidadUno,
                                                capacidadDos            : data.capacidadDos,
                                                ultimaMilla             : data.ultimaMilla,
                                                idElementoPadre         : json.idElementoPadre,
                                                //datos de recursos
                                                vlan                    : txtVlan.getValue(),
                                                vrf                     : cbxVrf.getValue(),
                                                idSubred                : cbxSubred.getValue(),
                                                tipoSubred              : cbxTipoSubred.getValue(),
                                                anillo                  : json.anillo,
                                                nombreInterfaceElemento : json.nombreInterfaceElemento,
                                                nombreElemento          : json.nombreElemento,
                                                nombreElementoConector  : json.nombreElementoConector
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
                                    handler: function()
                                    {
                                        win.destroy();
                                    }
                                }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Asignar Recurso de Red - Internet MPLS',
                    modal: true,
                    width: 1100,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

                Ext.getCmp('txtNombreInterfaceElementoConector').hide();
                
                if (data.ultimaMilla=="Radio")
                {
                    Ext.getCmp('txtHiloAsignado').hide();
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

function showEjecutarMigrarAnilloInternetMPLS(data)
{
    Ext.get("grid").mask('Consultando Datos...');
    Ext.Ajax.request
    ({
        url: urlGetDatosBackboneL3mpls,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            tipoElementoPadre: 'ROUTER'
        },
        success: function(response){
            var datosBackbone = Ext.JSON.decode(response.responseText);

            Ext.get("grid").unmask();

            var formPanel = Ext.create('Ext.form.Panel', {
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
                    columns: 2
                },
                defaults: {
                    bodyStyle: 'padding:20px'
                },
                items: [
                    //informacion del servicio
                    {
                        colspan: 2,
                        rowspan: 2,
                        xtype: 'panel',
                        title: 'Informacion del Cliente y Servicio',
                        defaults: { 
                            height: 100
                        },
                        items: [
                           {
                                xtype: 'container',
                                layout: {
                                    type:    'table',
                                    columns: 5,
                                    align:   'stretch'
                                },
                                items: [

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'cliente',
                                        fieldLabel: 'Cliente',
                                        displayField: data.nombreCompleto,
                                        value: data.nombreCompleto,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'Login',
                                        fieldLabel: 'Login',
                                        displayField: data.login,
                                        value: data.login,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    {   width: '10%', border: false},
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
                                        name: 'tipoOrden',
                                        fieldLabel: 'Tipo Orden',
                                        displayField: data.tipoOrdenCompleto,
                                        value: data.tipoOrdenCompleto,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'capacidad1',
                                        name: 'capacidad1',
                                        fieldLabel: 'Capacidad1',
                                        displayField: data.capacidadUno,
                                        value: data.capacidadUno,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'capacidad2',
                                        name: 'capacidad2',
                                        fieldLabel: 'Capacidad2',
                                        displayField: data.capacidadDos,
                                        value: data.capacidadDos,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'mac',
                                        name: 'mac',
                                        fieldLabel: 'Mac',
                                        displayField: datosBackbone.mac,
                                        value: datosBackbone.mac,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    { width: '10%', border: false},
                                    { width: '10%', border: false},

                                ]
                            }

                        ]
                    },//cierre de informacion del servicio

                    //informacion del backbone
                    {
                        colspan: 2,
                        rowspan: 2,
                        xtype: 'panel',
                        title: 'Informacion del Backbone',
                        defaults: { 
                            height: 180
                        },
                        items: [
                           {
                                xtype: 'container',
                                layout: {
                                    type:    'table',
                                    columns: 5,
                                    align:   'stretch'
                                },
                                items: [
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'nombreElementoPadre',
                                        name: 'nombreElementoPadre',
                                        fieldLabel: 'Nombre Elemento Padre',
                                        displayField: datosBackbone.elementoPadre,
                                        value: datosBackbone.elementoPadre,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    {
                                        xtype:  'hidden',
                                        id:     'idElementoPadre',
                                        name:   'idElementoPadre',
                                        value:  datosBackbone.idElementoPadre,
                                        width:  '30%'
                                    },
                                    {
                                        xtype: 'textfield',
                                        id: 'anillo',
                                        name: 'anillo',
                                        fieldLabel: 'Anillo',
                                        displayField: datosBackbone.anillo,
                                        value: datosBackbone.anillo,
                                        readOnly: true,
                                        width: '15%'
                                    },
                                    { width: '10%', border: false},
                                    // --------------------------------------------------------------------
                                    {   width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'nombreElemento',
                                        name: 'nombreElemento',
                                        fieldLabel: 'Nombre Elemento',
                                        displayField: datosBackbone.elemento,
                                        value: datosBackbone.elemento,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'interfaceElemento',
                                        name: 'interfaceElemento',
                                        fieldLabel: 'Interface Elemento',
                                        displayField: datosBackbone.interfaceElemento,
                                        value: datosBackbone.interfaceElemento,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},
                                    // --------------------------------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'vlan',
                                        name: 'vlan',
                                        fieldLabel: 'Vlan',
                                        displayField: datosBackbone.vlan,
                                        value: datosBackbone.vlan,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'vrf',
                                        name: 'vrf',
                                        fieldLabel: 'Vrf',
                                        displayField: datosBackbone.vrf,
                                        value: datosBackbone.vrf,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},
                                    // --------------------------------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'asPrivado',
                                        name: 'asPrivado',
                                        fieldLabel: 'As Privado',
                                        displayField: data.asPrivado,
                                        value: data.asPrivado,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'ipServicio',
                                        name: 'ipServicio',
                                        fieldLabel: 'Ip Servicio',
                                        displayField: data.ipServicio,
                                        value: data.ipServicio,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},
                                    // --------------------------------------------------------------------
                                    { width: '10%', border: false},
                                    {
                                        xtype:  'hidden',
                                        id:     'idElemento',
                                        name:   'idElemento',
                                        value:  datosBackbone.idElemento,
                                        displayField:  datosBackbone.idElemento,
                                        width:  '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype:  'hidden',
                                        id:     'idInterfaceElemento',
                                        name:   'idInterfaceElemento',
                                        value:  datosBackbone.idInterfaceElemento,
                                        displayField:  datosBackbone.idInterfaceElemento,
                                        width:  '30%'
                                    },
                                    { width: '10%', border: false}

                                ]
                            }

                        ]
                    }//cierre de informacion del backbone
                ],
                buttons: 
                [{
                    text: 'Ejecutar',
                    formBind: true,
                    handler: function(){
                        Ext.Msg.confirm('Mensaje','Está seguro de ejecutar la Migración a Anillo?', function(btn){
                            if(btn==='yes')
                            {
                                var vlan                    = Ext.getCmp('vlan').getValue();
                                var vrf                     = Ext.getCmp('vrf').getValue();
                                var asPrivado               = Ext.getCmp('asPrivado').getValue();
                                var ipServicio              = Ext.getCmp('ipServicio').getValue();
                                var idElementoPadre         = Ext.getCmp('idElementoPadre').getValue();
                                var idElemento              = Ext.getCmp('idElemento').getValue();
                                var idInterfaceElemento     = Ext.getCmp('idInterfaceElemento').getValue();
                                var capacidad1              = Ext.getCmp('capacidad1').getValue();
                                var capacidad2              = Ext.getCmp('capacidad2').getValue();
                                var rdId                    = data.rdId;
                                var anillo                  = Ext.getCmp('anillo').getValue();
                                var nombreElementoPadre     = data.elementoPadre;
                                var mascaraSubredServicio   = data.mascaraSubredServicio;
                                var gwSubredServicio        = data.gwSubredServicio;
                                var tipoEnlace              = data.tipoEnlace;;

                                Ext.get(formPanel.getId()).mask('Guardando datos...');

                                Ext.Ajax.request({
                                    url: ejecutaMigracionAnilloEstable,
                                    method: 'post',
                                    timeout: 1000000,
                                    params: { 
                                        idServicio:             data.idServicio,
                                        idElementoPadre:        idElementoPadre,
                                        idElemento:             idElemento,
                                        idInterfaceElemento:    idInterfaceElemento,
                                        vlan:                   vlan,
                                        vrf:                    vrf,
                                        asPrivado:              asPrivado,
                                        ipServicio:             ipServicio,
                                        capacidadUno:           capacidad1,
                                        capacidadDos:           capacidad2,
                                        rdId:                   rdId,
                                        anillo:                 anillo,
                                        nombreElementoPadre:    nombreElementoPadre,
                                        mascaraSubredServicio:  mascaraSubredServicio,
                                        gwSubredServicio:       gwSubredServicio,
                                        tipoEnlace:             tipoEnlace
                                    },
                                    success: function(response){
                                        Ext.get(formPanel.getId()).unmask();
                                        if(response.responseText === "OK")
                                        {
                                            Ext.Msg.alert('Mensaje','Se ejecuto la migración a anillo con éxito', function(btn){
                                                if(btn==='ok')
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
                            }//if(btn==='yes')
                        });
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
                title: 'Ejecutar Migración a Anillo',
                modal: true,
                width: 600,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
        },
        failure: function(response)
        {
            Ext.MessageBox.show({
                title: 'Error',
                msg: response.responseText,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            }); 
        }
    });
}
