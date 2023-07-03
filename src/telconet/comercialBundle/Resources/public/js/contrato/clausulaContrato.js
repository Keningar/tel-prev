var arrayEnunciado = [];
var objEncuesta;
$(document).ready(function () {

  //Se valida que solo se puede seleccionar una opción por pregunta.
  $(document).on('change', 'input:checkbox', function () {
    var $box = $(this);
    if ($box.is(":checked")) {
      // the name of the box is retrieved using the .attr() method
      // as it is assumed and expected to be immutable
      var group = "input:checkbox[name='" + $box.attr("name") + "']";
      // the checked state of the group/box on the other hand will change
      // and the current value is retrieved using .prop() method
      $(group).prop("checked", false);
      $box.prop("checked", true);
    } else {
      $box.prop("checked", false);
    }
  });

  /**
   * Valida Campos requeridos 
   * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
   * @version 1.0 02-03-2022
   * @since 1.0
   */
  var forms = document.getElementsByClassName('formDatosContrato');
  Array.prototype.filter.call(forms, function (form) {
    form.addEventListener('submit', function (event) {
      if (arrayEnunciado == null || arrayEnunciado.length == 0) {
       
        form.classList.add('was-validated');
        $('#modalMensajes .modal-body').html("Debe seleccionar una respuesta a cada cláusula");
        $('#modalMensajes').modal({ show: true });
        return false;
      }
      
      $('#num_tarjeta').removeAttr('disabled');
      $('#titular_cta').removeAttr('disabled');
      var esDataBancaria = $('#esDataBancaria').val();
     
      if (form.checkValidity() === false && esDataBancaria == 'S') {
        event.preventDefault();
        event.stopPropagation();
        form.classList.add('was-validated');
        $('#modalMensajes .modal-body').html("Debe ingresar los datos completos de la forma de pago");
        $('#modalMensajes').modal({ show: true });
      } else {
        guardarTermCondBanc();
      }
      $('#num_tarjeta').attr('disabled', 'disabled');
      $('#titular_cta').attr('disabled', 'disabled');
    }, false);
  });

});

function guardarTermCondBanc() {
  var strMostrarClausula = $('#strMostrarClausula').val();
  if (strMostrarClausula == "N") {
    valoresEncuestas();
  } else {
    actualizarEstadoEnunciado();
  }
}

function actualizarEstadoEnunciado() {
  // Actualizar el estado de la encuesta por punto
  $.ajax({
    url: urlActualizarEstadoClausula,
    type: 'POST',
    data: { "puntoId": (typeof idPunto === 'undefined') ? null : idPunto },
    success: function (data) {
      if (data.status === "ERROR") {
        $('#modalMensajes .modal-body').html(data.message);
        $('#modalMensajes').modal({ show: true });
      } else {
        creacionPorPunto();
        $('#modalMensajes .modal-body').html(data.data);
        $('#modalMensajes').modal({ show: true });
        var urlNueva = "../../../comercial/punto/" + idPunto + "/Cliente/show";
        window.location.href = urlNueva;
      }
    },
    failure: function (response) {
      $('#modalMensajes .modal-body').html('No se pudo crear la solicitud por el siguiente error: ' + response);
      $('#modalMensajes').modal({ show: true });
    }
  });
}

