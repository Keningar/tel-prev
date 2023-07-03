$(document).ready(function() {


    detalleAsignacionesRecorridos =   $('#detalleAsignacionesRecorridos').DataTable( {
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
            {"data": "ticket",
            "title":"Tarea de Recorrido"
            }, 
            {"data": "fecha_ini_tarea",
             "title":"Inicio Recorrido"
            },
            {"data": "tramo",
             "title": "Tramo",
             "render":function(data, type, full, meta)
                      {
                          var colorTipo = "#cfd8dc;";
                          var strDatoRetorna = "";
                        if (full.tramo != null && full.tramo != "")
                        {

                          strDatoRetorna=
                                             "<div><b>"+full.tramo+
                                             "</b></div><div style=\"padding:0.5em;background-color:"+colorTipo+"\">Hilo Telefonica: "+
                                             full.hilo_telefonica+
                                             "</div>";
                        }
                        return strDatoRetorna;
                      }
            },
            {"data": "asig_tarea",
             "title":"Información Técnico",
             "render":function(data, type, full, meta)
                      {
                          var colorTipo = "#cfd8dc;";

                          var strDatoRetorna=
                                             "<div><b>"+full.asig_tarea+
                                             "</b></div><div style=\"padding:0.5em;background-color:"+colorTipo+"\">"+
                                             full.telf_asig_tarea+
                                             "</div>";
                          return strDatoRetorna;
                      }
            },
            {"data": "ult_seguimiento",
             "title":"Ult. Seguimiento",
             "render":function(data, type, full, meta)
                      {
                        var strDatoRetorna = "";

                            var arrayDeCadenas = full.ult_seguimiento.split("|");
                            for (var i=0; i < arrayDeCadenas.length; i++) 
                            {
                                var arrayTarea = arrayDeCadenas[i].split("**");
                                for (var x=0; x < arrayTarea.length; x++) 
                                {
                                    var strTexto = arrayTarea[x];
                                    if (strTexto != 'Tarea:')
                                    {
                                        if (x == 0)
                                        {
                                            strTexto = "<strong>"+strTexto+"</strong>";
                                        }
                                        strTexto = strTexto.replace('UltSeg:','');

                                        strDatoRetorna = strDatoRetorna + strTexto+"<br>";
                                    }
                                }
                                strDatoRetorna = strDatoRetorna+"<br>";
                            }

                          return strDatoRetorna;
                      }
            },
            {"data": "fecha_fin_tarea",
             "title": "Fin o Actualización Recorrido"
            },
            {"data": "tarea_informe_id",
             "title":"Tarea de Informe"
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
                                                   '        onClick=\'javascript:mostrarInformacionDelPendienteRecorrido('+JSON.stringify(full)+');\'>'+
                                                   '    <span class="glyphicon glyphicon-search"></span>'+
                                                   '</button>'+
                                                   '</span>';
                            if (full.tarea_informe_id == null || full.tarea_informe_id == "")
                            {
                                strDatoRetorna    +=     '<span class="hint--bottom-right hint--default hint--medium'+
                                                    ' hint--rounded" aria-label="Ingresar Tramo">'+
                                                    '<button type="button" class="btn btn-default btn btn-md" '+
                                                    '   onClick="javascript:mostrarIngresarTramo(\''+full.id_asignacion+'\',\''+'\');">'+
                                                    '    <span class="glyphicon glyphicon-edit"></span>'+
                                                    '</button>'+
                                                    '</span>';

                                if (full.tramo != null && full.tramo != "")
                                {
                                    strDatoRetorna    +=     '<span class="hint--bottom-right hint--default hint--medium'+
                                                        ' hint--rounded" aria-label="Ingresar Tarea Informe">'+
                                                        '<button type="button" class="btn btn-default btn btn-md" '+
                                                        '   onClick="javascript:mostrarIngresarTareaInforme('+
                                                        full.id_asignacion+','+full.referencia_id+',\''+full.tramo+'\');">'+
                                                        '    <span class="glyphicon glyphicon-cog"></span>'+
                                                        '</button>'+
                                                        '</span>';
                                }
                            }
                           //Cerrar asignación
                           if(full.estado_pendiente !== null && full.estado_pendiente != 'Cerrado') 
                           {
                                if (full.estado == 'Finalizada' || full.estado == 'Cancelada')
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
            { "width": "150", "targets": 3 },
            { "width": "140", "targets": 4 },
            { "width": "190", "targets": 5 },
            { "width": "80", "targets": 6 },
            { "width": "70", "targets": 7 },
            { "width": "160", "targets": 8 }
        ],
        "paging":true
    } )
    .on('preXhr.dt', function ( e, settings, data ) {
               $("#divLoaderDetalleAsignacionesRecorridos").show();
        } )
    .on('xhr.dt', function ( e, settings, json, xhr ) {

        for(var indice=0; indice < json.data.length; indice ++)
        {
            if(json.data[indice].id_asignacion == document.getElementById('txtIdAsignacion').value && $("#divInfoPendiente").is(":visible"))
            {
                mostrarInformacionDelPendienteRecorrido(json.data[indice]);
            }
        }

       $("#divLoaderDetalleAsignacionesRecorridos").hide();
    } );



});

document.getElementById("btnNuevoTramo").onclick = function () { 
    $('#divCmbTramoIngresarTramo').hide();
    $('#divTxtTramoIngresarTramo').show();
    document.getElementById('txtTramo').value = "";
};

document.getElementById("btnCancelarNuevoTramo").onclick = function () { 
    $('#divCmbTramoIngresarTramo').show();
    $('#divTxtTramoIngresarTramo').hide();
    $("#divTxtTramoIngresarTramo").removeClass('has-success');
    $("#divTxtTramoIngresarTramo").removeClass('has-error');
    $("#divTxtTramoIngresarTramo").removeClass('has-warning');
    $('#alertaValidaAgregarTramo').hide();
    document.getElementById('txtTramo').value = "";
};

document.getElementById("btnGrabarNuevoTramo").onclick = function () { 
    grabarNuevoTramo("TRAMOS_PARA_MODULO_GESTION_PENDIENTES",document.getElementById('txtTramo').value);
};
document.getElementById("btnNuevoCircuito").onclick = function () { 
    $('#divCmbCircuito').hide();
    $('#divTxtIngresarNuevoCircuito').show();
    document.getElementById('txtCircuito').value = "";
};

document.getElementById("btnCancelarNuevoCircuito").onclick = function () { 
    $('#divCmbCircuito').show();
    $('#divTxtIngresarNuevoCircuito').hide();
    $("#divTxtIngresarNuevoCircuito").removeClass('has-success');
    $("#divTxtIngresarNuevoCircuito").removeClass('has-error');
    $("#divTxtIngresarNuevoCircuito").removeClass('has-warning');
    $('#alertaValidaAgregarTramo').hide();
    document.getElementById('txtCircuito').value = "";
};

document.getElementById("btnGrabarNuevoCircuito").onclick = function () { 
    grabarNuevoCircuito("CIRCUITOS_PARA_MODULO_GESTION_PENDIENTES",document.getElementById('txtCircuito').value);
};
document.getElementById("btnGrabarTramo").onclick = function () { 
    grabarTramo();
};
document.getElementById("confirmaCrearTareaInforme").onclick = function () { 
    if(document.getElementById('cmbCity').value === '' || document.getElementById('cmbDepartment').value === ''
        || document.getElementById('cmbEncargado').value === '')
    {
        configuraMensajeIngresoFallido('#alertaConfirmaCrearTareaInforme',
                            '<strong>Debe ingresar la ciudad, departamento y jefe</strong>',
                            '#btnLoadingGrabarTareaInforme',
                            '#confirmaCrearTareaInforme');
        setTimeout(function() {
            $('#alertaConfirmaCrearTareaInforme').hide();
        }, 2000);
        return false;
    }

    if(document.getElementById('txtFlagCrearTarea').value == '')
    {   document.getElementById('txtFlagCrearTarea').value = 1;
        grabarTareaInforme();
    }else
    {
        return false;
    }
};

document.getElementById("btnEstadosRecorrido").onclick = function () 
{   
    var arrayDatosCombo = [];
    arrayDatosCombo.push({descripcion:"ABIERTO", valor:"ABIERTO"});
    arrayDatosCombo.push({descripcion:"CERRADO", valor:"CERRADO"});
    llenarCombo("#ulDropDownEstadosRecorrido",
                      "#textBtnEstadosRecorrido",
                      "#txtIdBtnEstadosRecorrido",
                      "txtBuscaEstadosPendientesRecorrido",
                      arrayDatosCombo);
};
document.getElementById("btnBuscarRecorridoPorFecha").onclick = function () {
    var tipoPendiente  = "TAREA";
    var estado         = document.getElementById('txtIdBtnEstadosRecorrido').value;
    var fechaInicio    = $('#reportrangeRecorrido').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaFin       = $('#reportrangeRecorrido').data('daterangepicker').endDate.format('DD/MM/YYYY');
    filtroDefaultRecorridos  = false;
    buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "recorridos",filtroDefaultRecorridos);

};

