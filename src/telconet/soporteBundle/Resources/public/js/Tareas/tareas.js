/**
 * Archivo que permite gestionar las opciones del modulo Soporte/Tareas
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 06-05-2022
 * @since 1.0
 */

 var gridTareas;
 var fechaIni;
 var fechaFin;
 var valorAsignacion   = "empleado";
 var cuadrillaAsignada = "S";
 var seleccionaHal     = false;
 var nIntentos = 0;
 var tipoHal;
 var finesTarea  = null;
 var finesTareaReasig  = null;
 var nombreOlt = '';
 var url_listGridTarea = 'grid';
 var dataTable = null;
 var date = new Date();
 var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
 var start = moment().add(-30, 'days');//moment().startOf('month');
 var end  =  moment();  //moment().endOf('month');
 var departamentoSession='';
 var cliente = '';
 var feSolicitadaDesde = '';
 var feSolicitadaHasta = '';
 var limitSearchFilter = 4;
 var indexPageTable = 0;
 var myMenuContactoCliente = null;
 //arreglo de columnas del grid para customizar los indices y mostrar/ocultar comuna
 var arrayClm =[{id:"acciones",tittle:"Acciones","checked":true},{id:"strEmpresaTarea",tittle:"Emp.","checked":true},{id:"clientes",tittle:"Pto. Cliente","checked":true},{id:"numero_caso",tittle:"Numero Caso","checked":true},
                        {id:"estado_caso",tittle:"Estado del Caso","checked":true},{id:"nombre_proceso",tittle:"Nombre Proceso","checked":true},{id:"numero_tarea_Padre",tittle:"No. Tarea Padre","checked":true},{id:"numero_tarea",tittle:"No. Tarea","checked":true},
                        {id:"nombre_tarea",tittle:"Tarea","checked":true},{id:"observacion",tittle:"Observacion","checked":true},{id:"ref_asignado_nombre",tittle:"Responsable Asignado","checked":true},{id:"feSolicitada",tittle:"Fecha Ejecucion","checked":true},
                        {id:"actualizadoPor",tittle:"Actualizado Por","checked":true},{id:"feTareaHistorial",tittle:"Fecha Estado","checked":true},{id:"estado",tittle:"Estado","checked":true},{id:"seFactura",tittle:"Se Factura","checked":true},
                        {id:"esHal",tittle:"Es Hal","checked":true},{id:"atenderAntes",tittle:"Atender Antes","checked":true},{id:"duracionTarea",tittle:"Tiempo Transcurrido","checked":true}]

 //fix para omitir las tareas en estado finalizada al cargar la vista
 var estadosTareasDefault = 'Asignada,Pausada,Cancelada,Anulada,Reprogramada,Aceptada';
 var arrayEstadosDefault = estadosTareasDefault.split(',');
 //Variables para registro de contacto del cliente
 var registroContacto = null;
 var registroContactoModificado = null;
 var heightRegistroContactos = 150.0;
 var mostrarRegistroContactos = false;
 var heightWinFinalizarTarea = 0.0;
 var strObservacionRegistroContactos = '';
 if( strPuntoPersonaSession != '' )
 {
     cliente             = strPuntoPersonaSession;
     departamentoSession = strDepartamentoSession;
 }
 
 
 //Datarangepicker que se usa para seleccionar la fecha de consulta de información tarea
 $('input[name="feSolicitada"]').daterangepicker({
     autoUpdateInput: false,
     startDate: start,
     endDate: end,
     ranges   : 
     {
       'Hoy'             : [moment(), moment()],
       'Ultimos 7 Días'  : [moment().subtract(6, 'days'), moment()],
       'Este mes'        : [moment().startOf('month'), moment().endOf('month')],
       'El mes Anterior' : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
     },
     locale: {
         format: 'YYYY/MM/DD',
         "customRangeLabel": "Rango de fechas",
         cancelLabel: 'Limpiar',
         applyLabel: 'Aplicar',
         autoApply : true
     }
 });
 
 $('input[name="feSolicitada"]').on('apply.daterangepicker', function(ev, picker) {
     $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
 });
 
 $('input[name="feSolicitada"]').on('cancel.daterangepicker', function(ev, picker) {
     $(this).val('');
 });
 
 
 $('input[name="feFinalizada"]').daterangepicker({
     autoUpdateInput: false,
     ranges   : 
     {
       'Hoy'             : [moment(), moment()],
       'Ultimos 7 Días'  : [moment().subtract(6, 'days'), moment()],
       'Este mes'        : [moment().startOf('month'), moment().endOf('month')],
       'El mes Anterior' : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
     },
     locale: {
         format: 'YYYY/MM/DD',
         "customRangeLabel": "Rango de fechas",
         cancelLabel: 'Limpiar',
         applyLabel: 'Aplicar',
         autoApply : true
     }
 });
 
 $('input[name="feFinalizada"]').on('apply.daterangepicker', function(ev, picker) {
     $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
 });
 
 $('input[name="feFinalizada"]').on('cancel.daterangepicker', function(ev, picker) {
     $(this).val('');
 });
 
 $('#feSolicitada').val('');
 if(intNumeroActividad =='')
 {
     //$('#feSolicitada span').html(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
     $('#feSolicitada').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
 }
 
 if($('#div_feSolicitada').css('display') !== 'none' && $('#feSolicitada').val() !== '')
 {
     feSolicitadaDesde  = $('#feSolicitada').data('daterangepicker').startDate.format('YYYY-MM-DD')+'T00:00';
     feSolicitadaHasta  = $('#feSolicitada').data('daterangepicker').endDate.format('YYYY-MM-DD')+'T00:00';
 }
 $('#hid_opcion_busqueda').val('N'); //fix para firefox
 var queryAllTask = ""; //variable que permite consultar todas las tareas del perfil del user
 if(strOrigen == 'tareasPorEmpleado' && intNumeroActividad =='')
 {
    queryAllTask = 'S';
 }
 var valueGet = {"cliente":cliente,"asignado":"","strOrigen":strOrigen,'departamentoSession':departamentoSession,
                 "estado":"Todos","feSolicitadaDesde":feSolicitadaDesde,"feSolicitadaHasta":feSolicitadaHasta,
                 "feFinalizadaDesde":"","feFinalizadaHasta":"","numeroActividad":intNumeroActividad,
                 "queryAllTask":queryAllTask,"page":1,"start":0,"limit":6000}; 
 var paramGet = buildDataGet(valueGet); 
 
//Actualizar el indicador de tareas
Ext.Ajax.request({
    url: url_indicadorTareas,
    method: 'post',
    params: {
        personaEmpresaRol : intPersonaEmpresaRolId
    },
    success: function(response) {
        var text = Ext.decode(response.responseText);
        $("#spanTareasDepartamento").text(text.tareasPorDepartamento);
        $("#spanTareasPersonales").text(text.tareasPersonales);
        $("#spanCasosMoviles").text(text.cantCasosMoviles);
    }
});
 //llamamos a plugin de bootstrap datetimepicker
 $(function () {
     $('#datetimepickerEjecReasigna').datetimepicker({
         format: 'YYYY-MM-DD',
         minDate: today
     });
 
     $('#datetimepickerEjecReprogramar').datetimepicker({
         format: 'YYYY-MM-DD',
         minDate: today
     });
 });
 
 $(document).ready(function() {
 
     $('[data-toggle="tooltip"]').tooltip();
 
     clearInterval(
         setInterval( 
             function () {  //buscaPendientes();
                         }, 60000 
                         )
     );
 
     //define configuracion de ventana modals
     $('.modal-content').resizable({
         minHeight: 300,
         minWidth: 300
     });
 
     //configura a arrastrable la ventana modals
     $('.modal-dialog').draggable();
     $('.modal-dialog-edit-content').draggable({
         handle: ".modal-header"
       });
     
     //Mejora para customizar los indices de las columnas del grid, si se desea cambiar la posición de las columnas 
     //considerar texto th(html), columnas dataTable(js) y el arrego arrayClm 
     
     function getColumnClass(typeClass)
     {
        if(typeClass == 'dt-left')
        { 
            columnClass = [ getIdArray(arrayClm,'clientes'),getIdArray(arrayClm,'numero_caso'),getIdArray(arrayClm,'estado_caso'),
                            getIdArray(arrayClm,'nombre_proceso'),getIdArray(arrayClm,'numero_tarea_Padre'),
                            getIdArray(arrayClm,'numero_tarea'),getIdArray(arrayClm,'nombre_tarea'),
                            getIdArray(arrayClm,'observacion'),getIdArray(arrayClm,'ref_asignado_nombre'),
                            getIdArray(arrayClm,'actualizadoPor')];
        }
        if(typeClass == 'dt-center')
        {
            columnClass = [getIdArray(arrayClm,'strEmpresaTarea'),getIdArray(arrayClm,'feSolicitada'),
                            getIdArray(arrayClm,'feTareaHistorial'),getIdArray(arrayClm,'estado'),
                            getIdArray(arrayClm,'seFactura'),getIdArray(arrayClm,'esHal'),
                            getIdArray(arrayClm,'atenderAntes'),getIdArray(arrayClm,'duracionTarea'),
                            getIdArray(arrayClm,'acciones')];
        }
        return columnClass;
     }     
     var targetsColumnLeft = getColumnClass("dt-left");
     var targetsColumnCenter =  getColumnClass("dt-center");
       
     //Define los detalles del cuadro de asignaciones
     gridTareas =   $('#gridTareas').DataTable( {    
             "ajax": {
                 "method": "GET",
                 "url": url_listGridTarea+paramGet,
                 "dataSrc": "encontrados",
                 error: function(XMLHttpRequest, textStatus, errorThrown) {
                     console.log(textStatus);
                    if (errorThrown=='Forbidden') {
                      location.reload();
                    }
                  },
                 complete: function (data) {
                    arrayResponse = data.responseJSON;
                    //validación para limpiar la fecha cuando se consulte toda las tareas del perfil
                    if(typeof arrayResponse != 'undefined')
                    {
                        if(typeof arrayResponse.showAllTask != 'undefined' && 
                                arrayResponse.showAllTask === 'S')
                        {
                            $('#feSolicitada').val('');
                        }
                        else if(typeof arrayResponse.showAllTask != 'undefined' && 
                            arrayResponse.showAllTask === 'N' && strOrigen == 'tareasPorEmpleado' &&
                            $('#hid_opcion_busqueda').val() == 'N' && $('#feSolicitada').val() == '')
                        {
                            //se setea la fecha por defecto en caso que en el refresh pase el límite de la consulta
                            $('#feSolicitada').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
                        }
                    }
                    if(indexPageTable > 0)
                    {   
                        $('#gridTareas').DataTable().page(indexPageTable).draw('page');
                    }
                } 
             },
             "language": {
                 "lengthMenu": "Muestra _MENU_ filas por página",
                 "zeroRecords": "No hay datos para mostrar",
                 "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                 "infoEmpty": "No hay información disponible",
                 "infoFiltered": "(filtrado de _MAX_ total filas)",
                 "search": "Buscar:",
                 "loadingRecords": "Cargando datos...",
             },
             "columns": [ 
                 {"data": "acciones",
                 "title":"Acciones",
                 "orderable": false,
                 "render": function (data, type, full, meta)
                        {
                            var strDatoRetorna = '<span>';

                            var permiso     = '{{ is_granted("ROLE_197-1237") }}';
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);
                            
                            //fix para optener la observación con el id elemento de html y no pasarlo en la data de acciones (crea conflicto)
                            var objTarea  = JSON.stringify(full);
                            var fullTmp = JSON.parse(objTarea);
                            fullTmp.accionTarea = "";
                            fullTmp.observacion = '';
                            fullTmp.textObservacion = 'obs-div-popover-'+fullTmp.id_detalle+fullTmp.numero_tarea;

                            //fix para eliminar html de los valores
                            fullTmp.atenderAntes = (fullTmp.atenderAntes !== 'NO')?'SI':fullTmp.atenderAntes;
                            fullTmp.esHal = (fullTmp.esHal !== 'NO')?'SI':fullTmp.atenderAntes;

                            tareasEjecucion  = JSON.stringify(fullTmp);
                            tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                            // Ejecutar Tarea
                            if (boolPermiso && fullTmp.action6 !== "icon-invisible")
                            {
                                fullTmp.accionTarea = "iniciar";
                                tareasEjecucion  = JSON.stringify(fullTmp);
                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                ' hint--rounded btnEjecucionTarea" aria-label="Ejecutar Tarea" >'+
                                '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                "onClick='javascript:mostrarEjecucionTarea(\""+tareasEjecucion+"\")'>"+
                                '<span class="glyphicon glyphicon-play"></span>'+
                                '</button>'+
                                '</span>';
                            }

                            if (boolPermisoRenaudarPausar )
                            {
                                // Pausar Tarea
                                if(fullTmp.action13 !== "icon-invisible"){
                                    fullTmp.accionTarea = "pausar";
                                    strBanderaFinalizarInformeEjecutivo = fullTmp.strBanderaFinalizarInformeEjecutivo
                                    tareasEjecucion  = JSON.stringify(fullTmp);
                                    tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                    strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnEjecucionTarea" aria-label="Pausar Tarea" >'+
                                    '<button type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:mostrarEjecucionTarea(\""+tareasEjecucion+"\")'>"+
                                    '<span class="glyphicon glyphicon-pause"></span>'+
                                    '</button>'+
                                    '</span>';
                                }

                                // Reanudar Tarea
                                if (fullTmp.action14 !== "icon-invisible")
                                {
                                    fullTmp.accionTarea = "reanudar";
                                    tareasEjecucion  = JSON.stringify(fullTmp);
                                    tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');
    
                                    strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnEjecucionTarea" aria-label="Reanudar Tarea" >'+
                                    '<button type="button" class="btn btn-default btn-xs"  style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:mostrarEjecucionTarea(\""+tareasEjecucion+"\")'>"+
                                    '<span class="glyphicon glyphicon-step-forward"></span>'+
                                    '</button>'+
                                    '</span>';
                                }
                            }

                            permiso = '{{ is_granted("ROLE_197-584") }}';
                            boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            // Reprogramar Tarea
                            if (boolPermiso && fullTmp.strBanderaFinalizarInformeEjecutivo !== "N" && fullTmp.action1 !== "icon-invisible")
                            {
                                
                                fullTmp.accionTarea = "reprogramar";
                                tareasEjecucion  = JSON.stringify(fullTmp);
                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnEjecucionTarea" aria-label="Reprogramar Tarea" >'+
                                    '<button type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:mostrarReprogramarTarea(\""+tareasEjecucion+"\",this)'>"+
                                    '<span class="glyphicon glyphicon-retweet"></span>'+
                                    '</button>'+
                                    '</span>';
                            }

                            permiso     = '{{ is_granted("ROLE_197-585") }}';
                            boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            // Cancelar Tarea
                            if (boolPermiso && fullTmp.strBanderaFinalizarInformeEjecutivo !== "N" && fullTmp.action2 !== "icon-invisible")
                            {
                                fullTmp.accionTarea = "cancelada";
                                tareasEjecucion  = JSON.stringify(fullTmp);
                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnCancelarTarea" aria-label="Cancelar Tarea" >'+
                                    '<button type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:validaCancelarTarea(\""+tareasEjecucion+"\")'>"+
                                    '<span class="glyphicon glyphicon-remove-circle"></span>'+
                                    '</button>'+
                                    '</span>';
                            }

                            permiso = '{{ is_granted("ROLE_197-38") }}';
                            boolPermiso = (typeof permiso === 'undefined') ? false : (permiso ? true : false);

                            // Finalizar Tarea
                            if (boolPermiso && fullTmp.strBanderaFinalizarInformeEjecutivo !== "N" && fullTmp.action3 !== "icon-invisible")
                            {
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                ' hint--rounded btnFinalizarTarea" aria-label="Finalizar Tarea" >'+
                                '<button type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                "onClick='javascript:validaFinalizarTarea(\""+tareasEjecucion+"\",this)'>"+
                                '<span class="glyphicon glyphicon-stop"></span>'+
                                '</button>'+
                                '</span>';
                            }

                            // ReasignarTarea
                            if (boolPermiso && fullTmp.action4 !== "icon-invisible")
                            {
                                fullTmp.accionTarea = "reasignar";
                                strBanderaFinalizarInformeEjecutivo = fullTmp.strBanderaFinalizarInformeEjecutivo
                                tareasEjecucion  = JSON.stringify(fullTmp);
                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');

                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                ' hint--rounded btnReasignarTarea" aria-label="Reasignar Tarea" >'+
                                '<button type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                "onClick='javascript:reasignarTarea(\""+tareasEjecucion+"\",this)'>"+
                                '<span class="glyphicon glyphicon-dashboard"></span>'+
                                '</button>'+
                                '</span>';
                            }

                            // Ingresar Seguimiento
                            if (boolPermiso && fullTmp.action7 !== "icon-invisible")
                            {
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnIngresoSeguimiento" aria-label="Ingresar Seguimiento" >'+
                                    '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:mostrarIngresoSeguimiento(\""+tareasEjecucion+"\")'>"+
                                    '<span class="glyphicon glyphicon-list-alt"></span>'+
                                    '</button>'+
                                    '</span>';
                            }

                            // Rechazar Tarea
                            if (boolPermiso && fullTmp.strBanderaFinalizarInformeEjecutivo !== "N" && fullTmp.action9 !== "icon-invisible")
                            {
                                fullTmp.accionTarea = "rechazada";
                                strBanderaFinalizarInformeEjecutivo = fullTmp.strBanderaFinalizarInformeEjecutivo
                                tareasEjecucion  = JSON.stringify(fullTmp);
                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                ' hint--rounded btnFinalizarTarea" aria-label="Rechazar Tarea" >'+
                                '<button type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                "onClick='javascript:validaCancelarTarea(\""+tareasEjecucion+"\")'>"+
                                '<span class="glyphicon glyphicon-remove-circle"></span>'+
                                '</button>'+
                                '</span>';
                            }

                            // Ver Seguimiento
                            if (boolPermiso && fullTmp.action8 !== "icon-invisible")
                            {
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnSeguimiento" aria-label="Ver Seguimiento" >'+
                                    '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:mostrarSeguimiento(\""+fullTmp.id_detalle+"\")'>"+
                                    '<span class="glyphicon glyphicon-search"></span>'+
                                    '</button>'+
                                    '</span>';
                            }

                            // Cargar Archivo
                            if (boolPermiso && fullTmp.action10 !== "icon-invisible")
                            {
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                ' hint--rounded btnCargarArchivo" aria-label="Cargar Archivo" >'+
                                '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                "onClick='javascript:subirMultipleAdjuntosTarea(\""+fullTmp.id_detalle+"\")'>"+
                                '<span class="glyphicon glyphicon-open-file"></span>'+
                                '</button>'+
                                '</span>';
                            }

                            // Ver Archivos
                            if (boolPermiso && fullTmp.action11 !== "icon-invisible")
                            {
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                                ' hint--rounded btnVerArchivo" aria-label="Ver Archivos" >'+
                                                '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 2px 2px 0;" '+
                                                "onClick='javascript:presentarDocumentosTareas("+fullTmp.id_detalle+
                                                ",\""+fullTmp.strTareaIncAudMant+"\",this)'>"+
                                                '<span class="glyphicon glyphicon-eye-open"></span>'+
                                                '</button>'+
                                                '</span>';
                            }

                            //Crear Tarea
                            if (boolPermiso && fullTmp.action12 !== "icon-invisible")
                            {
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnSeguimiento" aria-label="Crear Tarea" >'+
                                    '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:validateAgregarSubTarea(\""+tareasEjecucion+"\",this)'>"+
                                    '<span class="glyphicon glyphicon-plus-sign"></span>'+
                                    '</button>'+
                                    '</span>';
                            }

                            //Anular Tarea
                            if (boolPermiso && fullTmp.action15 !== "icon-invisible")
                            {
                                fullTmp.accionTarea = "anulada";
                                tareasEjecucion  = JSON.stringify(fullTmp);
                                tareasEjecucion  = tareasEjecucion.replace(/"/g, '\\"');
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnSeguimiento" aria-label="Anular Tarea" >'+
                                    '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:validaCancelarTarea(\""+tareasEjecucion+"\")'>"+
                                    '<span class="glyphicon glyphicon-minus-sign"></span>'+
                                    '</button>'+
                                    '</span>';
                            }  

                            //ReintentoTareaSysCloud
                            if (boolPermiso && fullTmp.boolRenviarSysCloud)
                            {
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnTareaSysCloud" aria-label="Reintento Tarea Sys Cloud-Center" >'+
                                    '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:confirmarTareaSysCloud(\""+tareasEjecucion+"\")'>"+
                                    '<span class="glyphicon glyphicon-refresh"></span>'+
                                    '</button>'+
                                    '</span>';
                            }
                            
                            // Confirmar Ip Servicio
                            permiso             = fullTmp.permiteConfirIpSopTn;
                            var casoPerteneceTN     = fullTmp.casoPerteneceTN;
                            var tieneProgConfIp     = fullTmp.strTieneConfirIpServ;
                            var ultimaMillaSoporte  = fullTmp.ultimaMillaSoporte;
                            if(permiso && casoPerteneceTN && tieneProgConfIp === 'NO' 
                                &&  (ultimaMillaSoporte === 'FO' || ultimaMillaSoporte === 'RAD' 
                                || ultimaMillaSoporte === 'UTP'))
                            {   
                                 strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnConfIpServicio" aria-label="Confirmar enlace" >'+
                                    '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:confirmaIpServicioSoporte(\""+tareasEjecucion+"\")'>"+
                                    '<span class="glyphicon glyphicon-saved"></span>'+
                                    '</button>'+
                                    '</span>';   
                            }

                            // Validar enlace
                            permiso = fullTmp.permiteValidarEnlaceSopTn;
                            var tipoCasoEnlace = fullTmp.tipoCasoEnlace;
                            if (permiso && casoPerteneceTN  && tieneProgConfIp === 'NO' 
                                && (ultimaMillaSoporte === 'FO' || ultimaMillaSoporte === 'RAD' 
                                    || ultimaMillaSoporte === 'UTP')  && tipoCasoEnlace != 'Backbone') 
                            {
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnValidaServicio" aria-label="Validar Enlace" >'+
                                    '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:confirmarServicioSoporte(\""+tareasEjecucion+"\")'>"+
                                    '<span class="glyphicon glyphicon-info-sign"></span>'+
                                    '</button>'+
                                    '</span>';
                            }

                            //Permite crear kml ?
                            var permiteCrearKml = fullTmp.permiteCrearKml;
                            var etadoTarea  = fullTmp.estado;
                            var tipoMedioId = fullTmp.tipoMedioId;
                            if ( (etadoTarea === 'Aceptada' || etadoTarea === 'Pausada') && (   tipoMedioId === 1  || tipoMedioId === 2
                                    || tipoMedioId === 104 || tipoMedioId === 107 ) && boolPermisoCrearKml === 'S' 
                                    && (tipoCasoEnlace != 'Backbone' || isEmpty($tipoCasoEnlace)) 
                                     && (permiteCrearKml == null || permiteCrearKml != 'S')) 
                            {
                                strDatoRetorna    += '<span class="hint--right hint--default hint--medium'+
                                    ' hint--rounded btnPermiteKml" aria-label="Permite Crear KML" >'+
                                    '<button  type="button" class="btn btn-default btn-xs" style="margin: 0 5px 3px 0;" '+
                                    "onClick='javascript:confirmaCrearKml(\""+tareasEjecucion+"\")'>"+
                                    '<span class="glyphicon glyphicon-random"></span>'+
                                    '</button>'+
                                    '</span>';
                            }

                            return strDatoRetorna; 
                        }                
                 },
                 {"data": "strEmpresaTarea",
                 "title":"Emp"
                 },
                 {"data": "clientes",
                 "title": "Pto. Cliente",
                 "render": function (data, type, full, meta)
                             {
                                 var login = full.clientes;
                                 return '<a href="#" onclick="setPuntoSesionByLogin(\''+login+'\');">'+login+'</a>';
                             }
                 
                 },
                 {"data": "numero_caso",
                 "title": "Numero Caso"
                 },
                 {"data": "estado_caso",
                 "title": "Estado del Caso"
                 },
                 {"data": "nombre_proceso",
                 "title": "Nombre Proceso"
                 }, 
                 {"data": "numero_tarea_Padre",
                 "title": "No. Tarea Padre"
                 },
                 {"data": "numero_tarea",
                 "title": "No. Tarea"
                 },
                 {"data": "nombre_tarea",
                 "title": "Tarea"
                 },
                 {"data": "observacion",
                 "title": "Observacion",
                 "render": function ( data, type, row ) {
                                var idPopover = 'obs-div-popover-'+row.id_detalle+row.numero_tarea;
                                var idPopoverA = 'a-popover-'+row.id_detalle+row.numero_tarea;
                                var valHeight = (data.indexOf("<b>Información Adicional</b>") !== -1)?'170px':'32px';
                                var tareaAutom = (data.indexOf("Tarea Automática: Se solicita") !== -1)?true:false;
                                return (type === 'display' && data.length > 91 && !tareaAutom ) ?
                                "<div id="+idPopover+" class=\"td_obs_tarea\" style=\"height: "+valHeight+"; padding-left: 10px;overflow: hidden;text-overflow: ellipsis;\">"
                                    +data+"</div><div style=\"text-align: right;\"><a id="+idPopoverA+" class=\"obs-popover\" title=\"Observación Tarea\" onClick=\"javascript:showPopover('"+idPopover+"','"+idPopoverA+"');\" data-toggle=\"popover\">  leer más</a></div><script>showPopover('"+idPopover+"','"+idPopoverA+"');</script>" :
                                '<div id='+idPopover+' class=\"td_obs_tarea\" style="padding-left: 10px;">'+data+'</div>' ;
                            }
                 },
                 {"data": "ref_asignado_nombre",
                 "title": "Responsable Asignado"
                 },
                 {"data": "feSolicitada",
                 "title": "Fecha Ejecucion"
                 },
                 {"data": "actualizadoPor",
                 "title": "Actualizado Por"
                 },
                 {"data": "feTareaHistorial",
                 "title": "Fecha Estado"
                 },
                 {
                     "orderable" : false,
                     "data"      : "estado",
                     "title"     : "Estado",
                     "render": function (data, type, full, meta)
                             {
                                     var strDatoRetorna   = "";
     
                                     var estadoTarea      = full.estado;
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
                                     else if (estadoTarea == 'Reprogramada')
                                     {
                                         colorEstadoTarea = "warning";
                                     }
                                     else if (estadoTarea == 'Aceptada')
                                     {
                                         colorEstadoTarea = "info";
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
                 {"data": "seFactura",
                 "title": "Se Factura"
                 },
                 {"data": "esHal",
                 "title": "Es Hal"
                 },
                 {"data": "atenderAntes",
                 "title": "Atender Antes"
                 },
                 {"data": "duracionTarea",
                 "title": "Tiempo<br/>Transcurrido"
                 }                 
             ],
             "columnDefs":
             [
                 {"className": "dt-left", "targets":  targetsColumnLeft},
                 {"className": "dt-center","targets": targetsColumnCenter}
             ],
             select: { 
                 style: 'os', 
                 selector: 'td:first-child' 
             }, 
             order: [ 
                 [getIdArray(arrayClm,'feSolicitada'), 'desc'] 
             ],
             "paging":true,
             scrollY: 500,
             scrollX: true,
             autoWidth: true,
             scrollCollapse: true,
             preDrawCallback: function() {
                 $('div.dataTables_scrollHeadInner thead tr#filterboxrow th').each(function() {
                     if($(this).text() !== '')
                     { 
                         $("#divLoaderDetalleTareas").show();
                         if($(this).index() == getIdArray(arrayClm,'observacion') || $(this).index() == getIdArray(arrayClm,'acciones'))
                         {
                             $(this).html('<input id="input' + $(this).index() + '" type="text" style="color:black; height: 25px;"  placeholder="' + $(this).text() + '" disabled />');
                         } else if($(this).index() == getIdArray(arrayClm,'strEmpresaTarea') || $(this).index() == getIdArray(arrayClm,'numero_tarea_Padre') || 
                                   $(this).index() == getIdArray(arrayClm,'numero_tarea') || $(this).index() == getIdArray(arrayClm,'estado') || 
                                   $(this).index() == getIdArray(arrayClm,'seFactura') || $(this).index() == getIdArray(arrayClm,'esHal') || 
                                   $(this).index() == getIdArray(arrayClm,'atenderAntes') || $(this).index() == getIdArray(arrayClm,'duracionTarea') )
                         {
                             $(this).html('<input id="input' + $(this).index() + '" type="text" style="color:black; height: 25px; width: 99%"  placeholder="' + $(this).text() + '" />');
                         }
                         else
                         { 
                             $(this).html('<input id="input' + $(this).index() + '" type="text" style="color:black; height: 25px;"  placeholder="' + $(this).text() + '" />');
 
                         }
                         $(this).on('keyup change', function() {
                            var idInput = $(this).children('input')[0].id;
                            var val;
                            //var valIndex = $(this).index();
                            var valIndex = idInput.replace('input','');
                            valIndex = Number(valIndex);
                            val = $('#input' + valIndex).val();
        
                            gridTareas.column(valIndex).search(val).draw();
                         });
                     }
                     
                 });
             } 
         } )
         .on('preXhr.dt', function ( e, settings, data ) {
                    $("#divLoaderDetalleTareas").show();
                    indexPageTable = $('#gridTareas').DataTable().page.info().page;
                    $('#btnRefreshTable').attr('disabled','disabled');
             } )
         .on('xhr.dt', function ( e, settings, json, xhr ) {
            $("#divLoaderDetalleTareas").hide(); 
            $('#btnRefreshTable').removeAttr('disabled');                       
         } )
     ;
 
     dataTable = $('#gridTareas').DataTable();
     if (boolOcultarColumnaEmpresa)
     {
         dataTable.columns([getIdArray(arrayClm,'strEmpresaTarea')]).visible(false);
         arrayClm[getIdArray(arrayClm,'strEmpresaTarea')].checked = false;   
     }

     //construir html de check que se presenta en el popover de las columnas
     var htmlCheckColumn = '';
     for (const x in arrayClm) {
        var setDisabled = '';
        if(arrayClm[x].id == 'acciones' || arrayClm[x].id == 'observacion')
        {
            setDisabled = 'disabled';
        }
        htmlCheckColumn += '<div><input type="checkbox" id=chbx_'+arrayClm[x].id+' class="chbxColumnGrid" name="chbxColumnGrid" value="'
        +arrayClm[x].id+'" '+setDisabled+' >&nbsp;&nbsp;<label>'+arrayClm[x].tittle+'</label></div>';
     }
      
     //Evento para marcar con un color el registro seleccionado del datatable
     $('#gridTareas tbody').on('click', 'tr', function () {
        $('#gridTareas tbody tr').removeClass("tr-table-select");
        $(this).addClass("tr-table-select");
     });
    
     //Añadir bottom mostrar/ocultar y refresh en la cabecera del datatable
     var buttomColumn = '<span class="hint--top hint--default hint--rounded" aria-label="Mostrar/Ocultar Columnas" style="margin-left: 10px;" >'+
                    '<button id="btnShowColumn" type="button" class="btn btn-default" data-toggle="popover" onclick=""></button> </span>';
     var buttomRefresh = '<span class="hint--top hint--default hint--rounded" aria-label="Refresh" style="margin-left: 10px;" >'+
                    '<button id="btnRefreshTable" type="button" class="btn btn-default" data-toggle="popover" '+
                    'onclick="javascript:gridTareas.ajax.url(url_listGridTarea+paramGet).load();"></button> </span>'
     $button = $('<div style="text-align: right;">'+buttomRefresh+buttomColumn+'</div> ');
     $("#gridTareas_length, .dataTables_length").after($button);

     // construir popover de check columnas
     buildPopover('btnShowColumn',htmlCheckColumn);

     //evento chequear columnas del popover
     $(document).on('change', '.chbxColumnGrid', function() {
        if(this.checked) {
            dataTable.columns([getIdArray(arrayClm,this.value)]).visible(true);
            arrayClm[getIdArray(arrayClm,this.value)].checked = true; 
        }else
        {
            dataTable.columns([getIdArray(arrayClm,this.value)]).visible(false);
            arrayClm[getIdArray(arrayClm,this.value)].checked = false; 
        } 
     });
 
     //evento mostrar columnas de datatable chequeadas
     $('#btnShowColumn').on('shown.bs.popover', function () {
        for (const x in arrayClm) {
            $.each($("input[name='chbxColumnGrid']"), function(){                    
                if($(this).val() == arrayClm[x].id && arrayClm[x].checked)
                {
                    $(this).prop("checked","checked");
                }
            });
        }
     
     })

     //evento para cerrar los popovers
     $('html').on('click', function(e) {
       if (typeof $(e.target).data('original-title') == 'undefined' &&
          !$(e.target).parents().is('.popover.in') && e.target.id != 'btn_cmb_estadosTareas'
               && e.target.id != 'span_cmb_estadosTareas') {
            $('[data-original-title]').popover('hide');
            if ($('.tarea-popover').css('display') === 'block' )
            {
                //fix para escenarios cuando el popover esta en show y ya no existe 
                //el elemento data- que lo levanto
                $(".tarea-popover").remove();   
            }
       }
     });

     //eliminar espacios en blanco de número de tarea
     $("#txtTareaPadre,#txtActividad").keyup(function(){              
        var ta      =    $(this);
        letras      =   ta.val().replace(/\s+/g, "");
        ta.val(letras);
     });
     
     //inicializar filtros de busquedas
     getEmpresas('cmbEmpresaBusqueda','S');
     getTareas('cmb_tarea','S');
     getCiudades('cmbCiudadDestino','S');
     getCiudadesOrigen('cmbCiudadOrigen','S');
     getDepartamentoOrigen('cmb_departamentoOrigen','S');
     getListaProcesos('cmb_listaProcesos','S');
     getEstadosTareas();
     $('#feFinalizada').val('');
     $('#btn_filtros').click();
     if(boolNoVisualizacionECUCERT){
         $('#div_checkboxECUCERT').css('display','none');
     }
     //set width dropdown filters
     setWidthDropDown('width_txtActividad','ul-drw-list');

 } );
 
 //#######################################################
 //############## GET DATA FILTROS DE BUSQUEDA ###########
 //#######################################################
 
 //mostrar modals de filtros
 function seleccionarFiltros()
 {   $('.chbxFiltros').prop("checked",false);
     $('#chbxAllFiltros').prop("checked",false);
     $.each($("input[name='chbxFiltros']"), function(){    
         if($('#div_'+$(this).val()).css('display') == 'block' ) 
         {
             $(this).prop("checked","checked");
         }            
     });
     $('#modalAgresgarFiltros').modal('show');
 }
 
 //agregar filtros de busquedas
 function agregarFiltros()
 {
     var arrayFilterSelect = [];
     $.each($("input[name='chbxFiltros']:checked"), function(){                    
         arrayFilterSelect.push($(this).val());
     });
     if(arrayFilterSelect.length >0)
     {   $('.div_filter').css('display','none');
         arrayFilterSelect.forEach((filtro, index) => {
             $('#div_'+filtro).css('display','block');
         });
         $('#modalAgresgarFiltros').modal('hide');
     }
     else
     {
         showAlertModals("alertaValidaFiltros","Debe seleccionar al menos un filtro para realizar la consulta de tarea");
     }
     //limpiarFiltros();
 }
 
 //chechear todos los filtros de búsqueda
 $("#chbxAllFiltros").change(function() {
     if(this.checked) {
         $('.chbxFiltros').prop("checked","checked");
     }else
     { 
         $('.chbxFiltros').prop("checked",false);
     }
 });
 
 //eliminar all check en caso que esten seleccionados todos
 $('.chbxFiltros').change(function() {
     if( $('#chbxAllFiltros').is(":checked")) {    
         $('#chbxAllFiltros').prop("checked",false);
     }
     //cmb_cliente,txtActividad,txtTareaPadre,txtCaso
     if(this.checked && $(this).val() === 'cmb_departamento')
     {
         $('#chbxEmpresa').prop("checked","checked");
     }  
     if(this.checked && $(this).val() !== 'cmb_cliente' && $(this).val() !== 'txtActividad' 
             && $(this).val() !== 'txtTareaPadre' && $(this).val() !== 'txtCaso'
             && $(this).val() !== 'feSolicitada' && $(this).val() !== 'feFinalizada')
     {
         $('#chbxFeSolicitada').prop("checked","checked");
     }
 });
 
 //resetear filtros
 function resetearFiltros()
 {
     $('.div_filter').css('display','none');
     $('#div_cmb_estadosTareas,#div_feSolicitada,#div_txtActividad,#div_txtCaso').css('display','block');
     //$('#feSolicitada').val('');
     //$('#feFinalizada').val('');
     $('.chbxFiltros,#chbxAllFiltros').prop("checked",false);
     $('#chbxFeSolicitada,#chbxEstado,#chbxNumeroTarea,#chbxNumeroCaso').prop("checked","checked");
 }
 
 function limpiarFiltros()
 {
     $('#txt_cmb_cliente,#cmb_cliente').val('');
     $('#txt_cmb_tarea,#cmb_tarea').val('');
     $('#txtAsignado').val('');
     $('#txt_cmb_estadosTareas,#cmb_estadosTareas').val('');
     $('#feSolicitada').val('');
     $('#feFinalizada').val('');
     $('#txtActividad').val('');
     $('#txtCaso').val('');
     $('#cmbEmpresaBusqueda,#txt_cmbEmpresaBusqueda').val('');
     $('#txt_cmbCiudadDestino,#cmbCiudadDestino').val(''); 
     $('#txt_cmb_departamento,#cmb_departamento').val('');
     $('#txt_cmb_cuadrillas').val('');
     storeSeleccionCuadrillas.removeAll();
     $('#txt_cmbCiudadOrigen,#cmbCiudadOrigen').val('');
     $('#txt_cmb_departamentoOrigen,#cmb_departamentoOrigen').val('');
     $('#txtTareaPadre').val('');
     $('#txt_cmb_listaProcesos,#cmb_listaProcesos').val('');
     $('#btn_cmb_cuadrillas,#txt_cmb_cuadrillas,#txt_cmbEmpresaBusqueda,#btn_cmbEmpresaBusqueda').removeAttr('disabled');  
     $('#txt_cmb_departamento,#btn_cmb_departamento').attr('disabled','disabled');
     $('#checkboxECUCERT').prop('checked',false);
     dataTable.clear().draw();
 }
 
 //consultar clientes 
 function searchCliente(e)
 {
     var idInput = 'txt_'+e; 
     var idCombo = e;
     var idInputVal = 'name_'+e;
     var idBtnDrw = 'btn_'+e;
     var idUlDrw = 'ul_'+e;
     var clienteTmp = $('#'+idInputVal).val();
     var queryCliente = $('#'+idInput).val();
     if(queryCliente.length < limitSearchFilter){
         return false;
     }
     if(queryCliente.indexOf(clienteTmp) == 0 && clienteTmp != '' && $( "#"+idUlDrw).hasClass( "loadList") == true)
     {
       showDropDown(idBtnDrw);
       autocompleteDropDown(idUlDrw,idInput)
       return false;
     }
     var parametros = {};
     var paramGet = buildDataGet({ nombre:queryCliente,estado: 'Activo'}); 
     var ulHtmClientes = '';
     showDropDown(idBtnDrw);                           
     $.ajax({
             data :  parametros,
             url  :  'getClientes'+paramGet,
             type :  'post',
             beforeSend: function () {
               resetDropDown(idUlDrw,idCombo,idInput,'load',idInputVal)
             },
             success:  function (response) {
                 var clientes   = [];
                 var clientesIds   = [];
                 if(response.total != '0')
                 {
                     var arrayClientes  = response.encontrados;
                     if(arrayClientes.length > 0)
                     {   for(var i=0;i<arrayClientes.length;i++)
                         {
                             clientes.push(arrayClientes[i].cliente);
                             clientesIds.push(arrayClientes[i].id_cliente);
                         }
                         $( "#"+idUlDrw).addClass( "loadList");
                         $('#'+idInputVal).val(queryCliente);
                         ulHtmClientes = buildListDropDown(idInput,idCombo,clientes,clientesIds);
                         $('#'+idUlDrw).html(ulHtmClientes);
                         showDropDown(idBtnDrw);
                         autocompleteDropDown(idUlDrw,idInput);
                     }
                     else
                     {
                       $('#'+idUlDrw).html(getDefaultLi());
                     }
                 }
                 else
                 {
                   $('#'+idUlDrw).html(getDefaultLi());                   
                 }
                 document.getElementById(idInput).focus();
             },
             failure: function(response){
               resetDropDown(idUlDrw,idCombo,idInput,'',idInputVal);
             }
     });
     
 }
 
 //consultar tareas
 function getTareas(e,init)
 {
     var idInput = 'txt_'+e; 
     var idCombo = e;
     var idBtnDrw = 'btn_'+e;
     var idUlDrw = 'ul_'+e;
     var idInputVal = 'name_'+e;
     var txtNametarea = $('#'+idInput).val();
     init = (typeof init !== 'undefined')?init:'';
     if(controlComboBox(e,init) == false)
     {
         return false;
     }   
     var parametros = {};
     var paramGet = buildDataGet({ nombre:txtNametarea,estado:'Activo',visible:'SI'});
     $.ajax({
         data :  parametros,
         url  :  url_gridTarea+paramGet,
         type :  'post',
         beforeSend: function () {
             resetDropDown(idUlDrw,idCombo,idInput,'load',idInputVal)
         },
         success:  function (response) {
             var tareas   = [];
             var tareasIds   = [];
             if(response.total > 0 && response.encontrados.length > 0)
             {
                 var arrTareas  = response.encontrados;
                 for(var i=0;i<arrTareas.length;i++)
                 {
                     tareas.push(arrTareas[i].nombre_tarea);
                     tareasIds.push(arrTareas[i].id_tarea);
                 }
                 $( "#"+idUlDrw).addClass( "loadList"); 
                 $('#'+idInputVal).val(txtNametarea);
                 ulHtmClientes = buildListDropDown(idInput,idCombo,tareas,tareasIds);
                 $('#'+idUlDrw).html(ulHtmClientes);
                 if(init !== 'S')
                 {
                     showDropDown(idBtnDrw);
                 }
                 autocompleteDropDown(idUlDrw,idInput);
                 if(init == 'S' && $('#'+idInput).val() == '')
                 {
                     $( "#"+idUlDrw).addClass( "loadListTotal");
                 } 
             }else
             {
                 $('#'+idUlDrw).html(getDefaultLi());
             }                                     
         },
         failure: function(response){
             resetDropDown(idUlDrw,idCombo,idInput,'',idInputVal);
         }
     });
 }
 
 //consultar empresas
 
 function getEmpresas(e,init)
 {
     var idInput = 'txt_'+e; 
     var idCombo = e;
     var idBtnDrw = 'btn_'+e;
     var idUlDrw = 'ul_'+e;
     var idInputVal = 'name_'+e;
     var txtNametarea = $('#'+idInput).val();
     init = (typeof init !== 'undefined')?init:'';
     if(controlComboBox(e,init) == false)
     {
         return false;
     }   
     var parametros = {};
     var paramGet = buildDataGet({ app: 'TELCOS'});
     $.ajax({
         data :  parametros,
         url  :  url_empresaPorSistema+paramGet,
         type :  'post',
         beforeSend: function () {
             resetDropDown(idUlDrw,idCombo,idInput,'load',idInputVal)
         },
         success:  function (response) {
             var empresas   = [];
             var empresasIds   = [];
             if(response.total > 0 && response.encontrados.length > 0)
             {
                 var arrEmpresas  = response.encontrados;
                 for(var i=0;i<arrEmpresas.length;i++)
                 {
                     empresas.push(arrEmpresas[i].nombre_empresa);
                     empresasIds.push(arrEmpresas[i].prefijo);
                 }
                 $( "#"+idUlDrw).addClass( "loadList"); 
                 $('#'+idInputVal).val(txtNametarea);
                 ulHtmClientes = buildListDropDown(idInput,idCombo,empresas,empresasIds);
                 $('#'+idUlDrw).html(ulHtmClientes);
                 if(init !== 'S')
                 {
                     showDropDown(idBtnDrw);
                 }
                 autocompleteDropDown(idUlDrw,idInput);
                 if(init == 'S' && $('#'+idInput).val() == '')
                 {
                     $( "#"+idUlDrw).addClass( "loadListTotal");
                 } 
             }else
             {
                 $('#'+idUlDrw).html(getDefaultLi());
             }                                     
         },
         failure: function(response){
             resetDropDown(idUlDrw,idCombo,idInput,'',idInputVal);
         }
     });
 }
 
 //evento al  seleccionar empresa
 $("#cmbEmpresaBusqueda" ).change(function () {
     if($("#cmbEmpresaBusqueda" ).val() !== '')
     {
         $('#txt_cmb_cuadrillas').val('');
         $('#txt_cmb_cuadrillas,#btn_cmb_cuadrillas').attr('disabled','disabled');
         storeSeleccionCuadrillas.removeAll();
         $('#txt_cmb_departamento,#cmb_departamento').val('');
         getDepartamento('cmb_departamento','S',$("#cmbEmpresaBusqueda" ).val());
         $('#txt_cmb_departamento,#btn_cmb_departamento').removeAttr('disabled');  
         
     }else{
         $('#btn_cmb_cuadrillas,#txt_cmb_cuadrillas').removeAttr('disabled');  
         $('#txt_cmb_departamento,#btn_cmb_departamento').attr('disabled','disabled');
     }        
 })
 
 //consultar ciudad destino
 function getCiudades(e,init)
 {
     var idInput = 'txt_'+e; 
     var idCombo = e;
     var idBtnDrw = 'btn_'+e;
     var idUlDrw = 'ul_'+e;
     var idInputVal = 'name_'+e;
     var txtNameCiudad = $('#'+idInput).val();
     if(controlComboBox(e,init) == false)
     {
         return false;
     }   
     var parametros = {};
     var paramGet = buildDataGet({query:txtNameCiudad,origen:'D',estado:'Activo'});
     $.ajax({
         data :  parametros,
         url  :  url_ciudadPorEmpresa+paramGet,
         type :  'post',
         beforeSend: function () {
             resetDropDown(idUlDrw,idCombo,idInput,'load',idInputVal)
         },
         success:  function (response) {
             var ciudades   = [];
             var ciudadesIds   = [];
             if(response.total > 0 && response.encontrados.length > 0)
             {
                 var arrCiudades  = response.encontrados;
                 for(var i=0;i<arrCiudades.length;i++)
                 {
                     ciudades.push(arrCiudades[i].nombre_cantonD);
                     ciudadesIds.push(arrCiudades[i].id_cantonD);
                 } 
                 $( "#"+idUlDrw).addClass( "loadList");
                 $('#'+idInputVal).val(txtNameCiudad); 
                 ulHtmClientes = buildListDropDown(idInput,idCombo,ciudades,ciudadesIds);
                 $('#'+idUlDrw).html(ulHtmClientes);
                 if(init !== 'S')
                 {
                     showDropDown(idBtnDrw);
                 }
                 autocompleteDropDown(idUlDrw,idInput); 
                 if(init == 'S' && $('#'+idInput).val() == '')
                 {
                     $( "#"+idUlDrw).addClass( "loadListTotal");
                 }  
             }else
             {
                 $('#'+idUlDrw).html(getDefaultLi());
             }                                    
         },
         failure: function(response){
             resetDropDown(idUlDrw,idCombo,idInput,'',idInputVal);
         }
     });
 }
 
 //consultar ciudad origen 
 function getCiudadesOrigen(e,init)
 {
     var idInput = 'txt_'+e; 
     var idCombo = e;
     var idBtnDrw = 'btn_'+e;
     var idUlDrw = 'ul_'+e;
     var idInputVal = 'name_'+e;
     var txtNameCiudad = $('#'+idInput).val();
     if(controlComboBox(e,init) == false)
     {
         return false;
     }   
     var parametros = {};
     var paramGet = buildDataGet({query:txtNameCiudad,origen:'O',estado:'Activo'});
     $.ajax({
         data :  parametros,
         url  :  url_ciudadPorEmpresa+paramGet,
         type :  'post',
         beforeSend: function () {
             resetDropDown(idUlDrw,idCombo,idInput,'load',idInputVal)
         },
         success:  function (response) {
             var ciudadesO   = [];
             var ciudadesOIds   = [];
             if(response.total > 0 && response.encontrados.length > 0)
             {
                 var arrCiudades  = response.encontrados;
                 for(var i=0;i<arrCiudades.length;i++)
                 {
                     ciudadesO.push(arrCiudades[i].nombre_cantonO);
                     ciudadesOIds.push(arrCiudades[i].id_cantonO);
                 }
                 $( "#"+idUlDrw).addClass( "loadList"); 
                 $('#'+idInputVal).val(txtNameCiudad);
                 ulHtmClientes = buildListDropDown(idInput,idCombo,ciudadesO,ciudadesOIds);
                 $('#'+idUlDrw).html(ulHtmClientes);
                 if(init !== 'S')
                 {
                     showDropDown(idBtnDrw);
                 }
                 autocompleteDropDown(idUlDrw,idInput); 
                 if(init == 'S' && $('#'+idInput).val() == '')
                 {
                     $( "#"+idUlDrw).addClass( "loadListTotal");
                 }  
             }else{
                 $('#'+idUlDrw).html(getDefaultLi());
             }                                     
         },
         failure: function(response){
             resetDropDown(idUlDrw,idCombo,idInput,'',idInputVal);
         }
     });
 }
 
 //consultar departamento
 function getDepartamento(e,init,empresa,canton)
 {
     var txtCanton = (typeof canton !== 'undefined')?canton:'';
     var reload = (typeof empresa !== 'undefined')?true:false;
     var txtEmpresa = (typeof empresa !== 'undefined')?empresa:
                     ((typeof $("#cmbEmpresaBusqueda" ).val() !== 'undefined')?$("#cmbEmpresaBusqueda" ).val():'');
     var idInput = 'txt_'+e; 
     var idCombo = e;
     var idBtnDrw = 'btn_'+e;
     var idUlDrw = 'ul_'+e;
     var idInputVal = 'name_'+e;
     var txtNameDepart = $('#'+idInput).val();
     if(controlComboBox(e,init,reload) == false)
     {
         return false;
     }   
     var parametros = {};
     var paramGet = buildDataGet({query:txtNameDepart,estado:'Activo',id_canton:txtCanton,empresa:txtEmpresa});
     $.ajax({
         data :  parametros,
         url  :  '/soporte/info_caso/getDepartamentosPorEmpresaYCiudad'+paramGet,
         type :  'post',
         beforeSend: function () {
             resetDropDown(idUlDrw,idCombo,idInput,'load',idInputVal)
         },
         success:  function (response) {
             var departamentos   = [];
             var departamentosIds   = [];
             if(response.total > 0 && response.encontrados.length > 0)
             {
                 var arrDepart  = response.encontrados;
                 for(var i=0;i<arrDepart.length;i++)
                 {
                     departamentos.push(arrDepart[i].nombre_departamento);
                     departamentosIds.push(arrDepart[i].id_departamento);
                 }
                 $( "#"+idUlDrw).addClass( "loadList"); 
                 $('#'+idInputVal).val(txtNameDepart);
                 ulHtmClientes = buildListDropDown(idInput,idCombo,departamentos,departamentosIds);
                 $('#'+idUlDrw).html(ulHtmClientes);
                 if(init !== 'S')
                 {
                     showDropDown(idBtnDrw);
                 }
                 autocompleteDropDown(idUlDrw,idInput)
                 if(init == 'S' && $('#'+idInput).val() == '')
                 {
                     $( "#"+idUlDrw).addClass( "loadListTotal");
                 }   
             }else
             {
                 $('#'+idUlDrw).html(getDefaultLi());
             }                                 
         },
         failure: function(response){
             resetDropDown(idUlDrw,idCombo,idInput,'',idInputVal);
         }
     });
 }
 
 //consultar departamento origen
 function getDepartamentoOrigen(e,init)
 {
     var idInput = 'txt_'+e; 
     var idCombo = e;
     var idBtnDrw = 'btn_'+e;
     var idUlDrw = 'ul_'+e;
     var idInputVal = 'name_'+e;
     var txtNameDepart = $('#'+idInput).val();
     if(controlComboBox(e,init) == false)
     {
         return false;
     }   
     var parametros = {};
     var paramGet = buildDataGet({origen:'O',query:txtNameDepart,estado:'Activo'});
     $.ajax({
         data :  parametros,
         url  :  url_departamentoPorEmpresaCiudad+paramGet,
         type :  'post',
         beforeSend: function () {
             resetDropDown(idUlDrw,idCombo,idInput,'load',idInputVal)
         },
         success:  function (response) {
             var departamentos   = [];
             var departamentosIds   = [];
             if(response.total > 0 && response.encontrados.length > 0)
             {
                 var arrDepart  = response.encontrados;
                 for(var i=0;i<arrDepart.length;i++)
                 {
                     departamentos.push(arrDepart[i].nombre_departamentoO);
                     departamentosIds.push(arrDepart[i].id_departamentoO);
                 } 
                 $( "#"+idUlDrw).addClass( "loadList"); 
                 $('#'+idInputVal).val(txtNameDepart);
                 ulHtmClientes = buildListDropDown(idInput,idCombo,departamentos,departamentosIds);
                 $('#'+idUlDrw).html(ulHtmClientes);
                 if(init !== 'S')
                 {
                     showDropDown(idBtnDrw);
                 }
                 autocompleteDropDown(idUlDrw,idInput);
                 if(init == 'S' && $('#'+idInput).val() == '')
                 {
                     $( "#"+idUlDrw).addClass( "loadListTotal");
                 }   
             }else{
                 $('#'+idUlDrw).html(getDefaultLi());
             }                                  
         },
         failure: function(response){
             resetDropDown(idUlDrw,idCombo,idInput,'',idInputVal);
         }
     });
 }
 
 //consultar lista de procesos
 function getListaProcesos(e,init)
 {
     var idInput = 'txt_'+e; 
     var idCombo = e;
     var idBtnDrw = 'btn_'+e;
     var idUlDrw = 'ul_'+e;
     var idInputVal = 'name_'+e;
     var txtNameProceso = $('#'+idInput).val();
     if(controlComboBox(e,init) == false)
     {
         return false;
     }    
     var parametros = {};
     var paramGet = buildDataGet({query:txtNameProceso});
     $.ajax({
         data :  parametros,
         url  :  url_procesos+paramGet,
         type :  'post',
         beforeSend: function () {
             resetDropDown(idUlDrw,idCombo,idInput,'load',idInputVal)
         },
         success:  function (response) {
             var procesos   = [];
             var procesosIds   = [];
             response = JSON.parse(response);
             if(response.total > 0 && response.registros.length > 0)
             {
                 var arrProcesos  = response.registros;
                 for(var i=0;i<arrProcesos.length;i++)
                 {
                     procesos.push(arrProcesos[i].nombreProceso);
                     procesosIds.push(arrProcesos[i].id);
                 }
                 
                 $( "#"+idUlDrw).addClass( "loadList");
                 $('#'+idInputVal).val(txtNameProceso); 
                 ulHtmClientes = buildListDropDown(idInput,idCombo,procesos,procesosIds);
                 $('#'+idUlDrw).html(ulHtmClientes);
                 if(init !== 'S')
                 {
                     showDropDown(idBtnDrw);
                 }
                 autocompleteDropDown(idUlDrw,idInput);
                 if(init == 'S' && $('#'+idInput).val() == '')
                 {
                     $( "#"+idUlDrw).addClass( "loadListTotal");
                 }   
             }else{
                 $('#'+idUlDrw).html(getDefaultLi());
             }                                       
         },
         failure: function(response){
             resetDropDown(idUlDrw,idCombo,idInput,'',idInputVal);
         }
     });
 }
 
 function controlComboBox(e,init,reload)
 {
     var idInput = 'txt_'+e; 
     var idBtnDrw = 'btn_'+e;
     var idUlDrw = 'ul_'+e;
     var idInputVal = 'name_'+e;
     var txtName = $('#'+idInput).val();
     var textNameTmp = $('#'+idInputVal).val();
     reload = (typeof reload !== 'undefined')?reload:false;
     //autocomplete de total de data
     if($("#"+idUlDrw).hasClass( "loadListTotal") === true && reload === false)
     {
         if(init !== 'S')
         {
             showDropDown(idBtnDrw);
         }
         autocompleteDropDown(idUlDrw,idInput)
         return false;
     }
     if(txtName.length < limitSearchFilter && init !== 'S'){
         //reset display block li
         if($("#"+idUlDrw).hasClass( "loadList") == true)
         {
             autocompleteDropDown(idUlDrw,idInput)
         }
         return false;
     }
     //
     if( txtName.indexOf(textNameTmp) == 0 && textNameTmp != '' &&
         $("#"+idUlDrw).hasClass( "loadList") == true && init !== 'S')
     {
       showDropDown(idBtnDrw);
       autocompleteDropDown(idUlDrw,idInput)
       return false;
     }
 
     return true;
 }
 
 //consultar estados
 function getEstadosTareas()
 {
     var parametros = {};
     var paramGet = buildDataGet({estado:'Activo'});
     //var smsError = "";
     var htmlEstados = '';
     $.ajax({
         data :  parametros,
         url  :  url_estadosTareas+paramGet,
         type :  'post',
         success:  function (response) {
             if(response.total > 0)
             {
                 var arrEstadosTareas  = response.encontrados;
                 for(var i=0;i<arrEstadosTareas.length;i++)
                 {
                     htmlEstados += '<div><input type="checkbox" id=chbx_'+arrEstadosTareas[i].nombre_estado_tarea+' class="chbxEstadosTareas" name="chbxEstadosTareas" value="'
                     +arrEstadosTareas[i].nombre_estado_tarea+'">&nbsp;&nbsp;<label>'+arrEstadosTareas[i].nombre_estado_tarea+'</label></div>';
                 }  
             }
             selectEstados('txt_cmb_estadosTareas',htmlEstados); 
             $('#btn_cmb_estadosTareas,#txt_cmb_estadosTareas').removeAttr('disabled');                                     
         },
         failure: function(response){
             //smsError = 'Error al realizar acción. Por favor informar a Sistemas.';
         }
     });
 }
 
 //evento chequear estados
 $(document).on('change', '.chbxEstadosTareas', function() {
    var estadosChecked = $('#cmb_estadosTareas').val();
     $('#cmb_estadosTareas').val('');
     $('#txt_cmb_estadosTareas').val('');
     var valueInput="";
     var valueTex = "";
     var delimitadorInput = "";
     var delimitadorText = "";
     if(this.checked) {
         if(this.value == 'Todos'){
             $('.chbxEstadosTareas').prop("checked","checked");
         }
     }else
     {
         if(this.value == 'Todos'){
             $('.chbxEstadosTareas').prop("checked",false);
         }
         // fix cuando esta marcado Tods y desmarca uno de los estados
         if($('#chbx_Todos').is(":checked"))
         {
            $("input[name='chbxEstadosTareas']").prop("checked",false);
            estadosChecked = estadosChecked.replace(this.value+',', ""); 
            if(estadosChecked !== "")
            {
                var arrayEstados = estadosChecked.split(',');
                for (var i=0; i<arrayEstados.length; i++) 
                { 
                    $.each($("input[name='chbxEstadosTareas']"), function(){                    
                        if($(this).val() == arrayEstados[i] && $(this).val() != 'Todos')
                        {
                            $(this).prop("checked","checked"); 
                        }
                    });
                }
            }
            
         }
     }
     $.each($("input[name='chbxEstadosTareas']:checked"), function(){                    
         if(valueInput != "")
         {
             delimitadorInput = ",";
         }
         if(valueTex != "")
         {
             delimitadorText = ", ";
         }
         valueInput = valueInput+delimitadorInput+$(this).val();
         valueTex = valueTex+delimitadorText+$(this).val();
     });
     $('#cmb_estadosTareas').val(valueInput);
     $('#txt_cmb_estadosTareas').val(valueTex);
 
 });
 
 //evento mostrar estados chequeados
 $('#txt_cmb_estadosTareas').on('shown.bs.popover', function () {
     var estadosChecked = $('#cmb_estadosTareas').val();
     if(estadosChecked !== "")
     {
         var arrayEstados = estadosChecked.split(',');
         for (var i=0; i<arrayEstados.length; i++) 
         { 
             $.each($("input[name='chbxEstadosTareas']"), function(){                    
                 if($(this).val() == arrayEstados[i])
                 {
                     $(this).prop("checked","checked");
                 }
             });
         }
     }
     
     })
 
 function dropDownEstados(e,action)
 {
     $('#btn_cmb_estadosTareas').removeAttr('onclick');
     if(action == 'show')
     {
         $('#txt_cmb_estadosTareas').popover('show');
         $('#btn_cmb_estadosTareas').attr("onclick","dropDownEstados('txt_cmb_estadosTareas','hide');");
     }else
     {
         $('#txt_cmb_estadosTareas').popover('hide');
         $('#btn_cmb_estadosTareas').attr("onclick","dropDownEstados('txt_cmb_estadosTareas','show');");
     }
     
 }
 
 //Se consultan cuadrillas para buscar tareas relacionadas a estas
 storeCuadrillas = new Ext.data.Store({
     total: 'total',
     pageSize: 200,
     proxy: {
         type: 'ajax',
         method: 'post',
         url: url_cuadrillas,
         reader: {
             type: 'json',
             totalProperty: 'total',
             root: 'encontrados'
         },
         extraParams: {
             estado: 'Eliminado'
         }
     },
     fields:
         [
             {name: 'id_cuadrilla', mapping: 'id_cuadrilla'},
             {name: 'nombre_cuadrilla', mapping: 'nombre_cuadrilla'}
         ]      
 }); 
 
 //Store Seleccion Cuadrillas
 storeSeleccionCuadrillas = Ext.create('Ext.data.Store', {
     fields   : ['id_cuadrilla','nombre_cuadrilla'],
     pageSize : 5,
     proxy    : {type: 'memory'}
 });
 
 //mostrar ventana de selección de cuadrilla
 function cargarSeleccionCuadrilla()
 {
     seleccionarCuadrillas({'storeCuadrillas'          : storeCuadrillas,
                             'storeSeleccionCuadrillas' : storeSeleccionCuadrillas},'S');
 }  
 
 //Función de consulta de tareas por filtros
 function buscar()
 {
     var mensaje = '';
     var login       = ($('#div_cmb_cliente').css('display') !== 'none')?$('#cmb_cliente').val():'';
     var numeroTarea = ($('#div_txtActividad').css('display') !== 'none')?$('#txtActividad').val():'';
     var tareaPadre  = ($('#div_txtTareaPadre').css('display') !== 'none')?$('#txtTareaPadre').val():'';
     var numeroCaso  = ($('#div_txtCaso').css('display') !== 'none')?$('#txtCaso').val():'';
     var feDesde = '';
     var feHasta = '';
     var feDesdeEst = '';
     var feHastaEst = '';
     
     var departamento      = ($('#div_cmb_departamento').css('display') !== 'none')?$('#cmb_departamento').val():'';
     var departamentoOrig  = ($('#div_cmb_departamentoOrigen').css('display') !== 'none')?$('#cmb_departamentoOrigen').val():'';
     var ciudadOrigen      = ($('#div_cmbCiudadOrigen').css('display') !== 'none')?$('#cmbCiudadOrigen').val():'';
     var proceso           = ($('#div_cmb_listaProcesos').css('display') !== 'none')?$('#cmb_listaProcesos').val():''; 
     var ciudadDestino     = ($('#div_cmbCiudadDestino').css('display') !== 'none')?$('#cmbCiudadDestino').val():'';
     var tarea             = ($('#div_cmb_tarea').css('display') !== 'none')?$('#cmb_tarea').val():'';
     var asignado          = ($('#div_txtAsignado').css('display') !== 'none')?$('#txtAsignado').val():'';
     var estadosChecked    = $('#cmb_estadosTareas').val();
     
     if( (login === "" || !login) && (numeroTarea === "" || !numeroTarea) && numeroCaso === "" && (tareaPadre === "" || !tareaPadre))
     {
         if($('#div_feSolicitada').css('display') !== 'none' && $('#feSolicitada').val() !== '')
         {
             feDesde  = $('#feSolicitada').data('daterangepicker').startDate.format('YYYY-MM-DD');
             feHasta  = $('#feSolicitada').data('daterangepicker').endDate.format('YYYY-MM-DD');
         }
         if($('#div_feFinalizada').css('display') !== 'none' && $('#feFinalizada').val() !== '')
         {
             feDesdeEst  = $('#feFinalizada').data('daterangepicker').startDate.format('YYYY-MM-DD');    
             feHastaEst  = $('#feFinalizada').data('daterangepicker').endDate.format('YYYY-MM-DD');
         }
 
         if(( feDesde === "" || feHasta === "") && ( feDesdeEst === "" || feHastaEst === ""))       
         {
             mensaje = "Debe escoger el rango de <b>Fecha Solicitada o Fecha Estado</b> para su búsqueda";
             showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:mensaje,btnOkText:'Cerrar'});
             return false;
         }
         else
         {
             if(getDiferenciaTiempo(new Date(feDesde), new Date(feHasta) ) > 31)
             {
                 mensaje = "Consulta permitida para Fecha Solicitida con un máximo de 30 dias";
                 showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:mensaje,btnOkText:'Cerrar'});
                 return false;
             }
             if(getDiferenciaTiempo(new Date(feDesdeEst), new Date(feHastaEst)) > 31)
             {
                 mensaje = "Consulta permitida para Fecha de Estado con un máximo de 30 dias";
                 showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:mensaje,btnOkText:'Cerrar'});
                 return false;
             }
             
         }
     }
     if(login !== "")
     {
         if($('#div_feSolicitada').css('display') !== 'none' && $('#feSolicitada').val() !== '')
         {
             feDesde  = $('#feSolicitada').data('daterangepicker').startDate.format('YYYY-MM-DD');
             feHasta  = $('#feSolicitada').data('daterangepicker').endDate.format('YYYY-MM-DD');
         }
         if($('#div_feFinalizada').css('display') !== 'none' && $('#feFinalizada').val() !== '')
         {
             feDesdeEst  = $('#feFinalizada').data('daterangepicker').startDate.format('YYYY-MM-DD');    
             feHastaEst  = $('#feFinalizada').data('daterangepicker').endDate.format('YYYY-MM-DD');
         }
     }
     feDesde = (feDesde !=='')?feDesde+'T00:00':'';
     feHasta = (feHasta !=='')?feHasta+'T00:00':'';
     feDesdeEst = (feDesdeEst !=='')?feDesdeEst+'T00:00':'';
     feHastaEst = (feHastaEst !=='')?feHastaEst+'T00:00':'';
     if (isNaN(login))
     {
         $('#cmb_cliente').val('');
     }
     $('#hid_opcion_busqueda').val("S");
     var JsonCuadrillas = [];
     if (storeSeleccionCuadrillas !== null && storeSeleccionCuadrillas.data !== null
         && storeSeleccionCuadrillas.data.items !== null && storeSeleccionCuadrillas.data.items.length > 0
         && $('#txt_cmb_cuadrillas').val()!=='') {
         for(var i = 0 ; i < storeSeleccionCuadrillas.data.items.length ; ++i) {
             JsonCuadrillas.push(storeSeleccionCuadrillas.data.items[i].data.id_cuadrilla);
         }
         JsonCuadrillas = Ext.JSON.encode(JsonCuadrillas);
     }

     // fix para omitir las tares finalizadas
     var boolOmiteTareaFinalizada = false;
     if((((feDesde != '' && feHasta != '') ||  (feDesdeEst != '' && feHastaEst != '')) 
        &&  strOrigen == 'tareasPorEmpleado' && numeroTarea == '' && JsonCuadrillas == ''
        && tareaPadre == '' &&  numeroCaso == '' && asignado == '' && proceso == '' 
        && login == '') 
        || (login != '' && feDesde == '' && feDesdeEst == ''))
        {
            boolOmiteTareaFinalizada = true; 
        }

     var arrayEstados = estadosChecked.split(',');
     if (arrayEstados.toString().indexOf('Todos') >= 0 || estadosChecked == '') {
         if(strOrigen == 'tareasPorEmpleado' && boolOmiteTareaFinalizada)
         {
            arrayEstados = Ext.JSON.encode(arrayEstadosDefault);
         }else
         {
            arrayEstados = '';
         }         
     } else {
        if(strOrigen == 'tareasPorEmpleado' && boolOmiteTareaFinalizada)
        {
            estadosChecked = estadosChecked.replace('Finalizada,', ""); 
            arrayEstados = estadosChecked.split(',');
        }
        arrayEstados = Ext.JSON.encode(arrayEstados);
     }
     var verTareasEcucert = 'N';
     if($('#checkboxECUCERT').is(":checked") == true && $('#div_checkboxECUCERT').css('display') !== 'none'){
         verTareasEcucert = 'S';
     }
     var valueGetSearch = {
     strOrigen         : strOrigen,
     departamentoSession : departamentoSession,
     cliente           : login,
     departamento      : departamento,
     departamentoOrig  : departamentoOrig,
     ciudadOrigen      : ciudadOrigen,
     proceso           : proceso,
     ciudadDestino     : ciudadDestino,
     tarea             : tarea,
     asignado          : asignado,
     estado            : arrayEstados,
     numeroActividad   : numeroTarea,
     numeroTareaPadre  : tareaPadre,
     numeroCaso        : numeroCaso,
     feSolicitadaDesde : feDesde,
     feSolicitadaHasta : feHasta,
     feFinalizadaDesde : feDesdeEst,
     feFinalizadaHasta : feHastaEst,
     cuadrilla         : JsonCuadrillas,
     opcionBusqueda    : "S",
     verTareasEcucert  : verTareasEcucert,
     queryAllTask      : "",
     page              :1,
     start             :0,
     limit             :6000};
     paramGet = buildDataGet(valueGetSearch); 
 
     gridTareas.ajax.url(url_listGridTarea+paramGet).load();
 }
 
 //función validar filtros para exportar tareas
 function validarExportarExcel()
 {
     var mensaje = '';
     var cuadrilla          =  '';
     var nombreCuadrilla    =  '';
     var opcionBusqueda     = $('#hid_opcion_busqueda').val()   ? $('#hid_opcion_busqueda').val() : 'N';
     var login       = ($('#div_cmb_cliente').css('display') !== 'none')?$('#cmb_cliente').val():'';
     var numeroTarea = ($('#div_txtActividad').css('display') !== 'none')?$('#txtActividad').val():'';
     var tareaPadre  = ($('#div_txtTareaPadre').css('display') !== 'none')?$('#txtTareaPadre').val():'';
     var numeroCaso  = ($('#div_txtCaso').css('display') !== 'none')?$('#txtCaso').val():'';
     var empresaBusca = ($('#div_cmbEmpresaBusqueda').css('display') !== 'none')?$('#cmbEmpresaBusqueda').val():'';
     var feSolicitadaDesde = '';
     var feSolicitadaHasta = '';
     var feFinalizadaDesde = '';
     var feFinalizadaHasta = '';
 
     if( (numeroTarea === "" || !numeroTarea) && numeroCaso === "" && (tareaPadre === "" || !tareaPadre))
     {
         if($('#div_feSolicitada').css('display') !== 'none' && $('#feSolicitada').val() !== '')
         {
             feSolicitadaDesde  = $('#feSolicitada').data('daterangepicker').startDate.format('YYYY-MM-DD');
             feSolicitadaHasta  = $('#feSolicitada').data('daterangepicker').endDate.format('YYYY-MM-DD');
         }
         if($('#div_feFinalizada').css('display') !== 'none' && $('#feFinalizada').val() !== '')
         {
             feFinalizadaDesde  = $('#feFinalizada').data('daterangepicker').startDate.format('YYYY-MM-DD');    
             feFinalizadaHasta  = $('#feFinalizada').data('daterangepicker').endDate.format('YYYY-MM-DD');
         }
     }
 
     var filtroUsuario      = $('#filtroUsuario').val() ? $('#filtroUsuario').val() : '';
     var departamento      = ($('#div_cmb_departamento').css('display') !== 'none')?$('#cmb_departamento').val():'';
     var tarea             = ($('#div_cmb_tarea').css('display') !== 'none')?$('#cmb_tarea').val():'';
     var asignado          = ($('#div_txtAsignado').css('display') !== 'none')?$('#txtAsignado').val():'';
 
     if (storeSeleccionCuadrillas !== null &&
         storeSeleccionCuadrillas.data !== null &&
         storeSeleccionCuadrillas.data.items !== null &&
         storeSeleccionCuadrillas.data.items.length > 0 ) {
         cuadrilla       = [];
         nombreCuadrilla = [];
         for(var i = 0 ; i < storeSeleccionCuadrillas.data.items.length ; ++i) {
             cuadrilla.push(storeSeleccionCuadrillas.data.items[i].data.id_cuadrilla);
             nombreCuadrilla.push(storeSeleccionCuadrillas.data.items[i].data.nombre_cuadrilla);
         }
         cuadrilla       = cuadrilla.toString();
         nombreCuadrilla = nombreCuadrilla.toString();
     }
     var estadosChecked = $('#cmb_estadosTareas').val();
     var estado  = estadosChecked.split(',');
     if (estado.toString().indexOf('Todos') >= 0 || estadosChecked == '') {
         estado = '';
     } else {
         estado = estado.toString();
     }
 
     if (
         (
             (opcionBusqueda === 'S' || gridTareas.data().count() < 1) && numeroTarea == '' && tareaPadre == '' && numeroCaso == ''
         ) ||
         (
             login != '' || tarea != '' || asignado != '' || estado != '' || departamento != '' || empresaBusca != '' || cuadrilla != ''
         )
         )
     {
         if (feSolicitadaDesde == '' && feSolicitadaHasta == '' && feFinalizadaDesde == '' && feFinalizadaHasta == '') {
             mensaje = "Por favor elegir un rango de fechas sea por <b>Fecha Solicitada</b> o <b>Fecha Estado</b><br/>"+
                         "y <b>no mayor a 30 días</b>.";
             showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:mensaje,btnOkText:'Cerrar'});
             return false;
         }
 
         if (feSolicitadaDesde !== '' && feSolicitadaHasta !== '' && 
             getDiferenciaTiempo(new Date(feSolicitadaDesde), new Date(feSolicitadaHasta) ) > 31) {
             mensaje = "La <b>Fecha Solicitada</b> supera un rango mayor a 30 días.";
             showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:mensaje,btnOkText:'Cerrar'});
             return false;
         }
 
         if (feFinalizadaDesde !== '' && feFinalizadaHasta !== '' && 
             getDiferenciaTiempo(new Date(feFinalizadaDesde), new Date(feFinalizadaHasta) ) > 31) {
             mensaje = "La <b>Fecha Estado</b> supera un rango mayor a 30 días.";
             showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:mensaje,btnOkText:'Cerrar'});
             return false;
         }
     }
 
     feSolicitadaDesde = (feSolicitadaDesde !=='')?feSolicitadaDesde+'T00:00':'';
     feSolicitadaHasta = (feSolicitadaHasta !=='')?feSolicitadaHasta+'T00:00':'';
     feFinalizadaDesde = (feFinalizadaDesde !=='')?feFinalizadaDesde+'T00:00':'';
     feFinalizadaHasta = (feFinalizadaHasta !=='')?feFinalizadaHasta+'T00:00':'';
 
     var parametros = {
         'hid_cliente'         : login,
         'hid_tarea'           : tarea,
         'hid_asignado'        : asignado,
         'hid_estado'          : estado,
         'hid_numeroTarea'     : numeroTarea,
         'hid_TareaPadre'      : tareaPadre,
         'hid_numeroCaso'      : numeroCaso,
         'hid_departamento'    : departamento,
         'hid_empresa'         : empresaBusca,
         'hid_cuadrilla'       : cuadrilla,
         'hid_nombreCuadrilla' : nombreCuadrilla,
         'hid_opcion_busqueda' : opcionBusqueda,
         'filtroUsuario'       : filtroUsuario,
         'feSolicitadaDesde'   : feSolicitadaDesde,
         'feSolicitadaHasta'   : feSolicitadaHasta,
         'feFinalizadaDesde'   : feFinalizadaDesde,
         'feFinalizadaHasta'   : feFinalizadaHasta
     };
 
     objParametros = JSON.stringify(parametros);
     objParametros  = objParametros.replace(/"/g, '\\"');
     
     var strOnclick="javascript:exportarExcel(\""+objParametros+"\",this);";
     $('#btnConfirmaExportarOk').attr('onclick',strOnclick);
     $('#rd_todo').prop("checked",false);
     $('#rd_filtro').prop("checked","checked");
     $('#modalConfirmaExportar').modal('show');    
 }
 
 //función exportar tareas
 function exportarExcel(data,e){
     var parametros = JSON.parse(data);
     var idBtn = e.id;
     
     //Validación para generar reporte total
     if($("input[name=rd_opcionReporte]:checked").val() == 'chk_todo')
     {
         parametros = {
             'hid_cliente'         : '',
             'hid_tarea'           : '',
             'hid_asignado'        : '',
             'hid_estado'          : '',
             'hid_numeroTarea'     : '',
             'hid_TareaPadre'      : '',
             'hid_numeroCaso'      : '',
             'hid_departamento'    : '',
             'hid_empresa'         : '',
             'hid_cuadrilla'       : '',
             'hid_nombreCuadrilla' : '',
             'hid_opcion_busqueda' : 'N',
             'filtroUsuario'       : '',
             'feSolicitadaDesde'   : '',
             'feSolicitadaHasta'   : '',
             'feFinalizadaDesde'   : '',
             'feFinalizadaHasta'   : ''
         };
     }
 
     var mensaje = '';
     $.ajax({
         data :  parametros,
         url  :  urlTareasExportar,
         type :  'post',
         beforeSend: function () {
             spinnerLoadinButton(idBtn,"show")
         }, 
         success:  function (response) {
             if(response.status == 'ok')
             {
                 mensaje = response.message+'. En breves minutos llegará el reporte a su correo.'
             }
             else
             {
                 mensaje = response.message;
             }
             spinnerLoadinButton(idBtn,"hide");
             $('#modalConfirmaExportar').modal('hide'); 
             showModalMensajeCustom({tittle:'Mensaje',tipo:'success',mensaje:mensaje,btnOkText:'Cerrar'});   
         },
         failure: function(response){
             spinnerLoadinButton(idBtn,"hide");
             mensaje = 'Error al realizar acción. Por favor informar a Sistemas.';
             $('#modalConfirmaExportar').modal('hide');
             showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:mensaje,btnOkText:'Cerrar'});
             
         }
     });
 }
 
 // Función que permite crear un dropdown para el filtro de estados
 function selectEstados(e,body)
 {
     var $element = $('#'+e);
     $element.popover({
         html: true,
         placement: 'bottom',
         container: $('body'), 
         content: '<div class="text-popover" style="font-size: 12px;">'+body+'</div>'
     }).data('bs.popover')
     .tip()
     .addClass('estados-popover')
     .css({"max-width": "480px"});
 }
 
  // Función que permite crear popover para mostrar columnas de grid
  function buildPopover(e,body)
  {
      var $element = $('#'+e);
      $element.popover({
          html: true,
          placement: 'bottom',
          container: $('body'), 
          content: '<div class="text-popover" style="font-size: 12px;">'+body+'</div>'
      }).data('bs.popover')
      .tip()
      .addClass(e+'-popover')
      .css({"max-width": "480px"});
  }

 //#######################################################
 //############## INGRESAR SEGUIMIENTO ###########
 //#######################################################
 
 function mostrarIngresoSeguimiento(tarea)
 {
     var objTarea = JSON.parse(tarea);
     $("#divtxtaRegistroInterno").css('display', 'none');
     if(objTarea.seguimientoInterno == 'S')
     {
         $("#divtxtaRegistroInterno").css('display', 'block');
     }
     $('#txtSeguimientoTarea').val('');
     $("#btnLoadingIngresoSeguimiento").hide();
     $('#btnIngresoSeguimiento').show(); 
     $('#modalIngresoSeguimiento').modal('show');
     $('#alertaValidaIngresoSeguimiento').hide();
     $("#txtTareaSeg").text(objTarea.nombre_tarea);
 
     document.getElementById('txtIdCaso').value                      = objTarea.id_caso
     document.getElementById('txtDetalleTareaSeg').value             = objTarea.id_detalle; 
 }
 
 document.getElementById("btnIngresoSeguimiento").onclick = function ()
 {
     var detalleTarea = $('#txtDetalleTareaSeg').val();
     var seguimientoTarea = $('#txtSeguimientoTarea').val();
     var idCaso = $('#txtIdCaso').val();
     var registroInterno = $('#cmbRegistroInterno').val();
 
     ingresarSeguimiento(idCaso,detalleTarea,seguimientoTarea,registroInterno)
 }
 
 function ingresarSeguimiento(idCaso,data, detalleSeguimiento, registroInterno)
 {
      $.ajax({
         data :  {
             id_caso: idCaso,
             id_detalle: data,
             seguimiento: detalleSeguimiento,
             registroInterno: registroInterno
         },
         url: '../info_caso/ingresarSeguimiento',
         type :  'POST',
         beforeSend: function () {
             $('#btnIngresoSeguimiento').hide();
             $("#btnLoadingIngresoSeguimiento").show();
         },
         success:  function (response) {
             if (response.mensaje != "cerrada")
             {
                 $('#modalIngresoSeguimiento').modal('hide');
                 showModalMensajeCustom({tipo:'success',mensaje: "Se ingreso el seguimiento.",btnOkText:'OK'});
             } else
             {
                 $('#modalIngresoSeguimiento').modal('hide');
                 showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje: "La tarea se encuentra Cerrada, por favor consultela nuevamente.",btnOkText:'Cerrar'});
             }
             $("#btnLoadingIngresoSeguimiento").hide();
             $('#btnIngresoSeguimiento').show();  
 
         },
         failure: function(response){
             $('#modalIngresoSeguimiento').modal('hide');
             showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje: "Error al realizar acción. Por favor informar a Sistemas.",btnOkText:'Cerrar'});
         }
     });
  }
  //Ingresar seguimiento de registro de contactos
  //opcion: Reasignar, Cerrar
function ingresarSeguimientoRegistroContactos(arraySeguimientoRegistroContacto)
{
    var codEmpresa = arraySeguimientoRegistroContacto.empresa_id;
    var idCaso = arraySeguimientoRegistroContacto.id_caso;
    var idDetalle = arraySeguimientoRegistroContacto.id_detalle;
    var user = arraySeguimientoRegistroContacto.login;
    var dep = arraySeguimientoRegistroContacto.departamento_id;
    var prefijoEmp = arraySeguimientoRegistroContacto.str_empresa_tarea;
    var opcion = arraySeguimientoRegistroContacto.tipo;
    //var seguimiento = '';
    var valorTmpComboTelefono, valorConvencional, valorNombre, valorCelular, valorCargo, valorCorreo = '';
    var ingresarSeguimiento = false;
    var seguimientoArray=[];
    var seguimiento = '';
    var dataTemporal={};
    var dataBase={};
    if (opcion == 'Reasignar')
    {
        valorTmpComboTelefono = $("#cmbPrefijoTelefono").val() + $('#txtConvencional').val();
        valorConvencional = (valorTmpComboTelefono == '-1' || valorTmpComboTelefono == 'null') ? 'NA' : valorTmpComboTelefono;
        valorNombre = $('#txtNombreContacto').val() == '' ? 'NA' : $('#txtNombreContacto').val();
        valorCelular = $('#txtCelularContacto').val() == '' ? 'NA' : $('#txtCelularContacto').val();
        valorCargo = $('#txtCargoContacto').val() == '' ? 'NA' : $('#txtCargoContacto').val();
        valorCorreo = $('#txtCorreoContacto').val() == '' ? 'NA' : $('#txtCorreoContacto').val();
        if (registroContacto != null && (registroContacto.nombre != valorNombre
            || registroContacto.celular != valorCelular
            || registroContacto.cargo != valorCargo
            || registroContacto.correo != valorCorreo
            || registroContacto.convencional != valorConvencional))
        {
            ingresarSeguimiento = true;
            dataTemporal = {
                'nombre' : valorNombre,
                'celular' : valorCelular,
                'cargo': valorCargo,
                'correo': valorCorreo,
                'convencional': valorConvencional,
                'estado' : 'Temporal'
            }
            dataBase =
            {
                'nombre' : registroContacto.nombre,
                'celular' : registroContacto.celular,
                'cargo': registroContacto.cargo,
                'correo': registroContacto.correo,
                'convencional': registroContacto.convencional,
                'observacion' : registroContacto.observacion,
                'estado' : 'Base'
            }
            seguimientoArray.push(dataTemporal);
            seguimientoArray.push(dataBase);
            seguimiento = JSON.stringify(seguimientoArray);
        } else if (registroContacto == null && ('NA' != valorNombre
            || 'NA' != valorCelular
            || 'NA' != valorCargo
            || 'NA' != valorCorreo
            || 'NA' != valorConvencional))
        {
            ingresarSeguimiento = true;
            dataTemporal = {
                'nombre' : valorNombre,
                'celular' : valorCelular,
                'cargo': valorCargo,
                'correo': valorCorreo,
                'convencional': valorConvencional,
                'estado' : 'Temporal'
            }
            seguimientoArray.push(dataTemporal);
            seguimiento = JSON.stringify(seguimientoArray);
        }
    } else if (opcion == 'Cerrar' && registroContactoModificado != null) 
    {
        valorConvencional = registroContactoModificado.convencional == "null" ? 'NA' : registroContactoModificado.convencional;
        valorNombre = registroContactoModificado.nombre == '' ? 'NA' : registroContactoModificado.nombre;
        valorCelular = registroContactoModificado.celular == '' ? 'NA' : registroContactoModificado.celular;
        valorCargo = registroContactoModificado.cargo == '' ? 'NA' : registroContactoModificado.cargo;
        valorCorreo = registroContactoModificado.correo == '' ? 'NA' : registroContactoModificado.correo;

        if (registroContacto == null)
        {
            ingresarSeguimiento = true;
            dataTemporal = {
                'nombre' : valorNombre,
                'celular' : valorCelular,
                'cargo': valorCargo,
                'correo': valorCorreo,
                'convencional': valorConvencional,
                'estado' : 'Temporal'
            }
            seguimientoArray.push(dataTemporal);
            seguimiento = JSON.stringify(seguimientoArray);

        } else 
        {
            if (registroContacto != null && (registroContacto.nombre != valorNombre
                || registroContacto.celular != valorCelular
                || registroContacto.cargo != valorCargo
                || registroContacto.correo != valorCorreo
                || registroContacto.convencional != valorConvencional))
            {
                ingresarSeguimiento = true;
                dataTemporal = {
                    'nombre' : valorNombre,
                    'celular' : valorCelular,
                    'cargo': valorCargo,
                    'correo': valorCorreo,
                    'convencional': valorConvencional,
                    'estado' : 'Temporal'
                }
                dataBase = {
                    'nombre' : registroContacto.nombre,
                    'celular' : registroContacto.celular,
                    'cargo': registroContacto.cargo,
                    'correo': registroContacto.correo,
                    'convencional': registroContacto.convencional,
                    'observacion' : registroContacto.observacion,
                    'estado' : 'Base'
                }
                seguimientoArray.push(dataTemporal);
                seguimientoArray.push(dataBase);
                seguimiento = JSON.stringify(seguimientoArray);
                
            }
        }
    } else if (opcion == 'Cerrar' && strObservacionRegistroContactos != '')
    {
        ingresarSeguimiento = true;
        dataTemporal = {
            'nombre' : valorNombre,
            'celular' : valorCelular,
            'cargo': valorCargo,
            'correo': valorCorreo,
            'convencional': valorConvencional,
            'observacion' : strObservacionRegistroContactos,
            'estado' : 'Temporal'
        }
        seguimientoArray.push(dataTemporal);
        seguimiento = JSON.stringify(seguimientoArray);
    }

    if (ingresarSeguimiento)
    {
        var dataParametro = {
            'codEmpresa': codEmpresa,
            'idDetalle': idDetalle,
            'idCaso': idCaso,
            'seguimiento': seguimiento,
            "ejecucionTarea": user,
            "departamento": dep,
            "prefijoEmpresa": prefijoEmp,
            'esRegistroContacto': 'S'
        };
        var parametros = {
            'data': dataParametro,
            'op': 'putIngresarSeguimiento',
            'user': user
        };
        $.ajax({
            data: JSON.stringify(parametros),
            url: urlGetSoporteProcesar,
            type: 'POST',
            success: function (response) {
            }
        });
    }
}
 //#######################################################
 //############## VER SEGUIMIENTO ###########
 //#######################################################
 
 function mostrarSeguimiento(id_detalle)
 {
     $("#tbodyMostrarSeguimiento").html("");
     $('#modalMostrarSeguimiento').modal('show');
     var seguimientos     = "<tr><td colspan='4' style='text-align:center;'>La tarea seleccionada no tiene seguimiento.</td></tr>";
         $.ajax({
             data :  {id_detalle: id_detalle},
             url: '../info_caso/verSeguimientoTarea',
             type :  'GET',
             success:  function (response) {
                 var arrSeguimientos  = response.encontrados;
                 if(response.total > 0)
                 {  seguimientos = "";
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
 
 //#######################################################
 //############## EJECUTAR,PAUSAR,REANUDAR TAREA ###########
 //#######################################################
 
 function mostrarEjecucionTarea(tarea)
 {
     var objTarea = JSON.parse(tarea);
     if(objTarea.accionTarea !== "pausar")
     {
         $('#modalValidaTareasAbiertas').modal('show');
     }
     $('#alertaValidaEjecucionTarea').hide();
     $('#divtxtaMotivoPausa').hide();
     $("#txtTarea").text(objTarea.nombre_tarea);
 
     document.getElementById('txtDetalleTarea').value             = objTarea.id_detalle;
     document.getElementById('txtTiempo').value                   = objTarea.duracionTarea;
     document.getElementById('txtPersonaEmpresaRol').value        = objTarea.personaEmpresaRolId;
     document.getElementById('txtNumeroTarea').value              = objTarea.numero_tarea;
 
     document.getElementById('txtNombreProceso').value            = objTarea.nombre_proceso;
     document.getElementById('txtUsrAsignado').value              = objTarea.ref_asignado_nombre;
     document.getElementById('txtDeptAsignado').value             = objTarea.asignado_nombre;
     document.getElementById('txtDetalleHist').value              = objTarea.intIdDetalleHist; //
     document.getElementById('txtNombreUsrAsignado').value        = objTarea.ref_asignado_nombre;
 
     var numeroTarea = $('#txtNumeroTarea').val();
 
     validarTareasAbiertas(objTarea.accionTarea,numeroTarea);
 }
 
 function validarTareasAbiertas(origen, numeroTarea)
 {
     document.getElementById('txtOrigen').value = origen;
     $("#divtxtaDetalleTarea").css('display', 'block');
     spinnerLoadinButton('btnGrabarEjecucionTarea','hide');
     $('#txtObservacionTarea').val(''); 
     if(origen == "pausar")
     {
         $('#divtxtaMotivoPausa').show();
         $("#accionTareaTitle").text("Pausar Tarea Asignada");
         buscaMotivosPausa("cmbMotivoPausa");
         $("#divtxtaDetalleTarea").css('display', 'none');
 
         $('#modalEjecucionTarea').modal('show');
         return false;
     }
 
     if(origen == "reanudar")
     {
         $("#accionTareaTitle").text("Reanudar Tarea Asignada");
     }
 
     if(origen == "iniciar")
     {
         $("#accionTareaTitle").text("Ejecutar Tarea Asignada");
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
                 $('#btnLoadingConfirmaTareaEjecucion').hide(); 
                 $('#modalConfirmaTareaEjecucion').modal('show');
                 spinnerLoadinButton('btnConfirmaTareaEjecucion','hide');
                 $("#txtNumeroTareaConfirma").text(response.strTareas);
             }
             else
             {
                 $('#modalValidaTareasAbiertas').modal('hide');
                 $('#modalEjecucionTarea').modal('show');
             }
         },
         failure: function(response){
             showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje: "Error al realizar acción. Por favor informar a Sistemas.",btnOkText:'Cerrar'});
         }
     });
 }
 
 document.getElementById("btnConfirmaTareaEjecucion").onclick = function () 
 {
     var nombreTarea = $('#txtTarea').text();
     var detalleTarea = $('#txtDetalleTarea').val();
     var personaEmpresaRol = $('#txtPersonaEmpresaRol').val();
     var numeroTarea = $('#txtNumeroTarea').val();
     var nombreProceso = $('#txtNombreProceso').val();
     var usrAsignado = $('#txtUsrAsignado').val();
     var deptAsignado = $('#txtDeptAsignado').val();
 
     gestionarTareas(nombreTarea,detalleTarea, personaEmpresaRol, numeroTarea, nombreProceso, usrAsignado, deptAsignado);
 }
 
 document.getElementById("btnGrabarEjecucionTarea").onclick = function () 
 {
     spinnerLoadinButton('btnGrabarEjecucionTarea','show');
     var nombreTarea = $('#txtTarea').text();
     var detalleTarea = $('#txtDetalleTarea').val();
     var tiempoTarea = $('#txtTiempo').val();
     var numeroTarea = $('#txtNumeroTarea').val();
     var nombreProceso = $('#txtNombreProceso').val();
     var deptAsignado = $('#txtDeptAsignado').val();
     var detalleHist = $('#txtDetalleHist').val();
     var origen = $('#txtOrigen').val();
     var nombreUsrAsignado = $('#txtNombreUsrAsignado').val();
 
     aceptarRechazarTarea(origen, detalleTarea, nombreTarea, tiempoTarea, numeroTarea, nombreProceso, deptAsignado, detalleHist, nombreUsrAsignado);
 };
 
 function gestionarTareas(nombre,detalleTarea,personaEmpresaRol,numeroTarea,nombreProceso,usrAsignado,deptAsignado)
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
         beforeSend: function () {
             spinnerLoadinButton('btnConfirmaTareaEjecucion','show');
         },
         success:  function (response) {
             $('#modalConfirmaTareaEjecucion').modal('hide');
             spinnerLoadinButton('btnGrabarEjecucionTarea','hide');
             var strOnclick = '';
             var strTipo = 'error';
             var strTittle = 'Alerta';
             if (typeof response.status !== 'undefined' && response.status == 'EXITO')
             {
                 strOnclick = "$('#modalEjecucionTarea').modal('show');";
                 strTipo = 'success';
                 strTittle = 'Información';
             }
             spinnerLoadinButton('btnConfirmaTareaEjecucion','hide');            
             showModalMensajeCustom({tittle:strTittle,tipo:strTipo,mensaje: response.mensaje, btnOkOnClick:strOnclick,btnOkText:'OK'});
         },
         failure: function(response){
             spinnerLoadinButton('btnConfirmaTareaEjecucion','hide');
             showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje: "Error al realizar acción. Por favor informar a Sistemas.",btnOkText:'Cerrar'});
         }
     });
 }

 let ObjDatosPausa;
 
 function aceptarRechazarTarea(origen,data,nombre,duracionTarea,numeroTarea,nombreProceso,deptAsignado,detalleHist,nombreUsrAsignado)
 {
     var observacion = "";
     let jsonDatosPausa;
     if(origen == "pausar")
     {
         observacion = $('#cmbMotivoPausa').val() ===''?'':$( "#cmbMotivoPausa option:selected" ).text();
         jsonDatosPausa = $('#cmbMotivoPausa').val() ===''?'' : ObjDatosPausa.filter(word => word.id_motivo == $('#cmbMotivoPausa').val());
         jsonDatosPausa = JSON.stringify(jsonDatosPausa[0]);
     }else{
        jsonDatosPausa = '';
         observacion = $('#txtObservacionTarea').val();
     }
 
     $.ajax({
         data :  {
             id               : data,
             observacion      : observacion,
             bandera          : 'Aceptada',
             origen           : origen,
             duracionTarea    : duracionTarea,
             jsonDatosPausa   : jsonDatosPausa,
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
             $('#modalValidaTareasAbiertas').modal('hide');
             $('#modalConfirmaTareaEjecucion').modal('hide');
             $('#modalEjecucionTarea').modal('hide');
             if (!response.success && !response.seguirAccion) 
             {
                 showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:response.mensaje,btnOkText:'Cerrar'});                         
             }
             else if (response.mensaje != "cerrada")
             {
                 showModalMensajeCustom({tipo:'success',mensaje: "Se actualizó los datos.",btnOkText:'OK'});
             } else
             {
                 showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje: "La tarea se encuentra Cerrada, por favor consultela nuevamente.",btnOkText:'Cerrar'});
             }
             gridTareas.ajax.url(url_listGridTarea+paramGet).load();
             
         },
         failure: function(response){
             showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje: "Error al realizar acción. Por favor informar a Sistemas.",btnOkText:'Cerrar'});
         }
     });
  }
 
 function buscaMotivosPausa(nombreComboMotivoPausa)
 {
     var parametros = {
     };
     $.ajax({
             data :  parametros,
             url  :  url_motivosPausarTarea,
             type :  'post',
             success:  function (response) {
                 var arrTiposProblema  = response.encontrados;
                 ObjDatosPausa = arrTiposProblema;
                 var tiposProblema     = "<option  value=''>Seleccione...</option>"; 
                 for(var i=0;i<arrTiposProblema.length;i++)
                 {
                     tiposProblema += "<option value='"+arrTiposProblema[i].id_motivo+"'>"+arrTiposProblema[i].nombre_motivo+"</option>"; 
                 }
                 $("#"+nombreComboMotivoPausa).html(tiposProblema);
             },
             failure: function(response){
                 showModalMensajeCustom({tittle:'Alerta',tipo:'error',mensaje:'Error al realizar acción. Por favor informar a Sistemas.',btnOkText:'Cerrar'}); 
             }
     });
 }
 
 //#######################################################
 //############## FINALIZAR TAREA (SIGUIENTE FASE) ###########
 //#######################################################
 
 function finalizarTareaNew(tarea,e)
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
     $("#txtTareaInicial").text(objTarea.nombre_tarea);
 
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
             
                     rs = validarFechaTareaReprogramadaNew(objTarea.fechaEjecucion,objTarea.horaEjecucion,fechaActual,json.horaActual);
                                                         
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
                                             showModalsFinalizartarea(objTarea.id_detalle, Ext.JSON.encode(objTarea), fechaActual,json.horaActual, objTarea.tipoAsignado, objTarea.asignado_id);
                                             
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
             strClick = "guardarTareaFinalizadaNew(\""+objTareaJson+"\","+idDetalle+")";
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
 
 function guardarTareaFinalizadaNew(tarea, id_detalle)
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
 
         finalizarTareaRequestNew(arayDataFinalizar);
     }
 }
 
 function finalizarTareaRequestNew(arrayFin)
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
         idFinTarea          : arrayFin.idFinTarea, 
         boolFinalTareaAnterior : arrayFin.boolFinalTareaAnterior
     };
     var stroOnclick="";
 
         stroOnclick="javascript:gridTareas.ajax.url(url_listGridTarea+paramGet).load();";
     
 
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
                 showAlertModals("alertaValidaFinalizaTarea",response.mensaje);
                 $('#btnGrabarFinalizaTarea').show();
                 $('#btnLoadingGrabarFinalizaTarea').hide();
             }
         },
         failure: function(response){
             showAlertModals("alertaValidaFinalizaTarea",response.mensaje);
             $('#btnGrabarFinalizaTarea').show();
             $('#btnLoadingGrabarFinalizaTarea').hide();
         }
     });
         
 }
 
 // Función que muestra el mensaje de final de tarea.
 
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
 
 //#######################################################
 //############## REASIGNAR TAREA ###########
 //#######################################################
 
 function reasignarTarea(tarea,e)
 {
     $("#intIdDetalleReasigna,#txtaObsTareaFinalReasigna,#txtDatetimepickerEjecReasigna").val("");
     $("#cmbEmpresaReasigna").attr('disabled','disabled');
     $("#cmbCiudadReasigna,#cmbDepartReasigna,#cmbEmpleadoReasigna,#cmbCuadrillaReasigna").val('');
     $("#txtDepartment,#txtEmpleado,#txtCiudad,#txtCuadrilla").val('');
     $("#txtDepartment,#txtEmpleado,#txtCiudad,#txtCuadrilla").attr('disabled','disabled');
     $('#divIdArboltareaReasig,#alertaValidaReasignaTarea,#tab_manual_hal,#tittle_tareaHal').css('display', 'none');
     $('#divIdArboltareaReasig').removeClass('showDiv');
     $('#divcmbMotivoTareaFinalReasig,#divdrwTareaFinalReasig,#divcmbEmpresaReasigna,#divcmbCiudadReasigna,#divtxtaObsTareaFinalReasigna'+
     ',#divcmbDepartReasigna,#divcmbEmpleadoReasigna,#divcmbdDateEjecReasigna,#divcmbHourEjecReasigna,#divcmbCuadrillaReasigna,#divcmbContratistaReasigna').removeClass('has-error');
     $('#divtxtCorreoContacto,#divtxtConvencionalContacto,#divtxtCelularContacto,#divtxtNombreContacto,#divtxtCargoContacto').removeClass('has-error');
     $('#divcmbMotivoTareaFinal').removeClass('showMotivo');
     $('#btnGrabarReasignaTarea').show();
     $('#btnLoadingGrabarReasignaTarea').hide();
     $("#chxRespuestaInmediata" ).prop( "checked", false);
     $(".divcmbResponsable").css('display','none');
     $("#divcmbEmpleadoReasigna").css('display','block');
     $("#radio_empleado,#radio_cuadrilla,#radio_contratista").prop( "checked",false);
     document.getElementById('radio_cuadrilla').disabled = false;
     $("#radio_empleado").prop( "checked",true);
     $("#tarea_hal,#li_tarea_hal").removeClass("in active");
     $("#tarea_manual,#li_tarea_manual").addClass("in active");
     spinnerLoadinButton('btnGrabarReasignaTarea','hide');
 
     e.setAttribute("disabled", "disabled");
     setTimeout(function(){e.removeAttribute("disabled", "");}, 5000);
     var arrayDataGrid = JSON.parse(tarea);
 
     
     var strTieneProgresoMateriales  = arrayDataGrid.tieneProgresoMateriales;
     var strEmpresaTarea             = arrayDataGrid.strEmpresaTarea;
     var strControlActivos           = arrayDataGrid.requiereControlActivo;
 
     $("#txtTareaInicialReasigna").text(arrayDataGrid.nombre_tarea);
     $("#intIdDetalleReasigna").val(arrayDataGrid.id_detalle);
 
     if (arrayDataGrid.tareaEsHal)
     {
         $('#tittle_tareaHal').css('display','block');
     }
         
     if((strTieneProgresoMateriales === "NO") && strEmpresaTarea === "TN" && strControlActivos === 'SI')
     {
         arrayDataGrid['redisenioTarea'] = true;
         registroFibraMaterial(arrayDataGrid);
     }
     else{
         if(arrayDataGrid.permiteRegistroActivos === true && arrayDataGrid.departamentoId != 126)
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
                                     showModalsReasignarTarea(arrayDataGrid.id_detalle,
                                         arrayDataGrid.id_tarea,
                                         Ext.JSON.encode(arrayDataGrid),
                                         response.fechaActual,
                                         response.horaActual,
                                         arrayDataGrid.tipoAsignado);
                                         if(arrayDataGrid.tareaParametro)
                                         {        
                                             $('#tab_manual_hal').css('display','block');
                                             var htmlHalAsigna = getHtmlhalAsigna('reasignar',
                                                                             response.fechaActual,
                                                                             JSON.stringify(arrayDataGrid));
                                             $('#reasignarHal-tarea').html(htmlHalAsigna); 
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
                         showModalsReasignarTarea(arrayDataGrid.id_detalle,
                             arrayDataGrid.id_tarea,
                             Ext.JSON.encode(arrayDataGrid),
                             response.fechaActual,
                             response.horaActual,
                             arrayDataGrid.tipoAsignado);
                         if(arrayDataGrid.tareaParametro)
                         {        
                             $('#tab_manual_hal').css('display','block');
                             var htmlHalAsigna = getHtmlhalAsigna('reasignar',
                                                             response.fechaActual,
                                                             JSON.stringify(arrayDataGrid));
                             $('#reasignarHal-tarea').html(htmlHalAsigna); 
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
     }
 
 }
 
 function showModalsReasignarTarea(idDetalle,idTarea,tarea, fechaActual,horaActual,tipoAsignado)
 {
     var objTarea = JSON.parse(tarea);
     $('#btnGrabarReasignaTarea').attr('onclick', '');
     $("#cmbPrefijoTelefono").val('');
     $("#txtNombreContacto,#txtCelularContacto,#txtCargoContacto,#txtCorreoContacto,#txtConvencional").val('');

     if (objTarea.tareaEsHal && tipoAsignado.toUpperCase() === 'CUADRILLA') 
     {
         document.getElementById('radio_cuadrilla').disabled = true;
     }
 
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
             loadCiudad('cmbCiudadReasigna','txtCiudad',strPrefijoEmpresaSession.trim(),strIdCantonUsrSession.trim());
             
             if(typeof strIdDepartamentoUsrSession !== 'undefined' && strIdDepartamentoUsrSession.trim())
             {
                 loadDepartamentos("cmbDepartReasigna","txtDepartment",strPrefijoEmpresaSession.trim(),strIdCantonUsrSession.trim(),strIdDepartamentoUsrSession.trim());
                 loadEmpleados("cmbEmpleadoReasigna","txtEmpleado",strPrefijoEmpresaSession.trim(),strIdCantonUsrSession.trim(),strIdDepartamentoUsrSession.trim());
                 loadCuadrillasXDepartamento('cmbCuadrillaReasigna','txtCuadrilla',strIdDepartamentoUsrSession.trim());
             }
         }
     }else
     {
         loadEmpresa();
     }
     loadContratistas('cmbContratistaReasigna','txtContratista');
     registroContacto = null;
     // Se debe presentar formulario de datos de cliente solo si tarea pertenece a caso
     // y empresa es Telconet
     if(objTarea.perteneceCaso && objTarea.strEmpresaTarea == "TN")
     {
        loadPrefijosTelefono(idDetalle);
        $('#divtxtCorreoContacto,#divtxtConvencionalContacto,#divtxtCelularContacto,#divtxtNombreContacto,#divtxtCargoContacto').removeClass('has-error');
     }else
     {
        $( "#formDatosContacto" ).remove();
     }
     objTareaJson  = Ext.JSON.encode(objTarea).replace(/"/g, '\\"');
     strClick = "guardarTareaReasignar(\""+objTareaJson+"\","+idDetalle+")";
     $('#btnGrabarReasignaTarea').attr('onclick',strClick);
     $('#modalReasignarTarea').modal('show'); 
 }
 
 function guardarTareaReasignar(tarea, id_detalle)
 {
     var data = JSON.parse(tarea);
     var strFinSelectNombre = '';
     var motivoFinalizaTarea;
     var idMotivoFinalizaTarea;
     var valorBool = true;//validarTareasMateriales();
     
     //variable para mantener el flujo de registro de materiales. 
     var conn = new Ext.data.Connection({
         listeners: {
             'beforerequest': {
                 fn: function (con, opt) {
                     Ext.get(document.body).mask('Loading...');
                 },
                 scope: this
             },
             'requestcomplete': {
                 fn: function (con, res, opt) {
                     Ext.get(document.body).unmask();
                 },
                 scope: this
             },
             'requestexception': {
                 fn: function (con, res, opt) {
                     Ext.get(document.body).unmask();
                 },
                 scope: this
             }
         }
     });
 
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
     $('#divtxtCorreoContacto,#divtxtConvencionalContacto,#divtxtCelularContacto,#divtxtNombreContacto,#divtxtCargoContacto').removeClass('has-error');
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
     else if($('#cmbCiudadReasigna').val() == '-1' || $('#cmbCiudadReasigna').val() == '')
     {
         showAlert("alertaValidaReasignaTarea","divcmbCiudadReasigna","Por favor seleccione ciudad");    
     }
     else if($('#cmbDepartReasigna').val() == '-1' || $('#cmbDepartReasigna').val() == '')
     {
         showAlert("alertaValidaReasignaTarea","divcmbDepartReasigna","Por favor seleccione departamento");    
     }
     else if($('#cmbEmpleadoReasigna').val() == '-1' || $('#cmbEmpleadoReasigna').val() == '' 
             && $("input[name=rdoTipoEmpleado]:checked").val() == 'Empleado')
     {
         showAlert("alertaValidaReasignaTarea","divcmbEmpleadoReasigna","Por favor escoja un empleado");    
     }
     else if($('#cmbCuadrillaReasigna').val() == '-1' || $('#cmbCuadrillaReasigna').val() == '' 
             && $("input[name=rdoTipoEmpleado]:checked").val() == 'Cuadrilla')
     {
         showAlert("alertaValidaReasignaTarea","divcmbCuadrillaReasigna","Por favor escoja una cuadrilla");    
     }
     else if($('#cmbContratistaReasigna').val() == '-1' || $('#cmbContratistaReasigna').val() == '' 
             && $("input[name=rdoTipoEmpleado]:checked").val() == 'Contratista')
     {
         showAlert("alertaValidaReasignaTarea","divcmbContratistaReasigna","Por favor escoja un contratista");    
     }
     else if($('#txtDatetimepickerEjecReasigna').val() == '')
     {
         showAlert("alertaValidaReasignaTarea","divcmbdDateEjecReasigna","Por favor seleccione fecha");    
     }
     else if($('#cmbHourEjecReasigna').val() == '-1')
     {
         showAlert("alertaValidaReasignaTarea","divcmbHourEjecReasigna","Por favor seleccione la hora");    
     }
     else if (data.perteneceCaso && data.strEmpresaTarea == "TN" && !validarDatosContacto('txtNombreContacto','Nombre'))
     {
        showAlert("alertaValidaReasignaTarea","divtxtNombreContacto","Existen errores en el campo Nombre de la sección registro de contacto del cliente");
     }
     else if (data.perteneceCaso && data.strEmpresaTarea == "TN" && !validarDatosContacto('txtCelularContacto','Celular'))
     {
        showAlert("alertaValidaReasignaTarea","divtxtCelularContacto","Existen errores en el campo Celular de la sección registro de contacto del cliente");
     } 
     else if (data.perteneceCaso && data.strEmpresaTarea == "TN" && !validarDatosContacto('txtCargoContacto','Cargo'))
     {
        showAlert("alertaValidaReasignaTarea","divtxtCargoContacto","Existen errores en el campo Cargo/Área de la sección registro de contacto del cliente");
     }  
     else if (data.perteneceCaso && data.strEmpresaTarea == "TN" && !validarDatosContacto('txtCorreoContacto','Correo'))
     {
        showAlert("alertaValidaReasignaTarea","divtxtCorreoContacto","Existen errores en el campo Correo de la sección registro de contacto del cliente");
     } 
     else if (data.perteneceCaso && data.strEmpresaTarea == "TN" && !validarDatosContacto('txtConvencional','Convencional'))
     {
        showAlert("alertaValidaReasignaTarea","divtxtConvencionalContacto","Existen errores en el campo Convencional de la sección registro de contacto del cliente");
     }      
     /*else if($('#txtaObsTareaFinalReasigna').val() == '')
     {
         showAlert("alertaValidaReasignaTarea","divtxtaObsTareaFinalReasigna","Por favor ingrese una observación");    
     }*/
     else
     {
         var strEmpleadoAsignado = $('#cmbEmpleadoReasigna').val();
         var strCuadrillaAsignado = $('#cmbCuadrillaReasigna').val();
         var strContratistaAsignado = $('#cmbContratistaReasigna').val();
         var strDepartamentoAsignado = $('#cmbDepartReasigna').val();
 
         var stroOnclick="javascript:gridTareas.ajax.url(url_listGridTarea+paramGet).load();";
 
         if((data.permiteRegistroActivos === true &&  typeof  finesTareaReasig !== 'undefined'
             && finesTareaReasig !== null && finesTareaReasig.length > 0)
             &&((data.strRequiereFibra       === 'S'  && data.tieneProgresoRuta           === 'NO')  
             ||(data.strRequiereMaterial    === 'S'  && data.tieneProgresoMateriales     === 'NO'))   
             )
         {   
                     if(data.strRequiereFibra === 'N')
                     {
                         data['tieneProgresoRuta'] = 'SI';
                     }
                     if(data.strRequiereMaterial === 'N')
                     {
                         data['tieneProgresoMateriales'] = 'SI';
                     }
                     data['redisenioTarea'] = true;
                     $('#modalReasignarTarea').modal('hide');
                     registroFibraMaterial(data, function (statusRegistro) { 
 
                         if(statusRegistro === 'OK')
                         {   $('#modalReasignarTarea').modal('show');
                             spinnerLoadinButton('btnGrabarReasignaTarea','show');
                             if(valorBool)
                             {
                                 //winReasignarTarea.destroy();
                                 conn.request({
                                     method: 'POST',
                                     params :{
                                         id_detalle: id_detalle,
                                         id_tarea:data.id_tarea,
                                         motivo: $("#txtaObsTareaFinalReasigna").val(),
                                         departamento_asignado: strDepartamentoAsignado,
                                         empleado_asignado   :  strEmpleadoAsignado,
                                         cuadrilla_asignada  :  strCuadrillaAsignado,
                                         contratista_asignada:  strContratistaAsignado,
                                         tipo_asignado       :  valorAsignacion, 
                                         fecha_ejecucion     :  $('#txtDatetimepickerEjecReasigna').val()+'T00:00',
                                         hora_ejecucion      :  $('#txtDatetimepickerEjecReasigna').val()+'T'+$('#cmbHourEjecReasigna').val(),
                                         intIdDetalleHist    :  data.intIdDetalleHist,
                                         nombre_tarea        :  data.nombre_tarea,
                                         numero_tarea        :  data.numero_tarea,
                                         nombreFinTarea      :  strFinSelectNombre,
                                         idFinTarea          :  (typeof data.intFinTareaId !== 'undefined')?data.intFinTareaId:"",
                                         motivoFinTarea      :  motivoFinalizaTarea,
                                         idMotivoFinTarea    :  idMotivoFinalizaTarea
                                     },
                                     url: 'reasignarTarea',
                                     success: function(response){                         
                                         var json = Ext.JSON.decode(response.responseText);
 
                                         if (!json.success && !json.seguirAccion) {
                                             Ext.MessageBox.show({
                                                 closable   :  false  , multiline : false,
                                                 title      : 'Alerta', msg : json.mensaje,
                                                 buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                                                 buttonText : {ok: 'Cerrar'},
                                                 fn : function (button) {
                                                     if(button === 'ok') {
                                                         $('#modalReasignarTarea').modal('hide');
                                                         gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                                                     }
                                                 }
                                             });
                                             return;
                                         }
 
                                         if(json.mensaje != "cerrada")
                                         {
                                             if(json.success)
                                             {
                                                if(data.perteneceCaso && data.strEmpresaTarea == "TN")
                                                 {
                                                     var arrayIngresoRegistroContacto =
                                                     {
                                                         empresa_id: 10,
                                                         id_caso: data.id_caso,
                                                         id_detalle: data.id_detalle,
                                                         login: data.loginSesion,
                                                         departamento_id: data.departamentoId,
                                                         str_empresa_tarea: data.strEmpresaTarea,
                                                         tipo: 'Reasignar'
                                                     };
                                                     ingresarSeguimientoRegistroContactos(arrayIngresoRegistroContacto);
                                                 } 
                                                 Ext.Msg.alert('Mensaje','Se asigno la tarea.', function(btn){
                                                     if(btn=='ok'){
                                                         $('#modalReasignarTarea').modal('hide');
                                                         gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                                                     }
                                                 });
                                             }
                                             else
                                             {
                                                 Ext.Msg.alert('Alerta ',json.mensaje);
                                             }
                                         }
                                         else
                                         {
                                                 Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                                         }
                                         spinnerLoadinButton('btnGrabarReasignaTarea','hide');
                                     },
                                     failure: function(response) {
                                         var json = Ext.JSON.decode(response.responseText);
                                         Ext.Msg.alert('Alerta ',json.mensaje);
                                         spinnerLoadinButton('btnGrabarReasignaTarea','hide');
                                     }
                                 });
                             }
                         }
                         else
                         {
                             Ext.Msg.alert('Alerta', 'No se pudo reasignar la tarea.');
                         }
                     });      
         }
         else{
             var objParametro = 
             {
                 id_detalle: id_detalle,
                 id_tarea: data.id_tarea,
                 motivo: $("#txtaObsTareaFinalReasigna").val(),
                 departamento_asignado: strDepartamentoAsignado,
                 empleado_asignado   :  strEmpleadoAsignado,
                 cuadrilla_asignada  :  strCuadrillaAsignado,
                 contratista_asignada:  strContratistaAsignado,
                 tipo_asignado       :  valorAsignacion,
                 fecha_ejecucion     :  $('#txtDatetimepickerEjecReasigna').val()+'T00:00',
                 hora_ejecucion      :  $('#txtDatetimepickerEjecReasigna').val()+'T'+$('#cmbHourEjecReasigna').val(),
                 intIdDetalleHist    :  data.intIdDetalleHist,
                 nombre_tarea        :  data.nombre_tarea,
                 numero_tarea        :  data.numero_tarea,
                 nombreFinTarea      :  strFinSelectNombre,
                 idFinTarea          :  (typeof data.intFinTareaId !== 'undefined')?data.intFinTareaId:"",
                 motivoFinTarea      :  motivoFinalizaTarea,
                 idMotivoFinTarea    :  idMotivoFinalizaTarea
             };
             $.ajax({
                 data :  objParametro,
                 url  :  'reasignarTarea',
                 type :  'post',
                 beforeSend: function () {
                     spinnerLoadinButton('btnGrabarReasignaTarea','show');
                 },
                 success:  function (response) {
                     if (!response.success && !response.seguirAccion) 
                     {
                         $('#modalReasignarTarea').modal('hide');
                         showModalsMessageFinTarea("error",response.mensaje,'Alerta',stroOnclick,true);
                         spinnerLoadinButton('btnGrabarReasignaTarea','hide');
                         return;
                     }
 
                     if (response.success)
                     {
                        if(data.perteneceCaso && data.strEmpresaTarea == "TN")
                         {
                             var arrayIngresoRegistroContacto =
                             {
                                 empresa_id: 10,
                                 id_caso: data.id_caso,
                                 id_detalle: data.id_detalle,
                                 login: data.loginSesion,
                                 departamento_id: data.departamentoId,
                                 str_empresa_tarea: data.strEmpresaTarea,
                                 tipo: 'Reasignar'
                             };
                             ingresarSeguimientoRegistroContactos(arrayIngresoRegistroContacto);
                         } 
                        
                         $('#modalReasignarTarea').modal('hide');
                         if(response.mensaje != "cerrada")
                         {
                             showModalsMessageFinTarea("success","Se asigno la tarea.","Mensaje",stroOnclick);
                         }
                         else
                         {
                             showModalsMessageFinTarea("error","La tarea se encuentra Cerrada, por favor consultela nuevamente","Alerta");
                         }
                         spinnerLoadinButton('btnGrabarReasignaTarea','hide');
                     }
                     else
                     {
                         showAlertModals("alertaValidaFinalizaTarea",response.mensaje);
                         spinnerLoadinButton('btnGrabarReasignaTarea','hide');
                     }
                 },
                 failure: function(response){
                     showAlertModals("alertaValidaFinalizaTarea",response.mensaje);
                     spinnerLoadinButton('btnGrabarReasignaTarea','hide');
                 }
             });
         }
     }
 }
 
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
 
 
 $("#cmbEmpresaReasigna").on("change", function() {
     $("#cmbCiudadReasigna,#cmbDepartReasigna,#cmbEmpleadoReasigna,#cmbCuadrillaReasigna,#cmbContratistaReasigna").val('');
     $("#txtCiudad,#txtEmpleado,#txtCuadrilla,#txtContratista,#txtDepartment").val('');
     $("#txtCiudad,#txtEmpleado,#txtCuadrilla,#txtContratista,#txtDepartment").attr('disabled','disabled');
     $("#radio_empleado,#radio_cuadrilla,#radio_contratista").prop( "checked",false);
     $("#radio_empleado").prop( "checked",true);
     $(".divcmbResponsable").css('display','none');
     $("#divcmbEmpleadoReasigna").css('display','block');
     $("#chxRespuestaInmediata" ).prop( "checked", false);
 
     var strEmpresa = $("#cmbEmpresaReasigna").val();
     loadCiudad('cmbCiudadReasigna','txtCiudad',strEmpresa)
 
 });
 
  //Obtener prefijos de telefono
function loadPrefijosTelefono(idDetalle) {
    //$("#cmbPrefijoTelefono").html("<option value='-1' >Seleccione prefijo</option>");
    var parametros = {};
    $.ajax({
        data: parametros,
        url: urlGetPrefijosTelefono,
        type: 'post',
        success: function (response) {
            if (response.total > 0) 
            {
                var arrayEmpresas = response.encontrados;
                if (arrayEmpresas.length > 0)
                {
                    var listaEmpresas = "<option value='-1' >Seleccione código de área</option>";
                    for (var i = 0; i < arrayEmpresas.length; i++)
                    {
                        listaEmpresas += "<option value='" + arrayEmpresas[i].codigo + "'>" + arrayEmpresas[i].codigo + "</option>";
                    }
                    $("#cmbPrefijoTelefono").html(listaEmpresas);
                    $('#cmbPrefijoTelefono').removeAttr('disabled');
                }
                else
                {
                    showAlertModals('alertaValidaReasignaTarea', 'Error al cargar las empresas. Por favor intente nuevamente.');
                }
                loadDatosContacto(idDetalle, 'Reasignar');
            }
        },
        failure: function (response) {
            showAlertModals('alertaValidaReasignaTarea', 'Error al realizar acción. Por favor informar a Sistemas.');
        }
    });
}

$("#cmbPrefijoTelefono").on("change", function() {
    $('#txtConvencional').val('');
});


function validateContactEmail() {
    var email = document.getElementById("txtCorreoContacto").value;
    var lblError = document.getElementById("lblErrorTxtCorreoContacto");
    lblError.innerHTML = "";
    var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (!expr.test(email) && email.length > 0)
    {
        lblError.innerHTML = "Debe ingresar un correo electrónico válido.";
        return false;//document.getElementById('btnGrabarReasignaTarea').disabled = true;
    }else{
        //document.getElementById('btnGrabarReasignaTarea').disabled = false;
    }
    return true;
}

function validateContactCelular() {
    var vContactCelular = document.getElementById("txtCelularContacto").value;
    var lblErrorCelular = document.getElementById("lblErrorTxtCelularContacto");
    lblErrorCelular.innerHTML = "";
    //var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (!/^[0-9]+$/.test(vContactCelular) && vContactCelular.length > 0)
    {
        lblErrorCelular.innerHTML = "Se permiten solo números";
        return false;//document.getElementById('btnGrabarReasignaTarea').disabled = true;
    } else if (vContactCelular.length > 0 && vContactCelular[0] != 0)
    {
        lblErrorCelular.innerHTML = "El número de celular debe empezar con 0, ejemplo: 098XXXXXXX";
        return false;//document.getElementById('btnGrabarReasignaTarea').disabled = true;
    } else if (vContactCelular.length > 0 && vContactCelular.length < 10)
    {
        lblErrorCelular.innerHTML = "Se debe ingresar 10 digitos";
        return false;//document.getElementById('btnGrabarReasignaTarea').disabled = true;
    } 
    return true;
}

function validateContactConvencional() {
    var vContactConvencional = document.getElementById("txtConvencional").value;
    var lblErrorConvencional = document.getElementById("lblErrorTxtConvencional");
    lblErrorConvencional.innerHTML = "";
    
    if ($("#cmbPrefijoTelefono").val() == '-1' && vContactConvencional.length > 0)
    {
        lblErrorConvencional.innerHTML = "Debe seleccionar un código de área";
        //Ext.getCmp('prefijoNumeroClienteARecibir').setActiveError('Seleccionar código de área')
        return false;
    } else if (!/^[0-9]+$/.test(vContactConvencional) && vContactConvencional.length > 0)
    {
        lblErrorConvencional.innerHTML = "Se permiten solo números";
        return false;
    } else if (vContactConvencional.length > 0 && vContactConvencional[0] != 2)
    {
        lblErrorConvencional.innerHTML = "El número convencional debe empezar con 2, ejemplo: 29XXXXX";
        return false
    } else if (vContactConvencional.length > 0 && vContactConvencional.length < 7)
    {
        lblErrorConvencional.innerHTML = "Se debe ingresar 7 digitos";
        return false
    } 
    
    return true;
}

function validateContactNombre() {
    var txtValor = document.getElementById("txtNombreContacto").value;
    var lblError = document.getElementById("lblErrorTxtNombreContacto");
    lblError.innerHTML = "";
    //var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (!/^[A-Za-zÀ-ÿ\\u00f1\\u00d1\s]+$/.test(txtValor) && txtValor.length > 0)
    {
        lblError.innerHTML = "Solo se permiten caracteres de texto";
        return false;//document.getElementById('btnGrabarReasignaTarea').disabled = true;
    }
    return true;
}

function validateContactCargo() {
    var txtValor = document.getElementById("txtCargoContacto").value;
    var lblError = document.getElementById("lblErrorTxtCargoContacto");
    lblError.innerHTML = "";
    //var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (!/^[A-Za-z0-9À-ÿ\\u00f1\\u00d1\s]+$/.test(txtValor) && txtValor.length > 0)
    {
        lblError.innerHTML = "Solo se permiten caractéres alfanuméricos";
        return false;//document.getElementById('btnGrabarReasignaTarea').disabled = true;
    }
    return true;
}

function validarDatosContacto(elementId, campo){
    var validate = true;
    var txtValor = document.getElementById(elementId).value;
    switch (campo) {
        case 'Correo':
            if(txtValor.length > 0)
            {
                validate = validateContactEmail();
            }
          break;
        case 'Celular':
            if(txtValor.length > 0)
            {
                validate = validateContactCelular();
            }
          break;
        case 'Convencional':
            if(txtValor.length > 0)
            {
                validate = validateContactConvencional();
            }
          break;
        case 'Nombre':
            if(txtValor.length > 0)
            {
                validate = validateContactNombre();
            }
          break;
        case 'Cargo':
            if(txtValor.length > 0)
            {
                validate = validateContactCargo();
            }
          break;
        default:
          console.log('');
      }
    
    return validate;
}

storePrefijoProvincias = new Ext.data.Store({
    total: 'total',
    pageSize: 200,
    proxy: {
        type: 'ajax',
        method: 'post',
        url: '../info_caso/getPrefijosTelefono',
        reader: {
            type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        }
    },
    fields:
        [
            {name: 'codigo', mapping: 'codigo'}
        ]
});



 //Obtener datos de contacto 
 //opcion: Reasignar, Cierre
function loadDatosContacto(idDetalle, opcion) {

    var dataParametro = {
        'idDetalle': idDetalle
    };
    var parametros = {
        'data': dataParametro,
        'op': 'getDatosContactoSeguimientoTarea'
    };

    $.ajax({
        data: JSON.stringify(parametros),
        url: urlGetSoporteProcesar,
        type: 'post',
        success: function (response) {
            var tieneRegistros = false;
            if (response.count > 0) {
                tieneRegistros = response.contactoCliente[0].tieneRegistros;
                var registroActual = response.contactoCliente[0];
                registroContacto = registroActual;
            }
            if (opcion == 'Reasignar' && registroContacto != null) {
                precargarDatosContactoCliente(registroContacto,'','');
            } else if (opcion == 'Cierre') {
                Ext.MessageBox.hide();
                FieldsDatosContacto = new Ext.form.FieldSet(
                    {
                        xtype: 'fieldset',
                        title: '&nbsp;<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b style="color:black";>Ingrese la información del cliente que lo atendió</b>',
                        //width: 500,
                        id: 'fieldsDatosContacto',
                        layout: 'fit',
                        items:
                            [
                                {
                                    xtype: 'combobox',
                                    id: 'comboConfirmacionCliente',
                                    name: 'comboConfirmacionCliente',
                                    store: myMenuContactoCliente,
                                    displayField: 'name',
                                    valueField: 'value',
                                    queryMode: "local",
                                    emptyText: '',
                                    fieldLabel: '¿La siguiente persona lo atendió?',
                                    labelWidth: 240,
                                    hidden: tieneRegistros ? false : true,
                                    height: 30,
                                    listeners:
                                    {
                                        select: function (combo) {
                                            deshabilitarDatosContactoCliente(false);
                                            if (combo.getValue() == 1) {
                                                if (registroContacto != null) {
                                                    precargarDatosContactoCliente(registroContacto, 'Finalizar-inputEl', 'Inhabilitar');
                                                }
                                            } else if (combo.getValue() == -1) {
                                                deshabilitarDatosContactoCliente(true);
                                                if (registroContacto != null) {
                                                    precargarDatosContactoCliente(registroContacto, 'Finalizar-inputEl', 'Inhabilitar');
                                                }
                                            } else {
                                                $("#cmbPrefijoTelefonoFinalizar-inputEl").val('');
                                                $("#txtNombreContactoFinalizar-inputEl,#txtCelularContactoFinalizar-inputEl,#txtCargoContactoFinalizar-inputEl,#txtCorreoContactoFinalizar-inputEl,#txtConvencionalFinalizar-inputEl").val('');
                                                Ext.getCmp('cmbPrefijoTelefonoFinalizar').readOnly = false;
                                            }
                                        },
                                        afterrender: function (checkbox) {
                                            Ext.getCmp('comboConfirmacionCliente').setValue(-1);
                                        }
                                    },
                                    forceSelection: true
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'txtNombreContactoFinalizar',
                                    fieldLabel: '* Nombre y Apellido',
                                    labelWidth: 150,
                                    name: 'nombreCliente',
                                    maxLength: 50,
                                    enforceMaxLength : true,
                                    validator: function (v) 
                                    {
                                        if(v.length==0)
                                        {
                                            return 'Ingrese Nombre y Apellido';
                                        }else if (!/^[A-Za-zÀ-ÿ\\u00f1\\u00d1\s]+$/.test(v) && v.length > 0)
                                        {
                                            return 'Solo se permiten caracteres de texto';
                                        } else
                                        {
                                            return true;
                                        }
                                    }
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'txtCelularContactoFinalizar',
                                    fieldLabel: '* Celular',
                                    labelWidth: 150,
                                    name: 'telefonoCliente',
                                    maxLength: 10,
                                    enforceMaxLength: 10,
                                    validator: function (v)
                                    {
                                        if(v.length==0)
                                        {
                                            return 'Ingrese número de celular';
                                        } else if (!/^[0-9]+$/.test(v) && v.length > 0) {
                                            return 'Se permiten solo números';
                                        } else if (v.length > 0 && v[0] != 0) {
                                            return 'El número de celular debe empezar con 0, ejemplo: 098XXXXXXX'
                                        } else if (v.length > 0 && v.length < 10) {
                                            return 'Se debe ingresar 10 digitos'
                                        } else {
                                            return true;
                                        }
                                    }
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'txtCargoContactoFinalizar',
                                    fieldLabel: 'Cargo/Área',
                                    labelWidth: 150,
                                    name: 'cargoCliente',
                                    allowBlank: true,
                                    maxLength: 50,
                                    enforceMaxLength : true,
                                    validator: function (v) 
                                    {
                                        if (!/^[A-Za-z0-9À-ÿ\\u00f1\\u00d1\s]+$/.test(v) && v.length > 0)
                                        {
                                            return 'Solo se permiten caracteres alfanuméricos';
                                        } else
                                        {
                                            return true;
                                        }
                                    }
                                },
                                {
                                    xtype: 'textfield',
                                    id: 'txtCorreoContactoFinalizar',
                                    fieldLabel: 'Correo',
                                    labelWidth: 150,
                                    name: 'correoCliente',
                                    validator: function (v)
                                    {
                                        if (!Ext.form.VTypes.email(v) && v.length > 0) {
                                            return 'Debe ingresar un correo electrónico válido.';
                                        } else {
                                            return true;
                                        }
                                    },
                                    allowBlank: true
                                },
                                {
                                    xtype: 'fieldcontainer',
                                    id: 'contenedorTelefonoFinalizar',
                                    fieldLabel: 'Convencional',
                                    labelWidth: 150,
                                    //width_: 470,
                                    layout: 'hbox',
                                    //disabled: tieneRegistros ? true : false,
                                    items: [
                                        {
                                            xtype: 'combobox',
                                            id: 'cmbPrefijoTelefonoFinalizar',
                                            name: 'prefijoNumeroClienteARecibir',
                                            store: storePrefijoProvincias,
                                            displayField: 'codigo',
                                            valueField: 'codigo',
                                            queryMode: "remote",
                                            emptyText: '',
                                            width: 40,
                                            //disabled: tieneRegistros ? true : false,
                                            listeners:
                                            {
                                                select: function (combo)
                                                {
                                                    if (combo.getValue() != null) {
                                                        Ext.getCmp('cmbPrefijoTelefonoFinalizar').setActiveError(false)
                                                    }
                                                }
                                            },
                                            forceSelection: true
                                        },
                                        {
                                            xtype: 'textfield',
                                            id: 'txtConvencionalFinalizar',
                                            name: 'convencionalCliente',
                                            width: 271,
                                            //disabled: tieneRegistros ? true : false,
                                            allowBlank: true,
                                            validator: function (v)
                                            {
                                                if (Ext.getCmp('cmbPrefijoTelefonoFinalizar').getValue() == null && v.length > 0) {
                                                    Ext.getCmp('cmbPrefijoTelefonoFinalizar').setActiveError('Seleccionar código de área')
                                                    return 'Debe seleccionar un código de área';
                                                } else if (!/^[0-9]+$/.test(v) && v.length > 0) {
                                                    return 'Se permiten solo números';
                                                } else if (v.length > 0 && v[0] != 2) {
                                                    return 'El número convencional debe empezar con 2, ejemplo: 29XXXXX'
                                                } else if (v.length > 0 && v.length < 7) {
                                                    return 'Se debe ingresar 7 digitos'
                                                } else {
                                                    return true;
                                                }
                                            },
                                            maxLength: 7,
                                            enforceMaxLength: 7,
                                        }]
                                }
                            ]
                    });

                winDatosContacto = Ext.create('Ext.window.Window', {
                    title: 'Registro de Contacto del Cliente',
                    modal: true,
                    width: 500,
                    height: 310,
                    resizable: false,
                    //layout: 'fit',
                    items: [
                        FieldsDatosContacto],
                    buttonAlign: 'center',
                    buttons: [btnGuardarDatosContacto, btnCancelarDatosContacto],
                    closable: false
                }).show();

                if (registroContacto != null && tieneRegistros) {
                    precargarDatosContactoCliente(registroContacto, 'Finalizar-inputEl','InhabilitarTodo');
                }else if(!tieneRegistros){ 
                    precargarDatosContactoCliente(registroContacto, 'Finalizar-inputEl','');
                }
            }


        },
        failure: function (response) {
            Ext.MessageBox.hide();
            showAlertModals('alertaValidaReasignaTarea', 'Error al realizar acción. Por favor informar a Sistemas.');
        }
    });
}

function deshabilitarDatosContactoCliente(value){
    Ext.getCmp('txtConvencionalFinalizar').setDisabled(value).setActiveError();
    Ext.getCmp('txtCorreoContactoFinalizar').setDisabled(value).setActiveError();
    Ext.getCmp('txtCargoContactoFinalizar').setDisabled(value).setActiveError();
    Ext.getCmp('txtCelularContactoFinalizar').setDisabled(value).setActiveError();
    Ext.getCmp('txtNombreContactoFinalizar').setDisabled(value).setActiveError();
    Ext.getCmp('contenedorTelefonoFinalizar').setDisabled(value).setActiveError();
    Ext.getCmp('cmbPrefijoTelefonoFinalizar').setDisabled(value).setActiveError();
}

//acciones sobre los campos: Inhabilitar, InhabilitarTodo
function precargarDatosContactoCliente(datos, seccion, accion) {

    if (datos.nombre != 'NA') {
        $('#txtNombreContacto' + seccion).val(datos.nombre);
        if (accion == 'Inhabilitar') {
            document.getElementById('txtNombreContacto' + seccion).disabled = true;
        }
    }
    if (datos.celular != 'NA') {
        $('#txtCelularContacto' + seccion).val(datos.celular);
        if (accion == 'Inhabilitar') {
            document.getElementById('txtCelularContacto' + seccion).disabled = true;
        }
    }
    if (datos.cargo != 'NA') {
        $('#txtCargoContacto' + seccion).val(datos.cargo);
        if (accion == 'Inhabilitar') {
            document.getElementById('txtCargoContacto' + seccion).disabled = true;
        }
    }
    if (datos.correo != 'NA') {
        $('#txtCorreoContacto' + seccion).val(datos.correo);
        if (accion == 'Inhabilitar') {
            document.getElementById('txtCorreoContacto' + seccion).disabled = true;
        }
    }
    if (datos.convencional.length == 9) {
        $("#cmbPrefijoTelefono" + seccion).val(datos.convencional.substring(0, 2)).change();
        $('#txtConvencional' + seccion).val(datos.convencional.substring(2, 9));
        if (accion == 'Inhabilitar') {
            document.getElementById('cmbPrefijoTelefono' + seccion).disabled = true;
            document.getElementById('txtConvencional' + seccion).disabled = true;
            Ext.getCmp('cmbPrefijoTelefonoFinalizar').readOnly = true;
        }

    } else if (accion == 'Inhabilitar') {
        Ext.getCmp('cmbPrefijoTelefonoFinalizar').readOnly = false;
    }

    if(accion == 'InhabilitarTodo'){
        document.getElementById('txtNombreContacto' + seccion).disabled = true;
        document.getElementById('txtCelularContacto' + seccion).disabled = true;
        document.getElementById('txtCargoContacto' + seccion).disabled = true;
        document.getElementById('txtCorreoContacto' + seccion).disabled = true;
        document.getElementById('cmbPrefijoTelefono' + seccion).disabled = true;
        document.getElementById('txtConvencional' + seccion).disabled = true;
        Ext.getCmp('cmbPrefijoTelefonoFinalizar').readOnly = true;
    }
}

 
 //function loadCiudad(prefEmpresa,idCanton)
 function loadCiudad(idCombo,idInput,prefEmpresa,idCanton)
 {
     var parametros = {};
     var strPrefEmpresa = prefEmpresa;
     var txtCiudad = "";
     var paramQuery = "?empresa="+strPrefEmpresa+"&page=1&start=0&limit=200";
     $("#"+idCombo).html("<option value='-1' >Seleccione ciudad</option>");
     $.ajax({
             data :  parametros,
             url  :  url_ciudadPorEmpresa+paramQuery,
             type :  'post',
             success:  function (response) {
                 if(response.total > 0)
                 {                    
                     var items     = "<option value='-1' >Seleccione ciudad</option>";
                     var ciudades   = [];
                     var ciudadesIds   = [];
                     var arrayCiudades  = response.encontrados;
                     if(arrayCiudades.length > 0)
                     {
                         for(var i=0;i<arrayCiudades.length;i++)
                         {
                             items += "<option value='"+arrayCiudades[i].id_canton+"'>"+arrayCiudades[i].nombre_canton+"</option>";
                             ciudades.push(arrayCiudades[i].nombre_canton);
                             ciudadesIds.push(arrayCiudades[i].id_canton);
                             if(typeof idCanton !== 'undefined' && idCanton == arrayCiudades[i].id_canton)
                             {
                                 txtCiudad = arrayCiudades[i].nombre_canton;
                             }
                         }
                         $("#"+idCombo).html(items);
                         $('#'+idInput).removeAttr('disabled');
                         autocomplete(document.getElementById(idInput),document.getElementById(idCombo), ciudades,ciudadesIds);
                         if(typeof idCanton !== 'undefined')
                         {
                             $("#"+idCombo).val(idCanton);
                             $("#"+idInput).val(txtCiudad);
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
 
 //OnChange de select ciudad (form reasignar tarea)
 $("#txtCiudad").change(function() {
     $("#cmbDepartReasigna,#cmbEmpleadoReasigna,#cmbCuadrillaReasigna,#cmbContratistaReasigna").val('');
     $("#txtEmpleado,#txtCuadrilla,#txtContratista,#txtDepartment").val('');
     $("#txtEmpleado,#txtCuadrilla,#txtContratista,#txtDepartment").attr('disabled','disabled');
     $("#radio_empleado,#radio_cuadrilla,#radio_contratista").prop( "checked",false);
     $("#radio_empleado").prop( "checked",true);
     $(".divcmbResponsable").css('display','none');
     $("#divcmbEmpleadoReasigna").css('display','block');
     $("#chxRespuestaInmediata" ).prop( "checked", false);
 
     var strEmpresa = $("#cmbEmpresaReasigna").val();
     var intCiudad = $("#cmbCiudadReasigna").val();
     loadDepartamentos("cmbDepartReasigna","txtDepartment",strEmpresa,intCiudad);
 });
 
 
 //function loadDepartamentos(prefEmpresa,idCanton,idDepartamento)
 function loadDepartamentos(idCombo,idInput,prefEmpresa,idCanton,idDepartamento)
 {
     var parametros = {};
     var strPrefEmpresa = prefEmpresa;
     var intIdCanton = idCanton;
     var textDepartamento = '';
     var paramQuery = "?id_canton="+intIdCanton+"&empresa="+strPrefEmpresa+"&page=1&start=0&limit=200";
     $("#"+idCombo).html("<option value='-1' >Seleccione departamento</option>");
     $.ajax({
             data :  parametros,
             url  :  url_departamentoPorEmpresaCiudad+paramQuery,
             type :  'post',
             success:  function (response) {
                 if(response.total > 0)
                 {
                     var items     = "<option value='-1' >Seleccione departamento</option>";
                     departamentos   = [];
                     departamentosIds   = [];
                     var arrayDepartamentos  = response.encontrados;
                     if(arrayDepartamentos.length > 0)
                     {
                         for(var i=0;i<arrayDepartamentos.length;i++)
                         {
                             items += "<option value='"+arrayDepartamentos[i].id_departamento+"'>"+arrayDepartamentos[i].nombre_departamento+"</option>";
                             departamentos.push(arrayDepartamentos[i].nombre_departamento);
                             departamentosIds.push(arrayDepartamentos[i].id_departamento);
                             if(typeof idDepartamento !== 'undefined' && idDepartamento == arrayDepartamentos[i].id_departamento)
                             {
                                 textDepartamento = arrayDepartamentos[i].nombre_departamento;
                             }
                         }
                         $("#"+idCombo).html(items);
                         $('#'+idInput).removeAttr('disabled');
                         autocomplete(document.getElementById(idInput),document.getElementById(idCombo), departamentos,departamentosIds);
                         if(typeof idDepartamento !== 'undefined')
                         {
                             $("#"+idCombo).val(idDepartamento);
                             $("#"+idInput).val(textDepartamento);
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
 
 //OnChange de select departamentos (form reasignar tarea)
 $("#txtDepartment").change(function() {
     $("#cmbEmpleadoReasigna,#cmbCuadrillaReasigna,#cmbContratistaReasigna").val('');
     $('#txtEmpleado,#txtCuadrilla,#txtContratista').val('');
     $('#txtEmpleado,#txtCuadrilla,#txtContratista').attr('disabled','disabled');
     $("#radio_empleado,#radio_cuadrilla,#radio_contratista").prop( "checked",false);
     $("#radio_empleado").prop( "checked",true);
     $(".divcmbResponsable").css('display','none');
     $("#divcmbEmpleadoReasigna").css('display','block');
     $("#chxRespuestaInmediata" ).prop( "checked", false);
     var strEmpresa = $("#cmbEmpresaReasigna").val();
     var intCiudad = $("#cmbCiudadReasigna").val();
     var intDepartamento = $("#cmbDepartReasigna").val();
 
     loadEmpleados("cmbEmpleadoReasigna","txtEmpleado",strEmpresa,intCiudad,intDepartamento);
     loadCuadrillasXDepartamento('cmbCuadrillaReasigna','txtCuadrilla',intDepartamento);
 });
 
 //Función que carga los empleados (form reasignar tarea)
 
 function loadEmpleados(idCombo,idInput,prefEmpresa,idCanton,idDepartamento,idEmpleado)
 {
     var parametros = {};
     var strPrefEmpresa = prefEmpresa;
     var intIdCanton = idCanton;
     var intIdDepartamento = idDepartamento;
     var textEmpleado = '';
     var smsError = '';
     $('.smsErrorEmpleado').val('');
     var paramQuery = "?id_canton="+intIdCanton+"&empresa="+strPrefEmpresa+"&id_departamento="
                     +intIdDepartamento+"&departamento_caso=&page=1&start=0&limit=25";
     $("#"+idCombo).html("<option value='-1' >Seleccione empleado</option>");
     $.ajax({
             data :  parametros,
             url  :  url_empleadoPorDepartamentoCiudad+paramQuery,
             type :  'post',
             success:  function (response) {
                 var items     = "<option value='-1'>Seleccione empleado</option>";
                 empleados   = [];
                 empleadosIds   = [];
                 if(response.myMetaData.boolSuccess == 1)
                 {
                     var arrayEmpleados  = response.result.encontrados;
                     if(arrayEmpleados.length > 0)
                     {
                         for(var i=0;i<arrayEmpleados.length;i++)
                         {
                             items += "<option value='"+arrayEmpleados[i].id_empleado+"'>"+arrayEmpleados[i].nombre_empleado+"</option>";
                             empleados.push(arrayEmpleados[i].nombre_empleado);
                             empleadosIds.push(arrayEmpleados[i].id_empleado);
                             if(typeof idEmpleado !== 'undefined' && idEmpleado == arrayEmpleados[i].id_empleado)
                             { 
                                 textEmpleado = arrayEmpleados[i].nombre_empleado;
                             }
                         }
                         $('#'+idInput).removeAttr('disabled');
                         if(typeof idEmpleado !== 'undefined')
                         {
                             $("#"+idCombo).val(idEmpleado);
                             $("#"+idInput).val(textEmpleado);
                         }
                     }
                     else
                     {
                         smsError = 'Error al cargar los empleados. Por favor intente nuevamente.';
                     }
                 }
                 else
                 {
                     smsError = response.myMetaData.message;                    
                 }
                 if($("input[name=rdoTipoEmpleado]:checked").val() == 'Empleado' && smsError !== '')
                 {
                     showAlertModals('alertaValidaReasignaTarea',smsError);
                 }
                 $('.smsErrorEmpleado').val(smsError);
                 $("#"+idCombo).html(items); 
                 autocomplete(document.getElementById(idInput),document.getElementById(idCombo),empleados,empleadosIds);
             },
             failure: function(response){
                 smsError = 'Error al realizar acción. Por favor informar a Sistemas.';
                 showAlertModals('alertaValidaReasignaTarea',smsError);
                 $('.smsErrorEmpleado').val(smsError);
             }
     });
 }
 
 
 function loadCuadrillasXDepartamento(idCombo,idInput,idDepartamento,idCuadrilla)
 {
     var parametros = {};
     var textCuadrilla = '';
     var paramGet = buildDataGet({ departamento:idDepartamento,estado: 'Eliminado',origenD: 'Departamento'}); 
     var smsError = '';
     $('.smsErrorCuadrilla').val('');
     $("#"+idCombo).html("<option value='-1' >Seleccione cuadrilla</option>");
     $.ajax({
             data :  parametros,
             url  :  url_integrantesCuadrilla+paramGet,
             type :  'post',
             success:  function (response) {
                 var items     = "<option value='-1'>Seleccione cuadrilla</option>";
                 var cuadrillas   = [];
                 var cuadrillasIds   = [];
                 if(response.myMetaData.boolSuccess == 1)
                 {
                     var arrayCuadrilla  = response.result.encontrados;
                     if(arrayCuadrilla.length > 0)
                     {
                         for(var i=0;i<arrayCuadrilla.length;i++)
                         {
                             items += "<option value='"+arrayCuadrilla[i].idCuadrilla+"'>"+arrayCuadrilla[i].nombre+"</option>";
                             cuadrillas.push(arrayCuadrilla[i].nombre);
                             cuadrillasIds.push(arrayCuadrilla[i].idCuadrilla);
                             if(typeof idCuadrilla !== 'undefined' && idCuadrilla == arrayCuadrilla[i].idCuadrilla)
                             { 
                                 textCuadrilla = arrayCuadrilla[i].nombre;
                             }
                         }
                         $('#'+idInput).removeAttr('disabled');
                         if(typeof idCuadrilla !== 'undefined')
                         {
                             $("#"+idCombo).val(idCuadrilla);
                             $("#"+idInput).val(textCuadrilla);
                         }
                     }
                     else
                     {
                         smsError = 'Error al cargar las cuadrillas. Por favor intente nuevamente.';
                     }
                 }
                 else
                 {
                     smsError = response.myMetaData.message;                    
                 }
                 if($("input[name=rdoTipoEmpleado]:checked").val() == 'Cuadrilla' && smsError !== '')
                 {
                     showAlertModals('alertaValidaReasignaTarea',smsError); 
                 }
                 $('.smsErrorCuadrilla').val(smsError);
                 $("#"+idCombo).html(items); 
                 autocomplete(document.getElementById(idInput),document.getElementById(idCombo),cuadrillas,cuadrillasIds);
             },
             failure: function(response){
                 smsError = 'Error al realizar acción. Por favor informar a Sistemas.';
                 showAlertModals('alertaValidaReasignaTarea',smsError);
                 $('.smsErrorCuadrilla').val(smsError);
             }
     });
 }
 
 $("#txtCuadrilla").change(function() {
     var idCuadrilla = $("#cmbCuadrillaReasigna").val();
     if(idCuadrilla != '')
     {
        validarTabletPorCuadrilla(idCuadrilla,'cmbCuadrillaReasigna','txtCuadrilla')
     }     
 });
 
 function loadContratistas(idCombo,idInput,idContratista)
 {
     var parametros = {};
     var textContratista = '';
     var paramGet = buildDataGet({rol : 'Empresa Externa'}); 
     var smsError = '';
     $('.smsErrorContratista').val('');
     $("#"+idCombo).html("<option value='-1' >Seleccione Contratista</option>");
     $.ajax({
             data :  parametros,
             url  :  url_empresasExternas+paramGet,
             type :  'post',
             success:  function (response) {
                 var items     = "<option value='-1'>Seleccione...</option>";
                 var contratistas   = [];
                 var contratistasIds   = [];
                 if(response.myMetaData.boolSuccess == 1)
                 {
                     var arrayContratista  = response.result.encontrados;
                     if(arrayContratista.length > 0)
                     {
                         for(var i=0;i<arrayContratista.length;i++)
                         {
                             items += "<option value='"+arrayContratista[i].id_empresa_externa+"'>"+arrayContratista[i].nombre_empresa_externa+"</option>";
                             contratistas.push(arrayContratista[i].nombre_empresa_externa);
                             contratistasIds.push(arrayContratista[i].id_empresa_externa);
                             if(typeof idContratista !== 'undefined' && idContratista == arrayContratista[i].id_empresa_externa)
                             { 
                                 textContratista = arrayContratista[i].nombre_empresa_externa;
                             }
                         }
                         $('#'+idInput).removeAttr('disabled');
                         if(typeof idContratista !== 'undefined')
                         {
                             $("#"+idCombo).val(idContratista);
                             $("#"+idInput).val(textContratista);
                         }
                     }
                     else
                     {
                         smsError = 'Error al cargar las cuadrillas. Por favor intente nuevamente.';
                     }
                 }
                 else
                 {
                     smsError = response.myMetaData.message;                    
                 }
                 if($("input[name=rdoTipoEmpleado]:checked").val() == 'Contratista' && smsError !== '')
                 {
                     showAlertModals('alertaValidaReasignaTarea',smsError); 
                 }
                 $('.smsErrorContratista').val(smsError);
                 $("#"+idCombo).html(items); 
                 autocomplete(document.getElementById(idInput),document.getElementById(idCombo),contratistas,contratistasIds);
             },
             failure: function(response){
                 smsError = 'Error al realizar acción. Por favor informar a Sistemas.';
                 showAlertModals('alertaValidaReasignaTarea',smsError);
                 $('.smsErrorContratista').val(smsError);
             }
     });
 }
 
 //Check de respuesta inmediata (form reasignar tarea)
 
 $('#chxRespuestaInmediata').change(function () 
 {    
     var idDetalle = $("#intIdDetalleReasigna").val();
     $("#radio_empleado,#radio_cuadrilla,#radio_contratista").prop( "checked",false);
     $("#radio_empleado").prop( "checked",true);
     $(".divcmbResponsable").css('display','none');
     $("#cmbEmpleadoReasigna,#cmbCuadrillaReasigna,#cmbContratistaReasigna").val('');
     $("#txtEmpleado,#txtCuadrilla,#txtContratista").val('');
     $("#divcmbEmpleadoReasigna").css('display','block');
 
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
                     loadCiudad('cmbCiudadReasigna','txtCiudad',strPrefijoEmpresa,intCiudad);
                     loadDepartamentos("cmbDepartReasigna","txtDepartment",strPrefijoEmpresa,intCiudad,intIdDepartamento);
                     loadEmpleados("cmbEmpleadoReasigna","txtEmpleado",strPrefijoEmpresa,intCiudad,intIdDepartamento,strUsuarioRespuesta);
                     loadCuadrillasXDepartamento('cmbCuadrillaReasigna','txtCuadrilla',intIdDepartamento);  
                 }else{
                     $("#cmbEmpresaReasigna").val(strPrefijoSession);
                     loadCiudad('cmbCiudadReasigna','txtCiudad',strPrefijoSession,intCiudadSession);
                     loadDepartamentos("cmbDepartReasigna","txtDepartment",strPrefijoSession,intCiudadSession,intDepartamentoSession);
                     loadEmpleados("cmbEmpleadoReasigna","txtEmpleado",strPrefijoSession,intCiudadSession,intDepartamentoSession);
                     loadCuadrillasXDepartamento('cmbCuadrillaReasigna','txtCuadrilla',intDepartamentoSession);
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
 
 $('input:radio[name="rdoTipoEmpleado"]').change(function(){
     $(".divcmbResponsable").css('display','none');
     $("#cmbEmpleadoReasigna,#cmbCuadrillaReasigna,#cmbContratistaReasigna").val('');
     $("#txtEmpleado,#txtCuadrilla,#txtContratista").val('');
     $("#divcmb"+$(this).val()+"Reasigna").css('display','block');
     $("#chxRespuestaInmediata" ).prop( "checked", false);
     if($(this).val() === 'Empleado')
     {
         cuadrillaAsignada = "S";
         if($('.smsError'+$(this).val()).val() == '')
         {
             valorAsignacion = "empleado";  
         }
     }else if($(this).val() === 'Cuadrilla')
     {
         if($('.smsError'+$(this).val()).val() == '')
         {
             valorAsignacion = "cuadrilla";
         }
     }else
     {
         cuadrillaAsignada = "S";
         if($('.smsError'+$(this).val()).val() == '')
         {
             valorAsignacion = "contratista";
         }
     }
     if($('.smsError'+$(this).val()).val() !== '')
     {
         showAlertModals('alertaValidaReasignaTarea',$('.smsError'+$(this).val()).val());
     }
 });
 
 function validarTabletPorCuadrilla(idCuadrilla,idCombo,idInput)
 {
     $.ajax({
         data :  {cuadrillaId : idCuadrilla},
         url  :  url_validarTabletPorCuadrilla,
         type :  'post',
         success:  function (response) {
             response = JSON.parse(response);
             if(response.existeTablet == "S")
             {
                 cuadrillaAsignada = "S";
             }
             else
             {
                cuadrillaAsignada = "N";
                if(typeof idCombo === 'undefined' && typeof idInput === 'undefined')
                {
                    Ext.Msg.alert("Alerta","La cuadrilla "+response.nombreCuadrilla+" no posee tablet asignada. Realice la asignación de tablet correspondiente o \n\
                                        seleccione otra cuadrilla.");
                    Ext.getCmp('comboCuadrilla').setValue("");
                }else
                {
                    showAlertModals('alertaValidaReasignaTarea','La cuadrilla '+ response.nombreCuadrilla +' no posee tablet asignada. Realice la asignación de tablet correspondiente o \n\ seleccione otra cuadrilla.');
                    $('#'+idCombo).val('');
                    $('#'+idInput).val('');
                }      
             } 
         },
         failure: function(response){
            if(typeof idCombo === 'undefined' && typeof idInput === 'undefined')
            {
                Ext.Msg.show({
                    title: 'Error',
                    msg: 'Error al realizar acción. Por favor informar a Sistemas.',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }else
            {
                showAlertModals('alertaValidaReasignaTarea','Error al realizar acción. Por favor informar a Sistemas.');
                $('#'+idCombo).val('');
                $('#'+idInput).val('');
            }             
         }
     });
 }
 
 
 //#######################################################
 //############## REPROGRAMAR TAREA ###########
 //#######################################################
 
 function mostrarReprogramarTarea(tarea,e)
 {
     var data = JSON.parse(tarea);
     var fechaActual = "";
     var HoraActual = "00:00";
     $("#txtDatetimepickerEjecReprogramar").val("");
     $('#divcmbHourEjecReprogramar,#divcmbMotivoReprogramar,#divtxtaObsReprogramar,#divcmbdDateEjecReprogramar').removeClass('has-error');
     $('#cmbMotivoReprogramar').val('-1');
     $('#alertaValidaReprogramarTarea').css('display', 'none');
     $('#txtaObsReprogramar').val("");
     spinnerLoadinButton('btnGrabarReprogramarTarea','hide');
     $('#reprogramarHal-tarea').html(''); 
 
     e.setAttribute("disabled", "disabled");
     setTimeout(function(){e.removeAttribute("disabled", "");}, 5000);
 
     var listaHoras     = "<option value='-1' >Seleccione Hora</option>"; 
     arrayHoras = buildHoras();
     for(var i=0;i<arrayHoras.length;i++)
     {
         listaHoras += "<option value='"+arrayHoras[i]+"'>"+arrayHoras[i]+"</option>"; 
     }
     $("#cmbHourEjecReprogramar").html(listaHoras);
     $('#divcmbMotivoReprogramar').css('display', 'none');
     if(data.id_caso !== 0)
     {
         $('#divcmbMotivoReprogramar').css('display', 'block');
     }
     var dataReprogramar = {"id_caso":data.id_caso,"intIdDetalleHist" : data.intIdDetalleHist,
                             "numero_tarea": data.numero_tarea,"nombre_tarea": data.nombre_tarea,
                             "nombre_proceso": data.nombre_proceso,"ref_asignado_nombre": data.ref_asignado_nombre,
                             "asignado_nombre": data.asignado_nombre,"id_detalle": data.id_detalle}
     var parametros = {};
     //Obtener la fecha y hora del servidor
     $.ajax({
         data :  parametros,
         url  :  url_obtenerFechaServer,
         type :  'post',
         success:  function (response) {
             if(response.success)
             {
                 if (data.tareaEsHal && data.tipoAsignado.toUpperCase() === 'CUADRILLA') {
                     var htmlHalAsigna = getHtmlhalAsigna('reprogramar',
                                         response.fechaActual,
                                         JSON.stringify(data));
                     $('#reprogramarHal-tarea').html(htmlHalAsigna);     
                     $('#reprogramar-tarea').css('display','none');               
                     $('#reprogramarHal-tarea').css('display','block');
                     $('#modalReprogramarTarea').modal('show'); 
              
                 } else {
                     fechaActual = response.fechaActual;
                     HoraActual = response.horaActual;
                     $('#reprogramarHal-tarea').css('display','none');               
                     $('#reprogramar-tarea').css('display','block');
                     $("#txtTareaInicialReprogramar").text(data.nombre_tarea);
                     document.getElementById('txtDatetimepickerEjecReprogramar').value = fechaActual;
                     document.getElementById('cmbHourEjecReprogramar').value = HoraActual;
                     
                     objTareaJson  = Ext.JSON.encode(dataReprogramar).replace(/"/g, '\\"');
                     strClick = "reprogramarTarea(\""+objTareaJson+"\")";
                     $('#btnGrabarReprogramarTarea').attr('onclick',strClick);
                     $('#modalReprogramarTarea').modal('show'); 
                 }
             }
             else
             {
                 showModalMensajeCustom({tipo:'error',mensaje: response.error,btnOkText:'Cerrar'});
             }
 
         },
         failure: function(response){
             showModalMensajeCustom({tipo:'error',mensaje: "Error al realizar acción. Por favor informar a Sistemas.",btnOkText:'Cerrar'});
         }
     });   
 }
 
 function reprogramarTarea(objTarea)
 {
     var data = JSON.parse(objTarea);
     var motivoReprogramar = ($('#cmbMotivoReprogramar').val() === '-1')?'':$('#cmbMotivoReprogramar').val();
     
     if($('#divcmbMotivoReprogramar').css('display') !== 'none' && data.id_caso !== 0 && motivoReprogramar === '')
     {
         showAlert("alertaValidaReprogramarTarea","divcmbMotivoReprogramar","Por favor seleccione un motivo");   
     }
     else
     {
         var fechaEjecucion = $('#txtDatetimepickerEjecReprogramar').val()+ 'T00:00';
         var horaEjecucion = $('#txtDatetimepickerEjecReprogramar').val()+'T'+$('#cmbHourEjecReprogramar').val();
         var observacion = $('#txtaObsReprogramar').val();
      
         
         var strOnclick="javascript:gridTareas.ajax.url(url_listGridTarea+paramGet).load();";
         
         var objParametrosSms = '';
         objParametro = {
             id_detalle       : data.id_detalle,
             fe_ejecucion     : fechaEjecucion,
             ho_ejecucion     : horaEjecucion,
             observacion      : observacion,
             motivo           : motivoReprogramar,
             intIdDetalleHist : data.intIdDetalleHist,
             numeroTarea: data.numero_tarea,
             nombre_tarea: data.nombre_tarea,
             nombre_proceso: data.nombre_proceso,
             asignado_nombre: data.ref_asignado_nombre,
             departamento_nombre: data.asignado_nombre
             };
         $.ajax({
             data :  objParametro,
             url  :  'reprogramarTarea',
             type :  'post',
             beforeSend: function () {
                 spinnerLoadinButton('btnGrabarReprogramarTarea','show');
             },
             success:  function (response) {
                 spinnerLoadinButton('btnGrabarReprogramarTarea','hide');
                 
                 if (!response.success && !response.seguirAccion) 
                 {
                     $('#modalReprogramarTarea').modal('hide');
                     objParametrosSms = {tittle:'Mensaje',btnOkOnClick:strOnclick, btnOkText:'Cerrar',tipo:'error',mensaje:response.mensaje};
                     showModalMensajeCustom(objParametrosSms);
                     return;
                 }
 
                 if (response.success)
                 {
                     $('#modalReprogramarTarea').modal('hide');
                     if(response.mensaje != "cerrada")
                     {
                         objParametrosSms = {tittle:'Mensaje',btnOkOnClick:strOnclick, btnOkText:'OK',tipo:'success',mensaje:'Se reprogramó la tarea.'};
                     }
                     else
                     {
                         objParametrosSms = {tittle:'Mensaje',btnOkText:'Cerrar',tipo:'error',
                                             mensaje:'La tarea se encuentra Cerrada, por favor consultela nuevamente'};
                     }
                     
                     showModalMensajeCustom(objParametrosSms);
                 }
                 else
                 {
                     showAlertModals("alertaValidaReprogramarTarea",response.mensaje);
                 }
             },
             failure: function(response){
                 spinnerLoadinButton('btnGrabarReprogramarTarea','hide');
                 showAlertModals("alertaValidaReprogramarTarea",response.mensaje); 
             }
         });
     }
 }
 
 
 function getHtmlhalAsigna(accion,fechaActua,objTarea)
 {
     nIntentos = 0;
     var data = JSON.parse(objTarea);
     var idDetalle = data.id_detalle;
     var numeroTarea = data.numero_tarea;
     var checked = (data.atenderAntes !== 'NO' ? 'checked' : '');
     var textAccion = accion === 'reprogramar' ? 'Reprogramar' : 'Reasignar';
     
     var listaHoras     = ""; 
     arrayHoras = buildHoras();
     for(var i=0;i<arrayHoras.length;i++)
     {
         listaHoras += "<option value='"+arrayHoras[i]+"'>"+arrayHoras[i]+"</option>"; 
     }
 
     var htmlAsigna = '<div class="modal-body" >'+
                         '<div class="panel panel-info">'+
                             '<div class="panel-heading">'+
                                 '<h3 class="panel-title">Notificación HAL</h3>'+
                             '</div>'+
                             '<div class="panel-body" id="tex-notification-hal">'+
                                 '<!-- content notification-->'+ 
                             '</div>'+
                             '</div>'+
                             '<div class="panel panel-info">'+
                             '<div class="panel-heading">'+
                                 '<h3 class="panel-title">Asignación de Tareas HAL</h3>'+
                             '</div>'+
                             '<div class="panel-body">'+
                                 '<div class="alert alert-danger" id="alertaAsignacionHal" role="alert" style="display:none;" >'+
                                     '<strong></strong>'+
                                 '</div>'+
                                 '<form>'+
                                 '<div  class="row">'+
                                     '<div id="divcmbSolicitante" class="col-xs-6">'+
                                     '<label for="cmbSolicitante" class="control-label lbtoppadding">Solicitar '+ textAccion+':</label>'+
                                     '<select class="form-control" id="cmbSolicitante" onchange="onChangeSolicitaHal();">'+
                                         '<option value="-1" selected>Seleccione..</option>'+
                                         '<option value="C">Cliente</option>'+
                                         '<option value="E">Empresa</option>'+
                                     '</select>'+
                                     '</div>'+
                                     '<div id="divchbxSolicitane" class="col-xs-6" style="padding-top: 15px;padding-left: 50px;">'+
                                         '<div class="form-check form-check-inline">'+
                                         '<input class="form-check-input" type="radio" name="radioCuadrilla" id="radio_a" value="halDice" onchange="opcionesHal(1,\''+idDetalle+'\');">'+
                                         '<label class="form-check-label" for="radio_a">&nbsp; Mejor Opción </label>'+
                                         '</div>'+
                                         '<div class="form-check form-check-inline">'+
                                         '<input class="form-check-input" type="radio" name="radioCuadrilla" id="radio_b" value="halSugiere" onchange="opcionesHal(2,\''+idDetalle+'\');">'+
                                         '<label class="form-check-label" for="radio_b">&nbsp; Sugerencia</label>'+
                                         '</div>'+
                                     '</div>'+
                                 '</div></br>'+
                                 
                                 '<div id="divSugerenciaHal" style="display: none;">'+
                                     '<div class="row" id="divHalDice" style="display: none;">'+
                                     '<div class="divHalDice" style="padding: 0px 15px;">'+
                                         '<div class="panel panel-default">'+
                                         '<!-- Default panel contents -->'+
                                         '<div class="panel-heading">Sugerencia de Hal</div>'+
                                         '<div style="height: 120px;overflow: scroll;">'+
                                         '<table class="table table-striped table-bordered table-hover">'+
                                             '<thead>'+
                                             '<tr>'+
                                                 '<th class="bg-header-titlle" style="vertical-align: inherit;"><strong>Fecha Disponible</strong></th>'+
                                                 '<th class="bg-header-titlle" style="vertical-align: inherit;"><strong>Hora Inicio</strong></th>'+
                                                 '<th class="bg-header-titlle" style="vertical-align: inherit;"><strong>Mensaje</strong></th>'+
                                                 '<th class="bg-header-titlle" style="vertical-align: inherit;"><strong>Reserva (Seg)</strong></th>'+
                                             '</tr>'+
                                             '</thead>'+
                                             '<tbody id="tbodyHalDice">'+
                                             '</tbody>'+
                                         '</table>'+
                                         '</div>'+
                                         '</div>'+
                                     '</div>'+
                                     '</div>'+
                                     '<div class="row" id="divHalSugiere" style="display: block;">'+
                                     '<div class="divHalSugiere" style="padding: 0px 15px;">'+
                                         '<div style="border: 1px solid #ddd;">'+
                                         '<div style="color: #333;background-color: #f5f5f5;padding: 10px 5px;border-bottom: 1px solid #ddd;">Sugerencias</div>'+
                                         '<div class="row" style="padding: 0px 5px;">'+
                                             '<div id="divcmbdDateSolicitaHal" class="divdatetimepickerSolicitaHal col-xs-5">'+
                                             '<label for="datetimepickerSolicitaHal" class="control-label lbtoppadding">Fecha solicitada</label>'+
                                             '<div class="input-group date" id="datetimepickerSolicitaHal"> '+
                                                 '<input type="text" class="form-control" id="txtDatetimepickerSolicitaHal" value = ""/>'+
                                                 '<span class="input-group-addon">'+
                                                     '<span class="glyphicon glyphicon-calendar"></span>'+
                                                 '</span>'+
                                             '</div>'+
                                             '<script>'+
                                             '$("#datetimepickerSolicitaHal").datetimepicker({ '+
                                             '    format: "YYYY-MM-DD", '+
                                             '    minDate: today '+
                                             '});'+
                                             '</script>'+
                                             '</div>'+
                                             '<div id="divcmbHourSolicitaHal" class="divcmbHourSolicitaHal col-xs-3" >'+
                                             '<label for="cmbHourSolicitaHal" class="control-label lbtoppadding">Hora</label>'+
                                             '<select class="form-control" id="cmbHourSolicitaHal">'+
                                                 '<option value="-1" selected></option>'+
                                                 listaHoras+
                                             '</select>'+
                                             '</div>'+
                                             '<div id="divbtnSugerencia" class="col-xs-4" style="text-align: right;padding-top: 8px;"></br>'+
                                             '<button type="button" class="btn btn-success" id="btnConsultaSugerencia" onclick="consultaSugerencia(\''+idDetalle+'\');"><span class="text-btn">Nueva Sugerencia</span></button>'+
                                             '</div>'+
                                         '</div></br>'+
                                         '</div>'+
                                         '<div style="height: 120px;overflow: scroll;">'+
                                         '<table class="table table-striped table-bordered table-hover" >'+
                                             '<thead style="background-color: #f5f5f5;">'+
                                             '<tr>'+
                                                 '<th class="bg-header-titlle" style="vertical-align: inherit;"><strong></strong></th>'+
                                                 '<th class="bg-header-titlle" style="vertical-align: inherit;"><strong>Fecha Disponible</strong></th>'+
                                                 '<th class="bg-header-titlle" style="vertical-align: inherit;"><strong>Hora Inicio</strong></th>'+
                                                 '<th class="bg-header-titlle" style="vertical-align: inherit;"><strong>Mensaje</strong></th>'+
                                                 '<th class="bg-header-titlle" style="vertical-align: inherit;"><strong>Reserva (Seg)</strong></th>'+
                                             '</tr>'+
                                             '</thead>'+
                                             '<tbody id="tbodyHalSugiere">'+
                                             '</tbody>'+
                                         '</table>'+
                                         '</div>'+
                                     '</div></br>'+
                                     '</div>'+
                                     '<div class="row">'+
                                     '<div class="form-check" style="padding: 0px 15px;">'+
                                         '<label class="form-check-label"><b>¿De existir disponibilidad, el cliente desea ser atendido antes de la fecha acordada?</b></label> &nbsp;&nbsp;'+
                                         '<input class="form-check-input" type="checkbox" id="cboxAtenderAntes" name="cboxAtenderAntes" value="S" '+checked+'>'+
                                     '</div>'+
                                     '</div>'+
                                 '</div>'+
                                 '</form>'+
                             '</div>'+
                             '</div>'+
                         '</div>'+
                         '<div class="modal-footer">'+
                         '<button type="button" class="btn btn-default" data-dismiss="modal" onclick="notificarHal()">Cerrar</button>'+
                         '<button type="button" class="btn btn-primary" id="btnGrabarAsignacionHal" onclick="grabarAsignacionHal(\''+idDetalle+'\',\''+numeroTarea+'\',\''+accion+'\');"><span class="text-btn">Guardar</span></button>'+
                         '</div>';
     
     return htmlAsigna;
 }
 
 function opcionesHal(tipo,idDetalle)
 {
     tipoHal       = tipo;
     var solicitante = $('#cmbSolicitante').val();
     var paramIntentos = 0;
     $('#tex-notification-hal').html('');
     if(solicitante == '-1')
     {
         showAlertModals("alertaAsignacionHal",'Debe escoger un <b>Solicitante</b>.'); 
         return false;
     }
     if(tipo == '1')
     {
         paramIntentos = 1;
         $('#divSugerenciaHal').css('display','block');
         $('#divHalSugiere').css('display','none');
         $('#divHalDice').css('display','block');
         
     }else{
         nIntentos = nIntentos + 1;
         paramIntentos = nIntentos;
         $('#divSugerenciaHal').css('display','block');
         $('#divHalDice').css('display','none');
         $('#divHalSugiere').css('display','block');
         $('#txtDatetimepickerSolicitaHal').val('');
         $('#cmbHourSolicitaHal').val('-1');
     }
     var valueGet = {'nIntentos':paramIntentos,'idDetalle':idDetalle,'fechaSugerida':'',
                     'horaSugerida':'','solicitante':solicitante,'tipoHal':tipo,
                     'page':1,'start':0,'limit':1000};
     var paramGet = buildDataGet(valueGet); 
     $("#tbodyHalDice").html("");
     $("#tbodyHalSugiere").html("");
     var haldice     = "<tr><td colspan='5' style='text-align:center;'>Sin datos para mostrar, Por favor leer la Notificación HAL</td></tr>";
     var halSugiere     = "<tr><td colspan='6' style='text-align:center;'>Sin datos para mostrar, Por favor leer la Notificación HAL</td></tr>";
     $.ajax({
         data :  {},
         url  :  url_getIntervalosHal+paramGet,
         type :  'post',
         beforeSend: function () {
             $("#tbodyHalDice").html(haldice);
             $("#tbodyHalSugiere").html(halSugiere);
         },
         success:  function (response) {
             var boolExiste = (typeof response.intervalos === 'undefined') ? false :
                                  (typeof response.mensaje === 'undefined') ? false : true;
             if (boolExiste) {
                 var mensaje = response.mensaje;
                 if (mensaje !== null || mensaje !== '') {
                     $('#tex-notification-hal').html(mensaje);
                 } else {
                     $('#tex-notification-hal').html('');
                 }
                 if(response.intervalos.length >0)
                 {   haldice = "";
                     halSugiere=""; 
                     var intervalos = response.intervalos;
                     var valueCheckbox = '';
                     var i;
                     if(tipo == 1)
                     {
                         for(i=0;i<intervalos.length;i++)
                         {
                             valueCheckbox = intervalos[i].idSugerencia+'#'+intervalos[i].fecha+'#'+intervalos[i].horaIni+'#'+intervalos[i].fechaVigencia;
                             haldice += "<tr class='trSugiereHal'>";
                             haldice += "<td> <input class='idSugHalDice' type='hidden' value='"+valueCheckbox+"'> "+intervalos[i].fecha+"</td>";
                             haldice += "<td>"+intervalos[i].horaIni+"</td>";
                             haldice += "<td>"+intervalos[i].fechaTexto+"</td>";
                             haldice += "<td>"+intervalos[i].segTiempoVigencia+"</td>";
                             haldice += "</tr>";
                         }
                     }else
                     {
                         for(i=0;i<intervalos.length;i++)
                         {
                             valueCheckbox = intervalos[i].idSugerencia+'#'+intervalos[i].fecha+'#'+intervalos[i].horaIni+'#'+intervalos[i].fechaVigencia;
                             halSugiere += '<tr class="trSugiereHal">';
                             halSugiere += '<td><input class="form-check-input cboxSugerencia" type="checkbox" id="cboxSugerencia-'+intervalos[i].idSugerencia+'" name="cboxSugerencia" value="'+valueCheckbox+'" onchange="onlyCheck(this)"></td>';
                             halSugiere += "<td>"+intervalos[i].fecha+"</td>";
                             halSugiere += "<td>"+intervalos[i].horaIni+"</td>";
                             halSugiere += "<td>"+intervalos[i].fechaTexto+"</td>";
                             halSugiere += "<td>"+intervalos[i].segTiempoVigencia+"</td>";
                             halSugiere += '</tr>';
                         }
                     } 
                 }
 
             } else {
                 $('#tex-notification-hal').html('<b style="color:red";>Error interno, Comunique a Sistemas..!!</b>');
             }
             $("#tbodyHalDice").html(haldice);
             $("#tbodyHalSugiere").html(halSugiere);
         },
         failure: function(response){
             showAlertModals("alertaAsignacionHal",response.mensaje); 
         }
     });
 }
 
 function consultaSugerencia(idDetalle)
 {
     var solicitante = $('#cmbSolicitante').val();
     var fechaSugerida = "";
     var horaSugerida = "";
     if($('#txtDatetimepickerSolicitaHal').val() !== '')
     {
         fechaSugerida = $('#txtDatetimepickerSolicitaHal').val()+ 'T00:00';
     }
     if($('#cmbHourSolicitaHal').val() !== '-1')
     {
        horaSugerida = $('#txtDatetimepickerSolicitaHal').val()+'T'+$('#cmbHourSolicitaHal').val()+':00';
     }    
     
     nIntentos = nIntentos + 1;
     var valueGet = {'nIntentos':nIntentos,'idDetalle':idDetalle,'fechaSugerida':fechaSugerida,
                     'horaSugerida':horaSugerida,'solicitante':solicitante,'tipoHal':tipoHal,
                     'page':1,'start':0,'limit':1000};
     var paramGet = buildDataGet(valueGet); 
     var haldice     = "<tr><td colspan='5' style='text-align:center;'>Sin datos para mostrar, Por favor leer la Notificación HAL</td></tr>";
     var halSugiere  = "<tr><td colspan='6' style='text-align:center;'>Sin datos para mostrar, Por favor leer la Notificación HAL</td></tr>";
     $.ajax({
         data :  {},
         url  :  url_getIntervalosHal+paramGet,
         type :  'post',
         beforeSend: function () {
             $("#tbodyHalSugiere").html(halSugiere);
             spinnerLoadinButton('btnConsultaSugerencia','show');
         },
         success:  function (response) {
             spinnerLoadinButton('btnConsultaSugerencia','hide');
             var boolExiste = (typeof response.intervalos === 'undefined') ? false :
                                  (typeof response.mensaje === 'undefined') ? false : true;
             if (boolExiste) {
                 var mensaje = response.mensaje;
                 if (mensaje !== null || mensaje !== '') {
                     $('#tex-notification-hal').html(mensaje);
                 } else {
                     $('#tex-notification-hal').html('');
                 }
                 if(response.intervalos.length >0)
                 {   
                     halSugiere=""; 
                     var intervalos = response.intervalos;
                     var valueCheckbox = '';
                     for(var i=0;i<intervalos.length;i++)
                     {
                         valueCheckbox = intervalos[i].idSugerencia+'#'+intervalos[i].fecha+'#'+intervalos[i].horaIni+'#'+intervalos[i].fechaVigencia;
                         halSugiere += '<tr class="trSugiereHal">';
                         halSugiere += '<td><input class="form-check-input cboxSugerencia" type="checkbox" id="cboxSugerencia-'+intervalos[i].idSugerencia+'" name="cboxSugerencia" value="'+valueCheckbox+'" onchange="onlyCheck(this)"></td>';
                         halSugiere += "<td>"+intervalos[i].fecha+"</td>";
                         halSugiere += "<td>"+intervalos[i].horaIni+"</td>";
                         halSugiere += "<td>"+intervalos[i].fechaTexto+"</td>";
                         halSugiere += "<td>"+intervalos[i].segTiempoVigencia+"</td>";
                         halSugiere += '</tr>';
                     } 
                 }
             } else {
                 $('#tex-notification-hal').html('<b style="color:red";>Error interno, Comunique a Sistemas..!!</b>');
             }
             $("#tbodyHalDice").html(haldice);
             $("#tbodyHalSugiere").html(halSugiere);            
         },
         failure: function(response){
             showAlertModals("alertaAsignacionHal",response.mensaje); 
             spinnerLoadinButton('btnConsultaSugerencia','hide');
         }
     });
 }
 
 function onlyCheck(e)
 {
     $('input.cboxSugerencia').not(e).prop('checked', false); 
 }
 
 function onChangeSolicitaHal(){
 
         $('#divSugerenciaHal').css('display','none');
         $('#divHalSugiere').css('display','none');
         $('#divHalDice').css('display','none');
         $("#radio_a").prop('checked', false);
         $("#radio_b").prop('checked', false);
         $("#cboxAtenderAntes").prop('checked', false);
         $('#tex-notification-hal').html('');
 };
 
 function grabarAsignacionHal(idDetalle,numeroTarea,accion)
 {
     var atenderAntes = "N";
     var solicitante = $('#cmbSolicitante').val();
     var elementSave = '';
     if(document.getElementById('cboxAtenderAntes').checked)
     {
         atenderAntes = "S";
     }
     var checkeSugerencia = $('input[type="radio"][name="radioCuadrilla"]:checked').val();
     if(typeof checkeSugerencia === 'undefined' || solicitante === '-1')
     {
         showAlertModals("alertaAsignacionHal",'Debe escoger una opción de Hal...!!'); 
         return false;
     }
     if (tipoHal == 1)
     {
         var boolHalDice =false;
         $('td .idSugHalDice').each(function(){
             boolHalDice = true;
             var dataSugerencia = $(this).val().split("#");
             idSugerencia   = dataSugerencia[0];
             fechaEjecucion = dataSugerencia[1];
             horaEjecucion  = dataSugerencia[2];
             fechaVigencia  = dataSugerencia[3];
             elementSave = $(this);
         });
         if(boolHalDice === false)
         {
             showAlertModals("alertaAsignacionHal",'No se obtuvieron sugerencias de hal...!!'); 
             return false;   
         }
     }
     else
     {
         if($('td .cboxSugerencia').length == 0)
         {
             showAlertModals("alertaAsignacionHal",'No se obtuvieron sugerencias de hal...!!'); 
             return false;
         }
         var dataSugerencia = $('input[name="cboxSugerencia"]:checked').val();
         if (typeof dataSugerencia === 'undefined')
         {
             showAlertModals("alertaAsignacionHal",'Debe escoger una fecha...!!'); 
             return false;
         }
         dataSugerencia = dataSugerencia.split("#");
         idSugerencia   = dataSugerencia[0];
         fechaEjecucion = dataSugerencia[1];
         horaEjecucion  = dataSugerencia[2];
         fechaVigencia  = dataSugerencia[3];
         elementSave = $('input[name="cboxSugerencia"]:checked');
     }
     var objParametrosSms = '';    
     var strOnclick="javascript:gridTareas.ajax.url(url_listGridTarea+paramGet).load();";
     objParametro = {
         idDetalle     : idDetalle,
         idSugerencia  : idSugerencia,
         fechaVigencia : fechaVigencia
         };
     $.ajax({
         data :  objParametro,
         url  :  url_confirmarReservaHal,
         type :  'post',
         beforeSend: function () {
             spinnerLoadinButton('btnGrabarAsignacionHal','show');
         },
         success:  function (response) {
             spinnerLoadinButton('btnGrabarAsignacionHal','hide');
             if (response.success)
             {
                 segTiempoVigencia   = response.segTiempoVigencia;
                 fechaTiempoVigencia = response.fechaTiempoVigencia;
                 horaTiempoVigencia  = response.horaTiempoVigencia;
                 var objParametro = {
                     idDetalle      : idDetalle,
                     idComunicacion : numeroTarea,
                     idSugerencia   : idSugerencia,
                     atenderAntes   : atenderAntes,
                     'solicitante'  : solicitante
                 }
                 $.ajax({
                     data :  objParametro,
                     url  :  url_confirmarSugerenciaHal,
                     type :  'post',
                     beforeSend: function () {
                         spinnerLoadinButton('btnGrabarAsignacionHal','show');
                     },
                     success:  function (response) {
                         spinnerLoadinButton('btnGrabarAsignacionHal','hide');
                         if (response.success)
                         {
                             tipoAsignado         = response.tipoAsignado;
                             idAsignado           = response.idAsignado;
                             fechaEjecucion       = response.fecha;
                             horaEjecucion        = response.horaIni;
                             empleadoAsignado     = response.empleadoAsignado;
                             cuadrillaAsignada    = response.cuadrillaAsignada;
                             departamentoAsignado = response.departamentoAsignado;
                             if(accion == 'reprogramar')
                             {
                                 $('#modalReprogramarTarea').modal('hide');
                             }else
                             {
                                 $('#modalReasignarTarea').modal('hide');
                             }                                
                             objParametrosSms = {tittle:'Mensaje',btnOkOnClick:strOnclick, btnOkText:'OK',tipo:'success',mensaje:'Se asignó la tarea.'};
                             showModalMensajeCustom(objParametrosSms);
                         }
                         else
                         {
                             showAlertModals("alertaAsignacionHal",response.mensaje); 
                         }
                     },
                     failure: function(response){
                         spinnerLoadinButton('btnGrabarAsignacionHal','hide');
                         showAlertModals("alertaAsignacionHal",response.mensaje); 
                     }
                 });
             }
             else
             {
                 showAlertModals("alertaAsignacionHal",response.mensaje);
                 if (response.noDisponible)
                 {
                     eliminarSeleccionHal(elementSave,tipoHal);
                 }
             }
         },
         failure: function(response){
             spinnerLoadinButton('btnGrabarAsignacionHal','hide');
             showAlertModals("alertaAsignacionHal",response.mensaje); 
         }
     });
 }
 
 function eliminarSeleccionHal(e,tipo)
 {
     var haldice     = "<tr><td colspan='5' style='text-align:center;'>Sin datos para mostrar, Por favor leer la Notificación HAL</td></tr>";
     var halSugiere  = "<tr><td colspan='6' style='text-align:center;'>Sin datos para mostrar, Por favor leer la Notificación HAL</td></tr>"; 
     e.parents('.trSugiereHal').remove();
     if(tipo == 2)
     {
         if($('td .cboxSugerencia').length == 0)
         {
             $("#tbodyHalSugiere").html(halSugiere); 
         }
     }else
     {
         var boolHalDice =false;
         $('td .idSugHalDice').each(function(){
             boolHalDice = true;
         });
         if(boolHalDice === false)
         {
             $("#tbodyHalDice").html(haldice);
         }
     }
 }
 
 function notificarHal()
 {
     //Notificar a HAL al presionar botón Cerrar  
     var boolHalDice =false;
     var idSugerencias = '';
     nIntentos     = 0;
     $('td .idSugHalDice').each(function(){
         boolHalDice = true;
         var dataSugerencia = $(this).val().split("#");
         idSugerencias   = idSugerencias+dataSugerencia[0]+'|';
     });
 
     if (boolHalDice)
     {
         $.ajax({
             data :  {idSugerencia  : idSugerencias},
             url  :  urlNotificarCancelarHal,
             type :  'post',
             success:  function (response) {
                 //notificacion enviada          
             },
             failure: function(response){
                 //error notificacion
             }
         });
     }
 }
 
 
 //#######################################################
 //############## MOSTRAR DOCUMENTO ###########
 //#######################################################
 
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
                $("#tbodyMostrarArchivos").html(strTextNoData);
                 $('#modalVerArchivos').modal('show');                 
             }    
         },
         failure: function(response){
             showModalsMessageFinTarea('error','Error al realizar acción. Por favor informar a Sistemas.');
         }
     });    
 }
 
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
 
 //#######################################################
 //############## CARGAR DOCUMENTO ###########
 //#######################################################
 
 function subirMultipleAdjuntosTarea(id_detalle)
 {
    //reiniciar elementos modals
     $('#IdTarea').val('');
 
     var strInputFile = '<div class="entry input-group upload-input-group" style="margin-bottom: 10px; width: 100%;">'+
                             '<input class="form-control form-control-sm load-files-task" name="archivos[]" type="file"'+
                             'style="width: 80% !important;" onchange="enableAddFile(this)">'+
                             '<button class="btn btn-upload btn-success btn-add btn-load-file" type="button" style="margin-left: 10px;" disabled>'+ 
                             '<i class="fa fa-plus" style="margin: 0px !important;"> </i> </button>'+
                         '</div>';
     $('#divInputFiles').html(strInputFile);
     $('#IdTarea').val(id_detalle);
     strClick = "uploadFilesTareas('formFileTarea')";
     $('#btnCargarArchivo').attr('onclick',strClick);   
     $('#modalCagarArchivos').modal('show'); 
 }
 
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
 
 //Función para customizar los inputs de tipo file del subir archivos tareas
 
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
 
 // Función para deshabilitar boton de agregar nuevo input para cargar archivo
 
 function enableAddFile(e)
 {
     elNexButton = e.nextElementSibling;
     if(elNexButton.classList.contains("btn-add"))
     {
        elNexButton.removeAttribute("disabled");   
     }
 }
 
 //#######################################################
 //############## REINTENTO TAREA SYSCLOUD ###########
 //#######################################################
 
 function confirmarTareaSysCloud(objData) {
 var data = JSON.parse(objData); 
 var smsConfirma = '¿Está seguro que desea reintentar la creación de la tarea en Sys Cloud-Center?';
 
 var arrayRequest = {
     asignado: data.ref_asignado_nombre,
     depAsignado: data.asignado_nombre,
     numeroTarea: data.numero_tarea,
     nombreTarea: data.nombre_tarea,
     nombreProceso: data.nombre_proceso,
     fechaAsignado: data.fechaEjecucion,
     horaAsignado: data.horaEjecucion,
     //observacion: data.observacion
 };
 
 var objParameters  = JSON.stringify(arrayRequest);
 objParameters  = objParameters.replace(/"/g, '\\"');
 
 var strOnclick="javascript:reintentoTareaSysCloud(\""+objParameters+"\",\""+data.textObservacion+"\",this);";
 var objParametrosSms = {tittle:'Mensaje',btnOkOnClick:strOnclick,btnCancelOnClick:'',dismissBtnOk:'N',
                             btnCancelText:'No', btnOkText:'Si',tipo:'info',mensaje:smsConfirma,btnCancel:'S'};
 showModalMensajeCustom(objParametrosSms);
 }
 
 function reintentoTareaSysCloud(parametros,textObservacion,e) {
 var data = JSON.parse(parametros);
 data.observacion =  $('#'+textObservacion).html()
 var srtData = JSON.stringify(data);
 var idBtn = e.id; 
 $.ajax({
     data : {'datos':srtData},
     url  :  urlAjaxReintentoTareaSysCloud,
     type :  'post',
     beforeSend: function () {
         spinnerLoadinButton(idBtn,"show")
     }, 
     success:  function (response) {
         
         if(response.status)
         {
             showModalMensajeCustom({tipo:'success',mensaje: response.message,btnOkText:'Cerrar'});
             gridTareas.ajax.url(url_listGridTarea+paramGet).load();
         }
         else
         {
             showModalMensajeCustom({tittle:'Error',tipo:'error',mensaje: response.message,btnOkText:'Cerrar'});   
         }
         spinnerLoadinButton(idBtn,"hide")                          
     },
     failure: function(response){
         showModalMensajeCustom({tittle:'Error',tipo:'error',mensaje: 'Error al realizar acción. Por favor informar a Sistemas.',
                                     btnOkText:'Cerrar'});
         spinnerLoadinButton(idBtn,"hide") 
     }
 });
 
 
 }
 
 //#######################################################
 //############## CONFIRMAR ENLACE ###########
 //#######################################################
 
 function confirmaIpServicioSoporte(objData) {
 var data = JSON.parse(objData); 
 var smsConfirma = '¿Está seguro que desea confirmar el enlace?';
 
 var strEmpresaTarea     = data.strEmpresaTarea;
 var idEmpresa;
 if(strEmpresaTarea == 'TN')
 {
     idEmpresa = '10';
 }
else if (strEmpresaTarea == 'MD') 
{
     idEmpresa = '18';
 }else {
    idEmpresa = '33';
 }
 
 var arrayRequest = {
     idEmpresa: idEmpresa,
     idComunicacion: data.numero_tarea,
     idDetalle: data.id_detalle,
     strCodigoProgreso: 'CONFIRMA_IP_SERVICIO',
     idServicio: data.servicioId,
     strOrigenProgreso: 'WEB'
 };
 
 var objParameters  = JSON.stringify(arrayRequest);
 objParameters  = objParameters.replace(/"/g, '\\"');
 
 var strOnclick="javascript:confirmarIpServicioSoporte(\""+objParameters+"\",this);";
 var objParametrosSms = {tittle:'Mensaje',btnOkOnClick:strOnclick,btnCancelOnClick:'',dismissBtnOk:'N',
                             btnCancelText:'No', btnOkText:'Si',tipo:'info',mensaje:smsConfirma,btnCancel:'S'};
 showModalMensajeCustom(objParametrosSms);
 }
 
 function confirmarIpServicioSoporte(parametros,e) {
 var data = JSON.parse(parametros);
 var srtData = JSON.stringify(data);
 var idBtn = e.id; 
 $.ajax({
     data : {'data':srtData},
     url  :  urlConfirmarIpServicioSoporteAction,
     type :  'post',
     beforeSend: function () {
         spinnerLoadinButton(idBtn,"show")
     }, 
     success:  function (response) {
         
         if(response.status === 'OK')
         {
             showModalMensajeCustom({tipo:'success',mensaje: response.mensaje,btnOkText:'Cerrar'});
             gridTareas.ajax.url(url_listGridTarea+paramGet).load();
         }
         else
         {
             showModalMensajeCustom({tittle:'Error',tipo:'error',mensaje: response.mensaje,btnOkText:'Cerrar'});   
         }
         spinnerLoadinButton(idBtn,"hide")                          
     },
     failure: function(response){
         showModalMensajeCustom({tittle:'Error',tipo:'error',mensaje: 'Error al realizar acción. Por favor informar a Sistemas.',
                                     btnOkText:'Cerrar'});
         spinnerLoadinButton(idBtn,"hide") 
     }
 });
 
 }
 
 //#######################################################
 //############## VALIDAR ENLACE ###########
 //#######################################################
 
 function confirmarServicioSoporte(objData) {
 var data = JSON.parse(objData); 
 var smsConfirma = '¿Está seguro que desea validar el enlace?';
 
 var strEmpresaTarea = data.strEmpresaTarea;
 var idEmpresa;
 if (strEmpresaTarea == 'TN') {
     idEmpresa = '10';
 } 
 else if (strEmpresaTarea == 'MD') 
 {
     idEmpresa = '18';
 }else 
 {
    idEmpresa = '33';
 }
 var arrayRequest = {
     idEmpresa: idEmpresa,
     idComunicacion: data.numero_tarea,
     idDetalle: data.id_detalle,
     casoId: data.id_caso,
     servicioId: data.idServicioVrf,
     user: data.loginSesion,
     ultimaMilla: data.ultimaMillaSoporte,
     empresaCod: strEmpresaTarea,
     departamentoId: data.departamentoId,
     idServicio: data.idServicio,
     strOrigenProgreso: 'WEB'
 };
 
 var objParameters  = JSON.stringify(arrayRequest);
 objParameters  = objParameters.replace(/"/g, '\\"');
 
 var strOnclick="javascript:validarServicioSoporte(\""+objParameters+"\",this);";
 var objParametrosSms = {tittle:'Mensaje',btnOkOnClick:strOnclick,btnCancelOnClick:'',dismissBtnOk:'N',
                             btnCancelText:'No', btnOkText:'Si',tipo:'info',mensaje:smsConfirma,btnCancel:'S'};
 showModalMensajeCustom(objParametrosSms);
 }
 
 function validarServicioSoporte(parametros,e) {
 var data = JSON.parse(parametros);
 var srtData = JSON.stringify(data);
 var idBtn = e.id; 
 $.ajax({
     data : {'data':srtData},
     url  :  urlValidarServicioSoporteAction,
     type :  'post',
     beforeSend: function () {
         spinnerLoadinButton(idBtn,"show")
     }, 
     success:  function (response) {
         var objData = response;
         var message = objData.message;
         var statusPing = objData.data.statusPing;
         var packageSent = null;
         var packageReceived = null;
         var packageLost = null;
         if (objData.data.packages!= null ) {
             packageSent = objData.data.packages.sent;
             packageReceived = objData.data.packages.received;
             packageLost = objData.data.packages.lost;
             if(!statusPing)
             {
                 message = "Error";
             }
         }  else {
             message = "Error";
         }
         var latency = "No se encontro latencia";
         var metric = "";
         var ipCliente = "No se encuentra datos del cliente."
 
         if (objData.data.ipClient != null) {
             ipCliente = objData.data.ipClient;
         }
         if (objData.data.latency != null && (statusPing || objData.data.latency.avg > 0)) {
             metric = "ms";
             latency = "Latencia media : " + objData.data.latency.avg + metric;
         } else if (statusPing && (packageSent == packageReceived && packageLost == 0)) {
             metric = "ms";
             latency = "Latencia media : " + packageLost + metric;
         }else if(objData.data.latency != null && !statusPing ) {
             latency = "No se encontro latencia." ;
         }
         var messageContet = "";
         if (packageSent > 0) {
 
             if(objData.data.strTieneProgConfirIPserv != null && 
                 objData.data.strTieneProgConfirIPserv == 'SI'){
                     messageContet =
                     "</br><b>Verificaci&oacute;n del Enlace </b> </br>" + "</br>"+ objData.data.message + " </br>";
             }
             else{
                 var result = objData.data.message.indexOf("%");
             
                 if (result != -1)
                 {   
                     var arrayMensaje    = objData.data.message.split("%");
                     var strPartUnaMens  = arrayMensaje[0];
                     var strPartDosMens  = arrayMensaje[1];
                     messageContet =
                     "</br><b>Verificaci&oacute;n del Enlace </b> </br>" + "</br>"+  strPartUnaMens + "</br> <p style='color:red' >" + strPartDosMens + " </p> </br> <b>Ping :</b> " + ipCliente + "<br><table style='height: 60px; border-color: #d8d8d8;' border='1' width='291' margin-top : '10' ><thead><tr><td style='width: 88px; padding: 5px; text-align: center;'>ENVIADOS</td><td style='width: 88px;  text-align: center;'>RECIBIDOS</td><td style='width: 88px;  text-align: center;'>PERDIDOS</td></tr></thead><tbody><tr><td style='width: 88px;  text-align: center;'>" + packageSent + "</td><td style='width: 88px;  text-align: center;'>" + packageReceived + "</td><td style='width: 88px;  text-align: center;'>" + packageLost + "</td></tr></tbody></table></br><p style='text-align: center;'> " + latency + "<br>";
                 }
                 else
                 {
                     messageContet =
                     "</br><b>Verificaci&oacute;n del Enlace </b> </br>" + "</br>"+ objData.data.message + " </br><b>Ping :</b> " + ipCliente + "<br><table style='height: 60px; border-color: #d8d8d8;' border='1' width='291' margin-top : '10' ><thead><tr><td style='width: 88px; padding: 5px; text-align: center;'>ENVIADOS</td><td style='width: 88px;  text-align: center;'>RECIBIDOS</td><td style='width: 88px;  text-align: center;'>PERDIDOS</td></tr></thead><tbody><tr><td style='width: 88px;  text-align: center;'>" + packageSent + "</td><td style='width: 88px;  text-align: center;'>" + packageReceived + "</td><td style='width: 88px;  text-align: center;'>" + packageLost + "</td></tr></tbody></table></br><p style='text-align: center;'> " + latency + "<br>";
                 } 
             }
 
             message = message + messageContet;
         } else {
             messageContet = objData.data.message;
             message = "</br>" + message + "</br>" + messageContet;
         }
 
         if (statusPing) 
         {
             showModalMensajeCustom({tipo:'none',mensaje: message,btnOkText:'Cerrar'});
             gridTareas.ajax.url(url_listGridTarea+paramGet).load();
         }
         else
         {
             showModalMensajeCustom({tittle:'Error',tipo:'error',mensaje: message,btnOkText:'Cerrar'});   
         }
         spinnerLoadinButton(idBtn,"hide")                          
     },
     failure: function(response){
         showModalMensajeCustom({tittle:'Error',tipo:'error',mensaje: 'Error al realizar acción. Por favor informar a Sistemas.',
                                     btnOkText:'Cerrar'});
         spinnerLoadinButton(idBtn,"hide") 
     }
 });
 }
 
 //#######################################################
 //############## PERMITE CREAR KML ###########
 //#######################################################
 
 function confirmaCrearKml(objData) {
     var data = JSON.parse(objData);
     var smsConfirma = '¿Está seguro que desea permitir crear KML desde el móvil?';
     var strEmpresaTarea = data.strEmpresaTarea;
     var idEmpresa;
     if (strEmpresaTarea == 'TN') {
         idEmpresa = '10';
     } 
     else if (strEmpresaTarea == 'MD') 
     {
        idEmpresa = '18';
    }else 
    {
       idEmpresa = '33';
    }
     var arrayRequest = {
         idEmpresa: idEmpresa,
         idComunicacion: data.numero_tarea,
         idDetalle: data.id_detalle,
         casoId: data.id_caso,
         servicioId: data.idServicioVrf,
         user: data.loginSesion,
         ultimaMilla: data.ultimaMilla,
         empresaCod: strEmpresaTarea,
         departamentoId: data.departamentoId,
         idServicio: data.idServicio,
     };
     var objParameters  = JSON.stringify(arrayRequest);
     objParameters  = objParameters.replace(/"/g, '\\"');
 
     var strOnclick="javascript:permitirCrearKml(\""+objParameters+"\",this);";
     var objParametrosSms = {tittle:'Mensaje',btnOkOnClick:strOnclick,btnCancelOnClick:'',dismissBtnOk:'N',
                                 btnCancelText:'No', btnOkText:'Si',tipo:'info',mensaje:smsConfirma,btnCancel:'S'};
     showModalMensajeCustom(objParametrosSms);
 }
 
 function permitirCrearKml(parametros,e) {
     var data = JSON.parse(parametros);
     var srtData = JSON.stringify(data);
     var idBtn = e.id; 
     $.ajax({
         data : {'data':srtData},
         url  :  urlPermiteCrearKmlAction,
         type :  'post',
         beforeSend: function () {
             spinnerLoadinButton(idBtn,"show")
         }, 
         success:  function (response) {
             
             if(response.status == 200)
             {
                 showModalMensajeCustom({tipo:'success',mensaje: response.message,btnOkText:'Cerrar'});
                 gridTareas.ajax.url(url_listGridTarea+paramGet).load();
             }
             else
             {
                 showModalMensajeCustom({tittle:'Error',tipo:'error',mensaje: response.message,btnOkText:'Cerrar'});   
             }
             spinnerLoadinButton(idBtn,"hide")                          
         },
         failure: function(response){
             showModalMensajeCustom({tittle:'Error',tipo:'error',mensaje: 'Error al realizar acción. Por favor informar a Sistemas.',
                                         btnOkText:'Cerrar'});
             spinnerLoadinButton(idBtn,"hide") 
         }
     });
 }
 
 //#######################################################
 //############## CANCELAR/RECHAZAR/ANULAR TAREA ###########
 //#######################################################
 
 function validaCancelarTarea(objTarea)
 {
     var data = JSON.parse(objTarea);
     if(data.cerrarTarea == "S")
     {
         var parametros = {'id_detalle'           : data.id_detalle,
                             'accionTarea'        : data.accionTarea,
                             'id_caso'            : data.id_caso,
                             'nombre_tarea'       : data.nombre_tarea,
                             'intIdDetalleHist'   : data.intIdDetalleHist,
                             'numero_tarea'        : data.numero_tarea,
                             'nombre_proceso'     : data.nombre_proceso,
                             'ref_asignado_nombre': data.ref_asignado_nombre,
                             'asignado_nombre'    : data.asignado_nombre,
                             'casoPerteneceTN'    : data.casoPerteneceTN};
 
         var objParameters  = JSON.stringify(parametros);  
         objParameters  = objParameters.replace(/"/g, '\\"'); 
         var strOnclick="javascript:cancelarTarea(\""+objParameters+"\",this);";
         $('#btnGrabarCancelaTarea').attr('onClick',strOnclick);
         $('#accionCancelaTitle').text('');
         $('#txtaObsTareaCanc').val('');
         spinnerLoadinButton('btnGrabarCancelaTarea',"hide") 
         $('#txtTareaCancela').text(data.nombre_tarea);
         var tipo = data.accionTarea =='cancelada'? "Cancelar": (data.accionTarea=='anulada') ? "Anular" : "Rechazar";
         $('#accionCancelaTitle').text(tipo);
         $('#modalCancelarTarea').modal('show');
 
     }
     else
     {
         showModalMensajeCustom({tittle:'Alerta',tipo:'info',mensaje: 'Esta tarea no se puede cancelar / rechazar debido que posee una o más '+
                                         'subtareas asociadas, por favor \n\ cerrar las tareas asociadas a la tarea principal.',
                                         btnOkText:'Cerrar'});
     }
 
 }
 
 function cancelarTarea(objTarea,e)
 {
     var data = JSON.parse(objTarea);
 
     var parametros = {'id_detalle'         : data.id_detalle,
                       'observacion'        : $('#txtaObsTareaCanc').val(),
                       'tipo'               : data.accionTarea,
                       'id_caso'            : data.id_caso,
                       'nombreTarea'        : data.nombre_tarea,
                       'intIdDetalleHist'   : data.intIdDetalleHist,
                       'numeroTarea'        : data.numero_tarea,
                       'nombre_proceso'     : data.nombre_proceso,
                       'asignado_nombre'    : data.ref_asignado_nombre,
                       'departamento_nombre': data.asignado_nombre};
 
     var strOnclick="javascript:gridTareas.ajax.url(url_listGridTarea+paramGet).load();";
     var idBtn = e.id; 
     $.ajax({
         data : parametros,
         url  :  strUrlCancelarTarea,
         type :  'post',
         beforeSend: function () {
             spinnerLoadinButton(idBtn,"show")
         }, 
         success:  function (response) {
             
             spinnerLoadinButton(idBtn,"hide")
             if (!response.success && !response.seguirAccion) 
             {
                 $('#modalCancelarTarea').modal('hide');
                 objParametrosSms = {tittle:'Alerta',btnOkOnClick:strOnclick, btnOkText:'Cerrar',tipo:'error',mensaje:response.mensaje};
                 showModalMensajeCustom(objParametrosSms);
                 return;
             }
 
             if (response.success)
             {
                 $('#modalCancelarTarea').modal('hide');
                 if(response.mensaje != "cerrada")
                 {
                     if (data.accionTarea == 'rechazada')
                     {
                         mensaje = 'Se rechazo la tarea.';
                     }
                     else if(data.accionTarea == 'anulada')
                     {
                         mensaje = 'Se anuló la tarea.';
                     }
                     else
                     {
                         mensaje = 'Se cancelo la tarea.';
                     }
                     //Cuando se cancela la tarea y esta pertenece a un caso
                     if (data.accionTarea !== 'rechazada' && data.id_caso !== 0 && response.tareasAbiertas === 0 && !data.casoPerteneceTN)
                     {
                         var esCancelada = true;
                         strOnclick = strOnclick+'validarDatosCierreCaso('+data.id_caso+','+esCancelada+');';
                     }
                     objParametrosSms = {tittle:'Mensaje',btnOkOnClick:strOnclick, btnOkText:'OK',tipo:'success',mensaje:mensaje};
                 }
                 else
                 {
                     objParametrosSms = {tittle:'Alerta',btnOkText:'Cerrar',tipo:'error',
                                         mensaje:'La tarea se encuentra Cerrada, por favor consultela nuevamente'};
                 }                
                 showModalMensajeCustom(objParametrosSms);
             }
             else
             {
                 showAlertModals("alertaValidaCancelaTarea",response.mensaje);
             }
         },
         failure: function(response){
             $('#modalCancelarTarea').modal('hide');
             showModalMensajeCustom({tittle:'Error',tipo:'error',mensaje: 'Error al realizar acción. Por favor informar a Sistemas.',
                                     btnOkText:'Cerrar'});
             spinnerLoadinButton(idBtn,"hide") 
         }
     });
 
 }
 
 function validarDatosCierreCaso(id_caso,esCancelada)
 {
     //variable para finallizar caso
     var conn = new Ext.data.Connection
     ({
         listeners: 
         {
             'beforerequest':
             {
                 fn: function(con, opt) 
                 {
                     Ext.get(document.body).mask('Loading...');
                 },
                 scope: this
             },
             'requestcomplete': 
             {
                 fn: function(con, res, opt)
                 {
                     Ext.get(document.body).unmask();
                 },
                 scope: this
             },
             'requestexception': 
             {
                 fn: function(con, res, opt) 
                 {
                     Ext.get(document.body).unmask();
                 },
                 scope: this
             }
         }
     });
     obtenerDatosCasosCierre(id_caso, conn, esCancelada);
 }
 
 //#######################################################
 //############## FINALIZAR TAREA (se mantiene diseño anterior) ###########
 //#######################################################
  
 function validaFinalizarTarea(tarea,e)
 {
     e.setAttribute("disabled", "disabled");
     setTimeout(function(){e.removeAttribute("disabled", "");}, 5000);
 
     var objTarea = JSON.parse(tarea);
 
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
                                                         
                     if (rs !== -1)
                     {
 
                         if(objTarea.permiteRegistroActivos === true && ((objTarea.id_caso !== 0) || (objTarea.esInterdepartamental === true)))
                         {                             
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
                                             finesTarea = response.categoriaTareas;;
                                             finalizarTarea(objTarea.id_detalle, objTarea, fechaActual,
                                                                         json.horaActual, objTarea.tipoAsignado, objTarea.asignado_id);  
                                                                         
                                             
                                             // Funcion que crea la pantalla de indisponibilidad, de manera que permanezca oculta
                                             //para poder obtener los valores a guardar y realizar un unico commit
                                             verIndisponibilidadTarea(objTarea);
                                             obtenerTiempoAfectacionIndisponibilidadTarea(objTarea);
                                             
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
                             finalizarTarea(objTarea.id_detalle, objTarea, fechaActual,
                                                         json.horaActual, objTarea.tipoAsignado, objTarea.asignado_id);  
                                                         
                             
                             // Funcion que crea la pantalla de indisponibilidad, de manera que permanezca oculta
                             //para poder obtener los valores a guardar y realizar un unico commit
                             verIndisponibilidadTarea(objTarea);
                             obtenerTiempoAfectacionIndisponibilidadTarea(objTarea);
                                                 
                         }                    
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
 
 var winFinalizarTarea;
 
 function finalizarTarea(id_detalle, data, fechaActual, horaActual,tipoAsignado, idCuadrilla)
 {
     if(data.cerrarTarea == "S")
     {
         if(data.iniciadaDesdeMobil == "S")
         {
             isCuadrilla = false;
                 comboTareaStore = new Ext.data.Store({
                 pageSize: 200,
                     total: 'total',
                     limit:1000,
                     proxy: {
                     type: 'ajax',
                         url : url_gridTarea,
                         reader:
                     {
                     type: 'json',
                         totalProperty: 'total',
                         root: 'encontrados'
                     },
                         extraParams:
                     {
                     nombre: '',
                         estado: 'Activo',
                         visible: 'SI',
                         caso:data.id_caso,
                         detalle:id_detalle //Se añade el id detalle para validacion de empresa
                     }
                     },
                     fields:
                     [
                     {name:'id_tarea', mapping:'id_tarea'},
                     {name:'nombre_tarea', mapping:'nombre_tarea'}
                     ]
                 });
                 
                 comboTarea = Ext.create('Ext.form.ComboBox', {
                 id:'comboTarea',
                     store: comboTareaStore,
                     displayField: 'nombre_tarea',
                     valueField: 'id_tarea',
                     height:30,
                     width:450,
                     border:0,
                     margin:0,
                     fieldLabel: 'Tarea Final ',
                     queryMode: "remote",
                     emptyText: ''
                 });
                 comboMotivoFinalizaStore = new Ext.data.Store({
                     pageSize: 200,
                         total: 'total',
                         limit:1000,
                         autoLoad: false,
                         proxy: {
                             type: 'ajax',
                                 url : url_gridMotivosCategoriaTareas,
                                 reader:
                             {
                                 type: 'json',
                                 totalProperty: 'total',
                                 root: 'data'
                             },
                             extraParams: {
                                 nombre: '',
                                 estado: 'Activo',
                                 visible: 'SI',
                                 caso:data.id_caso,
                                 detalle:id_detalle //Se añade el id detalle para validacion de empresa
                             }
                         },
                         fields:
                         [
                             {name:'idMotivo', mapping:'idMotivo'},
                             {name:'nombreMotivo', mapping:'nombreMotivo'}
                         ],
                         listeners: {
                             load: function(store) { 
                                 if (store.data.length > 0)
                                 {
                                     Ext.getCmp('comboMotivoFinaliza').value = "";
                                     Ext.getCmp('comboMotivoFinaliza').setRawValue("");
                                     Ext.getCmp('comboMotivoFinaliza').reset();
                                     Ext.getCmp('comboMotivoFinaliza').setVisible(true);
                                 }
                                 else
                                 {
                                     Ext.getCmp('comboMotivoFinaliza').value = "";
                                     Ext.getCmp('comboMotivoFinaliza').setRawValue("");
                                     Ext.getCmp('comboMotivoFinaliza').reset();
                                     Ext.getCmp('comboMotivoFinaliza').setVisible(false);
                                 }
                             }
                         }
                 });
                 comboMotivoFinaliza = Ext.create('Ext.form.ComboBox', {
                     id:'comboMotivoFinaliza',
                         store: comboMotivoFinalizaStore,
                         disabled: true,
                         displayField: 'nombreMotivo',
                         valueField: 'idMotivo',
                         height:30,
                         width:450,
                         border:0,
                         margin:0,
                         fieldLabel: 'Motivo ',
                         queryMode: "remote",
                         emptyText: ''
                     });
                 gridCuadrilla = null;
                 Ext.getCmp('comboTarea').setRawValue(data.nombre_tarea);
                 //data['intFinTareaId'] = data.id_tarea;
                 
                 var strRequiereMaterial     = '';
                 var strRequiereFibra        = '';
                 var strRequiereRutaFibra    = '';
                 data['strFinSelectNombre']  = '';
                 
                 if(data.permiteRegistroActivos === true && ((data.id_caso !== 0) || (data.esInterdepartamental === true)))
                 {
                     var arrayMenu1              = [];
                     
                     for(var i = 0; i < finesTarea.length; i++)
                     {
                         var categoriaPadre          = finesTarea[i];
                         var nombrePadre             = categoriaPadre.nombreCategoria;
                         var hijosPadre              = categoriaPadre.hijosCategoria;        
                         var arrayMenu2              = [];
                             
                             for(var j = 0; j < hijosPadre.length; j++)
                             {
                                 var categoriaHijo           = hijosPadre[j];
                                 var nombreHijo              = categoriaHijo.nombreHijo;
                                 var listaTareas             = categoriaHijo.listaTareas;    
                                 var arrayMenuTres           = [];
                                     
                                     for(var k = 0; k < listaTareas.length; k++)
                                     {
                                         var tarea = listaTareas[k];
                                         var numeroTarea         = tarea.numeroTarea;
                                         var nombreTarea         = tarea.nombreTarea;
                                         var nombreNivel2        = nombreHijo;
                                         var nombreNivel1        = nombrePadre;
                                         strRequiereMaterial     = tarea.requiereMaterial;
                                         strRequiereFibra        = tarea.requiereFibra;
                                         strRequiereRutaFibra    = tarea.requiereRutaFibra;
                                         var objFinLTres = 
                                         {
                                             text: nombreTarea,
                                             requiereMaterial:   strRequiereMaterial,
                                             requiereFibra:      strRequiereFibra,
                                             requiereRutaFibra:  strRequiereRutaFibra,
                                             numeroTarea:        numeroTarea,
                                             nombreNivel2:       nombreNivel2,
                                             nombreNivel1:       nombreNivel1,
                                         };
                                         arrayMenuTres.push(objFinLTres);  
                                     }
                                     
                                 var menuLevel3          = Ext.create('Ext.menu.Menu', {
                                     items: arrayMenuTres
                                 });
                                 
                                 var objFinLDos;
                                     if(arrayMenuTres.length > 0)
                                     {
                                         objFinLDos = 
                                         {
                                             text: nombreHijo,
                                             menu:menuLevel3
                                         };    
                                     }
                                     else
                                     {
                                         objFinLDos = {
                                             text: nombreHijo
                                         };    
                                     }
 
                                 menuLevel3.on('click', function(menu, item)
                                 {
                                     mostrarComboRegistroContactos(item.nombreNivel1, data);
                                     data['strFinSelectNombre']      = item.text;
                                     data['strRequiereMaterial']     = item.requiereMaterial;
                                     data['strRequiereFibra']        = item.requiereFibra;
                                     data['strRequiereRutaFibra']    = item.requiereRutaFibra;
                                     data['intFinTareaId']           = item.numeroTarea;
                                     Ext.getCmp('splitbutton_fin').setText(data['strFinSelectNombre']);
                                     Ext.getCmp('comboMotivoFinaliza').value = "";
                                     Ext.getCmp('comboMotivoFinaliza').setRawValue("");
                                     Ext.getCmp('comboMotivoFinaliza').reset();
                                     Ext.getCmp('comboMotivoFinaliza').setDisabled(false);
 
                                     comboMotivoFinalizaStore.proxy.extraParams = {
                                                                                      valor1: item.nombreNivel1, 
                                                                                      valor2: item.nombreNivel2, 
                                                                                      valor3: item.numeroTarea
                                                                                  };
                                     comboMotivoFinalizaStore.load();
                                 });
 
                                 arrayMenu2.push(objFinLDos);   
                             }
                             
                             var menuLevel2          = Ext.create('Ext.menu.Menu', {
                                 items: arrayMenu2
                             });
                             
                             var objFinLUno;
                             if(arrayMenu2.length > 0)
                             {
                                 objFinLUno = {
                                     text: nombrePadre,
                                     menu:menuLevel2
                                 };  
                             }
                             else
                             {
                                 objFinLUno = 
                                 {
                                     text: nombrePadre
                                 };  
                             }
 
                             arrayMenu1.push(objFinLUno);  
                     }
 
                     var mymenu      = new Ext.menu.Menu({
                                         items: arrayMenu1
                                     });
 
                      var cmbSeleccion = Ext.create('Ext.SplitButton', {
                         id:'splitbutton_fin',
                         xtype:  'splitbutton',
                         text:   'Seleccionar fin de tarea',
                         name:   'btnSelecFinTarea',
                         width:  270,
                         menu:   mymenu,
                         style: 'margin:0px 0px 10px 0px',
                     });
                     
                 }
                 
                 var cmbMostrarFinTarea;
                 var itemTareaInical;
                 
                 if( data.permiteRegistroActivos === true && ((data.id_caso !== 0) || (data.esInterdepartamental === true))) 
                 {
                     cmbMostrarFinTarea  = cmbSeleccion;
                     Ext.getCmp('comboMotivoFinaliza').setVisible(true);
                     itemTareaInical =  new Ext.form.FieldSet({
                         xtype: 'fieldset',
                         title: '',
                         style: 'border: none;padding:0px',
                         bodyStyle: 'padding:0px',
                         layout: {
                             type: 'table',
                             columns: 3,
                             pack: 'center'
                         },
                         items: [
                                 {
                                  xtype: 'displayfield',
                                  fieldLabel: 'Tarea Inicial:',
                                  id: 'tareaCaso',
                                  name: 'tareaCaso',
                                  value: data.nombre_tarea
                                 }, 
                                 {
                                     width: 40,
                                     layout: 'form',
                                     border: false,
                                     items: 
                                     [
                                             {
                                             xtype: 'displayfield'
                                             }
                                     ]
                                 },
                                 {
                                         xtype: 'checkboxfield',
                                         fieldLabel: 'Hereda Tarea Anterior',
                                         name: 'cbox_tarea_ini',
                                         id: 'cbox_tarea_ini',
                                         checked: false,
                                         labelWidth: 120,
                                         listeners: {
                                             afterrender: function(checkbox) {
                                                    checkbox.getEl().on('click', function() {
                                                     if(Ext.getCmp('cbox_tarea_ini').checked == true)
                                                     {
                                                         Ext.getCmp('splitbutton_fin').setDisabled(true);
                                                         Ext.getCmp('tareaAnterior').show();
                                                     }    
                                                     else
                                                     {
                                                         Ext.getCmp('splitbutton_fin').setDisabled(false);
                                                         Ext.getCmp('tareaAnterior').hide();
                                                     }
                                                  });
                                             }
                                          }
                                 }
                             ]
                     });
                 }
                 else
                 {   
                     cmbMostrarFinTarea  = comboTarea;    
                     Ext.getCmp('comboMotivoFinaliza').setVisible(false);
                     itemTareaInical =  new Ext.form.DisplayField({
                         id:'tareaCaso',
                         xtype:  'displayfield',
                         text:   'Tarea Inicial:',
                         fieldLabel: 'Tarea Inicial:',
                         name:   'tareaCaso',
                         value:  data.nombre_tarea
                     });
                 }
                     
                 if (tipoAsignado === 'CUADRILLA')
             {
             isCuadrilla = true;
                 //Grid Asignados de Cuadrilla
                 storeCuadrilla = new Ext.data.Store({
                 pageSize: 10,
                     total: 'total',
                     proxy: {
                     type: 'ajax',
                         url : url_getMiembrosCuadrilla,
                         reader: {
                         type: 'json',
                             totalProperty: 'total',
                             root: 'encontrados'
                         },
                         extraParams: {
                         idCuadrilla: idCuadrilla,
                         }
                     },
                     fields:
                     [
                     {name: 'id_persona_rol', mapping: 'id_persona_rol'},
                     {name: 'id_persona', mapping: 'id_persona'},
                     {name: 'nombre', mapping: 'nombre'},
                     ],
                     autoLoad: true
                 });
                 gridCuadrilla = Ext.create('Ext.grid.Panel', {
                 width: 450,
                     height: 170,
                     title:'Miembros de Cuadrilla',
                     store: storeCuadrilla,
                     viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
                         loadMask: true,
                         frame: false,
                         columns:
                         [
                         {
                         id: 'id_persona',
                             header: 'id_persona',
                             dataIndex: 'id_persona',
                             hidden: true,
                             hideable: false
                         },
                         {
                         id: 'id_persona_rol',
                             header: 'id_persona_rol',
                             dataIndex: 'id_persona_rol',
                             hidden: true,
                             hideable: false
                         },
                         {
                         id: 'nombre',
                             header: 'Nombre Tecnico',
                             dataIndex: 'nombre',
                             width: 440,
                             sortable: true
                         }
                         ],
                         bbar: Ext.create('Ext.PagingToolbar', {
                         store: storeCuadrilla,
                             displayInfo: true,
                             displayMsg: 'Mostrando {0} - {1} de {2}',
                             emptyMsg: "No hay datos que mostrar."
                         })
                     });
                 }
 
                 btnguardar2 = Ext.create('Ext.Button', {
                 text: 'Guardar',
                     cls: 'x-btn-rigth',
                     handler: function() {
                             //Obtener si es cuadrilla los empleados relacionados a la misma
                             if (tipoAsignado === 'CUADRILLA')
                             {
                             Ext.Msg.confirm('Confirmación', 'Esta seguro que desea cerrar la tarea con los integrantes de esta cuadrilla ?, caso contrario \n\
                                              notificar para que se actualice los integrantes', function(id) {
                             if (id === 'yes')
                             {
                                 guardarTareaFinalizada(data, id_detalle);
                             }
                             else
                             {
                                 winIndisponibilidadTarea.destroy();
                                 winFinalizarTarea.destroy();
 
                             }
                             }, this);
                             }
                             else
                             {
                                 guardarTareaFinalizada(data, id_detalle);
                             }
                     }
                 });
                     btncancelar2 = Ext.create('Ext.Button', {
                     text: 'Cerrar',
                         cls: 'x-btn-rigth',
                         handler: function() {
                             winIndisponibilidadTarea.destroy();
                             winFinalizarTarea.destroy();
 
                         }
                     });
 
                     btnGuardarDatosContacto = Ext.create('Ext.Button', {
                        text: 'Guardar',
                        cls: 'x-btn-rigth',
                        handler: function () {
                            if (registroContacto != null && registroContacto.tieneRegistros && (Ext.getCmp('comboConfirmacionCliente').getValue() == null ||
                                Ext.getCmp('comboConfirmacionCliente').getValue() == -1)) {
                                Ext.Msg.alert('Alerta', 'Por favor responda la pregunta ¿La siguiente persona lo atendió?');
                            } else if (!Ext.getCmp('txtNombreContactoFinalizar').isValid()
                                || Ext.getCmp('txtNombreContactoFinalizar').getValue() == ''
                            ) {
                                strOpError = Ext.getCmp('txtNombreContactoFinalizar').getValue() == '' 
                                ? "ingrese":"corriga";
                                strError = 'Por favor, '+ strOpError + ' nombre y apellido';
                                Ext.Msg.alert('Alerta', strError);
                            } else if (!Ext.getCmp('txtCelularContactoFinalizar').isValid()
                                || Ext.getCmp('txtCelularContactoFinalizar').getValue() == ''
                            ) {
                                strOpError = Ext.getCmp('txtCelularContactoFinalizar').getValue() == '' 
                                ?"ingrese":"corriga";
                                strError = 'Por favor, '+ strOpError + ' número de celular';
                                Ext.Msg.alert('Alerta', strError);
                            } else if (!Ext.getCmp('txtConvencionalFinalizar').isValid()
                                || !Ext.getCmp('txtCorreoContactoFinalizar').isValid()
                            ) {
                                Ext.Msg.alert('Alerta', 'Por favor, corriga el número convencional');
                            } else {
                                registroContactoModificado = Array();
                                registroContactoModificado.nombre = Ext.getCmp('txtNombreContactoFinalizar').getValue();
                                registroContactoModificado.celular = Ext.getCmp('txtCelularContactoFinalizar').getValue();
                                registroContactoModificado.cargo = Ext.getCmp('txtCargoContactoFinalizar').getValue();
                                registroContactoModificado.correo = Ext.getCmp('txtCorreoContactoFinalizar').getValue();
                                if (Ext.getCmp('cmbPrefijoTelefonoFinalizar').getValue() != null && Ext.getCmp('txtConvencionalFinalizar').getValue().length == 0) {
                                    Ext.getCmp('cmbPrefijoTelefonoFinalizar').setValue(null)
                                }
                                registroContactoModificado.convencional = Ext.getCmp('cmbPrefijoTelefonoFinalizar').getValue() + Ext.getCmp('txtConvencionalFinalizar').getValue();
                                winDatosContacto.removeAll();
                                winDatosContacto.setVisible(false);
                            }

                        }
                    });
       
                   btnCancelarDatosContacto = Ext.create('Ext.Button', {
                       text: 'Cerrar',
                       cls: 'x-btn-rigth',
                       handler: function () {
                           //winDatosContacto.remove();
                           winDatosContacto.removeAll();
                           winDatosContacto.setVisible(false);
                           Ext.getCmp('comboIngresoInstalacionesCliente').setValue(-1);
                       }
                   });
                     fieldsetCoordenadasTotal     = null;
                     fieldsetCoordenadasManga1    = null;
                     fieldsetCoordenadasManga2    = null;
                     fieldsetCoordenadasIncidente = null;
 
                      btnCoordenadasIncidente = Ext.create('Ext.button.Button', {
                          iconCls: 'button-grid-Gmaps',
                          itemId: 'ing_coordenadasIncidente',
                          text: ' ',
                          scope: this,
                          handler: function(){ muestraMapa(3);}
                      });
                      btnCoordenadasManga1 = Ext.create('Ext.button.Button', {
                          iconCls: 'button-grid-Gmaps',
                          itemId: 'ing_coordenadasManga1',
                          text: ' ',
                          scope: this,
                          handler: function(){ muestraMapa(1);}
                      });
                      btnCoordenadasManga2 = Ext.create('Ext.button.Button', {
                          iconCls: 'button-grid-Gmaps',
                          itemId: 'ing_coordenadasManga2',
                          text: ' ',
                          scope: this,
                          handler: function(){ muestraMapa(2);}
                      });
 
                     if (data.perteneceCaso && data.casoPerteneceTN && data.mostrarCoordenadas == "S")
                     {
                         widthCoordenadas = 300;
                         if(data.tareasManga == "S")
                         {
                             widthCoordenadas = "100%";
                             fieldsetCoordenadasManga1 = new Ext.form.FieldSet(
                             {
                                 xtype: 'fieldset',
                                 title: 'Manga 1',
                                 width: 230,
                                 items:
                                 [
                                     {
                                         layout: 'table',
                                         border: false,
                                         items:
                                         [
                                             {
                                                 width: 180,
                                                 layout: 'form',
                                                 border: false,
                                                 items:
                                                 [
                                                     {
                                                         xtype: 'displayfield'
                                                     }
                                                 ]
                                             },
                                             btnCoordenadasManga1
                                         ]
                                     },
                                     {
                                         width: 230,
                                         layout: 'form',
                                         border: false,
                                         items:
                                         [
                                             {
                                                 xtype: 'displayfield'
                                             }
                                         ]
                                     },
                                     {
                                     xtype: 'textfield',
                                         fieldLabel: '* Longitud:',
                                         maskRe: /[0-9-.]/,
                                         id: 'text_longitudManga1',
                                         name: 'text_longitudManga1',
                                         width: 200,
                                         value: '',
                                         readOnly: false
                                     },
                                     {
                                     xtype: 'textfield',
                                         fieldLabel: '* Latitud:',
                                         maskRe: /[0-9-.]/,
                                         id: 'text_latitudManga1',
                                         name: 'text_latitudManga1',
                                         width: 200,
                                         value: '',
                                         readOnly: false
                                     },
 
                                 ]
                             });
 
                             fieldsetCoordenadasManga2 = new Ext.form.FieldSet(
                             {
                                 xtype: 'fieldset',
                                 title: 'Manga 2',
                                 width: 230,
                                 items:
                                 [
                                     {
                                         layout: 'table',
                                         border: false,
                                         items:
                                         [
                                             {
                                                 width: 180,
                                                 layout: 'form',
                                                 border: false,
                                                 items:
                                                 [
                                                     {
                                                         xtype: 'displayfield'
                                                     }
                                                 ]
                                             },
                                             btnCoordenadasManga2
                                         ]
                                     },
                                     {
                                         width: 230,
                                         layout: 'form',
                                         border: false,
                                         items:
                                         [
                                             {
                                                 xtype: 'displayfield'
                                             }
                                         ]
                                     },
                                     {
                                     xtype: 'textfield',
                                         fieldLabel: '* Longitud:',
                                         maskRe: /[0-9-.]/,
                                         id: 'text_longitudManga2',
                                         name: 'text_longitudManga2',
                                         width: 200,
                                         value: '',
                                         readOnly: false
                                     },
                                     {
                                     xtype: 'textfield',
                                         fieldLabel: '* Latitud:',
                                         maskRe: /[0-9-.]/,
                                         id: 'text_latitudManga2',
                                         name: 'text_latitudManga2',
                                         width: 200,
                                         value: '',
                                         readOnly: false
                                     },
 
                                 ]
                             });
                         }
 
                         fieldsetCoordenadasIncidente = new Ext.form.FieldSet(
                         {
                             xtype: 'fieldset',
                             title: 'Incidente',
                             width: 230,
                             items:
                             [
                                 {
                                     layout: 'table',
                                     border: false,
                                     items:
                                     [
                                         {
                                             width: 180,
                                             layout: 'form',
                                             border: false,
                                             items:
                                             [
                                                 {
                                                     xtype: 'displayfield'
                                                 }
                                             ]
                                         },
                                         btnCoordenadasIncidente
                                     ]
                                 },
                                 {
                                     width: 230,
                                     layout: 'form',
                                     border: false,
                                     items:
                                     [
                                         {
                                             xtype: 'displayfield'
                                         }
                                     ]
                                 },
                                 {
                                 xtype: 'textfield',
                                     fieldLabel: '* Longitud:',
                                     maskRe: /[0-9-.]/,
                                     id: 'text_longitudI',
                                     name: 'text_longitudI',
                                     width: 200,
                                     value: '',
                                     readOnly: false
                                 },
                                 {
                                 xtype: 'textfield',
                                     fieldLabel: '* Latitud:',
                                     maskRe: /[0-9-.]/,
                                     id: 'text_latitudI',
                                     name: 'text_latitudI',
                                     width: 200,
                                     value: '',
                                     readOnly: false
                                 },
 
                             ]
                         });
 
                         fieldsetCoordenadasTotal = new Ext.form.FieldSet(
                         {
                             xtype: 'fieldset',
                             title: 'Seleccionar Coordenadas',
                             width: widthCoordenadas,
                             items:
                             [
                                 {
                                     layout: 'table',
                                     border: false,
                                     items:
                                     [
                                         fieldsetCoordenadasManga1,
                                         fieldsetCoordenadasManga2,
                                     ]
                                 },
                                 fieldsetCoordenadasIncidente
                             ]
                         });
                     }
                     if(data.tareasManga == "S")
                     {
                         width = 540;
                     }
                     else
                     {
                         width = 500;
                     }
 
                     if (data.perteneceCaso && !data.casoPerteneceTN )
                     {
                         height = 540;
                     }
                     else
                     {
                         if(data.mostrarCoordenadas == "S" && data.casoPerteneceTN && data.tareasManga == "S")
                         {
                             height = 890;
                         }
                         else if(data.mostrarCoordenadas == "S" && data.casoPerteneceTN && data.tareasManga == "N")
                         {
                             height = 690;
                         }
                         else
                         {
                             height = 490;
                         }
                     }
 
                     if (isCuadrilla && data.mostrarCoordenadas == "S" && data.casoPerteneceTN)
                     {
                         if (data.tareasManga == "S")
                         {
                             height = 970;
                         }
                         else
                         {
                             height = 865;
                         }
                     }
                     else if(isCuadrilla)
                     {
                         height = 680;
             }
             myMenuContactoCliente = Ext.create('Ext.data.Store', {
                 fields: ['value', 'name'],
                 data: [
                     { "value": -1, "name": "Seleccione..." },
                     { "value": 1, "name": "SI" },
                     { "value": 0, "name": "NO" }
                 ]
             });
             cmbDentroInstalaciones = new Ext.form.FieldSet(
                 {
                     xtype: 'fieldset',
                     title: '&nbsp;&nbsp;&nbsp;<i class="fa fa-user" aria-hidden="true"></i>&nbsp;<b style="color:black";>Registro de Contacto del Cliente</b>',
                     id: 'fieldsIngresoInstalacionesCliente',
                     hidden: true,
                     layout:'fit',
                     items:
                         [
                             {
                                 xtype: 'combobox',
                                 id: 'comboIngresoInstalacionesCliente',
                                 name: 'comboIngresoInstalacionesCliente',
                                 store: myMenuContactoCliente,
                                 displayField: 'name',
                                 valueField: 'value',
                                 queryMode: "local",
                                 emptyText: '',
                                 fieldLabel: '* ¿Ingresó a las instalaciones del cliente?',
                                 labelWidth: 240,
                                 //width: 40,
                                 listeners: {
                                     select: function (combo) {
                                        registroContactoModificado = null;
                                        Ext.getCmp('txtObservacionContactoCliente').setValue('');
                                         if (combo.getValue() == 1) {
                                             Ext.getCmp('txtObservacionContactoCliente').setVisible(false);
                                             Ext.MessageBox.wait("Verificando datos...");
                                             registroContacto = null;
                                             loadDatosContacto(data.id_detalle, 'Cierre');
                                         } else if (combo.getValue() == -1) {
                                            Ext.getCmp('txtObservacionContactoCliente').setVisible(false);
                                         } else {
                                             Ext.getCmp('txtObservacionContactoCliente').setVisible(true);
                                         }
                                     },
                                     afterrender: function (checkbox) {
                                         Ext.getCmp('comboIngresoInstalacionesCliente').setValue(-1);
                                     }
                                 },
                                 forceSelection: true
                             },
                             {
                                 xtype: 'textarea',
                                 id: 'txtObservacionContactoCliente',
                                 fieldLabel: '* Observación',
                                 name: 'observacion',
                                 rows: 2,
                                 cols: 160,
                                 allowBlank: false,
                                 hidden: true,

                             }

                         ]
                 });

             formPanel2 = Ext.create('Ext.form.Panel', {
                 bodyPadding: 5,
                 waitMsgTarget: true,
                 height: height,
                 width: width,
                 layout: 'fit',
                 fieldDefaults: {
                     labelAlign: 'left',
                     msgTarget: 'side'
                 },
                 items:
                     [
                         {
                             xtype: 'fieldset',
                             title: 'Información',
                             defaultType: 'textfield',
                             items:
                                 [
                                     itemTareaInical,
                                     {
                                         id: 'tareaAnterior',
                                         xtype: 'displayfield',
                                         fieldLabel: 'Tarea Final:',
                                         text: 'Tarea Final:',
                                         name: 'tareaFinalCaso',
                                         hidden: true,
                                         value: data.nombreTareaAnterior
                                     },
                                     cmbMostrarFinTarea,
                                     comboMotivoFinaliza,
                                     {
                                         xtype: 'textarea',
                                         fieldLabel: 'Observación:',
                                         id: 'observacion',
                                         name: 'observacion',
                                         maxLength: 500,
                                         enforceMaxLength: true,
                                         enableKeyEvents: true,
                                         rows: 5,
                                         cols: 160
                                     },
                                     {
                                         xtype: 'radiogroup',
                                         fieldLabel: 'Es Solucion',
                                         columns: 1,
                                         vertical: true,
                                         hidden: (data.perteneceCaso && !data.casoPerteneceTN) ? false : true,
                                         items: [
                                             {
                                                 boxLabel: 'Si',
                                                 name: 'esSolucion',
                                                 inputValue: 'S',
                                                 id: 'radio1',
                                                 checked: true
                                             }, {
                                                 boxLabel: 'No',
                                                 name: 'esSolucion',
                                                 inputValue: 'N',
                                                 id: 'radio2'
                                             }
                                         ]
                                     },
                                     {
                                         xtype: 'textfield',
                                         fieldLabel: 'Fecha de Inicio:',
                                         id: 'fechaInicial',
                                         name: 'fechaInicial',
                                         value: data.fechaEjecucion,
                                         readOnly: true
                                     },
                                     {
                                         xtype: 'textfield',
                                         fieldLabel: 'Hora Inicial Tarea:',
                                         id: 'horaInicial',
                                         name: 'horaInicial',
                                         value: data.horaEjecucion,
                                         readOnly: true
                                     },
                                     {
                                         fieldLabel: 'Fecha de Cierre:',
                                         xtype: 'textfield',
                                         id: 'fe_cierre_value',
                                         name: 'fe_cierre_value',
                                         format: 'Y-m-d',
                                         editable: false,
                                         readOnly: true,
                                         value: fechaActual,
                                         listeners:
                                         {
                                             select:
                                             {
                                                 fn: function(e)
                                                 {
                                                     date = e.getValue();
                                                     total =
                                                         getTiempoTotal(fecha, hora, date, Ext.getCmp('ho_cierre_value').value,
                                                             'fecha');
                                                     if (total !== - 1)
                                                     {
                                                         Ext.getCmp('tiempoTotal').setValue(total);
                                                     }
                                                     else
                                                     {
                                                         Ext.getCmp('tiempoTotal').setValue(getTiempoTotal(fecha, hora, date,
                                                             Ext.getCmp('ho_cierre_value').value,
                                                             ''));
                                                         Ext.getCmp('fe_cierre_value').setValue(date);
                                                     }
                                                 }
                                             }
                                         }
                                     },
                                     {
                                         fieldLabel: 'Hora de Cierre:',
                                         xtype: 'textfield',
                                         format: 'H:i',
                                         id: 'ho_cierre_value',
                                         name: 'ho_cierre_value',
                                         value: horaActual,
                                         editable: false,
                                         increment: 1,
                                         readOnly: true,
                                         listeners:
                                         {
                                             select:
                                             {
                                                 fn: function(e)
                                                 {
                                                     date = e.getValue();
                                                     total =
                                                         getTiempoTotal(fecha, hora, Ext.getCmp('fe_cierre_value').value, date,
                                                             'hora');
                                                     if (total !== - 1)
                                                     {
                                                         Ext.getCmp('tiempoTotal').setValue(total);
                                                     }
                                                     else
                                                     {
                                                         Ext.getCmp('tiempoTotal').setValue(getTiempoTotal(fecha, hora,
                                                             Ext.getCmp('fe_cierre_value').value,
                                                             date, ''));
                                                         Ext.getCmp('ho_cierre_value').setValue(date);
                                                     }
                                                 }
                                             }
                                         }
                                     },
                                     {
                                         xtype: 'textfield',
                                         fieldLabel: 'Tiempo Total Tarea (minutos) index',
                                         id: 'tiempoTotal',
                                         name: 'tiempoTotal',
                                         value: data.duracionMinutos,
                                         readOnly: true
                                     },
                                     {
                                         xtype: 'button',
                                         id: 'btnIndisponibilidadTarea',
                                         text: 'Indisponibilidad',
                                         formBind: true,
                                         hidden: true,
                                         style: {
                                             marginLeft: '79%'
                                         },
                                         handler: function()
                                         {
                                             Ext.getCmp('winIndisponibilidadTarea').setVisible(true);
                                         },
                                     },
                                     fieldsetCoordenadasTotal,
                                     gridCuadrilla,
                                     cmbDentroInstalaciones
                                 ]
                         }
                     ]
             });
 
                     var conn = new Ext.data.Connection({
                         listeners: {
                             'beforerequest': {
                                 fn: function (con, opt) {
                                     Ext.get(document.body).mask('Cargando...');
                                 },
                                 scope: this
                             },
                             'requestcomplete': {
                                 fn: function (con, res, opt) {
                                     Ext.get(document.body).unmask();
                                 },
                                 scope: this
                             },
                             'requestexception': {
                                 fn: function (con, res, opt) {
                                     Ext.get(document.body).unmask();
                                 },
                                 scope: this
                             }
                         }
                     }); 
 
                     // verificar perfil TAP
                     conn.request({
                         url: url_verificarRolTap,
                         method: 'post',
                         success: function(response){	
                             if((response.responseText) === 'S'){
                                 Ext.getCmp('btnIndisponibilidadTarea').setVisible(true);
                             }
                         },
                         failure: function(result) {
                             Ext.Msg.show({
                                 title: 'Error',
                                 msg: result.statusText,
                                 buttons: Ext.Msg.OK,
                                 icon: Ext.MessageBox.ERROR
                             });
                         }
                     }); 
 
                     registroContactoModificado =  null;
                     winFinalizarTarea = Ext.create('Ext.window.Window', {
                     title: 'Finalizar Tarea',
                         modal: true,
                         width: width,
                         height: height,
                         resizable: false,
                         layout: 'fit',
                         items: [formPanel2],
                         buttonAlign: 'center',
                         buttons:[btnguardar2, btncancelar2],
                         closable: false//jgiler
                     }).show();
                     heightWinFinalizarTarea = height;
         }
         else
         {
            Ext.Msg.alert('Alerta ', "Para poder finalizar esta tarea, debe ser iniciada desde la aplicación Móvil");
         }
     }
     else
     {
        Ext.Msg.alert('Alerta ', "Esta tarea no se puede finalizar debido que posee una o más subtareas asociadas, por \n\
                                 favor cerrar las tareas asociadas a la tarea principal.");
     }
 }
 
function mostrarComboRegistroContactos(tarea, data){
    Ext.getCmp('comboIngresoInstalacionesCliente').setValue(-1);
    Ext.getCmp('txtObservacionContactoCliente').setVisible(false);
    if(tarea == "CLIENTE" && data.casoPerteneceTN && data.id_caso !== 0)
    {
        Ext.getCmp('fieldsIngresoInstalacionesCliente').setVisible(true);
        mostrarRegistroContactos = true;
        winFinalizarTarea.setHeight(heightWinFinalizarTarea + heightRegistroContactos);
    }else
    {
        Ext.getCmp('fieldsIngresoInstalacionesCliente').setVisible(false);
        mostrarRegistroContactos = false;
        winFinalizarTarea.setHeight(heightWinFinalizarTarea + 10.0);
    }
}
 function guardarTareaFinalizada(data, id_detalle)
 {
     
     var boolValidaTareaFinal = true;
     var boolTieneRegistroContactos = false;
     strObservacionRegistroContactos = Ext.getCmp('txtObservacionContactoCliente').getValue();
     if (!Ext.getCmp('cbox_tarea_ini') || Ext.getCmp('cbox_tarea_ini').getValue() == false) 
     { 
         boolValidaTareaFinal = false ;
     }    
 
     if(data.casoPerteneceTN && data.mostrarCoordenadas == "S" && data.tareasManga == "S" && ( Ext.getCmp('text_longitudManga1').getValue() == ""
         || Ext.getCmp('text_latitudManga1').getValue() == "" || Ext.getCmp('text_latitudManga2').getValue() == ""
         || Ext.getCmp('text_longitudManga2').getValue() == "" || Ext.getCmp('text_latitudI').getValue() == ""
         || Ext.getCmp('text_longitudI').getValue() == ""))
     {
         alert("Por favor llenar los campos obligatorios");
     }
     else if(data.casoPerteneceTN && data.mostrarCoordenadas == "S" && data.tareasManga == "N" && (Ext.getCmp('text_latitudI').getValue() == ""
         || Ext.getCmp('text_longitudI').getValue() == ""))
     {
         alert("Por favor llenar los campos obligatorios");
     }
     else if(data.permiteRegistroActivos === true && data.strFinSelectNombre === '' && ((data.id_caso !== 0) || (data.esInterdepartamental === true))
             && boolValidaTareaFinal == false)
     {
         Ext.Msg.alert('Alerta', 'Por favor seleccione fin de tarea');
     }
     else if((Ext.getCmp('comboMotivoFinaliza').getValue() == '' || Ext.getCmp('comboMotivoFinaliza').getValue() == null) 
              && Ext.getCmp('comboMotivoFinaliza').isVisible() && boolValidaTareaFinal == false)
     {
         Ext.Msg.alert('Alerta', 'Por favor seleccione motivo');
     }
     else if(mostrarRegistroContactos && Ext.getCmp('comboIngresoInstalacionesCliente').getValue() == -1)
     {
         Ext.Msg.alert('Alerta', 'Por favor responda la pregunta: ¿Ingresó a las instalaciones del cliente?');
     }
     else if(mostrarRegistroContactos && 
              Ext.getCmp('comboIngresoInstalacionesCliente').getValue() == 0
              && strObservacionRegistroContactos == '')
     {
         Ext.Msg.alert('Alerta', 'Por favor ingrese una observación en la sección de registro de contacto del cliente');
     }
     else
     {   
         if (mostrarRegistroContactos) 
         {
            boolTieneRegistroContactos = true;
         }
         var conn = new Ext.data.Connection
         ({
             listeners:
             {
                 'beforerequest':
                 {
                     fn: function (con, opt)
                     {
                         Ext.get(document.body).mask('Finalizando Tarea...');
                     },
                     scope: this
                 },
                 'requestcomplete':
                 {
                     fn: function (con, res, opt)
                     {
                         Ext.get(document.body).unmask();
                     },
                     scope: this
                 },
                 'requestexception':
                 {
                     fn: function (con, res, opt)
                     {
                         Ext.get(document.body).unmask();
                     },
                     scope: this
                 }
             }
         });
         var finalizaObservacion = Ext.getCmp('observacion').value;
         var finalizaRadio       = Ext.getCmp('radio1').getValue();
         var finalizaTiempo      = Ext.getCmp('tiempoTotal').getValue();
         var finalizaFeCierre    = Ext.getCmp('fe_cierre_value').getValue();
         var finalizaHorCierre   = Ext.getCmp('ho_cierre_value').getValue();
         var motivoFinalizaTarea;
         var finalizaComboTarea;
         
         if (data.permiteRegistroActivos === true && ((data.id_caso !== 0) || (data.esInterdepartamental === true))
            && boolValidaTareaFinal == false)
         {
             motivoFinalizaTarea = Ext.getCmp('comboMotivoFinaliza').getValue();
             finalizaComboTarea  = data.strFinSelectNombre;
         }
         else if(boolValidaTareaFinal)
         {
             finalizaComboTarea =  data.nombreTareaAnterior;
         }  
         else
         {
             finalizaComboTarea  = Ext.getCmp('comboTarea').getRawValue();
         }
 
         var longitudIncidente   = "";
         var latitudIncidente    = "";
         var longitudManga1      = "";
         var latitudManga1       = "";
         var longitudManga2      = "";
         var latitudManga2       = "";
         if(data.casoPerteneceTN && data.mostrarCoordenadas == "S")
         {
             longitudIncidente   = Ext.getCmp('text_longitudI').getValue();
             latitudIncidente    = Ext.getCmp('text_latitudI').getValue();
 
             if(data.tareasManga == "S")
             {
                 longitudManga1   = Ext.getCmp('text_longitudManga1').getValue();
                 latitudManga1    = Ext.getCmp('text_latitudManga1').getValue();
                 longitudManga2   = Ext.getCmp('text_longitudManga2').getValue();
                 latitudManga2    = Ext.getCmp('text_latitudManga2').getValue();
             }
         }
 
         if(data.perteneceCaso && data.casoPerteneceTN )
         {
             finalizaRadio       = false;
         }
 
         let intFinTareaId;
         let nombreTarea = Ext.getCmp('comboTarea').getValue();
         if (data.intFinTareaId === undefined) 
         {
             if (Ext.getCmp('comboTarea').getValue() == data.nombre_tarea && boolValidaTareaFinal == false) 
             {
                 intFinTareaId = data.id_tarea;
             }
             else if(boolValidaTareaFinal)
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
 
 
         
         var strGuardar = 'NO';
         var strIndisponibilidadI = '';
         var strTipoI = 'T';
         var intTiempoAfectacionI = '0';
         var strMasivoI = '';
         var intComboResponsableI = '';
         var intClientesAfectadosI = '0';
         var strObservacionesI = '';
         var strOltI = '';
         var strPuertoI = '';
         var strCajaI = '';
         var strSplitterI = '';
         var i;
         var j;
 
         if(Ext.getCmp('btnIndisponibilidadTarea').isVisible()){
 
             strGuardar = 'SI';
 
             strIndisponibilidadI = Ext.getCmp('comboIndisponibilidadTarea').getValue();
 
             if(strIndisponibilidadI == 'SI'){
                 
                 intTiempoAfectacionI = Ext.getCmp('tiempoAfectacionTarea').getValue();
                 strMasivoI = Ext.getCmp('comboMasivoTarea').getValue();
 
                 if(strMasivoI == 'SI'){
                 
                     intClientesAfectadosI = Ext.getCmp('clientesAfectadosTarea').getValue();
                     intComboResponsableI = Ext.getCmp('comboResponsableTarea').getValue();
                     strObservacionesI = Ext.getCmp('observacionesTarea').getValue();
                     strOltI = Ext.getCmp('oltSeleccionadosTarea').getValue();
 
                     if (strOltI == ''){
                         Ext.Msg.alert("Alerta","Debe escoger un elemento Olt");
                         return false;
                     }else if (intClientesAfectadosI == null){
                         Ext.Msg.alert("Alerta","Debe llenar clientes afectados");
                         return false;
                     }else if (intComboResponsableI == null){
                         Ext.Msg.alert("Alerta","Debe escoger un responsable del problema");
                         return false;
                     }
 
 
                     // combo puerto
                     if(comboPuertoTarea.valueModels != null){
                         
                         for (i = 0; i<comboPuertoTarea.valueModels.length; i++){
 
                             for (j = 0; j<storePuertoTarea.data.items.length; j++){
 
                                 if (comboPuertoTarea.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                                     
                                     if (strPuertoI == ''){
                                         strPuertoI = comboPuertoTarea.valueModels[i].data.idInterface;
                                     }else{
                                         strPuertoI = strPuertoI + ', ' + comboPuertoTarea.valueModels[i].data.idInterface;
                                     }
                                     break;
         
                                 }
 
                             }
 
                         }
                     }
 
                     // combo caja
                     if(comboCajaTarea.valueModels != null){
                         
                         for (i = 0; i<comboCajaTarea.valueModels.length; i++){
 
                             for (j = 0; j<storeCajaTarea.data.items.length; j++){
 
                                 if (comboCajaTarea.valueModels[i].data.idCaja == storeCajaTarea.data.items[j].data.idCaja){
                                     
                                     if (strCajaI == ''){
                                         strCajaI = comboCajaTarea.valueModels[i].data.idCaja;
                                     }else{
                                         strCajaI = strCajaI + ', ' + comboCajaTarea.valueModels[i].data.idCaja;
                                     }
                                     break;
         
                                 }
 
                             }
 
                         }
                     }
 
                     // combo splitter
                     if(comboSplitterTarea.valueModels != null){
                         
                         for (i = 0; i<comboSplitterTarea.valueModels.length; i++){
 
                             for (j = 0; j<storeSplitterTarea.data.items.length; j++){
 
                                 if (comboSplitterTarea.valueModels[i].data.idSplitter == storeSplitterTarea.data.items[j].data.idSplitter){
                                     
                                     if (strSplitterI == ''){
                                         strSplitterI = comboSplitterTarea.valueModels[i].data.idSplitter;
                                     }else{
                                         strSplitterI = strSplitterI + ', ' + comboSplitterTarea.valueModels[i].data.idSplitter;
                                     }
                                     break;
         
                                 }
 
                             }
 
                         }
                     }
         
                 }
 
             }
 
         }
 
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
                 conn:               conn,
                 id_caso:            data.id_caso,
                 casoPerteneceTN:    data.casoPerteneceTN,
                 intIdDetalleHist:   data.intIdDetalleHist,
                 numeroTarea:        data.numero_tarea,
                 nombre_tarea:       data.nombre_tarea,
                 nombre_proceso:     data.nombre_proceso,
                 asignado_nombre:    data.ref_asignado_nombre,
                 departamento_nombre:data.asignado_nombre,
                 esInterdepartamental:data.esInterdepartamental,
                 tipoMedioId:        data.tipoMedioId,
                 idMotivoFinaliza:   motivoFinalizaTarea,
                 idFinTarea:         intFinTareaId,
         boolFinalTareaAnterior: boolValidaTareaFinal,
                 strGuardar: strGuardar,
                 strIndisponibilidadI:  strIndisponibilidadI,
                 strTipoI:              strTipoI,
                 intTiempoAfectacionI:  intTiempoAfectacionI,
                 strMasivoI:            strMasivoI,
                 intComboResponsableI:  intComboResponsableI,
                 intClientesAfectadosI: intClientesAfectadosI,
                 strObservacionesI:     strObservacionesI,
                 strOltI:               strOltI,
                 strPuertoI:            strPuertoI,
                 strCajaI:              strCajaI,
                 strSplitterI:          strSplitterI
     };
         
         if((data.permiteRegistroActivos  === true && 
            ((data.strRequiereFibra       === 'S'  && data.tieneProgresoRuta           === 'NO')  ||
             (data.strRequiereMaterial    === 'S'  && data.tieneProgresoMateriales     === 'NO'))
            ) && ((data.id_caso !== 0) || (data.esInterdepartamental === true)))
         {   
                     if(data.strRequiereFibra === 'N')
                     {
                         data['tieneProgresoRuta'] = 'SI';
                     }
                     if(data.strRequiereMaterial === 'N')
                     {
                         data['tieneProgresoMateriales'] = 'SI';
                     }
                     data['redisenioTarea'] = true;
                     
                     registroFibraMaterial(data, function (statusRegistro) { 
 
                         if(statusRegistro === 'OK')
                         {
                            if(boolTieneRegistroContactos)
                            {
                                var arrayIngresoRegistroContacto =
                                {
                                    empresa_id: 10,
                                    id_caso: data.id_caso,
                                    id_detalle: data.id_detalle,
                                    login: data.loginSesion,
                                    departamento_id: data.departamentoId,
                                    str_empresa_tarea: data.strEmpresaTarea,
                                    tipo: 'Cerrar'
                                };
                                ingresarSeguimientoRegistroContactos(arrayIngresoRegistroContacto);
                            }
                             finalizarTareaRequest(arayDataFinalizar);
                         }
                         else
                         {
                             Ext.Msg.alert('Alerta', 'No se pudo finalizar la tarea.');
                         }
                     });      
         }
         else
         {
             if(boolTieneRegistroContactos)
             {
                var arrayIngresoRegistroContacto =
                {
                    empresa_id: 10,
                    id_caso: data.id_caso,
                    id_detalle: data.id_detalle,
                    login: data.loginSesion,
                    departamento_id: data.departamentoId,
                    str_empresa_tarea: data.strEmpresaTarea,
                    tipo: 'Cerrar'
                };
                ingresarSeguimientoRegistroContactos(arrayIngresoRegistroContacto);
             }
             finalizarTareaRequest(arayDataFinalizar);
         
         }    
     
     }
 } 
 
 function finalizarTareaRequest(arrayFin){
 
     var id_detalle          = arrayFin.id_detalle;
     var finalizaObservacion = arrayFin.observacion;
     var finalizaRadio       = arrayFin.esSolucion;
     var finalizaTiempo      = arrayFin.tiempo_total;
     var finalizaFeCierre    = arrayFin.tiempo_cierre;
     var finalizaHorCierre   = arrayFin.hora_cierre;
     var fechaEjecucion      = arrayFin.tiempo_ejecucion;
     var horaEjecucion       = arrayFin.hora_ejecucion;
     var clientes            = arrayFin.clientes;
     var finalizaComboTarea  = arrayFin.tarea;
     var tarea_final         = arrayFin.tarea_final;
     var longitudIncidente   = arrayFin.longitud;
     var latitudIncidente    = arrayFin.latitud;
     var longitudManga1		= arrayFin.longitudManga1;
     var latitudManga1		= arrayFin.latitudManga1;
     var longitudManga2		= arrayFin.longitudManga2;
     var latitudManga2		= arrayFin.latitudManga2;
     var duracionTarea  		= arrayFin.duracionTarea;
     var conn                = arrayFin.conn;
     var id_caso             = arrayFin.id_caso;
     var casoPerteneceTN     = arrayFin.casoPerteneceTN;
     var numeroTarea         =    arrayFin.numeroTarea;
     var nombre_tarea        =    arrayFin.nombre_tarea;
     var nombre_proceso      =    arrayFin.nombre_proceso;
     var asignado_nombre     =    arrayFin.asignado_nombre;
     var departamento_nombre =    arrayFin.departamento_nombre;
     var esInterdepartamental=    arrayFin.esInterdepartamental; 
     var idMotivoFinaliza    =    arrayFin.idMotivoFinaliza; 
     var idFinTarea          = arrayFin.idFinTarea;
     var boolFinalTareaAnterior = arrayFin.boolFinalTareaAnterior;
     var strGuardar             =  arrayFin.strGuardar;
     var strIndisponibilidadI   =  arrayFin.strIndisponibilidadI;
     var strTipoI               =  arrayFin.strTipoI;
     var intTiempoAfectacionI   =  arrayFin.intTiempoAfectacionI;
     var strMasivoI             =  arrayFin.strMasivoI;
     var intComboResponsableI   =  arrayFin.intComboResponsableI;
     var intClientesAfectadosI  =  arrayFin.intClientesAfectadosI;
     var strObservacionesI      =  arrayFin.strObservacionesI;
     var strOltI                =  arrayFin.strOltI;
     var strPuertoI             =  arrayFin.strPuertoI;
     var strCajaI               =  arrayFin.strCajaI;
     var strSplitterI           =  arrayFin.strSplitterI;
 
 
     winIndisponibilidadTarea.destroy();
     winFinalizarTarea.destroy();      
 
         Ext.MessageBox.wait("Guardando datos...");
 
         conn.request
         ({
             method: 'POST',
             params:
             {
                 id_detalle: id_detalle,
                 observacion: finalizaObservacion,
                 esSolucion: finalizaRadio,
                 tiempo_total: finalizaTiempo,
                 tiempo_cierre: finalizaFeCierre,
                 hora_cierre: finalizaHorCierre,
                 tiempo_ejecucion: fechaEjecucion,
                 hora_ejecucion: horaEjecucion,
                 clientes: clientes,
                 tarea: finalizaComboTarea,
                 tarea_final: tarea_final,
                 longitud: longitudIncidente,
                 latitud: latitudIncidente,
                 longitudManga1: longitudManga1,
                 latitudManga1: latitudManga1,
                 longitudManga2: longitudManga2,
                 latitudManga2: latitudManga2,
                 duracionTarea: duracionTarea,
                 intIdDetalleHist : arrayFin.intIdDetalleHist,
                 numeroTarea:         numeroTarea,
                 nombre_tarea:        nombre_tarea,
                 nombre_proceso:      nombre_proceso,
                 asignado_nombre:     asignado_nombre,
                 departamento_nombre: departamento_nombre,
                 esInterdepartamental:esInterdepartamental,
                 idMotivoFinaliza    : idMotivoFinaliza,
                 idFinTarea          : idFinTarea, //jobedon
                 boolFinalTareaAnterior : boolFinalTareaAnterior,
                 strGuardar:            strGuardar,
                 strIndisponibilidadI:  strIndisponibilidadI,
                 strTipoI:              strTipoI,
                 intTiempoAfectacionI:  intTiempoAfectacionI,
                 strMasivoI:            strMasivoI,
                 intComboResponsableI:  intComboResponsableI,
                 intClientesAfectadosI: intClientesAfectadosI,
                 strObservacionesI:     strObservacionesI,
                 strOltI:               strOltI,
                 strPuertoI:            strPuertoI,
                 strCajaI:              strCajaI,
                 strSplitterI:          strSplitterI
             },
             url: url_finalizarTarea,
             timeout: 300000,
             success: function(response)
             {
                 Ext.MessageBox.hide();
 
                 var json = Ext.JSON.decode(response.responseText);
 
                 if (!json.success && !json.seguirAccion) {
                     Ext.MessageBox.show({
                         closable   :  false  , multiline : false,
                         title      : 'Alerta', msg : json.mensaje,
                         buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                         buttonText : {ok: 'Cerrar'},
                         fn : function (button) {
                             if(button === 'ok') {
                                 //store.load();
                                 gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                             }
                         }
                     });
                     return;
                 }
 
                 if (json.success)
                 {
                     winIndisponibilidadTarea.destroy();
                     winFinalizarTarea.destroy(); 
 
                         Ext.Msg.alert('Mensaje', 'Se finalizó la tarea.',
                         function(btn)
                         {
                             if (btn == 'ok')
                             {
                                 winIndisponibilidadTarea.destroy();
                                 winFinalizarTarea.destroy(); 
 
                                 //store.load();
                                 gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                                 //Se determina analisis de cierre de caso siempre y cuando la tarea que finaliza
                                 //pertenezca a un caso, caso contrario no verifica nada
                                 if (id_caso !== 0)
                                 {
                                     winIndisponibilidadTarea.destroy();
                                     winFinalizarTarea.destroy();
 
                                     //store.load();
                                     gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                                     //Se determina analisis de cierre de caso siempre y cuando la tarea que finaliza
                                     //pertenezca a un caso, caso contrario no verifica nada
                                     if (id_caso !== 0)
                                     {
                                         //Si ya no existen tareas abiertas y al menos una tarea que dio solucion al caso
                                         if (json.tareasAbiertas === 0 && json.tareasSolucionadas > 0 && json.presentar == "1")
                                         {
                                             //store.load();
                                             gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                                             //Se determina analisis de cierre de caso siempre y cuando la tarea que finaliza
                                             //pertenezca a un caso, caso contrario no verifica nada
                                             if (id_caso !== 0)
                                             {
                                                 //Si ya no existen tareas abiertas y al menos una tarea que dio solucion al caso
                                                 if(json.tareasAbiertas === 0 && json.tareasSolucionadas > 0 && json.presentar == "1"
                                                     && !casoPerteneceTN)
                                                 {
                                                     obtenerDatosCasosCierre(id_caso, conn, false);
                                                 }
                                             }
                                         }
                                     }
                                 }
                                 else
                                 {
                                     Ext.Msg.alert('Alerta ', json.mensaje);
                                     winIndisponibilidadTarea.destroy();
                                     winFinalizarTarea.destroy();
 
                                     //store.load();
                                     gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                                 }
                             }
                         });
                 }
                 else
                 {
                         Ext.Msg.alert('Alerta ',"La tarea se encuentra Cerrada, por favor consultela nuevamente");
                 }  
             },
             failure: function(rec, op)
             {
                 Ext.MessageBox.hide();
 
                 var json = Ext.JSON.decode(op.response.responseText);
                 Ext.Msg.alert('Alerta ', json.mensaje);
             }
     });
 }
   
 /************************************************************************ */
 /*********************** INDISPONIBILIDAD ******************************** */
 /************************************************************************ */
 
 var winIndisponibilidadTarea;
 
 function verIndisponibilidadTarea(data)
 {
 
     actualizarTiempoAfectacion = Ext.create('Ext.Button', {
         id          : 'actualizarTiempoAfectacion',
         text        : '<i class="fa fa-refresh" aria-hidden="true" style="margin:0px"></i>',
         tooltip     : 'Actualizar tiempo afectacion',
         tooltipType : 'title',
         style       : 'position: absolute; margin: -8% 0% 0% 50%;',
         hidden       : true,
         handler: function() {
             obtenerTiempoAfectacionIndisponibilidadTarea(data);
         }
     });
 
 
     /*** combo olt multiple ***/
     storeOltTarea = new Ext.data.Store({
         total: 'total',
         pageSize: 200,
         proxy: {
             type: 'ajax',
             method: 'post',
             url: url_getElementosPorTipo,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'encontrados'
             },
             extraParams: {
                 estado: 'Activo'
             }
         },
         sorters: [{
              property : 'nombreElemento',
              direction: 'ASC'
         }],
         fields: [
             {name: 'idElemento', mapping: 'idElemento'},
             {name: 'nombreElemento', mapping: 'nombreElemento'}
         ]
     });
 
     //Define el contador y multi selector de combos
     Ext.define('comboSelectedCount1Tarea', {
         alias: 'plugin.selectedCount1Tarea',
         init: function (combo) {
             combo.on({
                 select: function (me, records) {
                     var store = combo.getStore(),
                         diff = records.length != store.count,
                         newAll = false,
                         all = false,
                         newRecords = [];
                     var acumuladorValue = '';
                     var acumuladorDescripcion = '';
 
                     Ext.each(records, function (obj, i, recordsItself) {
                         if (records[i].data.nombre_estado_tarea === 'Todos') {
                             allRecord = records[i];
                             if (!combo.allSelected) {
                                 combo.select(store.getRange());
                                 combo.allSelected = true;
                                 all = true;
                                 newAll = true;
                             } else {
                                 all = true;
                             }
                         } else {
                             if (diff && !newAll)
                                 newRecords.push(records[i]);
                         }
                     });
                     if (combo.allSelected && !all) {
                         combo.clearValue();
                         combo.allSelected = false;
                     } else  if (diff && !newAll) {
                         combo.select(newRecords);
                         combo.allSelected = false;
 
                         
 
                         // acumula lo seleccionado en label aparte, para poder dejar el combo libre para 
                         // realizar busquedas
                         //anteriorValue = Ext.getCmp('oltValue').getValue();
                         acumuladorDescripcion = Ext.getCmp('oltSeleccionadosTarea').getValue();
 
                         if (combo.valueModels != null){
                             
                             if (combo.valueModels.length > 0){
 
                                 if (acumuladorDescripcion != ''){
                                     //anteriorValue = anteriorValue + ',';
                                     acumuladorDescripcion = acumuladorDescripcion + ', ';
                                 }
 
                                 // si no existe lo agrego
                                 if(acumuladorDescripcion.indexOf(combo.rawValue) == -1){
 
                                     //Ext.getCmp('oltValue').setValue(anteriorValue + combo.valueModels[0].data.idElemento);
                                     Ext.getCmp('oltSeleccionadosTarea').setValue(acumuladorDescripcion + combo.rawValue);
 
                                 }
                                 
                             }
 
                         }
 
                         Ext.getCmp('comboOltTarea').value = "";
                         Ext.getCmp('comboOltTarea').setRawValue("");
                     }
                 }
             })
         }
     });
 
     comboOltTarea = Ext.create('Ext.form.ComboBox', {
         id           : 'comboOltTarea',
         store        :  storeOltTarea,
         displayField : 'nombreElemento',
         valueField   : 'idElemento',
         fieldLabel   : 'Elemento',
         width        :  390,
         queryMode    : "remote",
         plugins      : ['selectedCount1Tarea'],
         disabled     : false,
         editable     : true,
         multiSelect  : false,
         hidden       : true/*,
         displayTpl   : '<tpl for="."> {nombreElemento} <tpl if="xindex < xcount">, </tpl> </tpl>',
         listConfig   : {
             itemTpl: '{nombreElemento} <div class="uncheckedChkbox"></div>'
         },
         listeners: {
             change: function(combo, records, eOpts) {
 
                 
 
             }
         }*/
     });
     /*** combo olt multiple ***/
 
     /*** combo puerto multiple ***/
     storePuertoTarea = new Ext.data.Store({
         total: 'total',
         pageSize: 200,
         proxy: {
             type: 'ajax',
             method: 'post',
             url: url_getInterfacesPorElemento,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'encontrados'
             }
         },
         sorters: [{
              property : 'idInterface',
              direction: 'ASC'
         }],
         fields: [
             {name: 'idInterface', mapping: 'idInterface'},
             {name: 'nombreInterface', mapping: 'nombreInterface'}
         ]
     });
 
     //Define el contador y multi selector de combos
     Ext.define('comboSelectedCount2Tarea', {
         alias: 'plugin.selectedCount2Tarea',
         init: function (combo) {
             combo.on({
                 select: function (me, records) {
                     var store = combo.getStore(),
                         diff = records.length != store.count,
                         newAll = false,
                         all = false,
                         newRecords = [];
                     Ext.each(records, function (obj, i, recordsItself) {
                         if (records[i].data.nombre_estado_tarea === 'Todos') {
                             allRecord = records[i];
                             if (!combo.allSelected) {
                                 combo.select(store.getRange());
                                 combo.allSelected = true;
                                 all = true;
                                 newAll = true;
                             } else {
                                 all = true;
                             }
                         } else {
                             if (diff && !newAll)
                                 newRecords.push(records[i]);
                         }
                     });
                     if (combo.allSelected && !all) {
                         combo.clearValue();
                         combo.allSelected = false;
                     } else  if (diff && !newAll) {
                         combo.select(newRecords);
                         combo.allSelected = false;
                     }
                 }
             })
         }
     });
 
     comboPuertoTarea = Ext.create('Ext.form.ComboBox', {
         id           : 'comboPuertoTarea',
         store        :  storePuertoTarea,
         displayField : 'idInterface',
         valueField   : 'nombreInterface',
         fieldLabel   : 'Puerto Elemento',
         width        :  390,
         queryMode    : "remote",
         plugins      : ['selectedCount2Tarea'],
         disabled     : false,
         editable     : true,
         multiSelect  : true,
         hidden       : true,
         displayTpl   : '<tpl for=".">{nombreInterface}<tpl if="xindex < xcount">,</tpl> </tpl>',
         listConfig   : {
             itemTpl: '{nombreInterface} <div class="uncheckedChkbox"></div>'
         },
         listeners: {
             change: function(combo, records, eOpts) {
 
                 var acumulador = '';
 
                 if(combo.valueModels != null){
 
                     nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();
 
                     if(nombreOlt.indexOf(",") == -1){
 
                         for (var i = 0; i<combo.valueModels.length; i++){
 
                             for (var j = 0; j<storePuertoTarea.data.items.length; j++){
 
                                 if (combo.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                                     
                                     if (acumulador == ''){
                                         acumulador = combo.valueModels[i].data.idInterface;
                                     }else{
                                         acumulador = acumulador + ', ' + combo.valueModels[i].data.idInterface;
                                     }
                                     break;
         
                                 }
 
                             }
 
                         }
 
                         nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();
                         storeCajaTarea.proxy.extraParams = {nombreOlt: nombreOlt, idPuerto: acumulador};
                         storeCajaTarea.load({params: {}});
 
                         obtenerClientesAfectadosIndisponibilidadTarea(acumulador, '', '');
                     }
 
                 } 
                 
             }
         }
     });
     /*** combo puerto multiple ***/
 
     /*** combo caja multiple ***/
     storeCajaTarea = new Ext.data.Store({
         total: 'total',
         pageSize: 200,
         proxy: {
             type: 'ajax',
             method: 'post',
             url: url_getElementosContenedoresPorPuerto,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'encontrados'
             },
             extraParams: {
                 estado: 'Activo'
             }
         },
         sorters: [{
              property : 'nombreCaja',
              direction: 'ASC'
         }],
         fields: [
             {name: 'idCaja', mapping: 'idCaja'},
             {name: 'nombreCaja', mapping: 'nombreCaja'}
         ]
     });
 
     //Define el contador y multi selector de combos
     Ext.define('comboSelectedCount3Tarea', {
         alias: 'plugin.selectedCount3Tarea',
         init: function (combo) {
             combo.on({
                 select: function (me, records) {
                     var store = combo.getStore(),
                         diff = records.length != store.count,
                         newAll = false,
                         all = false,
                         newRecords = [];
                     Ext.each(records, function (obj, i, recordsItself) {
                         if (records[i].data.nombre_estado_tarea === 'Todos') {
                             allRecord = records[i];
                             if (!combo.allSelected) {
                                 combo.select(store.getRange());
                                 combo.allSelected = true;
                                 all = true;
                                 newAll = true;
                             } else {
                                 all = true;
                             }
                         } else {
                             if (diff && !newAll)
                                 newRecords.push(records[i]);
                         }
                     });
                     if (combo.allSelected && !all) {
                         combo.clearValue();
                         combo.allSelected = false;
                     } else  if (diff && !newAll) {
                         combo.select(newRecords);
                         combo.allSelected = false;
                     }
                 }
             })
         }
     });
 
     comboCajaTarea = Ext.create('Ext.form.ComboBox', {
         id           : 'comboCajaTarea',
         store        :  storeCajaTarea,
         displayField : 'idCaja',
         valueField   : 'nombreCaja',
         fieldLabel   : 'Caja Elemento',
         width        :  390,
         queryMode    : "remote",
         plugins      : ['selectedCount3Tarea'],
         disabled     : false,
         editable     : true,
         multiSelect  : true,
         hidden       : true,
         displayTpl   : '<tpl for=".">{nombreCaja}<tpl if="xindex < xcount">,</tpl></tpl>',
         listConfig   : {
             itemTpl: '{nombreCaja} <div class="uncheckedChkbox"></div>'
         },
         listeners: {
             change: function(combo, records, eOpts) {
 
                 var acumuladorPuerto = '';  
                 var acumuladorCaja = '';  
                 var i;
                 var j;
 
                 if(comboPuertoTarea.valueModels != null){
 
                     if(comboPuertoTarea.getRawValue() != 'NO APLICA'){
                     
                         for (i = 0; i<comboPuertoTarea.valueModels.length; i++){
 
                             for (j = 0; j<storePuertoTarea.data.items.length; j++){
 
                                 if (comboPuertoTarea.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                                     
                                     if (acumuladorPuerto == ''){
                                         acumuladorPuerto = comboPuertoTarea.valueModels[i].data.idInterface;
                                     }else{
                                         acumuladorPuerto = acumuladorPuerto + ', ' + comboPuertoTarea.valueModels[i].data.idInterface;
                                     }
                                     break;
         
                                 }
 
                             }
 
                         }
 
 
 
                         if(comboCajaTarea.valueModels != null){
                         
                             for (i = 0; i<comboCajaTarea.valueModels.length; i++){
         
                                 for (j = 0; j<storeCajaTarea.data.items.length; j++){
         
                                     if (comboCajaTarea.valueModels[i].data.idCaja == storeCajaTarea.data.items[j].data.idCaja){
                                         
                                         if (acumuladorCaja == ''){
                                             acumuladorCaja = comboCajaTarea.valueModels[i].data.idCaja;
                                         }else{
                                             acumuladorCaja = acumuladorCaja + ', ' + comboCajaTarea.valueModels[i].data.idCaja;
                                         }
                                         break;
             
                                     }
         
                                 }
         
                             }
                         }
 
                         nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();
                         storeSplitterTarea.proxy.extraParams = {nombreOlt: nombreOlt, idPuerto: acumuladorPuerto, idCaja: acumuladorCaja};
                         storeSplitterTarea.load({params: {}});
 
                         obtenerClientesAfectadosIndisponibilidadTarea(acumuladorPuerto, acumuladorCaja, '');
 
                     }
                 } 
                 
             }
         }
     });
     /*** combo caja multiple ***/
     
     /*** combo splitter multiple ***/
     storeSplitterTarea = new Ext.data.Store({
         total: 'total',
         pageSize: 200,
         proxy: {
             type: 'ajax',
             method: 'post',
             url: url_getElementosConectorPorElementoContenedor,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'encontrados'
             },
             extraParams: {
                 estado: 'Activo'
             }
         },
         sorters: [{
              property : 'nombreSplitter',
              direction: 'ASC'
         }],
         fields: [
             {name: 'idSplitter', mapping: 'idSplitter'},
             {name: 'nombreSplitter', mapping: 'nombreSplitter'}
         ]
     });
 
     //Define el contador y multi selector de combos
     Ext.define('comboSelectedCount4Tarea', {
         alias: 'plugin.selectedCount4Tarea',
         init: function (combo) {
             combo.on({
                 select: function (me, records) {
                     var store = combo.getStore(),
                         diff = records.length != store.count,
                         newAll = false,
                         all = false,
                         newRecords = [];
                     Ext.each(records, function (obj, i, recordsItself) {
                         if (records[i].data.nombre_estado_tarea === 'Todos') {
                             allRecord = records[i];
                             if (!combo.allSelected) {
                                 combo.select(store.getRange());
                                 combo.allSelected = true;
                                 all = true;
                                 newAll = true;
                             } else {
                                 all = true;
                             }
                         } else {
                             if (diff && !newAll)
                                 newRecords.push(records[i]);
                         }
                     });
                     if (combo.allSelected && !all) {
                         combo.clearValue();
                         combo.allSelected = false;
                     } else  if (diff && !newAll) {
                         combo.select(newRecords);
                         combo.allSelected = false;
                     }
                 }
             })
         }
     });
 
     comboSplitterTarea = Ext.create('Ext.form.ComboBox', {
         id           : 'comboSplitterTarea',
         store        :  storeSplitterTarea,
         displayField : 'idSplitter',
         valueField   : 'nombreSplitter',
         fieldLabel   : 'Splitter Elemento',
         width        :  390,
         queryMode    : "remote",
         plugins      : ['selectedCount4Tarea'],
         disabled     : false,
         editable     : true,
         multiSelect  : true,
         hidden       : true,
         displayTpl   : '<tpl for="."> {nombreSplitter} <tpl if="xindex < xcount">, </tpl> </tpl>',
         listConfig   : {
             itemTpl: '{nombreSplitter} <div class="uncheckedChkbox"></div>'
         },
         listeners: {
             change: function(combo, records, eOpts) {
 
                 var acumuladorSplitter = '';
                 var acumuladorPuerto = '';  
                 var acumuladorCaja = ''; 
                 var i;
                 var j;
 
                 if(combo.valueModels != null){
                     
                     for (i = 0; i<combo.valueModels.length; i++){
 
                         for (j = 0; j<storeSplitterTarea.data.items.length; j++){
 
                             if (combo.valueModels[i].data.idSplitter == storeSplitterTarea.data.items[j].data.idSplitter){
                                 
                                 if (acumuladorSplitter == ''){
                                     acumuladorSplitter = combo.valueModels[i].data.idSplitter;
                                 }else{
                                     acumuladorSplitter = acumuladorSplitter + ', ' + combo.valueModels[i].data.idSplitter;
                                 }
                                 break;
     
                             }
 
                         }
 
                     }
 
 
                     for (i = 0; i<comboPuertoTarea.valueModels.length; i++){
 
                         for (j = 0; j<storePuertoTarea.data.items.length; j++){
 
                             if (comboPuertoTarea.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                                 
                                 if (acumuladorPuerto == ''){
                                     acumuladorPuerto = comboPuertoTarea.valueModels[i].data.idInterface;
                                 }else{
                                     acumuladorPuerto = acumuladorPuerto + ', ' + comboPuertoTarea.valueModels[i].data.idInterface;
                                 }
                                 break;
     
                             }
 
                         }
 
                     }
 
 
 
                     if(comboCajaTarea.valueModels != null){
                     
                         for (i = 0; i<comboCajaTarea.valueModels.length; i++){
     
                             for (j = 0; j<storeCajaTarea.data.items.length; j++){
     
                                 if (comboCajaTarea.valueModels[i].data.idCaja == storeCajaTarea.data.items[j].data.idCaja){
                                     
                                     if (acumuladorCaja == ''){
                                         acumuladorCaja = comboCajaTarea.valueModels[i].data.idCaja;
                                     }else{
                                         acumuladorCaja = acumuladorCaja + ', ' + comboCajaTarea.valueModels[i].data.idCaja;
                                     }
                                     break;
         
                                 }
     
                             }
     
                         }
                     }
 
                     obtenerClientesAfectadosIndisponibilidadTarea(acumuladorPuerto, acumuladorCaja, acumuladorSplitter);
 
                 } 
                 
             }
         }
     });
     /*** combo splitter multiple ***/
 
     /*** combo responsable ***/
     comboResponsableStoreTarea = new Ext.data.Store({
         pageSize: 200,
         total: 'total',
         limit:1000,
         proxy: {
         type: 'ajax',
             url : url_empresaIndisponibilidadTarea,
             reader:
         {
         type: 'json',
             totalProperty: 'total',
             root: 'encontrados'
         }
         },
         fields:
         [
         {name:'codigo', mapping:'valor1'},
         {name:'descripcion', mapping:'valor2'}
         ]
     });
         
     comboResponsableTarea = Ext.create('Ext.form.ComboBox', {
         id:'comboResponsableTarea',
         store: comboResponsableStoreTarea,
         displayField: 'descripcion',
         valueField: 'codigo',
         height:30,
         width:200,
         border:0,
         margin:0,
         fieldLabel: 'Responsable del problema',
         queryMode: "remote",
         emptyText: '',
         hidden: true,
         editable: false
     });
     /*** combo responsable ***/
 
 
     /*** combo tarea ***/
     comboTareaStore2 = new Ext.data.Store({
         pageSize: 200,
         total: 'total',
         limit:1000,
         proxy: {
             type: 'ajax',
             url : url_gridTarea,
             reader:
         {
             type: 'json',
             totalProperty: 'total',
             root: 'encontrados'
         },
             extraParams:
         {
             nombre: '',
             estado: 'Activo',
             visible: 'SI',
             caso:data.id_caso,
             detalle:data.idDetalle //Se añade el id detalle para validacion de empresa
         }
         },
         fields:
         [
         {name:'id_tarea', mapping:'id_tarea'},
         {name:'nombre_tarea', mapping:'nombre_tarea'}
         ]
     });
         
     comboTarea2 = Ext.create('Ext.form.ComboBox', {
         id:'comboTarea2',
         store: comboTareaStore2,
         displayField: 'nombre_tarea',
         valueField: 'id_tarea',
         height:30,
         width:390,
         border:0,
         margin:0,
         fieldLabel: 'Tarea Inicial',
         queryMode: "remote",
         emptyText: '',
         editable: true
     });
     Ext.getCmp('comboTarea2').setRawValue(data.nombre_tarea);
     /*** combo tarea ***/
 
     /*** combo indisponibilidad ***/   
     comboIndisponibilidadStoreTarea = Ext.create('Ext.data.Store', {
         fields: ['codigo', 'descripcion'],
         data : [
             {"codigo":"NO", "descripcion":"NO"},
             {"codigo":"SI", "descripcion":"SI"}
         ]
     });
     
     comboIndisponibilidadTarea = Ext.create('Ext.form.ComboBox', {
         id:'comboIndisponibilidadTarea',
         store: comboIndisponibilidadStoreTarea,
         displayField: 'descripcion',
         valueField: 'codigo',
         height:30,
         width:200,
         border:0,
         margin:0,
         fieldLabel: 'Indisponibilidad',
         queryMode: "remote",
         emptyText: '',
         editable: false,
         listeners: {
             select: function(combo, records, eOpts) {
                 
                 var cmbIndisponibilidad = records[0].get('descripcion');
                 
                 if (cmbIndisponibilidad == 'SI'){
                     setVisibleIndisponibilidadTarea(true);
                 }else{
                     setVisibleIndisponibilidadTarea(false);
                 }
             }
         }
     });
     Ext.getCmp('comboIndisponibilidadTarea').value = "NO";
     Ext.getCmp('comboIndisponibilidadTarea').setRawValue('NO');
     /*** combo indisponibilidad ***/   
 
     /*** combo masivo ***/   
     comboMasivoStoreTarea = Ext.create('Ext.data.Store', {
         fields: ['codigo', 'descripcion'],
         data : [
             {"codigo":"SI", "descripcion":"SI"},
             {"codigo":"NO", "descripcion":"NO"}
         ]
     });
     
     comboMasivoTarea = Ext.create('Ext.form.ComboBox', {
         id:'comboMasivoTarea',
         store: comboMasivoStoreTarea,
         displayField: 'descripcion',
         valueField: 'codigo',
         height:30,
         width:200,
         border:0,
         margin:0,
         fieldLabel: 'Masivo',
         //labelStyle: 'width:600px',
         queryMode: "remote",
         emptyText: '',
         hidden: true,
         editable: false,
         listeners: {
             select: function(combo, records, eOpts) {
                 
                 var cmbVisible = records[0].get('descripcion');
                 
                 if (cmbVisible == 'SI'){
                     setVisibleMasivoTarea(true);
                 }else{
                     setVisibleMasivoTarea(false);
                 }
             }
         }
     });
     Ext.getCmp('comboMasivoTarea').value = "NO";
     Ext.getCmp('comboMasivoTarea').setRawValue('NO');
     /*** combo masivo ***/
 
 
     btnCerrarTarea = Ext.create('Ext.Button', {
         text: 'Cerrar',
         cls: 'x-btn-rigth',
         handler: function() {
             Ext.getCmp('winIndisponibilidadTarea').setVisible(false);
         }
     });
 
     btnLimpiarTarea = Ext.create('Ext.Button', {
         text: 'Limpiar',
         cls: 'x-btn-rigth',
         handler: function() {
 
             Ext.getCmp('oltSeleccionadosTarea').setValue('');
             
             storePuertoTarea.removeAll();
             storePuertoTarea.proxy.extraParams = {};
             storePuertoTarea.load();
             comboPuertoTarea.setValue('');
 
             storeCajaTarea.removeAll();
             storeCajaTarea.proxy.extraParams = {};
             storeCajaTarea.load();
             comboCajaTarea.setValue('');
 
             storeSplitterTarea.removeAll();
             storeSplitterTarea.proxy.extraParams = {};
             storeSplitterTarea.load();
             comboSplitterTarea.setValue('');
 
         }
     });
 
     formPanelIndisponibilidadTarea = Ext.create('Ext.form.Panel', {
         bodyPadding: 5,
         waitMsgTarget: true,
         height: 530,
         width: 440,
         layout: 'fit',
         fieldDefaults: {
         labelAlign: 'left',
             msgTarget: 'side'
         },
         items:
         [
         {
             xtype: 'fieldset',
             title: 'Información',
             defaultType: 'textfield',
             items:
             [
                 comboIndisponibilidadTarea,
                 {
                     xtype: 'numberfield',
                     fieldLabel: 'Tiempo de afectación:',
                     id: 'tiempoAfectacionTarea',
                     name: 'tiempoAfectacionTarea',
                     hidden: true,
                     width: 200,
                     minValue: 0,
                     allowNegative: false,
                     allowPureDecimal: true
                     //value: data.fechaEjecucion
                 },
                 actualizarTiempoAfectacion,
                 comboMasivoTarea,
                 comboOltTarea,
                 {
                     xtype: 'textfield',
                     fieldLabel: '',
                     id: 'oltSeleccionadosTarea',
                     name: 'oltSeleccionadosTarea',
                     hidden: true,
                     disabled: true,
                     width: 390,
                     listeners : {
                         change : function (txt, newValue,oldValue){
                             
                             nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();
                             storePuertoTarea.removeAll();
                             storeCajaTarea.removeAll();
                             storeSplitterTarea.removeAll();
 
                             // activa/desactiva combos puerto, caja, splitter
                             validarSeleccionOltTarea(nombreOlt, storePuertoTarea);
                         }
                     }
                 },
                 /*{
                     xtype: 'textfield',
                     fieldLabel: 'Olt Value',
                     id: 'oltValue',
                     name: 'oltValue',
                     hidden: true,
                     width: 390
                     
                 },*/
                 comboPuertoTarea,
                 comboCajaTarea,
                 comboSplitterTarea,
                 {
                     xtype: 'numberfield',
                     fieldLabel: 'Clientes afectados',
                     id: 'clientesAfectadosTarea',
                     name: 'clientesAfectadosTarea',
                     hidden: true,
                     width: 200,
                     minValue: 0,
                     allowNegative: false,
                     allowPureDecimal: true
                     //value:data.duracionMinutos
                 },
                 comboResponsableTarea,
                 {
                     xtype: 'textarea',
                     fieldLabel: 'Observaciones:',
                     id: 'observacionesTarea',
                     name: 'observacionesTarea',
                     maxLength: 500,
                     enforceMaxLength: true,
                     enableKeyEvents: true,
                     rows: 5,
                     cols: 160,
                     hidden: true
                 }
             ]
         }
         ]
     });
     
     winIndisponibilidadTarea = Ext.create('Ext.window.Window', {
         id: 'winIndisponibilidadTarea',
         title: 'Indisponibilidad',
         modal: true,
         width: 440,
         height: 530,
         resizable: true,
         layout: 'fit',
         items: [formPanelIndisponibilidadTarea],
         buttonAlign: 'center',
         buttons:[btnLimpiarTarea, btnCerrarTarea],
         closable: false
     }).show();
 
     Ext.getCmp('winIndisponibilidadTarea').setVisible(false);
 
 }
 
 function setVisibleIndisponibilidadTarea(boolean){
 
     Ext.getCmp('tiempoAfectacionTarea').setVisible(boolean);
     Ext.getCmp('comboMasivoTarea').setVisible(boolean);
     Ext.getCmp('actualizarTiempoAfectacion').setVisible(boolean);
 
     if (!boolean){
         setVisibleMasivoTarea(boolean);
         Ext.getCmp('comboMotivoFinalizaTarea').value = "NO";
         Ext.getCmp('comboMasivoTarea').setRawValue("NO");
     }
 }
 
 function setVisibleMasivoTarea(boolean){
 
     Ext.getCmp('clientesAfectadosTarea').setVisible(boolean);
     Ext.getCmp('comboResponsableTarea').setVisible(boolean);
     Ext.getCmp('observacionesTarea').setVisible(boolean);
     Ext.getCmp('comboOltTarea').setVisible(boolean);
     Ext.getCmp('oltSeleccionadosTarea').setVisible(boolean);
     Ext.getCmp('comboPuertoTarea').setVisible(boolean);
     Ext.getCmp('comboCajaTarea').setVisible(boolean);
     Ext.getCmp('comboSplitterTarea').setVisible(boolean);
 }
 
 function obtenerTiempoAfectacionIndisponibilidadTarea(data)
 {        
 
    // Ext.getCmp('tiempoAfectacion').setValue(data.id_tarea);
     
     var strIdDetalle = data.id_detalle;
 
     var conn = new Ext.data.Connection({
         listeners: {
             'beforerequest': {
                 fn: function (con, opt) {
                     Ext.get(document.body).mask('Cargando...');
                 },
                 scope: this
             },
             'requestcomplete': {
                 fn: function (con, res, opt) {
                     Ext.get(document.body).unmask();
                 },
                 scope: this
             },
             'requestexception': {
                 fn: function (con, res, opt) {
                     Ext.get(document.body).unmask();
                 },
                 scope: this
             }
         }
     }); 
     
     
     conn.request({
         url: url_getTiempoAfectacionIndisponibilidadTarea,
         method: 'post',
         params: 
             { 
                 strIdDetalle : strIdDetalle
             },
         success: function(response){			
             Ext.getCmp('tiempoAfectacionTarea').setValue(Ext.decode(response.responseText));
           
         },
         failure: function(result) {
             Ext.Msg.show({
                 title: 'Error',
                 msg: result.statusText,
                 buttons: Ext.Msg.OK,
                 icon: Ext.MessageBox.ERROR
             });
         }
     });      
       
 }
 
 function obtenerClientesAfectadosIndisponibilidadTarea(pIdPuerto, pIdCaja, pIdSplitter)
 {        
     
     var nombreOlt = Ext.getCmp('oltSeleccionadosTarea').getValue();
     var reemplazar = /, /gi;
     nombreOlt = nombreOlt.replace(reemplazar, "','");
 
     var idPuerto = pIdPuerto;
     var idCaja = pIdCaja;
     var idSplitter = pIdSplitter;
     var i;
     var j;
 
     // parametro puerto vacio, obtengo seleccionados
     if(idPuerto == ''){
         
         for (i = 0; i<comboPuertoTarea.valueModels.length; i++){
 
             for (j = 0; j<storePuertoTarea.data.items.length; j++){
 
                 if (comboPuertoTarea.valueModels[i].data.idInterface == storePuertoTarea.data.items[j].data.idInterface){
                     
                     if (idPuerto == ''){
                         idPuerto = comboPuertoTarea.valueModels[i].data.idInterface;
                     }else{
                         idPuerto = idPuerto + ', ' + comboPuertoTarea.valueModels[i].data.idInterface;
                     }
                     break;
 
                 }
 
             }
 
         }
 
     }
 
 
     // parametro caja vacio, obtengo seleccionados
     if(idCaja == ''){
 
         if(comboCajaTarea.valueModels != null){
 
             for (i = 0; i<comboCajaTarea.valueModels.length; i++){
 
                 for (j = 0; j<storeCajaTarea.data.items.length; j++){
 
                     if (comboCajaTarea.valueModels[i].data.idCaja == storeCajaTarea.data.items[j].data.idCaja){
                         
                         if (idCaja == ''){
                             idCaja = comboCajaTarea.valueModels[i].data.idCaja;
                         }else{
                             idCaja = idCaja + ', ' + comboCajaTarea.valueModels[i].data.idCaja;
                         }
                         break;
 
                     }
 
                 }
 
             }
             
         }
 
     }
 
 
     // parametro splitter vacio, obtengo seleccionados
     if(idSplitter == ''){
 
         if(comboSplitterTarea.valueModels != null){
 
             for (i = 0; i<comboSplitterTarea.valueModels.length; i++){
 
                 for (j = 0; j<storeSplitterTarea.data.items.length; j++){
 
                     if (comboSplitterTarea.valueModels[i].data.idSplitter == storeSplitterTarea.data.items[j].data.idSplitter){
                         
                         if (idSplitter == ''){
                             idSplitter = comboSplitterTarea.valueModels[i].data.idSplitter;
                         }else{
                             idSplitter = idSplitter + ', ' + comboSplitterTarea.valueModels[i].data.idSplitter;
                         }
                         break;
 
                     }
 
                 }
 
             }
             
         }
     }
 
     var conn = new Ext.data.Connection({
         listeners: {
             'beforerequest': {
                 fn: function (con, opt) {
                     Ext.get(document.body).mask('Cargando...');
                 },
                 scope: this
             },
             'requestcomplete': {
                 fn: function (con, res, opt) {
                     Ext.get(document.body).unmask();
                 },
                 scope: this
             },
             'requestexception': {
                 fn: function (con, res, opt) {
                     Ext.get(document.body).unmask();
                 },
                 scope: this
             }
         }
     }); 
     
     
     conn.request({
         url: url_getClientesAfectados,
         method: 'post',
         params: 
             { 
                 nombreOlt : nombreOlt,
                 idPuerto : idPuerto,
                 idCaja : idCaja,
                 idSplitter : idSplitter,
             },
         success: function(response){			
             Ext.getCmp('clientesAfectadosTarea').setValue(Ext.decode(response.responseText));
           
         },
         failure: function(result) {
             Ext.Msg.show({
                 title: 'Error',
                 msg: result.statusText,
                 buttons: Ext.Msg.OK,
                 icon: Ext.MessageBox.ERROR
             });
         }
     });      
       
 }
 
 function validarSeleccionOltTarea(obj, storePuertoTarea)
 { 
 
     // si selecciono mas de 1, se bloquean los demas
     if(obj.indexOf(",") != -1){
 
         comboPuertoTarea.setValue("NO APLICA");
         comboPuertoTarea.setRawValue("NO APLICA");
         comboPuertoTarea.setDisabled(true);
 
         comboCajaTarea.setValue("NO APLICA");
         comboCajaTarea.setRawValue("NO APLICA");
         comboCajaTarea.setDisabled(true);
 
         comboSplitterTarea.setValue("NO APLICA");
         comboSplitterTarea.setRawValue("NO APLICA");
         comboSplitterTarea.setDisabled(true);
 
     // solo un olt
     }else{
 
         //comboPuerto.setValue("");
         //comboPuerto.setRawValue("Seleccione puerto");
         comboPuertoTarea.setDisabled(false);
 
         comboCajaTarea.setRawValue("");
         comboCajaTarea.setValue(""); 
         comboCajaTarea.setDisabled(false);
 
         comboSplitterTarea.setRawValue("");
         comboSplitterTarea.setValue("");
         comboSplitterTarea.setDisabled(false);
 
         if(obj.length > 0){
             storePuertoTarea.proxy.extraParams = {nombreOlt: nombreOlt};
             storePuertoTarea.load({params: {}});
         } 
 
     }
 
     obtenerClientesAfectadosIndisponibilidadTarea('', '', '');
 
 }
 
 //#######################################################
 //############## CREAR SUB TAREAS (se mantiene diseño anterior) ###########
 //#######################################################
 
 function validateAgregarSubTarea(tarea,e)
 {
     e.setAttribute("disabled", "disabled");
     setTimeout(function(){e.removeAttribute("disabled", "");}, 5000);
 
     var objTarea = JSON.parse(tarea);
 
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
                     var fechaActual = fechaFinArray[0] + "-" + fechaFinArray[1] + "-" + fechaFinArray[2];
                     agregarSubTarea(objTarea,fechaActual,json.horaActual);  
 
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
 
 function agregarSubTarea(data,fecha,hora)
 {
     var valorIdDepartamento = '';
 
     storeProcesos = new Ext.data.Store({
         total: 'total',
         autoLoad:true,
         proxy: {
             type: 'ajax',
             url: url_procesos,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'registros'
             }
         },
         fields:
             [
                 {name: 'id', mapping: 'id'},
                 {name: 'nombreProceso', mapping: 'nombreProceso'}
             ]
 
     });
 
     storeTareas = new Ext.data.Store({
         total: 'total',
         autoLoad: true,
         proxy: {
             type: 'ajax',
             url: url_tareaProceso,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'encontrados'
             },
             extraParams: {
                 nombreProceso: 'TAREAS SOPORTE',
                 estado: 'Activo'
             }
         },
         fields:
             [
                 {name: 'idTarea', mapping: 'idTarea'},
                 {name: 'nombreTarea', mapping: 'nombreTarea'}
             ]
     });
 
     //Informacion de asignacion
 
     storeAsignaEmpleado = new Ext.data.Store({
         total: 'total',
         pageSize: 1000,
         proxy: {
             type: 'ajax',
             url: url_empleadoPorDepartamentoCiudad,
             reader: {
                 type: 'json',
                 totalProperty: 'result.total',
                 root: 'result.encontrados',
                 metaProperty: 'myMetaData'
             },
             extraParams: {
                 nombre: ''
             }
         },
         fields:
             [
                 {name: 'id_empleado', mapping: 'id_empleado'},
                 {name: 'nombre_empleado', mapping: 'nombre_empleado'}
             ]
     });
 
 
     storeEmpresas = new Ext.data.Store({
         total: 'total',
         pageSize: 200,
         proxy: {
             type: 'ajax',
             method: 'post',
             url: url_empresaPorSistema,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'encontrados'
             },
             extraParams: {
                 app: 'TELCOS'
             }
         },
         fields:
             [
                 {name: 'opcion', mapping: 'nombre_empresa'},
                 {name: 'valor', mapping: 'prefijo'}
             ]
     });
 
     storeCiudades = new Ext.data.Store({
         total: 'total',
         pageSize: 200,
         proxy: {
             type: 'ajax',
             method: 'post',
             url: url_ciudadPorEmpresa,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'encontrados'
             },
             extraParams: {
                 nombre: '',
                 estado: 'Activo'
             }
         },
         fields:
             [
                 {name: 'id_canton', mapping: 'id_canton'},
                 {name: 'nombre_canton', mapping: 'nombre_canton'}
             ]
     });
 
 
     storeDepartamentosCiudad = new Ext.data.Store({
         total: 'total',
         pageSize: 200,
         proxy: {
             type: 'ajax',
             method: 'post',
             url: url_departamentoPorEmpresaCiudad,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'encontrados'
             },
             extraParams: {
                 nombre: '',
                 estado: 'Activo'
             }
         },
         fields:
             [
                 {name: 'id_departamento', mapping: 'id_departamento'},
                 {name: 'nombre_departamento', mapping: 'nombre_departamento'}
             ]
     });
 
 
     storeCuadrillas = new Ext.data.Store({
         total: 'total',
         pageSize: 9999,
         proxy: {
             type: 'ajax',
             method: 'post',
             url: url_integrantesCuadrilla,
             reader: {
                 type: 'json',
                 totalProperty: 'result.total',
                 root: 'result.encontrados',
                 metaProperty: 'myMetaData'
             },
             extraParams: {
                 estado: 'Eliminado'
             }
         },
         fields:
             [
                 {name: 'idCuadrilla', mapping: 'idCuadrilla'},
                 {name: 'nombre', mapping: 'nombre'}
             ],
         listeners: {
             load: function(store) {
                 if (store.proxy.extraParams.origenD === "Departamento")
                 {
                     document.getElementById('radio_e').disabled = false;
                     document.getElementById('radio_c').disabled = false;
                     document.getElementById('radio_co').disabled = false;
                     document.getElementById('radio_e').checked = false;
                     document.getElementById('radio_c').checked = false;
                     document.getElementById('radio_co').checked = false;
                     Ext.getCmp('comboCuadrilla').setDisabled(true);
                     Ext.getCmp('combo_empleados').setDisabled(true);
                     Ext.getCmp('comboContratista').setDisabled(true);
                     
                     storeCuadrillas.proxy.extraParams.origenD = '';
                 }
             }
 
         }
     });
 
     storeContratista = new Ext.data.Store({
         total: 'total',
         pageSize: 9999,
         proxy: {
             type: 'ajax',
             method: 'post',
             url: url_empresasExternas,
             reader: {
                 type: 'json',
                 totalProperty: 'result.total',
                 root: 'result.encontrados',
                 metaProperty: 'myMetaData'
             },
             extraParams: {
             }
         },
         fields:
             [
                 {name: 'id_empresa_externa', mapping: 'id_empresa_externa'},
                 {name: 'nombre_empresa_externa', mapping: 'nombre_empresa_externa'}
             ]
     });
 
     var iniHtml = '<div align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n\
                       &nbsp;<input type="radio" onchange="setearCombo(1);" value="empleado" name="radioCuadrilla" id="radio_e" disabled>&nbsp;\n\
                       Empleado&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo(2);" value="cuadrilla" name="radioCuadrilla" \n\
                       id="radio_c" disabled>&nbsp;Cuadrilla&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" onchange="setearCombo(3);"\n\
                       value="contratista" name="radioCuadrilla" id="radio_co" disabled>&nbsp;Contratista</div>';
 
     RadiosTiposResponsable = Ext.create('Ext.Component', {
         html: iniHtml,
         width: 600,
         padding: 10,
         style: {color: '#000000'}});
 
     combo_empleados = new Ext.form.ComboBox({
         id: 'combo_empleados',
         name: 'combo_empleados',
         fieldLabel: "Empleado",
         store: storeAsignaEmpleado,
         displayField: 'nombre_empleado',
         valueField: 'id_empleado',
         queryMode: "remote",
         emptyText: '',
         disabled: true,
         width: 380,
         listeners: {
             select: function(){
                 Ext.getCmp('comboCuadrilla').value = "";
                 Ext.getCmp('comboCuadrilla').setRawValue("");
                 Ext.getCmp('comboContratista').value = "";
                 Ext.getCmp('comboContratista').setRawValue("");
             }
         }
     });
     var formPanelTareas = Ext.create('Ext.form.Panel',
         {
             bodyPadding: 5,
             waitMsgTarget: true,
             fieldDefaults:
                 {
                     labelAlign: 'left',
                     labelWidth: 85,
                     msgTarget: 'side'
                 },
             items:
                 [
                     {
                         xtype: 'fieldset',
                         autoHeight: true,
                         width: 450,
                         items:
                             [
                                 {
                                     xtype: 'combobox',
                                     fieldLabel: 'Procesos:',
                                     id:'cmbProcesos',
                                     store: storeProcesos,
                                     displayField: 'nombreProceso',
                                     valueField: 'id',
                                     queryMode: "remote",
                                     emptyText: '',
                                     width: 380,
                                     listeners: {
                                         select: function(combo, records, eOpts)
                                         {
                                             storeTareas.proxy.extraParams = {id: combo.getValue()};
                                             storeTareas.load();
                                             Ext.getCmp('cmbTarea').setVisible(true);
                                             Ext.getCmp('cmbTarea').setDisabled(false);
 
                                         }
                                     }
                                 },
                                 {
                                     xtype: 'combobox',
                                     id: 'cmbTarea',
                                     store: storeTareas,
                                     displayField: 'nombreTarea',
                                     valueField: 'idTarea',
                                     fieldLabel: 'Tarea:',
                                     queryMode: "remote",
                                     emptyText: '',
                                     width: 380,
                                     disabled: true
                                 },
                                 {
                                     xtype: 'combobox',
                                     fieldLabel: 'Empresa:',
                                     id: 'comboEmpresa',
                                     name: 'comboEmpresa',
                                     store: storeEmpresas,
                                     displayField: 'opcion',
                                     valueField: 'valor',
                                     queryMode: "remote",
                                     emptyText: '',
                                     width: 380,
                                     listeners: {
                                         select: function(combo) {
 
                                             Ext.getCmp('comboCiudad').reset();
                                             Ext.getCmp('comboDepartamento').reset();
                                             Ext.getCmp('combo_empleados').reset();
 
                                             Ext.getCmp('comboCiudad').setDisabled(false);
                                             Ext.getCmp('comboDepartamento').setDisabled(true);
                                             Ext.getCmp('combo_empleados').setDisabled(true);
 
                                             presentarCiudades(combo.getValue());
                                         }
                                     },
                                     forceSelection: true
                                 },
                                 {
                                     xtype: 'combobox',
                                     fieldLabel: 'Ciudad',
                                     id: 'comboCiudad',
                                     name: 'comboCiudad',
                                     store: storeCiudades,
                                     displayField: 'nombre_canton',
                                     valueField: 'id_canton',
                                     queryMode: "remote",
                                     emptyText: '',
                                     disabled: true,
                                     width: 380,
                                     listeners: {
                                         select: function(combo) {
                                             Ext.getCmp('comboDepartamento').reset();
                                             Ext.getCmp('combo_empleados').reset();
 
                                             Ext.getCmp('comboDepartamento').setDisabled(false);
                                             Ext.getCmp('combo_empleados').setDisabled(true);
 
                                             empresa = Ext.getCmp('comboEmpresa').getValue();
 
                                             presentarDepartamentosPorCiudad(combo.getValue(), empresa);
                                         }
                                     },
                                     forceSelection: true
                                 },
                                 {
                                     xtype: 'combobox',
                                     fieldLabel: 'Departamento',
                                     id: 'comboDepartamento',
                                     name: 'comboDepartamento',
                                     store: storeDepartamentosCiudad,
                                     displayField: 'nombre_departamento',
                                     valueField: 'id_departamento',
                                     queryMode: "remote",
                                     emptyText: '',
                                     disabled: true,
                                     width: 380,
                                     listeners: {
                                         afterRender: function(combo) {
                                             if(typeof strPrefijoEmpresaSession !== 'undefined' && strPrefijoEmpresaSession.trim())
                                             {
                                                 storeEmpresas.load(function() {
                                                     Ext.getCmp('comboEmpresa').setValue(strPrefijoEmpresaSession);
                                                     storeCiudades.proxy.extraParams = { empresa:strPrefijoEmpresaSession };
                                                     storeCiudades.load(function() {
                                                         Ext.getCmp('comboCiudad').setDisabled(false);
                                                         if(typeof strIdCantonUsrSession !== 'undefined' && strIdCantonUsrSession.trim())
                                                         {
                                                             Ext.getCmp('comboCiudad').setValue(Number(strIdCantonUsrSession));
                                                             storeDepartamentosCiudad.proxy.extraParams = { id_canton:   strIdCantonUsrSession,
                                                                                                            empresa  :   strPrefijoEmpresaSession};
                                                             storeDepartamentosCiudad.load(function() {
                                                                 Ext.getCmp('comboDepartamento').setDisabled(false);
                                                                 combo.setValue(Number(strIdDepartamentoUsrSession));
                                                                 presentarEmpleadosXDepartamentoCiudad(strIdDepartamentoUsrSession, 
                                                                                                       strIdCantonUsrSession, 
                                                                                                       strPrefijoEmpresaSession);
                                                                 presentarCuadrillasXDepartamento(strIdDepartamentoUsrSession);
                                                                 presentarContratistas();
                                                                 elWinCrearTarea.unmask();
                                                             });
                                                         }
                                                         else
                                                         {
                                                             elWinCrearTarea.unmask();
                                                         }
                                                     });
                                                 });
                                             }
                                             else
                                             {
                                                 elWinCrearTarea.unmask();
                                             }
                                         },
                                         select: function(combo) {
 
 
                                             Ext.getCmp('combo_empleados').reset();
                                             Ext.getCmp('combo_empleados').setDisabled(true);
                                             Ext.getCmp('comboCuadrilla').value = "";
                                             Ext.getCmp('comboCuadrilla').setRawValue("");
                                             Ext.getCmp('comboCuadrilla').setDisabled(true);
                                             Ext.getCmp('comboContratista').value = "";
                                             Ext.getCmp('comboContratista').setRawValue("");
                                             Ext.getCmp('comboContratista').setDisabled(true);
                                             empresa = Ext.getCmp('comboEmpresa').getValue();
                                             canton = Ext.getCmp('comboCiudad').getValue();
                                             presentarEmpleadosXDepartamentoCiudad(combo.getValue(), canton, empresa, valorIdDepartamento);
                                             presentarCuadrillasXDepartamento(Ext.getCmp('comboDepartamento').getValue());
                                             presentarContratistas();
                                         }
                                     },
                                     forceSelection: true
                                 },
                                 RadiosTiposResponsable,
                                 {
                                     xtype: 'combobox',
                                     id: 'combo_empleados',
                                     name: 'combo_empleados',
                                     fieldLabel: "Empleado",
                                     store: storeAsignaEmpleado,
                                     displayField: 'nombre_empleado',
                                     valueField: 'id_empleado',
                                     queryMode: "remote",
                                     emptyText: '',
                                     disabled: true,
                                     width: 380,
                                     listeners: {
                                         select: function() {
                                             Ext.getCmp('comboCuadrilla').value = "";
                                             Ext.getCmp('comboCuadrilla').setRawValue("");
                                             Ext.getCmp('comboContratista').value = "";
                                             Ext.getCmp('comboContratista').setRawValue("");
                                         }
                                     }
                                 },
                                 {
                                     xtype: 'combobox',
                                     fieldLabel: 'Cuadrilla',
                                     id: 'comboCuadrilla',
                                     name: 'comboCuadrilla',
                                     store: storeCuadrillas,
                                     displayField: 'nombre',
                                     valueField: 'idCuadrilla',
                                     queryMode: "remote",
                                     emptyText: '',
                                     disabled: true,
                                     width: 380,
                                     listeners: {
                                         select: function(combo) {
                                             Ext.getCmp('combo_empleados').value = "";
                                             Ext.getCmp('combo_empleados').setRawValue("");
                                             Ext.getCmp('comboContratista').value = "";
                                             Ext.getCmp('comboContratista').setRawValue("");
                                             validarTabletPorCuadrilla(combo.getValue());
                                         }
                                     }
 
                                 },
                                 {
                                     xtype: 'combobox',
                                     fieldLabel: 'Contratista',
                                     id: 'comboContratista',
                                     name: 'comboContratista',
                                     store: storeContratista,
                                     displayField: 'nombre_empresa_externa',
                                     valueField: 'id_empresa_externa',
                                     queryMode: "remote",
                                     emptyText: '',
                                     disabled: true,
                                     width: 380,
                                     listeners: {
                                         select: function() {
                                             Ext.getCmp('combo_empleados').value = "";
                                             Ext.getCmp('combo_empleados').setRawValue("");
                                             Ext.getCmp('comboCuadrilla').value = "";
                                             Ext.getCmp('comboCuadrilla').setRawValue("");
                                         }
                                     }
 
                                 },
                                 {
                                     xtype: 'datefield',
                                     fieldLabel: 'Fecha de Ejecución:',
                                     id: 'fecha_ejecucion',
                                     name: 'fecha_ejecucion',
                                     editable: false,
                                     format: 'Y-m-d',
                                     width: 380,
                                     value: fecha,
                                     minValue: fecha
                                 },
                                 {
                                     xtype: 'timefield',
                                     fieldLabel: 'Hora de Ejecución:',
                                     format: 'H:i',
                                     id: 'hora_ejecucion',
                                     name: 'hora_ejecucion',
                                     minValue: '00:01',
                                     maxValue: '23:59',
                                     increment: 1,
                                     editable: true,
                                     value: hora,
                                     width: 380
                                 },
                                 {
                                     xtype: 'textarea',
                                     id: 'observacionAsignacion',
                                     fieldLabel: 'Observacion',
                                     name: 'observacion',
                                     rows: 3,
                                     allowBlank: false,
                                     width: 380
                                 }
                             ]
                     }
                 ],
             buttons:
                 [
                     {
                         text: 'Agregar Tarea',
                         handler: function()
                         {
                             if (Ext.getCmp('cmbTarea').value !== null && Ext.getCmp('cmbTarea').value !== "")
                             {
                                 if (Ext.getCmp('comboEmpresa').getValue() !== null && Ext.getCmp('comboDepartamento').getValue() !== null &&
                                     Ext.getCmp('comboCiudad').getValue() !== null)
                                 {
 
                                     if ((Ext.getCmp('combo_empleados') && Ext.getCmp('combo_empleados').value) ||
                                         (Ext.getCmp('comboCuadrilla') && Ext.getCmp('comboCuadrilla').value && valorAsignacion === "cuadrilla") ||
                                         (Ext.getCmp('comboContratista') && Ext.getCmp('comboContratista').value &&
                                          valorAsignacion === "contratista"))
                                     {
                                         personaEmpresaRol = null;
                                         refAsignadoNombre = null;
 
                                         if (valorAsignacion === "empleado")
                                         {
                                             var comboEmpleado        = Ext.getCmp('combo_empleados').value;
                                             var valoresComboEmpleado = comboEmpleado.split("@@");
                                             refAsignadoId       = valoresComboEmpleado[0];
                                             personaEmpresaRol   = valoresComboEmpleado[1];
                                             refAsignadoNombre   = Ext.getCmp('combo_empleados').rawValue;
                                             asignadoId          = Ext.getCmp('comboDepartamento').value;
                                             asignadoNombre      = Ext.getCmp('comboDepartamento').rawValue;
                                             tipoAsignado        = "EMPLEADO";
 
                                         }
                                         else if (valorAsignacion === "cuadrilla")
                                         {
                                             refAsignadoId       = "0";
                                             asignadoId          = Ext.getCmp('comboCuadrilla').value;
                                             asignadoNombre      = Ext.getCmp('comboCuadrilla').rawValue;
                                             tipoAsignado        = "CUADRILLA";
                                         }
                                         else
                                         {
                                             refAsignadoId       = "0";
                                             asignadoId          = Ext.getCmp('comboContratista').value;
                                             asignadoNombre      = Ext.getCmp('comboContratista').rawValue;
                                             tipoAsignado        = "EMPRESAEXTERNA";
                                         }
 
                                         observacion     = Ext.getCmp('observacionAsignacion').value;
                                         fechaEjecucion  = Ext.getCmp('fecha_ejecucion').value;
                                         horaEjecucion   = Ext.getCmp('hora_ejecucion').value;
 
                                         var conn = new Ext.data.Connection({
                                             listeners: {
                                                 'beforerequest': {
                                                     fn: function(con, opt) {
                                                         Ext.getBody().mask('Creando Subtarea');
                                                     },
                                                     scope: this
                                                 },
                                                 'requestcomplete': {
                                                     fn: function(con, res, opt) {
                                                         Ext.getBody().unmask();
                                                     },
                                                     scope: this
                                                 },
                                                 'requestexception': {
                                                     fn: function(con, res, opt) {
                                                         Ext.getBody().unmask();
                                                     },
                                                     scope: this
                                                 }
                                             }
                                         });
 
                                         conn.request({
                                             method: 'POST',
                                             params: {
                                                 detalleIdRelac    : data.id_detalle,
                                                 idTarea           : Ext.getCmp('cmbTarea').value,
                                                 personaEmpresaRol : personaEmpresaRol,
                                                 asignadoId        : asignadoId,
                                                 nombreAsignado    : asignadoNombre,
                                                 refAsignadoId     : refAsignadoId,
                                                 refAsignadoNombre : refAsignadoNombre,
                                                 observacion       : observacion,
                                                 fechaEjecucion    : fechaEjecucion,
                                                 horaEjecucion     : horaEjecucion,
                                                 tipoAsignacion    : tipoAsignado,
                                                 numeroTarea       : data.numero_tarea,
                                                 empresaAsignacion : Ext.getCmp('comboEmpresa').value,
                                                 intIdDetalleHist  : data.intIdDetalleHist,
                                                 strValidarAccion  : 'SI'
                                             },
                                             url: url_crearSubTarea,
                                             success: function(response)
                                             {                                           
                                                 var json = Ext.JSON.decode(response.responseText);
 
                                                 if (!json.success && !json.seguirAccion) {
                                                     Ext.MessageBox.show({
                                                         closable   :  false  , multiline : false,
                                                         title      : 'Alerta', msg : json.mensaje,
                                                         buttons    :  Ext.MessageBox.OK, icon : Ext.MessageBox.WARNING,
                                                         buttonText : {ok: 'Cerrar'},
                                                         fn : function (button) {
                                                             if(button === 'ok') {
                                                                 winAsignarTarea.destroy();
                                                                 //store.load();
                                                                 gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                                                             }
                                                         }
                                                     });
                                                     return;
                                                 }
 
                                                 Ext.Msg.alert('Mensaje', json.mensaje, function(btn) {
                                                     if (btn == 'ok') {
                                                         winAsignarTarea.destroy();
                                                         //store.load();
                                                         gridTareas.ajax.url(url_listGridTarea+paramGet).load();
                                                     }
                                                 });                                              
                                             },
                                             failure: function(rec, op) {
                                                 var json = Ext.JSON.decode(op.response.responseText);
                                                 Ext.Msg.alert('Alerta ', json.mensaje);
                                             }
                                         });
                                     }
                                     else
                                     {
                                         Ext.Msg.alert('Alerta ', 'Por favor escoja un empleado, cuadrilla o contratista');
                                     }
                                 }
                                 else
                                 {
                                     Ext.Msg.alert('Alerta ', 'Campos incompletos, debe seleccionar Empresa,Ciudad y Departamento');
                                 }
                             }
                             else
                             {
                                 Ext.Msg.alert('Alerta ', 'Debe escoger una Tarea a asignar');
                             }
                         }
                     },
                     {
                         text: 'Cancelar',
                         handler: function()
                         {
                             winAsignarTarea.destroy();
                         }
                     }
                 ]
         });
     var winAsignarTarea = Ext.create('Ext.window.Window',
         {
             title: 'Asignar Tarea',
             modal: true,
             width: 480,
             closable: true,
             layout: 'fit',
             items: [formPanelTareas]
         }).show();
         
     elWinCrearTarea = winAsignarTarea.getEl();
     elWinCrearTarea.mask('Cargando...');
 }
 
 function setearCombo(tipo)
 {
 
     var myData_message = '';
     var myData_boolSuccess = '';
     if(tipo == "1")
     {        
         Ext.getCmp('combo_empleados').value = "";
         Ext.getCmp('combo_empleados').setRawValue("");
         cuadrillaAsignada = "S";
         myData_message = storeAsignaEmpleado.getProxy().getReader().jsonData.myMetaData.message;
         myData_boolSuccess = storeAsignaEmpleado.getProxy().getReader().jsonData.myMetaData.boolSuccess;
                 
         if (myData_boolSuccess != "1")
         {
             Ext.Msg.alert('Mensaje ', myData_message);
             Ext.getCmp('combo_empleados').setDisabled(true); 
             Ext.getCmp('comboCuadrilla').setDisabled(true); 
             Ext.getCmp('comboCuadrilla').setValue("");
             Ext.getCmp('comboContratista').setDisabled(true);
             Ext.getCmp('comboContratista').setValue("");
         }
         else
         {
             if (storeAsignaEmpleado.getCount() <= 1 && myData_boolSuccess != "1") {
                 Ext.Msg.alert('Mensaje ', "No existen empleados asignados para este departamento.");
                 Ext.getCmp('combo_empleados').setDisabled(true);  
                 Ext.getCmp('comboCuadrilla').setDisabled(true); 
                 Ext.getCmp('comboCuadrilla').setValue("");
                 Ext.getCmp('comboContratista').setDisabled(true);
                 Ext.getCmp('comboContratista').setValue("");
             }
             else
             {
                 Ext.getCmp('combo_empleados').setDisabled(false);
                 Ext.getCmp('comboCuadrilla').setDisabled(true); 
                 Ext.getCmp('comboCuadrilla').setValue("");
                 Ext.getCmp('comboContratista').setDisabled(true);
                 Ext.getCmp('comboContratista').setValue("");
                 valorAsignacion = "empleado";  
             }
             
         }
     }
     else if (tipo == "2")
     {     
         myData_message = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.message;
         myData_boolSuccess = storeCuadrillas.getProxy().getReader().jsonData.myMetaData.boolSuccess;                 
 
         if (myData_boolSuccess != "1")
         {
             Ext.Msg.alert('Alerta ', myData_message);
             Ext.getCmp('comboCuadrilla').setDisabled(true);
             Ext.getCmp('combo_empleados').setDisabled(true); 
             Ext.getCmp('combo_empleados').setValue("");
             Ext.getCmp('comboContratista').setDisabled(true);
         }                
         else
         {                
             Ext.getCmp('comboCuadrilla').setDisabled(false);
             Ext.getCmp('combo_empleados').setDisabled(true);
             Ext.getCmp('combo_empleados').setValue("");
             Ext.getCmp('comboContratista').setDisabled(true);
             Ext.getCmp('comboContratista').setValue("");
             valorAsignacion = "cuadrilla";
         }
     } 
     else
     {
         cuadrillaAsignada = "S";
         myData_message = storeContratista.getProxy().getReader().jsonData.myMetaData.message;
         myData_boolSuccess = storeContratista.getProxy().getReader().jsonData.myMetaData.boolSuccess;
 
         if (myData_boolSuccess != "1")
         {
             Ext.Msg.alert('Alerta ', myData_message);
             Ext.getCmp('comboCuadrilla').setDisabled(true);
             Ext.getCmp('comboCuadrilla').setValue("");
             Ext.getCmp('combo_empleados').setDisabled(true);
             Ext.getCmp('combo_empleados').setValue("");
             Ext.getCmp('comboContratista').setDisabled(true);
         }
         else
         {
             Ext.getCmp('comboCuadrilla').setDisabled(true);
             Ext.getCmp('comboCuadrilla').setValue("");
             Ext.getCmp('combo_empleados').setDisabled(true);
             Ext.getCmp('combo_empleados').setValue("");
             Ext.getCmp('comboContratista').setDisabled(false);
             valorAsignacion = "contratista";
         }
     }
 
     //Ext.getCmp('cbox_responder').setValue(false);
 }
 
 function presentarCiudades(empresa) 
 {
     storeCiudades.proxy.extraParams = {empresa: empresa};
     storeCiudades.load();
 }
 
 
 function presentarDepartamentosPorCiudad(id_canton, empresa) 
 {
     storeDepartamentosCiudad.proxy.extraParams = {id_canton: id_canton, empresa: empresa};
     storeDepartamentosCiudad.load();
 }
 
 function presentarEmpleadosXDepartamentoCiudad(id_departamento, id_canton, empresa, valorIdDepartamento) 
 {
     storeAsignaEmpleado.proxy.extraParams = {id_canton: id_canton, empresa: empresa, id_departamento: id_departamento, departamento_caso: valorIdDepartamento};
     storeAsignaEmpleado.load();
 }
 
 
 function presentarCuadrillasXDepartamento(id_departamento){
     
     storeCuadrillas.proxy.extraParams = { departamento:id_departamento,estado: 'Eliminado',origenD: 'Departamento'};
     storeCuadrillas.load();
   
 }
 
 function presentarContratistas(){
 
     storeContratista.proxy.extraParams = { rol : 'Empresa Externa'};
     storeContratista.load();
 
 }
 
 //#######################################################
 //############## FUNCIONES ###########
 //#######################################################
 
 function getDiferenciaTiempo(fechaIni, fechaFin) {
 
     var fechaIniS = getDate(fechaIni).split("-");
     var fechaFinS = getDate(fechaFin).split("-");
 
     fechaF = (String)(fechaFinS[2] + "/" + fechaFinS[1] + "/" + fechaFinS[0]);
 
     fecha = (String)(fechaIniS[2] + "/" + fechaIniS[1] + "/" + fechaIniS[0]);
 
     var fechaInicio = new Date(fecha);
     var fechaFinal = new Date(fechaF);
 
     var difFecha = fechaFinal - fechaInicio;
 
     return Math.ceil((((difFecha / 1000) / 60) / 60) / 24);
 }
 
 function addZero(num)
 {
     (String(num).length < 2) ? num = String("0" + num) :  num = String(num);
     return num;        
 }
 
 function getHour(hour) {
     var currentTime ='';
     if (hour === null)
     {
         currentTime = new Date();
     }
     else
     {
         currentTime = hour;
     }
 
     var hours = addZero(currentTime.getHours());
     var minute = addZero(currentTime.getMinutes());
     return(hours + ":" + minute);
 }
 
 function getDate(date) {
     var currentTime ='';
     if (date === null)
     {
         currentTime = new Date();
     }
     else
     {
         currentTime = date;
     }
 
     var month = addZero(currentTime.getMonth() + 1);
     var day = addZero(currentTime.getDate());
     var year = currentTime.getFullYear();
     return(day + "-" + month + "-" + year);
 
 }
 
 // Función mostrar mensajes en modasl
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
  
 // Función que permite mostrar alerta en el modals
 
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
  
 // Función que permite mostrar alerta en el modals y ocultarlos dentro de segundos
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
 
 // Función que muestra mensajes personalizados.
 function showModalMensajeCustom(parametros)
 {  
     //tipos de estilos para alerts
     var objTipo = {"success":"success","error":"danger","info":"info","none":"light"};
     for (const x in objTipo) {
     $('#alertaSmsCustomDiv').removeClass("alert-"+objTipo[x]);
     }
     //Iniciar parametros
     document.getElementById("btnSmsCustomOk").setAttribute("data-dismiss", "modal");
     document.getElementById("btnSmsCustomOk").removeAttribute("disabled", "");
     var tittleModal = (typeof parametros.tittle !== 'undefined')?parametros.tittle:"Mensaje";
     var strBtnOkOnClick = (typeof parametros.btnOkOnClick !== 'undefined')?parametros.btnOkOnClick:"";
     var strBtnCancelOnClick = (typeof parametros.btnCancelOnClick !== 'undefined')?parametros.btnCancelOnClick:"";
     var strBtnCancelText = (typeof parametros.btnCancelText !== 'undefined')?parametros.btnCancelText:"Cerrar";
     var strBtnOkText = (typeof parametros.btnOkText !== 'undefined')?parametros.btnOkText:"OK";
     var tipo = (typeof parametros.tipo !== 'undefined')?parametros.tipo:"error";
     var isBtnCancel = (typeof parametros.btnCancel !== 'undefined')?parametros.btnCancel:"N";
     var isDismissBtnOk = (typeof parametros.dismissBtnOk !== 'undefined')?parametros.dismissBtnOk:"S";
     var tipoAlert = "alert-danger";
     tipoAlert =  (tipo !=='')?"alert-"+objTipo[tipo]:tipoAlert;
     $('#successSmsCustomDiv').css('display', 'none');
     $('#btnSmsCustomCancel').hide();
     $('#tittelSmsCustom').text(tittleModal);
     $('#btnSmsCustomCancel').html('<span class="text-btn">'+strBtnCancelText+'</span>');
     //$('#btnSmsCustomCancel >.text-btn').text(strBtnCancelText);
     $('#btnSmsCustomOk').html('<span class="text-btn">'+strBtnOkText+'</span>');
     //$('#btnSmsCustomOk >.text-btn').text(strBtnOkText);
     $('#alertaSmsCustomDiv').addClass(tipoAlert);
     $('#alertaSmsCustomDiv').css('display', 'block');
     $('#alertaSmsCustom').html(parametros.mensaje);
     $("#btnSmsCustomOk").removeClass('btn-primary');
     $("#btnSmsCustomOk").addClass('btn-default');
     if(isBtnCancel == 'S')
     {
     $("#btnSmsCustomOk").removeClass('btn-default');
     $("#btnSmsCustomOk").addClass('btn-primary');
     $('#btnSmsCustomCancel').show();
     $("#btnSmsCustomOk").attr('onclick',strBtnCancelOnClick);
     }
     if (isDismissBtnOk ==='N')
     {
     document.getElementById("btnSmsCustomOk").removeAttribute("data-dismiss");
     }
     $("#btnSmsCustomOk").attr('onclick',strBtnOkOnClick);
     $('#modalSmsCustom').modal('show');
 }
 
 // Función que muestra u oculta el loading de un botton
 function spinnerLoadinButton(idBtn,show)
 {
     var showSpiner = (typeof show !== 'undefined')?show:'hide';
 
     if(showSpiner === 'show')
     {   $('#'+idBtn+' >.text-btn').css('display','none');
         document.getElementById(idBtn).setAttribute("disabled","disabled");
         $('#'+idBtn).append('<span class="text-spinner"><i class="fa fa-spinner fa-spin"></i>Loading<span>');
     }else{
         document.getElementById(idBtn).removeAttribute("disabled", "");
         $('#'+idBtn+' >.text-btn').css('display','block');
         $('#'+idBtn+' >.text-spinner').remove();
     }
 }
 
 function buildDataGet(data)
 {
     var  dataGet = data;  //JSON.parse(data)
     var paramtrosGet = "";
     var caracterAnd = "";
     if (Object.keys(dataGet).length > 0){
         paramtrosGet = "?";
         for (const x in dataGet) {
             paramtrosGet += caracterAnd+x+"="+dataGet[x];
             if(caracterAnd ==='') 
             {                
                 caracterAnd = "&";
             }
         }
     } 
     return paramtrosGet;    
 }
 
 // Función que retorna el index de un array
 function getIdArray(data,text,find)
 {   var c = (typeof find !== 'undefined')?find:'index';
      var value = '';
      for (const x in data) {
          if(text === data[x].id) 
          {   if(c === 'id') 
              {
                value = data[x].id
              }else if(c === 'tittle')
              {
                value = data[x].tittle
              }else
              {
                value = Number(x);
              }    
          }
      }
      return value;    
 }

 // Función que retorna un arraglo de horas
 
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
  
 // Función que crea el popover para ver mas detalle de parrafos
  
 function showPopover(idtext, e){
     var body = $('#'+idtext).html();
     var $element = $('#'+e);
     $element.popover({
         html: true,
         placement: 'top',
         container: $('body'), 
         content: '<button type="button" onClick="$(\'#'+e+'\').popover(\'hide\'); $(\'.tarea-popover\').remove();" class="close">&times;</button><div class="text-popover" style="font-size: 12px; overflow: auto; max-height: 480px;">'+
         body+'</div><div  class="modal-footer" style="padding: 5px 0 0 !important;"><button type="button" onClick="$(\'#'+e+'\').popover(\'hide\'); $(\'.tarea-popover\').remove();" class="btn btn-default btn-xs">Cerrar</button></div>'
     }).data('bs.popover')
     .tip()
     .addClass('tarea-popover')
     .css({"max-width": "480px"});
  }
   
 // Función que permite validar la fecha de las tareas
  
 function validarFechaTareaReprogramadaNew(fechaInicio, horaInicio, fechaFin, horaFin) 
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
 
 function getTiempoTotal(fechaInicio,horaInicio,fechaFin, horaFin,tipo)
 { 
     
    if(tipo === 'fecha') 
    {
         if (validate_fechaMayorQue(horaInicio, horaFin, 'hora') === 1) 
         {
             if (validate_fechaMayorQue(fechaInicio, fechaFin, tipo) === 0) 
             {
                 Ext.Msg.alert('Alerta ', 'Fecha de Cierre no puede ser menor a la Fecha de Apertura');
                 return -1;
             }
         } 
         else 
         {
             Ext.Msg.alert('Alerta ', 'La Hora de cierre es menro que la fecha de Apertura, corrija');
             return -1;
         }
     }
     if (tipo === 'hora') 
     {
         if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 2) 
         {
             if (validate_fechaMayorQue(horaInicio, horaFin, tipo) === 0) 
             {
                 Ext.Msg.alert('Alerta ', 'Hora de Cierre no puede ser menor a la Hora de Apertura');
                 return -1;
             }
         } 
         else if (validate_fechaMayorQue(fechaInicio, fechaFin, 'fecha') === 0) 
         {
             Ext.Msg.alert('Alerta ', 'La Fecha de cierre es menor que la fecha de Apertura, corrija');
             return -1;
         }
     }
     
     ///////////////////////////////////////////////////////////////////////////////7
     
     var fechas = fechaInicio.split("-");
 
     //FECHAS - DETERMINAR DIAS
     fecha = (String)(fechas[2] + "/" + fechas[1] + "/" + fechas[0]);
 
     var fechaFinS = fechaFin.split("-");
 
     fechaF = (String)(fechaFinS[2] + "/" + fechaFinS[1] + "/" + fechaFinS[0]);
 
     var fechaIni = new Date(fecha);
     var fechaFinal = new Date(fechaF);
 
     var horaFinal = horaFin;
 
     var horasTotalesInicio = horaInicio.split(":");
     var horasTotalesFin    = horaFinal.split(":");
 
     var difFecha = fechaFinal - fechaIni;
 
     //     (((fechaResta/1000)))          --> Segundos
     //     (((fechaResta/1000)/60))       --> Minutos
     //     (((fechaResta/1000)/60)/60)    --> Horas
     //     (((fechaResta/1000)/60)/60)/24 --> Días	
 
     var diasTotales = Math.ceil((((difFecha / 1000) / 60) / 60) / 24); //dias totales        
 
     var minutosAdjudicar = "";
 
     if (diasTotales > 0) 
     {
         diasTotales = diasTotales - 1;
 
         minutosInicio = (24 * 60) - (parseInt(horasTotalesInicio[0]) * 60 + parseInt(horasTotalesInicio[1]));
         minutosFin = (parseInt(horasTotalesFin[0]) * 60 + parseInt(horasTotalesFin[1]));
 
         minutosTotales = minutosInicio + minutosFin;
 
         minutosAdjudicar = (diasTotales * 1440) + minutosTotales; //minutos						
     }
     else
     {
         minutosInicio = parseInt(horasTotalesInicio[0]) * 60 + parseInt(horasTotalesInicio[1]);
         minutosFin = parseInt(horasTotalesFin[0]) * 60 + parseInt(horasTotalesFin[1]);
 
         minutosAdjudicar = minutosFin - minutosInicio;
     }
 
     return  minutosAdjudicar < 0 ? minutosAdjudicar * -1 : minutosAdjudicar;	        
 }
  
 // Función que permite validar fecha hora
  
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
  
  // Función que permite setear el punto en sesión con el punto cuyo login es el enviado como parámetro.
  
  function setPuntoSesionByLogin(strLogin)
  {
      if(confirm("Est\u00E1 seguro(a) de cambiar el punto en sesi\u00F3n ?"))
      {
          
              Ext.Ajax.request({
                  url: urlSetPtoSessionByLogin,
                  method: 'post',
                  params: { 
                      strLogin: strLogin
                  },
                  success: function(response){
                      var strRespuesta = response.responseText;
                      if(strRespuesta !== "Error")
                      {
                          window.open('/comercial/punto/'+strRespuesta+'/Cliente/show', '_blank');
                      }else{
                          Ext.MessageBox.hide();
                          Ext.Msg.alert('Error al setear punto en sesi\u00F3n.');
                      }
                  },
                  failure: function(result)
                  {
                      Ext.MessageBox.hide();
                      Ext.Msg.alert('Error',result.responseText);
                  }
              });       
          
              
      }
  }