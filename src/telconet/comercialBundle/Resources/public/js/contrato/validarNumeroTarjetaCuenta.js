/**
 * Documentación para el método 'validarNumeroTarjetaCuenta'.
 *
 * Valida si los datos asociados al numero de cuenta o tarjeta son validos
 * 
 * @param integer    tipoCuentaId Obtiene el Id del tipo de cuenta del contrato del cliente
 * @param integer    bancoTipoCuentaId Obtiene el Id del Banco asociado al contrato del cliente
 * @param integer    numeroCtaTarjeta Obtiene el Numero de Cuenta o tarjeta de Credito asociado al contrato
 * @param integer    codigoVerificacion Obtiene el Codigo de Verificacion asociado al contrato del cliente
 *
 * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
 * @version 1.0 02-03-2015
 * 
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.1 24-04-2020 Se agrega validación para envío de número de cta-tarjeta desenmascarado en caso de no ser editado dicho campo.
 * 
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.2 20-05-2020 Se agrega validación para envío de parámetros provenientes de formulario de edición.
 */
function validarNumeroTarjetaCuenta()
{
    $('button[type=submit]').attr('disabled', 'disabled');
    var tipoCuentaId = $('#infocontratoformapagotype_tipoCuentaId').val();
    var bancoTipoCuentaId = $('#infocontratoformapagotype_bancoTipoCuentaId').val();
    var numeroCtaTarjeta = $('#infocontratoformapagotype_numeroCtaTarjeta').val();
    var codigoVerificacion = $('#infocontratoformapagotype_codigoVerificacion').val();
    var formaPagoId        = $('#infocontratotype_formaPagoId').val();
    
    if(document.getElementById("prefijoEmpresa") !== null)
    {
        var prefijoEmpresa  = document.getElementById("prefijoEmpresa").value;
        var strNumCtaTarj   = document.getElementById("strNumCtaTarj").value; 
        if((prefijoEmpresa == 'MD' ||prefijoEmpresa == 'EN' ) && isNaN(numeroCtaTarjeta))
        {
            numeroCtaTarjeta = strNumCtaTarj;
        }        
    }    

    $.ajax({
        type: "POST",
        data: "tipoCuentaId=" + tipoCuentaId + "&bancoTipoCuentaId=" + bancoTipoCuentaId + "&numeroCtaTarjeta=" + numeroCtaTarjeta +
            "&codigoVerificacion=" + codigoVerificacion + "&formaPagoId=" + formaPagoId,
        url: url_validarNumeroTarjetaCta,
        timeout: 10000,
        success: function(msg) {
            if (msg.msg == 'ok')
            {
                mensajes_bin = "";
                var info = JSON.stringify(msg.validaciones);
                var myArray = JSON.parse(info);
                for (var i = 0; i < myArray.length; i++) {
                    var object = myArray[i];
                    mensajes_bin += object.mensaje_validaciones + ' <br /> ';
                }
                $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
                $('button[type=submit]').attr('disabled', 'disabled');
            }
            else
            {
                mensajes_bin = "";
                $('#mensaje_validaciones').addClass('campo-oculto').html("");
                $('button[type="submit"]').removeAttr('disabled');
                aprobarClick();
            }
        }
    });
}