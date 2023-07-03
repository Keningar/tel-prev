Ext.require([
    '*'
]);

var esRequerido = (prefijoEmpresa == 'MD' ||  prefijoEmpresa == 'EN');

var esObligatorio = true;

Ext.onReady(function(){

    if (prefijoEmpresa == 'TN')
    {                
        $('#idcanton').attr('required','required');
        $('#idparroquia').attr('required','required');
        $('#idsector').attr('required','required');
    } 
    $( "#infopuntotype_tipoNegocioId" ).change(function() {
      if (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN')
      {
          generaLogin();
      }
    }) 

   
    Ext.define('ListModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id', type:'int'},
            {name:'nombre', type:'string'}
        ]
    });

    Ext.define('ListModelVendedor', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'login', type:'string'},
            {name:'nombre', type:'string'}
        ]
    });
    
    storeVendedores = Ext.create('Ext.data.Store', 
    {
        model: 'ListModelVendedor',
        autoLoad: true,
        proxy: 
            {
            type: 'ajax',
            url : url_vendedores,
            reader: 
                {
                type: 'json',
                root: 'registros'
            }
        }        
    });		 
    
    if ('S' === strAplicaTipoOrigen && 'S' === strIsGrantedTipoOrigen)
    {
        /*-- MODELO PARA LA PETICIÓN DE TIPO DE ORIGEN --*/
        Ext.define('ListModelTipoOrigen', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'valor1', type: 'string'},
                {name: 'valor2', type: 'string'}
            ]
        });
        /*-- STORE DE TIPO DE ORIGEN --*/
        objStoreTipoOrigen = Ext.create('Ext.data.Store',
                {
                    model: 'ListModelTipoOrigen',
                    autoLoad: true,
                    proxy:
                            {
                                type: 'ajax',
                                url: strUrlTipoOrigenEmpresa,
                                reader:
                                        {
                                            type: 'json',
                                            root: 'registros'
                                        }
                            },
                    listeners: {
                        load: function()
                        {
                            var rec = {valor1: 'Nuevo', valor2: ''};
                            this.insert(0, rec);
                        }
                    }

                });
        /*-- COMBOLIST DE TIPO DE ORIGEN --*/
        var objTipoOrigenList = new Ext.form.ComboBox(
                {
                    xtype: 'combobox',
                    store: objStoreTipoOrigen,
                    labelAlign: 'left',
                    name: 'tipoOrigenCaracteristica',
                    id: 'tipoOrigenCaracteristica',
                    valueField: 'valor2',
                    displayField: 'valor1',
                    fieldLabel: '',
                    width: 300,
                    allowBlank: true,
                    emptyText: 'Seleccione',
                    disabled: false,
                    renderTo: 'tipoOrigenDiv',
                    listeners:
                            {
                                select:
                                        {
                                            fn: function(combo, value)
                                            {
                                                $('#strTipoOrigenSelected').val(combo.getValue());
                                            }
                                        }
                            }
                });

        objStoreTipoOrigen.on('load', function()
        {
            objTipoOrigenList.setValue(strTipoOrigenSelected);
            objTipoOrigenList.setRawValue(strTipoOrigenDescripcion);
            $('#strTipoOrigenSelected').val(strTipoOrigenSelected);
        });
    }

    storeVendedores.on('load', function()
    {
        combo_vendedores.setValue(loginEmpleado);
        combo_vendedores.setRawValue(nombreEmpleado);
        $('#infopuntoextratype_loginVendedor').val(loginEmpleado);
    });

    
    var combo_vendedores = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            store: storeVendedores,
            labelAlign: 'left',
            name: 'idvendedor',
            id: 'idvendedor',
            valueField: 'login',
            displayField: 'nombre',
            fieldLabel: '',
            width: 290,
            allowBlank: false,
            emptyText: 'Seleccione Vendedor',
            disabled: false,
            renderTo: 'combo_vendedor',
            listeners:
                {
                    select:
                        {
                            fn: function(combo, value)
                            {
                                $('#infopuntoextratype_loginVendedor').val(combo.getValue());
                                ocultarDiv('div_errorvendedor');
                            }
                        },
                    click:
                        {
                            element: 'el',
                            fn: function()
                            {
                                storeVendedores.load();
                            }
                        }
                }
        });
    
    if (esRequerido)
    {
        Ext.define('ListModelCanal',
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'canal', type: 'string'},
                    {name: 'descripcion', type: 'string'}
                ]
            });

        Ext.define('ListModelPuntoVenta',
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'punto_venta', type: 'string'},
                    {name: 'descripcion', type: 'string'}
                ]
            });

        storeCanales = Ext.create('Ext.data.Store',
            {
                model: 'ListModelCanal',
                autoLoad: true,
                proxy:
                    {
                        type: 'ajax',
                        url: url_canales,
                        reader:
                            {
                                type: 'json',
                                root: 'canales'
                            }
                    }
            });

        storePuntoVenta = Ext.create('Ext.data.Store',
            {
                model: 'ListModelPuntoVenta',
                autoLoad: true,
                proxy:
                    {
                        type: 'ajax',
                        url: url_punto_venta,
                        reader: {
                            type: 'json',
                            root: 'puntos_venta'
                        }
                    }
            });

        var combo_canales = new Ext.form.ComboBox(
            {
                xtype: 'combobox',
                store: storeCanales,
                labelAlign: 'left',
                name: 'idCanal',
                valueField: 'canal',
                displayField: 'descripcion',
                fieldLabel: '',
                width: 250,
                allowBlank: false,
                emptyText: 'Seleccione Canal',
                editable: false,
                renderTo: 'Canales',
                listeners:
                    {
                        select:
                            {
                                fn: function(combo)
                                {
                                    $('#infopuntoextratype_canal').val(combo.getValue());
                                    $('#infopuntoextratype_punto_venta').val(null);
                                    ocultarDiv('div_errorcanal');
                                    combo_punto_venta.setValue(null);
                                    combo_punto_venta.setRawValue(null);
                                    storePuntoVenta.getProxy().extraParams.canal = combo.getValue();
                                    storePuntoVenta.load();
                                    if (combo.getValue() == 'CANAL_INTERNO')
                                    {
                                        capa = document.getElementById('label_punto_venta');
                                        capa.style.display = 'none';

                                        capa = document.getElementById('PuntosVenta');
                                        esObligatorio = false;
                                        combo_punto_venta.getEl().hide();
                                    }
                                    else
                                    {
                                        capa = document.getElementById('label_punto_venta');
                                        capa.style.display = 'block';

                                        capa = document.getElementById('PuntosVenta');
                                        esObligatorio = true;
                                        combo_punto_venta.getEl().show();
                                    }
                                }
                            },
                        click:
                            {
                                element: 'el'
                            }
                    }
            });

        var combo_punto_venta = new Ext.form.ComboBox(
            {
                xtype: 'combobox',
                store: storePuntoVenta,
                labelAlign: 'left',
                name: 'idPuntoVenta',
                valueField: 'punto_venta',
                displayField: 'descripcion',
                fieldLabel: '',
                width: 250,
                allowBlank: false,
                emptyText: 'Seleccione Punto de Venta',
                editable: false,
                renderTo: 'PuntosVenta',
                listeners:
                    {
                        select:
                            {
                                fn: function(combo)
                                {
                                    $('#infopuntoextratype_punto_venta').val(combo.getValue());
                                    ocultarDiv('div_errorpuntoventa');
                                }
                            },
                        click:
                            {
                                element: 'el'
                            }
                    }
            });

        storePuntoVenta.on('load', function()
        {
            if (canal != '')
            {
                combo_punto_venta.setValue(puntoVenta);
                combo_punto_venta.setRawValue(puntoVentaDesc);
            }
        });
        $('#infopuntoextratype_canal').val(canal);
        $('#infopuntoextratype_punto_venta').val(puntoVenta);

        combo_canales.setValue(canal);
        combo_canales.setRawValue(canalDesc);
        combo_punto_venta.setValue(puntoVenta);
        combo_punto_venta.setRawValue(puntoVentaDesc);

        if(canal != '' && boolCamposLectura == true)
        {
            combo_punto_venta.disable();
            combo_canales.disable();

        }

    }
    
    /*
     * 
     * DATASTORES
     * 
     */

    storePtosCobertura = Ext.create('Ext.data.Store',
        {
            model: 'ListModel',
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: url_puntoscobertura,
                    reader:
                        {
                            type: 'json',
                            root: 'jurisdicciones'
                        }
                },
            listeners:
                {
                    load: function(store)
                    {
                        if (esRequerido) {                           
                        let combo1 = Ext.getCmp("idptocobertura");
                        if (combo1)
                        {   combo1.setDisabled(false); 
                            store.each(function(record)
                            {
                                if (record.data.id == punto_cobertura_default)
                                {
                                    combo1.setValue(record.data.id);
                                    combo1.setDisabled(true); 
                                    $('#infopuntoextratype_ptoCoberturaId').val(record.data.id);
                                }
                            });
                        }
                      }
                    }
                }

        });

    storeCantones = Ext.create('Ext.data.Store',
        {
            model: 'ListModel',
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: url_cantones, 
                    reader:
                        {
                            type: 'json',
                            root: 'cantones'
                        }
                },
                 listeners:
                {
                    load: function(store)
                    {
                        if (esRequerido) {     
                        let combo1  = Ext.getCmp("idcanton");
                        if (combo1)
                        {
                            store.each(function(record)
                            {
                                if (record.data.id == canton_default)
                                {
                                    combo1.setValue(record.data.id);
                                    $('#infopuntoextratype_cantonId').val(record.data.id);
                                }
                            });
                        }
                      }
                    }
                }

        }); 

    storeParroquias = Ext.create('Ext.data.Store',
        {
            model: 'ListModel',
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: url_lista_parroquias,
                    extraParams : {idcanton: canton_default}, 
                    reader:
                        {
                            type: 'json',
                            root: 'parroquias'
                        }
                       
                }, 
                listeners:
                {
                    load: function(store)
                    {   if (esRequerido) {  
                           let combo1 = Ext.getCmp("idparroquia");
                            if (combo1)
                            {   
                                store.each(function(record)
                                {
                                    if (record.data.id == parroquia_default)
                                    {
                                        combo1.setValue(record.data.id);
                                        $('#infopuntoextratype_parroquiaId').val(record.data.id);
                                    }
                                });
                            }
                        }
                    }
                }
        });
  
    storeSectores = Ext.create('Ext.data.Store',
        {            
            model: "ListModel",
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: url_lista_sectores,
                    extraParams : {idparroquia: parroquia_default},
                    reader:
                        {
                            type: 'json',
                            root: 'sectores'
                        }
                },
                 listeners:
                {
                    load: function(store)
                    {  if (esRequerido) {  
                        let combo1  = Ext.getCmp("idsector");
                        if (combo1)
                        {   
                            store.each(function(record)
                            {
                                if (record.data.id == sector_default)
                                {
                                    combo1.setValue(record.data.id);
                                    $('#infopuntoextratype_sectorId').val(record.data.id);
                                }
                            });
                        }
                      }
                    }
                }
        });

    /*
     * 
     * DATASTORES EVENTS ONLOAD
     * 
     */

    storePtosCobertura.on('load', function()
    {  if (!esRequerido) 
        {
        coberturaId = null;

        if (typeof ptoCoberturaId !== typeof undefined && ptoCoberturaId != '')
        {
            coberturaId = ptoCoberturaId;
        }
        else
        {
            coberturaId = punto_cobertura_default;
        }

        rec = storePtosCobertura.findRecord('id', coberturaId);
        if (rec != null)
        {
            combo_ptoscobertura.select(parseInt(coberturaId), true);
            $('#infopuntoextratype_ptoCoberturaId').val(coberturaId);
            storeCantones.proxy.extraParams = {idjurisdiccion: coberturaId};
            storeCantones.load(); 
        }
       } 


    });

    storeCantones.on('load', function()
    {   if (!esRequerido) 
        {
        rec = storeCantones.findRecord('id', canton_default);
        if(rec != null)
        {
            combo_cantones.select(parseInt(canton_default), true);
            storeParroquias.proxy.extraParams = {idcanton: canton_default};
            storeParroquias.load();            
        }
        $('#infopuntoextratype_cantonId').val(canton_default);
       } 
    });

    storeParroquias.on('load', function()
    {  if (!esRequerido) 
        {
            
        rec = storeParroquias.findRecord('id', parroquia_default);
        if(rec != null)
        {
            combo_parroquias.select(parseInt(parroquia_default), true);
            storeSectores.proxy.extraParams = {idparroquia: parroquia_default};
            storeSectores.load(); 
        }
        $('#infopuntoextratype_parroquiaId').val(parroquia_default);
       } 

    });
    
    storeSectores.on('load', function()
    {
        if (!esRequerido) 
        {
        sector = null;
        
        if (typeof sectorId !== typeof undefined && sectorId != '')
        {
            sector = sectorId;
        }
        else
        {
            sector = sector_default;
        }
        
        rec = storeSectores.findRecord('id', sector);
        if(rec != null)
        {
            Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);
            combo_sector.select(parseInt(sector), true);
           
        }
        $('#infopuntoextratype_sectorId').val(sector);
                
        if (strEditaDatosGeograficos != 'S')
        {            
            Ext.getCmp('idptocobertura').disable();
            Ext.getCmp('idcanton').disable();
            Ext.getCmp('idparroquia').disable();
            Ext.getCmp('idsector').disable();            
        }
        } 
    });
    

    /*
     *
     * COMBOBOXES
     *  
     */
    
    combo_ptoscobertura = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            store: storePtosCobertura,
            labelAlign: 'left',
            emptyText: 'Escriba y Seleccione Pto Cobertura',
            name: 'idptocobertura',
            id: 'idptocobertura',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: '',
            width: 300,
            allowBlank: false,
            renderTo: 'combo_ptoscobertura',
            listeners:
                {
                    select:
                        {
                            fn: function(combo, value)
                            {
                                Ext.ComponentQuery.query('combobox[name=idcanton]')[0].reset();
                                Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                                Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                                Ext.ComponentQuery.query('combobox[name=idcanton]')[0].setDisabled(false);
                                Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(true);
                                Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);
                                
                                canton_default    = null;
                                parroquia_default = null;
                                sector_default    = null;
                                sectorId          = null;

                                $('#infopuntoextratype_cantonId').val('');
                                $('#infopuntoextratype_parroquiaId').val('');
                                $('#infopuntoextratype_sectorId').val('');
                                $('#infopuntoextratype_ptoCoberturaId').val(combo.getValue());
                                storeCantones.proxy.extraParams = {idjurisdiccion: combo.getValue()};
                                storeCantones.load();

                            }
                        }
                }
        });

    var strLblCanton = 'Seleccione Cant\u00F3n';
    var strLblParroquia = 'Seleccione Parroquia';
    var strLblSector = 'Seleccione Sector';
    if (strNombrePais === 'PANAMA')
    {
        strLblCanton = 'Seleccione Distrito';
        strLblParroquia = 'Seleccione Corregimiento';
    }
    combo_cantones = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            name: 'idcanton',
            id: 'idcanton',
            labelAlign: 'left',
            fieldLabel: '',
            anchor: '100%',
            width: 300,
            emptyText: strLblCanton,
            store: storeCantones,
            displayField: 'nombre',
            valueField: 'id',
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            renderTo: 'combo_cantones',
            listeners:
                {
                    select:
                        {
                            fn: function(combo, value)
                            { 
                                Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                                Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(false);
                                Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);
                                
                                parroquia_default = null;
                                sector_default    = null;
                                sectorId          = null;

                                $('#infopuntoextratype_cantonId').val(combo.getValue());
                                storeParroquias.proxy.extraParams = {idcanton: combo.getValue()};
                                storeParroquias.load();

                                if (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN'  )
                                {
                                    generaLogin();
                                }
                                
                                $('#canton').val(combo.getRawValue());
                                if ($('#canton').val() == 'GUAYAQUIL' || $('#canton').val() == 'QUITO') 
                                {
                                    $('#labelLatitud').addClass('campo-obligatorio');
                                    $('#grados_la').attr('required', 'required');
                                    $('#minutos_la').attr('required', 'required');
                                    $('#segundos_la').attr('required', 'required');
                                    $('#decimas_segundos_la').attr('required', 'required');
                                    $('#latitud').attr('required', 'required');
                                    $('#labelLongitud').addClass('campo-obligatorio');
                                    $('#grados_lo').attr('required', 'required');
                                    $('#minutos_lo').attr('required', 'required');
                                    $('#segundos_lo').attr('required', 'required');
                                    $('#decimas_segundos_lo').attr('required', 'required');
                                    $('#longitud').attr('required', 'required');
                                } else
                                {
                                    $('#labelLatitud').removeClass('campo-obligatorio');
                                    $('#labelLongitud').removeClass('campo-obligatorio');
                                    $('#grados_la').removeAttr('required');
                                    $('#minutos_la').removeAttr('required');
                                    $('#segundos_la').removeAttr('required');
                                    $('#decimas_segundos_la').removeAttr('required');
                                    $('#latitud').removeAttr('required');
                                    $('#grados_lo').removeAttr('required');
                                    $('#minutos_lo').removeAttr('required');
                                    $('#segundos_lo').removeAttr('required');
                                    $('#decimas_segundos_lo').removeAttr('required');
                                    $('#longitud').removeAttr('required');
                                }

                            }}
                }
        });
    
    combo_parroquias = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            store: storeParroquias,
            labelAlign: 'left',
            name: 'idparroquia',
            id: 'idparroquia',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: '',
            width: 300,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            emptyText: strLblParroquia,
            renderTo: 'combo_parroquias',          
            listeners:
                {
                    select:
                        {
                            fn: function(combo, value)
                            {  
                                Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                                Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);

                                sector_default = null;
                                sectorId       = null;
                                
                                $('#infopuntoextratype_parroquiaId').val(combo.getValue());
                                storeSectores.proxy.extraParams = {idparroquia: combo.getValue()};
                                storeSectores.load();

                            }
                        },
                    change:
                        {
                            fn: function(combo, newValue, oldValue)
                            {
                             if (!esRequerido) {
                                Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                              }                                
                            }
                        }
                }
        });    

    var combo_sector = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            store: storeSectores,
            labelAlign: 'left',
            name: 'idsector',
            id: 'idsector',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: '',
            width: 300,
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            emptyText: strLblSector,
            renderTo: 'combo_sector',
            listeners:
                {
                    select:
                        {
                            fn: function(combo, value)
                            {
                                $('#infopuntoextratype_sectorId').val(combo.getValue());
                                ocultarDiv('div_errorsector');
                            }
                        }
                }
        });

    /*
     * 
     * DATA MODELS
     * 
     */

    Ext.define('PersonaFormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'idPersonaFormaContacto', type: 'int'},
            {name: 'formaContacto'},
            {name: 'valor', type: 'string'}
        ]
    });

    Ext.define('FormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'id', type: 'int'},
            {name: 'descripcion', type: 'string'}
        ]
    });
    
    // create the Data Store
    storePersonaFormasContacto = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'PersonaFormasContactoModel',
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: url_formas_contacto_persona,
            reader: {
                type: 'json',
                root: 'personaFormasContacto',
                // records will have a 'plant' tag
                totalProperty: 'total'
            },
            extraParams:{personaid:''},
            simpleSortMode: true               
        },
        listeners: {
                        beforeload: function(store){
				store.getProxy().extraParams.personaid= personaid; 
                        }
                }
    });

    // create the Data Store
    var storeFormasContacto = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'FormasContactoModel',
        proxy: {
            type: 'ajax',
            // load remote data using HTTP
            url: url_formas_contacto,
            reader: {
                type: 'json',
                root: 'formasContacto'
            }
        }
    });
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    });

    gridFormasContacto = Ext.create('Ext.grid.Panel',
        {
            store: storePersonaFormasContacto,
            columns:
                [
                    {
                        text: 'Forma Contacto',
                        header: 'Forma Contacto',
                        dataIndex: 'formaContacto',
                        width: 150,
                        editor: new Ext.form.field.ComboBox(
                            {
                                typeAhead: true,
                                triggerAction: 'all',
                                selectOnTab: true,
                                id: 'id',
                                name: 'formaContacto',
                                valueField: 'descripcion',
                                displayField: 'descripcion',
                                store: storeFormasContacto,
                                lazyRender: true,
                                listClass: 'x-combo-list-small'
                            }
                        )
                    },
                    {
                        text: 'Valor',
                        dataIndex: 'valor',
                        width: 400,
                        align: 'right',
                        editor:
                            {
                                width: '80%',
                                xtype: 'textfield',
                                allowBlank: false
                            }
                    },
                    {
                        xtype: 'actioncolumn',
                        width: 45,
                        sortable: false,
                        items:
                            [
                                {
                                    iconCls: "button-grid-delete",
                                    tooltip: 'Borrar Forma Contacto',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        storePersonaFormasContacto.removeAt(rowIndex);
                                    }
                                }
                            ]
                    }
                ],
            selModel:
                {
                    selType: 'cellmodel'
                },
            renderTo: Ext.get('lista_formas_contacto_grid'),
            width: 600,
            height: 300,
            title: '',
            tbar:
                [
                    {
                        text: 'Agregar',
                        handler: function()
                        {
                            
                            var boolError = false;
                            var indice = 0;
                            for (var i = 0; i < storePersonaFormasContacto.getCount(); i++)
                            {
                                variable = storePersonaFormasContacto.getAt(i).data;
                                boolError = trimAll(variable['formaContacto']) == '';

                                if (boolError)
                                {
                                    break;
                                }
                                else
                                {
                                    boolError = trimAll(variable['valor']) == '';
                                    if (boolError)
                                    {
                                        indice = 1;
                                        break;
                                    }
                                }
                            }
                            if (!boolError)
                            {
                                var r = Ext.create('PersonaFormasContactoModel',
                                    {
                                        idPersonaFormaContacto: '',
                                        formaContacto: '',
                                        valor: ''
                                    });
                                storePersonaFormasContacto.insert(0, r);
                            }
                            cellEditing.startEditByPosition({row: 0, column: indice});
                        }
                    }
                ],
            plugins: [cellEditing]
        });

    function trimAll(texto)
    {
        return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
    }
    
    if ((typeof latitudFloat  !== typeof undefined) && 
        (typeof longitudFloat !== typeof undefined))
    {
        dd2dms(parseFloat(latitudFloat), parseFloat(longitudFloat));
    }
    
    if (typeof cargaFormasContacto === typeof undefined)
    {
        storePersonaFormasContacto.load();
    }
    else
    {
        agregarFormasContacto();
    }
        
    function agregarFormasContacto()
    {
        if (typeof formasDeContacto !== typeof undefined && formasDeContacto != '')
        {
            arrayFormasContacto = formasDeContacto.split(',');
            for (i = 0; i < arrayFormasContacto.length; i += 3)
            {
                var registro =
                    {
                        'idPersonaFormaContacto': arrayFormasContacto[i],
                        'formaContacto': arrayFormasContacto[i + 1],
                        'valor': arrayFormasContacto[i + 2]
                    };
                var rec = new PersonaFormasContactoModel(registro);
                if (rec.formaContacto !== "")
                {
                    storePersonaFormasContacto.add(rec);
                }
            }
        }
    }

    storePersonaFormasContacto.on('load', function()
    {
        agregarFormasContacto();
    });

    var tabs = new Ext.TabPanel({
        height: 580,
        renderTo: 'my-tabs',
        activeTab: 0,
        plain:true,
        autoRender:true,
        autoShow:true,
        items:[
             {contentEl:'tab1', title:'Datos Principales'},
             {contentEl:'tab2', title:'Formas de contacto',listeners:{
                  activate: function(tab){
                          gridFormasContacto.view.refresh()
                                
                  }
                                
              }}
        ]            
    }); 

});

