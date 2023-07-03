<?php

namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\AdmiIntervalo;
use telconet\administracionBundle\Service\InfoCoordinadorTurnoService;

class PlanificacionHalController extends Controller implements TokenAuthenticatedController
{
    /**
     * 
     * Metodo encargado de redireccionar a la pantalla donde se gestiona la planificacion de HAL
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 03-04-2018
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 - 20-06-2018 - Se agrega el order by al array de zonas.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 - 17-08-2018 - Se valida si el usuario en sesión tiene el perfil para visualizar la planificación general de hal.
     *
     * @Secure(roles="ROLE_408-1")
    */
    public function indexAction()
    {
        $objRequest  = $this->get('request');
        $objSession  = $objRequest->getSession();
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');
        $objItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("408", "1");
        $boolExiste  = false;
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;

        if (true === $this->get('security.context')->isGranted('ROLE_408-5958')){
             $boolExiste = true;
        }

        //Obtener las cuadrillas ligadas a un coordinador
        $arrayParametros = array(
                                    'intCoordinadorPrincipal' => ($boolExiste ? null : $intIdPersonEmpresaRol),
                                    'criterios'               => array( 'nombre' => '', 
                                                                        'estado' => 'Activo' , 
                                                                        'esHal'  => 'S'
                                                                      )
                                );

        $arrayResultados = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')->getCuadrillasByCriterios($arrayParametros); 
        
        $arrayCuadrillas = array();
        $arrayZona       = array();
        
        foreach($arrayResultados['registros'] as $objCuadrilla)
        {
            $arrayCuadrillas[] = array('id'     => $objCuadrilla->getId(),
                                       'nombre' => strtoupper($objCuadrilla->getNombreCuadrilla()));
        }
        
        $arrayResultadoZona = $emComercial->getRepository('schemaBundle:AdmiZona')
            ->findBy(array('estado'     => 'Activo'),
                     array('nombreZona' => 'desc'));
        
        foreach($arrayResultadoZona as $objZona)
        {
            $arrayZona[] = array('id'     => $objZona->getId(),
                                 'nombre' => strtoupper($objZona->getNombreZona()));
        }
                
        $intIntervalo = 0;
        
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('PLANIFICACION_SOPORTE_HAL','SOPORTE','','GRANULARIDAD_INTERVALO','','','','','','');
        //Parametro de intervalo
       
        if(!empty($arrayAdmiParametroDet))
        {
            $intIntervalo = $arrayAdmiParametroDet['valor1'];
        }
        
        return $this->render('planificacionBundle:PlanificacionHal:index.html.twig', array(
             'arrayCuadrillas' => $arrayCuadrillas,
             'arrayZonas'      => $arrayZona,
             'item'            => $objItemMenu,
             'intervalo'       => intval($intIntervalo)
        ));
    }
        
