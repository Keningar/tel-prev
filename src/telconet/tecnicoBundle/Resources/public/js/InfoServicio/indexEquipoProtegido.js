function confirmarServicioEquipoProtegido(data, idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Confirmar el Servicio de Equipo Protegido?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Confirmando el Servicio...');
            Ext.Ajax.request({
                url: confirmarServicioEquipoProtegidoBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Confirmo el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo confirmar el servicio!' );
                    }

                }

            });
        }
    });
}

function cancelarServicioEquipoProtegido(data, idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cancelar el Servicio de Equipo Protegido?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cancelando el Servicio...');
            Ext.Ajax.request({
                url: cancelarServicioEquipoProtegidoBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Cancelo el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo cancelar el servicio!' );
                    }

                }

            });
        }
    });
}

function cortarServicioEquipoProtegido(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Cortar el Servicio de Equipo Protegido?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Cortando el Servicio...');
            Ext.Ajax.request({
                url: cortarServicioEquipoProtegidoBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Corto el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo Cortar el servicio!' );
                    }

                }

            });
        }
    });
}

function reconectarServicioEquipoProtegido(data,idAccion){
    Ext.Msg.alert('Mensaje','Esta seguro que desea Reconectar el Servicio de Equipo Protegido?', function(btn){
        if(btn=='ok'){
            Ext.get("grid").mask('Reconectando el Servicio...');
            Ext.Ajax.request({
                url: reconectarServicioEquipoProtegidoBoton,
                method: 'post',
                timeout: 400000,
                params: { 
                    idServicio: data.idServicio,
                    idAccion: idAccion
                },
                success: function(response){
                    Ext.get("grid").unmask();

                    if(response.responseText == "OK"){
                        Ext.Msg.alert('Mensaje','Se Reconectar el Servicio!', function(btn){
                            if(btn=='ok'){
                                store.load();
                                //win.destroy();
                            }
                        });
                    }
                    else{
                        Ext.Msg.alert('Mensaje ','No se pudo reconectar el servicio!' );
                    }

                }

            });
        }
    });
}