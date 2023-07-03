Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 10;
var store = '';
var estado_id = '';
var area_id = '';
var login_id = '';
var tipo_asignacion = '';
var pto_sucursal = '';
var idClienteSucursalSesion;


function winDatosEnvio4(idPto) {

    winDetalle = "";
    if (!winDetalle) {
        Ext.define('CiudadesList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', type: 'int'},
                {name: 'nombre', type: 'string'}
            ]
        });

        storeCiudad = Ext.create('Ext.data.Store', {
            model: 'CiudadesList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: url_lista_ciudades,
                reader: {
                    type: 'json',
                    root: 'ciudades'
                }
            }
        });
        combo_ciudad = new Ext.form.ComboBox({
            xtype: 'combobox',
            store: storeCiudad,
            labelAlign: 'left',
            emptyText: 'Escriba y Seleccione Ciudad',
            name: 'idciudad',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: 'Ciudad',
            width: 300,
            //allowBlank: false,

            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(false);
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);
                        storeParroquia.proxy.extraParams = {idcanton: combo.getValue()};
                        storeParroquia.load();

                    }},
                change: {fn: function(combo, newValue, oldValue) {
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                    }}
            }

        });




        Ext.define('ParroquiasList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', type: 'int'},
                {name: 'nombre', type: 'string'}
            ]
        });

        storeParroquia = Ext.create('Ext.data.Store', {
            model: 'ParroquiasList',
            proxy: {
                type: 'ajax',
                url: url_lista_parroquias,
                reader: {
                    type: 'json',
                    root: 'parroquias'
                }
            }
        });
        var combo_parroquia = new Ext.form.ComboBox({
            name: 'idparroquia',
            labelAlign: 'left',
            fieldLabel: 'Parroquia',
            anchor: '100%',
            disabled: true,
            width: 200,
            emptyText: 'Seleccione parroquia',
            store: storeParroquia,
            displayField: 'nombre',
            valueField: 'id',
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            //allowBlank: false,
            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);
                        sector_store.proxy.extraParams = {idparroquia: combo.getValue()};
                        sector_store.load();

                    }},
                change: {fn: function(combo, newValue, oldValue) {
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                    }}
            }
        });

        //CREAMOS DATA STORE PARA FACTURAS
        Ext.define('modelSectores', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', type: 'string'},
                {name: 'nombre', type: 'string'}
            ]
        });
        var sector_store = Ext.create('Ext.data.Store', {
            autoLoad: false,
            model: "modelSectores",
            proxy: {
                type: 'ajax',
                url: url_lista_sectores,
                reader: {
                    type: 'json',
                    root: 'sectores'
                }
            }
        });
        var combo_sector = new Ext.form.ComboBox({
            xtype: 'combobox',
            store: sector_store,
            labelAlign: 'left',
            name: 'idsector',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: 'Sector',
            width: 325,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            //allowBlank: false,	
            emptyText: 'Seleccione Sector',
            disabled: true,
            listeners: {
                select:
                    function(e) {
                        //storeValoresFact.load({params: {fact:e.value}});

                    },
                click: {
                    element: 'el', //bind to the underlying el property on the panel
                    fn: function() {
                        //estado_id='';
                        //facturas_store.removeAll();
                        //facturas_store.load();
                    }
                }
            }
        });


        var form = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 130,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0'
            },
            url: url_grabar,
            items: [
                {
                    xtype: 'textfield',
                    fieldLabel: 'Nombre',
                    labelAlign: 'left',
                    name: 'nombre',
                    allowBlank: false
                },
                combo_ciudad,
                combo_parroquia,
                combo_sector,
                {
                    xtype: 'textareafield',
                    fieldLabel: 'Direccion',
                    labelAlign: 'left',
                    name: 'direccion',
                    value: '',
                    allowBlank: false,
                    anchor: '100%'
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Email',
                    labelAlign: 'left',
                    name: 'email',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Telefono',
                    labelAlign: 'left',
                    name: 'telefono',
                    allowBlank: false
                },
                {
                    xtype: 'hiddenfield',
                    name: 'idPto',
                    value: idPto
                }

            ],
            buttons: [{
                    text: 'Cancel',
                    handler: function() {
                        this.up('form').getForm().reset();
                        this.up('window').destroy();
                    }
                }, {
                    text: 'Grabar',
                    name: 'grabar',
                    handler: function() {
                        var form1 = this.up('form').getForm();
                        if (form1.isValid()) {
                            form1.submit({
                                waitMsg: "Procesando",
                                success: function(form1, action) {
                                    Ext.Msg.alert('Success', 'Los datos fueron ingresados con exito');
                                    if (store) {
                                        store.load();
                                    }
                                    form1.reset();
                                    form1.destroy();
                                    this.up('window').destroy();
                                },
                                failure: function(form1, action) {
                                    console.log(action.result.errors.error);
                                    Ext.Msg.alert('Failed', 'Error al ingresar los datos, por favor comunicarse con el departamento de Sistemas');
                                    form1.reset();
                                    form1.destroy();
                                    this.up('window').destroy();
                                }
                            });
                        }
                        else {
                            Ext.Msg.alert('Failed', 'Falta ingresar datos.');
                        }
                    }
                }]
        });

        winDetalle = Ext.widget('window', {
            title: 'Ingresar Datos de Envio',
            closeAction: 'hide',
            closable: false,
            width: 350,
            height: 380,
            minHeight: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form
        });

    }

    winDetalle.show();

}