    /**
     * 
     * Metodo encargado de crear los intervalos requeridos por los usuarios para la Planfificacion de HAL
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 03-04-2018
     *
     * @author Modificado: Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 10-04-2018 - Se agrega una nueva funcionalidad de notificaciones para HAL.
     *
     * @return JsonResponse
     */
    public function ajaxCrearIntervaloAction()
    {
        $objRequest     = $this->get('request');
        $strHoraInicio  = $objRequest->get('horaInicio');
        $strHoraFin     = $objRequest->get('horaFin');
        $objSession     = $objRequest->getSession();
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceUtil    = $this->get('schema.Util');
        $strStatus      = 'OK';
        $boolContinua   = true;
        $serviceSoporte = $this->get('soporte.SoporteService');
        $emSoporte->getConnection()->beginTransaction();

        try
        {
            $objFechaInicio   = new \DateTime('01/01/2018');
            $objFechaFin      = new \DateTime('01/01/2018');
            $arrayFechaInicio = explode(":", $strHoraInicio);
            $arrayFechaFin    = explode(":", $strHoraFin);

            $objFechaInicio->setTime($arrayFechaInicio[0], $arrayFechaInicio[1]);
            $objFechaFin->setTime($arrayFechaFin[0], $arrayFechaFin[1]);

            $arrayIntervalo   = $emSoporte->getRepository("schemaBundle:AdmiIntervalo")->findBy(array('estado' => 'Activo'));

            foreach ($arrayIntervalo as $objIntervalo)
            {
                $strHoraInicioEx = date_format($objIntervalo->getHoraIni(), 'H:i');
                $strHoraFinEx    = date_format($objIntervalo->getHoraFin(), 'H:i');

                $arrayFechaInicioExistente = explode(":", $strHoraInicioEx);
                $arrayFechaFinExistente    = explode(":", $strHoraFinEx);

                if ($arrayFechaInicioExistente[0] == $arrayFechaInicio[0] && $arrayFechaFinExistente[0] == $arrayFechaFin[0] &&
                    $arrayFechaInicioExistente[1] == $arrayFechaInicio[1] && $arrayFechaFinExistente[1] == $arrayFechaFin[1])
                {
                    $boolContinua = false;
                    break;
                }
            }

            if($boolContinua)
            {
                $objAdmiIntervalo = new AdmiIntervalo();
                $objAdmiIntervalo->setEstado("Activo");
                $objAdmiIntervalo->setFeCreacion(new \DateTime('now'));
                $objAdmiIntervalo->setHoraIni($objFechaInicio);
                $objAdmiIntervalo->setHoraFin($objFechaFin);
                $objAdmiIntervalo->setUsrCreacion($objSession->get('user'));
                $objAdmiIntervalo->setIpCreacion($objRequest->getClientIp());
                $emSoporte->persist($objAdmiIntervalo);
                $emSoporte->flush();

                $emSoporte->commit();

                /*========================= INICIO NOTIFICACION HAL ==========================*/
                $serviceSoporte->notificacionesHal(
                                array ('strModulo' =>  'intervaloAdmi',
                                       'strUser'   =>  $objSession->get('user'),
                                       'strIp'     =>  $objRequest->getClientIp(),
                                       'arrayJson' =>  array ('metodo' => 'nueva',
                                                              'id'     =>  $objAdmiIntervalo->getId())));
                /*=========================== FIN NOTIFICACION HAL ===========================*/

                //Mensaje de respuesta
                $strMensaje = "Intervalo creado correctamente";
            }
            else
            {
                $strStatus  = 'ERROR';
                $strMensaje = "Intervalo escogido ya existe creado, por favor intente con uno distinto";
            }
        } 
        catch (\Exception $ex) 
        {
            if ($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->rollback();
            }
            
            $serviceUtil->insertError('Telcos+', 
                                      'ajaxCrearIntervaloAction', 
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al guardar nuevo Intervalo, Notificar a Sistemas';
                        
            $emSoporte->close();            
        }
        
        $objResponse    = new JsonResponse();
        
        $objResponse->setData(array('status' => $strStatus , 'mensaje' => $strMensaje));
        
        return $objResponse;
    }
    
    /**
     * 
     * Metodo encargado de consultar los intervalos requeridos por los usuarios para la Planfificacion de HAL
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 03-04-2018
     * 
     * @return JsonResponse
     */
    public function ajaxConsultarIntervalosAction()
    {
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $arrayIntervalo = $emSoporte->getRepository("schemaBundle:AdmiIntervalo")->findBy(array('estado'  => 'Activo'),
                                                                                          array('horaIni' => 'ASC'));
        
        $arrayIntervalosExistentes = array();
        
        foreach($arrayIntervalo as $objIntervalo)
        {
            $strHoraInicio = date_format($objIntervalo->getHoraIni(), 'H:i');
            $strHoraFin    = date_format($objIntervalo->getHoraFin(), 'H:i');
            
            $arrayIntervalosExistentes[] = array('id'         => $objIntervalo->getId(),
                                                 'horaInicio' => $strHoraInicio,
                                                 'horaFin'    => $strHoraFin);
        }
        
        $objResponse    = new JsonResponse();        
        $objResponse->setData($arrayIntervalosExistentes);
        return $objResponse;
    }        

    /**
     * 
     * Metodo encargado de guardar la planificacion de cuadrillas HAL
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 04-04-2018
     * 
     * @return JsonResponse
     */
    public function ajaxGuardarPlanificacionHALAction()
    {
        $objRequest            = $this->get('request');
        $strData               = $objRequest->get('data');
        $serviceUtil           = $this->get('schema.Util');
        $objSession            = $objRequest->getSession();
        $strEmpresaCod         = $objSession->get('idEmpresa');
        $strUsrCreacion        = $objSession->get('user');
        $strIpCreacion         = $objRequest->getClientIp();
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $emSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $serviceSoporte        = $this->get('soporte.SoporteService');
        $strStatus             = 'OK';
        $strErrores            = '';
        $strMensaje            = '';
        $arrayData             = json_decode($strData);
        $arrayLista            = array();
        $arrayIdCab            = array();

        foreach ($arrayData as $objJson)
        {
            //Convertir a TimeStamp
            $arrayFechaInicio = explode("-", $objJson->fechaInicio);
            $objFechaInicio   = date("Y/m/d", strtotime($arrayFechaInicio[0] . "-" . $arrayFechaInicio[1] . "-" . $arrayFechaInicio[2]));

            $arrayFechaFin    = explode("-", $objJson->fechaFin);
            $objFechaFin      = date("Y/m/d", strtotime($arrayFechaFin[0] . "-" . $arrayFechaFin[1] . "-" . $arrayFechaFin[2]));

            $arrayParametros = array(
                                        'intIdCuadrilla'  =>  $objJson->idCuadrilla,
                                        'intIdIntervalo'  =>  $objJson->idIntervalo,
                                        'objFechaDesde'   =>  $objFechaInicio,
                                        'objFechaHasta'   =>  $objFechaFin,
                                        'strEmpresaCod'   =>  $strEmpresaCod,
                                        'strAsignado'     =>  'N',
                                        'strUsrCreacion'  =>  $strUsrCreacion,
                                        'strIpCreacion'   =>  $strIpCreacion,
                                        'intIdPersonaRol' =>  $intIdPersonEmpresaRol,
                                        'strEsAutomatico' =>  'N',
                                        'intIdZona'       =>  $objJson->idZona,
                                        'strCodActividad' =>  $objJson->codActividad
                                      );

            $strRespuestaTrx = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")->crearPlanificacionHAL($arrayParametros);
            $arrayRespuesta  = (array) json_decode($strRespuestaTrx);

            //Si el paquete devuelve error se gestiona
            if (!empty($arrayRespuesta) && $arrayRespuesta['estado'] === 'fail')
            {
                $strMensaje .= '<br/>Se generó problema al Guardar la Planificación de :'.
                                '<br/><b>Cuadrilla</b> : '.$objJson->cuadrilla.
                                '<br/><b>Zona</b> : '.$objJson->zona.
                                '<br/><b>Intervalo</b> : '.$objJson->intervalo.
                                '<br/><b>Rango Trabajo</b> : '.$objJson->fechaInicio.' a '.$objJson->fechaFin.
                                '<br/><b>Actividad</b> : '.$objJson->codActividad
                                ;

                $strErrores .= '<br/>Error : ('.$objJson->cuadrilla.') '.$arrayRespuesta['mensaje'];
            }
            else
            {
                $arrayLista[] = $arrayRespuesta['idLista'];
            }
        }

        if (empty($strMensaje))
        {
            $strMensaje = 'Se generó la Planificación correctamente';
        }
        else
        {
            $strMensaje = 'Se generó la Planificación pero se obtuvieron las siguientes observaciones : '.$strMensaje;
            $strStatus  = 'ERROR';

            $serviceUtil->insertError('Telcos+', 
                                      'PlanificacionHalController->ajaxGuardarPlanificacionHALAction',
                                       $strErrores,
                                       $strUsrCreacion,
                                       $strIpCreacion);
        }

        try
        {
            // Preparamos los id creados para enviar a hal
            if (!empty($arrayLista) && count($arrayLista) > 0)
            {
                foreach($arrayLista as $arrayValue)
                {
                    foreach($arrayValue as $intValue)
                    {
                        $arrayIdCab[] = $intValue;
                    }
                }

                /*========================= INICIO NOTIFICACION HAL ==========================*/
                $serviceSoporte->notificacionesHal(
                            array ('strModulo' =>  'intervaloCab',
                                   'strUser'   =>  $objSession->get('user'),
                                   'strIp'     =>  $objRequest->getClientIp(),
                                   'arrayJson' =>  array ('metodo'  => 'nueva',
                                                          'idLista' => $arrayIdCab)));
                /*=========================== FIN NOTIFICACION HAL ===========================*/
            }
        }
        catch (\Exception $objException)
        {
            $serviceUtil->insertError('Telcos+',
                                      'PlanificacionHalController->ajaxGuardarPlanificacionHALAction',
                                       'Error en el envio de notificacion a Hal: '.$objException->getMessage(),
                                       $strUsrCreacion,
                                       $strIpCreacion);
        }

        $objResponse = new JsonResponse();

        $objResponse->setData(array('status' => $strStatus , 'mensaje' => $strMensaje));

        return $objResponse;
    }

    /**
     * 
     * Metodo encargado de realizar la consulta de las planificaciones HAL generadas 
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 05-04-2018
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 - 20-06-2018 - Se agrega el filtro por zona.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 - 13-08-2018 - Se agrega el filtro por fecha de trabajo inicio y fecha de trabajo fin y en caso que la fecha inicio
     *                             sea nula, se filtra por defecto desde 15 días atrás en adelante.
     *                           - Se valida si el usuario en sesión tiene el perfil para visualizar la planificación general de hal.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 - 20-09-2018 - Se valida el envío de la empresaCod cuando el coordinador tiene el perfil de planificacionGeneralHal.
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.4 - 16-01-2023 - Se elimina la verificación del perfil perfil  ‘planificación general de hal’,
     *                             ahora se retornan las cuadrillas correspondientes a la planificación HAL en base a:
     *                              1. Departamento
     *                              2. Oficina y Departamento
     *                             Se realizan las siguientes validaciones:
     *                              1. Los usuarios que correspondan únicamente al departamento de ‘Operaciones Urbanas’ 
     *                                  podrán obtener y visualizar en un principio únicamente las cuadrillas que le hayan 
     *                                  sido asignadas, pero si el mismo cuenta con un Turno Activo podrá consultar y visualizar 
     *                                  cuadrillas de todo el departamento.
     *                              2. Si el usuario corresponde al departamento de ‘GIS’, ‘Fiscalizacion’ podrá obtener 
     *                                  y visualizar todas las cuadrillas correspondientes al departamento.
     *                              3. Cualquier otro usuario que corresponda a otro departamento podrá obtener y  visualizar 
     *                                  todas las cuadrillas correspondientes a su oficina y departamento.
     * 
     * @author José Castillo <jmcastillo@telconet.ec>
     * @version 1.5 - 06-06-2023 - Se agregar el parametro de departamento para filtrar cuadrillas de OPU.
     * 
     * @return JsonResponse
     */
    public function ajaxConsultarPlanificacionAgendaHALAction()
    {
        $objRequest            = $this->get('request');
        $emSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $objSession            = $objRequest->getSession();
        $intIdCuadrilla        = $objRequest->get('idCuadrilla');
        $intIdZona             = $objRequest->get('idZona');
        $strFechaIni           = $objRequest->get('fechaIni');
        $strFechaFin           = $objRequest->get('fechaFin');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strEmpresaCod         = $objSession->get('idEmpresa');
        $strEsconsulta         = $objRequest->get('strEsConsulta') ? $objRequest->get('strEsConsulta') : 'NO';

        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $strEmpresaCod);
        
        $strNombreDepartamento  = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];
        $intDepartamentoId      = $arrayEmpleado['ID_DEPARTAMENTO'];
        $intOficinaId           = $arrayEmpleado['ID_OFICINA'];
        $strBuscarPor           = 'oficina';
        
