$("[data-mask]").inputmask();

var myComboStores = ['storeNodo', 'storeClase', 'storeSwitches', 'storePuertos', 'storeMarcas', 'storeModelos', 'storeSnmp'];
var gridBaterias  = null;

function initData() 
{
    var loaded = true;
    Ext.each(myComboStores, function(storeId) 
    {
        var store = Ext.getStore(storeId);
        
        if (store.isLoading()) 
        {
            loaded = false;
        }
    });
    
    if(loaded) 
    {
        Ext.MessageBox.hide();
        
        if( Ext.isEmpty(intIdNodo) )
        {            
            Ext.getCmp('cmbDispositivo').reset();
            Ext.getCmp('cmbDispositivo').setDisabled(true);
        }
        
        if( Ext.isEmpty(intIdSwitch) )
        {            
            Ext.getCmp('cmbPuertos').reset();
            Ext.getCmp('cmbPuertos').setDisabled(true);
        }
    }
}

Ext.onReady(function()
{
    Ext.MessageBox.wait("Cargando información...");
    
    var storeNodo = new Ext.data.Store
    ({
        storeId: 'storeNodo',
        total: 'total',
        autoLoad: 
        {
            callback: initData
        },
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetEncontradosNodo,
            timeout: 900000,
            extraParams: 
            {
                nombreElemento: '',
                modeloElemento: '',
                marcaElemento: '',
                canton: '',
                jurisdiccion: '',
                estado: 'Todos'
            },
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            limitParam: undefined,
            startParam: undefined,
        },
        fields:
        [
            {name: 'idElemento',     mapping: 'idElemento'},
            {name: 'nombreElemento', mapping: 'nombreElemento'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                Ext.getCmp('cmbNodos').setDisabled(false);
                
                Ext.each(records, function(record)
                {
                    if( record.get('idElemento') == intIdNodo )
                    {
                        Ext.getCmp('cmbNodos').setValue(record.get('idElemento'));
                    }
                });
            }      
        }
    });

    var cbxNodos = new Ext.form.ComboBox
    ({
        id: 'cmbNodos',
        name: 'cmbNodos',
        fieldLabel: false,
        editable: false,
        disabled: true,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Nodo',
        store: storeNodo,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'combo_nodos',
        forceSelection: true,
        listeners: 
        {
            select: 
            {
                fn: function(combo, value) 
                {
                    Ext.getCmp('cmbDispositivo').reset();
                    Ext.getCmp('cmbDispositivo').setDisabled(false);
                    
                    Ext.getCmp('cmbPuertos').reset();
                    Ext.getCmp('cmbPuertos').setDisabled(true);
                    
                    if( combo.getValue() != '' && combo.getValue() != null)
                    {
                        getDispositivos(combo.getValue());
                    }
                }
            }
        }
    });
    
    function getDispositivos(nodoId)
    {
        storeSwitches.proxy.extraParams = {nodo: nodoId};
        storeSwitches.load();
    }


    var storeClaseUps = new Ext.data.Store
    ({
        storeId: 'storeClase',
        total: 'total',
        autoLoad: 
        {
            callback: initData
        },
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetClases,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'id',          mapping: 'id'},
            {name: 'descripcion', mapping: 'descripcion'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                Ext.getCmp('comboClases').setDisabled(false);
                
                Ext.each(records, function(record)
                {
                    if( record.get('descripcion') == strClase )
                    {
                        Ext.getCmp('comboClases').setValue(record.get('id'));
                    }
                });
            }      
        }
    });

    comboClases = new Ext.form.ComboBox
    ({
        id: 'comboClases',
        name: 'comboClases',
        fieldLabel: false,
        disabled: true,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Clase',
        store: storeClaseUps,
        displayField: 'descripcion',
        valueField: 'id',
        renderTo: 'claseUps'
    });
    
    
    
    var storeSwitches = new Ext.data.Store
    ({
        storeId: 'storeSwitches',
        total: 'total',
        autoLoad: 
        {
            callback: initData
        },
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetDispositivos,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                nodo: intIdNodo
            }
        },
        fields:
        [
            {name: 'idElemento',     mapping: 'idElemento'},
            {name: 'nombreElemento', mapping: 'nombreElemento'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                Ext.getCmp('cmbDispositivo').setDisabled(false);
                
                Ext.each(records, function(record)
                {
                    if( record.get('idElemento') == intIdSwitch )
                    {
                        Ext.getCmp('cmbDispositivo').setValue(record.get('idElemento'));
                    }
                });
            }      
        }
    });
    
    
    comboDispositivos = new Ext.form.ComboBox
    ({
        id: 'cmbDispositivo',
        name: 'cmbDispositivo',
        fieldLabel: false,
        disabled: true,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Dispositivo',
        store: storeSwitches,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'dispositivo',
        listeners: 
        {
            select: 
            {
                fn: function(combo, value) 
                {
                    Ext.getCmp('cmbPuertos').reset();
                    Ext.getCmp('cmbPuertos').setDisabled(false);
                    
                    if( combo.getValue() != '' && combo.getValue() != null)
                    {
                        getPuertos(combo.getValue());
                    }
                }
            }
        },
        forceSelection: true
    });
    
    
    var storePuertos = new Ext.data.Store
    ({
        storeId: 'storePuertos',
        total: 'total',
        autoLoad: 
        {
            callback: initData
        },
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetPuertos,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                elemento: intIdSwitch,
                accion: 'editar',
                idPuerto: intIdPuerto
            },
        },
        fields:
        [
            {name: 'id',     mapping: 'id'},
            {name: 'puerto', mapping: 'puerto'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                Ext.getCmp('cmbPuertos').setDisabled(false);
                
                Ext.each(records, function(record)
                {
                    if( record.get('id') == intIdPuerto )
                    {
                        Ext.getCmp('cmbPuertos').setValue(record.get('id'));
                    }
                });
            }      
        }
    });

    comboPuertos = new Ext.form.ComboBox
    ({
        id: 'cmbPuertos',
        name: 'cmbPuertos',
        fieldLabel: false,
        disabled: true,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Puerto',
        store: storePuertos,
        displayField: 'puerto',
        valueField: 'id',
        renderTo: 'puerto'
    });
    
    function getPuertos(switchId)
    {
        storePuertos.proxy.extraParams = {elemento: switchId};
        storePuertos.load();
    }
    
    
    var storeMarcas = new Ext.data.Store
    ({
        storeId: 'storeMarcas',
        total: 'total',
        autoLoad: 
        {
            callback: initData
        },
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetMarcas,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                tipoElemento: 'UPS'
            }
        },
        fields:
        [
            {name: 'idMarcaElemento',     mapping: 'idMarcaElemento'},
            {name: 'nombreMarcaElemento', mapping: 'nombreMarcaElemento'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                Ext.getCmp('cmbMarca').setDisabled(false);

                Ext.each(records, function(record)
                {
                    if( record.get('idMarcaElemento') == intIdMarca )
                    {
                        Ext.getCmp('cmbMarca').setValue(record.get('idMarcaElemento'));
                    }
                });
            }      
        }
    });
    
    
    cmbMarcas = new Ext.form.ComboBox
    ({
        id: 'cmbMarca',
        name: 'cmbMarca',
        fieldLabel: false,
        disabled: true,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Marca',
        store: storeMarcas,
        displayField: 'nombreMarcaElemento',
        valueField: 'idMarcaElemento',
        renderTo: 'marca',
        listeners: 
        {
            select: 
            {
                fn: function(combo, value) 
                {
                    Ext.getCmp('cmbModelo').reset();
                    Ext.getCmp('cmbModelo').setDisabled(false);
                    
                    if( combo.getValue() != '' && combo.getValue() != null)
                    {
                        getModelos(combo.getValue());
                    }
                }
            }
        },
        forceSelection: true
    });
    
    
    var storeModelos = new Ext.data.Store
    ({
        storeId: 'storeModelos',
        total: 'total',
        autoLoad: 
        {
            callback: initData
        },
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetModelos,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: 
            {
                idMarca: intIdMarca, 
                tipoElemento: 'UPS'
            }
        },
        fields:
        [
            {name: 'idModeloElemento',     mapping: 'idModeloElemento'},
            {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                Ext.getCmp('cmbModelo').setDisabled(false);
                
                Ext.each(records, function(record)
                {
                    if( record.get('idModeloElemento') == intIdModelo )
                    {
                        Ext.getCmp('cmbModelo').setValue(record.get('idModeloElemento'));
                    }
                });
            }      
        }
    });

    cmbModelos = new Ext.form.ComboBox
    ({
        id: 'cmbModelo',
        name: 'cmbModelo',
        fieldLabel: false,
        disabled: true,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Modelo',
        store: storeModelos,
        displayField: 'nombreModeloElemento',
        valueField: 'idModeloElemento',
        renderTo: 'modelo'
    });
    
    function getModelos(marcaId)
    {
        storeModelos.proxy.extraParams = {idMarca: marcaId, tipoElemento: 'UPS'};
        storeModelos.load();
    }
    
    
    var storeSnmp = new Ext.data.Store
    ({
        storeId: 'storeSnmp',
        total: 'total',
        autoLoad: 
        {
            callback: initData
        },
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetSnmp,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'idSnmp',        mapping: 'idSnmp'},
            {name: 'snmpComunidad', mapping: 'snmpComunidad'}
        ],
        listeners: 
        {
            load: function(store, records)
            {
                Ext.each(records, function(record)
                {
                    if( record.get('idSnmp') == intIdSnmp )
                    {
                        Ext.getCmp('cmbSnmp').setValue(record.get('idSnmp'));
                    }
                });
            }      
        }
    });

    comboSnmp = new Ext.form.ComboBox
    ({
        id: 'cmbSnmp',
        name: 'cmbSnmp',
        fieldLabel: false,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione SNMP',
        store: storeSnmp,
        displayField: 'snmpComunidad',
        valueField: 'idSnmp',
        renderTo: 'snmp'
    });
    
    
    /******************* Creacion Grid Baterias ******************/
    var storeModelosBaterias = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetModelosPorTipoElemento,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                tipoElemento: idBateriaElemento,
                estado: 'Activo'
            }
        },
        fields:
        [
            {name: 'idModeloElemento',     mapping: 'idModeloElemento'},
            {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'}
        ]
    });
    

    var storeTipoBaterias = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetTipoBaterias,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'id',          mapping: 'id'},
            {name: 'descripcion', mapping: 'descripcion'}
        ]
    });
    
    Ext.define('BateriasModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [			
            {name:'intIdElemento',          mapping:'intIdElemento'},
            {name:'strNombreElemento',      mapping:'strNombreElemento'},
            {name:'strFechaCreacion',       mapping:'strFechaCreacion'},
            {name:'strTipoElemento',        mapping:'strTipoElemento'},
            {name:'strMarcaElemento',       mapping:'strMarcaElemento'},
            {name:'intIdMarcaElemento',     mapping:'intIdMarcaElemento'},
            {name:'intIdModeloElemento',    mapping:'intIdModeloElemento'},
            {name:'strModeloElemento',      mapping:'strModeloElemento'},
            {name:'strSerieFisica',         mapping:'strSerieFisica'},
            {name:'strAMPERAJE',            mapping:'strAMPERAJE'},
            {name:'strTIPO_BATERIA',        mapping:'strTIPO_BATERIA'},
            {name:'strFECHA_INSTALACION',   mapping:'strFECHA_INSTALACION'}
        ]
    });

    
    storeBaterias = Ext.create('Ext.data.Store', 
    {
        autoLoad: true,
        model: 'BateriasModel',        
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetBaterias,
            timeout: 900000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                ups: intIdElementoUps
            }
        }
    });
        
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', 
    {
        clicksToEdit: 2,
        listeners: 
        {
            edit: function()
            {
                gridBaterias.getView().refresh();
            }
        }
    });
    
    var selBateriasModel = Ext.create('Ext.selection.CheckboxModel', 
    {
        listeners: 
        {
            selectionchange: function(sm, selections) 
            {
                gridBaterias.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });
    
    gridBaterias = Ext.create('Ext.grid.Panel', 
    {
        id:'gridBats',
        store: storeBaterias,
        columns: 
        [
            {
                id: 'intIdElemento',
                header: 'intIdElemento',
                dataIndex: 'intIdElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'strTIPO_BATERIA',
                header: 'Tipo',
                dataIndex: 'strTIPO_BATERIA',
                width: 100,
                sortable: true,
                renderer: function (value, metadata, record, rowIndex, colIndex, store)
                {
                    if (typeof(record.data.strTIPO_BATERIA) == "number")
                    {
                        for (var i = 0;i< storeTipoBaterias.data.items.length;i++)
                        {
                            if (storeTipoBaterias.data.items[i].data.id == record.data.strTIPO_BATERIA)
                            {
                                record.data.strTIPO_BATERIA = storeTipoBaterias.data.items[i].data.descripcion;
                                break;
                            }
                        }
                    }
                    
                    return record.data.strTIPO_BATERIA;
                },
                editor: 
                {
                    id:'cmbTipoBateria',
                    xtype: 'combobox',
                    typeAhead: true,
                    displayField:'descripcion',
                    valueField: 'id',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    loadingText: 'Buscando ...',
                    hideTrigger: false,
                    editable: false,
                    store: storeTipoBaterias,
                    lazyRender: true,
                    listClass: 'x-combo-list-small'
                }
            },
            {
                id: 'strAmperaje',
                header: 'Amperaje',
                dataIndex: 'strAMPERAJE',
                width: 94,
                editor: 
                {
                    allowBlank: false,
                    maxLength: 5,
                    enableKeyEvents: true,
                    listeners: 
                    {                
                        keypress: function(me, e) 
                        {
                            var charCode = e.getKey();

                            if( charCode >= 48 && charCode <= 57 )
                            {
                                me.isValid();
                            }
                            else if( charCode === 8 || charCode === 46 )
                            {
                                me.isValid();
                            }
                            else
                            {
                                e.stopEvent();
                            }
                        }
                    }
                }
            },
            {
                id: 'strSerieFisica',
                header: 'Serie Física',
                dataIndex: 'strSerieFisica',
                width: 180,
                editor: 
                {
                    allowBlank: true,
                    maxLength: 15
                }
            },
            {
                id: 'strModeloElemento',
                header: 'Modelo',
                dataIndex: 'strModeloElemento',
                width: 150,
                sortable: true,
                renderer: function (value, metadata, record, rowIndex, colIndex, store)
                {
                    if (typeof(record.data.strModeloElemento) == "number")
                    {
                        for (var i = 0;i< storeModelosBaterias.data.items.length;i++)
                        {
                            if (storeModelosBaterias.data.items[i].data.idModeloElemento == record.data.strModeloElemento)
                            {
                                record.data.strModeloElemento = storeModelosBaterias.data.items[i].data.nombreModeloElemento;
                                break;
                            }
                        }
                    }
                    
                    return record.data.strModeloElemento;
                },
                editor:  
                {
                    xtype: 'combobox',
                    id:'cmbModelosBaterias',
                    name: 'cmbModelosBaterias',
                    store: storeModelosBaterias,
                    displayField: 'nombreModeloElemento',
                    valueField: 'idModeloElemento',
                    queryMode: 'local',
                    editable: false,
                    lazyRender: true,
                    emptyText: '',
                    forceSelection: true
                }
            },
            {
                id: 'strFECHA_INSTALACION',
                header: 'Fecha de Instalación',
                dataIndex: 'strFECHA_INSTALACION',
                width: 150,
                renderer: function(field)
                {
                    if( typeof field === 'string')
                    {
                        field = Ext.Date.parse(field,'d-m-Y');
                    }
                    
                    var formated = Ext.util.Format.date(field, 'd-m-Y');
                    
                    return formated;
                },
                editor: 
                {
                    allowBlank: true,
                    editable: false,
                    id: 'dateFechaInstalacion',
                    fieldLabel: false,
                    labelAlign: 'left',
                    xtype: 'datefield',
                    minValue: Ext.Date.add(new Date(), Ext.Date.YEAR, -1),
                    format: 'd-m-Y',
                    width: 150,
                    name: 'dateFechaInstalacion'
                }
            }
        ],
        selModel: selBateriasModel,
        viewConfig:
        {
            stripeRows:true
        },
        dockedItems: 
        [
            {
                xtype: 'toolbar',
                items: 
                [
                    {
                        itemId: 'removeButton',
                        text:'Eliminar',
                        tooltip:'Elimina el item seleccionado',
                        iconCls:'remove',
                        disabled: true,
                        handler : function()
                        {
                            eliminarSeleccion(gridBaterias);
                        }
                    }, 
                    '-', 
                    {
                        text:'Agregar',
                        tooltip:'Agrega un item a la lista',
                        iconCls:'add',
                        handler : function()
                        {
                            var recordRelacion = Ext.create('BateriasModel', 
                            {
                                intIdElemento: '',
                                strNombreElemento: '',
                                strFechaCreacion: '',
                                strTipoElemento: '',
                                strMarcaElemento: '',
                                strModeloElemento: '',
                                strSerieFisica: '',
                                strAMPERAJE: '',
                                strTIPO_BATERIA: '',
                                strFECHA_INSTALACION: '',
                                intIdMarcaElemento: '',
                                intIdModeloElemento: ''
                            });
                            
                            if(!existeRecordRelacion(gridBaterias))
                            {
                                storeBaterias.insert(0, recordRelacion);
                                cellEditing.startEditByPosition({row: 0, column: 1});
                            }
                            else
                            {
                                alert('Ya existe un registro vacio.');
                            }
                        }
                    }
                ]
            }
        ],
        width: 700,
        height: 200,
        title: 'Baterias',
        renderTo: 'gridBaterias',
        plugins: [cellEditing]
    });
});


