<?php

namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use telconet\schemaBundle\Entity\InfoAgendaCupoCab;
use telconet\schemaBundle\Entity\InfoAgendaCupoDet;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Documentación para la clase 'InfoPlantillaHorario'.
 *
 * Clase utilizada para manejar metodos que permiten realizar acciones de administracion de Plantilla de Horarios
 *
 * @author Edgar Pin Villavicencio <epin@telconet.ec>          
 * @version 1.0 04-12-2017
 */

class AgendaCupoController extends Controller
{   
   /**
    * Documentación para el método 'indexAction'.
    *
    * Metodo utilizado para retornar a la pagina principal de la administracion de Plantilla de Horarios
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>          
    * @version 1.0 04-12-2017
    */
    public function indexAction()
    {
        $arrayRoles = array();
        $arrayRoles[] = 'ROLE_406-1';
            
        return $this->render('planificacionBundle:AgendaCupo:index.html.twig',
                                array('rolesPermitidos'   => $arrayRoles));
    }
    
   /**
    * Documentación para el método 'gridAction'.
    *
    * Metodo utilizado para mostrar el grid de la administracion de Plantilla de Horarios
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>          
    * @version 1.0 05-06-2018
    */    
    public function gridAction()
    {
        $serviceUtil = $this->get('schema.Util');
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion        = $this->get('request');
        $objSession         = $this->get( 'session' );

        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $strUsrCreacion     = $objSession->get('user');
        $strIpClient        = $objPeticion->getClientIp();


        $arrayFechaDesde = explode('T',$objPeticion->query->get('fechaDesde'));
        $arrayFechaHasta = explode('T',$objPeticion->query->get('fechaHasta'));
        $emComercial      = $this->get('doctrine')->getManager('telconet');

        $arrayDatosBusqueda    = array();
        $arrayDatosBusqueda['strFechaDesde'] = $arrayFechaDesde[0];
        $arrayDatosBusqueda['strFechaHasta'] = $arrayFechaHasta[0];
        $arrayDatosBusqueda['strDescripcion']    = $objPeticion->query->get('descripcion');
        $arrayDatosBusqueda['intJurisdiccionId'] = $objPeticion->query->get('jurisdiccion');
               
        try
        {
            $objJson = $this->getDoctrine()->getManager("telconet")
                                           ->getRepository('schemaBundle:InfoAgendaCupoCab')
                                           ->generarJsonAgendaCupo($arrayDatosBusqueda);
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'CoordinarController->gridAction', $ex->getMessage(), $strUsrCreacion, $strIpClient);
        }
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }    
    
   /**
    * Documentación para el método 'editAction'.
    *
    * Metodo utilizado para editar la administracion de Plantilla de Horarios
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>          
    * @version 1.0 05-06-2018
    */    
    public function editAction($intIdCupo)
    {
        $objCabecera = $this->getDoctrine()
          ->getManager()
          ->getRepository('schemaBundle:InfoAgendaCupoCab')
          ->find($intIdCupo);
        
        
        
        return $this->render('planificacionBundle:AgendaCupo:edit.html.twig',
                             array( 'strHoraInicio' => trim($this->container->getParameter('planificacion.hora.inicio')),
                                    'strHoraFin'    => trim($this->container->getParameter('planificacion.hora.fin')),
                                    'intIntervalo'  => trim($this->container->getParameter('planificacion.hora.intervalo')),
                                    'strDescripcion' => $objCabecera->getPlantillaHorarioId()->getDescripcion(),
                                    'strJurisdiccion' => $objCabecera->getJurisdiccionId()->getNombreJurisdiccion(),
                                    'strFechaPeriodo' => $objCabecera->getFechaPeriodo()->format("d/m/Y"),
                                    'intCupoTotal' => $objCabecera->getTotalCupos(),
                                    'boolDefault' => 'N',
                                    'intHorarioCabeceraId' => $intIdCupo,
                                    'strMinimoHoras' => trim($this->container->getParameter('planificacion.plantilla.minimohoras')),
                                    'intIntervaloMaximo' => trim($this->container->getParameter('planificacion.plantilla.intervalomaximo'))
                                   ));
    }
    
   /**
    * Documentación para el método 'detalleCupoAjaxAction'.
    *
    * Metodo utilizado para mostrar el detalle de una agenda de planificación
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>          
    * @version 1.0 05-06-2018
    */        
     public function detalleCupoAjaxAction()
    {
        $objRespuesta = new JsonResponse();
        
        $objRequest       = $this->getRequest();
        $intPlantillaHorarioCab       = $objRequest->query->get('agendaCupoId');
        $strJson = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('schemaBundle:InfoAgendaCupoDet')
                        ->generarJsonAgendaCupo($intPlantillaHorarioCab);
        
        $objRespuesta->setContent($strJson);
        
        return $objRespuesta;   
    }
    
   /**
    * Documentación para el método 'gridAction'.
    *
    * Metodo utilizado para generar los cupos para la planificación de instalación
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>          
    * @version 1.0 05-06-2018
    */        
    public function generarCuposAjaxAction()
    {
        $emComercial      = $this->getDoctrine()->getManager("telconet");
        $objResponse       = new JsonResponse();
        
        $objRequest       = $this->getRequest();
        $objSession       = $this->getRequest()->getSession();
        
        $strFechaDesde    = $objRequest->get('strFechaDesde');
        $strFechaHasta    = $objRequest->get('strFechaHasta');
        $intPlantilla     = $objRequest->get('intPlantilla');
        $intTotalCupos     = $objRequest->get('intTotalCupos');
        $strCodEmpresa    = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        
        
                
        $arrayFechaDesde       = explode("T", $strFechaDesde);
        $arrayFD           = explode("-", $arrayFechaDesde[0]);
        $strFechaDesde = date("Y/m/d H:i", strtotime($arrayFD[2] . "-" . $arrayFD[1] . "-" . $arrayFD[0] . " " . $arrayFechaDesde[1]));

        $arrayFechaHasta  = explode("T", $strFechaHasta);
        $arrayFD          = explode("-", $arrayFechaHasta[0]);
        $strFechaHasta    = date("Y/m/d H:i", strtotime($arrayFD[2] . "-" . $arrayFD[1] . "-" . $arrayFD[0] . " " . $arrayFechaHasta[1]));        

        $objPlantillaDet  = json_decode($objRequest->get('jsonPlantillaDet'));

        try
        {  
            $emComercial->getConnection()->beginTransaction();
            $entityPlantillaCab = $emComercial
                 ->getRepository('schemaBundle:AdmiPlantillaHorarioCab')
                 ->find($intPlantilla); 
            $intJurisdiccion = $entityPlantillaCab->getJurisdiccionId();
            $entityJurisdiccion = $emComercial
                 ->getRepository('schemaBundle:AdmiJurisdiccion')
                 ->find($intJurisdiccion);             
            
            //Creo bucle para las fechas
            $objFechaDesde = new \DateTime($strFechaDesde);
            $objFechaHasta = new \DateTime($strFechaHasta);

            while($objFechaDesde->format("Y-m-d") <= $objFechaHasta->format("Y-m-d"))
            {            
                $entityAgendaCab = $emComercial
                     ->getRepository('schemaBundle:InfoAgendaCupoCab')
                     ->findOneBy(array('empresaCod' => $strCodEmpresa, 
                                       'fechaPeriodo' => $objFechaDesde, 
                                       'plantillaHorarioId' => $intPlantilla));
                if (!(isset($entityAgendaCab) && count($entityAgendaCab) == 0) )
                {
                    $entityAgendaCab = new InfoAgendaCupoCab();
                    $entityAgendaCab->setFeCreacion(new \DateTime('now'));
                    $entityAgendaCab->setUsrCreacion($objSession->get('user'));
                    $entityAgendaCab->setIpCreacion($objRequest->getClientIp());
                }
                $entityAgendaCab->setEmpresaCod($strCodEmpresa); 
                $entityAgendaCab->setFechaPeriodo($objFechaDesde);
                $entityAgendaCab->setTotalCupos($intTotalCupos);
                $entityAgendaCab->setJurisdiccionId($entityJurisdiccion);
                $entityAgendaCab->setPlantillaHorarioId($entityPlantillaCab);
                $entityAgendaCab->setEstadoRegistro("Activo");

                $emComercial->persist($entityAgendaCab);
                $emComercial->flush();
                
                $entityPlantillaCab->setFeUltGeneracion($objFechaHasta);
                $emComercial->persist($entityPlantillaCab);
                $emComercial->flush();
                
                
                /* @var $objPlantillaDet type */
                foreach($objPlantillaDet->arrayData as $objDet)
                 {
                     $arrayHoraInicio = explode("T", $objDet->horaInicio);
                     $strFechaHoraDesde = $objFechaDesde->format("Y-m-d") . " " . $arrayHoraInicio[1];
                     $arrayHoraFin = explode("T", $objDet->horaFin);
                     $strFechaHoraHasta = $objFechaDesde->format("Y-m-d") . " " . $arrayHoraFin[1];
                     
                     $entityAgendaDet = $emComercial->getRepository('schemaBundle:InfoAgendaCupoDet')
                                                       ->find($objDet->idPlantillaHorarioDet);
                     if (!(isset($entityAgendaDet) && count($entityAgendaDet)== 0) )
                     {
                         $entityAgendaDet = new InfoAgendaCupoDet();
                         $entityAgendaDet->setFeCreacion(new \DateTime('now'));
                         $entityAgendaDet->setUsrCreacion($objSession->get('user'));
                         $entityAgendaDet->setIpCreacion($objRequest->getClientIp());                               
                         $entityAgendaDet->setAgendaCupoId($entityAgendaCab);
                     }
                     $entityAgendaDet->setHoraDesde(new \DateTime($strFechaHoraDesde));
                     $entityAgendaDet->setHoraHasta(new \DateTime($strFechaHoraHasta));
                     $entityAgendaDet->setCuposWeb($objDet->cupoWeb);
                     $entityAgendaDet->setCuposMovil($objDet->cupoMobile);
                     $entityAgendaDet->setTotalCupos($objDet->cupoWeb + $objDet->cupoMobile);
                     $entityAgendaDet->setObservacion($objDet->observacion);
                     $entityAgendaDet->setEstadoRegistro('Activo');
                     $emComercial->persist($entityAgendaDet);
                     $emComercial->flush();
                }
                 //elimino los registros traidos en el array
                $objFechaDesde->add(new \DateInterval('P1D'));
            }    

            //Genero los cupos acorde a la coordinacion de agenda
            $emComercial->getConnection()->beginTransaction();
            $arrayParametros = array("strFechaDesde" => $strFechaDesde,
                                    "strFechaHasta" => $strFechaHasta,
                                    "intAgendaId" => $entityAgendaCab->getId(),
                                    "intJurisdiccionId" => $intJurisdiccion,
                                    "emComercial" => $emComercial
                );
            $arrayRespuesta = $emComercial
                 ->getRepository('schemaBundle:InfoAgendaCupoCab')    
                 ->generaCuposPorPeriodo($arrayParametros);                

            
            $emComercial->getConnection()->commit();            
            if ($arrayRespuesta["codigo"] == 0)
            {
                $arrayResult['strStatus']        = 'OK';
                $arrayResult['strMessageStatus'] = 'Agenda Generada Correctamente';            
            }
            else
            {
                $arrayResult['strStatus']        = 'Error';
                $arrayResult['strMessageStatus'] = $arrayRespuesta["mensaje"];
            }

        }
        catch (DBALException $ex) 
        {
            $emComercial->getConnection()->rollback();
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'No se pudo Grabar la data!';
        }
        catch (Exception $ex) 
        {
            $emComercial->getConnection()->rollback();
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'No se pudo Grabar la data!';
        }
        
        $objResponse->setData($arrayResult);
        return $objResponse;     
        
    }

   /**
    * Documentación para el método 'gridAction'.
    *
    * Metodo utilizado para generar cupos adicionales en casos especificos
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>          
    * @version 1.0 05-06-2018
    */        
    public function generarCuposAdicionalesAjaxAction()
    {
        $emComercial      = $this->getDoctrine()->getManager("telconet");
        $objResponse       = new JsonResponse();
        
        $objRequest       = $this->getRequest();
        $objSession       = $this->getRequest()->getSession();
        
        $strFechaPeriodo = $objRequest->get('strFechaPeriodo');

        $objPlantillaDet  = json_decode($objRequest->get('jsonPlantillaDet'));
        
        try
        {  
            /* @var $objPlantillaDet type */
            $emComercial->getConnection()->beginTransaction();

            foreach($objPlantillaDet->arrayData as $objDet)
            {
                $arrayHoraInicio = explode("T", $objDet->horaDesde);
                $strFechaHoraDesde = $strFechaPeriodo . " " . $arrayHoraInicio[1];
                $arrayHoraFin = explode("T", $objDet->horaHasta);
                $strFechaHoraHasta = $strFechaPeriodo . " " . $arrayHoraFin[1];

                $boolNuevo = false;
                $entityAgendaDet = $emComercial->getRepository('schemaBundle:InfoAgendaCupoDet')
                                                   ->find($objDet->idAgendaCupoDet);
                $entityAgendaCab = $emComercial->getRepository('schemaBundle:InfoAgendaCupoCab')
                                              ->find($objDet->agendaCupoId);                 
                if (!(isset($entityAgendaDet) && count($entityAgendaDet)> 0) )
                {
                    $arrayFecha       = explode(" ",$strFechaHoraDesde);
                    $arrayF           = explode("/", $arrayFecha[0]);                            
                    $arrayFechaInicio = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));
                    $arrayFecha       = explode(" ",$strFechaHoraHasta);
                    $arrayF           = explode("/", $arrayFecha[0]);                            
                    $arrayFechaFin = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));
                    $boolNuevo = true;  
                    $entityAgendaDet = new InfoAgendaCupoDet();
                    $entityAgendaDet->setFeCreacion(new \DateTime('now'));
                    $entityAgendaDet->setUsrCreacion($objSession->get('user'));
                    $entityAgendaDet->setIpCreacion($objRequest->getClientIp());                               
                    $entityAgendaDet->setAgendaCupoId($entityAgendaCab);
                    $intCupos = $objDet->cuposWeb + $objDet->cuposMovil;
                    $intCupoWeb = $objDet->cuposWeb;
                    $intCupoMobile = $objDet->cuposMovil;
                  
                }
                else
                {
                    $intCupoWeb =  $objDet->cuposWeb;
                    $intCupoMobile = $objDet->cuposMovil;                    
                    $arrayFecha       = explode(" ",$strFechaHoraDesde);
                    $arrayF           = explode("/", $arrayFecha[0]);                            
                    $arrayFechaInicio = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));
                    $arrayFecha       = explode(" ",$strFechaHoraHasta);
                    $arrayF           = explode("/", $arrayFecha[0]);                            
                    $arrayFechaFin = date("Y/m/d H:i", strtotime($arrayF[2] . "-" . $arrayF[1] . "-" . $arrayF[0] . " " . $arrayFecha[1]));
                    $intCupos = ($entityAgendaDet->getCuposWeb() + $entityAgendaDet->getCuposMovil()) -
                                   ($objDet->cuposWeb + $objDet->cuposMovil);   
                    
                    /* @var $intCupos type */
                    if ($intCupos >= 0)
                    {
                        $intCont = 0;
                        for ($intCont = 1; $intCont <= $intCupos; $intCont++) 
                        {
                            $arrayParametrosRango['strFeInicio']       = $arrayFechaInicio;
                            $arrayParametrosRango['strFeFin']          = $arrayFechaFin;
                            $arrayParametrosRango['intJurisdiccionId'] = $entityAgendaCab->getJurisdiccionId()->getId();
                            $arrayRangos                               = $emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                 ->getRangoFecha($arrayParametrosRango);   
                            foreach($arrayRangos as $arrayRango)
                            {
                                $entityCupoPlanificacion = $emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                        ->find($arrayRango['id']);
                                $emComercial->remove($entityCupoPlanificacion);
                                $emComercial->flush();
                            }
                        }
                        $arrayResult['strStatus']        = 'OK';
                        $arrayResult['strMessageStatus'] = 'Agenda Generada Correctamente';            

                    }
                    else
                    {

                        //agrego cupos
                       /* $arrayParametros = array("strFechaDesde" => $strFechaHoraDesde,
                                                "strFechaHasta" => $strFechaHoraHasta,
                                                "cupoTotal" => $objDet->cuposWeb + $objDet->cuposMovil,
                                                "cupo" => $intCupos,
                                                "intJurisdiccionId" => $entityAgendaCab->getJurisdiccionId()->getId() 
                                );
                        $arrayRespuesta = $emComercial
                                 ->getRepository('schemaBundle:InfoAgendaCupoCab')    
                                 ->generaCuposAdicional($arrayParametros);*/
                        $boolNuevo = true;
                        if ($arrayRespuesta["codigo"] == 0)
                        {
                            $arrayResult['strStatus']        = 'OK';
                            $arrayResult['strMessageStatus'] = 'Agenda Generada Correctamente';            
                        }
                        else
                        {
                            $arrayResult['strStatus']        = 'Error';
                            $arrayResult['strMessageStatus'] = $arrayRespuesta["mensaje"];
                        }                     
                        
                    }
                    
                        
                }
                $strFechaHoraDesde = $arrayFechaInicio;
                $strFechaHoraHasta = $arrayFechaFin;
                $entityAgendaDet->setHoraDesde(new \DateTime($strFechaHoraDesde));
                $entityAgendaDet->setHoraHasta(new \DateTime($strFechaHoraHasta));
                $entityAgendaDet->setCuposWeb($intCupoWeb);
                $entityAgendaDet->setCuposMovil($intCupoMobile);
                $entityAgendaDet->setTotalCupos($intCupos);
                $entityAgendaDet->setObservacion($objDet->observacion);
                $entityAgendaDet->setEstadoRegistro('Activo');
                $emComercial->persist($entityAgendaDet);
                $emComercial->flush();   
                if ($boolNuevo)
                {
                    
                    $arrayParametros = array("strFechaDesde" => $strFechaHoraDesde,
                                            "strFechaHasta" => $strFechaHoraHasta,
                                            "cupoTotal" => $objDet->cuposWeb + $objDet->cuposMovil,
                                            "cupo" => $objDet->cuposWeb + $objDet->cuposMovil,
                                            "intJurisdiccionId" => $entityAgendaCab->getJurisdiccionId()->getId()
                        );
                    $arrayRespuesta = $emComercial
                             ->getRepository('schemaBundle:InfoAgendaCupoCab')    
                             ->generaCuposAdicional($arrayParametros);
                    if ($arrayRespuesta["codigo"] == 0)
                    {
                        $arrayResult['strStatus']        = 'OK';
                        $arrayResult['strMessageStatus'] = 'Agenda Generada Correctamente';            
                    }
                    else
                    {

                        $arrayResult['strStatus']        = 'Error';
                        $arrayResult['strMessageStatus'] = $arrayRespuesta["mensaje"];
                    }   
                }
            }

        }
        catch (DBALException $ex) 
        {
            $emComercial->getConnection()->rollback();
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'No se pudo Grabar la data!';
            error_log("por dbal " . print_r($arrayResult, 1));
        }
        catch (Exception $ex) 
        {
            $emComercial->getConnection()->rollback();
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'No se pudo Grabar la data!';
            error_log("por catch " . print_r($arrayResult, 1));
        }
        $emComercial->getConnection()->commit();   
        $objResponse->setData($arrayResult);
        return $objResponse;     
        
    }    
    
    public function showAction($intIdCupo)
    {
        $objCabecera = $this->getDoctrine()
          ->getManager()
          ->getRepository('schemaBundle:InfoAgendaCupoCab')
          ->find($intIdCupo);
        
        
        
        return $this->render('planificacionBundle:AgendaCupo:show.html.twig',
                             array( 'strHoraInicio' => trim($this->container->getParameter('planificacion.hora.inicio')),
                                    'strHoraFin'    => trim($this->container->getParameter('planificacion.hora.fin')),
                                    'intIntervalo'  => trim($this->container->getParameter('planificacion.hora.intervalo')),
                                    'strDescripcion' => $objCabecera->getPlantillaHorarioId()->getDescripcion(),
                                    'strJurisdiccion' => $objCabecera->getJurisdiccionId()->getNombreJurisdiccion(),
                                    'strFechaPeriodo' => $objCabecera->getFechaPeriodo()->format("Y-m-d"),
                                    'intCupoTotal' => $objCabecera->getTotalCupos(),
                                    'boolDefault' => 'N',
                                    'intHorarioCabeceraId' => $intIdCupo,
                                    'strMinimoHoras' => trim($this->container->getParameter('planificacion.plantilla.minimohoras')),
                                    'intIntervaloMaximo' => trim($this->container->getParameter('planificacion.plantilla.intervalomaximo'))
                                   ));
    
    }
}
