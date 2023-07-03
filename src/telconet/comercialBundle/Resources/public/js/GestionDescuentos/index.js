$(document).ready(function () {
    var objPermisoApReDoc = $("#ROLE_443-7037");
    var boolPermisoApReDoc = (typeof objPermisoApReDoc === 'undefined') ? false : (objPermisoApReDoc.val() == 1 ? true : false);
    var boolVerTodoDescuento = "NO";
    var boolVerTodoInstalacion = "NO";
    $('#objFechaCreacionDesdeBuscar').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'es-es',
        dateFormat: 'yy-mm-dd'
    });
    $('#objFechaCreacionFinBuscar').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'es-es',
        dateFormat: 'yy-mm-dd'
    });
    $("#limpiar").click(function () {
        limpiarFormBuscar();
    });
    /**
    * Documentación para la función 'validarFecha'.
    *
    * Función que valida el ingreso de las fechas.
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2022
    *
    */
    function validarFecha() {
        var objFechaCreacionDesdeBuscar = $('#objFechaCreacionDesdeBuscar').val();
        var objFechaCreacionFinBuscar = $('#objFechaCreacionFinBuscar').val();
        if (objFechaCreacionDesdeBuscar > objFechaCreacionFinBuscar) {
            swal.fire({
                title: "Error",
                text: "La 'fecha creación desde' no debe ser mayor a la 'fecha de creación hasta'.",
                showConfirmButton: false,
                icon: "error"
            });
            return false;
        }
        return true;
    }

    objLanguage = {
        "sProcessing": "Procesando...",
        "sEmptyTable": "No hay datos disponibles para su búsqueda",
        "sZeroRecords": "No hay datos disponibles para su búsqueda",
        "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
        "oPaginate": {
            "sPrevious": "Anterior",
            "sNext": "Siguiente"
        }
    };
    ocutarDiv();
    getTipoAutorizacion();
    getEstado();
    getPanelDescuento(boolVerTodoDescuento);
    $('#objTipoAutorizacionBuscar').change(function () {
        ocutarDiv();
        if ($('#objTipoAutorizacionBuscar').val() == "AUTORIZACION_DESCUENTO") {
            $("#objIsp").show();
            $("#objIspBuscar").show();
            getPanelDescuento(boolVerTodoDescuento);
        }
        else if ($('#objTipoAutorizacionBuscar').val() == "AUTORIZACION_INSTALACION") {
            $('#objIsp').val("No");
            $("#objIsp").hide();
            $("#objIspBuscar").hide();
            getPanelInstalacion(boolVerTodoInstalacion);
        }
        else if ($('#objTipoAutorizacionBuscar').val() == "AUTORIZACION_CAMBIO_DOCUMENTO") {
            $('#objIsp').val("No");
            $("#objIsp").hide();
            $("#objIspBuscar").hide();
            getPanelCortesia();
        }
        else if ($('#objTipoAutorizacionBuscar').val() == "SOLICITUD_TRASLADO") {
            $('#objIsp').val("No");
            $("#objIsp").hide();
            $("#objIspBuscar").hide();
            getPanelTraslado();
        }
        else if ($('#objTipoAutorizacionBuscar').val() == "SOLICITUD_REUBICACION") {
            $('#objIsp').val("No");
            $("#objIsp").hide();
            $("#objIspBuscar").hide();
            getPanelReubicacion();
        }
        else {
            swal.fire({
                title: "Error",
                text: "Seleccione un tipo de autorización.",
                showConfirmButton: false,
                icon: "error"
            });
        }
    });
    if (boolAutorizacion) {
        $("#objDivAutorizacionDescuento").show();
        $("#objDivAutorizacionInstalacion").show();
    }
    else {
        $("#objDivAutorizacionDescuento").hide();
        $("#objDivAutorizacionInstalacion").hide();
    }
    /**
     * Documentación para la función 'buscar'.
     *
     * Función encargada listar las solicitudes de acuerdo al tipo.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2022
     *
     */
    $("#objBuscar").click(function () {
        ocutarDiv();
        if (validarFecha()) {
            if ($('#objTipoAutorizacionBuscar').val() == "AUTORIZACION_DESCUENTO") {
                getPanelDescuento(boolVerTodoDescuento);
            }
            else if ($('#objTipoAutorizacionBuscar').val() == "AUTORIZACION_INSTALACION") {
                getPanelInstalacion(boolVerTodoInstalacion);
            }
            else if ($('#objTipoAutorizacionBuscar').val() == "AUTORIZACION_CAMBIO_DOCUMENTO") {
                getPanelCortesia();
            }
            else if ($('#objTipoAutorizacionBuscar').val() == "SOLICITUD_TRASLADO") {
                getPanelTraslado();
            }
            else if ($('#objTipoAutorizacionBuscar').val() == "SOLICITUD_REUBICACION") {
                getPanelReubicacion();
            }
            else {
                ocutarDiv();
                swal.fire({
                    title: "Error",
                    text: "Seleccione un tipo de autorización.",
                    showConfirmButton: false,
                    icon: "error"
                });
            }
        }
    });

    /**
     * Documentación para la función 'getTipoAutorizacion'.
     *
     * Función encargada de retornar los tipos de autorizaciones.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2022
     *
     */
    function getTipoAutorizacion() {
        $.ajax({
            url: url_lista_tipo_autorizacion,
            method: 'post',
            success: function (data) {
                $.each(data.tipoAutorizacion, function (id, registro) {
                    $("#objTipoAutorizacionBuscar").append('<option value=' + registro.descripTecnica + '>' + registro.descripcion + '</option>');
                });
                $("#objTipoAutorizacionBuscar").val('AUTORIZACION_DESCUENTO');
            },
            error: function () {
                swal.fire({
                    title: "Error",
                    text: "No se pudieron cargar los tipos de autorizaciones. Por favor comuníquese con el departamento de Sistemas.",
                    showConfirmButton: false,
                    icon: "error"
                });
            }
        });
    }

    /**
     * Documentación para la función 'getEstado'.
     *
     * Función encargada de retornar los estados de autorizaciones.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2022
     *
     */
     function getEstado() {
        $.ajax({
            url: url_lista_estado,
            method: 'post',
            success: function (data) {
                $.each(data.estados, function (id, registro) {
                    $("#objEstadoBuscar").append('<option value=' + registro.descripcion + '>' + registro.descripcion + '</option>');
                });
                $("#objEstadoBuscar").val('Pendiente');
            },
            error: function () {
                swal.fire({
                    title: "Error",
                    text: "No se pudieron cargar los tipos de autorizaciones. Por favor comuníquese con el departamento de Sistemas.",
                    showConfirmButton: false,
                    icon: "error"
                });
            }
        });
    }
    //Bloque que contiene funciones de las solicitudes de descuento.
    $('#objListadoDescuento-select-all').on('click', function () {
        var rows = objListadoDescuento.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });
    $('#objPanelDescuento tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) {
            var el = $('#objListadoDescuento-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });
    $("#objVerTodoDescuento").click(function () {
        if (document.getElementById("objVerTodoDescuento").innerHTML.trim() == "Ver todo") {
            boolVerTodoDescuento = "SI";
            document.getElementById("objVerTodoDescuento").innerHTML = "Ver Asignado";
            $("#objAprobarDescuento").hide();
            $("#objRechazarDescuento").hide();
            getPanelDescuento(boolVerTodoDescuento);
        }
        else if (document.getElementById("objVerTodoDescuento").innerHTML == "Ver Asignado") {
            boolVerTodoDescuento = "NO";
            document.getElementById("objVerTodoDescuento").innerHTML = "Ver todo";
            $("#objAprobarDescuento").show();
            $("#objRechazarDescuento").show();
            getPanelDescuento(boolVerTodoDescuento);
        }
    });
    $("#objAprobarDescuento").click(function () {
        aprobarSolicitudDescuento();
    });
    $("#objRechazarDescuento").click(function () {
        rechazarSolicitudDescuento();
    });
    //Bloque que contiene funciones de las solicitudes de instalación.
    $("#objVerTodoInstalacion").click(function () {
        if (document.getElementById("objVerTodoInstalacion").innerHTML.trim() == "Ver todo") {
            boolVerTodoInstalacion = "SI";
            document.getElementById("objVerTodoInstalacion").innerHTML = "Ver Asignado";
            $("#objAprobarInstalacion").hide();
            $("#objRechazarInstalacion").hide();
            getPanelInstalacion(boolVerTodoInstalacion);
        }
        else if (document.getElementById("objVerTodoInstalacion").innerHTML == "Ver Asignado") {
            boolVerTodoInstalacion = "NO";
            document.getElementById("objVerTodoInstalacion").innerHTML = "Ver todo";
            $("#objAprobarInstalacion").show();
            $("#objRechazarInstalacion").show();
            getPanelInstalacion(boolVerTodoInstalacion);
        }
    });
    $('#objListadoInstalacion-select-all').on('click', function () {
        var rows = objListadoInstalacion.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });
    $('#objPanelInstalacion tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) {
            var el = $('#objListadoInstalacion-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });
    $("#objAprobarInstalacion").click(function () {
        aprobarSolicitudInstalacion();
    });
    $("#objRechazarInstalacion").click(function () {
        rechazarSolicitudInstalacion();
    });
    //Bloque que contiene funciones de las solicitudes de cortesía.
    $('#objListadoCortesia-select-all').on('click', function () {
        var rows = objListadoCortesia.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });
    $('#objPanelCortesia tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) {
            var el = $('#objListadoCortesia-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });
    if (boolPermisoApReDoc) {
        $("#objAprobarCortesia").show();
        $("#objRechazarCortesia").show();
    }
    else {
        $("#objAprobarCortesia").hide();
        $("#objRechazarCortesia").hide();
    }
    $("#objAprobarCortesia").click(function () {
        aprobarSolicitudCortesia();
    });
    $("#objRechazarCortesia").click(function () {
        rechazarSolicitudCortesia();
    });
    //Bloque que contiene funciones de las solicitudes de traslado.
    $('#objListadoTraslado-select-all').on('click', function () {
        var rows = objListadoTraslado.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });
    $('#objPanelTraslado tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) {
            var el = $('#objListadoTraslado-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });
    $("#objActualizarPrecio").click(function () {
        actualizarPrecioTraslado();
    });
    $("#objAprobarTraslado").click(function () {
        aprobarSolicitudTraslado();
    });
    $("#objRechazarTraslado").click(function () {
        rechazarSolicitudTraslado();
    });
    //Bloque que contiene funciones de las solicitudes de reubicación.
    $('#objListadoReubicacion-select-all').on('click', function () {
        var rows = objListadoReubicacion.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });
    $('#objPanelReubicacion tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) {
            var el = $('#objListadoReubicacion-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });
    $("#objAprobarReubicacion").click(function () {
        aprobarSolicitudReubicacion();
    });
    $("#objRechazarReubicacion").click(function () {
        rechazarSolicitudReubicacion();
    });

    /**
     * Documentación para la función 'ocutarDiv'.
     *
     * Función encargada de ocultar las autorizaciones.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2022
     *
     */
    function ocutarDiv() {
        document.getElementById("AutorizacionDescuento").style.display = "none";
        document.getElementById("AutorizacionInstalacion").style.display = "none";
        document.getElementById("AutorizacionCortesia").style.display = "none";
        document.getElementById("AutorizacionTraslado").style.display = "none";
        document.getElementById("AutorizacionReubicacion").style.display = "none";
    }

    /**
     * Documentación para la función 'limpiarFormBuscar'.
     *
     * Función encargada de limpiar los campos.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 17-08-2022
     *
     */
    function limpiarFormBuscar() {
        ocutarDiv();
        $('#objFechaCreacionDesdeBuscar').val("");
        $('#objFechaCreacionFinBuscar').val("");
        $('#objIdentificacionBuscar').val("");
        $('#objLoginBuscar').val("");
        $('#objNombreCltBuscar').val("");
        $('#objApellidoCltBuscar').val("");
        $('#objRazonSocialBuscar').val("");
        $('#objEstadoBuscar').val("Pendiente");
        $('#objIsp').val("No");
        if ($('#objTipoAutorizacionBuscar').val() == "AUTORIZACION_DESCUENTO") {
            getPanelDescuento(boolVerTodoDescuento);
        }
        else if ($('#objTipoAutorizacionBuscar').val() == "AUTORIZACION_INSTALACION") {
            getPanelInstalacion(boolVerTodoInstalacion);
        }
        else if ($('#objTipoAutorizacionBuscar').val() == "AUTORIZACION_CAMBIO_DOCUMENTO") {
            getPanelCortesia();
        }
        else if ($('#objTipoAutorizacionBuscar').val() == "SOLICITUD_TRASLADO") {
            getPanelTraslado();
        }
        else if ($('#objTipoAutorizacionBuscar').val() == "SOLICITUD_REUBICACION") {
            getPanelReubicacion();
        }
        else {
            ocutarDiv();
        }
    }
});