document.getElementById("btnLimpiaBuscarRecorridoPorFecha").onclick = function () 
{
    filtroDefaultRecorridos = true;
    var tipoPendiente = "";
    var estado        = "ABIERTO";
    $('#reportrangeRecorrido').data('daterangepicker').setStartDate(start);
    $('#reportrangeRecorrido').data('daterangepicker').setEndDate( end);
    cb(start, end);
    $("#textBtnEstadosRecorrido").text(estado.toUpperCase()+"   ");
    $("#txtIdBtnEstadosRecorrido").val(estado.toUpperCase());

    buscarPendientes("", "", tipoPendiente, estado, "recorridos",filtroDefaultRecorridos);
};

$('#txtCiudad').change(function() {
    $( "#txtDepartamento" ).val("");
    $( "#cmbDepartamento" ).val(""); 
   $( "#txtDepartamento" ).prop( "disabled", false );
   llenarComboDepartamento("cmbDepartamento","",document.getElementById('cmbCiudad').value,idEmpresa);
});

$('#txtCity').change(function() {
    $( "#txtDepartment" ).val("");
    $( "#cmbDepartment" ).val(""); 
   $( "#txtDepartment" ).prop( "disabled", false );
   llenarComboDepartamento("cmbDepartment","",document.getElementById('cmbCity').value,idEmpresa,'S');
});

$('#txtDepartment').change(function() {
    $( "#txtEncargado" ).val("");
    $( "#cmbEncargado" ).val(""); 
   $( "#txtEncargado" ).prop( "disabled", false );
   llenarComboEncargado("cmbEncargado","",document.getElementById('cmbCity').value,document.getElementById('cmbDepartment').value);
});

$('#txtCircuitoSelect').keydown(function() {
    $( "#cmbCircuito" ).val("");
});
$('#txtCircuitoSelect').focusout(function() {
    if($('#cmbCircuito').val() == "")
    {
        $( "#txtCircuitoSelect" ).val("");
    }
});

$('#txtTramoSelect').keydown(function() {
    $( "#cmbTramo" ).val("");
});
$('#txtTramoSelect').focusout(function() {
    if($('#cmbTramo').val() == "")
    {
        $( "#txtTramoSelect" ).val("");
    }
});

