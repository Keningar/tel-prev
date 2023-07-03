Ext.require('Ext.chart.*');
Ext.require('Ext.layout.container.Fit');

Ext.onReady(function () {

    dateFechaDesde = new Ext.form.DateField({
        id: 'dateFechaDesde',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });

    dateFechaHasta = new Ext.form.DateField({
        id: 'dateFechaHasta',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325
    });

    Ext.define('modelOficinas', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_oficina', type: 'string'},
            {name: 'nombre_oficina', type: 'string'}
        ]
    });

    var storeOficina = Ext.create('Ext.data.Store', {
        autoLoad: true,
        model: "modelOficinas",
        proxy: {
            type: 'ajax',
            url: url_getOficinas,
            reader: {
                type: 'json',
                root: 'encontrados'
            },
            extraParams: {
                idEmpresa: empresa
            }
        }
    });

    var comboOficinas = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: storeOficina,
        labelAlign: 'left',
        name: 'comboOficinas',
        id: 'comboOficinas',
        valueField: 'id_oficina',
        displayField: 'nombre_oficina',
        fieldLabel: 'Oficina',
        width: 325,
        triggerAction: 'all',
        queryMode: 'local',
        allowBlank: true,
        listeners: {
            select:
                function (e) {
                },
            click: {
                element: 'el',
                fn: function () {
                }
            }
        }
    });

    var objFilterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'left',
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: Buscar,
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function () {
                    Limpiar();
                }
            }

        ],
        items: [
            {html: "&nbsp;", border: false, width: 50},
            dateFechaDesde,
            {html: "&nbsp;", border: false, width: 50},
            dateFechaHasta,
            {html: "&nbsp;", border: false, width: 50},
            {html: "&nbsp;", border: false, width: 50},
            comboOficinas,
            {html: "&nbsp;", border: false, width: 50},
            {html: "&nbsp;", border: false, width: 325},
            {html: "&nbsp;", border: false, width: 50}
        ],
        renderTo: 'filtroFacturasElectronicas'
    });

    Ext.define('modelResumenFacturas', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'MES', type: 'string'},
            {name: 'PENDIENTES ENVIO SRI', type: 'int'},
            {name: 'PROCESANDO', type: 'int'},
            {name: 'RECHAZADAS', type: 'int'},
            {name: 'AUTORIZADAS', type: 'int'},
            {name: 'ACTUALIZADAS', type: 'int'},
            {name: 'CON ERRORES', type: 'int'},
            {name: 'TOTAL', type: 'int'},
            {name: 'FECHA', type: 'string'}
        ]
    });

    /*Inicio Notas de Credito*/
    storeResumenNostasCredito = Ext.create('Ext.data.JsonStore', {
        model: 'modelResumenFacturas',
        autoLoad: true,
        timeout: 90000,
        proxy: {
            type: 'ajax',
            url: url_getTotalResumenFactElectronicas,
            reader: {
                type: 'json',
                root: 'arraResult'
            },
            extraParams: {dateFechaDesde: '', dateFechaHasta: '', intIdOficina: '', strTipoDocumento: 'NC'},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function (store) {
                storeResumenNostasCredito.getProxy().extraParams.dateFechaDesde = Ext.getCmp('dateFechaDesde').getValue();
                storeResumenNostasCredito.getProxy().extraParams.dateFechaHasta = Ext.getCmp('dateFechaHasta').getValue();
                storeResumenNostasCredito.getProxy().extraParams.intIdOficina   = Ext.getCmp('comboOficinas').getValue();
            },
            load: function (store) {
            }
        }
    });

    var panel = Ext.create('widget.panel', {
        width: 800,
        height: 400,
        title: 'Resumen de Notas de Crédito Electronicas',
        renderTo: 'resumenNCElectronica',
        layout: 'fit',
        items: {
            xtype: 'chart',
            animate: true,
            shadow: true,
            store: storeResumenNostasCredito,
            autoScroll: false,
            legend: {
                position: 'top'
            },
            axes: [{
                    type: 'Numeric',
                    position: 'bottom',
                    fields: ['AUTORIZADAS', 'PROCESANDO', 'RECHAZADAS', 'PENDIENTES ENVIO SRI', 'CON ERRORES', 'ACTUALIZADAS'],
                    title: 'Total de Notas de Crédito',
                    grid: true,
                    label: {
                        renderer: function (v) {
                            return String(v).replace(/000000$/, 'M');
                        }
                    },
                    roundToDecimal: false
                }, {
                    type: 'Category',
                    position: 'left',
                    fields: ['MES'],
                    title: 'Mes'
                }],
            series: [{
                    type: 'bar',
                    axis: 'bottom',
                    gutter: 80,
                    xField: 'MES',
                    yField: ['AUTORIZADAS', 'PROCESANDO', 'RECHAZADAS', 'PENDIENTES ENVIO SRI', 'CON ERRORES', 'ACTUALIZADAS'],
                    stacked: true,
                    highlight: true,
                    label: {
                        renderer: function (storeItem, item) {
                            //this.setTitle(String(item.value[1] / 1000000) + 'M');
                            this.setTitle(String(item.value[1]));
                        }
                    },
                    tips: {
                        trackMouse: true,
                        width: 65,
                        height: 28,
                        renderer: function (storeItem, item) {
                            //this.setTitle(String(item.value[1] / 1000000) + 'M');
                            this.setTitle(String(item.value[1]));
                        }
                    }
                }]
        }
    });

    Ext.create('Ext.grid.Panel', {
        renderTo: 'resumenDetNCElectronica',
        store: storeResumenNostasCredito,
        width: 642,
        height: 200,
        title: 'Resumen de Notas de Crédito Electronicas',
        columns: [
            {
                text: 'Mes',
                width: 80,
                height: 40,
                dataIndex: 'MES'
            },
            {
                text: 'Autorizadas',
                width: 80,
                dataIndex: 'AUTORIZADAS'
            },
            {
                text: 'Procesando',
                width: 80,
                dataIndex: 'PROCESANDO'
            },
            {
                text: 'Rechazadas',
                width: 80,
                dataIndex: 'RECHAZADAS'
            },
            {
                text: 'Pendientes de</br> Envio al SRI',
                width: 80,
                dataIndex: 'PENDIENTES ENVIO SRI'
            },
            {
                text: 'Con Errores',
                width: 80,
                dataIndex: 'CON ERRORES'
            },
            {
                text: 'Actualizadas',
                width: 80,
                dataIndex: 'ACTUALIZADAS'
            },
            {
                text: 'Total',
                width: 80,
                dataIndex: 'TOTAL'
            }
        ]
    });
    /*Fin Notas de Crédito*/

});
/****/
function Buscar() {
    if ((Ext.getCmp('dateFechaDesde').getValue()) && (Ext.getCmp('dateFechaHasta').getValue()))
    {
        if (Ext.getCmp('dateFechaDesde').getValue() > Ext.getCmp('dateFechaHasta').getValue())
        {
            Ext.Msg.show({
                title: 'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });

        }
        else
        {
            storeResumenNostasCredito.getProxy().extraParams.dateFechaDesde = Ext.getCmp('dateFechaDesde').value;
            storeResumenNostasCredito.getProxy().extraParams.dateFechaHasta = Ext.getCmp('dateFechaHasta').value;
            storeResumenNostasCredito.getProxy().extraParams.intIdOficina   = Ext.getCmp('comboOficinas').value;
            storeResumenNostasCredito.load();
        }
    }
    else
    {
        storeResumenNostasCredito.getProxy().extraParams.dateFechaDesde = Ext.getCmp('dateFechaDesde').value;
        storeResumenNostasCredito.getProxy().extraParams.dateFechaHasta = Ext.getCmp('dateFechaHasta').value;
        storeResumenNostasCredito.getProxy().extraParams.intIdOficina   = Ext.getCmp('comboOficinas').value;
        storeResumenNostasCredito.load();
    }
}

function Limpiar() {
    Ext.getCmp('dateFechaDesde').setValue('');
    Ext.getCmp('dateFechaHasta').setValue('');
    Ext.getCmp('comboOficinas').value = '';
    Ext.getCmp('comboOficinas').setRawValue('');
}