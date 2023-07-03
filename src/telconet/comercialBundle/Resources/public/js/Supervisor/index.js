Ext.onReady(function() 
{
    /*
     * Grid, Store y Filtros para las Ventas Brutas
     */
    var modelVentasBrutas = Ext.define('VentasModel',
        {
            extend: 'Ext.data.Model',
            fields:
            [
                { name: 'intIdVendedor',         mapping: 'intIdVendedor' },
                { name: 'strNombreVendedor',     mapping: 'strNombreVendedor' },
                { name: 'strMetabrutas',         mapping: 'strMetabrutas' },
                { name: 'intCumplimientobrutas', mapping: 'intCumplimientobrutas' },
                { name: 'intPorcentajebrutas',   mapping: 'intPorcentajebrutas' }
            ]
        });
        
    var storeVentasBrutas = new Ext.data.Store
        ({
            pageSize: 20,
            total: 'total',
            model: modelVentasBrutas,
            proxy: 
            {
                type: 'ajax',
                url: strUrlGetVentas,
                timeout: 1200000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: 
                {
                    strTipo: 'brutas'
                }
            },
            autoLoad: true
        });

    var gridVentasBrutas = Ext.create('Ext.grid.Panel',
        {
            width: 450,
            height: 510,
            store: storeVentasBrutas,
            loadMask: true,
            iconCls: 'icon-grid',
            layout: 'fit',
            layoutConfig: 
            {
                align: 'middle'
            },
            columns: 
            [            
                new Ext.grid.RowNumberer(),
                {
                    header: 'PerfilId',
                    dataIndex: 'id_perfil',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Supervisor',
                    dataIndex: 'strNombreVendedor',
                    width: 200,
                    sortable: true
                },
                {
                    header: 'Meta',
                    dataIndex: 'strMetabrutas',
                    width: 70,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Brutas',
                    dataIndex: 'intCumplimientobrutas',
                    width: 70,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Cumplimiento',
                    dataIndex: 'intPorcentajebrutas',
                    width: 85,
                    renderer: function(value, metaData, record, row, col, store, gridView)
                    {
                        var strReturn = '';
                        
                         strReturn += '<div style="float:left; width: 20px;">';
                        
                        if( value < 70)
                        {
                            strReturn += '<div class="trafficlight trafficlight-red-small">&nbsp;</div>';
                        }
                        else if( value >= 70 && value < 99 )
                        {
                            strReturn += '<div class="trafficlight trafficlight-yellow-small">&nbsp;</div>';
                        }
                        else
                        {
                            strReturn += '<div class="trafficlight trafficlight-green-small">&nbsp;</div>';
                        }
                        
                        strReturn += '</div>'
                                    +'<div style="float:right; width: 50px; text-align: right;">'
                                        + value +'%'
                                    +'</div>';
                                
                        return  strReturn;
                    } 
                }
            ],
            title: 'Cumplimiento de Ventas Brutas',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeVentasBrutas,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'gridVentasBrutas'
        });
        
    var filterVentasBrutas = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout:
            {
                type: 'table',
                columns: 3,
                align: 'left'
            },
            bodyStyle:
            {
                background: '#fff'
            },
            collapsible: true,
            collapsed: true,
            width: 450,
            title: 'Criterios de busqueda',
            buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function()
                    {
                        buscar('ventasBrutas');
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function()
                    {
                        limpiar('ventasBrutas');
                    }
                }
            ],
            items: 
            [
                {width: '1%', border: false},
                {
                    xtype: 'textfield',
                    id: 'txtNombre',
                    fieldLabel: 'Nombre',
                    value: '',
                    labelWidth: '7',
                    width: '80%',
                    style: 'margin: 5px'
                },
                {width: '19%', border: false},
                {width: '1%', border: false},
                {width: '80%', border: false},
                {width: '19%', border: false},
                {width: '1%', border: false},
                Ext.define('Ext.form.field.Month',
                {
                    extend: 'Ext.form.field.Date',
                    alias: 'widget.monthfield',
                    id: 'dateVentasBrutas',
                    name: 'dateVentasBrutas',
                    format: 'F, Y',
                    labelWidth: '7',
                    width: '80%',
                    style: 'margin: 5px',
                    fieldLabel: 'Fecha',
                    requires: ['Ext.picker.Month'],
                    alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
                    selectMonth: null,
                    createPicker: function()
                    {
                        var me = this,
                            format = Ext.String.format;
                        
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
                        if (me.selectMonth)
                        {
                            me.setValue(me.selectMonth);
                            me.fireEvent('select', me, me.selectMonth);
                        }
                        me.collapse();
                    },
                    onSelect: function(m, d)
                    {
                        var me = this;
                        me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
                    }
                })
            ],
            renderTo: 'filtroVentasBrutas'
        });    
    /*
     * Fin Grid, Store y Filtros para las Ventas Brutas
     */


     /*
     * Grid, Store y Filtros para las Ventas Activas
     */
    var modelVentasActivas = Ext.define('VentasModel',
        {
            extend: 'Ext.data.Model',
            fields:
            [
                { name: 'intIdVendedor',          mapping: 'intIdVendedor' },
                { name: 'strNombreVendedor',      mapping: 'strNombreVendedor' },
                { name: 'strMetaactivas',         mapping: 'strMetaactivas' },
                { name: 'intCumplimientoactivas', mapping: 'intCumplimientoactivas' },
                { name: 'intPorcentajeactivas',   mapping: 'intPorcentajeactivas' }
            ]
        });
        
    var storeVentasActivas = new Ext.data.Store
        ({
            pageSize: 20,
            total: 'total',
            model: modelVentasActivas,
            proxy: 
            {
                type: 'ajax',
                url: strUrlGetVentas,
                timeout: 1200000,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: 
                {
                    strTipo: 'activas'
                }
            },
            autoLoad: true
        });

    var gridVentasActivas = Ext.create('Ext.grid.Panel',
        {
            width: 450,
            height: 510,
            store: storeVentasActivas,
            loadMask: true,
            iconCls: 'icon-grid',
            layout: 'fit',
            layoutConfig: 
            {
                align: 'middle'
            },
            columns: 
            [            
                new Ext.grid.RowNumberer(),
                {
                    header: 'PerfilId',
                    dataIndex: 'id_perfil',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Supervisor',
                    dataIndex: 'strNombreVendedor',
                    width: 200,
                    sortable: true
                },
                {
                    header: 'Meta',
                    dataIndex: 'strMetaactivas',
                    width: 70,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Activas',
                    dataIndex: 'intCumplimientoactivas',
                    width: 70,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Cumplimiento',
                    dataIndex: 'intPorcentajeactivas',
                    width: 85,
                    renderer: function(value, metaData, record, row, col, store, gridView)
                    {
                        var strReturn = '';
                        
                         strReturn += '<div style="float:left; width: 20px;">';
                        
                        if( value < 70)
                        {
                            strReturn += '<div class="trafficlight trafficlight-red-small">&nbsp;</div>';
                        }
                        else if( value >= 70 && value < 99 )
                        {
                            strReturn += '<div class="trafficlight trafficlight-yellow-small">&nbsp;</div>';
                        }
                        else
                        {
                            strReturn += '<div class="trafficlight trafficlight-green-small">&nbsp;</div>';
                        }
                        
                        strReturn += '</div>'
                                    +'<div style="float:right; width: 50px; text-align: right;">'
                                        + value +'%'
                                    +'</div>';
                                
                        return  strReturn;
                    } 
                }
            ],
            title: 'Cumplimiento de Ventas Activas',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeVentasActivas,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'gridVentasActivas'
        });
        
    var filterVentasActivas = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout:
            {
                type: 'table',
                columns: 3,
                align: 'left'
            },
            bodyStyle:
            {
                background: '#fff'
            },
            collapsible: true,
            collapsed: true,
            width: 450,
            title: 'Criterios de busqueda',
            buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function()
                    {
                        buscar('ventasActivas');
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function()
                    {
                        limpiar('ventasActivas');
                    }
                }
            ],
            items: 
            [
                {width: '1%', border: false},
                {
                    xtype: 'textfield',
                    id: 'txtNombreActivas',
                    fieldLabel: 'Nombre',
                    value: '',
                    labelWidth: '7',
                    width: '80%',
                    style: 'margin: 5px'
                },
                {width: '19%', border: false},
                {width: '1%', border: false},
                {width: '80%', border: false},
                {width: '19%', border: false},
                {width: '1%', border: false},
                Ext.define('Ext.form.field.Month',
                {
                    extend: 'Ext.form.field.Date',
                    alias: 'widget.monthfield',
                    id: 'dateVentasActivas',
                    name: 'dateVentasActivas',
                    format: 'F, Y',
                    labelWidth: '7',
                    width: '80%',
                    style: 'margin: 5px',
                    fieldLabel: 'Fecha',
                    requires: ['Ext.picker.Month'],
                    alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
                    selectMonth: null,
                    createPicker: function()
                    {
                        var me = this,
                            format = Ext.String.format;
                        
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
                        if (me.selectMonth)
                        {
                            me.setValue(me.selectMonth);
                            me.fireEvent('select', me, me.selectMonth);
                        }
                        me.collapse();
                    },
                    onSelect: function(m, d)
                    {
                        var me = this;
                        me.selectMonth = new Date((d[0] + 1) + '/1/' + d[1]);
                    }
                })
            ],
            renderTo: 'filtroVentasActivas'
        });    
    /*
     * Fin Grid, Store y Filtros para las Ventas Activas
     */


    function buscar(tipo) 
    {
        if (tipo == 'ventasBrutas')
        {
            cargarFiltrosBusquedaAlStore(tipo);
            storeVentasBrutas.load();
        }
        else
        {
            cargarFiltrosBusquedaAlStore(tipo);
            storeVentasActivas.load();
        }
    }


    function limpiar(tipo) 
    {
        if (tipo == 'ventasBrutas')
        {
            Ext.getCmp('txtNombre').value = "";
            Ext.getCmp('txtNombre').setRawValue("");
            
            Ext.getCmp('dateVentasBrutas').value = "";
            Ext.getCmp('dateVentasBrutas').setRawValue("");
            
            storeVentasBrutas.loadData([],false);
            cargarFiltrosBusquedaAlStore(tipo);
            storeVentasBrutas.currentPage = 1;
            storeVentasBrutas.load();
        }
        else
        {
            Ext.getCmp('txtNombreActivas').value  = "";
            Ext.getCmp('txtNombreActivas').setRawValue("");
            
            Ext.getCmp('dateVentasActivas').value = "";
            Ext.getCmp('dateVentasActivas').setRawValue("");
            
            storeVentasActivas.loadData([],false);
            cargarFiltrosBusquedaAlStore(tipo);
            storeVentasActivas.currentPage = 1;
            storeVentasActivas.load();
        }
    }
    
    
    function cargarFiltrosBusquedaAlStore(tipo)
    {
        if (tipo == 'ventasBrutas')
        {
            storeVentasBrutas.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
            storeVentasBrutas.getProxy().extraParams.fecha  = Ext.getCmp('dateVentasBrutas').value;
        }
        else
        {
            storeVentasActivas.getProxy().extraParams.nombre = Ext.getCmp('txtNombreActivas').value;
            storeVentasActivas.getProxy().extraParams.fecha  = Ext.getCmp('dateVentasActivas').value;
        }
    }
    
});