$('#txtCiudad').keydown(function() {
    $( "#cmbCiudad" ).val("");
});
$('#txtCiudad').focusout(function() {
    if($('#cmbCiudad').val() == "")
    {
        $( "#txtCiudad" ).val("");
        $("#txtDepartamento").attr('disabled','disabled');
    }
});

$('#txtDepartamento').keydown(function() {
    $( "#cmbDepartamento" ).val("");
});
$('#txtDepartamento').focusout(function() {
    if($('#cmbDepartamento').val() == "")
    {
        $( "#txtDepartamento" ).val("");
    }
});

function mostrarInformacionDelPendienteRecorrido(data)
{
    detalleSeguimientos.clear().draw();
    $("#divInfoTareaCaso").html("");
    $("#divTareasCaso").html("");
    mostrarDivInformacionDelPendiente();
    document.getElementById('txtReferenciaId').value     = data.referencia_id;
    document.getElementById('txtTareaInformeId').value   = data.tarea_informe_id;
    document.getElementById('txtIdAsignacion').value     = data.id_asignacion;
    document.getElementById('txtEstado').value           = data.estado;
    document.getElementById('txtEstadoPendiente').value  = data.estado_pendiente;
    document.getElementById('txtTipo').value             = data.tipo;
    document.getElementById('txtIdTarea').value          = data.referencia_id;
    var tareasRecorrido = '';
    var dataTareaRecorrido = "";
    var dataTareaInforme   = "";

    mostrarLeyendas();

    if (document.getElementById('txtReferenciaId').value != null && document.getElementById('txtReferenciaId').value != "" )
    {
        $.ajax(
        {
            data :  { "idTarea" : document.getElementById('txtReferenciaId').value },
            url  :  url_obtener_datos_tarea,
            type :  'get',
            success:  function (response) {
                var color = "#cfd8dc;";

                if(response.data[0].estado == 'Cerrado' || response.data[0].estado == 'Finalizada')
                {
                  color = "#4db6ac;";
                }
                else if(response.data[0].estado == 'Asignado' || response.data[0].estado == 'Asignada')
                {
                  color = "#9fa8da;";
                }
                else if(response.data[0].estado == 'Reprogramada')
                {
                  color = "#bcaaa4;";
                }
                letraEstadoTarea = response.data[0].estado.substring(0, 1);
                var obj = { 
                    ticket: document.getElementById('txtReferenciaId').value,
                    detalle_id:response.data[0].detalle_id,
                    estado: response.data[0].estado,
                    fecha: response.data[0].fecha,
                    tipo_problema: response.data[0].tarea,
                    observacion: response.data[0].observacion,
                    tipo: 'TAREA',
                    liActive: 'liTareaRecorrido'+document.getElementById('txtReferenciaId').value,
                    liRemoveActive: 'liTareaRecorrido'+document.getElementById('txtTareaInformeId').value
                 };
                 dataTareaRecorrido = JSON.stringify(obj);
                 tareasRecorrido = tareasRecorrido + '<li class="nav-item active" id="liTareaRecorrido'+obj.ticket+'">'+
                 '<a  href=\'javascript:mostrarInformacionTareasRecorrido('+
                 dataTareaRecorrido+');\' id=\'lnTareaRecorrido'+obj.ticket+'\' >Tarea Recorrido'+
                 ' <span class="badge" style="background-color:'+color+'">'+letraEstadoTarea+'</span> '+'</a> '+
                 '</li>';
                 $("#divTareasRecorrido").html(
                    '<ul class="nav nav-tabs">'+
                    tareasRecorrido + 
                    '</ul>'
                );

                mostrarInformacionTareasRecorrido(obj);


                if (document.getElementById('txtTareaInformeId').value != null && document.getElementById('txtTareaInformeId').value != "" )
                {
                    $.ajax(
                    {
                        data :  { "idTarea" : document.getElementById('txtTareaInformeId').value },
                        url  :  url_obtener_datos_tarea,
                        type :  'get',
                        success:  function (response) {
                            var color = "#cfd8dc;";

                            if(response.data[0].estado == 'Cerrado' || response.data[0].estado == 'Finalizada')
                            {
                              color = "#4db6ac;";
                            }
                            else if(response.data[0].estado == 'Asignado' || response.data[0].estado == 'Asignada')
                            {
                              color = "#9fa8da;";
                            }
                            else if(response.data[0].estado == 'Reprogramada')
                            {
                              color = "#bcaaa4;";
                            }
                            letraEstadoTarea = response.data[0].estado.substring(0, 1);
                            var obj = { 
                                ticket: document.getElementById('txtTareaInformeId').value,
                                detalle_id:response.data[0].detalle_id,
                                estado: response.data[0].estado,
                                fecha: response.data[0].fecha,
                                tipo_problema: response.data[0].tarea,
                                observacion: response.data[0].observacion,
                                tipo: 'TAREA',
                                liActive: 'liTareaRecorrido'+document.getElementById('txtTareaInformeId').value,
                                liRemoveActive: 'liTareaRecorrido'+document.getElementById('txtReferenciaId').value
                             };
                             dataTareaInforme = JSON.stringify(obj);
                             tareasRecorrido = tareasRecorrido + '<li class="nav-item" id="liTareaRecorrido'+obj.ticket+'">'+
                             '<a  href=\'javascript:mostrarInformacionTareasRecorrido('+
                             dataTareaInforme+');\' id=\'lnTareaRecorrido'+obj.ticket+'\' >Tarea Informe'+
                             ' <span class="badge" style="background-color:'+color+'">'+letraEstadoTarea+'</span> '+'</a> '+
                             '</li>';
                             $("#divTareasRecorrido").html(
                                '<ul class="nav nav-tabs">'+
                                tareasRecorrido + 
                                '</ul>'
                            );
                        }
                    });
                }

            }
        });
    }
}

