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
            //autoDestroy: true,
            model: 'PlantilaHorarioModel',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                //method: "GET",
                url: url_detalle_Plantilla,
                timeout: 90000,
                extraParams: {intHorarioCabeceraId: intHorarioCabeceraId},
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
            width: 450,
            height: 250,
            title: '',
            colspan: 3,
            viewConfig:
                {
                    enableTextSelection: true,
                    trackOver: true,
                    stripeRows: true,
                    loadMask: true
                },

            columns:
                [
                    {
                        header: 'Hora Inicio',
                        dataIndex: 'horaInicio',
                        align: 'right',
                        renderer: Ext.util.Format.dateRenderer('H:i'),
                        width: '200',
                        format: 'H:i',
                        allowBlank: false,
                        minValue: strHoraInicio,
                        maxValue: strHoraFin,
                        increment: 30,
                        dateFormat: 'H:i'
                    },
                    {
                        header: 'Hora Fin',
                        dataIndex: 'horaFin',
                        align: 'right',
                        renderer: Ext.util.Format.dateRenderer('H:i'),
                        width: '200',
                        format: 'H:i',
                        allowBlank: false,
                        minValue: strHoraInicio,
                        maxValue: strHoraFin,
                        increment: 30,
                        dateFormat: 'H:i'
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
                    }
                ]
        });

    formPlantillaDet = Ext.create('Ext.form.Panel', {
        bodyStyle: 'padding: 0px 0px 0; background:#FFFFFF;',
        //bodyPadding: 15,
        width: 620,
        title: 'Ver Plantilla de Horario',
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


    var objTxtDescripcion = Utils.objText();

    objTxtDescripcion.id = 'objTxtDescripcion';
    objTxtDescripcion.name = 'objTxtDescripcion';
    objTxtDescripcion.fieldLabel = "Nombre de Plantilla:";
    objTxtDescripcion.labelWidth = 120;
    objTxtDescripcion.colSpan = 2;
    objTxtDescripcion.width = 300;
    objTxtDescripcion.value = strDescripcion;
    objTxtDescripcion.readOnly = true

    var objTxtJurisdiccion = Utils.objText();

    objTxtJurisdiccion.id = 'objTxtJurisdiccion';
    objTxtJurisdiccion.name = 'objTxtJurisdiccion';
    objTxtJurisdiccion.fieldLabel = "Jurisdicción:";
    objTxtJurisdiccion.labelWidth = 60;
    objTxtJurisdiccion.colSpan = 2;
    objTxtJurisdiccion.width = 270;
    objTxtJurisdiccion.value = strJurisdiccion;
    objTxtJurisdiccion.readOnly = true


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
            columns: 1,
            readOnly: true
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
                                    width: '300px',
                                    border: false,
                                    frame: false

                                },
                            items: [objTxtDescripcion]
                        },
                        {
                            xtype: 'fieldset',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 15px; border:none",
                            defaults:
                                {
                                    width: '300px',
                                    border: false,
                                    frame: false

                                },
                            items: [objTxtJurisdiccion]

                        }]
                }]
        });

    var container2 = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'center', },
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
    formPlantillaDet.getForm().findField('objTxtJurisdiccion').setValue(strJurisdiccion);
    //formPlantillaDet.add(objChkDefault);
    //formPlantillaDet.add(gridPlantilla);
    //Ext.getCmp('objTxtDescripcion').getEl().dom.setAttribute('readOnly', true);
    //Ext.getCmp('objChkDefault').getEl().dom.setAttribute('readOnly', true);
    //Ext.getCmp('almuerzo').getEl().dom.setAttribute('readOnly', true);
});


