

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
    strHoraInicio = Ext.Date.format(hora, 'H:i');
    hora.setHours(strHoraFin);
    strHoraFin = Ext.Date.format(hora, 'H:i');
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
                timeout: 90000,
                extraParams: {agendaCupoId: idCabeceraCupo},
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                }
            }

        });


    //validaciones que se cargan segun el tipo de elemento
    //var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {clicksToEdit: 1});


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
            //plugins: [cellEditing],
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
                                strError = ''
                                rowActual = storeHorario.indexOf(context.record);
                                storeHorario.each(function(record)
                                {
                                    rowIndex = storeHorario.indexOf(record);
                                    if (rowActual !== rowIndex)
                                    {
                                        valorHoraInicio = record.get('horaDesde');
                                        valorHoraFin = record.get('horaHasta');
                                        if (valor.getTime() == record.get('horaDesde').getTime())
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
                                        valorHoraInicio = record.get('horaDesde');
                                        valorHoraFin = record.get('horaHasta');
                                        if (valor.getTime() == record.get('horaHasta').getTime())
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
                        dataIndex: 'horaHasta',
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
                }});

    formPlantillaDet = Ext.create('Ext.form.Panel', {
        bodyStyle: 'padding: 0px 0px 0; background:#FFFFFF;',
        //bodyPadding: 15,
        width: 850,
        title: 'Mostrar Agenda de Cupos',
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
                                    html: 'Plantilla: ' + strDescripcion + "\n"
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: "Total Cupos ",
                                    value: intTotalCupos,
                                    width: 160,
                                    readOnly: true
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
                                    html: 'Jurisdiccion: ' + strJurisdiccion
                                }]
                        }]
                }]
        });

    var container2 = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'center'},
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


    //formPlantillaDet.add(objChkDefault);
    //formPlantillaDet.add(gridPlantilla);

});


