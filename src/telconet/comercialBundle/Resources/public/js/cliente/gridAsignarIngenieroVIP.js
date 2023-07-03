Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox',
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    Ext.override(Ext.data.proxy.Ajax, {timeout: 900000});

    dataStoreComboIngenieros = Ext.create('Ext.data.Store',
        {
            autoLoad: true,
            proxy:
                {
                    type:    'ajax',
                    url:      url_combo_ingenieros_vip,
                    timeout:  600000,
                    reader:
                        {
                            type:          'json',
                            totalProperty: 'total',
                            root:          'registros'
                        }
                },
            fields:
                [
                    {name: 'id_per',       mapping: 'id_per',       type: 'string'},
                    {name: 'ingenieroVip', mapping: 'ingenieroVip', type: 'string'}
                ]
        });

    dataStoreComboIngenierosCiudad = Ext.create('Ext.data.Store',
        {
            autoLoad: true,
            proxy:
                {
                    type:    'ajax',
                    url:      url_combo_ciudades_vip,
                    timeout:  600000,
                    reader:
                        {
                            type:          'json',
                            totalProperty: 'total',
                            root:          'registros'
                        }
                },
            fields:
                [
                    {name: 'id_ciu', mapping: 'id_ciu', type: 'string'},
                    {name: 'ciudad', mapping: 'ciudad', type: 'string'}
                ]
        });

    var cbxIngenierosVIP = new Ext.form.ComboBox(
        {
            xtype:         'combobox',
            store:          dataStoreComboIngenieros,
            labelAlign:    'left',
            id:            'cbxIngenierosVIP',
            name:          'cbxIngenierosVIP',
            valueField:    'id_per',
            displayField:  'ingenieroVip',
            fieldLabel:    'Asignar Ingeniero VIP',
            labelWidth:    '13',
            style:         'white-space: nowrap',
            width:          390,
            triggerAction: 'all',
            selectOnFocus:  true,
            lastQuery:     '',
            mode:          'local',
            allowBlank:     true,
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
        });

    var cbxCiudadIngenierosVIP      = new Ext.form.ComboBox(
        {
            xtype:         'combobox',
            store:          dataStoreComboIngenierosCiudad,
            labelAlign:    'left',
            id:            'cbxCiudadIngenierosVIP',
            name:          'cbxCiudadIngenierosVIP',
            valueField:    'id_ciu',
            displayField:  'ciudad',
            fieldLabel:    'Ciudad',
            labelWidth:    '7',
            style:         'white-space: nowrap',
            width:          280,
            triggerAction: 'all',
            selectOnFocus:  true,
            lastQuery:     '',
            mode:          'local',
            allowBlank:     true,
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
        });

    var itemExtensionIngenierosVIP  = new Ext.form.TextField(
        {
                        xtype:      'textfield',
                        name:       'itemExtensionIngenierosVIP',
                        id:         'itemExtensionIngenierosVIP',
                        fieldLabel: 'Extensión',
                        valueField: 'extension',
                        labelWidth: '7',
                        width:      200,
                        maskRe:     /[0-9]/,
                        maxLength:  8,
                        allowBlank: true
        });

    var itemEditIdIngenierosVIP     = new Ext.form.TextField(
        {
                        xtype:     'textfield',
                        name:      'itemEditIdIngenierosVIP',
                        id:        'itemEditIdIngenierosVIP',
                        hideLabel: true,
                        hidden:    true
        });

    var itemEditIngenierosVIP       = new Ext.form.TextField(
        {
                        xtype:      'textfield',
                        name:       'itemEditIngenierosVIP',
                        id:         'itemEditIngenierosVIP',
                        fieldLabel: 'Ingeniero VIP',
                        valueField: 'ingeniero',
                        readonly:   true,
                        labelWidth: '8',
                        style:      'white-space: nowrap',
                        width:      360
        });

    var cbxEditCiudadIngenierosVIP  = new Ext.form.ComboBox(
        {
            xtype:         'combobox',
            store:          dataStoreComboIngenierosCiudad,
            labelAlign:    'left',
            id:            'cbxEditCiudadIngenierosVIP',
            name:          'cbxEditCiudadIngenierosVIP',
            valueField:    'id_ciu',
            displayField:  'ciudad',
            fieldLabel:    'Ciudad',
            labelWidth:    '7',
            style:         'white-space: nowrap',
            width:          260,
            triggerAction: 'all',
            selectOnFocus:  true,
            lastQuery:     '',
            mode:          'local',
            allowBlank:     true,
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
        });

    var itemEditExtensionIngenierosVIP = new Ext.form.TextField(
        {
                        xtype:      'textfield',
                        name:       'itemEditExtensionIngenierosVIP',
                        id:         'itemEditExtensionIngenierosVIP',
                        fieldLabel: 'Extensión',
                        valueField: 'extension',
                        labelWidth: '7',
                        width:      180,
                        maskRe:     /[0-9]/,
                        maxLength:  8,
                        allowBlank: true
        });

    dataStoreIngenierosVIP = new Ext.data.Store(
        {
            autoLoad:  true,
            pageSize:  200,
            total:    'total',
            proxy:
                {
                    type:    'ajax',
                    timeout:  600000,
                    url:      url_grid_ingenieros_vip,
                    reader:
                        {
                            type:          'json',
                            totalProperty: 'total',
                            root:          'registros'
                        }
                },
            fields:
                [
                    {name: 'id_per',       mapping: 'id_per',       type: 'string'},
                    {name: 'id_per_caract',mapping: 'id_per_caract',type: 'string'},
                    {name: 'id_ciudad',    mapping: 'id_ciudad',    type: 'string'},
                    {name: 'ingenieroVip', mapping: 'ingenieroVip', type: 'string'},
                    {name: 'ciudad',       mapping: 'ciudad',       type: 'string'},
                    {name: 'extension',    mapping: 'extension',    type: 'string'}
                ]
        });

    var permiso = $("#ROLE_151-3697");
    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
    var objPermiso      = $("#ROLE_151-7197");
    var boolPermisoEdit = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);

    gridIngenierosViP = Ext.create('Ext.grid.Panel',
        {
            width:        980,
            height:       400,
            id:          'gridIngenieros',
            title:       'Listado de Ingenieros VIP',
            store:        dataStoreIngenierosVIP,
            renderTo:     Ext.get('lista_ingenieros_vip'),
            multiSelect:  false,
            dockedItems:
                [
                    {
                        id:    'toolbarAsignarIngeniero',
                        xtype: 'toolbar',
                        dock:  'top',
                        align: '->',
                        items:
                            [
                                cbxIngenierosVIP,
                                cbxCiudadIngenierosVIP,
                                itemExtensionIngenierosVIP,
                                {xtype: 'tbfill'},
                                {
                                    iconCls:  'icon_add',
                                    text:     'Asignar',
                                    id:       'AsignarIngeniero',
                                    itemId:   'AsignarIngeniero',
                                    scope:     this,
                                    handler:   function()
                                    {
                                        
                                        if (boolPermiso)
                                        {
                                            asignarIngenieroVIP();
                                        }
                                        else
                                        {
                                            Ext.Msg.show(
                                                {
                                                    title: 'Error',
                                                    msg: 'No dispone del permiso necesario para realizar esta acci\xf3n.',
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            Ext.getCmp('AsignarIngeniero').disable();
                                        }
                                    }
                                }]
                    },
                    {
                        id:     'toolbarEditarIngeniero',
                        xtype:  'toolbar',
                        dock:   'top',
                        align:  '->',
                        hidden: true,
                        items:
                            [
                                itemEditIdIngenierosVIP,
                                itemEditIngenierosVIP,
                                cbxEditCiudadIngenierosVIP,
                                itemEditExtensionIngenierosVIP,
                                {xtype: 'tbfill'},
                                {
                                    iconCls:  'icon_edit',
                                    text:     'Actualizar',
                                    id:       'EditarIngeniero',
                                    itemId:   'EditarIngeniero',
                                    scope:     this,
                                    handler:   function()
                                    {
                                        if (boolPermisoEdit)
                                        {
                                            editarIngenieroVIP();
                                        }
                                        else
                                        {
                                            Ext.Msg.show({
                                                    title:   'Error',
                                                    msg:     'No dispone del permiso necesario para realizar esta acci\xf3n.',
                                                    buttons: Ext.Msg.OK,
                                                    icon:    Ext.MessageBox.ERROR
                                                });
                                            Ext.getCmp('EditarIngeniero').disable();
                                        }
                                    }
                                },
                                {
                                    iconCls:  'icon_limpiar',
                                    text:     'Cancelar',
                                    id:       'CancelarIngeniero',
                                    itemId:   'CancelarIngeniero',
                                    scope:     this,
                                    handler:   function()
                                    {
                                        Ext.getCmp('itemEditIdIngenierosVIP').setValue('');
                                        Ext.getCmp('toolbarAsignarIngeniero').setVisible(true);
                                        Ext.getCmp('toolbarEditarIngeniero').setVisible(false);
                                        Ext.getCmp('itemEditIngenierosVIP').setReadOnly(true);
                                        Ext.getCmp('itemEditIngenierosVIP').setValue('');
                                        Ext.getCmp('cbxEditCiudadIngenierosVIP').setValue('');
                                        Ext.getCmp('cbxEditCiudadIngenierosVIP').setRawValue("");
                                        Ext.getCmp('itemEditExtensionIngenierosVIP').setValue('');
                                    }
                                }
                            ]
                    }
                ],
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store:        dataStoreIngenierosVIP,
                    displayInfo:  true,
                    displayMsg:  'Mostrando Ingenieros VIP: {0} - {1} of {2}',
                    emptyMsg:    'No hay datos para mostrar'
                }),
            viewConfig:
                {
                    emptyText: '<br><center><b>No hay datos para mostrar'
                },
            columns:
                [
                    new Ext.grid.RowNumberer(),
                    {
                        dataIndex: 'id_per_caract',
                        hidden:     true
                    },
                    {
                        dataIndex: 'id_per',
                        hidden:     true
                    },
                    {
                        dataIndex: 'id_ciudad',
                        hidden:     true
                    },
                    {
                        dataIndex: 'ingenieroVip',
                        header:    'Ingeniero',
                        width:      440
                    },
                    {
                        dataIndex: 'ciudad',
                        header:    'Ciudad',
                        width:      280
                    },
                    {
                        dataIndex: 'extension',
                        header:    'Extensión',
                        width:      120
                    },
                    {
                        xtype:  'actioncolumn',
                        header: 'Acciones',
                        width:   110,
                        items:
                            [
                                {
                                    tooltip: 'Editar Ingeniero VIP',
                                    getClass: function()
                                    {
                                        strEditarIngenieroVIP = 'button-grid-invisible';
                                        var permiso     = $("#ROLE_151-7197");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        if (boolPermiso)
                                        {
                                            strEditarIngenieroVIP = 'button-grid-edit';
                                        }
                                        return strEditarIngenieroVIP;
                                    },
                                    handler: function(grid, rowIndex)
                                    {
                                        Ext.getCmp('cbxIngenierosVIP').setValue("");
                                        Ext.getCmp('cbxIngenierosVIP').setRawValue("");
                                        Ext.getCmp('cbxCiudadIngenierosVIP').setValue("");
                                        Ext.getCmp('cbxCiudadIngenierosVIP').setRawValue("");
                                        Ext.getCmp('itemExtensionIngenierosVIP').setValue("");

                                        var rec = dataStoreIngenierosVIP.getAt(rowIndex);
                                        Ext.getCmp('toolbarAsignarIngeniero').setVisible(false);
                                        Ext.getCmp('toolbarEditarIngeniero').setVisible(true);
                                        Ext.getCmp('itemEditIngenierosVIP').setReadOnly(true);
                                        Ext.getCmp('itemEditIdIngenierosVIP').setValue(rec.get('id_per_caract'));
                                        Ext.getCmp('itemEditIngenierosVIP').setValue(rec.get('ingenieroVip'));
                                        Ext.getCmp('cbxEditCiudadIngenierosVIP').setValue(rec.get('id_ciudad'));
                                        Ext.getCmp('itemEditExtensionIngenierosVIP').setValue(rec.get('extension'));
                                    }
                                },
                                {
                                    tooltip: 'Eliminar Ingeniero VIP',
                                    getClass: function()
                                    {
                                        strEliminarIngenieroVIP = 'button-grid-invisible';
                                        
                                        var permiso     = $("#ROLE_151-3737");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        
                                        if (boolPermiso)
                                        {
                                            strEliminarIngenieroVIP = 'button-grid-delete';
                                        }
                                        
                                        return strEliminarIngenieroVIP;
                                    },
                                    handler: function(grid, rowIndex)
                                    {
                                        var rec = dataStoreIngenierosVIP.getAt(rowIndex);
                                        
                                        Ext.Msg.confirm('Alerta', 'Eliminar al Ingeniero VIP: ' + rec.get('ingenieroVip') + '.<br>¿Desea continuar?', 
                                        function(btn)
                                        {
                                            if (btn === 'yes')
                                            {
                                                Ext.getCmp('itemEditIdIngenierosVIP').setValue('');
                                                Ext.getCmp('toolbarAsignarIngeniero').setVisible(true);
                                                Ext.getCmp('toolbarEditarIngeniero').setVisible(false);
                                                Ext.getCmp('itemEditIngenierosVIP').setReadOnly(true);
                                                Ext.getCmp('itemEditIngenierosVIP').setValue('');
                                                Ext.getCmp('cbxEditCiudadIngenierosVIP').setValue('');
                                                Ext.getCmp('cbxEditCiudadIngenierosVIP').setRawValue("");
                                                Ext.getCmp('itemEditExtensionIngenierosVIP').setValue('');
                                                connEliminandoDatos.request(
                                                    {
                                                        url:    url_ajax_eliminar_ingeniero,
                                                        method: 'post',
                                                        params:
                                                            {
                                                                ingeniero: rec.get('id_per_caract')
                                                            },
                                                        success: function(response)
                                                        {
                                                            var respuesta = response.responseText;

                                                            if (respuesta == 'OK')
                                                            {
                                                                Ext.Msg.show({
                                                                    title:   'Informaci\xf3n',
                                                                    msg:     'Ingeniero VIP eliminado correctamente.',
                                                                    buttons:  Ext.Msg.OK,
                                                                    icon:     Ext.MessageBox.INFO
                                                                });
                                                                
                                                                dataStoreComboIngenieros.loadPage(1);
                                                                dataStoreIngenierosVIP.loadPage(1);
                                                                
                                                                Ext.getCmp('cbxIngenierosVIP').value = "";
                                                                Ext.getCmp('cbxIngenierosVIP').setRawValue("");
                                                            }
                                                            else
                                                            {
                                                                Ext.Msg.show(
                                                                    {
                                                                        title: 'Error',
                                                                        msg: respuesta,
                                                                        buttons: Ext.Msg.OK,
                                                                        icon: Ext.MessageBox.ERROR
                                                                    });
                                                            }
                                                        },
                                                        failure: function(result)
                                                        {
                                                            Ext.Msg.show(
                                                                {
                                                                    title: 'Error',
                                                                    msg: 'Error: ' + result.statusText,
                                                                    buttons: Ext.Msg.OK,
                                                                    icon: Ext.MessageBox.ERROR
                                                                });
                                                        }
                                                    });
                                            }
                                        });
                                    }
                                }
                            ]
                    }
                ]
        });

    if (!boolPermiso)
    {
        Ext.getCmp('toolbarAsignarIngeniero').setVisible(false);
        Ext.getCmp('AsignarIngeniero').disable();
    }
    if (!boolPermisoEdit)
    {
        Ext.getCmp('toolbarEditarIngeniero').setVisible(false);
        Ext.getCmp('EditarIngeniero').disable();
    }
    
    function asignarIngenieroVIP()
    {
        if (cbxIngenierosVIP.value && cbxCiudadIngenierosVIP.value)
        {
            Ext.Msg.confirm('Alerta', 'Se asignar\xe1 el ingeniero VIP: ' + Ext.getCmp('cbxIngenierosVIP').getRawValue()+ '.<br>¿Desea continuar?', 
            function(btn)
            {
                if (btn == 'yes')
                {

                    connGrabandoDatos.request(
                        {
                            url:     url_ajax_asignar_ingeniero,
                            method: 'post',
                            params:
                                {
                                    ingeniero: cbxIngenierosVIP.value,
                                    ciudad:    cbxCiudadIngenierosVIP.value,
                                    extension: itemExtensionIngenierosVIP.value
                                },
                            success: function(response)
                            {
                                var respuesta = response.responseText;

                                if (respuesta == 'OK')
                                {
                                    Ext.Msg.show({
                                        title:   'Informaci\xf3n',
                                        msg:     'Ingeniero VIP asignado correctamente.',
                                        buttons:  Ext.Msg.OK,
                                        icon:     Ext.MessageBox.INFO
                                    });

                                    dataStoreComboIngenieros.loadPage(1);
                                    dataStoreIngenierosVIP.loadPage(1);

                                    Ext.getCmp('cbxIngenierosVIP').setValue("");
                                    Ext.getCmp('cbxIngenierosVIP').setRawValue("");
                                    Ext.getCmp('cbxCiudadIngenierosVIP').setValue("");
                                    Ext.getCmp('cbxCiudadIngenierosVIP').setRawValue("");
                                    Ext.getCmp('itemExtensionIngenierosVIP').setValue("");
                                }
                                else
                                {
                                    Ext.Msg.show(
                                        {
                                            title:   'Error',
                                            msg:      respuesta,
                                            buttons:  Ext.Msg.OK,
                                            icon:     Ext.MessageBox.ERROR
                                        });
                                }
                            },
                            failure: function(result)
                            {
                                Ext.Msg.show(
                                    {
                                        title:   'Error',
                                        msg:     'Error: ' + result.statusText,
                                        buttons:  Ext.Msg.OK,
                                        icon:     Ext.MessageBox.ERROR
                                    });
                            }
                        });
                }
            });
        }
        else if (cbxIngenierosVIP.value)
        {
            Ext.Msg.show(
                {
                    title:   'Error',
                    msg:     'No ha seleccionado la ciudad del Ingeniero VIP',
                    buttons:  Ext.Msg.OK,
                    icon:     Ext.MessageBox.ERROR
                });
            
        }
        else if (cbxCiudadIngenierosVIP.value)
        {
            Ext.Msg.show(
                {
                    title:   'Error',
                    msg:     'No ha seleccionado el Ingeniero VIP',
                    buttons:  Ext.Msg.OK,
                    icon:     Ext.MessageBox.ERROR
                });
            
        }
        else
        {
            Ext.Msg.show(
                {
                    title:   'Error',
                    msg:     'No ha seleccionado el Ingeniero VIP y la Ciudad',
                    buttons:  Ext.Msg.OK,
                    icon:     Ext.MessageBox.ERROR
                });
        }
    }
    
    function editarIngenieroVIP()
    {
        if (cbxEditCiudadIngenierosVIP.value)
        {
            Ext.Msg.confirm('Alerta', 'Se actualizar\xe1 el ingeniero VIP: ' + Ext.getCmp('itemEditIngenierosVIP').getValue()+ '.<br>¿Desea continuar?', 
            function(btn)
            {
                if (btn == 'yes')
                {

                    connGrabandoDatos.request(
                        {
                            url:    url_ajax_editar_ingeniero,
                            method: 'post',
                            params:
                                {
                                    ingeniero: itemEditIdIngenierosVIP.value,
                                    ciudad:    cbxEditCiudadIngenierosVIP.value,
                                    extension: itemEditExtensionIngenierosVIP.value
                                },
                            success: function(response)
                            {
                                var respuesta = response.responseText;

                                if (respuesta == 'OK')
                                {
                                    Ext.Msg.show({
                                        title:   'Informaci\xf3n',
                                        msg:     'Ingeniero VIP actualizado correctamente.',
                                        buttons:  Ext.Msg.OK,
                                        icon:     Ext.MessageBox.INFO
                                    });

                                    dataStoreComboIngenieros.loadPage(1);
                                    dataStoreIngenierosVIP.loadPage(1);

                                    Ext.getCmp('toolbarAsignarIngeniero').setVisible(true);
                                    Ext.getCmp('toolbarEditarIngeniero').setVisible(false);
                                    Ext.getCmp('itemEditIngenierosVIP').setReadOnly(true);
                                    Ext.getCmp('itemEditIdIngenierosVIP').setValue('');
                                    Ext.getCmp('itemEditIngenierosVIP').setValue('');
                                    Ext.getCmp('cbxEditCiudadIngenierosVIP').setValue('');
                                    Ext.getCmp('cbxEditCiudadIngenierosVIP').setRawValue("");
                                    Ext.getCmp('itemEditExtensionIngenierosVIP').setValue('');
                                }
                                else
                                {
                                    Ext.Msg.show(
                                        {
                                            title:   'Error',
                                            msg:      respuesta,
                                            buttons:  Ext.Msg.OK,
                                            icon:     Ext.MessageBox.ERROR
                                        });
                                }
                            },
                            failure: function(result)
                            {
                                Ext.Msg.show(
                                    {
                                        title:   'Error',
                                        msg:     'Error: ' + result.statusText,
                                        buttons:  Ext.Msg.OK,
                                        icon:     Ext.MessageBox.ERROR
                                    });
                            }
                        });
                }
            });
        }
        else
        {
            Ext.Msg.show(
                {
                    title:   'Error',
                    msg:     'No ha seleccionado la ciudad del Ingeniero VIP',
                    buttons:  Ext.Msg.OK,
                    icon:     Ext.MessageBox.ERROR
                });
        }
    }
});

var connGrabandoDatos = new Ext.data.Connection(
    {
        listeners:
            {
                'beforerequest':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.show(
                                {
                                    msg:          'Asignando al ingeniero VIP, Por favor espere!!',
                                    progressText: 'Saving...',
                                    width:         300,
                                    wait:          true,
                                    waitConfig:
                                        {
                                            interval: 200
                                        }
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }
    });
    
var connEliminandoDatos = new Ext.data.Connection(
    {
        listeners:
            {
                'beforerequest':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.show(
                                {
                                    msg:          'Eliminando al ingeniero VIP, Por favor espere!!',
                                    progressText: 'Saving...',
                                    width:         300,
                                    wait:          true,
                                    waitConfig:
                                        {
                                            interval: 200
                                        }
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }
    });