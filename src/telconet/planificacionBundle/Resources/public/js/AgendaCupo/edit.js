

var strRegistrosEliminados = '';
/*var strDescripcion         = '';*/

function mensajeHora(intHora)
{
    var strMensaje = intHora.substr(0, 1) + " Hora";
    if (intHora % 1 == 0)
    {
        if (intHora > 1)
        {
            strMensaje += "s"
        }
    } else
    {
        strMensaje += " y Media";
    }
    return strMensaje;
}
Ext.onReady(function() {
    var descripcion = strDescripcion;
    var hora = new Date();
    hora.setHours(strHoraInicio);
    strHoraInicio = Ext.Date.format(hora, 'G:i');
    hora.setHours(strHoraFin);
    strHoraFin = Ext.Date.format(hora, 'G:i');
    var unaHora = 60 * 60 * 1000;

    Ext.define('PlantilaHorarioModel',
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'idAgendaCupoDet', type: 'int'},
                {name: 'agendaCupoId', type: 'int'},
                {name: 'horaDesde', type: 'time'},
                {name: 'horaHasta', type: 'time'},
                {name: 'cuposWeb', type: 'int'},
                {name: 'cuposMovil', type: 'int'},
                {name: 'totalCupos', type: 'int'},
                {name: 'observacion', type: 'string'}
            ]
        });

    storeHorario = Ext.create('Ext.data.Store',
        {
            //autoDestroy: true,
            model: 'PlantilaHorarioModel',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                //method: "GET",
                url: url_detalle_cupo,
                timeout: 900000,
                extraParams: {agendaCupoId: idCabeceraCupo},
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                }
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
            width: 750,
            height: 250,
            title: '',
            colspan: 3,
            plugins: [cellEditing],
            //plugins: [rowEditing],
            listeners:
                {
                    edit: function(ed, context)
                    {
                        //console.log(context);
                        //console.log(ed);
                        var valor = context.record.get(context.column.dataIndex)
                        switch (context.column.dataIndex)
                        {
                            case 'horaDesde' :
                                intTotal = storeHorario.getCount();
                                strError = '';
                                rowActual = storeHorario.indexOf(context.record);
                                storeHorario.each(function(record)
                                {
                                    rowIndex = storeHorario.indexOf(record);
                                    if (rowActual !== rowIndex)
                                    {

                                        valorHoraInicio = new Date(record.get('horaDesde'));
                                        valorHoraFin = new Date(record.get('horaHasta'));
                                        if (valor.getTime() == valorHoraInicio.getTime())
                                        {
                                            strError = " No se puede ingresar mas una vez la misma hora de Inicio";
                                        }
                                        if (valor.getTime() > valorHoraInicio.getTime() && valor.getTime() < valorHoraFin.getTime())
                                        {
                                            strError = " Hora de inicio no puede estar dentro de un rango ya asignado";
                                        }
                                    }
                                    /*record.fields.each(function(field)
                                     {
                                     var fieldValue = record.get(field.name);
                                     console.log(fieldValue);
                                     });*/
                                });
                                if (strError !== '')
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", strError);
                                    context.record.set('horaDesde', '');
                                }
                                break;
                            case 'horaHasta' :
                                intTotal = storeHorario.getCount();
                                strError = ''
                                rowActual = storeHorario.indexOf(context.record);
                                storeHorario.each(function(record)
                                {
                                    rowIndex = storeHorario.indexOf(record);
                                    if (rowActual !== rowIndex)
                                    {
                                        valorHoraInicio = new Date(record.get('horaDesde'));
                                        valorHoraFin = new Date(record.get('horaHasta'));
                                        if (valor.getTime() == valorHoraFin.getTime())
                                        {
                                            strError = " No se puede ingresar mas una vez la misma hora de Finalización";
                                        }
                                        if (valor.getTime() > valorHoraInicio.getTime() && valor.getTime() < valorHoraFin.getTime())
                                        {
                                            strError = " Hora de Fin no puede estar dentro de un rango ya asignado";
                                        }
                                    }
                                    /*record.fields.each(function(field)
                                     {
                                     var fieldValue = record.get(field.name);
                                     console.log(fieldValue);
                                     });*/
                                });
                                if (strError !== '')
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", strError);
                                    context.record.set('horaHasta', '');
                                }

                                //valido que las horas nos sean iguales
                                if (context.record.get('horaDesde').getTime() == valor.getTime())
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", "Hora de Inicio no puede ser igual a la hora de Finalizacion");
                                    //ed.cancelEdit();
                                    context.record.set('horaHasta', '');
                                }
                                if (context.record.get('horaDesde').getTime() > valor.getTime())
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", "Hora de Fin no puede ser menor a la hora de Inicio");
                                    context.record.set('horaHasta', '');
                                }
                                var diferencia = (valor.getTime() - context.record.get('horaDesde').getTime()) / unaHora;
                                console.log(diferencia);
                                console.log(intIntervaloMaximo);
                                console.log(diferencia > intIntervaloMaximo);
                                if (diferencia > intIntervaloMaximo)
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", "Tiempo Maximo por rango es " + mensajeHora(intIntervaloMaximo));
                                    context.record.set('horaHasta', '');
                                }
                                break;
                        }
                        context.record.set('horaDesde1', context.record.get('horaDesde'));
                    }
                },
            columns:
                [
                    {
                        header: 'Hora Inicio',
                        dataIndex: 'horaDesde',
                        align: 'right',
                        renderer: Ext.util.Format.dateRenderer('G:i'),
                        editor:
                            {
                                width: '200',
                                xtype: 'timefield',
                                reference: 'timeField',
                                format: 'G:i',
                                allowBlank: false,
                                minValue: strHoraInicio,
                                maxValue: strHoraFin,
                                increment: 30,
                                dateFormat: 'G:i'
                            }
                    },
                    {
                        header: 'Hora Fin',
                        dataIndex: 'horaHasta',
                        align: 'right',
                        renderer: Ext.util.Format.dateRenderer('G:i'),
                        editor:
                            {
                                width: '200',
                                xtype: 'timefield',
                                reference: 'timeField',
                                format: 'G:i',
                                allowBlank: false,
                                minValue: strHoraInicio,
                                maxValue: strHoraFin,
                                increment: 30,
                                dateFormat: 'G:i'
                            }
                    },
                    {
                        header: 'Cupos Web',
                        dataIndex: 'cuposWeb',
                        align: 'right',
                        editor:
                            {
                                width: '130',
                                xtype: 'numberfield',
                                anchor: '100%',
                                value: 1,
                                maxValue: 99,
                                minValue: 0
                            }
                    },
                    {
                        header: 'Cupos Movil',
                        dataIndex: 'cuposMovil',
                        align: 'right',
                        editor:
                            {
                                width: '130',
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
                        width: 280,
                        editor:
                            {
                                width: '600',
                                xtype: 'textfield'
                            }
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
                                boolError = variable['horaDesde'] == '';

                                if (boolError)
                                {
                                    break;
                                } else
                                {
                                    boolError = variable['horaHasta'] == '';
                                    if (boolError)
                                    {
                                        indice = 1;
                                        break;
                                    }
                                }
                            }
                            if (!boolError)
                            {
                                var r = Ext.create('PlantilaHorarioModel',
                                    {
                                        idAgendaCupoDet: '0',
                                        agendaCupoId: idCabeceraCupo,
                                        horaDesde: '',
                                        horaHasta: '',
                                        almuerzo: ''
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
        width: 850,
        title: 'Editar Agenda de Cupos',
        renderTo: 'Div_Edit_Plantilla_Horario',
        frame: true,
        listeners: {
            afterRender: function(thisForm, options) {
            }
        },

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
                text: 'Guardar',
                name: 'btnGuardar',
                id: 'idBtnGuardar',
                disabled: false,
                handler: function() {
                    var form = formPlantillaDet.getForm();
                    if (form.isValid())
                    {
                        var arrayPlantillaDet = new Object();
                        jsonCreaPlantillaDet = '';
                        var arrayGridCreaPlantillaDet = Ext.getCmp('gridPlantilla');
                        arrayPlantillaDet['inTotal'] = arrayGridCreaPlantillaDet.getStore().getCount();
                        arrayPlantillaDet['arrayData'] = new Array();
                        arrayPlantillaDetData = Array();
                        if (arrayGridCreaPlantillaDet.getStore().getCount() !== 0)
                        {
                            horaMenor = arrayGridCreaPlantillaDet.getStore().getAt(0).data.horaDesde
                            horaMayor = arrayGridCreaPlantillaDet.getStore().getAt(0).data.horaHasta
                            //Itera el grid gridPlantillaDet y realiza un push en la variable arrayPlantillaDetData
                            for (var intCounterStore = 0;
                                intCounterStore < arrayGridCreaPlantillaDet.getStore().getCount(); intCounterStore++)
                            {
                                horaMenor = Math.min(arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data.horaDesde, horaMenor);
                                horaMayor = Math.max(arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data.horaHasta, horaMayor);
                                arrayPlantillaDetData.push(arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data);
                            }

                            var diferenciaTime = (horaMayor - horaMenor) / unaHora;

                            if (diferenciaTime < strMinimoHoras)
                            {
                                Ext.Msg.alert("Error2 - Creación de Plantillas de Horarios", "Debe completar minimo <span><b>" + strMinimoHoras + "</b></span> horas");
                                strDescripcion.focus();
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

                        var data = form.getValues();
                        Ext.get(document.body).mask('Guardando datos...');
                        Ext.Ajax.request({
                            url: urlSaveAgenda,
                            timeout: 1000000,
                            method: 'POST',
                            params: {
                                data,
                                jsonPlantillaDet: jsonCreaPlantillaDet,
                                intIdPlantilla: idCabeceraCupo,
                                strDetalleEliminado: strRegistrosEliminados,
                                strFechaPeriodo: strFechaPeriodo
                            },

                            success: function(response) {
                                Ext.get(document.body).unmask();
                                var json = Ext.JSON.decode(response.responseText);
                                console.log(json);
                                //Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                if (json.strStatus == 'OK')
                                {
                                    Ext.Msg.alert('Edición de Plantilla de Horarios ', json.strMessageStatus);
                                    window.location.href = strUrlIndex;
                                } else
                                {
                                    Ext.Msg.alert('Error - Edición de Plantilla de Horarios ', json.strMessageStatus);
                                }
                            },
                            failure: function(result) {
                                console.log("error");
                                console.log(result.statusText);
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
                    //this.up('form').getForm().reset();
                    //storeHorario.removeAll();
                    window.location.href = strUrlIndex;
                }
            }]
    });



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
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 15px; border-left:none; border:none",
                            defaults:
                                {
                                    width: '300px',
                                    border: false,
                                    frame: false

                                },
                            items: [
                                {
                                    xtype: 'label',
                                    html: 'Plantilla: ' + strDescripcion
                                },
                                {
                                    xtype: 'numberfield',
                                    anchor: '100%',
                                    name: 'objTxtCuposTotal',
                                    fieldLabel: 'Total Cupos',
                                    value: intTotalCupos,
                                    fieldStyle: "text-align:right;",
                                    maxValue: 999,
                                    minValue: 0
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            defaultType: 'checkbox',
                            style: "font-weight:bold; margin-bottom: 15px; border:none",
                            defaults:
                                {
                                    width: '250px',
                                    border: false,
                                    frame: false

                                },
                            items: [
                                {
                                    xtype: 'label',
                                    html: 'Jurisdiccion: ' + strJurisdiccion + "<br>"
                                },
                                {
                                    xtype: 'label',
                                    html: 'Fecha: ' + strFechaPeriodo
                                }]
                        }]
                }]
        });

    var container2 = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'center', },
            width: 800,
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
    var boolValue = boolDefault == 'S' ? true : false;

    //formPlantillaDet.add(objChkDefault);
    //formPlantillaDet.add(gridPlantilla);

});


