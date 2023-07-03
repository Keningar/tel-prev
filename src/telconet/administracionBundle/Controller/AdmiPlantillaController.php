<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

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

class AdmiPlantillaController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_237-1")
    */
    public function indexAction()
    {
    
	$rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_237-6'))
	{
	    $rolesPermitidos[] = 'ROLE_237-6';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_237-5'))
	{
	    $rolesPermitidos[] = 'ROLE_237-5';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_237-4'))
	{
	    $rolesPermitidos[] = 'ROLE_237-4';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_237-8'))
	{
	    $rolesPermitidos[] = 'ROLE_237-8';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_237-1'))
	{
	    $rolesPermitidos[] = 'ROLE_237-1';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_237-2'))
	{
	    $rolesPermitidos[] = 'ROLE_237-2';
	}
            
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");			       
				
        return $this->render('administracionBundle:AdmiPlantilla:index.html.twig', array(
            'item' => $entityItemMenu,
            'rolesPermitidos' => $rolesPermitidos           
        ));
    }
    
    /**
    * @Secure(roles="ROLE_237-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emComercial = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");

        if (null == $plantilla = $em->find('schemaBundle:AdmiPlantilla', $id)) {
            throw new NotFoundHttpException('No existe Plantilla que se quiere mostrar');
        }
			
		
        return $this->render('administracionBundle:AdmiPlantilla:show.html.twig', array(
            'item' => $entityItemMenu,
            'plantilla'   => $plantilla	             
        ));
    }
    
    /**
    * newAction
    *
    * Método encargado de crear plantilla de correo   
    *
    * @version 1.0 Version Inicial
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.1 - Se agrega la variable del código de plantilla generado por 
    *                el proceso ECUCERT
    * @since 1.0
    *    
    * @Secure(roles="ROLE_237-2")
    */
    public function newAction()
    {
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");  
        $emComunicacion             = $this->get('doctrine')->getManager('telconet_comunicacion'); 
        $objPeticion                = $this->get('request');
        $strBanderaEcu              = 0;
        $strCodPlantilla            = $objPeticion->query->get('codigo')?
                                      $objPeticion->query->get('codigo'):'';
        $intCodEmpresa              = $objPeticion->getSession()->get('idEmpresa');
        $objParametroDetEstadoInc   = "";
        $strEstado                  = "Activo";
        $strDescripBasePlantilla    = "BASE DE PLANTILLA ECUCERT";
        $strCorreo                  = "";
        $strEmpresa                 = "";
        $strNombreCateg             = "";
        $arrayAlias                 = array();

        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");
        
        $entity = new AdmiPlantilla(); 
        if(!empty($strCodPlantilla))
        { 
            $arrayParamEcucert  = array(
                'nombreParametro' => "PLANTILLAS DE NOTIFICACIONES",
                'estado'          => $strEstado
            );

            $entityParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneByNombreParametro($arrayParamEcucert);

            $intIdParametrosECU = 0;
            if( isset($entityParametroCab) && !empty($entityParametroCab) )
            {
                $intIdParametrosECU = $entityParametroCab->getId();
            }

            $arrayParametrosDet  = array( 
                        'estado'      => $strEstado, 
                        'parametroId' => $intIdParametrosECU,
                        'descripcion' => $strDescripBasePlantilla,
                        'empresaCod'  => $intCodEmpresa
                    );

            $objParametroDetEstadoInc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametrosDet);

            if(is_object($objParametroDetEstadoInc))
            {
                $strCorreo    = $objParametroDetEstadoInc->getValor2();
                $strEmpresa   = $objParametroDetEstadoInc->getValor3();

                $entityAdmiAlias = $emComunicacion->getRepository('schemaBundle:AdmiAlias')
                                            ->findBy(array(
                                                        'valor'        => 'encargados_seguridad@netlife.net.ec',
                                                        'empresaCod'   => $intCodEmpresa,
                                                        'estado'       => $strEstado
                                                        )
                                                );
                if(is_array($entityAdmiAlias))
                {
                    foreach($entityAdmiAlias as $objAlias)
                    {
                        array_push($arrayAlias,
                            array('id_alias' => $objAlias->getId(),
                                'esCC'     => 'NO'
                                )
                        );
                    }
                }        
            }

            $arrayParametrosCatDet  = array( 
                'estado'      => $strEstado, 
                'parametroId' => $intIdParametrosECU,
                'valor4'      => $strCodPlantilla,
                'empresaCod'  => $intCodEmpresa
            );

            $objParametroDetNomCat = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy($arrayParametrosCatDet);

            if(is_object($objParametroDetNomCat))
            {
                $strNombreCateg = $objParametroDetNomCat->getValor1();
            }

            $strBanderaEcu              = 1;
            $entity->setNombrePlantilla("PLANTILLA DE NOTIFICACION ".$strCodPlantilla);  
            $entity->setCodigo($strCodPlantilla);  
            $entity->setModulo("SOPORTE"); 
            
        }       
        $form   = $this->createForm(new AdmiPlantillaType(),$entity);
        
        return $this->render('administracionBundle:AdmiPlantilla:new.html.twig', array(
            'item' => $entityItemMenu,            
            'form'   => $form->createView(),
            'banderaEcucert'  => $strBanderaEcu,
            'plantillaBase'   => $objParametroDetEstadoInc,
            'nombreCategoria' => $strNombreCateg,
            'correo'          => $strCorreo,
            'alias'           => $arrayAlias,
            'empresa'         => $strEmpresa  
        ));
    }
    
    /**
    * createAction
    *
    * Metodo encargado de crear la nueva plantilla de correo   
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 2.0 16-11-2014 - Actualizacion (Se cambia para que reciba el alias con la carcateristica es_copia )
    * 
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 Version Inicial
    *    
    * @Secure(roles="ROLE_237-3")
    */
   public function createAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $request = $this->get('request');

        $em = $this->get('doctrine')->getManager('telconet_comunicacion');              

        $entity = new AdmiPlantilla();

        $nombrePlantilla = $request->get('nombrePlantilla');
        $codigo          = $request->get('codigo');
        $plantilla       = $request->get('plantilla');
        $correos         = $request->get('correos');
        $modulo          = $request->get('modulo');

        $arrayAlias = array();
        
        if($correos != '')
        {
            $arrayJson = json_decode($correos);           
            $arrayAlias = $arrayJson->asignaciones;
        }

        $em->getConnection()->beginTransaction();

        try
        {

            $entity->setPlantilla($plantilla);
            $entity->setNombrePlantilla($nombrePlantilla);
            $entity->setCodigo($codigo);
            $entity->setModulo($modulo);
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            $em->persist($entity);
            $em->flush();           

            foreach($arrayAlias as $alias):

                $infoAliasPlantilla = new InfoAliasPlantilla();
                $infoAliasPlantilla->setPlantillaId($entity->getId());
                $infoAliasPlantilla->setAliasId($alias->id_alias);
                $infoAliasPlantilla->setEsCopia($alias->esCC);              
                $infoAliasPlantilla->setEstado('Activo');
                $infoAliasPlantilla->setFeCreacion(new \DateTime('now'));
                $infoAliasPlantilla->setUsrCreacion($request->getSession()->get('user'));
                $em->persist($infoAliasPlantilla);
                $em->flush();

            endforeach;

            $em->getConnection()->commit();

            $resultado = json_encode(array('success' => true,
                                           'id'      => $entity->getId()
            ));
        }
        catch(Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $resultado = json_encode(array('success' => false, 'mensaje' => $e));
        }

        $respuesta->setContent($resultado);

        return $respuesta;
    }

    /**
    * getPlantillaAliasAction
    *
    * Metodo encargado de obtener los alias asociados a una determinada plantilla   
    *
    * @author Lizbeth Cruz <@telconet.ec>
    * @version 1.1 08-09-2016 - Se agrega la búsqueda por el nombre del alias
    * 
    * @version 1.0 Version Inicial
    * 
    */
    public function getPlantillaAliasAction()
    {

        $respuesta  = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $request    = $this->get('request');

        $emComunicacion     = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura  = $this->get('doctrine')->getManager('telconet_general');

        $start      = $request->query->get('start')  ? $request->query->get('start') :'';
        $limit      = $request->query->get('limit')  ? $request->query->get('limit') :'';
        $nombre     = $request->query->get('nombre') ? $request->query->get('nombre'):'';

        $id         = $request->get('id');

        $objJson    = $emComunicacion->getRepository('schemaBundle:AdmiAlias')
                                     ->generarJson($nombre, '', '', '', '', $start, $limit, $emComercial, $emInfraestructura, $id);

        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
   /**
    * newAction
    *
    * Método encargado de editar plantilla de correo   
    *
    * @version 1.0 Version Inicial
    *
    * @author Néstor Naula <nnaulal@telconet.ec>
    * @version 1.1 - Se agrega la variable del código de plantilla generado por 
    *                el proceso ECUCERT
    * @since 1.0
    *   
    * @Secure(roles="ROLE_237-4")
    */
    public function editAction($id)
   {              
        $objPeticion      = $this->get('request');
        $strNomParametro  = "PLANTILLAS DE NOTIFICACIONES";
        $strEstado        = "Activo";
        $strDescripCateg  = "CODIGO DE PLANTILLA ECUCERT";
        $intCodEmpresa    = $objPeticion->getSession()->get('idEmpresa');
        $strBanderaEcu    = $objPeticion->query->get('banderEcu')?
                            $objPeticion->query->get('banderEcu'):0;
        $emGeneral        = $this->getDoctrine()->getManager("telconet_general");

        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        $emComercial = $this->getDoctrine()->getManager("telconet");
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("46", "1");

        if (null == $plantilla = $em->find('schemaBundle:AdmiPlantilla', $id)) {
            throw new NotFoundHttpException('No existe la Plantilla que se quiere modificar');
        }
        
        if($strBanderaEcu == 1)
        {
            $arrayParamEcucert   = array(
                'nombreParametro' => $strNomParametro,
                'estado'          => $strEstado
            );

            $entityParametroCab    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                           ->findOneByNombreParametro($arrayParamEcucert);

            $intIdParametrosECU = 0;
            if( isset($entityParametroCab) && !empty($entityParametroCab) )
            {
                $intIdParametrosECU = $entityParametroCab->getId();
            }

            $arrayParametrosDet  = array( 
                'estado'      => $strEstado, 
                'parametroId' => $intIdParametrosECU,
                'descripcion' => $strDescripCateg,
                'empresaCod'  => $intCodEmpresa,
                'valor4'      => $plantilla->getCodigo()
            );

            $objParametroDetEstadoInc = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findOneBy($arrayParametrosDet);
            
            if(!is_object($objParametroDetEstadoInc))
            {
                throw $this->createNotFoundException('No se permite el ingreso a la plantilla');
            }
        }

        $form   = $this->createForm(new AdmiPlantillaType(),$plantilla);
        
        return $this->render('administracionBundle:AdmiPlantilla:edit.html.twig', array(
            'item' => $entityItemMenu,     
	    'plantilla'=>$plantilla,
            'form'   => $form->createView(),
            'banderaEcucert' => $strBanderaEcu            
        ));
                     
    }
    
    /**
     * 
     * Metodo que realiza la actualizacion de las plantillas de correos con sus respectivos alias
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 - Se actualiza el método, realizando una acción de acuerdo a la que el usuario seleccionó al gestionar el alias
     * @since 08-09-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se actualiza metodo para que cuando se edite una plantilla y no se haga nada con sus correos estos permanezcan con el mismo
     *                estado.
     * @since 15-02-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 1.0 Version Inicial
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws type
     * 
     * @Secure(roles="ROLE_237-5")
     */
    public function updateAction()
    {
        $objRequest = $this->getRequest();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emComunicacion     = $this->getDoctrine()->getManager('telconet_comunicacion');
        $strNombrePlantilla = $objRequest->get('nombrePlantilla');
        $codigo             = $objRequest->get('codigo');
        $plantilla          = $objRequest->get('plantilla');
        $correos            = $objRequest->get('correos');
        $modulo             = $objRequest->get('modulo');
        $strIpClient        = $objRequest->getClientIp();
        $objSession         = $objRequest->getSession();
	    $strUserSession     = ($objSession->get('user') ? $objSession->get('user') : "");
        $serviceUtil        = $this->get('schema.Util');
        
        $emComunicacion->getConnection()->beginTransaction(); 
        try
        {
            $entity = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->find($objRequest->get('id'));

            if(!$entity)
            {
                throw $this->createNotFoundException('NO se ha podido encontrar la plantilla requerida.');
            }
            
            $entity->setNombrePlantilla($strNombrePlantilla);
            $entity->setCodigo($codigo);
            $entity->setModulo($modulo);
            $entity->setEstado('Modificado');
            $entity->setPlantilla($plantilla);
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($strUserSession);
            $emComunicacion->persist($entity);
            $emComunicacion->flush();
            
            $intTotal   = 0;
            if($correos != '')
            {
                $arrayJson  = json_decode($correos);           
                $intTotal   = $arrayJson->total;
                if($intTotal>0)
                { 
                    $arrayAliasGestionados = $arrayJson->registros;
                    foreach( $arrayAliasGestionados as $aliasGestionado )
                    {
                        $accionAEjecutar    = $aliasGestionado->accion;

                        if($accionAEjecutar=="Insertar")
                        {
                            $infoAliasPlantilla = new InfoAliasPlantilla();
                            $infoAliasPlantilla->setPlantillaId($entity->getId());
                            $infoAliasPlantilla->setAliasId($aliasGestionado->id_alias);
                            $infoAliasPlantilla->setesCopia($aliasGestionado->esCC);
                            $infoAliasPlantilla->setEstado('Activo');
                            $infoAliasPlantilla->setFeCreacion(new \DateTime('now'));
                            $infoAliasPlantilla->setUsrCreacion($strUserSession);
                            $emComunicacion->persist($infoAliasPlantilla);
                            $emComunicacion->flush();
                        }
                        else if($accionAEjecutar=="Editar")
                        {
                            $infoAliasPlantilla = $emComunicacion->getRepository('schemaBundle:InfoAliasPlantilla')
                                                     ->findBy(array(
                                                                        'aliasId'       => $aliasGestionado->id_alias,  
                                                                        'plantillaId'   => $objRequest->get('id'),
                                                                        'estado'        => 'Activo'
                                                                     )
                                                               );
                            $boolInfoAliasSinEditar=true;
                            foreach($infoAliasPlantilla as $aliasPlantilla)
                            {
                                if($boolInfoAliasSinEditar)
                                {
                                    $aliasPlantilla->setEsCopia($aliasGestionado->esCC);
                                    $emComunicacion->persist($aliasPlantilla);
                                    $emComunicacion->flush();
                                    $boolInfoAliasSinEditar=false;
                                }
                                else
                                {
                                    /*Se eliminan registros duplicados*/
                                    $aliasPlantilla->setEstado('Eliminado');
                                    $emComunicacion->persist($aliasPlantilla);
                                    $emComunicacion->flush();
                                }
                            }
                        }
                        else if($accionAEjecutar=="Eliminar")
                        {
                            $infoAliasPlantilla = $emComunicacion->getRepository('schemaBundle:InfoAliasPlantilla')
                                                     ->findBy(array( 
                                                                        'aliasId'       => $aliasGestionado->id_alias, 
                                                                        'plantillaId'   => $objRequest->get('id'),
                                                                        'estado'        => 'Activo'
                                                                      ));
                            foreach($infoAliasPlantilla as $aliasPlantilla)
                            {
                                $aliasPlantilla->setEstado('Eliminado');
                                $emComunicacion->persist($aliasPlantilla);
                                $emComunicacion->flush();
                            }
                        }
                    }
                }
            }
            
            $emComunicacion->getConnection()->commit();
            $resultado = json_encode(array('success' => true,'id' => $entity->getId()));
        }
        catch (\Exception $ex) {
            $serviceUtil->insertError('Telcos+', 'actualizarPlantilla', $ex->getMessage(), $strUserSession, $strIpClient);
            $emComunicacion->getConnection()->rollback();
            $resultado = json_encode(array('success' => false, 'mensaje' => 'Se presentaron errores al intentar actualizar la plantilla'));
      }
        $emComunicacion->getConnection()->close();

        $respuesta->setContent($resultado);

        return $respuesta;
    }

    public function validarIdsExisten($arrayAlias,$arrayAliasExistentes)
    {
    
        $arrayAliasNuevos = array(); //Alias Nuevos               
	    
        //Busqueda de correos nuevos
        foreach($arrayAlias as $alias)
        {            
            $arrayAliasNuevos[] = $alias->id_alias; //Array con ids nuevos
            
            if(!in_array($alias->id_alias, $arrayAliasExistentes))
            {
                $correosNuevos[] = array('id'=>$alias->id_alias,'esCopia'=>$alias->esCC);
            }
            else
            {
                $correosIguales[] = array('id'=>$alias->id_alias,'esCopia'=>$alias->esCC);
            }
        }

        //Busqueda de correos viejos para editar estado a Eliminado
        foreach($arrayAliasExistentes as $id)
        {
            if(!in_array($id, $arrayAliasNuevos))
            {
                $correosViejos[] = $id;
            }
        }

        $arrayCorreos['nuevos'] = $correosNuevos;
        $arrayCorreos['viejos'] = $correosViejos;
        $arrayCorreos['iguales'] = $correosIguales;

        return $arrayCorreos;
    }

    /**
    * @Secure(roles="ROLE_237-8")
    */
    public function deleteAction($id){

	  $request = $this->getRequest();

	  $em = $this->getDoctrine()->getManager('telconet_comunicacion');
	  $entity = $em->getRepository('schemaBundle:AdmiPlantilla')->find($id);

	  if (!$entity) {
	      throw $this->createNotFoundException('Unable to find AdmiAlias entity.');
	  }	  
	  $entity->setEstado('Eliminado');            
	  $entity->setFeUltMod(new \DateTime('now'));            
	  $entity->setUsrUltMod($request->getSession()->get('user'));            
	  $em->persist($entity);			            
	  $em->flush();

	  return $this->redirect($this->generateUrl('admiplantilla'));
    }

    /**
    * @Secure(roles="ROLE_237-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_comunicacion");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiPlantilla', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");
					$entity->setFeUltMod(new \DateTime('now'));
					$entity->setUsrUltMod($request->getSession()->get('user'));
					$em->persist($entity);
					$em->flush();
                }
				
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;        
        
        return $respuesta;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_237-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        	
        $estado = $peticion->query->get('estado')?$peticion->query->get('estado'):'';
        $codigo = $peticion->query->get('codigo')?$peticion->query->get('codigo'):'';
        $nombre = $peticion->query->get('nombre')?$peticion->query->get('nombre'):'';
        $modulo = $peticion->query->get('modulo')?$peticion->query->get('modulo'):'';
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');                
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_comunicacion")
            ->getRepository('schemaBundle:AdmiPlantilla')
            ->generarJson($nombre,$estado,$modulo,$codigo,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    
    public function envioPruebaAction(){
    
	    $respuesta = new Response();
	    $respuesta->headers->set('Content-Type', 'text/json');
    
	    $peticion = $this->get('request');
	    
	    $correo[] = $peticion->get('correoPrueba');
	    
	    $plantilla = $peticion->get('plantilla');	    	    
	    
	    try{
		$this->notificaOperacion($plantilla, $correo);
		$resultado = json_encode(array('success'=>true,'mensaje'=>'Mensaje de Prueba enviado a '.$correo[0]));
			    
	    }catch(Exception $e){	    
		$resultado = json_encode(array('success'=>false,'mensaje'=>'No se pudo enviar el correo'));
	    }
	    
	    $respuesta->setContent($resultado);		    
            
	    return $respuesta;
    }
    
     public function notificaOperacion($view,$to = null){
	
	    $to[]="notificaciones_telcos@telconet.ec";			
		  
	    foreach($to as $correo){
						      
		if($correo!=null && $correo!="")		  						    
		      $correos[] = $correo;
	    }
		  
	    $message = \Swift_Message::newInstance()
					      ->setSubject('Correo de Prueba de Plantilla')
					      ->setFrom('notificaciones_telcos@telconet.ec')
					      ->setTo($correos)
					      ->setBody($view,'text/html');

	    $this->get('mailer')->send($message);
		    
    }
	    
}