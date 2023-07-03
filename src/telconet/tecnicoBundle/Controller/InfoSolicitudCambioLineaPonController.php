<?php
/*
 * To change this template, choose Tools | Templates and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud; 
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\AdmiDepartamento;
use telconet\schemaBundle\Entity\InfoOficinaGrupo;
use telconet\schemaBundle\Entity\InfoCaso;
use telconet\soporteBundle\Service\EnvioPlantillaService;
use telconet\soporteBundle\Service\SoporteService;

use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Clase para crear la Solicitud de Cambio de Linea Pon.
 * 
 * @author John Vera         <javera@telconet.ec>
 * @author Francisco Adum    <fdaum@telconet.ec>
 * @version 1.0 17-06-2014
 * @version 1.1 modificado:21-06-2014
*/
class InfoSolicitudCambioLineaPonController extends Controller implements TokenAuthenticatedController {
    
    /**
     * Funcion que carga la pantalla de Solicitud de Cambio de Linea pon
     * 
     * @author John Vera         <javera@telconet.ec>
     * @author Francisco Adum    <fdaum@telconet.ec>
     * @version 1.0 17-06-2014
     * @version 1.1 modificado:21-06-2014
    */
    public function solicitudCambioLineaPonAction() 
    {   
        $rolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_250-1497'))
        {
            $rolesPermitidos[] = 'ROLE_250-1497'; //Aprobar y rechazar solicitudes
        }
        if (true === $this->get('security.context')->isGranted('ROLE_250-1498'))
        {
            $rolesPermitidos[] = 'ROLE_250-1498'; //crear solicitudes
        }
        
        return $this->render('tecnicoBundle:InfoSolicitudCambioLineaPon:index.html.twig', array(
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * Funcion que carga los datos que solicitaron en el filtro de busqueda
     * 
     * @author John Vera         <javera@telconet.ec>
     * @author Francisco Adum    <fdaum@telconet.ec>
     * @version 1.0 17-06-2014
     * @version 1.1 modificado:21-06-2014
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 16-02-2018  Se modifica el envío de parámetros para agregar el prefijo de la empresa para permitir el cambio de línea pon
     *                          para los servicios Internet Small Business en la empresa TN
     * 
    */
    public function getConsultaAction() 
    {
        ini_set('max_execution_time', 400000);
        $objResponse            = new JsonResponse();
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $objSession             = $this->get('session');
        $objRequest             = $this->get('request');
        $intStart               = $objRequest->get('start');
        $intLimit               = $objRequest->get('limit');
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strLogin               = $objRequest->get('login');
        $strEstadoSolicitud     = $objRequest->get('estado');
        $strFechaDesde          = $objRequest->get('fechaDesde');
        $strFechaHasta          = $objRequest->get('fechaHasta');
        $arrayPtoCliente        = $objSession->get('ptoCliente');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');

        if(!empty($arrayPtoCliente['id']))
        {
            $objPunto   = $emComercial->getRepository('schemaBundle:InfoPunto')->findOneById($arrayPtoCliente['id']);
            if(is_object($objPunto))
            {
                $strLogin   = $objPunto->getLogin();
            }
        }
        $intIdTipoSolicitud = 0;
        $objTipoSolicitud   = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                          ->findOneBy(array("descripcionSolicitud" => 'SOLICITUD CAMBIO LINEA PON',
                                                            "estado" => "Activo"));
        if(is_object($objTipoSolicitud))
        {
            $intIdTipoSolicitud = $objTipoSolicitud->getId();
        }
        
        $intIdProdCaracInternetPerfil = 0;
        //obtencion del perfil del cliente
        $objCaractPerfil    = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                          ->findOneBy(array("descripcionCaracteristica" => "PERFIL",
                                                            "estado"                    => "Activo"));
        
        if(is_object($objCaractPerfil))
        {
            $objProductoInternet = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                               ->findOneBy(array(   "nombreTecnico" => "INTERNET",
                                                                    "empresaCod"    => $strCodEmpresa,
                                                                    "estado"        => "Activo"));
            if(is_object($objProductoInternet))
            {
                $objProdCaracInternetPerfil = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                          ->findOneBy(array("productoId"        => $objProductoInternet->getId(),
                                                                            "caracteristicaId"  => $objCaractPerfil->getId(),
                                                                            "estado"            => "Activo"));
                if(is_object($objProdCaracInternetPerfil))
                {
                    $intIdProdCaracInternetPerfil = $objProdCaracInternetPerfil->getId();
                }
            }
        }
        $objJson    = $emComercial->getRepository('schemaBundle:InfoServicio')
                                  ->generarJsonConsultaSolicitudesPon(array("strCodEmpresa"                 => $strCodEmpresa, 
                                                                            "strLogin"                      => $strLogin, 
                                                                            "strEstadoSolicitud"            => $strEstadoSolicitud, 
                                                                            "strFechaDesde"                 => $strFechaDesde, 
                                                                            "strFechaHasta"                 => $strFechaHasta, 
                                                                            "emComercial"                   => $emComercial, 
                                                                            "intStart"                      => $intStart, 
                                                                            "intLimit"                      => $intLimit,
                                                                            "strPrefijoEmpresa"             => $strPrefijoEmpresa,
                                                                            "intIdTipoSolicitud"            => $intIdTipoSolicitud,
                                                                            "intIdProdCaracInternetPerfil"  => $intIdProdCaracInternetPerfil));
        $objResponse->setContent($objJson);

        return $objResponse;
    }
    
    /**
     * Funcion que crea la solicitud de cambio de línea pon
     * 
     * @author John Vera         <javera@telconet.ec>
     * @author Francisco Adum    <fdaum@telconet.ec>
     * @version 1.0 17-06-2014
     * @version 1.1 modificado:21-06-2014
    */
    public function crearSolicitudAction() 
    {
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        $em         = $this->get('doctrine')->getManager('telconet');
        $emSoporte = $this->get('doctrine')->getManager('telconet_soporte');
        $session    = $this->get('session');
        $peticion   = $this->get('request');
        $idServicio = $peticion->get('idServicio');
        $motivo     = $peticion->get('motivo');
        $observacion= $peticion->get('observacion');
        $caso       = $peticion->get('caso');
        $user = $session->get('user');
        $host = $peticion->getClientIp();
        
        
        $objServicio = $em->getRepository('schemaBundle:InfoServicio')
                          ->find($idServicio);
        $objPunto = $em->getRepository('schemaBundle:InfoPunto')
                          ->find($objServicio->getPuntoId());
        $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                ->findOneBy(array("descripcionSolicitud"=>"SOLICITUD CAMBIO LINEA PON",
                                                  "estado"              =>"Activo"));
        $objMotivo = $em->getRepository('schemaBundle:AdmiMotivo')
                          ->find($motivo);

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $em->getConnection()->beginTransaction();
        $emSoporte->getConnection()->beginTransaction();
        
        //*----------------------------------------------------------------------*/
        
        //*LOGICA DE NEGOCIO-----------------------------------------------------*/
        try {
            //inserto en la tabla InfoDetalleSolicitud
            $InfoDetalleSolicitud = new InfoDetalleSolicitud();
            $InfoDetalleSolicitud->setServicioId($objServicio);
            $InfoDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud); //tipo de solicitud de linea Pon
            $InfoDetalleSolicitud->setMotivoId($motivo);
            $InfoDetalleSolicitud->setObservacion($observacion);
            $InfoDetalleSolicitud->setUsrCreacion($user);
            $InfoDetalleSolicitud->setFeCreacion(new \DateTime('now'));
            $InfoDetalleSolicitud->setEstado('Pendiente');
            $em->persist($InfoDetalleSolicitud);

            //se realiza el insert en la tabla de historicos INFO_DETALLE_SOL_HIST
            $InfoDetalleSolHist = new InfoDetalleSolHist();
            $InfoDetalleSolHist->setDetalleSolicitudId($InfoDetalleSolicitud);
            $InfoDetalleSolHist->setMotivoId($motivo);
            $InfoDetalleSolHist->setObservacion($observacion);
            $InfoDetalleSolHist->setUsrCreacion($user);
            $InfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $InfoDetalleSolHist->setEstado('Pendiente');
            $em->persist($InfoDetalleSolHist);
            $em->flush();

            //*----------------------------------------------------------------------*/

            //notificacion de la creacion de solicitud de cambio de linea Pon
            $envioPlantilla1 = $this->get('soporte.EnvioPlantilla');      
            $asunto = "Creacion de solicitud de cambio de línea Pon del cliente " . $objPunto->getLogin();
            $parametrosSolicitud = array('login' => $objPunto->getLogin(), 'motivo' => $objMotivo->getNombreMotivo());
            $envioPlantilla1->generarEnvioPlantilla($asunto, '', 'CSCLP', $parametrosSolicitud, '', '', '');
            
            if (trim($caso)) {
                
                $objCaso = $emSoporte->getRepository('schemaBundle:InfoCaso')
                                   ->find($caso);
                
                $objTarea = $emSoporte->getRepository('schemaBundle:AdmiTarea')
                                   ->findOneByNombreTarea('Cambiar linea PON');
                
                $serviceSoporte = $this->get('soporte.SoporteService');
                /* @var $serviceSoporte SoporteService */
                $parametros = array('observacion' => $observacion);   
                     
                $resultado = $serviceSoporte->crearTarea($objTarea, $objCaso, $InfoDetalleSolicitud, $peticion, $parametros);
                
                if ($resultado != 'OK'){
                    throw new Exception($resultado);
                }
                
            }
        } catch(Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            if($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->getConnection()->rollback();
            }
            $respuesta->setContent("Error: " . $e->getMessage() . ", <br> Favor Notificar a Sistemas.");
            return $respuesta;
        }
        //*DECLARACION DE COMMITS*/
        if($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
        }
        //*DECLARACION DE COMMITS*/
        if($emSoporte->getConnection()->isTransactionActive())
        {
            $emSoporte->getConnection()->commit();
        }
   
        $respuesta->setContent("OK");
        return $respuesta;

    }//fin de funcion crearSolicitudAction
    
    /**
     * Funcion que aprueba la solicitud de cambio de linea pon
     * 
     * @author John Vera         <javera@telconet.ec>
     * @author Francisco Adum    <fdaum@telconet.ec>
     * @version 1.0 17-06-2014
     * @version 1.1 modificado:21-06-2014
    */
    public function aprobarSolicitudAction() 
    {
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        $em         = $this->get('doctrine')->getManager('telconet');
        $session    = $this->get('session');
        $peticion   = $this->get('request');
        $idSolicitud = $peticion->get('idSolicitud');
        $observacion= $peticion->get('observacion');
        
        $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                           ->findOneById($idSolicitud);
             
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $em->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        //*LOGICA DE NEGOCIO-----------------------------------------------------*/
        try 
        {
            //se actualiza en la tabla InfoDetalleSolicitud
            $objSolicitud-> setEstado('Aprobada');
            $em->persist($objSolicitud);
            $em->flush();

            //se realiza el insert en la tabla de historicos INFO_DETALLE_SOL_HIST
            $InfoDetalleSolHist = new InfoDetalleSolHist();
            $InfoDetalleSolHist-> setDetalleSolicitudId($objSolicitud);
            $InfoDetalleSolHist-> setObservacion($observacion);
            $InfoDetalleSolHist-> setUsrCreacion($session->get('user'));
            $InfoDetalleSolHist-> setFeCreacion(new \DateTime('now'));
            $InfoDetalleSolHist-> setEstado('Aprobada');
            $em->persist($InfoDetalleSolHist);
            $em->flush();            
        } 
        catch(\Exception $e)
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            $respuesta->setContent("Error: ".$e.", <br> Favor Notificar a Sistemas");
            return $respuesta;
        }
        //*----------------------------------------------------------------------*/
        
        //*DECLARACION DE COMMITS*/
        if ($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
        }
        //*----------------------------------------------------------------------*/
        
        $respuesta->setContent("OK");
        return $respuesta;
    }//fin de la funcion aprobarSolicitudAction
    
    /**
     * Funcion que rechaza la solicitud de cambio de linea pon
     * 
     * @author John Vera         <javera@telconet.ec>
     * @author Francisco Adum    <fdaum@telconet.ec>
     * @version 1.0 17-06-2014
     * @version 1.1 modificado:21-06-2014
    */
    public function rechazarSolicitudAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->get('doctrine')->getManager('telconet');
        $emSoporte = $this->get('doctrine')->getManager('telconet_soporte');
        $session = $this->get('session');
        $peticion = $this->get('request');
        $idSolicitud = $peticion->get('idSolicitud');
        $observacion = $peticion->get('observacion');
        $caso = $peticion->get('caso');

        $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->findOneById($idSolicitud);

        
                 //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $em->getConnection()->beginTransaction();
        
        //*LOGICA DE NEGOCIO-----------------------------------------------------*/
        try
        {
            //*----------------------------------------------------------------------*/
            //se actualiza en la tabla InfoDetalleSolicitud
            $objSolicitud->setEstado('Rechazada');
            $objSolicitud->setUsrRechazo($session->get('user'));
            $objSolicitud->setFeRechazo(new \DateTime('now'));
            $em->persist($objSolicitud);
            $em->flush();

            //se realiza el insert en la tabla de historicos INFO_DETALLE_SOL_HIST
            $InfoDetalleSolHist = new InfoDetalleSolHist();
            $InfoDetalleSolHist->setDetalleSolicitudId($objSolicitud);
            $InfoDetalleSolHist->setObservacion($observacion);
            $InfoDetalleSolHist->setUsrCreacion($session->get('user'));
            $InfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
            $InfoDetalleSolHist->setEstado('Rechazada');
            $em->persist($InfoDetalleSolHist);
            $em->flush();

            if(trim($caso))
            {

                $objInfoDet = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                    ->findOneByDetalleSolicitudId($idSolicitud);

                $objCaso = $emSoporte->getRepository('schemaBundle:InfoCaso')
                    ->find($caso);

                $serviceSoporte = $this->get('soporte.SoporteService');
                
                $parametros = array('observacion' => $observacion);
                /* @var $serviceSoporte SoporteService */
                $resultado = $serviceSoporte->rechazarTarea($objInfoDet, $objCaso, 'N', $peticion, $parametros);
                
                if($resultado != 'OK')
                {
                    throw new Exception($resultado);
                }
            }
        }
        catch(Exception $e)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            if($emSoporte->getConnection()->isTransactionActive())
            {
                $emSoporte->getConnection()->rollback();
            }
            $respuesta->setContent("Error: " . $e->getMessage() . ", <br> Favor Notificar a Sistemas.");
            return $respuesta;
        }
        //*DECLARACION DE COMMITS*/
        if($em->getConnection()->isTransactionActive())
        {
            $em->getConnection()->commit();
        }
        //*DECLARACION DE COMMITS*/
        if($emSoporte->getConnection()->isTransactionActive())
        {
            $emSoporte->getConnection()->commit();
        }

