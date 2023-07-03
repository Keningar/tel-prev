function asignarMeta(arrayParametros)
{
    var formPanel = Ext.create('Ext.form.Panel',
        {
            id: 'formAsignarMeta',
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults:
                {
                    labelAlign: 'left',
                    labelWidth: 125,
                    msgTarget: 'side'
                },
            items:
                [
                    {
                        xtype: 'fieldset',
                        title: '',
                        defaultType: 'textfield',
                        defaults:
                            {
                                width: 700
                            },
                        layout:
                            {
                                type: 'table',
                                columns: 4,
                                align: 'left'
                            },
                        items:
                            [
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Meta Bruta (Ventas) *',
                                    width: '600',
                                    name: 'strMetaBruta',
                                    id: 'strMetaBruta',
                                    value: arrayParametros['strMetaBruta'],
                                    colspan: 4,
                                    hideTrigger: true,
                                    listeners:
                                        {
                                            keyup:
                                                {
                                                    element: 'el',
                                                    fn: function(event, target)
                                                    {
                                                        getValorMetaActiva();
                                                    }
                                                }
                                        }
                                },
                                {
                                    xtype: 'numberfield',
                                    fieldLabel: 'Meta Activa (%) *',
                                    width: 200,
                                    id: 'strMetaActiva',
                                    name: 'strMetaActiva',
                                    value: arrayParametros['strMetaActiva'],
                                    colspan: 2,
                                    hideTrigger: true,
                                    style:
                                        {
                                            width: '10%'
                                        },
                                    listeners:
                                        {
                                            keyup:
                                                {
                                                    element: 'el',
                                                    fn: function(event, target)
                                                    {
                                                        getValorMetaActiva();
                                                    }
                                                }
                                        }
                                },
                                {
                                    xtype: 'displayfield',
                                    value: '=',
                                    width: 10,
                                    style:
                                        {
                                            marginRight: '5px',
                                            marginLeft: '5px'
                                        }
                                },
                                {
                                    xtype: 'displayfield',
                                    id: 'strMetaActivaValor',
                                    name: 'strMetaActivaValor',
                                    value: '100'
                                }
                            ]
                    }
                ],
            buttons:
                [
                    {
                        text: 'Asignar',
                        type: 'submit',
                        handler: function()
                        {
                            var form = Ext.getCmp('formAsignarMeta').getForm();

                            if (form.isValid())
                            {
                                var strMetaBruta = Ext.getCmp('strMetaBruta').getValue();
                                var strMetaActiva = Ext.getCmp('strMetaActiva').getValue();

                                if (strMetaBruta != '0' && strMetaBruta != null && strMetaBruta != ''
                                    && strMetaActiva != null && strMetaActiva != '' && strMetaActiva != '0')
                                {
                                    arrayParametros['valor'] = strMetaBruta + '|' + strMetaActiva;
                                    arrayParametros['caracteristica'] = strCaracteristicaMetaBruta + '|' + strCaracteristicaMetaActiva;
                                    ajaxAsignarCaracteristica(arrayParametros);
                                }
                                else
                                {
                                    Ext.Msg.alert('Atenci\xf3n', 'Todos los valores son requeridos');
                                }
                            }
                        }
                    },
                    {
                        text: 'Cerrar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }
                ]
        });

    getValorMetaActiva();

    win = Ext.create('Ext.window.Window',
        {
            title: 'Asignar Meta',
            modal: true,
            width: 350,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
}


function ajaxAsignarCaracteristica(arrayParametros)
{
    if (typeof win != 'undefined' && win != null)
    {
        win.destroy();
    }

    Ext.Ajax.request
        ({
            url: strAsignarCaracteristica,
            method: 'post',
            dataType: 'json',
            params:
                {
                    intIdPersonaEmpresaRol: arrayParametros['intIdPersonalEmpresaRol'],
                    strCaracteristica: arrayParametros['caracteristica'],
                    strValor: arrayParametros['valor'],
                    strAccion: arrayParametros['accion']
                },
            success: function(response)
            {
                if (response.responseText == 'OK')
                {
                    Ext.Msg.alert('Información', 'Se guardaron los cambios con éxito');
                }
                else
                {
                    Ext.Msg.alert('Error', 'Hubo un problema al guardar los cambios');
                }

                arrayParametros['store'].load();
            },
            failure: function(result)
            {
                Ext.Msg.alert('Error', result.responseText);
            }
        });
}

function getValorMetaActiva()
{
    var strMetaActiva = '' + Ext.getCmp('strMetaActiva').getValue();
    strMetaActiva = strMetaActiva.replace(/[^0-9]+/g, '');
    Ext.getCmp('strMetaActiva').setRawValue(strMetaActiva);

    var strMetaBruta = '' + Ext.getCmp('strMetaBruta').getValue();
    strMetaBruta = strMetaBruta.replace(/[^0-9]+/g, '');
    Ext.getCmp('strMetaBruta').setRawValue(strMetaBruta);

    var intValorMetaActiva = 0;
    intValorMetaActiva = Math.round((parseInt(strMetaBruta) * parseInt(strMetaActiva)) / 100);

    if (intValorMetaActiva > 0)
    {
        Ext.getCmp('strMetaActivaValor').setRawValue(intValorMetaActiva + ' Ventas');
    }
    else
    {
        Ext.getCmp('strMetaActivaValor').setRawValue('0 Ventas');
    }
}