function validaLogin(){
    currentLogin=$('#infopuntodatoadicionaltype_login').val();
	$.ajax({
			type: "POST",
			data: "login=" + currentLogin,
			url: url_valida_login,
			beforeSend: function(){
				$('#img-valida-login').attr("src",url_img_loader);
			},
			success: function(msg){
				if (msg != ''){
					if(msg=="no"){
						flagLoginCorrecto = 1;
						$('#img-valida-login').attr("title","login correcto");
						$('#img-valida-login').attr("src",url_img_check);
					}
					if(msg=="si"){
						flagLoginCorrecto = 0;
						$('#img-valida-login').attr("title","login incorrecto");
						$('#img-valida-login').attr("src",url_img_delete);
						$('#infopuntotype_login').focus();
						alert("Login ya existente. Favor Corregir");
					}
				   
			   }
			   else
			   {
				   alert("Error: No se pudo validar el login ingresado.");
			   }
			}
	});
}

function grabarFormasContacto(campo){
    var array_data = new Array();
    var variable='';
    var valoresVacios=false;
    for(var i=0; i < gridFormasContacto.getStore().getCount(); i++){ 
        variable=gridFormasContacto.getStore().getAt(i).data;
        for(var key in variable) {
            var valor = variable[key];
            if (key=='valor' && valor==''){
                    valoresVacios=true;
            }else{
                    array_data.push(valor);
            }
        } 
    }
    $(campo).val(array_data); 
    if (($(campo).val()=='0,,') || ($(campo).val()=='')) {
        alert('No hay formas de contacto aun ingresadas.');
        $(campo).val('');
    }else{
        if(valoresVacios==true){
                alert('Hay formas de contacto que tienen valor vacio, por favor corregir.');
                $(campo).val('');
        }
    }
}