        $respuesta->setContent("OK");
        return $respuesta;
    }

//fin de la funcion rechazarSolicitudAction

    /**
     * Funcion que crea un json de todas las solicitudes de linea pon que ha tenido un servicio.
     * 
     * 
     * @author John Vera         <javera@telconet.ec>
     * @author Francisco Adum    <fdaum@telconet.ec>
     * @version 1.0 17-06-2014
     * @version 1.1 modificado:21-06-2014
    */
    public function getSolicitudesAction() 
    {
        $arr_encontrados = array();
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        $em         = $this->getDoctrine()->getManager('telconet');
        $peticion   = $this->get('request');

        $idServicio = $peticion->get('idServicio');
        
        $objTipoSolicitud =  $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                  ->findOneBy(array("descripcionSolicitud" =>'SOLICITUD CAMBIO LINEA PON',
                                                    "estado" =>"Activo"));
                       
        $objDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                  ->findBy(array("servicioId"    =>$idServicio,
                                                 "tipoSolicitudId" =>$objTipoSolicitud->getId()));
        
        foreach ($objDetalleSolicitud as $detalleSolicitud)
        {
            $objMotivo = $em->getRepository('schemaBundle:AdmiMotivo')
                            ->findOneById($detalleSolicitud->getMotivoId());

            $arr_encontrados[]=array('idSolicitud'  => $detalleSolicitud->getId(),
                                     'motivo'       => $objMotivo->getNombreMotivo(),
                                     'observacion'  => $detalleSolicitud->getObservacion(),
                                     'fechaCrea'    => date_format($detalleSolicitud->getFeCreacion(), 'Y-m-d H:i:s'),
                                     'usuarioCrea'  => $detalleSolicitud->getUsrCreacion(),
                                     'estado'       => $detalleSolicitud->getEstado()
                                    );
        }
        
        $num = count($arr_encontrados);
        $data=json_encode($arr_encontrados);
        $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';
        $respuesta->setContent($resultado);

        return $respuesta;
    }//fin de la funcion getSolicitudesAction
    
    /**
     * Funcion que genera el json para el historial de cada solicitud de
     * cambio de linea pon.
     * 
     * @author John Vera         <javera@telconet.ec>
     * @author Francisco Adum    <fdaum@telconet.ec>
     * @version 1.0 17-06-2014
     * @version 1.1 modificado:21-06-2014
    */
    public function getHistorialSolicitudAction() 
    {
        $arr_encontrados = array();
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        $em         = $this->getDoctrine()->getManager('telconet');
        $peticion   = $this->get('request');

        $idSolicitud = $peticion->get('idSolicitud');
        
        //InfoDetalleSolicitud
        $objDetalleSolHist = $em->getRepository('schemaBundle:InfoDetalleSolHist')
                                  ->findByDetalleSolicitudId($idSolicitud);
        
        foreach ($objDetalleSolHist as $detalleSolHist)
        {
            $arr_encontrados[]=array( 'observacion'  => $detalleSolHist->getObservacion(),
                                      'fechaCrea'    => date_format($detalleSolHist->getFeCreacion(), 'Y-m-d H:i:s'),
                                      'usuarioCrea'  => $detalleSolHist->getUsrCreacion(),
                                      'estado'       => $detalleSolHist->getEstado()
                                     );
        }
        
        $num = count($arr_encontrados);
        $data=json_encode($arr_encontrados);
        $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';
        $respuesta->setContent($resultado);

        return $respuesta;
    }//fin de la funcion getHistorialSolicitudAction
    
    
    /**
     * Función que genera el json de los casos segun el login del cliente
     * 
     * @author John Vera         <javera@telconet.ec>
     * @version 1.0 12-08-2014
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 22-02-2018 Se agrega parámetro que determina si es un servicio Internet Small Business
     * 
     */
    public function getCasosPorUsuarioAction()
    {
        $objResponse    = new JsonResponse();
        $objRequest     = $this->get('request');
        $objSession     = $this->get('session');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $strLogin       = $objRequest->get('login');
        $strCodEmpresa  = $objSession->get('idEmpresa');
        $strEsIsb       = $objRequest->get('esIsb');

        $mixCasos = $emComercial->getRepository('schemaBundle:InfoCaso')
                                ->getCasoPorLogin(array("strLogin"      => $strLogin,
                                                        "strCodEmpresa" => $strCodEmpresa,
                                                        "strEsIsb"      => $strEsIsb));

        $intTotal       = count($mixCasos);

        $strCasos       = json_encode($mixCasos);
        $strResultado   = '{"total":"' . $intTotal . '","encontrados":' . $strCasos . '}';
        $objResponse->setContent($strResultado);

        return $objResponse;
    }

}
