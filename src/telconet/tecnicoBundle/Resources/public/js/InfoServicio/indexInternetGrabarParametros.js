/* Funcion que sirve para realizar la llamada ajax
 * que graba los parametros iniciales del servicio para
 * la empresa TTCO 
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * @param       gridIndex
 */    
function grabarHistorial(data,gridIndex){
    Ext.get(gridServicios.getId()).mask('Grabando Parámetros Iniciales...');
    
    Ext.Ajax.request({
        url: grabarHistorialBoton,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            capacidad1: data.capacidadUno,
            capacidad2: data.capacidadDos,
            login: data.login
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            if(response.responseText == "OK"){
                Ext.Msg.alert('Mensaje','Se Grabaron los Parámetros Iniciales', function(btn){
                    if(btn=='ok'){
                        store.load();
                    }
                });
            }
            else{
                Ext.Msg.alert('Mensaje ',response.responseText );
            }
        },
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    }); 
}

/* Funcion que sirve para realizar la llamada ajax
 * que graba los parametros iniciales del servicio para
 * la empresa MD 
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * @param       gridIndex
 */    
function grabarHistorialMd(data,gridIndex, accion){
    Ext.get(gridServicios.getId()).mask('Grabando Parámetros Iniciales...');
    
    Ext.Ajax.request({
        url: grabarHistorialBoton,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio       : data.idServicio,
            capacidad1       : data.capacidadUno,
            capacidad2       : data.capacidadDos,
            login            : data.login,
            idProducto       : data.productoId,
            idAccion         : accion,
            esISB            : ((data.descripcionProducto === "INTERNET SMALL BUSINESS" || data.descripcionProducto === "TELCOHOME") 
                                ? "SI" : "")
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            if(response.responseText === "OK"){
                Ext.Msg.alert('Mensaje','Se Confirmo el Servicio', function(btn){
                    if(btn=='ok'){
                        store.load();
                    }
                });
            }
            else if(response.responseText == "OK1"){
                Ext.Msg.alert('Mensaje','No se pudo obtener la potencia, <br>De todas formas se Confirmo el Servicio', function(btn){
                    if(btn=='ok'){
                        store.load();
                    }
                });
            }
            else{
                Ext.Msg.alert('Mensaje ','No se pudieron grabar los parámetros iniciales, \n\
                                          problemas en la Ejecucion del Script! <br>'+response.responseText );
            }
        },
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    }); 
}