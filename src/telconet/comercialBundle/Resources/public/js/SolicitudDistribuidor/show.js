    $(document).ready(function ()
    {
        $(document).on('click','#btnAprobarPre',function(){
            $("#modalAprobarDet").modal('show');
        });

        $(document).on('click','#btnRechazarPre',function(){
            $("#modalRechazarDet").modal('show');
        });

        $(document).on('click','#btnAprobarDet',function(){
            aprobarRechazarSolicitudDetalle("aprobar");
        });

        $(document).on('click','#btnRechazarDet',function(){
            aprobarRechazarSolicitudDetalle("rechazar");
        });

        /**
         * Documentación para la función 'aprobarRechazarSolicitudDetalle'.
         *
         * Función encargada de aprobar/asignar/rechazar la solicitud.
         *
         * @author Kevin Baque Puya <kbaque@telconet.ec>
         * @version 1.0 11-11-2020
         *
         */
        function aprobarRechazarSolicitudDetalle(strAccion)
        {
            var arrayListado = [];
            var strSolicitud = $("#intIdSolicitudTd")[0].innerHTML;
            var strEstado    = $("#strEstadoTd")[0].innerHTML;
            console.log(strEstado);
            if(strEstado.trim() == "Pendiente" || strEstado.trim() == "Pendiente Gerente")
            {
                arrayListado.push(strSolicitud.trim());
            }
            if(arrayListado.length > 0)
            {
                var arrayParametros = {
                                        "strObservacionAprobar"  : $("#observacion_aprobar_det").val(),
                                        "strObservacionRechazar" : $("#observacion_rechazar_det").val(),
                                        "arraySolicitudes"       : arrayListado,
                                        "strAccion"              : strAccion
                                      };
                $.ajax({
                    data: arrayParametros,
                    url: url_aprobar_rechazar,
                    type: 'post',
                    success: function (response) {
                        if (response)
                        {
                            if(strAccion=="aprobar")
                            {
                                $('#observacion_aprobar_det').val('');
                            }
                            else if(strAccion=="rechazar")
                            {
                                $('#observacion_rechazar_det').val('');
                            }
                            $('#tabla').DataTable().ajax.reload();
                            $('#modalMensajes .modal-body').html(response);
                            $('#modalMensajes').modal({show: true});
                        }
                    },
                    beforeSend: function()
                    {
                        Ext.get(document.body).mask('Cargando Información.');
                    },
                    complete: function() 
                    {
                        Ext.get(document.body).unmask();
                    },
                    failure: function (response)
                    {
                        $('#modalMensajes .modal-body').html('No se pudo '+strAccion+' la(s) solicitude(s) por el siguiente error: ' + response);
                        $('#modalMensajes').modal({show: true});
                    }
                });
            }
            else
            {
                $('#modalMensajes .modal-body').html('Solo se puede '+strAccion+' una solicitud en estado Pendiente o Pendiente Gerente.');
                $('#modalMensajes').modal({show: true});
            }
        }
    });