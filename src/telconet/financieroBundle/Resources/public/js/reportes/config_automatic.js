
Ext.onReady(function() {

    storeFormasPago = new Ext.data.Store({
        total: 'intTotal',
        proxy: {
            type: 'ajax',
            url : 'getFormasPagoMulti',
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'objFormaPago'
            }
        },
        autoLoad: true,
        fields:
                [
                        {name:'intValue', mapping:'intValue'},
                        {name:'strDescripcionFormaPago', mapping:'strDescripcionFormaPago'}
                ]
    });

    storeEstadoPunto = new Ext.data.Store({
        total: 'intTotal',
        proxy: {
            type: 'ajax',
            url : 'getEstadoPunto',
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'objEstadoPunto'
            }
        },
        autoLoad: true,
        fields:
                [
                        {name:'intValue', mapping:'intValue'},
                        {name:'strDescripcionEstadoPunto', mapping:'strDescripcionEstadoPunto'}
                ]
    });

    storeFormasPago.proxy.limitParam=null;

    storeEstadoPago = new Ext.data.Store({
        total: 'intTotal',
        proxy: {
            type: 'ajax',
            url : 'getEstadoPago',
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'objEstadoPago'
            }
        },
        autoLoad: true,
        fields:
                [
                        {name:'intValue', mapping:'intValue'},
                        {name:'strEstado', mapping:'strDescripcionEstadoPago'}
                ]
    });

    //Define el contador y multi selector de combos
    Ext.define('comboSelectedCount', {
        alias: 'plugin.selectedCount',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var len = records.length,
                        store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.intValue === 'ALL') {
                            allRecord = records[i];
                            if (!combo.allSelected) {
                                len = store.getCount();
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                all = true;
                                newAll = true;
                            } else {
                                all = true;
                            }
                        } else {
                            if (diff && !newAll)
                                newRecords.push(records[i]);
                        }
                    });
                    if (combo.allSelected && !all) {
                        combo.clearValue();
                        combo.allSelected = false;
                    } else  if (diff && !newAll) {
                        combo.select(newRecords);
                        combo.allSelected = false;
                    }
                },
            })
        }
    });

    // Crea el combo multi selccion cboEstadoPago
    cboEstadoPago = Ext.create('Ext.form.ComboBox', {
        disabled: false,
        id: 'cboEstadoPago',
        plugins: ['selectedCount'],
        fieldLabel: 'Estado del Pago',
        store: storeEstadoPago,
        queryMode: 'local',
        editable: false,
        displayField: 'strEstado',
        valueField: 'intValue',
        multiSelect: true,
        width: 360,
        displayTpl: '<tpl for="."> {strEstado} <tpl if="xindex < xcount">, </tpl> </tpl>',
        listConfig: {
            itemTpl: '{strEstado} <div class="uncheckedChkbox"></div>'
        },
        listeners: {
            scope: this,
            afterRender: function(me){
                var arrayCombo=strEstadoPago.split(',');
                me.setValue(arrayCombo);
            }
        }
    });

    // Crea el combo multi selccion cboFormaPago
    cboFormaPago = Ext.create('Ext.form.ComboBox', {
        id: 'cboFormaPago',
        fieldLabel: 'Forma de Pago',
        typeAhead: true,
        plugins: ['selectedCount'],
        triggerAction: 'all',
        displayField:'strDescripcionFormaPago',
        valueField: 'intValue',
        selectOnTab: true,
        editable: false,
        disabled: false,
        store: storeFormasPago,
        width: 360,
        multiSelect: true,
        displayTpl: '<tpl for="."> {strDescripcionFormaPago} <tpl if="xindex < xcount">, </tpl> </tpl>',
        listConfig: {
            itemTpl: '{strDescripcionFormaPago} <div class="uncheckedChkbox"></div>'
        },
        listeners: {
            scope: this,
            afterRender: function(me){
                if (strFormaPago != 'ALL' && strFormaPago != '')
                {
                    var arrayCombo=strFormaPago.split(',').map(Number);
                    me.setValue(arrayCombo);
                }
                else
                {
                    me.setValue(strFormaPago);
                }
            }
        }
    });

    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanelFinanciero = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        buttonAlign: 'center',
        layout:{
            type:'table',
            columns: 1,
            align: 'left'
        },
        border: false,
        bodyStyle: {
                       background: '#fff'
                   },
        width: 950,
        height: 400,
        title: 'Criterios Financiero',
                header: false,
                items:
                [
                        {
                                xtype:'fieldset',
                                width: 900,
                                columnWidth: 0.5,
                                title: 'General',
                                collapsible: false,
                                layout:{
                                        type:'table',
                                        columns: 5,
                                        align: 'left'
                                },
                                items :
                                [
                                        {html:"&nbsp;",border:false,width:50},
                                        {
                                                xtype: 'combobox',
                                                id: 'strTipoDocumento',
                                                fieldLabel: 'Tipo de Documento',
                                                typeAhead: true,
                                                triggerAction: 'all',
                                                displayField:'nombre_tipo_documento',
                                                valueField: 'codigo_tipo_documento',
                                                selectOnTab: true,
                                                editable: false,
                                                value: 'PAG',
                                                store: [
                                                      ['PAG','Pago']
                                                ],
                                                width: 360
                                        },
                                        {html:"&nbsp;",border:false,width:80},
                                        {html:"&nbsp;",border:false,width:360},
                                        {html:"&nbsp;",border:false,width:50}
                                ]
                        },
                        {
                                xtype:'fieldset',
                                id: 'fieldsetPago',
                                width: 900,
                                columnWidth: 0.5,
                                title: 'Pago',
                                collapsible: false,
                                collapsed: false,
                                layout:{
                                        type:'table',
                                        columns: 5,
                                        align: 'left'
                                },
                                items :
                                [
                                        {html:"&nbsp;",border:false,width:50},
                                        {
                                                xtype: 'combobox',
                                                fieldLabel: 'Estado del Punto',
                                                id: 'strEstPunto',
                                                editable: false,
                                                store: storeEstadoPunto,
                                                displayField:'strDescripcionEstadoPunto',
                                                valueField: 'intValue',
                                                width: 360,
                                                listeners: {
                                                    scope: this,
                                                    afterRender: function(me){
                                                        me.setValue(strEstadoPunto);   
                                                    }
                                                }
                                        },
                                        {html:"&nbsp;",border:false,width:80},
                                        cboEstadoPago,
                                        {html:"&nbsp;",border:false,width:50},
                                        {html:"&nbsp;",border:false,width:50},
                                        cboFormaPago
                                ]
                        }
                ],
        renderTo: 'filtro_financiero'
    });
});

