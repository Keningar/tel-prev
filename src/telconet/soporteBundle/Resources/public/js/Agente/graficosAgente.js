var objHighCharts = Highcharts;

//Se declara fecha inicio y fecha fin con la fecha de hoy por defecto
var start         = moment();
var end           = moment();

/**
 * 
 * Permite agregar el rango de fechas al campo de texto del daterangepicker
 * @author Andrés Montero <amontero@telconet.ec>
 * @param start - fecha inicio
 * @param end   - fecha fin
 * @version 1.0 06-11-2018
 * @since 1.0
 */
function cb(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
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

cb(start, end);

$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
  seleccionaFechaGrafica(picker.startDate.format('YYYY/MM/DD'),picker.endDate.format('YYYY/MM/DD'));
});

/**
 * 
 * Función que convierte el mes recibido por parametro de cadena a su número correspondiente
 * @author Andrés Montero <amontero@telconet.ec>
 * @param strMes - Mes en tipo string
 * @version 1.0 12-10-2018
 * @since 1.0
 */
function convertirMesCadenaANumero(strMes)
{
    var arrMeses  = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    var mesNumero = 0;
    for(var i=0;i<arrMeses.length;i++)
    {
        if(arrMeses[i]===strMes)
        {
            mesNumero = i;
        }
    }
    return mesNumero;
}

/**
 *
 * Actualización: Se recibe fecha inicio y fecha fin directamente como parametros
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.3 06-11-2018
 *
 * Actualización: Ahora se puede seleccionar fecha por mes y año recibido en el parametro tipoConsulta
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 12-10-2018
 * 
 * Actualización: presenta según lo seleccionado en el combo de fechas
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 24-09-2018
 *
 * Presenta la información de los graficos según el tipo de consulta calcula las fechas a consultar
 * @author Andrés Montero <amontero@telconet.ec>
 * @param fechaIni - fecha inicio
 * @param fechaFin - fecha fin
 * @version 1.0 24-09-2018
 * @since 1.0
 */
function seleccionaFechaGrafica(fechaIni,fechaFin){
    var fechasParaTitulo = "";
    if (fechaIni === fechaFin)
    {
        fechasParaTitulo = ' del '+fechaIni;
    }
    else
    {
        fechasParaTitulo = ' del '+fechaIni+' al '+fechaFin;
    }
    agentesConAsignaciones(fechaIni,fechaFin,fechasParaTitulo);
}

/**
 * Actualización: Se agrega la fecha en el subtítulo del gráfico
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 07-03-2019
 *
 * Se encarga de crear el gráfico de total de asignaciones por usuario
 * @author Andrés Montero <amontero@telconet.ec>
 * @param json   agentesAsignaciones
 * @param string tiposProblemaUser
 * @param string fechasParaTitulo
 * @version 1.0 24-09-2018
 * @since 1.0
 */
function creaGraficoTotalAsignacionesPorUsuario(agentesAsignaciones, tiposProblemaUser,fechasParaTitulo)
{
        objHighCharts.setOptions({
            lang: {
                drillUpText: '<< Regresar'
            },
            colors: ['#26c6da','#424242', '#50B432', '#ED561B', '#4db6ac', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
        });

        // Create the chart
        objHighCharts.chart('divChartAsignaciones', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Asignaciones por agente'
            },
            subtitle: {
                text: fechasParaTitulo
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text   : '<b>Asignaciones</b>',
                    useHTML: true
                }
            },
            legend: {
                enabled: false
            },

            plotOptions: {
                series          : 
                {
                    borderWidth: 0,
                    dataLabels : {
                        enabled: true
                    }
                }
            },
            series: [{
                name        : 'Asignaciones',
                colorByPoint: true,
                data        : agentesAsignaciones

            }],
            drilldown: {
                drillUpButton: {
                    relativeTo: 'spacingBox',
                    position  : {
                        y: 0,
                        x: 0
                    },
                    theme: {
                        fill          : 'white',
                        'stroke-width': 1,
                        stroke        : 'silver',
                        r             : 0,
                        states: {
                            hover: {
                                fill: '#a4edba'
                            },
                            select: {
                                stroke: '#039',
                                fill  : '#a4edba'
                            }
                        }
                    }
                },
                series: tiposProblemaUser
            }
        });
}

