/******************
 * @author jlafuente
 * @since 2014-01-01
 * @date 2014-02-11
 */
var fechaCreacionDocBusqueda;
var tiposDocumentosBusqueda;
var numDocsAbiertosBusqueda;
var valorMontoCarteraBusqueda;
var idTipoNegocioBusqueda;
var valorClienteCanalBusqueda;
var nombreUltimaMillaBusqueda;
var idCicloFacturacionBusqueda;
var idsOficinasBusqueda;
var idsFormasPagoBusqueda;
var valorCuentaTarjetaBusqueda;
var idsTiposCuentaTarjetaBusqueda;
var idsBancosBusqueda;

var fechaLimActivacion;
var nombreArchivoAdjunto;
var identificacionesExcluidas;
var jsonIdentificaciones;

var opcionesCuentaTarjetaBancosVisible  = false;
var ejecutaSelectAllFormaPago           = false;
var permiteConsultarCuentaTarjeta       = true;
var ejecutaSelectAllCuentaTarjeta       = false;
var permiteConsultarTiposCuentaTarjeta  = true;
var ejecutaSelectAllTipoCuentaTarjeta   = false;
var permiteConsultarBancos              = true;
var consultaEnEjecucionClientesCorte    = false;
var muestraMessageConsultaClientesCorte = true;
Ext.onReady(function() {
    itemsOficina            = [];
    itemsFormaPago          = [];
    itemsTipoCuentaTarjeta  = [];
    itemsBanco              = [];
    var storeTipoNegocio = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetTiposNegocio,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
            [
                {name: 'idTipoNegocio', mapping: 'idTipoNegocio'},
                {name: 'nombreTipoNegocio', mapping: 'nombreTipoNegocio'}
            ],
        autoLoad: true
    });

    Ext.define('Ext.form.field.Month', {
        extend: 'Ext.form.field.Date',
        alias: 'widget.monthfield',
        requires: ['Ext.picker.Month'],
        alternateClassName: ['Ext.form.MonthField', 'Ext.form.Month'],
        selectMonth: null,
        createPicker: function () {
            var me = this,
                format = Ext.String.format;
            return Ext.create('Ext.picker.Month', {
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
                listeners: {
                    select: {
                        scope: me,
                        fn: me.onSelect
                    },
                    monthdblclick: {
                        scope: me,
                        fn: me.onOKClick
                    },
                    yeardblclick: {
                        scope: me,
                        fn: me.onOKClick
                    },
                    OkClick: {
                        scope: me,
                        fn: me.onOKClick
                    },
                    CancelClick: {
                        scope: me,
                        fn: me.onCancelClick
                    }
                },
                keyNavConfig: {
                    esc: function () {
                        me.collapse();
                    }
                }
            });
        },
        onCancelClick: function () {
            var me = this;
            me.selectMonth = null;
            me.collapse();
        },
        onOKClick: function () {
            var me = this;
            if (me.selectMonth) {
                me.setValue(me.selectMonth);
                me.fireEvent('select', me, me.selectMonth);
            }
            me.collapse();
        },
        onSelect: function (m, d) {
            var me = this;
            me.selectMonth = new Date((d[0] + 1) + '/2/' + d[1]);
        }
    });

    var storeUltimaMilla = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetUltimaMilla,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
            [
                {name: 'nombreUltimaMilla', mapping: 'nombreUltimaMilla'}
            ],
        autoLoad: true
    });

    var storeCiclosFacturacion = new Ext.data.Store({
        pageSize: 10,
        total: 'intTotal',
        proxy: {
            type: 'ajax',
            url: strUrlGetCiclos,
            reader: {
                type: 'json',
                totalProperty: 'intTotal',
                root: 'arrayRegistros'
            }
        },
        fields:
            [
                {name: 'intIdCiclo', mapping: 'intIdCiclo'},
                {name: 'strNombreCiclo', mapping: 'strNombreCiclo'}
            ],
        autoLoad: true
    });

    storeTipoCuentaTarjeta = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetTipoCuentaTarjeta,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields: [
            {
                name: 'intIdTipoCuenta',
                mapping: 'intIdTipoCuenta'
            },
            {
                name: 'strDescripcionCuenta',
                mapping: 'strDescripcionCuenta'
            }],
        listeners: {
			beforeload: function(t, records, options){
                Ext.MessageBox.show({
                    title: 'Favor espere',
                    msg: 'Cargando Tipos de Cuenta/Tarjeta...',
                    closable: false,
                    progressText: 'Cargando...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 500
                    }
                });
			},
            load: function(t, records, options) {
                frameTipoCuentaTarjeta.removeAll();
                Ext.getCmp('panelTipoCuentaTarjeta').setVisible(false);
                if (records.length > 0)
                {
                    if (records[0].data.strDescripcionCuenta != "")
                    {
                        for (var contadorTipoCuentaTarjeta= 0; contadorTipoCuentaTarjeta < records.length; contadorTipoCuentaTarjeta++)
                        {
                            var cb = Ext.create('Ext.form.field.Checkbox',
                                {
                                    boxLabel: records[contadorTipoCuentaTarjeta].data.strDescripcionCuenta,
                                    inputValue: records[contadorTipoCuentaTarjeta].data.intIdTipoCuenta,
                                    id: 'idTipoCuentaTarjeta_' + contadorTipoCuentaTarjeta,
                                    name: 'idTipoCuentaTarjeta'
                                });
                            frameTipoCuentaTarjeta.add(cb);
                            itemsTipoCuentaTarjeta[contadorTipoCuentaTarjeta] = cb;
                        }
                        Ext.getCmp('panelTipoCuentaTarjeta').setVisible(true);
                        if(ejecutaSelectAllCuentaTarjeta)
                        {
                            ejecutaFuncionSelectAllTipoCuentaTarjeta();
                        }
                        else
                        {
                            Ext.MessageBox.hide();
                        }
                    }
                    else
                    {
                        Ext.MessageBox.hide();
                    }
                }
                else
                {
                    Ext.getCmp('panelTipoCuentaTarjeta').setVisible(false);
                    Ext.MessageBox.hide();
                }
            }
        }
    });
    
    storeBancos = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetBancos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields: [
            {
                name: 'intIdBanco',
                mapping: 'intIdBanco'
            },
            {
                name: 'strDescripcionBanco',
                mapping: 'strDescripcionBanco'
            }],
        listeners: {
			beforeload: function(t, records, options){
                Ext.MessageBox.show({
                    title: 'Favor espere',
                    msg: 'Cargando Bancos...',
                    closable: false,
                    progressText: 'Cargando...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 500
                    }
                });
			},
            load: function(t, records, options) {
                frameBanco.removeAll();
                Ext.getCmp('panelBanco').setVisible(false);
                if (records.length > 0)
                {
                    if (records[0].data.strDescripcionBanco != "")
                    {
                        for (var contadorBancos = 0; contadorBancos < records.length; contadorBancos++)
                        {
                            var cb = Ext.create('Ext.form.field.Checkbox',
                                {
                                    boxLabel: records[contadorBancos].data.strDescripcionBanco,
                                    inputValue: records[contadorBancos].data.intIdBanco,
                                    id: 'idBanco_' + contadorBancos,
                                    name: 'banco'
                                });
                            frameBanco.add(cb);
                            itemsBanco[contadorBancos] = cb;
                        } 
                        Ext.getCmp('panelBanco').setVisible(true);
                        if(ejecutaSelectAllTipoCuentaTarjeta)
                        {
                            Ext.each(itemsBanco, function(record){
                                record.setValue(true);
                                record.setDisabled(true);
                            });
                            ejecutaSelectAllTipoCuentaTarjeta   = false;
                            ejecutaSelectAllCuentaTarjeta       = false;
                            ejecutaSelectAllFormaPago           = false;
                        }
                    }
                }
                else
                {
                    Ext.getCmp('panelBanco').setVisible(false);
                }
                Ext.MessageBox.hide();
            }
        }
    });

    Ext.define('comboSelectedTipoDocumento', {
        alias: 'plugin.selectedTipoDocumento',
        init: function (combo) {
            combo.on({
                select: function (me, records) {
                    var store = combo.getStore(),
                        diff = records.length != store.count,
                        newAll = false,
                        all = false,
                        newRecords = [];
                    Ext.each(records, function (obj, i, recordsItself) {
                        if (records[i].data.nombreOpcionTipoDocumento === 'TODAS') {
                            all = true;
                            if (!combo.allSelected) {
                                combo.select(store.getRange());
                                combo.allSelected = true;
                                newAll = true;
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
                }
            })
        }
    });
    
    storeTipoDocumento = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: strUrlGetParametrosAsociadosAServiciosCorte,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                valor2Param: 'TIPOS_DE_DOCUMENTOS'
            }
        },
        fields: [
            {
                name: 'nombreOpcionTipoDocumento',
                mapping: 'valor3Param'
            },
            {
                name: 'valorOpcionTipoDocumento',
                mapping: 'valor4Param'
            }],
        autoLoad: true
    });
    
    var cboTipoDocumento = Ext.create('Ext.form.ComboBox', {
        id: 'cmbTipoDocumento',
        plugins: ['selectedTipoDocumento'],
        fieldLabel: 'Tipo de Documento: *',
        labelStyle: 'text-align:left;',
        labelSeparator : "",
        store: storeTipoDocumento,
        queryMode: 'local',
        displayField: 'nombreOpcionTipoDocumento',
        valueField: 'valorOpcionTipoDocumento',
        editable: false,
        multiSelect: true,
        displayTpl: '<tpl for="."> {nombreOpcionTipoDocumento} <tpl if="xindex < xcount">, </tpl> </tpl>',
        listConfig: {
                        itemTpl: '{nombreOpcionTipoDocumento} <div class="uncheckedChkbox"></div>'
                    }
    });
    
    var cboCiclosFacturacion = Ext.create('Ext.form.ComboBox', {
        xtype: 'combo',
        fieldLabel: 'Ciclo de Facturaci\xf3n: *',
        labelSeparator : "",
        id: 'cmbCicloFacturacion',
        name: 'cmbCicloFacturacion',
        displayField: 'strNombreCiclo',
        valueField: 'intIdCiclo',
        emptyText: 'Seleccione...',
        labelStyle: 'text-align:left;',
        multiSelect: false,
        queryMode: 'local',
        store: storeCiclosFacturacion
    });

    var frameTipoCuentaTarjeta = new Ext.form.CheckboxGroup({
        id: 'frameTipoCuentaTarjeta',
        flex: 4,
        vertical: true,
        align: 'left',
        columns: 1,
        listeners: {
            change: function (field, newValue, oldValue) {
                if(newValue.idTipoCuentaTarjeta)
                {
                    if(permiteConsultarBancos)
                    {
                        var valorCuentaTarjetaSelected = '';
                        if (Ext.getCmp('valorCuentaTarjeta_1').value == true && Ext.getCmp('valorCuentaTarjeta_2').value == true)
                        {
                            valorCuentaTarjetaSelected = Ext.getCmp('valorCuentaTarjeta_1').inputValue + "," 
                                                            + Ext.getCmp('valorCuentaTarjeta_2').inputValue;
                        }
                        else if(Ext.getCmp('valorCuentaTarjeta_1').value == true)
                        {
                            valorCuentaTarjetaSelected = Ext.getCmp('valorCuentaTarjeta_1').inputValue;
                        }
                        else if(Ext.getCmp('valorCuentaTarjeta_2').value == true)
                        {
                            valorCuentaTarjetaSelected = Ext.getCmp('valorCuentaTarjeta_2').inputValue;
                        }

                        var idTipoCuentaSelected = "";
                        for (var i = 0; i < itemsTipoCuentaTarjeta.length; i++)
                        {
                            if (Ext.getCmp('idTipoCuentaTarjeta_' + i).value == true)
                            {
                                if (i > 0)
                                {
                                    idTipoCuentaSelected = idTipoCuentaSelected + ',';
                                }
                                idTipoCuentaSelected = idTipoCuentaSelected + Ext.getCmp('idTipoCuentaTarjeta_' + i).inputValue;
                            }
                        }
                        itemsBanco = [];
                        storeBancos.getProxy().extraParams.idTipoCuentaSelected         = idTipoCuentaSelected;
                        storeBancos.getProxy().extraParams.valorCuentaTarjetaSelected   = valorCuentaTarjetaSelected;
                        storeBancos.getProxy().extraParams.procesoEjecutante            = 'CORTE_MASIVO';
                        storeBancos.removeAll();
                        storeBancos.load();
                    }
                }
                else
                {
                    Ext.each(itemsBanco, function(record){
                        record.setValue(false);
                    });
                    itemsBanco = [];
                    Ext.getCmp('panelBanco').setVisible(false);
                }
            }
        }
    });

    var frameBanco = new Ext.form.CheckboxGroup({
        id: 'frameBanco',
        flex: 3,
        vertical: true,
        align: 'left',
        columns: 2
    });

    
    Ext.Ajax.request({
        url: strUrlGetValidarDisponibilidadOpcionPorHora,
        method: 'post',
        params: {
            opcionTelcos: 'CORTES_MASIVOS_MD'
        },
        success: function (response)
        {
            var respuestaPermiteAcceso = false;
            var json = Ext.JSON.decode(response.responseText);
            
            if (json.strPermiteAcceso == "SI")
            {
                respuestaPermiteAcceso = true;
            }
            
            Ext.Ajax.request({
                url: strUrlGetParametrosAsociadosAServiciosCorte,
                method: 'post',
                params: {
                    valor2Param: 'NUM_LOGINES_POR_LOTE'
                },
                success: function (response)
                {
                    var jsonNumeroLoginesPorLote = Ext.JSON.decode(response.responseText);
                    var intNumeroLoginesPorLote = jsonNumeroLoginesPorLote.encontrados[0].valor3Param;
                    storePuntosACortar = new Ext.data.Store({
                        pageSize: intNumeroLoginesPorLote,
                        total: 'intTotal',
                        proxy: {
                            type: 'ajax',
                            timeout: 2700000,
                            url: strUrlGetPuntosACortar,
                            actionMethods: {
                                read: 'POST'
                            },
                            paramsAsJson: true,
                            reader: {
                                type: 'json',
                                totalProperty: 'intTotal',
                                root: 'arrayResultado'
                            },

                            extraParams: {
                                fechaCreacionDocBusqueda : '',
                                tiposDocumentosBusqueda : '',
                                numDocsAbiertosBusqueda : '',
                                valorMontoCarteraBusqueda : '',
                                idTipoNegocioBusqueda : '',
                                valorClienteCanalBusqueda : '',
                                nombreUltimaMillaBusqueda : '',
                                idCicloFacturacionBusqueda : '',
                                idsOficinasBusqueda : '',
                                idsFormasPagoBusqueda : '',
                                valorCuentaTarjetaBusqueda : '',
                                idsTiposCuentaTarjetaBusqueda : '',
                                idsBancosBusqueda : '',
                                permiteConsultar: '',
                                fechaLimActivacion: '',
                                identificacionesExcluidas: ''
                            }
                        },
                        listeners: {
                            beforeload: function(t, records, options){
                                if(muestraMessageConsultaClientesCorte)
                                {
                                    Ext.MessageBox.show({
                                        title: 'Favor espere',
                                        msg: 'Generando clientes de corte masivo...',
                                        closable: false,
                                        progressText: 'Consultando...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {
                                            interval: 500
                                        }
                                    });
                                }
                                consultaEnEjecucionClientesCorte = true;
                            },
                            load: function (store, records, success) {
                                consultaEnEjecucionClientesCorte = false;
                                if(muestraMessageConsultaClientesCorte)
                                {
                                    Ext.MessageBox.hide();
                                }
                            }
                        },
                        fields: [
                            {
                                name: 'idPuntoCorteMasivo',
                                mapping: 'idPuntoCorteMasivo'
                            },
                            {
                                name: 'loginCorteMasivo',
                                mapping: 'loginCorteMasivo'
                            },
                            {
                                name: 'nombreClienteCorteMasivo',
                                mapping: 'nombreClienteCorteMasivo'
                            },
                            {
                                name: 'nombreOficinaCorteMasivo',
                                mapping: 'nombreOficinaCorteMasivo'
                            },
                            {
                                name: 'saldoCorteMasivo',
                                mapping: 'saldoCorteMasivo'
                            },
                            {
                                name: 'descripcionFormaPagoCorteMasivo',
                                mapping: 'descripcionFormaPagoCorteMasivo'
                            },
                            {
                                name: 'descripcionBancoCorteMasivo',
                                mapping: 'descripcionBancoCorteMasivo'
                            },
                            {
                                name: 'descripcionCuentaCorteMasivo',
                                mapping: 'descripcionCuentaCorteMasivo'
                            },
                            {
                                name: 'nombreTipoNegocioCorteMasivo',
                                mapping: 'nombreTipoNegocioCorteMasivo'
                            },
                            {
                                name: 'nombreUltimaMillaCorteMasivo',
                                mapping: 'nombreUltimaMillaCorteMasivo'
                            },
                            {
                                name: 'fechaActivacionCorteMasivo',
                                mapping: 'fechaActivacionCorteMasivo'
                            }
                        ]
                    });

                    Ext.Ajax.request({
                        url: strUrlGetOficinasYFormasPago,
                        method: 'post',
                        params: {
                            procesoEjecutante: 'CORTE_MASIVO'
                        },
                        success: function (response) {
                            var variable = response.responseText.split("&");
                            var oficinasGrupo = variable[0];
                            var formasPago = variable[1];
                            var r = Ext.JSON.decode(oficinasGrupo);
                            for (var i = 0; i < r.total; i++) {
                                var linea = r.encontrados[i].nombreOficina;
                                var idLinea = r.encontrados[i].idOficina;
                                itemsOficina[i] = new Ext.form.Checkbox({
                                    boxLabel: linea,
                                    id: 'idOficina_' + i,
                                    name: 'oficina',
                                    inputValue: idLinea
                                });
                            }

                            var form = Ext.JSON.decode(formasPago);
                            for (var contFormasPagoRequest = 0; contFormasPagoRequest < form.total; contFormasPagoRequest++) {
                                var idFormaPago = form.encontrados[contFormasPagoRequest].idFormaPago;
                                var descripcionFormaPago = form.encontrados[contFormasPagoRequest].descripcionFormaPago;
                                itemsFormaPago[contFormasPagoRequest] = new Ext.form.Checkbox({
                                    boxLabel: descripcionFormaPago,
                                    id: 'idFormaPago_' + contFormasPagoRequest,
                                    name: 'idFormaPago',
                                    inputValue: idFormaPago
                                });
                            }

                            var formResumenPrevio = Ext.create('Ext.form.Panel', {
                                id:'formResumenPrevio',
                                bodyPadding: 4,
                                border:false,
                                width:250,
                                height:140,
                                waitMsgTarget: true,
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    labelWidth: 50,
                                    msgTarget: 'side'
                                },
                                style: {
                                    'display': 'none',
                                    'margin': 'auto'
                                },
                                items:[
                                    {
                                        xtype: 'fieldset',
                                        title: 'Resumen Previo',
                                        defaultType: 'textfield',
                                        border: true,
                                        items:
                                        [
                                            {
                                                xtype: 'container',
                                                layout: 'vbox',
                                                width: '100%',
                                                style: "margin:0 auto;",
                                                items: [
                                                    {
                                                        xtype: 'label',
                                                        id:'numClientesFCRecurrente',
                                                        style: 'font-weight:bold;font-size:14px;'
                                                    },
                                                    {
                                                        xtype: 'label',
                                                        id:'numClientesFCNoRecurrente',
                                                        style: 'font-weight:bold;font-size:14px;'
                                                    },
                                                    {
                                                        xtype: 'label',
                                                        id:'numClientesNDI',
                                                        style: 'font-weight:bold;font-size:14px;'
                                                    },
                                                    {
                                                        xtype: 'image',
                                                        src: '/public/images/images_crud/ajax-loader.gif',
                                                        id: 'imgLoadResumenPrevio',
                                                        name: 'imgLoadResumenPrevio',
                                                        width: 32,
                                                        height: 32,
                                                        style: "margin-left:80%;display:none;"
                                                    }
                                                ]
                                            }
                                        ]
                                    }
                                ]
                            });

                            var panelSuperior = Ext.create('Ext.panel.Panel', {
                                bodyPadding: 7,
                                border:false,
                                buttonAlign: 'center',
                                layout:{
                                    type:'table',
                                    columns: 5,
                                    align: 'left'
                                },
                                items: [
                                    {
                                        xtype: 'container',
                                        id: 'contenedor',
                                        name: 'contenedor',
                                        layout: 'vbox',
                                        items: [
                                            {
                                                xtype: 'monthfield',
                                                format: 'M/Y',
                                                id: 'instaladosHasta',
                                                name: 'instaladosHasta',
                                                fieldLabel: 'Instalados Hasta:',
                                                labelStyle: 'text-align:left;',
                                                labelSeparator : ""
                                            },
                                            {
                                                xtype: 'filefield',
                                                fieldLabel: 'Excluir Comportamiento Pago:',
                                                id: 'importXls',
                                                name: 'importXls',
                                                labelStyle: 'text-align:left;',
                                                labelSeparator : "",
                                                msgTarget: 'side',
                                                anchor: '100%',
                                                buttonConfig: {
                                                    text: 'Examinar',
                                                    iconCls: 'icon_upload'
                                                },
                                                regex: /(.)+((\.xls)|(\.xlsx)(\w)?)$/i,
                                                regexText: 'Sólo se permite formatos XLSX o XLS'
                                            },
                                            {
                                                xtype: 'datefield',
                                                format: 'd/m/Y',
                                                id: 'fechaCreacionDoc',
                                                name: 'fechaCreacionDoc',
                                                fieldLabel: 'Fecha Creaci\xf3n Doc.: *',
                                                labelStyle: 'text-align:left;',
                                                labelSeparator : ""
                                            },
                                            cboTipoDocumento,
                                            {
                                                xtype: 'numberfield',
                                                fieldLabel: 'Docs. Abiertos',
                                                id: 'docsAbiertos',
                                                name: 'docsAbiertos',
                                                minValue: 1,
                                                maxValue: 10,
                                                allowDecimals: false,
                                                decimalPrecision: 2,
                                                step: 1,
                                                emptyText: 'Rango (1-10)',
                                                labelStyle: 'text-align:left;'
                                            },
                                            {
                                                xtype: 'numberfield',
                                                hideTrigger: true,
                                                fieldLabel: 'Monto Cartera',
                                                id: 'montoCartera',
                                                name: 'montoCartera',
                                                minValue: 5,
                                                maxValue: 10000,
                                                emptyText: 'Rango ($5 - $10.000)',
                                                labelStyle: 'text-align:left;'
                                            },
                                            {
                                                xtype: 'combo',
                                                fieldLabel: 'Tipo Negocio',
                                                id: 'tipoNegocio',
                                                name: 'tipoNegocio',
                                                displayField: 'nombreTipoNegocio',
                                                valueField: 'idTipoNegocio',
                                                emptyText: 'Seleccione...',
                                                labelStyle: 'text-align:left;',
                                                multiSelect: false,
                                                queryMode: 'local',
                                                store: storeTipoNegocio
                                            },
                                            {
                                                xtype: 'combo',
                                                fieldLabel: 'Clientes Canal',
                                                id: 'clienteCanal',
                                                name: 'clienteCanal',
                                                labelStyle: 'text-align:left;',
                                                multiSelect: false,
                                                store: [
                                                    ['Todos', 'Todos'],
                                                    ['S', 'SI'],
                                                    ['N', 'NO']
                                                ]
                                            },
                                            {
                                                xtype: 'combo',
                                                fieldLabel: 'Ultima Milla',
                                                id: 'ultimaMilla',
                                                name: 'ultimaMilla',
                                                displayField: 'nombreUltimaMilla',
                                                valueField: 'nombreUltimaMilla',
                                                emptyText: 'Seleccione...',
                                                labelStyle: 'text-align:left;',
                                                multiSelect: false,
                                                queryMode: 'local',
                                                store: storeUltimaMilla,
                                            },
                                            boolPermisoEmpresas ? cboCiclosFacturacion : {html: "&nbsp;", border: false, width: 50}
                                        ]
                                    },
                                    {html:"&nbsp;",border:false,width:200},
                                    formResumenPrevio,
                                    {html:"&nbsp;",border:false,width:200},
                                    {html:"&nbsp;",border:false,width:200}
                                ]
                            });

                            var panel = Ext.create('Ext.panel.Panel',
                            {
                                bodyPadding: 7,
                                layout: 'anchor',
                                buttonAlign: 'center',
                                collapsible: true,
                                collapsed: false,
                                width: '1280px',
                                title: 'Criterios de B\xfasqueda',
                                buttons: [
                                    {
                                        text: 'Buscar',
                                        iconCls: "icon_search",
                                        handler: function () {                                            
                                            let file = Ext.getCmp('contenedor').down('filefield').el.down('input[type=file]').dom.files[0];
                                            if(file != null){
                                                var reader = new FileReader();
                                                reader.onload = (function(theFile) {
                                                    Ext.MessageBox.show({
                                                        title: 'Favor espere',
                                                        msg: 'Procesando archivo adjunto...',
                                                        progressText: 'Saving...',
                                                        width: 300,
                                                        wait: true,
                                                        closable: false,
                                                        waitConfig: {
                                                            interval: 200
                                                        }
                                                    });
                                                    return function(e) {
                                                        nombreArchivoAdjunto            = Ext.getCmp('importXls').value;
                                                        let extensionAdjunto            = nombreArchivoAdjunto.split(".").pop();
                                                        nombreArchivoAdjunto = nombreArchivoAdjunto.split("\\").pop();

                                                        if(extensionAdjunto != "xlsx" && extensionAdjunto != "xls"){
                                                            Ext.Msg.alert('Alerta', "Favor, la extensión del archivo es incorrecta");
                                                            Ext.getCmp('importXls').reset();
                                                            return;
                                                        }
                                                        else{
                                                            let data = e.target.result;
                                                            let workbook = XLSX.read(data, {type:"binary"});
                                                            workbook.SheetNames.forEach(sheet => {
                                                                let rowObject = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheet]);
                                                                jsonIdentificaciones = JSON.stringify(rowObject);
                                                                identificacionesExcluidas = LZString.compressToBase64(JSON.stringify(rowObject));
                                                            });

                                                            if(jsonIdentificaciones.length <= 2)
                                                            {
                                                                Ext.Msg.alert('Alerta', "Error, el archivo adjunto está vacío");
                                                                Ext.getCmp('importXls').reset();
                                                                return; 
                                                            }
                                                            Ext.MessageBox.hide();
                                                            ejecutaOpcionCorteMasivo('BUSCAR');
                                                        }
                                                    };
                                                })(file);
                                                reader.readAsBinaryString(file);
                                            }
                                            else{
                                                ejecutaOpcionCorteMasivo('BUSCAR');
                                            }
                                        }
                                    },
                                    {
                                        text: 'Limpiar',
                                        iconCls: "icon_limpiar",
                                        handler: function () {
                                            limpiar();
                                        }
                                    }],
                                items: [
                                    panelSuperior,
                                    {
                                        xtype: 'fieldset',
                                        title: 'Oficinas *',
                                        width: 1010,
                                        style: 'text-align:left;',
                                        collapsible: false,
                                        collapsed: false,
                                        items: [
                                            {
                                                xtype: 'checkboxgroup',
                                                columns: 3,
                                                vertical: true,
                                                items: itemsOficina
                                            },
                                            {
                                                xtype: 'panel',
                                                buttonAlign: 'right',
                                                bbar: [
                                                    {
                                                        text: 'Select All',
                                                        handler: function () {
                                                            for (var i = 0; i < itemsOficina.length; i++) {
                                                                Ext.getCmp('idOficina_'+ i).setValue(true);
                                                            }
                                                        }
                                                    },
                                                    '-',
                                                    {
                                                        text: 'Deselect All',
                                                        handler: function () {
                                                            for (var i = 0; i < itemsOficina.length; i++) {
                                                                Ext.getCmp('idOficina_'+ i).setValue(false);
                                                            }
                                                        }
                                                    }]
                                            }]
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',
                                        items: [
                                            {
                                                xtype: 'fieldset',
                                                width: 185,
                                                title: 'Forma Pago',
                                                collapsible: false,
                                                collapsed: false,
                                                items: [
                                                    {
                                                        xtype: 'checkboxgroup',
                                                        columns: 1,
                                                        vertical: true,
                                                        align: 'left',
                                                        items: itemsFormaPago,
                                                        listeners: {
                                                            change: function(field, newValue, oldValue, eOpts) {
                                                                if(newValue.idFormaPago)
                                                                {
                                                                    if(permiteConsultarCuentaTarjeta)
                                                                    {
                                                                        var idsFormasPagoSelected = newValue.idFormaPago;
                                                                        var arrayIdsFormasPagoCuentaTarjetaBancos = 
                                                                            strIdsFormasPagoCuentaTarjetaBancos.split(',').map(function(item) {
                                                                                    return parseInt(item);
                                                                        });

                                                                        if(arrayIdsFormasPagoCuentaTarjetaBancos.length > 0
                                                                           && (arrayIdsFormasPagoCuentaTarjetaBancos.includes(idsFormasPagoSelected)
                                                                               || (Array.isArray(idsFormasPagoSelected)
                                                                                   && (idsFormasPagoSelected.filter(
                                                                                        x => arrayIdsFormasPagoCuentaTarjetaBancos.includes(x))).length > 0)
                                                                              )    
                                                                          )
                                                                        {
                                                                            Ext.getCmp('panelEsCuentaTarjeta').setVisible(true);
                                                                            opcionesCuentaTarjetaBancosVisible = true;

                                                                            if(ejecutaSelectAllFormaPago)
                                                                            {
                                                                                ejecutaFuncionSelectAllCuentaTarjeta();
                                                                            }
                                                                        }
                                                                        else if(opcionesCuentaTarjetaBancosVisible)
                                                                        {
                                                                            permiteConsultarBancos              = false;
                                                                            permiteConsultarTiposCuentaTarjeta  = false;
                                                                            permiteConsultarCuentaTarjeta       = false;
                                                                            Ext.each(itemsBanco, function(record){
                                                                                record.setValue(false);
                                                                            });
                                                                            itemsBanco = [];
                                                                            Ext.each(itemsTipoCuentaTarjeta, function(record){
                                                                                record.setValue(false);
                                                                            });
                                                                            itemsTipoCuentaTarjeta = [];
                                                                            Ext.getCmp('valorCuentaTarjeta_1').setValue(false);
                                                                            Ext.getCmp('valorCuentaTarjeta_1').setDisabled(false);
                                                                            Ext.getCmp('valorCuentaTarjeta_2').setValue(false);
                                                                            Ext.getCmp('valorCuentaTarjeta_2').setDisabled(false);
                                                                            permiteConsultarBancos              = true;
                                                                            permiteConsultarTiposCuentaTarjeta  = true;
                                                                            permiteConsultarCuentaTarjeta       = true;
                                                                            Ext.getCmp('panelBanco').setVisible(false);
                                                                            Ext.getCmp('panelTipoCuentaTarjeta').setVisible(false);
                                                                            Ext.getCmp('panelEsCuentaTarjeta').setVisible(false);
                                                                            opcionesCuentaTarjetaBancosVisible = false;
                                                                        }
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    permiteConsultarBancos              = false;
                                                                    permiteConsultarTiposCuentaTarjeta  = false;
                                                                    permiteConsultarCuentaTarjeta       = false;
                                                                    Ext.each(itemsBanco, function(record){
                                                                        record.setValue(false);
                                                                    });
                                                                    itemsBanco = [];
                                                                    Ext.each(itemsTipoCuentaTarjeta, function(record){
                                                                            record.setValue(false);
                                                                    });
                                                                    itemsTipoCuentaTarjeta  = [];
                                                                    Ext.getCmp('valorCuentaTarjeta_1').setValue(false);
                                                                    Ext.getCmp('valorCuentaTarjeta_1').setDisabled(false);
                                                                    Ext.getCmp('valorCuentaTarjeta_2').setValue(false);
                                                                    Ext.getCmp('valorCuentaTarjeta_2').setDisabled(false);
                                                                    permiteConsultarBancos              = true;
                                                                    permiteConsultarTiposCuentaTarjeta  = true;
                                                                    permiteConsultarCuentaTarjeta       = true;
                                                                    Ext.getCmp('panelBanco').setVisible(false);
                                                                    Ext.getCmp('panelTipoCuentaTarjeta').setVisible(false);
                                                                    Ext.getCmp('panelEsCuentaTarjeta').setVisible(false);

                                                                }
                                                            }
                                                        }
                                                    },
                                                    {
                                                        xtype: 'panel',
                                                        buttonAlign: 'right',
                                                        bbar: [
                                                            {
                                                                text: 'Select All',
                                                                handler: function () {
                                                                    if(itemsFormaPago.length > 0)
                                                                    {
                                                                        permiteConsultarCuentaTarjeta   = false;
                                                                        var numChkFormaPagoTrue         = 0;
                                                                        var posUltimoChkCuentaTarjeta = itemsFormaPago.length - 1;
                                                                        for (var i = 0; i < itemsFormaPago.length; i++)
                                                                        {
                                                                            if(Ext.getCmp('idFormaPago_' + i).value)
                                                                            {
                                                                                numChkFormaPagoTrue = numChkFormaPagoTrue + 1;
                                                                            }
                                                                            Ext.getCmp('idFormaPago_' + i).setValue(true);
                                                                        }

                                                                        if(numChkFormaPagoTrue != itemsFormaPago.length)
                                                                        {
                                                                            Ext.getCmp('idFormaPago_'+posUltimoChkCuentaTarjeta).setValue(false);
                                                                            permiteConsultarCuentaTarjeta   = true;
                                                                            ejecutaSelectAllFormaPago       = true;
                                                                            Ext.getCmp('idFormaPago_'+posUltimoChkCuentaTarjeta).setValue(true);
                                                                        }
                                                                        else
                                                                        {
                                                                            permiteConsultarCuentaTarjeta       = true;
                                                                            ejecutaSelectAllFormaPago           = false;
                                                                            ejecutaSelectAllTipoCuentaTarjeta   = false;
                                                                            ejecutaSelectAllCuentaTarjeta       = false;
                                                                        }
                                                                    }
                                                                }
                                                            },
                                                            '-',
                                                            {
                                                                text: 'Deselect All',
                                                                handler: function () {
                                                                    ejecutaSelectAllFormaPago       = false;
                                                                    permiteConsultarCuentaTarjeta   = false;
                                                                    Ext.each(itemsFormaPago, function(record){
                                                                        record.setValue(false);
                                                                    });
                                                                    permiteConsultarCuentaTarjeta = true;
                                                                }
                                                            }]
                                                    }

                                                ]
                                            },
                                            {
                                                xtype: 'component',
                                                width: 4
                                            },    
                                            {
                                                id: 'panelEsCuentaTarjeta',
                                                name: 'panelEsCuentaTarjeta',
                                                xtype: 'fieldset',
                                                title: 'Cuenta/Tarjeta',
                                                width: 180,
                                                collapsible: false,
                                                collapsed: false,
                                                items: [{
                                                        xtype: 'checkboxgroup',                                            
                                                        fieldLabel: '',
                                                        columns: 1,
                                                        vertical: true,                                            
                                                        items: [
                                                            {
                                                                boxLabel: 'Tarjeta', 
                                                                id: 'valorCuentaTarjeta_1', 
                                                                name: 'valorCuentaTarjeta', 
                                                                inputValue: 'Tarjeta'
                                                            },
                                                            {
                                                                boxLabel: 'Cuenta Bancaria', 
                                                                id: 'valorCuentaTarjeta_2',
                                                                name: 'valorCuentaTarjeta', 
                                                                inputValue: 'Cuenta'
                                                            },
                                                            {
                                                                xtype: 'panel',
                                                                buttonAlign: 'right',
                                                                bbar: [
                                                                    {
                                                                        text: 'Select All',
                                                                        handler: function () {
                                                                            ejecutaFuncionSelectAllCuentaTarjeta();
                                                                        }
                                                                    },
                                                                    '-',
                                                                    {
                                                                        text: 'Deselect All',
                                                                        handler: function () {
                                                                            ejecutaSelectAllCuentaTarjeta       = false;
                                                                            permiteConsultarTiposCuentaTarjeta  = false;
                                                                            Ext.getCmp('valorCuentaTarjeta_1').setValue(false);
                                                                            Ext.getCmp('valorCuentaTarjeta_1').setDisabled(false);
                                                                            Ext.getCmp('valorCuentaTarjeta_2').setValue(false);
                                                                            Ext.getCmp('valorCuentaTarjeta_2').setDisabled(false);
                                                                            permiteConsultarTiposCuentaTarjeta = true;
                                                                        }
                                                                    }]
                                                            }
                                                        ],
                                                        listeners: {
                                                            change: function(field, newValue, oldValue) {
                                                                if(newValue.valorCuentaTarjeta)
                                                                {
                                                                    if(permiteConsultarTiposCuentaTarjeta)
                                                                    {
                                                                        var cuentaTarjetaSelected = newValue.valorCuentaTarjeta;
                                                                        if(Array.isArray(cuentaTarjetaSelected))
                                                                        {
                                                                            cuentaTarjetaSelected = cuentaTarjetaSelected.toString();
                                                                        }
                                                                        Ext.getCmp('panelBanco').setVisible(false);
                                                                        Ext.getCmp('panelTipoCuentaTarjeta').setVisible(true);
                                                                        permiteConsultarBancos              = false;
                                                                        permiteConsultarTiposCuentaTarjeta  = false;
                                                                        Ext.each(itemsBanco, function(record){
                                                                            record.setValue(false);
                                                                        });
                                                                        itemsBanco = [];
                                                                        Ext.each(itemsTipoCuentaTarjeta, function(record){
                                                                            record.setValue(false);
                                                                        });
                                                                        itemsTipoCuentaTarjeta = [];
                                                                        storeTipoCuentaTarjeta.getProxy()
                                                                            .extraParams.strInCuentaTarjeta = cuentaTarjetaSelected;
                                                                        storeTipoCuentaTarjeta.removeAll();
                                                                        storeTipoCuentaTarjeta.load();
                                                                        permiteConsultarBancos              = true;
                                                                        permiteConsultarTiposCuentaTarjeta  = true;
                                                                    }
                                                                }
                                                                else{
                                                                    permiteConsultarBancos              = false;
                                                                    permiteConsultarTiposCuentaTarjeta  = false;
                                                                    Ext.each(itemsBanco, function(record){
                                                                        record.setValue(false);
                                                                    });
                                                                    itemsBanco = [];
                                                                    Ext.each(itemsTipoCuentaTarjeta, function(record){
                                                                            record.setValue(false);
                                                                    });
                                                                    itemsTipoCuentaTarjeta = [];
                                                                    permiteConsultarBancos              = true;
                                                                    permiteConsultarTiposCuentaTarjeta  = true;
                                                                    Ext.getCmp('panelBanco').setVisible(false);
                                                                    Ext.getCmp('panelTipoCuentaTarjeta').setVisible(false);
                                                                }

                                                            }
                                                        }
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'component',
                                                width: 4
                                            },                               
                                            {
                                                id: 'panelTipoCuentaTarjeta',
                                                name: 'panelTipoCuentaTarjeta',
                                                xtype: 'fieldset',
                                                title: 'Tipos de Cuenta/Tarjeta',
                                                width: 230,
                                                collapsible: false,
                                                collapsed: false,                                    
                                                items: [
                                                    frameTipoCuentaTarjeta,
                                                    {
                                                        xtype: 'panel',
                                                        buttonAlign: 'right',
                                                        bbar: [
                                                            {
                                                                text: 'Select All',
                                                                handler: function() {
                                                                    ejecutaFuncionSelectAllTipoCuentaTarjeta();
                                                                }
                                                            },
                                                            '-',
                                                            {
                                                                text: 'Deselect All',
                                                                handler: function() {
                                                                    ejecutaSelectAllTipoCuentaTarjeta   = false;
                                                                    permiteConsultarBancos              = false;
                                                                    Ext.each(itemsTipoCuentaTarjeta, function(record){
                                                                        record.setValue(false);
                                                                        record.setDisabled(false);
                                                                    });
                                                                    permiteConsultarBancos = true;
                                                                }
                                                            }]                                       
                                                    }]
                                            },                                
                                            {
                                                xtype: 'component',
                                                width: 4
                                            },                               
                                            {
                                                id: 'panelBanco',
                                                name: 'panelBanco',
                                                xtype: 'fieldset',
                                                title: 'Bancos',
                                                width: 650,
                                                collapsible: false,
                                                collapsed: false,
                                                items: [
                                                    frameBanco,
                                                    {
                                                        xtype: 'panel',
                                                        buttonAlign: 'right',
                                                        bbar: [
                                                            {
                                                                text: 'Select All',
                                                                handler: function() {
                                                                    Ext.each(itemsBanco, function(record){
                                                                        record.setValue(true);
                                                                    });
                                                                }
                                                            },
                                                            '-',
                                                            {
                                                                text: 'Deselect All',
                                                                handler: function() {
                                                                    Ext.each(itemsBanco, function(record){
                                                                        record.setValue(false);
                                                                        record.setDisabled(false);
                                                                    });
                                                                }
                                                            }]
                                                    }]
                                            }]
                                    }],
                                renderTo: 'filtro'
                            });
                            Ext.getCmp('panelBanco').setVisible(false);
                            Ext.getCmp('panelTipoCuentaTarjeta').setVisible(false);
                            Ext.getCmp('panelEsCuentaTarjeta').setVisible(false);
                            Ext.EventManager.onWindowResize(function() {
                                panel.doComponentLayout();
                            });
                        },
                        failure: function (result) {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });


                    sm = Ext.create('Ext.selection.CheckboxModel', {
                        checkOnly : true,
                        mode: 'MULTI'
                    });

                    gridServicios = Ext.create('Ext.grid.Panel', {
                        width: '1280px',
                        height: 500,
                        store: storePuntosACortar,
                        loadMask: true,
                        frame: false,
                        selModel: sm,
                        iconCls: 'icon-grid',
                        dockedItems: [{
                                xtype: 'toolbar',
                                dock: 'top',
                                align: '->',
                                items: [
                                    {
                                        xtype: 'fieldcontainer',
                                        defaultType: 'checkboxfield',
                                        style: 'margin-left:10px;',
                                        items: [
                                            {
                                                boxLabel  : '<b>TODO</b>',
                                                name      : 'chkCortarTodos',
                                                id        : 'chkCortarTodos'
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'tbfill'
                                    }, 
                                    {
                                        xtype: 'label',
                                        id:'numLoginesPorLote',
                                        style: 'color:red; font-weight:bold;font-size:12px;',
                                        text: "# Login x Lote: " + intNumeroLoginesPorLote
                                    },
                                    {
                                        xtype: 'component',
                                        width: 10
                                    },
                                    {
                                        iconCls: 'icon_corteMasivo',
                                        text: 'Cortar',
                                        itemId: 'deleteAjax',
                                        scope: this,
                                        handler: function () {
                                            if (respuestaPermiteAcceso)
                                            {
                                                corteMasivoClientes();
                                            }
                                            else
                                            {
                                                var w = new Ext.Window({
                                                    height: 95, width: 525,
                                                    resizable: false,
                                                    title: 'Informativo',
                                                    html: "<img style=\"vertical-align:middle\" " +
                                                        "src=\"/./public/images/stop.png\"> " + "<span>Esta opción solo se " +
                                                        "encuentra disponible desde las " + json.strHoraInicio +
                                                        " hasta las " + json.strHoraFin + "</span>"
                                                });

                                                w.show();

                                            }
                                        }
                                    },
                                    {
                                        iconCls: 'icon_exportar',
                                        text: 'Exportar CSV',
                                        itemId: 'exportarCsvCorte',
                                        scope: this,
                                        handler: function () {
                                            if(storePuntosACortar.getCount() > 0)
                                            {
                                                ejecutaOpcionCorteMasivo('EXPORTAR_CSV');
                                            }
                                            else
                                            {
                                                var w = new Ext.Window({
                                                    height: 95, 
                                                    width: 400,
                                                    resizable: false,
                                                    title: 'Informativo',
                                                    html: "<img style=\"vertical-align:middle\" " +
                                                        "src=\"/./public/images/stop.png\"> " + "<span>No existen registros a exportar</span>"
                                                });

                                                w.show();
                                            }

                                        }
                                    }
                                ]
                            }],
                        columns: [
                            {
                                xtype: 'rownumberer',
                                width: 40
                            },
                            {
                                header: 'Login',
                                dataIndex: 'loginCorteMasivo',
                                width: 130,
                                sortable: true
                            },
                            {
                                header: 'Cliente Nombre',
                                dataIndex: 'nombreClienteCorteMasivo',
                                width: 280,
                                sortable: true
                            },
                            {
                                header: 'Oficina',
                                dataIndex: 'nombreOficinaCorteMasivo',
                                width: 200,
                                sortable: true
                            },
                            {
                                header: 'Cartera',
                                dataIndex: 'saldoCorteMasivo',
                                width: 80,
                                sortable: true
                            },
                            {
                                header: 'Forma Pago',
                                dataIndex: 'descripcionFormaPagoCorteMasivo',
                                width: 150,
                                sortable: true
                            },
                            {
                                header: 'Banco/Tarjeta',
                                dataIndex: 'descripcionBancoCorteMasivo',
                                width: 200,
                                sortable: true
                            },
                            {
                                header: 'Tipo Cuenta/Tipo Tarjeta',
                                dataIndex: 'descripcionCuentaCorteMasivo',
                                width: 200,
                                sortable: true
                            },
                            {
                                header: 'Fecha Activaci\xf3n',
                                dataIndex: 'fechaActivacionCorteMasivo',
                                width: 120,
                                sortable: true
                            },
                            {
                                header: 'Tipo Negocio',
                                dataIndex: 'nombreTipoNegocioCorteMasivo',
                                width: 120,
                                sortable: true
                            },
                            {
                                header: 'Ultima Milla',
                                dataIndex: 'nombreUltimaMillaCorteMasivo',
                                width: 80,
                                sortable: true
                            },
                            {
                                xtype: 'actioncolumn',
                                header: 'Ver Servicios',
                                width: 100,
                                items: [{
                                        getClass: function (v, meta, rec) {
                                            return 'button-grid-show';
                                        },
                                        tooltip: 'show servicios',
                                        handler: function (grid, rowIndex, colIndex) {
                                            showServicios(grid.getStore().getAt(rowIndex).data.idPuntoCorteMasivo);
                                        }
                                    }]
                            }
                        ],
                        bbar: Ext.create('Ext.PagingToolbar', {
                            store: storePuntosACortar,
                            displayInfo: true,
                            displayMsg: 'Mostrando {0} - {1} de {2}',
                            emptyMsg: "No hay datos que mostrar."
                        }),
                        renderTo: 'grid'
                    });
            
                },
                failure: function (result) {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        },
        failure: function (result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
});

function showServicios(id) {
	storeServiciosXPadreFacturacion = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: strUrlGetServiciosXPadreFacturacion,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            },
            extraParams: {
                idPuntoFacturacion: id,
                estado: 'Activo'
            }
        },
        fields: [{
                name: 'idPuntoFacturacion',
                mapping: 'idPuntoFacturacion'
            },
            {
                name: 'idServicio',
                mapping: 'idServicio'
            },
            {
                name: 'puntoId',
                mapping: 'puntoId'
            },
            {
                name: 'login',
                mapping: 'login'
            },
            {
                name: 'cliente',
                mapping: 'cliente'
            },
            {
                name: 'planId',
                mapping: 'planId'
            },
            {
                name: 'nombrePlan',
                mapping: 'nombrePlan'
            },
            {
                name: 'estado',
                mapping: 'estado'
            }]
    });

	gridServiciosXPadreFacturacion = Ext.create('Ext.grid.Panel', {
		width : 1300,
		height : 300,
		store : storeServiciosXPadreFacturacion,
		columns : [ 
        {
			id : 'idPuntoFacturacion',
			header : 'idPuntoFacturacion',
			dataIndex : 'idPuntoFacturacion',
			hidden : true,
			hideable : false
		},
        {
			id : 'idServicio',
			header : 'idServicio',
			dataIndex : 'idServicio',
			hidden : true,
			hideable : false
		},
        {
			id : 'puntoId',
			header : 'puntoId',
			dataIndex : 'puntoId',
			hidden : true,
			hideable : false
		},
        {
			id : 'login',
			header : 'login',
			dataIndex : 'login',
			hidden : false,
			hideable : false
		},
        {
			id : 'cliente',
			header : 'cliente',
			dataIndex : 'cliente',
			hidden : false,
			hideable : false
		},
        {
			id : 'planId',
			header : 'planId',
			dataIndex : 'planId',
			hidden : true,
			hideable : false
		},
        {
			id : 'nombrePlan',
			header : 'nombrePlan',
			dataIndex : 'nombrePlan',
			hidden : false,
			hideable : false
		},
        {
			id : 'estado',
			header : 'estado',
			dataIndex : 'estado',
			hidden : false,
			hideable : false
		}]
	});
    
	Ext.create('Ext.window.Window', {
		title : 'Mostrar Servicios',
		modal : true,
		width : 500,
		height : 280,
		resizable : false,
		layout : 'fit',
		items : [ gridServiciosXPadreFacturacion ],
		buttonAlign : 'center'
	}).show();
}

function ejecutaOpcionCorteMasivo(opcion) {
    if(consultaEnEjecucionClientesCorte)
    {
        Ext.Msg.alert('Alerta', "No se puede ejecutar la acción debido a que existe una consulta en proceso");
        return;
    }
    
    var fechaCreacionDocSeleccionada = Ext.getCmp('fechaCreacionDoc').value;
    if(Ext.isEmpty(fechaCreacionDocSeleccionada))
    {
        Ext.Msg.alert('Alerta', "Favor, seleccione la fecha de creación del documento");
        return;
    }
    fechaCreacionDocBusqueda        = Ext.getCmp('fechaCreacionDoc').value;
    
    var tiposDocumentosSeleccionados = Ext.getCmp('cmbTipoDocumento').getValue().toString();
    if(Ext.isEmpty(tiposDocumentosSeleccionados))
    {
        Ext.Msg.alert('Alerta', "Favor, seleccione al menos un tipo de documento");
        return;
    }
    tiposDocumentosBusqueda = Ext.getCmp('cmbTipoDocumento').getValue().toString();
    
    numDocsAbiertosBusqueda         = Ext.getCmp('docsAbiertos').value;
    valorMontoCarteraBusqueda       = Ext.getCmp('montoCartera').value;
    idTipoNegocioBusqueda           = Ext.getCmp('tipoNegocio').value;
    valorClienteCanalBusqueda       = Ext.getCmp('clienteCanal').value;
    nombreUltimaMillaBusqueda       = Ext.getCmp('ultimaMilla').value;
    idCicloFacturacionBusqueda      = "";    
    idsOficinasBusqueda             = "";
    idsFormasPagoBusqueda           = "";
    valorCuentaTarjetaBusqueda      = "";
    idsTiposCuentaTarjetaBusqueda   = "";
    idsBancosBusqueda               = "";

    //Javier Hidalgo
    fechaLimActivacion             = Ext.getCmp('instaladosHasta').value;
   
    if(numDocsAbiertosBusqueda == 0)
    {
        Ext.Msg.alert('Alerta', "Favor, el número de documentos abiertos no puede ser igual a 0");
        Ext.getCmp('docsAbiertos').reset();
        return;
    }
    
    var idCicloFacturacionSeleccionado = Ext.getCmp('cmbCicloFacturacion').getValue();
    if(boolPermisoEmpresas)
    {
        if (idCicloFacturacionSeleccionado <= 0)
        {
            Ext.Msg.alert('Alerta', "Favor, seleccione un Ciclo de Facturación");
            return;
        }
    }
    idCicloFacturacionBusqueda = idCicloFacturacionSeleccionado;
    
    var idsOficinasSeleccionadas = ""; 
    for (var i = 0; i < itemsOficina.length; i++) {
        if (Ext.getCmp('idOficina_' + i).value == true) {
            if (idsOficinasSeleccionadas != null && idsOficinasSeleccionadas == "") {
                idsOficinasSeleccionadas = idsOficinasSeleccionadas + Ext.getCmp('idOficina_' + i).inputValue;
            } else {
                idsOficinasSeleccionadas = idsOficinasSeleccionadas + "," + Ext.getCmp('idOficina_' + i).inputValue;
            }
        }
    }
    if (idsOficinasSeleccionadas == "") {
        Ext.Msg.alert('Alerta',"Favor, seleccione una oficina");
        return;
    }
    idsOficinasBusqueda = idsOficinasSeleccionadas;
    
    var idsFormasPagoSeleccionadas = "";
    for (var contFormasPagoBusqueda = 0; contFormasPagoBusqueda < itemsFormaPago.length; contFormasPagoBusqueda++) {
        if (Ext.getCmp('idFormaPago_' + contFormasPagoBusqueda).value == true) {
            if (idsFormasPagoSeleccionadas != null && idsFormasPagoSeleccionadas == "") {
                idsFormasPagoSeleccionadas = idsFormasPagoSeleccionadas + Ext.getCmp('idFormaPago_' + contFormasPagoBusqueda).inputValue;
            }
            else {
                idsFormasPagoSeleccionadas = idsFormasPagoSeleccionadas + "," + Ext.getCmp('idFormaPago_' + contFormasPagoBusqueda).inputValue;
            }
        }
    }
    idsFormasPagoBusqueda = idsFormasPagoSeleccionadas;

    var valorCuentaTarjetaSeleccionado = "";
    if(Ext.getCmp('valorCuentaTarjeta_1').value == true && Ext.getCmp('valorCuentaTarjeta_2').value == true)
    {
        valorCuentaTarjetaSeleccionado = Ext.getCmp('valorCuentaTarjeta_1').inputValue+","+Ext.getCmp('valorCuentaTarjeta_2').inputValue;
    }
    else if(Ext.getCmp('valorCuentaTarjeta_1').value == true)
    {
        valorCuentaTarjetaSeleccionado = Ext.getCmp('valorCuentaTarjeta_1').inputValue;
    }
    else if(Ext.getCmp('valorCuentaTarjeta_2').value == true)
    {
        valorCuentaTarjetaSeleccionado = Ext.getCmp('valorCuentaTarjeta_2').inputValue;
    }
    valorCuentaTarjetaBusqueda = valorCuentaTarjetaSeleccionado;
    
    var idsTipoCuentaTarjetaSeleccionados = "";
    for (var contTiposCuentaTarjetaBusqueda = 0; contTiposCuentaTarjetaBusqueda < itemsTipoCuentaTarjeta.length; contTiposCuentaTarjetaBusqueda++) {
        if (Ext.getCmp('idTipoCuentaTarjeta_' + contTiposCuentaTarjetaBusqueda).value == true) {
            if(idsTipoCuentaTarjetaSeleccionados != null && idsTipoCuentaTarjetaSeleccionados == "") {
                idsTipoCuentaTarjetaSeleccionados = idsTipoCuentaTarjetaSeleccionados 
                                                    + Ext.getCmp('idTipoCuentaTarjeta_' + contTiposCuentaTarjetaBusqueda).inputValue;
            }
            else{
                idsTipoCuentaTarjetaSeleccionados = idsTipoCuentaTarjetaSeleccionados + "," 
                                                    + Ext.getCmp('idTipoCuentaTarjeta_' + contTiposCuentaTarjetaBusqueda).inputValue;
            }
        }
    }
    idsTiposCuentaTarjetaBusqueda = idsTipoCuentaTarjetaSeleccionados;
    
    var idsBancosSeleccionados = "";
    for (var contBancosBusqueda = 0; contBancosBusqueda < itemsBanco.length; contBancosBusqueda++) {
        if (Ext.getCmp('idBanco_' + contBancosBusqueda).value == true) {
            if(idsBancosSeleccionados != null && idsBancosSeleccionados == "") {
                idsBancosSeleccionados = idsBancosSeleccionados + Ext.getCmp('idBanco_' + contBancosBusqueda).inputValue;
            }
            else{
                idsBancosSeleccionados = idsBancosSeleccionados + "," + Ext.getCmp('idBanco_' + contBancosBusqueda).inputValue;
            }
        }
    }
    idsBancosBusqueda = idsBancosSeleccionados;
    
    if(opcion == 'BUSCAR')
    {
        buscar();
    }
    else if(opcion == 'EXPORTAR_CSV')
    {
        exportarClientesCorteMasivo();
    }
}

function buscar()
{
    Ext.getCmp('numClientesFCRecurrente').setText("");
    Ext.getCmp('numClientesFCNoRecurrente').setText("");
    Ext.getCmp('numClientesNDI').setText("");
    Ext.get('imgLoadResumenPrevio').setStyle('display', 'block');
    Ext.get('formResumenPrevio').setStyle('display', 'block');
    storePuntosACortar.removeAll();
    var connResumenPrevio = new Ext.data.Connection({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function (con, opt)
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Generando Resumen Previo, por favor espere...',
                                    progressText: 'Saving...',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function (con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function (con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }
    });
    
    connResumenPrevio.request({
        url: strUrlGetResumenCorteMasivo,
        method: 'post',
        timeout: 900000,
        params: {
            fechaCreacionDocBusqueda       : fechaCreacionDocBusqueda,
            tiposDocumentosBusqueda        : tiposDocumentosBusqueda,
            numDocsAbiertosBusqueda        : numDocsAbiertosBusqueda,
            valorMontoCarteraBusqueda      : valorMontoCarteraBusqueda,
            idTipoNegocioBusqueda          : idTipoNegocioBusqueda,
            valorClienteCanalBusqueda      : valorClienteCanalBusqueda,
            nombreUltimaMillaBusqueda      : nombreUltimaMillaBusqueda,
            idCicloFacturacionBusqueda     : idCicloFacturacionBusqueda,
            idsOficinasBusqueda            : idsOficinasBusqueda,
            idsFormasPagoBusqueda          : idsFormasPagoBusqueda,
            valorCuentaTarjetaBusqueda     : valorCuentaTarjetaBusqueda,
            idsTiposCuentaTarjetaBusqueda  : idsTiposCuentaTarjetaBusqueda,
            idsBancosBusqueda              : idsBancosBusqueda,
            permiteConsultar               : 'SI',
            fechaLimActivacion             : fechaLimActivacion,
            identificacionesExcluidas      : identificacionesExcluidas
        },
        success: function (response) {
            var json = Ext.JSON.decode(response.responseText);
            Ext.get('imgLoadResumenPrevio').setStyle('display', 'none');
            if (json.status === "OK")
            {
                Ext.getCmp('numClientesFCRecurrente').setText(json.arrayResultado['FAC']);
                Ext.getCmp('numClientesFCNoRecurrente').setText(json.arrayResultado['FACP']);
                Ext.getCmp('numClientesNDI').setText(json.arrayResultado['NDI']);
                
                storePuntosACortar.currentPage = 1;
                storePuntosACortar.getProxy().extraParams.fechaCreacionDocBusqueda       = fechaCreacionDocBusqueda;
                storePuntosACortar.getProxy().extraParams.tiposDocumentosBusqueda        = tiposDocumentosBusqueda;
                storePuntosACortar.getProxy().extraParams.numDocsAbiertosBusqueda        = numDocsAbiertosBusqueda;
                storePuntosACortar.getProxy().extraParams.valorMontoCarteraBusqueda      = valorMontoCarteraBusqueda;
                storePuntosACortar.getProxy().extraParams.idTipoNegocioBusqueda          = idTipoNegocioBusqueda;
                storePuntosACortar.getProxy().extraParams.valorClienteCanalBusqueda      = valorClienteCanalBusqueda;
                storePuntosACortar.getProxy().extraParams.nombreUltimaMillaBusqueda      = nombreUltimaMillaBusqueda;
                storePuntosACortar.getProxy().extraParams.idCicloFacturacionBusqueda     = idCicloFacturacionBusqueda;
                storePuntosACortar.getProxy().extraParams.idsOficinasBusqueda            = idsOficinasBusqueda;
                storePuntosACortar.getProxy().extraParams.idsFormasPagoBusqueda          = idsFormasPagoBusqueda;
                storePuntosACortar.getProxy().extraParams.valorCuentaTarjetaBusqueda     = valorCuentaTarjetaBusqueda;
                storePuntosACortar.getProxy().extraParams.idsTiposCuentaTarjetaBusqueda  = idsTiposCuentaTarjetaBusqueda;
                storePuntosACortar.getProxy().extraParams.idsBancosBusqueda              = idsBancosBusqueda;
                storePuntosACortar.getProxy().extraParams.permiteConsultar               = 'SI';
                storePuntosACortar.getProxy().extraParams.fechaLimActivacion             = fechaLimActivacion;
                storePuntosACortar.getProxy().extraParams.identificacionesExcluidas      = identificacionesExcluidas;
                muestraMessageConsultaClientesCorte                                      = true;
                storePuntosACortar.load();
            }
            else if(json.status === "FORMAT_ERROR")
            {
                Ext.Msg.alert('Error en Doc. Adjunto', json.mensaje);
                storePuntosACortar.removeAll();
                muestraMessageConsultaClientesCorte = false;
                storePuntosACortar.load({
                    params: {
                        fechaCreacionDocBusqueda : '',
                        tiposDocumentosBusqueda : '',
                        numDocsAbiertosBusqueda : '',
                        valorMontoCarteraBusqueda : '',
                        idTipoNegocioBusqueda : '',
                        valorClienteCanalBusqueda : '',
                        nombreUltimaMillaBusqueda : '',
                        idCicloFacturacionBusqueda : '',
                        idsOficinasBusqueda : '',
                        idsFormasPagoBusqueda : '',
                        valorCuentaTarjetaBusqueda : '',
                        idsTiposCuentaTarjetaBusqueda : '',
                        idsBancosBusqueda : '',
                        permiteConsultar: ''
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Error ', json.mensaje);
                storePuntosACortar.removeAll();
                muestraMessageConsultaClientesCorte = false;
                storePuntosACortar.load({
                    params: {
                        fechaCreacionDocBusqueda : '',
                        tiposDocumentosBusqueda : '',
                        numDocsAbiertosBusqueda : '',
                        valorMontoCarteraBusqueda : '',
                        idTipoNegocioBusqueda : '',
                        valorClienteCanalBusqueda : '',
                        nombreUltimaMillaBusqueda : '',
                        idCicloFacturacionBusqueda : '',
                        idsOficinasBusqueda : '',
                        idsFormasPagoBusqueda : '',
                        valorCuentaTarjetaBusqueda : '',
                        idsTiposCuentaTarjetaBusqueda : '',
                        idsBancosBusqueda : '',
                        permiteConsultar: ''
                    }
                });
            }
        },
        failure: function (result) {
            Ext.get('imgLoadResumenPrevio').setStyle('display', 'none');
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
            storePuntosACortar.removeAll();
            muestraMessageConsultaClientesCorte = false;
            storePuntosACortar.load({
                params: {
                    fechaCreacionDocBusqueda : '',
                    tiposDocumentosBusqueda : '',
                    numDocsAbiertosBusqueda : '',
                    valorMontoCarteraBusqueda : '',
                    idTipoNegocioBusqueda : '',
                    valorClienteCanalBusqueda : '',
                    nombreUltimaMillaBusqueda : '',
                    idCicloFacturacionBusqueda : '',
                    idsOficinasBusqueda : '',
                    idsFormasPagoBusqueda : '',
                    valorCuentaTarjetaBusqueda : '',
                    idsTiposCuentaTarjetaBusqueda : '',
                    idsBancosBusqueda : '',
                    permiteConsultar: ''
                }
            });
        }
    });


}

function limpiar() {
    sm.deselectAll();
    Ext.getCmp('docsAbiertos').reset();
    Ext.getCmp('fechaCreacionDoc').reset();
    Ext.getCmp('cmbTipoDocumento').reset();
    Ext.getCmp('montoCartera').reset();
    Ext.getCmp('tipoNegocio').reset();
    Ext.getCmp('clienteCanal').reset();
    Ext.getCmp('ultimaMilla').value = "";
    Ext.getCmp('ultimaMilla').setRawValue("");
    Ext.getCmp('cmbCicloFacturacion').reset();
    Ext.getCmp('instaladosHasta').reset();
    Ext.getCmp('importXls').reset();
    
    fechaCreacionDocBusqueda        = "";
    tiposDocumentosBusqueda         = "";
    numDocsAbiertosBusqueda         = "";
    valorMontoCarteraBusqueda       = "";
    idTipoNegocioBusqueda           = "";
    valorClienteCanalBusqueda       = "";
    nombreUltimaMillaBusqueda       = "";
    idCicloFacturacionBusqueda      = "";    
    idsOficinasBusqueda             = "";
    idsFormasPagoBusqueda           = "";
    valorCuentaTarjetaBusqueda      = "";
    idsTiposCuentaTarjetaBusqueda   = "";
    idsBancosBusqueda               = "";
    fechaLimActivacion              = "";
    nombreArchivoAdjunto            = "";
    identificacionesExcluidas       = "";
    
    Ext.each(itemsOficina, function(record){
        record.setValue(false);
    });
    
    opcionesCuentaTarjetaBancosVisible  = false;
    ejecutaSelectAllFormaPago           = false;
    ejecutaSelectAllCuentaTarjeta       = false;
    ejecutaSelectAllTipoCuentaTarjeta   = false;
    
    permiteConsultarBancos              = false;
    permiteConsultarTiposCuentaTarjeta  = false;
    permiteConsultarCuentaTarjeta       = false;
    Ext.each(itemsBanco, function(record){
        record.setValue(false);
    });
    itemsBanco = [];
    Ext.each(itemsTipoCuentaTarjeta, function(record){
        record.setValue(false);
    });
    itemsTipoCuentaTarjeta = [];
    Ext.getCmp('valorCuentaTarjeta_1').setValue(false);
    Ext.getCmp('valorCuentaTarjeta_2').setValue(false);
    Ext.each(itemsFormaPago, function(record){
        record.setValue(false);
    });
    permiteConsultarBancos              = true;
    permiteConsultarTiposCuentaTarjeta  = true;
    permiteConsultarCuentaTarjeta       = true;
    Ext.getCmp('panelBanco').setVisible(false);
    Ext.getCmp('panelTipoCuentaTarjeta').setVisible(false);
    Ext.getCmp('panelEsCuentaTarjeta').setVisible(false);
    
    Ext.getCmp('numClientesFCRecurrente').setText("");
    Ext.getCmp('numClientesFCNoRecurrente').setText("");
    Ext.getCmp('numClientesNDI').setText("");
    Ext.get('imgLoadResumenPrevio').setStyle('display', 'none');
    Ext.get('formResumenPrevio').setStyle('display', 'none');

    
    Ext.getCmp('chkCortarTodos').setValue(false);
    
    storePuntosACortar.removeAll();
    muestraMessageConsultaClientesCorte = false;
    storePuntosACortar.load({
        params: {
            fechaCreacionDocBusqueda : '',
            tiposDocumentosBusqueda : '',
            numDocsAbiertosBusqueda : '',
            valorMontoCarteraBusqueda : '',
            idTipoNegocioBusqueda : '',
            valorClienteCanalBusqueda : '',
            nombreUltimaMillaBusqueda : '',
            idCicloFacturacionBusqueda : '',
            idsOficinasBusqueda : '',
            idsFormasPagoBusqueda : '',
            valorCuentaTarjetaBusqueda : '',
            idsTiposCuentaTarjetaBusqueda : '',
            idsBancosBusqueda : '',
            permiteConsultar: ''
        }
    });
}

function corteMasivoClientes() {
    if(storePuntosACortar.getCount() > 0)
    {
        var valueChkCortarTodos = Ext.getCmp('chkCortarTodos').getValue();
        if(valueChkCortarTodos)
        {
            Ext.Msg.confirm('Alerta', 'Se procederá a <b>CORTAR A TODOS LOS CLIENTES</b>. Desea continuar?',
            function (btn) {
                if (btn == 'yes') {
                    Ext.MessageBox.show({
                        title: 'Favor espere',
                        msg: 'Procesando...',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        closable: false,
                        waitConfig: {
                            interval: 200
                        }
                    });

                    Ext.Ajax.request({
                        url: strUrlCortarClientesMasivoPorLotes,
                        method: 'post',
                        timeout: 2700000,
                        params: {
                            fechaCreacionDocBusqueda : fechaCreacionDocBusqueda,
                            tiposDocumentosBusqueda : tiposDocumentosBusqueda,
                            numDocsAbiertosBusqueda : numDocsAbiertosBusqueda,
                            valorMontoCarteraBusqueda : valorMontoCarteraBusqueda,
                            idTipoNegocioBusqueda : idTipoNegocioBusqueda,
                            valorClienteCanalBusqueda : valorClienteCanalBusqueda,
                            nombreUltimaMillaBusqueda : nombreUltimaMillaBusqueda,
                            idCicloFacturacionBusqueda : idCicloFacturacionBusqueda,
                            idsOficinasBusqueda : idsOficinasBusqueda,
                            idsFormasPagoBusqueda : idsFormasPagoBusqueda,
                            valorCuentaTarjetaBusqueda : valorCuentaTarjetaBusqueda,
                            idsTiposCuentaTarjetaBusqueda : idsTiposCuentaTarjetaBusqueda,
                            idsBancosBusqueda : idsBancosBusqueda,
                            fechaLimActivacion: fechaLimActivacion,
                            identificacionesExcluidas: identificacionesExcluidas
                        },
                        success: function (response) {
                            Ext.MessageBox.hide();
                            var respuesta = Ext.JSON.decode(response.responseText);
                            if(respuesta.status === 'OK')
                            {
                                sm.deselectAll();
                                storePuntosACortar.removeAll();
                                muestraMessageConsultaClientesCorte = false;
                                storePuntosACortar.load({
                                    params: {
                                        fechaCreacionDocBusqueda : '',
                                        tiposDocumentosBusqueda : '',
                                        numDocsAbiertosBusqueda : '',
                                        valorMontoCarteraBusqueda : '',
                                        idTipoNegocioBusqueda : '',
                                        valorClienteCanalBusqueda : '',
                                        nombreUltimaMillaBusqueda : '',
                                        idCicloFacturacionBusqueda : '',
                                        idsOficinasBusqueda : '',
                                        idsFormasPagoBusqueda : '',
                                        valorCuentaTarjetaBusqueda : '',
                                        idsTiposCuentaTarjetaBusqueda : '',
                                        idsBancosBusqueda : '',
                                        permiteConsultar: ''
                                    }
                                });
                                Ext.Msg.alert('Alerta',
                                              'Se procedió a ejecutar el script de Corte Masivo, <br/> favor esperar el email de confirmación!');
                            }
                            else
                            {
                                Ext.Msg.alert('Error', respuesta.mensaje);
                            }
                        },
                        failure: function (result) {
                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                        }
                    });
                }
            });
        }
        else
        {
            var idsPuntosSeleccionados      = '';
            var cantidadPuntosSeleccionados = 0;
            if (sm.getSelection().length > 0) {
                var contadorServiciosInCorte = 0;
                for (var i = 0; i < sm.getSelection().length; ++i) {
                    cantidadPuntosSeleccionados = cantidadPuntosSeleccionados + 1;
                    idsPuntosSeleccionados = idsPuntosSeleccionados + sm.getSelection()[i].data.idPuntoCorteMasivo;
                    if (sm.getSelection()[i].data.estado == 'In-Corte') {
                        contadorServiciosInCorte = contadorServiciosInCorte + 1;
                    }
                    if (i < (sm.getSelection().length - 1)) {
                        idsPuntosSeleccionados = idsPuntosSeleccionados + '|';
                    }
                }

                if (contadorServiciosInCorte == 0) {
                    Ext.Msg
                        .confirm(
                            'Alerta', 'Se procederá a <b>CORTAR ' + cantidadPuntosSeleccionados + ' CLIENTES</b>. Desea continuar?',
                            function (btn) {
                                if (btn == 'yes') {
                                    Ext.MessageBox.show({
                                        title: 'Favor espere',
                                        msg: 'Procesando...',
                                        progressText: 'Saving...',
                                        width: 300,
                                        wait: true,
                                        closable: false,
                                        waitConfig: {
                                            interval: 200
                                        }
                                    });
                                    var idsBancosTarjetasSeleccionados = "";
                                    if (valorCuentaTarjetaBusqueda != "" || idsTiposCuentaTarjetaBusqueda != "" || idsBancosBusqueda != "") {
                                        idsBancosTarjetasSeleccionados  = valorCuentaTarjetaBusqueda + '&' + idsTiposCuentaTarjetaBusqueda 
                                                                          + '&' + idsBancosBusqueda;
                                    }
                                    Ext.Ajax.request({
                                        url: strUrlCortarClientesMasivo,
                                        method: 'post',
                                        timeout: 400000,
                                        params: {
                                            idsPuntos: idsPuntosSeleccionados,
                                            oficina: idsOficinasBusqueda,
                                            numFacturasAbiertas: numDocsAbiertosBusqueda,
                                            valorMontoDeuda: valorMontoCarteraBusqueda,
                                            idsBancosTarjetas: idsBancosTarjetasSeleccionados,
                                            idsOficinas: idsOficinasBusqueda,
                                            cantidadPuntos: cantidadPuntosSeleccionados
                                        },
                                        success: function (response) {
                                            sm.deselectAll();
                                            storePuntosACortar.removeAll();
                                            muestraMessageConsultaClientesCorte = false;
                                            storePuntosACortar.load({
                                                params: {
                                                    fechaCreacionDocBusqueda : '',
                                                    tiposDocumentosBusqueda : '',
                                                    numDocsAbiertosBusqueda : '',
                                                    valorMontoCarteraBusqueda : '',
                                                    idTipoNegocioBusqueda : '',
                                                    valorClienteCanalBusqueda : '',
                                                    nombreUltimaMillaBusqueda : '',
                                                    idCicloFacturacionBusqueda : '',
                                                    idsOficinasBusqueda : '',
                                                    idsFormasPagoBusqueda : '',
                                                    valorCuentaTarjetaBusqueda : '',
                                                    idsTiposCuentaTarjetaBusqueda : '',
                                                    idsBancosBusqueda : '',
                                                    permiteConsultar: ''
                                                }
                                            });
                                            Ext.MessageBox.hide();
                                            Ext.Msg.alert(
                                                'Alerta',
                                                'Se procedió a ejecutar el script de Corte Masivo, <br/> favor esperar el email de confirmación!');
                                        },
                                        failure: function (result) {
                                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                        }
                                    });
                                }
                            });
                } else {
                    alert('Por lo menos uno de los CLIENTES se encuentra en estado In-Corte');
                }
            } else {
                alert('Seleccione por lo menos un CLIENTE de la lista');
            }
        }
    }
    else
    {
        var w = new Ext.Window({
                    height: 95, 
                    width: 400,
                    resizable: false,
                    title: 'Informativo',
                    html: "<img style=\"vertical-align:middle\" " +
                        "src=\"/./public/images/stop.png\"> " + "<span>No existen registros a cortar</span>"
                });

        w.show();
    }
}

function exportarClientesCorteMasivo()
{
    var connEsperaExportar = new Ext.data.Connection({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function (con, opt)
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Generando archivo CSV, por favor espere...',
                                    progressText: 'Saving...',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function (con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function (con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }
    });
    
    connEsperaExportar.request({
        url: strUrlExportarClientesCorteMasivo,
        method: 'post',
        timeout: 2700000,
        params: {
            fechaCreacionDocExportar: fechaCreacionDocBusqueda,
            tiposDocumentosExportar: tiposDocumentosBusqueda,
            numDocsAbiertosExportar: numDocsAbiertosBusqueda,
            valorMontoCarteraExportar: valorMontoCarteraBusqueda,
            idTipoNegocioExportar: idTipoNegocioBusqueda,
            valorClienteCanalExportar: valorClienteCanalBusqueda,
            nombreUltimaMillaExportar: nombreUltimaMillaBusqueda,
            idCicloFacturacionExportar: idCicloFacturacionBusqueda,
            idsOficinasExportar: idsOficinasBusqueda,
            idsFormasPagoExportar: idsFormasPagoBusqueda,
            valorCuentaTarjetaExportar: valorCuentaTarjetaBusqueda,
            idsTiposCuentaTarjetaExportar: idsTiposCuentaTarjetaBusqueda,
            idsBancosExportar: idsBancosBusqueda,
            fechaLimActivacion: fechaLimActivacion,
            nombreArchivoAdjunto: nombreArchivoAdjunto,
            identificacionesExcluidas: identificacionesExcluidas
        },
        success: function (response) {
            var datos = Ext.JSON.decode(response.responseText);
            if (datos.status === "OK")
            {
                window.location = datos.idDocumento+"/descargarCsvProcesoMasivo";
            }
            else if(datos.status === "FORMAT_ERROR")
            {
                Ext.Msg.alert('Error en Doc. Adjunto', datos.mensaje);
            }
            else
            {
                Ext.Msg.alert('Error ', datos.mensaje);
            }
        },
        failure: function (result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}

function ejecutaFuncionSelectAllCuentaTarjeta()
{
    permiteConsultarTiposCuentaTarjeta  = false;
    var numChkCuentaTarjetaTrue         = 0;
    if(Ext.getCmp('valorCuentaTarjeta_1').value)
    {
        numChkCuentaTarjetaTrue = numChkCuentaTarjetaTrue + 1;
    }

    if(Ext.getCmp('valorCuentaTarjeta_2').value)
    {
        numChkCuentaTarjetaTrue = numChkCuentaTarjetaTrue + 1;
    }

    Ext.getCmp('valorCuentaTarjeta_1').setValue(true);
    Ext.getCmp('valorCuentaTarjeta_2').setValue(true);
    if(ejecutaSelectAllFormaPago)
    {
        Ext.getCmp('valorCuentaTarjeta_1').setDisabled(true);
        Ext.getCmp('valorCuentaTarjeta_2').setDisabled(true);
    }

    if(numChkCuentaTarjetaTrue != 2)
    {
        Ext.getCmp('valorCuentaTarjeta_2').setValue(false);
        permiteConsultarTiposCuentaTarjeta  = true;
        ejecutaSelectAllCuentaTarjeta       = true;
        Ext.getCmp('valorCuentaTarjeta_2').setValue(true);
    }
    else
    {
        permiteConsultarTiposCuentaTarjeta  = true;
        ejecutaSelectAllCuentaTarjeta       = false;
        ejecutaSelectAllFormaPago           = false;
    }
}

function ejecutaFuncionSelectAllTipoCuentaTarjeta()
{
    if(itemsTipoCuentaTarjeta.length > 0)
    {
        permiteConsultarBancos          = false;
        var numChkTipoCuentaTarjetaTrue = 0;
        var posUltimoChkTipoCuentaTarjeta = itemsTipoCuentaTarjeta.length - 1;
        for (var i = 0; i < itemsTipoCuentaTarjeta.length; i++)
        {
            if(Ext.getCmp('idTipoCuentaTarjeta_' + i).value)
            {
                numChkTipoCuentaTarjetaTrue = numChkTipoCuentaTarjetaTrue + 1;
            }
            Ext.getCmp('idTipoCuentaTarjeta_' + i).setValue(true);
            if(ejecutaSelectAllCuentaTarjeta)
            {
                Ext.getCmp('idTipoCuentaTarjeta_' + i).setDisabled(true);
            }
        }

        if(numChkTipoCuentaTarjetaTrue != itemsTipoCuentaTarjeta.length)
        {
            Ext.getCmp('idTipoCuentaTarjeta_'+posUltimoChkTipoCuentaTarjeta).setValue(false);
            permiteConsultarBancos              = true;
            ejecutaSelectAllTipoCuentaTarjeta   = true;
            Ext.getCmp('idTipoCuentaTarjeta_'+posUltimoChkTipoCuentaTarjeta).setValue(true);
        }
        else
        {
            permiteConsultarBancos              = true;
            ejecutaSelectAllTipoCuentaTarjeta   = false;
            ejecutaSelectAllCuentaTarjeta       = false;
        }
    }
}