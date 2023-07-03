
$(document).ready(function () {

    /**
     * Obtiene la empresa en sesión
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 28-02-2023
     * @since 1.0
     */
    $.ajax({
        url: urlGetEmpresaEnSesion,
        method: 'GET',
        success: function (data) {
            prefijoEmpresa = data.prefijoEmpresa;
            if(prefijoEmpresa !== "MD"){
                $("#opcionFranjaHoraria").hide();
            }
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudo obtener la empresa en sesión. Por favor consulte con el Administrador");
            $('#modalMensajes').modal({show: true});
        }
    });
    
    /**
     * Inicializa con valores vacíos el formulario de búsqueda.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 15-03-2019
     * @since 1.0
     */
    limpiarFormBuscar();

    /**
     * Inicializa calendario de Fecha-Inicio de Vigencia.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 15-03-2019
     * @since 1.0
     */
    $('#inicio_vigencia_buscar').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy-mm-dd'
    });

    /**
     * Obtiene los estados de promoción.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 15-03-2019
     * @since 1.0
     */
    $.ajax({
        url: url_estados_promocion,
        method: 'GET',
        success: function (data) {
            $.each(data.estados, function (id, registro) {
                $("#estado_promo_buscar").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Estados de Promoción. Por favor consulte con el Administrador");
            $('#modalMensajes').modal({show: true});
        }
    });


    /**
     * Obtiene el listado de promociones.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 15-03-2019
     * @since 1.0
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.1 15-03-2022 - Se agrega que se envíe el intIdPromocion del objeto data al seleccionar un registro en el checkbox. 
     */
    var listaPromociones = $('#tabla_lista_promociones').DataTable({
        "ajax": {
            "url": url_grid_promociones,
            "type": "POST",
            "data": function (param) {
                param.strNombrePromo    = $("#nombre_promo_buscar").val();
                param.strInicioVigencia = $('#inicio_vigencia_buscar').val();
                param.strEstadoPromo    = $('#estado_promo_buscar option:selected').val();
            }
        },
        "language": {
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "Cargando datos...",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "loadingRecords": "Cargando datos..."
        },
        "columns": [
            {"data": "intIdPromocion"},
            {"data": "strTipoPromocion"},
            {"data": "strNombrePromocion"},
            {"data": "strFechaInicioVigencia"},
            {"data": "strFechaFinVigencia"},
            {"data": "strEstado"},
            {"data": "strFeCreacion"},
            {"data": "strUsrCreacion"},
            {"data": "strAcciones",
                "render": function (data){
                    var strDatoRetorna = '';
                    if (data.linkVer !== '') 
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" title="Ver Promoción" ' +                            
                            'onClick="javascript:mostrarModalDetalle(\'' + data.linkVer + '\');"> <i class="fa fa-search"></i>' +
                            '</button>&nbsp;';
                    }
                    /*if (data.linkEditar !== '' && strIsGrantedEditar === 'S')
                    {*/
                        strDatoRetorna += '<button type="button" class="btn btn-outline-warning btn-sm" title="Editar Promoción" ' +                            
                            'onClick="window.location.href=\'' + data.linkEditar + '\'"> <i class="fa fa-pencil"></i> </button>&nbsp;';
                    //}
                    if (data.linkDetener !== '')
                    {
                        intIdPromo = data.linkDetener.intIdPromocion;
                        strTipoPromo = data.linkDetener.strTipoPromocion;
                        strDatoRetorna += '<button type="button" class="btn btn-outline-danger btn-sm" title="Detener Promoción" ' +                            
                            'onClick="javascript:detenerPromocion(\'' + intIdPromo + '\',\''+ strTipoPromo + '\');"> <i class="fa fa-ban"></i> </button>&nbsp;';
                    }
                    if (data.linkAnular !== '')
                    {
                        intIdPromoAnular = data.linkAnular.intIdPromocion;
                        strTipoPromoAnular = data.linkAnular.strTipoPromocion;
                        strEstadoPromoAnular = data.linkAnular.strEstadoPromocion;
                        strDatoRetorna += '<button type="button" class="btn btn btn-outline-success btn-sm" title="Anular Promoción" ' +                            
                            'onClick="javascript:anularPromocion(\'' + intIdPromoAnular + '\',\''+ strTipoPromoAnular + '\',\''+ 
                            strEstadoPromoAnular + '\');"> <i class="fa fa-times"></i> </button>&nbsp;';
                    }
                    return strDatoRetorna;          
                }
            }
        ],
        'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'render': function (data) {
                return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
            }
        }]
    });

    $('#promocion-select-all').on('click', function () {
        var rows = listaPromociones.rows({'search': 'applied'}).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    $('#tabla_lista_promociones tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) 
        {
            var el = $('#promocion-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) 
            {
                el.indeterminate = true;
            }
        }
    });


    /**
     * Agrega botones de Clonar e Inactivar, en la página principal.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 15-03-2019
     * @since 1.0
     */
    if (strIsGrantedClonar === 'S')
    {
    $("#tabla_lista_promociones_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalClonar" '+
        'class="btn btn-info btn-sm" title="Clonar" <i class="fa fa-files-o"></i> Clonar </button>');
    }
    if (strIsGrantedAnular === 'S')
    {
    $("#tabla_lista_promociones_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalInactivar" ' +
        'class="btn btn-info btn-sm" title="Anular Promociones" <i class="fa fa-ban"></i> Anular </button>');

    }

    if (strIsGrantedInactivar === 'S')
    {

     $("#tabla_lista_promociones_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalInactivarVigencias" ' +
        'class="btn btn-info btn-sm" title="Inactivar Promociones" <i class="fa fa-ban"></i> Inactivar </button>');
    }

    $("#btInactivarPromociones").click(function () {
        inactivarPromociones();
    });
     $("#btClonarPromociones").click(function () {
        clonarPromociones();
    });
    $("#btInactivarPromocionesVigentes").click(function () {
        inactivarPromocionesVigentes();
    });

    /**
     * Obtiene los motivos relacionados a las promociones.
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 15-03-2019
     * @since 1.0
     */
    $.ajax({
        url: urlGetMotivos,
        method: 'GET',
        success: function (data) {
            $.each(data.motivos, function (id, registro) {
                $("#motivo_inactivar").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                $("#motivo_clonar").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
                $("#motivo_inactivar_vigente").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Motivos. Por favor consulte con el Administrador");
            $('#modalMensajes').modal({show: true});
        }
    });

    $('#motivo_inactivar,#motivo_clonar','#motivo_inactivar_vigente').select2({
        placeholder: 'Seleccione un motivo'
    });


    /**
     * Realiza la llamada a la función Ajax que genera el Proceso masivo,
     * para la Inactivación de las Promociones
     *    
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 04-04-2019
     * @since 1.0
     */
    function inactivarPromociones()
    {
        var arrayIdsGrupoPromocion = [];
        listaPromociones.$('input[type="checkbox"]').each(function () {
            if (this.checked)
            {
                arrayIdsGrupoPromocion.push(this.value);
            }
        });
        if (arrayIdsGrupoPromocion.length > 0)
        {
            var parametros = {
                "intIdMotivo": $("#motivo_inactivar").val(),
                "strObservacion": $("#observacion_inactivar").val(),
                "arrayIdsGrupoPromocion": arrayIdsGrupoPromocion,
                "strTipoPma": 'InactivarPromo',
                "strAccion": 'Anular'
            };
            $.ajax({
                data: parametros,
                url: urlAjaxInactivarPromociones,
                type: 'post',
                success: function (response) {
                    if (response)
                    {                        
                        $('#observacion_inactivar').val('');
                        $('#tabla_lista_promociones').DataTable().ajax.reload();
                        $('#modalMensajes .modal-body').html(response);
                        $('#modalMensajes').modal({show: true});                        
                    }
                },
                failure: function (response) {
                    $('#modalMensajes .modal-body').html('No se pudo Anular la(s) Promocion(e)s existe un error: ' + response);
                    $('#modalMensajes').modal({show: true});
                }
            });
        } else
        {
            $('#modalMensajes .modal-body').html('Seleccione por lo menos una Promoción de la lista.');
            $('#modalMensajes').modal({show: true});
        }
    } 
    
    
    /**
     * Realiza la llamada a la función Ajax que genera el Proceso masivo,
     * para la Inactivación de las Promociones vigentes
     *    
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 05-10-2020
     * @since 1.0
    */
    function inactivarPromocionesVigentes()
    {
        var arrayIdsGrupoPromocion = [];
        listaPromociones.$('input[type="checkbox"]').each(function () {
            if (this.checked)
            {
                arrayIdsGrupoPromocion.push(this.value);
            }
        });
        if (arrayIdsGrupoPromocion.length > 0)
        {
            var parametros = {
                "intIdMotivo": $("#motivo_inactivar_vigente").val(),
                "strObservacion": $("#observacion_inactivar_vigente").val(),
                "arrayIdsGrupoPromocion": arrayIdsGrupoPromocion,
                "strTipoPma": 'InactPromoVigente',
                "strAccion": 'Inactivar'
            };
            $.ajax({
                data: parametros,
                url: urlAjaxInactivarPromociones,
                type: 'post',
                success: function (response) {
                    if (response)
                    {                        
                        $('#observacion_inactivar').val('');
                        $('#tabla_lista_promociones').DataTable().ajax.reload();
                        $('#modalMensajes .modal-body').html(response);
                        $('#modalMensajes').modal({show: true});                        
                    }
                },
                failure: function (response) {
                    $('#modalMensajes .modal-body').html('No se pudo Inactivar la(s) Promocion(e)s existe un error: ' + response);
                    $('#modalMensajes').modal({show: true});
                }
            });
        } else
        {
            $('#modalMensajes .modal-body').html('Seleccione por lo menos una Promoción de la lista.');
            $('#modalMensajes').modal({show: true});
        }
    } 
    
    /**
     * Realiza la llamada a la función Ajax que genera el Proceso masivo,
     * para la clonación de las Promociones.
     *    
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 04-04-2019
     * @since 1.0
     */ 
    function clonarPromociones()
    {
        var arrayIdsGrupoPromocion = [];
        listaPromociones.$('input[type="checkbox"]').each(function () {
            if (this.checked)
            {
                arrayIdsGrupoPromocion.push(this.value);
            }
        });
        if (arrayIdsGrupoPromocion.length > 0)
        {
            var parametros = {
                "intIdMotivo": $("#motivo_clonar").val(),
                "strObservacion": $("#observacion_clonar").val(),
                "arrayIdsGrupoPromocion": arrayIdsGrupoPromocion
            };
            $.ajax({
                data: parametros,
                url: urlAjaxClonarPromociones,
                type: 'post',
                success: function (response) {
                    if (response)
                    {                        
                        $('#observacion_clonar').val('');
                        $('#tabla_lista_promociones').DataTable().ajax.reload();
                        $('#modalMensajes .modal-body').html(response);
                        $('#modalMensajes').modal({show: true});
                    }
                },
                failure: function (response) {
                    $('#modalMensajes .modal-body').html('No se pudo Clonar la(s) Promocion(e)s existe un error: ' + response);
                    $('#modalMensajes').modal({show: true});
                }
            });
        } else
        {
            $('#modalMensajes .modal-body').html('Seleccione por lo menos una Promoción de la lista.');
            $('#modalMensajes').modal({show: true});
        }
    } 

    $("#buscar_promocion").click(function () {
        $('#tabla_lista_promociones').DataTable().ajax.reload();
    });

    $("#limpiar_formPromocion").click(function () {
        limpiarFormBuscar();
    });

    function limpiarFormBuscar() {
        $('#nombre_promo_buscar').val("");
        $('#inicio_vigencia_buscar').val("");
        $('#estado_promo_buscar').val("");
    }

    $('form').keypress(function (e) {
        if (e === 13) {
            return false;
        }
    });

    $('input').keypress(function (e) {
        if (e.which === 13) {
            return false;
        }
    });
    setInterval(function(){ $('#tabla_lista_promociones').DataTable().ajax.reload(); }, 300000);
});

    
/**
 * Muestra una ventana modal con el detalle de la Promoción consultada,
 *    
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 04-04-2019
 * @since 1.0
 */
function mostrarModalDetalle(url_accion) {
    $.ajax({
        url: url_accion,
        type: 'get',
        dataType: "html",
        success: function (response) {
            $('#modalDetalle .modal-body').html(response);
            $('#modalDetalle').modal({show: true});
        },
        error: function () {
            $('#modalDetalle .modal-body').html('<p>Ocurrió un error, por favor consulte con el Administrador.</p>');
            $('#modalDetalle').modal('show');
        }
    });
}

/**
 * Muestra una alerta para validar si desea inactivar la promocion,
 *    
 * @author Daniel Reyes <djreyes@telconet.ec>
 * @version 1.0 30-11-2021
 * @since 1.0
 * 
 */
function detenerPromocion(intIdPromocion, strTipoPromocion) {
    Ext.Msg.confirm('Alerta','Seguro desea detener esta promocion?', function(btn){
        if(btn == 'yes'){
            var parametros = {
                "intIdPromocion": intIdPromocion,
                "strTipoPromocion": strTipoPromocion
            };
            $.ajax({
                data: parametros,
                type: "POST",
                url: url_detener_promocion,
                timeout: 300000,
                success: function (data) {
                    if (data.result === 'OK') {
                        $('#modalMensajes .modal-body').html('Se inicia proceso para detener promoción con éxito.');
                        $('#modalMensajes').modal({show: true});
                        location.reload();
                    } else {
                        $('#modalMensajes .modal-body').html(data.message);
                        $('#modalMensajes').modal({show: true});
                    }
                },
                error: function(xmlhttprequest, textstatus, message) {
                    if(textstatus === "timeout") {
                        $('#modalMensajes .modal-body').html('Se inicia proceso para detener promoción con éxito.');
                        $('#modalMensajes').modal({show: true});
                        location.reload();
                    } else {
                        $('#modalMensajes .modal-body').html('Ocurrió un error, por favor consulte con el Administrador.');
                        $('#modalMensajes').modal({show: true});
                    }
                }
            });
        }
    });
}

/**
 * Proceso que muestra una alerta para validar si desea anular la promocion,
 * y de ser afirmativa la anula por completo
 *    
 * @author Daniel Reyes <djreyes@telconet.ec>
 * @version 1.0 11-04-2022
 * @since 1.0
 * 
 */
function anularPromocion(intIdPromoAnular, strTipoPromoAnular, strEstadoPromoAnular) {
    Ext.Msg.confirm('Alerta','Seguro desea anular esta promocion?', function(btn){
        if(btn == 'yes'){
            var parametros = {
                "intIdPromocion": intIdPromoAnular,
                "strTipoPromocion": strTipoPromoAnular,
                "strEstadoPromocion": strEstadoPromoAnular
            };
            $.ajax({
                data: parametros,
                type: "POST",
                url: url_anular_promocion,
                success: function (data) {
                    if (data.result === 'OK') {
                        $('#modalMensajes .modal-body').html('Se anulo la promocion con exito.');
                        $('#modalMensajes').modal({show: true});
                        location.reload();
                    } else {
                        $('#modalMensajes .modal-body').html(data.message);
                        $('#modalMensajes').modal({show: true});
                    }
                },
                error: function(xmlhttprequest, textstatus, message) {
                    $('#modalMensajes .modal-body').html('Ocurrió un error, por favor consulte con el Administrador.');
                    $('#modalMensajes').modal({show: true});
                }
            });
        }
    });
}
