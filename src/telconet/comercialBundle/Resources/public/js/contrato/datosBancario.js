var datosBancarios;
var objDataDatosBancarios;
var tipoCuentaBanco;
var correoSeleccionado;
var continuarFlujoDatoBancario = true;
var dataPreCliente;
var ejecutarUnaVezSol = true;
var boolEditarFormaPago = false;
var isMostrarInformacionCliente = true;
var objCorreo;
var idPersonaFormaContacto;
var idPersonaFormaContactoNum;
var numeroSeleccionado;
var solicitaNumero= false;
$(document).ready(function () {
  
    $(".spinner_tajeta_ctaBanco").hide();
    $(".spinner_Banco").hide();
    $("#tabFormaPago").hide();
    let cantidadAniosMostrar = 15;

    $("#strCorreoEnvio").select2({placeholder: "Seleccionar correo", multiple: false});

    $("#strNumeroEnvio").select2({placeholder: "Seleccionar Numero", multiple: false});

    $('#forma_pago').change(function()
    {
        var idFormaPago = $('#forma_pago').val();
        if(idFormaPago == 3){
            cargarTipoCuenta();                         
        } else {
            $(".spinner_tajeta_ctaBanco").hide();
            $(".spinner_Banco").hide();
            $("#tipo_cuenta").val("").trigger('change');
        }
    });
  
    var pantallaFormaPago   = $('#pantallaEdicionFormaPago').val();
    if(typeof pantallaFormaPago !== 'undefined' && pantallaFormaPago === 'S'){
        boolEditarFormaPago = true;
    }

    $('#tipo_cuenta').change(function()
    {
        $(".spinner_Banco").show();
        obtieneBancos('');
    });

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

    $("#btnSolInfCliente").click(function () {
        correoSeleccionado = $('#strCorreoEnvio option:selected').text();
        if(solicitaNumero){
            numeroSeleccionado = $('#strNumeroEnvio option:selected').text();
            if (numeroSeleccionado == "") {
                $("#faltaCorreo").show();
                $("#msjCorreoNoSeleccionado").text('Estimado usuario, por favor seleccione el '+
                'número al cual se enviarán las credenciales');
            }
        }

        if (correoSeleccionado == "") {
            $("#faltaCorreo").show();
            $("#msjCorreoNoSeleccionado").text('Estimado usuario, por favor seleccione el '+
            'correo electrónico al cual se enviarán las credenciales');
        }  
        
        if(correoSeleccionado != "" && ((solicitaNumero  && numeroSeleccionado != "") || !solicitaNumero)){
            $("#modalCrear").modal('hide');
            if(ejecutarUnaVezSol){
                limpiarCamposFormaPago();
                solicitarInformacionCliente();
                ejecutarUnaVezSol = false;
            }
        }
    });

    //Consultar si la forma de pago es debito bancario de no serlo se trunca el proceso
    $.ajax({
        url: urlDatosIniciales,
        type: 'POST',
        data: {'intPersonaEmpRolId': idPersonaEmpresaRol},
        beforeSend: function () {
            $(".container-preload").fadeIn();
        },
        success: function (data) {
            $(".container-preload").fadeOut();
            if (data.status == 'OK') {
                $("#strMostrarClausula").val(data.strMostrarClausula);
                $("#strMostrarInfoBanco").val(data.strMostrarInfoBanco);

                $("#esDebitoBancario").val(data.esDebitoBancario);
                $('#esRolCliente').val(data.esRolCliente);

                if(data.esRolCliente == 'S' && data.esDebitoBancario == 'S' && data.strMostrarInfoBanco == 'S'){
                    $('#esDataBancaria').val('S');
                    $("#tabFormaPago").show();
                    inhabilitarTodo();
                    dataPreCliente = data;
                    cargarTipoCuenta();
                } else{
                    $("#tabFormaPago").hide();
                }
                if(data.esTarjeta !== 'S'){
                    $('#row_fechas').hide();
                }
                creacionPorPunto();
            }
        },
        error: function () {
            $(".container-preload").fadeOut();
            $('#modalMensajes .modal-body').html("No se pudo cargar los datos iniciales del proceso. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        },
        complete: function (data) {
            $(".container-preload").fadeOut();
            if(data.responseJSON === 'undefined' || 
                data.responseJSON.strMostrarInfoBanco === 'undefined' || 
                data.responseJSON.strMostrarInfoBanco === 'N'){
                $("#tabFormaPago").hide();
            }
            $('#modalCargando').modal('hide');
        }
    });

    

    // https://stackoverflow.com/questions/51659414/populate-dropdown-list-with-current-day-month-and-year
    let selectYear = $("#anio_vencimiento");
    let anioActual = new Date().getFullYear();
    for (var y = 0; y < cantidadAniosMostrar; y++) {
        let yearElem = document.createElement("option");
        yearElem.value = anioActual
        yearElem.textContent = anioActual;
        selectYear.append(yearElem);
        anioActual++;
      }
});

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
    numeroSeleccionado = $('#strNumeroEnvio option:selected').text();
    var arrayParam = {
                        'intPersonaFormaContacto'   : $('#strCorreoEnvio').val(),
                        'intPersonaFormaContactNum' : $('#strNumeroEnvio').val(),
                        'intPersonaEmpRolId'        : idPersonaEmpresaRol,
                        'intPuntoId'                : (typeof idPunto === 'undefined') ? null : idPunto,
                        'boolReenvio'               : $('#chkReenviarCredenciales').is(':checked') ? true : false,
                        'isEditarFormaPago'         : boolEditarFormaPago
    }

    $.ajax({
        url: urlSolicitarInformacionCliente,
        type: 'POST',
        data: arrayParam,
        beforeSend: function () {
            $(".container-preload").fadeIn();
        },
        success: function (data) {
            $(".container-preload").fadeOut();
            if(data.status == 'ERROR'){
                $('#modalMensajes .modal-body').html(data.message);
                $('#modalMensajes').modal({show: true});
            } else {
                $('#modalMensajes .modal-body').html(data.data);
                $('#modalMensajes').modal({show: true});
            }
        },
        failure: function (response) {
            $(".container-preload").fadeOut();
            $(".spinner_Banco").hide();
            $('#modalMensajes .modal-body').html('No se pudo crear la solicitud por el siguiente error: ' + response);
            $('#modalMensajes').modal({show: true});
        }, complete: function (data) {
            $(".container-preload").fadeOut();
            $('#modalCargando').modal('hide');
            $("#mostrarCorreo").show();
            $("#correoSeleccionadoMostrar").text(correoSeleccionado);
            if(solicitaNumero)
            {
                $("#mostrarNumero").show();
                $("#numeroSeleccionadoMostrar").text(numeroSeleccionado);
            }

            $(".spinner_Banco").hide();
            creacionPorPunto();
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
    * Documentación para la función 'solicitarInformacionCliente'.
    *
    * Función encargada de solicitar información al cliente
    *
    * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
    * @version 1.0 19-04-2022
    */
  function llenarFormaPago()
  {
      //Obtiene las formas de pagos para los datos bancarios.
    $.ajax({
        url: urlGetFormasPago,
        method: 'GET',
        success: function (data) {
            $("#tipo_cuenta option").each(function () {
                $(this).remove();
            });
            $.each(data.formas_de_pago, function (id, registro) {
                $("#forma_pago").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar las Formas de Pago. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        },
        complete: function() {
            if(dataPreCliente != null && dataPreCliente != 'undefined'){
                $("#forma_pago").val(dataPreCliente.formaPagoId).change();
            }
            if(datosBancarios != null && datosBancarios != 'undefined'){
                $('#forma_pago').val(datosBancarios.formaPagoId);
                $('#forma_pago').trigger('change');
            }
            var idFormaPago = $('#forma_pago').val();
            if(idFormaPago == 3){
                cargarTipoCuenta();
            } else {
                $("#tabFormaPago").hide();
                inhabilitarTodo();
            }
        }
    });
  }

  function cargarTipoCuenta(){
    $(".spinner_tajeta_ctaBanco").show();
    $.ajax({
        url     : urlGetTipoCuenta,
        method  : 'GET',
        success: function (data) {
            $(".spinner_tajeta_ctaBanco").hide();
            $("#tipo_cuenta option").each(function () {
                $(this).remove();
            });
            $.each(data.jsonTipoCuenta, function (id, registro) {
                $("#tipo_cuenta").append('<option name="'+registro.strDescCuenta+
                                                '" value="' + registro.intIdAdmiTipoCUenta + '">'+
                                                registro.strDescCuenta + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>No se pueden cargar las opciones por tipo de Emisor.</p>');
            $('#modalMensajes').modal('show');
        },
        complete: function() {
            if(dataPreCliente != null && dataPreCliente != 'undefined'){
                $('#tipo_cuenta').val(dataPreCliente.tipoCuentaId).change();
            }
            if(datosBancarios != null && datosBancarios != 'undefined'){
                $('#tipo_cuenta').val(datosBancarios.tipoCuentaId);
                $('#tipo_cuenta').trigger('change');
            } 
            obtieneBancos('');
        }
    });
  }

  /**
   * Documentación para la función 'obtieneBancos'.
   *
   * Función encargada de solicitar información de los bancos.
   *
   * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
   * @version 1.0 25-04-2022
   */
  function obtieneBancos(bancoTipoCuentaId)
  {
      var tipoCuenta = $('#tipo_cuenta').val();
      if (bancoTipoCuentaId != '') {
          parametros = "tipoCuenta=" + tipoCuenta + "&bcoTipoCtaId=" + bancoTipoCuentaId;
      } else
      {
          parametros = "tipoCuenta=" + tipoCuenta;
      }

      $.ajax({
          type: "POST",
          data: parametros,
          url: urlListadoBancosAsociados,
          success: function(msg) 
          {
            $(".spinner_Banco").hide();
              if (msg.msg == 'ok')
              {
                  document.getElementById("tipo_banco").innerHTML = msg.div;
              }
              else
                  document.getElementById("tipo_banco").innerHTML = msg.msg;
          },
          complete: function() {
            if(dataPreCliente != null && dataPreCliente != 'undefined'){
                $('#tipo_banco').val(dataPreCliente.bancoTipoCuentaId).change();
            }
            if(datosBancarios != null && datosBancarios != 'undefined'){
                $('#tipo_banco').val(datosBancarios.bancoTipoCuentaId);
                $('#tipo_banco').trigger('change');
            }    
          }
      });
  }

  /**
   * Documentación para la función 'mostrarInformacionCliente'.
   *
   * Función encargada de mostrar la información del cliente.
   *
   * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
   * @version 1.0 25-04-2022
   */
   function mostrarInformacionCliente(){
    if(datosBancarios != null && datosBancarios != 'undefined'){
        if(datosBancarios.estado == 'Eliminado'){
            limpiarCamposFormaPago();
        } else {
            $('#num_tarjeta').val(datosBancarios.mostrarCuenta);
            $('#titular_cta').val(datosBancarios.titular);
            $('#anio_vencimiento').val(datosBancarios.anio);
            $('#mes_vencimiento').val(datosBancarios.mes);
        }
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
   function obtenerCorreo(idFormaContacto,idFormaContactoNum){
     // Cargar spinner de correo
     console.log("estoy aquyi");
     $('#divNumeroEnvio').hide();
    $.ajax({
        url: urlGetCorreo,
        method: 'post',
        data : {
            idPunto: (typeof idPunto === 'undefined') ? null : idPunto
        },
        success: function (data) {
            $(".spinner_correo").hide();
            $("#strCorreoEnvio").empty();
            $("#strNumeroEnvio").empty();
            $('#divNumeroEnvio').hide();
            $("#strCorreoEnvio").append('<option value=Seleccione Correo></option>');
            objCorreo = data;
            $.each(data, function (id, registro) {
                if(registro.correos !=null){
                    $.each(registro.correos, function (id, registroC) {
                        $("#strCorreoEnvio").append('<option value=' + registroC.clave + '>' + registroC.valor + ' </option>');
                    });
                }
                if(registro.numeros !=null && registro.numeros.length>0){
                    solicitaNumero=true;
                    $("#divNumeroEnvio").show();
                    $.each(registro.numeros, function (id, registroN) {
                        $("#strNumeroEnvio").append('<option value=' + registroN.clave + '>' + registroN.valor + ' </option>');
                    });

                }
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar la información de contacto electrónico del cliente. Por favor comuníquese con el departamento de Sistemas.");
            $('#modalMensajes').modal({show: true});
        },
        complete: function() {
            $.each(objCorreo, function (id, registro) {
                if(registro.correos !=null){
                    $.each(registro.correos, function (id, registroC) {
                        if(registroC.clave == idPersonaFormaContacto){
                            $("#mostrarCorreo").show();
                            $("#correoSeleccionadoMostrar").text(registroC.valor);
                            $("#strCorreoEnvio").val(registroC.clave).trigger('change');
                        }
                    });
                }
                if(registro.numeros !=null && registro.numeros.length>0){

                    $.each(registro.numeros, function (id, registroN) {
                        if(registroN.clave == idPersonaFormaContactoNum){
                            $("#mostrarNumero").show();
                            $("#numeroSeleccionadoMostrar").text(registroN.valor);
                            $("#strNumeroEnvio").val(registroN.clave).trigger('change');
                        }
                    });
                }
              

            });
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

        $('#infocontratoformapagotype_anioVencimiento').val(datosBancarios.anio);
        $('#infocontratoformapagotype_anioVencimiento').trigger('change');

        let mesVencimiento = Number(datosBancarios.mes);
        $('#infocontratoformapagotype_mesVencimiento').val(mesVencimiento);
        $('#infocontratoformapagotype_mesVencimiento').trigger('change');
   }

   function inhabilitarTodo()
    {
        $('#tipo_cuenta').attr("disabled", "disabled");
        $('#tipo_banco').attr("disabled", "disabled");
        $('#num_tarjeta').attr("disabled", "disabled");
        $('#titular_cta').attr("disabled", "disabled");
        $('#forma_pago').attr("disabled", "disabled");
        $('#anio_vencimiento').attr("disabled", "disabled");
        $('#mes_vencimiento').attr("disabled", "disabled");
    }

    function limpiarCamposFormaPago(){
        $('#num_tarjeta').val('').trigger('change');
        $('#titular_cta').val('').trigger('change');
        $('#anio_vencimiento').val('').trigger('change');
        $('#mes_vencimiento').val('').trigger('change');
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
                    'intIdPersonaEmpresaRol': (typeof idPersonaEmpresaRol === 'undefined') ? null : idPersonaEmpresaRol },
            beforeSend: function()
            {
                $(".container-preload").fadeIn();
            },
            success: function (data) {
                $(".container-preload").fadeOut();
                isMostrarInformacionCliente = true;
                if(data.status == 'OK'){
                    objDataDatosBancarios = data.data.creacionPunto;
                    if(objDataDatosBancarios.dataBancario != null && objDataDatosBancarios.dataBancario != 'undefined'){
                        datosBancarios = objDataDatosBancarios.dataBancario;
                        mostrarInformacionCliente();
                    }
                } else {
                    isMostrarInformacionCliente = false;
                    $('#accionPermitida').show();
                    $('#mensajeLinkBancario').text(data.message);
                }
            },
            error: function () {
                $(".container-preload").fadeOut();
                $('#modalMensajes .modal-body').html("No se pudo obtener la información. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            },
            complete: function (data) {
                if(!isMostrarInformacionCliente){
                    return;
                }
                if(!boolEditarFormaPago){
                    llenarEncuesta(data.responseJSON.data.clausulas);

                    if(data.responseJSON.data.creacionPunto.hastieneClausulasSaved == true ){
                        $("#clausulaContrato_form").find('input, textarea, checkbox, button, select').attr('disabled','disabled');
                    } else {
                        var strMostrarClausula = $('#strMostrarClausula').val();
                        if(strMostrarClausula == 'S'){
                            $("#clausulaContrato_form").find('input, textarea, checkbox, button, select').attr('disabled','disabled');
                        }
                    }
                 }

                if(typeof contratoId === 'undefined'){
                    var esDebitoBancario = $('#esDebitoBancario').val();
                    if(esDebitoBancario == 'S'){
                        llenarFormaPago();
                    }
                } else {
                    llenarEditarFormaPago();
                }
            }
        });
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
                    'intIdPersonaEmpresaRol': (typeof idPersonaEmpresaRol === 'undefined') ? null : idPersonaEmpresaRol },
            beforeSend: function () {
                $(".container-preload").fadeIn();
            },
            success: function (data) {
                $(".container-preload").fadeOut();
                isMostrarInformacionCliente = true;
                var hastieneClausulasSaved = (typeof data.data === 'undefined') ? false : data.data.hastieneClausulasSaved;
                $('#hastieneClausulasSaved').val(hastieneClausulasSaved);
                if(data.status == 'OK'){
                    objDataDatosBancarios = data.data;
                    if(data.data.hastieneClausulasSaved == true){
                        $('#enlacesDatosBancario').hide();
                        recibirInformacionCliente();
                    } else {
                        var strMostrarClausula = $("#strMostrarClausula").val();
                        var strMostrarInfoBanco = $("#strMostrarInfoBanco").val();
                        if(strMostrarClausula == 'S' || strMostrarInfoBanco == 'S'){
                            $('#enlacesDatosBancario').show();
                        }
                        if(strMostrarClausula == 'N'){
                            cargarEncuesta();
                        } else {
                            $("#clausulaContrato_form").find('input, textarea, checkbox, button, select').attr('disabled','disabled');
                        }
                    }

                    if(objDataDatosBancarios.dataBancario != null && objDataDatosBancarios.dataBancario != 'undefined'){
                        datosBancarios = objDataDatosBancarios.dataBancario;
                        mostrarInformacionCliente();
                    }
                } else {
                    isMostrarInformacionCliente = false;
                    $('#modalMensajes .modal-body').html(data.message);
                    $('#modalMensajes').modal({show: true});
                }
            },
            error: function () {
                $(".container-preload").fadeOut();
                $('#modalMensajes .modal-body').html("No se pudo obtener la información. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            },
            complete: function (data) {
                if(!isMostrarInformacionCliente){
                    return;
                }
                if(!boolEditarFormaPago){
                    $("#clausulaContrato_form").html('');
                    $("#sectionButtonContrato").show();
                }
                if(typeof data.responseJSON.data !== 'undefined' && 
                    data.responseJSON.data.haslinkDatosBancarios == false) {
                    $("#accionPermitida").show();
                    $("#mensajeLinkBancario").text("El punto no requiere solicitar información del cliente.");
                    $("#formularioDatosContrato").hide();
                    return;
                }
                if(typeof data.responseJSON.data !== 'undefined' && 
                    data.responseJSON.data.hastieneClausulasSaved == true) {
                    $("#sectionButtonContrato").hide();
                }
                if(typeof data.responseJSON.data !== 'undefined' && 
                    data.responseJSON.data.hasReenvioDatosInvalidos == true) {
                    $("#mostrarCheckReenvio").show();
                } else {
                    $("#mostrarCheckReenvio").hide();
                }
                if(typeof contratoId === 'undefined'){
                    var esDebitoBancario = $('#esDebitoBancario').val();
                    if(esDebitoBancario == 'S'){
                        llenarFormaPago();
                    }
                } else {
                    llenarEditarFormaPago();
                }
                idPersonaFormaContacto = (typeof objDataDatosBancarios === 'undefined')  ? null : 
                                                (typeof objDataDatosBancarios.idPersonaFormaContacto === 'undefined')  ? null :
                                              objDataDatosBancarios.idPersonaFormaContacto;
                idPersonaFormaContactoNum = (typeof objDataDatosBancarios === 'undefined')  ? null : 
                                              (typeof objDataDatosBancarios.idPersonaFormaContactNum === 'undefined')  ? null :
                                            objDataDatosBancarios.idPersonaFormaContactNum
                obtenerCorreo(idPersonaFormaContacto,idPersonaFormaContactoNum);
            }
        });
    }