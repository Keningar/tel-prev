var ids_array = new Array();
$(document).ready(function () {
  var boolPermisoCrear =
    typeof boolCrear === "undefined" ? false : boolCrear ? true : false;
  var boolPermisoEditar =
    typeof boolEditar === "undefined" ? false : boolEditar ? true : false;
  var boolPermisoDelete =
    typeof boolDelete === "undefined" ? false : boolDelete ? true : false;
  var boolPermisoClonar =
    typeof boolClonar === "undefined" ? false : boolClonar ? true : false;

  $(".submit").click(function () {
    return false;
  });

  var objListado = $("#tabla").DataTable({

    ajax: {
      url: url_grid,
      type: "POST",
      beforeSend: function () {
        Ext.get(document.body).mask("Cargando Información.");
      },
      complete: function () {
        Ext.get(document.body).unmask();
      }, 
      failure: function () {
        $("#modalMensajes .modal-body").html(
          "<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>"
        );
        $("#modalMensajes").modal("show");
      }
    },
    language: {
      oPaginate: {
        sPrevious: "Anterior",
        sNext: "Siguiente",
      },
      sProcessing: "Procesando...",
      lengthMenu: "Muestra _MENU_ filas por página",
      zeroRecords: "No hay información disponible",
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      infoEmpty: "No hay información disponible",
      infoFiltered: "(filtrado de _MAX_ total filas)",
      search: "Buscar:",
      processing: true,
      loadingRecords: "Cargando datos...",
    },
    columns: [

      { data: "idCampania" },
      { data: "strNombreCampania" },
      { data: "strEstado" },
      { data: "strFechaIni",
      render: function (data){
        if(null!== data && ''!== data){
          data = data + ':00'
          const [dateStr, timeStr] = data.split(' ');
          const [day, month, year] = dateStr.split('/');
          const [hours, minutes, seconds] = timeStr.split(':');
          let date = new Date(
            +year,
            month - 1,
            +day,
            +hours,
            +minutes,
            +seconds
          );
          date = moment(date).format('DD/MM/YYYY HH:mm')
          return date;
        }else{
          return data;
        }
      }  
    },
      {
        data: "strAcciones",

        render: function (data, row, meta) {
          var strDatoRetorna = "";

          if (data.linkVer !== "") {
            strDatoRetorna += " <button type='button' class='btn btn-warning btn-sm' ";
            strDatoRetorna += " title='Ver Detalle' ";
            strDatoRetorna += " onclick='mostrarModalDetalle(" + JSON.stringify(meta) + ")'> ";
            strDatoRetorna += " <i class='fa fa-search'></i> ";
            strDatoRetorna += " </button>&nbsp; ";
          }

          if (data.linkDetener !== "") {
            strDatoRetorna +=
              '<button type="button" class="btn btn-secondary btn-sm" ' +
              ' title="Detener" ' +
              "onClick=\"javascript:detenerCampania('" + data.linkDetener + "');\">" +
              '<i class="fa fa-stop"></i>' +
              "</button>&nbsp;";
          }

          if (data.linkReini !== "") {
              strDatoRetorna +=
              '<button type="button" class="btn btn-success btn-sm" ' +
              ' title="Reiniciar" ' +
              "onClick=\"javascript:reiniciarCampania('" + data.linkReini + "');\">" +
              '<i class="fa fa-play"></i>' +
              "</button>&nbsp;";
          }

          if (boolPermisoEditar && data.linkEditar !== "") {

            strDatoRetorna += " <button type='button' class='btn btn-info btn-sm' ";
            strDatoRetorna += " title='Editar' ";
            strDatoRetorna += " onclick='mostrarModalEdit(" + JSON.stringify(meta) + ")'> ";
            strDatoRetorna += " <i class='fa fa-edit'></i> ";
            strDatoRetorna += " </button>&nbsp; ";

          }

          if (boolPermisoDelete && data.linkEliminar !== "") {
            strDatoRetorna +=
              '<button type="button" class="btn btn-danger btn-sm" ' +
              ' title="Eliminar" ' +
              " onClick=\"javascript:deleteCampania('" + data.linkEliminar + "');\">" +
              '<i class="fa fa-eraser"></i>' +
              "</button>&nbsp;";
          }

          return strDatoRetorna;
        },
      },
    ],
    columnDefs: [
      {
        targets: 0,
        searchable: false,
        orderable: false,
        render: function (data, row, meta) {
          var dataCheck = [];
          dataCheck = {
            id: data,
            status: meta.strEstado
          };
          return (
            "<input id='idcheckNoti' class='delete_checkbox' type='checkbox' name='id[]' value='" +
            JSON.stringify(dataCheck) + "' >"
          );
        },
      },
      {
        targets:3, 
        type:'date-euro'
      }
    ],
  });
  $("#objListado-select-all").on("click", function () {
    var rows = objListado.rows({ search: "applied" }).nodes();
    $('input[type="checkbox"]', rows).prop("checked", this.checked);
  });
  if (boolPermisoCrear) {
    $("#tabla_filter").append(
      '&nbsp;<button type="button" data-toggle="modal" onclick="mostrarModalCrear()" ' +
      'class="btn btn-primary btn-sm" title="Nueva Notificación" <i class="fa fa-files-o"></i> NUEVO </button>'
    );
  }
  if (boolPermisoClonar) {
    $("#tabla_filter").append(
      '&nbsp;<button type="button" id="btn_clon"' +
      'onClick="javascript:clonarCampania();"' +
      'class="btn btn-primary btn-sm" title="Clonar" <i class="fa fa-files-o"></i> CLONAR </button>'
    );
  }
  if (boolPermisoDelete) {
    $("#tabla_filter").append(
      '&nbsp;<button type="button" ' +
      'onClick="javascript:eliminarCampanias();"' +
      'class="btn btn-primary btn-sm" title="Eliminar" <i class="fa fa-files-o"></i> ELIMINAR </button>'
    );
  }
  $("#tabla tbody").on("change", 'input[type="checkbox"]', function () {
    var ischecked = $(this).is(':checked');
    var indexRemove;
    var s = $(this).val();
    var x = JSON.parse(s);
    if (ischecked) {
      ids_array.push(s);
    } else {
      if (ids_array.length > 0) {
        ids_array.forEach((element, index) => {
          itemArray = JSON.parse(element);
          if (x.id === itemArray.id) {
           
            indexRemove = index;
          }
        });
        ids_array = ids_array.filter((element, index) => index !== indexRemove);
      }
    }
    if (!this.checked) {
      var el = $("#objListado-select-all").get(0);
      if (el && el.checked && "indeterminate" in el) {
        el.indeterminate = true;
      }
    }
  });

  $("#buscar").click(function () {
    $("#tabla").DataTable().ajax.reload();
  });

  $("form").keypress(function (e) {
    if (e === 13) {
      return false;
    }
  });
  });
