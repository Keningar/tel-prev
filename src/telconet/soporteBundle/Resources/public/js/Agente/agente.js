var cuadroAsignaciones;
var detalleAsignaciones;
var detalleAsignacionesProactivas;
var detalleAsignacionesHijas;
var arrayAsignacionProactiva  =[];
var cuadroAsignacionesUsr;
var agentesEditadosCambioTurno=[];
var agentes=[];
var agentesCambioTurno=[];
var detalleSeguimientos;
var detalleUsuarios;
var detalleTareas;
var fechaIni;
var fechaFin;
var fechaInicioVal;
var fechaFinVal;
var fechaAux;
var dia; 
var mes;
var annio;
var readyForDrawDetalleSeg        = false;
var ordenAgentesCuadroGen         = "";
var muestraBtnEliminarSeguimiento = "";
var objAsignacionActualizar = "";

$(function () {
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    $('#dpPonerStandby').datetimepicker({
        format: 'DD/MM/YYYY',
        minDate: today
    });

    $('#datetimepickerEjecReasigna').datetimepicker({
        format: 'YYYY-MM-DD',
        minDate: today
    });
});

navigator.sayswho= (function(){
    var ua= navigator.userAgent, tem, 
    M= ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
    if(/trident/i.test(M[1])){
        tem=  /\brv[ :]+(\d+)/g.exec(ua) || [];
        return 'IE '+(tem[1] || '');
    }
    if(M[1]=== 'Chrome'){
        tem= ua.match(/\b(OPR|Edge)\/(\d+)/);
        if(tem!= null) return tem.slice(1).join(' ').replace('OPR', 'Opera');
    }
    M= M[2]? [M[1],'versión', M[2]]: [navigator.appName,'versión', navigator.appVersion, '-?'];
    if((tem= ua.match(/version\/(\d+)/i))!= null) M.splice(1, 1, tem[1]);
    return M.join(' ');
})();


if ('serviceWorker' in navigator) {
    var firebaseConfig   = {
                            apiKey: fcmApiKey,
                            authDomain: fcmAuthDomain,
                            databaseURL: fcmDatabaseUrl,
                            projectId: fcmProyectId,
                            storageBucket: fcmStorageBucket,
                            messagingSenderId: fcmMessagingSenderId,
                            appId: fcmAppId,
                            measurementId: fcmMeasurementId
                           };
    var mensajeErrorDefaultNotificaciones = 'No se puede recibir notificaciones en este sitio por un error en su navegador';
    if (typeof firebase !== 'undefined') 
    {
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
    }

    if (typeof firebase !== 'undefined' && firebase.messaging.isSupported())
    {
        // Retrieve Firebase Messaging object.
        const messaging = firebase.messaging();

        messaging.usePublicVapidKey(publicVapidKey);

        navigator.serviceWorker.getRegistrations().then(function(registrations) 
        {
            for(let registration of registrations)
            {
                registration.unregister();
            }

            navigator.serviceWorker.register(url_service_worker)
            .then((registration) => {
                console.log("Se registra Service worker");
                messaging.useServiceWorker(registration);
                Notification.requestPermission().then((permission) => {
                    if (permission === 'granted') 
                    {
                        console.log("Permite recibir notificaciones push");
                        messaging.getToken().then((currentToken) => {
                            if (currentToken) 
                            {
                                var parametros = 
                                {
                                    "strValor"     : currentToken,
                                    "strCarac"     : "SUBSCRIPCION_NOTIFICA_AGENTE",
                                };
                                $.ajax({
                                    data :  parametros,
                                    url  :  url_actualiza_caracteristica_agent,
                                    type :  'POST'
                                });
                            } 
                        }).catch((err) => {
                            console.log('An error occurred while retrieving token. ', err);
                            mostrarMensajeOtrosErroresNavegador(mensajeErrorDefaultNotificaciones);
                        });
                    }
                    else
                    {
                        console.log("No tiene permisos de notificaciones");
                        mostrarMensajeErrorGeneralNavegador('Para recibir alertas en el módulo agente debe dar '+
                                                            'permisos de notificaciones a este sitio.');
                    }
                });
            }, function(err)
            {
                console.log("Error al registrar service worker:",err);
                mostrarMensajeOtrosErroresNavegador(mensajeErrorDefaultNotificaciones);
            });

        }, function(err)
        {
                console.log("Error al deregistrar service worker:",err);
                mostrarMensajeOtrosErroresNavegador(mensajeErrorDefaultNotificaciones);
        });
        // Callback fired if Instance ID token is updated.
        messaging.onTokenRefresh(() => {
            messaging.getToken().then((refreshedToken) => {
                var parametros = {
                    "strValor"     : refreshedToken,
                    "strCarac"     : "SUBSCRIPCION_NOTIFICA_AGENTE",
                };
                $.ajax({
                    data :  parametros,
                    url  :  url_actualiza_caracteristica_agent,
                    type :  'POST'
                });
            }).catch((err) => {
            console.log('Unable to retrieve refreshed token ', err);
            });
        });
    }
    else
    {
        console.log("La versión de su navegador no es compatible para recibir notificaciones. "+navigator.sayswho);
        mostrarMensajeActualizarNavegador(navigator.sayswho);
    }
}
else
{
    urlDownloadNavigator   = obtenerUrlActualizarNavegador(navigator.sayswho);
    mensajeUpdateNavigator = "";
    if (urlDownloadNavigator != "")
    {
        mensajeUpdateNavigator = "<br>Se sugiere actualizar su navegador, para actualizar puede ingresar en el siguiente link : "+
                                 urlDownloadNavigator;
    }
    else
    {
        mensajeUpdateNavigator = "<br>Se sugiere utilizar Firefox para el correcto funcionamiento de este sitio, "+
                                 "para descargarlo puede ingresar en el siguiente link : "+
                                 "<a href='https://www.mozilla.org/es-ES/firefox/new/' class='alert-link' target='_blank' >Descargar Firefox</a>";
    }
    mostrarMensajeErrorGeneralNavegador('La versión de su navegador ('+navigator.sayswho+
                                        ') no es compatible para recibir notificaciones o es posible que el sitio no tenga https valido.'+
                                        mensajeUpdateNavigator);
}

function mostrarMensajeActualizarNavegador(navegador)
{
    var mensajeUpdateNavigator  = "";
    var supportVersionNavigator = "";
    var urlDownloadNavigator    = obtenerUrlActualizarNavegador(navegador);
    if (urlDownloadNavigator != "")
    {
        mensajeUpdateNavigator = supportVersionNavigator+"<br> Se sugiere actualizar su navegador, "+
                                                         "para actualizar puede ingresar en el siguiente link : "+urlDownloadNavigator;
    }
    else
    {
        mensajeUpdateNavigator = "<br>Se sugiere utilizar Firefox para el correcto funcionamiento de este sitio, "+
                                 "para descargarlo puede ingresar en el siguiente link : "+
                                 "<a href='https://www.mozilla.org/es-ES/firefox/new/' class='alert-link' target='_blank' >Descargar Firefox</a>";
    }
    $("#alertaErroresAplicacion").html('<strong>La versión de su navegador ('+navigator.sayswho+
    ') no es compatible para recibir notificaciones.'+mensajeUpdateNavigator+"</strong>"+
    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
    '<span aria-hidden="true">&times;</span></button>');

    $("#alertaErroresAplicacion").show();
    setTimeout(function(){$("#alertaErroresAplicacion").hide();}, 60000);
}

function mostrarMensajeErrorGeneralNavegador(mensaje)
{
    $("#alertaErroresAplicacion").html("<strong>"+mensaje+"</strong>"+
    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
    '<span aria-hidden="true">&times;</span></button>');
    $("#alertaErroresAplicacion").show();
    setTimeout(function(){$("#alertaErroresAplicacion").hide();}, 60000);
}

function mostrarMensajeOtrosErroresNavegador(mensajeErrorDefaultNotificaciones)
{
    if (navigator.sayswho.toUpperCase().indexOf('FIREFOX') < 0)
    {
        mensajeErrorDefaultNotificaciones = mensajeErrorDefaultNotificaciones+"<br>Se recomienda usar Firefox para el correcto"+
                                                                              " funcionamiento de este sitio, "+
                                                                              "para descargarlo puede ingresar en el siguiente link : "+
                                                                              "<a href='https://www.mozilla.org/es-ES/firefox/new/' "+
                                                                              " class='alert-link' target='_blank' >Descargar Firefox</a>";

    }
    else
    {
        mensajeErrorDefaultNotificaciones = mensajeErrorDefaultNotificaciones+
                                            ", refrescar la página y volver a intentar. "+
                                            "Si el problema persiste comunicarse con sistemas.";
    }
    mostrarMensajeErrorGeneralNavegador(mensajeErrorDefaultNotificaciones);
}

function obtenerUrlActualizarNavegador(navegador)
{
    var urlDownloadNavigator    = "";
    if (navegador.toUpperCase().indexOf('FIREFOX') >= 0)
    {
        urlDownloadNavigator    = "<a href='https://www.mozilla.org/es-ES/firefox/new/' class='alert-link' target='_blank' >Actualizar Firefox</a>";
    }
    return urlDownloadNavigator;
}

var alarmaAsignacion = new Howl({
                                 src: [url_sound_notif_asig],
                                 autoplay: false,
                                 loop: false,
                                 volume: 0.1
                                });

var alarmaSeguimiento = new Howl({
                                  src: [url_sound_notif_seg],
                                  autoplay: false,
                                  loop: false,
                                  volume: 0.1
                                 });



    /**
     * Actualización: Se agrega programación para grabar cambios en el campo tipo problema
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 20-02-2019
     * 
     * Graba cambios en campos de nombre reporta, nombre sitio y dato adicional de la asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-11-2018
     * @since 1.0
     */
    function grabarCambiosEnAsignacion(tipo, valor)
    {
        var id    = document.getElementById('idAsignacionInfo').value;
        //VALIDA CAMPOS VACIOS
        var parametros = {
            "strValor"       : valor,
            "intId"          : id,
            "strTipo"        : tipo
        };
        $.ajax({
            data :  parametros,
            url  :  url_modificar_asignacion,
            type :  'post',
            beforeSend: function () {
                        if (tipo === 'nombreReporta'){
                            $('#btnLoadingEditarNombreReporta').show();
                            $('#btnGrabarEditarNombreReporta').hide();
                        }
                        else if (tipo === 'nombreSitio'){
                            $('#btnLoadingEditarNombreSitio').show();
                            $('#btnGrabarEditarNombreSitio').hide();
                        }
                        else if (tipo === 'datoAdicional'){
                            $('#btnLoadingEditarDatoAdicional').show();
                            $('#btnGrabarEditarDatoAdicional').hide();
                        }
                        else if (tipo === 'tipoProblema'){
                            $('#btnLoadingEditarTipoProblema').show();
                            $('#btnGrabarEditarTipoProblema').hide();
                        }
            },
            success:  function (response) {
                    if(response==="OK")
                    {
                        $("#divMensajeAlertaError").html("<img src='/public/images/images_crud/check48.png' width='50' height='50' />"+
                                                         "<strong>"+" Se ingresaron datos con éxito!"+"</strong>");
                        $('#modalAlertaError').modal('show');
                        if (tipo === 'nombreReporta'){
                            $('#btnLoadingEditarNombreReporta').hide();
                            $('#btnGrabarEditarNombreReporta').show();
                            $('#labelNombreReportaInfo').html(valor);
                            presentarLabelNombreReporta();
                        }
                        else if (tipo === 'nombreSitio'){
                            $('#btnLoadingEditarNombreSitio').hide();
                            $('#btnGrabarEditarNombreSitio').show();
                            $('#labelNombreSitioInfo').html(valor);
                            presentarLabelNombreSitio();
                        }
                        else if (tipo === 'datoAdicional'){
                            $('#btnLoadingEditarDatoAdicional').hide();
                            $('#btnGrabarEditarDatoAdicional').show();
                            $('#labelDatoAdicionalInfo').html(valor);
                            presentarLabelDatoAdicional();
                        }
                        else if (tipo === 'tipoProblema'){
                            $('#btnLoadingEditarTipoProblema').hide();
                            $('#btnGrabarEditarTipoProblema').show();
                            $('#labelTipoProblemaInfo').html(valor);
                            presentarLabelTipoProblema();
                        }
                    }
                    else
                    {
                        $("#divMensajeAlertaError").html("<img src='/public/images/images_crud/error.png' width='50' height='50' />"+
                                                 "<strong>"+" Error, No se pudo editar los datos!"+"</strong>");
                        $('#modalAlertaError').modal('show');
                    }
            },
            failure: function(response){
                        $("#divMensajeAlertaError").html("<img src='/public/images/images_crud/error.png' width='50' height='50' />"+
                                                 "<strong>"+" Error, No se pudo editar los datos!"+"</strong>");
                        $('#modalAlertaError').modal('show');
            }
        });
    }

    /**
     * Graba mover asignación al tab de Incidencias de seguridad
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-05-2020
     * @since 1.0
     */
    function grabarMoverIncidenciaSeguridad(idAsignacion, tipo, valor)
    {
        var parametros = {
            "strValor"       : valor,
            "intId"          : idAsignacion,
            "strTipo"        : tipo
        };
        $.ajax({
            data :  parametros,
            url  :  url_modificar_asignacion,
            type :  'post',
            beforeSend: function () {

            },
            success:  function (response) {
                    if(response==="OK")
                    {
                        $("#divMensajeAlertaError").html("<img src='/public/images/images_crud/check48.png' width='50' height='50' />"+
                                                         "<strong>"+" Se movio incidencia de seguridad con éxito!"+"</strong>");
                        $('#modalAlertaError').modal('show');
                        document.getElementById("btnBuscarPorFecha").onclick();
                    }
                    else
                    {
                        $("#divMensajeAlertaError").html("<img src='/public/images/images_crud/error.png' width='50' height='50' />"+
                                                 "<strong>"+" Error, No se pudo mover incidencia de seguridad!"+"</strong>");
                        $('#modalAlertaError').modal('show');
                    }
            },
            failure: function(response){
                        $("#divMensajeAlertaError").html("<img src='/public/images/images_crud/error.png' width='50' height='50' />"+
                                                 "<strong>"+" Error, No se pudo mover incidencia de seguridad!"+"</strong>");
                        $('#modalAlertaError').modal('show');
            }
        });
    }
/**
 * Presenta los labels para campo nombre reporta en pestaña infoAsignacion
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 28-11-2018
 * @since 1.0
 */
function presentarLabelNombreReporta(){
    $('#divTextNombreReportaInfo').hide();
    $('#labelNombreReportaInfo').show();
    $('#btnEditarNombreReporta').show();
    $('#txtNombreReportaInfo').hide();
    $('#btnCancelarEditarNombreReporta').hide();
    $('#btnGrabarEditarNombreReporta').hide();
}
/**
 * Presenta los labels para campo nombre sitio en pestaña infoAsignacion
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 28-11-2018
 * @since 1.0
 */
function presentarLabelNombreSitio() {
    $('#divTextNombreSitioInfo').hide();
    $('#labelNombreSitioInfo').show();
    $('#btnEditarNombreSitio').show();
    $('#txtNombreSitioInfo').hide();
    $('#btnCancelarEditarNombreSitio').hide();
    $('#btnGrabarEditarNombreSitio').hide();
}
/**
 * Presenta los labels para campo dato adicional en pestaña infoAsignacion
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 28-11-2018
 * @since 1.0
 */
function presentarLabelDatoAdicional() {
    $('#divTextDatoAdicionalInfo').hide();
    $('#labelDatoAdicionalInfo').show();
    $('#btnEditarDatoAdicional').show();
    $('#txtDatoAdicionalInfo').hide();
    $('#btnCancelarEditarDatoAdicional').hide();
    $('#btnGrabarEditarDatoAdicional').hide();
}

/**
 * Presenta los labels para campo tipo problema en pestaña infoAsignación
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 20-02-2019
 * @since 1.0
 */
function presentarLabelTipoProblema() {
    $('#divTextTipoProblemaInfo').hide();
    $('#labelTipoProblemaInfo').show();
    $('#btnEditarTipoProblema').show();
    $('#txtTipoProblemaInfo').hide();
    $('#btnCancelarEditarTipoProblema').hide();
    $('#btnGrabarEditarTipoProblema').hide();
}

function hideCloseButton(idModal){
    // get the close button inside the modal
    $('#'+idModal+' .close').css('display', 'none');
 }
 // finally call the method
 hideCloseButton('modalNuevaAsignacion');
 hideCloseButton('modalAsignacionLote');

/**
 * Función que se encarga de decodicar json de las tareas y darles formato para
 * presentarlas en el detalle de las asignaciones
 * @param tareas      => listado de tareas para presentar en el listado
 * @param numeroCaso  => número del caso
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 20-02-2019
 * @since 1.0
 */
function decodeTareas(tareas,numeroCaso)
{
    var strDatoRetorna   = ""; 
    var numeroTarea      = "";
    var depAsignado      = "";
    var estadoTarea      = "";
    var colorEstadoTarea = 'default';
    if (tareas !== null)
    {
       var arrayTareas = JSON.parse(tareas);
       
       for(var i=0;i<arrayTareas.length;i++)
       {
            numeroTarea      = arrayTareas[i].NUM;
            depAsignado      = arrayTareas[i].ASIG;
            estadoTarea      = arrayTareas[i].EST;
            colorEstadoTarea = 'default';
            if (arrayTareas[i].EST === 'Finalizada')
            {
                colorEstadoTarea = 'success';
            }
            else if (arrayTareas[i].EST === 'Asignada')
            {
                colorEstadoTarea = 'danger';
            }
            strDatoRetorna += '<div class="cardTarea">'+
                              '    <div class="cardTareaCapaSuperior">'+
                              '        <div class="cardTareaNumero">'+
                              '          <b>'+
                              '          <a target="_blank" href="'+url_mis_tareas_grid+'?numTarea='+numeroTarea+'">'+
                                           numeroTarea+
                              '          </a>'+
                              '          </b></div>'+
                              "        <div class='cardTareaDepartamento'>"+depAsignado+"</div>"+
                              '        <div style="margin:5px;">'+
                              '        <span class="label label-'+colorEstadoTarea+' xs" >'+
                                       estadoTarea+'</span></div>'+
                              '    </div>'+
                              '</div>';
            if(i === 1)
            {
                break;
            }
       }
       if (arrayTareas.length > 2)
       {
           tareas = tareas.replace(/"/g, '\\"');
           strDatoRetorna += '<div style="text-align:right">'+
                             '<span style="font-size:10px;">Total '+( arrayTareas.length )+' tareas </span>'+
                             '<span class="hint--bottom-right hint--default hint--small'+
                             ' hint--rounded" aria-label="Ver todas las tareas del caso">'+
                             '<button type="button" class="btn btn-primary btn btn-xs xss circular" '+
                             '  onClick=\'javascript:mostrarTareasCaso(\"'+tareas+'","'+numeroCaso+'\");\' >'+
                             '<span class="glyphicon glyphicon-plus"></span>'+
                             '</button>'+
                             '<span>'+
                             '</div>';
       }
       else
       {
           strDatoRetorna += '<div style="text-align:center">'+
                             '<span style="font-size:10px;">Total '+( arrayTareas.length )+' tareas </span>'+
                             '</div>';
           
       }
    }
    return strDatoRetorna;
}

/**
 * Función que se encarga de decodicar json de las tareas y darles formato para
 * presentarlas la pestaña de infoAsignacion
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 20-02-2019
 * @since 1.0
 */
function decodeTareasInfoAsignacion(tareas,verEnInfoAsignacion)
{
    var strDatoRetorna   = ""; 
    var numeroTarea      = "";
    var depAsignado      = "";
    var estadoTarea      = "";
    var feCreacion       = "";
    var colorEstadoTarea = 'default';

    if (tareas !== null)
    {
       var arrayTareas = JSON.parse(tareas);

       if(verEnInfoAsignacion === true)
       {
          strDatoRetorna +=
                             "<div class=\"row\" style=\"height: 50px;\">"+
                             "    <div class=\"col-lg-11 cabeceraGrid\">"+    
                             "        <h5><span class=\"glyphicon glyphicon-chevron-down\" style=\"color:#ffffff\"></span>"+
                             "            <font color=\"white\">Tareas del Caso</font>"+
                             "        </h5>"+
                             "    </div>"+
                             "</div>";
       }
       strDatoRetorna += '<div class="row" >';
       strDatoRetorna += '<div class="col-lg-2 cabeceraGridCompleto">';
       strDatoRetorna += '<b>N&uacute;mero</b>';
       strDatoRetorna += '</div>';
       strDatoRetorna += '<div class="col-lg-2 cabeceraGridCompleto">';
       strDatoRetorna += '<b>Fecha Creaci&oacute;n</b>';
       strDatoRetorna += '</div>';
       strDatoRetorna += '<div class="col-lg-2 cabeceraGridCompleto">';
       strDatoRetorna += '<b>Asignado</b>';
       strDatoRetorna += '</div>';
       strDatoRetorna += '<div class="col-lg-2 cabeceraGridCompleto">';
       strDatoRetorna += '<b>Estado</b>';
       strDatoRetorna += '</div>';
       if(verEnInfoAsignacion === true)
       {
           strDatoRetorna += '<div class="col-lg-1 cabeceraGridCompleto">';
           strDatoRetorna += '<b>Acciones</b>';
           strDatoRetorna += '</div>';
       }
       strDatoRetorna += '</div>';
       for(var i=0;i<arrayTareas.length;i++)
       {
            numeroTarea      = arrayTareas[i].NUM;
            depAsignado      = arrayTareas[i].ASIG;
            estadoTarea      = arrayTareas[i].EST;
            feCreacion       = arrayTareas[i].FE;
            colorEstadoTarea = 'default';
            if (i===0)
            {
                document.getElementById("idTareaCasoInfoAsignacion").value = numeroTarea;
                $("#numeroTareaReplicaSpan").html('&nbsp;<b>'+numeroTarea+'</b>');
                $("#numeroTareaDiv").html('<div style="background:#d8d8d8;padding:1px;">'+
                                          '<div style="padding:9px;background:white;">'+
                                          '<b>TAREA:&nbsp;&nbsp;</b>'+
                                          '<b><a target="_blank" href="'+url_mis_tareas_grid+'?numTarea='+numeroTarea+'">'+
                                           numeroTarea+
                                          '</a></b></div></div>');
            }
            if (arrayTareas[i].EST === 'Finalizada')
            {
                colorEstadoTarea = 'success';
            }
            else if (arrayTareas[i].EST === 'Asignada')
            {
                colorEstadoTarea = 'danger';
            }
            strDatoRetorna += '<div class="row">';
            //columna número tarea
            strDatoRetorna += '<div class="col-lg-2 detalleGridCompleto">';
            strDatoRetorna += '<b>'+
                              '<a target="_blank" href="'+url_mis_tareas_grid+'?numTarea='+numeroTarea+'">'+
                              numeroTarea+
                              '</a>'+
                              '</b>';
            strDatoRetorna += '</div>';
            //columna fecha
            strDatoRetorna += '<div class="col-lg-2 detalleGridCompleto">';
            strDatoRetorna += ''+
                              feCreacion+
                              ''+
                              '';
            strDatoRetorna += '</div>';
            //columna Departamento Asignado
            strDatoRetorna += '<div class="col-lg-2 detalleGridCompleto" >';
            strDatoRetorna += depAsignado;
            strDatoRetorna += '</div>';
            //columna Estado tarea
            strDatoRetorna += '<div class="col-lg-2 detalleGridCompleto" >';
            strDatoRetorna += '<span class="label label-'+colorEstadoTarea+'" style="font-size:12px;">'+estadoTarea+'</span>';
            strDatoRetorna += '</div>';
            if(verEnInfoAsignacion === true)
            {
                strDatoRetorna += '<div class="col-lg-1 detalleGridCompleto">';
                strDatoRetorna +=
                                  '<span class="hint--bottom-right hint--default hint--medium'+
                                  ' hint--rounded" aria-label="Ver Seguimientos">'+
                                  '<button type="button" class="btn btn-default btn-xs" '+
                                  '     onClick="javascript:mostrarSeguimientos('+numeroTarea+',\''+estadoTarea+'\');">'+
                                  '    <span class="glyphicon glyphicon-th-list"></span>'+
                                  '</button></span>';
                strDatoRetorna += '</div>';
            }
            strDatoRetorna += '</div>';               
       }
    }
    return strDatoRetorna;
}

/**
 * Actualización: Se agrega programación para lo siguiente:
 *                - Mostrar un listado de tareas si tipo de atención es un caso.
 *                - Permitir editar el campo tipo de problema.
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.3 20-02-2019
 * 
 * Actualización: Se agrega validación : Si la asignación esta en estado cerrado,
 *                entonces se oculta el botón de ingreso de seguimientos.
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 01-02-2019
 * 
 * Actualización: Se agrega edición de nombre reporta, nombre sitio y dato adicional
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 28-11-2018
 *
 * Lee la cabecera del cuadro de asignaciones por medio de un ajax
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 19-07-2018
 * @since 1.0
 */
function obtenerInformacionAsignacion(idAsignacion)
{
    var resultado;
    var resultadoDiv;
    var resultadoDivTareasCaso;
    var idCaso       = 0;
    var idTarea      = 0;
    var numeroTarea  = "";
    var feAsignacion = "";
    var tipoAtencion = "";
    $("#infoAsignacionDiv").html("");
    $("#infoAsignacionTecDiv").html("");
    $("#infoAsignacionAdicDiv").html("");
    mostrarInfoAsignacion();

    $.ajax({
                url:   url_info_asignacion,
                type:  'post',
                data: { intId : idAsignacion },
                beforeSend: function () {
                        $("#infoAsignacionTiemposRespDiv").html("");
                        $("#infoAsignacionTiemposCierreDiv").html("");

                        $("#divLoaderInfoAsignacionDiv").show();
                        $("#divLoaderInfoAsignacionTiemposRespDiv").show();
                        $("#divLoaderInfoAsignacionTiemposCierreDiv").show();
                },
                success:  function (response) {
                        $("#divLoaderInfoAsignacionDiv").hide();
                        $("#divLoaderInfoAsignacionTiemposRespDiv").hide();
                        $("#divLoaderInfoAsignacionTiemposCierreDiv").hide();
                        resultado                 = "";
                        resultadoDiv              = "";
                        resultadoDivTareasCaso    = "";
                        resultadoDivTiemposResp   = "";
                        resultadoDivTiemposCierre = "";
                        for (var i in response.data) 
                        {
                            if (response.data.hasOwnProperty(i))
                            {
                                numeroTarea  = response.data[i].numeroTarea;
                                
                                var colorLabel="default";
                                resultadoDiv+=
                                    "<div class=\"row\" style=\"height: 50px;\">"+
                                    "    <div class=\"col-lg-11 cabeceraGrid\">"+    
                                    "        <h5><span class=\"glyphicon glyphicon-chevron-down\" style=\"color:#ffffff\"></span>"+
                                    "            <font color=\"white\">Informaci&oacute;n General</font>"+
                                    "        </h5>"+
                                    "    </div>"+
                                    "</div>";
                                //FILA 1
                                resultadoDiv+="<div class=\"row\">"+
                                                "<div class=\"col-lg-1 labelTituloInfo\" >Numero:</div>"+
                                                "<div class=\"col-lg-1 labelDetalleInfo\"><div style='font-size:17px;font-weight:bold;'>"+
                                                   response.data[i].numero+
                                                "</div></div>"+
                                                "<div class=\"col-lg-1 labelTituloInfo\">Fecha Asignaci&oacute;n:</div>"+
                                                "<div class=\"col-lg-1 labelDetalleInfo\">"+response.data[i].feAsignacion+"</div>";
                                resultadoDiv+=  "<div class=\"col-lg-1\" style='width:60px;padding:0'></div>";
                                resultadoDiv+=  "<div class=\"col-lg-1 labelTituloInfo\">Responsable</div>"+
                                                "<div class=\"col-lg-3 labelDetalleInfo\">"+response.data[i].usrAsignado+"</div>";
                                resultadoDiv+="</div>";

                                //FILA 2
                                colorLabel="default";
                                if(response.data[i].criticidad==="ALTA")
                                {
                                    colorLabel="danger";
                                }
                                else if (response.data[i].criticidad==="MEDIA")
                                {
                                    colorLabel="warning";
                                }
                                resultadoDiv+=  "<div class=\"row\">"+
                                                  "<div class=\"col-lg-1 labelTituloInfo\" >Criticidad:</div>"+
                                                  "<div class=\"col-lg-1 labelDetalleInfo\">"+
                                                  "    <span class=\"label label-"+colorLabel+"\" style=\"font-size:13px;\">"+
                                                       response.data[i].criticidad+"</span>"+
                                                  "</div>";
                                resultadoDiv+=    "<div class=\"col-lg-1 labelTituloInfo\" >Origen:</div>"+
                                                  "<div class=\"col-lg-1 labelDetalleInfo\">"+response.data[i].origen+"</div>";
                                resultadoDiv+=    "<div class=\"col-lg-1\" style='width:60px;padding:0'></div>";
                                resultadoDiv+=    "<div class=\"col-lg-1 labelTituloInfo\" >Contacto Reporta:</div>";
                                resultadoDiv+=    "<div id=\"labelNombreReportaInfo\" class=\"col-lg-3 labelDetalleInfo\" >"+
                                                   response.data[i].nombreReporta+"</div>";
                                resultadoDiv+=    "<div id=\"divTextNombreReportaInfo\" class=\"col-lg-3 labelDetalleInfo\" "+
                                                  " style='display:none'>"+
                                                  "    <input type=\"text\" maxlength=\"100\" class=\"form-control input-sm\" "+
                                                  "        id=\"txtNombreReportaInfo\"  style=\"display : none\"></div>";
                                resultadoDiv+=    "    <div class=\"col-lg-1\" style='width:60px;padding:0'>";
                                resultadoDiv+=    "    <button type='button' id='btnEditarNombreReporta' class='btn btn-primary btn-xs' "+
                                                  "           title='Editar Contacto reporta'>"+
                                                  "           <span class='glyphicon glyphicon-pencil'></span>"+
                                                  "    </button>";
                                resultadoDiv+=    "    <button type='button' id='btnCancelarEditarNombreReporta' class='btn btn-danger btn-xs' "+
                                                  "           style='display:none'  title='Cancelar edición'>"+
                                                  "           <span class='glyphicon glyphicon-floppy-remove'></span>"+
                                                  "    </button>";
                                resultadoDiv+=    "    <button type='button' id='btnGrabarEditarNombreReporta' class='btn btn-success btn-xs' "+
                                                  "           style='display:none'  title='Grabar datos'>"+
                                                  "           <span class='glyphicon glyphicon-floppy-saved'></span>"+
                                                  "    </button>";
                                resultadoDiv+=    "    <button type='button' id='btnLoadingEditarNombreReporta' class='btn btn-success btn-xs' "+
                                                  "           style='display:none' >"+
                                                  "            <i class='fa fa-spinner fa-spin'></i>"+
                                                  "    </button>";

                                resultadoDiv+=    "</div>";
                                resultadoDiv+=  "</div>";

                                //FILA 3
                                resultadoDiv+=  "<div class=\"row\">";

                                if(response.data[i].tipoAtencion==="TAREA")
                                {
                                    colorLabel="success";
                                }
                                else if(response.data[i].tipoAtencion==="CASO")
                                {
                                    colorLabel="warning";
                                }    

                                resultadoDiv+=  "<div class=\"col-lg-1 labelTituloInfo\" >Tipo Atenci&oacute;n:</div>"+
                                                "<div class=\"col-lg-1 labelDetalleInfo\">"+
                                                "    <span class=\"label label-"+colorLabel+"\" style=\"font-size:13px;\">"+
                                                               response.data[i].tipoAtencion+
                                                "        </span></div>"+
                                                "<div class=\"col-lg-1 labelTituloInfo\">Tipo Problema:</div>"+
                                                "<div id=\"labelTipoProblemaInfo\" class=\"col-lg-1 labelDetalleInfo\">"+
                                                response.data[i].tipoProblema+"</div>";
                                resultadoDiv+= "<div id='divTextTipoProblemaInfo' class='col-lg-3 labelDetalleInfo' style='display:none;'>"+
                                               "<div class='dropdown' id='dropDownTipoProblemaInfo'>"+
                                               "<button class='btn btn-default dropdown-toggle xssAdj' "+
                                               "        type='button' data-toggle='dropdown'"+
                                               "        data-value='"+response.data[i].tipoProblema.toUpperCase()+
                                               "'       value='"+response.data[i].tipoProblema.toUpperCase()+
                                               "'       title='Tipo de problema' "+
                                               "        id='btnTipoProblemaInfo'>"+
                                               response.data[i].tipoProblema.toUpperCase()+" <span class='caret'></span>"+
                                               "</button>"+
                                               "<ul class='dropdown-menu'  id='ulDropDownTipoProblemaInfo'> "+
                                               "</ul>"+
                                               "</div>"+
                                               "</div>";

                                resultadoDiv+=    "<div class=\"col-lg-1\" style='width:60px;padding:0'>";
                                resultadoDiv+=    "    <button type='button' id='btnEditarTipoProblema' class='btn btn-primary btn-xs' "+
                                                  "           title='Editar Tipo Problema'>"+
                                                  "           <span class='glyphicon glyphicon-pencil'></span>"+
                                                  "    </button>";
                                resultadoDiv+=    "    <button type='button' id='btnCancelarEditarTipoProblema' class='btn btn-danger btn-xs' "+
                                                  "           style='display:none'  title='Cancelar edición'>"+
                                                  "           <span class='glyphicon glyphicon-floppy-remove'></span>"+
                                                  "    </button>";
                                resultadoDiv+=    "    <button type='button' id='btnGrabarEditarTipoProblema' class='btn btn-success btn-xs' "+
                                                  "           style='display:none'  title='Grabar datos'>"+
                                                  "           <span class='glyphicon glyphicon-floppy-saved'></span>"+
                                                  "    </button>";
                                resultadoDiv+=    "    <button type='button' id='btnLoadingEditarTipoProblema' class='btn btn-success btn-xs' "+
                                                  "           style='display:none' >"+
                                                  "            <i class='fa fa-spinner fa-spin'></i>"+
                                                  "    </button>";
                                resultadoDiv+=    "</div>";

                                resultadoDiv+=  "<div class=\"col-lg-1 labelTituloInfo\">Contacto en Sitio:</div>"+
                                                "<div id=\"labelNombreSitioInfo\" class=\"col-lg-3 labelDetalleInfo\">"+
                                                response.data[i].nombreSitio+"</div>";
                                resultadoDiv+=  "<div id=\"divTextNombreSitioInfo\" class=\"col-lg-3 labelDetalleInfo\" "+
                                                " style='display:none'>"+
                                                "    <input type=\"text\" maxlength=\"100\" class=\"form-control input-sm\" "+
                                                "    id=\"txtNombreSitioInfo\" style=\"display : none\"></div>";

                                resultadoDiv+=  "<div class=\"col-lg-1\" style='width:60px;padding:0'>";
                                resultadoDiv+=  "    <button type='button' id='btnEditarNombreSitio' class='btn btn-primary btn-xs'"+
                                                "           title='Editar Nombre en Sitio'>"+
                                                "           <span class='glyphicon glyphicon-pencil'></span>"+
                                                "    </button>";
                                resultadoDiv+=  "    <button type='button' id='btnCancelarEditarNombreSitio' class='btn btn-danger btn-xs' "+
                                                "           style='display:none' title='Cancelar edición'>"+
                                                "           <span class='glyphicon glyphicon-floppy-remove'></span>"+
                                                "    </button>";
                                resultadoDiv+=  "    <button type='button' id='btnGrabarEditarNombreSitio' class='btn btn-success btn-xs' "+
                                                "           style='display:none' title='Grabar datos'>"+
                                                "           <span class='glyphicon glyphicon-floppy-saved'></span>"+
                                                "    </button>";
                                resultadoDiv+=    "    <button type='button' id='btnLoadingEditarNombreSitio' class='btn btn-success btn-xs' "+
                                                  "           style='display:none' >"+
                                                  "            <i class='fa fa-spinner fa-spin'></i>"+
                                                  "    </button>";
                                resultadoDiv+=  "</div>";
                                resultadoDiv+=  "</div>";



                                //FILA 4                     
                                colorLabel="default";

                                if(response.data[i].estadoTarea==="Finalizada")
                                {
                                    colorLabel="success";
                                }
                                else if (response.data[i].estadoTarea==="Asignada")
                                {
                                    colorLabel="warning";
                                }
                                var textEstadoTarea = "<span class=\"label label-"+colorLabel+"\" style=\"font-size:13px;\">"+
                                                               response.data[i].estadoTarea+
                                                     "</span>";
                                if (response.data[i].estadoTarea==="")
                                {
                                    textEstadoTarea = " ";
                                }

                                resultadoDiv+=  "<div class=\"row\">";
                                resultadoDiv+= "<div class=\"col-lg-1 labelTituloInfo\">Estado Tarea:</div>"+
                                                 "<div class=\"col-lg-1 labelDetalleInfo\">"+textEstadoTarea+"</div>";
                                resultadoDiv+=
                                                 "<div class=\"col-lg-1 labelTituloInfo\">Datos Adicionales:</div>"+
                                                 "<div id=\"labelDatoAdicionalInfo\" class=\"col-lg-2 labelDetalleTextInfo\">"+
                                                 response.data[i].datoAdicional+"</div>";
                                resultadoDiv+=  "<div id=\"divTextDatoAdicionalInfo\" class=\"col-lg-3 labelDetalleTextInfo \" "+
                                                " style='display:none'>"+
                                                "    <input type=\"text\" maxlength=\"150\" class=\"form-control input-sm\" "+
                                                "    id=\"txtDatoAdicionalInfo\" style=\"display : none\"></div>";

                                resultadoDiv+=  "<div class=\"col-lg-1\" style='width:60px;padding:0'>";
                                resultadoDiv+=  "    <button type='button' id='btnEditarDatoAdicional' class='btn btn-primary btn-xs' "+
                                                "           title='Editar Datos adicionales'>"+
                                                "           <span class='glyphicon glyphicon-pencil'></span>"+
                                                "    </button>";
                                resultadoDiv+=  "    <button type='button' id='btnCancelarEditarDatoAdicional' class='btn btn-danger btn-xs' "+
                                                "           style='display:none'  title='Cancelar edición'>"+
                                                "           <span class='glyphicon glyphicon-floppy-remove'></span>"+
                                                "    </button>";
                                resultadoDiv+=  "    <button type='button' id='btnGrabarEditarDatoAdicional' class='btn btn-success btn-xs' "+
                                                "           style='display:none'  title='Grabar datos'>"+
                                                "           <span class='glyphicon glyphicon-floppy-saved'></span>"+
                                                "    </button>";
                                resultadoDiv+=    "    <button type='button' id='btnLoadingEditarDatoAdicional' class='btn btn-success btn-xs' "+
                                                  "           style='display:none' >"+
                                                  "            <i class='fa fa-spinner fa-spin'></i>"+
                                                  "    </button>";
                                resultadoDiv+=  "</div>";
                                resultadoDiv+=  "</div>";
                                colorLabel = "default";



                                //FILA 5
                                colorLabel="default";

                                if(response.data[i].estadoCaso==="Asignado")
                                {
                                    colorLabel="warning";
                                }
                                else if (response.data[i].estadoCaso==="Cerrado")
                                {
                                    colorLabel="success";
                                }
                                else if (response.data[i].estadoCaso==="Abierto")
                                {
                                    colorLabel="primary";
                                }
                                if(response.data[i].estadoCaso==="")
                                {
                                    colorLabel     = "default";
                                    textEstadoCaso = " ";
                                }
                                var textEstadoCaso = "<h4><span class=\"label label-"+colorLabel+"\">"+
                                                               response.data[i].estadoCaso+
                                                     "</span></h4>";

                                resultadoDiv+="<div class=\"row\">"+
                                                 "<div class=\"col-lg-1 labelTituloInfo\">Estado Caso:</div>"+
                                                 "<div class=\"col-lg-1 labelDetalleInfo\">"+textEstadoCaso+"</div>"+
                                                 "<div class=\"col-lg-1 labelTituloInfo\" >Detalle:</div>"+
                                                 "<div class=\"col-lg-8 labelDetalleTextInfo\">"+response.data[i].detalle+"</div>"+
                                                 "</div>";


                                if(response.data[i].tipoAtencion==="CASO")
                                {
                                    idCaso = response.data[i].referenciaId;
                                }
                                else if(response.data[i].tipoAtencion==="TAREA")
                                {
                                    idTarea      = response.data[i].referenciaId;
                                }
                                feAsignacion = response.data[i].feAsignacion;

                                //Si la tarea ya esta finalizada o la asignación esta pendiente o cerrado,
                                //entonces la asignación oculta el botón de ingreso de seguimientos
                                if ( ( response.data[i].tipoAtencion === "TAREA"      && response.data[i].estadoTarea === "Finalizada" ) ||
                                     ( response.data[i].tipoAtencion === "CASO"       && response.data[i].estadoCaso  === "Cerrado"    ) ||
                                     ( response.data[i].estado       === "Pendiente"                                                   ) ||
                                     ( response.data[i].estado       === "Cerrado"                                                     )
                                   )
                                {
                                    $("#btnNuevoSeguimiento").hide();
                                    muestraBtnEliminarSeguimiento = false;
                                }
                                else
                                {
                                    $("#btnNuevoSeguimiento").show();
                                    muestraBtnEliminarSeguimiento = true;
                                }
                                //blanqueamos el número de tarea antes seleccionar o regargar la nueva tarea.
                                $("#numeroTareaDiv").html('');
                                $("#numeroTareaReplicaSpan").html('');
                                document.getElementById("idTareaCasoInfoAsignacion").value = '';

                                if ( ( response.data[i].tipoAtencion === "CASO") && (response.data[i].infoTareas !== null) )
                                {
                                    resultadoDivTareasCaso = decodeTareasInfoAsignacion(response.data[i].infoTareas,true);
                                }
                            }
                        }
                        resultado+="";
                        $("#infoAsignacionDiv").html(resultadoDiv);
                        $("#tareasCasoDiv").html(resultadoDivTareasCaso);

                        //busca y agrega los tipos de problema al combo de edición del campo tipo de problema
                        buscaTiposProblemaParaEditar("#ulDropDownTipoProblemaInfo");

                        //Obtiene la información técnica del caso
                         if(response.data[i].tipoAtencion==="CASO")
                            obtenerInformacionTecnicaCaso(idCaso,feAsignacion,numeroTarea);
                         else if(response.data[i].tipoAtencion==="TAREA")
                         {
                            obtenerInformacionAdicionalTarea(idTarea,feAsignacion);
                            document.getElementById("idTareaCasoInfoAsignacion").value = numeroTarea;
                            $("#numeroTareaDiv").html('<div style="background:#d8d8d8;padding:1px;">'+
                                                      '<div style="padding:9px;background:white;">'+
                                                      '<b>TAREA:&nbsp;&nbsp;</b>'+
                                                      numeroTarea+'</div></div>'
                                                     );
                            $("#numeroTareaReplicaSpan").html('&nbsp;<b>'+numeroTarea+'</b>');
                         }
                        document.getElementById("idAsignacionInfo").value = idAsignacion;
                        detalleSeguimientos.ajax.url(url_detalle_seguimientos+'?intId='+
                                    document.getElementById("idAsignacionInfo").value).load();
                        detalleHistorial.ajax.url(url_detalle_historial+'?intId='+
                                    document.getElementById("idAsignacionInfo").value).load();

                    document.getElementById("btnEditarNombreReporta").onclick = function () {
                        $('#divTextNombreReportaInfo').show();
                        $('#labelNombreReportaInfo').hide();
                        $('#txtNombreReportaInfo').show();
                        $('#btnEditarNombreReporta').hide();
                        $('#btnCancelarEditarNombreReporta').show();
                        $('#btnGrabarEditarNombreReporta').show();
                        document.getElementById("txtNombreReportaInfo").value = $('#labelNombreReportaInfo').html();
                    };

                    document.getElementById("btnCancelarEditarNombreReporta").onclick = function () {
                        $('#divTextNombreReportaInfo').hide();
                        $('#labelNombreReportaInfo').show();
                        $('#btnEditarNombreReporta').show();
                        $('#txtNombreReportaInfo').hide();
                        $('#btnCancelarEditarNombreReporta').hide();
                        $('#btnGrabarEditarNombreReporta').hide();
                    };
                    
                    document.getElementById("btnGrabarEditarNombreReporta").onclick = function () {
                        grabarCambiosEnAsignacion("nombreReporta",document.getElementById("txtNombreReportaInfo").value);
                    };

                    document.getElementById("btnEditarNombreSitio").onclick = function () {
                        $('#divTextNombreSitioInfo').show();
                        $('#labelNombreSitioInfo').hide();
                        $('#txtNombreSitioInfo').show();
                        $('#btnEditarNombreSitio').hide();
                        $('#btnCancelarEditarNombreSitio').show();
                        $('#btnGrabarEditarNombreSitio').show();
                        document.getElementById("txtNombreSitioInfo").value = $('#labelNombreSitioInfo').html();
                    };

                    document.getElementById("btnCancelarEditarNombreSitio").onclick = function () {
                        $('#divTextNombreSitioInfo').hide();
                        $('#labelNombreSitioInfo').show();
                        $('#btnEditarNombreSitio').show();
                        $('#txtNombreSitioInfo').hide();
                        $('#btnCancelarEditarNombreSitio').hide();
                        $('#btnGrabarEditarNombreSitio').hide();
                    };

                    document.getElementById("btnGrabarEditarNombreSitio").onclick = function () {
                        grabarCambiosEnAsignacion("nombreSitio",document.getElementById("txtNombreSitioInfo").value);                    
                    };

                    document.getElementById("btnEditarDatoAdicional").onclick = function () {
                        $('#divTextDatoAdicionalInfo').show();
                        $('#labelDatoAdicionalInfo').hide();
                        $('#txtDatoAdicionalInfo').show();
                        $('#btnEditarDatoAdicional').hide();
                        $('#btnCancelarEditarDatoAdicional').show();
                        $('#btnGrabarEditarDatoAdicional').show();
                        document.getElementById("txtDatoAdicionalInfo").value = $('#labelDatoAdicionalInfo').html();
                    };

                    document.getElementById("btnCancelarEditarDatoAdicional").onclick = function () {
                        $('#divTextDatoAdicionalInfo').hide();
                        $('#labelDatoAdicionalInfo').show();
                        $('#btnEditarDatoAdicional').show();
                        $('#txtDatoAdicionalInfo').hide();
                        $('#btnCancelarEditarDatoAdicional').hide();
                        $('#btnGrabarEditarDatoAdicional').hide();
                    };

                    document.getElementById("btnGrabarEditarDatoAdicional").onclick = function () {
                        grabarCambiosEnAsignacion("datoAdicional",document.getElementById("txtDatoAdicionalInfo").value);                    
                    };

                    document.getElementById("btnEditarTipoProblema").onclick = function () {
                        $('#divTextTipoProblemaInfo').show();
                        $('#labelTipoProblemaInfo').hide();
                        $('#txtTipoProblemaInfo').show();
                        $('#btnEditarTipoProblema').hide();
                        $('#btnCancelarEditarTipoProblema').show();
                        $('#btnGrabarEditarTipoProblema').show();
                        $("#btnTipoProblemaInfo").html ( $('#labelTipoProblemaInfo').html() + " <span class='caret'></span>" );
                    };

                    document.getElementById("btnCancelarEditarTipoProblema").onclick = function () {
                        $('#divTextTipoProblemaInfo').hide();
                        $('#divTextTipoProblemaInfo').hide();
                        $('#labelTipoProblemaInfo').show();
                        $('#btnEditarTipoProblema').show();
                        $('#txtTipoProblemaInfo').hide();
                        $('#btnCancelarEditarTipoProblema').hide();
                        $('#btnGrabarEditarTipoProblema').hide();
                    };

                    document.getElementById("ulDropDownTipoProblemaInfo").onclick = function(event) {
                        var e = event || window.event;
                        var target =  e.target || e.srcElement;
                        $("#btnTipoProblemaInfo").attr('data-value',target.text);
                        $("#btnTipoProblemaInfo").attr('value',target.text);
                        $("#btnTipoProblemaInfo").html(target.text+" <span class='caret'></span>");
                    };
                    document.getElementById("btnGrabarEditarTipoProblema").onclick = function () {
                        grabarCambiosEnAsignacion("tipoProblema",document.getElementById("btnTipoProblemaInfo").value);                    
                    };

                },
                failure: function(response){
                        console.log("failure");
                }
            });
}



/**
 * 
 * Ejecuta el ajax que lee la información de los seguimientos y los presenta en un listado
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 21-02-2019
 * @since 1.0
 */
function mostrarSeguimientos(numeroTarea,estadoTarea)
{
    document.getElementById("idTareaCasoInfoAsignacion").value = numeroTarea;
    $("#numeroTareaReplicaSpan").html('&nbsp;<b>'+numeroTarea+'</b>');
    $("#numeroTareaDiv").html('<div style="background:#d8d8d8;padding:1px;">'+
                              '<div style="padding:9px;background:white;">'+
                              '<b>TAREA:&nbsp;&nbsp;</b>'+
                              '<b><a target="_blank" href="'+url_mis_tareas_grid+'?numTarea='+numeroTarea+'">'+numeroTarea+'</a></b>'+'</div></div>');
    detalleSeguimientos.ajax.url(url_detalle_seguimientos+
                                 '?intId='+document.getElementById("idAsignacionInfo").value+
                                 '&intIdTarea='+numeroTarea).load();
    if (estadoTarea === 'Finalizada' || estadoTarea === 'Cancelada')
    {
        $("#checkBoxReplicarTarea").hide();
    }
    else
    {
        $("#checkBoxReplicarTarea").show();
    }
}



/**
 * 
 * Actualización: Se agrega título para data técnica y se envia arreglo de servicios para obtener ips
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.3 10-01-2019
 * 
 * Actualización: Se agrega vrf, capacidad 1 y capacidad 2
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 28-11-2018
 *
 * Actualización: Para los tiempos de cierre se cambia "m" por "min"
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 06-11-2018
 *
 * Lee la información técnica del caso
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 28-08-2018
 * @since 1.0
 */
function obtenerInformacionTecnicaCaso(idCaso,feAsignacion,numeroTarea)
{
    var resultado;
    var resultadoDiv;
    var resultadoDivTiemposResp;
    var resultadoDivTiemposCierre;
    var idServicio;
    var dataCaso;
    var minutos       = "0";
    var minutosCierre = "0";
    mostrarInfoAsignacion();
    var parametros = {
        "intId"          : idCaso,
        "feAsignacion"   : feAsignacion,
        "strNumeroTarea" : numeroTarea
    };
    var arrayServicios= [];
    $.ajax({
                url:   url_info_tecnica_caso,
                type:  'post',
                data: parametros,
                beforeSend: function () {
                        $("#divLoaderInfoAsignacionTiemposRespDiv").show();
                        $("#divLoaderInfoAsignacionTiemposCierreDiv").show();
                        $("#divLoaderInfoAsignacionTecDiv").show();
                        resultadoDivTiemposResp =
                                        "<div class=\"row\">"+
                                          "<div class=\"col-lg-10\">"+
                                            "<div class='row'>"+
                                            "  <div style='text-align:center;' class='col-lg-11'>"+
                                                "<span class='glyphicon glyphicon-time' style='font-size: 4em;color:#1565c0;'></span>"+
                                            "  </div>"+
                                            "</div>"+
                                            "<div class='row'>"+
                                            "  <div style='text-align:center;' class='col-lg-11'>"+
                                                "<h6>Tiempo Respuesta</h6>"+
                                            "  </div>"+
                                            "</div>"+
                                            "<div class='row'>"+
                                            "  <div style='text-align:center;' class='col-lg-11'>"+
                                                "<h3>"+minutos+" min"+"</h3>"+
                                            "  </div>"+
                                            "</div>"+
                                          "</div>"+
                                        "</div>";
                        resultadoDivTiemposCierre =
                                        "<div class=\"row\">"+
                                          "<div class=\"col-lg-10\">"+
                                            "<div class='row'>"+
                                            "  <div style='text-align:center;' class='col-lg-11'>"+
                                                "<span class='glyphicon glyphicon-time' style='font-size: 4em;color:#7e57c2;'></span>"+
                                            "  </div>"+
                                            "</div>"+
                                            "<div class='row'>"+
                                            "  <div style='text-align:center;' class='col-lg-11'>"+
                                                "<h6>Tiempo Cierre</h6>"+
                                            "  </div>"+
                                            "</div>"+
                                            "<div class='row'>"+
                                            "  <div style='text-align:center;' class='col-lg-11'>"+
                                                "<h3>"+minutosCierre+" min"+"</h3>"+
                                            "  </div>"+
                                            "</div>"+
                                          "</div>"+
                                        "</div>";
                        $("#infoAsignacionTiemposRespDiv").html(resultadoDivTiemposResp);
                        $("#infoAsignacionTiemposCierreDiv").html(resultadoDivTiemposCierre);

                },
                success:  function (response) {
                        resultado    = "";
                        resultadoDiv = "";
                        dataCaso     = response.data[0];

                        $("#divLoaderInfoAsignacionTiemposRespDiv").hide();
                        $("#divLoaderInfoAsignacionTiemposCierreDiv").hide();
                        $("#divLoaderInfoAsignacionTecDiv").hide();
                        if(dataCaso)
                        {
                            resultadoDiv+=
                                "<div class=\"row\" style=\"height: 50px;\">"+
                                "    <div class=\"col-lg-11 cabeceraGrid\">"+    
                                "        <h5><span class=\"glyphicon glyphicon-chevron-down\" style=\"color:#ffffff\"></span>"+
                                "            <font color=\"white\">Informaci&oacute;n Adicional</font>"+
                                "        </h5>"+
                                "    </div>"+
                                "</div>";

                            resultadoDiv+="<div class=\"row\">"+
                                            "<div class=\"col-lg-1 labelTituloRespInfo\" >Departamento:</div>"+
                                            "<div class=\"col-lg-1 labelDetalleInfo\">"+
                                            dataCaso.departamentoAsignado+"</div>"+
                                            "<div class=\"col-lg-1 labelTituloRespInfo\" >Fecha apertura:</div>"+
                                            "<div class=\"col-lg-1 labelDetalleInfo\">"+dataCaso.feApertura+"</div>"+
                                            "<div class=\"col-lg-1 labelTituloRespInfo\" >Fecha cierre:</div>"+
                                            "<div class=\"col-lg-1 labelDetalleInfo\">"+dataCaso.feCierre+"</div>"+
                                          "</div>";

                            minutos       = dataCaso.minutos;
                            minutosCierre = dataCaso.minutosCierre;

                            resultadoDivTiemposResp =
                                            "<div class=\"row\">"+
                                              "<div class=\"col-lg-10\">"+
                                                "<div class='row'>"+
                                                "  <div style='text-align:center;' class='col-lg-11'>"+
                                                    "<span class='glyphicon glyphicon-time' style='font-size: 4em;color:#1565c0;'></span>"+
                                                "  </div>"+
                                                "</div>"+
                                                "<div class='row'>"+
                                                "  <div style='text-align:center;' class='col-lg-11'>"+
                                                    "<h6>Tiempo Respuesta</h6>"+
                                                "  </div>"+
                                                "</div>"+
                                                "<div class='row'>"+
                                                "  <div style='text-align:center;' class='col-lg-11'>"+
                                                    "<h3>"+minutos+" min"+"</h3>"+
                                                "  </div>"+
                                                "</div>"+
                                              "</div>"+
                                            "</div>";
                            resultadoDivTiemposCierre =
                                            "<div class=\"row\">"+
                                              "<div class=\"col-lg-10\">"+
                                                "<div class='row'>"+
                                                "  <div style='text-align:center;' class='col-lg-11'>"+
                                                    "<span class='glyphicon glyphicon-time' style='font-size: 4em;color:#7e57c2;'></span>"+
                                                "  </div>"+
                                                "</div>"+
                                                "<div class='row'>"+
                                                "  <div style='text-align:center;' class='col-lg-11'>"+
                                                    "<h6>Tiempo Cierre</h6>"+
                                                "  </div>"+
                                                "</div>"+
                                                "<div class='row'>"+
                                                "  <div style='text-align:center;' class='col-lg-11'>"+
                                                    "<h3>"+minutosCierre+" min"+"</h3>"+
                                                "  </div>"+
                                                "</div>"+
                                              "</div>"+
                                            "</div>";
                            if (response.dataTecnica)
                            {
                                resultadoDiv+=
                                    "<div class=\"row\" style=\"height: 50px;\">"+
                                    "    <div class=\"col-lg-11 cabeceraGrid\">"+    
                                    "        <h5><span class=\"glyphicon glyphicon-chevron-down\" style=\"color:#ffffff\"></span>"+
                                    "            <font color=\"white\">Informaci&oacute;n T&eacute;cnica Afectado</font>"+
                                    "        </h5>"+
                                    "    </div>"+
                                    "</div>";

                                for (var i in response.dataTecnica) 
                                {
                                    if (response.dataTecnica.hasOwnProperty(i))
                                    {
                                        arrayServicios.push(response.dataTecnica[i].idServicio);
                                        resultadoDiv+="<div class=\"row\" style=\"height: 40px\">"+
                                                      "  <div class=\"col-lg-12\">"+
                                                      "    <span style='font-size=20px' class='glyphicon glyphicon-inbox'></span>&nbsp;"+
                                                      "    <b>"+response.dataTecnica[i].producto+"</b>"+
                                                      "    <a title='Ver más Información Técnica' target='_blank' class='btn btn-primary btn-xs' "+
                                                      "      href='../../tecnico/clientes/"+response.dataTecnica[i].idServicio+"/showServicio'>"+
                                                      "      <span class='glyphicon glyphicon-info-sign'></span> Ver m&aacute;s"+
                                                      "    </a>"+
                                                      "  </div>"+
                                                      "</div>";
                                        resultadoDiv+="<div class=\"row\">"+
                                                        "<div class=\"col-sm-2 labelTituloTecInfo\" >Login:</div>"+
                                                        "<div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].login+"</div>"+
                                                        "<div class=\"col-sm-2 labelTituloTecInfo\" >Switch:</div>"+
                                                        "<div class=\"col-sm-2 labelDetalleInfo\">"+
                                                        response.dataTecnica[i].switch+"</div>"+
                                                        "<div class=\"col-sm-2 labelTituloTecInfo\" >Interface Elemento:</div>"+
                                                        "<div class=\"col-sm-2 labelDetalleInfo\">"+
                                                        response.dataTecnica[i].interfaceElemento+"</div>"+
                                                      "</div>";
                                        resultadoDiv+="<div class=\"row\">"+
                                                        "<div class=\"col-sm-2 labelTituloTecInfo\" >PE:</div>"+
                                                        "<div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].pe+"</div>"+
                                                        "<div class=\"col-sm-2 labelTituloTecInfo\" >Mac cpe:</div>"+
                                                        "<div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].macCpe+"</div>"+
                                                        "<div class=\"col-sm-2 labelTituloTecInfo\" >IP WAN:</div>"+
                                                        "  <div id=\"ipWanDiv"+response.dataTecnica[i].idServicio+
                                                        "\" class=\"col-sm-2 labelDetalleInfo\">N/A</div>"+
                                                      "</div>";
                                        resultadoDiv+="<div class=\"row\">"+
                                                        "<div class=\"col-sm-2 labelTituloTecInfo\" >Vrf:</div>"+
                                                        "<div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].vrf+"</div>"+
                                                        "<div class=\"col-sm-2 labelTituloTecInfo\" >Capacidad 1 / 2 (Mb):</div>"+
                                                        "<div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].capacidad1+" / "+
                                                        response.dataTecnica[i].capacidad2+"</div>"+
                                                        "<div class=\"col-sm-2 labelTituloTecInfo\" >Vlan:</div>"+
                                                        "<div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].vlan+"</div>"+
                                                      "</div>";
                                        resultadoDiv+="<div class=\"row\" style=\"height: 20px\">"+
                                                      "  <div class=\"col-lg-12\">&nbsp;"+    
                                                      "  </div>"+
                                                      "</div>";
                                    }
                                }
                            }
                        }
                        resultadoDiv+=
                            "<div class=\"row\" style=\"height: 20px\">"+
                            "    <div class=\"col-lg-12\">&nbsp;"+    
                            "    </div>"+
                            "</div>";

                        $("#infoAsignacionTecDiv").html(resultadoDiv);
                        $("#infoAsignacionTiemposRespDiv").html(resultadoDivTiemposResp);
                        $("#infoAsignacionTiemposCierreDiv").html(resultadoDivTiemposCierre);

                        if (arrayServicios.length > 0)
                        {
                            var resultadoIp = obtenerInformacionIpsCliente(arrayServicios);
                        }
                },
                failure: function(response){
                        $("#divLoaderInfoAsignacionTiemposRespDiv").hide();
                        $("#divLoaderInfoAsignacionTiemposCierreDiv").hide();
                        $("#divLoaderInfoAsignacionTecDiv").hide();
                }
            });

}

/**
 * Lee la información técnica para la tarea
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 10-01-2019
 * @since 1.0
 */
function obtenerInformacionTecnicaTarea(numeroTarea)
{
    var resultado;
    var resultadoDiv;
    var data;
    mostrarInfoAsignacion();
    var parametros = {
        "strNumeroTarea" : numeroTarea
    };
    var arrayServicios= [];
    $.ajax({
                url:   url_info_tecnica_tarea,
                type:  'post',
                data: parametros,
                beforeSend: function () {
                        $("#divLoaderInfoAsignacionTecDiv").show();
                },
                success:  function (response) {
                        resultado    = "";
                        resultadoDiv = "";
                        data     = response.dataTecnica;
                        $("#divLoaderInfoAsignacionTecDiv").hide();
                        if(data.length > 0)
                        {
                            resultadoDiv+=
                                "<div class=\"row\" style=\"height: 50px;\">"+
                                "    <div class=\"col-lg-11 cabeceraGrid\">"+    
                                "        <h5><span class=\"glyphicon glyphicon-chevron-down\" style=\"color:#ffffff\"></span>"+
                                "            <font color=\"white\">Informaci&oacute;n T&eacute;cnica Afectado</font>"+
                                "        </h5>"+
                                "    </div>"+
                                "</div>";
                            for (var i in response.dataTecnica) 
                            {
                                if (response.dataTecnica.hasOwnProperty(i))
                                {
                                    arrayServicios.push(response.dataTecnica[i].idServicio);
                                    resultadoDiv+="<div class=\"row\" style=\"height: 40px;\">"+
                                                  "  <div class=\"col-lg-4\">"+
                                                  "    <span style='font-size=20px;background-color:#f8f8f8;'"+
                                                  "    class='glyphicon glyphicon-inbox'></span>&nbsp;"+
                                                  "    <b>"+response.dataTecnica[i].producto+"</b>"+
                                                  "  </div>"+
                                                  "  <div class=\"col-lg-2\">"+
                                                  "    <a title='Ver más Información Técnica' target='_blank' class='btn btn-primary btn-xs' "+
                                                  "      href='../../tecnico/clientes/"+response.dataTecnica[i].idServicio+"/showServicio'>"+
                                                  "      <span class='glyphicon glyphicon-info-sign'></span> Ver m&aacute;s"+
                                                  "    </a>"+
                                                  "  </div>"+

                                                  "</div>";
                                    resultadoDiv+="<div class=\"row\">"+
                                                  "  <div class=\"col-sm-2 labelTituloTecInfo\" >Login:</div>"+
                                                  "  <div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].login+"</div>"+
                                                  "  <div class=\"col-sm-2 labelTituloTecInfo\" >Switch:</div>"+
                                                  "  <div class=\"col-sm-2 labelDetalleInfo\">"+
                                                  response.dataTecnica[i].switch+"</div>"+
                                                  "  <div class=\"col-sm-2 labelTituloTecInfo\" >Interface Elemento:</div>"+
                                                  "  <div class=\"col-sm-2 labelDetalleInfo\">"+
                                                  response.dataTecnica[i].interfaceElemento+"</div>"+
                                                  "</div>";
                                    resultadoDiv+="<div class=\"row\">"+
                                                  "  <div class=\"col-sm-2 labelTituloTecInfo\" >PE:</div>"+
                                                  "  <div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].pe+"</div>"+
                                                  "  <div class=\"col-sm-2 labelTituloTecInfo\" >Mac cpe:</div>"+
                                                  "  <div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].macCpe+"</div>"+
                                                  "  <div class=\"col-sm-2 labelTituloTecInfo\" >IP WAN:</div>"+
                                                  "  <div id=\"ipWanDiv"+response.dataTecnica[i].idServicio+"\" class=\"col-sm-2 labelDetalleInfo\">"+
                                                  "  N/A</div>"+
                                                  "</div>";
                                    resultadoDiv+="<div class=\"row\">"+
                                                  "  <div class=\"col-sm-2 labelTituloTecInfo\" >Vrf:</div>"+
                                                  "  <div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].vrf+"</div>"+
                                                  "  <div class=\"col-sm-2 labelTituloTecInfo\" >Capacidad 1 / 2 (Mb):</div>"+
                                                  "  <div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].capacidad1+" / "+
                                                  response.dataTecnica[i].capacidad2+"</div>"+
                                                  "  <div class=\"col-sm-2 labelTituloTecInfo\" >Vlan:</div>"+
                                                  "  <div class=\"col-sm-2 labelDetalleInfo\">"+response.dataTecnica[i].vlan+"</div>"+
                                                  "</div>";
                                    resultadoDiv+="<div class=\"row\" style=\"height: 20px\">"+
                                                  "  <div class=\"col-lg-12\">&nbsp;"+    
                                                  "  </div>"+
                                                  "</div>";
                                }
                            }
                        }
                        resultadoDiv+=
                            "<div class=\"row\" style=\"height: 20px\">"+
                            "  <div class=\"col-lg-12\">&nbsp;"+    
                            "  </div>"+
                            "</div>";

                        $("#infoAsignacionTecDiv").html(resultadoDiv);

                        if (arrayServicios.length > 0)
                        {
                            var resultadoIp = obtenerInformacionIpsCliente(arrayServicios);
                        }
                },
                failure: function(response){
                        $("#divLoaderInfoAsignacionTiemposRespDiv").hide();
                        $("#divLoaderInfoAsignacionTiemposCierreDiv").hide();
                        $("#divLoaderInfoAsignacionTecDiv").hide();
                }
            });

}


/**
 * 
 * Se agrega mostrar ips tipo WAN o PUBLICA
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 01-02-2019
 * 
 * Se arreglo con varios servicios para obtener la información de ips
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 10-01-2019
 * 
 * Lee la información de ip por servicio
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 28-08-2018
 * @since 1.0
 */
function obtenerInformacionIpsCliente(arrayServicios)
{
    var resultado;
    $.each(arrayServicios, function (i, item) {

            var parametros = {
                "idServicio" : item
            };
            var idServicio = item;
            $.ajax({
                    url:   url_ver_ips,
                    type:  'post',
                    data:  parametros,
                    beforeSend: function () {

                    },
                    success:  function (response) {
                            resultado = "";
                            for (var i in response.encontrados) 
                            {
                                if (response.encontrados[i].tipo === "WAN" || response.encontrados[i].tipo === "PUBLICA")
                                {
                                    resultado += response.encontrados[i].ip + " ";
                                    $("#ipWanDiv"+idServicio).html(resultado);
                                }   
                            }
                    },
                    failure: function(response){

                    }
            });
    });
    return resultado;
}



/**
 * 
 * Actualización: Se obtiene información técnica de la tarea
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 10-01-2019
 * 
 * Actualización: Para los tiempos de cierre se cambia "m" por "min"
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 06-11-2018
 *
 * Lee la información adicional de la tarea
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 28-08-2018
 * @since 1.0
 */
function obtenerInformacionAdicionalTarea(idTarea,feAsignacion)
{

    var resultado;
    var resultadoDiv;
    var resultadoDivTiemposResp;
    var resultadoDivTiemposCierre;
    var minutos       = "0";
    var minutosCierre = "0";

    var parametros = {
        "intId"       : idTarea,
        "feAsignacion": feAsignacion
    };
    $.ajax({
                url:   url_info_adicional_tarea,
                type:  'post',
                data: parametros,
                beforeSend: function () {
                    $("#divLoaderInfoAsignacionAdicDiv").show();
                    
                    resultadoDivTiemposResp =
                                    "<div class=\"row\">"+
                                      "<div class=\"col-lg-10\">"+
                                        "<div class='row'>"+
                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                            "<span class='glyphicon glyphicon-time' style='font-size: 4em;color:#1565c0;'></span>"+
                                        "  </div>"+
                                        "</div>"+
                                        "<div class='row'>"+
                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                            "<h6>Tiempo Respuesta</h6>"+
                                        "  </div>"+
                                        "</div>"+
                                        "<div class='row'>"+
                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                            "<h3>"+minutos+" min"+"</h3>"+
                                        "  </div>"+
                                        "</div>"+
                                      "</div>"+
                                    "</div>";
                    resultadoDivTiemposCierre =
                                    "<div class=\"row\">"+
                                      "<div class=\"col-lg-10\">"+
                                        "<div class='row'>"+
                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                            "<span class='glyphicon glyphicon-time' style='font-size: 4em;color:#7e57c2;'></span>"+
                                        "  </div>"+
                                        "</div>"+
                                        "<div class='row'>"+
                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                            "<h6>Tiempo Cierre</h6>"+
                                        "  </div>"+
                                        "</div>"+
                                        "<div class='row'>"+
                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                            "<h3>"+minutosCierre+" min"+"</h3>"+
                                        "  </div>"+
                                        "</div>"+
                                      "</div>"+
                                    "</div>";
                    $("#infoAsignacionTiemposRespDiv").html(resultadoDivTiemposResp);
                    $("#infoAsignacionTiemposCierreDiv").html(resultadoDivTiemposCierre);

                },
                success:  function (response) {
                        $("#divLoaderInfoAsignacionAdicDiv").hide();
                        resultado = "";
                        resultadoDiv = "";
                        if (response.data)
                        {    
                            resultadoDiv+=
                                "<div class=\"row\" style=\"height: 50px;\">"+
                                "    <div class=\"col-lg-11 cabeceraGrid\">"+    
                                "        <h5><span class=\"glyphicon glyphicon-chevron-down\" style=\"color:#ffffff\"></span>"+
                                "            <font color=\"white\">Informaci&oacute;n Adicional</font>"+
                                "        </h5>"+
                                "    </div>"+
                                "</div>";

                            for (var i in response.data) 
                            {
                                if(response.data.hasOwnProperty(i))
                                {
                                    resultadoDiv+="<div class=\"row\">"+
                                                    "<div class=\"col-sm-2 labelTituloRespInfo\" >Departamento Asig.:</div>"+
                                                    "<div class=\"col-sm-2 labelDetalleInfo\">"+
                                                    response.data[i].departamentoAsignado+"</div>"+
                                                    "<div class=\"col-sm-2 labelTituloRespInfo\" >Fecha Creada:</div>"+
                                                    "<div class=\"col-sm-2 labelDetalleInfo\">"+response.data[i].fechaCreada+"</div>"+
                                                    "<div class=\"col-sm-2 labelTituloRespInfo\" >Fecha Finalizada:</div>"+
                                                    "<div class=\"col-sm-2 labelDetalleInfo\">"+response.data[i].fechaFinalizada+"</div>"+
                                                  "</div>";

                                    minutos       = response.data[i].minutos;
                                    minutosCierre = response.data[i].minutosCierre;

                                    resultadoDivTiemposResp =
                                                    "<div class=\"row\">"+
                                                      "<div class=\"col-lg-10\">"+
                                                        "<div class='row'>"+
                                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                                            "<span class='glyphicon glyphicon-time' style='font-size: 4em;color:#1565c0;'></span>"+
                                                        "  </div>"+
                                                        "</div>"+
                                                        "<div class='row'>"+
                                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                                            "<h6>Tiempo Respuesta</h6>"+
                                                        "  </div>"+
                                                        "</div>"+
                                                        "<div class='row'>"+
                                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                                            "<h3>"+minutos+" min"+"</h3>"+
                                                        "  </div>"+
                                                        "</div>"+
                                                      "</div>"+
                                                    "</div>";
                                    resultadoDivTiemposCierre =
                                                    "<div class=\"row\">"+
                                                      "<div class=\"col-lg-10\">"+
                                                        "<div class='row'>"+
                                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                                            "<span class='glyphicon glyphicon-time' style='font-size: 4em;color:#7e57c2;'></span>"+
                                                        "  </div>"+
                                                        "</div>"+
                                                        "<div class='row'>"+
                                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                                            "<h6>Tiempo Cierre</h6>"+
                                                        "  </div>"+
                                                        "</div>"+
                                                        "<div class='row'>"+
                                                        "  <div style='text-align:center;' class='col-lg-11'>"+
                                                            "<h3>"+minutosCierre+" min"+"</h3>"+
                                                        "  </div>"+
                                                        "</div>"+
                                                      "</div>"+
                                                    "</div>";
                                }
                            }
                            resultadoDiv+=
                                "<div class=\"row\" style=\"height: 20px\">"+
                                "    <div class=\"col-lg-12\">&nbsp;"+    
                                "    </div>"+
                                "</div>";
                            $("#infoAsignacionAdicDiv").html(resultadoDiv);
                            $("#infoAsignacionTiemposRespDiv").html(resultadoDivTiemposResp);
                            $("#infoAsignacionTiemposCierreDiv").html(resultadoDivTiemposCierre);
                        }
                        obtenerInformacionTecnicaTarea(idTarea);
                },
                failure: function(response){
                        console.log("failure");
                }
            });
}


/**
 * Busca los tipos de problema según tipo de atención enviado por parámetro
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 22-08-2018
 * @since 1.0
 */
function buscaTiposProblema(nombreComboTipoProblema,tipoProblema)
{
    var parametros = {
    };
    $.ajax({
            data :  parametros,
            url  :  url_tipos_problema,
            type :  'post',
            success:  function (response) {
                var arrTiposProblema  = response.data;
                var tiposProblema     = "<option>Seleccione...</option>"; 
                for(var i=0;i<arrTiposProblema.length;i++)
                {
                    tiposProblema += "<option value='"+arrTiposProblema[i].valor2+"'>"+arrTiposProblema[i].valor2+"</option>"; 
                }
                $("#"+nombreComboTipoProblema).html(tiposProblema);
                if(tipoProblema!=="")
                {
                    document.getElementById(nombreComboTipoProblema).value = tipoProblema;
                }
            },
            failure: function(response){
                    console.log("failure");
            }
    });
}

/**
 * Busca los tipos de problema según tipo de atención enviado por parámetro para llenar combo de edición de tipo de problema
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 20-02-2019
 * @since 1.0
 */
function buscaTiposProblemaParaEditar(nombreComboTipoProblema)
{
    var parametros = {
    };
    $.ajax({
            data :  parametros,
            url  :  url_tipos_problema,
            type :  'post',
            success:  function (response) {
                var arrTiposProblema  = response.data;
                var tiposProblema     = ""; 
                for(var i=0;i<arrTiposProblema.length;i++)
                {
                    tiposProblema += "   <li>"+
                                     "     <a href='#'>"+arrTiposProblema[i].valor2+"</a>"+
                                     "   </li>";
                }
                $(nombreComboTipoProblema).html(tiposProblema);
            },
            failure: function(response){
            }
    });
}


 /**
 * Actualización: Se agrega ocultar div de listado de usuarios cuando muestre información de la asignación
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 06-11-2018
 *
 * Actualización: Se cambia nombre de div de graficos a divCharts
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 26-09-2018
 *
 * Muestra divs para mostrar información de la asignación
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 23-07-2018
 * @since 1.0
 */
function mostrarInfoAsignacion()
{
    $('#divCuadroAsignaciones').hide();
    $('#divCuadroAsignacionesUsr').hide();
    $('#divListadoAsignaciones').hide();
    $('#divListadoUsuarios').hide();
    $('#divCharts').hide();
    $('#divInfoAsignacion').show();
    $('#liUsuarios').removeClass('active');
    $('#liCuadro').removeClass('active');
    $('#liCuadroUsr').removeClass('active');
    $('#liInfoAsignacion').show();
    $('#liInfoAsignacion').addClass('active');
    $('#liDetalles').removeClass('active');
    $('#liGrafico1').removeClass('active');
}
    
/**
 * Actualización: Se realiza cambios para que se pueda reutilizar función para mostrar mensaje de alerta según acción enviada por parámetro.
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 12-03-2020
 * 
 * Muestra ventana de confirmación para una acción
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 23-07-2018
 * @since 1.0
 */
function mostrarConfirmacionEliminaRegistro(idRegistro,accion)
{
    var mensajeAlerta = '<table width="500" align="center" cellspacing="3" cellpadding="3" border="0"><tr>'+
                        '<td><h2><span class="glyphicon glyphicon-warning-sign"></span></h2></td>'+
                        '<td><h4> Esta seguro(a) de eliminar el '+accion+'?</h4></td></tr></table>';
    $('#divMensajeConfirmarAlertaSeg').html(mensajeAlerta);
    document.getElementById('txtTipoAccionEliminar').value    = accion;
    document.getElementById('txtIdSeguimientoEliminar').value = idRegistro;
    $('#modalConfirmarAlertaSeguimiento').modal('show');
}

/**
 * Muestra formulario para el ingreso de nueva asignacion
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 23-07-2018
 * @since 1.0
 */
function mostrarNuevaAsignacionUsr(usrAsignado)
{
    $('#modalNuevaAsignacion').modal('show');
    document.getElementById('txtAgente').value = usrAsignado;
    buscaTiposProblema("cmbTipoProblema","");
}

/**
 * Muestra formulario para el ingreso de nueva asignación, ingresado a partir de una tarea
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 25-02-2019
 * @since 1.0
 */
function mostrarNuevaAsignacionTarea(tarea)
{    
    var objTarea = JSON.parse(tarea);
    $('#alertaValidaNuevaAsignacion').hide();
    $('#modalNuevaAsignacion').modal('show');
    document.getElementById('txtAgente').value                   = objTarea.usrAsignado;
    document.getElementById('txtLogin').value                    = objTarea.loginAfectado;
    document.getElementById('txtNumeroTareaCasoNuevaAsig').value = objTarea.numeroTarea;
    document.getElementById('txtaDetalle').value                 = objTarea.observacionTarea;
    document.getElementById('cmbTipoAtencion').value             = "TAREA";
    document.getElementById('cmbOrigen').value                   = "TAREA INTERNA";
    document.getElementById('cmbCriticidad').value               = "ALTA";
    $('#btnGrabarAsignacion').removeAttr('disabled');
    $('#btnGrabarAsignacion').show();
    $('#btnLoadingGrabarAsig').hide();
    $('#divCmbOrigen,#divCmbCriticidad,#divCmbTipoAtencion,#divCmbTipoProblema,#divtxtAgente,#divtxtaDetalle').removeClass('has-error');
    buscaTiposProblema("cmbTipoProblema","");
}

/**
 * Se añade funcionalidad para actualizar asignación cuando se ejecuta una tarea de otro usuario
 * @author Fernando López <filopez@telconet.ec>
 * @version 2.0 15-12-2021
 * 
 * Muestra formulario para la gestión de la tarea seleccionada
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.0 13-09-2021
 * @since 1.0
 */
 function mostrarEjecucionTarea(tarea)
 {
     var objTarea = JSON.parse(tarea);
     $('#modalValidaTareasAbiertas').modal('show');
     $('#alertaValidaEjecucionTarea').hide();
     $('#divtxtaMotivoPausa').hide();
     $("#txtTarea").text(objTarea.nombreTarea);

     document.getElementById('txtDetalleTarea').value             = objTarea.idDetalle;
     document.getElementById('txtTiempo').value                   = objTarea.minutosTranscurridos;
     document.getElementById('txtPersonaEmpresaRol').value        = objTarea.idPersonaEmpresaRol;
     document.getElementById('txtNumeroTarea').value              = objTarea.numeroTarea;

     document.getElementById('txtNombreProceso').value            = objTarea.nombreProceso;
     document.getElementById('txtUsrAsignado').value              = objTarea.usrAsignado;
     document.getElementById('txtDeptAsignado').value             = objTarea.asignado;
     document.getElementById('txtDetalleHist').value              = objTarea.idDetalleHist;
     document.getElementById('txtNombreUsrAsignado').value        = objTarea.nombreUsrAsignado;

    var nombreTarea = $('#txtTarea').text();
    var numeroTarea = $('#txtNumeroTarea').val();
    var detalleTarea = $('#txtDetalleTarea').val();
    var tiempoTarea = $('#txtTiempo').val();
    var nombreProceso = $('#txtNombreProceso').val()
    var usrAsignado = $('#txtUsrAsignado').val()
    var deptAsignado = $('#txtDeptAsignado').val()
    var detalleHist = $('#txtDetalleHist').val();
    var nombreUsrAsignado = $('#txtNombreUsrAsignado').val();

    // fix para actualizar una asignación cuando se ejecute una tarea de otro usuario
    objAsignacionActualizar = "";
    if($('#divListadoAsignaciones').css('display') !== 'none' && $('#liDetalles').hasClass('active') == true 
        && permiteVerNuevosCamposTareas == 1 && objTarea.id !='' && objTarea.accionTarea !=='pausar'
        && objTarea.strUsuarioSession !== objTarea.usrAsignado)
    {
        
        objAsignacionActualizar = objTarea;
    }

    validarTareasAbiertas(objTarea.accionTarea, detalleTarea, nombreTarea, tiempoTarea, numeroTarea, nombreProceso, usrAsignado, deptAsignado, detalleHist, nombreUsrAsignado);
 }

 /**
 * Muestra formulario para ingreso se seguimiento
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.0 13-09-2021
 * @since 1.0
 */
  function mostrarIngresoSeguimiento(tarea)
  {
      var objTarea = JSON.parse(tarea);
      $('#modalIngresoSeguimiento').modal('show');
      $('#alertaValidaIngresoSeguimiento').hide();
      $("#txtTareaSeg").text(objTarea.nombreTarea);
 
      document.getElementById('txtDetalleTareaSeg').value             = objTarea.idDetalle;
      document.getElementById('txtTiempoSeg').value                   = objTarea.minutosTranscurridos;
      document.getElementById('txtPersonaEmpresaRolSeg').value        = objTarea.idPersonaEmpresaRol;
      document.getElementById('txtNumeroTareaSeg').value              = objTarea.numeroTarea;
 
      document.getElementById('txtNombreProcesoSeg').value            = objTarea.nombreProceso;
      document.getElementById('txtUsrAsignadoSeg').value              = objTarea.usrAsignado;
      document.getElementById('txtDeptAsignadoSeg').value             = objTarea.asignado;
      document.getElementById('txtDetalleHistSeg').value              = objTarea.idDetalleHist;
      document.getElementById('txtNombreUsrAsignadoSeg').value        = objTarea.nombreUsrAsignado;

  }

  /**
 * Muestra formulario para seguimientos
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.0 13-09-2021
 * @since 1.0
 */
   function mostrarSeguimiento(tarea)
   {
       var objTarea = JSON.parse(tarea);
       $('#modalMostrarSeguimiento').modal('show');

        $.ajax({
            data :  {id_detalle: objTarea.idDetalle},
            url: '../info_caso/verSeguimientoTarea',
            type :  'GET',
            success:  function (response) {
                var arrSeguimientos  = response.encontrados;
                var seguimientos     = "";
                if(response.total > 0)
                {
                    for(var i=0;i<arrSeguimientos.length;i++)
                    {
                        seguimientos += "<tr>";
                        seguimientos += "<td>"+arrSeguimientos[i].observacion+"</td>";
                        seguimientos += "<td>"+arrSeguimientos[i].empleado+"</td>";
                        seguimientos += "<td>"+arrSeguimientos[i].departamento+"</td>";
                        seguimientos += "<td>"+arrSeguimientos[i].fecha+"</td>";
                        seguimientos += "</tr>";
                    }
                }
                $("#tbodyMostrarSeguimiento").html(seguimientos);
            }
    });

   }

/**
 * Muestra formulario para el ingreso de asignaciones por lotes
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.0 01-07-2021
 * @since 1.0
 */
 function mostrarAsignacionTareaMasiva()
 {
    var arrayElementos = [];
    var dataTable = $('#detalleTareas').dataTable();

    $(dataTable.fnGetNodes()).each(function(){
        var clases = this.className;
        if(clases.indexOf("claseCheck") !== -1){
            var celda      = $(this).find("td:eq(1)");
            var nodo       = celda[0].childNodes;
            arrayElementos.push(nodo[0].textContent);
        }
    });

    $('input[name=tareasSeleccionadas]').val(arrayElementos);
    $('#tareasSeleccionadas').val();
    $('#alertaValidaNuevaAsignacion').hide();
    $('#modalAsignacionLote').modal('show');

     document.getElementById('cmbTipoAtencion').value             = "TAREA";
     document.getElementById('cmbOrigen').value                   = "TAREA INTERNA";
     document.getElementById('cmbCriticidad').value               = "ALTA";
     buscaTiposProblema("cmbTipoProblemaLote","");
}

/**
 * 
 * Se limpia campos antes de presentar la ventana
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 11-01-2019
 * 
 * Muestra formulario para el ingreso del numero de tarea
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 23-07-2018
 * @since 1.0
 */
function mostrarAgregarNumeroTarea(idAsignacion, tipoProblema, tipoAtencion)
{
    $('#alertaValidaNumeroTarea').hide();
    document.getElementById('txtNumeroTareaCaso').value="";
    document.getElementById('txtIdAsignacionEdit').value="";
    $('#modalAgregarNumeroTarea').modal('show');
    document.getElementById('txtIdAsignacionEdit').value = idAsignacion;
    document.getElementById('cmbTipoAtencionEdit').value = tipoAtencion;
    buscaTiposProblema("cmbTipoProblemaEdit",tipoProblema);
}

/**
 * Muestra formulario para la asignacion de seguimientos
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 02-08-2018
 * @since 1.0
 */
function mostrarAsignarSeguimiento(idAsignacion,tareas,tipoAtencion)
{    
    var arrayTareas = tareas.split("|");
    var tareasCmb   = "";
    $("#cmbTareaAsigSeg").html("");
    if (tipoAtencion === "CASO")
    {
        tareasCmb = "<option value=''>Seleccione...</option>";
        $("#divCmbTareaAsigSeg").show();
        for (var indice=0; indice < arrayTareas.length; indice++)
        {
            if (arrayTareas[indice] !== "")
            {
                tareasCmb += "<option value="+arrayTareas[indice]+">"+arrayTareas[indice]+"</option>";
            }
        }
    }
    else
    {
        $("#divCmbTareaAsigSeg").hide();
    }
    $("#cmbTareaAsigSeg").html(tareasCmb);
    $('#modalAsignarSeguimiento').modal('show');
    document.getElementById('txtIdAsignacionAsigSeg').value = idAsignacion;
    autocomplete(document.getElementById("txtAgenteAsigSeg"), agentes);
}

/**
 * Muestra formulario para poner asignación en standby
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 18-05-2020
 * @since 1.0
 */
function mostrarPonerStandby(idAsignacion)
{
    $('#modalPonerStandby').modal('show');
    document.getElementById('txtIdAsignacionPonerStandby').value = idAsignacion;
    document.getElementById('txtDetallePonerStandby').value = '';
    document.getElementById('txtIdBtnHorasCambioTurno').value = '';
    $("#textBtnHorasCambioTurno").text("Seleccione...");
    document.getElementById('txtFechaPonerStandby').value = '';
}
/**
 * Muestra formulario para quitar asignación de standby
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 18-05-2020
 * @since 1.0
 */
function mostrarQuitarStandby(idAsignacion)
{
    $('#modalQuitarStandby').modal('show');
    document.getElementById('txtIdAsignacionQuitarStandby').value = idAsignacion;
    document.getElementById('txtAgenteQuitarStandby').value      = '';
    document.getElementById('txtDetalleQuitarStandby').value      = '';
    autocomplete(document.getElementById("txtAgenteQuitarStandby"), agentes);
}
/**
 * 
 * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
 * @version 1.0 24-06-2019
 * @since 1.0
 */
function asignacionHijaPadre(idAsigPadre)
{
    $('#btnAceptarAsigProact').hide();
    $('#btnCancelarAsigProact').hide();
    $('#btnAceptarAsigProact_1').show();
    $('#btnCancelarAsigProact_1').show();
    document.getElementById('txtIdAsigPadre').value = idAsigPadre;
    $('#modalAsignacionProactiva').modal('show');
    detalleAsignacionesProactivas.clear().draw();
    detalleAsignacionesProactivas.ajax.url(url_detalle_asignaciones+"?asigProactivas='S'").load();
}


  /**
   * Funcion mostrar los seguimientos de las.
   * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
   * @version 1.0 27-06-2019
   * @since 1.0
   */
function mostrarAsignacionHija(idAsignacionPadre)
    {
        $('#btnAceptarAsigProact').hide();
        $('#btnCancelarAsigProact').hide();
        $('#btnAceptarAsigProact_1').hide();
        $('#btnCancelarAsigProact_1').show();
        $('#modalAsignacionHija').modal('show');
        detalleAsignacionesHijas.ajax.url(url_detalle_asignaciones+"?asignacionConsultaHijas=S"+
                                                                   "&intPadreId="+idAsignacionPadre).load();
    };

    
/**
 * Muestra ventana de confirmación para una acción
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 23-07-2018
 * @since 1.0
 */
function mostrarConfirmacionEliminaAsig(idAsignacion)
{
    var mensajeAlerta = '<table width="500" align="center" cellspacing="3" cellpadding="3" border="0"><tr>'+
                        '<td><h2><span class="glyphicon glyphicon-warning-sign"></span></h2></td>'+
                        '<td><h4> Esta seguro(a) de eliminar la asignaci&oacute;n?</h4></td></tr></table>';
    $('#divMensajeConfirmarAlertaAsig').html(mensajeAlerta);
    document.getElementById('txtIdAsigSegEliminar').value = idAsignacion;
    $('#modalConfirmarAlertaAsignacion').modal('show');
    
}

/**
 * Muestra ventana de confirmación para cerrar una asignación
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 30-01-2019
 * @since 1.0
 */
function mostrarConfirmacionCerrarAsig(idAsignacion, cambioTurno)
{
    if (cambioTurno === 'N')
    {
        var mensajeAlerta = '<table width="500" align="center" cellspacing="3" cellpadding="3" border="0"><tr>'+
                            '<td><h2><span class="glyphicon glyphicon-warning-sign"></span></h2></td>'+
                            '<td><h4> Esta seguro(a) de cerrar la asignaci&oacute;n?</h4></td></tr></table>';
        $('#divMensajeConfirmarCerrarAsig').html(mensajeAlerta);
        document.getElementById('txtIdAsigCerrar').value = idAsignacion;
        $('#modalConfirmarCerrarAsignacion').modal('show');
    }
    else
    {
        $('#modalAlertaError').modal('show');
        $("#divMensajeAlertaError").html("<img src=\"/public/images/aviso.png\" width=\"50\" height=\"50\" />"+
                                         "<strong>"+"No se puede cerrar asignaciones que esten marcadas como cambio de turno!"+"</strong>");
    }
}

/**
 * Muestra ventana de confirmación para enviar reporte de asignaciones pendientes
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 14-02-2019
 * @since 1.0
 */
function mostrarConfirmacionEnviarReporte()
{
    var mensajeAlerta = '<table width="550" align="center" cellspacing="3" cellpadding="3" border="0"><tr>'+
                        '<td><h2><span class="glyphicon glyphicon-warning-sign"></span></h2></td>'+
                        '<td><h4> Esta seguro(a) de enviar reporte de asignaciones pendientes?</h4></td></tr></table>';
    $('#divMensajeConfirmarEnviarReporte').html(mensajeAlerta);
    $('#modalConfirmarEnviarReporte').modal('show');
}

/**
 * Muestra ventana de confirmación para grabar una asignación
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 21-02-2019
 * @since 1.0
 */
function mostrarConfirmacionGrabarAsignacion(mensaje)
{
    var mensajeAlerta = '<table width="550" align="center" cellspacing="3" cellpadding="3" border="0">'+
                        '  <tr>'+
                        '    <td>'+
                        '      <h2><span class="glyphicon glyphicon-warning-sign" style="margin:15px;font-size:40px;"></span></h2>'+
                        '    </td>'+
                        '    <td>'+
                        '      <h4>'+mensaje+'</h4>'+
                        '    </td>'+
                        '  </tr>'+
                        '</table>';
    $('#divMensajeConfirmarGrabarAsignacion').html(mensajeAlerta);
    $('#modalConfirmarGrabarAsignacion').modal('show');
}

/**
 * 
 * Se limpia campos antes de presentar la ventana
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 11-01-2019
 * 
 * Muestra formulario para la edición de número de extensión
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 24-10-2018
 * @since 1.0
 */
function mostrarEditarNumeroExtension(idPersonaEmpresaRol,extension,bandActualizaEstConex, estadoConexion)
{
    $('#alertaValidaNumeroExtension').hide();
    document.getElementById('txtIdPersonaEmpresaRolEdit').value      = "";
    document.getElementById('txtNumeroExtensionEdit').value          = "";
    document.getElementById('txtActualizarEstadoConexionEdit').value = "";
    document.getElementById('txtEstadoConexionEdit').value           = "";
    $('#modalEditarNumeroExtension').modal('show');
    document.getElementById('txtNumeroExtensionEdit').value          = extension;
    document.getElementById('txtIdPersonaEmpresaRolEdit').value      = idPersonaEmpresaRol;
    document.getElementById('txtActualizarEstadoConexionEdit').value = bandActualizaEstConex;
    document.getElementById('txtEstadoConexionEdit').value           = estadoConexion;
}

/**
 * 
 * Muestra el modal con el detalle de historial de conexión
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 18-03-2020
 * @since 1.0
 */
function mostrarHistorialConexion(idPer)
{   
    var fecha = new Date();
    var mes   = fecha.getMonth()+1;
    var anio  = fecha.getFullYear();
    document.getElementById("cmbMesHistorialConexion").value = mes;
    document.getElementById("cmbAnioHistorialConexion").value = anio;
    document.getElementById("txtIdPerHistConexion").value = idPer;
    $('#modalHistorialConexion').modal('show');
    detalleHistorialConexion.ajax.url(url_historial_conexion_usr+"?idPer="+idPer+"&mes="+mes+"&anio="+anio).load();

}

/**
 * 
 * Muestra formulario para la edición de tipo de problema
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 10-03-2020
 * @since 1.0
 */
function mostrarEditarTipoProblema(idTipoProblema,descripcion,detalle,departamento)
{
    $('#alertaValidaAdminTipoProblema').hide();
    document.getElementById('txtAccionAdminTipoProblema').value       = 'EDITAR';
    document.getElementById('txtIdAdminTipoProblema').value           = idTipoProblema;
    document.getElementById('txtDetalleAdminTipoProblema').value      = detalle;
    document.getElementById('txtDescripcionAdminTipoProblema').value  = descripcion;
    document.getElementById('txtDepartamentoAdminTipoProblema').value = departamento;
    $('#modalAdminTipoProblema').modal('show');
}

/**
 * Muestra en una ventana el detalle de tareas enviada por parámetro.
 * @author Andrés Montero <amontero@telconet.ec>
 * @param tareas     => tareas que se desea presentar.
 * @param numeroCaso => número del caso
 * @version 1.0 29-02-2019
 * @since 1.0
 */
function mostrarTareasCaso(tareas,numeroCaso)
{
    $('#detalleTareasCasoDiv').html('');
    $('#modalTareasCaso').modal('show');
    $('#myModalLabelTareasCaso').html('Listado de Tareas del Caso No.'+numeroCaso);
    $('#detalleTareasCasoDiv').html(decodeTareasInfoAsignacion(tareas,false));
}

/**
 * Recopila cada asignación proactiva marcada y desmarcada
 * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>|
 * @version 1.0 17-06-2019
 */
$("body").on("click",".checkbox", function() {
    
    var asignacionProactiva = {id:$(this).val()};
    
    if(this.checked)
    {
        arrayAsignacionProactiva.push(asignacionProactiva);
    }
    else
    {
        arrayAsignacionProactiva = arrayAsignacionProactiva.filter(
            elementoasignacionProactiva => elementoasignacionProactiva['id'] !== asignacionProactiva.id);
    }
});


    /**
     * Realiza la relacion de Asignaciones hijas(Proactivas) con Asignaciones padre
     * @author Miguel Angulo Sanchez <jmangulos@telconet.ec>
     * @version 1.0 24-06-2019
     */
    function relacionAsiganacionHijaPadre()
    {
        var inIdAsigPadre = document.getElementById('txtIdAsigPadre').value;
        var arrayAsigHija = Ext.JSON.encode(arrayAsignacionProactiva);
        
        var parametros = {
                            "intIdAsigPadre"  : inIdAsigPadre,
                            "arrayIdAsigHija" : arrayAsigHija
                         };
        $.ajax({
                data :  parametros,
                url  :  url_asignacion_hija_padre,
                type :  'post', 
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                     Ext.Msg.alert('Relación de Asignaciones',response);
                     arrayAsignacionProactiva=[];
                     document.getElementById("btnBuscarPorFecha").onclick();
                },
                failure: function(response){   
                        arrayAsignacionProactiva=[];
                        Ext.Msg.alert(response);
                }
        });
    }


/**
 * Muestra ventana para poder ingresar un seguimiento por cambio de turno
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 27-02-2019
 * @since 1.0
 */
function mostrarNuevoSeguimientoCambioTurno(strCambioTurno, intIdAsignacion)
{
    if (strCambioTurno === 'S')
    {
        $('#modalIngresarSeguimientoCambioTurno').modal('show');
        document.getElementById('txtIdAsignacionNuevoSegCambioTurno').value = intIdAsignacion;
        document.getElementById('txtCambioTurno').value                     = strCambioTurno;
        document.getElementById('txtaDetalleSegCambioTurno').value          = "";
    }
    else
    {
        desmarcarCambioTurno(
                              strCambioTurno,
                              intIdAsignacion,
                              "Se desmarca cambio de turno"
                             );
    }
    
}
/**
 * Actualización: Se modifica la consulta para considerar los campos 
 *                de fecha al realizar una nueva consulta.
 * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
 * @version 1.2 18-06-2019
 * 
 * Actualización: Se configura mensaje de eliminación fallida
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 26-09-2018
 *
 * Graba numero de tarea en la asignación
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 16-08-2018
 * @since 1.0
 */
function eliminarAsignacion()
{
    var idAsignacion = document.getElementById('txtIdAsigSegEliminar').value; 
    if (idAsignacion !== null )
    {
        var parametros = {
            "intId" : idAsignacion
        };
        $.ajax({
                data :  parametros,
                url  :  url_elimina_asignacion,
                type :  'post',
                beforeSend: function () {
                        $('#btnLoadingEliminarAsignacion').show();
                        $('#confirmaEliminarAsignacion').hide();
                },
                success:  function (response) {
                        if(response==="OK")
                        {
                            
                            configuraMensajeIngresoConExito(
                                '#alertaConfirmaEliminarAsignacion',
                                '<strong>Se eliminó la asignación con éxito!</strong>',
                                '#btnLoadingEliminarAsignacion',
                                '#confirmaEliminarAsignacion'
                            );

                            //se cierra ventana luego de 2 segundos
                             setTimeout(function() {
                                 $('#modalConfirmarAlertaAsignacion').modal('hide');
                                 $('#alertaConfirmaEliminarAsignacion').hide();
                             }, 2000);
                            document.getElementById("btnBuscarPorFecha").onclick();
                            document.getElementById('txtIdAsigSegEliminar').value = null; 
                        }
                        else
                        {
                            configuraMensajeIngresoFallido('#alertaConfirmaEliminarAsignacion',
                                                           '<strong>'+response+'</strong>',
                                                           '#btnLoadingEliminarAsignacion',
                                                           '#confirmaEliminarAsignacion');
                        }
                },
                failure: function(response){
                    configuraMensajeIngresoFallido('#alertaConfirmaEliminarAsignacion',
                                                   '<strong>'+response+'</strong>',
                                                   '#btnLoadingEliminarAsignacion',
                                                   '#confirmaEliminarAsignacion');
                }
        });
    }
}

/**
 * Actualización: Se modifica la consulta para considerar los campos 
 *                de fecha al realizar una nueva consulta.
 * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
 * @version 1.1 18-06-2019
 * 
 * Ejecuta el proceso para cambiar estado a cerrado de una asignación
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 30-01-2019
 * @since 1.0
 */
function cerrarAsignacion()
{
    var idAsignacion = document.getElementById('txtIdAsigCerrar').value;
    if (idAsignacion !== null )
    {
        var parametros = {
            "intId"     : idAsignacion,
            "strEstado" : 'Cerrado',
            "strTipo"   : 'estado'
        };
        $.ajax({
                data :  parametros,
                url  :  url_modificar_asignacion,
                type :  'post',
                beforeSend: function () {
                        $('#btnLoadingCerrarAsignacion').show();
                        $('#confirmaCerrarAsignacion').hide();
                },
                success:  function (response) {
                        if(response==="OK")
                        {
                            configuraMensajeIngresoConExito(
                                '#alertaConfirmaCerrarAsignacion',
                                '<strong>Se cerro la asignación con éxito!</strong>',
                                '#btnLoadingCerrarAsignacion',
                                '#confirmaCerrarAsignacion'
                            );

                            //se cierra ventana luego de 2 segundos
                             setTimeout(function() {
                                 $('#modalConfirmarCerrarAsignacion').modal('hide');
                                 $('#alertaConfirmaCerrarAsignacion').hide();
                             }, 2000);
                            document.getElementById("btnBuscarPorFecha").onclick();
                            document.getElementById('txtIdAsigCerrar').value = null;
                        }
                        else
                        {
                            configuraMensajeIngresoFallido('#alertaConfirmaCerrarAsignacion',
                                                           '<strong>'+response+'</strong>',
                                                           '#btnLoadingCerrarAsignacion',
                                                           '#confirmaCerrarAsignacion');
                        }
                },
                failure: function(response){
                    configuraMensajeIngresoFallido('#alertaConfirmaCerrarAsignacion',
                                                   '<strong>'+response+'</strong>',
                                                   '#btnLoadingCerrarAsignacion',
                                                   '#confirmaCerrarAsignacion');
                }
        });
    }
}


/**
 * Actualización: Se modifica la consulta para considerar los campos 
 *                de fecha al realizar una nueva consulta.
 * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
 * @version 1.2 18-06-2019
 * 
 * Actualización: Se agrega programación para que obtenga los campos de cambio de turno y id de asignación desde los
 *                campos de texto de la ventana de ingreso de seguimiento por cambio de turno
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 27-02-2019
 * 
 * Marca el cambio de turno para asignación
 * @param strCambioTurno        => Indica si se cambia de turno a la asignación.
 * @param intIdAsignacion       => Referencia al id de la asignación.
 * @param strDetalleSeguimiento => Detalle del seguimiento por cambio de turno.
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 24-07-2018
 * @since 1.0
 */
function marcarCambioTurno(strCambioTurno, intIdAsignacion, strDetalleSeguimiento)
{
    if (strDetalleSeguimiento !== "")
    {
        var parametros = {
            "strCambioTurno" : strCambioTurno,
            "intId"          : intIdAsignacion,
            "strDetalle"     : strDetalleSeguimiento
        };
        $.ajax({
                data :  parametros,
                url  :  url_marcar_cambio_turno,
                type :  'post',
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                        $("#btnGrabarSeguimientoCambioTurno").hide();
                        $("#btnLoadingGrabarNuevoSeguimientoCambioTurno").show();
                },
                success:  function (response) {
                    if(response === "OK")
                    {
                        $("#resultado").html(response);
                        configuraMensajeIngresoConExito(
                            '#alertaValidaNuevoSeguimientoCambioTurno',
                            '<strong>Se marcó asignación a cambio de turno con éxito!</strong>',
                            '#btnLoadingGrabarNuevoSeguimientoCambioTurno',
                            '#btnGrabarSeguimientoCambioTurno'
                        );
                        //se cierra ventana luego de 2 segundos
                         setTimeout(function() {
                             $('#modalIngresarSeguimientoCambioTurno').modal('hide');
                             $('#alertaValidaNuevoSeguimientoCambioTurno').hide();
                         }, 2000);
                        document.getElementById("btnBuscarPorFecha").onclick();
                    }
                    else
                    {
                        configuraMensajeIngresoFallido('#alertaValidaNuevoSeguimientoCambioTurno',
                                                       '<strong>Ocurrio un error al intentar marcar cambio de turno!</strong>',
                                                       '#btnLoadingGrabarNuevoSeguimientoCambioTurno',
                                                       '#btnGrabarSeguimientoCambioTurno');                        
                    }
                    document.getElementById('txtIdAsignacionNuevoSegCambioTurno').value = "";
                    document.getElementById('txtaDetalleSegCambioTurno').value          = "";
                    document.getElementById('txtCambioTurno').value                     = "";
                },
                failure: function(response){
                    configuraMensajeIngresoFallido('#alertaValidaNuevoSeguimientoCambioTurno',
                                                   '<strong>'+response+'</strong>',
                                                   '#btnLoadingGrabarNuevoSeguimientoCambioTurno',
                                                   '#btnGrabarSeguimientoCambioTurno');
                }
        });
    }
    else
    {
        configuraMensajeIngresoFallido('#alertaValidaNuevoSeguimientoCambioTurno',
                                       '<strong>Debe ingresar un detalle para el cambio de turno!</strong>',
                                       '#btnLoadingGrabarNuevoSeguimientoCambioTurno',
                                       '#btnGrabarSeguimientoCambioTurno');        
    }
}

/**
 * Actualización: Se modifica la consulta para considerar los campos 
 *                de fecha al realizar una nueva consulta.
 * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
 * @version 1.1 18-06-2019
 * 
 * Desmarca el cambio de turno para una asignación
 * @param strCambioTurno        => Indica si se cambia de turno a la asignación.
 * @param intIdAsignacion       => Referencia al id de la asignación.
 * @param strDetalleSeguimiento => Detalle del seguimiento por cambio de turno.
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 27-02-2019
 * @since 1.0
 */
function desmarcarCambioTurno(strCambioTurno, intIdAsignacion, strDetalleSeguimiento)
{
    var parametros = {
        "strCambioTurno" : strCambioTurno,
        "intId"          : intIdAsignacion,
        "strDetalle"     : strDetalleSeguimiento
    };
    $.ajax({
            data :  parametros,
            url  :  url_marcar_cambio_turno,
            type :  'post',
            success:  function (response) {
                if(response === "OK")
                {
                    $("#resultado").html(response);
                    document.getElementById("btnBuscarPorFecha").onclick();
                }
                else
                {
                }
            },
            failure: function(response){
                $('#modalAlertaError').modal('show');
                $("#divMensajeAlertaError").html("<img src=\"/public/images/aviso.png\" width=\"50\" height=\"50\" />"+
                                                 "<strong>"+"Ocurrio un error, mo se pudo desmarcar la asignación de cambio de turno!"+"</strong>");
            }
    });

}


/**
 * Muestra formulario para respuesta de la asignacion de seguimientos
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 08-08-2018
 * @since 1.0
 */
function mostrarRespuestaAsignarSeguimiento(idAsignacion, idSeguimiento)
{
    $('#modalRespuestaAsignarSeguimiento').modal('show');
    document.getElementById('txtIdRespuestaAsignacionAsigSeg').value  = idAsignacion;
    document.getElementById('txtIdSeguimientoRespuestaAsigSeg').value = idSeguimiento;
}

//llamamos a plugin de bootstrap datetimepicker
$(function () {
    $('#datetimepickerBuscaAsignacion').datetimepicker({
                 format: 'DD-MM-YYYY'
    });  
    
    $('#datetimepickerBuscaAsignacionFin').datetimepicker({
                 format: 'DD-MM-YYYY'
    }); 
    
    $('#datetimepickerBuscaAsignacionTarea').datetimepicker({
        format: 'DD-MM-YYYY'
    });

    $('#datetimepickerBuscaAsignacionFinTarea').datetimepicker({
            format: 'DD-MM-YYYY'
    });
});
/*
* Muestra la busqueda avanzada enviando el login
* @author Andrés Montero <amontero@telconet.ec>
* @version 1.1 23-09-2018
**/
function mostrarBusquedaAvanzada(paramBusqueda)
{
    showBusquedaAvanzada();
    if($.isNumeric( paramBusqueda ))
    {
        document.getElementById('login_punto_avanzada-inputEl').value ="";
        document.getElementById('identificacion_cliente_avanzada-inputEl').value =paramBusqueda;
    }
    else
    {
        document.getElementById('identificacion_cliente_avanzada-inputEl').value ="";
        document.getElementById('login_punto_avanzada-inputEl').value =paramBusqueda;
    }
    buscarAvanzada();
}
 
$(document).ready(function() {
    var columnas   =[];
    var columnasUsr=[];

    $('[data-toggle="tooltip"]').tooltip();

    //Notificaciones con push.js
    
    /**
     * Actualización: se suspende tiempo de actualización automática
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 29-11-2018
     * 
     * Configura la actualización automática de busqueda de asignaciones pendientes a cada minuto
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
    */
    clearInterval(
        setInterval( 
            function () {
                            buscaPendientes();
                        }, 
            60000 
        )
    );


    //Define los detalles de seguimientos
    detalleSeguimientos =   $('#detalleSeguimientos').DataTable( {
            "columns": [
                {"data": "numero",
                "title": "#"
                },
                {"data": "feCreacion",
                "title": "Fecha"
                },
                {"data": "usrCreacion",
                "title": "Usuario"
                },
                {"data": "detalle",
                "title": "Detalle"
                },
                {"data": "procedencia",
                 "title": "Procedencia",
                 "render":function (data, type, full, meta)
                          {
                              var strDatoRetorna= '<span class="label label-default"><span class="glyphicon glyphicon-flag"></span></span>';
                              if(full.procedencia==="interno")
                              {
                                  strDatoRetorna= '<span class="label label-default"><span class="glyphicon glyphicon-flag"></span></span>';
                              }
                              else if(full.procedencia==="sincronizado")
                              {
                                  strDatoRetorna= '<span class="label label-success"><span class="glyphicon glyphicon-flag"></span></span>';
                              }
                              else if(full.procedencia==="externo")
                              {
                                  strDatoRetorna= '<span class="label label-primary"><span class="glyphicon glyphicon-flag"></span></span>';
                              }
                              return strDatoRetorna;
                          }
                },
                {"data" : "acciones",
                 "title": "Acciones",
                 "render": function (data, type, full, meta)
                           {
                               var strDatoRetorna    = "";
                               if(
                                   (full.procedencia==="interno") &&
                                   (muestraBtnEliminarSeguimiento) &&
                                   ($('#vistaSoporte').text().trim() === 'Vista Administrador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Jefe'
                                   )
                               )
                               {
                                   strDatoRetorna    =     '<span class="hint--bottom-right hint--default hint--medium'+
                                                           ' hint--rounded" aria-label="Eliminar Seguimiento">'+
                                                           '<button type="button" class="btn btn-default btn-xs" '+
                                                           '     onClick="javascript:mostrarConfirmacionEliminaRegistro(\''+
                                                           full.idSeguimientoAsig+'\',\''+
                                                           'seguimiento'+'\');">'+
                                                           '    <span class="glyphicon glyphicon-remove"></span>'+
                                                           '</button></span>';
                               }
                               return strDatoRetorna;
                           }
                }
            ],
            "columnDefs":
            [
                {"className": "dt-center", "targets": [0,1,2,4,5]},
                {"className": "dt-left", targets: [3]},
                {
                    "targets": [0,1,2,3,4],
                    "createdCell": function (td, cellData, rowData, row, col) 
                    {
                        $(td).css('padding', '12px')
                    }
                }
            ],
            "paging":false,
            "searching":false
        } )
        .on('preXhr.dt', function ( e, settings, data ) {
           $("#divLoaderSeguimientos").show();
        } )
        .on('xhr.dt', function ( e, settings, json, xhr ) {
           $("#divLoaderSeguimientos").hide();
        })
    ;


    //Define los detalles de historial
    detalleHistorial =   $('#detalleHistorial').DataTable( {
            /*"ajax": {
                     "url": url_detalle_historial,
                     "data":{} 
                    },*/
            "columns": [
                {"data": "numero","title": "#"},    
                {"data": "feCreacion","title": "Fecha"},
                {"data": "usrAsignado","title": "Usuario"},
                {"data": "tipo","title": "Tipo"}
            ],
            "columnDefs":
            [
                {"className": "dt-center", "targets": "_all"}
            ],
            "paging"   : false,
            "searching": false,
            "info"     : false
        } );



    //define max height la ventana de ingreso de seguimientos
    $('#modalIngresarSeguimiento').on('show.bs.modal', function() {
      $(this).find('.modal-body').css({
        'max-height': '100%'
      });
    });
    
    //llenar la informacion de la asignación
    //obtenerInformacionAsignacion();


    /**
     * Graba una nuevo seguimiento por medio un ajax
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 20-07-2018
     * @since 1.0
     */
    function grabarNuevoSeguimiento()
    {
        var replicarTarea = "N";
        if (document.getElementById("replicarTarea").checked)
        {
            replicarTarea = "S";
        }
            var parametros = {
                "strDetalle" : document.getElementById('txtaDetalleSeg').value,
                "intIdTarea" : document.getElementById("idTareaCasoInfoAsignacion").value,
                "intId"      : document.getElementById("idAsignacionInfo").value,
                "sync"       : replicarTarea,
            };
            $.ajax({
                    data :  parametros,
                    url  :  url_crea_seguimiento,
                    type :  'post',
                    beforeSend: function () {
                            $('#btnLoadingGrabarNuevoSeguimiento').show();
                            $('#btnGrabarSeguimiento').hide();
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
                                 document.getElementById('replicarTarea').checked = false;
                                 var arrayCampos                                  = ['txtaDetalleSeg'];
                                 limpiarCampos('#alertaValidaNuevoSeguimiento',arrayCampos);
                             }, 2000);

                            detalleSeguimientos.ajax.reload();
                            
                    },
                    failure: function(response){
                            console.log("failure");
                    }
            });
    }

    /**
     * 
     * Actualización: Se realiza reutilización de procedimiento para eliminar en la misma función seguimientos de una asignación y tipos de problema.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 12-03-2020
     * 
     * Elimina seguimiento de asignacion
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 16-08-2018
     * @since 1.0
     */
    function eliminarRegistro(accion)
    {
        var idRegistro = document.getElementById('txtIdSeguimientoEliminar').value;
        var url        = "";
        var parametros = {};

        if (idRegistro !== null )
        {
            if (accion === 'tipo problema')
            {
                parametros = {
                    "intIdParametroDet" : idRegistro
                };
                url = url_eliminar_admi_parametro_det;
            }
            else if (accion === 'seguimiento')
            {
                parametros = {
                    "intId" : idRegistro
                };
                url = url_elimina_seguimiento;
            }

            $.ajax({
                    data :  parametros,
                    url  :  url,
                    type :  'post',
                    beforeSend: function () {
                            $('#btnLoadingEliminarSeguimiento').show();
                            $('#btnConfirmaEliminarSeguimiento').hide();
                    },
                    success:  function (response) {
                            var respuesta        = response;
                            var mensajeOk        = "OK";
                            var detalleRegistros = detalleSeguimientos;
                            if (accion === 'tipo problema')
                            {
                                respuesta = response.strStatus;
                                mensajeOk = "100";
                                detalleRegistros = detalleTiposProblema;
                            }

                            if(respuesta === mensajeOk)
                            {
                                configuraMensajeIngresoConExito(
                                    '#alertaConfirmaEliminarSeguimiento',
                                    '<strong>Se eliminó el seguimiento con éxito!</strong>',
                                    '#btnLoadingEliminarSeguimiento',
                                    '#btnConfirmaEliminarSeguimiento'
                                );
                                $('#btnConfirmaEliminarSeguimiento').attr('disabled','disabled');
                                //se cierra ventana luego de 2 segundos
                                 setTimeout(function() {
                                     $('#btnConfirmaEliminarSeguimiento').removeAttr('disabled');
                                     $('#modalConfirmarAlertaSeguimiento').modal('hide');
                                     $('#alertaConfirmaEliminarSeguimiento').hide();
                                 }, 2000);
                                 detalleRegistros.ajax.reload();
                                document.getElementById('txtIdSeguimientoEliminar').value = null; 
                            }
                            else
                            {
                                configuraMensajeIngresoFallido('#alertaConfirmaEliminarSeguimiento',
                                                               '<strong>'+response+'</strong>',
                                                               '#btnLoadingEliminarSeguimiento',
                                                               '#btnConfirmaEliminarSeguimiento');
                                
                            }

                    },
                    failure: function(response){
                        configuraMensajeIngresoFallido('#alertaConfirmaEliminarSeguimiento',
                                                        response,
                                                        '#btnLoadingEliminarSeguimiento',
                                                        '#btnConfirmaEliminarSeguimiento');
                        
                    }
            });
        }
    }
    
    agregarDefinicionColumnaUsr();

    cuadroAsignacionesUsr =   $('#asignacionesUsr').DataTable( {
            
            "language": {
                "lengthMenu": "Muestra _MENU_ filas por página",
                "zeroRecords": "Cargando datos...",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "No hay información disponible",
                "infoFiltered": "(filtrado de _MAX_ total filas)",
                "search": "Buscar:",
                "loadingRecords": "Cargando datos..."
            },
            ajax : ($('#vistaSoporte').text().trim() === 'Vista Agente')?url_asignaciones_usuario:"",
            "columns": columnasUsr,
            "columnDefs":
            [
                {"className": "dt-center", "targets": "_all"}
            ],
            "scrollY"       : false,
            "scrollX"       : true,
            "scrollCollapse": true,
            "paging"        : false,
            "ordering"      : false,
            "searching"     : false,
            "info"          : false
        } )
    .on('preXhr.dt', function ( e, settings, data ) {
           $("#divLoaderCuadroAsignacionesUsr").show();
           $("#divLegendaCuadroAsignacionesUsr").hide();
        } )
    .on('xhr.dt', function ( e, settings, json, xhr ) {
        
                var totalTareas       = 0;
                var totalCasos        = 0;
                var totalPendientes   = 0;
                var totalSeguimientos = 0;
                if (json!=null)
                {
                    if (json.data.length > 0)
                    {    
                        //Recorremos el arreglo que contiene las filas para cuadro de asignaciones por usuario
                        for ( var i=1, ien=5; i<ien ; i++ ) {
                            var arrSeguimientos = json.data[i];
                            for (var iseg=0, ienseg=arrSeguimientos.length; iseg<ienseg; iseg++)
                            {
                                if(arrSeguimientos[iseg].tipoAtencion !== "")
                                {
                                    if (i === 1){
                                        totalTareas = parseInt(totalTareas) + 1;
                                    }
                                    else if (i === 2){
                                        totalCasos = parseInt(totalCasos) + 1;
                                    }
                                    else if (i === 3){
                                        totalPendientes = parseInt(totalPendientes) + 1;
                                    }
                                    else if (i === 4){
                                        totalSeguimientos = parseInt(totalSeguimientos) + 1;
                                    }
                                }
                            }
                        }
                    }
                }
                $("#totalTareasUsr").html("<b>"+totalTareas+"</b>");
                $("#totalCasosUsr").html("<b>"+totalCasos+"</b>");
                $("#totalPendientesUsr").html("<b>"+totalPendientes+"</b>");
                $("#totalSeguimientosUsr").html("<b>"+totalSeguimientos+"</b>");
                $("#divLoaderCuadroAsignacionesUsr").hide();
                $("#divLegendaCuadroAsignacionesUsr").show();
    } )
    .on( 'init.dt', function () {

    } )
    ;

    /**
     * Define las columnas con numeración
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     */
    function agregarDefinicionColumnaUsr()
    {
        for(var i=0; i<25; i++)
        {
            titulo = i;
                columnasUsr.push({ "data": i,
                                "title": parseInt(i)+1,
                                "render": function (data, type, full, meta)
                                          {
                                              return validacionesRenderCuadros(data);
                                          } 
                              });
        }
    }

    /**
     *
     * Actualización: Se agrega nuevas imagenes para el cuadro de asignaciones: asignación caso inicial, reasignación caso inicial,
     *     asignación tarea inicial, reasignación tarea inicial
     *     Tambien se lee el dato pin para verificar si asignación inicial o reasignación inicial  
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 26-09-2018
     *
     * Define las columnas con numeración
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @param data => Objeto con la información para validar en el render para el cuadro de asignaciones
    */
    function validacionesRenderCuadros(data)
    {
        var strDatoRetorna = "";
        if (data != null){
            var strTipo              = data.tipo;
            var strTipoAtencion      = data.tipoAtencion;
            var strEstado            = data.estado;
            var strEstadoTarea       = data.estadoTarea;
            var strEstadoCaso        = data.estadoCaso;
            var strReferenciaCliente = data.referenciaCliente;
            var strTitulo            = data.titulo;
            var strNumero            = data.numero;
            var strFeCreacion        = data.feCreacion;
            var strPin               = data.pin;
            var strLoginAfectado     = data.loginAfectado;

            var strInfoToolTipPend   = 'Fecha:'+strFeCreacion+'  Tipo atención:'+strTipoAtencion+
                                       '    Cliente:'+strReferenciaCliente+'   Estado:'+strEstado;
            var strInfoToolTipCaso   = 'Fecha:'+strFeCreacion+' Número:'+strNumero+' Cliente:'+strLoginAfectado+'   Estado:'+strEstadoCaso;
            var strInfoToolTipTarea  = 'Fecha:'+strFeCreacion+' Número:'+strNumero+' Cliente:'+strLoginAfectado+'   Estado:'+strEstadoTarea;
            var strInfoToolTipCasoN  = 'Fecha:'+strFeCreacion+' Número:'+strNumero+' Tipo atención:'+strTipoAtencion+' Cliente:'+strLoginAfectado;
            var strInfoToolTipTareaN = 'Fecha:'+strFeCreacion+' Número:'+strNumero+' Cliente:'+strLoginAfectado;
            
            var strImagePendiente                    = '<img src="/public/images/asignacionPendiente.png" />';
            var strImageCasoFinalizado               = '<img src="/public/images/asignacionCasoFinalizado.png" />';
            var strImageCasoFinalizadoTurno          = '<img src="/public/images/asignacionCasoFinalizadoTurno.png" />';
            var strImageCasoFinalizadoInicial        = '<img src="/public/images/asignacionCasoInicial.png" />';
            var strImageCasoFinalizadoReasigInicial  = '<img src="/public/images/asignacionCasoReasigInicial.png" />';
            var strImageTareaFinalizada              = '<img src="/public/images/asignacionTareaFinalizada.png" />';
            var strImageTareaFinalizadaTurno         = '<img src="/public/images/asignacionTareaFinalizadaTurno.png" />';
            var strImageTareaFinalizadaInicial       = '<img src="/public/images/asignacionTareaInicial.png" />';
            var strImageTareaFinalizadaReasigInicial = '<img src="/public/images/asignacionTareaReasigInicial.png" />';
            var strImageCasoPendiente                = '<img src="/public/images/asignacionCaso.png" />';
            var strImageCasoPendienteTurno           = '<img src="/public/images/asignacionCasoTurno.png" />';
            var strImageCasoPendienteInicial         = '<img src="/public/images/asignacionCasoInicial.png" />';
            var strImageCasoPendienteReasigInicial   = '<img src="/public/images/asignacionCasoReasigInicial.png" />';
            var strImageTareaPendiente               = '<img src="/public/images/asignacionTarea.png" />';
            var strImageTareaPendienteTurno          = '<img src="/public/images/asignacionTareaTurno.png" />';
            var strImageTareaPendienteInicial        = '<img src="/public/images/asignacionTareaInicial.png" />';
            var strImageTareaPendienteReasigInicial  = '<img src="/public/images/asignacionTareaReasigInicial.png" />';
            var strImageAsignacionDefault            = '<img src="/public/images/asignacionDefault.png" />';

            if(strTitulo != null && strTitulo != "")
            {
                return strTitulo;
            }
            if(strEstado === 'Pendiente')
            {
                var strDatoRetorna = '<span class="hint--bottom-right hint--info hint--medium'+
                                     ' hint--rounded" aria-label="'+strInfoToolTipPend+'">'+strImagePendiente+
                                      '</span>'; 
                return strDatoRetorna; 
            }
            else if (strEstado === 'EnGestion' || strEstado === 'Cerrado')
            {
                var strDatoRetorna = '<span class="hint--bottom-right hint--info hint--medium'+
                                     ' hint--rounded" aria-label="'+strInfoToolTipPend+'">'+strImageAsignacionDefault+
                                      '</span>'; 
                var strToolTip = "";
                var strImage   = "";
                if((strTipoAtencion === 'CASO') && (strEstadoCaso === 'Cerrado')) 
                {
                    strToolTip = strInfoToolTipCaso;
                    strImage = ( strTipo === 'REASIGNACION' ) ?
                               (( strPin === 'PIN2' ) ? strImageCasoFinalizadoReasigInicial : strImageCasoFinalizadoTurno) :
                               (( strPin === 'PIN1' ) ? strImageCasoFinalizadoInicial : strImageCasoFinalizado) ;
                }
                else if((strTipoAtencion === 'CASO') && (strEstadoCaso !== 'Cerrado'))
                {
                    strToolTip = strInfoToolTipCaso;
                    strImage   = strImageCasoPendiente;
                    strImage = ( strTipo === 'REASIGNACION' ) ?
                               (( strPin === 'PIN2' ) ? strImageCasoPendienteReasigInicial : strImageCasoPendienteTurno) :
                               (( strPin === 'PIN1' ) ? strImageCasoPendienteInicial : strImageCasoPendiente) ;
                }
                else if((strTipoAtencion === 'TAREA')&&(strEstadoTarea === 'Finalizada'))
                {
                    strToolTip = strInfoToolTipTarea;
                    strImage = ( strTipo === 'REASIGNACION' ) ?
                               (( strPin === 'PIN2' ) ? strImageTareaFinalizadaReasigInicial : strImageTareaFinalizadaTurno) :
                               (( strPin === 'PIN1' ) ? strImageTareaFinalizadaInicial : strImageTareaFinalizada) ;

                }
                else if((strTipoAtencion === 'TAREA')&&(strEstadoTarea !== 'Finalizada'))
                {
                    strToolTip = strInfoToolTipTarea;
                    strImage   = strImageTareaPendiente;
                    strImage = ( strTipo === 'REASIGNACION' ) ?
                               (( strPin === 'PIN2' ) ? strImageTareaPendienteReasigInicial : strImageTareaPendienteTurno) :
                               (( strPin === 'PIN1' ) ? strImageTareaPendienteInicial : strImageTareaPendiente) ;
                }
                strDatoRetorna = '<span class="hint--bottom-right hint--info hint--medium'+
                                 ' hint--rounded" aria-label="'+strToolTip+'">'+strImage+
                                  '</span>';
                return strDatoRetorna;                                            
            }
            else
            {
                var strDatoRetorna = '<p></p>';
                if(strTipoAtencion == 'CASO')
                {
                    strDatoRetorna = '<span class="hint--bottom-right hint--info hint--medium'+
                                     ' hint--rounded" aria-label="'+strInfoToolTipCasoN+'">'+
                                     '<img src="/public/images/asignacionCaso.png" />'+
                                      '</span>';
                }
                else if(strTipoAtencion == 'TAREA')
                {
                    strDatoRetorna = '<span class="hint--bottom-right hint--info hint--medium'+
                                     ' hint--rounded" aria-label="'+strInfoToolTipTareaN+'">'+
                                     '<img src="/public/images/asignacionTarea.png" />'+
                                      '</span>';
                }
                return strDatoRetorna;
            }
        }
        else
        {
          return '<p></p>';
        }
    }

    

    
    /**
     * 
     * Actualización: Se realizan los siguientes cambios en el cuadro de asignaciones:
     *  - Se muestra en la parte inferior del login del usuario la fecha de estado de conexión. 
     *  - Se muestra en la parte superior del login del usuario la cantidad de asignaciones del agente.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 22-01-2019
     * 
     * Actualización: Se valida el color del nombre del usuario según el estado de conexión
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 24-10-2018
     *
     * Define las columnas con los logins de los empleados que se presentaran en el cuadro de asignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @param usrAsignado                   => Login del empleado para agregarlo al cuadro de asignaciones
     * @param indice                        => Número secuencial del agente
     * @param estadoConexion                => Estado de conexión actual del agente
     * @param strFeEstadoConexion           => Fecha del último cambio de estado de conexión del agente
     * @param strCantidadAsignacionesAgente => cantidad de asignaciones por agente
     */
    function agregarDefinicionColumna(usrAsignado, indice, estadoConexion, strFeEstadoConexion,strCantidadAsignacionesAgente)
    {
        var arrayFeEstadoConexion = strFeEstadoConexion.split(" ");
        var colorEstadoConexion = 'btn-default';
        if (estadoConexion === 'Ocupado')
        {
            colorEstadoConexion = 'btn-warning';
        }
        else if (estadoConexion === 'Disponible')
        {
            colorEstadoConexion = 'btn-success';
        }
        else if (estadoConexion === 'Almuerzo')
        {
            colorEstadoConexion = 'btn-primary';
        }
        else if (estadoConexion === 'Ausente')
        {
            colorEstadoConexion = 'btn-danger';
        }
        var strEstadoConexionInfo = ( estadoConexion !== null ) ? estadoConexion : ''; 
        var strFechaConexion = '<span class="hint--top-right hint--default hint--medium'+
                             ' hint--rounded fechaConexionCuadroGeneral" aria-label="'+
                             arrayFeEstadoConexion[0]+' '+arrayFeEstadoConexion[1]+' '+strEstadoConexionInfo+'">'+arrayFeEstadoConexion[1]+
                              '</span>';
        columnas.push({ "data": usrAsignado,
                        "width": "3%",
                        "title": "<div class='cantidadAsignacionesAgente'>"+strCantidadAsignacionesAgente+"</div>"+
                                 "<p class='verticaltext'><button type='button' class='btn btn btn-xs "+colorEstadoConexion+"' "+
                                 "onClick=\"javascript:mostrarNuevaAsignacionUsr('"+usrAsignado+"')\">"+indice+"."+usrAsignado+"</button></p>"+
                                 strFechaConexion+
                                 "",

                        "render": function (data, type, full, meta)
                                  {
                                      return validacionesRenderCuadros(data);
                                  }
                      });
    }



    /**
     * Define el cuadro de asignaciones en Datatable
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @param theads => codigo en html que se agrega a el tag <thead> con id "columnasCabecera" de la tabla con id "asignaciones" 
     */
    function definirCuadroDeAsignaciones(theads)
    {
        if ( $.fn.dataTable.isDataTable( '#asignaciones' ) ) 
        {
            cuadroAsignaciones.destroy();
            $('#asignaciones').empty();
            $('#asignaciones').html("<table id=\"asignaciones\" class=\"table table-striped table-bordered compact\""+
                                      //" style=\"width:850px;height:530px\">"+
                                        " style=\"width:100%;\">"+
                                        "<thead id=\"columnasCabecera\">"+
                                        "</thead>"+
                                    "</table>");
            $("#columnasCabecera").html(theads);
            
            cuadroAsignaciones =   $('#asignaciones').DataTable( {
                    "language": {
                        "lengthMenu": "Muestra _MENU_ filas por página",
                        "zeroRecords": "Cargando datos...",
                        "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros   (Información de hoy)",
                        "infoEmpty": "No hay información disponible",
                        "infoFiltered": "(filtrado de _MAX_ total filas)",
                        "search": "Buscar:",
                        "loadingRecords": "Cargando datos..."
                    },
                    "retrieve": true,
                    "ajax": ($('#vistaSoporte').text().trim() === 'Vista Administrador') ? url_asignaciones : "",
                    "columns": columnas,
                    "columnDefs":
                    [
                        {"className": "dt-center", "targets": "_all"}
                    ],
                    "paging":false,
                    "searching":false,
                    "scrollX": true,
                    "scrollY": true,
                    "ordering":false
                } );
        }
        else
        {
            cuadroAsignaciones =   $('#asignaciones').DataTable( {
                    "language": {
                        "lengthMenu": "Muestra _MENU_ filas por página",
                        "zeroRecords": "Cargando datos...",
                        "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros   (Información de hoy)",
                        "infoEmpty": "No hay información disponible",
                        "infoFiltered": "(filtrado de _MAX_ total filas)",
                        "search": "Buscar:",
                        "loadingRecords": "Cargando datos..."
                    },
                    "retrieve": true,
                    "ajax": ($('#vistaSoporte').text().trim() === 'Vista Administrador') ? url_asignaciones : "",
                    "columns": columnas,
                    "columnDefs":
                    [
                        {"className": "dt-center", "targets": "_all"}
                    ],
                    "paging":false,
                    "searching":false,
                    "scrollX": true,
                    "scrollY": true,
                    "ordering":false
                } );                
        }
    }

    
    //Define los detalles del cuadro de asignaciones
    detalleAsignaciones =   $('#detalleAsignaciones').DataTable( {
            "createdRow": function( row, data, dataIndex){
                if( data.colorRegistro !== null && data.colorRegistro !== ""){
                    $(row).addClass(data.colorRegistro);
                }
            },
            dom:"ipftl",
            "language": {
                "lengthMenu": "Muestra _MENU_ filas por página",
                "zeroRecords": "Cargando datos...",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "No hay información disponible",
                "infoFiltered": "(filtrado de _MAX_ total filas)",
                "search": "Buscar:",
                "loadingRecords": "Cargando datos...",
            },
            "columns": [
                {
                    "className"     : 'details-control',
                    "orderable"     : false,
                    "data"          : null,
                    "defaultContent": '',
                    "render"        : function (data, type, full, meta)
                                      {
                                          var strDatoRetorna = '<span class="hint--bottom-right hint--default hint--small'+
                                                               ' hint--rounded" aria-label="Ver más">';
                                          strDatoRetorna    += '<button type="button" class="btn btn-success btn btn-xs xss circular" '+'>'+
                                                               '    <span class="glyphicon glyphicon-plus"></span>'+
                                                               '</button>';
                                          strDatoRetorna    += '</span>';
                                          return strDatoRetorna;
                                      }
                },
                {
                    "orderable" : false,
                    "data"      : "estado",
                    "title"     : "Estado asignaci&oacute;n",
                    "render"    : function (data, type, full, meta)
                                  {
                                      var strDatoRetorna = '';
                                      if (full.estado == 'EnGestion' || full.estado == 'Pendiente')
                                      {
                                          strDatoRetorna = '<span class="hint--bottom-right hint--default hint--medium'+
                                                           ' hint--rounded" aria-label="Asignación Abierta">'+
                                                           '<img src=\"/public/images/images_crud/lock_open.png\" width=\"24\" height=\"24\" />'+
                                                           '</span>';
                                      }
                                      else if (full.estado == 'Cerrado')
                                      {
                                          strDatoRetorna = '<span class="hint--bottom-right hint--default hint--medium'+
                                                           ' hint--rounded" aria-label="Asignación Cerrada">'+
                                                           '<img src=\"/public/images/images_crud/lock_close.png\" width=\"24\" height=\"24\" />'+
                                                           '</span>';
                                      }
                                      else if (full.estado == 'Standby')
                                      {
                                          strDatoRetorna = '<span class="hint--bottom-right hint--default hint--medium'+
                                                           ' hint--rounded" aria-label="Asignación en Standby">'+
                                                           '<img src=\"/public/images/images_crud/time_red.png" width=\"24\" height=\"24\" />'+
                                                           '</span>';
                                      }
                                      else
                                      {
                                          strDatoRetorna = '';
                                      }
                                      return strDatoRetorna;
                                  }
                },
                {"data": "numero",
                "title": "#",
                "orderable": false
                },    
                {"data": "casoTarea",
                 "title":"N&uacute;mero tarea/caso",
                 "render":function(data, type, full, meta)
                          {
                              var strDatoRetorna = full.casoTarea;
                              if (full.urlVerCasoTarea!=="" && full.urlVerCasoTarea!=null)
                              {
                                  strDatoRetorna="<a target='_blank' href='"+full.urlVerCasoTarea+"'>"+full.casoTarea+"</a>";
                              }
                              return strDatoRetorna;
                          }
                },    
                {"data": "feCreacion",
                 "title":"Fecha asignaci&oacute;n",
                 "render":function(data, type, full, meta)
                          {
                              var colorCiudad = "#f9fbe7;";

                              if(full.ciudad == 'GUAYAQUIL')
                              {
                                  colorCiudad = "#efebe9;";
                              }
                              else if(full.ciudad == 'QUITO')
                              {
                                colorCiudad = "#c8e6c9;";
                              }
                              var strDatoRetorna="<div>"+full.feCreacion+"</div>"+
                                                 "<div style=\"background-color:"+colorCiudad+"\">"+full.ciudad+"</div>";
                              return strDatoRetorna;
                          }
                },
                {"data": "referenciaCliente",
                 "title": "Cliente",
                 "render":function(data, type, full, meta)
                          {
                              var strDatoRetorna = full.referenciaCliente;
                              if (full.referenciaCliente!=="" && full.referenciaCliente!==null)
                              {
                                  strDatoRetorna = "<a href='javascript:mostrarBusquedaAvanzada(\""+full.referenciaCliente+"\");'>"+
                                                   full.referenciaCliente+"</a>";
                              }
                              return strDatoRetorna;
                          }
                },
                {"data": "loginAfectado",
                 "title": "Login",
                 "render":function(data, type, full, meta)
                          {
                              var strDatoRetorna = full.referenciaCliente;
                              strDatoRetorna     = "<a href='"+full.urlLoginAfectado+"' target='_blank'>"+full.loginAfectado+"</a>";
                              return strDatoRetorna;
                          }
                },
                {"data": "tipoAtencion",
                 "title":"Tipo atención",
                 "render": function (data, type, full, meta)
                           {
                               var tipoAtencion      = full.tipoAtencion;
                               var colorTipoAtencion = "default";
                               if (tipoAtencion === 'TAREA')
                               {
                                   colorTipoAtencion = "success";
                               }
                               else if (tipoAtencion === 'CASO')
                               {
                                   colorTipoAtencion = "warning";
                               }
                               else if (tipoAtencion === null)
                               {
                                   return '';
                               }
                               var strDatoRetorna = '<h5><span class="label label-'+colorTipoAtencion+'" style="padding:4px;font-size:12px;">'+
                                                    tipoAtencion+'</span></h5>';
                               return strDatoRetorna;
                           }
                },
                {"data": "origen",
                 "title":"Origen"
                },
                {"data": "tipoProblema",
                 "title":"Tipo problema"
                },
                {"data": "criticidad",
                 "title":"Criticidad",
                 "render": function (data, type, full, meta)
                           {
                               var criticidad      = full.criticidad;
                               var colorCriticidad = "default";
                               if (criticidad === 'ALTA')
                               {
                                   colorCriticidad = "danger";
                               }
                               else if (criticidad === 'MEDIA')
                               {
                                   colorCriticidad = "warning";
                               }
                               else if (criticidad === 'BAJA')
                               {
                                   colorCriticidad = "default";
                               }
                               else if (criticidad === null)
                               {
                                   return '';
                               }
                               var strDatoRetorna = '<span class="label label-'+colorCriticidad+'" style="padding:4px;font-size:12px;">'+
                                                    criticidad+'</span>';
                               return strDatoRetorna;
                           }
                },
                {"data": "usrAsignado",
                 "title": "Asignado"
                },
                {"data": "asignado",
                 "title": "Dep. Asignado"
                },
                {"data": "estadoCaso",
                 "title":"Estado caso",
                 "render": function (data, type, full, meta)
                           {
                               var estadoCaso      = full.estadoCaso;
                               var colorEstadoCaso = "default";
                               if (estadoCaso === 'Cerrado')
                               {
                                    colorEstadoCaso = "success";
                               }
                               else if (estadoCaso === 'Asignado')
                               {
                                    colorEstadoCaso = "warning";
                               }
                               else if (estadoCaso === 'Abierto')
                               {
                                    colorEstadoCaso = "primary";
                               }
                               else if (estadoCaso === null)
                               {
                                    return '';
                               }
                               var strDatoRetorna = '<span class="label label-'+colorEstadoCaso+'" style="padding:4px;font-size:12px;">'+
                                                    estadoCaso+'</span>';
                               return strDatoRetorna;
                           }
                },
                {"data": "infoTareas",
                 "title":"Estado tarea",
                 "render": function (data, type, full, meta)
                           {
                               var strDatoRetorna   = "";

                               if (full.tipoAtencion === "CASO")
                               {
                                   strDatoRetorna = decodeTareas(full.infoTareas,full.casoTarea);
                               }
                               else if(full.tipoAtencion === "TAREA")
                               {
                                    var estadoTarea      = full.estadoTarea;
                                    var colorEstadoTarea = "default";
                                    if (estadoTarea == 'Finalizada')
                                    {
                                        colorEstadoTarea = "success";
                                    }
                                    else if (estadoTarea == 'Asignada')
                                    {
                                        colorEstadoTarea = "danger";
                                    }
                                    else if (estadoTarea == 'Pausada')
                                    {
                                        colorEstadoTarea = "primary";
                                    }
                                    else if (estadoTarea == null)
                                    {
                                         return '';
                                    }
                                    strDatoRetorna = '<span class="label label-'+colorEstadoTarea+'" style="padding:4px;font-size:12px;">'
                                                     +estadoTarea+'</span>';
                               }
                               return strDatoRetorna;
                           }
                },
                {"data": "cambioTurno",
                 "title": "Turno",
                 "orderable": false,
                 "render": function (data, type, full, meta)
                           {
                               var strCambioTurno = "N";
                               var strDisabled    = "disabled";
                               var strColorBoton  = "default";
                               if(full.estado !== 'Pendiente' && full.estado!=='Cerrado' && full.estado!=='Standby' &&
                                  ((full.tipoAtencion === 'CASO' && full.estadoCaso !== 'Cerrado') ||
                                  (full.tipoAtencion === 'TAREA' && full.estadoTarea !== 'Finalizada'))
                                 )
                               {
                                    strDisabled    = "";
                                    if (full.cambioTurno === 'N')
                                    {
                                        strColorBoton  = 'default';
                                        strCambioTurno = 'S';
                                    }
                                    else
                                    {
                                        strColorBoton  = 'success';
                                        strCambioTurno = 'N';
                                    }
                               }
                               var strDatoRetorna = '<span class="hint--bottom-right hint--default hint--medium'+
                                                    ' hint--rounded" aria-label="Marcar Cambio de Turno">'+
                                                    '<button type="button" class="btn btn-'+strColorBoton+' btn btn-xs" '+
                                                    '  onClick="javascript:mostrarNuevoSeguimientoCambioTurno(\''+strCambioTurno+'\','+full.id+');" '+
                                                       strDisabled+'>'+
                                                    '  <span class="glyphicon glyphicon-pushpin"></span>'+
                                                    '</button>'+
                                                    '<span>';

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
                                                         ' hint--rounded" aria-label="Ver información de asignación">'+
                                                        '<button type="button" class="btn btn-default btn btn-xs" '+
                                                        '        onClick="javascript:obtenerInformacionAsignacion(\''+full.id+'\');">'+
                                                        '    <span class="glyphicon glyphicon-search"></span>'+
                                                        '</button>'+
                                                        '</span>';
                     
                               if((full.referenciaId === null || full.referenciaId === '') && (full.estado!=='Cerrado') && (full.estado!=='Standby') && 
                                  (
                                       $('#vistaSoporte').text().trim() === 'Vista Agente' ||
                                       $('#vistaSoporte').text().trim() === 'Vista Administrador' ||
                                       $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
                                       $('#vistaSoporte').text().trim() === 'Vista Jefe'
                                  )
                                 )
                               {
                               strDatoRetorna    +=     '<span class="hint--bottom-right hint--default hint--medium'+
                                                         ' hint--rounded" aria-label="Agregar número tarea">'+
                                                            '<button type="button" class="btn btn-default btn-xs" '+
                                                            '     onClick="javascript:mostrarAgregarNumeroTarea(\''+full.id+'\',\''+
                                                                                                                    full.tipoProblema+'\',\''+
                                                                                                                    full.tipoAtencion+'\');">'+
                                                            '    <span class="glyphicon glyphicon-edit"></span>'+
                                                            '</button>'+
                                                        '</span>';
                               }
                               if((full.estado!=='Pendiente') && (full.estado!=='Cerrado') && (full.estado!=='Standby')  &&
                                  ($('#vistaSoporte').text().trim() === 'Vista Administrador' ||
                                       $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
                                       $('#vistaSoporte').text().trim() === 'Vista Jefe'
                                  )
                                 )
                               {
                                   var tareasCmb   = "";
                                   if (full.infoTareas !== null)
                                   {
                                       var arrayTareas = JSON.parse(full.infoTareas);
                                       for(var i=0;i<arrayTareas.length;i++)
                                       {
                                           tareasCmb += '|'+ arrayTareas[i].NUM;
                                       }
                                   }
                                   strDatoRetorna += '<span class="hint--bottom-right hint--default hint--medium'+
                                                         ' hint--rounded" aria-label="Asignar seguimiento">'+
                                                     '<button type="button" class="btn btn-default btn-xs" '+
                                                     '     onClick="javascript:mostrarAsignarSeguimiento(\''+full.id+'\',\''+
                                                                                                             tareasCmb+'\',\''+
                                                                                                             full.tipoAtencion+'\');">'+
                                                     '    <span class="glyphicon glyphicon-user"></span>'+
                                                     '</button>'+
                                                     '</span>';
                                    strDatoRetorna += '<span class="hint--bottom-right hint--default hint--medium'+
                                                     ' hint--rounded" aria-label="Enviar a Standby">'+
                                                     '<button type="button" class="btn btn-default btn-xs" '+
                                                     '     onClick="javascript:mostrarPonerStandby(\''+full.id+'\');">'+
                                                     '    <span class="glyphicon glyphicon-time"></span>'+
                                                     '</button>'+
                                                     '</span>';
                               }
                               //Cerrar asignación
                               if((full.estado!=='Cerrado' && full.estado!=='Standby') && (full.casoTarea !== '' && full.casoTarea !== null) && 
                                  ( (full.asignado !== nombreDepartamento) ||
                                    (full.tipoAtencion === 'CASO' && full.estadoCaso === 'Cerrado') ||
                                    (full.tipoAtencion === 'TAREA' && full.estadoTarea === 'Finalizada')
                                  )
                                 )
                               {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                             ' hint--rounded" aria-label="Cerrar Asignación">'+
                                                             '<button type="button" class="btn btn-default btn-xs" '+
                                                             '     onClick="javascript:mostrarConfirmacionCerrarAsig(\''+full.id+'\',\''+
                                                                                                                         full.cambioTurno+'\');">'+
                                                             '    <span class="glyphicon glyphicon-lock"></span>'+
                                                             '</button>'+
                                                         '</span>';
                               }
                               if (
                                    $('#vistaSoporte').text().trim() === 'Vista Administrador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Jefe'
                                  )
                               {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                             ' hint--rounded" aria-label="Eliminar Asignación">'+
                                                             '<button type="button" class="btn btn-default btn-xs" '+
                                                             '     onClick="javascript:mostrarConfirmacionEliminaAsig(\''+full.id+'\');">'+
                                                             '    <span class="glyphicon glyphicon-remove"></span>'+
                                                             '</button>'+
                                                         '</span>';


                                    tareas             = JSON.stringify(full);
                                    tareas             = tareas.replace(/"/g, '\\"');
                                    
                                    if(permiteVerNuevosCamposTareas == 1 && full.tipoAtencion.toUpperCase() == 'TAREA')
                                    {
                                        tareasEjecucion  = JSON.stringify(full);
                                        tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                        if(full.estadoTarea !== 'Finalizada')
                                        {
                                            
                                            strDatoRetorna    +=    '<span class="hint--bottom-left hint--default hint--medium'+
                                            ' hint--rounded" aria-label="Crear nueva asignación" >'+
                                            '<button type="button" class="btn btn-default btn-xs" '+
                                            "onClick='javascript:mostrarNuevaAsignacionTarea(\""+tareasEjecucion+"\")'>"+
                                            '<span class="glyphicon glyphicon-file"></span>'+
                                            '</button>'+
                                            '</span>';
                                            
                                            if(full.EstadoHist == "Asignada" || full.EstadoHist == "Reprogramada")
                                            {
                                                full.accionTarea = "iniciar";
                                                tareasEjecucion  = JSON.stringify(full);
                                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                                strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                ' hint--rounded btnEjecucionTarea" aria-label="Ejecutar Tarea" >'+
                                                '<button  type="button" class="btn btn-default btn-xs" '+
                                                "onClick='javascript:mostrarEjecucionTarea(\""+tareasEjecucion+"\")'>"+
                                                '<span class="glyphicon glyphicon-play"></span>'+
                                                '</button>'+
                                                '</span>';
                                            }

                                            if(full.EstadoHist == "Pausada")
                                            {
                                                full.accionTarea = "reanudar";
                                                tareasEjecucion  = JSON.stringify(full);
                                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                                strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                ' hint--rounded btnEjecucionTarea" aria-label="Reanudar Tarea" >'+
                                                '<button type="button" class="btn btn-default btn-xs" '+
                                                "onClick='javascript:mostrarEjecucionTarea(\""+tareasEjecucion+"\")'>"+
                                                '<span class="glyphicon glyphicon-step-forward"></span>'+
                                                '</button>'+
                                                '</span>';
                                            }

                                            if(full.EstadoHist == "Aceptada")
                                            {
                                                full.accionTarea = "pausar";
                                                strBanderaFinalizarInformeEjecutivo = full.strBanderaFinalizarInformeEjecutivo
                                                tareasEjecucion  = JSON.stringify(full);
                                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                                strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                ' hint--rounded btnEjecucionTarea" aria-label="Pausar Tarea" >'+
                                                '<buttontype="button" class="btn btn-default btn-xs" '+
                                                "onClick='javascript:mostrarEjecucionTarea(\""+tareasEjecucion+"\")'>"+
                                                '<span class="glyphicon glyphicon-pause"></span>'+
                                                '</button>'+
                                                '</span>';

                                                if(permiteAccionesTareas == 1 && strBanderaFinalizarInformeEjecutivo == 'S'){
                                                    strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                    ' hint--rounded btnFinalizarTarea" aria-label="Finalizar Tarea" >'+
                                                    '<buttontype="button" class="btn btn-default btn-xs" '+
                                                    "onClick='javascript:finalizarTarea(\""+tareasEjecucion+"\",this)'>"+
                                                    '<span class="glyphicon glyphicon-stop"></span>'+
                                                    '</button>'+
                                                    '</span>';
                                                }
                                            }
                                            if(full.EstadoHist == "Aceptada" || full.EstadoHist == "Pausada")
                                            {
                                                full.accionTarea = "reasignar";
                                                strBanderaFinalizarInformeEjecutivo = full.strBanderaFinalizarInformeEjecutivo
                                                tareasEjecucion  = JSON.stringify(full);
                                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                                if(permiteAccionesTareas == 1){
                                                    strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                    ' hint--rounded btnReasignarTarea" aria-label="Reasignar Tarea" >'+
                                                    '<buttontype="button" class="btn btn-default btn-xs" '+
                                                    "onClick='javascript:reasignarTarea(\""+tareasEjecucion+"\",this)'>"+
                                                    '<span class="glyphicon glyphicon-dashboard"></span>'+
                                                    '</button>'+
                                                    '</span>';
                                                }
                                            }

                                            strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                ' hint--rounded btnIngresoSeguimiento" aria-label="Ingresar Seguimiento" >'+
                                                '<button  type="button" class="btn btn-default btn-xs" '+
                                                "onClick='javascript:mostrarIngresoSeguimiento(\""+tareasEjecucion+"\")'>"+
                                                '<span class="glyphicon glyphicon-list-alt"></span>'+
                                                '</button>'+
                                                '</span>';

                                        }

                                        if(full.EstadoHist != "Pausada" && full.EstadoHist != "")
                                        {
                                            strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                            ' hint--rounded btnCargarArchivo" aria-label="Cargar Archivo" >'+
                                            '<button  type="button" class="btn btn-default btn-xs" '+
                                            "onClick='javascript:subirMultipleAdjuntosTarea(\""+tareasEjecucion+"\")'>"+
                                            '<span class="glyphicon glyphicon-open-file"></span>'+
                                            '</button>'+
                                            '</span>';
                                        }
                                        if(full.idDetalle != '' && full.strTareaIncAudMant != '')
                                        {
                                            strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                            ' hint--rounded btnVerArchivo" aria-label="Ver Archivos" >'+
                                            '<button  type="button" class="btn btn-default btn-xs" '+
                                            "onClick='javascript:presentarDocumentosTareas("+full.idDetalle+
                                            ",\""+full.strTareaIncAudMant+"\",this)'>"+
                                            '<span class="glyphicon glyphicon-eye-open"></span>'+
                                            '</button>'+
                                            '</span>';
                                        }

                                        strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                        ' hint--rounded btnSeguimiento" aria-label="Mostrar Seguimientos" >'+
                                        '<button  type="button" class="btn btn-default btn-xs" '+
                                        "onClick='javascript:mostrarSeguimiento(\""+tareasEjecucion+"\")'>"+
                                        '<span class="glyphicon glyphicon-search"></span>'+
                                        '</button>'+
                                        '</span>';  

                                    }
                               }
                               
                               if((full.origen !== 'PROACTIVOS') && (full.estado!=='Cerrado') && (full.estado!=='Standby'))
                               {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                                  ' hint--rounded" aria-label="Asignaciones Proactivas">'+
                                                                  '<button type="button" class="btn btn-default btn-xs" '+
                                                                  '     onClick="javascript:asignacionHijaPadre(\''+full.id+'\');">'+
                                                                  '    <span class="glyphicon glyphicon-compressed"></span>'+
                                                                  '</button>'+
                                                              '</span>';
                               }
                               
                               if(full.padre == 'S')
                               {
                                    strDatoRetorna    += '<span class="hint--bottom-right hint--default hint--medium'+
                                                                ' hint--rounded" aria-label="Asignaciones Relacionadas">'+
                                                                '<button type="button" class="btn btn-info btn-xs" '+
                                                                '     onClick="javascript:mostrarAsignacionHija(\''+full.id+'\');">'+
                                                                '    <span class="glyphicon glyphicon-link"></span>'+
                                                                '</button>'+
                                                             '</span>';
                               }
                               
                               if(full.estado==='Standby')
                               {
                                    strDatoRetorna += '<span class="hint--bottom-right hint--default hint--medium'+
                                                     ' hint--rounded" aria-label="Finalizar Standby">'+
                                                     '<button type="button" class="btn btn-default btn-xs" '+
                                                     '     onClick="javascript:mostrarQuitarStandby(\''+full.id+'\');">'+
                                                     '    <span class="glyphicon glyphicon-ok"></span>'+
                                                     '</button>'+
                                                     '</span>';
                               }

                               if( (full.tabVisible!=='detallesIncidenciasSeg') && full.tipoProblema === 'INCIDENCIA DE SEGURIDAD'
                                   && !$('#liDetallesIseg').hasClass('active') )
                               {
                                    strDatoRetorna += '<span class="hint--bottom-right hint--default hint--medium'+
                                                     ' hint--rounded" aria-label="Mover Incidencia de Seguridad">'+
                                                     '<button type="button" class="btn btn-default btn-xs" '+
                                                     '     onClick="javascript:grabarMoverIncidenciaSeguridad(\''+full.id+'\',\''+
                                                                                                                'tabVisible'+'\',\''+
                                                                                                                'detallesIncidenciasSeg'+'\');">'+
                                                     '    <span class="glyphicon glyphicon-arrow-right"></span>'+
                                                     '</button>'+
                                                     '</span>';
                               }
                               return strDatoRetorna;
                           }
                }
            ],
            "columnDefs":
            [
                {"className": "dt-center verticalAlignCellDt", "targets": "_all"},
                { "width": "5", "targets": 0 },
                { "width": "5", "targets": 1 },
                { "width": "5", "targets": 2 },
                { "width": "25", "targets": 3 },
                { "width": "60", "targets": 4 },
                { "width": "60", "targets": 5 },
                { "width": "40", "targets": 6 },
                { "width": "40", "targets": 7 },
                { "width": "40", "targets": 8 },
                { "width": "40", "targets": 9 },
                { "width": "50", "targets": 10 },
                { "width": "45", "targets": 11 },
                { "width": "45", "targets": 12 },
                { "width": "25", "targets": 13 },
                { "width": "20", "targets": 14 },
                { "width": "130", "targets": 15 }
            ],
            "paging":false
        } )
        .on('preXhr.dt', function ( e, settings, data ) {
                   $("#divLoaderDetalleAsignaciones").show();
            } )
        .on('xhr.dt', function ( e, settings, json, xhr ) {
           $("#divLoaderDetalleAsignaciones").hide();
        } )
    ;



    //Define los detalles del cuadro de asignaciones Proactivas 
    detalleAsignacionesProactivas =  $('#detalleAsignacionesProactivas').DataTable( {
            "language": {
                "lengthMenu": "Muestra _MENU_ filas por página",
                "zeroRecords": "Cargando datos...",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "No hay información disponible",
                "infoFiltered": "(filtrado de _MAX_ total filas)",
                "search": "Buscar:",
                "loadingRecords": "Cargando datos...",
            },
            "columns": [  
                {"data": "numero",
                "title": "#",
                "orderable": false
                },
                {"data": "casoTarea",
                 "title":"N&uacute;mero tarea/caso",
                 "render":function(data, type, full, meta)
                          {
                              var strDatoRetorna = full.casoTarea;
                              if (full.urlVerCasoTarea!=="" && full.urlVerCasoTarea!=null)
                              {
                                  strDatoRetorna="<a target='_blank' href='"+full.urlVerCasoTarea+"'>"+full.casoTarea+"</a>";
                              }
                              return strDatoRetorna;
                          }
                },    
                {"data": "feCreacion",
                 "title":"Fecha asignaci&oacute;n"
                },
                {"data": "loginAfectado",
                 "title": "Login",
                 "render":function(data, type, full, meta)
                          {
                              var strDatoRetorna = full.referenciaCliente;
                              strDatoRetorna = "<a href='javascript:mostrarBusquedaAvanzada(\""+full.loginAfectado+"\");'>"+
                                      full.loginAfectado+"</a>";
                              return strDatoRetorna;
                          }
                },
                {"data": "infoTareas",
                 "title":"Estado tarea",
                 "render": function (data, type, full, meta)
                           {
                               var strDatoRetorna   = "";

                               if (full.tipoAtencion === "CASO")
                               {
                                   strDatoRetorna = decodeTareas(full.infoTareas,full.casoTarea);
                               }
                               else if(full.tipoAtencion === "TAREA")
                               {
                                    var estadoTarea      = full.estadoTarea;
                                    var colorEstadoTarea = "default";
                                    if (estadoTarea == 'Finalizada')
                                    {
                                        colorEstadoTarea = "success";
                                    }
                                    else if (estadoTarea == 'Asignada')
                                    {
                                        colorEstadoTarea = "danger";
                                    }
                                    else if (estadoTarea == 'Pausada')
                                    {
                                        colorEstadoTarea = "primary";
                                    }
                                    else if (estadoTarea == null)
                                    {
                                         return '';
                                    }
                                    strDatoRetorna = '<span class="label label-'+colorEstadoTarea+'" style="padding:4px;font-size:12px;">'
                                                     +estadoTarea+'</span>';
                               }
                               return strDatoRetorna;
                           }
                },
                {"data": "acciones",
                 "title": "Selección Proactivas",
                  "render": function (data, type, full, meta)
                           {
                                var strDatoRetorna = '';
                                   strDatoRetorna    +=   '<label><input type="checkbox" class="checkbox" id="'+full.id+
                                                          '" value='+full.id+' change="checked" ';
                                                  
                                var encontrado = (arrayAsignacionProactiva.find(asignacion => asignacion.id == full.id) != null) ? true : false;
                                   if(arrayAsignacionProactiva.length > 0 && encontrado)
                                    {
                                        strDatoRetorna    +='checked="True" ';
                                    }  
                                    
                                   strDatoRetorna    += '></label>';           
                            return strDatoRetorna;
                           }
                }
            ],
            "columnDefs":
            [
                {"className": "dt-center verticalAlignCellDt", "targets": "_all"},
                { "width": "5", "targets": 0 },
                { "width": "5", "targets": 1 },
                { "width": "5", "targets": 2 },
                { "width": "25", "targets": 3 },
                { "width": "60", "targets": 4 },
                { "width": "60", "targets": 5 }
            ],
            "paging":false
        } );

    detalleAsignacionesHijas =  $('#detalleAsignacionesHijas').DataTable( {
            "language": {
                "lengthMenu": "Muestra _MENU_ filas por página",
                "zeroRecords": "Cargando datos...",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "No hay información disponible",
                "infoFiltered": "(filtrado de _MAX_ total filas)",
                "search": "Buscar:",
                "loadingRecords": "Cargando datos...",
            },
            "columns": [  
                {
                    "orderable" : false,
                    "data"      : "estado",
                    "title"     : "Estado",
                    "render"    : function (data, type, full, meta)
                                  {
                                      var strDatoRetorna = '';
                                      if (full.estado == 'EnGestion' || full.estado == 'Pendiente')
                                      {
                                          strDatoRetorna = '<span class="hint--bottom-right hint--default hint--medium'+
                                                           ' hint--rounded" aria-label="Asignación Abierta">'+
                                                           '<img src=\"/public/images/images_crud/lock_open.png\" width=\"24\" height=\"24\" />'+
                                                           '</span>';
                                      }
                                      else if (full.estado == 'Cerrado')
                                      {
                                          strDatoRetorna = '<span class="hint--bottom-right hint--default hint--medium'+
                                                           ' hint--rounded" aria-label="Asignación Cerrada">'+
                                                           '<img src=\"/public/images/images_crud/lock_close.png\" width=\"24\" height=\"24\" />'+
                                                           '</span>';
                                      }
                                      else
                                      {
                                          strDatoRetorna = '';
                                      }
                                      return strDatoRetorna;
                                  }
                },
                {"data": "casoTarea",
                 "title":"N&uacute;mero tarea/caso",
                 "render":function(data, type, full, meta)
                          {
                              var strDatoRetorna = full.casoTarea;
                              if (full.urlVerCasoTarea!=="" && full.urlVerCasoTarea!=null)
                              {
                                  strDatoRetorna="<a target='_blank' href='"+full.urlVerCasoTarea+"'>"+full.casoTarea+"</a>";
                              }
                              return strDatoRetorna;
                          }
                },    
                {"data": "feCreacion",
                 "title":"Fecha asignaci&oacute;n"
                },
                {"data": "loginAfectado",
                 "title": "Login",
                 "render":function(data, type, full, meta)
                          {
                              var strDatoRetorna = full.referenciaCliente;
                              strDatoRetorna = "<a href='javascript:mostrarBusquedaAvanzada(\""+full.loginAfectado+"\");'>"+
                                      full.loginAfectado+"</a>";
                              return strDatoRetorna;
                          }
                },
                {"data": "infoTareas",
                 "title":"Estado tarea",
                 "render": function (data, type, full, meta)
                           {
                               var strDatoRetorna   = "";

                               if (full.tipoAtencion === "CASO")
                               {
                                   strDatoRetorna = decodeTareas(full.infoTareas,full.casoTarea);
                               }
                               else if(full.tipoAtencion === "TAREA")
                               {
                                    var estadoTarea      = full.estadoTarea;
                                    var colorEstadoTarea = "default";
                                    if (estadoTarea == 'Finalizada')
                                    {
                                        colorEstadoTarea = "success";
                                    }
                                    else if (estadoTarea == 'Asignada')
                                    {
                                        colorEstadoTarea = "danger";
                                    }
                                    else if (estadoTarea == 'Pausada')
                                    {
                                        colorEstadoTarea = "primary";
                                    }
                                    else if (estadoTarea == null)
                                    {
                                         return '';
                                    }
                                    strDatoRetorna = '<span class="label label-'+colorEstadoTarea+'" style="padding:4px;font-size:12px;">'
                                                     +estadoTarea+'</span>';
                               }
                               return strDatoRetorna;
                           }
                }
            ],
            "columnDefs":
            [
                {"className": "dt-center verticalAlignCellDt", "targets": "_all"},
                { "width": "5", "targets": 0 },
                { "width": "5", "targets": 1 },
                { "width": "25", "targets": 2 },
                { "width": "60", "targets": 3 },
                { "width": "60", "targets": 4 }
            ],
            "paging":false
        } );

    //Define una tabla con el listado de usuario del departamento
    detalleUsuarios =   $('#detalleUsuarios').DataTable( {
            "language": {
                "lengthMenu": "Muestra _MENU_ filas por página",
                "zeroRecords": "Cargando datos...",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "No hay información disponible",
                "infoFiltered": "(filtrado de _MAX_ total filas)",
                "search": "Buscar:",
                "loadingRecords": "Cargando datos..."
            },
            /*"ajax": {
                "url": url_cabecera_asignaciones
             },*/
            "columns": [
                {"data": "numero",
                "title": "#",
                "width":"10"
                },    
                {"data": "usrAsignado",
                 "title":"Usuario"
                },
                {"data": "nombres",
                 "title":"Nombres"
                },
                {"data": "apellidos",
                 "title":"Apellidos"
                },
                {"data": "estadoConexion",
                 "title": "Estado conexión",
                 "width":"100",
                 "render": function (data, type, full, meta)
                           {
                               var color  = "gray";
                               var estado = "No iniciado";
                               if (full.estadoConexion === "Disponible")
                               {
                                   color  = "green";
                                   estado = full.estadoConexion;
                               }
                               else if (full.estadoConexion === "Ausente")
                               {
                                   color  = "brown";
                                   estado = full.estadoConexion;
                               }
                               else if (full.estadoConexion === "Ocupado")
                               {
                                   color  = "orange";
                                   estado = full.estadoConexion;
                               }
                               else if (full.estadoConexion === "Almuerzo")
                               {
                                   color  = "#01579b";
                                   estado = full.estadoConexion;
                               }

                               var strDatoRetorna =
                               '<span class="glyphicon glyphicon-user" style="color:'+color+'"></span> '+estado;
                               return strDatoRetorna;
                           }
                },
                {"data": "extension",
                 "title":"Extensión",
                 "width": "50"
                },
                {"data": "acciones",
                 "title":"Acciones",
                 "width": "50",
                 "render": function (data, type, full, meta)
                           {
                               var strDatoRetorna = '<span>';

                               if (
                                   ($('#vistaSoporte').text().trim() === 'Vista Administrador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Jefe')
                                  )
                               {
                                    strDatoRetorna    +=     '<button type="button" class="btn btn-default btn-xs" '+
                                                             ' title="Editar número extensión" '+
                                                             'onClick="javascript:mostrarEditarNumeroExtension(\''+full.idPersonaRol+'\',\''
                                                                                                                  +full.extension+'\',\'N\',\'\');">'+
                                                             '<span class="glyphicon glyphicon-pencil"></span>'+
                                                             '</button>';
                                    strDatoRetorna    +=     '<button type="button" class="btn btn-default btn-xs" '+
                                                             ' title="Historial de conexión" '+
                                                             'onClick="javascript:mostrarHistorialConexion('+full.idPersonaRol+');">'+
                                                             '<span class="glyphicon glyphicon-header"></span>'+
                                                             '</button>';
                               }
                               return strDatoRetorna;
                           }
                }
            ],
            "columnDefs":
            [
                {"className": "dt-left", "targets": "_all"}
            ],
            "paging":false
        } )
    ;

    var boolMuestraPaginacion = false;
    var strTituloNumero = '#';
    if(permiteVerNuevosCamposTareas == 1){
        boolMuestraPaginacion = true;
        strTituloNumero = '<input class="form-check-input" type="checkbox" value="" id="checkTodoTareas">';
    }

    //Define los detalles del cuadro de asignaciones
    detalleTareas =   $('#detalleTareas').DataTable( {
            "language": {
                "lengthMenu": "Muestra _MENU_ filas por página",
                "zeroRecords": "Cargando datos...",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "No hay información disponible",
                "infoFiltered": "(filtrado de _MAX_ total filas)",
                "search": "Buscar:",
                "loadingRecords": "Cargando datos...",
            },
            "columns": [
                {
                "title": strTituloNumero,
                "data": "numero",
                "orderable": true,
                'render': function (data, type, full, meta){
                        if(permiteVerNuevosCamposTareas == 1){
                            return '<input type="checkbox" onchange="cambiaCheck(this)" class="select-checkbox select-checkbox-table">';
                        }else{
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }
                },
                {
                    "data": "numeroTarea",
                    "title":"N&uacute;mero Tarea",
                    "render": function (data, type, full, meta)
                    {
                        var strDatoRetorna = '<b>'+
                                            '<a target="_blank" href="'+url_mis_tareas_grid+'?numTarea='+full.numeroTarea+'">'+
                                            full.numeroTarea+
                                            '</a>'+
                                            '</b>';
                        return strDatoRetorna;
                    
                    }
                },    
                {"data": "feCreacion",
                "title":"Fecha asignaci&oacute;n"
                },
                {"data": "loginAfectado",
                "title": "Login"
                },
                {"data": "nombreTarea",
                "title": "Nombre Tarea"
                },
                {"data": "usrAsignado",
                "title": "Asignado"
                },
                {"data": "asignado",
                "title": "Dep. Asignado"
                },
                {
                "orderable" : false,
                "data"      : "EstadoHist",
                "title"     : "Estado tarea",
                "render": function (data, type, full, meta)
                        {
                                var strDatoRetorna   = "";

                                var estadoTarea      = full.EstadoHist;
                                var colorEstadoTarea = "default";
                                if (estadoTarea == 'Finalizada')
                                {
                                    colorEstadoTarea = "success";
                                }
                                else if (estadoTarea == 'Asignada')
                                {
                                    colorEstadoTarea = "danger";
                                }
                                else if (estadoTarea == 'Pausada')
                                {
                                    colorEstadoTarea = "primary";
                                }
                                else if (estadoTarea == null)
                                {
                                    return '';
                                }
                                strDatoRetorna = '<span class="label label-'+colorEstadoTarea+'" style="padding:4px;font-size:12px;">'
                                                +estadoTarea+'</span>';
                            return strDatoRetorna;
                        }

                }, 
                {"data": "usrCreacion",
                "title": "Usuario Creaci&oacute;n"
                },
                {"data": "departamentoCrea",
                "title": "Dep. Creaci&oacute;n"
                },
                {"data": "nombreProceso",
                "title": "Nombre Proceso"
                },
                {"data": "trazabilidadTareaCrea",
                "title": "Trazabilidad Tarea"
                },
                {"data": "observacionTarea",
                "title": "Observaci&oacute;n Tarea"
                },
                {"data": "empresaTarea",
                "title": "Empresa Tarea"
                },
                {"data": "ultimoDeptoAsig",
                "title": "&Uacute;ltimo dep. Asignado"
                },
                {"data": "sisUltimoUsrAsig",
                "title": "Sistema &Uacute;ltimo Usr Asignado"
                },
                {"data": "minutosTranscurridos",
                "title": "Tiempo"
                },
                {"data": "idDetalle",
                "title": "detalle"
                },
                {"data": "idPersonaEmpresaRol",
                "title": "personaEmpresaRol"
                },
                {"data": "idDetalleHist",
                "title": "Detalle Historial"
                },
                {"data": "acciones",
                "title":"Acciones",
                "orderable": false,
                "render": function (data, type, full, meta)
                        {
                            var strDatoRetorna = '<span>';
                            var tareas         = '';

                            if (
                                ($('#vistaSoporte').text().trim() === 'Vista Administrador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Jefe')
                                )
                            {
                                    tareas             = JSON.stringify(full);
                                    tareas             = tareas.replace(/"/g, '\\"');
                                    strDatoRetorna    +=    '<span class="hint--bottom-left hint--default hint--medium'+
                                                            ' hint--rounded" aria-label="Crear nueva asignación" >'+
                                                            '<button type="button" class="btn btn-default btn-xs" '+
                                                            "onClick='javascript:mostrarNuevaAsignacionTarea(\""+tareas+"\")'>"+
                                                            '<span class="glyphicon glyphicon-file"></span>'+
                                                            '</button>'+
                                                            '</span>';

                                    if(permiteVerNuevosCamposTareas == 1){
                                        tareasEjecucion  = JSON.stringify(full);
                                        tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                        if(full.EstadoHist == "Asignada" || full.EstadoHist == "Reprogramada")
                                        {
                                            full.accionTarea = "iniciar";
                                            tareasEjecucion  = JSON.stringify(full);
                                            tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                            strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                            ' hint--rounded btnEjecucionTarea" aria-label="Ejecutar Tarea" >'+
                                            '<button  type="button" class="btn btn-default btn-xs" '+
                                            "onClick='javascript:mostrarEjecucionTarea(\""+tareasEjecucion+"\")'>"+
                                            '<span class="glyphicon glyphicon-play"></span>'+
                                            '</button>'+
                                            '</span>';
                                        }

                                        if(full.EstadoHist == "Pausada")
                                        {
                                            full.accionTarea = "reanudar";
                                            tareasEjecucion  = JSON.stringify(full);
                                            tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                            strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                            ' hint--rounded btnEjecucionTarea" aria-label="Reanudar Tarea" >'+
                                            '<button type="button" class="btn btn-default btn-xs" '+
                                            "onClick='javascript:mostrarEjecucionTarea(\""+tareasEjecucion+"\")'>"+
                                            '<span class="glyphicon glyphicon-step-forward"></span>'+
                                            '</button>'+
                                            '</span>';
                                        }

                                        if(full.EstadoHist == "Aceptada")
                                        {
                                            full.accionTarea = "pausar";
                                            strBanderaFinalizarInformeEjecutivo = full.strBanderaFinalizarInformeEjecutivo
                                            tareasEjecucion  = JSON.stringify(full);
                                            tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                            strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                            ' hint--rounded btnEjecucionTarea" aria-label="Pausar Tarea" >'+
                                            '<buttontype="button" class="btn btn-default btn-xs" '+
                                            "onClick='javascript:mostrarEjecucionTarea(\""+tareasEjecucion+"\")'>"+
                                            '<span class="glyphicon glyphicon-pause"></span>'+
                                            '</button>'+
                                            '</span>';
                                            if(permiteAccionesTareas == 1 && strBanderaFinalizarInformeEjecutivo == 'S'){
                                                strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                ' hint--rounded btnFinalizarTarea" aria-label="Finalizar Tarea" >'+
                                                '<buttontype="button" class="btn btn-default btn-xs" '+
                                                "onClick='javascript:finalizarTarea(\""+tareasEjecucion+"\",this)'>"+
                                                '<span class="glyphicon glyphicon-stop"></span>'+
                                                '</button>'+
                                                '</span>';
                                            }
                                        }
                                        if(full.EstadoHist == "Aceptada" || full.EstadoHist == "Pausada")
                                        {
                                            full.accionTarea = "reasignar";
                                            strBanderaFinalizarInformeEjecutivo = full.strBanderaFinalizarInformeEjecutivo
                                            tareasEjecucion  = JSON.stringify(full);
                                            tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                            if(permiteAccionesTareas == 1){
                                                strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                ' hint--rounded btnReasignarTarea" aria-label="Reasignar Tarea" >'+
                                                '<buttontype="button" class="btn btn-default btn-xs" '+
                                                "onClick='javascript:reasignarTarea(\""+tareasEjecucion+"\",this)'>"+
                                                '<span class="glyphicon glyphicon-dashboard"></span>'+
                                                '</button>'+
                                                '</span>';
                                            }
                                        }
                                        if(full.EstadoHist != "Pausada" && full.EstadoHist != "")
                                        {
                                            strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                            ' hint--rounded btnCargarArchivo" aria-label="Cargar Archivo" >'+
                                            '<button  type="button" class="btn btn-default btn-xs" '+
                                            "onClick='javascript:subirMultipleAdjuntosTarea(\""+tareasEjecucion+"\")'>"+
                                            '<span class="glyphicon glyphicon-open-file"></span>'+
                                            '</button>'+
                                            '</span>';
                                        }
                                        if(full.idDetalle != '' && full.strTareaIncAudMan != '')
                                        {
                                            strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                            ' hint--rounded btnVerArchivo" aria-label="Ver Archivos" >'+
                                                            '<button  type="button" class="btn btn-default btn-xs" '+
                                                            "onClick='javascript:presentarDocumentosTareas("+full.idDetalle+
                                                            ",\""+full.strTareaIncAudMant+"\",this)'>"+
                                                            '<span class="glyphicon glyphicon-eye-open"></span>'+
                                                            '</button>'+
                                                            '</span>';
                                        }

                                        strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                            ' hint--rounded btnIngresoSeguimiento" aria-label="Ingresar Seguimiento" >'+
                                            '<button  type="button" class="btn btn-default btn-xs" '+
                                            "onClick='javascript:mostrarIngresoSeguimiento(\""+tareasEjecucion+"\")'>"+
                                            '<span class="glyphicon glyphicon-list-alt"></span>'+
                                            '</button>'+
                                            '</span>';

                                        strDatoRetorna    += '<span class="hint--bottom-left hint--default hint--medium'+
                                            ' hint--rounded btnSeguimiento" aria-label="Mostrar Seguimientos" >'+
                                            '<button  type="button" class="btn btn-default btn-xs" '+
                                            "onClick='javascript:mostrarSeguimiento(\""+tareasEjecucion+"\")'>"+
                                            '<span class="glyphicon glyphicon-search"></span>'+
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
                {"className": "dt-center", "targets": [0,1,5,6,7,8,13,14,15]},
                {"className": "dt-left", targets: [2,3,4]},
                {
                    targets: [11],
                    render: function ( data, type, row ) {
                        var dataViewMore = data;
                        var idPopover = (typeof row.numeroTarea !== 'undefined')?'div-popover-'+row.numeroTarea:'div-popover-'+row.numero;
                        idPopover = 'tra-'+idPopover;
                        return type === 'display' && data.length > 30 ?
                            data.substr( 0, 30 ) +' <a id="'+idPopover+'" class="obs-popover" title="Trazabilidad Tarea" onClick="javascript:showPopover(\''+idPopover+'\',\''+dataViewMore+'\');" data-toggle="popover">  leer más …</a><script>showPopover(\''+idPopover+'\',\''+dataViewMore+'\');</script>' :
                            data;
                    }
                },
                {
                    targets: [12],
                    render: function ( data, type, row ) {
                        var dataViewMore = data;
                        var idPopover = (typeof row.numeroTarea !== 'undefined')?'div-popover-'+row.numeroTarea:'div-popover-'+row.numero;
                        idPopover = 'obs-'+idPopover;
                        return type === 'display' && data.length > 30 ?
                            data.substr( 0, 30 ) +' <a id="'+idPopover+'" class="obs-popover" title="Observación Tarea" onClick="javascript:showPopover(\''+idPopover+'\',\''+dataViewMore+'\');" data-toggle="popover">  leer más …</a><script>showPopover(\''+idPopover+'\',\''+dataViewMore+'\');</script>' :
                            data;
                    }
                }  
            ],
            select: { 
                style: 'os', 
                selector: 'td:first-child' 
            }, 
            order: [ 
                [1, 'desc'] 
            ],
            "paging":boolMuestraPaginacion,
            scrollY: 600,
            scrollX: true,
            autoWidth: true,
            initComplete: function() {
              $('div.dataTables_scrollHeadInner thead tr#filterboxrow th').each(function() {
                if($(this).index() == 0 || $(this).index() == 4 || $(this).index() == 5 || $(this).index() == 6 || $(this).index() == 7)
                {
                    $(this).html('<input id="input' + $(this).index() + '" type="text" style="color:black; height: 25px;width: 98%"  placeholder="' + $(this).text() + '" />');
                }else
                {
                    $(this).html('<input id="input' + $(this).index() + '" type="text" style="color:black; height: 25px;"  placeholder="' + $(this).text() + '" />');
                }

                $(this).on('keyup change', function() {
                  var val;

                  if ($(this).index() === 0) {
                    val = $('.DTFC_Cloned #input' + $(this).index()).val()
                  } else {
                    val = $('#input' + $(this).index()).val();
                  }

                  detalleTareas.column($(this).index()).search(val).draw();
              });
          });
            }
        } )
        .on('preXhr.dt', function ( e, settings, data ) {
                   $("#divLoaderDetalleTareas").show();
            } )
        .on('xhr.dt', function ( e, settings, json, xhr ) {
           $("#divLoaderDetalleTareas").hide();
        } )
    ;

    var dataTable = $('#detalleTareas').DataTable();
    if(permiteVerNuevosCamposTareas == 1){
        $('#input0').hide();
        $('#input16').hide();
        $('#input4').css("width", "100%");
        $('#input10').css("width", "100%");
        $('#input11').css("width", "100%");
        $('#input12').css("width", "100%");
        dataTable.columns([16, 17, 18, 19]).visible(false);
    }else{
        dataTable.columns([8,9,10,11,12,13,14,15, 16, 17, 18, 19]).visible(false);
        $('#filterboxrow').hide();
        $('#btnAsignarMasivo').hide();
        $('#rowFiltrosFecha').css("display", "none");
    }

    //Define una tabla con el listado de usuario del departamento
    detalleTiposProblema =   $('#detalleTiposProblema').DataTable( {
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
            {
                "title"     : "#",
                "data"      :  null,
                "render"    : function (data, type, full, meta)
                              {
                                  strDatoRetorna = meta.row+1;
                                  return strDatoRetorna;
                              }
            },   
            {"data": "valor2",
             "title":"Tipo Problema"
            },
            {"data": "acciones",
             "title":"Acciones",
             "width": "50",
             "render": function (data, type, full, meta)
                       {
                           var strDatoRetorna = '<span>';

                           if (
                               ($('#vistaSoporte').text().trim() === 'Vista Administrador' ||
                                $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
                                $('#vistaSoporte').text().trim() === 'Vista Jefe')
                              )
                           {
                                strDatoRetorna    +=    '<span class="hint--bottom-right hint--default hint--medium'+
                                                        ' hint--rounded" aria-label="Editar Tipo de problema">'+
                                                        '<button type="button" class="btn btn-default btn-xs" '+
                                                        'onClick="javascript:mostrarEditarTipoProblema(\''+full.id+'\',\''+
                                                                                                            full.descripcion+'\',\''+
                                                                                                            full.valor2+'\',\''+
                                                                                                            full.valor3+'\');">'+
                                                        '<span class="glyphicon glyphicon-pencil"></span>'+
                                                        '</button></span>';
                                strDatoRetorna    +=    '<span class="hint--bottom-right hint--default hint--medium'+
                                                        ' hint--rounded" aria-label="Eliminar Tipo de problema">'+
                                                        '<button type="button" class="btn btn-default btn-xs" '+
                                                        '     onClick="javascript:mostrarConfirmacionEliminaRegistro(\''+
                                                        full.id+'\',\''+
                                                        'tipo problema'+'\');">'+
                                                        '    <span class="glyphicon glyphicon-remove"></span>'+
                                                        '</button></span>';

                           }
                           return strDatoRetorna;
                       }
            }
        ],
        "columnDefs":
        [
            {"className": "dt-left", "targets": "_all"}
        ],
        "paging":false
    } );

    /**
     * Define el formato para mostrar seguimientos por asignación en grid de detalles
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     * @param datos => seguimientos de la asignación consultada
     */
    function format ( id, datos ) {        
        var seguimientos = JSON.parse(datos.seguimientos);
        var html = '<p><h4>Seguimientos (3 ultimos)</h4></p>';
            html += '<table id="detalles" class="table" style="width:100%;border-color: #ddd">';
            html += '<thead><tr>'+
                    '<th></th>'+
                    '<th>Fecha</th>'+
                    '<th>UsrCreacion</th>'+
                    '<th>Detalle</th>'+
                    '<th>Asignado</th>'+
                    '<th>Acciones</th>'+
                    '</tr></thead>';
            html += '<tbody>';

            for ( var i=0, ien=seguimientos.length ; i<ien ; i++ ) 
            {
                var colorTd  = 'background-color:#FFFFFF;';
                var acciones = '<td></td>';
                if (seguimientos[i].USR_GESTION !=='' && seguimientos[i].GESTIONADO==='N')
                {
                    colorTd  = 'background-color:#EF9A9A;';
                    acciones = '<span><button type="button" class="btn btn-danger btn-xs" '+
                                             '     onClick="javascript:mostrarRespuestaAsignarSeguimiento('+
                                                    id+','+seguimientos[i].ID_SEGUIMIENTO_ASIGNACION+');">'+
                                             '    <span class="glyphicon glyphicon-check"></span>'+
                                             '</button></span>';
                }
                else if (seguimientos[i].USR_GESTION !=='' && seguimientos[i].GESTIONADO==='S')
                {
                    colorTd = 'background-color:#A9A9F5;';
                }
                html += '<tr>';
                html += '<td style="'+colorTd+'text-align:left;'+'"><span class="glyphicon glyphicon-chevron-down"></span></td>';
                html += '<td style="'+colorTd+'">'+seguimientos[i].FE_CREACION+'</td>';
                html += '<td style="'+colorTd+'">'+seguimientos[i].USR_CREACION+'</td>';
                html += '<td style="'+colorTd+'">'+seguimientos[i].DETALLE+'</td>';
                html += '<td style="'+colorTd+'">'+seguimientos[i].USR_GESTION+'</td>';
                html += '<td style="'+colorTd+'">'+acciones+'</td>';
                html += '</tr>';
                var hijos = [];
                var hijos = seguimientos[i].HIJOS;
                if(hijos.length > 0)
                {
                    colorTd='background-color:#E0E0F8;';
                    for ( var ih=0, ienh=hijos.length ; ih<ienh ; ih++ ) 
                    {
                        html += '<tr>';
                        html += '<td style="'+colorTd+'text-align:right;'+'"><span class="glyphicon glyphicon-chevron-right"></span></td>';
                        html += '<td style="'+colorTd+'">'+hijos[ih].FE_CREACION+'</td>';
                        html += '<td style="'+colorTd+'">'+hijos[ih].USR_CREACION+'</td>';
                        html += '<td style="'+colorTd+'">'+hijos[ih].DETALLE+'</td>';
                        html += '<td style="'+colorTd+'">'+hijos[ih].USR_GESTION+'</td>';
                        html += '<td style="'+colorTd+'"></td>';
                        html += '</tr>';
                    }
                }
            }
            html+='</tbody></table>';
        return html;
    }

    

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
    
    var selected = [];

    //Define los detalles de cambio de turno
    detalleCambioTurno =   $('#detalleCambioTurno').DataTable( {
            "language": {
                "lengthMenu": "Muestra _MENU_ filas por página",
                "zeroRecords": "",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "No hay información disponible",
                "infoFiltered": "(filtrado de _MAX_ total filas)",
                "search": "Buscar:",
                "loadingRecords": "Cargando datos..."
            },
            "searching": false,
            "ordering" : false,
             "rowCallback": function( row, data ) {
                 if ( $.inArray(data.DT_RowId, selected) !== -1 ) {
                     $(row).addClass('selected');
                 }
             },
            "columns": [
                {"data": "numero","title":"#"},    
                {"data": "casoTarea","title":"Caso/Tarea"},  
                {
                    "data": "feCreacion",
                    "title":"Fecha",
                    "render":function(data, type, full, meta)
                    {
                        var colorCiudad = "#f9fbe7;";

                        if(full.ciudad == 'GUAYAQUIL')
                        {
                            colorCiudad = "#efebe9;";
                        }
                        else if(full.ciudad == 'QUITO')
                        {
                            colorCiudad = "#c8e6c9;";
                        }
                        var strDatoRetorna="<div>"+full.feCreacion+"</div>"+
                                           "<div style=\"background-color:"+colorCiudad+"\">"+full.ciudad+"</div>";
                        return strDatoRetorna;
                    }
                },
                {"data": "referenciaCliente","title":"Cliente"},
                {"data": "origen","title":"Origen"},
                {"data": "loginAfectado","title":"Login"},
                {"data": "tipoAtencion","title":"Tipo Atención"},
                {"data": "tipoProblema","title":"Tipo Problema"},
                {"data": "usrAsignado",
                 "title":"Asignado",
                 "render": function (data, type, full, meta)
                           {
                               var idInput        = 'txtAgenteCambioTurno'+full.id;
                               var nombreInput    = 'agenteCambioTurno'+full.id;
                               var usrAsignado    = '';
                               if (full.usrAsignado != null && full.usrAsignado != 'null' && full.usrAsignado != '')
                               {
                                usrAsignado    = full.usrAsignado;
                               }

                               agentesCambioTurno.push(idInput);
                               var strDatoRetorna =
                               '<div class="autocomplete">'+
                               '    <input id="'+idInput+'" class="inputtxt" type="text" name="'+nombreInput+
                                                           '" placeholder="Agente" value="'+usrAsignado+
                                                           '" onChange="agregarReasignadoCambioTurno('+full.id+')">'+
                               '</div>';
                               return strDatoRetorna;
                           }
                },
                {"data": "estadoCaso","title":"Estado Caso"},
                {"data": "estadoTarea", "title": "Estado Tarea"}
            ],
            "columnDefs":
            [
                {"className": "dt-center", "targets": "_all"}
            ],
            "paging":false
        } )
    .on('preXhr.dt', function ( e, settings, data ) {
        agentesCambioTurno=[];
        $("#divLoaderCambioTurno").show();
        } )
    .on('xhr.dt', function ( e, settings, json, xhr ) {
        $("#divLoaderCambioTurno").hide();
        })        
    .on( 'draw', function () {
        
        for ( var i=0, ien=agentesCambioTurno.length ; i<ien ; i++ ) 
        {
            autocomplete(document.getElementById(agentesCambioTurno[i]), agentes);
        }
    } );

    $('#detalleCambioTurno tbody').on('change', 'tr', function () {
        $(this).toggleClass('selected');
    } );

    //validación para mostrar columna Origen y ocultar columna Cliente en grid cambio de turno para usuarios IPCCL1
    var dataDetalleCambioTurno = $('#detalleCambioTurno').DataTable();
    if(strIdDepartamentoUsrSession == 132){
        dataDetalleCambioTurno.columns([3]).visible(false);
    }else{
        dataDetalleCambioTurno.columns([4]).visible(false);
    }

    //Define los detalles de historial de conexión de un usuario
    detalleHistorialConexion =   $('#detalleHistorialConexion').DataTable( {
        "language": {
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "loadingRecords": "Cargando datos..."
        },
        "searching": false,
        "ordering" : false,
        "columns": [
            {
                "title"     : "#",
                "data"      :  null,
                "render"    : function (data, type, full, meta)
                              {
                                  strDatoRetorna = meta.row+1;
                                  return strDatoRetorna;
                              }
            },
            {"data": "fecha","title":"Fecha"},
            {"data": "extension","title":"Extensión"},
            {"data": "estado","title":"Estado"}
        ],
        "columnDefs":
        [
            {"className": "dt-center", "targets": "_all"}
        ],
        "paging":false
    } )
    .on('preXhr.dt', function ( e, settings, data ) {
        $("#divLoaderHistorialConexion").show();
        } )
    .on('xhr.dt', function ( e, settings, json, xhr ) {
        $("#divLoaderHistorialConexion").hide();
    }) ;
    
    /**
     * Actualización: Se invocó el evento onclick de la consulta para aprovechar la validación 
     *                de los campos fechaIni y fechaFin.
     * @author Miguel Angulo <jmangulos@telconet.ec>
     * @version 1.2 05-06-2019
     * 
     * Actualización: se configura para que se actualice automaticamente cada 6 minutos
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 29-11-2018
     * 
     * Configura la actualizacion del cuadro de asignaciones y del grid detalle de asignaciones cada 2 minutos
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
    */
    setInterval(
                function () {
                                reloadCuadroAsignaciones();
                                if ($('#divListadoAsignaciones').css('display') !== 'none')
                                {
                                    document.getElementById("btnBuscarPorFecha").onclick();
                                }
                                if (($('#vistaSoporte').text().trim() === 'Vista Administrador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
                                    $('#vistaSoporte').text().trim() === 'Vista Jefe'
                                   )&& $('#divCharts').css('display') !== 'none')
                                {
                                    seleccionaFechaGrafica($('#reportrange').data('daterangepicker').startDate.format('YYYY/MM/DD'), 
                                                           $('#reportrange').data('daterangepicker').endDate.format('YYYY/MM/DD'));
                                }
                            }, 
                    360000 
               );

    /**
     *
     * Actualización: Se incluye validación por vista tipo de vista
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 26-09-2018
     *
     * Define el cuadro de asignaciones en Datatable
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     */
    function reloadCuadroAsignaciones()
    {
        if (($('#vistaSoporte').text().trim() === 'Vista Administrador' ||
            $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
            $('#vistaSoporte').text().trim() === 'Vista Jefe'
           )&& $('#divCuadroAsignaciones').css('display') !== 'none')
        {
            cabeceraCuadroAsignaciones();

        }
        else if(($('#vistaSoporte').text().trim() === 'Vista Agente') && $('#divCuadroAsignacionesUsr').css('display') !== 'none')
        {
            cuadroAsignacionesUsr.ajax.url(url_asignaciones_usuario).load();
        }
    }


    /**
     * 
     * Actualización: Envia parametros de fecha conexión y cantidad de asignaciones
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 22-01-2019
     * 
     * Envia parametro para el ordenamiento de los agentes del cuadro general
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 10-01-2019
     * 
     * Lee la cabecera del cuadro de asignaciones por medio de un ajax
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     */
    function cabeceraCuadroAsignaciones()
    {
        var resultado;
        var parametros= {
                            "orden"    : ordenAgentesCuadroGen,
                            "idCanton" : document.getElementById('txtIdBtnCantonesCuadroGen').value
                        };
            $.ajax({
                        data:  parametros,
                        url :  url_cabecera_asignaciones,
                        type:  'post',
                        beforeSend: function () {
                                $("#divLoaderCuadroAsignaciones").show();
                                $("#divLegendaCuadroAsignaciones").hide();
                        },
                        success:  function (response) {
                                $("#divLoaderCuadroAsignaciones").hide();
                                $("#divLegendaCuadroAsignaciones").show();
                                resultado = "<tr>";
                                agentes   = [];
                                columnas  = [];
                                var numeroAsignado = 0;
                                for (var i in response.data) 
                                {
                                    if (response.data.hasOwnProperty(i))
                                    {
                                        numeroAsignado = parseInt(i) + parseInt(1);
                                        resultado+= "<th style='height:120px;width:21.7333px;'>"+response.data[i].usrAsignado+"</th>";
                                        agregarDefinicionColumna(response.data[i].usrAsignado, 
                                                                 numeroAsignado, 
                                                                 response.data[i].estadoConexion,
                                                                 response.data[i].feEstadoConexion,
                                                                 response.data[i].cantidad
                                                                );
                                        agentes.push(response.data[i].usrAsignado);
                                    }
                                }
                                resultado+="</tr>";
                                $("#columnasCabecera").html(resultado);
                                definirCuadroDeAsignaciones(resultado);
                                obtienePendientesDepartamento();
                                autocomplete(document.getElementById("txtAgente"), agentes);
                                autocomplete(document.getElementById("txtAgenteLote"), agentes);

                        },
                        failure: function(response){
                                console.log("failure");
                        }
                    });
    }
    
    //llenar las cabeceras del cuadro de asignaciones
    if ($('#vistaSoporte').text().trim() === 'Vista Administrador')
    {
        cabeceraCuadroAsignaciones();
    }



    //define configuracion de ventana de ingreso de asignaciones
    $('.modal-content').resizable({
    minHeight: 300,
    minWidth: 300
    });
    //configura a arrastrable la ventana de ingreso de asignaciones
    $('.modal-dialog').draggable();
    
    //define max height la ventana de ingreso de asignaciones
    $('#modalNuevaAsignacion').on('show.bs.modal', function() {
      $(this).find('.modal-body').css({
        'max-height': '100%'
      });
    });

    //define max height la ventana de ingreso de asignaciones
    $('#modalAgregarNumeroTarea').on('show.bs.modal', function() {
      $(this).find('.modal-body').css({
        'max-height': '100%'
      });
    });
    //define max height la ventana de ingreso de asignaciones
    $('#modalCambioTurno').on('show.bs.modal', function() {
      $(this).find('.modal-body').css({
        'max-height': '100%'
      });
    });
    //define max height la ventana de ingreso de asignaciones
    $('#modalAsignarSeguimiento').on('show.bs.modal', function() {
      $(this).find('.modal-body').css({
        'max-height': '100%'
      });
    });

    /**
     * Actualización: Se añade la funcionalidad para resetear los filtros de datable detalleTareas al grabar una nueva asignación, 
     *                con el objetivo que al refrescar los registros se visualicen todas las tareas.
     *                Se agregan reseteos de boton al grabar una nueva asignación 
     * @author Fernando Lopez <filopez@telconet.ec>
     * @version 1.6 29-10-2021
     * 
     * Actualización: Se añade la funcionalidad para asignar por lotes de tareas
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.5 01-07-2021
     * @since 1.0
     *
     * Actualización: Se modifica la consulta para considerar los campos 
     *                de fecha al realizar una nueva consulta.
     *                Se agrega un campo de parámetro tipo array para enviar
     *                las asignaciones Proactivas seleccionadas
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.4 18-06-2019
     * 
     * Actualización: Se agrega acción de controlar reactivacion del boton grabar asignación
     * @author Miguel Angulo <jmangulos@telconet.ec>
     * @version 1.3 09-06-2019
     * 
     * Actualización: Se permite agregar el numero de 
     * tarea o caso directamente al momento de crear una asignación para todos los usuarios
     * (Ya no es necesario tener credencial)
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 10-01-2019
     * 
     * Actualización: Se permite agregar el numero de 
     * tarea o caso directamente al momento de crear una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 03-10-2018
     *
     * Graba una nueva asignación por medio un ajax
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     */
    function grabarNuevaAsignacion()
    {
        var strNumero = "";
        var boolValidaAsignacion = false;
        var boolAsignacionLote = false;
        var arrayTareasSeleccionadas = [];
        if($('#tareasSeleccionadas').val()){
            arrayTareasSeleccionadas = $('#tareasSeleccionadas').val().split(','); 
            arrayTareasSeleccionadas = arrayTareasSeleccionadas.filter(item => item); 
        }else{
            arrayTareasSeleccionadas = [];
        }

        if (arrayTareasSeleccionadas.length > 0) {
            boolValidaAsignacion = validaNuevaAsignacionLote(); 
            boolAsignacionLote = true; 
        }else{
            boolValidaAsignacion = validaNuevaAsignacion(); 
        } 

        if(boolValidaAsignacion) 
        { 
            if (arrayTareasSeleccionadas.length > 0) { 
                strNumero = arrayTareasSeleccionadas; 
            }else{ 
                strNumero = document.getElementById('txtNumeroTareaCasoNuevaAsig').value; 
            } 

            var strUpdateAsignacion = 'N';
            if($('#divListadoAsignaciones').css('display') !== 'none' && $('#liDetalles').hasClass('active') == true 
                && permiteVerNuevosCamposTareas == 1)
            {
                strUpdateAsignacion = 'S';
            }

            var parametros = [];
            if(boolAsignacionLote){ 
                parametros = { 
                    "strOrigen"         : document.getElementById('cmbOrigenLote').value,
                    "strTipoAtencion"   : document.getElementById('cmbTipoAtencionLote').value,
                    "strTipoProblema"   : document.getElementById('cmbTipoProblemaLote').value,
                    "strCriticidad"     : document.getElementById('cmbCriticidadLote').value,
                    "strAgente"         : document.getElementById('txtAgenteLote').value,
                    "strDetalle"        : document.getElementById('txtaDetalleLote').value,
                    "strNumero"         : strNumero, 
                    "arrayAsigProact"   : Ext.JSON.encode(arrayAsignacionProactiva) 
                };
            }else{
                parametros = { 
                    "strOrigen"         : document.getElementById('cmbOrigen').value, 
                    "strTipoAtencion"   : document.getElementById('cmbTipoAtencion').value, 
                    "strLogin"          : document.getElementById('txtLogin').value, 
                    "strTipoProblema"   : document.getElementById('cmbTipoProblema').value, 
                    "strNombreReporta"  : document.getElementById('txtNombreReporta').value, 
                    "strNombreSitio"    : document.getElementById('txtNombreSitio').value, 
                    "strCriticidad"     : document.getElementById('cmbCriticidad').value, 
                    "strAgente"         : document.getElementById('txtAgente').value, 
                    "strDetalle"        : document.getElementById('txtaDetalle').value, 
                    "strNumero"         : strNumero, 
                    "arrayAsigProact"   : Ext.JSON.encode(arrayAsignacionProactiva) 
                }; 
            }
            parametros.strUpdateAsignacion = strUpdateAsignacion;
            $.ajax({
                    data :  parametros,
                    url  :  url_crea_asignacion,
                    type :  'post',
                    beforeSend: function () {
                        if(boolAsignacionLote){
                            $('#btnLoadingGrabarAsigLote').show();
                            $('#btnGrabarAsignacionLote').hide();
                        }else{
                            $('#btnLoadingGrabarAsig').show();
                            $('#btnGrabarAsignacion').hide();
                        }
                        $('#modalConfirmarGrabarAsignacion').modal('hide');
                    },
                    success:  function (response) {
                        if (response === 'OK')
                        {
                            reloadCuadroAsignaciones();
                            document.getElementById("btnBuscarPorFecha").onclick();
                            if(boolAsignacionLote){
                                configuraMensajeIngresoConExito('#alertaValidaNuevaAsignacionLote',
                                                            '<strong>Se Grabaron datos con éxito</strong>',
                                                            '#btnLoadingGrabarAsigLote',
                                                            '#btnGrabarAsignacionLote');

                                $('#btnGrabarAsignacionLote').attr('disabled','disabled');
                            }else{
                                configuraMensajeIngresoConExito('#alertaValidaNuevaAsignacion',
                                                            '<strong>Se Grabaron datos con éxito</strong>',
                                                            '#btnLoadingGrabarAsig',
                                                            '#btnGrabarAsignacion');

                                $('#btnGrabarAsignacion').attr('disabled','disabled');
                            }
                            arrayAsignacionProactiva=[];
                            //se cierra ventana luego de 2 segundos
                             setTimeout(function() {
                                 $('#btnGrabarAsignacion').removeAttr('disabled');
                                 $('#btnGrabarAsignacionLote').removeAttr('disabled');
                                 var arrayCampos = [];
                                 if(boolAsignacionLote){ 
                                    $('#modalAsignacionLote').modal('hide');
                                    $('#btnAsignarMasivo').prop("disabled",true);

                                    arrayCampos = ['cmbOrigenLote',
                                                        'cmbTipoAtencionLote',
                                                        'cmbTipoProblemaLote',
                                                        'cmbCriticidadLote',
                                                        'txtAgenteLote',
                                                        'txtaDetalleLote',
                                                        'input1',
                                                        'input2',
                                                        'input3',
                                                        'input4',
                                                        'input5',
                                                        'input6',
                                                        'input7',
                                                        'input8',
                                                        'input9',
                                                        'input10',
                                                        'input11',
                                                        'input12',
                                                        'input13',
                                                        'input14',
                                                        'input15'
                                                    ];

                                    limpiarCampos('#alertaValidaNuevaAsignacionLote',arrayCampos);
                                }else{
                                    $('#modalNuevaAsignacion').modal('hide');

                                    arrayCampos = ['cmbOrigen', 
                                                        'cmbTipoAtencion', 
                                                        'txtLogin',
                                                        'cmbTipoProblema',
                                                        'txtNombreReporta',
                                                        'txtNombreSitio',
                                                        'cmbCriticidad',
                                                        'txtAgente',
                                                        'txtaDetalle',
                                                        'txtNumeroTareaCasoNuevaAsig',
                                                        'input1',
                                                        'input2',
                                                        'input3',
                                                        'input4',
                                                        'input5',
                                                        'input6',
                                                        'input7',
                                                        'input8',
                                                        'input9',
                                                        'input10',
                                                        'input11',
                                                        'input12',
                                                        'input13',
                                                        'input14',
                                                        'input15'                                                        
                                                    ];
                                    limpiarCampos('#alertaValidaNuevaAsignacion',arrayCampos);
                                }

                                 
                             }, 2000);
                            if ($('#divListadoTareas').css('display') !== 'none')
                            {
                                //Resetear filtros de fecha
                                document.getElementById('txtDatetimepickerBuscaAsignacionTarea').value = "";
                                document.getElementById('txtDatetimepickerBuscaAsignacionFinTarea').value = "";
                                $('#btnBuscarPorFechaTarea').prop("disabled",true);

                                if(permiteVerNuevosCamposTareas == 1)
                                {
                                    detalleTareas.search('').columns().search('').draw();
                                }
                                detalleTareas.ajax.url(url_tareas_grid).load();                                
                            }
                        }
                        else
                        {
                            $("#divTxtNumeroTareaCasoNuevaAsig").removeClass('has-success');
                            $("#divTxtNumeroTareaCasoNuevaAsig").addClass('has-error');

                            if(boolAsignacionLote){
                                configuraMensajeIngresoFallido('#alertaValidaNuevaAsignacionLote',
                                                           '<strong>'+response+'</strong>',
                                                           '#btnLoadingGrabarAsigLote',
                                                           '#btnGrabarAsignacionLote');
                                $('#btnGrabarAsignacionLote').removeAttr('disabled');
                            }else{
                                configuraMensajeIngresoFallido('#alertaValidaNuevaAsignacion',
                                                           '<strong>'+response+'</strong>',
                                                           '#btnLoadingGrabarAsig',
                                                           '#btnGrabarAsignacion');
                                $('#btnGrabarAsignacion').removeAttr('disabled');
                            }
                        }
                    },
                    failure: function(response){
                        if(boolAsignacionLote){
                            configuraMensajeIngresoFallido('#alertaValidaNuevaAsignacionLote',
                                                       '<strong>'+response+'</strong>',
                                                       '#btnLoadingGrabarAsigLote',
                                                       '#btnGrabarAsignacionLote');
                            $('#btnGrabarAsignacionLote').removeAttr('disabled');
                        }else{
                            configuraMensajeIngresoFallido('#alertaValidaNuevaAsignacion',
                                                       '<strong>'+response+'</strong>',
                                                       '#btnLoadingGrabarAsig',
                                                       '#btnGrabarAsignacion');
                            $('#btnGrabarAsignacion').removeAttr('disabled');
                        }
                    }
            });
        }
        else
        {
            $('#modalConfirmarGrabarAsignacion').modal('hide');
            document.getElementById("btnGrabarAsignacion").disabled = false;
            document.getElementById("btnGrabarAsignacionLote").disabled = false;
        }
    }


    /**
     * Actualización: Se modifica la consulta para considerar los campos 
     *                de fecha al realizar una nueva consulta.
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.2 18-06-2019
     * 
     * Actualización: Se permite agregar el numero de 
     * tarea o caso directamente al momento de crear una asignación
     * y se realiza la validación del formato del numero que se ingresa
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 03-10-2018
     *
     * Graba numero de tarea en la asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-07-2018
     * @since 1.0
     */
    function grabarNumeroTarea()
    {
        //VALIDA QUE LOS CAMPOS NO ESTEN VACIOS
        if ( validaVaciosAgregaNumero() )
        {
            var numeroTareaCaso = document.getElementById('txtNumeroTareaCaso').value;
            var tipoAtencion    = document.getElementById('cmbTipoAtencionEdit').value;
            var tipoProblema    = document.getElementById('cmbTipoProblemaEdit').value;
            //VALIDA QUE EL FORMATO DEL NUMERO DE TAREA O CASO SEA CORRECTO
            if(validaFormatoNumeroCasoTarea(tipoAtencion,'#alertaValidaNumeroTarea','#divTxtNumeroTareaCaso','txtNumeroTareaCaso'))
            {
                var parametros = {
                    "strNumeroTarea" : numeroTareaCaso,
                    "intId"          : document.getElementById('txtIdAsignacionEdit').value,
                    "strTipoAtencion": tipoAtencion,
                    "strTipoProblema": tipoProblema
                };
                $.ajax({
                        data :  parametros,
                        url  :  url_graba_numero_tarea,
                        type :  'post',
                        beforeSend: function () {
                                $('#btnLoadingGrabarNumeroTareaCaso').show();
                                $('#btnGrabarNumeroTareaCaso').hide();
                        },
                        success:  function (response) {
                                if(response==="OK")
                                {
                                    reloadCuadroAsignaciones();
                                    document.getElementById("btnBuscarPorFecha").onclick();
                                    configuraMensajeIngresoConExito('#alertaValidaNumeroTarea',
                                                                    '<strong>Se Grabaron datos con éxito</strong>',
                                                                    '#btnLoadingGrabarNumeroTareaCaso',
                                                                    '#btnGrabarNumeroTareaCaso');
                                    $('#btnGrabarNumeroTareaCaso').attr('disabled','disabled');
                                    //se cierra ventana luego de 2 segundos
                                    setTimeout(function() {
                                        $('#btnGrabarNumeroTareaCaso').removeAttr('disabled');
                                        $('#modalAgregarNumeroTarea').modal('hide');
                                        var arrayCampos = ['cmbTipoAtencionEdit','cmbTipoProblemaEdit','txtNumeroTareaCaso','txtIdAsignacionEdit'];
                                        limpiarCampos('#alertaValidaNumeroTarea',arrayCampos);


                                    }, 2000);
                                }
                                else
                                {
                                    configuraMensajeIngresoFallido('#alertaValidaNumeroTarea',
                                                                   '<strong>'+response+'</strong>',
                                                                   '#btnLoadingGrabarNumeroTareaCaso',
                                                                   '#btnGrabarNumeroTareaCaso');
                                }
                        },
                        failure: function(response){
                            configuraMensajeIngresoFallido('#alertaValidaNumeroTarea',
                                                           '<strong>'+response+'</strong>',
                                                           '#btnLoadingGrabarNumeroTareaCaso',
                                                           '#btnGrabarNumeroTareaCaso');
                        }
                });
            }
        }
    }

    /**
     * Actualización: Se modifica la consulta para considerar los campos 
     *                de fecha al realizar una nueva consulta.
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.3 18-06-2019
     * 
     * Actualización: Se realiza corrección de ortografía en mensaje 
     *                de error si no se escogio asignaciones para cambio de turno
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 06-02-2019
     * 
     * Se lee de mejor manera la respuesta para saber si se procesaron todas las reasignaciones de cambio de turno
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 10-01-2019
     * 
     * Graba numero de tarea en la asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-07-2018
     * @since 1.0
     */
    function grabarCambioTurno()
    {
            var arrAsignacionesCambioTurno = ordenaArrayCambioTurno(agentesEditadosCambioTurno);
            if(arrAsignacionesCambioTurno.length > 0)
            {    
                var parametros = {
                    "strAsignaciones" : arrAsignacionesCambioTurno.toString()
                };
                $.ajax({
                        data :  parametros,
                        url  :  url_graba_cambio_turno,
                        type :  'post',
                        beforeSend: function () {
                                $('#btnLoadingGrabarCambioTurno').show();
                                $('#btnGrabarCambioTurno').hide();
                        },
                        success:  function (response) {
                                if(response.respuesta === "OK")
                                {
                                    document.getElementById("btnBuscarPorFecha").onclick();
                                    reloadCuadroAsignaciones();
                                    arrAsignacionesCambioTurno=[];
                                    agentesEditadosCambioTurno=[];
                                    if(response.procesoCompleto === 'OK')
                                    {
                                        configuraMensajeIngresoConExito('#alertaCambioTurno',
                                                                        '<strong>'+response.detalle+'</strong>',
                                                                        '#btnLoadingGrabarCambioTurno',
                                                                        '#btnGrabarCambioTurno');
                                        $('#btnGrabarCambioTurno').attr('disabled','disabled');
                                        //se cierra ventana luego de 2 segundos
                                        setTimeout(function() {
                                            $('#btnGrabarCambioTurno').removeAttr('disabled');
                                            $('#modalCambioTurno').modal('hide');
                                            $('#alertaCambioTurno').hide();
                                        }, 2000);
                                    }
                                    else if(response.procesoCompleto === 'PARCIAL')
                                    {
                                        configuraMensajeIngresoFallido('#alertaCambioTurno',
                                                                       '<strong>'+response.detalle+'</strong>'+
                                                                       '<br>'+response.detalleNoExiste+
                                                                       '<br>'+response.detalleNoPermit,
                                                                       '#btnLoadingGrabarCambioTurno',
                                                                       '#btnGrabarCambioTurno');
                                        $('#alertaCambioTurno').removeClass('alert-success');
                                        $('#alertaCambioTurno').removeClass('alert-danger');
                                        $('#alertaCambioTurno').addClass('alert-warning');
                                        detalleCambioTurno.ajax.url(url_asignaciones_cambio_turno).load();
                                    }
                                }
                                else
                                {
                                    configuraMensajeIngresoFallido('#alertaCambioTurno',
                                                                   '<strong>'+response.detalle+'</strong>',
                                                                   '#btnLoadingGrabarCambioTurno',
                                                                   '#btnGrabarCambioTurno');
                                }
                        },
                        failure: function(response){
                            configuraMensajeIngresoFallido('#alertaCambioTurno',
                                                            response.detalle,
                                                            '#btnLoadingGrabarCambioTurno',
                                                            '#btnGrabarCambioTurno');
                        }
                });
            }
            else
            {
                configuraMensajeIngresoFallido('#alertaCambioTurno',
                                                "<strong>No se ha reasignado ninguna asignación</strong>",
                                                '#btnLoadingGrabarCambioTurno',
                                                '#btnGrabarCambioTurno');
            }
    }
    /**
     *
     * Graba numero de extensión del usuario
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-10-2018
     * @since 1.0
     */
    function grabarNumeroExtension()
    {
        //VALIDA QUE LOS CAMPOS NO ESTEN VACIOS
        if ( validaEditarNumeroExtenxion() )
        {
            var numeroExtension        = document.getElementById('txtNumeroExtensionEdit').value;
            var personaEmpresaRolId    = document.getElementById('txtIdPersonaEmpresaRolEdit').value;
            var bandActualizaEstadoCon = document.getElementById('txtActualizarEstadoConexionEdit').value;
            var estadoConexion         = document.getElementById('txtEstadoConexionEdit').value;
            var arrayCampos            = ['txtIdPersonaEmpresaRolEdit','txtNumeroExtensionEdit'];
            var parametros = {
                "strExtension"        : numeroExtension,
                "idPersonaEmpresaRol" : personaEmpresaRolId
            };

            if(validaFormatoNumeroExtension('#alertaValidaNumeroExtension','#divTxtNumeroExtension','txtNumeroExtensionEdit'))
            {
                $.ajax({
                        data :  parametros,
                        url  :  url_actualiza_extension,
                        type :  'post',
                        beforeSend: function () {
                                $('#btnLoadingGrabarNumeroExtension').show();
                                $('#btnGrabarNumeroExtension').hide();
                        },
                        success:  function (response) {
                                if(response==="OK")
                                {
                                    if (bandActualizaEstadoCon === "S")
                                    {
                                        actualizaEstadoConexion(estadoConexion);
                                    }
                                    detalleUsuarios.ajax.url(url_cabecera_asignaciones).load();
                                    configuraMensajeIngresoConExito('#alertaValidaNumeroExtension',
                                                                    '<strong>Se Grabaron datos con éxito</strong>',
                                                                    '#btnLoadingGrabarNumeroExtension',
                                                                    '#btnGrabarNumeroExtension');
                                    $('#btnGrabarNumeroExtension').attr('disabled','disabled');
                                    //se cierra ventana luego de 2 segundos
                                    setTimeout(function() {
                                        $('#btnGrabarNumeroExtension').removeAttr('disabled');
                                        $('#modalEditarNumeroExtension').modal('hide');

                                        limpiarCampos('#alertaValidaNumeroExtension',arrayCampos);

                                        obtenerEstadoConexion();
                                    }, 2000);
                                }
                                else
                                {
                                    configuraMensajeIngresoFallido('#alertaValidaNumeroExtension',
                                                                   '<strong>'+response+'</strong>',
                                                                   '#btnLoadingGrabarNumeroExtension',
                                                                   '#btnGrabarNumeroExtension');
                                    limpiarCampos('#alertaValidaNumeroExtension',arrayCampos);
                                }
                        },
                        failure: function(response){
                            configuraMensajeIngresoFallido('#alertaValidaNumeroExtension',
                                                           '<strong>'+response+'</strong>',
                                                           '#btnLoadingGrabarNumeroExtension',
                                                           '#btnGrabarNumeroExtension');
                            limpiarCampos('#alertaValidaNumeroExtension',arrayCampos);
                        }
                });
            }
        }
    }

    /**
     *
     * Graba cambios admin tipo problema
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 11-03-2020
     * @since 1.0
     */
    function actualizarTipoProblema()
    {
        var arrayCampos    = ['txtIdAdminTipoProblema', 'txtDetalleAdminTipoProblema'];
        var url            = url_actualiza_admi_parametro_det;
        var descripcion    = document.getElementById('txtDescripcionAdminTipoProblema').value;
        var idTipoProblema = document.getElementById('txtIdAdminTipoProblema').value;
        var detalle        = document.getElementById('txtDetalleAdminTipoProblema').value;
        var departamento   = document.getElementById('txtDepartamentoAdminTipoProblema').value;

        if (document.getElementById('txtAccionAdminTipoProblema').value === "CREAR")
        {
            url                                                          = url_crea_admi_parametro_det;
            arrayCampos                                                  = ['txtDetalleAdminTipoProblema'];
            descripcion                                                  = 'TIPO DE PROBLEMA MODULO AGENTE';
            idTipoProblema                                               = "";
            departamento                                                 = idDepartamento;
        }
        //VALIDA QUE LOS CAMPOS NO ESTEN VACIOS
        if (validaCampos('#alertaValidaAdminTipoProblema', arrayCampos))
        {

            var parametros = {
                "strValor2"                   : detalle,
                "strValor3"                   : departamento,
                "strDescripcion"              : descripcion,
                "intIdParametroDet"           : idTipoProblema,
                "strActualizaSoloDescripcion" : "NO",
                "intParametroCab"             : idParametroCabTipoProblema
            };

            $.ajax({
                    data :  parametros,
                    url  :  url,
                    type :  'post',
                    beforeSend: function () {
                            $('#btnLoadingGrabarAdminTipoProblema').show();
                            $('#btnGrabarAdminTipoProblema').hide();
                    },
                    success:  function (response) {
                            if(response.strStatus==="100")
                            {
                                detalleTiposProblema.ajax.url(url_tipos_problema).load();
                                configuraMensajeIngresoConExito('#alertaValidaAdminTipoProblema',
                                                                '<strong>Se Grabaron datos con éxito</strong>',
                                                                '#btnLoadingGrabarAdminTipoProblema',
                                                                '#btnGrabarAdminTipoProblema');
                                $('#btnGrabarAdminTipoProblema').attr('disabled','disabled');
                                //se cierra ventana luego de 2 segundos
                                setTimeout(function() {
                                    $('#btnGrabarAdminTipoProblema').removeAttr('disabled');
                                    $('#modalAdminTipoProblema').modal('hide');
                                    $('#alertaValidaAdminTipoProblema').hide();
                                    limpiarCampos('#alertaValidaAdminTipoProblema',arrayCampos);
                                }, 2000);
                            }
                            else
                            {
                                configuraMensajeIngresoFallido('#alertaValidaAdminTipoProblema',
                                                                '<strong>'+response.strMessageStatus+'</strong>',
                                                                '#btnLoadingGrabarAdminTipoProblema',
                                                                '#btnGrabarAdminTipoProblema');
                                document.getElementById('txtDetalleAdminTipoProblema').value      = "";
                            }
                    },
                    failure: function(response){
                        configuraMensajeIngresoFallido('#alertaValidaAdminTipoProblema',
                                                        '<strong>'+response.strMessageStatus+'</strong>',
                                                        '#btnLoadingGrabarAdminTipoProblema',
                                                        '#btnGrabarAdminTipoProblema');
                        document.getElementById('txtDetalleAdminTipoProblema').value      = "";
                        
                    }
            });
        }
    }

    /**
     * Actualización: Si la actualización es exitosa entonces ejecuta reload al listado de usuarios 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 06-11-2018
     *
     *Actualiza el estado de conexión del usuario
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 24-10-2018
     * @since 1.0
     */
    function actualizaEstadoConexion(estadoConexion)
    {
        var extension = $("#btnEditarExtension").html();
        extension     = extension.substring(50,60).trim();
        if (estadoConexion === "Disponible")
        {
            extension = document.getElementById("txtNumeroExtensionEdit").value;
        }
        var parametros = {
            "strEstadoConexion" : estadoConexion,
            "strExtension"      : extension                
        };
        $.ajax({
                data :  parametros,
                url  :  url_actualiza_estado_conexion,
                type :  'post',
                success:  function (response) {
                        if(response!=="OK")
                        {
                            $('#modalAlertaError').modal('show');
                            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                             "<strong>"+"Ocurrio un error, No se pudo cambiar su estado de conexión"+"</strong>");
                        }
                        else
                        {
                            detalleUsuarios.ajax.url(url_cabecera_asignaciones).load();
                        }
                },
                failure: function(response){
                    $('#modalAlertaError').modal('show');
                    $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                     "<strong>"+"Ocurrio un error, No se pudo cambiar su estado de conexión"+"</strong>");
                }
        });
    }
    /**
     * Envia reporte de asignaciones pendientes por correo.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 04-02-2019
     * @since 1.0
     */
    function enviarReportePendientes()
    {
        var parametros = {
        };
        $.ajax({
                data :  parametros,
                url  :  url_envia_reporte_pendientes,
                type :  'post',
                beforeSend: function () {
                        $('#btnLoadingEnviarReporte').show();
                        $('#btnConfirmaEnviarReporte').hide();
                },
                success:  function (response) {
                        if(response!=="OK")
                        {
                            configuraMensajeIngresoFallido('#alertaConfirmaReporte',
                                                           '<strong>Ocurrio un error, No se pudo enviar reporte!</strong>',
                                                           '#btnLoadingEnviarReporte',
                                                           '#btnConfirmaEnviarReporte');
                            //se cierra ventana luego de 2 segundos
                            setTimeout(function() {
                                $('#btnConfirmaEnviarReporte').removeAttr('disabled');
                                $('#modalConfirmarEnviarReporte').modal('hide');
                                $('#alertaConfirmaReporte').hide();
                            }, 2000);
                        }
                        else
                        {
                            configuraMensajeIngresoConExito('#alertaConfirmaReporte',
                                                            '<strong>Se envió reporte con éxito!</strong>',
                                                            '#btnLoadingEnviarReporte',
                                                            '#btnConfirmaEnviarReporte');
                            //se cierra ventana luego de 2 segundos
                            setTimeout(function() {
                                $('#btnConfirmaEnviarReporte').removeAttr('disabled');
                                $('#modalConfirmarEnviarReporte').modal('hide');
                                $('#alertaConfirmaReporte').hide();
                            }, 2000);
                        }
                },
                failure: function(response){
                    configuraMensajeIngresoFallido('#alertaConfirmaReporte',
                                                   '<strong>Ocurrio un error, No se pudo enviar reporte!</strong>',
                                                   '#btnLoadingEnviarReporte',
                                                   '#btnConfirmaEnviarReporte');
                    //se cierra ventana luego de 2 segundos
                    setTimeout(function() {
                        $('#btnConfirmaEnviarReporte').removeAttr('disabled');
                        $('#modalConfirmarEnviarReporte').modal('hide');
                        $('#alertaConfirmaReporte').hide();
                    }, 2000);
                }
        });
    }

    /**
     * Consulta el estado de conexión del usuario
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 24-10-2018
     * @since 1.0
     */
    function obtenerEstadoConexion()
    {
            var parametros = {
                "strTipoConsulta" : "estadoConexion"
            };
        $.ajax({
                data : parametros,
                url  :  url_obtener_estado_conexion,
                type :  'post',
                success:  function (response) {
                        if(response.data.mensajeError === "")
                        {
                            cambiarEstadoConexion(response.data.estadoConexion);
                            document.getElementById('txtIdPersonaEmpresaRolEdit').value=response.data.idPersonaEmpresaRol;
                            document.getElementById('txtNumeroExtensionEdit').value=response.data.extensionUsuario;
                            $("#btnEditarExtension").html("<span class='glyphicon glyphicon-earphone'></span> "+response.data.extensionUsuario);
                        }
                        else
                        {
                            $('#modalAlertaError').modal('show');
                            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                             "<strong>"+response.data.mensajeError+"</strong>");
                        }
                },
                failure: function(response){
                            $('#modalAlertaError').modal('show');
                            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                             "<strong>"+"Ocurrio un error, No se pudo obtener estado de conexión"+"</strong>");
                }
        });
    }

    obtenerEstadoConexion();

    function obtenerEstadoConParaNuevaAsignacion(idBtnGuardar)
    {
        var idTxtAgente = "txtAgente";
        if(idBtnGuardar == "btnGrabarAsignacionLote"){
            idTxtAgente = "txtAgenteLote";
        }
        var parametros = {
            "strAgente" : document.getElementById(idTxtAgente).value
        };
        $.ajax({
                data : parametros,
                url  :  url_obtener_estado_conexion,
                type :  'post',
                success:  function (response) {
                        if ((response.data.mensajeError === "") && 
                           (document.getElementById(idTxtAgente).value !== "") &&
                           (response.data.estadoConexion === "Almuerzo" ) || (response.data.estadoConexion === "Ocupado" ))
                        {
                            $('#modalAlertaError').modal('show');
                            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                            "<strong> El agente "+document.getElementById(idTxtAgente).value+
                                                            " se encuentra en estado "+response.data.estadoConexion+
                                                            " y no puede ser asignado</strong>");

                            document.getElementById(idTxtAgente).value = "";
                            document.getElementById(idBtnGuardar).disabled = false;
                        }
                        else if (response.data.mensajeError === "")
                        {
                            obtenerUltimoAsignado(idTxtAgente, idBtnGuardar);
                        }
                        else
                        {
                            $('#modalAlertaError').modal('show');
                            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                            "<strong>"+response.data.mensajeError+"</strong>");
                        }
                },
                failure: function(response){
                            $('#modalAlertaError').modal('show');
                            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                            "<strong>"+"Ocurrio un error, No se pudo obtener estado de conexión"+"</strong>");
                }
        });

    }

    /**
     * Actualización: Se añaden validaciones para asignación por lotes de tareas
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.1 14-07-2021
     *
     * Consulta el último agente asignado
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 21-02-2019
     * @since 1.0
     */
    function obtenerUltimoAsignado(idTxtAgente, idBtnGuardar)
    {
        var idBtnLoading = "btnLoadingGrabarAsig";
        if(idBtnGuardar == "btnGrabarAsignacionLote"){
            idBtnLoading = "btnLoadingGrabarAsigLote";
        }
            var parametros = {
                "strTipoConsulta" : "estadoConexion"
            };
            var agenteSeleccionado = document.getElementById(idTxtAgente).value;
        $.ajax({
                data :  parametros,
                url  :  url_obtener_ultimo_asignado,
                type :  'post',
                success:  function (response) {
                    $(idBtnLoading).show();
                    $(idBtnGuardar).hide();
                    if (response !== agenteSeleccionado)
                    {
                        grabarNuevaAsignacion();
                    }
                    else
                    {
                        mostrarConfirmacionGrabarAsignacion("El agente "+agenteSeleccionado+" fue el último asignado, desea volver asignarlo?");
                    }
                },
                failure: function(response){
                    mostrarConfirmacionGrabarAsignacion("Ocurrio un problema al validar el último usuario asignado, desea grabar la asignación?");
                }
        });
    }


/**
  * Se encarga de obtener información de origenes con asignaciones
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 06-03-2019
 * @since 1.1
 * 
 * Se encarga de obtener información de origenes con asignaciones
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 07-02-2019
 * @since 1.0
 */
function obtienePendientesDepartamento()
{
    var parametros = {
    };
    $.ajax({
        url : url_total_estado_asignaciones,
        type: 'post',
        data: parametros,
        success:  function (response)
        {
            var totalizado         = response.data;
            var totalAbiertos      = 0;
            var totalPendientes    = 0;
            var totalEnGestion     = 0;
            var totalCerrados      = 0;
            var totalCerradosHoy   = 0;
            var totalCerradosMes   = 0;
            var totalAbiertosHoy   = 0;
            var totalPendientesHoy = 0;
            var totalEnGestionHoy  = 0;
            for(var i=0;i<totalizado.length;i++)
            {
                var objTotal = totalizado[i];
                
                
                if (objTotal.name === "Pendiente")
                {
                    totalAbiertos   = totalAbiertos + objTotal.y;
                    totalPendientes = totalPendientes + objTotal.y;
                }
                else if (objTotal.name === "PendienteHoy")
                {
                    totalAbiertosHoy   = totalAbiertosHoy + objTotal.y;
                    totalPendientesHoy = totalPendientesHoy + objTotal.y;
                }
                else if (objTotal.name === "Cerrado")
                {
                    totalCerrados = totalCerrados + objTotal.y;
                }
                else if (objTotal.name === "CerradoHoy")
                {
                    totalCerradosHoy = totalCerradosHoy + objTotal.y;
                }
                else if (objTotal.name === "CerradoMes")
                {
                    totalCerradosMes = totalCerradosMes + objTotal.y;
                }
                else if (objTotal.name === "EnGestion")
                {
                    totalAbiertos  = totalAbiertos + objTotal.y;
                    totalEnGestion = totalEnGestion + objTotal.y;
                }
                else if (objTotal.name === "EnGestionHoy")
                {
                    totalAbiertosHoy  = totalAbiertosHoy + objTotal.y;
                    totalEnGestionHoy = totalEnGestionHoy + objTotal.y;
                }
            }

            $("#divTotalPendientes").html("Total Abiertas:&nbsp;"+
                                          '<span class="hint--bottom-right hint--default hint--medium'+
                                          ' hint--rounded" aria-label="'+
                                          'En gestión - - - '+autoCompletarCaracterAlIzquierda(totalEnGestion,5,'- ')+
                                          ' Pendientes - - '+autoCompletarCaracterAlIzquierda(totalPendientes,5,'- ')+'">'+
                                          "    <span class='badge' style='background-color:#ef5350'>"+totalAbiertos+"</span>"+
                                          '</span>'    
                                         );
            $("#divTotalPendientesHoy").html("Abiertas hoy:&nbsp;"+
                                          '<span class="hint--bottom-right hint--default hint--medium'+
                                          ' hint--rounded" aria-label="'+
                                          'En gestión - - - '+autoCompletarCaracterAlIzquierda(totalEnGestionHoy,5,'- ')+
                                          ' Pendientes - - '+autoCompletarCaracterAlIzquierda(totalPendientesHoy,5,'- ')+'">'+
                                          "    <span class='badge' style='background-color:#439889'>"+totalAbiertosHoy+"</span>"+
                                          '</span>'    
                                         );
            $("#divTotalCerrados").html("Cerradas hoy:&nbsp;"+
                                          '<span class="hint--bottom-right hint--default hint--medium'+
                                          ' hint--rounded" aria-label="'+
                                          ' Cerradas mes '+autoCompletarCaracterAlIzquierda(totalCerradosMes,6,'- ')+
                                          ' Cerradas hoy '+autoCompletarCaracterAlIzquierda(totalCerradosHoy,6,'- ')+'">'+
                                          "    <span class='badge' style='background-color:#7986cb'>"+totalCerradosHoy+"</span>"+
                                          '</span>'    
                                         );
        },
        failure: function(response)
        {
            $('#modalAlertaError').modal('show');
            $("#divMensajeAlertaError").html("<img src=\"/public/images/images_crud/error.png\" width=\"50\" height=\"50\" />"+
                                                     "<strong>"+
                                                         "Ocurrio un error, "+
                                                         "No se pudo leer información de asignaciones Abiertas y Cerradas"+
                                                     "</strong>");
        }
    });
}

    /**
     * Actualización: Se modifica la consulta para considerar los campos 
     *                de fecha al realizar una nueva consulta.
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.2 18-06-2019
     * 
     * Actualización: Se agrega número de tarea si el combo de tareas de asignación de seguimientos esta lleno
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 07-03-2019
     * 
     * Graba asignacion de seguimiento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 03-08-2018
     * @since 1.0
     */
    function grabarAsignarSeguimiento()
    {
        //VALIDA QUE LOS CAMPOS NO ESTEN VACIOS
        if ( validaAsignarSeguimiento() )
        {
            var idAsignacion = document.getElementById('txtIdAsignacionAsigSeg').value;
            var agente       = document.getElementById('txtAgenteAsigSeg').value;
            var detalleTarea = "";
            if (document.getElementById('cmbTareaAsigSeg').value !== "")
            {
                detalleTarea = " para la tarea " + document.getElementById('cmbTareaAsigSeg').value + " ";
            }
            var detalle      = "Se asigna seguimiento a "+agente+detalleTarea+" con el siguiente detalle: "+
                               document.getElementById('txtDetalleAsigSeg').value;
            var parametros = {
                "intId"                     : idAsignacion,
                "strUsrGestion"             : agente,
                "strDetalle"                : detalle
            };
            $.ajax({
                    data :  parametros,
                    url  :  url_graba_asignar_seguimiento,
                    type :  'post',
                    beforeSend: function () {
                        $('#btnLoadingGrabarAsignacionSeguimiento').show();
                        $('#btnGrabarAsignacionSeguimiento').hide();
                    },
                    success:  function (response) {
                            if(response==="OK")
                            {                                
                                configuraMensajeIngresoConExito('#alertaValidaAsignarSeguimiento',
                                                                '<strong>Se Grabaron datos con éxito</strong>',
                                                                '#btnLoadingGrabarAsignacionSeguimiento',
                                                                '#btnGrabarAsignacionSeguimiento');
                                $('#btnGrabarAsignacionSeguimiento').attr('disabled','disabled');
                                document.getElementById("btnBuscarPorFecha").onclick();
                                //se cierra ventana luego de 2 segundos
                                setTimeout(function() {
                                    $('#btnGrabarAsignacionSeguimiento').removeAttr('disabled');
                                    $('#modalAsignarSeguimiento').modal('hide');
                                    var arrayCampos = ['txtIdAsignacionAsigSeg','txtAgenteAsigSeg','txtDetalleAsigSeg'];
                                    lmpiarCampos('alertaValidaAsignarSeguimiento',arrayCampos);
                                }, 2000);
                            }
                            else
                            {
                                configuraMensajeIngresoFallido('#alertaValidaAsignarSeguimiento',
                                                               '<strong>'+response+'</strong>',
                                                               '#btnLoadingGrabarAsignacionSeguimiento',
                                                               '#btnGrabarAsignacionSeguimiento');
                            }
                    },
                    failure: function(response){
                            configuraMensajeIngresoFallido('#alertaValidaAsignarSeguimiento',
                                                           '<strong>'+response+'</strong>',
                                                           '#btnLoadingGrabarAsignacionSeguimiento',
                                                           '#btnGrabarAsignacionSeguimiento');
                    }
            });
        }
        else
        {
            configuraMensajeIngresoFallido('#alertaValidaAsignarSeguimiento',
                                           '<strong>Faltan datos por ingresar</strong>',
                                           '#btnLoadingGrabarAsignacionSeguimiento',
                                           '#btnGrabarAsignacionSeguimiento');
        }
    }

    /**
     * Actualización: Se modifica la consulta para considerar los campos 
     *                de fecha al realizar una nueva consulta.
     * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
     * @version 1.1 18-06-2019 
     * 
     * Graba respuesta de asignacion de seguimiento
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 03-08-2018
     * @since 1.0
     */
    function grabarRespuestaAsignarSeguimiento()
    {        
        //VALIDA QUE LOS CAMPOS NO ESTEN VACIOS
        if (document.getElementById('txtDetalleRespuestaAsigSeg').value != '' )
        {
            var idAsignacion          = document.getElementById('txtIdRespuestaAsignacionAsigSeg').value;
            var idSeguimientoAsignado = document.getElementById('txtIdSeguimientoRespuestaAsigSeg').value;
            var detalle               = document.getElementById('txtDetalleRespuestaAsigSeg').value;            
            var parametros = {
                "intId"      : idAsignacion,
                "intIdSeg"   : idSeguimientoAsignado,
                "strDetalle" : detalle
            };
            $.ajax({
                    data :  parametros,
                    url  :  url_graba_resp_asignar_seg,
                    type :  'post',
                    beforeSend: function () {
                        $('#btnLoadingGrabarRespuestaAsigSeg').show();
                        $('#btnGrabarRespuestaAsigSeg').hide();
                    },
                    success:  function (response) {
                            if(response==="OK")
                            {
                                configuraMensajeIngresoConExito('#alertaValidaRespuestaAsignarSeguimiento',
                                                                '<strong>Se Grabaron datos con éxito</strong>',
                                                                '#btnLoadingGrabarRespuestaAsigSeg',
                                                                '#btnGrabarRespuestaAsigSeg');
                                $('#btnGrabarRespuestaAsigSeg').attr('disabled','disabled');
                                document.getElementById("btnBuscarPorFecha").onclick();
                                //se cierra ventana luego de 2 segundos
                                setTimeout(function() {
                                    $('#btnGrabarRespuestaAsigSeg').removeAttr('disabled');
                                    $('#modalRespuestaAsignarSeguimiento').modal('hide');
                                    var arrayCampos = ['txtIdRespuestaAsigSeg','txtDetalleRespuestaAsigSeg'];
                                    limpiarCampos('#alertaValidaRespuestaAsignarSeguimiento',arrayCampos);
                                }, 2000);

                            }
                            else
                            {
                                configuraMensajeIngresoFallido('#alertaValidaRespuestaAsignarSeguimiento',
                                                               '<strong>'+response+'</strong>',
                                                               '#btnLoadingGrabarRespuestaAsigSeg',
                                                               '#btnGrabarRespuestaAsigSeg');
                            }
                    },
                    failure: function(response){
                                configuraMensajeIngresoFallido('#alertaValidaRespuestaAsignarSeguimiento',
                                                               '<strong>'+response+'</strong>',
                                                               '#btnLoadingGrabarRespuestaAsigSeg',
                                                               '#btnGrabarRespuestaAsigSeg');
                    }
            });
        }
        else
        {
            $("#alertaValidaRespuestaAsignarSeguimiento").html("<strong> Faltan datos por ingresar </strong>");
            $('#alertaValidaRespuestaAsignarSeguimiento').show();
            $('#alertaValidaRespuestaAsignarSeguimiento').fadeIn();
            $('#alertaValidaRespuestaAsignarSeguimiento').slideDown();
            $('#divtxtDetalleRespAsigSeg').addClass('has-error');
        }
    }

    /**
     * Graba el Cambio de estado a standby a una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 18-05-2020
     * @since 1.0
     */
    function grabarCambiarAStandby(tipo)
    {
        var idAsignacion  = document.getElementById('txtIdAsignacionPonerStandby').value;
        var fechaCambT    = document.getElementById('txtFechaPonerStandby').value;
        var horaCambT     = document.getElementById('txtIdBtnHorasCambioTurno').value;
        var detalle       = document.getElementById('txtDetallePonerStandby').value;
        var tipoHistorial = tipo;
        //VALIDA QUE LOS CAMPOS NO ESTEN VACIOS
        if (detalle != '' && fechaCambT != '' && horaCambT != '')
        {
            $('#divtxtDetallePonerStandby').removeClass('has-error');
            $('#divtxtFechaPonerStandby').removeClass('has-error');
            $('#divColumnHorasCambioTurno').removeClass('has-error');

            var parametros = {
                "intId"        : idAsignacion,
                "strFeCambT"   : fechaCambT+" "+horaCambT,
                "strObs"       : detalle,
                "strTipoHist"  : tipoHistorial
            };
            $.ajax({
                    data :  parametros,
                    url  :  url_poner_quitar_asig_standby,
                    type :  'post',
                    beforeSend: function () {
                        $('#btnLoadingGrabarPonerStandby').show();
                        $('#btnGrabarPonerStandby').hide();
                    },
                    success:  function (response) {
                            if(response==="OK")
                            {
                                configuraMensajeIngresoConExito('#alertaValidaPonerStandby',
                                                                '<strong>Se Grabaron datos con éxito</strong>',
                                                                '#btnLoadingGrabarPonerStandby',
                                                                '#btnGrabarPonerStandby');
                                $('#btnGrabarPonerStandby').attr('disabled','disabled');

                                //se cierra ventana luego de 2 segundos
                                setTimeout(function() {
                                    $('#btnGrabarPonerStandby').removeAttr('disabled');
                                    $('#modalPonerStandby').modal('hide');
                                    var arrayCampos = ['txtIdAsignacionPonerStandby','txtDetallePonerStandby'];
                                    limpiarCampos('#alertaValidaPonerStandby',arrayCampos);
                                }, 2000);
                                document.getElementById("btnBuscarPorFecha").onclick();

                            }
                            else
                            {
                                $('#btnLoadingGrabarPonerStandby').hide();
                                $('#btnGrabarPonerStandby').show();
                                configuraMensajeIngresoFallido('#alertaValidaPonerStandby',
                                                               '<strong>'+response+'</strong>',
                                                               '#btnLoadingGrabarPonerStandby',
                                                               '#btnGrabarPonerStandby');
                            }
                    },
                    failure: function(response){
                        $('#btnLoadingGrabarPonerStandby').hide();
                        $('#btnGrabarPonerStandby').show();
                        configuraMensajeIngresoFallido('#alertaValidaPonerStandby',
                                                        '<strong>'+response+'</strong>',
                                                        '#btnLoadingGrabarPonerStandby',
                                                        '#btnGrabarPonerStandby');
                    }
            });
            
        }
        else
        {
            $("#alertaValidaPonerStandby").html("<strong> Faltan datos por ingresar </strong>");
            $('#alertaValidaPonerStandby').show();
            $('#alertaValidaPonerStandby').fadeIn();
            $('#alertaValidaPonerStandby').slideDown();
            (detalle === '')?$('#divtxtDetallePonerStandby').addClass('has-error'):$('#divtxtDetallePonerStandby').removeClass('has-error');
            (fechaCambT === '')?$('#divtxtFechaPonerStandby').addClass('has-error'):$('#divtxtFechaPonerStandby').removeClass('has-error');
            (horaCambT === '')?$('#divColumnHorasCambioTurno').addClass('has-error'):$('#divColumnHorasCambioTurno').removeClass('has-error');
            
        }
    }

    /**
     * Graba el Cambio de estado a standby a una asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 18-05-2020
     * @since 1.0
     */
    function grabarQuitarStandby()
    {
        var idAsignacion  = document.getElementById('txtIdAsignacionQuitarStandby').value;
        var agente        = document.getElementById('txtAgenteQuitarStandby').value;
        var detalle       = document.getElementById('txtDetalleQuitarStandby').value;
        var tipoHistorial = "REASIGNACION";
        //VALIDA QUE LOS CAMPOS NO ESTEN VACIOS
        if (detalle != '' && agente != '' )
        {
            $('#divtxtDetalleQuitarStandby').removeClass('has-error');
            $('#divtxtAgenteQuitarStandby').removeClass('has-error');

            var parametros = {
                "intId"        : idAsignacion,
                "strAgente"    : agente,
                "strObs"       : detalle,
                "strTipoHist"  : tipoHistorial
            };
            $.ajax({
                    data :  parametros,
                    url  :  url_poner_quitar_asig_standby,
                    type :  'post',
                    beforeSend: function () {
                        $('#btnLoadingGrabarQuitarStandby').show();
                        $('#btnGrabarQuitarStandby').hide();
                    },
                    success:  function (response) {
                            if(response==="OK")
                            {
                                configuraMensajeIngresoConExito('#alertaValidaQuitarStandby',
                                                                '<strong>Se Grabaron datos con éxito</strong>',
                                                                '#btnLoadingGrabarQuitarStandby',
                                                                '#btnGrabarQuitarStandby');
                                $('#btnGrabarQuitarStandby').attr('disabled','disabled');

                                //se cierra ventana luego de 2 segundos
                                setTimeout(function() {
                                    $('#btnGrabarQuitarStandby').removeAttr('disabled');
                                    $('#modalQuitarStandby').modal('hide');
                                    var arrayCampos = ['txtIdAsignacionQuitarStandby','txtDetalleQuitarStandby'];
                                    limpiarCampos('#alertaValidaQuitarStandby',arrayCampos);
                                }, 2000);
                                document.getElementById("btnBuscarPorFecha").onclick();
                            }
                            else
                            {
                                configuraMensajeIngresoFallido('#alertaValidaQuitarStandby',
                                                               '<strong>'+response+'</strong>',
                                                               '#btnLoadingGrabarQuitarStandby',
                                                               '#btnGrabarQuitarStandby');
                            }
                    },
                    failure: function(response){
                                configuraMensajeIngresoFallido('#alertaValidaQuitarStandby',
                                                               '<strong>'+response+'</strong>',
                                                               '#btnLoadingGrabarQuitarStandby',
                                                               '#btnGrabarQuitarStandby');
                    }
            });
        }
        else
        {
            $("#alertaValidaQuitarStandby").html("<strong> Faltan datos por ingresar </strong>");
            $('#alertaValidaQuitarStandby').show();
            $('#alertaValidaQuitarStandby').fadeIn();
            $('#alertaValidaQuitarStandby').slideDown();
            (detalle === '')?$('#divtxtDetalleQuitarStandby').addClass('has-error'):$('#divtxtDetalleQuitarStandby').removeClass('has-error');
            (agente === '')?$('#divtxtAgenteQuitarStandby').addClass('has-error'):$('#divtxtAgenteQuitarStandby').removeClass('has-error');
        }
    }

    /**
     * Limpia campos enviados por parámetro
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 12-03-2020
     * @since 1.0
     */
    function limpiarCampos(divAlerta,arrayCampos)
    {
        $(divAlerta).hide();
        for(var i = 0; i < arrayCampos.length; i++)
        {
            if(document.getElementById(arrayCampos[i]))
            {
                document.getElementById(arrayCampos[i]).value = "";
            }
        }
    }

    //Configura la acción de grabar nueva asignacion en el boton btnGrabarAsignacion
    document.getElementById("btnNuevaAsignacion").onclick = function () 
    {   
        buscaTiposProblema("cmbTipoProblema","");
    };

    /**
     * Actualización: Se añade la funcionalidad para resetear lista de tareas checkeadas al realizar una asiganción masiva, con el objetivo que permita realizar la asignación individual
     * @author Fernando Lopez <filopez@telconet.ec>
     * @version 1.0 29-10-2021
     * @since 1.0
     */

    //Configura la acción de grabar nueva asignacion en el boton btnGrabarAsignacion
    document.getElementById("btnGrabarAsignacion").onclick = function () 
    {   
        if($("#checkTodoTareas").is(":checked")){
            $("#checkTodoTareas").prop( "checked", false );
        }
        var dataTable = $('#detalleTareas').dataTable();
        $(dataTable.fnGetNodes()).each(function(){
            var celda = $(this).find("td:eq(0)");
            var nodo = celda[0].childNodes;
            nodo[0].checked = false;
            $(this).removeClass("claseCheck");
        });
        $('#btnAsignarMasivo').prop("disabled",true);
        document.getElementById('tareasSeleccionadas').value = '';
        document.getElementById("btnGrabarAsignacion").disabled = true;
        obtenerEstadoConParaNuevaAsignacion("btnGrabarAsignacion");
    };

    document.getElementById("btnGrabarAsignacionLote").onclick = function () 
    { 
        document.getElementById("btnGrabarAsignacionLote").disabled = true;
        obtenerEstadoConParaNuevaAsignacion("btnGrabarAsignacionLote");
    };

    document.getElementById("btnGrabarEjecucionTarea").onclick = function () 
    {
        document.getElementById("btnGrabarEjecucionTarea").disabled = true;
        var nombreTarea = $('#txtTarea').text();
        var detalleTarea = $('#txtDetalleTarea').val();
        var tiempoTarea = $('#txtTiempo').val();
        var personaEmpresaRol = $('#txtPersonaEmpresaRol').val();
        var numeroTarea = $('#txtNumeroTarea').val();
        var nombreProceso = $('#txtNombreProceso').val();
        var usrAsignado = $('#txtUsrAsignado').val();
        var deptAsignado = $('#txtDeptAsignado').val();
        var detalleHist = $('#txtDetalleHist').val();
        var origen = $('#txtOrigen').val();
        var nombreUsrAsignado = $('#txtNombreUsrAsignado').val();

        aceptarRechazarTarea(origen, detalleTarea, nombreTarea, tiempoTarea, personaEmpresaRol, numeroTarea, nombreProceso, usrAsignado, deptAsignado, detalleHist, nombreUsrAsignado);
    };

    document.getElementById("btnConfirmaTareaEjecucion").onclick = function () 
    {
        document.getElementById("btnGrabarEjecucionTarea").disabled = true;
        var nombreTarea = $('#txtTarea').text();
        var detalleTarea = $('#txtDetalleTarea').val();
        var tiempoTarea = $('#txtTiempo').val();
        var personaEmpresaRol = $('#txtPersonaEmpresaRol').val();
        var numeroTarea = $('#txtNumeroTarea').val();
        var nombreProceso = $('#txtNombreProceso').val();
        var usrAsignado = $('#txtUsrAsignado').val();
        var deptAsignado = $('#txtDeptAsignado').val();
        var detalleHist = $('#txtDetalleHist').val();
        var origen = $('#txtOrigen').val();
        var nombreUsrAsignado = $('#txtNombreUsrAsignado').val();

        gestionarTareas(origen, nombreTarea, tiempoTarea, detalleTarea, personaEmpresaRol, numeroTarea, nombreProceso, usrAsignado, deptAsignado, detalleHist, nombreUsrAsignado);
    }

    document.getElementById("btnIngresoSeguimiento").onclick = function ()
    {
        document.getElementById("btnGrabarEjecucionTarea").disabled = true;
        var detalleTarea = $('#txtDetalleTareaSeg').val();
        var seguimientoTarea = $('#txtSeguimientoTarea').val();
        var registroInterno = $('#cmbRegistroInterno').val();

        ingresarSeguimiento(detalleTarea, seguimientoTarea, registroInterno)
    }

    //Configura la acción de grabar numero de tarea en el boton btnGrabarNumeroTarea
    document.getElementById("btnGrabarNumeroTareaCaso").onclick = function () { grabarNumeroTarea(); };

    //Configuración de las posibles configuraciones que se pueden dar para realizar la busqueda
    document.getElementById("btnBuscarPorFecha").onclick = function () {
        var fechaInicio = document.getElementById('txtDatetimepickerBuscaAsignacion').value;
        var fechaFin    = document.getElementById('txtDatetimepickerBuscaAsignacionFin').value;
        var idCanton    = document.getElementById('txtIdBtnCantones').value;
        var estado      = document.getElementById('txtIdBtnEstados').value;
        var reload      = false;
        var parametros  = "";
        if ( (fechaInicio != "") && (fechaFin != "") )
        {
            annio    = fechaInicio.substr(6, 4); // Extraemos el año
            mes      = fechaInicio.substr(3, 2); // Extraemos el mes
            dia      = fechaInicio.substr(0, 2); // Extraemos el día
            fechaInicioVal = annio+"-"+mes+"-"+dia;

            annio    = fechaFin.substr(6, 4);
            mes      = fechaFin.substr(3, 2);
            dia      = fechaFin.substr(0, 2);
            fechaFinVal  = annio+"-"+mes+"-"+dia;

            fechaAux = Date.parse(fechaInicioVal);
            fechaInicial = new Date(fechaAux);
            
            fechaAux = Date.parse(fechaFinVal);
            fechaFinal = new Date(fechaAux);

            if( (fechaFinal >= fechaInicial) )
            {
                reload = true;
                parametros = "?fecha="+document.getElementById('txtDatetimepickerBuscaAsignacion').value+
                             "&fechaFin="+document.getElementById('txtDatetimepickerBuscaAsignacionFin').value;
            }
            else
            {
                Ext.Msg.alert("Alerta",'Búsqueda inconsistente.');
            } 
        }
        else if( ( (fechaInicio != "") && (fechaFin == "") ) || ( (fechaInicio == "") && (fechaFin == "") ))
        {
            reload     = true;
            parametros = "?fecha="+document.getElementById('txtDatetimepickerBuscaAsignacion').value;
        }
        else if( (fechaInicio == "") && (fechaFin != "") )
        {
            Ext.Msg.alert("Error de Parámetros","Parámetros incompletos.");
        }
        
        if (idCanton != null && idCanton != "")
        {
            parametros += "&idCanton="+idCanton;
        }
        if (estado != null && estado != "")
        {
            parametros += "&estado="+estado;
        }
        //Si pestaña Incidencias Seg esta activa envia tipo problema
        if ($('#liDetallesIseg').hasClass('active'))
        {
            parametros += "&tabVisible=detallesIncidenciasSeg";
        }

        if (reload == true)
        {
            detalleAsignaciones.ajax.url(url_detalle_asignaciones+parametros).load();
        }
    
    };
    
    document.getElementById("btnLimpiaBuscarPorFecha").onclick = function () 
    { 
        parametros = "";
        estado     = "Abierto"; 
        document.getElementById('txtDatetimepickerBuscaAsignacion').value = "";
        document.getElementById('txtDatetimepickerBuscaAsignacionFin').value = "";

        $("#textBtnCantones").text("TODAS   ");
        $("#textBtnEstados").text(estado.toUpperCase()+"   ");

        document.getElementById('txtIdBtnCantones').value = '';
        document.getElementById('txtIdBtnEstados').value = estado;

        //Si pestaña Incidencias Seg esta activa envia tipo problema
        if ($('#liDetallesIseg').hasClass('active'))
        {
            parametros += "?tabVisible=detallesIncidenciasSeg";
            parametros += "&estado="+estado;
        }
        else
        {
            parametros += "?estado="+estado;
        }
        detalleAsignaciones.ajax.url(url_detalle_asignaciones+parametros).load();
    };

    document.getElementById("btnCambioTurno").onclick = function () { 
        $('#alertaCambioTurno').hide();    
        detalleCambioTurno.ajax.url(url_asignaciones_cambio_turno).load();
    };

    document.getElementById("btnGrabarSeguimientoCambioTurno").onclick = function () {
        var strCambioTurno  = document.getElementById('txtCambioTurno').value;
        var strDetalleSeg   = document.getElementById('txtaDetalleSegCambioTurno').value;
        var intIdAsignacion = document.getElementById('txtIdAsignacionNuevoSegCambioTurno').value;
        marcarCambioTurno(
                          strCambioTurno,
                          intIdAsignacion,
                          strDetalleSeg
                         );
    };

    document.getElementById("btnGrabarCambioTurno").onclick = function () { grabarCambioTurno();};

    //Configuración de las posibles configuraciones que se pueden dar para realizar la busqueda en ventana de cambio de turno
    document.getElementById("btnBuscarCambioTurno").onclick = function () {
        var idCanton    = document.getElementById('txtIdBtnCantonesCambioTurno').value;
        var parametros  = "";
        if (idCanton != null && idCanton != "")
        {
            parametros += "?idCanton="+idCanton;
        }
        detalleCambioTurno.ajax.url(url_asignaciones_cambio_turno+parametros).load();
    };

    document.getElementById("btnLimpiaBuscarCambioTurno").onclick = function () 
    {
        $("#textBtnCantonesCambioTurno").text("TODAS   ");
        $("#txtIdBtnCantonesCambioTurno").text("");
        document.getElementById('txtIdBtnCantonesCambioTurno').value = '';
        detalleCambioTurno.ajax.url(url_asignaciones_cambio_turno).load();
    };

    document.getElementById("btnGrabarAsignacionSeguimiento").onclick = function () { grabarAsignarSeguimiento();};

    document.getElementById("btnGrabarRespuestaAsigSeg").onclick = function () { grabarRespuestaAsignarSeguimiento();};

    document.getElementById("btnGrabarNumeroExtension").onclick = function () { grabarNumeroExtension();};

    document.getElementById("confirmaEliminarAsignacion").onclick = function () {
        eliminarAsignacion();};

    document.getElementById("confirmaCerrarAsignacion").onclick = function () {
        cerrarAsignacion();};
    document.getElementById("cancelaEliminarAsignacion").onclick = function () { 
        document.getElementById('txtIdAsigSegEliminar').value = null;
        $('#modalConfirmarAlertaAsignacion').modal('hide');
    };
    document.getElementById("cancelaCerrarAsignacion").onclick = function () { 
        document.getElementById('txtIdAsigCerrar').value = null;
        $('#modalConfirmarCerrarAsignacion').modal('hide');
    };
    document.getElementById("btnPendientes").onclick = function () { buscaPendientesCombo(); };

    document.getElementById("btnCantones").onclick = function () { buscaCantonesCombo("#ulDropDownCantones",
                                                                                      "#textBtnCantones",
                                                                                      "#txtIdBtnCantones"); 
                                                                 };


    document.getElementById("btnEstados").onclick = function () { buscaEstadosCombo("#ulDropDownEstados",
                                                                                    "#textBtnEstados",
                                                                                    "#txtIdBtnEstados",
                                                                                    "estados"); 
    };

    document.getElementById("btnCantonesCambioTurno").onclick = function () { buscaCantonesCombo("#ulDropDownCantonesCambioTurno",
                                                                                                 "#textBtnCantonesCambioTurno",
                                                                                                 "#txtIdBtnCantonesCambioTurno"); 
                                                                            };

    document.getElementById("btnCantonesCuadroGen").onclick = function () { buscaCantonesCombo("#ulDropDownCantonesCuadroGen",
                                                                                                 "#textBtnCantonesCuadroGen",
                                                                                                 "#txtIdBtnCantonesCuadroGen"); 
                                                                            };
    document.getElementById("btnCantonesEstadisticas").onclick = function () { buscaCantonesCombo("#ulDropDownCantonesEstadisticas",
                                                                                                  "#textBtnCantonesEstadisticas",
                                                                                                  "#txtIdBtnCantonesEstadisticas");
                                                                             };
    document.getElementById("btnEstadosEstadisticas").onclick = function () { buscaEstadosCombo("#ulDropDownEstadosEstadisticas",
                                                                                                  "#textBtnEstadosEstadisticas",
                                                                                                  "#txtIdBtnEstadosEstadisticas",
                                                                                                  "estados");
                                                                            };
    document.getElementById("btnHorasCambioTurno").onclick = function () { buscaEstadosCombo("#ulDropDownHorasCambioTurno",
                                                                                             "#textBtnHorasCambioTurno",
                                                                                             "#txtIdBtnHorasCambioTurno",
                                                                                             "horas");
                                                      };
    //Configura la acción de grabar nueva asignacion en el boton btnGrabarAsignacion
    document.getElementById("btnGrabarSeguimiento").onclick = function () { grabarNuevoSeguimiento(); };

    //Configura la acción de grabar edición de tipo de problema
    document.getElementById("btnGrabarAdminTipoProblema").onclick = function () { actualizarTipoProblema();};

    //Configura la acción para el boton de eliminar seguimiento
    document.getElementById("btnConfirmaEliminarSeguimiento").onclick = function () {
        accion = document.getElementById("txtTipoAccionEliminar").value;
        eliminarRegistro(accion);
    };
    //Configura la acción para el boton de cancelar eliminar seguimiento
    document.getElementById("btnCancelaEliminarSeguimiento").onclick = function () { 
        document.getElementById('txtIdSeguimientoEliminar').value = null;
        $('#modalConfirmarAlertaSeguimiento').modal('hide');
    };
    //Configura la acción para el boton de editar el número de extensión del usuario en sesión
    document.getElementById("btnEditarExtension").onclick = function () {
                obtenerEstadoConexion();
                mostrarEditarNumeroExtension(document.getElementById('txtIdPersonaEmpresaRolEdit').value, 
                                             document.getElementById('txtNumeroExtensionEdit').value,
                                             'N',
                                             ''
                                            );
    };
    //Configura la acción de ordenar cuadro general por estado de conexión
    document.getElementById("btnOrdenAgentesEstadoConex").onclick = function () { 
                ordenAgentesCuadroGen = 'ESTADO_CONEXION';
                reloadCuadroAsignaciones();
    };
    //Configura la acción de ordenar cuadro general por cantidad
    document.getElementById("btnOrdenAgentesCantidad").onclick = function () { 
                ordenAgentesCuadroGen = 'CANTIDAD';
                reloadCuadroAsignaciones();
    };
    //controla acción para mostrar ventana de confirmación para envio de reporte
    document.getElementById("btnEnviarCorreoExcelDetalles").onclick = function () {
            mostrarConfirmacionEnviarReporte();
    }
    //controla acción de confirmación de envio del reporte de asignaciones pendientes
    document.getElementById("btnConfirmaEnviarReporte").onclick = function () 
    {
        enviarReportePendientes();
    };
    //controla acción de cancelación de envio del reporte de asignaciones pendientes
    document.getElementById("btnCancelaEnviarReporte").onclick = function () 
    { 
        $('#modalConfirmarEnviarReporte').modal('hide');
    };

    //controla acción de confirmación de grabar una asignación
    document.getElementById("btnConfirmaGrabarAsignacion").onclick = function () 
    {
        grabarNuevaAsignacion();
    };
    //controla acción de cancelación de grabar una asignación
    document.getElementById("btnCancelaGrabarAsignacion").onclick = function () 
    { 
        $('#modalConfirmarGrabarAsignacion').modal('hide');
        $('#btnLoadingGrabarAsig').hide();
        $('#btnGrabarAsignacion').show();
        document.getElementById("btnGrabarAsignacion").disabled = false;

        var arrayCampos = ['cmbOrigenLote',
                            'cmbTipoAtencionLote',
                            'cmbTipoProblemaLote',
                            'cmbCriticidadLote',
                            'txtAgenteLote',
                            'txtaDetalleLote',
                        ];

        $('#alertaValidaNuevaAsignacionLote').removeClass('alert-danger');
        $('#alertaValidaNuevaAsignacionLote').addClass('alert-success');
        $('#alertaValidaNuevaAsignacionLote').addClass('alert-warning');
        $('#divCmbOrigenLote').removeClass('has-error');
        $('#divCmbCriticidadLote').removeClass('has-error');
        $('#divCmbTipoAtencionLote').removeClass('has-error');
        $('#divCmbTipoProblemaLote').removeClass('has-error');
        $('#divtxtAgenteLote').removeClass('has-error');
        $('#divtxtaDetalleLote').removeClass('has-error');
        $('#btnLoadingGrabarAsigLote').hide();
        $('#btnGrabarAsignacionLote').show();
        $('#btnGrabarAsignacionLote').prop("disabled",false);

        limpiarCampos('#alertaValidaNuevaAsignacionLote',arrayCampos);
    };
    //controla acción de cancelación de grabar una asignación
    document.getElementById("btnCerrarGrabarAsignacion").onclick = function () 
    { 
        $('#modalConfirmarGrabarAsignacion').modal('hide');
        $('#btnLoadingGrabarAsig').hide();
        $('#btnGrabarAsignacion').show();
    };
    document.getElementById("btnGrabarPonerStandby").onclick = function () 
    { 
        grabarCambiarAStandby("STANDBY");
    };
    document.getElementById("btnGrabarQuitarStandby").onclick = function () 
    { 
        grabarQuitarStandby();
    };
    document.getElementById("lnUsuarios").onclick = function () { 
        $('#divListadoUsuarios').show();
        $('#divCuadroAsignaciones').hide();
        $('#divListadoAsignaciones').hide();
        $('#divCuadroAsignacionesUsr').hide();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#divListadoTareas').hide();
        $('#divAdminTiposProblema').hide();
        $('#liUsuarios').addClass('active');
        $('#liCuadro').removeClass('active');
        $('#liDetalles').removeClass('active');
        $('#liGrafico1').removeClass('active');
        $('#liCuadroUsr').removeClass('active');
        $('#liInfoAsignacion').removeClass('active');
        $('#liInfoAsignacion').hide();
        $('#liTareas').removeClass('active');
        $('#liAdmin').addClass('active');
        $('#liAdminTiposProblema').removeClass('active');
        detalleUsuarios.ajax.url(url_cabecera_asignaciones).load();
    };

    document.getElementById("btnAsignacionProactiva").onclick = function (){
        $('#btnAceptarAsigProact_1').hide();
        $('#btnCancelarAsigProact_1').hide();
        $('#btnAceptarAsigProact').show();
        $('#btnCancelarAsigProact').show();
        $('#modalNuevaAsignacion').modal('hide');
        $('#modalAsignacionProactiva').modal('show');
        detalleAsignacionesProactivas.clear().draw();
        detalleAsignacionesProactivas.ajax.url(url_detalle_asignaciones+"?asigProactivas='S'").load();
    };
    
    document.getElementById("btnCancelarAsigProact").onclick = function (){
        arrayAsignacionProactiva=[];
        $('#modalNuevaAsignacion').modal('show');
        $('#modalAsignacionProactiva').modal('hide');
    };
    
    document.getElementById("btnCancelarAsigProact_1").onclick = function (){
        arrayAsignacionProactiva=[];
        $('#modalAsignacionProactiva').modal('hide');
        $('#modalAsignacionHija').modal('hide');
        detalleAsignacionesProactivas.clear().draw();
        
        $(document).ready(function() {
            $('#limpiar').click(function() {
                $('.form-control form-control-sm').val('');
            });
        });
    };
    
    document.getElementById("btnCerrarVistaHijas").onclick = function (){
        $('#modalAsignacionHija').modal('hide');
        detalleAsignacionesHijas.clear().draw();
    };
    
    document.getElementById("btnAceptarAsigProact").onclick = function (){ 
        Ext.Msg.confirm('Confirmación',
                            'Se relacionará '+arrayAsignacionProactiva.length+' Asignación(es).',function(btn){
                                if (btn == 'yes')
                                {
                                    $('#modalNuevaAsignacion').modal('show');
                                    $('#modalAsignacionProactiva').modal('hide');
                                }
                            });
    };
    
    
    document.getElementById("btnAceptarAsigProact_1").onclick = function (){ 
        Ext.Msg.confirm('Confirmación',
                            'Se relacionará '+arrayAsignacionProactiva.length+' Asignación(es).',function(btn){
                                if (btn == 'yes')
                                {
                                    relacionAsiganacionHijaPadre();
                                    $('#modalAsignacionProactiva').modal('hide');
                                }
                            });
    };
 
    document.getElementById("btnLimpiaBuscarCuadroGen").onclick = function () 
    {
        $("#textBtnCantonesCuadroGen").text("TODAS   ");
        $("#txtIdBtnCantonesCuadroGen").text("");
        document.getElementById('txtIdBtnCantonesCuadroGen').value = '';
        reloadCuadroAsignaciones();
    };

    document.getElementById("btnBuscarCuadroGen").onclick = function () {
        reloadCuadroAsignaciones();
    };

    document.getElementById("btnNuevoTipoProblema").onclick = function () {
        var arrayCampos = ['txtIdAdminTipoProblema', 'txtDetalleAdminTipoProblema'];
        limpiarCampos('#alertaValidaAdminTipoProblema',arrayCampos);
        document.getElementById('txtAccionAdminTipoProblema').value = 'CREAR';
    };

    document.getElementById("lnCuadro").onclick = function () { 
        $('#divCuadroAsignaciones').show();
        $('#divListadoUsuarios').hide();
        $('#divListadoAsignaciones').hide();
        $('#divCuadroAsignacionesUsr').hide();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#divListadoTareas').hide();
        $('#divAdmin').hide();
        $('#divAdminTiposProblema').hide();
        $('#liCuadro').addClass('active');
        $('#liUsuarios').removeClass('active');
        $('#liDetalles').removeClass('active');
        $('#liGrafico1').removeClass('active');
        $('#liCuadroUsr').removeClass('active');
        $('#liInfoAsignacion').removeClass('active');
        $('#liInfoAsignacion').hide();
        $('#liTareas').removeClass('active');
        $('#liAdmin').removeClass('active');
        $('#liAdminTiposProblema').removeClass('active');
        $('#liDetallesIseg').removeClass('active');
        reloadCuadroAsignaciones();
    };

    document.getElementById("lnCuadroUsr").onclick = function () { 
        $('#divCuadroAsignacionesUsr').show();
        $('#divListadoUsuarios').hide();
        $('#divCuadroAsignaciones').hide();
        $('#divListadoAsignaciones').hide();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#divListadoTareas').hide();
        $('#divAdmin').hide();
        $('#divAdminTiposProblema').hide();
        $('#liUsuarios').removeClass('active');
        $('#liCuadroUsr').addClass('active');
        $('#liDetalles').removeClass('active');
        $('#liCuadro').removeClass('active');
        $('#liGrafico1').removeClass('active');
        $('#liInfoAsignacion').removeClass('active');
        $('#liInfoAsignacion').hide();
        $('#liTareas').removeClass('active');
        $('#liAdmin').removeClass('active');
        $('#liAdminTiposProblema').removeClass('active');
        $('#liDetallesIseg').removeClass('active');
        cuadroAsignacionesUsr.ajax.url(url_asignaciones_usuario).load();
    };

    document.getElementById("lnDetalles").onclick = function () { 
        $('#divListadoUsuarios').hide();
        $('#divCuadroAsignaciones').hide();
        $('#divCuadroAsignacionesUsr').hide();
        $('#divListadoAsignaciones').show();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#divListadoTareas').hide();
        $('#divAdmin').hide();
        $('#divAdminTiposProblema').hide();
        $('#liUsuarios').removeClass('active');
        $('#liCuadro').removeClass('active');
        $('#liCuadroUsr').removeClass('active');
        $('#liGrafico1').removeClass('active');
        $('#liInfoAsignacion').removeClass('active');
        $('#liInfoAsignacion').hide();
        $('#liDetalles').addClass('active');
        $('#liTareas').removeClass('active');
        $('#liAdmin').removeClass('active');
        $('#liAdminTiposProblema').removeClass('active');
        $('#liDetallesIseg').removeClass('active');
        //document.getElementById("btnBuscarPorFecha").onclick();
        document.getElementById("btnLimpiaBuscarPorFecha").onclick();
    };

    document.getElementById("lnDetallesIseg").onclick = function () { 
        $('#divListadoUsuarios').hide();
        $('#divCuadroAsignaciones').hide();
        $('#divCuadroAsignacionesUsr').hide();
        $('#divListadoAsignaciones').show();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#divListadoTareas').hide();
        $('#divAdmin').hide();
        $('#divAdminTiposProblema').hide();
        $('#liInfoAsignacion').hide();
        $('#liUsuarios').removeClass('active');
        $('#liCuadro').removeClass('active');
        $('#liCuadroUsr').removeClass('active');
        $('#liGrafico1').removeClass('active');
        $('#liInfoAsignacion').removeClass('active');
        $('#liDetalles').removeClass('active');
        $('#liTareas').removeClass('active');
        $('#liAdmin').removeClass('active');
        $('#liAdminTiposProblema').removeClass('active');
        $('#liDetallesIseg').addClass('active');
        document.getElementById("btnLimpiaBuscarPorFecha").onclick();
    };

    document.getElementById("lnGrafico1").onclick = function () { 
        $('#divListadoUsuarios').hide();
        $('#divCuadroAsignaciones').hide();
        $('#divCuadroAsignacionesUsr').hide();
        $('#divListadoAsignaciones').hide();
        $('#divInfoAsignacion').hide();
        $('#divListadoTareas').hide();
        $('#divAdmin').hide();
        $('#divAdminTiposProblema').hide();
        $('#divCharts').show();
        $('#liUsuarios').removeClass('active');
        $('#liCuadro').removeClass('active');
        $('#liCuadroUsr').removeClass('active');
        $('#liGrafico1').addClass('active');
        $('#liInfoAsignacion').removeClass('active');
        $('#liInfoAsignacion').hide();
        $('#liDetalles').removeClass('active');
        $('#liTareas').removeClass('active');
        $('#liAdmin').removeClass('active');
        $('#liAdminTiposProblema').removeClass('active');
        $('#liDetallesIseg').removeClass('active');

        $("#textBtnCantonesEstadisticas").text("TODAS   ");
        $("#textBtnEstadosEstadisticas").text("TODOS   ");
        document.getElementById('txtIdBtnCantonesEstadisticas').value = '';
        document.getElementById('txtIdBtnEstadosEstadisticas').value = 'todos';

        seleccionaFechaGrafica($('#reportrange').data('daterangepicker').startDate.format('YYYY/MM/DD'), 
                               $('#reportrange').data('daterangepicker').endDate.format('YYYY/MM/DD'));
    };

    document.getElementById("lnTareas").onclick = function () { 
        $('#divListadoUsuarios').hide();
        $('#divCuadroAsignaciones').hide();
        $('#divCuadroAsignacionesUsr').hide();
        $('#divListadoAsignaciones').hide();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#liInfoAsignacion').hide();
        $('#divAdmin').hide();
        $('#divAdminTiposProblema').hide();
        $('#divListadoTareas').show();
        $('#liUsuarios').removeClass('active');
        $('#liCuadro').removeClass('active');
        $('#liCuadroUsr').removeClass('active');
        $('#liGrafico1').removeClass('active');
        $('#liInfoAsignacion').removeClass('active');
        $('#liDetalles').removeClass('active');
        $('#liTareas').addClass('active');
        $('#liAdmin').removeClass('active');
        $('#liAdminTiposProblema').removeClass('active');
        $('#liDetallesIseg').removeClass('active');
        $('#btnAsignarMasivo').prop("disabled",true);
        $('#btnBuscarPorFechaTarea').prop("disabled",true);
        detalleTareas.ajax.url(url_tareas_grid).load();
    };

    document.getElementById("lnAdmin").onclick = function () { 
        $('#divListadoUsuarios').hide();
        $('#divCuadroAsignaciones').hide();
        $('#divCuadroAsignacionesUsr').hide();
        $('#divListadoAsignaciones').hide();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#liInfoAsignacion').hide();
        $('#divListadoTareas').hide();
        $('#divAdminTiposProblema').hide();
        $('#divAdmin').show();
        $('#liUsuarios').removeClass('active');
        $('#liCuadro').removeClass('active');
        $('#liCuadroUsr').removeClass('active');
        $('#liGrafico1').removeClass('active');
        $('#liInfoAsignacion').removeClass('active');
        $('#liDetalles').removeClass('active');
        $('#liTareas').removeClass('active');
        $('#liAdmin').addClass('active');
        $('#liAdminTiposProblema').removeClass('active');
        $('#liDetallesIseg').removeClass('active');
    };

    document.getElementById("lnAdminTiposProblema").onclick = function () { 
        $('#divListadoUsuarios').hide();
        $('#divCuadroAsignaciones').hide();
        $('#divCuadroAsignacionesUsr').hide();
        $('#divListadoAsignaciones').hide();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#liInfoAsignacion').hide();
        $('#divListadoTareas').hide();
        $('#divAdminTiposProblema').show();
        $('#liUsuarios').removeClass('active');
        $('#liCuadro').removeClass('active');
        $('#liCuadroUsr').removeClass('active');
        $('#liGrafico1').removeClass('active');
        $('#liInfoAsignacion').removeClass('active');
        $('#liDetalles').removeClass('active');
        $('#liTareas').removeClass('active');
        $('#liAdmin').addClass('active');
        $('#liAdminTiposProblema').addClass('active');
        $('#liDetallesIseg').removeClass('active');
        detalleTiposProblema.ajax.url(url_tipos_problema).load();
    };

    $("#txtNumeroTareaCaso").on("keyup", function() {
      if (document.getElementById('txtNumeroTareaCaso').value != '' )
      {
          $('#divTxtNumeroTareaCaso').removeClass('has-error');
      }
      else
          $('#divTxtNumeroTareaCaso').addClass('has-error');
    });
  
    $("#cmbTipoProblemaEdit").on("change", function() {
      if (document.getElementById('cmbTipoProblemaEdit').value != '' )
      {
          $('#divCmbTipoProblemaEdit').removeClass('has-error');
      }
      else
          $('#divCmbTipoProblemaEdit').addClass('has-error');
    });
  
    $("#cmbTipoAtencionEdit").on("change", function() {
      if (document.getElementById('cmbTipoAtencionEdit').value != '' )
      {
          $('#divCmbTipoAtencionEdit').removeClass('has-error');
      }
      else
          $('#divCmbTipoAtencionEdit').addClass('has-error');
    });
      
    $("#cmbMesHistorialConexion").on("change", function() {
        var mes   = document.getElementById("cmbMesHistorialConexion").value;
        var anio  = document.getElementById("cmbAnioHistorialConexion").value;
        var idPer = document.getElementById("txtIdPerHistConexion").value;
        detalleHistorialConexion.ajax.url(url_historial_conexion_usr+"?idPer="+idPer+"&mes="+mes+"&anio="+anio).load();
    });


    /**
     * Actualización: Se realiza actualización para validar el evento estado ausente 
     *               para ejecutar el cambio de turno automático.
     * @author Miguel Angulo Sanchez <jmangulos@telconet.ec>
     * @version 1.1 31-05-2019
     */
    
    document.getElementById("ulDropDownEstadoUsr").onclick = function(event) {
        var e            = event || window.event;
        var target       = e.target || e.srcElement;
        var cadenaBasura = target.innerHTML.split('<span').pop().split('span>')[0];
        var estadoUsr    = target.innerHTML.replace(cadenaBasura,"").replace("<spanspan>","").trim();

        if(estadoUsr === 'Ausente')
        {
            $.ajax({
                url  :  url_total_asignaciones_sin_numero,
                type :  'GET', 
                success:  function (response) {
                    if (response[0].total > 0)
                    {
                        $("#divMensajeAlertaError").
                        html("<img src='/public/images/images_crud/error.png' width='50' height='50' />"+
                             "<strong>"+
                             " No se puede cambiar estado a Ausente porque tiene asignaciones sin número de tarea o "+
                             " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; caso asociado."+
                             "</strong>");
                        $('#modalAlertaError').modal('show');
                    }
                    else
                    {
                        Ext.Msg.confirm('Alerta cambio turno automático',
                        'Se realizará el cambio automático por Cambio de Turno. <br/> ¿Está seguro que desea continuar?',function(btn)
                        {
                            if (btn == 'yes')
                            {
                                cambioTurnoAutomatico();
                                cambiarEstadoConexion(estadoUsr);
                                detalleAsignaciones.ajax.url(url_detalle_asignaciones).load();
                                actualizaEstadoConexion(estadoUsr);
                            }
                        });
                    }
                },
                failure: function(response){                        
                        Ext.Msg.alert('Error al consultar asignaciones sin números asociados');
                }
        });

        }
        else if(estadoUsr === 'Disponible')
        {
            mostrarEditarNumeroExtension
            (
                                         document.getElementById('txtIdPersonaEmpresaRolEdit').value, 
                                         "",
                                         "S",
                                         estadoUsr
            );
        }
        else
        {
            cambiarEstadoConexion(estadoUsr);
            actualizaEstadoConexion(estadoUsr);
        }
        

    };

    /**
     * Realiza el cambio automatico de cambio de turno
     * @author Miguel Angulo Sanchez <jmangulos@telconet.ec>
     * @version 1.0 30-05-2019
     */

    function cambioTurnoAutomatico()
    {
        $.ajax({
                url  :  url_cambio_turno_automatico,
                type :  'POST', 
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                     Ext.Msg.alert('Cambio de Turno',response);
                },
                failure: function(response){                        
                        Ext.Msg.alert(response);
                }
        });
    }

    /**
     * Realiza el cambio de color al combo de estado de conexión
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 24-10-2018
     * @since 1.0
     */
    function cambiarEstadoConexion(estadoUsr)
    {
        $("#btnEstadoUsr").attr('data-value',estadoUsr);
        if(estadoUsr === 'Disponible')
        {
            $("#btnEstadoUsr").attr('style','color:white;border-color:green!important; background-color: green!important;');
        }
        else if(estadoUsr === 'Ocupado')
        {
            $("#btnEstadoUsr").attr('style','color:white;border-color:orange!important; background-color: orange!important;');
        }
        else if(estadoUsr === 'Ausente')
        {
            $("#btnEstadoUsr").attr('style','color:white;border-color:brown!important; background-color:brown!important;');
        }
        else if(estadoUsr === 'Almuerzo')
        {
            $("#btnEstadoUsr").attr('style','color:white;border-color:#01579b!important; background-color:#01579b!important;');
        }
    }

    //Verificamos que tipo de vista es para habilitar permisos
    if ($('#vistaSoporte').text().trim() === 'Vista Administrador' ||
        $('#vistaSoporte').text().trim() === 'Vista Coordinador' ||
        $('#vistaSoporte').text().trim() === 'Vista Jefe'
       )
    {

        $('#divCuadroAsignaciones').show();
        $('#divCuadroAsignacionesUsr').hide();
        $('#divListadoAsignaciones').hide();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#divListadoTareas').hide();
        $('#liCuadro').addClass('active');
        $('#liCuadroUsr').removeClass('active');
        $('#liCuadroUsr').hide();
        $('#liDetalles').removeClass('active');
        $('#liGrafico1').removeClass('active');
        $('#liTareas').removeClass('active');
        if (!permiteVerOpcionTareas)
        {
            $('#liTareas').hide();
        }
        $('#liAdmin').removeClass('active');
        if (!permiteVerOpcionAdministrador)
        {
            $('#liAdmin').hide();
        }
        $('#liInfoAsignacion').removeClass('active');
        $('#liInfoAsignacion').hide();
        $('#btnNuevaAsignacion').show();
        $('#btnCambioTurno').show();
        $('#btnEnviarCorreoExcelDetalles').show();
        $('#divBlancoBotonesDetalles').hide();
    }
    else if ($('#vistaSoporte').text().trim() === 'Vista Agente')
    {
        $('#divCuadroAsignaciones').hide();
        $('#divCuadroAsignacionesUsr').show();
        $('#divListadoAsignaciones').hide();
        $('#divInfoAsignacion').hide();
        $('#divCharts').hide();
        $('#divListadoTareas').hide();
        $('#liCuadro').removeClass('active');
        $('#liCuadro').hide();
        $('#liCuadroUsr').addClass('active');
        $('#liDetalles').removeClass('active');
        $('#liGrafico1').hide();
        $('#liTareas').removeClass('active');
        $('#liTareas').hide();
        $('#liAdmin').hide();
        $('#liInfoAsignacion').removeClass('active');
        $('#liInfoAsignacion').hide();
        $('#btnNuevaAsignacion').hide();
        $('#btnCambioTurno').hide();
        $('#btnEnviarCorreoExcelDetalles').hide();
        $('#divBlancoBotonesDetalles').show();
    }

    /**
     * Graba numero de tarea en la asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-07-2018
     * @since 1.0
     */
    function buscaPendientesCombo()
    {
        
            var parametros = {
                "todos" : ($('#vistaSoporte').text().trim() === 'Vista Agente')?"N":"S",
            };
        $.ajax({
                url  :  url_asignaciones_pendientes,
                type :  'post',
                data : parametros, 
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                    var asignaciones = response.data;
                    var dropDown     ='<input class="form-control" id="txtBuscaPendiente" name="txtBuscaPendiente" '+
                                      'type="text" placeholder="Buscar.." onkeyup="onkeyupBuscaPendiente()" style="display:none">';
                     var itemDropDown ="";
                    for(var i=0;i<asignaciones.length;i++)
                    {
                        var numero = "N/A";
                        if (asignaciones[i].casoTarea!=="" && asignaciones[i].casoTarea!==null)
                        {
                            numero = asignaciones[i].casoTarea;
                        }
                        var login  = asignaciones[i].referenciaCliente;
                        var imagen = "";
                        if (asignaciones[i].loginAfectado)
                        {
                            login = asignaciones[i].loginAfectado;
                        }
                        if(asignaciones[i].estado === "Pendiente")
                        {
                            imagen = "<img src=\"/public/images/asignacionPendiente.png\" width=\"15\" height=\"15\" />";
                        }
                        else if(asignaciones[i].estado === "EnGestion" && asignaciones[i].tipoAtencion === "CASO")
                        {
                            imagen = "<img src=\"/public/images/asignacionCaso.png\" width=\"15\" height=\"15\"/>";
                        }
                        else if(asignaciones[i].estado === "EnGestion" && asignaciones[i].tipoAtencion === "TAREA")
                        {
                            imagen = "<img src=\"/public/images/asignacionTarea.png\" width=\"15\" height=\"15\"/>";
                        }
                        
                        itemDropDown+= '<li><a href="javascript:obtenerInformacionAsignacion(\''+asignaciones[i].id+'\');">'+
                                                 imagen +" "+ asignaciones[i].tipoAtencion+': '+numero+
                                                 ' Cliente:'+login+
                                       '</a></li>';
                    }
                    dropDown = dropDown + itemDropDown + '';
                    $("#ulDropDown").html(dropDown);
                    $("#badgeNotificacionesPendientes").html(i);

                },
                failure: function(response){
                        console.log("error al notificar pendientes de asignaciones");
                },
                complete: function(){
                    buscaPendientesUsr();
                }
        });
    }

    /**
     * Graba numero de tarea en la asignación
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 23-07-2018
     * @since 1.0
     */
    function buscaPendientes()
    {
            var parametros = {
            //    "sinCerrar" : "S"
            };
        $.ajax({
                url  :  url_asignaciones_pendientes,
                type :  'post',
                data : parametros,
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                    var asignaciones                 = response.data;
                    var asignacionesPendientesUsr    = [];
                    var asignacionesTxt              = "";
                    for(var i=0;i<asignaciones.length;i++)
                    {
                        if (asignaciones[i].estado === "Pendiente")
                        {
                            asignacionesPendientesUsr.push(asignaciones[i].referenciaCliente);
                        }
                    }
                    asignacionesTxt = asignacionesPendientesUsr.join(", ");
                    var bodyPush    = "";
                    if(asignacionesTxt!="")
                    {
                        //Envia notificacion para asignaciones pendientes
                        bodyPush = "Usted tiene ("+asignacionesPendientesUsr.length+") asignaciones pendientes: "+asignacionesTxt;
                        Push.create('Notificación de asignaciones',{
                            body: bodyPush,
                            icon: url_icon_notificaciones_asig,
                            timeout: 7000,
                            onShow: function(){
                                //Ejecuta el sonido para notificación por 10 segundos
                                alarmaAsignacion.stop();
                                var id1 = alarmaAsignacion.play();
                                alarmaAsignacion.fade(1, 0, 7000, id1);                                
                            },
                            onClick: function () {
                                window.focus();
                                this.close();
                                alarmaAsignacion.stop();
                            },
                            onClose: function (){
                                alarmaAsignacion.stop();
                            }

                        });
                    }
                    $("#badgeNotificacionesPendientes").html(i);
                },
                failure: function(response){
                        console.log("error al notificar pendientes de asignaciones");
                },
                complete: function(){
                    buscaPendientesUsr();
                }
        });
    }
    
    /**
     * Busca los seguimientos de asignaciones pendientes por usuario
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 17-08-2018
     * @since 1.0
     */
    function buscaPendientesUsr()
    {
        $.ajax({
                url  :  url_seguimientos_pendientes_usr,
                type :  'post',
                success:  function (response) {
                    var seguimientos              = response.data;
                    var seguimientosPendientesUsr = [];
                    var seguimientosTxt           = "";
                    for(var i=0;i<seguimientos.length;i++)
                    {
                        seguimientosPendientesUsr.push(seguimientos[i].tipoAtencion+'('+seguimientos[i].numero+')');
                    }
                    seguimientosTxt = seguimientosPendientesUsr.join(", ");
                    var bodyPush    = "";
                    if(seguimientosTxt!="")
                    {
                        bodyPush = "Usted tiene ("+seguimientosPendientesUsr.length+") seguimientos asignados pendientes: "+seguimientosTxt;
                        Push.create('Notificación de asignación de Seguimiento',{
                            body: bodyPush,
                            icon: url_icon_notificaciones_seg,
                            timeout: 7000,
                            onShow: function(){
                                //Ejecuta el sonido para notificación por 10 segundos
                                alarmaSeguimiento.stop();
                                var id1 = alarmaSeguimiento.play();
                                alarmaSeguimiento.fade(1, 0, 7000, id1);                                
                            },
                            onClick: function () {
                                window.focus();
                                this.close();
                                alarmaSeguimiento.stop();
                            },
                            onClose: function (){
                                alarmaSeguimiento.stop();
                            }
                        });

                    }
                },
                failure: function(response){
                        console.log("failure");
                }
        });
    }

    $('#checkTodoTareas').change(function() {
        $('.select-checkbox-table').not(this).prop('checked', this.checked);
        var dataTable = $('#detalleTareas').dataTable();
        var dataTableApi = $('#detalleTareas').dataTable().api();
        var registrosFiltrados = dataTableApi.rows({"search" : "applied"});
        var intContador = 0;
        if($(this).is(":checked")) {
            $(dataTable.fnGetNodes()).each(function(){
                if(registrosFiltrados[0].includes(intContador)){
                var celda = $(this).find("td:eq(0)");
                var nodo = celda[0].childNodes;
                nodo[0].checked = true;
                $(this).addClass("claseCheck");
                }
                intContador++;
            });
            $('#btnAsignarMasivo').prop("disabled",false);
        } else {
            $(dataTable.fnGetNodes()).each(function(){
                var celda = $(this).find("td:eq(0)");
                var nodo = celda[0].childNodes;
                nodo[0].checked = false;
                $(this).removeClass("claseCheck");
            });
            $('#btnAsignarMasivo').prop("disabled",true);
        }
    });

    $('#btnCerrarAsignacionLote').click(function() {
        var arrayCampos = ['cmbOrigenLote',
                            'cmbTipoAtencionLote',
                            'cmbTipoProblemaLote',
                            'cmbCriticidadLote',
                            'txtAgenteLote',
                            'txtaDetalleLote',
                        ];

        $('#alertaValidaNuevaAsignacionLote').removeClass('alert-danger');
        $('#alertaValidaNuevaAsignacionLote').addClass('alert-success');
        $('#alertaValidaNuevaAsignacionLote').addClass('alert-warning');
        $('#divCmbOrigenLote').removeClass('has-error');
        $('#divCmbCriticidadLote').removeClass('has-error');
        $('#divCmbTipoAtencionLote').removeClass('has-error');
        $('#divCmbTipoProblemaLote').removeClass('has-error');
        $('#divtxtAgenteLote').removeClass('has-error');
        $('#divtxtaDetalleLote').removeClass('has-error');
        $('#btnLoadingGrabarAsigLote').hide();
        $('#btnGrabarAsignacionLote').show();
        $('#btnGrabarAsignacionLote').prop("disabled",false);

        limpiarCampos('#alertaValidaNuevaAsignacionLote',arrayCampos);
    });

    $('#btnCerrarEjecucionTarea').click(function() {
        var arrayCampos = ['txtDetalleTarea',
                            'txtTiempo',
                            'txtPersonaEmpresaRol',
                            'txtNumeroTarea',
                            'txtNombreProceso',
                            'txtUsrAsignado',
                            'txtDeptAsignado',
                            'txtDetalleHist',
                            'txtOrigen',
                            'txtNombreUsrAsignado',
                            'txtObservacionTarea',
                            'cmbMotivoPausa'
                        ];

        $('#btnGrabarEjecucionTarea').prop("disabled",false);
        $('#btnConfirmaTareaEjecucion').prop("disabled",false);
        limpiarCampos('#alertaValidaNuevaAsignacionLote',arrayCampos);
    });

    $('#btnCerrarConfirmaTareaEjecucion').click(function() {
        var arrayCampos = ['txtDetalleTarea',
                            'txtTiempo',
                            'txtPersonaEmpresaRol',
                            'txtNumeroTarea',
                            'txtNombreProceso',
                            'txtUsrAsignado',
                            'txtDeptAsignado',
                            'txtDetalleHist',
                            'txtOrigen',
                        ];

        $('#btnGrabarEjecucionTarea').prop("disabled",false);
        $('#btnConfirmaTareaEjecucion').prop("disabled",false);
        limpiarCampos('#alertaValidaNuevaAsignacionLote',arrayCampos);
    });

    $('#btnCerrarMensajeTarea').click(function() {

        var arrayCampos = ['txtDetalleTarea',
                            'txtTiempo',
                            'txtPersonaEmpresaRol',
                            'txtNumeroTarea',
                            'txtNombreProceso',
                            'txtUsrAsignado',
                            'txtDeptAsignado',
                            'txtDetalleHist',
                            'txtOrigen',
                            'txtNombreUsrAsignado',
                            'txtObservacionTarea',
                            'cmbMotivoPausa',
                            "txtDetalleTareaSeg",
                            "txtTiempoSeg",
                            "txtPersonaEmpresaRolSeg",
                            "txtNumeroTareaSeg",
                            "txtNombreProcesoSeg",
                            "txtUsrAsignadoSeg",
                            "txtDeptAsignadoSeg",
                            "txtDetalleHistSeg",
                            "txtOrigenSeg",
                            "txtNombreUsrAsignadoSeg",
                            "cmbRegistroInterno",
                            'txtSeguimientoTarea'
                        ];

        $('#btnGrabarEjecucionTarea').prop("disabled",false);
        $('#btnConfirmaTareaEjecucion').prop("disabled",false);
        $('#btnIngresoSeguimiento').prop("disabled",false);
        $('#alertaMensajeTareasDiv').css('display', 'none');
        $('#alertaMensajeTareas').text("");
        $('#successMensajeTareasDiv').css('display', 'none');
        $('#successMensajeTareas').text("");
        limpiarCampos('#alertaValidaNuevaAsignacionLote',arrayCampos);
    });

    $('#btnCerrarIngresoSeguimiento').click(function() {

        var arrayCampos = ['cmbRegistroInterno',
                            'txtSeguimientoTarea',
                            "txtDetalleTareaSeg",
                            "txtTiempoSeg",
                            "txtPersonaEmpresaRolSeg",
                            "txtNumeroTareaSeg",
                            "txtNombreProcesoSeg",
                            "txtUsrAsignadoSeg",
                            "txtDeptAsignadoSeg",
                            "txtDetalleHistSeg",
                            "txtOrigenSeg",
                            "txtNombreUsrAsignadoSeg"
                        ];


        $('#btnIngresoSeguimiento').prop("disabled",false);
        $('#alertaMensajeTareasDiv').css('display', 'none');
        $('#alertaMensajeTareas').text("");
        $('#successMensajeTareasDiv').css('display', 'none');
        $('#successMensajeTareas').text("");

        limpiarCampos('#alertaValidaNuevaAsignacionLote',arrayCampos);
    });

    var estadoFechaInicio = false;
    var estadoFechaFin = false;
    $('#datetimepickerBuscaAsignacionTarea').on('dp.change', function(e){
        estadoFechaInicio = true;
        if(estadoFechaInicio && estadoFechaFin)
        {
            $('#btnBuscarPorFechaTarea').prop("disabled",false);
        }
    });

    $('#datetimepickerBuscaAsignacionFinTarea').on('dp.change', function(e){
        estadoFechaFin = true;
        if(estadoFechaInicio && estadoFechaFin)
        {
            $('#btnBuscarPorFechaTarea').prop("disabled",false);
        }
    });

    $("#btnBuscarPorFechaTarea").addClass();

    document.getElementById("btnBuscarPorFechaTarea").onclick = function () {
        var fechaInicio = document.getElementById('txtDatetimepickerBuscaAsignacionTarea').value;
        var fechaFin    = document.getElementById('txtDatetimepickerBuscaAsignacionFinTarea').value;
        var reload      = false;
        var parametros  = "";
        if ( (fechaInicio != "") && (fechaFin != "") )
        {
            annio    = fechaInicio.substr(6, 4);
            mes      = fechaInicio.substr(3, 2);
            dia      = fechaInicio.substr(0, 2);
            fechaInicioVal = annio+"-"+mes+"-"+dia;

            annio    = fechaFin.substr(6, 4);
            mes      = fechaFin.substr(3, 2);
            dia      = fechaFin.substr(0, 2);
            fechaFinVal  = annio+"-"+mes+"-"+dia;

            fechaAux = Date.parse(fechaInicioVal);
            fechaInicial = new Date(fechaAux);
            
            fechaAux = Date.parse(fechaFinVal);
            fechaFinal = new Date(fechaAux);

            if( (fechaFinal >= fechaInicial) )
            {
                reload = true;
                parametros = "?fecha="+document.getElementById('txtDatetimepickerBuscaAsignacionTarea').value+
                                "&fechaFin="+document.getElementById('txtDatetimepickerBuscaAsignacionFinTarea').value;
            }
            else
            {
                Ext.Msg.alert("Alerta",'Búsqueda inconsistente.');
            } 
        }
        else if( ( (fechaInicio != "") && (fechaFin == "") ) || ( (fechaInicio == "") && (fechaFin == "") ))
        {
            reload     = true;
            parametros = "?fecha="+document.getElementById('txtDatetimepickerBuscaAsignacionTarea').value;
        }
        else if( (fechaInicio == "") && (fechaFin != "") )
        {
            Ext.Msg.alert("Error de Parámetros","Parámetros incompletos.");
        }

        if (reload == true)
        {
            detalleTareas.ajax.url(url_tareas_grid+parametros).load();
        }
    };


    document.getElementById("btnLimpiaBuscarPorFechaTarea").onclick = function () 
    {
        document.getElementById('txtDatetimepickerBuscaAsignacionTarea').value = "";
        document.getElementById('txtDatetimepickerBuscaAsignacionFinTarea').value = "";

        $('#btnBuscarPorFechaTarea').prop("disabled",true);
        estadoFechaInicio = false;
        estadoFechaFin = false;

        detalleTareas.ajax.url(url_tareas_grid).load();
    };



} );


/**
 * Agregar reasignado para cambio de turno
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
function agregarReasignadoCambioTurno(idAsignacion)
{
    var asignacion = {id:idAsignacion, usuario:document.getElementById('txtAgenteCambioTurno'+idAsignacion).value};
    agentesEditadosCambioTurno.push(asignacion);
}

/**
 * Ordenar arreglo de cambio de turno
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
function ordenaArrayCambioTurno(arr)
{
    var id="";
    var usr="";
    var arrResult=[];
    for(var i=0;i<arr.length;i++)
    {   
        var empleado = arr[i];

        if (id != empleado.id)
        {
            id = empleado.id;
            usr = empleado.usuario;
	    arrResult.push(id+"|"+usr);
        }
    }
    return arrResult;
}

/**
 * Ordenar arreglo en forma dinamica
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}

/**
 * buscar clientes con metodo keyup en la busqueda de clientes pendientes
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
function onkeyupBuscaPendiente() {
  $("#txtBuscaPendiente").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".dropdown-menu li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
}
/**
 * 
 * Muestra error en el formulario de grabar asignación de seguimiento
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 07-03-2019
 * @since 1.0
 */
function mostrarErrorAsignarSeguimiento(){
    $('#alertaValidaAsignarSeguimiento').html("<strong>Faltan datos por ingresar</strong>");
    $('#alertaValidaAsignarSeguimiento').show();
    $('#alertaValidaAsignarSeguimiento').fadeIn();
    $('#alertaValidaAsignarSeguimiento').slideDown();
    $('#divtxtAgenteAsigSeg').addClass('has-error');
    $('#divtxtDetalleAsigSeg').addClass('has-error');
    if ($('#divCmbTareaAsigSeg').css('display') !== 'none' )
    {
        $('#divCmbTareaAsigSeg').addClass('has-error');    
    }
}

/**
 * Actualización: Se recibe por parametro los campos de:
 * tipoAtencion, divAlerta, divTxtNumero y txtNumeroTareaCaso
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 03-10-2018
 *
 * Valida el formato del numero de caso o de tarea
 * @param tipoAtencion       => tipo de atención
 * @param divAlerta          => div donde se muestran las alertas de la ventana
 * @param divTxtNumero       => div donde esta el número de tarea o caso
 * @param txtNumeroTareaCaso => campo donde se ingresó el número de tarea o caso
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
function validaFormatoNumeroCasoTarea(tipoAtencion,divAlerta,divTxtNumero,txtNumeroTareaCaso) { 
  var m = document.getElementById(txtNumeroTareaCaso).value;
  if (tipoAtencion === "CASO")
  {
      var expreg    = /^\d{8}-\d{4}|[A-Z]{1}$/;
  }
  else if (tipoAtencion === "TAREA")
  {
      var expreg    = /^[0-9]+$/;
  }
  var boolRespuesta = false;
  
  if(expreg.test(m))
  {
      $(divTxtNumero).removeClass('has-error');
      $(divTxtNumero).addClass('has-success');
      $(divAlerta).hide();
      boolRespuesta = true;
  }
  else
  {
      $(divAlerta).removeClass('alert-success');
      $(divAlerta).addClass('alert-danger');
      $(divAlerta).html("<strong>El número es incorrecto</strong>");
      $(divAlerta).show();
      $(divAlerta).fadeIn();
      $(divAlerta).slideDown();
      $(divTxtNumero).removeClass('has-success');
      $(divTxtNumero).addClass('has-error');
      boolRespuesta = false;
  }
  return boolRespuesta;
}
/**
 * Valida el formato del numero de extensión
 * @param divAlerta    => div donde se muestran las alertas de la ventana
 * @param divTxtNumero => div donde esta el número de extensión
 * @param txtNumero    => campo donde se ingresó el número de extensión
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 25-10-2018
 * @since 1.0
 */
function validaFormatoNumeroExtension(divAlerta,divTxtNumero,txtNumero) { 
  var m             = document.getElementById(txtNumero).value;
  var expreg        = /^\d{4,7}$/;
  var boolRespuesta = false;
  
  if(expreg.test(m))
  {
      $(divTxtNumero).removeClass('has-error');
      $(divTxtNumero).addClass('has-success');
      $(divAlerta).hide();
      boolRespuesta = true;
  }
  else
  {
      $(divAlerta).removeClass('alert-success');
      $(divAlerta).addClass('alert-danger');
      $(divAlerta).html("<strong>El número es incorrecto</strong>");
      $(divAlerta).show();
      $(divAlerta).fadeIn();
      $(divAlerta).slideDown();
      $(divTxtNumero).removeClass('has-success');
      $(divTxtNumero).addClass('has-error');
      boolRespuesta = false;
  }
  return boolRespuesta;
}

/**
 * Actualización: Se realiza la validacion de que el campo 
 * RUC/LOGIN (txtLogin) sea obligatorio.
 * @author Miguel Angulo Sánchez <jmangulos@telconet.ec>
 * @version 1.4 10-04-2019
 * 
 * Actualización: Se permite agregar el numero de 
 * tarea o caso directamente al momento de crear una asignación para todos los usuarios
 * (Ya no es necesario tener credencial)
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.3 10-01-2019
 * 
 * Actualización: Se valida el número de la tarea o caso solo si esta el campo de número lleno.
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.2 26-09-2018
 *
 * Actualización: Se agrega validación para que tambien pregunte para combo tipo problema por valor 'Seleccione...'
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 26-09-2018
 *
 * Valida ingreso de datos de una nueva asignación
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
function validaNuevaAsignacion()
{   
    var boolRespuesta= false;
        if ( (document.getElementById('cmbOrigen').value != '' ) && 
             (document.getElementById('cmbCriticidad').value != '' ) && 
             (document.getElementById('cmbTipoAtencion').value != '' ) &&
             (document.getElementById('cmbTipoAtencion').value != 'Seleccione...' ) &&
             (document.getElementById('cmbTipoProblema').value != '' ) &&
             (document.getElementById('cmbTipoProblema').value != 'Seleccione...' ) &&
             (document.getElementById('txtAgente').value != '' ) &&
             (document.getElementById('txtaDetalle').value != '' ) &&
             (document.getElementById('txtLogin').value != '' )&&
             (document.getElementById('txtLogin').value != '' )) 
        {
            var tipoAtencion = document.getElementById('cmbTipoAtencion').value;
            //VALIDA QUE EL FORMATO DEL NUMERO DE TAREA O CASO SEA CORRECTO SOLO SI EL CAMPO DE TAREA/CASO ESTA LLENO
            if (document.getElementById('txtNumeroTareaCasoNuevaAsig').value !== "")
            {
                boolRespuesta= validaFormatoNumeroCasoTarea(tipoAtencion,
                                                            '#alertaValidaNuevaAsignacion',
                                                            '#divTxtNumeroTareaCasoNuevaAsig',
                                                            'txtNumeroTareaCasoNuevaAsig');
            }
            else
            {
                boolRespuesta = true;
            }
        }
        else
        {
            $('#alertaValidaNuevaAsignacion').addClass('alert-danger');
            $('#alertaValidaNuevaAsignacion').removeClass('alert-success');
            $('#alertaValidaNuevaAsignacion').removeClass('alert-warning');
            $('#alertaValidaNuevaAsignacion').html("<strong>Faltan datos por ingresar</strong>");
            $('#alertaValidaNuevaAsignacion').show();
            $('#alertaValidaNuevaAsignacion').fadeIn();
            $('#alertaValidaNuevaAsignacion').slideDown();
            $('#divCmbOrigen').addClass('has-error');
            $('#divCmbCriticidad').addClass('has-error');
            $('#divCmbTipoAtencion').addClass('has-error');
            $('#divCmbTipoProblema').addClass('has-error');
            $('#divtxtAgente').addClass('has-error');
            $('#divtxtaDetalle').addClass('has-error');
            $('#btnLoadingGrabarAsig').hide();
            $('#btnGrabarAsignacion').show();
            boolRespuesta= false;
        }
        return boolRespuesta;
}

/**
 * Actualización: Se agrega validación que combo de tareas este lleno si el combo esta visible
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 07-03-2019
 * 
 * Valida el ingreso de asignar seguimientos
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
function validaAsignarSeguimiento()
{
    var boolRespuesta= false;
        if (( 
             $('#divCmbTareaAsigSeg').css('display') === 'none' &&
             document.getElementById('txtAgenteAsigSeg').value != ''  &&
             document.getElementById('txtDetalleAsigSeg').value != ''  ) ||
            ( 
             $('#divCmbTareaAsigSeg').css('display') !== 'none' &&
             document.getElementById('txtAgenteAsigSeg').value != ''  &&
             document.getElementById('txtDetalleAsigSeg').value != '' && 
             document.getElementById('cmbTareaAsigSeg').value != ''
            )
           )
        {
                $('#alertaValidaAsignarSeguimiento').hide();
                boolRespuesta= true;
        }
        else
        {
            mostrarErrorAsignarSeguimiento();
            boolRespuesta= false;
        }
        return boolRespuesta;
}

/**
 * Actualización: Se agrega validación para que tambien pregunte para combo tipo problema por valor 'Seleccione...'
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.1 26-09-2018
 *
 * Valida campos para agregar numero de caso o tarea
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
function validaVaciosAgregaNumero()
{
    var boolRespuesta= false;
        if ( (document.getElementById('txtNumeroTareaCaso').value != '' ) && 
             (document.getElementById('cmbTipoProblemaEdit').value != '' ) &&
             (document.getElementById('cmbTipoProblemaEdit').value != 'Seleccione...' ) &&
             (document.getElementById('cmbTipoAtencionEdit').value != '' )&&
             (document.getElementById('cmbTipoAtencionEdit').value != 'Seleccione...' )
           )
        {
            $('#alertaValidaNumeroTarea').hide();
            boolRespuesta= true;
        }
        else
        {
            $('#alertaValidaNumeroTarea').html("<strong>Faltan datos por ingresar</strong>");
            $('#alertaValidaNumeroTarea').show();
            $('#alertaValidaNumeroTarea').fadeIn();
            $('#alertaValidaNumeroTarea').slideDown();
            $('#divTxtNumeroTareaCaso').addClass('has-error');
            boolRespuesta= false;
        }
        return boolRespuesta;
}

/**
 * Valida el ingreso de asignar seguimientos
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
function validaEditarNumeroExtenxion()
{
    var boolRespuesta= false;
        if ( document.getElementById('txtNumeroExtensionEdit').value != '' )
        {
            $('#alertaValidaNumeroExtension').hide();
            boolRespuesta= true;
        }
        else
        {
            $('#alertaValidaNumeroExtension').html("<strong>Faltan datos por ingresar</strong>");
            $('#alertaValidaNumeroExtension').show();
            $('#alertaValidaNumeroExtension').fadeIn();
            $('#alertaValidaNumeroExtension').slideDown();
            $('#divTxtNumeroExtension').addClass('has-error');
            boolRespuesta= false;
        }
        return boolRespuesta;
}


/**
 * Valida que los campos enviados por parámetro no se encuentren vacios
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 11-03-2020
 * @since 1.0
 */
function validaCampos(divAlerta, arrayCampos)
{
    var boolRespuesta= true;

    for(var i = 0; i < arrayCampos.length; i++)
    {
        if ( document.getElementById(arrayCampos[i]).value === '' )
        {
            boolRespuesta = false;
            break;
        }
    }
    if(!boolRespuesta)
    {
        $(divAlerta).html("<strong>Faltan datos por ingresar</strong>");
        $(divAlerta).show();
        $(divAlerta).fadeIn();
        $(divAlerta).slideDown();
        $(divAlerta).addClass('has-error');
    }
    return boolRespuesta;
}

/**
 * Configura el modal de ingreso exitoso
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
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

/**
 * Configura el modal de ingreso Fallido
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 17-08-2018
 * @since 1.0
 */
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

function getSeguimientos(idAsignacion)
{
    var jsonSeguimiento = {};

    $.ajax({
                data :  {
                    "data"       : idAsignacion                   
                },
                url  :  url_seguimientoAsignacion,
                type :  'POST',
                async: false,

                success:  function (response) {
                    jsonSeguimiento = response.seguimiento[0];                    
                },
                failure: function(response){

                }
        });
        return jsonSeguimiento;
}

    /**
     * Busca los cantones que se encuentran configuradas en las tablas de parametros: INFO_PARAMETRO_DET
     * Estas oficinas representan a las ciudades desde donde se ingresan asignaciones.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 08-01-2020
     * @since 1.0
     */
    function buscaCantonesCombo(ulDropDown,text,textId)
    {
        $.ajax({
                url  :  url_parametros,
                type :  'post',
                data : {'tipo':'cantones'},
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                    var oficinas = response.data;
                    var dropDown     ='<input class="form-control" id="txtBuscaCantones" name="txtBuscaCantones" '+
                                      'type="text" placeholder="Buscar.." onkeyup="" style="display:none">';
                     var itemDropDown ="";
                    for(var i=0; i < oficinas.length; i++)
                    {                        
                        itemDropDown+= "<li>"+
                        "<a href='javascript:obtieneValorCantonCombo(\""+oficinas[i].valor2+"\",\""+
                                                                         oficinas[i].valor3+"\",\""+
                                                                         text+"\",\""+
                                                                         textId+"\");'>"+
                            oficinas[i].valor2+
                        "</a></li>";
                    }
                    dropDown = dropDown + "<li><a href='javascript:obtieneValorCantonCombo(\"TODAS\",\"\",\""+
                                                                                             text+"\",\""+
                                                                                             textId+"\");'>TODAS</a></li>" + itemDropDown + '';
                    $(ulDropDown).html(dropDown);
                },
                failure: function(response){
                }
        });
    }

    function obtieneValorCantonCombo(oficina, idOficina, text, textId){
        $(text).text(oficina+"   ");
        $(textId).val(idOficina);
        if (textId === '#txtIdBtnCantonesEstadisticas')
        {
            seleccionaFechaGrafica($('#reportrange').data('daterangepicker').startDate.format('YYYY/MM/DD'), 
            $('#reportrange').data('daterangepicker').endDate.format('YYYY/MM/DD'));
        }

    }
    /**
     * Busca los cantones que se encuentran configuradas en las tablas de parametros: INFO_PARAMETRO_DET
     * Estas oficinas representan a las ciudades desde donde se ingresan asignaciones.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 08-01-2020
     * @since 1.0
     */
    function buscaEstadosCombo(ulDropDown,text,textId,tipo)
    {
        $.ajax({
                url  :  url_parametros,
                type :  'post',
                data : {'tipo':tipo},
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                    var estados = response.data;
                    var dropDown     ='<input class="form-control" id="txtBuscaEstados" name="txtBuscaEstados" '+
                                      'type="text" placeholder="Buscar.." onkeyup="" style="display:none">';
                     var itemDropDown ="";
                    for(var i=0; i < estados.length; i++)
                    {                        
                        itemDropDown+= "<li>"+
                        "<a href='javascript:obtieneValorEstadoCombo(\""+estados[i].valor2+"\",\""+
                                                                         estados[i].valor3+"\",\""+
                                                                         text+"\",\""+
                                                                         textId+"\");'>"+
                        estados[i].valor2+"</a></li>";
                    }
                    if (textId === '#txtIdBtnEstadosEstadisticas')
                    {
                        dropDown = dropDown + 
                        "<li><a href='javascript:obtieneValorEstadoCombo(\"TODOS\",\"todos\",\""+
                                                                         text+"\",\""+
                                                                         textId+"\");'>TODOS</a></li>" + 
                        itemDropDown + '';
                    }
                    else
                    {
                        dropDown = dropDown + itemDropDown + '';
                    }
                    $(ulDropDown).html(dropDown);
                }
        });
    }

    function obtieneValorEstadoCombo(oficina, idOficina, text, textId){
        $(text).text(oficina+"   ");
        $(textId).val(idOficina);
        if (textId === '#txtIdBtnEstadosEstadisticas')
        {
            seleccionaFechaGrafica($('#reportrange').data('daterangepicker').startDate.format('YYYY/MM/DD'), 
            $('#reportrange').data('daterangepicker').endDate.format('YYYY/MM/DD'));
        }
    }

    if (permiteVerAsignacionesDepNacional == false)
    {
        $('#divComboCantones').hide();
        $('#divBusquedaCantonesCambioTurno').hide();
        $("#barraOpcionesGridAsignaciones").removeClass( "buscarPorFechaDetallesAsig" ).addClass( "buscarPorFechaDetallesAsigSinCantones" );
        $("#divColumnCantones").addClass("anchoComboCantones");
    }
    else
    {
        $('#divComboCantones').show();
        $('#divBusquedaCantonesCambioTurno').show();
        $("#barraOpcionesGridAsignaciones").removeClass( "buscarPorFechaDetallesAsigSinCantones" ).addClass( "buscarPorFechaDetallesAsig" );
        $("#divColumnCantones").removeClass("anchoComboCantones");
    }

    /**
     * Añade clase a fila cuando se realiza check
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.0 09-047-2021
     */
    function cambiaCheck(el) {
        if($(el).is(":checked")) {
            $(el).parent('td').parent('tr').addClass("claseCheck");
            $('#btnAsignarMasivo').prop("disabled",false);
        } else {
            $(el).parent('td').parent('tr').removeClass("claseCheck");
            var numClaseCheck = $('.claseCheck').length;
            if(numClaseCheck < 1){
                $('#btnAsignarMasivo').prop("disabled",true);
            }
        }
    }

    /**
     * Validar campos en modal de asignación por lote
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.0 09-047-2021
     */
    function validaNuevaAsignacionLote()
    {
        var boolRespuesta= false;
            if ( (document.getElementById('cmbOrigenLote').value != '' ) &&
                (document.getElementById('cmbCriticidadLote').value != '' ) &&
                (document.getElementById('cmbTipoAtencionLote').value != '' ) &&
                (document.getElementById('cmbTipoAtencionLote').value != 'Seleccione...' ) &&
                (document.getElementById('cmbTipoProblemaLote').value != '' ) &&
                (document.getElementById('cmbTipoProblemaLote').value != 'Seleccione...' ) &&
                (document.getElementById('txtAgenteLote').value != '' ) &&
                (document.getElementById('txtaDetalleLote').value != '' )
                )
            {
                var tipoAtencion = document.getElementById('cmbTipoAtencionLote').value;
                //VALIDA QUE EL FORMATO DEL NUMERO DE TAREA O CASO SEA CORRECTO SOLO SI EL CAMPO DE TAREA/CASO ESTA LLENO
                if (document.getElementById('txtNumeroTareaCasoNuevaAsig').value !== "")
                {
                    boolRespuesta= validaFormatoNumeroCasoTarea(tipoAtencion,
                                                                '#alertaValidaNuevaAsignacion',
                                                                '#divTxtNumeroTareaCasoNuevaAsig',
                                                                'txtNumeroTareaCasoNuevaAsig');
                }
                else
                {
                    boolRespuesta = true;
                }
            }
            else
            {
                $('#alertaValidaNuevaAsignacionLote').addClass('alert-danger');
                $('#alertaValidaNuevaAsignacionLote').removeClass('alert-success');
                $('#alertaValidaNuevaAsignacionLote').removeClass('alert-warning');
                $('#alertaValidaNuevaAsignacionLote').html("<strong>Faltan datos por ingresar</strong>");
                $('#alertaValidaNuevaAsignacionLote').show();
                $('#alertaValidaNuevaAsignacionLote').fadeIn();
                $('#alertaValidaNuevaAsignacionLote').slideDown();
                $('#divCmbOrigenLote').addClass('has-error');
                $('#divCmbCriticidadLote').addClass('has-error');
                $('#divCmbTipoAtencionLote').addClass('has-error');
                $('#divCmbTipoProblemaLote').addClass('has-error');
                $('#divtxtAgenteLote').addClass('has-error');
                $('#divtxtaDetalleLote').addClass('has-error');
                $('#btnLoadingGrabarAsigLote').hide();
                $('#btnGrabarAsignacionLote').show();
            }
            return boolRespuesta;
    }

/**
 * Validar Tarea
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.0 13-09-2021
 * @since 1.0
 */
function validarTareasAbiertas(origen, data, nombre, duracionTarea, numeroTarea, nombreProceso, usrAsignado, deptAsignado, detalleHist, nombreUsrAsignado)
{
    document.getElementById('txtOrigen').value = origen;
    $("#divtxtaDetalleTarea").css('display', 'block');
    if(origen == "pausar")
    {
        $('#divtxtaMotivoPausa').show();
        $("#accionTareaTitle").text("Pausar Tarea");
        buscaMotivosPausa("cmbMotivoPausa","");
        $("#divtxtaDetalleTarea").css('display', 'none');

        //fix cuando se pausa tarea no se debe validar tareas abiertas
        $('#modalValidaTareasAbiertas').modal('hide');
        $('#modalEjecucionTarea').modal('show');
        return false;

    }

    if(origen == "reanudar")
    {
        $("#accionTareaTitle").text("Reanudar Tarea");
    }

    if(origen == "iniciar")
    {
        $("#accionTareaTitle").text("Gestionar Tarea");
    }

    var personaEmpresaRol = $("#txtPersonaEmpresaRol").val();
    $("#txtOrigenTarea").text(numeroTarea);

    $.ajax({
        data :  {
            personaEmpresaRolId: personaEmpresaRol
        },
        url  :  url_getTareasAbiertas,
        type :  'post',
        success:  function (response) {
            if (response.intCantidadTareasEjecutando > 0)
            {
                $('#modalValidaTareasAbiertas').modal('hide');
                $('#modalConfirmaTareaEjecucion').modal('show');
                $("#txtNumeroTareaConfirma").text(response.strTareas);
            }
            else
            {
                $('#modalValidaTareasAbiertas').modal('hide');
                $('#modalEjecucionTarea').modal('show');
            }
        },
        failure: function(response){
            $('#modalMensajeTareas').modal('show');
            $('#alertaMensajeTareas').text("Error al realizar acción. Por favor informar a Sistemas.");
        }
    });
}

/**
 * Ejecutar Pausa de tarea
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.0 13-09-2021
 * @since 1.0
 */
function gestionarTareas(origen, nombre, duracionTarea, detalleTarea, personaEmpresaRol, numeroTarea, nombreProceso, usrAsignado, deptAsignado, detalleHist, nombreUsrAsignado)
{

    $.ajax({
        data :  {
            personaEmpresaRolId: personaEmpresaRol,
            numeroTarea: numeroTarea,
            nombre_tarea: nombre,
            nombre_proceso: nombreProceso,
            asignado_nombre: usrAsignado,
            departamento_nombre: deptAsignado,
            id_detalle: detalleTarea
        },
        url  :  url_ejecutarPausarTareas,
        type :  'post',
        success:  function (response) {
            $('#modalConfirmaTareaEjecucion').modal('hide');
            document.getElementById("btnGrabarEjecucionTarea").disabled = false;
            var strOnclick = '';
            var strTipo = 'error';
            var strTittle = 'Alerta';
            if (typeof response.status !== 'undefined' && response.status == 'EXITO')
            {
                strOnclick = "$('#modalEjecucionTarea').modal('show');";
                strTipo = 'success';
                strTittle = 'Información';
            }            
            showModalMensajeCustom({tittle:strTittle,tipo:strTipo,mensaje: response.mensaje, btnOkOnClick:strOnclick,btnOkText:'OK'});
            //aceptarRechazarTarea(origen, detalleTarea, nombre, duracionTarea, personaEmpresaRol, 
            //numeroTarea, nombreProceso, usrAsignado, deptAsignado, detalleHist, nombreUsrAsignado);
        },
        failure: function(response){
            $('#modalMensajeTareas').modal('show');
            $('#alertaMensajeTareas').text("Error al realizar acción. Por favor informar a Sistemas.");
        }
    });
}

/**
 * Se añade lógica de actualizar asignación cuando se ejecute una tarea a otro usuario
 * @author Fernando López <filopez@telconet.ec>
 * @version 2.0 15-12-2021
 * 
 * Procesar Tarea
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.0 13-09-2021
 * @since 1.0
 */
function aceptarRechazarTarea(origen, data, nombre, duracionTarea, personaEmpresaRol, numeroTarea, nombreProceso, usrAsignado, deptAsignado, detalleHist, nombreUsrAsignado)
{
    var observacion = "";
    if(origen == "pausar")
    {
        observacion = nombre;

    }else{
        observacion = $('#txtObservacionTarea').val();
    }

    //fix para actualizar asiganción cuando se ejecute una tarea de otro usuario,
    //se ejecuta antes de iniciar o reanudar la tarea para que no se quede en estado asignada
    if(objAsignacionActualizar !== "" && objAsignacionActualizar.strUsuarioSession !== objAsignacionActualizar.usrAsignado && 
        $('#divListadoAsignaciones').css('display') !== 'none' && $('#liDetalles').hasClass('active') == true 
        && permiteVerNuevosCamposTareas == 1 && objAsignacionActualizar.id !='' )
    {
        parametros = { 
            "strOrigen"         : objAsignacionActualizar.origen, 
            "strTipoAtencion"   : objAsignacionActualizar.tipoAtencion, 
            "strLogin"          : objAsignacionActualizar.referenciaCliente, 
            "strTipoProblema"   : objAsignacionActualizar.tipoProblema, 
            "strNombreReporta"  : '', 
            "strNombreSitio"    : '', 
            "strCriticidad"     : objAsignacionActualizar.criticidad, 
            "strAgente"         : objAsignacionActualizar.strUsuarioSession, 
            "strDetalle"        : objAsignacionActualizar.detalle, 
            "strNumero"         : objAsignacionActualizar.numeroTarea, 
            "arrayAsigProact"   : Ext.JSON.encode([]),
            "strUpdateAsignacion": 'S',
            "strUpdateNewAgente": 'S'
        }; 
        $.ajax({
                data :  parametros,
                url  :  url_crea_asignacion,
                type :  'post',
                success:  function (response) {
                    var arrayResponse = response;
                    var strResponse = "";
                    var intIdDetalleHist = "";
                    if(response.indexOf("&") !== '-1')
                    {
                        arrayResponse = response.split('&');
                        strResponse = arrayResponse[0];
                        intIdDetalleHist = arrayResponse[1];
                    }
                    detalleHist = (intIdDetalleHist !== "")?intIdDetalleHist:detalleHist;
                    strResponse = (strResponse !== "")?strResponse:arrayResponse;
                    if (strResponse === 'OK')
                    {
                        $.ajax({
                            data :  {
                                id               : data,
                                observacion      : observacion,
                                bandera          : 'Aceptada',
                                origen           : origen,
                                duracionTarea    : duracionTarea,
                                jsonDatosPausa   : '',
                                intIdDetalleHist : detalleHist,
                                numeroTarea      : numeroTarea,
                                nombre_tarea     : nombre,
                                nombre_proceso   : nombreProceso,
                                asignado_nombre  : nombreUsrAsignado,
                                departamento_nombre: deptAsignado
                            },
                            url: '../tareas/administrarTareaAsignada',
                            type :  'POST',
                            success:  function (response) {
                                if (!response.success && !response.seguirAccion) {
                                    $('#modalValidaTareasAbiertas').modal('hide');
                                    $('#modalConfirmaTareaEjecucion').modal('hide');
                                    $('#modalValidaTareasAbiertas').modal('hide');
                                    $('#modalEjecucionTarea').modal('hide');
                                    $('#modalMensajeTareas').modal('show');
                                    $('#alertaMensajeTareasDiv').css('display', 'block');
                                    $('#alertaMensajeTareas').html(response.mensaje);                          
                                }
                                else if (response.mensaje != "cerrada")
                                {
                                    $('#modalValidaTareasAbiertas').modal('hide');
                                    $('#modalConfirmaTareaEjecucion').modal('hide');
                                    $('#modalValidaTareasAbiertas').modal('hide');
                                    $('#modalEjecucionTarea').modal('hide');
                                    $('#modalMensajeTareas').modal('show');
                                    $('#successMensajeTareasDiv').css('display', 'block');
                                    $('#successMensajeTareas').text("Se actualizó los datos.");
                                } else
                                {
                                    $('#modalValidaTareasAbiertas').modal('hide');
                                    $('#modalConfirmaTareaEjecucion').modal('hide');
                                    $('#modalValidaTareasAbiertas').modal('hide');
                                    $('#modalEjecucionTarea').modal('hide');
                                    $('#modalMensajeTareas').modal('show');
                                    $('#alertaMensajeTareasDiv').css('display', 'block');
                                    $('#alertaMensajeTareas').text("La tarea se encuentra Cerrada, por favor consultela nuevamente.");
                                }
                                if($('#divListadoAsignaciones').css('display') !== 'none' && $('#liDetalles').hasClass('active') == true )
                                {
                                    detalleAsignaciones.ajax.url(url_detalle_asignaciones).load();
                                }else
                                {
                                    detalleTareas.ajax.url(url_tareas_grid).load();
                                }                               
                                
                            },
                            failure: function(response){
                                $('#modalMensajeTareas').modal('show');
                                $('#alertaMensajeTareas').text("Error al realizar acción. Por favor informar a Sistemas.");
                            }
                        });
                        
                    }else
                    {
                        $('#modalValidaTareasAbiertas').modal('hide');
                        $('#modalConfirmaTareaEjecucion').modal('hide');
                        $('#modalValidaTareasAbiertas').modal('hide');
                        $('#modalEjecucionTarea').modal('hide');
                        showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje: "Error al realizar acción. Por favor informar a Sistemas.",
                                                btnOkText:'Cerrar'});  
                    }
                },
                failure: function(response){
                    $('#modalValidaTareasAbiertas').modal('hide');
                    $('#modalConfirmaTareaEjecucion').modal('hide');
                    $('#modalValidaTareasAbiertas').modal('hide');
                    $('#modalEjecucionTarea').modal('hide');
                    showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:"Error al realizar acción. Por favor informar a Sistemas.",
                                            btnOkText:'Cerrar'});
                    
                }
            });        
    }
    else
    {
        $.ajax({
            data :  {
                id               : data,
                observacion      : observacion,
                bandera          : 'Aceptada',
                origen           : origen,
                duracionTarea    : duracionTarea,
                jsonDatosPausa   : '',
                intIdDetalleHist : detalleHist,
                numeroTarea      : numeroTarea,
                nombre_tarea     : nombre,
                nombre_proceso   : nombreProceso,
                asignado_nombre  : nombreUsrAsignado,
                departamento_nombre: deptAsignado
            },
            url: '../tareas/administrarTareaAsignada',
            type :  'POST',
            success:  function (response) {
                if (!response.success && !response.seguirAccion) {
                    $('#modalValidaTareasAbiertas').modal('hide');
                    $('#modalConfirmaTareaEjecucion').modal('hide');
                    $('#modalValidaTareasAbiertas').modal('hide');
                    $('#modalEjecucionTarea').modal('hide');
                    $('#modalMensajeTareas').modal('show');
                    $('#alertaMensajeTareasDiv').css('display', 'block');
                    $('#alertaMensajeTareas').html(response.mensaje);                          
                }
                else if (response.mensaje != "cerrada")
                {
                    $('#modalValidaTareasAbiertas').modal('hide');
                    $('#modalConfirmaTareaEjecucion').modal('hide');
                    $('#modalValidaTareasAbiertas').modal('hide');
                    $('#modalEjecucionTarea').modal('hide');
                    $('#modalMensajeTareas').modal('show');
                    $('#successMensajeTareasDiv').css('display', 'block');
                    $('#successMensajeTareas').text("Se actualizó los datos.");
                } else
                {
                    $('#modalValidaTareasAbiertas').modal('hide');
                    $('#modalConfirmaTareaEjecucion').modal('hide');
                    $('#modalValidaTareasAbiertas').modal('hide');
                    $('#modalEjecucionTarea').modal('hide');
                    $('#modalMensajeTareas').modal('show');
                    $('#alertaMensajeTareasDiv').css('display', 'block');
                    $('#alertaMensajeTareas').text("La tarea se encuentra Cerrada, por favor consultela nuevamente.");
                }
                if($('#divListadoAsignaciones').css('display') !== 'none' && $('#liDetalles').hasClass('active') == true )
                {
                    detalleAsignaciones.ajax.url(url_detalle_asignaciones).load();
                }else
                {
                    detalleTareas.ajax.url(url_tareas_grid).load();
                }  
            },
            failure: function(response){
                $('#modalMensajeTareas').modal('show');
                $('#alertaMensajeTareas').text("Error al realizar acción. Por favor informar a Sistemas.");
            }
        });
    }
 }

/**
 * Ingreso de Seguimiento
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.0 13-09-2021
 * @since 1.0
 */
function ingresarSeguimiento(data, detalleSeguimiento, registroInterno)
{
     $.ajax({
        data :  {
            id_caso: "0",
            id_detalle: data,
            seguimiento: detalleSeguimiento,
            registroInterno: registroInterno
        },
        url: '../info_caso/ingresarSeguimiento',
        type :  'POST',
        success:  function (response) {
            if (response.mensaje != "cerrada")
            {
                $('#modalIngresoSeguimiento').modal('hide');
                $('#modalMensajeTareas').modal('show');
                $('#successMensajeTareasDiv').css('display', 'block');
                $('#successMensajeTareas').text("Se ingreso el seguimiento.");
            } else
            {
                $('#modalIngresoSeguimiento').modal('hide');
                $('#modalMensajeTareas').modal('show');
                $('#alertaMensajeTareasDiv').css('display', 'block');
                $('#alertaMensajeTareas').text("La tarea se encuentra Cerrada, por favor consultela nuevamente.");
            }

        },
        failure: function(response){
            $('#modalIngresoSeguimiento').modal('hide');
            $('#alertaMensajeTareas').text("Error al realizar acción. Por favor informar a Sistemas.");
        }
    });
 }

 /**
 * Busca los motivos de pausa
 * @author Francisco Cueva <facueva@telconet.ec>
 * @version 1.0 13-09-2021
 * @since 1.0
 */
function buscaMotivosPausa(nombreComboMotivoPausa,tipoProblema)
{
    var parametros = {
    };
    $.ajax({
            data :  parametros,
            url  :  url_motivosPausarTarea,
            type :  'post',
            success:  function (response) {
                var arrTiposProblema  = response.encontrados;
                var tiposProblema     = "<option>Seleccione...</option>"; 
                for(var i=0;i<arrTiposProblema.length;i++)
                {
                    tiposProblema += "<option value='"+arrTiposProblema[i].id_motivo+"'>"+arrTiposProblema[i].nombre_motivo+"</option>"; 
                }
                $("#"+nombreComboMotivoPausa).html(tiposProblema);
            },
            failure: function(response){
                $('#modalMensajeTareas').modal('show');
                $('#alertaMensajeTareas').text("Error al realizar acción. Por favor informar a Sistemas.");
            }
    });
}

/**
 * Cargar formulario para finalizar la tarea
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */
function finalizarTarea(tarea,e)
{
    //reiniciar elementos modals
    $('#divrbObsTareaFinal, #divchxFinalTareaHereda,#divtxtTareaFinalHereda, #divIdArboltarea, #alertaValidaFinalizaTarea').css('display', 'none');
    $('#divrbObsTareaFinal, #divchxFinalTareaHereda,#divtxtTareaFinalHereda, #divIdArboltarea, #alertaValidaFinalizaTarea').removeClass('showDiv');
    $('#divcmbMotivoTareaFinal, #divdrwTareaFinal, #divcmbTareaFinal').removeClass('has-error');
    $('#divcmbMotivoTareaFinal').removeClass('showMotivo')
    $("#chxFinalTareaHereda" ).prop( "checked", false);
    $("input[name='esSolucion']:checked").prop("checked",false);
    $("input[name='esSolucion'][value='S']").prop("checked",true);
    $('#btnGrabarFinalizaTarea').show();
    $('#txtaObsTareaFinal').val('');
    $('#btnLoadingGrabarFinalizaTarea').hide();
    e.setAttribute("disabled", "disabled");
    setTimeout(function(){e.removeAttribute("disabled", "");}, 5000);

    var objTarea = JSON.parse(tarea);
    $("#txtTareaInicial").text(objTarea.nombreTarea);

    var parametros = {};
    $.ajax({
            data :  parametros,
            url  :  url_obtenerFechaServer,
            type :  'post',
            success:  function (response) {
                if(response.success)
                {
                    var json = response;
                    var fechaFinArray = json.fechaActual.split("-");
                    var fechaActual   = fechaFinArray[2]+"-"+fechaFinArray[1]+"-"+fechaFinArray[0];
            
                    rs = validarFechaTareaReprogramada(objTarea.fechaEjecucion,objTarea.horaEjecucion,fechaActual,json.horaActual);
                                                        
                    if (rs == 1)
                    {
                        $("#txtTareaFinalHereda").text(objTarea.nombreTareaAnterior);
                        $("#txtTareaFinalHereda-name").val(objTarea.nombreTareaAnterior);
                        $("#txtTareaFinalHereda-id").val(objTarea.idTareaAnterior);

                        if(objTarea.permiteRegistroActivos === true && ((objTarea.id_caso !== 0) || (objTarea.esInterdepartamental === true)))
                        { 
                            $('#divcmbTareaFinal').css('display', 'none');
                            $('#divcmbTareaFinal').removeClass('showDiv');
                            $('#divIdArboltarea, #divchxFinalTareaHereda').css('display', 'block');
                            $('#divIdArboltarea, #divchxFinalTareaHereda').addClass('showDiv');
                            $('#spnTareaFinal').text('Seleccionar fin de tarea');
                            $('#drwArbolTarea-id').val('-1');
                            document.getElementById("cmbMotivoTareaFinal").disabled = true;
                            
                            var idDepartamento                  = objTarea.departamentoId;
                            var tipo                            = 'WEB';
                            var user                            = '';
                            var op                              = 'getCategoriasFinTarea';                          
        
                            var dataRequest = 
                            {
                                idDepartamento: idDepartamento,
                                tipo:           tipo
                            };
        
                            var dataJsonRequest = 
                            {
                                data: dataRequest,
                                user: user,    
                                op: op
                            };

                            var msgErrorGetFin = 'No se pudo cargar las categorías de fin de tarea, vuelva a intentar';
                            var titleAlerta = 'ALERTA';
                            $.ajax({
                                    data :  Ext.JSON.encode(dataJsonRequest),
                                    url  :  '../../rs/tecnico/ws/rest/procesar',
                                    type :  'post',
                                    success:  function (response) {
                                        if(response.status == 200)
                                        {
                                            
                                            finesTarea = response.categoriaTareas;
                                            buildArbolTarea(Ext.JSON.encode(finesTarea));  
                                            showModalsFinalizartarea(objTarea.idDetalle, Ext.JSON.encode(objTarea), fechaActual,json.horaActual, objTarea.tipoAsignado, objTarea.asignado_id );
                                            
                                        }
                                        else
                                        {
                                            msgErrorGetFin = (typeof response.mensaje !== 'undefined')? response.mensaje:msgErrorGetFin;
                                            showModalsMessage("error",msgErrorGetFin,titleAlerta);
                                            
                                        }

                                    },
                                    failure: function(response){
                                        showModalsMessage("error",msgErrorGetFin,titleAlerta);
                                        
                                    }
                            });     
                        }
                        else
                        {
                            $('#divcmbTareaFinal').css('display', 'block');
                            $('#divIdArboltarea, #divchxFinalTareaHereda').css('display', 'none');
                            $('#divcmbTareaFinal').addClass('showDiv');
                            $('#divIdArboltarea, #divchxFinalTareaHereda').removeClass('showDiv');

                            var parametros = {};
                            var paramtQuery = "?query=&nombre=&estado=Activo&visible=SI&caso="+objTarea.id_caso+"&detalle="+objTarea.idDetalle+"&page=1&start=0&limit=200";
                            $("#cmbTareaFinal").html("<option value='-1' >Seleccionar fin de tarea</option>");
                            $.ajax({
                                    data :  parametros,
                                    url  :  url_gridTarea+paramtQuery,
                                    type :  'post',
                                    success:  function (response) {
                                        if(response.total > 0)
                                        {
                                            var arrTareas  = response.encontrados;
                                            var listTareas     = "<option value='"+objTarea.nombreTarea+"' >"+objTarea.nombreTarea+"</option>"; 
                                            for(var i=0;i<arrTareas.length;i++)
                                            {
                                                listTareas += "<option value='"+arrTareas[i].id_tarea+"'>"+arrTareas[i].nombre_tarea+"</option>"; 
                                            }
                                            $("#cmbTareaFinal").html(listTareas);
                                            showModalsFinalizartarea(objTarea.idDetalle, Ext.JSON.encode(objTarea), fechaActual,json.horaActual, objTarea.tipoAsignado, objTarea.asignado_id );  
                                        }
                                        else
                                        {
                                            showModalsMessage("error","Error al realizar acción. Por favor informar a Sistemas.");
                                        }                                        
                                    },
                                    failure: function(response){
                                        showModalsMessage("error","Error al realizar acción. Por favor informar a Sistemas.");
                                    }
                            });
                                                
                        }                    
                    }
                    else
                    {
                        showModalsMessage("error","Error al realizar acción. Por favor informar a Sistemas.");
                    }
                }
                else
                {
                    showModalsMessage("error",response.error);
                }
            },
            failure: function(response){
                showModalsMessage("error","Error al realizar acción. Por favor informar a Sistemas.");
                
            }
    });
}

/**
 * Mostrar formulario para finalizar la tarea
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */

function showModalsFinalizartarea(idDetalle,tarea, fechaActual,horaActual,tipoAsignado,asignado_id )
{
    var objTarea = JSON.parse(tarea);
    $('#btnGrabarFinalizaTarea').attr('onclick', ''); 
    if(objTarea.cerrarTarea == "S")
    {
        if(objTarea.iniciadaDesdeMobil == "S")
        {      
            $('#divrbObsTareaFinal').removeClass('showDiv');      
            if(objTarea.perteneceCaso && !objTarea.casoPerteneceTN)
            {
                $('#divrbObsTareaFinal').css('display', 'block');
                $('input[name=esSolucion][value=S]').prop('checked', 'checked');
                $('#divrbObsTareaFinal').addClass('showDiv');
            }
            document.getElementById('txtFechaInicio').value  = objTarea.fechaEjecucion;
            document.getElementById('txtHoraInicio').value   = objTarea.horaEjecucion;
            document.getElementById('txtFechaCierre').value  = fechaActual;
            document.getElementById('txtHoraCierre').value   = horaActual;
            document.getElementById('txtTiempoTotal').value  = objTarea.duracionMinutos;
            objTareaJson  = Ext.JSON.encode(objTarea).replace(/"/g, '\\"');
            strClick = "guardarTareaFinalizada(\""+objTareaJson+"\","+idDetalle+")";
            $('#btnGrabarFinalizaTarea').attr('onclick',strClick);

            $('#modalFinalizarTarea').modal('show');
        }
        else
        {
            showModalsMessage("error","Para poder finalizar esta tarea, debe ser iniciada desde la aplicación Móvil");
        }
    }
    else
    {
        showModalsMessage("error","Esta tarea no se puede finalizar debido que posee una o más subtareas asociadas, por favor cerrar las tareas asociadas a la tarea principal.");
    }
}

/**
 * Checkbox para permitir finalizar tarea con tarea anterior
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */

$('#chxFinalTareaHereda').change(function () {
    if(this.checked) {
        $('#divIdArboltarea').css('display', 'none');
        $('#divIdArboltarea').removeClass('showDiv');
        $('#divtxtTareaFinalHereda').css('display', 'block');
        $('#divtxtTareaFinalHereda').addClass('showDiv');
        
    }else{
        $('#divIdArboltarea').css('display', 'block');
        $('#divIdArboltarea').addClass('showDiv');
        $('#divtxtTareaFinalHereda').css('display', 'none');
        $('#divtxtTareaFinalHereda').removeClass('showDiv');
    } 
});

/**
 * Función que arma el dropdown del arbol de tarea para lista de fin de tarea
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */

function buildArbolTarea(finesTarea,view)
{
    var strEl = (typeof view !== 'undefined')?((view === 'reasignar')? "Reasig":"" ):"";
    var objArbolTarea = JSON.parse(finesTarea); 
    drpArbolTarea = '';
    for(var i=0;i<objArbolTarea.length;i++)
    {
        objArbolTareaLevel2 = objArbolTarea[i].hijosCategoria;
        strNameLeve1= objArbolTarea[i].nombreCategoria; 
        drpArbolTarea += '<li class="dropdown-submenu"><a class="dropdown-item" tabindex="-1" href="#">'+objArbolTarea[i].nombreCategoria+'</a>';
        drpArbolTarea += '<ul class="dropdown-menu">';
        for(var j=0;j<objArbolTareaLevel2.length;j++)
        {
            drpArbolTarea += '<li class="dropdown-submenu"><a class="dropdown-item" href="#">'+objArbolTareaLevel2[j].nombreHijo+'</a>';
            drpArbolTarea += '<ul class="dropdown-menu">';
            objArbolTareaLevel3 = objArbolTareaLevel2[j].listaTareas;
            strNameLeve2= objArbolTareaLevel2[j].nombreHijo;  
            for(var k=0;k<objArbolTareaLevel3.length;k++)
            {
                strNameLeve3= objArbolTareaLevel3[k].nombreTarea;
                strNumeroTarea= objArbolTareaLevel3[k].numeroTarea;
                objTareaSelect = {nameLeve1: strNameLeve1,
                                nameLeve2: strNameLeve2,
                                nameLeve3: strNameLeve3,
                                numeroTarea: strNumeroTarea,
                                requiereMaterial: objArbolTareaLevel3[k].requiereMaterial,
                                requiereFibra: objArbolTareaLevel3[k].requiereFibra,
                                requiereRutaFibra: objArbolTareaLevel3[k].requiereRutaFibra,
                                requiereEquipo: objArbolTareaLevel3[k].requiereEquipo
                                };
                strParametros = Ext.JSON.encode(objTareaSelect);
                drpArbolTarea += '<li class="dropdown-item"><a onClick=\'javascript:loadMotivosTareas('+strParametros+',"'+strEl+'");setdropdownArboltarea('+strParametros+',"'+strEl+'")\' href="#">'+objArbolTareaLevel3[k].nombreTarea+'</a></li>';
            }
            drpArbolTarea += '</ul>';
            drpArbolTarea += '</li>';
        }
        drpArbolTarea += '</ul>';
        drpArbolTarea += '</li>';
        
    }
    $("#drwArbolTarea"+strEl).html(drpArbolTarea);
}

/**
 * Función que permite setear la tarea en el dropdown del arbol de tareas
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */

function setdropdownArboltarea(objTareaSelect,prefElem)
{
    var strEl = (typeof prefElem !== 'undefined')?prefElem:"";

    $('#drwTareaFinal'+strEl+'>#spnTareaFinal'+strEl).text(objTareaSelect.nameLeve3);    
    document.getElementById('drwArbolTarea'+strEl+'-namel1').value  = objTareaSelect.nameLeve1;
    document.getElementById('drwArbolTarea'+strEl+'-namel2').value  = objTareaSelect.nameLeve2;
    document.getElementById('drwArbolTarea'+strEl+'-name').value  = objTareaSelect.nameLeve3;
    document.getElementById('drwArbolTarea'+strEl+'-id').value  = objTareaSelect.numeroTarea;
    document.getElementById('drwArbolTarea'+strEl+'-RequiereMaterial').value  = objTareaSelect.requiereMaterial;
    document.getElementById('drwArbolTarea'+strEl+'-RequiereFibra').value  = objTareaSelect.requiereFibra;
    document.getElementById('drwArbolTarea'+strEl+'-RequiereRutaFibra').value  = objTareaSelect.requiereRutaFibra;
    document.getElementById('drwArbolTarea'+strEl+'-RequiereEquipo').value  = objTareaSelect.requiereEquipo;
}

/**
 * Función que carga los motivos de una tarea del arbol de tareas
 * @author Fernando Lopez <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */
function loadMotivosTareas(objTareaSelect,prefElem)
{
    var strEl = (typeof prefElem !== 'undefined')?prefElem:"";
    var parametros = {};
    $('#divcmbMotivoTareaFinal'+strEl).removeClass('showMotivo')
    strNumeroTareaSelect = document.getElementById('drwArbolTarea'+strEl+'-id').value;
    strName1L1 = document.getElementById('drwArbolTarea'+strEl+'-namel1').value;
    strName1L2 = document.getElementById('drwArbolTarea'+strEl+'-namel2').value;
    if(strNumeroTareaSelect !== '-1' && strNumeroTareaSelect == objTareaSelect.numeroTarea && 
        strName1L1 == objTareaSelect.nameLeve1 && strName1L2 == objTareaSelect.nameLeve2)
    {
        $('#divcmbMotivoTareaFinal'+strEl).addClass('showMotivo')
        return false;
    }
    var paramtQuery = "?query=''&valor1="+objTareaSelect.nameLeve1+"&valor2="+objTareaSelect.nameLeve2+"&valor3="+objTareaSelect.numeroTarea+"&page=1&start=0&limit=200";
    
    document.getElementById("cmbMotivoTareaFinal"+strEl).disabled = true;
    $("#cmbMotivoTareaFinal"+strEl).html("<option value='-1' >Seleccione motivo</option>");

    $.ajax({
            data :  parametros,
            url  :  url_gridMotivosCategoriaTareas+paramtQuery,
            type :  'post',
            success:  function (response) {
                if(response.status == 'ok')
                {
                    var arrMotivos  = response.data;
                    if(arrMotivos.length > 0)
                    {
                        var listMotivos     = "<option value='-1' >Seleccione motivo</option>"; 
                        for(var i=0;i<arrMotivos.length;i++)
                        {
                            listMotivos += "<option value='"+arrMotivos[i].idMotivo+"'>"+arrMotivos[i].nombreMotivo+"</option>"; 
                        }
                        $("#cmbMotivoTareaFinal"+strEl).html(listMotivos);
                        document.getElementById("cmbMotivoTareaFinal"+strEl).disabled = false;
                        $('#divcmbMotivoTareaFinal'+strEl).css('display', 'block');
                        $('#divcmbMotivoTareaFinal'+strEl).addClass('showMotivo')
                    }
                    else
                    {
                        $('#divcmbMotivoTareaFinal'+strEl).css('display', 'none');
                    }
                }
                else
                {
                $('#divcmbMotivoTareaFinal'+strEl).css('display', 'none');
                    
                }
                
            },
            failure: function(response){
                $('#divcmbMotivoTareaFinal'+strEl).css('display', 'none');
            }
    });
}

/*
* Función que valida el formulario de finalizar tarea
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 16-11-2021
* @since 1.0
*/

function guardarTareaFinalizada(tarea, id_detalle)
{
    var data = JSON.parse(tarea);
    var boolValidaTareaFinal = false;
    var strFinSelectNombre = '';
    var strIdMotivo = '';
    if ($('#divchxFinalTareaHereda').hasClass('showDiv') && ($("#chxFinalTareaHereda").is( ":checked" )) == true)
    {
        boolValidaTareaFinal = true;
    }
    if ($("#divchxFinalTareaHereda").hasClass('showDiv') && ($("#chxFinalTareaHereda").is( ":checked" )) !== true )
    {
        boolValidaTareaFinal = false ;
    }
    if($("#divIdArboltarea").hasClass('showDiv') && boolValidaTareaFinal == false)
    {
        if(document.getElementById('drwArbolTarea-id').value != '-1')
        {
            strFinSelectNombre           = document.getElementById('drwArbolTarea-name').value;
            data.strRequiereMaterial     = document.getElementById('drwArbolTarea-RequiereMaterial').value;
            data.strRequiereFibra        = document.getElementById('drwArbolTarea-RequiereFibra').value;
            data.strRequiereRutaFibra    = document.getElementById('drwArbolTarea-RequiereRutaFibra').value;
            data.intFinTareaId           = document.getElementById('drwArbolTarea-id').value;

        }
        if($('#divcmbMotivoTareaFinal').hasClass('showMotivo') && $('#cmbMotivoTareaFinal').val() !='-1')
        {
            strIdMotivo = $('#cmbMotivoTareaFinal').val();
        } 
    }

    if(data.permiteRegistroActivos === true && ((data.id_caso !== 0) || (data.esInterdepartamental === true) ) && strFinSelectNombre === '') 
    {
            showAlert("alertaValidaFinalizaTarea","divdrwTareaFinal","Por favor seleccione fin de tarea");   
    }
    else if( strIdMotivo == '' && $('#divcmbMotivoTareaFinal').hasClass('showMotivo') && $("#divIdArboltarea").hasClass('showDiv') &&boolValidaTareaFinal == false)
    {
        showAlert("alertaValidaFinalizaTarea","divcmbMotivoTareaFinal","Por favor seleccione motivo"); 
    }
    else if($('#divcmbTareaFinal').hasClass('showDiv') && $('#cmbTareaFinal').val() == '-1')
    {
        showAlert("alertaValidaFinalizaTarea","divcmbTareaFinal","Por favor seleccione fin de tarea");    
    }
    else
    {

        var finalizaObservacion = $('#txtaObsTareaFinal').val();
        var finalizaRadio       = $("input[name='esSolucion']:checked").val();
        var finalizaTiempo      = $('#txtTiempoTotal').val();
        var finalizaFeCierre    = $('#txtFechaCierre').val();
        var finalizaHorCierre   = $('#txtHoraCierre').val();
        var motivoFinalizaTarea;
        var finalizaComboTarea;
        finalizaRadio = (typeof finalizaRadio !== 'undefined')?((finalizaRadio=='S')?true:false):false;
        

        if (data.permiteRegistroActivos === true && ((data.id_caso !== 0) || (data.esInterdepartamental === true))
        && boolValidaTareaFinal == false && $('#divcmbMotivoTareaFinal').hasClass('showMotivo'))
        {
            motivoFinalizaTarea = strIdMotivo;
            finalizaComboTarea  = strFinSelectNombre;
        }
        else if(boolValidaTareaFinal)
        {
            finalizaComboTarea =  data.nombreTareaAnterior;
        }  
        else
        {
            finalizaComboTarea  = $( "#cmbTareaFinal option:selected" ).text();
        }
        if(data.perteneceCaso && data.casoPerteneceTN )
        {
            finalizaRadio       = false;
        }
        let intFinTareaId;
        let nombreTarea = $( "#cmbTareaFinal" ).val();
        if (data.intFinTareaId === undefined) 
        {
            if ($( "#cmbTareaFinal" ).val() == data.nombreTarea && boolValidaTareaFinal == false) 
            {
                intFinTareaId = data.id_tarea;
            }
            else if(boolValidaTareaFinal && $('#divchxFinalTareaHereda').hasClass('showDiv'))
            {
                intFinTareaId = data.idTareaAnterior;
            }
            else
            {            
                intFinTareaId = nombreTarea;
            }
        }
        else
        {
            intFinTareaId = data.intFinTareaId;
        }
        var longitudIncidente   = "";
        var latitudIncidente    = "";
        var longitudManga1      = "";
        var latitudManga1       = "";
        var longitudManga2      = "";
        var latitudManga2       = "";

        var arayDataFinalizar = 
            {
                id_detalle:         id_detalle,
                observacion:        finalizaObservacion,
                esSolucion:         finalizaRadio,
                tiempo_total:       finalizaTiempo,
                tiempo_cierre:      finalizaFeCierre,
                hora_cierre:        finalizaHorCierre,
                tiempo_ejecucion:   data.fechaEjecucion,
                hora_ejecucion:     data.horaEjecucion,
                clientes:           data.clientes,
                tarea:              finalizaComboTarea,
                tarea_final:        "S",
                longitud:           longitudIncidente,
                latitud:            latitudIncidente,
                longitudManga1:     longitudManga1,
                latitudManga1:      latitudManga1,
                longitudManga2:     longitudManga2,
                latitudManga2:      latitudManga2,
                duracionTarea:      data.duracionTarea,
                id_caso:            data.id_caso,
                casoPerteneceTN:    data.casoPerteneceTN,
                intIdDetalleHist:   data.intIdDetalleHist,
                numeroTarea:        data.numeroTarea,
                nombre_tarea:       data.nombreTarea,
                nombre_proceso:     data.nombreProceso,
                asignado_nombre:    data.ref_asignado_nombre,
                departamento_nombre:data.asignado,
                esInterdepartamental:data.esInterdepartamental,
                tipoMedioId:        data.tipoMedioId,
                idMotivoFinaliza:   motivoFinalizaTarea,
                idFinTarea:         intFinTareaId,
                boolFinalTareaAnterior: boolValidaTareaFinal

        };

        finalizarTareaRequest(arayDataFinalizar);
    }
}

/*
* Función que realiza la finalización de tarea
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 16-11-2021
* @since 1.0
*/

function finalizarTareaRequest(arrayFin)
{
    parametros = 
    {
        id_detalle: arrayFin.id_detalle,
        observacion: arrayFin.observacion,
        esSolucion: arrayFin.esSolucion,
        tiempo_total: arrayFin.tiempo_total,
        tiempo_cierre: arrayFin.tiempo_cierre,
        hora_cierre: arrayFin.hora_cierre,
        tiempo_ejecucion: arrayFin.tiempo_ejecucion,
        hora_ejecucion: arrayFin.hora_ejecucion,
        clientes: arrayFin.clientes,
        tarea: arrayFin.tarea,
        tarea_final: arrayFin.tarea_final,
        longitud: arrayFin.longitud,
        latitud: arrayFin.latitud,
        longitudManga1: arrayFin.longitudManga1,
        latitudManga1: arrayFin.latitudManga1,
        longitudManga2: arrayFin.longitudManga2,
        latitudManga2: arrayFin.latitudManga2,
        duracionTarea: arrayFin.duracionTarea,
        intIdDetalleHist : arrayFin.intIdDetalleHist,
        numeroTarea:         arrayFin.numeroTarea,
        nombre_tarea:        arrayFin.nombre_tarea,
        nombre_proceso:      arrayFin.nombre_proceso,
        asignado_nombre:     arrayFin.asignado_nombre,
        departamento_nombre: arrayFin.departamento_nombre,
        esInterdepartamental:arrayFin.esInterdepartamental,
        idMotivoFinaliza    : arrayFin.idMotivoFinaliza,
        idFinTarea          : arrayFin.idFinTarea, //jobedon
        boolFinalTareaAnterior : arrayFin.boolFinalTareaAnterior
    };
    var stroOnclick="";
    if($('#liTareas').hasClass('active') == true)
    {
        stroOnclick="javascript:detalleTareas.search('').columns().search('').draw();detalleTareas.ajax.url(url_tareas_grid).load();";
    }else if($('#liDetalles').hasClass('active') == true)
    {
        stroOnclick="javascript:detalleAsignaciones.search('').columns().search('').draw();$('#lnDetalles').click();";
    }

    $.ajax({
        data :  parametros,
        url  :  url_finalizarTarea,
        type :  'post',
        beforeSend: function () {
            $('#btnGrabarFinalizaTarea').hide();
            $('#btnLoadingGrabarFinalizaTarea').show();
        },
        success:  function (response) {
            if (!response.success && !response.seguirAccion) {

                $('#modalFinalizarTarea').modal('hide');
                showModalsMessageFinTarea("error",response.mensaje,'Alerta',stroOnclick,true);
                $('#btnGrabarFinalizaTarea').show();
                $('#btnLoadingGrabarFinalizaTarea').hide();
                return;
            }

            if (response.success)
            {
                if(response.mensaje != "cerrada")
                {
                    $('#modalFinalizarTarea').modal('hide');
                    showModalsMessageFinTarea("success",response.mensaje,'Mensaje',stroOnclick);
                }
                else
                {
                    $('#modalFinalizarTarea').modal('hide');
                    showModalsMessageFinTarea("error","La tarea se encuentra Cerrada, por favor consultela nuevamente","Alerta");
                }
                $('#btnGrabarFinalizaTarea').show();
                $('#btnLoadingGrabarFinalizaTarea').hide();
            }
            else
            {
                showAlert("alertaValidaFinalizaTarea","",response.mensaje);
                $('#btnGrabarFinalizaTarea').show();
                $('#btnLoadingGrabarFinalizaTarea').hide();
            }
        },
        failure: function(response){
            showAlert("alertaValidaFinalizaTarea","",response.mensaje);
            $('#btnGrabarFinalizaTarea').show();
            $('#btnLoadingGrabarFinalizaTarea').hide();
        }
    });
        
}

/*
* Función que realiza la reasignación de una tarea (validaciones)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function reasignarTarea(tarea,e)
{
    $("#intIdDetalleReasigna,#txtaObsTareaFinalReasigna,#txtDatetimepickerEjecReasigna").val("");
    $("#cmbCiudadReasigna,#cmbEmpresaReasigna,#cmbDepartReasigna,#cmbEmpleadoReasigna").attr('disabled','disabled');
    $('#divIdArboltareaReasig,#alertaValidaReasignaTarea').css('display', 'none');
    $('#divIdArboltareaReasig').removeClass('showDiv');
    $('#divcmbMotivoTareaFinalReasig,#divdrwTareaFinalReasig,#divcmbEmpresaReasigna,#divcmbCiudadReasigna,#divtxtaObsTareaFinalReasigna'+
    ',#divcmbDepartReasigna,#divcmbEmpleadoReasigna,#divcmbdDateEjecReasigna,#divcmbHourEjecReasigna').removeClass('has-error');
    $('#divcmbMotivoTareaFinal').removeClass('showMotivo');
    $('#btnGrabarReasignaTarea').show();
    $('#btnLoadingGrabarReasignaTarea').hide();
    $("#chxRespuestaInmediata" ).prop( "checked", false);

    e.setAttribute("disabled", "disabled");
    setTimeout(function(){e.removeAttribute("disabled", "");}, 5000);
    var arrayDataGrid = JSON.parse(tarea);

    /* No aplica para agente
    var strTieneProgresoMateriales  = arrayDataGrid.tieneProgresoMateriales;
    var strEmpresaTarea             = arrayDataGrid.strEmpresaTarea;
    var strControlActivos           = arrayDataGrid.requiereControlActivo;
    var strTareaEsHal               = arrayDataGrid.tareaEsHal*/

    $("#txtTareaInicialReasigna").text(arrayDataGrid.nombreTarea);
    $("#intIdDetalleReasigna").val(arrayDataGrid.idDetalle);
    
    if(arrayDataGrid.permiteRegistroActivos === true)
    {
        $('#divIdArboltareaReasig').css('display', 'block');
        $('#divIdArboltareaReasig').addClass('showDiv');
        $('#spnTareaFinalReasig').text('Seleccionar fin de tarea');
        $('#drwArbolTareaReasig-id').val('-1');
        document.getElementById("cmbMotivoTareaFinalReasig").disabled = true;

        var idDepartamento                  = arrayDataGrid.departamentoId;
        var tipo                            = 'WEB';
        var user                            = '';
        var op                              = 'getCategoriasFinTarea';
        var msgErrorGetFin                  = 'No se pudo cargar las categorías de fin de tarea, vuelva a intentar';
        var titleAlerta                     = 'ALERTA';

        var dataRequest =
        {
            idDepartamento: idDepartamento,
            tipo:           tipo
        };

        var dataJsonRequest =
        {
            data: dataRequest,
            user: user,
            op: op
        };

        $.ajax({
            data :  Ext.JSON.encode(dataJsonRequest),
            url  :  '../../rs/tecnico/ws/rest/procesar',
            type :  'post',
            success:  function (response) {

                if(response.status === null || response.status === 'ERROR')
                {
                    showModalsMessage("error",msgErrorGetFin,titleAlerta);
                }
                else if(response.status === 200)
                {
                    var parametros = {};
                    finesTareaReasig = response.categoriaTareas;
                    buildArbolTarea(Ext.JSON.encode(finesTareaReasig),'reasignar'); 
                    //Obtener la fecha y hora del servidor
                    $.ajax({
                        data :  parametros,
                        url  :  url_obtenerFechaServer,
                        type :  'post',
                        success:  function (response) {
                            if(response.success)
                            {
                                showModalsReasignarTarea(arrayDataGrid.idDetalle,
                                    arrayDataGrid.id_tarea,
                                    Ext.JSON.encode(arrayDataGrid),
                                    response.fechaActual,
                                    response.horaActual,
                                    arrayDataGrid.tipoAsignado);
                            }
                            else
                            {
                                showModalsMessage("error",response.error);
                            }
                        },
                        failure: function(response){
                            showModalsMessage("error","Error al realizar acción. Por favor informar a Sistemas.");
                            
                        }
                    });
                }
                else
                {
                    showModalsMessage("error",response.mensaje,titleAlerta);
                }

            },
            failure: function(response){
                showModalsMessage("error",msgErrorGetFin,titleAlerta);
                
            }
        });
    }
    else
    {
        var parametros = {};
        //Obtener la fecha y hora del servidor
        $.ajax({
            data :  parametros,
            url  :  url_obtenerFechaServer,
            type :  'post',
            success:  function (response) {
                if(response.success)
                {
                    showModalsReasignarTarea(arrayDataGrid.idDetalle,
                        arrayDataGrid.id_tarea,
                        Ext.JSON.encode(arrayDataGrid),
                        response.fechaActual,
                        response.horaActual,
                        arrayDataGrid.tipoAsignado);
                }
                else
                {
                    showModalsMessage("error",response.error);
                }
            },
            failure: function(response){
                showModalsMessage("error","Error al realizar acción. Por favor informar a Sistemas.");
                
            }
        });        
    }

}

/*
* Función que levanta el modals de reasignar tarea
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function showModalsReasignarTarea(idDetalle,idTarea,tarea, fechaActual,horaActual,tipoAsignado)
{
    var objTarea = JSON.parse(tarea);
    $('#btnGrabarReasignaTarea').attr('onclick', '');
    
    var listaHoras     = "<option value='-1' >Seleccione Hora</option>"; 
    arrayHoras = buildHoras();
    for(var i=0;i<arrayHoras.length;i++)
    {
        listaHoras += "<option value='"+arrayHoras[i]+"'>"+arrayHoras[i]+"</option>"; 
    }
    $("#cmbHourEjecReasigna").html(listaHoras);
    $("#cmbHourEjecReasigna").val(horaActual);

    $('#txtDatetimepickerEjecReasigna').val(fechaActual);
    
    if(typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim())
    { 
        loadEmpresa(strPrefijoEmpresaSession.trim());
        if(typeof strIdCantonUsrSession !== 'undefined' && strIdCantonUsrSession.trim())
        {
            loadCiudad(strPrefijoEmpresaSession.trim(),strIdCantonUsrSession.trim());
            
            if(typeof strIdDepartamentoUsrSession !== 'undefined' && strIdDepartamentoUsrSession.trim())
            {
                loadDepartamentos(strPrefijoEmpresaSession.trim(),strIdCantonUsrSession.trim(),strIdDepartamentoUsrSession.trim());
                loadEmpleados(strPrefijoEmpresaSession.trim(),strIdCantonUsrSession.trim(),strIdDepartamentoUsrSession.trim());
            }
        }
    }else
    {
        loadEmpresa();
    }
    objTareaJson  = Ext.JSON.encode(objTarea).replace(/"/g, '\\"');
    strClick = "guardarTareaReasignar(\""+objTareaJson+"\","+idDetalle+")";
    $('#btnGrabarReasignaTarea').attr('onclick',strClick);
    $('#modalReasignarTarea').modal('show'); 
}

/*
* Función que realiza la reasignación de una tarea (guardar)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function guardarTareaReasignar(tarea, id_detalle)
{
    var data = JSON.parse(tarea);
    var strFinSelectNombre = '';
    var motivoFinalizaTarea;
    var idMotivoFinalizaTarea;
    if($("#divIdArboltareaReasig").hasClass('showDiv') && data.permiteRegistroActivos === true)
    {
        if(document.getElementById('drwArbolTareaReasig-id').value != '-1')
        {
            strFinSelectNombre           = document.getElementById('drwArbolTareaReasig-name').value;
            data.strRequiereMaterial     = document.getElementById('drwArbolTareaReasig-RequiereMaterial').value;
            data.strRequiereFibra        = document.getElementById('drwArbolTareaReasig-RequiereFibra').value;
            data.strRequiereRutaFibra    = document.getElementById('drwArbolTareaReasig-RequiereRutaFibra').value;
            data.intFinTareaId           = document.getElementById('drwArbolTareaReasig-id').value;
            idMotivoFinalizaTarea = null;
            motivoFinalizaTarea  = "Sin Motivo";

        }
        if($('#divcmbMotivoTareaFinalReasig').hasClass('showMotivo') && $('#cmbMotivoTareaFinalReasig').val() !='-1')
        {
            idMotivoFinalizaTarea = $('#cmbMotivoTareaFinalReasig').val();
            motivoFinalizaTarea  = $( "#cmbMotivoTareaFinalReasig option:selected" ).text();
        } 
    }

    if(data.permiteRegistroActivos === true && $("#divIdArboltareaReasig").hasClass('showDiv') && strFinSelectNombre === '') 
    {
            showAlert("alertaValidaReasignaTarea","divdrwTareaFinalReasig","Por favor seleccione fin de tarea");   
    }
    else if( idMotivoFinalizaTarea == '' && $('#divcmbMotivoTareaFinal').hasClass('showMotivo') && 
            $("#divIdArboltareaReasig").hasClass('showDiv'))
    {
        showAlert("alertaValidaReasignaTarea","divcmbMotivoTareaFinalReasig","Por favor seleccione motivo"); 
    }
    else if($('#cmbEmpresaReasigna').val() == '-1')
    {
        showAlert("alertaValidaReasignaTarea","divcmbEmpresaReasigna","Por favor seleccione una empresa");    
    }
    else if($('#cmbCiudadReasigna').val() == '-1')
    {
        showAlert("alertaValidaReasignaTarea","divcmbCiudadReasigna","Por favor seleccione ciudad");    
    }
    else if($('#cmbDepartReasigna').val() == '-1')
    {
        showAlert("alertaValidaReasignaTarea","divcmbDepartReasigna","Por favor seleccione departamento");    
    }
    else if($('#cmbEmpleadoReasigna').val() == '-1')
    {
        showAlert("alertaValidaReasignaTarea","divcmbEmpleadoReasigna","Por favor escoja un empleado");    
    }
    else if($('#txtDatetimepickerEjecReasigna').val() == '')
    {
        showAlert("alertaValidaReasignaTarea","divcmbdDateEjecReasigna","Por favor seleccione fecha");    
    }
    else if($('#cmbHourEjecReasigna').val() == '-1')
    {
        showAlert("alertaValidaReasignaTarea","divcmbHourEjecReasigna","Por favor seleccione la hora");    
    }
    else if($('#txtaObsTareaFinalReasigna').val() == '')
    {
        showAlert("alertaValidaReasignaTarea","divtxtaObsTareaFinalReasigna","Por favor ingrese una observación");    
    }
    else
    {
        var strEmpleadoAsignado = $('#cmbEmpleadoReasigna').val();
        var strDepartamentoAsignado = $('#cmbDepartReasigna').val();
        var objParametro = 
            {
                id_detalle: id_detalle,
                id_tarea: data.id_tarea,
                motivo: $("#txtaObsTareaFinalReasigna").val(),
                departamento_asignado: strDepartamentoAsignado,
                empleado_asignado   :  strEmpleadoAsignado,
                cuadrilla_asignada  :  '',
                contratista_asignada:  '',
                tipo_asignado       :  data.tipoAsignado.toLowerCase(),
                fecha_ejecucion     :  $('#txtDatetimepickerEjecReasigna').val(),
                hora_ejecucion      :  $('#cmbHourEjecReasigna').val(),
                intIdDetalleHist    :  data.intIdDetalleHist,
                nombre_tarea        :  data.nombreTarea,
                numero_tarea        :  data.numeroTarea,
                nombreFinTarea      :  strFinSelectNombre,
                idFinTarea          :  (typeof data.intFinTareaId !== 'undefined')?data.intFinTareaId:"",
                motivoFinTarea      :  motivoFinalizaTarea,
                idMotivoFinTarea    :  idMotivoFinalizaTarea
        };

        var stroOnclick="";
        if($('#liTareas').hasClass('active') == true)
        {
            stroOnclick="javascript:detalleTareas.search('').columns().search('').draw();detalleTareas.ajax.url(url_tareas_grid).load();";
        }else if($('#liDetalles').hasClass('active') == true)
        {
            stroOnclick="javascript:detalleAsignaciones.search('').columns().search('').draw();$('#lnDetalles').click();";
        }

        $.ajax({
            data :  objParametro,
            url  :  url_reasignarTarea,
            type :  'post',
            beforeSend: function () {
                $('#btnGrabarReasignaTarea').hide();
                $('#btnLoadingGrabarReasignaTarea').show();
            },
            success:  function (response) {
                if (!response.success && !response.seguirAccion) 
                {
                    $('#modalReasignarTarea').modal('hide');
                    showModalsMessageFinTarea("error",response.mensaje,'Alerta',stroOnclick,true);
                    $('#btnGrabarReasignaTarea').show();
                    $('#btnLoadingGrabarReasignaTarea').hide();
                    return;
                }

                if (response.success)
                {
                    $('#modalReasignarTarea').modal('hide');
                    if(response.mensaje != "cerrada")
                    {
                        showModalsMessageFinTarea("success","Se asigno la tarea.","Mensaje",stroOnclick);
                        //actualizar asignación en base a la reasignación realizada
                        actualizarAsignacion(Ext.JSON.encode(data),strEmpleadoAsignado);
                    }
                    else
                    {
                        showModalsMessageFinTarea("error","La tarea se encuentra Cerrada, por favor consultela nuevamente","Alerta");
                    }
                    $('#btnGrabarReasignaTarea').show();
                    $('#btnLoadingGrabarReasignaTarea').hide();
                }
                else
                {
                    showAlert("alertaValidaFinalizaTarea","",response.mensaje);
                    $('#btnGrabarReasignaTarea').show();
                    $('#btnLoadingGrabarReasignaTarea').hide();
                }
            },
            failure: function(response){
                showAlert("alertaValidaFinalizaTarea","",response.mensaje);
                $('#btnGrabarReasignaTarea').show();
                $('#btnLoadingGrabarReasignaTarea').hide();
            }
        });
    }
}

/*
* Función que permite actualizar la asignación cuando se realiza una reasignación
* Aplica solo para el módulo agente=>asignaciones
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function actualizarAsignacion(data,strEmpleadoReasigna)
{
    var objTarea = JSON.parse(data);
    if($('#divListadoAsignaciones').css('display') !== 'none' && $('#liDetalles').hasClass('active') == true 
        && permiteVerNuevosCamposTareas == 1 && objTarea.id !='')
    {    
        parametros = { 
            "strOrigen"         : objTarea.origen, 
            "strTipoAtencion"   : objTarea.tipoAtencion, 
            "strLogin"          : objTarea.referenciaCliente, 
            "strTipoProblema"   : objTarea.tipoProblema, 
            "strNombreReporta"  : '', 
            "strNombreSitio"    : '', 
            "strCriticidad"     : objTarea.criticidad, 
            "strAgente"         : objTarea.usrAsignado, 
            "strDetalle"        : objTarea.detalle, 
            "strNumero"         : objTarea.numeroTarea, 
            "arrayAsigProact"   : Ext.JSON.encode([]),
            "strUpdateAsignacion": 'S',
            "strAgenteReasigna" : strEmpleadoReasigna
        }; 
        $.ajax({
                data :  parametros,
                url  :  url_crea_asignacion,
                type :  'post',
                success:  function (response) {
                    if (response === 'OK')
                    {
                        //asignación actualizada desde una reasignacion de tarea
                        return true;
                    }
                },
                failure: function(response){
                    return false;
                }
            });
    }else
    {
        return false;
    }
}

/*
* Función que carga las empresas (form reasignar tarea)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function loadEmpresa(prefEmpresa)
{
    var parametros = {};
    var strApp = 'TELCOS';
    var paramQuery = "?app="+strApp+"&page=1&start=0&limit=200";
    
    $("#cmbEmpresaReasigna").html("<option value='-1' >Seleccione empresa</option>");

    $.ajax({
            data :  parametros,
            url  :  url_empresaPorSistema+paramQuery,
            type :  'post',
            success:  function (response) {
                if(response.total > 0)
                {
                    var arrayEmpresas  = response.encontrados;
                    if(arrayEmpresas.length > 0)
                    {
                        var listaEmpresas     = "<option value='-1' >Seleccione empresa</option>"; 
                        for(var i=0;i<arrayEmpresas.length;i++)
                        {
                            listaEmpresas += "<option value='"+arrayEmpresas[i].prefijo+"'>"+arrayEmpresas[i].nombre_empresa+"</option>"; 
                        }
                        $("#cmbEmpresaReasigna").html(listaEmpresas);
                        $('#cmbEmpresaReasigna').removeAttr('disabled');
                        if(typeof prefEmpresa !== 'undefined')
                        {
                            $("#cmbEmpresaReasigna").val(prefEmpresa);
                        }
                    }
                    else
                    {
                        showAlertModals('alertaValidaReasignaTarea','Error al cargar las empresas. Por favor intente nuevamente.');
                    }
                }
                else
                {
                    showAlertModals('alertaValidaReasignaTarea','Error al cargar las empresas. Por favor intente nuevamente.');
                    
                }    
            },
            failure: function(response){
                showAlertModals('alertaValidaReasignaTarea','Error al realizar acción. Por favor informar a Sistemas.');
            }
    });
}

/*
* OnChange de select empresas (form reasignar tarea)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

$("#cmbEmpresaReasigna").on("change", function() {
    $("#cmbCiudadReasigna").html("<option value='-1' >Seleccione ciudad</option>");
    $("#cmbCiudadReasigna").attr('disabled','disabled');
    $("#cmbDepartReasigna").html("<option value='-1' >Seleccione departamento</option>");
    $("#cmbDepartReasigna").attr('disabled','disabled');
    $("#cmbEmpleadoReasigna").html("<option value='-1' >Seleccione empleado</option>");
    $("#cmbEmpleadoReasigna").attr('disabled','disabled');
    var strEmpresa = $("#cmbEmpresaReasigna").val();
    loadCiudad(strEmpresa);

});

/*
* Función que carga las ciudades (form reasignar tarea)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function loadCiudad(prefEmpresa,idCanton)
{
    var parametros = {};
    var strPrefEmpresa = prefEmpresa;
    var paramQuery = "?empresa="+strPrefEmpresa+"&page=1&start=0&limit=200";
    $("#cmbCiudadReasigna").html("<option value='-1' >Seleccione ciudad</option>");
    $.ajax({
            data :  parametros,
            url  :  url_ciudadPorEmpresa+paramQuery,
            type :  'post',
            success:  function (response) {
                if(response.total > 0)
                {
                    var arrayCiudades  = response.encontrados;
                    if(arrayCiudades.length > 0)
                    {
                        var listaCiudades     = "<option value='-1' >Seleccione ciudad</option>"; 
                        for(var i=0;i<arrayCiudades.length;i++)
                        {
                            listaCiudades += "<option value='"+arrayCiudades[i].id_canton+"'>"+arrayCiudades[i].nombre_canton+"</option>"; 
                        }
                        $("#cmbCiudadReasigna").html(listaCiudades);
                        $('#cmbCiudadReasigna').removeAttr('disabled');

                        if(typeof idCanton !== 'undefined')
                        {
                            $("#cmbCiudadReasigna").val(idCanton);
                        }
                    }
                    else
                    {
                        showAlertModals('alertaValidaReasignaTarea','Error al cargar las ciudades. Por favor intente nuevamente.');
                    }
                }
                else
                {
                    showAlertModals('alertaValidaReasignaTarea','Error al cargar las ciudades. Por favor intente nuevamente.');
                    
                }
                
            },
            failure: function(response){
                showAlertModals('alertaValidaReasignaTarea','Error al realizar acción. Por favor informar a Sistemas.');
            }
    });
}

/*
* OnChange de select ciudad (form reasignar tarea)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

$("#cmbCiudadReasigna").on("change", function() {
    $("#cmbDepartReasigna").html("<option value='-1' >Seleccione departamento</option>");
    $("#cmbDepartReasigna").attr('disabled','disabled');
    $("#cmbEmpleadoReasigna").html("<option value='-1' >Seleccione empleado</option>");
    $("#cmbEmpleadoReasigna").attr('disabled','disabled');
    var strEmpresa = $("#cmbEmpresaReasigna").val();
    var intCiudad = $("#cmbCiudadReasigna").val();
    loadDepartamentos(strEmpresa,intCiudad);
});

/*
* Función que carga los departamentos (form reasignar tarea)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function loadDepartamentos(prefEmpresa,idCanton,idDepartamento)
{
    var parametros = {};
    var strPrefEmpresa = prefEmpresa;
    var intIdCanton = idCanton;
    var paramQuery = "?id_canton="+intIdCanton+"&empresa="+strPrefEmpresa+"&page=1&start=0&limit=200";
    $("#cmbDepartReasigna").html("<option value='-1' >Seleccione departamento</option>");
    $.ajax({
            data :  parametros,
            url  :  url_departamentoPorEmpresaCiudad+paramQuery,
            type :  'post',
            success:  function (response) {
                if(response.total > 0)
                {
                    var arrayDepartamentos  = response.encontrados;
                    if(arrayDepartamentos.length > 0)
                    {
                        var listaDepartamentos     = "<option value='-1' >Seleccione departamento</option>"; 
                        for(var i=0;i<arrayDepartamentos.length;i++)
                        {
                            listaDepartamentos += "<option value='"+arrayDepartamentos[i].id_departamento+
                            "'>"+arrayDepartamentos[i].nombre_departamento+"</option>"; 
                        }
                        $("#cmbDepartReasigna").html(listaDepartamentos);
                        $('#cmbDepartReasigna').removeAttr('disabled');
                        if(typeof idDepartamento !== 'undefined')
                        {
                            $("#cmbDepartReasigna").val(idDepartamento);
                        }
                    }
                    else
                    {
                        showAlertModals('alertaValidaReasignaTarea','Error al cargar los departamentos. Por favor intente nuevamente.');
                    }
                }
                else
                {
                    showAlertModals('alertaValidaReasignaTarea','Error al cargar las departamentos. Por favor intente nuevamente.');
                    
                }
                
            },
            failure: function(response){
                showAlertModals('alertaValidaReasignaTarea','Error al realizar acción. Por favor informar a Sistemas.');
            }
    });
}

/*
* OnChange de select departamentos (form reasignar tarea)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

$("#cmbDepartReasigna").on("change", function() {
    $("#cmbEmpleadoReasigna").html("<option value='-1' >Seleccione empleado</option>");
    $("#cmbEmpleadoReasigna").attr('disabled','disabled');
    var strEmpresa = $("#cmbEmpresaReasigna").val();
    var intCiudad = $("#cmbCiudadReasigna").val();
    var intDepartamento = $("#cmbDepartReasigna").val();
    loadEmpleados(strEmpresa,intCiudad,intDepartamento);
});

/*
* Función que carga los empleados (form reasignar tarea)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function loadEmpleados(prefEmpresa,idCanton,idDepartamento,idEmpleado)
{
    var parametros = {};
    var strPrefEmpresa = prefEmpresa;
    var intIdCanton = idCanton;
    var intIdDepartamento = idDepartamento;
    var paramQuery = "?id_canton="+intIdCanton+"&empresa="+strPrefEmpresa+"&id_departamento="
                    +intIdDepartamento+"&departamento_caso=&page=1&start=0&limit=25";
    $("#cmbEmpleadoReasigna").html("<option value='-1' >Seleccione empleado</option>");
    $.ajax({
            data :  parametros,
            url  :  url_empleadoPorDepartamentoCiudad+paramQuery,
            type :  'post',
            success:  function (response) {
                if(response.myMetaData.boolSuccess == 1)
                {
                    var arrayEmpleados  = response.result.encontrados;
                    if(arrayEmpleados.length > 0)
                    {
                        var listaEmpleados     = "<option value='-1' >Seleccione empleado</option>"; 
                        for(var i=0;i<arrayEmpleados.length;i++)
                        {
                            listaEmpleados += "<option value='"+arrayEmpleados[i].id_empleado+
                            "'>"+arrayEmpleados[i].nombre_empleado+"</option>"; 
                        }
                        $("#cmbEmpleadoReasigna").html(listaEmpleados);
                        $('#cmbEmpleadoReasigna').removeAttr('disabled');
                        if(typeof idEmpleado !== 'undefined')
                        {
                            $("#cmbEmpleadoReasigna").val(idEmpleado);
                        }
                    }
                    else
                    {
                        showAlertModals('alertaValidaReasignaTarea','Error al cargar los empleados. Por favor intente nuevamente.');
                    }
                }
                else
                {
                    showAlertModals('alertaValidaReasignaTarea',response.myMetaData.message);
                    
                }   
            },
            failure: function(response){
                showAlertModals('alertaValidaReasignaTarea','Error al realizar acción. Por favor informar a Sistemas.');
            }
    });
}

/*
* Check de respuesta inmediata (form reasignar tarea)
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

$('#chxRespuestaInmediata').change(function () 
{    
    var idDetalle = $("#intIdDetalleReasigna").val();
    var bollIsChecked = this.checked;
    $.ajax({
        data :  {intDetalleId : idDetalle},
        url  :  url_datosRespuestaTarea,
        type :  'post',
        success:  function (response) {
            if(typeof response.strUsuarioRespuesta !== 'undefined' && response.strUsuarioRespuesta != '')
            {
                strPrefijoEmpresa       = response.strPrefijoEmpresa;
                intCiudad               = response.intIdCiudad;
                intIdDepartamento       = response.intDepartamento;
                strUsuarioRespuesta     = response.strUsuarioRespuesta;
                strPrefijoSession       = response.strPrefijoSession;
                intCiudadSession        = response.intCiudadSession;
                intDepartamentoSession  = response.intDepartamentoSession;

                if(bollIsChecked) {
                    $("#cmbEmpresaReasigna").val(strPrefijoEmpresa);
                    loadCiudad(strPrefijoEmpresa,intCiudad);
                    loadDepartamentos(strPrefijoEmpresa,intCiudad,intIdDepartamento);
                    loadEmpleados(strPrefijoEmpresa,intCiudad,intIdDepartamento,strUsuarioRespuesta);  
                }else{
                    $("#cmbEmpresaReasigna").val(strPrefijoSession);
                    loadCiudad(strPrefijoSession,intCiudadSession);
                    loadDepartamentos(strPrefijoSession,intCiudadSession,intDepartamentoSession);
                    loadEmpleados(strPrefijoSession,intCiudadSession,intDepartamentoSession);  
                }                
            }
            else
            {
                return false;
            }   
        },
        failure: function(response){
            showAlertModals('alertaValidaReasignaTarea','Error al realizar acción. Por favor informar a Sistemas.');
        }
    });
});

/*
* Función que muestra los documentos cargados a una tarea
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function presentarDocumentosTareas(idDetalle,strTareaIncAudMant,e)
{
    //reiniciar elementos modals
    $("#tbodyMostrarArchivos").html('');
    e.setAttribute("disabled", "disabled");
    setTimeout(function(){e.removeAttribute("disabled", "");}, 3000);

    var listArchivos     = ""; 
    var strTextNoData= "<tr><td colspan='4' style='text-align:center;'>La tarea seleccionada no posee archivos adjuntos.</td></tr>";
    $.ajax({
        data : { idTarea : idDetalle, strTareaIncAudMant : strTareaIncAudMant },
        url  :  url_verifica_casos,
        type :  'post',
        success:  function (response) {
            response = JSON.parse(response);
            if(response.total > 0)
            {
                var strQuery = "?idTarea="+idDetalle+"&strTareaIncAudMant="+strTareaIncAudMant+"&page=1&start=0&limit=1000"; 
                $.ajax({
                    data : {},
                    url  :  url_documentosCaso+strQuery,
                    type :  'post',
                    success:  function (response) {
                        if(response.total > 0)
                        {
                            e.setAttribute("id", "verArchivo_"+idDetalle);
                            var arrArchivos  = response.encontrados;
                            
                            for(var i=0;i<arrArchivos.length;i++)
                            {
                                listArchivos += "<tr>";
                                 var strNameFile = '<span style="width:250px" class="hint--bottom-left hint--default hint--medium'+
                                                ' hint--rounded btnVerArchivoDigital" aria-label="'+arrArchivos[i].ubicacionLogica+'" >'+
                                                '<div style="width: 250px; white-space: nowrap; text-overflow: ellipsis; '+
                                                'overflow: hidden;">'+arrArchivos[i].ubicacionLogica+'</div>'+
                                                '</span>';
                                listArchivos += "<td style='width: 250px;'>"+strNameFile+"</td>";                
                                listArchivos += "<td>"+arrArchivos[i].usrCreacion+"</td>";
                                listArchivos += "<td>"+arrArchivos[i].feCreacion+"</td>";
                                
                                var rutaFisica = arrArchivos[i].linkVerDocumento;
                                var posicion = rutaFisica.indexOf('/public'); 
                                var ruta = rutaFisica.substring(posicion,rutaFisica.length);
                                ruta  = ruta.replace(/"/g, '\\"');
                                strOpción    = '<span class="hint--bottom-left hint--default hint--medium'+
                                                            ' hint--rounded btnVerArchivoDigital" aria-label="Ver Archivo Digital" >'+
                                                            '<button  type="button" class="btn btn-default btn-xs" '+
                                                            'onClick="javascript:window.open(\''+ruta+'\', \'_blank\');">'+
                                                            '<span class="glyphicon glyphicon-search"></span>'+
                                                            '</button>'+
                                                            '</span>';
                                if(arrArchivos[i].boolEliminarDocumento)
                                {
                                    storeDocumentosCaso  = JSON.stringify(arrArchivos[i]);
                                    storeDocumentosCaso  = storeDocumentosCaso.replace(/"/g, '\\"');
                                    strOpción    += '<span class="hint--bottom-left hint--default hint--medium'+
                                                            ' hint--rounded btnVerArchivoDigital" aria-label="Eliminar Archivo Digital" >'+
                                                            '<button  type="button" class="btn btn-default btn-xs" '+
                                                            "onClick='javascript:confirmaEliminarArchivoAdjunto(\""+storeDocumentosCaso+
                                                            "\",\"verArchivo_"+idDetalle+"\",this);'>"+
                                                            '<span class="glyphicon glyphicon-trash"></span>'+
                                                            '</button>'+
                                                            '</span>';
                                }
                                listArchivos += "<td style='text-align:center;'>"+strOpción+"</td>";
                                listArchivos += "</tr>";
                            }
                            $("#tbodyMostrarArchivos").html(listArchivos);
                            $('#modalVerArchivos').modal('show');    
                        }
                        else
                        {
                            //showModalsMessageFinTarea("error","La tarea seleccionada no posee archivos adjuntos.",'Mensaje');
                            $("#tbodyMostrarArchivos").html(strTextNoData);
                            $('#modalVerArchivos').modal('show');                             
                        }
                                                  
                    },
                    failure: function(response){
                        showModalsMessageFinTarea('error','Error al realizar acción. Por favor informar a Sistemas.');
                    }
                });
            }
            else
            {
                //showModalsMessageFinTarea("error","La tarea seleccionada no posee archivos adjuntos.",'Mensaje');
                $("#tbodyMostrarArchivos").html(strTextNoData);
                $('#modalVerArchivos').modal('show');                 
            }    
        },
        failure: function(response){
            showModalsMessageFinTarea('error','Error al realizar acción. Por favor informar a Sistemas.');
        }
    });    
}

/*
* Confirmación para eliminar archivo de tarea
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function confirmaEliminarArchivoAdjunto(storeDocumentosCaso,elementSearch,e)
{ 
    var objDocumento = JSON.parse(storeDocumentosCaso);
    objDocumento  = JSON.stringify(objDocumento);
    objDocumento  = objDocumento.replace(/"/g, '\\"');
    
    e.setAttribute("disabled", "disabled");
    setTimeout(function(){e.removeAttribute("disabled", "");}, 3000);

    strOnclick="javascript:eliminarArchivoAdjunto(\""+objDocumento+"\",\""+elementSearch+"\");";
    //inicar parametros
    var objParametrosSms = {tittle:'Confirmación',btnOkOnClick:strOnclick,btnCancelOnClick:'',
                              btnCancelText:'No', btnOkText:'Si',tipo:'success',mensaje:"Se eliminará el documento. Desea continuar?",btnCancel:'S'};
    showModalMensajeCustom(objParametrosSms);
}

/*
* Función que elimina el archivo de una tarea
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function eliminarArchivoAdjunto(storeDocumentosCaso,elementSearch)
{
    var objDocumento = JSON.parse(storeDocumentosCaso);
    
    var strOnclick = "$('#"+ elementSearch+"').click();";
    $.ajax({
        data : {id:objDocumento.idDocumento},
        url  :  url_eliminar_adjunto,
        type :  'post',
        success:  function (response) {
            $('#modalVerArchivos').modal('hide'); 
            response = JSON.parse(response);
            if(response.status == 'OK')
            {
                showModalMensajeCustom({tipo:'success',mensaje: response.message, btnOkOnClick:strOnclick});
            }
            else
            {
                strOnclick = "$('#modalVerArchivos').modal('show');";
                showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje: response.message, btnOkOnClick:strOnclick,btnOkText:'Cerrar'});
                //showAlertModals('alertaValidaEliminarArchivo',response.message);   
            }                           
        },
        failure: function(response){
            $('#modalVerArchivos').modal('hide'); 
            strOnclick = "$('#modalVerArchivos').modal('show');";
            showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje: 'Error al realizar acción. Por favor informar a Sistemas.',
                                     btnOkOnClick:strOnclick,btnOkText:'Cerrar'});
            //showAlertModals('alertaValidaEliminarArchivo','Error al realizar acción. Por favor informar a Sistemas.');
        }
    });

}

/*
* Función que levanta el modals para realizar la carga de archivo
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function subirMultipleAdjuntosTarea(tarea)
{
    var objTarea = JSON.parse(tarea);
    //reiniciar elementos modals
    $('#IdTarea').val('');

    var strInputFile = '<div class="entry input-group upload-input-group" style="margin-bottom: 10px; width: 100%;">'+
                            '<input class="form-control form-control-sm load-files-task" name="archivos[]" type="file"'+
                            'style="width: 80% !important;" onchange="enableAddFile(this)">'+
                            '<button class="btn btn-upload btn-success btn-add btn-load-file" type="button" style="margin-left: 10px;" disabled>'+ 
                            '<i class="fa fa-plus" style="margin: 0px !important;"> </i> </button>'+
                        '</div>';
    $('#divInputFiles').html(strInputFile);
    $('#IdTarea').val(objTarea.idDetalle);
    strClick = "uploadFilesTareas('formFileTarea')";
    $('#btnCargarArchivo').attr('onclick',strClick);   
    $('#modalCagarArchivos').modal('show'); 
}

/*
* Función que guarda los archivos cargados
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function uploadFilesTareas(idForm)
{
    var objData = new FormData($("#"+idForm)[0]);

    var fileInput = $('input[name^="archivos"]');

    if(fileInput.length>0 && fileInput[0].files.length > 0){
        if($('#IdTarea').val() != ''){       
            $.ajax({
                data :  objData,
                cache: false,
                contentType: false,
                processData: false,
                url  :  url_multipleFileUpload,
                type :  'post',
                beforeSend: function () {
                    $('#btnCargarArchivo').hide();
                    $('#btnLoadingCargarArchivo').show();
                },                
                success:  function (response) {
                    $('#modalCagarArchivos').modal('hide');
                    response = JSON.parse(response);
                    if(response.success)
                    {
                        $('#btnCargarArchivo').show();
                        $('#btnLoadingCargarArchivo').hide();
                        showModalsMessageFinTarea("success",response.respuesta,'Mensaje');              
                    }
                    else
                    {
                        $('#btnCargarArchivo').show();
                        $('#btnLoadingCargarArchivo').hide();
                        showModalsMessageFinTarea("error",response.respuesta,'Mensaje');
                    }   
                },
                failure: function(response){
                    $('#modalCagarArchivos').modal('hide');
                    $('#btnCargarArchivo').show();
                    $('#btnLoadingCargarArchivo').hide();
                    showModalsMessageFinTarea("error","Error al realizar acción. Por favor informar a Sistemas.",'Mensaje');
                }
            });
        }else
        {
            $('#btnCargarArchivo').show();
            $('#btnLoadingCargarArchivo').hide();
            showAlertModals('alertaValidaCargarArchivo','Error al realizar acción. Por favor informar a Sistemas.');
        }
    }else
    {
        $('#btnCargarArchivo').show();
        $('#btnLoadingCargarArchivo').hide();
        showAlertModals('alertaValidaCargarArchivo','No existen archivos para subir.');
    }

}

/*
* Función para customizar los inputs de tipo file del subir archivos tareas
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

$(function () {  
    $(document).on('click', '.btn-add', function (e) {  
        e.preventDefault();  
        var controlForm = $('.controls:first'),  
            currentEntry = $(this).parents('.entry:first'),  
            newEntry = $(currentEntry.clone()).appendTo(controlForm);  
        newEntry.find('input').val('');
        newEntry.find('button').attr('disabled','disabled'); 
        controlForm.find('.entry:not(:last) .btn-add')  
            .removeClass('btn-add').addClass('btn-remove')  
            .removeClass('btn-success').addClass('btn-danger')  
            .html('<span class="fa fa-trash" style="margin: 0px !important;"> </span>');  
    }).on('click', '.btn-remove', function (e) {  
        $(this).parents('.entry:first').remove();  
        e.preventDefault();  
        return false;  
    });  
});  

/*
* Función para deshabilitar boton de agregar nuevo input para cargar archivo
* @author Fernando Lopez <filopez@telconet.ec>
* @version 1.0 02-12-2021
* @since 1.0
*/

function enableAddFile(e)
{
    elNexButton = e.nextElementSibling;
    if(elNexButton.classList.contains("btn-add"))
    {
       elNexButton.removeAttribute("disabled");   
    }
}

/**
 * Función que permite levantar el modal de mensaje de alertas
 * @author Fernando Lopez <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */

 function showModalsMessage(tipo,mensaje,tittle)
 {
     $('#modalMensajeTareas').modal('show');
     var tittleModal = (typeof tittle !== 'undefined')?tittle:"Mensaje";
     $('#tittelMessage').text(tittleModal);
     if(tipo=='success')
     {        
         $('#alertaMensajeTareasDiv').css('display', 'none');
         $('#successMensajeTareasDiv').css('display', 'block');
         $('#successMensajeTareas').text(mensaje);
     }else
     {
         $('#successMensajeTareasDiv').css('display', 'none');
         $('#alertaMensajeTareasDiv').css('display', 'block');
         $('#alertaMensajeTareas').text(mensaje);
     }
 }
 
 /**
  * Función que permite mostrar alerta en el modals
  * @author Fernando Lopez <filopez@telconet.ec>
  * @version 1.0 16-11-2021
  * @since 1.0
  */
 function showAlert(idAlert,idElement,mensaje)
 {   
     $('#'+idAlert).addClass('alert-danger');
     $('#'+idAlert).removeClass('alert-success');
     $('#'+idAlert).removeClass('alert-warning');
     $('#'+idAlert).html("<strong>"+mensaje+"</strong>");
     $('#'+idAlert).show();
     $('#'+idAlert).fadeIn();
     $('#'+idAlert).slideDown();
     //for(var i=0;i<objElements.length;i++)
     //{
         $('#'+idElement).addClass('has-error');
 
     //}
     return false;
 }
 
 function showAlertModals(idAlert,mensaje)
 {   
     $('#'+idAlert).addClass('alert-danger');
     $('#'+idAlert).removeClass('alert-success');
     $('#'+idAlert).removeClass('alert-warning');
     $('#'+idAlert).html("<strong>"+mensaje+"</strong>");
     $('#'+idAlert).show();
     $('#'+idAlert).fadeIn();
     $('#'+idAlert).slideDown();
     setTimeout(function () {
         $('#'+idAlert).hide();
     }, 8000);
     return false;
 }

/**
 * Función que muestra el mensaje de final de tarea.
 * @author Fernando Lopez <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */

 function showModalsMessageFinTarea(tipo,mensaje,tittle,strClick,isSmshtm)
 {
     $('#modalFinTarea').modal('show');
     var tittleModal = (typeof tittle !== 'undefined')?tittle:"Mensaje";
     var strOnClick = (typeof strClick !== 'undefined')?strClick:"";
     var boolIsSmsHtml = (typeof isSmshtm !== 'undefined')?isSmshtm:false;
     $('#tittelMensajeFinTarea').text(tittleModal);
     if(tipo=='success')
     {        
         $('#alertaMensajeFinTareasDiv').css('display', 'none');
         $('#successMensajeFinTareasDiv').css('display', 'block');
         if(boolIsSmsHtml)
         {
            $('#successMensajeFinTareas').html(mensaje);
         }else
         {
            $('#successMensajeFinTareas').text(mensaje);
         }
         
     }else
     {
         $('#successMensajeFinTareasDiv').css('display', 'none');
         $('#alertaMensajeFinTareasDiv').css('display', 'block');
         if(boolIsSmsHtml)
         {
            $('#alertaMensajeFinTareas').html(mensaje);
         }
         else
         {
            $('#alertaMensajeFinTareas').text(mensaje);
         }
     }
     $("#btnMensajeFinTarea").attr('onclick',strOnClick)
 }

 /**
 * Función que muestra mensajes personalizados.
 * @author Fernando Lopez <filopez@telconet.ec>
 * @version 1.0 13-12-2021
 * @since 1.0
 */

  function showModalMensajeCustom(parametros)
  {  
      $('#modalSmsCustom').modal('show');
      //Iniciar parametros
      var tittleModal = (typeof parametros.tittle !== 'undefined')?parametros.tittle:"Mensaje";
      var strBtnOkOnClick = (typeof parametros.btnOkOnClick !== 'undefined')?parametros.btnOkOnClick:"";
      var strBtnCancelOnClick = (typeof parametros.btnCancelOnClick !== 'undefined')?parametros.btnCancelOnClick:"";
      var strBtnCancelText = (typeof parametros.btnCancelText !== 'undefined')?parametros.btnCancelText:"Cerrar";
      var strBtnOkText = (typeof parametros.btnOkText !== 'undefined')?parametros.btnOkText:"OK";
      var tipo = (typeof parametros.tipo !== 'undefined')?parametros.tipo:"error";
      var isBtnCancel = (typeof parametros.btnCancel !== 'undefined')?parametros.btnCancel:"N";
      $('#btnSmsCustomCancel').hide();
      $('#tittelSmsCustom').text(tittleModal);
      $('#btnSmsCustomCancel').text(strBtnCancelText);
      $('#btnSmsCustomOk').text(strBtnOkText);
      
      if(tipo=='success')
      {        
          $('#alertaSmsCustomDiv').css('display', 'none');
          $('#successSmsCustomDiv').css('display', 'block');
          $('#successSmsCustom').text(parametros.mensaje);
          if(isBtnCancel == 'S')
          {
              $("#btnSmsCustomOk").removeClass('btn-default');
              $("#btnSmsCustomOk").addClass('btn-primary');
              $('#btnSmsCustomCancel').show();
              $("#btnSmsCustomOk").attr('onclick',strBtnCancelOnClick);
          }
      }else
      {

         $("#btnSmsCustomOk").removeClass('btn-primary');
         $("#btnSmsCustomOk").addClass('btn-default');
          $('#successSmsCustomDiv').css('display', 'none');
          $('#alertaSmsCustomDiv').css('display', 'block');
          $('#alertaSmsCustom').text(parametros.mensaje);
      }
      $("#btnSmsCustomOk").attr('onclick',strBtnOkOnClick);
  }

 /**
 * Función que retorna un arraglo de horas
 * @author Fernando Lopez <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */

function buildHoras()
{
    var arrayHoras = [];
    var strHora = '';
    var strMin = '';

    for (var i=0; i<24; i++){
        strHora = (i<10)?'0'+i:i;
        for (var j=0; j<60; j++){
            strMin = (j<10)?'0'+j:j;
            arrayHoras.push(strHora+":"+strMin);
        }       
    }
    return arrayHoras;
}

 /**
 * Función que crea el popover para ver mas detalle de parrafos
 * @author Fernando Lopez <filopez@telconet.ec>
 * @version 1.0 06-12-2021
 * @since 1.0
 */

function showPopover(e,body){
    var $element = $('#'+e);
    $element.popover({
        html: true,
        placement: 'top',
        container: $('body'), 
        content: '<button type="button" onClick="$(\'#'+e+'\').popover(\'hide\')" class="close">&times;</button><div class="text-popover" style="font-size: 12px;">'+
        body+'</div><div  class="modal-footer" style="padding: 5px 0 0 !important;"><button type="button" onClick="$(\'#'+e+'\').popover(\'hide\')" class="btn btn-default btn-xs">Cerrar</button></div>'
    }).data('bs.popover')
    .tip()
    .addClass('tarea-popover')
    .css({"max-width": "480px"});
}


/**
 * Función que permite validar la fecha de las tareas
 * @author Fernando Lopez <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */

function validarFechaTareaReprogramada(fechaInicio, horaInicio, fechaFin, horaFin) 
{         
    if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 0) 
    {
        showModalsMessage("error","No puede finalizar la tarea, aun no se cumple la fecha de planificacion","Alerta");
        return false;
    } 
    else 
    {
        //son fechas iguales por tanto se valida la diferencia por horas
        if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 2) 
        {     
            if (validate_fechaMayorQue(horaInicio, horaFin, 'hora') === 0) 
            {
                showModalsMessage("error","No puede finalizar la tarea, aun no se cumple la fecha de planificacion","Alerta");
                return false;
            }
            return 1;
        } 
        else
        {
            return 1;
        }
    }
}

/**
 * Función que permite validar fecha hora
 * @author Fernando Lopez <filopez@telconet.ec>
 * @version 1.0 11-11-2021
 * @since 1.0
 */

function validate_fechaMayorQue(fechaInicial,fechaFinal,tipo)
{        
    if (tipo === 'fecha') 
    {
        valuesStart = fechaInicial.split("-");
        valuesEnd   = fechaFinal.split("-");
        
        //Si los años son diferentes
        if (parseInt(valuesStart[2]) !== parseInt(valuesEnd[2]))
        {
            if (parseInt(valuesStart[2]) > parseInt(valuesEnd[2])) // Si el año de programacion es mayor lanza el mensaje
            {
                return 0;
            }
            else //Si el año de promagramacion ya paso por ende se puede finalizar la tarea
            {
                return 1;
            }
        }
        //Si el año es igual se valida meses y luego por dias
        else if (parseInt(valuesStart[1]) > parseInt(valuesEnd[1]))
        {
            return 0;
        }
        else if (parseInt(valuesStart[1]) < parseInt(valuesEnd[1]))//Significa que el dia de ejecucion ya paso y puede ser finalizado
        {  
            return 1;
        }
        else if (parseInt(valuesStart[0]) > parseInt(valuesEnd[0]))
        {
            return 0;
        }
        else //Se valida la diferencia entre dias
        {
            if (parseInt(valuesStart[0]) === parseInt(valuesEnd[0]))
            {
                return 2;
            }
            else if (parseInt(valuesStart[0]) < parseInt(valuesEnd[0]))
            {
                return 1;
            }
        }
    } 
    else 
    {
        valuesStart = fechaInicial.split(":");
        valuesEnd   = fechaFinal.split(":");

        if (parseInt(valuesStart[0]) > parseInt(valuesEnd[0]))
            return 0;

        else if (parseInt(valuesStart[0]) === parseInt(valuesEnd[0])) //es la misma hora
        {
            if (parseInt(valuesStart[1]) > parseInt(valuesEnd[1]))
            {
                return 0;
            }
            else
            {
                return 1;
            }
        }
        else
        {
            return 1;
        }
    }
}