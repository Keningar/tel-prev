Ext.onReady(function() 
{
    var modelVentas = Ext.define('VentasModel',
        {
            extend: 'Ext.data.Model',
            fields:
            [
                { name: 'strTipoVenta',     mapping: 'strTipoVenta' },
                { name: 'strMeta',          mapping: 'strMeta' },
                { name: 'strVendido',       mapping: 'strVendido' },
                { name: 'intCumplimiento',  mapping: 'intCumplimiento' },
                { name: 'intSumaVendido',   mapping: 'intSumaVendido' }
            ]
        });
        
    var storeVentas = new Ext.data.Store
        ({
            pageSize: 20,
            total: 'total',
            model: modelVentas,
            timeout: 1200000,
            proxy: 
            {
                type: 'ajax',
                url: strUrlGetVentas,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            autoLoad: true
        });

    var gridVentas = Ext.create('Ext.grid.Panel',
        {
            width: 650,
            height: 190,
            store: storeVentas,
            iconCls: 'icon-grid',
            viewConfig: 
            {
                enableTextSelection: true,
                id: 'gv',
                trackOver: true,
                stripeRows: true,
                loadMask: true
            },
            columns: 
            [            
                new Ext.grid.RowNumberer(),
                {
                    header: 'Tipo de Venta',
                    dataIndex: 'strTipoVenta',
                    width: 200,
                    sortable: true
                },
                {
                    header: 'Meta',
                    dataIndex: 'strMeta',
                    width: 70,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Número de Logins',
                    dataIndex: 'strVendido',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Suma del Plan',
                    dataIndex: 'intSumaVendido',
                    width: 100,
                    align: 'center',
                    sortable: true
                },
                {
                    header: 'Cumplimiento',
                    dataIndex: 'intCumplimiento',
                    width: 155,
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
            title: 'Resumen de Ventas',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeVentas,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'gridVentasVendedor'
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
                align: 'center'
            },
            bodyStyle:
            {
                background: '#fff'
            },
            collapsible: true,
            collapsed: true,
            width: 650,
            title: 'Criterios de busqueda',
            buttons:
            [
                {
                    text: 'Buscar',
                    iconCls: "icon_search",
                    handler: function()
                    {
                        buscar();
                    }
                },
                {
                    text: 'Limpiar',
                    iconCls: "icon_limpiar",
                    handler: function()
                    {
                        limpiar();
                    }
                }
            ],
            items: 
            [
                {width: '40%', border: false},
                Ext.define('Ext.form.field.Month',
                {
                    extend: 'Ext.form.field.Date',
                    alias: 'widget.monthfield',
                    id: 'dateVentas',
                    name: 'dateVentas',
                    format: 'F, Y',
                    labelWidth: '7',
                    width: '50%',
                    align: 'center',
                    style: 'margin: 5px 150px',
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
            renderTo: 'filtroVentas'
        });


    function buscar() 
    {
        cargarFiltrosBusquedaAlStore();
        storeVentas.load();
    }


    function limpiar() 
    {
        Ext.getCmp('dateVentas').value = "";
        Ext.getCmp('dateVentas').setRawValue("");

        storeVentas.loadData([],false);
        cargarFiltrosBusquedaAlStore();
        storeVentas.currentPage = 1;
        storeVentas.load();
    }


    function cargarFiltrosBusquedaAlStore()
    {
        storeVentas.getProxy().extraParams.fecha  = Ext.getCmp('dateVentas').value;
    }
});


