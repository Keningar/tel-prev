Ext.onReady(function() {
    
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
            {name: 'estado', mapping: 'estado'}
        ]
    });
    
    
    var storeMarcas = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: '../../../administracion/tecnico/admi_marca_elemento/getMarcasOltMigracion',
            extraParams: {
                tipoElemento: 'OLT'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
        },
        listeners: {
            load: function() {
                store.getProxy().extraParams.marcaElemento = this.first().data.idMarcaElemento;
                buscar();
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
                {name: 'estado', mapping: 'estado'}
                
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
        width: 550,
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
        width: 550,
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
        title: 'Olt Seleccionados',
        
        renderTo: 'gridAsignaciones'
    });
    
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', 
    {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: 
        {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: 
        {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 550,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() 
                {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() 
                {
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
                forceSelection: true,
                allowBlank: false,
                loadingText: 'Buscando ...',
                queryMode: 'local',
                listClass: 'x-combo-list-small',
                width: '30%',
                listeners: {
                    select: function(){
                        store.getProxy().extraParams.marcaElemento = Ext.getCmp('sltMarca').value;
                    }
                }
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
    filterPanel.toggleCollapse(true);
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
    store.currentPage = 1;
    store.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.canton = Ext.getCmp('sltCanton').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
    store.load();
};

function limpiar() {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltCanton').value = "";
    Ext.getCmp('sltCanton').setRawValue("");
    Ext.getCmp('sltEstado').value = "Activo";
    Ext.getCmp('sltEstado').setRawValue("Activo");
    buscar();
};

function listaOltMigrar()
{    
    var intListaMigrar = sm.getSelection().length;
    
    if( intListaMigrar == 0 )
    {
        Ext.Msg.alert('Alerta', 'Seleccione por lo menos un olt de la lista');
        return 0;
    };
    
    if( intListaMigrar  > 0)
    {
        var strElementoOlt = "";
        
        for(var i=0 ;  i < sm.getSelection().length ; ++i)
        {
            var strFlaBoleans = true;
            if(gridAsignaciones.getStore().getCount() != 0)
            {
                gridAsignaciones.getStore().each(function(record)
                {
                    if(sm.getSelection()[i].get('idElemento') == record.get('idElemento'))
                    {
                        strElementoOlt += ' <br/>'+sm.getSelection()[i].get('nombreElemento')+ "\t Existe en la lista"
                        strFlaBoleans = false
                        return 0;
                    }
                });                
            }
            
            if(strFlaBoleans)
            {
                var r = Ext.create('Asignacion', 
                {
                    idElemento: sm.getSelection()[i].get('idElemento'),
                    nombreElemento: sm.getSelection()[i].get('nombreElemento'),
                    marcaElemento: sm.getSelection()[i].get('marcaElemento'),
                    cantonNombre: sm.getSelection()[i].get('cantonNombre'),
                    modeloElemento: sm.getSelection()[i].get('modeloElemento'),
                    estado: sm.getSelection()[i].get('estado'),
                });
                SelectDataOltMigracion.insert(0,r);
            }
        };
        
        if(strElementoOlt)
        {
            Ext.Msg.alert('Información', strElementoOlt);
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

function ejecutarMigracion()
{
    Ext.Ajax.request({
        url: urlValidarMigracion,
        method: 'POST',
        timeout: 60000,
        success: function(response) 
        {
            var json = Ext.JSON.decode(response.responseText);
            if (!json.success)
            {
                Ext.Msg.alert("Información",json.mensaje);
            }
            else 
            {
                migrarOltAltaDensidad();
            }
        },
        failure: function (rec, op) 
        {
            var json = Ext.JSON.decode(op.response.responseText);
            ejecutaMigracion = false;
            Ext.Msg.alert("Alerta ", "Ha ocurrido un error: "+json.mensaje);
        }
    });
};

function migrarOltAltaDensidad()
{
    var formPanel =  Ext.widget('form', {    
        width: 400,
        bodyPadding: 15,
        items: [
            {
                xtype: "filefield",
                name: "formatoOLT",
                id: "formatoOLT",
                fieldLabel: "Archivo formatoOLT a cargar(*):",
                labelWidth: 150,
                anchor: "100%",
                buttonText: "Seleccionar Archivo...",
            },
            {
                xtype: "filefield",
                name: "formatoSPLITTER",
                id: "formatoSPLITTER",
                fieldLabel: "Archivo formatoSPLITTER a cargar(*):",
                labelWidth: 150,
                anchor: "100%",
                buttonText: "Seleccionar Archivo...",
            },
            {
                xtype: "filefield",
                name: "formatoENLACES",
                id: "formatoENLACES",
                fieldLabel: "Archivo formatoENLACES a cargar(*):",
                labelWidth: 150,
                anchor: "100%",
                buttonText: "Seleccionar Archivo...",
            },
            {
                xtype: "filefield",
                name: "formatoSCOPES",
                id: "formatoSCOPES",
                fieldLabel: "Archivo formatoSCOPES a cargar(*):",
                labelWidth: 150,
                anchor: "100%",
                buttonText: "Seleccionar Archivo...",
            }
        ],
        buttons: [{
            text: 'Ejecutar',
            handler: function () {
                var form = this.up('form').getForm();
                form.submit(
                {
                    url: urlEjecutaMigracion,
                    waitMsg: "Ejecutando validacion de archivos cargados ...",
                    success: function (rec, op) 
                    {
                        var json = Ext.JSON.decode(op.response.responseText);
                        Ext.Msg.alert("Información",json.mensaje);
                        win.destroy();
                    },
                    failure: function (rec, op) 
                    {
                      var json = Ext.JSON.decode(op.response.responseText);
                      Ext.Msg.alert("Alerta ", "Ha ocurrido un error en el proceso de migracion olt alta densidad. " + json.mensaje);
                    },
                });
            }
            },
            {
                text: 'Cancelar',
                handler: function()
                {
                    win.destroy();
                }
            }
        ]
    });

    var win = Ext.create('Ext.window.Window',
        {
            title: 'Carga de archivos para la migración de olts alta densidad',
            modal: true,
            width: 600,
            closable: false,
            layout: 'fit',
            items: [formPanel]
        }).show();
};

function getFileExt(sPTF, bDot) {
    if (!bDot) 
    {
        bDot = false;
    }
    return sPTF.substr(sPTF.lastIndexOf(".") + (!bDot ? 1 : 0));
};

function generarReporteOlt()
{
    var arrayDatosOltMigrar = new Object();
    var jsonDataOltMigrar = '';
    arrayDatosOltMigrar['arrayData'] = new Array();
    var arrayData = Array();
    
    var intListaOltMigrar = gridAsignaciones.getStore().getCount();
    
    if( intListaOltMigrar == 0 )
    {
        Ext.Msg.alert('Alerta', 'Seleccione al menos un olt para poder generar los reportes previo a la migracion olt alta densidad.');
        return 0;
    };
    
    Ext.Msg.confirm('Alerta', 'Se generará el reporte tecnico con los olt seleccionados. ¿Seguro desea continuar?', function(btn)
    {
        if (btn == 'yes')
        {
            Ext.MessageBox.show({
                msg: 'Ejecutando reporte previo a migracion OLT...',
                title:
                        'Procesando',
                progressText: 'Ejecutando reporte previo a migracion OLT...',
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
                url: urlReporteOltMigracion,
                method: 'POST',
                timeout: 60000,
                params: 
                {
                    jsonListaOltMigrar: jsonDataOltMigrar
                },
                success: function(response) 
                {
                    var json = Ext.JSON.decode(response.responseText);
                    Ext.Msg.alert("Información",json.mensaje);
                    store.load();
                    gridAsignaciones.getStore().removeAll();
                },
                failure: function (rec, op) 
                {
                    var json = Ext.JSON.decode(op.response.responseText);
                    Ext.Msg.alert("Alerta ", "Ha ocurrido un error: "+json.mensaje);
                },
            });
        };
    });
};
