Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    var boolSetJurisdiccion = true;

    // FILTROS BÚSQUEDA

    // DATASTORES
    dataStoreTiposElemento = new Ext.data.Store(
        {
            autoLoad: true,
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlCargarTiposElemento,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'idTipoElemento', mapping: 'idTipoElemento', type: 'integer'},
                    {name: 'nombreTipoElemento', mapping: 'nombreTipoElemento', type: 'string'}
                ]
        });

    dataStoreMarcasElemento = new Ext.data.Store(
        {
            total: 'total',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: urlCargarMarcasElemento,
                    extraParams:
                        {
                            tipo: tipoDefault
                        },
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'idMarcaElemento', mapping: 'idMarcaElemento', type: 'string'},
                    {name: 'nombreMarcaElemento', mapping: 'nombreMarcaElemento', type: 'string'}
                ]
        });

    dataStoreCantones = new Ext.data.Store(
        {
            total: 'total',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: urlCargarCantones,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'id_canton', mapping: 'id_canton', type: 'integer'},
                    {name: 'nombre_canton', mapping: 'nombre_canton', type: 'string'}
                ]
        });

    Ext.define('ModelStoreEmpresas',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'prefijo', mapping: 'prefijo'},
                    {name: 'nombre_empresa', mapping: 'nombre_empresa'}
                ]
        });

    dataStoreEmpresas = Ext.create('Ext.data.Store',
        {
            autoLoad: false,
            model: 'ModelStoreEmpresas',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlCargarEmpresas,
                    params:
                        {
                            empresa: ''
                        },
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'registros'
                        }
                }
        });
        
    dataStoreEmpresaAsignacion = Ext.create('Ext.data.Store',
        {
            autoLoad: false,
            model: 'ModelStoreEmpresas',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlCargarEmpresas,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'registros'
                        }
                }
        });

    dataStoreModelosElemento = new Ext.data.Store
        ({
            total: 'total',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: urlCargarModelosElemento,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'idModeloElemento', mapping: 'idModeloElemento', type: 'string'},
                    {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento', type: 'string'}
                ]
        });

    dataStoreJurisdiccion = new Ext.data.Store(
        {
            autoLoad: false,
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlCargarJurisdicciones,
                    extraParams:
                        {
                            empresa: empresa
                        },
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'id_jurisdiccion', mapping: 'id_jurisdiccion', type: 'string'},
                    {name: 'nombre_jurisdiccion', mapping: 'nombre_jurisdiccion', type: 'string'}
                ]
        });

    dataStoreTiposElemento.on('load', function()
    {
        Ext.getCmp('cbxTiposElemento').value = parseInt(tipoDefault);
        // Se eliminan los tipo de elementos MOTO y VEHICULO que no son asignables a otra empresa.
        dataStoreTiposElemento.remove(dataStoreTiposElemento.findRecord("nombreTipoElemento", "MOTO"));
        dataStoreTiposElemento.remove(dataStoreTiposElemento.findRecord("nombreTipoElemento", "VEHICULO"));
        
        dataStoreMarcasElemento.getProxy().extraParams.tipo = tipoDefault;
        dataStoreMarcasElemento.load();
    });
    
    dataStoreMarcasElemento.on('load', function()
    {
        dataStoreMarcasElemento.insert(0, {idMarcaElemento: 'Todas', nombreMarcaElemento: 'Todas'});
        dataStoreEmpresas.load();
    });
    
    dataStoreModelosElemento.on('load', function()
    {
        dataStoreModelosElemento.insert(0, {idModeloElemento: 'Todos', nombreModeloElemento: 'Todos'});
    });
    
    dataStoreEmpresas.on('load', function()
    {
        Ext.getCmp('cbxEmpresas').value = empresa;
        dataStoreJurisdiccion.getProxy().extraParams.empresa = empresa;
        dataStoreJurisdiccion.load();
    });
    
    dataStoreJurisdiccion.on('load', function()
    {
        dataStoreJurisdiccion.insert(0, {id_jurisdiccion: 'Todas', nombre_jurisdiccion: 'Todas'});
        
        if(boolSetJurisdiccion)
        {
            Ext.getCmp('cbxJurisdicciones').value = jurisdiccionDefault;
            boolSetJurisdiccion = false;
        }
        dataStoreCantones.getProxy().extraParams.jurisdiccion = jurisdiccionDefault;
        dataStoreCantones.load();
    });
    
    dataStoreCantones.on('load', function()
    {
        dataStoreCantones.insert(0, {id_canton: 'Todos', nombre_canton: 'Todos'});
    });

    dataStoreElementosEmpresa = new Ext.data.Store
        ({
            pageSize: 1000,
            total: 'total',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlGridElementosEmpresa,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                },
            fields:
                [
                    {name: 'id_elemento', mapping: 'id_elemento'},
                    {name: 'nombre_elemento', mapping: 'nombre_elemento'},
                    {name: 'tipo_elemento', mapping: 'tipo_elemento'},
                    {name: 'marca_elemento', mapping: 'marca_elemento'},
                    {name: 'modelo_elemento', mapping: 'modelo_elemento'},
                    {name: 'estado', mapping: 'estado'}
                ]
        });

    // COMPONENTES TOOLBAR
    cbxTiposElemento =
        {
            xtype: 'combobox',
            fieldLabel: 'Tipo',
            id: 'cbxTiposElemento',
            labelWidth: '5',
            store: dataStoreTiposElemento,
            displayField: 'nombreTipoElemento',
            valueField: 'idTipoElemento',
            loadingText: 'Buscando ...',
            listClass: 'x-combo-list-small',
            queryMode: 'local',
            width: 300,
            typeAhead: true,
            editable: false,
            matchFieldWidth: false,
            listeners:
                {
                    select: function()
                    {
                        Ext.getCmp('cbxMarcasElemento').value = 'Todas';
                        Ext.getCmp('cbxMarcasElemento').setRawValue('Todas');
                        dataStoreMarcasElemento.getProxy().extraParams.tipo = Ext.getCmp('cbxTiposElemento').value;
                        dataStoreMarcasElemento.load();
                        Ext.getCmp('cbxModelosElemento').value = 'Todos';
                        Ext.getCmp('cbxModelosElemento').setRawValue('Todos');
                    }
                },
            listConfig:
                {
                    listeners:
                        {
                            beforeshow: function(picker)
                            {
                                picker.minWidth = picker.up('combobox').getSize().width;
                            }
                        }
                }
        };

    cbxMarcasElemento =
        {
            xtype: 'combobox',
            fieldLabel: 'Marca',
            id: 'cbxMarcasElemento',
            value: 'Todas',
            labelWidth: '5',
            store: dataStoreMarcasElemento,
            displayField: 'nombreMarcaElemento',
            valueField: 'idMarcaElemento',
            loadingText: 'Buscando ...',
            listClass: 'x-combo-list-small',
            queryMode: 'local',
            width: 300,
            allowBlank: true,
            editable: false,
            matchFieldWidth: false,
            listeners:
                {
                    select: function()
                    {
                        Ext.getCmp('cbxModelosElemento').value = 'Todos';
                        Ext.getCmp('cbxModelosElemento').setRawValue('Todos');
                        
                        dataStoreModelosElemento.getProxy().extraParams.tipo  = Ext.getCmp('cbxTiposElemento').value;
                        dataStoreModelosElemento.getProxy().extraParams.marca = Ext.getCmp('cbxMarcasElemento').value;
                        
                        dataStoreModelosElemento.load();
                    }
                },
            listConfig:
                {
                    listeners:
                        {
                            beforeshow: function(picker)
                            {
                                picker.minWidth = picker.up('combobox').getSize().width;
                            }
                        }
                }
        };

    cbxCantones =
        {
            xtype: 'combobox',
            fieldLabel: 'Cant\xf3n',
            id: 'cbxCantones',
            value: 'Todos',
            labelWidth: '7',
            store: dataStoreCantones,
            displayField: 'nombre_canton',
            valueField: 'id_canton',
            loadingText: 'Buscando ...',
            listClass: 'x-combo-list-small',
            queryMode: 'local',
            width: 300,
            allowBlank: true,
            editable: false,
            matchFieldWidth: false,
            listConfig:
                {
                    listeners:
                        {
                            beforeshow: function(picker)
                            {
                                picker.minWidth = picker.up('combobox').getSize().width;
                            }
                        }
                }
        };

    cbxEmpresas =
        {
            xtype: 'combobox',
            fieldLabel: 'Empresa',
            id: 'cbxEmpresas',
            labelWidth: '5',
            store: dataStoreEmpresas,
            displayField: 'nombre_empresa',
            valueField: 'prefijo',
            loadingText: 'Buscando ...',
            listClass: 'x-combo-list-small',
            queryMode: 'local',
            width: 300,
            editable: false,
            matchFieldWidth: false,
            listeners:
                {
                    select: function()
                    {
                        Ext.getCmp('cbxJurisdicciones').value = 'Todas';
                        
                        dataStoreJurisdiccion.getProxy().extraParams.empresa = Ext.getCmp('cbxEmpresas').value;
                        dataStoreJurisdiccion.load();
                    }
                },
            listConfig:
                {
                    listeners:
                        {
                            beforeshow: function(picker)
                            {
                                picker.minWidth = picker.up('combobox').getSize().width;
                            }
                        }
                }
        };

    cbxModelosElemento =
        {
            xtype: 'combobox',
            fieldLabel: 'Modelo',
            id: 'cbxModelosElemento',
            value: 'Todos',
            labelWidth: '5',
            store: dataStoreModelosElemento,
            displayField: 'nombreModeloElemento',
            valueField: 'idModeloElemento',
            loadingText: 'Buscando ...',
            listClass: 'x-combo-list-small',
            queryMode: 'local',
            width: 300,
            allowBlank: true,
            editable: false,
            matchFieldWidth: false,
            listConfig:
                {
                    listeners:
                        {
                            beforeshow: function(picker)
                            {
                                picker.minWidth = picker.up('combobox').getSize().width;
                            }
                        }
                }
        };

    cbxJurisdicciones =
        {
            xtype: 'combobox',
            fieldLabel: 'Jurisdicci\xf3n',
            id: 'cbxJurisdicciones',
            name: 'cbxJurisdicciones',
            labelWidth: '7',
            store: dataStoreJurisdiccion,
            displayField: 'nombre_jurisdiccion',
            valueField: 'id_jurisdiccion',
            loadingText: 'Buscando ...',
            listClass: 'x-combo-list-small',
            queryMode: 'local',
            width: 300,
            typeAhead: true,
            editable: false,
            matchFieldWidth: false,
            listeners:
                {
                    select: function()
                    {
                        Ext.getCmp('cbxCantones').value = 'Todos';
                        Ext.getCmp('cbxCantones').setRawValue('Todos');
                        
                        dataStoreCantones.getProxy().extraParams.jurisdiccion = Ext.getCmp('cbxJurisdicciones').value;
                        dataStoreCantones.load();
                    }
                },
            listConfig:
                {
                    listeners:
                        {
                            beforeshow: function(picker)
                            {
                                picker.minWidth = picker.up('combobox').getSize().width;
                            }
                        }
                }
        };

    cbxEstados =
        {
            xtype: 'combobox',
            fieldLabel: 'Estado',
            labelWidth: '5',
            id: 'cbxEstados',
            value: 'Activo',
            editable: false,
            store: [
                ['Activo', 'Activo'],
                ['Eliminado', 'Eliminado']
            ],
            width: 300
        };

    // PANEL BÚSQUEDA
    Ext.create('Ext.panel.Panel',
        {
            renderTo: 'filtrosBusqueda',
            bodyPadding: 6,
            border: false,
            buttonAlign: 'center',
            layout:
                {
                    type: 'table',
                    columns: 6,
                    align: 'stretch'
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            collapsible: true,
            collapsed: false,
            width: 980,
            title: 'Criterios de B\xfasqueda',
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function()
                        {
                            dataStoreElementosEmpresa.getProxy().extraParams.tipo         = Ext.getCmp('cbxTiposElemento').value;
                            dataStoreElementosEmpresa.getProxy().extraParams.marca        = Ext.getCmp('cbxMarcasElemento').value;
                            dataStoreElementosEmpresa.getProxy().extraParams.canton       = Ext.getCmp('cbxCantones').value;
                            dataStoreElementosEmpresa.getProxy().extraParams.empresa      = Ext.getCmp('cbxEmpresas').value;
                            dataStoreElementosEmpresa.getProxy().extraParams.modelo       = Ext.getCmp('cbxModelosElemento').value;
                            dataStoreElementosEmpresa.getProxy().extraParams.jurisdiccion = Ext.getCmp('cbxJurisdicciones').value;
                            dataStoreElementosEmpresa.getProxy().extraParams.estado       = Ext.getCmp('cbxEstados').value;
                            dataStoreElementosEmpresa.getProxy().extraParams.nombre       = Ext.getCmp('txtNombreElemento').value;
                            
                            dataStoreElementosEmpresa.currentPage = 1;
                            
                            dataStoreElementosEmpresa.load();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function()
                        {
                            Ext.getCmp('cbxMarcasElemento').value  = "Todas";
                            Ext.getCmp('cbxCantones').value        = "Todos";
                            Ext.getCmp('cbxModelosElemento').value = "Todos";
                            Ext.getCmp('cbxJurisdicciones').value  = "Todas";
                            Ext.getCmp('cbxEstados').value         = "Activo";
                            Ext.getCmp('txtNombreElemento').value  = "";

                            Ext.getCmp('cbxMarcasElemento').setRawValue("Todas");
                            Ext.getCmp('cbxCantones').setRawValue("Todos");
                            Ext.getCmp('cbxModelosElemento').setRawValue("Todos");
                            Ext.getCmp('cbxJurisdicciones').setRawValue("Todas");
                            Ext.getCmp('cbxEstados').setRawValue("Activo");
                            Ext.getCmp('txtNombreElemento').setRawValue("");
                        }
                    }

                ],
            items:
                [
                    cbxTiposElemento,
                    {
                        width: 30,
                        border: false
                    },
                    cbxEmpresas,
                    {
                        width: 30,
                        border: false
                    },
                    cbxJurisdicciones,
                    {
                        width: 5,
                        border: false
                    },
                    {
                        xtype: 'textfield',
                        id: 'txtNombreElemento',
                        fieldLabel: 'Nombre',
                        labelWidth: '5',
                        value: '',
                        width: 300
                    },
                    {
                        width: 30,
                        border: false
                    },
                    cbxEstados,
                    {
                        width: 30,
                        border: false
                    },
                    cbxCantones,
                    {
                        width: 5, 
                        border: false
                    },
                    cbxMarcasElemento,
                    {
                        width: 30, 
                        border: false
                    },
                    cbxModelosElemento,
                    {
                        width: 30, 
                        border: false
                    },
                    {
                        width: 30, 
                        border: false
                    },
                    {
                        width: 30, 
                        border: false
                    }
                ]
        });

    var isEvent = true;

    CheckBoxModelAsignarMetaMasiva = Ext.create('Ext.selection.CheckboxModel',
        {
            checkOnly: true,
            showHeaderCheckbox: false,
            listeners:
                {
                    selectionchange: function(model, selection)
                    {
                        var intSizeGrid = selection.length;
                        if (isEvent && model.lastFocused != null)
                        {
                            if ('Eliminado' == model.lastFocused.get('estado'))
                            {
                                model.doDeselect(model.lastFocused, false);
                                model.lastFocused = null;
                                Ext.Msg.show(
                                    {
                                        title: 'Alerta',
                                        msg: 'Registro tiene estado "Eliminado", No puede ser seleccionado',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.WARNING
                                    });
                                intSizeGrid -= 1;
                            }
                        }
                        gridEmpresaElemento.down('#asignarEmpresaElementos').setDisabled(intSizeGrid <= 0);
                    }
                }
        });
        
    // PANEL LISTADO 
    gridEmpresaElemento = Ext.create('Ext.grid.Panel',
        {
            id: 'gridEmpresaElemento',
            width: 980,
            height: 370,
            bufferedRenderer: false,
            frame: false,
            store: dataStoreElementosEmpresa,
            loadMask: true,
            renderTo: 'gridEmpresaElemento',
            selModel: CheckBoxModelAsignarMetaMasiva,
            plugins: [{ptype: 'pagingselectpersist'}],
            split: true,
            region: 'north',
            dockedItems:
                [
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items:
                            [
                                {
                                    iconCls: 'icon_add',
                                    text: 'Seleccionar Activos',
                                    itemId: 'select',
                                    scope: this,
                                    handler: function()
                                    {
                                        gridEmpresaElemento.getStore().each(function(record)
                                        {
                                            isEvent = false;
                                            if ('Eliminado' != record.get('estado'))
                                            {
                                                gridEmpresaElemento.getSelectionModel().select(record, true);
                                            }
                                        });

                                        isEvent = true;
                                    }
                                },
                                {
                                    iconCls: 'icon_limpiar',
                                    text: 'Desmarcar Seleccionados',
                                    itemId: 'clear',
                                    scope: this,
                                    handler: function()
                                    {
                                        isEvent = false;
                                        Ext.getCmp('gridEmpresaElemento').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                                        isEvent = true;
                                    }
                                },
                                {xtype: 'tbfill'},
                                {
                                    itemId: 'asignarEmpresaElementos',
                                    text: 'Asignar Empresa a Elementos',
                                    scope: this,
                                    tooltip: 'Asigna la Empresa a los elementos seleccionados',
                                    iconCls: 'btn-asignar-empresa',
                                    disabled: true,
                                    handler: function()
                                    {
                                        dataStoreEmpresaAsignacion.getProxy().extraParams.empresa = Ext.getCmp('cbxEmpresas').value;
                                        dataStoreEmpresaAsignacion.load();
                                        
                                        asignarEmpresaElementosMasivaSeleccion(gridEmpresaElemento);
                                    }
                                }
                            ]
                    }
                ],
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store: dataStoreElementosEmpresa,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar."
                }),
            viewConfig:
                {
                    enableTextSelection: true,
                    loadingText: '<b>Cargando Elementos, Por favor espere...',
                    emptyText: '<center><b>*** No se encontraron Elementos ***',
                    loadMask: true
                },
            columns:
                [
                    new Ext.grid.RowNumberer({width: 35}),
                    {
                        id: 'id_elemento',
                        header: 'ID',
                        dataIndex: 'id_elemento',
                        hidden: true
                    },
                    {
                        id: 'nombre_elemento',
                        header: 'Nombre',
                        dataIndex: 'nombre_elemento',
                        hidden: false,
                        width: 380,
                        sortable: true
                    },
                    {
                        id: 'tipo_elemento',
                        header: 'Tipo',
                        dataIndex: 'tipo_elemento',
                        hidden: false,
                        width: 150,
                        sortable: true
                    },
                    {
                        id: 'marca_elemento',
                        header: 'Marca',
                        dataIndex: 'marca_elemento',
                        hidden: false,
                        width: 150,
                        sortable: true
                    },
                    {
                        id: 'modelo_elemento',
                        header: 'Modelo',
                        dataIndex: 'modelo_elemento',
                        hidden: false,
                        width: 150,
                        sortable: true
                    },
                    {
                        id: 'estado',
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 75
                    }
                ]
        });
});

