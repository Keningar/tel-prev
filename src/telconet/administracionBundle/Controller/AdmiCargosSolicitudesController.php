<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Form\AdmiParametroDetType;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\AdmiRol;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Documentación para la clase 'AdmiCargosSolicitudesController'.
 *
 * Clase utilizada para manejar los rangos de aprobación según el cargo de empleado.
 *
 * @author Kevin Baque Puya <kbaque@telconet.ec>
 * @version 1.0 16-06-2021
 *
 */
class AdmiCargosSolicitudesController extends Controller implements TokenAuthenticatedController
{
    /**
     *  @Secure(roles="ROLE_444-1")
     *
     * Documentación para la función 'indexAction'.
     *
     * Función que sirve para cargar la pantalla de la administración de cargos de empleados.
     *
     * @return Response retorna al index de la administración de cargos de empleados.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();

        if($this->get('security.context')->isGranted('ROLE_444-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_444-1';
        }
        if($this->get('security.context')->isGranted('ROLE_444-7'))
        {
            $arrayRolesPermitidos[] = 'ROLE_444-7';
        }
        if($this->get('security.context')->isGranted('ROLE_444-7057'))
        {
            $arrayRolesPermitidos[] = 'ROLE_444-7057';
        }
        $emSeguridad    = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("444", "1");

        return $this->render('administracionBundle:AdmiCargosSolicitudes:index.html.twig', array(
            'item'            => $entityItemMenu,
            'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }

    /**
     *  @Secure(roles="ROLE_444-7")
     *
     * Documentación para la función 'gridAction'.
     *
     * Función que sirve para cargar el listado de cargos de empleados, para 
     * administrar los rangos de porcentaje de aprobación de solicitudes.
     *
     * @return Response $objRespuesta retorna al grid el listado de cargos de empleados.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     *
     */
    public function gridAction()
    {
        $objRespuesta             = new JsonResponse();
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $arrayRegistros           = array();
        $emComercial              = $this->getDoctrine()->getManager('telconet');
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil              = $this->get('schema.Util');
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa');
        $intEmpresaCod            = $objSession->get('idEmpresa');
        $strUserSession           = $objSession->get('user');
        $strIpCreacion            = $objRequest->getClientIp();
        $strNombreParametroCab    = "GRUPO_ROLES_PERSONAL";
        $strEstadoActivo          = 'Activo';
        try
        {
            if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
            {
                $objParametroCargo = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                               ->findOneByNombreParametro(array('nombreParametro' => $strNombreParametroCab,
                                                                                'estado'          => $strEstadoActivo));
                if(!is_object($objParametroCargo) || empty($objParametroCargo))
                {
                    throw new \Exception('No existe el parámetro '.$strNombreParametroCab);
                }
                $arrayParametroCargos = array('estado'      => $strEstadoActivo,
                                              'parametroId' => $objParametroCargo->getId(),
                                              'valor4'      => 'ES_JEFE');
                $arrayResultados = $emComercial->getRepository('schemaBundle:AdmiParametroDet')->getParametrosByCriterios($arrayParametroCargos);
                sort($arrayResultados['registros']);
                if((isset($arrayResultados['registros']) && !empty($arrayResultados['registros'])) && is_array($arrayResultados))
                {
                    foreach($arrayResultados['registros'] as $arrayData)
                    {
                        $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne('RANGO_APROBACION_SOLICITUDES',
                                                                'COMERCIAL',
                                                                'ADMINISTRACION_CARGOS_SOLICITUDES', 
                                                                '', 
                                                                '',
                                                                '',
                                                                $arrayData['descripcion'],
                                                                'ES_JEFE',
                                                                '',
                                                                $intEmpresaCod,
                                                                '');
                        if(!empty($arrayParametroDet) && is_array($arrayParametroDet))
                        {
                            $intIdRango       = (!empty($arrayParametroDet) && is_array($arrayParametroDet)) ? $arrayParametroDet['id']:'?';
                            $intRangoIni      = (!empty($arrayParametroDet) && is_array($arrayParametroDet)) ? $arrayParametroDet['valor1']:'?';
                            $intRangoFin      = (!empty($arrayParametroDet) && is_array($arrayParametroDet)) ? $arrayParametroDet['valor2']:'?';
                            $strEstado        = (!empty($arrayParametroDet) && is_array($arrayParametroDet)) ? $arrayParametroDet['estado']:'?';
                            $strLoginAux      = (!empty($arrayParametroDet) && is_array($arrayParametroDet)) ? $arrayParametroDet['observacion']:'?';
                            $arrayRegistros[] = array('intIdCargo'     => $arrayData['id'],
                                                      'strDescripcion' => str_replace("_"," ",ucwords(strtolower($arrayData['descripcion']))),
                                                      'intIdRango'     => $intIdRango,
                                                      'strRangoTotal'  => $intRangoIni.'% - '.$intRangoFin.'%',
                                                      'intRangoIni'    => $intRangoIni,
                                                      'intRangoFin'    => $intRangoFin,
                                                      'strEstado'      => $strEstado,
                                                      'strLoginAux'    => $strLoginAux);
                        }
                    }
                }
                else
                {
                    throw new \Exception('No existen cargos con la descripción enviada por parámetro.');
                }
            }
            else
            {
                throw new \Exception('La Administración de cargos para la gestión de autorizaciones solo aplica para Telconet.');
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCargosSolicitudesController->gridAction', 
                                      $ex->getMessage(), 
                                      $strUserSession, 
                                      $strIpCreacion);
        }
        $objRespuesta->setData(array('cargos' => $arrayRegistros));
        return $objRespuesta;
    }