/**
 * Actualización: Se agrega la fecha en el subtítulo del gráfico
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 07-03-2019
 *
 * Se encarga de crear el gráfico de total de seguimientos por usuario
 * @author Andrés Montero <amontero@telconet.ec>
 * @param json   agentesAsignaciones
 * @param string fechasParaTitulo
 * @version 1.0 24-09-2018
 * @since 1.0
 */
function creaGraficoTotalSeguimientosPorUsuario(agentesAsignaciones,fechasParaTitulo)
{
    objHighCharts.setOptions({
        lang: {
            drillUpText: '<< Regresar'
        }
    });

    // Create the chart
    objHighCharts.chart('divChartSeguimientos', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Seguimientos por agente'
        },
        subtitle: {
            text: fechasParaTitulo
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text   : '<b>Seguimientos</b>',
                useHTML: true
            }
        },
        legend: {
            enabled: false
        },

        plotOptions: {
            series         : {
                borderWidth: 0,
                dataLabels : {
                enabled    : true
                }
            }
        },
        series: [{
            name        : 'Seguimientos',
            colorByPoint: true,
            data        : agentesAsignaciones
        }]
    });
}

/**
 * Actualización: Se agrega la fecha en el subtítulo del gráfico
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 07-03-2019
 *
 * Se encarga de crear el gráfico de total de tipos de atención
 * @author Andrés Montero <amontero@telconet.ec>
 * @param json   tiposAtencion
 * @param json   tiposProblema
 * @param string fechasParaTitulo
 * @version 1.0 24-09-2018
 * @since 1.0
 */
function creaGraficoTotalTipoAtencion(tiposAtencion, tiposProblema,fechasParaTitulo)
{
    objHighCharts.setOptions({
        lang: {
            drillUpText: '<< Regresar'
        }
    });

    // Create the chart
    objHighCharts.chart('divChartTipoAtencion', {
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Asignaciones por tipo atención '
        },
        subtitle: {
            text: fechasParaTitulo
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text   : '<b>Cantidad</b>',
                useHTML: true
            }
        },
        legend: {
            enabled: false
        },

        plotOptions: {
            series: {
                borderWidth: 0,
                innerSize  : '50%',
                dataLabels : {
                    enabled  : true,
                    formatter: function (){
                        return this.y ? '<b>' + this.point.name + ':</b> ' + this.y + '' : null;
                    }
                }
            }
        },

        series: [{
            type        : 'pie',
            innerSize   : '50%',
            name        : 'tiposAtencion',
            colorByPoint: true,
            data        : tiposAtencion

        }],
        drilldown: {
            drillUpButton: {
                relativeTo: 'spacingBox',
                position: {
                    y: 0,
                    x: 0
                },
                theme: {
                    fill          : 'white',
                    'stroke-width': 1,
                    stroke        : 'silver',
                    r             : 0,
                    states: {
                        hover: {
                            fill: '#a4edba'
                        },
                        select: {
                            stroke: '#039',
                            fill  : '#a4edba'
                        }
                    }
                }
            },
            series: tiposProblema
        }
    });
}



/**
 * Actualización: Se agrega la fecha en el subtítulo del gráfico
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 07-03-2019
 *
 * Se encarga de crear el gráfico de total de tipos de problema
 * @author Andrés Montero <amontero@telconet.ec>
 * @param json   tiposProblema
 * @param json   usuariosTipoProblema
 * @param string fechasParaTitulo
 * @version 1.0 24-09-2018
 * @since 1.0
 */
