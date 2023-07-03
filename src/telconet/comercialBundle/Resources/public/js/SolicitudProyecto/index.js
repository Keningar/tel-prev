    $(document).ready(function () {
        $('#strFechaInicioBuscar').datepicker({
            uiLibrary  : 'bootstrap4',
            locale     : 'es-es',
            dateFormat : 'dd/mm/yy'
        });
        $('#strFechaFinBuscar').datepicker({
            uiLibrary  : 'bootstrap4',
            locale     : 'es-es',
            dateFormat : 'dd/mm/yy'
        });

        $.ajax({
            url: url_usuarios_solicitud_proyecto,
            method: 'GET',
            success: function (data) {
                $.each(data.usuarios, function (id, registro) {
                    $("#usuarios_asignar").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los Usuarios. Por favor comuníquese con el departamento de Sistemas.");
                $('#modalMensajes').modal({show: true});
            }
        });

        $.ajax({
            url: url_motivo_solicitud_proyecto,
            method: 'post',
            data:
            {
                strAccion: "rechazar"
            },
            success: function (data) {
                $.each(data.motivos, function (id, registro) {
                    $("#motivo_rechazar").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los motivos de rechazo. Por favor comuníquese con el departamento de Sistemas.");
                $('#modalMensajes').modal({show: true});
            }
        });

        $.ajax({
            url: url_motivo_solicitud_proyecto,
            method: 'post',
            data:
            {
                strAccion: "reasignar"
            },
            success: function (data) {
                $.each(data.motivos, function (id, registro) {
                    $("#motivo_reasignar").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los motivos de reasignación. Por favor comuníquese con el departamento de Sistemas.");
                $('#modalMensajes').modal({show: true});
            }
        });

        $.ajax({
            url: url_estados_solicitud_proyecto,
            method: 'GET',
            success: function (data) {
                $.each(data.estados, function (id, registro) {
                    $("#strEstadoBuscar").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los Estados. Por favor comuníquese con el departamento de Sistemas.");
                $('#modalMensajes').modal({show: true});
            }
        });

        var objListado = $('#tabla').DataTable({
            "ajax": {
                "url": url_grid_solicitud_proyecto,
                "type": "POST",
                beforeSend: function()
                {
                    Ext.get(document.body).mask('Cargando Información.');
                },
                complete: function() 
                {
                    Ext.get(document.body).unmask();
                },
                "data": function (param) {
                    param.strNombre        = $("#strNombre_buscar").val();
                    param.strApellido      = $("#strApellido_buscar").val();
                    param.strRazonSocial   = $("#strRazonSocial_buscar").val();
                    param.strFechaInicio   = $('#strFechaInicioBuscar').val();
                    param.strFechaFin      = $('#strFechaFinBuscar').val();
                    param.strEstado        = $('#strEstadoBuscar option:selected').val();
                }
            },
            "language": {
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente"
                },
                "sProcessing": "Procesando...",
                "lengthMenu": "Muestra _MENU_ filas por página",
                "zeroRecords": "No hay información disponible",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "No hay información disponible",
                "infoFiltered": "(filtrado de _MAX_ total filas)",
                "search": "Buscar:",
                "processing": true,
                "loadingRecords": "Cargando datos..."
            },
            "columns": [
                    {"data": "intIdCotizacion"},
                    {"data": "strCliente"},
                    {"data": "strAsesor"},
                    {"data": "strEstado"},
                    {"data": "strObservacion"},
                    {"data": "strFeCreacion"},
                    {"data": "strAcciones",
                        "render": function (data)
                        {
                            var strDatoRetorna = '';
                            if (data.linkVer !== '') 
                            {
                                strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" ' + ' title="Ver Detalle" ' +
                                    'onClick="javascript:mostrarModalDetalle(\'' + data.linkVer + '\');">' + '<i class="fa fa-search"></i>' +
                                    '</button>&nbsp;';
                            }
                            return strDatoRetorna;
                        }
                    }
            ],
            'columnDefs': [{
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'render': function (data) {
                    return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
                }
            }]
        });

        $('#objListado-select-all').on('click', function () {
            var rows = objListado.rows({'search': 'applied'}).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
        });

        $('#tabla tbody').on('change', 'input[type="checkbox"]', function () {
            if (!this.checked) 
            {
                var el = $('#objListado-select-all').get(0);
                if (el && el.checked && ('indeterminate' in el)) 
                {
                    el.indeterminate = true;
                }
            }
        });

        $("#tabla_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalAprobar" '+
            'class="btn btn-info btn-sm" title="Aprobar" <i class="fa fa-files-o"></i> Aprobar </button>');

        $("#tabla_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalReasignar" ' +
            'class="btn btn-secondary btn-sm" title="Reasignar" <i class="fa fa-ban"></i> Reasignar </button>');

        $("#tabla_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalRechazar" ' +
            'class="btn btn-danger btn-sm" title="Rechazar" <i class="fa fa-ban"></i> Rechazar </button>');

        $("#btnAprobar").click(function () {
            aprobarRechazarSolicitud("aprobar");
        });
        $("#btnReasignar").click(function () {
            aprobarRechazarSolicitud("reasignar");
        });
        $("#btnRechazar").click(function () {
            aprobarRechazarSolicitud("rechazar");
        });

        /**
         * Documentación para la función 'aprobarRechazarSolicitud'.
         *
         * Función encargada de aprobar/reasignar/rechazar la solicitud.
         *
         * @author Kevin Baque Puya <kbaque@telconet.ec>
         * @version 1.0 17-07-2020
         *
         */
        function aprobarRechazarSolicitud(strAccion)
        {
            var arrayListado = [];
            $('#tabla tbody tr').each(function()
            {
                objSolicitud = $(this).find('input[type="checkbox"]').get(0);
                strEstado    = $(this).find('td').eq(3).text();
                if(objSolicitud.checked && strEstado.trim() == "Pendiente")
                {
                    arrayListado.push(objSolicitud.value.trim());
                }
            });

            if (arrayListado.length > 0)
            {
                var arrayParametros = {
                                        "strCodigo": $("#codigo_aprobar").val(),
                                        "strObservacionAprobar": $("#observacion_aprobar").val(),
                                        "strObservacionReasignar": $("#observacion_reasignar").val(),
                                        "strObservacionRechazar": $("#observacion_rechazar").val(),
                                        "intIdMotivoReasignar":$('#motivo_reasignar option:selected').val(),
                                        "intIdMotivoRechazar":$('#motivo_rechazar option:selected').val(),
                                        "arraySolicitudes": arrayListado,
                                        "strAccion":strAccion,
                                        "strUsuario":$('#usuarios_asignar option:selected').val()
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
                                $('#codigo_aprobar').val('');
                                $('#observacion_aprobar').val('');
                            }
                            else if(strAccion=="reasignar")
                            {
                                $('#observacion_reasignar').val('');
                            }
                            else if(strAccion=="rechazar")
                            {
                                $('#observacion_rechazar').val('');
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
                    failure: function (response) {
                        $('#modalMensajes .modal-body').html('No se pudo '+strAccion+' la(s) solicitude(s) por el siguiente error: ' + response);
                        $('#modalMensajes').modal({show: true});
                    }
                });
            }
            else
            {
                $('#modalMensajes .modal-body').html('Seleccione por lo menos un registro de la lista en estado Pendiente.');
                $('#modalMensajes').modal({show: true});
            }
        }

        $("#buscar").click(function () {
            $('#tabla').DataTable().ajax.reload();
        });

        $("#limpiar").click(function () {
            limpiarFormBuscar();
        });

        /**
         * Documentación para la función 'limpiarFormBuscar'.
         *
         * Función encargada de limpiar los campos.
         *
         * @author Kevin Baque Puya <kbaque@telconet.ec>
         * @version 1.0 17-07-2020
         *
         */
        function limpiarFormBuscar()
        {
            $('#strNombre_buscar').val("");
            $('#strApellido_buscar').val("");
            $('#strRazonSocial_buscar').val("");
            $('#strFechaInicioBuscar').val("");
            $('#strFechaFinBuscar').val("");
            $('#strEstadoBuscar').val("");
        }

        $('form').keypress(function (e) {
            if (e === 13) {
                return false;
            }
        });

        $('input').keypress(function (e) {
            if (e.which === 13) {
                return false;
            }
        });
    });

    /**
     * Documentación para la función 'mostrarModalDetalle'.
     *
     * Función encargada de mostrar el detalle de la solicitud.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-07-2020
     *
     */
    function mostrarModalDetalle(url_accion)
    {
        $.ajax({
            url: url_accion,
            type: 'get',
            dataType: "html",
            success: function (response) {
                $('#modalDetalle .modal-body').html(response);
                $('#modalDetalle').modal({show: true});
            },
            beforeSend: function()
            {
                Ext.get(document.body).mask('Cargando Información.');
            },
            complete: function() 
            {
                Ext.get(document.body).unmask();
            },
            error: function () {
                $('#modalMensajes .modal-body').html('<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>');
                $('#modalMensajes').modal('show');
            }
        });
    }