function mostrarInformacionTareasRecorrido(data)
{
    var tareaInicial = "";

    detalleSeguimientos.ajax.url(url_seguimientos+"?idTarea="+data.ticket+"&referenciaId="+data.ticket+"&procedencia="+procedencia).load();

    if ( (data.tipo == 'CASO' && data.estado != 'Cerrado' ) ||
         (data.tipo == 'TAREA' && data.estado != 'Finalizada' ) )
    {
        $('#btnNuevoSeguimiento').show();
        $('#btnNuevoSeguimientoInterno').hide();
    }
    else
    {
        $('#btnNuevoSeguimiento').hide();
        $('#btnNuevoSeguimientoInterno').show();
    }

    var strDivColor          = "#cfd8dc;";

    if(data.estado == 'Cerrado' || data.estado == 'Finalizada')
    {
        strDivColor = "#4db6ac;";
    }
    else if(data.estado == 'Asignado' || data.estado == 'Asignada')
    {
        strDivColor = "#9fa8da;";
    }
    else if(data.estado == 'Reprogramada')
    {
        strDivColor = "#bcaaa4;";
    }
    else if (data.estado == 'Cerrar Caso')
    {
        strDivColor = "#ff867a;";
    }
    var strDivEstado = "<div style=\"padding:0.2em;width:90px;background-color:"+strDivColor+"\">"+data.estado+"</div>";

    document.getElementById('txtIdTarea').value   = data.ticket;
    document.getElementById("txtIdDetalle").value = data.detalle_id;

    $("#divCabeceraInfoDetallada").html(
        '<div class="row"">'+
        '  <div class="col-lg-2 label-info-first"><b>Ticket: </b></div>'+
        '  <div class="col-lg-3 text-label-info-gen ticket-resaltado"> '+data.ticket+' </div>'+
        '  <div class="col-lg-2 label-info-gen"><b>Fecha: </b></div>'+
        '  <div class="col-lg-3 text-label-info-gen"> '+data.fecha+' </div>'+
        '</div>'+
        '<div class="row">'+
        '  <div class="col-lg-2 label-info-first"><b>Estado: </b></div>'+
        '  <div class="col-lg-3 text-label-info-gen"> '+strDivEstado+' </div>'+
        '  <div class="col-lg-2 label-info-first"><b>Tarea: </b></div>'+
        '  <div class="col-lg-3 text-label-info-gen"> '+data.tipo_problema+' </div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-lg-10 label-info-gen" style="margin-left:1.5em;width:95%" ><b>Observaci&oacute;n: </b></div>'+
        '</div>'+
        '<div class="row">'+
        '<div class="col-lg-10 text-label-info-gen" style="margin-left:1.5em;width:95%">'+data.observacion+'</div>'+
        '</div>'
    );

    $('#'+data.liActive).addClass('active');
    $('#'+data.liRemoveActive).removeClass('active');

    if (tareaInicial != '')
    {
        mostrarSeguimientos(tareaInicial,data.tareas );
    }
    //Si el pendiente esta cerrado deshabilita botón de nuevo seguimiento
    if (document.getElementById('txtEstadoPendiente').value == 'Cerrado')
    {
        $('#btnNuevoSeguimiento').attr('disabled','disabled');
        $('#btnNuevoSeguimientoInterno').attr('disabled','disabled');
    }
    else
    {
        $('#btnNuevoSeguimiento').removeAttr('disabled');
        $('#btnNuevoSeguimientoInterno').removeAttr('disabled');
    }
}



function mostrarIngresarTareaInforme(idAsignacion,tareaRecorrido, tramo)
{
    document.getElementById('txtIdAsignacion').value = idAsignacion;
    buscaValoresDefectoTareaInforme(tareaRecorrido,tramo);
    document.getElementById('txtFlagCrearTarea').value = ''
    $( "#txtCity" ).val("");$( "#cmbCity" ).val(""); 
    $( "#txtDepartment" ).val("");$( "#cmbDepartment" ).val(""); 
    $( "#txtEncargado" ).val("");$( "#cmbEncargado" ).val("");
    $('#btnLoadingGrabarTareaInforme').hide();
    $('#confirmaCrearTareaInforme').show(); 
    llenarComboCiudad("cmbCity","",'S');
    $('#modalConfirmarCrearTareaInforme').modal('show');
}
function buscaValoresDefectoTareaInforme(tareaRecorrido, tramo)
{
    var parametros = {
        descParametro : "VALORES_TAREA_INFORME_MODULO_GESTION_PENDIENTES"
    };
    $.ajax({
            data :  parametros,
            url  :  url_obtener_parametros,
            type :  'post',
            success:  function (response) {
                var formaContacto = response.data[0].valor1;
                var arrayFormaContacto = formaContacto.split("|");
                var claseDocumento = response.data[0].valor2;
                var arrayClaseDocumento = claseDocumento.split("|");
                var proceso = response.data[0].valor3;
                var arrayProceso = proceso.split("|");
                var tarea = response.data[0].valor4;
                var arrayTarea = tarea.split("|");
                var observacion = response.data[0].valor5;
                observacion = observacion.replace("*nombre_tramo*", tramo);
                observacion = observacion.replace("*tarea_recorrido*", tareaRecorrido);
                document.getElementById("txtIdTareaTareaInforme").value = tareaRecorrido;
                document.getElementById("txtIdOrigenTareaInforme").value = arrayFormaContacto[0];
                document.getElementById("txtIdClaseTareaInforme").value = arrayClaseDocumento[0];
                document.getElementById("txtIdProcesoTareaInforme").value = arrayProceso[0];
                document.getElementById("txtIdAdmiTareaTareaInforme").value = arrayTarea[0];
                document.getElementById("txtObservacionTareaInforme").value = observacion;
            },
            failure: function(response){
                    console.log("failure");
            }
    });
}