function creaGraficoTotalTipoProblema(tiposProblema, usuariosTipoProblema,fechasParaTitulo)
{
    objHighCharts.setOptions({
        lang: {
            drillUpText: '<< Regresar'
        }
    });

    // Create the chart
    objHighCharts.chart('divChartTipoProblema', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Asignaciones por tipo problema '
        },
        subtitle: {
            text: fechasParaTitulo
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text   : '<b>Asignaciones</b>',
                useHTML: true
            }
        },
        legend: {
            enabled: false
        },

        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels : {
                    enabled: true
                }
            }
        },

        series: [{
            name        : 'tiposProblema',
            colorByPoint: true,
            data        : tiposProblema
        }],
        drilldown: {
            drillUpButton: {
                relativeTo: 'spacingBox',
                position  : {
                    y: 0,
                    x: 0
                },
                theme: {
                    fill          : 'white',
                    'stroke-width': 1,
                    stroke        : 'silver',
                    r             : 0,
                    states: {
                        hover: {
                            fill: '#a4edba'
                        },
                        select: {
                            stroke: '#039',
                            fill: '#a4edba'
                        }
                    }
                }
            },
            series: usuariosTipoProblema
        }
    });
}


/**
 * Actualización: Se agrega la fecha en el subtítulo del gráfico
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 07-03-2019
 *
 * Se encarga de crear el gráfico de total de asignaciones por origen
 * @author Andrés Montero <amontero@telconet.ec>
 * @param json   origenes         => El grupo de origenes que se van a graficar
 * @param json   tiposProblema    => Tipos de problema que se grafican en el drilldown
 * @param string fechasParaTitulo => Fechas que se van a presentar en el título del gráfico
 * @version 1.0 05-02-2019
 * @since 1.0
 */
function creaGraficoTotalOrigen(origenes, tiposProblema,fechasParaTitulo)
{
    objHighCharts.setOptions(
    {
        lang:
        {
            drillUpText: '<< Regresar'
        }
    });

    // Create the chart
    objHighCharts.chart('divChartOrigen', 
    {
        chart:
        {
            type: 'column'
        },
        title:
        {
            text: 'Asignaciones por origen '
        },
        subtitle: {
            text: fechasParaTitulo
        },
        xAxis:
        {
            type: 'category'
        },
        yAxis:
        {
            title:
            {
                text   : '<b>Asignaciones</b>',
                useHTML: true
            }
        },
        legend:
        {
            enabled: false
        },
        plotOptions:
        {
            series :
            {
                borderWidth: 0,
                dataLabels : 
                {
                    enabled: true
                }
            }
        },
        series:
        [
            {
                name        : 'Asignaciones',
                colorByPoint: true,
                data        : origenes
            }
        ],
        drilldown:
        {
            drillUpButton:
            {
                relativeTo: 'spacingBox',
                position  :
                {
                    y: 0,
                    x: 0
                },
                theme:
                {
                    fill          : 'white',
                    'stroke-width': 1,
                    stroke        : 'silver',
                    r             : 0,
                    states:
                    {
                        hover:
                        {
                            fill: '#a4edba'
                        },
                        select:
                        {
                            stroke: '#039',
                            fill  : '#a4edba'
                        }
                    }
                }
            },
            series: tiposProblema
        }
    });
}

/**
 * Se encarga de crear el gráfico de top de logins con mas tareas
 * @author Andrés Montero <amontero@telconet.ec>
 * @param json   agentesAsignaciones
 * @param string fechasParaTitulo
 * @version 1.0 27-02-2019
 * @since 1.0
 */
function creaGraficoTopLogins(data,tipo,fechasParaTitulo)
{
    objHighCharts.chart('divChartTopLogins'+tipo, {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Top 10 de logins con más '+tipo
        },
        subtitle: {
            text: fechasParaTitulo
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Cantidad'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.1,
                borderWidth: 1
            },
            series :
            {
                borderWidth: 0,
                dataLabels : 
                {
                    enabled: true
                }
            }
        },
        series: [
            data
        ]
    });
}

