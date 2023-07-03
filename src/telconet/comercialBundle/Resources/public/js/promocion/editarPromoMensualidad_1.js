$(document).ready(function () {

    //Métodos que limpian campos del formulario.
    $("#formularioPromocionMensual").trigger("reset");
    $("#limpiarPromocion").click(function () {
        $("#formularioPromocionMensual").trigger("reset");
        location.reload();
    });
    $(".spinner_guardarPromocion,.spinner_canton,.spinner_parroquia,.spinner_sect_olt_edif").hide();
    $("#promoMix_cont_tipoPeriodo, #promoPlan_cont_tipoPeriodo,#promoProd_cont_tipoPeriodo").hide();
    $("#promoMix_contPeriodo, #promoPlan_contPeriodo,#promoProd_contPeriodo").hide();

    $("#sect_olt_edif").select2({placeholder: "Seleccionar",multiple: true});
    $('#estado_servicio').select2({placeholder: "Seleccionar", multiple: true});
    $('#forma_pago').select2({placeholder: "Seleccionar", multiple: true});
    $('#tipo_cliente').select2({placeholder: "Seleccionar"});
    var arrayTiposClientes = strTipoCliente.split(',');
    $('#tipo_cliente').val(arrayTiposClientes).trigger('change');
    $('#promoMix_permanenciaMin, #promoPlan_permanenciaMin,#promoProd_permanenciaMin,#selectTiempoPermanencia').select2({placeholder: "Seleccionar", allowClear: true});
    $('#promoMix_productos,#promoProd_productos').select2({placeholder: "Seleccionar", multiple: true});
    $('#promoMix_planes, #promoPlan_planes').select2({placeholder: "Seleccionar", multiple: true});
    $('#opcionEmisorTarCta').select2({multiple: true});
    $('#opcionEmisorBanco').select2({multiple: true});
       
    
    
    $('#check_tiempoPermanencia').change(function () {
        if (this.checked)
        {
            $('#selectTiempoPermanencia').removeAttr('disabled');
        } else
        {
            $('#selectTiempoPermanencia').val('').trigger('change');
            $('#selectTiempoPermanencia').attr('disabled', 'disabled');
        }
    });
    
     $('#tipo_cliente').change(function(e) {
        var strTipoCliente        = "";
        var contTipoCliente            = 0;
        $("#tipo_cliente :selected").each(function(){
            strTipoCliente       = $(this).text();
            if (strTipoCliente === "Nuevo")
            {
                contTipoCliente = contTipoCliente + 1;
            }
        });
        if(contTipoCliente === 0)
        {
            $('#check_tiempoPermanencia').prop('checked', false );
            $('#check_tiempoPermanencia').attr('disabled', 'disabled');
            $('#selectTiempoPermanencia').attr('disabled', 'disabled');
            $('#selectTiempoPermanencia').val('').trigger('change');
        }
        else
        {
            $('#check_tiempoPermanencia').removeAttr('disabled');
        }
    });


    //Métodos que inicializa combos de fechas.
    $('#datetimepickerFechaIniVigencia').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: true,
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
     var fechaActual = $.datepicker.formatDate('yy-mm-dd', new Date());
        $('#datetimepickerFechaIniVigencia').data("DateTimePicker").minDate(fechaActual);

    $("#datetimepickerFechaIniVigencia").on("dp.change", function (e) {
        $('#datetimepickerFechaFinVigencia').data("DateTimePicker").minDate(e.date);
        $('#fin_vigencia').val('');
    });

    $(document).on('click', '#button-feIniVigencia', function (event) {
        var fechaActual = $.datepicker.formatDate('yy-mm-dd', new Date());
        $('#datetimepickerFechaIniVigencia').data("DateTimePicker").minDate(fechaActual);
    });
    
    $("#promoMix_descIndefinido,#promoMix_descUnico,#promoPlan_descIndefinido,#promoPlan_descUnico,#promoProd_descIndefinido" +
        ",#promoProd_descUnico,#promoDescTotal_descUnico").blur(function () {
        var expesion_regular = /^\d+(\.\d+)?$/;
        if (!expesion_regular.test($(this).val()))
        {
            $(this).val('');
        }
    });

    //Función que valida los campos cuando la promoción es indefinida.
    function validaCamposPromoIndefinidaSi(tipoPromo) 
    {
        $("#" + tipoPromo + "_descIndefinido").show();
        $("#" + tipoPromo + "_cont_tipoPeriodo").hide();
        $("#" + tipoPromo + "_contPeriodo").hide();
        $("#" + tipoPromo + "_descUnico").val("");
        $("[name='" + tipoPromo + "_periodo']").prop("checked", false);
    }

    //Función que valida los campos cuando la promoción no es indefinida.
    function validaCamposPromoIndefinidaNo(tipoPromo)
    {
        $("#" + tipoPromo + "_descIndefinido").val("");
        $("#" + tipoPromo + "_descIndefinido").hide();
        $("#" + tipoPromo + "_cont_tipoPeriodo").show();
        $("#" + tipoPromo + "_contPeriodo").show();
    }
    
    //Función que valida campos del tipo de período único.
    function validaCamposPeriodoUnico(tipoPromo)
    {
        $("#" + tipoPromo + "_descUnico").show();
        $("[name='" + tipoPromo + "_periodo']").prop("checked", false);
        $("[name='" + tipoPromo + "_periodo']").click(function () {
            $("#descuento_periodo_" + tipoPromo + $(this).val()).val("");
            $("#descuento_periodo_" + tipoPromo + $(this).val()).attr('disabled', 'disabled');
        });
    }

    //Función que valida campos del tipo de período variable.
    function validaCamposPeriodoVariable(tipoPromo) 
    {
        $("#" + tipoPromo + "_descUnico").hide();
        $("#" + tipoPromo + "_descUnico").val("");
        $("[name='" + tipoPromo + "_periodo']").prop("checked", false);
        $("[name='" + tipoPromo + "_periodo']").click(function () {
            if (this.checked)
            {
                $("#descuento_periodo_" + tipoPromo + $(this).val()).removeAttr('disabled');
            } else
            {
                $("#descuento_periodo_" + tipoPromo + $(this).val()).val("");
                $("#descuento_periodo_" + tipoPromo + $(this).val()).attr('disabled', 'disabled');
            }
        });
    }

    //Función que valida si esta seleccionada la promoción Mix.
    $(".promoMix_contDiasMora").hide();
    function validaPromoMix() 
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
                validaCamposPromoIndefinidaSi("promoMix");
                $('.input_descuento_periodo').val("");

            } else
            {
                validaCamposPromoIndefinidaNo("promoMix");
                $('.input_descuento_periodo').attr('disabled', 'disabled');
            }
        });
        $('input:radio[name="promoMix_tipoPeriodo"]').change(function () {
            if ($(this).val() === 'Unico')
            {
                validaCamposPeriodoUnico("promoMix");
                $('.input_descuento_periodo').attr('disabled', 'disabled');
                $('.input_descuento_periodo').val("");
            } else
            {
                $('.input_descuento_periodo').attr('disabled', 'disabled');
                $('.input_descuento_periodo').val("");
                validaCamposPeriodoVariable("promoMix");
            }
        });
    }
    
    //Función que valida si esta seleccionada la promoción de Planes.
    $(".promoPlan_contDiasMora").hide();
    function validaPromoPlan() 
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
                validaCamposPromoIndefinidaSi("promoPlan");
                $('.input_descuento_periodo_plan').val("");
            } else
            {
                validaCamposPromoIndefinidaNo("promoPlan");
                $('.input_descuento_periodo_plan').attr('disabled', 'disabled');
            }
        });
        $('input:radio[name="promoPlan_tipoPeriodo"]').change(function () {
            if ($(this).val() === 'Unico')
            {
                validaCamposPeriodoUnico("promoPlan");
                $('.input_descuento_periodo_plan').attr('disabled', 'disabled');
                $('.input_descuento_periodo_plan').val("");
            } else
            {
                $('.input_descuento_periodo_plan').attr('disabled', 'disabled');
                $('.input_descuento_periodo_plan').val("");
                validaCamposPeriodoVariable("promoPlan");
            }
        });
    }
    
    //Función que valida si esta seleccionada la promoción de Productos.
    $(".promoProd_contDiasMora").hide();
    function validaPromoProd()
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
                validaCamposPromoIndefinidaSi("promoProd");
                $('.input_descuento_periodo_prod').val("");
            } else
            {
                validaCamposPromoIndefinidaNo("promoProd");
                $('.input_descuento_periodo_prod').attr('disabled', 'disabled');
            }
        });
        $('input:radio[name="promoProd_tipoPeriodo"]').change(function () {
            if ($(this).val() === 'Unico')
            {
                validaCamposPeriodoUnico("promoProd");
                $('.input_descuento_periodo_prod').attr('disabled', 'disabled');
                $('.input_descuento_periodo_prod').val("");
            } else
            {
                $('.input_descuento_periodo_prod').attr('disabled', 'disabled');
                $('.input_descuento_periodo_prod').val("");
                validaCamposPeriodoVariable("promoProd");
            }
        });
    }
    
    //Función que valida si esta seleccionada la promoción de Desc.Total.
    function validaPromoDescTotal()
    {
        $('#promoDescTotal_form').find('input, select').removeAttr('disabled');
        $('.input_descuento_periodo_total').attr('disabled', 'disabled');
        $('input:radio[name="promoDescTotal_tipoPeriodo"]').change(function () {
            if ($(this).val() === 'Unico')
            {
                validaCamposPeriodoUnico("promoDescTotal");
                $('.input_descuento_periodo_total').attr('disabled', 'disabled');
                $('.input_descuento_periodo_total').val("");
            } else
            {
                $('.input_descuento_periodo_total').attr('disabled', 'disabled');
                $('.input_descuento_periodo_total').val("");
                validaCamposPeriodoVariable("promoDescTotal");
            }
        });
    }

    //Método que setea propiedades al seleccionar Promoción de Mix.
    if (checkPromoMix === "checked")
    {
        $('[name="check_promoMix"]').attr('checked', true);
        strTieneMoraMix === "SI" ? ($("#promoMix_moraSi").prop("checked", true), $("#promoMix_diasMora").val(intValMoraMix),
                                    $(".promoMix_contDiasMora").show()) : ($("#promoMix_moraNo").prop("checked", true),
                                    $("#promoMix_diasMora").val(), $(".promoMix_contDiasMora").hide());
        if (strPromoIndefinidaMix === "SI")
        {
            $("#promoMix_indefinidaSi").prop("checked", true);
            $("#promoMix_descIndefinido").val(strDescIndefinidoMix);
        } else
        {
            $("#promoMix_indefinidaNo").prop("checked", true);
            validaCamposPromoIndefinidaNo("promoMix");
            strTipoPeriodoMix === "Unico" ? $("#promoMix_tipoPeriodoUnico").prop("checked", true) :
                ($("#promoMix_tipoPeriodoVariable").prop("checked", true), $("#promoMix_descUnico").hide(), $("#promoMix_descUnico").val(""));
        }
        validaPromoMix();
    } else
    {
        $('#promoMix_form').find('input, select').attr('disabled', 'disabled');
    }

    //Método que setea propiedades al seleccionar Promoción de Planes.
    if (checkPromoPlan === "checked")
    {
        $('[name="check_promoPlan"]').attr('checked', true);
        strTieneMoraPlan === "SI" ? ($("#promoPlan_moraSi").prop("checked", true), $("#promoPlan_diasMora").val(intValMoraPlan),
                                     $(".promoPlan_contDiasMora").show()) : ($("#promoPlan_moraNo").prop("checked", true),
                                     $("#promoPlan_diasMora").val(), $(".promoPlan_contDiasMora").hide());
        if (strPromoIndefinidaPlan === "SI")
        {
            $("#promoPlan_indefinidaSi").prop("checked", true);
            $("#promoPlan_descIndefinido").val(strDescIndefinidoPlan);
        } else
        {
            $("#promoPlan_indefinidaNo").prop("checked", true);
            validaCamposPromoIndefinidaNo("promoPlan");
            strTipoPeriodoPlan === "Unico" ? $("#promoPlan_tipoPeriodoUnico").prop("checked", true) :
                ($("#promoPlan_tipoPeriodoVariable").prop("checked", true), $("#promoPlan_descUnico").hide(), $("#promoPlan_descUnico").val(""));
        }
        validaPromoPlan();
    } else
    {
        $('#promoPlan_form').find('input, select').attr('disabled', 'disabled');
    }

    //Método que setea propiedades al seleccionar Promoción de Productos.
    if (checkPromoProd === "checked")
    {
        $('[name="check_promoProd"]').attr('checked', true);
        strTieneMoraProd === "SI" ? ($("#promoProd_moraSi").prop("checked", true), $("#promoProd_diasMora").val(intValMoraProd),
                                     $(".promoProd_contDiasMora").show()) : ($("#promoProd_moraNo").prop("checked", true),
                                     $("#promoProd_diasMora").val(), $(".promoProd_contDiasMora").hide());
        if (strPromoIndefinidaProd === "SI")
        {
            $("#promoProd_indefinidaSi").prop("checked", true);
            $("#promoProd_descIndefinido").val(strDescIndefinidoProd);
        } else
        {
            $("#promoProd_indefinidaNo").prop("checked", true);
            validaCamposPromoIndefinidaNo("promoProd");
            strTipoPeriodoProd === "Unico" ? $("#promoProd_tipoPeriodoUnico").prop("checked", true) :
                ($("#promoProd_tipoPeriodoVariable").prop("checked", true), $("#promoProd_descUnico").hide(), $("#promoProd_descUnico").val(""));
        }
        validaPromoProd();
    } else
    {
        $('#promoProd_form').find('input, select').attr('disabled', 'disabled');
    }

    //Método que setea propiedades al seleccionar Promoción de Desc.Total.
    if (checkPromoDescTotal === "checked")
    {
        $('[name="check_promoDescTotal"]').attr('checked', true);
        strTipoPeriodoDescTotal === "Unico" ? $("#promoDescTotal_tipoPeriodoUnico").prop("checked", true) :
            ($("#promoDescTotal_tipoPeriodoVariable").prop("checked", true), $("#promoDescTotal_descUnico").hide(),
                $("#promoDescTotal_descUnico").val(""));
        validaPromoDescTotal();
    } else
    {
        $('#promoDescTotal_form').find('input, select').attr('disabled', 'disabled');
    }


    $('#check_promoMix').change(function () {
        this.checked ? (validaPromoMix(), $('.input_descuento_periodo').attr('disabled', 'disabled')) :
            $('#promoMix_form').find('input, select').attr('disabled', 'disabled');
    });

    $('#check_promoPlan').change(function () {
        this.checked ? (validaPromoPlan(), $('.input_descuento_periodo_plan').attr('disabled', 'disabled')) :
            $('#promoPlan_form').find('input, select').attr('disabled', 'disabled');
    });

    $('#check_promoProd').change(function () {
        this.checked ? (validaPromoProd(), $('.input_descuento_periodo_prod').attr('disabled', 'disabled')) :
            $('#promoProd_form').find('input, select').attr('disabled', 'disabled');
    });

    $('#check_promoDescTotal').change(function () {
        this.checked ? (validaPromoDescTotal(), $('.input_descuento_periodo_total').attr('disabled', 'disabled')) :
            $('#promoDescTotal_form').find('input, select').attr('disabled', 'disabled');
    });

    //Obtiene Períodos para los tipo de promociones(Promo Mix, Promo Planes, Promo Productos, Promo Desc.Total).
    $.ajax({
        url: urlGetPeriodos,
        method: 'GET',
        success: function (data) {
            var cont = 0;
            $.each(data.periodos, function (id, registro) {
                $('#promoMix_checkPeriodos').append('<div class="form-check form-check-inline descuento_periodo_promoMix">' +
                    '<label class="form-check-label">' +
                    registro.id + '</label>&nbsp;<input class="form-check-input" type="checkbox" name="promoMix_periodo" id="check_periodo' +
                    registro.id + '" value="' +
                    registro.id + '"><input class="form-control form-control-sm input_descuento_periodo" type="text" id="descuento_periodo_promoMix' +
                    registro.id + '" onblur="validaDecimalDescVariable(\'promoMix' + registro.id + '\')"></div>');

                $('#promoPlan_checkPeriodos').append('<div class="form-check form-check-inline descuento_periodo_promoPlan">' +
                    '<label class="form-check-label">' + registro.id +
                    '</label>&nbsp;<input class="form-check-input" name="promoPlan_periodo" type="checkbox" id="check_periodo' + registro.id +
                    '" value="' + registro.id + '">' +
                    '<input class=" form-control form-control-sm input_descuento_periodo_plan" type="text" id="descuento_periodo_promoPlan' +
                    registro.id + '" onblur="validaDecimalDescVariable(\'promoPlan' + registro.id + '\')"></div>');

                $('#promoProd_checkPeriodos').append('<div class="form-check form-check-inline descuento_periodo_promoProd">' +
                    '<label class="form-check-label">' + registro.id +
                    '</label>&nbsp;<input class="form-check-input" name="promoProd_periodo" type="checkbox" id="check_periodo' + registro.id +
                    '" value="' + registro.id + '">' +
                    '<input class=" form-control form-control-sm input_descuento_periodo_prod" type="text" id="descuento_periodo_promoProd' +
                    registro.id + '" onblur="validaDecimalDescVariable(\'promoProd' + registro.id + '\')"></div>');

                $('#promoDescTotal_checkPeriodos').append('<div class="form-check form-check-inline descuento_periodo_promoDescTotal">' +
                    '<label class="form-check-label">' + registro.id +
                    '</label>&nbsp;' +
                    '<input class="form-check-input" name="promoDescTotal_periodo" type="checkbox" id="check_periodo' +
                    registro.id + '" value="' + registro.id + '">' +
                    '<input class=" form-control form-control-sm input_descuento_periodo_total" type="text" id="descuento_periodo_promoDescTotal' +
                    registro.id + '" disabled="disabled" onblur="validaDecimalDescVariable(\'promoDescTotal' + registro.id + '\')"></div>');
                cont++;
                if (cont === 4)
                {
                    cont = 0;
                    $('#promoMix_checkPeriodos,#promoPlan_checkPeriodos,#promoProd_checkPeriodos,#promoDescTotal_checkPeriodos').append('<br/>');
                }
            });

            if (checkPromoMix === "checked")
            {
                validaDescPeriodoUnicoVariableEdit(strPeriodoDescuentosMix, strTipoPeriodoMix, "input_descuento_periodo", "promoMix");
            }
            if (checkPromoPlan === "checked")
            {
                validaDescPeriodoUnicoVariableEdit(strPeriodoDescuentosPlan, strTipoPeriodoPlan, "input_descuento_periodo_plan", "promoPlan");
            }
            if (checkPromoProd === "checked")
            {
                validaDescPeriodoUnicoVariableEdit(strPeriodoDescuentosProd, strTipoPeriodoProd, "input_descuento_periodo_prod", "promoProd");
            }
            if (checkPromoDescTotal === "checked")
            {
                validaDescPeriodoUnicoVariableEdit(strPeriodoDescuentosDescTotal, strTipoPeriodoDescTotal,
                                                   "input_descuento_periodo_total", "promoDescTotal");
            }
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Periodos. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });

    //Función que valida campos cuando selecciona período Único o Variable.
    function validaDescPeriodoUnicoVariableEdit(periodoDescPromo, tipoPeriodoPromo, inputDescuento, tipoPromo) {
        $("." + inputDescuento).attr('disabled', 'disabled');
        var arrayPeriodosDescuentos = periodoDescPromo.split(",");
        var fltDescuento = "";
        for (var i = 0; i < arrayPeriodosDescuentos.length; i++)
        {
            var arrayValoresPeriodoDescuento = "";
            arrayValoresPeriodoDescuento = arrayPeriodosDescuentos[i].split("|");

            if (tipoPeriodoPromo === "Unico")
            {
                $("#" + tipoPromo + "_checkPeriodos #check_periodo" + arrayValoresPeriodoDescuento[0]).prop("checked", true);
                fltDescuento = arrayValoresPeriodoDescuento[1];
            }
            if (tipoPeriodoPromo === "Variable")
            {
                $("#" + tipoPromo + "_checkPeriodos #check_periodo" + arrayValoresPeriodoDescuento[0]).prop("checked", true);
                $("#descuento_periodo_" + tipoPromo + arrayValoresPeriodoDescuento[0]).val(arrayValoresPeriodoDescuento[1]);
                $("#descuento_periodo_" + tipoPromo + arrayValoresPeriodoDescuento[0]).removeAttr('disabled');
            }
        }
        $("#" + tipoPromo + "_descUnico").val(fltDescuento).trigger('change');
        if (tipoPeriodoPromo === "Variable")
        {
            $("[name='" + tipoPromo + "_periodo']").click(function () {
                if (this.checked)
                {
                    $("#descuento_periodo_" + tipoPromo + $(this).val()).removeAttr('disabled');
                } else
                {
                    $("#descuento_periodo_" + tipoPromo + $(this).val()).val("");
                    $("#descuento_periodo_" + tipoPromo + $(this).val()).attr('disabled', 'disabled');
                }
            });
        }
    }

    //Obtiene Estados de Servicios para la promoción de mensualidad.
    $.ajax({
        url: urlGetEstadosServ,
        method: 'GET',
        success: function (data) {
            $.each(data.estados_servicios, function (id, registro) {
                $("#estado_servicio").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
            var arrayEstadosServicios = strEstadosServicios.split(',');
            $('#estado_servicio').val(arrayEstadosServicios).trigger('change');
            $(".spinner_estadoServ").hide();
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Estados de Servicio. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
    
    //Obtiene las jurisdicciones para la sectorización de la promoción de mensualidad.
    $.ajax({
        url: url_getJurisdicciones,
        method: 'GET',
        success: function (data) {
            $(".spinner_jurisdiccion").hide();
            $.each(data.jurisdicciones, function (id, registro) {
                $("#jurisdiccion").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar las Jurisdicciones. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
   
   //Obtiene los cantones para la sectorización de la promoción de mensualidad.
    $('#jurisdiccion').select2({placeholder: "Seleccionar"}).on('select2:select', function (e) {
        $('#canton').val("").trigger('change');
        $('#parroquia').val("").trigger('change');
        $('#sect_olt_edif').empty().trigger('change');
        $("#radioSector").prop("checked", true);
        var idjurisdiccion = e.params.data.id;
        $(".spinner_canton").show();
        $.ajax({
            url: url_getCantones,
            method: 'GET',
            data: {'idjurisdiccion': idjurisdiccion},
            success: function (data) {
                $(".spinner_canton").hide();
                $("#canton option").each(function () {
                    $(this).remove();
                    $("#canton").append('<option></option>');
                });
                $.each(data.cantones, function (id, registro) {
                    $("#canton").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los Cantones. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    });

    //Obtiene las parroquias para la sectorización de la promoción de mensualidad.
    $('#canton').select2({placeholder: "Seleccionar"}).on('select2:select', function (e) {
        $('#parroquia').val("").trigger('change');
        $('#sect_olt_edif').empty().trigger('change');
        $("#radioSector").prop("checked", true);
        var idcanton = e.params.data.id;
        $(".spinner_parroquia").show();
        $.ajax({
            url: url_getParroquias,
            method: 'GET',
            data: {'idcanton': idcanton},
            success: function (data) {
                $(".spinner_parroquia").hide();
                $("#parroquia option").each(function () {
                    $(this).remove();
                    $("#parroquia").append('<option></option>');
                });
                $.each(data.parroquias, function (id, registro) {
                    $("#parroquia").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar las Parroquias. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    });
    
    //Obtiene los Sectores/Olt's/Edificios para la sectorización de la promoción de mensualidad.
    $('#parroquia').select2({placeholder: "Seleccionar"}).on('select2:select', function (e) {
        $('#sect_olt_edif').empty().trigger('change');
        $("#radioSector").prop("checked", true);
        var codParroquia = e.params.data.id;
        $('#cod_parroquia').val(codParroquia);
        var valorSectOltEdif = $('input:radio[name=radio_sect_olt_edif]:checked').val();

        if (valorSectOltEdif === 'sector') 
        {
            obtenerSector(codParroquia);
        }
        if (valorSectOltEdif === 'olt') 
        {
            obtenerOlt(codParroquia);
        }
        if (valorSectOltEdif === 'edificio')
        {
            obtenerEdificio(codParroquia);
        }
    });    
    
    function obtenerSector(codParroquia)
    {
        $('#sect_olt_edif').empty().trigger('change');
        var idparroquia = codParroquia;
        $(".spinner_sect_olt_edif").show();
        $.ajax({
            url: url_getSectores,
            method: 'GET',
            data: {'idparroquia': idparroquia},
            success: function (data) {
                $(".spinner_sect_olt_edif").hide();
                $("#sect_olt_edif option").each(function () {
                    $(this).remove();
                    $("#sect_olt_edif").append('<option></option>');
                });
                $.each(data.sectores, function (id, registro) {
                    $("#sect_olt_edif").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los Sectores. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    }

    function obtenerOlt(codParroquia)
    {
        $('#sect_olt_edif').empty().trigger('change');
        var idparroquia = codParroquia;
        $(".spinner_sect_olt_edif").show();
        $.ajax({
            url: url_getOlts,
            method: 'GET',
            data: {'intIdparroquia': idparroquia},
            success: function (data) {
                $(".spinner_sect_olt_edif").hide();
                $("#sect_olt_edif option").each(function () {
                    $(this).remove();
                    $("#sect_olt_edif").append('<option></option>');
                });
                $.each(data.olts, function (id, registro) {
                    $("#sect_olt_edif").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los Olt's. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    }
    
    function obtenerEdificio(codParroquia)
    {
        $('#sect_olt_edif').empty().trigger('change');
        var idparroquia = codParroquia;
        $(".spinner_sect_olt_edif").show();
        $.ajax({
            url: url_getEdificios,
            method: 'GET',
            data: {'intIdparroquia': idparroquia},
            success: function (data) {
                $(".spinner_sect_olt_edif").hide();
                $("#sect_olt_edif option").each(function () {
                    $(this).remove();
                    $("#sect_olt_edif").append('<option></option>');
                });
                $.each(data.edificios, function (id, registro) {
                    $("#sect_olt_edif").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los Edificios. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    }
    
    $('input:radio[name="radio_sect_olt_edif"]').change(function () {
        if ($(this).val() === 'sector')
        {
            obtenerSector($('#cod_parroquia').val());
            
        } else if ($(this).val() === 'olt')
        {
            obtenerOlt($('#cod_parroquia').val());

        } else
        {
            obtenerEdificio($('#cod_parroquia').val());
        }
    });

    //Obtiene las formas de pagos para la promoción de mensualidad.
    $.ajax({
        url: urlGetFormasPago,
        method: 'GET',
        success: function (data) {
            $.each(data.formas_de_pago, function (id, registro) {
                $("#forma_pago").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
            var arrayFormasPagos = strIdFormasPagos.split(',');
            $('#forma_pago').val(arrayFormasPagos).trigger('change');
            $(".spinner_formaPago").hide();
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar las Formas de Pago. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
    //Obtiene los tipos de cliente para la promoción de mensualidad.
    $.ajax({
        url: urlGetTipoClientes,
        method: 'GET',
        success: function (data) { 
            $.each(data.tipo_cliente, function (id, registro) {
                $("#tipo_cliente").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
            var arrayTipoCliente = strTipoCliente.split(',');
            $('#tipo_cliente').val(arrayTipoCliente).trigger('change');
            $(".spinner_tipoCliente").hide();
            if ( arrayTipoCliente.includes( "Nuevo" ) )
            {
                $('#check_tiempoPermanencia').removeAttr('disabled');
                $('#selectTiempoPermanencia').attr('disabled', 'disabled');
            }
            else
            {
                $('#check_tiempoPermanencia').prop('checked', false );
                $('#selectTiempoPermanencia').attr('disabled', 'disabled');
                $('#check_tiempoPermanencia').attr('disabled', 'disabled');
            }
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Tipos de Clientes. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });

    
    //Obtiene las permanencias mínimas para calculo de procesos de Cancelacion Voluntaria.
    $.ajax({
        url: urlGetPermanenciaPromoCancelVol,
        method: 'GET',
        success: function (data) {
            $.each(data.permanenciaMinimaPromoCancelVol, function (id, registro) {
                $("#selectTiempoPermanencia").append('<option value=' + registro.nombre + '>' + registro.nombre + '</option>');
            });       
            
            if(strPermMinimaCancelVol != "")
            { 
                $('#selectTiempoPermanencia').val(strPermMinimaCancelVol).trigger('change');
                $('#selectTiempoPermanencia').removeAttr('disabled');
                $('#check_tiempoPermanencia').prop('checked', true );
            }


        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar las Permanencias Mínimas. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
    
    //Obtiene las permanencias mínimas para los tipos de promociones de la promoción de mensualidad.
    $.ajax({
        url: urlGetPermanenciasMinimas,
        method: 'GET',
        success: function (data) {
            $.each(data.permanenciasMinimas, function (id, registro) {
                $("#promoMix_permanenciaMin").append('<option value=' + registro.nombre + '>' + registro.nombre + '</option>');
                $("#promoPlan_permanenciaMin").append('<option value=' + registro.nombre + '>' + registro.nombre + '</option>');
                $("#promoProd_permanenciaMin").append('<option value=' + registro.nombre + '>' + registro.nombre + '</option>');
            });
            $('#promoMix_permanenciaMin').val(intPermanenciaMinimaMix).trigger('change');
            $('#promoPlan_permanenciaMin').val(intPermanenciaMinimaPlan).trigger('change');
            $('#promoProd_permanenciaMin').val(intPermanenciaMinimaProd).trigger('change');
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los tiempos de Servicio Activo. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });

    //Obtiene los productos para los tipos de promociones(Mix, Productos) de la promoción de mensualidad.
    $.ajax({
        url: url_GetProductos,
        method: 'GET',
        success: function (data) {
            $.each(data.productos, function (id, registro) {
                $("#promoMix_productos").append('<option value=' + registro.id + '>' + registro.nombre + ' - [' + registro.id + '] </option>');
                $("#promoProd_productos").append('<option value=' + registro.id + '>' + registro.nombre + ' - [' + registro.id + '] </option>');
            });
            var arrayProductosMix = strIdProductosMix.split(',');
            $('#promoMix_productos').val(arrayProductosMix).trigger('change');
            var arrayProductosPromoProd = strIdProductosPromoProd.split(',');
            $('#promoProd_productos').val(arrayProductosPromoProd).trigger('change');
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Productos. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });

    //Obtiene los planes para los tipos de promociones(Mix, Planes) de la promoción de mensualidad.
    $.ajax({
        url: url_GetPlanes,
        method: 'GET',
        success: function (data) {
            $.each(data.planes, function (id, registro) {
                $("#promoMix_planes").append('<option value=' + registro.id + '>' + registro.nombre + ' - [' + registro.id + '] </option>');
                $("#promoPlan_planes").append('<option value=' + registro.id + '>' + registro.nombre + ' - [' + registro.id + '] </option>');
            });         
            var arrayPlanesMix = strIdPlanesMix.split(',');
            $('#promoMix_planes').val(arrayPlanesMix).trigger('change');
            var arrayPlanesPromoPlan = strIdPlanesPromoPlan.split(',');
            $('#promoPlan_planes').val(arrayPlanesPromoPlan).trigger('change');
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Planes. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
    
    //Obtiene los emisores de la Promoción de Mensualidad.
    var parametros = { "intIdPromocion" : $("#idPromocion").val() };
    $.ajax({
        data: parametros,
        url: url_getEmisoresPromoMensualidad,
        method: 'GET',
        success: function (data) {            
            if ($('.checkboxEmisor').prop('checked'))
            {               
                $("#contenedor_tipo_emisores").show();
                $("#opcionEmisorTarCta option").each(function () {
                    $(this).remove();
                });
                $("#opcionEmisorBanco option").each(function () {
                    $(this).remove();
                });
                $("#optTipoEmisorTarjeta").prop('checked', false);
                $("#optTipoEmisorCtaBanco").prop('checked', false);
                $("#tbodyInformacionEmisores").remove();
                $('#tablaInformacionEmisores').append('<tbody id="tbodyInformacionEmisores"></tbody>');                
                $("#contenedorBanco").hide();
            }
            $("#contenedorTablaEmisor").show();
            $.each(data.emisores, function (id, registro) {
                $('#tablaInformacionEmisores #tbodyInformacionEmisores').append('<tr><td style="display:none;">' + registro.idTipoCuenta +
                    '</td><td style="display:none;">' + registro.idBanco + '</td><td>' + registro.esTarjeta +
                    '</td><td>' + registro.descCuenta + '</td><td>'+ registro.descBanco + 
                    '</td><td class="actions">' +
                    '<button title="Limpiar" type="button" class="btn btn-danger btn btn-sm btnEliminarEmisor" id="btnEliminarEmisor">' +
                    '<i class="fa fa-times-circle-o" aria-hidden="true"></i></button></td></tr>');
            });
            $(".spinner_emisores").hide();
        },
        error: function () {
        }
    });   
    // botón todas las jurisdicciones
    $("#radioTodosJurisd").click(function() {
        $("#tablaInfSectorizacion tbody tr").each(function (index) {
              $(this).closest('tr').remove();
        });
        if($("#radioTodosJurisd").is(':checked')) {  
            $('#jurisdiccion').val("").trigger('change');
            $('#canton').val("").trigger('change');
            $('#parroquia').val("").trigger('change');
            $('#sect_olt_edif').val("").trigger('change');
             
            $('#canton').prop('disabled', 'disabled');
            $('#parroquia').prop('disabled', 'disabled');
            $('#sect_olt_edif').prop('disabled', 'disabled');
            $('#jurisdiccion').prop('disabled', 'disabled');     
        } 
        else 
        {
            $('#canton').prop('disabled', false);
            $('#parroquia').prop('disabled', false);
            $('#sect_olt_edif').prop('disabled', false);
            $('#jurisdiccion').prop('disabled', false);
        }
    });
    
        /**
     * Obtiene los motivos relacionados a las promociones.
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 12-10-2020
     * @since 1.0
     */

    if (strTipoEdicion==='ED')
    {

        $.ajax({
        url: urlGetMotivos,
        method: 'GET',
        success: function (data) {
            $.each(data.motivos, function (id, registro) {
                $("#motivo_inactivar_vigente").append('<option value="">SELECCIONE</option>');
               
                $("#motivo_inactivar_vigente").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
              
            });
            $(".spinner_motivoInactivar").hide();
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Motivos. Por favor consulte con el Administrador");
            $('#modalMensajes').modal({show: true});
        }
    });

    $('#motivo_inactivar_vigente').select2({
        placeholder: 'Seleccione un motivo'
    });
    }


});

// Función que valida que solo se ingresen números decimales
function validaDecimalDescVariable(codPromoDesc) {
    var expesion_regular = /^\d+(\.\d+)?$/;
    if (!expesion_regular.test($("#descuento_periodo_" + codPromoDesc).val()))
    {
        $("#descuento_periodo_" + codPromoDesc).val('');
    }
}