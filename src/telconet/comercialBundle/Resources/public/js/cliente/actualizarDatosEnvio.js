Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var ciudadGlobal;
var parroquiaGlobal;
var sectorGlobal;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.1 05-07-2017
 * @since 1.0
 * Se agrega validación para números telefónicos de Panamá
 * 
 **/
function winDatosEnvioActualizar(data, boolEdit) {
    
    var intNumeroMaxCorreos            = parseInt(document.getElementById("numeroMaxCorreosDatoEnvio").value);
    var intNumeroMaxTelefonos          = parseInt(document.getElementById("numeroMaxTelefonosDatoEnvio").value);
    var nombreDatoEnvio                = "";
    var direccionDatoEnvio             = "";
    var telefonoDatoEnvio              = "";
    var correoElectronicoDatoEnvio     = "";
    var ciudadDatoEnvio                = "";
    var parroquiaDatoEnvio             = "";
    var sectorDatoEnvio                = "";
    var id_ciudadEnvio                 = "";
    var id_parroquiaEnvio              = "";
    var id_sectorEnvio                 = "";
    var id_punto                       = data.idPto;
    
    if (boolEdit)
    {
        nombreDatoEnvio             = data.nombreEnvio;
        direccionDatoEnvio          = data.direccionEnvio;
        telefonoDatoEnvio           = data.telefonoEnvio;
        correoElectronicoDatoEnvio  = data.emailEnvio;
        ciudadDatoEnvio             = data.ciudadEnvio;
        parroquiaDatoEnvio          = data.parroquiaEnvio;
        sectorDatoEnvio             = data.sectorEnvio;
        
        id_ciudadEnvio              = data.id_ciudadEnvio;
        id_parroquiaEnvio           = data.id_parroquiaEnvio;
        id_sectorEnvio              = data.id_sectorEnvio;
        id_punto                    = data.idPto;
    }
     
     var rowEditingTelefono = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText: 'Guardar',
        cancelBtnText: 'Cancelar',
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {
            canceledit: function(editor, e, eOpts) {
                e.store.remove(e.record);
            },
            afteredit: function(roweditor, changes, record, rowIndex) {
                var intCountGridDetalle = Ext.getCmp('gridCreaTelefonos').getStore().getCount();
                var selectionModel = Ext.getCmp('gridCreaTelefonos').getSelectionModel();
                selectionModel.select(0);
                 if (strNombrePais === 'PANAMA') {
                    if (!Utils.validateFoneMin7Max8(changes.newValues.strValorFormaContacto.trim())) {
                        Ext.Msg.alert('Error', 'El formato de teléfono no es correcto.! <br>' +
                            'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                            'Se permite un <b>mínimo de 7 dígitos y un máximo de 8 dígitos</b>. <br>' +
                            'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.');
                        rowEditingTelefono.startEdit(0, 0);
                        return false;
                    }
                }
                else
                {
                  if (!Utils.validateFoneMin8Max10(changes.newValues.strValorFormaContacto.trim())) {
                    Ext.Msg.alert('Error', 'El formato de teléfono no es correcto.! <br>' +
                        'Se permiten solo números entre <b>[0-9]</b>. <br>' +
                        'Se permite un <b>mínimo de 8 dígitos y un máximo de 10 dígitos</b>. <br>' +
                        'No se permiten <b>espacios o caracteres especiales</b>, favor revisar.');
                    rowEditingTelefono.startEdit(0, 0);
                    return false;
                  }
                }
                    

                if (intCountGridDetalle > 0)
                {
                    if (Ext.isEmpty(Ext.getCmp('gridCreaTelefonos').getStore().getAt(0).data.strValorFormaContacto.trim()))
                    {
                        Ext.Msg.alert('Error', 'No puede ingresar registros en blanco.');
                        rowEditingTelefono.cancelEdit();
                        selectionModel.select(0);
                        rowEditingTelefono.startEdit(0, 0);
                        return false;
                    }
                }
                for (var i = 1; i < intCountGridDetalle; i++)
                {
                    if (Ext.getCmp('gridCreaTelefonos').getStore().getAt(i).get('strValorFormaContacto') === changes.newValues.strValorFormaContacto.trim())
                    {
                        Ext.Msg.alert('Error', 'Este numero de telefono ya se encuentra previamente ingresada.');
                        rowEditingTelefono.startEdit(0, 0);
                        break;
                    }
                }
            }
        }
    });

    var rowEditingCorreoElectronico = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText: 'Guardar',
        cancelBtnText: 'Cancelar',
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {
            canceledit: function(editor, e, eOpts) {
                e.store.remove(e.record);
            },
            afteredit: function(roweditor, changes, record, rowIndex) {
                var intCountGridDetalle = Ext.getCmp('gridCreaCorreoElectronico').getStore().getCount();
                var selectionModel = Ext.getCmp('gridCreaCorreoElectronico').getSelectionModel();
                selectionModel.select(0);
                
                if (!Utils.validateMail(changes.newValues.strValorFormaContacto.trim())) {
                    Ext.Msg.alert('Error', 'El formato de correo no es correcto, favor revisar.');
                    rowEditingCorreoElectronico.startEdit(0, 0);
                    return false;
                }

                if (intCountGridDetalle > 0)
                {
                    if (Ext.isEmpty(Ext.getCmp('gridCreaCorreoElectronico').getStore().getAt(0).data.strValorFormaContacto.trim()))
                    {
                        Ext.Msg.alert('Error', 'No puede ingresar registros en blanco.');
                        rowEditingCorreoElectronico.cancelEdit();
                        selectionModel.select(0);
                        rowEditingCorreoElectronico.startEdit(0, 0);
                        return false;
                    }
                }
                for (var i = 1; i < intCountGridDetalle; i++)
                {
                    if (Ext.getCmp('gridCreaCorreoElectronico').getStore().getAt(i).get('strValorFormaContacto') === changes.newValues.strValorFormaContacto.trim())
                    {
                        Ext.Msg.alert('Error', 'Este correo electronico ya se encuentra previamente ingresada.');
                        rowEditingCorreoElectronico.startEdit(0, 0);
                        break;
                    }
                }
            }
        }
    });
    
    /* INI - CORREO ELECTRONICO */
    //Model memory - Correo electronico
    Ext.define('correoElectronicoFormaContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strValorFormaContacto', type: 'string'}
        ]
    });

    //Store memory - Correo electronico
    var storeCreaCorreoElectronicoDatosEnvio = Ext.create('Ext.data.Store', {
        name: 'storeCreaCorreoElectronicoDatosEnvio',
        id: 'storeCreaCorreoElectronicoDatosEnvio',
        pageSize: 5,
        model: 'correoElectronicoFormaContactoModel',
        autoLoad: true,
        proxy: {
            type: 'memory'
        }
    });

    
    //Boton - Agregar correo electronico
    var btnCrearCorreoElectronico = Ext.create('Ext.button.Button', {
        text: 'Agregar Correo electronico',
        width: 160,
        iconCls: 'button-grid-crearSolicitud-without-border',
        handler: function() {

            rowEditingCorreoElectronico.cancelEdit();

            var recordParamDet = Ext.create('correoElectronicoFormaContactoModel', {
                strValorFormaContacto: ''
            });
            storeCreaCorreoElectronicoDatosEnvio.insert(0, recordParamDet);
            rowEditingCorreoElectronico.startEdit(0, 0);
            if (Ext.getCmp('gridCreaCorreoElectronico').getStore().getCount() > 1)
            {
                if (Ext.isEmpty(Ext.getCmp('gridCreaCorreoElectronico').getStore().getAt(1).data.strValorFormaContacto.trim()))
                {
                    Ext.Msg.alert('Error', 'No puede ingresar registros en blanco.');
                    var selectionModel = Ext.getCmp('gridCreaCorreoElectronico').getSelectionModel();
                    rowEditingCorreoElectronico.cancelEdit();
                    storeCreaCorreoElectronicoDatosEnvio.remove(selectionModel.getSelection());
                    selectionModel.select(0);
                    rowEditingCorreoElectronico.startEdit(0, 0);
                }
            }
        }
    });

    //Boton - Eliminar correo electronico
    var btnDeleteCorreoElectronico = Ext.create('Ext.button.Button', {
        text: 'Eliminar Correo electronico',
        width: 160,
        iconCls: 'button-grid-quitarFacturacionElectronica-without-border',
        handler: function() {
            var gridCreaCorreoElectronico = Ext.getCmp('gridCreaCorreoElectronico');
            var selectionModel = gridCreaCorreoElectronico.getSelectionModel();
            rowEditingCorreoElectronico.cancelEdit();
            storeCreaCorreoElectronicoDatosEnvio.remove(selectionModel.getSelection());
            if (storeCreaCorreoElectronicoDatosEnvio.getCount() > 0) {
                selectionModel.select(0);
            }
        }
    });

    //Toolbar - Correo electronico
    var toolbarCorreoElectronico = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [{xtype: 'tbfill'},
                btnCrearCorreoElectronico,
                btnDeleteCorreoElectronico
            ]
    });

    //Model memory - Telefonos
    Ext.define('telefonosFormaContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strValorFormaContacto', type: 'string'}
        ]
    });

    //Store memory - Telefonos
    var storeCreaTelefonosDatosEnvio = Ext.create('Ext.data.Store', {
        name:'storeCreaTelefonosDatosEnvio',
        id: 'storeCreaTelefonosDatosEnvio',
        pageSize: 5,
        autoDestroy: true,
        model: 'telefonosFormaContactoModel',
        proxy: {
            type: 'memory'
        }
    });

    //Boton - Agregar telefonos
    var btnCrearTelefono = Ext.create('Ext.button.Button', {
        text: 'Agregar Telefonos',
        width: 160,
        iconCls: 'button-grid-crearSolicitud-without-border',
        handler: function() {

            rowEditingTelefono.cancelEdit();

            var recordParamDet = Ext.create('telefonosFormaContactoModel', {
                strValorFormaContacto: ''
            });
            storeCreaTelefonosDatosEnvio.insert(0, recordParamDet);
            rowEditingTelefono.startEdit(0, 0);
            if (Ext.getCmp('gridCreaTelefonos').getStore().getCount() > 1)
            {

                if ( Ext.isEmpty(Ext.getCmp('gridCreaTelefonos').getStore().getAt(1).data.strValorFormaContacto.trim()) )
                {
                    Ext.Msg.alert('Error', 'No puede ingresar registros en blanco.');
                    var selectionModel = Ext.getCmp('gridCreaTelefonos').getSelectionModel();
                    rowEditingTelefono.cancelEdit();
                    storeCreaTelefonosDatosEnvio.remove(selectionModel.getSelection());
                    selectionModel.select(0);
                    rowEditingTelefono.startEdit(0, 0);
                }
            }
        }
    });

    //Boton - Eliminar telefonos
    var btnDeleteTelefono = Ext.create('Ext.button.Button', {
        text: 'Eliminar Telefonos',
        width: 160,
        iconCls: 'button-grid-quitarFacturacionElectronica-without-border',
        handler: function() {
            var gridCreaTelefonos = Ext.getCmp('gridCreaTelefonos');
            var selectionModel = gridCreaTelefonos.getSelectionModel();
            rowEditingTelefono.cancelEdit();
            storeCreaTelefonosDatosEnvio.remove(selectionModel.getSelection());
            if (storeCreaTelefonosDatosEnvio.getCount() > 0) {
                selectionModel.select(0);
            }
        }
    });

    //Toolbar - telefonos
    var toolbarTelefono = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [{xtype: 'tbfill'},
                btnCrearTelefono,
                btnDeleteTelefono
            ]
    });