function grabarTareaInforme()
{
    var idAsignacion   = document.getElementById('txtIdAsignacion').value;
    var formaContacto  = document.getElementById("txtIdOrigenTareaInforme").value;
    var claseDocumento = document.getElementById("txtIdClaseTareaInforme").value;
    var proceso        = document.getElementById("txtIdProcesoTareaInforme").value;
    var tarea          = document.getElementById("txtIdAdmiTareaTareaInforme").value;
    var idComunicacion = document.getElementById("txtIdTareaTareaInforme").value;
    var observacion    = document.getElementById("txtObservacionTareaInforme").value;
    var encargadoTarea = document.getElementById("cmbEncargado").value;

        var parametros = {
            "idAsignacion"     : idAsignacion,
            "idFormaContacto"  : formaContacto,
            "idClaseDocumento" : claseDocumento,
            "idProceso"        : proceso,
            "idTarea"          : tarea,
            "idComunicacion"   : idComunicacion,
            "observacion"      : observacion,
            "encargadoTarea" : encargadoTarea
        };
        $.ajax({
                data :  parametros,
                url  :  url_crear_tarea_informe,
                type :  'post',
                beforeSend: function () {
                        $('#btnLoadingGrabarTareaInforme').show();
                        $('#confirmaCrearTareaInforme').hide();
                },
                success:  function (response) {
                        if(response.status===200)
                        {
                            configuraMensajeIngresoConExito(
                                '#alertaConfirmaCrearTareaInforme',
                                '<strong>Se creó la tarea de informe con éxito!</strong>',
                                '#btnLoadingGrabarTareaInforme',
                                '#confirmaCrearTareaInforme'
                            );
                            //se cierra ventana luego de 2 segundos
                             setTimeout(function() {
                                $('#modalConfirmarCrearTareaInforme').modal('hide');
                                $('#alertaConfirmaCrearTareaInforme').hide();
                             }, 2000);
                             if(filtroDefaultRecorridos)
                             {
                                 document.getElementById("btnLimpiaBuscarRecorridoPorFecha").onclick();
                             }
                             else
                             {
                                 document.getElementById("btnBuscarRecorridoPorFecha").onclick();
                             }
                        }
                        else
                        {
                            var strMensajeError = 'Error, no se pudo crear la tarea de Informe!';
                            if(response.mensaje.indexOf('Estimado usuario ya existe una tarea de informe registrada para la tarea') != -1)
                            {
                                strMensajeError = response.mensaje;
                            }
                            configuraMensajeIngresoFallido('#alertaConfirmaCrearTareaInforme',
                            '<strong>'+strMensajeError+'</strong>',
                            '#btnLoadingGrabarTareaInforme',
                            '#confirmaCrearTareaInforme');
                            setTimeout(function() {
                                $('#modalConfirmarCrearTareaInforme').modal('hide');
                                $('#alertaConfirmaCrearTareaInforme').hide();
                             }, 2000);
                             document.getElementById('txtFlagCrearTarea').value = '';
                        }
                },
                failure: function(response){
                            configuraMensajeIngresoFallido('#alertaConfirmaCrearTareaInforme',
                            '<strong>Error, no se pudo crear la tarea de Informe!</strong>',
                            '#btnLoadingGrabarTareaInforme',
                            '#confirmaCrearTareaInforme');
                            setTimeout(function() {
                                $('#modalConfirmarCrearTareaInforme').modal('hide');
                                $('#alertaConfirmaCrearTareaInforme').hide();
                             }, 2000);
                             document.getElementById('txtFlagCrearTarea').value = '';
                }
        });
}

function mostrarIngresarTramo(id,tipoPendiente)
{
    document.getElementById('txtIdPendienteTramo').value = id;
    document.getElementById('txtTipoPendienteTramo').value = tipoPendiente;
    llenarComboTramos("cmbTramo","");
    llenarComboCircuitos("cmbCircuito","");
    llenarComboCiudad("cmbCiudad","");
    $('#modalIngresarTramo').modal('show');
    $('#alertaValidaAgregarTramo').hide();

    if(document.getElementById('txtModulo').value == 'recorridos')
    {
        $('#divCmbHiloTelefonica').show();
        $('#divCmbCircuito').hide();
        $('#divCmbCiudad').hide();
        $('#divCmbDepartamento').hide();
    }
    else if(document.getElementById('txtModulo').value == 'telefonica' && tipoPendiente == 'CASO')
    {
        $('#divCmbCircuito').show();
        $('#divCmbCiudad').show();
        $('#divCmbDepartamento').show();
        $('#divCmbHiloTelefonica').hide();
    }
    else if(document.getElementById('txtModulo').value == 'telefonica' && tipoPendiente == 'TAREA')
    {
        $('#divCmbCircuito').show();
        $('#divCmbCiudad').hide();
        $('#divCmbDepartamento').hide();
        $('#divCmbHiloTelefonica').hide();
    }
    $("#cmbHiloTelefonica").val("");
    $("#cmbCircuito").val("");
    $("#txtCircuitoSelect").val("");
    $("#cmbTramo").val("");
    $("#txtTramoSelect").val("");
    $("#cmbCiudad").val("");
    $("#txtCiudad").val("");
    $("#cmbDepartamento").val("");
    $("#txtDepartamento").val("");
    $("#txtDepartamento").attr('disabled','disabled');
}

