Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox',
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function ()
{
    Ext.tip.QuickTipManager.init();

    Ext.override(Ext.data.proxy.Ajax, {timeout: 900000});

    var intSizeGrid = 0;

    storeProcesosMasivos = new Ext.data.Store
        ({
            total: 'total',
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    method: 'post',
                    url: strUrlGetDetallesParametros,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams: {
                        nombreParametro: 'PROCESOS_MASIVOS_TELCOHOME'
                    }
                },
            fields:
                [
                    {name: 'valor2', mapping: 'valor2'},
                    {name: 'valor3', mapping: 'valor3'},
                ]
        });
        
    var comboProcesosMasivos = new Ext.form.ComboBox({
        id: 'cbxProcesosMasivos',
        name: 'cbxProcesosMasivos',
        queryMode: 'local',
        width: 220,
        emptyText: 'Seleccione la acción',
        allowBlank: false,
        labelWidth: '5',
        fieldLabel: 'Acción',
        store: storeProcesosMasivos,
        displayField: 'valor2',
        valueField: 'valor3',
        listeners: {
            select: function ()
            {
                storePuntosCliente.proxy.extraParams = {estadoServicio: Ext.getCmp('cbxProcesosMasivos').value};
                storePuntosCliente.loadPage(1);
            }
        }
    });

    var cbxProcesosMasivos = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    comboProcesosMasivos
                ]
        });

    Ext.define('ListaDetalleModel',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'idPunto', mapping: 'idPunto', type: 'int'},
                    {name: 'idServicio', mapping: 'idServicio', type: 'int'},
                    {name: 'login', mapping: 'login', type: 'string'},
                    {name: 'loginFact', mapping: 'loginFact', type: 'string'},
                    {name: 'direccion', mapping: 'direccion', type: 'string'},
                    {name: 'nombre', mapping: 'nombre', type: 'string'},
                    {name: 'estado', mapping: 'estado', type: 'string'},
                    {name: 'idProcesoMasivoDet', mapping: 'idProcesoMasivoDet', type: 'string'},
                    {name: 'estadoProcesoMasivoDet', mapping: 'estadoProcesoMasivoDet', type: 'string'}
                ]
        });

    storePuntosCliente = new Ext.data.Store(
        {
            autoLoad: true,
            model: 'ListaDetalleModel',
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    limitParam: undefined,
                    pageParam: undefined,
                    startParam: undefined,
                    noCache: false,
                    url: strUrlGridPuntosTelcoHome,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'registros'
                        }
                },
            listeners:
                {
                    beforeload: function (storePuntosCliente)
                    {
                        storePuntosCliente.getProxy().extraParams.estadoServicio = Ext.getCmp('cbxProcesosMasivos').value;
                        storePuntosCliente.getProxy().extraParams.login = Ext.getCmp('txtLogin').value;
                        storePuntosCliente.getProxy().extraParams.loginFact = Ext.getCmp('txtLoginFact').value;

                    }
                }
        });

    smPuntos = new Ext.selection.CheckboxModel(
        {
            checkOnly: true,
            showHeaderCheckbox: false,
            listeners:
                {
                    beforeselect: function() {
                        if (!Ext.getCmp('cbxProcesosMasivos').value) {
                            Ext.Msg.show(
                            {
                                title: 'Alerta',
                                msg: 'Por favor primero elija la Acción que desea ejecutar',
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.INFO
                            });
                            return false;
                        }
                    },
                    selectionchange: function (model, selection)
                    {
                        intSizeGrid = selection.length;
                        intSizeGrid = intSizeGrid < 0 ? 0 : intSizeGrid;
                        if (Ext.getCmp('cbxProcesosMasivos').value && intSizeGrid > 0)
                        {
                            boolBloquearEjecucion = false;
                        }
                        else
                        {
                            boolBloquearEjecucion = true;
                        }
                        gridPuntos.down('#ejecutarProceso').setDisabled(boolBloquearEjecucion);
                        Ext.getCmp('labelSeleccion').setText(intSizeGrid + ' seleccionados');
                    }
                }
        });

    gridPuntos = Ext.create('Ext.grid.Panel',
        {
            width: 1000,
            height: 370,
            id: 'gridPuntos',
            collapsible: false,
            title: 'Listado de Puntos',
            selModel: smPuntos,
            plugins: [{ptype: 'pagingselectpersist'}],
            store: storePuntosCliente,
            renderTo: Ext.get('lista_puntos'),
            multiSelect: false,
            listeners:
                {
                    sortchange: function ()
                    {
                        gridPuntos.getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                    },
                    viewready: function (grid)
                    {
                        var view = grid.view;

                        grid.mon(view,
                        {
                            uievent: function (type, view, cell, recordIndex, cellIndex, e)
                            {
                                grid.cellIndex   = cellIndex;
                                grid.recordIndex = recordIndex;
                            }
                        });

                        grid.tip = Ext.create('Ext.tip.ToolTip',
                        {
                            target: view.el,
                            delegate: '.x-grid-cell',
                            trackMouse: true,
                            autoHide: false,
                            renderTo: Ext.getBody(),
                            listeners:
                            {
                                beforeshow: function(tip)
                                {
                                    if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                    {
                                        header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                        if( header.dataIndex != null )
                                        {
                                            var trigger         = tip.triggerElement,
                                                parent          = tip.triggerElement.parentElement,
                                                columnTitle     = view.getHeaderByCell(trigger).text,
                                                columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                            if( view.getRecord(parent).get(columnDataIndex) != null )
                                            {
                                                var columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                                if (columnText)
                                                {
                                                    tip.update(columnText);
                                                }
                                                else
                                                {
                                                    return false;
                                                }
                                            }
                                            else
                                            {
                                                return false;
                                            }
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }     
                                }
                            }
                        });

                        grid.tip.on('show', function()
                        {
                            var timeout;

                            grid.tip.getEl().on('mouseout', function()
                            {
                                timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                            });

                            grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                            Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                            Ext.get(view.el).on('mouseout', function()
                            {
                                timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                            });
                        });
                    }
                },
            dockedItems:
                [
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items:
                            [
                                cbxProcesosMasivos,
                                {
                                    id: 'labelSeleccion',
                                    cls: 'greenTextGrid',
                                    text: '0 seleccionados',
                                    scope: this
                                },
                                {xtype: 'tbfill'},
                                {
                                    iconCls: 'icon_add',
                                    text: 'Ejecutar',
                                    id: 'ejecutarProceso',
                                    itemId: 'ejecutarProceso',
                                    disabled: true,
                                    scope: this,
                                    handler: function ()
                                    {
                                        generarCorteReactivacionMasiva();
                                    }
                                }
                            ]
                    },
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items:
                            [
                                {
                                    iconCls: 'icon_add',
                                    text: 'Seleccionar todos',
                                    itemId: 'select',
                                    scope: this,
                                    handler: function ()
                                    {
                                        gridPuntos.getSelectionModel().selectAll(true);
                                    }
                                },
                                {
                                    iconCls: 'icon_limpiar',
                                    text: 'Desmarcar seleccionados',
                                    itemId: 'clear',
                                    scope: this,
                                    handler: function ()
                                    {
                                        gridPuntos.getSelectionModel().deselectAll(true);
                                    }
                                }
                            ]
                    }
                ],
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store: storePuntosCliente,
                    displayInfo: true,
                    displayMsg: 'Mostrando puntos {0} - {1} of {2}',
                    emptyMsg: 'No hay datos para mostrar'
                }),
            viewConfig:
                {
                    emptyText: '<br><center><b>No hay datos para mostrar',
                    enableTextSelection: true,
                    getRowClass: function(record, index)
                    {
                        var idProcesoMasivoDet = record.get('idProcesoMasivoDet');

                        if (idProcesoMasivoDet > 0)
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
                        hidden: true
                    },
                    {
                        dataIndex: 'idServicio',
                        hidden: true
                    },
                    {
                        dataIndex: 'login',
                        header: 'Login',
                        width: 180
                    },
                    {
                        dataIndex: 'loginFact',
                        header: 'Padre Fact.',
                        width: 150
                    },
                    {
                        dataIndex: 'direccion',
                        header: 'Direcci\xf3n',
                        width: 220
                    },
                    {
                        dataIndex: 'nombre',
                        header: 'Nombre del Punto',
                        width: 240
                    },
                    {
                        header: 'Estado del Servicio',
                        dataIndex: 'estado',
                        align: 'right',
                        width: 110
                    },
                    {
                        header: '# de Proceso',
                        dataIndex: 'idProcesoMasivoDet',
                        align: 'right',
                        width: 90,
                        allowBlank: false,
                    },
                    {
                        header: 'Estado del Proceso',
                        dataIndex: 'estadoProcesoMasivoDet',
                        align: 'right',
                        width: 110
                    }
                ]
        });

    /* ******************************************* */
    /* FILTROS DE BÚSQUEDA */
    /* ******************************************* */
    Ext.create('Ext.panel.Panel',
        {
            renderTo: 'filtro_puntos',
            width: 980,
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 5,
                align: 'left'
            },
            bodyStyle:
                {
                    background: '#fff'
                },
            title: 'Criterios de búsqueda',
            collapsible: true,
            collapsed: false,
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: 'icon_search',
                        handler: function ()
                        {
                            storePuntosCliente.loadPage(1);
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: 'icon_limpiar',
                        handler: function ()
                        {
                            Ext.getCmp('txtLogin').value = "";
                            Ext.getCmp('txtLogin').setRawValue("");
                            Ext.getCmp('txtLoginFact').value = "";
                            Ext.getCmp('txtLoginFact').setRawValue("");
                            storePuntosCliente.loadPage(1);
                        }
                    }
                ],
            items:
                [
                    {
                        width: 110,
                        border: false
                    },
                    {
                        xtype: 'textfield',
                        id: 'txtLogin',
                        fieldLabel: 'Login',
                        labelWidth: 40,
                        value: ''
                    },
                    {
                        width: 200,
                        border: false
                    },
                    {
                        xtype: 'textfield',
                        id: 'txtLoginFact',
                        fieldLabel: 'Padre de Facturación',
                        labelWidth: 125,
                        value: ''
                    },
                    {
                        width: 50,
                        border: false
                    },
                ]
        });

});

