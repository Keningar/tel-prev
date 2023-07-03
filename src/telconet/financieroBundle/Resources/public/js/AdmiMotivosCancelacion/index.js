
$(document).ready(function () {
        
    /**
     * Obtiene el listado de motivos.
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     */
    $('#tabla_lista_motivos').DataTable({
        "ajax": {
            "url": url_grid_motivos,
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
            {"data": "strdescripcion"},
            {"data": "strFechaCreacion"},
            {"data": "strUsrCreacion"},
            {"data": "strFechaModifica"},
            {"data": "strUsrModifica"},
            {"data": "strAcciones",
                "render": function (data){
                    var strDatoRetorna = '';

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
    setInterval(function(){ $('#tabla_lista_motivos').DataTable().ajax.reload(); }, 300000);
});

    
/**
 * Muestra una ventana modal con el detalle de la Motivo,
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-12-2021
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
 * Muestra una ventana modal para editar Motivo,
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-12-2021
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
 * Muestra una ventana modal eliminar motivo
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-12-2021
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
 * Muestra una ventana modal para crear motivo
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-12-2021
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
  * Realiza la llamada a la función Ajax crea un motivo de cancelacion
  *    
  * @author Ivan Romero <icromero@telconet.ec>
  * @version 1.0 25-12-2021
  * @since 1.0
  */
  function crearMotivoAccion(url_crear_motivo_accion)
  {
      
     
      if ($("#descripcionMotivoCrear").val() === '' )
      {

        $('#modalMensajes .modal-body').html('Descripción del motivo de cancelación es obligatoria');
        $('#modalMensajes').modal({show: true});

      } else {


          var parametros = {
            "strDescripcionMotivo": $("#descripcionMotivoCrear").val()
        };
        $.ajax({
            data: parametros,
            url: url_crear_motivo_accion,
            type: 'post',
            success: function (response) {
                console.log(response);
                if (response.strStatus==='0')
                {
                $('#tabla_lista_motivos').DataTable().ajax.reload();
                $('#modalCrear').modal('hide');      
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});   
                }else{
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
                }
            },
            failure: function (response) {
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
            }
        });
      }
  } 
 /**
  * Realiza la llamada a la función Ajax editar un motivo de cancelacion
  *    
  * @author Ivan Romero <icromero@telconet.ec>
  * @version 1.0 25-12-2021
  * @since 1.0
  */
 function editarMotivoAccion(url_editar_motivo_accion)
 {
    
    
     if ($("#descripcionMotivoEditar").val() === '' )
     {

       $('#modalMensajes .modal-body').html('Descripción del motivo de cancelación es obligatoria');
       $('#modalMensajes').modal({show: true});

     } else  {
         var parametros = {
           "strDescripcionMotivo": $("#descripcionMotivoEditar").val(),
           "intIdMotivo": $("#idMotivoEditar").val()
       };
       $.ajax({
           data: parametros,
           url: url_editar_motivo_accion,
           type: 'post',
           success: function (response) {
               console.log(response);
               if (response.strStatus==='0')
               {
                    $('#tabla_lista_motivos').DataTable().ajax.reload();
                    $('#modalEditar').modal('hide');      
                    $('#modalMensajes .modal-body').html(response.strMensaje);
                    $('#modalMensajes').modal({show: true}); 
                    console.log("editocorrectamte");
               }else{
                    $('#modalMensajes .modal-body').html(response.strMensaje);
                    $('#modalMensajes').modal({show: true});
               }
           },
           failure: function (response) {
               $('#modalMensajes .modal-body').html(response.strMensaje);
               $('#modalMensajes').modal({show: true});
           }
       });
     }
 } 

 /**
  * Realiza la llamada a la función Ajax elimina un motivo de cancelacion
  *    
  * @author Ivan Romero <icromero@telconet.ec>
  * @version 1.0 25-12-2021
  * @since 1.0
  */
  function eliminarMotivoAccion(url_eliminar_motivo_accion)
  {
     
     
      if ($("#idMotivoEditar").val() === '' )
      {
 
        $('#modalMensajes .modal-body').html('  son obligatorios');
        $('#modalMensajes').modal({show: true});
 
      } else  {
          var parametros = {
            "intIdMotivo": $("#idMotivoEditar").val()
        };
        $.ajax({
            data: parametros,
            url: url_eliminar_motivo_accion,
            type: 'post',
            success: function (response) {
                console.log(response);
                if (response.strStatus==='0')
                {
                $('#tabla_lista_motivos').DataTable().ajax.reload();
                $('#modalEliminar').modal('hide');      
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});   
                }else{
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
                }
            },
            failure: function (response) {
                $('#modalMensajes .modal-body').html( response.strMensaje);
                $('#modalMensajes').modal({show: true});
            }
        });
      }
  } 