function winDatosEnvio(data, boolEdit) {
    var nombreEnvio = "";
    var direccionEnvio = "";
    var telefonoEnvio = "";
    var emailEnvio = "";
    var ciudadEnvio = "";
    var parroquiaEnvio = "";
    var sectorEnvio = "";
    var id_ciudadEnvio = "";
    var id_parroquiaEnvio = "";
    var id_sectorEnvio = "";

    if (boolEdit)
    {
        nombreEnvio = data.nombreEnvio;
        direccionEnvio = data.direccionEnvio;
        telefonoEnvio = data.telefonoEnvio;
        emailEnvio = data.emailEnvio;

        ciudadEnvio = data.ciudadEnvio;
        parroquiaEnvio = data.parroquiaEnvio;
        sectorEnvio = data.sectorEnvio;
        id_ciudadEnvio = data.id_ciudadEnvio;
        id_parroquiaEnvio = data.id_parroquiaEnvio;
        id_sectorEnvio = data.id_sectorEnvio;
    }

    winDetalle = "";
    if (!winDetalle) {
        Ext.define('CiudadesList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', type: 'int'},
                {name: 'nombre', type: 'string'}
            ]
        });
        storeCiudad = Ext.create('Ext.data.Store', {
            model: 'CiudadesList',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: url_lista_ciudades,
                reader: {
                    type: 'json',
                    root: 'ciudades'
                }
            }
        });
        combo_ciudad = new Ext.form.ComboBox({
            xtype: 'combobox',
            store: storeCiudad,
            labelAlign: 'left',
            emptyText: 'Escriba y Seleccione Ciudad',
            name: 'idciudad',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: 'Ciudad',
            width: 300,
            //allowBlank: false,
            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(false);
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);
                        storeParroquia.proxy.extraParams = {idcanton: combo.getValue()};
                        storeParroquia.load();

                    }},
                change: {fn: function(combo, newValue, oldValue) {
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                    }}
            }
        });

        Ext.define('ParroquiasList', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', type: 'int'},
                {name: 'nombre', type: 'string'}
            ]
        });
        storeParroquia = Ext.create('Ext.data.Store', {
            model: 'ParroquiasList',
            proxy: {
                type: 'ajax',
                url: url_lista_parroquias,
                reader: {
                    type: 'json',
                    root: 'parroquias'
                }
            }
        });
        var combo_parroquia = new Ext.form.ComboBox({
            name: 'idparroquia',
            labelAlign: 'left',
            fieldLabel: 'Parroquia',
            anchor: '100%',
            disabled: true,
            width: 200,
            emptyText: 'Seleccione parroquia',
            store: storeParroquia,
            displayField: 'nombre',
            valueField: 'id',
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            //allowBlank: false,
            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);
                        sector_store.proxy.extraParams = {idparroquia: combo.getValue()};
                        sector_store.load();

                    }},
                change: {fn: function(combo, newValue, oldValue) {
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                    }}
            }
        });

        //CREAMOS DATA STORE PARA FACTURAS
        Ext.define('modelSectores', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id', type: 'string'},
                {name: 'nombre', type: 'string'}
            ]
        });
        var sector_store = Ext.create('Ext.data.Store', {
            autoLoad: false,
            model: "modelSectores",
            proxy: {
                type: 'ajax',
                url: url_lista_sectores,
                reader: {
                    type: 'json',
                    root: 'sectores'
                }
            }
        });
        var combo_sector = new Ext.form.ComboBox({
            xtype: 'combobox',
            store: sector_store,
            labelAlign: 'left',
            name: 'idsector',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: 'Sector',
            width: 325,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            //allowBlank: false,	
            emptyText: 'Seleccione Sector',
            disabled: true,
            listeners: {
                select:
                    function(e) {
                        //storeValoresFact.load({params: {fact:e.value}});

                    },
                click: {
                    element: 'el', //bind to the underlying el property on the panel
                    fn: function() {
                        //estado_id='';
                        //facturas_store.removeAll();
                        //facturas_store.load();
                    }
                }
            }
        });

        var form = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,
            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 100,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0'
            },
            url: url_grabar,
            items:
                [
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Nombre',
                        labelAlign: 'left',
                        name: 'nombre',
                        value: nombreEnvio,
                        allowBlank: false,
                        maxLength: 60,
                        maxLengthText: "Esta permitido ingresar hasta {0} caracteres",
                        blankText: "El nombre es obligatorio"
                    },
                    combo_ciudad,
                    combo_parroquia,
                    combo_sector,
                    {
                        xtype: 'textareafield',
                        fieldLabel: 'Direccion',
                        labelAlign: 'left',
                        name: 'direccion',
                        value: direccionEnvio,
                        allowBlank: false,
                        anchor: '100%',
                        blankText: "La direccion es obligatoria"
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Email',
                        labelAlign: 'left',
                        name: 'email',
                        value: emailEnvio,
                        allowBlank: false,
                        maxLength: 100,
                        maxLengthText: "Esta permitido ingresar hasta {0} caracteres",
                        blankText: "El email es obligatorio"
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Telefono',
                        labelAlign: 'left',
                        name: 'telefono',
                        value: telefonoEnvio,
                        allowBlank: false,
                        maxLength: 10,
                        maxLengthText: "Esta permitido ingresar hasta {0} caracteres",
                        blankText: "El telefono es obligatorio"
                    },
                    {
                        xtype: 'hiddenfield',
                        name: 'idPto',
                        value: data.idPto
                    },
                    {
                        xtype: 'hiddenfield',
                        name: 'ant_id_ciudad',
                        value: id_ciudadEnvio
                    },
                    {
                        xtype: 'hiddenfield',
                        name: 'ant_id_parroquia',
                        value: id_parroquiaEnvio
                    },
                    {
                        xtype: 'hiddenfield',
                        name: 'ant_id_sector',
                        value: id_sectorEnvio
                    },
                    {
                        xtype: 'hiddenfield',
                        name: 'modifica',
                        value: boolEdit
                    }
                ],
            buttons: [
                {
                    text: 'Cancel',
                    handler: function() {
                        this.up('form').getForm().reset();
                        winDetalle.destroy();
                    }
                }, {
                    text: 'Grabar',
                    name: 'grabar',
                    handler: function() {
                        var form1 = this.up('form').getForm();
                        if (form1.isValid()) {
                            form1.submit({
                                waitMsg: "Procesando",
                                success: function(form1, action) {
                                    Ext.Msg.alert('Success', 'Los datos fueron ingresados con exito');
                                    if (store) {
                                        store.load();
                                    }
                                    form1.reset();
                                    form1.destroy();

                                    winDetalle.destroy();
                                },
                                failure: function(form1, action) {
                                    console.log(action.result.errors.error);
                                    Ext.Msg.alert('Failed', 'Error al ingresar los datos, por favor comunicarse con el departamento de Sistemas');
                                    form1.reset();
                                    form1.destroy();

                                    winDetalle.destroy();
                                }
                            });
                        }
                        else {
                            Ext.Msg.alert('Failed', 'Falta ingresar datos.');
                        }
                    }
                }
            ]
        });

        var tituloForm = "";
        if (boolEdit)
            tituloForm = "Modificar Datos de Envio";
        else
            tituloForm = "Ingresar Datos de Envio";

        winDetalle = Ext.widget('window', {
            title: tituloForm,
            closeAction: 'hide',
            closable: false,
            width: 400,
            height: 420,
            minHeight: 350,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form
        });

    }

    winDetalle.show();


    if (boolEdit)
    {
        /*ciudadEnvio = data.ciudadEnvio;
         parroquiaEnvio = data.parroquiaEnvio;
         sectorEnvio = data.sectorEnvio*/
        if (ciudadEnvio != "")
        {
            Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
            Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(false);
            Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);

            storeParroquia.proxy.extraParams = {idcanton: id_ciudadEnvio};
            storeParroquia.load();

            if (parroquiaEnvio != "")
            {
                Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);

                sector_store.proxy.extraParams = {idparroquia: id_parroquiaEnvio};
                sector_store.load();
            }

            /*        
             combo_ciudad.setValue(id_ciudadEnvio);
             combo_parroquia.setValue(id_parroquiaEnvio);
             combo_sector.setValue(id_sectorEnvio);
             */

            combo_ciudad.setRawValue(ciudadEnvio);
            combo_parroquia.setRawValue(parroquiaEnvio);
            combo_sector.setRawValue(sectorEnvio);
        }
    }

}



