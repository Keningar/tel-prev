$("#personaempleadotype_identificacionCliente").attr('readonly', true);
$("#personaempleadotype_identificacionCliente").attr('style', 'color:gray; font-weight:bold');
$("#personaempleadotype_tipoIdentificacion").attr('disabled', true);
$("#personaempleadotype_tipoIdentificacion").attr('style', '-moz-appearance: none; text-indent: 0.01px; text-overflow:"" ');

function esRuc()
{
    if ($('#personaempleadotype_tipoIdentificacion').val() == 'RUC')
    {
        $('#personaempleadotype_identificacionCliente').removeAttr('maxlength');
        $('#personaempleadotype_identificacionCliente').attr('maxlength', '13');
        $('#personaempleadotype_identificacionCliente').val('');
    }
    else
    {
        $('#personaempleadotype_identificacionCliente').removeAttr('maxlength');
        $('#personaempleadotype_identificacionCliente').attr('maxlength', '10');
        $('#personaempleadotype_identificacionCliente').val('');
    }
}

function mostrarDiv(div)
{
    capa = document.getElementById(div);
    capa.style.display = 'block';
}

function ocultarDiv(div)
{
    capa = document.getElementById(div);
    capa.style.display = 'none';
}

$(function()
{
    $("#personaempleadotype_identificacionCliente").keydown(function(event)
    {
        if (!isNumeric(event))
            return false;
    });

});

function isNumeric(event)
{
    return ((event.keyCode > 7 && event.keyCode < 10)
        || (event.keyCode > 47 && event.keyCode < 60)
        || (event.keyCode > 95 && event.keyCode < 106)
        || event.keyCode == 17
        || event.keyCode == 116);
}