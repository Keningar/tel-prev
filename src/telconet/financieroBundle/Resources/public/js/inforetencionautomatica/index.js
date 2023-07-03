/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var itemsPerPage = 10;
var store = '';
var estado_id = '';
var area_id = '';
var login_id = '';
var tipo_asignacion = '';
var pto_sucursal = '';
var idClienteSucursalSesion;
var idRetencionEliminar = 0;

$(document).ready(function () {

    $('#fecha_desde').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy-mm-dd'
    });
    

    $('#fecha_hasta').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy-mm-dd'
    });
     
    $('#fecha_autorizacion').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy-mm-dd'
    });
    
    /**
    * Obtiene listado de motivos
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 10-06-2021
    * @since 1.0
    */   
    $.ajax({
        url: urlGetMotivos,
        method: 'GET',
        success: function (data) {
            $.each(data.lista_motivos, function (id, registro) {
                $("#motivos_retencion").append('<option value=' + registro.id + '>' + registro.descripcion + '</option>');
            });
        },
        error: function () {            
            $('#modalMensajes .modal-body').html("No se pueden cargar los motivos");
            $('#modalMensajes').modal({show: true});
        }
    });
    $('#motivos_retencion').select2({
        placeholder:'Seleccione un motivo'
     });     

    /**
     * Obtiene el listado de retenciones.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 01-02-2021
     * @since 1.0
     */    
    var listaInfoPagoAutomaticoCab = $('#tabla_lista_retenciones').DataTable({
        "ajax": {
            "url": urlGridInfoPagoAutomatico,
            "type": "POST",
            "data": function (param) {
                param.strFechaDesde = $('#fecha_desde').val();
                param.strFechaHasta = $('#fecha_hasta').val();
            }
        },
        "searching":true,
        "language": {
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "Cargando datos...",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "loadingRecords": "Cargando datos..."
        },
        "drawCallback": function() 
        {
            $(".spinner_procesarPago").hide();
        },
        "rowCallback": function( row, data, index ) 
        {
            if(data.strEstado == 'Procesado')
            {
                $('td', row).css('background-color', 'grey');
                $('select', row).css('background-color', 'grey');
                $('input', row).css('background-color', 'grey');
            }
            
            if(data.strEstado == 'Eliminado')
            {
                $('td', row).css('background-color', 'orange');
                $('select', row).css('background-color', 'orange');
                $('input', row).css('background-color', 'orange');
            }
            
            if(data.strEstado == 'Procesado' || data.strEstado == 'Eliminado' || data.strEstado == 'Error')
            {
                $('input[type="checkbox"]', row).prop('disabled', true);
                $('input[type="checkbox"]', row).prop('visible', false);
            }            
            
            if(data.strEstado == 'Error')
            {
                $('td', row).css('background-color', 'yellow');
                $('select', row).css('background-color', 'yellow');
                $('input', row).css('background-color', 'yellow');
            }           
        },     
        "columns": [
            {"data": "intIdPagoAutomatico"},
            {"data": "strCliente"},
            {"data": "strReferencia"},
            {"data": "strEstado"},
            {"data": "strFeCreacion"},
            {"data": "strUsrCreacion"},
            {"data": "strOpAcciones",
                "render": function (data){
                    var strDatoRetorna = '';

                    if (data.linkVer !== '') 
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" ' + ' title="Ver Detale" ' +                            
                            'onClick="javascript:mostrarDetallesEstadoCuenta(\'' + data.linkVer + '\');">' + '<i class="fa fa-search"></i>' +
                            '</button>&nbsp;';
                    
                    }
                    
                    if(data.strEditFechaAut==='S' && data.strEstado!=='Procesado' && data.strEstado!=='Eliminado')
                    {
                        strDatoRetorna += '<a class="btn btn-outline-dark btn-sm editFechaRetencion" data-toggle="modal" title="Editar Fecha Autorización" ' +                            
                            ' data-id="' + data.intIdPagoAutomatico + '">' + '<i class="fa fa-calendar"></i>' +
                            '</a>&nbsp;';
                    }                    
                    
                    if(data.strEstado!=='Procesado' && data.strEstado!=='Eliminado')
                    {
                        strDatoRetorna += '<a class="btn btn-outline-dark btn-sm eliminarRetencion" data-toggle="modal" title="Eliminar Retención" ' +                            
                            ' data-id="' + data.intIdPagoAutomatico + '">' + '<i class="fa fa-trash-o"></i>' +
                            '</a>&nbsp;';
                    }
                    
                    return strDatoRetorna;          
                }
            }
        ],
        'columnDefs': [
            {
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'render': function (data,rowData,cellData) 
                {
                    if(cellData.strEstado==='Pendiente')
                    {
                        return '<input type="checkbox"  name="id[]" value="' + $('<div/>').text(data).html() + '">';
                    }
                    else
                    {
                        return '<input type="checkbox"  style="display:none" name="id[]" value="' + $('<div/>').text(data).html() + '">';
                    }
                }
            }            
        ]
    });
    
    $('#retencion-select-all').on('click', function () {
        var rows = listaInfoPagoAutomaticoCab.rows({'search': 'applied'}).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    $('#tabla_lista_retenciones tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) 
        {
            var el = $('#retencion-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) 
            {
                el.indeterminate = true;
            }
        }
    });
    $(document).on( 'click','#btProcesarRetenciones', function () {
        $('.spinner_procesarPago').show();
       });
    $("#tabla_lista_retenciones_filter").append('&nbsp;<button type="button" data-toggle="modal" data-target="#modalProcesar" '+
        'class="btn btn-info btn-sm" title="Procesar" <i class="fa fa-files-o"></i> Procesar </button>'+
        ' <i class="fa fa-spinner fa-spin spinner_procesarPago" id = "spinner_procesarPago">'); 

    $("#buscar_pag_aut_cab").click(function () {
        $('#tabla_lista_retenciones').DataTable().ajax.reload();
    });

    $("#limpiar_formulario").click(function () {
        limpiarFormBuscar();
    });
     $("#btProcesarRetenciones").click(function () {
        procesarRetenciones();
    });
    function limpiarFormBuscar() 
    {
        $('#fecha_desde').val("");
        $('#fecha_hasta').val("");
    }

    function procesarRetenciones()
    {
        var arrayIdsRetenciones = [];
        listaInfoPagoAutomaticoCab.$('input[type="checkbox"]').each(function () {
            if (this.checked)
            {
                arrayIdsRetenciones.push(this.value);
            }
        });
        if (arrayIdsRetenciones.length > 0)
        {
            var parametros = {
                "arrayIdsRetenciones": arrayIdsRetenciones
            };
            $.ajax({
                data: parametros,
                url: urlProcesarRetenciones,
                type: 'post',
                success: function (response) {
                    if (response=='OK')
                    {
                        $('#modalMensajes .modal-body').html('La(s) Retencion(e)s fueron procesadas correctamente: ');
                        $('#modalMensajes').modal({show: true});
                        $('#tabla_lista_retenciones').DataTable().ajax.reload();

                    }
                },
                failure: function (response) {
                    $('#modalMensajes .modal-body').html('No se pudo Procesar la(s) Retencion(e)s existe un error: ' + response);
                    $('#modalMensajes').modal({show: true});
                }
            });
        } else
        {
            $('#modalMensajes .modal-body').html('Seleccione por lo menos una Retención de la lista.');
            $('#modalMensajes').modal({show: true});
        }
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
    

    $(document).on( 'click','.eliminarRetencion', function () {
         $("#idPagoAutomatico").val($(this).data("id"));
         $('#modalEliminarRetencion').modal('show');
    });
    
    var formEliminar = document.getElementsByClassName('formEliminarRetencion');
    Array.prototype.filter.call(formEliminar, function (form) {
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
                eliminarRetencion();               
            }
        }, false);
    });
    
    $(document).on( 'click','.editFechaRetencion', function () {
     $("#idPagAutomatico").val($(this).data("id"));
     $('#modalEditFechaRetencion').modal('show');
    });    
    
    var formEditFeccha = document.getElementsByClassName('formEditFechaRetencion');
    Array.prototype.filter.call(formEditFeccha, function (form) {
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
                editarFechaRetencion();               
            }
        }, false);
    }); 
    
    setInterval(function(){ $('#tabla_lista_retenciones').DataTable().ajax.reload(); }, 300000);    
});