/**
 * Actualización: Se agrega la fecha en el subtítulo del gráfico
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 07-03-2019
 *
 * Se encarga de crear el gráfico de total de asignaciones por origen
 * @author Andrés Montero <amontero@telconet.ec>
 * @param json   origenes         => El grupo de origenes que se van a graficar
 * @param json   tiposProblema    => Tipos de problema que se grafican en el drilldown
 * @param string fechasParaTitulo => Fechas que se van a presentar en el título del gráfico
 * @version 1.0 05-02-2019
 * @since 1.0
 */
function creaGraficoTotalCiudad(origenes, ciudad,fechasParaTitulo)
{   
    objHighCharts.setOptions(
    {
        lang:
        {
            drillUpText: '<< Regresar'
        }
    });

    // Create the chart
    objHighCharts.chart('divChartCiudad', 
    {
        chart:
        {
            type: 'column'
        },
        title:
        {
            text: 'Total Asignaciones '
        },
        subtitle: {
            text: fechasParaTitulo
        },
        xAxis:
        {
            type: 'category'
        },
        yAxis:
        {
            title:
            {
                text   : '<b>Asignaciones</b>',
                useHTML: true
            }
        },
        legend:
        {
            enabled: false
        },
        plotOptions:
        {
            series :
            {
                borderWidth: 0,
                dataLabels : 
                {
                    enabled: true
                }
            }
        },
        series:
        [
            {
                name        : 'Asignaciones',
                colorByPoint: true,
                data        : origenes
            }
        ],
        drilldown:
        {
            drillUpButton:
            {
                relativeTo: 'spacingBox',
                position  :
                {
                    y: 0,
                    x: 0
                },
                theme:
                {
                    fill          : 'white',
                    'stroke-width': 1,
                    stroke        : 'silver',
                    r             : 0,
                    states:
                    {
                        hover:
                        {
                            fill: '#a4edba'
                        },
                        select:
                        {
                            stroke: '#039',
                            fill  : '#a4edba'
                        }
                    }
                }
            },
            series: ciudad
        }
    });
}

/**
 * Actualización: Se agrega enviar al ajax parametro idCanton para realizar consulta por ciudad
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 22-04-2020
 * 
 * Se envia parametro totalizadoPor con USR_ASIGNADO en lugar de USUARIO
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 05-12-2018
 * 
 * Se encarga de obtener información de agentes con asignaciones
 * @author Andrés Montero <amontero@telconet.ec>
 * @param string   fechaIni
 * @param string   fechaFin
 * @param string fechasParaTitulo
 * @version 1.0 24-09-2018
 * @since 1.0
 */
function agentesConAsignaciones(fechaIni, fechaFin, fechasParaTitulo)
{
    var idCanton   = document.getElementById('txtIdBtnCantonesEstadisticas').value;
    var estado     = document.getElementById('txtIdBtnEstadosEstadisticas').value;
    var parametros = {
        "totalizadoPor": "USR_ASIGNADO",
        "fechaIni"     : fechaIni,
        "fechaFin"     : fechaFin,
        "idCanton"     : idCanton,
        "estado"       : estado
    };
    $("#export").attr("disabled", true);
    $.ajax({
        url    : url_total_asignaciones,
        type   : 'post',
        data   : parametros,
        complete: function(){
            seguimientosPorAgente(fechaIni,fechaFin,fechasParaTitulo);
        },
        success: function (response) {
            creaGraficoTotalAsignacionesPorUsuario(response.data, response.dataUser,fechasParaTitulo);
        },
        failure: function(response){
            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                     "<strong>"+
                                                         "Ocurrio un error, "+
                                                         "No se pudo leer información del gráfico de asignaciones por agente"+
                                                     "</strong>");
        }
    });
}


/**
 * Actualización: Se agrega enviar al ajax parametro idCanton para realizar consulta por ciudad
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 22-04-2020
 * 
 * Se encarga de obtener información de seguimientos por agente
 * @author Andrés Montero <amontero@telconet.ec>
 * @param string   fechaIni
 * @param string   fechaFin
 * @param string fechasParaTitulo
 * @version 1.0 24-09-2018
 * @since 1.0
 */
