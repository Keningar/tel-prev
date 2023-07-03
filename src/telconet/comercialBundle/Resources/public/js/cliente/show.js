Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox' 
]);

Ext.QuickTips.init();

var cambio_prepago = false;
var cambio_iva     = false;
        
Ext.onReady(function()
{
    new Ext.Panel(
        {
            id: 'paneltoolBarVip',
            renderTo: 'toolBarVip',
            baseCls:  'x-plain', // Quita la línea inferior del marco 
            dockedItems:
                [
                    {
                        xtype:   'toolbar',
                        dock:    'top',
                        baseCls: 'x-plain', // Quita el color de fondo y el marco del componente
                        items:
                            [
                                {
                                    xtype: 'button',
                                    id: 'btnHistoralCliente',
                                    cls: 'icon_cliente_log',
                                    tooltip: '<b>Historial Cliente',
                                    handler: function()
                                    {
                                        verHistorialCliente();
                                    }
                                },
                                {
                                    xtype: 'button',
                                    id:    'bntActualizarClienteVIP',
                                    cls:   'icon_vip',
                                    handler: function()
                                    {
                                        if (esVip == 'No')
                                        {
                                            estadoCliente = 'VIP';
                                        }
                                        else
                                        {
                                            estadoCliente = 'Normal<br>*** ALERTA: Se eliminarán los ingenieros VIP asociados a este cliente ***';
                                        }

                                        Ext.Msg.confirm('Alerta', 'Se actualizará al Cliente como ' + estadoCliente +
                                            '<br> ¿Desea continuar?', function(btn)
                                            {
                                                if (btn === 'yes')
                                                {
                                                    connEsperaAccion.request
                                                        (
                                                            {
                                                                url:     urlDefinirClienteVIPNormal,
                                                                method: 'POST',
                                                                timeout: 60000,
                                                                params:
                                                                    {
                                                                        idPer: idPer
                                                                    },
                                                                success: function(response)
                                                                {
                                                                    var resp = response.responseText;
                                                                    if (resp == 'OK')
                                                                    {
                                                                        window.location.reload();
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.show(
                                                                            {
                                                                                title: 'Error',
                                                                                msg: resp,
                                                                                buttons: Ext.Msg.OK,
                                                                                icon: Ext.MessageBox.ERROR
                                                                            });
                                                                    }
                                                                },
                                                                failure: function(result)
                                                                {
                                                                    Ext.MessageBox.hide();
                                                                    Ext.Msg.alert('Error', result.responseText);
                                                                }
                                                            }
                                                        );
                                                }
                                            });
                                    }
                                },
                                {
                                    xtype: 'button',
                                    id: 'bntDatosFacturacion',
                                    cls: 'icon_data_fact_edit',
                                    tooltip: '<b>Editar Datos de Facturación',
                                    handler: function()
                                    {
                                        editarDatosFacturacion();
                                    }
                                },
                                {
                                    xtype: 'button',
                                    id: 'bntFechaFinContrato',
                                    cls: 'icon_fecha_fin_contrato',
                                    tooltip: '<b>Editar Fecha Fin Contrato',
                                    handler: function()
                                    {
                                        editarFechaFinContrato();
                                    }
                                }
                            ]
                    }
                ]
        });
    
    // Solo TN muestra los botones de para actualizar a VIP, y Editar datos de facturación.
    if (prefijoEmpresa == 'TN')
    {
        if (esVip == 'Sí' || esVipTecnico == 'Sí')
        {
            new Ext.Panel(
                {
                    renderTo: 'toolIngVip',
                    baseCls: 'x-plain', // Quita la línea inferior del marco 
                    dockedItems:
                        [
                            {
                                xtype: 'toolbar',
                                dock: 'top',
                                baseCls: 'x-plain', // Quita el color de fondo y el marco del componente
                                items:
                                    [
                                        {
                                            xtype: 'button',
                                            id: 'bntShowIngVIP',
                                            cls: 'icon_ver_ing_vip',
                                            tooltip: '<b>Ver Ingenieros VIP',
                                            handler: function()
                                            {
                                                verIngenierosVIP();
                                            }
                                        },
                                        {
                                            xtype: 'button',
                                            id: 'bntHistorialClienteVIP',
                                            cls: 'icon_vip_log',
                                            tooltip: '<b>Ver Historial VIP',
                                            handler: function()
                                            {
                                                verHistorialVIP();
                                            }
                                        }
                                    ]
                            }
                        ]
                });
        }

        if (esVip == 'No')
        {
            Ext.getCmp('bntActualizarClienteVIP').setTooltip("<b>Definir Como Cliente VIP");
        }
        else
        {
            Ext.getCmp('bntActualizarClienteVIP').setTooltip("<b>Definir Como Cliente Normal");
        }

        var permiso1              = $("#ROLE_151-3717"); // ACTUALIZAR CLIENTE VIP
        var boolPermisoActualizar = (typeof permiso1 === 'undefined') ? false : (permiso1.val() == 1 ? true : false);

        var permiso2             = $("#ROLE_151-3738"); // SHOW ASIGNAR CLIENTE VIP
        var boolPermisoShowHisto = (typeof permiso2 === 'undefined') ? false : (permiso2.val() == 1 ? true : false);

        var permiso3             = $("#ROLE_151-4138"); // ACTUALIZAR DATOS FACTURACION
        var boolDatosFacturacion = (typeof permiso3 === 'undefined') ? false : (permiso3.val() == 1 ? true : false);

        var permiso4             = $("#ROLE_151-4677"); // ACTUALIZAR FECHA FIN DE CONTRATO
        var boolFechaFinContrato = (typeof permiso4 === 'undefined') ? false : (permiso4.val() == 1 ? true : false);

        var permiso5             = $("#ROLE_151-7177"); // ACTUALIZAR LA MARCA VIP TECNICO
        var boolUpdateVipTecnico = (typeof permiso5 === 'undefined') ? false : (permiso5.val() == 1 ? true : false);

        if (!boolPermisoActualizar)
        {
            Ext.getCmp('bntActualizarClienteVIP').hide();
        }

        if (esVip == 'Sí')
        {
            if (!boolPermisoShowHisto)
            {
                Ext.getCmp('bntHistorialClienteVIP').hide();
            }
        }
        
        if (!boolDatosFacturacion)
        {
            Ext.getCmp('bntDatosFacturacion').hide();
        }
        if (!boolFechaFinContrato || strEstadoContrato!="Activo")
        {
            Ext.getCmp('bntFechaFinContrato').hide();
        }
        
        if (boolUpdateVipTecnico)
        {
            if (esVipTecnico == 'Sí')
            {
                strTooltipVipTecnico = '<b>Inactivar';
            }
            else
            {
                strTooltipVipTecnico = '<b>Activar';
            }
            new Ext.Panel(
            {
                renderTo: 'toolVipTecnico',
                baseCls: 'x-plain',
                dockedItems:
                    [
                        {
                            xtype: 'toolbar',
                            dock: 'top',
                            baseCls: 'x-plain',
                            items:
                                [
                                    {
                                        xtype:   'button',
                                        id:      'bntMarcaVipTecnico',
                                        cls:     'icon_cambiar_marca_vip_tecnico',
                                        tooltip: strTooltipVipTecnico,
                                        handler: function()
                                        {
                                            cambiarMarcaVipTecnico();
                                        }
                                    },
                                ]
                        }
                    ]
            });
        }
    }
    else
    {
        Ext.getCmp('bntActualizarClienteVIP').hide();
        Ext.getCmp('bntDatosFacturacion').hide();
        Ext.getCmp('bntFechaFinContrato').hide();
    }
    

    function verIngenierosVIP()
    {
        dataStoreIngenierosVIP = new Ext.data.Store(
            {
                autoLoad: true,
                total:   'total',
                proxy:
                    {
                        type: 'ajax',
                        timeout: 600000,
                        url: url_grid_ingenieros_vip,
                        reader:
                            {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'registros'
                            }
                    },
                fields:
                    [
                        {name: 'id_per', mapping: 'id_per', type: 'string'},
                        {name: 'ciudad', mapping: 'ciudad', type: 'string'},
                        {name: 'extension', mapping: 'extension', type: 'string'},
                        {name: 'ingenieroVip', mapping: 'ingenieroVip', type: 'string'}
                    ]
            });

        gridIngenierosViP = Ext.create('Ext.grid.Panel',
            {
                id:    'gridIngenieros',
                store:  dataStoreIngenierosVIP,
                width:  450,
                height: 200,
                collapsible: false,
                multiSelect: false,
                viewConfig:
                    {
                        emptyText: '<br><center><b>No hay datos para mostrar'
                    },
                layout: 'fit',
                region: 'center',
                buttons:
                    [
                        {
                            text: 'Cerrar',
                            handler: function()
                            {
                                win1.destroy();
                            }
                        }
                    ],
                columns:
                    [
                        new Ext.grid.RowNumberer(),
                        {
                            dataIndex: 'id_per',
                            header: 'Login',
                            hidden: true
                        },
                        {
                            dataIndex: 'ingenieroVip',
                            header: 'Ingeniero',
                            width: 250
                        },
                        {
                            dataIndex: 'ciudad',
                            header: 'Ciudad',
                            width: 100
                        },
                        {
                            dataIndex: 'extension',
                            header: 'Extensión',
                            width: 80
                        }
                    ]
            });

        Ext.create('Ext.form.Panel',
            {
                id: 'formShowIngVIP',
                bodyPadding: 2,
                waitMsgTarget: true,
                items:
                    [
                        {
                            xtype: 'fieldset',
                            layout:
                                {
                                    type: 'table',
                                    columns: 4,
                                    align: 'left'
                                },
                            items:
                                [
                                    gridIngenierosViP
                                ]
                        }
                    ]
            });

        win1 = Ext.create('Ext.window.Window',
            {
                title: 'Ingenieros VIP',
                modal: true,
                width: 450,
                closable: true,
                layout: 'fit',
                items: [gridIngenierosViP]
            }).show();
    }
    
    function verHistorialVIP()
    {
        dataStoreHistorialVIP = new Ext.data.Store(
            {
                autoLoad: true,
                total: 'total',
                proxy:
                    {
                        type: 'ajax',
                        timeout: 600000,
                        url: urlGridHistorialVIP,
                        reader:
                            {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'registros'
                            }
                    },
                fields:
                    [
                        {name: 'accion',  mapping: 'accion',  type: 'string'},
                        {name: 'valor',   mapping: 'valor',   type: 'string'},
                        {name: 'usuario', mapping: 'usuario', type: 'string'},
                        {name: 'fecha',   mapping: 'fecha',   type: 'string'}
                    ]
            });

        gridHistorialVIP = Ext.create('Ext.grid.Panel',
            {
                id: 'gridHistorialVIP',
                store: dataStoreHistorialVIP,
                width: 790,
                height: 300,
                collapsible: false,
                multiSelect: true,
                viewConfig:
                    {
                        emptyText: '<br><center><b>No hay datos para mostrar',
                        forceFit: true
                    },
                layout: 'fit',
                region: 'center',
                buttons:
                    [
                        {
                            text: 'Cerrar',
                            handler: function()
                            {
                                win2.destroy();
                            }
                        }
                    ],
                columns:
                    [
                        {
                            dataIndex: 'accion',
                            header: 'Acci\xf3n',
                            width: 100
                        },
                        {
                            dataIndex: 'valor',
                            header: 'Valor',
                            width: 450
                        },
                        {
                            dataIndex: 'usuario',
                            header: 'Usuario',
                            width: 80
                        },
                        {
                            dataIndex: 'fecha',
                            header: 'Fecha',
                            width: 150
                        }
                    ]
            });

        Ext.create('Ext.form.Panel',
            {
                id: 'formHistorialVIP',
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
                                    gridHistorialVIP
                                ]
                        }
                    ]
            });

        win2 = Ext.create('Ext.window.Window',
            {
                title: 'Historial VIP',
                modal: true,
                width: 800,
                closable: true,
                layout: 'fit',
                items: [gridHistorialVIP]
            }).show();
    }
    
    function verHistorialCliente()
    {
        dataStoreHistorialCliente = new Ext.data.Store(
            {
                autoLoad: true,
                total: 'total',
                proxy:
                    {
                        type: 'ajax',
                        timeout: 600000,
                        url: urlGridHistorialCliente,
                        reader:
                            {
                                type: 'json',
                                totalProperty: 'total',
                                root: 'registros'
                            }
                    },
                fields:
                    [
                        {name: 'accion', mapping: 'accion', type: 'string'},
                        {name: 'usuario', mapping: 'usuario', type: 'string'},
                        {name: 'fecha', mapping: 'fecha', type: 'string'}
                    ]
            });

        gridHistorialCliente = Ext.create('Ext.grid.Panel',
        {
            id: 'gridHistorialCliente',
            store: dataStoreHistorialCliente,
            width: 690,
            height: 300,
            collapsible: false,
            multiSelect: true,
            viewConfig: 
            {
                emptyText: '<br><center><b>No hay datos para mostrar',
                forceFit: true,
                stripeRows: true,
                enableTextSelection: true
            },
            listeners: 
            {
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
            layout: 'fit',
            region: 'center',
            buttons:
            [
                {
                    text: 'Cerrar',
                    handler: function()
                    {
                        win4.destroy();
                    }
                }
            ],
            columns:
            [
                {
                    dataIndex: 'accion',
                    header: 'Acci\xf3n',
                    width: 537
                },
                {
                    dataIndex: 'usuario',
                    header: 'Usuario',
                    width: 100
                },
                {
                    dataIndex: 'fecha',
                    header: 'Fecha',
                    width: 150
                }
            ]
        });

        Ext.create('Ext.form.Panel',
            {
                id: 'formHistorialCliente',
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
                                    columns: 3,
                                    align: 'left'
                                },
                            items:
                                [
                                    gridHistorialCliente
                                ]
                        }
                    ]
            });

        win4 = Ext.create('Ext.window.Window',
            {
                title: 'Historial Cliente',
                modal: true,
                width: 800,
                closable: true,
                layout: 'fit',
                items: [gridHistorialCliente]
            }).show();
    }
    
    function editarDatosFacturacion()
    {
        cambio_prepago               = false;
        cambio_iva                   = false;
        cambio_contribucionSolidaria = false;
        
        esPrepago               = $("#tdEsPrepago").attr('name');
        pagaIva                 = $("#tdPagaIva").attr('name');
        contribucionSolidaria   = $("#tdContribucionSolidaria").attr('name');

        var formEditarFacturacion = Ext.create('Ext.form.Panel',
            {
                id: 'formEditar',
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults:
                    {
                        labelAlign: 'left',
                        labelWidth: 125,
                        msgTarget: 'side'
                    },
                buttons:
                    [
                        {
                            text: 'Guardar',
                            handler: function()
                            {
                                if (cambio_prepago || cambio_iva || cambio_contribucionSolidaria)
                                {
                                    var msg = '';
                                    
                                    if (cambio_iva)
                                    {
                                        var SiNo = (Ext.getCmp('cbxPagaIva').getValue() == 'S' ? 'Sí' : 'No');
                                        msg += '<br>Es Paga IVA: de ' + (pagaIva == 'S' ? 'Sí' : 'No') + ' a ' + SiNo;
                                    }

                                    if (cambio_prepago)
                                    {
                                        var strTipoFacturacion = '';
                                        
                                        if(Ext.getCmp('cbxEsPrepago').getValue() == 'S')
                                        {
                                            strTipoFacturacion = 'Prepago';
                                        }
                                        else if(Ext.getCmp('cbxEsPrepago').getValue() == 'N')
                                        {
                                            strTipoFacturacion = 'Postpago';
                                        }
                                        else
                                        {
                                            strTipoFacturacion = 'Postpago Manual';
                                        }                                        
                                        msg += '<br>Tipo Facturación: de ' + (esPrepago === 'S' ? 'Prepago' : 
                                                                              esPrepago === 'N' ? 'Postpago': 
                                                                                                 'Postpago Manual') + ' a ' + strTipoFacturacion;
                                    }
                                    
                                    if (cambio_contribucionSolidaria)
                                    {
                                        var SiNo = Ext.getCmp('cbxContribucionSolidaria').getValue() == 'S' ? 'Sí' : 'No';
                                        msg += '<br>Contribución Solidaria : de ' + (contribucionSolidaria == 'S' ? 'Sí' : 'No') + ' a ' + SiNo;
                                    }

                                    Ext.Msg.confirm('Alerta', 'Se actualizarán los datos de facturacion del Cliente: ' + msg +
                                        '<br> ¿Desea continuar?', function(btn)
                                        {
                                            if (btn === 'yes')
                                            {
                                                connEsperaAccion2.request
                                                    (
                                                        {
                                                            url: urlActualizarDatosFacturacion,
                                                            method: 'POST',
                                                            timeout: 60000,
                                                            params:
                                                                {
                                                                    idPer:                     idPer,
                                                                    pagaIva:                   Ext.getCmp('cbxPagaIva').getValue(),
                                                                    esPrepago:                 Ext.getCmp('cbxEsPrepago').getValue(),
                                                                    contribucionSolidaria:     Ext.getCmp('cbxContribucionSolidaria').getValue(),
                                                                    boolPagaIva:               cambio_iva,
                                                                    boolEsPrepago:             cambio_prepago,
                                                                    boolContribucionSolidaria: cambio_contribucionSolidaria
                                                                },
                                                            success: function(response)
                                                            {
                                                                var obj = Ext.decode(response.responseText);

                                                                if (response.statusText == 'OK')
                                                                {
                                                                    if (cambio_prepago)
                                                                    {
                                                                        $("#tdEsPrepago").html(obj.esPrepago === 'S' ? 'Sí' : 
                                                                                               obj.esPrepago === 'N' ? 'No':'Postpago Manual');
                                                                        $("#tdEsPrepago").attr('name', obj.esPrepago);
                                                                        esPrepago = $("#tdEsPrepago").attr('name');
                                                                    }
                                                                    if (cambio_iva)
                                                                    {
                                                                        $("#tdPagaIva").html(obj.pagaIva == 'S' ? 'Sí' : 'No');
                                                                        $("#tdPagaIva").attr('name', obj.pagaIva);
                                                                        pagaIva = $("#tdPagaIva").attr('name');
                                                                    }
                                                                    if (cambio_contribucionSolidaria)
                                                                    {
                                                                        $("#tdContribucionSolidaria").html(obj.contribucionSolidaria == 'S' 
                                                                                                           ? 'Sí' : 'No');
                                                                        $("#tdContribucionSolidaria").attr('name', obj.contribucionSolidaria);
                                                                        contribucionSolidaria = $("#tdContribucionSolidaria").attr('name');
                                                                    }
                                                                    
                                                                    Ext.Msg.show(
                                                                        {
                                                                            title: 'Información',
                                                                            msg: 'Se actualizaron los datos exitosamente',
                                                                            buttons: Ext.Msg.OK,
                                                                            icon: Ext.MessageBox.INFO
                                                                        });
                                                                    win3.destroy();
                                                                }
                                                                else
                                                                {
                                                                    Ext.Msg.show(
                                                                        {
                                                                            title: 'Error',
                                                                            msg: obj.error,
                                                                            buttons: Ext.Msg.OK,
                                                                            icon: Ext.MessageBox.ERROR
                                                                        });
                                                                }
                                                            },
                                                            failure: function(result)
                                                            {
                                                                Ext.MessageBox.hide();
                                                                Ext.Msg.alert('Error', result.responseText);
                                                            }
                                                        }
                                                    );
                                            }
                                        });
                                }
                            }
                        },
                        {
                            text: 'Cerrar',
                            handler: function()
                            {
                                win3.destroy();
                            }
                        }
                    ],
                items:
                    [
                        {
                            xtype: 'fieldset',
                            autoHeight: 400,
                            labelWidth: 70,
                            width: 320,
                            items:
                                [
                                    {
                                        fieldLabel: 'Paga IVA',
                                        type: 'combobox',
                                        id: 'cbxPagaIva',
                                        labelStyle: 'font-weight:bolder;',
                                        width: 200,
                                        xtype: 'combo',
                                        hiddenName: 'rating',
                                        store: new Ext.data.SimpleStore(
                                            {
                                                data:
                                                    [
                                                        ['S', 'Sí'],
                                                        ['N', 'No']
                                                    ],
                                                fields: ['value', 'text']
                                            }),
                                        valueField: 'value',
                                        displayField: 'text',
                                        value: pagaIva,
                                        triggerAction: 'all',
                                        editable: false,
                                        listeners:
                                            {
                                                change:
                                                    {
                                                        fn: function(that, e, eOpts)
                                                        {
                                                            cambio_iva = pagaIva != this.value;
                                                        }
                                                    }

                                            }
                                    },
                                    {
                                        fieldLabel: 'Contribución Solidaria',
                                        type: 'combobox',
                                        id: 'cbxContribucionSolidaria',
                                        labelStyle: 'font-weight:bolder;',
                                        width: 200,
                                        xtype: 'combo',
                                        hiddenName: 'rating',
                                        store: new Ext.data.SimpleStore(
                                            {
                                                data:
                                                    [
                                                        ['S', 'Sí'],
                                                        ['N', 'No']
                                                    ],
                                                fields: ['value', 'text']
                                            }),
                                        valueField: 'value',
                                        displayField: 'text',
                                        value: contribucionSolidaria,
                                        triggerAction: 'all',
                                        editable: false,
                                        listeners:
                                            {
                                                change:
                                                    {
                                                        fn: function(that, e, eOpts)
                                                        {
                                                            cambio_contribucionSolidaria = contribucionSolidaria != this.value;
                                                        }
                                                    }

                                            }
                                    },                                    
                                    {
                                        fieldLabel: 'Tipo Facturación',
                                        type: 'combobox',
                                        labelStyle: 'font-weight:bolder;',
                                        id: 'cbxEsPrepago',
                                        width: 250,
                                        xtype: 'combo',
                                        hiddenName: 'rating',
                                        store: new Ext.data.SimpleStore(
                                            {
                                                data:
                                                    [
                                                        ['S', 'Prepago'],
                                                        ['N', 'Postpago'],
                                                        ['M', 'Postpago Manual']
                                                    ],
                                                fields: ['value', 'text']
                                            }),
                                        valueField: 'value',
                                        displayField: 'text',
                                        value: esPrepago,
                                        triggerAction: 'all',
                                        editable: false,
                                        listeners:
                                            {
                                                change:
                                                    {
                                                        fn: function(that, e, eOpts)
                                                        {
                                                            cambio_prepago = esPrepago != this.value;
                                                        }
                                                    }

                                            }
                                    }
                                ]
                        }
                    ]
            });

        win3 = Ext.create('Ext.window.Window',
            {
                title: 'Actualizar Datos de Facturación',
                modal: true,
                width: 340,
                closable: true,
                layout: 'fit',
                items: [formEditarFacturacion]
            }).show();
    }
    
    function editarFechaFinContrato()
    {        
        var today = new Date();
        var dd    = today.getDate();
        var mm    = today.getMonth() + 1; 

        var yyyy = today.getFullYear();
        if (dd < 10)
        {
            dd = '0' + dd;
        }
        if (mm < 10) 
        {
            mm = '0' + mm;
        }
        var today = yyyy + '-' + mm + '-' + dd;
        
        var textFechaFinContrato = new Ext.form.DateField({
              id: 'fechaFinContrato',
              fieldLabel: 'Fecha Fin Contrato',
              labelAlign : 'left',
              xtype: 'datefield',
              format: 'Y-m-d',   
              allowBlank: false,
              width:325,                   
            });        
        
        var formEditarFechaFinContrato = Ext.create('Ext.form.Panel', {
        title: '',
        renderTo: Ext.getBody(),
        bodyPadding: 5,
        width: 450,
        items:[
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:
                {
                    width: 400
                },
                items: [                
                textFechaFinContrato,                           
              ],
            }
        ],
        buttons:
        [
            {
                text: 'Guardar',
                name: 'guardarBtn',
                disabled: false,
                handler: function() 
                {
                    var form1 = this.up('form').getForm();
                    if (form1.isValid())
                    {                       
                        if(formatDate(Ext.getCmp('fechaFinContrato').getValue())>= today)
                        {
                            Ext.MessageBox.show({
                                msg: 'Guardando datos...',
                                title: 'Procesando',
                                progressText: 'Mensaje',
                                progress: true,
                                closable: false,
                                width: 300,
                                wait: true,
                                waitConfig: {interval: 200}
                            });

                            Ext.Ajax.request({
                            url: urlActualizarFeFinContrato,
                            method: 'POST',
                            params: 
                            {
                                idPer: idPer,
                                fechaFinContrato: Ext.getCmp('fechaFinContrato').getValue()
                            },
                            success: function(response, request) 
                            {
                                Ext.MessageBox.hide();
                                var obj = Ext.decode(response.responseText);
                                if (obj.success) 
                                {                                    
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Información',
                                        msg: 'Guardado correctamente.',
                                        width: 300,
                                        icon: Ext.MessageBox.INFO,
                                        buttons: Ext.Msg.OK
                                    });
                                    form1.reset();                                    
                                    $("#tdFeFinContrato").html(obj.strFechaFinContrato);
                                    $("#tdFeFinContrato").attr('name',obj.strFechaFinContrato);                                   
                                    ventanaEditarFechaFinContrato.destroy();
                                } 
                                else 
                                {
                                    Ext.MessageBox.show({
                                        modal: true,
                                        title: 'Error',
                                        msg: 'Error al guardar.',
                                        width: 300,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }

                            },
                            failure: function() 
                            {
                                Ext.MessageBox.show({
                                    modal: true,
                                    title: 'Error',
                                    msg: 'Error al guardar.',
                                    width: 300,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                            });

                        }
                        else 
                        {
                            Ext.MessageBox.show({
                                modal: true,
                                title: 'Error',
                                msg: 'La fecha escogida no puede ser menor a la fecha actual',
                                width: 300,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    }
                    else 
                    {
                        Ext.MessageBox.show({
                            modal: true,
                            title: 'Error',
                            msg: 'Ingrese el campo solicitado',
                            width: 300,
                            icon: Ext.MessageBox.ERROR
                        });
                    }
                }
            },
            {
                text: 'Cancelar',
                handler: function() {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }
        ]
       });
       
       ventanaEditarFechaFinContrato = Ext.widget('window', {
        title: 'Editar Fecha Finalizacion del Contrato',
        closeAction: 'hide',
        closable: true,
        width: 400,
        height: 150,
        minHeight: 150,
        autoScroll: true,
        layout: 'fit',
        resizable: true,
        modal: true,
        items: formEditarFechaFinContrato
       });

       ventanaEditarFechaFinContrato.show();
    }
    function formatDate(date)
    {
        var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day   = '' + d.getDate(),
        year  = d.getFullYear();

        if (month.length < 2)
            month = '0' + month;
        if (day.length < 2)
            day = '0' + day;

        return [year, month, day].join('-');
    }

    function cambiarMarcaVipTecnico()
    {
        var booleanMaskVip = new Ext.LoadMask(Ext.getBody(), {msg:"Actualizando..."});
        booleanMaskVip.show();
        $.ajax({
            url:     url_cambiar_marca_vip_tecnico,
            method:  'GET',
            timeout: 60000,
            params:
            {
                idPer: idPer
            },
            success: function(response)
            {
                booleanMaskVip.hide();
                if (response.status == 'OK')
                {
                    $('#labelVipTecnico').html(response.result);
                    if (response.result == 'Sí')
                    {
                        Ext.getCmp('bntMarcaVipTecnico').setTooltip("<b>Inactivar");
                    }
                    else
                    {
                        Ext.getCmp('bntMarcaVipTecnico').setTooltip("<b>Activar");
                    }
                    Ext.Msg.show(
                        {
                            title:   'Información',
                            msg:      response.mensaje,
                            buttons:  Ext.Msg.OK,
                            icon:     Ext.MessageBox.INFO
                        });
                }
                else
                {
                    Ext.Msg.show(
                        {
                            title:   'Error',
                            msg:      response.mensaje,
                            buttons:  Ext.Msg.OK,
                            icon:     Ext.MessageBox.ERROR
                        });
                }
            },
            error: function () {
                booleanMaskVip.hide();
                Ext.Msg.show(
                    {
                        title:   'Error',
                        msg:     'En la lógica del negocio, por favor notificar a Sistemas.',
                        buttons: Ext.Msg.OK,
                        icon:    Ext.MessageBox.ERROR
                    });
            }
        });
    }
});

var connEsperaAccion = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Actualizando Cliente como ' + (esVip == 'No' ? 'VIP' : 'Normal') + ', Por favor espere!!',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
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

var connEsperaAccion2 = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function()
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Actualizando Datos de Facturación del Cliente.',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
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

    /**
     * Documentación para la función 'getValidacionRazonSocial'.
     *
     * Función que muestra mensaje de validación.
     *
     * @param float floatSaldoPendiente - Saldo pendiente del cliente
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-11-2020
     */
    function getValidacionRazonSocial(floatSaldoPendiente)
    {
        var strMensaje = 'Cliente en sesión tiene una deuda de: $'+floatSaldoPendiente+' <br>'+
                         'Por lo cual no es permitido realizar el cambio de razón social.';
        Ext.MessageBox.show({
            modal : true,
            title : 'Error',
            msg   : strMensaje,
            width : 420,
            icon  : Ext.MessageBox.ERROR,
            buttons : Ext.Msg.OK
        });
    }


    function verRecomendacionesEquifax(tipoIdentificacion,identificacionCliente, tipoTributario) { 
  
    var formPanel = Ext.create('Ext.form.Panel', {
        title: 'Recomendaciones Equifax',
        header:false,          
        width: 1000,           
        height: 500,   
        bodyPadding: 10, 
        border: false,
        margin: 5,
        padding: 0,
        autoScroll :true,
        layout: {
            type: 'table',
            columns: 2,
            pack: 'center',
            align: 'middle',
            autoScroll :true,
            tableAttrs: {
                style: {
                    width: '100%', 
                }
            },
            tdAttrs: {
                align: 'center',
                valign: 'top', 
                autoScroll :true,
            }
        },
        items: [
            {
                
            xtype:'fieldset',
            id: 'idIngresoRecomendadoTarjetaCredito', 
            name: 'idIngresoRecomendadoTarjetaCredito', 
            columnWidth: 0.5,
            title: 'TARJETAS DE CRÉDITO RECOMENDADAS',
            collapsible: false,
            defaultType: 'textfield',
            defaults: {anchor: '100%'},
            layout: 'anchor',
            autoHeight:true,
            autoScroll :true,
            width: 400,
            margin: 3,
            padding: 3,
            items :[]
            },
            {
            xtype:'fieldset',
            id: 'idIngresoRecomendadoIngresos', 
            name: 'idIngresoRecomendadoIngresos', 
            columnWidth: 0.5,
            title: 'MÁS DETALLES',
            collapsible: false,
            defaultType: 'textfield',
            defaults: {anchor: '100%'},
            layout: 'anchor',
            autoHeight:true,
            autoScroll :true,
            width: 600,
            margin: 3,
            padding: 3,
            items :[]
            } 
        ]
    });


    
    var win = Ext.create('Ext.window.Window', {
        title: 'Recomendaciones Equifax',
        modal: true,
        width: 1200,           
        height: 500,   
        closable: true,
        layout: 'fit',
        items: [formPanel],
        buttons: [
                    { 
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    },

                    { 
                        text: 'Verificar',
                        handler: function(){
                       
                            createTabPanelRecomendacion(tipoIdentificacion,identificacionCliente, tipoTributario,  win ) ; 
                        }
                    }
               ]

    }).show();
 


  
   
    }
    
    function createTabPanelRecomendacion(tipoIdentificacion,identificacionCliente, tipoTributario ,  win  ) { 
        
        win.getEl().mask('Obteniendo recomendaciones de Equifax...');
  
       $.ajax ({
            type: 'POST',
            data: 'identificacion=' +identificacionCliente + '&tipoIdentificacion=' +  tipoIdentificacion + '&tipoTributario=' + tipoTributario ,
            url: url_verificar_recomendaciones,
            success:  function (response) {
              let objData = response.objData;
              let strMensaje = response.strMensaje;
              let strStatus = response.strStatus;
              if (strStatus == 'OK') {
     
                let  arrayRecomendacionesTarjetaCredito =  objData.arrayRecomendacionesTarjetaCredito ||[];
                let  arrayRecomendacionesIngresos       =   objData.arrayRecomendacionesIngresos ||[];
    
                let mapRecomendacionesIngresos = []; 
                for (let index = 0; index < arrayRecomendacionesIngresos .length; index++) {
                    const e= arrayRecomendacionesIngresos [index];
                    let n= index+1;
                    mapRecomendacionesIngresos.push({
                        fieldLabel:  e.titulo,
                        name: 'fieldIngresosRecomendada'+n, 
                        margin: 5,
                        readOnly: true,
                        value: e.descripcion,
                    });                            
                }     
                Ext.getCmp('idIngresoRecomendadoIngresos').removeAll(true);
                Ext.getCmp('idIngresoRecomendadoIngresos').add(mapRecomendacionesIngresos);
    
                let mapRecomendacionesTarjetaCredito = []; 
                for (let index = 0; index < arrayRecomendacionesTarjetaCredito .length; index++) {
                    const e= arrayRecomendacionesTarjetaCredito[index];
                    let n= index+1;
                    mapRecomendacionesTarjetaCredito.push({
                        fieldLabel:  '',//e.titulo,
                        name: 'fieldTarjetaRecomendada'+n, 
                        margin: 5,
                        readOnly: true,
                        value: e.titulo,//e.descripcion,
                    });                            
                }     
                Ext.getCmp('idIngresoRecomendadoTarjetaCredito').removeAll(true);
                Ext.getCmp('idIngresoRecomendadoTarjetaCredito').add(mapRecomendacionesTarjetaCredito); 
                if ( mapRecomendacionesIngresos.length==0 &&  mapRecomendacionesTarjetaCredito.length==0) {
                    Ext.Msg.alert('Alerta:', 'No se encontró información recomendada.');  
                }
              }else { 
                Ext.Msg.alert('Alerta:', strMensaje);           
              }
              win.getEl().unmask();
              
             },
             error: function (XMLHttpRequest, textStatus, errorThrown) {  
                win.getEl().unmask();   
                console.log('Error '+ errorThrown);
                Ext.Msg.alert('Error:', result.statusText);
               
              },
              failure: function (result) {   
                win.getEl().unmask();
                console.log( 'Error: ' + result.statusText);
                Ext.Msg.alert('Error:', result.statusText);
              
              },
    
        });
    
     }
      