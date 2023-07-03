$(function () {
    $('#dpFechaNotificacionFinal').datetimepicker({
        useCurrent: false,
        format: 'DD/MM/YYYY  HH:mm',
        //defaultDate: new Date(),
    });
    //$('#dpFechaNotificacionFinal').datepicker('setDate', 'now');
});



$(document).ready(function() {

    mostrarLeyendas();

    //Define los detalles del cuadro de asignaciones
    detalleAsignacionesTelefonica =   $('#detalleAsignacionesTelefonica').DataTable( {
        "pagingType": "full_numbers",
        "language": {
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "Sin resultados encontrados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "loadingRecords": "Cargando datos...",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "columns": [
           
            {"data": "numero",
            "title": "#",
            "orderable": false
            },
            {"data": "tipo",
             "title":"Tipo",
             "render":function(data, type, full, meta)
                      {
                          var colorTipo = "#cfd8dc;";

                          if(full.tipo == 'CASO')
                          {
                            colorTipo = "#fbc02d;";
                          }
                          else if(full.tipo == 'TAREA')
                          {
                            colorTipo = "#7cb342;";
                          }
                          var strDatoRetorna=
                                             "<div style=\"padding:0.5em;background-color:"+colorTipo+"\">"+full.tipo+"</div>";
                          return strDatoRetorna;
                      }
            },
            {"data": "ticket",
             "title":"Ticket",
             "render":function(data, type, full, meta)
                      {
                          var colorTipo = "#cfd8dc;";

                          if(full.tipo == 'CASO')
                          {
                            colorTipo = "#fff263;";
                          }
                          else if(full.tipo == 'TAREA')
                          {
                            colorTipo = "#bef67a;";
                          }
                          var strDatoRetorna=
                                             "<div style=\"padding:0.5em;background-color:"+colorTipo+"\">"+full.ticket+"</div>";
                          return strDatoRetorna;
                      }
            },
            {"data": "tramo",
             "title": "Circuito / Tramo",
             "render":function(data, type, full, meta)
                      {
                          var colorTipo = "#cfd8dc;";

                          var strDatoRetorna=
                                             "<div><b>"+full.circuito+
                                             "</b></div><div style=\"padding:0.5em;background-color:"+colorTipo+"\">"+
                                             full.tramo+
                                             "</div>";
                          return strDatoRetorna;
                      }
            },  
            {"data": "fecha",
             "title":"Fecha Inicio"
            },
            {"data": "fechaFin",
             "title": "Fecha Fin"
            },
            {"data": "versionInicial",
             "title": "Versión Inicial (Observación)",
             "render":function(data, type, full, meta)
                      {
                        var strDatoRetorna = full.versionInicial;
                          if (full.tipo == 'TAREA')
                          {
                            strDatoRetorna = full.observacion;
                          }
                          return strDatoRetorna;
                      }
            },
            {"data": "ult_seguimiento",
             "title":"Ult. Seguimiento",
             "render":function(data, type, full, meta)
                      {
                        var strDatoRetorna = full.ult_seguimiento;
                          if (full.tipo == 'CASO')
                          {
                            strDatoRetorna = "";
                            var arrayDeCadenas = full.ult_seguimiento.split("|");
                            for (var i=0; i < arrayDeCadenas.length; i++) 
                            {
                                var arrayTarea = arrayDeCadenas[i].split("**");
                                for (var x=0; x < arrayTarea.length; x++) 
                                {
                                    var strTexto = arrayTarea[x];
                                    if (x == 0)
                                    {
                                        strTexto = "<strong>"+strTexto+"</strong>";
                                    }
                                    strTexto = strTexto.replace('UltSeg:','');
                                    strDatoRetorna = strDatoRetorna + strTexto+"<br>"; 
                                }
                                strDatoRetorna = strDatoRetorna+"<br>";
                            }
                          }
                          return strDatoRetorna;
                      }
            },
            {"data": "estado",
             "title":"Estado",
             "render":function(data, type, full, meta)
                      {
                          var color = "#cfd8dc;";

                          if(full.estado == 'Cerrado' || full.estado == 'Finalizada')
                          {
                            color = "#4db6ac;";
                          }
                          else if(full.estado == 'Asignado' || full.estado == 'Asignada')
                          {
                            color = "#9fa8da;";
                          }
                          else if(full.estado == 'Reprogramada')
                          {
                            color = "#bcaaa4;";
                          }
                          var strDatoRetorna = "<div style=\"padding:0.5em;background-color:"+color+"\">"+full.estado+"</div>";

                          if (full.tipo == 'CASO')
                          {
                                countTareasFinalizadas = 0;
                                var jsonTareas = JSON.parse(full.tareas);
                                for(var indice=0; indice < jsonTareas.length; indice ++)
                                {
                                    if (jsonTareas[indice].ESTADO == 'Finalizada')
                                    {
                                        countTareasFinalizadas = countTareasFinalizadas + 1;
                                    }
                                }
                                if (jsonTareas.length != 0 && jsonTareas.length == countTareasFinalizadas && full.estado != 'Cerrado')
                                {
                                    strDatoRetorna = "<div style=\"padding:0.5em;background-color:"+"#ff867a"+"\">Cerrar Caso</div>";
                                }
                          }
                          
                          return strDatoRetorna;
                      }
            },
            {"data": "notificacion",
             "title": "Notificacion",
             "render":function(data, type, full, meta){

                strDatoRetorna = "";
                if(full.tipo == 'CASO')
                {
                    strDatoRetorna = full.notificacion;
                }
                if ( (full.tipo == 'CASO') && (strDatoRetorna == null || strDatoRetorna == "" || strDatoRetorna == "SIN NOTIFICAR") )
                {
                    strDatoRetorna = "SIN NOTIFICAR";
                }

                return strDatoRetorna;

             }
            },
            {"data": "acciones",
             "title":"Acciones",
             "orderable": false,
             "render": function (data, type, full, meta)
                       {
                           var strDatoRetorna = '';
                           strDatoRetorna    +=     '<span class="hint--bottom-right hint--default hint--medium'+
                           ' hint--rounded" aria-label="Ver información del pendiente">'+
                           '<button type="button" class="btn btn-default btn btn-md" '+
                           '        onClick=\'javascript:mostrarInformacionDelPendiente('+JSON.stringify(full)+');\'>'+
                           '    <span class="glyphicon glyphicon-search"></span>'+
                           '</button>'+
                           '</span>';
                           //Notificación
                           if(full.tipo == 'CASO' && full.circuito != "" && full.tramo != "") 
                           {
                               if ( full.notificacion == 'INICIAL')
                               {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                            ' hint--rounded" aria-label="Enviar Notificación Final">'+
                                                            '<button type="button" class="btn btn-default btn-md" '+
                                                            '   onClick="javascript:mostrarEnviarNotificacionFinal(\''+full.id_asignacion+'\');">'+
                                                            '    <span class="glyphicon glyphicon-send"></span>'+
                                                            '</button>'+
                                                        '</span>';
                               }
                               else if (full.notificacion == null || full.notificacion == "" || full.notificacion == "SIN NOTIFICAR")
                               {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                            ' hint--rounded" aria-label="Enviar Notificación Inicial">'+
                                                            '<button type="button" class="btn btn-default btn-md" '+
                                                            '   onClick="javascript:mostrarConfirmacionEnviarNotificacionInicial(\''+full.id_asignacion+'\');">'+
                                                            '    <span class="glyphicon glyphicon-send"></span>'+
                                                            '</button>'+
                                                        '</span>';
                               }
                           }
                           //Cerrar pendiente e ingresar tramo
                           if(full.estado_pendiente !== null && full.estado_pendiente != 'Cerrado' &&  
                              full.estado != 'Finalizada' && full.notificacion != 'FINAL')
                           {
                                strDatoRetorna    +=     '<span class="hint--bottom-right hint--default hint--medium'+
                                                            ' hint--rounded" aria-label="Ingresar Tramo">'+
                                                            '<button type="button" class="btn btn-default btn btn-md" '+
                                                            '   onClick="javascript:mostrarIngresarTramo(\''+full.id_asignacion+'\',\''+full.tipo+'\');">'+
                                                            '    <span class="glyphicon glyphicon-edit"></span>'+
                                                            '</button>'+
                                                            '</span>';
                           }

                            if (full.estado_pendiente !== null && full.estado_pendiente != 'Cerrado') 
                            {
                                if(full.estado == 'Cerrado' || full.estado == 'Finalizada' || full.estado == 'Cancelada')
                                {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                            ' hint--rounded" aria-label="Cerrar Pendiente">'+
                                                            '<button type="button" class="btn btn-default btn-md" '+
                                                            '  onClick="javascript:mostrarConfirmacionCerrarPendiente(\''+full.id_asignacion+'\');">'+
                                                            '    <img src=\"/public/images/images_crud/lock_open.png\" width=\"18\" height=\"18\" />'+
                                                            '</button>'+
                                                        '</span>';
                                }
                            }
                            else
                            {
                                strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                        ' hint--rounded" aria-label="Pendiente Cerrado">'+
                                                        '<button type="button" class="btn btn-default btn-md" '+
                                                        '     onClick="">'+
                                                        '    <img src=\"/public/images/images_crud/lock_close.png\" width=\"18\" height=\"18\" />'+
                                                        '</button>'+
                                                    '</span>';
                            }

                           return strDatoRetorna;
                       }
            }
        ],
        "columnDefs":
        [
            {"className": "dt-center verticalAlignCellDt columnas-list", "targets": "_all"},
            { "width": "8", "targets": 0 },
            { "width": "8", "targets": 1 },
            { "width": "8", "targets": 2 },
            { "width": "50", "targets": 3 },
            { "width": "50", "targets": 4 },
            { "width": "50", "targets": 5 },
            { "width": "190", "targets": 6 },
            { "width": "190", "targets": 7 },
            { "width": "10", "targets": 8 },
            { "width": "10", "targets": 9 },
            { "width": "10", "targets": 10 }
        ],
        "paging":true
    } )
    .on('preXhr.dt', function ( e, settings, data ) {
               $("#divLoaderDetalleAsignacionesTelefonica").show();
        } )
    .on('xhr.dt', function ( e, settings, json, xhr ) {
        rowsData = [
            ["ITEM", "TIPO","TICKET", "CIRCUITO", "TRAMO","FECHA INICIO","FECHA FIN","VERSIÓN INICIAL (OBSERVACIÓN)","ULT. SEGUIMIENTO","ESTADO","NOTIFICACIÓN"]
        ];
        if (json != null)
        { 
            for(var indice=0; indice < json.data.length; indice ++)
            {
                var versionInicial = json.data[indice].versionInicial;

                if (json.data[indice].tipo == 'TAREA')
                {
                  versionInicial = json.data[indice].observacion;
                }
                if(json.data[indice].id_asignacion == document.getElementById('txtIdAsignacion').value && $("#divInfoPendiente").is(":visible"))
                {
                    mostrarInformacionDelPendiente(json.data[indice]);
                }
                var arrayUltSeguimiento = json.data[indice].ult_seguimiento.split("|");
                var ult_seguimiento = json.data[indice].ult_seguimiento.replaceAll("**"," ");
                if (arrayUltSeguimiento.length<=2)
                {
                    ult_seguimiento = ult_seguimiento.replaceAll("|","");
                }
                else
                {
                    ult_seguimiento = ult_seguimiento.replaceAll("|","  |  ");
                }
                ult_seguimiento = ult_seguimiento.replaceAll("UltSeg:","Ult. Seguimiento:");
                rowsData.push([json.data[indice].numero,json.data[indice].tipo,json.data[indice].ticket,
                    json.data[indice].circuito,json.data[indice].tramo,json.data[indice].fecha,json.data[indice].fechaFin,
                    versionInicial,ult_seguimiento,json.data[indice].estado,json.data[indice].notificacion]);
            }
        }
       $("#divLoaderDetalleAsignacionesTelefonica").hide();
    } );

    $('#detalleAsignacionesTelefonica tbody').on('click', 'td', function () {
        var td = $(this).closest('td');
        if(td.prevObject[0].cellIndex===0 && td.prevObject[0].className==='details-control sorting_1')
        {
            var tr = $(this).closest('tr');
            var row = detalleAsignacionesTelefonica.row( tr );
            var id =  detalleAsignacionesTelefonica.row(tr).data().id;
            if ( row.child.isShown() ) {
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                var retorna = getSeguimientos(id);                
                row.child( format(id,retorna) ).show();
                tr.addClass('shown');
            }
        }
    } );

});