/**
 * Documentación para la función 'mostrarModalDetalle'.
 *
 * Función encargada de mostrar el detalle de la campaña
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 30-12-2022
 *
 */
function mostrarModalDetalle(data) {
  var arrayParametros = {
    'data': data
  }
  $.ajax({
    data: arrayParametros,
    url: data.strAcciones.linkVer,
    type: "post",
    dataType: "html",
    success: function (response) {
      $("#modalDetalle .modal-body").html(response);
      $("#modalDetalle").modal({ show: true });
    },
    beforeSend: function () {
      Ext.get(document.body).mask("Cargando Información.");
    },
    complete: function () {
      Ext.get(document.body).unmask();
    },
    error: function () {
      $("#modalMensajes .modal-body").html(
        "<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>"
      );
      $("#modalMensajes").modal("show");
    },
  });
}
/**
 * Documentación para la función 'mostrarModalCrear'.
 *
 * Función encargada de mostrar modal para crea una campaña
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 12-01-2023
 *
 */
function mostrarModalCrear() {
  $.ajax({
    url:url_new,
    type: "post",
    dataType: "html",
    success: function (response) {
      $("#modalCrear .modal-body").html(response);
      $("#modalCrear").modal({ show: true });
    }, 
    beforeSend: function () {
      Ext.get(document.body).mask("Iniciando...");
    },
    complete: function () {
      Ext.get(document.body).unmask();
    },
    error: function () {
      $("#modalMensajes .modal-body").html(
        "<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>"
      );
      $("#modalMensajes").modal("show");
    },
  });
}
/**
 * Documentación para la función 'mostrarModalEdit'.
 *
 * Función encargada de mostrar el detalle de la campaña.
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 30-12-2022
 *
 */
function mostrarModalEdit(data) {
  var arrayParametros = {
    'data': data
  }
  $.ajax({
    data: arrayParametros,
    url: data.strAcciones.linkEditar,
    type: 'post',
    dataType: "html",
    success: function (response) {
      $('#modalEdit .modal-body').html(response);
      $('#modalEdit').modal({ show: true });
    },
    beforeSend: function () {
      Ext.get(document.body).mask('Cargando Información.');
    },
    complete: function () {
      Ext.get(document.body).unmask();
    },
    error: function () {
      $('#modalMensajes .modal-body').html('<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>');
      $('#modalMensajes').modal('show');
    }
  });
}
/**
 * Documentación para la función 'detenerCampania'.
 *
 * Función encargada de pasar a estado detenido una campaña.
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 30-12-2022
 *
 */
