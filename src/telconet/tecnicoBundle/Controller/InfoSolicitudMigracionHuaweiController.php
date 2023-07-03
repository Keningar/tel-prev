<?php
/*
 * To change this template, choose Tools | Templates and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud; 
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
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
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Clase para crear la Solicitud de Migracion Huawei Manual.
 * 
 * @author Edgar Holguin     <eholguin@telconet.ec>
 * @author Francisco Adum    <fdaum@telconet.ec>
 * @version 1.0 17-06-2014
 * @version 1.1 modificado:25-03-2015
*/
class InfoSolicitudMigracionHuaweiController extends Controller implements TokenAuthenticatedController {
    
    /**
     * Funcion que carga la pantalla de Solicitud de Migracion Huawei
     * 
     * @author Edgar Holguin     <eholguin@telconet.ec>
     * @version 1.0 modificado:25-03-2015
    */
    public function indexAction() 
    {   
        $rolesPermitidos = array();
        
        if (true === $this->get('security.context')->isGranted('ROLE_283-2437'))
        {
            $rolesPermitidos[] = 'ROLE_283-2437'; //crear solicitudes de migracion huawei 
        }
        if (true === $this->get('security.context')->isGranted('ROLE_283-2577'))
        {
            $rolesPermitidos[] = 'ROLE_283-2577'; //anular solicitudes de migracion huawei 
        }
        
        return $this->render('tecnicoBundle:InfoSolicitudMigracionHuawei:index.html.twig', array(
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
     * Funcion que carga los datos que solicitaron en el filtro de busqueda
     * 
     * @author Edgar Holguin     <eholguin@telconet.ec>
     * @version 1.0 modificado:25-03-2015
    */
    public function getConsultaAction() 
    {
        ini_set('max_execution_time', 400000);
        $respuesta  = new Response();
        $respuesta  ->headers->set('Content-Type', 'text/json');
        $em         = $this->getDoctrine()->getManager('telconet');
        $session    = $this->get('session');
        $peticion   = $this->get('request');
        $start      = $peticion->get('start');
        $limit      = $peticion->get('limit');
        $idEmpresa  = $session->get('idEmpresa');
        $login      = $peticion->get('login');
        $estado     = $peticion->get('estado');
        $fechaDesde = $peticion->get('fechaDesde');
        $fechaHasta = $peticion->get('fechaHasta');
        $ptoCliente_sesion = $session->get('ptoCliente');

        if($ptoCliente_sesion){
            $puntoId=$ptoCliente_sesion['id'];
            $objPunto = $this->getDoctrine()->getManager("telconet")
                             ->getRepository('schemaBundle:InfoPunto')
                             ->findOneById($ptoCliente_sesion['id']);
            $login= $objPunto->getLogin(); 
        }
        
        $objJson = $this->getDoctrine()->getManager("telconet")
                        ->getRepository('schemaBundle:InfoServicio')
                        ->generarJsonConsultaSolicitudesMigracionHuawei($idEmpresa, $login, $estado, $fechaDesde, $fechaHasta, $em, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
     * Funcion que crea la solicitud de migracion huawei
     * 
     * @author Edgar Holguin     <eholguin@telconet.ec>
     * @version 1.0 modificado:25-03-2015
     * @version 1.1 modificado John Vera <javera@telconet.ec>
     * @version 1.2 modificado:27-07-2015 John Vera <javera@telconet.ec>
     * @version 1.3 modificado:11-05-2016 Jesus Bozada <jbozada@telconet.ec>
     * 
     * @Secure(roles="ROLE_283-2437")
    */
    public function crearSolicitudAction() 
    {
        $respuesta             = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em                    = $this->get('doctrine')->getManager('telconet');
        $emSoporte             = $this->get('doctrine')->getManager('telconet_soporte');
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $session               = $this->get('session');
        $peticion              = $this->get('request');
        $idServicio            = $peticion->get('idServicio');
        $motivo                = $peticion->get('motivo');
        $observacion           = $peticion->get('observacion');
        $idSplitterAnterior    = $peticion->get('splitterPrimario');
        $idSplitterNuevo       = $peticion->get('splitterNuevo');
        $user                  = $session->get('user');
        $host                  = $peticion->getClientIp();
        
   
        $objServicio      = $em->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $objPunto         = $em->getRepository('schemaBundle:InfoPunto')->find($objServicio->getPuntoId());
        $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneBy(array("descripcionSolicitud" => "SOLICITUD MIGRACION",
                                                                                                  "estado"               => "Activo")
                                              );
        $objMotivo              = $em->getRepository('schemaBundle:AdmiMotivo')->find($motivo);
        $objInfoServicioTecnico = $em->getRepository('schemaBundle:InfoServicioTecnico')->findOneBy(array("servicioId"=>$idServicio)); 
           
        if($idSplitterNuevo !='')
        {
            $objSplitterNuevo = $em->getRepository('schemaBundle:InfoElemento')
                                   ->find($idSplitterNuevo);
            if($objSplitterNuevo)
            {
                $idSplitterAnterior = $objSplitterNuevo->getRefElementoId();
            }               
        }
        else
        {
            $objSplitterNuevo = $em->getRepository('schemaBundle:InfoElemento')
                                   ->findOneByRefElementoId($idSplitterAnterior);
            if($objSplitterNuevo)
            {
                $idSplitterNuevo = $objSplitterNuevo->getId();
            }      
        }
        
        $objInterfaceNuevo = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findOneBy(array("elementoId" => $idSplitterNuevo,
                                                                                                       "estado"     => "not connect"));
        if (!$objInterfaceNuevo)
        {                   
            $respuesta->setContent('{"status":"ERROR","mensaje":"No existen puertos disponibles en el elemento"}');
            return $respuesta;
        }

        //obtengo la caja del elemento
        $objRelacionElemento  = $emInfraestructura->getRepository('schemaBundle:InfoRelacionElemento')
                                                 ->findOneBy(array('elementoIdB'=> $idSplitterAnterior));
        $idElementoContenedor = '';
        if($objRelacionElemento)
        {
            $objElementoContenedor = $em->getRepository('schemaBundle:InfoElemento')
                                        ->find($objRelacionElemento->getElementoIdA());
            $idElementoContenedor = $objElementoContenedor->getId();
        }       
        //Obtengo interfaz del splitter anterior
        $objInterfaceElemento = $em->getRepository('schemaBundle:InfoInterfaceElemento')->findOneBy(array("elementoId" => $idSplitterAnterior,
                                                                                                          "estado"     => "deleted"));
      
      
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $em->getConnection()->beginTransaction();
        $emSoporte->getConnection()->beginTransaction();
        
        //*----------------------------------------------------------------------*/
        
        //*LOGICA DE NEGOCIO-----------------------------------------------------*/
        try 
        {
            $detallesExistentes = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                     ->getSolicitudesMigracionHuawei($idServicio);                      
	    
	    if($detallesExistentes)
	    {
	        $respuesta->setContent('{"status":"EXISTE","mensaje":"Ya existe una solicitud Creada"}');
	    }        
            else
            {
		// Modificar Informacion tecncica del servico
		if($objInfoServicioTecnico)
                {
		    $objInfoServicioTecnico->setElementoConectorId($idSplitterAnterior);
		    $objInfoServicioTecnico->setInterfaceElementoConectorId($objInterfaceElemento->getId());
                    $objInfoServicioTecnico->setElementoContenedorId($idElementoContenedor);
		    $em->persist($objInfoServicioTecnico);
		}           
		//inserto en la tabla InfoDetalleSolicitud
		$InfoDetalleSolicitud = new InfoDetalleSolicitud();
		$InfoDetalleSolicitud->setServicioId($objServicio);
		$InfoDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud); //tipo de solicitud de Migracion manual huawei
		$InfoDetalleSolicitud->setMotivoId($motivo);
		$InfoDetalleSolicitud->setObservacion('Solicitud manual: '.$observacion);
		$InfoDetalleSolicitud->setUsrCreacion($user);
		$InfoDetalleSolicitud->setUsrCreacion($user);
		$InfoDetalleSolicitud->setFeCreacion(new \DateTime('now'));
		if($objServicio->getEstado()=='Activo')
                {
		    $InfoDetalleSolicitud->setEstado('PrePlanificada');
                }
		else if($objServicio->getEstado()=='In-Corte')
                {
		    $InfoDetalleSolicitud->setEstado('In-Corte');
                }
		$em->persist($InfoDetalleSolicitud);

		//se realiza el insert en la tabla de historicos INFO_DETALLE_SOL_HIST
		$InfoDetalleSolHist = new InfoDetalleSolHist();
		$InfoDetalleSolHist->setDetalleSolicitudId($InfoDetalleSolicitud);
		$InfoDetalleSolHist->setMotivoId($motivo);
		$InfoDetalleSolHist->setObservacion('Solicitud manual: '.$observacion);
		$InfoDetalleSolHist->setUsrCreacion($user);
		$InfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
		if($objServicio->getEstado()=='Activo')
                {
		    $InfoDetalleSolHist->setEstado('PrePlanificada');
                }
		else if($objServicio->getEstado()=='In-Corte')
                {
		    $InfoDetalleSolHist->setEstado('In-Corte');
                }
		$em->persist($InfoDetalleSolHist);
        
                if($objInterfaceNuevo)
                {
                    $objInterfaceNuevo->setEstado('reserved');
                    $objInterfaceNuevo->setUsrCreacion($user);
                    $objInterfaceNuevo->setFeCreacion(new \DateTime('now'));

                    $em->persist($objInterfaceNuevo);

                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $entityServicioHist = new InfoServicioHistorial();
                    $entityServicioHist->setServicioId($objServicio);
                    $entityServicioHist->setIpCreacion($peticion->getClientIp());
                    $entityServicioHist->setFeCreacion(new \DateTime('now'));
                    $entityServicioHist->setUsrCreacion($session->get('user'));
                    $entityServicioHist->setEstado($objServicio->getEstado());
                    //se agrega codigo para llevar rastro de la ip asignada por el sistema Telcos
                    $entityServicioHist->setObservacion('Se cambio a estado reserved la interface : ' . $objInterfaceNuevo->getNombreInterfaceElemento().
                                                        ' , Splitter : '. $objSplitterNuevo->getNombreElemento());

                    $em->persist($entityServicioHist);
                }
        
		$em->flush();
		
		$respuesta->setContent('{"status":"OK","mensaje":"Se creó la solicitud correctamente."}');
            
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
            $mensaje = $e->getMessage() . ", <br> Favor Notificar a Sistemas.";
            $respuesta->setContent('{"status":"ERROR","mensaje":"'.$mensaje.'"}');          
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
           
        return $respuesta;

    }//fin de funcion crearSolicitudAction
    

    /**
     * Funcion que crea un json de todas las solicitudes de migracion huawei manual con servicios activos.
     * 
     * 
     * @author Edgar Holguin     <eholguin@telconet.ec>
     * 
     * @version 1.0 modificado:25-03-2015
     * @version 1.1 modificado:19-06-2015 John Vera
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
                                  ->findOneBy(array("descripcionSolicitud" =>'SOLICITUD MIGRACION',
                                                    "estado" =>"Activo"));
                       
        $objDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                  ->findBy(array("servicioId"    =>$idServicio,
                                                 "tipoSolicitudId" =>$objTipoSolicitud->getId()));
        
        foreach ($objDetalleSolicitud as $detalleSolicitud)
        {
            $objMotivo = $em->getRepository('schemaBundle:AdmiMotivo')
                            ->findOneById($detalleSolicitud->getMotivoId());
            //Consulto la caracteristica SPLITTER
            $objCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array('descripcionCaracteristica'=>'SPLITTER L2', 'estado'=>'Activo'));
            //Consulto el splitter del que proviene el cliente
            $objDetalleCaract = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                   ->findOneBy(array('detalleSolicitudId' => $detalleSolicitud->getId(), 
                                                     'caracteristicaId' => $objCaracteristica->getId()));
            $strNombreSplitterAnterior = '';
            if ($objDetalleCaract)
            {
                $objElemento = $em->getRepository('schemaBundle:InfoElemento')->findOneById($objDetalleCaract->getValor());
                $strNombreSplitterAnterior = $objElemento->getNombreElemento();
            }

            $arr_encontrados[]=array('idSolicitud'  => $detalleSolicitud->getId(),
                                     'motivo'       => $objMotivo->getNombreMotivo(),
                                     'observacion'  => $detalleSolicitud->getObservacion(),
                                     'splitterAnt'  => $strNombreSplitterAnterior,
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
     * migracion huawei manual.
     * 
     * @author Edgar Holguin     <eholguin@telconet.ec>
     * @version 1.0 modificado:25-03-2015
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
    
    /**anularSolicitudAjax
     * Funcion que anula la solicitud de migracion
     * 
     * @author John Vera     <javera@telconet.ec>
     * @version 1.0 24-04-2015
     * @Secure(roles="ROLE_283-2577")
    */
    public function anularSolicitudAjaxAction()
    {
        $respuesta      = new Response();
        $respuesta      ->headers->set('Content-Type', 'text/json');
        $em             = $this->get('doctrine')->getManager('telconet');
        $session        = $this->get('session');
        $peticion       = $this->get('request');
        $idSolicitud    = $peticion->get('idSolicitud');
        $observacion    = $peticion->get('observacion');
        $user           = $session->get('user');

        $objSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($idSolicitud);

        //se actualiza en la tabla InfoDetalleSolicitud
        $objSolicitud->setEstado('Anulada');
        $objSolicitud->setUsrRechazo($user);
        $objSolicitud->setFeRechazo(new \DateTime('now'));
        $em->persist($objSolicitud);
        $em->flush();

        //se realiza el insert en la tabla de historicos INFO_DETALLE_SOL_HIST
        $InfoDetalleSolHist = new InfoDetalleSolHist();
        $InfoDetalleSolHist->setDetalleSolicitudId($objSolicitud);
        $InfoDetalleSolHist->setObservacion($observacion);
        $InfoDetalleSolHist->setUsrCreacion($user);
        $InfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
        $InfoDetalleSolHist->setEstado('Anulada');
        $em->persist($InfoDetalleSolHist);
        $em->flush();

        $respuesta->setContent("OK");
        return $respuesta;
    }

}