var formCreaCorreoElectronico = Ext.create('Ext.form.Panel', {
        id: 'formCreaCorreoElectronico',
        height: 190,
        width: 812,
        bodyStyle: 'padding:10px 10px 0; background:#FFFFFF;',
        bodyPadding: 10,
        autoScroll: false,
        layout: {
            type: 'table',
            columns: 1,
            tableAttrs: {
                style: {
                    width: '100%',
                    height: '100%'
                }
            },
            tdAttrs: {
                align: 'center',
                valign: 'middle'
            }
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Dato correo electronico',
                style: 'margin-bottom: 360px',
                layout: {
                    type: 'vbox',
                    align: 'left',
                    pack: 'left'
                },
                items: [
                    {
                        xtype: 'grid',
                        store: storeCreaCorreoElectronicoDatosEnvio,
                        plugins: [rowEditingCorreoElectronico],
                        dockedItems: [toolbarCorreoElectronico],
                        id: 'gridCreaCorreoElectronico',
                        height: 125,
                        columns: [
                            {
                                header: "Correo electronico",
                                dataIndex: 'strValorFormaContacto',
                                width: 350,
                                editor: 'textfield'
                            }
                        ],
                        listeners: {
                                afterrender: function(grid, evt) {

                                 if(!Ext.isEmpty( correoElectronicoDatoEnvio ))
                                 {
                                    var arrayGruposCorreo;
                                    var tieneMasDeUnCorreo;
                                    
                                    if (correoElectronicoDatoEnvio.indexOf(";") >= 0)
                                    {
                                         arrayGruposCorreo  = correoElectronicoDatoEnvio.split(";");
                                         tieneMasDeUnCorreo = true;
                                    }
                                    else
                                    {
                                         arrayGruposCorreo  = correoElectronicoDatoEnvio;
                                         tieneMasDeUnCorreo = false;
                                    }
                                    
                                    var strValorCorreo        = '';
                                    var arrayData             = [];

                                    if ( tieneMasDeUnCorreo )
                                    {
                                        for (intCorreos in arrayGruposCorreo) {
                                            strValorCorreo   = arrayGruposCorreo[intCorreos];

                                            if( !Ext.isEmpty( strValorCorreo )){
                                                arrayData.push({
                                                    'strValorFormaContacto':   strValorCorreo
                                                });
                                            }
                                        }
                                    }
                                    else
                                    {
                                        strValorCorreo  = arrayGruposCorreo;
                                        
                                        if( !Ext.isEmpty( strValorCorreo )){
                                                arrayData.push({
                                                    'strValorFormaContacto':   strValorCorreo
                                                });
                                        }   
                                    }
                                    
                                    setTimeout(function(){
                                        // true - add new records to store
                                        Ext.getStore('storeCreaCorreoElectronicoDatosEnvio').loadData( arrayData, true );
                                    }, 500);
                                    
                                    
                                 }
                            }
                        }
                    }
                ]
            }
        ]
    });
    /* FIN - CORREO ELECTRONICO */
    
 /* INI - TELEFONOS */

