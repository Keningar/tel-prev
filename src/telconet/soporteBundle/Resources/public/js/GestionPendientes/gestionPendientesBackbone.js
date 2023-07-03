    
$(document).ready(function() {
    
    mostrarLeyendas();

    //Define los detalles del cuadro de asignaciones
    detalleAsignaciones =   $('#detalleAsignaciones').DataTable( {
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

                           //Cerrar asignación
                           if(full.estado_pendiente !== null && full.estado_pendiente != 'Cerrado') 
                           {

                               if ( full.estado == 'Cerrado' || full.estado == 'Finalizada' || full.estado == 'Cancelada')
                               {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                            ' hint--rounded" aria-label="Cerrar Pendiente">'+
                                                            '<button type="button" class="btn btn-default btn-md" '+
                                                            '   onClick="javascript:mostrarConfirmacionCerrarPendiente(\''+full.id_asignacion+'\');">'+
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
            { "width": "10", "targets": 0 },
            { "width": "10", "targets": 1 },
            { "width": "10", "targets": 2 },
            { "width": "80", "targets": 3 },
            { "width": "80", "targets": 4 },
            { "width": "200", "targets": 5 },
            { "width": "200", "targets": 6 },
            { "width": "90", "targets": 7 },
            { "width": "120", "targets": 8 }
        ],
        "paging":true
    } )
    .on('preXhr.dt', function ( e, settings, data ) {
               $("#divLoaderDetalleAsignaciones").show();
        } )
    .on('xhr.dt', function ( e, settings, json, xhr ) {
        rowsData = [
            ["ITEM", "TIPO","TICKET","FECHA INICIO","FECHA FIN","VERSIÓN INICIAL (OBSERVACIÓN)","ULT. SEGUIMIENTO","ESTADO"]
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
                    json.data[indice].fecha,json.data[indice].fechaFin,
                    versionInicial,ult_seguimiento,json.data[indice].estado]);
            }
        }
       $("#divLoaderDetalleAsignaciones").hide();
    } );

    $('#detalleAsignaciones tbody').on('click', 'td', function () {
        var td = $(this).closest('td');
        if(td.prevObject[0].cellIndex===0 && td.prevObject[0].className==='details-control sorting_1')
        {
            var tr = $(this).closest('tr');
            var row = detalleAsignaciones.row( tr );
            var id =  detalleAsignaciones.row(tr).data().id;
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

    detalleSeguimientos =   $('#detalleSeguimientos').DataTable( {
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
             "title":"Tipo"
            },
            {"data": "observacion",
             "title":"Observación"
            },
            {"data": "empleado",
             "title":"Ejecutando"
            },    
            {"data": "departamento",
             "title":"Departamento"
            },
            {"data": "fecha",
             "title": "Fecha"
            }
        ],
        "columnDefs":
        [
            {"className": "dt-center verticalAlignCellDt columnas-list", "targets": "_all"},
            { "width": "10", "targets": 0 },
            { "width": "40", "targets": 1 },
            { "width": "600", "targets": 2 },
            { "width": "40", "targets": 3 },
            { "width": "40", "targets": 4 },
            { "width": "80", "targets": 5 }
        ],
        "paging":true,
        "searching":false
    } )
    .on('preXhr.dt', function ( e, settings, data ) {
               $("#divLoaderDetalleSeguimientos").show();
        } )
    .on('xhr.dt', function ( e, settings, json, xhr ) {
       $("#divLoaderDetalleSeguimientos").hide();
    } );


});

document.getElementById("btnEnviarListadoPendientesBackbone").onclick = function () 
{
    mostrarConfirmacionEnviarListadoPendientes();
}

document.getElementById("btnGenerarReporteCsvBackbone").onclick = function () 
{
    let csvContent = "data:text/csv;charset=utf-8," 
    + rowsData.map(e => e.join(",")).join("\n");
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "reporteDePendientesClienteBackbone.csv");
    document.body.appendChild(link);
    link.click();
}


