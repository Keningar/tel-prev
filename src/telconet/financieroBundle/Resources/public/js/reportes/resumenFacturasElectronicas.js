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

    /*Inicio Facturas*/
    storeResumenFacturas = Ext.create('Ext.data.JsonStore', {
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
            extraParams: {dateFechaDesde: '', dateFechaHasta: '', intIdOficina: '', strTipoDocumento: 'FAC'},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function (store) {
                storeResumenFacturas.getProxy().extraParams.dateFechaDesde = Ext.getCmp('dateFechaDesde').getValue();
                storeResumenFacturas.getProxy().extraParams.dateFechaHasta = Ext.getCmp('dateFechaHasta').getValue();
                storeResumenFacturas.getProxy().extraParams.intIdOficina = Ext.getCmp('comboOficinas').getValue();
            },
            load: function (store) {
            }
        }
    });

    var panel = Ext.create('widget.panel', {
        width: 800,
        height: 400,
        title: 'Resumen Facturación Electronica',
        renderTo: 'resumenFACElectronica',
        layout: 'fit',
        items: {
            xtype: 'chart',
            animate: true,
            shadow: true,
            store: storeResumenFacturas,
            autoScroll: false,
            legend: {
                position: 'top'
            },
            axes: [{
                    type: 'Numeric',
                    position: 'bottom',
                    fields: ['AUTORIZADAS', 'PROCESANDO', 'RECHAZADAS', 'PENDIENTES ENVIO SRI', 'CON ERRORES', 'ACTUALIZADAS'],
                    title: 'Total de Facturas',
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
        renderTo: 'resumenDetFACElectronica',
        store: storeResumenFacturas,
        width: 642,
        height: 200,
        title: 'Resumen Facturación Electronica',
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
    /*Fin Facturas*/
    
    /*Inicio Documentos No Creados*/
    Ext.define('modelDocumentosNoCreados', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'LOGIN', type: 'string'},
            {name: 'NUMERO_FACTURA_SRI', type: 'string'},
            {name: 'VALOR_TOTAL', type: 'string'},
            {name: 'ESTADO_IMPRESION_FACT', type: 'string'},
            {name: 'FE_CREACION', type: 'string'},
            {name: 'FE_EMISION', type: 'string'},
            {name: 'USR_CREACION', type: 'string'},
            {name: 'RECURRENTE', type: 'string'},
            {name: 'NOMBRE_TIPO_DOCUMENTO', type: 'string'}
        ]
    });

    storeDocumentosNoCreados = Ext.create('Ext.data.JsonStore', {
        model: 'modelDocumentosNoCreados',
        autoLoad: true,
        timeout: 90000,
        proxy: {
            type: 'ajax',
            url: url_getDocumentosNoCreados,
            reader: {
                type: 'json',
                root: 'arrayComprobantesNoCreados'
            },
            simpleSortMode: true
        }
    });
    
    Ext.create('Ext.grid.Panel', {
        renderTo: 'documentosNoCreados',
        store: storeDocumentosNoCreados,
        width: 820,
        height: 200,
        title: 'Documentos Financieros No Creados',
        listeners: {
            itemdblclick: function (view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        },
        columns: [
            {
                text: 'Login',
                width: 100,
                height: 40,
                dataIndex: 'LOGIN'
            },
            {
                text: 'Numero de</br>Documento',
                width: 120,
                dataIndex: 'NUMERO_FACTURA_SRI'
            },
            {
                text: 'Valor Total',
                width: 80,
                dataIndex: 'VALOR_TOTAL'
            },
            {
                text: 'Estado</br>Documento',
                width: 80,
                dataIndex: 'ESTADO_IMPRESION_FACT'
            },
            {
                text: 'Fecha de</br> Creación',
                width: 80,
                dataIndex: 'FE_CREACION'
            },
            {
                text: 'Fecha de</br> Emisión',
                width: 80,
                dataIndex: 'FE_EMISION'
            },
            {
                text: 'Usuario</br> Creación',
                width: 80,
                dataIndex: 'USR_CREACION'
            },
            {
                text: 'Recurrente',
                width: 80,
                dataIndex: 'RECURRENTE'
            },
            {
                text: 'Tipo Documento',
                width: 100,
                dataIndex: 'NOMBRE_TIPO_DOCUMENTO'
            }
        ]
    });
    /*Fin Documentos No Creados*/
    
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
            storeResumenFacturas.getProxy().extraParams.dateFechaDesde = Ext.getCmp('dateFechaDesde').value;
            storeResumenFacturas.getProxy().extraParams.dateFechaHasta = Ext.getCmp('dateFechaHasta').value;
            storeResumenFacturas.getProxy().extraParams.intIdOficina = Ext.getCmp('comboOficinas').value;
            storeResumenFacturas.load();
        }
    }
    else
    {
        storeResumenFacturas.getProxy().extraParams.dateFechaDesde = Ext.getCmp('dateFechaDesde').value;
        storeResumenFacturas.getProxy().extraParams.dateFechaHasta = Ext.getCmp('dateFechaHasta').value;
        storeResumenFacturas.getProxy().extraParams.intIdOficina = Ext.getCmp('comboOficinas').value;
        storeResumenFacturas.load();
    }
}

function Limpiar() {
    Ext.getCmp('dateFechaDesde').setValue('');
    Ext.getCmp('dateFechaHasta').setValue('');
    Ext.getCmp('comboOficinas').value = '';
    Ext.getCmp('comboOficinas').setRawValue('');
}