function cargarEncuesta() {
  //Cargar los enunciados de preguntas
  $.ajax({
    url: urlClausulaContrato,
    method: 'POST',
    data: { puntoId: idPunto },
    success: function (data) {
      if (data.status === "ERROR") {
        $('#modalMensajes .modal-body').html(data.message);
        $('#modalMensajes').modal({ show: true });
        return;
      }
      objEncuesta = data.data;
      var enunciados = data.data;
      var enunciadoResp = data.data.enunciadoResp;
      $.each(enunciados, function (id, registro) {
        var strHtmlPreguntas = '';
        var strHtmlDetalle = '';
        var strHtmlLink = '';
        if(registro.clausulas==null){
             strHtmlPreguntas = '';
             strHtmlDetalle = '';
             strHtmlLink = '';
            var preguntas = registro.aplicaDocumento[0].respuestas;
            $.each(preguntas, function (id, regPregunta) {
              var checkedOrNot = '';
              let objEnunciadoResp;
              if (regPregunta.esDefault == 'S') {
                checkedOrNot = 'checked';
              }
              if (enunciadoResp != null && enunciadoResp !== "undefined") {
                objEnunciadoResp = enunciadoResp.find(x => x.idEnunciado == registro.idEnunciado);
              }
              if (objEnunciadoResp != null && objEnunciadoResp !== "undefined" && regPregunta.idRespuesta == objEnunciadoResp.idRespuesta) {
                checkedOrNot = 'checked';
              }
              
              strHtmlPreguntas += '<div class="form-check form-check-inline">' +
                '<label class="form-check-label" for="inlineCheckbox">' + regPregunta.valorRespuesta +
                '</label>&nbsp;&nbsp; <input class="form-check-input" type="checkbox" id="inlineCheckbox" ' +
                ' value="' + registro.idEnunciado + "_" + regPregunta.idRespuesta + "_" + regPregunta.valorRespuesta + "_" + regPregunta.idDocEnunciadoResp + '" ' +
                ' name="' + registro.idEnunciado + '" ' + checkedOrNot + '>' +
                ' </div> ';
            });
            $.each(registro.attEnunciado, function (id, regAttr) {
              if (regAttr.codigo === 'DETALLE') {
                var mensajeRespuesta = regAttr.data[0].valor;
                var titulo = registro.nombreEnunciado || '' + ' - ' + registro.descripcionEnunciado || '';
                strHtmlDetalle += '<a href="#" class="stretched-link col-md-1"' +
                  'onClick="mostrarDetalleEncuesta(' + "'" + titulo + "'," + "'" + mensajeRespuesta + "'" + ');">Ver más</a>';
              }
              if (regAttr.codigo === 'LINK') {
                strHtmlLink += '<div class="form-group row col-md-12">' +
                  '<div class="form-group col-sm-1"> </div>' +
                  '<a href="' + regAttr.data[0].valor + '" target="_blank" class="stretched-link">' + regAttr.data[0].valor + '</a>' +
                  '</div>';
              }
            });
        } 


        $("#clausulaContrato_form").append(' <div class="form-group row" id="rowClausula_' + registro.idEnunciado +
          '"> <label col-form-label class="form-group col-md-10"> <b>' +
          registro.nombreEnunciado + '</b>  ' + (registro.descripcionEnunciado || '') +
          '</label>&nbsp; ' +
          strHtmlDetalle +
          strHtmlLink +
          '<div class="form-group col-md-3"> </div>' +
          strHtmlPreguntas + '</div> '
        );

        if(registro.clausulas!=null){
          $.each(registro.clausulas, function (id, clausulas) {
            var strHtmlPreguntasCla = '';
            var strHtmlDetalleCla  = '';
            var strHtmlLinkCla  = '';
               var preguntas = clausulas.aplicaDocumento[0].respuestas;
               $.each(preguntas, function (id, regPregunta) {
                var checkedOrNot = '';
                let objEnunciadoResp;
                if (regPregunta.esDefault == 'S') {
                  checkedOrNot = 'checked';
                }
                if (enunciadoResp != null && enunciadoResp !== "undefined") {
                  objEnunciadoResp = enunciadoResp.find(x => x.idEnunciado == registro.idEnunciado);
                }
                if (objEnunciadoResp != null && objEnunciadoResp !== "undefined" && regPregunta.idRespuesta == objEnunciadoResp.idRespuesta) {
                  checkedOrNot = 'checked';
                }
                
                strHtmlPreguntasCla += '<div class="form-check form-check-inline" >' +
                  '<label class="form-check-label" for="inlineCheckbox">' + regPregunta.valorRespuesta +
                  '</label>&nbsp;&nbsp; <input class="form-check-input" type="checkbox" id="inlineCheckbox" ' +
                  ' value="' + clausulas.idEnunciado + "_" + regPregunta.idRespuesta + "_" + regPregunta.valorRespuesta + "_" + regPregunta.idDocEnunciadoResp + '" ' +
                  ' name="' + clausulas.idEnunciado + '" ' + checkedOrNot + '>' +
                  ' </div> ';
              });


              $.each(clausulas.attEnunciado, function (id, regAttrCla) {
                if (regAttrCla.codigo === 'DETALLE') {
                  var mensajeRespuesta = regAttrCla.data[0].valor;
                  var titulo = clausulas.nombreEnunciado || '' + ' - ' + clausulas.descripcionEnunciado || '';
                  strHtmlDetalleCla += '<a href="#" class="stretched-link col-md-1"' +
                    'onClick="mostrarDetalleEncuesta(' + "'" + titulo + "'," + "'" + mensajeRespuesta + "'" + ');">Ver más</a>';
                }
                if (regAttrCla.codigo === 'LINK') {
                  strHtmlLinkCla += '<div class="form-group row col-md-12">' +
                    '<div class="form-group col-sm-1"> </div>' +
                    '<a href="' + regAttrCla.data[0].valor + '" target="_blank" class="stretched-link">' + regAttrCla.data[0].valor + '</a>' +
                    '</div>';
                }
              });
              $("#clausulaContrato_form").append(' <div style="padding-left: 2em;"class="form-group row" id="rowClausula_' + clausulas.idEnunciado +
              '"> <label col-form-label class="form-group col-md-10"> <b>' +
              '</b>  ' + (clausulas.descripcionEnunciado || '') +
              '</label>&nbsp; ' +
              strHtmlDetalleCla +
              strHtmlLinkCla +
              '<div class="form-group col-md-3"> </div>' +
              strHtmlPreguntasCla + '</div> '
            );
            
           });
        }
        
      });
    },
    error: function () {
      $('#modalMensajes .modal-body').html("No se pudieron cargar las Formas de Pago. Por favor consulte con el Administrador.");
      $('#modalMensajes').modal({ show: true });
    },
    complete: function (data) {
      if (data.responseJSON.status === "OK") {
        let datos = data.responseJSON.data;
        arrayEnunciado = datos;
        var tieneClausula = $('#hastieneClausulasSaved').val();
        if (tieneClausula == true) {
          $('#clausulaContrato_form').find('input[type=checkbox]').prop('disabled', true);
        }
      }

    }
  });
}