/**
 * Permite validar las formas de contactos.
 *
 * @version 1.00
 * 
 * Se llama a validación de formas de contactos centralizada.
 *
 * @author Héctor Ortega <haortega@telconet.ec>
 * @version 1.01, 29/11/2016
 */
function validaFormasContacto(){
    return Utils.validaFormasContacto(gridFormasContacto);
}

function validacionesForm()
{
    if(!validaFormasContacto())
    {
        return false;
    }
    
    if (esRequerido)
    {
        if ($('#infopuntoextratype_canal').val() == '')
        {
            mostrarDiv('div_errorcanal');
            return false;
        }
        if (esObligatorio)
        {
            if ($('#infopuntoextratype_punto_venta').val() == '')
            {
                mostrarDiv('div_errorpuntoventa');
                return false;
            }
        }
    }
    
    if($('#infopuntoextratype_sectorId').val()=='')
    {
        mostrarDiv('div_errorsector');
        return false;
    }
    
    if($('#infopuntoextratype_loginVendedor').val()=='')
    {
        mostrarDiv('div_errorvendedor');
        return false;
    }			

    if (origlogin!=$('#infopuntodatoadicionaltype_login').val())
    {
        if(!validaLoginCorrecto())
        {
                return false;
        }
    }	
    
    if ((document.forms[0].grados_la.value && document.forms[0].minutos_la.value && document.forms[0].segundos_la.value && document.forms[0].decimas_segundos_la.value && (document.forms[0].latitud.value!='T'))&&
    (document.forms[0].grados_lo.value && document.forms[0].minutos_lo.value && document.forms[0].segundos_lo.value && document.forms[0].decimas_segundos_lo.value) && (document.forms[0].longitud.value!='T'))
    {
    //funciiones para validar las coordenadas
      if(!validarGradosNuevo(document.forms[0].grados_la.value,1))
        return false;
      if(!validarMinutosNuevo(document.forms[0].minutos_la.value,1))
        return false;
      if(!validarSegundosNuevo(document.forms[0].segundos_la.value,1))
        return false;
      if(!validarDecimasSegundosNuevo(document.forms[0].decimas_segundos_la.value,1))
        return false;
      if(!validarGradosNuevo(document.forms[0].grados_lo.value,2))
        return false;
      if(!validarMinutosNuevo(document.forms[0].minutos_lo.value,2))
        return false;
      if(!validarSegundosNuevo(document.forms[0].segundos_lo.value,2))
        return false;
      if(!validarDecimasSegundosNuevo(document.forms[0].decimas_segundos_lo.value,2))
        return false;
      if(document.forms[0].latitud[document.forms[0].latitud.selectedIndex].value=='T')
      {
        alert('Ingrese la latitud (Norte/Sur)');
        return false;
      }

      if(document.forms[0].longitud[document.forms[0].longitud.selectedIndex].value=='T')
      {
          alert('Ingrese la longitud (Este/Oeste)');
          return false;
      }

        if(!validarCoordenadasEcuador(document.forms[0]))
          return false;
    }else
    {
        if ((document.forms[0].grados_la.value || document.forms[0].minutos_la.value || document.forms[0].segundos_la.value || document.forms[0].decimas_segundos_la.value || (document.forms[0].latitud.value!='T'))||
        (document.forms[0].grados_lo.value || document.forms[0].minutos_lo.value || document.forms[0].segundos_lo.value || document.forms[0].decimas_segundos_lo.value) || (document.forms[0].longitud.value!='T'))
        {	
                alert('Si no va a ingresar coordenadas debe dejar todos los campos de las coordenadas vacios.');
                return false;
        }else
        {
                return true;
        }
    }


    return true;
}


