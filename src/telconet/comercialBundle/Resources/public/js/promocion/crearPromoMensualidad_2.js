$(document).ready(function () {

    //Métodos que inicializa combos de fechas.
    $('#datetimepickerFechaIniVigencia').datetimepicker({
        format: 'YYYY-MM-DD',
        minDate: new Date(),
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

    $('#datetimepickerFechaFinVigencia').datetimepicker({
        format: 'YYYY-MM-DD',
        minDate: new Date(),
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

    $("#datetimepickerFechaIniVigencia").on("dp.change", function (e) {
        $('#datetimepickerFechaFinVigencia').data("DateTimePicker").minDate(e.date);
        $('#fin_vigencia').val('');
    });

    $(document).on('click', '#button-feIniVigencia', function (event) {
        var fechaActual = $.datepicker.formatDate('yy-mm-dd', new Date());
        $('#datetimepickerFechaIniVigencia').data("DateTimePicker").minDate(fechaActual);
    });

    //Método que setea propiedades al seleccionar Promoción de Mix.
    $(".promoMix_contDiasMora").hide();
    $('#check_promoMix').change(function () {
        if (this.checked)
        {
            $('#promoMix_form').find('input, select').removeAttr('disabled');
            $('input:radio[name="promoMix_mora"]').change(function () {
                if ($(this).val() === 'SI')
                {
                    $("#promoMix_diasMora").val('');
                    $(".promoMix_contDiasMora").show();
                } else
                {
                    $("#promoMix_diasMora").val('');
                    $(".promoMix_contDiasMora").hide();
                }
            });
            $('input:radio[name="promoMix_indefinida"]').change(function () {
                if ($(this).val() === 'SI')
                {
                    $("#promoMix_descIndefinido").show();
                    $("#promoMix_cont_tipoPeriodo").hide();
                    $("#promoMix_contPeriodo").hide();
                    $('.input_descuento_periodo').val("");
                    $('#promoMix_descUnico').val("");
                    $('[name="promoMix_periodo"]').prop("checked", false);
                } else
                {
                    $("#promoMix_descIndefinido").val("");
                    $("#promoMix_descIndefinido").hide();
                    $("#promoMix_cont_tipoPeriodo").show();
                    $("#promoMix_contPeriodo").show();
                    $('.input_descuento_periodo').attr('disabled', 'disabled');
                }
            });

            $('input:radio[name="promoMix_tipoPeriodo"]').change(function () {
                if ($(this).val() === 'Unico')
                {
                    $("#promoMix_descUnico").show();
                    $('.input_descuento_periodo').attr('disabled', 'disabled');
                    $('.input_descuento_periodo').val("");
                    $('[name="promoMix_periodo"]').prop("checked", false);
                    $('[name="promoMix_periodo"]').click(function () {
                        $("#descuento_periodo_promoMix" + $(this).val()).val("");
                        $("#descuento_periodo_promoMix" + $(this).val()).attr('disabled', 'disabled');
                    });
                } else
                {
                    $("#promoMix_descUnico").hide();
                    $("#promoMix_descUnico").val("");
                    $('.input_descuento_periodo').attr('disabled', 'disabled');
                    $('.input_descuento_periodo').val("");
                    $('[name="promoMix_periodo"]').prop("checked", false);
                    $('[name="promoMix_periodo"]').click(function () {
                        if (this.checked)
                        {
                            $("#descuento_periodo_promoMix" + $(this).val()).removeAttr('disabled');
                        } else
                        {
                            $("#descuento_periodo_promoMix" + $(this).val()).val("");
                            $("#descuento_periodo_promoMix" + $(this).val()).attr('disabled', 'disabled');
                        }
                    });
                }
            });
        } else
        {
            $('#promoMix_form').find('input, select').attr('disabled', 'disabled');
        }
    });

    //Método que setea propiedades al seleccionar Promoción de Planes.
    $(".promoPlan_contDiasMora").hide();
    $('#check_promoPlan').change(function () {
        if (this.checked)
        {
            $('#promoPlan_form').find('input, select').removeAttr('disabled');
            $('input:radio[name="promoPlan_mora"]').change(function () {
                if ($(this).val() === 'SI')
                {
                    $("#promoPlan_diasMora").val('');
                    $(".promoPlan_contDiasMora").show();
                } else
                {
                    $("#promoPlan_diasMora").val('');
                    $(".promoPlan_contDiasMora").hide();
                }
            });
            $('input:radio[name="promoPlan_indefinida"]').change(function () {
                if ($(this).val() === 'SI')
                {
                    $("#promoPlan_descIndefinido").show();
                    $("#promoPlan_cont_tipoPeriodo").hide();
                    $("#promoPlan_contPeriodo").hide();
                    $('.input_descuento_periodo_plan').val("");
                    $('#promoPlan_descUnico').val("");
                    $('[name="promoPlan_periodo"]').prop("checked", false);
                } else
                {
                    $("#promoPlan_descIndefinido").val("");
                    $("#promoPlan_descIndefinido").hide();
                    $("#promoPlan_cont_tipoPeriodo").show();
                    $("#promoPlan_contPeriodo").show();
                    $('.input_descuento_periodo_plan').attr('disabled', 'disabled');
                }
            });

            $('input:radio[name="promoPlan_tipoPeriodo"]').change(function () {
                if ($(this).val() === 'Unico')
                {
                    $("#promoPlan_descUnico").show();
                    $('.input_descuento_periodo_plan').attr('disabled', 'disabled');
                    $('.input_descuento_periodo_plan').val("");
                    $('[name="promoPlan_periodo"]').prop("checked", false);
                    $('[name="promoPlan_periodo"]').click(function () {
                        $("#descuento_periodo_promoPlan" + $(this).val()).val("");
                        $("#descuento_periodo_promoPlan" + $(this).val()).attr('disabled', 'disabled');
                    });
                } else
                {
                    $("#promoPlan_descUnico").hide();
                    $("#promoPlan_descUnico").val("");
                    $('.input_descuento_periodo_plan').attr('disabled', 'disabled');
                    $('.input_descuento_periodo_plan').val("");
                    $('[name="promoPlan_periodo"]').prop("checked", false);
                    $('[name="promoPlan_periodo"]').click(function () {
                        if (this.checked)
                        {
                            $("#descuento_periodo_promoPlan" + $(this).val()).removeAttr('disabled');
                        } else
                        {
                            $("#descuento_periodo_promoPlan" + $(this).val()).val("");
                            $("#descuento_periodo_promoPlan" + $(this).val()).attr('disabled', 'disabled');
                        }
                    });
                }
            });
        } else
        {
            $('#promoPlan_form').find('input, select').attr('disabled', 'disabled');
        }
    });

    //Método que setea propiedades al seleccionar Promoción de Productos.
    $(".promoProd_contDiasMora").hide();
    $('#check_promoProd').change(function () {
        if (this.checked)
        {
            $('#promoProd_form').find('input, select').removeAttr('disabled');
            $('input:radio[name="promoProd_mora"]').change(function () {
                if ($(this).val() === 'SI')
                {
                    $("#promoProd_diasMora").val('');
                    $(".promoProd_contDiasMora").show();
                } else
                {
                    $("#promoProd_diasMora").val('');
                    $(".promoProd_contDiasMora").hide();
                }
            });
            $('input:radio[name="promoProd_indefinida"]').change(function () {
                if ($(this).val() === 'SI')
                {
                    $("#promoProd_descIndefinido").show();
                    $("#promoProd_cont_tipoPeriodo").hide();
                    $("#promoProd_contPeriodo").hide();
                    $('.input_descuento_periodo_prod').val("");
                    $('#promoProd_descUnico').val("");
                    $('[name="promoProd_periodo"]').prop("checked", false);
                } else
                {
                    $("#promoProd_descIndefinido").val("");
                    $("#promoProd_descIndefinido").hide();
                    $("#promoProd_cont_tipoPeriodo").show();
                    $("#promoProd_contPeriodo").show();
                    $('.input_descuento_periodo_prod').attr('disabled', 'disabled');
                }
            });
            $('input:radio[name="promoProd_tipoPeriodo"]').change(function () {
                if ($(this).val() === 'Unico')
                {
                    $("#promoProd_descUnico").show();
                    $('.input_descuento_periodo_prod').attr('disabled', 'disabled');
                    $('.input_descuento_periodo_prod').val("");
                    $('[name="promoProd_periodo"]').prop("checked", false);
                    $('[name="promoProd_periodo"]').click(function () {
                        $("#descuento_periodo_promoProd" + $(this).val()).val("");
                        $("#descuento_periodo_promoProd" + $(this).val()).attr('disabled', 'disabled');
                    });
                } else
                {
                    $("#promoProd_descUnico").hide();
                    $("#promoProd_descUnico").val("");
                    $('.input_descuento_periodo_prod').attr('disabled', 'disabled');
                    $('.input_descuento_periodo_prod').val("");
                    $('[name="promoProd_periodo"]').prop("checked", false);
                    $('[name="promoProd_periodo"]').click(function () {
                        if (this.checked)
                        {
                            $("#descuento_periodo_promoProd" + $(this).val()).removeAttr('disabled');
                        } else
                        {
                            $("#descuento_periodo_promoProd" + $(this).val()).val("");
                            $("#descuento_periodo_promoProd" + $(this).val()).attr('disabled', 'disabled');
                        }
                    });
                }
            });
        } else
        {
            $('#promoProd_form').find('input, select').attr('disabled', 'disabled');
        }
    });

    //Método que setea propiedades al seleccionar Promoción de Desc.Total.
    $('#check_promoDescTotal').change(function () {
        if (this.checked)
        {
            $('#promoDescTotal_form').find('input, select').removeAttr('disabled');
            $('.input_descuento_periodo_total').attr('disabled', 'disabled');

            $('input:radio[name="promoDescTotal_tipoPeriodo"]').change(function () {
                if ($(this).val() === 'Unico')
                {
                    $("#promoDescTotal_descUnico").show();
                    $('.input_descuento_periodo_total').attr('disabled', 'disabled');
                    $('.input_descuento_periodo_total').val("");
                    $('[name="promoDescTotal_periodo"]').prop("checked", false);
                    $('[name="promoDescTotal_periodo"]').click(function () {
                        $("#descuento_periodo_promoDescTotal" + $(this).val()).val("");
                        $("#descuento_periodo_promoDescTotal" + $(this).val()).attr('disabled', 'disabled');
                    });
                } else
                {
                    $("#promoDescTotal_descUnico").hide();
                    $("#promoDescTotal_descUnico").val("");
                    $('.input_descuento_periodo_total').attr('disabled', 'disabled');
                    $('.input_descuento_periodo_total').val("");
                    $('[name="promoDescTotal_periodo"]').prop("checked", false);
                    $('[name="promoDescTotal_periodo"]').click(function () {
                        if (this.checked)
                        {
                            $("#descuento_periodo_promoDescTotal" + $(this).val()).removeAttr('disabled');
                        } else
                        {
                            $("#descuento_periodo_promoDescTotal" + $(this).val()).val("");
                            $("#descuento_periodo_promoDescTotal" + $(this).val()).attr('disabled', 'disabled');
                        }
                    });
                }
            });
        } else
        {
            $('#promoDescTotal_form').find('input, select').attr('disabled', 'disabled');
        }
    });

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
    //RadioButton Tipo de Emisores
    $('input:radio[name="optTipoEmisor"]').change(function() {
        $("#optTipoSi").prop('checked',true);
        obtenerTipoEmisor($(this).val());
    });
    
    /**
    * obtenerTipoEmisor, función otiene los tipo de emisores por Cuentas
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
                $('#modalMensajes .modal-body').html('<p>No se pudeden cargar las opciones por tipo de Emisor.</p>');
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
                            
                            if(index2 == 0)
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
     * @author Héctor Lozano <hlozano@telconet.ec>
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
    
    // botón que agrega dinámicamente la sectorización a la promoción de mensualidad.
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
                  '</td><td>' + strJurisdiccion + '</td><td>' + strCanton + '</td><td>' + strParroquia + '<td>' + strOptSectOltEdif + '</td>' +
                  '</td><td>' + textSectOltEdif.replace(/(^\s*,)|(,\s*$)/g, '') + '</td><td class="actions">' +
                  '<button title="Limpiar" type="button" class="btn btn-danger btn btn-sm btnEliminarSectorizacion" id="btnEliminarSectorizacion">' +
                  '<i class="fa fa-times-circle-o" aria-hidden="true"></i></button></td></tr>');

                $('#jurisdiccion').val("").trigger('change');
                $('#canton').val("").trigger('change');
                $('#parroquia').val("").trigger('change');
                $('#sect_olt_edif').empty().trigger('change');
                $("#radioSector").prop("checked", true);
            }
        }
    });

    //Botón para eliminar registros de Sectorización
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