function valoresEncuestas() {
  arrayEncChecked = [];
  var objClausula = {};
  var objDataBancario = {};
  var hayPreguntasPorValidar = false;
  var booleanValidacion = false;
  $.each(arrayEnunciado, function (id, registro) {
    booleanValidacion = false;
    if(registro.clausulas==null)
      { 
        $("#rowClausula_" + registro.idEnunciado).find("input[type=checkbox]").each(function () {
          if ($(this).is(':checked')) {
           
            booleanValidacion = true;
            var idEnunciado = $(this).val().split("_")[0];
            var idRespuesta = $(this).val().split("_")[1];
            // var valorRespuesta = $(this).val().split("_")[2];
            var idDocEnunciadoResp = $(this).val().split("_")[3];
    
            var objRespuesta= registro.aplicaDocumento[0].respuestas.find(x => x.idRespuesta == idRespuesta);
           
            if (objRespuesta.esRequerido == 'N') {
              $('#modalMensajes .modal-body').html("Estimado usuario, Por favor actualizar la respuesta de la cláusula " + registro.nombreEnunciado +
                " para poder continuar con el flujo del contrato");
              $('#modalMensajes').modal({ show: true });
              hayPreguntasPorValidar = true;
            }
            var arrayValorRespuesta = {
              'idRespuesta': idRespuesta,
              'idEnunciado': idEnunciado,
              'idDocEnunciadoResp': idDocEnunciadoResp
            };
    
            arrayEncChecked.push(arrayValorRespuesta);
          }
        });

      }else{
        booleanValidacionClausula = false;
        $.each(registro.clausulas, function (id, clausulas) {
          $("#rowClausula_" + clausulas.idEnunciado).find("input[type=checkbox]").each(function () {
            if ($(this).is(':checked')) {
              booleanValidacionClausula = true;
              var idEnunciado = $(this).val().split("_")[0];
              var idRespuesta = $(this).val().split("_")[1];
              var idDocEnunciadoResp = $(this).val().split("_")[3];

              var  objRespuesta = clausulas.aplicaDocumento[0].respuestas.find(x => x.idRespuesta == idRespuesta);
              if (objRespuesta.esRequerido == 'N') {
                $('#modalMensajes .modal-body').html("Estimado usuario, Por favor actualizar la respuesta de la cláusula " + registro.nombreEnunciado +
                  " para poder continuar con el flujo del contrato");
                $('#modalMensajes').modal({ show: true });
                hayPreguntasPorValidar = true;
              }
              var arrayValorRespuesta = {
                'idRespuesta': idRespuesta,
                'idEnunciado': idEnunciado,
                'idDocEnunciadoResp': idDocEnunciadoResp
              };
      
              arrayEncChecked.push(arrayValorRespuesta);
            }
          });
          if (!booleanValidacionClausula) {
            $('#modalMensajes .modal-body').html("Estimado usuario, Por favor debe seleccionar una respuesta a la cláusula " + clausulas.nombreEnunciado);
            $('#modalMensajes').modal({ show: true });
            return false;
          }  
        
        });
        booleanValidacion = true;
      }
    if (!booleanValidacion) {
      $('#modalMensajes .modal-body').html("Estimado usuario, Por favor debe seleccionar una respuesta a la cláusula " + registro.nombreEnunciado);
      $('#modalMensajes').modal({ show: true });
      return false;
    }   

  });
  if (hayPreguntasPorValidar == true) {
    return;
  }
  var strMostrarClausula = $('#strMostrarClausula').val();
  if (strMostrarClausula === 'N') {
      var idDocumento=null;
      $.each(objEncuesta, function (id, encuestas) {
        if( encuestas.aplicaDocumento!=null && encuestas.aplicaDocumento.length>0  ){
             idDocumentos=encuestas.aplicaDocumento[0].idDocumento;
         }
         if( idDocumentos==null && encuestas.clausulas!=null && encuestas.clausulas.length>0  ){
            $.each(encuestas.clausulas, function (id, clausula) {
                if( clausula.aplicaDocumento!=null && clausula.aplicaDocumento.length>0  ){
                  idDocumentos=clausula.aplicaDocumento[0].idDocumento;
              }
            });
         }
      });
    objClausula = {
      'idDocumento': idDocumentos,
      'requiereAprobacion': false,
      'respuestaCliente': arrayEncChecked,
      'enunciado': arrayEncChecked,
      'listEstado': ['Reenviado'],
      'referenciasDocumento': [{
        'nombreReferencia': 'PERSONA',
        'valor': identificacionCliente
      },
      {
        'nombreReferencia': 'PUNTO',
        'valor': idPunto
      }
      ]

    };
  }
  if (typeof dataPreCliente !== 'undefined' && dataPreCliente.esDebitoBancario === 'S') {
    objDataBancario = {
      'tipoCuentaId': $('#tipo_cuenta').val(),
      'formaPagoId': $("#forma_pago").val(),
      'titular': $('#titular_cta').val(),
      'numeroCuenta': $('#num_tarjeta').val(),
      'anio': $('#anio_vencimiento').val(),
      'mes': $('#mes_vencimiento').val(),
      'bancoTipoCuentaId': $('#tipo_banco').val(),
      'requiereAprobacion': false
    };
  }


  var arrayParam = {
    "puntoId": (typeof idPunto === 'undefined') ? null : idPunto,
    "clausulas": objClausula,
    "dataBancario": objDataBancario
  }
  //Metodo de guardar las clausulas
  $.ajax({
    url: urlGuardarClausulasOrDataBancaria,
    type: 'POST',
    data: arrayParam,
    beforeSend: function () {
      $(".container-preload").fadeIn();
    },
    success: function (data) {
      $(".container-preload").fadeOut();
      if (data.status === "ERROR") {
        $('#modalMensajes .modal-body').html(data.message);
        $('#modalMensajes').modal({ show: true });
      } else {
        $("#sectionButtonContrato").hide();
        $("#clausulaContrato_form").find('input, textarea, checkbox, button, select').attr('disabled', 'disabled');
      }
    },
    failure: function (response) {
      $(".container-preload").fadeOut();
      $('#modalMensajes .modal-body').html('No se pudo crear la solicitud por el siguiente error: ' + response);
      $('#modalMensajes').modal({ show: true });
    },
    complete: function (data) {
      if (data.responseJSON.status === "OK") {
        //creacionPorPunto();
        //  $('#modalMensajes .modal-body').html(data.data);
        // $('#modalMensajes').modal({ show: true });
        var urlNueva = "../../../comercial/punto/" + idPunto + "/Cliente/show";
        window.location.href = urlNueva;
      }
    }
  });
}

