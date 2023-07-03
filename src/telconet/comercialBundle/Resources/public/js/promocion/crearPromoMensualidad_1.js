$(document).ready(function () {
    
    //Métodos que limpian campos del formulario.
    $("#formularioPromocionMensual").trigger("reset");
    $("#limpiarPromocion").click(function () {
        $("#formularioPromocionMensual").trigger("reset");
        location.reload();
    });
    
    //Inicialización de atributos a elementos de la página.
    $(".spinner_guardarPromocion,.spinner_canton,.spinner_parroquia,.spinner_sect_olt_edif").hide();
    $("#promoMix_cont_tipoPeriodo, #promoPlan_cont_tipoPeriodo,#promoProd_cont_tipoPeriodo").hide();
    $("#promoMix_contPeriodo, #promoPlan_contPeriodo,#promoProd_contPeriodo").hide();
    if ($('#check_promoMix,#check_promoPlan,#check_promoProd,#check_promoDescTotal').is(':checked'))
    {
        $('#promoMix_form,#promoPlan_form,#promoProd_form,#promoDescTotal_form').find('input, select').removeAttr('disabled');
    } else
    {
        $('#promoMix_form,#promoPlan_form,#promoProd_form,#promoDescTotal_form').find('input, select').attr('disabled', 'disabled');
    }
    $("#sect_olt_edif").select2({placeholder: "Seleccionar", multiple: true});
    $("#estado_servicio").select2({placeholder: "Seleccionar", multiple: true});
    $('#forma_pago').select2({placeholder: "Seleccionar", multiple: true});
    $('#tipo_cliente').select2({placeholder: "Seleccionar"});
    $('#promoMix_planes, #promoPlan_planes').select2({placeholder: "Seleccionar", multiple: true});
    $('#promoMix_productos,#promoProd_productos').select2({placeholder: "Seleccionar", multiple: true});
    $('#promoMix_permanenciaMin, #promoPlan_permanenciaMin,#promoProd_permanenciaMin,#selectTiempoPermanencia').select2({placeholder: "Seleccionar", allowClear: true});
    
    $('#selectTiempoPermanencia').attr('disabled', 'disabled');
    $('#check_tiempoPermanencia').attr('disabled', 'disabled');  
    
    
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
    
    
    
    
    //Obtiene las permanencias mínimas para calculo de procesos de Cancelacion Voluntaria.
    $.ajax({
        url: urlGetPermanenciaPromoCancelVol,
        method: 'GET',
        success: function (data) {
            $.each(data.permanenciaMinimaPromoCancelVol, function (id, registro) {
                $("#selectTiempoPermanencia").append('<option value=' + registro.nombre + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar las Permanencias Mínimas. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
    

    //Método que valida el valor ingresado sea decimal.
    $("#promoMix_descIndefinido,#promoMix_descUnico,#promoPlan_descIndefinido,#promoPlan_descUnico,#promoProd_descIndefinido" +
        ",#promoProd_descUnico,#promoDescTotal_descUnico").blur(function () {
        var expesion_regular = /^\d+(\.\d+)?$/;
        if (!expesion_regular.test($(this).val()))
        {
            $(this).val('');
        }
    });
    //Método que valida el valor ingresado sea entero.
    $("#promoMix_diasMora, #promoPlan_diasMora, #promoProd_diasMora").blur(function () {
        var expesion_regular = /^[0-9]+$/;
        if (!expesion_regular.test($(this).val()))
        {
            $(this).val('');
        }
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
                    registro.id + '"><input class="form-control form-control-sm input_descuento_periodo" type="text" id="descuento_periodo_promoMix'+
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
                    '<input class="form-check-input" name="promoDescTotal_periodo" type="checkbox" disabled="disabled" id="check_periodo' +
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
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Períodos. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
    
    //Obtiene Estados de Servicios para la promoción de mensualidad.
    $.ajax({
        url: urlGetEstadosServ,
        method: 'GET',
        success: function (data) {
            $.each(data.estados_servicios, function (id, registro) {
                $("#estado_servicio").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
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

    //Obtiene los Sectores/Olt's para la sectorización de la promoción de mensualidad.
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
            
        } else if($(this).val() === 'olt')
        {
            obtenerOlt($('#cod_parroquia').val());
            
        }else
        {
            obtenerEdificio($('#cod_parroquia').val());
        }
    });

    //Método que limpia la tabla y formulario de sectorización.
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
            $('#sect_olt_edif').empty().trigger('change');
            $("#radioSector").prop("checked", true);
            $("#radioTodosJurisd").prop("checked", false);
        } else
        {
            $('#contenedor_sectorizacion').find('input, select, button').removeAttr('disabled');
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
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Tipos de Clientes. Por favor consulte con el Administrador.");
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
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Planes. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });

    /**
     * Función valida que se ingresen los campos obligatorios.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 27-02-2019
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

                if (!validaSectorizacion())
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
                    $('#modalMensajes .modal-body').html("Debe llenar el campo de descuento si selecciona un Período Variable.");
                    $('#modalMensajes').modal({show: true});

                }else if (!validaFormatoCodigo())
                {
                    $('#modalMensajes .modal-body').html("El código ingresado no cumple con el formato correcto: Cadena de texto alfanumérica sin espacios.");
                    $('#modalMensajes').modal({show: true});
                }else if (!validaCodigoUnico())
                {
                    $('#modalMensajes .modal-body').html("El código ingresado existe para otra promoción, debe ser único.");
                    $('#modalMensajes').modal({show: true});
                } else
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
        
        if ((boolEspacios || !boolAlfaNumerico) && strCodigoPromocion!='') {
          
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
        var boolCantidadCodigos = false;
        var strGrupoPromocion   = 'PROM_MENS';
        $.ajax({
            url: urlCodigoUnico,
            method: 'get',
            async: false,
            data: {'strCodigoPromocion': strCodigoPromocion,'strGrupoPromocion':strGrupoPromocion},
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
    
    // Función que valida que se ingrese por lo menos un tipo de Promoción.
    function validaSeleccionPromocion() {

        if (!$('#check_promoMix').prop('checked') && !$('#check_promoPlan').prop('checked') &&
            !$('#check_promoProd').prop('checked') && !$('#check_promoDescTotal').prop('checked'))
        {
            return false;
        }
        return true;
    }
    
    // Función que valida el período de la promoción.
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
    
    // Función que valida el descuento variable de la promoción.
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

    // Función que valida que ingrese por lo menos una sectorización.
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
    
    
   /**
    * guardarPromocion, función que realiza el guardado de la promoción de mensualidad.
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 27-02-2019
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
        arrayConfGenerales         = {arraySectorizacion: JSON.stringify(arraySectorizacion), strNombrePromocion: strNombrePromocion,
                                      arrayEstadoServicio: arrayEstadoServicio, strInicioVigencia: strInicioVigencia,
                                      strFinVigencia: strFinVigencia, arrayFormaPago: arrayFormaPago, arrayEmisores: JSON.stringify(arrayEmisores),
                                      arrayTipoCliente: arrayTipoCliente, strPermMinimaCancelVol:strPermMinimaCancelVol};

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
                fltDescIndefinido  = "";
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
                             fltDescIndefinido: fltDescIndefinido,strTipoPeriodo: strTipoPeriodo, arrayDescuentoPeriodo: arrayDescuentoPeriodo};
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
                fltDescIndefinidoPlan  = "";
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
                fltDescIndefinidoProd  = "";
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
            var strTipoPeriodoDescTotal        = $("input:radio[name=promoDescTotal_tipoPeriodo]:checked").val();
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
        
        $.ajax({
            url: url_guardarPromocionMensualidad,
            method: 'POST',
            data: {arrayConfGenerales: arrayConfGenerales,
                   arrayPromoMix: arrayPromoMix,
                   arrayPromoPlanes: arrayPromoPlanes,
                   arrayPromoProductos: arrayPromoProductos,
                   arrayPromoDescTotal: arrayPromoDescTotal,
                   strCodigoPromocion : $("#codigo_promocion").val()},
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
                location.reload();
            },
            error: function () {
                $(".spinner_guardarPromocion").hide();
                $("#guardarPromociones").removeAttr("disabled");
                $('#modalMensajes .modal-body').html("No se pudo guardar la Promoción. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    }
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
});

// Función que valida que solo se ingresen números decimales
function validaDecimalDescVariable(codPromoDesc) {
    var expesion_regular = /^\d+(\.\d+)?$/;
    if (!expesion_regular.test($("#descuento_periodo_" + codPromoDesc).val()))
    {
        $("#descuento_periodo_" + codPromoDesc).val('');
    }
}