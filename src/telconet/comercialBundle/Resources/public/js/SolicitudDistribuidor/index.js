    $(document).ready(function ()
    {
        var boolPermisoAprobar  = (typeof boolAprobar === 'undefined')  ? false : (boolAprobar ? true : false);
        var boolPermisoRechazar = (typeof boolRechazar === 'undefined') ? false : (boolRechazar ? true : false);
        var boolPermisoCrear    = (typeof boolCrear === 'undefined')    ? false : (boolCrear ? true : false);
        $('#productos').select2({placeholder: "Seleccionar", multiple: true, width: "365px"});
        $(".spinner_sect_productos").hide();
        $('#linea_negocio').select2({placeholder: "Seleccionar", multiple: true, width: "365px"});
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
        getLineaNegocio();
        $('#linea_negocio').change(function () {
            $(".spinner_sect_productos").show();
            $('#productos').select2({placeholder: "Seleccionar", multiple: true, width: "365px"});
            $.ajax({
                url: url_productos,
                method: 'post',
                data: {arrayLineaNegocio: $('#linea_negocio').val()},
                success: function (data) {
                    $(".spinner_sect_productos").hide();
                    $.each(data.arrayProductos, function (id, registro) {
                        $("#productos").append('<option value=' + registro.id + '>' + registro.descripcionProducto + ' </option>');
                    });
                },
                error: function () {
                    $('#modalMensajes .modal-body').html("No se pudieron cargar los productos. Por favor comuníquese con el departamento de Sistemas.");
                    $('#modalMensajes').modal({show: true});
                }
            });
        });

        $.ajax({
            url: url_vendedor,
            method: 'post',
            success: function (data)
            {
                $.each(data.arrayVendedores, function (id, registro)
                {
                    $("#vendedor_crear").append('<option value=' + registro.login + '>' + registro.nombre + '</option>');
                });
            },
            error: function ()
            {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los vendedores. Por favor comuníquese con el departamento de Sistemas.");
                $('#modalMensajes').modal({show: true});
            }
        });

        var objListado = $('#tabla').DataTable({
            "ajax": {
                "url": url_grid,
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
                    param.strIdentificacion = $("#strIdentificacion_buscar").val();
                    param.strRazonSocial    = $("#strRazonSocial_buscar").val();
                    param.strFechaInicio    = $('#strFechaInicioBuscar').val();
                    param.strFechaFin       = $('#strFechaFinBuscar').val();
                    param.strEstado         = $('#strEstadoBuscar option:selected').val();
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
                    {"data": "intIdSolicitud"},
                    {"data": "strIdentificacion"},
                    {"data": "strCliente"},
                    {"data": "strVendedor"},
                    {"data": "strEstado"},
                    {"data": "strObservacion"},
                    {"data": "strFeCreacion"},
                    {"data": "strUsrCreacion"},
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

        if(boolPermisoCrear)
        {
            $("#tabla_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalCrear" '+
            'class="btn btn-success btn-sm" title="Nueva Solicitud" <i class="fa fa-files-o"></i> Nueva Solicitud </button>');
        }
        if(boolPermisoAprobar)
        {
            $("#tabla_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalAprobar" '+
            'class="btn btn-info btn-sm" title="Aprobar" <i class="fa fa-files-o"></i> Aprobar </button>');
        }
        if(boolPermisoRechazar)
        {
            $("#tabla_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalRechazar" ' +
            'class="btn btn-danger btn-sm" title="Rechazar" <i class="fa fa-ban"></i> Rechazar </button>');
        }
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
        $("#btnCrear").click(function () {
            crearSolicitud();
        });
        $("#btnAprobar").click(function () {
            aprobarRechazarSolicitud("aprobar");
        });
        $("#btnRechazar").click(function () {
            aprobarRechazarSolicitud("rechazar");
        });

        /**
         * Documentación para la función 'crearSolicitud'.
         *
         * Función encargada de crear la solicitud.
         *
         * @author Kevin Baque Puya <kbaque@telconet.ec>
         * @version 1.0 11-05-2021
         *
        */
        function crearSolicitud()
        {
            var arrayParametros = {"strVendedor"       : $("#vendedor_crear").val(),
                                   "strIdentificacion" : $("#identificacion_crear").val(),
                                   "strRazonSocial"    : $("#razon_social_crear").val(),
                                   "arrayLineaNegocio" : $('#linea_negocio').val(),
                                   "arrayProductos"    : $("#productos").val(),
                                   "strObservacion"    : $("#observacion_crear").val()
                                  };
            $.ajax({
                data: arrayParametros,
                url: url_new,
                type: 'post',
                success: function (response) {
                    if (response)
                    {
                        $('#vendedor_crear').val('');
                        $('#identificacion_crear').val('');
                        $('#razon_social_crear').val('');
                        $('#observacion_crear').val('');
                        $('#linea_negocio').select2({placeholder: "Seleccionar", multiple: true, width: "365px"});
                        $('#linea_negocio').empty();
                        $('#productos').select2({placeholder: "Seleccionar", multiple: true, width: "365px"});
                        $('#productos').empty();
                        getLineaNegocio();
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
                    $('#modalMensajes .modal-body').html('No se pudo crear la solicitud por el siguiente error: ' + response);
                    $('#modalMensajes').modal({show: true});
                }
            });
        }

        /**
         * Documentación para la función 'aprobarRechazarSolicitud'.
         *
         * Función encargada de aprobar/rechazar la solicitud.
         *
         * @author Kevin Baque Puya <kbaque@telconet.ec>
         * @version 1.0 11-05-2021
         *
         */
        function aprobarRechazarSolicitud(strAccion)
        {
            var arrayListado = [];
            $('#tabla tbody tr').each(function()
            {
                objSolicitud = $(this).find('input[type="checkbox"]').get(0);
                strEstado    = $(this).find('td').eq(4).text();
                if(objSolicitud.checked && (strEstado.trim() == "Pendiente" || strEstado.trim() == "Pendiente Gerente"))
                {
                    arrayListado.push(objSolicitud.value.trim());
                }
            });

            if (arrayListado.length > 0)
            {
                var arrayParametros = {
                                        "strObservacionAprobar"  : $("#observacion_aprobar").val(),
                                        "strObservacionRechazar" : $("#observacion_rechazar").val(),
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
                                $('#observacion_aprobar').val('');
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
                var strEstadoMensaje = 'Pendiente o Pendiente Gerente';
                $('#modalMensajes .modal-body').html('Seleccione por lo menos un registro de la lista en estado '+strEstadoMensaje+'.');
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
         * @version 1.0 11-05-2021
         *
         */
        function limpiarFormBuscar()
        {
            $('#strIdentificacion_buscar').val("");
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
     * @version 1.0 11-05-2021
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
    /**
     * Documentación para la función 'getLineaNegocio'.
     *
     * Función encargada de mostrar las líneas de negocio.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 11-05-2021
     *
    */
    function getLineaNegocio()
    {
        $.ajax({
            url: url_lineaNegocio,
            method: 'post',
            success: function (data) {
                $(".spinner_sect_lineaNegocio").hide();
                $.each(data.arrayLineaNegocio, function (id, registro) {
                    $("#linea_negocio").append('<option value=' + registro.id + '>' + registro.lineaNegocio + ' </option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar las líneas de negocio. Por favor comuníquese con el departamento de Sistemas.");
                $('#modalMensajes').modal({show: true});
            }
        });
    }