document.getElementById("btnTipoPendienteTelefonica").onclick = function () 
{ 
    var arrayDatosCombo = [];
    arrayDatosCombo.push({descripcion:"TAREA", valor:"TAREA"});
    arrayDatosCombo.push({descripcion:"CASO", valor:"CASO"});
    llenarCombo("#ulDropDownTipoPendienteTelefonica",
                            "#textBtnTipoPendienteTelefonica",
                            "#txtIdBtnTipoPendienteTelefonica",
                            "txtBuscaPendientesTelefonica",
                            arrayDatosCombo
                            ); 
};

document.getElementById("btnEstadosTelefonica").onclick = function () 
{   
    var arrayDatosCombo = [];
    arrayDatosCombo.push({descripcion:"ABIERTO", valor:"ABIERTO"});
    arrayDatosCombo.push({descripcion:"CERRADO", valor:"CERRADO"});
    llenarCombo("#ulDropDownEstadosTelefonica",
                      "#textBtnEstadosTelefonica",
                      "#txtIdBtnEstadosTelefonica",
                      "txtBuscaEstadosPendientesTelefonica",
                      arrayDatosCombo);
};

document.getElementById("btnBuscarTelefonicaPorFecha").onclick = function () {
    var tipoPendiente  = document.getElementById('txtIdBtnTipoPendienteTelefonica').value;
    var estado         = document.getElementById('txtIdBtnEstadosTelefonica').value;
    var fechaInicio    = $('#reportrangeTelefonica').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaFin       = $('#reportrangeTelefonica').data('daterangepicker').endDate.format('DD/MM/YYYY');
    filtroDefaultTelefonica = false;
    buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "telefonica",filtroDefaultTelefonica);

};

