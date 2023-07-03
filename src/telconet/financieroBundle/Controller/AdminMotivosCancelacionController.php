<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\financieroBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiPlantilla;
use telconet\schemaBundle\Entity\InfoAliasPlantilla;

use telconet\schemaBundle\Form\AdmiPlantillaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\administracionBundle\Service\PlantillaService;

class AdminMotivosCancelacionController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_474-1")
    */
    public function indexAction()
    {	

        return $this->render('financieroBundle:AdminMotivosCancelacion:index.html.twig');

    }
    /**
     * Documentación para el método 'getMotivosCancelacionAdminAction'.
     * Función que retorna listado de motivos asociados a cancelación administrativa.
     *
     * @author Ivan Romero <icromerto@telconet.ec>
     * @version 1.0 18-12-2021
     * 
     * @return object $objResponse
     */    
    public function getMotivosCancelacionAdminAction()
    {
        $emGeneral   = $this->getDoctrine()->getManager();
        
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $objSistModulo = $emSeguridad->getRepository('schemaBundle:SistModulo')
                                     ->findOneBy(array( 'nombreModulo' => 'clientes','estado' => 'Modificado'));
        $objSistAccion = $emSeguridad->getRepository('schemaBundle:SistAccion')
                                     ->findOneBy(array( 'nombreAccion' => 'cancelarCliente','estado' => 'Activo',
                                    'usrCreacion' => 'fadum'));
        if(is_object($objSistModulo))
        {        
            $objSeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                  ->findOneBy(array( 'moduloId' => $objSistModulo->getId(),
                                                  'accionId' =>$objSistAccion->getId()));

            if(is_object($objSeguRelacionSistema))
            {
                $arrayResultado = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                            ->loadMotivos($objSeguRelacionSistema->getId());

                foreach($arrayResultado as $objMotivo):
                    $arrayMotivos[] = array(
                        'id'          => $objMotivo->getId(),
                        'descripcion' => $objMotivo->getNombreMotivo()
                    );
                
                    $objResponse = new Response(json_encode(array('documentos' => $arrayMotivos)));
                endforeach;
            }
        }
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }
     /**
     * @Secure(roles="ROLE_474-1")
     * gridPromocionesAction()
     * Función que obtiene un listado de motivos de cancelacion
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     * @return $objResponse - Listado de motivos
     */
    public function gridMotivosAction()
    {
        $emGeneral   = $this->getDoctrine()->getManager();
        
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $objSistModulo = $emSeguridad->getRepository('schemaBundle:SistModulo')
                                     ->findOneBy(array( 'nombreModulo' => 'clientes','estado' => 'Modificado'));
        $objSistAccion = $emSeguridad->getRepository('schemaBundle:SistAccion')
                                     ->findOneBy(array( 'nombreAccion' => 'cancelarCliente','estado' => 'Activo',
                                    'usrCreacion' => 'fadum'));
        if(is_object($objSistModulo))
        {        
            $objSeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                  ->findOneBy(array( 'moduloId' => $objSistModulo->getId(),
                                                  'accionId' =>$objSistAccion->getId()));

            if(is_object($objSeguRelacionSistema))
            {
                $arrayResultado = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                            ->loadMotivos($objSeguRelacionSistema->getId());

                foreach($arrayResultado as $objMotivo):
                    $arrayMotivos[] = array(  'intIdPlantilla'         => $objMotivo->getId(),
                    'strdescripcion'     => $objMotivo->getNombreMotivo(),
                    'strFechaCreacion' => strval(date_format($objMotivo->getFeCreacion(),"d-m-Y H:i:s")),
                    'strUsrCreacion'         => $objMotivo->getUsrCreacion(),
                    'strFechaModifica' => strval(date_format($objMotivo->getFeUltMod(),"d-m-Y H:i:s")),
                    'strUsrModifica'         => $objMotivo->getUsrUltMod(),
                    'strAcciones'            => array( 
                                                'linkEditar' => $this->generateUrl('adminMotivosCancelacion_editar', 
                                                array('intIdMotivo' => $objMotivo->getId())),
                                                'linkEliminar' => $this->generateUrl('adminMotivosCancelacion_eliminar', 
                                                array('intIdMotivo' => $objMotivo->getId())))
                    );
                
                    $objResponse = new Response(json_encode(array('data' => $arrayMotivos)));
                endforeach;
            }
        }
    

        
        
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }
	

    /**
     * @Secure(roles="ROLE_474-1")
     * editarAction()
     * Función que modal de editar motivo de cancelacion
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     */
    public function editarAction($intIdMotivo)
    {
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $objMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivo);
        $strNombreMotivo = $objMotivo->getNombreMotivo();
        return $this->render('financieroBundle:AdminMotivosCancelacion:editarMotivo.html.twig',
        array('nombreMotivo' => $strNombreMotivo,
              'id'=> $intIdMotivo));
    }

    
    /**
     * @Secure(roles="ROLE_474-1")
     * eliminarAction()
     * Función que modal de eliminar motivo de cancelacion
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     */
    public function eliminarAction($intIdMotivo)
    {
        $servicePlantilla          = $this->get('administracion.Plantilla');
        $emGeneral = $this->getDoctrine()->getManager("telconet_general");
        $objMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivo);
        $strNombreMotivo = $objMotivo->getNombreMotivo();
        return $this->render('financieroBundle:AdminMotivosCancelacion:eliminarMotivo.html.twig',
        array('nombreMotivo' => $strNombreMotivo,
              'id'=> $intIdMotivo));

    }

    /**
     * @Secure(roles="ROLE_474-1")
     * crearAction()
     * Función que modal de crear motivo de cancelacion
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 05-12-2021
     * @since 1.0
     *
     */
    public function crearAction()
    {

        return $this->render('financieroBundle:AdminMotivosCancelacion:crearMotivo.html.twig');

    }

    /**
     * @Secure(roles="ROLE_474-1")
     * crearMotivoAction()
     * Función que  crea  un motivo de cancelacion
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 18-12-2021
     * @since 1.0
     *
     * @return $objResponse - motivo creado
     */
    public function crearMotivoAction()
    {   $objRequest           = $this->getRequest();
        $objSesion    = $objRequest->getSession();

        $emGeneral = $this->get('doctrine')->getManager('telconet_general');
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityMotivo = new AdmiMotivo();
        $objSistModulo = $emSeguridad->getRepository('schemaBundle:SistModulo')
                                     ->findOneBy(array( 'nombreModulo' => 'clientes','estado' => 'Modificado'));
        $objSistAccion = $emSeguridad->getRepository('schemaBundle:SistAccion')
                                     ->findOneBy(array( 'nombreAccion' => 'cancelarCliente','estado' => 'Activo',
                                    'usrCreacion' => 'fadum'));
        try
        {
        if(is_object($objSistModulo))
        {        
            $objSeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                  ->findOneBy(array( 'moduloId' => $objSistModulo->getId(),
                                                  'accionId' =>$objSistAccion->getId()));

                if(is_object($objSeguRelacionSistema))
                {
                    
                    $entityMotivo->setNombreMotivo($objRequest->get('strDescripcionMotivo'));
                    $entityMotivo->setRelacionSistemaId($objSeguRelacionSistema->getId());
                    $entityMotivo->setEstado('Activo');
                    $entityMotivo->setFeCreacion(new \DateTime('now'));
                    $entityMotivo->setUsrCreacion($objSesion->get('user'));
                    $entityMotivo->setFeUltMod(new \DateTime('now'));
                    $entityMotivo->setUsrUltMod($objSesion->get('user'));

                    // Save
                    $emGeneral->persist($entityMotivo);
                    $emGeneral->flush();
                }
            } 
            

            $objResponse = new Response(json_encode(array('objData' => $entityMotivo,
            'strStatus' => '0',
            'strMensaje' =>'Motivo creado correctamente')));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
        catch(Exception $e)
        {
            // Rollback the failed transaction attempt
            $emGeneral->getConnection()->rollback();
            $objResponse = new Response(json_encode(array('objData' => $entityMotivo,
            'strStatus' => '1',
            'strMensaje' =>'Ocurrió un error al crear motivo, por favor contactar con soporte.')));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
    }

    /**
     * @Secure(roles="ROLE_474-1")
     * editarMotivoAction()
     * Función que edita un motivo de cancelacion
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 18-12-2021
     * @since 1.0
     *
     * @return $objResponse - motivo editado
     */
    public function editarMotivoAction()
    {   $objRequest           = $this->getRequest();
        $objSesion    = $objRequest->getSession();

        $emGeneral = $this->get('doctrine')->getManager('telconet_general');
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $objSistModulo = $emSeguridad->getRepository('schemaBundle:SistModulo')
                                     ->findOneBy(array( 'nombreModulo' => 'clientes','estado' => 'Modificado'));
        $entityMotivo = new AdmiMotivo();
        try
        {
            if(is_object($objSistModulo))
            {        
                $objSeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                    ->findOneBy(array( 'moduloId' => $objSistModulo->getId()));

                if(is_object($objSeguRelacionSistema))
                {
                    $entityMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneById($objRequest->get('intIdMotivo'));

                    $entityMotivo->setNombreMotivo($objRequest->get('strDescripcionMotivo'));
                    $entityMotivo->setFeUltMod(new \DateTime('now'));
                    $entityMotivo->setUsrUltMod($objSesion->get('user'));

                    // Save
                    $emGeneral->persist($entityMotivo);
                    $emGeneral->flush();
                }
            } 
            

            $objResponse = new Response(json_encode(array('objData' => $entityMotivo,
            'strStatus' => '0',
            'strMensaje' =>'Motivo editado correctamente')));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
        catch(Exception $e)
        {
            // Rollback the failed transaction attempt
            $emGeneral->getConnection()->rollback();
            $objResponse = new Response(json_encode(array('objData' => $entityMotivo,
            'strStatus' => '1',
            'strMensaje' =>'Ocurrió un error al editar motivo, por favor contactar con soporte.')));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
    }

     /**
     * @Secure(roles="ROLE_474-1")
     * eliminarMotivoAction()
     * Función que elimina un motivo de cancelacion
     * 
     * @author Ivan Romero <icromero@telconet.ec>
     * @version 1.0 18-12-2021
     * @since 1.0
     *
     * @return $objResponse - motivo eliminado
     */
    public function eliminarMotivoAction()
    {   $objRequest           = $this->getRequest();
        $objSesion    = $objRequest->getSession();

        $emGeneral = $this->get('doctrine')->getManager('telconet_general');
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $objSistModulo = $emSeguridad->getRepository('schemaBundle:SistModulo')
                                     ->findOneBy(array( 'nombreModulo' => 'clientes','estado' => 'Modificado'));
        $entityMotivo = new AdmiMotivo();
        try
        {
            if(is_object($objSistModulo))
            {        
                $objSeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                    ->findOneBy(array( 'moduloId' => $objSistModulo->getId()));

                if(is_object($objSeguRelacionSistema))
                {
                    $entityMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneById($objRequest->get('intIdMotivo'));

                    $entityMotivo->setEstado('Eliminado');
                    $entityMotivo->setFeUltMod(new \DateTime('now'));
                    $entityMotivo->setUsrUltMod($objSesion->get('user'));

                    // Save
                    $emGeneral->persist($entityMotivo);
                    $emGeneral->flush();
                }
            } 
            

            $objResponse = new Response(json_encode(array('objData' => $entityMotivo,
            'strStatus' => '0',
            'strMensaje' =>'Motivo eliminado correctamente')));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
        catch(Exception $e)
        {
            // Rollback the failed transaction attempt
            $emGeneral->getConnection()->rollback();
            $objResponse = new Response(json_encode(array('objData' => $entityMotivo,
            'strStatus' => '1',
            'strMensaje' =>'Ocurrió un error al eliminar motivo, por favor contactar con soporte.')));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }
    }

    
}
