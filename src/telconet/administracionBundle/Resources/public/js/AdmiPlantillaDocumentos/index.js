
$(document).ready(function () {
        
    /**
     * Obtiene el listado de plantillas.
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     */
    $('#tabla_lista_plantillas').DataTable({
        "ajax": {
            "url": url_grid_plantillas,
            "type": "POST"
        },
        "language": {
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "Cargando datos...",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "loadingRecords": "Cargando datos..."
        },
        "columns": [
            {"data": "strCodigoPlantilla"},
            {"data": "strdescripcion"},
            {"data": "strFechaCreacion"},
            {"data": "strUsrCreacion"},
            {"data": "strAcciones",
                "render": function (data){
                    var strDatoRetorna = '';

                    if (data.linkVer !== '') 
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" ' + ' title="Ver Plantilla" ' +                            
                            'onClick="javascript:mostrarModalDetalle(\'' + data.linkVer + '\');">' + '<i class="fa fa-search"></i>' +
                            '</button>&nbsp;';
                    }
                    if (data.linkEditar !== '')
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-warning btn-sm" ' + ' title="Editar Plantilla" ' +                            
                            'onClick="javascript:mostrarModalEditar(\'' + data.linkEditar + '\');">' + '<i class="fa fa-pencil"></i>' + '</button>&nbsp;';

                    }
                    if (data.linkEliminar !== '')
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-danger btn-sm" ' + ' title="Eliminar Plantilla" ' +                            
                            'onClick="javascript:mostrarModalEliminar(\'' + data.linkEliminar + '\');">' + '<i class="fa fa-trash"></i>' + '</button>&nbsp;';
                    }

                    if (data.linkDescargar !== '')
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-success btn-sm" ' + ' title="Descargar Plantilla" ' +                            
                            'onClick="javascript:mostrarModalDescargar(\'' + data.linkDescargar + '\');">' + '<i class="fa fa-download"></i>' + '</button>&nbsp;';
                    }
                    return strDatoRetorna;          
                }
            }
        ]
    });


  

    $('form').keypress(function (e) {
        if (e === 13) {
            return false;
        }
    });

    $('input').keypress(function (e) {
        if (e.which === 13) {
            return false;
        }
    });
    setInterval(function(){ $('#tabla_lista_plantilla').DataTable().ajax.reload(); }, 300000);
});

    
/**
 * Muestra una ventana modal con el detalle de la Plantilla,
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 04-04-2019
 * @since 1.0
 */
function mostrarModalDetalle(url_accion) {
    $.ajax({
        url: url_accion,
        type: 'get',
        dataType: "html",
        success: function (response) {
            $('#modalDetalle .modal-body').html(response);
            $('#modalDetalle').modal({show: true});
        },
        error: function () {
            $('#modalDetalle .modal-body').html('<p>Ocurrió un error, por favor consulte con el Administrador.</p>');
            $('#modalDetalle').modal('show');
        }
    });
}