function llenarComboTramos(nombreCombo,item)
{
    var parametros = {
        descParametro : "TRAMOS_PARA_MODULO_GESTION_PENDIENTES"
    };
    $.ajax({
            data :  parametros,
            url  :  url_obtener_parametros,
            type :  'post',
            success:  function (response) {
                var arrItems  = response.data;
                var items     = "<option>Seleccione...</option>"; 
                tramos   = [];
                tramosIds   = [];
                for(var i=0;i<arrItems.length;i++)
                {
                    items += "<option value='"+arrItems[i].valor2+"'>"+arrItems[i].valor2+"</option>";
                    tramos.push(arrItems[i].valor2);
                    tramosIds.push(arrItems[i].valor2);
                }
                $("#"+nombreCombo).html(items);
                autocomplete(document.getElementById("txtTramoSelect"),document.getElementById("cmbTramo"), tramos,tramosIds);
                if(item!=="")
                {
                    document.getElementById("txtTramoSelect").value = item;
                }
            },
            failure: function(response){
                    console.log("failure");
            }
    });
}

function llenarComboCircuitos(nombreCombo,item)
{
    var parametros = {
        descParametro : "CIRCUITOS_PARA_MODULO_GESTION_PENDIENTES"
    };
    $.ajax({
            data :  parametros,
            url  :  url_obtener_parametros,
            type :  'post',
            success:  function (response) {
                var arrItems  = response.data;
                var items     = "<option>Seleccione...</option>"; 
                /*for(var i=0;i<arrItems.length;i++)
                {
                    items += "<option value='"+arrItems[i].valor2+"'>"+arrItems[i].valor2+"</option>"; 
                }
                $("#"+nombreCombo).html(items);
                if(item!=="")
                {
                    document.getElementById(nombreCombo).value = item;
                }*/


                circuitos   = [];
                circuitosIds   = [];
                for(var i=0;i<arrItems.length;i++)
                {
                    items += "<option value='"+arrItems[i].valor2+"'>"+arrItems[i].valor2+"</option>";
                    circuitos.push(arrItems[i].valor2);
                    circuitosIds.push(arrItems[i].valor2);
                }
                $("#"+nombreCombo).html(items);
                autocomplete(document.getElementById("txtCircuitoSelect"),document.getElementById("cmbCircuito"), circuitos,circuitosIds);
                if(item!=="")
                {
                    document.getElementById("txtCircuitoSelect").value = item;
                }

            },
            failure: function(response){
                    console.log("failure");
            }
    });
}

