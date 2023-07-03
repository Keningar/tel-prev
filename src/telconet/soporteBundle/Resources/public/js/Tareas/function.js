function reintentoTareaSysCloud(data) {

    var dataJson = {};

    Ext.MessageBox.show({
        title: "Mensaje",
        msg: '¿Está seguro que desea reintentar la creación de la tarea en Sys Cloud-Center?',
        closable: false,
        multiline: false,
        icon: Ext.Msg.QUESTION,
        buttons: Ext.Msg.YESNO,
        buttonText: { yes: 'Si', no: 'No' },
        fn: function(buttonValue) {
            if (buttonValue === "yes") {

                dataJson['asignado'] = data.ref_asignado_nombre;
                dataJson['depAsignado'] = data.asignado_nombre;
                dataJson['numeroTarea'] = data.numero_tarea;
                dataJson['nombreTarea'] = data.nombre_tarea;
                dataJson['nombreProceso'] = data.nombre_proceso;
                dataJson['fechaAsignado'] = data.fechaEjecucion;
                dataJson['horaAsignado'] = data.horaEjecucion;
                dataJson['observacion'] = data.observacion;

                Ext.get("grid").mask('Ejecutando proceso...');

                Ext.Ajax.request({
                    url: urlAjaxReintentoTareaSysCloud,
                    method: 'post',
                    timeout: 400000,
                    params: {
                        datos: Ext.JSON.encode(dataJson)
                    },
                    success: function(response) {

                        Ext.get("grid").unmask();
                        var objData = Ext.JSON.decode(response.responseText); //Obtenemos la respuesta del controlador
                        var status = objData.status;
                        var message = objData.message;

                        if (status) {
                            store.load(); //Store del grid principal
                        }

                        Ext.MessageBox.show({
                            title: status ? 'Mensaje' : 'Error',
                            msg: message,
                            buttons: Ext.MessageBox.OK,
                            icon: status ? Ext.MessageBox.INFO : Ext.MessageBox.ERROR,
                            closable: false,
                            multiline: false,
                            buttonText: { ok: 'Cerrar' },
                        });
                    },
                    failure: function(result) {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        }
    });
}


function confirmarIpServicioSoporte(data) {

    Ext.MessageBox.show({
        title: "Mensaje",
        msg: '¿Está seguro que desea confirmar el enlace?',
        closable: false,
        multiline: false,
        icon: Ext.Msg.QUESTION,
        buttons: Ext.Msg.YESNO,
        buttonText: { yes: 'Si', no: 'No' },
        fn: function(buttonValue) {
            if (buttonValue === "yes") {
                var arrayRequest = {
                    idEmpresa: data.idEmpresa,
                    idComunicacion: data.idComunicacion,
                    idDetalle: data.idDetalle,
                    strCodigoProgreso: data.strCodigoProgreso,
                    idServicio: data.idServicio,
                    strOrigenProgreso: data.strOrigenProgreso
                };

                Ext.get("grid").mask('Confirmando enlace...');

                Ext.Ajax.request({
                    url: urlConfirmarIpServicioSoporteAction,
                    method: 'post',
                    timeout: 400000,
                    params: {
                        data: Ext.encode(arrayRequest)
                    },
                    success: function(response) {

                        Ext.get("grid").unmask();
                        var objData = Ext.JSON.decode(response.responseText); //Obtenemos la respuesta del controlador
                        var status = objData.status;
                        var message = objData.mensaje;

                        if (status) {
                            store.load(); //Store del grid principal
                        }


                        Ext.MessageBox.show({
                            title: status ? 'Mensaje' : 'Error',
                            msg: message,
                            buttons: Ext.MessageBox.OK,
                            icon: status ? Ext.MessageBox.INFO : Ext.MessageBox.ERROR,
                            closable: false,
                            multiline: false,
                            buttonText: { ok: 'Cerrar' },
                        });
                    },
                    failure: function(result) {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        }
    });
}

//wvera validación enlaces
function validarServicioSoporte(data) {

    Ext.MessageBox.show({
        title: "Mensaje",
        msg: '¿Está seguro que desea validar el enlace?',
        closable: false,
        multiline: false,
        icon: Ext.Msg.QUESTION,
        buttons: Ext.Msg.YESNO,
        buttonText: { yes: 'Si', no: 'No' },
        fn: function(buttonValue) {
            if (buttonValue === "yes") {
                var arrayRequest = {
                    idEmpresa: data.idEmpresa,
                    idComunicacion: data.idComunicacion,
                    idDetalle: data.idDetalle,
                    casoId: data.casoId,
                    servicioId: data.servicioId,
                    user: data.user,
                    ultimaMilla: data.ultimaMilla,
                    empresaCod: data.empresaCod,
                    departamentoId: data.departamentoId,
                    idServicio: data.idServicio,
                    strOrigenProgreso: data.strOrigenProgreso
                };

                Ext.get("grid").mask('Validando enlace...');

                Ext.Ajax.request({
                    url: urlValidarServicioSoporteAction,
                    method: 'post',
                    timeout: 400000,
                    params: {
                        data: Ext.encode(arrayRequest)
                    },
                    success: function(response) {

                        Ext.get("grid").unmask();
                        var objData = Ext.JSON.decode(response.responseText); //Obtenemos la respuesta del controlador
                        var status = objData.status;
                        var message = objData.message;
                        var statusPing = objData.data.statusPing;
                        var packageSent = null;
                        var packageReceived = null;
                        var packageLost = null;
                        if (objData.data.packages!= null ) {
                            packageSent = objData.data.packages.sent;
                            packageReceived = objData.data.packages.received;
                            packageLost = objData.data.packages.lost;
                            if(!statusPing)
                            {
                                message = "Error";
                            }
                        }  else {
                            message = "Error";
                        }
                        var latency = "No se encontro latencia";
                        var metric = "";
                        var ipCliente = "No se encuentra datos del cliente."

                        if (objData.data.ipClient != null) {
                            ipCliente = objData.data.ipClient;
                        }
                        if (objData.data.latency != null && (statusPing || objData.data.latency.avg > 0)) {
                            metric = "ms";
                            latency = "Latencia media : " + objData.data.latency.avg + metric;
                        } else if (statusPing && (packageSent == packageReceived && packageLost == 0)) {
                            metric = "ms";
                            latency = "Latencia media : " + packageLost + metric;
                        }else if(objData.data.latency != null && !statusPing ) {
                            latency = "No se encontro latencia" ;
                        }
                        var messageContet = "";
                        if (packageSent > 0) {

                            if(objData.data.strTieneProgConfirIPserv != null && 
                                objData.data.strTieneProgConfirIPserv == 'SI'){
                                    messageContet =
                                    "</br><b>Verificaci&oacute;n del Enlace </b> </br>" + "</br>"+ objData.data.message + " </br>";
                            }
                            else{
                                var result = objData.data.message.indexOf("%");
                            
                                if (result != -1)
                                {   
                                    var arrayMensaje    = objData.data.message.split("%");
                                    var strPartUnaMens  = arrayMensaje[0];
                                    var strPartDosMens  = arrayMensaje[1];
                                    messageContet =
                                    "</br><b>Verificaci&oacute;n del Enlace </b> </br>" + "</br>"+  strPartUnaMens + "</br> <p style='color:red' >" + strPartDosMens + " </p> </br> <b>Ping :</b> " + ipCliente + "<br><table style='height: 60px; border-color: #d8d8d8;' border='1' width='291' margin-top : '10' ><thead><tr><td style='width: 88px; padding: 5px; text-align: center;'>ENVIADOS</td><td style='width: 88px;  text-align: center;'>RECIBIDOS</td><td style='width: 88px;  text-align: center;'>PERDIDOS</td></tr></thead><tbody><tr><td style='width: 88px;  text-align: center;'>" + packageSent + "</td><td style='width: 88px;  text-align: center;'>" + packageReceived + "</td><td style='width: 88px;  text-align: center;'>" + packageLost + "</td></tr></tbody></table></br><p style='text-align: center;'> " + latency + "<br>";
                                }
                                else
                                {
                                    messageContet =
                                    "</br><b>Verificaci&oacute;n del Enlace </b> </br>" + "</br>"+ objData.data.message + " </br><b>Ping :</b> " + ipCliente + "<br><table style='height: 60px; border-color: #d8d8d8;' border='1' width='291' margin-top : '10' ><thead><tr><td style='width: 88px; padding: 5px; text-align: center;'>ENVIADOS</td><td style='width: 88px;  text-align: center;'>RECIBIDOS</td><td style='width: 88px;  text-align: center;'>PERDIDOS</td></tr></thead><tbody><tr><td style='width: 88px;  text-align: center;'>" + packageSent + "</td><td style='width: 88px;  text-align: center;'>" + packageReceived + "</td><td style='width: 88px;  text-align: center;'>" + packageLost + "</td></tr></tbody></table></br><p style='text-align: center;'> " + latency + "<br>";
                                } 
                            }

                            message = message + messageContet;
                        } else {
                            messageContet = objData.data.message;
                            message = "</br>" + message + "</br>" + messageContet;
                        }

                        if (status && statusPing) {
                            store.load(); //Store del grid principal
                        }

                        if (statusPing) {
                            Ext.MessageBox.show({
                                title: 'Mensaje',
                                msg: message,
                                buttons: Ext.MessageBox.OK,
                                icon: status ? Ext.MessageBox.INFO : Ext.MessageBox.ERROR,
                                closable: false,
                                multiline: false,
                                buttonText: { ok: 'Cerrar' },
                            });
                        } else {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: message,
                                buttons: Ext.MessageBox.ERROR,
                                icon: Ext.MessageBox.ERROR,
                                closable: false,
                                multiline: false,
                                buttonText: { ok: 'Cerrar' },
                            });
                        }

                    },
                    failure: function(result) {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        }
    });
}

//wvera crear kml
function permitirCrearKml(data) {

    Ext.MessageBox.show({
        title: "Mensaje",
        msg: '¿Está seguro que desea permitir crear KML desde el móvil?',
        closable: false,
        multiline: false,
        icon: Ext.Msg.QUESTION,
        buttons: Ext.Msg.YESNO,
        buttonText: { yes: 'Si', no: 'No' },
        fn: function(buttonValue) {
            if (buttonValue === "yes") {
                var arrayRequest = {
                    idEmpresa: data.idEmpresa,
                    idComunicacion: data.idComunicacion,
                    idDetalle: data.idDetalle,
                    casoId: data.casoId,
                    servicioId: data.servicioId,
                    user: data.user,
                    ultimaMilla: data.ultimaMilla,
                    empresaCod: data.empresaCod,
                    departamentoId: data.departamentoId,
                    idServicio: data.idServicio,
                };

                Ext.get("grid").mask('Otorgando permiso...');

                Ext.Ajax.request({
                    url: urlPermiteCrearKmlAction,
                    method: 'post',
                    timeout: 400000,
                    params: {
                        data: Ext.encode(arrayRequest)
                    },
                    success: function(response) {

                        Ext.get("grid").unmask();
                        var objData = Ext.JSON.decode(response.responseText); //Obtenemos la respuesta del controlador
                        var status = objData.status;
                        var message = objData.message;

                        if (status) {
                            store.load(); //Store del grid principal
                        }

                        if (status == 200) {
                            Ext.MessageBox.show({
                                title: 'Mensaje',
                                msg: message,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.INFO ,
                                closable: false,
                                multiline: false,
                                buttonText: { ok: 'Cerrar' },
                            });
                        } else {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: message,
                                buttons: Ext.MessageBox.ERROR,
                                icon: Ext.MessageBox.ERROR,
                                closable: false,
                                multiline: false,
                                buttonText: { ok: 'Cerrar' },
                            });
                        }

                    },
                    failure: function(result) {
                        Ext.get("grid").unmask();
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        }
    });
}