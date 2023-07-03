$(document).ready(function () {
    var intContador = 0;
    $(".spinner_guardarPromocion,.spinner_canton,.spinner_parroquia,.spinner_sect_olt_edif").hide();
    $(".spinner_planSuperior").hide();
    $(".spinner_cargaPlan").hide();
    $('#tipo_cliente').select2({placeholder: "Seleccionar"});
    //CALENDARIOS
    $('#datetimepickerFechaIniVigencia').datetimepicker({
        format      : 'YYYY-MM-DD',
        minDate     : new Date(),
        useCurrent  : false,
        icons: {
            time        : 'fa fa-clock-o',
            date        : 'fa fa-calendar',
            up          : 'fa fa-chevron-up',
            down        : 'fa fa-chevron-down',
            previous    : 'fa fa-chevron-left',
            next        : 'fa fa-chevron-right',
            today       : 'fa fa-crosshairs',
            clear       : 'fa fa-trash-o',
            close       : 'fa fa-times'
        }
    });
    $('#datetimepickerFechaFinVigencia').datetimepicker({
        format      : 'YYYY-MM-DD',
        minDate     : new Date(),
        useCurrent  : false,
        icons: {
            time        : 'fa fa-clock-o',
            date        : 'fa fa-calendar',
            up          : 'fa fa-chevron-up',
            down        : 'fa fa-chevron-down',
            previous    : 'fa fa-chevron-left',
            next        : 'fa fa-chevron-right',
            today       : 'fa fa-crosshairs',
            clear       : 'fa fa-trash-o',
            close       : 'fa fa-times'
        }
    });
    $(document).on('click', '.button-addon1', function () {
        var fechaActual = $.datepicker.formatDate('yy-mm-dd', new Date());
        $('#datetimepickerFechaIniVigencia').data("DateTimePicker").minDate(fechaActual);
    });

    // Carga de Planes
    $(document).ready(function() {
        obtenerPlanesOrigen();
        //obtenerPlanesDestino();
    });

    // Formato de hora
    $('#fechaIniHora').datetimepicker({
        format: 'HH',
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
    $('#fechaFinHora').datetimepicker({
        format: 'HH',
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

    $(document).on('click', '.button-horaon1', function (event) {
        var fechaActual = $.datepicker.formatDate('hh:mi', new Date());
        $('#fechaIniHora').data("DateTimePicker").minDate(fechaActual);
    });
    $(document).on('click', '.button-horaon2', function (event) {
        var fechaActual = $.datepicker.formatDate('hh:mi', new Date());
        $('#fechaFinHora').data("DateTimePicker").minDate(fechaActual);
    });
    
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
    /**
    * obtenerSector, función que obtiene el sector en base a la parroquia.
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 27-04-2019
    * @since 1.0
    */
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
    /**
    * validaSectorizacion, función que obtiene los olt en base a la parroquia.
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 27-04-2019
    * @since 1.0
    */
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

    $("#sect_olt_edif").select2({placeholder: "Seleccionar", multiple  :true});
    
    $(document).on('keyup', '#antiguedad', function(event) {
        var max     = parseInt(this.max);
        var min     = parseInt(this.min);
        var valor   = parseInt(this.value);
        if((valor>max) || (valor<min)){
            $("#antiguedad").prop('value',"");
            $('#modalMensajes .modal-body').html('<p>El campo Antigüedad debe ser menor a '+ max +' meses</p>');
            $('#modalMensajes').modal('show');
            return false;
        }
    });

    $('#idPlanSuperior').select2({placeholder: "Seleccionar Plan Nuevo"});
    $('#idPlan').select2({placeholder: "Seleccionar Plan"}).on('select2:select', function (e) {
        $('#idPlanSuperior').val("").trigger('change');
        var objPlan = e.params.data.id;
        var arrPlan = objPlan.split("-");
        idPlan      = arrPlan[0];
        $(".spinner_planSuperior").show();
        obtenerPlanesDestino(idPlan);
    });

    // Obtenemos los planes que no han sido seleccionados para el origen
    function obtenerPlanesOrigen() {
        var tipoPromocion = 'PROM_BW';
        $.ajax({
            url: url_GetPlanesNoSeleccionados,
            method: 'GET',
            data: {'strIdTipoPromocion': tipoPromocion},
            success: function (data) {
                $(".spinner_plan").hide();
                $("#idPlan option").each(function () {
                    $(this).remove();
                    $("#idPlan").append('<option></option>');
                });
                $.each(data.planes, function (id, registro) {
                    $("#idPlan").append('<option value=' + registro.id + '-' + registro.valor + '>' +
                    registro.nombre + ' - [' +registro.id + ']</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los Planes. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    }

    // Obtenemos todos los planes para destino
    function obtenerPlanesDestino(idPlan) {
        $.ajax({
            url: url_GetPlanesDestino,
            method: 'POST',
            data: {'idPlan': idPlan},
            success: function (data) {
                $("#idPlanSuperior option").each(function () {
                    $(".spinner_planSuperior").hide();
                    $(this).remove();
                    $("#idPlanSuperior").append('<option></option>');
                });
                $.each(data.planes, function (id, registro) {
                    $("#idPlanSuperior").append('<option value=' +registro.id + '>' + registro.nombre + ' - [' + registro.id + ']</option>');
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los Planes. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    }

});
