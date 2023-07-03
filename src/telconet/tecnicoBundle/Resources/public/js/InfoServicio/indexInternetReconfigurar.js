/* Funcion que sirve para realizar la reconfiguracion
 * de un servicio sobre un puerto
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * @param       gridIndex
 */
function reconfigurarPuertoMd(data,gridIndex)
{
    Ext.Msg.alert('Mensaje','Esta seguro que desea Reconfigurar el Puerto?', function(btn)
    {
        if(btn=='ok')
        {
            Ext.get("grid").mask('Reconfigurando puerto...');
            Ext.Ajax.request
            ({
                url: reconfigurarPuertoBoton,
                method: 'post',
                timeout: 400000,
                params: 
                { 
                    idServicio: data.idServicio
                },
                success: function(response)
                {
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK")
                    {
                        Ext.Msg.alert('Mensaje','Se reconfiguro el puerto!', function(btn)
                        {
                            if(btn=='ok')
                            {
                                store.load();
                            }
                        }
                        );
                    }
                    else
                    {
                        Ext.Msg.alert('Mensaje ',response.responseText+', <br> No se pudo reconfigurar el puerto!' );
                    }
                }
            });
        }
    });
}