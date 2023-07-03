var objListadoReubicacion;

/**
 * Documentación para la función 'getPanelReubicacion'.
 *
 * Función encargada de listar las solicitudes de reubicación.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function getPanelReubicacion() {
    var objPermisoAprobarReubicacion    = $("#ROLE_404-5638");
    var boolPermisoAprobarReubicacion   = (typeof objPermisoAprobarReubicacion === 'undefined') ? false : (objPermisoAprobarReubicacion.val() == 1 ? true : false);
    document.getElementById("AutorizacionReubicacion").style.display = "";
    objListadoReubicacion = $('#objPanelReubicacion').DataTable
        ({
            oLanguage: objLanguage,
            "processing": true,
            "bLengthChange": false,
            "serverSide": true,
            "bSort": false,
            "bFilter": false,
            "scrollY": 500,
            "destroy": true,
            "ajax":
            {
                "method": "post",
                "url": url_store_reubicacion,
                "dataType": "json",
                "dataSrc": "solicitudes",
                "beforeSend": function () {
                    Ext.get(document.body).mask('Cargando Información.');
                },
                "complete": function () {
                    Ext.get(document.body).unmask();
                },
                "error": function () {
                    swal.fire({
                        title: "Error",
                        text: "Por favor comuníquese con el departamento de Sistemas.",
                        showConfirmButton: false,
                        icon: "error"
                    });
                },
                "data": function (objParametros) {
                    objParametros.strTipoSolicitud = $('#objTipoAutorizacionBuscar option:selected').val();
                    objParametros.strFechaDesde = $('#objFechaCreacionDesdeBuscar').val() + "T00:00:00";
                    objParametros.strFechaHasta = $('#objFechaCreacionFinBuscar').val() + "T00:00:00";
                    objParametros.strIdentificacion = $("#objIdentificacionBuscar").val();
                    objParametros.strLogin = $("#objLoginBuscar").val();
                    objParametros.strNombre = $("#objNombreCltBuscar").val();
                    objParametros.strApellido = $("#objApellidoCltBuscar").val();
                    objParametros.strRazonSocial = $("#objRazonSocialBuscar").val();
                    objParametros.strEstadoFiltro = $('#objEstadoBuscar option:selected').val();
                }
            },
            columns:
                [
                    { data: 'id' },
                    { data: 'feCreacion' },
                    { data: 'cliente' },
                    { data: 'login' },
                    { data: 'asesor' },
                    { data: 'estadoSolicitud' },
                    { data: 'servicio' },
                    { data: 'motivo' },
                    { data: 'descuento' },
                    { data: 'observacion' },
                    { data: 'usrCreacion' }
                ],
            "columnDefs":
                [
                    {
                        'targets': 0,
                        'searchable': false,
                        'orderable': false,
                        'render': function (data) {
                            return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
                        }
                    },
                    {
                        "targets": 11,
                        "render": function (data, type, row) {
                            var strAcciones = '';
                            strAcciones = strAcciones + '<button class="button-grid-logs" data-toggle="tooltip" data-placement="bottom" ' +
                                'title="Ver Historial" onClick="javascript:getHistorialReubicacion(\'' + row.id + '\');">' +
                                '</button>';
                            if (row.estadoSolicitud == "Pendiente" && boolPermisoAprobarReubicacion) {
                                strAcciones = strAcciones + '<button class="button-grid-aprobar" data-toggle="tooltip" data-placement="bottom" ' +
                                    'title="Aprobar/Rechazar Solicitud de Reubicación" onClick="javascript:$(\'#objModalAprobarRechazarReubicacion\').modal({ show: true });$(\'#strClienteReubicacion\').val(\'' + row.cliente + '\');' +
                                    '$(\'#strLoginReubicacion\').val(\'' + row.login + '\');$(\'#strCiudadReubicacion\').val(\'' + row.ciudad + '\');$(\'#strDireccionReubicacion\').val(\'' + row.direccion + '\');' +
                                    '$(\'#strSectorReubicacion\').val(\'' + row.nombreSector + '\');$(\'#intIdSolicitudReubicacion\').val(\'' + row.id + '\');"></button>';
                            }
                            return strAcciones;
                        }
                    }
                ]
        });
}

/**
 * Documentación para la función 'getHistorialReubicacion'.
 *
 * Función encargada de mostrar el historial.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function getHistorialReubicacion(intIdSolicitud) {
    $('#objModalHistorialSolicitud').modal({ show: true });
    $('#objPanelHistorialSolicitud').DataTable
        ({
            "oLanguage": {
                "sProcessing": "Procesando...",
                "sEmptyTable": "No hay datos disponibles para su búsqueda",
                "sInfo": "",
                "sInfoEmpty": "",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Siguiente"
                }
            },
            "processing": false,
            "bLengthChange": false,
            "serverSide": true,
            "bSort": false,
            "bFilter": false,
            "destroy": true,
            "ajax":
            {
                "method": "get",
                "url": url_store_historial,
                "dataType": "json",
                "dataSrc": "encontrados",
                "beforeSend": function () {
                    Ext.get(document.body).mask('Cargando Información.');
                },
                "complete": function () {
                    Ext.get(document.body).unmask();
                },
                "error": function () {
                    swal.fire({
                        title: "Error",
                        text: "Por favor comuníquese con el departamento de Sistemas.",
                        showConfirmButton: false,
                        icon: "error"
                    });
                },
                "data": function (objParametros) {
                    objParametros.idSolicitud = intIdSolicitud;
                }
            },
            columns:
                [
                    { data: 'usrCreacion' },
                    { data: 'feCreacion' },
                    { data: 'ipCreacion' },
                    { data: 'estado' },
                    { data: 'nombreMotivo' },
                    { data: 'observacion' }
                ]
        });
}

/**
 * Documentación para la función 'aprobarSolicitudReubicacion'.
 *
 * Función encargada de aprobar las solicitudes de reubicación.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function aprobarSolicitudReubicacion() {
    if ($("#strObservacionReubicacion").val() == "") {
        swal.fire({
            title: "Error",
            text: "Ingrese una observación.",
            showConfirmButton: false,
            icon: "error"
        });
        return;
    }
    else {
        Ext.MessageBox.wait("Procesando petición...");
        $.ajax({
            url: url_aprobar_rechazar_reubicacion,
            method: 'post',
            data: {
                idSolicitud: $("#intIdSolicitudReubicacion").val(),
                observacion: $("#strObservacionReubicacion").val(),
                proceso: "PrePlanificada"
            },
            success: function () {
                Ext.MessageBox.hide();
                swal.fire({
                    title: "¡Alerta!",
                    text: "Se aprobaron las solicitudes con éxito.",
                    showConfirmButton: false,
                    icon: "success"
                });
                getPanelReubicacion();
            },
            error: function () {
                Ext.MessageBox.hide();
                swal.fire({
                    title: "Error",
                    text: "Ha ocurrido un error al intentar aprobar las solicitudes. ¡Por favor comuníquese con el departamento de Sistemas!",
                    showConfirmButton: false,
                    icon: "error"
                });
            }
        });
    }
}

/**
 * Documentación para la función 'rechazarSolicitudReubicacion'.
 *
 * Función encargada de rechazar las solicitudes de reubicación.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function rechazarSolicitudReubicacion() {
    if ($("#strObservacionReubicacion").val() == "") {
        swal.fire({
            title: "Error",
            text: "Ingrese una observación.",
            showConfirmButton: false,
            icon: "error"
        });
        return;
    }
    else {
        Ext.MessageBox.wait("Procesando petición...");
        $.ajax({
            url: url_aprobar_rechazar_reubicacion,
            method: 'post',
            data: {
                idSolicitud: $("#intIdSolicitudReubicacion").val(),
                observacion: $("#strObservacionReubicacion").val(),
                proceso: "Rechazado"
            },
            success: function () {
                Ext.MessageBox.hide();
                swal.fire({
                    title: "¡Alerta!",
                    text: "Se rechazaron las solicitudes con éxito.",
                    showConfirmButton: false,
                    icon: "success"
                });
                getPanelReubicacion();
            },
            error: function () {
                Ext.MessageBox.hide();
                swal.fire({
                    title: "Error",
                    text: "Ha ocurrido un error al intentar rechazar las solicitudes. ¡Por favor comuníquese con el departamento de Sistemas!",
                    showConfirmButton: false,
                    icon: "error"
                });
            }
        });
    }
}