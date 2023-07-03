$(document).ready(function () {
        
    $("#btDiferidoFacturas").click(function () 
    {
        ejecutaDiferidoFacturas(); 
    });   
    
    $("#limpiarDiferidoFacturas").click(function () 
    {
        limpiarDatos(); 
    });      
    
    function limpiarDatos()
    {       
        $('#meses_diferir').val(null).trigger('change');  
        $('#idContenedorCuotas').html('');
        $("#idContenedorCuotas").hide();
    }

    var strParametroCab     = "PROCESO_EMER_SANITARIA";
    var strDescripcionMeses = "MES_DIFERIDO";
    
    $.ajax({
        url     : urlGetParametrosDet,
        method  : 'GET',
        data: {'strParametroCab': strParametroCab,
               'strDescripcionDet': strDescripcionMeses},
        success: function (data) {
            $.each(data.arrayValores, function (id, registro) {
                $("#meses_diferir").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>No se pueden cargar los meses a diferir.</p>');
            $('#modalMensajes').modal('show');
        }
    });
    $('#meses_diferir').select2({
        placeholder :'Seleccione Meses a Diferir'
     });
    
     $("#idContenedorCuotas").hide();
           
    $('#meses_diferir').change(function(e) {
        var strMesesDiferir     = "";
        var fltCuotas           = 0;
        var contador            = 0;
        $("#meses_diferir :selected").each(function(){
            strMesesDiferir       = $(this).text();
            if (strMesesDiferir)
            {
                contador = contador + 1;
            }
        });
        if(contador === 0)
        {
            $("#idContenedorCuotas").hide();           
        }
        else
        {      
            fltCuotas = (fltTotalSaldoFactPorPto/strMesesDiferir);
            $('#idContenedorCuotas').html('Cuotas de $ ' + (Math.round(fltCuotas * 100)/100).toFixed(2) + ' por ' + strMesesDiferir + ' meses.');
            $("#idContenedorCuotas").show();
        }
    });
    
    function ejecutaDiferidoFacturas()
    {
        var mesesDiferir = $("#meses_diferir").val();        

        if (fltTotalSaldoFactPorPto <= 0)
        {
            $('#modalMensajes .modal-body').html('No se pudo ejecutar Diferido de Facturas, El Punto no posee Facturas impagas.');
            $('#modalMensajes').modal({show: true});
        } else
        {
            if (mesesDiferir)
            {
                var parametros = {
                    "intIdPunto": intIdPunto,
                    "strMesesDiferir": mesesDiferir
                };

                $.ajax({
                    data: parametros,
                    url: urlAjaxEjecutarEmergenciaSanitariaPto,
                    type: 'post',
                    success: function (response) {
                        if (response === "OK")
                        {
                            $('#modalMensajes .modal-body').html('<p>Se Ejecutó con éxito el Proceso de Diferido de Facturas.</p>');
                            $('#modalMensajes').modal('show');
                            limpiarDatos();
                        } else
                        {
                            if (response === "EXISTE")
                            {
                                $('#modalMensajes .modal-body').html("No se pudo ejecutar Diferido de Facturas, aún existe un proceso pendiente de \n\
                                                               ejecución.");
                                $('#modalMensajes').modal('show');
                            } else
                            {
                                $('#modalMensajes .modal-body').html(response);
                                $('#modalMensajes').modal('show');
                            }
                        }
                    },
                    failure: function (response) {
                        $('#modalMensajes .modal-body').html('No se pudo ejecutar Diferido de Facturas existe un error: ' + response);
                        $('#modalMensajes').modal({show: true});
                    }
                });
            } else
            {
                $('#modalMensajes .modal-body').html('No se pudo ejecutar Diferido de Facturas, Seleccione los meses a Diferir.');
                $('#modalMensajes').modal({show: true});
            }
        }
    }
});
