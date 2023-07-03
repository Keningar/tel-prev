<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaGrupo;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoContrasena;
use telconet\schemaBundle\Entity\AdmiUsuarioAcceso;
use telconet\schemaBundle\Entity\AdmiModeloUsuarioAcceso;
use telconet\schemaBundle\Entity\AdmiMarcaElemento;
use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\InfoElementoContrasena;
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;


use telconet\schemaBundle\Form\AdmiAreaType;
use telconet\schemaBundle\Form\EmpresasType;
use telconet\schemaBundle\Form\ElementoGatewayType;
use telconet\schemaBundle\Form\InfoIpType;
use telconet\schemaBundle\Form\InfoContrasenaType;
use telconet\schemaBundle\Form\AdmiUsuarioAccesoType;
use telconet\schemaBundle\Form\MarcaElementoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\soporteBundle\Service\EnvioPlantillaService;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Style_Fill;


/**
 * ElementoGatewayController : Clase controlador que gestiona las acciones para la Administracion de Gateways
 *
 * El controlador esta encargado de todas las acciones para la creacion, edicion, vista
   de todos los Elementos tipo GATEWAY que sirven para el envio de SMS desde Telcos+
 *
 * @category   AdminstracionBundle    
 * @version    1.0
 * @link       https://telcos.telconet.ec/administracion/tecnico/crear_gateway/
 * @author     Allan Suárez Carvajal (arsuarez) 
 */

class ElementoGatewayController extends Controller implements TokenAuthenticatedController
{ 
   
