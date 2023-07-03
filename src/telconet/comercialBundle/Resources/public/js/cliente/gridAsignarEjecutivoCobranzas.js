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

    var intSizeGrid = 0;

    var dataStoreEjecutivos = Ext.create('Ext.data.Store',
        {
            autoLoad: true,
            proxy:
                {
                    type:    'ajax',
                    url:      url_ejecutivos_cobranza,
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
                    {name: 'login',             mapping: 'login',             type: 'string'},
                    {name: 'ejecutivoCobranza', mapping: 'ejecutivoCobranza', type: 'string'}
                ]
        });

    var cbxEjecutivoCobranza = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    {
                        xtype:         'combobox',
                        store:          dataStoreEjecutivos,
                        labelAlign:    'left',
                        id:            'cbxEjecutivoCobranza',
                        name:          'cbxEjecutivoCobranza',
                        valueField:    'login',
                        displayField:  'ejecutivoCobranza',
                        fieldLabel:    'Ejecutivo Cobranza',
                        style:         'white-space: nowrap',
                        width:          425,
                        triggerAction: 'all',
                        selectOnFocus:  true,
                        lastQuery:     '',
                        mode:          'local',
                        allowBlank:     true,
                        labelWidth:    '11',
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
        });        

    Ext.define('ListaDetalleModel',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'idPunto',      mapping: 'idPunto',      type: 'int'},
                    {name: 'login',        mapping: 'login',        type: 'string'},
                    {name: 'usrCobranzas', mapping: 'usrCobranzas', type: 'string'},
                    {name: 'direccion',    mapping: 'direccion',    type: 'string'},
                    {name: 'nombre',       mapping: 'nombre',       type: 'string'},
                    {name: 'estado',       mapping: 'estado',       type: 'string'}
                ]
        });

    storePuntosCliente = new Ext.data.Store(
        {
            autoLoad:  true,
            pageSize:  200,
            model:    'ListaDetalleModel',
            total:    'total',
            proxy:
                {
                    type:    'ajax',
                    timeout:  600000,
                    url:      url_grid_puntos,
                    reader:
                        {
                            type:          'json',
                            totalProperty: 'total',
                            root:          'registros'
                        }
                },
            listeners:
                {
                    beforeload: function(storePuntosCliente)
                    {
                        storePuntosCliente.getProxy().extraParams.login    = Ext.getCmp('txtLogin').value;
                        storePuntosCliente.getProxy().extraParams.estado   = Ext.getCmp('cbxEstado').value;
                        storePuntosCliente.getProxy().extraParams.asignado = Ext.getCmp('cbxAsignado').value;
                    }
                }
        });

    var smPuntos = new Ext.selection.CheckboxModel(
        {
            checkOnly:          true,
            showHeaderCheckbox: false,
            listeners:
                {
                    selectionchange: function(model, selection)
                    {
                        intSizeGrid = selection.length;

                        gridPuntos.down('#AsignarEjecutivo').setDisabled(intSizeGrid <= 0);

                        intSizeGrid = intSizeGrid < 0 ? 0 : intSizeGrid;

                        Ext.getCmp('labelSeleccion').setText(intSizeGrid + ' seleccionados');
                    }
                }
        });

    gridPuntos = Ext.create('Ext.grid.Panel',
        {
            width:        980,
            height:       370,
            id:          'gridPuntos',
            collapsible:  false,
            title:       'Listado de Puntos',
            selModel:     smPuntos,
            plugins:     [{ptype: 'pagingselectpersist'}],
            store:        storePuntosCliente,
            renderTo:     Ext.get('lista_puntos'),
            multiSelect:  false,
            listeners:
                {
                    sortchange: function()
                    {
                        gridPuntos.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                    }
                },
            dockedItems:
                [
                    {
                        xtype: 'toolbar',
                        dock:  'top',
                        align: '->',
                        items:
                            [
                                cbxEjecutivoCobranza,
                                {
                                    id:    'labelSeleccion',
                                    cls:   'greenTextGrid',
                                    text:  '0 seleccionados',
                                    scope: this
                                },
                                {xtype: 'tbfill'},
                                {
                                    iconCls:  'icon_add',
                                    text:     'Asignar',
                                    id:       'AsignarEjecutivo',
                                    itemId:   'AsignarEjecutivo',
                                    disabled:  true,
                                    scope:     this,
                                    handler:   function()
                                    {
                                        asignarEjecutivosCobranza();
                                    }
                                }
                            ]
                    },
                    {
                        xtype: 'toolbar',
                        dock:  'top',
                        align: '->',
                        items:
                            [
                                {
                                    iconCls: 'icon_add',
                                    text:    'Seleccionar todos',
                                    itemId:  'select',
                                    scope:    this,
                                    handler: function()
                                    {
                                        gridPuntos.getSelectionModel().selectAll(true);
                                    }
                                },
                                {
                                    iconCls: 'icon_limpiar',
                                    text:    'Desmarcar seleccionados',
                                    itemId:  'clear',
                                    scope:    this,
                                    handler:  function()
                                    {
                                        gridPuntos.getSelectionModel().deselectAll(true);
                                    }
                                }
                            ]
                    }
                ],
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store:        storePuntosCliente,
                    displayInfo:  true,
                    displayMsg:  'Mostrando puntos {0} - {1} of {2}',
                    emptyMsg:    'No hay datos para mostrar'
                }),
            viewConfig:
                {
                    emptyText: '<br><center><b>No hay datos para mostrar',
                    getRowClass: function(record, index)
                    {
                        var ejecutivo = record.get('usrCobranzas');

                        if (ejecutivo == 'No-Asignado')
                        {
                            return 'redTextGrid';
                        }
                        else
                        {
                            return 'blackTextGrid';
                        }
                    }
                },
            columns:
                [
                    new Ext.grid.RowNumberer(),
                    {
                        dataIndex: 'idPunto',
                        hidden:     true
                    },
                    {
                        dataIndex: 'login',
                        header:    'Login',
                        width:      145
                    },
                    {
                        dataIndex: 'usrCobranzas',
                        header:    'Ejecutivo de Cobranza',
                        width:      250
                    },
                    {
                        dataIndex: 'direccion',
                        header:    'Direcci\xf3n',
                        width:      280
                    },
                    {
                        dataIndex: 'nombre',
                        header:    'Nombre',
                        width:      170
                    },
                    {
                        header:    'Estado',
                        dataIndex: 'estado',
                        align:     'right',
                        width: 70
                    }
                ]
        });

    function asignarEjecutivosCobranza()
    {
        var arrayPuntos = [];

        if (smPuntos.getSelection().length > 0)
        {
            if (Ext.getCmp('cbxEjecutivoCobranza').value)
            {
                Ext.Msg.confirm('Alerta', 'El Ejecutivo de Cobranza se asignar\xe1 a los puntos seleccionados. Desea continuar?', function(btn)
                {
                    if (btn == 'yes')
                    {
                        for (var i = 0; i < smPuntos.getSelection().length; ++i)
                        {
                            arrayPuntos.push(smPuntos.getSelection()[i].data.idPunto);
                        }

                        connGrabandoDatos.request(
                            {
                                url:     url_ajax_asignar_ejecutivo,
                                method: 'post',
                                params:
                                    {
                                        puntos:    arrayPuntos.toString(),
                                        ejecutivo: Ext.getCmp('cbxEjecutivoCobranza').value   
                                    },
                                success: function(response)
                                {
                                    var respuesta = response.responseText;

                                    if (respuesta == 'OK')
                                    {
                                        Ext.Msg.show({
                                            title:   'Informaci\xf3n',
                                            msg:     'Ejecutivo asignado correctamente.',
                                            buttons:  Ext.Msg.OK,
                                            icon:     Ext.MessageBox.INFORMATION
                                        });
                                        storePuntosCliente.loadPage(1); // Realiza el load y establece el númer de página
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
                        msg:     'No ha seleccionado el Ejecutivo de Cobranzas',
                        buttons:  Ext.Msg.OK,
                        icon:     Ext.MessageBox.ERROR
                    });
            }
        }
        else
        {
            alert('Seleccione por lo menos un registro de la lista');
        }
    }

    /* ******************************************* */
    /* FILTROS DE BÚSQUEDA */
    /* ******************************************* */
    Ext.create('Ext.panel.Panel',
        {
            renderTo:    'filtro_puntos',
            width:        980,
            bodyPadding:  7,
            border:       false,
            buttonAlign: 'center',
            layout:
                {
                    type:  'hbox',
                    align: 'stretch'
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            title:       'Criterios de búsqueda',
            collapsible:  true,
            collapsed:    false,
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: 'icon_search',
                        handler: function()
                        {
                            storePuntosCliente.loadPage(1);
                        }
                    },
                    {
                        text:    'Limpiar',
                        iconCls: 'icon_limpiar',
                        handler: function()
                        {
                            Ext.getCmp('txtLogin').value    = "";
                            Ext.getCmp('cbxEstado').value   = "Activo,Pendiente,Cancelado";
                            Ext.getCmp('cbxAsignado').value = "Todos";

                            Ext.getCmp('txtLogin').setRawValue("");
                            Ext.getCmp('cbxEstado').setRawValue("Todos");
                            Ext.getCmp('cbxAsignado').setRawValue("Todos");

                            storePuntosCliente.loadPage(1);
                        }
                    }
                ],
            items:
                [
                    {
                        width:  '5%',
                        border:  false
                    },
                    {
                        xtype:      'textfield',
                        id:         'txtLogin',
                        fieldLabel: 'Login',
                        labelWidth: '5',
                        value:      '',
                        width:      '100'
                    },
                    {width: '5%', border: false},
                    {
                        xtype:      'combobox',
                        fieldLabel: 'Estado',
                        labelWidth: '5',
                        id:         'cbxEstado',
                        value:      'Activo,Pendiente,Cancelado',
                        editable:    false,
                        width:      '100',
                        store:
                            [
                                ['Activo,Pendiente,Cancelado', 'Todos'],
                                ['Activo',           'Activo'],
                                ['Pendiente',        'Pendiente'],
                                ['Cancelado',        'Cancelado']
                            ]
                    },
                    {width: '5%', border: false},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Ejecutivo Asignado',
                        labelWidth: '12',
                        style: 'white-space:nowrap',
                        id: 'cbxAsignado',
                        value: 'Todos',
                        editable: false,
                        store:
                            [
                                ['Todos', 'Todos'],
                                ['SI', 'SI'],
                                ['NO', 'NO']
                            ],
                        width: '100'
                    }
                ]
        });

});

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