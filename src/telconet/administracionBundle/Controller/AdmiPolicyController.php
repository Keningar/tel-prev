<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiPolicy;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Form\AdmiPolicyType;

use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiPolicyController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * indexAction
    *
    * Metodo encargado de redireccionar al index de la adminstracion      
    *
    * @return index
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 05-03-2015
     *     
    * @Secure(roles="ROLE_277-1")
    */
   public function indexAction()
   {
        $rolesPermitidos = array();
        
        if(true === $this->get('security.context')->isGranted('ROLE_277-8'))
        {
            $rolesPermitidos[] = 'ROLE_277-8';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_277-9'))
        {
            $rolesPermitidos[] = 'ROLE_277-9';
        }   
        if(true === $this->get('security.context')->isGranted('ROLE_277-6'))
        {
            $rolesPermitidos[] = 'ROLE_277-6';
        } 

        return $this->render('administracionBundle:AdmiPolicy:index.html.twig', array('rolesPermitidos' => $rolesPermitidos));                
    }

    /**
    * showAction
    *
    * Metodo encargado de redireccionar al show del policy creado
    *
    * @return show
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 05-03-2015
     *     
    * @Secure(roles="ROLE_277-6")
    */
    public function showAction($id)
    {        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");       

        if(null == $policy = $em->find('schemaBundle:AdmiPolicy', $id))
        {
            throw new NotFoundHttpException('No existe la Policy que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiPolicy:show.html.twig', array('policy' => $policy ));
    }

    /**
    * newAction
    *
    * Metodo encargado de redireccionar a la ventana para crear un nuevo policy
    *
    * @return new
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 05-03-2015
     *     
    * @Secure(roles="ROLE_277-2")
    */
    public function newAction()
    {        		                
        $form   = $this->createForm(new AdmiPolicyType(),new AdmiPolicy());        
        return $this->render('administracionBundle:AdmiPolicy:new.html.twig',array('form'=>$form->createView(),'error'=>''));
    }
    
    /**
    * createAction
    *
    * Metodo encargado de crear la policy con los parametros ingresados en el formulario
    *
    * @return show
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 05-03-2015
     *     
    * @Secure(roles="ROLE_277-3")
    */
   public function createAction()
   {       
        $request = $this->get('request');

        $em = $this->get('doctrine')->getManager('telconet_infraestructura');     
        
        $parametros = $request->request->get('telconet_schemabundle_admipolicytype');
        
        $dnsServers = $request->get('dns-servers');
        
        $seEjecutaInmediato = $request->get('seEjecuta');
        
        $em->getConnection()->beginTransaction();
        
        $mensajeError = '';
        
        try
        {

            $entity = new AdmiPolicy();       
            $entity->setNombrePolicy($parametros['nombrePolicy']);
            $entity->setLeaseTime($parametros['leaseTime']."h");
            $entity->setMascara($parametros['mascara']);
            $entity->setDnsName($parametros['dnsName']);
            $entity->setGateway($parametros['gateway']);
            $entity->setDnsServers($dnsServers);
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));        
            $entity->setIpCreacion($request->getClientIp());      

            //Elemento Servidor a gestionar
            $objElemento = $em->getRepository('schemaBundle:InfoElemento')->find($parametros['elementoId']);            

            if($seEjecutaInmediato == 'SI')
            {
                if($objElemento)
                {
                    /* @var $script InfoServicioTecnicoService */
                    $script             = $this->get('tecnico.InfoServicioTecnico');
                    $scriptArray        = $script->obtenerArregloScript("crearPolicy",$objElemento->getModeloElementoId());
                    $idDocumento        = $scriptArray[0]->idDocumento;
                    $usuario            = $scriptArray[0]->usuario;
                    
                    $scriptDnsArray     = $script->obtenerArregloScript("crearPolicyDnsServer",$objElemento->getModeloElementoId());
                    $idDocumentoDns     = $scriptDnsArray[0]->idDocumento;

                    $nombrePolicy       = $parametros['nombrePolicy'];
                    $leaseTime          = $parametros['leaseTime'].'h';
                    $mascara            = $parametros['mascara'];
                    $dnsName            = $parametros['dnsName'];
                    $gateway            = $parametros['gateway'];

                    //Cargar Datos
                    $datosEjecucionPolicy = $nombrePolicy.",".$nombrePolicy.",".$gateway.",".$nombrePolicy.",".$leaseTime.",".$nombrePolicy.",".
                                            $mascara.",".$nombrePolicy.",".$dnsName;

                    $datosEjecucionPolicyDnsServers  = $nombrePolicy.",".$dnsServers;

                    $resultadoPolicy            = $script->ejecutarComandoMdEjecucion($objElemento->getId(),$usuario,
                                                                                      $datosEjecucionPolicy,$idDocumento);
                    $resultadoPolicyDnsServers  = $script->ejecutarComandoPersonalizadoMdDatos($objElemento->getId(),$usuario,
                                                                                               $datosEjecucionPolicyDnsServers,
                                                                                               $idDocumentoDns,'crearPolicyDnsServer');

                    $statusMdEjecucion = $resultadoPolicy->status;
                    $statusMdDatos     = $resultadoPolicyDnsServers->status;

                    if($statusMdEjecucion == "ERROR")
                    {
                        $mensajeError = "Error al Crear : ".$resultadoPolicy->mensaje;
                    }
                    if($statusMdDatos == "ERROR")
                    {
                        //Aplicar rollback en CNR de la policy ingresada
                        $scriptArray  = $script->obtenerArregloScript("eliminarPolicy",$objElemento->getModeloElementoId());
                        $idDocumento  = $scriptArray[0]->idDocumento;
                        $usuario      = $scriptArray[0]->usuario;

                        $datosEliminarPolicy = $parametros['nombrePolicy'];

                        $resultado = $script->ejecutarComandoMdEjecucion($objElemento->getId(),$usuario,$datosEliminarPolicy,$idDocumento);

                        if($resultado->status == 'ERROR')
                        {
                            $mensajeError = "Error al Crear : ".$resultado->mensaje;
                        }
                        else
                        {
                            $mensajeError = "Error al Crear : ".$resultadoPolicyDnsServers->mensaje;
                        }                                        
                    }
                    else
                    {   
                        //Se crea un historial
                        $objHistorialElemento = new InfoHistorialElemento();
                        $objHistorialElemento->setElementoId($objElemento);
                        $objHistorialElemento->setEstadoElemento("Activo");

                        $params= "Policy : ".$nombrePolicy." ; LeaseTime : ".$leaseTime." ; Mascara: ".$mascara." ; "
                                    . "DNS Name : ".$dnsName." ; DNS Serevrs : ".$dnsServers;

                        $objHistorialElemento->setObservacion("Se ejecuta la policy con los siguientes parametros: ".$params);

                        $objHistorialElemento->setUsrCreacion($request->getSession()->get('user'));
                        $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                        $objHistorialElemento->setIpCreacion($request->getClientIp());

                        $em->persist($objHistorialElemento);
                        $em->flush();

                        $em->persist($entity);
                        $em->flush();

                        $em->getConnection()->commit();   

                        return $this->redirect($this->generateUrl('admipolicy_show', array('id' => $entity->getId())));
                    }
                }
                else //No existe elemento a relacionar la ejecucion de la accion del policy
                {
                    $mensajeError = 'Error insperado al Crear Policy';
                } 
            }
            else
            {
                $em->persist($entity);
                $em->flush();

                $em->getConnection()->commit();   

                return $this->redirect($this->generateUrl('admipolicy_show', array('id' => $entity->getId())));
            }
           
        }
        catch(Exception $e)
        {            
            $em->getConnection()->rollback();
            $em->getConnection()->close();                        
        } 
                
        $form = $this->createForm(new AdmiPolicyType(),new AdmiPolicy());        
        return $this->render('administracionBundle:AdmiPolicy:new.html.twig',array('form'=>$form->createView(),'error'=>$mensajeError));
    }            

    /**
    * ajaxDeleteAction
    *
    * Metodo encargado de eliminar la policy tanto logicamente como en el CNR
    *
    * @return respuesta
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 05-03-2015
     *     
    * @Secure(roles="ROLE_277-9")
    */
   public function ajaxDeleteAction()
   {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');

        $request  = $this->getRequest();
        $id         = $request->get('param');
        $idElemento = $request->get('elemento');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if(null == $entity = $em->find('schemaBundle:AdmiPolicy', $id))
        {
            $respuesta->setContent("No existe la entidad");
        }
        else
        {           
            /*Realizar eliminacion a nivel de equipo*/
            //Elemento Servidor a gestionar
            $objElemento = $em->getRepository('schemaBundle:InfoElemento')->find($idElemento);
            
            if($objElemento)
            {
                $entity->setEstado("Eliminado");
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($request->getSession()->get('user'));
                $entity->setIpCreacion($request->getClientIp());
                
                /* @var $script InfoServicioTecnicoService */
                $script       = $this->get('tecnico.InfoServicioTecnico');
                $scriptArray  = $script->obtenerArregloScript("eliminarPolicy",$objElemento->getModeloElementoId());
                $idDocumento  = $scriptArray[0]->idDocumento;
                $usuario      = $scriptArray[0]->usuario;
                
                $datos = $entity->getNombrePolicy();
                
                $resultado = $script->ejecutarComandoMdEjecucion($objElemento->getId(),$usuario,$datos,$idDocumento);
                
                $status = $resultado->status;
                
                if($status == 'ERROR')
                {
                    $mensaje = 'Error al Eliminar : '.$resultado->mensaje;                    
                }
                else
                {
                    $objHistorialElemento = new InfoHistorialElemento();
                    $objHistorialElemento->setElementoId($objElemento);
                    $objHistorialElemento->setEstadoElemento("Eliminado");
                                        
                    $objHistorialElemento->setObservacion("Se Elimina la policy : ".$entity->getNombrePolicy()." del Equipo");
                    
                    $objHistorialElemento->setUsrCreacion($request->getSession()->get('user'));
                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objHistorialElemento->setIpCreacion($request->getClientIp());
                    $em->persist($objHistorialElemento);
                    $em->flush();
                    
                    $em->persist($entity);
                    $em->flush();

                    $mensaje = "Se elimino la Policy"; 
                }               
            }
            else
            {
                $mensaje = "Error al Eliminar"; 
            }           
        }
        $respuesta->setContent($mensaje);
        return $respuesta;
    }
    
    /**
    * ajaxGetElementoServidorAction
    *
    * Metodo encargado de obtener los elemento de tipo servidor CNR para gestionar las policy
    *
    * @return respuesta
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 05-03-2015    
    */
    public function ajaxGetElementoServidorAction()
    {
        $respuesta  = new Response();
        $parametros = array();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');                
       
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $objTipoElemento = $em->getRepository("schemaBundle:AdmiTipoElemento")->findOneBy(array('nombreTipoElemento'=>'SERVIDOR'));
       
        $parametros["nombre"]       = '';
        $parametros["estado"]       = 'Activo';
        $parametros["tipoElemento"] = $objTipoElemento->getId();
        $parametros["codEmpresa"]   = $peticion->get('idEmpresa');
        $parametros["start"]        = '';
        $parametros["limit"]        = '';

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoElemento')
            ->generarJsonElementosXTipo($parametros);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
    * ajaxGrid
    *
    * Metodo encargado de obtener toda la informacion de los policys creados
    *
    * @return respuesta
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 05-03-2015
     *     
    * @Secure(roles="ROLE_277-7")
    */
    public function ajaxGridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
       
        $nombre = $peticion->query->get('nombre');
        $estado = $peticion->query->get('estado');
        
        $start  = $peticion->query->get('start');
        $limit  = $peticion->query->get('limit');
       
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiPolicy')
            ->generarJson($nombre, $estado, $start, $limit);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

}