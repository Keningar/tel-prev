
function validarFacturacion(esOfiFacturacion)
{
    if (esOfiFacturacion == 'S')
    {
        $('#telconet_schemabundle_infooficinagrupotype_numEstabSri').attr('required', 'required');
        $("[for=telconet_schemabundle_infooficinagrupotype_numEstabSri]")[0].innerHTML = '* Número Estab. SRI:';
    }
    else
    {
        $('#telconet_schemabundle_infooficinagrupotype_numEstabSri').removeAttr('required');
        $("[for=telconet_schemabundle_infooficinagrupotype_numEstabSri]")[0].innerHTML = 'Número Estab. SRI:';
    }
}