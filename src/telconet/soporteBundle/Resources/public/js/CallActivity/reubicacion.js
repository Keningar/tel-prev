
Ext.onReady(function ()
{

    function limpiarDatos()
    {
        $("#radioNcN").prop("checked", true);
        $("#btnEjecutaNcReubicacion").hide();
        $('#divMensaje').html('');
        $("#divMensaje").hide();
        $("#divRequisitos").hide();
        $('#strCumpleReqNc').val('');

        $('#porcentajeNc').val("").trigger('change');
        $('#nombreMotivo').val("").trigger('change');
        $('#nombreAutorizado').val("").trigger('change');
        $('#textoObservacion').val("");
    }

    limpiarDatos();

    $(".limpiarDatos").click(function ()
    {
        limpiarDatos();
    });


    $("#btnEjecutaNcReubicacion").click(function ()
    {
        var forms = document.getElementsByClassName('formNcReubicacion');
        Array.prototype.filter.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false && $('#strCumpleReqNc').val() == 'N')
                {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add('was-validated');

                } else
                {
                    $("#confirmModal").modal('show');
                    $("#solicitudModal").modal('hide');
                }
            }, false);
        });
    });


    $('input:radio[name="radio_nc"]').change(function () {

        if ($(this).val() === 'S')
        {
            validaRequisitosAnexo();
        } else
        {
            limpiarDatos();
        }
    });


    //Porcentaje NC
    var strParametroCab = "PROCESO_REUBICACION";
    var strDescripcionDet = "PORCENTAJE_NC";
    $.ajax({
        url: urlGetPorcentajesNc,
        method: 'GET',
        data: {'strParametroCab': strParametroCab,
            'strDescripcionDet': strDescripcionDet},
        success: function (data) {
            $.each(data.porcentajesNc, function (id, registro) {
                $("#porcentajeNc").append('<option value=' + registro.id + '>' + registro.valor + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>No se pueden cargar los porcentajes.</p>');
            $('#modalMensajes').modal('show');
        }
    });
    $('#porcentajeNc').select2({placeholder: "Seleccionar", allowClear: true, width: '100%'});


    //Personal Autoriza NC
    var strParametroCab = "PROCESO_REUBICACION";
    var strDescripcionDet = "PERSONAL_AUTORIZA_NC";
    $.ajax({
        url: urlGetPersonalAutorizadoNc,
        method: 'GET',
        data: {'strParametroCab': strParametroCab,
            'strDescripcionDet': strDescripcionDet},
        success: function (data) {
            $.each(data.personalAutorizadoNc, function (id, registro) {
                $("#nombreAutorizado").append('<option value=' + registro.intIdEmpleado + '>' + (registro.strNombre).ucwords() + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>No se pueden cargar las personas autorizadas.</p>');
            $('#modalMensajes').modal('show');
        }
    });
    $('#nombreAutorizado').select2({placeholder: "Seleccionar", allowClear: true, width: '100%'});


    //Motivos NC
    var strParametroCab = "PROCESO_REUBICACION";
    var strDescripcionDet = "MOTIVO_NC";
    $.ajax({
        url: urlGetMotivosNc,
        method: 'GET',
        data: {'strParametroCab': strParametroCab,
            'strDescripcionDet': strDescripcionDet},
        success: function (data) {
            $.each(data.motivosNc, function (id, registro) {
                $("#nombreMotivo").append('<option value=' + registro.id + '>' + registro.valor + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>No se pueden cargar los motivos.</p>');
            $('#modalMensajes').modal('show');
        }
    });
    $('#nombreMotivo').select2({placeholder: "Seleccionar", allowClear: true, width: '100%'});



    function validaRequisitosAnexo()
    {
        var cumpleRequisitos;
        var intIdTareaReub = $("#idTareaReub").val();
        var divMensaje = document.getElementById("divMensaje");
        $(".spinnerAplicaNc").removeAttr('hidden');

        $.ajax({
            url: urlValidaAplicaNc,
            method: 'GET',
            data: {'intIdTareaReub': intIdTareaReub},
            success: function (data) {
                $(".spinnerAplicaNc").attr('hidden', 'true');
                cumpleRequisitos = data.strAplicaNc;

                // S= cumple, N= no cumple
                if (cumpleRequisitos == 'S')
                {
                    $('#strCumpleReqNc').val('S');
                    divMensaje.className = "alert alert-success";
                    $('#divMensaje').html('Cumple con los requisitos para acceder a una NC y se otorga el descuento del 100%.');
                    $("#divMensaje").show();
                }

                if (cumpleRequisitos == 'N')
                {
                    $('#strCumpleReqNc').val('N');
                    divMensaje.className = "alert alert-danger";
                    $('#divMensaje').html('No cumple con los requisitos para acceder a una NC por retención.');
                    $("#divMensaje").show();
                    $("#divRequisitos").show();
                }

                $("#btnEjecutaNcReubicacion").show();

            },
            error: function () {
                $(".spinnerAplicaNc").attr('hidden', 'true');
                cumpleRequisitos = 'N';
                divMensaje.className = "alert alert-danger";
                $('#divMensaje').html('No cumple con los requisitos para acceder a una NC por retención.');
                $("#divMensaje").show();
            }
        });
    }


    $("#btnConfirmNcReubicacion").click(function ()
    {
        $('#confirmModal').modal('hide');
        $('#modal-loading').modal('show');

        var intIdTareaReub = $("#idTareaReub").val();
        var strCumpleReqNc = $("#strCumpleReqNc").val();
        var arrayCaracNc   = {};

        var strPerAutorizaNc = $('#nombreAutorizado option:selected').text();
        var strMotivoNc = $('#nombreMotivo option:selected').text();
        var intPorcentajeNc = $('#porcentajeNc option:selected').text();
        var strObservacion = $('#textoObservacion').val();
        
        if(strCumpleReqNc == "N")
        {
            arrayCaracNc = {"AUTORIZADO_NC_REUBICACION": strPerAutorizaNc, "PORCENTAJE_NC_REUBICACION": intPorcentajeNc,
                            "MOTIVO_NC_REUBICACION": strMotivoNc, "OBSERVACION_NC_REUBICACION": strObservacion};
        }
        
        $.ajax({
            data: {"intIdTareaReub": intIdTareaReub, "strCumpleReqNc": strCumpleReqNc, "strMotivoNc": strMotivoNc, "arrayCaracNc": arrayCaracNc},
            url: urlEjecutarNcReubicacion,
            type: 'post',
            success: function (response) {

                $('#modal-loading').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();

                if (response.strStatus == "OK")
                {
                    $('#loading-mensaje').hide();
                    $('#btnMensajeNCReub').removeAttr('data-dismiss');
                    $('#btnMensajeNCReub').attr('href', '');
                    $('#modal-mensaje .modal-body').html('<h6>'+response.strMensaje+'</h6>');
                    $('#modal-mensaje').modal('show');

                } else
                {
                    $('#loading-mensaje').hide();
                    $('#btnMensajeNCReub').attr('data-dismiss', 'modal');
                    $('#modal-mensaje .modal-body').html('<h6>'+response.strMensaje+'</h6>');
                    $('#modal-mensaje').modal('show');

                }

            },
            failure: function (response) {

                $('#loading-mensaje').hide();
                $('#modal-loading').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();

                $('#btnMensajeNCReub').attr('data-dismiss', 'modal');
                $('#modal-mensaje .modal-body').html('<h6>Hubo un error. Favor comuníquese con el Departamento de Sistemas</h6>');
                $('#modal-mensaje').modal('show');
            }
        });
    });

    $("#btnMensajeNCReub").click(function ()
    {
        $('#loading-mensaje').show();
    });

    String.prototype.ucwords = function () {
        str = this.toLowerCase();
        return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
            function ($1) {
                return $1.toUpperCase();
            });
    };

});