function asignarEmpresaElementosMasivaSeleccion(grid)
{
    var formPanel = Ext.create('Ext.form.Panel',
        {
            id: 'formAsginarEmpresaElementos',
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 125,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 700
                            },
                        layout:
                            {
                                type: 'table',
                                columns: 4,
                                align: 'left'
                            },
                        items:
                            [
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Empresa',
                                    id: 'cbxAsignacionEmpresa',
                                    labelWidth: '5',
                                    store: dataStoreEmpresaAsignacion,
                                    displayField: 'nombre_empresa',
                                    valueField: 'prefijo',
                                    loadingText: 'Buscando ...',
                                    listClass: 'x-combo-list-small',
                                    queryMode: 'local',
                                    width: 250,
                                    editable: false,
                                    matchFieldWidth: false,
                                    listConfig:
                                        {
                                            listeners:
                                                {
                                                    beforeshow: function(picker)
                                                    {
                                                        picker.minWidth = picker.up('combobox').getSize().width;
                                                    }
                                                }
                                        }
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Asignar',
                        type: 'submit',
                        handler: function()
                        {
                            var form = Ext.getCmp('formAsginarEmpresaElementos').getForm();

                            if (form.isValid())
                            {
                                var strEmpresaAsignacion = Ext.getCmp('cbxAsignacionEmpresa').getValue();
                                var strEmpresaAsignacionN = Ext.getCmp('cbxAsignacionEmpresa').getRawValue();
                                if (strEmpresaAsignacion != '0' && strEmpresaAsignacion != null && strEmpresaAsignacion != '')
                                {
                                    var strIdElemento = '';

                                    Ext.Msg.confirm('Alerta',
                                        'Los elementos seleccionados se asignar\xe1n masivamente la Empresa ' + strEmpresaAsignacionN +
                                        '<br><br>¿Desea continuar?',
                                        function(btn)
                                        {
                                            if (btn == 'yes')
                                            {
                                                var xRowSelMod = grid.getSelectionModel().getSelection();

                                                for (var i = 0; i < xRowSelMod.length; i++)
                                                {
                                                    var RowSel = xRowSelMod[i];

                                                    strIdElemento = strIdElemento + RowSel.get('id_elemento');

                                                    if (i < (xRowSelMod.length - 1))
                                                    {
                                                        strIdElemento = strIdElemento + '|';
                                                    }
                                                }
                                                
                                                if (typeof win != 'undefined' && win != null)
                                                {
                                                    win.destroy();
                                                }

                                                connGrabandoDatos.request
                                                    ({
                                                        url: urlAsignarEmpresaElementos,
                                                        method: 'post',
                                                        dataType: 'json',
                                                        params:
                                                            {
                                                                empresa: strEmpresaAsignacion,
                                                                elementos: strIdElemento
                                                            },
                                                        success: function(response)
                                                        {
                                                            if (response.responseText == 'OK')
                                                            {
                                                                Ext.Msg.alert('Informaci\xf3n', 'Se guardaron los cambios con \xe9xito');
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.alert('Error', 'Hubo un problema al guardar los cambios');
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.Msg.alert('Error', result.responseText);
                                                        }
                                                    });
                                            }
                                        }
                                    );
                                }
                                else
                                {
                                    Ext.Msg.show({
                                        title: 'Atenci\xf3n',
                                        msg: 'No ha seleccionado la Empresa',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            }
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
        });

    win = Ext.create('Ext.window.Window',
        {
            title: 'Asignar Empresa Elementos',
            modal: true,
            width: 300,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

var connGrabandoDatos = new Ext.data.Connection(
    {
        listeners:
            {
                'beforerequest':
                    {
                        fn: function(con, opt)
                        {
                            Ext.MessageBox.show(
                                {
                                    msg: 'Grabando los datos, Por favor espere!!',
                                    progressText: 'Saving...',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function(con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function(con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }
    });
    