function seguimientosPorAgente(fechaIni, fechaFin,fechasParaTitulo)
{
    var idCanton   = document.getElementById('txtIdBtnCantonesEstadisticas').value;
    var estado     = document.getElementById('txtIdBtnEstadosEstadisticas').value;
    var parametros = {
        "fechaIni": fechaIni,
        "fechaFin": fechaFin,
        "idCanton": idCanton,
        "estado"  : estado
    };
    $.ajax({
        url    : url_total_seguimientos_usr,
        type   : 'post',
        data   : parametros,
        complete: function(){
            topLogins(fechaIni,fechaFin,fechasParaTitulo);
        },
        success: function (response) {
            creaGraficoTotalSeguimientosPorUsuario(response.data,fechasParaTitulo);
        },
        failure: function(response){
            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                     "<strong>"+
                                                         "Ocurrio un error, "+
                                                         "No se pudo leer información de seguimientos por agente"+
                                                     "</strong>");
        }
    });
}

/**
 * Actualización: Se agrega enviar al ajax parametro idCanton para realizar consulta por ciudad
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 22-04-2020
 * 
 * Actualización: Se agrega código de programación para que cuando termine el 
 *                gráfico de asignaciones por tipos de problema
 *                se empiece a graficar el gráfico de asignaciones por origen
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 05-02-2019
 * 
 * Se encarga de obtener información de tipos de problema con asignaciones
 * @author Andrés Montero <amontero@telconet.ec>
 * @param string   fechaIni
 * @param string   fechaFin
 * @param string fechasParaTitulo
 * @version 1.0 24-09-2018
 * @since 1.0
 */
function tiposProblemaConAsignaciones(fechaIni, fechaFin,fechasParaTitulo)
{
    var idCanton   = document.getElementById('txtIdBtnCantonesEstadisticas').value;
    var estado     = document.getElementById('txtIdBtnEstadosEstadisticas').value;
    var parametros = {
        "totalizadoPor": "TIPO_PROBLEMA",
        "fechaIni"     :fechaIni,
        "fechaFin"     :fechaFin,
        "idCanton"     : idCanton,
        "estado"       : estado
    };
    $.ajax({
        url     : url_total_asignaciones,
        type    : 'post',
        data    : parametros,
        complete: function(){
            origenConAsignaciones(fechaIni,fechaFin,fechasParaTitulo);
        },
        success : function (response) {
            creaGraficoTotalTipoProblema(response.data, response.dataUser,fechasParaTitulo);
        },
        failure : function(response){
            $('#modalAlertaError').modal('show');
            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                     "<strong>"+
                                                         "Ocurrio un error, "+
                                                         "No se pudo leer información del gráfico de asignaciones por tipos de problema"+
                                                     "</strong>");
        }
    });
}

/**
 * Actualización: Se agrega enviar al ajax parametro idCanton para realizar consulta por ciudad
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 22-04-2020
 * 
 * Se encarga de obtener información de tipos de atención con asignaciones
 * @author Andrés Montero <amontero@telconet.ec>
 * @param string   fechaIni
 * @param string   fechaFin
 * @param string fechasParaTitulo
 * @version 1.0 24-09-2018
 * @since 1.0
 */
function tiposAtencionConAsignaciones(fechaIni, fechaFin,fechasParaTitulo)
{
    var idCanton   = document.getElementById('txtIdBtnCantonesEstadisticas').value;
    var estado     = document.getElementById('txtIdBtnEstadosEstadisticas').value;
    var parametros = {
        "totalizadoPor": "TIPO_ATENCION",
        "fechaIni"     :fechaIni,
        "fechaFin"     :fechaFin,
        "idCanton"     :idCanton,
        "estado"       : estado
    };
    $.ajax({
        url : url_total_asignaciones,
        type: 'post',
        data: parametros,
        complete: function(){
            tiposProblemaConAsignaciones(fechaIni,fechaFin,fechasParaTitulo);
        },
        success:  function (response) {
            creaGraficoTotalTipoAtencion(response.data, response.dataUser,fechasParaTitulo);
        },
        failure: function(response){
            $('#modalAlertaError').modal('show');
            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                     "<strong>"+
                                                         "Ocurrio un error, "+
                                                         "No se pudo leer información del gráfico de asignaciones por tipos de atención"+
                                                     "</strong>");
        }
    });
}


