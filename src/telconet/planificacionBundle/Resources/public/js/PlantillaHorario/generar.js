/* global Ext*/

//ar strDescripcion = '';
function mensajeHora(intHora)
{
    var strMensaje = intHora.substr(0, 1) + " Hora";
    if (intHora % 1 == 0)
    {
        if (intHora > 1)
        {
            strMensaje += "s";
        }
    } else
    {
        strMensaje += " y Media";
    }
    return strMensaje;
}

Ext.onReady(function() {
    var hora = new Date();
    hora.setHours(strHoraInicio);
    strHoraInicio = Ext.Date.format(hora, 'h:00 A');
    hora.setHours(strHoraFin);
    strHoraFin = Ext.Date.format(hora, 'h:00 A');
    var horaMinima = 0;
    var horaMaxima = 0;
    var unaHora = 60 * 60 * 1000;

    DTFechaDesde = Ext.create('Ext.data.fecha', {
        id: 'fechaDesde',
        name: 'fechaDesde',
        fieldLabel: 'Desde',
        minValue: strFechaMinima,
        value: strFechaMinima

    });

    DTFechaHasta = Ext.create('Ext.data.fecha', {
        id: 'fechaHasta',
        name: 'fechaHasta',
        fieldLabel: 'Hasta',
        minValue: strFechaMinima,
        value: strFechaMinima
    });



    Ext.define('PlantilaHorarioModel',
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idPlantillaHorarioDet', type: 'int'},
                {name: 'plantillaHorarioId', type: 'int'},
                {name: 'horaInicio', type: 'time'},
                {name: 'horaFin', type: 'time'},
                {name: 'almuerzo', type: 'string'},
                {name: 'cupoWeb', type: 'int'},
                {name: 'cupoMobile', type: 'int'},
                {name: 'observacion', type: 'string'}
            ]
        });

    storeHorario = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'PlantilaHorarioModel',
            autoload: true,
            proxy: {
                type: 'ajax',
                url: ajaxGetPlantillaDetalle,
                timeout: 1200000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                params: {
                    intHorarioCabeceraId: intHorarioCabeceraId
                }
            }
        });
    storeHorario.load({//step 4
        params: {
            intHorarioCabeceraId: intHorarioCabeceraId	//step 5
        }
    });

    //validaciones que se cargan segun el tipo de elemento
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {clicksToEdit: 1});

    gridPlantilla = Ext.create('Ext.grid.Panel',
        {
            id: 'gridPlantilla',
            name: 'gridPlantilla',
            store: storeHorario,
            //renderTo: Ext.get('horario_grid'),
            width: 780,
            height: 250,
            title: '',
            plugins: [cellEditing],
            listeners:
                {
                    edit: function(ed, context)
                    {
                        var valor = context.record.get(context.column.dataIndex)
                        switch (context.column.dataIndex)
                        {
                            case 'horaInicio' :
                                intTotal = storeHorario.getCount();
                                strError = '';
                                rowActual = storeHorario.indexOf(context.record);
                                storeHorario.each(function(record)
                                {
                                    rowIndex = storeHorario.indexOf(record);
                                    if (rowActual !== rowIndex)
                                    {
                                        valorHoraInicio = record.get('horaInicio');
                                        valorHoraFin = record.get('horaFin');
                                        if (valor.getTime() == record.get('horaInicio').getTime())
                                        {
                                            strError = " No se puede ingresar mas una vez la misma hora de Inicio";
                                        }
                                        if (valor.getTime() > valorHoraInicio.getTime() && valor.getTime() < valorHoraFin.getTime())
                                        {
                                            strError = " Hora de inicio no puede estar dentro de un rango ya asignado";
                                        }
                                    }

                                });
                                if (strError !== '')
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", strError);
                                    context.record.set('horaInicio', '');
                                }
                                break;
                            case 'horaFin' :
                                intTotal = storeHorario.getCount();
                                strError = '';
                                rowActual = storeHorario.indexOf(context.record);
                                storeHorario.each(function(record)
                                {
                                    rowIndex = storeHorario.indexOf(record);
                                    if (rowActual !== rowIndex)
                                    {
                                        valorHoraInicio = record.get('horaInicio');
                                        valorHoraFin = record.get('horaFin');
                                        if (valor.getTime() == record.get('horaFin').getTime())
                                        {
                                            strError = " No se puede ingresar mas una vez la misma hora de Finalización";
                                        }
                                        if (valor.getTime() > valorHoraInicio.getTime() && valor.getTime() < valorHoraFin.getTime())
                                        {
                                            strError = " Hora de Fin no puede estar dentro de un rango ya asignado";
                                        }
                                    }

                                });
                                if (strError !== '')
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", strError);
                                    context.record.set('horaFin', '');
                                }

                                //valido que las horas nos sean iguales
                                if (context.record.get('horaInicio').getTime() == valor.getTime())
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", "Hora de Inicio no puede ser igual a la hora de Finalizacion");
                                    //ed.cancelEdit();
                                    context.record.set('horaFin', '');
                                }
                                if (context.record.get('horaInicio').getTime() > valor.getTime())
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", "Hora de Fin no puede ser menor a la hora de Inicio");
                                    context.record.set('horaFin', '');
                                }
                                var diferencia = (valor.getTime() - context.record.get('horaInicio').getTime()) / unaHora;

                                if (diferencia > intIntervaloMaximo)
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", "Tiempo Maximo por rango es " + mensajeHora(intIntervaloMaximo));
                                    context.record.set('horaFin', '');
                                }
                                break;
                        }
                        context.record.set('horaInicio1', context.record.get('horaInicio'));
                    }
                },
            columns:
                [
                    {
                        header: 'Hora Inicio',
                        dataIndex: 'horaInicio',
                        align: 'right',
                        renderer: Ext.util.Format.dateRenderer('H:i'),
                        editor:
                            {
                                width: '200',
                                xtype: 'timefield',
                                reference: 'timeField',
                                format: 'H:i',
                                allowBlank: false,
                                minValue: strHoraInicio,
                                maxValue: strHoraFin,
                                increment: 30,
                                dateFormat: 'H:i'
                            }
                    },
                    {
                        header: 'Hora Fin',
                        dataIndex: 'horaFin',
                        align: 'right',
                        renderer: Ext.util.Format.dateRenderer('H:i'),
                        editor:
                            {
                                width: '200',
                                xtype: 'timefield',
                                reference: 'timeField',
                                format: 'H:i',
                                allowBlank: false,
                                minValue: strHoraInicio,
                                maxValue: strHoraFin,
                                increment: 30,
                                dateFormat: 'H:i'
                            }
                    },
                    {
                        header: 'Cupos Web',
                        dataIndex: 'cupoWeb',
                        align: 'right',
                        editor:
                            {
                                width: '200',
                                xtype: 'numberfield',
                                anchor: '100%',
                                value: 1,
                                maxValue: 99,
                                minValue: 0
                            }
                    },
                    {
                        header: 'Cupos Movil',
                        dataIndex: 'cupoMobile',
                        align: 'right',
                        editor:
                            {
                                width: '200',
                                xtype: 'numberfield',
                                anchor: '100%',
                                value: 1,
                                maxValue: 99,
                                minValue: 0
                            }
                    },
                    {
                        header: 'Observación',
                        dataIndex: 'observacion',
                        align: 'left',
                        width: 300,
                        editor:
                            {
                                width: '600',
                                xtype: 'textfield'
                            }
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 75,
                        sortable: false,
                        items:
                            [
                                {
                                    iconCls: "button-grid-delete",
                                    tooltip: 'Borrar Registro de Plantilla',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        Ext.Msg.confirm('Alerta', 'Seguro eliminar el registro?', function(btn) {
                                            if (btn == 'yes') {
                                                storeHorario.removeAt(rowIndex);
                                            }
                                        });
                                    }
                                }
                            ]
                    }
                ],
            selModel:
                {
                    selType: 'cellmodel'
                },
            tbar:
                [
                    {
                        text: 'Agregar',
                        handler: function()
                        {
                            var boolError = false;
                            var indice = 0;
                            for (var i = 0; i < gridPlantilla.getStore().getCount(); i++)
                            {
                                variable = gridPlantilla.getStore().getAt(i).data;
                                boolError = variable['horaInicio'] == '';

                                if (boolError)
                                {
                                    break;
                                } else
                                {
                                    boolError = variable['horaFin'] == '';
                                    if (boolError)
                                    {
                                        indice = 1;
                                        break;
                                    }
                                }
                            }
                            if (!boolError)
                            {
                                //Ext.getCmp('objTxtCuposMobile').getValue();
                                var r = Ext.create('PlantilaHorarioModel',
                                    {
                                        idPlantillaHorarioDet: 0,
                                        plantillaHorarioId: '',
                                        horaInicio: '',
                                        horaFin: '',
                                        almuerzo: '',
                                        cupoWeb: 0,
                                        cupoMobile: 0
                                    });

                                storeHorario.insert(0, r);
                            }
                            var position = gridPlantilla.getStore().getCount();
                            cellEditing.startEditByPosition({row: 0, column: indice});
                        }
                    }
                ]
        });

    formPlantillaDet = Ext.create('Ext.form.Panel', {
        bodyStyle: 'padding: 0px 0px 0; background:#FFFFFF;',
        //bodyPadding: 15,
        width: 900,
        title: 'Generación de Cupos',
        renderTo: 'Div_Edit_Plantilla_Horario',
        frame: true,
        //layout:'vbox',
        layoutConfig: {
            type: 'table',
            columns: 3,
            pack: 'center',
            align: 'middle',
            tableAttrs: {
                style: {
                    width: '90%',
                    height: '90%'
                }
            },
            tdAttrs: {
                align: 'left',
                valign: 'middle'
            }
        },
        buttonAlign: 'center',
        buttons: [
            {
                text: 'Generar',
                name: 'btnGuardar',
                id: 'idBtnGuardar',
                disabled: false,
                handler: function() {
                    var form = formPlantillaDet.getForm();
                    if (form.isValid())
                    {
                        var arrayPlantillaDet = new Object();
                        jsonCreaPlantillaDet = '';
                        var intTotalCupos = 0;
                        var arrayGridCreaPlantillaDet = Ext.getCmp('gridPlantilla');
                        arrayPlantillaDet['inTotal'] = arrayGridCreaPlantillaDet.getStore().getCount();
                        arrayPlantillaDet['arrayData'] = new Array();
                        arrayPlantillaDetData = Array();
                        if (arrayGridCreaPlantillaDet.getStore().getCount() !== 0)
                        {
                            horaMenor = arrayGridCreaPlantillaDet.getStore().getAt(0).data.horaInicio;
                            horaMayor = arrayGridCreaPlantillaDet.getStore().getAt(0).data.horaFin;
                            //Itera el grid gridPlantillaDet y realiza un push en la variable arrayPlantillaDetData
                            for (var intCounterStore = 0;
                                intCounterStore < arrayGridCreaPlantillaDet.getStore().getCount(); intCounterStore++)
                            {
                                horaMenor = Math.min(arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data.horaInicio, horaMenor);
                                horaMayor = Math.max(arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data.horaFin, horaMayor);
                                arrayPlantillaDetData.push(arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data);
                                intTotalCupos += arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data.cupoWeb + arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data.cupoMobile;
                            }
                            var diferenciaTime = (horaMayor - horaMenor) / unaHora;

                            if (diferenciaTime < strMinimoHoras)
                            {
                                Ext.Msg.alert("Error2 - Creación de Plantillas de Horarios", "Debe completar minimo <span><b>" + strMinimoHoras + "</b></span> horas");
                                //strDescripcion.focus();
                                return false;
                            }
                            arrayPlantillaDet['arrayData'] = arrayPlantillaDetData;
                            /*Realiza encode a arrayPlantillaDet para ser enviada por el request
                             * de creación de parámetros al controlador
                             */
                            jsonCreaPlantillaDet = Ext.JSON.encode(arrayPlantillaDet);
                        } else
                        {
                            Ext.Msg.alert("Error - Creación de Plantillas de Horarios", "Debe Registrar minimo <span><b>" + strMinimoHoras + "</b></span> horas laborales");
                            return false;
                        }
                        var form = formPlantillaDet.getForm();
                        var strFechaDesde = form.findField('fechaDesde');

                        var strFechaHasta = form.findField('fechaHasta');
                        //var intPlantilla = form.findField('cmbPlantilla');

                        var data = form.getValues();
                        console.log("data");
                        console.log(intTotalCupos);
                        Ext.get(document.body).mask('Guardando datos...');
                        Ext.Ajax.request({
                            url: urlSaveAgendaCupo,
                            timeout: 1200000,
                            method: 'POST',
                            params: {
                                data,
                                strFechaDesde: strFechaDesde.getValue(),
                                strFechaHasta: strFechaHasta.getValue(),
                                jsonPlantillaDet: jsonCreaPlantillaDet,
                                intPlantilla: intHorarioCabeceraId,
                                intTotalCupos: intTotalCupos
                            },

                            success: function(response) {
                                Ext.get(document.body).unmask();
                                var json = Ext.JSON.decode(response.responseText);
                                //Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                if (json.strStatus == 'OK')
                                {
                                    Ext.Msg.alert('Creacion de Plantilla de Horarios ', json.strMessageStatus);
                                    window.location.href = strUrlIndex;
                                } else
                                {
                                    Ext.Msg.alert('Error - Creacion de Plantilla de Horarios ', json.strMessageStatus);
                                }
                            },
                            failure: function(result) {
                                Ext.get(document.body).unmask();
                                Ext.Msg.alert('Error - ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                }
            },
            {
                text: 'Regresar',
                handler: function() {
                    this.up('form').getForm().reset();
                    storeHorario.removeAll();
                }
            }]
    });

    var objTxtDescripcion = Utils.objText();

    objTxtDescripcion.id = 'objTxtDescripcion';
    objTxtDescripcion.name = 'objTxtDescripcion';
    objTxtDescripcion.fieldLabel = "*Nombre de Plantilla:";
    objTxtDescripcion.labelWidth = 120;
    objTxtDescripcion.colSpan = 2;
    objTxtDescripcion.width = 300;
    objTxtDescripcion.allowBlank = false;
    objTxtDescripcion.blankText = 'Ingrese Nombre de Plantilla';
    objTxtDescripcion.placeHolder = 'aass';
    objTxtDescripcion.hasfocus = true;

    var objChkDefault = [
        {
            id: 'objChkDefault',
            name: 'objChkDefault',
            colSpan: 1,
            xtype: 'checkbox',
            boxLabel: 'Plantilla Predeterminada',
            checked: true,
            cls: 'red',
            width: 150,
            columns: 1
        }];

    var objTxtCuposWeb = [
        {
            xtype: 'numberfield',
            anchor: '100%',
            name: 'objTxtCuposWeb',
            fieldLabel: 'Cupos Telcos',
            value: 1,
            maxValue: 99,
            minValue: 0
        }
    ];

    var objTxtCuposMobile = [
        {
            xtype: 'numberfield',
            anchor: '100%',
            name: 'objTxtCupoMobile',
            fieldLabel: 'Cupos Movil',
            value: 1,
            maxValue: 99,
            minValue: 0
        }
    ];

    var container = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'hbox',
                style: "border:none"
            },
            width: 980,
            items: [
                {
                    xtype: 'panel',
                    border: false,
                    layout: {type: 'hbox', align: 'stretch'},
                    items: [
                        {
                            xtype: 'fieldset',
                            defaultType: 'datefield',
                            style: "font-weight:bold; margin-bottom: 15px; border-left:none; border:none",
                            defaults:
                                {
                                    width: '250px',
                                    border: false,
                                    frame: false

                                },
                            items: [DTFechaDesde,
                                DTFechaHasta]
                        },
                        {
                            xtype: 'fieldset',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 15px; border:none",
                            defaults:
                                {
                                    width: '350px',
                                    border: false,
                                    frame: false
                                },
                            items: [
                                {
                                    xtype: 'label',
                                    html: 'Plantilla: ' + strDescripcion
                                }
                            ]
                        }]
                }]
        });

    var container2 = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'center'
            },
            width: 820,
            items: [
                {
                    xtype: 'panel',
                    border: false,
                    //layout: { type: 'hbox', align: 'stretch' },
                    items: [
                        {
                            items: [gridPlantilla]
                        }]
                }]
        });



    formPlantillaDet.add(container);
    formPlantillaDet.add(container2);

    //formPlantillaDet.add(objChkDefault);
    //formPlantillaDet.add(gridPlantilla);

});