var formCreaTelefono = Ext.create('Ext.form.Panel', {
        id: 'formCreaTelefono',
        height: 190,
        width: 812,
        bodyStyle: 'padding:10px 10px 0; background:#FFFFFF;',
        bodyPadding: 10,
        autoScroll: false,
        layout: {
            type: 'table',
            columns: 1,
            tableAttrs: {
                style: {
                    width: '100%',
                    height: '100%'
                }
            },
            tdAttrs: {
                align: 'center',
                valign: 'middle'
            }
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Dato Telefono',
                style: 'margin-bottom: 360px',
                layout: {
                    type: 'vbox',
                    align: 'left',
                    pack: 'left'
                },
                items: [
                    {
                        xtype: 'grid',
                        store: storeCreaTelefonosDatosEnvio,
                        plugins: [rowEditingTelefono],
                        dockedItems: [toolbarTelefono],
                        id: 'gridCreaTelefonos',
                        height: 125,
                        columns: [
                            {
                                header: "Numero de Telefono",
                                dataIndex: 'strValorFormaContacto',
                                width: 350,
                                editor: 'textfield'
                            }
                        ],
                         listeners: {
                                afterrender: function(grid, evt) {
                                 
                                 if(!Ext.isEmpty( telefonoDatoEnvio ))
                                 {
                                    var arrayGruposTelefono;
                                    var tieneMasDeUnTelefono;
                                    
                                    if (telefonoDatoEnvio.indexOf(";") >= 0)
                                    {
                                         arrayGruposTelefono  = telefonoDatoEnvio.split(";");
                                         tieneMasDeUnTelefono = true;
                                    }
                                    else
                                    {
                                         arrayGruposTelefono  = telefonoDatoEnvio;
                                         tieneMasDeUnTelefono = false;
                                    }
                                    
                                    var strValorTelefono        = '';
                                    var arrayDataTelefono       = [];

                                    if ( tieneMasDeUnTelefono )
                                    {
                                        for (intTelefonos in arrayGruposTelefono) {
                                            strValorTelefono   = arrayGruposTelefono[intTelefonos];

                                            if( !Ext.isEmpty( strValorTelefono )){
                                                arrayDataTelefono.push({
                                                    'strValorFormaContacto':   strValorTelefono
                                                });
                                            }
                                        }
                                    }
                                    else
                                    {
                                        strValorTelefono  = arrayGruposTelefono;
                                        
                                        if( !Ext.isEmpty( strValorTelefono )){
                                                arrayDataTelefono.push({
                                                    'strValorFormaContacto':   strValorTelefono
                                                });
                                        }   
                                    }
                                    
                                    setTimeout(function(){
                                        // true - add new records to store
                                        Ext.getStore('storeCreaTelefonosDatosEnvio').loadData( arrayDataTelefono, true );
                                    }, 500);
                                    
                                }
                            }
                       }
                    }
                ]
            }
        ]
    });
    /* FIN - TELEFONOS */
    
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
            id: 'idCiudad',
            name: 'idCiudad',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: 'Ciudad',
            width: 300,
            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('combobox[name=idParroquia]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idParroquia]')[0].setDisabled(false);
                        storeParroquia.proxy.extraParams = {idcanton: combo.getValue()};
                        storeParroquia.load();
                        ciudadGlobal =  combo.getValue() ;

                    }},
                change: {fn: function(combo, newValue, oldValue) {
                        Ext.ComponentQuery.query('combobox[name=idParroquia]')[0].reset();
                    }},
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
       combo_parroquia = new Ext.form.ComboBox({
            xtype: 'combobox',
            name: 'idParroquia',
            id: 'idParroquia',
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
            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);
                        sector_store.proxy.extraParams = {idparroquia: combo.getValue()};
                        sector_store.load();
                        parroquiaGlobal =  combo.getValue();

                    }},
                change: {fn: function(combo, newValue, oldValue) {
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                    }},
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
        sector_store = Ext.create('Ext.data.Store', {
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
        
        combo_sector = new Ext.form.ComboBox({
            xtype: 'combobox',
            id: 'idsector',
            name: 'idsector',
            labelAlign: 'left',
            fieldLabel: 'Sector',
            disabled: true,
            width: 325,	
            emptyText: 'Seleccione Sector',
            store: sector_store,
            valueField: 'id',
            displayField: 'nombre',
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            listeners: {
                 select: {fn: function(combo, value) {
                        sectorGlobal =  combo.getValue() ;
                    }}
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
                        name: 'nombre',
                        id: 'nombre',
                        fieldLabel: 'Nombre',
                        labelAlign: 'left',
                        value: nombreDatoEnvio,
                        maxLength: 150,
                        maxLengthText: "Esta permitido ingresar hasta {0} caracteres",
                        blankText: "El nombre es obligatorio"
                    },
                    combo_ciudad,
                    combo_parroquia,
                    combo_sector,
                    {
                        xtype: 'textareafield',
                        id:'direccion',    
                        name: 'direccion',
                        fieldLabel: 'Direccion',
                        labelAlign: 'left',
                        value: direccionDatoEnvio,
                        allowBlank: false,
                        anchor: '100%',
                        blankText: "La direccion es obligatoria"
                    }, 
                     formCreaCorreoElectronico,
                     formCreaTelefono,
                    {
                    xtype: 'hiddenfield',
                    name: 'idPto',
                    value: id_punto
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
                        
                        if ( Ext.isEmpty( Ext.getCmp('nombre').value ) ) {
                            
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Debe ingresar nombre al dato de envio.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                        
                        if (Ext.isEmpty( ciudadGlobal ) && Ext.isEmpty( ciudadDatoEnvio )) {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Debe seleccionar ciudad al dato de envio.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                        
                        if (Ext.isEmpty( combo_parroquia.getValue() )) {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Debe seleccionar parroquia al dato de envio.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                        
                        if (Ext.isEmpty( combo_sector.getValue() )) {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Debe seleccionar sector al dato de envio.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                        
                        if ( Ext.isEmpty( Ext.getCmp('direccion').value ) ) {
                            
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Debe ingresar direccion al dato de envio.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                        
                        var arraygridCreaCorreoElectronicoDE       = Ext.getCmp('gridCreaCorreoElectronico');
                        var intCounterCorreoElectronico            = 0;
                        var strAcumuladorCorreoElectronico         = '';

                        if (arraygridCreaCorreoElectronicoDE.getStore().getCount() !== 0)
                        {
                            strAcumuladorCorreoElectronico = '';
                            for (var intCounterStore = 0;
                                 intCounterStore < arraygridCreaCorreoElectronicoDE.getStore().getCount(); intCounterStore++)
                            {
                                boolCorreo                     = true;
                                intCounterCorreoElectronico    = intCounterCorreoElectronico + 1;
                                strAcumuladorCorreoElectronico = strAcumuladorCorreoElectronico + ';' + 
                                arraygridCreaCorreoElectronicoDE.getStore().getAt(intCounterStore).data.strValorFormaContacto.trim();
                            }
                            
                            if ( boolCorreo ) 
                            {

                                if ( intCounterCorreoElectronico > intNumeroMaxCorreos )
                                {
                                   Ext.MessageBox.show({
                                       title: 'Alerta',
                                       msg: 'Solo se permite el ingreso de '+intNumeroMaxCorreos+' correos electronicos.',
                                       buttons: Ext.MessageBox.OK,
                                       icon: Ext.MessageBox.WARNING
                                    });
                                    return false;
                                }
                                else
                                {   
                                    if (strAcumuladorCorreoElectronico.indexOf(";") >= 0)
                                    {
                                        strAcumuladorCorreoElectronico = strAcumuladorCorreoElectronico.substr(1,strAcumuladorCorreoElectronico.length);
                                    }
                                }
                            } 
                            else 
                            {
                                Ext.MessageBox.show({
                                    title: 'Alerta',
                                    msg: 'Debe ingresar al menos un correo electronico valido.',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.WARNING
                                });
                                return false;
                            }
                        } 
                        else 
                        {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Debe ingresar al menos un correo electronico valido.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                        
                        //Telefonos
                        var arraygridCreaTelefonoDE       = Ext.getCmp('gridCreaTelefonos');
                        var intCounterTelefono            = 0;
                        var strAcumuladorTelefono         = '';

                        if (arraygridCreaTelefonoDE.getStore().getCount() !== 0)
                        {
                            strAcumuladorTelefono = ''; 
                            for (var intCounterTelefonoStore = 0;
                                 intCounterTelefonoStore < arraygridCreaTelefonoDE.getStore().getCount(); intCounterTelefonoStore++)
                            {
                                    boolTelefono            = true;
                                    intCounterTelefono      = intCounterTelefono + 1;
                                    strAcumuladorTelefono   = strAcumuladorTelefono + ';' +
                                    arraygridCreaTelefonoDE.getStore().getAt(intCounterTelefonoStore).data.strValorFormaContacto.trim();
                            }

                            if ( boolTelefono ) 
                            {

                                if ( intCounterTelefono > intNumeroMaxTelefonos )
                                {
                                   Ext.MessageBox.show({
                                       title: 'Alerta',
                                       msg: 'Solo se permite el ingreso de '+ intNumeroMaxTelefonos +' telefonos.',
                                       buttons: Ext.MessageBox.OK,
                                       icon: Ext.MessageBox.WARNING
                                    });
                                    return false;
                                }
                                else
                                {
                                    if (strAcumuladorTelefono.indexOf(";") >= 0)
                                    {
                                        strAcumuladorTelefono = strAcumuladorTelefono.substr(1,strAcumuladorTelefono.length);
                                    }
                                }
                            } 
                            else 
                            {
                                Ext.MessageBox.show({
                                    title: 'Alerta',
                                    msg: 'Debe ingresar al menos un numero de telefono valido.',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.WARNING
                                });
                                return false;
                            }
                        } 
                        else 
                        {
                            Ext.MessageBox.show({
                                title: 'Alerta',
                                msg: 'Debe ingresar al menos un numero de telefono valido.',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.WARNING
                            });
                            return false;
                        }
                        
                        var form1 = this.up('form').getForm();
                        if (form1.isValid()) {
                            form1.submit({
                                params: {
                                    email:      strAcumuladorCorreoElectronico,
                                    telefono:   strAcumuladorTelefono,
                                    datoEnvioMasivo: false
                                },
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
            closable: false,
            height: 690,
            width: 650,
            minHeight: 350,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form,
            onEsc: function() {
                    var me = this;
                    me.destroy();
            }
        });

    }

    winDetalle.show();

    if (boolEdit)
    {
        if (ciudadDatoEnvio != "")
        {
            Ext.ComponentQuery.query('combobox[name=idParroquia]')[0].reset();
            Ext.ComponentQuery.query('combobox[name=idParroquia]')[0].setDisabled(false);
            Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);

            storeParroquia.proxy.extraParams = {idcanton: id_ciudadEnvio};
            storeParroquia.load();

            if (parroquiaDatoEnvio != "")
            {
                Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);

                sector_store.proxy.extraParams = {idparroquia: id_parroquiaEnvio};
                sector_store.load();
            }

            combo_ciudad.setRawValue(ciudadDatoEnvio);
            combo_parroquia.setRawValue(parroquiaDatoEnvio);
            combo_sector.setRawValue(sectorDatoEnvio);
            combo_sector.setValue(id_sectorEnvio);
        }
    }
}