/** 
 * Función javascript que sirve para ejecutar un metodo ajax para asignar al cliente una
 * ipv4 publica.
 * 
 * @author Francisco Adum <fadum@netlife.net.ec>
 * @version 1.0 06-07-2017
 */
function asignarIpv4Publico(data,gridIndex)
{
    Ext.Msg.alert('Mensaje','Está seguro que desea Asignar IPV4 Pública al Cliente?', function(btn)
    {
        if(btn=='ok')
        {
            Ext.get("grid").mask('Asignando nueva ipv4 publica...');
            Ext.Ajax.request
            ({
                url: asignarIpv4Publica,
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
                        Ext.Msg.alert('Mensaje','Se asignó la ipv4 pública al cliente!', function(btn)
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
                        Ext.Msg.alert('Mensaje ',response.responseText+', <br> No se pudo asignar la ipv4 pública al cliente' );
                    }
                }
            });
        }
    });
}

