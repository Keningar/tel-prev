<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoRelacionElemento;
use telconet\schemaBundle\Form\InfoElementoSinUbicacionType;
use telconet\schemaBundle\Form\InfoElementoPopType;
use telconet\tecnicoBundle\Resources\util\Util;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Response;

class InfoElementoSinUbicacionController extends Controller implements TokenAuthenticatedController
{ 
    public function indexSinUbicacionAction(){
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $rolesPermitidos = array();

        //MODULO 227 - OLT
        
        if (true === $this->get('security.context')->isGranted('ROLE_227-4'))
        {
                $rolesPermitidos[] = 'ROLE_227-4'; //editar elemento olt
        }
        if (true === $this->get('security.context')->isGranted('ROLE_227-8'))
        {
                $rolesPermitidos[] = 'ROLE_227-8'; //eliminar elemento olt
        }
        if (true === $this->get('security.context')->isGranted('ROLE_227-6'))
        {
                $rolesPermitidos[] = 'ROLE_227-6'; //ver elemento olt
        }
        if (true === $this->get('security.context')->isGranted('ROLE_227-828'))
        {
                $rolesPermitidos[] = 'ROLE_227-828'; //administrar puertos elemento olt
        }
        if (true === $this->get('security.context')->isGranted('ROLE_227-1217'))
        {
                $rolesPermitidos[] = 'ROLE_227-1217'; //mostrar subscribers en el olt
        }
        
        if (true === $this->get('security.context')->isGranted('ROLE_151-1127')) {
                $rolesPermitidos[] = 'ROLE_151-1127'; //Administrar pool
        }
        
        return $this->render('tecnicoBundle:InfoElementoSinUbicacion:index.html.twig', array(
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    public function getEncontradosSinUbicacionAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $peticion = $this->get('request');
        
        $nombreElemento = $peticion->query->get('nombreElemento');
        $idEmpresa = $session->get('idEmpresa');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonSinUbicacion(strtoupper($nombreElemento),$start,$limit,$em,$idEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    
    /**
     * Documentación para el método 'grabarUbicacionAction'
     * 
     * Método que gurda la información de la ubicación de un elemento
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 19-09-2018 - Se agrega la validación de las coordenadas de latitud y longitud de acuerdo al país en sesión
     * 
     */
    public function grabarUbicacionAction()
    {
        $objRespuesta = new JsonResponse();
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $peticion = $this->get('request');
        
        $idElemento = $peticion->get('idElemento');
        $idParroquia = $peticion->get('parroquia');
        $longitud = $peticion->get('longitud');
        $latitud = $peticion->get('latitud');
        $altura = $peticion->get('altura');
            
        $em->beginTransaction();
        try
        {
            $elementoSinUbicacion = $em->find('schemaBundle:InfoElemento', $idElemento);
            
            $serviceInfoElemento        = $this->get("tecnico.InfoElemento");
            $arrayRespuestaCoordenadas  = $serviceInfoElemento->validarLimitesCoordenadasElemento(array("latitudElemento"   => $latitud,
                                                                                                        "longitudElemento"  => $longitud,
                                                                                                        "msjTipoElemento"   => "del elemento "
                                                                                                 ));
            if($arrayRespuestaCoordenadas["status"] === "ERROR")
            {
                throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
            }

            //info ubicacion
            $parroquia = $em->find('schemaBundle:AdmiParroquia', $idParroquia);
            $ubicacionElemento = new InfoUbicacion();
            $ubicacionElemento->setLatitudUbicacion($latitud);
            $ubicacionElemento->setLongitudUbicacion($longitud);
            $ubicacionElemento->setDireccionUbicacion("NA");
            $ubicacionElemento->setAlturaSnm($altura);
            $ubicacionElemento->setParroquiaId($parroquia);
            $ubicacionElemento->setUsrCreacion($session->get('user'));
            $ubicacionElemento->setFeCreacion(new \DateTime('now'));
            $ubicacionElemento->setIpCreacion($peticion->getClientIp());
            $em->persist($ubicacionElemento);

            //empresa elemento ubicacion
            $empresaElementoUbica = new InfoEmpresaElementoUbica();
            $empresaElementoUbica->setEmpresaCod($session->get('idEmpresa'));
            $empresaElementoUbica->setElementoId($elementoSinUbicacion);
            $empresaElementoUbica->setUbicacionId($ubicacionElemento);
            $empresaElementoUbica->setUsrCreacion($session->get('user'));
            $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
            $empresaElementoUbica->setIpCreacion($peticion->getClientIp());
            $em->persist($empresaElementoUbica);

            $em->flush();
            $em->commit();

            $objRespuesta->setContent("OK");
        } 
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive())
            {
                $em->rollback();
            }
            $em->close();
            $objRespuesta->setContent($e->getMessage());
        }
        return $objRespuesta;
    }
    
    public function deleteSinUbicacionAction($id){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $request = $this->getRequest();
        $peticion = $this->get('request');
        $session  = $request->getSession();
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emC = $this->getDoctrine()->getManager('telconet');
        $entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoElemento entity.');
        }
                
        $em->getConnection()->beginTransaction();

        //elemento
        $entity->setEstado("Eliminado");
        $entity->setUsrCreacion($session->get('user'));
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setIpCreacion($peticion->getClientIp());  
        $em->persist($entity);
        
        //relacion elemento
        $arrayRelacionElemento = $em->getRepository('schemaBundle:InfoRelacionElemento')->findBy(array( "elementoIdB" =>$entity));
        for($i=0;$i<count($arrayRelacionElemento);$i++)
        {
            $objRelacionElemento = $arrayRelacionElemento[$i];
            
            $objRelacionElemento->setEstado("Eliminado");
            $em->persist($objRelacionElemento);
        }

        //historial elemento
        $historialElemento = new InfoHistorialElemento();
        $historialElemento->setElementoId($entity);
        $historialElemento->setEstadoElemento("Eliminado");
        $historialElemento->setObservacion("Se elimino un SinUbicacion");
        $historialElemento->setUsrCreacion($session->get('user'));
        $historialElemento->setFeCreacion(new \DateTime('now'));
        $historialElemento->setIpCreacion($peticion->getClientIp());
        $em->persist($historialElemento);
            
        $em->flush();
        $em->getConnection()->commit();

        return $this->redirect($this->generateUrl('elementosinubicacion'));
    }
    
    
}