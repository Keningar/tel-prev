$(document).ready(function ()
{
    var boolPermisoCrear    = (typeof boolCrear === 'undefined')    ? false : (boolCrear ? true : false);
    var boolPermisoEditar    = (typeof boolEditar === 'undefined')    ? false : (boolEditar ? true : false);
         
    
    $('#strResponsable_crear').select2({placeholder: "Seleccionar", multiple: false, width: "365px"});
    getResponsables();
    $('#strFechaInicioBuscar').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'dd/mm/yy'
    });
    $('#strFechaFinBuscar').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'dd/mm/yy'
    });
    
    $('#strFechaInicioCrear').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy/mm/dd'
    });
    $('#strFechaFinCrear').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy/mm/dd'
    });


    var objListado = $('#tabla').DataTable({
        "ajax": {
            "url": url_grid,
            "type": "POST",
            beforeSend: function()
            {
                Ext.get(document.body).mask('Cargando Información.');
            },
            complete: function() 
            {
                Ext.get(document.body).unmask();
            },
            "data": function (param) {
                param.strNombreProyecto = $("#strNombreProyecto_buscar").val();
                param.strFechaInicio    = $('#strFechaInicioBuscar').val();
                param.strFechaFin       = $('#strFechaFinBuscar').val();
                param.strEstado         = $('#strEstadoBuscar option:selected').val();
            }
        },
        "language": {
            "oPaginate": {
                "sPrevious": "Anterior",
                "sNext": "Siguiente"
            },
            "sProcessing": "Procesando...",
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "No hay información disponible",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "processing": true,
            "loadingRecords": "Cargando datos..."
        },
        "columns": [
                {"data": "intIdProyecto"},
                {"data": "strNombre"},
                {"data": "strNombrePer"},
                {"data": "strTipoContabilidad"},
                {"data": "intIdCuenta"},
                {"data": "strFeInicio"},
                {"data": "strFeFin"},
                {"data": "strEstado"},
                {"data": "strAcciones",
                    "render": function (data)
                    {
                        var strDatoRetorna = '';
                        if (data.linkVer !== '') 
                        {
                            strDatoRetorna += '<button type="button" class="btn btn-info btn-sm" ' + ' title="Ver Detalle" ' +
                                'onClick="javascript:mostrarModalDetalle(\'' + data.linkVer + '\');">' + '<i class="fa fa-search"></i>' +
                                '</button>&nbsp;';
                            
                        }
                         if (boolPermisoEditar && data.linkEditar !== '') 
                        {
                            strDatoRetorna += '<button type="button" class="btn btn-secondary btn-sm" ' + ' title="Editar" ' +
                                'onClick="javascript:mostrarModalEdit(\'' + data.linkEditar + '\');">' + '<i class="fa fa-edit"></i>' +
                                '</button>&nbsp;';
                            
                        }
                        return strDatoRetorna;
                        
                    }
                }
                
                
        ],
        'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'render': function (data) {
                return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
            }
        }]
    });

    $('#objListado-select-all').on('click', function () {
        var rows = objListado.rows({'search': 'applied'}).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    if(boolPermisoCrear)
    {
        $("#tabla_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalCrear" '+
        'class="btn btn-success btn-sm" title="Nuevo Proyecto" <i class="fa fa-files-o"></i> Nuevo Proyecto </button>');
    }

    $('#tabla tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) 
        {
            var el = $('#objListado-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) 
            {
                el.indeterminate = true;
            }
        }
    });
    $("#btnCrear").click(function () {
		console.log("click en crear");
        crearProyecto();
    });
    
    $("#btnEditar").click(function () {
		console.log("click en editar");
        ActualizaProyecto();
    });

    /**
     * Documentación para la función 'crearProyecto'.
     *
     * Función encargada de crear el proyecto.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
    */
    function crearProyecto()
    {
        console.log("funcion");
        var arrayParametros = {"strTipoContabilidad" : $("#strTipoContabilidad_crear").val(),
                                 "strNombreProyecto"   : $("#strNombreProyecto_crear").val(),
                                 "strCuenta"           : $('#strCuenta_crear').val(),
                                 "strFechaInicioCrear" : $('#strFechaInicioCrear').val(),
                                 "strFechaFinCrear"    : $('#strFechaFinCrear').val(),
                                 "strResponsable"      : $("#strResponsable_crear").val()
                                };
        $.ajax({
            data: arrayParametros,
            url: url_new,
            type: 'post',
            success: function (response) {
                if (response)
                {
                    $('#strNombreProyecto_crear').val('');
                    $('#strCuenta_crear').val('');
                    $('#strResponsable_crear').select2({placeholder: "Seleccionar", multiple: false, width: "365px"});
                    $('#strResponsable_crear').empty();
                    getResponsables();
                    $('#strFechaInicioCrear').val('');
                    $('#strFechaFinCrear').val('');
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

    $("#buscar").click(function () {
        $('#tabla').DataTable().ajax.reload();
    });

    $("#limpiar").click(function () {
        limpiarFormBuscar();
    });

    /**
     * Documentación para la función 'limpiarFormBuscar'.
     *
     * Función encargada de limpiar los campos.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
     */
    function limpiarFormBuscar()
    {
        $('#strNombreProyecto_buscar').val("");
        $('#strFechaInicioBuscar').val("");
        $('#strFechaFinBuscar').val("");
        $('#strEstadoBuscar').val("");
    }

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
});

/**
 * Documentación para la función 'mostrarModalDetalle'.
 *
 * Función encargada de mostrar el detalle de la solicitud.
 *
 * @author Byron Anton <banton@telconet.ec>
 * @version 1.0 11-05-2021
 *
 */
function mostrarModalDetalle(url_accion)
{
    $.ajax({
        url: url_accion,
        type: 'get',
        dataType: "html",
        success: function (response) {
            $('#modalDetalle .modal-body').html(response);
            $('#modalDetalle').modal({show: true});
        },
        beforeSend: function()
        {
            Ext.get(document.body).mask('Cargando Información.');
        },
        complete: function() 
        {
            Ext.get(document.body).unmask();
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>');
            $('#modalMensajes').modal('show');
        }
    });
}


/**
 * Documentación para la función 'mostrarModalEdit'.
 *
 * Función encargada de mostrar el detalle de la solicitud.
 *
 * @author Byron Anton <banton@telconet.ec>
 * @version 1.0 11-05-2021
 *
 */
function mostrarModalEdit(url_accion)
{
    $.ajax({
        url: url_accion,
        type: 'get',
        dataType: "html",
        success: function (response) {
            $('#modalEdit .modal-body').html(response);
            $('#modalEdit').modal({show: true});
        },
        beforeSend: function()
        {
            Ext.get(document.body).mask('Cargando Información.');
        },
        complete: function() 
        {
            Ext.get(document.body).unmask();
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>Ocurrió un error. Por favor comuníquese con Sistemas.</p>');
            $('#modalMensajes').modal('show');
        }
    });
}

/**
     * Documentación para la función 'getResponsables'.
     *
     * Función que devuelve lista de responsables.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 11-05-2021
     *
    */  
    
function getResponsables()
{
    $.ajax({
        url: url_responsables,
        method: 'post',
        success: function (data) {
            $(".spinner_sect_responsables").hide();
            $("#strResponsable_crear").append('<option value=Seleccione></option>');
            $.each(data.arrayResponsable, function (id, registro) {
                 $("#strResponsable_crear").append('<option value=' + registro.idRes + '>' + registro.nombre + ' </option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar listado de clientes. Por favor comuníquese con el departamento de Sistemas.");
            $('#modalMensajes').modal({show: true});
        }
    });
}        
    
    