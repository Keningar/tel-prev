$(document).ready(function () {

    $('#idBtnPeriodo,#idInformacionPlanes,#idInformacionEmisores').click();
    var arrayTiposClientes = strTipoCliente.split(',');
    $('#tipo_cliente').val(arrayTiposClientes).trigger('change.select2');
    $("#sector_olt").select2({placeholder: "Seleccionar", multiple  :true});

    var forms = document.getElementsByClassName('formPromoAnchoBanda');
    var intTotalBene = 0;
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
                if (! validaSectorizacion())
                {
                    $('#modalMensajes .modal-body').html("Debe ingresar al menos una Sectorización.");
                    $('#modalMensajes').modal({show: true});
                }
                else
                {   
                    grabarPromoAnchoBanda();
                }
            }
        }, false);
    });
    
    $(document).ready(function() {
        obtenerInformacion();
        actualizaBeneficiarios();
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
        var strIdGrupoPromocion  = $("#idPromocion").val();
        var boolCantidadCodigos = false;
        var strGrupoPromocion   = 'PROM_BW';
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
     * obtenerInformacion, función obtiene información de una promoción por ancho de banda.
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 27-02-2019
     * @since 1.0
     */
    function obtenerInformacion() {
        var idPromocion         = document.getElementById('idPromocion').value;
        var parametros          = {
            "intIdPromocion"    : idPromocion
        };
        //DatosGenerales
        $.ajax({
            data    : parametros,
            url     : url_datos_gene_promo,
            method  : 'GET',
            success: function (data) {
                $.each(data.datosGenePromo, function (id, registro) {
                    if (strTipoEdicion=='ED')
                    {
                        $('#nombre').val('ED_'+registro.nombreGrupo);
                    }
                    else
                    {
                        $('#nombre').val(registro.nombreGrupo);
                    }
                    
                    // Ingresamos la fecha y hora de la promocion
                    var arrayFechaHora = registro.feInicioVigencia.split(" ");
                    var fecha = arrayFechaHora[0];
                    var arrayHora = arrayFechaHora[1].split(":");
                    var hora = arrayHora[0];
                    $('#fechaIniVigencia').val(fecha);
                    $('#hora_ini').val(hora);
                    $('#antiguedad').val(registro.antiguedad);
                    arrayFechaHora = registro.feFinVigencia.split(" ");
                    fecha = arrayFechaHora[0];
                    arrayHora = arrayFechaHora[1].split(":");
                    hora = arrayHora[0];
                    $('#fechaFinVigencia').val(fecha);
                    $('#hora_fin').val(hora);
                    $('#codigo_promocion').val(registro.codigoProm);  
              
                    if ( null != registro.codigoProm ||  registro.codigoProm!='')
                    {
                      $('#codigo_promocion_ingresado').val('S'); 
                    }
                    else
                    {
                      $('#codigo_promocion_ingresado').val('N'); 
                    }
                   
                });
            },
            error: function () {
                $('#modalMensajes .modal-body').html('<p>No se pueden cargar datos generales de la promoción.</p>');
                $('#modalMensajes').modal('show');
            }
        });
    }

    /**
    * validaInfEmisores, función valida que se ingresen los emisores por débito bancario.
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 27-02-2019
    * @since 1.0
    */
    function validaInfEmisores() {
        var strFormaPago    = "";
        var contFormaPago   = 0;
        var contTabla       = 0;
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
    * limpiarEmisores, función que limpia información de emisores por dédito bancario.
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 27-02-2019
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
        $("#optTipoEmisorTarjeta").prop('checked',false);
        $("#optTipoEmisorCtaBanco").prop('checked',false);
        $("#optTipoSi").prop('checked',false);
        $("#optTipoNo").prop('checked',false);
        $("#tbodyInformacionEmisores").remove();
        $('#tablaInformacionEmisores').append('<tbody id="tbodyInformacionEmisores"></tbody>');
        $("#contenedorTablaEmisor").hide();
        $("#contenedorBanco").hide();
    }
    $("#idContenedorEmisores").hide();
    var cntFormaPago = 0;
    $('#forma_pago').change(function(e) {
        var strFormaPago    = "";
        var contador        = 0;
        $("#forma_pago :selected").each(function(){
            strFormaPago       = $(this).text();
            if (strFormaPago === "DEBITO BANCARIO")
            {
                cntFormaPago    = cntFormaPago + 1;
                contador        = contador + 1;
            }
        });
        if(contador === 0)
        {   if (cntFormaPago !== 0)
            {
                limpiarEmisores(); 
            }
            $("#idContenedorEmisores").hide();
        }
        else
        {
            $("#idContenedorEmisores").show();
        }
    });
    $(".spinner_tajeta_ctaBanco").hide();
    $(".spinner_banco").hide();
    $("#contenedorBanco").hide();
    $("#contenedorTarjCta").hide();
    //RadioButton Tipo de Emisores
    $('input:radio[name="optTipoEmisor"]').change(function() {
        $("#optTipoSi").prop('checked',true);
        obtenerTipoEmisor($(this).val());
    });
    /**
     * obtenerTipoEmisor, función obtiene los tipo de emisores por Cuentas
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 27-02-2019
     * @since 1.0
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
                                                   '" value="' + registro.intIdBanco + '">'+
                                                   registro.strDescripcionBanco + '</option>');
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
                        $('#tablaInformacionEmisores #tbodyInformacionEmisores').append('<tr><td style="display:none;">'+opcionEmisorTarCta+'</td>'+
                                                                                        '<td style="display:none;">'+opcionEmisorBanco+'</td>'+
                                                                                        '<td>'+tipoEmisor+'</td><td>'+nameOpcionEmisorTarCta+'</td>'+
                                                                                        '<td>'+nameOpcionEmisorBanco +'</td><td class="actions">'+
                                                                                        '<button title="Limpiar" type="button"'+
                                                                                        'class="btn btn-danger btn btn-sm btnEliminarEmisor"'+
                                                                                        'id="btnEliminarEmisor">'+
                                                                                        '<i class="fa fa-times-circle-o" aria-hidden="true"></i>'+
                                                                                        '</button></td></tr>');
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
                    $('#tablaInformacionEmisores #tbodyInformacionEmisores').append('<tr><td style="display:none;">'+opcionEmisorTarCta+'</td>'+
                                                                                    '<td style="display:none;">'+opcionEmisorBanco+'</td>'+
                                                                                    '<td>'+tipoEmisor+'</td><td>'+nameOpcionEmisorTarCta+'</td>'+
                                                                                    '<td>'+nameOpcionEmisorBanco+'</td><td class="actions">'+
                                                                                    '<button title="Limpiar" type="button"'+
                                                                                    'class="btn btn-danger btn btn-sm btnEliminarEmisor"'+
                                                                                    'id="btnEliminarEmisor">'+
                                                                                    '<i class="fa fa-times-circle-o" aria-hidden="true"></i>'+
                                                                                    '</button></td></tr>');
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
                                $('#tablaInformacionEmisores #tbodyInformacionEmisores').append('<tr><td style="display:none;">'+opcionEmisorTarCta+'</td>'+
                                                                                                '<td style="display:none;">'+opcionEmisorBanco+'</td>'+
                                                                                                '<td>'+tipoEmisor+'</td><td>'+nameOpcionEmisorTarCta+'</td>'+
                                                                                                '<td>'+nameOpcionEmisorBanco+'</td><td class="actions">'+
                                                                                                '<button title="Limpiar" type="button"'+
                                                                                                'class="btn btn-danger btn btn-sm btnEliminarEmisor"'+
                                                                                                'id="btnEliminarEmisor">'+
                                                                                                '<i class="fa fa-times-circle-o" aria-hidden="true"></i>'+
                                                                                                '</button></td></tr>');
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
                            $('#tablaInformacionEmisores #tbodyInformacionEmisores').append('<tr><td style="display:none;">'+opcionEmisorTarCta+'</td>'+
                                                                                            '<td style="display:none;">'+opcionEmisorBanco+'</td>'+
                                                                                            '<td>'+tipoEmisor+'</td><td>'+nameOpcionEmisorTarCta+'</td>'+
                                                                                            '<td>'+nameOpcionEmisorBanco+'</td><td class="actions">'+
                                                                                            '<button title="Limpiar" type="button"'+
                                                                                            'class="btn btn-danger btn btn-sm btnEliminarEmisor"'+
                                                                                            'id="btnEliminarEmisor">'+
                                                                                            '<i class="fa fa-times-circle-o" aria-hidden="true"></i>'+
                                                                                            '</button></td></tr>');
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
     * EliminaCabecera, función que elimina las cabeceras de las tablas dinámicas.
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 27-02-2019
     * @since 1.0
     */
    function eliminaCabecera()
    {
        var nFilasEmisor = $("#tablaInformacionEmisores tr").length;
        var nFilasPlan = $("#tablaInformacionPlanes tr").length;
        var nFilasSector = $("#tablaInfSectorizacion tr").length;
        if (nFilasEmisor === 1)
        {
            $("#contenedorTablaEmisor").hide();
        }
        if (nFilasPlan === 1)
        {
            $("#contenedorInformacionPlanes").hide();
        }
        if (nFilasSector === 1)
        {
            $("#contTablaSectorizacion").hide();
            $("#conTotalBeneficiarios").hide();
            intTotalBene = 0;
        }
        actualizaBeneficiarios();
    }

    /**
     * grabarPromoAnchoBanda, función graba datos de una promoción por ancho de banda.
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 27-02-2019
     * @since 1.0
     */
    function grabarPromoAnchoBanda()
    {
        $(".spinner_guardarPromocion").show();
        $("#btnGuardarPromocion").attr('disabled','disabled');
        var tipoCliente         = $('#tipo_cliente').val();
        var idPromocion         = document.getElementById('idPromocion').value;
        var nombrePromocion     = document.getElementById('nombre').value;
        var antiguedad          = document.getElementById('antiguedad').value;
        var tipoNegocio         = $("#tipo_negocio").val();
        var ultimaMilla         = $("#ultima_milla").val();
        var formaPago           = $("#forma_pago").val();
        var estadoServicio      = $("#estado_servicio").val();
        var arrayPeriodo        = [];
        var arrayEmisores       = [];//EMISORES
        var arrayPlanes         = [];
        var arraySectorizacion  = [];
        var fechaIniVigencia    = document.getElementById('fechaIniVigencia').value.replace(/-/g,'/');
        var fechaFinVigencia    = document.getElementById('fechaFinVigencia').value.replace(/-/g,'/');
        var horaInicia          = document.getElementById('hora_ini').value + ":00:00";
        var horaFin             = document.getElementById('hora_fin').value + ":00:00";
        var checkboxPeriodos        = 0;
        $("#tablaInformacionPlanes tbody tr").each(function (index) {
            var plan, planSuperior, idSecuencia;
            $(this).children("td").each(function (index2) {
                switch (index2) {
                    case 0:
                        plan = $(this).text();
                        break;
                    case 1:
                        planSuperior = $(this).text();
                        break;
                    case 2:
                        idSecuencia = $(this).text();
                        break;
                    default:
                        break;
                }
            });
            arrayPlanes.push(plan + '|' + planSuperior + '|' + idSecuencia);
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
            arraySectorizacion.push({"intJurisdiccion": intJurisdiccion, "intCanton": intCanton, "intParroquia": intParroquia,
                                     "strOptSectOltEdif": strOptSectOltEdif, "intSectOltEdif": intSectOltEdif,
                                     "intSectorizacion":intSectorizacion});
        });
        if (arrayPlanes.length === 0)
        {
            $('#idInformacionPlanes').click();
            $('#modalMensajes .modal-body').html('<p>Debe escoger planes para la promoción.</p>');
            $('#modalMensajes').modal('show');
            $(".spinner_guardarPromocion").hide();
            $("#btnGuardarPromocion").removeAttr("disabled");
            return false;
        }
        $('[name="checkboxPeriodos"]:checked').each(function(){
            arrayPeriodo.push(this.value+'|0');
        });
        //EMISORES
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
        
        // Prepara la fecha de promocion
        fechaIniVigencia = fechaIniVigencia + ' ' + horaInicia;
        fechaFinVigencia = fechaFinVigencia + ' ' + horaFin;
        
        /*  @author Hector Lozano <hlozano@telconet.ec>
         *  @version 1.0 1-07-2022 - Se agrega función JSON.stringify para codificar el JSON 
         *                           de Emisores y Sectorización para enviarlos desde la peticion ajax de la interfaz,
         *                           la cual se utilizó para enviar una gran cantidad de información de la misma.
         */     
        var parametros = {
            "intIdPromocion"            : idPromocion,
            "strNombrePromocion"        : nombrePromocion,
            "strAntiguedad"             : antiguedad,
            "strTiposNegocio"           : tipoNegocio,
            "arrayUltimasMilla"         : ultimaMilla,
            "arrayFormasPago"           : formaPago,
            "arrayEstadoServicio"       : estadoServicio,
            "arrayPeriodos"             : arrayPeriodo,
            "strFeIniVigencia"          : fechaIniVigencia,
            "strFeFinVigencia"          : fechaFinVigencia,
            "arrayTipoCliente"          : tipoCliente,
            "arrayEmisores"             : JSON.stringify(arrayEmisores),
            "arrayPlanes"               : arrayPlanes,
            "arraySectorizacion"        : JSON.stringify(arraySectorizacion),
            "strTipoEdicion"            : strTipoEdicion,
            "strMotivoInactivarVigente" : strMotivoInactivarVigente,
            "intIdPromocionOrigen"      : $("#id_grupo_promocion").val(),
            "strCodigoPromocion"        : $("#codigo_promocion").val(),
            "strCodigoPromocionIng"     : $("#codigo_promocion_ingresado").val()
        };

        $.ajax({
            data :  parametros,
            url  :  url_editar_promo_ancho_banda,
            type :  'post',
            async: false,
            beforeSend: function () {
            },
            success:  function (response) {
                $(".spinner_guardarPromocion").hide();
                $("#btnGuardarPromocion").removeAttr("disabled");
                if (response === "OK" && strTipoEdicion==='E' )
                {   
                    $('#modalMensajes .modal-body').html('<p>Se actualizó con éxito la Promoción de Ancho de Banda.</p>');
                    $('#modalMensajes').modal('show');
                }
                else if (response != "OK" )
                {
                    $('#modalMensajes .modal-body').html(response.replaceAll("_", " "));
                    $('#modalMensajes').modal('show');
                }
                if (strTipoEdicion==='ED')
                {
                  strRespuestaED=response;
                }
                else
                {
                    setTimeout(function(){location.reload();},5000);
                }
            
            },
            error: function () {
                $(".spinner_guardarPromocion").hide();
                $("#btnGuardarPromocion").removeAttr("disabled");
                $('#modalMensajes .modal-body').html("No se pudo Actualizar la Promoción. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal('show');
                location.reload();
            }
        });
        
        if(strTipoEdicion==='ED' && strRespuestaED==='OK')
        {
            $.ajax({
                data: parametros,
                url:  url_graba_promo_ancho_banda,
                type: 'post',   
                async: false,
                success: function (response) {
                    $(".spinner_guardarPromocion").hide();
                    $("#guardarPromociones").removeAttr("disabled");
                  
                    if (response === "OK")
                    {
                        $('#modalMensajes .modal-body').html('Se Ingresó con éxito la Promoción de Ancho de Banda.');
                        $('#modalMensajes').modal({show: true});
                    } 
                    else
                    {                
                        $('#modalMensajes .modal-body').html('No se pudo guardar la Promoción. Por favor consulte con el Administrador.');
                        $('#modalMensajes').modal({show: true});
                    }
                    window.location.href = strUrlListaPromo;
                },
                failure: function (response) {
                    $(".spinner_guardarPromocion").hide();
                    $("#guardarPromociones").removeAttr("disabled");
                    $('#modalMensajes .modal-body').html("No se pudo guardar la Promoción. Por favor consulte con el Administrador.");
                    $('#modalMensajes').modal({show: true});            
                }
            });

        }
        
    }
    eliminaCabecera();
    $("#conTotalBeneficiarios").hide();
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
        var intBeneficiarios  = 0;

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
            intBeneficiarios = obtenerBeneficiarios(intJurisdiccion,intCanton,intParroquia,intSectOltEdif);
            intTotalBene = parseInt(intTotalBene) + parseInt(intBeneficiarios);
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
                                                                                  '<td class="beneficiarios">' + intBeneficiarios + '</td>'+
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
                $("#contTablaSectorizacion").show();
                $('#tablaInfSectorizacion #tbodyInfSectorizacion').append('<tr><td style="display:none;">' + intJurisdiccion +'</td>'+
                                                                          '<td style="display:none;">' + intCanton + '</td>'+
                                                                          '<td style="display:none;">' + intParroquia +'</td>'+
                                                                          '<td style="display:none;">' + strOptSectOltEdif + '</td>'+
                                                                          '<td style="display:none;">' + intSectOltEdif +'</td>'+
                                                                          '<td style="display:none;">0</td><td>' + strJurisdiccion + '</td>'+
                                                                          '<td>' + strCanton + '</td><td>' + strParroquia + '</td>'+
                                                                          '<td>' + strOptSectOltEdif + '</td>'+
                                                                          '<td>' + textSectOltEdif.replace(/(^\s*,)|(,\s*$)/g, '') + '</td>'+
                                                                          '<td class="beneficiarios">' + intBeneficiarios + '</td>'+
                                                                          '<td class="actions"><button title="Limpiar" type="button"'+
                                                                          'class="btn btn-danger btn btn-sm btnEliminarSectorizacion"'+
                                                                          'id="btnEliminarSectorizacion">'+
                                                                          '<i class="fa fa-times-circle-o" aria-hidden="true"></i>'+
                                                                          '</button></td></tr>');

                $('#jurisdiccion').val("").trigger('change');
                $('#canton').val("").trigger('change');
                $('#parroquia').val("").trigger('change');
                $('#sect_olt_edif').empty().trigger('change');
                $("#radioSector").prop("checked", true);
            }
            // Mostramos la tabla de total de Beneficiarios
            mostrarTotalBeneficiarios();
        }
    });
    
    //Botón eliminar registros de Sectorización
    $(document).on('click', '.btnEliminarSectorizacion', function (event) {
        event.preventDefault();
        $(this).closest('tr').remove();
        eliminaCabecera();
        $('#radioTodosJurisd').prop('checked',false);
        $('#canton').prop('disabled', false);
        $('#parroquia').prop('disabled', false);
        $('#sect_olt_edif').prop('disabled', false);
        $('#jurisdiccion').prop('disabled', false);
    });
    
    $('#check_sectorizacion').change(function () {
        if (this.checked) 
        {
            $('#contenedor_sectorizacion').find('input, select, button').attr('disabled', 'disabled');
            $('#jurisdiccion').val("").trigger('change');
            $('#canton').val("").trigger('change');
            $('#parroquia').val("").trigger('change');
            $('#sect_olt_edif').empty().trigger('change');
            $("#radioSector").prop("checked", true);
            $("#tbodyInfSectorizacion").remove();
            $('#tablaInfSectorizacion').append('<tbody id="tbodyInfSectorizacion"></tbody>');
            $("#contTablaSectorizacion").hide();
            $("#tbodyTotalBeneficiarios").remove();
            $('#tablaTotalBeneficiarios').append('<tbody id="tbodyTotalBeneficiarios"></tbody>');
            $("#conTotalBeneficiarios").hide();
            $("#radioTodosJurisd").prop("checked", false);
            intTotalBene = 0;
        }
        else
        {
            $('#contenedor_sectorizacion').find('input, select, button').removeAttr('disabled');   
        }
    });
    /**
    * validaSectorizacion, función valida que se ingresen al menos una sectorización.
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 27-04-2019
    * @since 1.0
    */
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
     * validaCabeceraSectorizacion, función que valida las cabeceras de las tablas dinámicas.
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

    function obtenerBeneficiarios(codJurisdiccion, codCanton, codParroquia, codSector)
    {
        var codLineProfile = obtenerPlanesSeleccionados();
        var intCantidad = 0;
        var parametros = {
            "idJurisdiccion": codJurisdiccion,
            "idCanton": codCanton,
            "idParroquia": codParroquia,
            "idSectores": codSector,
            "codLineProfile": codLineProfile,
            "tipoPromocion": 'PROM_BW'
        };
        $.ajax({
            type: "POST",
            data: parametros,
            url: url_getBeneficiariosOlt,
            async: false,
            success: function (response) {
                if (response.resultado === 'OK') {
                    intCantidad = response.cantidad;
                } else {
                    $('#modalMensajes .modal-body').html("Problemas al obtener la cantidad de beneficiarios");
                    $('#modalMensajes').modal({show: true});
                }
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudieron cargar los Beneficiarios. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
        return intCantidad;
    }

    function obtenerPlanesSeleccionados()
    {
        var idslineProfile = [];
        $('#tablaInformacionPlanes tbody tr').each(function (index) {
            var strLineProfile;
            $(this).children('td').each(function (index2) {
                switch(index2) {
                    case 3:
                        strLineProfile = $(this).text();
                        break;
                }
            })
            idslineProfile.push(strLineProfile);
        })
        return idslineProfile;
    }

    function mostrarTotalBeneficiarios() {
        $("#tbodyTotalBeneficiarios").remove();
        $('#tablaTotalBeneficiarios').append('<tbody id="tbodyTotalBeneficiarios"></tbody>');
        $("#conTotalBeneficiarios").show();
        $('#tablaTotalBeneficiarios #tbodyTotalBeneficiarios').append('<tr><td> Total: </td>' +
                                                                    '<td>' + intTotalBene + '</td></tr>');
    }

    function actualizaBeneficiarios() {
        intTotalBene = 0;
        $('#tablaInfSectorizacion tbody tr').each(function (index) {
            var intJurisdiccion, intCanton, intParroquia, intSector, intCantidadBene;
            $(this).children('td').each(function (index2) {
                switch(index2) {
                    case 0:
                        intJurisdiccion = $(this).text();
                        break;
                    case 1:
                        intCanton = $(this).text();
                        break;
                    case 2:
                        intParroquia = $(this).text();
                        break;
                    case 4:
                        intSector = $(this).text();
                        break;
                }
            })
            intCantidadBene = obtenerBeneficiarios(intJurisdiccion, intCanton, intParroquia, intSector);
            intTotalBene = parseInt(intTotalBene) + parseInt(intCantidadBene);
            $(this).closest('tr').find('td.beneficiarios').text(intCantidadBene);
        })
        if (intTotalBene > 0) {
            mostrarTotalBeneficiarios();
        }
    }

    $(".btnAgregarPlan").on("click", function () {
        //var tipoNegocio         = $("#tipo_negocio").val();
        var validaPlan          = $("#idPlan option:selected").val();
        var validaPlanSuperior  = $("#idPlanSuperior option:selected").val();
        var opcionPlan          = "";
        var opcionPlanSuperior  = "";
        var objPlan             = "";
        var arrPlan             = "";
        var profilePlan         = "";
        var namePlan            = "";
        var namePlanSuperior    = ""; 
        if (validaPlan.length === 0)
        {
            $('#modalMensajes .modal-body').html('<p>Debe seleccionar un Plan contratado.</p>');
            $('#modalMensajes').modal('show');
            return false;
        }
        if (validaPlanSuperior.length === 0)
        {
            $('#modalMensajes .modal-body').html('<p>Debe seleccionar un Plan promocion.</p>');
            $('#modalMensajes').modal('show');
            return false;
        }
        if (validaPlan === validaPlanSuperior)
        {
            $('#modalMensajes .modal-body').html('<p>Debe escoger planes distintos.</p>');
            $('#modalMensajes').modal('show');
            return false;
        }
        $("#idPlan :selected").each(function(){
            namePlan    = $(this).text();
            objPlan     = $(this).val();
            arrPlan     = objPlan.split("-");
            opcionPlan  = arrPlan[0];
            profilePlan = arrPlan[1];

            $("#idPlanSuperior :selected").each(function(){
                namePlanSuperior   = $(this).text();
                opcionPlanSuperior = $(this).val();
                
                if (namePlan !== namePlanSuperior)
                {
                    $("#contenedorInformacionPlanes").show();
                    var contador = 0;
                    $("#tablaInformacionPlanes tbody tr").each(function (index) {
                        var idPlan, idPlanSuperior, proPlan;
                        $(this).children("td").each(function (index2) {
                            switch (index2) {
                                case 0:
                                    idPlan = $(this).text();
                                    break;
                                case 1:
                                    idPlanSuperior = $(this).text();
                                    break;
                                case 3:
                                    proPlan = $(this).text();
                                    break;
                                default:
                                    break;  
                            }
                            if ((idPlan === opcionPlan) || (idPlan === opcionPlan && idPlanSuperior === opcionPlanSuperior))
                            {
                                contador = contador + 1;
                                $('#modalMensajes .modal-body').html('<p>Los Planes ya se encuentran incluidos entre los preseleccionados.</p>');
                                $('#modalMensajes').modal('show');
                                return false;
                            } else if (proPlan === profilePlan) {
                                contador = contador + 1;
                                $('#modalMensajes .modal-body').html('<p>Solo puede seleccionar un plan con el mismo LineProfile.</p>');
                                $('#modalMensajes').modal('show');
                                return false;
                            }
                        });
                    });

                    if (contador !== 1)
                    {
                        $('#tablaInformacionPlanes #tbodyInformacionPlanes').append('<tr><td style="display:none;">'+opcionPlan+'</td>'+
                                                                                    '<td style="display:none;">'+opcionPlanSuperior+'</td>'+
                                                                                    '<td style="display:none;">0</td>'+
                                                                                    '<td style="display:none;">'+profilePlan+'</td>'+
                                                                                    '<td>'+namePlan+'</td>'+
                                                                                    '<td>'+namePlanSuperior+'</td><td class="actions">'+
                                                                                    '<button title="Limpiar" type="button"'+
                                                                                    'class="btn btn-danger btn btn-sm btnEliminarEmisor"'+
                                                                                    'id="btnEliminarPlan">'+
                                                                                    '<i class="fa fa-times-circle-o" aria-hidden="true"></i>'+
                                                                                    '</button></td></tr>');
                    }
                }
                else
                {
                    $('#modalMensajes .modal-body').html('<p>No se agregó el plan '+namePlan+', debe ser diferente al plan nuevo</p>');
                    $('#modalMensajes').modal('show');
                }
            });
        });
        $("#idPlan,#idPlanSuperior").val(null).trigger('change');
        actualizaBeneficiarios();
    });

    $(document).on('click', '.btnEliminarPlan', function (event) {
        event.preventDefault();
        $(this).closest('tr').remove();
        eliminaCabecera();
    });

});