Ext.onReady(function()
{
    
     Ext.Ajax.request(
        {
            type: "POST",
            url: urlGetMotivos,
            success: function(response)
            {
                var msg = Ext.decode(response.responseText);
                if (msg.msg == 'ok')
                {
                    document.getElementById("telconet_schemabundle_admibinestype_motivo_id").innerHTML = msg.div;
                }
                else
                {
                    document.getElementById("telconet_schemabundle_admibinestype_motivo_id").innerHTML = msg.msg;
                }
            }
        });

    Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 10,
            border: false,
            buttonAlign: 'center',
            renderTo: 'form_eliminarBin_twig_js',
            layout:
                {
                    type: 'table',
                    columns: 1,
                    align: 'stretch'
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            buttons:
                [
                    {
                        id: 'btnEliminar',
                        name: 'btnEliminar',
                        text: 'Eliminar BIN',
                        ui: 'lo-que-sea',
                        cls: 'button-eliminar',
                        handler: function()
                        {
                            Ext.Msg.confirm('Alerta', 'Se eliminará el registro. Desea continuar?', function(btn)
                            {
                                if (btn === 'yes')
                                {
                                    Ext.getCmp('btnEliminar').disable();
                                    Ext.getCmp('btnCancelar').disable();
                                    Ext.Ajax.request(
                                        {
                                            url: urlEliminarBIN,
                                            method: 'POST',
                                            timeout: 60000,
                                            params:
                                                {
                                                    int_IdBin_Ctrl_js: $('#idBin').val(),
                                                    str_descripcion_Ctrl_js: $('#telconet_schemabundle_admibinestype_motivo_descripcion').val(),
                                                    int_motivoId_Ctrl_js: $('#telconet_schemabundle_admibinestype_motivo_id').val()
                                                },
                                            success: function(response)
                                            {
                                                var text = Ext.decode(response.responseText);
                                                if (text.estatus)
                                                {
                                                    Ext.Msg.alert('Error', text.msg);
                                                    window.location = urlShow;
                                                }
                                                else
                                                {
                                                    Ext.Msg.alert('Error', text.msg);
                                                    Ext.getCmp('btnEliminar').enable();
                                                    Ext.getCmp('btnCancelar').enable();
                                                }
                                            },
                                            failure: function(result)
                                            {
                                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                Ext.getCmp('btnEliminar').enable();
                                                Ext.getCmp('btnCancelar').enable();
                                            }
                                        });
                                }
                            });
                        }
                    },
                    {xtype: 'splitter'},
                    {xtype: 'splitter'},
                    {
                        id: 'btnCancelar',
                        name: 'btnCancelar',
                        text: 'Cancelar',
                        ui: 'lo-que-sea',
                        cls: 'button-crud',
                        handler: function()
                        {
                            Ext.getCmp('btnEliminar').disable();
                            Ext.getCmp('btnCancelar').disable();
                            window.location = urlIndex;
                        }
                    }
                ]
        });
});
