function reconfigurarServicioIntMpls(data, idAccion)
{
    Ext.Msg.alert('Mensaje', 'Esta seguro que desea Reconfigurar el Servicio?', function(btn)
    {
        if (btn === 'ok')
        {
            Ext.get(gridServicios.getId()).mask("Reconfigurando Servicio...");
            Ext.Ajax.request({
                url: urlReactivarClienteTN,
                method: 'post',
                timeout: 400000,
                params: {
                    idServicio: data.idServicio,
                    idProducto: data.productoId,
                    idAccion:   idAccion,
                    vlan:       data.vlan,
                    mac:        data.mac,
                    anillo    : data.anillo,
                    capacidad1: data.capacidadUno,
                    capacidad2: data.capacidadDos,
                    //datos l3mpls 
                    tipoEnlace:              data.tipoEnlace,
                    loginAux:                data.loginAux,
                    elementoPadre:           data.elementoPadre,
                    elementoNombre:          data.elementoNombre,                    
                    interfaceElementoNombre: data.interfaceElementoNombre,
                    ipServicio:              data.ipServicio,
                    subredServicio:          data.subredServicio,
                    gwSubredServicio:        data.gwSubredServicio,
                    mascaraSubredServicio:   data.mascaraSubredServicio,
                    protocolo:               data.protocolo,
                    defaultGateway:          data.defaultGateway,
                    asPrivado:               data.asPrivado,
                    vrf:                     data.vrf,
                    rdId:                    data.rdId                    
                },
                success: function(response) {
                    Ext.get(gridServicios.getId()).unmask();
                    if (response.responseText === "OK")
                    {
                        Ext.Msg.alert('Mensaje', 'Se Reconfiguro el Cliente', function(btn)
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