<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\AdmiZona;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;

class AdmiZonaController extends Controller implements TokenAuthenticatedController
{
    /**
    * indexAction
    * Esta funcion carga el index de los zonas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 04-04-2018
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 10-09-2018 - Se agrega en el método la información en sesión del usuario, que será de utilidad
    *                           para la asignación del nuevo responsable de la Zona.
    *
    * @Secure(roles="ROLE_410-1")
    *
    */
    public function indexAction()
    {
        $arrayRolesPermitidos        = array();
        $emSeguridad                 = $this->getDoctrine()->getManager('telconet_seguridad');
        $emComercial                 = $this->getDoctrine()->getManager();
        $objSession                  = $this->get('request')->getSession();
        $strPrefijoEmpresaSession    = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $intIdDepartamentoUsrSession = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $intIdCantonUsrSession       = 0;
        $intIdOficinaSesion          = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;

        if($intIdOficinaSesion)
        {
            $objOficinaSesion = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficinaSesion);

            if(is_object($objOficinaSesion))
            {
                $intIdCantonUsrSession = $objOficinaSesion->getCantonId();
            }
        }

        if(true === $this->get('security.context')->isGranted('ROLE_410-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_410-1';//consultar zonas
        }
        if(true === $this->get('security.context')->isGranted('ROLE_410-7'))
        {
            $arrayRolesPermitidos[] = 'ROLE_410-7';//grid de zonas
        }
        if(true === $this->get('security.context')->isGranted('ROLE_410-5820'))
        {
            $arrayRolesPermitidos[] = 'ROLE_410-5820';//eliminar zonas
        }
        if(true === $this->get('security.context')->isGranted('ROLE_410-5819'))
        {
            $arrayRolesPermitidos[] = 'ROLE_410-5819';//actualizar zonas
        }
        if(true === $this->get('security.context')->isGranted('ROLE_410-5818'))
        {
            $arrayRolesPermitidos[] = 'ROLE_410-5818';//nueva zonas
        }

        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("410", "1");

        return $this->render('administracionBundle:AdmiZona:index.html.twig',
                              array('item'                        => $entityItemMenu,
                                    'rolesPermitidos'             => $arrayRolesPermitidos,
                                    'strPrefijoEmpresaSession'    => $strPrefijoEmpresaSession,
                                    'intIdCantonUsrSession'       => $intIdCantonUsrSession,
                                    'intIdDepartamentoUsrSession' => $intIdDepartamentoUsrSession));
    }

    /**
    * gridAction
    * Esta funcion llena el grid de la consulta de zonas
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 04-04-2018
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 10-09-2018 - Se modifica el método para obtener el responsable de la zona.
    *
    * @Secure(roles="ROLE_410-7")
    *
    */
    public function gridAction()
    {
        $objPeticion   = $this->get('request');
        $emGeneral     = $this->getDoctrine()->getManager("telconet_general");
        $strEstadoZona = $objPeticion->get('estado');
        $strNombreZona = $objPeticion->get('nombreZona');
        $objResponse   = new JsonResponse();
        $intTotal      = 0;
        $arrayZonas    = array();

        $arrayEncontrados = $emGeneral->getRepository('schemaBundle:AdmiZona')
                                      ->getZonasNativeQuery(array ('boolSubQuery'      => true,
                                                                   'strCaracteristica' => 'RESPONSABLE_ZONA',
                                                                   'strEstadoAc'       => 'Activo',
                                                                   'strEstadoIperc'    => 'Activo',
                                                                   'strEstadoIper'     => 'Activo',
                                                                   'strEstadoIp'       => 'Activo',
                                                                   'strEstadoZona'     => $strEstadoZona,
                                                                   'strNombreZona'     => $strNombreZona));
        if ($arrayEncontrados['status'] === 'ok')
        {
            $intTotal   = count($arrayEncontrados['result']);
            $arrayZonas = $arrayEncontrados['result'];
        }

        $objResponse->setData(array ('total'       => $intTotal,
                                     'encontrados' => $arrayZonas));

        return $objResponse;
    }

