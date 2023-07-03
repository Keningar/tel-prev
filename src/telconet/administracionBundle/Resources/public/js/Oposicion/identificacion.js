function validarIdentificacionTipo()
{
    var currentIdentificacion = $(input).val();
    var currentTipo = $(inputTipo).val();
    $("#diverrorident").hide().html('');
    if (currentIdentificacion != '' && currentTipo != '')
    {
        $.ajax({
            type : "POST",
            data : "identificacion=" + currentIdentificacion + "&tipo=" + currentTipo,
            url : url_validar_identificacion_tipo,
            beforeSend : function()
            {
                $('#img-valida-identificacion').attr("src", url_img_loader);
            },
            success : function(msg)
            {
                if (msg != '')
                {
                    flagIdentificacionCorrecta = 0;
                    $('#img-valida-identificacion').attr("title", msg).attr("src", url_img_delete);
                    $("#diverrorident").show().html(isRuc(currentTipo, msg));
                    check_sri.checked = false;
                    $(input).focus();
                    $('button[type=submit]').attr('disabled', 'disabled');
                }
                else
                {
                    flagIdentificacionCorrecta = 1;
                    $('#img-valida-identificacion').attr("title", "Identificacion correcta").attr("src", url_img_check);
                    $("#diverrorident").hide().html('');
                    $("#div_check_sri").hide();
                    $('button[type=submit]').removeAttr('disabled');
                }
            }
        });
    }
}

function ischeked()
{
    var check_sri = document.querySelector('#check_sri');
    if( $('#check_sri').attr('checked') ) 
    {
        var resultado = window.confirm('¿Esta seguro que el ruc que ingreso se encuentra registrado en el SRI?');
        if (resultado === true) 
        {
            $('#img-valida-identificacion').attr("title", "Identificacion correcta").attr("src", url_img_check);
            $("#diverrorident").hide().html('');
            isVerifiedRuc(true);
            $('button[type=submit]').removeAttr('disabled');
        } 
        else 
        { 
            check_sri.checked = false;
            isVerifiedRuc(false);
        }
    }
}

function isRuc(tipoDocumento, msg)
{
    var arrayEmpresa = $('#globalEmpresaEscogida option:selected').val().trim().split("@@");
    var idEmpresa = arrayEmpresa[0];
    if (tipoDocumento === 'RUC' && idEmpresa === '10')
    {
        $("#div_check_sri").show();
        return 'RUC con inconsistencias, por favor valídelo en la página del SRI';
    }else
    {
        $("#div_check_sri").hide();
        return msg;
    }
}

function isVerifiedRuc(estado)
{
    var precliente = $("#preclientetype_observacion");
    var cliente = $("#clientetype_rucClienteInvalido");
    var convertirCliente = $("#convertirtype_rucClienteInvalido");
    if(precliente.val() != undefined)
    {
        precliente.val(estado);
    }
    else if(cliente.val() != undefined)
    {
        cliente.val(estado);
    }
    else if(convertirCliente.val() != undefined)
    {
        convertirCliente.val(estado);
    }
    else if (precliente.val() == '')
    {
        precliente.val(estado);
    }
    else if (cliente.val() == '')
    {
        cliente.val(estado);
    }
    else if (convertirCliente.val() == '')
    {
        convertirCliente.val(estado);
    }
}
