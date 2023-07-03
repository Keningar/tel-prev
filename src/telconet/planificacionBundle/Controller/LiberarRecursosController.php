<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Documentación para la clase 'LiberarRecursos'.
 *
 * Clase utilizada para manejar metodos que permiten realizar la liberacion de recursos de red
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 17-12-2014
 */
class LiberarRecursosController extends Controller
{

    /**
     * @Secure(roles="ROLE_270-1")
     * Documentación para el método 'indexAction'.
     *
     * Index de opcion Liberar Recursos de Red
     * @return response.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 12-12-2014
     */
    public function indexAction()
    {
        $rolesPermitidos = array();
        //MODULO 270 - LIBERAR SERVICIOS FACTIBLES
        if(true === $this->get('security.context')->isGranted('ROLE_270-7'))
        {
            $rolesPermitidos[] = 'ROLE_270-7'; // GRID 
        }
        if(true === $this->get('security.context')->isGranted('ROLE_270-1997'))
        {
            $rolesPermitidos[] = 'ROLE_270-1997'; // LIBERAR RECURSOS
        }

        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("270", "1");
        
        return $this->render('planificacionBundle:Factibilidad:indexLiberarRecursos.html.twig', array(
                'item' => $entityItemMenu,
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * @Secure(roles="ROLE_270-7")
     * Documentación para el método 'ajaxGridAction'.
     *
     * Llena grid de servicios factibles a liberar
     * @return response.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 12-12-2014
     */
    public function ajaxGridAction()
    {
        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion   = $this->get('request');
        $fechaDesde = explode('T', $peticion->get('fechaDesde'));
        $fechaHasta = explode('T', $peticion->get('fechaHasta'));
        $automatica = $peticion->get('automatica');
        $mayorA     = $peticion->get('mayorA');
        $login      = $peticion->get('login');
        $start      = $peticion->get('start');
        $limit      = $peticion->get('limit');
        $em         = $this->getDoctrine()->getManager("telconet");

        $arrayParametros                        = array();
        $arrayParametros["start"]               = $start;
        $arrayParametros["limit"]               = $limit;
        $arrayParametros["factibilidadDesde"]   = $fechaDesde[0];
        $arrayParametros["factibilidadHasta"]   = $fechaHasta[0];
        $arrayParametros["automatica"]          = $automatica;
        $arrayParametros["login"]               = $login;
        $arrayParametros["mayorA"]              = $mayorA;
        
        $objJson = $em->getRepository('schemaBundle:InfoServicio')
                      ->generarJsonServicioALiberar($arrayParametros);

        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_270-1997")
     * Documentación para el método 'deleteAction'.
     *
     * Libera los recursos de red de un servicio factible
     * @return response.
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 12-12-2014
     */
    public function deleteAjaxAction()
    {
        ini_set('max_execution_time', 3000000);
        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $usrCreacion    = $session->get('user');
        $clientIp       = $peticion->getClientIp();
        $respuesta      = new Response();
        //Obtiene parametros enviados desde el ajax
        $strParam       = $peticion->get('param');
        $array_valor    = explode("|", $strParam);
        $respuesta->headers->set('Content-Type', 'text/plain');
        $em             = $this->getDoctrine()->getManager("telconet");
        $em->beginTransaction();
        try
        {
            foreach($array_valor as $id):

                //INFO_SERVICIO
                $entityServicio = $em->getRepository('schemaBundle:InfoServicio')->find($id);
                $entityServicio->setEstado('Pre-servicio');
                $em->persist($entityServicio);

                //INFO_SERVICIO_HISTORIAL
                $entityServicioHist = new InfoServicioHistorial();
                $entityServicioHist->setServicioId($entityServicio);
                $entityServicioHist->setObservacion('Se liberaron recursos de red del servicio por exceder dias de factibilidad');
                $entityServicioHist->setIpCreacion($clientIp);
                $entityServicioHist->setFeCreacion(new \DateTime('now'));
                $entityServicioHist->setUsrCreacion($usrCreacion);
                $entityServicioHist->setEstado($entityServicio->getEstado());
                $em->persist($entityServicioHist);

                //INFO_SERVICIO_TECNICO
                $entityServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($id);
                if($entityServicioTecnico)
                {
                    if($entityServicioTecnico->getInterfaceElementoConectorId())
                    {
                        //INFO_INTERFACE_ELEMENTO
                        $entityInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')
                                                      ->findOneById($entityServicioTecnico->getInterfaceElementoConectorId());
                        if($entityInterfaceElemento)
                        {
                            $entityInterfaceElemento->setEstado('not connect');
                            $em->persist($entityInterfaceElemento);
                        }
                    }
                    $entityServicioTecnico->setElementoId(null);
                    $entityServicioTecnico->setInterfaceElementoId(null);
                    $entityServicioTecnico->setElementoContenedorId(null);
                    $entityServicioTecnico->setElementoConectorId(null);
                    $entityServicioTecnico->setInterfaceElementoConectorId(null);
                    $em->persist($entityServicioTecnico);
                }
                //SOLICITUD DE FACTIBILIDAD
                $entityDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                             ->findOneBy(array("servicioId" => $id, "tipoSolicitudId" => 6, "estado" => "Factible"));
                if($entityDetalleSolicitud)
                {
                    $entityDetalleSolicitud->setObservacion("Se liberaron recursos de red del servicio, por superar tiempo limite de factibilidad");
                    $entityDetalleSolicitud->setEstado("Anulado");
                    $em->persist($entityDetalleSolicitud);
                    $em->flush();

                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $entityDetalleSolHist = new InfoDetalleSolHist();
                    $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                    $entityDetalleSolHist->setObservacion("Se liberaron recursos de red del servicio, por superar tiempo limite de factibilidad");
                    $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                    $entityDetalleSolHist->setEstado('Anulado');
                    $em->persist($entityDetalleSolHist);
                }
                $respuesta->setContent("Se liberaron recursos de red correctamente.");
                
            endforeach;
            $em->flush();
            $em->commit();
        }
        catch(\Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $em->getConnection()->close();
            $mensajeError = "Error: ".$e->getMessage();
            error_log($mensajeError);
            $respuesta->setContent("Existieron problemas al liberar recursos de red, favor notificar a sistemas.");
        }

        return $respuesta;
    }
}