function llenarComboCiudad(nombreCombo,item,isTareaInfo)
{
    var isTareaInformeRecorrido = (typeof isTareaInfo !== 'undefined' && isTareaInfo === 'S')?isTareaInfo:"N";
    var parametros = {
        estado : "Activo"
    };
    $.ajax({
            data :  parametros,
            url  :  url_ciudades_empresa,
            type :  'post',
            success:  function (response) {
                var arrItems  = response.encontrados;
                var items     = "<option>Seleccione...</option>";

                ciudades   = [];
                ciudadesIds   = [];
                for(var i=0;i<arrItems.length;i++)
                {
                    items += "<option value='"+arrItems[i].id_canton+"'>"+arrItems[i].nombre_canton+"</option>";
                    ciudades.push(arrItems[i].nombre_canton);
                    ciudadesIds.push(arrItems[i].id_canton);
                }
                $("#"+nombreCombo).html(items);
                if(isTareaInformeRecorrido === 'S')
                {
                    autocomplete(document.getElementById("txtCity"),document.getElementById("cmbCity"), ciudades,ciudadesIds);
                }else
                {
                    autocomplete(document.getElementById("txtCiudad"),document.getElementById("cmbCiudad"), ciudades,ciudadesIds);
                }
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

function llenarComboDepartamento(nombreCombo,item,idCanton,empresaCod,isTareaInfo)
{
    var isTareaInformeRecorrido = (typeof isTareaInfo !== 'undefined' && isTareaInfo === 'S')?isTareaInfo:"N";
    var parametros = {
        id_canton : idCanton,
        empresa : empresaCod,
        page : 1,
        start: 0,
        limit : 200
    };
    $.ajax({
            data :  parametros,
            url  :  url_departamentos_emp_ciudad,
            type :  'get',
            success:  function (response) {
                var arrItems  = response.encontrados;
                var items     = "<option>Seleccione...</option>";
                departamentos   = [];
                departamentosIds   = [];
                for(var i=0;i<arrItems.length;i++)
                {
                    items += "<option value='"+arrItems[i].id_departamento+"'>"+arrItems[i].nombre_departamento+"</option>";
                    departamentos.push(arrItems[i].nombre_departamento);
                    departamentosIds.push(arrItems[i].id_departamento);
                }
                $("#"+nombreCombo).html(items);
                if(isTareaInformeRecorrido === 'S')
                {
                    autocomplete(document.getElementById("txtDepartment"),document.getElementById("cmbDepartment"), departamentos,departamentosIds);
                }else
                {
                    autocomplete(document.getElementById("txtDepartamento"),document.getElementById("cmbDepartamento"), departamentos,departamentosIds);
                }
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

function llenarComboEncargado(nombreCombo,item,idCanton,idDepartamento)
{
    var parametros = {
        id_canton : idCanton,
        id_departamento : idDepartamento,
        page : 1,
        start: 0,
        limit : 25
    };
    $.ajax({
            data :  parametros,
            url  :  url_empleado_departamento_ciudad,
            type :  'get',
            success:  function (response) {
                var arrItems  = response.result.encontrados;
                var items     = "<option>Seleccione...</option>";
                empleados   = [];
                empleadosIds   = [];
                if(response.myMetaData.boolSuccess === '1')
                {
                    for(var i=0;i<arrItems.length;i++)
                    {
                        items += "<option value='"+arrItems[i].id_empleado+"'>"+arrItems[i].nombre_empleado+"</option>";
                        empleados.push(arrItems[i].nombre_empleado);
                        empleadosIds.push(arrItems[i].id_empleado);
                    }
                }else
                {
                    $('#lblMessageError').text(response.myMetaData.message);
                    $('#lblMessageError').css('display','block');
                    setTimeout(function() {
                        $('#lblMessageError').text('');
                        $('#lblMessageError').css('display','none');
                    }, 5000);
                }
                $("#"+nombreCombo).html(items);
                
                autocomplete(document.getElementById("txtEncargado"),document.getElementById("cmbEncargado"), empleados,empleadosIds);
               
                if(item!=="")
                {
                    document.getElementById(nombreCombo).value = item;
                }
            },
            failure: function(response){
                    console.log("failure jefes");
            }
    });
}

function grabarTramo()
{
    var idAsignacion    = document.getElementById('txtIdPendienteTramo').value;
    var tipoPendiente   = document.getElementById('txtTipoPendienteTramo').value;
    var tramo           = document.getElementById("cmbTramo").value;
    var hiloTelef       = document.getElementById("cmbHiloTelefonica").value;
    var circuito        = document.getElementById("cmbCircuito").value;
    var ciudad          = document.getElementById("cmbCiudad").value;
    var departamento    = document.getElementById("cmbDepartamento").value;
    var permiteGrabar   = false;
    if (document.getElementById('txtModulo').value == 'recorridos' && tramo != null && tramo != "" && tramo != "Seleccione..." &&
    hiloTelef != null && hiloTelef != "" && hiloTelef != "Seleccione...")
    {
        permiteGrabar = true;
    }
    else if(document.getElementById('txtModulo').value == 'telefonica' && tipoPendiente == 'CASO' 
    && tramo != null && tramo != "" && tramo != "Seleccione..." && 
    circuito != null && circuito != "" && circuito != "Seleccione..." &&
    ciudad != null && ciudad != "" && ciudad != "Seleccione..." && departamento != null && departamento != "" && departamento != "Seleccione...")
    {
        permiteGrabar = true;
    }
    else if(document.getElementById('txtModulo').value == 'telefonica' && tipoPendiente == 'TAREA' 
    && tramo != null && tramo != "" && tramo != "Seleccione..." && 
    circuito != null && circuito != "" && circuito != "Seleccione..." )
    {
        ciudad        = "";
        departamento  = "";
        permiteGrabar = true;
    }

    if($("#divTxtIngresarNuevoCircuito").is(":visible"))
    {
        configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
        '<strong>Por favor, debe terminar el ingreso del nuevo circuito o cancele la acción!</strong>',
        '#btnLoadingGrabarTramo',
        '#btnGrabarTramo');
    }
    else if($("#divTxtTramoIngresarTramo").is(":visible"))
    {
        configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
        '<strong>Por favor, debe terminar el ingreso del nuevo tramo o cancele la acción!</strong>',
        '#btnLoadingGrabarTramo',
        '#btnGrabarTramo');
    }
    else if (permiteGrabar)
    {
        var parametros = {
            "intId"          : idAsignacion,
            "strTramo"       : tramo,
            "strHiloTelef"   : hiloTelef,
            "strCircuito"    : circuito,
            "strCiudadNotif" : ciudad,
            "strDepNotif"    : departamento
        };
        $.ajax({
                data :  parametros,
                url  :  url_agregar_tramo,
                type :  'post',
                beforeSend: function () {
                        $('#btnLoadingGrabarTramo').show();
                        $('#btnGrabarTramo').hide();
                },
                success:  function (response) {
                        if(response==="OK")
                        {
                            configuraMensajeIngresoConExito(
                                '#alertaValidaAgregarTramo',
                                '<strong>Se guardaron los cambios con éxito!</strong>',
                                '#btnLoadingGrabarTramo',
                                '#btnGrabarTramo'
                            );

                            //se cierra ventana luego de 2 segundos
                             setTimeout(function() {
                                 $('#modalIngresarTramo').modal('hide');
                                 $('#alertaValidaAgregarTramo').hide();
                             }, 2000);
                            if(document.getElementById('txtModulo').value == 'recorridos' && filtroDefaultRecorridos)
                            {
                                document.getElementById("btnLimpiaBuscarRecorridoPorFecha").onclick();
                            }
                            else
                            {
                                document.getElementById("btnBuscarRecorridoPorFecha").onclick();
                            }

                            if(document.getElementById('txtModulo').value == 'telefonica' && filtroDefaultTelefonica)
                            {
                                document.getElementById("btnLimpiaBuscarTelefonicaPorFecha").onclick();
                            }
                            else
                            {
                                document.getElementById("btnBuscarTelefonicaPorFecha").onclick();
                            }

                            document.getElementById('txtIdPendienteCerrar').value = null;
                        }
                        else
                        {
                            configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                                           '<strong>'+response+'</strong>',
                                                           '#btnLoadingGrabarTramo',
                                                           '#btnGrabarTramo');
                        }
                },
                failure: function(response){
                    configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                                   '<strong>'+response+'</strong>',
                                                   '#btnLoadingGrabarTramo',
                                                   '#btnGrabarTramo');
                }
        });
    }
    else
    {
        if (document.getElementById('txtModulo').value == 'recorridos' )
        {
        configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                       '<strong>Debe ingresar el Tramo e Hilo Telefónica</strong>',
                                       '#btnLoadingGrabarTramo',
                                       '#btnGrabarTramo');
        }
        else if(document.getElementById('txtModulo').value == 'telefonica' && tipoPendiente == 'TAREA' )
        {
            configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                           '<strong>Debe ingresar el Circuito y Tramo</strong>',
                                           '#btnLoadingGrabarTramo',
                                           '#btnGrabarTramo');
        }
        else if(document.getElementById('txtModulo').value == 'telefonica' && tipoPendiente == 'CASO' )
        {
            configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                           '<strong>Debe ingresar el Circuito, Tramo, Ciudad y Departamento</strong>',
                                           '#btnLoadingGrabarTramo',
                                           '#btnGrabarTramo');
        }
        else{
            configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                           '<strong>Faltan datos por ingresar</strong>',
                                           '#btnLoadingGrabarTramo',
                                           '#btnGrabarTramo');
        }
    }
}


