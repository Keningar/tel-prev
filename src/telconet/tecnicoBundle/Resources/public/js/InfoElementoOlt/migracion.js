var arrayTituloMensajeBox = [];
    arrayTituloMensajeBox['100'] = 'Información';
    arrayTituloMensajeBox['001'] = 'Error';
    arrayTituloMensajeBox['000'] = 'Alerta';

Ext.onReady(function() {
    //array que contiene el titulo de las ventanas de mensajes    
    Ext.define('estados', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'opcion', type: 'string'},
            {name: 'valor', type: 'string'}
        ]
    });
    
    Ext.define('Asignacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idElemento', mapping: 'idElemento'},
            {name: 'nombreElemento', mapping: 'nombreElemento'},
            {name: 'marcaElemento', mapping: 'marcaElemento'},
            {name: 'cantonNombre', mapping: 'cantonNombre'},
            {name: 'modeloElemento', mapping: 'modeloElemento'},
            {name: 'estado', mapping: 'estado'},
            {name: 'strmigracionHuaweiZte', mapping: 'strmigracionHuaweiZte' }
        ]
    });
    
    
    var storeMarcas = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: '../../../administracion/tecnico/admi_marca_elemento/getMarcasElementosTipo',
            extraParams: {
                tipoElemento: 'OLT'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreMarcaElemento', mapping: 'nombreMarcaElemento'},
                {name: 'idMarcaElemento', mapping: 'idMarcaElemento'}
            ]
    });

    var storeCantones = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: '../../../administracion/general/admi_canton/getCantones',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombre_canton', mapping: 'nombre_canton'},
                {name: 'id_canton', mapping: 'id_canton'}
            ]
    });
    
    store = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 900000,
            url: urlOltGridMigracion,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                marcaElemento: '',
                canton: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'marcaElemento', mapping: 'marcaElemento'},
                {name: 'cantonNombre', mapping: 'cantonNombre'},
                {name: 'modeloElemento', mapping: 'modeloElemento'},
                {name: 'estado', mapping: 'estado'},
                {name: 'strmigracionHuaweiZte', mapping: 'strmigracionHuaweiZte' }
                
            ],
    });
    
    SelectDataOltMigracion = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 900000,
            url: gridAsignaciones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                //root: 'encontrados'
                root:'asignado'
            },
            extraParams: {
                arrayDataJson: ''
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'marcaElemento', mapping: 'marcaElemento'},
                {name: 'cantonNombre', mapping: 'cantonNombre'},
                {name: 'modeloElemento', mapping: 'modeloElemento'},
                {name: 'estado', mapping: 'estado'},
                {name: 'strmigracionHuaweiZte', mapping: 'strmigracionHuaweiZte' }
                
            ],
    });

    var pluginExpanded = true;

    /*****Arreglo de Check de seleccion******/
    sm = new Ext.selection.CheckboxModel({
            listeners:
            {
                selectionchange: function(selectionModel, selected, options)
                {
                    arregloSeleccionados= new Array();
                    Ext.each(selected, function(record)
                    {
                    });			
                }
            }
        });

    sm2 = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
            selectionchange: function(sm, selections) {
                gridAsignaciones.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });

    grid = Ext.create('Ext.grid.Panel', {
        width: 750,
        height: 350,
        store: store,
        loadMask: true,
        frame: false,
        selModel: sm,
        viewConfig: {enableTextSelection: true},
        iconCls: 'icon-grid',
        dockedItems: [ 
                        {
                            xtype: 'toolbar',
                            dock: 'top',
                            align: '->',
                            items: [
                                {
                                    iconCls: 'icon_add',
                                    text: 'Agregar Lista',
                                    disabled: false,
                                    itemId: 'idagregar',
                                    scope: this,
                                    handler: function(){listaOltMigrar()}
                                },
                                { xtype: 'tbfill' },
                                {
                                    id: 'sltMarcaMigrar',
                                    fieldLabel: 'Tecnología destino seleccionada',
                                    xtype: 'combobox',
                                    value: 'HUAWEI',
                                    store: [
                                        ['HUAWEI', 'HUAWEI'],
                                        ['ZTE', 'ZTE']
                                    ],
                                    displayField: 'nombreMarcaElemento',
                                    valueField: 'idMarcaElemento',
                                    loadingText: 'Buscando ...',
                                    queryMode: 'local',
                                    listClass: 'x-combo-list-small',
                                    width: '30%'
                                }
                            ]
                        }
                    ],
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'ipElemento',
                header: 'Olt',
                xtype: 'templatecolumn',
                width: 240,
                tpl: '<span class="box-detalle">{nombreElemento}</span>\n\ '+
                      '<span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\ '
            },
            {
                header: 'Marca',
                dataIndex: 'marcaElemento',
                width: 80,
                sortable: true
            },
            {
                header: 'Modelo',
                dataIndex: 'modeloElemento',
                width: 60,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 60,
                sortable: true
            },
            {
                header: 'Tecnología Destino',
                dataIndex: 'strmigracionHuaweiZte',
                width: 120,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    gridAsignaciones = Ext.create('Ext.grid.Panel', {
        width: 750,
        height: 350,
        store: SelectDataOltMigracion,
        loadMask: true,
        frame: false,
        selModel: sm2,
        iconCls: 'icon-grid',
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'idElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'idElemento',
                header: 'Olt Migrar',
                xtype: 'templatecolumn',
                width: 240,
                tpl: '<span class="box-detalle">{nombreElemento}</span>\n\ '+
                      '<span class="bold">Canton:</span><span>{cantonNombre}</span></br>\n\ '
            },
            {
                header: 'Marca',
                dataIndex: 'marcaElemento',
                width: 80,
                sortable: true
            },
            {
                header: 'Modelo',
                dataIndex: 'modeloElemento',
                width: 60,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 60,
                sortable: true
            },
            {
                header: 'Tecnología Destino',
                dataIndex: 'strmigracionHuaweiZte',
                width: 100,
                sortable: true
            }
        ],
        dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                itemId: 'removeButton',
                                text: 'Eliminar',
                                tooltip: 'Elimina los items seleccionado',
                                iconCls: 'remove',
                                scope: this,
                                disabled: true,
                                handler: function() {
                                    eliminarSeleccion(gridAsignaciones);
                                }
                            }]
                    }],
        title: 'Tecnología Destino',
        
        renderTo: 'gridAsignaciones'
    });
    
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 750,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                }
            }
        ],
        items: [
            {width: '10%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                width: '200px'
            },
            {width: '20%', border: false},
            {
                id: 'sltMarca',
                fieldLabel: 'Marca',
                xtype: 'combobox',
                store: storeMarcas,
                displayField: 'nombreMarcaElemento',
                valueField: 'idMarcaElemento',
                loadingText: 'Buscando ...',
                queryMode: 'local',
                listClass: 'x-combo-list-small',
                width: '30%'
            },
            {width: '10%', border: false},
            {width: '10%', border: false},
            {
                xtype: 'combobox',
                id: 'sltCanton',
                fieldLabel: 'Canton',
                displayField: 'nombre_canton',
                valueField: 'id_canton',
                loadingText: 'Buscando ...',
                store: storeCantones,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '30%'
            },
            {width: '20%', border: false},
            {
                xtype: 'combobox',
                fieldLabel: 'Estado',
                id: 'sltEstado',
                value: 'Activo',
                store: [
                    ['Activo', 'Activo'],
                    ['Modificado', 'Modificado']
                ],
                width: '30%'
            },
            {width: '10%', border: false}
        ],
        renderTo: 'filtro'
    });
    
    store.load({
        callback: function() 
        {
            storeMarcas.load(
            {
                callback: function() 
                {
                    storeCantones.load(
                    {
                        callback: function() 
                        {
                        }
                    });
                }
            });
        }
    });
});

