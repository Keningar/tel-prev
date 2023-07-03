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

    cmbJurisdiccion = Ext.create('Ext.data.comboJurisdiccion', {
        id: 'cmbJurisdiccion',
        name: 'cmbJurisdiccion'});
    storeJurisdiccion.on('load', function()
    {
        cmbJurisdiccion.setValue(intJurisdiccionId);
        cmbJurisdiccion.setRawValue(strNombreJurisdiccion);
        $('#cmbJurisdiccion').val(intJurisdiccionId);
    });

    cmbJurisdiccion.on('select', function(combo, record)
    {
        intJurisdiccionId = this.getValue();
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
                {name: 'cupoMobile', type: 'int'}
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
                timeout: 120000,
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
    Ext.getCmp('cmbJurisdiccion').setValue(intJurisdiccionId);
    //cmbJurisdiccion.setRawValue(intJurisdiccionId);


    //validaciones que se cargan segun el tipo de elemento
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {clicksToEdit: 1});
    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        clicksToMoveEditor: 1,
        autoCancel: false
    });

    gridPlantilla = Ext.create('Ext.grid.Panel',
        {
            id: 'gridPlantilla',
            name: 'gridPlantilla',
            store: storeHorario,
            //renderTo: Ext.get('horario_grid'),
            width: 580,
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
                            case 'horaInicio' :
                                intTotal = storeHorario.getCount();
                                strError = ''
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
                                    /*record.fields.each(function(field)
                                     {
                                     var fieldValue = record.get(field.name);
                                     console.log(fieldValue);
                                     });*/
                                });
                                if (strError !== '')
                                {
                                    Ext.Msg.alert("Error - Creacion de Plantilla de Horarios", strError);
                                    context.record.set('horaInicio', '');
                                }
                                break;
                            case 'horaFin' :
                                intTotal = storeHorario.getCount();
                                strError = ''
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
                                    /*record.fields.each(function(field)
                                     {
                                     var fieldValue = record.get(field.name);
                                     console.log(fieldValue);
                                     });*/
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
                                dateFormat: 'h:i A'
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
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 55,
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
                                                strRegistrosEliminados += storeHorario.getAt(rowIndex).data.idPlantillaHorarioDet + ","
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
                                var r = Ext.create('PlantilaHorarioModel',
                                    {
                                        idPlantillaHorarioDet: '0',
                                        plantillaHorarioId: intHorarioCabeceraId,
                                        horaInicio: '',
                                        horaFin: '',
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
        width: 620,
        title: 'Editar Plantilla de Horario',
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
                    var strDescripcion = form.findField('objTxtDescripcion');
                    var intCupoWeb = form.findField('objTxtCuposWeb');
                    var intCupoMobile = form.findField('objTxtCuposMobile');
                    var intCupoTotal = form.findField('objTxtCuposTotal');
                    if (strDescripcion.getValue().length == 0)
                    {
                        Ext.Msg.alert("Error - Creación de Plantillas de Horarios", "Debe digitar un nombre para la plantilla");
                        //document.forms['formPlantillaDet'].objTxtDescripcion.focus();
                        strDescripcion.focus();
                        return false;
                    }
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
                            horaMenor = arrayGridCreaPlantillaDet.getStore().getAt(0).data.horaInicio
                            horaMayor = arrayGridCreaPlantillaDet.getStore().getAt(0).data.horaFin
                            //Itera el grid gridPlantillaDet y realiza un push en la variable arrayPlantillaDetData
                            for (var intCounterStore = 0;
                                intCounterStore < arrayGridCreaPlantillaDet.getStore().getCount(); intCounterStore++)
                            {
                                horaMenor = Math.min(arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data.horaInicio, horaMenor);
                                horaMayor = Math.max(arrayGridCreaPlantillaDet.getStore().getAt(intCounterStore).data.horaFin, horaMayor);
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
                        var chkEsDefault = form.findField('objChkDefault')
                        var data = form.getValues();
                        Ext.get(document.body).mask('Guardando datos...');
                        Ext.Ajax.request({
                            url: urlSavePlantillaHorario,
                            method: 'POST',
                            params: {
                                data,
                                objTxtDescripcion: strDescripcion.getValue(),
                                jsonPlantillaDet: jsonCreaPlantillaDet,
                                intIdPlantilla: intHorarioCabeceraId,
                                strDetalleEliminiado: strRegistrosEliminados,
                                intCupoWeb: intCupoWeb.getValue(),
                                intCupoMobile: intCupoMobile.getValue(),
                                intCupoTotal: intCupoTotal.getValue(),
                                intJurisdiccion: intJurisdiccionId
                            },

                            success: function(response) {
                                Ext.get(document.body).unmask();
                                var json = Ext.JSON.decode(response.responseText);
                                //Ext.Msg.alert('Mensaje', json.strMessageStatus);
                                if (json.strStatus == 'OK')
                                {
                                    Ext.Msg.alert('Edición de Plantilla de Horarios ', json.strMessageStatus);
                                    //window.location.href = strUrlIndex;
                                } else
                                {
                                    Ext.Msg.alert('Error - Edición de Plantilla de Horarios ', json.strMessageStatus);
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
                    //this.up('form').getForm().reset();
                    //storeHorario.removeAll();
                    window.location.href = strUrlIndex;
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
    objTxtDescripcion.value = strDescripcion;

    var objChkDefault = [
        {
            id: 'objChkDefault',
            name: 'objChkDefault',
            colSpan: 1,
            xtype: 'checkbox',
            boxLabel: 'Plantilla Predeterminada',
            checked: boolDefault,
            cls: 'red',
            width: 150,
            columns: 1
        }];

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
                                    width: '250px',
                                    border: false,
                                    frame: false

                                },
                            items: [objTxtDescripcion,
                                {
                                    xtype: 'numberfield',
                                    anchor: '100%',
                                    name: 'objTxtCuposWeb',
                                    fieldLabel: 'Cupos Telcos',
                                    value: intCuposTelcos,
                                    maxValue: 99,
                                    minValue: 0
                                },
                                {
                                    xtype: 'numberfield',
                                    anchor: '100%',
                                    name: 'objTxtCuposTotal',
                                    fieldLabel: 'Total Cupos',
                                    value: intCuposTotal,
                                    maxValue: 999,
                                    minValue: 0
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            defaultType: 'combobox',
                            style: "font-weight:bold; margin-bottom: 15px; border:none",
                            defaults:
                                {
                                    width: '350px',
                                    border: false,
                                    frame: false

                                },
                            items: [cmbJurisdiccion,
                                {
                                    xtype: 'numberfield',
                                    anchor: '100%',
                                    name: 'objTxtCuposMobile',
                                    fieldLabel: 'Cupos Movil',
                                    value: intCuposMobile,
                                    maxValue: 99,
                                    minValue: 0
                                }]
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
            width: 620,
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
    formPlantillaDet.getForm().findField('objTxtDescripcion').setValue(strDescripcion);
    //formPlantillaDet.add(objChkDefault);
    //formPlantillaDet.add(gridPlantilla);

});