/*
 * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec>       
 * @version 1.0  07-07-2021
 * #GEO Se dejo el metodo generarLogin de manera global
 */
function generaLogin () {
    var cantonId = combo_cantones.getValue ();
    var tiponegocioid = '';
    if (!isNaN (document.getElementById ('infopuntotype_tipoNegocioId').value))
      tiponegocioid = document.getElementById ('infopuntotype_tipoNegocioId')
        .value;
    $.ajax ({
      type: 'POST',
      data: 'idCanton=' +
        cantonId +
        '&idCliente=' +
        clienteId +
        '&tipoNegocio=' +
        tiponegocioid,
      url: url_genera_login,
      beforeSend: function () {
        $ ('#img-valida-login').attr ('src', url_img_loader);
      },
      success: function (msg) {
        if (msg != '') {
          $ ('#infopuntodatoadicionaltype_login').removeAttr ('readonly');
          $ ('#infopuntodatoadicionaltype_login').val (msg);
          if (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' ) {
            $ ('#infopuntodatoaldicionaltype_login').attr (
              'readonly',
              'readonly'
            );
          }
          $ ('#img-valida-login').attr ('title', 'login correcto');
          $ ('#img-valida-login').attr ('src', url_img_check);
          validaLogin ();
        } else {
          alert ('Error: No se pudo generar el login ingresado.');
        }
      },
    });
  }
  
   /*
   * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec>       
   * @version 1.0  07-07-2021
   * #GEO Se inicializar cambios en interfaz grafica
   */
   function bloquearInputGeolocalizacionMD () {
    if (esRequerido) {  
      Ext.getCmp("idptocobertura").setDisabled(true); 
      Ext.getCmp("idcanton").setDisabled(true); 
      Ext.getCmp("idparroquia").setDisabled(true); 
      Ext.getCmp("idsector").setDisabled(true); 
    }  
  }   
  /*
   * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec>       
   * @version 1.0  07-07-2021
   * #GEO Se inicializar cambios en interfaz grafica
   */
  $ (document).ready (function () {  
      setTimeout (() => {
        bloquearInputGeolocalizacionMD(); 
      }, 200);  
  });