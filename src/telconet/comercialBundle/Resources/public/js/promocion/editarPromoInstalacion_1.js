$(document).ready(function () {  
    /**
     * Setea con valores iniciales el formulario de Promoción de Instalación.
     *    
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 11-04-2019
     * @since 1.0
     */     
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

    $(document).on('click', '.button-addon1', function (event) {
        var fechaActual = $.datepicker.formatDate('yy-mm-dd', new Date());
        $('#datetimepickerFechaIniVigencia').data("DateTimePicker").minDate(fechaActual);
    });
        
    $("#datetimepickerFechaIniVigencia").on("dp.change", function (e) {
        $('#datetimepickerFechaFinVigencia').data("DateTimePicker").minDate(e.date);
        $('#fin_vigencia').val('');
    });
    
    $("#sect_olt_edif").select2({placeholder: "Seleccionar",multiple: true});
    $("#estado_servicio").select2({placeholder: "Seleccionar"});

    $(".spinner_canton,.spinner_parroquia,.spinner_sect_olt_edif").hide();
    $('#informacionPeriodos').click();    
    $('#idInformacionEmisores').click(); 
    
    $("#idformPromoInstalacion").trigger("reset");    
   
    $(".spinner_guardarPromocion").hide();
    $("#guardarPromociones").removeAttr("disabled");    
    
    $("#limpiarPromocion").click(function () {    
        if( $('.checkboxEmisor').prop('checked') )
        {
            $('#idformPromoInstalacion').click(); 
        }
       $("#idformPromoInstalacion").trigger("reset");       
       $('#tipo_negocio').val(null).trigger('change');
       $('#forma_pago').val(null).trigger('change');
       $('#ultima_milla').val(null).trigger('change');
       $('#estado_servicio').val(null).trigger('change'); 
       $('#checkboxEmisor').click();
       $("#checkboxEmisor").prop("checked", false);
       location.reload();
    });
    
    /**
     * Valida Campos requeridos 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 04-04-2019
     * @since 1.0
     */
    var forms = document.getElementsByClassName('formPromoInstalacion');
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

                } else if (!validaPeriodoPromocion())
                {
                    $('#modalMensajes .modal-body').html("Debe Seleccionar al menos el primer Período");
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
                    grabarPromocionInstalacion();
                }
            }
        }, false);
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
   //Obtiene los Sectores/Olt's/Edificios para la sectorización     
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
        var strIdGrupoPromocion  = $("#id_grupo_promocion").val();
        var boolCantidadCodigos = false;
        var strGrupoPromocion   = 'PROM_INS';
        $.ajax({
            url: urlCodigoUnico,
            method: 'get',
            async: false,
            data: {'strCodigoPromocion': strCodigoPromocion, 'strGrupoPromocion':strGrupoPromocion, 'strIdGrupoPromocion':strIdGrupoPromocion},
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
    
    validaCabeceraSectorizacion();
    /**
     * validaCabeceraSectorizacion, función que valida las cabeceras de las tablas
     * dinamicas
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 27-02-2019
     * @since 1.0
     */    
    function validaCabeceraSectorizacion()
    {
        var nFilasSector = $("#tablaInfSectorizacion tr").length;
        if (nFilasSector === 1)
        {
            $('#check_sectorizacion').click();
        }
    }
    
   /**
    * Obtiene los períodos
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-03-2019
    * @since 1.0
    */
   $.ajax({
        url: urlGetPeriodos,
        method: 'GET',
        success: function (data) {
            var cont = 0;

            $.each(data.periodos, function (id, registro) {               
                if(registro.id ==1)
                {
                    $('#check_periodos').append('<div class="form-check form-check-inline"><label class="form-check-label">' + registro.id +
                 '</label>&nbsp;<input class="form-check-input" type="checkbox" name="checkboxPeriodos" id="check_periodo'
                 +registro.id+'" value="'+registro.id+'" checked></div>');   
                }  
                else
                {
                    $('#check_periodos').append('<div class="form-check form-check-inline"><label class="form-check-label">' + registro.id +
                 '</label>&nbsp;<input class="form-check-input" type="checkbox" name="checkboxPeriodos" id="check_periodo'
                 +registro.id+'" value="'+registro.id+'"></div>');
                }
                cont++;
                if (cont === 5)
                {
                    cont = 0;
                    $('#check_periodos').append('<br/><br/>');
                }
            });
            //Obtengo el Período y el descuento, Formato del Periodo PERIODO|DESCUENTO  -> 1|50,2|50,3|50
            var arrayPeriodosDescuentos = strPeriodosDescuentos.split(",");  
            var fltDescuento = "";
            
            for (var i = 0; i < arrayPeriodosDescuentos.length; i++)
            {
                var arrayValoresPeriodoDescuento = "";
                arrayValoresPeriodoDescuento     = arrayPeriodosDescuentos[i].split("|");

                for (var j = 0; j < arrayValoresPeriodoDescuento.length; j++)
                {
                    //Periodo
                    if (j == 0)
                    {
                        $('#check_periodo' + arrayValoresPeriodoDescuento[j]).prop("checked", true);
                    }
                    //Descuento
                    if (j == 1 && fltDescuento == "")
                    {
                        fltDescuento = arrayValoresPeriodoDescuento[j];
                    }
                }
            }
            $('#descuento').val(fltDescuento).trigger('change');   
                        
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pueden cargar los Periodos.");
            $('#modalMensajes').modal({show: true});
        }
    });
    
    //DatosEmisores
    var parametros = { "intIdPromocion" : $("#id_grupo_promocion").val() };
    $.ajax({
        data: parametros,
        url: urlAjaxCargaEmisoresPromo,
        method: 'GET',
        success: function (data) {                        
            //fin de carga Emisores
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
        
    /**
    * Obtiene las formas de Pago
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-03-2019
    * @since 1.0
    */   
   $.ajax({
        url: urlGetFormasPago,
        method: 'GET',
        success: function (data) {
            $.each(data.formas_de_pago, function (id, registro) {
                $("#forma_pago").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
            var arrayFormasPagos = strIdsFormasPago.split(',');
            $('#forma_pago').val(arrayFormasPagos).trigger('change');
            $(".spinner_formaPago").hide();
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar las Formas de Pago. Por favor consulte con el Administrador");
            $('#modalMensajes').modal({show: true});
        }
    });
    $('#forma_pago').select2({       
        multiple:true,
        placeholder:'Seleccione Formas de Pago'
     });     
     
    /**
    * Obtiene los Tipos de Negocio
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-03-2019
    * @since 1.0
    */
    $.ajax({
        url: urlGetTiposNegocio,
        method: 'GET',
        success: function (data) {
            $.each(data.tipos_de_negocio, function (id, registro) {
                $("#tipo_negocio").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
            var arrayIdsTiposNegocio = strIdsTiposNegocio.split(",");                  
            $('#tipo_negocio').val(arrayIdsTiposNegocio).trigger('change');
        },
        error: function () {            
            $('#modalMensajes .modal-body').html("No se pueden cargar los Tipos de Negocio");
            $('#modalMensajes').modal({show: true});
        }
    });
    $('#tipo_negocio').select2({       
        multiple:true,
        placeholder:'Seleccione Tipos de Negocio'        
     });               
     
    /**
    * Obtiene las últimas millas
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-03-2019
    * @since 1.0
    */
     $.ajax({
        url: urlGetUltimaMilla,
        method: 'GET',
        data: {'strCodTipoPromocion': 'PROM_INS'},
        success: function (data) {
            $.each(data.ultimas_millas, function (id, registro) {
                $("#ultima_milla").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
            var arrayIdsUltimasMillas = strIdsUltimasMillas.split(",");                  
            $('#ultima_milla').val(arrayIdsUltimasMillas).trigger('change');
        },
        error: function () {            
            $('#modalMensajes .modal-body').html("No se pueden cargar las Últimas Millas");
            $('#modalMensajes').modal({show: true});
        }
    });
    $('#ultima_milla').select2({       
        multiple:true,
        placeholder:'Seleccione Últimas Millas'
     });     
     
    /**
    * Obtiene los estados de servicios parametrizados
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-03-2019
    * @since 1.0
    */
     $.ajax({
        url: urlGetEstadosServ,
        method: 'GET',
        success: function (data) {
            $.each(data.estados_servicios, function (id, registro) {
                $("#estado_servicio").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
            var arrayEstadosServicio = strEstadosServicio.split(",");                  
            $('#estado_servicio').val(arrayEstadosServicio).trigger('change');
        },
        error: function () {            
            $('#modalMensajes .modal-body').html("No se pueden cargar los Estados de Servicio");
            $('#modalMensajes').modal({show: true});
        }
    });
    $('#estado_servicio').select2({       
        multiple:true,
        placeholder:'Seleccione Estado del Servicio'
     });
    $('#estado_servicio').parent().hide(); 
    /**
    * Valida Descuento
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-03-2019
    * @since 1.0
    */
    $("#descuento").blur(function()
     {
        ocultarDiv('div_descuento');  
        if(validaDescuento())
        {
            ocultarDiv('div_descuento');                          
        }
        else
        {   if (isNaN($("#descuento").val()))
            {
                mostrarDiv('div_descuento');
                $('#div_descuento').html('El valor debe ser numérico entero o decimal (Formato:9999.99)');
                $("#descuento").val('');
            }
        }
     });         
         
    /**
    * Obtiene Funciones para cargar Emisores
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 29-03-2019
    * @since 1.0
    */
    $("#idContenedorEmisores").hide();
          
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
     * @version 1.0 19-10-2020
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