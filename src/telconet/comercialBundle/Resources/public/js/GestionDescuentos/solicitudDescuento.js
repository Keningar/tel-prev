
var objListadoDescuento;
/**
 * Documentación para la función 'getPanelDescuento'.
 *
 * Función encargada de listar las solicitudes de descuento.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function getPanelDescuento(boolVerTodo = "NO") {
    document.getElementById("AutorizacionInstalacion").style.display = "none";
    document.getElementById("AutorizacionCortesia").style.display = "none";
    document.getElementById("AutorizacionTraslado").style.display = "none";
    document.getElementById("AutorizacionReubicacion").style.display = "none";
    document.getElementById("AutorizacionDescuento").style.display = "";
    objListadoDescuento = $('#objPanelDescuento').DataTable
        ({
            oLanguage: objLanguage,
            "processing": false,
            "bLengthChange": false,
            "serverSide": true,
            "bFilter": false,
            "bSort": false,
            "scrollY": 500,
            "destroy": true,
            "ajax":
            {
                "method": "post",
                "url": url_store_descuento,
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
                    objParametros.fechaDesde = $('#objFechaCreacionDesdeBuscar').val() + "T00:00:00";
                    objParametros.fechaHasta = $('#objFechaCreacionFinBuscar').val() + "T00:00:00";
                    objParametros.identificacion = $("#objIdentificacionBuscar").val();
                    objParametros.login = $("#objLoginBuscar").val();
                    objParametros.nombre = $("#objNombreCltBuscar").val();
                    objParametros.apellido = $("#objApellidoCltBuscar").val();
                    objParametros.razonSocial = $("#objRazonSocialBuscar").val();
                    objParametros.strEstadoFiltro = $('#objEstadoBuscar option:selected').val();
                    objParametros.strIsp = $('#objIspBuscar option:selected').val();
                    objParametros.boolVerTodo = boolVerTodo;
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
                    { data: 'vOriginal' },
                    { data: 'descuento' },
                    { data: 'vFinal' },
                    { data: 'floatPorcentaje' },
                    { data: 'strCargoAsignado' },
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
                    }
                ]
        });
}

/**
 * Documentación para la función 'aprobarSolicitudDescuento'.
 *
 * Función encargada de aprobar las solicitudes de descuento.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function aprobarSolicitudDescuento() {
    var objSolicitud, strEstado, strSolicitudes = "", arraySolictud = [];
    $('#objPanelDescuento tbody tr').each(function () {
        objSolicitud = $(this).find('input[type="checkbox"]').get(0);
        strEstado = $(this).find('td').eq(5).text();
        if (objSolicitud.checked && strEstado.trim() == "Pendiente") {
            strSolicitudes = strSolicitudes + objSolicitud.value.trim() + '|';
            arraySolictud.push(objSolicitud.value.trim());
        }
    });
    if (arraySolictud.length > 0) {
        strSolicitudes = strSolicitudes.substring(0, strSolicitudes.length - 1);
        swal.fire({
            title: "¡Alerta!",
            html: "Se aprobarán las solicitudes seleccionadas <br> ¿Desea continuar?",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonColor: '#4099ff',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Aprobar',
            reverseButtons: false,
        }).then((boolConfirmado) => {
            if (boolConfirmado.isConfirmed) {
                Ext.MessageBox.wait("Procesando petición...");
                $.ajax({
                    url: url_aprobar_descuento,
                    method: 'post',
                    data: { param: strSolicitudes },
                    success: function () {
                        Ext.MessageBox.hide();
                        swal.fire({
                            title: "¡Alerta!",
                            text: "Se aprobaron las solicitudes con éxito.",
                            showConfirmButton: false,
                            icon: "success"
                        });
                        $('#objPanelDescuento').DataTable().ajax.reload();
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
        });
    }
    else {
        swal.fire({
            title: "Error",
            text: "Seleccione por lo menos un registro de la lista en estado Pendiente.",
            showConfirmButton: false,
            icon: "error",
        });
    }
}

/**
 * Documentación para la función 'rechazarSolicitudDescuento'.
 *
 * Función encargada de rechazar las solicitudes de descuento.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 17-08-2022
 *
 */
function rechazarSolicitudDescuento() {
    var objSolicitud, strEstado, strSolicitudes = "", arraySolictud = [];
    $('#objPanelDescuento tbody tr').each(function () {
        objSolicitud = $(this).find('input[type="checkbox"]').get(0);
        strEstado = $(this).find('td').eq(5).text();
        if (objSolicitud.checked && strEstado.trim() == "Pendiente") {
            strSolicitudes = strSolicitudes + objSolicitud.value.trim() + '|';
            arraySolictud.push(objSolicitud.value.trim());
        }
    });
    if (arraySolictud.length > 0) {
        strSolicitudes = strSolicitudes.substring(0, strSolicitudes.length - 1);
        Ext.MessageBox.wait("Cargando motivos de rechazo...");
        $.ajax({
            url: url_lista_motivos_descuento,
            method: 'post',
            success: function (objRespuesta) {
                Ext.MessageBox.hide();
                var objMotivo = new Map();
                objRespuesta.motivos.forEach(arrayItem => {
                    objMotivo.set(arrayItem.idMotivo, arrayItem.descripcion);
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
                                url: url_rechazar_descuento,
                                method: 'post',
                                data: { param: strSolicitudes, motivoId: strMotivo },
                                success: function () {
                                    Ext.MessageBox.hide();
                                    swal.fire({
                                        title: "¡Alerta!",
                                        text: "Se rechazaron las solicitudes con éxito.",
                                        showConfirmButton: false,
                                        icon: "success"
                                    });
                                    $('#objPanelDescuento').DataTable().ajax.reload();
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
    else {
        swal.fire({
            title: "Error",
            text: "Seleccione por lo menos un registro de la lista en estado Pendiente.",
            showConfirmButton: false,
            icon: "error",
        });
    }
}
