Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    modelStorePersonalExterno = Ext.define('ModelStore',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id_persona', mapping: 'id_persona'},
                    {name: 'id_persona_empresa_rol', mapping: 'id_persona_empresa_rol'},
                    {name: 'id_empresa', mapping: 'id_empresa'},
                    {name: 'nombre_empresa', mapping: 'nombre_empresa'},
                    {name: 'tipo_identificacion', mapping: 'tipo_identificacion'},
                    {name: 'identificacion', mapping: 'identificacion'},
                    {name: 'nombres', mapping: 'nombres'},
                    {name: 'apellidos', mapping: 'apellidos'},
                    {name: 'nacionalidad', mapping: 'nacionalidad'},
                    {name: 'direccion', mapping: 'direccion'},
                    {name: 'empresa_externa', mapping: 'empresa_externa'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'meta_bruta', mapping: 'meta_bruta'},
                    {name: 'meta_activa', mapping: 'meta_activa'},
                    {name: 'action1', mapping: 'action1'},
                    {name: 'action2', mapping: 'action2'},
                    {name: 'action3', mapping: 'action3'},
                    {name: 'action4', mapping: 'action4'}
                ],
            idProperty: 'id_persona_empresa_rol'
        });

    dataStorePersonalExterno = new Ext.data.Store(
        {
            pageSize: 20,
            model: modelStorePersonalExterno,
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlGrid,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            identificacion: '',
                            nombre: '',
                            razonSocial: '',
                            empresaExterna: '',
                            estado: 'Activo'
                        }
                },
            autoLoad: true
        });

    var pluginExpanded = true;

    //****************  DOCKED ITEMS - BOTONES PARTE SUPERIOR ************************
    var permiso = $("#ROLE_182-8");
    var boolPermiso1 = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    var eliminarBtn = "";
    sm = "";
    if (boolPermiso1)
    {
        sm = Ext.create('Ext.selection.CheckboxModel',
            {
                checkOnly: true
            });

        eliminarBtn = Ext.create('Ext.button.Button',
            {
                iconCls: 'icon_delete',
                text: 'Eliminar',
                itemId: 'deleteAjax',
                scope: this,
                disabled: true,
                handler: function()
                {
                    var param = '';
                    var selection = gridPersonalExterno.getPlugin('pagingSelectionPersistence').getPersistedSelection();

                    if (selection.length > 0)
                    {
                        var estado = 0;
                        for (var i = 0; i < selection.length; ++i)
                        {
                            param = param + selection[i].getId();

                            if (i < (selection.length - 1))
                            {
                                param = param + '|';
                            }
                        }

                        if (estado == 0)
                        {
                            Ext.Msg.confirm('Alerta', 'Se eliminarán los registros seleccionados. Desea continuar?', function(btn)
                            {
                                if (btn == 'yes')
                                {
                                    Ext.Ajax.request(
                                        {
                                            url: urlDeleteAjax,
                                            timeout: 600000,
                                            method: 'post',
                                            params: {param: param},
                                            success: function(response)
                                            {
                                                var text = response.responseText;
                                                Ext.Msg.show(
                                                    {
                                                        title: 'Información',
                                                        msg: text,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.MessageBox.INFO
                                                    });
                                                dataStorePersonalExterno.load();
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.show(
                                                    {
                                                        title: 'Error',
                                                        msg: result.statusText,
                                                        buttons: Ext.Msg.OK,
                                                        icon: Ext.MessageBox.ERROR
                                                    });
                                            }
                                        });
                                }
                            });

                        }
                        else
                        {
                            alert('Por lo menos uno de las registros se encuentra en estado ELIMINADO');
                        }
                    }
                    else
                    {
                        alert('Seleccione por lo menos un registro de la lista');
                    }
                }
            });
    }

    var isEvent = true;

    var toolbar = Ext.create('Ext.toolbar.Toolbar',
        {
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
                            gridPersonalExterno.getStore().each(function(record)
                            {
                                isEvent = false;
                                if ('Eliminado' != record.get('estado'))
                                {
                                    gridPersonalExterno.getSelectionModel().select(record, true);
                                }
                            });

                            isEvent = true;
                        }
                    },
                    {
                        iconCls: 'icon_limpiar',
                        text: 'Borrar Todos',
                        itemId: 'clear',
                        scope: this,
                        handler: function()
                        {
                            isEvent = false;
                            Ext.getCmp('gridPersonaExterno').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                            isEvent = true;
                        }
                    },
                    {xtype: 'tbfill'},
                    eliminarBtn,
                    {
                        itemId: 'asignarMetaButton',
                        text: 'Asignar Meta',
                        scope: this,
                        tooltip: 'Asigna meta del empleado seleccionado',
                        iconCls: 'btn-asignar-meta',
                        disabled: true,
                        handler: function()
                        {
                            asignarMetaMasivaSeleccion(gridPersonalExterno);
                        }
                    }
                ]
        });

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
                        gridPersonalExterno.down('#deleteAjax').setDisabled(intSizeGrid <= 0);
                        gridPersonalExterno.down('#asignarMetaButton').setDisabled(intSizeGrid <= 0);
                    }
                }
        });

    gridPersonalExterno = Ext.create('Ext.grid.Panel',
        {
            id: 'gridPersonaExterno',
            width: 1050,
            height: 405,
            store: dataStorePersonalExterno,
            loadMask: true,
            renderTo: 'grid',
            selModel: CheckBoxModelAsignarMetaMasiva,
            iconCls: 'icon-grid',
            plugins: [{ptype: 'pagingselectpersist'}],
            dockedItems: [toolbar],
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store: dataStorePersonalExterno,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar."
                }),
            viewConfig:
                {
                    enableTextSelection: true,
                    id: 'gv'
                },
            columns:
                [
                    {
                        id: 'id_persona_empresa_rol',
                        header: 'IdPersonaEmpresaRol',
                        dataIndex: 'id_persona_empresa_rol',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'id_persona',
                        header: 'IdPersona',
                        dataIndex: 'id_persona',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'id_empresa',
                        header: 'IdEmpresa',
                        dataIndex: 'id_empresa',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'nombre_empresa',
                        header: 'Nombre Empresa',
                        dataIndex: 'nombre_empresa',
                        width: 150,
                        sortable: true,
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'tipo_identificacion',
                        header: 'Tipo Ident.',
                        dataIndex: 'tipo_identificacion',
                        width: 70,
                        sortable: true,
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'identificacion',
                        header: 'Identificación',
                        dataIndex: 'identificacion',
                        width: 100,
                        sortable: true
                    },
                    {
                        id: 'nombres',
                        header: 'Nombres',
                        dataIndex: 'nombres',
                        width: 180,
                        sortable: true
                    },
                    {
                        id: 'apellidos',
                        header: 'Apellidos',
                        dataIndex: 'apellidos',
                        width: 180,
                        sortable: true
                    },
                    {
                        id: 'nacionalidad',
                        header: 'Nacionalidad',
                        dataIndex: 'nacionalidad',
                        width: 80,
                        sortable: true,
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'direccion',
                        header: 'Direccion',
                        dataIndex: 'direccion',
                        width: 250,
                        sortable: true,
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'empresaExterna',
                        header: 'Empresa Externa',
                        dataIndex: 'empresa_externa',
                        width: 230,
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 60,
                        sortable: true
                    },
                    {
                        header: 'Meta Bruta',
                        dataIndex: 'meta_bruta',
                        width: 70,
                        align: 'right',
                        sortable: true
                    },
                    {
                        header: 'Meta Activa',
                        dataIndex: 'meta_activa',
                        width: 70,
                        align: 'right',
                        sortable: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 130,
                        items: [
                            {
                                getClass: function(v, meta, rec)
                                {
                                    var permiso = $("#ROLE_182-6");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                    if (!boolPermiso)
                                    {
                                        rec.data.action1 = "icon-invisible";
                                    }

                                    if (rec.get('action1') == "icon-invisible")
                                    {
                                        this.items[0].tooltip = '';
                                    }
                                    else
                                    {
                                        this.items[0].tooltip = 'Ver';
                                    }

                                    return rec.get('action1');
                                },
                                tooltip: 'Ver',
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = dataStorePersonalExterno.getAt(rowIndex);
                                    var permiso = $("#ROLE_182-6");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                    if (!boolPermiso)
                                    {
                                        rec.data.action1 = "icon-invisible";
                                    }

                                    if (rec.get('action1') != "icon-invisible")
                                    {
                                        window.location = rec.get('id_persona_empresa_rol') + "/show";
                                    }
                                    else
                                    {
                                        Ext.Msg.show({
                                            title: 'Error',
                                            msg: 'No tiene permisos para realizar esta acción',
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }

                                }
                            },
                            {
                                getClass: function(v, meta, rec)
                                {
                                    var permiso = $("#ROLE_182-4");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                    if (!boolPermiso)
                                    {
                                        rec.data.action2 = "icon-invisible";
                                    }
                                    if (rec.get('action2') == "icon-invisible")
                                    {
                                        this.items[1].tooltip = '';
                                    }
                                    else
                                    {
                                        this.items[1].tooltip = 'Editar';
                                    }
                                    return rec.get('action2');
                                },
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = dataStorePersonalExterno.getAt(rowIndex);
                                    var permiso = $("#ROLE_182-4");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                    if (!boolPermiso)
                                    {
                                        rec.data.action2 = "icon-invisible";
                                    }

                                    if (rec.get('action2') != "icon-invisible")
                                    {
                                        window.location = rec.get('id_persona_empresa_rol') + "/edit";
                                    }
                                    else
                                    {
                                        Ext.Msg.show({
                                            title: 'Error',
                                            msg: 'No tiene permisos para realizar esta acción',
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                }
                            },
                            {
                                getClass: function(v, meta, rec)
                                {
                                    var permiso = $("#ROLE_182-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso)
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }
                                    var permiso = $("#ROLE_182-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                    if (!boolPermiso)
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }

                                    if (rec.get('action3') == "icon-invisible")
                                    {
                                        this.items[2].tooltip = '';
                                    }
                                    else
                                    {
                                        this.items[2].tooltip = 'Eliminar';
                                    }

                                    return rec.get('action3');
                                },
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = dataStorePersonalExterno.getAt(rowIndex);

                                    var permiso = $("#ROLE_182-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso)
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }

                                    var permiso = $("#ROLE_182-8");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (!boolPermiso)
                                    {
                                        rec.data.action3 = "icon-invisible";
                                    }

                                    if (rec.get('action3') != "icon-invisible")
                                    {
                                        Ext.Msg.confirm('Alerta', 'Se eliminará el registro. Desea continuar?', function(btn)
                                        {
                                            if (btn == 'yes')
                                            {
                                                Ext.Ajax.request(
                                                    {
                                                        url: urlDeleteAjax,
                                                        method: 'post',
                                                        params: {param: rec.get('id_persona_empresa_rol')},
                                                        success: function(response)
                                                        {
                                                            var text = response.responseText;
                                                            Ext.Msg.show({
                                                                title: 'Información',
                                                                msg: text,
                                                                buttons: Ext.Msg.OK,
                                                                icon: Ext.MessageBox.INFO
                                                            });
                                                            dataStorePersonalExterno.load();
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.Msg.show({
                                                                title: 'Error',
                                                                msg: result.statusText,
                                                                buttons: Ext.Msg.OK,
                                                                icon: Ext.MessageBox.ERROR
                                                            });
                                                        }
                                                    });
                                            }
                                        });
                                    }
                                    else
                                    {
                                        Ext.Msg.show({
                                            title: 'Error',
                                            msg: 'No tiene permisos para realizar esta acción',
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }

                                }
                            },
                            {
                                /*Asignar Metas*/
                                getClass: function(v, meta, rec)
                                {
                                    strAsignarMetas = 'button-grid-invisible';
                                    var permiso = $("#ROLE_182-2980");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (boolPermiso)
                                    {
                                        if ('Eliminado' !== rec.get('estado'))
                                        {
                                            strAsignarMetas = 'btn-acciones btn-asignar-meta';
                                        }
                                    }
                                    return strAsignarMetas;
                                },
                                tooltip: 'Asignar Meta',
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = dataStorePersonalExterno.getAt(rowIndex);
                                    var strMetaBruta = rec.data.meta_bruta;
                                    var strMetaActiva = Math.round(((rec.data.meta_activa / rec.data.meta_bruta) * 100));
                                    var id_persona_empresa_rol = rec.data.id_persona_empresa_rol;

                                    var permiso = $("#ROLE_182-2980");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (boolPermiso)
                                    {
                                        var arrayParametros = [];
                                        arrayParametros['intIdPersonalEmpresaRol'] = id_persona_empresa_rol;
                                        arrayParametros['strMetaBruta'] = strMetaBruta;
                                        arrayParametros['strMetaActiva'] = strMetaActiva;
                                        arrayParametros['accion'] = 'Guardar';
                                        arrayParametros['store'] = dataStorePersonalExterno;
                                        asignarMeta(arrayParametros);
                                    }
                                    else
                                    {
                                        Ext.Msg.show({
                                            title: 'Error',
                                            msg: 'No tiene permisos para realizar esta acción',
                                            buttons: Ext.Msg.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                }
                            }
                        ]
                    }
                ]
        });

    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    Ext.define('ModelStoreEmpresasExternas',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id_persona', mapping: 'id_persona'},
                    {name: 'id_persona_empresa_rol', mapping: 'id_persona_empresa_rol'},
                    {name: 'id_empresa', mapping: 'id_empresa'},
                    {name: 'nombre_empresa', mapping: 'nombre_empresa'},
                    {name: 'razon_social', mapping: 'razon_social'},
                    {name: 'tipo_identificacion', mapping: 'tipo_identificacion'},
                    {name: 'identificacion', mapping: 'identificacion'},
                    {name: 'nombres', mapping: 'nombres'},
                    {name: 'apellidos', mapping: 'apellidos'},
                    {name: 'nacionalidad', mapping: 'nacionalidad'},
                    {name: 'direccion', mapping: 'direccion'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'action1', mapping: 'action1'},
                    {name: 'action2', mapping: 'action2'},
                    {name: 'action3', mapping: 'action3'}
                ],
            idProperty: 'id_persona_empresa_rol'
        });

    storeEmpresasExternas = Ext.create('Ext.data.Store',
        {
            autoLoad: true,
            model: 'ModelStoreEmpresasExternas',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlCargarEmpresasExternasActivas,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            identificacion: '',
                            nombre: '',
                            razonSocial: '',
                            empresaExterna: '',
                            estado: 'Activo'
                        }
                }
        });

    Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            renderTo: 'filtro',
            layout:
                {
                    type: 'table',
                    align: 'stretch',
                    columns: 6
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            collapsible: true,
            collapsed: true,
            width: 1050,
            title: 'Criterios de búsqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function()
                    {
                        getPersonalExterno();
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function()
                    {
                        Ext.getCmp('txtIdentificacion').value = "";
                        Ext.getCmp('txtIdentificacion').setRawValue("");
                        Ext.getCmp('txtNombres').value = "";
                        Ext.getCmp('txtNombres').setRawValue("");
                        Ext.getCmp('txtApellidos').value = "";
                        Ext.getCmp('txtApellidos').setRawValue("");
                        Ext.getCmp('cbxEstado').value = "Activo";
                        Ext.getCmp('cbxEstado').setRawValue("Activo");
                        Ext.getCmp('cbxEmpresaExterna').value = "";
                        Ext.getCmp('cbxEmpresaExterna').setRawValue("");
                        getPersonalExterno();
                    }
                }
            ],
            items:
                [
                    {width: 0, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtIdentificacion',
                        fieldLabel: 'Identificación',
                        maskRe: /[0-9]/,
                        value: '',
                        maxLength: 13,
                        enforceMaxLength: 13,
                        width: 210,
                        listeners:
                            {
                                focus: function(field, newValue, oldValue)
                                {
                                    Ext.getCmp('txtNombres').setValue('');
                                    Ext.getCmp('txtApellidos').setValue('');
                                },
                                specialkey: function(funct, event)
                                {
                                    if (event.getKey() == event.ENTER)
                                    {
                                        getPersonalExterno();
                                    }
                                }
                            }
                    },
                    {width: 30, border: false},
                    {
                        xtype: 'combobox',
                        id: 'cbxEmpresaExterna',
                        fieldLabel: 'Empresa Externa',
                        value: '',
                        width: 300,
                        store: storeEmpresasExternas,
                        labelAlign: 'left',
                        name: 'cbxOficina',
                        valueField: 'id_persona_empresa_rol',
                        displayField: 'razon_social',
                        triggerAction: 'all',
                        queryMode: 'local',
                        allowBlank: true,
                        editable: false,
                        matchFieldWidth: false,
                        listeners:
                            {
                                select: function()
                                {
                                    getPersonalExterno();
                                }
                            }
                    },
                    {width: 10, border: false},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Estado',
                        id: 'cbxEstado',
                        value: 'Activo',
                        width: 200,
                        editable: false,
                        labelAlign: 'right',
                        listeners:
                            {
                                select: function()
                                {
                                    getPersonalExterno();
                                }
                            },
                        store:
                            [
                                ['Todos', 'Todos'],
                                ['Activo', 'Activo'],
                                ['Eliminado', 'Eliminado']
                            ]
                    },
                    {width: 30, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtNombres',
                        fieldLabel: 'Nombres',
                        value: '',
                        maxLength: 300,
                        width: 300,
                        listeners:
                            {
                                focus: function(field, newValue, oldValue)
                                {
                                    Ext.getCmp('txtIdentificacion').setValue('');
                                    Ext.getCmp('txtApellidos').setValue('');
                                },
                                specialkey: function(funct, event)
                                {
                                    if (event.getKey() == event.ENTER)
                                    {
                                        getPersonalExterno();
                                    }
                                }
                            }
                    },
                    {width: 30, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtApellidos',
                        fieldLabel: 'Apellidos',
                        value: '',
                        maxLength: 300,
                        width: 300,
                        listeners:
                            {
                                focus: function(field, newValue, oldValue)
                                {
                                    Ext.getCmp('txtIdentificacion').setValue('');
                                    Ext.getCmp('txtNombres').setValue('');
                                },
                                specialkey: function(funct, event)
                                {
                                    if (event.getKey() == event.ENTER)
                                    {
                                        getPersonalExterno();
                                    }
                                }
                            }
                    },
                    {width: 30, border: false}
                ]
        });

});