function grabarNuevoTramo(descripcionParametro,item)
{
    if ( item !== null && item !=="" && item !=="null")
    {
        existeTramo = false;
        $.ajax({
            data :  {
                        descParametro : descripcionParametro
                    },
            url  :  url_obtener_parametros,
            type :  'post',
            success:  function (response) {
                var arrTiposProblema  = response.data;
                for(var i=0;i<arrTiposProblema.length;i++)
                {
                    if(arrTiposProblema[i].valor2 == item)
                    {
                        existeTramo = true;
                        break;
                    }
                }

                if (!existeTramo)
                {
                    var parametros = {
                        "strValor1"                   : idDepartamento,
                        "strValor2"                   : item,
                        "strDescripcion"              : item,
                        "intIdParametroDet"           : "",
                        "strActualizaSoloDescripcion" : "NO",
                        "intParametroCab"             : idParametroCabTramo
                    };
            
                    $.ajax({
                            data :  parametros,
                            url  :  url_crea_admi_parametro_det,
                            type :  'post',
                            beforeSend: function () {
                                
                                    $('#btnLoadingGrabarNuevoTramo').show();
                                    $('#btnGrabarNuevoTramo').hide();
                            },
                            success:  function (response) {
                                    if(response.strStatus === "100")
                                    {
                                        $('#btnLoadingGrabarNuevoTramo').hide();
                                        $('#btnGrabarNuevoTramo').show();
                                        configuraMensajeIngresoConExito('#alertaValidaAgregarTramo',
                                                                        '<strong>Se agrego nuevo tramo con éxito</strong>',
                                                                        '#btnLoadingGrabarNuevoTramo',
                                                                        '#btnGrabarNuevoTramo');
                                        llenarComboTramos("cmbTramo",item);
                                        $('#divCmbTramoIngresarTramo').show();
                                        $('#divTxtTramoIngresarTramo').hide();
                                    }
                            },
                            failure: function(response){
                                
                            }
                    });
                }
                else
                {
                    $("#divTxtTramoIngresarTramo").removeClass('has-success');
                    $("#divTxtTramoIngresarTramo").addClass('has-error');
                    configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                                    '<strong>El tramo ingresado ya existe!</strong>',
                                                    '#btnLoadingGrabarNuevoTramo',
                                                    '#btnGrabarNuevoTramo');
                }
            },
            failure: function(response){
                    console.log("failure");
            }
        });

    }
    else
    {
        $("#divTxtTramoIngresarTramo").removeClass('has-success');
        $("#divTxtTramoIngresarTramo").addClass('has-error');
        configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                        '<strong>Debe ingresar un tramo!</strong>',
                                        '#btnLoadingGrabarNuevoTramo',
                                        '#btnGrabarNuevoTramo');
    }
}

function grabarNuevoCircuito(descripcionParametro,item)
{
    if ( item !== null && item !=="" && item !=="null")
    {
        existe = false;
        $.ajax({
            data :  {
                        descParametro : descripcionParametro
                    },
            url  :  url_obtener_parametros,
            type :  'post',
            success:  function (response) {
                var arrDatos  = response.data;
                for(var i=0;i<arrDatos.length;i++)
                {
                    if(arrDatos[i].valor2 == item)
                    {
                        existe = true;
                        break;
                    }
                }
                if (!existe)
                {
                    var parametros = {
                        "strValor1"                   : idDepartamento,
                        "strValor2"                   : item,
                        "strDescripcion"              : item,
                        "intIdParametroDet"           : "",
                        "strActualizaSoloDescripcion" : "NO",
                        "intParametroCab"             : idParametroCabCircuito
                    };
            
                    $.ajax({
                            data :  parametros,
                            url  :  url_crea_admi_parametro_det,
                            type :  'post',
                            beforeSend: function () {
                                    $('#btnLoadingGrabarNuevoCircuito').show();
                                    $('#btnGrabarNuevoCircuito').hide();
                            },
                            success:  function (response) {
                                    if(response.strStatus === "100")
                                    {
                                        $('#btnLoadingGrabarNuevoCircuito').hide();
                                        $('#btnGrabarNuevoCircuito').show();
                                        configuraMensajeIngresoConExito('#alertaValidaAgregarTramo',
                                                                        '<strong>Se agrego nuevo Circuito con éxito</strong>',
                                                                        '#btnLoadingGrabarNuevoCircuito',
                                                                        '#btnGrabarNuevoCircuito');
                                        llenarComboCircuitos("cmbCircuito",item);
                                        $('#divCmbCircuito').show();
                                        $('#divTxtIngresarNuevoCircuito').hide();
                                    }
                            },
                            failure: function(response){
                            }
                    });
                }
                else
                {
                    $("#divTxtIngresarNuevoCircuito").removeClass('has-success');
                    $("#divTxtIngresarNuevoCircuito").addClass('has-error');
                    configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                                    '<strong>El circuito ingresado ya existe!</strong>',
                                                    '#btnLoadingGrabarNuevoCircuito',
                                                    '#btnGrabarNuevoCircuito');
                }
            },
            failure: function(response){
                    console.log("failure");
            }
        });

    }
    else
    {
        $("#divTxtIngresarNuevoCircuito").removeClass('has-success');
        $("#divTxtIngresarNuevoCircuito").addClass('has-error');
        configuraMensajeIngresoFallido('#alertaValidaAgregarTramo',
                                        '<strong>Debe ingresar un circuito!</strong>',
                                        '#btnLoadingGrabarNuevoCircuito',
                                        '#btnGrabarNuevoCircuito');
    }
}