function buscar() {
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.marcaElemento = Ext.getCmp('sltMarca').value;
    store.getProxy().extraParams.canton = Ext.getCmp('sltCanton').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
};

function limpiar() {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");

    Ext.getCmp('sltMarca').value = "";
    Ext.getCmp('sltMarca').setRawValue("");

    Ext.getCmp('sltCanton').value = "";
    Ext.getCmp('sltCanton').setRawValue("");

    Ext.getCmp('sltEstado').value = "Activo";
    Ext.getCmp('sltEstado').setRawValue("Activo");
    store.load({params: {
            nombreElemento: Ext.getCmp('txtNombre').value,
            marcaElemento: Ext.getCmp('sltMarca').value,
            canton: Ext.getCmp('sltCanton').value,
            estado: Ext.getCmp('sltEstado').value
        }});
};

function listaOltMigrar()
{    
    var strTecnoligiaDestino = Ext.getCmp('sltMarcaMigrar').rawValue;
    var intListaMigrar = sm.getSelection().length;
    
    if( strTecnoligiaDestino == "")
    {
        Ext.Msg.alert('Alerta', 'Seleccione la tecnología destino');
        return 0;
    }
    else if (strTecnoligiaDestino != 'HUAWEI' && strTecnoligiaDestino != 'ZTE')
    {
        Ext.Msg.alert('Alerta', 'Tecnología destino no permitida');
        return 0;
    };
    
    if( intListaMigrar == 0 )
    {
        Ext.Msg.alert('Alerta', 'Seleccione por lo menos un olt de la lista');
        return 0;
    };

    Ext.MessageBox.wait("Lista Olt a migrar...");
    
    if( intListaMigrar  > 0)
    {
        var strElementoOlt = "";
        
        //Recorre los registro que tienen el checkout en true
        for(var i=0 ;  i < sm.getSelection().length ; ++i)
        {
            var strFlaBoleans = true;
            //Valida si hay elemento en la lista
            if(gridAsignaciones.getStore().getCount() != 0)
            {
                //Verifico si existe este registro en el grid gridAsignaciones por medio del ifElemento
                gridAsignaciones.getStore().each(function(record){
                    if(sm.getSelection()[i].get('idElemento') == record.get('idElemento'))
                    {
                        strElementoOlt += ' <br/>'+sm.getSelection()[i].get('nombreElemento')+ "\t Existe en la lista"
                        strFlaBoleans = false
                        return 0;
                    }
                });                
            }
            
            if(strFlaBoleans){
                if(sm.getSelection()[i].get('strmigracionHuaweiZte') != Ext.getCmp('sltMarcaMigrar').getValue()) 
                {
                    var r = Ext.create('Asignacion', {
                        idElemento: sm.getSelection()[i].get('idElemento'),
                        nombreElemento: sm.getSelection()[i].get('nombreElemento'),
                        marcaElemento: sm.getSelection()[i].get('marcaElemento'),
                        cantonNombre: sm.getSelection()[i].get('cantonNombre'),
                        modeloElemento: sm.getSelection()[i].get('modeloElemento'),
                        estado: sm.getSelection()[i].get('estado'),
                        strmigracionHuaweiZte:Ext.getCmp('sltMarcaMigrar').getValue()
                    });
                    SelectDataOltMigracion.insert(0,r);
                    
                }
                else
                {
                    strElementoOlt+= ' <br/>'+sm.getSelection()[i].get('nombreElemento')+ "\t No se agrego a la lista coincide con la misma tecnología"
                }
            }
        };
        
        if(strElementoOlt)
        {
            Ext.Msg.alert('Información', "Olt(s) seleccionado(s) tecnología "+ 
                    Ext.getCmp('sltMarcaMigrar').getValue() + " <br/>" + strElementoOlt);
        }
        else
        {
             Ext.Msg.alert('Información', "Olt(s) seleccionado(s) tecnología "+ Ext.getCmp('sltMarcaMigrar').getValue()+ " agregado(s) a la lista");
        }
    };
    
};

