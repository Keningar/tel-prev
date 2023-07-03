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

$(document).ready(function () {

    /**
     * Inicializa calendario de Fecha-Desde.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-08-2020
     * @since 1.0
     */
    $('#fecha_desde').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy-mm-dd'
    });
    
    /**
     * Inicializa calendario de Fecha-Desde.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-08-2020
     * @since 1.0
     */
    $('#fecha_hasta').datepicker({
        uiLibrary  : 'bootstrap4',
        locale     : 'es-es',
        dateFormat : 'yy-mm-dd'
    });
    
    
    /**
    * Obtiene las cuentas bancarias
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 29-03-2019
    * @since 1.0
    */   
    $.ajax({
        url: urlGetCuentasBancarias,
        method: 'GET',
        success: function (data) {
            $.each(data.cuentas_bancarias, function (id, registro) {
                $("#banco_cuenta").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {            
            $('#modalMensajes .modal-body').html("No se pueden cargar las cuentas bancarias");
            $('#modalMensajes').modal({show: true});
        }
    });
    $('#banco_cuenta').select2({       
        multiple:true,
        placeholder:'Seleccione cuenta bancaria'
     });    
    

    /**
     * Obtiene el listado de estados de cuenta.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 29-08-2020
     * @since 1.0
     */
    var listaInfoPagoAutomaticoCab = $('#tabla_lista_pago_automatico').DataTable({
        "ajax": {
            "url": urlGridInfoPagoAutomatico,
            "type": "POST",
            "data": function (param) {
                param.strFechaDesde = $('#fecha_desde').val();
                param.strFechaHasta = $('#fecha_hasta').val();
                param.intBcoCta     = $('#banco_cuenta').val();
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
        "columns": [
            {"data": "strBanco"},
            {"data": "strEstado"},
            {"data": "strFeCreacion"},
            {"data": "strUsrCreacion"},
            {"data": "strOpAcciones",
                "render": function (data){
                    var strDatoRetorna = '';

                    if (data.linkVer !== '') 
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" ' + ' title="Ver Estado de Cuenta" ' +                            
                            'onClick="javascript:mostrarDetallesEstadoCuenta(\'' + data.linkVer + '\');">' + '<i class="fa fa-search"></i>' +
                            '</button>&nbsp;';
                    }
                    if(data.strEstado!=='Eliminado')
                    {
                        strDatoRetorna += '<a class="btn btn-outline-dark btn-sm eliminarEstCta" data-toggle="modal" title="Eliminar Estado de Cuenta" ' +                            
                            ' data-id="' + data.intIdPagoAutomatico + '">' + '<i class="fa fa-trash-o"></i>' +
                            '</a>&nbsp;';
                    }
                    
                    return strDatoRetorna;          
                }
            }
        ],

    });
    
    $("#buscar_pag_aut_cab").click(function () {
        $('#tabla_lista_pago_automatico').DataTable().ajax.reload();
    });

    $("#limpiar_formulario").click(function () {
        limpiarFormBuscar();
    });

    function limpiarFormBuscar() 
    {
        $('#fecha_desde').val("");
        $('#fecha_hasta').val("");
        $('#banco_cuenta').val(null).trigger('change');
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
    

    $(document).on( 'click','.eliminarEstCta', function () {
         $("#idPagoAutomatico").val($(this).data('id'));
         $('#modalEliminarEstCta').modal('show');
    });
    
    var forms = document.getElementsByClassName('formEliminarEstadoCta');
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
                eliminarEstadoCuenta();               
            }
        }, false);
    });    
    
    setInterval(function(){ $('#tabla_lista_pago_automatico').DataTable().ajax.reload(); }, 300000);    
});

/**
 * Función para visualización de detalles de estado de cuenta,
 *    
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 04-09-2020
 * @since 1.0
 */
function mostrarDetallesEstadoCuenta(strUrlAccion) {
    window.location.href = strUrlAccion; 
}




/**
 * Función para eliminar estado de cuenta,
 *    
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 15-10-2020
 * @since 1.0
 */
function eliminarEstadoCuenta() {

        $.ajax({

            data: new FormData(document.getElementById("formEliminarEstadoCta")),
            contentType: false,
            cache: false,
            processData: false,
            url:  urlEliminarEstadoCta,
            type: 'post',           
            success: function (response) {
                if (response === "Ok")
                {                
                    $('#modalEliminarEstCta').hide();
                    $('#modalMensajes .modal-body').html('Se eliminó con éxito el estado de cuenta.');
                    $('#modalMensajes').modal({show: true});
                    $("#idPagoAutomatico").val("");
                    $("#observacionEliminar").val("");
                    $('#modalMensajes').on('hidden.bs.modal', function () {
                      window.location.reload();
                    })
                } 
                else
                {
                    $('#modalEliminarEstCta').hide();
                    $('#modalMensajes .modal-body').html('No se pudo eliminar el estado de cuenta. Existen detalles ya procesados. Favor revisar.');
                    $('#modalMensajes').modal({show: true});
                    $("#idPagoAutomatico").val("");
                    $("#observacionEliminar").val("");
                    $('#modalMensajes').on('hidden.bs.modal', function () {
                      window.location.reload();
                    })                    
                }                    
            },
            failure: function (response) {
                $('#modalMensajes .modal-body').html("No se pudo eliminar el estado de cuenta. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});            
            }
        });
    
}