/**
 * Función para visualización de detalles de retención,
 *    
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 04-04-2021
 * @since 1.0
 */
function mostrarDetallesEstadoCuenta(strUrlAccion) {
    window.location.href = strUrlAccion; 
}

/**
 * Función para eliminar retención,
 *    
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 14-04-2021
 * @since 1.0
 */
function eliminarRetencion() {

        $.ajax({

            data: new FormData(document.getElementById("formEliminarRetencion")),
            contentType: false,
            cache: false,
            processData: false,
            url:  urlEliminarRetencion,
            type: 'post',           
            success: function (response) {
                if (response === "Ok")
                {                
                    $('#modalEliminarRetencion').hide();
                    $('#modalMensajes .modal-body').html('Se eliminó con éxito la retención.');
                    $('#modalMensajes').modal({show: true});
                    $("#idPagoAutomatico").val("");
                    $("#observacionEliminar").val("");
                    $('#modalMensajes').on('hidden.bs.modal', function () {
                      window.location.reload();
                    })
                } 
                else
                {
                    $('#modalEliminarRetencion').hide();
                    $('#modalMensajes .modal-body').html('No se pudo eliminar la retención. Existen detalles ya procesados. Favor revisar.');
                    $('#modalMensajes').modal({show: true});
                    $("#idPagoAutomatico").val("");
                    $("#observacionEliminar").val("");
                    $('#modalMensajes').on('hidden.bs.modal', function () {
                      window.location.reload();
                    })                    
                }                    
            },
            failure: function (response) {
                $('#modalMensajes .modal-body').html("No se pudo eliminar la retención. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});            
            }
        });  
}

/**
 * Función para editar la fecha de proceso de una retención,
 *    
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 13-05-2021
 * @since 1.0
 */
function editarFechaRetencion() {

    $.ajax({

        data: new FormData(document.getElementById("formEditFechaRetencion")),
        contentType: false,
        cache: false,
        processData: false,
        url:  urlEditarFechaRetencion,
        type: 'post',           
        success: function (response) {
            if (response === "Ok")
            {                
                $('#modalEditFechaRetencion').hide();
                $('#modalMensajes .modal-body').html('Se actualizó correctamente la fecha de proceso de la retención.');
                $('#modalMensajes').modal({show: true});
                $("#idPagoAutomatico").val("");
                $("#fecha_autorizacion").val("");
                $('#modalMensajes').on('hidden.bs.modal', function () {
                  window.location.reload();
                })
            } 
            else
            {
                $('#modalEditFechaRetencion').hide();
                $('#modalMensajes .modal-body').html('No se pudo actualizar la fecha de proceso de la retención. Favor revisar.');
                $('#modalMensajes').modal({show: true});
                $("#idPagAutomatico").val("");
                $('#fecha_autorizacion').val("");
                $('#modalMensajes').on('hidden.bs.modal', function () {
                  window.location.reload();
                })                    
            }                    
        },
        failure: function (response) {
            $('#modalMensajes .modal-body').html("No se pudo actualizar la fecha de la retención. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});            
        }
    });  
}
