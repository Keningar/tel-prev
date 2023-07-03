/**
 * Funcion que sirve para crear el cacti
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 10-06-2016
 * */
function crearCacti(data,idAccion)
{

    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Crear el cacti?', function(btn)
    {
        if (btn === 'ok')
        {
            Ext.get(gridServicios.getId()).mask();
            Ext.Ajax.request({
                url: urlCrearCacti,
                method: 'post',
                timeout: 400000,
                params: {
                    idServicio: data.idServicio,
                    idPersonaEmpresaRol: data.idPersonaEmpresaRol
                },
                success: function(response) {
                    Ext.get(gridServicios.getId()).unmask();
                    if (response.responseText === "OK")
                    {
                        Ext.Msg.alert('Mensaje', 'Se generó el cacti para el Servicio:'+data.loginAux, function(btn)
                        {
                            if (btn === 'ok')
                            {
                                store.load();
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert('Mensaje', response.responseText, function(btn)
                        {
                            if (btn === 'ok')
                            {
                                store.load();
                            }
                        });
                    }
                },
                failure: function(result)
                {
                    Ext.get(gridServicios.getId()).unmask();
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}