document.getElementById("btnLimpiaBuscarTelefonicaPorFecha").onclick = function () 
{
    filtroDefaultTelefonica = true;
    $('#reportrangeTelefonica').data('daterangepicker').setStartDate(start);
    $('#reportrangeTelefonica').data('daterangepicker').setEndDate( end);
    cb(start, end);
    var tipoPendiente    = "";
    var estado           = "ABIERTO";
    $("#txtIdBtnTipoPendienteTelefonica").text("TODOS   ");
    $("#txtIdBtnTipoPendienteTelefonica").val("");
    $("#textBtnEstadosTelefonica").text(estado.toUpperCase()+"   ");
    $("#txtIdBtnEstadosTelefonica").val(estado.toUpperCase());

    buscarPendientes("", "", tipoPendiente, estado, "telefonica",filtroDefaultTelefonica);
};


document.getElementById("confirmaNotifInicial").onclick = function () 
{
    crearEnvioNotificacion(document.getElementById('txtIdAsigNotifInicial').value,"INICIAL");
};


document.getElementById("cancelaNotifInicial").onclick = function () 
{ 
    document.getElementById('txtIdAsigNotifInicial').value = null;
    $('#modalConfirmarNotificacionInicial').modal('hide');
};

document.getElementById("btnGrabarEnviarNotifFinal").onclick = function () 
{
    crearEnvioNotificacion(document.getElementById('txtIdAsigNotifFinal').value,"FINAL");
};

