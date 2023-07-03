/**
 * Configuracion de datatable en español
 * @author Wilson Quinto <wquinto@telconet.ec>
 */
var idioma=

            {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningun dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Ãšltimo",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copyTitle": 'Informacion copiada',
                    "copyKeys": 'Use your keyboard or menu to select the copy command',
                    "copySuccess": {
                        "_": '%d filas copiadas al portapapeles',
                        "1": '1 fila copiada al portapapeles'
                    },

                    "pageLength": {
                    "_": "Mostrar %d filas",
                    "-1": "Mostrar Todo"
                    }
                }
            };
var parametroPagina={'num_meses':'1'};
$(document).ready(function () {
    

    /**
     * Configuracion de moment en español
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    moment.lang('es', {
        months: 'Enero_Febrero_Marzo_Abril_Mayo_Junio_Julio_Agosto_Septiembre_Octubre_Noviembre_Diciembre'.split('_'),
        monthsShort: 'Enero._Feb._Mar_Abr._May_Jun_Jul._Ago_Sept._Oct._Nov._Dec.'.split('_'),
        weekdays: 'Domingo_Lunes_Martes_Miercoles_Jueves_Viernes_Sabado'.split('_'),
        weekdaysShort: 'Dom._Lun._Mar._Mier._Jue._Vier._Sab.'.split('_'),
        weekdaysMin: 'Do_Lu_Ma_Mi_Ju_Vi_Sa'.split('_')
      }
      );
      $('.spinner_buscarAnulacionPago').hide();
      var startDateDisable = new Date();
      var endDateDisable =new Date();
      
      
      
    
    /**
     * accion para deshabilitar popup de carga de inicio de pagina
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    //Enviar archivo excel de pago.
    $.ajax({
        type: "GET",
        url: urlParametoPagina,
        data: $('#filtrobusqueda').serialize(),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        timeout: 600000,
        success: function (data) {
            $('#loading-wrapper').hide();
            if(data.error==true){
                $('#mensajeDialog').text(data.msg);
                $('#mensajeDialogPopUp').modal('show');
            }else{
                parametroPagina=data.parametros;
                console.log(parametroPagina);
                
            }
            configParametros();
        },
        error: function () {
            $('#loading-wrapper').hide();
            $('#mensajeDialog').text("Error al cargar parametros de configuracion");
            $('#mensajeDialogPopUp').modal('show');
            configParametros();
        }
    });
   // $('#loading-wrapper').hide();
   


   function configParametros(){
       startDateDisable.setDate(1);
       startDateDisable.setMonth(endDateDisable.getMonth() - parametroPagina.num_meses);
   }


    limpiarFormBuscar();

     /**
     * Instancia select como componente de select2 por documento
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#tipo_documentos").select2({placeholder: "SELECCIONAR", multiple: true});

    /**
     * Instancia select como componente de select2 por ciclo
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
     $("#c_facturacion").select2({placeholder: "SELECCIONAR", multiple: true});

    /**
     * Instancia select como componente de select2 por tipos de pagos
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#tipo_pagos").select2({placeholder: "SELECCIONAR", multiple: true});

     /**
     * Instancia select como componente de select2 por canales en linea
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#canal_pagos").select2({placeholder: "SELECCIONAR", multiple: true});

    /**
     * Instancia select como componente de select2 por estados
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#estado_pagos").select2({placeholder: "SELECCIONAR", multiple: true});


    /**
     * Instancia select como componente de select2 por bancos
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
     $("#bancos").select2({placeholder: "SELECCIONAR", multiple: true});

     
     /**
     * Instancia input como componente de datepicker
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $('#fecha_pago_desde').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'dd-mm-yy',
        minDate: startDateDisable,
        maxDate: endDateDisable,
    });
    /**
     * Instancia input como componente de datepicker
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $('#fecha_pago_hasta').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'dd-mm-yy',
        minDate: startDateDisable,
        maxDate: endDateDisable,
    });

    /**
     * Accion que ejecuta el limpiado del formulario de busqueda por filtros
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#limpiarAnulacionPago").click(function () {
        limpiarFormBuscar();
    });

    /**
     * Funcion que ejecuta el limpiado de filtros de busqueda
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    function limpiarFormBuscar() {
        $('#filtrobusqueda').trigger("reset");
        $('.select2-hidden-accessible').trigger('change.select2');
    }


    $("#buscarAnulacionPago").click(function () {
        $('.spinner_buscarAnulacionPago').show();
        $('#tabla_pagos_realizados').DataTable().ajax.reload();
    });

    /**
     * Accion que verificar la carga de archivo al popup
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $('#archivoPagoExcel').on('dragenter focus click', function() {
        $('.file-drop-area').addClass('is-active');
    });

    /**
     * Accion que verificar la carga de archivo al popup
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $('#archivoPagoExcel').on('dragleave blur drop', function() {
        $('.file-drop-area').removeClass('is-active');
    });

    /**
     * Accion que verificar la carga de archivo al popup
     * @author Wilson Quinto <wquinto@telconet.ec>
     */    
    $('#archivoPagoExcel').on('change', function() {
        var dataFile=$(this)[0].files;
        if( dataFile.length>0)
            $('.file-msg').html('Archivo seleccionado: <b>'+dataFile[0].name+'</b>');
        else
            $('.file-msg').text('Arrastre o click seleccione un archivo');
    });

    /**
     * Funcion que habilita o deshabilita la busqueda por filtros de pagos
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    function filter(){
        $('#btnEliminarExcel').toggleClass('d-none');
        $('#btnCargaExcel').toggleClass('d-none');

        if ($('#inputsFilter').attr('disabled')) {
            $('#inputsFilter').removeAttr('disabled');
            $('#pago-select-all').prop('checked',false);
            $("#totalSeleccionado").text('$0.00');
        } else {
            $('#inputsFilter').attr('disabled', true);
        }
    }

    /**
     * Accion que realizar la liberacion de busqueda por archivo
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $('#btnEliminarExcel').click(function (event) {
        filter();
        $('#tabla_pagos_realizados').dataTable().fnClearTable();
    });

    /**
     * Accion que realizar la busqueda por archivo de pagos
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#btnProcesarArchivo").click(function (event) {
        $('#tabla_pagos_realizados').dataTable().fnClearTable();
        $('#loading-wrapper').show();
        $('#pago-select-all').prop('checked',false);
        $("#totalSeleccionado").text('$0.00');
        filter();
        // Get form
        var form = $('#archivoPagoExcelForm')[0];

        // Create an FormData object 
        var data = new FormData(form);

        //Enviar archivo excel de pago.
        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: urlConsultarPagoExcel,
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                $('#loading-wrapper').hide();
                if(data.error==true){
                    $('#mensajeDialog').text(data.msg);
                    $('#mensajeDialogPopUp').modal('show');
                    filter();
                }else if(Array.isArray(data.pagos) && !data.pagos.length){
                    $('#mensajeDialog').text("No se encontraron datos con archivo de excel enviado");
                    $('#mensajeDialogPopUp').modal('show');
                }else{
                    $('#tabla_pagos_realizados').dataTable().fnAddData(data.pagos);
                }
               
            },
            error: function () {
                $('#loading-wrapper').hide();
                console.log("Error");
                filter();
            }
        });

    });

    /**
     * Accion que realizar la busqueda por filtros de pagos
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#buscarPagoFiltros").click(function (event) {
        $('#tabla_pagos_realizados').dataTable().fnClearTable();
        $('#loading-wrapper').show();
        $('#pago-select-all').prop('checked',false);
        $("#totalSeleccionado").text('$0.00');
        console.log("Consulta");
        if(!validarFiltrado()){
            $('#loading-wrapper').hide();
            $('#mensajeDialogPopUp').modal('show');
            return;
        }

        $.ajax({
            type: "GET",
            url: urlConsultarPago,
            data: $('#filtrobusqueda').serialize(),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            timeout: 600000,
            success: function (data) {
                $('#loading-wrapper').hide();
                console.log(data);
                if(data.error==true){
                    $('#mensajeDialog').text(data.msg);
                    $('#mensajeDialogPopUp').modal('show');
                }else if(Array.isArray(data.pagos) && !data.pagos.length){
                    $('#mensajeDialog').text("No se encontraron datos con los parametros de filtro");
                    $('#mensajeDialogPopUp').modal('show');
                }else
                    $('#tabla_pagos_realizados').dataTable().fnAddData(data.pagos);
             
                $('.spinner_buscarAnulacionPago').hide(); 
            },
            error: function () {
                $('#loading-wrapper').hide();
                console.log("Error");
                $('.spinner_buscarAnulacionPago').hide(); 
            }
        });

    });

    /**
     * Funcion que realiza la validacion la seleccion de numero de pago o fecha
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    function validarFiltrado()
    {
        if($("#num_pago").val().length || ($("#fecha_pago_desde").val().length && $("#fecha_pago_hasta").val().length)){
            if($("#fecha_pago_desde").val().length && $("#fecha_pago_hasta").val().length){
                if($("#tipo_documentos").val().length){
                    if($("#bancos").val().length){
                        return validarFechas($("#fecha_pago_desde").val(), $("#fecha_pago_hasta").val());
                    }else{
                        $('#mensajeDialog').text("Para el filtro con fecha es necesario seleccionar por lo menos un banco");
                        return false;
                    }
                }else{
                    $('#mensajeDialog').text("Para el filtro con fecha es necesario seleccionar por lo menos un tipo de documento");
                    return false;
                }     
            } 
            return true;
        }else{
            $('#mensajeDialog').text("Para continuar con la busqueda es requerido el numero de pago o un rango de fecha no mayor a 14 dìas");
        }
    }

    /**
     * Funcion que realiza la validacion de fecha en la busqueda por filtros
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    function validarFechas(start, end) {
        var startDate = moment(start,"DD-MM-YYYY");
        var endDate = moment(end,"DD-MM-YYYY");
        var nowDate = moment();

        var nowMonths = getAbsoluteMonths(nowDate);
        var startMonths = getAbsoluteMonths(startDate);

        var monthDifference = nowMonths-startMonths;

        if(!moment(start,"DD-MM-YYYY").isValid()) {
           $('#mensajeDialog').text("Fecha desde no es valida");
           return false;
        }
        if(!moment(end,"DD-MM-YYYY").isValid()) {
            $('#mensajeDialog').text("Fecha hasta no es valida");
            return false;
        }
        if (moment(endDate).isBefore(startDate)) {
            $('#mensajeDialog').text("Fecha hasta debe ser mayor o igual a Fecha desde");
            return false;
        }

        if (moment(nowDate).isBefore(startDate)) {
            $('#mensajeDialog').text("No es posible realizar busquedas de pagos a futuro");
            return false;
        }
        if (endDate.diff(startDate, 'days')>14) {
            $('#mensajeDialog').text("La diferencia entre fechas no debe ser mayor a 14 dias");
            return false;
        }
        if (monthDifference>parametroPagina.num_meses) {
            var nowMonth =  moment().subtract(0, "month").startOf("month").format('MMMM');
            var beforeMonth =  moment().subtract(parametroPagina.num_meses, "month").startOf("month").format('MMMM');
            $('#mensajeDialog').text("Mes seleccionado para la busqueda no es valido, meses posibles para la busqueda de ("+beforeMonth+" hasta "+nowMonth+")");
            return false;
        }
        return true;
     }

    /**
     * Funcion que obtiene el total de meses de una fecha
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    function getAbsoluteMonths(momentDate) {
        var months = Number(momentDate.format("MM"));
        var years = Number(momentDate.format("YYYY"));
        return months + (years * 12);
    }

    /**
     * Accion que realizar validacion de seleccion de registros a anular y mostrar el popup de confirmacion de pagos anular
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#btnReprocesoIndividual").click(function (event) {
       var arrayPagos = [];
        listaPagoRealizados.$('input[type="checkbox"]').each(function () {
            if (this.checked)
            {
                var data = listaPagoRealizados.row($(this).parent().parent()).data();
                arrayPagos.push({"id":data.id,"puntoId":data.puntoId});
            }
        });
        if(arrayPagos.length <= 0)
        {
            $('#loading-wrapper').hide();
            $('#mensajeDialog').text("Se debe seleccionar al menos un registros para anular pago.");
            $('#mensajeDialogPopUp').modal('show');
        }else{
            $('#reprocesoIndividual').modal('show');
        }
    });

    /**
     * Accion que realizar la ejecucion del la funcion del guardado de los pagos a anular
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#btReprocesoIndividual").click(function (event) {
        $('#reprocesoIndividual').modal('hide');
        $('#loading-wrapper').show();
        reprocesoIndividual();
    });

    /**
     * Accion que realizar un evento click relacionado con el boton de exportar excel, en popup de aceptacion de anulacion
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $("#exportExcelPagos").click(function (event) {
       console.log('exportar excel');
       $(".exportExcelPago").trigger( "click" );
    });


    /**
     * Funcion que realizar la invocacion de guardado de los posibles pagos a anular
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    function reprocesoIndividual()
    {
        var arrayPagos = [];
        listaPagoRealizados.$('input[type="checkbox"]').each(function () {
            if (this.checked)
            {
                var data = listaPagoRealizados.row($(this).parent().parent()).data();
                arrayPagos.push({"id":data.id,"puntoId":data.puntoId});
            }
        });
        if (arrayPagos.length > 0)
        {
            var parametros = {"pagos": arrayPagos};
            $.ajax({
                data: JSON.stringify(parametros),
                url: urlEjecutarAnulacionPago,
                type: 'post',
                contentType: 'application/json; charset=utf-8',
                dataType: 'json',
                async: false,
                success: function (data) {
                    $('#loading-wrapper').hide();
                    if(data.error==true){
                        $('#mensajeDialog').text(data.msg);
                        $('#mensajeDialogPopUp').modal('show');
                    }else{
                        $('#tabla_pagos_realizados').dataTable().fnClearTable();
                        $('#pago-select-all').prop('checked',false);
                        $("#totalSeleccionado").text('$0.00');
                        $('#mensajeDialog').text("Datos almacenados para cargar masiva");
                        $('#mensajeDialogPopUp').modal('show');
                    }
                },
                error: function (data) {
                    $('#loading-wrapper').hide();
                }
            });      
        } else
        {
            $('#loading-wrapper').hide();
            $('#mensajeDialog').text("Se debe seleccionar al menos un registros para anular pago.");
            $('#mensajeDialogPopUp').modal('show');
        }
    }    
    

    /**
     * Configuracion de tabla datatable para mostrar pagos encontrados en la busqueda.
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    var listaPagoRealizados = $('#tabla_pagos_realizados').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "language": idioma,
        "lengthMenu": [[15,25,50,100, -1],[15,25,50,100,"Mostrar Todo"]],
        dom: 'Bfrt<"col-md-6 inline"i> <"col-md-6 inline"p>',
        select:true,
        buttons: {
            dom: {
                container:{
                    tag:'div',
                    className:'flexcontent'
                },
                buttonLiner: {
                  tag: null
                }
            },
            buttons: [
                {
                    extend:    'pageLength',
                    titleAttr: 'Registros a mostrar',
                    className: 'btn btn-app-row selectTable'
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i>Excel',
                    title:'Titulo de tabla en excel',
                    titleAttr: 'Excel',
                    className: 'btn btn-app export excel exportExcelPago',
                    exportOptions: {
                        columns: ':not(.notexport)',
                        rows: '.isSelect'
                    }
                },
                {
                    extend:    'excelHtml5',
                    text:      '<i class="fa fa-file-excel-o"></i>Excel',
                    title:'Titulo de tabla en excel',
                    titleAttr: 'Excel',
                    className: 'btn btn-app export execlError',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8,9,10,11,12],
                        rows: '.errorrow',
                        format: {
                            header: function ( data, columnIdx ) {
                                if(columnIdx==1){
                                    return 'Num.Pago';
                                }
                                else if(columnIdx==2){
                                    return 'Valor';
                                }
                                else if(columnIdx==3){
                                    return 'Error';
                                }
                                return '';
                            }
                        }
                    }
                }
            ],
            select:true,
        },
        "columns": [
            {"data": ""},
            {"data": "numeroPago"},
            {"data": "valorTotal"},
            {"data": "login"},
            {"data": "identificacionCliente"},
            {"data": "nombreCompleto"},
            {"data": "usrCreacion"},
            {"data": "tipoDocumento"},
            {"data": "tipoPago"},
            {"data": "banco"},
            {"data": "canal"},
            {"data": "feCreacion", "render": function ( data, type, full, meta ) {
                if(moment(data,"YYYY-MM-DD").isValid())
                    return moment(data,"YYYY-MM-DD").format("DD-MM-YYYY")
                else if(moment(data,"DD mm YY").isValid())
                    return moment(data,"DD mm YY").format("DD-MM-YYYY");
                else
                    return '';
           }},
           {"data": "estadoPago"},                      
        ],
        "columnDefs": [
            { 
                width: '20%', 
                targets: 5 
            },
            {
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'render': function (data) {
                    return '<input type="checkbox" class="check-box" name="id[]" value="' + $('<div/>').text(data).html() + '">';               
                },
            },
            {
                "render": function (data, type, row) {
                    var i = (type === 'export' ? ($(data).prop("checked")===true ? 'Yes' : 'No') : data);
                    return i;
                },
                "targets": [1,2,3]
            }
        ],
        "createdRow": function (row, data, index) {
            if(data.error!=null && data.error!='0')
            {
                var mensaje='Datos de consulta incorrectos, verificar que numero de pago y valor sean correctos';
                if(data.error=='1'){
                    mensaje='Datos de consulta incompletos, se debe enviar numero de pago y valor';
                }
                $(row).css('background-color', 'rgba(255, 69, 49, 0.3)');
                $(row).addClass('errorrow');
                $('input[type="checkbox"]', row).attr("disabled", true).css('display', 'none');
                $('td:eq(3)', row).attr('colspan', 10);
                $('td:eq(4)', row).css('display', 'none');
                $('td:eq(5)', row).css('display', 'none');
                $('td:eq(6)', row).css('display', 'none');
                $('td:eq(7)', row).css('display', 'none');
                $('td:eq(8)', row).css('display', 'none');
                $('td:eq(9)', row).css('display', 'none');
                $('td:eq(10)', row).css('display', 'none');
                $('td:eq(11)', row).css('display', 'none');
                $('td:eq(12)', row).css('display', 'none');
                this.api().cell($('td:eq(3)', row)).data(mensaje);
            }
        },
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api();
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            // Update footer
            $( api.column( 12 ).footer() ).html(
                '$'+pageTotal.toFixed(2) +' ( $'+ total.toFixed(2) +' total)'
            );
        }
    });

     /**
     * Accion que marca como exprotable o no exportable un fila selecionada,
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $('#tabla_pagos_realizados').on('click','input[type="checkbox"]', function () {
        $(this).parent().parent().toggleClass('isSelect');
        updateSumRow();
    });

    function updateSumRow(){
        var suma=0;
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };
        listaPagoRealizados.$('input[type="checkbox"]').each(function () {
            if (this.checked)
            {
                var data = listaPagoRealizados.row($(this).parent().parent()).data();
                suma=suma+intVal(data.valorTotal);
            }
        });
            $("#totalSeleccionado").text('$'+suma.toFixed(2));
    
    }

    

    /**
     * Accion que se ejecuta en la seleccione de todo el contenido de la tabla de pagos 
     * marca como exportables las filas que no tenga error
     * @author Wilson Quinto <wquinto@telconet.ec>
     */
    $('#pago-select-all').on('click', function () {
        var rows = listaPagoRealizados.rows({'search': 'applied'}).nodes();
        $('input[type="checkbox"]', rows).each(function() {
            if(!$(this).attr('disabled')){
                var checkall= !$(this).prop("checked");
                $(this).prop('checked', checkall);
                if(checkall)
                {
                    $(this).parent().parent().addClass('isSelect');
                }else{
                    $(this).parent().parent().removeClass('isSelect');
                }
            }
        });
        updateSumRow();
    });


});

    
