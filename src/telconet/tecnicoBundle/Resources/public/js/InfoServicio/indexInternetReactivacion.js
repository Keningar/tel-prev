/* Funcion que sirve para realizar la llamada ajax para
 * la reactivacion del servicio para la empresa TTCO y MD
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @author      Javier Hidalgo <jihidalgo@telconet.ec>
 * @version     1.1     09-12-2021 - Se agrega parámetro para indicar
 *                                   si el cliente esta en estado Inaudit
 * @param Array data        Informacion que fue cargada en el grid
 * @param int   idAccion    id de accion de la credencial
 */
function reconectarCliente(data,idAccion){
    Ext.get(gridServicios.getId()).mask('Esperando Respuesta del Elemento...');
    Ext.Ajax.request({
        url: reconectarClienteBoton,
        method: 'post',
        timeout: 600000,
        params: { 
            idServicio: data.idServicio,
            idProducto: data.productoId,
            perfil: data.perfilDslam,
            login: data.login,
            capacidad1: data.capacidadUno,
            capacidad2: data.capacidadDos,
            estaInaudit: data.strServicioInternetInAudit,
            idAccion: idAccion
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            if(response.responseText == "OK")
            {
                Ext.Msg.alert('Mensaje','Se Reconecto el Cliente', function(btn)
                {
                    if(btn=='ok')
                    {
                        store.load();
                    }
                });
            }
            else if(response.responseText == "ACL")
            {
                Ext.Msg.alert('Mensaje ','No se pudo Reconectar el cliente, <br> No tiene ACL configurada, revisar la Base' );
            }
            else if(response.responseText == "INAUDIT")
            {
                Ext.Msg.alert('Mensaje ','No se pudo Reconectar el cliente, <br> El estado del Cliente es InAudit' );
            }
            else
            {
                Ext.Msg.alert('Mensaje',response.responseText, function(btn)
                {
                    if(btn=='ok')
                    {
                        store.load();
                    }
                });
            }
        },
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });    
}/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Funcion que sirve para realizar la llamada ajax para
 * la reactivacion del servicio para la empresa TNG
 * 
 * @author Jesús Banchen <jbanchen@telconet.ec>
 * @version 1.0 30-04-2019
 * @param data
 * @param idAccion
 */
function reconectarClienteTng(data,idAccion){
    
     Ext.MessageBox.confirm('Confirmacion ',
                            '¿Esta seguro que desea reconectar el servicio?', function (btn) {
                                if (btn == 'yes') {
                        
    
    Ext.get(gridServicios.getId()).mask('Procesando...');
    Ext.Ajax.request({
        url: reconectarClienteBoton,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            idProducto: data.productoId,
            perfil: data.perfilDslam,
            login: data.login,
            capacidad1: data.capacidadUno,
            capacidad2: data.capacidadDos,
            idAccion: idAccion
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            if(response.responseText == "OK")
            {
                Ext.Msg.alert('Mensaje','Se reconecto el servicio', function(btn)
                {
                    if(btn=='ok')
                    {
                        store.load();
                    }
                });
            }
            else if(response.responseText == "ACL")
            {
                Ext.Msg.alert('Mensaje ','No se pudo Reconectar el cliente, <br> No tiene ACL configurada, revisar la Base' );
            }
            else
            {
                Ext.Msg.alert('Mensaje',response.responseText, function(btn)
                {
                    if(btn=='ok')
                    {
                        store.load();
                    }
                });
            }
        },
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });    
    
                        } }); 

}