function detenerCampania(url_accion) {
  Ext.Msg.confirm('Alerta', '¿ Está seguro de Detener la notificación ?', function (btn) {
    if (btn == 'yes') {
      $.ajax({
        url: url_accion,
        type: "get",
        dataType: "html",
        success: function (response) {
          if (response) {

            $("#modalMensajes .modal-body").html(
              "<p>" + response + "</p>"
            );
            $("#modalMensajes").modal("show");
            $("#tabla").DataTable().ajax.reload();

          }
        },
        beforeSend: function () {
          Ext.get(document.body).mask("Procesando");
        },
        complete: function () {
          Ext.get(document.body).unmask();
        },
        error: function () {
          $("#modalMensajes .modal-body").html(
            "<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>"
          );
          $("#modalMensajes").modal("show");
        },
      });
    }
  });
}

/**
 * Documentación para la función 'detenerCampania'.
 *
 * Función encargada de pasar a estado detenido una campaña.
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 30-12-2022
 *
 */
function reiniciarCampania(url_accion) {
  Ext.Msg.confirm('Alerta', '¿ Está seguro de Reiniciar la notificación ?', function (btn) {
    if (btn == 'yes') {
      $.ajax({
        url: url_accion,
        type: "get",
        dataType: "html",
        success: function (response) {
          if (response) {

            $("#modalMensajes .modal-body").html(
              "<p>" + response + "</p>"
            );
            $("#modalMensajes").modal("show");
            $("#tabla").DataTable().ajax.reload();

          }
        },
        beforeSend: function () {
          Ext.get(document.body).mask("Procesando");
        },
        complete: function () {
          Ext.get(document.body).unmask();
        },
        error: function () {
          $("#modalMensajes .modal-body").html(
            "<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>"
          );
          $("#modalMensajes").modal("show");
        },
      });
    }
  });
}
/**
 * Documentación para la función 'eliminarCampanias'.
 *
 * Función encargada de pasar a estado Eliminada una campaña.
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 30-12-2022
 *
 */
  function eliminarCampanias() {
  var idsCampaing = [];
  var boolValidacion = false;
  var array_data;
    if (ids_array.length > 0) {
      ids_array.forEach(element => {
        array_data = JSON.parse(element);

        if (array_data.status == 'Programada') {
          idsCampaing.push(array_data.id);
        } else {
          $("#modalMensajes .modal-body").html(
            " Una o más notificaciones seleccionadas se encuentran en estado " + array_data.status +
            " sólo se permite eliminar notificaciones " + "Programadas" + ", Por favor, vuelva a intentar. "
          );
          $("#modalMensajes").modal({ show: true });
          boolValidacion = true;
          return false;
        }
      });

        if (!boolValidacion && idsCampaing.length > 0) {
          var arrayParametros = { "arrayIdsCampanias": idsCampaing };
          $.ajax({
            data: arrayParametros,
            url: url_delete,
            type: "post",
            success: function (response) {
              if (response) {
                $("#strNombreProyecto_crear").val("");
                $("#strCuenta_crear").val("");
                $("#strResponsable_crear").select2({
                  placeholder: "Seleccione",
                  multiple: false,
                  width: "100%",
                });
                $("#strResponsable_crear").empty();
                getPantallasApp();
                $("#strFechaInicioCrear").val("");
                $("#strFechaFinCrear").val("");
                $("#tabla").DataTable().ajax.reload();
                $("#modalMensajes .modal-body").html(response);
                $("#modalMensajes").modal({ show: true });
                ids_array = [];
              }
            },
            beforeSend: function () {
              Ext.get(document.body).mask("Cargando Información.");
            },
            complete: function () {
              Ext.get(document.body).unmask();
            },
            failure: function (response) {
              $("#modalMensajes .modal-body").html(
                "No se pudo eliminar la notificación por el siguiente error: " + response
              );
              $("#modalMensajes").modal({ show: true });
            },
          });
      } 
    }else {
        $("#modalMensajes .modal-body").html(
          "Seleccione registro para eliminar"
        );
        $("#modalMensajes").modal({ show: true });
    }
  }
/*
 * Documentación para la función 'getPantallasApp'.
 *
 * Función que devuelve lista de pantallas a redireccionar la notificacion.
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 11-05-2021
 *
 */