/**
 * Documentación para la función 'mostrarModalEdit'.
 *
 * Función encargada de mostrar el detalle de la solicitud.
 *
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>
 * @version 1.0 11-03-2022
 *
 */
function mostrarDetalleEncuesta(title, response) {
  $('#detalleModal').text(title);
  $('#modalMensajes .modal-body').html(response);
  $('#modalMensajes').modal({ show: true });
}

function llenarEncuesta(response) {
  if (response == null || typeof response.enunciados == "undefined") {
    return;
  }
  objEncuesta = response;
  $("#clausulaContrato_form").html('');
  arrayEnunciado = response.enunciados;
  var enunciado = response.enunciados;
  var enunciadoResp = response.enunciadoResp;
  $.each(enunciado, function (id, registro) {
    var strHtmlPreguntas = '';
    var strHtmlDetalle = '';
    var strHtmlLink = '';
    if(registro.clausulas ==null || registro.clausulas.length<=0)
    {         strHtmlPreguntas = '';
             strHtmlDetalle = '';
             strHtmlLink = '';
             preguntas = registro.aplicaDocumento[0].respuestas;
            $.each(preguntas, function (id, regPregunta) {
              var checkedOrNot = '';
              let objEnunciadoResp;
        
              if (enunciadoResp != null && enunciadoResp !== "undefined") {
                objEnunciadoResp = enunciadoResp.find(x => x.idEnunciado == registro.idEnunciado);
              }
              if (objEnunciadoResp != null && objEnunciadoResp !== "undefined" && regPregunta.idRespuesta == objEnunciadoResp.idRespuesta) {
                checkedOrNot = 'checked';
              }
              strHtmlPreguntas += '<div class="form-check form-check-inline">' +
                '<label class="form-check-label" for="inlineCheckbox">' + regPregunta.valorRespuesta +
                '</label>&nbsp;&nbsp; <input class="form-check-input" type="checkbox" id="inlineCheckbox" ' +
                ' value="' + registro.idEnunciado + "_" + regPregunta.idRespuesta + "_" + regPregunta.valorRespuesta + "_" + regPregunta.idDocEnunciadoResp + '" ' +
                ' name="' + registro.idEnunciado + '" ' + checkedOrNot + '>' +
                ' </div> ';
            });    
            
            
        $.each(registro.attEnunciado, function (id, regAttr) {
          if (regAttr.codigo === 'DETALLE') {
            var mensajeRespuesta = regAttr.data[0].valor;
            var titulo = registro.nombreEnunciado || '' + ' - ' + registro.descripcionEnunciado || '';
            strHtmlDetalle += '<a href="#" class="stretched-link col-md-1"' +
              'onClick="mostrarDetalleEncuesta(' + "'" + titulo + "'," + "'" + mensajeRespuesta + "'" + ');">Ver más</a>';
          }
              if (regAttr.codigo === 'LINK') {
                strHtmlLink += '<div class="form-group row col-md-12">' +
                  '<div class="form-group col-sm-1"> </div>' +
                  '<a href="' + regAttr.data[0].valor + '" target="_blank" class="stretched-link">' + regAttr.data[0].valor+ '</a>' +
                  '</div>';
              }
        });
    }

      $("#clausulaContrato_form").append(' <div class="form-group row" id="rowClausula_' + registro.idEnunciado +
        '"> <label col-form-label class="form-group col-md-10"> <b>' +
        registro.nombreEnunciado + '</b>  ' + (registro.descripcionEnunciado || '') +
        '</label>&nbsp; ' +
        strHtmlDetalle +
        strHtmlLink +
        '<div class="form-group col-md-3"> </div>' +
        strHtmlPreguntas + '</div> '
      );   

    if(registro.clausulas!=null && registro.clausulas.length>0)
    {
      $.each(registro.clausulas, function (id, clausula) {
        var strHtmlDetalleCla  = '';
        var strHtmlLinkCla  = '';
        var strHtmlPreguntasCla = '';
        var preguntas = clausula.aplicaDocumento[0].respuestas;
        $.each(preguntas, function (id, regPregunta) {
          var checkedOrNot = '';
          let objEnunciadoResp;
          if (enunciadoResp != null && enunciadoResp !== "undefined") {
            objEnunciadoResp = enunciadoResp.find(x => x.idEnunciado == clausula.idEnunciado);
          }
          if (objEnunciadoResp != null && objEnunciadoResp !== "undefined" && regPregunta.idRespuesta == objEnunciadoResp.idRespuesta) {
            checkedOrNot = 'checked';
          }
          strHtmlPreguntasCla += '<div class="form-check form-check-inline">' +
            '<label class="form-check-label" for="inlineCheckbox">' + regPregunta.valorRespuesta +
            '</label>&nbsp;&nbsp; <input class="form-check-input" type="checkbox" id="inlineCheckbox" ' +
            ' value="' + registro.idEnunciado + "_" + regPregunta.idRespuesta + "_" + regPregunta.valorRespuesta + "_" + regPregunta.idDocEnunciadoResp + '" ' +
            ' name="' + registro.idEnunciado + '" ' + checkedOrNot + '>' +
            ' </div> ';
        });  

        $.each(clausula.attEnunciado, function (id, regAttr) {

          if (regAttr.codigo === 'DETALLE') {
            var mensajeRespuesta = regAttr.data[0].valor;
            var titulo = clausula.nombreEnunciado || '' + ' - ' + clausula.descripcionEnunciado || '';
            strHtmlDetalleCla += '<a href="#" class="stretched-link col-md-1"' +
              'onClick="mostrarDetalleEncuesta(' + "'" + titulo + "'," + "'" + mensajeRespuesta + "'" + ');">Ver más</a>';
          }
          if (regAttr.codigo === 'LINK') {
            strHtmlLinkCla += '<div class="form-group row col-md-12">' +
              '<div class="form-group col-sm-1"> </div>' +
              '<a href="' + regAttr.data[0].valor + '" target="_blank" class="stretched-link">' + regAttr.data[0].valor+ '</a>' +
              '</div>';
          }
        });

          $("#clausulaContrato_form").append(' <div class="form-group row" id="rowClausula_' + clausula.idEnunciado +
          '" style="margin-left: 2em;"> <label col-form-label class="form-group col-md-10"> <b>' +
           '</b>  ' + (clausula.descripcionEnunciado || '') +
          '</label>&nbsp; ' +
          strHtmlDetalleCla +
          strHtmlLinkCla +
          '<div class="form-group col-md-3"> </div>' +
          strHtmlPreguntasCla + '</div> '
        );

      });
    }
  });
}