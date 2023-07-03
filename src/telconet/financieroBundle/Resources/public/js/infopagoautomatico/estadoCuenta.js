/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

var intPaginaActual;
var arrayPaginasVistas = [1];
var itemsPerPage = 10;
var store = '';
var estado_id = '';
var area_id = '';
var login_id = '';
var tipo_asignacion = '';
var pto_sucursal = '';
var idClienteSucursalSesion;
var intCallBack = 0;
var totalPago = 0;
var totalPagoPre = 0;
var numDetalle = 1;
var arrayDetallesPago = [];
var arrayDetallesPagoPrecargado = [];
var arrayInfoDetEstadoCuenta    = [];
var arrayInfoDetEstadoCuentaPre = [];
var intIdUltDetEstadoCta = 0;
var idClienteSelect = 0;
var idClienteSelectPre = 0;
var idFormaPagoPre = 0;
var idPagoAutomaticoDetSelect = 0;
var fechaTransaccionDet = '';
var intCantMaxRegistros = 0;
let listCheck                = [];
var listaInfoDetalle;
$(document).ready(function () {
    
    $('#spinner_procesarPago').hide();
    /**
    * Obtiene los estados para filtro de búsqueda
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 29-10-2020
    * @since 1.0
    * 
    * Se agrega el campo editable a la tabla en la columna de Valor Pago
    * @author Kevin Villegas <kmvillegas@telconet.ec>
    * @version 1.1 13-09-2022
    * 
    * Se agrega modal para reiniciar tiempo de sesion en pagos precargados 
    * @author Kevin Villegas <kmvillegas@telconet.ec>
    * @version 1.1 08-12-2022
    */   
    $.ajax({
        url: urLGetEstados,
        method: 'GET',
        success: function (data) {
            $.each(data.estados_cta, function (id, registro) {
                $("#estados").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {            
            $('#modalMensajes .modal-body').html("No se pueden cargar las estados para filtro de búsqueda.");
            $('#modalMensajes').modal({show: true});
        }
    });
    $('#estados').select2({       
        multiple:true,
        placeholder:'Seleccione estado'
     });
    $(".overflowX").scroll(function() {
        $(".cliente").attr("size", 5);
    });
    $(".overflowX").on("mouseleave", function() {
        $(".cliente").attr("size", 1);
    });
     $("#limpiar_formulario").click(function () {
        limpiarFormBuscar();
    });
     $("#limpiarFormInfoCliente").click(function () {
        limpiarFormInfoCliente();
    }); 
    
     $("#limpiarFormInfoClientePre").click(function () {
        limpiarFormInfoPrecargado();
    });
    
     $(".cerrarPagoPre").click(function () {
        limpiarFormInfoPrecargado();
    }); 
    
    $(".cerrarPago").click(function () {
        numDetalle = 1;
        limpiarFormInfoCliente();
        inicializarDetallesPago();
    });    

    function limpiarFormBuscar() 
    {
        $('#estados').val(null).trigger('change');
    }
    
   

    //Obtiene mediante llamada ajax los clientes por empresa.

   $('#cliente').select2({

       placeholder: 'Seleccione..',

       ajax: {

           url: strUrlGetCientes,

           dataType: 'json',

           minimumInputLength: 4,

           delay: 250,

           data: function (data) {

               return {

                   searchTerm: data.term

               };

           },

           processResults: function (data) {
               var results = $.map(data, function (item) {
                   return {
                       id:item.id,
                       text: item.nombres
                   };
               });

               return {
                   results: results,
               };
           },
           cache: true

       }

   }).on('select2:select', function (e) {            
       var idPersona = e.params.data.id;
       $('#login').val(null).trigger('change');
       $('#facturasCliente').val(null).trigger('change');
       $('#saldoFactura').val(null).trigger('change');
       $('#cliente').prop('disabled',true);
       $('#login').empty().trigger('change');                
       $('#facturasCliente').empty().trigger('change');
       $('#saldoFactura').val("");
       $.ajax({
           url: urlGetLoginesCliente,
           method: 'GET',
           async:true,
           data: {idPersona: idPersona},
           success: function (data) {
               $('#login').select2({
                   multiple:false
                });
               if(data.puntos.length >0)
               {
                    $("#login").append('<option value=0>Seleccione</option>');
               }               
               $.each(data.puntos, function (id, registro) {
                   $("#login").append('<option value=' + registro.id + '>' + registro.login + '</option>');
               });
           },
           error: function () {
               $('#modalMensajes .modal-body').html("No se pudieron cargar los logines. Por favor consulte con el Administrador.");
               $('#modalMensajes').modal({show: true});
           }
       });
   });
   
   

   $('#clientePre').select2({

       placeholder: 'Seleccione..',

       ajax: {

           url: strUrlGetCientes,

           dataType: 'json',

           minimumInputLength: 4,

           delay: 250,

           data: function (data) {

               return {

                   searchTerm: data.term

               };

           },

           processResults: function (data) {
               var results = $.map(data, function (item) {
                   return {
                       id:item.id,
                       text: item.nombres
                   };
               });

               return {
                   results: results,
               };
           },
           cache: true

       }

   }).on('select2:select', function (e) {            
        idClienteSelectPre = e.params.data.id;
        $('#infoPagoDetPre').DataTable().ajax.reload();
        $('#clientePre').prop('disabled',true);
    });


   /**
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 16-09-2020          
    */     
   //Obtiene las facturas pendientes del punto seleccionado.
   $('#login').select2({placeholder: "Seleccione"}).on('select2:select', function (e) {
       $('#facturasCliente').val(null).trigger('change');
       $('#saldoFactura').val(null).trigger('change');
       $('#facturasCliente').empty().trigger('change');
       $('#saldoFactura').val("");
       $('#valorPago').val("");
       var idPunto = e.params.data.id;
      
       $.ajax({
           url: urlGetFacturasPtoCliente,
           method: 'GET',
           async:true,
           data: {idPunto: idPunto},
           success: function (data) {

               $('#facturasCliente').select2({
                   multiple:false
                });
               if(data.facturas.length >0)
               {
                    $("#facturasCliente").append('<option value=0>Seleccione</option>');
               }
               $.each(data.facturas, function (id, registro) {
                   $("#facturasCliente").append('<option value=' + registro.id + '>' + registro.numeroFacturaSri + '</option>');
               });
           },
           error: function () {
               $('#modalMensajes .modal-body').html("No se pudieron cargar las facturas. Por favor consulte con el Administrador.");
               $('#modalMensajes').modal({show: true});
           }
       });
   });

   //Obtiene el saldo de la factura seleccionada.
   $('#facturasCliente').select2({placeholder: "Seleccione"}).on('select2:select', function (e) { 
       var idFactura     = e.params.data.id;
       $('#saldoFactura').val(null).trigger('change');
       $('#saldoFactura').val("");                
       $('#valorPago').val("");
       $.ajax({
           url: urlGetSaldoFactura,
           method: 'GET',
           data: {idFactura: idFactura},
           success: function (data) 
           {
               $('#saldoFactura').val(data.saldoFactura);
           },
           error: function () {
               $('#modalMensajes .modal-body').html("No se pudo obtener el saldo de la factura. Por favor consulte con el Administrador.");
               $('#modalMensajes').modal({show: true});
           }
       });
   });

    var listaInfoDetalles =  $('#infoPagoDetPre');
    listaInfoDetalle  =  listaInfoDetalles.DataTable({ 
        "ajax": {
            "url": urlGridInfoPagPrecargado,
            "type": 'POST',
            "data": function (param) {
                param.intIdCliente   = idClienteSelectPre;
                param.intIdFormaPago = idFormaPagoPre;
            },             
        }, 
        "searching":true,
        "ordering":true,
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
            {"data": "intIdPagDet"},
            {"data": "strLogin"},
            {"data": "strFactura"},
            {"data": "strSaldo"},
            {"data": "strValor",
                "render": function (data,rowData,cellData){
                    var strDatoRetorna = '';
                        strDatoRetorna += '<input type="text" class="Valor'+cellData.intIdPagDet+'" onkeyup="return soloNumeros('+cellData.intIdPagDet+')" onfocus="cambioValor('+cellData.intIdPagDet+')" id="Valor'+cellData.intIdPagDet+'" value="' + data + '" >'
                            +'</input>&nbsp;';
                    return strDatoRetorna;
                }
            },
           {"data": "strAcciones",
                "render": function(data){
                    var strDatoRetorna = ''; 
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
                    return '<input type="checkbox" id="check'+cellData.intIdPagDet+'" name="id[]" value="' + $('<div/>').text(data).html() + '">';

                }
            },           
        ]        

    });

     
    /**
    * Cálculo del valor total si todas las filas están o no seleccionadas.
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 12-05-2022
    * 
    * Se agrega Editar en la columna de Valor pago
    * @author Kevin Villegas <kmvillegas@telconet.ec>
    * @version 1.1 12-09-2022
    */   
    $('#info-select-all').on('click', function () {
        var rows = listaInfoDetalle.rows({'search': 'applied'}).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
        totalPagoPre                = 0;
        arrayDetallesPagoPrecargado = [];
        if(this.checked)
        {
            listaInfoDetalle.data().each(function(info) 
            {
                totalPagoPre += parseFloat(info.strValor);
                totalPagoPre = Math.round(totalPagoPre * 100)/100;
            });
        }

        $('#totalPre').html(parseFloat(totalPagoPre).toFixed(2)); 
        $('#valorTotalPre').html(parseFloat(totalPagoPre).toFixed(2));        
    });

    /**
    * Cálculo del valor total según la fila seleccionada.
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 12-05-2022
    */
    $('#infoPagoDetPre tbody').on('change', 'input[type="checkbox"]', function () {
        if (!this.checked) 
        {
            var el = $('#info-select-all').get(0);
            if (el && el.checked && ('indeterminate' in el)) 
            {
                el.indeterminate = true;
            }
        }
        var info         = listaInfoDetalle.row($(this).closest('tr')).data();
        var valorPagoDet = $('#Valor'+info.intIdPagDet).val();

        if ($(this).is(':checked') ) 
        {
            listCheck.push({
                id:info.intIdPagDet,
                valor:$('#Valor'+info.intIdPagDet).val()
            });

            totalPagoPre += parseFloat(valorPagoDet);
            totalPagoPre = Math.round(totalPagoPre * 100)/100;
            
        } else 
        {
            listCheck=listCheck.filter((e)=>e.id!=info.intIdPagDet);
            
            totalPagoPre -= parseFloat(valorPagoDet);
            totalPagoPre = Math.round(totalPagoPre * 100)/100;
        }
        $('#totalPre').html(parseFloat(totalPagoPre).toFixed(2)); 
        $('#valorTotalPre').html(parseFloat(totalPagoPre).toFixed(2));
        
    }); 
   
     
    /**
    * Obtiene las formas de pago
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 08-09-2020
    * 
    * @author Kevin Villegas <kmvillegas@telconet.ec>
    * @version 1.1 24-08-2022  se agrega diferencia de notificacion enviada
    */   
    $('#tabla_lista_pago_automatico_det').DataTable({ 
        dom: "Bfrtip",       
        buttons: ["excel","pdf"],         
        "ajax": {
            "url": urlGridDetalleEstadoCuenta,
            "type": 'POST',
            "data": function (param) {
                param.intIdPagoAutomatico = $('#intIdPagoAutomatico').val();
                param.strEstado           = $('#estados').val();
            }
        },
        "scrollY":"300px",
        "scrollX":true,
        "scrollCollapse": true,        
        "searching":true,
        "ordering":true,
        "order": [[ 0, "asc" ]],
        "language": {
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "Cargando datos...",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "loadingRecords": "Cargando datos..."
        },        
        "rowCallback": function( row, data, index ) 
        {
            if(data.strEstado == 'Procesado')
            {
                $('td', row).css('background-color', 'grey');
                $('select', row).css('background-color', 'grey');
                $('input', row).css('background-color', 'grey');
            }
        },    
        "columns": [
            {"data": "strFecha","width": "20%"},
            {"data": "strTipo","width": "10%"},
            {"data": "strReferencia","width": "15%"},
            {"data": "strMonto"},
            {"data": "strEstado"},
            {"data": "strConcepto"},
            {"data": "strAcciones",
                "render": function (data){
                    var strDatoRetorna = '';
                    
                    if (data.strEstado === 'Pendiente') 
                    {
                        strDatoRetorna += '<a class="btn btn-outline-dark btn-sm generarPago" data-toggle="modal" title="Generar Pago" ' +                            
                            ' data-id="' + data.intIdPagoAutDet + '">' + '<i class="fa fa-sticky-note-o"></i>' +
                            '</a>&nbsp;';     
                    
                        strDatoRetorna += '<a class="btn btn-outline-dark btn-sm generarPagoPrecargado" data-toggle="modal"title="Generar Pago Precargado" ' + ' data-id="' + data.intIdPagoAutDet + '"><i class="fa fa-navicon"></i></a>&nbsp;';               
                    
                    }
                    if (data.strEstado === 'Procesado') 

                    {
                        strDatoRetorna += '<a type="button" class="verInfoPagos btn btn-outline-dark btn-sm"  data-id="'+data.intIdPagoAutDet+
                                '" title="Ver Pagos" ' +                            
                            '>' + '<i class="fa fa-search"></i>' +
                            '</a>&nbsp;';                        

                        if(data.strEsNotificado!='N'){
                            strDatoRetorna += '<a type="button" class="notificaPago btn btn-outline-dark btn-sm"  data-id="'+data.intIdPagoAutDet+
                                    '" title="Notificar Pago" ' +                            
                                '>' + '<i class="fa fa-envelope-open-o "></i></a>&nbsp;';                 
                        }else{
                            strDatoRetorna += '<a type="button" class="notificaPago btn btn-outline-dark btn-sm"  data-id="'+data.intIdPagoAutDet+
                                    '" title="Notificar Pago" ' +                            
                                '>' + '<i class="fa fa-envelope-o" ></i></a>&nbsp;'; 
                        }

                    }
                        strDatoRetorna += '<a type="button" class="verHistorial btn btn-outline-dark btn-sm"  data-id="'+data.intIdPagoAutDet+
                        '" title="Ver Historial" ' +                            
                        '>' + '<i class="fa fa-list-alt"></i>' +
                        '</a>&nbsp;';  

                    return strDatoRetorna;          
                }
            }
        ],

    });

    $('#tabla_lista_pago_automatico_det').on('draw.dt', function() {
        intPaginaActual = $('#tabla_lista_pago_automatico_det').DataTable().page.info().page+1;
        if(!arrayPaginasVistas.includes(intPaginaActual))
        {
            arrayPaginasVistas.push(intPaginaActual);
        }
        
        intCallBack--;
    });

    $("#buscar_pag_aut_det").click(function () {
        $('#tabla_lista_pago_automatico_det').DataTable().ajax.reload();
    });
    
    /**
    * Obtiene los pagos asociados a un detalle de estado de cuenta
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 19-05-2022
    */   
    $('#infoPagos').DataTable({
        "ajax": {
            "url": urlGetPagosEstadoCta,
            "type": 'POST',
            "data": function (param) {
                param.intIdPagoAutomaticoDet = idPagoAutomaticoDetSelect;
                param.strOpcion = 'DEP';
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
            {"data": "strTipo"},
            {"data": "strOficina"},
            {"data": "strNumeroPago"},
            {"data": "strLogin"},
            {"data": "strTotal"},
            {"data": "strFecha"},
            {"data": "strUser"},
            {"data": "strEstado"},
            {"data": "strOpAcciones",
                "render": function (data){
                    var strDatoRetorna = '';

                    strDatoRetorna += '<a type="button" class="verEstadoCtaFactura btn btn-outline-dark btn-sm" data-id="verEstadoCtaFactura_'+data.intIdPago+'" title="Ver Estado de Cta Punto" ' +                            
                        '>' + '<i class="fa fa-money"></i>' +
                        '</a>&nbsp;';  
                    
                    if (data.strUrlVerPago !== '') 
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" formtarget="_blank"' + ' title="Ver Pago" ' +                            
                            'onClick="javascript:mostrarOpcion(\'' + data.strUrlVerPago + '\');">' + '<i class="fa fa-search"></i>' +
                            '</button>&nbsp;';
                    }
                    
                    if (data.strUrlImprimirPago !== '') 
                    {
                        strDatoRetorna += '<button type="button" class="btn btn-outline-dark btn-sm" formtarget="_blank"' + ' title="Imprimir Pago" '+                            
                            'onClick="javascript:mostrarOpcion(\'' + data.strUrlImprimirPago + '\');">' + '<i class="fa fa-print"></i>' +
                            '</button>&nbsp;';
                    }

                    return strDatoRetorna;          
                }
            }
        ],

    });    
    
    
   /**
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 16-09-2020          
     */     
   //Funcionalidad para agregar configuración de nueva fila de estado de cuenta.
   
    $(document).on( 'click','.generarPago', function () {
        var idPagoAutomaticoDet = $(this).data("id");
        $('#spinner_procesarPago').hide();

        limpiarFormInfoCliente();
        arrayDetallesPago        = [];
        arrayInfoDetEstadoCuenta = [];            
        $('#infoPagoDet > tbody').empty();
        totalPago = 0;
        $('#total').html(totalPago); 
        $('#valorTotal').html(totalPago);            
        
        intIdUltDetEstadoCta = idPagoAutomaticoDet;
        $.ajax({
            url: urlGetMaxNunDetalles,
            method: 'POST',
            async:true,
            success: function (data) 
            {
                intCantMaxRegistros = data.intCantMaxRegistros;
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudo obtener parametro cantidan maxima de registros. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });        
        $.ajax({
            url: urlGetDetalleEstadoCta,
            method: 'POST',
            async:true,
            data: {idDetalle: idPagoAutomaticoDet},
            success: function (data) 
            {
                var montoDet = (Math.round(data.monto * 100)/100);
                fechaTransaccionDet = data.fechaTransaccion;
                $('#fechaTransaccionDet').html(data.fechaTransaccion);
                $('#tipoTransaccionDet').html(data.tipoTran);
                $('#numeroReferenciaDet').html(data.numeroReferencia);
                $('#montoDet').html(parseFloat(montoDet).toFixed(2));
                $('#estadoDet').html(data.estado);

                arrayInfoDetEstadoCuenta = {"intidPagAutCab":data.idPagAutCab,"intidPagoAutomaticoDet":idPagoAutomaticoDet,"strFecha":data.fechaTransaccion,"strTipo":data.tipoTran,"strReferencia":data.numeroReferencia,"strMonto":data.monto,"strEstado":data.estado};                
            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudo obtener información del detalle de estado de cuenta. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
        $('#formaPago').empty().trigger('change');
        $('#formaPago').val(null).trigger('change');
        $.ajax({
            url: strUrlGetFormasPago,
            method: 'GET',
            async:true,
            data: {fechaTransaccionDet: fechaTransaccionDet, idPagoAutomaticoDet: idPagoAutomaticoDet},
            success: function (data) 
            {
                $('#formaPago').select2({
                    placeholder: "Seleccione",
                    multiple:false
                 }).on('select2:select', function (e) { 
                     
                    var idFormaPago = e.params.data.id;
                    if(idFormaPago !== '0')
                    {
                       $('#formaPago').prop('disabled',true);
                    }                     
                });
                $.each(data.formas_pago, function (id, registro) 
                {
                  $('#formaPago').append('<option value=' + registro.id + '>' + registro.descripcion + '</option>');
                });         
            },
            error: function () {            
                $('#modalMensajes .modal-body').html("No se pueden cargar las formas de pago");
                $('#modalMensajes').modal({show: true});
            }
        });        
        
        //Evita cierre automático de modal al dar click fuera de él
        $('#modalGenerarPago').modal({backdrop: 'static', keyboard: false});
        
        $("#modalGenerarPago").draggable({
               handle: ".modal-header"
        });
        $("#modalGenerarPago").modal("show");       
    });
    
    $(document).on( 'click','.generarPagoPrecargado', function () {
        var idPagoAutomaticoDet = $(this).data("id");
        $('#spinner_procPagPre').hide();
        
        $.ajax({
            url: urlGetDetalleEstadoCta,
            method: 'POST',
            async:true,
            data: {idDetalle: idPagoAutomaticoDet},
            success: function (data) 
            {
                var montoDet = (Math.round(data.monto * 100)/100);
                fechaTransaccionDet = data.fechaTransaccion;
                $('#fechaTransaccionDetPre').html(data.fechaTransaccion);
                $('#tipoTransaccionDetPre').html(data.tipoTran);
                $('#numeroReferenciaDetPre').html(data.numeroReferencia);
                $('#montoDetPre').html(parseFloat(montoDet).toFixed(2));
                $('#estadoDetPre').html(data.estado);

                arrayInfoDetEstadoCuentaPre = {
                    "intidPagAutCab":data.idPagAutCab,
                    "intidPagoAutomaticoDet":idPagoAutomaticoDet,
                    "strFecha":data.fechaTransaccion,
                    "strTipo":data.tipoTran,
                    "strReferencia":data.numeroReferencia,
                    "strMonto":data.monto,
                    "strEstado":data.estado};                

            },
            error: function () {
                $('#modalMensajes .modal-body').html("No se pudo obtener información del detalle de estado de cuenta. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
        $('#formaPagoPre').empty().trigger('change');
        $('#formaPagoPre').val(null).trigger('change');
        $.ajax({
            url: strUrlGetFormasPago,
            method: 'GET',
            async:true,
            data: {fechaTransaccionDet: fechaTransaccionDet, idPagoAutomaticoDet: idPagoAutomaticoDet},
            success: function (data) 
            {
                $('#formaPagoPre').select2({
                    placeholder: "Seleccione",
                    multiple:false
                 }).on('select2:select', function (e) { 
                     idFormaPagoPre = e.params.data.id;
                     if(idFormaPagoPre != '0')
                     {
                        $('#formaPagoPre').prop('disabled',true);
                     }
                });
                $.each(data.formas_pago, function (id, registro) 
                {
                  $('#formaPagoPre').append('<option value=' + registro.id + '>' + registro.descripcion + '</option>');
                });           
            },
            error: function () {            
                $('#modalMensajes .modal-body').html("No se pueden cargar las formas de pago");
                $('#modalMensajes').modal({show: true});
            }
        });        
        //Evita cierre automático de modal al dar click fuera de él
        $('#modalGenPagPrecargado').modal({backdrop: 'static', keyboard: false})
        
        $("#modalGenPagPrecargado").draggable({
               handle: ".modal-header"
        });
        $("#modalGenPagPrecargado").modal("show"); 
    });
    
    $(document).on( 'click','.verInfoPagos', function () {
        idPagoAutomaticoDetSelect = $(this).data("id");
        $('#infoPagos').DataTable().ajax.reload();
        //Evita cierre automático de modal al dar click fuera de él
        $('#modalInfoPagos').modal({backdrop: 'static', keyboard: false})
        
        $("#modalInfoPagos").draggable({
               handle: ".modal-header"
        });
        $("#modalInfoPagos").modal("show"); 
    }); 
    /**
    * Obtiene el historial asociados a un detalle de estado de cuenta
    * @author Kevin Villegas <kmvillegas@telconet.ec>
    * @version 1.0 09-11-2022
    */   
        $('#infoHistorial').DataTable({
        "ajax": {
            "url": urlGetHistorialEstadoCta,
            "type": 'POST',
            "data": function (param) {
                param.intIdPagoAutomaticoDet = idPagoAutomaticoDetSelect;
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
            {"data": "strFecha"},
            {"data": "strObservacion"},
            {"data": "strUser"},
            {"data": "strEstado"}                       
        ],
        "aaSorting":[[0,"desc"]],
    });    
    
    $(document).on( 'click','.verHistorial', function () {
        idPagoAutomaticoDetSelect = $(this).data("id");
        $('#infoHistorial').DataTable().ajax.reload();
        //Evita cierre automático de modal al dar click fuera de él
        $('#modalInfoHistorial').modal({backdrop: 'static', keyboard: false})
        
        $("#modalInfoHistorial").draggable({
               handle: ".modal-header"
        });
        $("#modalInfoHistorial").modal("show"); 
    }); 
    


    $(document).on( 'click','.notificaPago', function (data) {
        var idPagoAutDetSelect = $(this).data("id");
        $.ajax({
            url: urlNotificaPago,
            method: 'POST',
            data: {idPagoAutomaticoDet: idPagoAutDetSelect},
            success: function (data) 
            {
                if(data.strRespuesta === 'OK')
                {
                    $('#modalMensajes .modal-body').html("Notificación de pago fue enviada correctamente.");
                    location.reload();
                }
                else
                {
                    $('#modalMensajes .modal-body').html("Error al enviar notificación de pago. Por favor consulte con el Administrador.");
                }
                $('#modalMensajes').modal({show: true});               
            },
            error: function () {
                $('#modalMensajes .modal-body').html("Error al enviar notificación de pago. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});
            }
        });
    });      
    
    
   var indiceFilaNew = 0;
   var indiceFilaAct = 0;
    
   $(document).on( 'click','.addRow', function () {

    var table            = $('#tabla_lista_pago_automatico_det').DataTable();
    if(!$("#clonedTable").length)
    {
        document.querySelectorAll('[aria-describedby="tabla_lista_pago_automatico_det_info"]')[1].setAttribute("id","clonedTable");
    }
    

    var data             = table.row( $(this).parents('tr') ).data();
    var tr               = $(this).closest('tr');
    var indiceFilaActual = $(this).closest('tr').index();
    var row              = table.row( tr );
    
    
    var rowIndex         = row.index();
    var idClienteSelect  = $('#cliente_'+rowIndex).val();
    var total            = $('#total_'+rowIndex).val();
    
    if(idClienteSelect === '0')
    {
        $('#modalMensajes .modal-body').html("Debe seleccionar un cliente.");
        $('#modalMensajes').modal({show: true});
        return false;
    }
    
    if(rowIndex !== indiceFilaAct)
    {
        indiceFilaNew = 0;
    }

    


    indiceFilaAct = rowIndex;
    var nuevaFila ="<tr class='detalleNew' id = 'detalleNew_"+rowIndex+"_"+indiceFilaNew+"'><td colspan='7'></td>";
    nuevaFila     +="<td><div style='width:200px;' class='overflowX'><select class='loginNew form-control'  id = 'login_"+rowIndex+"_"+indiceFilaNew+"'></select></div></td> <td><div style='width:200px;' class='overflowX'><select class='facturaNew form-control' id = 'factura_"+rowIndex+"_"+indiceFilaNew+"'></select></div></td> <td><div style='width:100px;' class='overflowX'><input type='text' class='saldoFacturaNew form-control' id = 'saldoFactura_"+rowIndex+"_"+indiceFilaNew+"' disabled/></div></td> <td><div style='width:200px;' class='overflowX'><select class='formaPagoNew form-control' id = 'formaPago_"+rowIndex+"_"+indiceFilaNew+"'></select></div></td> <td><div style='width:120px;' class='overflowX'><input type='text' class='valorFacturaNew form-control' id = 'valorPago_"+rowIndex+"_"+indiceFilaNew+"'/></div></td><td><div style='width:150px;' class='overflowX'> <a type='button' class='verEstadoCtaFactura btn btn-outline-dark btn-sm' title='Ver Estado de Cta Factura'  data-id='login_"+rowIndex+"_"+indiceFilaNew+"' id = 'verEstadoCtaFactura_"+rowIndex+"_"+indiceFilaNew+"'><i class='fa fa-money'></i></a> <a type='button' class='eliminarDetalle btn btn-outline-dark btn-sm' data-id = 'eliminarDetalle_"+rowIndex+"_"+indiceFilaNew+"' title='Eliminar Detalle'><i class='fa fa-trash-o'></i></a></div></td>";
    
    
    //insertando en tabla clonada

    var newHiddenROw = "<tr class='detalleNew' id='detalleClonedNew_"+rowIndex+"_"+indiceFilaNew+"'><td <div style='width:120px;' class='overflowX'><input type='input' style='visibility:hidden'  class='valorFacturaNew form-control' ></div></td>";
    newHiddenROw +="<td <div style='width:120px;' class='overflowX'><input type='input' style='visibility:hidden'  class='valorFacturaNew form-control' ></div></td>";
    newHiddenROw +="<td <div style='width:120px;' class='overflowX'><input type='input' style='visibility:hidden'  class='valorFacturaNew form-control' ></div></td>";
    newHiddenROw +="<td <div style='width:120px;' class='overflowX'><input type='input' style='visibility:hidden'  class='valorFacturaNew form-control' ></div></td>";
    newHiddenROw +="<td <div style='width:120px;' class='overflowX'><input type='input' style='visibility:hidden'  class='valorFacturaNew form-control' ></div></td>";
    newHiddenROw += "</tr>";
    $("#clonedTable > tbody > tr").eq(indiceFilaActual).after(newHiddenROw);


    var arrayIndice = [];
    arrayIndice[0] = '';
    arrayIndice[1] = rowIndex;
    arrayIndice[2] = indiceFilaNew;

    $('#login_'+arrayIndice[1]+'_'+arrayIndice[2]+'').val("").trigger('change'); 

    $.ajax({
        url: urlGetLoginesCliente,
        method: 'POST',
        async:true,
        data: {idPersona: idClienteSelect},
        success: function (data) {
            $('#login_'+arrayIndice[1]+'_'+arrayIndice[2]+'').select2({
                multiple:false
             });
             
            $('#login_'+arrayIndice[1]+'_'+arrayIndice[2]+'').append('<option value=0>Seleccione</option>');

            $.each(data.puntos, function (id, registro) 
            {
                $('#login_'+arrayIndice[1]+'_'+arrayIndice[2]+'').append('<option value=' + registro.id + '>' + registro.login + '</option>');
            });


            $('.loginNew').select2({placeholder: "Seleccionar"}).on('select2:select', function (e) {
                var idLoginSelect = $(this).attr('id');
                var strIndice     = idLoginSelect.toString();
                var arrayIndice   = strIndice.split('_');
                var idPunto       = e.params.data.id;
                
                $('#factura_'+arrayIndice[1]+'_'+arrayIndice[2]+'').val("").trigger('change');
                $('#factura_'+arrayIndice[1]+'_'+arrayIndice[2]+'').empty().trigger('change');                 
                $('#saldoFactura_'+arrayIndice[1]+'_'+arrayIndice[2]+'').val("");
                $('#valorPago_'+arrayIndice[1]+'_'+arrayIndice[2]+'').val("");

                $.ajax({
                    url: urlGetFacturasPtoCliente,
                    method: 'GET',
                    async:false,
                    data: {idPunto: idPunto},
                    success: function (data) {

                         $('#factura_'+arrayIndice[1]+'_'+arrayIndice[2]+'').select2({
                            multiple:false
                         });
                         $('#factura_'+arrayIndice[1]+'_'+arrayIndice[2]+'').append('<option value=0>Seleccione</option>');
                        $.each(data.facturas, function (id, registro) {
                             $('#factura_'+arrayIndice[1]+'_'+arrayIndice[2]+'').append('<option value=' + registro.id + '>' + registro.numeroFacturaSri + '</option>');
                        });
                    },
                    error: function () {
                        $('#modalMensajes .modal-body').html("No se pudieron cargar las facturas. Por favor consulte con el Administrador.");
                        $('#modalMensajes').modal({show: true});
                    }
                });
            });


            $('.facturaNew').select2({placeholder: "Seleccionar"}).on('select2:select', function (e) {  
                var idFacturaSelect = $(this).attr('id');
                var strIndice     = idFacturaSelect.toString();
                var arrayIndice   = strIndice.split('_');   
                var idFactura     = e.params.data.id;

                $.ajax({
                    url: urlGetSaldoFactura,
                    method: 'GET',
                    data: {idFactura: idFactura},
                    success: function (data) 
                    {
                        $('#saldoFactura_'+arrayIndice[1]+'_'+arrayIndice[2]+'').val(data.saldoFactura);
                    },
                    error: function () {
                        $('#modalMensajes .modal-body').html("No se pudo obtener el saldo de la factura. Por favor consulte con el Administrador.");
                        $('#modalMensajes').modal({show: true});
                    }
                });
            });
            $(".valor_factura").on("change",function() {
                var idElemento    = $(this).attr('id');
                var strIndice     = idElemento.toString();
                var arrayIndice   = strIndice.split('_');
                var nuevoTotal    = 0
                var numFilas      = 0;
                //Obtenemos todos los componentes con id específico
                $("select[id*='login_"+arrayIndice[1]+"']").each(function (i, el) {               
                     numFilas ++;
                 });

                for(var numFila = 0; numFila < numFilas; numFila++) 
                {
                    if(numFila === 0)
                    {                       
                        if(isNumeric($("#valorPago_"+arrayIndice[1]+"").val()))
                        {
                            nuevoTotal  += parseFloat($("#valorPago_"+arrayIndice[1]+"").val());
                        }
                        
                    }
                    else
                    {                       
                        if(isNumeric($('#valorPago_'+arrayIndice[1]+'_'+(numFila-1)+'').val()))
                        {
                            nuevoTotal  += parseFloat($('#valorPago_'+arrayIndice[1]+'_'+(numFila-1)+'').val());
                        }
                        
                    }

                }
                $('#total_'+arrayIndice[1]).val(nuevoTotal);   
                let strValId = 'total_'+arrayIndice[1];
                nuevoTotalCloned(strValId,nuevoTotal);
                
            });             
            $(".valorFacturaNew").on("change",function() {
                var idElemento    = $(this).attr('id');
                var strIndice     = idElemento.toString();
                var arrayIndice   = strIndice.split('_');
                var nuevoTotal    = 0;
                var numFilas      = 0;
                
                //Obtenemos todos los componentes con id específico
                $("select[id*='login_"+arrayIndice[1]+"']").each(function (i, el) {
                     numFilas ++;
                 });

                for(var numFila = 0; numFila < numFilas; numFila++) 
                {
                    if(numFila === 0)
                    {                         
                        if(isNumeric($("#valorPago_"+arrayIndice[1]+"").val()))
                        {
                            nuevoTotal  += parseFloat($("#valorPago_"+arrayIndice[1]+"").val());
                        }
                        
                    }
                    else
                    {                       
                        if(isNumeric($('#valorPago_'+arrayIndice[1]+'_'+(numFila-1)+'').val()))
                        {
                            nuevoTotal  += parseFloat($('#valorPago_'+arrayIndice[1]+'_'+(numFila-1)+'').val());
                        }
                        
                    }

                }
                nuevoTotal = Math.round(nuevoTotal * 100)/100;
                $('#total_'+arrayIndice[1]).val(nuevoTotal);   
                let strValId = 'total_'+arrayIndice[1];
                nuevoTotalCloned(strValId,nuevoTotal);
            }); 
            
            $.ajax({
                url: strUrlGetFormasPago,
                method: 'GET',
                async:true,
                success: function (data) 
                {
                    $('.formaPagoNew').select2({       
                        multiple:false
                     });
                    $('.formaPagoNew').append('<option value=0>Seleccione</option>');
                    $.each(data.formas_pago, function (id, registro) 
                    {
                      $('.formaPagoNew').append('<option value=' + registro.id + '>' + registro.descripcion + '</option>');
                    });
                },
                error: function () {            
                    $('#modalMensajes .modal-body').html("No se pueden cargar las formas de pago");
                    $('#modalMensajes').modal({show: true});
                }
            });            
            
        },
        error: function () {
            $('#modalMensajes .modal-body').html("No se pudieron cargar los logines. Por favor consulte con el Administrador.");
            $('#modalMensajes').modal({show: true});
        }
    });
         
    
    nuevaFila+="</tr>";
    $('#tabla_lista_pago_automatico_det > tbody > tr').eq(indiceFilaActual).after(nuevaFila);
    indiceFilaNew++;
    } ); 
    
    /**
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 06-10-2020          
     */     
   //Funcionalidad para procesar un pago a partir de los parámetros enviados del estado de cuenta.   
   $(document).on( 'click','.procesarPago', function () {
      
    var table                  = $('#tabla_lista_pago_automatico_det').DataTable();
    var data                   = table.row( $(this).parents('tr') ).data();
    var floatMonto             = parseFloat(data['strMonto']).toFixed(2);
    var floatMontoTotal        = 0;
    var tr                     = $(this).closest('tr');
    var row                    = table.row( tr );
    var rowIndex               = row.index();
    var intIdPagoAutomaticoCab = $('#intIdPagoAutomatico').val();
    var idClienteSelect        = $('#cliente_'+rowIndex).val();
    var idPtoSelect            = 0;
    var idFactSelect           = 0;
    var idFormaPagSelect       = 0;
    var valorPago              = 0
    var arrayDetalles          = new Array();
    var numFilas               = 0;
    var boolCambiaFP           = false;
    var formaPagPrincipal      = 0;
    var formaPagNuevaFila      = 0;
    $('#spinner_procesarPago_'+rowIndex).show();
    $('.procesarPago').attr('disabled', true);
    //Obtenemos todos los componentes con id específico
    $("select[id*='login_"+rowIndex+"']").each(function (i, el) {               
         numFilas ++;
     });
    
    for(var numFila = 0; numFila < numFilas; numFila++) 
    {
        var arrayDetallesInt = new Array(7);
        if(numFila === 0)
        {
            idPtoSelect          = $('#login_'+rowIndex).val();
            idFactSelect         = $('#factura_'+rowIndex).val();
            idFormaPagSelect     = $("#formaPago_"+rowIndex+"").val();
            valorPago            = $("#valorPago_"+rowIndex+"").val(); 
            arrayDetallesInt[0]  = intIdPagoAutomaticoCab;
            arrayDetallesInt[1]  = data['intIdPagoAutDet'];
            arrayDetallesInt[2]  = $('#cliente_'+rowIndex).val();
            arrayDetallesInt[3]  = $('#login_'+rowIndex).val();
            arrayDetallesInt[4]  = $('#factura_'+rowIndex).val();
            arrayDetallesInt[5]  = $("#formaPago_"+rowIndex+"").val();
            arrayDetallesInt[6]  = data['strReferencia'];
            
            if(idClienteSelect  === null || idClienteSelect  === '0' || 
               idPtoSelect      === null || idPtoSelect      === '0' || 
               idFactSelect     === null || idFactSelect     === '0' ||
               idFormaPagSelect === null || idFormaPagSelect === '0')
            {
                $('#spinner_procesarPago_'+rowIndex).hide();
                $('#modalMensajes .modal-body').html("Faltan datos de seleccionar e ingresar. Favor revisar.");
                $('.procesarPago').attr('disabled', false);
                $('#modalMensajes').modal({show: true});
                return false;
            }            
            if(!isNumeric($("#valorPago_"+rowIndex+"").val()))
            {
                $('#spinner_procesarPago_'+rowIndex).hide();
                $('.procesarPago').attr('disabled', false);
                $('#modalMensajes .modal-body').html("Valor de pago no válido.Favor verificar.");
                $('#modalMensajes').modal({show: true});
                return false;
            }            
            arrayDetallesInt[7]  = $("#valorPago_"+rowIndex+"").val();
            arrayDetallesInt[8]  = $('#saldoFactura_'+rowIndex).val();
            formaPagPrincipal    = $("#formaPago_"+rowIndex+"").val();
            arrayDetalles[numFila] = arrayDetallesInt;
            floatMontoTotal        += parseFloat(arrayDetallesInt[7]);
        }
        else
        {
            idPtoSelect         = $('#login_'+rowIndex).val();
            idFactSelect        = $('#factura_'+rowIndex+'_'+(numFila-1)+'').val();
            idFormaPagSelect    = $('#formaPago_'+rowIndex+'_'+(numFila-1)+'').val();
            valorPago           = $('#valorPago_'+rowIndex+'_'+(numFila-1)+'').val();
            arrayDetallesInt[0] = intIdPagoAutomaticoCab;
            arrayDetallesInt[1] = data['intIdPagoAutDet'];
            arrayDetallesInt[2] = $('#cliente_'+rowIndex).val();
            arrayDetallesInt[3] = $('#login_'+rowIndex+'_'+(numFila-1)+'').val();
            arrayDetallesInt[4] = $('#factura_'+rowIndex+'_'+(numFila-1)+'').val();
            arrayDetallesInt[5] = $('#formaPago_'+rowIndex+'_'+(numFila-1)+'').val();
            arrayDetallesInt[6] = data['strReferencia'];
            
            if(idClienteSelect  == null || idClienteSelect  == '0' || 
               idPtoSelect      == null || idPtoSelect      == '0' || 
               idFactSelect     == null || idFactSelect     == '0' ||
               idFormaPagSelect == null || idFormaPagSelect == '0')
            {
                $('#spinner_procesarPago_'+rowIndex).hide();
                $('#modalMensajes .modal-body').html("Faltan datos de seleccionar e ingresar. Favor revisar.");
                $('.procesarPago').attr('disabled', false);
                $('#modalMensajes').modal({show: true});
                return false;
            }            
            if(!isNumeric($('#valorPago_'+rowIndex+'_'+(numFila-1)+'').val()))
            {
                $('#spinner_procesarPago_'+rowIndex).hide();
                $('#modalMensajes .modal-body').html("Valor de pago no válido.Favor verificar.");
                $('.procesarPago').attr('disabled', false);                
                $('#modalMensajes').modal({show: true});
                return false;
            }            
            arrayDetallesInt[7] = $('#valorPago_'+rowIndex+'_'+(numFila-1)+'').val();
            arrayDetallesInt[8] = $('#saldoFactura_'+rowIndex+'_'+(numFila-1)+'').val();
            formaPagNuevaFila   = $('#formaPago_'+rowIndex+'_'+(numFila-1)+'').val();
            floatMontoTotal     += parseFloat(arrayDetallesInt[7]);
            arrayDetalles[numFila] = arrayDetallesInt;

            if((formaPagPrincipal !== 0 && formaPagPrincipal !== formaPagNuevaFila) && (formaPagPrincipal==='34' || formaPagPrincipal==='35' || formaPagPrincipal==='37' || formaPagPrincipal==='38' || formaPagNuevaFila==='35' || formaPagNuevaFila==='37' || formaPagNuevaFila==='38'))
            {
                $('#spinner_procesarPago_'+rowIndex).hide();
                $('#modalMensajes .modal-body').html("Error en formas de pago seleccionadas. Para formas de pago grupal deben ser iguales.Favor verificar.");
                $('.procesarPago').attr('disabled', false);
                $('#modalMensajes').modal({show: true});
                return false;
            }            
        }
         
    }
    floatMontoTotal = Math.round(floatMontoTotal * 100)/100;
    var parametros = {
                      "intIdPagoAutomaticoCab" : intIdPagoAutomaticoCab,
                      "intIdPagoAutDet"        : data['intIdPagoAutDet'],
                      "intIdCliente"           : idClienteSelect,
                      "arrayDetalles"          : arrayDetalles
        };    

    if(idClienteSelect === null || idClienteSelect === '0' )
    {
        $('#spinner_procesarPago_'+rowIndex).hide();
        $('#modalMensajes .modal-body').html("Debe seleccionar cliente.");
        $('.procesarPago').attr('disabled', false);
        $('#modalMensajes').modal({show: true});
        return false;
    }
    else if(idPtoSelect == null || idPtoSelect == '0')
    {
        $('#spinner_procesarPago_'+rowIndex).hide();
        $('#modalMensajes .modal-body').html("Debe seleccionar punto.");
        $('.procesarPago').attr('disabled', false);
        $('#modalMensajes').modal({show: true});
        return false;
    }
    else if(idFactSelect == null || idFactSelect == '0')
    {
        $('#spinner_procesarPago_'+rowIndex).hide();
        $('#modalMensajes .modal-body').html("Debe seleccionar factura.");
        $('.procesarPago').attr('disabled', false);
        $('#modalMensajes').modal({show: true});
        return false;
    }
    else if(idFormaPagSelect == null || idFormaPagSelect == '0')
    {
        $('#spinner_procesarPago_'+rowIndex).hide();
        $('#modalMensajes .modal-body').html("Debe seleccionar forma de pago.");
        $('.procesarPago').attr('disabled', false);
        $('#modalMensajes').modal({show: true});
        return false;
    } 
    else if(valorPago === null || valorPago === '0'|| valorPago === '')
    {
        $('#spinner_procesarPago_'+rowIndex).hide();
        $('#modalMensajes .modal-body').html("Debe ingresar valor de pago.");
        $('.procesarPago').attr('disabled', false);
        $('#modalMensajes').modal({show: true});
        return false;
    }     
    else if(floatMontoTotal != floatMonto)
    {
        $('#spinner_procesarPago_'+rowIndex).hide();
        $('#modalMensajes .modal-body').html("El monto del estado de cuenta debe ser igual al valor total a pagar. Favor revisar");
        $('.procesarPago').attr('disabled', false);
        $('#modalMensajes').modal({show: true});
        return false;
    }    
    else
    {
        $.ajax({
            data: parametros,
            url:  urlProcesarPago,
            type: 'post',       
            success: function (response) {
                $('#spinner_procesarPago_'+rowIndex).hide();
                $('#modalMensajes .modal-body').html('Detalle de estado de cuenta fué procesado correctamente.');
                $('#modalMensajes').modal({show: true});
                $('#modalMensajes').on('hidden.bs.modal', function () {
                  location.reload();
                })
            },
            failure: function (response) {
                $('#spinner_procesarPago_'+rowIndex).hide();
                $('#modalMensajes .modal-body').html("No se pudo procesar detalle de estado de cuenta. Por favor consulte con el Administrador.");
                $('.procesarPago').attr('disabled', false);
                $('#modalMensajes').modal({show: true});            
            }
        });        
    }
       
    } );
    (function($, window) {
        'use strict';

        var MultiModal = function(element) {
            this.$element = $(element);
            this.modalCount = 0;
        };

        MultiModal.BASE_ZINDEX = 1040;

        MultiModal.prototype.show = function(target) {
            var that = this;
            var $target = $(target);
            var modalIndex = that.modalCount++;

            $target.css('z-index', MultiModal.BASE_ZINDEX + (modalIndex * 20) + 10);

            // Bootstrap triggers the show event at the beginning of the show function and before
            // the modal backdrop element has been created. The timeout here allows the modal
            // show function to complete, after which the modal backdrop will have been created
            // and appended to the DOM.
            window.setTimeout(function() {
                // we only want one backdrop; hide any extras
                if(modalIndex > 0)
                    $('.modal-backdrop').not(':first').addClass('hidden');

                that.adjustBackdrop();
            });
        };

        MultiModal.prototype.hidden = function(target) {
            this.modalCount--;

            if(this.modalCount) {
               this.adjustBackdrop();
                // bootstrap removes the modal-open class when a modal is closed; add it back
                $('body').addClass('modal-open');
            }
        };

        MultiModal.prototype.adjustBackdrop = function() {
            var modalIndex = this.modalCount - 1;
            $('.modal-backdrop:first').css('z-index', MultiModal.BASE_ZINDEX + (modalIndex * 20));
        };

        function Plugin(method, target) {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('multi-modal-plugin');

                if(!data)
                    $this.data('multi-modal-plugin', (data = new MultiModal(this)));

                if(method)
                    data[method](target);
            });
        }

        $.fn.multiModal = Plugin;
        $.fn.multiModal.Constructor = MultiModal;

        $(document).on('show.bs.modal', function(e) {
            $(document).multiModal('show', e.target);
        });

        $(document).on('hidden.bs.modal', function(e) {
            $(document).multiModal('hidden', e.target);
        });
    }(jQuery, window));
    
    $(document).on( 'click','.crearPago', function () 
    {
        calculaTotal();
        $('#spinner_procesarPago').show();
        var formaPagoSelect        = document.getElementById("formaPago").value;
        var intIdPagoAutomaticoCab = parseInt(arrayInfoDetEstadoCuenta["intidPagAutCab"]);
        var intidPagoAutomaticoDet = parseInt(arrayInfoDetEstadoCuenta["intidPagoAutomaticoDet"]);
        var floatMonto             = parseFloat(arrayInfoDetEstadoCuenta["strMonto"]);
        var errorMsj               = '';
        $('.crearPago').attr('disabled', true);
        if(arrayDetallesPago.length<=0)
        {
            errorMsj += "No existen detalles a procesar. Favor revisar";
        }        
        else if(idClienteSelect===0 && idFormaPago===0)
        {
            errorMsj += "Faltan datos por ingresar. Favor revisar";
        }
        else if(idClienteSelect===0)
        {
            errorMsj += "Seleccione cliente. Favor revisar";
        }
        else if(formaPagoSelect===0 || formaPagoSelect==='0')
        {
            errorMsj += "Seleccione forma de pago. Favor revisar";
        }
        
        if(idClienteSelect===0 || formaPagoSelect===0  || formaPagoSelect==='0'|| arrayDetallesPago.length<=0)
        {
            $('.crearPago').attr('disabled', false);
            $('#modalMensajes .modal-body').html(errorMsj);
            $('#modalMensajes').modal({show: true});
            $('#spinner_procesarPago').hide();
            return false;
        }            
        if(totalPago !== floatMonto)
        {
            $('#spinner_procesarPago').hide();
            $('#modalMensajes .modal-body').html("El valor total a pagar debe ser igual al monto del detalle de estado de cuenta. Favor revisar");
            $('.crearPago').attr('disabled', false);
            $('#modalMensajes').modal({show: true});
            return false;
        }      
        
        var parametros = {
                         "intIdPagoAutomaticoCab" : intIdPagoAutomaticoCab,
                         "intIdPagoAutDet"        : intidPagoAutomaticoDet,
                         "intIdCliente"           : idClienteSelect,
                         "arrayDetalles"          : arrayDetallesPago
           };    

        $.ajax({
            data: parametros,
            url:  urlProcesarPago,
            type: 'post',       
            success: function (response) {
                arrayDetallesPago = [];
                arrayInfoDetEstadoCuenta = [];
                intIdUltDetEstadoCta = 0;
                idClienteSelect = 0;
                numDetalle = 1;
                $('#spinner_procesarPago').hide();             
                $('#modalMensajes .modal-body').html('Detalle de estado de cuenta fué procesado correctamente.');
                $('#modalMensajes').modal({show: true});
                $('#modalMensajes').on('hidden.bs.modal', function () {
                  location.reload();
                })
            },
            failure: function (response) {
                $('#spinner_procesarPago').hide();
                $('#modalMensajes .modal-body').html("No se pudo procesar detalle de estado de cuenta. Por favor consulte con el Administrador.");
                $('.procesarPago').attr('disabled', false);
                $('#modalMensajes').modal({show: true});            
            }
        });      
               
    });   
   
   
    $(document).on( 'click','.crearPagoPre', function () 
    {
        var errorMsj = '';
        $('#spinner_procPagPre').show();
        $('.crearPagoPre').attr('disabled', true);
        
        if(idClienteSelectPre===0 && idFormaPagoPre===0)
        {
            errorMsj += "Faltan datos por ingresar. Favor revisar";
        }
        else if(idClienteSelectPre===0)
        {
            errorMsj += "Seleccione cliente. Favor revisar";
        }
        else if(idFormaPagoPre==0 || idFormaPagoPre=='0')
        {
            errorMsj += "Seleccione forma de pago. Favor revisar";
        }
        
        if(idClienteSelectPre==0 || idFormaPagoPre==0 || idFormaPagoPre=='0')
        {
            $('.crearPagoPre').attr('disabled', false);
            $('#modalMensajes .modal-body').html(errorMsj);
            $('#modalMensajes').modal({show: true});
            $('#spinner_procPagPre').hide();
            return false;
        }
        
        var intIdPagoAutomaticoCab = parseInt(arrayInfoDetEstadoCuentaPre["intidPagAutCab"]);
        var intidPagoAutomaticoDet = parseInt(arrayInfoDetEstadoCuentaPre["intidPagoAutomaticoDet"]);
        var floatMontoPre          = parseFloat(arrayInfoDetEstadoCuentaPre["strMonto"]);
        var strReferencia          = arrayInfoDetEstadoCuentaPre["strReferencia"]; 
        var info = null;
        let valorTotal = null;
        var arrayDetPagoPrecargado = '';
        listaInfoDetalle.$('input[type="checkbox"]').each(function () {            
            if (this.checked)
            {
                info = listaInfoDetalle.row($(this).closest('tr')).data();
                valorTotal= listCheck.find((e)=>e.id==info.intIdPagDet);
                arrayDetPagoPrecargado = intIdPagoAutomaticoCab+'|'+intidPagoAutomaticoDet+'|'+info.intIdCliente+'|'+info.intIdPunto+'|'
                                         +info.intIdDocumento+'|'+idFormaPagoPre+'|'+strReferencia+'|'+valorTotal.valor+'|'+info.strSaldo;
                arrayDetallesPagoPrecargado.push(arrayDetPagoPrecargado);
            }
        });
        
        if(arrayDetallesPagoPrecargado.length <=0 )
        {
            errorMsj = "No existen detalles de pago seleccionados. Favor Revisar.";
            $('.crearPagoPre').attr('disabled', false);
            $('#spinner_procPagPre').hide();
            $('#modalMensajes .modal-body').html(errorMsj);
            $('#modalMensajes').modal({show: true});
            return false;
        }
        
        if(totalPagoPre !== floatMontoPre)
        {
            $('#spinner_procPagPre').hide();
            $('.crearPagoPre').attr('disabled', false);
            $('#modalMensajes .modal-body').html("El valor total a pagar debe ser igual al monto del detalle de estado de cuenta. Favor revisar");
            $('#modalMensajes').modal({show: true});
            return false;
        }     
                
        var parametrosPrec = {
                                "intIdPagoAutomaticoCab" : intIdPagoAutomaticoCab,
                                "intIdPagoAutDet"        : intidPagoAutomaticoDet,
                                "intIdCliente"           : idClienteSelectPre,
                                "intIdFormaPago"         : idFormaPagoPre,
                                "arrayDetalles"          : arrayDetallesPagoPrecargado
                             };    

        $.ajax({
            data: parametrosPrec,
            url:  urlProcesarPago,
            type: 'post',       
            success: function (response) {
                arrayDetallesPagoPrecargado = [];
                arrayInfoDetEstadoCuentaPre = [];
                idClienteSelectPre          = 0;
                numDetalle                  = 1;
                $('#spinner_procPagPre').hide();             
                $('#modalMensajes .modal-body').html('Detalle de estado de cuenta fué procesado correctamente.');
                $('#modalMensajes').modal({show: true});
                $('#modalMensajes').on('hidden.bs.modal', function () {
                  location.reload();
                })
            },
            failure: function (response) {
                $('#spinner_procPagPre').hide();
                $('#modalMensajes .modal-body').html("No se pudo procesar detalle de estado de cuenta. Por favor consulte con el Administrador.");
                $('.crearPagoPre').attr('disabled', false);
                $('#modalMensajes').modal({show: true});            
            }
        });      
               
    });   
   
    /**
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 06-10-2020          
     */     
   //Elimina fila detalle del estado de cuenta.   
   $(document).on( 'click','.eliminarDetalle', function () {

    var table            = $('#tabla_lista_pago_automatico_det').DataTable();
    var data             = table.row( $(this).parents('tr') ).data();
    var tr               = $(this).closest('tr');
    var indiceFilaActual = $(this).closest('tr').index();
    var row              = table.row( tr );
    var idElemento       = $(this).data('id');
    var strIndice        = idElemento.toString();
    var arrayIndice      = strIndice.split('_');    
    var rowIndex         = row.index();
    var nuevoTotal       = $('#total_'+arrayIndice[1]).val();
    var nuevoTotal       = $('#total_'+arrayIndice[1]).val();
    var valorPagoReg     = $('#valorPago_'+arrayIndice[1]+'_'+arrayIndice[2]+'').val();
    if(valorPagoReg>0)
    {
        nuevoTotal  -= parseFloat($('#valorPago_'+arrayIndice[1]+'_'+arrayIndice[2]+'').val());
        $('#total_'+arrayIndice[1]).val(nuevoTotal);
    }
    $("#detalleNew_"+arrayIndice[1]+"_"+arrayIndice[2]+"").remove();
    $("#detalleClonedNew_"+arrayIndice[1]+"_"+arrayIndice[2]+"").remove();

   } );   
  
    /**
     * @version 1.0 13-10-2020          
     */
    
   //Funcionalidad para visualizar estado de cuenta de una factura.   
   $(document).on( 'click','.verEstadoCtaFactura', function () {
    var idPagoSelect = ''; 
    var idPago       = $(this).data('id');
    var strIndice    = idPago.toString();
    var arrayIndice  = strIndice.split('_');
    idPagoSelect     = arrayIndice[1];
    Ext.override(Ext.data.proxy.Ajax, {timeout: 900000});
    
    //Modelo para el listado de Errores
    Ext.define('ErroresModel',
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'comentario_error', type: 'string'},
                {name: 'login', type: 'string'},
                {name: 'origen_documento', type: 'string'},
            ]
        });

    //Store para el listado de errores
    var store_errores = Ext.create('Ext.data.JsonStore',
        {
            model: 'ErroresModel',
            proxy: {
                type: 'memory',
                reader: {
                    type: 'json',
                    root: 'listado_errores'
                }
            }
        });
        
    var objRequest = Ext.Ajax.request({
            url: urLStoreErrores,
            method: 'post',
            timeout: 99999,
            async: false,         
            success: function(response) {
                                
                Ext.each(Ext.JSON.decode(response.responseText), function(json){                   
                   Ext.each(json.listado_errores, function(array){                       
                        store_errores.add(array);
                   });
                   
                });
               
            },
            scope: store_errores
        });       

    
    //Listado de errores
    var listErrores = Ext.create('Ext.grid.Panel', {
        width: 1300,
        height: 250,
        collapsible: true,
        title: 'Listado',
        store: store_errores,
        viewConfig: {
            emptyText: 'No existen errores a presentar'
        },
        columns:
            [
                {
                    text: 'Error',
                    flex: 500,
                    dataIndex: 'comentario_error'
                }, {
                    text: 'Login Pago',
                    flex: 100,
                    dataIndex: 'login'
                }, {
                    text: 'Origen',
                    flex: 35,
                    dataIndex: 'origen_documento'
                }
            ]
    });

    //Errores
    var myPanel = new Ext.Panel({
        title: 'Errores...',
        items: [listErrores],
    });    
    
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'documento', type: 'string'},
            {name: 'punto', type: 'string'},
            {name: 'valor_ingreso', type: 'string'},
            {name: 'valor_egreso', type: 'string'},
            {name: 'acumulado', type: 'string'},
            {name: 'Fecreacion', type: 'string'},
            {name: 'strFeEmision', type: 'string'},
            {name: 'strFeAutorizacion', type: 'string'},
            {name: 'tipoDocumento', type: 'string'},
            {name: 'oficina', type: 'string'},
            {name: 'referencia', type: 'string'},
            {name: 'formaPago', type: 'string'},
            {name: 'numero', type: 'string'},
            {name: 'observacion', type: 'string'},
            {name: 'boolSumatoriaValorTotal', type: 'string'},
        ]
    });

    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            async: false,
            url: urlgetEstadoCuentaPorFactura,
            reader: {
                type: 'json',
                root: 'documentos',
                totalProperty: 'total'
            },
            extraParams: {idPagoSelect: idPagoSelect},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.idPagoSelect = idPagoSelect;
            },
            load: function(store) {
                linea_tabla = "<table  width='50%' id='table-3'>";
                linea_tabla += "<thead>";
                linea_tabla += "<tr>";
                linea_tabla += "<th>Fecha creacion</th>";
                linea_tabla += "<th>Oficina</th>";
                linea_tabla += "<th>Tipo documento</th>";
                linea_tabla += "<th>Documento</th>";
                linea_tabla += "<th>Referencia</th>";
                linea_tabla += "<th>Punto</th>";
                linea_tabla += "<th>Ingreso</th>";
                linea_tabla += "<th>Egreso</th>";
                linea_tabla += "<th>Sumatoria</th>";
                linea_tabla += "</tr>";
                linea_tabla += "</thead>";
                linea_tabla += "<tbody>";
                store.each(function(record) {
                    linea_tabla += "<tr><td>" + record.data.Fecreacion + "</td>";
                    linea_tabla += "<td>" + record.data.oficina + "</td>";
                    linea_tabla += "<td>" + record.data.tipoDocumento + "</td>";
                    linea_tabla += "<td>" + record.data.documento + "</td>";
                    linea_tabla += "<td>" + record.data.referencia + "</td>";
                    linea_tabla += "<td>" + record.data.punto + "</td>";
                    linea_tabla += "<td>" + record.data.valor_ingreso + "</td>";
                    linea_tabla += "<td>" + record.data.valor_egreso + "</td>";
                    linea_tabla += "<td>" + record.data.acumulado + "</td></tr>";
                });
                linea_tabla += "</tbody>";
                linea_tabla += "</table>";

                $('#estado_cuenta').html(linea_tabla);
                $('#tabla_lista_pago_automatico_det').DataTable().ajax.reload();

            }
        }
    });

    store.load();

    var listView = Ext.create('Ext.grid.Panel', {
        width: 1300,
        height: 700,
        collapsible: false,
        title: 'Estado de Cuenta Por Punto',
        store: store,
        multiSelect: false,
        viewConfig: {
            stripeRows: true,
            enableTextSelection: true,
            emptyText: 'No hay datos para mostrar',
            getRowClass: function(record, index) {
                var cls = '';
                if (record.data.documento == 'MOVIMIENTOS' ||
                    record.data.documento == 'Anticipos no aplicados' ||
                    record.data.documento == 'Anticipos asignados' ||
                    record.data.documento == 'SALDO:' ||
                    record.data.documento == 'RESUMEN PTO CLIENTE:' ||
                    record.data.documento == 'Historial Anticipos asignados')
                {
                    cls = 'estado_cta';
                    record.data.valor_ingreso = '';
                    record.data.valor_egreso = '';
                    record.data.acumulado = '';
                }

                if (record.data.documento == 'Total:')
                {
                    cls = 'total_estado_cta';
                }
                
                if (record.data.observacion != '' && record.data.observacion != null)
                {
                    cls = 'multilineColumn';
                }
                
                if (record.data.oficina != '' && record.data.oficina != null)
                {
                    cls = 'multilineColumn';
                }
                //Se marca en otro color en el estado de cuenta para el caso de ANTC que no sumarizan el saldo en el estado de cuenta.
                if(record.data.boolSumatoriaValorTotal=='false')
                {
                    cls = 'antc_estado_cta'; 
                }
                return cls;
            }
        },
        listeners: {
            itemdblclick: function(view, record, item, index, eventobj, obj) {
                var position = view.getPositionByEvent(eventobj),
                    data = record.data,
                    value = data[this.columns[position.column].dataIndex];
                Ext.Msg.show({
                    title: 'Copiar texto?',
                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                    buttons: Ext.Msg.OK,
                    icon: Ext.Msg.INFORMATION
                });
            }
        },
        columns: [{
                text: 'F. Creacion',
                dataIndex: 'Fecreacion',
                align: 'right',
                width: 70,
                tdCls: 'x-change-cell'
            }, {
                text: 'F. Emision',
                dataIndex: 'strFeEmision',
                align: 'right',
                width: 70,
                tdCls: 'x-change-cell'
             }, {
                text: 'F. Autorizacion',
                dataIndex: 'strFeAutorizacion',
                align: 'right',
                width: 90,
                tdCls: 'x-change-cell'    
            }, {
                text: 'Oficina',
                width: 150,
                dataIndex: 'oficina',
                tdCls: 'x-change-cell  x-grid-cell-inner'
            }, {
                text: 'No. documento',
                width: 200,
                dataIndex: 'documento',
                tdCls: 'x-change-cell'
            }, {
                text: 'Doc.',
                width: 50,
                dataIndex: 'tipoDocumento',
                tdCls: 'x-change-cell'
            }, {
                text: 'F. Pago',
                width: 50,
                dataIndex: 'formaPago',
                tdCls: 'x-change-cell'
            }, {
                text: '',
                width: 100,
                dataIndex: 'numero',
                tdCls: 'x-change-cell'
            }, {
                text: 'Observacion',
                dataIndex: 'observacion',
                tdCls: 'x-change-cell x-grid-cell-inner',
                flex: 1
            }, {
                text: 'Ingreso',
                width: 130,
                align: 'right',
                dataIndex: 'valor_ingreso',
                tdCls: 'x-change-cell'
            }, {
                text: 'Egreso',
                width: 130,
                align: 'right',
                dataIndex: 'valor_egreso',
                tdCls: 'x-change-cell'
            }, {
                text: 'Saldo',
                dataIndex: 'acumulado',
                align: 'right',
                tdCls: 'x-change-cell',
                width: 130
            }]
    });


    var myForm = new Ext.form.Panel({
        width: 1300,
        height: 730,
        autoScroll: true,
        title: 'Estado de Cuenta',
        items: [listView],
        floating: true,
        closable : true
    });
    myForm.show();    
    
    });    

});

