Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    var sizeResulSuperIzquierdo = 1204;

    var sizeMotivo = 650;
    var sizeMegaGye = 275;
    var sizePorcRch = 275;
    var sizeAltoRechazos = 600;
    
    var boolExpandio = false;

    var sizeColPadding = 50;
    var summSt = '<span style="color:white; font-weight:bolder; font-size:12; ';
    var paddingL = 'padding-left:' + sizeColPadding + 'px;';
    var paddingR = 'padding-right:' + (sizeColPadding - 30) + 'px;';
    var tooltip = '<div class="tooltipStyle">Recargar Rechazos en Ventas</div>';
    var tooltip2 = '<div class="tooltipStyle">Exporta Listado de Rechazos en Ventas</div>';

    var activoRechazos = false;
    var activoRechazos2 = false;

    $("#msgRechazosVentas").click(function()
    {
        $("#msgRechazosVentas").hide(400);
        activoRechazos = false;
    });
    $("#msgRechazosVentas2").click(function()
    {
        $("#msgRechazosVentas2").hide(400);
        activoRechazos2 = false;
    });

    Ext.define('ModelStoreRechazos',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'motivo_desc', mapping: 'motivo_desc', type: 'string'},
                    {name: 'cant_rechazos', mapping: 'cant_rechazos', type: 'integer'},
                    {name: 'porc_rechazos_desc', mapping: 'porc_rechazos_desc', type: 'string'},
                    {name: 'porc_rechazos', mapping: 'porc_rechazos', type: 'float'}
                ]
        });

    Ext.define('ModelStoreSupervisores',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id_persona_sup', mapping: 'id_persona_sup', type: 'string'},
                    {name: 'nombre_sup', mapping: 'nombre_sup', type: 'string'}
                ]
        });

    Ext.define('ModelStoreAsesores',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'login_asesor', mapping: 'login_asesor', type: 'string'},
                    {name: 'nombre_ase', mapping: 'nombre_ase', type: 'string'}
                ]
        });

    Ext.define('ModelStoreJurisdicciones2',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id', mapping: 'id'},
                    {name: 'display', mapping: 'display'}
                ]
        });

    dataStoreRechazos = new Ext.data.Store(
        {
            model: 'ModelStoreRechazos',
            total: 'total',
            proxy:
                {type: 'ajax',
                    timeout: 600000,
                    url: urlGridRechazos,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'rechazos'
                        }
                }
        });

    dataStoreSupervisores = new Ext.data.Store(
        {
            autoLoad: false,
            model: 'ModelStoreSupervisores',
            total: 'total',
            proxy:
                {type: 'ajax',
                    timeout: 600000,
                    url: urlCargarSupervisores,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'supervisores'
                        }
                }
        });

    dataStoreAsesores = new Ext.data.Store(
        {
            model: 'ModelStoreAsesores',
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlCargarAsesores,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'asesores'
                        }
                }
        });

    dataStoreJurisdicciones2 = new Ext.data.Store(
        {
            autoLoad: false,
            model: 'ModelStoreJurisdicciones2',
            total: 'total',
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

    fechaRechazosVentas = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    Ext.define('Ext.form.field.Month',
                        {
                            extend: 'Ext.form.field.Date',
                            alias: 'widget.monthfield',
                            id: 'dateRechazos',
                            name: 'dateRechazos',
                            format: 'F, Y',
                            labelWidth: '3',
                            width: 130,
                            style: 'margin-top: 0px; margin-left: 16px;',
                            fieldLabel: 'Mes',
                            maxValue: new Date(),
                            editable: false,
                            requires: ['Ext.picker.Month'],
                            alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
                            selectMonth: null,
                            handler: function(button)
                            {
                                var gridpanel = button.up('gridpanel');
                                var gridview = gridpanel.getView();
                                gridview.emptyText = '<div class="x-grid-empty">Test</div>';
                                gridview.refresh();
                            },
                            createPicker: function()
                            {
                                var me = this;
                                var format = Ext.String.format;
                                return Ext.create('Ext.picker.Month',
                                    {
                                        pickerField: me,
                                        ownerCt: me.ownerCt,
                                        renderTo: document.body,
                                        floating: true,
                                        hidden: true,
                                        focusOnShow: true,
                                        minDate: me.minValue,
                                        maxDate: me.maxValue,
                                        disabledDatesRE: me.disabledDatesRE,
                                        disabledDatesText: me.disabledDatesText,
                                        disabledDays: me.disabledDays,
                                        disabledDaysText: me.disabledDaysText,
                                        format: me.format,
                                        showToday: me.showToday,
                                        startDay: me.startDay,
                                        minText: format(me.minText, me.formatDate(me.minValue)),
                                        maxText: format(me.maxText, me.formatDate(me.maxValue)),
                                        listeners:
                                            {
                                                select:
                                                    {
                                                        scope: me,
                                                        fn: me.onSelect
                                                    },
                                                monthdblclick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                yeardblclick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                OkClick:
                                                    {
                                                        scope: me,
                                                        fn: me.onOKClick
                                                    },
                                                CancelClick:
                                                    {
                                                        scope: me,
                                                        fn: me.onCancelClick
                                                    }
                                            },
                                        keyNavConfig:
                                            {
                                                esc: function()
                                                {
                                                    me.collapse();
                                                }
                                            }
                                    });
                            },
                            onCancelClick: function()
                            {
                                var me = this;
                                me.selectMonth = null;
                                me.collapse();
                            },
                            onOKClick: function()
                            {
                                var me = this;
                                var titulo = '';
                                if (me.selectMonth)
                                {
                                    me.setValue(me.selectMonth);
                                    me.fireEvent('select', me, me.selectMonth);
                                    dataStoreRechazos.getProxy().extraParams.mes = Ext.getCmp('dateRechazos').value;
                                    dataStoreRechazos.getProxy().extraParams.supervisor = Ext.getCmp('cbxSupervisores').value;
                                    dataStoreRechazos.getProxy().extraParams.asesor = Ext.getCmp('cbxAsesores').value;
                                    dataStoreRechazos.getProxy().extraParams.jurisdiccion = Ext.getCmp('cbxJurisdicciones2').value;
                                    dataStoreRechazos.getProxy().extraParams.supervisorDesc = Ext.getCmp('cbxSupervisores').getRawValue();
                                    dataStoreRechazos.getProxy().extraParams.asesorDesc = Ext.getCmp('cbxAsesores').getRawValue();
                                    dataStoreRechazos.getProxy().extraParams.jurisdiccionDesc = Ext.getCmp('cbxJurisdicciones2').getRawValue();
                                    dataStoreRechazos.currentPage = 1;
                                    dataStoreRechazos.load();
                                    titulo = 'RECHAZOS EN VENTAS DE ';
                                    titulo += Ext.getCmp('dateRechazos').getRawValue().toUpperCase();
                                    gridRechazos.setTitle(titulo);
                                }
                                me.collapse();
                            },
                            onSelect: function(m, d)
                            {
                                var me = this;
                                me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
                            }
                        }
                    )
                ]
        });

    dataStoreRechazos.on('load', function()
    {
        Ext.getCmp('cantRechazos').setWidth(dataStoreRechazos.data.getCount() > 13 ? sizeMegaGye - 12 : sizeMegaGye);
    });

    cbxSupervisores = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Supervisor',
                        id: 'cbxSupervisores',
                        value: 'Todos',
                        labelWidth: '7',
                        store: dataStoreSupervisores,
                        displayField: 'nombre_sup',
                        valueField: 'id_persona_sup',
                        width: 320,
                        triggerAction: 'all',
                        queryMode: 'local',
                        allowBlank: true,
                        editable: false,
                        matchFieldWidth: false,
                        listeners:
                            {
                                select: function()
                                {
                                    //Consulta los Asesores por el supervisor seleccionado
                                    Ext.getCmp('cbxAsesores').value = 'Todos';
                                    Ext.getCmp('cbxAsesores').setRawValue('Todos');
                                    dataStoreAsesores.getProxy().extraParams.supervisor = Ext.getCmp('cbxSupervisores').value;
                                    dataStoreAsesores.currentPage = 1;
                                    dataStoreAsesores.load();

                                    //Consulta los Rechazos
                                    dataStoreRechazos.getProxy().extraParams.mes = Ext.getCmp('dateRechazos').value;
                                    dataStoreRechazos.getProxy().extraParams.supervisor = Ext.getCmp('cbxSupervisores').value;
                                    dataStoreRechazos.getProxy().extraParams.asesor = Ext.getCmp('cbxAsesores').value;
                                    dataStoreRechazos.getProxy().extraParams.jurisdiccion = Ext.getCmp('cbxJurisdicciones2').value;
                                    dataStoreRechazos.getProxy().extraParams.supervisorDesc = Ext.getCmp('cbxSupervisores').getRawValue();
                                    dataStoreRechazos.getProxy().extraParams.asesorDesc = Ext.getCmp('cbxAsesores').getRawValue();
                                    dataStoreRechazos.getProxy().extraParams.jurisdiccionDesc = Ext.getCmp('cbxJurisdicciones2').getRawValue();
                                    dataStoreRechazos.currentPage = 1;
                                    dataStoreRechazos.load();
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
                    }
                ]
        });

    cbxAsesores = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Asesor',
                        id: 'cbxAsesores',
                        value: 'Todos',
                        labelWidth: '5',
                        store: dataStoreAsesores,
                        displayField: 'nombre_ase',
                        valueField: 'login_asesor',
                        width: 320,
                        triggerAction: 'all',
                        queryMode: 'local',
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
                    }
                ]
        });

    var comboJurisdicciones = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            fieldLabel: 'Jurisdicción',
            id: 'cbxJurisdicciones2',
            value: 'Todos',
            labelWidth: '7',
            store: dataStoreJurisdicciones2,
            displayField: 'display',
            valueField: 'id',
            width: 280,
            triggerAction: 'all',
            queryMode: 'local',
            allowBlank: true,
            editable: false,
            matchFieldWidth: false,
            listeners:
                {
                    select: function()
                    {
                        gridRechazos.columns[1].setText(Ext.getCmp('cbxJurisdicciones2').value);
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
        }
    );

    var cbxJurisdicciones2 = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    comboJurisdicciones
                ]
        });

    dataStoreJurisdicciones2.on('load', function()
    {
        comboJurisdicciones.setValue($('#strJurisdiccion').val());
        comboJurisdicciones.setRawValue($('#strJurisdiccion').val());
        gridRechazos.columns[1].setText($('#strJurisdiccion').val());
    });

    btnRefresh =
        {
            xtype: 'button',
            id: 'refreshRechazos',
            iconCls: 'iconReloadDataStore',
            handler: function()
            {
                fecha = Ext.getCmp('dateRechazos').value;
                if ((typeof fecha === 'undefined') || fecha == null)
                {
                    if (!activoRechazos)
                    {
                        setTimeout(function()
                        {
                            activoRechazos = true;
                            $('#msgRechazosVentas').show(100);
                        }, 0);
                        setTimeout(function()
                        {
                            $('#msgRechazosVentas').hide(400);
                            setTimeout(function()
                            {
                                activoRechazos = false;
                            }, 400);
                        }, 3000); // Tiempo que espera antes de ejecutar el código interno
                    }
                }
                else
                {
                    dataStoreRechazos.getProxy().extraParams.mes = Ext.getCmp('dateRechazos').value;
                    dataStoreRechazos.getProxy().extraParams.supervisor = Ext.getCmp('cbxSupervisores').value;
                    dataStoreRechazos.getProxy().extraParams.asesor = Ext.getCmp('cbxAsesores').value;
                    dataStoreRechazos.getProxy().extraParams.jurisdiccion = Ext.getCmp('cbxJurisdicciones2').value;
                    dataStoreRechazos.getProxy().extraParams.supervisorDesc = Ext.getCmp('cbxSupervisores').getRawValue();
                    dataStoreRechazos.getProxy().extraParams.asesorDesc = Ext.getCmp('cbxAsesores').getRawValue();
                    dataStoreRechazos.getProxy().extraParams.jurisdiccionDesc = Ext.getCmp('cbxJurisdicciones2').getRawValue();
                    dataStoreRechazos.currentPage = 1;
                    dataStoreRechazos.load();
                }
            },
            listeners:
                {
                    afterrender: function()
                    {
                        Ext.create('Ext.tip.ToolTip',
                            {
                                target: 'refreshRechazos',
                                html: tooltip,
                                anchor: 'top'
                            });
                    }
                }
        };

    btnExportar =
        {
            xtype: 'button',
            id: 'exportarRechazosVentas',
            iconCls: 'icon_exportar',
            handler: function()
            {
                var permiso = $("#ROLE_312-3445");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                if (boolPermiso)
                {
                    if (dataStoreRechazos.data.getCount() == 0)
                    {
                        if (!activoRechazos2)
                        {
                            setTimeout(function()
                            {
                                activoRechazos2 = true;
                                $('#msgRechazosVentas2').show(100);
                            }, 0);
                            setTimeout(function()
                            {
                                $('#msgRechazosVentas2').hide(400);
                                setTimeout(function()
                                {
                                    activoRechazos2 = false;
                                }, 400);
                            }, 3000); // Tiempo que espera antes de ejecutar el código interno
                        }
                    }
                    else
                    {
                        document.forms[3].submit();
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
            },
            listeners:
                {
                    afterrender: function()
                    {
                        Ext.create('Ext.tip.ToolTip',
                            {
                                target: 'exportarRechazosVentas',
                                html: tooltip2,
                                anchor: 'top'
                            });
                    }
                }
        };

    gridRechazos = Ext.create('Ext.grid.Panel',
        {
            id: 'gridRechazos',
            width: sizeResulSuperIzquierdo,
            height: sizeAltoRechazos,
            store: dataStoreRechazos,
            loadMask: true,
            renderTo: 'ReporteRechazos',
            iconCls: 'global_grid',
            cls: 'panelBar1 custom-grid extra-alt',
            title: 'RECHAZOS EN VENTAS',
            style: 'color:#1496DB',
            collapsible: true,
            collapsed: true,
            columnLines: true,
            features:
                [
                    {
                        ftype: 'summary',
                        dock: 'bottom'
                    }
                ],
            dockedItems:
                [
                    {
                        xtype: 'toolbar',
                        dock: 'top',
                        layout:
                            {
                                pack: 'start',
                                type: 'hbox'
                            },
                        items:
                            [
                                fechaRechazosVentas,
                                {xtype: 'tbspacer'}, cbxSupervisores,
                                {xtype: 'tbspacer'}, cbxAsesores,
                                {xtype: 'tbspacer'}, cbxJurisdicciones2,
                                {xtype: 'tbspacer'}, {xtype: 'tbseparator'},
                                {xtype: 'tbspacer'}, btnRefresh,
                                {xtype: 'tbspacer'}, {xtype: 'tbseparator'},
                                {xtype: 'tbspacer'}, btnExportar
                            ]
                    }
                ],
            viewConfig:
                {
                    enableTextSelection: true,
                    loadingText: '<b>Cargando Rechazos en Ventas, Por favor espere',
                    emptyText: '',
                    deferEmptyText: true
                },
            columns:
                [
                    {
                        id: 'motivoDesc',
                        header: 'MOTIVO',
                        dataIndex: 'motivo_desc',
                        style: 'font-weight:bold; padding-left:' + sizeColPadding + 'px',
                        width: sizeMotivo,
                        sortable: true,
                        summaryType: 'count',
                        summaryRenderer: function(value)
                        {
                            return Ext.String.format('{0}{1}{2}TOTAL({3})<span>', summSt, paddingL, '">', value);
                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-left:' + sizeColPadding + 'px; font-size:12px">' + value + '</div><br>';
                        }
                    },
                    {
                        id: 'cantRechazos',
                        header: 'MEGADATOS GUAYAQUIL',
                        dataIndex: 'cant_rechazos',
                        style: 'font-weight:bold; padding-right:' + (sizeColPadding - 40) + 'px;',
                        width: sizeMegaGye,
                        align: 'right',
                        sortable: true,
                        summaryType: 'sum',
                        summaryRenderer: function(value)
                        {
                            return Ext.String.format('{0}{1}{2}{3}<span>', summSt, paddingR, '">', value);
                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-right:' + (sizeColPadding - 30) + 'px; font-size:12px">' + value + '</div><br>';
                        }
                    },
                    {
                        id: 'porcRechazos',
                        header: '% DE RECHAZO',
                        dataIndex: 'porc_rechazos_desc',
                        style: 'font-weight:bold; padding-right:' + (sizeColPadding - 40) + 'px;',
                        width: sizePorcRch,
                        align: 'right',
                        sortable: true,
                        summaryType: 'count',
                        summaryRenderer: function()
                        {
                            rechazos = this.grid.getStore().sum('porc_rechazos');
                            render = Ext.String.format('{0}{1}{2}{3} %<span>', summSt, paddingR, '">', rechazos.toFixed(2));
                            return render;
                        },
                        renderer: function(value)
                        {
                            var strIni = '<br><div style="cursor:pointer; padding-right:';
                            return strIni + (sizeColPadding - 30) + 'px; font-size:12px" ' + value + ' %</div><br>';
                        }
                    }
                ],
            listeners:
                {
                    expand: function()
                    {
                        if (!boolExpandio)
                        {
                            dataStoreSupervisores.load();
                            dataStoreJurisdicciones2.load();
                        }
                        boolExpandio = true;
                    }
                }
        });
});
