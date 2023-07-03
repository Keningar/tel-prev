Ext.onReady(function() {

    //Define el modelo de canal de pagos en linea usado en el store storeCanalPagoLinea
    Ext.define('modelCanalPagoLinea', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdCanalPagoLinea', type: 'int'},
            {name: 'strDescripcionCanalPagoLinea', type: 'string'}
        ]
    });

    //Define el contador y multi selector del cbo cboCanalPagoLinea
    Ext.define('cboSelectedCountCanalPagoLinea', {
        alias: 'plugin.selectedCount',
        init: function(cboCountCanalPagoLinea) {
            cboCountCanalPagoLinea.on({
                select: function(me, objRecords) {
                    intNumeroRegistros = objRecords.length,
                        storecboCountCanalPagoLinea = cboCountCanalPagoLinea.getStore(),
                        boolDiffRowCbo = objRecords.length != storecboCountCanalPagoLinea.count,
                        boolNewAll = false,
                        boolSelectedAll = false,
                        objNewRecords = [];
                    Ext.each(objRecords, function(obj, i, objRecordsItself) {
                        //Pregunta si el registro seleccionado es 0 entonces seleccionado todo
                        if (objRecords[i].data.intIdCanalPagoLinea === 0) {
                            boolSelectedAll = true;
                            //Si no esta todo seleccionado, permite seleccionar todo nuevamente
                            if (!cboCountCanalPagoLinea.boolCboSelectedAll) {
                                intNumeroRegistros = storecboCountCanalPagoLinea.getCount();
                                cboCountCanalPagoLinea.select(storecboCountCanalPagoLinea.getRange());
                                cboCountCanalPagoLinea.boolCboSelectedAll = true;
                                boolNewAll = true;
                            }
                        } else {
                            if (boolDiffRowCbo && !boolNewAll)
                                objNewRecords.push(objRecords[i]);
                        }

                    });
                    //Validacion que realiza el uncheck del combo
                    if (cboCountCanalPagoLinea.boolCboSelectedAll && !boolSelectedAll) {
                        cboCountCanalPagoLinea.clearValue();
                        cboCountCanalPagoLinea.boolCboSelectedAll = false;
                    } else if (boolDiffRowCbo && !boolNewAll) {
                        cboCountCanalPagoLinea.select(objNewRecords);
                        cboCountCanalPagoLinea.boolCboSelectedAll = false;
                    }
                }
            });
        }
    });

    //Store storeCanalPagoLinea usado en el combo cboCanalPagoLinea
    var storeCanalPagoLinea = new Ext.data.Store({
        autoLoad: true,
        model: 'modelCanalPagoLinea',
        proxy: {
            extraParams: {
                strTipoObjeto: "ComboBox"
            },
            type: 'ajax',
            url: urlGetListadoCanalPagosLinea,
            reader: {
                type: 'json',
                root: 'jsonResponseAdmiCanalPagosLinea'
            }
        }
    });

    // Crea el combo multi selccion usado como item en objFilterPanel
    cboCanalPagoLinea = Ext.create('Ext.form.ComboBox', {
        disabled: false,
        id: 'cboCanalPagoLinea',
        plugins: ['selectedCount'],
        fieldLabel: 'Canal Recaudador',
        store: storeCanalPagoLinea,
        queryMode: 'local',
        editable: false,
        displayField: 'strDescripcionCanalPagoLinea',
        valueField: 'intIdCanalPagoLinea',
        multiSelect: true,
        width: 325,
        displayTpl: '<tpl for="."> {strDescripcionCanalPagoLinea} <tpl if="xindex < xcount">, </tpl> </tpl>',
        listConfig: {
            itemTpl: '{strDescripcionCanalPagoLinea} <div class="uncheckedChkbox"></div>'
        }
    });

    //Campo fecha usado en el objFilterPanel
    var dateFechaHasta = new Ext.form.DateField({
        id: 'dateFechaHasta',
        fieldLabel: 'Fecha Creación Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        editable: false,
        format: 'd-m-Y',
        width: 325
    });

    //Campo fecha usado en el objFilterPanel
    var dateFechaDesde = new Ext.form.DateField({
        id: 'dateFechaDesde',
        fieldLabel: 'Fecha Creación Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        editable: false,
        format: 'd-m-Y',
        width: 325
    });

    //Campo usuario creacion usado en el objFilterPanel
    var txtUsuarioCreacion = Ext.create('Ext.form.Text',
        {
            id: 'txtUsuarioCreacion',
            name: 'txtUsuarioCreacion',
            fieldLabel: 'Usuario creación',
            labelAlign: 'left',
            allowBlank: true,
            width: 325
        });

    //formulario que contiene la sumatoria del grid de pagos agupados por fecha y canal recaudador
    formReportePagosLineaFechaCanalGroup = Ext.create('Ext.form.Panel', {
        renderTo: 'formReportePagosLineaFechaCanalGroup',
        width: 190,
        bodyPadding: 10,
        autoScroll: true,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 2,
            pack: 'center'
        },
        items: [
            {
                colspan: 2,
                labelAlign: 'top',
                xtype: 'displayfield',
                fieldLabel: 'Total de pagos entre las fechas',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'center'
            },
            {
                xtype: 'displayfield',
                id: 'strFechaDesdeFormFCG',
                value: '',
                renderer: Ext.util.Format.dateRenderer('d/m/Y'),
                textAlign: 'left'
            },
            {
                xtype: 'displayfield',
                id: 'strFechaHastaFormFCG',
                value: '',
                renderer: Ext.util.Format.dateRenderer('d/m/Y'),
                textAlign: 'left'
            },
            {
                colspan: 2,
                xtype: 'displayfield',
                labelAlign: 'top',
                fieldLabel: 'Pendientes',
                labelStyle: 'font-weight:bold;',
                textAlign: 'center'
            },
            {
                id: 'intTotalPagosPendienteFormFCG',
                xtype: 'displayfield',
                value: 'N°. 0',
                textAlign: 'center'
            },
            {
                id: 'intTotalValorPendienteFormFCG',
                xtype: 'displayfield',
                value: '$ 0',
                textAlign: 'center'
            },
            {
                colspan: 2,
                xtype: 'displayfield',
                labelAlign: 'top',
                fieldLabel: 'Reversados',
                labelStyle: 'font-weight:bold;',
                textAlign: 'center'
            },
            {
                id: 'intTotalPagosReverEliminadoFormFCG',
                xtype: 'displayfield',
                value: 'N°. 0',
                textAlign: 'center'
            },
            {
                id: 'intTotalValorReverEliminadoFormFCG',
                xtype: 'displayfield',
                value: '$ 0',
                textAlign: 'center'
            },
            {
                colspan: 2,
                xtype: 'displayfield',
                labelAlign: 'top',
                fieldLabel: 'Conciliados',
                labelStyle: 'font-weight:bold; align: center;',
                textAlign: 'center'
            },
            {
                id: 'intTotalPagosConciliadoFormFCG',
                xtype: 'displayfield',
                value: 'N°. 0',
                textAlign: 'center'
            },
            {
                id: 'intTotalValorConciliadoFormFCG',
                xtype: 'displayfield',
                value: '$ 0',
                textAlign: 'center'
            }
        ]
    });

    //formulario que contiene la sumatoria del grid de pagos agupados por fecha
    formReportePagosLineaFechaGroup = Ext.create('Ext.form.Panel', {
        renderTo: 'formReportePagosLineaFechaGroup',
        width: 190,
        bodyPadding: 10,
        autoScroll: true,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 2,
            pack: 'center'
        },
        items: [
            {
                colspan: 2,
                labelAlign: 'top',
                xtype: 'displayfield',
                fieldLabel: 'Total de pagos entre las fechas',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'center'
            },
            {
                xtype: 'displayfield',
                id: 'strFechaDesdeFormFG',
                value: '',
                renderer: Ext.util.Format.dateRenderer('d/m/Y'),
                textAlign: 'left'
            },
            {
                xtype: 'displayfield',
                id: 'strFechaHastaFormFG',
                value: '',
                renderer: Ext.util.Format.dateRenderer('d/m/Y'),
                textAlign: 'left'
            },
            {
                colspan: 2,
                xtype: 'displayfield',
                labelAlign: 'top',
                fieldLabel: 'Pendientes',
                labelStyle: 'font-weight:bold;',
                textAlign: 'center'
            },
            {
                id: 'intTotalPagosPendienteFormFG',
                xtype: 'displayfield',
                value: 'N°. 0',
                textAlign: 'center'
            },
            {
                id: 'intTotalValorPendienteFormFG',
                xtype: 'displayfield',
                value: '$ 0',
                textAlign: 'center'
            },
            {
                colspan: 2,
                xtype: 'displayfield',
                labelAlign: 'top',
                fieldLabel: 'Reversados',
                labelStyle: 'font-weight:bold;',
                textAlign: 'center'
            },
            {
                id: 'intTotalPagosReverEliminadoFormFG',
                xtype: 'displayfield',
                value: 'N°. 0',
                textAlign: 'center'
            },
            {
                id: 'intTotalValorReverEliminadoFormFG',
                xtype: 'displayfield',
                value: '$ 0',
                textAlign: 'center'
            },
            {
                colspan: 2,
                xtype: 'displayfield',
                labelAlign: 'top',
                fieldLabel: 'Conciliados',
                labelStyle: 'font-weight:bold; align: center;',
                textAlign: 'center'
            },
            {
                id: 'intTotalPagosConciliadoFormFG',
                xtype: 'displayfield',
                value: 'N°. 0',
                textAlign: 'center'
            },
            {
                id: 'intTotalValorConciliadoFormFG',
                xtype: 'displayfield',
                value: '$ 0',
                textAlign: 'center'
            }
        ]
    });

    //formulario que contiene la sumatoria del grid de pagos agupados por canal recaudador
    formReportePagosLineaCanalGroup = Ext.create('Ext.form.Panel', {
        renderTo: 'formReportePagosLineaCanalGroup',
        width: 190,
        bodyPadding: 10,
        autoScroll: true,
        layout: {
            tdAttrs: {style: 'padding: 5px;'},
            type: 'table',
            columns: 2,
            pack: 'center'
        },
        items: [
            {
                colspan: 2,
                labelAlign: 'top',
                xtype: 'displayfield',
                fieldLabel: 'Total de pagos entre las fechas',
                labelStyle: 'font-weight:bold;',
                value: '',
                textAlign: 'center'
            },
            {
                xtype: 'displayfield',
                id: 'strFechaDesdeFormCG',
                value: '',
                type: 'string',
                renderer: Ext.util.Format.dateRenderer('d/m/Y'),
                textAlign: 'left'
            },
            {
                xtype: 'displayfield',
                id: 'strFechaHastaFormCG',
                value: '',
                renderer: Ext.util.Format.dateRenderer('d/m/Y'),
                textAlign: 'left'
            },
            {
                colspan: 2,
                xtype: 'displayfield',
                labelAlign: 'top',
                fieldLabel: 'Pendientes',
                labelStyle: 'font-weight:bold;',
                textAlign: 'center'
            },
            {
                id: 'intTotalPagosPendienteFormCG',
                xtype: 'displayfield',
                value: 'N°. 0',
                textAlign: 'center'
            },
            {
                id: 'intTotalValorPendienteFormCG',
                xtype: 'displayfield',
                value: '$ 0',
                textAlign: 'center'
            },
            {
                colspan: 2,
                xtype: 'displayfield',
                labelAlign: 'top',
                fieldLabel: 'Reversados',
                labelStyle: 'font-weight:bold;',
                textAlign: 'center'
            },
            {
                id: 'intTotalPagosReverEliminadoFormCG',
                xtype: 'displayfield',
                value: 'N°. 0',
                textAlign: 'center'
            },
            {
                id: 'intTotalValorReverEliminadoFormCG',
                xtype: 'displayfield',
                value: '$ 0',
                textAlign: 'center'
            },
            {
                colspan: 2,
                xtype: 'displayfield',
                labelAlign: 'top',
                fieldLabel: 'Conciliados',
                labelStyle: 'font-weight:bold; align: center;',
                textAlign: 'center'
            },
            {
                id: 'intTotalPagosConciliadoFormCG',
                xtype: 'displayfield',
                value: 'N°. 0',
                textAlign: 'center'
            },
            {
                id: 'intTotalValorConciliadoFormCG',
                xtype: 'displayfield',
                value: '$ 0',
                textAlign: 'center'
            }
        ]
    });

    //Panel que muestra los filtros para la busqueda en los grid's
    objFilterPanel = Ext.create('Ext.panel.Panel', {
        border: false,
        buttonAlign: 'center',
        layout: {
            tdAttrs: {style: 'padding: 10px;'},
            type: 'table',
            columns: 3,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 1270,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    var boolError = false;
                    //Si las fechas desde y hasta estan vacias muestra un mensaje y no permite realizar la consulta
                    if ((Ext.getCmp('dateFechaDesde').getValue() != null) && (Ext.getCmp('dateFechaHasta').getValue() != null))
                    {
                        //Si la fecha desde es menor que la fecha hasta muestra un mensaje y no permite hacer la busqueda
                        if (Ext.getCmp('dateFechaDesde').getValue() > Ext.getCmp('dateFechaHasta').getValue())
                        {
                            boolError = true;
                            Ext.Msg.show({
                                title: 'Error',
                                msg: 'La fecha de creación desde debe ser menor que la fecha de creación hasta.',
                                buttons: Ext.Msg.OK,
                                animEl: 'elId',
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                        var fechaDesdePermitido = Ext.getCmp('dateFechaDesde').getValue().getTime();
                        var fechaHastaPermitido = Ext.getCmp('dateFechaHasta').getValue().getTime();

                        var dateFechaDiferencia = Math.abs(fechaDesdePermitido - fechaHastaPermitido)

                        //Convierto de milisegundos a dias
                        var intDias = dateFechaDiferencia / 86400000;

                        if (intDias > 180) {
                            boolError = true;
                            Ext.Msg.show({
                                title: 'Error en Busqueda',
                                msg: 'El rango de busqueda no puede ser mayor a 180 dias ',
                                buttons: Ext.Msg.OK,
                                animEl: 'elId',
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    }
                    else
                    {
                        boolError = true;
                        Ext.Msg.show({
                            title: 'Error',
                            msg: 'Ingrese fecha de creación desde y hasta.',
                            buttons: Ext.Msg.OK,
                            animEl: 'elId',
                            icon: Ext.MessageBox.ERROR
                        });
                    }

                    if (!boolError)
                    {

                        intTotalPagosPendienteFormFCG = 0;
                        intTotalValorPendienteFormFCG = 0;
                        intTotalPagosReverEliminadoFormFCG = 0;
                        intTotalValorReverEliminadoFormFCG = 0;
                        intTotalPagosConciliadoFormFCG = 0;
                        intTotalValorConciliadoFormFCG = 0;

                        //Store que trae la data agrupada por fecha y canal reacaudador
                        storeReportePagosLineaFechaCanalGroup.load({
                            params:
                                {
                                    dateFechaDesde: Ext.getCmp('dateFechaDesde').getValue(),
                                    dateFechaHasta: Ext.getCmp('dateFechaHasta').getValue(),
                                    strCboCanalPagoLinea: Ext.getCmp('cboCanalPagoLinea').getValue().toString(),
                                    strUsrCreacion: Ext.getCmp('txtUsuarioCreacion').getValue(),
                                    strTipoQuery: 'groupFechaCanal'
                                },
                            callback: function(records, operation, success) {
                                storeReportePagosLineaFechaCanalGroup.each(function(rec) {

                                    intTotalPagosPendienteFormFCG += rec.get('intTotalPagosPendiente');
                                    intTotalValorPendienteFormFCG += rec.get('intTotalValorPendiente');
                                    intTotalPagosReverEliminadoFormFCG += rec.get('intTotalPagosReverEliminado');
                                    intTotalValorReverEliminadoFormFCG += rec.get('intTotalValorReverEliminado');
                                    intTotalPagosConciliadoFormFCG += rec.get('intTotalPagosConciliado');
                                    intTotalValorConciliadoFormFCG += rec.get('intTotalValorConciliado');

                                    Ext.getCmp('intTotalPagosPendienteFormFCG').setValue("<b>N°</b> " + intTotalPagosPendienteFormFCG);
                                    Ext.getCmp('intTotalValorPendienteFormFCG').setValue("<b>$</b> " + intTotalValorPendienteFormFCG.toFixed(2));
                                    Ext.getCmp('intTotalPagosReverEliminadoFormFCG').setValue("<b>N°</b> " + intTotalPagosReverEliminadoFormFCG);
                                    Ext.getCmp('intTotalValorReverEliminadoFormFCG').setValue("<b>$</b> " +
                                        intTotalValorReverEliminadoFormFCG.toFixed(2));
                                    Ext.getCmp('intTotalPagosConciliadoFormFCG').setValue("<b>N°</b> " + intTotalPagosConciliadoFormFCG);
                                    Ext.getCmp('intTotalValorConciliadoFormFCG').setValue("<b>$</b> " + intTotalValorConciliadoFormFCG.toFixed(2));

                                });
                            }
                        });

                        Ext.getCmp('strFechaDesdeFormFCG').setValue(Ext.getCmp('dateFechaDesde').getValue());
                        Ext.getCmp('strFechaHastaFormFCG').setValue(Ext.getCmp('dateFechaHasta').getValue());

                        intTotalPagosPendienteFormFG = 0;
                        intTotalValorPendienteFormFG = 0;
                        intTotalPagosReverEliminadoFormFG = 0;
                        intTotalValorReverEliminadoFormFG = 0;
                        intTotalPagosConciliadoFormFG = 0;
                        intTotalValorConciliadoFormFG = 0;

                        //Store que trae la data agrupada por fecha
                        storeReportePagosLineaFechaGroup.load({
                            params:
                                {
                                    dateFechaDesde: Ext.getCmp('dateFechaDesde').getValue(),
                                    dateFechaHasta: Ext.getCmp('dateFechaHasta').getValue(),
                                    strCboCanalPagoLinea: Ext.getCmp('cboCanalPagoLinea').getValue().toString(),
                                    strUsrCreacion: Ext.getCmp('txtUsuarioCreacion').getValue(),
                                    strTipoQuery: 'groupPorFecha'
                                },
                            callback: function(records, operation, success) {
                                storeReportePagosLineaFechaGroup.each(function(rec) {

                                    intTotalPagosPendienteFormFG += rec.get('intTotalPagosPendiente');
                                    intTotalValorPendienteFormFG += rec.get('intTotalValorPendiente');
                                    intTotalPagosReverEliminadoFormFG += rec.get('intTotalPagosReverEliminado');
                                    intTotalValorReverEliminadoFormFG += rec.get('intTotalValorReverEliminado');
                                    intTotalPagosConciliadoFormFG += rec.get('intTotalPagosConciliado');
                                    intTotalValorConciliadoFormFG += rec.get('intTotalValorConciliado');

                                    Ext.getCmp('intTotalPagosPendienteFormFG').setValue("<b>N°</b> " + intTotalPagosPendienteFormFG);
                                    Ext.getCmp('intTotalValorPendienteFormFG').setValue("<b>$</b> " + intTotalValorPendienteFormFG.toFixed(2));
                                    Ext.getCmp('intTotalPagosReverEliminadoFormFG').setValue("<b>N°</b> " + intTotalPagosReverEliminadoFormFG);
                                    Ext.getCmp('intTotalValorReverEliminadoFormFG').setValue("<b>$</b> " +
                                        intTotalValorReverEliminadoFormFG.toFixed(2));
                                    Ext.getCmp('intTotalPagosConciliadoFormFG').setValue("<b>N°</b> " + intTotalPagosConciliadoFormFG);
                                    Ext.getCmp('intTotalValorConciliadoFormFG').setValue("<b>$</b> " + intTotalValorConciliadoFormFG.toFixed(2));

                                });
                            }
                        });

                        Ext.getCmp('strFechaDesdeFormFG').setValue(Ext.getCmp('dateFechaDesde').getValue());
                        Ext.getCmp('strFechaHastaFormFG').setValue(Ext.getCmp('dateFechaHasta').getValue());

                        intTotalPagosPendienteFormCG = 0;
                        intTotalValorPendienteFormCG = 0;
                        intTotalPagosReverEliminadoFormCG = 0;
                        intTotalValorReverEliminadoFormCG = 0;
                        intTotalPagosConciliadoFormCG = 0;
                        intTotalValorConciliadoFormCG = 0;

                        //Store que trae la data agrupada por canal recaudador
                        storeReportePagosLineaCanalGroup.load({
                            params:
                                {
                                    dateFechaDesde: Ext.getCmp('dateFechaDesde').getValue(),
                                    dateFechaHasta: Ext.getCmp('dateFechaHasta').getValue(),
                                    strCboCanalPagoLinea: Ext.getCmp('cboCanalPagoLinea').getValue().toString(),
                                    strUsrCreacion: Ext.getCmp('txtUsuarioCreacion').getValue(),
                                    strTipoQuery: 'groupPorCanal'
                                },
                            callback: function(records, operation, success) {
                                storeReportePagosLineaCanalGroup.each(function(rec) {

                                    intTotalPagosPendienteFormCG += rec.get('intTotalPagosPendiente');
                                    intTotalValorPendienteFormCG += rec.get('intTotalValorPendiente');
                                    intTotalPagosReverEliminadoFormCG += rec.get('intTotalPagosReverEliminado');
                                    intTotalValorReverEliminadoFormCG += rec.get('intTotalValorReverEliminado');
                                    intTotalPagosConciliadoFormCG += rec.get('intTotalPagosConciliado');
                                    intTotalValorConciliadoFormCG += rec.get('intTotalValorConciliado');

                                    Ext.getCmp('intTotalPagosPendienteFormCG').setValue("<b>N°</b> " + intTotalPagosPendienteFormCG);
                                    Ext.getCmp('intTotalValorPendienteFormCG').setValue("<b>$</b> " + intTotalValorPendienteFormCG.toFixed(2));
                                    Ext.getCmp('intTotalPagosReverEliminadoFormCG').setValue("<b>N°</b> " + intTotalPagosReverEliminadoFormCG);
                                    Ext.getCmp('intTotalValorReverEliminadoFormCG').setValue("<b>$</b> " +
                                        intTotalValorReverEliminadoFormCG.toFixed(2));
                                    Ext.getCmp('intTotalPagosConciliadoFormCG').setValue("<b>N°</b> " + intTotalPagosConciliadoFormCG);
                                    Ext.getCmp('intTotalValorConciliadoFormCG').setValue("<b>$</b> " + intTotalValorConciliadoFormCG.toFixed(2));

                                });
                            }
                        });

                        Ext.getCmp('strFechaDesdeFormCG').setValue(Ext.getCmp('dateFechaDesde').getValue());
                        Ext.getCmp('strFechaHastaFormCG').setValue(Ext.getCmp('dateFechaHasta').getValue());
                    }
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    Ext.getCmp('dateFechaDesde').setValue('');
                    Ext.getCmp('dateFechaHasta').setValue('');
                    Ext.getCmp('txtUsuarioCreacion').setValue('');
                    Ext.getCmp('cboCanalPagoLinea').value = '';
                    Ext.getCmp('cboCanalPagoLinea').setRawValue('');
                }
            }

        ],
        items: [
            dateFechaDesde,
            dateFechaHasta,
            cboCanalPagoLinea,
            txtUsuarioCreacion
        ],
        renderTo: 'filtroReportesPagoLinea'
    });

    /**Modelo usado en los grid's que muestran el resumen de los pagos en linea gridReportePagosLineaFechaCanalGroup, gridReportePagosLineaFechaGroup
     * gridReportePagosLineaCanalGroup
     */
    Ext.define('modelReportePagosLinea', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'strFechaPago', mapping: 'FECHA_PAGO', type: 'string'},
            {name: 'strEmpresa', mapping: 'EMPRESA', type: 'string'},
            {name: 'strCodEmpresa', mapping: 'COD_EMPRESA', type: 'string'},
            {name: 'intIdCanal', mapping: 'ID_CANAL', type: 'int'},
            {name: 'strFechaNombreCanal', mapping: 'FECHA_NOMBRE_CANAL', type: 'string'},
            {name: 'strUsrCreacion', mapping: 'USR_CREACION', type: 'string'},
            {name: 'strCanalRecaudador', mapping: 'CANAL', type: 'string'},
            {name: 'intTotalPagosPendiente', mapping: 'TOTAL_PAGOS_PENDIENTE', type: 'int'},
            {name: 'intTotalValorPendiente', mapping: 'TOTAL_VALOR_PENDIENTE', type: 'float'},
            {name: 'intTotalPagosReversado', mapping: 'TOTAL_PAGOS_REVERSADO', type: 'int'},
            {name: 'intTotalValorReversado', mapping: 'TOTAL_VALOR_REVERSADO', type: 'float'},
            {name: 'intTotalPagosEliminado', mapping: 'TOTAL_PAGOS_ELIMINADO', type: 'int'},
            {name: 'intTotalValorEliminado', mapping: 'TOTAL_VALOR_ELIMINADO', type: 'float'},
            {name: 'intTotalPagosReverEliminado', mapping: 'TOTAL_PAGOS_REVER_ELIMINADO', type: 'int'},
            {name: 'intTotalValorReverEliminado', mapping: 'TOTAL_VALOR_REVER_ELIMINADO', type: 'float'},
            {name: 'intTotalPagosConciliado', mapping: 'TOTAL_PAGOS_CONCILIADO', type: 'int'},
            {name: 'intTotalValorConciliado', mapping: 'TOTAL_VALOR_CONCILIADO', type: 'float'},
            {name: 'intValorTotalTransacciones', mapping: 'VALOR_TOTAL_TRANSACCIONES', type: 'float'}
        ]
    });

    //Store que realiza la peticion ajax para obtener la data usado en el grid gridReportePagosLineaFechaCanalGroup
    storeReportePagosLineaFechaCanalGroup = Ext.create('Ext.data.Store', {
        pageSize: 20,
        model: 'modelReportePagosLinea',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: urlGetReportePagosLinea,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonResumenPagosLinea'
            },
            simpleSortMode: true
        }
    });

    //Grid que muestra la data de pagos en linea agrupado por fecha y canal recaudador
    gridReportePagosLineaFechaCanalGroup = Ext.create('Ext.grid.Panel', {
        store: storeReportePagosLineaFechaCanalGroup,
        id: 'gridReportePagosLineaFechaCanalGroup',
        renderTo: 'gridReportePagosLineaFechaCanalGroup',
        title: 'Reporte agrupado por fecha y canal recaudador',
        columns: [
            {header: "Fechas Pagos", dataIndex: 'strFechaPago', width: 80, renderer: function(v) {
                    return Ext.util.Format.substr(v, 0, 10);
                }
            },
            {header: 'Usuario', dataIndex: 'strUsrCreacion', width: 150},
            {header: 'Canal Recaudador', dataIndex: 'strCanalRecaudador', width: 170},
            {header: 'N° Pendientes', dataIndex: 'intTotalPagosPendiente', align: 'center'},
            {
                header: 'Valor Total Pendientes',
                dataIndex: 'intTotalValorPendiente',
                width: 120,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color:#94AE0A; color: white;";
                    return value;
                }
            },
            {header: 'N° Reversados', dataIndex: 'intTotalPagosReverEliminado', align: 'center'},
            {
                header: 'Valor Total Reversados ',
                dataIndex: 'intTotalValorReverEliminado',
                width: 130,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color:#115FA6 ; color: white;";
                    return value;
                }
            },
            {header: 'N° Conciliados', dataIndex: 'intTotalPagosConciliado', align: 'center'},
            {
                header: 'Valor Total Conciliados',
                dataIndex: 'intTotalValorConciliado',
                width: 120,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color: #A61120; color: white;";
                    return value;
                }
            },
            {
                header: 'Valor Total Transacciones ',
                dataIndex: 'intValorTotalTransacciones',
                width: 140,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color:#FFA500; color: white;";
                    return value;
                }
            },
            {
                xtype: 'actioncolumn',
                header: 'Exportar',
                align: 'center',
                width: 50,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-excel-small';
                        },
                        tooltip: 'Seleccionar',
                        handler: function(grid, rowIndex, colIndex) {
                            var recordGrid = storeReportePagosLineaFechaCanalGroup.getAt(rowIndex);
                            Ext.MessageBox.confirm(
                                'Exportar Excel',
                                '¿ Generar reporte?',
                                function(btn) {
                                    if (btn === 'yes') {
                                        window.location = urlGetExportReportePagosLinea + '?strFechaInicio=' + recordGrid.get('strFechaPago')
                                            + '&strFechaFin=' + recordGrid.get('strFechaPago')
                                            + '&intIdCanal=' + recordGrid.get('intIdCanal')
                                            + '&strCodEmpresa=' + recordGrid.get('strCodEmpresa')
                                            + '&strUsrCreacion=' + recordGrid.get('strUsrCreacion');
                                    }
                                });
                        }
                    }
                ]
            }
        ],
        height: 450,
        width: 1275
    });

    //Chart usadp para graficar los pagos en linea agrupados por fecha y canal recaudador
    chartReportePagosLineaFechaCanalGroup = Ext.create('Ext.chart.Chart', {
        animate: true,
        renderTo: 'chartReportePagosLineaFechaCanalGroup',
        width: 1225,
        height: 600,
        store: storeReportePagosLineaFechaCanalGroup,
        legend: {
            position: 'right'
        },
        axes: [{
                type: 'Numeric',
                position: 'bottom',
                fields: ['intTotalValorPendiente',
                    'intTotalValorReverEliminado',
                    'intTotalPagosConciliado',
                    'intTotalPagosPendiente',
                    'intTotalPagosReverEliminado',
                    'intTotalPagosConciliado'],
                title: '[# | $] Pagos',
                grid: true,
                label: {
                    renderer: function(v) {
                        return String(v);
                    }
                }
            }, {
                type: 'Category',
                position: 'left',
                fields: ['strFechaNombreCanal'],
                title: 'Fecha - Nombre Canal Recaudador',
                label: {
                    renderer: function(v) {
                        strFechaNombre = String(v);
                        return String(strFechaNombre.replace(/00:00:00/g, ""));
                    }
                }
            }],
        series: [{
                type: 'bar',
                axis: 'bottom',
                gutter: 80,
                xField: 'strFechaNombreCanal',
                title: ['$ Pendientes', '$ Reversados', '$ Conciliados', '# Pendientes', '# Reversados', '# Conciliados'],
                yField: ['intTotalValorPendiente',
                    'intTotalValorReverEliminado',
                    'intTotalValorConciliado',
                    'intTotalPagosPendiente',
                    'intTotalPagosReverEliminado',
                    'intTotalPagosConciliado'],
                stacked: true,
                tips: {
                    trackMouse: true,
                    width: 65,
                    height: 28,
                    renderer: function(storeItem, item) {
                        this.setTitle(String(item.value[1]));
                    }
                }
            }]
    });

    //Store que realiza la peticion ajax para obtener la data usado en el grid gridReportePagosLineaFechaGroup
    storeReportePagosLineaFechaGroup = Ext.create('Ext.data.Store', {
        pageSize: 20,
        model: 'modelReportePagosLinea',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: urlGetReportePagosLinea,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonResumenPagosLinea'
            },
            simpleSortMode: true
        }
    });

    //Grid que muestra la data de pagos en linea agrupado por fecha
    gridReportePagosLineaFechaGroup = Ext.create('Ext.grid.Panel', {
        store: storeReportePagosLineaFechaGroup,
        id: 'gridReportePagosLineaFechaGroup',
        renderTo: 'gridReportePagosLineaFechaGroup',
        title: 'Reporte agrupado por fecha',
        autoScroll: true,
        columns: [
            {header: "Fechas Pagos", dataIndex: 'strFechaPago', width: 100, renderer: function(v) {
                    return Ext.util.Format.substr(v, 0, 10);
                }
            },
            {header: 'N° Pendientes', dataIndex: 'intTotalPagosPendiente', align: 'center'},
            {
                header: 'Valor Total Pendientes',
                dataIndex: 'intTotalValorPendiente',
                width: 120,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color:#94AE0A; color: white;";
                    return value;
                }
            },
            {header: 'N° Reversados', dataIndex: 'intTotalPagosReverEliminado', align: 'center'},
            {
                header: 'Valor Total Reversados ',
                dataIndex: 'intTotalValorReverEliminado',
                width: 130,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color:#115FA6 ; color: white;";
                    return value;
                }
            },
            {header: 'N° Conciliados', dataIndex: 'intTotalPagosConciliado', align: 'center'},
            {
                header: 'Valor Total Conciliados',
                dataIndex: 'intTotalValorConciliado',
                width: 120,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color: #A61120; color: white;";
                    return value;
                }
            },
            {
                header: 'Valor Total Transacciones ',
                dataIndex: 'intValorTotalTransacciones',
                width: 140,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color:#FFA500; color: white;";
                    return value;
                }
            }
        ],
        height: 260,
        width: 930
    });

    //Chart usadp para graficar los pagos en linea agrupados por fecha
    chartReportePagosLineaFechaGroup = Ext.create('Ext.chart.Chart', {
        animate: true,
        renderTo: 'chartReportePagosLineaFechaGroup',
        width: 1225,
        height: 600,
        store: storeReportePagosLineaFechaGroup,
        legend: {
            position: 'right'
        },
        axes: [{
                type: 'Numeric',
                position: 'bottom',
                fields: ['intTotalValorPendiente',
                    'intTotalValorReverEliminado',
                    'intTotalPagosConciliado',
                    'intTotalPagosPendiente',
                    'intTotalPagosReverEliminado',
                    'intTotalPagosConciliado'],
                title: '[# | $] Pagos',
                grid: true,
                label: {
                    renderer: function(v) {
                        return String(v);
                    }
                }
            }, {
                type: 'Category',
                position: 'left',
                fields: ['strFechaPago'],
                title: 'Fecha',
                label: {
                    renderer: function(v) {
                        strFechaNombre = String(v);
                        return String(strFechaNombre.replace(/00:00:00/g, ""));
                    }
                }
            }],
        series: [{
                type: 'bar',
                axis: 'bottom',
                gutter: 80,
                xField: 'strFechaPago',
                title: ['$ Pendientes', '$ Reversados', '$ Conciliados', '# Pendientes', '# Reversados', '# Conciliados'],
                yField: ['intTotalValorPendiente',
                    'intTotalValorReverEliminado',
                    'intTotalValorConciliado',
                    'intTotalPagosPendiente',
                    'intTotalPagosReverEliminado',
                    'intTotalPagosConciliado'],
                stacked: true,
                tips: {
                    trackMouse: true,
                    width: 65,
                    height: 28,
                    renderer: function(storeItem, item) {
                        this.setTitle(String(item.value[1]));
                    }
                }
            }]
    });

    //Store que realiza la peticion ajax para obtener la data usado en el grid gridReportePagosLineaCanalGroup
    storeReportePagosLineaCanalGroup = Ext.create('Ext.data.Store', {
        pageSize: 20,
        model: 'modelReportePagosLinea',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: urlGetReportePagosLinea,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'jsonResumenPagosLinea'
            },
            simpleSortMode: true
        }
    });

    //Grid que muestra la data de pagos en linea agrupado por canal recaudador
    gridReportePagosLineaCanalGroup = Ext.create('Ext.grid.Panel', {
        title: 'Reporte agrupado por canal recaudador',
        store: storeReportePagosLineaCanalGroup,
        id: 'gridReportePagosLineaCanalGroup',
        renderTo: 'gridReportePagosLineaCanalGroup',
        autoScroll: true,
        columns: [
            {header: 'Canal Recaudador', dataIndex: 'strCanalRecaudador', width: 170},
            {header: 'N° Pendientes', dataIndex: 'intTotalPagosPendiente', align: 'center'},
            {
                header: 'Valor Total Pendientes',
                dataIndex: 'intTotalValorPendiente',
                width: 120,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color:#94AE0A; color: white;";
                    return value;
                }
            },
            {header: 'N° Reversados', dataIndex: 'intTotalPagosReverEliminado', align: 'center'},
            {
                header: 'Valor Total Reversados ',
                dataIndex: 'intTotalValorReverEliminado',
                width: 130,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color:#115FA6 ; color: white;";
                    return value;
                }
            },
            {header: 'N° Conciliados', dataIndex: 'intTotalPagosConciliado', align: 'center'},
            {
                header: 'Valor Total Conciliados',
                dataIndex: 'intTotalValorConciliado',
                width: 120,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color: #A61120; color: white;";
                    return value;
                }
            },
            {
                header: 'Valor Total Transacciones ',
                dataIndex: 'intValorTotalTransacciones',
                width: 140,
                align: 'right',
                renderer: function(value, meta) {
                    meta.style = "background-color:#FFA500; color: white;";
                    return value;
                }
            },
            {
                xtype: 'actioncolumn',
                header: 'Exportar',
                align: 'center',
                width: 50,
                items: [
                    {
                        getClass: function(v, meta, rec) {
                            return 'button-grid-excel-small';
                        },
                        tooltip: 'Seleccionar',
                        handler: function(grid, rowIndex, colIndex) {
                            var recordGrid = storeReportePagosLineaCanalGroup.getAt(rowIndex);
                            Ext.MessageBox.confirm(
                                'Exportar Excel',
                                '¿General reporte?',
                                function(btn) {
                                    if (btn === 'yes') {
                                        window.location = urlGetExportReportePagosLinea + '?strFechaInicio='
                                            + Ext.util.Format.date(Ext.getCmp('strFechaDesdeFormCG').getValue(), 'd-m-Y')
                                            + '&strFechaFin=' + Ext.util.Format.date(Ext.getCmp('strFechaHastaFormCG').getValue(), 'd-m-Y')
                                            + '&intIdCanal=' + recordGrid.get('intIdCanal');
                                    }
                                });
                        }
                    }
                ]
            }
        ],
        height: 250,
        width: 1045
    });

    //Chart usadp para graficar los pagos en linea agrupados por canal recaudador
    chartReportePagosLineaCanalGroup = Ext.create('Ext.chart.Chart', {
        animate: true,
        renderTo: 'chartReportePagosLineaCanalGroup',
        width: 1225,
        height: 600,
        store: storeReportePagosLineaCanalGroup,
        legend: {
            position: 'right'
        },
        axes: [{
                type: 'Numeric',
                position: 'bottom',
                fields: ['intTotalValorPendiente',
                    'intTotalValorReverEliminado',
                    'intTotalPagosConciliado',
                    'intTotalPagosPendiente',
                    'intTotalPagosReverEliminado',
                    'intTotalPagosConciliado'],
                title: '[# | $] Pagos',
                grid: true,
                label: {
                    renderer: function(v) {
                        return String(v);
                    }
                }
            }, {
                type: 'Category',
                position: 'left',
                fields: ['strCanalRecaudador'],
                title: 'Canal recaudador'
            }],
        series: [{
                type: 'bar',
                axis: 'bottom',
                gutter: 80,
                xField: 'strCanalRecaudador',
                title: ['$ Pendientes', '$ Reversados', '$ Conciliados', '# Pendientes', '# Reversados', '# Conciliados'],
                yField: ['intTotalValorPendiente',
                    'intTotalValorReverEliminado',
                    'intTotalValorConciliado',
                    'intTotalPagosPendiente',
                    'intTotalPagosReverEliminado',
                    'intTotalPagosConciliado'],
                stacked: true,
                tips: {
                    trackMouse: true,
                    width: 65,
                    height: 28,
                    renderer: function(storeItem, item) {
                        this.setTitle(String(item.value[1]));
                    }
                }
            }]
    });

});