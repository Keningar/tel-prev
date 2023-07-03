$(document).ready(function() {
    
    mostrarLeyendas();
    
    //Define los detalles del cuadro de asignaciones
    detalleAsignacionesMunicipio =   $('#detalleAsignacionesMunicipio').DataTable( {
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
            {"data": "login_afectado",
             "title":"Login Afectado (Versión Inicial)",
             "render":function(data, type, full, meta)
             {
               var strDatoRetorna = full.login_afectado;
                 if (full.tipo == 'CASO' && full.tipo_problema.toLowerCase() === 'backbone' )
                 {
                   strDatoRetorna = full.versionInicial;
                 }
                 return strDatoRetorna;
             }
            },
            {"data": "fecha",
            "title":"Fecha Inicio"
           },
            {"data": "fechaFin",
             "title": "Fecha Fin"
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
                           if(full.estado_pendiente !== null && full.estado_pendiente != 'Cerrado' && full.estado_pendiente != 'ProblemaDeAcceso') 
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
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                        ' hint--rounded" aria-label="Marcar como Problemas de acceso">'+
                                                        '<button type="button" class="btn btn-default btn-md" '+
                                                        '   onClick="javascript:mostrarConfirmacionProblemasDeAcceso(\''+full.id_asignacion+'\');">'+
                                                        '    <span class="glyphicon glyphicon-warning-sign"></span>'+
                                                        '</button>'+
                                                    '</span>';

                               }
                           }
                           else
                           {
                                if ( full.estado_pendiente == 'Cerrado') 
                                {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                            ' hint--rounded" aria-label="Pendiente Cerrado">'+
                                                            '<button type="button" class="btn btn-default btn-md" '+
                                                            '     onClick="">'+
                                                            '  <img src=\"/public/images/images_crud/lock_close.png\" width=\"18\" height=\"18\" />'+
                                                            '</button>'+
                                                        '</span>';
                                }
                                if ( full.estado_pendiente == 'ProblemaDeAcceso') 
                                {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                            ' hint--rounded" aria-label="Pendiente con Problemas de acceso">'+
                                                            '<button type="button" class="btn btn-default btn-md" '+
                                                            '     onClick="">'+
                                                            '    <span class="glyphicon glyphicon-warning-sign" style="color:#f44336;"></span>'+
                                                            '</button>'+
                                                        '</span>';
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                        ' hint--rounded" aria-label="Cerrar Pendiente">'+
                                                        '<button type="button" class="btn btn-default btn-md" '+
                                                        '   onClick="javascript:mostrarConfirmacionCerrarPendiente(\''+full.id_asignacion+'\');">'+
                                                        '    <img src=\"/public/images/images_crud/lock_open.png\" width=\"18\" height=\"18\" />'+
                                                        '</button>'+
                                                    '</span>';
                                }

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
            { "width": "10", "targets": 3 },
            { "width": "70", "targets": 4 },
            { "width": "70", "targets": 5 },
            { "width": "200", "targets": 6 },
            { "width": "100", "targets": 7 },
            { "width": "100", "targets": 8 }
        ],
        "paging":true
    } )
    .on('preXhr.dt', function ( e, settings, data ) {
               $("#divLoaderDetalleAsignacionesMunicipio").show();
        } )
    .on('xhr.dt', function ( e, settings, json, xhr ) {
        rowsData = [
            ["ITEM", "TIPO","TICKET", "LOGIN AFECTADO (VERSIÓN INICIAL)","FECHA INICIO","FECHA FIN","ULT. SEGUIMIENTO","ESTADO"]
        ];
        if (json != null)
        { 
            for(var indice=0; indice < json.data.length; indice ++)
            {
                if(json.data[indice].id_asignacion == document.getElementById('txtIdAsignacion').value && $("#divInfoPendiente").is(":visible"))
                {
                    mostrarInformacionDelPendiente(json.data[indice]);
                }
                var login_afectado = json.data[indice].login_afectado;
                if (json.data[indice].tipo == 'CASO' && json.data[indice].tipo_problema.toLowerCase() === 'backbone' )
                {
                    login_afectado = json.data[indice].versionInicial;
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
                            login_afectado,json.data[indice].fecha,json.data[indice].fechaFin,
                            ult_seguimiento,json.data[indice].estado]);

            }
        }
       $("#divLoaderDetalleAsignacionesMunicipio").hide();
    } );

    $('#detalleAsignacionesMunicipio tbody').on('click', 'td', function () {
        var td = $(this).closest('td');
        if(td.prevObject[0].cellIndex===0 && td.prevObject[0].className==='details-control sorting_1')
        {
            var tr = $(this).closest('tr');
            var row = detalleAsignacionesMunicipio.row( tr );
            var id =  detalleAsignacionesMunicipio.row(tr).data().id;
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

document.getElementById("btnTipoPendienteMunicipio").onclick = function () 
{ 
    var arrayDatosCombo = [];
    arrayDatosCombo.push({descripcion:"TAREA", valor:"TAREA"});
    arrayDatosCombo.push({descripcion:"CASO", valor:"CASO"});
    llenarCombo("#ulDropDownTipoPendienteMunicipio",
                            "#textBtnTipoPendienteMunicipio",
                            "#txtIdBtnTipoPendienteMunicipio",
                            "txtBuscaPendientesMunicipio",
                            arrayDatosCombo
                            ); 
};

document.getElementById("btnEstadosMunicipio").onclick = function () 
{   
    var arrayDatosCombo = [];
    arrayDatosCombo.push({descripcion:"ABIERTO", valor:"ABIERTO"});
    arrayDatosCombo.push({descripcion:"CERRADO", valor:"CERRADO"});
    arrayDatosCombo.push({descripcion:"PROBLEMAS DE ACCESO", valor:"PROBLEMAS_DE_ACCESO"});
    llenarCombo("#ulDropDownEstadosMunicipio",
                      "#textBtnEstadosMunicipio",
                      "#txtIdBtnEstadosMunicipio",
                      "txtBuscaEstadosPendientesMunicipio",
                      arrayDatosCombo);
};

document.getElementById("btnBuscarPorFechaMunicipio").onclick = function () {
    var tipoPendiente      = document.getElementById('txtIdBtnTipoPendienteMunicipio').value;
    var estado             = document.getElementById('txtIdBtnEstadosMunicipio').value;
    var fechaInicio        = $('#reportrangeMunicipio').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaFin           = $('#reportrangeMunicipio').data('daterangepicker').endDate.format('DD/MM/YYYY');
    filtroDefaultMunicipio = false;
    buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "municipio",filtroDefaultMunicipio);

};

document.getElementById("btnLimpiaBuscarPorFechaMunicipio").onclick = function () 
{
    filtroDefaultMunicipio = true;
    $('#reportrangeMunicipio').data('daterangepicker').setStartDate(start);
    $('#reportrangeMunicipio').data('daterangepicker').setEndDate( end);
    cb(start, end);
    var tipoPendiente    = "";
    var estado           = "ABIERTO";
    $("#textBtnTipoPendienteMunicipio").text("TODOS   ");
    $("#txtIdBtnTipoPendienteMunicipio").val("");
    $("#textBtnEstadosMunicipio").text(estado.toUpperCase()+"   ");
    $("#txtIdBtnEstadosMunicipio").val(estado.toUpperCase());

    buscarPendientes("", "", tipoPendiente, estado, "municipio",filtroDefaultMunicipio);
};


document.getElementById("btnGenerarReporteCsvMunicipio").onclick = function () 
{
    let csvContent = "data:text/csv;charset=utf-8," 
    + rowsData.map(e => e.join(",")).join("\n");
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "reporteDePendientesClienteMunicipio.csv");
    document.body.appendChild(link);
    link.click();
}



function mostrarConfirmacionProblemasDeAcceso(id)
{
    var mensajeAlerta = '<table width="500" align="center" cellspacing="3" cellpadding="3" border="0"><tr>'+
                        '<td><h2><span class="glyphicon glyphicon-warning-sign"></span></h2></td>'+
                        '<td><h4> Esta seguro(a) de marcar como problemas de acceso el pendiente? </h4></td></tr></table>';
    $('#divMensajeConfirmarProblemasDeAcceso').html(mensajeAlerta);
    document.getElementById('txtIdPendienteCerrar').value = id;
    $('#modalConfirmarProblemasDeAcceso').modal('show');
}


document.getElementById("confirmaProblemasDeAcceso").onclick = function () 
{
    modificarEstadoDePendiente("ProblemaDeAcceso");
};


document.getElementById("cancelaProblemasDeAcceso").onclick = function () 
{
    document.getElementById('txtIdPendienteProblemasDeAcceso').value = null;
    $('#modalConfirmarProblemasDeAcceso').modal('hide');
};

document.getElementById("btnEnviarReporteLaboresMunicipio").onclick = function () 
{
    mostrarEnviarReporteLaboresDiarias();
}


document.getElementById("btnCerrarReporteLaboresDiarias").onclick = function () 
{
    $('#modalEnviarNotifFinal').modal('hide');
};


document.getElementById("btnGrabarReporteLaboresDiarias").onclick = function () 
{
    crearEnvioReporteLaboresDiarias();
};


function mostrarEnviarReporteLaboresDiarias()
{
    llenarComboTurnos("cmbTurno","");
    $('#modalReporteLaboresDiarias').modal('show');
    $('#alertaReporteLaboresDiarias').hide();
}


function llenarComboTurnos(nombreCombo,item)
{
    var parametros = {
        descParametro : "TURNOS_NOC_PARA_MODULO_GESTION_PENDIENTES"
    };
    $.ajax({
            data :  parametros,
            url  :  url_obtener_parametros,
            type :  'post',
            success:  function (response) {
                var arrItems  = response.data;
                var items     = "<option>Seleccione...</option>"; 
                for(var i=0;i<arrItems.length;i++)
                {
                    items += "<option value='"+arrItems[i].valor2+"'>"+arrItems[i].valor1+"</option>"; 
                }
                $("#"+nombreCombo).html(items);
                if(item!=="")
                {
                    document.getElementById(nombreCombo).value = item;
                }
            },
            failure: function(response){
                    console.log("failure");
            }
    });
}


function crearEnvioReporteLaboresDiarias()
{
    var btnLoading         = '#btnLoadingGrabarReporteLaboresDiarias';
    var alerta             = '#alertaReporteLaboresDiarias';
    var btnConfirmar       = '#btnGrabarReporteLaboresDiarias';
    var modal              = '#modalReporteLaboresDiarias';
    var turno              = document.getElementById('cmbTurno').value;

    if ( turno !== null && turno !== ""  && turno !== "Seleccione...")
    {
        var parametros = {
            "turno" : turno
        };
        $.ajax({
                data :  parametros,
                url  :  url_enviar_rep_lab_diarias,
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
                                '<strong>'+response.mensaje+'</strong>',
                                btnLoading,
                                btnConfirmar
                            );

                            //se cierra ventana luego de 2 segundos
                             setTimeout(function() {
                                 $(modal).modal('hide');
                                 $(alerta).hide();
                             }, 2000);
                        }
                        else
                        {
                            configuraMensajeIngresoFallido(alerta,
                                                           '<strong>'+response.mensaje+'</strong>',
                                                           btnLoading,
                                                           btnConfirmar);
                        }
                },
                failure: function(response){
                    configuraMensajeIngresoFallido(alerta,
                                                   '<strong>'+response.mensaje+'</strong>',
                                                   btnLoading,
                                                   btnConfirmar);
                }
        });
    }
    else
    {
        configuraMensajeIngresoFallido(alerta,
        '<strong>'+'Debe seleccionar un turno'+'</strong>',
        btnLoading,
        btnConfirmar);   
    }
}