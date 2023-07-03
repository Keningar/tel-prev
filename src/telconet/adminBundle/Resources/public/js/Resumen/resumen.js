function verInfoCliente(strTipo, intIdPersona, intIdPunto)
{
    var strUrlRedireccion = '';
    
    if(strTipo == 'casos')
    {
        strUrlRedireccion = strUrlCasos;
    }
    else if(strTipo == 'tareas')
    {
        strUrlRedireccion = strUrlTareas;
    }
    else
    {
        strUrlRedireccion = strUrlDebitos;
    }
    
    Ext.MessageBox.wait("Cargando...");
                                        
    Ext.Ajax.request
    ({
        url: strUrlAjaxPuntoSession,
        method: 'post',
        params:
        { 
            idPersona: intIdPersona,
            idPunto: intIdPunto
        },
        success: function(response)
        {
            var text = response.responseText;

            if(text === "OK")
            {
                if(strTipo == 'tareas')
                {
                    document.forms[0].submit();
                }
                else
                {
                    window.location = strUrlRedireccion;
                }  
            }
            else
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error',text); 
            }
        },
        failure: function(result)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.responseText); 
        }
    });
}

function verSeguimientoTarea(intIdDetalle)
{
    var strRespuestaSeguimiento = '';
    
    Ext.MessageBox.wait("Cargando...");
                                        
    Ext.Ajax.request
    ({
        url: strUrlSeguimientoTarea,
        method: 'get',
        datatype: 'json',
        params:
        { 
            id_detalle: intIdDetalle,
        },
        success: function(response)
        {
            strRespuestaSeguimiento = response.responseText;
    
            if( strRespuestaSeguimiento != '')
            {
                Ext.Ajax.request
                ({
                    url: strUrlMostrarVentanaTarea,
                    method: 'get',
                    datatype: 'json',
                    params:
                    { 
                        respuestaJson: strRespuestaSeguimiento,
                    },
                    success: function(response)
                    {
                        $('#bodySeguimientoTarea').html('');
                        $('#bodySeguimientoTarea').html(response.responseText);
                        
                        $('#modalTarea').modal('show');
                        
                        Ext.MessageBox.hide();
                    },
                    failure: function(result)
                    {
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error',result.responseText); 
                    }
                });
            }
            else
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error', 'No se encontro información de seguimiento'); 
            }
        },
        failure: function(result)
        {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error',result.responseText); 
        }
    });
}

