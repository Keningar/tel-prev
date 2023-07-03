$(document).ready(function () {
   
    /**
    * limpiarEmisores, función que limpia información de emisores por dédito bancario.
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 27-02-2019
    */ 
    $("#idContenedorEmisores").hide();
    
    function limpiarEmisores()
    {
        $("#opcionEmisorTarCta option").each(function () {
            $(this).remove();
        });
        $("#opcionEmisorBanco option").each(function () {
            $(this).remove();
        });
        $("#optTipoEmisorTarjeta").prop('checked',false);
        $("#optTipoEmisorCtaBanco").prop('checked',false);
        $("#optTipoSi").prop('checked',false);
        $("#optTipoNo").prop('checked',false);
        $("#tbodyInformacionEmisores").remove();
        $('#tablaInformacionEmisores').append('<tbody id="tbodyInformacionEmisores"></tbody>');
        $("#contenedorTablaEmisor").hide();
        $("#contenedorBanco").hide();
    }
    
    $('#forma_pago').change(function(e) {
        var strFormaPago        = "";
        var contador            = 0;
        $("#forma_pago :selected").each(function(){
            strFormaPago       = $(this).text();
            if (strFormaPago === "DEBITO BANCARIO")
            {
                contador = contador + 1;
            }
        });
        if(contador === 0)
        {
            $("#idContenedorEmisores").hide();
            limpiarEmisores();
        }
        else
        {
            $("#idContenedorEmisores").show();
        }
    });
    $(".spinner_tajeta_ctaBanco").hide();
    $(".spinner_banco").hide();
    $("#contenedorTablaEmisor").hide();
    $("#contenedorBanco").hide();
    $("#contenedorTarjCta").hide();
    $('input:radio[name="optTipoEmisor"]').change(function() {
        $("#optTipoSi").prop('checked',true);
        obtenerTipoEmisor($(this).val());
    });
    
    /**
    * obtenerTipoEmisor, función obtiene los tipo de emisores por Cuentas
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 27-02-2019
    */    
    function obtenerTipoEmisor(codTipo)
    {
        var tipoTodos   = $('input:radio[name=optTipoTodos]:checked').val();
        var idTipo      = codTipo;
        if (idTipo === "Tarjeta")
        {
            $("#contenedorBanco").hide();
            if(tipoTodos === 'No')
            {
                $("#contenedorTarjCta").show();
            }
            else
            {
                $("#contenedorTarjCta").hide();
            }        
        }else
        {
            if (idTipo === "Cuenta Bancaria")
            {
                $("#contenedorTarjCta").show();
                if(tipoTodos === 'No')
                {
                    $("#contenedorBanco").show();
                }
            }
        }
        $(".spinner_tajeta_ctaBanco").show();
        
        $.ajax({
            url     : urlGetTipoCuenta,
            method  : 'GET',
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
                    $("#opcionEmisorTarCta").append('<option name="'+registro.strDescripcionCuenta+
                            '" value="' + registro.intIdTipoCuenta + '">' + registro.strDescripcionCuenta + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html('<p>No se pueden cargar las opciones por tipo de Emisor.</p>');
                $('#modalMensajes').modal('show');
            }
        });
    }
    //Select de tarjetas o cuentas bancarias
    $('#opcionEmisorTarCta').select2({placeholder: "Seleccionar"}).on('select2:select', function (e) {
        var idOpcionEmisorTarCta = e.params.data.id;
        $(".spinner_banco").show();
        $.ajax({
            url     : urlGetBancos,
            method  : 'GET',
            data: {'idTipoCuentaSelected': idOpcionEmisorTarCta},
            success: function (data) {
                $(".spinner_banco").hide();
                $("#opcionEmisorBanco option").each(function () {
                    $(this).remove();
                });
                $.each(data.encontrados, function (id, registro) {
                    $("#opcionEmisorBanco").append('<option name="'+registro.strDescripcionBanco+
                            '" value="' + registro.intIdBanco + '">' + registro.strDescripcionBanco + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html('<p>No se pueden cargar los Bancos.</p>');
                $('#modalMensajes').modal('show');
            }
        });
    });
    //RadioButton
    $('input:radio[name="optTipoTodos"]').change(function() {
        var Todos                       = $(this).val();
        var tipoEmisor                  = $('input:radio[name=optTipoEmisor]:checked').val();
        var validaOptTipoEmisorTarjeta  = $("#optTipoEmisorTarjeta").prop('checked');
        var validaOptTipoEmisorCtaBanco = $("#optTipoEmisorCtaBanco").prop('checked');

        if(validaOptTipoEmisorTarjeta === false && validaOptTipoEmisorCtaBanco === false)
        {
            $('#modalMensajes .modal-body').html('<p>Debe seleccionar un tipo de emisor.</p>');
            $('#modalMensajes').modal('show');
            $("#optTipoSi").prop('checked',false);
            $("#optTipoNo").prop('checked',false);
            return false;
        }
        if(Todos === 'No')
        {
            $("#contenedorTarjCta").show();
            if(tipoEmisor === 'Tarjeta')
            {
                $("#contenedorBanco").hide();
            }
            else
            {
                $("#contenedorBanco").show();
            }
        }
        else
        {
            if(tipoEmisor === 'Tarjeta')
            {
                $("#contenedorTarjCta").hide();
                $("#contenedorBanco").hide();
                $('#opcionEmisorTarCta').val(null).trigger('change');
                $('#opcionEmisorBanco').val(null).trigger('change');
            }
            else
            {
                $("#contenedorBanco").hide();
                $('#opcionEmisorBanco').val(null).trigger('change');
            }
        }
    });
    $('#opcionEmisorTarCta,#opcionEmisorBanco').select2({
        multiple    :true
    });
    //Botón agregar emisores
    $(".btnAgregaEmisor").on("click", function () {
        var tipoEmisor                  = $('input:radio[name=optTipoEmisor]:checked').val();
        var tipoTodos                   = $('input:radio[name=optTipoTodos]:checked').val();
        var validaOptTipoEmisorTarjeta  = $("#optTipoEmisorTarjeta").prop('checked');
        var validaOptTipoEmisorCtaBanco = $("#optTipoEmisorCtaBanco").prop('checked');
        var validaOpcionEmisorTarCta    = $("#opcionEmisorTarCta option:selected");
        var validaOpcionEmisorBanco     = $("#opcionEmisorBanco option:selected");
        var opcionEmisorTarCta          = "";
        var opcionEmisorBanco           = "";
        var nameOpcionEmisorTarCta      = "";
        var nameOpcionEmisorBanco       = ""; 

        if(validaOptTipoEmisorTarjeta === false && validaOptTipoEmisorCtaBanco === false)
        {
            $('#modalMensajes .modal-body').html('<p>Debe seleccionar un tipo de emisor.</p>');
            $('#modalMensajes').modal('show');
            return false;
        }
        if (tipoEmisor === "Tarjeta")
        {
            if (tipoTodos === 'No' && validaOpcionEmisorTarCta.length === 0)
            {
                $('#modalMensajes .modal-body').html('<p>Debe seleccionar al menos una Tarjeta.</p>');
                $('#modalMensajes').modal('show');
                return false;
            }
            $("#contenedorTablaEmisor").show();
            if(tipoTodos === 'No')
            {
                $("#opcionEmisorTarCta :selected").each(function(){
                    nameOpcionEmisorTarCta  = $(this).text();
                    opcionEmisorTarCta      = $(this).val();
                    opcionEmisorBanco       = "0";
                    nameOpcionEmisorBanco   = "N/A";
                    var contador = 0;
                    $("#tablaInformacionEmisores tbody tr").each(function (index) {
                        var idTarjeta;
                        $(this).children("td").each(function (index2) {
                            
                            if (index2 == 0)
                            {
                                idTarjeta = $(this).text();
                            }
                            if (idTarjeta === opcionEmisorTarCta)
                            {
                                contador = contador + 1;
                                return false;
                            }
                        });
                    });

                    if (contador !== 1)
                    {
                        $('#tablaInformacionEmisores #tbodyInformacionEmisores').append('<tr><td style="display:none;">'+opcionEmisorTarCta+
                                '</td><td style="display:none;">'+opcionEmisorBanco+'</td><td>'+tipoEmisor+'</td><td>'+nameOpcionEmisorTarCta+
                                '</td><td>'+nameOpcionEmisorBanco +'</td><td class="actions">'+
                                '<button title="Limpiar" type="button" class="btn btn-danger btn btn-sm btnEliminarEmisor" id="btnEliminarEmisor">'+
                                '<i class="fa fa-times-circle-o" aria-hidden="true"></i></button></td></tr>');
                    }
                });
            }
            else
            {   
                $("#tablaInformacionEmisores tbody tr").each(function (index) {
                    var emisor;
                    $(this).children("td").each(function (index2) {
                        
                        if (index2 == 2)
                        {
                            emisor = $(this).text();
                            if (emisor === 'Tarjeta')
                            {
                                $(this).closest('tr').remove();
                            }
                        }
                    });
                });
                $("#opcionEmisorTarCta option").each(function(){
                    nameOpcionEmisorTarCta  = $(this).text();
                    opcionEmisorTarCta      = $(this).val();
                    opcionEmisorBanco       = "0";
                    nameOpcionEmisorBanco   = "N/A";
                    $('#tablaInformacionEmisores #tbodyInformacionEmisores').append('<tr><td style="display:none;">'+opcionEmisorTarCta+
                            '</td><td style="display:none;">'+opcionEmisorBanco+'</td><td>'+tipoEmisor+'</td><td>'+nameOpcionEmisorTarCta+
                            '</td><td>'+nameOpcionEmisorBanco+'</td><td class="actions">'+
                            '<button title="Limpiar" type="button" class="btn btn-danger btn btn-sm btnEliminarEmisor" id="btnEliminarEmisor">'+
                            '<i class="fa fa-times-circle-o" aria-hidden="true"></i></button></td></tr>');
                });
            }
        }
        else
        {
            if (tipoEmisor === "Cuenta Bancaria")
            {
                if (validaOpcionEmisorTarCta.length === 0)
                {
                    $('#modalMensajes .modal-body').html('<p>Debe seleccionar al menos una Cuenta.</p>');
                    $('#modalMensajes').modal('show');
                    return false;
                }
                $("#opcionEmisorTarCta :selected").each(function(){
                    nameOpcionEmisorTarCta  = $(this).text();
                    opcionEmisorTarCta      = $(this).val();
                    
                    if (tipoTodos === 'No' && validaOpcionEmisorBanco.length === 0)
                    {
                        $('#modalMensajes .modal-body').html('<p>Debe seleccionar al menos un banco.</p>');
                        $('#modalMensajes').modal('show');
                        return false;
                    }
                    $("#contenedorTablaEmisor").show();
                    if(tipoTodos === 'No')
                    {            
                        $("#opcionEmisorBanco :selected").each(function(){
                            nameOpcionEmisorBanco   = $(this).text();
                            opcionEmisorBanco       = $(this).val();
                            var contador = 0;
                            $("#tablaInformacionEmisores tbody tr").each(function (index) {
                                var idCuenta, idBanco;
                                $(this).children("td").each(function (index2) {

                                    switch (index2) {
                                        case 0:
                                            idCuenta = $(this).text();
                                            break;
                                        case 1:
                                            idBanco = $(this).text();
                                            break;
                                        default:
                                            break;  
                                    }
                                    if (idCuenta === opcionEmisorTarCta && idBanco === opcionEmisorBanco)
                                    {
                                        contador = contador + 1;
                                        return false;
                                    }
                                });
                            });

                            if (contador !== 1)
                            {
                                $('#tablaInformacionEmisores #tbodyInformacionEmisores').append('<tr><td style="display:none;">'+opcionEmisorTarCta+
                                  '</td><td style="display:none;">'+opcionEmisorBanco+'</td><td>'+tipoEmisor+'</td><td>'+nameOpcionEmisorTarCta+
                                  '</td><td>'+nameOpcionEmisorBanco+'</td><td class="actions">'+
                                  '<button title="Limpiar" type="button" class="btn btn-danger btn btn-sm btnEliminarEmisor" id="btnEliminarEmisor">'+
                                  '<i class="fa fa-times-circle-o" aria-hidden="true"></i></button></td></tr>');
                            }
                        });
                    }
                    else
                    {
                        $("#tablaInformacionEmisores tbody tr").each(function (index) {
                            var emisor;
                            $(this).children("td").each(function (index2) {
                                
                                if (index2 == 3)
                                {
                                    emisor = $(this).text();
                                    if (emisor === nameOpcionEmisorTarCta)
                                    {
                                        $(this).closest('tr').remove();
                                    }
                                }
                            });
                        });

                        $("#opcionEmisorBanco option").each(function(){
                            nameOpcionEmisorBanco   = $(this).text();
                            opcionEmisorBanco       = $(this).val();
                            $('#tablaInformacionEmisores #tbodyInformacionEmisores').append('<tr><td style="display:none;">'+opcionEmisorTarCta+
                              '</td><td style="display:none;">'+opcionEmisorBanco+'</td><td>'+tipoEmisor+'</td><td>'+nameOpcionEmisorTarCta+
                              '</td><td>'+nameOpcionEmisorBanco+'</td><td class="actions">'+
                              '<button title="Limpiar" type="button" class="btn btn-danger btn btn-sm btnEliminarEmisor" id="btnEliminarEmisor">'+
                              '<i class="fa fa-times-circle-o" aria-hidden="true"></i></button></td></tr>');
                        });
                    }
                });
            }
        }
        $("#opcionEmisorTarCta option").each(function () {
            $(this).remove();
        });
        $("#opcionEmisorBanco option").each(function () {
            $(this).remove();
        });
        $("#optTipoEmisorTarjeta").prop('checked',false);
        $("#optTipoEmisorCtaBanco").prop('checked',false);
        $("#optTipoSi").prop('checked',false);
        $("#optTipoNo").prop('checked',false);
        $("#contenedorTarjCta").hide();
        $("#contenedorBanco").hide();
    });
    //Botón eliminar registros de emisores
    $(document).on('click', '.btnEliminarEmisor', function (event) {
        event.preventDefault();
        $(this).closest('tr').remove();
        eliminaCabecera();
    });

    
    /**
     * EliminaCabecera, función que elimina las cabeceras de las tablas dinámicas
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 27-02-2019
     */
    function eliminaCabecera()
    {
        var nFilasEmisor = $("#tablaInformacionEmisores tr").length;
        if (nFilasEmisor === 1)
        {
            $("#contenedorTablaEmisor").hide();
        }

    }
    
    /**
     * Función valida que se ingresen los campos obligatorios.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 17-04-2019
     */
    var forms = document.getElementsByClassName('formPromoMensualidad');
    Array.prototype.filter.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
            if (form.checkValidity() === false)
            {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                $('#modalMensajes .modal-body').html("Por favor llenar campos requeridos.");
                $('#modalMensajes').modal({show: true});
            } else
            {
                form.classList.add('was-validated');
                if ( strFeInicioVigencia == $("#inicio_vigencia").val()  &&  strFeFinVigencia == $("#fin_vigencia").val() )
                {
                    $('#modalMensajes .modal-body').html("Ya existe una Promoción en esa fecha, debe editar la Fecha de Inicio o  Fin de Vigencia");
                    $('#modalMensajes').modal({show: true});
 
                } else if (!validaSectorizacion())
                {
                    $('#modalMensajes .modal-body').html("Debe ingresar al menos una Sectorización.");
                    $('#modalMensajes').modal({show: true});

                } else if (!validaInfEmisores())
                {
                    $('#modalMensajes .modal-body').html("Debe ingresar la información del Débito Bancario.");
                    $('#modalMensajes').modal({show: true});

                } else if (!validaSeleccionPromocion())
                {
                    $('#modalMensajes .modal-body').html("Debe Seleccionar al menos un Tipo de Promoción.");
                    $('#modalMensajes').modal({show: true});

                } else if (!validaPeriodoPromocion('promoMix') || !validaPeriodoPromocion('promoPlan') ||
                    !validaPeriodoPromocion('promoProd') || !validaPeriodoPromocion('promoDescTotal'))
                {
                    $('#modalMensajes .modal-body').html("Debe Seleccionar al menos un Período en la Promoción Seleccionada." +
                        " Si el Tipo de Período es Único, llene el campo de Descuento Único.");
                    $('#modalMensajes').modal({show: true});

                } else if (!validaDescPeriodoVariablePromocion('promoMix') || !validaDescPeriodoVariablePromocion('promoPlan') ||
                    !validaDescPeriodoVariablePromocion('promoProd') || !validaDescPeriodoVariablePromocion('promoDescTotal'))
                {
                    $('#modalMensajes .modal-body').html("Debe llenar el campo de descuento si selecciona un Período Variable");
                    $('#modalMensajes').modal({show: true});

                }else if ($("#observacion_inactivar_vigente").val()==='')
                {
                    $('#modalMensajes .modal-body').html("Debe llenar el campo observación");
                    $('#modalMensajes').modal({show: true});
                }  
                else if (!validaFormatoCodigo())
                {
                    $('#modalMensajes .modal-body').html("El código ingresado no cumple con el formato correcto: Cadena de texto alfanumérica sin espacios.");
                    $('#modalMensajes').modal({show: true});
                }
                else if (!validaCodigoUnico())
                {
                    $('#modalMensajes .modal-body').html("El código ingresado existe para otra promoción, debe ser único.");
                    $('#modalMensajes').modal({show: true});
                }
                else
                {
                    guardarPromocion();
                }

            }
        }, false);
    });
       
    /**
    * validaInfEmisores, función valida que se ingresen los emisores por débito bancario.
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 27-02-2019
    */     
    function validaInfEmisores() {
        var strFormaPago        = "";
        var contFormaPago       = 0;
        var contTabla           = 0;
        $("#forma_pago :selected").each(function(){
            strFormaPago       = $(this).text();
            if (strFormaPago === "DEBITO BANCARIO")
            {
                contFormaPago = contFormaPago + 1;
            }
        });
        if(contFormaPago === 0)
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
    * Valida formato Alfa numérico sin espacios.
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0 05-11-2020
    * @since 1.0
    */
    function validaFormatoCodigo() {
        var strCodigoPromocion= $("#codigo_promocion").val();
        var strEspacios = /\s/;
        var boolEspacios = strEspacios.test(strCodigoPromocion);
        var strAlfaNumerico = /^[a-z0-9]+$/i;
        var boolAlfaNumerico = strAlfaNumerico.test(strCodigoPromocion);
          
        if ((boolEspacios || !boolAlfaNumerico) && strCodigoPromocion!='') 
        {
        
            return false;
        }
        return true;
    }

    /**
    * Valida que código promocional sea único.
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0 05-11-2020
    * @since 1.0
    */
    function validaCodigoUnico() {
        var strCodigoPromocion  = $("#codigo_promocion").val();
        var strIdGrupoPromocion  = $("#idPromocion").val();
        var boolCantidadCodigos = false;
        var strGrupoPromocion   = 'PROM_MENS';
        $.ajax({
            url: urlCodigoUnico,
            method: 'get',
            async: false,
            data: {'strCodigoPromocion': strCodigoPromocion,'strGrupoPromocion':strGrupoPromocion, 'strIdGrupoPromocion':strIdGrupoPromocion},
            success: function (data) {
           
                if (data.intCantidad=='0')
                {
                    boolCantidadCodigos= true;
                }
                else
                {
                    boolCantidadCodigos= false;
                }
               
            },
            error: function () {
                $('#modalMensajes .modal-body').html('<p>Ocurrió un error al consultar si el código es único.</p>');
                $('#modalMensajes').modal('show');
            }
        });
        
        return boolCantidadCodigos;
    }

   /**
    * validaSeleccionPromocion, función valida que se ingrese al menos un tipo de promoción.
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 20-03-2019
    */  
    function validaSeleccionPromocion() {
        if (!$('#check_promoMix').prop('checked') && !$('#check_promoPlan').prop('checked') &&
            !$('#check_promoProd').prop('checked') && !$('#check_promoDescTotal').prop('checked'))
        {
            return false;
        }
        return true;
    }

    function validaPeriodoPromocion(tipoPromo) {
        if (($('#check_' + tipoPromo).prop('checked') && $('input:radio[name=' + tipoPromo + '_indefinida]:checked').val() === 'NO')
            || $('#check_' + tipoPromo).prop('checked') && $('input:checkbox[name=check_' + tipoPromo + ']:checked').val() === 'promoDescTotal')
        {
            if (($('input:radio[name=' + tipoPromo + '_tipoPeriodo]:checked').val() === 'Unico') &&
                $("#" + tipoPromo + "_descUnico").val() === "") {
                return false;
            }
            var checkboxPeriodos = 0;
            $('[name="' + tipoPromo + '_periodo"]:checked').each(function () {
                checkboxPeriodos = checkboxPeriodos + 1;
            });
            if (checkboxPeriodos === 0)
            {
                return false;
            }
        }
        return true;
    }

    function validaDescPeriodoVariablePromocion(tipoPromo) {
        if (($('#check_' + tipoPromo).prop('checked') && $('input:radio[name=' + tipoPromo + '_indefinida]:checked').val() === 'NO' &&
            $('input:radio[name=' + tipoPromo + '_tipoPeriodo]:checked').val() === 'Variable')
            || $('#check_' + tipoPromo).prop('checked') && $('input:checkbox[name=check_' + tipoPromo + ']:checked').val() === 'promoDescTotal' &&
            $('input:radio[name=' + tipoPromo + '_tipoPeriodo]:checked').val() === 'Variable')
        {
            var checkboxDescPeriodoVariable = 0;
            $('[name="' + tipoPromo + '_periodo"]:checked').each(function () {
                var intPeriodo = this.value;
                if ($("#descuento_periodo_" + tipoPromo + intPeriodo).val() === "")
                {
                    checkboxDescPeriodoVariable = checkboxDescPeriodoVariable + 1;
                }
            });
            if (checkboxDescPeriodoVariable !== 0)
            {
                return false;
            }
        }
        return true;
    }
    
   /**
    * guardarPromocion, función que guarda la Promoción que se editó.
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 29-03-2019
    */ 
    function guardarPromocion() {
        $(".spinner_guardarPromocion").show();
        $("#guardarPromociones").attr('disabled', 'disabled');
        var arrayConfGenerales  = [];
        var arrayPromoMix       = [];
        var arrayPromoPlanes    = [];
        var arrayPromoProductos = [];
        var arrayPromoDescTotal = [];
        var arrayEmisores       = [];
        var arraySectorizacion  = [];
        var intIdPromocion      = $('#idPromocion').val();
        
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

        $("#tablaInfSectorizacion tbody tr").each(function (index) {
            var intJurisdiccion, intCanton, intParroquia, strOptSectOltEdif, intSectOltEdif,intSectorizacion;
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
                    case 5:
                        intSectorizacion = $(this).text();
                        break;
                    default:
                        break;
                }
            });

            arraySectorizacion.push({"intSectorizacion": intSectorizacion, "intJurisdiccion": intJurisdiccion, "intCanton": intCanton, 
                                     "intParroquia": intParroquia, "strOptSectOltEdif": strOptSectOltEdif, "intSectOltEdif": intSectOltEdif});
        });
        
        
       /*  @author Hector Lozano <hlozano@telconet.ec>
        *  @version 1.0 1-07-2022 - Se agrega función JSON.stringify para codificar el JSON 
        *                           de Emisores y Sectorización para enviarlos desde la peticion ajax de la interfaz,
        *                           la cual se utilizó para enviar una gran cantidad de información de la misma.  
        */     
        var strNombrePromocion     = $('#nombre_promocion').val();
        var arrayEstadoServicio    = $('#estado_servicio').val();
        var strInicioVigencia      = $('#inicio_vigencia').val();
        var strFinVigencia         = $('#fin_vigencia').val();
        var arrayFormaPago         = $('#forma_pago').val();
        var arrayTipoCliente       = $('#tipo_cliente').val();
        var strPermMinimaCancelVol = $('#selectTiempoPermanencia').val();
        
        arrayConfGenerales      = {arraySectorizacion: JSON.stringify(arraySectorizacion), strNombrePromocion: strNombrePromocion,
                                   arrayEstadoServicio: arrayEstadoServicio, strInicioVigencia: strInicioVigencia,
                                   strFinVigencia: strFinVigencia, arrayFormaPago: arrayFormaPago, arrayEmisores: JSON.stringify(arrayEmisores),
                                   arrayTipoCliente: arrayTipoCliente,strPermMinimaCancelVol:strPermMinimaCancelVol};


        if ($('#check_promoMix').prop('checked'))
        {
            var strTipoPromo         = $('input:checkbox[name=check_promoMix]:checked').val();
            var arrayPlanes          = $('#promoMix_planes').val();
            var arrayProductos       = $('#promoMix_productos').val();
            var strPermanenciaMinima = $('#promoMix_permanenciaMin').val();
            var strMora              = $("input:radio[name=promoMix_mora]:checked").val();
            var intValMoraMix        = strMora === "SI" ? $('#promoMix_diasMora').val() : "";
            var strIndefinida        = $("input:radio[name=promoMix_indefinida]:checked").val();
            var fltDescIndefinido;
            if (strIndefinida === 'SI')
            {
                fltDescIndefinido = $('#promoMix_descIndefinido').val();
            } else
            {
                fltDescIndefinido = "";
                var strTipoPeriodo = $("input:radio[name=promoMix_tipoPeriodo]:checked").val();
                var arrayDescuentoPeriodo = [];
                if (strTipoPeriodo === 'Unico')
                {
                    var fltValorDescuentoUnico = $('#promoMix_descUnico').val();
                    $('[name="promoMix_periodo"]:checked').each(function () {
                        var intPeriodo = this.value;
                        arrayDescuentoPeriodo.push(intPeriodo + '|' + fltValorDescuentoUnico);
                    });
                } else
                {
                    $('[name="promoMix_periodo"]:checked').each(function () {
                        var intPeriodo = this.value;
                        var fltValorDescuentoVariable = $("#descuento_periodo_promoMix" + intPeriodo).val();
                        arrayDescuentoPeriodo.push(intPeriodo + '|' + fltValorDescuentoVariable);
                    });
                }
            }
            arrayPromoMix = {strTipoPromo: strTipoPromo, arrayPlanes: arrayPlanes, arrayProductos: arrayProductos, strMora: strMora,
                             intValMora: intValMoraMix, strPermanenciaMinima: strPermanenciaMinima, strIndefinida: strIndefinida, 
                             fltDescIndefinido: fltDescIndefinido, strTipoPeriodo: strTipoPeriodo, arrayDescuentoPeriodo: arrayDescuentoPeriodo};
        }

        if ($('#check_promoPlan').prop('checked'))
        {
            var strTipoPromoPlan         = $('input:checkbox[name=check_promoPlan]:checked').val();
            var arrayPlanesPlan          = $('#promoPlan_planes').val();
            var strPermanenciaMinimaPlan = $('#promoPlan_permanenciaMin').val();
            var strMoraPlan              = $("input:radio[name=promoPlan_mora]:checked").val();
            var intValMoraPlan           = strMoraPlan === "SI" ? $('#promoPlan_diasMora').val() : ""; 
            var strIndefinidaPlan        = $("input:radio[name=promoPlan_indefinida]:checked").val();
            var fltDescIndefinidoPlan;
            if (strIndefinidaPlan === 'SI')
            {
                fltDescIndefinidoPlan = $('#promoPlan_descIndefinido').val();
            } else
            {
                fltDescIndefinidoPlan = "";
                var strTipoPeriodoPlan = $("input:radio[name=promoPlan_tipoPeriodo]:checked").val();
                var arrayDescuentoPeriodoPlan = [];
                if (strTipoPeriodoPlan === 'Unico')
                {
                    var fltValorDescuentoUnicoPlan = $('#promoPlan_descUnico').val();
                    $('[name="promoPlan_periodo"]:checked').each(function () {
                        var intPeriodoPlan = this.value;
                        arrayDescuentoPeriodoPlan.push(intPeriodoPlan + '|' + fltValorDescuentoUnicoPlan);
                    });
                } else
                {
                    $('[name="promoPlan_periodo"]:checked').each(function () {
                        var intPeriodoPlan = this.value;
                        var fltValorDescuentoVariablePlan = $("#descuento_periodo_promoPlan" + intPeriodoPlan).val();
                        arrayDescuentoPeriodoPlan.push(intPeriodoPlan + '|' + fltValorDescuentoVariablePlan);
                    });
                }
            }
            arrayPromoPlanes = {strTipoPromo: strTipoPromoPlan, arrayPlanes: arrayPlanesPlan, strMora: strMoraPlan,
                                intValMora: intValMoraPlan, strPermanenciaMinima: strPermanenciaMinimaPlan, 
                                strIndefinida: strIndefinidaPlan, fltDescIndefinido: fltDescIndefinidoPlan,
                                strTipoPeriodo: strTipoPeriodoPlan, arrayDescuentoPeriodo: arrayDescuentoPeriodoPlan};
        }

        if ($('#check_promoProd').prop('checked'))
        {
            var strTipoPromoProd         = $('input:checkbox[name=check_promoProd]:checked').val();
            var arrayProductosProd       = $('#promoProd_productos').val();
            var strPermanenciaMinimaProd = $('#promoProd_permanenciaMin').val();
            var strMoraProd              = $("input:radio[name=promoProd_mora]:checked").val();
            var intValMoraProd           = strMoraProd === "SI" ? $('#promoProd_diasMora').val() : "";  
            var strIndefinidaProd        = $("input:radio[name=promoProd_indefinida]:checked").val();
            var fltDescIndefinidoProd;
            if (strIndefinidaProd === 'SI')
            {
                fltDescIndefinidoProd = $('#promoProd_descIndefinido').val();
            } else
            {
                fltDescIndefinidoProd = "";
                var strTipoPeriodoProd = $("input:radio[name=promoProd_tipoPeriodo]:checked").val();
                var arrayDescuentoPeriodoProd = [];
                if (strTipoPeriodoProd === 'Unico')
                {
                    var fltValorDescuentoUnicoProd = $('#promoProd_descUnico').val();
                    $('[name="promoProd_periodo"]:checked').each(function () {
                        var intPeriodoProd = this.value;
                        arrayDescuentoPeriodoProd.push(intPeriodoProd + '|' + fltValorDescuentoUnicoProd);
                    });
                } else
                {
                    $('[name="promoProd_periodo"]:checked').each(function () {
                        var intPeriodoProd = this.value;
                        var fltValorDescuentoVariableProd = $("#descuento_periodo_promoProd" + intPeriodoProd).val();
                        arrayDescuentoPeriodoProd.push(intPeriodoProd + '|' + fltValorDescuentoVariableProd);
                    });
                }
            }
            arrayPromoProductos = {strTipoPromo: strTipoPromoProd, arrayProductos: arrayProductosProd, strMora: strMoraProd,
                                   intValMora: intValMoraProd, strPermanenciaMinima: strPermanenciaMinimaProd,
                                   strIndefinida: strIndefinidaProd, fltDescIndefinido: fltDescIndefinidoProd, 
                                   strTipoPeriodo: strTipoPeriodoProd, arrayDescuentoPeriodo: arrayDescuentoPeriodoProd};
        }

        if ($('#check_promoDescTotal').prop('checked'))
        {
            var strTipoPeriodoDescTotal = $("input:radio[name=promoDescTotal_tipoPeriodo]:checked").val();
            var arrayDescuentoPeriodoDescTotal = [];
            if (strTipoPeriodoDescTotal === 'Unico')
            {
                var fltValorDescuentoUnicoDescTotal = $('#promoDescTotal_descUnico').val();
                $('[name="promoDescTotal_periodo"]:checked').each(function () {
                    var intPeriodoDescTotal = this.value;
                    arrayDescuentoPeriodoDescTotal.push(intPeriodoDescTotal + '|' + fltValorDescuentoUnicoDescTotal);
                });
            } else
            {
                $('[name="promoDescTotal_periodo"]:checked').each(function () {
                    var intPeriodoDescTotal = this.value;
                    var fltValorDescuentoVariableDescTotal = $("#descuento_periodo_promoDescTotal" + intPeriodoDescTotal).val();
                    arrayDescuentoPeriodoDescTotal.push(intPeriodoDescTotal + '|' + fltValorDescuentoVariableDescTotal);

                });
            }
            arrayPromoDescTotal = {strTipoPeriodo: strTipoPeriodoDescTotal, arrayDescuentoPeriodo: arrayDescuentoPeriodoDescTotal};
        }
        
        strMotivoInactivarVigente      = "";
        strObservacionInactivarVigente = "";
        strMensaje                     = "Se actualizó con éxito la Promoción de Mensualidad.";
        strMensajeError                = "No se pudo Actualizar la Promoción. Por favor consulte con el Administrador.";
        
        if (strTipoEdicion==='ED')
        {
            strMotivoInactivarVigente      = $('#motivo_inactivar_vigente').val();
            strObservacionInactivarVigente = $('#observacion_inactivar_vigente').val();
            strMensaje                     = 'Se creó con éxito la nueva promoción';
            strMensajeError                = "No se pudo Crear la Promoción. Por favor consulte con el Administrador.";
            strRespuestaED                 = '';
        }
       
            $.ajax({
                url: url_editarPromocionMensualidad,
                method: 'POST',
                async: false,
                data: {intIdPromocion : intIdPromocion,
                       arrayConfGenerales: arrayConfGenerales,
                       arrayPromoMix: arrayPromoMix,
                       arrayPromoPlanes: arrayPromoPlanes,
                       arrayPromoProductos: arrayPromoProductos,
                       arrayPromoDescTotal: arrayPromoDescTotal,
                       strTipoEdicion:strTipoEdicion,
                       strMotivoInactivarVigente:strMotivoInactivarVigente,
                       strObservacionInactivarVigente:strObservacionInactivarVigente,
                       strCodigoPromocion            : $("#codigo_promocion").val(),
                       strCodigoPromocionIng         : $("#codigo_promocion_ingresado").val()
                       },
                success: function (response) {              
                 

                    if (response === "OK" && strTipoEdicion==='E')
                    {
                        $('#modalMensajes .modal-body').html(strMensaje);
                        $('#modalMensajes').modal({show: true});
                        
                        $(".spinner_guardarPromocion").hide();
                        $("#guardarPromociones").removeAttr("disabled");
                    } else if (response != "OK" )
                    {
                        $('#modalMensajes .modal-body').html(strMensajeError);
                        $('#modalMensajes').modal({show: true});
                    }
                    
                    if (strTipoEdicion==='ED')
                    {
                        strRespuestaED=response;
                    }
                    else
                    {
                      location.reload();
                    }
               
                },
                error: function () {
                    $(".spinner_guardarPromocion").hide();
                    $("#guardarPromociones").removeAttr("disabled");
                    $('#modalMensajes .modal-body').html(strMensajeError);
                    $('#modalMensajes').modal({show: true});
                }
            });
            
            
            if(strTipoEdicion==='ED' && strRespuestaED==='OK')
            {
                $.ajax({
                    url: url_guardarPromocionMensualidad,
                    method: 'POST',
                    data: {arrayConfGenerales: arrayConfGenerales,
                           arrayPromoMix: arrayPromoMix,
                           arrayPromoPlanes: arrayPromoPlanes,
                           arrayPromoProductos: arrayPromoProductos,
                           arrayPromoDescTotal: arrayPromoDescTotal,
                           strTipoEdicion:strTipoEdicion,
                           intIdPromocionOrigen:intIdPromocion,
                           strCodigoPromocion:$("#codigo_promocion").val()},
                    success: function (response) {
                        $(".spinner_guardarPromocion").hide();
                        $("#guardarPromociones").removeAttr("disabled");

                        if (response === "OK")
                        {
                            $('#modalMensajes .modal-body').html('Se Ingresó con éxito la Promoción de Mensualidad.');
                            $('#modalMensajes').modal({show: true});
                        } else
                        {
                            $('#modalMensajes .modal-body').html('No se pudo guardar la Promoción. Por favor consulte con el Administrador.');
                            $('#modalMensajes').modal({show: true});
                        }
                         window.location.href = strUrlListaPromo;
                    },
                    error: function () {
                        $(".spinner_guardarPromocion").hide();
                        $("#guardarPromociones").removeAttr("disabled");
                        $('#modalMensajes .modal-body').html("No se pudo guardar la Promoción. Por favor consulte con el Administrador.");
                        $('#modalMensajes').modal({show: true});
                    }
                });
            }
        
    }
    
    $('#check_sectorizacion').change(function () {
        if (this.checked)
        {
            $("#tablaInfSectorizacion tbody tr").each(function (index) {
                  $(this).closest('tr').remove();
            });
            $('#contenedor_sectorizacion').find('input, select, button').attr('disabled', 'disabled');
            $('#jurisdiccion').val("").trigger('change');
            $('#canton').val("").trigger('change');
            $('#parroquia').val("").trigger('change');
            $('#sector_olt').empty().trigger('change');
            $("#radioSector").prop("checked", true);
            $("#radioTodosJurisd").prop("checked", false);
        } else
        {
            $('#contenedor_sectorizacion').find('input, select, button').removeAttr('disabled');
        }
    });
    
   // validaCabeceraSectorizacion, función que valida las cabeceras de las tablas dinámicas.
    validaCabeceraSectorizacion();  
    function validaCabeceraSectorizacion()
    {
        var nFilasSector = $("#tablaInfSectorizacion tr").length;
        if (nFilasSector === 1)
        {
            $('#check_sectorizacion').click();
        }
    }
    
    function validaSectorizacion() {

        if (!$('#check_sectorizacion').prop('checked')) {
            var cont = 0;
            $("#tablaInfSectorizacion tbody tr").each(function (index) {
                cont++;
            });
            if (cont !== 0)
            {
                return true;
            }

            return false;
        }
        return true;
    }
    
    $(".btnAgregaSectorizacion").on("click", function () {

        var intJurisdiccion   = $('#jurisdiccion option:selected').val();
        var strJurisdiccion   = $('#jurisdiccion option:selected').text();
        var intCanton         = $('#canton option:selected').val() !== "" ? $('#canton option:selected').val() : "0";
        var strCanton         = $('#canton option:selected').text() !== "" ? $('#canton option:selected').text() : "TODOS";
        var intParroquia      = $('#parroquia option:selected').val() !== "" ? $('#parroquia option:selected').val() : "0";
        var strParroquia      = $('#parroquia option:selected').text() !== "" ? $('#parroquia option:selected').text() : "TODOS";
        var strOptSectOltEdif = $('#sect_olt_edif').val() != "" ? $("input:radio[name=radio_sect_olt_edif]:checked").val() : "TODOS";
        var intSectOltEdif    = $('#sect_olt_edif').val() != "" ? ($('#sect_olt_edif').val()) : "0";
        var arraySectOltEdif  = $('#sect_olt_edif').select2('data');
        var textSectOltEdif   = "";

        for (var index in arraySectOltEdif)
        {
            if (arraySectOltEdif[index].hasOwnProperty(['text'])) 
            {
                textSectOltEdif += arraySectOltEdif[index]['text'] + ",";
            }
        }

        if (intJurisdiccion === "" && !$("#radioTodosJurisd").is(':checked'))
        {
            $('#modalMensajes .modal-body').html('<p>Debe seleccionar Jurisdicción.</p>');
            $('#modalMensajes').modal('show');
            return false;
        }

        var contador = 0;
        $("#tablaInfSectorizacion tbody tr").each(function (index) {
            var idJurisdiccion, idCanton, idParroquia;
            $(this).children("td").each(function (index2) {
                switch (index2) {
                    case 0:
                        idJurisdiccion = $(this).text();
                        break;
                    case 1:
                        idCanton = $(this).text();
                        break;
                    case 2:
                        idParroquia = $(this).text();
                        break;
                    default:
                        break;
                }
                if (idJurisdiccion === intJurisdiccion && (idCanton === intCanton || idCanton === "0" || intCanton === "0")
                    && (idParroquia === intParroquia || intParroquia === "0" || idParroquia === "0"))
                {
                    contador = contador + 1;
                    $('#modalMensajes .modal-body').html('<p>La sectorización ya se encuentra incluida entre las preseleccionadas.</p>');
                    $('#modalMensajes').modal('show'); 
                    return false;
                }
            });
        });

        if (contador === 0)
        {
            if($("#radioTodosJurisd").is(':checked'))
            {
                $("#jurisdiccion option").each(function(){
                $("#contTablaSectorizacion").show();
                strJurisdiccion  = $(this).text();
                intJurisdiccion  = $(this).val();
                if (intJurisdiccion !== "")
                {
                    var contador = 0;
                    $("#tablaInfSectorizacion tbody tr").each(function (index) {
                        var idJurisdiccion, idCanton, idParroquia;
                        $(this).children("td").each(function (index2) {
                            switch (index2) {
                                case 0:
                                    idJurisdiccion = $(this).text();
                                    break;
                                case 1:
                                    idCanton = $(this).text();
                                    break;
                                case 2:
                                    idParroquia = $(this).text();
                                    break;
                                default:
                                    break;
                            }
                            if (idJurisdiccion === intJurisdiccion && (idCanton === intCanton || idCanton === "0" || intCanton === "0")
                                && (idParroquia === intParroquia || intParroquia === "0" || idParroquia === "0"))
                            {                
                                contador = contador + 1;                  
                                return false;
                            }
                        });
                    });
                    
                    if (contador === 0)
                    {
                        $('#tablaInfSectorizacion #tbodyInfSectorizacion').append('<tr><td style="display:none;">' + intJurisdiccion +'</td>'+
                                                                                  '<td style="display:none;">' + intCanton + '</td>'+
                                                                                  '<td style="display:none;">' + intParroquia +'</td>'+
                                                                                  '<td style="display:none;">' + strOptSectOltEdif + '</td>'+
                                                                                  '<td style="display:none;">' + intSectOltEdif +'</td>'+
                                                                                  '<td style="display:none;">0</td>'+
                                                                                  '<td>' + strJurisdiccion + '</td><td>' + strCanton + '</td>'+
                                                                                  '<td>' + strParroquia + '</td><td>' + strOptSectOltEdif + '</td>'+
                                                                                  '<td>' + textSectOltEdif.replace(/(^\s*,)|(,\s*$)/g, '') + '</td>'+
                                                                                  '<td class="actions"><button title="Limpiar" type="button"'+
                                                                                  'class="btn btn-danger btn btn-sm btnEliminarSectorizacion"'+
                                                                                  'id="btnEliminarSectorizacion">'+
                                                                                  '<i class="fa fa-times-circle-o" aria-hidden="true"></i>'+
                                                                                  '</button></td></tr>');
                    }
                }
                $('#jurisdiccion').val("").trigger('change');
                $('#canton').val("").trigger('change');
                $('#parroquia').val("").trigger('change');
                $('#sect_olt_edif').empty().trigger('change');
                $("#radioSector").prop("checked", true); 
                });
            }
            else
            {
                $('#tablaInfSectorizacion #tbodyInfSectorizacion').append('<tr><td style="display:none;">' + intJurisdiccion +
                  '</td><td style="display:none;">' + intCanton + '</td><td style="display:none;">' + intParroquia +
                  '</td><td style="display:none;">' + strOptSectOltEdif + '</td><td style="display:none;">' + intSectOltEdif +
                  '</td><td style="display:none;">0</td><td>' + strJurisdiccion + '</td><td>' + strCanton + '</td><td>' + strParroquia + 
                  '</td><td>' + strOptSectOltEdif + '</td><td>' + textSectOltEdif.replace(/(^\s*,)|(,\s*$)/g, '') + '</td><td class="actions">' +
                  '<button title="Limpiar" type="button" class="btn btn-danger btn btn-sm btnEliminarSectorizacion" id="btnEliminarSectorizacion">' +
                  '<i class="fa fa-times-circle-o" aria-hidden="true"></i></button></td></tr>');

                $('#jurisdiccion').val(null).trigger('change');
                $('#canton').val(null).trigger('change');
                $('#parroquia').val(null).trigger('change');
                $('#sect_olt_edif').empty().trigger('change');
                $("#radioSector").prop("checked", true);
            }
        }
    });

    //Botón eliminar registros de Sectorización
    $(document).on('click', '.btnEliminarSectorizacion', function (event) {
        event.preventDefault();
        $(this).closest('tr').remove();
        $('#radioTodosJurisd').prop('checked',false);
        $('#canton').prop('disabled', false);
        $('#parroquia').prop('disabled', false);
        $('#sect_olt_edif').prop('disabled', false);
        $('#jurisdiccion').prop('disabled', false);
    });

});