/**
 * Muestra una ventana modal para editar Plantilla,
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 05-12-2021
 * @since 1.0
 */
 function mostrarModalEditar(url_accion) {
    $.ajax({
        url: url_accion,
        type: 'get',
        dataType: "html",
        success: function (response) {
            $('#modalEditar .modal-body').html(response);
            $('#modalEditar').modal({show: true});
        },
        error: function () {
            $('#modalEditar .modal-body').html('<p>Ocurrió un error, por favor consulte con el Administrador.</p>');
            $('#modalEditar').modal('show');
        }
    });
}
/**
 * Muestra una ventana modal con mensaje de confirmacion para eliminar Plantilla,
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 05-12-2021
 * @since 1.0
 */
 function mostrarModalEliminar(url_accion) {
    $.ajax({
        url: url_accion,
        type: 'get',
        dataType: "html",
        success: function (response) {
            $('#modalEliminar .modal-body').html(response);
            $('#modalEliminar').modal({show: true});
        },
        error: function () {
            $('#modalEliminar .modal-body').html('<p>Ocurrió un error, por favor consulte con el Administrador.</p>');
            $('#modalEliminar').modal('show');
        }
    });
}
    /**
 * Muestra una ventana modal para crear Plantilla,
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 05-12-2021
 * @since 1.0
 */
 function mostrarModalCrear(url_accion) {
    $.ajax({
        url: url_accion,
        type: 'get',
        dataType: "html",
        success: function (response) {
            $('#modalCrear .modal-body').html(response);
            $('#modalCrear').modal({show: true});
        },
        error: function () {
            $('#modalCrear .modal-body').html('<p>Ocurrió un error, por favor consulte con el Administrador.</p>');
            $('#modalCrear').modal('show');
        }
    });
}

 /**
 * Muestra una ventana modal para descargar Plantilla,
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 05-12-2021
 * @since 1.0
 */
  function mostrarModalDescargar(url_accion) {
    $.ajax({
        url: url_accion,
        type: 'get',
        dataType: "html",
        success: function (response) {
            $('#modalDescargar .modal-body').html(response);
            $('#modalDescargar').modal({show: true});
        },
        error: function () {
            $('#modalDescargar .modal-body').html('<p>Ocurrió un error, por favor consulte con el Administrador.</p>');
            $('#modalDescargar').modal('show');
        }
    });
}
 /**
  * Realiza la llamada a la función Ajax que crea plantilla
  *    
  * @author Ivan Romero <icromero@telconet.ec>
  * @version 1.0 05-12-2021
  * @since 1.0
  */
  function crearPlantillaAccion(url_crear_plantilla_accion)
  {
      
     
      if ($("#codigoPlantillaCrear").val() === '' || $("#descripcionTextAreaCrear").val() === '' || $("#plantillaTextAreaCrear").val() === '')
      {

        $('#modalMensajes .modal-body').html('Todos los datos de la plantilla son obligatorios');
        $('#modalMensajes').modal({show: true});

      } else {


          var parametros = {
            "strCodigoPlantilla": $("#codigoPlantillaCrear").val(),
            "strDescripcion": $("#descripcionTextAreaCrear").val(),
            "strPlantilla":  $("#plantillaTextAreaCrear").val()
        };
        $.ajax({
            data: parametros,
            url: url_crear_plantilla_accion,
            type: 'post',
            success: function (response) {
                console.log(response);
                if (response.strStatus===0)
                {
                $('#tabla_lista_plantillas').DataTable().ajax.reload();
                $('#modalCrear').modal('hide');      
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});   
                }else{
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
                }
            },
            failure: function (response) {
                $('#modalMensajes .modal-body').html('No se pudo Crear la plantilla por favor contacte a soporte :error: ' + response.strMensaje);
                $('#modalMensajes').modal({show: true});
            }
        });
      }
  } 
 /**
  * Realiza la llamada a la función Ajax que edita plantilla
  *    
  * @author Ivan Romero <icromero@telconet.ec>
  * @version 1.0 05-12-2021
  * @since 1.0
  */
 function editarPlantillaAccion(url_editar_plantilla_accion)
 {
    
    
     if ($("#idPlantillaEditar").val() === '' || $("#codigoPlantillaEditar").val() === '' || $("#descripcionTextAreaEditar").val() === '' || $("#plantillaTextAreaEditar").val() === '')
     {

       $('#modalMensajes .modal-body').html('Todos los datos de la plantilla son obligatorios');
       $('#modalMensajes').modal({show: true});

     } else  {
         var parametros = {
           "intIdPlantilla": $("#idPlantillaEditar").val(),
           "strCodigoPlantilla": $("#codigoPlantillaEditar").val(),
           "strDescripcion": $("#descripcionTextAreaEditar").val(),
           "strPlantilla":  $("#plantillaTextAreaEditar").val()
       };
       $.ajax({
           data: parametros,
           url: url_editar_plantilla_accion,
           type: 'post',
           success: function (response) {
               console.log(response);
               if (response.strStatus===0)
               {
                    $('#tabla_lista_plantillas').DataTable().ajax.reload();
                    $('#modalEditar').modal('hide');      
                    $('#modalMensajes .modal-body').html(response.strMensaje);
                    $('#modalMensajes').modal({show: true});   
               }else{
                    $('#modalMensajes .modal-body').html(response.strMensaje);
                    $('#modalMensajes').modal({show: true});
               }
           },
           failure: function (response) {
               $('#modalMensajes .modal-body').html('No se pudo Editar la plantilla por favor contacte a soporte :error: ' + response.strMensaje);
               $('#modalMensajes').modal({show: true});
           }
       });
     }
 } 

 /**
  * Realiza la llamada a la función Ajax que elimina plantilla
  *    
  * @author Ivan Romero <icromero@telconet.ec>
  * @version 1.0 05-12-2021
  * @since 1.0
  */
  function eliminarPlantillaAccion(url_eliminar_plantilla_accion)
  {
     
     
      if ($("#codigoPlantillaEliminar").val() === '' || $("#descripcionTextAreaEliminar").val() === '')
      {
 
        $('#modalMensajes .modal-body').html('Todos los datos de la plantilla son obligatorios');
        $('#modalMensajes').modal({show: true});
 
      } else  {
          var parametros = {
            "strCodigoPlantilla": $("#codigoPlantillaEliminar").val(),
            "strDescripcion": $("#descripcionTextAreaEliminar").val()
        };
        $.ajax({
            data: parametros,
            url: url_eliminar_plantilla_accion,
            type: 'post',
            success: function (response) {
                console.log(response);
                if (response.strStatus===0)
                {
                $('#tabla_lista_plantillas').DataTable().ajax.reload();
                $('#modalEliminar').modal('hide');      
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});   
                }else{
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
                }
            },
            failure: function (response) {
                $('#modalMensajes .modal-body').html('No se pudo Editar la plantilla por favor contacte a soporte :error: ' + response.strMensaje);
                $('#modalMensajes').modal({show: true});
            }
        });
      }
  } 


 /**
  * Realiza la llamada a la función Ajax que descarga plantilla
  *    
  * @author Ivan Romero <icromero@telconet.ec>
  * @version 1.0 05-12-2021
  * @since 1.0
  */
  function descargarPlantillaAccion(url_descargar_plantilla_accion)
  {
     
     
      if ($("#codigoPlantillaDescargar").val() === '' || $("#plantillaTextAreaDescargar").val() === '')
      {
 
        $('#modalMensajes .modal-body').html('Todos los datos de la plantilla son obligatorios');
        $('#modalMensajes').modal({show: true});
 
      } else  {
          var parametros = {
            "intIdPlantilla": $("#idPlantillaDescargar").val(),
            "strCodigoPlantilla": $("#codigoPlantillaDescargar").val(),
            "strPlantilla": $("#plantillaTextAreaDescargar").val(),
            "strContrato":$("#contratoTextAreaDescargar").val()
        };
        $.ajax({
            data: parametros,
            url: url_descargar_plantilla_accion,
            type: 'post',
            success: function (response) {
                console.log(response);
                if (response.strStatus===0)
                {
                $('#tabla_lista_plantillas').DataTable().ajax.reload();
                $('#modalDescargar').modal('hide');
                var arrBuffer = base64ToArrayBuffer(response.objData.base64);
                var newBlob = new Blob([arrBuffer]);
                if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                    window.navigator.msSaveOrOpenBlob(newBlob);
                    return;
                }
                var data = window.URL.createObjectURL(newBlob);
                var link = document.createElement('a');
                document.body.appendChild(link);
                link.href = data;
                link.download = $("#codigoPlantillaDescargar").val()+".pdf";
                link.click();
                window.URL.revokeObjectURL(data);
                link.remove();

                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});   
                }else{
                $('#modalDescargar').modal('hide'); 
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
                }
            },
            failure: function (response) {
                $('#modalDescargar').modal('hide'); 
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
            }
        });
      }
  } 
 /**
  * transforma un base64 en un arraybuffer
  * @author Ivan Romero <icromero@telconet.ec>
  * @version 1.0 05-12-2021
  * @since 1.0
  */
  function base64ToArrayBuffer (base64) {
    base64 = base64.replace(/^data\:([^\;]+)\;base64,/gmi, '');
    var binaryString = atob(base64);
    var len = binaryString.length;
    var bytes = new Uint8Array(len);
    for (var i = 0; i < len; i++) {
        bytes[i] = binaryString.charCodeAt(i);
    }
    return bytes.buffer;
}