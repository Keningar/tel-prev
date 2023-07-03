var ejecutarUnaVezSol=true;
var boolEditarFormaPago = true;
$(document).ready(function () {

    obtenerCorreo(idPersonaEmpresaRol);

    $.ajax({
        url: urlDatosIniciales,
        type: 'POST',
        data: {'intPersonaEmpRolId': idPersonaEmpresaRol},
        success: function (data) {
            if (data.status == 'OK') {
                $('#paramFormaPago').val(data.strMostrarInfoBanco);
                var idFormaPago     = $('#infocontratotype_formaPagoId').val();
                if(data.strMostrarInfoBanco == 'S' && idFormaPago == 3){
                    $("#tabFormaPago").show();
                    bloquearFormaPagoDebito();
                }
            }
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudo cargar los datos iniciales del proceso. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
    
    $('#infocontratotype_formaPagoId').change(function()
    {
        mostrarPanelSolicitar();
    });

    $("#btnSolInfCliente").click(function () {
        correoSeleccionado = $('#strCorreoEnvio option:selected').text();
        if (correoSeleccionado == "") {
            $("#faltaCorreo").show();
            $("#msjCorreoNoSeleccionado").text('Estimado usuario, por favor seleccione el '+
            'correo electrónico al cual se enviarán las credenciales');
        } else {
            $("#modalCrear").hide();
            if(ejecutarUnaVezSol){
                solicitarInformacionCliente();
                ejecutarUnaVezSol = false;
            }
        }
    });

});

function mostrarPanelSolicitar(){
    var idFormaPago     = $('#infocontratotype_formaPagoId').val();
    var paramFormaPago  = $('#paramFormaPago').val();
    $("#tabFormaPago").hide();
    if(idFormaPago == 3 && paramFormaPago == 'S'){
        creacionPorPunto();
        $("#tabFormaPago").show();
        bloquearFormaPagoDebito();
    } else {
        desbloquearFormaPagoDebito();
    }
}

/**
   * Documentación para la función 'obtenerCorreo'.
   *
   * Función encargada de mostrar los correos del cliente.
   *
   * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
   * @version 1.0 25-04-2022
   */ 
 function obtenerCorreo(idFormaContacto){
    // Cargar spinner de correo
   $.ajax({
       url: urlGetCorreo,
       method: 'post',
       data : {
           idPunto: (typeof idPunto === 'undefined') ? null : idPunto
       },
       success: function (data) {
           $(".spinner_correo").hide();
           $("#strCorreoEnvio").empty();
           $("#strCorreoEnvio").append('<option value=Seleccione Correo></option>');
           objCorreo = data;
           $.each(data, function (id, registro) {
               $("#strCorreoEnvio").append('<option value=' + registro.clave + '>' + registro.valor + ' </option>');
           });
       },
       error: function () {
           $('#modalMensajes .modal-body').html("No se pudieron cargar la información de correo electrónico del cliente. Por favor comuníquese con el departamento de Sistemas.");
           $('#modalMensajes').modal({show: true});
       },
       complete: function() {
           $.each(objCorreo, function (id, registro) {
               if((typeof idPersonaFormaContacto !== 'undefined') && registro.clave == idPersonaFormaContacto){
                   $("#mostrarCorreo").show();
                   $("#correoSeleccionadoMostrar").text(registro.valor);
                   $("#strCorreoEnvio").val(registro.clave).trigger('change');
               }

           });
       }
   });
  }

  /**
 * Documentación para la función 'solicitarInformacionCliente'.
 *
 * Función encargada de solicitar información al cliente
 *
 * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
 * @version 1.0 19-04-2022
 */
function solicitarInformacionCliente()
{
    correoSeleccionado = $('#strCorreoEnvio option:selected').text();
    var arrayParam = {
                        'intPersonaFormaContacto'   : $('#strCorreoEnvio').val(),
                        'intPersonaEmpRolId'        : idPersonaEmpresaRol,
                        'intPuntoId'                : (typeof idPunto === 'undefined') ? null : idPunto,
                        'boolReenvio'               : $('#chkReenviarCredenciales').is(':checked') ? true : false,
                        'isEditarFormaPago'         : boolEditarFormaPago
    }

    $.ajax({
        url: urlSolicitarInformacionCliente,
        type: 'POST',
        data: arrayParam,
        success: function (data) {
            if(data.status == 'ERROR'){
                $('#modalMensajes .modal-body').html(data.message);
                $('#modalMensajes').modal({show: true});
            } else {
                $('#modalMensajes .modal-body').html(data.data);
                $('#modalMensajes').modal({show: true});
                limpiarDebitoBancario();
            }
        },
        failure: function (response) {
            $('#modalMensajes .modal-body').html('No se pudo crear la solicitud por el siguiente error: ' + response);
            $('#modalMensajes').modal({show: true});
        }, complete: function (data) {
            $("#mostrarCorreo").show();
            $("#correoSeleccionadoMostrar").text(correoSeleccionado);
            ejecutarUnaVezSol = true;
        }
    });
}

/**
 * Documentación para la función 'recibirInformacionCliente'.
 *
 * Función encargada de recibir la información del cliente
 *
 * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
 * @version 1.0 19-04-2022
 */
 function recibirInformacionCliente()
 {
    cargarValorDefecto();
 }

 /**
     * Documentación para la función 'cargarValorDefecto'.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 25-05-2022
     */
  function cargarValorDefecto(){
    $.ajax({
        url: urlObtenerInformacionCliente,
        method: 'POST',
        data: { 'intPuntoId': (typeof idPunto === 'undefined') ? null : idPunto,
                'intIdPersonaEmpresaRol': (typeof idPersonaEmpresaRol === 'undefined') ? null : idPersonaEmpresaRol,
                'isEditarFormaPago' : boolEditarFormaPago},
        beforeSend: function () {
            Ext.get(document.body).mask('Cargando Información.');
        },
        success: function (data) {
            if(data.status == 'OK'){
                objDataDatosBancarios = data.data.creacionPunto;
                if(objDataDatosBancarios.dataBancario != null && objDataDatosBancarios.dataBancario != 'undefined'
                    && objDataDatosBancarios.dataBancario.estado == 'PreActivo'){
                    datosBancarios = objDataDatosBancarios.dataBancario;
                    $('#objDatoBancario').val(JSON.stringify(datosBancarios));
                    llenarEditarFormaPago();
                }
            } else {
                $('#modalMensajes .modal-body').html(data.message);
                $('#modalMensajes').modal({show: true});
            }
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudo obtener la información. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        },
        complete: function () {
            Ext.get(document.body).unmask();
        }
    });
}

/**
   * Documentación para la función 'llenarEditarFormaPago'.
   *
   * Función encargada de mostrar los correos del cliente.
   *
   * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
   * @version 1.0 25-04-2022
   */
 function llenarEditarFormaPago(){
    $('#infocontratotype_formaPagoId').val(datosBancarios.formaPagoId);
    $('#infocontratotype_formaPagoId').trigger('change');

    $('#infocontratoformapagotype_tipoCuentaId').val(datosBancarios.tipoCuentaId);
    $('#infocontratoformapagotype_tipoCuentaId').trigger('change');

    tipoCuentaBanco = datosBancarios.bancoTipoCuentaId;
    $('#infocontratoformapagotype_bancoTipoCuentaId').val(datosBancarios.bancoTipoCuentaId).change();

    $('#infocontratoformapagotype_numeroCtaTarjeta').val(datosBancarios.mostrarCuenta);
    $('#infocontratoformapagotype_numeroCtaTarjeta').trigger('change');

    $('#infocontratoformapagotype_titularCuenta').val(datosBancarios.titular).trigger('change');

    $('#infocontratoformapagotype_anioVencimiento').val(datosBancarios.anio);
    $('#infocontratoformapagotype_anioVencimiento').trigger('change');

    let mesVencimiento = Number(datosBancarios.mes);
    $('#infocontratoformapagotype_mesVencimiento').val(mesVencimiento);
    $('#infocontratoformapagotype_mesVencimiento').trigger('change');

    bloquearFormaPagoDebito();
}

function bloquearFormaPagoDebito(){
    $('#infocontratoformapagotype_tipoCuentaId').attr('disabled', true);
    $('#infocontratoformapagotype_bancoTipoCuentaId').attr('disabled', true);
    $('#infocontratoformapagotype_numeroCtaTarjeta').attr('disabled', true);
    $('#infocontratoformapagotype_titularCuenta').attr('disabled', true);
    $('#infocontratoformapagotype_anioVencimiento').attr('disabled', true);
    $('#infocontratoformapagotype_mesVencimiento').attr('disabled', true);
}

function desbloquearFormaPagoDebito(){
    $('#infocontratoformapagotype_tipoCuentaId').removeAttr('disabled');
    $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr('disabled');
    $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr('disabled');
    $('#infocontratoformapagotype_titularCuenta').removeAttr('disabled');
    $('#infocontratoformapagotype_anioVencimiento').removeAttr('disabled');
    $('#infocontratoformapagotype_mesVencimiento').removeAttr('disabled');

    limpiarDebitoBancario();
}

function limpiarDebitoBancario(){
    $('#infocontratoformapagotype_numeroCtaTarjeta').val('');
    $('#infocontratoformapagotype_titularCuenta').val('');
}

/**
     * Documentación para la función 'creacionPorPunto'.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 25-05-2022
     */
 function creacionPorPunto(){
    $.ajax({
        url: urlCreacionPunto,
        method: 'POST',
        data: { 'intPuntoId': (typeof idPunto === 'undefined') ? null : idPunto,
                'intIdPersonaEmpresaRol': (typeof idPersonaEmpresaRol === 'undefined') ? null : idPersonaEmpresaRol,
                'isEditarFormaPago' : boolEditarFormaPago },
        success: function (data) {
            if(data.status == 'OK'){
                objDataDatosBancarios = data.data;
                if(typeof objDataDatosBancarios !== 'undefined' && 
                    objDataDatosBancarios.hasReenvioDatosInvalidos == true) {
                    $("#mostrarCheckReenvio").show();
                } else {
                    $("#mostrarCheckReenvio").hide();
                }
                idPersonaFormaContacto = (typeof objDataDatosBancarios === 'undefined')  ? null : 
                                            (typeof objDataDatosBancarios.idPersonaFormaContacto === 'undefined')  ? null :
                                          objDataDatosBancarios.idPersonaFormaContacto
                obtenerCorreo(idPersonaFormaContacto);
            } else {
                $('#modalMensajes .modal-body').html(data.message);
                $('#modalMensajes').modal({show: true});
            }
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudo obtener la información. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
}