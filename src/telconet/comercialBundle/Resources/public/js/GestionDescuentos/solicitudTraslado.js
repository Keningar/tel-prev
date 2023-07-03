var objListadoTraslado;
/**
 * Documentación para la función 'getPanelTraslado'.
 *
 * Función encargada de listar las solicitudes de traslado.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function getPanelTraslado() {
    document.getElementById("AutorizacionTraslado").style.display = "";
    var objPermisoCambioPrecioTraslado  = $("#ROLE_404-5639");
    var boolPermisoCambioPrecioTraslado = (typeof objPermisoCambioPrecioTraslado === 'undefined') ? false : (objPermisoCambioPrecioTraslado.val() == 1 ? true : false);
    var objPermisoAprobarTraslado       = $("#ROLE_404-5917");
    var boolPermisoAprobarTraslado      = (typeof objPermisoAprobarTraslado === 'undefined') ? false : (objPermisoAprobarTraslado.val() == 1 ? true : false);
    objListadoTraslado = $('#objPanelTraslado').DataTable
        ({
            oLanguage: objLanguage,
            "processing": false,
            "bLengthChange": false,
            "serverSide": true,
            "bSort": false,
            "bFilter": false,
            "scrollY": 500,
            "destroy": true,
            "ajax":
            {
                "method": "post",
                "url": url_store_traslado,
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
                        'render': function (objData) {
                            return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(objData).html() + '">';
                        }
                    },
                    {
                        "targets": 11,
                        "render": function (data, type, row) {
                            var strAcciones = '';
                            strAcciones = strAcciones + '<button class="button-grid-logs" data-toggle="tooltip" data-placement="bottom" ' +
                                'title="Ver Historial" onClick="javascript:getHistorialTraslado(\'' + row.id + '\');">' +
                                '</button>';
                            if (row.estadoSolicitud == "Pendiente" && boolPermisoCambioPrecioTraslado) {
                                strAcciones = strAcciones + '<button class="button-grid-cambioVelocidad" data-toggle="tooltip" data-placement="bottom" ' +
                                    'title="Cambiar Precio de Traslado" onClick="javascript:$(\'#objModalActualizarPrecio\').modal({ show: true });$(\'#strPrecioActual\').val(\'' + row.descuento + '\');$(\'#intIdSolicitud\').val(\'' + row.id + '\');">' +
                                    '</button>';
                            }
                            if (row.estadoSolicitud == "PendienteAutorizar" && boolPermisoAprobarTraslado) {
                                strAcciones = strAcciones + '<button class="button-grid-aprobar" data-toggle="tooltip" data-placement="bottom" ' +
                                    'title="Aprobar/Rechazar Solicitud de Traslado" onClick="javascript:getServiciosAtrasladar(\'' + row.idsServicioTraslado + '\');$(\'#objModalAprobarRechazarTraslado\').modal({ show: true });document.querySelector(\'#strDescTraslado\').innerHTML = \'' + row.observacion + '\';' +
                                    'document.querySelector(\'#strPrecioTraslado\').innerHTML = \'' + row.descuento + '\';document.querySelector(\'#strTipoNegocio\').innerHTML = \'' + row.tipoNegocio + '\';' +
                                    'document.querySelector(\'#strTiempoEsperaMes\').innerHTML = \'' + row.tiempoEsperaMeses + '\';document.querySelector(\'#strSaldo\').innerHTML = \'' + row.saldoPunto + '\';' +
                                    'document.querySelector(\'#intId\').innerHTML = \'' + row.id + '\';document.querySelector(\'#intIdPunto\').innerHTML = \'' + row.id_punto + '\';' +
                                    'document.querySelector(\'#intIdsServiciosTrasladar\').innerHTML = \'' + row.idsServicioTraslado + '\';' +
                                    'document.querySelector(\'#strPtoCliente\').innerHTML = \'' + row.login + '\';document.querySelector(\'#strCliente\').innerHTML = \'' + row.cliente + '\';"></button>';
                            }
                            return strAcciones;
                        }
                    }
                ]
        });
}

/**
 * Documentación para la función 'getHistorialTraslado'.
 *
 * Función encargada de mostrar el historial.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function getHistorialTraslado(intIdSolicitud) {
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
 * Documentación para la función 'actualizarPrecioTraslado'.
 *
 * Función encargada de actualizar Valor Precio Traslado.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function actualizarPrecioTraslado() {
    var objExpRegular = /^[0-9]+([.][0-9]{1,2})?$/;
    if ($("#strObservacion").val() == "" || $("#strPrecioNuevo").val() == "") {
        swal.fire({
            title: "Error",
            text: "Estimado usuario, debe llenar todos los campos.",
            showConfirmButton: false,
            icon: "error"
        });
        return;
    }
    if (!objExpRegular.test($("#strPrecioNuevo").val())) {
        swal.fire({
            title: "Error",
            text: "Por favor ingresar solo números enteros o decimales.",
            showConfirmButton: false,
            icon: "error"
        });
        return;
    }
    else {
        $.ajax({
            url: url_actualiza_precio_traslado,
            method: 'post',
            data: {
                idSolicitud: $("#intIdSolicitud").val(),
                precioNuevo: $("#strPrecioNuevo").val(),
                observacion: $("#strObservacion").val()
            },
            success: function (objResponse) {
                if (objResponse.strStatus == "OK") {
                    swal.fire({
                        title: "¡Alerta!",
                        text: objResponse.strMensaje,
                        showConfirmButton: false,
                        icon: "success"
                    });
                    getPanelTraslado();
                }
                else {
                    swal.fire({
                        title: "Error",
                        text: objResponse.strMensaje,
                        showConfirmButton: false,
                        icon: "error"
                    });
                }
            },
            beforeSend: function () {
                Ext.get(document.body).mask('Actualizando precio.');
            },
            complete: function () {
                Ext.get(document.body).unmask();
            },
            error: function () {
                swal.fire({
                    title: "Error",
                    text: "No se pudo actualizar el precio del traslado. Por favor comuníquese con el departamento de Sistemas.",
                    showConfirmButton: false,
                    icon: "error"
                });
            }
        });
    }
}

/**
 * Documentación para la función 'getServiciosAtrasladar'.
 *
 * Función encargada de mostrar los servicios a trasladar.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function getServiciosAtrasladar(strServiciosTrasladar) {
    $('#objPanelServiciosAtrasladar').DataTable
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
                "url": url_store_servicios_aTrasladar,
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
                    objParametros.idsServiciosTraslado = strServiciosTrasladar;
                }
            },
            columns:
                [
                    { data: 'servicio' },
                    { data: 'estado' }
                ]
        });
}

/**
 * Documentación para la función 'aprobarSolicitudTraslado'.
 *
 * Función encargada de aprobar las solicitudes de traslado.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function aprobarSolicitudTraslado() {
    Ext.MessageBox.wait("Procesando petición...");
    $.ajax({
        url: url_aprobar_traslado,
        method: 'post',
        data: {
            idDetalleSolicitud: document.querySelector("#intId").innerHTML,
            banderaAutorizarSol: "S",
            idsServiciosTrasladar: document.querySelector("#intIdsServiciosTrasladar").innerHTML,
            idPuntoSession: document.querySelector("#intIdPunto").innerHTML,
            precioTrasladoTn: document.querySelector("#strPrecioTraslado").innerHTML,
            descripcionTrasladoTn: document.querySelector("#strDescTraslado").innerHTML
        },
        success: function () {
            Ext.MessageBox.hide();
            swal.fire({
                title: "¡Alerta!",
                text: "Se aprobaron las solicitudes con éxito.",
                showConfirmButton: false,
                icon: "success"
            });
            getPanelTraslado();
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

/**
* Documentación para la función 'rechazarSolicitudTraslado'.
*
* Función encargada de rechazar las solicitudes de traslado.
*
* @author Kevin Baque Puya <kbaque@telconet.ec>
* @version 1.0 17-08-2022
*
*/
function rechazarSolicitudTraslado() {
    Ext.MessageBox.wait("Cargando motivos de rechazo...");
    $.ajax({
        url: url_lista_motivos_traslado,
        method: 'post',
        success: function (objRespuesta) {
            Ext.MessageBox.hide();
            var objMotivo = new Map();
            objRespuesta.encontrados.forEach(arrayItem => {
                objMotivo.set(arrayItem.id_motivo, arrayItem.nombre_motivo);
            });
            Swal.fire({
                title: 'Seleccione un motivo de rechazo',
                input: 'select',
                inputOptions: objMotivo,
                inputPlaceholder: 'Seleccione...',
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Rechazar',
                inputValidator: (strMotivo) => {
                    if (strMotivo == "") {
                        swal.fire({
                            title: "Error",
                            text: "Seleccione un motivo de rechazo.",
                            showConfirmButton: false,
                            icon: "error"
                        });
                    }
                    else {
                        Ext.MessageBox.wait("Procesando petición...");
                        $.ajax({
                            url: url_rechazar_traslado,
                            method: 'post',
                            data: {
                                idDetalleSolicitud: document.querySelector("#intId").innerHTML,
                                idPunto: document.querySelector("#intIdPunto").innerHTML,
                                motivo: strMotivo,
                                descripcion: document.querySelector("#strDescTraslado").innerHTML
                            },
                            success: function () {
                                Ext.MessageBox.hide();
                                swal.fire({
                                    title: "¡Alerta!",
                                    text: "Se rechazaron las solicitudes con éxito.",
                                    showConfirmButton: false,
                                    icon: "success"
                                });
                                getPanelTraslado();
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
            });
        },
        error: function () {
            Ext.MessageBox.hide();
            swal.fire({
                title: "Error",
                text: "No se pudieron cargar los motivos de rechazo. Por favor comuníquese con el departamento de Sistemas.",
                showConfirmButton: false,
                icon: "error"
            });
        }
    });

}