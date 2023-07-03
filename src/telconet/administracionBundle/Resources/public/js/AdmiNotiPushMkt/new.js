var ids_array = new Array();
var fileList;
var fileB64;
var isCheckedVal;
$(document).ready(function () {
  var current_fs, next_fs, previous_fs; //fieldsets
  var opacity;
  var boolError = false;
  var fechaActual;
 
  $("#next1").click(function () {
    imgNoti = $("#imgNoti").val();
    mensaje = ", no se encuentra configurado correctamente, por favor revisar";
    pantallaRedirige = $("#strPantallas_crear").val();
    if ($("#NombreNoti").val().trim().length==0) {
      boolError = true;
      $("#modalMensajes .modal-body").html(
        $("#NombreNoti").attr("name") + mensaje
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    }else if ($("#textoNoti").val().trim().length==0) {
      boolError = true;
      $("#modalMensajes .modal-body").html(
        $("#textoNoti").attr("name") + mensaje
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    }else if ($("#tituloNoti").val().trim().length==0) {
      boolError = true;
      $("#modalMensajes .modal-body").html(
        $("#tituloNoti").attr("name") + mensaje 
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    }else if (pantallaRedirige == 'Seleccione' || pantallaRedirige == '') {
      boolError = true;
      $("#modalMensajes .modal-body").html(
        $("#strPantallas_crear").attr("name") + mensaje
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    } else if (imgNoti != '') {
      if (!isValidURL(imgNoti)) {
        boolError = true;
        $("#modalMensajes .modal-body").html(
          $("#imgNoti").attr("name") + mensaje
        )
        $("#modalMensajes").modal({ show: true });
        return false;
      } else {
        boolError = false;
      }
    }
    else
      boolError = false;
  });

  $("#next2").click(function () {
    boolError = true;
    fecha_Ini_noti = $("#fechaIniVigencia").val();
    hora_Ini_noti = $("#hora_ini").val();

    if (fecha_Ini_noti == '') {
      $("#modalMensajes .modal-body").html(
        $("#fechaIniVigencia").attr("name") + ", no se encuentra configurado correctamente, por favor revisar"
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    } else if (hora_Ini_noti == '') {
      $("#modalMensajes .modal-body").html(
        $("#hora_ini").attr("name") + ", no se encuentra configurado correctamente, por favor revisar"
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    } else
      boolError = false;
  });

  $(".next").click(function () {

    if (!boolError) {
      current_fs = $(this).parent();
      next_fs = $(this).parent().next();
      
      //Add Class Active
      $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

      //show the next fieldset
      next_fs.show();
      //hide the current fieldset with style
      current_fs.animate(
        { opacity: 0 },
        {
          step: function (now) {
            // for making fielset appear animation
            opacity = 1 - now;

            current_fs.css({
              display: "none",
              position: "relative",
            });
            next_fs.css({ opacity: opacity });
          },
          duration: 600,
        }
      );
    }
  });

  $(".previous").click(function () {
    current_fs = $(this).parent();
    previous_fs = $(this).parent().prev();
   
    //Remove class active
    $("#progressbar li")
      .eq($("fieldset").index(current_fs))
      .removeClass("active");

    //show the previous fieldset
    previous_fs.show();

    //hide the current fieldset with style
    current_fs.animate(
      { opacity: 0 },
      {
        step: function (now) {
          // for making fielset appear animation
          opacity = 1 - now;

          current_fs.css({
            display: "none",
            position: "relative",
          });
          previous_fs.css({ opacity: opacity });
        },
        duration: 600,
      }
    );
  });

  $("#strPantallas_crear").select2({
    placeholder: "Seleccione",
    multiple: false,
    width: "100%"
  });

  getPantallasApp();
  
  elementDiv = document.getElementById("divCargaArchivoNew");
  elementDiv.style.display = 'none';
  isCheckedVal = 'N';
  check = document.getElementById("checkEnvio");
  check.checked = false;

   // Formato de hora
  $('#fechaIniHora').datetimepicker({
    format: 'HH:mm',
    icons: {
      time: 'fa fa-clock-o',
      date: 'fa fa-calendar',
      up: 'fa fa-chevron-up',
      down: 'fa fa-chevron-down',
      previous: 'fa fa-chevron-left',
      next: 'fa fa-chevron-right',
      today: 'fa fa-crosshairs',
      clear: 'fa fa-trash-o',
      close: 'fa fa-times'
    }
  });
  ///setMaxTime();
  $('#datetimepickerFechaIniVigencia').datetimepicker({
    format: 'DD/MM/YYYY',
    minDate: new Date(),
    maxDate: sumarDias(new Date(), 120),
    useCurrent: false,
    icons: {
      time: 'fa fa-clock-o',
      date: 'fa fa-calendar',
      up: 'fa fa-chevron-up',
      down: 'fa fa-chevron-down',
      previous: 'fa fa-chevron-left',
      next: 'fa fa-chevron-right',
      today: 'fa fa-crosshairs',
      clear: 'fa fa-trash-o',
      close: 'fa fa-times'
    }
  });

  $(document).on('click', '.button-addon1', function () {
    fechaActual = $.datepicker.formatDate('dd/mm/yy', new Date());
    $('#datetimepickerFechaIniVigencia').data("DateTimePicker").minDate(fechaActual);
    
  });

  function sumarDias(fecha, dias) {
    fecha.setDate(fecha.getDate() + dias);
    return fecha;
  }

  $('#fechaIniVigencia').blur(function () {
    var dt = new Date();
    if ($('#fechaIniVigencia').val() === fechaActual) {
      var time;
      var hora, min;

      if(dt.getMinutes() < 10 )
        min =  "0" + dt.getMinutes();
      else
        min = dt.getMinutes();
      if( dt.getHours() < 10 )
        hora = "0" + dt.getHours();
      else 
        hora = dt.getHours();
      
      time = hora + ":" + min;
      
      $('#hora_ini').val(time.toString());
      /*VALIDACION  MIN Y MAX HORAS*/
      $('#fechaIniHora').data("DateTimePicker").minDate(dt);
     
      let today = new Date()
      today.setHours(23,59,59)
      
      $('#fechaIniHora').data("DateTimePicker").maxDate(today);
    } else {
      $('#hora_ini').val("00:00");
    /*VALIDACION  MIN Y MAX HORAS*/
      const [day, month, year] = $('#fechaIniVigencia').val().split('/')
      let date = new Date(
        +year,
        month - 1,
        +day
      );
     
      date.setHours(23,59,59)  
      $('#fechaIniHora').data("DateTimePicker").maxDate(date);
      date.setHours(0,0,0,0)
      $('#fechaIniHora').data("DateTimePicker").minDate(date);
    }
  });

  const fileSelector = document.getElementById("file-selector");
  fileSelector.addEventListener('change', (e) => {
    // Get a reference to the file
    fileList = e.target.files[0];
    var mimeType = fileList.type;
    if(mimeType.match(/csv\/*/) == null)
    {
      Ext.Msg.alert('Error ','Error: Solo se admiten archivos .csv delimitado por ,') ; 	
      e.target.value = ''
      fileList = null
    }
   
    if(fileList != null)
    {
      // Encode the file using the FileReader API
      const reader = new FileReader();
      reader.onloadend = () => {
        // Use a regex to remove data url part
        const base64String = reader.result
          .replace('data:', '')
          .replace(/^.+,/, '');
        fileB64 = base64String;
      };
      reader.readAsDataURL(fileList);
    } 
  });

  $("#checkEnvio").click(function () {
    if (check.checked) {
      elementDiv.style.display = 'block';
      isCheckedVal = "S";
    } else {
      elementDiv.style.display = 'none';
      isCheckedVal = "N";
    }
  });

  $("#btnSave").click(function () {
    srtEstadoNoti = "Pendiente";
    Ext.Msg.confirm('Alerta','¿ Está seguro de guardar la información ?', function(btn){
      if(btn=='yes'){
        crearNotificacion(srtEstadoNoti);
      }
    });
  });

  $("#btnPublicar").click(function () {
    srtEstadoNoti = "Programada";
    Ext.Msg.confirm('Alerta', '¿ Está seguro de publicar la información ?', function (btn) {
      if (btn == 'yes') {
        crearNotificacion(srtEstadoNoti);
      }
    });
  });

  $("#tituloNoti").keyup(function () {
    tituloNot = $("#tituloNoti").val();
    $("#titulo_view").text(tituloNot);
  });

  $("#textoNoti").keyup(function () {
    textoNoti = $("#textoNoti").val();
    $("#texto_view").text(textoNoti);
  });

  $("#imgNoti").keyup(function () {
    imgval = $("#imgNoti").val();
    $("#img_view").attr("src", imgval);
  });

  select = document.getElementById("valNum");
  for (i = 1; i <= 72; i++) {
    option = document.createElement("option");
    option.value = i;
    option.text = i;
    if (i == 72)
      option.selected = i;

    select.appendChild(option);
  }

  $("#btnCerrarModalCrar").click(function () {
    limpiarData();
    $("#progressbar li")
    .eq($("fieldset").index(0))
    .removeClass("active");
    $("#progressbar li")
    .eq($("fieldset").index(1))
    .removeClass("active");
    current_fs = $(this).parent();
    previous_fs = $(this).parent().prev();
    previous_fs.show();
  });

  limpiarData();

});

  /**
   * Documentación para la función 'crearNotificacion'.
   *
   * Función encargada de crear publicar la notificación
   *
   * @author Andrea Orellana <adorellana@telconet.ec>
   * @version 1.0 10-01-2023
   *
   */
  function crearNotificacion(strEstado) {
    var nombreArchivo;
    var pantallaRedirige;
    if(isCheckedVal == 'S' && fileList == null ){
      $("#modalMensajes .modal-body").html(
        "Envío segmentado fue marcado, por favor seleccione archivo."
      );
      $("#modalMensajes").modal("show");
      return;
    }
    if(fileList != null ){
      nombreArchivo = fileList.name;
    }
    if($("#strPantallas_crear").val() != 'Seleccione'){
      pantallaRedirige = $("#strPantallas_crear").val();
    }
   
    if ((fileB64 == null || fileB64 == '') && nombreArchivo != null){
      $("#modalMensajes .modal-body").html(
        "El archivo ingresado está incorrecto, se ha encontrado errores."
      );
      $("#modalMensajes").modal("show");
      return;
    }
     
    var arrayParametros = {
                            "strTituloNoti": $("#tituloNoti").val(),
                            "strTextoNoti": $("#textoNoti").val(),
                            "strImgNoti": $("#imgNoti").val(),
                            "strNombreNoti": $("#NombreNoti").val(),
                            "intHraVencimiento": $("#valNum").val(),
                            "strPantallaSelec": pantallaRedirige,
                            "strFechaInicio": $("#fechaIniVigencia").val(),
                            "strHoraInicio": $("#hora_ini").val(),
                            "esSegmentado": isCheckedVal,
                            "strArchivoCsvB64": fileB64,
                            "strNombreArchivo": nombreArchivo,
                            "strEstadoNoti": strEstado
    };
    $.ajax({
      data: arrayParametros,
      url: url_save,
      type: "post",
      beforeSend: function () {
        Ext.get(document.body).mask("Cargando Información.");
      },
      complete: function () {
        Ext.get(document.body).unmask();
      },
      success: function (response) {
        if (response && response.strStatus == 'OK') {
          $("#tituloNoti").val("");
          $("#titulo_view").val("");
          $("#texto_view").val("");
          $("#img_view").val("");
          $("#textoNoti").val("");
          $("#strPantallas_crear").select2({
            placeholder: "Seleccione",
            multiple: false,
            width: "100%",
          });
          $("#strPantallas_crear").empty();
          getPantallasApp();
          $("#imgNoti").val("");
          $("#NombreNoti").val("");
          $("#fechaIniVigencia").val("");
          $("#hora_ini").val("");
          $("#file-selector").empty();
          $("#tabla").DataTable().ajax.reload();
          $("#modalMensajes .modal-body").html(response.strResponseMsj);
          $("#modalMensajes").modal({ show: true });
          $("#modalCrear").modal("hide");
        }else{
          $("#modalMensajes .modal-body").html(response.strResponseMsj);
          $("#modalMensajes").modal({ show: true });
        }
      },
      failure: function (response) {
        $("#modalMensajes .modal-body").html(
          "No se pudo crear la solicitud por el siguiente error: " + response.strResponseMsj
        );
        $("#modalMensajes").modal({ show: true });
      }
    });
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