function mostrarLeyendas()
{
    colorEstadoOtros        = '#cfd8dc';
    colorEstadoFinalizada   = '#4db6ac';
    colorEstadoAsignada     = '#9fa8da';
    colorEstadoReprogramada = '#bcaaa4';

    $("#divLeyendas").html(

        '<div class="row" style="margin-top:10px;">'+
        '<div class="col-lg-12">'+
            '<table>'+
                '<tr>'+
                    '<td style="font-size: 10px;font-weight:bold">Estados:</td>'+
                    '<td>&nbsp;&nbsp;&nbsp;</td>'+
                    '<td style="font-size: 9px;width:60px;text-align:right;">Finalizada</td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="background-color:#4db6ac;width:30px;">&nbsp;&nbsp;</td>'+
                    '<td style="width:15px;">&nbsp;</td>'+
                    '<td style="font-size: 9px;width:60px;text-align:right;"> Asignada(o) </td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="background-color:#9fa8da;width:30px;"> &nbsp;&nbsp;</td>'+
                    '<td style="width:15px;">&nbsp;</td>'+
                    '<td style="font-size: 9px;width:60px;text-align:right;">Reprogramada</td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="background-color:#bcaaa4;width:30px;"> &nbsp;&nbsp;</td>'+
                    '<td style="width:15px;">&nbsp;</td>'+
                    '<td style="font-size: 9px;width:60px;text-align:right;">Otros</td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="background-color:#cfd8dc;width:30px;"> &nbsp;&nbsp;</td>'+
                '</tr>'+
                '<tr>'+
                '<td>&nbsp;</td>'+
                '</tr>'+
                '<tr>'+
                    '<td style="font-size: 10px;font-weight:bold">Tipos:</td>'+
                    '<td>&nbsp;&nbsp;&nbsp;</td>'+
                    '<td style="font-size: 9px;width:60px;text-align:right;">Tarea</td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="background-color:#bef67a;width:30px;">&nbsp;&nbsp;</td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="font-size: 9px;width:60px;text-align:right;"> Caso </td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="background-color:#fff263;width:30px;"> &nbsp;&nbsp;</td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="font-size: 9px;font-weight: bold;width:60px;"></td>'+
                    '<td>&nbsp;</td>'+
                    '<td style=";width:30px;"> &nbsp;&nbsp;</td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="font-size: 9px;font-weight: bold;width:60px;"></td>'+
                    '<td>&nbsp;</td>'+
                    '<td style="width:30px;"> &nbsp;&nbsp;</td>'+
                '</tr>'+
                '<tr>'+
                '<td>&nbsp;</td>'+
                '</tr>'+
            '</table>'+
        '</div>'+
    '</div>' 

    );


    $("#divLeyendasInfoPendiente").html(

        '<div class="row" style="margin-top:10px;">'+
        '<div class="col-lg-12">'+
        '<table>'+
        '<tr>'+
        '<td style="font-size: 10px;font-weight:bold">Estados:</td>'+
        '<td>&nbsp;&nbsp;&nbsp;</td>'+
        '<td style="font-size: 9px;width:60px;text-align:right;">Finalizada</td>'+
        '<td>&nbsp;</td>'+
        '<td style="background-color:'+colorEstadoFinalizada+';width:30px;">&nbsp;&nbsp;</td>'+
        '<td style="width:15px;">&nbsp;</td>'+
        '<td style="font-size: 9px;width:60px;text-align:right;"> Asignada(o) </td>'+
        '<td>&nbsp;</td>'+
        '<td style="background-color:'+colorEstadoAsignada+';width:30px;"> &nbsp;&nbsp;</td>'+
        '<td style="width:15px;">&nbsp;</td>'+
        '<td style="font-size: 9px;width:60px;text-align:right;">Reprogramada</td>'+
        '<td>&nbsp;</td>'+
        '<td style="background-color:'+colorEstadoReprogramada+';width:30px;"> &nbsp;&nbsp;</td>'+
        '<td style="width:15px;">&nbsp;</td>'+
        '<td style="font-size: 9px;width:60px;text-align:right;">Otros</td>'+
        '<td>&nbsp;</td>'+
        '<td style="background-color:'+colorEstadoOtros+';width:30px;"> &nbsp;&nbsp;</td>'+
        '</tr>'+
        '<tr>'+
        '<td>&nbsp;</td>'+
        '</tr>'+
        '</table>'+
        '</div>'+
        '</div> '
    );  
}