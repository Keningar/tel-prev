var cbxTipoCuenta;

Ext.onReady(function()
{
    Ext.create('Ext.panel.Panel',
        {
            style: 'padding-top:10px; padding-left: 100px; ',
            border: false,
            buttonAlign: 'center',
            renderTo: 'form_dataTipoCuenta_twig_js',
            layout:
                {
                    type: 'vbox'
                },
            buttons:
                [
                    {
                        id: 'btnGuardarBin',
                        name: 'btnGuardarBin',
                        text: 'Guardar',
                        ui: 'lo-que-sea',
                        cls: 'button-crud',
                        handler: function()
                        {
                            Ext.getCmp('btnGuardarBin').disable();
                            Ext.getCmp('btnRegresar').disable();
                            Ext.Ajax.request(
                                {
                                    url: urlGuardarBIN,
                                    method: 'POST',
                                    timeout: 60000,
                                    params:
                                        {
                                            strBinNuevo: $('#telconet_schemabundle_admibinestype_bin_nuevo').val(),
                                            strDescripcion: $('#telconet_schemabundle_admibinestype_descripcion').val(),
                                            intTipoCuentaId: $('#telconet_schemabundle_admibinestype_tarjeta').val(),
                                            strTipoCuentaDescripcion: $('#telconet_schemabundle_admibinestype_tarjeta option:selected').text(),
                                            intBancoTipoCuentaId: $('#telconet_schemabundle_admibinestype_banco').val(),
                                            strBancoDescripcion: $('#telconet_schemabundle_admibinestype_banco option:selected').text()
                                        },
                                    success: function(response)
                                    {
                                        var text = Ext.decode(response.responseText);
                                        if (text.estatus)
                                        {
                                            Ext.Msg.alert('Error', text.msg);
                                            window.location = "" + text.id + "/show";
                                        }
                                        else
                                        {
                                            Ext.Msg.alert('Error', text.msg);
                                            Ext.getCmp('btnGuardarBin').enable();
                                            Ext.getCmp('btnRegresar').enable();
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        Ext.getCmp('btnGuardarBin').enable();
                                        Ext.getCmp('btnRegresar').enable();
                                    }
                                });
                        }
                    },
                    {
                        id: 'btnRegresar',
                        name: 'btnRegresar',
                        text: 'Regresar',
                        ui: 'lo-que-sea',
                        cls: 'button-crud',
                        handler: function()
                        {
                            Ext.getCmp('btnGuardarBin').disable();
                            Ext.getCmp('btnRegresar').disable();
                            window.location = urlIndex;
                        }
                    }
                ]
        });
});

function validarBin(input)
{
    if (!/^\d+$/.test($(input).val()))
    {
         $(input).val('');
    }
}

function getBancos()
{
    Ext.Ajax.request(
        {
            type: "POST",
            data: 'tipoCuenta=' + $('#telconet_schemabundle_admibinestype_tarjeta').val(),
            url: urlGetBanco,
            params: {
                tipoCuenta: $('#telconet_schemabundle_admibinestype_tarjeta').val()
            },
            success: function(response)
            {
                var msg = Ext.decode(response.responseText);
                if (msg.msg == 'ok')
                {
                    document.getElementById("telconet_schemabundle_admibinestype_banco").innerHTML = msg.div;
                }
                else
                {
                    document.getElementById("telconet_schemabundle_admibinestype_banco").innerHTML = msg.msg;
                }
            }
        });
}