function generarCorteReactivacionMasiva()
{
    var arrayServiciosPuntos = [];
    var contadorEstadosIncorrectos = 0;
    var contadorServiciosEnProcesos = 0;
    if (smPuntos.getSelection().length > 0)
    {
        if (Ext.getCmp('cbxProcesosMasivos').value)
        {
            for (var i = 0; i < smPuntos.getSelection().length; ++i)
            {
                if(smPuntos.getSelection()[i].data.estado != Ext.getCmp('cbxProcesosMasivos').value)
                {
                    contadorEstadosIncorrectos = contadorEstadosIncorrectos + 1;
                }
                if(smPuntos.getSelection()[i].data.idProcesoMasivoDet > 0)
                {
                    contadorServiciosEnProcesos = contadorServiciosEnProcesos + 1;
                }
                arrayServiciosPuntos.push(smPuntos.getSelection()[i].data.idServicio);
            }
            
            if(contadorEstadosIncorrectos > 0)
            {
                Ext.Msg.show(
                {
                    title: 'Error',
                    msg: 'Por lo menos uno de los servicios TelcoHome en los puntos seleccionados se encuentra en un estado diferente a '
                         +Ext.getCmp('cbxProcesosMasivos').value,
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });

            }
            else if(contadorServiciosEnProcesos > 0)
            {
                Ext.Msg.show(
                {
                    title: 'Error',
                    msg: 'Por lo menos uno de los servicios TelcoHome en los puntos seleccionados ya se encuentra en un proceso ',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
            else
            {
                Ext.Msg.confirm('Alerta', 'Se procederá a <b>'+Ext.getCmp('cbxProcesosMasivos').getRawValue()+' '
                                          +smPuntos.getSelection().length+' servicios TelcoHome</b> de los puntos seleccionados. '
                                          +'Desea continuar?', function (btn)
                {
                    if (btn == 'yes')
                    {
                        var connGrabandoDatos = new Ext.data.Connection(
                        {
                            listeners:
                                {
                                    'beforerequest':
                                        {
                                            fn: function (con, opt)
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
                                            fn: function (con, res, opt)
                                            {
                                                Ext.MessageBox.hide();
                                            },
                                            scope: this
                                        },
                                    'requestexception':
                                        {
                                            fn: function (con, res, opt)
                                            {
                                                Ext.MessageBox.hide();
                                            },
                                            scope: this
                                        }
                                }
                        });
                        
                        connGrabandoDatos.request(
                        {
                            url: strUrlGenerarProcesoMasivoTelcoHome,
                            method: 'post',
                            params:
                                {
                                    totalServicios: smPuntos.getSelection().length,
                                    servicios: arrayServiciosPuntos.toString(),
                                    estadoActualServicio: Ext.getCmp('cbxProcesosMasivos').value
                                },
                            success: function (response)
                            {
                                var respuesta = response.responseText;

                                if (respuesta == 'OK')
                                {
                                    Ext.Msg.show({
                                        title: 'Informaci\xf3n',
                                        msg: 'Proceso creado correctamente. Se está ejecutando el script, favor espere el correo de reporte general',
                                        buttons: Ext.Msg.OK,
                                        icon: Ext.MessageBox.INFORMATION
                                    });
                                    storePuntosCliente.loadPage(1);
                                } else
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
                            failure: function (result)
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
        else
        {
            Ext.Msg.show(
                {
                    title: 'Error',
                    msg: 'No ha seleccionado la acción a ejecutar',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
        }
    }
    else
    {
        Ext.Msg.show(
                {
                    title: 'Error',
                    msg: 'Seleccione por lo menos un registro de la lista',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
    }
}
    