/**
 * Función para visualización de opción enviada como parámetro,
 *   
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 20-05-2022
 * @since 1.0
 */
function mostrarOpcion(strUrlOpcion) {
    window.location.href = strUrlOpcion; 
}

function limpiarFormInfoCliente() 
{
    if(arrayDetallesPago.length == 0)
    {
        $('#cliente').prop('disabled',false);
        $('#cliente').val(null).trigger('change');
        $('#cliente').empty().trigger('change');
        
        $('#formaPago').prop('disabled',false);
        $('#formaPago').val='0';
        
        $('#login').val(null).trigger('change');
        $('#login').empty().trigger('change');
    }
    
    $('#facturasCliente').empty().trigger('change');        
    $('#facturasCliente').val(null).trigger('change');
    $('#saldoFactura').val(null).trigger('change');
    $('#saldoFactura').val(""); 
    $('#valorPago').val("");        
} 

function inicializarDetallesPago()
{
    arrayDetallesPago = [];
}

function limpiarFormInfoPrecargado() 
{
    totalPagoPre                = 0;
    idClienteSelectPre          = 0;
    arrayDetallesPagoPrecargado = [];
    $('#clientePre').prop('disabled',false);
    $('#formaPagoPre').prop('disabled',false);        
    $('.crearPagoPre').attr('disabled', false);
    $('#info-select-all').prop('checked',false);
    $('#totalPre').html(totalPagoPre); 
    $('#valorTotalPre').html(totalPagoPre);         
    $('#clientePre').val(null).trigger('change');
    $('#clientePre').empty().trigger('change');
    //$('#formaPagoPre').empty().trigger('change');
    //$('#formaPagoPre').val(null).trigger('change');       
    $('#infoPagoDetPre').DataTable().ajax.reload();        
}