function getPantallasApp() {
  $.ajax({
    url: url_pantallas,
    method: "post",
    success: function (data) {
      $(".spinner_sect_responsables").hide();
      $("#strPantallas_crear").append("<option value=Seleccione></option>");
      $.each(data.arrayPantallas, function (id, registro) {
        $("#strPantallas_crear").append(
          "<option value=" +
          registro.idPantalla +
          ">" +
          registro.descripcion +
          " </option>"
        );
      });
    },
    error: function () {
      $("#modalMensajes .modal-body").html(
        "No se pudieron cargar listado de pantallas. Por favor comuníquese con el departamento de Sistemas."
      );
      $("#modalMensajes").modal({ show: true });
    },
  });
}
/**
 * Documentación para la función 'deleteCampania'.
 *
 * Función encargada de pasar a estado eliminada una campaña.
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 04-01-2023
 *
 */
function deleteCampania(url_accion) {
  Ext.Msg.confirm('Alerta', '¿ Esta seguro de Eliminar Notificación ?', function (btn) {
    if (btn == 'yes') {
      $.ajax({
        url: url_accion,
        type: "get",
        dataType: "html",
        success: function (response) {
          if (response) {
            $("#modalMensajes .modal-body").html(
              "<p>" + response + "</p>"
            );
            $("#modalMensajes").modal("show");
            $("#tabla").DataTable().ajax.reload();
          }
        },
        beforeSend: function () {
          Ext.get(document.body).mask("Procesando");
        },
        complete: function () {
          Ext.get(document.body).unmask();
        },
        error: function () {
          $("#modalMensajes .modal-body").html(
            "<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>"
          );
          $("#modalMensajes").modal("show");
        },
      });
    }
  });
}
/**
 * Documentación para la función 'clonarCampania'.
 *
 * Función encargada de pasar a estado Eliminada una campaña.
 *
 * @author Andrea Orellana <adorellana@telconet.ec>
 * @version 1.0 03-01-2023
 *
 */
function clonarCampania() {
  var idsCampaing = [];
  var boolValidacion = false;
  var array_data;
  if (ids_array.length > 0) {
    ids_array.forEach(element => {
      array_data = JSON.parse(element);
      if (array_data.status != 'Eliminada') {
        idsCampaing.push(array_data.id);
      } else {
        $("#modalMensajes .modal-body").html(
          "Una o más notificaciones seleccionadas se encuentran en estado " + array_data.status +
          " no se permite clonar notificaciones " + array_data.status + ", Por favor, vuelva a intentar. "
        );
        $("#modalMensajes").modal({ show: true });
        boolValidacion = true;
        return false;
      }
    });

    if (!boolValidacion && idsCampaing.length > 0) {
      var arrayParametros = { "arrayIdsCampanias": idsCampaing };
      $.ajax({
        data: arrayParametros,
        url: url_clonar,
        type: "post",
        success: function (response) {
          if (response) {
            $("#strNombreProyecto_crear").val("");
            $("#strCuenta_crear").val("");
            $("#strResponsable_crear").select2({
              placeholder: "Seleccione",
              multiple: false,
              width: "100%",
            });
            $("#strResponsable_crear").empty();
            getPantallasApp();
            $("#strFechaInicioCrear").val("");
            $("#strFechaFinCrear").val("");
            $("#tabla").DataTable().ajax.reload();
            $("#modalMensajes .modal-body").html(response);
            $("#modalMensajes").modal({ show: true });
            ids_array = [];
          }
        },
        beforeSend: function () {
          Ext.get(document.body).mask("Cargando Información.");
        },
        complete: function () {
          Ext.get(document.body).unmask();
        },
        failure: function (response) {
          $("#modalMensajes .modal-body").html(
            "No se pudo clonar la notificación por el siguiente error: " + response
          );
          $("#modalMensajes").modal({ show: true });
        },
      });
    }

  } else {
    $("#modalMensajes .modal-body").html(
      "Seleccione registro para clonar"
    );
    $("#modalMensajes").modal({ show: true });
  }

}

function isValidURL(string) {
  var res = string.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
  return (res !== null)
};

function limpiarData() {
  $("#tituloNoti").val("");
  $("#textoNoti").val("");
  $("#strPantallas_crear").select2({
      placeholder: "Seleccione",
      multiple: false,
      width: "100%",
  });
  $("#strPantallas_crear").empty();
  $("#imgNoti").val("");
  $("#NombreNoti").val("");
  $("#fechaIniVigencia").val("");
  $("#hora_ini").val("");
  $( "#checkEnvio" ).prop( "checked", false );
  $("#file-selector").val(null);
  elementDiv.style.display = 'none';
  $("#titulo_view").val("");
  $("#texto_view").val("");
  $("#img_view").val("");
}