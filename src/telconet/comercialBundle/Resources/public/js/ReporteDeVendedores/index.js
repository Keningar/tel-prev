Ext.onReady(function()
{
    var modelVendedores = Ext.define('Comercial.Reportes.VendedoresModel',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'nombreCompleto', mapping: 'nombre'},
                    {name: 'login'}
                ]
        });

    var storeVendedores = new Ext.data.Store
        ({
            // pageSize: 20,
            // total: 'total',
            model: modelVendedores,
            timeout: 1200000,
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetVendedores,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'registros'

                        }
                },
            autoLoad: true
        });




    var vendedores_combo = new Ext.form.ComboBox({
        xtype: 'combo',
        labelAlign: 'left',
        store: storeVendedores,
        id: 'idComboVendedor',
        name: 'comboVendedor',
        valueField: 'login',
        displayField: 'nombreCompleto',
        fieldLabel: 'Nombre Vendedor:',
        width: 450,
        queryMode: 'local',
        typeAhead: true,
        emptyText: 'Ingrese el nombre del vendedor',
        listeners: {
            beforequery: function(record) {
                record.query = new RegExp(record.query, 'i');
                record.forceAll = true;
            }
        }

    });


    var fecha = Ext.define('Ext.form.field.Month',
        {
            extend: 'Ext.form.field.Date',
            alias: 'widget.monthfield',
            id: 'dateServicio',
            name: 'dateServicio',
            format: 'F, Y',
            labelWidth: '7',
            width: '50%',
            fieldLabel: 'Fecha:',
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
        });

    var filterVendedores = Ext.create('Ext.panel.Panel',
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
            width: 1200,
            title: 'Criterios de busqueda',
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function()
                        {
                            buscarPuntoPorVendedor();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function()
                        {
                            limpiarFiltroVendedores();
                        }
                    }
                ],
            items:
                [
                    vendedores_combo,
                    {html: "&nbsp;", border: false, width: 50},
                    fecha


                ],
            renderTo: 'filtroVendedores'
        });


    var modelReporteVentas = Ext.define('Comercial.Reportes.ReporteVentasVendedorModel',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'nombreCliente'},
                    {name: 'direccionCliente', type: 'string'},
                    {name: 'loginPunto'},
                    {name: 'direccionPunto'},
                    {name: 'planProducto'},
                    {name: 'precioVenta'},
                    {name: 'cantidad'},
                    {name: 'porcentajeDescuento'},
                    {name: 'valorDescuento'},
                    {name: 'esVenta'},
                    {name: 'loginPuntoFacturacion'},
                    {name: 'feCreacionServ'},
                    {name: 'fechaActivacionServicio'}
                ]
        });

    var storeReporteVentas = new Ext.data.Store
        ({
            pageSize: 600,
            total: 'total',
            model: modelReporteVentas,
            timeout: 1200000,
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetReporteVentasPorVendedor,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        }
                }
        });

    var gridReporteVentas = Ext.create('Ext.grid.Panel',
        {
            width: 1200,
            height: 400,
            store: storeReporteVentas,
            iconCls: 'icon-grid',
            dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items: [
                        //tbfill -> alinea los items siguientes a la derecha
                        {xtype: 'tbfill'},
                        {
                            iconCls: 'icon_exportar',
                            text: 'Exportar',
                            disabled: false,
                            itemId: 'exportar',
                            scope: this,
                            handler: function() {
                                exportar()
                            }
                        }
                    ]}],
            viewConfig:
                {
                    enableTextSelection: true,
                    id: 'gridReporteVentasPorVendedor',
                    trackOver: true,
                    stripeRows: true,
                    loadMask: true
                },
            columns:
                [
                    new Ext.grid.RowNumberer(),
                    {
                        header: 'Fecha Activación de Servicio',
                        dataIndex: 'fechaActivacionServicio',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Fecha Creación',
                        dataIndex: 'feCreacionServ',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Cliente',
                        dataIndex: 'nombreCliente',
                        width: 200,
                        sortable: true
                    },
                    {
                        header: 'Dirección Cliente',
                        dataIndex: 'direccionCliente',
                        width: 200,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Punto',
                        dataIndex: 'loginPunto',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Dirección Punto',
                        dataIndex: 'direccionPunto',
                        width: 200,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Punto Facturación',
                        dataIndex: 'loginPuntoFacturacion',
                        width: 100,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Producto /Plan (Servicio)',
                        dataIndex: 'planProducto',
                        width: 100,
                        align: 'center',
                        sortable: true

                    },                    
                    {
                        header: 'Precio',
                        dataIndex: 'precioVenta',
                        width: 50,
                        align: 'center',
                        sortable: true

                    },
                    {
                        header: 'Cantidad',
                        dataIndex: 'cantidad',
                        width: 50,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Porcentaje Descuento',
                        dataIndex: 'porcentajeDescuento',
                        width: 50,
                        align: 'center',
                        sortable: true
                    },
                    {
                        header: 'Valor Descuento',
                        dataIndex: 'valorDescuento',
                        width: 50,
                        align: 'center',
                        sortable: true

                    },
                    {
                        header: 'Es Venta',
                        dataIndex: 'esVenta',
                        width: 50,
                        align: 'center',
                        sortable: true

                    }


                ],
            title: 'Reporte de Ventas',
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeReporteVentas,
                displayInfo: true,
                displayMsg: 'Desde {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            }),
            renderTo: 'gridReporteDeVentas'
        });







    function buscarPuntoPorVendedor()
    {
        var valorFecha = Ext.getCmp('dateServicio').value;
        var valorVendedor = Ext.getCmp('idComboVendedor').value

        if (valorFecha && valorVendedor)
        {
            cargarFiltrosBusquedaAlStorePuntos();
            storeReporteVentas.load();

        }
        else
        {

            Ext.Msg.alert('Error', 'Debe escoger el vendedor y el mes del reporte');

        }



    }

    function limpiarFiltroVendedores()
    {
        Ext.getCmp('dateServicio').value = "";
        Ext.getCmp('dateServicio').setRawValue("");

        Ext.getCmp('idComboVendedor').value = "";
        Ext.getCmp('idComboVendedor').setRawValue("");

        storeReporteVentas.loadData([], false);
        cargarFiltrosBusquedaAlStorePuntos();
        storeReporteVentas.currentPage = 1;
    }


    function cargarFiltrosBusquedaAlStorePuntos()
    {
        storeReporteVentas.getProxy().extraParams.fecha = Ext.getCmp('dateServicio').value;
        storeReporteVentas.getProxy().extraParams.usuarioVendedor = Ext.getCmp('idComboVendedor').value;
    }
    function pad(n) {
        return n < 10 ? '0' + n : n
    }




    function ISODateString(d) {
        function pad(n) {
            return n < 10 ? '0' + n : n
        }
        return d.getUTCFullYear() + '-'
            + pad(d.getUTCMonth() + 1) + '-'
            + pad(d.getUTCDate()) + 'T'
            + pad(d.getUTCHours()) + ':'
            + pad(d.getUTCMinutes()) + ':'
            + pad(d.getUTCSeconds()) + 'Z'
    }

    function exportar() {

        var valorFecha = Ext.getCmp('dateServicio').value;
        var valorVendedor = Ext.getCmp('idComboVendedor').value;

        if (valorFecha && valorVendedor)
        {
            var fecha = ISODateString(valorFecha);
            window.open(strUrlExcelPuntosServiciosPorVendedor + '?fecha=' + fecha +
                '&usuarioVendedor=' + valorVendedor);
        }
        else
        {

            Ext.Msg.alert('Error', 'Debe escoger el vendedor y el mes del reporte');

        }

    }


});