function limpiarFormParcialInfoCliente() 
{
    $('#valorPago').val("");
}   

/**
 * Valida  valor numérico entero o decimal
 * @author Edgar Holguin <eholguin@telconet.ec>
 * @version 1.0 04-11-2020
 * @since 1.0
 */
function isNumeric(input){
    var expReg = /^([0-9]+\.?[0-9]{0,2})$/; 
    return (expReg.test(input));
}
function filterFloat(evt,input){
    // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
    var key = window.Event ? evt.which : evt.keyCode;    
    var chark = String.fromCharCode(key);
    var tempValue = input.value+chark;
    if(key >= 48 && key <= 57){
        if(isNumeric(tempValue)=== false){
            return false;
        }else{       
            return true;
        }
    }else{
            if(key == 8 || key == 13 || key == 46 || key == 0) {            
                return true;              
            }else{
                return false;
            }
    }
}   
function calculaTotal()
{
    totalPago = 0;     
    for(var i=1;i<numDetalle;i++)
    {
        var valor=$('#valorPago'+i).val();
        if(valor !== undefined)
        {
            totalPago += parseFloat($('#valorPago'+i).val());
        }
    }
    totalPago = (Math.round(totalPago * 100)/100);
    $('#total').html(parseFloat(totalPago).toFixed(2)); 
    $('#valorTotal').html(parseFloat(totalPago).toFixed(2));
    
} 

