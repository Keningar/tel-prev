
Ext.onReady(function ()
{
    //Presenta Ventana para confirmar la Pre Cancelación del Saldo Diferido
    $("#btnEjecutaPreCancelacion").click(function ()
    {
        $('#confirmModal').modal('show');
    });

    //Ejecuta la Pre Cancelación del Saldo Diferido
    $("#btnConfirmPreCancelacion").click(function ()
    {
        $('#confirmModal').modal('hide');
        $('#modal-loading').modal('show');

        var strCodEmpresa = $("#strCodEmpresa").val();
        var intIdServicio = $("#intIdServicio").val();

        $.ajax({
            data: {"intIdServicio": intIdServicio, "strCodEmpresa": strCodEmpresa},
            url: urlAjaxEjecutarPreCancelacionDiferida,
            type: 'post',
            success: function (response) {

                $('#modal-loading').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();

                if (response === "OK")
                {
                    $('#loading-mensaje').hide();
                    $('#btnMensajePreCancelacion').removeAttr('data-dismiss');
                    $('#btnMensajePreCancelacion').attr('href', '/financiero/reportes/estado_cuenta_pto');
                    $('#modal-mensaje .modal-body').html('<h6>La Pre-Cancelación del saldo diferido se ejecutó con éxito.</h6>');
                    $('#modal-mensaje').modal('show');

                } else
                {
                    $('#loading-mensaje').hide();
                    $('#btnMensajePreCancelacion').attr('data-dismiss', 'modal');
                    $('#modal-mensaje .modal-body').html('<h6>Hubo un error. Favor comuníquese con el Departamento de Sistemas</h6>');
                    $('#modal-mensaje').modal('show');
                }

            },
            failure: function (response) {

                $('#loading-mensaje').hide();
                $('#modal-loading').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();

                $('#btnMensajePreCancelacion').attr('data-dismiss', 'modal');
                $('#modal-mensaje .modal-body').html('<h6>Hubo un error. Favor comuníquese con el Departamento de Sistemas</h6>');
                $('#modal-mensaje').modal('show');
            }
        });
    });

    $("#btnCancelaPreCancelacion").click(function ()
    {
        javascript:history.back();
    });

    $("#btnMensajePreCancelacion").click(function ()
    {
        $('#loading-mensaje').show();
    });

});



