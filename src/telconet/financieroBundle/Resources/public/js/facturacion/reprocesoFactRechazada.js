
$(document).ready(function () {
        
    /**
     * Inicializa con valores vacíos el formulario de búsqueda.
     * @author Hector Lozano <hlozano@telconet.ec>
     */
    $('.spinner_buscarFactRechazadas').hide();
    limpiarFormBuscar();

    /**
     * Inicializa calendario de Fechas
     * @author Hector Lozano <hlozano@telconet.ec>
     */
    
    $("#tipo_rechazo").select2({placeholder: "SELECCIONAR", multiple: true});
     
    $('#fecha_emision_desde').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'dd-mm-yy'
    });
    
    $('#fecha_emision_hasta').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'dd-mm-yy'
    });


    //Obtiene los tipo de mensajes de errores.
    $.ajax({
        url: url_getTipoErrores,
        method: 'GET',
        success: function (data) {
          
            $.each(data.strTipoError, function (id, registro) {
                $("#tipo_rechazo").append('<option value=' + registro.strMensaje + '>' + registro.strMensaje + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Mensajes de Errores. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });

    /**
     * Obtiene el listado de facturas rechazadas.
     * @author Hector Lozano <hlozano@telconet.ec>
     */
    var listaFactRechazadas = $('#tabla_facturas_rechazadas').DataTable({
        "ajax": {
            "url": url_grid_factRechazadas,
            "type": "POST",
            "data": function (param) {
                
                var arrayTipoRechazo = [];
                $.each($('#tipo_rechazo').select2('data'), function (id, registro) {   
                  arrayTipoRechazo.push(registro.text);
                });
  
                param.arrayTipoRechazo  = arrayTipoRechazo;
                param.strFeEmisionDesde = ($('#fecha_emision_desde').val()).trim();
                param.strFeEmisionHasta = ($('#fecha_emision_hasta').val()).trim();
                param.strIdentificacion = ($('#identificacion_cliente').val()).trim();
                param.strLogin          = ($('#login').val()).trim();
                
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
            {"data": "intIdDocumento"},
            {"data": "strNumeroFactSri"},
            {"data": "strLogin"},
            {"data": "strNombreCliente"},
            {"data": "strIdentificacion"},
            {"data": "strEstado"},
            {"data": "strFeCreacion"},
            {"data": "strFeEmision"},
            {"data": "strValorTotal"},
            {"data": "strMensajeError"}                        
        ],
        "columnDefs": [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'render': function (data) {
                return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';               
            }
        }],
        "drawCallback": function() {
            $('.spinner_buscarFactRechazadas').hide(); 
        }
    });

    $('#factRechazada-select-all').on('click', function () {
        var rows = listaFactRechazadas.rows({'search': 'applied'}).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    $('#tabla_facturas_rechazadas tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) 
        {
            var el = $('#factRechazada-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) 
            {
                el.indeterminate = true;
            }
        }
    });


    $("#btnReprocesoIndividual").click(function () 
    {
        var arrayIdsDocumento = [];
        listaFactRechazadas.$('input[type="checkbox"]').each(function ()
        {
            if (this.checked)
            {
                arrayIdsDocumento.push(this.value);

            }
        });
        if (arrayIdsDocumento.length > 0)
        {
            $('#reprocesoIndividual .modal-body').html(" <h6>¿Está seguro de reenviar al SRI "+arrayIdsDocumento.length+" Facturas?</h6>");
             $('#reprocesoIndividual').modal({show: true});
        }
        else
        {
            $('#modalMensajes .modal-body').html('Seleccione por lo menos una Promoción de la lista.');
            $('#modalMensajes').modal({show: true});
        }
        
          
    });
    $("#btReprocesoIndividual").click(function () {
        reprocesoIndividual();
    });
     $("#btReprocesoMasivo").click(function () {
        reprocesoMasivo();
    });

    /**
     * Realiza la llamada a la función Ajax que genera el reprocesoIndividual
     */
    function reprocesoIndividual()
    {
        var arrayIdsDocumento = [];
        listaFactRechazadas.$('input[type="checkbox"]').each(function () {
            if (this.checked)
            {
                arrayIdsDocumento.push(this.value);

            }
        });
        if (arrayIdsDocumento.length > 0)
        {
            var parametros = {"strTipoTransaccion": 'INDIVIDUAL' ,
                              "strIdsDocumento"   : arrayIdsDocumento.toString()};
            
        $.ajax({
            data: parametros,
            url: urlEjecutarReprocesoFactRechazadas,
            type: 'post',
            success: function (response) {
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
            },
            failure: function (response) {
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
            }
        });       
        } else
        {
            $('#modalMensajes .modal-body').html('Seleccione por lo menos un registro de la lista.');
            $('#modalMensajes').modal({show: true});
        }
    } 
    
    
    /**
     * Realiza la llamada a la función Ajax que genera el reprocesoMasivo
     */ 
    function reprocesoMasivo()
    {      
        var parametros = {"strTipoTransaccion": 'MASIVO',
                          "strIdsDocumento"   : ''};
            
        $.ajax({
            data: parametros,
            url: urlEjecutarReprocesoFactRechazadas,
            type: 'post',
            success: function (response) {
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
            },
            failure: function (response) {
                $('#modalMensajes .modal-body').html(response.strMensaje);
                $('#modalMensajes').modal({show: true});
            }
        });       
    } 

    $("#buscarFactRechazadas").click(function () {
        $('.spinner_buscarFactRechazadas').show();
        $('#tabla_facturas_rechazadas').DataTable().ajax.reload();
    });

    $("#limpiarFactRechazadas").click(function () {
        limpiarFormBuscar();
    });

    function limpiarFormBuscar() {
        $('#fecha_emision_desde').val("");
        $('#fecha_emision_hasta').val("");
        $('#identificacion_cliente').val("");
        $('#login').val("");
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
    setInterval(function(){ $('#tabla_facturas_rechazadas').DataTable().ajax.reload(); }, 300000);
});

    