/**
 * Actualización: Se agrega enviar al ajax parametro idCanton para realizar consulta por ciudad
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 22-04-2020
 * 
 * Se encarga de obtener información de origenes con asignaciones
 * @author Andrés Montero <amontero@telconet.ec>
 * @param string   fechaIni       => Fecha inicio para consultar los datos
 * @param string   fechaFin       => Fecha fin para consultar los datos
 * @param string fechasParaTitulo => Fechas que se presentan en el título del gráfico
 * @version 1.0 05-02-2019
 * @since 1.0
 */
function origenConAsignaciones(fechaIni, fechaFin,fechasParaTitulo)
{
    var idCanton   = document.getElementById('txtIdBtnCantonesEstadisticas').value;
    var estado     = document.getElementById('txtIdBtnEstadosEstadisticas').value;
    var parametros = {
        "totalizadoPor": "ORIGEN",
        "fechaIni"     :fechaIni,
        "fechaFin"     :fechaFin,
        "idCanton"     :idCanton,
        "estado"       : estado
    };
    $.ajax({
        url : url_total_asignaciones,
        type: 'post',
        data: parametros,
        complete: function()
        {
            ciudadConAsignaciones(fechaIni, fechaFin,fechasParaTitulo);
        },
        success:  function (response)
        {
            creaGraficoTotalOrigen(response.data, response.dataUser,fechasParaTitulo);
        },
        failure: function(response)
        {
            $('#modalAlertaError').modal('show');
            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                     "<strong>"+
                                                         "Ocurrio un error, "+
                                                         "No se pudo leer información del gráfico de asignaciones por Origen"+
                                                     "</strong>");
        }
    });
}



/**
 * Actualización: Se agrega enviar al ajax parametro idCanton para realizar consulta por ciudad
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 22-04-2020
 * 
 * Se encarga de obtener el top de los 10 logins con mas tareas y casos
 * @author Andrés Montero <amontero@telconet.ec>
 * @param string   fechaIni       => Fecha inicio para consultar los datos
 * @param string   fechaFin       => Fecha fin para consultar los datos
 * @param string fechasParaTitulo => Fechas que se presentan en el título del gráfico
 * @version 1.0 27-02-2019
 * @since 1.0
 */
function topLogins(fechaIni, fechaFin, fechasParaTitulo)
{
    var idCanton   = document.getElementById('txtIdBtnCantonesEstadisticas').value;
    var estado     = document.getElementById('txtIdBtnEstadosEstadisticas').value;
    var parametros = {
        "fechaIni"     :fechaIni,
        "fechaFin"     :fechaFin,
        "idCanton"     : idCanton,
        "estado"       : estado
    };
    $.ajax({
        url : url_top_logins,
        type: 'post',
        data: parametros,
        complete: function()
        {
            tiposAtencionConAsignaciones(fechaIni, fechaFin,fechasParaTitulo);
            
        },
        success:  function (response)
        {
            var tareas = response[0];
            creaGraficoTopLogins(tareas, 'Tareas', fechasParaTitulo);

            var casos = response[1];            
            creaGraficoTopLogins(casos, 'Casos', fechasParaTitulo);

        },
        failure: function(response)
        {
            $('#modalAlertaError').modal('show');
            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                     "<strong>"+
                                                         "Ocurrio un error, "+
                                                         "No se pudo leer información del gráfico de top de logins"+
                                                     "</strong>");
        }
    });
}

/**
 * Actualización: Se agrega enviar al ajax parametro idCanton para realizar consulta por ciudad
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 22-04-2020
 * 
 * Se encarga de obtener información de origenes con asignaciones
 * @author Andrés Montero <amontero@telconet.ec>
 * @param string   fechaIni       => Fecha inicio para consultar los datos
 * @param string   fechaFin       => Fecha fin para consultar los datos
 * @param string fechasParaTitulo => Fechas que se presentan en el título del gráfico
 * @version 1.0 05-02-2019
 * @since 1.0
 */
