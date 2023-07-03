
/**
 * Envia una solicitud de precancelacion
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-12-2021
 * @since 1.0
 */
  function enviarSolicitud() {

    var nombresCliente = $("#nombres_cliente").text();
    var contratoCliente = $("#contrato_cliente").text();
    var categoriaCliente = $("#categoria_cliente").text();
    var loginCliente = $("#login_cliente").text();
    var servicioCliente = $("#comboBoxServiciosCancelacion option:selected").text();
    var deudaCliente = $("#deuda_cliente").text();
    var direccionCliente = $("#direccion_cliente").text();
    var motivoCliente = $("#comboBoxMotivosCancelacion option:selected").text();
    var nombresApellidosCompletos =  $("#nombres_apellidos_cliente").text();
    var observacionesCancelacion = $("#observacionesCancelacion").val(); 
    var oficinaCliente =  $("#oficina_cliente").text();
    var fechaActualCliente =  $("#fecha_actual_cliente").text();
    var valorEquipos =  $("#valor_equipos").text();
    var valorInstalacion =  $("#valor_instalacion").text();
    var valorPromocion =  $("#valor_promocion").text();
    var valorSubtotalFactura =  $("#valor_subtotal_factura").text();
    var identificacionCliente =  $("#id_cliente").text();
    var fechaVigencia =  $("#fecha_vigencia").text();
    var entregaEquipos =  $("#entrega_equipos").text();
    
    var parametros = {    
        "strNombresCliente" : nombresCliente,
        "strContratoCliente"  : contratoCliente,
        "strCategoriaCliente" : categoriaCliente,
        "strLoginCliente" : loginCliente,
        "strServicioCliente" : servicioCliente,
        "strDeudaCliente" : deudaCliente,
        "strDireccionCliente" : direccionCliente,
        "strMotivoCliente" : motivoCliente,
        "strNombresApellidosCompletos" : nombresApellidosCompletos,
        "strObservacionesCancelacion" : observacionesCancelacion,
        "strOficinaCliente" : oficinaCliente,
        "strFechaActualCliente" : fechaActualCliente,  
        "strValorEquipos"  : valorEquipos,
        "strValorInstalacion" : valorInstalacion ,
        "strValorPromociones" : valorPromocion ,
        "strValorSubtotalFactura" : valorSubtotalFactura,
        "strIdentificacion" : identificacionCliente ,
        "strFechaVigencia" : fechaVigencia ,
        "entregaEquipos":entregaEquipos,
        "objCliente" : objCliente,
        "objEquipos" : objEquipos                        
    };
    

    $.ajax({
        data: parametros,
        url: url_solicitud_pre_cancelacion_enviarTareaRapida,
        type: 'post',
        dataType: "json",
        success: function (response) {
            console.log(response);
            if (response.strStatus==="Ok")
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
        error: function (response) {
            $('#modalMensajes .modal-body').html('No se pudo generar la solicitud por favor contacte a soporte :error: ' + response.strMensaje);
            $('#modalMensajes').modal({show: true});
        }
    });

}

/**
 * Calcula valores a pagar segun estado de equipos
 *    
 * @author Ivan Romero <icromero@telconet.ec>
 * @version 1.0 25-12-2021
 * @since 1.0
 */
 function
calcularValorEquipos(selectObject){

    var comboBoxEstadoEquipo = selectObject.value;
    var comboBoxDescripcionEquipo = selectObject.attributes.getNamedItem("data-descripcion").value;
    var entregaEquipos = "SI";
    var valorEquiposFacturar =  $("#valor_equipos").text();
    var valorSubtotal = 0;
    var valorInstalacion = $("#valor_instalacion").text();
    var valorPromocion = $("#valor_promocion").text();
    var totalEquiposFacturar = 0;
    for (var i = 0; i < objEquipos.length; i++)
    {
        var objEquipo = objEquipos[i];
        if(objEquipo.descripcion===comboBoxDescripcionEquipo){
            if(comboBoxEstadoEquipo!=="Bueno"){
                if(objEquipos[i].estado==="Bueno"){
                    totalEquiposFacturar = parseFloat(valorEquiposFacturar) + parseFloat(objEquipo.precio);
                }else{
                    totalEquiposFacturar =  parseFloat(valorEquiposFacturar);
                }
            }else{
                    totalEquiposFacturar = parseFloat(valorEquiposFacturar) - parseFloat(objEquipo.precio);
            }
            objEquipos[i].estado = comboBoxEstadoEquipo;
        }
    }

    for (var j = 0; j < objEquipos.length; j++)
    {
        if(objEquipos[j].estado!=="Bueno")
        {
            entregaEquipos = "NO";
            break;
        }
    }
    valorSubtotal = parseFloat(valorInstalacion) + parseFloat(totalEquiposFacturar) +parseFloat(valorPromocion);
    $("#entrega_equipos").text(entregaEquipos);
    $("#valor_subtotal_factura").text(valorSubtotal.toFixed(2));
    $("#valor_equipos").text(totalEquiposFacturar.toFixed(2));
}