        $arrayDefaultDepartamentos  = array('Operaciones Urbanas', 'Fiscalizacion', 'GIS');
        $boolCheckDepartamento      = in_array($strNombreDepartamento, $arrayDefaultDepartamentos);

        if ($strFechaIni != "" && !is_null($strFechaIni))
        {
            $arrayFechaIni = explode("T", $strFechaIni);
            $strFechaIni   = $arrayFechaIni[0];
        }
        else
        {
             $objDateNow  = new \DateTime('now');
             $objDateNow->modify('-15 day');
             $strFechaIni = date_format($objDateNow, 'Y-m-d');
        }

        if ($strFechaFin != "" && !is_null($strFechaFin))
        {
            $arrayFechaFin = explode("T", $strFechaFin);
            $strFechaFin   = $arrayFechaFin[0];
        }

        $arrayParametros                        = array();
        $arrayParametros['intIdCuadrilla']      = $intIdCuadrilla;
        $arrayParametros['intIdZona']           = $intIdZona;
        $arrayParametros['strFechaIni']         = $strFechaIni;
        $arrayParametros['strFechaFin']         = $strFechaFin;
        $arrayParametros['strBuscarPor']        = 'oficina';
        $arrayParametros['intOficinaId']        = $intOficinaId;
        $arrayParametros['intDepartamentoId']   = $intDepartamentoId;
        $arrayParametros['strNombreDepartamento']   = $strNombreDepartamento;

        if ($boolCheckDepartamento)
        {
            $arrayParametros['strBuscarPor']    = 'departamento';
            $arrayParametros['intIdPersonaRol'] = $intIdPersonEmpresaRol;
            $arrayParametros['strCodEmpresa']   =  $strEmpresaCod;

            $serviceAdministracion  = $this->get('administracion.InfoCoordinadorTurno');
            $boolCoordinadorTurno   = $serviceAdministracion->isCoordinador($intIdPersonEmpresaRol);
            
            if ($strEsconsulta == 'SI' && $boolCoordinadorTurno)
            {
                $arrayParametros['intIdPersonaRol'] = null;
                $arrayParametros['strCodEmpresa']   =  null;
            }         
        }

        $arrayResultado = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")->getArrayPlanificacionAgendaHAL($arrayParametros);
        $arrayResultado = array_map("unserialize", array_unique(array_map("serialize", $arrayResultado)));
        $arrayRef       = array();
        $strColor       = '';

        //Se asigna un color distinto a cada cuadrilla planificada para visualizacion en el calendario
        foreach($arrayResultado as $arrayData)
        {            
            $boolRepetido = false;           
            
            foreach($arrayRef as $arrayR)
            {
                if($arrayR['idCuadrilla'] == $arrayData['idCuadrilla'])
                {
                    $boolRepetido = true;
                    break;
                }
            }
            
            if(!$boolRepetido)
            {
                $arrayRand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
                $strColor = '#'.$arrayRand[rand(0,15)].$arrayRand[rand(0,15)].$arrayRand[rand(0,15)]
                               .$arrayRand[rand(0,15)].$arrayRand[rand(0,15)].$arrayRand[rand(0,15)];
            }
            else
            {
                foreach($arrayRef as $arrayR)
                {
                    if($arrayR['idCuadrilla'] == $arrayData['idCuadrilla'])
                    {
                        $strColor = $arrayR['color'];
                        break;
                    }
                }
            }
            
            $arrayData['color'] = $strColor;
            $arrayRef[]         = $arrayData;
        }
        
        $objResponse    = new JsonResponse();
        
        $objResponse->setData($arrayRef);
        
