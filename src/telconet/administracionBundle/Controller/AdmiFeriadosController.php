<?php
namespace telconet\administracionBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiFeriados;
use telconet\schemaBundle\Repository\AdmiFeriadosRepository;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;


use Symfony\Component\HttpFoundation\Response;

class AdmiFeriadosController extends Controller 
{ 
    /**
     * Funcion que sirve para mostrar la pantalla principal de feriados
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 07-12-2018
     * @Secure(roles="ROLE_421-1")
     */
    public function indexAction()
    {
        if(true === $this->get('security.context')->isGranted('ROLE_421-1'))
        {
            $arrayRolesPermitidos[] = 'ROLE_421-1'; //index
        }
        return $this->render('administracionBundle:AdmiFeriados:index.html.twig', array(
                'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }

   /**
    * Documentación para el método 'gridAction'.
    *
    * Metodo utilizado para mostrar el grid de la administracion de Feriados
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>          
    * @version 1.0 10-12-2018
    */    
    public function gridAction()
    {
        $serviceUtil  = $this->get('schema.Util');
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion        = $this->get('request');
        $objSession         = $this->get( 'session' );

        $strUsrCreacion     = $objSession->get('user');
        $strIpClient        = $objPeticion->getClientIp();

        $arrayDatosBusqueda    = array();
        $arrayDatosBusqueda['intStart'] = 0;
        $arrayDatosBusqueda['intLimit'] = 99999999;
               
        try
        {
            $objJson = $this->getDoctrine()->getManager("telconet_general")
                                           ->getRepository('schemaBundle:AdmiFeriados')
                                           ->generarJsonFeriados($arrayDatosBusqueda);
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 'AdmiFeriadosController->gridAction', $ex->getMessage(), $strUsrCreacion, $strIpClient);
        }
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
    }

    /**
    * Documentación para el método 'newAction'.
    *
    * Metodo utilizado para direccionar al js de creacion de Feriados.
    *
    * @author Edgar Pin Villavicencio <epin@telconet.ec>          
    * @version 1.0 10-12-2017
    */
    public function newAction()
    {
        return $this->render('administracionBundle:AdmiFeriados:new.html.twig',
                             array( 'strHoraInicio' => trim($this->container->getParameter('planificacion.hora.inicio')),
                                    'strHoraFin'    => trim($this->container->getParameter('planificacion.hora.fin')),
                                    'intIntervalo'  => trim($this->container->getParameter('planificacion.hora.intervalo')),
                                    'strMinimoHoras' => trim($this->container->getParameter('planificacion.plantilla.minimohoras')),
                                    'intIntervaloMaximo' => trim($this->container->getParameter('planificacion.plantilla.intervalomaximo'))
                                   ));
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

    public function saveAction()
    {
        $emGeneral        = $this->getDoctrine()->getManager('telconet_general');
        $objResponse      = new JsonResponse();
        
        $objRequest       = $this->getRequest();
        $objSession       = $this->getRequest()->getSession();
        
        $intIdFeriados   = ($objRequest->get('intIdFeriados') ? $objRequest->get('intIdFeriados') : 0);
        $strDescripcion  = $objRequest->get('strDescripcion');
        $strTipoFeriado  = $objRequest->get('strTipo');
        $strMes          = $objRequest->get('strMes');
        $strDia          = $objRequest->get('strDia');
        $strComentario   = $objRequest->get('strComentario');
        $intCantonId     = $objRequest->get('intCanton');
        
        try
        {  
            //Valido que no exista la plantilla
            if ($intIdFeriados == 0)
            {
                $entityFeriados = $emGeneral->getRepository('schemaBundle:AdmiFeriados')
                     ->findOneBy(array('descripcion' => $strDescripcion));     
                if (isset($entityFeriados) && count($entityFeriados) > 0 && $intIdFeriados == 0)
                {
                    $arrayResult['strStatus']        = 'ERROR';
                    $arrayResult['strMessageStatus'] = 'Feriado <span style="color:blue"><b>'. $strDescripcion . '</b></span> ya Existe! <br>';
                    $objResponse->setData($arrayResult);
                    return $objResponse;  
                } 
            }
            else
            {
                $entityFeriados = $emGeneral->getRepository('schemaBundle:AdmiFeriados')
                     ->find($intIdFeriados); 
            }            
            $emGeneral->getConnection()->beginTransaction();
            if ($intIdFeriados == 0)
            {
                $entityFeriados = new AdmiFeriados();
                $entityFeriados->setFeCreacion(new \DateTime('now'));
                $entityFeriados->setUsrCreacion($objSession->get('user'));
            }
            $entityFeriados->setDescripcion($strDescripcion);
            $entityFeriados->setTipo($strTipoFeriado);
            $entityFeriados->setMes($strMes);
            $entityFeriados->setDia($strDia);
            $entityFeriados->setComentario($strComentario);
            $entityFeriados->setCantonId($intCantonId);
            
            $entityFeriados->setEstado('Activo');
            
            $emGeneral->persist($entityFeriados);
            $emGeneral->flush();

            $emGeneral->getConnection()->commit();
            $arrayResult['strStatus']        = 'OK';
            $arrayResult['strMessageStatus'] = 'Feriado Creado Correctamente';            
            if ($intIdFeriados > 0)
            {
                $arrayResult['strMessageStatus'] = 'Feriado Editado Correctamente';
            }     
        }
        catch (DBALException $ex) 
        {
            $emGeneral->getConnection()->rollback();
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'No se pudo Grabar la data!';
        }
        catch (Exception $ex) 
        {
            $emGeneral->getConnection()->rollback();
            $arrayResult['strStatus']        = 'ERROR';
            $arrayResult['strMessageStatus'] = 'No se pudo Grabar la data!';
        }
        
        $objResponse->setData($arrayResult);
        return $objResponse;            
    }
      
    /**
     * Documentación para el método 'editAction'.
     *
     * Metodo utilizado para modificar información de los Feriados
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 10-12-2018
     * 
     */
    
    public function editAction($intIdFeriados)
    {
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');
        $entityFeriado =  $emGeneral->getRepository('schemaBundle:AdmiFeriados')
          ->find($intIdFeriados);

        $entityCanton =  $emGeneral->getRepository('schemaBundle:AdmiCanton')
          ->find($entityFeriado->getCantonId());
        
        
        return $this->render('administracionBundle:AdmiFeriados:edit.html.twig',
                             array( 'intIdFeriados'   => $entityFeriado->getId(),
                                    'strDescripcion'  => trim($entityFeriado->getDescripcion()),
                                    'strTipo'         => $entityFeriado->getTipo(),
                                    'strMes'          => $entityFeriado->getMes(),
                                    'strDia'          => $entityFeriado->getDia(),
                                    'strEstado'       => $entityFeriado->getEstado(),
                                    'intIdCanton'     => $entityFeriado->getCantonId(),
                                    'strNombreCanton' => $entityCanton->getNombreCanton(),
                                    'strComentario'   => $entityFeriado->getComentario()));
    }
    
    /**
     * Documentación para el método 'showAction'.
     *
     * Metodo utilizado para visualizar información de los Feriados
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>          
     * @version 1.0 11-12-2018
     * 
     */
    
    public function showAction($intIdFeriados)
    {
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');
        $entityFeriado =  $emGeneral->getRepository('schemaBundle:AdmiFeriados')
          ->find($intIdFeriados);
        $entityCanton =  $emGeneral->getRepository('schemaBundle:AdmiCanton')
          ->find($entityFeriado->getCantonId());
        
        
        return $this->render('administracionBundle:AdmiFeriados:show.html.twig',
                             array( 'intIdFeriados'   => $entityFeriado->getId(),
                                    'strDescripcion'  => trim($entityFeriado->getDescripcion()),
                                    'strTipo'         => $entityFeriado->getTipo(),
                                    'strMes'          => $entityFeriado->getMes(),
                                    'strDia'          => $entityFeriado->getDia(),
                                    'strEstado'       => $entityFeriado->getEstado(),
                                    'intIdCanton'     => $entityFeriado->getCantonId(),
                                    'strNombreCanton' => $entityCanton->getNombreCanton(),
                                    'strComentario'   => $entityFeriado->getComentario()));
    }   
}