function asignarMetaMasivaSeleccion(grid)
{
    var formPanel = Ext.create('Ext.form.Panel',
        {
            id: 'formAsignarMeta',
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
                                    xtype: 'numberfield',
                                    fieldLabel: 'Meta Bruta (Ventas) *',
                                    width: '600',
                                    name: 'strMetaBruta',
                                    id: 'strMetaBruta',
                                    colspan: 4,
                                    hideTrigger: true,
                                    listeners:
                                        {
                                            keyup:
                                                {
                                                    element: 'el',
                                                    fn: function(event, target)
                                                    {
                                                        getValorMetaActiva();
                                                    }
                                                }
                                        }
                                },
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Meta Activa (%) *',
                                    width: 200,
                                    id: 'strMetaActiva',
                                    name: 'strMetaActiva',
                                    colspan: 2,
                                    hideTrigger: true,
                                    style:
                                        {
                                            width: '10%'
                                        },
                                    listeners:
                                        {
                                            keyup:
                                                {
                                                    element: 'el',
                                                    fn: function(event, target)
                                                    {
                                                        getValorMetaActiva();
                                                    }
                                                }
                                        }
                                },
                                {
                                    xtype: 'displayfield',
                                    value: '=',
                                    width: 10,
                                    style:
                                        {
                                            marginRight: '5px',
                                            marginLeft: '5px'
                                        }
                                },
                                {
                                    xtype: 'displayfield',
                                    id: 'strMetaActivaValor',
                                    name: 'strMetaActivaValor',
                                    value: '0'
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
                            var form = Ext.getCmp('formAsignarMeta').getForm();

                            if (form.isValid())
                            {
                                var strMetaBruta = Ext.getCmp('strMetaBruta').getValue();
                                var strMetaActiva = Ext.getCmp('strMetaActiva').getValue();
                                if (strMetaBruta != '0' && strMetaBruta != null && strMetaBruta != ''
                                    && strMetaActiva != null && strMetaActiva != '' && strMetaActiva != '0')
                                {
                                    var strIdPersonasEmpresaRol = '';

                                    Ext.Msg.confirm(
                                        'Alerta',
                                        'Se agregará Meta Masiva a los empleados que han sido seleccionados.<br>¿Desea continuar?',
                                        function(btn)
                                        {
                                            if (btn == 'yes')
                                            {
                                                var xRowSelMod = grid.getSelectionModel().getSelection();

                                                for (var i = 0; i < xRowSelMod.length; i++)
                                                {
                                                    var RowSel = xRowSelMod[i];

                                                    strIdPersonasEmpresaRol = strIdPersonasEmpresaRol + RowSel.get('id_persona_empresa_rol');

                                                    if (i < (xRowSelMod.length - 1))
                                                    {
                                                        strIdPersonasEmpresaRol = strIdPersonasEmpresaRol + '|';
                                                    }
                                                }
                                                var arrayParametros = [];
                                                arrayParametros['intIdPersonalEmpresaRol'] = strIdPersonasEmpresaRol;
                                                arrayParametros['valor'] = strMetaBruta + '|' + strMetaActiva;
                                                arrayParametros['caracteristica'] = strCaracteristicaMetaBruta + '|' + strCaracteristicaMetaActiva;
                                                arrayParametros['accion'] = 'Guardar';
                                                arrayParametros['store'] = dataStorePersonalExterno;

                                                ajaxAsignarCaracteristica(arrayParametros);
                                            }
                                        }
                                    );
                                }
                                else
                                {
                                    Ext.Msg.show({
                                        title: 'Atenci\xf3n',
                                        msg: 'Todos los valores son requeridos',
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
            title: 'Asignar Meta',
            modal: true,
            width: 350,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}

function getPersonalExterno()
{
    dataStorePersonalExterno.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
    dataStorePersonalExterno.getProxy().extraParams.nombres = Ext.getCmp('txtNombres').value;
    dataStorePersonalExterno.getProxy().extraParams.apellidos = Ext.getCmp('txtApellidos').value;
    dataStorePersonalExterno.getProxy().extraParams.empresaExterna = Ext.getCmp('cbxEmpresaExterna').value;
    dataStorePersonalExterno.getProxy().extraParams.estado = Ext.getCmp('cbxEstado').value;
    dataStorePersonalExterno.currentPage = 1;
    dataStorePersonalExterno.load();
}