    /**
    * deleteAjaxAction
    * Funcion que cambia a estado Eliminado las Zonas
    *
    * @return json $arrayRespuesta
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 14-08-2018 - Se agrega una nueva validación para identificar
    *                           las zonas planificadas en las estructuras de HAL.
    *                         - Se modifica el método para obtener en una array todas las ZONAS que fueron eliminadas y notificarle a HAL.
    *
    * @Secure(roles="ROLE_410-5820")
    *
    */
    public function deleteAjaxAction()
    {
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $objPeticion        = $this->getRequest();
        $objSession         = $objPeticion->getSession();
        $strUserSession     = $objSession->get('user');
        $strIpCreacion      = $objPeticion->getClientIp();
        $strTramaZonas      = $objPeticion->get('tramaZonas');
        $arrayZonas         = explode("|", $strTramaZonas);
        $objResponse        = new JsonResponse();
        $arrayRespuesta     = array();
        $strEstadoZona      = "Eliminado";
        $serviceUtil        = $this->get('schema.Util');
        $serviceSoporte     = $this->get('soporte.SoporteService');
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $boolBandera        = false;
        $arrayElementos     = array();
        $arrayCuadrillas    = array();
        $arrayVehiculos     = array();
        $arrayVehiProvis    = array();
        $arrayCuadPlanif    = array();
        $arrayZonasElim     = array();

        $emGeneral->getConnection()->beginTransaction();

        try
        {
            for ($i = 0; $i <= count($arrayZonas); $i++)
            {
                if (!empty($arrayZonas[$i]))
                {
                    $objAdmiZona = $emGeneral->getRepository('schemaBundle:AdmiZona')->find($arrayZonas[$i]);

                    if (is_object($objAdmiZona))
                    {
                        // Verificamos si existe un elemento atado a la zona
                        $arrayInfoDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                            ->getDetalleElemento( array('strDetalleNombre' => 'ZONA',
                                                        'strDetalleValor'  =>  $objAdmiZona->getId(),
                                                        'strEstado'        => 'Activo'));
 
                        if (!empty($arrayInfoDetalleElemento) && count($arrayInfoDetalleElemento) > 0)
                        {
                            $arrayElementos[] = $objAdmiZona->getNombreZona();
                            $boolBandera      = true;
                            continue;
                        }

                        // Verificamos si existe una cuadrilla atado a la zona
                        $arrayAdmiCuadrilla = $emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                            ->findCuadrillas( array('intZonaId' => $objAdmiZona->getId()));

                        if (!empty($arrayAdmiCuadrilla) && count($arrayAdmiCuadrilla) > 0)
                        {
                            $arrayCuadrillas[] = $objAdmiZona->getNombreZona();
                            $boolBandera       = true;
                            continue;
                        }

                        // Verificamos si existe un vehiculo atado a la zona
                        $arrayInfoDetalleElementoVehiculo = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                            ->getDetalleElementoCaracteristica( array(
                                'strDetalleNombre'  => 'ASIGNACION_VEHICULAR_ID_SOL_PREDEF_CUADRILLA',
                                'strCaracteristica' => 'ZONA_PREDEFINIDA_ASIGNACION_VEHICULAR',
                                'intValor'          =>  $objAdmiZona->getId(),
                                'strEstadoElemento' => 'Activo',
                                'strEstadoSol'      => 'Activo',
                                'strEstadoCar'      => 'Activo'));
 
                        if (!empty($arrayInfoDetalleElementoVehiculo) && count($arrayInfoDetalleElementoVehiculo) > 0)
                        {
                            $arrayVehiculos[] = $objAdmiZona->getNombreZona();
                            $boolBandera      = true;
                            continue;
                        }

                        // Verificamos si existe un vehiculo provisional atado a la zona
                        $arrayVehiculoProvisional = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                            ->getDetalleElemento( array('strDetalleNombre' => 'ASIGNACION_PROVISIONAL_CHOFER_ZONA',
                                                        'strDetalleValor'  =>  $objAdmiZona->getId(),
                                                        'strEstado'        => 'Activo'));
 
                        if (!empty($arrayVehiculoProvisional) && count($arrayVehiculoProvisional) > 0)
                        {
                            $arrayVehiProvis[] = $objAdmiZona->getNombreZona();
                            $boolBandera       = true;
                            continue;
                        }

                        // Verificamos si existe una cuadrilla planificada de hal atado a la zona
                        $arrayCuadrillaPlanifHal = $emSoporte->getRepository('schemaBundle:InfoCuadrillaPlanifCab')
                            ->findBy( array('zonaId' => $objAdmiZona->getId(),
                                            'estado' => array('Activo','Liberado')));

                        if (!empty($arrayCuadrillaPlanifHal) && count($arrayCuadrillaPlanifHal) > 0)
                        {
                            $arrayCuadPlanif[] = $objAdmiZona->getNombreZona();
                            $boolBandera       = true;
                            continue;
                        }

                        // Si la zona no se encuentra atada a ninguna de las validaciones realizadas, se procede con la eliminacion
                        $objAdmiZona->setEstado($strEstadoZona);
                        $objAdmiZona->setUsrModificacion($strUserSession);
                        $objAdmiZona->setFeUltMod(new \DateTime('now'));
                        $emGeneral->persist($objAdmiZona);
                        $emGeneral->flush();
                        $arrayZonasElim[] = $objAdmiZona->getId();
                    }
                }
            }

            if ($boolBandera)
            {
                if (!empty($arrayElementos))
                {
                    $strMensaje .= 'No se eliminaron las siguientes zonas:<br />';

                    foreach($arrayElementos as $intValor)
                    {
                        if (is_null($strZonasElementos))
                        {
                            $strZonasElementos = '[<b style="color:green;">'.$intValor.'</b>';
                        }
                        else
                        {
                            $strZonasElementos .= ', <b style="color:green;">'.$intValor.'</b>';
                        }
                    }

                    $strMensaje .= $strZonasElementos.']<br /> Debido a que tienen elementos asociados.<br /><br />';
                }

                if (!empty($arrayCuadrillas))
                {
                    $strMensaje .= 'No se eliminaron las siguientes zonas:<br />';

                    foreach($arrayCuadrillas as $intValor)
                    {
                        if (is_null($strZonaCuadrilla))
                        {
                            $strZonaCuadrilla = '[<b style="color:green;">'.$intValor.'</b>';
                        }
                        else
                        {
                            $strZonaCuadrilla .= ', <b style="color:green;">'.$intValor.'</b>';
                        }
                    }
                    
                    $strMensaje .= $strZonaCuadrilla.']<br /> Debido a que tienen cuadrillas asociadas.<br /><br />';
                }

                if (!empty($arrayVehiculos))
                {
                    $strMensaje .= 'No se eliminaron las siguientes zonas:<br />';

                    foreach($arrayVehiculos as $intValor)
                    {
                        if (is_null($strZonaVehiculo))
                        {
                            $strZonaVehiculo = '[<b style="color:green;">'.$intValor.'</b>';
                        }
                        else
                        {
                            $strZonaVehiculo .= ', <b style="color:green;">'.$intValor.'</b>';
                        }
                    }
                    
                    $strMensaje .= $strZonaVehiculo.']<br /> Debido a que tienen vehículos asociados.<br /><br />';
                }

                if (!empty($arrayVehiProvis))
                {
                    $strMensaje .= 'No se eliminaron las siguientes zonas:<br />';

                    foreach($arrayVehiProvis as $intValor)
                    {
                        if (is_null($strZonaVehiculoProv))
                        {
                            $strZonaVehiculoProv = '[<b style="color:green;">'.$intValor.'</b>';
                        }
                        else
                        {
                            $strZonaVehiculoProv .= ', <b style="color:green;">'.$intValor.'</b>';
                        }
                    }
                    
                    $strMensaje .= $strZonaVehiculoProv.']<br /> Debido a que tienen asignación provisional de vehículos.<br /><br />';
                }

                if(!empty($arrayCuadPlanif))
                {
                    $strMensaje .= 'No se eliminaron las siguientes zonas:<br />';

                    foreach($arrayCuadPlanif as $intValor)
                    {
                        if (is_null($strCuadPlanifHal))
                        {
                            $strCuadPlanifHal = '[<b style="color:green;">'.$intValor.'</b>';
                        }
                        else
                        {
                            $strCuadPlanifHal .= ', <b style="color:green;">'.$intValor.'</b>';
                        }
                    }

                    $strMensaje .= $strCuadPlanifHal.']<br /> Debido a que tienen cuadrillas planificadas en HAL.<br /><br />';
                }

                $arrayRespuesta["estado"]  = "fail";
                $arrayRespuesta["mensaje"] = $strMensaje;
            }
            else
            {
                $arrayRespuesta["estado"]  = "ok";
                $arrayRespuesta["mensaje"] = "Transacción Exitosa";
            }

            /*========================= INICIO NOTIFICACION HAL ==========================*/
            if (!empty($arrayZonasElim) && count($arrayZonasElim) > 0)
            {
                $emGeneral->getConnection()->commit();

                foreach($arrayZonasElim as $intValor)
                {
                        $serviceSoporte->notificacionesHal(
                                array ('strModulo' => 'zona',
                                       'strUser'   =>  $strUserSession,
                                       'strIp'     =>  $strIpCreacion,
                                       'arrayJson' =>  array ('metodo' => 'elimino',
                                                              'id'     => $intValor)));
                }
            }
            /*========================== FIN NOTIFICACION HAL ============================*/
        }
        catch (\Exception $objEx)
        {
            if ($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
                $emGeneral->getConnection()->close();
            }

            $serviceUtil->insertError('Telcos+',
                                      'AdmiZonaController->deleteAjaxAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $arrayRespuesta["estado"]  = "fail";
            $arrayRespuesta["mensaje"] = "<b>Transacción Fallida..!!</b>";
        }

        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }

    /**
    * updateAjaxAction
    * Funcion que cambia los datos de una zona
    *
    * @return json $arrayRespuesta
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @Secure(roles="ROLE_410-5819")
    *
    */
    public function updateAjaxAction()
    {
        $emGeneral       = $this->getDoctrine()->getManager("telconet_general");
        $objPeticion     = $this->getRequest();
        $objSession      = $objPeticion->getSession();
        $strUserSession  = $objSession->get('user');
        $strIpCreacion   = $objPeticion->getClientIp();
        $strNombreZona   = $objPeticion->get('nombre_zona');
        $strId           = $objPeticion->get('id_zona');
        $objResponse     = new JsonResponse();
        $arrayRespuesta  = array();
        $serviceUtil     = $this->get('schema.Util');
        $serviceSoporte  = $this->get('soporte.SoporteService');

        $emGeneral->getConnection()->beginTransaction();

        try
        {
            if (!empty($strId) && !empty($strNombreZona))
            {
                $objAdmiZona = $emGeneral->getRepository('schemaBundle:AdmiZona')->find($strId);

                if(is_object($objAdmiZona))
                {
                    $arrayAdmiZonas = $emGeneral->getRepository('schemaBundle:AdmiZona')
                        ->getZonas( array('strEstado'     => 'Activo',
                                          'strNombreZona' => $strNombreZona,
                                          'strOpcion'     => 'actualizar',
                                          'intIdZona'     => $objAdmiZona->getId()));

                    if (!empty($arrayAdmiZonas) && count($arrayAdmiZonas) > 0)
                    {
                        $arrayRespuesta["estado"]  = "fail";
                        $arrayRespuesta["mensaje"] = "<b>La zona ya se encuentra registrada..!!</b>";
                    }
                    else
                    {
                        $objAdmiZona->setNombreZona(strtoupper($strNombreZona));
                        $objAdmiZona->setUsrModificacion($strUserSession);
                        $objAdmiZona->setFeUltMod(new \DateTime('now'));
                        $emGeneral->persist($objAdmiZona);
                        $emGeneral->flush();

                        $emGeneral->getConnection()->commit();

                        /*========================= INICIO NOTIFICACION HAL ==========================*/
                        if (!empty($strId))
                        {
                            $serviceSoporte->notificacionesHal(
                                        array ('strModulo' => 'zona',
                                               'strUser'   =>  $strUserSession,
                                               'strIp'     =>  $strIpCreacion,
                                               'arrayJson' =>  array ('metodo' => 'actualizo',
                                                                      'id'     =>  $strId)));

                        }
                        /*========================== FIN NOTIFICACION HAL ============================*/

                        $arrayRespuesta["estado"]  = "ok";
                        $arrayRespuesta["mensaje"] = "Transaccion Exitosa";
                    }
                }
            }
            else
            {
                $arrayRespuesta["estado"]  = "fail";
                $arrayRespuesta["mensaje"] = "<b>No se puede registrar valores nulos..!!</b>";
            }
        }
        catch (\Exception $ex)
        {
            if ($emGeneral->isTransactionActive())
            {
                $emGeneral->rollback();
            }

            $serviceUtil->insertError('Telcos+',
                                      'AdmiZonaController->updateAjaxAction',
                                      $ex->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $emGeneral->close();

            $arrayRespuesta["estado"]  = "fail";
            $arrayRespuesta["mensaje"] = "<b>Transaccion Fallida..!!</b>";
        }

        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }

    /**
    * ingresarAjaxAction
    * Funcion que ingresa una nueva zona
    *
    * @return json $arrayRespuesta
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 08-03-2018
    *
    * @Secure(roles="ROLE_410-5818")
    *
    */
    public function ingresarAjaxAction()
    {
        $emGeneral       = $this->getDoctrine()->getManager("telconet_general");
        $objPeticion     = $this->getRequest();
        $objSession      = $objPeticion->getSession();
        $strUserSession  = $objSession->get('user');
        $strIpCreacion   = $objPeticion->getClientIp();
        $strNombreZona   = $objPeticion->get('nombre_zona');
        $objResponse     = new JsonResponse();
        $arrayRespuesta  = array();
        $serviceUtil     = $this->get('schema.Util');
        $serviceSoporte  = $this->get('soporte.SoporteService');

        $emGeneral->getConnection()->beginTransaction();

        try
        {
            if (!is_null($strNombreZona))
            {
                $arrayAdmiZonas = $emGeneral->getRepository('schemaBundle:AdmiZona')
                    ->getZonas( array('strEstado'     => 'Activo',
                                      'strNombreZona' =>  $strNombreZona,
                                      'strOpcion'     => 'nuevo'));

                if (!empty($arrayAdmiZonas) && count($arrayAdmiZonas) > 0)
                {
                    $arrayRespuesta["estado"]  = "Error";
                    $arrayRespuesta["mensaje"] = "<b>La zona ya se encuentra registrada..!!</b>";
                }
                else
                {
                    $objAdmiZona = new AdmiZona();
                    $objAdmiZona->setNombreZona(strtoupper($strNombreZona));
                    $objAdmiZona->setLatitud(0);
                    $objAdmiZona->setLongitud(0);
                    $objAdmiZona->setRadio(1);
                    $objAdmiZona->setEstado('Activo');
                    $objAdmiZona->setUsrCreacion($objSession->get('user'));
                    $objAdmiZona->setFeCreacion(new \DateTime('now'));
                    $emGeneral->persist($objAdmiZona);
                    $emGeneral->flush();

                    $emGeneral->getConnection()->commit();

                    /*========================= INICIO NOTIFICACION HAL ==========================*/
                    $serviceSoporte->notificacionesHal(
                            array ('strModulo' => 'zona',
                                   'strUser'   =>  $strUserSession,
                                   'strIp'     =>  $strIpCreacion,
                                   'arrayJson' =>  array ('metodo' => 'nueva',
                                                          'id'     =>  $objAdmiZona->getId())));
                    /*========================== FIN NOTIFICACION HAL ============================*/

                    $arrayRespuesta["estado"]  = "Ok";
                    $arrayRespuesta["mensaje"] = "Transaccion Exitosa";

                }
            }
            else
            {
                $arrayRespuesta["estado"]  = "Error";
                $arrayRespuesta["mensaje"] = "<b>No se puede registrar valores nulos..!!</b>";
            }
        }
        catch (\Exception $objEx)
        {
            if ($emGeneral->isTransactionActive())
            {
                $emGeneral->rollback();
            }

            $serviceUtil->insertError('Telcos+',
                                      'AdmiZonaController->ingresarAjaxAction',
                                      $objEx->getMessage(),
                                      $strUserSession,
                                      $strIpCreacion);

            $emGeneral->close();
            $arrayRespuesta["estado"]  = "Error";
            $arrayRespuesta["mensaje"] = "<b>Transaccion Fallida..!!</b>";
        }
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }

    /**
     * Método que se encarga de asignar al responsable de la ZONA.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 - 10-09-2018
     *
     * @return JsonResponse
     */
    public function asignarResponsableAjaxAction()
    {
        $objResponse                = new JsonResponse();
        $objRequest                 = $this->get('request');
        $objSession                 = $objRequest->getSession();
        $strJsonData                = $objRequest->get('jsonData');
        $strUsuario                 = $objSession->get('user');
        $strIp                      = $objRequest->getClientIp();
        $emComercial                = $this->getDoctrine()->getManager('telconet');
        $serviceSoporte             = $this->get('soporte.SoporteService');
        $serviceUtil                = $this->get('schema.Util');
        $arrayData                  = (array) json_decode($strJsonData);
        $arrayResultado             = array();
        $arrayResultado ['succes']  = false;
        $arrayResultado ['message'] = 'Fallo al guardar los datos';

        $emComercial->getConnection()->beginTransaction();

        try
        {
            if (!empty($arrayData['nuevo'])
                && count($arrayData['nuevo']) > 0
                && !empty($arrayData['nuevo']->idPersonaEmpresaRol)
                && !empty($arrayData['idZona']))
            {
                $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->find($arrayData['nuevo']->idPersonaEmpresaRol);

                $objAdmiCaracteristica    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array ('descripcionCaracteristica' => 'RESPONSABLE_ZONA', 'estado' => 'Activo'));

                if (is_object($objInfoPersonaEmpresaRol) && is_object($objAdmiCaracteristica))
                {
                    $arrayPersonaEmpresaRolCarac = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                        ->findBy(array ('caracteristicaId' => $objAdmiCaracteristica,
                                        'valor'            => $arrayData['idZona'],
                                        'estado'           => 'Activo'));

                    if (!empty($arrayPersonaEmpresaRolCarac) && count($arrayPersonaEmpresaRolCarac) > 0)
                    {
                        foreach ($arrayPersonaEmpresaRolCarac as $objInfoPersonaEmpresaRolCarac)
                        {
                            if  (is_object($objInfoPersonaEmpresaRolCarac))
                            {
                                $objInfoPersonaEmpresaRolCarac->setEstado('Eliminado');
                                $objInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                                $objInfoPersonaEmpresaRolCarac->setUsrUltMod($strUsuario);
                                $emComercial->persist($objInfoPersonaEmpresaRolCarac);
                                $emComercial->flush();
                            }
                        }
                    }

                    $objPersonaEmpresaRolCaracNew = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                        ->findOneBy(array ('caracteristicaId'    => $objAdmiCaracteristica,
                                           'valor'               => $arrayData['idZona'],
                                           'personaEmpresaRolId' => $objInfoPersonaEmpresaRol));

                    if (is_object($objPersonaEmpresaRolCaracNew))
                    {
                        $objPersonaEmpresaRolCaracNew->setEstado('Activo');
                        $objPersonaEmpresaRolCaracNew->setFeUltMod(new \DateTime('now'));
                        $objPersonaEmpresaRolCaracNew->setUsrUltMod($strUsuario);
                    }
                    else
                    {
                        $objPersonaEmpresaRolCaracNew = new InfoPersonaEmpresaRolCarac();
                        $objPersonaEmpresaRolCaracNew->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $objPersonaEmpresaRolCaracNew->setCaracteristicaId($objAdmiCaracteristica);
                        $objPersonaEmpresaRolCaracNew->setValor($arrayData['idZona']);
                        $objPersonaEmpresaRolCaracNew->setFeCreacion(new \DateTime('now'));
                        $objPersonaEmpresaRolCaracNew->setUsrCreacion($strUsuario);
                        $objPersonaEmpresaRolCaracNew->setIpCreacion($strIp);
                        $objPersonaEmpresaRolCaracNew->setEstado('Activo');
                    }

                    $emComercial->persist($objPersonaEmpresaRolCaracNew);
                    $emComercial->flush();
                    $emComercial->commit();

                    $arrayResultado ['succes']  = true;
                    $arrayResultado ['message'] = 'El responsable se asignó correctamente';

                    /*========================= INICIO NOTIFICACION HAL ==========================*/
                    $serviceSoporte->notificacionesHal(
                                array ('strModulo' => 'zona',
                                       'strUser'   =>  $strUsuario,
                                       'strIp'     =>  $strIp,
                                       'arrayJson' =>  array ('metodo' => 'actualizo',
                                                              'id'     =>  intval($arrayData['idZona']))));
                    /*========================== FIN NOTIFICACION HAL ============================*/
                }
            }
        }
        catch (\Exception $objException)
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }

            $emComercial->close();

            $serviceUtil->insertError('Telcos+',
                                      'AdmiZonaController->asignarResponsableAjaxAction',
                                       $objException->getMessage(),
                                       $strUsuario,
                                       $strIp);

            $arrayResultado ['succes']  = false;
            $arrayResultado ['message'] = 'Error al guardar los datos';
        }

        $objResponse->setData($arrayResultado);
        return $objResponse;
    }
}