function eliminarSeleccion(datosSelect)
{
    var xRowSelMod      = datosSelect.getSelectionModel().getCount();
    var intValorInicial = xRowSelMod - 1;
 
    for(var i = intValorInicial; i >= 0; i--)
    {
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
}


function existeRecordRelacion(grid)
{
    var existe = false;
    var num    = grid.getStore().getCount();

    for(var i=0; i < num ; i++)
    {
        var tipoBateria      = grid.getStore().getAt(i).get('strTIPO_BATERIA');
        var fechaInstalacion = grid.getStore().getAt(i).get('strFECHA_INSTALACION');
        var modelo           = grid.getStore().getAt(i).get('strModeloElemento');
        var amperaje         = grid.getStore().getAt(i).get('strAMPERAJE');

        if( tipoBateria == '' || fechaInstalacion == '' || modelo == '' || amperaje == '' )
        {
            existe = true;
            break;
        }
    }
    
    return existe;
}


function obtenerBaterias()
{
    var intTotalOpciones          = 0;
    var arrayData                 = new Array();
    var arrayOpciones             = new Object();
        arrayOpciones['baterias'] = new Array();

    for(var i=0; i < gridBaterias.getStore().getCount(); i++)
    {
        var tipoBateria      = gridBaterias.getStore().getAt(i).get('strTIPO_BATERIA');
        var fechaInstalacion = gridBaterias.getStore().getAt(i).get('strFECHA_INSTALACION');
        var modelo           = gridBaterias.getStore().getAt(i).get('strModeloElemento');
        var amperaje         = gridBaterias.getStore().getAt(i).get('strAMPERAJE');

        if( tipoBateria.trim() != '' && fechaInstalacion != '' && modelo.trim() != '' && amperaje.trim() != '' )
        {
            arrayData.push(gridBaterias.getStore().getAt(i).data);
            intTotalOpciones++;
        }
    }

    arrayOpciones['baterias'] = arrayData;
    arrayOpciones['total']    = intTotalOpciones;

    if( arrayOpciones['total'] > 0 )
    {
        Ext.get('baterias').dom.value = Ext.JSON.encode(arrayOpciones);
        
        return true;
    }
    else
    {
        Ext.get('baterias').dom.value = '';
        
        return false;
    }
}


function verificarData()
{    
    Ext.MessageBox.wait("Guardando datos...");
    
    var nodoElemento = Ext.getCmp('cmbNodos').getValue();
    var clase        = Ext.getCmp('comboClases').getValue();
    var dispositivo  = Ext.getCmp('cmbDispositivo').getValue();
    var puerto       = Ext.getCmp('cmbPuertos').getValue();
    var snmp         = Ext.getCmp('cmbSnmp').getValue();
    var ip           = $("#infoElementoUps_ipElemento").val();
    var nombre       = $("#infoElementoUps_nombreElemento").val();
        nombre       = nombre.trim();
    var marca        = Ext.getCmp('cmbMarca').getValue();
    var modelo       = Ext.getCmp('cmbModelo').getValue();
    var continuar    = true;

    if( nodoElemento == "" || nodoElemento == null )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un nodo");
        
        return false;
    }
    else if( clase == "" || clase == null )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar una clase");
        
        return false;
    }
    else if( ip == "" || ip == null )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe ingresar una ip");
        
        return false;
    }
    else if( marca == "" || marca == null )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar una marca");
        
        return false;
    }
    else if( modelo == "" || modelo == null )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un modelo");
        
        return false;
    }
    else if( snmp == "" || snmp == null )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un SNMP");
        
        return false;
    }
    else if( existeRecordRelacion(gridBaterias) )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe llenar la información correspondiente a las baterias");
        
        return false;
    }
    else if( !obtenerBaterias() )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe agregar baterias al UPS");
        
        return false;
    }
    else if( ip != "" || ip != null )
    {
        var ipTmp          = ip.replace(/_/g,"");
        var resultadoSplit = ipTmp.split(".");
        var incompleto     = false;
            
        ip = ipTmp;
            
        for (var i = 0; i < resultadoSplit.length; i++)
        {
            if( resultadoSplit[i] == "" || resultadoSplit[i] == null)
            {
                incompleto = true;
            }
        }
        
        
        if(incompleto)
        {        
            continuar = false;
        
            Ext.MessageBox.hide();
            Ext.Msg.alert("Atención", "Debe ingresar una ip válida");

            return false;
        }
    }

    if( continuar )
    {
        Ext.Ajax.request
        ({
            url: strUrlVerificarData,
            method: 'post',
            params: 
            {
                puerto: puerto,
                ip: ip,
                nombre: nombre,
                idUps: intIdUps
            },
            success: function(response)
            {
                var text = response.responseText;

                if(text === "OK")
                {
                    document.getElementById('intIdNodo').value        = nodoElemento;
                    document.getElementById('intIdClase').value       = clase;
                    document.getElementById('intIdDispositivo').value = dispositivo;
                    document.getElementById('intIdPuerto').value      = puerto;
                    document.getElementById('modeloElementoId').value = modelo;
                    document.getElementById('intIdSnmp').value        = snmp;
                    document.getElementById("form_new_proceso").submit();
                }
                else
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error', text); 
                }
            },
            failure: function(result)
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error',result.responseText);
            }
        });
    }
    
    return false;
}
