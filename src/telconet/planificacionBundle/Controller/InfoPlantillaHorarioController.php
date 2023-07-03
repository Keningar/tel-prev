<?php

namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use telconet\schemaBundle\Entity\AdmiPlantillaHorarioCab;
use telconet\schemaBundle\Entity\AdmiPlantillaHorarioDet;
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

class InfoPlantillaHorarioController extends Controller
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
        $arrayRoles[] = 'ROLE_405-1';
            
        return $this->render('planificacionBundle:InfoPlantillaHorario:index.html.twig',
                                array('rolesPermitidos'   => $arrayRoles));

    }

    /**
     * Documentación para el método 'ajaxGetEncontradosAction'.
     *
     * Metodo utilizado para obtener información de las Plantillas de Horario
     * 
     * @param Object  $objRespuesta con informacion de la Plantilla.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 04-12-2017
     * 
     */
    public function ajaxGetEncontradosAction()
    {
        $objRespuesta = new JsonResponse();
        $objSession   = $this->get('session');
        $objPeticion  = $this->get('request');
        
        $arrayParametros =  array();
        
        $arrayParametros['START']       = $objPeticion->query->get('start');
        $arrayParametros['LIMIT']       = $objPeticion->query->get('limit');
        $arrayParametros["empresaCod"]  = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");        
        $arrayParametros["descripcion"] = $objPeticion->query->get('descripcion');
        $arrayParametros["esDefault"]   = "";
        $arrayParametros["estado"]      = $objPeticion->query->get('estado');
        
        if ($objPeticion->query->get('fechaDesde'))
        {
            $arrayFechaDesde = explode("T", $objPeticion->query->get('fechaDesde'));
            $arrayFechaDesde = date_create($arrayFechaDesde[0]);
            $arrayParametros["fechaDesde"] = date_format($arrayFechaDesde, 'Y-m-d');            
        }
        if ($objPeticion->query->get('fechaHasta'))
        {
            $arrayFechaHasta = explode("T", $objPeticion->query->get('fechaHasta'));
            $arrayDateHasta = date_create($arrayFechaHasta[0] + " 23:59:59");
            $arrayParametros["fechaHasta"] = date_format($arrayDateHasta, 'Y-m-d H:i:s');
        }
        
        

        $strJson = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('schemaBundle:AdmiPlantillaHorarioCab')
                        ->generarJsonPlantillaHorario($arrayParametros);
        
        $objRespuesta->setContent($strJson);
        
        return $objRespuesta;
    }  

    /**
    * Documentación para el método 'newAction'.
    *
    * Metodo utilizado para direccionar al js de creacion de Plantilla de Horario.
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>          
    * @version 1.0 04-12-2017
    */
    public function newAction()
    {
        return $this->render('planificacionBundle:InfoPlantillaHorario:new.html.twig',
                             array( 'strHoraInicio' => trim($this->container->getParameter('planificacion.hora.inicio')),
                                    'strHoraFin'    => trim($this->container->getParameter('planificacion.hora.fin')),
                                    'intIntervalo'  => trim($this->container->getParameter('planificacion.hora.intervalo')),
                                    'strMinimoHoras' => trim($this->container->getParameter('planificacion.plantilla.minimohoras')),
                                    'intIntervaloMaximo' => trim($this->container->getParameter('planificacion.plantilla.intervalomaximo'))
                                   ));
    }

    /**
     * Documentación para el método 'ajaxGetDetallePlantillaAction'.
     *
     * Metodo utilizado para obtener información del detalle de Plantillas de Horario
     * 
     * @param Object  $objRespuesta con informacion de la Plantilla.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 04-12-2017
     * 
     */
    public function ajaxGetDetallePlantillaAction()
    {
        $objRespuesta = new JsonResponse();
        
        $objRequest       = $this->getRequest();
        $intPlantillaHorarioCab       = $objRequest->query->get('intHorarioCabeceraId');
        
        $strJson = $this->getDoctrine()
                        ->getManager()
                        ->getRepository('schemaBundle:AdmiPlantillaHorarioDet')
                        ->generarJsonDetalleHorario($intPlantillaHorarioCab);
        
        $objRespuesta->setContent($strJson);
        
        return $objRespuesta;   

    }

    /**
     * Documentación para el método 'savePlantillaAction'.
     *
     * Metodo utilizado para guardar nuevas plantillas de horarios
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 04-12-2017
     * 
     */

    public function savePlantillaAjaxAction()
    {
        $emComercial      = $this->getDoctrine()->getManager("telconet");
        $objResponse       = new JsonResponse();
        
        $objRequest       = $this->getRequest();
        $objSession       = $this->getRequest()->getSession();
        
        $intIdPlantilla   = ($objRequest->get('intIdPlantilla') ? $objRequest->get('intIdPlantilla') : 0);
        $intJurisdiccion  = $objRequest->get('intJurisdiccion');
        $intCupoWeb       = $objRequest->get('intCupoWeb');
        $intCupoMobile    = $objRequest->get('intCupoMobile');
        $intCupoTotal     = $objRequest->get('intCupoTotal');
        $strCodEmpresa    = ($objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : "");
        $strDescripcion   = $objRequest->get('objTxtDescripcion');
        $objPlantillaDet  = json_decode($objRequest->get('jsonPlantillaDet'));
        $arrayEliminados  = explode(",", $objRequest->get('strDetalleEliminiado'));
        
        $objPlantillas = $this->getDoctrine()
                 ->getManager()
                 ->getRepository('schemaBundle:AdmiPlantillaHorarioCab')
                 ->findAll();
        
        $intRegistro = 0;
        if ($objPlantillas)
        {
            $intRegistro = count($objPlantillas);
        }

        try
        {  
            //Valido que no exista la plantilla
            if ($intIdPlantilla == 0)
            {
                $entityPlantillaCab = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('schemaBundle:AdmiPlantillaHorarioCab')
                     ->findOneBy(array('empresaCod' => $strCodEmpresa, 'descripcion' => $strDescripcion));     
                if (isset($entityPlantillaCab) && count($entityPlantillaCab) > 0 && $intIdPlantilla == 0)
                {
                    $arrayResult['strStatus']        = 'ERROR';
                    $arrayResult['strMessageStatus'] = 'Plantila <span style="color:blue"><b>'. $strDescripcion . '</b></span> ya Existe! <br>'
                        . 'Cambie el nombre de la Plantilla para Grabar!';
                    $objResponse->setData($arrayResult);
                    return $objResponse;  
                } 
            }
            else
            {
                $entityPlantillaCab = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('schemaBundle:AdmiPlantillaHorarioCab')
                     ->find($intIdPlantilla); 
            }            
            $emComercial->getConnection()->beginTransaction();
            if ($intIdPlantilla == 0)
            {
                $entityPlantillaCab = new AdmiPlantillaHorarioCab();
                $entityPlantillaCab->setFeCreacion(new \DateTime('now'));
                $entityPlantillaCab->setUsrCreacion($objSession->get('user'));
                $entityPlantillaCab->setIpCreacion($objRequest->getClientIp());
                $entityPlantillaCab->setFeUltGeneracion(new \DateTime('now'));
            }
            $entityPlantillaCab->setEmpresaCod($strCodEmpresa); 
            $entityPlantillaCab->setDescripcion($strDescripcion);
            $entityPlantillaCab->setEsDefault("");
            $entityPlantillaCab->setEstado('Activo');
            $entityPlantillaCab->setJurisdiccionId($intJurisdiccion);
            $entityPlantillaCab->setCupoWeb($intCupoWeb);
            $entityPlantillaCab->setCupoMobile($intCupoMobile);
            $entityPlantillaCab->setCupoTotal($intCupoTotal);
            
            $emComercial->persist($entityPlantillaCab);
            $emComercial->flush();

            foreach($objPlantillaDet->arrayData as $objDet)
            {
                $strAlmuerzo = $objDet->almuerzo == 'true' ? 'S' : 'N';
                $arrayFechaInicio = date_create(date('Y-m-d H:i',strtotime($objDet->horaInicio)));
                $arrayFechaFin = date_create(date('Y-m-d H:i',strtotime($objDet->horaFin)));
                //seguir aqui
                $entityPlantillaDet = $emComercial->getRepository('schemaBundle:AdmiPlantillaHorarioDet')
                                                  ->find($objDet->idPlantillaHorarioDet);
                if ($objDet->idPlantillaHorarioDet == 0)
                {
                    $entityPlantillaDet = new AdmiPlantillaHorarioDet();
                    $entityPlantillaDet->setFeCreacion(new \DateTime('now'));
                    $entityPlantillaDet->setUsrCreacion($objSession->get('user'));
                    $entityPlantillaDet->setIpCreacion($objRequest->getClientIp());                               
                    $entityPlantillaDet->setPlantillaHorarioId($entityPlantillaCab);
                    
                }
                $entityPlantillaDet->setHoraInicio($arrayFechaInicio);
                $entityPlantillaDet->setHoraFin($arrayFechaFin);
                $entityPlantillaDet->setAlmuerzo($strAlmuerzo);
                $entityPlantillaDet->setCupoWeb($objDet->cupoWeb);
                $entityPlantillaDet->setCupoMobile($objDet->cupoMobile);
                
                $emComercial->persist($entityPlantillaDet);
                $emComercial->flush();                            
            }
            //elimino los registros traidos en el array
            if ($intIdPlantilla > 0)
            {
                foreach ($arrayEliminados as $intIdEliminar) 
                {
                    $entityDetalleEliminar = $emComercial->getRepository('schemaBundle:AdmiPlantillaHorarioDet')
                                      ->find($intIdEliminar);
                    if ($entityDetalleEliminar != null)
                    {
                        $emComercial->remove($entityDetalleEliminar);
                        $emComercial->flush();                                                   
                    }
                }                
            }
            $emComercial->getConnection()->commit();
            $arrayResult['strStatus']        = 'OK';
            $arrayResult['strMessageStatus'] = 'Plantilla Creada Correctamente';            
            if ($intIdPlantilla > 0)
            {
                $arrayResult['strMessageStatus'] = 'Plantilla Editada Correctamente';
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
     * Documentación para el método 'editAction'.
     *
     * Metodo utilizado para modificar plantillas de horarios
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 04-12-2017
     * 
     */
    
    public function editAction($intIdPlantilla)
    {
        $objCabecera = $this->getDoctrine()
          ->getManager()
          ->getRepository('schemaBundle:AdmiPlantillaHorarioCab')
          ->find($intIdPlantilla);
        
        $objJurisdiccion = $this->getDoctrine()
          ->getManager()
          ->getRepository('schemaBundle:AdmiJurisdiccion')
          ->find($objCabecera->getJurisdiccionId());
        
        return $this->render('planificacionBundle:InfoPlantillaHorario:edit.html.twig',
                             array( 'strHoraInicio' => trim($this->container->getParameter('planificacion.hora.inicio')),
                                    'strHoraFin'    => trim($this->container->getParameter('planificacion.hora.fin')),
                                    'intIntervalo'  => trim($this->container->getParameter('planificacion.hora.intervalo')),
                                    'strDescripcion' => trim($objCabecera->getDescripcion()) ,
                                    'intJurisdiccionId' => $objCabecera->getJurisdiccionId(),
                                    'strNombreJurisdiccion' => $objJurisdiccion->getNombreJurisdiccion(),
                                    'boolDefault' => $objCabecera->getEsDefault(),
                                    'intHorarioCabeceraId' => $intIdPlantilla,
                                    'strMinimoHoras' => trim($this->container->getParameter('planificacion.plantilla.minimohoras')),
                                    'intIntervaloMaximo' => trim($this->container->getParameter('planificacion.plantilla.intervalomaximo')),
                                    'intCuposTelcos' => $objCabecera->getCupoWeb(),
                                    'intCuposMobile' => $objCabecera->getCupoMobile(),
                                    'intCuposTotal' => $objCabecera->getCupoTotal()
                                   ));
    }
    
    /**
     * Documentación para el método 'savePlantillaAction'.
     *
     * Metodo utilizado para mostrar información individual de plantillas de horarios
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 04-12-2017
     * 
     */
    
    public function showAction($intIdPlantilla)
    {
        $objCabecera = $this->getDoctrine()
          ->getManager()
          ->getRepository('schemaBundle:AdmiPlantillaHorarioCab')
          ->find($intIdPlantilla);
        $objJurisdiccion = $this->getDoctrine()
          ->getManager()
          ->getRepository('schemaBundle:AdmiJurisdiccion')
          ->find($objCabecera->getJurisdiccionId());
        return $this->render('planificacionBundle:InfoPlantillaHorario:show.html.twig',
                             array( 'strHoraInicio' => trim($this->container->getParameter('planificacion.hora.inicio')),
                                    'strHoraFin'    => trim($this->container->getParameter('planificacion.hora.fin')),
                                    'intIntervalo'  => trim($this->container->getParameter('planificacion.hora.intervalo')),
                                    'strDescripcion' => trim($objCabecera->getDescripcion()) ,
                                    'boolDefault' => $objCabecera->getEsDefault(),
                                    'intHorarioCabeceraId' => $intIdPlantilla,
                                    'strJurisdiccion' => $objJurisdiccion->getNombreJurisdiccion(),
                                    'strMinimoHoras' => trim($this->container->getParameter('planificacion.plantilla.minimohoras')),
                                    'intIntervaloMaximo' => trim($this->container->getParameter('planificacion.plantilla.intervalomaximo'))
                                   ));
    } 
    
    /**
     * Documentación para el método 'generarAction'.
     *
     * Metodo utilizado para generar cupos por horario por un periodo determinado
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 04-06-2017
     * 
     */
    
    public function generarAction($intIdPlantilla)
    {
        $objCabecera = $this->getDoctrine()
          ->getManager()
          ->getRepository('schemaBundle:AdmiPlantillaHorarioCab')
          ->find($intIdPlantilla);
        
        $objFecha       = $objCabecera->getFeUltGeneracion();
        $strFecha       = $objFecha->format('Y-m-d');   


        $strFecha       = date("Y-m-d", strtotime($strFecha . "+ 1 days"));
        $objFecha       = new \DateTime($strFecha);        
        
        return $this->render('planificacionBundle:InfoPlantillaHorario:generar.html.twig',
                             array( 'strHoraInicio' => trim($this->container->getParameter('planificacion.hora.inicio')),
                                    'strHoraFin'    => trim($this->container->getParameter('planificacion.hora.fin')),
                                    'intIntervalo'  => trim($this->container->getParameter('planificacion.hora.intervalo')),
                                    'strDescripcion' => trim($objCabecera->getDescripcion()) ,
                                    'strFechaMinima' => $objFecha->format('Y-m-d') ,
                                    'boolDefault' => $objCabecera->getEsDefault(),
                                    'intHorarioCabeceraId' => $intIdPlantilla,
                                    'strMinimoHoras' => trim($this->container->getParameter('planificacion.plantilla.minimohoras')),
                                    'intIntervaloMaximo' => trim($this->container->getParameter('planificacion.plantilla.intervalomaximo'))
                                   ));
    }
    
}
