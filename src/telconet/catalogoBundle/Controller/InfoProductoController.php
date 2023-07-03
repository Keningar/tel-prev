<?php

namespace telconet\catalogoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoProducto;
use telconet\schemaBundle\Form\InfoProductoType;

/**
 * InfoProducto controller.
 *
 */
class InfoProductoController extends Controller
{
    /**
     * Lists all InfoProducto entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('schemaBundle:InfoProducto')->findAll();

        return $this->render('catalogoBundle:InfoProducto:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a InfoProducto entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet');
	
        $entity = $em->getRepository('schemaBundle:InfoProducto')->find($id);
		
        /*print_r($entity);
        die();*/
        /*datos adicionales del producto*/
		$datosAdicionales = $em->getRepository('schemaBundle:InfoProductoDatosAdicio')->findByProductoId($id);
		
		/*caracteristicas del producto*/
		$carc_href = $em->getRepository('schemaBundle:InfoProductoCaracteristica')->findByProductoId($id);
		
		foreach ($carc_href as $datos):
					$caracteristicas[]= array(
					'codigoCaracteristica'=> $datos->getCaracteristicaId()->getCodigoCaracteristica(),
					'descripcionCaracteristica'=> $datos->getCaracteristicaId()->getDescripcionCaracteristica(),
					'tipo'=> ucfirst($datos->getCaracteristicaId()->getDescripcionCaracteristica()),
					'valor'=>$datos->getValor(),
					 );             
		endforeach;
		/*Presentacion de la empresa*/
		$empresa = $em->getRepository('schemaBundle:AdmiEmpresa')->find($entity->getEmpresaId()->getId());
		$nombre_empresa=$empresa->getRazonSocial();
		
        $deleteForm = $this->createDeleteForm($id);
		
		/*Para la carga de la imagen desde el default controller*/
		//$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		//$adminController = new DefaultController();
		//$img_opcion = $adminController->getImgOpcion($em_seguridad,'ADMCOM-CATESL');
		/*Presentar acciones relacionada*/
        //$acc_relacionadas=$adminController->getAccionesRelacionadas($em_seguridad,'ADMCOM-CATESL','show');
        
        $parametros=array(
				'entity'      => $entity,
				'delete_form' => $deleteForm->createView(),
				//'img_opcion_menu'=>$img_opcion,
				'nombre_empresa'=>"Ecuanet",
				//'acc_relaciondas'=>$acc_relacionadas
				);

		//'nombre_empresa'=>$nombre_empresa,
		if (!empty($datosAdicionales))
			$parametros['datosAdicionales']=$datosAdicionales;
			
		if(!empty($caracteristicas))
			$parametros['caracteristicas']=$caracteristicas;
		
