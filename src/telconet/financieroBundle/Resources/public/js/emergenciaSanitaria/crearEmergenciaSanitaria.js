$(document).ready(function () {
    
    var guardar = 0;
    var reporte = 0;

    $(".spinner_guardarEmerSant,.spinner_reporteEmerSant").hide();
    $("#idFormEmergenciaSanitaria").trigger("reset");
    $("#limpiarEmerSanit").click(function () 
    {
        limpiarDatosPromocion(); 
    });

    $("#saldoDesde").bind('keyup mouseup', function () {
        var valor   = parseInt(this.value);
        document.getElementById('saldoHasta').value = "";
        $("#saldoHasta").prop('min',valor+1);
    });

    $("#saldoHasta").bind('change', function () {
        var intSaldoHasta   = parseInt(this.value);
        var intSaldoDesde   = document.getElementById('saldoDesde').value;
        if(intSaldoHasta <= intSaldoDesde){
            document.getElementById('saldoHasta').value = "";
            $('#modalMensajes .modal-body').html('<p>El Saldo Hasta no puede ser menor o igual al Saldo Desde.</p>');
            $('#modalMensajes').modal('show');
            return false;
        }
    });

    $("#btnGuardarEmerSanit").click(function () 
    {
        guardar = 1;
    }); 

    $("#btnGenerarReporte").click(function () 
    {
        reporte = 1;
    });

    var forms = document.getElementsByClassName('formEmergenciaSanitaria');
    Array.prototype.filter.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
            if (form.checkValidity() === false) 
            {   
                guardar = 0;
                reporte = 0;
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                $('#modalMensajes .modal-body').html("Por favor llenar campos requeridos.");
                $('#modalMensajes').modal({show: true});
            }
            else 
            {   
                if (guardar === 1) 
                {
                    grabarEmergenciaSanitaria();
                    guardar = 0;
                }

                if (reporte === 1)
                {
                    reporteEmergenciaSanitaria();
                    reporte = 0;
                }
            }
        }, false);
    });
    

    function reporteEmergenciaSanitaria()
    {
        $(".spinner_reporteEmerSant").show();
        $("#btnGenerarReporte").attr('disabled','disabled');
        var saldoDesde              = document.getElementById('saldoDesde').value;
        var saldoHasta              = document.getElementById('saldoHasta').value;
        var estadoServicio          = $("#estado_servicio").val();
        var mesesDiferir            = $("#meses_diferir").val();
        var ciclosFacturacion       = $("#ciclos_facturacion").val();
        var motivo                  = "ReporteEmerSanit";

        var parametros = {
            "strSaldoDesde"              : saldoDesde,
            "strSaldoHasta"              : saldoHasta,
            "strMesesDiferir"            : mesesDiferir,
            "arrayCiclosFacturacion"     : ciclosFacturacion,
            "arrayEstadoServicio"        : estadoServicio,
            "strMotivo"                  : motivo
        };
        $.ajax({
            data :  parametros,
            url  :  url_ejecutar_emergencia_sanitaria,
            type :  'post',
            beforeSend: function () {
            },
            success:  function (response) {
                $(".spinner_reporteEmerSant").hide();
                $("#btnGenerarReporte").removeAttr("disabled");
                if(response === "OK")
                {
                    $('#modalMensajes .modal-body').html('<p>Se Ejecutó con éxito el reporte previo para las NCI.</p>');
                    $('#modalMensajes').modal('show');
                    location.reload();
                }
                else
                {
                    if(response === "EXISTE")
                    {
                        $('#modalMensajes .modal-body').html("No se pudo ejecutar el reporte previo para las NCI, aún existe un reporte pendiente de ejecución.");
                        $('#modalMensajes').modal('show');
                    }
                    else
                    {
                        $('#modalMensajes .modal-body').html("No se pudo generar el reporte previo para las NCI, Por favor consulte con el Administrador.");
                        $('#modalMensajes').modal('show');
                    }
                }
            },
            error: function () {
                $(".spinner_reporteEmerSant").hide();
                $("#btnGenerarReporte").removeAttr("disabled");
                $('#modalMensajes .modal-body').html("No se pudo generar el reporte previo para las NCI. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    }

    function grabarEmergenciaSanitaria()
    {
        $(".spinner_guardarEmerSant").show();
        $("#btnGuardarEmerSanit").attr('disabled','disabled');
        var saldoDesde              = document.getElementById('saldoDesde').value;
        var saldoHasta              = document.getElementById('saldoHasta').value;
        var estadoServicio          = $("#estado_servicio").val();
        var mesesDiferir            = $("#meses_diferir").val();
        var ciclosFacturacion       = $("#ciclos_facturacion").val();
        var motivo                  = "EjecutarEmerSanit";

        var parametros = {
            "strSaldoDesde"              : saldoDesde,
            "strSaldoHasta"              : saldoHasta,
            "strMesesDiferir"            : mesesDiferir,
            "arrayCiclosFacturacion"     : ciclosFacturacion,
            "arrayEstadoServicio"        : estadoServicio,
            "strMotivo"                  : motivo
        };
        $.ajax({
            data :  parametros,
            url  :  url_ejecutar_emergencia_sanitaria,
            type :  'post',
            beforeSend: function () {
            },
            success:  function (response) {
                $(".spinner_guardarEmerSant").hide();
                $("#btnGuardarEmerSanit").removeAttr("disabled");
                if(response === "OK")
                {
                    $('#modalMensajes .modal-body').html('<p>Se Ejecutó con éxito la generación de NCI.</p>');
                    $('#modalMensajes').modal('show');
                    location.reload();
                }
                else
                {   
                    if(response === "EXISTE")
                    {
                        $('#modalMensajes .modal-body').html("No se pudo ejecutar las NCI, aún existe un proceso pendiente de ejecución.");
                        $('#modalMensajes').modal('show');
                    }
                    else
                    {
                        $('#modalMensajes .modal-body').html("No se pudo ejecutar las NCI. Por favor consulte con el Administrador.");
                        $('#modalMensajes').modal('show');
                    }
                }
            },
            error: function () {
                $(".spinner_guardarEmerSant").hide();
                $("#btnGuardarEmerSanit").removeAttr("disabled");
                $('#modalMensajes .modal-body').html("No se pudo ejecutar las NCI. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    }

    function limpiarDatosPromocion()
    {
        document.getElementById('saldoDesde').value = "";
        document.getElementById('saldoHasta').value = "";
        $('#meses_diferir').val(null).trigger('change');
        $('#ciclos_facturacion').val(null).trigger('change');
        $('#estado_servicio').val(null).trigger('change');
    }

    var strParametroCab     = "PROCESO_EMER_SANITARIA";
    var strDescripcionMeses = "MES_DIFERIDO";
    $.ajax({
        url     : urlGetParametrosDet,
        method  : 'GET',
        data: {'strParametroCab': strParametroCab,
               'strDescripcionDet': strDescripcionMeses},
        success: function (data) {
            $.each(data.arrayValores, function (id, registro) {
                $("#meses_diferir").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>No se pueden cargar los meses a diferir.</p>');
            $('#modalMensajes').modal('show');
        }
    });
    $('#meses_diferir').select2({
        placeholder :'Seleccione Meses a Diferir'
     });

    $.ajax({
        url     : urlGetCiclos,
        method  : 'GET',
        success: function (data) {
            $.each(data.ciclos_facturacion, function (id, registro) {
                $("#ciclos_facturacion").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>No se pueden cargar los ciclos de facturación.</p>');
            $('#modalMensajes').modal('show');
        }
    });
    $('#ciclos_facturacion').select2({
        multiple    :true,
        placeholder :'Seleccione Ciclo Facturación'
    });

    var strDescripcionEstado = "ESTADOS_SERVICIO";
    $.ajax({
        url     : urlGetParametrosDet,
        method  : 'GET',
        data: {'strParametroCab': strParametroCab,
               'strDescripcionDet': strDescripcionEstado},
        success: function (data) {
            $.each(data.arrayValores, function (id, registro) {
                $("#estado_servicio").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>No se pueden cargar los Estados de Servicio.</p>');
            $('#modalMensajes').modal('show');
        }
    });
    $('#estado_servicio').select2({
        multiple    :true,
        placeholder :'Seleccione Estado Servicio'
    });

});