        return $objResponse;
    }

    /**
     * Metodo encargado de realizar una consulta general de la planificacion generada ( HAL )
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 13-04-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 - 20-06-2018 - Se agrega el filtro por zona.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 - 13-08-2018 - Se agrega el filtro por fecha de trabajo inicio y fecha de trabajo fin y en caso que la fecha inicio
     *                             sea nula, se filtra por defecto desde 15 días atrás en adelante.
     *                           - Se valida si el usuario en sesión tiene el perfil para visualizar la planificación general de hal.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 - 20-09-2018 - Se valida el envío de la empresaCod cuando el coordinador tiene el perfil de planificacionGeneralHal.
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.4 - 16-01-2023 - Se elimina la verificación del perfil perfil  ‘planificación general de hal’,
     *                             ahora se retornan las cuadrillas correspondientes a la planificación HAL en base a:
     *                              1. Departamento
     *                              2. Oficina y Departamento
     *                             Se realizan las siguientes validaciones:
     *                              1. Los usuarios que correspondan únicamente al departamento de ‘Operaciones Urbanas’ 
     *                                  podrán obtener y visualizar en un principio únicamente las cuadrillas que le hayan 
     *                                  sido asignadas, pero si el mismo cuenta con un Turno Activo podrá consultar y visualizar 
     *                                  cuadrillas de todo el departamento.
     *                              2. Si el usuario corresponde al departamento de ‘GIS’, ‘Fiscalizacion’ podrá obtener 
     *                                  y visualizar todas las cuadrillas correspondientes al departamento.
     *                              3. Cualquier otro usuario que corresponda a otro departamento podrá obtener y  visualizar 
     *                                  todas las cuadrillas correspondientes a su oficina y departamento.
     * 
     * @author José Castillo <jmcastillo@telconet.ec>
     * @version 1.5 - 06-06-2023 - Se agregar el parametro de departamento para filtrar cuadrillas de OPU.
     * @return JsonResponse
     */
    public function ajaxConsultarPlanificacionGeneralHALAction()
    {
        $objRequest            = $this->get('request');
        $emSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $objSession            = $objRequest->getSession();
        $intIdCuadrilla        = $objRequest->get('idCuadrilla');
        $intIdZona             = $objRequest->get('idZona');
        $strFechaIni           = $objRequest->get('fechaIni');
        $strFechaFin           = $objRequest->get('fechaFin');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strEmpresaCod         = $objSession->get('idEmpresa');
        $boolExiste            = false;
        $strEsconsulta         = $objRequest->get('strEsConsulta') ? $objRequest->get('strEsConsulta') : 'NO';

        $emComercial  = $this->getDoctrine()->getManager('telconet');
        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $strEmpresaCod);
        
        $strNombreDepartamento  = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];
        $intDepartamentoId      = $arrayEmpleado['ID_DEPARTAMENTO'];
        $intOficinaId           = $arrayEmpleado['ID_OFICINA'];
        $strBuscarPor           = 'oficina';
        
        $arrayDefaultDepartamentos  = array('Operaciones Urbanas', 'Fiscalizacion', 'GIS');
        $boolCheckDepartamento      = in_array($strNombreDepartamento, $arrayDefaultDepartamentos);

        if ($strFechaIni != "" && !is_null($strFechaIni))
        {
            $arrayFechaIni = explode("T", $strFechaIni);
            $strFechaIni   = $arrayFechaIni[0];
        }
        else
        {
             $objDateNow  = new \DateTime('now');
             $objDateNow->modify('-15 day');
             $strFechaIni = date_format($objDateNow, 'Y-m-d');
        }

        if ($strFechaFin != "" && !is_null($strFechaFin))
        {
            $arrayFechaFin = explode("T", $strFechaFin);
            $strFechaFin   = $arrayFechaFin[0];
        }

        $arrayParametros                    = array();
        $arrayParametros['intIdCuadrilla']  = $intIdCuadrilla;
        $arrayParametros['intIdZona']       = $intIdZona;
        $arrayParametros['strFechaIni']     = $strFechaIni;
        $arrayParametros['strFechaFin']     = $strFechaFin;
        $arrayParametros['strBuscarPor']    = 'oficina';
        $arrayParametros['intOficinaId']        = $intOficinaId;
        $arrayParametros['intDepartamentoId']    = $intDepartamentoId;
        $arrayParametros['strNombreDepartamento']   = $strNombreDepartamento;

        if ($boolCheckDepartamento)
        {
            $arrayParametros['strBuscarPor']    = 'departamento';
            $arrayParametros['intIdPersonaRol'] = $intIdPersonEmpresaRol;
            $arrayParametros['strCodEmpresa']   =  $strEmpresaCod;

            $serviceAdministracion  = $this->get('administracion.InfoCoordinadorTurno');
            $boolCoordinadorTurno   = $serviceAdministracion->isCoordinador($intIdPersonEmpresaRol);
            
            if ($strEsconsulta == 'SI' && $boolCoordinadorTurno)
            {
                $arrayParametros['intIdPersonaRol'] = null;
                $arrayParametros['strCodEmpresa']   =  null;
            }         
        }

        $arrayResultado = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")->getArrayPlanificacionGeneralHAL($arrayParametros);

        $objResponse    = new JsonResponse();

        $objResponse->setData($arrayResultado);

        return $objResponse;
    }
    
    /**
     * Metodo encargado de cargar las cuadrillas de un coordinador ( HAL )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 12-04-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 - 17-08-208 - Se valida si el usuario en sesión tiene el perfil para visualizar la planificación general de hal.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 - 10-09-208 - Se agrega en el $arrayParametros el filtro por nombre de cuadrilla.
     * 
     * @author Modificado: Andrés Montero <amontero@telconet.ec>
     * @version 1.3 - 28-11-2019 - Se agrega consulta por preferencia para las cuadrillas y se agrega retornar 
     * preferencia para las cuadrillas consultadas.
     * 
     * @author Modificado: David De La Cruz <ddelacruz@telconet.ec>
     * @version 1.4 - 19-17-2021 - Se agrega petición para consultar las cuadrillas con estado Prestado, con el objetivo de 
     * que el Coordinador temporal planifique las cuadrillas prestadas
     * 
     * @author Modificado: Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.5 - 28-01-2022 - Se agrega petición para consultar las cuadrillas con estado Prestado
     *
     * @author Daniel Guzmán <ddguzman@telconet.ec>
     * @version 1.6 - 16-01-2023 - Se elimina la verificación del perfil perfil  ‘planificación general de hal’,
     *                             ahora se retornan las cuadrillas correspondientes a la planificación HAL en base a:
     *                              1. Departamento
     *                              2. Oficina y Departamento
     *                             Se realizan las siguientes validaciones:
     *                              1. Si el usuario cuenta con Turno Activo y corresponde al departamento de ‘Operaciones  Urbanas’ 
     *                                  obtendrá las cuadrillas correspondientes al departamento, caso contrario obtendrá únicamente
     *                                  las cuadrillas se le hayan asignado.
     *                              2. Si el usuario corresponde al departamento de ‘GIS’, ‘Fiscalizacion’ obtendrá todas 
     *                                  las cuadrillas correspondientes al departamento.
     *                              3. Cualquier otro departamento obtendrá todas las cuadrillas correspondientes a su oficina 
     *                                  y departamento.
     * 
     * @author José Castillo <jmcastillo@telconet.ec>
     * @version 1.7 - 06-06-2023 - Se agregar el parametro de departamento para filtrar cuadrillas de OPU.
     * 
     * @return JsonResponse
     */
    public function ajaxGetCuadrillasPlanificacionHalAction()  	
    { 	
        $objRequest   = $this->get('request');
        $objSession   = $objRequest->getSession();
        $emComercial  = $this->getDoctrine()->getManager('telconet');
        $boolExiste   = false;
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $strNombreCuadrilla    = $objRequest->query->get('query') ? strtoupper($objRequest->query->get('query')) : '';
        $strPreferencia        = $objRequest->query->get('preferencia') ? strtoupper($objRequest->query->get('preferencia')) : '';

        $strUsuarioRequest  = $objSession->get('user') ? $objSession->get('user') : '';
        $strCodEmpresa      = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $arrayEmpleado      = $emComercial->getRepository('schemaBundle:InfoPersona')
                                            ->getPersonaDepartamentoPorUserEmpresa($strUsuarioRequest, $strCodEmpresa);
        
        $strNombreDepartamento  = $arrayEmpleado['NOMBRE_DEPARTAMENTO'];
        $intDepartamentoId      = $arrayEmpleado['ID_DEPARTAMENTO'];
        $intOficinaId           = $arrayEmpleado['ID_OFICINA'];
        $strBuscarPor           = 'oficina';
        

        $arrayDefaultDepartamentos  = array('Operaciones Urbanas', 'Fiscalizacion', 'GIS');
        $boolCheckDepartamento      = in_array($strNombreDepartamento, $arrayDefaultDepartamentos);

        $arrayParametros    = array(
                                    'criterios' => array( 
                                                'nombre' => $strNombreCuadrilla,
                                                'estado' => 'multiple',
                                                'esHal'  => 'S',
                                                'preferencia' => $strPreferencia,
                                                'strBuscarPor' => $strBuscarPor,
                                                'intOficinaId' => $intOficinaId,
                                                'intDepartamentoId' => $intDepartamentoId,
                                                'strNombreDepartamento' => $strNombreDepartamento
                                            )
                            );

        if ($boolCheckDepartamento)
        {
            $arrayParametros['criterios']['strBuscarPor']    = 'departamento';

            $serviceAdministracion  = $this->get('administracion.InfoCoordinadorTurno');
            $boolCoordinadorTurno   = $serviceAdministracion->isCoordinador($intIdPersonEmpresaRol);
            
            if ($strNombreDepartamento == 'Operaciones Urbanas' && !$boolCoordinadorTurno)
            {
                $arrayParametros['intCoordinadorPrincipal'] = $intIdPersonEmpresaRol;
            }                
        }

        $arrayResultados    = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                            ->getCuadrillasByCriterios($arrayParametros);
        $arrayRegistros     = $arrayResultados['registros'];

        foreach($arrayRegistros as $objCuadrilla)
        {
            $strPreferenciaCuadrilla = '';

            if ( $objCuadrilla->getPreferencia() !== null )
            {
                $arrayAdmiParametrosDet = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('PREFERENCIAS_CUADRILLAS_HAL', 
                        'PLANIFICACION', 
                        '',
                        '', 
                        $objCuadrilla->getPreferencia(),
                        '', 
                        '', 
                        '', 
                        '', 
                        $objSession->get('idEmpresa'));

                $strPreferenciaCuadrilla = $arrayAdmiParametrosDet['valor2'];
            }


            $arrayCuadrillas[] = array('id'          => $objCuadrilla->getId(),
                                       'nombre'      => $objCuadrilla->getNombreCuadrilla(),
                                       'preferencia' => $strPreferenciaCuadrilla);
        }

        $objResponse    = new JsonResponse();
        
        $objResponse->setData($arrayCuadrillas);
        
        return $objResponse;	
    } 	

    /**
     * Metodo encargado de retornar las preferencias para las cuadrillas de HAL
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 - 27-11-2019
     *
     * @return JsonResponse
     */
    public function ajaxGetPreferenciasCuadrillasHalAction()  	
    { 	
        $objRequest   = $this->get('request');
        $objSession   = $objRequest->getSession();
        $emGeneral    = $this->getDoctrine()->getManager('telconet');

        $arrayAdmiParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->get( 'PREFERENCIAS_CUADRILLAS_HAL', 
                  'PLANIFICACION', 
                  '', 
                  '', 
                  '',
                  '', 
                  '', 
                  '', 
                  '', 
                  $objSession->get('idEmpresa'));
        if ($objRequest->get('consulta') !== 'list_cuadrillas')
        {
            $arrayPreferencias['encontrados'][] = array('valor'       => '',
                                                        'descripcion' => 'Todos'
                                                    );
        }
        if($arrayAdmiParametrosDet && count($arrayAdmiParametrosDet) > 0)
        {
            foreach($arrayAdmiParametrosDet as $arrayPreferencia)
            {
                $arrayPreferencias['encontrados'][] = array('valor'     => $arrayPreferencia['valor1'],
                                                          'descripcion' => $arrayPreferencia['valor2']
                );
            }
        }
        $objResponse    = new JsonResponse();
        $objResponse->setData($arrayPreferencias);
        return $objResponse;	
    }

    /**
     * Metodo encargado de cargar el detalle de horas de trabajo por dia de planificacion HAL
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 13-04-2018
     * 
     * @return JsonResponse
     */
    public function ajaxGetDetallePlanificacionDiariaAction()
    {
        $objRequest    = $this->get('request');
        $intIdCabecera = $objRequest->get('idCabecera');
        $emSoporte     = $this->getDoctrine()->getManager('telconet_soporte');
        
        $arrayDetalles = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")->getArrayDetallePlanificacionDiaria($intIdCabecera);
        
        $objResponse   = new JsonResponse();
        
        $objResponse->setData($arrayDetalles);
        
        return $objResponse;
    }
    
    /**
     * 
     * Metodo encargado de liberar dias u horas de trabajo o eliminar planificaciones completas en caso de ser necesario
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 13-04-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 24-08-2018 - Se agrega un nuevo else if, para considerar la reactivación de una fecha de trabajo y notificar a Hal.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 12-09-2018 - Se agrega el usuario y fecha de modificación al momento de actualizar el detalle y/o la cabecera
     *                           de la planificación.
     *
     * @return JsonResponse
     */
    public function ajaxEliminarLiberarPlanificacionHalAction()
    {
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $strAccion      = $objRequest->get('accion');
        $intIdCuadrilla = $objRequest->get('idCuadrilla')?$objRequest->get('idCuadrilla'):'';
        $intIdZona      = $objRequest->get('idZona')?$objRequest->get('idZona'):'';
        $intIdIntervalo = $objRequest->get('idIntervalo')?$objRequest->get('idIntervalo'):'';
        $intIdCabecera  = $objRequest->get('idCabecera')?$objRequest->get('idCabecera'):'';
        $strDetalles    = $objRequest->get('detalles')?$objRequest->get('detalles'):'';
        $serviceUtil    = $this->get('schema.Util');
        $strStatus      = 'OK';
        $strMensaje     = '';
        $boolContinua   = true;
        $emSoporte      = $this->getDoctrine()->getManager('telconet_soporte');
        $arrayIdLIsta   = array();
        $arrayActivar   = array();
        $strModulo      = "";
        $serviceSoporte = $this->get('soporte.SoporteService');

        $emSoporte->getConnection()->beginTransaction();

        try
        {
            if ($strAccion == 'eliminarPlanificacion')
            {
                $arrayCabecera = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")
                                           ->findBy(array ('cuadrillaId' => $intIdCuadrilla,
                                                           'zonaId'      => $intIdZona,
                                                           'intervaloId' => $intIdIntervalo,
                                                           'estado'      => array('Activo','Liberado')));
                //Eliminar Detalles
                foreach ($arrayCabecera as $objCabecera)
                {
                    $arrayDetalles = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifDet")
                                               ->findByCuadrillaPlanifCabId($objCabecera->getId());

                    foreach ($arrayDetalles as $objDetalle)
                    {
                        $objDetalle->setEstado('Eliminado');
                        $objDetalle->setUsrModificacion($objSession->get('user'));
                        $objDetalle->setFeModificacion(new \DateTime('now'));
                        $emSoporte->persist($objDetalle);
                        $emSoporte->flush();
                    }

                    $objCabecera->setEstado('Eliminado');
                    $objCabecera->setUsrModificacion($objSession->get('user'));
                    $objCabecera->setFeModificacion(new \DateTime('now'));
                    $emSoporte->persist($objCabecera);
                    $emSoporte->flush();

                    $arrayIdLIsta[] = $objCabecera->getId();
                }
 
                $strModulo    = 'intervaloCab';
                $strMensaje   = 'Se Eliminó la Planificación correctamente';
            }
            else if ($strAccion == 'activarDiaTrabajo')
            {
                $objInfoCuadrillaPlanifCab = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")
                    ->find($intIdCabecera);

                if (is_object($objInfoCuadrillaPlanifCab))
                {
                    $arrayInfoCuadrillaPlanifCab = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")
                        ->findBy(array ('feTrabajo'   => $objInfoCuadrillaPlanifCab->getFeTrabajo(),
                                        'zonaId'      => $objInfoCuadrillaPlanifCab->getZonaId(),
                                        'intervaloId' => $objInfoCuadrillaPlanifCab->getIntervaloId(),
                                        'cuadrillaId' => $objInfoCuadrillaPlanifCab->getCuadrillaId(),
                                        'estado'      => 'Activo'));

                    if (!empty($arrayInfoCuadrillaPlanifCab) && count($arrayInfoCuadrillaPlanifCab) > 0)
                    {
                        $strStatus    = 'fail';
                        $strMensaje   = 'Ya se tiene una planificación Activa para la fecha de trabajo seleccionada..';
                    }
                    else
                    {
                        $objAdmiIntervalo = $emSoporte->getRepository("schemaBundle:AdmiIntervalo")
                                    ->find($objInfoCuadrillaPlanifCab->getIntervaloId());

                        $arrayResultadoService = $serviceSoporte->validarFechasPlanificadasHal(
                            array ('intIdCuadrilla' => $objInfoCuadrillaPlanifCab->getCuadrillaId(),
                                   'intIdIntervalo' => $objInfoCuadrillaPlanifCab->getIntervaloId(),
                                   'strFechaInicio' => date_format($objInfoCuadrillaPlanifCab->getFeTrabajo(), 'Y-m-d'),
                                   'strFechaFin'    => date_format($objInfoCuadrillaPlanifCab->getFeTrabajo(), 'Y-m-d'),
                                   'strHoraInicio'  => date_format($objAdmiIntervalo->getHoraIni(), 'H:i'),
                                   'strHoraFin'     => date_format($objAdmiIntervalo->getHoraFin(), 'H:i'),
                                   'strUsuario'     => $objSession->get('user'),
                                   'strIp'          => $objRequest->getClientIp()));

                        if (!$arrayResultadoService['success'])
                        {
                            if ($arrayResultadoService['status'] === 'ok')
                            {
                                $strStatus    = 'fail';
                                $strMensaje   = 'Ya se tiene una planificación Activa para la fecha de trabajo seleccionada..';
                            }
                            else
                            {
                                $strStatus    = 'fail';
                                $strMensaje   = 'Error al validar, por favor comuniquese con sistemas si el problema persiste..';
                            }
                        }
                        else
                        {
                            //Procedemos con la Activación de la fecha de trabajo.
                            $arrayInfoCuadrillaPlanifDet = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifDet")
                                ->findByCuadrillaPlanifCabId($objInfoCuadrillaPlanifCab->getId());

                            foreach ($arrayInfoCuadrillaPlanifDet as $objDetalle)
                            {
                                if (is_object($objDetalle))
                                {
                                    $objDetalle->setEstado('Activo');
                                    $objDetalle->setUsrModificacion($objSession->get('user'));
                                    $objDetalle->setFeModificacion(new \DateTime('now'));
                                    $emSoporte->persist($objDetalle);
                                    $emSoporte->flush();
                                }
                            }

                            $strModulo = 'intervaloCab';
                            $objInfoCuadrillaPlanifCab->setEstado('Activo');
                            $objInfoCuadrillaPlanifCab->setUsrModificacion($objSession->get('user'));
                            $objInfoCuadrillaPlanifCab->setFeModificacion(new \DateTime('now'));
                            $emSoporte->persist($objInfoCuadrillaPlanifCab);
                            $emSoporte->flush();
                            $arrayActivar[] = $objInfoCuadrillaPlanifCab->getId();

                            //Verificamos si existen planificaciones en estado Liberado, para proceder a eliminarlas.
                            $arrayInfoCuadrillaPlanifCab = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")
                                ->findBy(array ('feTrabajo'   => $objInfoCuadrillaPlanifCab->getFeTrabajo(),
                                                'zonaId'      => $objInfoCuadrillaPlanifCab->getZonaId(),
                                                'intervaloId' => $objInfoCuadrillaPlanifCab->getIntervaloId(),
                                                'cuadrillaId' => $objInfoCuadrillaPlanifCab->getCuadrillaId(),
                                                'estado'      => 'Liberado'));

                            if (!empty($arrayInfoCuadrillaPlanifCab) && count($arrayInfoCuadrillaPlanifCab) > 0)
                            {
                                foreach($arrayInfoCuadrillaPlanifCab as $objCab)
                                {
                                    if (is_object($objCab))
                                    {
                                        $arrayInfoCuadrillaPlanifDet = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifDet")
                                            ->findByCuadrillaPlanifCabId($objCab->getId());

                                         foreach ($arrayInfoCuadrillaPlanifDet as $objDetalle)
                                         {
                                             if (is_object($objDetalle))
                                             {
                                                $objDetalle->setEstado('Eliminado');
                                                $objDetalle->setUsrModificacion($objSession->get('user'));
                                                $objDetalle->setFeModificacion(new \DateTime('now'));
                                                $emSoporte->persist($objDetalle);
                                                $emSoporte->flush();
                                             }
                                         }

                                         $objCab->setEstado('Eliminado');
                                         $objCab->setUsrModificacion($objSession->get('user'));
                                         $objCab->setFeModificacion(new \DateTime('now'));
                                         $emSoporte->persist($objCab);
                                         $emSoporte->flush();
                                    }
                                }
                            }

                            $strMensaje   = 'Se Activo el día de trabajo correctamente';

                        }
                    }
                }
            }
            else if ($strAccion == 'liberarDiaTrabajo')
            {
                $objCabecera = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")->find($intIdCabecera);
                
                if (is_object($objCabecera))
                {
                    $arrayDetalles = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifDet")
                                               ->findByCuadrillaPlanifCabId($intIdCabecera);

                    foreach ($arrayDetalles as $objDetalle)
                    {
                        $objDetalle->setEstado('Liberado');
                        $objDetalle->setUsrModificacion($objSession->get('user'));
                        $objDetalle->setFeModificacion(new \DateTime('now'));
                        $emSoporte->persist($objDetalle);
                        $emSoporte->flush();
                    }

                    $objCabecera->setEstado('Liberado');
                    $objCabecera->setUsrModificacion($objSession->get('user'));
                    $objCabecera->setFeModificacion(new \DateTime('now'));
                    $emSoporte->persist($objCabecera);
                    $emSoporte->flush();

                    $arrayIdLIsta[] = $objCabecera->getId();
                    $strModulo      = 'intervaloCab';
                    $strMensaje     = 'Se liberó el día de trabajo correctamente';
                }
                else
                {
                    $strStatus    = 'ERROR';
                    $strMensaje   = 'No existe referencia del día de trabajo a liberar';
                    $boolContinua = false;
                }
            }
            else//Liberar horas de trabajo
            {
                $arrayJson = json_decode($strDetalles);
                
                foreach ($arrayJson as $objJson)
                {
                    $objDetalle = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifDet")->find($objJson->idDetalle);
                    
                    if (is_object($objDetalle))
                    {
                        $objDetalle->setEstado('Liberado');
                        $objDetalle->setUsrModificacion($objSession->get('user'));
                        $objDetalle->setFeModificacion(new \DateTime('now'));
                        $emSoporte->persist($objDetalle);
                        $emSoporte->flush();
                    }

                    $arrayIdLIsta[] = $objJson->idDetalle;
                    $strMensaje     = 'Se liberaron las horas de trabajo correctamente';
                }

                $strModulo = 'intervaloDetalle';

                // Si no existe ni un detalle activo, se libera el dia de trabajo
                $arrayDetalles = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifDet")
                    ->findBy(array ('cuadrillaPlanifCabId' => $intIdCabecera,
                                    'estado'               => 'Activo'));

                if (empty($arrayDetalles))
                {
                    $objCabecera = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")->find($intIdCabecera);

                    if (is_object($objCabecera))
                    {
                        $objCabecera->setEstado('Liberado');
                        $objCabecera->setUsrModificacion($objSession->get('user'));
                        $objCabecera->setFeModificacion(new \DateTime('now'));
                        $emSoporte->persist($objCabecera);
                        $emSoporte->flush();
                    }

                    $arrayIdLIsta   =  array();
                    $strModulo      = 'intervaloCab';
                    $arrayIdLIsta[] =  $objCabecera->getId();
                }
            }

            if ($boolContinua)
            {
                $emSoporte->commit();

                if (!empty($arrayIdLIsta) && count($arrayIdLIsta) > 0)
                {
                    /*========================= INICIO NOTIFICACION HAL ==========================*/
                    $serviceSoporte->notificacionesHal(
                            array ('strModulo' =>  $strModulo,
                                   'strUser'   =>  $objSession->get('user'),
                                   'strIp'     =>  $objRequest->getClientIp(),
                                   'arrayJson' =>  array ('metodo'       => 'elimino',
                                                          'idReferencia' => intval($intIdCuadrilla),
                                                          'idLista'      => $arrayIdLIsta)));
                    /*=========================== FIN NOTIFICACION HAL ===========================*/
                }

                if (!empty($arrayActivar) && count($arrayActivar) > 0)
                {
                    /*========================= INICIO NOTIFICACION HAL ==========================*/
                    $serviceSoporte->notificacionesHal(
                                array ('strModulo' =>  $strModulo,
                                       'strUser'   =>  $objSession->get('user'),
                                       'strIp'     =>  $objRequest->getClientIp(),
                                       'arrayJson' =>  array ('metodo'  => 'nueva',
                                                              'idLista' => $arrayActivar)));
                    /*=========================== FIN NOTIFICACION HAL ===========================*/
                }
            }
        }
        catch (\Exception $ex)
        {
            if ($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->rollback();
            }

            $serviceUtil->insertError('Telcos+',
                                      'ajaxEliminarLiberarPlanificacionHalAction',
                                      $ex->getMessage(),
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                    );
            $strStatus  = 'ERROR';
            $strMensaje = 'Error al realizar la gestión sobre la Planificación Generada, Notificar a Sistemas';

            $emSoporte->close();
        }

        $objResponse   = new JsonResponse();

        $objResponse->setData( array('status' => $strStatus, 'mensaje' => $strMensaje));

        return $objResponse;
    }

    /**
     *
     * Metodo encargado de actualizar las horas de trabajo segun requiera el usuario
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 14-04-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 28-06-2018 - Se agrega el parametro tipo de proceso, para identificar si es Manual o Automatico
     *                           en caso del controlador es manual.
     *
     * @return JsonResponse
     */
    public function ajaxActualizarHorasTrabajoHalAction()
    {
        $objRequest            = $this->get('request');
        $intIdCabecera         = $objRequest->get('idCabecera');
        $strHoraInicio         = $objRequest->get('horaInicio');
        $strHoraFin            = $objRequest->get('horaFin');
        $strIntervalo          = $objRequest->get('intervalo');
        $intIdCuadrilla        = $objRequest->get('idCuadrilla');
        $strFecha              = $objRequest->get('fecha');
        $serviceUtil           = $this->get('schema.Util');
        $objSession            = $objRequest->getSession();
        $strUsrCreacion        = $objSession->get('user');
        $strIpCreacion         = $objRequest->getClientIp();
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $emSoporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $strStatus             = 'OK';
        $strRespuestaTrx       = '';
        $serviceSoporte        = $this->get('soporte.SoporteService');
        $arrayIntervalos       = explode(" - ", $strIntervalo);
        $objFechaTrabajo       = date("Y/m/d", strtotime($strFecha));

        // Parseo para las horas extendidas antes de la Jornada laborar
        $objHora1      = new \DateTime($strFecha.' '.$strHoraInicio);
        $objHoraInicio = $objHora1->format("Y/m/d H:i");
        $objHora2      = new \DateTime($strFecha.' '.$arrayIntervalos[0]);
        $objHoraFin    = $objHora2->format("Y/m/d H:i");

        //Enviar intervalo editado de inicio
        $arrayParametros = array ('intIdCabecera'      =>  $intIdCabecera,
                                  'objHoraInicio'      =>  $objHoraInicio,
                                  'objHoraFin'         =>  $objHoraFin,
                                  'objFechaRegistro'   =>  $objFechaTrabajo,
                                  'intIdPersonaRol'    =>  $intIdPersonEmpresaRol,
                                  'strUsrCreacion'     =>  $strUsrCreacion,
                                  'strIpCreacion'      =>  $strIpCreacion,
                                  'strTipoProceso'     => 'Manual');

        $strRespuestaTrx .= $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")->actualizarHorasTrabajoHAL($arrayParametros);

        // Parseo para las horas extendidas despues de la Jornada laborar
        $objHora3       = new \DateTime($strFecha.' '.$arrayIntervalos[1]);
        $objHoraInicio1 = $objHora3->format("Y/m/d H:i");
        $objHora4       = new \DateTime($strFecha.' '.$strHoraFin);
        $objHoraFin1    = $objHora4->format("Y/m/d H:i");

        //Enviar intervalo editado de fin
        $arrayParametros = array ('intIdCabecera'      =>  $intIdCabecera,
                                  'objHoraInicio'      =>  $objHoraInicio1,
                                  'objHoraFin'         =>  $objHoraFin1,
                                  'objFechaRegistro'   =>  $objFechaTrabajo,
                                  'intIdPersonaRol'    =>  $intIdPersonEmpresaRol,
                                  'strUsrCreacion'     =>  $strUsrCreacion,
                                  'strIpCreacion'      =>  $strIpCreacion,
                                  'strTipoProceso'     => 'Manual');

        $strRespuestaTrx .= $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")->actualizarHorasTrabajoHAL($arrayParametros);

        // Validamos la respuesta de la DB
        if (empty($strRespuestaTrx))
        {
            $strMensaje = 'Se Actualizó correctamente las horas de trabajo';

            /*========================= INICIO NOTIFICACION HAL ==========================*/
            $serviceSoporte->notificacionesHal(
                array ('strModulo' => 'actualizadetallehoras',
                       'strUser'   =>  $objSession->get('user'),
                       'strIp'     =>  $objRequest->getClientIp(),
                       'arrayJson' =>  array ('idCuadrilla'          => intval($intIdCuadrilla),
                                              'idCuadrillaPlanifCab' => intval($intIdCabecera),
                                              'feInicio'             => $objHora1->format("Y-m-d H:i:s"),
                                              'feFin'                => $objHora4->format("Y-m-d H:i:s"))));
            /*========================== FIN NOTIFICACION HAL ============================*/
        }
        else
        {
            $strMensaje = 'Fallo al actualizar las horas de trabajo, notificar a Sistemas';
            $strStatus  = 'ERROR';

            $serviceUtil->insertError('Telcos+', 
                                     'ajaxActualizarHorasTrabajoHalAction', 
                                      $strRespuestaTrx, 
                                      $strUsrCreacion, 
                                      $strIpCreacion);
        }

        $objResponse = new JsonResponse();

        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));

        return $objResponse;
    }

    /**
     * Metodo que se encarga de obtener la jornada de trabajo de una cuadrilla.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 - 24-04-2018
     *
     * @return JsonResponse
     */
    public function ajaxGetJornadaDeTrabajoAction()
    {
        $objResponse = new JsonResponse();
        $objRequest  = $this->get('request');
        $emSoporte   = $this->getDoctrine()->getManager('telconet_soporte');

        $arrayParametros['intIdCuadrillaPlanifCab'] = $objRequest->get('idCab');
        $arrayParametros['strEstadoCab']            = $objRequest->get('estadoCab');
        $arrayParametros['strEstadoDet']            = $objRequest->get('estadoDet');

        $arrayResultado = $emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")
            ->getJornadaDeTrabajo($arrayParametros);

        $objResponse->setData($arrayResultado);

        return $objResponse;
    }

    /**
     * Método que se encarga de validar si la fecha a crear ya se encuentra planificada.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 - 28-08-2018
     *
     * @return JsonResponse
     */
    public function ajaxValidarFechasPlanificadasAction()
    {
        $objResponse    = new JsonResponse();
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $intIdCuadrilla = $objRequest->get('idCuadrilla');
        $intIdIntervalo = $objRequest->get('idIntervalo');
        $strFechaInicio = $objRequest->get('fechaInicio');
        $strFechaFin    = $objRequest->get('fechaFin');
        $strHoraInicio  = $objRequest->get('horaInicio');
        $strHoraFin     = $objRequest->get('horaFin');
        $strUsuario     = $objSession->get('user');
        $strIp          = $objRequest->getClientIp();
        $serviceSoporte = $this->get('soporte.SoporteService');

        $arrayResultado = $serviceSoporte->validarFechasPlanificadasHal(array ('intIdCuadrilla' => $intIdCuadrilla,
                                                                               'intIdIntervalo' => $intIdIntervalo,
                                                                               'strFechaInicio' => $strFechaInicio,
                                                                               'strFechaFin'    => $strFechaFin,
                                                                               'strHoraInicio'  => $strHoraInicio,
                                                                               'strHoraFin'     => $strHoraFin,
                                                                               'strUsuario'     => $strUsuario,
                                                                               'strIp'          => $strIp));

        $objResponse->setData($arrayResultado);
        return $objResponse;
    }

    /**
     * Metodo encargado de cargar las cuadrillas de zonas de Reprogramación
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.0 - 27-11-2020
     *
     * @return JsonResponse
     */
    public function ajaxGetCuadrillasReprogramacionHalAction()  	
    {
        $objRequest          = $this->get('request');
        $emComercial         = $this->getDoctrine()->getManager('telconet');
        $strFecha            = $objRequest->query->get('fecha') ? strtoupper($objRequest->query->get('fecha')) : '';
        $strHoraInicio       = $objRequest->query->get('horaInicio') ? strtoupper($objRequest->query->get('horaInicio')) : '';
        $strHoraFin          = $objRequest->query->get('horaFin') ? strtoupper($objRequest->query->get('horaFin')) : '';

        //Obtenemos la zona que tenga nombre de zona "REPROGRAMACIONES"
        $arrayResultadoZona  = $emComercial->getRepository('schemaBundle:AdmiZona')->findByNombreZona('REPROGRAMACIONES'); 

        $arrayFecha          = explode(" ",$strFecha);

        $strFechaIni         = trim($arrayFecha[0])." ".trim($strHoraInicio);
        $strFechaFin         = trim($arrayFecha[0])." ".trim($strHoraFin);

        if ( isset($arrayResultadoZona) && !empty($arrayResultadoZona) && 
             isset($strFechaIni) && !empty($strFechaIni) && 
             isset($strFechaFin) && !empty($strFechaFin) )
        {

            $objZonaReprograma = $arrayResultadoZona[0];
            $intIdZona         = $objZonaReprograma->getId();

            $arrayParametros = array(
                'idZona'    => $intIdZona, 
                'fechaIni'  => trim($strFechaIni),
                'fechaFin'  => trim($strFechaFin)
            );
            $arrayResultados = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                           ->getCuadrillasPorZonaPlanificada($arrayParametros);
        }
        foreach($arrayResultados['resultado'] as $arrayCuadrilla)
        {

            $arrayCuadrillas[] = array(
                                       'id'      => $arrayCuadrilla['cuadrillaId'],
                                       'nombre'  => $arrayCuadrilla['nombreCuadrilla']
                                      );
        }

        $objResponse    = new JsonResponse();
        
        $objResponse->setData(array('encontrados'=>$arrayCuadrillas));
        
        return $objResponse;	
    }


    /**
     *
     * Metodo encargado de reprogramar planificación de cuadrilla y cambio de zonas en planificación
     *
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 27-10-2020
     *
     * @return JsonResponse
     */
    public function ajaxReprogramarPlanficacionAction()
    {
        $objRequest            = $this->get('request');
        $intIdCabecera         = $objRequest->get('idCabecera');
        $strTipoReprogramacion = $objRequest->get('tipo');
        $strOpcion             = $objRequest->get('opcion');
        $intIdCuadrilla        = $objRequest->get('idCuadrilla');
        $intIdNuevaCuadrilla   = $objRequest->get('idNuevaCuadrilla');
        $intIdZona             = $objRequest->get('idZona');
        $intIdNuevaZona        = $objRequest->get('idNuevaZona');
        $objServiceUtil        = $this->get('schema.Util');
        $objSession            = $objRequest->getSession();
        $strUsrCreacion        = $objSession->get('user');
        $strIpCreacion         = $objRequest->getClientIp();
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $objServiceSoporte        = $this->get('soporte.SoporteService');

        $arrayRespuesta        = array();
        $objResponse           = new JsonResponse();
        $strParametrosEnviados = "Parámetros recibidos=> tipo(".$strTipoReprogramacion.") idCabecera(".$intIdCabecera.
                                 ") idCuadrilla(".$intIdCuadrilla.") idNuevaCuadrilla(".$intIdNuevaCuadrilla.") idZona(".$intIdZona.
                                 ") idNuevaZona(".$intIdNuevaZona.")";

        /*========================= INICIO NOTIFICACION HAL ==========================*/
        if ($strTipoReprogramacion == 'CUADRILLA' && $strOpcion == "CUALQUIER_CUADRILLA" && 
            $intIdCuadrilla != null && $intIdCabecera != null && $strOpcion != null)
        {
            $arrayRespuesta = $objServiceSoporte->notificacionesHal(
                                array ('strModulo' => 'REPROGRAMARPLANIFICACION',
                                        'strUser'   =>  $strUsrCreacion,
                                        'strIp'     =>  $strIpCreacion,
                                        'arrayJson' =>  array (
                                                                'idCuadrilla'        => intval($intIdCuadrilla),
                                                                'idCabecera'         => intval($intIdCabecera),
                                                                'idCuadrillaNueva'   => null,
                                                                'cualquierCuadrilla' => true
                                                            )));
        }
        elseif ($strTipoReprogramacion == 'CUADRILLA' && $strOpcion == "A_CUADRILLA" && 
                $intIdCuadrilla != null && $intIdNuevaCuadrilla != null && $intIdCabecera != null && $strOpcion != null)
        {
            $arrayRespuesta = $objServiceSoporte->notificacionesHal(
                                array ('strModulo' => 'REPROGRAMARPLANIFICACION',
                                        'strUser'   =>  $strUsrCreacion,
                                        'strIp'     =>  $strIpCreacion,
                                        'arrayJson' =>  array (
                                                                'idCuadrilla'        => intval($intIdCuadrilla),
                                                                'idCabecera'         => intval($intIdCabecera),
                                                                'idCuadrillaNueva'   => intval($intIdNuevaCuadrilla),
                                                                'cualquierCuadrilla' => false
                                                            )));
        }
        elseif($strTipoReprogramacion == 'ZONA' && $intIdZona != null && 
               $intIdNuevaZona != null && $intIdCabecera != null && $intIdCuadrilla != null)
        {
            $arrayRespuesta =  $objServiceSoporte->notificacionesHal(
                                array ('strModulo' => 'CAMBIARZONAPLANIFICACION',
                                        'strUser'   =>  $strUsrCreacion,
                                        'strIp'     =>  $strIpCreacion,
                                        'arrayJson' =>  array (
                                                                'idCuadrilla' => intval($intIdCuadrilla),
                                                                'idCabecera'  => intval($intIdCabecera),
                                                                'idZonaNueva' => intval($intIdNuevaZona)
                                                            )));
        }
        else
        {
            $strMensaje = 'No se puede procesar, faltan parámetros por enviar.';
            $strStatus  = 'Error';

            $objServiceUtil->insertError('Telcos+', 
                                         'ajaxReprogramarPlanficacionAction', 
                                         $strMensaje+$strParametrosEnviados, 
                                         $strUsrCreacion, 
                                         $strIpCreacion);
            
            $objResponse->setData(array('status'=>$strStatus,'mensaje'=>$strMensaje));
            return $objResponse;

        }
        /*========================== FIN NOTIFICACION HAL ============================*/
        // Validamos la respuesta de la DB
        if ( $arrayRespuesta['result']['status'] == '200' || $arrayRespuesta['result']['status'] == 200 || 
             strtoupper($arrayRespuesta['result']['respuesta']) == 'OK' )
        {
            $strMensaje = 'Se ejecutó correctamente el proceso';
            $strStatus  = 'Ok';
        }
        else
        {
            $strStatus   = 'Error';
            $strMensaje  = isset($arrayRespuesta['result']['descripcion'])?$arrayRespuesta['result']['descripcion']:"";

            $objServiceUtil->insertError('Telcos+', 
                                         'ajaxReprogramarPlanficacionAction', 
                                         "Se produjo un error al ejecutar webservice. Mensaje de webservice => ".$strMensaje,
                                         $strUsrCreacion, 
                                         $strIpCreacion);
        }

        $objResponse->setData(array('status'=>$strStatus,'mensaje'=>$strMensaje));
        return $objResponse;
    }


    /**
     * 
     * Metodo encargado de liberar eventos (alimentacion, finJornada) de una cuadrilla
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 15-10-2020
     *
     *
     * @return JsonResponse
     */
    public function ajaxLiberarPermisoEventoAction()
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strOpcion              = $objRequest->get('opcion')?$objRequest->get('opcion'):'';
        $intIdCuadrilla         = $objRequest->get('idCuadrilla')?$objRequest->get('idCuadrilla'):'';
        $intIdCabecera          = $objRequest->get('idCabecera')?$objRequest->get('idCabecera'):'';
        $intIdPersonaEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $serviceUtil            = $this->get('schema.Util');
        $strStatus              = 'ERROR';
        $strMensaje             = 'Permiso no habilitado';
        $serviceSoporte         = $this->get('soporte.SoporteService');

        try
        {
            $arrayRequestPermisos = array(
                'idCabecera'            => $intIdCabecera,
                'idCuadrilla'           => $intIdCuadrilla,
                'opcion'                => $strOpcion,
                'idPersonaEmpresaRol'   => $intIdPersonaEmpresaRol,
                'publishid'             => '',
            );

            //llamar al service que consuma WS de HAL
            $arrayRespuestaHal = $serviceSoporte->getSolicitarPermisoEvento($arrayRequestPermisos);

            if(isset($arrayRespuestaHal) && !empty($arrayRespuestaHal))
            {
                if($arrayRespuestaHal['status'] == 200)
                {
                    $strStatus  = 'OK';
                }
                $strMensaje = $arrayRespuestaHal['mensaje'];
            }
        }
        catch (\Exception $ex)
        {
            $strClass       = "PlanificacionHalController";
            $strAppMethod   = "ajaxLiberarPermisoEventoAction";

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

        $objResponse   = new JsonResponse();

        $objResponse->setData(array('status' => $strStatus, 'mensaje' => $strMensaje));

        return $objResponse;
    }

}
