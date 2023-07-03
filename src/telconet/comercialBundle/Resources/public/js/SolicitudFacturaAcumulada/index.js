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
            url: url_motivo_solicitud_factura,
            method: 'GET',
            success: function (data) {
                $.each(data.motivos, function (id, registro) {
                    $("#motivo_solicitud").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los motivos de rechazo. Por favor comuníquese con el departamento de Sistemas.");
                $('#modalMensajes').modal({show: true});
            }
        });


        $.ajax({
            url: url_estados_solicitud_factura,
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
            retrieve: true,
            "ajax": {
                "url": url_grid_solicitud_proyecto,
                "type": "POST",
                "timeout": 60000,
                beforeSend: function()
                {
                    Ext.get(document.body).mask('Cargando Información.');
                },
                complete: function() 
                {
                    Ext.get(document.body).unmask();
                },
                "data": function (param) {
                    param.strLogin        = $("#strNombre_buscar").val();
                    param.strFechaInicio   = $('#strFechaInicioBuscar').val();
                    param.strFechaFin      = $('#strFechaFinBuscar').val();
                    param.strEstado        = $('#strEstadoBuscar option:selected').text();
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
                    {"data": "intNumero"},
                    {"data": "strSolicitud"},
                    {"data": "strVendedor"},
                    {"data": "strEstado"},
                    {"data": "strObservacion"},
                    {"data": "valor_total"},
                    {"data": "strEmision"},
                    {"data": "strAcciones",
                        "render": function (data)
                        {
                            var strDatoRetorna = '';
                            strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" ' + ' title="Ver Detalle" ' +
                                    'onClick="javascript:mostrarModalDetalle('+ data.strSolicitud +');">' + '<i class="fa fa-search"></i>' +
                                    '</button>&nbsp;';
                            if (data.strCargo !== '' && (data.strCargo=='SUBGERENTE' || data.strCargo =='GERENTE_VENTAS'))
                            {
                                strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" ' + ' title="Aprobar Solicitud" ' +
                                    'onClick=aprobarSolicitud('+data.strSolicitud+') data-toggle="modal" data-target="#modalAprobar">'+ '<i class="fa fa-check"></i>' +
                                    '</button>&nbsp;';
                                
                                strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" ' + ' title="Rechazar Solicitud" ' +
                                    'onClick=rechazarSolicitud('+data.strSolicitud+') data-toggle="modal" data-target="#modalRechazar">'+ '<i class="fa fa-close"></i>' +
                                    '</button>&nbsp;';
                            }
                            return strDatoRetorna;
                        }
                    }
            ]
        });

        $("#tabla_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalSolicitar" '+
            'class="btn btn-info btn-sm" title="Nueva Solicitud" onclick="listarFacturas()" <i class="fa fa-files-o"></i> Nueva Solicitud </button>');

        $("#btnSolicitar").click(function () {
            crearSolicitud();
            listarFacturas();
        });

        $("#btnAprobar").click(function () {
            aprobarRechazarSolicitud("Aprobar");
        });
        
        $("#btnRechazar").click(function () {
            aprobarRechazarSolicitud("rechazar");
        });
       

        /**
         * Documentación para la función 'crearSolicitud'.
         *
         * Función encargada de crear la solicitud.
         *
         * @author David Leon <mdleon@telconet.ec>
         * @version 1.0 15-01-2021
         *
         */
        function crearSolicitud()
        {
                var arrayParametros = {
                                        "strFactura": document.getElementById('factura').value,
                                        "strObservacionSoli": $("#observacion_solicitud").val(),
                                        "intIdMotivoSoli":$('#motivo_solicitud option:selected').val(),
                                        "strTipoFact":$('input:radio[name=exampleRadios]:checked').val()
                                      };
                $.ajax({
                    data: arrayParametros,
                    url: url_solicitud_factura ,
                    type: 'post',
                    success: function (response) {
                        if (response)
                        {   
                            $('#observacion_solicitud').val('');
                            $('#modalSolicitar').modal("hide");
                            
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
        
        function aprobarRechazarSolicitud(strAccion)
        {
            if(strAccion=="rechazar")
            {
                var strSolicitud = document.getElementById('solicitudR').value;
            }
            else
            {
                var strSolicitud = document.getElementById('solicitudA').value;
            }
            var arrayParametros = {
                                    "strSolicitud": strSolicitud,
                                    "strObservacionSoli": $("#observacion_rechazar").val(),
                                    "strAccion":strAccion
                                  };
            $.ajax({
                data: arrayParametros,
                url: url_aprobar_rechazar_solicitud,
                type: 'post',
                success: function (response) {
                    if (response)
                    {
                        if(strAccion=="rechazar")
                        {
                            $('#observacion_rechazar').val("");
                        }
                        $('#tituloAprob').val("");
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
         * @author David Leon <mdleon@telconet.ec>
         * @version 1.0 17-07-2020
         *
         */
        function limpiarFormBuscar()
        {
            $('#strNombre_buscar').val("");
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
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 17-07-2020
     *
     */
    function mostrarModalDetalle(strSolicitud)
    {
        var arrayParametros = {
                                    "strSolicitud": strSolicitud
                                  };
        $.ajax({
            data : arrayParametros,
            url: url_show_solicitud,
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
     * Documentación para la función 'listarFacturas'.
     *
     * Función encargada de mostrar las facturas manuales que se pueden gestionar.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 11-01-2021
     *
     */
    function listarFacturas()
    {
        $('#tablaFac').DataTable({
        retrieve: true,
        autoWidth: false,
        "ajax": {
            "url": url_listar_facturas,
            "type": "POST",
            beforeSend: function()
            {
                Ext.get(document.body).mask('Cargando Información.');
            },
            complete: function() 
            {
                Ext.get(document.body).unmask();
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
                {"data": "intNumero"},
                {"data": "strFactura"},
                {"data": "strConsumo"},
                {"data": "strVendedor"},
                {"data": "strCliente"},
                {"data": "strLogin"},
                {"data": "valor_total"},
                {"data": "strTipoFac",
                render: function (data)
                    {
                        var strRadioMrc = '';
                        if (data.strTipo !== '' && data.strTipo === 'MRC') 
                        {
                            strRadioMrc = '<input type="radio" disabled="true" id='+data.strFactura+' name="'+data.strFactura+'" value="1" checked >';
                        }
                        else
                        {
                            strRadioMrc = '<input type="radio" disabled="true" id='+data.strFactura+' name="'+data.strFactura+'" value="2"  >';
                        }
                        return strRadioMrc;
                    }
                },
                {"data": "strTipoFac2",
                render: function (data)
                    {
                        var strRadioNrc = '';
                        if (data.strTipo !== '' && data.strTipo === 'NRC') 
                        {
                            strRadioNrc = '<input type="radio" disabled="true" id='+data.strFactura+'  name="'+data.strFactura+'" value="11" checked >';
                        }
                        else
                        {
                            strRadioNrc = '<input type="radio" disabled="true" id='+data.strFactura+' name="'+data.strFactura+'" value="22"  >';
                        }
                        return strRadioNrc;
                    }
                },
                {"data": "strAcciones",
                    "render": function (data)
                    {
                        var strDatoRetorna = '';
                        if (data.linkVer !== '') 
                        {
                            strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" title="Solicitar"  ' +
                                 'onClick=listarMotivos('+data.strFactura+') data-toggle="modal" data-target="#modalApruebaSol">' + '<i class="fa fa-file-text"></i>' +
                                ' </button>&nbsp;';
                        }
                        return strDatoRetorna;
                    }
                }
        ]
        });
    }
    
    function listarMotivos(strFactura)
    {
        if(strFactura !=='')
        {    
            $("#tituloFact").html('<option id=factura value=' + strFactura + '>Factura #' + strFactura + '</option>');
        }
    }
    
    function aprobarSolicitud(strSolicitud)
    {
        if(strSolicitud !=='')
        {    
            $("#tituloAprob").html('<input type="hidden" id=solicitudA value=' + strSolicitud + '>Desea aprobar la Solicitud #' + strSolicitud + '</input>');
        }
    }
    
    function rechazarSolicitud(strSolicitud)
    {
        if(strSolicitud !=='')
        {    
            $("#tituloRech").html('<input type="hidden" id=solicitudR value=' + strSolicitud + '>Desea rechazar la Solicitud #' + strSolicitud + '</input>');
        }
    }