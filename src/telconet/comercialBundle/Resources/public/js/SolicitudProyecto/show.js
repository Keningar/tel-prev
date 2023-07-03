    $(document).ready(function () {

        $(document).on('click','#btnAprobarPre',function(){
            getUsuarios();
            $("#modalAprobarDet").modal('show');
        });

        $(document).on('click','#btnRechazarPre',function(){
            getMotivos("rechazar");
            $("#modalRechazarDet").modal('show');
        });

        $(document).on('click','#btnReasignarPre',function(){
            getMotivos("reasignar");
            $("#modalReasignarDet").modal('show');
        });

        $(document).on('click','#btnAprobarDet',function(){
            aprobarRechazarSolicitudDetalle("aprobar");
        });

        $(document).on('click','#btnRechazarDet',function(){
            aprobarRechazarSolicitudDetalle("rechazar");
        });

        $(document).on('click','#btnReasignarDet',function(){
            aprobarRechazarSolicitudDetalle("reasignar");
        });

        /**
         * Documentación para la función 'aprobarRechazarSolicitudDetalle'.
         *
         * Función encargada de aprobar/reasignar/rechazar la solicitud.
         *
         * @author Kevin Baque Puya <kbaque@telconet.ec>
         * @version 1.0 17-07-2020
         *
         */
        function aprobarRechazarSolicitudDetalle(strAccion)
        {
            var arrayListado = [];
            var strSolicitud = $("#intIdCotizacionTd")[0].innerHTML;
            var strEstado    = $("#strEstadoTd")[0].innerHTML;
            if(strEstado.trim() == "Pendiente")
            {
                arrayListado.push(strSolicitud.trim());
            }
            if(arrayListado.length > 0)
            {
                var arrayParametros = {
                                        "strCodigo": $("#codigo_aprobar_det").val(),
                                        "strObservacionAprobar": $("#observacion_aprobar_det").val(),
                                        "strObservacionReasignar": $("#observacion_reasignar_det").val(),
                                        "strObservacionRechazar": $("#observacion_rechazar_det").val(),
                                        "intIdMotivoReasignar":$('#motivo_reasignar_det option:selected').val(),
                                        "intIdMotivoRechazar":$('#motivo_rechazar_det option:selected').val(),
                                        "arraySolicitudes": arrayListado,
                                        "strAccion":strAccion,
                                        "strUsuario":$('#usuarios_asignar_det option:selected').val()
                                      };
                $.ajax({
                    data: arrayParametros,
                    url: url_aprobar_rechazar_solicitud_proyecto,
                    type: 'post',
                    success: function (response) {
                        if (response)
                        {
                            if(strAccion=="aprobar")
                            {
                                $('#codigo_aprobar_det').val('');
                                $('#observacion_aprobar_det').val('');
                            }
                            else if(strAccion=="reasignar")
                            {
                                $('#observacion_reasignar_det').val('');
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
                $('#modalMensajes .modal-body').html('Solo se puede '+strAccion+' una solicitud en estado Pendiente.');
                $('#modalMensajes').modal({show: true});
            }
        }

        /**
         * Documentación para la función 'getMotivos'.
         *
         * Función encargada de mostrar el listado de motivos.
         *
         * @author Kevin Baque Puya <kbaque@telconet.ec>
         * @version 1.0 17-07-2020
         *
         */
        function getMotivos(strAccion)
        {
            document.getElementById("motivo_reasignar_det").innerHTML="";
            document.getElementById("motivo_rechazar_det").innerHTML="";
            $.ajax({
                url: url_motivo_solicitud_proyecto,
                method: 'post',
                data:
                {
                    strAccion: strAccion
                },
                success: function (data) {
                    if(strAccion=="reasignar")
                    {
                        $.each(data.motivos, function (id, registro) {
                            $("#motivo_reasignar_det").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                        });
                    }
                    else if(strAccion=="rechazar")
                    {
                        $.each(data.motivos, function (id, registro) {
                            $("#motivo_rechazar_det").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                        });
                    }
                },
                error: function () {
                    $('#modalMensajes .modal-body').html("No se pudieron cargar los motivos. Por favor comuníquese con el departamento de Sistemas");
                    $('#modalMensajes').modal({show: true});
                }
            });
        }

        /**
         * Documentación para la función 'getUsuarios'.
         *
         * Función encargada de mostrar el listado de usuarios.
         *
         * @author Kevin Baque Puya <kbaque@telconet.ec>
         * @version 1.0 17-07-2020
         *
         */
        function getUsuarios()
        {
            document.getElementById("usuarios_asignar_det").innerHTML="";
            $.ajax({
                url: url_usuarios_solicitud_proyecto,
                method: 'GET',
                success: function (data) {
                    $.each(data.usuarios, function (id, registro) {
                        $("#usuarios_asignar_det").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                    });
                },
                error: function () {
                    $('#modalMensajes .modal-body').html("No se pudieron cargar los Estados. Por favor comuníquese con el departamento de Sistemas");
                    $('#modalMensajes').modal({show: true});
                }
            });
        }
    });