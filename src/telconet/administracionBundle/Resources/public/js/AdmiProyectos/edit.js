$('#strFechaInicioEditar').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy/mm/dd'
    });
$('#strFechaFinEditar').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy/mm/dd'
    });
$('#strResponsable_editar').select2({placeholder: "Seleccionar", multiple: false, width: "365px"});
 
getResponsablesEdit(); 
    
function getResponsablesEdit()
{
    $.ajax({
        url: url_responsables,
        method: 'post',
        success: function (data) {
            $(".spinner_sect_responsablesEdit").hide();
            $.each(data.arrayResponsable, function (id, registro) {
                $("#strResponsable_editar").append('<option value=' + registro.idRes + '>' + registro.nombre + ' </option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar listado de clientes. Por favor comuníquese con el departamento de Sistemas.");
            $('#modalMensajes').modal({show: true});
        }
    });
} 
    
/**
* Documentación para la función 'ActualizaProyecto'.
*
* Función encargada de crear el proyecto.
*
* @author Byron Anton <banton@telconet.ec>
* @version 1.0 11-05-2021
*
*/
function ActualizaProyecto()
{
    console.log("funcion");
    var arrayParametros = {
                             "strNombreProyecto"   : $("#strNombreProyecto_editar").val(),
                             "intIdProyecto"       : $('#intIdProyecto').val(),
                             "strResponsable"      : $("#strResponsable_editar").val(),
                             "strTipoContabilidad" : $("#strTipoContabilidad_editar").val(),
                             "strFechaInicioEditar": $('#strFechaInicioEditar').val(),
                             "strFechaFinEditar"   : $('#strFechaFinEditar').val(),
                             "strEstadoEditar"     : $('#strEstado_editar').val()
                            };
    if (validarFormulario())
    {
        $.ajax({
            data: arrayParametros,
            url: url_edit,
            type: 'post',
            success: function (response) {
                if (response)
                {
                    $('#strNombreProyecto_editar').val('');
                    $('#intIdProyecto').val('');
                    $('#tabla').DataTable().ajax.reload();
                    $('#modalMensajes .modal-body').html(response);
                    $('#modalMensajes').modal({show: true});
                }
            },
            beforeSend: function()
            {
                Ext.get(document.body).mask('Cargando Información.');
            },
            complete: function() 
            {
                Ext.get(document.body).unmask();
            },
            failure: function (response) {
                $('#modalMensajes .modal-body').html('No se pudo crear la solicitud por el siguiente error: ' + response);
                $('#modalMensajes').modal({show: true});
            }
        });
    }
        
}     

/**
* Documentación para la función 'validarFormulario'.
*
* Función que valida informacion de actualizacion.
*
* @author Byron Anton <banton@telconet.ec>
* @version 1.0 11-05-2021
*
*/
function validarFormulario()
{   
    var FechaInicio   = $('#strFechaInicioEditar').val();
    var FechaFin      = $('#strFechaFinEditar').val();
    if (FechaFin < FechaInicio)
    {
        $('#modalMensajes .modal-body').html('La fecha fin no debe ser menor a la fecha de inicio...');
        $('#modalMensajes').modal({show: true});
        return false;
    }    
    return true;
} 
