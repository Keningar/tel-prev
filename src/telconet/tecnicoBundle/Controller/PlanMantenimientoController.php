<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiProceso;
use telconet\schemaBundle\Entity\AdmiProcesoEmpresa;
use telconet\schemaBundle\Entity\AdmiTarea;
use telconet\schemaBundle\Entity\InfoMantenimientoTarea;
use telconet\schemaBundle\Form\PlanMantenimientoType;
use telconet\schemaBundle\Form\MantenimientoType;


use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class PlanMantenimientoController extends Controller
{ 
    /**
     * @Secure(roles="ROLE_343-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redirección a la pantalla principal de la administracion de los Planes de Mantenimiento
     * @return render.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-01-2015
     *
     */
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        
        //MODULO 343 - PLANMANTENIMIENTO/CREAR
        if(true === $this->get('security.context')->isGranted('ROLE_343-3'))
        {
            $arrayRolesPermitidos[] = 'ROLE_343-3';
        }
        
        //MODULO 343 - PLANMANTENIMIENTO/CONSULTAR
        if (true === $this->get('security.context')->isGranted('ROLE_343-6'))
        {
            $arrayRolesPermitidos[] = 'ROLE_343-6';
        }
        //MODULO 343 - PLANMANTENIMIENTO/ELIMINAR
        if (true === $this->get('security.context')->isGranted('ROLE_343-8'))
        {
            $arrayRolesPermitidos[] = 'ROLE_343-8';
        }

        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("343", "1");

        return $this->render('tecnicoBundle:PlanMantenimiento:index.html.twig', array(
                'item'              => $entityItemMenu,
                'rolesPermitidos'   => $arrayRolesPermitidos
        ));
    }



    /**
     * @Secure(roles="ROLE_343-2")
     * 
     * Documentación para el método 'newAction'.
     * 
     * Muestra el formulario vacío para crear un nuevo plan de mantenimiento y las tareas asociadas.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-01-2016
     * 
     */ 
    public function newAction()
    {
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("343", "1");
        
        $emGeneral = $this->getDoctrine()->getManager("telconet_general"); 
        
        $strNombreParametroTiposFrecuencia = 'UNIDADES_MEDIDA_MANTENIMIENTOS_MOVILIZACION';
        $strNombreParametroFrecuencias     = 'FRECUENCIAS_MANTENIMIENTOS_MOVILIZACION';
        
        $tiposFrecuenciasMantenimiento = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getResultadoDetallesParametro($strNombreParametroTiposFrecuencia,"","");
        $registrosTiposFrecuenciasMantenimiento = $tiposFrecuenciasMantenimiento["registros"];
        
        $arrayTiposFrecuenciasMantenimiento = array(); 
        foreach ( $registrosTiposFrecuenciasMantenimiento as $tipoFrecuencia )
        {   
           $arrayTiposFrecuenciasMantenimiento[$tipoFrecuencia['valor2']] = $tipoFrecuencia['valor2'];
        }

        $frecuenciasMantenimiento = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getResultadoDetallesParametro($strNombreParametroFrecuencias, "kilometraje","");
        $registrosFrecuenciasMantenimiento = $frecuenciasMantenimiento["registros"];
        $arrayFrecuenciasMantenimiento = array(); 
        foreach ( $registrosFrecuenciasMantenimiento as $frecuencia )
        {   
           $arrayFrecuenciasMantenimiento[$frecuencia['valor2']] = $frecuencia['valor2'];
        }

        $form_mantenimientos                = $this->createForm(new MantenimientoType(
                                                                    array
                                                                        (
                                                                            'arrayFrecuenciasMantenimiento'     =>$arrayFrecuenciasMantenimiento,
                                                                            'arrayTiposFrecuenciasMantenimiento'=>$arrayTiposFrecuenciasMantenimiento
                                                                        )
                                                                ), new AdmiProceso());
        
        
        $entity         = new AdmiProceso();

        $form           = $this->createForm(new PlanMantenimientoType(), $entity);
        
        return $this->render('tecnicoBundle:PlanMantenimiento:new.html.twig', array(
                'item'      => $entityItemMenu,
                'entity'    => $entity,
                'form'      => $form->createView(),
            
                'form_mantenimientos'                   => $form_mantenimientos->createView(),
                'arrayTiposFrecuenciasMantenimiento'    => $arrayTiposFrecuenciasMantenimiento,
                'arrayFrecuenciasMantenimiento'         => $arrayFrecuenciasMantenimiento
        ));
    }
    
    
    public function getTareasMantenimientosAction()
    {
        $strNombreProcesoTareasMantenimiento    = 'PROCESO TAREAS MANTENIMIENTOS';

        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        
        $objRequest     = $this->get('request');
        $nombreTarea    = $objRequest->get('query') ? $objRequest->get('query') : "";
        $arrayParametros=array(
            "proceso"   => $strNombreProcesoTareasMantenimiento,
            "estado"    => 'Activo',
            "nombre"    => $nombreTarea
        );
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiTarea')
            ->generarJson($arrayParametros);
        $objResponse->setContent($objJson);
        
        return $objResponse;
    
    }
    
    
    public function getTareasMantenimientoAsociadoPlanAction()
    {
        $objRequest = $this->get('request');
        $idMantenimiento     = $objRequest->get('idMantenimiento') ? $objRequest->get('idMantenimiento') : "";
        
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        
        $arrayParametros=array(
            "idMantenimiento"   => $idMantenimiento
        );
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoMantenimientoTarea')
            ->getJSONTareasPorMantenimiento($arrayParametros);
        $objResponse->setContent($objJson);
        
        return $objResponse;
    
    }
    
    /**
     * @Secure(roles="ROLE_343-3")
     * 
     * Documentación para el método 'createAction'.
     * 
     * Guarda un plan de mantenimiento con sus respectivas tareas.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-01-2016
     * 
     */ 
    public function createAction()
    {
        $objRequest = $this->get('request');
        $session    = $objRequest->getSession();
        $codEmpresa = $session->get('idEmpresa');

        $numMantenimientosFinal     = $objRequest->get('numMantenimientosFinal') ? $objRequest->get('numMantenimientosFinal') : "";
        $datos_form_mantenimiento   = $objRequest->get('mantenimientotype');
        
        $strIndicesMantenimientos   = $objRequest->get('indicesMantenimientos') ? $objRequest->get('indicesMantenimientos') : "";
        $arrayIndicesMantenimientos = explode(",",$strIndicesMantenimientos);
        

        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $em_soporte     = $this->getDoctrine()->getManager('telconet_soporte');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("51", "1");
        
        $emGeneral = $this->getDoctrine()->getManager("telconet_general"); 
        
        $strNombreParametroTiposFrecuencia = 'UNIDADES_MEDIDA_MANTENIMIENTOS_MOVILIZACION';
        $strNombreParametroFrecuencias     = 'FRECUENCIAS_MANTENIMIENTOS_MOVILIZACION';
        
        $tiposFrecuenciasMantenimiento = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getResultadoDetallesParametro($strNombreParametroTiposFrecuencia, "","");
        $registrosTiposFrecuenciasMantenimiento=$tiposFrecuenciasMantenimiento["registros"];
        
        $arrayTiposFrecuenciasMantenimiento = array(); 
        foreach ( $registrosTiposFrecuenciasMantenimiento as $tipoFrecuencia )
        {   
           $arrayTiposFrecuenciasMantenimiento[$tipoFrecuencia['valor2']] = $tipoFrecuencia['valor2'];
        }

        $frecuenciasMantenimiento = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getResultadoDetallesParametro($strNombreParametroFrecuencias,"kilometraje","");
        $registrosFrecuenciasMantenimiento = $frecuenciasMantenimiento["registros"];
        
        $arrayFrecuenciasMantenimiento = array(); 
        foreach ( $registrosFrecuenciasMantenimiento as $frecuencia )
        {   
           $arrayFrecuenciasMantenimiento[$frecuencia['valor2']] = $frecuencia['valor2'];
        }

        $form_mantenimientos                = $this->createForm(new MantenimientoType(
                                                                    array
                                                                        (
                                                                            'arrayFrecuenciasMantenimiento'     =>$arrayFrecuenciasMantenimiento,
                                                                            'arrayTiposFrecuenciasMantenimiento'=>$arrayTiposFrecuenciasMantenimiento
                                                                        )
                                                                ), new AdmiProceso());
        
        
        $entityPlanMantenimiento    = new AdmiProceso();
        $form                       = $this->createForm(new PlanMantenimientoType(), $entityPlanMantenimiento);
        $form->bind($objRequest);
        
        if ($form->isValid()) 
        {
            $em_soporte->getConnection()->beginTransaction();	
            try 
            {
                
                /*************Plan Mantenimiento************/
                $entityPlanMantenimiento->setVisible('NO');
                $entityPlanMantenimiento->setAplicaEstado('N');
                $entityPlanMantenimiento->setEsPlanMantenimiento('S');
                $entityPlanMantenimiento->setEstado('Activo');
                $entityPlanMantenimiento->setFeCreacion(new \DateTime('now'));
                $entityPlanMantenimiento->setUsrCreacion($objRequest->getSession()->get('user'));
                $entityPlanMantenimiento->setFeUltMod(new \DateTime('now'));
                $entityPlanMantenimiento->setUsrUltMod($objRequest->getSession()->get('user'));
                $em_soporte->persist($entityPlanMantenimiento);
                $em_soporte->flush();	            	           


                $entityPlanMantenimientoEmp = new AdmiProcesoEmpresa();
                $entityPlanMantenimientoEmp->setEstado('Activo');
                $entityPlanMantenimientoEmp->setUsrCreacion($objRequest->getSession()->get('user'));
                $entityPlanMantenimientoEmp->setFeCreacion(new \DateTime('now'));
                $entityPlanMantenimientoEmp->setEmpresaCod($codEmpresa);
                $entityPlanMantenimientoEmp->setProcesoId($entityPlanMantenimiento);

                $em_soporte->persist($entityPlanMantenimientoEmp);
                $em_soporte->flush();
                
                $strNombrePlanMantenimiento=$entityPlanMantenimiento->getNombreProceso();
                
                /******************************************/
                
                
                /*************Mantenimientos de un Plan de Mantenimiento************/
                
                $arrayFrecuencias       = array();
                $arrayTiposFrecuencia   = array();
                foreach ($datos_form_mantenimiento as $key => $arrayAttr)
                {   
                    if($key=='frecuencias')
                    {
                        foreach ( $arrayAttr as $key_tipo => $value)
                        {                     
                            $arrayFrecuencias[$key_tipo]=$value;                
                        }
                    }
                    else if($key=="tiposFrecuencia")
                    {
                        foreach ( $arrayAttr as $key_tipo => $value)
                        {                     
                            $arrayTiposFrecuencia[$key_tipo]=$value;                
                        }
                    }
                }
                
                for($i = 0; $i < $numMantenimientosFinal; $i++)
                {
                    
                    $indiceMantenimiento=$arrayIndicesMantenimientos[$i];
                    
                    //Un Mantenimiento es un proceso con un proceso padre que es el plan de mantenimiento
                    $frecuencia                 = $arrayFrecuencias[$indiceMantenimiento];
                    $tipoFrecuencia             = $arrayTiposFrecuencia[$indiceMantenimiento];
                    $nombreMantenimiento        = $strNombrePlanMantenimiento." ".$frecuencia." ".$tipoFrecuencia;
                    $descripcionMantenimiento   = "Plan de Mantenimiento: ".$entityPlanMantenimiento->getNombreProceso();
                    $descripcionMantenimiento  .= " Frecuencia: ".$frecuencia;
                    $descripcionMantenimiento  .= " Tipo de Frecuencia: ".$tipoFrecuencia;
                    
                    $entityMantenimiento  = new AdmiProceso();
                    $entityMantenimiento->setNombreProceso($nombreMantenimiento);
                    $entityMantenimiento->setProcesoPadreId($entityPlanMantenimiento);
                    
                    $entityMantenimiento->setDescripcionProceso($descripcionMantenimiento);
                    $entityMantenimiento->setVisible('NO');
                    $entityMantenimiento->setAplicaEstado('N');
                    $entityMantenimiento->setEsPlanMantenimiento('N');
                    $entityMantenimiento->setEstado('Activo');
                    $entityMantenimiento->setFeCreacion(new \DateTime('now'));
                    $entityMantenimiento->setUsrCreacion($objRequest->getSession()->get('user'));
                    $entityMantenimiento->setFeUltMod(new \DateTime('now'));
                    $entityMantenimiento->setUsrUltMod($objRequest->getSession()->get('user'));
                    $em_soporte->persist($entityMantenimiento);
                    $em_soporte->flush();	            	           


                    $entityMantenimientoEmpresa = new AdmiProcesoEmpresa();
                    $entityMantenimientoEmpresa->setEstado('Activo');
                    $entityMantenimientoEmpresa->setUsrCreacion($objRequest->getSession()->get('user'));
                    $entityMantenimientoEmpresa->setFeCreacion(new \DateTime('now'));
                    $entityMantenimientoEmpresa->setEmpresaCod($codEmpresa);
                    $entityMantenimientoEmpresa->setProcesoId($entityMantenimiento);

                    $em_soporte->persist($entityMantenimientoEmpresa);
                    $em_soporte->flush();
                    
                    
                    
                    /***************Tareas de cada Mantenimiento**********************/
                    $jsonTareas     = json_decode($objRequest->get('tareas_escogidas_'.$indiceMantenimiento));
                    $arrayTareas    = $jsonTareas->tareas;
                    
                    if($arrayTareas && count($arrayTareas) > 0)                               
                    {
                        foreach($arrayTareas as $tarea)
                        {
                            $idTarea            = $tarea->idTarea;
                            $objTarea = $em_soporte->getRepository('schemaBundle:AdmiTarea')->find($idTarea);
                            
                            $entityMantenimientoTarea = new InfoMantenimientoTarea();
                            $entityMantenimientoTarea->setMantenimientoId($entityMantenimiento);
                            $entityMantenimientoTarea->setTareaId($objTarea);
                            $entityMantenimientoTarea->setFrecuencia($frecuencia);
                            $entityMantenimientoTarea->setTipoFrecuencia($tipoFrecuencia);
                            $entityMantenimientoTarea->setEstado('Activo');
                            $entityMantenimientoTarea->setUsrCreacion($session->get('user'));
                            $entityMantenimientoTarea->setFeCreacion(new \DateTime('now'));
                            $entityMantenimientoTarea->setIpCreacion($objRequest->getClientIp());
                            
                            $em_soporte->persist($entityMantenimientoTarea);
                            $em_soporte->flush();
                        }
                    }
                }     
                /***********Fin de Mantenimientos*************/
                
                $em_soporte->getConnection()->commit();
                $em_soporte->getConnection()->close();
                
                
                return $this->redirect($this->generateUrl('planmantenimiento_show', array('id' => $entityPlanMantenimiento->getId())));

            } 
            catch (Exception $e) 
            {
                $em_soporte->getConnection()->rollback();
                $em_soporte->getConnection()->close();
            }
        }

        
        return $this->render('tecnicoBundle:PlanMantenimiento:new.html.twig', array(
                'item'      => $entityItemMenu,
                'entity'    => $entityPlanMantenimiento,
                'form'      => $form->createView(),
            
                'form_mantenimientos'                   => $form_mantenimientos->createView(),
                'arrayTiposFrecuenciasMantenimiento'    => $arrayTiposFrecuenciasMantenimiento,
                'arrayFrecuenciasMantenimiento'         => $arrayFrecuenciasMantenimiento
        ));
    }


    /**
     * @Secure(roles="ROLE_343-6")
     * 
     * Documentación para el método 'showAction'.
     * 
     * Muestra un plan de Mantenimiento con sus tareas.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-01-2016
     * 
     */ 
    public function showAction($id)
    {
        $objRequest = $this->get('request');
        $rolesPermitidos = array();
        if (true === $this->get('security.context')->isGranted('ROLE_51-8'))
        {
            $rolesPermitidos[] = 'ROLE_51-8';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_51-9'))
        {
            $rolesPermitidos[] = 'ROLE_51-9';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_51-6'))
        {
            $rolesPermitidos[] = 'ROLE_51-6';
        }
        if (true === $this->get('security.context')->isGranted('ROLE_51-4'))
        {
            $rolesPermitidos[] = 'ROLE_51-4';
        }       
        $em_soporte     = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("51", "1");

        if (null == $proceso = $em_soporte->find('schemaBundle:AdmiProceso', $id)) {
            throw new NotFoundHttpException('No existe el Plan de Mantenimiento que se quiere mostrar');
        }

        $mantenimientos=$em_soporte->getRepository('schemaBundle:AdmiProceso')->findBy(array("procesoPadreId"=>$proceso));
        
        $strIdsMantenimientos='';
        foreach($mantenimientos as $mantenimiento)
        {
            $strIdsMantenimientos.=$mantenimiento->getId().",";
        }
        
        return $this->render('tecnicoBundle:PlanMantenimiento:show.html.twig', array(
                'item'                  => $entityItemMenu,
                'proceso'               => $proceso,
                'mantenimientos'        => $mantenimientos,
                'numMantenimientosPlan' => count($mantenimientos),
                'strIdsMantenimientos'  => $strIdsMantenimientos,
                'rolesPermitidos'       => $rolesPermitidos,
                'flag'                  => $objRequest->get('flag')
        ));
    }



    /**
     * @Secure(roles="ROLE_343-7")
     * 
     * Documentación para el método 'gridAction'.
     * 
     * Se visualizan los planes de mantenimientos .
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-01-2016
     * 
     */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $objRequest = $this->get('request');

        $session    = $objRequest->getSession();
        $codEmpresa = $session->get('idEmpresa');

        $queryNombre    = $objRequest->query->get('query') ? $objRequest->query->get('query') : "";
        $nombre         = ($queryNombre != '' ? $queryNombre : $objRequest->query->get('nombre'));
        $estado         = 'Activo';
        $start      = $objRequest->query->get('start');
        $limit      = $objRequest->query->get('limit');
        $visible    = $objRequest->query->get('visible');
        $parametros                         = array();
        $parametros["esPlanMantenimiento"]  = 'S';
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiProceso')
            ->generarJson($parametros, $nombre,$estado,$start,$limit,$codEmpresa,$visible);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * @Secure(roles="ROLE_343-19")
     * 
     * Documentación para el método 'getTareasPlanMantenimientoAction'.
     * 
     * Se obtiene todas las tareas asociadas a un plan de mantenimiento.
     * 
     * @param integer $id
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-01-2016
     * 
     */
    public function getTareasPlanMantenimientoAction($id)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $objRequest = $this->get('request');

        $start = $objRequest->query->get('start');
        $limit = $objRequest->query->get('limit');
        $estado= 'Activo';

        $objJson = $this->getDoctrine()
                        ->getManager("telconet_soporte")->getRepository('schemaBundle:AdmiTarea')
                        ->generarJsonTareasPorProceso($id,$start,$limit,$estado);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    
    /**
     * @Secure(roles="ROLE_343-4")
     * 
     * Documentación para el método 'actualizarTareaPlanAction'.
     * 
     * Actualiza la información de una tarea asociada a un plan de mantenimiento ya creado.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-01-2016
     * 
     */
    public function actualizarTareaPlanAction()
    {
        $em_soporte     = $this->getDoctrine()->getManager('telconet_soporte');
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');

        $objRequest             = $this->get('request');
        $session                = $objRequest->getSession();
        $intIdTarea             = $objRequest->get('idTarea');
        $intIdPlanMantenimiento = $objRequest->get('idPlanMantenimiento');
        $strNombreTarea         = $objRequest->get('nombreTarea');
        $strDescripcionTarea    = $objRequest->get('descripcionTarea');

        $em_soporte->getConnection()->beginTransaction();	
        try 
        {
            $objTareaPlan = $em_soporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTarea);

            if (!$objTareaPlan) 
            {
                throw $this->createNotFoundException('Unable to find AdmiTarea entity.');
            } 
            $objTareaPlan->setNombreTarea($strNombreTarea);
            $objTareaPlan->setDescripcionTarea($strDescripcionTarea);
            $objTareaPlan->setFeUltMod(new \DateTime('now'));
            $objTareaPlan->setUsrUltMod($session->get('user'));
            $em_soporte->persist($objTareaPlan);
            $em_soporte->flush();

            if ($em_soporte->getConnection()->isTransactionActive())
            {
                $em_soporte->getConnection()->commit();
                $em_soporte->getConnection()->close();
            }
            
            return $this->redirect($this->generateUrl('planmantenimiento_show', array('id'=>$intIdPlanMantenimiento))); 
        }
        catch (Exception $e) 
        {
            if ($em_soporte->getConnection()->isTransactionActive())
            {
                $em_soporte->getConnection()->rollback();
                $em_soporte->getConnection()->close();
            }

            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('planmantenimiento_show', array('id'=>$intIdPlanMantenimiento)));
        }


    }
    
    /**
     * @Secure(roles="ROLE_343-8")
     * 
     * Documentación para el método 'eliminarTareaPlanAction'.
     * 
     * Eliminar la o las tarea asociadas a un plan de mantenimiento ya creado.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 25-01-2016
     * 
     */
    public function eliminarTareaPlanAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest  = $this->get('request');
        $parametro   = $objRequest->get('id');

        /*
         * tipo=1 Eliminar una tarea por medio de su id
         * tipo=2 Eliminación Masiva de tareas
         * 
         */
        $tipo = $objRequest->get('tipo');

        if($parametro)
        {
            $arrayValor = explode("|",$parametro);
        }
        else
        {
            $parametro  = $objRequest->get('param');

            $arrayValor = explode("|",$parametro);
        } 

        $em_soporte             = $this->getDoctrine()->getManager('telconet_soporte');
        $objTareaIni            = $em_soporte->getRepository('schemaBundle:AdmiTarea')->find($arrayValor[0]);
        $idPlanMantenimiento    = $objTareaIni->getProcesoId()->getId();

        $estado             = 'Eliminado';
        $strMensajeError    = "";

        try
        {
            foreach($arrayValor as $id)
            {
                $objTarea = $em_soporte->getRepository('schemaBundle:AdmiTarea')->find($id);
                if( $objTarea )
                {
                    $em_soporte->getConnection()->beginTransaction();
                    try
                    { 
                        $objTarea->setEstado($estado);
                        $objTarea->setFeUltMod(new \DateTime('now'));
                        $objTarea->setUsrUltMod($objRequest->getSession()->get('user'));
                        $em_soporte->persist($objTarea);	
                        $em_soporte->flush();
                        if ($em_soporte->getConnection()->isTransactionActive())
                        {
                            $em_soporte->getConnection()->commit();
                            $em_soporte->getConnection()->close(); 
                        }                
                         
                    } 
                    catch (Exception $e) 
                    {
                        if ($em_soporte->getConnection()->isTransactionActive())
                        {
                            $em_soporte->getConnection()->rollback();
                            $em_soporte->getConnection()->close();
                        }                            
                          
                        throw $e;
                    }
                 }
                 else
                 {
                     $strMensajeError.="No existe la Tarea con id ".$id." <br>";
                 }
            }

            if($tipo==1)
            {
                return $this->redirect($this->generateUrl('planmantenimiento_show', array('id'=>$idPlanMantenimiento))); 
            }
            else if($tipo==2)
            {
                return $objResponse->setContent('La eliminacion fue exitosa');
            }

        } 
        catch (Exception $e) 
        {
            if($tipo==1)
            {
                $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
                return $this->redirect($this->generateUrl('planmantenimiento_show', array('id'=>$idPlanMantenimiento)));

            }
            else if($tipo==2)
            {
                return $objResponse->setContent($strMensajeError);
            }
        }          
    }
    
    /**
     * 
     * @Secure(roles="ROLE_343-8")
     * 
     * Documentación para el método 'deleteAction'.
     * 
     * Eliminar el o los planes de de mantenimiento.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 25-01-2016
     * 
     */
    public function deleteAction()
    {
        $em_soporte     = $this->getDoctrine()->getManager('telconet_soporte');
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objRequest     = $this->get('request');
        $parametro      = $objRequest->get('id');

        /*
         * tipo=1 Eliminar un plan de mantenimiento por medio de su id
         * tipo=2 Eliminación Masiva de planes de mantenimiento
         * 
         */
        $tipo = $objRequest->get('tipo') ? $objRequest->get('tipo') : 0;

        if($parametro)
        {
            $arrayValor = explode("|",$parametro);
        }
        else
        {
            $parametro  = $objRequest->get('param');

            $arrayValor = explode("|",$parametro);
        } 

        $estado             = 'Eliminado';
        $strMensajeError    = "";

        try
        {
            foreach($arrayValor as $id)
            {
                $objPlanMantenimiento = $em_soporte->getRepository('schemaBundle:AdmiProceso')->find($id);
                if( $objPlanMantenimiento )
                {
                    $em_soporte->getConnection()->beginTransaction();
                    try
                    { 
                        $objPlanMantenimiento->setEstado($estado);
                        $objPlanMantenimiento->setFeUltMod(new \DateTime('now'));
                        $objPlanMantenimiento->setUsrUltMod($objRequest->getSession()->get('user'));
                        $em_soporte->persist($objPlanMantenimiento);	
                        $em_soporte->flush();
                        
                        
                        $objMantenimientosPlan = $em_soporte->getRepository('schemaBundle:AdmiProceso')
                                                            ->findBy(array("procesoPadreId"=>$objPlanMantenimiento));
                        foreach($objMantenimientosPlan as $objMantenimientoPlan)
                        {
                            $objMantenimientoPlan->setEstado($estado);
                            $objMantenimientoPlan->setFeUltMod(new \DateTime('now'));
                            $objMantenimientoPlan->setUsrUltMod($objRequest->getSession()->get('user'));
                            $em_soporte->persist($objMantenimientoPlan);	
                            $em_soporte->flush();
                            
                            $objTareasMantenimiento = $em_soporte->getRepository('schemaBundle:AdmiTarea')
                                                        ->findBy(array("procesoId"=>$objMantenimientoPlan));
                            foreach($objTareasMantenimiento as $objTareaMantenimiento)
                            {
                                $objTareaMantenimiento->setEstado($estado);
                                $objTareaMantenimiento->setFeUltMod(new \DateTime('now'));
                                $objTareaMantenimiento->setUsrUltMod($objRequest->getSession()->get('user'));
                                $em_soporte->persist($objTareaMantenimiento);	
                                $em_soporte->flush();

                            }

                        }
                        
                        if ($em_soporte->getConnection()->isTransactionActive())
                        {
                            $em_soporte->getConnection()->commit();
                            $em_soporte->getConnection()->close();
                        }                
                          
                    } 
                    catch (Exception $e) 
                    {
                        if ($em_soporte->getConnection()->isTransactionActive())
                        {
                            $em_soporte->getConnection()->rollback();
                            $em_soporte->getConnection()->close();
                        }                            
                        throw $e;
                    }
                 }
                 else
                 {
                     $strMensajeError.="No existe el Plan de Mantenimiento con id ".$id." <br>";
                 }
            }

            if($tipo==0 || $tipo==1)
            {
                return $this->redirect($this->generateUrl('planmantenimiento')); 
            }
            else if($tipo==2)
            {
                return $objResponse->setContent('La eliminacion fue exitosa');
            }

        } 
        catch (Exception $e) 
        {
            if($tipo==0 || $tipo==1)
            {
                $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
                return $this->redirect($this->generateUrl('planmantenimiento'));

            }
            else if($tipo==2)
            {
                return $objResponse->setContent($strMensajeError);
            }
        }   

    }
    
    /**
     * 
     * Documentación para el método 'verificarNombrePlanAction'.
     * 
     * Método que valida que no se repitan los nombres de los planes.
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-02-2016
     * 
     */
    public function verificarNombrePlanAction()
    {
        $em_soporte     = $this->getDoctrine()->getManager('telconet_soporte');
        $objResponse    = new Response();
        $objRequest     = $this->get('request');
        $idPlan         = $objRequest->get('idPlan');
        $nombrePlan     = trim($objRequest->get('nombrePlan'));
        $estado         ='Activo';

        $session        = $objRequest->getSession();
        $empresaCod     = $session->get('idEmpresa');

        $parametros                         = array();
        $parametros["esPlanMantenimiento"]  = 'S';
        
        if($idPlan)
        {
            $parametros["idProcesoActual"]=$idPlan;
        }

        $objPlanes = $em_soporte->getRepository('schemaBundle:AdmiProceso')->getRegistros($parametros, $nombrePlan, $estado, '', '',$empresaCod,'');

        if($objPlanes)
        {
            $strMensaje='ERROR';
        }
        else
        {
            $strMensaje="OK";
        }

        $objResponse->setContent( $strMensaje );

        return $objResponse;
    }

}