
$(document).ready(function () {
  var fileListCsvEdit;
  var fileB64;
  var current_fs, next_fs, previous_fs; //fieldsets
  var opacity;
  var boolError = false;
  var fechaActual;
  var isCheckedVal;
  var dtIni;

  /*INICIALIZACION DE DATOS*/
  getPantallasAppEdit();
  elementDiv = document.getElementById("divCargaArchivo");
  checkedit = document.getElementById("checkEnvioEdit");
  divNombreArchivo = document.getElementById("divExisteArchivo");
 

  if (envioSegmCheck == "S" && nombreArchivo) {
    elementDiv.style.display = 'block';
    checkedit.checked = true;
    isCheckedVal = "S";
  } else {
    elementDiv.style.display = 'none';
    checkedit.checked = false;
    isCheckedVal = "N";
  }

  $("#textoNoti_edit").text(textoCampania);
    
  if (fechaIni != '') {
    var hoy = $.datepicker.formatDate('dd/mm/yy', new Date());
    const [day, month, year] = fechaIni.split('/');
    dtIni = new Date(+year, month - 1, +day)
    if(fechaIni == hoy){
      dtIni = new Date();
    }
  }else{
    dtIni = null;
  }
  
  $('#datetimepickerFechaIniVigenciaEdit').datetimepicker({
    minDate: dtIni,
    date: dtIni,
    format: 'DD/MM/YYYY',
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
    $('#datetimepickerFechaIniVigenciaEdit').data("DateTimePicker").minDate(fechaActual);
  });

  $("#next1").click(function () {
    imgNoti = $("#imgNoti_edit").val();
    mensaje = ", no se encuentra configurado correctamente, por favor revisar";
    pantallaRedirige = $("#strPantallas_editar").val();
    if ($("#NombreNoti_edit").val().trim().length==0) {
      boolError = true;
      $("#modalMensajes .modal-body").html(
        $("#NombreNoti_edit").attr("name") + mensaje
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    } else if ($("#textoNoti_edit").val().trim().length==0) {
      boolError = true;
      $("#modalMensajes .modal-body").html(
        $("#textoNoti_edit").attr("name") + mensaje
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    }else if ($("#tituloNoti_edit").val().trim().length==0) {
      boolError = true;
      $("#modalMensajes .modal-body").html(
        $("#tituloNoti_edit").attr("name") + mensaje 
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    }else if (pantallaRedirige == 'Seleccione' || pantallaRedirige == '') {
      boolError = true;
      $("#modalMensajes .modal-body").html(
        $("#strPantallas_editar").attr("name") + mensaje
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    }else if (imgNoti != '') {
      if (!isValidURL(imgNoti)) {
        boolError = true;
        $("#modalMensajes .modal-body").html(
          $("#imgNoti_edit").attr("name") + mensaje
        )
        $("#modalMensajes").modal({ show: true });
        return false;
      } else {
        boolError = false;
      }
    }  else
      boolError = false;
  });

  $("#next2").click(function () {
    boolError = true;
    fecha_Ini_noti = $("#fechaIniVigenciaEdit").val();
    hora_Ini_noti = $("#hora_ini_edit").val();
    if (fecha_Ini_noti == '') {
      $("#modalMensajes .modal-body").html(
        $("#fechaIniVigenciaEdit").attr("name") + ", no se encuentra configurado correctamente, por favor revisar"
      )
      $("#modalMensajes").modal({ show: true });
      return false;
    } else if (hora_Ini_noti == '') {
      $("#modalMensajes .modal-body").html(
        $("#hora_ini_edit").attr("name") + ", no se encuentra configurado correctamente, por favor revisar"
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

  $('#strPantallas_editar').select2({ placeholder: "Seleccionar", multiple: false, width: "100%" });

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

  function sumarDias(fecha, dias) {
    fecha.setDate(fecha.getDate() + dias);
    return fecha;
  }

  $('#fechaIniVigenciaEdit').blur(function () {
    var dt = new Date();
    if ($('#fechaIniVigenciaEdit').val() === fechaActual) {
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
    $('#hora_ini_edit').val(time.toString());

     /*VALIDACION  MIN Y MAX HORAS*/
     $('#fechaIniHora').data("DateTimePicker").minDate(dt);
    
     let today = new Date()
     today.setHours(23,59,59)
     $('#fechaIniHora').data("DateTimePicker").maxDate(today);
     
    } else {
      $('#hora_ini_edit').val("00:00");

      /*VALIDACION  MIN Y MAX HORAS*/
      const [day, month, year] = $('#fechaIniVigenciaEdit').val().split('/')
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
    fileListCsvEdit = e.target.files[0];
    var mimeType = fileListCsvEdit.type;
    if(mimeType.match(/csv\/*/) == null)
    {
      Ext.Msg.alert('Error ','Error: Solo se admiten archivos .csv delimitado por ,') ; 	
      e.target.value = ''
      fileListCsvEdit = null
    }
    if(fileListCsvEdit != null)
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
      reader.readAsDataURL(fileListCsvEdit);
    }
  });

  $("#tituloNoti_edit").keyup(function () {
    tituloNot = $("#tituloNoti_edit").val();
    $("#titulo_view_edit").text(tituloNot);
  });

  $("#textoNoti_edit").keyup(function () {
    textoNoti = $("#textoNoti_edit").val();
    $("#texto_view_edit").text(textoNoti);
  });

  $("#imgNoti_edit").keyup(function () {
    imgval = $("#imgNoti_edit").val();
    $("#img_view").attr("src", imgval);
  });

  select = document.getElementById("valNum_edit");
  for (i = 1; i <= 72; i++) {
    option = document.createElement("option");
    option.value = i;
    option.text = i;
    if (i == hraVencimiento)
      option.setAttribute("selected", true);
    select.appendChild(option);
  }

  $("#checkEnvioEdit").click(function () {
    if (checkedit.checked) {
      elementDiv.style.display = 'block';
      isCheckedVal = "S";
    } else {
      elementDiv.style.display = 'none';
      isCheckedVal = "N";
    }
  });

  $("#btnUpdate").click(function () {
    srtEstadoNoti = "Pendiente";
    Ext.Msg.confirm('Alerta', '¿ Está seguro de actualizar la información ?', function (btn) {
      if (btn == 'yes') {
        updateCampania(srtEstadoNoti);
      }
    });
  });

  $("#btnPublicar").click(function () {
    srtEstadoNoti = "Programada";
    Ext.Msg.confirm('Alerta', '¿ Está seguro de publicar la información ?', function (btn) {
      if (btn == 'yes') {
        updateCampania(srtEstadoNoti);
      }
    });
  });

  /**
* Documentación para la función 'ActualizaProyecto'.
*
* Función encargada de actualizar una campaña.
*
* @author Andrea Orellana <adorellana@telconet.ec>
* @version 1.0 13-01-2023
*
*/
  function updateCampania(strEstado) {
    var nameArchivo;
    var pantallaRedirige;
    
    if((isCheckedVal == 'S' && fileListCsvEdit == null) && nombreArchivo == ''){
      $("#modalMensajes .modal-body").html(
        "Envío segmentado fue marcado, por favor seleccione archivo."
      );
      $("#modalMensajes").modal("show");
      return;
    }

    if (fileListCsvEdit != null)
      nameArchivo = fileListCsvEdit.name;
    if ($("#strPantallas_editar").val() != 'Seleccione') {
      pantallaRedirige = $("#strPantallas_editar").val();
    }
    if ((fileB64 == null || fileB64 == '') && nameArchivo != null){
      $("#modalMensajes .modal-body").html(
        "El archivo ingresado está incorrecto, se ha encontrado errores."
      );
      $("#modalMensajes").modal("show");
      return;
    }
     
    var arrayParametros = {
      strTituloNoti: $("#tituloNoti_edit").val(),
      strTextoNoti: $("#textoNoti_edit").val(),
      strImgNoti: $("#imgNoti_edit").val(),
      strNombreNoti: $("#NombreNoti_edit").val(),
      intHraVencimiento: $("#valNum_edit").val(),
      strPantallaSelec: pantallaRedirige,
      strFechaInicio: $("#fechaIniVigenciaEdit").val(),
      strHoraInicio: $("#hora_ini_edit").val(),
      esSegmentado: isCheckedVal,
      strArchivoCsvB64: fileB64,
      strNombreArchivo: nameArchivo,
      strEstadoNoti: strEstado,
      intIdCampania: idCampania
    };

    $.ajax({
      data: arrayParametros,
      url: url_update,
      type: 'post',
      success: function (response) {
        if (response && response.strStatus == 'OK') {
          $("#tituloNoti_edit").val("");
          $("#textoNoti_edit").val("");
          $("#titulo_view_edit").val("");
          $("#texto_view_edit").val("");
          $("#img_view").val("");
          $("#strPantallas_edit").select2({
            placeholder: "Seleccionar",
            multiple: false,
            width: "100%",
          });
          $("#strPantallas_edit").empty();
          getPantallasAppEdit();
          $("#imgNoti_edit").val("");
          $("#NombreNoti_edit").val("");
          $("#fechaIniVigenciaEdit").val("");
          $("#hora_ini_edit").val("");
          $("#file-selector").empty();
          $("#tabla").DataTable().ajax.reload();
          $("#modalMensajes .modal-body").html(response.strResponseMsj);
          $("#modalMensajes").modal({ show: true });
          $("#modalEdit").modal("hide");
        }else{
          $("#modalMensajes .modal-body").html(response.strResponseMsj);
          $("#modalMensajes").modal({ show: true });
        }
      },
      beforeSend: function () {
        Ext.get(document.body).mask('Cargando Información.');
      },
      complete: function () {
        Ext.get(document.body).unmask();
      },
      failure: function (response) {
        $('#modalMensajes .modal-body').html('No se pudo crear la solicitud por el siguiente error: ' + response.strResponseMsj);
        $('#modalMensajes').modal({ show: true });
      }
    });
  }
});

function getPantallasAppEdit() {
  $.ajax({
    url: url_pantallas,
    method: "post",
    success: function (data) {
      $(".spinner_sect_responsables").hide();
      $("#strPantallas_editar").append("<option value=Seleccione></option>");
      $.each(data.arrayPantallas, function (id, registro) {
        if (registro.idPantalla == idPantallaSelec) {
          $("#strPantallas_editar").append(
            '<option value="' +
            registro.idPantalla + '"' + 'selected' +
            '>' +
            registro.descripcion +
            ' </option>'
          );

        } else {
          $("#strPantallas_editar").append(
            "<option value=" +
            registro.idPantalla +
            ">" +
            registro.descripcion +
            " </option>"
          );
        }

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