document.getElementById("btnEnviarListadoPendientesTelefonica").onclick = function () 
{
    mostrarConfirmacionEnviarListadoPendientes();
}

document.getElementById("btnGenerarReporteCsvTelefonica").onclick = function () 
{
    let csvContent = "data:text/csv;charset=utf-8," 
    + rowsData.map(e => e.join(",")).join("\n");
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "reporteDePendientesClienteTelefonica.csv");
    document.body.appendChild(link);
    link.click();
}

function mostrarConfirmacionEnviarNotificacionInicial(id)
{
    var mensajeAlerta = '<table width="500" align="center" cellspacing="3" cellpadding="3" border="0"><tr>'+
                        '<td><h2><span class="glyphicon glyphicon-warning-sign"></span></h2></td>'+
                        '<td><h4> Esta seguro(a) de enviar notificación inicial?</h4></td></tr></table>';
    $('#divMensajeConfirmarNotifInicial').html(mensajeAlerta);
    document.getElementById('txtIdAsigNotifInicial').value = id;
    $('#modalConfirmarNotificacionInicial').modal('show');
}


function mostrarEnviarNotificacionFinal(id)
{
    document.getElementById('txtIdPendienteTramo').value       = id;
    document.getElementById('txtIdAsigNotifFinal').value       = id;
    document.getElementById('txtaDetalleNotifFinal').value     = "";
    var currentTime = moment();

    document.getElementById("txtFechaNotificacionFinal").value = moment(currentTime).format('DD')+"/"+
                                        moment(currentTime).format('MM')+"/"+
                                        moment(currentTime).format('YYYY')+"  "+
                                        moment(currentTime).format("HH:mm");
    $('#modalEnviarNotifFinal').modal('show');
    $('#alertaValidaIngresarNotifFinal').hide();
}