function nuevoTotalCloned(nameInputId, valueInputId)
{
    var objTable=document.querySelectorAll('[aria-describedby="tabla_lista_pago_automatico_det_info"]')[1].children[1].getElementsByClassName("total");
    for (let i of objTable) 
    {
        if(i.id===nameInputId)
        {
            i.value=valueInputId;
        }
    }
}

function add(button) 
{
    var row = button.parentNode.parentNode;
    var cells = row.querySelectorAll('td:not(:last-of-type)');
    agregarDetalle(cells);
    calculaTotal();
}
(function() {
    'use strict';
    window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
        }, false);
    });
    }, false);
})();
function createRemoveBtn() 
{
    var btnRemove = document.createElement('button');
    btnRemove.className = 'btn btn-xs btn-danger';
    btnRemove.onclick = remove;
    btnRemove.innerText = 'Descartar';
    return btnRemove;
}
function remove(button) 
{
    var row = button.parentNode.parentNode;
    var cells = row.querySelectorAll('td:not(:last-of-type)');
    // totalPago -= parseFloat(cells[5].children[0].value).toFixed(2);
    // totalPago = parseFloat(totalPago).toFixed(2);
    // $('#total').html(totalPago); 
    // $('#valorTotal').html(totalPago);  
    // eliminar del array los respectivos valores para el insert
    if(arrayDetallesPago.length==1){
        arrayDetallesPago=[];
    }else{
        for(let i = 0; i < arrayDetallesPago.length; i++)
        {
        if(arrayDetallesPago[i].includes(parseFloat(cells[5].children[0].value)))
        {
                arrayDetallesPago.splice(i,1);
        }
        }
    }             
    document.querySelector('#infoPagoDet tbody').removeChild(row);
    calculaTotal();
}