function guardarParametrosReportes()
{
    var strTipoDocumento = "";
    var strEstadoPunto   = "";
    var strEstadoPago    = "";
    var strFormaPago     = "";

    strTipoDocumento = Ext.getCmp('strTipoDocumento').getValue();
    if (strTipoDocumento != 'PAG')
    {
        Ext.Msg.alert('Alerta ','Solo puede seleccionar el tipo de documento Pago');
        return false;
    }

    strEstadoPunto = Ext.getCmp('strEstPunto').getValue();
    if (!strEstadoPunto)
    {
        strEstadoPunto="ALL";
    }

    strEstadoPago = Ext.getCmp('cboEstadoPago').getValue().toString();
    if (strEstadoPago.indexOf('ALL') >= 0 || !strEstadoPago)
    {
        strEstadoPago="ALL";
    }
    
    strFormaPago = Ext.getCmp('cboFormaPago').getValue().toString();
    if (strFormaPago.indexOf('ALL') >= 0 || !strFormaPago)
    {
        strFormaPago="ALL";
    }

    Ext.MessageBox.wait('Guardando Datos. Favor espere..');
    Ext.Ajax.request(
    {
        timeout: 900000,
        url: urlGuardaParamReport,
        params:
        {
            strTipoDocumento: strTipoDocumento,
            strEstadoPunto: strEstadoPunto,
            strEstadoPago: strEstadoPago,
            strFormaPago: strFormaPago
        },
        method: 'get',
        success: function(response)
        {
            Ext.Msg.alert('Mensaje', response.responseText);
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error al guardar los datos: ' + result.statusText);
        }
    });
}