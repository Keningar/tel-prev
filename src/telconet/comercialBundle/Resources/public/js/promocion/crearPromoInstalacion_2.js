/**
 * Función Guarda Promoción de Instalación
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0 29-03-2019
 * @since 1.0
 */
function grabarPromocionInstalacion()
{
    $(".spinner_guardarPromocion").show();
    $("#guardarPromociones").attr('disabled','disabled');
    var arrayPeriodo            = [];
    var arrayEmisores           = [];
    var arraySectorizacion      = [];
        
    //Períodos
    $('[name="checkboxPeriodos"]:checked').each(function(){        
        if($("#descuento").val() =='')
        {
           arrayPeriodo.push(this.value+'|0');
        }
        else
        {
           arrayPeriodo.push(this.value+'|'+$("#descuento").val()); 
        }        
    });
    //Emisores
    $("#tablaInformacionEmisores tbody tr").each(function (index) {
        var tipoCuenta, banco;
        $(this).children("td").each(function (index2) {
            switch (index2) {
                case 0:
                    tipoCuenta = $(this).text();
                    break;
                case 1:
                    banco = $(this).text();
                    break;
                default:
                    break;
            }
        });
        arrayEmisores.push(tipoCuenta + '|' + banco);
    });
    //Sectorización
    $("#tablaInfSectorizacion tbody tr").each(function (index) {
        var intJurisdiccion, intCanton, intParroquia, strOptSectOltEdif, intSectOltEdif;
        $(this).children("td").each(function (index2) {
            switch (index2) {
                case 0:
                    intJurisdiccion = $(this).text();
                    break;
                case 1:
                    intCanton = $(this).text();
                    break;
                case 2:
                    intParroquia = $(this).text();
                    break;
                case 3:
                    strOptSectOltEdif = $(this).text();
                    break;
                case 4:
                    intSectOltEdif = $(this).text();
                    break;
                default:
                    break;
            }
        });
        arraySectorizacion.push({"intJurisdiccion": intJurisdiccion, "intCanton": intCanton, "intParroquia": intParroquia,
            "strOptSectOltEdif": strOptSectOltEdif, "intSectOltEdif": intSectOltEdif});
    });
        
    /* @author Hector Lozano <hlozano@telconet.ec>
    *  @version 1.0 1-07-2022 - Se agrega función JSON.stringify para codificar el JSON 
    *                           de Emisores y Sectorización para enviarlos desde la peticion ajax de la interfaz,
    *                           la cual se utilizó para enviar una gran cantidad de información de la misma.  
    */         
    var parametros = {
                      "strNombrePromocion"     : $("#nombre_promocion").val(),
                      "strInicioVigencia"      : $("#inicio_vigencia").val(),
                      "strFinVigencia"         : $("#fin_vigencia").val(),
                      "arrayTiposNegocio"      : $("#tipo_negocio").val(),                      
                      "arrayFormasPago"        : $("#forma_pago").val(),
                      "arrayUltimasMilla"      : $("#ultima_milla").val(),
                      "arrayEstadoServicio"    : $("#estado_servicio").val(),
                      "strCodigoPromocion"     : $("#codigo_promocion").val(),
                      "arrayEmisores"          : JSON.stringify(arrayEmisores),
                      "arrayPeriodo"           : arrayPeriodo,                     
                      "arraySectorizacion"     : JSON.stringify(arraySectorizacion)                      
        };

    $.ajax({
        data: parametros,
        url:  urlAjaxGuardarPromoInstalacion,
        type: 'post',       
        success: function (response) {
            $(".spinner_guardarPromocion").hide();
            $("#guardarPromociones").removeAttr("disabled");
            location.reload();
            if (response === "OK")
            {                
                $('#modalMensajes .modal-body').html('Se Ingresó con éxito la Promoción de Instalación.');
                $('#modalMensajes').modal({show: true});
            } 
            else
            {                
                $('#modalMensajes .modal-body').html('No se pudo guardar la Promoción. Por favor consulte con el Administrador.');
                $('#modalMensajes').modal({show: true});
            }
        },
        failure: function (response) {
            $(".spinner_guardarPromocion").hide();
            $("#guardarPromociones").removeAttr("disabled");
            $('#modalMensajes .modal-body').html("No se pudo guardar la Promoción. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});            
        }
    });
}
/**
 * validaInfEmisores, función valida que se ingresen los emisores por débito bancario.
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 27-04-2019
 * @since 1.0
 */
function validaInfEmisores()
{
    var strFormaPago = "";
    var contFormaPago = 0;
    var contTabla = 0;
    $("#forma_pago :selected").each(function () {
        strFormaPago = $(this).text();
        if (strFormaPago === "DEBITO BANCARIO")
        {
            contFormaPago = contFormaPago + 1;
        }
    });
    if (contFormaPago === 0)
    {
        return true;
    } 
    else
    {
        $("#tablaInformacionEmisores tbody tr").each(function (index) {
            contTabla++;
        });
        return (contTabla !== 0);
    }
}
    
/**
 * limpiarEmisores, función que limpia información de emisores por dédito bancario.
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 27-04-2019
 * @since 1.0
 */
function limpiarEmisores()
{
    $("#opcionEmisorTarCta option").each(function () {
        $(this).remove();
    });
    $("#opcionEmisorBanco option").each(function () {
        $(this).remove();
    });
    $("#optTipoEmisorTarjeta").prop('checked', false);
    $("#optTipoEmisorCtaBanco").prop('checked', false);
    $("#optTipoSi").prop('checked', false);
    $("#optTipoNo").prop('checked', false);
    $("#tbodyInformacionEmisores").remove();
    $('#tablaInformacionEmisores').append('<tbody id="tbodyInformacionEmisores"></tbody>');
    $("#contenedorTablaEmisor").hide();
    $("#contenedorBanco").hide();
}
/**
 * obtenerTipoEmisor, función obtiene los tipo de emisores por Cuentas
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0 22-03-2019
 * @since 1.0
 */
function obtenerTipoEmisor(codTipo)
{
    var tipoTodos = $('input:radio[name=optTipoTodos]:checked').val();
    var idTipo = codTipo;
    if (idTipo === "Tarjeta")
    {
        $("#contenedorBanco").hide();
        if (tipoTodos === 'No')
        {
            $("#contenedorTarjCta").show();
        } else
        {
            $("#contenedorTarjCta").hide();
        }

    } else
    {
        if (idTipo === "Cuenta Bancaria")
        {
            $("#contenedorTarjCta").show();
            if (tipoTodos === 'No')
            {
                $("#contenedorBanco").show();
            }
        }
    }
    $(".spinner_tajeta_ctaBanco").show();
    $.ajax({
        url: urlGetTipoCuenta,
        method: 'GET',
        data: {'strEsCuentaTarjetaSelected': idTipo},
        success: function (data) {
            $(".spinner_tajeta_ctaBanco").hide();
            $("#opcionEmisorTarCta option").each(function () {
                $(this).remove();
            });
            $("#opcionEmisorBanco option").each(function () {
                $(this).remove();
            });
            $.each(data.encontrados, function (id, registro) {
                $("#opcionEmisorTarCta").append('<option name="' + registro.strDescripcionCuenta +
                    '" value="' + registro.intIdTipoCuenta + '">' + registro.strDescripcionCuenta + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html('<p>No se pueden cargar las opciones por tipo de Emisor.</p>');
            $('#modalMensajes').modal('show');
        }
    });
}
/**
 * Valida que por lo menos un período sea checkeado.
 * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
 * @version 1.0 04-04-2019
 * @since 1.0
 */
function validaPeriodoPromocion() 
{    
    var boolChecked = false;
    $('[name="checkboxPeriodos"]:checked').each(function(){           
         if(this.value==1)
         {
            boolChecked = true;
         }            
        });
    return boolChecked;
}
/**
 * Valida Descuento valor numérico entero o decimal
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0 29-03-2019
 * @since 1.0
 */
function validaDescuento()
{   
   return  /^\d+(\.\d+)?$/.test($("#descuento").val());
}
/**
 * Muestra div
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0 29-03-2019
 * @since 1.0
 */
function mostrarDiv(div)
{
    var capa = document.getElementById(div);
    capa.style.display = 'block';
}
/**
 * Oculta Div
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0 29-03-2019
 * @since 1.0
 */
function ocultarDiv(div)
{
    var capa = document.getElementById(div);
    capa.style.display = 'none';
}