function agregarDetalle(cells) 
{

    idClienteSelect      = document.getElementById("cliente").value;
    var loginSelect      = document.getElementById("login").value;
    var facturaSelect    = document.getElementById("facturasCliente").value;
    var saldoSelect      = document.getElementById("saldoFactura").value;
    var formaPagoSelect  = document.getElementById("formaPago").value;
    var valorPago        = document.getElementById("valorPago").value;
    var errorMsj         = '';    
    
    var strLoginSelect      = $('#login option:selected').text();
    var strFacturaSelect    = $('#facturasCliente option:selected').text();
    var strSaldoSelect      = $('#saldoFactura').val();
    var strFormaPagoSelect  = $('#formaPago option:selected').text();

    if(idClienteSelect==0 && formaPagoSelect==0 || idClienteSelect==0 && formaPagoSelect=='0'
        || idClienteSelect== '0' || loginSelect== '0' || facturaSelect== '0')
    {
        errorMsj += "Faltan datos por ingresar. Favor revisar";
    }
    else if(idClienteSelect===0)
    {
        errorMsj += "Seleccione cliente. Favor revisar";
    }
    else if(formaPagoSelect===0 || formaPagoSelect==='0')
    {
        errorMsj += "Seleccione forma de pago. Favor revisar";
    }
    
    if(idClienteSelect===0 || formaPagoSelect===0 || formaPagoSelect==='0')
    {
        $('.crearPago').attr('disabled', false);
        $('#modalMensajes .modal-body').html(errorMsj);
        $('#modalMensajes').modal({show: true});
        $('#spinner_procesarPago').hide();
        return false;
    }

    var strDetallePago = '';
    strDetallePago     = arrayInfoDetEstadoCuenta["intidPagAutCab"]+'|'+arrayInfoDetEstadoCuenta["intidPagoAutomaticoDet"]
            +'|'+idClienteSelect+'|'+loginSelect+'|'+facturaSelect+'|'+formaPagoSelect+'|'+arrayInfoDetEstadoCuenta["strReferencia"]
            +'|'+valorPago+'|'+saldoSelect;
    if(idClienteSelect =='' && loginSelect =='' && facturaSelect =='' && saldoSelect =='' && formaPagoSelect =='0'  || 
        valorPago == ""|| parseFloat(valorPago) === 0)
    {
        //$('#spinner_procesarPago_'+rowIndex).hide();
        $('#modalMensajes .modal-body').html("Faltan datos a ingresar. Favor revisar");
        $('.procesarPago').attr('disabled', false);
        $('#modalMensajes').modal({show: true});
        return false;
    }

    if(numDetalle > intCantMaxRegistros)
    {
        //$('#spinner_procesarPago_'+rowIndex).hide();
        $('#modalMensajes .modal-body').html("La cantidad máxima de detalles permitidos es: "+intCantMaxRegistros+" .Favor revisar");
        $('.procesarPago').attr('disabled', false);
        $('#modalMensajes').modal({show: true});
        return false;
    }       
    // if(clienteSelect !=='' && loginSelect !=='' && facturaSelect !=='' && saldoSelect !=='' && formaPagoSelect !=='0' && parseFloat(valorPago) > 0)
    // {
        arrayDetallesPago.push(strDetallePago);
        var newRow = document.createElement('tr');
        $(newRow).append('<td>'+numDetalle+'</td>');
        $(newRow).append('<td>'+strLoginSelect+'</td>');
        $(newRow).append('<td>'+strFacturaSelect+'</td>');
        $(newRow).append('<td>'+strSaldoSelect+'</td>');
        $(newRow).append('<td>'+strFormaPagoSelect+'</td>');
        $(newRow).append('<td><input type="text" class="form-control input-sm valorPago" id="valorPago'+numDetalle+'" readonly onkeypress="return filterFloat(event,this);" onChange="calculaTotal(this)" value="'+valorPago+'" ></td>');
        $(newRow).append('<td><button type="button" class="addDetalle btn btn-outline-dark btn-sm" onclick="remove(this)" title="Eliminar Detalle"><i class="fa fa-trash-o"></i></button></td>');
        numDetalle++;
        document.querySelector('#infoPagoDet tbody').appendChild(newRow);
        
    // }
    limpiarFormParcialInfoCliente();
}

