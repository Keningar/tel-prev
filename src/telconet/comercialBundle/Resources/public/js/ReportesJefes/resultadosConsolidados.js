Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();
    var sizeGridConsolidados = 1204;
    var sizeColPaddingLeft = 16;
    var sizeAltoConsolidado = 700;

    var myColumnas = [];
    var myCamposMS = [];
    var myCabeceras = [];
    var mySupers = [];

    var summaryStyle = '<span style="color:white; font-weight:bolder; font-size:12">';
    var tooltip = '<span class="tooltipStyle">Recargar Resultados Consolidados</span>';
    var tooltip2 = '<div class="tooltipStyle">Exporta Resultados Consolidados</div>';

    var activoConsolidado = false;
    var activoConsolidado2 = false;

    $("#msgResultadosConsolidados").click(function()
    {
        $("#msgResultadosConsolidados").hide(400);
        activoConsolidado = false;
    });
    $("#msgResultadosConsolidados2").click(function()
    {
        $("#msgResultadosConsolidados2").hide(400);
        activoConsolidado2 = false;
    });

    //Obtención de la configuración de los componentes del GridPanel Resultados Consolidados.
    Ext.Ajax.request
        (
            {
                url: urlMetaDataResultadosConsolidados,
                method: 'post',
                success: function(response)
                {
                    var objDefine = Ext.decode(response.responseText);
                    //Parámetros que definen las estructura de columnas del GridPanel.
                    var camposMS = objDefine.metaData.fields;
                    var columnas = objDefine.metaData.columns;
                    var cantColumnas = objDefine.metaData.cantColumnas;
                    var cbxContratoDS = objDefine.metaData.contrato;
                    var cbxJurisdiccionDS = objDefine.metaData.jurisdiccion;
                    var cbxServicioDS = objDefine.metaData.servicio;

                    //Campos del Model Store para el GridPanel Resultados Consolidados.
                    camposMS.forEach(function(field)
                    {
                        myCamposMS.push(
                            {
                                name: field.name,
                                mapping: field.mapping,
                                type: field.type
                            });
                    });

                    //Definición del Model Store para el GridPanel Resultados Consolidados.
                    Ext.define('ModelStoreResultadosConsolidados',
                        {
                            extend: 'Ext.data.Model',
                            fields: myCamposMS
                        });

                    //Data Store para el GridPanel Resultados Consolidados.
                    dataStoreResultadosConsolidados = new Ext.data.Store(
                        {
                            model: 'ModelStoreResultadosConsolidados',
                            total: 'total', proxy:
                                {type: 'ajax',
                                    timeout: 600000,
                                    url: urlGridResultadosConsolidados,
                                    reader:
                                        {
                                            type: 'json',
                                            totalProperty: 'total',
                                            root: 'resultadosConsolidados'
                                        }
                                }
                        });

                    //Data Store para el ComboBox Estado del Contrato.
                    var dataStoreContrato = Ext.create('Ext.data.Store',
                        {
                            autoLoad: true,
                            fields: ['id', 'display'],
                            data: cbxContratoDS.list
                        });

                    //Data Store para el ComboBox Estado de la Jurisidicción.
                    var dataStoreJurisdiccion = Ext.create('Ext.data.Store',
                        {
                            autoLoad: true,
                            fields: ['id', 'display'],
                            data: cbxJurisdiccionDS.list
                        });

                    //Data Store para el ComboBox Estado del Servicio.
                    var dataStoreServicio = Ext.create('Ext.data.Store',
                        {
                            autoLoad: true,
                            fields: ['id', 'display'],
                            data: cbxServicioDS.list
                        });

                    cbxContrato = Ext.create('Ext.toolbar.Toolbar',
                        {
                            items:
                                [
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: 'Contrato',
                                        id: 'cbxContrato',
                                        value: cbxContratoDS.value,
                                        labelWidth: '5',
                                        store: dataStoreContrato,
                                        displayField: 'display',
                                        valueField: 'id',
                                        width: '100',
                                        matchFieldWidth: false
                                    }
                                ]
                        });

                    $('#strJurisdiccion').val(cbxJurisdiccionDS.value);

                    cbxJurisdiccion = Ext.create('Ext.toolbar.Toolbar',
                        {
                            items:
                                [
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: 'Jurisdicción',
                                        id: 'cbxJurisdiccion',
                                        value: cbxJurisdiccionDS.value,
                                        labelWidth: '7',
                                        store: dataStoreJurisdiccion,
                                        displayField: 'display',
                                        valueField: 'id',
                                        width: '100',
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

                    cbxServicio = Ext.create('Ext.toolbar.Toolbar',
                        {
                            items:
                                [
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: 'Servicio',
                                        id: 'cbxServicio',
                                        value: cbxServicioDS.value,
                                        labelWidth: '5',
                                        store: dataStoreServicio,
                                        displayField: 'display',
                                        valueField: 'id',
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

                    //Columnas del GridPanel Resultados Consolidados.
                    columnas.forEach(function(col)
                    {
                        if (col.type == 'sum') // Columnas a Sumar
                        {
                            myCabeceras.push(
                                {
                                    index: col.index
                                });
                        }
                        myColumnas.push(
                            {
                                dataIndex: col.index,
                                header: col.text,
                                width: col.width,
                                align: col.align,
                                style: 'font-weight:bold;' + col.style,
                                sortable: col.sort,
                                menuDisabled: true,
                                summaryType: col.type,
                                summaryRenderer: function(value)
                                {
                                    if (col.type == 'sum')
                                    {
                                        return Ext.String.format('{0}{1}<span>', summaryStyle, value);
                                    }
                                    else if ('total_general' == col.index)
                                    {
                                        total = 0;
                                        myCabeceras.forEach(function(cab)
                                        {
                                            total += gridResultadosConsolidados.getStore().sum(cab.index); // Suma de sumarios de columnas
                                        });
                                        return Ext.String.format('{0}<div style="padding-right: 25px;">{1}</div><span>', summaryStyle, total);
                                    }
                                    else
                                    {
                                        return Ext.String.format('{0}TOTAL({1})<span>', summaryStyle, value);
                                    }
                                },
                                renderer: function(value)
                                {
                                    if ('total_general' == col.index)
                                    {
                                        return value;
                                    }
                                    else
                                    {
                                        if ('asesores' == col.index)
                                        {
                                            mySupers = value.split(';');
                                            return '<br><div style="padding-left: 10px; font-size:12px">' + mySupers[0] + '</div>';
                                        }
                                        else
                                        {
                                            if (mySupers != null)
                                            {
                                                if (col.index == mySupers[1])
                                                {
                                                    return '<br><div style="font-size:12px">' + value + '</div>';
                                                }
                                                else
                                                {
                                                    return '<div style="display:none" />';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                    });

                    btnRefresh =
                        {
                            xtype: 'button',
                            id: 'refreshResultadosConsolidados',
                            iconCls: 'iconReloadDataStore',
                            handler: function()
                            {
                                fechaMes = Ext.getCmp('dateResultadosConsolidados').value;
                                if ((typeof fechaMes === 'undefined') || fechaMes == null)
                                {
                                    if (!activoConsolidado)
                                    {
                                        setTimeout(function()
                                        {
                                            activoConsolidado = true;
                                            $('#msgResultadosConsolidados').show(100);
                                        }, 0);
                                        setTimeout(function()
                                        {
                                            $('#msgResultadosConsolidados').hide(400);
                                            setTimeout(function()
                                            {
                                                activoConsolidado = false;
                                            }, 400);
                                        }, 3000); // Tiempo que espera antes de ejecutar el código interno
                                    }
                                }
                                else
                                {
                                    dataStoreResultadosConsolidados.sortOnLoad = false;

                                    //se quita la flecha guía de ordenamiento de las columnas
                                    for (i = 0; i < cantColumnas; i++)
                                    {
                                        objIdEl = gridResultadosConsolidados.headerCt.items.get(i).el.id;
                                        document.getElementById(objIdEl + '-textEl').className = '';
                                    }
                                    gridResultadosConsolidados.getView().refresh(true);
                                    dataStoreResultadosConsolidados.removeAll();
                                    var jurisdiccionDesc = Ext.getCmp('cbxJurisdiccion').getRawValue();
                                    dataStoreResultadosConsolidados.getProxy().extraParams.mes = fechaMes;
                                    dataStoreResultadosConsolidados.getProxy().extraParams.contrato = Ext.getCmp('cbxContrato').value;
                                    dataStoreResultadosConsolidados.getProxy().extraParams.jurisdiccion = Ext.getCmp('cbxJurisdiccion').value;
                                    dataStoreResultadosConsolidados.getProxy().extraParams.servicio = Ext.getCmp('cbxServicio').value;
                                    dataStoreResultadosConsolidados.getProxy().extraParams.contratoDesc = Ext.getCmp('cbxContrato').getRawValue();
                                    dataStoreResultadosConsolidados.getProxy().extraParams.jurisdiccionDesc = jurisdiccionDesc;
                                    dataStoreResultadosConsolidados.getProxy().extraParams.servicioDesc = Ext.getCmp('cbxServicio').getRawValue();
                                    dataStoreResultadosConsolidados.currentPage = 1;
                                    dataStoreResultadosConsolidados.load();
                                }
                            },
                            listeners:
                                {
                                    afterrender: function()
                                    {
                                        Ext.create('Ext.tip.ToolTip',
                                            {
                                                target: 'refreshResultadosConsolidados',
                                                html: tooltip,
                                                anchor: 'top'
                                            });
                                    }
                                }
                        };

                    btnExportar =
                        {
                            xtype: 'button',
                            id: 'exportarResultadosConsolidados',
                            iconCls: 'icon_exportar',
                            handler: function()
                            {
                                var permiso = $("#ROLE_312-3444");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                                if (boolPermiso)
                                {
                                    if (dataStoreResultadosConsolidados.data.getCount() == 0)
                                    {
                                        if (!activoConsolidado2)
                                        {
                                            setTimeout(function()
                                            {
                                                activoConsolidado2 = true;
                                                $('#msgResultadosConsolidados2').show(100);
                                            }, 0);
                                            setTimeout(function()
                                            {
                                                $('#msgResultadosConsolidados2').hide(400);
                                                setTimeout(function()
                                                {
                                                    activoConsolidado2 = false;
                                                }, 400);
                                            }, 3000); // Tiempo que espera antes de ejecutar el código interno
                                        }
                                    }
                                    else
                                    {
                                        document.forms[2].submit();
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
                                                target: 'exportarResultadosConsolidados',
                                                html: tooltip2,
                                                anchor: 'top'
                                            });
                                    }
                                }
                        };

                    fechaConsolidados = Ext.create('Ext.toolbar.Toolbar',
                        {
                            items:
                                [
                                    Ext.define('Ext.form.field.Month',
                                        {
                                            extend: 'Ext.form.field.Date',
                                            alias: 'widget.monthfield',
                                            id: 'dateResultadosConsolidados',
                                            name: 'dateResultadosConsolidados',
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
                                                gridview.emptyText = '<span class="x-grid-empty">Test</span>';
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
                                                var fechaMes = '';
                                                if (me.selectMonth)
                                                {
                                                    me.setValue(me.selectMonth);
                                                    me.fireEvent('select', me, me.selectMonth);
                                                    fechaMes = Ext.getCmp('dateResultadosConsolidados').value;
                                                    dataStoreResultadosConsolidados.sortOnLoad = false;

                                                    //se quita la flecha guía de ordenamiento de las columnas
                                                    for (i = 0; i < cantColumnas; i++)
                                                    {
                                                        objIdEl = gridResultadosConsolidados.headerCt.items.get(i).el.id;
                                                        document.getElementById(objIdEl + '-textEl').className = '';
                                                    }
                                                    gridResultadosConsolidados.getView().refresh(true);
                                                    dataStoreResultadosConsolidados.removeAll();

                                                    jurisdiccionSt = Ext.getCmp('cbxJurisdiccion').value;
                                                    contratoDesc = Ext.getCmp('cbxContrato').getRawValue();
                                                    jurisdiccionDesc = Ext.getCmp('cbxJurisdiccion').getRawValue();
                                                    servicioDesc = Ext.getCmp('cbxServicio').getRawValue();

                                                    dataStoreResultadosConsolidados.getProxy().extraParams.mes = fechaMes;
                                                    dataStoreResultadosConsolidados.getProxy().extraParams.contrato = Ext.getCmp('cbxContrato').value;
                                                    dataStoreResultadosConsolidados.getProxy().extraParams.jurisdiccion = jurisdiccionSt;
                                                    dataStoreResultadosConsolidados.getProxy().extraParams.servicio = Ext.getCmp('cbxServicio').value;
                                                    dataStoreResultadosConsolidados.getProxy().extraParams.contratoDesc = contratoDesc;
                                                    dataStoreResultadosConsolidados.getProxy().extraParams.jurisdiccionDesc = jurisdiccionDesc;
                                                    dataStoreResultadosConsolidados.getProxy().extraParams.servicioDesc = servicioDesc;
                                                    dataStoreResultadosConsolidados.currentPage = 1;
                                                    dataStoreResultadosConsolidados.load();

                                                    titulo = 'RESULTADOS CONSOLIDADOS POR ASESOR-SUPERVISOR (DISTRIBUIDOR) ';
                                                    titulo += Ext.getCmp('dateResultadosConsolidados').getRawValue().toUpperCase();
                                                    gridResultadosConsolidados.setTitle(titulo);
                                                    gridResultadosConsolidados.reconfigure(null, gridResultadosConsolidados.initialConfig.columns);
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

                    dataStoreResultadosConsolidados.on('load', function()
                    {
                        var myVentas = [];
                        var myMetasSuper = [];
                        var myTotales = [];

                        myCabeceras.forEach(function(cab)
                        {
                            sum = gridResultadosConsolidados.getStore().sum(cab.index);
                            myVentas.push(
                                {
                                    dataIndex: cab.index,
                                    venta: sum
                                });

                            myTotales.push(sum);
                        });

                        Ext.Ajax.request
                            (
                                {
                                    url: urlMetasSupervisores,
                                    method: 'post',
                                    params:
                                        {
                                            mes: Ext.getCmp('dateResultadosConsolidados').value,
                                            totales: myTotales.toString()
                                        },
                                    success: function(response)
                                    {
                                        var objDefine = Ext.decode(response.responseText);
                                        var metasSuper = objDefine.metas;
                                        metasSuper.forEach(function(met)
                                        {
                                            myMetasSuper.push(
                                                {
                                                    dataIndex: met.dataIndex,
                                                    meta: met.meta,
                                                    text: met.text
                                                });
                                        });

                                        max = gridResultadosConsolidados.columns.length - 1;

                                        myVentas.forEach(function(ven)
                                        {
                                            myMetasSuper.forEach(function(met)
                                            {
                                                if (ven.dataIndex == met.dataIndex)
                                                {
                                                    cumplimiento = ((ven.venta / met.meta) * 100).toFixed(2);
                                                    for (i = 1; i < max; i++)
                                                    {
                                                        if (gridResultadosConsolidados.columns[i].dataIndex == met.dataIndex)
                                                        {
                                                            text = met.text;
                                                            var strText = '';
                                                            var strTooltip = '';
                                                            strTooltip += 'title="META: ' + met.meta + ' ventas';
                                                            strTooltip += ' \nVENTAS ACTIVAS: ' + ven.venta + ' ventas';
                                                            strTooltip += ' \nCUMPLIMIENTO: ' + cumplimiento + '% "';
                                                            strText += '<div ' + strTooltip + ' style="float:left; width: 5px; ';
                                                            strText += 'cursor:pointer; padding-left: 0px">';
                                                            if (cumplimiento < 70)
                                                            {
                                                                strText += '<div class="trafficlight2 trafficlight2-red-small">&nbsp;</div>';
                                                            }
                                                            else if (cumplimiento >= 70 && cumplimiento < 99)
                                                            {
                                                                strText += '<div class="trafficlight2 trafficlight2-yellow-small">&nbsp;</div>';
                                                            }
                                                            else
                                                            {
                                                                strText += '<div class="trafficlight2 trafficlight2-green-small">&nbsp;</div>';
                                                            }
                                                            strText += '</div><div style="cursor:pointer; padding-left:15px; font-size:12px" ';
                                                            strText += strTooltip + '>' + text + ' </div>';
                                                            gridResultadosConsolidados.columns[i].setText(strText);
                                                        }
                                                    }
                                                    return;
                                                }
                                            });
                                        });
                                    },
                                    failure: function(result)
                                    {
                                        Ext.MessageBox.hide();
                                        Ext.Msg.alert('Error', result.responseText);
                                    }
                                }
                            );
                    });

                    gridResultadosConsolidados = Ext.create('Ext.grid.Panel',
                        {
                            id: 'gridResultadosConsolidados',
                            width: sizeGridConsolidados,
                            height: sizeAltoConsolidado,
                            store: dataStoreResultadosConsolidados,
                            loadMask: true,
                            renderTo: 'ResultadosConsolidados',
                            style: 'color:#1496DB',
                            iconCls: 'icon-grid',
                            cls: 'panelBar1 custom-grid extra-alt',
                            title: 'RESULTADOS CONSOLIDADOS POR ASESOR-SUPERVISOR (DISTRIBUIDOR)',
                            collapsible: true,
                            stateful: false,
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
                                                fechaConsolidados,
                                                {xtype: 'tbspacer'}, cbxContrato,
                                                {xtype: 'tbspacer'}, cbxJurisdiccion,
                                                {xtype: 'tbspacer'}, cbxServicio,
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
                                    loadingText: '<b>Cargando Informe Consolidado, Por Favor Espere',
                                    emptyText: '',
                                    deferEmptyText: true
                                },
                            columns: myColumnas
                        });

                },
                failure: function(result)
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error', result.responseText);
                }
            });

});