function crearEnvioNotificacion(idAsignacion,tipoNotificacion)
{
console.log(document.getElementById('txtFechaNotificacionFinal').value);
    var validadoNotifFinal = true;
    var btnLoading         = '#btnLoadingNotifInicial';
    var alerta             = '#alertaConfirmaNotifInicial';
    var btnConfirmar       = '#confirmaNotifInicial';
    var modal              = '#modalConfirmarNotificacionInicial';

    if (tipoNotificacion == 'FINAL')
    {
        var fechaNotifFinal   = document.getElementById('txtFechaNotificacionFinal').value;
        var detalleNotifFinal = document.getElementById('txtaDetalleNotifFinal').value;
        btnLoading            = '#btnLoadingEnviarNotifFinal';
        alerta                = '#alertaValidaIngresarNotifFinal';
        btnConfirmar          = '#btnGrabarEnviarNotifFinal';
        modal                 = '#modalEnviarNotifFinal';

        if (!detalleNotifFinal || detalleNotifFinal.length === 0 || fechaNotifFinal == '' || fechaNotifFinal.length == 0)
        {
            validadoNotifFinal = false;
        }
    }

    if (idAsignacion !== null &&  validadoNotifFinal)
    {
        var parametros = {
            "idAsignacion" : idAsignacion,
            "tipoNotificacion" : tipoNotificacion,
            "fechaNotifFinal" : fechaNotifFinal,
            "detalleNotifFinal" : detalleNotifFinal,
            "strEstado" : 'Cerrado',
            "strTipo"   : 'estado'
        };
        $.ajax({
                data :  parametros,
                url  :  url_crear_envio_Notificacion,
                type :  'post',
                beforeSend: function () {
                        $(btnLoading).show();
                        $(btnConfirmar).hide();
                },
                success:  function (response) {

                        if(response.status == 200)
                        {
                            configuraMensajeIngresoConExito(
                                alerta,
                                '<strong>Se envio notificación con éxito!</strong>',
                                btnLoading,
                                btnConfirmar
                            );

                            //se cierra ventana luego de 2 segundos
                             setTimeout(function() {
                                 $(modal).modal('hide');
                                 $(alerta).hide();
                             }, 2000);
                            
                            document.getElementById("lnTelefonica").onclick();

                            document.getElementById('txtIdAsigNotifInicial').value = null;
                        }
                        else
                        {
                            configuraMensajeIngresoFallido(alerta,
                                                           '<strong>'+response+'</strong>',
                                                           btnLoading,
                                                           btnConfirmar);
                        }
                },
                failure: function(response){
                    configuraMensajeIngresoFallido(alerta,
                                                   '<strong>'+response+'</strong>',
                                                   btnLoading,
                                                   btnConfirmar);
                }
        });
    }
    else
    {
        configuraMensajeIngresoFallido(alerta,
        '<strong>'+'Debe ingresar detalle, fecha y hora para enviar la notificación final'+'</strong>',
        btnLoading,
        btnConfirmar);   
    }
}