     /**
     * Metodo que redirecciona al index.html.twig de la administacion    
     * @return redireccion al index
     * @author arsuarez
     */
      /**
    * @Secure(roles="ROLE_239-1")
    */
    public function indexAction()
    {
        $rolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_239-6'))
        {
            $rolesPermitidos[] = 'ROLE_239-6';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_239-5'))
        {
            $rolesPermitidos[] = 'ROLE_239-5';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_239-4'))
        {
            $rolesPermitidos[] = 'ROLE_239-4';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_239-8'))
        {
            $rolesPermitidos[] = 'ROLE_239-8';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_239-1'))
        {
            $rolesPermitidos[] = 'ROLE_239-1';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_239-2017'))
        {
            $rolesPermitidos[] = 'ROLE_239-2017'; //Perfil para consultar Saldos de Gateway GSM
        }
        if(true === $this->get('security.context')->isGranted('ROLE_239-2018'))
        {
            $rolesPermitidos[] = 'ROLE_239-2018'; //Perfil para generar/exportar Reporte de Saldos de Gateway GSM
        }                
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");

        return $this->render('administracionBundle:ElementoGateway:index.html.twig', array(
                'item' => $entityItemMenu,
                'rolesPermitidos' => $rolesPermitidos
        ));
    }

    /**
     * Redirecciona a la ventana que en lista los Gateways existentes
     * @param integer $id   
     * @return twig de presentacion de elementos tipo Gateway existentes
     * @author arsuarez
     */
    /**
    * @Secure(roles="ROLE_46-6")
    */
    public function showAction($id)
    {    	
	
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");        
        $emcom = $this->getDoctrine()->getManager("telconet");        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");

        if (null == $vista = $em->find('schemaBundle:VistaGateways', $id)) {
           // throw new NotFoundHttpException('No existe el VistaGateways que se quiere mostrar');
        }
        
        $entityEmpresaElemento = new InfoEmpresaElemento();              
        
        $entity = $em->find('schemaBundle:InfoElemento',$id);                 
        
        $entityEmpresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneByElementoId($entity);                 
        
        if($entityEmpresaElemento){
	  $entityEmpresas = $emcom->find('schemaBundle:InfoEmpresaGrupo',$entityEmpresaElemento->getEmpresaCod()); 
	  $nombreEmpresa = $entityEmpresas->getNombreEmpresa();
	}else $nombreEmpresa="Todas las empresas";
		
        return $this->render('administracionBundle:ElementoGateway:show.html.twig', array(
            'item' => $entityItemMenu,
            'vista'   => $vista,
            'empresa' => $nombreEmpresa
        ));
    }
    /**
     * Redirecciona a la ventana que permite ingresar un nuevo Gateway     
     * @return twig de creacion de un nuevo elemento Gateway
     * @author arsuarez
     */
    /**
    * @Secure(roles="ROLE_46-2")
    */
    public function newAction()
    {        		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
        
        $entity = new InfoElemento();
        $entityIp = new InfoIp();
        $entityPass = new InfoContrasena();
        $entityUser = new AdmiUsuarioAcceso();
        $entityMarca = new AdmiMarcaElemento();
        $entityEmpresas = new InfoEmpresaGrupo();
        
        $form   = $this->createForm(new ElementoGatewayType(), $entity);
        $formIp = $this->createForm(new InfoIpType(), $entityIp);
        $formPass = $this->createForm(new InfoContrasenaType(), $entityPass);
        $formUser = $this->createForm(new AdmiUsuarioAccesoType(), $entityUser);
        $formMarca = $this->createForm(new MarcaElementoType(''), $entityMarca);
        $formEmpresa = $this->createForm(new EmpresasType(), $entityEmpresas);

        return $this->render('administracionBundle:ElementoGateway:new.html.twig', array(
            'item' => $entityItemMenu,            
            'form'   => $form->createView(),
            'formIp' => $formIp->createView(),
            'formPass' => $formPass->createView(),
            'formUser' => $formUser->createView(),
            'formMarca' => $formMarca->createView(),
            'formEmpresa' => $formEmpresa->createView(),
        ));
    }
    /**
     * Metodo encargado de guardar en la base de datos el Elemento Ingresado en el twig de creacion del mismo     
     * @return twig de presentacion de elementos tipo Gateway existentes
     * @author arsuarez
     */
    /**
    * @Secure(roles="ROLE_46-3")
    */
    public function createAction()
    {        		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request = $this->getRequest();
        
        $peticion = $this->get('request');
        
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
        
        $entity = new InfoElemento();
        $entityIp = new InfoIp();
        $entityPass = new InfoContrasena();
       // $entityUser = new AdmiUsuarioAcceso();
        $entityMarca = new AdmiMarcaElemento();               
        $entityElContra = new InfoElementoContrasena();      
        $entityDetaInter = new InfoDetalleInterface();  
        $entityInterElem = new InfoInterfaceElemento();  
        $entityEmpresaElemento = new InfoEmpresaElemento();
       // $entityAdmiModelUserAcceso = new AdmiModeloUsuarioAcceso();  
                        
        try{
		$marcaId =   $peticion->get('marcarId');   
		$nombreGw = $peticion->get('nombreGw');  
		$descriGw = $peticion->get('descriGw');  
		$ip= $peticion->get('ip');  
		$subred= $peticion->get('subred');  
		$gateway= $peticion->get('gateway');  
		//$usuario= $peticion->get('usuario');  
		$password= $peticion->get('password');  				  
		$interfaces= $peticion->get('interfaces');  
		$empresa = $peticion->get('empresa');
				
			      
		$marca = $em->find('schemaBundle:AdmiMarcaElemento', $marcaId);      
		$modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findOneByMarcaElementoId($marca);		      		
			
		//InfoElemento
		$entity->setNombreElemento($nombreGw);
		$entity->setDescripcionElemento($descriGw);
		$entity->setModeloElementoId($modelo);
		$entity->setEstado('Activo');
		$entity->setUsrResponsable($request->getSession()->get('user'));
		$entity->setFeCreacion(new \DateTime('now'));
		$entity->setUsrCreacion($request->getSession()->get('user'));
		$entity->setIpCreacion('127.0.0.1');
		
		$em->getConnection()->beginTransaction();
		
		$em->persist($entity);
		$em->flush();	
		
		//InfoEmpresaElemento
		$entityEmpresaElemento->setEmpresaCod($empresa);
		$entityEmpresaElemento->setElementoId($entity);
		$entityEmpresaElemento->setObservacion('Gateway GSM');
		$entityEmpresaElemento->setEstado('Activo');
		$entityEmpresaElemento->setUsrCreacion($request->getSession()->get('user'));
		$entityEmpresaElemento->setFeCreacion(new \DateTime('now'));
		$entityEmpresaElemento->setIpCreacion('0.0.0.0');
		
		$em->persist($entityEmpresaElemento);
		$em->flush();				
		
		//InfoIp
		$entityIp->setElementoId($entity->getId());
		$entityIp->setIp($ip);
		$entityIp->setGateway($gateway);
		$entityIp->setMascara($subred);
		$entityIp->setEstado('Activo');
		$entityIp->setVersionIp('IPV4');
		$entityIp->setTipoIp('PUBLICA');
		$entityIp->setFeCreacion(new \DateTime('now'));
		$entityIp->setUsrCreacion($request->getSession()->get('user'));
		$entityIp->setIpCreacion('127.0.0.1');
		
		$em->persist($entityIp);
		$em->flush();
		
		//echo 'infoIp : '.$entityIp->getId();
		
		//InfoContrasena
		$entityPass->setContrasena($password);
		$entityPass->setEstado('Activo');
		$entityPass->setFeCreacion(new \DateTime('now'));
		$entityPass->setUsrCreacion($request->getSession()->get('user'));
		$entityPass->setFeUltMod(new \DateTime('now'));
		$entityPass->setUsrUltMod($request->getSession()->get('user'));				
		
		$em->persist($entityPass);
		$em->flush();				
		
		//InfoElementoContrasena
		$entityElContra->setElementoId($entity);
		$entityElContra->setContrasenaId($entityPass);
		$entityElContra->setEstado('Activo');
		$entityElContra->setFeCreacion(new \DateTime('now'));
		$entityElContra->setIpCreacion('127.0.0.1');
		
		$em->persist($entityElContra);
		$em->flush();				
				
		$json = json_decode($interfaces);				
		
		$array = $json->caracteristicas;
		
		$i=1;
		$puertoInicial = '';
		
		if($array && count($array)>0)
		{
		     foreach($array as $interface)
		     {
		     
			  $puerto = $interface->puertos;
			  $detalleModulo = $interface->detalleModulo;
			  $valorModulo = $interface->valorModulo;
			  
			  if($puertoInicial!=$puerto){
			  
				  $entityInterElem = new InfoInterfaceElemento();
			  
				  //InfoInterfaceElemento
				  $entityInterElem->setElementoId($entity);
				  $entityInterElem->setNombreInterfaceElemento($puerto);
				  $entityInterElem->setDescripcionInterfaceElemento('Puerto '.$i);
				  $entityInterElem->setEstado('Activo');
				  $entityInterElem->setUsrCreacion($request->getSession()->get('user'));
				  $entityInterElem->setFeCreacion(new \DateTime('now'));
				  $entityInterElem->setIpCreacion('127.0.0.1');
				  
				  $em->persist($entityInterElem);
				  $em->flush();
				
				  $puertoInicial = $puerto;
				  
				  $i++;				  				  
				  
			  }else $puertoInicial = $puerto;	
			  			 
			  $entityDetaInter = new InfoDetalleInterface();
			  
			  $entityDetaInter->setInterfaceElementoId($entityInterElem);
			  $entityDetaInter->setDetalleNombre($detalleModulo);
			  $entityDetaInter->setDetalleValor($valorModulo);
			  $entityDetaInter->setUsrCreacion($request->getSession()->get('user'));
			  $entityDetaInter->setFeCreacion(new \DateTime('now'));
			  $entityDetaInter->setIpCreacion('127.0.0.1');
			  
			  $em->persist($entityDetaInter);
			  $em->flush();
			  			
			  			 
		     }		  
		}
				
		$em->getConnection()->commit();				
				
		$resultado = json_encode(array('success'=>true,
					      'id'=>$entity->getId()
					      ));		
		
        }catch (Exception $e) {
			// Rollback the failed transaction attempt				
			$em->getConnection()->rollback();
			$em->getConnection()->close();			
			$resultado = json_encode(array('success'=>false,'mensaje'=>$e));			
		}                
        
        $respuesta->setContent($resultado);
	
        return $respuesta;
        
    }
     /**
     * Redirecciona a la ventana que permite editar un nuevo Gateway ya ingresado
     * @return twig de edicion de elementos tipo Gateway existentes
     * @author arsuarez
    /**
    * @Secure(roles="ROLE_46-4")
    */
    public function editAction($id)
    {        			
	
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $emcom = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");

        if (null == $vista = $em->find('schemaBundle:InfoElemento', $id)) {
            //throw new NotFoundHttpException('No existe el AdmiArea que se quiere modificar');
        }
                  
	$entity = new InfoElemento();	
        $entityIp = new InfoIp();
        $entityPass = new InfoContrasena();
        $entityUser = new AdmiUsuarioAcceso();
        $entityMarca = new AdmiMarcaElemento();
        $entityModelo = new AdmiModeloElemento();
        $entityElContra = new InfoElementoContrasena();      
        $entityDetaInter = new InfoDetalleInterface();  
        $entityInterElem = new InfoInterfaceElemento();                  
        $entityEmpresas = new InfoEmpresaGrupo();
        $entityEmpresaElemento = new InfoEmpresaElemento();
                
        $entity = $em->find('schemaBundle:InfoElemento',$id);        
        
        $entityEmpresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneByElementoId($entity);   
        
        if($entityEmpresaElemento)
	    $entityEmpresas = $emcom->find('schemaBundle:InfoEmpresaGrupo',$entityEmpresaElemento->getEmpresaCod());                
        
        $entityIp = $em->getRepository('schemaBundle:InfoIp')->findOneByElementoId($entity);             
        $entityElContra= $em->getRepository('schemaBundle:InfoElementoContrasena')->findOneByElementoId($entity);        
        $entityPass = $em->find('schemaBundle:InfoContrasena',$entityElContra->getContrasenaId()->getId());       
        $entityModelo = $em->find('schemaBundle:AdmiModeloElemento',$entity->getModeloElementoId()->getId());        
        $entityMarca  = $em->find('schemaBundle:AdmiMarcaElemento',$entityModelo->getMarcaElementoId()->getId());   
        
        $form   = $this->createForm(new ElementoGatewayType(), $entity);
        $formIp = $this->createForm(new InfoIpType(), $entityIp);
        $formPass = $this->createForm(new InfoContrasenaType(), $entityPass);
        $formUser = $this->createForm(new AdmiUsuarioAccesoType(), $entityUser);
        $formMarca = $this->createForm(new MarcaElementoType(''), $entityMarca);
        $formEmpresa = $this->createForm(new EmpresasType(), $entityEmpresas);
                         
        return $this->render('administracionBundle:ElementoGateway:edit.html.twig', array(
            'item' => $entityItemMenu,            
            'form'   => $form->createView(),
            'formIp' => $formIp->createView(),
            'formPass' => $formPass->createView(),
            'formUser' => $formUser->createView(),
            'formMarca' => $formMarca->createView(),
            'formEmpresa'=>$formEmpresa->createView(),            
            'id'=>$id
        ));
    }
     /**
     * Metodo encargado de realizar la edicion de Elementos modificados en el twig de edicion
     * @param integer $id
     * @return twig de presentacion de elementos tipo Gateway modificados
     * @author arsuarez
     */
    /**
    * @Secure(roles="ROLE_46-5")
    */
    public function updateAction($id)
    {        
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request = $this->getRequest();
        
        $peticion = $this->get('request');
		
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emcom = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");                
                  
	$entity = new InfoElemento();	
        $entityIp = new InfoIp();
        $entityPass = new InfoContrasena();
        $entityUser = new AdmiUsuarioAcceso();
        $entityMarca = new AdmiMarcaElemento();
        $entityModelo = new AdmiModeloElemento();
        $entityElContra = new InfoElementoContrasena();      
        $entityDetaInter = new InfoDetalleInterface();  
        $entityInterElem = new InfoInterfaceElemento();       
        $entityEmpresas = new InfoEmpresaGrupo();
        $entityEmpresaElemento = new InfoEmpresaElemento();
        
        try{
		$id = $peticion->get('id');   
		$marcaId =  $peticion->get('marcarId');   
		$nombreGw = $peticion->get('nombreGw');  
		$descriGw = $peticion->get('descriGw');  
		$ip= $peticion->get('ip');  
		$subred= $peticion->get('subred');  
		$gateway= $peticion->get('gateway');  
		//$usuario= $peticion->get('usuario');  
		$password= $peticion->get('password');  				  
		$interfaces= $peticion->get('interfaces');  
		$empresa = $peticion->get('empresa'); //Codigo Empresa										
		
		$entity = $em->find('schemaBundle:InfoElemento',$id);        
		
		$entityEmpresaElemento = $em->getRepository('schemaBundle:InfoEmpresaElemento')->findOneByElementoId($entity);    
		
		//if($entityEmpresaElemento)$entityEmpresas = $emcom->find('schemaBundle:InfoEmpresaGrupo',$empresa); 
		
		$entityIp = $em->getRepository('schemaBundle:InfoIp')->findOneByElementoId($entity);           
		$entityElContra= $em->getRepository('schemaBundle:InfoElementoContrasena')->findOneByElementoId($entity);        
		$entityPass = $em->find('schemaBundle:InfoContrasena',$entityElContra->getContrasenaId()->getId());  
		$entityModelo = $em->find('schemaBundle:AdmiModeloElemento',$entity->getModeloElementoId()->getId());        
		$entityMarca  = $em->find('schemaBundle:AdmiMarcaElemento',$entityModelo->getMarcaElementoId()->getId());			      
		$marca = $em->find('schemaBundle:AdmiMarcaElemento', $marcaId);      
		$modelo = $em->getRepository('schemaBundle:AdmiModeloElemento')->findOneByMarcaElementoId($marca);		      		
			
		//InfoElemento
		$entity->setNombreElemento($nombreGw);
		$entity->setDescripcionElemento($descriGw);
		$entity->setModeloElementoId($modelo);
		//$entity->setEstado('Activo');
		//$entity->setUsrResponsable($request->getSession()->get('user'));
		//$entity->setFeCreacion(new \DateTime('now'));
		//$entity->setUsrCreacion($request->getSession()->get('user'));
		//$entity->setIpCreacion('127.0.0.1');
		
		$em->getConnection()->beginTransaction();
		
		$em->persist($entity);
		$em->flush();		
		
		//InfoEmpresaElemento
		if($entityEmpresaElemento)
		    $entityEmpresaElemento->setEmpresaCod($empresa);
		else{
		    $entityEmpresaElemento = new InfoEmpresaElemento();
		    $entityEmpresaElemento->setEmpresaCod($empresa);
		    $entityEmpresaElemento->setElementoId($entity);
		    $entityEmpresaElemento->setObservacion('Gateway GSM');
		    $entityEmpresaElemento->setEstado('Activo');
		    $entityEmpresaElemento->setUsrCreacion($request->getSession()->get('user'));
		    $entityEmpresaElemento->setFeCreacion(new \DateTime('now'));
		    $entityEmpresaElemento->setIpCreacion('0.0.0.0');						
		}
		
		$em->persist($entityEmpresaElemento);
		$em->flush();	
				
		
		//InfoIp
		$entityIp->setElementoId($entity->getId());
		$entityIp->setIp($ip);
		$entityIp->setGateway($gateway);
		$entityIp->setMascara($subred);
		$entityIp->setEstado('Activo');
		$entityIp->setVersionIp('IPV4');
		$entityIp->setTipoIp('PUBLICA');
		$entityIp->setFeCreacion(new \DateTime('now'));
		$entityIp->setUsrCreacion($request->getSession()->get('user'));
		$entityIp->setIpCreacion('127.0.0.1');
		
		$em->persist($entityIp);
		$em->flush();
				
		
		//echo 'infoIp : '.$entityIp->getId();
		
		//InfoContrasena
		$entityPass->setContrasena($password);
		$entityPass->setEstado('Activo');
		$entityPass->setFeCreacion(new \DateTime('now'));
		$entityPass->setUsrCreacion($request->getSession()->get('user'));
		$entityPass->setFeUltMod(new \DateTime('now'));
		$entityPass->setUsrUltMod($request->getSession()->get('user'));				
		
		$em->persist($entityPass);
		$em->flush();				
		
		/*//InfoElementoContrasena
		$entityElContra->setElementoId($entity);
		$entityElContra->setContrasenaId($entityPass);
		$entityElContra->setEstado('Activo');
		$entityElContra->setFeCreacion(new \DateTime('now'));
		$entityElContra->setIpCreacion('127.0.0.1');
		
		$em->persist($entityElContra);
		$em->flush();		*/	
				
				
		$json = json_decode($interfaces);				
		
		$array = $json->caracteristicas;
		
		$i=1;
		$puertoInicial = '';
				
		if($array && count($array)>0)
		{
		     foreach($array as $interface)
		     {
		     
			  $puerto = $interface->puertos;
			  $detalleModulo = $interface->detalleModulo;
			  $valorModulo = $interface->valorModulo;
			  $idInterfaceElemento = $interface->idInterfaceElemento;
			  $idDetalleInterface  = $interface->idDetalleInterface;	
			  			
			  if($puertoInicial!=$puerto){
			  				  
				  $entityInterElem = $em->find('schemaBundle:InfoInterfaceElemento',$idInterfaceElemento); 
				  
				  if($entityInterElem==null)
					      $entityInterElem = new InfoInterfaceElemento();
					      				
				  $entityInterElem->setElementoId($entity);
				  $entityInterElem->setNombreInterfaceElemento($puerto);
				  $entityInterElem->setDescripcionInterfaceElemento('Puerto '.$i);
				  $entityInterElem->setEstado('Activo');
				  $entityInterElem->setUsrCreacion($request->getSession()->get('user'));
				  $entityInterElem->setFeCreacion(new \DateTime('now'));
				  $entityInterElem->setIpCreacion('127.0.0.1');
				  
				  $em->persist($entityInterElem);
				  $em->flush();				  				  
				
				  $puertoInicial = $puerto;
				  
				  $i++;				  				  
				  
			  }else $puertoInicial = $puerto;	
			  
			  $entityDetaInter = new InfoDetalleInterface();
			  			 			  
			  $entityDetaInter = $em->find('schemaBundle:InfoDetalleInterface',$idDetalleInterface); 
			  
			  if($entityDetaInter == null)$entityDetaInter = new InfoDetalleInterface();
			  
			  $entityDetaInter->setInterfaceElementoId($entityInterElem);
			  $entityDetaInter->setDetalleNombre($detalleModulo);
			  $entityDetaInter->setDetalleValor($valorModulo);
			  $entityDetaInter->setUsrCreacion($request->getSession()->get('user'));
			  $entityDetaInter->setFeCreacion(new \DateTime('now'));
			  $entityDetaInter->setIpCreacion('127.0.0.1');
			  
			  $em->persist($entityDetaInter);
			  $em->flush();
			  			
			  			 
		     }		  
		}
				
		$em->getConnection()->commit();						
				
		$resultado = json_encode(array('success'=>true,'id'=>$id));		
		
        }catch (Exception $e) {						
			$em->getConnection()->rollback();
			$em->getConnection()->close();			
			$resultado = json_encode(array('success'=>false,'mensaje'=>$e));			
		}                
        
        $respuesta->setContent($resultado);
	
        return $respuesta;
    }
      /**
     * Metodo encargado de eliminar un elemento seleccionado , cambiando de estado Activo a Eliminado
     * @param integer $id
     * @return twig de presentacion de elementos con estado actualizado
     * @author arsuarez
     */
    /**
    * @Secure(roles="ROLE_46-8")
    */
    public function deleteAction($id){

	$request = $this->getRequest();

	$em = $this->getDoctrine()->getManager('telconet_infraestructura');
	$entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

	if (!$entity) {
	    throw $this->createNotFoundException('Unable to find InfoElemento entity.');
	}
	$estado = 'Eliminado';
	$entity->setEstado($estado);                                  
	$em->persist($entity);			            
	$em->flush();

        return $this->redirect($this->generateUrl('elementogateway'));
    }

    /**
    * @Secure(roles="ROLE_46-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoElemento', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");					
					$em->persist($entity);
					$em->flush();
                }
				
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;        
        
        return $respuesta;
    }
    
    
          /**
     * Metodo encargado de eliminar un elemento seleccionado , cambiando de estado Activo a Eliminado
     * @param integer $id
     * @return twig de presentacion de elementos con estado actualizado
     * @author arsuarez
     */   
    public function habilitarAction(){
    
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

	$request = $this->get('request');
	
	$id = $request->get('param');

	$em = $this->getDoctrine()->getManager('telconet_infraestructura');
	
	$entity = $em->getRepository('schemaBundle:InfoElemento')->find($id);

	if (!$entity) {
	    throw $this->createNotFoundException('Unable to find InfoElemento entity.');
	}
	
	$msg="";
	
	if($entity->getEstado()=='Activo'){$estado = 'Inactivo';$msg='Inactivado';}
	else if($entity->getEstado()=='Inactivo'){$estado = 'Activo';$msg='Activado';}
	
	//$estado = 'Eliminado';
	$entity->setEstado($estado);                                  
	$em->persist($entity);			            
	$em->flush();

        //return $this->redirect($this->generateUrl('elementogateway'));
        $respuesta->setContent($msg);
        return $respuesta;
    }
    
    /**
     * Metodo encargado de llenar el grid de consultas de elementos Gateway a partir de la Vista creada     
     * @return twig de presentacion de elementos tipo Gateway modificados
     * @author arsuarez
     */
    /**
    * @Secure(roles="ROLE_46-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $em_com = $this->getDoctrine()->getManager("telconet");
        
        $peticion = $this->get('request');
        
	$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        $marca = $peticion->query->get('marca');
        $empresa = $peticion->query->get('empresa');
               
        if($marca!='Todos')
	$marcaElemento = $em->find('schemaBundle:AdmiMarcaElemento', $marca);
	else $marcaElemento = '';
	
	if($empresa && $empresa!='Todos')
	     $empresaCod = $empresa;
	else $empresaCod = 'Todos';
		               
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
         $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:VistaGateways')
            ->generarJsonGateways($em_com,$nombre,$estado,$start,$limit,$marcaElemento,$empresaCod);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }	   
    
    
    /**
    * Metodo encargado de obtener los saldos de cada puerto de los equipos
    * 
    * @return json con los resultados
    * 
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.0 09-12-2014 Version Inicial       
    *      
    * @Secure(roles="ROLE_239-2017")
    */
    public function gridSaldosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
                        
        $peticion = $this->get('request');
        
        $strIp = $peticion->query->get('ip')?$peticion->query->get('ip'):'Todos'; 
        
        if($strIp!='Todos')
        {
            $objIp = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array('ip'=>$strIp));
            
            if($objIp)
            {
                $strIp = $objIp->getElementoId();
            }
        }
		               
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
         $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")->getRepository('schemaBundle:VistaGateways')
                                                                                ->generarJsonSaldoInterface($strIp,$start,$limit);
         
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }	   
    
    
    
      /**
     * Metodo encargado de obtener la Marca de Elementos tipo Gateway     
     * @return json con la data obtenido en la consulta
     * @author arsuarez
     */
    public function getMarcaElementoAction(){
	
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
               
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
         $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:VistaGateways')
            ->generarJsonMarcaElemento($start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    
    }
      /**
     * Metodo encargado de obtener las interfaces y los detalles de interfaces de los elementos seleccionados
     * @param integer $id
     * @return json con la data obtenida en la consulta
     * @author arsuarez
     */
    public function getInterfaceElementosAction($id){
    	
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
               
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
         $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:VistaGateways')
            ->generarJsonInterfaceElementos($start,$limit,$id);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    
    }
    
      /**
     * Metodo encargado de obtener las empresas existentes
     * @param integer $id
     * @return json con la data obtenida en la consulta
     * @author arsuarez
     */
    public function getEmpresasAction(){
    	
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
               
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
         $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoEmpresaGrupo')
            ->generarJson('','Activo',$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    
    }
    
    /**
    * getSaldosAction
    *
    * Metodo encargado de ejecutar script de obtencion de saldos de los equipos                                        
    *
    * @return respuesta de ejecucion OK
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 10-12-2014
    */
    public function getSaldosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $elementoId = $peticion->get('elementoId')?$peticion->get('elementoId'):' ';
        
        $strScript = '/home/scripts-telcos/md/soporte/sources/sms-server/dist/SMSServer.jar';
        
        $tipoEvento = 'saldo';
        $path    = $this->container->getParameter('path_telcos');
        $host    = $this->container->getParameter('host_scripts');
        $empresa = $peticion->getSession()->get('idEmpresa');
        $strPathJava = $this->container->getParameter('path_java_soporte');
        $strScriptPathJava = $this->container->getParameter('path_script_java_soporte');

        $strParametros = $tipoEvento . "|" . $path . "|" . $host . "|" . $empresa . "|" . $elementoId . "|";                

        $strEsperaRespuesta = 'NO';

        $strComunicacion = "telcos/app/Resources/scripts/TelcosComunicacionScripts.jar";
        $strLogScript = "/home/telcos/app/Resources/scripts/log/log.txt";

        $strComando = "nohup ".$strPathJava." -jar -Djava.security.egd=file:/dev/./urandom " . $path . $strComunicacion." '" .
            $strScript . "' '" . $strParametros . "' '" . $strEsperaRespuesta . "' '" . $host . "' '" .
            $strScriptPathJava."' >> ".$strLogScript." &";                        

        shell_exec($strComando);
        
        $resultado = json_encode(array('mensaje'=>'Se obtendran saldos'));
        
        $respuesta->setContent($resultado);

        return $respuesta;
    }
    
    /**
      * exportarConsultaAction
      *
      * Metodo encargado de realizar la exportación a excel de los equipos con sus respectivos numeros                                        
      *
      * @return xls de salida
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 01-12-2014        
      */
    public function exportarConsultaAction()
    {                   
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        $em_com = $this->getDoctrine()->getManager("telconet");

        $peticion = $this->get('request');

        $queryNombre = $peticion->get('query') ? $peticion->get('query') : "";
        $nombre      = ($queryNombre != '' ? $queryNombre : $peticion->get('nombre_hidden'));
        $estado      = $peticion->get('estado_hidden');
        $marca       = $peticion->get('marca_hidden');
        $empresa     = $peticion->get('empresa_hidden');          
        
        $marcaElemento  = '';
        $empresaCod     = 'Todos';
        $strNombre      = 'Todos';
        $strNombreMarca = 'Todos';

        if($marca && $marca != 'Todos')
        {
            $marcaElemento = $em->find('schemaBundle:AdmiMarcaElemento', $marca);
            
            if($marcaElemento)
            {
                $strNombreMarca = $marcaElemento->getDescripcionMarcaElemento();
            }
        }
        
        if($empresa && $empresa != 'Todos')
        {
            $empresaCod = $empresa;
            $objEmpresa = $em_com->getRepository('schemaBundle:InfoEmpresaGrupo')->find($empresaCod);
            if($objEmpresa)
            {
                $strNombre = $objEmpresa->getNombreEmpresa();
            }
        }                            
        
        $parametros = array();        
        
        $parametros['marca']   = $strNombreMarca;
        $parametros['empresa'] = $strNombre;
        $parametros['nombre']  = $nombre;
        $parametros['estado']  = $estado;                           
        $parametros['usuario'] = $peticion->getSession()->get('user');              
              
        $objJson = $em->getRepository('schemaBundle:VistaGateways')
                      ->getEntidades($nombre, $estado, '', '', $marcaElemento, $empresaCod);                

        $this->exportarConsultaGateways($objJson, $parametros , $em_com , $em);

    }
    
      /**
      * exportarConsultaGateways
      *
      * Metodo encargado de realizar la exportación a excel de los equipos con sus respectivos numeros                                         
      *
      * @return xls de salida
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 01-12-2014
      */

    public function exportarConsultaGateways($data,$parametros,$emComercial,$emInfraestructura)
    {         	
    
        error_reporting(E_ALL);
                
        $objPHPExcel = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
                                
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateConsultaEquipos.xls");               
                
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($parametros['usuario']);
        $objPHPExcel->getProperties()->setTitle("Consulta Gateways GSM");
        $objPHPExcel->getProperties()->setSubject("Consulta Gateways GSM");
        $objPHPExcel->getProperties()->setDescription("Resultado de busqueda de Gateways GSM.");
        $objPHPExcel->getProperties()->setKeywords("Gateways GSM");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',''.$parametros['usuario']);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
               
        
        $objPHPExcel->getActiveSheet()->setCellValue('B8' ,''.$parametros['nombre']?$parametros['nombre']:'');                 
        $objPHPExcel->getActiveSheet()->setCellValue('B9' ,''.$parametros['marca']?$parametros['marca']:'Todos');
        $objPHPExcel->getActiveSheet()->setCellValue('E8' ,''.$parametros['estado']?$parametros['estado']:'Todos');
        $objPHPExcel->getActiveSheet()->setCellValue('E9' ,''.$parametros['empresa']?$parametros['empresa']:'Todos');                         	                                                   
                
        $i=15;                
        
        $intContInterfaces = 0;
        
        foreach($data as $gateway):
            
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$gateway->getNombreMarcaElemento());                 
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$gateway->getNombreModeloElemento());                 
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$gateway->getNombreElemento());
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$gateway->getIp());        		
            
            $empresa = $emComercial->find('schemaBundle:InfoEmpresaGrupo',$gateway->getEmpresaCod()); 
            
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$empresa->getNombreEmpresa());       
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$gateway->getEstado());  
            
            $objPHPExcel->getActiveSheet()->getStyle('A' . $i)->getFont()->getColor()->setARGB('#006600');
            $objPHPExcel->getActiveSheet()->getStyle('B' . $i)->getFont()->getColor()->setARGB('#006600');
            $objPHPExcel->getActiveSheet()->getStyle('C' . $i)->getFont()->getColor()->setARGB('#006600');
            $objPHPExcel->getActiveSheet()->getStyle('D' . $i)->getFont()->getColor()->setARGB('#006600');
            $objPHPExcel->getActiveSheet()->getStyle('E' . $i)->getFont()->getColor()->setARGB('#006600');
            $objPHPExcel->getActiveSheet()->getStyle('F' . $i)->getFont()->getColor()->setARGB('#006600');
            
            //Se hace la busqueda de las interfaces ( Numeros relacionados a cada equipo )
            $arrayInterfaces = $emInfraestructura->getRepository('schemaBundle:VistaGateways')->getInterfaces('','',$gateway->getId());
            
            if($arrayInterfaces && count($arrayInterfaces)>0)
            {
                $intContInterfaces = count($arrayInterfaces)+1;
                
                $j=$i+1;
                $intContDinstarPorts = 0;
                
                foreach($arrayInterfaces as $interfaces):
                    
                    if($gateway->getNombreMarcaElemento()=='DINSTAR') //Equipo DINSTAR
                    {
                        if($intContDinstarPorts<8)
                        {
                            $objPHPExcel->getActiveSheet()->setCellValue('G'.$j,$interfaces['detalleValor']); 
                            $objPHPExcel->getActiveSheet()->getStyle('G' . $j)->getFont()->getColor()->setARGB('#FF0000');
                        }
                        else
                        {
                            $objPHPExcel->getActiveSheet()->setCellValue('H'.$j,$interfaces['detalleValor']);
                            $objPHPExcel->getActiveSheet()->getStyle('H' . $j)->getFont()->getColor()->setARGB('#0000FF');
                        }
                        $intContDinstarPorts = $intContDinstarPorts + 1;
                    }
                    else if($gateway->getNombreMarcaElemento()=='PORTECH')
                    {                        
                        if($interfaces['detalleNombre'] == 'module1') //Puertos con CHIPS claro
                        {                            
                            $objPHPExcel->getActiveSheet()->setCellValue('G'.$j,$interfaces['detalleValor']);
                            $objPHPExcel->getActiveSheet()->getStyle('G' . $j)->getFont()->getColor()->setARGB('#FF0000');
                        }
                        else //Puertos con CHIPS movistar
                        {
                            $objPHPExcel->getActiveSheet()->setCellValue('H'.$j,$interfaces['detalleValor']);
                            $objPHPExcel->getActiveSheet()->getStyle('H' . $j)->getFont()->getColor()->setARGB('#0000FF');
                        }
                    }                                                                          
                
                    $j = $j + 1;
                    
                endforeach;
            }
            else
            {
                $intContInterfaces = 1;
            }

     		$i = $i +$intContInterfaces; 		 		

        endforeach;


        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');

        header('Content-Disposition: attachment;filename="Consulta_Gateways_GSM_'.date('d_M_Y').'.xls"');

        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;    
    
    }
    
    
    /**
      * exportarConsultaSaldos
      *
      * Metodo encargado de realizar la exportación a excel del saldo de cada equipo                                        
      *
      * @return xls de salida
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 10-12-2014
      *
      * @Secure(roles="ROLE_239-2018")      
     */
    public function exportarConsultaSaldosAction()
    {                   
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $peticion = $this->get('request');
        
        $strIp       = $peticion->get('ip_hidden')?$peticion->get('ip_hidden'):'Todos';   
        
        $intElementoId='Todos';
        
        if($strIp!='Todos')
        {
            $objIp = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array('ip'=>$strIp));
            
            if($objIp)
            {
                $intElementoId = $objIp->getElementoId();
            }
        }                                            
        
        $parametros = array();        
        
        $parametros['ip']   = $strIp;                      
        $parametros['usuario'] = $peticion->getSession()->get('user');              
              
        $arraySaldos = $em->getRepository('schemaBundle:VistaGateways')
                      ->generarJsonSaldoInterface($intElementoId, '', '',false);                

        $this->exportarConsultaSaldos($arraySaldos, $parametros, "Todos",false,'');

    }
    
    /**
      * exportarConsultaSaldos
      *
      * Metodo encargado de realizar la exportación a excel de los saldos de cada puerto de cada equipo                                         
      *
      * @return xls de salida
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 10-12-2014
      */

    public function exportarConsultaSaldos($data,$parametros,$operadora,$boolSeAjuntaCorreo,$arrayExtraParams)
    {         	    
        error_reporting(E_ALL);
                
        $objPHPExcel = new PHPExcel();
       
        $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array( ' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
                                
        $objPHPExcel = $objReader->load(__DIR__."/../Resources/templatesExcel/templateConsultaSaldoEquipos.xls");               
                
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($parametros['usuario']);
        $objPHPExcel->getProperties()->setTitle("Consulta Saldos Gateways GSM");
        $objPHPExcel->getProperties()->setSubject("Consulta Saldos Gateways GSM");
        $objPHPExcel->getProperties()->setDescription("Resultado de Saldos Gateways GSM.");
        $objPHPExcel->getProperties()->setKeywords("Saldos Gateways GSM");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('C3',''.$parametros['usuario']);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel( gmmktime(0,0,0,date('m'),date('d'),date('Y')) ));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);
               
        
        $objPHPExcel->getActiveSheet()->setCellValue('B8' ,''.$parametros['ip']?$parametros['ip']:'Todos');                                              	                                                   
                
        $i=13;                                
        
        foreach($data as $saldo):
            
            if($operadora==$saldo['operadora'] || $operadora=="Todos" || $saldo['operadora']=='')
            {
            
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$saldo['ip']);                 
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$saldo['puerto']);                 
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$saldo['numero']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$saldo['operadora']);        		
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$saldo['saldo']);    

                if($saldo['puerto']=='')
                {                
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                             'color' => array('rgb' => 'CCFFCC'))));
                    $objPHPExcel->getActiveSheet()->getStyle('B' . $i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                             'color' => array('rgb' => 'CCFFCC'))));
                    $objPHPExcel->getActiveSheet()->getStyle('C' . $i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                             'color' => array('rgb' => 'CCFFCC'))));
                    $objPHPExcel->getActiveSheet()->getStyle('D' . $i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                             'color' => array('rgb' => 'CCFFCC'))));
                    $objPHPExcel->getActiveSheet()->getStyle('E' . $i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                             'color' => array('rgb' => 'CCFFCC'))));
                    $objPHPExcel->getActiveSheet()->getStyle('F' . $i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                             'color' => array('rgb' => 'CCFFCC'))));
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                             'color' => array('rgb' => 'CCFFCC'))));
                    $objPHPExcel->getActiveSheet()->getStyle('H' . $i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                             'color' => array('rgb' => 'CCFFCC'))));
                    $objPHPExcel->getActiveSheet()->getStyle('I' . $i)->applyFromArray(array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                                                             'color' => array('rgb' => 'CCFFCC'))));
                }                     

                $i = $i + 1;	 
            }

        endforeach;


        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        if($boolSeAjuntaCorreo)
        {
            //Cuando se requiere crear y adjuntar a correo el reporte generado
            $fileName = $arrayExtraParams['fileName'];
        }
        else
        {
            //EL file name por default cuando se requiere exportar el excel solamente
            $fileName = "Consulta_Saldos_Gateways_GSM_".date('d_M_Y').".xls";
        }

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');

        header('Content-Disposition: attachment;filename='.$fileName);

        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                       
        if($boolSeAjuntaCorreo)
        {
            $pathFile = $arrayExtraParams['pathFile'];
            $objWriter->save($pathFile.$fileName);
        }
        else
        { 
            $objWriter->save('php://output');   
        }                                            
    }
    
    /**
    * enviarReporteSaldosAction
    *
    * Metodo encargado de realizar el envio del reporte en excel generado a los correos que se designen para la gestion de los mismos                                         
    *
    * @return respuesta
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
     * 
    * @version 1.0 11-12-2014
    *      
    * @Secure(roles="ROLE_239-2018")
    */
    public function enviarReporteSaldosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $peticion = $this->get('request');
        
        /* @var $envioPlantilla EnvioPlantilla */
        $envioPlantilla = $this->get('soporte.EnvioPlantilla');
        
        //Asunto del correo a ser enviado
        $asuntoCorreo = "Reporte de Saldos de CHIPS ";   
        
        //File Name de xls de reporte a adjuntar
        $strFileXlsName = "Consulta_Saldos_Gateways_GSM_".date('d_M_Y').".xls";
        
        //Path donde se tomara y se creará el xls a enviar
        $strPathFile    = $this->container->getParameter('path_telcos')."telcos/src/telconet/administracionBundle/Resources/templatesExcel/";                                                  
        
        try
        {        
            $tipo           = $peticion->get('tipo');
            $correoClaro    = $peticion->get('correoClaro');
            $correoMovistar = $peticion->get('correoMovistar');        
            $strIp          = $peticion->get('elementoId')?$peticion->get('elementoId'):'Todos';   

            $intElementoId='Todos';

            if($strIp!='Todos')
            {
                $objIp = $em->getRepository('schemaBundle:InfoIp')->findOneBy(array('ip'=>$strIp));

                if($objIp)
                {
                    $intElementoId = $objIp->getElementoId();
                }
            }

            $arraySaldos = $em->getRepository('schemaBundle:VistaGateways')
                          ->generarJsonSaldoInterface($intElementoId, '', '',false);

            $parametros = array();        

            $parametros['ip']      = $strIp;                      
            $parametros['usuario'] = 'telcos';
            
            
            //Array de parametros a enviar al generador del xls y al gestor de envio de correos
            $arrayExtraParams = array();
            
            $arrayExtraParams['data']       = $arraySaldos;
            $arrayExtraParams['xlsParams']  = $parametros;                      
            $arrayExtraParams['service']    = $envioPlantilla;                                  
            $arrayExtraParams['pathFile']   = $strPathFile;
            $arrayExtraParams['fileName']   = $strFileXlsName;

            //Se envia reporte a la operadora escogida
            if($tipo!='Todos')
            {                        
                $to[] = $tipo=='CLARO'?$correoClaro:$correoMovistar;
                
                $arrayExtraParams['asuntoCorreo']  = $asuntoCorreo.$tipo;
                $arrayExtraParams['destinatario']  = $to;
                $arrayExtraParams['operadora']     = array('operadora' => $tipo);                

                $this->envioCorreoReporteSaldos($arrayExtraParams);
                
                unlink($strPathFile.$strFileXlsName);
            }
            else
            {
                //Se envia reporte Claro
                $to[] = $correoClaro;
                
                $arrayExtraParams['asuntoCorreo']  = $asuntoCorreo.'CLARO';
                $arrayExtraParams['destinatario']  = $to;
                $arrayExtraParams['operadora']     = array('operadora' => 'CLARO');  

                $this->envioCorreoReporteSaldos($arrayExtraParams);
                
                // se borra el reporte ya que el mismo puede ser generado en cualquier rato
                unlink($strPathFile.$strFileXlsName);

                $to = array();
                
                //Se envia reporte Movistar                                                
                $to[] = $correoMovistar;
                
                $arrayExtraParams['asuntoCorreo']  = $asuntoCorreo.'MOVISTAR';
                $arrayExtraParams['destinatario']  = $to;
                $arrayExtraParams['operadora']     = array('operadora' => 'MOVISTAR');

                $this->envioCorreoReporteSaldos($arrayExtraParams);
                
                // se borra el reporte ya que el mismo puede ser generado en cualquier rato
                unlink($strPathFile.$strFileXlsName);
            }

            $resultado = json_encode(array('mensaje'=>'Reporte enviado exitosamente'));
                        
        }catch(\Exception $e)
        {            
            $resultado = json_encode(array('mensaje'=>$e->getMessage()));
        }
        
        $respuesta->setContent($resultado);

        return $respuesta;
    }
    
   /**
    * envioCorreoReporteSaldos
    *
    * Metodo encargado realizar generar el reporte en xls y luego despacharlo via correo como archivo adjunto       
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
     * 
    * @version 1.0 11-12-2014
    */
    private function envioCorreoReporteSaldos($arrayParams)
    {                
        $this->exportarConsultaSaldos($arrayParams['data'], $arrayParams['xlsParams'], $arrayParams['operadora']['operadora'] , true , $arrayParams);

        //Se envia el correo respectivo al destinatario ingresado
        $arrayParams['service']->generarEnvioPlantilla( $arrayParams['asuntoCorreo'],
                                                        $arrayParams['destinatario'],
                                                        'SALDOS',
                                                        $arrayParams['operadora'],
                                                        '',
                                                        '',
                                                        '',$arrayParams['pathFile'].$arrayParams['fileName'],false);
    }
    

}