function generarPago(idPagoAutomaticoDet)
{  
    var dataStoreHistorial = new Ext.data.Store
    ({
        autoLoad: true,
        total: 'total',
        proxy:
        {
            type: 'ajax',
            timeout: 600000,
            url: urlHistorialRetencion,
            extraParams: {				
                intIdPagoAutDet: idPagoAutomaticoDet
            },                
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
        [
            {name: 'detalle', mapping: 'detalle', type: 'string'},
            {name: 'motivo', mapping: 'motivo', type: 'string'},
            {name: 'estado', mapping: 'estado', type: 'string'},
            {name: 'usuario', mapping: 'usuario', type: 'string'},
            {name: 'fecha', mapping: 'fecha', type: 'string'}
        ]
    });

    var gridHistorial = Ext.create('Ext.grid.Panel',
    {
        id: 'gridHistorial',
        store: dataStoreHistorial,
        width: 790,
        height: 300,
        collapsible: false,
        multiSelect: true,
        viewConfig: 
        {
            emptyText: '<br><center><b>No hay datos para mostrar',
            forceFit: true,
            stripeRows: true,
            enableTextSelection: true
        },
        listeners: 
        {
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
                        grid.recordIndex = recordIndex;
                    }
                });

                grid.tip = Ext.create('Ext.tip.ToolTip',
                {
                    target: view.el,
                    delegate: '.x-grid-cell',
                    trackMouse: true,
                    autoHide: false,
                    renderTo: Ext.getBody(),
                    listeners:
                    {
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();

                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });

                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        layout: 'fit',
        region: 'center',
        buttons:
        [
            {
                text: 'Cerrar',
                handler: function()
                {
                    win.destroy();
                }
            }
        ],
        columns:
        [
            {
                dataIndex: 'detalle',
                header: 'Observaci\xf3n',
                width: 300
            }, 
            {
                dataIndex: 'motivo',
                header: 'Motivo',
                width: 150
            },                  
            {
                dataIndex: 'estado',
                header: 'Estado',
                width: 70
            },
            {
                dataIndex: 'usuario',
                header: 'Usuario',
                width: 100
            },
            {
                dataIndex: 'fecha',
                header: 'Fecha',
                width: 150
            }
        ]
    });

    Ext.create('Ext.form.Panel',
    {
        id: 'formHistorialPunto',
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults:
        {
            labelAlign: 'left',
            labelWidth: 125,
            msgTarget: 'side'
        },
        items:
        [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults:{ width: 700 },
                layout:
                {
                    type: 'table',
                    columns: 4,
                    align: 'left'
                },
                items:[ gridHistorial ]
            }
        ]
    });

    var win = Ext.create('Ext.window.Window',
    {
        title: 'Historial Retención',
        modal: true,
        width: 800,
        closable: true,
        layout: 'fit',
        items: [gridHistorial]
    }).show();
}   

function cambioValor(data){
    var x=$('#check'+data).is(":checked");
    var valor = $('#Valor'+data).val();

    if(x)
    {
    totalPagoPre -= parseFloat(valor);
    totalPagoPre = Math.round(totalPagoPre * 100)/100;
    
    $('#totalPre').html(parseFloat(totalPagoPre).toFixed(2)); 
    $('#valorTotalPre').html(parseFloat(totalPagoPre).toFixed(2));
    listCheck=listCheck.filter((e)=>e.id!=data);

    $('#check'+data).prop("checked", false);

    }      
}

function soloNumeros(data){
    var x=$('#Valor'+data).val();
    var y=x.replace(/[^\d.-]/g, '');
        $('#Valor'+data).val(y);
}