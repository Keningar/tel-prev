<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiFormaContacto;
use telconet\schemaBundle\Form\AdmiFormaContactoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\ReturnResponse;

class AdmiFormaContactoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_31-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("31", "1");

        $entities = $em->getRepository('schemaBundle:AdmiFormaContacto')->findAll();

        return $this->render('administracionBundle:AdmiFormaContacto:index.html.twig', array(
            'item' => $entityItemMenu,
            'formacontacto' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_31-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("31", "1");

        if (null == $formacontacto = $em->find('schemaBundle:AdmiFormaContacto', $id)) {
            throw new NotFoundHttpException('No existe el AdmiFormaContacto que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiFormaContacto:show.html.twig', array(
            'item' => $entityItemMenu,
            'formacontacto'   => $formacontacto,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_31-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("31", "1");
        $entity = new AdmiFormaContacto();
        $form   = $this->createForm(new AdmiFormaContactoType(), $entity);

        return $this->render('administracionBundle:AdmiFormaContacto:new.html.twig', array(
            'item' => $entityItemMenu,
            'formacontacto' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_31-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("31", "1");
        $entity  = new AdmiFormaContacto();
        $form    = $this->createForm(new AdmiFormaContactoType(), $entity);        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Proceso para generar un codigo de forma de contacto*/
            $arrayCodigoGenerado = $this->creaCodigo($entity->getDescripcionFormaContacto());                       
            if(isset($arrayCodigoGenerado['status']) && $arrayCodigoGenerado['status'] == '200')
            {
                $entity->setCodigo($arrayCodigoGenerado['codigo']);
                $em->persist($entity);
                $em->flush();               
                $em->getConnection()->commit();               
                return $this->redirect($this->generateUrl('admiformacontacto_show', array('id' => $entity->getId()))); 
            }
            /*Proceso para generar un codigo de forma de contacto*/
        }
        
        return $this->render('administracionBundle:AdmiFormaContacto:new.html.twig', array(
            'item' => $entityItemMenu,
            'formacontacto' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_31-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("31", "1");

        if (null == $formacontacto = $em->find('schemaBundle:AdmiFormaContacto', $id)) {
            throw new NotFoundHttpException('No existe el AdmiFormaContacto que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiFormaContactoType(), $formacontacto);
        return $this->render('administracionBundle:AdmiFormaContacto:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'formacontacto'   => $formacontacto));
    }
    
    /**
    * @Secure(roles="ROLE_31-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("31", "1");
        $entity = $em->getRepository('schemaBundle:AdmiFormaContacto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiFormaContacto entity.');
        }

        $editForm   = $this->createForm(new AdmiFormaContactoType(), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admiformacontacto_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiFormaContacto:edit.html.twig',array(
            'item' => $entityItemMenu,
            'formacontacto'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_31-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet');
            $entity = $em->getRepository('schemaBundle:AdmiFormaContacto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiFormaContacto entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
			$em->persist($entity);	
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('admiformacontacto'));
    }

    /**
    * @Secure(roles="ROLE_31-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiFormaContacto', $id)) {
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
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_31-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:AdmiFormaContacto')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * getAdmiFormaContactoAction, obtiene las forma contacto
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 17-04-2016
     * @since 1.0
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 27-03-2019 Se agrega consulta de formas de contacto para Telconet Guatemala.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function getAdmiFormaContactoAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa'); 
        $strEmpresaCod      = $objSession->get('idEmpresa');
        $objReturnResponse  = new ReturnResponse();
        $objResponse        = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $strEstado          = $objRequest->get('strEstado');

        $emGeneral = $this->getDoctrine()->getManager();

        if(empty($strEstado))
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'No se esta enviando el estado de las formas contacto.');
            $objResponse->setContent(json_encode((array) $objReturnResponse));
            return $objResponse;
        }
        
        if($strPrefijoEmpresa === 'TNG')
        {
            $arrayAdmiParametroDet  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('COD_FORMA_CONTACTO', 
                                                      'COMERCIAL',
                                                      'COD_FORMA_CONTACTO', 
                                                      "", 
                                                      "",
                                                      "",
                                                      "", 
                                                      "", 
                                                      "", 
                                                      $strEmpresaCod);

            if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
            {
                $arrayCodFormasContacto = array();
                
                foreach($arrayAdmiParametroDet as $arrayParametro)
                {
                    $arrayCodFormasContacto[] = $arrayParametro['valor1'];
                }
            }
            else
            {
                $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . 'Error: No existen parametros configurados.');
                $objResponse->setContent(json_encode((array) $objReturnResponse));
                return $objResponse;
            }            
            
            $arrayFormasContacto = $emGeneral->getRepository('schemaBundle:AdmiFormaContacto')
                                             ->findBy( array('estado' => $strEstado, 'codigo' => $arrayCodFormasContacto));           
        }
        else
        {
            $arrayFormasContacto = $emGeneral->getRepository('schemaBundle:AdmiFormaContacto')
                                                 ->findBy( array('estado'                    => $strEstado),
                                                           array('descripcionFormaContacto'  => 'ASC'));    
        }

        $arrayAdmiFormaContacto = array();
        foreach($arrayFormasContacto as $objAdmiFormaContacto):
            
            $arrayAdmiFormaContacto[] = array('intIdFormaContacto'          => $objAdmiFormaContacto->getId(),
                                              'strDescripcionFormaContacto' => $objAdmiFormaContacto->getDescripcionFormaContacto(),
                                              'strEstado'                   => $objAdmiFormaContacto->getEstado(),
                                              'strFeCreacion'               => ($objAdmiFormaContacto->getFeCreacion()) ? 
                                                                               date_format($objAdmiFormaContacto->getFeCreacion(), "d-m-Y H:i:s") : '',
                                              'strUsrCreacion'              => $objAdmiFormaContacto->getUsrCreacion());
        
        endforeach;

        $objReturnResponse->setRegistros($arrayAdmiFormaContacto);
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objResponse->setContent(json_encode((array) $objReturnResponse));

        return $objResponse;
    } //getAdmiFormaContactoAction

    /**
     * getEscalabilidadContactoAction, obtiene los niveles de Escalabilidad
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 03-04-2019
     * @since 1.0
     * 
     * @return $objRespuesta
     * 
     */
    public function getEscalabilidadContactoAction()
    {
        $objReturnResponse  = new ReturnResponse();
        $objRespuesta       = new JsonResponse();
        $arrayParametros['strNombreParametroCab'] = "PERMITE_ESCALABILIDAD";
        $emGeneral = $this->getDoctrine()->getManager();
        
        $arrayNivelEscalabilidad = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findParametrosDet($arrayParametros);
        
        foreach($arrayNivelEscalabilidad['arrayResultado'] as $objEscalabilidadContacto):
            $arrayEscalabilidadContacto[] = array('intIdEscalabilidadContacto'  => $objEscalabilidadContacto['intIdParametroDet'],
                                                  'strEscalabilidadContacto'    => $objEscalabilidadContacto['strValor1']);
        
        endforeach;
        $objReturnResponse->setRegistros($arrayEscalabilidadContacto);
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objRespuesta->setContent(json_encode((array) $objReturnResponse));

        return $objRespuesta;
    }
    
    /**
     * getHorarioContactoAction, obtiene los horarios de los contactos
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 03-04-2019
     * @since 1.0
     * 
     * @return $objRespuesta
     * 
     */
    public function getHorariosContactoAction()
    {
        $objReturnResponse  = new ReturnResponse();
        $objRespuesta       = new JsonResponse();
        $arrayParametros['strNombreParametroCab'] = "HORARIO_CONTACTOS";
        $emGeneral = $this->getDoctrine()->getManager();
        
        $arrayHorarioEscalabilidad = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findParametrosDet($arrayParametros);
        
        foreach($arrayHorarioEscalabilidad['arrayResultado'] as $objEscalabilidadContacto):
            $arrayEscalabilidadContacto[] = array('intIdHorarioContacto'  => $objEscalabilidadContacto['intIdParametroDet'],
                                                  'strHorarioContacto'    => $objEscalabilidadContacto['strValor1']);
        
        endforeach;
        $objReturnResponse->setRegistros($arrayEscalabilidadContacto);
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
        $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
        $objRespuesta->setContent(json_encode((array) $objReturnResponse));

        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_182-3")
     * 
     * Documentación para el método 'creaCodigo'.
     * 
     * Método encargado de generar codigo para la Forma de Contacto.
     * 
     * @return strCodigoFormaContacto.
     * 
     * @author Eduardo Vargas Perero<eevargas@telconet.ec>
     * @version 1.0 15-03-2023
     */
    private function creaCodigo($strDescripcionFormaContacto)
    {
        $strCodigoFormaContacto = "";
        $emGeneral = $this->get('doctrine')->getManager('telconet');
        $intPalabras = str_word_count($strDescripcionFormaContacto, 0);
        $arrayCadena = str_word_count($strDescripcionFormaContacto, 1);
        if($intPalabras == 0)
        {
            return	['codigo'   => "",
                    'status'    => "500",
	    	        'msj'       => 'Codigo invalido - vacio'
		            ];
        }
        else if($intPalabras == 1)
        {
            $strCodigo    = substr($arrayCadena[0], 0, 3);
            $strCodigoFormaContacto   = $strCodigo;
            if(strlen($strCodigo) == 1)
            {
                $strCodigoFormaContacto    = $strCodigo.$strCodigo.$strCodigo;
            }
            else if(strlen($strCodigo) == 2)
            {
                $strCodigoUno               = substr($arrayCadena[0], 1, 2);
                $strCodigoFormaContacto     = $strCodigo.$strCodigoUno;
            }
        }
        else if($intPalabras == 2)
        {
            $strCodigoUno   = substr($arrayCadena[0], 0, 1);
            $strCodigoDos   = substr($arrayCadena[1], 0, 2);
            $strCodigo      = $strCodigoUno.$strCodigoDos;
            $strCodigoFormaContacto   = $strCodigo;
            if(strlen($strCodigo) == 2)
            {
                $strCodigoFormaContacto    = $strCodigo.$strCodigoDos;
            }
        }
        else
        {
            foreach ($arrayCadena as $palabra) 
            {
                $strCodigoFormaContacto    = $strCodigoFormaContacto.substr($palabra, 0, 1);
            }
        }
        
        $strCodigoFormaContacto = strtoupper($strCodigoFormaContacto);
        $strValidarCodigo       = $strCodigoFormaContacto;
        $intContador            = 1;
        
        do
        {
            $boolExisteCodigo       = false;            
            $entityFormaContacto    = $emGeneral->getRepository('schemaBundle:AdmiFormaContacto')->findPorCodigoFormaContacto($strValidarCodigo);
            if(null == $entityFormaContacto) 
            {
                $strCodigoFormaContacto = $strValidarCodigo;
                $boolExisteCodigo       = false; 
            }
            else
            {
                $boolExisteCodigo       = true;
                $strValidarCodigo = $strCodigoFormaContacto.$intContador;
            }
            $intContador++;
        }
        while($boolExisteCodigo);

        return	['codigo'   => $strCodigoFormaContacto,
                'status'    => "200",
	    	    'msj'       => 'Codigo generado con exito'
		];
    }
}