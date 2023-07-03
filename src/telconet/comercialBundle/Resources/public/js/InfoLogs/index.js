
$(document).ready(function () {
    var intParametroRangoFechas = 0;
    $('#fecha_desde').datetimepicker({
        format: 'YYYY-MM-DD',
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

    $('#fecha_hasta').datetimepicker({
        format: 'YYYY-MM-DD',
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


    $("#limpiar_formulario").click(function () {
        limpiarFormBuscar();
    });
    function limpiarFormBuscar() 
    {
        $('#fecha_desde').val("");
        $('#fecha_hasta').val("");
        $('#metodos_consulta').val(null).trigger('change');
    }

    $.ajax({
        url: urlGetMetodos,
        method: 'GET',
        async: false,
        success: function (data) {
            $("#metodos_consulta").append('<option value=0> Seleccione </option>');
            $.each(data.lista_metodos, function (id, registro) {                
                $("#metodos_consulta").append('<option value=' + registro.metodo + '>' + registro.descripcion + '</option>');
            });
        },
        error: function () {            
            $('#modalMensajes .modal-body').html("No se pueden cargar los metodos");
            $('#modalMensajes').modal({show: true});
        }
    });


    $('#metodos_consulta').select2({
        placeholder:'Seleccione'
     });  

    $.ajax({
        url: urlGetRangoFechas,
        method: 'get',
        async: false,
        success: function (data) {
          intParametroRangoFechas = data.intRangoFechas;
        },
        error: function () {
            $('#modalMensajes .modal-body').html("Error al consultar parametro de rango de fechas. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    }); 
 
    $("#buscar_info_logs").click(function () {
        var strFechaDesde = moment($('#fecha_desde').val());
        var strFechaHasta = moment($('#fecha_hasta').val());
        var intRangoDias = strFechaHasta.diff(strFechaDesde, 'days');

        if(strFechaDesde > strFechaHasta)
        {
            $('#modalMensajes .modal-body').html('Rango de fechas no permitido. Favor revisar. ');
            $('#modalMensajes').modal({show: true});
            return false;
        }

        if(intRangoDias > intParametroRangoFechas)
        {
            $('#modalMensajes .modal-body').html('El rango permitido de consulta es de '+intParametroRangoFechas+' dias. Favor revisar. ');
            $('#modalMensajes').modal({show: true});
            return false;
        }
        $('#tabla_lista_logs').DataTable().ajax.reload();
    });
       
    /**
     * Obtiene el listado de logs.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 05-01-2022
     * @since 1.0
     */
    $('#tabla_lista_logs').DataTable({
        "ajax": {
            "url": urlGridInfoLogs,
            "type": "POST",
            "data": function (param) {
                param.strFechaDesde = $('#fecha_desde').val();
                param.strFechaHasta = $('#fecha_hasta').val();
                param.strMetodo     = $('#metodos_consulta').val();
            }
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
            {"data": "strOrigen"},
            {"data": "strMetodo"},
            {"data": "strNombre"},
            {"data": "strApellido"},
            {"data": "strRazonSocial"},
            {"data": "strIdentificacion"},
            {"data": "strTipoIdentificacion"},
            {"data": "strTipoTributario"},
            {"data": "strLoginPto"},
            {"data": "strUsrEvento"},
            {"data": "strFechaEvento"},
            {"data": "strIpEvento"}
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
    setInterval(function(){ $('#tabla_lista_logs').DataTable().ajax.reload(); }, 300000);
});


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