    /**
     *  @Secure(roles="ROLE_444-7057")
     *
     * Documentación para la función 'setRangosAction'.
     *
     * Función que sirve para editar los nuevos rangos según el cargo.
     *
     * @return Response retorna un mensaje que indica si la transacción fue exitosa.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     */
    public function setRangosAction()
    {
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil              = $this->get('schema.Util');
        $intIdRango               = $objRequest->get("intIdRango") ? $objRequest->get("intIdRango")   : 0;
        $intRangoIni              = $objRequest->get("intRangoIni") ? $objRequest->get("intRangoIni") : '0';
        $intRangoFin              = $objRequest->get("intRangoFin") ? $objRequest->get("intRangoFin") : '0';
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa');
        $strUserSession           = $objSession->get('user');
        $strIpCreacion            = $objRequest->getClientIp();
        $strDatetimeActual        = new \DateTime('now');
        $strMensaje               = '¡Transacción exitosa!';
        $boolGrabar               = true;
        try
        {
            if((!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN") && (!empty($intIdRango) && $intIdRango > 0))
            {
                $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find(intval($intIdRango));

                if(empty($objAdmiParametroDet) || !is_object($objAdmiParametroDet))
                {
                    throw new \Exception('No existe el detalle del parámetro '.$strNombreParametroCab);
                }

                if($objAdmiParametroDet->getValor1() != $intRangoIni)
                {
                    $objAdmiParametroDet->setValor1($intRangoIni);
                    $boolGrabar = true;
                }
                if($objAdmiParametroDet->getValor2() != $intRangoFin)
                {
                    $objAdmiParametroDet->setValor2($intRangoFin);
                    $boolGrabar = true;
                }
                if($boolGrabar)
                {
                    $objAdmiParametroDet->setUsrUltMod($strUserSession);
                    $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                    $objAdmiParametroDet->setFeUltMod($strDatetimeActual );
                    $emGeneral->persist($objAdmiParametroDet);
                    $emGeneral->flush();
                    if($emGeneral->getConnection()->isTransactionActive())
                    {
                        $emGeneral->getConnection()->commit();
                        $emGeneral->getConnection()->close();
                    }
                }
            }
            else
            {
                throw new \Exception('La Administración de cargos para la gestión de autorizaciones solo aplica para Telconet.');
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = 'Hubo un problema al guardar los cambios, por favor comunicarse con el departamento de Sistemas.';
            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
                $emGeneral->getConnection()->close();
            }
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCargosSolicitudesController->setRangosAction', 
                                      $ex->getMessage(), 
                                      $strUserSession, 
                                      $strIpCreacion);
        }
        return new Response($strMensaje);
    }

    /**
     *
     * Documentación para la función 'setPersonaAuxiliarAction'.
     *
     * Función que sirve para editar el usuario Auxiliar que podrá aprobar las solicitudes.
     *
     * @return Response retorna un mensaje que indica si la transacción fue exitosa.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     */
    public function setPersonaAuxiliarAction()
    {
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil              = $this->get('schema.Util');
        $intIdParametro           = $objRequest->get("intIdParametro") ? $objRequest->get("intIdParametro")   : 0;
        $intIdPersona             = $objRequest->get("intIdPersona") ? $objRequest->get("intIdPersona") : '0';
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa');
        $strUserSession           = $objSession->get('user');
        $strIpCreacion            = $objRequest->getClientIp();
        $strDatetimeActual        = new \DateTime('now');
        $strMensaje               = '¡Transacción exitosa!';
        $strLogin                 = '';
        try
        {
            if(empty($strPrefijoEmpresa) && $strPrefijoEmpresa != "TN")
            {
                throw new \Exception('La Administración de persona auxiliar para la gestión de autorizaciones solo aplica para Telconet.');
            }
            if(empty($intIdParametro))
            {
                throw new \Exception('El parámetro es un campo obligatorio para realizar la acción.');
            }
            $objInfoPersona = $emGeneral->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
            if(!is_object($objInfoPersona) || empty($objInfoPersona))
            {
                throw new \Exception('No existe Usuario, con los parámetros enviados.');
            }
            $strLogin = $objInfoPersona->getLogin();
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->find(intval($intIdParametro));
            if(!is_object($objAdmiParametroDet) || empty($objAdmiParametroDet))
            {
                throw new \Exception('No existe el detalle del parámetro enviado.');
            }
            $objCargosCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                      ->findOneBy(array('nombreParametro' => 'RANGO_APROBACION_SOLICITUDES',
                                                        'modulo'          => 'COMERCIAL',
                                                        'proceso'         => 'ADMINISTRACION_CARGOS_SOLICITUDES',
                                                        'estado'          => 'Activo'));
            if(!is_object($objCargosCab) || empty($objCargosCab))
            {
                throw new \Exception('No existe el parámetro RANGO_APROBACION_SOLICITUDES');
            }
            $arrayCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                        'valor4'      => 'ES_JEFE',
                                                        'valor7'      => 'SI',
                                                        'estado'      => 'Activo'));
            $boolRepetido = false;
            if(!empty($arrayCargosDet) && is_array($arrayCargosDet))
            {
                foreach($arrayCargosDet as $objCargoDet)
                {
                    if( $objCargoDet->getId() != $intIdParametro && $objCargoDet->getObservacion() == $strLogin )
                    {
                        $boolRepetido = true;
                        throw new \Exception('El usuario '.$strLogin.' ya se encuentra como auxilar.');
                    }
                }
            }
            else
            {
                throw new \Exception('No existe el detalle del parámetro RANGO_APROBACION_SOLICITUDES');
            }

            if($objAdmiParametroDet->getObservacion() != $strLogin)
            {
                $objAdmiParametroDet->setObservacion($strLogin);
                $objAdmiParametroDet->setUsrUltMod($strUserSession);
                $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                $objAdmiParametroDet->setFeUltMod($strDatetimeActual );
                $emGeneral->persist($objAdmiParametroDet);
                $emGeneral->flush();
                if($emGeneral->getConnection()->isTransactionActive())
                {
                    $emGeneral->getConnection()->commit();
                    $emGeneral->getConnection()->close();
                }
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = 'Hubo un problema al guardar los cambios, por favor comunicarse con el departamento de Sistemas.';
            if($boolRepetido)
            {
                $strMensaje = 'El usuario '.$strLogin.' ya se encuentra como auxilar.';
            }

            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
                $emGeneral->getConnection()->close();
            }
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCargosSolicitudesController->setPersonaAuxiliarAction', 
                                      $ex->getMessage(), 
                                      $strUserSession, 
                                      $strIpCreacion);
        }
        return new Response($strMensaje);
    }

    /**
     *
     * Documentación para la función 'setEliminarAuxiliarAction'.
     *
     * Función que sirve para eliminar el usuario Auxiliar existente.
     *
     * @return Response retorna un mensaje que indica si la transacción fue exitosa.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 16-06-2021
     */

    public function setEliminarAuxiliarAction()
    {
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtil              = $this->get('schema.Util');
        $intIdParametro           = $objRequest->get("intIdParametro") ? $objRequest->get("intIdParametro")   : 0;
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa');
        $strUserSession           = $objSession->get('user');
        $strIpCreacion            = $objRequest->getClientIp();
        $strDatetimeActual        = new \DateTime('now');
        $strMensaje               = '¡Transacción exitosa!';
        $boolGrabar               = false;

        try
        {
            if(empty($strPrefijoEmpresa) && $strPrefijoEmpresa != "TN")
            {
                throw new \Exception('La Administración de persona auxiliar para la gestión de autorizaciones solo aplica para Telconet.');
            }
            if(empty($intIdParametro))
            {
                throw new \Exception('El parámetro es un campo obligatorio para realizar la acción.');
            }
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find(intval($intIdParametro));
            if(empty($objAdmiParametroDet) || !is_object($objAdmiParametroDet))
            {
                throw new \Exception('No existe el detalle del parámetro enviado.');
            }

            if( $objAdmiParametroDet->getObservacion() != null )
            {
                $objAdmiParametroDet->setObservacion(null);
                $objAdmiParametroDet->setUsrUltMod($strUserSession);
                $objAdmiParametroDet->setIpUltMod($strIpCreacion);
                $objAdmiParametroDet->setFeUltMod($strDatetimeActual );
                $emGeneral->persist($objAdmiParametroDet);
                $emGeneral->flush();
                if($emGeneral->getConnection()->isTransactionActive())
                {
                    $emGeneral->getConnection()->commit();
                    $emGeneral->getConnection()->close();
                }
            }
            else
            {
                $strMensaje = 'El cargo seleccionado no tiene auxiliar asignado.';
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = 'Hubo un problema al guardar los cambios, por favor comunicarse con el departamento de Sistemas.';
            
            if($emGeneral->getConnection()->isTransactionActive())
            {
                $emGeneral->getConnection()->rollback();
                $emGeneral->getConnection()->close();
            }
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiCargosSolicitudesController->setEliminarAuxiliarAction', 
                                      $ex->getMessage(), 
                                      $strUserSession, 
                                      $strIpCreacion);
        }
        return new Response($strMensaje);
    }
}
