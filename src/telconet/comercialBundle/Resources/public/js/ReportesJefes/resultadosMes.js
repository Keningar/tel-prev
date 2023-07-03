Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    var sizeResulMes        = 1204;
    var sizeJurisdiccion    = 434;
    var sizeVentasBrutas    = 384;
    var sizeVentasActivs    = 384;
    var sizeColPaddingLeft  = 16;
    var sizeColPaddingRight = 15;

    var tooltip      = '<div class="tooltipStyle">Recargar Resultados por Mes</div>';
    var tooltip2     = '<div class="tooltipStyle">Exporta Resultados por Mes</div>';
    var paddingL     = 'padding-left:' + sizeColPaddingLeft + 'px;';
    var paddingR     = 'padding-right:' + sizeColPaddingRight + 'px;';
    var summaryStyle = '<span style="color:white; font-weight:bolder; font-size:12;';

    var activoMes = false;
    var activoMes2 = false;
    
    $("#msgResultadosMes").click(function()
    {
        $("#msgResultadosMes").hide(400);
        activoMes = false;
    });
    $("#msgResultadosMes2").click(function()
    {
        $("#msgResultadosMes2").hide(400);
        activoMes2 = false;
    });

    Ext.define('ModelStoreResultadosMes',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'brutas',       mapping: 'brutas',       type: 'integer'},
                    {name: 'activas',      mapping: 'activas',      type: 'integer'},
                    {name: 'jurisdiccion', mapping: 'jurisdiccion', type: 'string'}
                ]
        });

    dataStoreResultadosMes = new Ext.data.Store(
        {
            model: 'ModelStoreResultadosMes',
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlGridResultadosMes,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'resultadosMes'
                        }
                }
        });

    dataStoreResultadosMes.on('load', function()
    {
        var tamanio = dataStoreResultadosMes.data.getCount();
        tamanio++;
        gridResultadosMes.height = ((tamanio < 21 ? tamanio : 20) * 33) + 112;

    });

    fechaResultadosMes = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    Ext.define('Ext.form.field.Month',
                        {
                            extend: 'Ext.form.field.Date',
                            alias: 'widget.monthfield',
                            id: 'dateResultadosMes',
                            name: 'dateResultadosMes',
                            format: 'F, Y',
                            labelWidth: '3',
                            width: 130,
                            style: 'margin-top: 0px; margin-left: ' + sizeColPaddingLeft + 'px;',
                            fieldLabel: 'Mes',
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
                                    dataStoreResultadosMes.getProxy().extraParams.mes = Ext.getCmp('dateResultadosMes').value;
                                    dataStoreResultadosMes.currentPage = 1;
                                    dataStoreResultadosMes.load();
                                    titulo = 'RESULTADOS DEL MES DE ' + Ext.getCmp('dateResultadosMes').getRawValue().toUpperCase();
                                    gridResultadosMes.setTitle(titulo);
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

    btnRefresh =
        {
            xtype: 'button',
            id: 'refreshPorMes',
            iconCls: 'iconReloadDataStore',
            handler: function()
            {
                fecha = Ext.getCmp('dateResultadosMes').value;
                if ((typeof fecha === 'undefined') || fecha == null)
                {
                    if (!activoMes)
                    {
                        setTimeout(function()
                        {
                            activoMes = true;
                            $('#msgResultadosMes').show(100);
                        }, 0);
                        setTimeout(function()
                        {
                            $('#msgResultadosMes').hide(400);
                            setTimeout(function()
                            {
                                activoMes = false;
                            }, 400);
                        }, 3000); // Tiempo que espera antes de ejecutar el código interno
                    }
                }
                else
                {
                    dataStoreResultadosMes.getProxy().extraParams.mes = Ext.getCmp('dateResultadosMes').value;
                    dataStoreResultadosMes.currentPage = 1;
                    dataStoreResultadosMes.load();
                }
            },
            listeners:
                {
                    afterrender: function()
                    {
                        Ext.create('Ext.tip.ToolTip',
                            {
                                target: 'refreshPorMes',
                                html: tooltip,
                                anchor: 'top'
                            });
                    }
                }
        };

    btnExportar =
        {
            xtype: 'button',
            id: 'exportarResultadosMes',
            iconCls: 'icon_exportar',
            handler: function()
            {
                var permiso = $("#ROLE_312-3442");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                if (boolPermiso)
                {
                    if (dataStoreResultadosMes.data.getCount() == 0)
                    {
                        if (!activoMes2)
                        {
                            setTimeout(function()
                            {
                                activoMes2 = true;
                                $('#msgResultadosMes2').show(100);
                            }, 0);
                            setTimeout(function()
                            {
                                $('#msgResultadosMes2').hide(400);
                                setTimeout(function()
                                {
                                    activoMes2 = false;
                                }, 400);
                            }, 3000); // Tiempo que espera antes de ejecutar el código interno
                        }
                    }
                    else
                    {
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
            },
            listeners:
                {
                    afterrender: function()
                    {
                        Ext.create('Ext.tip.ToolTip',
                            {
                                target: 'exportarResultadosMes',
                                html: tooltip2,
                                anchor: 'top'
                            });
                    }
                }
        };

    gridResultadosMes = Ext.create('Ext.grid.Panel',
        {
            id: 'gridResultadosMes',
            width: sizeResulMes,
            height: 130,
            store: dataStoreResultadosMes,
            loadMask: true,
            renderTo: 'ResultadosMes',
            iconCls: 'icon-grid',
            cls: 'panelBar1 custom-grid-none extra-alt',
            title: 'RESULTADOS DEL MES',
            style: 'color:#1496DB',
            collapsible: true,
            collapsed: false,
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
                                fechaResultadosMes,
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
                    loadingText: '<b>Cargando Ventas del Mes, Por favor espere',
                    emptyText: '<center><b>*** No existe información en la Fecha consultada ***',
                    deferEmptyText: true
                },
            columns:
                [
                    {
                        id: 'jurisdiccion',
                        header: 'Jurisdicción',
                        dataIndex: 'jurisdiccion',
                        style: 'font-weight:bold; padding-left:' + sizeColPaddingLeft + 'px',
                        width: sizeJurisdiccion,
                        sortable: true,
                        summaryType: 'count',
                        summaryRenderer: function()
                        {
                            return Ext.String.format('{0}{1}{2}TOTAL<span>', summaryStyle, paddingL, '">');
                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-left:' + sizeColPaddingLeft + 'px; font-size:12px">' + value + '</div><br>';
                        }
                    },
                    {
                        id: 'brutas',
                        header: 'Ventas Brutas',
                        dataIndex: 'brutas',
                        style: 'font-weight:bold',
                        width: sizeVentasBrutas,
                        align: 'right',
                        sortable: true,
                        summaryType: 'sum',
                        summaryRenderer: function(value)
                        {
                            return Ext.String.format('{0}{1}{2}{3}<span>', summaryStyle, paddingR, '">', value);
                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-right:' + sizeColPaddingRight + 'px; font-size:12px">' + value + '</div><br>';
                        }
                    },
                    {
                        id: 'activas',
                        header: 'Clientes Activos/Instalados',
                        dataIndex: 'activas',
                        style: 'font-weight:bold',
                        width: sizeVentasActivs,
                        align: 'right',
                        sortable: true,
                        summaryType: 'sum',
                        summaryRenderer: function(value)
                        {
                            return Ext.String.format('{0}{1}{2}{3}<span>', summaryStyle, paddingR, '">', value);
                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-right:' + sizeColPaddingRight + 'px; font-size:12px">' + value + '</div><br>';
                        }
                    }
                ]
        });

    var tamanio = $('#intTamanioGrids').val();
    gridResultadosMes.height = ((tamanio < 21 ? tamanio : 20) * 33) + 112;
});
