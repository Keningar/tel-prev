var detalleAsignaciones;
var         rowsData = [
    ["Item", "Tipo","Ticket", "Login Afectado","Fecha Inicio","Fecha Fin","Observación (Ult. Seguimiento)","Estado"]
];
//Se declara fecha inicio y fecha fin con la fecha de hoy por defecto
var start         = moment().startOf('month');
var end           = moment().endOf('month');
var procedencia   = 'Interno-gestion';
var filtroDefault = true;
var filtroDefaultRecorridos = true;
var filtroDefaultTelefonica = true;
var filtroDefaultMunicipio  = true;
setInterval(
    function () {
                    if (document.getElementById('txtModulo').value == 'backbone')
                    {
                        document.getElementById("lnBackbone").onclick();
                    }
                    else if (document.getElementById('txtModulo').value == 'recorridos')
                    {
                        document.getElementById("lnRecorrido").onclick();
                    }
                    else if (document.getElementById('txtModulo').value == 'telefonica')
                    {
                        document.getElementById("lnTelefonica").onclick();
                    }
                    else if (document.getElementById('txtModulo').value == 'municipio')
                    {
                        document.getElementById("lnMunicipio").onclick();
                    }
                }, 
        240000 
   );

function cb(start, end) {
    if (document.getElementById('txtModulo').value == "backbone")
    {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    else if (document.getElementById('txtModulo').value == "recorridos")
    {
        $('#reportrangeRecorrido span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    else if (document.getElementById('txtModulo').value == "telefonica")
    {
        $('#reportrangeTelefonica span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    else if (document.getElementById('txtModulo').value == "municipio")
    {
        $('#reportrangeMunicipio span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    else
    {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#reportrangeRecorrido span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#reportrangeTelefonica span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#reportrangeMunicipio span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
}

//Datarangepicker que se usa para seleccionar la fecha de consulta de información de los gráficos
$('#reportrange').daterangepicker(
    {
        startDate: start,
        endDate  : end,
        ranges   : 
        {
           'Hoy'             : [moment(), moment()],
           'Ultimos 7 Días'  : [moment().subtract(6, 'days'), moment()],
           'Este mes'        : [moment().startOf('month'), moment().endOf('month')],
           'El mes Anterior' : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: 
        {
            "customRangeLabel": "Rango de fechas",
        },
        autoApply : true,
        opens     : "right" ,
        drops     : "down"
    }, cb);

$('#reportrangeRecorrido').daterangepicker(
{
    startDate: start,
    endDate  : end,
    ranges   : 
    {
       'Hoy'             : [moment(), moment()],
       'Ultimos 7 Días'  : [moment().subtract(6, 'days'), moment()],
       'Este mes'        : [moment().startOf('month'), moment().endOf('month')],
       'El mes Anterior' : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    locale: 
    {
        "customRangeLabel": "Rango de fechas",
    },
    autoApply : true,
    opens     : "right" ,
    drops     : "down"
}, cb);

$('#reportrangeTelefonica').daterangepicker(
    {
        startDate: start,
        endDate  : end,
        ranges   : 
        {
           'Hoy'             : [moment(), moment()],
           'Ultimos 7 Días'  : [moment().subtract(6, 'days'), moment()],
           'Este mes'        : [moment().startOf('month'), moment().endOf('month')],
           'El mes Anterior' : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: 
        {
            "customRangeLabel": "Rango de fechas",
        },
        autoApply : true,
        opens     : "right" ,
        drops     : "down"
    }, cb);

$('#reportrangeMunicipio').daterangepicker(
    {
        startDate: start,
        endDate  : end,
        ranges   : 
        {
            'Hoy'             : [moment(), moment()],
            'Ultimos 7 Días'  : [moment().subtract(6, 'days'), moment()],
            'Este mes'        : [moment().startOf('month'), moment().endOf('month')],
            'El mes Anterior' : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: 
        {
            "customRangeLabel": "Rango de fechas",
        },
        autoApply : true,
        opens     : "right" ,
        drops     : "down"
    }, cb);

cb(start, end);

$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
});
$('#reportrangeRecorrido').on('apply.daterangepicker', function(ev, picker) {
});
$('#reportrangeTelefonica').on('apply.daterangepicker', function(ev, picker) {
});
//llamamos a plugin de bootstrap datetimepicker
$(function () {
    fechaHoy = new Date();
});

$(document).ready(function() {

    //Inicializamos los campos de busqueda
    $("#textBtnTipoPendiente").text("TODOS   ");
    $("#txtIdBtnTipoPendiente").val("");
    $("#textBtnTipoPendienteTelefonica").text("TODOS   ");
    $("#txtIdBtnTipoPendienteTelefonica").val("");
    $("#textBtnTipoPendienteMunicipio").text("TODOS   ");
    $("#txtIdBtnTipoPendienteMunicipio").val("");
    $("#textBtnEstados").text("ABIERTO   ");
    $("#txtIdBtnEstados").val("ABIERTO");
    $("#textBtnEstadosRecorrido").text("ABIERTO   ");
    $("#txtIdBtnEstadosRecorrido").val("ABIERTO");
    $("#textBtnEstadosTelefonica").text("ABIERTO   ");
    $("#txtIdBtnEstadosTelefonica").val("ABIERTO");
    $("#textBtnEstadosMunicipio").text("ABIERTO   ");
    $("#txtIdBtnEstadosMunicipio").val("ABIERTO");
    $('[data-toggle="tooltip"]').tooltip();  

    //define configuracion de ventana de ingreso de asignaciones
    $('.modal-content').resizable({
        minHeight: 300,
        minWidth: 300
        });
        //configura a arrastrable la ventana de ingreso de asignaciones
        $('.modal-dialog').draggable();

});

document.getElementById("lnBackbone").onclick = function () { 
    document.getElementById('txtModulo').value = "backbone";
    $('#divListadoBackbone').show();
    $('#liBackbone').addClass('active');
    $('#liRecorrido').removeClass('active');
    $('#liTelefonica').removeClass('active');
    $('#liMunicipio').removeClass('active');
    $('#divListadoRecorrido').hide();
    $('#divListadoTelefonica').hide();
    $('#divListadoMunicipio').hide();
    $('#divInfoPendiente').hide();
    $("#divTareasRecorrido").html('');
    if(filtroDefault)
    {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    var tipoPendiente  = document.getElementById('txtIdBtnTipoPendiente').value;
    var estado         = document.getElementById('txtIdBtnEstados').value;
    var fechaInicio    = $('#reportrange').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaFin       = $('#reportrange').data('daterangepicker').endDate.format('DD/MM/YYYY');
    buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "backbone",filtroDefault);
};
document.getElementById("lnRecorrido").onclick = function () { 
    document.getElementById('txtModulo').value = "recorridos";
    $('#divListadoRecorrido').show();
    $('#liRecorrido').addClass('active');
    $('#liBackbone').removeClass('active');
    $('#liTelefonica').removeClass('active');
    $('#liMunicipio').removeClass('active');
    $('#divListadoBackbone').hide();
    $('#divListadoTelefonica').hide();
    $('#divListadoMunicipio').hide();
    $('#divInfoPendiente').hide();
    $("#divTareasRecorrido").html('');
    if(filtroDefaultRecorridos)
    {
        $('#reportrangeRecorrido span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    var tipoPendiente  = 'TAREA';
    var estado         = document.getElementById('txtIdBtnEstadosRecorrido').value;
    var fechaInicio    = $('#reportrangeRecorrido').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaFin       = $('#reportrangeRecorrido').data('daterangepicker').endDate.format('DD/MM/YYYY');
    buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado,"recorridos",filtroDefaultRecorridos);
};
document.getElementById("lnTelefonica").onclick = function () { 
    document.getElementById('txtModulo').value = "telefonica";
    $('#divListadoTelefonica').show();
    $('#liTelefonica').addClass('active');
    $('#liBackbone').removeClass('active');
    $('#liRecorrido').removeClass('active');
    $('#liMunicipio').removeClass('active');
    $('#divListadoBackbone').hide();
    $('#divListadoRecorrido').hide();
    $('#divListadoMunicipio').hide();
    $('#divInfoPendiente').hide();
    $("#divTareasRecorrido").html('');
    if(filtroDefaultTelefonica)
    {
        $('#reportrangeTelefonica span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    var tipoPendiente  = document.getElementById('txtIdBtnTipoPendienteTelefonica').value;
    var estado         = document.getElementById('txtIdBtnEstadosTelefonica').value;
    var fechaInicio    = $('#reportrangeTelefonica').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaFin       = $('#reportrangeTelefonica').data('daterangepicker').endDate.format('DD/MM/YYYY');
    buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, document.getElementById('txtModulo').value,filtroDefaultTelefonica);
};
document.getElementById("lnMunicipio").onclick = function () { 
    document.getElementById('txtModulo').value = "municipio";
    $('#divListadoMunicipio').show();
    $('#liMunicipio').addClass('active');
    $('#liBackbone').removeClass('active');
    $('#liRecorrido').removeClass('active');
    $('#liTelefonica').removeClass('active');
    $('#divListadoRecorrido').hide();
    $('#divListadoTelefonica').hide();
    $('#divListadoBackbone').hide();
    $('#divInfoPendiente').hide();
    $("#divTareasRecorrido").html('');
    if(filtroDefaultMunicipio)
    {
        $('#reportrangeMunicipio span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }
    var tipoPendiente  = document.getElementById('txtIdBtnTipoPendienteMunicipio').value;
    var estado         = document.getElementById('txtIdBtnEstadosMunicipio').value;
    var fechaInicio    = $('#reportrangeMunicipio').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaFin       = $('#reportrangeMunicipio').data('daterangepicker').endDate.format('DD/MM/YYYY');
    buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "municipio",filtroDefaultMunicipio);
};
document.getElementById("btnTipoPendiente").onclick = function () 
{ 
    var arrayDatosCombo = [];
    arrayDatosCombo.push({descripcion:"TAREA", valor:"TAREA"});
    arrayDatosCombo.push({descripcion:"CASO", valor:"CASO"});
    llenarCombo("#ulDropDownTipoPendiente",
                            "#textBtnTipoPendiente",
                            "#txtIdBtnTipoPendiente",
                            "txtBuscaPendientes",
                            arrayDatosCombo
                            ); 
};

document.getElementById("btnEstados").onclick = function () 
{   
    var arrayDatosCombo = [];
    arrayDatosCombo.push({descripcion:"ABIERTO", valor:"ABIERTO"});
    arrayDatosCombo.push({descripcion:"CERRADO", valor:"CERRADO"});
    llenarCombo("#ulDropDownEstados",
                      "#textBtnEstados",
                      "#txtIdBtnEstados",
                      "txtBuscaEstadosPendientes",
                      arrayDatosCombo);
};
document.getElementById("btnBuscarPorFecha").onclick = function () {
    var tipoPendiente  = document.getElementById('txtIdBtnTipoPendiente').value;
    var estado         = document.getElementById('txtIdBtnEstados').value;
    var fechaInicio    = $('#reportrange').data('daterangepicker').startDate.format('DD/MM/YYYY');
    var fechaFin       = $('#reportrange').data('daterangepicker').endDate.format('DD/MM/YYYY');
    filtroDefault      = false;
    buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "backbone",filtroDefault);

};

document.getElementById("btnLimpiaBuscarPorFecha").onclick = function () 
{
    filtroDefault = true;
    $('#reportrange').data('daterangepicker').setStartDate(start);
    $('#reportrange').data('daterangepicker').setEndDate( end);
    cb(start, end);
    var tipoPendiente    = "";
    var estado           = "ABIERTO";
    $("#textBtnTipoPendiente").text("TODOS   ");
    $("#txtIdBtnTipoPendiente").val("");
    $("#textBtnEstados").text(estado.toUpperCase()+"   ");
    $("#txtIdBtnEstados").val(estado.toUpperCase());

    buscarPendientes("", "", tipoPendiente, estado, "backbone",filtroDefault);
};

document.getElementById("btnNuevoSeguimiento").onclick = function () 
{   
    document.getElementById('txtaDetalleSeg').value = '';
    $( "#txtaDetalleSeg" ).prop( "disabled", false );
    var arrayCampos = ['txtaDetalleSeg'];
    limpiarCampos('#alertaValidaNuevoSeguimiento',arrayCampos);
};

//Configura la acción de grabar nueva asignacion en el boton btnGrabarAsignacion
document.getElementById("btnGrabarSeguimiento").onclick = function () 
{
    grabarNuevoSeguimiento(); 
};

document.getElementById("confirmaCerrarAsignacion").onclick = function () 
{
    modificarEstadoDePendiente("Cerrado");
};


document.getElementById("cancelaCerrarAsignacion").onclick = function () 
{ 
    document.getElementById('txtIdPendienteCerrar').value = null;
    $('#modalConfirmarCerrarAsignacion').modal('hide');
};

document.getElementById("confirmaEnviarListadoPendientes").onclick = function () 
{
    enviarListadoPendientes();
};


document.getElementById("cancelaEnviarListadoPendientes").onclick = function () 
{
    $('#modalConfirmarListadoPendientes').modal('hide');
};

document.getElementById("btnActualizarSeguimientos").onclick = function () 
{
    var tipoPendiente  = "";
    var estado         = "";
    var fechaInicio    = "";
    var fechaFin       = "";
    if ( document.getElementById('txtModulo').value == "recorridos" )
    {
        tipoPendiente  = "TAREA";
        estado         = document.getElementById('txtIdBtnEstadosRecorrido').value;
        fechaInicio    = $('#reportrangeRecorrido').data('daterangepicker').startDate.format('DD/MM/YYYY');
        fechaFin       = $('#reportrangeRecorrido').data('daterangepicker').endDate.format('DD/MM/YYYY');
        buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "recorridos",filtroDefaultRecorridos);
    }
    else if( document.getElementById('txtModulo').value == "backbone" )
    {
        tipoPendiente  = document.getElementById('txtIdBtnTipoPendiente').value;
        estado         = document.getElementById('txtIdBtnEstados').value;
        fechaInicio    = $('#reportrange').data('daterangepicker').startDate.format('DD/MM/YYYY');
        fechaFin       = $('#reportrange').data('daterangepicker').endDate.format('DD/MM/YYYY');
        buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "backbone",filtroDefault);
    }
    else if( document.getElementById('txtModulo').value == "telefonica" )
    {
        tipoPendiente  = document.getElementById('txtIdBtnTipoPendienteTelefonica').value;
        estado         = document.getElementById('txtIdBtnEstadosTelefonica').value;
        fechaInicio    = $('#reportrangeTelefonica').data('daterangepicker').startDate.format('DD/MM/YYYY');
        fechaFin       = $('#reportrangeTelefonica').data('daterangepicker').endDate.format('DD/MM/YYYY');
        buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "telefonica",filtroDefaultTelefonica);
    }
    else if( document.getElementById('txtModulo').value == "municipio" )
    {
        tipoPendiente  = document.getElementById('txtIdBtnTipoPendienteMunicipio').value;
        estado         = document.getElementById('txtIdBtnEstadosMunicipio').value;
        fechaInicio    = $('#reportrangeMunicipio').data('daterangepicker').startDate.format('DD/MM/YYYY');
        fechaFin       = $('#reportrangeMunicipio').data('daterangepicker').endDate.format('DD/MM/YYYY');
        buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, "municipio",filtroDefaultMunicipio);
    }
};


    function buscarPendientes(fechaInicio, fechaFin, tipoPendiente, estado, tipoPendientes,filtroDefaultLocal)
    {
        var parametros     = "";
        if ( fechaInicio != "" && fechaFin != "" && !filtroDefaultLocal)
        {
            parametros = "fechaIni="+fechaInicio+"&fechaFin="+fechaFin;
        }

        if (tipoPendiente != null && tipoPendiente != "")
        {
            parametros += "&tipoPendiente="+tipoPendiente;
        }

        if (estado != null && estado != "")
        {
            parametros += "&estado="+estado;
        }

        if (tipoPendientes == "backbone")
        {
            parametros += "&tabVisible="+"GestionPendientesBackbone";
            detalleAsignaciones.ajax.url(url_detalle_asignaciones+"?"+parametros).load();
        }
        else if (tipoPendientes == "recorridos")
        {
            parametros += "&tabVisible="+"GestionPendientesRecorridos";
            detalleAsignacionesRecorridos.ajax.url(url_detalle_asignaciones+"?"+parametros).load();
        }
        else if (tipoPendientes == "telefonica")
        {
            parametros += "&tabVisible="+"GestionPendientesTelefonica";
            detalleAsignacionesTelefonica.ajax.url(url_detalle_asignaciones+"?"+parametros).load();
        }
        else if (tipoPendientes == "municipio")
        {
            parametros += "&tabVisible="+"GestionPendientesMunicipio";
            detalleAsignacionesMunicipio.ajax.url(url_detalle_asignaciones+"?"+parametros).load();
        }
    }

    function llenarCombo(ulDropDown,text,textId, input, arrayDatos)
    {
        var dropDown     ='<input class="form-control" id="'+input+'" name="'+input+'" '+
        'type="text" placeholder="Buscar.." onkeyup="" style="display:none">';
        var itemDropDown ="";
        for(var i=0; i < arrayDatos.length; i++)
        {                        
            itemDropDown+= "<li>"+
            "<a href='javascript:obtieneValorComboFiltroBusqueda(\""+arrayDatos[i].descripcion+"\",\""+
                                                                    arrayDatos[i].valor+"\",\""+
                                                                    text+"\",\""+
                                                                    textId+"\");'>"+
                                                                    arrayDatos[i].descripcion+
            "</a></li>";
        }
        dropDown = dropDown + "<li><a href='javascript:obtieneValorComboFiltroBusqueda(\"TODOS\",\"\",\""+
                                                                                        text+"\",\""+
                                                                                        textId+"\");'>TODOS</a></li>" + itemDropDown + '';
        $(ulDropDown).html(dropDown);
    }

    function obtieneValorComboFiltroBusqueda(valorCombo, valorInput, combo, input)
    {
        $(combo).text(valorCombo+"   ");
        $(input).val(valorInput);

    }
    function mostrarInformacionDelPendiente(data)
    {
        detalleSeguimientos.clear().draw();
        $("#divInfoTareaCaso").html("");
        mostrarDivInformacionDelPendiente();
        document.getElementById('txtReferenciaId').value     = data.referencia_id;
        document.getElementById('txtIdAsignacion').value     = data.id_asignacion;
        document.getElementById('txtTipo').value             = data.tipo;
        document.getElementById('txtEstado').value           = data.estado;
        document.getElementById('txtEstadoPendiente').value  = data.estado_pendiente;
        document.getElementById("txtIdDetalle").value        = '';
        document.getElementById('txtIdTarea').value          = '';
        var tareas       = '';
        var tareaInicial = '';
        var estado       = data.estado;

        if (data.tipo == 'CASO')
        {
            $('#btnGrabarSeguimiento').attr('disabled','disabled');
            var countTareasFinalizadas = 0;
            var jsonTareas = JSON.parse(data.tareas);
            for(var indice=0; indice < jsonTareas.length; indice ++)
            {

                var estadoTarea      = jsonTareas[indice].ESTADO;
                var letraEstadoTarea = '';
                var color = "#cfd8dc;";

                if(jsonTareas[indice].ESTADO == 'Cerrado' || jsonTareas[indice].ESTADO == 'Finalizada')
                {
                  color = "#4db6ac;";
                  countTareasFinalizadas = countTareasFinalizadas + 1;
                }
                else if(jsonTareas[indice].ESTADO == 'Asignado' || jsonTareas[indice].ESTADO == 'Asignada')
                {
                  color = "#9fa8da;";
                }
                else if(jsonTareas[indice].ESTADO == 'Reprogramada')
                {
                  color = "#bcaaa4;";
                }

                letraEstadoTarea = estadoTarea.substring(0, 1);
                

                tareas = tareas + '<li class="nav-item" id="liTareasCaso'+jsonTareas[indice].ID+'">'+
                                '<a  href=\'javascript:mostrarSeguimientos('+
                                    jsonTareas[indice].ID+','+
                                    JSON.stringify(data.tareas)+');\' id=\'lnTareasCaso'+jsonTareas[indice].ID+'\' >'+
                                    jsonTareas[indice].ID+' <span class="badge" style="background-color:'+color+'">'+
                                    letraEstadoTarea+'</span> </a> '+
                                '</li>';

                if (indice == 0)
                {
                    tareaInicial = jsonTareas[indice].ID;
                }   
            }
            if (jsonTareas.length != 0 && jsonTareas.length == countTareasFinalizadas && estado != 'Cerrado')
            {
                estado = 'Cerrar Caso';
            }
        }
        else
        {
            $('#btnGrabarSeguimiento').removeAttr('disabled');
            document.getElementById("txtIdDetalle").value = data.detalle_id;
            document.getElementById('txtIdTarea').value   = data.referencia_id;
            detalleSeguimientos.ajax.url(url_seguimientos+"?idTarea="+data.referencia_id+"&referenciaId="+data.referencia_id+"&procedencia="+procedencia).load();
        }

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

        var tituloVersionInicial = "Observaci&oacute;n:";
        var textoVersionInicial  = data.observacion;
        var divNombreTarea       = 
        '<div class="row">'+
        '  <div class="col-lg-2 label-info-first"><b>Tarea: </b></div>'+
        '  <div class="col-lg-3 text-label-info-gen"> '+data.tipo_problema+' </div>'+
        '  <div class="col-lg-2 label-info-gen">&nbsp;</div>'+
        '  <div class="col-lg-3 text-label-info-gen">&nbsp;</div>'+
        '</div>';

        if (data.tipo == 'CASO')
        {
            tituloVersionInicial = "Versi&oacute;n Inicial:";
            textoVersionInicial  = data.versionInicial;
            divNombreTarea       = "";

        }

        var strDivColor = "#cfd8dc;";

        
        if(estado == 'Cerrado' || estado == 'Finalizada')
        {
            strDivColor = "#4db6ac;";
            countTareasFinalizadas = countTareasFinalizadas + 1;
        }
        else if(estado == 'Asignado' || estado == 'Asignada')
        {
            strDivColor = "#9fa8da;";
        }
        else if(estado == 'Reprogramada')
        {
            strDivColor = "#bcaaa4;";
        }
        else if (estado == 'Cerrar Caso')
        {
            strDivColor = "#ff867a;";

        }
        var strDivEstado = "<div style=\"padding:0.2em;width:90px;background-color:"+strDivColor+"\">"+estado+"</div>";

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
            '  <div class="col-lg-2 label-info-gen"><b>Tipo:</b></div>'+
            '  <div class="col-lg-3 text-label-info-gen"> '+data.tipo+' </div>'+
            '</div>'+
            divNombreTarea+
            '<div class="row">'+
            '<div class="col-lg-10 label-info-gen" style="margin-left:1.5em;width:95%" ><b>'+tituloVersionInicial+' </b></div>'+
            '</div>'+
            '<div class="row">'+
            '<div class="col-lg-10 text-label-info-gen" style="margin-left:1.5em;width:95%">'+textoVersionInicial+'</div>'+
            '</div>'
        );

        $("#divTareasCaso").html(
            '<ul class="nav nav-tabs">'+
            tareas + 
            '</ul>'
        );

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

    function mostrarDivInformacionDelPendiente()
    {
        $('#divListadoBackbone').hide();
        $('#divListadoRecorrido').hide();
        $('#divListadoTelefonica').hide();
        $('#divListadoMunicipio').hide();
        $('#divInfoPendiente').show();
    }

    function mostrarSeguimientos(idTarea, tareas)
    {
        $("#divInfoTareaCaso").html("");
        if (document.getElementById('txtTipo').value == 'CASO')
        {
            var parametros = {
                "idTarea" : idTarea
            };
            $.ajax(
            {
                data :  parametros,
                url  :  url_obtener_datos_tarea,
                type :  'get',
                success:  function (response) {

                    $("#divInfoTareaCaso").html(

                        '<div class="row"">'+
                        '  <div class="col-lg-2 label-info-first"><b>N&uacute;mero Tarea: </b></div>'+
                        '  <div class="col-lg-3 text-label-info-gen ticket-resaltado"> '+idTarea+' </div>'+
                        '  <div class="col-lg-2 label-info-gen"><b>Nombre Tarea: </b></div>'+
                        '  <div class="col-lg-3 text-label-info-gen"> '+response.data[0].tarea+' </div>'+
                        '</div>'+
                        '<div class="row">'+
                        '  <div class="col-lg-10 label-info-gen" style="margin-left:1.5em;width:95%" ><b>Observaci&oacute;n: </b></div>'+
                        '</div>'+
                        '<div class="row">'+
                        '<div class="col-lg-10 text-label-info-gen" style="margin-left:1.5em;width:95%">'+response.data[0].observacion+'</div>'+
                        '</div>'
                    );
                }
            });
        }
        document.getElementById('txtIdTarea').value = idTarea;
        var jsonTareas = JSON.parse(tareas);
        for(var indice=0; indice < jsonTareas.length; indice ++)
        {
            $('#liTareasCaso'+jsonTareas[indice].ID).removeClass('active');

            if (idTarea == jsonTareas[indice].ID)
            {
                document.getElementById('txtEstadoTarea').value = jsonTareas[indice].ESTADO;
                document.getElementById('txtIdDetalle').value   = jsonTareas[indice].IDDET;
                
                if (document.getElementById('txtEstadoTarea').value != 'Finalizada' ) 
                {
                    $('#btnNuevoSeguimiento').show();
                    $('#btnNuevoSeguimientoInterno').hide();
                }
                else
                {
                    $('#btnNuevoSeguimiento').hide();
                    $('#btnNuevoSeguimientoInterno').show();
                }
            }
        }

        $('#liTareasCaso'+idTarea).addClass('active');
        $('#btnGrabarSeguimiento').removeAttr('disabled');
        //Consultar con ajax los seguimientos de una tarea
        detalleSeguimientos.ajax.url(url_seguimientos+"?idTarea="+document.getElementById('txtIdTarea').value+
                                                        "&referenciaId="+document.getElementById('txtReferenciaId').value+
                                                        "&procedencia="+procedencia).load();
    }

    function grabarNuevoSeguimiento()
    {
       if($("#btnNuevoSeguimiento").is(":visible"))
       {
           grabarNuevoSeguimientoExterno();
       }
       else if($("#btnNuevoSeguimientoInterno").is(":visible"))
       {
           grabarNuevoSeguimientoInterno();
       }
    }

    function grabarNuevoSeguimientoExterno()
    {
        var idCaso = "";
        if (document.getElementById("txtTipo").value == 'CASO')
        {
            idCaso = document.getElementById("txtReferenciaId").value;
        }

        var parametros = {
            "seguimiento"     : document.getElementById('txtaDetalleSeg').value,
            "id_caso"         : idCaso,
            "id_detalle"      : document.getElementById("txtIdDetalle").value,
            "registroInterno" : 'N'
        };

        if (document.getElementById('txtaDetalleSeg').value != "")
        {
            $.ajax({
                    data :  parametros,
                    url  :  url_crea_seguimiento_externo,
                    type :  'post',
                    beforeSend: function () {
                            $('#btnLoadingGrabarNuevoSeguimiento').show();
                            $('#btnGrabarSeguimiento').hide();
                            $( "#txtaDetalleSeg" ).prop( "disabled", true );
                            $('#btnCerrarGrabarSeguimiento').attr('disabled','disabled');
                    },
                    success:  function (response) 
                    {
                        if (response.status == 'OK' && response.mensaje == 'Se ingreso el seguimiento!')
                        {
                            configuraMensajeIngresoConExito('#alertaValidaNuevoSeguimiento',
                                                            '<strong>Se Grabaron datos con éxito</strong>',
                                                            '#btnLoadingGrabarNuevoSeguimiento',
                                                            '#btnGrabarSeguimiento');
                            $('#btnGrabarSeguimiento').attr('disabled','disabled');
                            //se cierra ventana luego de 2 segundos
                             setTimeout(function() 
                             {
                                 $('#btnGrabarSeguimiento').removeAttr('disabled');
                                 $('#modalIngresarSeguimiento').modal('hide');
                                 var arrayCampos = ['txtaDetalleSeg'];
                                 limpiarCampos('#alertaValidaNuevoSeguimiento',arrayCampos);
                             }, 2000);

                        }
                        else if(response.status == 'OK' && response.mensaje == 'cerrada')
                        {
                            configuraMensajeIngresoFallido('#alertaValidaNuevoSeguimiento',
                                                            '<strong>No se puede ingresar seguimiento, la tarea ya se encuentra cerrada!</strong>',
                                                            '#btnLoadingGrabarNuevoSeguimiento','#btnGrabarSeguimiento');
                        }
                        else
                        {
                            configuraMensajeIngresoFallido('#alertaValidaNuevoSeguimiento',
                                                            '<strong>No se puede ingresar seguimiento, consultar con administrador!</strong>',
                                                            '#btnLoadingGrabarNuevoSeguimiento','#btnGrabarSeguimiento');
                        }
                        $('#btnCerrarGrabarSeguimiento').removeAttr('disabled');
                        $( "#txtaDetalleSeg" ).prop( "disabled", false );
                        detalleSeguimientos.ajax.url(url_seguimientos+"?idTarea="+document.getElementById('txtIdTarea').value+
                                                                      "&referenciaId="+document.getElementById('txtReferenciaId').value+
                                                                      "&procedencia="+procedencia).load();
                    },
                    failure: function(response){
                            console.log("failure");
                            $('#btnCerrarGrabarSeguimiento').removeAttr('disabled');
                            $( "#txtaDetalleSeg" ).prop( "disabled", false );
                    }
            });

        }
        else
        {
            configuraMensajeIngresoFallido('#alertaValidaNuevoSeguimiento',
                                           '<strong>Por favor ingresar Detalle del seguimiento</strong>',
                                           '#btnLoadingGrabarNuevoSeguimiento','#btnGrabarSeguimiento');
        }
    }

    function grabarNuevoSeguimientoInterno()
    {
        var parametros = {
            "strDetalle"     : document.getElementById('txtaDetalleSeg').value,
            "intId"          : document.getElementById("txtIdAsignacion").value,
            "sync"           : "N",
            "procedencia"    : procedencia,
            "comunicacionId" : document.getElementById('txtIdTarea').value
        };

        if (document.getElementById('txtaDetalleSeg').value != "")
        {
            $.ajax({
                    data :  parametros,
                    url  :  url_crea_seguimiento_interno,
                    type :  'post',
                    beforeSend: function () {
                            $('#btnLoadingGrabarNuevoSeguimiento').show();
                            $('#btnGrabarSeguimiento').hide();
                            $( "#txtaDetalleSeg" ).prop( "disabled", true );
                            $('#btnCerrarGrabarSeguimiento').attr('disabled','disabled');
                    },
                    success:  function (response) {
                            configuraMensajeIngresoConExito('#alertaValidaNuevoSeguimiento',
                                                            '<strong>Se Grabaron datos con éxito</strong>',
                                                            '#btnLoadingGrabarNuevoSeguimiento',
                                                            '#btnGrabarSeguimiento');
                            $('#btnGrabarSeguimiento').attr('disabled','disabled');
                            //se cierra ventana luego de 2 segundos
                             setTimeout(function() {
                                 $('#btnGrabarSeguimiento').removeAttr('disabled');
                                 $('#modalIngresarSeguimiento').modal('hide');
                                 var arrayCampos  = ['txtaDetalleSeg'];
                                 limpiarCampos('#alertaValidaNuevoSeguimiento',arrayCampos);
                             }, 2000);
                             $('#btnCerrarGrabarSeguimiento').removeAttr('disabled');
                             $( "#txtaDetalleSeg" ).prop( "disabled", false );
                            detalleSeguimientos.ajax.url(url_seguimientos+"?idTarea="+document.getElementById('txtIdTarea').value+
                                                                        "&referenciaId="+document.getElementById('txtReferenciaId').value+
                                                                        "&procedencia="+procedencia).load();                            
                    },
                    failure: function(response){
                            console.log("failure");
                            $('#btnCerrarGrabarSeguimiento').removeAttr('disabled');
                            $( "#txtaDetalleSeg" ).prop( "disabled", false );
                    }
            });
        }
        else
        {
            configuraMensajeIngresoFallido('#alertaValidaNuevoSeguimiento',
                                           '<strong>Por favor ingresar Detalle del seguimiento</strong>',
                                           '#btnLoadingGrabarNuevoSeguimiento','#btnGrabarSeguimiento');
        }
    }


    function mostrarConfirmacionCerrarPendiente(id)
    {
        var mensajeAlerta = '<table width="500" align="center" cellspacing="3" cellpadding="3" border="0"><tr>'+
                            '<td><h2><span class="glyphicon glyphicon-warning-sign"></span></h2></td>'+
                            '<td><h4> Esta seguro(a) de cerrar el pendiente?</h4></td></tr></table>';
        $('#divMensajeConfirmarCerrarAsig').html(mensajeAlerta);
        document.getElementById('txtIdPendienteCerrar').value = id;
        $('#modalConfirmarCerrarAsignacion').modal('show');
    }

    function mostrarConfirmacionEnviarListadoPendientes()
    {
        var mensajeAlerta = '<table width="500" align="center" cellspacing="3" cellpadding="3" border="0"><tr>'+
                            '<td><h2><span class="glyphicon glyphicon-warning-sign"></span></h2></td>'+
                            '<td><h4> Esta seguro(a) de enviar el correo de listado de pendientes? </h4></td></tr></table>';
        $('#divMensajeConfirmarListadoPendientes').html(mensajeAlerta);
        $('#modalConfirmarListadoPendientes').modal('show');
    }

    function modificarEstadoDePendiente(estado)
    {
        var idAsignacion = document.getElementById('txtIdPendienteCerrar').value;
        var btnLoading="#btnLoadingCerrarAsignacion";
        var btnConfirma="#confirmaCerrarAsignacion";
        var alerta='#alertaConfirmaCerrarAsignacion';
        var mensajeExito='<strong>Se cerro la asignación con éxito!</strong>';
        var modal='#modalConfirmarCerrarAsignacion';

        if(estado=='ProblemaDeAcceso')
        {
            btnLoading="#btnLoadingProblemasDeAcceso";
            btnConfirma="#confirmaProblemasDeAcceso";
            alerta='#alertaConfirmaProblemasDeAcceso';
            mensajeExito='<strong>Se modifico el pendiente con éxito!</strong>';
            modal='#modalConfirmarProblemasDeAcceso';
        }
        if (idAsignacion !== null )
        {
            var parametros = {
                "intId"     : idAsignacion,
                "strEstado" : estado,
                "strTipo"   : 'estado'
            };
            $.ajax({
                    data :  parametros,
                    url  :  url_modificar_asignacion,
                    type :  'post',
                    beforeSend: function () {
                            $(btnLoading).show();
                            $(btnConfirma).hide();
                    },
                    success:  function (response) {
                            if(response==="OK")
                            {
                                configuraMensajeIngresoConExito(
                                    alerta,
                                    mensajeExito,
                                    btnLoading,
                                    btnConfirma
                                );
    
                                //se cierra ventana luego de 2 segundos
                                 setTimeout(function() {
                                     $(modal).modal('hide');
                                     $(alerta).hide();
                                 }, 2000);
                                 if ( document.getElementById('txtModulo').value == "recorridos" )
                                 {
                                    document.getElementById("lnRecorrido").onclick();
                                 }
                                 else if( document.getElementById('txtModulo').value == "backbone" )
                                 {
                                    document.getElementById("lnBackbone").onclick();
                                 }
                                 else if( document.getElementById('txtModulo').value == "telefonica" )
                                 {
                                    document.getElementById("lnTelefonica").onclick();
                                 }
                                 else if( document.getElementById('txtModulo').value == "municipio" )
                                 {
                                    document.getElementById("lnMunicipio").onclick();
                                 }
                                document.getElementById('txtIdPendienteCerrar').value = null;
                            }
                            else
                            {
                                configuraMensajeIngresoFallido(alerta,
                                                               '<strong>'+response+'</strong>',
                                                               btnLoading,
                                                               btnConfirma);
                            }
                    },
                    failure: function(response){
                        configuraMensajeIngresoFallido(alerta,
                                                       '<strong>'+response+'</strong>',
                                                       btnLoading,
                                                       btnConfirma);
                    }
            });
        }
    }

    function configuraMensajeIngresoConExito(divAlerta,mensajeAlerta,botonLoading,botonGrabar)
    {
        $(divAlerta).removeClass('alert-danger');
        $(divAlerta).removeClass('alert-warning');
        $(divAlerta).addClass('alert-success');
        $(divAlerta).show();
        $(divAlerta).fadeIn();
        $(divAlerta).slideDown();
        $(divAlerta).html(mensajeAlerta);
        $(botonLoading).hide();
        $(botonGrabar).show();
    }

    function configuraMensajeIngresoFallido(divAlerta,mensajeAlerta,botonLoading,botonGrabar)
    {
        $(divAlerta).addClass('alert-danger');
        $(divAlerta).removeClass('alert-success');
        $(divAlerta).removeClass('alert-warning');
        $(divAlerta).show();
        $(divAlerta).fadeIn();
        $(divAlerta).slideDown();
        $(divAlerta).html(mensajeAlerta);
        $(botonLoading).hide();
        $(botonGrabar).show();
    }

    function limpiarCampos(divAlerta,arrayCampos)
    {
        $(divAlerta).hide();
        for(var i = 0; i < arrayCampos.length; i++)
        {
            document.getElementById(arrayCampos[i]).value = "";
        }
    }

    function enviarListadoPendientes()
    {
        var parametros         = "";
        var tabVisible         = "";
        var btnLoading         = '#btnLoadingEnviarListadoPendientes';
        var alerta             = '#alertaConfirmaListadoPendientes';
        var btnConfirmar       = '#confirmaEnviarListadoPendientes';
        var modal              = '#modalConfirmarListadoPendientes';

        if ( document.getElementById('txtModulo').value == "recorridos" )
        {
            tabVisible     = "GestionPendientesRecorridos";
        }
        else if( document.getElementById('txtModulo').value == "backbone" )
        {
            tabVisible     = "GestionPendientesBackbone";
        }
        else if( document.getElementById('txtModulo').value == "telefonica" )
        {
            tabVisible     = "GestionPendientesTelefonica";
        }
        else if( document.getElementById('txtModulo').value == "municipio" )
        {
            tabVisible     = "GestionPendientesMunicipio";
        }

        parametros = {
            "tabVisible":tabVisible,
            "fechaIni" : "",
            "fechaFin" : "",
            "tipoPendiente" : "",
            "estado" : "ABIERTO"
        };
//console.log(parametros);    
        /*if ( fechaInicio != "" && fechaFin != "" && !filtroDefaultLocal)
        {
            parametros = {
                "tabVisible":tabVisible,
                "fechaIni" : fechaInicio,
                "fechaFin" : fechaFin,
                "tipoPendiente" : tipoPendiente,
                "estado" : estado
            };
        }*/

        $.ajax({
                data :  parametros,
                url  :  url_enviar_listado_pendientes,
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
                                '<strong>Se envió correctamente el listado de pendientes con éxito!</strong>',
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