function eliminarSeleccion(datosSelect)
{
    Ext.Msg.confirm('Alerta', 'Se eliminara de la lista los olt(s) seleccionado(s). ¿Seguro desea continuar?', function(btn)
    {
        if (btn == 'yes')
        {
            var xRowSelMod = datosSelect.getSelectionModel().getSelection();
            for (var i = 0; i < xRowSelMod.length; i++)
            {
                var RowSel = xRowSelMod[i];
                datosSelect.getStore().remove(RowSel);
            };
        };
    });
};

/*
 * Funcion permite guardar los registro en la base datos
 * @returns {Number}
 */
function OltMigrar()
{
    var arrayDatosOltMigrar = new Object();
    var jsonDataOltMigrar = '';
    arrayDatosOltMigrar['arrayData'] = new Array();
    var arrayData = Array();
    
    var intListaOltMigrar = gridAsignaciones.getStore().getCount();
    
    if( intListaOltMigrar == 0 )
    {
        Ext.Msg.alert('Alerta', 'Lista Olt Vacia');
        return 0;
    };
    
    Ext.Msg.confirm('Alerta', 'Se guardaran los Olt de esta lista. ¿Seguro desea continuar?', function(btn)
    {
        if (btn == 'yes')
        {
            Ext.MessageBox.show({
                msg: 'Guardando...',
                title:
                        'Procesando',
                progressText: 'Guardando.',
                progress: true,
                closable: false,
                width: 300,
                wait: true,
                waitConfig: {interval: 200}
            });

            for (var i = 0; i < gridAsignaciones.getStore().getCount(); i++)
            {
                arrayData.push(gridAsignaciones.getStore().getAt(i).data);
            };

            arrayDatosOltMigrar['arrayData'] = arrayData;
            jsonDataOltMigrar = Ext.JSON.encode(arrayDatosOltMigrar);

            Ext.Ajax.request({
                url: urlOltMigracion,
                method: 'POST',
                timeout: 60000,
                params: {
                    jsonListaOltMigrar: jsonDataOltMigrar
                },
                success: function(response) {
                    
                    var text = Ext.decode(response.responseText);
                    Ext.Msg.alert("Información","Datos procesados con exito");
                    store.load();
                    gridAsignaciones.getStore().removeAll()    
                },
                failure: function(result) {
                    var text = Ext.decode(result.responseText);
                    Ext.Msg.alert("Alert","Error Olt Migrados");
                    
                }
            });
        };
    });
};
    