		return $this->render('catalogoBundle:InfoProducto:show.html.twig', $parametros);
    }

    /**
     * Displays a form to create a new InfoProducto entity.
     *
     */
    public function newAction()
    {
        $entity = new InfoProducto();
        
        /*Obtener la empresa para presentacion en el formulario*/
        $session = $this->get('request')->getSession();
		$options['empresa']= $session->get("empresa");
        $form   = $this->createForm(new InveProductoDatType(), $entity);
		
		/*Para la carga de la imagen desde el default controller*/
		//$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		//$adminController = new DefaultController();
		//$img_opcion = $adminController->getImgOpcion($em_seguridad,'ADMCOM-CATESL');
		
		/*Listado de productos de la empresa*/
		$em = $this->getDoctrine()->getManager('telconet');
		$estado = "Activo";
		//$idEmpresa=$session->get("idEmpresa");
		$idEmpresa=1;
		$productos=$em->getRepository('schemaBundle:InfoProducto')->findProductosxTipo($idEmpresa,"PR",$estado);
		
        return $this->render('catalogoBundle:InfoProducto:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            //'img_opcion_menu'=>$img_opcion,
            'productos'=>$productos
        ));
    }

    /**
     * Creates a new InfoProducto entity.
     *
     */
    public function createAction()
    {
		$user = $this->get('security.context')->getToken()->getUser();
        $entity  = new InfoProducto();
        $request = $this->getRequest();
        /*Tomo la empresa y se la paso al type*/
        //$session = $this->get('request')->getSession();
        //$nombre_empresa=$session->get("empresa");
		$nombre_empresa="Telconet"; //temporal
        $options['empresa']= $nombre_empresa;
        /*Tomo la empresa y se la paso al type*/
        $form_req_prod=$session->get("infoproducto");
        
        $form    = $this->createForm(new InfoProductoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet');
             //begin the transaction
            $em->getConnection()->beginTransaction();
            // Try and make the transaction
            try {
				$entity->setMultiplesPrecios("N");
				$entity->setAplicaPromocion("N");
				$entity->setTipoItem("PR");
				$estado = "Activo";
				$entity->setEstado($estado);
				/*Para la empresa del formulario*/
				$empresa = $em->getRepository('schemaBundle:AdmiEmpresa')->findOneByRazonSocial($nombre_empresa);
				$entity->setIdEmpresa($empresa);
				/*Para que guarde la fecha y el usuario correspondiente*/
				$entity->setFeCreacion(new \DateTime('now'));
				//$usuario=$user->getUsername();
				$usuario="amontero";
				$entity->setUsrCreacion($usuario);
				/*Para que guarde la fecha y el usuario correspondiente*/
				$em->persist($entity);
				$em->flush();
				
				/*Guardar los datos adicionales del producto*/
				$diasGracia = $request->request->get("dias"); 
				$extra = $request->request->get("extra"); 
				if(!empty($diasGracia)|| !empty($extra))
				{ 
					
					$datosAdicionales  = new InfoProductoDatosAdicio();
					
					/*Obtener producto*/
					$producto=$em->getRepository('schemaBundle:InfoProducto')->findOneById($entity->getId());
					$datosAdicionales->setProductoId($producto);
					
					if(!empty($diasGracia))
						$datosAdicionales->setDiasGracia($diasGracia);
					else
						$datosAdicionales->setDiasGracia(0);
					
					if(!empty($extra))
					{
						$producto_extra=$em->getRepository('schemaBundle:InfoProducto')->findOneByNombreProducto($extra);
						$datosAdicionales->setIdProductoHextra($producto_extra);
					}
					else
						$datosAdicionales->setIdProductoHextra(0);
					
					/*Para que guarde la fecha y el usuario correspondiente*/
					$datosAdicionales->setFeCreacion(new \DateTime('now'));
					//$usuario=$user->getUsername();
					$usuario="amontero";					
					$datosAdicionales->setUsrCreacion($usuario);
					/*Para que guarde la fecha y el usuario correspondiente*/
					
					$em->persist($datosAdicionales);
					$em->flush();
				}
				
				//Estableciendo relacion entre el producto - mecanismo - caracteristicas
				$caracteristicas = $request->request->get("id"); 
				$valores=$request->request->get("valor"); 
				if(!empty($caracteristicas) and !empty($valores))
				{
					for($i=0;$i<sizeof($caracteristicas);$i++)
					{
						if(!empty($caracteristicas[$i]))
						{
							$obj_caract=$em->getRepository('schemaBundle:AdmiCaracteristica')->find($caracteristicas[$i]);
							
							$infoRef  = new InfoProductoCaracteristica();
							$infoRef->setCaracteristicaId($obj_caract);
							$infoRef->setProductoId($entity);
							$infoRef->setValor($valores[$i]);
							$infoRef->setEstado($estado);
							//Para que guarde la fecha y el usuario correspondiente
							$infoRef->setFeCreacion(new \DateTime('now'));
							//$usuario=$user->getUsername();
							$usuario="amontero";
							$infoRef->setUsrCreacion($usuario);
							$em->persist($infoRef);
							$em->flush();
						}
					}
				}
				$em->getConnection()->commit();
				return $this->redirect($this->generateUrl('infoproducto_show', array('id' => $entity->getId())));

            } catch (\Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                $em->getConnection()->close();
                //aquí algún mensaje con la excepción concatenada    
            }
        }

		return $this->redirect($this->generateUrl('infoproducto_new'));
    }
    /**
     * Displays a form to edit an existing InfoProducto entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet');

        $entity = $em->getRepository('schemaBundle:InfoProducto')->find($id);
		/*datos adicionales del producto*/
		$datosAdicionales = $em->getRepository('schemaBundle:InfoProductoDatosAdicio')->findByProductoId($id);
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoProductoentity.');
        }
		/*Paso la empresa de la entidad*/
		$empresa = $em->getRepository('schemaBundle:AdmiEmpresa')->findOneById($entity->getEmpresaId()->getId());
		$options['empresa']=$empresa->getRazonSocial();
        $editForm = $this->createForm(new InfoProductoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
		
		/*Para la carga de la imagen desde el default controller*/
		//$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		//$adminController = new DefaultController();
		//$img_opcion = $adminController->getImgOpcion($em_seguridad,'ADMCOM-CATESL');
        
        /*Listado de productos de la empresa*/
		$em = $this->getDoctrine()->getManager('telconet');
		$estado = $em->getRepository('schemaBundle:AdmiEstadoDat')->findOneByDescripcionEstado('Activo');
		//$session = $this->get('request')->getSession();
		//$idEmpresa=$session->get("idEmpresa");
		$idEmpresa=1;
		$productos=$em->getRepository('schemaBundle:InfoProducto')->findProductosxTipo($idEmpresa,"PR",$estado);
		
        /*Presentar acciones relacionada*/
        //$acc_relacionadas=$adminController->getAccionesRelacionadas($em_seguridad,'ADMCOM-CATESL','show');
        
        if(!empty($datosAdicionales))
		{
			return $this->render('catalogoBundle:InveProductoDat:edit.html.twig', array(
				'entity'      => $entity,
				'edit_form'   => $editForm->createView(),
				'delete_form' => $deleteForm->createView(),
				//'img_opcion_menu'=>$img_opcion,
				'datosAdicionales'=>$datosAdicionales,
				//'acc_relaciondas'=>$acc_relacionadas,
				'productos'=>$productos
			));
		}
		else
		{
			return $this->render('catalogoBundle:InfoProducto:edit.html.twig', array(
				'entity'      => $entity,
				'edit_form'   => $editForm->createView(),
				'delete_form' => $deleteForm->createView(),
				//'img_opcion_menu'=>$img_opcion,
				//'acc_relaciondas'=>$acc_relacionadas
			));
		}
		
        
    }

    /**
     * Edits an existing InfoProducto entity.
     *
     */
    public function updateAction($id)
    {
		
		$user = $this->get('security.context')->getToken()->getUser();
		
        $em = $this->getDoctrine()->getManager('telconet');

        $entity = $em->getRepository('schemaBundle:InfoProducto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoProducto entity.');
        }

		/*Paso la empresa de la entidad*/
        $editForm   = $this->createForm(new InfoProductoType(),$entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);
		
		//$vista = $request->request->get("inveproductodat"); 
		/*print_r($prueba);
		die();*/
		
        if ($editForm->isValid()) {
			
			 //begin the transaction
            $em->getConnection()->beginTransaction();
            // Try and make the transaction
            try {
				/*modificacion de la empresa*/
				//$empresa = $em->getRepository('catalogoBundle:AdmiEmpresaDat')->findOneByRazonSocial($vista['idEmpresa']);
				//$entity->setIdEmpresa($empresa);
				/*Para que guarde la fecha y el usuario correspondiente*/
				$entity->setFeUltMod(new \DateTime('now'));
				//$usuario=$user->getUsername();
				$usuario="amontero";				
				$entity->setUsrUltMod($usuario);
				/*Para que guarde la fecha y el usuario correspondiente*/
				
				$em->persist($entity);
				$em->flush();

				/*Actualizacion para los datos Adicionales*/
				/*Guardar los datos adicionales del producto*/
				$diasGracia = $request->request->get("dias"); 
				$extra = $request->request->get("extra"); 
				
				if(!empty($diasGracia)|| !empty($extra))
				{ 
					//Obtener datos Adicional
					$band_cambio=0;
					$datosAdicionales=$em->getRepository('schemaBundle:InfoProductoDatosAdicio')->findOneByProductoId($entity->getId());
					
					if(!empty($diasGracia))
					{
						if($datosAdicionales->getDiasGracia()!=$diasGracia)
						{
							$datosAdicionales->setDiasGracia($diasGracia);
							$band_cambio=1;
						}
					}
					if(!empty($extra))
					{
						if($datosAdicionales->getProductoHextraId()->getNombreProducto()!=$extra)
						{
							$producto_extra=$em->getRepository('schemaBundle:InfoProducto')->findOneByNombreProducto($extra);
							$datosAdicionales->setProductoHextraId($producto_extra);
							$band_cambio=1;
						}
					}
					
					if($band_cambio==1)
					{
						//Solo si se activan las banderas significa que cambio algun valor
						$datosAdicionales->setFeUltMod(new \DateTime('now'));
						//$usuario=$user->getUsername();
						$usuario="amontero";						
						$datosAdicionales->setUsrUltMod($usuario);
						//Solo si se activan las banderas significa que cambio algun valor
					}
					$em->persist($datosAdicionales);
					$em->flush();
				}
				
				/*Estableciendo relacion entre el producto - mecanismo - caracteristicas*/
				
				$caracteristicas = $request->request->get("id"); 
				$valores=$request->request->get("valor"); 
				
				//print_r($caracteristicas);
				//print_r($valores);
				//die();
				if(!empty($caracteristicas) and !empty($valores))
				{
					for($i=0;$i<sizeof($caracteristicas);$i++)
					{
						$infoRef = $em->getRepository('schemaBundle:InfoProductoCaracteristica')->findCaracteristicasxProducto($id,$caracteristicas[$i]);
						
						//Debo verificar si los datos que me mandan son diferentes como para pretender actualizar
						if(!empty($infoRef[0]))
						{
							if($infoRef[0]->getValor()!=$valores[$i])
							{
								$infoRef[0]->setValor($valores[$i]);
								//Solo si se activan las banderas significa que cambio algun valor
								$infoRef[0]->setFeUltMod(new \DateTime('now'));
								//$usuario=$user->getUsername();
								$usuario="amontero";								
								$infoRef[0]->setUsrUltMod($usuario);
								//Solo si se activan las banderas significa que cambio algun valor
								$em->persist($infoRef[0]);
								$em->flush();
							}
						}
						else
						{
							$estado = "Activo";
							$obj_caract=$em->getRepository('schemaBundle:AdmiCaracteristicaDat')->find($caracteristicas[$i]);
							$infoRef  = new AdmiCaracProductoRef();
							$infoRef->setIdCaracteristica($obj_caract);
							$infoRef->setIdProducto($entity);
							$infoRef->setValor($valores[$i]);
							$infoRef->setEstado($estado);
							/*Para que guarde la fecha y el usuario correspondiente*/
							$infoRef->setFeCreacion(new \DateTime('now'));
								//$usuario=$user->getUsername();
								$usuario="amontero";								
							$infoRef->setUsrCreacion($usuario);
							$em->persist($infoRef);
							$em->flush();
						}
					}
				}
				$em->getConnection()->commit();
				return $this->redirect($this->generateUrl('infoproducto_edit', array('id' => $id)));
            } 
            catch (\Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                $em->getConnection()->close();
                //aquí algún mensaje con la excepción concatenada    
            }
        }

		/*Para la carga de la imagen desde el default controller*/
		//$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		//$adminController = new DefaultController();
		//$img_opcion = $adminController->getImgOpcion($em_seguridad,'ADMCOM-CATESL');
		
        return $this->render('catalogoBundle:InfoProducto:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            //'img_opcion_menu'=>$img_opcion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a InfoProducto entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
		//$user = $this->get('security.context')->getToken()->getUser();
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet');
            $entity = $em->getRepository('schemaBundle:InfoProducto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoProducto entity.');
            }

			/*No se debe eliminar, solo se inactiva la solucion y los items que coforman el mismo*/
			$estado = "Activo";
			$entity->setEstado($estado);
			$entity->setFechaHoraModificacion(new \DateTime('now'));
			$usuario=$user->getUsername();
			$usuario='amontero';
			$entity->setUsrUltMod($usuario);
			$em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infoproducto'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
	


    /*tabla productos llenado ajax*/        
    public function gridAction()
    {
		$request = $this->getRequest();		    
		$filter = $request->request->get("filter");    
		//$mecanismo_post=$filter['filters'][0]['value'];
		$mecanismo_post=$request->request->get("mecanismo");  
		$estado_post=$request->request->get("estado");  
		
		/*Obtener la empresa para presentacion en el formulario*/
        //$session = $this->get('request')->getSession();
		//$emp_sess= $session->get("empresa");
		$emp_sess="Telconet";
		$em = $this->getDoctrine()->getManager('telconet');
		$empresa = $em->getRepository('schemaBundle:AdmiEmpresa')->findOneByRazonSocial($emp_sess);
		
		if($mecanismo_post==0)
			$mecanismo="inicio";
		else
			$mecanismo = $mecanismo_post;
		
		if($estado_post==0)
			$estado="inicio";
		else
			$estado = $estado_post;
		
        $em = $this->get('doctrine')->getManager('telconet');
		
		if ($mecanismo=="inicio")
		{
			if ($estado=="inicio")
				$datos = $em->getRepository('schemaBundle:InfoProducto')->findProductosxTipo($empresa->getId(),'PR','');
			else
				$datos = $em->getRepository('schemaBundle:InfoProducto')->findProductosxTipo($empresa->getId(),'PR',$estado);
		}
		else
		{
			if ($estado=="inicio")
				$datos = $em->getRepository('schemaBundle:InfoProducto')->findProductosxTipoxMecanismo($empresa->getId(),'PR',$mecanismo,'');
			else
				$datos = $em->getRepository('schemaBundle:InfoProducto')->findProductosxTipoxMecanismo($empresa->getId(),'PR',$mecanismo,$estado);
		}
		
		$i=1;
		foreach ($datos as $datos):
				if($i % 2==0)
					$clase='k-alt';
				else
					$clase='';
					
				$urlVer = $this->generateUrl('infoproducto_show', array('id' => $datos->getId()));
				$urlEditar = $this->generateUrl('infoproducto_edit', array('id' => $datos->getId()));
						
				$linkVer = $urlVer;
				$linkEditar = $urlEditar;
				
				$arreglo[]= array(
				'Codigo'=> $datos->getCodigoProducto(),
				'Nombre'=> $datos->getNombreProducto(),
				'Descripcion'=> $datos->getDescripcionProducto(),
				'Precio'=> $datos->getPrecioProducto(),
				'fechaCreacion'=> strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
				'usuarioCreacion'=> $datos->getUsrCreacion(),
				'estado'=> $datos->getEstado(),
				'linkVer'=> $linkVer,
				'linkEditar'=> $linkEditar,
				'clase'=>$clase,
				'boton'=>""
                 );             
                 
                 $i++;     
		endforeach;
		if (!empty($arreglo))
			$response = new Response(json_encode($arreglo));
		else
		{
			$arreglo[]= array(
				'Codigo'=> "",
				'Nombre'=> "",
				'Descripcion'=> "",
				'Precio'=> "",
				'fechaCreacion'=> "",
				'usuarioCreacion'=>"",
				'estado'=> "",
				'linkVer'=> "",
				'linkEditar'=> "",
				'clase'=>"",
				'boton'=>"display:none;"
			);
			$response = new Response(json_encode($arreglo));
		}	
		$response->headers->set('Content-type', 'text/json');
		return $response;	
    }
    
    /*combo mecanismo llenado ajax*/
    public function comboAction()
    {
        $em = $this->get('doctrine')->getManager('telconet');
		$datos = $em->getRepository('schemaBundle:AdmiMecanismo')->findByEsCatalogoEstatico('S');	
		foreach ($datos as $datos):
			if($datos->getCodigoMecanismo()!='GENE')
			{
                $arreglo[]= array(
                'IdMecanismo'=>$datos->getId(),
				'Codigo'=> $datos->getCodigoMecanismo(),
				'Descripcion'=> $datos->getDescripcionMecanismo()
                                );                 
			}
		endforeach;
		
		$response = new Response(json_encode($arreglo));
		$response->headers->set('Content-type', 'text/json');
		return $response;
    }
	

    public function index_jsAction()
    {
	
        $em = $this->getDoctrine()->getManager('telconet');

        $entities = $em->getRepository('schemaBundle:InfoProducto')->findAll();

		/*Para la carga de la imagen desde el default controller*/
		//$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		//$adminController = new DefaultController();
		//$img_opcion = $adminController->getImgOpcion($em_seguridad,'ADMCOM-CATESL');
        
        /*Presentar acciones relacionada*/
        //$acc_relacionadas=$adminController->getAccionesRelacionadas($em_seguridad,'ADMCOM-CATESL','index');
        
        /*Para el listado de mecanismo*/
        $datos = $em->getRepository('schemaBundle:AdmiMecanismo')->findByEsCatalogoEstatico('S');	
		foreach ($datos as $datos):
			if($datos->getCodigoMecanismo()!='GENE')
			{
                $mecanismos[]= array(
                'IdMecanismo'=>$datos->getId(),
				'Codigo'=> $datos->getCodigoMecanismo(),
				'Descripcion'=> $datos->getDescripcionMecanismo()
                                );                 
			}
		endforeach;
        /*Para el listado de estados*/
        
        /*Modificacion a utilizacion de estados por modulos*/
        $em = $this->get('doctrine')->getManager('telconet_seguridad');
		$session = $this->get('request')->getSession();
		$modulo_activo=$session->get("modulo_activo");
		$datos = $em->getRepository('seguridadBundle:AdmiEstadoDat')->findEstadosXModulos($modulo_activo,"ADMCOM-SOL");
		
		foreach ($datos as $datos):
                $estados[]= array(
                'IdEstado'=>$datos['id'],
				'Codigo'=> $datos['codigoEstado'],
				'Descripcion'=> $datos['descripcionEstado']
				);                 
		endforeach;
		
        return $this->render('catalogoBundle:InveProductoDat:index_dos.html.twig', array(
            'entities' => $entities,
            'img_opcion_menu'=>$img_opcion,
            'acc_relaciondas'=>$acc_relacionadas,
            'mecanismos'=> $mecanismos,
            'estados'=>$estados
        ));
    }
    
    public function masivoAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');

        $entities = $em->getRepository('schemaBundle:InveProductoDat')->findAll();

		/*Para la carga de la imagen desde el default controller*/
		$em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
		$adminController = new DefaultController();
		$img_opcion = $adminController->getImgOpcion($em_seguridad,'ADMCOM-CATEP');
        
        return $this->render('catalogoBundle:InveProductoDat:index_masivo.html.twig', array(
            'entities' => $entities,
            'img_opcion_menu'=>$img_opcion,
        ));
    }
    
    public function listar_masivoAction()
    {	
        $em = $this->get('doctrine')->getManager('telconet');
			
		/*se cambio a la forma de query builder debido a que no hay oredenamiento de la forma directa
		 *con parametros especificos que es lo que se necesita
		 *de esta forma hacemos el ordenamiento para una mejor presentacion
		*/
		$query = $em->getRepository('schemaBundle:InveProductoDat')->createQueryBuilder('p')
							->where('p.tipoItem = :tipo')
							->setParameter('tipo', 'PR')
							->orderBy('p.fechaHoraCreacion', 'ASC')
							->getQuery();
		$datos = $query->getResult();
		
		foreach ($datos as $datos):
		
				$arreglo[]= array(
				'Id'=> $datos->getId(),
				'Nombre'=> $datos->getNombreProducto(),
				'Descripcion'=> $datos->getDescripcionProducto(),
				'Precio'=> $datos->getPrecioProducto(),
				'fechaCreacion'=> strval(date_format($datos->getFechaHoraCreacion(),"d/m/Y G:i")),
				'usuarioCreacion'=> $datos->getIdUsuarioCreacion(),
				'estado'=> $datos->getIdEstado()->getDescripcionEstado()
                 );             
		endforeach;
		
		$response = new Response(json_encode($arreglo));
		$response->headers->set('Content-type', 'text/json');
		return $response;
		
    }
    
    public function guardar_datos_masivosAction()
    {
		$request = $this->get('request');
		$request->isXmlHttpRequest(); // is it an Ajax request?
		$request->getPreferredLanguage(array('en', 'fr'));
		$datos=$request->query->get('models'); // get a $_GET parameter
		$array_datos=json_decode ($datos);
		$user = $this->get('security.context')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager('telconet');
		foreach ($array_datos as $datos){
	        $entity = $em->getRepository('schemaBundle:InveProductoDat')->findOneById($datos->Id);
	        if (!$entity) {
	            throw $this->createNotFoundException('Unable to find InveProductoDat entity.');
	        }
			$entity->setFechaHoraModificacion(new \DateTime('now'));
			$entity->setPrecioProducto($datos->Precio);
			$entity->setIdUsuarioModificacion($user->getUsername());
            $em->persist($entity);
            $em->flush();		
		}

		$response = new Response(json_encode($array_datos));
		$response->headers->set('Content-type', 'text/json');
		return $response;		
	}
	
	public function ajaxGetPreciosAction()
    {
		$request = $this->getRequest();
		$session  = $request->getSession();
		$em = $this->get('doctrine')->getManager('telconet');
		$user = $this->get('security.context')->getToken()->getUser();
		$idEmpresa = $session->get('idEmpresa') ;
		$idMecanismo = $request->request->get("idMecanismo"); 
		$idGrupoNegocio = $request->request->get("idGrupoNegocio"); 
		$bwSubida = trim($request->request->get("bwSubida")); 
		$bwBajada = trim($request->request->get("bwBajada")); 
		$idPtoCobertura = $request->request->get("idPtoCobertura"); 
		$idPtoCoberturaConcentrador = $request->request->get("idPtoCoberturaConcentrador"); 
		$preciosCatalogDinam = "";
		$zonaConcentrador = "";
		$ptoCoberturaConcentrador = "";
		
		$mecanismo = $em->getRepository('schemaBundle:AdmiMecanismoDat')->find($idMecanismo);
		$tipoServicio = $mecanismo->getIdServicio();
		
		$opc_precios= array();
		$opc_precios_nb = array();
		$productos = array();
		$msg = '';
		$es_catalogo_viejo = false;
		
		if($idMecanismo>0 && $idGrupoNegocio==0){
			$idGrupoNegocio = 5 ;
		}
		
        if($tipoServicio->getCodigoServicio() != "OTRS"){ //otros
			
			$ptoCobertura =  $em->getRepository('schemaBundle:CliePuntoCoberturaDat')->find($idPtoCobertura);
			$zona = $ptoCobertura->getIdZona();
			
			if($idPtoCoberturaConcentrador){
				$ptoCoberturaConcentrador =  $em->getRepository('schemaBundle:CliePuntoCoberturaDat')->find($idPtoCoberturaConcentrador);
				$zonaConcentrador = $ptoCoberturaConcentrador->getIdZona();
			}
			  
			if ($bwSubida>0 || $bwBajada>0 ){
				
				$bw = max($bwSubida,$bwBajada);
				
				$limiteBwTmp = $em->getRepository('schemaBundle:AdmiLimiteBwDat')->findOneByMecanismoyGrupoNegocio($mecanismo->getId() , $idGrupoNegocio);
				
				if($limiteBwTmp)
				{
					$bwTmp = $em->getRepository('schemaBundle:AdmiLimiteBwDat')->getConversion($bw , $limiteBwTmp->getIdUnidadBw());
					
					$limiteBw = $em->getRepository('schemaBundle:AdmiLimiteBwDat')->findByLimiteBwyMecanismoyGrupoNegocio($bwTmp,$mecanismo->getId() , $idGrupoNegocio);
					 
					if($limiteBw){
						  
						$bwConvertido = $em->getRepository('schemaBundle:AdmiLimiteBwDat')->getConversion($bw , $limiteBw->getIdUnidadBw());
						  
						if($tipoServicio->getCodigoServicio() == "TDD"){//Transmision de Datos
							if($zonaConcentrador){
								if($zona->getId() == $zonaConcentrador->getId() ){
									$zona = $em->getRepository('schemaBundle:AdmiZonaDat')->findOneByCodigoZona('ZON1');
								}else{
									if($ptoCobertura->getCoberturaCon()== 'F'){
										$zona = $em->getRepository('schemaBundle:AdmiZonaDat')->findOneByCodigoZona('ZON2');
									}else{
										$zona = $em->getRepository('schemaBundle:AdmiZonaDat')->findOneByCodigoZona('ZON3');
									}
								}
							}else{
								$zona = null;
							}                      
						}
						
						$preciosCatalogDinam = $em->getRepository('schemaBundle:InvePreciosCatalogDinamDat')->findPreciosByZonayLimiteBw($zona->getId() , $limiteBw->getId(),$idEmpresa);
						 
						foreach( $preciosCatalogDinam as $precio){
							$limitePrecio = $precio->getIdLimitePrecio();
						
							$opc_precios[] = array($limitePrecio->getOrdenJerarquia() => $this->redondeado($precio->getPrecioDinam()*$bwConvertido, 2 ));
							$opc_precios_nb[] = array($limitePrecio->getNombreLimite() => $this->redondeado($precio->getPrecioDinam()*$bwConvertido,2));
						}
					 }else{
						$msg = 'No existe categorizaci&oacute;n para ese bw';
					 }
				 }else{
						$msg = 'No existe categorizaci&oacute;n para ese bw';
					 }
			  }

			  if(count($preciosCatalogDinam) == 0 && $msg ==""){
				  $msg = 'No se han definido los precios para los datos solicitados';
			  }
			  
			  $producto = $em->getRepository('schemaBundle:InveProductoDat')->findProductoCatalogoDinamicoxMecanismo($idMecanismo,$idEmpresa);
			
			  $resultado = array('es_catalogo_nuevo'=> 'S' ,
											 'precios' => $opc_precios ,
											 'precios_nb' => $opc_precios_nb,
											 'id_producto' => $producto->getId(),
											 'msg' => $msg);
		}
	  
	    $response = new Response(json_encode($resultado));
	    $response->headers->set('Content-type', 'text/json');
	
	    return $response;
	
	}
	
	public static function redondeado ($numero, $decimales) {
		$factor = pow(10, $decimales);
		return (round($numero*$factor)/$factor); 
	}
	
	public function listar_caracteristicasAction()
	{
		$request = $this->getRequest();		    
		//$filter = $request->request->get("filter");    
		//$mecanismo_post=$filter['filters'][0]['value'];
		$mecanismo_post=$request->request->get("mecanismo");
		$producto_post=$request->request->get("producto");
		
		/*echo $mecanismo_post;
		echo "*";
		echo $producto_post;*/
		
		
		if(isset($mecanismo_post) and ($mecanismo_post!=0))
		{
			/*Se presentan las caracteristicas correspondiente al mecanismo escogido
			*/
			
			/*
			 * Se debe hacer la sigt la modificacion 
			 *  select * from admi_mecanismo_dat a
				left join admi_carac_serv_meca_ref b on a.id_mecanismo=b.id_mecanismo 
				left join admi_carac_producto_ref c on c.id_caracteristica=b.id_caracteristica
				where a.id_mecanismo=15
			 * */
			$em = $this->getDoctrine()->getManager('telconet');
			$datos = $em->getRepository('schemaBundle:AdmiMecanismoDat')->find($mecanismo_post);
			$caracteristicas=$datos->getCaracteristics();
			
			$i=1;
			foreach ($caracteristicas as $datos):
					if($i % 2==0)
						$clase='k-alt';
					else
						$clase='';
					
					if(isset($producto_post) and ($producto_post!=0))
					{
						//$datos = $em->getRepository('adminBundle:AdmiCaracProductoRef')->find($mecanismo_post);
						/*$query = $em->getRepository('schemaBundle:AdmiCaracProductoRef')->createQueryBuilder('p')
							->where('p.idProducto = :producto and p.idCaracteristica= :caracteristica')
							->setParameter('producto',$producto_post )
							->setParameter('caracteristica', $datos->getId())
							->getQuery();
						$caract_producto = $query->getResult();*/
						$caract_producto = $em->getRepository('schemaBundle:AdmiCaracProductoRef')->findCaracteristicasxProducto($producto_post,$datos->getId());
					}
					
					if(!empty($caract_producto))
					{
						foreach ($caract_producto as $cc):
							if(!empty($cc))
								$valor=$cc->getValor();
							else
								$valor="";
						endforeach;
					}
					else
						$valor="";
						
					$arreglo[]= array(
					'Id'=> $datos->getId(),
					'Codigo'=> $datos->getCodigoCaracteristica(),
					'Descripcion'=> $datos->getDescripcionCaracteristica(),
					'Tipo'=> ucfirst($datos->getTipo()),
					'Valor'=>$valor,
					'clase'=>$clase,
					'boton'=>""
					 );             
					 
					 $i++;     
			endforeach;
		}
		
		if (!empty($arreglo))
			$response = new Response(json_encode($arreglo));
		else
		{
			$arreglo[]= array(
				'Id'=> "",
				'Codigo'=> "",
				'Descripcion'=> "",
				'Tipo'=> "",
				'clase'=>"",
				'boton'=>"display:none;"
			);
			$response = new Response(json_encode($arreglo));
		}
		
		$response->headers->set('Content-type', 'text/json');		
		return $response;
	}
	
	public function listar_caracteristicas_productoAction()
	{
		$request = $this->getRequest();		    
		$filter = $request->request->get("filter");    
		$producto=$filter['filters'][0]['value'];
		
		if(isset($producto))
		{
			/*Se presentan las caracteristicas correspondiente al mecanismo escogido
			*/
			$em = $this->getDoctrine()->getManager('telconet');
			$caract_producto = $em->getRepository('schemaBundle:AdmiCaracProductoRef')->findByIdProducto($producto);
			
			/*print_r($caract_producto);
			die();*/
			$i=1;
			foreach ($caract_producto as $datos):
					if($i % 2==0)
						$clase='k-alt';
					else
						$clase='';
						
					$arreglo[]= array(
					'Id'=> $datos->getIdCaracteristica()->getIdCaracteristica(),
					'Codigo'=> $datos->getIdCaracteristica()->getCodigoCaracteristica(),
					'Descripcion'=> $datos->getIdCaracteristica()->getDescripcionCaracteristica(),
					'Tipo'=> ucfirst($datos->getIdCaracteristica()->getTipo()),
					'Valor'=>$datos->getValor(),
					'clase'=>$clase,
					'boton'=>""
					 );             
					 
					 $i++;     
			endforeach;
		}

		if (!empty($arreglo))
			$response = new Response(json_encode($arreglo));
		else
		{
			$arreglo[]= array(
				'Id'=> "",
				'Codigo'=> "",
				'Descripcion'=> "",
				'Tipo'=> "",
				'Valor'=> "",
				'clase'=>"",
				'boton'=>"display:none;"
			);
			$response = new Response(json_encode($arreglo));
		}
		
		$response->headers->set('Content-type', 'text/json');		
		return $response;
	}
	
	public function ajaxValidarPrecioNuevoAction()
	{
		/*Modificar a Symfony 2*/
		$request = $this->getRequest();
		$listado = $request->request->get("listado"); 
		$precioActual = $request->request->get("precioActual"); 
			
		if($listado!=0)
		{
			foreach ($listado as $precio_lista):
				if($precio_lista->Precio_Lista!="")
					$Precio_Lista=$precio_lista->Precio_Lista;
				if($precio_lista->Precio_Min!="")
					$Precio_Min=$precio_lista->Precio_Min; 	 	   
			endforeach;
			
			if($precioActual<0 || $precioActual =="" || preg_match('/[^\d.^\d{2}]/',$precioActual)){ 
					$output='[["msg_precio","Error, Ingrese un precio valido"],["nivel",""]]';
			}else{
				if($precioActual>=$Precio_Lista){
					$output='[["msg_precio",""],["nivel","0"]]';
				}else{
					if($precioActual<$Precio_Min){
						 $output='[["msg_precio","El valor es menor al precio de minimo, requerira aprobacion de Gerencia General"],["nivel","2"]]';
					  }
					else {
					   if($precioActual>=$Precio_Min && $precioActual<$Precio_Lista){
						$output='[["msg_precio","El valor es menor al precio de lista, requerira aprobacion de Gerencia Comercial"],["nivel","1"]]';
					   }
					}
				}
			}
			
				
	    }
	    else
			$output='No se puede generar valores';
			
	    $response = new Response(json_encode($output));
		$response->headers->set('Content-type', 'text/json');
		return $response;
	}
	
	public static function getConsultarPrecio($user,$em,$idEmpresa,$idMecanismo,$idGrupoNegocio,$idPtoCobertura,$bwSubida,$bwBajada)
    {
		$idPtoCoberturaConcentrador = "";
		$preciosCatalogDinam = "";
		$zonaConcentrador = "";
		$ptoCoberturaConcentrador = "";
		
		$mecanismo = $em->getRepository('schemaBundle:AdmiMecanismoDat')->find($idMecanismo);
		$tipoServicio = $mecanismo->getIdServicio();
		
		$opc_precios= array();
		$opc_precios_nb = array();
		$productos = array();
		$msg = '';
		$es_catalogo_viejo = false;
		
		if($idMecanismo>0 && $idGrupoNegocio==0){
			$idGrupoNegocio = 5 ;
		}
		
        if($tipoServicio->getCodigoServicio() != "OTRS"){ //otros
			
			$ptoCobertura =  $em->getRepository('schemaBundle:CliePuntoCoberturaDat')->find($idPtoCobertura);
			$zona = $ptoCobertura->getIdZona();
			
			if($idPtoCoberturaConcentrador){
				$ptoCoberturaConcentrador =  $em->getRepository('schemaBundle:CliePuntoCoberturaDat')->find($idPtoCoberturaConcentrador);
				$zonaConcentrador = $ptoCoberturaConcentrador->getIdZona();
			}
			  
			if ($bwSubida>0 || $bwBajada>0 ){
				
				$bw = max($bwSubida,$bwBajada);
				
				$limiteBwTmp = $em->getRepository('schemaBundle:AdmiLimiteBwDat')->findOneByMecanismoyGrupoNegocio($mecanismo->getId() , $idGrupoNegocio);
				
				$bwTmp = $em->getRepository('schemaBundle:AdmiLimiteBwDat')->getConversion($bw , $limiteBwTmp->getIdUnidadBw());
				
				$limiteBw = $em->getRepository('schemaBundle:AdmiLimiteBwDat')->findByLimiteBwyMecanismoyGrupoNegocio($bwTmp,$mecanismo->getId() , $idGrupoNegocio);
				 
				if($limiteBw){
					  
					$bwConvertido = $em->getRepository('schemaBundle:AdmiLimiteBwDat')->getConversion($bw , $limiteBw->getIdUnidadBw());
					  
					if($tipoServicio->getCodigoServicio() == "TDD"){//Transmision de Datos
						if($zonaConcentrador){
							if($zona->getId() == $zonaConcentrador->getId() ){
								$zona = $em->getRepository('schemaBundle:AdmiZonaDat')->findOneByCodigoZona('ZON1');
							}else{
								if($ptoCobertura->getCoberturaCon()== 'F'){
									$zona = $em->getRepository('schemaBundle:AdmiZonaDat')->findOneByCodigoZona('ZON2');
								}else{
									$zona = $em->getRepository('schemaBundle:AdmiZonaDat')->findOneByCodigoZona('ZON3');
								}
							}
						}else{
							$zona = null;
						}                      
					}
					
					$preciosCatalogDinam = $em->getRepository('schemaBundle:InvePreciosCatalogDinamDat')->findPreciosByZonayLimiteBw($zona->getId() , $limiteBw->getId(),$idEmpresa);
					 
					foreach( $preciosCatalogDinam as $precio){
						$limitePrecio = $precio->getIdLimitePrecio();
					
						//$opc_precios[] = array($limitePrecio->getOrdenJerarquia() => self::redondeado($precio->getPrecioDinam()*$bwConvertido, 2 ));
						$opc_precios[] = array($limitePrecio->getNombreLimite() => $limitePrecio->getOrdenJerarquia());
						$opc_precios_nb[] = array($limitePrecio->getNombreLimite() => self::redondeado($precio->getPrecioDinam()*$bwConvertido,2));
					}
				 }else{
					$msg = 'No existe categorizaci&oacute;n para ese bw';
				 }

			  }

			  if(count($preciosCatalogDinam) == 0 && $msg ==""){
				  $msg = 'No se han definido los precios para los datos solicitados';
			  }
			  
			  $producto = $em->getRepository('schemaBundle:InveProductoDat')->findProductoCatalogoDinamicoxMecanismo($idMecanismo,$idEmpresa);
			
			/*		
			  $resultado = array('es_catalogo_nuevo'=> 'S' ,
											 'precios' => $opc_precios ,
											 'precios_nb' => $opc_precios_nb,
											 'id_producto' => $producto->getId(),
											 'msg' => $msg);
			*/			
								
			$resultado[0] = $opc_precios_nb;
			$resultado[1] = $opc_precios;
		}
	  
	    return $resultado;
	
	}
	
	public function listar_productosAction()
    {
		/*tomo el valor del browser*/
		$request = $this->getRequest();		    
		$filter = $request->request->get("filter");    
		$producto_like=$filter['filters'][0]['value'];
		
		/*hago mi query para el like*/
		$em = $this->getDoctrine()->getManager('telconet');
		$products=$em->getRepository('schemaBundle:InveProductoDat')->findProductosxTipoYNombreProductoLike($producto_like,'PR');
		//print_r($query);
		/*lleno los valores para el json a retornar*/
		if (!empty($products))
		{
			foreach ($products as $res):
					$arreglo[]= array(
					'Codigo'=> $res->getCodigoProducto(),
					'Nombre'=> $res->getNombreProducto(),
					'Precio'=> $res->getPrecioProducto()
					 );             
			endforeach;
			$response = new Response(json_encode($arreglo));
		}
		else
		{
			$arreglo[]= array(
				'Codigo'=> "",
				'Nombre'=> "",
				'Precio'=> ""
			);
			$response = new Response(json_encode($arreglo));
		}
		
		$response->headers->set('Content-type', 'text/json');
		return $response;
		
	}	
	
	
}