function ciudadConAsignaciones(fechaIni, fechaFin,fechasParaTitulo)
{
    var idCanton   = document.getElementById('txtIdBtnCantonesEstadisticas').value;
    var estado     = document.getElementById('txtIdBtnEstadosEstadisticas').value;
    var parametros = {
        "totalizadoPor": "TODAS",
        "fechaIni"     :fechaIni,
        "fechaFin"     :fechaFin,
        "idCanton"     :idCanton,
        "estado"       : estado
    };
    $.ajax({
        url : url_total_asignaciones,
        type: 'post',
        data: parametros,
        complete: function()
        {
            $("#export").attr("disabled", false);
        },
        success:  function (response)
        {
            creaGraficoTotalCiudad(response.data, response.dataUser,fechasParaTitulo);
        },
        failure: function(response)
        {
            $('#modalAlertaError').modal('show');
            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                     "<strong>"+
                                                         "Ocurrio un error, "+
                                                         "No se pudo leer información del gráfico de asignaciones por Origen"+
                                                     "</strong>");
        }
    });
}

$('#export').click(function() {
    var imgChartAsignaciones = new Image();
    imgChartAsignaciones.setAttribute('src', 
                                      'data:image/svg+xml;base64,' + 
                                      btoa(unescape(encodeURIComponent($('#divChartAsignaciones').highcharts().getSVG()))));
    imgChartAsignaciones.onload = function() {
        var canvaChartAsignaciones = document.getElementById("canvaChartAsignaciones");
        var canvaChartAsignacionesCtx  = canvaChartAsignaciones.getContext('2d');
        canvaChartAsignacionesCtx.drawImage(imgChartAsignaciones, 0, 0);
        chartAsignaciones = canvaChartAsignaciones.toDataURL("image/png");
        var imgChartSeguimientos = new Image();
        imgChartSeguimientos.setAttribute('src', 
                                          'data:image/svg+xml;base64,' + 
                                          btoa(unescape(encodeURIComponent($('#divChartSeguimientos').highcharts().getSVG()))));
        imgChartSeguimientos.onload = function() {
            var canvaChartSeguimientos = document.getElementById("canvaChartSeguimientos");
            var canvaChartSeguimientosCtx = canvaChartSeguimientos.getContext('2d');
            canvaChartSeguimientosCtx.drawImage(imgChartSeguimientos, 0, 0);
            chartSeguimientos = canvaChartSeguimientos.toDataURL("image/png");
            var imgChartTopLoginsTareas = new Image();
            imgChartTopLoginsTareas.setAttribute('src', 
                                                 'data:image/svg+xml;base64,' + 
                                                 btoa(unescape(encodeURIComponent($('#divChartTopLoginsTareas').highcharts().getSVG()))));
            imgChartTopLoginsTareas.onload = function() {
                var canvaChartTopLoginsTareas = document.getElementById("canvaChartTopLoginsTareas");
                var canvaChartTopLoginsTareasCtx = canvaChartTopLoginsTareas.getContext('2d');
                canvaChartTopLoginsTareasCtx.drawImage(imgChartTopLoginsTareas, 0, 0);
                chartTopLoginsTareas = canvaChartTopLoginsTareas.toDataURL("image/png");
                var imgChartTopLoginsCasos = new Image();
                imgChartTopLoginsCasos.setAttribute('src', 
                                                    'data:image/svg+xml;base64,' + 
                                                    btoa(unescape(encodeURIComponent($('#divChartTopLoginsCasos').highcharts().getSVG()))));
                imgChartTopLoginsCasos.onload = function() {
                    var canvaChartTopLoginsCasos = document.getElementById("canvaChartTopLoginsCasos");
                    var canvaChartTopLoginsCasosCtx  = canvaChartTopLoginsCasos.getContext('2d');
                    canvaChartTopLoginsCasosCtx.drawImage(imgChartTopLoginsCasos, 0, 0);
                    chartTopLoginsCasos = canvaChartTopLoginsCasos.toDataURL("image/png");
                    var imgChartTipoAtencion = new Image();
                    imgChartTipoAtencion.setAttribute('src', 
                                                      'data:image/svg+xml;base64,' + 
                                                      btoa(unescape(encodeURIComponent($('#divChartTipoAtencion').highcharts().getSVG()))));
                    imgChartTipoAtencion.onload = function() {
                        var canvaChartTipoAtencion = document.getElementById("canvaChartTipoAtencion");
                        var canvaChartTipoAtencionCtx  = canvaChartTipoAtencion.getContext('2d');
                        canvaChartTipoAtencionCtx.drawImage(imgChartTipoAtencion, 0, 0);
                        chartTipoAtencion = canvaChartTipoAtencion.toDataURL("image/png");
                        var imgChartTipoProblema = new Image();
                        imgChartTipoProblema.setAttribute('src', 
                                                          'data:image/svg+xml;base64,' + 
                                                          btoa(unescape(encodeURIComponent($('#divChartTipoProblema').highcharts().getSVG()))));
                        imgChartTipoProblema.onload = function() {
                            var canvaChartTipoProblema = document.getElementById("canvaChartTipoProblema");
                            var canvaChartTipoProblemaCtx  = canvaChartTipoProblema.getContext('2d');
                            canvaChartTipoProblemaCtx.drawImage(imgChartTipoProblema, 0, 0);
                            chartTipoProblema = canvaChartTipoProblema.toDataURL("image/png");
                            var imgChartOrigen = new Image();
                            imgChartOrigen.setAttribute('src', 
                                                        'data:image/svg+xml;base64,' + 
                                                        btoa(unescape(encodeURIComponent($('#divChartOrigen').highcharts().getSVG()))));
                            imgChartOrigen.onload = function() {
                                var canvaChartOrigen = document.getElementById("canvaChartOrigen");
                                var canvaChartOrigenCtx  = canvaChartOrigen.getContext('2d');
                                canvaChartOrigenCtx.drawImage(imgChartOrigen, 0, 0);
                                chartOrigen = canvaChartOrigen.toDataURL("image/png");
                                var arrayGraficos = [
                                                     chartAsignaciones, 
                                                     chartSeguimientos, 
                                                     chartTopLoginsTareas, 
                                                     chartTopLoginsCasos,
                                                     chartTipoAtencion,
                                                     chartTipoProblema,
                                                     chartOrigen
                                                    ] ; 
                                build_pdf(arrayGraficos);
                            }
                        }
                    }
                }
            }
        }
    }


  });
  
  function build_pdf(arrayGraficos) {
      var docDefinition = {
        content: [
          {text: 'Reporte de estadísticas del módulo Agente', style:'header' },
          {text: ' ' },
          {text: ' ' },
          { image: arrayGraficos[0], width: 700, height:350, margin: [ 15, 1, 1, 1 ]  },
          { image: arrayGraficos[1], width: 700, height:350, margin: [ 15, 1, 1, 1 ] },
          {text: ' ' },
          {text: ' ' },
          {text: ' ' },
          {
            columns: [  
              [
                  { image: arrayGraficos[2], width: 310, height: 350, margin: [ 1, 1, 1, 1 ]},
                  { image: arrayGraficos[4], width: 310, height: 350, margin: [ 1, 1, 1, 1 ] }
              ],
              [
                { image: arrayGraficos[3], width: 310, height: 350, margin: [ 1, 1, 1, 1 ] },
                { image: arrayGraficos[5], width: 310, height: 350, margin: [ 1, 1, 1, 1 ] }
              ]
            ]
          },
          { image: arrayGraficos[6], width:300, height: 350 }
        ],
        styles: {
          header: {
            fontSize: 18,
            bold: true,
            alignment:'center'
          },
          anotherStyle: {
            italics: true,
            alignment: 'left'
          }
        }
      };
      
      pdfMake.createPdf(docDefinition).download('reporteEstadisticasAgente.pdf');
  }