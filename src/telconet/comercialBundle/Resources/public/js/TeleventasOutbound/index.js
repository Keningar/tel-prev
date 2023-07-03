Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    Ext.define('ModelStore',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'login', mapping: 'login'},
                    {name: 'servicio', mapping: 'servicio'},
                    {name: 'direccion', mapping: 'direccion'},
                    {name: 'telefono', mapping: 'telefono'},
                    {name: 'email', mapping: 'email'},
                    {name: 'nombre_plan', mapping: 'nombre_plan'},
                    {name: 'jurisdiccion', mapping: 'jurisdiccion'},
                    {name: 'sector', mapping: 'sector'},
                    {name: 'cliente', mapping: 'cliente'},
                    {name: 'empresa', mapping: 'empresa'},
                    {name: 'forma_pago', mapping: 'forma_pago'}
                ]
        });

    dataStoreTeleventasOutbound = new Ext.data.Store(
        {
            pageSize: 500,
            model: 'ModelStore',
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlGridTeleventasOutbound,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'registros'
                        }
                }
        });

    Ext.create('Ext.grid.Panel',
        {
            bufferedRenderer: false,
            store: dataStoreTeleventasOutbound,
            loadMask: true,
            frame: false,
            renderTo: 'gridTeleventasOutbound',
            width: 1050,
            height: 370,
            region: 'north',
            dockedItems:
                [
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        align: '->',
                        items:
                            [
                                {xtype: 'tbfill'},
                                {
                                    iconCls: 'icon_exportar',
                                    text: 'Exportar',
                                    id: 'btnExportar',
                                    scope: this,
                                    handler: function()
                                    {
                                        var permiso = $("#ROLE_309-3038");
                                        var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                        if (boolPermiso)
                                        {
                                            if (dataStoreTeleventasOutbound.data.getCount() == 0)
                                            {
                                                Ext.Msg.show({
                                                    title: 'Error',
                                                    msg: 'No realizado ninguna consulta previa a la generación del reporte.',
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.ERROR
                                                });
                                            }
                                            else
                                            {
                                                $("#msgReporteTeleventasOutbound").hide(400);
                                                document.forms[0].submit();
                                            }
                                        }
                                        else
                                        {
                                            Ext.Msg.show({
                                                title: 'Error',
                                                msg: 'No tiene permiso para realizar esta acción.',
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.ERROR
                                            });
                                        }
                                    }
                                }
                            ]
                    }
                ],
            viewConfig:
                {
                    enableTextSelection: true,
                    preserveScrollOnRefresh: true,
                    loadingText: '<b>Cargando Servicios, Por favor espere...',
                    loadMask: true
                },
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store: dataStoreTeleventasOutbound,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar."
                }),
            columns:
                [
                    new Ext.grid.RowNumberer({width: 30}),
                    {
                        id: 'login',
                        header: 'Login',
                        dataIndex: 'login',
                        width: 120,
                        sortable: true
                    },
                    {
                        id: 'servicio',
                        header: 'Servicio',
                        dataIndex: 'servicio',
                        width: 100,
                        sortable: true
                    },
                    {
                        id: 'direccion',
                        header: 'Dirección',
                        dataIndex: 'direccion',
                        width: 350,
                        sortable: true
                    },
                    {
                        id: 'telefono',
                        header: 'Teléfono(s)',
                        dataIndex: 'telefono',
                        width: 150,
                        sortable: true
                    },
                    {
                        id: 'email',
                        header: 'Correo(s)',
                        dataIndex: 'email',
                        width: 200,
                        sortable: true
                    },
                    {
                        id: 'nombre_plan',
                        header: 'Plan',
                        dataIndex: 'nombre_plan',
                        width: 250,
                        sortable: true
                    },
                    {
                        id: 'jurisdiccion',
                        header: 'Jurisdicción',
                        dataIndex: 'jurisdiccion',
                        width: 170,
                        sortable: true
                    },
                    {
                        id: 'sector',
                        header: 'Sector',
                        dataIndex: 'sector',
                        width: 170,
                        sortable: true
                    },
                    {
                        id: 'cliente',
                        header: 'Cliente',
                        dataIndex: 'cliente',
                        width: 270,
                        sortable: true
                    },
                    {
                        id: 'empresa',
                        header: 'Empresa',
                        dataIndex: 'empresa',
                        width: 150,
                        sortable: true
                    },
                    {
                        id: 'forma_pago',
                        header: 'Forma Pago',
                        dataIndex: 'forma_pago',
                        width: 150,
                        sortable: true
                    }
                ]
        });

    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    Ext.define('ModelStoreFormasPago',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'idFormaPago', mapping: 'idFormaPago'},
                    {name: 'descripcionFormaPago', mapping: 'descripcionFormaPago'}
                ]
        });

    Ext.define('ModelStoreJurisdicciones',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id_jurisdiccion', mapping: 'id_jurisdiccion'},
                    {name: 'nombre_jurisdiccion', mapping: 'nombre_jurisdiccion'}
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

    storeEmpresa = Ext.create('Ext.data.Store',
        {
            autoLoad: true,
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

    storeEmpresa.on('load', function()
    {
        Ext.getCmp('cbxEmpresa').value = empresa;
        storeJurisdicciones.getProxy().extraParams.empresa = empresa;
        storeJurisdicciones.load();
    });

    storeJurisdicciones = Ext.create('Ext.data.Store',
        {
            autoLoad: false,
            model: 'ModelStoreJurisdicciones',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlCargarJurisdicciones,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                }
        });

    storeJurisdicciones.on('load', function()
    {
        Ext.getCmp('cbxJurisdiccion').value = parseInt(jurisdiccion);
        var boolExiste = false;
        storeJurisdicciones.each(function(record)
        {
            if (parseInt(jurisdiccion) == parseInt(record.data.id_jurisdiccion))
            {
                boolExiste = true;
                return;
            }
        }, this);

        if (!boolExiste)
        {
            Ext.getCmp('cbxJurisdiccion').select(storeJurisdicciones.getAt(0));
        }
        storeFormasPago.load();
    });

    storeFormasPago = Ext.create('Ext.data.Store',
        {
            autoLoad: false,
            model: 'ModelStoreFormasPago',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlCargarFormasPagoActivas,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                }
        });

    storeFormasPago.on('load', function()
    {
        Ext.getCmp('cbxFormasPago').select(storeFormasPago.getAt(0));
    });

    $("#msgReporteTeleventasOutbound").click(function()
    {
        $("#msgReporteTeleventasOutbound").hide(400);
    });

    setTimeout(function()
    {
        $('#msgReporteTeleventasOutbound').hide(400);
        setTimeout(function()
        {
            activoMes = false;
        }, 400);
    }, 4000); // Tiempo que espera antes de ejecutar el código interno
    
    gridTeleventasOutbound = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            renderTo: 'filtroTeleventasOutbound',
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
            collapsed: false,
            width: 1050,
            title: 'Criterios de búsqueda',
            buttons: [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function()
                    {
                        $("#msgReporteTeleventasOutbound").hide(400);
                        
                        dataStoreTeleventasOutbound.getProxy().extraParams.txtPlan         = Ext.getCmp('txtPlan').value;
                        dataStoreTeleventasOutbound.getProxy().extraParams.txtDireccion    = Ext.getCmp('txtDireccion').value;
                        dataStoreTeleventasOutbound.getProxy().extraParams.cbxServicio     = Ext.getCmp('cbxServicio').value;
                        dataStoreTeleventasOutbound.getProxy().extraParams.cbxJurisdiccion = Ext.getCmp('cbxJurisdiccion').value;
                        dataStoreTeleventasOutbound.getProxy().extraParams.txtSector       = Ext.getCmp('txtSector').value;
                        dataStoreTeleventasOutbound.getProxy().extraParams.cbxEmpresa      = Ext.getCmp('cbxEmpresa').value;
                        dataStoreTeleventasOutbound.getProxy().extraParams.txtNombres      = Ext.getCmp('txtNombres').value;
                        dataStoreTeleventasOutbound.getProxy().extraParams.txtApellidos    = Ext.getCmp('txtApellidos').value;
                        dataStoreTeleventasOutbound.getProxy().extraParams.txtRazonSocial  = Ext.getCmp('txtRazonSocial').value;
                        dataStoreTeleventasOutbound.getProxy().extraParams.cbxFormasPago   = Ext.getCmp('cbxFormasPago').value;
                        
                        dataStoreTeleventasOutbound.currentPage = 1;
                        
                        dataStoreTeleventasOutbound.load();
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function()
                    {
                        Ext.getCmp('txtPlan').value        = "";
                        Ext.getCmp('txtDireccion').value   = "";
                        Ext.getCmp('txtSector').value      = "";
                        Ext.getCmp('txtNombres').value     = "";
                        Ext.getCmp('txtApellidos').value   = "";
                        Ext.getCmp('txtRazonSocial').value = "";
                        Ext.getCmp('cbxServicio').value    = "Activo";
                        
                        Ext.getCmp('txtPlan').setRawValue("");
                        Ext.getCmp('txtDireccion').setRawValue("");
                        Ext.getCmp('txtSector').setRawValue("");
                        Ext.getCmp('txtNombres').setRawValue("");
                        Ext.getCmp('txtApellidos').setRawValue("");
                        Ext.getCmp('txtRazonSocial').setRawValue("");
                        Ext.getCmp('cbxServicio').setRawValue("Activo");
                    }
                }
            ],
            items:
                [
                    {width: 0, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtPlan',
                        name: 'txtPlan',
                        fieldLabel: 'Plan',
                        labelStyle: 'padding-left:25px;',
                        value: '',
                        width: 325
                    },
                    {width: 10, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtDireccion',
                        name: 'txtDireccion',
                        fieldLabel: 'Dirección',
                        labelStyle: 'padding-left:30px;',
                        value: '',
                        width: 325
                    },
                    {width: 30, border: false},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Servicio',
                        id: 'cbxServicio',
                        name: 'cbxServicio',
                        value: 'Activo',
                        width: 325,
                        editable: false,
                        store:
                            [
                                ['Todos', 'Todos'],
                                ['Activo', 'Activo'],
                                ['Asignada', 'Asignada'],
                                ['AsignadoTarea', 'Asignado Tarea'],
                                ['Detenido', 'Detenido'],
                                ['EnPruebas', 'En Pruebas'],
                                ['EnVerificacion', 'En Verificacion'],
                                ['Inactivo', 'Inactivo'],
                                ['Preplanificada', 'Pre-planificada'],
                                ['Planificada', 'Planificada'],
                                ['Replanificada', 'Re-planificada']
                            ]
                    },
                    // SEGUNDA FILA
                    {width: 0, border: false},
                    {
                        xtype: 'combobox',
                        id: 'cbxEmpresa',
                        name: 'cbxEmpresa',
                        fieldLabel: 'Empresa',
                        value: '',
                        width: 325,
                        labelStyle: 'padding-left:25px;',
                        store: storeEmpresa,
                        labelAlign: 'left',
                        valueField: 'prefijo',
                        displayField: 'nombre_empresa',
                        triggerAction: 'all',
                        queryMode: 'local',
                        allowBlank: true,
                        editable: false,
                        matchFieldWidth: true,
                        listeners:
                            {
                                select: function()
                                {
                                    //Consulta las jurisdicciones por empresa
                                    Ext.getCmp('cbxJurisdiccion').value = '';
                                    Ext.getCmp('cbxJurisdiccion').setRawValue('');
                                    
                                    storeJurisdicciones.getProxy().extraParams.empresa = Ext.getCmp('cbxEmpresa').value;
                                    
                                    storeJurisdicciones.currentPage = 1;
                                    
                                    storeJurisdicciones.load();
                                }
                            }
                    },
                    {width: 10, border: false},
                    {
                        xtype: 'combobox',
                        id: 'cbxJurisdiccion',
                        name: 'cbxJurisdiccion',
                        fieldLabel: 'Jurisdicción',
                        store: storeJurisdicciones,
                        labelStyle: 'padding-left:30px;',
                        value: '',
                        width: 325,
                        labelAlign: 'left',
                        valueField: 'id_jurisdiccion',
                        displayField: 'nombre_jurisdiccion',
                        triggerAction: 'all',
                        queryMode: 'local',
                        allowBlank: true,
                        editable: false,
                        matchFieldWidth: true
                    },
                    {width: 10, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtSector',
                        name: 'txtSector',
                        fieldLabel: 'Sector',
                        value: '',
                        width: 325
                    },
                    // TERCERA FILA
                    {width: 0, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtNombres',
                        name: 'txtNombres',
                        fieldLabel: 'Nombres',
                        labelStyle: 'padding-left:25px;',
                        value: '',
                        maxLength: 50,
                        width: 325
                    },
                    {width: 10, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtApellidos',
                        name: 'txtApellidos',
                        fieldLabel: 'Apellidos',
                        labelStyle: 'padding-left:30px;',
                        value: '',
                        maxLength: 50,
                        width: 325
                    },
                    {width: 10, border: false},
                    {
                        xtype: 'combobox',
                        id: 'cbxFormasPago',
                        name: 'cbxFormasPago',
                        fieldLabel: 'Forma de Pago',
                        value: '',
                        width: 325,
                        store: storeFormasPago,
                        labelAlign: 'left',
                        valueField: 'idFormaPago',
                        displayField: 'descripcionFormaPago',
                        triggerAction: 'all',
                        queryMode: 'local',
                        allowBlank: true,
                        editable: false,
                        matchFieldWidth: true
                    },
                    {width: 0, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtRazonSocial',
                        name: 'txtRazonSocial',
                        fieldLabel: 'Razon Social',
                        labelStyle: 'padding-left:25px;white-space: nowrap;',
                        value: '',
                        width: 325
                    },
                    {width: 10, border: false},
                    {width: 30, border: false},
                    {width: 30, border: false},
                    {width: 30, border: false}
                ]
        });

    Ext.getCmp('btnExportar').disable();
    var permiso = $("#ROLE_309-3038");
    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

    if (boolPermiso)
    {
        Ext.getCmp('btnExportar').show();
        Ext.getCmp('btnExportar').enable();
    }
    else
    {
        Ext.getCmp('btnExportar').hide();
    }
});
