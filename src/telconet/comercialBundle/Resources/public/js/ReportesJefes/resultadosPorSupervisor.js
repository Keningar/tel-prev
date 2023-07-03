Ext.require(['Ext.ux.grid.plugin.PagingSelectionPersistence']);
Ext.require('Ext.chart.*');
Ext.require(['Ext.Window', 'Ext.fx.target.Sprite', 'Ext.layout.container.Fit']);
Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    var sizeResulSuperIzquierdo = 800;
    var sizeResulSuperDerecho = 390;

    var sizeAltoGraficos = 532;

    var sizeSupervisor = 345;
    var sizeMeta = 150;
    var sizeVentas = 150;
    var sizeCumplimiento = 150;
    var sizeVentasPorAse = 175;
    var sizeEstadoAsesor = 35;
    var sizeCantAsesores = 175;

    var sizePaddingVentasAsesor = 50;
    var sizeMinAsesores = 112 + 115;
    var sizeColPadding = 16;

    var tooltip = '<div class="tooltipStyle">Recargar Resultados por Supervisor</div>';
    var tooltip2 = '<div class="tooltipStyle">Exporta Resultados por Supervisor</div>';
    var paddingL = 'padding-left:' + sizeColPadding + 'px;';
    var paddingR = 'padding-right:10px;';
    var summSt = '<span style="color:white; font-weight:bolder; font-size:12;';

    var activoSupervisor = false;
    var activoSupervisor2 = false;

    $("#msgResultadosSupervisor").click(function()
    {
        $("#msgResultadosSupervisor").hide(400);
        activoSupervisor = false;
    });
    $("#msgResultadosSupervisor2").click(function()
    {
        $("#msgResultadosSupervisor2").hide(400);
        activoSupervisor2 = false;
    });
    var coloresCumplimiento = new Array(
        '#75BEF1',
        '#0069B1',
        '#838383',
        '#FD3209',
        '#FDB900',
        '#98FB98',
        '#2F4F4F',
        '#7B68EE',
        '#3CB371',
        '#BDB76B',
        '#F3F781',
        '#81F781',
        '#8181F7',
        '#8A0868',
        '#33FF99',
        '#81F7F3');

    Ext.define('ModelStoreResultadosPorSupervisor',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'supervisor', mapping: 'supervisor', type: 'string'},
                    {name: 'meta', mapping: 'meta', type: 'integer'},
                    {name: 'ventas', mapping: 'ventas', type: 'integer'},
                    {name: 'cumplimiento', mapping: 'cumplimiento', type: 'float'}
                ]
        });

    dataStoreResultadosSupervisor = new Ext.data.Store(
        {
            model: 'ModelStoreResultadosPorSupervisor',
            total: 'total', proxy:
                {type: 'ajax',
                    timeout: 600000,
                    url: urlGridResultadosPorSupervisor,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'resultadosPorSupervisor'
                        }
                }
        });

    dataStoreResultadosSupervisor.on('load', function()
    {
        var tamanio = dataStoreResultadosSupervisor.data.getCount();
        tamanio++;
        gridResultadosPorSupervisor.height = ((tamanio == 0 ? 1 : (tamanio > 20 ? 20 : tamanio)) * 35) + 112;

        dataStoreVentasPorAsesor.getProxy().extraParams.mes = Ext.getCmp('dateResultadosSupervisor').value;
        dataStoreVentasPorAsesor.currentPage = 1;
        dataStoreVentasPorAsesor.load();
        gridResultadosPorSupervisor.getView().refresh();
    });

    fechaPorSupervisor = Ext.create('Ext.toolbar.Toolbar',
        {
            items:
                [
                    Ext.define('Ext.form.field.Month',
                        {
                            extend: 'Ext.form.field.Date',
                            alias: 'widget.monthfield',
                            id: 'dateResultadosSupervisor',
                            name: 'dateResultadosSupervisor',
                            format: 'F, Y',
                            labelWidth: '3',
                            width: 130,
                            style: 'margin-top: 0px; margin-left: ' + sizeColPadding + 'px;',
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
                                    dataStoreResultadosSupervisor.getProxy().extraParams.mes = Ext.getCmp('dateResultadosSupervisor').value;
                                    dataStoreResultadosSupervisor.currentPage = 1;
                                    dataStoreResultadosSupervisor.load();
                                    titulo = 'RESULTADOS POR SUPERVISOR EN EL MES DE ';
                                    titulo += Ext.getCmp('dateResultadosSupervisor').getRawValue().toUpperCase();
                                    gridResultadosPorSupervisor.setTitle(titulo);
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
            id: 'refreshPorSupervisor',
            iconCls: 'iconReloadDataStore',
            handler: function()
            {
                fecha = Ext.getCmp('dateResultadosSupervisor').value;
                if ((typeof fecha === 'undefined') || fecha == null)
                {
                    if (!activoSupervisor)
                    {
                        setTimeout(function()
                        {
                            activoSupervisor = true;
                            $('#msgResultadosSupervisor').show(100);
                        }, 0);
                        setTimeout(function()
                        {
                            $('#msgResultadosSupervisor').hide(400);
                            setTimeout(function()
                            {
                                activoSupervisor = false;
                            }, 400);
                        }, 3000); // Tiempo que espera antes de ejecutar el código interno
                    }
                }
                else
                {
                    dataStoreResultadosSupervisor.getProxy().extraParams.mes = Ext.getCmp('dateResultadosSupervisor').value;
                    dataStoreResultadosSupervisor.currentPage = 1;
                    dataStoreResultadosSupervisor.load();
                }
            },
            listeners:
                {
                    afterrender: function()
                    {
                        Ext.create('Ext.tip.ToolTip',
                            {
                                target: 'refreshPorSupervisor',
                                html: tooltip,
                                anchor: 'top'
                            });
                    }
                }
        };

    btnExportar =
        {
            xtype: 'button',
            id: 'exportarResultadosSupervisor',
            iconCls: 'icon_exportar',
            handler: function()
            {
                var permiso = $("#ROLE_312-3443");
                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                if (boolPermiso)
                {
                    if (dataStoreResultadosSupervisor.data.getCount() == 0)
                    {
                        if (!activoSupervisor2)
                        {
                            setTimeout(function()
                            {
                                activoSupervisor2 = true;
                                $('#msgResultadosSupervisor2').show(100);
                            }, 0);
                            setTimeout(function()
                            {
                                $('#msgResultadosSupervisor2').hide(400);
                                setTimeout(function()
                                {
                                    activoSupervisor2 = false;
                                }, 400);
                            }, 3000); // Tiempo que espera antes de ejecutar el código interno
                        }
                    }
                    else
                    {
                        document.forms[1].submit();
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
                                target: 'exportarResultadosSupervisor',
                                html: tooltip2,
                                anchor: 'top'
                            });
                    }
                }
        };

    gridResultadosPorSupervisor = Ext.create('Ext.grid.Panel',
        {
            id: 'gridResultadosPorSupervisor',
            width: sizeResulSuperIzquierdo,
            height: 112,
            store: dataStoreResultadosSupervisor,
            loadMask: true,
            renderTo: 'ResultadosPorSupervisor',
            iconCls: 'global_grid',
            cls: 'panelBar1 custom-grid-none extra-alt',
            title: 'RESULTADOS POR SUPERVISOR',
            style: 'color:#1496DB',
            collapsible: false,
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
                                fechaPorSupervisor,
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
                    loadingText: '<b>Cargando Resultados por Supervisor, Por favor espere',
                    emptyText: '<center><b>*** No tiene Supervisores a cargo o los mismos no disponen de Metas asignadas en la Fecha consultada ***',
                    deferEmptyText: true
                },
            columns:
                [
                    {
                        id: 'supervisor',
                        header: 'Supervisor',
                        dataIndex: 'supervisor',
                        style: 'font-weight:bold; padding-left:' + sizeColPadding + 'px',
                        width: sizeSupervisor,
                        sortable: true,
                        summaryType: 'count',
                        summaryRenderer: function(value)
                        {
                            return Ext.String.format('{0}{1}{2}TOTAL({3})<span>', summSt, paddingL, '">', value);
                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-left:' + sizeColPadding + 'px; font-size:12px">' + value + '</div>';
                        }
                    },
                    {
                        id: 'meta',
                        header: 'Meta',
                        dataIndex: 'meta',
                        style: 'font-weight:bold',
                        width: sizeMeta,
                        align: 'right',
                        sortable: true,
                        summaryType: 'sum',
                        summaryRenderer: function(value)
                        {
                            return Ext.String.format('{0}{1}{2}{3}<span>', summSt, paddingR, '">', value);
                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-right:10px; font-size:12px">' + value + '</div>';
                        }
                    },
                    {
                        id: 'ventas',
                        header: 'Ventas',
                        dataIndex: 'ventas',
                        style: 'font-weight:bold',
                        width: sizeVentas,
                        align: 'right',
                        sortable: true,
                        summaryType: 'sum',
                        summaryRenderer: function(value)
                        {
                            return Ext.String.format('{0}{1}{2}{3}<span>', summSt, paddingR, '">', value);
                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-right:10px; font-size:12px">' + value + '</div>';
                        }
                    },
                    {
                        text: '% Cumplimiento',
                        dataIndex: 'cumplimiento',
                        style: 'font-weight:bold',
                        align: 'right',
                        width: sizeCumplimiento,
                        sortable: true,
                        summaryType: 'sum',
                        summaryRenderer: function(value, summaryData, dataIndex)
                        {
                            var porcentaje = 0;
                            var ventas = this.grid.getStore().sum('ventas');
                            var meta = this.grid.getStore().sum('meta');
                            if (meta > 0)
                            {
                                porcentaje = ((ventas / meta) * 100).toFixed(2);
                            }
                            return Ext.String.format('{0}{1}{2}{3} %<span>', summSt, paddingR, '">', porcentaje);
                        },
                        renderer: function(value, metaData, record, row, col, store, gridView)
                        {
                            var strReturn = '';
                            strReturn += '<br><div style="float:left; width: 5px; padding-left: 50px">';
                            if (value < 70)
                            {
                                strReturn += '<div class="trafficlight2 trafficlight2-red-small">&nbsp;</div>';
                            }
                            else if (value >= 70 && value < 99)
                            {
                                strReturn += '<div class="trafficlight2 trafficlight2-yellow-small">&nbsp;</div>';
                            }
                            else
                            {
                                strReturn += '<div class="trafficlight2 trafficlight2-green-small">&nbsp;</div>';
                            }
                            strReturn += '</div><div style="padding-right:10px; font-size:12px">' + value + ' %</div>';
                            return strReturn;
                        }
                    }
                ]
        });

    graficoCumplimiento = Ext.create('widget.panel',
        {
            width: sizeResulSuperIzquierdo,
            height: sizeAltoGraficos,
            title: 'Cumplimiento',
            renderTo: Ext.get('charResultadosPorSupervisor'),
            layout: 'fit',
            iconCls: 'icon-grid',
            collapsible: false,
            bodyStyle: 'background-color:#D8D8D8;padding-left: 5px',
            collapsed: true,
            viewConfig:
                {
                    preserveScrollOnRefresh: true
                },
            items:
                {
                    xtype: 'chart',
                    id: 'chartCmp',
                    animate: true,
                    store: dataStoreResultadosSupervisor,
                    shadow: true,
                    insetPadding: 5,
                    width: 250,
                    height: 150,
                    legend:
                        {
                            position: 'right'
                        },
                    series:
                        [
                            {
                                type: 'pie',
                                colorSet: coloresCumplimiento,
                                field: 'cumplimiento',
                                showInLegend: true,
                                donut: 3,
                                style: {
                                    cursor: 'pointer'
                                },
                                label:
                                    {
                                        field: 'supervisor',
                                        display: 'inside',
                                        contrast: true,
                                        font: '18px Arial',
                                        renderer: function(text)
                                        {
                                            var rec = gridResultadosPorSupervisor.store.findRecord('supervisor', text);
                                            return rec.get('cumplimiento') + '%';
                                        }
                                    },
                                tips:
                                    {
                                        trackMouse: true,
                                        width: 160,
                                        height: 40,
                                        field: 'ventas',
                                        renderer: function(storeItem)
                                        {
                                            this.setTitle(storeItem.get('supervisor'));
                                        }
                                    },
                                highlight:
                                    {
                                        segment:
                                            {
                                                margin: 20
                                            }
                                    }
                            }
                        ]
                },
            listeners:
                {
                    expand: function()
                    {
                        gridResultadosPorSupervisor.expand();
                        gridVentasPorAsesor.expand();
                        graficoVentasPorAsesor.expand();
                    },
                    collapse: function()
                    {
                        gridResultadosPorSupervisor.collapse();
                        gridVentasPorAsesor.collapse();
                        graficoVentasPorAsesor.collapse();
                    }
                }
        });

    Ext.define('ModelStoreVentasPorAsesor',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'idVentasAsesor', mapping: 'idVentasAsesor', type: 'integer'},
                    {name: 'puntos', mapping: 'puntos', type: 'string'},
                    {name: 'indicador', mapping: 'indicador', type: 'integer'},
                    {name: 'asesores', mapping: 'asesores', type: 'integer'}
                ],
            idProperty: 'idVentasAsesor'
        });

    dataStoreVentasPorAsesor = new Ext.data.Store(
        {
            model: 'ModelStoreVentasPorAsesor',
            total: 'total', proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlGridVentasPorAsesor,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'ventasPorAsesor'
                        }
                }
        });

    dataStoreVentasPorAsesor.on('beforeload', function()
    {
        if (gridResultadosPorSupervisor.height < sizeMinAsesores)
        {
            gridVentasPorAsesor.height = sizeMinAsesores;
            gridResultadosPorSupervisor.height = sizeMinAsesores;
        }
        else
        {
            gridVentasPorAsesor.height = gridResultadosPorSupervisor.height;
        }
    });

    dataStoreVentasPorAsesor.on('load', function()
    {
        gridResultadosPorSupervisor.getView().refresh();
        gridVentasPorAsesor.getView().refresh();
    });

    gridVentasPorAsesor = Ext.create('Ext.grid.Panel',
        {
            id: 'gridVentasPorAsesor',
            width: sizeResulSuperDerecho,
            height: 112,
            store: dataStoreVentasPorAsesor,
            loadMask: true,
            renderTo: 'VentasPorAsesor',
            style: 'color:#1496DB',
            iconCls: 'icon-grid',
            cls: 'panelBar1 custom-grid-none extra-alt',
            collapsible: true,
            collapsed: true,
            columnLine: true,
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
                        items:
                            [
                                {
                                    xtype: 'label',
                                    text: '',
                                    style: 'padding-top:30px'
                                }
                            ]
                    }
                ],
            viewConfig:
                {
                    enableTextSelection: true,
                    loadingText: '<b>Cargando Rangos de Ventas de Asesores, Por Favor Espere',
                    emptyText: '<center><b>*** No se encontraron datos ***',
                    deferEmptyText: true
                },
            columns:
                [
                    {
                        id: 'puntos',
                        header: 'Ventas',
                        dataIndex: 'puntos',
                        style: 'font-weight:bold; padding-left:' + sizePaddingVentasAsesor + 'px',
                        width: sizeVentasPorAse,
                        sortable: false,
                        menuDisabled: true,
                        summaryRenderer: function()
                        {
                            return Ext.String.format('{0}{1}{2}TOTAL<span>', summSt, paddingL, '">');

                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-left:' + sizePaddingVentasAsesor + 'px; font-size:12px">' + value + '</div>';
                        }
                    },
                    {
                        id: 'indicador',
                        header: '',
                        dataIndex: 'indicador',
                        style: 'font-weight:bold',
                        width: sizeEstadoAsesor,
                        align: 'center',
                        sortable: false,
                        menuDisabled: true,
                        summaryRenderer: function()
                        {
                            return '';
                        },
                        renderer: function(value)
                        {
                            var strReturn = '';
                            strReturn += '<br><div style="float:left; width: 5px; padding-left: 10px">';
                            if (value == 0)
                            {
                                strReturn += '<div class="trafficlight2 trafficlight2-red-small">&nbsp;</div>';
                            }
                            else if (value == 1)
                            {
                                strReturn += '<div class="trafficlight2 trafficlight2-green-small">&nbsp;</div>';
                            }
                            return strReturn += '</div>';
                        }
                    },
                    {
                        id: 'asesores',
                        header: 'Asesores',
                        dataIndex: 'asesores',
                        style: 'font-weight:bold; padding-right:' + (sizePaddingVentasAsesor - 10) + 'px',
                        width: sizeCantAsesores,
                        align: 'right',
                        sortable: false,
                        menuDisabled: true,
                        summaryType: 'sum',
                        summaryRenderer: function(value)
                        {
                            return Ext.String.format('{0}padding-right:' + sizePaddingVentasAsesor + 'px;{1}{2}<span>', summSt, '">', value);

                        },
                        renderer: function(value)
                        {
                            return  '<br><div style="padding-right:' + sizePaddingVentasAsesor + 'px; font-size:12px">' + value + '</div>';
                        }
                    }
                ],
            listeners:
                {
                    expand: function()
                    {
                        gridResultadosPorSupervisor.expand();
                        graficoCumplimiento.expand();
                        graficoVentasPorAsesor.expand();
                    },
                    collapse: function()
                    {
                        gridResultadosPorSupervisor.collapse();
                        graficoCumplimiento.collapse();
                        graficoVentasPorAsesor.collapse();
                    }
                }
        });

    graficoVentasPorAsesor = Ext.create('widget.panel',
        {
            width: sizeResulSuperDerecho,
            height: sizeAltoGraficos,
            title: 'Ventas por Asesor',
            renderTo: Ext.get('charVentasPorAsesor'),
            layout: 'fit',
            iconCls: 'icon-grid',
            bodyStyle: 'background-color:#D8D8D8;padding-left: 5px',
            collapsible: false,
            collapsed: true,
            items:
                {
                    xtype: 'chart',
                    animate: true,
                    store: dataStoreVentasPorAsesor,
                    shadow: true,
                    insetPadding: 5,
                    width: 250,
                    height: 150,
                    legend:
                        {
                            position: 'right'
                        },
                    series:
                        [
                            {
                                type: 'pie',
                                colorSet: coloresCumplimiento,
                                field: 'asesores',
                                showInLegend: true,
                                donut: 3,
                                style:
                                    {
                                        cursor: 'pointer',
                                        fillOpacity: 0.8
                                    },
                                label:
                                    {
                                        field: 'puntos',
                                        display: 'inside',
                                        contrast: true,
                                        font: '18px Arial',
                                        renderer: function(text)
                                        {
                                            var rec = gridVentasPorAsesor.store.findRecord('puntos', text);
                                            if(rec)
                                            {
                                                return rec.get('asesores');
                                            }
                                        }
                                    },
                                tips:
                                    {
                                        trackMouse: true,
                                        width: 160,
                                        height: 40,
                                        field: 'asesores',
                                        layout: 'fit',
                                        renderer: function(storeItem)
                                        {
                                            this.setTitle(storeItem.get('puntos'));
                                        }
                                    },
                                highlight:
                                    {
                                        segment:
                                            {
                                                margin: 20
                                            }
                                    }
                            }
                        ]
                }
        });

});
