<?php

namespace telconet\soporteBundle\WebService;

use Symfony\Component\Security\Acl\Exception\Exception;
use telconet\schemaBundle\DependencyInjection\BaseWSController;
use telconet\schemaBundle\Entity\InfoHistorialIngresoApp;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;
use telconet\tecnicoBundle\WebService\TecnicoWSController;
use telconet\schemaBundle\Entity\InfoElementoInstalacion;
use telconet\soporteBundle\Service\SoporteSDService;

/**
 * Clase que contiene las funciones necesarias para el funcionamiento del
 * Mobil Soporte.
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 2-06-2015
 */
class SoporteWSController extends BaseWSController
{

    const ID_CLIENTE       = 1;
    const ID_ADMINISTRADOR = 2;

    /**
     * Funcion que sirve para procesar las opciones que vienen desde el mobil
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 13/07/2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se adicionan nuevos métodos getUltimoEstadoTarea, putCambiarEstadoTarea,
     *                           se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 18/11/2016 Se agrega opción en el web Service para consultar la información de la asignación actual de una tarea
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 18/08/2017 Se agrega opcion en el web service para reasignar tarea
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.4 22/09/2017 - Se adiciona el método getListadoIncidencias para listar las tareas de incidencias.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.5 24/11/2017 - Se adiciona el método getListadoTareasInterdepartamentales para listar las tareas interdepartamentales.
     * @author John Vera R. <javera@telconet.ec>
     * @version 1.6 20/12/2017 - Se adiciona metodos para el nuevo movil de soporte
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.7 25/05/2018 - Switcheamos la opción de solicitar token en la app movil tecnico y operativo.
     *
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.8 08/03/2018 - Se adiciona el método putCrearCaso encargado de la creacion de casos.
     *                         - Se adiciona el método putAsignarSolicitudTarea encargado de la asignacion de tareas para la
     *                           interacion con Hal
     *                         - Se adiciona el método getSolicitarDetallePlanificacion encargado de obtener el detalle de
     *                           planificacion de las cuadrillas
     *                         - Se adiciona el método getSolicitarTrabajoCuadrilla encargado de obtener el detalle de trabajo de
     *                           las cuadrillas
     *                         - Se adiciona el método getSolicitarIntervalosTrabajo, encargado de obtener los intervalos de trabajo
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.9 20/06/2018 - Se adiciona el método getSolicitarZonas, encargado de obtener las zonas.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.0 28/06/2018 - Se adiciona el método putActualizarHorasTrabajoHAL, encargado de generar
     *                           las horas de trabajo dentro del detalle de planificacion.
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 2.1 12/07/2018 - Se adiciona el método getDatosSplitterOLT, encargado de  obtener el puerto,elemento,
     *                           modelo e interfaz del splitter y olt por servicio.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.2 16/07/2018 - Se adiciona el método getPartesAfectadasTareas,
     *                           encargado de obtener las partes afectadas de las tareas internas.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.3 17/07/2018 - Se adiciona el método putVisualizarMovil,
     *                           encargado de actualizar el campo VISUALIZAR_MOVIL para la visualización de las tareas en el telcos móvil.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.4 31/08/2018 - Se adiciona el método getTareasCaractAtenderAntes,
     *                           encargado de obtener las tareas con la característica ATENDER_ANTES.
     *                         - Se adiciona el método setTareasCaractAtenderAntes
     *                           encargado de insertar la característica ATENDER_ANTES.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.5 24/10/2018 - Se adiciona el método getEmpleados encargado de obtener la lista de empleados
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 2.6 19/12/2018 - Se agrega validaciones para el administrador de la app TN-Cliente, tipoUsuario = 2
     *
     * @author Néstor Naula López <nnaulal@telconet.ec>
     * @version 2.7 21/06/2019 - Se agrega función para crear tarea (putCrearTarea)
     * @since 2.6
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 2.8 30/07/2019 - Se adiciona el método getSLA,
     *                           encargado de obtener razón social, login y 
     *                           casos de clientes.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 2.9 06/03/2020
     * 
     * Se agrega lógica para obtener los códigos y mensajes de error del Token Security y retornar al Móvil
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 3.0 02/06/2020 - Se modifica código para crear nueva estructura de archivos.
     * 
     * @author Néstor Naula López <nnaulal@telconet.ec>
     * @version 3.1 21/05/2020 - Se agrega función para obtener información del cliente 
     *                           para el monitoreo del Zabbix (getInfoClienteZabbix)
     * @since 3.0
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 3.2 25/09/2020 - Se adiciona el método getVisualizarMovil,
     *                           encargado de obtener listado de planificacion de un listado de tareas.
     * 
     * @author Edgar Pin Villavicencio
     * @version 3.3 24/11/2020 - Se crea opcion getFibraTarea para retornar la cantidad de fibra y bobina 
     *                           utilizada por id comunicacion o id tarea
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 3.4 14/10/2020 - Se adiciona el método putPermisoJornadaAlimentacion y getSolicitarPermisoEvento,
     *                           encargado guardar y obtener permiso de alimentacion o fin de jornada, segun corresponda.
     *  
     * @author Carlos Caguana <ccaguana@telconet.ec>
     * @version 3.5 09/11/2020 Se agrega la validación de tareas
     * 
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 3.6 27/10/2020 - Se adiciona el método putPlanificarSolicitud,
     *                           encargado de realizar la planificación de una solicitud.
     * 
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 3.7 11/11/2020 - Cambios en el almacenamiento de archivos, ahora se guardarán los mismos
     *                           en el servidor NFS remoto.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 3.8 05/15/2021 - Se adiciona el método putRegistroCambioEquipo y putValidarEquiposPermitidos,
     *                           encargado guardar equipos ingresados desde el movil.
     *
     * @author Pedro Velez Quiroz <psvelez@telconet.ec>
     * @version 3.8 28/07/2021 - Se adiciona el método getHipotesisCierreCasoHal,
     *                           encargado de devolver la hipotesis de cierre de caso Hal.
     * @version 3.9 28/07/2021 - Se adiciona el método getMotivoCierreCasoHal,
     *                           encargado de devolver el motivo de cierre de caso Hal para MD.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 4.0 02/07/2021 - Se adiciona el método putCambiarYRetirarEquiposEnNodo, 
     *                           putCargaDescargaEquiposNodoInstalacion, getMotivoIngresoAlNodo, getEquiposParaRetirarNodo
     *                           para realizar control de activos de equipos en el nodo ingresados desde el movil.
     *  
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 3.9 03/08/2021 - Se adiciona los siguientes métodos para el portal de Security Data: getPersonaSD,
     *                           putCrearTareaSD, putIngresarSeguimientoSD, putAccionTareaSD,putAccionSolicitudSD, putSubirDocumentosSD.
     *
     * @author Diego Guamán <deguaman@telconet.ec>
     * @version 4.1 31/03/2023 - Se adiciona el método getDatosContactoSeguimientoTarea para obtener el registro de contactos del cliente.
     *
     * @param $request
     */
    public function procesarAction(Request $request)
    {
        $arrayData      = json_decode($request->getContent(),true);
        $response       = null;
        $token          = "";
        $objResponse    = new Response();
        $op             = $arrayData['op'];
        $boolBloqueoApp = false;
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil    = $this->get('schema.Util');

        //Carpeta segun el application que venga en el web service
        $strFolderApplication = "";
        $serviceSoporteSD     = $this->get('soporte.SoporteSDService');
        //obtener nombre del source para saber que viene de TMO
        $strNameSourceTMO      = "";
        $arrayNameSourceTMO    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne('PARAMETROS_GENERALES_MOVIL', 
                 '', 
                 '', 
                 '', 
                 'NOMBRE_SOURCE_MOVIL', 
                 '', 
                 '', 
                 ''
                 );

        if(is_array($arrayNameSourceTMO))
        {
            $strNameSourceTMO = !empty($arrayNameSourceTMO['valor2']) ? $arrayNameSourceTMO['valor2'] : "ec.telconet.mobile.telcos.operaciones";
        }

        $arrayParametrosDetNFS  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('BANDERA_NFS',
                                                    '',
                                                    '',
                                                    '',
                                                    'S',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '');
        if(isset($arrayParametrosDetNFS) && $arrayParametrosDetNFS['valor1'] === 'S')
        {
            $arrayData['bandNfs'] = true;
        }

        if($arrayData['source'])
        {

            $strFolderApplication = $serviceUtil->getValueByStructure(array('strStructure'  => 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS', 
                                                                            'strKey'        => $arrayData['source']['name']));

            if($arrayData['source']['name'] == 'ec.telconet.mobile.telcos.clientes')
            {
                $arrayParametroToken = array('token'        => $arrayData['token'],
                                             'source'       => $arrayData['source'],
                                             'user'         => $arrayData['user']);
                $token               = $this->isValidateOnlyToken($arrayParametroToken);
            }
            elseif($arrayData['source']['name'] == $strNameSourceTMO)
            {
                $arrayParametroToken = array('token'        => $arrayData['token'],
                                             'source'       => $arrayData['source'],
                                             'user'         => $arrayData['user']);

                $arrayReturnToken   = $this->validateGenerateTokenMobile($arrayParametroToken);

                if($arrayReturnToken['status'] != 200)
                {
                    return new Response(json_encode(array(
                                                            'status'    => $arrayReturnToken['status'],
                                                            'mensaje'   => $arrayReturnToken['mensaje']
                                                        )
                                                    )
                                        );
                }

                $token = $arrayReturnToken['token'];
            }
            else
            {
                $token = $this->validateGenerateToken($arrayData['token'], $arrayData['source'], $arrayData['user']);
            }

            if(!$token)
            {
                return new Response(json_encode(array(
                        'status' => 403,
                        'mensaje' => "token invalido"
                        )
                    )
                );
            }

        }
        //Verificar si el dispositivo que realiza la petición no fue eliminado anteriormente
        if($arrayData['source']['name'] == 'ec.telconet.mobile.telcos.clientes' && $op != 'putDispositivoVerificacionApp' &&

        isset($arrayData['tipoUsuario']) && $arrayData['tipoUsuario'] != self::ID_ADMINISTRADOR)
        {
            $arrayParametroBloqueo  = array('codigoDispositivo' => $arrayData['source']['originID'],
                                            'idPersona'         => $arrayData['data']['idPersona'],
                                            'user'              => $arrayData['user']);
            $arrayResponse          = $this->verificarBloqueoDispositivo($arrayParametroBloqueo);

            if($arrayResponse['status'] == $this->status['NULL'])
            {
                $response['status']  = $this->status['CONSULTA'];
                $response['mensaje'] = "Dispositivo Desvinculado";
                //Elimino el token
                $arrayParametroToken['token']   = $token;
                $this->isfinalizarToken($arrayParametroToken);
                $token                          = "";
                $boolBloqueoApp      = true;
            }
        }

        if($op && !$boolBloqueoApp)
        {

            $boolEstadoTareaActual=false;
            $strIdDetalle=$arrayData['data']['idDetalle'];
            $serviceUtils  = $this->get('schema.Util');
            if(isset($strIdDetalle))
            {
              $arrayRespuesta=$serviceUtils->estadoTarea($arrayData);
              $boolEstadoTareaActual =  $arrayRespuesta['estadoTarea'];
              $response['valor']= $arrayRespuesta['valorTarea'];
              $response['estado']= $arrayRespuesta['estadoTarea'];

            }
        
            if($boolEstadoTareaActual)
            {
            $response['mensaje']= "La tarea se encuentra ".$arrayRespuesta['valorTarea'].
            ",por favor verificarlo con su coordinador o jefe departamental";
            $response['status']= 400;
            }else
            {

            switch($op)
            {
                /********OPCIONES DE GET*************/
                case 'saveInfoTareaTiempo':
                    $response = $this->putIngresarTareaTiempo($arrayData);
                    break;
                case 'getCasosPorDepartamento':
                    $response = $this->getCasosPorDepartamento($arrayData);
                    break;
                case 'getSintomasPorCaso':
                    $response = $this->getSintomasPorCaso($arrayData);
                    break;
                case 'getTareasPorCaso':
                    $response = $this->getTareasPorCaso($arrayData);
                    break;
                case 'getHistorialCaso':
                    $response = $this->getHistorialCaso($arrayData);
                    break;
                case 'getHistorialTarea':
                    $response = $this->getHistorialTarea($arrayData);
                    break;
                case 'getSeguimientoTarea':
                    $response = $this->getSeguimientoTarea($arrayData);
                    break;
                case 'getDatosContactoSeguimientoTarea':
                    $response = $this->getDatosContactoSeguimientoTarea($arrayData);
                    break;    
                case 'getDatosFinalizarTarea':
                    $response = $this->getDatosFinalizarTarea($arrayData);
                    break;
                case 'getCatalogoSoporte':
                    $response = $this->getCatalogoSoporte($arrayData);
                    break;
                case 'identifyClient':
                    $response = $this->getIdentifyClient($arrayData);
                    break;
                case 'getDatosCerrarCaso':
                    $response = $this->getDatosCerrarCaso($arrayData);
                    break;
                case 'getActaEntregaSoporte':
                    $response = $this->getActaEntregaSoporte($arrayData);
                    break;
                case 'getEncuestaSoporte':
                    $response = $this->getEncuestaSoporte($arrayData);
                    break;
                case 'getEstadoTarea':
                    $response = $this->getEstadoTarea($arrayData);
                    break;
                case 'getUltimoEstadoTarea':
                    $response = $this->getUltimoEstadoTarea($arrayData);
                    break;
                case 'getMotivosPausar':
                    $response = $this->getMotivoPausar($arrayData);
                    break;
                case 'getDatosAsignacionActualTarea':
                    $response = $this->getDatosAsignacionActualTarea($arrayData);
                    break;
                case 'getCoordinadoresDeCuadrillas':
                    $response = $this->getCoordinadoresDeCuadrillas($arrayData);
                    break;
                case 'getCuadrillasActivas':
                    $response = $this->getCuadrillasActivas();
                    break;
                case 'getListadoIncidencias':
                    $response = $this->getListadoIncidencias($arrayData);
                    break;
                case 'getListadoTareasInterdepartamentales':
                    $response = $this->getListadoTareasInterdepartamentales($arrayData);
                    break;
                case 'getSolicitarDetallePlanificacion':
                    $response = $this->getSolicitarDetallePlanificacion($arrayData);
                    break;
                case 'getSolicitarTrabajoCuadrilla':
                    $response = $this->getSolicitarTrabajoCuadrilla($arrayData);
                    break;
                case 'getSolicitarIntervalosTrabajo':
                    $response = $this->getSolicitarIntervalosTrabajo($arrayData);
                    break;
                case 'getSolicitarZonas':
                    $response = $this->getSolicitarZonas($arrayData);
                    break;
                case 'getTareasCaractAtenderAntes':
                    $response = $this->getTareasCaractAtenderAntes($arrayData);
                    break;
                case 'getServicioPorPunto':
                    $response = $this->getServicioPorPunto($arrayData);
                    break;
                case 'getPartesAfectadasTareas':
                    $response = $this->getPartesAfectadasTareas($arrayData);
                    break;
                case 'getCatalogoSintomasTecnico':
                    $response = $this->getCatalogoSintomasTecnico($arrayData);
                    break;
                case 'getSLAporPunto':
                    $response = $this->getSLAporPunto($arrayData);
                    break;
                case 'getInfoZabbixPorPunto':
                    $response = $this->getInfoZabbixPorPunto($arrayData);
                    break;
                case 'getListadoDispositivo':
                    $response = $this->getListadoDispositivo($arrayData);
                    break;
				case 'getResumenTareasPersona':
                    $response = $this->getResumenTareasPersona($arrayData);
                    break;
				case 'getResumenTipoTareasTiempo':
                    $response = $this->getResumenTipoTareasTiempo($arrayData);
                    break;
				case 'getResumenEventosPersona':
                    $response = $this->getResumenEventosPersona($arrayData);
                    break;
                case 'getRazonesSociales':
                    $response = $this->getRazonesSociales($arrayData);
                    break;
                case 'getDispositivoPorRazonSocial':
                    $response = $this->getDispositivoPorRazonSocial($arrayData);
                    break;
                case 'getCantCasosPorRazonSocial':
                    $response = $this->getCantCasosPorRazonSocial($arrayData);
                    break;
                case 'getInfoClienteZabbix':
                    $response = $this->getInfoClienteZabbix($arrayData);
                    break;    
                case 'getDetalleEventos':
                    $response = $this->getDetalleEventos($arrayData);
                    break;
                case 'getMaxEvento':
                    $response = $this->getMaxEvento();
                    break;
                /********OPCIONES DE PUT*************/
                case 'putIngresarSeguimiento':
                    $response = $this->putIngresarSeguimiento($arrayData);
                    break;
                case 'putVisualizarMovil':
                    $response = $this->putVisualizarMovil($arrayData);
                    break;
                case 'putFinalizarTarea':
                    $response = $this->putFinalizarTarea($arrayData);
                    break;
                case 'putCerrarCaso':
                    $response = $this->putCerrarCaso($arrayData);
                    break;
                case 'putActaEntregaSoporte':
                    if(empty($strFolderApplication))
                    {
                        $strFolderApplication = $serviceUtil->getValueByStructure(array('strStructure'  => 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS', 
                                                                                        'strKey'        => $strNameSourceTMO));
                    }
                    $arrayData['strFolderApplication'] = $strFolderApplication;
                    $response = $this->putActaEntregaSoporte($arrayData);
                    break;
                case 'putEncuestaSoporte':
                    if(empty($strFolderApplication))
                    {
                        $strFolderApplication = $serviceUtil->getValueByStructure(array('strStructure'  => 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS', 
                                                                                        'strKey'        => $strNameSourceTMO));
                    }
                    $arrayData['strFolderApplication'] = $strFolderApplication;
                    $response = $this->putEncuestaSoporte($arrayData);
                    break;
                case 'putIngresarTareaInterna':
                    $response = $this->putIngresarTareaInterna($arrayData);
                    break;
                case 'putCambiarEstadoTarea':
                    $response = $this->putCambiarEstadoTarea($arrayData);
                    break;
                case 'putReasignarTarea':
                    $response = $this->putReasignarTarea($arrayData);
                    break;
                case 'insertEvento':
                    $response = $this->putIngresarEvento($arrayData);
                    break; 
                case 'updateEvento':
                    $response = $this->putActualizarEvento($arrayData);
                    break;  
                case 'getTipoEvento':
                    $response = $this->getTipoEvento($arrayData);
                    break;
                case 'getEventos':
                    $response = $this->getEventos($arrayData);
                case 'getTareasPorPersona':
                    $response = $this->getTareasPorPersona($arrayData);
                    break;
                case 'getEmpleados':
                    $response = $this->getEmpleados($arrayData);
                    break;
                case 'putNuevaCoordenadaDelPunto':
                    $response = $this->putNuevaCoordenadaDelPunto($arrayData);
                    break;
                case 'putIngresarProgresoTarea':
                    $response = $this->putIngresarProgresoTarea($arrayData);
                    break;
                case 'getEventosUser':
                    $response = $this->getEventosPersona($arrayData);
                    break;
                case 'getProgresoPorcentajeTarea':
                    $response = $this->getProgresoPorcentajeTarea($arrayData);
                    break;
                case 'getCasosCliente':
                    $response = $this->getCasosCliente($arrayData);
                    break;                
                case 'getElementoVehiculo':
                    $response = $this->getElementoVehiculo($arrayData);
                    break;
                case 'putCrearCaso':
                    $response = $this->putCrearCaso($arrayData);
                    break;
                case 'putCrearCasoNoc':
                    $response = $this->putCrearCasoNoc($arrayData);
                    break;
                case 'putAsignarSolicitudTarea':
                    $response = $this->putAsignarSolicitudTarea($arrayData);
                    break;
                case 'IngresarReasignacionCuadrilla':
                    $response = $this->IngresarReasignacionCuadrilla($arrayData);
                    break;
                case 'setTareasCaractAtenderAntes':
                    $response = $this->setTareasCaractAtenderAntes($arrayData);
                    break;
                case 'getEstadoInstalacionServicio':
                    $response = $this->getEstadoInstalacionServicio($arrayData);
                    break;

                case 'getUltimoEstadoServicioTarea':
                    $response = $this->getUltimoEstadoServicioTarea($arrayData);
                    break;

                case 'putActualizarHorasTrabajoHAL':
                    $response = $this->putActualizarHorasTrabajoHAL($arrayData);
                    break;
                case 'putCrearTarea':
                    $response = $this->putCrearTarea($arrayData);
                    break;
                case 'getDatosSplitterOLT':
                    $response = $this->getDatosSplitterOLT($arrayData);
                    break;
                case 'putCreaCasoAPP':
                    $response = $this->putCrearCasoAPP($arrayData);
                    break;
                case 'putNotificarPush':
                        $response = $this->putNotificarPush($arrayData);
                        break;
                case 'putPlanificarSolicitud':
                    $response = $this->putPlanificarSolicitud($arrayData);
                    break;    
                case 'getCaso':
                    $response = $this->getCaso($arrayData);
                    break;
                case 'getCliente':
                    $response = $this->getCliente($arrayData);
                    break;
                case 'getPuntoCliente':
                    $response = $this->getPuntoCliente($arrayData);
                    break;
                case 'getCoberturaCliente':
                    $response = $this->getCoberturaCliente($arrayData);
                    break;
                case 'getPuntoClientePorEstado':
                    $response = $this->getPuntoClientePorEstado($arrayData);
                    break;
                case 'getTareasDeCaso':
                    $response = $this->getTareasDeCaso($arrayData);
                    break;
                case 'getCasoTipoOrigen':
                    $response = $this->getCasoTipoOrigen($arrayData);
                    break;
                case 'putDispositivoVerificacionApp':
                    $response = $this->putDispositivoVerificacionApp($arrayData);
                    break;
                case 'getPerfilCliente':
                    $response = $this->getPerfilCliente($arrayData);
                    break;
                case 'putCambiarEstadoSession':
                    $response                       = $this->putCambiarEstadoSession($arrayData);
                    // Debo finalizar el token.
                    $arrayParametroToken['token']   = $token;
                    $token                          = $this->isfinalizarToken($arrayParametroToken);
                    break;
                case 'putActualizarTokenFCM':
                    $response = $this->putActualizarTokenFCM($arrayData);
                    break;
                case 'putLiberarHoras':
                    $response = $this->putLiberarHoras($arrayData);
                    break;
                case 'getDuplicidadCasoPorLogin':
                    $response = $this->getDuplicidadCasoPorLogin($arrayData);
                    break;
                case 'putFinalizarSessionRemoto':
                    $response = $this->putFinalizarSessionRemoto($arrayData);
                    break;
                case 'putActualizaInfoDispositivo':
                    $response = $this->putActualizaInfoDispositivo($arrayData);
                    break;
                case 'getDetalleTareaNetvoice':
                    $response = $this->getDetalleTareaNetvoice($arrayData);
                    break;
                 //TODO AQUI  403
                 case 'getJurisdiccionPorIdElemento':
                    $response = $this->getJurisdiccion($arrayData);
                    break;
                case 'getSLA':
                    $response = $this->getSLA($arrayData);
                    break;
                case 'getVisualizarMovil':
                    $response = $this->getVisualizarMovil($arrayData);
                    break;
                case 'getFibraPorTarea':
                    $response = $this->getFibraPorTarea($arrayData);
                    break;    
                case 'putPermisoJornadaAlimentacion':
                    $response = $this->putPermisoJornadaAlimentacion($arrayData);
                    break;
                case 'putCrearTareaExterna':
                    $response = $this->putCrearTareaExterna($arrayData);
                    break;
                case 'putRegistroCambioEquipo':
                    $response = $this->putRegistroCambioEquipo($arrayData);
                    break;
                case 'putValidarEquiposPermitidos':
                    $response = $this->putValidarEquiposPermitidos($arrayData);
                    break;
                case 'getHipotesisCierreCasoHal':
                    $response = $this->getHipotesisCierreCasoHal($arrayData);
                    break;
                case 'getPersonaSD':
                    $response = $serviceSoporteSD->getPersonaSD($arrayData["data"]);
                    break;
                case 'putCrearTareaSD':
                    $response = $serviceSoporteSD->putCrearTareaSD($arrayData["data"]);
                    break;
                case 'putIngresarSeguimientoSD':
                    $response = $serviceSoporteSD->putIngresarSeguimientoSD($arrayData["data"]);
                    break;
                case 'putAccionTareaSD':
                    $response = $serviceSoporteSD->putAccionTareaSD($arrayData["data"]);
                    break;
                case 'putAccionSolicitudSD':
                    $response = $serviceSoporteSD->putAccionSolicitudSD($arrayData["data"]);
                    break;
                case 'putSubirDocumentosSD':
                    $response = $serviceSoporteSD->putSubirDocumentosSD($arrayData["data"]);
                    break;
                case 'putCambiarYRetirarEquiposEnNodo':
                    $response = $this->putCambiarYRetirarEquiposEnNodo($arrayData);
                    break;
                case 'putCargaDescargaEquiposNodoInstalacion':
                    $response = $this->putCargaDescargaEquiposNodoInstalacion($arrayData);
                    break;
                case 'getMotivoIngresoAlNodo':
                    $response = $this->getMotivoIngresoAlNodo($arrayData);
                    break;
                case 'getEquiposParaRetirarNodo':
                    $response = $this->getEquiposParaRetirarNodo($arrayData);
                    break;
                default:
                    $response['status']  = $this->status['METODO'];
                    $response['mensaje'] = $this->mensaje['METODO'];
            }
            }
        }
        if($arrayData['source']['name'] == 'ec.telconet.mobile.telcos.clientes')
        {
            error_log("Usuario: ".$arrayData['user']."    ------   Metodo:  ".$op."    ------   Estado:  ".$response['status']);
        }
        if(isset($response))
        {
            // Funciones que pasan por alto el parametro "token" en response
            $arrayFuncionesToken = array("putCambiarEstadoSession", "putCrearTareaExterna");
            if(!in_array($op, $arrayFuncionesToken))
            {
                $response['token'] = $token;
            }
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($response));
        }

        return $objResponse;
    }
    
    /**
    * Método encargado de obtener reporte de SLA detallado para Telcograf.
    *
    * @author Karen Rodríguez <kyrodriguez@telconet.ec>
    * @version 1.0 30-07-2019
    *
    * @author Nestor Naula <nnaulal@telconet.ec>
    * @version 1.1 15-10-2019 - Se valida que la diferencia entre fecha inicial y final sea menor e igual a un mes.
    * @since 1.0
    *
    * @param  Array $arrayParametros
    * @return Array
    */
    public function getSLA ($arrayParametros)
    {
        $serviceSoporte     = $this->get('soporte.SoporteService');
        $objServiceUtil     = $this->get('schema.Util');
        $arrayAuditoria     = $arrayParametros['source'];
        $arrayData          = $arrayParametros['data'];
        try
        {
            if ((empty($arrayData['razonSocial']) && isset($arrayData['razonSocial'])) &&
               ((empty($arrayData['nombres']) && isset($arrayData['nombres'])) ||
                (empty($arrayData['apellidos']) && isset($arrayData['apellidos'])) ))
            {
                 throw new \Exception('Error : El atributo razonSocial o nombres y apellidos o login no pueden ser nulo');
            }

            if  (empty($arrayData['fechaInicio']) ||
                empty($arrayData['fechaFin']) ||
                !(isset($arrayData['fechaInicio']) ||
                isset($arrayData['fechaFin'])))
                
            {
                 throw new \Exception('Error : Debe ingresar fecha inicio y fecha fin para poder realizar la consulta');
            }

            $objFechaInicio = new \DateTime($arrayData['fechaInicio']);
            $objFechaFin    = new \DateTime($arrayData['fechaFin']);

            if ($objFechaInicio > $objFechaFin)
            {
                throw new \Exception('Error : La fechaInicio no puede ser mayor a la fechaFin');
            }

            $objDiferenciaFechas = $objFechaFin->diff($objFechaInicio);

            if ($objDiferenciaFechas->y > 0 || $objDiferenciaFechas->m > 1 ||
                    ($objDiferenciaFechas->m === 1 && $objDiferenciaFechas->d > 0))
            {
                throw new \Exception('Error : La consulta no puede ser mayor a un mes');
            }

            $objFormatDateIni = $objFechaInicio->format('d/m/Y');
            $objFormatDateFin = $objFechaFin->format('d/m/Y');
         
            $arrayRespuesta = $serviceSoporte->consultarSLATelcograf(
                    array ('strRazonSocial'     => $arrayData['razonSocial'],
                           'strNombres'         => $arrayData['nombres'],
                           'strApellidos'       => $arrayData['apellidos'],
                           'strRUC'             => $arrayData['ruc'],
                           'strFechaInicio'     => $objFormatDateIni,
                           'strFechaFin'        => $objFormatDateFin
                          ));

            if ($arrayRespuesta['status'] === 'fail')
            {
               throw new \Exception($arrayRespuesta['message']);
            }

            if (empty($arrayRespuesta['result']))
            {
                $arrayRespuesta['result'] = "No se encontró información del cliente";
            }

            return $arrayRespuesta;
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al consultar los datos';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ', $objException->getMessage())[1];
            }

            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController->getSLA',
                                          $objException->getMessage(),
                                          $arrayAuditoria['name'] ? $arrayAuditoria['name'] : 'Telcos',
                                          $arrayAuditoria['originID'] ? $arrayAuditoria['originID']   : '127.0.0.1');

            return array ('status' => 'fail', 'message' => $strMessage);
        }
            return $arrayRespuesta;
        }

    /********************************************************************************************
     * METODOS GET SOPORTE MOBIL
     ********************************************************************************************/
     /**
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 - 08-05-2019 
     * - Función que retorna la jurisdiccion a la que pertenece un canton
     */
    public function getJurisdiccion ($arrayData)
    {
        $serviceUtil        = $this->get('schema.Util');
        $arrayResultado     = [];
        $strUser            = $arrayData['user'];
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $intElementoId      = $arrayData['data']['elemetoId'];
        try
        {
            $arrayInfoCanton = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                ->getJurisdiccionPorElemtoId($intElementoId);

            if($arrayInfoCanton['status']  == 'ok' && $arrayInfoCanton['registros'] != null)
            {
                $arrayResultado['status']  = $this->status['OK'];
                $arrayResultado['mensaje'] = $this->mensaje['OK'];
                $arrayResultado['data']    = $arrayInfoCanton['registros'][0]['jurisdiccion'];    
            }
            else
            {
                $arrayResultado['status']  = $this->status['CONSULTA'];
                $arrayResultado['mensaje'] = $this->mensaje['CONSULTA'];
                $arrayResultado['data']    = 'Consulta no válida, el canton 
                                             no existe en nuestro sistema.
                                             Por favor reportar a Soporte Sistemas
                                             para que el cantón pueda ser ingresado.' ;
                
                $serviceUtil->insertError('Telcos+',
                                            'SoporteWSController.getJurisdiccion',
                                            $arrayInfoCanton['mensaje'],
                                            $strUser,
                                            '127.0.0.1');
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+',
                                      'SoporteWSController.getJurisdiccion',
                                      $ex->getMessage(),
                                      $strUser,
                                      '127.0.0.1');

            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $this->mensaje['ERROR'];
            $arrayResultado['data']    = '';
        }

        return $arrayResultado;
    }
    /**
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 - 25-03-2018
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 - 25-04-2018 - Validar la respuesta nula.
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.2 - 12-09-2018 - Retorna el limite mínimo entre las puntas
     * @since 1.1
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.3 - 21-03-2019 - Se agrega validación del servicioId antes de realizar la consulta de la descripción.
     * @since 1.2
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.4 - 28-03-2019 - Se agrega campo "detSolicitudId" para guardar fibra posteriormente.
     * @since 1.3
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.4 - 17-07-2019 - Se modifica la función para retorno correcto de la descripción en la tarea.
     * @since 1.4
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.5 - 12-07-2019 - Se cambia lógica por mejora en el método,
     * se agregan campos para obtener el plan o el producto del servicio,
     * se agrega campo donde se envía si la tarea tiene extender o no
     * @since 1.4
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.6 - 07-02-2020 
     * se agregan campos para obtener el número del caso
     * @since 1.5
     *  
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @author Ronny Moran <rmoranc@telconet.ec>
     * @version 1.7 - 28-07-2020 
     * se agrega campo "georutaIngresada" para realizar validación en el movil
     * @since 1.6
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.8 21/09/2020
     * se agrega lógica para filtrar tareas dependiendo de parámetros 
     * y que se mostraran en el movil
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.9 19/10/2020
     * se agrega lógica para identificar cuadrillas Hal y contar tareas pausadas
     * @version 1.9 22/10/2020
     * se agrega lógica para enviar permisos ya sea alimentacion o fin de jornada al app TMO
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 2.0 08/04/2021 Se agrega validacion para anexar valores a la tarea cuando es un CE empaquetado
     *                      para que el proceso lo tome como adicional al momento de activar y finalizar la tarea
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.1 21/05/2021 Se agrega el tipo de red MPLS o GPON del servicio al arreglo de la tarea.
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 2.1 04/06/2021 Se agregan validaciones al obtener la descripción del producto.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.2 10/06/2021 Se agrega el tipo de red MPLS o GPON del servicio al arreglo de la tarea.
     *
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 2.3 05/07/2021
     * se agrega lógica para discriminar cuando una tarea este asignada a realizar cambios en el nodo
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 2.4 20/09/2022
     * Se agrega campo Codigo de Trabajo en tareas de Casos Tecnico MD.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.5 23/09/2022 Se agrega la característica placa para los servicios Seg Vehiculos.
     *
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 2.6 12/11/2022 Se agrega criterios para mostrar campo Codigo de Trabajo.
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 2.7 11-01-2023 - Se obtiene el servicio SEG VEHICULOS cuando este llega vacio en los parametros
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 2.8 05-04-2023 - Se restringue los tipos de tareas para la empresa ECUANET
     * 
     * @author Axel Auza <aauza@telconet.ec>
     * @version 2.9 07-06-2023 - Se agregan nombre tecnico en array resultado
     * 
     * 
     * @param array $arrayData
     * 
     */
    public function getTareasPorPersona($arrayData)
    {
        $serviceUtil    = $this->get('schema.Util');
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        $arrayResultado             = [];
        $arrayResultado['status']   = $this->status['OK'];
        $arrayResultado['mensaje']  = $this->mensaje['OK'];
        $arrayResultado['permisos'] = array();
        $strBanderaTareasPermitidas = 'N';
        $arrayProductosSinData      = array();
        $boolProductoSinDataTecnica = false;
        
        try{
            
            $arrayParametros['user']        = $this->container->getParameter('user_soporte');
            $arrayParametros['pass']        = $this->container->getParameter('passwd_soporte');
            $arrayParametros['db']          = $this->container->getParameter('database_dsn');            
            $arrayParametros['intPersona']  = $arrayData['data']['intPersona'];
            $intIdCuadrilla                 = $arrayData['data']['idCuadrilla'];
            $strCuadrillaEsHal              = 'N';
            $intNumTareasPausadas           = 0;
            $objVersionActualMobile         = $arrayData['data']['versionMobile'];
            $intIdCabecera=$arrayData['data']['idCabecera'];

            
            $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");  
            $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
            $strUltimaVersion   = $emSoporte->getRepository('schemaBundle:InfoVersionMobile')->ObtenerUltimaVersionMobile();
                
            $arrayResultParcial[] = array(  'idTarea'           => 0,
                                            'tarea'             => 'FINALICE SU JORNADA',
                                            'feSolicitada'      => date("j, n, Y"),
                                            'solicitud'         => 0,
                                            'idEmpresa'         => 18,
                                            'idServicio'        => 0,
                                            'idPunto'           => 0,
                                            'idPersona'         => 0,
                                            'nombre'            => 'ACTUALIZAR EL APLICATIVO A LA VERSION '.$strUltimaVersion,
                                            'razonSocial'       => 'INFORMACION',
                                            'telefono'          => '0',
                                            'direccion'         => 'SN',
                                            'latitud'           => 0,
                                            'longitud'          => 0,
                                            'idElemento'        => 0,
                                            'nombreElemento'    => '',
                                            'tipoElemento'      => '',
                                            'modeloelemento'    => '',
                                            'ciudad'            => '',
                                            'estadoTarea'       => '',
                                            'esSolucion'        => '',
                                            'idCaso'            => 0,
                                            'tipoCaso'          => '',
                                            'nivelCriticidad'   => '',
                                            'descNivelCriticidad'  => '',
                                            'idDetalle'         => 0,
                                            'prefijoEmpresa'    => '',
                                            'tipoServicio'      => '',
                                            'idTipoTarea'       => 0,
                                            'idProgresoTarea'   => 0,
                                            'porcentaje'        => 0,
                                            'login'             => 'ACTUALICE',
                                            'tipoMedio'         => '',
										    'factibilidad'      => '',
                                            'esEnlace'          => '',
                                            'limitePuntas'      => 0,
                                            'descripcion'       => '',
                                            'codigoTrabajo'     => ''
                                );
            
            if($objVersionActualMobile != null && isset($objVersionActualMobile) && !empty($objVersionActualMobile))
            {
                if((strcmp($strUltimaVersion,$objVersionActualMobile) == 0))
                {
                    $strResultado = $emSoporte->getRepository('schemaBundle:InfoPersona')->getTareasPorPersona($arrayParametros);
                    if(!empty($strResultado))
                    {
                        $arrayBase              = explode('&&', $strResultado);
                        $arrayAdmiParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne(   'MINIMA_DIFERENCIA_PUNTA', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '');
                        $strValorLimiteFibra    = $arrayAdmiParametroDet["valor1"];
                        foreach($arrayBase as $strData)
                        {
                            $arrayValue = explode('|',$strData);

                            $arrayParametrosExtender = array('intPuntoId'   => $arrayValue[6]);
                            
                            $arrayResultExtender     = $emSoporte->getRepository('schemaBundle:InfoServicio')
                                                                 ->isPuntoExtender($arrayParametrosExtender);

                            if($arrayResultExtender['status'] == 'OK' && $arrayResultExtender['respuesta'])
                            {
                                $strTieneExtender = 'S';
                            }
                            else
                            {
                                $strTieneExtender = 'N';
                            }
                            $strPermisoKml = 'N';
                            
                            if($arrayValue[25] != null || $arrayValue[25] != 0)
                            {
                                $arrayData = array ('idDetalle'              => $arrayValue[25],
                                                    'nombreCaracteristica'   =>  'AUTH_CREACION_KML',
                                                    'idComunicacion'         => $arrayValue[0]);


                                $strPermisoKml = $emSoporte ->getRepository('schemaBundle:InfoDetalle')
                                                ->validarCaracteristicaIdDetalle($arrayData);
                            
                            }

                            // Anexa el producto para cableado
                            $arrayParametroTipos = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('VALIDA_PROD_ADICIONAL','COMERCIAL','',
                                        'Solicitud cableado ethernet','','','','','','18');
                            if (is_array($arrayParametroTipos) && !empty($arrayParametroTipos))
                            {
                                $objCableParametro = $arrayParametroTipos[0];
                            }
                            if ($arrayValue[3] == $objCableParametro['valor2'])
                            {
                                $arrayValue[38] = $objCableParametro['valor1'];
                                if(isset($objCableParametro['valor1']))
                                {
                                    $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->find($objCableParametro['valor1']);
                                    if(is_object($objProducto))
                                    {
                                        $arrayValue[39] = $objProducto->getDescripcionProducto();
                                    }    
                                }    
                            }

                            $strCodigoTrabajo = '';
                            $objCaractCodTrab = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica('CODIGO_TRABAJO');
                            if(is_object($objCaractCodTrab) && !empty($objCaractCodTrab)) 
                            {
                                $arrayBanderaCodTrabTN    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                ->getOne('PARAMETROS_GENERALES_MOVIL', 
                                        '', 
                                        '', 
                                        '', 
                                        'CODIGO_TRABAJO_TN', 
                                        '', 
                                        '', 
                                        ''
                                        );

                                if(is_array($arrayBanderaCodTrabTN))
                                {
                                    $strBanderaCodTrabTN = !empty($arrayBanderaCodTrabTN['valor2']) ? $arrayBanderaCodTrabTN['valor2'] : 'N';
                                }
                                $arrayTiposServCodTrab    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                ->getOne('PARAMETROS_GENERALES_MOVIL', 
                                        '', 
                                        '', 
                                        '', 
                                        'TIPOS_DE_SERVICIO', 
                                        '', 
                                        '', 
                                        ''
                                        );

                                if(is_array($arrayTiposServCodTrab))
                                {
                                    $arrayTiposServCodTrab = !empty($arrayTiposServCodTrab['valor2']) ? 
                                                        explode(",",$arrayTiposServCodTrab['valor2']) : '';
                                }

                                if ((!empty($arrayValue[21]) && $arrayValue[22] == 'Tecnico') || (in_array($arrayValue[27],$arrayTiposServCodTrab))) 
                                {
                                    $strTareaEmpresa = $arrayValue[26];
                                    if ($strTareaEmpresa == 'MD' || ($strBanderaCodTrabTN == 'S' && $strTareaEmpresa == 'TN')) 
                                    {
                                        $objInfoTareaCaract = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                                                    ->findOneBy( array('tareaId'          => $arrayValue[0],
                                                                        'detalleId'        => $arrayValue[25],
                                                                        'caracteristicaId' => $objCaractCodTrab->getId(),
                                                                        'estado'           => 'Activo'));
                                        if (is_object($objInfoTareaCaract))
                                        {
                                            $strCodigoTrabajo = $objInfoTareaCaract->getValor();
                                        }
                                    }
                                }
                            }
                            
                            $arrayResult[] = array( 'idTarea'               => $arrayValue[0],
                                                    'tarea'                 => $arrayValue[1],
                                                    'feSolicitada'          => $arrayValue[2],
                                                    'solicitud'             => $arrayValue[3],
                                                    'idEmpresa'             => $arrayValue[4],
                                                    'idServicio'            => $arrayValue[5],
                                                    'idPunto'               => $arrayValue[6],
                                                    'idPersona'             => $arrayValue[7],
                                                    'nombre'                => $arrayValue[8],
                                                    'razonSocial'           => $arrayValue[9],
                                                    'telefono'              => $arrayValue[10],
                                                    'direccion'             => $arrayValue[11],
                                                    'latitud'               => (float)$arrayValue[12],
                                                    'longitud'              => (float)$arrayValue[13],
                                                    'idElemento'            => $arrayValue[14],
                                                    'nombreElemento'        => $arrayValue[15],
                                                    'tipoElemento'          => $arrayValue[16],
                                                    'modeloelemento'        => $arrayValue[17],
                                                    'ciudad'                => $arrayValue[18],
                                                    'estadoTarea'           => $arrayValue[19],
                                                    'esSolucion'            => $arrayValue[20],
                                                    'idCaso'                => $arrayValue[21],
                                                    'tipoCaso'              => $arrayValue[22],
                                                    'nivelCriticidad'       => $arrayValue[23],
                                                    'descNivelCriticidad'   => $arrayValue[24],
                                                    'idDetalle'             => $arrayValue[25],
                                                    'prefijoEmpresa'        => $arrayValue[26],
                                                    'tipoServicio'          => $arrayValue[27],
                                                    'idTipoTarea'           => $arrayValue[28],
                                                    'idProgresoTarea'       => $arrayValue[29],
                                                    'porcentaje'            => $arrayValue[30],
                                                    'login'                 => $arrayValue[31],
                                                    'tipoMedio'             => $arrayValue[32],
													'factibilidad'          => $arrayValue[33],
                                                    'esEnlace'              => $arrayValue[34],
                                                    'detSolicitudId'        => $arrayValue[35],
                                                    'limitePuntas'          => $strValorLimiteFibra,
                                                    'idPlan'                => $arrayValue[36],
                                                    'nombrePlan'            => $arrayValue[37],
                                                    'idProducto'            => $arrayValue[38],
                                                    'nombreProducto'        => $arrayValue[39],
                                                    'numeroCaso'            => $arrayValue[40],
                                                    'tieneExtender'         => $strTieneExtender,
                                                    'georutaIngresada'      => $arrayValue[41],
                                                    'puedeCrearKml'         => $strPermisoKml,
                                                    'codigoTrabajo'         => $strCodigoTrabajo,
                                                    'nombreTecnico'         => ""
                                                );
                        }

                        //obtener bandera para filtro de tareas
                        $arrayBanderaTareasPermitidas    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne('PARAMETROS_GENERALES_MOVIL', 
                                '', 
                                '', 
                                '', 
                                'BANDERA_TAREAS_PERMITIDAS', 
                                '', 
                                '', 
                                ''
                                );

                        if(is_array($arrayBanderaTareasPermitidas))
                        {
                            $strBanderaTareasPermitidas = !empty($arrayBanderaTareasPermitidas['valor2']) ? 
                            $arrayBanderaTareasPermitidas['valor2'] : 'N';
                        }

                        //validar si la cuadrilal es hal
                        $objAdmiCuadrilla = $emComercial->getRepository("schemaBundle:AdmiCuadrilla")
                        ->findOneById($intIdCuadrilla);

                        if(is_object($objAdmiCuadrilla))
                        {
                            $strCuadrillaEsHal = $objAdmiCuadrilla->getEsHal();
                        }

                        //contabilizar número de tareas pausadas
                        for($intIteration = 0; $intIteration < count($arrayResult); $intIteration++)
                        {
                            if($arrayResult[$intIteration]['estadoTarea'] == 'Pausada')
                            {
                                $intNumTareasPausadas++;
                            }
                        }

                        if($strBanderaTareasPermitidas == 'S' && $strCuadrillaEsHal != 'S')
                        {
                            $arrayResult = $this->taskFilter($arrayResult);
                        
                            if($arrayResult['status'] == 200)
                            {
                                $arrayResultado['data'] = $arrayResult['data'];                                
                            }
                            else
                            {
                                $arrayResultado['data'] = $arrayResultParcial;
                            }
                        }
                        else
                        {
                            $arrayResultado['data'] = $arrayResult;
                        }
                    }
                }else{
                    $arrayResultado['data']     = $arrayResultParcial;
                }
            }else{
                 $arrayResultado['data']     = $arrayResultParcial;
            }

            $arrayResultadoTareas = array();
            foreach ($arrayResultado['data'] as $objTarea)
            {
                $arrayProductosParam =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('PRODUCTOS SIN DATA TECNICA', 
                                                            '', 
                                                            '', 
                                                            'PRODUCTOS MOVIL',
                                                            '', 
                                                            '', 
                                                            '', 
                                                            ''
                                                            );

                foreach($arrayProductosParam as $formaProductos)
                { 
                    $objInfoProducto= $emComercial->getRepository('schemaBundle:AdmiProducto')->find($formaProductos['valor1']);
                    $arrayProductosSinData[] = array(
                                            'idProducto' => $objInfoProducto->getId(),
                                            'descripcion' => $objInfoProducto->getDescripcionProducto()
                                            );
                }

                if (is_array($arrayProductosSinData)) 
                {
                    foreach ($arrayProductosSinData as $key) 
                    {
                        if ($objTarea['idProducto'] == $key['idProducto']) 
                        {
                            $boolProductoSinDataTecnica = true;
                        }
                    }
                }
                //Se realiza la obtencion del servicio SEG_VEHICULO
                if ($objTarea['idServicio'] == null && !empty($objTarea['idProducto']) && !empty($objTarea['idPunto'])) 
                {
                    $objProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($objTarea['idProducto']);

                    if (is_object($objProducto) && $objProducto->getNombreTecnico() == 'SEG_VEHICULO') 
                    {
                        $arrayEstadosNoPermitidos = array();

                        $arrayParametrosEstados   = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('PARAMETROS_SEG_VEHICULOS',
                                                                    'TECNICO',
                                                                    '',
                                                                    '',
                                                                    'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '');
                        foreach($arrayParametrosEstados as $arrayDetalles)
                        {
                            $arrayEstadosNoPermitidos[] = $arrayDetalles['valor2'];
                        }

                        $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                       ->createQueryBuilder('s')
                                       ->where("s.puntoId = :puntoId")
                                       ->andWhere("s.productoId = :productoId")
                                       ->andWhere("s.estado NOT IN (:estados)")
                                       ->setParameter('puntoId', $objTarea['idPunto'])
                                       ->setParameter('productoId', $objProducto->getId())
                                       ->setParameter('estados', array_values($arrayEstadosNoPermitidos))
                                       ->setMaxResults(1)
                                       ->getQuery()
                                       ->getOneOrNullResult();

                        if (is_object($objServicio)) 
                        {
                            $objTarea['idServicio'] = $objServicio->getId();
                        }
                    }
                }

                if($objTarea['idServicio']!=null && !$boolProductoSinDataTecnica)
                {
                    $arrayDataGetPotencia = array('data' => array(
                        'codEmpresa' =>     $objTarea['idEmpresa'],
                        'idServicio' =>     $objTarea['idServicio'],
                        'prefijoEmpresa'=>  $objTarea['prefijoEmpresa'],
                        'nombreCliente' =>  $objTarea['nombre'],
                        'loginCliente' =>   $objTarea['login'],
                        'ipCreacion' =>     '127.0.0.1',
                        'evaluarPotencia'=> false,
                    ),
                    'op' => 'getPotenciaDataTecnica',
                    'user' => $arrayData['user']
                    );
                    $objTecnicoWSController = new TecnicoWSController();
                    $objTecnicoWSController->setContainer($this->container);
                    $arrayObtenido = $objTecnicoWSController->getPotenciaDataTecnica($arrayDataGetPotencia);
                    
                    $arrayElementoCaja = array(
                        'tipoElemento'   => 'CAJA',
                        'direccion'      => $arrayObtenido['data']['datosBackbone']['direccion'],
                        'latitud'        => $arrayObtenido['data']['datosBackbone']['latitud'] ,
                        'longitud'       => $arrayObtenido['data']['datosBackbone']['longitud'] ,
                        'nombreElemento' => $arrayObtenido['data']['datosBackbone']['nombreElementoContenedor']
                        );

                    $arrayUbicacionCliente = array(
                        'tipoElemento'  => 'CLIENTE',
                        'direccion'     => $objTarea['direccion'],
                        'latitud'       => $objTarea['latitud'] ,
                        'longitud'      => $objTarea['longitud'] ,
                        'nombreElemento'=> $objTarea['nombre']
                        );

                               
                    $arrayUbicacionNodo = array(
                        'tipoElemento'  => 'NODO',
                        'direccion'     => "",
                        'latitud'       => null,
                        'longitud'      => null,
                        'nombreElemento'=> ""
                        );

                      if($arrayObtenido['data']['datosBackbone']['elementoNodo'] != null)
                      {
                        $arrayUbicacionNodo = $arrayObtenido['data']['datosBackbone']['elementoNodo']; 
                      }

                        $objTarea['opcionesMovilizacion'] = array( 
                                                            $arrayElementoCaja,
                                                            $arrayUbicacionNodo,
                                                            $arrayUbicacionCliente );

                        //se setea el tipo de red
                        $strTipoRed  = "MPLS";
                        //seteo el nombre tecnico
                        $objTarea['nombreTecnico'] = "";
                        //obtengo el servicio
                        $objServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->find($objTarea['idServicio']);
                        if(is_object($objServicio) && is_object($objServicio->getProductoId()))
                        {
                            $objTarea['nombreTecnico'] = $objServicio->getProductoId()->getNombreTecnico();
                            $objCaractTipoRed = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica("TIPO_RED");
                            if(is_object($objCaractTipoRed))
                            {
                                $objProCaractTipoRed = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array("productoId"       => $objServicio->getProductoId()->getId(),
                                                                          "caracteristicaId" => $objCaractTipoRed->getId()));
                                if(is_object($objProCaractTipoRed))
                                {
                                    $objServProdCaractTipoRed = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findOneBy(array('servicioId'                => $objServicio->getId(),
                                                                          'productoCaracterisiticaId' => $objProCaractTipoRed->getId()));
                                    if(is_object($objServProdCaractTipoRed))
                                    {
                                        $strTipoRed = $objServProdCaractTipoRed->getValor();
                                    }
                                }
                            }
                            //validar caracteristica unica placa
                            $objCaractPlaca = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica("PLACA");
                            if(is_object($objCaractPlaca))
                            {
                                $objProCaractPlaca = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array("productoId"       => $objServicio->getProductoId()->getId(),
                                                                          "caracteristicaId" => $objCaractPlaca->getId()));
                                if(is_object($objProCaractPlaca))
                                {
                                    $objServProdCaractPlaca = $emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findOneBy(array('servicioId'                => $objServicio->getId(),
                                                                          'productoCaracterisiticaId' => $objProCaractPlaca->getId()));
                                    if(is_object($objServProdCaractPlaca))
                                    {
                                        $objTarea['placa'] = $objServProdCaractPlaca->getValor();
                                    }
                                }
                            }
                        }
                        $objTarea['tipoRed'] = $strTipoRed;
                        //se agrega el objecto
                        $arrayResultadoTareas[] = $objTarea;
                }
                else if($objTarea['direccion'] != null  && $objTarea['tipoCaso'] != 'Backbone')
                {

                    $arrayElementoCaja = array(
                        'tipoElemento'   => 'CAJA',
                        'direccion'      => $arrayObtenido['data']['datosBackbone']['direccion'],
                        'latitud'        => $arrayObtenido['data']['datosBackbone']['latitud'] ,
                        'longitud'       => $arrayObtenido['data']['datosBackbone']['longitud'] ,
                        'nombreElemento' => $arrayObtenido['data']['datosBackbone']['nombreElementoContenedor']
                        );
                        
                    $arrayUbicacionCliente = array(
                        'tipoElemento'  => 'CLIENTE',
                        'direccion'     => $objTarea['direccion'],
                        'latitud'       => $objTarea['latitud'] ,
                        'longitud'      => $objTarea['longitud'] ,
                        'nombreElemento'=> $objTarea['nombre']
                        );

   
                    $arrayUbicacionNodo = array(
                            'tipoElemento'  => 'NODO',
                            'direccion'     => "",
                            'latitud'       => 0.0 ,
                            'longitud'      => 0.0 ,
                            'nombreElemento'=> ""
                            );
                                 


                    $objTarea['opcionesMovilizacion'] = array( 
                            $arrayElementoCaja,
                            $arrayUbicacionNodo,
                            $arrayUbicacionCliente );

                    $arrayResultadoTareas[] = $objTarea;
                }else if($objTarea['tipoCaso'] == 'Backbone')
                {

                    $objDetalleHipotesis = $emSoporte->getRepository('schemaBundle:InfoDetalleHipotesis')
                                                      ->findOneBy(array('casoId' => $objTarea['idCaso']));
                    $arrayParteAfectada = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                  ->getParteAfectadaPorDetalleId($objDetalleHipotesis->getId());
                    $arrayUbicacionNodo = array(
                        'tipoElemento'  => 'NODO',
                        'direccion'     => "",
                        'latitud'       => 0.0 ,
                        'longitud'      => 0.0 ,
                        'nombreElemento'=> ""
                        );
                    if($arrayParteAfectada[0]['strTipoAfectado'] == 'Elemento')
                    {
                         $emInfraestructura        = $this->getDoctrine()->getManager("telconet_infraestructura");
                         $objElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                             ->findOneBy(array("elementoId" => $arrayParteAfectada[0]['intAfectadoId']));

                        $objUbicacion = null;
                        if(is_object($objElementoUbica->getUbicacionId()))
                        {
                            $objUbicacion     = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                                                    ->find($objElementoUbica->getUbicacionId()->getId());
                            if(is_object($objUbicacion)) 
                            {
                                $arrayUbicacionNodo = array(
                                                    'tipoElemento'  => 'ELEMENTO ASOCIADO',
                                                    'direccion'     => "",
                                                    'latitud'       => $objUbicacion->getLatitudUbicacion() ,
                                                    'longitud'      => $objUbicacion->getLongitudUbicacion(),
                                                    'nombreElemento'=> ""
                                                    );    
                            }
                        }
                    }
                    $arrayElementoCaja = array(
                        'tipoElemento'   => 'CAJA',
                        'direccion'      => 0.0,
                        'latitud'        => 0.0,
                        'longitud'       => 0.0,
                        'nombreElemento' => ""
                        );

                    $arrayUbicacionCliente = array(
                            'tipoElemento'  => 'CLIENTE',
                            'direccion'     => $objTarea['direccion'],
                            'latitud'       => $objTarea['latitud'] ,
                            'longitud'      => $objTarea['longitud'] ,
                            'nombreElemento'=> $objTarea['nombre']
                            );                
                         
                    $objTarea['opcionesMovilizacion'] = array( 
                        $arrayElementoCaja,
                        $arrayUbicacionNodo,
                        $arrayUbicacionCliente );
                    $arrayResultadoTareas[] = $objTarea;
                }
                elseif ($objTarea['tipoElemento'] == 'NODO') 
                {

                    $arrayElementoCaja = array(
                        'tipoElemento'   => 'CAJA',
                        'direccion'      => 0.0,
                        'latitud'        => 0.0,
                        'longitud'       => 0.0,
                        'nombreElemento' => ""
                        );
                        
                    $arrayUbicacionNodo = array(
                        'tipoElemento'  => 'NODO',
                        'direccion'     => $objTarea['nombreElemento'],
                        'latitud'       => $objTarea['latitud'] ,
                        'longitud'      => $objTarea['longitud'] ,
                        'nombreElemento'=> $objTarea['nombreElemento']
                        );
                     
                    $arrayUbicacionCliente = array(
                            'tipoElemento'  => 'CLIENTE',
                            'direccion'     => "",
                            'latitud'       => 0.0 ,
                            'longitud'      => 0.0 ,
                            'nombreElemento'=> ""
                            );
                         
                    $objTarea['opcionesMovilizacion'] = array( 
                        $arrayElementoCaja,
                        $arrayUbicacionNodo,
                        $arrayUbicacionCliente );
                    $arrayResultadoTareas[] = $objTarea;
                }
                else 
                {
                    $arrayElementoCaja = array(
                        'tipoElemento'   => 'CAJA',
                        'direccion'      => 0.0,
                        'latitud'        => 0.0,
                        'longitud'       => 0.0,
                        'nombreElemento' => ""
                        );
                        
                    $arrayUbicacionNodo = array(
                        'tipoElemento'  => 'NODO',
                        'direccion'     => "",
                        'latitud'       => 0.0 ,
                        'longitud'      => 0.0 ,
                        'nombreElemento'=> ""
                        );
                     
                    $arrayUbicacionCliente = array(
                            'tipoElemento'  => 'CLIENTE',
                            'direccion'     => "",
                            'latitud'       => 0.0 ,
                            'longitud'      => 0.0 ,
                            'nombreElemento'=> ""
                            );
                         
                
                    $objTarea['opcionesMovilizacion'] = array( 
                        $arrayElementoCaja,
                        $arrayUbicacionNodo,
                        $arrayUbicacionCliente );
                    $arrayResultadoTareas[] = $objTarea;
                }
            }
            $arrayResultado['data'] = $arrayResultadoTareas;
            $arrayResultado['numTareasPausadas'] = $intNumTareasPausadas;


            if(!empty($intIdCabecera) && is_numeric($intIdCabecera) && $intIdCabecera!=0)
            {
    
                $arrayRequestPermisos = array(
                    'idCabecera'    => $intIdCabecera
                );

                $arrayResponsePermisos  = $this->obtenerPermisosJornadaAlimentacion($arrayRequestPermisos);

                if(!empty($arrayResponsePermisos) && 
                    count($arrayResponsePermisos) > 0 && 
                    $arrayResponsePermisos['status'] == 200 &&
                    count($arrayResponsePermisos['data']) > 0)
                {
                    $arrayResultado['permisos'] = array(
                        'status'            => $arrayResponsePermisos['status'],
                        'alimentacion'      => $arrayResponsePermisos['data']['AutorizaAlimentacion'],
                        'finalizarJornada'  => $arrayResponsePermisos['data']['AutorizaFinalizar']
                    );
                }
                else
                {
                    $arrayResultado['permisos'] = array(
                        'status'            => 100,
                        'alimentacion'      => null,
                        'finalizarJornada'  => null
                    );
                }

           }
            else
           {

                $arrayResultado['permisos'] = array(
                    'status'            => 100,
                    'alimentacion'      => null,
                    'finalizarJornada'  => null
                );
           }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError(  'Telcos Mobile', 
                                        'TecnicoWSController.getTareasPorPersona', 
                                        $ex->getMessage(),
                                        $arrayData['user'],
                                        "127.0.0.1"); 

            $arrayResultado['status'] = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $this->mensaje['ERROR'];
            $arrayResultado['data'] = '';
            $arrayResultado['numTareasPausadas'] = 0;
        }
        return $arrayResultado;
    }

    
    /**
     * Funcion que sirve para obtener los Casos asignados
     * a un departamento
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 22/05/2017 - Se obtiene la información del cliente y la caja si el servicio del mismo es Fibra Optica.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.3 23/06/2017 - Se reversa logica de manera de obtener el idServicio en MD cuando se cree un caso
     *                           en el cual no se afecte un servicio.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.4 26/02/2018 - Se envia el metraje maximos permitido para no actualizar las coordenadas del cliente.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.5 26/04/2018 - Se obtiene la información de la caja para los casos de MD.
     * @param array $arrayData
     * @return array $resultado
     * 
     * @author Wilmer Vera González <wvera@telconet.ec>
     * @version 1.6 11/07/2019 - Se añade idElemento en la respuesta del backBone.
     * @since 1.5
     * @param array $arrayData
     * @return array $resultado
     */
    private function getCasosPorDepartamento($arrayData)
    {
        $mensaje            = "No se pudieron obtener los casos";
        $idCaso             = 0;
        $intIdServicio      = 0;
        $strEstadoServicio  = "";
        $strMetros          = 0;
        
        try
        {
            $idCaso         = $arrayData['data']['idCaso'];
            $codEmpresa     = $arrayData['data']['codEmpresa'];
            $user           = $arrayData['user'];
            $start          = $this->get('request')->query->get('start');
            $limit          = $this->get('request')->query->get('limit');
            $emComercial    = $this->getDoctrine()->getManager("telconet");
            $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
            $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
            $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
            $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");

            //obtener los datos y departamento de la persona por empresa
            $datosUsuario   = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaDepartamentoPorUserEmpresa($user, $codEmpresa);

            //obtener los casos asignados al departamento del usuario que realiza la peticion
            $casos          = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                        ->getCasosPorDepartamento($datosUsuario['ID_PERSONA'], $codEmpresa, $idCaso);
            
            if(count($casos)<1)
            {
                throw new \Exception('NULL');
            }
            
            $idCaso         = 0;
            foreach($casos as $casoMobil)
            {
                if($idCaso != $casoMobil['idCaso'])
                {
                    //validar si el caso esta en null (finalizado, cancelado, etc)
                    if($casoMobil['estadoCaso'] != null)
                    {
                        $cerrarCaso = "";
                        $idCaso     = $casoMobil['idCaso'];

                        //validar si se puede cerrar caso
                        $numTareasAbiertas = $emSoporte->getRepository('schemaBundle:InfoCaso')->getCountTareasAbiertas($idCaso,'Abiertas');
                        $numTareasSolucion = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                                       ->getCountTareasAbiertas($idCaso,'FinalizadasSolucion');

                        if($numTareasAbiertas == 0 && $numTareasSolucion > 0)
                        {
                            $cerrarCaso = "S";
                        }
                        else
                        {
                            $cerrarCaso = "N";
                        }

                        // Obtener información del cliente, la caja y el idServicio en base a un caso.
                        $arrayParametros['intIdCaso'] = $casoMobil['idCaso'];
                        $arrayParametros['intCodigoEmpresa'] = $arrayData['data']['codEmpresa'];
                        $arrayInformacionClienteCaja  = $this->getInformacionClienteCajaCaso($arrayParametros);
                        $arrayCliente                 = array();
                        $arrayBackbone                = array();
                        if(isset($arrayInformacionClienteCaja['arrayCliente']))
                        {
                            $arrayCliente = $arrayInformacionClienteCaja['arrayCliente'];
                        }
                        if(isset($arrayInformacionClienteCaja['arrayBackbone']))
                        {
                            $arrayBackbone = $arrayInformacionClienteCaja['arrayBackbone'];
                        }
                        $intIdServicio        = isset($arrayInformacionClienteCaja['idServicio']) ? $arrayInformacionClienteCaja['idServicio'] : 0;
                        $strEstadoServicio    = $arrayInformacionClienteCaja['strEstado'];
                        $booleanFlagAfectado  = $intIdServicio > 0;
                        //Si el caso es de Megadatos y no tiene un servicio afectado se busca el idServicio.
                        if($arrayData['data']['prefijoEmpresa'] == 'MD' && !$booleanFlagAfectado)
                        {
                            //validar la cantidad de afectados que tiene el caso
                            $arrayAfectados = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                                        ->getRegistrosAfectadosTotalXCaso($idCaso,'','Data',$start,$limit);

                            if(count($arrayAfectados)>0)
                            {
                                $booleanFlagAfectado = true;
                            }
                            else
                            {
                                $booleanFlagAfectado = false;
                            }

                            //si tiene varios afectado, la bandera se pone false
                            for($i=0;$i<count($arrayAfectados);$i++)
                            {
                                $intIdAfectado    = $arrayAfectados[0]['afectadoId'];
                                $intTmpIdAfectado = $arrayAfectados[$i]['afectadoId'];

                                if($intIdAfectado != $intTmpIdAfectado)
                                {
                                    $booleanFlagAfectado = false;
                                }
                            }
                            //Consulto el idServicio afectado en MD
                            $arrayPeticiones    = array('idCaso'     => $casoMobil['idCaso'],
                                                        'codEmpresa' => $arrayData['data']['codEmpresa'],
                                                        'start'      => $start,
                                                        'limit'      => $limit
                                                        );
                            $arrayIdServicioMD  = $this->getIdServicioXCasoMD($arrayPeticiones);
                            if(!empty($arrayIdServicioMD))
                            {
                                $intIdServicio      = $arrayIdServicioMD['idServicio'];
                                $strEstadoServicio  = $arrayIdServicioMD['strEstado'];
                            }
                            //Obtengo la información de la caja en MD
                            if(!empty($intIdServicio))
                            {
                                $arrayParametrosServicio['intServicioId'] = $intIdServicio;
                                $arrayParametrosServicio['strTipoMedio']  = 'FO';
                                $arrayElemento                            = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                              ->getInfoElementoPorServicioTecnico($arrayParametrosServicio);
                                $arrayBackbone = array();
                                foreach($arrayElemento as $parametroElemento)
                                {
                                    $objElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                          ->findOneBy(array("elementoId" => $parametroElemento->getId()));

                                    $objUbicacion = null;
                                    if(is_object($objElementoUbica->getUbicacionId()))
                                    {
                                        $objUbicacion     = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                                                              ->find($objElementoUbica->getUbicacionId()->getId());
                                    }
                                    if(is_object($objUbicacion)) 
                                    {
                                        $arrayBackbone[]  = array(
                                                               'descripcion' => $parametroElemento->getNombreElemento(),
                                                               'latCaja'     => $objUbicacion->getLatitudUbicacion(),
                                                               'lonCaja'     => $objUbicacion->getLongitudUbicacion(),
                                                               'idElemento'  => $parametroElemento->getId());
                                    }
                                }
                            }
                        }
                        //si siempre es un solo afectado o el mismo afectado en todos los sintomas, se verifica si tiene acta de entrega por
                        //por cambio de equipo
                        if($booleanFlagAfectado)
                        {
                            //verificar si tiene acta entrega
                            $idActaEntrega = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                            ->getDocumentoPorCaso($idCaso, 'ACTA', 'SOPORTE');
                            if($idActaEntrega)
                            {
                                $actaEntrega = "S";
                            }
                            else
                            {
                                $actaEntrega = "N";
                            }
                            
                            //verificar si tiene encuesta el caso
                            $idEncuesta = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                            ->getDocumentoPorCaso($idCaso, 'ENC', 'SOPORTE');
                            if($idEncuesta)
                            {
                                $encuesta = "S";
                            }
                            else
                            {
                                $encuesta = "N";
                            }
                        }
                        else
                        {
                            $actaEntrega    = "S";
                            $encuesta       = "S";
                        }
                        $arrayAdmiParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne(   'COORDENADA_METROS',
                                                            'SOPORTE',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '');
                        if (isset($arrayAdmiParametroDet['valor1']) && !empty($arrayAdmiParametroDet['valor1']))
                        {
                            $strMetros = $arrayAdmiParametroDet['valor1'];
                        }
                        $arrayCaso[] = array(
                                                'idCaso'            => $casoMobil['idCaso'],
                                                'idServicio'        => $intIdServicio,
                                                'strEstadoServicio' => $strEstadoServicio,
                                                'cliente'           => $arrayCliente,
                                                'infBackbone'       => $arrayBackbone,
                                                'numeroCaso'        => $casoMobil['numeroCaso'],
                                                'tipoCaso'          => $casoMobil['nombreTipoCaso'],
                                                'tituloInicial'     => $casoMobil['tituloInicial'],
                                                'versionInicial'    => $casoMobil['versionInicial'],
                                                'fechaApertura'     => $casoMobil['fechaApertura'],
                                                'estadoCaso'        => $casoMobil['estadoCaso'],
                                                'usuarioCreacion'   => $casoMobil['usuarioCreacion'],
                                                'cerrarCaso'        => $cerrarCaso,
                                                'actaEntrega'       => $actaEntrega,
                                                'encuesta'          => $encuesta,
                                                'metros'            => $strMetros
                                            );
                    }//if($casoMobil['estadoCaso'] != null)
                }//if($idCaso != $casoMobil['idCaso'])
            }//foreach($casos as $casoMobil)
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['casos']     = $arrayCaso;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener los sintomas de un caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 17-01-2017 Se agrega en el arreglo el campo con el número de tarea
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.3 24/08/2017 - Se agrega en el array de tareas el departamentoId,departamentoNombre,
     *                           usuarioAsignadoId, idTareaInicial
     *
     * @param array $arrayData
     * @return array $resultado
     */
    private function getSintomasPorCaso($arrayData)
    {
        $mensaje = "";
        try
        {
            $codEmpresa     = $arrayData['data']['codEmpresa'];
            $idCaso         = $arrayData['data']['idCaso'];
            $user           = $arrayData['user'];
            $emComercial    = $this->getDoctrine()->getManager("telconet");
            $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
            $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
            $arrayDetalleHipotesis  = null;
            
            //obtener los datos y departamento de la persona por empresa
            $datosUsuario   = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaDepartamentoPorUserEmpresa($user, $codEmpresa);

            //buscar detalle hipotesis
            $detallesHipotesis = $emSoporte->getRepository('schemaBundle:InfoCaso')->getDetalleHipotesisPorCaso($idCaso);

            foreach($detallesHipotesis as $detalleHipotesisMobil)
            {
                $idDetalleHipotesis = $detalleHipotesisMobil['idDetalleHipotesis'];
                $sintoma            = $detalleHipotesisMobil['nombreSintoma'];
                $hipotesis          = $detalleHipotesisMobil['nombreHipotesis'];
                $arrayTareas        = null;

                //obtener las tareas por casos
                $tareas = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                    ->getDetallesPorDetalleHipotesisPersona($idDetalleHipotesis, $datosUsuario['ID_PERSONA']);

                foreach($tareas as $tareaMobil)
                {
                    $asignado    = "";
                    $objCanton   = null;
                    $intCantonId = (!empty($tareaMobil['cantonId'])) ? $tareaMobil['cantonId'] : 0;
                    if($datosUsuario['ID_PERSONA'] == $tareaMobil['usuarioAsignadoId'])
                    {
                        $asignado = "S";
                    }
                    else
                    {
                        $asignado = "N";
                    }
                    if($intCantonId != 0)
                    {
                        $objCanton = $emGeneral->getRepository('schemaBundle:AdmiCanton')->findOneById($intCantonId);
                    }
                    if($tareaMobil['estadoTarea'] != null)
                    {
                        $strNombreCanton = $objCanton->getNombreCanton();
                        $arrayTareas[]   = array  (
                                                    'idTarea'            => $tareaMobil['idDetalle'],
                                                    'idTareaInicial'     => $tareaMobil['idTareaInicial'],
                                                    'tareaInicial'       => $tareaMobil['nombreTarea'],
                                                    'fechaInicial'       => $tareaMobil['fechaTarea'],
                                                    'estado'             => $tareaMobil['estadoTarea'],
                                                    'esSolucion'         => $tareaMobil['esSolucion'],
                                                    'asignado'           => $asignado,
                                                    'nombreAsignado'     => $tareaMobil['usuarioAsignadoNombre'],
                                                    'idComunicacion'     => $tareaMobil['idComunicacion'],
                                                    'departamentoId'     => $datosUsuario['ID_DEPARTAMENTO'],
                                                    'departamentoNombre' => $datosUsuario['NOMBRE_DEPARTAMENTO'],
                                                    'usuarioAsignadoId'  => $tareaMobil['usuarioAsignadoId'],
                                                    'personaEmpresaRolId'=> $tareaMobil['personaEmpresaRolId'],
                                                    'ciudad'             => !empty($strNombreCanton) ? $strNombreCanton :
                                                                            "NO EXISTE"
                                                  );
                    }
                }//foreach($tareas as $tareaMobil)
                
                //sirve para ordenar el array
                foreach($arrayTareas as $clave => $fila)
                {
                    $asignados [$clave] = $fila['asignado'];
                }
                array_multisort($asignados, SORT_DESC, $arrayTareas);
                
                $arrayDetalleHipotesis[] = array(
                                                    'idDetalleHipotesis'    => $idDetalleHipotesis,
                                                    'sintoma'               => $sintoma,
                                                    'hipotesis'             => $hipotesis,
                                                    'tareas'                => $arrayTareas
                                                );
            }//foreach($detallesHipotesis as $detalleHipotesisMobil)
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['detallesHipotesis'] = $arrayDetalleHipotesis;
        $resultado['status']            = $this->status['OK'];
        $resultado['mensaje']           = $this->mensaje['OK'];
        return $resultado;
    }

    /**
     * Documentación de función getEmpleados
     *
     * Función que retorna la lista de empleados
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 - 23-10-2018
     *
     * @param array $arrayData
     * @return array
     */
    public function getEmpleados($arrayData)
    {
        $serviceUtil    = $this->get('schema.Util');
        $arrayResultado = [];
        $strUser        = $arrayData['user'];
        $strIpCreacion  = "127.0.0.1";
        $arrayResultado['status']  = $this->status['OK'];
        $arrayResultado['mensaje'] = $this->mensaje['OK'];
        $emComercial               = $this->getDoctrine()->getManager("telconet");

        try
        {
            $strParametros["strTipoRol"] = 'Empleado';
            $strParametros["strEstado"]  = 'Activo';
            $strParametros["strLogin"]   = $arrayData['data']['login'];
            $strArrayEmpleados = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getEmpleadosWebService($strParametros);

            foreach($strArrayEmpleados as $arrayIdxEmpleado)
            {
                $arrayEmpleados[] = array('nombres'      => $arrayIdxEmpleado["nombres"],
                                          'apellidos'    => $arrayIdxEmpleado["apellidos"],
                                          'departamento' => $arrayIdxEmpleado["departamento"],
                                          'perfil'       => $arrayIdxEmpleado["perfil"],
                                          'empresa'      => $arrayIdxEmpleado["empresa"],
                                          'ciudad'       => $arrayIdxEmpleado["ciudad"],
                                          'area'         => $arrayIdxEmpleado["area"]);
            }

            $arrayResultado['data'] = $arrayEmpleados;
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+',
                                      'SoporteWSController.getEmpleados',
                                      $ex->getMessage(),
                                      $strUser,
                                      $strIpCreacion);

            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $this->mensaje['ERROR'];
            $arrayResultado['data']    = '';
        }

        return $arrayResultado;
    }


    /**
     * Calcular distancia y tiempo con el api de google.
     * http://web-notes.wirehopper.com/2011/07/13/google-maps-api-distance-calculator
     *
     * @param type $strURL
     * @param type $strQueryString
     * @return type
     */
    public function curl_request($strURL,$strQueryString=null)
    {
        $objCurl        = curl_init();
        curl_setopt($objCurl,CURLOPT_URL,$strURL.'?'.$strQueryString);
        curl_setopt($objCurl,CURLOPT_RETURNTRANSFER, true);
        $objResponse    = trim(curl_exec($objCurl));
        curl_close($objCurl);
        return $objResponse;
    }

    /**
     * Método que retorna las tareas perteneciente a un caso, te devuelve el responsable de la tarea, 
     * la información de la cuadrilla si fuere el caso, entre otra información que disponga la tarea.
     *
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 04-05-2018
     *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getTareasDeCaso($arrayData)
    {
        $strMensaje            = "";
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        try
        {
            //buscar detalles del caso
            $arrayTareaCaso   = array('intIdCaso'     => $arrayData['data']['idCaso']);
            $arrayDetalles    = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                          ->getTareasCaso($arrayTareaCaso);

            $objPunto         = $emComercial->getRepository('schemaBundle:InfoPunto')
                                            ->findOneBy(array('login'    => $arrayData['data']['login'],
                                                              'estado'   => 'Activo'));
            $intIndiceDetalle = 0;
            // Calcular la distancia y tiempo entre 2 puntos datos
            foreach ($arrayDetalles as $arrayAsignadaTareas)
            {
                $arrayDetalles[$intIndiceDetalle]['latitud']           = $objPunto->getLatitud();
                $arrayDetalles[$intIndiceDetalle]['longitud']          = $objPunto->getLongitud();
                $arrayParametroCuadrilla = array('intIdPersonaRol'     => $arrayAsignadaTareas['usuarioAsignadoId'],
                                                 'strEstado'           => 'Activo',
                                                 'strDescripcion'      => 'RESPONSABLE');
                $arrayInfoCuadrilla = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->getInfoCuadrilla($arrayParametroCuadrilla);
                $arrayDetalles[$intIndiceDetalle]['latitudCuadrilla']  = $arrayInfoCuadrilla[0]['latitud'];
                $arrayDetalles[$intIndiceDetalle]['longitudCuadrilla'] = $arrayInfoCuadrilla[0]['longitud'];
                if(isset($arrayAsignadaTareas['latitud']) && isset($arrayAsignadaTareas['latitudCuadrilla']))
                {
                    $strDistanciaPunto      = $arrayAsignadaTareas['latitud'].','.$arrayAsignadaTareas['longitud'];
                    $strDistanciaCuadrilla  = $arrayAsignadaTareas['latitudCuadrilla'].','.$arrayAsignadaTareas['longitudCuadrilla'];

                    $objJsonApiGoogle       = $this->curl_request('https://maps.googleapis.com/maps/api/distancematrix/json',
                                            'units=metric&origins='.$strDistanciaPunto.'&destinations='.$strDistanciaCuadrilla.'&key=AIzaSyBAAW07MNlTmiFiykDRQxCeeONPewmOJ2U');

                    $arrayApiGoogle                                = json_decode($objJsonApiGoogle,true);
                    $arrayDetalles[$intIndiceDetalle]['distancia'] = $arrayApiGoogle['rows'][0]['elements'][0]['distance']['text'];
                    $arrayDetalles[$intIndiceDetalle]['duracion']  = $arrayApiGoogle['rows'][0]['elements'][0]['duration']['text'];
                }
                $intIndiceDetalle++;
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }

            return $arrayResultado;
        }

        $arrayResultado['tareas']    = $arrayDetalles;
        $arrayResultado['status']    = $this->status['OK'];
        $arrayResultado['mensaje']   = $this->mensaje['OK'];
        return $arrayResultado;
    }

    /**
     * Funcion que sirve para obtener las tareas asignadas
     * a un caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getTareasPorCaso($arrayData)
    {
        $mensaje = "";
        try
        {
            $codEmpresa     = $arrayData['data']['codEmpresa'];
            $idCaso         = $arrayData['data']['idCaso'];
            $user           = $arrayData['user'];
            $emComercial    = $this->getDoctrine()->getManager("telconet");
            $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
            $arrayTareas    = null;
            
            //obtener los datos y departamento de la persona por empresa
            $datosUsuario   = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaDepartamentoPorUserEmpresa($user, $codEmpresa);
            
            //buscar detalles del caso
            $detalles = $emSoporte->getRepository('schemaBundle:InfoDetalle')->getTareasPorCaso($idCaso, $datosUsuario['ID_PERSONA']);
            
            foreach($detalles as $detalleMobil)
            {
                $arrayTareas[] = array  (
                                            'idTarea'      => $detalleMobil['idDetalle'],
                                            'tareaInicial' => $detalleMobil['nombreTarea'],
                                            'fechaInicial' => $detalleMobil['fechaTarea'],
                                            'estado'       => $detalleMobil['estadoTarea'],
                                            'esSolucion'   => $detalleMobil['esSolucion']
                                        );
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['tareas']    = $arrayTareas;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * 
     * Funcion que sirve para obtener el historial de un caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getHistorialCaso($arrayData)
    {
        $mensaje = "";
        try
        {
            $idCaso         = $arrayData['data']['idCaso'];
            $start          = $this->get('request')->query->get('start');
            $limit          = $this->get('request')->query->get('limit');
            $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
            
            $arrHistorial = $emSoporte->getRepository('schemaBundle:InfoCasoHistorial')
                                        ->getHistorialCaso($idCaso,$start,$limit,'DESC');
            
            //si no existen registros, se envia mensaje null (SIN CONTENIDO)
            if(count($arrHistorial['registros'])==0)
            {
                $resultado['status']  = $this->status['NULL'];
                $resultado['mensaje'] = $this->mensaje['NULL'];
                
                return $resultado;
            }
            
            $historiales = $arrHistorial['registros'];
            
            foreach($historiales as $entity)
            {
                $usrCreacion    = $entity->getUsrCreacion();
                $feCreacion     = $entity->getFeCreacion();
                $fechaCreacion  = strval(date_format($feCreacion, "d/m/Y G:i"));
                $ipCreacion     = $entity->getIpCreacion();
                $estado         = $entity->getEstado();
                $observacion    = $entity->getObservacion();
                
                
                $arrEncontrados[] = array(  'usrCreacion'   => $usrCreacion,
                                            'feCreacion'    => $fechaCreacion,
                                            'ipCreacion'    => $ipCreacion,
                                            'estado'        => $estado,
                                            'observacion'   => $observacion
                                          );
            }//foreach($historiales as $entity)
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['historiales']   = $arrEncontrados;
        $resultado['status']        = $this->status['OK'];
        $resultado['mensaje']       = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener el historial de una tarea
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 16-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getHistorialTarea($arrayData)
    {
        $resultado  = array();
        $mensaje    = "No se pudo obtener el historial de la Tarea";
        
        try
        {
            $idDetalle  = $arrayData['data']['idDetalle'];
            $start      = $this->get('request')->query->get('start');
            $limit      = $this->get('request')->query->get('limit');
            $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");
            $emGeneral  = $this->getDoctrine()->getManager("telconet_general");
                        
            $arrHistorial = $emSoporte->getRepository('schemaBundle:InfoDetalleHistorial')
                                        ->getHistorialDetalle($idDetalle,$start,$limit,'DESC');
            
            if(count($arrHistorial['registros'])==0)
            {
                $resultado['status']  = $this->status['NULL'];
                $resultado['mensaje'] = $this->mensaje['NULL'];
                
                return $resultado;
            }
            
            $historiales = $arrHistorial['registros'];
            
            foreach($historiales as $entity)
            {
                $usrCreacion    = $entity->getUsrCreacion();
                $feCreacion     = $entity->getFeCreacion();
                $fechaCreacion  = strval(date_format($feCreacion, "d/m/Y G:i"));
                $ipCreacion     = $entity->getIpCreacion();
                $estado         = $entity->getEstado();
                $motivoId       = $entity->getMotivo();
                $observacion    = $entity->getObservacion();
                
                if($motivoId!=null)
                {
                    $motivo         = $emGeneral->find('schemaBundle:AdmiMotivo', $motivoId);
                    $nombreMotivo   = $motivo->getNombreMotivo();
                }
                else
                {
                    $nombreMotivo = "NA";
                }
                
                $arrEncontrados[] = array(  'usrCreacion'   => $usrCreacion,
                                            'feCreacion'    => $fechaCreacion,
                                            'ipCreacion'    => $ipCreacion,
                                            'estado'        => $estado,
                                            'nombreMotivo'  => $nombreMotivo,
                                            'observacion'   => $observacion
                                          );
            }//foreach($historiales as $entity)
        }
        catch(Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['historiales'] = $arrEncontrados;
        $resultado['status']      = $this->status['OK'];
        $resultado['mensaje']     = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener el seguimiento de una tarea
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 20-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 19-11-2016 Se modifica el formato de la fecha para obtener de forma correcta las fechas de los seguimientos
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.3 15-11-2019 -  Se agrega el número de la tarea para obtener los seguimientos de la tarea en el proceso SYSCLOUD
     *                            Y se arregla el campo departamento para que retorne el nombre                       
     * @since 1.2
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getSeguimientoTarea($arrayData)
    {
        $mensaje = "No se pudo obtener el seguimiento de la Tarea";
        
        try
        {
            $intIdDetalle      = $arrayData['data']['idDetalle'];
            $intIdComunicacion = $arrayData['data']['numeroTarea'];
            $emComunicacion    = $this->getDoctrine()->getManager("telconet_comunicacion");
            $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");
            $emComercial= $this->getDoctrine()->getManager("telconet");
            $objServiceUtil = $this->get('schema.Util');
            $strIpCliente   = $this->container->get('request')->getClientIp();

            if(isset($intIdComunicacion) && !empty($intIdComunicacion) && is_numeric($intIdComunicacion))
            {
                $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                      ->find($intIdComunicacion);

                if(is_object($objInfoComunicacion))
                {
                    $intIdDetalle = $objInfoComunicacion->getDetalleId();
                }
            }

            if (!is_numeric($intIdDetalle))
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
                $objServiceUtil->insertError('Telcos+',
                                      'SoporteWSController.getSeguimientoTarea',
                                      $intIdDetalle.' '.$intIdComunicacion,
                                      ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      ($arrayData['ip'] ? $arrayData['ip'] : $strIpCliente));
                return  $resultado;                                     
            }

            //obtener los seguimientos de un detalle
            $arraySeguimientos = $emSoporte->getRepository('schemaBundle:InfoTareaSeguimiento')
                                           ->findBy(array("detalleId" => $intIdDetalle), 
                                                    array("id"        => "DESC")
                                                   );
            
            foreach($arraySeguimientos as $seguimiento)
            {
                $idSeguimiento  = $seguimiento->getId();
                $observacion    = $seguimiento->getObservacion();
                $usrCreacion    = $seguimiento->getUsrCreacion();
                $feCreacion     = $seguimiento->getFeCreacion();

                $intEmpresaId   = $seguimiento->getEmpresaCod();
                $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                              ->findOneByLogin($usrCreacion);

                if($objInfoPersona)
                {
                    $arrayDepartamentoId = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->getDepartamentoPersonaLogueada($objInfoPersona->getId(),
                                                                                        $intEmpresaId);
                }

                if($arrayDepartamentoId[0]['departamento'])
                {
                    $objDepartamento = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                 ->find($arrayDepartamentoId[0]['departamento']);
                    if($objDepartamento)
                    {
                        $strNombreDepartamento = $objDepartamento->getNombreDepartamento();
                    }
                }
                else
                {
                    $strNombreDepartamento = 'Empresa';
                }
                                                  
                $seguimientos[] = array(
                                        'idSeguimiento' => $idSeguimiento,
                                        'observacion'   => $observacion,
                                        'usrCreacion'   => $usrCreacion,
                                        'departamento'  => $strNombreDepartamento,
                                        'feCreacion'    => date_format($feCreacion, 'd-m-Y G:i')
                                       );
            }
        }
        catch(Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['seguimientos']  = $seguimientos;
        $resultado['status']        = $this->status['OK'];
        $resultado['mensaje']       = $this->mensaje['OK'];
        
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener Informacion de Contacto del cliente desde seguimientos
     * 
     * @author Diego Guaman<deguaman@telconet.ec>
     * @version 1.0 31-03-2023
     * 
     * 
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getDatosContactoSeguimientoTarea($arrayData)
    {
        $strMensaje = "No se pudo obtener el seguimiento de la Tarea";
        
        try
        {
            $intIdDetalle           = $arrayData['data']['idDetalle'];
            $emSoporte              = $this->getDoctrine()->getManager("telconet_soporte");
            $objServiceUtil         = $this->get('schema.Util');
            $strIpCliente           = $this->container->get('request')->getClientIp();
            $emGeneral              = $this->getDoctrine()->getManager("telconet_general");  
            $arrayRegistrosCliente  = array();

            if (!isset($intIdDetalle) || (isset($intIdDetalle) && (empty($intIdDetalle) || !is_numeric($intIdDetalle))))
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
                $objServiceUtil->insertError('Telcos+',
                                      'SoporteWSController.getDatosContactoSeguimientoTarea',
                                      $intIdDetalle,
                                      ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      ($arrayData['ip'] ? $arrayData['ip'] : $strIpCliente));
                return  $arrayResultado;                                     
            }

            $arrayParametrosSeguimientos                                = array();
            $arrayParametrosSeguimientos['intDetalleId']                = $intIdDetalle;
            $arrayParametrosSeguimientos['strTituloDatosContacto']      = "<b>Datos Registros Contactos";


            $arraySeguimientos = $emSoporte->getRepository('schemaBundle:InfoTareaSeguimiento')
            ->getRegistrosDatosClienteDesdeSeguimientos($arrayParametrosSeguimientos);                               
            

            
            foreach($arraySeguimientos as $objSeguimiento)
            {
                $intIdSeguimiento  = $objSeguimiento->getId();
                $strObservacion    = $objSeguimiento->getObservacion();
                $strUsrCreacion    = $objSeguimiento->getUsrCreacion();
                $strFeCreacion     = $objSeguimiento->getFeCreacion();

                $arrayRegistroEncontrado = explode("\n",$strObservacion);
                
                if(!stripos($arrayRegistroEncontrado[0], "Sin Registro"))
                {
                    $arrayRegistrosCliente[] = array(
                        'idSeguimiento' => $intIdSeguimiento,
                        'usrCreacion'   => $strUsrCreacion,
                        'nombre'        => $objServiceUtil->obtenerTextoEntreCaracteres(
                            array(
                                "arrayDatos"        => $arrayRegistroEncontrado,
                                "strValorBuscar"    => "Nombre y Apellido: ",
                                "strCaracterInicio" => "</b>",
                                "strCaracterFin"    => "<br>"
                            )
                        ),
                        'celular'       => $objServiceUtil->obtenerTextoEntreCaracteres(
                            array(
                                "arrayDatos"        => $arrayRegistroEncontrado,
                                "strValorBuscar"    => "Celular: ",
                                "strCaracterInicio" => "</b>",
                                "strCaracterFin"    => "<br>"
                            )
                        ),
                        'cargo'         => $objServiceUtil->obtenerTextoEntreCaracteres(
                            array(
                                "arrayDatos"        => $arrayRegistroEncontrado,
                                "strValorBuscar"    => "Cargo/Área: ",
                                "strCaracterInicio" => "</b>",
                                "strCaracterFin"    => "<br>"
                            )
                        ),
                        'correo'        => $objServiceUtil->obtenerTextoEntreCaracteres(
                            array(
                                "arrayDatos"        => $arrayRegistroEncontrado,
                                "strValorBuscar"    => "Correo: ",
                                "strCaracterInicio" => "</b>",
                                "strCaracterFin"    => "<br>"
                            )
                        ),
                        'convencional'  => $objServiceUtil->obtenerTextoEntreCaracteres(
                            array(
                                "arrayDatos"        => $arrayRegistroEncontrado,
                                "strValorBuscar"    => "Convencional: ",
                                "strCaracterInicio" => "</b>",
                                "strCaracterFin"    => "<br>"
                            )
                        ),
                        'observacion'   => '',
                        'tieneRegistos' => true,
                        'tieneRegistros' => true,
                        'feCreacion'    => date_format($strFeCreacion, 'd-m-Y G:i')
                       );
                }else
                {
                    $arrayRegistrosCliente[] = array(
                        'idSeguimiento' => $intIdSeguimiento,
                        'usrCreacion'   => $strUsrCreacion,
                        'nombre'        => '',
                        'celular'       => '',
                        'cargo'         => '',
                        'correo'        => '',
                        'convencional'  => '',
                        'observacion'   => $objServiceUtil->obtenerTextoEntreCaracteres(
                            array(
                                "arrayDatos"        => $arrayRegistroEncontrado,
                                "strValorBuscar"    => "Observación: ",
                                "strCaracterInicio" => "</b>",
                                "strCaracterFin"    => "<br>"
                            )
                        ),
                        'tieneRegistos' => false,
                        'tieneRegistros' => false,
                        'feCreacion'    => date_format($strFeCreacion, 'd-m-Y G:i')
                       );
                }
            }


            $arrayLineasTelefonia = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get(
                                                                                                    'PARAMETROS_LINEAS_TELEFONIA',
                                                                                                    null,
                                                                                                    'FLUJO ACTIVACION',
                                                                                                    'PREFIJOS_PROVINCIA',
                                                                                                    null,
                                                                                                    null,
                                                                                                    null,
                                                                                                    null,
                                                                                                    null,
                                                                                                    null
                                                                                                );
            $arrayPrefijosNumerosFijos = [];                                                                                        
            if (!empty($arrayLineasTelefonia))
            {
                foreach ($arrayLineasTelefonia as $objData)
                {
                    $strCodigoArea = '0' . $objData['valor2'];
                    $boolExisteCodigo = false;
                    foreach ($arrayPrefijosNumerosFijos as $val)
                    {
                        if ($val === $strCodigoArea)
                        {
                            $boolExisteCodigo = true;
                        }
                    }
                    if (!$boolExisteCodigo)
                    {
                        $arrayPrefijosNumerosFijos[] = $strCodigoArea;
                    }
                }
                sort($arrayPrefijosNumerosFijos);
            }
        }
        catch(Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $arrayResultado;
        }
        
        $arrayResultado['contactoCliente']   = $arrayRegistrosCliente;
        $arrayResultado['prefijos']          = $arrayPrefijosNumerosFijos;
        $arrayResultado['status']            = $this->status['OK'];
        $arrayResultado['mensaje']           = $this->mensaje['OK'];
        $arrayResultado['count']             = count($arrayRegistrosCliente);

        return $arrayResultado;
    }

    /**
     * Función que sirve para obtener las tareas
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.4 04-09-2018 - Envío del parámetro que filtra los motivos de fin de tarea que se desea visualizar en el móvil operaciones.
     * @since 1.3
     *      
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.3 23-08-2018 - Se agrega un nuevo campo para el control de fibra al obtener los motivos de la tarea.
     * @since 1.2
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 06-01-2016 Se realizan ajustes en el envio de parametros al llamar al metodo getRegistros de la
     *                         clase AdmiTareaRepository
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 20-07-2015
     * 
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.4 23/04/2019
     * @since 1.3 
     * 
     * Se agrega campos "strRequiereMaterial", "strRequiereRutaFibra" para nueva validación
     * de registro de materiales en TM-OPERACIONES, se borra código para agregar nueva lógica
     * para no agregar mas campos en la ADMI_TAREA y por que no se pudo combinar lo que ya 
     * estaba con lo que se quería.
     * 
     * @return array $tareas
     */
    private function getCatalogoTareas()
    {
        $serviceUtil    = $this->get('schema.Util');

        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $arrayDatos         = array();
        $arrayDatos         = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')->getTareaFinalizaMovil();
        $arrayTareas        = $arrayDatos["registros"];
        $arrayTareasMovil = array();

        if($arrayDatos["status"] == "ok")
        {

            foreach($arrayTareas as $tarea)
            {
                $intTarea               = $tarea['idTarea'];
                $strNombreTarea         = $tarea['nombreTarea'];
                $strRequiereFibra       = $tarea['requiereFibra'];
                $strRequiereMaterial    = ($tarea['requiereMaterial']  ? $tarea['requiereMaterial']  : "N");
                $strRequiereRutaFibra   = ($tarea['requiereRutaFibra']  ? $tarea['requiereRutaFibra']  : "N");
    
                $arrayTareasMovil[] = array(
                                    'k'                 => $intTarea,
                                    'v'                 => $strNombreTarea,
                                    'requiereFibra'     => $strRequiereFibra,
                                    'requiereMaterial'  => $strRequiereMaterial,
                                    'requiereRutaFibra' => $strRequiereRutaFibra
                                 );
            }

        }
        else
        {
            $serviceUtil->insertError('Telcos+',
            'InfoTareaCaracteristicaRepository.getTareaFinalizaMovil',
            $arrayDatos["mensaje"],
            "",
            "127.0.0.1");
        }
        
        return $arrayTareasMovil;
    }
    
    /**
     * Funcion que sirve para obtener los datos necesarios para la
     * finalizacion de la tarea, fecha y hora servidor, tiempo total transcurrido, elemento afectado
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 21-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getDatosFinalizarTarea($arrayData)
    {
        $mensaje = "No se pudieron obtener los datos!";
        try
        {
            $idDetalle          = $arrayData['data']['idDetalle'];
            $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
            $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
            
            $detalle        = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($idDetalle);
            $fechaInicio    = $detalle->getFeCreacion()->format('d-m-Y');
            $horaInicio     = $detalle->getFeCreacion()->format('H:i');
            
            $arrayParametros = array(
                                        'fechaInicio'   => $fechaInicio,
                                        'horaInicio'    => $horaInicio
                                    );
            
            //obtener fecha y hora del servidor, tiempo transcurrido
            $getHoraServidor  = $this->get('soporte.SoporteService');
            $tiempoHoraServer = $getHoraServidor->obtenerHoraTiempoTranscurrido($arrayParametros);
            
            //elemento afectado
            $detalleTareaElemento = $emSoporte->getRepository('schemaBundle:InfoDetalleTareaElemento')->findOneByDetalleId($detalle->getId());
            if($detalleTareaElemento && count($detalleTareaElemento)>0)
            {

                $elemento       = $emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($detalleTareaElemento->getElementoId());
                $nombreElemento = ($elemento ? ($elemento->getNombreElemento() ? $elemento->getNombreElemento() : "") : "");
            }
            
            $arrayResultado = array(
                                    'idTarea'                   => $detalle->getId(),
                                    'tareaInicial'              => $detalle->getTareaId()->getNombreTarea(),
                                    'fechaInicial'              => $tiempoHoraServer['fechaInicio'],
                                    'horaInicial'               => $tiempoHoraServer['horaInicio'],
                                    'fechaCierre'               => $tiempoHoraServer['fechaFin'],
                                    'horaCierre'                => $tiempoHoraServer['horaFin'],
                                    'tiempoTotalTarea'          => $tiempoHoraServer['tiempoTotal'],
                                    'nombreElementoAfectado'    => $nombreElemento
                                    );
        }
        catch(Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['tarea']     = $arrayResultado;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener las hipotesis
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 06-01-2016 Se realizan ajustes en el envio de parametros al llamar al metodo getRegistros de la
     *                         clase AdmiHipotesisRepository
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 22-07-2015
     * @param array $arrayData
     * @return array $resultado
     */
    private function getCatalagoHipotesis($arrayParametros)
    {
        $codEmpresa = $arrayParametros['codEmpresa'];
        $start      = $arrayParametros['start'];
        $limit      = $arrayParametros['limit'];
        
        $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");
        $parametros  = array();

        //Se arma un array de parametros para enviarlos al Repositorio
        $parametros["nombre"]     = "";
        $parametros["estado"]     = "Activo";
        $parametros["codEmpresa"] = $codEmpresa;

        $datos = $emSoporte->getRepository('schemaBundle:AdmiHipotesis')
                           ->getRegistros($parametros,$start,$limit);

        $arrayHipotesis = $datos["registros"];
        if(count($arrayHipotesis)<1)
        {
            throw new \Exception("NULL");
        }
        
        foreach($arrayHipotesis as $hipotesis)
        {
            $idHipotesis        = $hipotesis->getId();
            $nombreHipotesis    = $hipotesis->getNombreHipotesis();

            $listaHipotesis[] = array(
                                        'k' => $idHipotesis,
                                        'v' => $nombreHipotesis
                                     );
        }
        
        return $listaHipotesis;
    }
    
    /**
     * Funcion que sirve para cargar los catalogos para soporte mobil (tareas, hipotesis)
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 22-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.2 23-08-2018 - Se devuelve un nuevo campo para el control de fibra al obtener los motivos de la tarea.
     * @since 1.1
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getCatalogoSoporte($arrayData)
    {
        try
        {
            $codEmpresa             = $arrayData['data']['codEmpresa'];
            $start                  = $this->get('request')->query->get('start');
            $limit                  = $this->get('request')->query->get('limit');
            
             //CREA PARCHE POR ENVIO ERRONEO DEL MOVIL - REGULARIZAR AQUI Y EN EL MOVIL
            $strvisuaMovil          = $arrayData['data']['visualizarMovil'];
            $strVisuaMovilParche    = $arrayData['visualizarMovil'];
            
            $boolBanderaVisualizarMovilParche = false;
            $boolBanderaVisualizarMovil       = false;
            
            if(isset($strvisuaMovil) && !empty($strvisuaMovil))
            {
                $boolBanderaVisualizarMovil=true;
            }
            
            if(isset($strVisuaMovilParche) && !empty($strVisuaMovilParche))
            {
                $boolBanderaVisualizarMovilParche=true;
            }
            
            if($boolBanderaVisualizarMovil)
            {
                $strvisuaMovil=$arrayData['data']['visualizarMovil'];
            }
            
            if($boolBanderaVisualizarMovilParche)
            {
                $strvisuaMovil=$arrayData['visualizarMovil'];
            }
            
            //FIN DEL PARCHE
            
            $arrayParametros = array(
                                        'codEmpresa'    => $codEmpresa,
                                        'start'         => $start,
                                        'limit'         => $limit,
                                        'visualizaMovil'=> $strvisuaMovil
                                    );
            
            //obtener listado de tareas
            $arrayTareas      = $this->getCatalogoTareas();            
            
            //obtener listado de hipotesis
            $arrayHipotesis   = $this->getCatalagoHipotesis($arrayParametros);
            
            $arrayCatalogo = array(
                                    'tareas'     => $arrayTareas,
                                    'hipotesis' => $arrayHipotesis
                                );
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']   = $this->status['NULL'];
                $resultado['mensaje']  = $this->mensaje['NULL'];
            }
            else
            {
                $resultado['status']   = $this->status['ERROR'];
                $resultado['mensaje']  = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['catalogos'] = $arrayCatalogo;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener los datos necesarios
     * para mostrar en la pantalla para cerrar el caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 22-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getDatosCerrarCaso($arrayData)
    {
        $mensaje = "No se puede obtener datos del Caso";
        try
        {
            $idCaso     = $arrayData['data']['idCaso'];
            $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");
            
            $caso           = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($idCaso);
            $fechaInicio    = $caso->getFeCreacion()->format('d-m-Y');
            $horaInicio     = $caso->getFeCreacion()->format('h:m');
            
            $arrayParametros = array(
                                        'fechaInicio'   => $fechaInicio,
                                        'horaInicio'    => $horaInicio
                                    );
            
            //obtener fecha y hora del servidor, tiempo transcurrido
            $getHoraServidor  = $this->get('soporte.SoporteService');
            $tiempoHoraServer = $getHoraServidor->obtenerHoraTiempoTranscurrido($arrayParametros);
            
            $detalleHipotesis = $emSoporte->getRepository('schemaBundle:InfoDetalleHipotesis')->findOneBy(array('casoId' => $idCaso));
            $hipotesisInicial = $detalleHipotesis->getHipotesisId()->getNombreHipotesis();
            
            $arrayResultado = array(
                                    'idCaso'            => $caso->getId(),
                                    'numeroCaso'        => $caso->getNumeroCaso(),
                                    'fechaApertura'     => $fechaInicio,
                                    'horaApertura'      => $horaInicio,
                                    'fechaCierre'       => $tiempoHoraServer['fechaFin'],
                                    'horaCierre'        => $tiempoHoraServer['horaFin'],
                                    'tiempoTotalCaso'   => $tiempoHoraServer['tiempoTotal'],
                                    'hipotesisInicial'  => $hipotesisInicial
                                   );
        }
        catch(Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['caso']     = $arrayResultado;
        $resultado['status']   = $this->status['OK'];
        $resultado['mensaje']  = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener el acta de entrega para un caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-05-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 13/06/2017 - Generar Actas de Entregas para TN y MD solo si existe un idServicio, se modifica la validación
     *                           para obtener el nombre o la razon social, se envia el modulo para obtener la acta.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.3 23/06/2017 - Se valida que exista un idServicio para poder generar una acta de entrega.
     *
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.4 19-12-2018 - Se adiciona los equipos ExtenderDualBand al acta.
     * @since 1.3
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getActaEntregaSoporte($arrayData)
    {
        $mensaje    = "";
        $start      = $this->get('request')->query->get('start');
        $limit      = 5;
        
        try
        {
            $idServicio         = $arrayData['data']['idServicio'];
            $codEmpresa         = $arrayData['data']['codEmpresa'];
            $prefijoEmpresa     = $arrayData['data']['prefijoEmpresa'];
            
            $arrParametros      = array (
                                            'idServicio'    => $idServicio,
                                            'idEmpresa'     => $codEmpresa,
                                            'prefijoEmpresa'=> $prefijoEmpresa,
                                            'strModulo'     => 'SOPORTE',
                                            'start'         => $start,
                                            'limit'         => $limit
                                        );
            if($idServicio == 0)
            {
                $mensaje = "No existe servicio, no se puede generar el Acta de Entrega";
                throw new \Exception('ERROR_PARCIAL');
            }
            $actaEntregaService = $this->get('tecnico.ActaEntrega');
            $arrResultado       = $actaEntregaService->getActaEntrega($arrParametros);
                                    
            //servicio-------------------------------------------------------------------------------------------------------------------
            $servicio           = $arrResultado['servicio'];
            $planCab            = $arrResultado['planCab'];
            $ultimaMilla        = $arrResultado['ultimaMilla'];
            
            if($servicio && $prefijoEmpresa != 'TN')
            {
                $tipoOrden = $servicio->getTipoOrden();
            
                $datosServicio[] = array(   'plan'          => $planCab->getNombrePlan(),
                                            'tipoOrden'     => $tipoOrden,
                                            'ultimaMilla'   => $ultimaMilla->getNombreTipoMedio(),
                                            'comparticion'  => "2:1"
                                        );
            }            
            //----------------------------------------------------------------------------------------------------------------------------
            
            //datos del cliente - punto---------------------------------------------------------------------------------------------------
            $datosCliente = $arrResultado['datosCliente'];
            
            if(!$datosCliente)
            {
                $mensaje = "No existen Datos para el Acta de Entrega";
                throw new \Exception("ERROR_PARCIAL");
            }            
                        
            $identificacionCliente  = $datosCliente['IDENTIFICACION_CLIENTE'];

            if(isset($datosCliente['RAZON_SOCIAL']))
            {
                $nombreCliente = $datosCliente['RAZON_SOCIAL'];
            }
            else
            {
                $nombreCliente = $datosCliente['NOMBRES'];
            }
            
            $punto = array  (
                                'login'     => $datosCliente['LOGIN'],
                                'cliente'   => $nombreCliente,
                                'direccion' => $datosCliente['DIRECCION'],
                                'latitud'   => $datosCliente['LATITUD'],
                                'longitud'  => $datosCliente['LONGITUD'],
                                'servicios' => $datosServicio
                            );
            //----------------------------------------------------------------------------------------------------------------------------
            
            //datos de forma de contacto del punto----------------------------------------------------------------------------------------
            $arrFormaContactosPunto = $arrResultado['formaContactoPunto'];

            if($arrFormaContactosPunto['total'] < 1)
            {
                //datos de forma de contacto del cliente
                $arrFormaContactoCliente = $arrResultado['formaContactoCliente'];
                
                if($arrFormaContactoCliente['total'] > 0)
                {
                    foreach($arrFormaContactoCliente['registros'] as $formaContactoCliente)
                    {
                        $contactos[] = array(
                                                'formaContacto' => $formaContactoCliente['descripcionFormaContacto'],
                                                'valorContacto' => $formaContactoCliente['valor']
                                            );
                    }
                }
            }
            else
            {
                foreach($arrFormaContactosPunto['registros'] as $formaContactoPunto)
                {
                    $contactos[] = array(
                                            'formaContacto' => $formaContactoPunto['descripcionFormaContacto'],
                                            'valorContacto' => $formaContactoPunto['valor']
                                        );
                }
            }
            //----------------------------------------------------------------------------------------------------------------------------
            
            //datos del contacto del cliente----------------------------------------------------------------------------------------------
            $contactoCliente = $arrResultado['contactoCliente'];
            
            if(!$contactoCliente)
            {
                $nombreContacto = "NA";
            }
            else
            {
                $nombreContacto = $contactoCliente['NOMBRE_CONTACTO'];
            }
            //----------------------------------------------------------------------------------------------------------------------------
            if($prefijoEmpresa == 'TN')
            {
                //$elementos[] = $arrResultado['dataTecnica'];
                foreach($arrResultado['dataTecnica'] as $objDataTecnica)
                {
                  $elementos[] = $objDataTecnica;
                }
            }
            else
            {
                //cpe-------------------------------------------------------------------------------------------------------------------------
                $elementoCpe = $arrResultado['elementoCpe'];
                if($elementoCpe)
                {
                    $spcMacCpe  = $arrResultado['macCpe'];
                    if($spcMacCpe)
                    {
                        $macCpe = $spcMacCpe->getValor();
                    }
                    else
                    {
                        $macCpe = "";
                    }

                    $serieCpe   = $elementoCpe->getSerieFisica();
                    $modeloCpe  = $elementoCpe->getModeloElementoId()->getNombreModeloElemento();
                    $marcaCpe   = $elementoCpe->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                    $tipoCpe    = $elementoCpe->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();

                    $cpe        = array(
                                            'modelo'    => $modeloCpe,
                                            'marca'     => $marcaCpe,
                                            'serie'     => $serieCpe,
                                            'mac'       => $macCpe,
                                            'tipo'      => $tipoCpe
                                        );
                    $elementos[]= $cpe;
                }
                //----------------------------------------------------------------------------------------------------------------------------

                //ont-------------------------------------------------------------------------------------------------------------------------
                $elementoOnt = $arrResultado['elementoOnt'];
                if($elementoOnt)
                {
                    $spcMacOnt  = $arrResultado['macOnt'];
                    if($spcMacOnt)
                    {
                        $macOnt = $spcMacOnt->getValor();
                    }
                    else
                    {
                        $macOnt = "";
                    }

                    $serieOnt   = $elementoOnt->getSerieFisica();
                    $modeloOnt  = $elementoOnt->getModeloElementoId()->getNombreModeloElemento();
                    $marcaOnt   = $elementoOnt->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                    $tipoOnt    = $elementoOnt->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();

                    $ont        = array(
                                            'modelo'    => $modeloOnt,
                                            'marca'     => $marcaOnt,
                                            'serie'     => $serieOnt,
                                            'mac'       => $macOnt,
                                            'tipo'      => $tipoOnt
                                        );
                    $elementos[]= $ont;
                }
                //----------------------------------------------------------------------------------------------------------------------------

                //wifi------------------------------------------------------------------------------------------------------------------------
                $elementoWifi = $arrResultado['elementoWifi'];
                if($elementoWifi)
                {
                    $spcMacWifi  = $arrResultado['macWifi'];
                    if($spcMacWifi)
                    {
                        $macWifi = $spcMacWifi->getValor();
                    }
                    else
                    {
                        $macWifi = "";
                    }

                    $serieWifi   = $elementoWifi->getSerieFisica();
                    $modeloWifi  = $elementoWifi->getModeloElementoId()->getNombreModeloElemento();
                    $marcaWifi   = $elementoWifi->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                    $tipoWifi    = $elementoWifi->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();

                    $wifi        = array(
                                            'modelo'    => $modeloWifi,
                                            'marca'     => $marcaWifi,
                                            'serie'     => $serieWifi,
                                            'mac'       => $macWifi,
                                            'tipo'      => $tipoWifi
                                        );
                    $elementos[] = $wifi;
                }
                
                //------------------ Equipos Extenders si existen ---------------
                $arrayElementosExtender = $arrResultado['arrayElementosExtender'];
                if(isset($arrayElementosExtender) && !empty($arrayElementosExtender))
                {
                    foreach($arrayElementosExtender as $objElementosExtender)
                    {
                        $strMacExtender  = $objElementosExtender->getMacElemento();
                        if($strMacExtender)
                        {
                            $strMacExtender = $objElementosExtender->getMacElemento();
                        }
                        else
                        {
                            $strMacExtender = "";
                        }
                        $strSerieExtender   = $objElementosExtender->getSerieFisica();
                        $strModeloExtender  = $objElementosExtender->getModeloElementoId()->getNombreModeloElemento();
                        $strMarcaExtender   = $objElementosExtender->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                        $strTipoExtender    = $objElementosExtender->getModeloElementoId()->getTipoElementoId()->getNombreTipoElemento();
                    
                        $arrayExtender        = array(
                                            'modelo'    => $strModeloExtender,
                                            'marca'     => $strMarcaExtender,
                                            'serie'     => $strSerieExtender,
                                            'mac'       => $strMacExtender,
                                            'tipo'      => $strTipoExtender
                                        );
                        $elementos[] = $arrayExtender;
                   
                    }
                }
                
            }
            //----------------------------------------------------------------------------------------------------------------------------
                 
            //obtener opciones para el acta-----------------------------------------------------------------------------------------------
            $preguntas = $arrResultado['opcionesActaEntrega']['preguntas'];
            //----------------------------------------------------------------------------------------------------------------------------
            
            $resultado = array  (
                                    'documentoIdentidad'    => $identificacionCliente,
                                    'punto'                 => $punto,
                                    'personaContacto'       => $nombreContacto,
                                    'contactos'             => $contactos,
                                    'elementos'             => $elementos,
                                    'preguntas'             => $preguntas,
                                    'observaciones'         => $servicio->getObservacion()
                                );
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
          
            return $resultado;
        }
        
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para obtener el catalogo para la encuesta de 
     * satisfaccion del cliente por soporte (casos)
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 3-08-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 13/06/2017 - Se envia el idServicio para generar la encuesta.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.3 23/09/2021 - Se añade nomenclatura para poder realizar el proyecto de forma Piloto.
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getEncuestaSoporte($arrayData)
    {
        $mensaje = "";
        
        try
        {
            $idServicio     = $arrayData['data']['idServicio'];
            $emComercial    = $this->getDoctrine()->getManager("telconet");
            $servicio       = $emComercial->find('schemaBundle:InfoServicio', $idServicio);
            
            if(!$servicio)
            {
                $mensaje = "No existe servicio, no se puede generar la Encuesta";
                throw new \Exception('ERROR_PARCIAL');
            }
            
            //obtener las preguntas de la encuesta
            $soporte                                 = $this->get('soporte.SoporteService');
            $arrayParametrosPreguntas                = array();
            $arrayParametrosPreguntas['objServicio'] = $servicio;

            //Obtenemos el codigo de la plantilla para poder generar encuesta
            $arrayAdmiParametroDetActa               = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                   ->getOne('CODIGO_ENCUESTA_VISITA_POR_EMPRESA',
                                                                            'SOPORTE',
                                                                            '',
                                                                            '',
                                                                            'CODIGO_ENCUESTA_VISITA',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            $arrayData['data']['codEmpresa']
                                                                           );
            if (isset($arrayAdmiParametroDetActa['valor2']) && !empty($arrayAdmiParametroDetActa['valor2']))
            {
                $arrayParametrosPreguntas['strCodigoPlantilla'] = $arrayAdmiParametroDetActa['valor2'];
            }
            else
            {
                $mensaje = "No existe codigo de plantilla";
                throw new \Exception("ERROR_PARCIAL");
            }
            $arrEncuesta = $soporte->obtenerPreguntasEncuesta($arrayParametrosPreguntas);
            
            $preguntas   = $arrEncuesta['preguntas'];
            
            if(!$preguntas)
            {
                $mensaje = "No existe la encuesta para este servicio!";
                throw new \Exception("ERROR_PARCIAL");
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['preguntas'] = $preguntas;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }

    /**
     * Funcion que sirve para obtener el ultimo estado y fecha de una tarea creada
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 13-02-2016
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 11-05-2018 - Se agregan 2 valores al momento de retornar la informacion: el idDetalle y el estado actual de la tarea
     *
     * @param  array $arrayData
     * @return int   $estado
     */
    private function getEstadoTarea($arrayData)
    {
        $arrayResultado = array();
        $strMensaje     = "No se puede obtener el estado de la Tarea";
        try
        {
            $soporteService  = $this->get('soporte.SoporteService');
            $arrayRespuesta  = $soporteService->obtenerEstadoTarea($arrayData['data']['idTarea']);

        }
        catch(Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }

            return $arrayResultado;
        }

        $arrayResultado['estado']        = $arrayRespuesta['estado'];
        $arrayResultado['fechaApertura'] = $arrayRespuesta['fechaApertura'];
        $arrayResultado['fechaCierre']   = $arrayRespuesta['fechaCierre'];
        $arrayResultado['idDetalle']     = $arrayRespuesta['idDetalle'];
        $arrayResultado['estadoActual']  = $arrayRespuesta['estadoActual'];
        $arrayResultado['status']        = $this->status['OK'];
        $arrayResultado['mensaje']       = $arrayRespuesta['mensaje'];
        return $arrayResultado;
    }

    /**
     * Funcion que trae el listado de tareas de tipo Incidencias.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 20/09/2017
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 25/09/2017 - Se muestra la observación del elemento al cual se le creo una tarea de incidencia.}
     *
     * @param array $arrayData[
     *                          "op"    => string con el nombre de la función que se desea consultar
     *                          "data"  => array con los parámetros necesarios para la obtención de la información
     *                                     de la asignación actual de la tarea.
     *                                     [
     *                                         "idDetalle"  => int del id del detalle de la tarea,
     *                                         "codEmpresa" => string del id de la empresa,
     *                                     ]
     *                          "token"      => token,
     *                          "source"     => {"name","originID","tipoOriginID"},
     *                          "user"       => string del login del usuario en sesión
     *                        ]
     * @return array $resultado
     */
    private function getListadoIncidencias($arrayData)
    {
        $arrayResultado                 = array();
        $arrayParametro                 = array();
        $arrayTareaIncidencia           = array();
        $arrayParametroTareasIncidencia = array();
        $arrayTareaId                   = array();
        $strMensaje                     = "No se puede obtener el listado de la Tarea de Incidencia";
        try
        {
            $emGeneral              = $this->getDoctrine()->getManager("telconet_general");
            $emSoporte              = $this->getDoctrine()->getManager("telconet_soporte");
            $emComercial            = $this->getDoctrine()->getManager("telconet");
            $arrayAdmiParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne(   'TAREA_INCIDENCIA',
                                                            'SOPORTE',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '');
            $arrayDatosUsuario      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->getPersonaDepartamentoPorUserEmpresa($arrayData['user'], $arrayData['data']['codEmpresa']);
            if(count($arrayAdmiParametroDet) > 0)
            {
                $arrayParametroTareasIncidencia['arrayNombreTarea'] = explode(",", $arrayAdmiParametroDet['valor2']);
                $arrayParametroTareasIncidencia['strEstado']        = "Activo";
                $arrayIdTarea                                       = $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                                ->getTareaIncidencia($arrayParametroTareasIncidencia);
                if(count($arrayIdTarea) > 0)
                {
                    foreach($arrayIdTarea as $arrayValorTarea)
                    {
                        $arrayTareaId[] = $arrayValorTarea['tareaId'];
                    }
                    $arrayParametro['arrayTareaId'] = $arrayTareaId;
                }
                $arrayParametro['arrayEstado'] = explode(",", $arrayAdmiParametroDet['valor1']);
            }
            $arrayParamPersonaRol = array("idEmpresa"       => $arrayData['data']['codEmpresa'],
                                          "strLoginPersona" => $arrayData['user'],
                                          "estado"          => 'Activo');
            $arrayPersonaEmpresa  = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                ->getResultadoPersonaEmpresaRolPorCriterios($arrayParamPersonaRol);
            if(isset($arrayPersonaEmpresa['resultado'][0]['idPersonaEmpresaRol'])
                && !empty($arrayPersonaEmpresa['resultado'][0]['idPersonaEmpresaRol']))
            {
                $arrayParametro['intIdPersonaEmpresaRol'] = $arrayPersonaEmpresa['resultado'][0]['idPersonaEmpresaRol'];
                $arrayParametro['intIdPersona']           = $arrayPersonaEmpresa['resultado'][0]['idPersona'];
            }
            $arrayRespTareaIncidencia = $emSoporte->getRepository("schemaBundle:InfoDetalleAsignacion")
                                                  ->getTareaPorIncidencias($arrayParametro);
            foreach($arrayRespTareaIncidencia as $arrayTarea)
            {
                $objCanton              = null;
                $strNombreCanton        = "";
                $intCantonId            = (!empty($arrayTarea['cantonId'])) ? $arrayTarea['cantonId'] : 0;
                $strTipoElementoTarea   = "";
                $strNombreElementoTarea = "";
                $strLatitudTarea        = $arrayTarea["latitud"] ? $arrayTarea["latitud"] : "N/A";
                $strLongitudTarea       = $arrayTarea["longitud"] ? $arrayTarea["longitud"] : "N/A";
                $strUsrCreacionDetalle  = $arrayTarea["usrCreacionDetalle"] ? $arrayTarea["usrCreacionDetalle"] : "N/A";
                $strObservacionDetalle  = $arrayTarea["observacion"] ? $arrayTarea["observacion"] : "N/A";
                // Identificar el elemento al cual se le creo la incidencia.
                $objDetalleTarea        = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($arrayTarea["idTarea"]);
                if(is_object($objDetalleTarea))
                {
                    $objDetalleTareaElemento = $emSoporte->getRepository('schemaBundle:InfoDetalleTareaElemento')
                                                         ->findOneBy(array("detalleId" => $objDetalleTarea));
                    if(is_object($objDetalleTareaElemento))
                    {
                        $intIdElementoTarea = $objDetalleTareaElemento->getElementoId();
                        if($intIdElementoTarea)
                        {
                            $objElementoTarea       = $emSoporte->getRepository('schemaBundle:InfoElemento')->find($intIdElementoTarea);
                            if(is_object($objElementoTarea))
                            {
                                $strNombreElementoTarea = $objElementoTarea->getNombreElemento();
                                $objModeloElementoTarea = $objElementoTarea->getModeloElementoId();
                                if(is_object($objModeloElementoTarea))
                                {
                                    $objTipoElementoTarea = $objModeloElementoTarea->getTipoElementoId();
                                    if(is_object($objTipoElementoTarea))
                                    {
                                        $strTipoElementoTarea = $objTipoElementoTarea->getNombreTipoElemento();
                                    }

                                }
                            }
                        }
                    }
                }
                $strObservacion  = "<b>Tipo de Elemento:</b> ".$strTipoElementoTarea."<br>"
                                   ."<b>Elemento:</b> ".$strNombreElementoTarea."<br>"
                                   ."<b>Latitud:</b> ".$strLatitudTarea."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
                                   ."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
                                   ."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
                                   ."<b style='margin-left:120px; color:red'>Longitud:</b> ".$strLongitudTarea."<br>"
                                   ."<b>Usr. Creación:</b> ".$strUsrCreacionDetalle."<br>"
                                   ."<b>Observación:</b> ".$strObservacionDetalle;

                if($intCantonId != 0)
                {
                    $objCanton = $emGeneral->getRepository('schemaBundle:AdmiCanton')->findOneById($intCantonId);
                }
                if(is_object($objCanton))
                {
                    $strNombreCanton = $objCanton->getNombreCanton();
                }
                $arrayTarea['fechaInicial']             = strval(date_format($arrayTarea["fechaInicial"], "d-m-Y H:i"));
                $arrayTarea['departamentoId']           = $arrayDatosUsuario['ID_DEPARTAMENTO'];
                $arrayTarea['departamentoNombre']       = $arrayDatosUsuario['NOMBRE_DEPARTAMENTO'];
                $arrayTarea['usuarioAsignadoId']        = $arrayParametro['intIdPersona'];
                $arrayTarea['personaEmpresaRolId']      = $arrayParametro['intIdPersonaEmpresaRol'];
                $arrayTarea['ciudad']                   = !empty($strNombreCanton) ? $strNombreCanton : "SIN INFORMACIÓN";
                $arrayTarea['observacionElementoTarea'] = $strObservacion;
                $arrayTareaIncidencia[]                 = $arrayTarea;
            }
            $arrayResultado['tareasIncidencia'] = $arrayTareaIncidencia;
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }

            return $arrayResultado;
        }
        $arrayResultado['status']        = $this->status['OK'];
        
        $arrayResultado['mensaje']       = $this->mensaje['OK'];
        return $arrayResultado;
    }

    /**
     * Funcion que trae el listado de tareas interdepartamentales.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 24/11/2017
     *
     * @author John Vera R <javera@telconet.ec>
     * @version 1.1 12/03/2018 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 25/06/2021 Se valida que la observación de la tarea no venga null, por problemas en tareas interdepartamentales
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.3 28/06/2021 Se establece id de la empresa telconet para la búsqueda de información 
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.4 01/07/2021 Se inicializan datos.
     *   
     * @param array $arrayData[
     *                          "op"    => string con el nombre de la función que se desea consultar
     *                          "data"  => array con los parámetros necesarios para la obtención de la información
     *                                     de la asignación actual de la tarea.
     *                                     [
     *                                         "prefijoEmpresa"  => string del prefijo de la empresa,
     *                                         "codEmpresa" => string del id de la empresa,
     *                                     ]
     *                          "token"      => token,
     *                          "source"     => {"name","originID","tipoOriginID"},
     *                          "user"       => string del login del usuario en sesión
     *                        ]
     * @return array $resultado
     */
    private function getListadoTareasInterdepartamentales($arrayData)
    {
        $emGeneral                      = $this->getDoctrine()->getManager("telconet_general");
        $emSoporte                      = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial                    = $this->getDoctrine()->getManager("telconet");
        $arrayResultado                 = array();
        $arrayParametro                 = array();
        $arrayTareaIncidencia           = array();
        $arrayParametroTareasIncidencia = array();
        $arrayTareaId                   = array();
        $strMensaje                     = "No se puede obtener el listado de las Tareas Interdepartamentales";
        
        try
        {
            $arrayAdmiParametroDetEmpresa   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne(   'PREFIJOS_EMPRESA', 
                                                                '', 
                                                                '', 
                                                                'TELCONET', 
                                                                '', 
                                                                '', 
                                                                '', 
                                                                '', 
                                                                '', 
                                                                '');
            
            if (isset($arrayAdmiParametroDetEmpresa['valor1']) && !empty($arrayAdmiParametroDetEmpresa['valor1']))
            {
                $arrayData['data']['codEmpresa'] = $arrayAdmiParametroDetEmpresa['valor1'];
            }
            
            //envio el parametro del id del detalle
            $arrayParametro['idDetalle'] =  $arrayData['data']['idDetalle'];
            
            $arrayAdmiParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne(   'TAREA_INTERDEPARTAMENTAL',
                                                            'SOPORTE',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '');
            $arrayDatosUsuario      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->getPersonaDepartamentoPorUserEmpresa($arrayData['user'], $arrayData['data']['codEmpresa']);
                                                  
                                                  
            if(count($arrayAdmiParametroDet) > 0)
            {
                $arrayParametroTareasIncidencia['arrayNombreTarea'] = explode(",", $arrayAdmiParametroDet['valor2']);
                $arrayParametroTareasIncidencia['strEstado']        = "Activo";
                $arrayIdTarea                                       = $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                                ->getTareaIncidencia($arrayParametroTareasIncidencia);
                if(count($arrayIdTarea) > 0)
                {
                    foreach($arrayIdTarea as $arrayValorTarea)
                    {
                        $arrayTareaId[] = $arrayValorTarea['tareaId'];
                    }
                    $arrayParametro['arrayTareaId'] = $arrayTareaId;
                }
                $arrayParametro['arrayEstado'] = explode(",", $arrayAdmiParametroDet['valor1']);
            }
            $arrayParamPersonaRol = array("idEmpresa"       => $arrayData['data']['codEmpresa'],
                                          "strLoginPersona" => $arrayData['user'],
                                          "estado"          => 'Activo');
            $arrayPersonaEmpresa  = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                ->getResultadoPersonaEmpresaRolPorCriterios($arrayParamPersonaRol);
            
            if(isset($arrayPersonaEmpresa['resultado'][0]['idPersonaEmpresaRol'])
                && !empty($arrayPersonaEmpresa['resultado'][0]['idPersonaEmpresaRol']))
            {
                $arrayParametro['intIdPersonaEmpresaRol'] = $arrayPersonaEmpresa['resultado'][0]['idPersonaEmpresaRol'];
                $arrayParametro['intIdPersona']           = $arrayPersonaEmpresa['resultado'][0]['idPersona'];
            }
            else
            {
                $arrayParametro['intIdPersonaEmpresaRol'] = $arrayAdmiParametroDet['valor3'];
                $arrayParametro['intIdPersona']           = $arrayAdmiParametroDet['valor4'];
            }

            $arrayRespTareaIncidencia = $emSoporte->getRepository("schemaBundle:InfoDetalleAsignacion")
                                                  ->getTareaInterdepartamentales($arrayParametro);

            foreach($arrayRespTareaIncidencia as $arrayTarea)
            {
                $objCanton              = null;
                $strNombreCanton        = "";
                $intCantonId            = (!empty($arrayTarea['cantonId'])) ? $arrayTarea['cantonId'] : 0;
                
                $arrayTarea["observacion"]  = $arrayTarea["observacion"] ? $arrayTarea["observacion"] : "N/A";
                $strObservacionDetalle      = $arrayTarea["observacion"];

                $strObservacion = $strObservacionDetalle;
                if($intCantonId != 0)
                {
                    $objCanton = $emGeneral->getRepository('schemaBundle:AdmiCanton')->findOneById($intCantonId);
                }
                if(is_object($objCanton))
                {
                    $strNombreCanton = $objCanton->getNombreCanton();
                }
                $arrayTarea['fechaInicial']             = strval(date_format($arrayTarea["fechaInicial"], "d-m-Y H:i"));
                $arrayTarea['departamentoId']           = $arrayDatosUsuario['ID_DEPARTAMENTO'];
                $arrayTarea['departamentoNombre']       = $arrayDatosUsuario['NOMBRE_DEPARTAMENTO'];
                $arrayTarea['usuarioAsignadoId']        = $arrayParametro['intIdPersona'];
                $arrayTarea['personaEmpresaRolId']      = $arrayParametro['intIdPersonaEmpresaRol'];
                $arrayTarea['ciudad']                   = !empty($strNombreCanton) ? $strNombreCanton : "SIN INFORMACIÓN";
                $arrayTarea['observacionElementoTarea'] = $strObservacion;
                $arrayTareaIncidencia[]                 = $arrayTarea;
            }
            if(empty($arrayTareaIncidencia))
            {
                $arrayTarea['fechaInicial']             = strval(date_format(new \DateTime('now'), "d-m-Y H:i"));
                $arrayTarea['departamentoId']           = $arrayDatosUsuario['ID_DEPARTAMENTO'];
                $arrayTarea['departamentoNombre']       =  $arrayDatosUsuario['NOMBRE_DEPARTAMENTO'];
                $arrayTarea['usuarioAsignadoId']        = $arrayParametro['intIdPersona'];
                $arrayTarea['personaEmpresaRolId']      = $arrayParametro['intIdPersonaEmpresaRol'];
                $arrayTarea['ciudad']                   = "SIN INFORMACIÓN";
                $arrayTarea['observacion']              = "SIN OBSERVACION";
                $arrayTareaIncidencia[]                 = $arrayTarea;
            }
            $arrayResultado['tareasInterdepartamentales'] = $arrayTareaIncidencia;
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }

            return $arrayResultado;
        }
        $arrayResultado['status']        = $this->status['OK'];
        $arrayResultado['mensaje']       = $this->mensaje['OK'];
        return $arrayResultado;
    }

   /**
    * Método que lista los servicios que tiene un punto.
    *
    * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
    * @version 1.0
    * @since 18/06/2018
    *
    * @param array $arrayData[
    *                          "data" [
    *                                  "idPunto"   => integer  => Id del punto
    *                                 ]
    *                          "op"    => String   => Nombre del método a consumir,
    *                          "user"  => String   => Usuario que ejecuta el método.
    *                        ]
    * @return array $arrayResultado
    */
    private function getServicioPorPunto($arrayData)
    {
       try
       {   $strMensaje             = "";
           $emComercial            = $this->getDoctrine()->getManager("telconet");
           $arrayEntrada           = array('intIdPunto'            => $arrayData['data']['idPunto'],
                                           'personaEmpresaRolId'   => $arrayData['data']['personaEmpresaRolId']);
           $arrayServicioPorPunto  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                 ->getServicioPorPunto($arrayEntrada);

           if(!is_array($arrayServicioPorPunto))
           {
               $strMensaje = "No existe servicio, para el login seleccionado";
               throw new \Exception('ERROR_PARCIAL');
           }

       }
       catch(\Exception $e)
       {
           if($e->getMessage() == "NULL")
           {
               $arrayResultado['status']    = $this->status['NULL'];
               $arrayResultado['mensaje']   = $this->mensaje['NULL'];
           }
           else if($e->getMessage() == "ERROR_PARCIAL")
           {
               $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
               $arrayResultado['mensaje']   = $strMensaje;
           }
           else
           {
               $arrayResultado['status']    = $this->status['ERROR'];
               $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
           }

           return $arrayResultado;
       }

       $arrayResultado['servicio']  = $arrayServicioPorPunto;
       $arrayResultado['status']    = $this->status['OK'];
       $arrayResultado['mensaje']   = $this->mensaje['OK'];
       return $arrayResultado;
    }

    /**
     * Método que retorna el catalogo de síntomas para crear un caso
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 18-06-2018
     *
     * @param array $arrayData
     * @return array $resultado
     */
    private function getCatalogoSintomasTecnico()
    {
        $arrayRespuesta = array();
        try
        {
            $emSoporte   = $this->getDoctrine()->getManager("telconet_soporte");
            $intStart    = $this->get('request')->query->get('start');
            $intLimit    = $this->get('request')->query->get('limit');

            $arrayParametros = array(
                                        'estado'        => "Activo",
                                        'tipoCaso'      => 1, // Tipo de caso técnico
                                        'codEmpresa'    => 10,
                                        'start'         => $intStart,
                                        'limit'         => $intLimit
                                    );

            $arraySintoma    = $emSoporte->getRepository('schemaBundle:AdmiSintoma')->getRegistros($arrayParametros);
            foreach($arraySintoma as $sintoma)
            {
                $arraySintomas[] = array(
                                            'k'     => $sintoma->getId(),
                                            'v'     => $sintoma->getNombreSintoma()
                                        );
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayRespuesta['status']   = $this->status['NULL'];
                $arrayRespuesta['mensaje']  = $this->mensaje['NULL'];
            }
            else
            {
                $arrayRespuesta['status']   = $this->status['ERROR'];
                $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
            }
            return $arrayRespuesta;
        }
        $arrayRespuesta['sintomas']  = $arraySintomas;
        $arrayRespuesta['status']    = $this->status['OK'];
        $arrayRespuesta['mensaje']   = $this->mensaje['OK'];
        return $arrayRespuesta;
    }

    /*
     * Función que obtiene el sla de un punto.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 29-06-2018
     *
     * @param array $arrayData(
     *                          data :
     *                                  puntos:     int:    punto a obtener
     * @return array $resultado
     */
    private function getSLAporPunto($arrayData)
    {
        $arrayRespuesta     = array();
        try
        {
            $emSoporte       = $this->getDoctrine()->getManager("telconet_soporte");
            $strDateDesde    = explode("-", $arrayData['data']['rangoDesde']);
            $strDateHasta    = explode("-", $arrayData['data']['rangoHasta']);
            $strFechaDesde   = date("Y-m-d", strtotime($strDateDesde[2] . "-" . $strDateDesde[1] . "-" . $strDateDesde[0]));
            $strFechaHasta   = date("Y-m-d", strtotime($strDateHasta[2] . "-" . $strDateHasta[1] . "-" . $strDateHasta[0]));
            do
            {
                $arrayParametros = array(
                                     'puntos'       => array($arrayData['data']['puntos']),
                                     'rangoDesde'   => $strFechaDesde,
                                     'rangoHasta'   => $strFechaDesde
                                    );
                $arraySLAPunto                          = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                                                    ->getDisponibilidadClientesSlaMovil($arrayParametros);
                $objDisponibilidad['disponibilidad'][]  = $arraySLAPunto[0]['porcentajeDisponibilidad'];
                $strDia                                = explode("-", $strFechaDesde);
                $strDia                                = $strDia[2];
                $arrayPuntosDisponibles[] = array('disponibilidad'      => $arraySLAPunto[0]['porcentajeDisponibilidad'],
                                                  'dia'                 => $strDia);
                $strFechaDesde            = date("Y-m-d", strtotime($strFechaDesde. "+1 day"));
            } while (strtotime($strFechaDesde)  <= strtotime($strFechaHasta));
        }
        catch (\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayRespuesta['status']   = $this->status['NULL'];
                $arrayRespuesta['mensaje']  = $this->mensaje['NULL'];
            }
            else
            {
                $arrayRespuesta['status']   = $this->status['ERROR'];
                $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
            }
            return $arrayRespuesta;
        }
        $arrayRespuesta['sla']       = $arrayPuntosDisponibles;
        $arrayRespuesta['status']    = $this->status['OK'];
        $arrayRespuesta['mensaje']   = $this->mensaje['OK'];
        return $arrayRespuesta;
    }

    /**
     * Función que Lista los dispositivos que estan logueados en la app por id_servcio.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 27/11/2018
     *
     * @param type $arrayData
     * @return type
     */
    public function getListadoDispositivo($arrayData)
    {
        $emSoporte       = $this->getDoctrine()->getManager("telconet_soporte");
        try
        {
            $arrayParametros   = array('personaId'  => $arrayData['data']['idPersona'],
                                       'estado'     => 'Activo');
            $arrayDispositivos = $emSoporte->getRepository('schemaBundle:AdmiDispositivoApp')
                                           ->findBy($arrayParametros,
                                                    array('id'    => 'DESC'));
            foreach($arrayDispositivos as $objDispositivoApp)
            {
                $arrayDispRespuesta['descripcion']  = $objDispositivoApp->getDescripcion();
                $arrayDispRespuesta['codigo']       = $objDispositivoApp->getCodigoDispositivo();
                $arrayDispRespuesta['feRegistro']    = strval(date_format($objDispositivoApp->getFeCreacion(), "Y-m-d H:i"));
                $arrayListadoDisp[]                 = $arrayDispRespuesta;
            }
            $arrayRespuesta['status']   = $this->status['OK'];
            $arrayRespuesta['mensaje']  = $this->mensaje['OK'];
            $arrayRespuesta['data']     = $arrayListadoDisp;
        }
        catch (Exception $ex)
        {
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
        }
        return $arrayRespuesta;
    }

    /**
     * Función que obtiene el listado de razones sociales que tienen monitoreo zabbix
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 18/12/2018
     *
     * @param  array $arrayData
     * @return array $arrayRespuesta
     */
    public function getRazonesSociales($arrayData)
    {
        $arrayRespuesta = array();
        $objServiceUtil = $this->get('schema.Util');
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        try
        {
            $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                      ->findOneByLogin($arrayData['user']);
            if(is_object($objPersona))
            {
                $objPersonaEmpRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->getPersonaOficina(array('intIdPersona'    => $objPersona,
                                                                          'strEstado'       => 'Activo',
                                                                          'intCodEmpresa'   => 10));
                if(is_object($objPersonaEmpRol))
                {
                    $objCaracteristica   = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findOneByDescripcionCaracteristica('ID_VIP');

                    $arrayEmpresasIngVip = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                       ->findBy(array('caracteristicaId'    => $objCaracteristica,
                                                                      'valor'               => $objPersonaEmpRol->getId(),
                                                                      'estado'              => 'Activo'));
                    if(isset($arrayEmpresasIngVip) && !empty($arrayEmpresasIngVip))
                    {
                        foreach($arrayEmpresasIngVip as $objEmpresaIngVip)
                        {
                            $arrayDataRS[] = array('idPersona'            => $objEmpresaIngVip->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                                   'idPersonaEmpresaRol'  => $objEmpresaIngVip->getPersonaEmpresaRolId()->getId(),
                                                   'razonSocial'          => $objEmpresaIngVip->getPersonaEmpresaRolId()
                                                                                              ->getPersonaId()->__toString(),
                                                   'identificacion'       => $objEmpresaIngVip->getPersonaEmpresaRolId()
                                                                                              ->getPersonaId()->getIdentificacionCliente());
                        }
                    }
                    else
                    {
                        //Si no es Ing Vip, consulto si es asesor comercial
                        $objDepartamento            = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                                  ->findOneById($objPersonaEmpRol->getDepartamentoId());
                        $objZabbixCaracteristica    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                  ->findOneByDescripcionCaracteristica('IP_SERVIDOR_ZABBIX');
                        if($objDepartamento->getNombreDepartamento() == 'Ventas')
                        {
                            $arrayParametrosComercial   = array('intCaracteristica' => $objCaracteristica->getId(),
                                                                'strEstadoVip'      => 'Activo',
                                                                'strEstado'         => 'Activo',
                                                                'strUsrVendedor'    => $arrayData['user']);
                            $arrayPersonaEmpRolCom      = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                      ->getPersonasEmpresaAsesorComeRS($arrayParametrosComercial);
                            foreach($arrayPersonaEmpRolCom as $objPersonaComercial)
                            {
                                $arrayValorPersona = json_decode(json_encode($objPersonaComercial, 1),1);
                                $objRSComercial = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                              ->findOneById($arrayValorPersona['intIdPersonaEmpresaRol']);

                                $arrayDataRS[] = array('idPersona'            => $objRSComercial->getPersonaId()->getId(),
                                                       'idPersonaEmpresaRol'  => $objRSComercial->getId(),
                                                       'razonSocial'          => $objRSComercial->getPersonaId()->__toString(),
                                                       'identificacion'       => $objRSComercial->getPersonaId()->getIdentificacionCliente());
                            }
                        }
                        else
                        {
                            $arrayEmpresasSinIngVip    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                     ->getPersonasEmpresaRS(array('intCaracteristica'       => $objCaracteristica->getId(),
                                                                                                  'strEstadoVip'            => 'Activo',
                                                                                                  'strEstado'               => 'Activo',
                                                                                                  'intCaracteristicaZabbix' => $objZabbixCaracteristica->getId()));
                            foreach($arrayEmpresasSinIngVip as $objEmpresaIngVip)
                            {
                                $arrayDataRS[] = array('idPersona'            => $objEmpresaIngVip->getPersonaEmpresaRolId()->getPersonaId()->getId(),
                                                       'idPersonaEmpresaRol'  => $objEmpresaIngVip->getPersonaEmpresaRolId()->getId(),
                                                       'razonSocial'          => $objEmpresaIngVip->getPersonaEmpresaRolId()
                                                                                                  ->getPersonaId()->__toString(),
                                                       'identificacion'       => $objEmpresaIngVip->getPersonaEmpresaRolId()
                                                                                                  ->getPersonaId()->getIdentificacionCliente());
                            }
                        }
                    }
                }
            }

            $arrayRespuesta['status']   = $this->status['OK'];
            $arrayRespuesta['mensaje']  = $this->mensaje['OK'];
            $arrayRespuesta['data']     = $arrayDataRS;
        }
        catch (\Exception $ex)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.putActualizaInfoDispositivo',
                                          $ex->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
        }
        return $arrayRespuesta;
    }

    /**
     * Función que obtiene el listado de dispositivos por razones sociales
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 20/01/2019
     *
     * @param  array $arrayData
     * @return array $arrayRespuesta
     */
    public function getDispositivoPorRazonSocial($arrayData)
    {
        $arrayRespuesta = array();
        $objServiceUtil = $this->get('schema.Util');
        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        try
        {
            $arrayDispositivos  = $emSoporte->getRepository('schemaBundle:AdmiDispositivoApp')
                                            ->getListadoDispRazonSocial(array('strEstado' => 'Activo'));
            foreach($arrayDispositivos as $objDispositivoApp)
            {
                $objPersona              = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                       ->findOneById($objDispositivoApp['personaId']);
                $arrayUsuarioVendedor    = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                       ->getUsrVendedorPorIdPersona(array('strEstadoPunto'   => 'Activo',
                                                                                         'intIdPersona'      => $objDispositivoApp['personaId'],
                                                                                         'intPersonaRolId'   => 1,
                                                                                         'strEstadoServicio' => 'Activo'));
                $objPersonaVendedor      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                       ->findOneBy(array('login'      => $arrayUsuarioVendedor[0]['usrVendedor'],
                                                                         'estado'     => 'Activo'));
                $arrayServicio['key']    = $objPersona->__toString();
                $arrayServicio['value']  = $objDispositivoApp['cantidad'];
                $arrayServicio['value2'] = $objPersonaVendedor->__toString();
                $arrayDataRS[]           = $arrayServicio;
            }
            $arrayRespuesta['status']    = $this->status['OK'];
            $arrayRespuesta['mensaje']   = $this->mensaje['OK'];
            $arrayRespuesta['data']      = $arrayDataRS;
        }
        catch (\Exception $ex)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.getDispositivoPorRazonSocial',
                                          $ex->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
        }
        return $arrayRespuesta;
    }

    /**
     * Función que obtiene la cantidad de casos por razón social
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 20/01/2019
     *
     * @param  array $arrayData
     * @return array $arrayRespuesta
     */
    public function getCantCasosPorRazonSocial($arrayData)
    {
        $arrayRespuesta = array();
        $objServiceUtil = $this->get('schema.Util');
        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        try
        {
            $arrayCasosMoviles  = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                            ->getCantCasoMovilPorRazonSocial(array('strOrigen'       => $arrayData['data']['origen'],
                                                                                   'strTipoAfectado' => $arrayData['data']['tipoAfectado'],
                                                                                   'strFeInicial'    => $arrayData['data']['feInicial'],
                                                                                   'strFeFinal'      => $arrayData['data']['feFinal']));
            foreach($arrayCasosMoviles as $objCasosMoviles)
            {
                $objPersona = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                          ->findOneById($objCasosMoviles['idPersonaEmpresaRol']);
                $arrayServicio['key']   = $objPersona->getPersonaId()->__toString();
                $arrayServicio['value'] = $objCasosMoviles['cantCasosMovil'];
                $arrayDataRS[]          = $arrayServicio;
            }
            $arrayRespuesta['status']   = $this->status['OK'];
            $arrayRespuesta['mensaje']  = $this->mensaje['OK'];
            $arrayRespuesta['data']     = $arrayDataRS;
        }
        catch (\Exception $ex)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.getCantCasosPorRazonSocial',
                                          $ex->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
        }
        return $arrayRespuesta;
    }
    /********************************************************************************************
     * FIN METODOS GET SOPORTE MOBIL
     ********************************************************************************************/
    
    /********************************************************************************************
     * METODOS PUT SOPORTE MOBIL
     ********************************************************************************************/
    /**
     * Funcion que sirve para ingresar el seguimiento de una tarea
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 20-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 13-01-2017 - Se realizan ajustes para determinar si se esta iniciando la ejecución de una tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 29-08-2017 - Se agrega un parametro para determinar que desde el movil no se envia el departamento
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.4 19-11-2017 - Se valida el campo del seguimiento para identificar un caso de retiro de equipo que no 
     *                           haya realizado un retiro de Fibra y asi notificar al coordinador asignado.
     * 
     * @author Macjhony Vargas <mmvargas@telconet.ec>
     * @version 1.5 28-10-2019 - Se modifico el resultado del mensaje del response, ya que mostraba null y no mostraba
     *                           su valor correcto.
     *
     * @author Diego Guamán <deguaman@telconet.ec>
     * @version 1.6 31-03-2023 - Se agrega parametro strContactoCliente para validar si es un registro de contacto de cliente
     *                           si es 'S' se obtiene texto html para ingresarlo en seguimiento
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function putIngresarSeguimiento($arrayData)
    {
        $mensaje = "Error al ingresar Seguimiento";
        
        try
        {
            $idEmpresa          = $arrayData['data']['codEmpresa'];
            $prefijoEmpresa     = $arrayData['data']['prefijoEmpresa'];
            $idCaso             = $arrayData['data']['idCaso'];
            $idDetalle          = $arrayData['data']['idDetalle'];
            $intIdComunicacion  = $arrayData['data']['idComunicacion'];
            $strLogin           = $arrayData['data']['login'];
            $seguimiento        = $arrayData['data']['seguimiento'];
            $strEjecucion       = $arrayData['data']['ejecucionTarea'];
            $strDepartamento    = $arrayData['data']['departamento'];
            $strIdPunto         = $arrayData['data']['idPunto'];
            $user               = $arrayData['user'];
            $ipCreacion         = "127.0.0.1";
            $strContactoCliente = $arrayData['data']['esRegistroContacto']?$arrayData['data']['esRegistroContacto'] : "N";
            $strClass           = "SoporteWSController";
            $strAppMethod       = "putIngresarSeguimiento";
            $objServiceUtil = $this->get('schema.Util');
            if($strContactoCliente == "S")
            {
                $strRespuesta = $objServiceUtil->obtenerHtmlSeguimientoRegistroContactos($seguimiento);
                if(!empty($strRespuesta)&& $strRespuesta != "IGUAL")
                {
                    $seguimiento = $strRespuesta;
                }else if(!empty($strRespuesta)&& $strRespuesta == "IGUAL")
                {
                    $arrayResultado['resultado'] = 'Registros iguales, no se actualizaron';
                    $arrayResultado['status']    = $this->status['OK'];
                    $arrayResultado['mensaje']   = $this->mensaje['OK'];
                    return $arrayResultado;
                }else
                {
                    $strMsjErrorRegistroContacto = 'Ocurrió un error en el ingreso de registro de contacto, pruebe nuevamente por favor.';
                    $objServiceUtil->insertLog(array(
                        'enterpriseCode'   => "10",
                        'logType'          => 1,
                        'logOrigin'        => 'TELCOS',
                        'application'      => 'TELCOS',
                        'appClass'         => $strClass,
                        'appMethod'        => $strAppMethod,
                        'descriptionError' => $strMsjErrorRegistroContacto,
                        'status'           => 'Seguimiento',
                        'inParameters'     => json_encode($arrayData),
                        'creationUser'     => 'TELCOS'));
                    $arrayResultado['resultado'] =  $strMsjErrorRegistroContacto;
                    $arrayResultado['status']    = $this->status['DATOS_NO_VALIDOS'];
                    $arrayResultado['mensaje']   = $this->mensaje['DATOS_NO_VALIDOS'];
                    return $arrayResultado;
                }
                
                
            }

            $emComercial    = $this->getDoctrine()->getManager("telconet");
            
            //obtener los datos y departamento de la persona por empresa
            $datos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                 ->getPersonaDepartamentoPorUserEmpresa($user, $idEmpresa);
            
            $arrayParametros = array(
                                        'idEmpresa'             => $idEmpresa,
                                        'prefijoEmpresa'        => $prefijoEmpresa,
                                        'idCaso'                => $idCaso,
                                        'idDetalle'             => $idDetalle,
                                        'seguimiento'           => $seguimiento,
                                        'strEjecucionTarea'     => $strEjecucion,
                                        'departamento'          => $strDepartamento,
                                        'empleado'              => $datos['NOMBRES']." ".$datos['APELLIDOS'],
                                        'usrCreacion'           => $user,
                                        'ipCreacion'            => $ipCreacion,
                                        'strEnviaDepartamento'  => "N",
                                        'strContactoCliente'    => $strContactoCliente,
                                    );
            
            /* @var $ingresarSeguimiento SoporteService */
            $ingresarSeguimiento = $this->get('soporte.SoporteService');
            //---------------------------------------------------------------------*/

            //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
            $respuestaArray = $ingresarSeguimiento->ingresarSeguimientoTarea($arrayParametros);
            $strStatus      = $respuestaArray['status'];
            $mensaje        = $respuestaArray['mensaje'];

            if ($strStatus=="ERROR")
            {
                throw new \Exception($mensaje);
            }
          
            //----------------------------------------------------------------------*/
            /*Si el seguimiento contiene -NO SE HA RETIRADO FIBRA :- procedemos a realizar un llamado 
            a un metodo que notificara a su coordinador que no se ha retirado fibra para que este a su vez
            pueda dar gestion de ello*/
            if (strpos($seguimiento, 'NO SE HA RETIRADO FIBRA :') !== false)
            {
                $emSoporte   = $this->getDoctrine()->getManager("telconet_soporte");
                $objTarea                   = $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                ->findOneBy
                                                                (array(
                                                                    'nombreTarea'    => "CONTROL DE FIBRA NO RETIRADA"
                                                                ));
                
                $seguimiento = $seguimiento." \n Tarea relacionada : ".$intIdComunicacion." \n Login relacionado : ".$strLogin;
                
                $arrayParametrosCoordenadas = array(
                    'objTarea'      => $objTarea,
                    'latitud'       => "0",
                    'longitud'      => "0",
                    'observaciones' => $seguimiento,
                    'empresaCod'    => $idEmpresa,
                    'ipCreacion'    => "127.0.0.1",
                    'usrCreacion'   => $user,
                    'intPuntoId'    => $strIdPunto);
                
                $serviceSoporte             = $this->get('soporte.SoporteService');
                $serviceSoporte->crearTareaAlCoordinador($arrayParametrosCoordenadas);  
            }
            if($strStatus == "OK")
            {
                $mensaje = "Se ingreso el Seguimiento!";
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $e->getMessage();
            }
        
            return $resultado;
        }
        
        $resultado['resultado'] = $mensaje;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para crear una tarea
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 12-02-2016
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 31/01/2017   Se agrega parametro al método que registra la tarea interna mediante un servicio publicado en el modulo de soporte,
     *                           este parametro indica el origin de la petición del servicio, los posibles valores de este parametro son 
     *                           ["WS","WEB-TN"]
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 25-10-2018 - Se agrega una nueva validación para considerar el login del Cliente si la variable conLogin se encuentra en S = SI.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.4 01/08/2019   - Se agrega parámetro al método que registra la tarea interna mediante un servicio 
     *                             publicado en el módulo de soporte,este parametro indica la empresa origen que crea la tarea.
     *                           - Se agrega parámetro finalizaTarea al método del ws el cual indica si se desea crear la tarea 
     *                             ya finalizada en forma automática.
     * 
     *  @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 07/10/2019   - Se agrega envío de parámetro clientes a la función de finalizarTarea 
     *                             donde se adjunta el login del cliente afectado. 
     *                             Esto es para que se pueda visualizar en el correo de finalización de tarea el login afectado.
     *
     * @author Macjhony Vargas <mmvargas@telconet.ec>
     * @version 1.6 28/10/2019   - Se le agrega el idDetalle al response, para facilitar acciones a los usuarios a la hora de
     *                             ingresar seguimiento tareas y finalizar tareas vía WS.
     *
     * @since 1.1
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.7 23/04/2020   - Se cambia la obtención del parámetro strOrigen
     * @since 1.6
     * 
     * @author David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.8 27/08/2021 - Se agrega validacion para evitar tareas duplicadas por observacion
     * @since 1.7
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function putIngresarTareaInterna($arrayData)
    {
        $status  = "";
        $mensaje = "";
        $id      = "";
  
        try
        {
            $prefijoEmpresa         = $arrayData['data']['prefijoEmpresa'];
            $codEmpresa             = $arrayData['data']['codEmpresa'];
            $strCodEmpresaOrig      = $arrayData['data']['codEmpresaOrig'];
            $strFinalizaTarea       = (isset($arrayData['data']['finalizaTarea']))?$arrayData['data']['finalizaTarea']:"N";
            $nombreTarea            = $arrayData['data']['nombreTarea'];
            $observacion            = $arrayData['data']['observacion'];
            $nombreDepartamento     = $arrayData['data']['nombreDepartamento'];
            $ciudad                 = $arrayData['data']['ciudad'];
            $ip                     = $arrayData['source']['originID'];
            $user                   = $arrayData['user'];
            $empleado               = "";
            $strLogin               = '';
            $strConLogin            = $arrayData['data']['conLogin'];
            $emComercial            = $this->getDoctrine()->getManager("telconet");
            $emComunicacion         = $this->getDoctrine()->getManager("telconet_comunicacion");
            $emGeneral              = $this->getDoctrine()->getManager("telconet_general");
            $strOrigen              = (isset($arrayData['data']['origen']))?$arrayData['data']['origen']:"WS";

            $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('WEB SERVICE TAREAS','SOPORTE','','RESTAR_MINUTOS','','','','','',$codEmpresa);

            if ($arrayAdmiParametroDet['valor1'] === 'S')
            {
                $emSoporte        = $this->getDoctrine()->getManager("telconet_soporte");
                $intTiempoMinutos = $arrayAdmiParametroDet['valor2'];
                $objFechaActual   = new \DateTime('now');
                $objFechaActual->modify('-'.$intTiempoMinutos.' minute');
                $strFechaPivote   = $objFechaActual->format('d/m/Y H:i:s');

                $arrayRespuesta = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                                ->getTareaPorObservacion(array(
                                                        'fechaPivote'=>$strFechaPivote,
                                                        'user'=>$user,
                                                        'observacion'=>$observacion
                                ));
                if (isset($arrayRespuesta[0]['intNumeroTarea']))
                {
                    return array('status'  => $this->status['ERROR'],
                                 'mensaje' => "Tarea duplicada");
                }
            }

            if (strtoupper($strConLogin) === 'S')
            {
                if(!isset($arrayData['data']['login']) ||
                    empty($arrayData['data']['login']) ||
                    !is_string($arrayData['data']['login']))
                {
                    return array('status'  => $this->status['ERROR'],
                                 'mensaje' => "Login Invalido");
                }

                $strLogin = $arrayData['data']['login'];

                $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                    ->findOneBy(array('login'  => $strLogin,
                                      'estado' => 'Activo'));

                if (!is_object($objInfoPunto))
                {
                    return array('status'  => $this->status['ERROR'],
                                 'mensaje' => "El login $strLogin no existe!");
                }
            }

            //obtener los datos y departamento de la persona por empresa
            $datosUsuario = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'  => $user,
                                                                                                     'estado' => 'Activo'));

            if($datosUsuario)
                $empleado   = $datosUsuario->getNombres()." ".$datosUsuario->getApellidos();

            $arrayParametros = array('strIdEmpresa'          => $codEmpresa,
                                     'strPrefijoEmpresa'     => $prefijoEmpresa,
                                     'strNombreTarea'        => $nombreTarea,
                                     'strObservacion'        => $observacion,
                                     'strNombreDepartamento' => $nombreDepartamento,
                                     'strCiudad'             => $ciudad,
                                     'strEmpleado'           => $empleado,
                                     'strUsrCreacion'        => $user,
                                     'strIp'                 => $ip,
                                     'strOrigen'             => $strOrigen,
                                     'strLogin'              => $strLogin,
                                     'intPuntoId'            => is_object($objInfoPunto)?$objInfoPunto->getId():'',
                                     'strCodEmpresaOrig'     => $strCodEmpresaOrig);

            /* @var $ingresarSeguimiento SoporteService */
            $ingresarSeguimiento = $this->get('soporte.SoporteService');
            //---------------------------------------------------------------------*/

            //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
            $respuestaArray = $ingresarSeguimiento->ingresarTareaInterna($arrayParametros);
            $status         = $respuestaArray['status'];
            $mensaje        = $respuestaArray['mensaje'];
            $id             = $respuestaArray['id'];
            $intIdDetalle   = $respuestaArray['idDetalle'];
            //----------------------------------------------------------------------*/

            if($status == "OK")
            {
                //Si se recibe parametro finalizaTarea => S entonces se procede a finalizar la tarea creada.
                if (isset($strFinalizaTarea) && strtoupper($strFinalizaTarea) === "S")
                {
                    //obtener los datos y departamento de la persona por empresa
                    $datosUsuario = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getPersonaDepartamentoPorUserEmpresa($user, $codEmpresaOrig);
                    $intIdAsignado = $datosUsuario['ID_PERSONA'];

                    $objFechaHoy         = new \DateTime('now');
                    $strFechaFinaliza    = $objFechaHoy->format('Y-m-d');
                    $strHoraFinaliza     = $objFechaHoy->format('H:i:s');
                    $intIdDetalle        = "";
                    $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($id);

                    if(is_object($objInfoComunicacion))
                    {
                        $intIdDetalle = $objInfoComunicacion->getDetalleId();
                    }
                    $arrayParametros = array(
                        'idEmpresa'             => $codEmpresa,
                        'prefijoEmpresa'        => $prefijoEmpresa,
                        'idCaso'                => "",
                        'idDetalle'             => $intIdDetalle,
                        'tarea'                 => $id,
                        'fechaCierre'           => $strFechaFinaliza,
                        'horaCierre'            => $strHoraFinaliza,
                        'fechaEjecucion'        => $strFechaFinaliza,
                        'horaEjecucion'         => $strHoraFinaliza,
                        'esSolucion'            => "",
                        'fechaApertura'         => "",
                        'horaApertura'          => "",
                        'jsonMateriales'        => "",
                        'idAsignado'            => $intIdAsignado,
                        'observacion'           => "Se finaliza tarea en forma automática",
                        'empleado'              => $empleado,
                        'usrCreacion'           => $user,
                        'ipCreacion'            => $ip,
                        'strEnviaDepartamento'  => "N",
                        "clientes"              => $strLogin
                    );

                    $arrayRespuestaFinaliza = $ingresarSeguimiento->finalizarTarea($arrayParametros);
                    $strStatusFinaliza      = $arrayRespuestaFinaliza['status'];
                    if($strStatusFinaliza == "OK")
                    {
                        $mensaje = "Se creó la tarea rápida!";
                    }
                    else
                    {
                        $mensaje = "Se creó la tarea rápida, pero no fue finalizada!";
                    }
                }
                else
                {
                    $mensaje = "Se creó la tarea!";
                }
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }

            return $resultado;
        }

        $resultado['id']        = $id;
        $resultado['idDetalle'] = $intIdDetalle;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $mensaje;
        return $resultado;
    }

    /**
     * Funcion que sirve para obtener el ultimo estado de una tarea.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 11-11-2016
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 12-01-2017 Se realiza la respectiva validación para que sólo una tarea se esté realizando a la vez
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 24-08-2017 Se contabiliza el numero de seguimientos ingresados por el tecnico.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 15-09-2017 - Buscar la tarea que se encuentra en estado Aceptada.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.4 26-02-2018 - Validación para mostrar boton de actualizar coordenada en las tareas del movil.
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getUltimoEstadoTarea($arrayData)
    {
        $arrayResultado                             = array();
        $arrayAdmiParamDetCant                      = array();
        $arrayPersonaEmpresaRolId                   = array();
        $strMensaje                                 = "No se puede obtener el estado de la Tarea";
        $boolExisteTareaHaIniciadoEjecucion         = false;
        $boolTareaHaIniciadoEjecucion               = false;
        $boolPresentarBtnReanudar                   = false;
        $boolBuscarOtrasTareasIniciadasDeAsignacion = false;
        $booleanReasignarTareaBoton                 = false;
        $boolPresentarBtnCoordenada                 = true;
        $intIdDetalleTareaEjecutandose              = 0;
        $strNombreTareaEjecutandose                 = "";
        $strObservacionTareaEjecutandose            = "";
        $strObservacionSeguimientoInicioEjecucion   = "";
        $intIdComunicacionTareaEjecutandose         = 0;
        try
        {
            $emSoporte                          = $this->getDoctrine()->getManager("telconet_soporte");
            $emGeneral                          = $this->getDoctrine()->getManager("telconet_general");
            $emComercial                        = $this->getDoctrine()->getManager("telconet");
            $arrayParametros["intIdTarea"]      = $arrayData['data']['idTarea'];
            $soporteService                     = $this->get('soporte.SoporteService');
           
            $stringRespuesta                    = $soporteService->obtenerUltimoEstadoTarea($arrayParametros);
            $arrayAdmiParametroDet              = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne(   'MSG_INICIO_EJECUCION_TAREA', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '');
            
            if( $arrayAdmiParametroDet )
            {
                $strObservacionSeguimientoInicioEjecucion = $arrayAdmiParametroDet["valor1"];
            }
             
            /*
             * Las tareas que se muestran en el móvil son sólo las automáticas y por defecto están aceptadas
             */
            if(isset($stringRespuesta))
            {
                /*
                 * Si el último estado es Aceptada o Reprogramada deberá mostrar el botón para iniciar la ejecución de tarea.
                 * Anteriormente se mostraba el botón Pausar, ahora debería mostrar el botón Iniciar
                 */
                if($stringRespuesta == "Aceptada" || $stringRespuesta == "Reprogramada")
                {
                    $stringRespuesta = "Aceptada";
                }
                $strMensaje = "Consulta Exitosa";
                
                
                
                if(($stringRespuesta == "Aceptada" || $stringRespuesta == "Asignada") && $strObservacionSeguimientoInicioEjecucion)
                {
                    /*
                     * Busca si se ha ingresado el respectivo seguimiento indicando que se ha iniciado la ejecucion de dicha tarea.
                     */
                    $arrayParametrosBusq    =   array(
                                                        "intIdDetalle"                      => $arrayParametros["intIdTarea"],
                                                        "strObservacionDetalleSeguimiento"  => $strObservacionSeguimientoInicioEjecucion
                                                );
                    $arrayRespuestaDetallesAsignacion   = $emSoporte->getRepository("schemaBundle:InfoDetalleSeguimiento")
                                                                    ->getTareasSeguimientosPorCriterios($arrayParametrosBusq);
                    
                    $intTotalDetallesAsignacion         = $arrayRespuestaDetallesAsignacion['intTotal'];
                    $arrayResultadoDetallesAsignacion   = $arrayRespuestaDetallesAsignacion['arrayResultado'];
                    
                    if($intTotalDetallesAsignacion>0)
                    { 
                        if($arrayResultadoDetallesAsignacion)
                        {
                            $boolExisteTareaHaIniciadoEjecucion = true;
                            $boolTareaHaIniciadoEjecucion       = true;
                            $intIdDetalleTareaEjecutandose      = $arrayParametros["intIdTarea"];
                            foreach($arrayResultadoDetallesAsignacion as $arrayResultadoDetalleAsignacion)
                            {
                                $intIdDetalleTareaEjecutandose      = $arrayResultadoDetalleAsignacion["intIdDetalle"];
                                $strNombreTareaEjecutandose         = $arrayResultadoDetalleAsignacion["strNombreTarea"];
                                $strObservacionTareaEjecutandose    = $arrayResultadoDetalleAsignacion["strObservacionTarea"];
                                $intIdComunicacionTareaEjecutandose = $arrayResultadoDetalleAsignacion["intIdComunicacion"];
                            }
                        }
                    }
                    else
                    {
                        $boolBuscarOtrasTareasIniciadasDeAsignacion = true;
                        
                    }
                }
                //Ya se ha iniciado la tarea en algún momento y por ende se debe mostrar el botón reanudar
                else if($stringRespuesta=="Pausada")
                {
                    $boolPresentarBtnReanudar                   = true;
                    $boolBuscarOtrasTareasIniciadasDeAsignacion = true;
                }
                else
                {    
                    $strMensaje =  'el estado no es el correcto.';
                }
                
                if($boolBuscarOtrasTareasIniciadasDeAsignacion)
                {
                    $objPersonaTarea = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                   ->findOneBy(array('login' => $arrayData['user']));
                    if(is_object($objPersonaTarea))
                    {
                        $arrayPersonaRolTarea = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                            ->findBy(array('personaId' => $objPersonaTarea->getId(),
                                                                           'estado'    => 'Activo'));
                        foreach($arrayPersonaRolTarea as $objPersonaRolTarea)
                        {
                            $arrayPersonaEmpresaRolId[] = $objPersonaRolTarea->getId();
                        }
                    }
                    $boolTareaHaIniciadoEjecucion   = false;
                    $objUltimaAsignacionTarea       = $emSoporte->getRepository("schemaBundle:InfoDetalleAsignacion")
                                                                ->getUltimaAsignacion($arrayParametros["intIdTarea"]);

                    if(is_object($objUltimaAsignacionTarea))
                    {
                        $arrayParametrosBusq = array(
                                                        "strTipoAsignado"                   => $objUltimaAsignacionTarea->getTipoAsignado(),
                                                        "intAsignadoId"                     => $objUltimaAsignacionTarea->getAsignadoId(),
                                                        "intRefAsignadoId"                  => $objUltimaAsignacionTarea->getRefAsignadoId(),
                                                        "arrayPersonaEmpresaRolId"          => $arrayPersonaEmpresaRolId,
                                                        "strObservacionDetalleSeguimiento"  => $strObservacionSeguimientoInicioEjecucion,
                                                        "arrayUltimosEstadoTarea"           => array("Aceptada", "Reprogramada")
                                                );
                        $arrayRespuestaDetallesAsignacion   = $emSoporte->getRepository("schemaBundle:InfoDetalleSeguimiento")
                                                                        ->getTareasSeguimientosPorCriterios($arrayParametrosBusq);

                        $intTotalDetallesAsignacion         = $arrayRespuestaDetallesAsignacion['intTotal'];
                        $arrayResultadoDetallesAsignacion   = $arrayRespuestaDetallesAsignacion['arrayResultado'];

                        if($intTotalDetallesAsignacion > 0)
                        {
                            if($arrayResultadoDetallesAsignacion)
                            {
                                $boolExisteTareaHaIniciadoEjecucion = true;
                                foreach($arrayResultadoDetallesAsignacion as $arrayResultadoDetalleAsignacion)
                                {
                                    $intIdComunicacionTareaEjecutandose = $arrayResultadoDetalleAsignacion["intIdComunicacion"];
                                    $intIdDetalleTareaEjecutandose      = $arrayResultadoDetalleAsignacion["intIdDetalle"];
                                    $strNombreTareaEjecutandose         = $arrayResultadoDetalleAsignacion["strNombreTarea"];
                                    $strObservacionTareaEjecutandose    = $arrayResultadoDetalleAsignacion["strObservacionTarea"];
                                }
                            }
                        }
                    }
                }
            }
            // Números de seguimientos requeridos para poder reasignar una tarea.
            $arrayAdmiParamDetCant  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne(   'NUMERO_SEGUIMIENTO_SOLICITADOS',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            '');
            if( $arrayAdmiParamDetCant && count($arrayAdmiParamDetCant)>0)
            {
                $arrayParametrosSeguimientos = array('intDetalleId'           => $arrayParametros["intIdTarea"],
                                                     'arrayEstadoTarea'       => array('Aceptada','Pausada'),
                                                     'strUsrCreacion'         => $arrayData['user'],
                                                     'strObservacionReanudar' => '%Tarea fue Reanudada%',
                                                     'strObservacionPausa'    => '%Tarea fue Pausada%',
                                                     'strOrigen'              => 'MOVIL');

                $arrayTareaSeguimiento       = $emSoporte->getRepository('schemaBundle:InfoTareaSeguimiento')
                                                         ->getSeguimiento($arrayParametrosSeguimientos);
                if(count($arrayTareaSeguimiento) >= $arrayAdmiParamDetCant["valor1"])
                {
                    $booleanReasignarTareaBoton = true;
                }
            }
            //Verificar si tengo una coordenada activa
            $objCaracteristica        = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica("ID_METROS_MAXIMO");

            $arrayPuntoCaracteristica = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                    ->findBy(array('puntoId'            => $arrayData['data']['idPunto'],
                                                                   'caracteristicaId'   => $objCaracteristica->getId(),
                                                                   'estado'             => 'Activo'));
            if(count($arrayPuntoCaracteristica) > 0)
            {
                $boolPresentarBtnCoordenada = false;
            }

            $arrayCantidadSugerida  = array('intIdCaracteristica'  => $objCaracteristica->getId(),
                                            'intIdPunto'           => $arrayData['data']['idPunto']);

            $intPtoCaracteristica = $emComercial->getRepository('schemaBundle:InfoPuntoCaracteristica')
                                                ->obtenerCantidadCoordenadaSugerida($arrayCantidadSugerida);

            if($intPtoCaracteristica == 1)
            {
                $boolPresentarBtnCoordenada = true;
            }
        }
        catch(Exception $e)
        {
            error_log(".....-.-.-.-.---- ERROr".$e->getMessage());
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }

            return $arrayResultado;
        }
        $arrayResultado['boolPresentarBtnReanudar']                     = $boolPresentarBtnReanudar;
        $arrayResultado['boolExisteTareaHaIniciadoEjecucion']           = $boolExisteTareaHaIniciadoEjecucion;
        $arrayResultado['boolTareaHaIniciadoEjecucion']                 = $boolTareaHaIniciadoEjecucion;
        $arrayResultado['strObservacionSeguimientoInicioEjecucion']     = $strObservacionSeguimientoInicioEjecucion;
        $arrayResultado['intIdDetalleTareaEjecutandose']                = $intIdDetalleTareaEjecutandose;
        $arrayResultado['intIdComunicacionTareaEjecutandose']           = $intIdComunicacionTareaEjecutandose;
        $arrayResultado['strNombreTareaEjecutandose']                   = $strNombreTareaEjecutandose;
        $arrayResultado['strObservacionTareaEjecutandose']              = $strObservacionTareaEjecutandose;
        $arrayResultado['boolReasignarTareaBoton']                      = $booleanReasignarTareaBoton;
        $arrayResultado['strObservacionReasignacion']                   = $arrayAdmiParamDetCant["valor2"];
        $arrayResultado['boolPresentarBtnCoordenada']                   = $boolPresentarBtnCoordenada;
        $arrayResultado['estado']                                       = $stringRespuesta;
        $arrayResultado['status']                                       = $this->status['OK'];
        $arrayResultado['mensaje']                                      = $strMensaje;
        return $arrayResultado;
    }
    
    /**
     * Funcion que sirve para obtener el ultimo estado de una tarea.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 19-11-2016
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 13-05-2019 - Se agrega el parámetro idDepartamento, para obtener los motivos parametrizados del departamento.
     *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getMotivoPausar($arrayData)
    {
        $arrayResultado = array();
        $arrayMotivos   = array();
        $strMensaje     = "No se puede obtener el motivo de pausar Tarea";
        try
        {
            $arrayParametros["strOpcion"]         = $arrayData['data']['strOpcion'];
            $arrayParametros["strIdDepartamento"] = $arrayData['data']['idDepartamento'];

            $soporteService               = $this->get('soporte.SoporteService');
            $arrayRespuesta               = json_decode($soporteService->obtenerMotivosPorOpcion($arrayParametros),true);
            if($arrayRespuesta['total']  != 0)
            {
                $strMensaje = "Consulta Exitosa";
                foreach($arrayRespuesta['encontrados'] as $motivos)
                {
                    $arrayMotivos[] = array( 'id_motivo'    => $motivos['id_motivo'],
                                             'nombre_motivo'=> $motivos['nombre_motivo']
                                            );
                }
            }
        }
        catch(Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }

            return $arrayResultado;
        }
        $arrayResultado['motivos']       = $arrayMotivos;
        $arrayResultado['status']        = $this->status['OK'];
        $arrayResultado['mensaje']       = $strMensaje;
        return $arrayResultado;
    }
    
    /**
     * Función que sirve para obtener los datos de la asignación de una tarea ya sea a un empleado,cuadrilla o contratista
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 18-11-2016
     * @param array $arrayData[
     *                          "op"    => string con el nombre de la función que se desea consultar
     *                          "data"  => array con los parámetros necesarios para la obtención de la información 
     *                                     de la asignación actual de la tarea.
     *                                     [
     *                                         "idDetalle"  => int del id del detalle de la tarea,
     *                                         "codEmpresa" => string del id de la empresa,
     *                                     ]
     *                          "token"      => token,
     *                          "source"     => ["name","originID","tipoOriginID"}, 
     *                          "user"       => string del login del usuario en sesión
     *                        ]
     *                          
     * ]
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.1 08-03-2022 - Se agrega validacion de estado en la Info_Persona_Empresa_Rol para que el empleado asignado como chofer esté activo
     * 
     * 
     * @return array $arrayResultado
     */
    private function getDatosAsignacionActualTarea($arrayData)
    {
        $arrayResultadoAsignacion           = array();
        $arrayTmpPersonasCuadrilla          = array();
        $arrayRegistrosPersonasCuadrilla    = array();
        $arrayResultado['status']           = $this->status['NULL'];
        try
        {
            $intIdDetalle                   = $arrayData['data']['idDetalle'];
            $strCodEmpresaSession           = $arrayData['data']['codEmpresa'];
            $strCodEmpresaAsignacion        = "";
            $emSoporte                      = $this->getDoctrine()->getManager("telconet_soporte");
            $emComercial                    = $this->getDoctrine()->getManager("telconet");
            $emInfraestructura              = $this->getDoctrine()->getManager("telconet_infraestructura");
            $strNombreLiderCuadrilla        = '';
            $strHoraInicioCuadrilla         = "";
            $strHoraFinCuadrilla            = "";
            $arrayIntegrantesCuadrilla      = array();
            $strNombreDepartamento          = "";
            $strNombreCoordinador           = "";
            $strNombreCargo                 = "";
            $intNumIntegrantesCuadrilla     = 0;

            $objUltimaAsignacionTarea = $emSoporte->getRepository("schemaBundle:InfoDetalleAsignacion")->getUltimaAsignacion($intIdDetalle);
            if(is_object($objUltimaAsignacionTarea))
            {
                $intIdDetalleAsignacion = $objUltimaAsignacionTarea->getId();
                $intAsignadoId          = $objUltimaAsignacionTarea->getAsignadoId();
                $strNombreAsignado      = $objUltimaAsignacionTarea->getAsignadoNombre();
                $intRefAsignadoId       = $objUltimaAsignacionTarea->getRefAsignadoId();
                $strRefNombreAsignado   = $objUltimaAsignacionTarea->getRefAsignadoNombre();
                $strTipoAsignado        = $objUltimaAsignacionTarea->getTipoAsignado();
                $intIdPerAsignacion     = $objUltimaAsignacionTarea->getPersonaEmpresaRolId();
     
                $intIdElementoVehiculo     = 0;
                $strPlacaElementoVehiculo  = '';
                
                if($intIdPerAsignacion)
                {
                    $objPerAsignacion   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerAsignacion);
                    if(is_object($objPerAsignacion))
                    {
                        $objEmpresaRolAsignacion  = $objPerAsignacion->getEmpresaRolId();
                        $intIdDepartamento        = $objPerAsignacion->getDepartamentoId();
                        $intCoordinador           = $objPerAsignacion->getReportaPersonaEmpresaRolId();
                        $objDepartamento          = $emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                                ->findOneById($intIdDepartamento);
                        $strNombreCargo           = $objPerAsignacion ->getPersonaId()
                                                                       ->getCargo();
                        if(is_object($objDepartamento))
                        {
                            $strNombreDepartamento = $objDepartamento->getNombreDepartamento();
                        }
                        
                        $objCoordinadorPersona    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                ->findOneById($intCoordinador);
                        if(is_object($objCoordinadorPersona))
                        {
                            $strNombreCoordinador = $objCoordinadorPersona->getPersonaId()->getNombres().' '.
                                                    $objCoordinadorPersona->getPersonaId()->getApellidos();
                        }
                        if(is_object($objEmpresaRolAsignacion))
                        {
                            $strCodEmpresaAsignacion    = $objEmpresaRolAsignacion->getEmpresaCod();
                        }
                    }
                }
                else
                {
                    $strCodEmpresaAsignacion    = $strCodEmpresaSession;
                }

                $arrayParametrosIntegrantesCuadrilla    = array();

                if($strTipoAsignado=="CUADRILLA")
                {
                    if($intAsignadoId)
                    {
                        $intIdCuadrilla             = $intAsignadoId;
                        $strNombreLiderCuadrilla    = $strRefNombreAsignado;
                        $objCuadrilla               = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdCuadrilla);

                        if(is_object($objCuadrilla))
                        {
                            $strHoraInicioCuadrilla = $objCuadrilla->getTurnoHoraInicio();
                            $strHoraFinCuadrilla    = $objCuadrilla->getTurnoHoraFin();
                        }

                        $arrayCargos                = array();
                        $objCargos                  = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->get('CARGOS AREA TECNICA', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        'Personal Tecnico', 
                                                                        '',
                                                                        '', 
                                                                        ''
                                                                        );
                        if(is_object($objCargos) )
                        {
                            foreach($objCargos as $objCargoTecnico)
                            {
                                $arrayCargos[] = $objCargoTecnico['descripcion'];

                            }
                        }

                        $arrayParametrosIntegrantesCuadrilla['criterios']['cargoSimilar']   = $arrayCargos;
                        $arrayParametrosIntegrantesCuadrilla['intIdCuadrilla']              = $intIdCuadrilla;
                        $arrayParametrosIntegrantesCuadrilla['empresa']                     = $strCodEmpresaAsignacion;

                        $arrayTmpPersonasCuadrilla = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                 ->findPersonalByCriterios($arrayParametrosIntegrantesCuadrilla);


                        $arrayRegistrosPersonasCuadrilla    = $arrayTmpPersonasCuadrilla['registros'];                                    
                        
                        if( $arrayRegistrosPersonasCuadrilla )
                        {
                            foreach ($arrayRegistrosPersonasCuadrilla as $arrayDatosIntegrante)
                            {
                                $intIdPersonaEmpresaRolIntegrante   = $arrayDatosIntegrante['idPersonaEmpresaRol'];
                                $objPerEmpresaRol                   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                  ->find($intIdPersonaEmpresaRolIntegrante);
                                $intNumIntegrantesCuadrilla++;
                                $strNombresApellidosIntegrante  = ucwords(strtolower(trim($arrayDatosIntegrante['nombres']))).' '.
                                                                  ucwords(strtolower(trim($arrayDatosIntegrante['apellidos'])));

                                $arrayIntegrantesCuadrilla[]    = array(
                                                                        "intIdPersonaEmpresaRolIntegrante"  => $intIdPersonaEmpresaRolIntegrante,
                                                                        "strNombreIntegrante"               => $strNombresApellidosIntegrante,
                                                                        "strCargo"                          => $objPerEmpresaRol->getPersonaId()
                                                                                                                                ->getCargo()
                                                                  );

                            }
                        }

                    }              
                }
                
                $arrayParametros = [ 
                                    'strDetalleValor'   => $intAsignadoId,
                                    'strDetalleNombre'  => 'CUADRILLA',
                                    'strEstado'         => 'Activo'
                                   ];
                        
                $arrResultVehiculo = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                       ->getArrayElementosPorDetalle($arrayParametros);

                if(isset($arrResultVehiculo) && count($arrResultVehiculo) > 0)
                {
                    $intIdElementoVehiculo      = $arrResultVehiculo[0]['idElemento'];
                    $strPlacaElementoVehiculo   = $arrResultVehiculo[0]['nombreElemento'];
                    
                    $arrayParametros = [ 
                                    'intIdElemento'   => $intIdElementoVehiculo,
                                    'strDetalleNombre'  => 'ASIGNACION_VEHICULAR_ID_SOL_PREDEF_CUADRILLA',
                                    'strEstado'         => 'Activo'
                                   ];
                        
                    $arrResultChoferSol = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                       ->getArrayElementosPorDetalle($arrayParametros);
                    if(isset($arrResultChoferSol) && count($arrResultChoferSol) > 0)
                    {
                        $intIdPersonaEmpresaRolIntegrante   = $arrResultChoferSol[0]['detalleValor'];
                        
                        $arrResultChofer = $emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                             ->getArrayAsignadoSolicitud([
                                                                'intDetalleSolicitudId' => $intIdPersonaEmpresaRolIntegrante
                                                               ]);
                        
                        if(isset($arrResultChofer) && count($arrResultChofer) > 0)
                        {
                            $intIdPersonaEmpresaRolIntegrante = $arrResultChofer[0]['idPersonaEmpresaRolId'];
                            $strNombresApellidosIntegrante    = $arrResultChofer[0]['refAsignadoNombre'];

                            $objPerEmpresaRol                   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                               ->findBy(array ('id' => $intIdPersonaEmpresaRolIntegrante,
                                                                                               'estado'  => 'Activo'));
                            if(!empty($objPerEmpresaRol))
                            {
                                foreach($objPerEmpresaRol as $objInfoPerEmpresaRol)
                                {
                                    $intNumIntegrantesCuadrilla++;

                                    $arrayIntegrantesCuadrilla[]    = array(
                                                                    "intIdPersonaEmpresaRolIntegrante"  => $intIdPersonaEmpresaRolIntegrante,
                                                                    "strNombreIntegrante"               => $strNombresApellidosIntegrante,
                                                                    "strCargo"                          => $objInfoPerEmpresaRol->getPersonaId()
                                                                                                                        ->getCargo()
                                                                    );
                                }
                            }
                        }
                    }
                }
                
                $arrayResultadoAsignacion   = array("idDetalleAsignacion"       => $intIdDetalleAsignacion,
                                                    "asignadoId"                => $intAsignadoId,
                                                    "nombreAsignadoId"          => $strNombreAsignado,
                                                    "refAsignadoId"             => $intRefAsignadoId,
                                                    "refAsignadoNombre"         => $strRefNombreAsignado,
                                                    "tipoAsignado"              => $strTipoAsignado,
                                                    "horaInicioCuadrilla"       => $strHoraInicioCuadrilla,
                                                    "horaFinCuadrilla"          => $strHoraFinCuadrilla,
                                                    "numIntegrantesCuadrilla"   => $intNumIntegrantesCuadrilla,
                                                    "integrantesCuadrilla"      => $arrayIntegrantesCuadrilla,
                                                    "nombreDepartamento"        => $strNombreDepartamento,
                                                    "nombreCoordinador"         => $strNombreCoordinador,
                                                    "codigolider"               => $intIdPerAsignacion,
                                                    "Cargo"                     => $strNombreCargo,
                                                    "intIdElementoVehiculo"     => $intIdElementoVehiculo,
                                                    "strPlacaElementoVehiculo"  => $strPlacaElementoVehiculo
                                                    );

                $arrayResultado['infoAsignacion']   = $arrayResultadoAsignacion;
                $arrayResultado['status']           = $this->status['OK'];
                $arrayResultado['mensaje']          = $this->mensaje['OK'];

            }
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = "No se pudo obtener la información de la Asignación de la Tarea";
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }
        }
        return $arrayResultado;
    }
    
    /**
     * Funcion que sirve para invocar al service de finalizar tarea
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 24/08/2017
     *
     * @param array $arrayData[
     *                          "op"    => string con el nombre de la función que se desea consultar
     *                          "data"  => array con los parámetros necesarios para la obtención de la información
     *                                     de la asignación actual de la tarea.
     *                                     [
     *                                         "idPersonaEmpresaRol"  => int del id persona empresa rol
     *                                     ]
     *                          "token"      => token,
     *                          "source"     => ["name","originID","tipoOriginID"},
     *                          "user"       => string del login del usuario en sesión
     *                        ]
     * @return array $resultado
     */
    private function getCoordinadoresDeCuadrillas($arrayData)
    {
        $arrayResultado        = array();
        $arrayPersonaCuadrilla = array();
        try
        {
            $emComercial               = $this->getDoctrine()->getManager("telconet");
            $objCuadrillaDelPersonal   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                     ->findOneById($arrayData['data']['idPersonaRol']);
            if(!empty($objCuadrillaDelPersonal))
            {
                $arrayPersonaCuadrilla     = array(
                                               'idCuadrilla' => $objCuadrillaDelPersonal->getCuadrillaId()
                                              );
                $objCoordinadorDeCuadrilla = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->getCoordinadorDeCuadrilla($arrayPersonaCuadrilla);
                if(!empty($objCoordinadorDeCuadrilla))
                {
                    $arrayResultado['coordinadorAsignado']   = array(
                                                                        'nombre'       => $objCoordinadorDeCuadrilla->getPersonaId()->__toString(),
                                                                        'idPersona'    => $objCoordinadorDeCuadrilla->getPersonaId()->getId(),
                                                                        'idPersonaRol' => $objCoordinadorDeCuadrilla->getId()
                                                                    );
                }
                $arrayCoordinadoresXDepartamento    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                  ->getCoordinadorXDepartamento($arrayData['data']);
                foreach($arrayCoordinadoresXDepartamento as $arrayCoordinador)
                {
                    $arrayCoordDepart[] = array(
                                                'nombre'       => $arrayCoordinador['nombres'],
                                                'idPersona'    => $arrayCoordinador['idPersona'],
                                                'idPersonaRol' => $arrayCoordinador['idPersonaRol']
                                                );
                }
                $arrayResultado['coordinadoresDept'] = $arrayCoordDepart;
                $arrayResultado['status']            = $this->status['OK'];
                $arrayResultado['mensaje']           = $this->mensaje['OK'];
            }
        }
        catch(\Exception $e)
        {
            $arrayResultado['status']    = $this->status['ERROR'];
            $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
        }
        return $arrayResultado;
    }

     /**
     * Funcion que obtiene las cuadrillas activas.
     *
     * @author Wilmer Vera González <wvera@telconet.ec>
     * @version 1.0 15/08/2019
     *
     * @return array $resultado lista de cuadrillas activas
     */
    private function getCuadrillasActivas()
    {
        $arrayResultado        = array();
        $arrayPersonaCuadrilla = array();
        $emComercial                        = $this->getDoctrine()->getManager("telconet");
        try
        {
           
            $arrayPersonaCuadrilla    = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                ->getLideresConCuadrillaAsignada();

            $arrayResultado['cuadrillas']        = $arrayPersonaCuadrilla;
            $arrayResultado['status']            = $this->status['OK'];
            $arrayResultado['mensaje']           = $this->mensaje['OK'];
            
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+',
            'SoporteWSController->getCuadrillasActivas()',
            'Error al obtener la información de cuadrillas activas. '.$e->getMessage(),
            $arrayData['user'],
            "127.0.0.1" );

            $arrayResultado['status']    = $this->status['ERROR'];
            $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            $arrayResultado['data']      = array();
            return $arrayResultado;
        }
        return $arrayResultado;
    }
    
    /**
     * Funcion que sirve para invocar al service de finalizar tarea
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 21-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 29-08-2017 -  Se envia el parametro 'strEnviaDepartamento' para identificar que no se envia el departamento que finaliza la tarea
     *
     * 
     * @author Modificado: Ronny Morán <rmoranc@telconet.ec>
     * @version 1.3 06-10-2020 -  Se agrega el parámetros para identificar 
     *                            el motivo con el cual finaliza la tarea.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.4 03-03-2021 -  Se agrega lógica para sobrescribir variable $intIdDetalle
     *                            segun el idComunicacion enviado.
     * 
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.5 08-11-2021 -  Se agrega parametro strMovil en arreglo arrayParametros
     *                            para validacion de tarea final en el proceso de finalizarTarea
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function putFinalizarTarea($arrayData)
    {
      
        $mensaje = "No se pudo Finalizar la Tarea";
        try
        {
            $codEmpresa         = $arrayData['data']['codEmpresa'];
            $idCaso             = $arrayData['data']['idCaso'];
            $esSolucion         = $arrayData['data']['tarea']['esSolucion'];
            $intIdDetalle       = $arrayData['data']['tarea']['idTarea'];
            $json               = $arrayData['data']['tarea']['materiales'];
            $tiempoTotal        = $arrayData['data']['tarea']['tiempoTotalTarea'];
            $fechaEjecucion     = $arrayData['data']['tarea']['fechaInicial'];
            $horaEjecucion      = $arrayData['data']['tarea']['horaInicial'];
            $observacion        = $arrayData['data']['tarea']['observacion'];
            $fechaCierre        = $arrayData['data']['tarea']['fechaCierre'];
            $horaCierre         = $arrayData['data']['tarea']['horaCierre'];
            $tarea              = $arrayData['data']['tarea']['idTareaFinal'];
            $intMotivoTarea     = $arrayData['data']['tarea']['idMotivoFinaliza'];
            $intComunicacionId  = $arrayData['data']['tarea']['idComunicacion'];
            
            $intIdMotivoFinCaso = $arrayData['data']['tarea']['idMotivoFinCaso'];
            $strEsHal           = $arrayData['data']['tarea']['esHal'];
            
            $usrCreacion        = $arrayData['user'];
            $strIpCreacion      = $arrayData['userIp']!=null?$arrayData['userIp']:"127.0.0.1";
            $prefijoEmpresa     = $arrayData['data']['prefijoEmpresa'];
            
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");
            $objServiceUtil     = $this->get('schema.Util');
            

            //obtener los datos y departamento de la persona por empresa
            $datosUsuario = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaDepartamentoPorUserEmpresa($usrCreacion, $codEmpresa);

            if(isset($intComunicacionId) && !empty($intComunicacionId))
            {
                $objInfoComunicacion = $emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                      ->find($intComunicacionId);

                if(is_object($objInfoComunicacion))
                {
                    $intIdDetalle = $objInfoComunicacion->getDetalleId();
                }
            }

            $empleado   = $datosUsuario['NOMBRES']." ".$datosUsuario['APELLIDOS'];
            $idAsignado = $datosUsuario['ID_PERSONA'];

            $arrayParametros = array(
                                        'idEmpresa'             => $codEmpresa,
                                        'prefijoEmpresa'        => $prefijoEmpresa,
                                        'idCaso'                => $idCaso,
                                        'idDetalle'             => $intIdDetalle,
                                        'tarea'                 => $tarea,
                                        'tiempoTotal'           => $tiempoTotal,
                                        'fechaCierre'           => $fechaCierre,
                                        'horaCierre'            => $horaCierre,
                                        'fechaEjecucion'        => $fechaEjecucion,
                                        'horaEjecucion'         => $horaEjecucion,
                                        'esSolucion'            => $esSolucion,
                                        'fechaApertura'         => "",
                                        'horaApertura'          => "",
                                        'jsonMateriales'        => $json,
                                        'idAsignado'            => $idAsignado,
                                        'observacion'           => $observacion,
                                        'empleado'              => $empleado,
                                        'usrCreacion'           => $usrCreacion,
                                        'ipCreacion'            => $strIpCreacion,
                                        'strEnviaDepartamento'  => "N",
                                        'idMotivoFinaliza'      => $intMotivoTarea,
                                        'numeroTarea'           => $intComunicacionId,
                                        'idMotivoFinCaso'       => $intIdMotivoFinCaso,
                                        'esHal'                 => $strEsHal,
                                        'strMovil'              => "S"
                                    );
                                   
            /* @var $ingresarSeguimiento SoporteService */
            $ingresarSeguimiento = $this->get('soporte.SoporteService');
            //---------------------------------------------------------------------*/

            //--se guarda log de request
            $objServiceUtil->insertLog(array(
                                    'enterpriseCode'   => "10",
                                    'logType'          => 1,
                                    'logOrigin'        => 'TELCOS',
                                    'application'      => 'Movil',
                                    'appClass'         => 'SoporteWSController',
                                    'appMethod'        => 'putFinalizarTarea',
                                    'descriptionError' => 'Seguimiento-tareas-hal',
                                    'status'           => 'Seguimiento',
                                    'inParameters'     => json_encode($arrayParametros),
                                    'creationUser'     => $usrCreacion));

            //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
            $respuestaArray = $ingresarSeguimiento->finalizarTarea($arrayParametros);
            //----------------------------------------------------------------------*/
            
            $status = $respuestaArray['status'];
            
            if($status != "OK")
            {
                $mensaje = $respuestaArray['mensaje'];
                throw new \Exception("ERROR_PARCIAL");
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            $objServiceUtil->insertLog(array(
                'enterpriseCode'   => $arrayData['data']['codEmpresa'],
                'logType'          =>  1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'putFinalizarTarea',
                'descriptionError' => $e->getMessage(),
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $arrayData['user']));

            return $resultado;
        }
        
        $resultado['resultado'] = $respuestaArray['mensaje'];
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
   /**
    * Función que sirve para crear un caso.
    *
    * @param array $arrayData
    * @return array $arrayResultado
    */
    private function putCrearCasoAPP($arrayData)
    {
       $arrayResultado = array();
       $arrayParametro = array();
       try
       {
           $this->validarPersonaId($arrayData);
           $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
           $emComercial        = $this->getDoctrine()->getManager("telconet");
           $objTipoCaso        = $emSoporte->getRepository('schemaBundle:AdmiTipoCaso')
                                           ->findOneByNombreTipoCaso('Tecnico');
           $objFormaContacto   = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                             ->findOneByDescripcionFormaContacto('Correo Electronico');
           // Nivel de criticidad - Alto.
           $objNivelCriticida  = $emSoporte->getRepository('schemaBundle:AdmiNivelCriticidad')
                                           ->findOneById(1);
           // Obtengo el sintoma general que se crea por crear un caso desde la app móvil.
           $objSintoma         = $emSoporte->getRepository('schemaBundle:AdmiSintoma')
                                           ->findOneByNombreSintoma('Caso creado por el cliente desde la App Móvil.');
           //Obtener la fecha de creación del caso.
           $strTimeFechaCaso   = new \DateTime('now');
           $strDateFecha       = $strTimeFechaCaso->format('d-m-Y');
           $strDateHora           = $strTimeFechaCaso->format('H:i');

           $arrayParametro = array(
                                   'objTipoCaso'           => $objTipoCaso,
                                   'tipoNotificacionId'    => $objFormaContacto->getId(),
                                   'nivelCriticidadId'     => $objNivelCriticida, //Alto
                                   'tipoAfectacion'        => $arrayData['data']['tipoAfectacion'],
                                   'tituloIni'             => $arrayData['data']['asunto'],
                                   'versionIni'            => $arrayData['data']['descripcion'],
                                   'idSintoma'             => $objSintoma->getId(),
                                   'feApertura'            => $strDateFecha,
                                   'horaApertura'          => $strDateHora,
                                   'idPunto'               => $arrayData['data']['idPunto'],
                                   'idPersona'             => $arrayData['data']['idPersona'],
                                   'idCanton'              => $arrayData['data']['idCanton'],
                                   'imei'                  => $arrayData['data']['imei'],
                                   'login'                 => $arrayData['data']['login'],
                                   'razonSocial'           => $arrayData['data']['razonSocial'],
                                   'idEmpresa'             => $arrayData['data']['codEmpresa'],
                                   'origen'                => $arrayData['data']['origen'],
                                   'usrCreacion'           => $arrayData['user'],
                                   'ipCreacion'            => $arrayData['ipCreacion']
                                   );
           /* @var $ingresarSeguimiento SoporteService */
           $serviceSoporte = $this->get('soporte.SoporteService');
           $arrayResultado = $serviceSoporte->crearCasoAPPCliente($arrayParametro);
       }
       catch(\Exception $e)
       {
           if($e->getMessage() == "NULL")
           {
               $arrayResultado['status']    = $this->status['NULL'];
               $arrayResultado['mensaje']   = $this->mensaje['NULL'];
           }
           else if($e->getMessage() == "ERROR_PARCIAL")
           {
               $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
               $arrayResultado['mensaje']   = $mensaje;
           }
           else
           {
               $arrayResultado['status']    = $this->status['ERROR'];
               $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
           }
       }
       return $arrayResultado;
    }

    /**
     * Funcion que sirve para cerrar el caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 22-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function putCerrarCaso($arrayData)
    {
        try
        {
            $codEmpresa             = $arrayData['data']['codEmpresa'];
            $prefijoEmpresa         = $arrayData['data']['prefijoEmpresa'];
            $idCaso                 = $arrayData['data']['caso']['idCaso'];
            $fechaCierre            = $arrayData['data']['caso']['fechaCierre'];
            $horaCierre             = $arrayData['data']['caso']['horaCierre'];
            $tituloFinalHipotesis   = $arrayData['data']['caso']['idHipotesisFinal'];
            $versionFinal           = $arrayData['data']['caso']['versionFinal'];
            $tiempoTotalSolucion    = $arrayData['data']['caso']['tiempoTotalCaso'];
            $usrCreacion            = $arrayData['user'];
            $ipCreacion             = "127.0.0.0";
            $emComercial            = $this->getDoctrine()->getManager("telconet");
            $emSoporte              = $this->getDoctrine()->getManager("telconet_soporte");
            
            //validar si se puede cerrar caso
            $numTareasAbiertas = $emSoporte->getRepository('schemaBundle:InfoCaso')->getCountTareasAbiertas($idCaso,'Abiertas');
            $numTareasSolucion = $emSoporte->getRepository('schemaBundle:InfoCaso')->getCountTareasAbiertas($idCaso,'FinalizadasSolucion');
            
            if($numTareasAbiertas == 0 && $numTareasSolucion > 0)
            {
                //obtener los datos y departamento de la persona por empresa
                $datosUsuario = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getPersonaDepartamentoPorUserEmpresa($usrCreacion, $codEmpresa);

                $empleado       = $datosUsuario['NOMBRES']." ".$datosUsuario['APELLIDOS'];
                $idEmpleado     = $datosUsuario['ID_PERSONA'];
                $idDepartamento = $datosUsuario['ID_DEPARTAMENTO'];

                $arrayParametros = array(
                                            'idEmpresa'             => $codEmpresa,
                                            'prefijoEmpresa'        => $prefijoEmpresa,
                                            'idCaso'                => $idCaso,
                                            'fechaCierre'           => $fechaCierre,
                                            'horaCierre'            => $horaCierre,
                                            'tituloFinalHipotesis'  => $tituloFinalHipotesis,
                                            'versionFinalHipotesis' => $versionFinal,
                                            'tiempoTotalCaso'       => $tiempoTotalSolucion,
                                            'usrCreacion'           => $usrCreacion,
                                            'ipCreacion'            => $ipCreacion,
                                            'idDepartamento'        => $idDepartamento,
                                            'idEmpleado'            => $idEmpleado,
                                            'empleado'              => $empleado
                                        );

                /* @var $ingresarSeguimiento SoporteService */
                $soporteService = $this->get('soporte.SoporteService');
                //---------------------------------------------------------------------*/

                //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
                $respuestaArray = $soporteService->cerrarCaso($arrayParametros);

                $status = $respuestaArray['status'];

                if($status != "OK")
                {
                    $mensaje = $respuestaArray['mensaje'];
                    throw new \Exception("ERROR_PARCIAL");
                }
                //----------------------------------------------------------------------*/
            }
            else
            {
                $mensaje = "No se puede cerrar el Caso, Aún existen Tareas Abiertas!";
                throw new \Exception("ERROR_PARCIAL");
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $resultado;
        }
        
        $resultado['resultado'] = $respuestaArray['mensaje'];
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para grabar el acta de entrega para un caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 24-07-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Para las visitas que realizan los tecnicos a los clientes TN-MD se procedera a realizar una factura
     *                           si el tecnico asi lo considere, generamos el acta de entrega de soporte para las empresas TN-MD, se envia
     *                           la cantidad de horas de la visita tecnica.
     *
     * @author Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.3 25/10/2018 - Se agrega parametro de idDetalle para la realación del documento.
     *  
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.4
     * @since 13-11-2018
     * Se agrega el parámetro idDetalle.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.5 02/06/2020 - Se modifica código para crear nueva estructura de archivos.
     * 
     * @author Wilmer Vera González <wvera@telconet.ec>
     * @version 1.6 06/07/2020 - Se agrega data de latencia.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.7 11/11/2020 - El pdf generado se guardará en el servivor NFS remoto.
     *
     * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
     * @version 1.8 08/02/2022 - Se agrega lógica para que guarde log en caso de error.
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.9 20/08/2022 - Se agrega lógica para tomar el idPunto en caso de no venir el idServicio.
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function putActaEntregaSoporte($arrayData)
    {
        $mensaje    = "";
        $start      = $this->get('request')->query->get('start');
        $limit      = 5;
        $serviceUtil        = $this->get('schema.Util'); 
        $emFinan            = $this->getDoctrine()->getManager("telconet_financiero");
        $strCodigoPostal    = '593';
        $strOrigenAccion    = 'tareas';
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $emComercial = $this->get('doctrine')->getManager('telconet');

        try
        {
            $firmaCliente       = $arrayData['data']['actaFirmaCliente'];
            $firmaEmpleado      = $arrayData['data']['actaFirmaEmpleado'];
            $preguntaRespuesta  = $arrayData['data']['actaResultado'];
            $intIdDetalle       = $arrayData['data']['idDetalle'];
            $idEmpresa          = $arrayData['data']['codEmpresa'];
            $prefijoEmpresa     = $arrayData['data']['prefijoEmpresa'];
            $idCaso             = $arrayData['data']['idCaso'];
            $idServicio         = $arrayData['data']['idServicio'];
            
            //1.6
            $intLatenciaMedia      = $arrayData['data']['latencia']['latenciaMedia'];
            $intPaquetesEnviados   = $arrayData['data']['latencia']['paquetesEnviados'];
            $intPaquetesRecibidos  = $arrayData['data']['latencia']['paquetesRecibidos'];
            $boolStatusPing        = $arrayData['data']['latencia']['statusPing'];
            
            $boolFacturable     = (empty($arrayData['data']['facturable']) ? false : $arrayData['data']['facturable']) ;
            $floatHorasFactura  = (empty($arrayData['data']['horasFacturable']) ? 0 : $arrayData['data']['horasFacturable']) ;
            $usrCreacion        = $arrayData['user'];
            $ipCreacion         = "127.0.0.1";
            $feCreacion         = new \DateTime('now');            
            $serverRoot         = $_SERVER['DOCUMENT_ROOT'];
            $strUnidadLatencia  = "";
            
            $arrayParametroUnidadLat = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('UNIDADES_CONFIRMAR_ENLACE', 
                        '', 
                        '', 
                        '', 
                        'UNIDAD_LATENCIA_ENLACE', 
                        '', 
                        '', 
                        ''
                    );

            if (is_array($arrayParametroUnidadLat))
            {
                $strUnidadLatencia = !empty($arrayParametroUnidadLat['valor2']) ? $arrayParametroUnidadLat['valor2'] : "";
            }
            
            $finder = new Finder();
            $finder->files()->in(__DIR__);

            foreach($finder as $file)
            {
                if(strpos($file->getRealpath(), "SoporteWS") !== false)
                {
                    $pathSrc = explode("/WebService/SoporteWSController.php", $file->getRealpath())[0];
                    $pathSrc = explode("\WebService\SoporteWSController.php", $pathSrc)[0];
                }
            }

            if(isset($arrayData['bandNfs']) && $arrayData['bandNfs'])
            {
                $strAplicacion = $arrayData['strFolderApplication'];
            }
            else
            {
                $arrayParametrosFilePath = array(
                                                    'strCodigoPostal'       => $strCodigoPostal,
                                                    'strPrefijoEmpresa'     => $prefijoEmpresa,
                                                    'strFolderApplication'  => $arrayData['strFolderApplication'],
                                                    'strController'         => 'Soporte',
                                                    'strOrigenAccion'       => $strOrigenAccion,
                                                    'strExt'                => ''
                                                );

                $strRutaFisicaCompleta = $serviceUtil->createNewFilePath($arrayParametrosFilePath);
            }
            
            if(empty($idServicio) && !empty($arrayData['data']['idPunto']))
            {
                $arrayResultadoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                            ->findServiciosPorEmpresaPorPuntoIdPorEstado($idEmpresa, $arrayData['data']['idPunto'], 'Activo');
                
                $arrayDatos = $arrayResultadoServicio['registros'];
                foreach ($arrayDatos as $dato):
                    $idServicio     = $dato->getId();
                endforeach;
            }
            
            $arrayParametros = array(
                                    'idEmpresa'             => $idEmpresa,
                                    'prefijoEmpresa'        => $prefijoEmpresa,
                                    'idCaso'                => $idCaso,
                                    'idDetalle'             => $intIdDetalle,
                                    'idServicio'            => $idServicio,
                                    'firmaCoordenadas'      => "",
                                    'firmaClienteCoord'     => "",
                                    'firmaEmpleadoCoord'    => "",
                                    'firmaCliente64'        => $firmaCliente,
                                    'firmaEmpleado64'       => $firmaEmpleado,
                                    'preguntaRespuesta'     => $preguntaRespuesta,
                                    'serverRoot'            => $serverRoot,
                                    'usrCreacion'           => $usrCreacion,
                                    'ipCreacion'            => $ipCreacion,
                                    'feCreacion'            => $feCreacion,
                                    'pathSource'            => $pathSrc,
                                    'facturable'            => $boolFacturable,
                                    'horasFactura'          => $floatHorasFactura,
                                    'start'                 => $start,
                                    'limit'                 => $limit,
                                    'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                    'latenciaMedia'         => $intLatenciaMedia." ".$strUnidadLatencia,
                                    'paquetesEnviados'      => $intPaquetesEnviados,
                                    'paquetesRecibidos'     => $intPaquetesRecibidos,
                                    'statusPing'            => $boolStatusPing,
                                    'strAplicacion'         => $strAplicacion,
                                    'bandNfs'               => $arrayData['bandNfs'],
                                    'strOrigenAccion'       => $strOrigenAccion
                                );
            
            $actaEntregaService = $this->get('tecnico.ActaEntrega');
            $arrResultado       = $actaEntregaService->grabarActaEntregaSoporte($arrayParametros);
            
            if($arrResultado['status']!="OK")
            {
                $mensaje = $arrResultado['mensaje'];
                throw new \Exception("ERROR_PARCIAL");
            }
        }
        catch(\Exception $e)
        {

            $strDescriptionError = $e->getMessage();

            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
                $strDescriptionError    = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }

            $serviceUtil->insertLog(array(
                'enterpriseCode'   => $idEmpresa,
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'putActaEntregaSoporte',
                'descriptionError' => $strDescriptionError,
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $usrCreacion));
            
            return $resultado;
        }
        
        $resultado['resultado'] = $mensaje;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }
    
    /**
     * Funcion que sirve para grabar la encuesta de satisfaccion por un caso
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 3-08-2015
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 12/11/2016 - Se renombra la variable $data a $arrayData en todo el archivo
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 13/06/2017 - Se generaran encuesta de soporte para las empresas MD y TN.
     * 
     * @author Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.3 25/10/2018 - Se agrega parametro de idDetalle para la realación del documento.
     *   
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.4 02/06/2020 - Se modifica código para crear nueva estructura de archivos.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.5 04/09/2021  
     * Se Añaden atributos para el nuevo ISO de actas sugerido por el dep. de calidad.
     * Se obtinen nombres de quienes conforman la cuadrilla enviada.
     * Se agregan parámetros para la generación de la encuesta.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.6 18/11/2021  
     * Se agrega validación a nivel de objetos
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function putEncuestaSoporte($arrayData)
    {
        $mensaje = "";
        
        try
        {
            $firma              = $arrayData['data']['encuestaFirma'];
            $preguntaRespuesta  = $arrayData['data']['encuestaResultado'];
            $idEmpresa          = $arrayData['data']['codEmpresa'];
            $prefijoEmpresa     = $arrayData['data']['prefijoEmpresa'];
            $idCaso             = $arrayData['data']['idCaso'];
            $idServicio         = $arrayData['data']['idServicio'];
            $idDetalle          = $arrayData['data']['idDetalle'];
            $strIdPunto         = $arrayData['data']['idPunto'];
            
            $strJefeCuadrilla    = $arrayData['data']['jefeCuadrilla'];
            $intIdCuadrilla      = $arrayData['data']['idCuadrilla'];
            $strTelfPersonaSitio = $arrayData['data']['telfContactoSitio'];
            
            
            
            $usrCreacion        = $arrayData['user'];
            $ipCreacion         = "127.0.0.1";
            $feCreacion         = new \DateTime('now');            
            $serverRoot         = $_SERVER['DOCUMENT_ROOT'];
            $serviceUtil        = $this->get('schema.Util'); 
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $emFinan            = $this->getDoctrine()->getManager("telconet_financiero");
            $strCodigoPostal    = '593';
            $strOrigenAccion    = 'tareas';
        
            $finder = new Finder();
            $finder->files()->in(__DIR__);

            foreach($finder as $file)
            {
                if(strpos($file->getRealpath(), "SoporteWS") !== false)
                {
                    $pathSrc = explode("/WebService/SoporteWSController.php", $file->getRealpath())[0];
                    $pathSrc = explode("\WebService\SoporteWSController.php", $pathSrc)[0];
                }
            }
            //Obtenemos el codigo de la plantilla para poder generar encuesta
            $arrayAdmiParametroEncuesta = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('CODIGO_ENCUESTA_VISITA_POR_EMPRESA',
                                                               'SOPORTE',
                                                               '',
                                                               '',
                                                               'CODIGO_ENCUESTA_VISITA',
                                                               '',
                                                               '',
                                                               '',
                                                               '',
                                                               $idEmpresa
                                                              );
            if (isset($arrayAdmiParametroEncuesta['valor2']) && !empty($arrayAdmiParametroEncuesta['valor2']))
            {
                $strCodigoPlantilla = $arrayAdmiParametroEncuesta['valor2'];
            }

            if(isset($arrayData['bandNfs']) && $arrayData['bandNfs'])
            {
                $strAplicacion = $arrayData['strFolderApplication'];
            }
            else
            {
                $arrayParametrosFilePath = array(
                    'strCodigoPostal'       => $strCodigoPostal,
                    'strPrefijoEmpresa'     => $prefijoEmpresa,
                    'strFolderApplication'  => $arrayData['strFolderApplication'],
                    'strController'         => 'Soporte',
                    'strOrigenAccion'       => $strOrigenAccion,
                    'strExt'                => ''
                );

                $strRutaFisicaCompleta = $serviceUtil->createNewFilePath($arrayParametrosFilePath);
            }
            //Obtener  nombre de quienes estan en la cuadrilla enviada 
            $arrayCargos    = array();
            $emComercial    = $this->getDoctrine()->getManager("telconet");
            $objCargos      = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->get('CARGOS AREA TECNICA', 
                                                            '', 
                                                            '', 
                                                            '', 
                                                            'Personal Tecnico', 
                                                            '',
                                                            '', 
                                                            ''
                                                            );
            if(is_object($objCargos))
            {
                foreach($objCargos as $objCargoTecnico)
                {
                    $arrayCargos[] = $objCargoTecnico['descripcion'];

                }
            }
            $arrayParametrosIntegrantesCuadrilla['criterios']['cargoSimilar']   = $arrayCargos;
            $arrayParametrosIntegrantesCuadrilla['intIdCuadrilla']              = $intIdCuadrilla;
            $arrayParametrosIntegrantesCuadrilla['empresa']                     = $idEmpresa;
            $arrayTmpPersonasCuadrilla = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                     ->findPersonalByCriterios($arrayParametrosIntegrantesCuadrilla);
            $objAdmiCuadrilla           = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdCuadrilla);

            $arrayRegistrosPersonasCuadrilla    = $arrayTmpPersonasCuadrilla['registros'];  
            $strIntegrantesCuadrilla = "";
            if( $arrayRegistrosPersonasCuadrilla )
            {
                foreach ($arrayRegistrosPersonasCuadrilla as $arrayDatosIntegrante)
                {
                    $strNombresApellidosIntegrante  = ucwords(strtolower(trim($arrayDatosIntegrante['nombres'])))
                    .' '.
                    ucwords(strtolower(trim($arrayDatosIntegrante['apellidos'])));
                    $strIntegrantesCuadrilla = $strIntegrantesCuadrilla.
                    " - ".$strNombresApellidosIntegrante;                                                 
                    
                }
            }

            if(empty($idServicio) && !empty($arrayData['data']['idPunto']))
            {
                $arrayResultadoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                            ->findServiciosPorEmpresaPorPuntoIdPorEstado($idEmpresa, $arrayData['data']['idPunto'], 'Activo');
                
                $arrayDatos = $arrayResultadoServicio['registros'];
                foreach ($arrayDatos as $dato):
                    $idServicio     = $dato->getId();
                endforeach;
            }
            
            //Obteniendo Fecha de inicio de contrato. 
            $objTmpAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                         ->findOneBy(array("servicioId" => $idServicio));
            
            $strFecha = "N/A";

            if($objTmpAdendum != null && $objTmpAdendum->getContratoId() != null)
            {
                $entityContrato = $emComercial->getRepository('schemaBundle:InfoContrato')
                                            ->findOneBy(array("id" => $objTmpAdendum->getContratoId(),
                                                              "estado"=> 'Activo'));
                
                if(is_object($entityContrato))
                {
                    $strFecha = $entityContrato->getFeAprobacion()!=null?$entityContrato->getFeAprobacion()->format('d/m/Y H:i:s') : 'N/A';
                }
            }
            else
            {
                $objTmpAdendum = $emComercial->getRepository('schemaBundle:InfoAdendum')
                                            ->findOneBy(array("puntoId" => $strIdPunto,
                                                              "estado"=> 'Activo'));

                if($objTmpAdendum !=null &&  $objTmpAdendum->getContratoId() != null)
                {
                    $entityContrato = $emComercial->getRepository('schemaBundle:InfoContrato')
                                            ->findOneBy(array("id" => $objTmpAdendum->getContratoId(),
                                                              "estado"=> 'Activo'));
                    if(is_object($entityContrato))
                    {
                        $strFecha = $entityContrato->getFeAprobacion()!=null?$entityContrato->getFeAprobacion()->format('d/m/Y H:i:s') : 'N/A';    
                    }
                    
                }
                else
                {
                    $strFecha = "N/A";
                }
                
            }
            
            $objAdmiCuadrilla           = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdCuadrilla);

            $arrayParametros = array(
                                        'idEmpresa'             => $idEmpresa,
                                        'prefijoEmpresa'        => $prefijoEmpresa,
                                        'idServicio'            => $idServicio,
                                        'idDetalle'             => $idDetalle,
                                        'idCaso'                => $idCaso,
                                        'firmaCoordenadas'      => "",
                                        'firmaBase64'           => $firma,
                                        'preguntaRespuesta'     => $preguntaRespuesta,
                                        'strCodigoPlantilla'    => $strCodigoPlantilla,
                                        'serverRoot'            => $serverRoot,
                                        'usrCreacion'           => $usrCreacion,
                                        'ipCreacion'            => $ipCreacion,
                                        'feCreacion'            => $feCreacion,
                                        'pathSource'            => $pathSrc,
                                        'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                        'strAplicacion'         => $strAplicacion,
                                        'bandNfs'               => $arrayData['bandNfs'],
                                        'strOrigenAccion'       => $strOrigenAccion,
                                        'strIntegrantesCuadrilla'=> $strIntegrantesCuadrilla,
                                        'strNombreCuadrilla'     => $objAdmiCuadrilla->getNombreCuadrilla(),
                                        'strJefeCuadrilla'       => $strJefeCuadrilla,
                                        'fechaContrato'          => $strFecha,
                                        'strTelfPersonaSitio'  => $strTelfPersonaSitio
                                    );
            
            $encuestaService  = $this->get('tecnico.Encuesta');
            $arrayResultado   = $encuestaService->grabarEncuestaSoporte($arrayParametros);
            
            if($arrayResultado['status']!="OK")
            {
                $mensaje = $arrayResultado['mensaje'];
                throw new \Exception("ERROR_PARCIAL");
            }
            
            $mensaje = "Se creo la encuesta correctamente!";
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $resultado['status']    = $this->status['NULL'];
                $resultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $resultado['status']    = $this->status['ERROR_PARCIAL'];
                $resultado['mensaje']   = $mensaje;
            }
            else
            {
                $resultado['status']    = $this->status['ERROR'];
                $resultado['mensaje']   = $this->mensaje['ERROR'];
            }

            $serviceUtil->insertLog(array(
                'enterpriseCode'   => "10",
                'logType'          => 0,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'putEncuestaSoporte',
                'descriptionError' => $e->getMessage(),
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $usrCreacion));

            return $resultado;
        }
        
        $resultado['resultado'] = $mensaje;
        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $this->mensaje['OK'];
        return $resultado;
    }

    /**
     * Funcion que sirve para cambiar el estado de una tarea
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 11-11-2016
     * 
     * @author modificado Richard Cabrera <wgaibor@telconet.ec>
     * @version 1.1 20/04/2017 - En la invocación de la función administrarTarea se agrega el parametro intPersonaEmpresaRol
     *
     * @author modificado Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 28/02/2018 - Se envia el id departamento al momento de cambiar el estado de la tarea.
     * 
     * @author modificado Néstor Naula López <nnaulal@telconet.ec>
     * @version 1.3 28/06/2018 - Se valida que cambie el estado solo cuando sea diferente al estado finalizado
     *
     * @author modificado Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.4 24/04/2019 - Se agrega el parámetro *idDepartamento*, que identifica el departamento del técnico en sesión.
     *
     * @author modificado Jeamopier Carriel <jcarriel@telconet.ec>
     * @version 1.5 03/04/2023 - Se setea parametro Empresa cuando venga vacia.
     *
     * @param array $arrayData
     * @return array $arrayRespuesta
     */
    private function putCambiarEstadoTarea($arrayData)
    {
        try
        {
            $emSoporte              = $this->getDoctrine()->getManager("telconet_soporte");
            $soporteService        = $this->get('soporte.SoporteService');
            $arrayObtenerResultado = array("intIdTarea" =>$arrayData['data']['idTarea']);
            $strEstado             = $soporteService->obtenerUltimoEstadoTarea($arrayObtenerResultado);
            if(isset($strEstado) && (strcmp($strEstado, 'Finalizada') == 0))
            {
                $arrayRespuesta['status']    = $this->status['ERROR_PARCIAL'];
                $arrayRespuesta['mensaje']   = 'La tarea se encuentra finalizada, por favor verificarlo con su coordinador o jefe departamental';
            }else{
           
                $emComercial                             = $this->getDoctrine()->getManager("telconet");
                $objDetalle                              = $emSoporte->getRepository('schemaBundle:InfoDetalle')->find($arrayData['data']['idTarea']);
                $arrayParametros['strTipo']              = $arrayData['data']['tipo'];
                $arrayParametros['objDetalle']           = $objDetalle;
                $arrayParametros['strObservacion']       = $arrayData['data']['observacion'];
                $arrayParametros['strCodEmpresa']        = ($arrayData['data']['codEmpresa']) ? $arrayData['data']['codEmpresa'] : "18";
                $arrayParametros['strUser']              = $arrayData['user'];
                $arrayParametros['strIpUser']            = "127.0.0.1";  
                $arrayParametros['intIdDepartamento']    = $arrayData['data']['idDepartamento'];
                $arrayParametros["intPersonaEmpresaRol"] = 0;
                //obtener los datos y departamento de la persona por empresa
                $datosUsuario                            = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                       ->getPersonaDepartamentoPorUserEmpresa($arrayData['user'], "10");
                $arrayParametros["idDepartamento"]       = $datosUsuario['ID_DEPARTAMENTO'];


                $soporteService                     = $this->get('soporte.SoporteService');
                $arrayRespuesta                     = $soporteService->administrarTarea($arrayParametros);
                if(isset($arrayRespuesta["strRespuesta"]) && $arrayRespuesta["strRespuesta"] == "OK")
                {
                    $arrayRespuesta['status']    = $this->status['OK'];
                    $arrayRespuesta['mensaje']   = 'Se han aplicado los cambios Exitosamente!!!';
                }
                else
                {
                    $arrayRespuesta['status']    = $this->status['ERROR'];
                    $arrayRespuesta['mensaje']   = 'No Se han aplicarón los cambios!!!';
                }
            
            }
        }
        catch(\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayRespuesta['status']    = $this->status['NULL'];
                $arrayRespuesta['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayRespuesta['status']    = $this->status['ERROR_PARCIAL'];
                $arrayRespuesta['mensaje']   = $mensaje;
            }
            else
            {
                $arrayRespuesta['status']    = $this->status['ERROR'];
                $arrayRespuesta['mensaje']   = $this->mensaje['ERROR'];
            }
        }        
        return $arrayRespuesta;
    }
    
    /**
     * Obtener ultimo estado de la tarea
     * 
     * @author modificado Néstor Naula López <nnaulal@telconet.ec>
     * @version 1.1 29/06/2018 - Se obtiene el ultimo estado de la tarea
     * 
     * @param array $arrayData
     * @return array $arrayRespuesta
     */
    private function getUltimoEstadoServicioTarea($arrayData)
    {
        try
        {
            $soporteService        = $this->get('soporte.SoporteService');
            $arrayObtenerResultado = array("intIdTarea" =>$arrayData['data']['idTarea']);
            $strEstado             = $soporteService->obtenerUltimoEstadoTarea($arrayObtenerResultado);
            if(isset($strEstado) && (strcmp($strEstado, 'Finalizada') == 0))
            {
                $arrayRespuesta['status']    = $this->status['ERROR_PARCIAL'];
                $arrayRespuesta['mensaje']   = 'La tarea se encuentra finalizada, por favor verificarlo con su coordinador o jefe departamental';
            }else{
                $arrayRespuesta['status']    = $this->status['OK'];
                $arrayRespuesta['mensaje']   = 'La tarea se encuentra finalizada, por favor verificarlo con su coordinador o jefe departamental';
            }
        
        } catch (\Exception $e)
        {
            if($e->getMessage() == "NULL")
            {
                $arrayRespuesta['status']    = $this->status['NULL'];
                $arrayRespuesta['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayRespuesta['status']    = $this->status['ERROR_PARCIAL'];
                $arrayRespuesta['mensaje']   = $mensaje;
            }
            else
            {
                $arrayRespuesta['status']    = $this->status['ERROR'];
                $arrayRespuesta['mensaje']   = $this->mensaje['ERROR'];
            }
        }
        return $arrayRespuesta;
    }
    
    
    /**
     * Funcion que retorna la informacion del cliente y caja en base a un id_caso
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 22-05-2017
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 13-06-2017 - Retorno en la variable $arrayResultado el idServicio.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 26-02-2018 - Se recupera el idPunto.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 27-02-2018 - Se recupera la informacion del cliente solo cuando el tipo afectado sea un cliente.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.4 13-06-2019 - Se recupera 'idElement' para lógica en TM-OPERACIONES.
     * @since 1.3
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.5 04-02-2021 - Se valida variable $objElementoUbica para verificar si es un objeto.
     * 
     *  
     * @param array $arrayParametros ['intIdCaso'] : id del casos
     * @return array $arrayResultado
     *   
     */
    private function getInformacionClienteCajaCaso($arrayParametros)
    {
        $emComercial              = $this->getDoctrine()->getManager("telconet");
        $emSoporte                = $this->getDoctrine()->getManager("telconet_soporte");
        $emInfraestructura        = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayResultado           = array();
        $arrayCliente             = array();
        $arrayBackbone            = array();
        $intServicioId            = 0;
        $strEstado                = "";
        try
        {
            //buscar detalle hipotesis
            $detallesHipotesis = $emSoporte->getRepository('schemaBundle:InfoCaso')->getDetalleHipotesisPorCaso($arrayParametros['intIdCaso']);
            // Obtener información del cliente.
            foreach($detallesHipotesis as $detalleHipotesisMobil)
            {
                //obtengo informacion del punto.
                $arrayParametrosPunto['intDetalleHipotesisId'] = $detalleHipotesisMobil['idDetalleHipotesis'];
                $arrayParametrosPunto['strTipoAfectado']       = "Cliente";
                $arrayInformacionPunto                         = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                                             ->getPuntoPorDetalleHipotesis($arrayParametrosPunto);
                foreach($arrayInformacionPunto as $informacionPunto)
                {
                    $arrayParametrosPersona['intPersonaEmpresaRolId'] = $informacionPunto->getPersonaEmpresaRolId();
                    $strNombreCliente                                 = '';
                    if(is_object($informacionPunto->getPersonaEmpresaRolId()))
                    {
                        if(is_object($informacionPunto->getPersonaEmpresaRolId()->getPersonaId()))
                        {
                            //Obtengo el nombre del cliente en base a un punto.
                            $strNombreCliente = $informacionPunto->getPersonaEmpresaRolId()->getPersonaId()->__toString();
                        }
                    }
                    $arrayCliente[] = array(
                                            'idPunto'       => $informacionPunto->getId(),
                                            'nombreCliente' => $strNombreCliente,
                                            'login'         => $informacionPunto->getLogin(),
                                            'direccion'     => $informacionPunto->getDireccion(),
                                            'descripcion'   => $informacionPunto->getDescripcionPunto(),
                                            'latCliente'    => $informacionPunto->getLatitud(),
                                            'lonCliente'    => $informacionPunto->getLongitud());
                }
            }
            // Obtener Informacion de la caja
            $arrayParametrosDetalles['intIdCaso']       = $arrayParametros['intIdCaso'];
            $arrayParametrosDetalles['strTipoAfectado'] = 'Servicio';
            $arrayDetalleInicial                        = $emSoporte->getRepository('schemaBundle:InfoParteAfectada')
                                                                    ->getDetalleInicialCasoXTipoAfectado($arrayParametrosDetalles);
            if($arrayDetalleInicial[0]["detalleInicial"])
            {
                $intServicioId = $arrayDetalleInicial[0]["detalleInicial"];
                $objServicio   = $emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->findOneById($intServicioId);
                $strEstado     = $objServicio->getEstado();
                if($intServicioId)
                {
                    $arrayParametrosServicio['intServicioId'] = $intServicioId;
                    $arrayParametrosServicio['strTipoMedio']  = 'FO';
                    $arrayElemento                            = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                  ->getInfoElementoPorServicioTecnico($arrayParametrosServicio);
                    
                    $arrayBackbone = array();

                    $arrayBackbone[0]['descripcion']    = "";
                    $arrayBackbone[0]['idElemento']     = null;
                    $arrayBackbone[0]['latCaja']        = null;
                    $arrayBackbone[0]['lonCaja']        = null;
                    $arrayBackbone[0]['ubicacionElemento'] = "";

                    foreach($arrayElemento as $parametroElemento)
                    {
                        $objElementoUbica = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                              ->findOneBy(array("elementoId" => $parametroElemento->getId()));

                        $objUbicacion = null;
                        if(is_object($objElementoUbica) && is_object($objElementoUbica->getUbicacionId()))
                        {
                            $objUbicacion     = $emInfraestructura->getRepository('schemaBundle:InfoUbicacion')
                                                                  ->find($objElementoUbica->getUbicacionId()->getId());
                        }
                        
                        if(is_object($objUbicacion)) 
                        {
                            $arrayBackbone[0]['descripcion']    = $parametroElemento->getNombreElemento();
                            $arrayBackbone[0]['idElemento']     = $parametroElemento->getId();
                            $arrayBackbone[0]['latCaja']        = $objUbicacion->getLatitudUbicacion();
                            $arrayBackbone[0]['lonCaja']        = $objUbicacion->getLongitudUbicacion();
                        }

                        $objDetalleElemento = $emComercial->getRepository('schemaBundle:InfoDetalleElemento')
                                ->findOneBy(array( "elementoId"     => $parametroElemento->getId(), 
                                                   "detalleNombre"  => "UBICADO EN", 
                                                   "estado"         => "Activo"));
                        if(is_object($objDetalleElemento))
                        {
                            $arrayBackbone[0]['ubicacionElemento'] = $objDetalleElemento->getDetalleValor();
                        }
                    }
                }
            }

            //Datos por defecto
            $arrayBackbone[0]['hiloMpls']                   = ""; 
            $arrayBackbone[0]['nombreparroquia']            = "";
            $arrayBackbone[0]['nombreCanton']               = "";
            $arrayBackbone[0]['nombreProvincia']            = "";
            $arrayBackbone[0]['fechaActivacion']            = "";
            $arrayBackbone[0]['nombreElementoConector']     = "";
            $arrayBackbone[0]['nombreElemento']             = "";
            $arrayBackbone[0]['nombreInterfaceElemento']    = "";

            $objInfoServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                    ->findOneByServicioId($intServicioId);

            if(is_object($objInfoServicioTecnico))
            {
                $objInterface = $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                ->findOneBy(array("id" => $objInfoServicioTecnico->getInterfaceElementoConectorId()));

                if(is_object($objInterface))
                {
                    $arrayParametrosEnviar = array( 'intIdElemento' => $objInfoServicioTecnico->getElementoConectorId(),
                    'strNombreInterfaceElemento' => $objInterface->getNombreInterfaceElemento(),
                    'intCodigoEmpresa' => $arrayParametros['intCodigoEmpresa']);

                    $arrayHiloMpls = $emComercial->getRepository('schemaBundle:InfoDetalleAsignacion')
                                    ->getHiloPrincipalPorElementoId($arrayParametrosEnviar);

                    if(isset($arrayHiloMpls) && $arrayHiloMpls!=null)
                    {
                     $arrayBackbone[0]['hiloMpls'] = $arrayHiloMpls[0]['colorHilo']; 
                    }                
                }
                
                //Obtener provincia y canton
                $objElementoUbica = $emInfraestructura->getRepository("schemaBundle:InfoEmpresaElementoUbica")
                ->findOneBy(array('elementoId' => $objInfoServicioTecnico->getElementoContenedorId(),
                                  'empresaCod' => $arrayParametros['intCodigoEmpresa']));

                if(is_object($objElementoUbica))
                {
                    $objUbicacion       = $emComercial->getRepository('schemaBundle:InfoUbicacion')
                    ->findOneBy(array("id" => $objElementoUbica->getUbicacionId()->getId()));

                    if(is_object($objUbicacion))
                    {
                        $objParroquia   = $emComercial->getRepository('schemaBundle:AdmiParroquia')
                                        ->findOneBy(array("id" =>$objUbicacion->getParroquiaId()));

                        if(is_object($objParroquia))
                        {
                            $objCanton      = $emComercial->getRepository('schemaBundle:AdmiCanton')
                                            ->findOneBy(array("id" =>$objParroquia->getCantonId()));
                            $arrayBackbone[0]['nombreparroquia']    = $objParroquia->getNombreParroquia(); 

                            if(is_object($objCanton))
                            {
                                $objProvincia   = $emComercial->getRepository('schemaBundle:AdmiProvincia')
                                                ->findOneBy(array("id" =>$objCanton->getProvinciaId()));
                                $arrayBackbone[0]['nombreCanton']       = $objCanton->getNombreCanton(); 

                                if(is_object($objProvincia))
                                {
                                    $arrayBackbone[0]['nombreProvincia']    = $objProvincia->getNombreProvincia();
                                }
                            }
                        }
                    }
                }

                //Obtener splitter
                $objElementoSplitter = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                        ->findOneBy(array("id" => $objInfoServicioTecnico->getElementoConectorId()));
                if(is_object($objElementoSplitter))
                {
                    $arrayBackbone[0]['nombreElementoConector'] = $objElementoSplitter->getNombreElemento();
                }

                //Obtener el switch en caso de TN u olt en caso de MD
                $objElementoSWOLT = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                        ->findOneBy(array("id" => $objInfoServicioTecnico->getElementoId()));

                if(is_object($objElementoSWOLT))
                {
                    $arrayBackbone[0]['nombreElemento'] = $objElementoSWOLT->getNombreElemento();
                }

                $objInterfaceElementoSWOLT = $emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                ->findOneBy(array("id" => $objInfoServicioTecnico->getInterfaceElementoId()));

                if(is_object($objInterfaceElementoSWOLT))
                {
                    $arrayBackbone[0]['nombreInterfaceElemento']    = $objInterfaceElementoSWOLT->getNombreInterfaceElemento();
                }
            }

            //Obtener historial
            $objHistorialServicio = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')
            ->findOneBy(
                        array('servicioId'=> $intServicioId,
                                'estado'    => 'Activo'),
                        array('id' => 'ASC'));

            if(is_object($objHistorialServicio))
            {
                $arrayBackbone[0]['fechaActivacion'] = date_format($objHistorialServicio->getFeCreacion(), "d-m-Y H:i:s");
            }

            $arrayResultado = array(
                                    'arrayCliente'  => $arrayCliente,
                                    'arrayBackbone' => $arrayBackbone,
                                    'idServicio'    => $intServicioId,
                                    'strEstado'     => $strEstado);
                            
        }
        catch(\Exception $ex)
        {
            error_log("Problemas al recuperar la información del metodo SoporteWSController:getInformacionClienteYCajaDelCaso   ".$ex->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * Funcion que retorna el idServicio en base a un Caso de MEGADATOS
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 22-05-2017
     * 
     * @return array $arrayResultado
     */
    private function getIdServicioXCasoMD($arrayParametros)
    {
        $emSoporte              = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        //Obtengo el registro total de afectados por el caso creado.
        $arrayAfectados         = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                            ->getRegistrosAfectadosTotalXCaso($arrayParametros['idCaso'],
                                                                              '',
                                                                              'Data',
                                                                              $arrayParametros['start'],
                                                                              $arrayParametros['limit']);
        // Se obtiene el idPunto para posterior obtener los servicios del punto.
        $intIdPunto             = $arrayAfectados[0]['afectadoId'];
        $arrayParametrosServicio= array(
                                        'intIdPunto'        => $intIdPunto,
                                        'estadosServicios'  => array('Activo', 'In-Corte')
                                        );
        $arrayServicio          = $emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->getServiciosByCriterios($arrayParametrosServicio); 
        $arrayProdInternet      = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                              ->findBy(array("nombreTecnico" =>"INTERNET",
                                                             "empresaCod"    =>$arrayParametros['codEmpresa'],
                                                             "estado"        =>"Activo")); 

        $serviceTecnico         = $this->get('tecnico.InfoServicioTecnico');
        $intIdServicio          = 0;
        $strEstado              = "";
        // Obtengo el idServicio.
        foreach($arrayServicio['registros'] as $servicio)
        {
            $intPlanCabId  = $servicio->getPlanId();
            $objPlanDet    = $emComercial->getRepository('schemaBundle:InfoPlanDet')->findBy(array("planId" => $intPlanCabId));

            $intIndice     = $serviceTecnico->obtenerIndiceInternetEnPlanDet($objPlanDet, $arrayProdInternet);

            if($intIndice != -1)
            {
                $intIdServicio = $servicio->getId();
                $strEstado     = $servicio->getEstado();
                break;
            }
        }

        $arrayResultado = array('idServicio'    => $intIdServicio,
                                'strEstado'     => $strEstado);
        return $arrayResultado;
    }

   /**
     * Funcion que sirve para reasignar una tarea
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 16-08-2017
     *
     * @author Germán Valenzuela<gvalenzuela@telconet.ec>
     * @version 1.1 15-12-2018 - Se agrega el parámetro origenHal para identificar si la reasignación proviene de hal.
     *
     * @author Germán Valenzuela<gvalenzuela@telconet.ec>
     * @version 1.2 24-04-2019 - Se agrega el parámetro clienteReprograma para identificar si
     *                           el cliente solicitó la reprogramación.
     *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function putReasignarTarea($arrayData)
    {
        $soporteService        = $this->get('soporte.SoporteService');
        $intDetalleId          = $arrayData['data']['idDetalle'];
        $arrayObtenerResultado = array("intIdTarea" =>$intDetalleId);
        $strEstado             = $soporteService->obtenerUltimoEstadoTarea($arrayObtenerResultado);
        if(isset($strEstado) && (strcmp($strEstado, 'Finalizada') == 0))
        {
            $arrayRespuesta['status']    = $this->status['ERROR_PARCIAL'];
            $arrayRespuesta['mensaje']   = 'La tarea se encuentra finalizada, por favor verificarlo con su coordinador o jefe departamental';
        }else{     
            $arrayParametros['idEmpresa']             = $arrayData['data']['codEmpresa'];
            $arrayParametros['strOrigenHal']          = $arrayData['data']['origenHal'];
            $arrayParametros['strClienteReprograma']  = $arrayData['data']['clienteReprograma'];
            $arrayParametros['prefijoEmpresa']        = $arrayData['data']['prefijoEmpresa'];
            $arrayParametros['id_detalle']            = $arrayData['data']['idDetalle'];
            $arrayParametros['id_tarea']              = $arrayData['data']['idTarea'];
            $arrayParametros['motivo']                = $arrayData['data']['motivo'];
            $arrayParametros['departamento_asignado'] = $arrayData['data']['idDepartamento'];
            $arrayParametros['empleado_asignado']     = $arrayData['data']['empleadoAsignado'];
            $arrayParametros['cuadrilla_asignada']    = (isset($arrayData['data']['cuadrillaAsignada']) ? $arrayData['data']['cuadrillaAsignada'] : "");
            $arrayParametros['contratista_asignada']  = (isset($arrayData['data']['contratistaAsignada']) ? 
                                                         $arrayData['data']['contratistaAsignada'] : "");
            $arrayParametros['tipo_asignado']         = $arrayData['data']['tipoAsignado'];
            $arrayParametros['fecha_ejecucion']       = $arrayData['data']['fechaEjecucion'];
            $arrayParametros['id_departamento']       = $arrayData['data']['idDepartamento'];
            $arrayParametros['empleado_logueado']     = $arrayData['user'];
            $arrayParametros['clientIp']              = (isset($arrayData['data']['ip']) ? $arrayData['data']['ip'] : "127.0.0.1");
            $arrayParametros['user']                  = $arrayData['user'];
            //cambio la fecha a formato yyyy-mm-dd
            $objSoporteService  = $this->get('soporte.SoporteService');
            $arrayResultado  = $objSoporteService->reasignarTarea($arrayParametros);
            if ($arrayResultado["success"])
            {
                $arrayRespuesta['status']    = $this->status['OK'];
                $arrayRespuesta['mensaje']   = $this->mensaje['OK'];
            }
            else
            {
                $arrayRespuesta['status']    = $this->status['ERROR'];
                $arrayRespuesta['mensaje']   = $this->mensaje['ERROR'];
            }
        }

        return $arrayRespuesta;
    }
    
    
     /**
     * Funcion que sirve para obtener los eventos segun los parametros enviados en el request los cuales comprende los parametros indicados en el 
     * param
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 20-12-2017
     * 
     * @since 1.0
     * 
     * @param array $arrayData [id, idCuadrilla, idDetalle, idPersonaEmpresaRolId, estado ]
     * @return array $resultado [status, mensaje, data]
     */
    private function getEventos($arrayData)
    {
        $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");
        $strMensaje = '';

        try
        {           

            $arrayParametros = array('intId'                    => $arrayData['data']['intId'],
                                     'intCuadrillaId'           => $arrayData['data']['intCuadrillaId'],
                                     'intDetalleId'             => $arrayData['data']['intDetalleId'],
                                     'intPersonaEmpresaRolId'   => $arrayData['data']['intPersonaEmpresaRolId'],
                                     'strEstado'                => $arrayData['data']['strEstado']);


            
            $arrayRespuesta  = $emSoporte->getRepository('schemaBundle:InfoEvento')->getArrayEventos($arrayParametros);
                    
            if(is_array($arrayRespuesta))
            {
                $strMensaje = "Consulta Exitosa!";
            }

        }
        catch(\Exception $e)
        {
            $resultado['status']  = $this->status['ERROR'];
            $resultado['mensaje'] = $this->mensaje['ERROR'];

            return $resultado;
        }

        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $strMensaje;
        $resultado['data']      = $arrayRespuesta;
        
        return $resultado;
    } 
    
    
    
     /**
     * Funcion que obtiene los casos del cliente
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-04-2018
     * 
     * @since 1.0
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getCasosCliente($arrayData)
    {
        $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");
        $strMensaje = '';
        $strStatus  =  $this->status['OK'];

        try
        {           

            $arrayParametros = array('intPuntoId'    =>  $arrayData['data']['idPunto']);

            
            $arrayRespuesta  = $emSoporte->getRepository('schemaBundle:InfoCaso')->getCasosPorPunto($arrayParametros);
                    
            if(is_array($arrayRespuesta))
            {
                $strMensaje = "Consulta Exitosa!";
            }
            else
            {
                $strMensaje = "Vacio";
                $strStatus  =  $this->status['ERROR'];                
            }

        }
        catch(\Exception $e)
        {
            $resultado['status']  = $this->status['ERROR'];
            $resultado['mensaje'] = $this->mensaje['ERROR'];

            return $resultado;
        }

        $resultado['status']    = $strStatus;
        $resultado['mensaje']   = $strMensaje;
        $resultado['data']      = $arrayRespuesta;
        
        return $resultado;
    }  
   
    
     /**
     * Funcion que sirve para obtener los tipos eventos
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 20-12-2017
     * 
     * @since 1.0
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getTipoEvento($arrayData)
    {
        $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {           

            $arrayParametros = array('intId'               => $arrayData['data']['intId'],
                                     'strCodigo'           => $arrayData['data']['strCodigo'],
                                     'strNombre'           => $arrayData['data']['strNombre'],
                                     'strEstado'           => $arrayData['data']['strEstado']);


            
            $arrayRespuesta  = $emSoporte->getRepository('schemaBundle:AdmiTipoEvento')->getArrayTipoEvento($arrayParametros);


            if(is_array($arrayRespuesta))
            {
                $strMensaje = "Consulta Exitosa!";
            }
        }
        catch(\Exception $e)
        {
            $resultado['status']  = $this->status['ERROR'];
            $resultado['mensaje'] = $this->mensaje['ERROR'];
            $resultado['data']    = array();

            return $resultado;
        }

        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $strMensaje;
        $resultado['data']      = $arrayRespuesta;
        
        return $resultado;
    }     
    
     /**
     * Funcion que sirve para obtener todos los eventos registrados, los parametros para filtrar son los indicados en el param
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 20-12-2017
     * 
     * @since 1.0
     * 
     * se añade el parametro usuario para validacion de usuarios sin cuadrilla
     * @author rsalgado <rsalgado@telconet.ec>
     * @version 1.0 02-03-2018
     * @since 1.1
     * 
     * @param array $arrayData  [idCuadrilla, estado, user]
     * @return array $resultado [status, mensaje, data]
     */
    private function getEventosPersona($arrayData)
    {
        $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");
        $serviceUtil = $this->get('schema.Util'); 

        try
        {           

            $arrayParametros = [
                                'intCuadrilla'  => $arrayData['data']['idCuadrilla'],
                                'strEstado'     => $arrayData['data']['estado'],
                                'strUser'       => $arrayData['user'],
                                'objUtilService'   => $serviceUtil 

                               ];


            
            $arrayRespuesta  = $emSoporte->getRepository('schemaBundle:AdmiTipoEvento')->getArrayEventosUser($arrayParametros);
            

            if(count($arrayRespuesta) > 0)
            {
                $strMensaje = "Consulta Exitosa!";
            }
            else
            {
                throw new \Exception('No existen registros.');
            }
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $resultado['status']  = $this->status['ERROR'];
            $resultado['mensaje'] = $e->getMessage();
            $resultado['data']    = array();

            return $resultado;
        }

        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $strMensaje;
        $resultado['data']      = $arrayRespuesta;
        
        return $resultado;
    } 
    
    /**
     * Funcion que sirve para actualizar los eventos
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 20-12-2017
     * 
     * @since 1.0
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 21-10-2020
     * Se agrega lógica para validar permisos en los eventos: Fin de Jornada
     * 
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function putActualizarEvento($arrayData)
    {
        $strStatus  = "";
        $strMensaje = "";
        $intId      = "";
        $strOpcion                  = "";
        $boolPermisoResponse        = false;
        $strMsgPermisoResponse      = '';
        $intStatusPermisoResponse   = 0;
        $strMsgExito                = 'Permiso habilitado';
        $strMsgError                = 'Permiso no habilitado';
        $intIdCabecera              = $arrayData['data']['idCabecera'];

        try
        {           
            if($arrayData['data']['strObservacion'] == 'FIN DE JORNADA')
            {
                $strOpcion = 'finJornada';
            }

            $arrayParametros = array('intId'                    => $arrayData['data']['intId'],
                                     'strVersion'               => $arrayData['data']['strVersion'],
                                     'strUsrCreacion'           => $arrayData['user'],
                                     'strIp'                    => $arrayData['ip']);

            /* @var $objSoporteService SoporteService */
            $objSoporteService = $this->get('soporte.SoporteService');
            //---------------------------------------------------------------------*/

            //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
            if($strOpcion == 'finJornada' && $intIdCabecera != 0)
            {
                $arrayRequestPermisos = array(
                    'idCabecera'            => $intIdCabecera,
                    'idCuadrilla'           => $arrayData['data']['intCuadrillaId'],
                    'publishid'             => $arrayData['data']['strPublishId'],
                    'idPersonaEmpresaRol'   => $arrayData['data']['intPersonaEmpresaRolId'],
                    'opcion'                => $strOpcion
                );
    
                $arrayResponsePermisos  = $this->obtenerPermisosJornadaAlimentacion($arrayRequestPermisos);
    
                if(!empty($arrayResponsePermisos) && 
                    count($arrayResponsePermisos) > 0 && 
                    $arrayResponsePermisos['status'] == 200 &&
                    count($arrayResponsePermisos['data']) > 0)
                {
                    if($strOpcion == 'finJornada' && $arrayResponsePermisos['data']['AutorizaFinalizar'] == 'S')
                    {
                        $boolPermisoResponse        = true;
                        $strMsgPermisoResponse      = $strMsgExito;
                        $intStatusPermisoResponse   = 200;
                    }
                    else
                    {
                        $strMsgPermisoResponse      = $strMsgError;
                        $intStatusPermisoResponse   = 403;
                    }
                }
    
                if(!$boolPermisoResponse)
                {
                    //llamar al service que consuma WS de HAL
                    $arrayRespuestaHal = $objSoporteService->getSolicitarPermisoEvento($arrayRequestPermisos);
    
                    if(isset($arrayRespuestaHal) && !empty($arrayRespuestaHal))
                    {
                        $intStatusPermisoResponse   = $arrayRespuestaHal['status'];
                        $strMsgPermisoResponse      = $arrayRespuestaHal['mensaje'];
                        $boolPermisoResponse        = $arrayRespuestaHal['permiso'];
                    }
                }
            }

            $arrayResultado['dataPermiso'] = array (
                'status'       => $intStatusPermisoResponse,
                'mensaje'      => $strMsgPermisoResponse,
                'permiso'      => $boolPermisoResponse,
                'evento'       => $strOpcion
            );

            if($intIdCabecera == 0 ||
                ($strOpcion == 'finJornada' && 
                    $boolPermisoResponse) || 
               $strOpcion != 'finJornada')
            {
                $respuestaArray = $objSoporteService->updateEvento($arrayParametros);
                $strStatus         = $respuestaArray['status'];
                $strMensaje        = $respuestaArray['mensaje'];
                //----------------------------------------------------------------------*/
            
                if($strStatus == "OK")
                {
                    $strMensaje = "Se actualizó la tarea!";
                }
            }
        }
        catch(\Exception $e)
        {
            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $this->mensaje['ERROR'];

            return $arrayResultado;
        }

        $arrayResultado['status']    = $this->status['OK'];
        $arrayResultado['mensaje']   = $strMensaje;
        return $arrayResultado;
    } 
    
    
    /**
     * Funcion que sirve para crear un evento
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 20-12-2017
     * @since 1.0
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 20-12-2017
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 21-10-2020
     * Se agrega lógica para validar permisos en los eventos: Alimentacion y Fin de Jornada
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function putIngresarEvento($arrayData)
    {
        $strStatus                  = "";
        $strMensaje                 = "";
        $intId                      = 0;
        $strOpcion                  = "";
        $boolPermisoResponse        = false;
        $strMsgPermisoResponse      = '';
        $intStatusPermisoResponse   = 0;
        $strMsgExito                = 'Permiso habilitado';
        $strMsgError                = 'Permiso no habilitado';
        $intIdCabecera              = $arrayData['data']['idCabecera'];

        try
        {
            if($arrayData['data']['strObservacion'] == 'ALIMENTACION')
            {
                $strOpcion = 'alimentacion';
            }
            elseif($arrayData['data']['strObservacion'] == 'FIN DE JORNADA')
            {
                $strOpcion = 'finJornada';
            }
            $arrayParametros = array('intCuadrillaId'           => $arrayData['data']['intCuadrillaId'],
                                     'intTipoEvento'            => $arrayData['data']['intTipoEvento'],
                                     'intDetalleId'             => $arrayData['data']['intDetalleId'],
                                     'intPersonaEmpresaRolId'   => $arrayData['data']['intPersonaEmpresaRolId'],
                                     'strObservacion'           => $arrayData['data']['strObservacion'],
                                     'strEstado'                => $arrayData['data']['strEstado'],
                                     'strSerieLogica'           => $arrayData['data']['strPublishId'],
                                     'strLatitud'               => $arrayData['data']['strLatitud'],
                                     'strLongitud'              => $arrayData['data']['strLongitud'],
                                     'strVersion'               => $arrayData['data']['strVersion'],
                                     'strUsrCreacion'           => $arrayData['user'],
                                     'strIp'                    => $arrayData['ip']);

            /* @var $objSoporteService SoporteService */
            $objSoporteService = $this->get('soporte.SoporteService');
            //---------------------------------------------------------------------*/

            //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
            if(($strOpcion == 'alimentacion' || $strOpcion == 'finJornada') &&
                $intIdCabecera != 0)
            {
                $arrayRequestPermisos = array(
                    'idCabecera'            => $intIdCabecera,
                    'idCuadrilla'           => $arrayData['data']['intCuadrillaId'],
                    'publishid'             => $arrayData['data']['strPublishId'],
                    'idPersonaEmpresaRol'   => $arrayData['data']['intPersonaEmpresaRolId'],
                    'opcion'                => $strOpcion
                );
    
                $arrayResponsePermisos  = $this->obtenerPermisosJornadaAlimentacion($arrayRequestPermisos);
    
                if(!empty($arrayResponsePermisos) && 
                    count($arrayResponsePermisos) > 0 && 
                    $arrayResponsePermisos['status'] == 200 &&
                    count($arrayResponsePermisos['data']) > 0)
                {
                    if(($strOpcion == 'alimentacion' && $arrayResponsePermisos['data']['AutorizaAlimentacion'] == 'S') ||
                        ($strOpcion == 'finJornada' && $arrayResponsePermisos['data']['AutorizaFinalizar'] == 'S'))
                    {
                        $boolPermisoResponse        = true;
                        $strMsgPermisoResponse      = $strMsgExito;
                        $intStatusPermisoResponse   = 200;
                    }
                    else
                    {
                        $strMsgPermisoResponse      = $strMsgError;
                        $intStatusPermisoResponse   = 403;
                    }
                }
    
                if(!$boolPermisoResponse)
                {
                    //llamar al service que consuma WS de HAL
                    $arrayRespuestaHal = $objSoporteService->getSolicitarPermisoEvento($arrayRequestPermisos);
    
                    if(isset($arrayRespuestaHal) && !empty($arrayRespuestaHal))
                    {
                        $intStatusPermisoResponse   = $arrayRespuestaHal['status'];
                        $strMsgPermisoResponse      = $arrayRespuestaHal['mensaje'];
                        $boolPermisoResponse        = $arrayRespuestaHal['permiso'];
                    }
                }
            }

            $arrayResultado['dataPermiso'] = array (
                'status'       => $intStatusPermisoResponse,
                'mensaje'      => $strMsgPermisoResponse,
                'permiso'      => $boolPermisoResponse,
                'evento'       => $strOpcion
            );

            if($intIdCabecera == 0 ||
                (($strOpcion == 'alimentacion' || 
                    $strOpcion == 'finJornada') && 
                    $boolPermisoResponse) || 
               ($strOpcion != 'alimentacion' && $strOpcion != 'finJornada'))
            {
            $respuestaArray = $objSoporteService->insertEvento($arrayParametros);

            $strStatus      = $respuestaArray['status'];
            $strMensaje     = $respuestaArray['mensaje'];
            $intId          = $respuestaArray['id'];
            }
            //----------------------------------------------------------------------*/
          
        }
        catch(\Exception $e)
        {
            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $e->getMessage();
            return $arrayResultado;
        }

        $arrayResultado['id']        = $intId;
        $arrayResultado['status']    = $strStatus;
        $arrayResultado['mensaje']   = $strMensaje;       
        
        return $arrayResultado;
    }

    /**
     * Funcion que sirve para insertar y actualizar una tarea tiempo, es el tiempo que se demora la tarea
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 20-15-2017
     *
     * @since 1.0
     *
     * @param array $arrayData
     * @return array $resultado
     */
    private function putIngresarTareaTiempo($arrayData)
    {
        /* @var $objSoporteService SoporteService */
        $objSoporteService  = $this->get('soporte.SoporteService');
        $arrayResultado  = $objSoporteService->saveInfoTareaTiempo($arrayData['data']);
       try{
           if ($arrayResultado["success"])
           {
               $arrayRespuesta['status']    = $this->status['OK'];
               $arrayRespuesta['mensaje']   = "INGESO DE DATOS CON EXITO";
           }
           else
           {
               $arrayRespuesta['status']    = $this->status['ERROR'];
               $arrayRespuesta['mensaje']   = 'NO SE INSERTO.';

           }
       }catch (Exception $a)
       {
           $arrayRespuesta['status']    = $this->status['Catch'];
           $arrayRespuesta['mensaje']   = 'NO SE INSERTO.';
           return $arrayRespuesta;
    }
        return $arrayRespuesta;
    }

    /**
     * Método que actualiza las coordenadas de un punto.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 26-02-2018
     *
     * @param array $arrayData[
     *                         "intIdPunto"         :integer: id del punto,
     *                         "strLatitud"         :string:  coordenada de latitud del punto,
     *                         "strLongitud"        :string:  coordenada de latitud del punto,
     *                         "strCodEmpresa"      :string:  Codigo de la empresa,
     *                         "strUsrCreacion"     :string:  Usuario de creación,
     *                         "strIpCreacion"      :string:  Ip de creación.
     *                         ]
     * @return array $arrayRespuesta['status'   : string :  Codigo de respuesta del consumo,
     *                               'mensaje'  : string :  Mensaje de respuesta.]
     */
    private function putNuevaCoordenadaDelPunto($arrayData)
    {
        $arrayRespuesta = array();
        $serviceUtil = $this->get('schema.Util');
        try
        {
            $arrayParametros  = array(
                                      'intIdPunto'      => $arrayData['data']['idPunto'],
                                      'strLatitud'      => $arrayData['data']['latitud'],
                                      'strLongitud'     => $arrayData['data']['longitud'],
                                      'strCodEmpresa'   => $arrayData['data']['codEmpresa'],
                                      'strUsrCreacion'  => $arrayData['user'],
                                      'strIpCreacion'   => '127.0.0.1');
            /* @var $serviceInfoPunto PuntoService */
            $serviceInfoPunto = $this->get('comercial.InfoPunto');
            $arrayRespuesta   = $serviceInfoPunto->actualizarCoordenadaDelPunto($arrayParametros);
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteWSController->putNuevaCoordenadaDelPunto()',
                                       'Error al actualizar las coordenadas del punto. '.$ex->getMessage(),
                                       $arrayData['user'],
                                       "127.0.0.1" );
            $arrayRespuesta['status']  = "ERROR";
            $arrayRespuesta['mensaje'] = "Se presentaron problemas al actualizar la información del punto, favor notificar a sistemas.";
        }

        return $arrayRespuesta;
    }
    
     /**
     * Método que ingresa el tipo de progreso en que se encuentra la tareas.
     *
     * @author Ronny Morán Ch. <rmoranc@telconet.ec>
     * @version 1.0 18-03-2018
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 04-10-2018 Se agrega nuevo progreso de tareas para las instalaciones que no requieren ingresar Ruta Fibra.
     * @since 1.0
     *
     * @author Jean Nazareno <jnazareno@telconet.ec>
     * @version 1.2 04-10-2019 Se agrega nuevo parámetro para saber el origen del ingreso del progreso.
     * @since 1.0 
     * 
     * @param array $arrayData[
     *                         "strCodEmpresa"          :integer: Codigo de la empresa,
     *                         "intIdTarea"             :integer: Id de tarea,
     *                         "intIdDetalle"           :integer: Id de detalle,
     *                         "strCodigoTipoProgreso"  :string:  Codigo tipo progreso,
     *                         "strUsrCreacion"         :string:  Usuario de creación,
     *                         "strIpCreacion"          :string:  Ip de creación.
     *                         ]
     * @return array $arrayRespuesta['status'   : string :  Codigo de respuesta del consumo,
     *                               'mensaje'  : string :  Mensaje de respuesta.]
     */
    private function putIngresarProgresoTarea($arrayData)
    {
        $arrayRespuesta = array();
        $serviceUtil = $this->get('schema.Util');
        try
        {
            $arrayParametros  = array(
                                      'strCodEmpresa'        => $arrayData['data']['strCodEmpresa'],
                                      'intIdTarea'           => $arrayData['data']['intIdTarea'],
                                      'intIdDetalle'         => $arrayData['data']['intIdDetalle'],
                                      'strCodigoTipoProgreso'=> $arrayData['data']['strCodigoTipoProgreso'],
                                      'intIdServicio'        => $arrayData['data']['intIdServicio'],
                                      'strDescripcionTarea'  => $arrayData['data']['strDescripcionTarea'],
                                      'strOrigen'            => $arrayData['data']['strOrigen'],
                                      'strUsrCreacion'       => $arrayData['user'],
                                      'strIpCreacion'        => '127.0.0.1');
            
            /* @var $objSoporteService SoporteService */
            $objSoporteService = $this->get('soporte.SoporteService');
            //---------------------------------------------------------------------*/

            //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/

        $intIdServicio =$arrayData['data']['intIdServicio'];
        $arrayRespuestaArray = $objSoporteService->ingresarProgresoTarea($arrayParametros); 
            
            $strStatus         = $arrayRespuestaArray['status'];
            $strMensaje        = $arrayRespuestaArray['mensaje'];
            //----------------------------------------------------------------------*/
            
            if($strStatus == "OK")
            {
                $strMensaje = "Se ingresó el progreso de la tarea!";
            }
            $arrayRespuesta['status']    = $this->status['OK'];
            $arrayRespuesta['mensaje']   = $strMensaje;
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteWSController->putIngresarProgresoTarea()',
                                       'Error al ingresar el progreso de la tarea. '.$ex->getMessage(),
                                       $arrayData['user'],
                                       "127.0.0.1" );
            $arrayRespuesta['status']  = "ERROR";
            $arrayRespuesta['mensaje'] = "Se presentaron problemas al ingresar la información el progreso de la tarea, favor notificar a sistemas.";
        }
        
        return $arrayRespuesta;
    }
    
    /**
     * Método que muestra los progresos que tiene la tarea .
     *
     * @author Ronny Moran Ch. <rmoranc@telconet.ec>
     * @version 1.0 18-03-2018
     * 
     * Se envia el idComunicacion para filtrar y obtener el progreso por numero de tarea
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.1 18-01-2018
     *
     * 
     * Se reemplaza el filtro de búsqueda del Id del progreso de la tarea por la fecha de creacion del progreso.
     * @author Ronny Morán Ch. <rmoranc@telconet.ec>
     * @version 1.2 10-02-2021
     *  
     * Se modifica la obtención del valor del progreso ingresado en la tarea. 
     * @author Ronny Morán Ch. <rmoranc@telconet.ec>
     * @version 1.3 26-05-2021
     *  
     * @param array $arrayData[
     *                         "intIdDetalle"           :integer: Id de detalle,
     *                         "intIdComunicacion"      :integer: Id de comunicacion (numero tarea),
     *                         "strUsrCreacion"         :string:  Usuario de creación,
     *                         "strIpCreacion"          :string:  Ip de creación.
     *                         ]     * 
     * 
     * @return array $arrayRespuesta['status'   : string :  Codigo de respuesta del consumo,
     *                               'mensaje'  : string :  Mensaje de respuesta.]
     */
    private function getProgresoPorcentajeTarea($arrayData)
    {
        $arrayRespuesta = array();
        $serviceUtil = $this->get('schema.Util');
        try
        {
            $arrayParametros  = array(
                                      'intIdDetalle'         => $arrayData['data']['intIdDetalle'],
                                      'strUsrCreacion'       => $arrayData['user'],
                                      'strIpCreacion'        => '127.0.0.1');
            
            $emSoporte            = $this->getDoctrine()->getManager("telconet_soporte");
            $objInfoProgresoTarea = $emSoporte->getRepository('schemaBundle:InfoProgresoTarea')->findBy(
                                                                                                        array(  'detalleId'        => $arrayData['data']['intIdDetalle'],
                                                                                                                'comunicacionId'   => $arrayData['data']['intIdComunicacion']),
                                                                                                        ['feCreacion'               => 'ASC']
                                                                                                        );
            $intTotal = 0;
            foreach($objInfoProgresoTarea as $progreso)
            {
                $hora_transaccion       = $progreso->getHoraTransaccion();
                $idProgresoPorcentaje   = $progreso->getProgresoPorcentaje();
                $objTipoProgreso        = $idProgresoPorcentaje->getTipoProgreso();
                $intPorcentaje          = $progreso->getValorProgreso();
                $codigoprogreso         = $objTipoProgreso->getCodigo();
                $nombreprogreso         = $objTipoProgreso->getNombreTipoProgreso();
                $estado                 = $progreso->getEstado();
                $intTotal = $intTotal + $intPorcentaje;
                
                $seguimientos[] = array(
                                        'horaTransaccion'       =>date_format($hora_transaccion, 'd-m-Y G:i'),
                                        'codigoProgreso'        =>$codigoprogreso,
                                        'porcentaje'            =>$intPorcentaje,
                                        'nombreProgreso'        =>$nombreprogreso,
                                        'estado'                =>$estado,
                                        'total'                 =>$intTotal
                                       );
            }
            
            $mensaje = "Se obtuvo el progreso de la tarea!";
            $arrayRespuesta['status']    = $this->status['OK'];
            $arrayRespuesta['mensaje']   = $mensaje;
            $arrayRespuesta['data']      = $seguimientos;
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteWSController->putIngresarProgresoTarea()',
                                       'Error al ingresar el progreso de la tarea. '.$ex->getMessage(),
                                       $arrayData['user'],
                                       "127.0.0.1" );
            $arrayRespuesta['status']  = "ERROR";
            $arrayRespuesta['mensaje'] = "Se presentaron problemas al obtener la información el progreso de la tarea, favor notificar a sistemas.";
            $arrayRespuesta['data']      = "";
        }
        return $arrayRespuesta;
    }
    
    /**
     * Funcion que sirve para obtener los elemento vehiculo
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 27-03-2018
     * 
     * @since 1.0
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getElementoVehiculo($arrayData)
    {
        $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arrayParametros = array('intIdElemento'    => $arrayData['data']['idElemento'],
                                     'strTipoElemento'  => 'VEHICULO',
                                     'strEstado'        => $arrayData['data']['estado']);
            
            $arrayRespuesta  = $emSoporte->getRepository('schemaBundle:InfoElemento')->getArrayElementoTipo($arrayParametros);

            if(count($arrayRespuesta) > 0)
            {
                $strMensaje = "Consulta Exitosa!";
            }
            else
            {
                throw new \Exception('No existen registros.');
            }
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
            $resultado['status']  = $this->status['ERROR'];
            $resultado['mensaje'] = $e->getMessage();
            $resultado['data']    = array();

            return $resultado;
        }

        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $strMensaje;
        $resultado['data']      = $arrayRespuesta;
        
        return $resultado;
    } 
    
   /**
    * Funcion que obtiene la bandera para saber si se esta procesando, se realizo o hubo un error
    * en una instalacion MD 
    * @author Nestor Naula <nnaulal@telconet.ec>
    * @version 1.0 22-06-2018
    * 
    * @param array $arrayData
    * @return array $arrayResultado
    */
    private function getCaso($arrayData)
    {
       $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
       $arrayResultado = array();
       try
       {
           $arrayParametros = array('strLogin'         => $arrayData['data']['login'],
                                    'strEstado'        => $arrayData['data']['estado'],
                                    'strCodEmpresa'    => $arrayData['data']['codEmpresa'],
                                    'strFeInicial'     => $arrayData['data']['feInicial'],
                                    'strFeFinal'       => $arrayData['data']['feFinal']
                                   );

           $arrayRespuesta  = $emSoporte->getRepository('schemaBundle:InfoCaso')->getCaso($arrayParametros);

           if(count($arrayRespuesta) > 0)
           {
               $strMensaje = "Consulta Exitosa!";
           }
           else
           {
               throw new \Exception('No existen registros.');
           }
       }
       catch(\Exception $e)
       {
           $arrayResultado['status']  = $this->status['ERROR'];
           $arrayResultado['mensaje'] = $e->getMessage();
           $arrayResultado['data']    = array();

           return $arrayResultado;
       }

       $arrayResultado['status']    = $this->status['OK'];
       $arrayResultado['mensaje']   = $strMensaje;
       $arrayResultado['data']      = $arrayRespuesta;

       return $arrayResultado;
    }
    
   /**
    * Método que obtiene el origen de creación de un caso puede ser desde Telcos o creada
    * desde la app cliente.
    *
    * @author Nestor Naula  <nnaula@telconet.ec>
    * @version 1.0 08-05-2018
    * 
    * @author Modificado: Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
    * @version 1.1 24-06-2018 - Compatibilidad para filtrar casos por estados,
    *                           cobertura, login, estados, rango de fechas,
    *                           o direccion.
    *
    * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.2 03-12-2018 - Se controla la excepción cuando una razon social no dispone de casos.
    *
    * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
    * @version 1.3 07/07/2020 - Se debe visualizar los casos del día.
    *
    * @param array $arrayData
    * @return array $arrayResultado
    */
    private function getCasoTipoOrigen($arrayData)
    {
       $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
       $arrayResultado = array();
       $serviceUtil    = $this->get('schema.Util');
       try
       {
           //Obtener el contacto del IPCCL1 R1,R2
           $arrayBandCasos     = $emSoporte->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('VISUALIZAR_CASOS_DEL_DIA_TM',
                                                    'SOPORTE',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '');
            if(!empty($arrayBandCasos) && $arrayBandCasos['valor1'] == 'S')
            {
                $arrayData['data']['feInicial'] = date("d/m/Y");
                $arrayData['data']['feFinal']   = date("d/m/Y");
            }
           $arrayData['data']['idPersonaEmpresaRol'] = $arrayData['data']['personaEmpresaRolId'];
           $this->validarPersonaEmpresaRolId($arrayData);
           $arrayParametros = array('strLogin'                 => $arrayData['data']['login'],
                                    'strEstados'               => $arrayData['data']['estados'],
                                    'strCodEmpresa'            => $arrayData['data']['codEmpresa'],
                                    'strFeInicial'             => $arrayData['data']['feInicial'],
                                    'strFeFinal'               => $arrayData['data']['feFinal'],
                                    'strDireccion'             => $arrayData['data']['direccion'],
                                    'intIdCiudad'              => $arrayData['data']['idCiudad'],
                                    'intPersonaEmpresaRolId'   => $arrayData['data']['personaEmpresaRolId']
                                   );

           $arrayRespuesta  = $emSoporte->getRepository('schemaBundle:InfoCaso')->getCasoTipoOrigen($arrayParametros);

           if(count($arrayRespuesta) > 0)
           {
               $strMensaje = "Consulta Exitosa!";
           }
           else
           {
               $strMensaje = "No existen registros.";
           }
       }
       catch(\Exception $e)
       {
           $serviceUtil->insertError( 'Telcos+',
                                       'SoporteWSController->getCasoTipoOrigen()',
                                       'Error al obtener la información. '.$e->getMessage(),
                                       $arrayData['user'],
                                       "127.0.0.1" );
           $arrayResultado['status']  = $this->status['ERROR'];
           $arrayResultado['mensaje'] = 'Error al obtener la información. ';
           $arrayResultado['data']    = array();
           return $arrayResultado;
       }
       $arrayResultado['status']    = $this->status['OK'];
       $arrayResultado['mensaje']   = $strMensaje;
       $arrayResultado['data']      = $arrayRespuesta;
       return $arrayResultado;
    }
    /**
     * Función que obtiene la bandera para saber si se esta procesando, se realizó o hubo un error
     * en una instalación MD 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 22-06-2018
     * 
     * @since 1.0
     * 
     * Se modifica el método para que permita obtener el estado de instalación por empresa
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.1 09-07-2019
     *
     * @param array $arrayData
     * @return array $resultado
     */
    private function getEstadoInstalacionServicio($arrayData)
    {
        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial    = $this->getDoctrine()->getManager("telconet"); 

        try
        {
            $intIdServicio  = $arrayData['data']['idServicio'];
            $strCodEmpresa  = $arrayData['data']['codEmpresa'];
         
            if($strCodEmpresa === "MD")
            {
                $arrayResultado = $emSoporte->getRepository('schemaBundle:InfoEstadoInstalacion')->ObtenerEstadoProceso($intIdServicio);
                if(isset($arrayResultado['estado']) && !empty($arrayResultado['estado']))
                {
                    $strMensaje = "Consulta Exitosa!";
                }
                else
                {
                     $strMensaje = "No existen registros";
                     $arrayResultado = null;
                   // throw new \Exception('No existen registros.');
                }
            }else
            {
                $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->findOneById($intIdServicio); 
                
                if(isset($arrayServicios))
                {
                    $arrayResultado['estado'] = $arrayServicios->getEstado();
                    $strMensaje               = "Consulta Exitosa!";
                }else
                {
                    $strMensaje     = "No existen registros";
                    $arrayResultado = null;
                }
                
            }
        }
        catch(\Exception $e)
        {
            
            $resultado['status']  = $this->status['ERROR'];
            $resultado['mensaje'] = $e->getMessage();
            $resultado['data']    = array();

            return $resultado;
        }

        $resultado['status']    = $this->status['OK'];
        $resultado['mensaje']   = $strMensaje;
        $resultado['data']      = $arrayResultado;
        
        return $resultado;
    } 

    /**
     * Funcion que sirve para obtener el cliente
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 10-04-2018
     *
     * @since 1.0
     *
     * @param array $arrayData
     * @return array $resultado
     */
    private function getCliente($arrayData)
    {
        $arrayRespuesta = array();
        $arrayResultado = array();
        try
        {
            $strCodEmpresa     = $arrayData['data']['codEmpresa'];
            $strIdentificacion = $arrayData['data']['identificacion'];
            $strPrefijoEmpresa = $arrayData['data']['prefijo'];

            //obtener fecha y hora del servidor, tiempo transcurrido
            $objClienteService  = $this->get('comercial.Cliente');
            $arrayRespuesta     = $objClienteService->obtenerDatosClientePorIdentificacion($strCodEmpresa,$strIdentificacion, $strPrefijoEmpresa);

            if(count($arrayRespuesta) > 0)
            {
                $strMensaje = "Consulta Exitosa!";
            }
            else
            {
                throw new \Exception('No existen registros.');
            }
        }
        catch(\Exception $e)
        {
            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $e->getMessage();
            $arrayResultado['data']    = array();

            return $resultado;
        }

        $arrayResultado['status']    = $this->status['OK'];
        $arrayResultado['mensaje']   = $strMensaje;
        $arrayResultado['data']      = $arrayRespuesta;

        return $arrayResultado;
    }

    /**
     * Funcion que sirve para obtener los Puntos de un cliente
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 10-04-2018
     *
     * @since 1.0
     *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getPuntoCliente($arrayData)
    {
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $arrayRespuesta     = array();
        $arrayResultado     = array();
        $intAnteriorZabbixId= 1;
        try
        {
            $arrayParametros = array('strCodEmpresa'        => $arrayData['data']['codEmpresa'],
                                     'intIdPersona'         => $arrayData['data']['idPersona'],
                                     'intIdCanton'          => $arrayData['data']['idCanton'],
                                     'strDireccion'         => $arrayData['data']['direccion'],
                                     'strEstado'            => $arrayData['data']['estado'],
                                     'strLogin'             => $arrayData['data']['login'],
                                     'strEstadoNotIn'       => ['Cancelado', 'Pendiente', 'Anulado']
                                     );

            $arrayPuntos     = $emSoporte->getRepository('schemaBundle:InfoPunto')
                                         ->getPuntoCliente($arrayParametros);

            foreach($arrayPuntos as $arrayPuntosMapa)
            {
                $arrayServicios = $emComercial->getRepository('schemaBundle:InfoServicio')
                                              ->findBy(array("puntoId"  => $arrayPuntosMapa['idPunto'],
                                                             "estado"   => "Activo"));

                foreach($arrayServicios as $objLoginAux)
                {
                    if(is_object($objLoginAux))
                    {
                        $strLoginAux = $objLoginAux->getLoginAux();
                        if(!empty($strLoginAux))
                        {
                            $objServicioLoginAux = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                               ->findOneByValor(strtoupper($strLoginAux));
                            if(is_object($objServicioLoginAux))
                            {
                             $intServicioLoginAuxId = $objServicioLoginAux->getId();
                             if(!empty($intServicioLoginAuxId))
                             {
                                 $objRespuestaZabbixId                     = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                                         ->findOneByPersonaEmpresaRolCaracId($intServicioLoginAuxId);
                                 $arrayPuntosMapa['monitoreo']['idZabbix'] = $objRespuestaZabbixId->getValor();
                                 if($intAnteriorZabbixId != $objRespuestaZabbixId->getValor())
                                 {
                                     $arrayRespuesta[] = $arrayPuntosMapa;
                                 }
                                 $intAnteriorZabbixId  = $objRespuestaZabbixId->getValor();
                             }
                            }
                        }    
                    }
                }
            }
            if(count($arrayPuntos) > 0)
            {
                $strMensaje = "Consulta Exitosa!";
            }
            else
            {
                throw new \Exception('No existen registros.');
            }
        }
        catch(\Exception $e)
        {
            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $e->getMessage();
            $arrayResultado['data']    = array();

            return $arrayResultado;
        }

        $arrayResultado['status']    = $this->status['OK'];
        $arrayResultado['mensaje']   = $strMensaje;
        $arrayResultado['data']      = $arrayRespuesta;

        return $arrayResultado;
    }

    /**
     * Funcion que sirve para obtener la cobertura de un cliente
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 18-04-2018
     *
     * @since 1.0
     *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getCoberturaCliente($arrayData)
    {
        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $arrayResultado = array();
        try
        {
            $this->validarPersonaId($arrayData);
            $arrayParametros = array('strCodEmpresa'   => $arrayData['data']['codEmpresa'],
                                     'intIdPersona'    => $arrayData['data']['idPersona'],
                                     'strRol'          => $arrayData['data']['rol'],
                                     'strEstadoNotIn'  => ['Cancelado', 'Pendiente', 'Anulado']
                                     );

            $arrayRespuesta  = $emSoporte->getRepository('schemaBundle:InfoPunto')->getCoberturaCliente($arrayParametros);
            $intIncremento = 0;
            foreach($arrayRespuesta as $arrayCanton)
            {
                $arrayCantones                            = explode(" - ", $arrayCanton['puntoCobertura']);
                $arrayRespuesta[$intIncremento]['canton'] = $arrayCantones[1];
                $intIncremento++;
            }

            if(count($arrayRespuesta) > 0)
            {
                $strMensaje = "Consulta Exitosa!";
            }
            else
            {
                throw new \Exception('No existen registros.');
            }
        }
        catch(\Exception $e)
        {
            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $e->getMessage();
            $arrayResultado['data']    = array();

            return $arrayResultado;
        }

        $arrayResultado['status']    = $this->status['OK'];
        $arrayResultado['mensaje']   = $strMensaje;
        $arrayResultado['data']      = $arrayRespuesta;

        return $arrayResultado;
    }

    /**
     * Función para obtener el resumen de casos, puntos y servicios de un 
     * cliente en los últimos N dias o un mes determinado del último año.
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 20-04-2018
     * @version 2.0 22-06-2018
     *
     * @since 1.0
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 2.1 07-07-2020 - Se requiere consultar todos los casos abiertos
     *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getPuntoClientePorEstado($arrayData)
    {
        $emSoporte            = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial          = $this->getDoctrine()->getManager("telconet");
        try
        {
            $this->validarPersonaId($arrayData);
            $arrayParametros = array('strCodEmpresa'   => $arrayData['data']['codEmpresa'],
                                     'intIdPersona'    => $arrayData['data']['idPersona'],
                                     'strRol'          => $arrayData['data']['rol'],
                                     'strEstadoNotIn'  => ['Cancelado', 'Pendiente', 'Anulado'],
                                     'strDias'         => $arrayData['data']['dias'],
                                     'strMes'          => $arrayData['data']['mes'],
                                     'strAnio'         => $arrayData['data']['anio'],
                                     'strFeInicio'     => $arrayData['data']['feInicio'],
                                     'strFeFin'        => $arrayData['data']['feFin'],
                                     'boolAbierto'     => $arrayData['data']['boolAbierto']
                                    );

            $arrayResultadoCasos      = $emSoporte->getRepository('schemaBundle:InfoCaso')->getCasosClientePorEstado($arrayParametros);
            $arrayResultadoServicios  = $emComercial->getRepository('schemaBundle:InfoServicio')->getServicioClientePorEstado($arrayParametros);

            $arrayResultadoTotal = array('casos'        => $arrayResultadoCasos,
                                         'servicios'    => $arrayResultadoServicios
                                        );

            if(count($arrayResultadoServicios) > 0)
            {
                $strMensaje = "Consulta Exitosa!";
            }
            else
            {
                throw new \Exception('No existen registros.');
            }
        }
        catch(\Exception $e)
        {
            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = "Problemas al ejecutar la consulta";
            $arrayResultado['data']    = array();

            return $arrayResultado;
        }

        $arrayResultado['status']    = $this->status['OK'];
        $arrayResultado['mensaje']   = $strMensaje;
        $arrayResultado['data']      = $arrayResultadoTotal;

        return $arrayResultado;
    }

 /**
     * Metodo que registra informacion del dispositivo e historial  y verifica el acceso a una aplicacion movil.
     *
     * @author Ronny Moran Chancay. <rmoranc@telconet.ec>
     * @version 1.0 12-06-2018
     *
     * @param array $arrayData[
     *                            "idPersona"               :integer: Id de detalle,
                                  "codigoDispositivo"       :string: codigo unico del dispositivo,
                                  "ipAcceso"                :string: ip del dispositivo,
                                  "descripcion"             :string: descripcion del dispositivo,
                                  "correo"                  :string: correo del dispositivo asociado,
                                  "estado"                  :string: estado del dispositivo,
                                  "nombreAppMovil"          :string: Nombre de app movil de donde accede,
                                  "latitud"                 :string: Latitud de dispositivo movil,
                                  "longitud"                :string: Longitud de dispositivo movil,
                                  "bloqueado"               :integer: Indica si el dispositivo se encuestra bloqueado,
                                  "sistemaOperativo"        :string: Sistema Operativo del dispositivo movil,
                                  "TipoDispositivo"         :string: Tipo de dispositivo movil,
                                  "opSesion"                :string: Opcion al iniciar la app.
     *                         ]
     * @return array $arrayRespuesta['status'   : string :  Codigo de respuesta del servidor,
     *                               'mensaje'  : string :  Mensaje de respuesta.]
     */
    private function putDispositivoVerificacionApp($arrayData)
    {
        $arrayRespuesta = array();
        $serviceUtil    = $this->get('schema.Util');
        try
        {
            $serviceSoporte     = $this->get('soporte.SoporteService');
            $arrayResultado     = $serviceSoporte->verificaDispositivoApp($arrayData);

            $strStatus          = $arrayResultado['status'];
            $strMensaje         = $arrayResultado['mensaje'];
            if($strStatus == "OK")
            {
                $arrayRespuesta['status']    = $this->status['OK'];
                $arrayRespuesta['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayRespuesta['status']    = $this->status['ERROR'];
                $arrayRespuesta['mensaje']   = $strMensaje ? $strMensaje : "Se presentaron problemas al ingresar a la app, intente más luego.";
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteWSController->putDispositivoApp()',
                                       'Error al ingresar el informacio del dispositivo '.$ex->getMessage(),
                                       $arrayData['user'],
                                       "127.0.0.1" );
            $arrayRespuesta['status']    = $this->status['ERROR'];
            $arrayRespuesta['mensaje']   = "Se presentaron problemas al ingresar y verificar el dispositivo, favor notificar a sistemas.";
        }
        return $arrayRespuesta;
    }

    /**
     * Función que sirve para obtener el perfil en aplicación móvil cliente
     *
     * @author Ronny Moran Chancay <rmoranc@telconet.ec>
     * @version 1.0 03-07-2018
     *
     * @since 1.0
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 - 07/07/2020 - Se adiciona el contacto de L1 Nacional
     *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getPerfilCliente($arrayData)
    {
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $arrayResultado     = array();
        try
        {
            $this->validarPersonaEmpresaRolId($arrayData);
            $arrayParametros = array('intIdPersonaEmpresaRol'        => $arrayData['data']['idPersonaEmpresaRol'],
                                     'intIdPersona'                  => $arrayData['data']['idPersona']
                                     );

            $arrayIngCom        = $emSoporte->getRepository('schemaBundle:InfoPersona')
                                            ->getPerfilUsuarioComercial($arrayParametros);

            foreach ($arrayIngCom as $arrayComercialValores)
            {
                $arrayValorComercial['nombre']          = $arrayComercialValores['nombre'];
                $arrayValorComercial['foto']            = $arrayComercialValores['foto'];
                $arrayValorComercial['mail']            = $arrayComercialValores['mail'];
                $arrayValorComercial['celular']         = $arrayComercialValores['celular'];
                $arrayValorComercial['tipo']            = $arrayComercialValores['tipo'];
                $arrayValorComercial['region']          = $arrayComercialValores['region'];
                $arrayValorComercial['sexo']            = $arrayComercialValores['sexo'];
                $arrayValorComercial['ultimaSesion']    = $arrayComercialValores['ultimaSesion'];
                //Buscar numero celular asignado al colaborador por la empresa.
                $arrayParmDetElem                       = array('detalleValor'  => $arrayComercialValores['personaEmpresaRolId'],
                                                                'detalleNombre' => 'COLABORADOR',
                                                                'estado'        => 'Activo');
                $objDetalleElemento                     = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->findOneBy($arrayParmDetElem);
                if(is_object($objDetalleElemento))
                {
                     $objInfoElemento        = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->findOneById($objDetalleElemento->getElementoId());
                     $arrayValorComercial['celular'] = $objInfoElemento->getNombreElemento();
                }

                $arrayRespuesta[]                       = $arrayValorComercial;
            }
            $arrayIngVip        = $emSoporte->getRepository('schemaBundle:InfoPersona')
                                            ->getPerfilUsuarioVip($arrayParametros);
            foreach ($arrayIngVip as $arrayVip)
            {
                $arrayRespuesta[] = $arrayVip;
            }

            //Obtener el contacto del IPCCL1 R1,R2
            $arrayIPCCL1        = $emSoporte->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('CONTACTOS_L1',
                                                     'SOPORTE',
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     '',
                                                     '');
            foreach ($arrayIPCCL1 as $arrayParDet)
            {
                $arrayDeptL1['nombre']          = $arrayParDet['valor1'];
                $arrayDeptL1['foto']            = null;
                $arrayDeptL1['mail']            = $arrayParDet['valor2'];
                if(!empty($arrayParDet['valor3']))
                {
                    $arrayDeptL1['celular']         = $arrayParDet['valor3'];
                }
                else
                {
                    $arrayContactosL1   = $emSoporte->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('NUMERO_CONTACTO_L1',
                                                          'COMERCIAL',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          '');
                    foreach ($arrayContactosL1 as $arrayTlfnL1)
                    {
                        error_log($arrayTlfnL1['valor1']);
                        $arrayListContactos[]   = array('contacto' => $arrayTlfnL1['valor1']);
                    }
                    if(!empty($arrayListContactos))
                    {
                        $arrayDeptL1['telefonos'] = $arrayListContactos;
                    }
                }
                $arrayDeptL1['tipo']            = $arrayParDet['valor4'];
                $arrayDeptL1['region']          = $arrayParDet['valor5'];
                $arrayDeptL1['sexo']            = 'M';
                $arrayDeptL1['ultimaSesion']    = null;
                $arrayRespuesta[]               = $arrayDeptL1;
            }
            if(count($arrayRespuesta) > 0)
            {
                $strMensaje = "Consulta Exitosa!";

                $arrayResultado['status']    = $this->status['OK'];
                $arrayResultado['mensaje']   = $strMensaje;
                $arrayResultado['data']      = $arrayRespuesta;

            }
            else
            {
                throw new \Exception('No existen registros.');
            }
        }
        catch(\Exception $e)
        {
            $arrayResultado['status']  = $this->status['ERROR'];
            $arrayResultado['mensaje'] = $e->getMessage();
            $arrayResultado['data']    = array();
        }
        return $arrayResultado;
    }


    /* Método que actualiza el estado de la sesión en la app movil clientes
     *
     * @author Ronny Moran Chancay. <rmoranc@telconet.ec>
     * @version 1.0 12-06-2018
     *
     * @param array $arrayData[
     *                            "idPersona"               :integer: Id de la persona,
                                  "codigoDispositivo"       :string: codigo unico del dispositivo,
     *                         ]
     * @return array $arrayRespuesta['status'   : string :  Codigo de respuesta del servidor,
     *                               'mensaje'  : string :  Mensaje de respuesta.]
     */
    private function putCambiarEstadoSession($arrayData)
    {
        $arrayRespuesta = array();
        try
        {
            $this->validarPersonaId($arrayData);
            $emSoporte  = $this->getDoctrine()->getManager("telconet_soporte");
            $emSoporte->beginTransaction();
            $arrayParametros    = array(
                                         'codigoDispositivo' => $arrayData['data']['codigoDispositivo'],
                                         'id'                => 'DESC',
                                         'personaId'         => $arrayData['data']['idPersona']);

            $intIdHistorial     = $emSoporte->getRepository("schemaBundle:InfoHistorialIngresoApp")
                                            ->getUltimoEstadoSession($arrayParametros);
            $objHistorialSesion = $emSoporte->getRepository("schemaBundle:InfoHistorialIngresoApp")
                                            ->findOneById($intIdHistorial);
            $strEstadoFinalizada="Finalizado";
            if(is_object($objHistorialSesion))
            {
             $objHistorialSesion->setEstado($strEstadoFinalizada);
             $emSoporte->persist($objHistorialSesion);
             $emSoporte->flush();

                if ($emSoporte->getConnection()->isTransactionActive())
                {
                    $emSoporte->getConnection()->commit();
                    $arrayRespuesta['status'] = 'OK';
                    $arrayRespuesta['mensaje'] = 'Sesion cerrada';
                }
            }
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteWSController->putCambiarEstadoSession()',
                                       'Error al cambiar el estado de la sesión '.$ex->getMessage(),
                                       $arrayData['user'],
                                       "127.0.0.1" );
            $arrayRespuesta['status'] = 'ERROR';
            $arrayRespuesta['mensaje'] = $ex->getMessage();
        }

        return $arrayRespuesta;
    }
    /********************************************************************************************
     * FIN METODOS PUT SOPORTE MOBILE
     ********************************************************************************************/

    /**
     * Función que sirve para la creación de casos
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 01-02-2018
     * @since 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 01-10-2018 - Se modifica el método para manejar un standar en la respuesta [status, message]
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.2 24-07-2019 - Se agrega los parámetros 'strEstadoActual','strEcucert'
     *                            '$strTipoReprograma' y 'intTiempo' para identificar
     *                            los casos creados por ECUCERT y ponerlos como tiempo
     *                            del cliente.
     * @since 1.1
     * 
     * @param type $arrayData
     * @return $arrayRespuesta
     */
    private function putCrearCaso($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');
        $objServiceUtil    = $this->get('schema.Util');
        try
        {
            $arrayParametros = array ('strPrefijoEmpresa'    => $arrayData['data']['prefijoEmpresa'],
                                      'strTipoCaso'          => $arrayData['data']['tipoCaso'],
                                      'strFormaContacto'     => $arrayData['data']['formaContacto'],
                                      'strNivelCriticidad'   => $arrayData['data']['nivelCriticidad'],
                                      'strTipoAfectacion'    => $arrayData['data']['tipoAfectacion'],
                                      'strFechaHoraApertura' => $arrayData['data']['fechaHoraApertura'],
                                      'strTipoBackbone'      => $arrayData['data']['tipoBackbone'],
                                      'strTituloInicial'     => $arrayData['data']['tituloInicial'],
                                      'strVersionInicial'    => $arrayData['data']['versionInicial'],
                                      'arraySintomas'        => $arrayData['data']['sintomas'],
                                      'arrayHipotesis'       => $arrayData['data']['hipotesis'],
                                      'strEmpleadoAsignado'  => $arrayData['data']['empleadoAsignado'],
                                      'arrayTareas'          => $arrayData['data']['tareas'],
                                      'strEstadoActual'      => $arrayData['data']['estadoActual'],
                                      'strTipoReprograma'    => $arrayData['data']['tipoReprograma'],
                                      'intTiempo'            => $arrayData['data']['tiempo'],
                                      'intIdCaso'            => $arrayData['data']['idCaso'],
                                      'intIdCasoHistorial'   => $arrayData['data']['idCasoHistorial'],
                                      'intIdComunicacion'    => $arrayData['data']['idComunicacion'],
                                      'intIdDocumento'       => $arrayData['data']['idDocumento'],
                                      'intIdDocComunicacion' => $arrayData['data']['idDocComunicacion'],
                                      'strUserCreacion'      => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIpCreacion'        => ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1'));

            /* Creación de caso */
            $arrayRespuesta = $objSoporteService->crearCasoSoporte($arrayParametros);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => 'Error en el webservice de creación de casos');

            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.putCrearCaso',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1'));
        }
        return $arrayRespuesta;
    }
    
    /**
     * putCrearTarea
     * Función que sirve para la creación de una tarea
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 20-06-2018
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.2 24-07-2019 - Se agrega los parámetros 'strEstadoActual','strEcucert'
     *                            '$strTipoReprograma' y 'intTiempo' para identificar
     *                            los casos creados por ECUCERT y ponerlos como tiempo
     *                            del cliente.
     
     * @author Pedro Velez <psvelez@telconet.ec>
     * @version 1.3 16-10-2021 - Se agrega llamado a proceso para envio de evento de asignacion 
     *                           de tareas Hal hacia megadatos para tracking map                          
     *
     * @since 1.0
     *
     * @param array $arrayData
     * [
     *      intIdCaso               - Id del caso            
            intIdDetalleHipotesis   - Id del detalle de la hipotesis
            strEmpleadoAsignado     - Nombre del empleado asignado
            intCuadrillaId          - Id de la cuadrilla
            strPrefijoEmpresa       - Prefijo de la empresa
            strNombreProceso        - Nombre del proceso
            strNombreTarea          - Nombre de la tarea
            strMotivoTarea          - Motivo de la tarea
            strObservacionTarea     - Observación de la tarea
            intIdPunto              - Id punto del cliente
            strFormaContacto        - Forma de contacto
            strTipoAsignacion       - Tipo de asignación
            strAsignacionAut        - Asignación automática de la tarea
            strUserCreacion         - Usuario de creación
            strIpCreacion           - Ip de creación
     * ] 
     * 
     * @return array $arrayRespuesta
     * [
     *      status  - Estado de la consulta
            message - Mensaje  de retorno
     * ]
     */
    private function putCrearTarea($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');
        $objServiceUtil    = $this->get('schema.Util');
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial       = $this->getDoctrine()->getManager("telconet");
        try
        {
            $intIdCaso              = $arrayData['data']['idCaso'];
            $intIdDetalleHipotesis  = $arrayData['data']['idDetalleHipotesis'];
            $strEmpleadoAsignado    = $arrayData['data']['empleadoAsignado'];
            $intCuadrillaId         = $arrayData['data']['idCuadrilla'];
            $strPrefijoEmpresa      = $arrayData['data']['prefijoEmpresa'];
            $strNombreProceso       = $arrayData['data']['tareas'][0]["nombreProceso"];
            $strNombreTarea         = $arrayData['data']['tareas'][0]["nombreTarea"];
            $intIdProceso           = $arrayData['data']['tareas'][0]["idProceso"];
            $intIdTarea             = $arrayData['data']['tareas'][0]["idTarea"];
            $strMotivoTarea         = $arrayData['data']['tareas'][0]["motivoTarea"];
            $intIdDetalle           = $arrayData['data']['tareas'][0]["idDetalle"];
            $intIdComunicacion      = $arrayData['data']['tareas'][0]["idComunicacion"];
            $intIdDocumento         = $arrayData['data']['tareas'][0]['idDocumento'];
            $intIdDocuComunica      = $arrayData['data']['tareas'][0]['idDocuComunica'];
            $intIdDetalleAsig       = $arrayData['data']['tareas'][0]['idDetalleAsig'];
            $intIdDetalleHisto      = $arrayData['data']['tareas'][0]['idDetalleHisto'];
            $intIdTareaSeguimiento  = $arrayData['data']['tareas'][0]['idTareaSeguimiento'];
            $strObservacionTarea    = $arrayData['data']['tareas'][0]["observacion"];
            $intIdPunto             = $arrayData['data']['tareas'][0]["afectados"]["idAfectados"];
            $strFormaContacto       = $arrayData['data']['formaContacto'];
            $strTipoAsignacion      = $arrayData['data']['tareas'][0]["tipoAsignacion"];
            $strAsignacionAut       = $arrayData['data']['tareas'][0]["asignacionAut"];
            $strUserCreacion        = $arrayData['user'] ? $arrayData['user'] : 'Telcos';
            $strIpCreacion          = $arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1';
            $strTipoReprograma      = $arrayData['data']["tipoReprograma"];
            $strEstadoActual        = $arrayData['data']["estadoActual"];
            $intTiempo              = $arrayData['data']["tiempo"];
            $intIdEmpresa           = null;
            $intIdPersonaEmpresaRol = null;
 
            if(isset($intIdCaso) && !empty($intIdCaso))
            {
                $objInfoCaso                = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                                        ->findOneById($intIdCaso);
            }
            if(isset($intIdDetalleHipotesis) && !empty($intIdDetalleHipotesis))
            {
                $objDetalleHipotesis        = $emSoporte->getRepository('schemaBundle:InfoDetalleHipotesis')
                                                        ->findOneById($intIdDetalleHipotesis);
            }
            if(isset($strEmpleadoAsignado) && !empty($strEmpleadoAsignado))
            {
                $objInfoPersonaEmpresaRol   = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                          ->find($strEmpleadoAsignado);
            
                $intIdPersonaEmpresaRol = $objInfoPersonaEmpresaRol->getId();
            }
            if(isset($intCuadrillaId) && !empty($intCuadrillaId))
            {
                $objInfoPersonaEmpresaRol   = $emComercial->getRepository("schemaBundle:AdmiCuadrilla")
                                                          ->findOneById($intCuadrillaId);
            
                $intIdPersonaEmpresaRol = $objInfoPersonaEmpresaRol->getId();
            }
            if(isset($strPrefijoEmpresa) && !empty($strPrefijoEmpresa))
            {
                $objInfoEmpresaGrupo        = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                          ->findOneBy(array ('prefijo' => $strPrefijoEmpresa,
                                                                             'estado'  => "Activo"));
            
                $intIdEmpresa = $objInfoEmpresaGrupo->getId();
            }
            if(isset($strFormaContacto) && !empty($strFormaContacto))
            {
                $objAdmiFormaContacto       = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                          ->findOneBy(array ('codigo' => $strFormaContacto,
                                                                             'estado' => "Activo"));
            }
            if(isset($intIdTarea) && !empty($intIdTarea))
            {
                $objAdmiTarea          = $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                   ->findOneById($intIdTarea);
                if(is_object($objAdmiTarea))
                {
                    $strNombreTarea    = $objAdmiTarea->getNombreTarea();
                }
            }
            if(isset($intIdProceso) && !empty($intIdProceso))
            {
                $objAdmiProceso        = $emSoporte->getRepository('schemaBundle:AdmiProceso')
                                                   ->findOneById($intIdProceso);
                if(is_object($objAdmiProceso))
                {
                    $strNombreProceso  = $objAdmiProceso->getNombreProceso();
                }
            }
            if (isset($strAsignacionAut) && !empty($strAsignacionAut) && strtoupper($strAsignacionAut) != "SI")
            {
                $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                                              ->findOneBy(array ('login' => $strUserCreacion, 'estado' => 'Activo'));

                if(isset($objInfoPersona) && !empty($objInfoPersona))
                {
                    $strUsuarioAsigna  = $objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos();
                }
                else
                {
                    $strUsuarioAsigna = 'telcos';
                }
                $boolAsignarTarea = true;
            }
            else
            {
                $boolAsignarTarea  = false;
            }
        
            $arrayParametros = array (
                                           "objInfoCaso"            => $objInfoCaso,
                                           "objDetalleHipotesis"    => $objDetalleHipotesis,
                                           "intIdPersonaEmpresaRol" => $intIdPersonaEmpresaRol,
                                           "intIdCuadrilla"         => $intCuadrillaId,
                                           "intIdEmpresa"           => $intIdEmpresa,
                                           "strPrefijoEmpresa"      => $strPrefijoEmpresa,
                                           "strNombreTarea"         => $strNombreTarea,
                                           "strNombreProceso"       => $strNombreProceso,
                                           "strUserCreacion"        => $strUserCreacion,
                                           "strIpCreacion"          => $strIpCreacion,
                                           "intFormaContacto"       => $objAdmiFormaContacto->getId(),
                                           "strMotivoTarea"         => $strMotivoTarea,
                                           "strObservacionTarea"    => $strObservacionTarea,
                                           "strUsuarioAsigna"       => $strUsuarioAsigna,
                                           "strTipoAsignacion"      => $strTipoAsignacion,
                                           "strTipoTarea"           => 'T',
                                           "strTareaRapida"         => 'N',
                                           "boolAsignarTarea"       => $boolAsignarTarea,
                                           "intPuntoId"             => $intIdPunto,
                                           "strTipoReprograma"      => $strTipoReprograma,
                                           "strEstadoActual"        => $strEstadoActual,
                                           "intTiempo"              => $intTiempo,
                                           "intIdDetalle"           => $intIdDetalle,
                                           "intIdComunicacion"      => $intIdComunicacion,
                                           "intIdDocumento"         => $intIdDocumento,
                                           "intIdDocuComunica"      => $intIdDocuComunica,
                                           "intIdDetalleAsig"       => $intIdDetalleAsig,
                                           "intIdDetalleHisto"      => $intIdDetalleHisto,
                                           "intIdTareaSeguimiento"  => $intIdTareaSeguimiento
                );
            /* Creación de caso */
            $arrayRespuesta = $objSoporteService->crearTareaCasoSoporte($arrayParametros);

            if( $boolAsignarTarea && strtoupper($arrayRespuesta['mensaje']) == 'OK' )
            {
                $boolEsHal = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                       ->isAsignadoHal(array( 'intDetalleId' => $arrayRespuesta['numeroDetalle']));
                
                if ($boolEsHal)
                {
                    $strIpUsrTracking = !empty($strIpCreacion) ? $strIpCreacion : "127.0.0.1";

                    $strRespuesta= $objSoporteService->guardarTareaCaracteristica(array (
                        'strDescripcionCaracteristica' => 'CODIGO_TRABAJO',
                        'intComunicacionId'            => $arrayRespuesta['numeroTarea'],
                        'idDetalle'                    => $arrayRespuesta['numeroDetalle'],
                        'strUsrCreacion'               => $strUserCreacion,
                        'strIpCreacion'                => $strIpUsrTracking,
                        'strCodigoTrabajo'             => substr(str_shuffle("123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10)
                         ));                  

                    $strCommand = 'nohup php /home/telcos/app/console Envia:Tracking ';
                    $strCommand = $strCommand . escapeshellarg($strUserCreacion). ' ';
                    $strCommand = $strCommand . escapeshellarg($strIpUsrTracking). ' ';
                    $strCommand = $strCommand . '"Tarea Asignada" ';
                    $strCommand = $strCommand . escapeshellarg($arrayRespuesta['numeroDetalle']). ' ';
        
                    $strCommand = $strCommand .'>/dev/null 2>/dev/null &';
        
                    shell_exec($strCommand);

                }                
            }
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'EROR',
                                     'message' => 'Error en el webservice de creación de la tarea');

            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.putCrearTarea',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1'));
        }
        return $arrayRespuesta;
    }

    /**
     * Método que procesa la asignación de la solicitud de planificación y tareas de soporte
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 03-09-2018 - Se modifica el método para almacenar el json de respuesta en la INFO_ERROR
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 24-09-2018 - Se agrega una nueva validación para detectar si la tarea se registró correctamente.
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function putAsignarSolicitudTarea($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');
        $serviceUtil       = $this->get('schema.Util');
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $arrayParametros = array ('dateFechaInicio'   => $arrayData['data']['fechaInicio'],
                                      'dateFechaFin'      => $arrayData['data']['fechaFin'],
                                      'dateFeIniOrigen'   => $arrayData['data']['feIniOrigen'],
                                      'dateFeFinOrigen'   => $arrayData['data']['feFinOrigen'],
                                      'intIdCuadrilla'    => $arrayData['data']['idCuadrilla'],
                                      'intIdSolicitud'    => $arrayData['data']['idSolicitud'],
                                      'intIdComunicacion' => $arrayData['data']['idComunicacion'],
                                      'strOpcion'         => $arrayData['data']['opcion'],
                                      'strUser'           => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIp'             => ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1'));

            /* Asginación de tarea o solicitud a la cuadrilla */
            $arrayRespuesta = $objSoporteService->setAsignacionPlanifCuadrilla($arrayParametros);

            error_log('Json putAsignarSolicitudTarea: '.json_encode($arrayParametros,true));

            $serviceUtil->insertError('Telcos+',
                                      'SoporteWSController->putAsignarSolicitudTarea',
                                      'Request  '.json_encode($arrayData['data'],true).' '.
                                        'Response '.json_encode($arrayRespuesta),
                                      'TelcosHal',
                                      '127.0.0.1');

            if (!empty($arrayRespuesta) && strtoupper($arrayRespuesta['mensaje']) === 'OK'
                && (strtoupper($arrayData['data']['opcion']) === 'LIMPIARACTUALIZAR' ||
                    strtoupper($arrayData['data']['opcion']) === 'ACTUALIZAR'))
            {
                $arrayInfoCuadrillaPlanifDet = $emSoporte->getRepository('schemaBundle:InfoCuadrillaPlanifDet')
                    ->findOneByComunicacionId($arrayData['data']['idComunicacion']);

                if (empty($arrayInfoCuadrillaPlanifDet) || count($arrayInfoCuadrillaPlanifDet) < 1)
                {
                    $arrayRespuesta["mensaje"]     = 'fail';
                    $arrayRespuesta["descripcion"] = 'La tarea no se registro';
                }
            }
        }
        catch (\Exception $objException)
        {
            error_log("Error SoporteWSController.putAsignarSolicitudTarea -> Error: ".$objException->getMessage());
            $arrayRespuesta["mensaje"]     = 'fail';
            $arrayRespuesta["descripcion"] = $objException->getMessage();
        }
        return $arrayRespuesta;
    }

    /**
     * Método que obtiene el detalle de planificacion de las cuadrillas
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 01-04-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function getSolicitarDetallePlanificacion($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');

        try
        {
            $arrayParametros = array ('arrayIdCab'        => $arrayData['data']['idListaCuadrillaPlanifCab'],
                                      'arrayIdDet'        => $arrayData['data']['idListaCuadrillaPlanifDet'],
                                      'intIdZona'         => $arrayData['data']['idZona'],
                                      'intIdCuadrilla'    => $arrayData['data']['idCuadrilla'],
                                      'intIdComunicacion' => $arrayData['data']['idComunicacion'],
                                      'strFechaIni'       => $arrayData['data']['fechaIni'],
                                      'strFechaFin'       => $arrayData['data']['fechaFin'],
                                      'strEstadoCab'      => $arrayData['data']['estadoCab'],
                                      'strEstadoDet'      => $arrayData['data']['estadoDet'],
                                      'strUser'           => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIp'             => ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1'));

            $arrayRespuesta = $objSoporteService->getSolicitarDetallePlanificacion($arrayParametros);
        }
        catch (\Exception $objException)
        {
            error_log("Error SoporteWSController.getSolicitarDetallePlanificacion -> Error: ".$objException->getMessage());
            $arrayRespuesta["mensaje"]     = 'fail';
            $arrayRespuesta["descripcion"] = $objException->getMessage();
        }
        return $arrayRespuesta;
    }

    /**
     * Método que obtiene el detalle de trabajo de las cuadrillas
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 01-04-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function getSolicitarTrabajoCuadrilla($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');

        try
        {
            $arrayParametros = array ('arrayIdCab'         => $arrayData['data']['idListaCuadrillaPlanifCab'],
                                      'intIdIntervalo'     => $arrayData['data']['idIntervalo'],
                                      'intIdCuadrilla'     => $arrayData['data']['idCuadrilla'],
                                      'intIdZona'          => $arrayData['data']['idZona'],
                                      'strFechaIni'        => $arrayData['data']['fechaIni'],
                                      'strFechaFin'        => $arrayData['data']['fechaFin'],
                                      'strEstadoIntervalo' => $arrayData['data']['estadoIntervalo'],
                                      'strEstadoPlanifCab' => $arrayData['data']['estadoPlanifCab'],
                                      'strUser'            => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIp'              => ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1'));

            $arrayRespuesta = $objSoporteService->getSolicitarTrabajoCuadrilla($arrayParametros);
        }
        catch (\Exception $objException)
        {
            error_log("Error SoporteWSController.getSolicitarTrabajoCuadrilla -> Error: ".$objException->getMessage());
            $arrayRespuesta["mensaje"]     = 'fail';
            $arrayRespuesta["descripcion"] = $objException->getMessage();
        }
        return $arrayRespuesta;
    }

    /**
     * Método que obtiene las Horas de trabajo o conocido tambien como Intervalos de trabajo
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 02-05-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function getSolicitarIntervalosTrabajo($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');

        try
        {
            $arrayParametros = array ('intIdIntervalo' => $arrayData['data']['idIntervalo'],
                                      'strHoraIni'     => $arrayData['data']['horaIni'],
                                      'strHoraFin'     => $arrayData['data']['horaFin'],
                                      'strEstado'      => $arrayData['data']['estado'],
                                      'strUser'        => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIp'          => ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1'));

            $arrayRespuesta = $objSoporteService->getSolicitarIntervalosTrabajo($arrayParametros);
        }
        catch (\Exception $objException)
        {
            error_log("Error SoporteWSController.getSolicitarIntervalosTrabajo -> Error: ".$objException->getMessage());
            $arrayRespuesta["mensaje"]     = 'fail';
            $arrayRespuesta["descripcion"] = $objException->getMessage();
        }
        return $arrayRespuesta;
    }
  
     /**
     * Funcion que sirve para guardar y actualizar el evento de tareaTiempo
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     *
     * @version 1.0 12-18-2017
     */
    
    private function IngresarReasignacionCuadrilla($arrayData)
    {
          
       try
        {  
            /* @var $objSoporteService SoporteService */
           $objSoporteService  = $this->get('soporte.SoporteService');
           $arrayResultado  = $objSoporteService->GuardarReasignacionCuadrilla($arrayData['data']);
        
           if(isset($arrayResultado) && !empty($arrayResultado))
           {
                if ($arrayResultado)
                {
                    $arrayRespuesta['status']    = $this->status['OK'];
                    $arrayRespuesta['mensaje']   = "INGESO DE DATOS CON EXITO";
                }
                else
                {
                    $arrayRespuesta['status']    = $this->status['ERROR'];
                    $arrayRespuesta['mensaje']   = 'NO SE INSERTO.';
                }
           }
       }catch (Exception $a)
       {
           $arrayRespuesta['status']    = $this->status['Catch'];
           $arrayRespuesta['mensaje']   = 'NO SE INSERTO.';
           return $arrayRespuesta;
    }
        return $arrayRespuesta;
    }

    /**
     * Método que obtiene las Zonas
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 20-06-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 10-09-2018 - 1.- Se modifica el arrayParametros para enviar la información necesaria para
     *                               obtener el responsable de la zona.
     *                           2.- Se agrega nuevas validaciones para controlar las excepciones que puedan dar error.
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function getSolicitarZonas($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');
        $objServiceUtil    = $this->get('schema.Util');
        $intIdZona         = $arrayData['data']['idZona'];
        $strNombreZona     = $arrayData['data']['nombreZona'];
        $strEstadoZona     = $arrayData['data']['estado'];

        try
        {
            if (isset($intIdZona) && !empty($intIdZona) && !is_int($intIdZona))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = 'idZona Invalido';
                return $arrayRespuesta;
            }

            if (isset($strNombreZona) && !empty($strNombreZona) && !is_string($strNombreZona))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = 'nombreZona Invalido';
                return $arrayRespuesta;
            }

            if(isset($strEstadoZona) && !empty($strEstadoZona) && !is_string($strEstadoZona))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = 'estado Invalido';
                return $arrayRespuesta;
            }

            $arrayParametros = array ('intIdZona'         => $intIdZona,
                                      'strNombreZona'     => $strNombreZona,
                                      'strEstadoZona'     => $strEstadoZona,
                                      'boolSubQuery'      => true,
                                      'strCaracteristica' => 'RESPONSABLE_ZONA',
                                      'strEstadoAc'       => 'Activo',
                                      'strEstadoIperc'    => 'Activo',
                                      'strEstadoIper'     => 'Activo',
                                      'strEstadoIp'       => 'Activo',
                                      'strUser'           => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIp'             => ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1')
                                     );

            $arrayRespuesta = $objSoporteService->getSolicitarZonas($arrayParametros);
        }
        catch (\Exception $objException)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.getSolicitarZonas',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1')
                                        );
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = $objException->getMessage();
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de obtener las partes afectadas de las tareas internas.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 16-07-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function getPartesAfectadasTareas($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');
        $objServiceUtil    = $this->get('schema.Util');

        try
        {
            if(!isset($arrayData['data']['idDetalle']) || empty($arrayData['data']['idDetalle']))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "idDetalle invalido..!!";
                return $arrayRespuesta;
            }

            $arrayParametros = array ('intDetalleId' => $arrayData['data']['idDetalle'],
                                      'strUser'      => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIp'        => ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));

            $arrayRespuesta = $objSoporteService->getPartesAfectadasTareas($arrayParametros);
        }
        catch (\Exception $objException)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.getPartesAfectadasTareas',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));
            $arrayRespuesta = array();
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = "Fallo en el metodo SoporteWSController.getPartesAfectadasTareas";
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de generar las horas de trabajo dentro del detalle de planificacion.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 28-06-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function putActualizarHorasTrabajoHAL($arrayData)
    {
        $objSoporteService       = $this->get('soporte.SoporteService');
        $objServiceUtil          = $this->get('schema.Util');
        $intIdCuadrillaPlanifCab = $arrayData['data']['idCuadrillaPlanifCab'];
        $strHoraInicio           = $arrayData['data']['horaInicio'];
        $strHoraFin              = $arrayData['data']['horaFin'];
        $strFechaTrabajo         = $arrayData['data']['fechaTrabajo'];

        try
        {
            if (is_null($intIdCuadrillaPlanifCab) ||
                is_null($strHoraInicio)           ||
                is_null($strHoraFin)              ||
                is_null($strFechaTrabajo))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = 'NINGUNO DE LOS SIGUIENTES ATRIBUTOS PUEDEN SER NULOS: '
                    . '(idCuadrillaPlanifCab, horaInicio, horaFin, fechaTrabajo)';
                return $arrayRespuesta;
            }

            //Parseo de la fecha y horas de trabajo
            $objFechaTrabajo = date("Y/m/d", strtotime($strFechaTrabajo));
            $objHora1        = new \DateTime($objFechaTrabajo.' '.$strHoraInicio);
            $objHoraInicio   = $objHora1->format("Y/m/d H:i");
            $objHora2        = new \DateTime($objFechaTrabajo.' '.$strHoraFin);
            $objHoraFin      = $objHora2->format("Y/m/d H:i");

            //Enviar intervalo editado de inicio
            $arrayParametros = array ('intIdCabecera'    => $intIdCuadrillaPlanifCab,
                                      'objHoraInicio'    => $objHoraInicio,
                                      'objHoraFin'       => $objHoraFin,
                                      'objFechaRegistro' => $objFechaTrabajo,
                                      'strTipoProceso'   => 'Automatico',
                                      'intIdPersonaRol'  => $arrayData['data']['idPersonaRol'],
                                      'strUsrCreacion'   => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIpCreacion'    => ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));

            $arrayRespuesta = $objSoporteService->setActualizarHorasTrabajoHAL($arrayParametros);
        }
        catch (\Exception $objException)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.putActualizarHorasTrabajoHAL',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1')
                                        );
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = $objException->getMessage();
        }
        return $arrayRespuesta;
    }
    
    /**
     * Método que obtiene puerto,elemento, modelo e interfaz del splitter y olt por servicio.
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 11-07-2018
     *
     * Se agregara tipo de orden para extraccion de data origen de traslados.
     * Se obtiene información de data tecnica del punto anterior.
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.1 18-02-2022
     * 
     * 
     * Se modifican los valores retornados de la extracion de origen de traslados
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.2 18-05-2022
     *
     * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec> 
     * @version 1.3 14-03-2023 - Se agrega validacion por codigo empresa para Ecuanet.
     * 
     * @param  $arrayData
     * @return $arrayResultado
     */
    private function getDatosSplitterOLT($arrayData)
    {
        $objServiceUtil   = $this->get('schema.Util');
        $emSoporte        = $this->getDoctrine()->getManager("telconet_soporte");  
        $strCodigoEmpresa = $arrayData['data']['codEmpresa'] ;
        $strTipoServicio  = $arrayData['data']['tipoServicio'];
        $intIdServicio    = $arrayData['data']['idServicio'];
        $emComercial      = $this->getDoctrine()->getManager("telconet");
        $serviceServicioTecnico = $this->get('tecnico.InfoServicioTecnico');
        $boolEsTrasladoDifTec   = false;
        $arrayDataPuntoAnterior = null;
        try
        {
            $objProdInternet   = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                             ->findOneBy(array("nombreTecnico" => "INTERNET",
                                                               "empresaCod"    =>  $strCodigoEmpresa,
                                                               "estado"        => "Activo"));
            $objServicioInternet = $emComercial->getRepository('schemaBundle:InfoServicio')
                                               ->find($intIdServicio);
            //Todo esto se va a ejecutar siempre y cuando MD // Valida si es traslado
            if(($strCodigoEmpresa == '18' || $strCodigoEmpresa == '33') && 
            $strTipoServicio == "TRASLADO" && is_object($objProdInternet) && is_object($objServicioInternet))
            {
                //validar caracteristica "DIFERENTE TECNOLOGIA FACTIBILIDAD"
                $objServProdCaract = $serviceServicioTecnico->getServicioProductoCaracteristica($objServicioInternet,
                                                                                                'DIFERENTE TECNOLOGIA FACTIBILIDAD',
                                                                                                $objProdInternet);
                if(is_object($objServProdCaract))//Validar la caracteristica  Si existe
                {
                    $boolEsTrasladoDifTec = true ;
                    //Validar Caracteristica "TRASLADO"
                    $objServProdCaract = $serviceServicioTecnico->getServicioProductoCaracteristica($objServicioInternet,
                                                                                                    "TRASLADO",
                                                                                                    $objProdInternet);
                    if(is_object($objServProdCaract))
                    {
                        $objServicio    = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->find($objServProdCaract->getValor());
                        if(is_object($objServicio))
                        {
                            $objInfoServicioTecnico = $emSoporte->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy( array( 'servicioId' => $objServicio->getId()));
                            //ELEMENTO_ID	ELEMENTO_CLIENTE_ID	 ELEMENTO_CONTENEDOR_ID
                            $strIdElementOlt      = $objInfoServicioTecnico->getElementoId();
                            $strIdElementCliente  = $objInfoServicioTecnico->getElementoClienteId();
                            $strIdElementSplitter = $objInfoServicioTecnico->getElementoConectorId();
                            $objElementoOlt             = $emSoporte->getRepository('schemaBundle:InfoElemento')
                                                                    ->find($strIdElementOlt);
                            $objElementoElementoCliente = $emSoporte->getRepository('schemaBundle:InfoElemento')
                                                                    ->find($strIdElementCliente);
                            $objElementoSplitter        = $emSoporte->getRepository('schemaBundle:InfoElemento')
                                                                    ->find($strIdElementSplitter);
                            $arrayElementosEncontrados = array(array('nombreOlt' => $objElementoOlt->getNombreElemento(),
                                                                'nombreSplitter' => $objElementoSplitter->getNombreElemento(),
                                                                'nombreOnt' => $objElementoElementoCliente->getNombreElemento(),
                                                                'serieOnt' => $objElementoElementoCliente->getSerieFisica()));
                            $strNombrePunto = null;
                            if($objServicio->getPuntoId()->getNombrePunto()!=null)
                            {
                                $strNombrePunto = $objServicio->getPuntoId()->getNombrePunto();
                            }
                            else
                            {
                                $objPersona    = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                                $strNombrePunto = $objPersona->getNombres()." ".$objPersona->getApellidos();
                            }
                            $arrayDataPuntoAnterior = array(
                                'direccionPunto'=>$objServicio->getPuntoId()->getDireccion(),
                                'descripcionPunto'=>$objServicio->getPuntoId()->getDescripcionPunto(),
                                'nombrePunto'=>$strNombrePunto,
                                'elementos' => $arrayElementosEncontrados
                            );
                        }
                    }
                }
            }
            $arrayRespuesta   = $emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')->getInfoOltySplitterPorServicio($arrayData);
            if(isset($arrayRespuesta) && !empty($arrayRespuesta))
            {
                $arrayResultado['data']      = $arrayRespuesta;
                $arrayResultado['status']    = $this->status['OK'];
                $arrayResultado['mensaje']   = 'CONSULTA EXITOSA';
            }
            else{
                $arrayResultado['data']      = null;
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = 'NO SE ENCONTRO REGISTRO';
            }
        }
        catch (\Exception $objException)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.getDatosSplitterOLT',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          '127.0.0.1'
                                        );
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = $objException->getMessage();
        }

        $arrayResultado["data"]["extraData"] = array('isDiferenteTecnologia' => $boolEsTrasladoDifTec,
                                                     'dataPuntoAnterior'     => $arrayDataPuntoAnterior);
        return $arrayResultado;
    }

    /**
     * Método encargado de actualizar el campo VISUALIZAR_MOVIL, para la visualización de las tareas en el telcos móvil
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 - 17-07-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta;
     */
    private function putVisualizarMovil($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');
        $objServiceUtil    = $this->get('schema.Util');

        try
        {
            if(!isset($arrayData['data']['listaIdComunicacion']) || empty($arrayData['data']['listaIdComunicacion']))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "listaIdComunicacion Inválido";
                return $arrayRespuesta;
            }

            if(!is_array($arrayData['data']['listaIdComunicacion']))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "listaIdComunicacion no es un array";
                return $arrayRespuesta;
            }

            if(!isset($arrayData['data']['visualizarMovil']) || empty($arrayData['data']['visualizarMovil']))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "visualizarMovil Invalido";
                return $arrayRespuesta;
            }

            if(strtoupper($arrayData['data']['visualizarMovil']) != 'S' && strtoupper($arrayData['data']['visualizarMovil']) != 'N')
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "El valor de visualizarMovil no puede ser diferente de S o N";
                return $arrayRespuesta;
            }

            if(!is_null($arrayData['data']['visualizarMovil']) && !is_string($arrayData['data']['visualizarMovil']))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "El valor de visualizarMovil no es un caracter";
                return $arrayRespuesta;
            }

            $arrayParametros = array ('arrayIdComunicacion' => $arrayData['data']['listaIdComunicacion'],
                                      'strVisualizarMovil'  => $arrayData['data']['visualizarMovil'],
                                      'strUsuario'          => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIp'               => ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));

            $arrayRespuesta = $objSoporteService->setVisualizarMovil($arrayParametros);
        }
        catch (\Exception $objException)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.putVisualizarMovil',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));
            $arrayRespuesta = array();
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = "Fallo en el método SoporteWSController.putVisualizarMovil";
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de verificar si existen casos duplicados
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 - 17-11-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta;
     */
    private function getDuplicidadCasoPorLogin($arrayData)
    {
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $arrayRespuesta     = array();
        $arrayDataResultado = array();

        $strMensaje = "Consulta Exitosa!";
        $arrayRespuesta['status']    = $this->status['OK'];

        try
        {
            $arrayParametros = array('strCodEmpresa'           => $arrayData['data']['codEmpresa'],
                                     'intHoras'                => $arrayData['data']['horas'],
                                     'intIdPersonaEmpresaRol'  => $arrayData['data']['idPersonaEmpresaRol'],
                                     'strLogin'                => $arrayData['data']['login']
                                    );

            $arrayResultado     = $emSoporte->getRepository('schemaBundle:InfoCasoHistorial')
                                            ->getHistorialCasoPorDuplicidad($arrayParametros);

            $intCantidadRegistros = 0;
            $intDuplicidad = 0;
            $strFecha = '';
            $strEstado = '';
            $strNumeroCaso = '';

            if(isset($arrayResultado))
            {

                if(!empty($arrayResultado))
                {

                    $intCantidadRegistros = count($arrayResultado);

                    $strFecha      = $arrayResultado[0]['fechaCreacion'];
                    $strEstado     = $arrayResultado[$intCantidadRegistros - 1]['estado'];
                    $strNumeroCaso = $arrayResultado[0]['numeroCaso'];
                    $intDuplicidad = 1;

                    $arrayDataResultado = array('duplicidad'    => $intDuplicidad,
                                                'numeroCaso'    => $strNumeroCaso,
                                                'login'         => $arrayData['data']['login'],
                                                'estadoActual'  => $strEstado,
                                                'fechaCreacion' => $strFecha,
                                                'fechaActual'   => date('Y-m-d H:m:s'));
                }
                else
                {
                    $intDuplicidad = 0;
                    $arrayDataResultado = array('duplicidad'   => $intDuplicidad);
                }
            }
            else
            {
                $arrayRespuesta['status']    = $this->status['ERROR'];
                $strMensaje   = "Consulta No Exitosa!";
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta['status']    = $this->status['ERROR'];
            $strMensaje = $e->getMessage();
            return $arrayRespuesta;
        }

        $arrayRespuesta['mensaje']   = $strMensaje;
        $arrayRespuesta['data']    = $arrayDataResultado;
        return $arrayRespuesta;
    }

    /**
     * Método que actualiza el senderId para el envio de notificaciones.
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 - 17-11-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta;
     */
    private function putActualizarTokenFCM($arrayData)
    {
        $arrayRespuesta = array();
        try
        {
            $emSoporte    = $this->getDoctrine()->getManager("telconet_soporte");
            $emSoporte->beginTransaction();

            $intIdPersona = intval ($arrayData['data']['personaId'], 10);
            $strImei      = $arrayData['data']['imei'];
            $strTokenFCM  = $arrayData['data']['tokenFCM'];

            $arrayParametros    = array('codigoDispositivo' => $strImei,
                                        'personaId'         => $intIdPersona,
                                        'estado'            => 'Activo');

            $objDispositivo = $emSoporte->getRepository("schemaBundle:AdmiDispositivoApp")->findOneBy($arrayParametros);

            $strStatus = 'OK';
            $strMensaje = 'No Actualizado';

            if(is_object($objDispositivo) && $objDispositivo->getTokenFCM() != $strTokenFCM)
            {
                $objDispositivo->setTokenFCM($strTokenFCM);
                $emSoporte->persist($objDispositivo);
                $emSoporte->flush();

                if ($emSoporte->getConnection()->isTransactionActive())
                {
                    $emSoporte->getConnection()->commit();
                    $strMensaje = 'Actualizado';
                }
            }
        }
        catch (\Exception $ex)
        {
            $serviceUtil = $this->get('schema.Util');
            $serviceUtil->insertError( 'Telcos+',
                                       'SoporteWSController->putActualizarTokenFCM()',
                                       'Error al actualizar token FCM '.$ex->getMessage(),
                                       $arrayData['user'],
                                       "127.0.0.1" );
            $strStatus = 'ERROR';
            $strMensaje = $ex->getMessage();
        }
        
        $arrayRespuesta['status'] = $strStatus;
        $arrayRespuesta['mensaje'] = $strMensaje;
        return $arrayRespuesta;
    }

    /**
     * Método encargado de obtener las tareas con la característica ATENDER_ANTES.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 31-08-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function getTareasCaractAtenderAntes($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');
        $objServiceUtil    = $this->get('schema.Util');

        try
        {
            if(!isset($arrayData['data']['listaIdComunicacion']) || empty($arrayData['data']['listaIdComunicacion']))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "listaIdComunicacion Invalido";
                return $arrayRespuesta;
            }

            if(!is_array($arrayData['data']['listaIdComunicacion']))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "listaIdComunicacion no es una lista";
                return $arrayRespuesta;
            }

            $arrayParametros = array ('arrayIdComunicacion' => $arrayData['data']['listaIdComunicacion'],
                                      'strEstado'           => 'Activo',
                                      'strCaracteristica'   => 'ATENDER_ANTES',
                                      'strUsuario'          => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                      'strIp'               => ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));

            $arrayRespuesta = $objSoporteService->getTareasCaractAtenderAntes($arrayParametros);
        }
        catch (\Exception $objException)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.getTareasCaractAtenderAntes',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));
            $arrayRespuesta = array();
            $arrayRespuesta["status"]  = 'fail';
            $arrayRespuesta["mensaje"] = "Fallo en el método SoporteWSController.getTareasCaractAtenderAntes";
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de ingresar la característica ATENDER_ANTES.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 03-09-2018
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function setTareasCaractAtenderAntes($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');
        $objServiceUtil    = $this->get('schema.Util');
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $emComercial       = $this->getDoctrine()->getManager("telconet");
        $arrayParametros   = array();
        $arrayRespuesta    = array();
        $boolGuardar       = false;

        try
        {
            if(!isset($arrayData['data']['idComunicacion']) || empty($arrayData['data']['idComunicacion']))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "idComunicacion Invalido";
                return $arrayRespuesta;
            }

            if(!isset($arrayData['data']['atenderAntes']) || empty($arrayData['data']['atenderAntes']))
            {
                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["mensaje"] = "atenderAntes Invalido";
                return $arrayRespuesta;
            }

            $arrayParametros['intIdComunicacion'] = $arrayData['data']['idComunicacion'];
            $arrayParametros['intIdDetalle']      = $arrayData['data']['idDetalle'];
            $arrayParametros['strValor']          = $arrayData['data']['atenderAntes'];
            $arrayParametros['strEstado']         = 'Activo';
            $arrayParametros['strUsrCreacion']    = ($arrayData['user'] ? $arrayData['user'] : 'Telcos');
            $arrayParametros['strIpCreacion']     = ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1');
            
            $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(array ('descripcionCaracteristica' => 'ATENDER_ANTES',
                               'estado'                    => 'Activo'));

            $objInfoTareaCaracteristica = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                ->findOneBy(array ('tareaId' => $arrayParametros['intIdComunicacion'],
                                   'caracteristicaId' => $objAdmiCaracteristica->getId()));

            if (is_object($objInfoTareaCaracteristica))
            {
                $arrayParametros['strOpcion']                  = 'edit';
                $arrayParametros['ojbInfoTareaCaracteristica'] = $objInfoTareaCaracteristica;
                $boolGuardar                                   = true;
            }
            else
            {
                    if (is_object($objAdmiCaracteristica))
                    {
                        $arrayParametros['strOpcion']           = 'new';
                        $arrayParametros['intCaracteristicaId'] = $objAdmiCaracteristica->getId();
                        $boolGuardar                            = true;
                    }
            }

            if ($boolGuardar)
            {
                $arrayRespuesta = $objSoporteService->setTareaCaracteristica($arrayParametros);
            }

            error_log('Json setTareasCaractAtenderAntes: '.json_encode($arrayParametros,true));
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController->setTareasCaractAtenderAntes',
                                          json_encode($arrayData['data'],true),
                                         'TelcosHal',
                                         '127.0.0.1');
        }
        catch (\Exception $objException)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.setTareasCaractAtenderAntes',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));

            $arrayRespuesta["mensaje"]     = 'fail';
            $arrayRespuesta["descripcion"] = "Fallo en el método SoporteWSController.setTareasCaractAtenderAntes";
        }
        return $arrayRespuesta;
    }

    /**
     * Método que valida que la personaId exista en la app TnCliente.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 14-11-2018
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.1 21-12-2018 - Si es administrador no se valida la persona id
     *
     * @param  $arrayData
     * @throws \Exception
     */
    private function validarPersonaId($arrayData)
    {
        if(isset($arrayData['tipoUsuario']) && $arrayData['tipoUsuario'] == self::ID_ADMINISTRADOR)
        {
            return "Admin";
        }
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        $objTnCliente   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                      ->findOneBy(array(
                                                        'personaId'     => $arrayData['data']['idPersona'],
                                                        'estado'        => 'Activo',
                                                        'empresaRolId'  => 1
                                                        ));
        if(!is_object($objTnCliente))
        {
            throw new \Exception('No existen registros.');
        }
        $objCliente     = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                      ->findOneBy(array(
                                                        'personaEmpresaRolId' => $objTnCliente->getId(),
                                                        'valor'               => $arrayData['user']
                                                        ));
        if(!is_object($objCliente))
        {
            throw new \Exception('No existen registros.');
        }
    }

    /**
     * Método que valida que la personaEmpresaRolId exista en la app TnCliente.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 14-11-2018
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.1 21-12-2018 - Si es administrador no se valida la persona empresa rol id
     *
     * @param  $arrayData
     * @throws \Exception
     */
    private function validarPersonaEmpresaRolId($arrayData)
    {
        if(isset($arrayData['tipoUsuario']) && $arrayData['tipoUsuario'] == self::ID_ADMINISTRADOR)
        {
            return "Admin";
        }
        $emComercial    = $this->getDoctrine()->getManager("telconet");
        //Verificar quien consulta el perfil corresponde a la empresa que solicita.
        $objTnCliente   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                      ->findOneBy(array(
                                                        'personaEmpresaRolId' => $arrayData['data']['idPersonaEmpresaRol'],
                                                        'valor'               => $arrayData['user']
                                                        ));
        if(!is_object($objTnCliente))
        {
            throw new \Exception('No existen registros.');
        }
        if((isset($arrayData['data']['idPersona']) && !empty($arrayData['data']['idPersona'])) &&
            $objTnCliente->getPersonaEmpresaRolId()->getPersonaId()->getId() != $arrayData['data']['idPersona'])
        {
            throw new \Exception('No existen registros.');
        }
    }

    /**
     * Método que elimina de la tabla ADMI_DISPOSITIVO_APP el registro de un dispositivo y almacena en el historico
     * de la tabla INFO_HISTORIAL_INGRESO_APP.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 28-11-2018
     *
     * @param array $arrayData[
     *                          [data][
     *                                  idPersona:          integer:    id Persona de la empresa.
     *                                  codigoDispositivo:  string:     imei o codigo del dispositivo.
     *                                  ipAcceso:           string:     ip desde donde se realiza la petición.
     *                                  latitud:            string:     latitud.
     *                                  longitud:           string:     longitud.
     *                                ],
     *                          user:   string:     Usuario que realiza la petición.
     *                        ]
     * @throws \Exception
     */
    private function putFinalizarSessionRemoto($arrayData)
    {
        $objServiceUtil    = $this->get('schema.Util');
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $strEstadoSession  = "Eliminado";
        $arrayRespuesta    = array();
        try
        {
            $objPersona     = $emSoporte->getRepository('schemaBundle:InfoPersona')
                                        ->findOneById($arrayData['data']['idPersona']);
            //Eliminar registro del dispositivo
            $arrayParamDisp = array('codigoDispositivo'     => $arrayData['data']['codigoDispositivo'],
                                    'personaId'             => $objPersona);
            $objDispositivo = $emSoporte->getRepository('schemaBundle:AdmiDispositivoApp')
                                        ->findOneBy($arrayParamDisp);
            $emSoporte->remove($objDispositivo);
            $emSoporte->flush();

            //Almacenar Historial de dispositivo como Finalizado Session Remota seteamos estado Eliminado.
            $objHistorial = new InfoHistorialIngresoApp;
            $objHistorial->setPersonaId($objPersona);
            $objHistorial->setIpAcceso(($arrayData['data']['ipAcceso']   ? $arrayData['data']['ipAcceso']   : '127.0.0.1'));
            $objHistorial->setCodigoDispositivo($arrayData['data']['codigoDispositivo']);
            $objHistorial->setEstado($strEstadoSession);
            $objHistorial->setLatitud($arrayData['data']['latitud']);
            $objHistorial->setLongitud($arrayData['data']['longitud']);
            $objHistorial->setUsrCreacion($arrayData['user']);
            $objHistorial->setFeCreacion(new \DateTime('now'));
            $objHistorial->setIpCreacion(($arrayData['data']['ipAcceso']   ? $arrayData['data']['ipAcceso']   : '127.0.0.1'));
            $objHistorial->setUsrUltMod($arrayData['user']);
            $objHistorial->setFeUltMod(new \DateTime('now'));
            $emSoporte->persist($objHistorial);
            $emSoporte->flush();

            if ($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->getConnection()->commit();
            }
            $arrayRespuesta['status']   = $this->status['OK'];
            $arrayRespuesta['mensaje']  = "Dispositivo eliminado.";
        }
        catch (\Exception $objException)
        {
            if ($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->getConnection()->rollback();
            }
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.putFinalizarSessionRemoto',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));

            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
        }
        return $arrayRespuesta;
    }

    /**
     * Método para validar si el dispositivo no se encuentra bloqueado.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 28-11-2018
     *
     * @param array $arrayData[
     *                          idPersona:          integer:    id Persona de la empresa.
     *                          codigoDispositivo:  string:     imei o codigo del dispositivo.
     *                          user:               string:     Usuario que realiza la petición.
     *                        ]
     * @throws \Exception
     */
    private function verificarBloqueoDispositivo($arrayData)
    {
        $objServiceUtil    = $this->get('schema.Util');
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        $arrayRespuesta    = array();
        try
        {
            $objPersona     = $emSoporte->getRepository('schemaBundle:InfoPersona')
                                        ->findOneById($arrayData['idPersona']);

            $arrayParaDispo = array('codigoDispositivo'      => $arrayData['codigoDispositivo'],
                                    'personaId'              => $objPersona->getId());
            $arrayDispositi = $emSoporte->getRepository('schemaBundle:AdmiDispositivoApp')
                                        ->getDispositivoApp($arrayParaDispo);

            if(isset($arrayDispositi) && !empty($arrayDispositi))
            {
                $arrayRespuesta['status']   = $this->status['OK'];
                $arrayRespuesta['mensaje']  = $this->mensaje['OK'];
            }
            else
            {
                $arrayRespuesta['status']   = $this->status['NULL'];
                $arrayRespuesta['mensaje']  = $this->mensaje['NULL'];
            }
        }
        catch (\Exception $objException)
        {
            if ($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->getConnection()->rollback();
            }
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.verificarBloqueoDispositivo',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          '127.0.0.1');

            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
        }
        return $arrayRespuesta;
    }

    /**
     * Método para actualizar información del dispositivo.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 28-11-2018
     *
     * @param array $arrayData[
     *                          idPersona:          integer:    id Persona de la empresa.
     *                          codigoDispositivo:  string:     imei o codigo del dispositivo.
     *                          descripcion:        string:     Descripción del dispositivo.
     *                        ]
     * @throws \Exception
     */
    private function putActualizaInfoDispositivo($arrayData)
    {
        $objServiceUtil    = $this->get('schema.Util');
        $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
        try
        {
            $emSoporte->beginTransaction();
            $objDispositivo = $emSoporte->getRepository('schemaBundle:AdmiDispositivoApp')
                                        ->findOneBy(array('personaId'           => $arrayData['data']['idPersona'],
                                                          'codigoDispositivo'   => $arrayData['data']['codigoDispositivo']));
            if(is_object($objDispositivo))
            {
                $objDispositivo->setDescripcion($arrayData['data']['descripcion']);
                $objDispositivo->setFeUltMod(new \DateTime('now'));
                $emSoporte->persist($objDispositivo);
                $emSoporte->flush();

                if ($emSoporte->getConnection()->isTransactionActive())
                {
                    $emSoporte->getConnection()->commit();
                }
                $arrayRespuesta['status']   = $this->status['OK'];
                $arrayRespuesta['mensaje']  = "Información del dispositivo actualizado.";
            }
            else
            {
                $arrayRespuesta['status']   = $this->status['ERROR'];
                $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
            }

        }
        catch (\Exception $objException)
        {
            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.putActualizaInfoDispositivo',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1'));

            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
        }
        return $arrayRespuesta;
    }
	
    /**
     * Función que obtiene el resumen de tareas por persona
     *
     * @author: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 05-12-2018 
	 *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getResumenTareasPersona($arrayData)
    {
		$serviceUtil            = $this->get('schema.Util');
		$objSoporte             = $this->get('soporte.SoporteService');
        $strMensaje             = "No se pudieron obtener los materiales!";
		 $arrayResultado        = [];
		$arrayRespuesta         = [];
        try
        {  
			$arrayRespuesta = $objSoporte->getResumenTareasPersona($arrayData['data']);
		}
        catch(\Exception $ex)
        {
			error_log($ex->getMessage());
            $serviceUtil->insertError(  'Telcos Mobile', 
                                        'SoporteWSController.getResumenTareasPersona', 
                                        $ex->getMessage(),
                                        $arrayData['user'],
                                        "127.0.0.1");
            
            if($ex->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($ex->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $arrayResultado;
        }
        
		$arrayResultado['data']          = $arrayRespuesta;
		$arrayResultado['status']        = $this->status['OK'];
		$arrayResultado['mensaje']       = $this->mensaje['OK'];
        
        return $arrayResultado;
    }
	
	/**
     * Función que obtiene el resumen de tipos tareas por tiempo
     *
     * @author: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 12-12-2018 
	 *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getResumenTipoTareasTiempo($arrayData)
    {
		$serviceUtil            = $this->get('schema.Util');
		$objSoporte             = $this->get('soporte.SoporteService');
        $strMensaje             = "No se pudieron obtener los materiales!";
		 $arrayResultado        = [];
		$arrayRespuesta         = [];
        try
        {  
			$arrayRespuesta = $objSoporte->getResumenTipoTareasTiempo($arrayData['data']);
		}
        catch(\Exception $ex)
        {
			error_log($ex->getMessage());
            $serviceUtil->insertError(  'Telcos Mobile', 
                                        'SoporteWSController.getResumenTipoTareasTiempo', 
                                        $ex->getMessage(),
                                        $arrayData['user'],
                                        "127.0.0.1");
            
            if($ex->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($ex->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $arrayResultado;
        }
        
		$arrayResultado['data']          = $arrayRespuesta;
		$arrayResultado['status']        = $this->status['OK'];
		$arrayResultado['mensaje']       = $this->mensaje['OK'];
        
        return $arrayResultado;
    }
	
	/**
     * Función que obtiene el resumen de tareas por persona
     *
     * @author: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 05-12-2018 
	 *
     * @param array $arrayData
     * @return array $arrayResultado
     */
    private function getResumenEventosPersona($arrayData)
    {
		$serviceUtil  	= $this->get('schema.Util');
		$objSoporte 	= $this->get('soporte.SoporteService');
        $strMensaje  	= "No se pudieron obtener los eventos!";
		$arrayResultado	= [];
        $arrayRespuesta	= [];
        $arrayData['data']['objUtilService']    = $serviceUtil ;
        try
        {  
			$arrayRespuesta = $objSoporte->getResumenEventosPersona($arrayData['data']);
		}
        catch(\Exception $ex)
        {
			error_log($ex->getMessage());
            $serviceUtil->insertError(  'Telcos Mobile', 
                                        'SoporteWSController.getResumenEventosPersona', 
                                        $ex->getMessage(),
                                        $arrayData['user'],
                                        "127.0.0.1");
            
            if($ex->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($ex->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }
            
            return $arrayResultado;
        }
        
		$arrayResultado['data']		= $arrayRespuesta;
		$arrayResultado['status']	= $this->status['OK'];
		$arrayResultado['mensaje']	= $this->mensaje['OK'];
        
        return $arrayResultado;
    }

    /**
     * Función que muestra el detalle de una tarea de Netvoice
     *
     * @author Ronny Morán Chancay <rmoranc@telconet.ec>
     * @version 1.0 03/12/2019
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 23/01/2020
     * Se modifica el manejo de errores en la función. 
     *
     * @param type $arrayData
     * @return type
     */
    public function getDetalleTareaNetvoice($arrayData)
    {
        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $serviceUtil    = $this->get('schema.Util');
        try
        {
            $arrayParametros['idDetalle']   = $arrayData['data']['idDetalle'];                               
            $strInformacionNetvoice         = $emSoporte->getRepository('schemaBundle:InfoPersona')->obtenerInformacionNetvoice($arrayParametros);
            
            $arrayRespuesta['status']       = $this->status['OK'];
            $arrayRespuesta['mensaje']      = $this->mensaje['OK'];
            $arrayRespuesta['data']         = $strInformacionNetvoice;
        }
        catch (\Exception $ex)
        {
            $serviceUtil->insertError(  'Telcos+', 
                                        'SoporteWSController.getDetalleTareaNetvoice', 
                                        $ex->getMessage(),
                                        $arrayData['user'],
                                        "127.0.0.1");

            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
        }
        return $arrayRespuesta;
    }
    
    /**
     * 
     * Método encargado de enviar el llamar al método getIdentifyClient
     * de SoporteServicio
     * 
     * @author Otto Navas Collao <onavas@telconet.ec>
     * @version 1.0 19-11-2019
     * 
     * @author Néstor Naula <Nestor Naula>
     * @version 1.1 02-09-202 - Se agrega la excepción y se envía los parámetros de conexión
     * @since 1.0
     * 
     * @return arreglo(json)
     */
    private function getIdentifyClient($arrayData)
    {    
	    $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $arrayRespuesta = array();
        
        try
        {
            $strUserSoporte = $this->container->getParameter('user_soporte');
            $strPassSoporte = $this->container->getParameter('passwd_soporte');
            $strDnsSoporte  = $this->container->getParameter('database_dsn');
            $strUser        = $arrayData['user'];
            $strIP          = $arrayData['data']['ip'];
            $strTimeStamp   = date('Y-m-d H:i:s', strtotime($arrayData['data']['timestamp']));
            $strOriginID    = $arrayData['source']['originID']; 

            $arrayParametros = array('strUserSoporte' => $strUserSoporte,
                                     'strPassSoporte' => $strPassSoporte,
                                     'strDnsSoporte'  => $strDnsSoporte,
                                     'strUser'        => $strUser,
                                     'strIP'          => $strIP,
                                     'strTimeStamp'   => $strTimeStamp,
                                     'strOriginID'    => $strOriginID);

            $arrayResultado = $emSoporte->getRepository('schemaBundle:InfoIncidenciaCab')
                                                    ->getIdentifyClient($arrayParametros);
        } 
        catch (\Exception $objException)
        {  
            $arrayRespuesta = 'Error en el WebService getIdentifyClient: '.$objException->getMessage();         
            
            $arrayResultado = array ('status'        =>'500',
                                     'data'          =>$arrayRespuesta,
                                     'mensaje'       =>'PROCESO NO REALIZADO');

            $serviceUtil->insertError(  'Telcos+', 
                                        'SoporteWSController.getIdentifyClient', 
                                         $objException->getMessage(),
                                         $arrayData['user'],
                                        "127.0.0.1");
        }
        return $arrayResultado;        
    }

    /**
     * Actualización: Se elimina conversión a minúsculas del login y el login_aux recibido por parámetros
     * @author Andrés Montero H <amontero@telconet.ec>
     * @version 1.2 28-12-2020
     * 
     * Actualización: Se realiza validación que exista login y login_aux recibidos por parámetros
     * @author Andrés Montero H <amontero@telconet.ec>
     * @version 1.1 14-12-2020
     * 
     * Función que retorna la información del cliente de zabbix en base
     * al login
     *
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 18-05-2020
     *
     * @param type $arrayData
     * @return type
     */
    public function getInfoClienteZabbix($arrayData)
    {
        $serviceUtil                  = $this->get('schema.Util');
        $serviceInfoServicioTecnico   = $this->get('tecnico.InfoServicioTecnico');
        $objEmComercial               = $this->getDoctrine()->getManager("telconet");

        try
        {
            $arrayParametros['strProceso']      = $arrayData['op'];
            $arrayParametros['strUsrCreacion']  = $arrayData['user'];
            $arrayParametros['strIpCreacion']   = $arrayData['source']['originID'];
            $arrayParametros['strLogin']        = $arrayData['data']['login']; 
            $arrayParametros['strLoginAux']     = $arrayData['data']['loginAux']; 
            $arrayParametros['strValidarFact']  = "NO";

            if(!isset($arrayData['data']['loginAux']) || empty($arrayData['data']['loginAux']))
            {
                throw new \Exception("Error : No se ha enviado el login Auxiliar");
            }

            if(!isset($arrayData['data']['login']) || empty($arrayData['data']['login']))
            {
                throw new \Exception("Error : No se ha enviado el login");
            }

 	
            //Valida login del Punto
            $arrayInfoPunto = $objEmComercial->getRepository("schemaBundle:InfoPunto")
                                             ->findBy(array('login'=>$arrayData['data']['login']));
            if(!isset($arrayInfoPunto) || empty($arrayInfoPunto))
            {
                throw new \Exception("Error : No se encontró información del login (".$arrayData['data']['login'].")");
            }

            //Valida login_aux Servicio
            $arrayInfoServicio = $objEmComercial->getRepository("schemaBundle:InfoServicio")
                                                ->findBy(array('loginAux'=>$arrayData['data']['loginAux']));
            if(!isset($arrayInfoServicio) || empty($arrayInfoServicio))
            {
                throw new \Exception("Error : No se encontró información del servicio con login_aux (".$arrayData['data']['loginAux'].")");
            }

            $arrayInfoCliente  = $serviceInfoServicioTecnico->enviarInfoClienteZabbix($arrayParametros);
            
            if(isset($arrayInfoCliente) && !empty($arrayInfoCliente) && is_array($arrayInfoCliente))
            {
                $arrayRespuesta['status']       = $this->status['OK'];
                $arrayRespuesta['mensaje']      = $this->mensaje['OK'];
                $arrayRespuesta['data']         = $arrayInfoCliente;
            }
            else
            {
                $arrayRespuesta['status']       = $this->status['ERROR'];
                $arrayRespuesta['mensaje']      = "No se encontró información del cliente";
                $arrayRespuesta['data']         = $arrayInfoCliente;
            }
            
        }
        catch (\Exception $objException)
        {
            $serviceUtil->insertError(  'Telcos+', 
                                        'SoporteWSController.getInfoClienteZabbix', 
                                        $objException->getMessage(),
                                        $arrayData['user'],
                                        "127.0.0.1");

            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];

            if ( strpos($objException->getMessage(),"Error : ") === 0 )
            {
                $arrayRespuesta['mensaje']  = $objException->getMessage();
            }

        }
        return $arrayRespuesta;
    }
    /**
     * Funcion que sirve para obtener los eventos según parámetros recibidos
     * a un caso
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 27-08-2020
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getDetalleEventos($arrayData)
    {
        $strMensaje = "";
        try
        {
            $intIdCuadrilla = $arrayData['data']['idCuadrilla'];
            $intIdEvento    = $arrayData['data']['eventoId'];
            $strFeCreacion  = $arrayData['data']['feCreacion'];
            $strFeUltMod    = $arrayData['data']['feUltMod'];
            $strFechaFin    = $arrayData['data']['fechaFin'];
            $objEmSoporte   = $this->getDoctrine()->getManager("telconet_soporte");
            $arrayEventos   = null;

            if (!empty($intIdCuadrilla) || !empty($intIdEvento) || !empty($strFeCreacion) || !empty($strFeUltMod) || !empty($strFechaFin))
            {
                $arrayParametros['intCuadrillaId'] = $intIdCuadrilla;
                $arrayParametros['intEventoId']    = $intIdEvento;
                $arrayParametros['strFeCreacion']  = $strFeCreacion;
                $arrayParametros['strFeUltMod']    = $strFeUltMod;
                $arrayParametros['strFechaFin']    = $strFechaFin;
                $arrayDetalles = $objEmSoporte->getRepository('schemaBundle:InfoEvento')->getDetalleEventos($arrayParametros);

                foreach($arrayDetalles as $arrayDetalle)
                {
                    $arrayEventos[] = array  (
                                                'idEvento'            => $arrayDetalle['intIdEvento'],
                                                'cuadrillaId'         => $arrayDetalle['intCuadrillaId'],
                                                'tipoEventoId'        => $arrayDetalle['intTipoEventoId'],
                                                'codigoEvento'        => $arrayDetalle['strCodigoEvento'],
                                                'detalleId'           => $arrayDetalle['intDetalleId'],
                                                'fechaInicio'         => $arrayDetalle['strFechaInicio'],
                                                'fechaFin'            => $arrayDetalle['strFechaFin'],
                                                'nombreEvento'        => $arrayDetalle['strNombreEvento'],
                                                'publishId'           => $arrayDetalle['strSerieLogica'],
                                                'valorTiempo'         => $arrayDetalle['intValorTiempo'],
                                                'personaEmpresaRolId' => $arrayDetalle['intPersonaEmpresaRolId'],
                                                'observacion'         => $arrayDetalle['strObservacion'],
                                                'estado'              => $arrayDetalle['strEstado'],
                                                'usrCreacion'         => $arrayDetalle['strUsrCreacion'],
                                                'ipCreacion'          => $arrayDetalle['strIpCreacion'],
                                                'feCreacion'          => $arrayDetalle['strFeCreacion'],
                                                'usrUltMod'           => $arrayDetalle['strUsrUltMod'],
                                                'feUltMod'            => $arrayDetalle['strFeUltMod'],
                                                'ipUltMod'            => $arrayDetalle['strIpUltMod'],
                                                'version'             => $arrayDetalle['strVersion'],
                                            );
                }
            }
            else
            {
                throw new \Exception('ERROR_FALTAN_PARAMETROS');
            }
        }
        catch(\Exception $objE)
        {
            if($objE->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($objE->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else if($objE->getMessage() == "ERROR_FALTAN_PARAMETROS")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = "Faltan parámetros para la consulta!";
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }

            return $arrayResultado;
        }

        $arrayResultado['eventos']    = $arrayEventos;
        $arrayResultado['status']    = $this->status['OK'];
        $arrayResultado['mensaje']   = $this->mensaje['OK'];
        return $arrayResultado;
    }
  /**
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.0 26-09-2020
     * Función que libera horas dependiendo los Ids que sean enviados.
     * 
     */
    private function putLiberarHoras($arrayData)
    {
        $strMensaje          = "Error al intentar liberar horas";
        $arrayDetalle       = $arrayData['data']['detallesLiberar'];
        $intIdCabecera      = $arrayData['data']['idCabecera'];
        $strUsuario         = $arrayData['user'];
        $intIdCuadrilla     = $arrayData['data']['idCuadrilla'];
        $arrayDetLiberados  = array();
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $serviceSoporte     = $this->get('soporte.SoporteService');
        $arrayResultado     = array();
        $strIp              = '127.0.0.1';
        try
        {
            foreach ($arrayDetalle as $objJson)
            {
                $objDetalle = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifDet")->find($objJson['idDetalle']);
                $emSoporte->beginTransaction();
                if (is_object($objDetalle))
                {
                    $objDetalle->setEstado('Liberado');
                    $objDetalle->setUsrModificacion($strUsuario);
                    $objDetalle->setFeModificacion(new \DateTime('now'));
                    $emSoporte->persist($objDetalle);
                    $emSoporte->flush();
                    $emSoporte->getConnection()->commit();
                    $arrayDetLiberados[] = array ('idDetalle'  =>$objJson['idDetalle'],
                    'estado'      =>$objDetalle->getestado());
                }
                else
                {
                    $arrayDetLiberados[] = array ('idDetalle'  =>$objJson['idDetalle'],
                    'estado'      =>$objDetalle->getestado());
                    $strMensaje     = 'Hubo un error al liberar el horario: '.$objJson['idDetalle'];
                    
                    $arrayResultado['data']['detallesLiberar'] = $arrayDetLiberados;
                    $arrayResultado['status']                  = $this->status['ERROR'];
                    $arrayResultado['mensaje']                 = $strMensaje;
                    return $arrayResultado;    
                }
            
                $arrayIdLIsta[] = $objJson['idDetalle'];
                $strMensaje     = 'Se liberaron las horas de trabajo correctamente';
            }
            $strModulo = 'intervaloDetalle';

            //preguntar
            // Si no existe ni un detalle activo, se libera el dia de trabajo
            $arrayDetalles = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifDet")
                ->findBy(array ('cuadrillaPlanifCabId' => $intIdCabecera,
                                'estado'               => 'Activo'));
            //preguntar 

            if(empty($arrayDetalles))
            {
                $objCabecera = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")->find($intIdCabecera);

                if (is_object($objCabecera))
                {
                    $objCabecera->setEstado('Liberado');
                    $objCabecera->setUsrModificacion($strUsuario);
                    $objCabecera->setFeModificacion(new \DateTime('now'));
                    $emSoporte->persist($objCabecera);
                    $emSoporte->flush();
                }

                $arrayIdLIsta   =  array();
                $strModulo      = 'intervaloCab';
                $arrayIdLIsta[] =  $objCabecera->getId();
            }
            if ($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->getConnection()->commit();
            }
            if (!empty($arrayIdLIsta) && count($arrayIdLIsta) > 0)
            {
                /*========================= INICIO NOTIFICACION HAL ==========================*/
                $serviceSoporte->notificacionesHal(
                        array ('strModulo' =>  $strModulo,
                               'strUser'   =>  $strUsuario,
                               'strIp'     =>  $strIp,
                               'arrayJson' =>  array ('metodo'       => 'elimino',
                                                      'idReferencia' => intval($intIdCuadrilla),
                                                      'idLista'      => $arrayIdLIsta)));
                /*=========================== FIN NOTIFICACION HAL ===========================*/
            }

            $arrayResultado['data']['detallesLiberar'] = $arrayDetLiberados;
            $arrayResultado['status']                  = $this->status['OK'];
            $arrayResultado['mensaje']                 = $strMensaje;
            return $arrayResultado;  
        }
        catch(\Exception $e)
        {
            $serviceUtil                  = $this->get('schema.Util');
            if($e->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($e->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }

             $serviceUtil->insertError( 'Telcos Mobile', 
                                        'SoporteWSController.putLiberarHoras', 
                                        $e->getMessage(),
                                        $arrayData['user'],
                                        "127.0.0.1"); 
         
            return $arrayResultado;
        }
        
    }


    /**
     * Funcion que retorna el id del último evento
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 28-08-2020
     * 
     * @param array $arrayData
     * @return array $resultado
     */
    private function getMaxEvento()
    {
        $strMensaje = "";
        try
        {
            $objEmSoporte  = $this->getDoctrine()->getManager("telconet_soporte");
            $arrayEventos   = null;
            $arrayDetalles = $objEmSoporte->getRepository('schemaBundle:InfoEvento')->getUltimoEvento();

            foreach($arrayDetalles as $arrayDetalle)
            {
                $arrayEventos[] = array  (
                                            'idEvento' => $arrayDetalle['intIdEvento'],
                                         );
            }
        }
        catch(\Exception $objE)
        {
            if($objE->getMessage() == "NULL")
            {
                $arrayResultado['status']    = $this->status['NULL'];
                $arrayResultado['mensaje']   = $this->mensaje['NULL'];
            }
            else if($objE->getMessage() == "ERROR_PARCIAL")
            {
                $arrayResultado['status']    = $this->status['ERROR_PARCIAL'];
                $arrayResultado['mensaje']   = $strMensaje;
            }
            else
            {
                $arrayResultado['status']    = $this->status['ERROR'];
                $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
            }

            return $arrayResultado;
        }

        $arrayResultado['evento']    = $arrayEventos;
        $arrayResultado['status']    = $this->status['OK'];
        $arrayResultado['mensaje']   = $this->mensaje['OK'];
        return $arrayResultado;
    }

    /**
     * Función que filtra listado de tarea según parámetros
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 21/09/2020
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 13/10/2020
     * Se agrega validación para permitir mostrar tareas es estado "Aceptada"
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.2 19/10/2020
     * Se agrega nueva lógica para mostrar tareas en el móvil.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 05/05/2021
     * Se agrega nueva lógica para mostrar tareas con el mismo login en el móvil.
     * 
     * @param type $arrayData
     * @return type $arrayRespuesta
     */
    public function taskFilter($arrayData)
    {
        $serviceUtil                    = $this->get('schema.Util');
        $emGeneral                      = $this->getDoctrine()->getManager("telconet_general");
        $arrayRespuesta                 = array();
        $arrayTareasGeneral             = array();
        $arrayTareasSoporte             = array();
        $arrayTareasOtras               = array();
        $arrayTareasOrdenadas           = array();
        $arrayTareaAceptada             = array();
        $arrayTareaPausada              = array();
        $intMaxTareasPermitidas         = 0;
        $intMaxTareasPausadasPermitidas = 0;
        $intCountTareasPausadas         = 0;
        $intCountTareasPermitidas       = 0; 
        $strClass                       = "SoporteWSController";
        $strAppMethod                   = "taskFilter";

        try
        {
            //obtener máximo de tareas permitidas
            $arrayMaxTareasPermitidas    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('PARAMETROS_GENERALES_MOVIL', 
                    '', 
                    '', 
                    '', 
                    'MAX_TAREAS_PERMITIDAS', 
                    '', 
                    '', 
                    ''
                    );

            if(is_array($arrayMaxTareasPermitidas))
            {
                $intMaxTareasPermitidas = !empty($arrayMaxTareasPermitidas['valor2']) ? 
                intval($arrayMaxTareasPermitidas['valor2']) : 0;
            }

            if($intMaxTareasPermitidas == 0)
            {
                $serviceUtil->insertLog(array(
                    'enterpriseCode'   => "10",
                    'logType'          => 0,
                    'logOrigin'        => 'TELCOS',
                    'application'      => 'TELCOS',
                    'appClass'         => $strClass,
                    'appMethod'        => $strAppMethod,
                    'descriptionError' => 'El valor máximo de tareas permitidas es: ' . $intMaxTareasPermitidas,
                    'status'           => 'Fallido',
                    'inParameters'     => $intMaxTareasPermitidas,
                    'creationUser'     => 'TELCOS'));
            }
            
            for($intIteration = 0; $intIteration < count($arrayData); $intIteration++)
            {
                if($arrayData[$intIteration]['estadoTarea'] == 'Aceptada' || 
                    $arrayData[$intIteration]['estadoTarea'] == 'Pausada')
                {
                    if($arrayData[$intIteration]['estadoTarea'] == 'Aceptada' && empty($arrayTareaAceptada))
                    {
                        $arrayTareaAceptada   = $arrayData[$intIteration];
                    }
                    elseif($arrayData[$intIteration]['estadoTarea'] == 'Pausada' && empty($arrayTareaPausada))
                    {
                        $arrayTareaPausada   = $arrayData[$intIteration];
                    }
                }
                else
                {
                    if(!empty($arrayData[$intIteration]['idCaso']))
                    {
                        $arrayTareasSoporte[]   = $arrayData[$intIteration];
                    }
                    else
                    {
                        $arrayTareasOtras[]     = $arrayData[$intIteration];
                    }
                }
            }


            if(!empty($arrayTareaAceptada))
            {
                $arrayTareasGeneral[]   = $arrayTareaAceptada;
                if(count($arrayTareasSoporte) > 0)
                {
                    $arrayTareasGeneral[]   = $arrayTareasSoporte[0];
                    unset($arrayTareasSoporte[0]);
                    $arrayTareasSoporte = array_values($arrayTareasSoporte);
                }
            }
            else
            {
                if(count($arrayTareasSoporte) > 0)
                {
                    $arrayTareasGeneral[]   = $arrayTareasSoporte[0];
                    unset($arrayTareasSoporte[0]);
                    $arrayTareasSoporte = array_values($arrayTareasSoporte);
                }
                elseif(count($arrayTareasOtras) > 0)
                {
                    $arrayTareasGeneral[]   = $arrayTareasOtras[0];
                    unset($arrayTareasOtras[0]);
                    $arrayTareasOtras = array_values($arrayTareasOtras);
                }
            }

            if(count($arrayTareasGeneral) < $intMaxTareasPermitidas && 
                !empty($arrayTareaPausada))
            {
                $arrayTareasGeneral[]   = $arrayTareaPausada;
            }

            if($intMaxTareasPermitidas > 2)
            {
                for($intIteration = count($arrayTareasGeneral); $intIteration < $intMaxTareasPermitidas; $intIteration++)
                {
                    if(count($arrayTareasSoporte) > 0)
                    {
                        $arrayTareasGeneral[]   = $arrayTareasSoporte[0];
                        unset($arrayTareasSoporte[0]);
                        $arrayTareasSoporte = array_values($arrayTareasSoporte);
                        
                    }
                    elseif(count($arrayTareasOtras) > 0)
                    {
                        $arrayTareasGeneral[]   = $arrayTareasOtras[0];
                        unset($arrayTareasOtras[0]);
                        $arrayTareasOtras = array_values($arrayTareasOtras);
                    }
                    else
                    {
                        break;
                    }
                }
            }

            for($intIteration = 0; $intIteration < count($arrayData); $intIteration++)
            {
                $boolAgregar = false;
                $boolExiste  = false; 

                for($intIteration2 = 0; $intIteration2 < count($arrayTareasGeneral); $intIteration2++)
                {
                    if($arrayData[$intIteration]['idTarea'] == $arrayTareasGeneral[$intIteration2]['idTarea'])
                    {
                        $boolExiste = true;
                        break;
                    }

                    if($arrayData[$intIteration]['login'] == $arrayTareasGeneral[$intIteration2]['login'])
                    {
                        $boolAgregar = true;
                    }
                }

                if($boolAgregar && !$boolExiste)
                {
                    $arrayTareasGeneral[]   = $arrayData[$intIteration];
                }
            }

            $arrayRespuesta['status']       = $this->status['OK'];
            $arrayRespuesta['mensaje']      = $this->mensaje['OK'];
            $arrayRespuesta['data']         = $arrayTareasGeneral;
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
            $arrayRespuesta['data']     = "";
            
            $serviceUtil->insertLog(array(
                                                    'enterpriseCode'   => "10",
                                                    'logType'          => 1,
                                                    'logOrigin'        => 'TELCOS',
                                                    'application'      => 'TELCOS',
                                                    'appClass'         => $strClass,
                                                    'appMethod'        => $strAppMethod,
                                                    'descriptionError' => $ex->getMessage(),
                                                    'status'           => 'Fallido',
                                                    'inParameters'     => json_encode($arrayRespuesta),
                                                    'creationUser'     => 'TELCOS'));

        }
        return $arrayRespuesta;
    }

    /**
     * Función encargada de obtener listado de planificacion de un listado de tareas.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 25/09/2020
     *
     * @param type $arrayData
     * @return type $arrayRespuesta
     */
    public function getVisualizarMovil($arrayData)
    {
        $serviceUtil        = $this->get('schema.Util');
        $objSoporteService  = $this->get('soporte.SoporteService');
        $arrayRespuestaData = array();

        try
        {

            for($intIteration = 0; $intIteration < count($arrayData['data']['listadoTareas']); $intIteration++)
            {
                $arrayParametros = array (
                'arrayIdCab'        => $arrayData['data']['listadoTareas'][$intIteration]['idListaCuadrillaPlanifCab'],
                'arrayIdDet'        => $arrayData['data']['listadoTareas'][$intIteration]['idListaCuadrillaPlanifDet'],
                'intIdZona'         => $arrayData['data']['listadoTareas'][$intIteration]['idZona'],
                'intIdCuadrilla'    => $arrayData['data']['listadoTareas'][$intIteration]['idCuadrilla'],
                'intIdComunicacion' => $arrayData['data']['listadoTareas'][$intIteration]['idComunicacion'],
                'strFechaIni'       => $arrayData['data']['listadoTareas'][$intIteration]['fechaIni'],
                'strFechaFin'       => $arrayData['data']['listadoTareas'][$intIteration]['fechaFin'],
                'strEstadoCab'      => $arrayData['data']['listadoTareas'][$intIteration]['estadoCab'],
                'strEstadoDet'      => $arrayData['data']['listadoTareas'][$intIteration]['estadoDet'],
                'strUser'           => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                'strIp'             => ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1'));

                $arrayRespuestaRepository = $objSoporteService->getSolicitarDetallePlanificacion($arrayParametros);
                
                $arrayRespuestaData['listadoTareas'][] = array(
                    "idComunicacion" => $arrayData['data']['listadoTareas'][$intIteration]['idComunicacion'],
                    "planificacion"  => $arrayRespuestaRepository["planificacion"]
                );
            }

            $arrayRespuesta['status']       = $this->status['OK'];
            $arrayRespuesta['mensaje']      = $this->mensaje['OK'];
            $arrayRespuesta['data']         = $arrayRespuestaData;
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
            $arrayRespuesta['data']     = "";
            $strClass                   = "SoporteWSController";
            $strAppMethod               = "getVisualizarMovil";
            
            $serviceUtil->insertLog(array(
                                                    'enterpriseCode'   => "10",
                                                    'logType'          => 1,
                                                    'logOrigin'        => 'TELCOS',
                                                    'application'      => 'TELCOS',
                                                    'appClass'         => $strClass,
                                                    'appMethod'        => $strAppMethod,
                                                    'descriptionError' => $ex->getMessage(),
                                                    'status'           => 'Fallido',
                                                    'inParameters'     => json_encode($arrayData),
                                                    'creationUser'     => 'TELCOS'));

        }
        return $arrayRespuesta;
    }

    /**
     * Función para guardar los permisos de alimentación y finalización de jornada.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 08/10/2020
     *
     * @param type $arrayData
     * @return type $arrayRespuesta
     */
    public function putPermisoJornadaAlimentacion($arrayData)
    {
        $serviceUtil        = $this->get('schema.Util');
        $objSoporteService  = $this->get('soporte.SoporteService');
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $intIdCabecera      = $arrayData['data']['idCabecera'];
        $boolPermiso        = $arrayData['data']['permiso'];
        $strOpcion          = $arrayData['data']['opcion'];
        $strUsuario         = $arrayData['user'];
        $strIp              = '127.0.0.1';
        $strMsgExito        = "";
        $strMsgError        = "";

        try
        {
            $arrayMsgExito    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('MENSAJES_TM_OPERACIONES', 
                     '', 
                     '', 
                     '', 
                     'MSG_EXITO_PERMISO_JORNADA_ALIMENTACION', 
                     '', 
                     '', 
                     ''
                     );
    
            if(is_array($arrayMsgExito))
            {
                $strMsgExito = !empty($arrayMsgExito['valor2']) ? $arrayMsgExito['valor2'] : "Valor del permiso seteado";
            }

            $arrayMsgError    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('MENSAJES_TM_OPERACIONES', 
                     '', 
                     '', 
                     '', 
                     'MSG_EXITO_PERMISO_JORNADA_ALIMENTACION', 
                     '', 
                     '', 
                     ''
                     );
    
            if(is_array($arrayMsgError))
            {
                $strMsgError = !empty($arrayMsgError['valor2']) ? $arrayMsgError['valor2'] : "No se pudo setear el valor del permiso";
            }

            $emSoporte->beginTransaction();

            $objInfoCuadrillaPlanifCab = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")
                        ->find($intIdCabecera);

            if (!is_object($objInfoCuadrillaPlanifCab))
            {
                $arrayRespuesta['status']       = $this->status['DATOS_NO_VALIDOS'];
                $arrayRespuesta['mensaje']      = "No existe el id: " . $intIdCabecera;
            }
            else
            {
                if($strOpcion == 'alimentacion' || $strOpcion == 'finJornada')
                {
    
                    if($strOpcion == 'alimentacion')
                    {
                        $objInfoCuadrillaPlanifCab->setAutorizaAlimentacion($boolPermiso ? 'S' : 'N');
                    }
    
                    if($strOpcion == 'finJornada')
                    {
                        $objInfoCuadrillaPlanifCab->setAutorizaFinalizar($boolPermiso ? 'S' : 'N');
                    }
    
                    $objInfoCuadrillaPlanifCab->setUsrModificacion($strUsuario);
                    $objInfoCuadrillaPlanifCab->setIpModificacion($strIp);
                    $objInfoCuadrillaPlanifCab->setFeModificacion(new \DateTime('now'));
                    $emSoporte->persist($objInfoCuadrillaPlanifCab);
                    $emSoporte->flush();
        
                    if ($emSoporte->getConnection()->isTransactionActive())
                    {
                        $emSoporte->getConnection()->commit();
                    }
            
                    $arrayRespuesta['status']       = $this->status['OK'];
                    $arrayRespuesta['mensaje']      = $strMsgExito;
                }
                else
                {
                    $arrayRespuesta['status']       = $this->status['DATOS_NO_VALIDOS'];
                    $arrayRespuesta['mensaje']      = "Opción no encontrada";
                }
            }
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $strMsgError;
            $strClass                   = "SoporteWSController";
            $strAppMethod               = "putPermisoJornadaAlimentacion";
            
            $serviceUtil->insertLog(array(
                                                    'enterpriseCode'   => "10",
                                                    'logType'          => 1,
                                                    'logOrigin'        => 'TELCOS',
                                                    'application'      => 'TELCOS',
                                                    'appClass'         => $strClass,
                                                    'appMethod'        => $strAppMethod,
                                                    'descriptionError' => $ex->getMessage(),
                                                    'status'           => 'Fallido',
                                                    'inParameters'     => json_encode($arrayData),
                                                    'creationUser'     => 'TELCOS'));

        }

        return $arrayRespuesta;
    }

    /**
     * Función para obtener los permisos de alimentación y finalización de jornada
     * de una cuadrilla.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 12/10/2020
     *
     * @param type $arrayData
     * @return type $arrayRespuesta
     */
    public function obtenerPermisosJornadaAlimentacion($arrayData)
    {
        $serviceUtil        = $this->get('schema.Util');
        $arrayRespuestaData = array();
        $intIdCabecera      = $arrayData['idCabecera'];
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");

        try
        {
            $objInfoCuadrillaPlanifCab = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")
            ->find($intIdCabecera);

            if (!is_object($objInfoCuadrillaPlanifCab))
            {
                $arrayRespuesta['status']       = $this->status['DATOS_NO_VALIDOS'];
                $arrayRespuesta['mensaje']      = "No existe el id: " . $intIdCabecera;
            }
            else
            {
                $arrayRespuestaData = array(
                        'AutorizaAlimentacion'  => $objInfoCuadrillaPlanifCab->getAutorizaAlimentacion(),
                        'AutorizaFinalizar'     => $objInfoCuadrillaPlanifCab->getAutorizaFinalizar()
                );
        
                $arrayRespuesta['status']       = $this->status['OK'];
                $arrayRespuesta['mensaje']      = $this->mensaje['OK'];
                $arrayRespuesta['data']         = $arrayRespuestaData;
            }
        }
        catch (\Exception $ex)
        {
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
            $arrayRespuesta['data']     = $arrayRespuestaData;
            $strClass                   = "SoporteWSController";
            $strAppMethod               = "obtenerPermisosJornadaAlimentacion";
            
            $serviceUtil->insertLog(array(
                                                    'enterpriseCode'   => "10",
                                                    'logType'          => 1,
                                                    'logOrigin'        => 'TELCOS',
                                                    'application'      => 'TELCOS',
                                                    'appClass'         => $strClass,
                                                    'appMethod'        => $strAppMethod,
                                                    'descriptionError' => $ex->getMessage(),
                                                    'status'           => 'Fallido',
                                                    'inParameters'     => json_encode($arrayData),
                                                    'creationUser'     => 'TELCOS'));

        }
        
        return $arrayRespuesta;
    }

     /**
     * Función encargada de mostrar datos de la tarea como la bobina y la cantidad de fibra utilizada.
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 24/11/2020
     *
     * @param type $arrayData
     * @return type $arrayRespuesta
     */
    public function getFibraPorTarea($arrayData)
    {
        $strUserDbSoporte     = $this->container->getParameter('user_soporte');
        $strPasswordDbSoporte = $this->container->getParameter('passwd_soporte');
        $strDatabaseDsn       = $this->container->getParameter('database_dsn');        
        $intIdComunicacion = $arrayData['data']['intIdComunicacion'];
        $intIdDetalle      = $arrayData['data']['intIdDetalle'];
        $serviceUtil        = $this->get('schema.Util');
        $arrayData['userSoporte'] = $strUserDbSoporte;
        $arrayData['pwdSoporte']  = $strPasswordDbSoporte;
        $arrayData['dsnSoporte']  = $strDatabaseDsn;
        try
        {
            if (!$intIdComunicacion && !$intIdDetalle)
            {
                throw new \Exception('Debe enviar un parámetro por lo menos');
            }
            $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");  
            $strRetorno   = $emSoporte->getRepository('schemaBundle:InfoComunicacion')->getFibraPorTarea($arrayData);
            $arrayRetorno = json_decode($strRetorno, true);
            $arrayRespuesta['status']   = $this->status['OK'];
            $arrayRespuesta['mensaje']  = $this->mensaje['OK'];
            $arrayRespuesta['data']     = $arrayRetorno['data'];
            error_log("retorno " . $strRetorno);

        }
        catch (\Exception $ex)
        {
            $arrayRespuesta['status']   = $this->status['ERROR'];
            $arrayRespuesta['mensaje']  = $this->mensaje['ERROR'];
            $arrayRespuesta['data']     = "";
            $strClass                   = "SoporteWSController";
            $strAppMethod               = "getFibraPorTarea";
            
            $serviceUtil->insertLog(array(
                                            'enterpriseCode'   => "10",
                                            'logType'          => 1,
                                            'logOrigin'        => 'TELCOS',
                                            'application'      => 'TELCOS',
                                            'appClass'         => $strClass,
                                            'appMethod'        => $strAppMethod,
                                            'descriptionError' => $ex->getMessage(),
                                            'status'           => 'Fallido',
                                            'inParameters'     => json_encode($arrayData),
                                            'creationUser'     => 'TELCOS'));

        }
        return $arrayRespuesta;      
        
    }

    /**
     * Función que sirve para la creación de casos para el departamento NOC
     *
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 30-11-2020
     *
     * @param type $arrayData
     * @return $arrayRespuesta
     */
    private function putCrearCasoNoc($arrayData)
    {
        $objSoporteService = $this->get('soporte.SoporteService');
        $objEmComercial    = $this->getDoctrine()->getManager("telconet");
        $objEmSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
        $objServiceUtil    = $this->get('schema.Util');
        try
        {
            $strPrefijoEmpresa    = $arrayData['data']['prefijoEmpresa'];
            $strFormaContacto     = $arrayData['data']['formaContacto'];
            $strNivelCriticidad   = $arrayData['data']['nivelCriticidad'];
            $strTipoAfectacion    = $arrayData['data']['tipoAfectacion'];
            $strTipoAsignacion    = $arrayData['data']['tarea']['tipoAsignacion'];
            $strTituloInicial     = $arrayData['data']['tituloInicial'];
            $strVersionInicial    = $arrayData['data']['versionInicial'];
            $strSintoma           = $arrayData['data']['sintoma'];
            $strHipotesis         = $arrayData['data']['hipotesis'];
            $strLogin             = $arrayData['data']['login'];
            $strLoginAux          = $arrayData['data']['loginaux'];
            $strUsrAsignadoCaso   = $arrayData['data']['asignadoCaso'];
            $strTipoCaso          = $arrayData['data']['tipoCaso'];
            $strNombreTarea       = $arrayData['data']['tarea']['nombreTarea'];
            $strUsrAsignadoTarea  = $arrayData['data']['tarea']['asignadoTarea'];
            $strMotivoTarea       = $arrayData['data']['tarea']['motivoTarea'];
            $strObservacion       = $arrayData['data']['tarea']['observacion'];
            $strUserCreacion      = $arrayData['user'];
            $strIpCreacion        = $arrayData['ip'];
            $strAsignacionAut     = "NO";
            $boolValidaDatosCaso  = false;

            $objFechaHoraApertura = new \DateTime('now');
            $strFechaHoraApertura = date_format($objFechaHoraApertura, 'd-m-Y H:i');
            $strFechaHoraApertura = $strFechaHoraApertura.":00";

            if (!$strTipoCaso || !$strFormaContacto || !$strNivelCriticidad || !$strTipoAfectacion ||
                !$strTituloInicial || !$strVersionInicial || !$strSintoma || !$strHipotesis || 
                !$strUsrAsignadoCaso )
            {
                $boolValidaDatosCaso = true;
            }

            if (!$strPrefijoEmpresa ||  !$strLogin || !$strLoginAux ||  !$strTipoAsignacion ||
                !$strUserCreacion || !$strIpCreacion || !$strNombreTarea || !$strUsrAsignadoTarea || $boolValidaDatosCaso
                )
            {
                throw new \Exception(  "Error : Los siguientes valores no pueden ser vacios: ("
                                     . "prefijoEmpresa, tipoCaso, formaContacto, nivelCriticidad, tipoAfectacion, tipoAsignacion, "
                                     . "tituloInicial, versionInicial, sintomas, hipotesis, login, "
                                     . "loginaux, asignadoCaso, nombreTarea, asignadoTarea, user,ip)");
            }

            //Valida tipo de caso
            if(strtoupper(trim($strTipoCaso)) != "TECNICO")
            {
                throw new \Exception("Error : El tipo de caso (".$strTipoCaso.") no es soportado"); 
            }

            //Valida tipo de caso
            if(strtoupper(trim($strTipoAsignacion)) != "EMPLEADO")
            {
                throw new \Exception("Error : El tipo de asignación (".$strTipoAsignacion.") no es soportado"); 
            }

            //Valida tipo de afectación
            if(strtoupper(trim($strTipoAfectacion)) != "CAIDA" && 
               strtoupper(trim($strTipoAfectacion)) != "INTERMITENCIA" &&
               strtoupper(trim($strTipoAfectacion)) != "SINAFECTACION")
            {
                throw new \Exception("Error : El tipo de afectación (".$strTipoAfectacion.") no es soportado"); 
            }

            //Obtiene forma de contacto
            $arrayFormaContacto   = $objEmComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                   ->findBy(array(
                                                                   'descripcionFormaContacto'=>$strFormaContacto,
                                                                   'estado'                  =>'Activo'   
                                                                 )
                                                           );
            $objFormaContacto     = $arrayFormaContacto[0];

            if(!is_object($objFormaContacto))
            {
                throw new \Exception("Error : No se encontró la forma de contacto (".$strFormaContacto.")");
            }

            //Obtiene empresa con prefijo
            $objEmpresa        = $objEmComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo($strPrefijoEmpresa);

            if(!is_object($objEmpresa))
            {
                throw new \Exception("Error : No se encontró empresa con prefijo (".$strPrefijoEmpresa.")");
            }

            //Obtiene idPer de usuario asignado
            $arrayEmpleado        = $objEmComercial->getRepository("schemaBundle:InfoPersona")
                                                ->getPersonaDepartamentoPorUserEmpresa($strUsrAsignadoCaso, $objEmpresa->getId());

            if (!empty($arrayEmpleado))
            {
                $intIdPersonaEmpresaRol   = $arrayEmpleado['ID_PERSONA_EMPRESA_ROL'];
                $strNombreDepAsignadoCaso = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];
                $strIdDepAsignadoCaso     = $arrayEmpleado['ID_DEPARTAMENTO'];
                $intIdCantonAsignadoCaso  = $arrayEmpleado['ID_CANTON'];
            }

            if (!isset($intIdPersonaEmpresaRol) || empty($intIdPersonaEmpresaRol))
            {
                throw new \Exception("Error : No se encontró usuario (".$strUsrAsignadoCaso.") asignado al caso");
            }

            //Valida el Sintoma
            $arrayAdmiSintoma = $objEmSoporte->getRepository("schemaBundle:AdmiSintoma")
                                             ->findBy(
                                                          array(
                                                                    'nombreSintoma' => $strSintoma,
                                                                    'estado'        => 'Activo', 
                                                                    'empresaCod'    => $objEmpresa->getId()
                                                                )
                                                     );
            if(!isset($arrayAdmiSintoma) || empty($arrayAdmiSintoma))
            {
                throw new \Exception("Error : No se encontró información del sintoma (".$strSintoma.")");
            }

            //Valida la Hipotesis
            $arrayAdmiHipotesis = $objEmSoporte->getRepository("schemaBundle:AdmiHipotesis")
                                             ->findBy(
                                                          array(
                                                                    'nombreHipotesis' => $strHipotesis,
                                                                    'estado'        => 'Activo', 
                                                                    'empresaCod'    => $objEmpresa->getId()
                                                                )
                                                     );
            if(!isset($arrayAdmiHipotesis) || empty($arrayAdmiHipotesis))
            {
                throw new \Exception("Error : No se encontró información de la hipotesis (".$strHipotesis.")");
            }
            $arrayHipotesis[] = $strHipotesis;

            //Obtiene Id del Punto
            $arrayInfoPunto = $objEmComercial->getRepository("schemaBundle:InfoPunto")->findBy(array('login'=>$strLogin));
            if(!isset($arrayInfoPunto) || empty($arrayInfoPunto))
            {
                throw new \Exception("Error : No se encontró información del login (".$strLogin.")");
            }
            $intIdPuntoAfectado = $arrayInfoPunto[0]->getId();

            //Obtiene Id del Servicio
            $arrayInfoServicio = $objEmComercial->getRepository("schemaBundle:InfoServicio")->findBy(array('loginAux'=>$strLoginAux));
            if(!isset($arrayInfoServicio) || empty($arrayInfoServicio))
            {
                throw new \Exception("Error : No se encontró información del servicio con login_aux (".$strLoginAux.")");
            }
            $intIdServicioAfectado = $arrayInfoServicio[0]->getId();

            $arraySintomas[] = array(
                "nombre" => $strSintoma,
                "afectados" => array(
                    "puntoId"    => array($intIdPuntoAfectado),
                    "servicioId" => $intIdServicioAfectado
                )
            );

            //Obtiene el proceso
            $arrayAdmiTarea   = $objEmSoporte->getRepository("schemaBundle:AdmiTarea")->findBy(array('nombreTarea' => $strNombreTarea,
                                                                                                     'estado'      => 'Activo'
                                                                                                    )
                                                                                              );

            if(!isset($arrayAdmiTarea) || empty($arrayAdmiTarea))
            {
                throw new \Exception("Error : No se encontró la tarea (".$strNombreTarea.")");
            }

            $strNombreProceso = $arrayAdmiTarea[0]->getProcesoId()->getNombreProceso();


            //Obtiene idPer de usuario asignado a la tarea
            $arrayEmpleadoTarea = $objEmComercial->getRepository("schemaBundle:InfoPersona")
                                                 ->getPersonaDepartamentoPorUserEmpresa($strUsrAsignadoTarea, $objEmpresa->getId());
            if (!empty($arrayEmpleadoTarea))
            {
                $intIdPersonaEmpresaRolTarea = $arrayEmpleadoTarea['ID_PERSONA_EMPRESA_ROL'];
            }

            if (!isset($intIdPersonaEmpresaRolTarea) || empty($intIdPersonaEmpresaRolTarea))
            {
                throw new \Exception("Error : No se encontró usuario (".$strUsrAsignadoTarea.") asignado a la tarea");
            }

            $arrayTarea[] = array(
                "nombreProceso"  => $strNombreProceso,
                "nombreTarea"    => $strNombreTarea,
                "sintoma"        => $strSintoma,
                "afectados"      => array($intIdPuntoAfectado),
                "empleado"       => $intIdPersonaEmpresaRolTarea,
                "cuadrilla"      => "",
                "tipoAsignacion" => $strTipoAsignacion,
                "motivoTarea"    => $strMotivoTarea,
                "observacion"    => $strObservacion,
                "asignacionAut"  => $strAsignacionAut
            );

            $arrayParametros = array ('strPrefijoEmpresa'    => $strPrefijoEmpresa,
                                      'strTipoCaso'          => $strTipoCaso,
                                      'strFormaContacto'     => $objFormaContacto->getCodigo(),
                                      'strNivelCriticidad'   => $strNivelCriticidad,
                                      'strTipoAfectacion'    => $strTipoAfectacion,
                                      'strFechaHoraApertura' => $strFechaHoraApertura,
                                      'strTipoBackbone'      => "",
                                      'strTituloInicial'     => $strTituloInicial,
                                      'strVersionInicial'    => $strVersionInicial,
                                      'arraySintomas'        => $arraySintomas,
                                      'arrayHipotesis'       => $arrayHipotesis,
                                      'strEmpleadoAsignado'  => $intIdPersonaEmpresaRol,
                                      'arrayTareas'          => $arrayTarea,
                                      'boolOrigenWsCasoNoc'  => true,
                                      'strEstadoActual'      => "",
                                      'strTipoReprograma'    => "",
                                      'intTiempo'            => "",
                                      'intIdCaso'            => "",
                                      'intIdCasoHistorial'   => "",
                                      'intIdComunicacion'    => "",
                                      'intIdDocumento'       => "",
                                      'intIdDocComunicacion' => "",
                                      'strUserCreacion'      => $strUserCreacion,
                                      'strIpCreacion'        => ($strIpCreacion ? $strIpCreacion : '127.0.0.1'));

            /* Creación de caso */
            $arrayRespuesta = $objSoporteService->crearCasoSoporte($arrayParametros);

            //Envio de notificación al cliente de creación de nuevo caso
            if(strtoupper($arrayRespuesta['status']) == 'OK' && $arrayRespuesta['result']['numeroCaso'] != "")
            {

                $objCaso = $objEmSoporte->getRepository("schemaBundle:InfoCaso")->findOneByNumeroCaso($arrayRespuesta['result']['numeroCaso']);

                $arrayParametrosNotificacion['prefijoEmpresa']     = $objEmpresa->getPrefijo();
                $arrayParametrosNotificacion['codEmpresa']         = $objEmpresa->getId();
                $arrayParametrosNotificacion['idPunto']            = $intIdPuntoAfectado;
                $arrayParametrosNotificacion['login']              = $strLogin;
                $arrayParametrosNotificacion['nombreDepartamento'] = $strNombreDepAsignadoCaso;
                $arrayParametrosNotificacion['idDepartamento']     = $strIdDepAsignadoCaso;
                $arrayParametrosNotificacion['usrAsignado']        = $strUsrAsignadoCaso;
                $arrayParametrosNotificacion['idCantonAsignado']   = $intIdCantonAsignadoCaso;
                $arrayParametrosNotificacion['objInfoCaso']        = $objCaso;
                
                $objSoporteService->notificaCreacionCaso($arrayParametrosNotificacion);
            }
        }
        catch (\Exception $objException)
        {
            $strMensaje = 'Error en el webservice de creación de casos';

            if ( strpos($objException->getMessage(),"Error : ") === 0 )
            {
                $strMensaje = $objException->getMessage();
            }
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => $strMensaje);

            $objServiceUtil->insertError('Telcos+',
                                         'SoporteWSController.putCrearCaso',
                                          $objException->getMessage(),
                                          ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                          ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1'));
        }
        return $arrayRespuesta;
    }


     /**
     * Método que planifica las solicitudes que son procesadas por HAL
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0
     *
     * @param  $arrayData
     * @return $arrayRespuesta
     */
    private function putPlanificarSolicitud($arrayData)
    {
        $objPlanificarService = $this->get('planificacion.planificar');
        $serviceUtil          = $this->get('schema.Util');
        $emSoporte            = $this->getDoctrine()->getManager("telconet");

        try
        {    
            $arrayParametros = array (
                                        'intIdSolicitud'       => $arrayData['data']['idSolicitud'],
                                        'intIdCuadrilla'       => $arrayData['data']['idCuadrilla'],
                                        'strFechaProgramacion' => $arrayData['data']['fechaProgramacion'],
                                        'strHoraIni'           => $arrayData['data']['horaInicio'],
                                        'strHoraFin'           => $arrayData['data']['horaFin'],
                                        'strObservacion'       => $arrayData['data']['observacion'],
                                        'strUser'              => ($arrayData['user'] ? $arrayData['user'] : 'Telcos'),
                                        'strIp'                => ($arrayData['ip'] ? $arrayData['ip'] : '127.0.0.1')
                                     );

            $arrayRespuesta = $objPlanificarService->programarPlanificacion($arrayParametros);

            error_log('Json putPlanificarSolicitud: '.json_encode($arrayParametros,true));

            $serviceUtil->insertError('Telcos+',
                                      'SoporteWSController->putPlanificarSolicitud',
                                      'Request  '.json_encode($arrayData['data'],true).' '.
                                        'Response '.json_encode($arrayRespuesta),
                                      'TelcosHal',
                                      '127.0.0.1');

        }
        catch (\Exception $objException)
        {
            error_log("Error SoporteWSController.putAsignarSolicitudTarea -> Error: ".$objException->getMessage());
            $arrayRespuesta["mensaje"]     = 'fail';
            $arrayRespuesta["descripcion"] = $objException->getMessage();
        }
        return $arrayRespuesta;

    }

    /**
     * Método que permite crear una tarea desde plataformas externas en Telcos mediante un WebService.
     *
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 1.0 26-03-2021
     *
     * @param  Array $arrayJson
     * @return Array $arrayRespuesta
     */
    private function putCrearTareaExterna($arrayJson)
    {
        $arrayRespuesta     = array();
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
        $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objSoporteService  = $this->get('soporte.SoporteService');
        $serviceUtil        = $this->get('schema.Util');
        $arrayData          = $arrayJson['data'];
        $arrayDataAudit     = $arrayJson['dataAuditoria'];
        $strCodEmpresa      = '';
        $strPrefijoEmpresa  = 'TN';
        $strCodigo          = '';
        $strLoginAfectado   = $arrayData["login"] ? $arrayData["login"] : "";
        $strFechaSolicitada = $arrayData["fechaSolicitada"] ? $arrayData["fechaSolicitada"] : "";
        $strHoraSolicitada  = $arrayData["horaSolicitada"] ? $arrayData["horaSolicitada"] : "";
        $boolFlagError      = false;
        $objPunto           = "";
        $strFicheroSubido   = "";
        $strNombreArchivo   = "";

        try
        {
            if($arrayData['login'] !== "")
            {
                $objPunto   = $emComercial->getRepository("schemaBundle:InfoPunto")->findOneByLogin($arrayData['login']);
                if(is_object($objPunto))
                {
                    $intIdOficina = $objPunto->getPuntoCoberturaId()->getOficinaId();
                    $objOficina   = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);
                    $objEmpresa = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneBy(
                                                array('id' => $objOficina->getEmpresaId()));
                    if(is_object($objEmpresa))
                    {
                        $strCodEmpresa = $objEmpresa->getId();
                        $strPrefijoEmpresa  = $objEmpresa->getPrefijo();
                    }else
                    {
                        $boolFlagError = true;
                        throw new \Exception('No existe empresa asignada para el login cliente '.$arrayData['login']);
                    }
                }else
                {
                    $boolFlagError = true;
                    throw new \Exception('Login '.$arrayData['login'].' de cliente, no se encuentra en Telcos.');
                }
            }
            // Validación existencia de campos correspondientes en json data
            $arrayParametrosRequest = array("origen", "nombreClase", "nombreEmpresa", "nombreProceso",
                                            "nombreTarea", "fechaSolicitada","cierreInmediato", "asignarTarea",
                                            "empresa", "ciudad", "departamento", "nombresEmpleado",
                                            "apellidosEmpleado", "login", "seleccionarElemento", "adjunto",
                                            "observacion", "accion", "extAdjunto", "nombreAdjunto"
                                    );

            $arrayParametrosFaltantes = array_diff_key(array_flip($arrayParametrosRequest), $arrayData);
            foreach($arrayParametrosFaltantes as $campo => $valor)
            {
                $boolFlagError = true;
                $strParametrosError .= ' '.$campo.',';
            }

            if($boolFlagError && !empty($arrayParametrosFaltantes))
            {
                throw new \Exception("No existen los siguientes parametros en request: ".substr($strParametrosError, 0, -1));
            }
            // Validación de campos vacíos en json data
            $arrayParametrosIgnorar = array("fechaSolicitada","nombreEmpresa","empresa","ciudad","departamento",
                                            "nombresEmpleado","apellidosEmpleado","adjunto","login","extAdjunto","nombreAdjunto");

            foreach($arrayData as $campo => $valor)
            {
                if (!in_array($campo, $arrayParametrosIgnorar) && $valor == "")
                {
                    $boolFlagError = true;
                    $strParametrosError .= ' '.$campo.',';
                }
            }

            if($boolFlagError)
            {
                throw new \Exception("Los siguientes parametros no tienen valor asignado: ".substr($strParametrosError, 0, -1));
            }

            if(strtoupper($arrayData['cierreInmediato']) !== 'S' && strtoupper($arrayData['cierreInmediato']) !== 'N')
            {
                $boolFlagError = true;
                throw new \Exception('El valor del parametro CierreInmediato debe ser S o N.');
            }

            if(strtoupper($arrayData['asignarTarea']) !== 'S' && strtoupper($arrayData['asignarTarea']) !== 'N')
            {
                $boolFlagError = true;
                throw new \Exception('El valor del parametro asignarTarea debe ser S o N.');
            }

            if(strtoupper($arrayData['nombreClase']) == "REQUERIMIENTO ENTRE EMPRESAS" && $arrayData['nombreEmpresa'] == "")
            {
                $boolFlagError = true;
                throw new \Exception('El parametro nombreEmpresa no tienen valor asignado.');
            }else if(strtoupper($arrayData['nombreClase']) == "REQUERIMIENTOS DE CLIENTES" && $arrayData['login'] == "")
            {
                $boolFlagError = true;
                throw new \Exception('El login del cliente no tienen valor asignado.');
            }

            // Valida forma de contacto
            $arrayFormaContactoActivos   = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                                ->findBy(array('estado' => 'Activo'));

            $arrayOrigenes = [];
            foreach ($arrayFormaContactoActivos as $intKey => $arrayValues)
            {
                $strFormaContacto                             = $arrayValues->getDescripcionFormaContacto();
                $arrayOrigenes[strtoupper($strFormaContacto)] = $strFormaContacto;
            }

            if(!array_key_exists(strtoupper($arrayData['origen']), $arrayOrigenes))
            {
                $boolFlagError = true;
                throw new \Exception("No se encontró el origen ".$arrayData['origen']."");
            }else
            {
                $arrayFormaContacto   = $emComercial->getRepository('schemaBundle:AdmiFormaContacto')
                                        ->findBy(array(
                                                    'descripcionFormaContacto'=>$arrayOrigenes[strtoupper($arrayData['origen'])],
                                                    'estado' => 'Activo'));
                $objFormaContacto    = $arrayFormaContacto[0];
                $arrayData['origen'] = $objFormaContacto->getDescripcionFormaContacto();
            }

            // Obtener y verificar que clase, proceso y la tarea enviada exista en el Telcos
            $objClases   = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")
                                                ->findBy(array('estado' => 'Activo'));
            $arrayClases = [];
            foreach ($objClases as $intKey => $arrayValues)
            {
                $strClase                           = $arrayValues->getDescripcionClaseDocumento();
                $arrayClases[strtoupper($strClase)] = $strClase;
            }

            if(!array_key_exists(strtoupper($arrayData['nombreClase']), $arrayClases))
            {
                $boolFlagError = true;
                throw new \Exception('La clase '.$arrayData['nombreClase'].' no existe en Telcos.');
            }else
            {
                $objClase   = $emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")
                            ->findOneBy(array('nombreClaseDocumento'=>$arrayClases[strtoupper($arrayData['nombreClase'])]));
            }

            $objProcesos   = $emSoporte->getRepository("schemaBundle:AdmiProceso")
                                    ->findBy(array ('estado' => 'Activo'));

            $arrayProcesos = [];
            foreach ($objProcesos as $intKey => $arrayValues)
            {
                $strProceso                             = $arrayValues->getNombreProceso();
                $arrayProcesos[strtoupper($strProceso)] = $strProceso;
            }

            if(!array_key_exists(strtoupper($arrayData['nombreProceso']), $arrayProcesos))
            {
                $boolFlagError = true;
                throw new \Exception('El proceso '.$arrayData['nombreProceso'].' no existe en Telcos.');
            }else
            {
                $objAdmiProceso = $emSoporte->getRepository("schemaBundle:AdmiProceso")
                                        ->findOneBy(array ('nombreProceso' => $arrayProcesos[strtoupper($arrayData['nombreProceso'])],
                                                           'estado'        => 'Activo'));
            }

            $objTareas   = $emSoporte->getRepository("schemaBundle:AdmiTarea")
                                ->findBy(array ('procesoId' => $objAdmiProceso->getId(), 'estado' => 'Activo'));

            $arrayTareas = [];
            foreach ($objTareas as $intKey => $arrayValues)
            {
                $strTarea                           = $arrayValues->getNombreTarea();
                $arrayTareas[strtoupper($strTarea)] = $strTarea;
            }

            if(!array_key_exists(strtoupper($arrayData['nombreTarea']), $arrayTareas))
            {
                $boolFlagError = true;
                throw new \Exception('La tarea '.$arrayData['nombreTarea'].' del proceso '.$arrayData['nombreProceso'].' no existe en Telcos.');
            }else
            {
                $objTarea = $emSoporte->getRepository("schemaBundle:AdmiTarea")
                                    ->findOneBy(array ('procesoId'   => $objAdmiProceso->getId(),
                                                    'nombreTarea' => $arrayTareas[strtoupper($arrayData['nombreTarea'])],
                                                    'estado'      => 'Activo'));
            }

            if($arrayData['asignarTarea'] == "S")
            {
                if($arrayData['empresa'] == "" || $arrayData['ciudad'] == "" || $arrayData['departamento'] == "" ||
                   $arrayData['nombresEmpleado'] == "" || $arrayData['apellidosEmpleado'] == "")
                {
                    throw new \Exception('No tienen valor los parametros correspondientes para asignar tarea.');
                }else
                {
                    $strNombreDepartamento = $arrayData['departamento'];
                    $objDepartamento = $emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                        ->findOneBy(array('nombreDepartamento' => $strNombreDepartamento,
                                            'estado'           => array('Activo','Modificado')));

                    if (!is_object($objDepartamento))
                    {
                        $boolFlagError = true;
                        throw new \Exception("El departamento $strNombreDepartamento no existe en Telcos.");
                    }
                }
            }

            if(strlen($arrayData['observacion']) > 4000)
            {
                $boolFlagError = true;
                throw new \Exception('El parametro observacion sobrepasa el limite aceptado de 4000 caracteres.');
            }

            $boolValidaBase64 = (base64_encode(base64_decode($arrayData['adjunto'], true)) === $arrayData['adjunto']);
            $intPesoAdjuntoBytes = (int) (strlen(rtrim($arrayData['adjunto'], '=')) * 3 / 4);
            $intPesoAdjuntoKb    = $intPesoAdjuntoBytes / 1024;
            $intPesoAdjuntoMb    = $intPesoAdjuntoKb / 1024;
            if (!$boolValidaBase64)
            {
                $boolFlagError = true;
                throw new \Exception('El archivo adjunto no se encuentra en el formato correspondiente.');
            }else if($intPesoAdjuntoMb > 10)
            {
                $boolFlagError = true;
                throw new \Exception('El archivo adjunto sobrepasa el tamano permitido de 10mb.');
            }

            if(!empty($arrayData['ciudad']))
            {
                $strNombreCanton   =  $arrayData['ciudad'];
            }

            $intIdCanton      = 0;

            if(!empty($strNombreCanton))
            {
                $objCanton = $emSoporte->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strNombreCanton);
                if(is_object($objCanton))
                {
                    $intIdCanton     = $objCanton->getId();
                }
            }

            if($arrayData['asignarTarea'] == "S")
            {
                $objConsultaPersona = $emComercial->getRepository("schemaBundle:InfoPersona")->findOneBy(
                    array('nombres' => strtoupper(trim($arrayData['nombresEmpleado'])),
                        'apellidos' => strtoupper(trim($arrayData['apellidosEmpleado'])))
                );

                if (!is_object($objConsultaPersona))
                {
                    $boolFlagError = true;
                    throw new \Exception("La persona ".$arrayData['nombresEmpleado']." ".$arrayData['apellidosEmpleado']." no existe en Telcos.");
                }

                $strLoginAsignado = $objConsultaPersona->getLogin();
                $objConsultaEmpresa = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneBy(
                    array('nombreEmpresa' => strtoupper(trim($arrayData['empresa'])))
                );

                if (!is_object($objConsultaEmpresa))
                {
                    $boolFlagError = true;
                    throw new \Exception("La empresa ".$arrayData['empresa']." no existe en Telcos.");
                }

                if(!empty($strLoginAsignado))
                {
                    $objInfoPersonaAsig = $emComercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($objConsultaPersona->getLogin());
                    if (!is_object($objInfoPersonaAsig) || !in_array($objInfoPersonaAsig->getEstado(), array('Activo','Pendiente','Modificado')))
                    {
                        $boolFlagError = true;
                        throw new \Exception('El login '.$strLoginAsignado.' no existe en telcos o no se encuentra Activo.');
                    }

                    $objPersonaDepartamento = $emComercial->getRepository("schemaBundle:InfoPersona")
                                        ->getPersonaDepartamentoPorUserEmpresa($strLoginAsignado, $objConsultaEmpresa->getId());

                    $objDepartamento = $emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                            ->findOneBy(array('nombreDepartamento' => $objPersonaDepartamento["NOMBRE_DEPARTAMENTO"],
                                                                'empresaCod' => $objConsultaEmpresa->getId(),
                                                                'estado'     => array('Activo','Modificado')));

                    $strNombrePerAsigna  = $objInfoPersonaAsig->getNombres()." ".$objInfoPersonaAsig->getApellidos();
                    $intIdPersonaAsig    = $objInfoPersonaAsig->getId();
                    $arrayDatosPersona   = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                            ->getInfoDatosPersona(array ('strRol'            => 'Empleado',
                                                                'strPrefijo'                 => $objConsultaEmpresa->getPrefijo(),
                                                                'strEstadoPersona'           => array('Activo','Pendiente','Modificado'),
                                                                'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                'strDepartamento'            => $objPersonaDepartamento["NOMBRE_DEPARTAMENTO"],
                                                                'strLogin'                   => $strLoginAsignado));
                    if(!empty($arrayDatosPersona['result'][0]))
                    {
                        $intIdPerRolAsiga = $arrayDatosPersona['result'][0]['idPersonaEmpresaRol'];
                    }
                    else
                    {
                        $boolFlagError = true;
                        throw new \Exception('La persona '.$arrayDatosPersona['result'][0].' no existe en telcos o no se encuentra Activo.');

                    }
                }
            }else
            {
                $objInfoPersonaAsig = $emComercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($arrayDataAudit['usrCreacion']);
                if (!is_object($objInfoPersonaAsig) || !in_array($objInfoPersonaAsig->getEstado(), array('Activo','Pendiente','Modificado')))
                {
                    $boolFlagError = true;
                    throw new \Exception('El login '.$arrayDataAudit['usrCreacion'].' no existe en telcos o no se encuentra Activo.');
                }

                $objConsultaEmpresa = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneBy(
                    array('prefijo' => strtoupper(trim($arrayDataAudit['prefijoEmpresaSolicitante'])))
                );

                if (!is_object($objConsultaEmpresa))
                {
                    $boolFlagError = true;
                    throw new \Exception("La empresa con el prefijo ".$arrayDataAudit['prefijoEmpresaSolicitante']." no existe en Telcos.");
                }

                $objPersonaDepartamento = $emComercial->getRepository("schemaBundle:InfoPersona")
                                        ->getPersonaDepartamentoPorUserEmpresa($arrayDataAudit['usrCreacion'], $objConsultaEmpresa->getId());

                $strNombreDepartamento = $objPersonaDepartamento["NOMBRE_DEPARTAMENTO"];

                $objDepartamento = $emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                    ->findOneBy(array('nombreDepartamento' => $strNombreDepartamento,
                                        'estado'             => array('Activo','Modificado')));

                if (!is_object($objDepartamento))
                {
                    $boolFlagError = true;
                    throw new \Exception("No existe departamento para el usuario ".$arrayDataAudit['usrCreacion'].
                                         " de empresa ".strtoupper($arrayDataAudit['prefijoEmpresaSolicitante']));
                }

                $arrayParamRoles = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne('ROLES_PERMITIDOS_CREAR_TAREAS_EXTERNAS','','CREAR_TAREAS_EXTERNAS','','','','','');
                $strNombrePerAsigna  = $objInfoPersonaAsig->getNombres()." ".$objInfoPersonaAsig->getApellidos();
                $intIdPersonaAsig    = $objInfoPersonaAsig->getId();
                $arrayDatosPersona   = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                        ->getInfoDatosPersona(array('strRol'                 => explode(",", $arrayParamRoles['valor1']),
                                                                'strPrefijo'                 => $objConsultaEmpresa->getPrefijo(),
                                                                'strEstadoPersona'           => array('Activo','Pendiente','Modificado'),
                                                                'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                'strDepartamento'            => $objPersonaDepartamento["NOMBRE_DEPARTAMENTO"],
                                                                'strLogin'                   => $arrayDataAudit['usrCreacion']));
                if(!empty($arrayDatosPersona['result'][0]))
                {
                    $intIdPerRolAsiga = $arrayDatosPersona['result'][0]['idPersonaEmpresaRol'];
                }
                else
                {
                    $boolFlagError = true;
                    throw new \Exception('La persona '.$arrayDatosPersona['result'][0].' no existe en telcos o no se encuentra Activo.');
                }
            }

            if($arrayData['fechaSolicitada'] == "")
            {
                $arrayData['fechaSolicitada'] = new \DateTime('now');
            }else
            {
                $arrayFormatoFecha = explode(' ', $arrayData['fechaSolicitada']);
                $arrayFecha = explode('-', $arrayFormatoFecha[0]);
                if(count($arrayFecha) !== 3 || !checkdate($arrayFecha[1], $arrayFecha[0], $arrayFecha[2]))
                {
                    $boolFlagError = true;
                    throw new \Exception('El formato de fecha '.$arrayData['fechaSolicitada'].' es invalido.');
                }

                if (strtotime($arrayFormatoFecha[1]) === false)
                {
                    $boolFlagError = true;
                    throw new \Exception('El formato de hora '.$arrayData['fechaSolicitada'].' es invalido.');
                }
                $arrayData['fechaSolicitada'] =  new \DateTime($arrayData['fechaSolicitada']);
            }
            //Ingreso de archivo
            if($arrayData['adjunto'] !== "" && $arrayData['extAdjunto'] !== "" && $arrayData['nombreAdjunto'] !== "")
            {
                $arrayRespuestaExtensionesRes = $objSoporteService->getExtensionesDeArchivosRestringidas();
                if(strpos($arrayRespuestaExtensionesRes['extensiones'], strtolower($arrayData['extAdjunto'])) !== false)
                {
                    $boolFlagError = true;
                    throw new \Exception('Archivo con extensión (' . $arrayData['extAdjunto'] . ') no es permitida');
                }

                $strFile           = $arrayData['adjunto'];
                $strPrefijo        = substr(md5(uniqid(rand())),0,6);
                $strNombreAdjunto = str_replace('.', '-', $arrayData['nombreAdjunto']);
                $strNombreArchivo = str_replace(' ', '_', strtoupper($strNombreAdjunto))."_".$strPrefijo.".".$arrayData['extAdjunto'];
                $arrayReplace = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z','ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A',
                                    'Æ'=>'A', 'Ç'=>'C', 'È'=>'E','É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
                                    'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O','Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U',
                                    'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B','ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a',
                                    'æ'=>'a', 'ç'=>'c', 'è'=>'e','é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
                                    'ð'=>'o', 'ñ'=>'n', 'ò'=>'o','ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u',
                                    'û'=>'u', 'ý'=>'y', 'þ'=>'b','ÿ'=>'y', '#'=>'_');
                $strNombreArchivo = strtoupper(strtr($strNombreArchivo, $arrayReplace));
                $arrayParamNfs     = array('prefijoEmpresa'       => $strPrefijoEmpresa,
                                            'strApp'               => 'TelcosWeb',
                                            'arrayPathAdicional'   => [],
                                            'strBase64'            => $strFile,
                                            'strNombreArchivo'     => $strNombreArchivo,
                                            'strUsrCreacion'       => $arrayDataAudit['usrCreacion'],
                                            'strSubModulo'         => "Tareas");

                $arrayRespNfs = $serviceUtil->guardarArchivosNfs($arrayParamNfs);
                if ($arrayRespNfs['intStatus'] == 200 )
                {
                    $strFicheroSubido = $arrayRespNfs['strUrlArchivo'];
                }
                else
                {
                    $boolFlagError = true;
                    throw new \Exception('Ocurrio un error al subir archivo al servidor Nfs : '.$arrayRespNfs['strMensaje']);
                }
            }else if($arrayData['adjunto'] !== "" && $arrayData['extAdjunto'] == "")
            {
                $boolFlagError = true;
                throw new \Exception('La extension del archivo adjunto no tiene valor asignado.');
            }else if($arrayData['adjunto'] !== "" && $arrayData['nombreAdjunto'] == "")
            {
                $boolFlagError = true;
                throw new \Exception('El nombre del archivo adjunto no tiene valor asignado.');
            }

            if(!filter_var($arrayDataAudit['ipCreacion'], FILTER_VALIDATE_IP))
            {
                $boolFlagError = true;
                throw new \Exception('Ip no es valida : '.$arrayDataAudit['ipCreacion']);
            }

            $objConsultaEmpresaCreacion = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneBy(
                array('prefijo' => strtoupper(trim($arrayDataAudit['prefijoEmpresaSolicitante']))));

            $arrayParametrosGeneracionTarea['strObservacion']     = $arrayData['observacion'];
            $arrayParametrosGeneracionTarea['strUsrCreacion']     = $arrayDataAudit['usrCreacion'];
            $arrayParametrosGeneracionTarea['strIpCreacion']      = $arrayDataAudit['ipCreacion'];
            $arrayParametrosGeneracionTarea['intDetalleSolId']    = null;
            $arrayParametrosGeneracionTarea['strTipoAfectado']    = "Cliente";
            $arrayParametrosGeneracionTarea['objPunto']           = $objPunto;
            $arrayParametrosGeneracionTarea['objDepartamento']    = $objDepartamento;
            $arrayParametrosGeneracionTarea['strCantonId']        = $intIdCanton;
            $arrayParametrosGeneracionTarea['strEmpresaCod']      = $objConsultaEmpresaCreacion->getId();
            $arrayParametrosGeneracionTarea['strPrefijoEmpresa']  = $strPrefijoEmpresa;
            $arrayParametrosGeneracionTarea['intTarea']           = $objTarea;
            $arrayParametrosGeneracionTarea["strBanderaTraslado"] = "N";
            $arrayParametrosGeneracionTarea["boolEnviaCorreo"]    = true;
            $arrayParametrosGeneracionTarea["esAutomatico"]       = "N";
            $arrayParametrosGeneracionTarea["origen"]             = 'ws';
            $arrayParametrosGeneracionTarea["seguimiento"]        = $arrayData['observacion'];
            $arrayParametrosGeneracionTarea["strAplicacion"]      = 'telcoSys';
            $arrayParametrosGeneracionTarea["intIdTareaTelcoSys"] = 1;
            $arrayParametrosGeneracionTarea["nombreClase"]        = $arrayClases[strtoupper($arrayData['nombreClase'])];
            $arrayParametrosGeneracionTarea["formaContacto"]      = $arrayOrigenes[strtoupper($arrayData['origen'])];
            $arrayParametrosGeneracionTarea["cierreInmediato"]    = $arrayData['cierreInmediato'];
            $arrayParametrosGeneracionTarea["strUrlAdjunto"]      = $strFicheroSubido;
            $arrayParametrosGeneracionTarea["strNombreAdjunto"]   = $strNombreArchivo;
            $arrayParametrosGeneracionTarea["strExtensionAdjunto"]  = strtoupper($arrayData['extAdjunto']);
            $arrayParametrosGeneracionTarea["strFechaSolicitada"]   = $arrayData['fechaSolicitada'];
            $arrayParametrosGeneracionTarea["strIdPersonaAsig"]     = $intIdPersonaAsig;
            $arrayParametrosGeneracionTarea["strNombrePersonaAsig"] = $strNombrePerAsigna;
            $arrayParametrosGeneracionTarea["strIdPerRolAsig"]      = $intIdPerRolAsiga;
            $arrayParametrosGeneracionTarea["asignarTarea"]         = $arrayData['asignarTarea'];

            $strNumeroTarea = $objSoporteService->crearTareaExterna($arrayParametrosGeneracionTarea);
            if(empty($strNumeroTarea))
            {
                throw new \Exception('Error al crear la tarea de forma automatica.');
            }
            else
            {
                $arrayRespuesta['status']    = 200;
                $arrayRespuesta['mensaje']   = 'Tarea creada satisfactoriamente.';
                if($arrayData['asignarTarea'] == "S")
                {
                    $arrayRespuesta['data']['usuarioAsignado'] = $objConsultaPersona->getLogin();
                }else
                {
                    $arrayRespuesta['data']['usuarioAsignado'] = $arrayDataAudit['usrCreacion'];
                }

                $arrayRespuesta['data']['numeroTarea'] = $strNumeroTarea;

                return $arrayRespuesta;
            }
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error general en creación de Tarea automática, por favor comunicarse con Sistemas';

            if(isset($boolFlagError) && $boolFlagError)
            {
                $strMessage = $objException->getMessage();
            }

            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
            $serviceUtil->insertError('Telcos+',
                                        'TelcoSysWSController->putCrearTareaExterna()',
                                        $strCodigo.'|'.$objException->getMessage(),
                                        $arrayDataAudit['usrCreacion'],
                                        $arrayDataAudit['ipCreacion']);
            $serviceUtil->insertError('Telcos+',
                                        'TelcoSysWSController->putCrearTareaExterna()',
                                        $strCodigo,
                                        $arrayDataAudit['usrCreacion'],
                                        $arrayDataAudit['ipCreacion']);
            $objConsultaCodEmpresa = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneBy(
                array('prefijo' => strtoupper(trim($arrayDataAudit['prefijoEmpresaSolicitante']))));
            $serviceUtil->insertLog(array(
                'enterpriseCode'   => $objConsultaCodEmpresa->getId(),
                'logType'          =>  1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'putCrearTareaExterna',
                'descriptionError' => $objException->getMessage(),
                'status'           => 'Fallido'));

            $arrayRespuesta['status']                   = 400;
            $arrayRespuesta['mensaje']                  = $strMessage;
            $arrayRespuesta['data']['usuarioAsignado']  = "";
            $arrayRespuesta['data']['NumeroTarea']      = "";

            return $arrayRespuesta;
        }
    }

    /**
     * Método que permite registrar y cambiar equipos (permitidos).
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 05-05-2021
     *
     * @param  Array $arrayData
     * @return Array $arrayRespuesta
     */
    private function putRegistroCambioEquipo($arrayData)
    {
        $arrayRespuesta                     = array();
        $emComercial                        = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura                  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emGeneral                          = $this->getDoctrine()->getManager('telconet_general');
        $serviceSoporte                     = $this->get('soporte.SoporteService');
        $serviceUtil                        = $this->get('schema.Util');
        $strCodEmpresa                      = $arrayData['data']['idEmpresa'];
        $strPrefijoEmpresa                  = $arrayData['data']['prefijoEmpresa'];
        $strIpCreacion                      = '127.0.0.1';
        $strUsrCreacion                     = $arrayData['user'];
        $arrayEquipos                       = $arrayData['data']['arrayEquipos'];
        $intPuntoId                         = $arrayData['data']['intIdPunto'];
        $intPersonaId                       = $arrayData['data']['personaId'];
        $strUbicacion                       = $arrayData['data']['ubicacion'];
        $strPropietario                     = $arrayData['data']['propietario'];
        $intIdDepartamento                  = $arrayData['data']['intIdDepartamento'];
        $intIdComunicacion                  = $arrayData['data']['intIdComunicacion'];
        $intDetalleId                       = $arrayData['data']['intDetalleId'];
        $strCodProgreso                     = $arrayData['data']['strCodProgreso'];
        $strOrigenProgreso                  = $arrayData['data']['strOrigenProgreso'];
        $intIdServicio                      = $arrayData['data']['intIdServicio'] ? $arrayData['data']['intIdServicio'] : 0;
        $strOrigen                          = $arrayData['data']['strOrigen'] ? $arrayData['data']['strOrigen'] : 'MOVIL';
        $intPersonaEmpresaRolId             = 0;
        $intTipoElementoId                  = 0; 
        $intElementoId                      = "";   
        $strEstado                          = "Activo";
        $strFeCreacion                      = new \DateTime('now');

        try
        {
            $objServicioPunto = $emComercial->getRepository('schemaBundle:InfoServicio')
            ->obtieneProductoInternetxPunto($intPuntoId); 

            if(is_object($objServicioPunto) && $objServicioPunto->getEstado() == 'Activo')
            {
                $intIdServicioInternet = $objServicioPunto->getId();

                $objServicioTecnico = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                ->findOneBy(array( "servicioId" => $intIdServicioInternet));

                if (!is_object($objServicioTecnico))
                {
                    throw new \Exception("No se encontró información técnica del servicio");
                }

                $objElemento        = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                    ->find($objServicioTecnico->getElementoClienteId());

                if (!is_object($objElemento))
                {
                    throw new \Exception("No se encontró información del elemento del servicio");
                }

                $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                ->findOneBy( array( 'nombreParametro' => 'MODELOS_EQUIPOS_VALIDAR_MOVIL',
                                    'estado'      => 'Activo' ) );

                $intIdParametroCab = 0;

                if($objParametroCab)
                {
                    $intIdParametroCab = $objParametroCab->getId();
                }

                for($intIteration = 0; $intIteration < count($arrayEquipos); $intIteration++)
                {
                    $strModeloEquipo   = $arrayEquipos[$intIteration]['strModeloEquipo'];
                    $strMacEquipo      = $arrayEquipos[$intIteration]['strMacEquipo'];
                    $strSerieEquipo    = $arrayEquipos[$intIteration]['strSerieEquipo'];

                    $arrayDetFiltro             = array(
                                                        'parametroId'   => $intIdParametroCab,
                                                        'valor2'        => $strModeloEquipo,
                                                        'estado'        => 'Activo'     
                                                    );
                    
                    $objParametroEquipo = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy($arrayDetFiltro);                     

                    if (!is_object($objParametroEquipo))
                    {
                        throw new \Exception("No se encontró información del nombre del tipo de elemento");
                    }
                    
                    
                    $strNombreTipoElemento = $objParametroEquipo->getValor3();
                                        
                    $objTipoElemento    = $emComercial->getRepository('schemaBundle:AdmiTipoElemento')
                    ->findOneBy(array( "nombreTipoElemento" => $strNombreTipoElemento));
                    
                    if (!is_object($objTipoElemento))
                    {
                        throw new \Exception("No se encontró información del tipo de elemento");
                    }
                    

                    $intTipoElementoId = $objTipoElemento->getId();

                    $arrayParametrosPersonaEmpresaRol = array(
                        'intIdPersona'  => $intPersonaId,
                        'strDescRol'    => 'Cliente',
                        'intCodEmpresa' => $strCodEmpresa
                    );

                    $objPersonaEmpRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                            ->getPersonaEmpresaRolPorPersonaPorTipoRolNew($arrayParametrosPersonaEmpresaRol);
                    
                    if(is_object($objPersonaEmpRol))
                    {
                        $intPersonaEmpresaRolId = $objPersonaEmpRol->getId();
                    }

                    //grabar elementos
                    $objElementoInstalacion = new InfoElementoInstalacion();
                    $objElementoInstalacion->setPersonaEmpresaRolId($intPersonaEmpresaRolId);
                    $objElementoInstalacion->setPuntoId($intPuntoId);
                    $objElementoInstalacion->setTipoElementoId($intTipoElementoId);
                    $objElementoInstalacion->setSerieElemento($strSerieEquipo);
                    $objElementoInstalacion->setElementoId($intElementoId);
                    $objElementoInstalacion->setServicioId($intIdServicioInternet);
                    $objElementoInstalacion->setIpElemento(null);
                    $objElementoInstalacion->setUbicacion($strUbicacion);
                    $objElementoInstalacion->setPropietario($strPropietario);
                    $objElementoInstalacion->setEstado($strEstado);
                    $objElementoInstalacion->setUsrCreacion($strUsrCreacion);
                    $objElementoInstalacion->setFeCreacion($strFeCreacion);
                    $emInfraestructura->persist($objElementoInstalacion);
                    $emInfraestructura->flush(); 

                    if($strNombreTipoElemento == 'CPE ONT')
                    {
                        $objSolicitudAgregarEquipo = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                        ->findOneBy(array("servicioId"      => $intIdServicio,
                                          "tipoSolicitudId" => 131));
                        
                        if (is_object($objSolicitudAgregarEquipo))
                        {
                            if($objSolicitudAgregarEquipo->getEstado() != 'Finalizada')
                            {
                                $arrayPeticiones[] = array( 
                                    'intIdDepartamento'                 => $intIdDepartamento,
                                    'idEmpresa'                         => $strCodEmpresa,
                                    'prefijoEmpresa'                    => $strPrefijoEmpresa,
                                    'idServicio'                        => $intIdServicio,
                                    'idElemento'                        => $objElemento->getId(),
                                    'modeloCpe'                         => $strModeloEquipo,
                                    'macCpe'                            => $strMacEquipo,
                                    'serieCpe'                          => $strSerieEquipo,
                                    'tipoElementoCpe'                   => $strNombreTipoElemento,
                                    'strTieneMigracionHw'               => "NO",
                                    'strEsSmartWifi'                    => "NO",
                                    'strEsApWifi'                       => "NO",
                                    'usrCreacion'                       => $strUsrCreacion,
                                    'esPseudoPe'                        => 'N',
                                    'ipCreacion'                        => $strIpCreacion,
                                    'serNaf'                            => $this->container->getParameter('database_host_naf'),
                                    'ptoNaf'                            => $this->container->getParameter('database_port_naf'),
                                    'sidNaf'                            => $this->container->getParameter('database_name_naf'),
                                    'usrNaf'                            => $this->container->getParameter('user_naf'),
                                    'pswNaf'                            => $this->container->getParameter('passwd_naf'),
                                    'host'                              => $this->container->getParameter('host'),
                                    'strEsExtenderDualBand'             => "NO",
                                    'strOrigen'                         => $strOrigen,
                                    'strEsCambioOntPorSolAgregarEquipo' => "SI"
                                    );
                    
                                    $serviceCambioElemento          = $this->get('tecnico.InfoCambioElemento');
                                    $arrayRespuestaCambioElemento   = $serviceCambioElemento->cambioElemento($arrayPeticiones);
                                    
                                    if($arrayRespuestaCambioElemento[0]['status'] != "OK")
                                    {
                                        throw new \Exception($arrayRespuestaCambioElemento[0]['mensaje']);
                                    }
                            }
                        }
                        else
                        {
                            throw new \Exception('No se encontró ninguna solicitud para agregar equipo');
                        }
                    }
                }

                $arrayProgreso     = array(
                    'strCodEmpresa'         => $strCodEmpresa,
                    'intIdTarea'            => $intIdComunicacion,
                    'intIdDetalle'          => $intDetalleId,
                    'strCodigoTipoProgreso' => $strCodProgreso,
                    'intIdServicio'         => 0,
                    'strOrigen'             => $strOrigenProgreso,
                    'strUsrCreacion'        => $strUsrCreacion,
                    'strIpCreacion'         => $strIpCreacion);

                $arrayRespuesta         = $serviceSoporte->ingresarProgresoTarea($arrayProgreso); 
                $strMensajeProg         = $arrayRespuesta['mensaje'];
                $strStatusProg          = $arrayRespuesta['status'];

                if($strStatusProg != 'OK' && strpos($strMensajeProg, 'Ya existe un registro del progreso de la tarea') === false)
                {
                    throw new \Exception($strMensajeProg);   
                } 
            }
            else
            {
                throw new \Exception("No se puede realizar el registro de equipos, "
                                        ."el servicio de Internet no se encuentra activo.");
            }  

            $strStatus  = $this->status['OK'];
            $strMensaje = "Equipos registrados correctamente";
        } 
        catch(\Exception $exception)
        {

            $strStatus  = $this->status['ERROR'];
            $strMensaje = $exception->getMessage();
            
            $serviceUtil->insertLog(array(
                'enterpriseCode'   => $strCodEmpresa,
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'putRegistroCambioEquipo',
                'descriptionError' => $exception->getMessage(),
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $strUsrCreacion));
        }

        $arrayRespuesta['status']   = $strStatus;
        $arrayRespuesta['mensaje']  = $strMensaje;

        return $arrayRespuesta;
    }

    /**
     * Método que permite validar equipos permitidos para su registro.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 05-05-2021
     *
     * @param  Array $arrayData
     * @return Array $arrayRespuesta
     */
    private function putValidarEquiposPermitidos($arrayData)
    {
        $arrayRespuesta         = array();
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil            = $this->get('schema.Util');
        $serviceTecnico         = $this->get('tecnico.InfoServicioTecnico');
        $boolModeloCompatible   = false; 
        $arrayElemento          = null;
        $strMac                 = '';
        $strModelo              = null;
        $strUsrCreacion         = $arrayData['user'];

        try
        {
            $strSerieEquipo     = $arrayData['data']['strSerieEquipo'];
            $strModeloEquipo    = '';

            $arrayRespuestaNaf = $serviceTecnico->buscarElementoEnNaf($strSerieEquipo, $strModeloEquipo, "PI", "ActivarServicio");

            if($arrayRespuestaNaf[0]['status']=="OK")
            {
                $arrayInfoNaf       = $arrayRespuestaNaf[0]['mensaje'];
                $arrayResultado     = explode(",",$arrayInfoNaf);

                if(count($arrayResultado)>1)
                {
                    $strMac        = $arrayResultado[1];
                    $strModelo     = $arrayResultado[2];
                }
                else
                {
                    throw new \Exception("NULL");
                }
            }
            else
            {
                $strMensajeRespuesta = $arrayRespuestaNaf[0]['mensaje'];
                throw new \Exception("ERROR_PARCIAL");
            }

            $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
            ->findOneBy( array( 'nombreParametro' => 'MODELOS_EQUIPOS_VALIDAR_MOVIL',
                                'estado'      => 'Activo' ) );

            $intIdParametroCab = 0;

            if(is_object($objParametroCab))
            {
                $intIdParametroCab = $objParametroCab->getId();
            }

            $arrayDetFiltro     = array(
                'parametroId'   => $intIdParametroCab,
                'valor2'        => $strModelo,
                'valor5'        => 'S',
                'estado'        => 'Activo'     
            );

            $objParametroEquipo = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->findOneBy($arrayDetFiltro);  
            
            
            if(!is_object($objParametroEquipo))
            {
                $strMensajeRespuesta = "El modelo de la serie ingresada no esta habilitado para registrarse, "
                . "favor ingresar una serie diferente."; 

                throw new \Exception("ERROR_PARCIAL");                
            }

            $arrayElemento  = array('descripcion' => '', 'mac' => $strMac, 'modelo' => $strModelo);
            $strStatus      = $this->status['OK'];
            $strMensaje     = $this->mensaje['OK'];
        } 
        catch(\Exception $exception)
        {

            if($exception->getMessage() == "NULL")
            {
                $strStatus   = $this->status['NULL'];
                $strMensaje  = $this->mensaje['NULL'];
            }
            else if($exception->getMessage() == "ERROR_PARCIAL")
            {
                $strStatus   = $this->status['ERROR_PARCIAL'];
                $strMensaje  = $strMensajeRespuesta;
            }
            else
            {
                $strStatus   = $this->status['ERROR'];
                $strMensaje  = $this->mensaje['ERROR'];
            }
            
            $serviceUtil->insertLog(array(
                'enterpriseCode'   => '10',
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'putValidarEquiposPermitidos',
                'descriptionError' => $strMensaje,
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $strUsrCreacion));
        }

        $arrayRespuesta['elemento'] = $arrayElemento;
        $arrayRespuesta['status']   = $strStatus;
        $arrayRespuesta['mensaje']  = $strMensaje;

        return $arrayRespuesta;
    } 

    /**
    *
     * Método que permite obtener la hipotesis de cierre de caso Hal.
     *
     * @author Pedro Velez Quiroz <psvele@telconet.ec>
     * @version 1.0 28-07-2021
     *
     * @param  Array $arrayData
     * @return Array $arrayResultado
     */
    private function getHipotesisCierreCasoHal($arrayData)
    { 
        $arrayResultado = array();
        try 
        {
            $objSoporteService  = $this->get('soporte.SoporteService');

            $arrayRespuesta  = $objSoporteService->obtenerHipotesisCierreCasoHal($arrayData['data']['idTarea']);
            if($arrayRespuesta['status'] === "OK" )
            {
                $arrayResultado['data']['idHipotesis']     = $arrayRespuesta['idHipotesis'];
                $arrayResultado['data']['nombreHipotesis'] = $arrayRespuesta['nombreHipotesis'];
                $arrayResultado['status']               = $arrayRespuesta['status'];
                $arrayResultado['mensaje']              = $arrayRespuesta['mensaje'];
            }
            else
            {
                $arrayResultado['data']    = null;
                $arrayResultado['status']  = "ERROR";
                $arrayResultado['mensaje'] = $arrayRespuesta['mensaje'];
            }    

        } 
        catch (\Exception $ex) 
        {
            $arrayResultado['status']  = "ERROR";
            $arrayResultado['mensaje'] = $ex->getMessage();
            $arrayResultado['data']    = null;
        }
        return $arrayResultado;
    }

    /**
     * Método que permite cambiar y retirar equipos ingresados desde el movil.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 01-06-2021
     *
     * @param  Array $arrayData
     * @return Array $arrayRespuesta
     */
    private function putCambiarYRetirarEquiposEnNodo($arrayData)
    {
        $arrayRespuesta             = array();
        $serviceUtil                = $this->get('schema.Util');
        $serviceInfoCambioElemento  = $this->get("tecnico.InfoCambioElemento");
        $serviceElemento            =  $this->get('tecnico.InfoElemento');
        $emGeneral                  = $this->getDoctrine()->getManager('telconet_general');
        $emComercial                = $this->getDoctrine()->getManager("telconet");
        $strUsrCreacion             = $arrayData['user'];
        $strIpUsuario               = '127.0.0.1';
        $strMensajeRespuesta        = '';
        $arrayCambiarElemento       = array(); 
        $arrayRetirarElemento       = array();
        $intIdDetalle               = $arrayData['data']['idDetalle'];
        $intIdEmpresa               = $arrayData['data']['idEmpresa'];
        $strTipoResponsable         = $arrayData['data']['tipoResponsable'];
        $intIdResponsable           = $arrayData['data']['idResponsable'];
        $intIdElementoNodo          = $arrayData['data']['intIdElementoNodo'];

        try
        {

            //Parametros fijos para realizar el cambio de equipo.
            $arrayCambiarElemento['boolPerteneceElementoNodo'] = true;
            $arrayCambiarElemento['intNumeroTarea']            = $arrayData['data']['numeroTarea'];
            $arrayCambiarElemento['intIdDetalle']              = $intIdDetalle;
            $arrayCambiarElemento['intIdEmpresa']              = $intIdEmpresa;
            $arrayCambiarElemento['intIdResponsable']          = $intIdResponsable;
            $arrayCambiarElemento['strTipoResponsable']        = $strTipoResponsable;
            $arrayCambiarElemento['strUsuario']                = $strUsrCreacion;
            $arrayCambiarElemento['strIpUsuario']              = $strIpUsuario;
            $arrayCambiarElemento['intIdElementoNodo']         = $intIdElementoNodo;


            //Parametros fijos para realizar el retiro de equipo.
            $arrayRetirarElemento['intIdDetalle']       = $intIdDetalle;
            $arrayRetirarElemento['strIdEmpresa']       = $intIdEmpresa;
            $arrayRetirarElemento['strTipoResponsable'] = $strTipoResponsable;
            $arrayRetirarElemento['intIdResponsable']   = $intIdResponsable;
            $arrayRetirarElemento['strUsuario']         = $strUsrCreacion;
            $arrayRetirarElemento['strIpUsuario']       = $strIpUsuario;
            $arrayRetirarElemento['intIdElementoNodo']  = $intIdElementoNodo;

            foreach ($arrayData['data']['equiposNodo'] as $arrayEquiposNodo)
            {
                $arrayCambiarElemento['intIdSolicitud']            = $arrayEquiposNodo['intIdSolicitud'];
                $arrayCambiarElemento['intIdDispositivoActual']    = $arrayEquiposNodo['intIdElemento'];
                $arrayCambiarElemento['strNombreNuevoElemento']    = $arrayEquiposNodo['strNombreNuevoElemento'];
                $arrayCambiarElemento['strSerieDispositivoNuevo']  = $arrayEquiposNodo['strSerieNuevoElemento'];
                $arrayCambiarElemento['strModeloDispositivoNuevo'] = $arrayEquiposNodo['strModeloNuevoElemento'];
                $arrayCambiarElemento['strMacDispositivoNuevo']    = $arrayEquiposNodo['strMacNuevoElemento'];
                $arrayCambiarElemento['strTipoDispositivoNuevo']   = $arrayEquiposNodo['strTipoElemento'];

                $arrayRespuestaCambio = $serviceInfoCambioElemento->cambioDispositivoNodo($arrayCambiarElemento);

                if($arrayRespuestaCambio['status'])
                {
                    $arrayRetirarElemento['intIdSolicitud']  = $arrayRespuestaCambio['data']['idSolicitudRetiro'];
                    $arrayRetirarElemento['intIdElemento']   = $arrayEquiposNodo['intIdElemento'];
                    //bandera para no finalizar la solicitud 
                    $arrayRetirarElemento['solRetiroAutomatico']   = true;
                    $arrayRespuestaRetiro = $serviceElemento->retirarElementoPerteneceNodo($arrayRetirarElemento);

                    if(!$arrayRespuestaRetiro['status'])
                    {
                        $serviceUtil->insertLog(array(
                            'enterpriseCode'   => '10',
                            'logType'          => 1,
                            'logOrigin'        => 'TELCOS',
                            'application'      => 'TELCOS',
                            'appClass'         => 'SoporteWSController',
                            'appMethod'        => 'putCambiarYRetirarEquiposEnNodo->retirarElementoPerteneceNodo',
                            'descriptionError' => $arrayRespuestaRetiro['message'],
                            'status'           => 'Fallido',
                            'inParameters'     => json_encode($arrayRetirarElemento),
                            'creationUser'     => $strUsrCreacion));
                    }
                }
                else
                {
                    $serviceUtil->insertLog(array(
                        'enterpriseCode'   => '10',
                        'logType'          => 1,
                        'logOrigin'        => 'TELCOS',
                        'application'      => 'TELCOS',
                        'appClass'         => 'SoporteWSController',
                        'appMethod'        => 'putCambiarYRetirarEquiposEnNodo->cambioDispositivoNodo',
                        'descriptionError' => $arrayRespuestaCambio['message'],
                        'status'           => 'Fallido',
                        'inParameters'     => json_encode($arrayCambiarElemento),
                        'creationUser'     => $strUsrCreacion));
                }
            }

            $strStatus      = $this->status['OK'];
            $strMensaje     = $this->mensaje['OK'];
        } 
        catch(\Exception $exception)
        {

            if($exception->getMessage() == "NULL")
            {
                $strStatus   = $this->status['NULL'];
                $strMensaje  = $this->mensaje['NULL'];
            }
            else if($exception->getMessage() == "ERROR_PARCIAL")
            {
                $strStatus   = $this->status['ERROR_PARCIAL'];
                $strMensaje  = $strMensajeRespuesta;
            }
            else
            {
                $strStatus   = $this->status['ERROR'];
                $strMensaje  = $this->mensaje['ERROR'];
            }
            
            $serviceUtil->insertLog(array(
                'enterpriseCode'   => '10',
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'putCambiarYRetirarEquiposEnNodo',
                'descriptionError' => $strMensaje,
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $strUsrCreacion));
        }

        $arrayRespuesta['status']   = $strStatus;
        $arrayRespuesta['mensaje']  = $strMensaje;

        return $arrayRespuesta;
    }

    /**
     * Método que permite realizar el cargo y descargo de los equipos en nodo desde el movil.
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 01-06-2021
     *
     * @param  Array $arrayData
     * @return Array $arrayRespuesta
     */
    private function putCargaDescargaEquiposNodoInstalacion($arrayData)
    {
        $arrayRespuesta             = array();
        $serviceUtil                = $this->get('schema.Util');
        $serviceInfoCambioElemento  = $this->get("tecnico.InfoCambioElemento");
        $serviceElemento            = $this->get('tecnico.InfoElemento');
        $serviceGeneral             = $this->get('tecnico.InfoServicioTecnico');
        $emNaf                      = $this->getDoctrine()->getManager("telconet_naf");
        $emGeneral                  = $this->getDoctrine()->getManager('telconet_general');
        $emComercial                = $this->getDoctrine()->getManager("telconet");
        $strUsrCreacion             = $arrayData['user'];
        $strIpUsuario               = '127.0.0.1';
        $strMensajeRespuesta        = '';
        $boolPerteneceElementoNodo  = true;
        $intIdElementoNodo          = $arrayData['data']['idElementoNodo'];
        $intIdEmpresa               = $arrayData['data']['idEmpresa'] ? $arrayData['data']['idEmpresa'] : '10';
        $intIdEmpleado              = $arrayData['data']['idEmpleado'];
        $intNumeroTarea             = $arrayData['data']['numeroTarea'];

        try
        {

            foreach ($arrayData['data']['equiposNodo'] as $arrayEquiposNodo)
            {

                $strSerieElementoNuevo          = $arrayEquiposNodo['strSerie'];
                $strModeloElementoNuevo         = $arrayEquiposNodo['strModelo'];
                $strMacElementoNuevo            = $arrayEquiposNodo['strMac'];
                $strTipoElementoNuevo           = $arrayEquiposNodo['strTipoElemento'];

                //Descarga Empleado y Carga a Telconet, equipos en el nodo.
                $arrayRequestObtenerEquiposAsignados = array('boolPerteneceElementoNodo' => $boolPerteneceElementoNodo,
                                                'strIdEmpresa'    =>  $intIdEmpresa,
                                                'intIdPersona'    =>  $intIdEmpleado,
                                                'strEstadoEquipo' => 'PI',
                                                'strNumeroSerie'  =>  $strSerieElementoNuevo,
                                                'strModelo'       =>  $strModeloElementoNuevo,
                                                'strTipoElemento' =>  $strTipoElementoNuevo);

                $arrayActivosCliente = $emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                ->obtenerEquiposAsignados($arrayRequestObtenerEquiposAsignados);

                if ($arrayActivosCliente['status'])
                {
                    $arrayActivoCliente  = $arrayActivosCliente['result'][0];
                    $intIdControl        = $arrayActivoCliente['idControl'];
                    $arrayEquipos        = array();
                    $arrayEquipos[]      = array('strNumeroSerie'  => $strSerieElementoNuevo,
                                                'intIdControl'    => $intIdControl,
                                                'intCantidadEnt'  => 1,
                                                'intCantidadRec'  => 1,
                                                'strTipoArticulo' => 'Equipos');

                    $arrayCargaDescarga = array();
                    $arrayCargaDescarga['intNumeroTarea']           =  $intNumeroTarea;
                    $arrayCargaDescarga['intIdEmpresa']             =  $intIdEmpresa;
                    $arrayCargaDescarga['strTipoRecibe']            = 'Nodo';
                    $arrayCargaDescarga['intIdElementoNodo']        = $intIdElementoNodo;
                    $arrayCargaDescarga['intIdEmpleado']            =  $intIdEmpleado;
                    $arrayCargaDescarga['strTipoTransaccion']       = 'Instalacion';
                    $arrayCargaDescarga['strTipoActividad']         = 'InstalacionNodo';
                    $arrayCargaDescarga['strObservacion']           = 'Instalacion de nuevo elemento en nodo';
                    $arrayCargaDescarga['arrayEquipos']             =  $arrayEquipos;
                    $arrayCargaDescarga['strUsuario']               =  $strUsrCreacion;
                    $arrayCargaDescarga['strIpUsuario']             =  $strIpUsuario;
                    $arrayCargaDescarga['boolRegistrarTraking']     = true;
                    
                    $arrayRespuestaCargoDescargo = $serviceElemento->cargaDescargaActivos($arrayCargaDescarga);

                    if($arrayRespuestaCargoDescargo['status'])
                    {
                        //Parámetros para la creación del nuevo elemento.
                        $arrayElementoNodo = array();
                        $arrayElementoNodo['boolEsUbicacionNodo']         = true;                           //SI - true
                        $arrayElementoNodo['boolPerteneceElementoNodo']   = $boolPerteneceElementoNodo;     //SI - true
                        $arrayElementoNodo['intIdElementoNodo']           = $intIdElementoNodo;             //SI
                        $arrayElementoNodo['objServicio']                 = null;                           //NO
                        $arrayElementoNodo['intIdElementoActual']         = 0;                              //NO
                        $arrayElementoNodo['nombreElementoCliente']       = 'nodo-'.$strTipoElementoNuevo.'-'.$strSerieElementoNuevo;    //SI
                        $arrayElementoNodo['serieElementoCliente']        = $strSerieElementoNuevo;         //SI
                        $arrayElementoNodo['nombreModeloElementoCliente'] = $strModeloElementoNuevo;        //SI
                        $arrayElementoNodo['strMacDispositivo']           = $strMacElementoNuevo;           //(OPCIONAL DEPENDIENDO DEL EQUIPO)
                        $arrayElementoNodo['intIdEmpresa']                = $intIdEmpresa;                  //SI
                        $arrayElementoNodo['usrCreacion']                 = $strUsrCreacion;                //SI
                        $arrayElementoNodo['ipCreacion']                  = $strIpUsuario;                  //SI
                        
                        $strRespuestaIngresarElementoCliente = $serviceGeneral->ingresarElementoClienteTN($arrayElementoNodo,$strTipoElementoNuevo);
                        
                        if ($strRespuestaIngresarElementoCliente !== "" && is_string($strRespuestaIngresarElementoCliente))
                        {
                            $serviceUtil->insertLog(array(
                                'enterpriseCode'   => '10',
                                'logType'          => 1,
                                'logOrigin'        => 'TELCOS',
                                'application'      => 'TELCOS',
                                'appClass'         => 'SoporteWSController',
                                'appMethod'        => 'putCargaDescargaEquiposNodoInstalacion->ingresarElementoClienteTN',
                                'descriptionError' => $strRespuestaIngresarElementoCliente,
                                'status'           => 'Fallido',
                                'inParameters'     => json_encode($arrayElementoNodo),
                                'creationUser'     => $strUsrCreacion));
                        }
                        else
                        {
                            //Se actualiza el nuevo elemento en el naf.
                            $arrayParametrosNaf = array();
                            $arrayParametrosNaf['empresaCod']            = $intIdEmpresa;
                            $arrayParametrosNaf['modeloCpe']             = '';
                            $arrayParametrosNaf['tipoArticulo']          = 'AF';
                            $arrayParametrosNaf['identificacionCliente'] = '';
                            $arrayParametrosNaf['serieCpe']              = $strSerieElementoNuevo;
                            $arrayParametrosNaf['cantidad']              = 1;

                            $strMensajeErrorInstalacionElemento = $serviceInfoCambioElemento->procesaInstalacionElemento($arrayParametrosNaf);
                            
                            if (strlen(trim($strMensajeErrorInstalacionElemento)) > 0)
                            {
                                $serviceUtil->insertLog(array(
                                    'enterpriseCode'   => '10',
                                    'logType'          => 1,
                                    'logOrigin'        => 'TELCOS',
                                    'application'      => 'TELCOS',
                                    'appClass'         => 'SoporteWSController',
                                    'appMethod'        => 'putCargaDescargaEquiposNodoInstalacion->procesaInstalacionElemento',
                                    'descriptionError' => $strMensajeErrorInstalacionElemento,
                                    'status'           => 'Fallido',
                                    'inParameters'     => json_encode($arrayParametrosNaf),
                                    'creationUser'     => $strUsrCreacion));
                            }
                        }
                    }
                    else
                    {
                        $serviceUtil->insertLog(array(
                            'enterpriseCode'   => '10',
                            'logType'          => 1,
                            'logOrigin'        => 'TELCOS',
                            'application'      => 'TELCOS',
                            'appClass'         => 'SoporteWSController',
                            'appMethod'        => 'putCargaDescargaEquiposNodoInstalacion->cargaDescargaActivos',
                            'descriptionError' => $arrayRespuestaCargoDescargo['message'],
                            'status'           => 'Fallido',
                            'inParameters'     => json_encode($arrayCargaDescarga),
                            'creationUser'     => $strUsrCreacion));
                    }
                }
                else
                {
                    $serviceUtil->insertLog(array(
                        'enterpriseCode'   => '10',
                        'logType'          => 1,
                        'logOrigin'        => 'TELCOS',
                        'application'      => 'TELCOS',
                        'appClass'         => 'SoporteWSController',
                        'appMethod'        => 'putCargaDescargaEquiposNodoInstalacion->obtenerEquiposAsignados',
                        'descriptionError' => $arrayActivosCliente['message'],
                        'status'           => 'Fallido',
                        'inParameters'     => json_encode($arrayRequestObtenerEquiposAsignados),
                        'creationUser'     => $strUsrCreacion));
                }
            }

            $strStatus      = $this->status['OK'];
            $strMensaje     = $this->mensaje['OK'];
        } 
        catch(\Exception $exception)
        {

            if($exception->getMessage() == "NULL")
            {
                $strStatus   = $this->status['NULL'];
                $strMensaje  = $this->mensaje['NULL'];
            }
            else if($exception->getMessage() == "ERROR_PARCIAL")
            {
                $strStatus   = $this->status['ERROR_PARCIAL'];
                $strMensaje  = $strMensajeRespuesta;
            }
            else
            {
                $strStatus   = $this->status['ERROR'];
                $strMensaje  = $this->mensaje['ERROR'];
            }
            
            $serviceUtil->insertLog(array(
                'enterpriseCode'   => '10',
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'putCargaDescargaEquiposNodoInstalacion',
                'descriptionError' => $strMensaje,
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $strUsrCreacion));
        }

        $arrayRespuesta['status']   = $strStatus;
        $arrayRespuesta['mensaje']  = $strMensaje;

        return $arrayRespuesta;
    }

    /**
     * Método que permite saber que tipo de acción se realizará en una tarea interdepartamental
     * puede ser (RETIRO, CAMBIO o INSTALACION)
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 01-06-2021
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 02-03-2022 - Se modifica lógica para validar 
     * tipo de solicitud de instalacion
     *
     * @param  Array $arrayData
     * @return Array $arrayRespuesta
     */
    private function getMotivoIngresoAlNodo($arrayData)
    {
        $arrayRespuesta             = array();
        $serviceUtil                = $this->get('schema.Util');
        $emComercial                = $this->getDoctrine()->getManager("telconet");
        $emSoporte                  = $this->getDoctrine()->getManager("telconet_soporte");
        $strUsrCreacion             = $arrayData['user'];
        $strMensajeRespuesta        = '';
        $strTipoTareaRetiro         = 'RETIRO';
        $strTipoTareaCambio         = 'CAMBIO';
        $strTipoTareaInstalacion    = 'INSTALACION';
        $intNumeroTarea             = $arrayData['data']['numeroTarea'];
        $strTipoTarea               = '';

        try
        {

            $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(array ('descripcionCaracteristica' => 'SOLICITUD NODO',
                                'estado'                    => 'Activo'));

            if (is_object($objAdmiCaracteristica))
            {
                $objInfoTareaCaracteristica = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                        ->findOneBy( array('tareaId'          => $intNumeroTarea,
                                           'caracteristicaId' => $objAdmiCaracteristica->getId(),
                                           'estado'           => 'Activo'),
                                     array('id'=>'asc'));

                if (is_object($objInfoTareaCaracteristica))
                {
                    $objDetalleSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                       ->find($objInfoTareaCaracteristica->getValor());

                    if (is_object($objDetalleSolicitud))
                    {
                        $strTipoSolicitud = $objDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud();

                        switch($strTipoSolicitud)
                        {
                            case 'SOLICITUD RETIRO EQUIPO':
                                $strTipoTarea   = $strTipoTareaRetiro;
                                break;
                            case 'SOLICITUD CAMBIO EQUIPO':
                                $strTipoTarea   = $strTipoTareaCambio;
                                break;
                            case 'SOLICITUD PLANIFICACION':
                                $strTipoTarea   = $strTipoTareaInstalacion;
                                break;
                            default:
                                $strTipoTarea   = '';
                                break;
                        }
                    }
                }
            }

            if(empty($strTipoTarea))
            {
                $strMensajeRespuesta = "No se encontró ninguna solicitud para trabajos en el nodo"
                . " asociada a la tarea."; 
                
                throw new \Exception("ERROR_PARCIAL"); 
            }

            $strStatus      = $this->status['OK'];
            $strMensaje     = $this->mensaje['OK'];
        } 
        catch(\Exception $exception)
        {

            if($exception->getMessage() == "NULL")
            {
                $strStatus   = $this->status['NULL'];
                $strMensaje  = $this->mensaje['NULL'];
            }
            else if($exception->getMessage() == "ERROR_PARCIAL")
            {
                $strStatus   = $this->status['ERROR_PARCIAL'];
                $strMensaje  = $strMensajeRespuesta;
            }
            else
            {
                $strStatus   = $this->status['ERROR'];
                $strMensaje  = $this->mensaje['ERROR'];
            }
            
            $strTipoTarea = '';

            $serviceUtil->insertLog(array(
                'enterpriseCode'   => '10',
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'getMotivoIngresoAlNodo',
                'descriptionError' => $strMensaje,
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $strUsrCreacion));
        }

        $arrayRespuesta['status']       =   $strStatus;
        $arrayRespuesta['mensaje']      =   $strMensaje;
        $arrayRespuesta['tipoTarea']    =   $strTipoTarea;

        return $arrayRespuesta;
    }

    /**
     * Método que permite saber los equipos a retirar en el nodo
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 01-06-2021
     *
     * @param  Array $arrayData
     * @return Array $arrayRespuesta
     */
    private function getEquiposParaRetirarNodo($arrayData)
    {
        $arrayRespuesta             = array();
        $serviceUtil                = $this->get('schema.Util');
        $emComercial                = $this->getDoctrine()->getManager("telconet");
        $emInfraestructura          = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emSoporte                  = $this->getDoctrine()->getManager("telconet_soporte");
        $serviceElemento            = $this->get('tecnico.InfoElemento');
        $strUsrCreacion             = $arrayData['user'];
        $strMensajeRespuesta        = '';
        $intNumeroTarea             = $arrayData['data']['numeroTarea'];
        $arrayEquipos               = array();

        try
        {

            $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(array ('descripcionCaracteristica' => 'SOLICITUD NODO',
                                'estado'                    => 'Activo'));

            if (is_object($objAdmiCaracteristica))
            {
                $arrayObjInfoTareaCaracteristica = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                        ->findBy( array('tareaId'          => $intNumeroTarea,
                                           'caracteristicaId' => $objAdmiCaracteristica->getId(),
                                           'estado'           => 'Activo'));

                foreach ($arrayObjInfoTareaCaracteristica as $objInfoTareaCaracteristica) 
                {
                    if (is_object($objInfoTareaCaracteristica))
                    {
                        $objDetalleSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                           ->find($objInfoTareaCaracteristica->getValor());
    
                        if (is_object($objDetalleSolicitud))
                        {

                            $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                            ->findOneBy(array ('descripcionCaracteristica' => 'ELEMENTO NODO',
                                                'estado'                    => 'Activo'));

                            $arrayParametrosObtenerDispositivosNodo = array(
                                'intIdDetalleSolicitud' => $objDetalleSolicitud->getId(),
                                'intIdCaracteristica' => $objAdmiCaracteristica->getId(),
                                'strEstado' => array('AsignadoTarea'));

                            $objElemento = $serviceElemento->obtenerDispositivoRetiroNodoPorCaracteristica($arrayParametrosObtenerDispositivosNodo);

                            $arrayEquipos[] = array(
                            'idSolicitud'           => $objDetalleSolicitud->getId(),
                            'idElemento'            => $objElemento->getId(),
                            'tipoElemento'          => $objElemento->getModeloElementoId()->getTipoElementoId()
                                                        ->getNombreTipoElemento(),
                            'serieElemento'         => $objElemento->getSerieFisica(),
                            'modeloElemento'        => $objElemento->getModeloElementoId()->getNombreModeloElemento()
                                                ); 
                        }
                    }
                }
            }

            $strStatus      = $this->status['OK'];
            $strMensaje     = $this->mensaje['OK'];
        } 
        catch(\Exception $exception)
        {

            if($exception->getMessage() == "NULL")
            {
                $strStatus   = $this->status['NULL'];
                $strMensaje  = $this->mensaje['NULL'];
            }
            else if($exception->getMessage() == "ERROR_PARCIAL")
            {
                $strStatus   = $this->status['ERROR_PARCIAL'];
                $strMensaje  = $strMensajeRespuesta;
            }
            else
            {
                $strStatus   = $this->status['ERROR'];
                $strMensaje  = $this->mensaje['ERROR'];
            }
            

            $serviceUtil->insertLog(array(
                'enterpriseCode'   => '10',
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => 'SoporteWSController',
                'appMethod'        => 'getEquiposParaRetirarNodo',
                'descriptionError' => $strMensaje,
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayData),
                'creationUser'     => $strUsrCreacion));
        }

        $arrayRespuesta['status']   =   $strStatus;
        $arrayRespuesta['mensaje']  =   $strMensaje;
        $arrayRespuesta['result']   =   $arrayEquipos;

        return $arrayRespuesta;
    }

    /**
    * Función que sirve para guradar registros de casos creados de manera automatica 
    * para posterior enviar notificacion Push.
    *
    * @param array $arrayData
    * @return array $arrayResultado
    */
    private function putNotificarPush($arrayData)
    {
       $arrayResultado = array();
       $arrayParametro = array();
       try
       {
           $arrayParametro = array(                                   
                                   'intCasoId'      => $arrayData['data']['idCaso'],
                                   'strCodEmpresa'  => $arrayData['data']['codEmpresa'],
                                   'strTipoProceso' => $arrayData['data']['strTipoProceso'],
                                   'strUserSession' => $arrayData['user'],
                                   'strIpCreacion'     => $arrayData['ipCreacion']
                                   );
           $serviceSoporte = $this->get('soporte.SoporteService');
           $serviceSoporte->guardaNotificacionPush($arrayParametro);
           $arrayResultado['status']='ok';
           $arrayResultado['mensaje']='Transaccion exitosa';

       }
       catch(\Exception $e)
       {
           if($e->getMessage() == "NULL")
           {
               $arrayResultado['status']    = $this->status['NULL'];
               $arrayResultado['mensaje']   = $this->mensaje['NULL'];
           }
           else
           {
               $arrayResultado['status']    = $this->status['ERROR'];
               $arrayResultado['mensaje']   = $this->mensaje['ERROR'];
           }
       }
       return $arrayResultado;
    }
}
