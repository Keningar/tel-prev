
$(document).ready(function () {
        
    /**
     * Inicializa con valores vacíos el formulario de búsqueda.
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 08-03-2021
     * @since 1.0
     */
    limpiarFormBuscar();

    /**
     * Obtiene el listado de solicitudes Beneficio 3era Edad/ Adulto Mayor - Cliente con Discapacidad.
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 08-03-2021
     * @since 1.0
     */              
     var listaSolicitudes = $('#tabla_lista_Beneficios').DataTable({
        "ajax": {
            "url": url_grid_solicitudes,
            "type": "POST",
            "data": function (param) {
                param.strIdentificacion = $("#identificacion_buscar").val();
                param.strLogin          = $('#login_buscar').val();                
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
            {"data": "intIdDetalleSolicitud"},
            {"data": "strIdentificacion"},
            {"data": "strCliente"},
            {"data": "strFechaNacimiento"},
            {"data": "strEdad"},
            {"data": "strDireccionLogin"},
            {"data": "strLogin"},
            {"data": "strPlan"},
            {"data": "strBeneficio"},
            {"data": "strPrecioVenta"},
            {"data": "strDescuento"},
            {"data": "strTotalPagar"},
            {"data": "strAcciones",
                 "render": function (data){                 
                    var strDatoRetorna = '';
                                        
                    if (data.linkActFeNacimiento !== '') 
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" ' + ' title="Actualizar Fecha de Nacimiento" ' +
                        'onClick="javascript:mostrarModalActFeNacimiento(\'' + data.linkActFeNacimiento + '\');">' + 
                        '<i class="fa fa-edit"></i>' +
                        '</button>&nbsp;';
                    }
                                      
                    if (data.linkCambioBeneficio !== '' && data.strCambioBenneficio =='SI') 
                    {   
                        var strFlujoAdultoMayor = 'PROCESO_3ERA_EDAD_RESOLUCION_072021';
                        strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" ' + ' title="Cambio de Beneficio" ' +
                        'onClick="javascript:cambioBeneficio(\'' + data.intIdDetalleSolicitud + '\',\'' + data.intIdServicio + '\',\'' + strFlujoAdultoMayor + '\');">' + 
                        '<i class="fa fa-user"></i>' +
                        '</button>&nbsp;';
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

    $('#solicitud-select-all').on('click', function () {
        var rows = listaSolicitudes.rows({'search': 'applied'}).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    $('#tabla_lista_Beneficios tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) 
        {
            var el = $('#solicitud-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) 
            {
                el.indeterminate = true;
            }
        }
    });     
     
        $("#tabla_lista_Beneficios_filter").append(
              '<table border="0"><tr>' +     
              '<td>Motivo:</td>  ' +         
              '<td><select id="motivo_cancelacion" class="form-control form-control-sm"> </select></td> ' +
              '<td>&nbsp;<button type="button" class="btn btn-info btn-sm" id="btCancelarBeneficio" title="Cancelación de Beneficio"> ' +
              '<i class="fa fa-remove"></i>&nbsp;Cancelar</button></td>  ' +
              '</tr></table>'
               );  
  
    $("#btCancelarBeneficio").click(function () {        
        Ext.Msg.confirm('Alerta','Está seguro de cancelar el (los) beneficio(s) ?', function(btn){
            if(btn=='yes'){              
              cancelarBeneficio();
            }
        });
    });
    /**
     * Realiza la llamada a la función Ajax que genera la Cancelación de Beneficios
     * para solicitudes por motivo: Beneficio 3era Edad/ Adulto Mayor - Cliente con Discapacidad.
     *    
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 29-03-2021
     * @since 1.0
     */
    function cancelarBeneficio()
    {
        var arrayIdsSolicitudes = [];
        listaSolicitudes.$('input[type="checkbox"]').each(function () {
            if (this.checked)
            {
                arrayIdsSolicitudes.push(this.value);

            }
        });
        if (arrayIdsSolicitudes.length > 0)
        {
            var parametros = {
                "intIdMotivo": $("#motivo_cancelacion").val(),                
                "arrayIdsSolicitudes": arrayIdsSolicitudes
            };
            $.ajax({
                data: parametros,
                url: urlCancelacionBeneficio,
                type: 'post',
                success: function (response) {
                    if (response)
                    {                                                                                              
                        $('#modalMensajes .modal-body').html(response);
                        $('#modalMensajes').modal({show: true});  
                        $('#tabla_lista_Beneficios').DataTable().ajax.reload();   
                        location.reload();
                    }
                },
                failure: function (response) {
                    $('#modalMensajes .modal-body').html('No se pudo Cancelar lo(s) Beneficio(s) existe un error: ' + response);
                    $('#modalMensajes').modal({show: true});
                }
            });
        } else
        {
            $('#modalMensajes .modal-body').html('Seleccione por lo menos una Solicitud de la lista.');
            $('#modalMensajes').modal({show: true});
        }
    } 
    
    /**
     * Obtiene los motivos de Cancelacion de Beneficio.
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-03-2021
     * @since 1.0
     */
    $.ajax({
        url: urlGetMotivos,
        method: 'GET',
        success: function (data) {
            $.each(data.motivos, function (id, registro) {
                $("#motivo_cancelacion").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');                
            });
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los Motivos. Por favor consulte con el Administrador");
            $('#modalMensajes').modal({show: true});
        }
    });

    $('#motivo_cancelacion').select2({
        placeholder: 'Seleccione un motivo'
    });

    $("#buscar_beneficio").click(function () {      
       $('#tabla_lista_Beneficios').DataTable().ajax.reload();
    }); 

    $("#limpiar_formBeneficio").click(function () {       
       limpiarFormBuscar();
    });     
    
    function limpiarFormBuscar() 
    {        
        $('#identificacion_buscar').val("");
        $('#login_buscar').val("");        
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
    setInterval(function(){ $('#tabla_lista_Beneficios').DataTable().ajax.reload(); }, 30000000);
        
});

function mostrarModalActFeNacimiento(url_accion) 
{
    $.ajax({
        url: url_accion,
        type: 'get',
        dataType: "html",
        success: function (response) {
            $('#modalActFechaNacimiento .modal-body').html(response);
            $('#modalActFechaNacimiento').modal({show: true});
        },
        error: function () {
            $('#modalActFechaNacimiento .modal-body').html('<p>Ocurrió un error, por favor consulte con el Administrador.</p>');
            $('#modalActFechaNacimiento').modal('show');
        }
    });
}

function cambioBeneficio(intIdDetalleSolicitud, intIdServicio, strFlujoAdultoMayor) 
{       
    Ext.Msg.confirm('Alerta', 'Está seguro que desea realizar cambio de beneficio ?', function (btn) {
        if (btn == 'yes') 
        {
            //Se calcula descuento por Adulto Mayor para el servicio.
            Ext.Ajax.request({
                url: url_calculaDescAdultoMayor,
                method: 'post',
                params: { intIdServicio: intIdServicio, strFlujoAdultoMayor : strFlujoAdultoMayor }, 
                success: function (response) {

                    Ext.MessageBox.hide();
                    var objData    = Ext.JSON.decode(response.responseText);
                    var strStatus  = objData.strStatus;
                    var strMensaje = objData.strMensaje;
                    var fltValorDescuentoAdultoMayor = objData.fltValorDescuentoAdultoMayor;
                    if (strStatus != 'OK')
                    {
                        Ext.Msg.alert('Error ', '' + strMensaje + ' para el servicio. ');                          
                    }
                    else
                    {                            
                        //Se ejecuta el cambio de beneficio
                        $.ajax({
                            url: urlCambioBeneficio, 
                            type: 'post',
                            data: { intIdDetalleSolicitud: intIdDetalleSolicitud,
                                    fltValorDescuentoAdultoMayor: fltValorDescuentoAdultoMayor, strFlujoAdultoMayor: strFlujoAdultoMayor },                            
                            success: function (response) {
                                if (response)
                                {
                                    $('#modalMensajes .modal-body').html(response);
                                    $('#modalMensajes').modal({show: true});
                                    $('#tabla_lista_Beneficios').DataTable().ajax.reload();
                                    location.reload();
                                }
                            },
                            failure: function (response) {
                                $('#modalMensajes .modal-body').html('No se pudo realizar cambio de beneficio existe un error: ' + response);
                                $('#modalMensajes').modal({show: true});
                            }
                        });            
                    }
                },
                failure: function (response)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + response.responseText);
                }
            });                         
        }
    });
}
 