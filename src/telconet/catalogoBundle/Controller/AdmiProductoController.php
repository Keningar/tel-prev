<?php

namespace telconet\catalogoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use telconet\schemaBundle\Entity\AdmiProducto;
use telconet\schemaBundle\Entity\InfoProductoNivel;
use telconet\schemaBundle\Entity\InfoProductoImpuesto;
use telconet\schemaBundle\Entity\AdmiProductoCaracteristica;
use telconet\schemaBundle\Form\AdmiProductoType;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\AdmiComisionCab;
use telconet\schemaBundle\Entity\AdmiComisionDet;
use telconet\schemaBundle\Entity\AdmiComisionHistorial;
use telconet\schemaBundle\Entity\AdmiParametroDet;

/**
 * AdmiProducto controller.
 *
 */
class AdmiProductoController extends Controller
{

    /**
     * Lists all AdmiProducto entities.
     * 
     * @return Response
     * 
     * @version 1.0
     * 
     * Se verifica si tiene disponibles los roles de creación y eliminación para enviarlos como 
     * parametros a la pagina de index
     * 
     * @author Hector Ortega <haortega@telconet.ec> 
     * @version 1.1 2017-02-13
     * 
     * @author Hector Ortega <haortega@telconet.ec> 
     * @version 1.2 2017-03-15 -  Se envia prefijo empresa al arreglo de parametros
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec> 
     * @version 1.3 2017-04-11 -  Se envia ROL al arreglo de parametros
     * Se obtiene por parametro el valor maximo de comision en venta permitido para el producto el cual debe validarse en el ingreso de la plantilla
     * de comisionistas
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec> 
     * @version 1.4 2017-04-25 - Se envia ROL al arreglo de parametros
     * 
     */
    public function indexAction()
    {
        $objRequest          = $this->get('request');        
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');        
        $strIdEmpresa        = $objSession->get('idEmpresa');        
        $objGeneral          = $this->get('doctrine')->getManager('telconet_general');
        $strParamPadre       = 'VALOR_MAXIMO_COMISION_VENTA';
        $strModulo           = 'COMERCIAL';
        $ftlValorMaxComision = 0;
        $arrayParametroDet   = array();
        $arrayParametroDet   = $objGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                          ->getOne($strParamPadre, $strModulo, '', '', '', '', '', '', '', $strIdEmpresa);                            
        if(isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]))
        {
            $ftlValorMaxComision = $arrayParametroDet["valor1"];
        }
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('schemaBundle:AdmiProducto')->findAll();
        
        if(true === $this->get('security.context')->isGranted('ROLE_41-2'))
        {
            $rolesPermitidos[] = 'ROLE_41-2';
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_41-9'))
        {
            $rolesPermitidos[] = 'ROLE_41-9';
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_41-5237'))
        {
            $rolesPermitidos[] = 'ROLE_41-5237';
        }
        if(true === $this->get('security.context')->isGranted('ROLE_41-5257'))
        {
            $rolesPermitidos[] = 'ROLE_41-5257';
        }
    
        if(true === $this->get('security.context')->isGranted('ROLE_41-5297'))
        {
            $rolesPermitidos[] = 'ROLE_41-5297';
        }
    
        $arrayParametros                        = array( 'entities' => $entities );
        $arrayParametros['rolesPermitidos']     = $rolesPermitidos;
        $arrayParametros['strPrefijoEmpresa']   = $strPrefijoEmpresa;
        $arrayParametros['ftlValorMaxComision'] = $ftlValorMaxComision;

        return $this->render('catalogoBundle:AdmiProducto:index.html.twig', 
                             $arrayParametros);               
               
    }

    /**
     * Finds and displays a AdmiProducto entity.
     * Documentación para la función showAction
     * 
     * Función que muestra producto existente.
     * 
     * @return Response 
     *
     * @author Desarrollo Inicial
     * @version 1.0
     * 
     * @author Modificado: Anabelle Penaherrera <apenaherrera@telconet.ec> 
     * @version 1.1 2016-10-07 
     * Se obtiene Prefijo de empresa en sesion y envia como parametro para mostrar los campos que son visibles solo para TN:
     * Grupo, Comisión Venta, Comisión Mantenimiento, Clasificación, Gerente de Producto.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec> 
     * @version 1.2 2017-03-13
     * Cambios Aplicables para TN: 
     * Se agrega nuevo campo requiere comisionar (SI/NO),     
     * Se quita campo Gerente de Producto, este sera ingresado a nivel de Plantilla bajo el esquema nuevo de comisionistas.     
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.3 2017-03-13
     * Cambios Aplicables para TN:
     * Se agrega la informacion de las comisiones en un arreglo para ser visualizadas en el detalle del producto
     */
    public function showAction($id)
    {
        $request           = $this->get('request');
        $session           = $request->getSession();
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $strCodEmpresa     = $session->get('idEmpresa');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:AdmiProducto')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find AdmiProducto entity.');
        }

        $arrayParametros['intIdProducto']    = $entity->getId();
        $arrayParametros['strCodEmpresa']    = $strCodEmpresa;
        
        $deleteForm = $this->createDeleteForm($id);
        $AdmiProductoCaracteristica = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoIdyEstado($entity->getId(), 'Activo');
        if ($strPrefijoEmpresa == 'TN')
        {
            $arrayComisiones    = $em->getRepository('schemaBundle:AdmiProducto')->getComisionPlantilla($arrayParametros);
            $arrayListaComision = $arrayComisiones['listado'];
        }
        
        $listadoNivelesDescuento = $em->getRepository('schemaBundle:InfoProductoNivel')->findPorEstado($entity->getId(), 'Activo');

        $i = 0;
        if($listadoNivelesDescuento)
        {
            foreach($listadoNivelesDescuento as $item):
                $empresa_rol = $em->getRepository('schemaBundle:InfoEmpresaRol')->find($item->getEmpresaRolId());
                $info_rol = $em->getRepository('schemaBundle:AdmiRol')->find($empresa_rol->getRolId());
                $arreglo[$i]['rol'] = $info_rol->getDescripcionRol();
                $arreglo[$i]['porcentaje'] = $item->getPorcentajeDescuento();
                $i++;
            endforeach;
        }

        $listadoImpuestos = $em->getRepository('schemaBundle:InfoProductoImpuesto')->findPorEstado($entity->getId(), 'Activo');

        $i = 0;
        if($listadoImpuestos)
        {
            foreach($listadoImpuestos as $item):
                $arreglo_imp[$i]['impuesto'] = $item->getImpuestoId()->getDescripcionImpuesto();
                $arreglo_imp[$i]['porcentaje'] = $item->getPorcentajeImpuesto();
                $i++;
            endforeach;
        }

        $parametros = array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );

        if($AdmiProductoCaracteristica)
            $parametros['AdmiProductoCaracteristica'] = $AdmiProductoCaracteristica;

        if(isset($arreglo))
            $parametros['niveles_descuento'] = $arreglo;

        if(isset($arreglo_imp))
            $parametros['impuestos'] = $arreglo_imp;
        if(isset($arrayListaComision))
        {
            $parametros['strComisiones'] = $arrayListaComision;
        }
        $parametros['strPrefijoEmpresa']         = $strPrefijoEmpresa;                
        $parametros['fltComisionVenta']          = number_format($entity->getComisionVenta(), 2, '.', ',');
        $parametros['fltComisionMantenimiento']  = number_format($entity->getComisionMantenimiento(), 2, '.', ',');
        $parametros['terminos']  = htmlspecialchars_decode(preg_replace("[\n|\r|\n\r]","",$entity->getTerminoCondicion()));
        
        return $this->render('catalogoBundle:AdmiProducto:show.html.twig', $parametros);
    }

    
    /**
     * Documentación para la función newAction
     * 
     * Función que muestra el formulario para ingresar un nuevo producto.
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 22-04-2016 - Se agrega al formulario las opciones que se deben presentar en el combo de Nombre Técnico.
     * 
     * @author Anabelle Penaherrera<apenaherrera@telconet.ec>
     * @version 1.2 05-10-2016 - Se envia al array de parrametros el prefijo empresa para validar el ingreso de campos
     * aplicables a TN
     * 
     * Se agrega validación de rol para presentar la página de creación del producto
     * 
     * @author Hector Ortega <harotega@telconet.ec>
     * @version 1.3  2017-02-13
     *
     * 
     */
    public function newAction()
    {             
        if (false === $this->get('security.context')->isGranted('ROLE_41-2'))
        {
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                 'mensaje' => 'No tiene permisos para usar esta opción.'));
        }
        
        $request                                     = $this->get('request');
        $session                                     = $request->getSession();
        $strPrefijoEmpresa                           = $session->get('prefijoEmpresa');
             
        $arrayParametrosEntity                       = array();
        $arrayNombresTecnicos                        = $this->getParametrosNombreTecnico();
        $arrayParametrosEntity['arrayNombreTecnico'] = $arrayNombresTecnicos;
               
        $entity = new AdmiProducto();
        $form   = $this->createForm(new AdmiProductoType($arrayParametrosEntity), $entity);
        $arrayParametrosDet=  array();
        $intEmpresaSession    = $session->get('idEmpresa');  
        $emGeneral = $this->get('doctrine')->getManager('telconet_general');
        $strParamPadre        = 'CONTRATO_DIGITAL_FONT_SIZE';
        $strParamDet            = 'CONTRATO_DIGITAL';
        $arrayParametrosDet                 = $emGeneral
        ->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne($strParamPadre  ,
                  '',
                  '',
                  $strParamDet,
                  '',
                  '',
                  '',
                  '',
                  '',
                  $intEmpresaSession);
        if( !empty($arrayParametrosDet)) 
        { 
            $intFontSize=ceil($arrayParametrosDet['valor1']*$arrayParametrosDet['valor2']);
        }          

        return $this->render('catalogoBundle:AdmiProducto:new.html.twig', array(
                                                                                    'entity'             => $entity,
                                                                                    'form'               => $form->createView(),
                                                                                    'msg'                => '',
                                                                                    'strPrefijoEmpresa'  => $strPrefijoEmpresa,
                                                                                    'intFontSize'  =>   $intFontSize,
                                                                                    'arrayResultado'    =>   $arrayResultado
                                                                                ) );
    }
         
    /**
     * Documentación para la función getParametrosNombreTecnico
     * 
     * Función que retorna un array con el listado de los nombres técnicos posibles que se pueden seleccionar al agregar un nuevo producto
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 22-04-2016
     */
    private function getParametrosNombreTecnico()
    {
        
        $arrayNombresTecnicos = array();
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();
        $intEmpresaSession    = $objSession->get('idEmpresa');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $arrayParametrosDet   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                          ->get('NOMBRE_TECNICO', 'COMERCIAL', '', '', '', '', '', '', '', $intEmpresaSession);
        foreach( $arrayParametrosDet as $arrayParametroDet )
        {
            $arrayNombresTecnicos[$arrayParametroDet['descripcion']] = $arrayParametroDet['descripcion'];
        }
        
        return $arrayNombresTecnicos;
    }
    

    /**
     * Creates a new AdmiProducto entity.
     *
     */
    /* public function createAction()
      {
      $entity  = new AdmiProducto();
      $request = $this->getRequest();
      $form    = $this->createForm(new AdmiProductoType(), $entity);
      $form->bind($request);

      if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $estado='Activo';
      $entity->setEstado($estado);
      //Para que guarde la fecha y el usuario correspondiente
      $entity->setFeCreacion(new \DateTime('now'));
      //$entity->setUsrCreacion($user->getUsername());
      $entity->setUsrCreacion('amontero');
      $entity->setIpCreacion($request->getClientIp());
      $em->persist($entity);
      $em->flush();
      return $this->redirect($this->generateUrl('admiproducto_show', array('id' => $entity->getId())));

      }

      return $this->render('catalogoBundle:AdmiProducto:new.html.twig', array(
      'entity' => $entity,
      'form'   => $form->createView()
      ));
      } */

    /**
     * Documentación para la función editAction
     * 
     * Función que edita un producto ya creado.
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 22-04-2016 - Se agrega al formulario las opciones que se presentarán en el combo de Nombre Técnico.
     * 
     * Se agrega validación por roles para presentar la página de edición de productos
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.2 2017-02-13
     * 
     */
    public function editAction($id)
    {
        
        if (false === $this->get('security.context')->isGranted('ROLE_41-4'))
        {
            return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                 'mensaje' => 'No tiene permisos para usar esta opción.'));
        }
        
        $arrayParametrosEntity                       = array();
        $arrayNombresTecnicos                        = $this->getParametrosNombreTecnico();
        $arrayParametrosEntity['arrayNombreTecnico'] = $arrayNombresTecnicos;
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:AdmiProducto')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find AdmiProducto entity.');
        }

        $editForm   = $this->createForm(new AdmiProductoType($arrayParametrosEntity), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $AdmiProductoCaracteristica = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoIdyEstado($entity->getId(), 'Activo');

        return $this->render('catalogoBundle:AdmiProducto:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'AdmiProductoCaracteristica' => $AdmiProductoCaracteristica,
        ));
    }

    /**
     * Edits an existing AdmiProducto entity.
     *
     */
    /* public function updateAction($id)
      {
      $em = $this->getDoctrine()->getManager();

      $entity = $em->getRepository('schemaBundle:AdmiProducto')->find($id);

      if (!$entity) {
      throw $this->createNotFoundException('Unable to find AdmiProducto entity.');
      }

      $editForm   = $this->createForm(new AdmiProductoType(), $entity);
      $deleteForm = $this->createDeleteForm($id);

      $request = $this->getRequest();

      $editForm->bind($request);

      if ($editForm->isValid()) {
      $entity->setFeUltMod(new \DateTime('now'));
      //$entity->setUsrCreacion($user->getUsername());
      $entity->setUsrUltMod('amontero');
      $em->persist($entity);
      $em->flush();
      return $this->redirect($this->generateUrl('admiproducto_show', array('id' => $id)));
      }

      return $this->render('catalogoBundle:AdmiProducto:edit.html.twig', array(
      'entity'      => $entity,
      'edit_form'   => $editForm->createView(),
      'delete_form' => $deleteForm->createView(),
      ));
      } */

    /**
     * Documentación para la función updateAction
     * 
     * Función que actualiza un producto ya creado.
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 22-04-2016 - Se agrega al formulario las opciones que se presentan en el combo de Nombre Técnico.
     * 
     * Se agrega la validación de roles.
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.2 2017-02-13
     * 
     * @Secure(roles="ROLE_41-5")
     */
    public function updateAction(Request $request, $id)
    {
        $arrayParametrosEntity                       = array();
        $arrayNombresTecnicos                        = $this->getParametrosNombreTecnico();
        $arrayParametrosEntity['arrayNombreTecnico'] = $arrayNombresTecnicos;
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('schemaBundle:AdmiProducto')->find($id);
        //$session  = $request->getSession();
        //$user = $this->get('security.context')->getToken()->getUser();
        $listado_caracteristicas = $request->get('listado_caracteristicas');
        //print_r($listado_caracteristicas);
        //echo"<br />";
        $caracteristicas = explode(",", $listado_caracteristicas);

        $listado_descuentos = $request->get('listado_descuentos');
        //$descuentos[]=explode(",", $listado_descuentos);
        $descuentos = json_decode($listado_descuentos);

        $listado_impuestos = $request->get('listado_impuestos');
        $impuestos = explode(",", $listado_impuestos);

        //print_r($impuestos);
        //die();
        $estado = 'Activo';
        $estadoI = 'Inactivo';
        $request = $this->getRequest();
        $session = $request->getSession();
        $id_empresa = $session->get('idEmpresa');
        //$id_empresa="10";

        $deleteForm = $this->createDeleteForm($id);
        $editForm   = $this->createForm(new AdmiProductoType($arrayParametrosEntity), $entity);
        $editForm->bind($request);

        //if ($editForm->isValid()) {
        $info_form = $request->get('telconet_schemabundle_admiproductotype');
        $instalacion = $info_form['instalacion'];
        //echo $instalacion;
        //die();
        $entity->setInstalacion($instalacion);
        $em->persist($entity);
        $em->flush();

        //$em->getConnection()->beginTransaction();
        //try{

        /* $entity->setCodigoProducto($request->get('codigoProducto'));
          $entity->setDescripcionProducto($request->get('descripcionProducto'));
          $entity->setFuncionPrecio($request->get('funcionPrecio'));
          $entity->setFuncionCosto($request->get('funcionCosto'));
          $entity->setInstalacion($request->get('instalacion'));
          $entity->setEstado($estado);
          $em->persist($entity);
          $em->flush();
         */
        //die();
        //PONE ESTADO INACTIVO A TODOS LOS caracteristicas DEL PRODUCTO que tengan estado ACTIVO
        $AdmiProductoCaracteristica = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoId($id);
        if($AdmiProductoCaracteristica)
        {
            foreach($AdmiProductoCaracteristica as $emp)
            {
                $entityProdCaract = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->find($emp->getId());
                $entityProdCaract->setEstado($estadoI);
                $em->persist($entityProdCaract);
                $em->flush();
            }
        }
        //GRABAR caracteristicas
        if($caracteristicas)
        {
            for($i = 0; $i < count($caracteristicas); $i++)
            {
                $entityProductoCaracteristica = new AdmiProductoCaracteristica();
                $caracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica($caracteristicas[$i]);
                $entityProductoCaracteristica->setProductoId($entity);
                $entityProductoCaracteristica->setCaracteristicaId($caracteristica);
                $entityProductoCaracteristica->setFeCreacion(new \DateTime('now'));
                $entityProductoCaracteristica->setUsrCreacion($session->get('user'));
                $entityProductoCaracteristica->setEstado($estado);
                $em->persist($entityProductoCaracteristica);
                $em->flush();
            }
        }

        //Inactivo los existentes
        $InfoProductoNivel = $em->getRepository('schemaBundle:InfoProductoNivel')->findByProductoId($id);
        if($InfoProductoNivel)
        {
            foreach($InfoProductoNivel as $info)
            {
                $info->setEstado($estadoI);
                $em->persist($info);
                $em->flush();
            }
        }

        if($descuentos)
        {
            foreach($descuentos as $descuento)
            {
                $info_empresa_rol = $em->getRepository('schemaBundle:InfoEmpresaRol')->findPorNombreRolPorEmpresa(current($descuento), $id_empresa);
                if($info_empresa_rol)
                {
                    $entityProductoNivel = new InfoProductoNivel();
                    $entityProductoNivel->setProductoId($entity);
                    $entityProductoNivel->setEmpresaRolId($info_empresa_rol->getId());
                    $entityProductoNivel->setPorcentajeDescuento(end($descuento));
                    $entityProductoNivel->setEstado("Activo");
                    $em->persist($entityProductoNivel);
                    $em->flush();
                }
            }
        }

        //Inactivo los existentes
        $InfoProductoImpuesto = $em->getRepository('schemaBundle:InfoProductoImpuesto')->findByProductoId($id);
        if($InfoProductoImpuesto)
        {
            foreach($InfoProductoImpuesto as $info)
            {
                $info->setEstado($estadoI);
                $em->persist($info);
                $em->flush();
            }
        }

        if($impuestos)
        {
            for($i = 0; $i < count($impuestos); $i++)
            {
                $entityProductoImpuesto = new InfoProductoImpuesto();
                $impuesto = $em->getRepository('schemaBundle:AdmiImpuesto')->findOneByDescripcionImpuesto($impuestos[$i]);
                $entityProductoImpuesto->setProductoId($entity);
                $entityProductoImpuesto->setImpuestoId($impuesto);
                $entityProductoImpuesto->setPorcentajeImpuesto($impuesto->getPorcentajeImpuesto());
                $entityProductoImpuesto->setFeCreacion(new \DateTime('now'));
                $entityProductoImpuesto->setUsrCreacion($session->get('user'));
                $entityProductoImpuesto->setEstado($estado);
                $em->persist($entityProductoImpuesto);
                $em->flush();
            }
        }
        /*
          $em->getConnection()->commit();
          }
          catch (\Exception $e) {
          // Rollback the failed transaction attempt
          $em->getConnection()->rollback();
          $em->getConnection()->close();
          //aqu? alg?n mensaje con la excepci?n concatenada
          } */
        return $this->redirect($this->generateUrl('admiproducto_edit', array('id' => $id)));

        /* $response = new Response(json_encode(array('msg'=>'ok','id'=>$entity->getId())));
          $response->headers->set('Content-type', 'text/json');
          return $response; */
    }

    /**
     * Deletes a AdmiProducto entity.
     * 
     * @version 1.0
     * 
     * Se agrega validación de roles
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.1 2017-02-13
     * 
     * @Secure(roles="ROLE_41-8")
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();
        $form->bind($request);
        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:AdmiProducto')->find($id);
            if(!$entity)
            {
                throw $this->createNotFoundException('Unable to find AdmiProducto entity.');
            }
            $entity->setEstado('Inactivo');
            $em->persist($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admiproducto'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                ->add('id', 'hidden')
                ->getForm()
        ;
    }

    /**
     * Documentación para el método 'ajaxListAllAction'.
     * 
     * Método que consulta el listado de productos.
     * 
     * @return Response Lista de Productos.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 11-12-2015
     * @since 1.0
     * Se agregó el tipo de producto (Bien/Servicio) a la consulta de listado de productos. 
     * 
     * @author Modificado: Duval medina C. <dmedina@telconet.ec>
     * @version 1.2 2016-05-26 Modificar los parámetros de consulta para cargar la consulta adecuadamente
     *              2016-05-31 No listar Productos en la pantalla inicial
     *                         Incluir nombre del producto para la busqueda
     * 
     * @author Modificado: Duval Medina C. <dmedina@telconet.ec>
     * @version 1.3 2016-06-15 Invocar método que obtine solo 10 productos como consulta inicial
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec> 
     * @version 1.4 2017-03-13
     * Se agrega al arreglo si el producto esta marcado que requiere_comisionar SI/NO
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.5 2017-06-09
     * Se agrega el filtro Grupos y se adiciona al Grid los campos grupos y subgrupos
     * Se elimina los el encerado a 'ACTIVO' de los estados para una mejor busqueda
     */
    public function ajaxListAllAction()
    {

        $request       = $this->getRequest();
        $em            = $this->get('doctrine')->getManager('telconet');

        $session       = $request->getSession();
        $empresa_cod   = $session->get('idEmpresa');

        $fechaDesde    = explode('T', $request->get("fechaDesde"));
        $fechaHasta    = explode('T', $request->get("fechaHasta"));
        $estado        = $request->get("estado");
        $nombreTecnico = $request->get("nombreTecnico");
        $descripcion   = $request->get("descripcion");
        $strGrupo      = $request->get("strGrupo");
        $limit         = $request->get("limit");
        $page          = $request->get("page");
        $start         = $request->get("start");

        if(!$fechaDesde[0] && !$fechaHasta[0] && !$estado && !$nombreTecnico && !$descripcion && !$strGrupo)
        {//Se presume como la pagina Inicial
            $resultado = $em->getRepository('schemaBundle:AdmiProducto')
                                ->findTodosProductosPorEstadoYEmpresa( 
                                              'Todos',
                                              'Activo',
                                              $empresa_cod,
                                              $limit,
                                              $page,
                                              $start
                                    );
        }
        else
        {
            if(!$nombreTecnico || $nombreTecnico=='')
            {
                $nombreTecnico = "Todos";
            }

            $resultado = $em->getRepository('schemaBundle:AdmiProducto')
                                ->findTodosProductosPorEstadoYEmpresaCriterios(
                                        array('nombreTecnico' => $nombreTecnico,
                                              'estado'        => $estado,
                                              'fechaDesde'    => $fechaDesde[0],
                                              'fechaHasta'    => $fechaHasta[0],
                                              'descripcion'   => $descripcion,
                                              'strGrupo'      => $strGrupo,
                                              'empresa_cod'   => $empresa_cod,
                                              'limit'         => $limit,
                                              'page'          => $page,
                                              'start'         => $start
                                             )
                                    );
        }
        
        $productos = $resultado['registros'];
        $total = $resultado['total'];

        $i = 1;

        foreach($productos as $producto)
        {
            if($i % 2 == 0)
            {
                $class = 'k-alt';
            }
            else
            {
                $class = '';
            }
            $urlVer      = $this->generateUrl('admiproducto_show', array('id' => $producto->getId()));
            $urlEditar   = $this->generateUrl('admiproducto_edit', array('id' => $producto->getId()));
            $urlEliminar = $this->generateUrl('admiproducto_delete_ajax', array('id' => $producto->getId()));            
            
            // Tipo (Bien/Servicio) en consulta de los productos. 
            $arreglo[] = array(
                'idproducto'             => $producto->getId(),
                'codigo'                 => $producto->getCodigoProducto(),
                'descripcion'            => $producto->getDescripcionProducto(),
                'nombreTecnico'          => $producto->getNombreTecnico(),
                'tipo'                   => ($producto->getTipo() == null ? null : ($producto->getTipo() == 'S' ? 'Servicio' : 'Bien')),
                'funcionPrecio'          => $producto->getFuncionPrecio(),
                'funcionCosto'           => $producto->getFuncionCosto(),
                'instalacion'            => $producto->getInstalacion(),
                'strGrupo'               => $producto->getGrupo(),
                'strSubgrupo'            => $producto->getSubgrupo(),
                'fechaCreacion'          => strval(date_format($producto->getFeCreacion(), "d/m/Y G:i")),
                'estado'                 => $producto->getEstado(),
                'linkVer'                => $urlVer,
                'linkEditar'             => $urlEditar,
                'linkEliminar'           => $urlEliminar,
                'strRequiereComisionar'  => $producto->getRequiereComisionar()
            );
            $i++;
        }

        if(empty($arreglo))
        {
            $arreglo[] = array(
                               'idproducto'             => "",
                               'codigo'                 => "",
                               'descripcion'            => "",
                               'nombreTecnico'          => "",
                               'tipo'                   => "",
                               'funcionPrecio'          => "",
                               'funcionCosto'           => "",
                               'instalacion'            => "",
                               'strGrupo'               => "",
                               'strSubgrupo'            => "",
                               'fechaCreacion'          => "",
                               'estado'                 => "",
                               'linkVer'                => "",
                               'linkEditar'             => "",
                               'linkEliminar'           => "",
                               'strRequiereComisionar'  => ""
            );
        }
        $response = new Response(json_encode(array('total' => $total, 'productos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function ajaxGetCaracteristicasAction()
    {
        //$request = $this->getRequest();
        //$session  = $request->getSession();
        //$idEmpresa = $session->get('idEmpresa');
        $estado = "Activo";
        $em = $this->get('doctrine')->getManager('telconet');
        $caracteristicas = $em->getRepository('schemaBundle:AdmiCaracteristica')->findTodasPorEstado($estado);
        if(!$caracteristicas)
        {
            $arr_caracteristicas[] = array("id" => "", "descripcion" => "");
        }
        else
        {
            $arr_caracteristicas = array();
            foreach($caracteristicas as $caracteristica)
            {
                $tecn['id'] = $caracteristica->getId();
                $tecn['descripcion'] = $caracteristica->getDescripcionCaracteristica();
                $arr_caracteristicas[] = $tecn;
            }
        }
        $response = new Response(json_encode(array('caracteristica' => $arr_caracteristicas)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function ajaxGetCaracteristicasProductoAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        $idProducto = $request->request->get("idProducto");
        $caracteristica = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoIdyEstado($idProducto, 'Activo');
        //print_r($telefono);die();
        //$clientes[] = array("idCl"=>"$telefono","loginClienteSucursal"=>"","descripcion"=>"");
        //print_r($caracteristica);
        //die();
        if(!$caracteristica)
        {
            $caracteristicas[] = array("idCaracteristica" => "", "caracteristica" => "");
        }
        else
        {
            $caracteristicas = array();
            foreach($caracteristica as $emp)
            {
                $tecn['idCaracteristica'] = $emp->getId();
                $tecn['caracteristica'] = $emp->getCaracteristicaId()->getDescripcionCaracteristica();
                $caracteristicas[] = $tecn;
            }
        }

        //print_r($caracteristicas);
        //die();
        $response = new Response(json_encode($caracteristicas));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }    
    
    /**
     * Documentación para la función createAction
     * 
     * Función que guarda un nuevo producto.
     * 
     * @return Response 
     *
     * @author Desarrollo Inicial
     * @version 1.0
     * 
     * @author Modificado: Duval Medina <dmedina@telconet.ec> 
     * @version 1.1 2016-04-20 - Se agregan validaciones adicionales y se presntan como error estas y las ya existentes.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 22-04-2016 - Se agrega al formulario las opciones que se presentan en el combo de Nombre Técnico.
     * 
     * @author Modificado: Duval Medina <dmedina@telconet.ec> 
     * @version 1.3 2016-05-31 - Se ajustó validacion de los listados en Crear Producto: 
     *                           impuesto (obligatorio), caracteristica (obligatorio) y descuento (opcional)
     * 
     * @author Modificado: Anabelle Penaherrera <apenaherrera@telconet.ec> 
     * @version 1.4 2016-10-05 
     * 1.- Se valida por prefijo_empresa campo nuevos aplicables solo para TN: Grupo, Comisión Venta, Comisión Mantenimiento,
     *     Clasificación, Gerente de Producto.
     * 2.- Se quita campo del formulario ES_CONCENTRADOR , ya que el campo sera marcado internamente y no de seleccion.
     * 3.- Para el caso de productos con CLASIFICACION -> DATOS, se procede a crear automaticamente el PRODUCTO CONCENTRADOR el cual
     *     debera tener precio cero e instalacion cero, solo si no existe ya creado el concentrador.
     * 4.- Se debera crear Plantilla de Caracteristicas necesarias para Productos Concentradores ADMI_PRODUCTO_CARACTERISTICA
     *     Las Caracteristicas requeridas para un Concentrador estaran definidas en la tabla ADMI_PARAMETRO_DET 
     *     con el parametro padre 'CARACTERISTICAS_CONCENTRADOR'    
     * 5.- Se debera crear en el producto extremo la caracteristica ENLACE_DATOS, la cual relaciona los enlaces extremo-concentrador
     * 6.- Se debera Marcar al producto concentrador en el campo ES_CONCENTRADOR en SI, todo producto que no sea concentrador debera estar
     *     marcado en NO.
     * 
     * Se agrega validación de roles
     * @author Hector Ortega <haortega@telconet.ec> 
     * @version 1.5 2017-02-13
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec> 
     * @version 1.6 2017-03-13
     * Cambios Aplicables para TN: 
     * Se agrega a la interfaz para producto nuevo campo requiere comisionar (SI/NO), si producto se marca que requiere comisionar, este
     * se guardara en estado "Pendiente",  y una vez sea ingresada la Planilla de Comisionistas este se activara para la venta. 
     * Quitar campos para el ingreso de comisión en venta, comision en mantenimiento. 
     * Se quita campo Gerente de Producto, este sera ingresado a nivel de Plantilla bajo el esquema nuevo de comisionistas.     
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec> 
     * @version 1.7 2017-04-13
     * Se modifica para que los concentradores que se generan sean marcados que no requieren Comisionar y estado Activo
     * 
     * @author David Leon <mdleon@telconet.ec> 
     * @version 1.8 2019-06-12
     * Se modifica para incluir el registro de subgrupos y linea de negocios en la tabla Admi_Producto
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.9 07-10-2020 - Se agrega sincronización entre TelcoS+ y TelcoCRM, esto permitirá la creación
     *                           del producto en TelcoCRM.
     * 
     * @author David Leon <mdleon@telconet.ec> 
     * @version 2.0 2020-11-27 - Se agregan los datos de grupo y subgrupo a la sincronización de TelcoCRM.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec> 
     * @version 2.1 2021-05-17 - Se almacena los terminos y condiciones del producto.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.2 29-12-2022 - Se elimina sincronización entre TelcoS+ y TelcoCRM.
     *                           Por que ahora la sincronización se hará desde el trigger db_comercial.after_admi_producto.
     *
     * @Secure(roles="ROLE_41-3")
     * 
     */
    public function createAction(Request $request)
    {                
        $session                                     = $request->getSession();
        $strPrefijoEmpresa                           = $session->get('prefijoEmpresa');     
        $strCodEmpresa                               = $session->get("idEmpresa");
        $strUsuarioCreacion                          = $session->get('user');
        $arrayParametrosEntity                       = array();
        $arrayNombresTecnicos                        = $this->getParametrosNombreTecnico();
        $arrayParametrosEntity['arrayNombreTecnico'] = $arrayNombresTecnicos;
        $boolEsConcentrador                          = false;
        $serviceTelcoCrm                             = $this->get('comercial.ComercialCrm');
        $em     = $this->get('doctrine')->getManager('telconet');
        $entity = new AdmiProducto();

        $form = $this->createForm(new AdmiProductoType($arrayParametrosEntity), $entity);
        
        $form->bind($request);
                
        $strEstado             = 'Activo';
        // Valido campos ingresados por Prefijo_Empresa TN
        if($strPrefijoEmpresa == 'TN')
        {
            $strGrupo = $request->request->get('grupo');
            if($strGrupo == null)
            {
                $request->getSession()->getFlashBag()->add('error', 'Debe seleccionar el Grupo al que pertenece el producto.');
                return $this->redirect($this->generateUrl('admiproducto_new'));
            }
            $entity->setGrupo($strGrupo);
            
            $strSubGrupo = $request->request->get('subgrupo');
            if($strSubGrupo == null)
            {
                $request->getSession()->getFlashBag()->add('error', 'Debe seleccionar el SubGrupo al que pertenece el producto.');
                return $this->redirect($this->generateUrl('admiproducto_new'));
            }
            $entity->setSubgrupo($strSubGrupo);
            
            $strLineaNegocio = $request->request->get('tiponegocio');
            if($strLineaNegocio == null)
            {
                $request->getSession()->getFlashBag()->add('error', 'Debe seleccionar la linea de negocio que pertenece el producto.');
                return $this->redirect($this->generateUrl('admiproducto_new'));
            }
            $entity->setLineaNegocio($strLineaNegocio);
                        
            $strClasificacion = $request->request->get('clasificacion');
            if($strClasificacion != null)
            {
               $entity->setClasificacion($strClasificacion);
            }                        
            /* Si producto requiere comisionar este debera guardarse en estado Pendiente , y una vez sea ingresada la Planilla 
             * de Comisionistas este se activara para la venta.
             */
            if($entity->getRequiereComisionar() == 'SI')
            {
                $strEstado  = 'Pendiente';
            }                        
        }
        else
        {
            $entity->setRequiereComisionar('NO');        
        }
        
        $entity->setEsConcentrador('NO');

        //INICIO Validación de campos ingresados
        $nombre_tecnico =  $request->request->get('nombreTecnico');
        if($nombre_tecnico == null)
        {
            $request->getSession()->getFlashBag()->add('error', 'Debe seleccionar el Nombre Técnico.');
            return $this->redirect($this->generateUrl('admiproducto_new'));
        }            
        $entity->setNombreTecnico($nombre_tecnico);
        
        if($entity->getTipo() == null)
        {
            $request->getSession()->getFlashBag()->add('error', 'Debe seleccionar el Tipo de Producto.');
            return $this->redirect($this->generateUrl('admiproducto_new'));
        }
        if(!is_numeric($entity->getInstalacion()))
        {
            $request->getSession()->getFlashBag()->add('error', 'Debe ingresar un valor numérico en Instalación.');
            return $this->redirect($this->generateUrl('admiproducto_new'));
        }
        
        $estado_inicial=  $request->request->get('estadoInicial');
        if($estado_inicial == null)
        {
            $request->getSession()->getFlashBag()->add('error', 'Debe seleccionar el Estado Inicial.');
            return $this->redirect($this->generateUrl('admiproducto_new'));
        }
        $entity->setEstadoInicial($estado_inicial);

        $listado_impuestos = $request->get('listado_impuestos');
        if(empty($listado_impuestos))
        {
            $request->getSession()->getFlashBag()->add('error', 'Debe ingresar al menos un impuesto.');
            return $this->redirect($this->generateUrl('admiproducto_new'));
        }
        $impuestos = explode(",", $listado_impuestos);
        
        $listado_caracteristicas = $request->get('listado_caracteristicas');
        if(empty($listado_caracteristicas))
        {
            $request->getSession()->getFlashBag()->add('error', 'Debe ingresar al menos una caracteristica.');
            return $this->redirect($this->generateUrl('admiproducto_new'));
        }
        $caracteristicas = explode(",", $listado_caracteristicas);
        
        $listado_descuentos = $request->get('listado_descuentos');
        if(!empty($listado_descuentos))
        {
            $descuentos[] = explode(",", $listado_descuentos);
        }
        $strTerminosCondiciones = $request->get('terminos_condiciones');
        if($strTerminosCondiciones)
        {
            $entity->setTerminoCondicion($strTerminosCondiciones);
        }
        //FIN de validaciones
                       
        $estado = 'Activo';
        $request = $this->getRequest();
        $session = $request->getSession();
        $id_empresa = $session->get('idEmpresa');
        $empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($id_empresa);

        $entity->setFeCreacion(new \DateTime('now'));
        $usrCreacion = $session->get('user');
        $entity->setUsrCreacion($usrCreacion);
        $entity->setIpCreacion($request->getClientIp());
        $entity->setEstado($strEstado);
        $entity->setEmpresaCod($empresa);
        $em->persist($entity);
        $em->flush();

        if(isset($caracteristicas))
        {
            for($i = 0; $i < count($caracteristicas); $i++)
            {
                $entityProductoCaracteristica = new AdmiProductoCaracteristica();
                $caracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica($caracteristicas[$i]);
                $entityProductoCaracteristica->setProductoId($entity);
                $entityProductoCaracteristica->setCaracteristicaId($caracteristica);
                $entityProductoCaracteristica->setFeCreacion(new \DateTime('now'));
                $entityProductoCaracteristica->setUsrCreacion($usrCreacion);
                $entityProductoCaracteristica->setEstado($estado);
                $entityProductoCaracteristica->setVisibleComercial("SI");
                $em->persist($entityProductoCaracteristica);
                $em->flush();
            }
        }

        if(isset($descuentos))
        {
            foreach($descuentos as $descuento)
            {
                $info_empresa_rol = $em->getRepository('schemaBundle:InfoEmpresaRol')->findPorNombreRolPorEmpresa($descuento[0], $id_empresa);
                if($info_empresa_rol)
                {
                    $entityProductoNivel = new InfoProductoNivel();
                    $entityProductoNivel->setProductoId($entity);
                    $entityProductoNivel->setEmpresaRolId($info_empresa_rol->getId());
                    $entityProductoNivel->setPorcentajeDescuento($descuento[1]);
                    $em->persist($entityProductoNivel);
                    $entityProductoNivel->setEstado("Activo");
                    $em->flush();
                }
            }
        }

        if(isset($impuestos))
        {
            for($i = 0; $i < count($impuestos); $i++)
            {
                $entityProductoImpuesto = new InfoProductoImpuesto();
                $impuesto = $em->getRepository('schemaBundle:AdmiImpuesto')->findOneByDescripcionImpuesto($impuestos[$i]);
                $entityProductoImpuesto->setProductoId($entity);
                $entityProductoImpuesto->setImpuestoId($impuesto);
                $entityProductoImpuesto->setPorcentajeImpuesto($impuesto->getPorcentajeImpuesto());
                $entityProductoImpuesto->setFeCreacion(new \DateTime('now'));
                $entityProductoImpuesto->setUsrCreacion($usrCreacion);
                $entityProductoImpuesto->setEstado($estado);
                $em->persist($entityProductoImpuesto);
                $em->flush();
            }
        }

        if($strPrefijoEmpresa == 'TN')
        {
            /* Si la clasificacion del producto es DATOS debo crearle la caracteristica ENLACE_DATOS si no existe, 
             * y debo crear automaticamente su producto concentrador si no existe*/
            if($strClasificacion == 'DATOS')
            {
                //creacion de registros para el nuevo enlace de datos
                $objCaracteristicaEnlaceDatos = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneBy(array("descripcionCaracteristica" => 'ENLACE_DATOS',
                                                                     "estado"                    => "Activo"));
                if(!is_object($objCaracteristicaEnlaceDatos))
                {                   
                    $request->getSession()->getFlashBag()->add('error', 'No existe Caracteristica ENLACE_DATOS definida,'
                                                             . ' No se puede crear Producto');
                    
                    return $this->redirect($this->generateUrl('admiproducto_new'));
                }
                $objProdCaractEnlaceDatos = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                               ->findOneBy(array( 
                                                                 "productoId"       => $entity->getId(),
                                                                 "caracteristicaId" => $objCaracteristicaEnlaceDatos->getId(),
                                                                 "estado"           => "Activo"
                                                                ));
                // Creo caracteristica del Producto ES_ENLACE
                if(!is_object($objProdCaractEnlaceDatos))
                {
                    $objAdmiProductoCaracteristica = new AdmiProductoCaracteristica();
                    $objAdmiProductoCaracteristica->setProductoId($entity);
                    $objAdmiProductoCaracteristica->setCaracteristicaId($objCaracteristicaEnlaceDatos);
                    $objAdmiProductoCaracteristica->setFeCreacion(new \DateTime('now'));
                    $objAdmiProductoCaracteristica->setUsrCreacion($usrCreacion);
                    $objAdmiProductoCaracteristica->setEstado($estado);
                    $objAdmiProductoCaracteristica->setVisibleComercial("NO");
                    $em->persist($objAdmiProductoCaracteristica);
                    $em->flush();
                }
                
                // Verifico si existe PRODUCTO CONCENTRADOR por NOMBRE_TECNICO, estado Tipo y empresa
                $objProductoConcentrador = $em->getRepository('schemaBundle:AdmiProducto')
                                              ->findOneBy(array("nombreTecnico"  => $nombre_tecnico,
                                                                "estado"         => "Activo",
                                                                "esConcentrador" => "SI",
                                                                "empresaCod"     => $id_empresa));
                //Si no existe Producto Concentrador lo inserto
                if(!is_object($objProductoConcentrador))
                {
                    $boolEsConcentrador          = true;
                    $objAdmiProductoConcentrador = clone $entity;                    
                    $objAdmiProductoConcentrador->setDescripcionProducto('Concentrador '.$nombre_tecnico);
                    $objAdmiProductoConcentrador->setFuncionCosto('COSTO=0');
                    $objAdmiProductoConcentrador->setInstalacion(0);
                    $objAdmiProductoConcentrador->setEsConcentrador('SI');
                    $objAdmiProductoConcentrador->setFuncionPrecio('PRECIO=0');
                    $objAdmiProductoConcentrador->setRequiereComisionar('NO');
                    $objAdmiProductoConcentrador->setEstado('Activo');
                    $em->persist($objAdmiProductoConcentrador);
                    $em->flush();
                    
                    // Inserto array de caracteristicas necesarias para el funcionamiento de un Concentrador
                    $arrayCaracteristicas = array();
                    $objGeneral           = $this->get('doctrine')->getManager('telconet_general');
                    $strParamPadre        = 'CARACTERISTICAS_CONCENTRADOR';
                    $strModulo            = 'COMERCIAL';
                    $arrayCaracteristicas = $objGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->get($strParamPadre, $strModulo, '', '', '', '', '', '', '', $id_empresa);
                    
                    // Leo Caracteristicas Definidas para un Concentrador a nivel de la tabla de Parametros
                    foreach($arrayCaracteristicas as $arrayNombre)
                    {                                       
                        $objAdmiCaracteristicaConcentrador = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneByDescripcionCaracteristica($arrayNombre['valor1']);
                        
                        if(!is_object($objAdmiCaracteristicaConcentrador))
                        {
                            $request->getSession()->getFlashBag()->add("error", "No existe Caracteristica ".$arrayNombre['valor1']." definida,"
                                                                     . " No se puede crear Producto Concentrador");
                            
                            return $this->redirect($this->generateUrl('admiproducto_new'));                           
                        }
                        $objProductoCaractConcentrador = new AdmiProductoCaracteristica();
                        $objProductoCaractConcentrador->setProductoId($objAdmiProductoConcentrador);
                        $objProductoCaractConcentrador->setCaracteristicaId($objAdmiCaracteristicaConcentrador);
                        $objProductoCaractConcentrador->setFeCreacion(new \DateTime('now'));
                        $objProductoCaractConcentrador->setUsrCreacion($usrCreacion);
                        $objProductoCaractConcentrador->setEstado($estado);
                        $objProductoCaractConcentrador->setVisibleComercial("SI");
                        $em->persist($objProductoCaractConcentrador);
                        $em->flush();
                     }
                }
            }
            $strNombreTecnico =  $entity->getNombreTecnico();
        }
            
        if($entity)
        {
            return $this->redirect($this->generateUrl('admiproducto_funcion_precio', array('id' => $entity->getId())));
        }
        else
        {
            return $this->redirect($this->generateUrl('admiproducto_new'));
        }
    }

    /**
     * Funcion que devuelve un div para presentar informacion de las caracteristicas para el validador de la
     * funcion Precio de los productos
     * agregados a un plan     
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 08-01-2015    
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 10-01-2016 - Se modifica para que acepte características que pueden ser combo box y se tenga que seleccionar las opciones para
     *                           validar la función ingresada por el usuario.
     * 
     * @param integer $producto  // id del producto     
     * @see \telconet\schemaBundle\Entity\AdmiProducto
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarCaracteristicasPorProductoAction()
    {
        $request = $this->getRequest();
        $productoId = $request->request->get("producto");
        $em = $this->get('doctrine')->getManager('telconet');
        $estado = "Activo";
        $items = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoIdyEstado($productoId, $estado);
        $producto = $em->getRepository('schemaBundle:AdmiProducto')->find($productoId);
        $i = 0;
        $presentar_div = "";
        $emGeneral = $this->get('doctrine')->getManager('telconet_general');


        if($items)
        {
            $presentar_div = "<table class='formulario'>";
            $presentar_div.= "<tr><td>Producto:</td>";
            $presentar_div.= "<td>" . $producto->getDescripcionProducto() . "</td></tr>";

            foreach($items as $item)
            {
                $strDescripcionCaracteristica = $item->getCaracteristicaId()->getDescripcionCaracteristica();

                $presentar_div.= "<tr><td>";

                if($item->getCaracteristicaId()->getTipoIngreso() == 'S')
                {
                    $strOpciones = '';
                    $strNombreParametro = 'PROD_' . $strDescripcionCaracteristica;

                    $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                        ->findOneBy(array('descripcion' => $strNombreParametro,
                        'estado' => 'Activo'));

                    if($objParametroCab)
                    {
                        $objParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                            ->findBy(array('parametroId' => $objParametroCab,
                            'estado' => 'Activo'));

                        if($objParametrosDet)
                        {
                            foreach($objParametrosDet as $objParametro)
                            {
                                $strOpciones .= '<option value="' . $objParametro->getValor1() . '">' . $objParametro->getValor1() . '</option>';
                            }//foreach( $objParametrosDet as $objParametro )
                        }//( $objParametrosDet )
                    }//( $objParametroCab )

                    $presentar_div.= $strDescripcionCaracteristica . ": </td><td>"
                        . "<select name='caracteristicas_$i' id='caracteristicas_$i'>"
                        . $strOpciones
                        . "</select>";
                }
                else
                {
                    $presentar_div.= $strDescripcionCaracteristica . ": </td><td><input type='text' value='' "
                        . "name='caracteristicas_$i' id='caracteristicas_$i'/>";
                }//( $item->getCaracteristicaId()->getTipoIngreso() == 'S' )

                $presentar_div.= "<input type='hidden' value='[" . $strDescripcionCaracteristica . "]' "
                    . " name='caracteristica_nombre_$i' id='caracteristica_nombre_$i'/>";
                $presentar_div.= "<input type='hidden' value='" . $item->getId() . "' name='producto_caracteristica_$i'"
                    . " id='producto_caracteristica_$i'/>";
                $presentar_div.= "</td></tr>";
                $i++;
            }//foreach($items as $item)

            $presentar_div.= "<tr><td><input type='hidden' value='" . $i . "' name='cantidad_caracteristicas'"
                . " id='cantidad_caracteristicas'/></td></tr>";
            $presentar_div.= "<tr><td>Precio:</td>";
            $presentar_div.= "<td><input type='text' value='' name='precio_unitario' id='precio_unitario' readonly='readonly'></td></tr>";
            $presentar_div.= "</table>";
            $presentar_div.= "<button type='button' class='button-crud' onClick='validar_funcion();'>Validar</button>";
            $presentar_div.= " ";
            $presentar_div.= "<button type='button' class='button-crud' onClick='limpiar_detalle();'>Limpiar</button>";
            $arreglo = array('msg' => 'ok', 'div' => $presentar_div);
        }
        else
        {
            $arreglo = array('msg' => 'No existen registros de Caracteristicas definidas para el producto');
        }

        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * Funcion para el ingreso o edicion de la Funcion Precio de un producto
     * 
     * @author telcos
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 09-01-2015
     * @param integer $id  // id del producto     
     * @see \telconet\schemaBundle\Entity\AdmiProducto
     * @return Renders a view.
     */
    public function funcionPrecioAction($id)
    {
        //clausulas 
        $arreglo_clausulas[1] = "if";
        $arreglo_clausulas[2] = "{";
        $arreglo_clausulas[3] = "else";
        $arreglo_clausulas[4] = "&&";
        $arreglo_clausulas[5] = "||";
        $arreglo_clausulas[6] = "PRECIO";
        $arreglo_clausulas[7] = "/";
        $arreglo_clausulas[8] = "*";
        $arreglo_clausulas[9] = "+";
        $arreglo_clausulas[10] = "-";
        $arreglo_clausulas[11] = "=";
        $arreglo_clausulas[12] = "}";
        $arreglo_clausulas[13] = "(";
        $arreglo_clausulas[14] = ")";
        $arreglo_clausulas[15] = "%";
        $arreglo_clausulas[16] = "Math.pow(x, y)";
        $arreglo_clausulas[17] = "Math.floor(x)";
        $arreglo_clausulas[18] = "Math.ceil(x)";
        //caracteristicas del producto
        $em = $this->get('doctrine')->getManager('telconet');
        $list_carac = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoIdyEstado($id, 'Activo');
        $producto = $em->getRepository('schemaBundle:AdmiProducto')->find($id);

        if($producto->getFuncionPrecio() != "")
        {
            $funcion_existente = $producto->getFuncionPrecio();
        }
        else
        {
            $funcion_existente = "";
        }
        foreach($list_carac as $datos)
        {
            $caracteristicas[] = array('descripcionCaracteristica' => "[" . $datos->getCaracteristicaId()->getDescripcionCaracteristica() . "]",);
        }
        return $this->render('catalogoBundle:AdmiProducto:funcion_precio.html.twig', array(
                'clausulas' => $arreglo_clausulas,
                'caracteristicas' => $caracteristicas,
                'id_producto' => $id,
                'funcion_existente' => $funcion_existente,
        ));
    }

    /**
     * 
     * Función para guardar la función precio de un producto
     * 
     * @param integer $id_producto
     * @return Response
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.1 29-09-2020 Se agrega validación para actualizar los precios de los servicios dual band
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 15-12-2020 Se modifica la función para que guarde el parámetro del precio del producto con el respectivo código de empresa
     *                         del producto
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 21-01-2021 Se realizan cambios para permitir guardar los valores para los productos Paramount y Noggin
     * 
     */
    public function guardarFuncionPrecioAction($id_producto)
    {
        $em = $this->get('doctrine')->getManager('telconet');
        $emGeneral          = $this->get('doctrine')->getManager('telconet_general');
        $objProducto        = $em->getRepository('schemaBundle:AdmiProducto')->find($id_producto);
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $strUsrCreacion     = $objSession->get('user');
        $strFuncionPrecio   = $objRequest->get('funcion');
        $objProducto->setFuncionPrecio($strFuncionPrecio);
        $em->persist($objProducto);
        $em->flush();

        if(is_object($objProducto))
        {
            $strCodEmpresaParamProd             = $objProducto->getEmpresaCod()->getId();
            $arrayNombreTecnicoParametrizado    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        'NOMBRES_TECNICOS_PRECIOS_PRODUCTOS_PARAMETRIZADOS',
                                                                        $objProducto->getNombreTecnico(),
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        $strCodEmpresaParamProd);
            if(isset($arrayNombreTecnicoParametrizado) && !empty($arrayNombreTecnicoParametrizado))
            {
                $floatPrecioProductoParamBD = 0;
                $strProductoSinCaracts      = $arrayNombreTecnicoParametrizado['valor3'];
                if($strProductoSinCaracts === "SI")
                {
                    $arrayParamsReemplazar  = array('PRECIO');
                    $arrayValoresReemplazar = array('$floatPrecioProductoParamBD');
                }
                else
                {
                    $arrayParamsReemplazar  = array('[ES_GRATIS]','PRECIO');
                    $arrayValoresReemplazar = array('"NO"', '$floatPrecioProductoParamBD');
                }
                
                $strFuncionPrecioProducto = str_replace($arrayParamsReemplazar, $arrayValoresReemplazar, $strFuncionPrecio);
                $strDigitoVerificacion      = substr($strFuncionPrecioProducto, -1, 1);
                if(is_numeric($strDigitoVerificacion))
                {
                    $strFuncionPrecioProducto = $strFuncionPrecioProducto . ";";
                }
                eval($strFuncionPrecioProducto);
                
                if(isset($floatPrecioProductoParamBD) && !empty($floatPrecioProductoParamBD) && is_numeric($floatPrecioProductoParamBD))
                {
                    $strCodEmpresaParamProd     = $objProducto->getEmpresaCod()->getId();
                    $arrayPrecioPorIdProducto   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        'PRECIOS_PRODUCTOS',
                                                                        $objProducto->getId(),
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        $strCodEmpresaParamProd);
                    if(isset($arrayPrecioPorIdProducto) && !empty($arrayPrecioPorIdProducto))
                    {
                        $objParamDetPrecioPorIdProducto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->find($arrayPrecioPorIdProducto["id"]);
                        if(is_object($objParamDetPrecioPorIdProducto))
                        {
                            $objParamDetPrecioPorIdProducto->setEstado("Eliminado");
                            $objParamDetPrecioPorIdProducto->setUsrUltMod($strUsrCreacion);
                            $objParamDetPrecioPorIdProducto->setFeUltMod(new \DateTime('now'));
                            $emGeneral->persist($objParamDetPrecioPorIdProducto);
                            $emGeneral->flush();
                        }
                    }

                    $objParametroPrecioPorIdProducto    = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                                    ->findOneBy(array(
                                                                                        'nombreParametro'   => 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                        'estado'            => 'Activo'
                                                                                     )
                                                                                );
                    if(is_object($objParametroPrecioPorIdProducto))
                    {
                        $objAdmiParametroDet = new AdmiParametroDet();
                        $objAdmiParametroDet->setParametroId($objParametroPrecioPorIdProducto);
                        $objAdmiParametroDet->setDescripcion("Valores parametrizados de producto que no se pueden evaluar por función precio");
                        $objAdmiParametroDet->setValor1("PRECIOS_PRODUCTOS");
                        $objAdmiParametroDet->setValor2($objProducto->getId());
                        $objAdmiParametroDet->setValor3($floatPrecioProductoParamBD);
                        $objAdmiParametroDet->setEstado("Activo");
                        $objAdmiParametroDet->setUsrCreacion($strUsrCreacion);
                        $objAdmiParametroDet->setUsrCreacion($strUsrCreacion);
                        $objAdmiParametroDet->setFeCreacion(new \DateTime('now'));
                        $objAdmiParametroDet->setIpCreacion($objRequest->getClientIp());
                        $objAdmiParametroDet->setEmpresaCod($strCodEmpresaParamProd);
                        $emGeneral->persist($objAdmiParametroDet);
                        $emGeneral->flush();
                    }
                }
            }
        }
        return $this->redirect($this->generateUrl('admiproducto_show', array('id' => $id_producto)));
    }

    public function listarCaracteristicasExistentesAction()
    {
        $request = $this->getRequest();
        $productoid = $request->get('productoid');

        //$idEmpresa = $session->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        //$idProducto = $request->request->get("idProducto"); 	
        $caracteristica = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoIdyEstado($productoid, 'Activo');
        //print_r($telefono);die();
        //$clientes[] = array("idCl"=>"$telefono","loginClienteSucursal"=>"","descripcion"=>"");
        if(!$caracteristica)
        {
            //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
            $caracteristicas[] = array("descripcionCaracteristica" => "");
        }
        else
        {
            $caracteristicas = array();
            foreach($caracteristica as $emp)
            {
                //$tecn['id'] = $emp->getId();
                $tecn['descripcionCaracteristica'] = $emp->getCaracteristicaId()->getDescripcionCaracteristica();
                $caracteristicas[] = $tecn;
            }
        }
        $response = new Response(json_encode(array('listadoCaracteristicas' => $caracteristicas)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * 
     * Documentación para el método 'estadosAction'.
     * 
     * Método encargado de obtener los posibles estados de los productos en Base de datos
     * 
     * 
     * @return Response.
     * 
     * @author Desarrollo Inicial
     * @version 1.0
     * 
     * @author Modificado: Duval Medina <dmedina@telconet.ec>
     * @version 1.1 2016-05-31  Consolidando los Estados registrados desde la BD
     *                          Eliminación de codigo comentado
     * 
     */
    public function estadosAction()
    {
        $empresa_cod   = $this->get('request')->getSession()->get('idEmpresa');
        $objManager     = $this->get('doctrine')->getManager('telconet');
        $estados = $objManager->getRepository('schemaBundle:AdmiProducto')
                                ->findEstadosPorEmpresa($empresa_cod);

        foreach($estados as $estado)
        {
            $arreglo[] = array('idEstado' => $estado['estado'], 'codigo' => 'ACT', 'descripcion' => $estado['estado']);
        }

        $response = new Response(json_encode(array('estados' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    
    /**
     * @Secure(roles="ROLE_41-9")
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * Este método se encarga de borrar el o los productos que le envien
     * en la petición separados por el caracter |
     * @version 1.0
     * 
     * Se agrega validación de roles
     * @author Hector Ortega <haortega@telconet.ec> 
     * @version 1.1 2017-02-13
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 07-10-2020 - Se agrega sincronización entre TelcoS+ y TelcoCRM, esto permitirá eliminar
     *                           el producto en TelcoCRM.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 29-12-2022 - Se elimina sincronización entre TelcoS+ y TelcoCRM.
     *                           Por que ahora la sincronización se hará desde el trigger db_comercial.after_admi_producto.
     *
     */
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $parametro = $peticion->get('id');

        $serviceTelcoCrm    = $this->get('comercial.ComercialCrm');
        $objSession         = $peticion->getSession();
        $strUsuarioCreacion = $objSession->get('user'); 
        $strPrefijoEmpresa  = $objSession->get("prefijoEmpresa") ? $objSession->get("prefijoEmpresa") : '';
        $strEmpresaCod      = $objSession->get("idEmpresa") ? $objSession->get("idEmpresa") : '';


        if($parametro)
            $array_valor = explode("|", $parametro);
        else
        {
            $parametro = $peticion->get('param');
            $array_valor = explode("|", $parametro);
        }
        //print_r($array_valor);
        $em = $this->getDoctrine()->getManager();
        foreach($array_valor as $id):
            //echo $id;
            $entity = $em->getRepository('schemaBundle:AdmiProducto')->find($id);
            if($entity)
            {
                $entity->setEstado("Inactivo");
                $em->persist($entity);
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
            else
                $respuesta->setContent("No existe el registro");
        endforeach;
        //die();
        return $respuesta;
    }

    public function listarNivelesDescuentoAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        //$idEmpresa="10";
        $em = $this->get('doctrine')->getManager('telconet');
        $roles_listado = $em->getRepository('schemaBundle:InfoEmpresaRol')->findRolesPorEmpresa($idEmpresa);
        if(!$roles_listado)
        {
            $arr_roles[] = array("id" => "", "descripcion" => "");
        }
        else
        {
            $arr_roles = array();
            foreach($roles_listado as $rol)
            {
                $tecn['id1'] = $rol->getId();
                $info_rol = $em->getRepository('schemaBundle:AdmiRol')->find($rol->getRolId());
                $tecn['descripcion1'] = $info_rol->getDescripcionRol();
                $arr_roles[] = $tecn;
            }
        }
        $response = new Response(json_encode(array('descuento' => $arr_roles)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * listarImpuestosAction
     *
     * Método que retorna los impuestos de la ciudad dependiendo de los criterios enviados por el usuario.                                    
     *
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 16-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 28-06-2016 - Se añaden los parámetros para encontrar los impuestos relacionados al detalle de una factura
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 21-11-2016 - Se añade validación para que retorne información de la tabla 'DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_IMP' cuando
     *                           se busca los impuestos de una factura para crear una Nota de Crédito con Valor original.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 28-06-2017 - Se agrega el parametro país
     */
    public function listarImpuestosAction()
    {
        $request   = $this->getRequest();
        $session   = $request->getSession();
        $intIdPais = $session->get('intIdPais');
        
        $em = $this->get('doctrine')->getManager('telconet_general');
        
        $arrayParametros = array();
        $arrayParametros['strTipoImpuesto']            = null;
        $arrayParametros['intPrioridad']               = null;
        $arrayParametros['boolDocumentoFinancieroImp'] = null;
        $arrayParametros['intDetalleDocId']            = null;
        $arrayParametros['booleanValorOriginal']       = null;
        $arrayParametros['intPaisId']                  = $intIdPais;
        $arrayParametros['strEstado']                  = "Activo";
        
        $arrayRespuesta = $em->getRepository('schemaBundle:AdmiImpuesto')->getImpuestosByCriterios($arrayParametros);
        $arrayListadoImpuestos = $arrayRespuesta['registros'];
        if(empty($arrayListadoImpuestos))
        {
            $arr_imp[] = array("id" => "", "descripcion" => "");
        }
        else
        {
            $arr_imp = array();
            foreach($arrayListadoImpuestos as $impuesto)
            {
                $tecn['id2'] = $impuesto->getId();
                $tecn['descripcion2'] = $impuesto->getDescripcionImpuesto();
                $arr_imp[] = $tecn;
            }
        }
        $response = new Response(json_encode(array('impuesto' => $arr_imp)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function listarDescuentosExistentesAction()
    {
        $request = $this->getRequest();
        $productoid = $request->get('productoid');

        //$idEmpresa = $session->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        //$idProducto = $request->request->get("idProducto"); 	
        $estado = "Activo";
        $listado_descuentos = $em->getRepository('schemaBundle:InfoProductoNivel')->findPorEstado($productoid, $estado);

        //print_r($listado_descuentos);
        //$clientes[] = array("idCl"=>"$telefono","loginClienteSucursal"=>"","descripcion"=>"");

        if(!$listado_descuentos)
        {
            //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
            $descuento_l[] = array("descuentos" => "");
        }
        else
        {
            $descuento_l = array();
            foreach($listado_descuentos as $desc)
            {
                //print_r($desc);
                //$tecn['id'] = $emp->getId();
                $empresa_rol = $em->getRepository('schemaBundle:InfoEmpresaRol')->find($desc->getEmpresaRolId());
                $info_rol = $em->getRepository('schemaBundle:AdmiRol')->find($empresa_rol->getRolId());
                $tecn['descuentos'] = $info_rol->getDescripcionRol();
                $tecn['valor'] = $desc->getPorcentajeDescuento();
                $descuento_l[] = $tecn;
            }
        }
        $response = new Response(json_encode(array('listadoNiveles' => $descuento_l)));
        $response->headers->set('Content-type', 'text/json');
        //echo $response;
        //die();
        return $response;
    }

    public function listarImpuestosExistentesAction()
    {
        $request = $this->getRequest();
        $productoid = $request->get('productoid');

        //$idEmpresa = $session->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        //$idProducto = $request->request->get("idProducto"); 	
        $estado = "Activo";
        $listado_impuestos = $em->getRepository('schemaBundle:InfoProductoImpuesto')->findPorEstado($productoid, $estado);

        //print_r($listado_descuentos);
        //$clientes[] = array("idCl"=>"$telefono","loginClienteSucursal"=>"","descripcion"=>"");

        if(!$listado_impuestos)
        {
            //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
            $impuestos_l[] = array("descuentos" => "");
        }
        else
        {
            $impuestos_l = array();
            foreach($listado_impuestos as $imp)
            {
                $tecn['impuesto'] = $imp->getImpuestoId()->getDescripcionImpuesto();
                $impuestos_l[] = $tecn;
            }
        }
        $response = new Response(json_encode(array('listadoImpuestos' => $impuestos_l)));
        $response->headers->set('Content-type', 'text/json');
        //echo $response;
        //die();
        return $response;
    }

    public function ajaxGetCaracteristicasProductoTecnicoAction()
    {
        $request = $this->getRequest();
        $em = $this->get('doctrine')->getManager('telconet');
        $idProducto = $request->get("idProducto");
        $cantidad = $request->get("cantidad");
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $caracteristicasTecnicas = $serviceInfoServicio->obtenerProductoCaracteristicasTecnicas($idProducto, 'idCaracteristica', 'caracteristica');
        $caracteristicas = array();
        if(empty($caracteristicasTecnicas))
        {
            $caracteristicas[] = array("idCaracteristica" => "", "caracteristica" => "");
        }
        else
        {
            for($i = 0; $i < $cantidad; $i++)
            {
                $caracteristicas = array_merge($caracteristicas, $caracteristicasTecnicas);
            }
        }
        $response = new Response(json_encode($caracteristicas));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * 
     * Documentación para el método 'ajaxGetEstadosInicialesAction'.
     * 
     * Método encargado de obtener los estados Iniciales que podría tener un producto.
     * Registrados en la tabla ADMI_PARAMETRO_DET con el parametro padre 'ESTADO_INICIAL_PRODUCTO'
     * 
     * @return Response.
     * 
     * @author Duval Medina <dmedina@telconet.ec>
     * @version 1.0 2016-04-16 15:00
     * 
     */
    public function ajaxGetEstadosInicialesAction()
    {
        $objManager     = $this->get('doctrine')->getManager('telconet');
        $strParamPadre  = 'ESTADO_INICIAL_PRODUCTO';
        $strModulo      = 'COMERCIAL';
        $listaEstados   = $objManager->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get($strParamPadre, $strModulo, '', '', '', '', '', '', '', '');
        $arregloEstados = array();

        foreach($listaEstados as $entityEstado)
        {
            $arregloEstados[] = array('estado' => $entityEstado['valor1']);
        }

        $objResponse = new Response(json_encode(array('estados' => $arregloEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * 
     * Documentación para el método 'ajaxGetNombresTecnicosAction'.
     * 
     * Método encargado de obtener los nombres técnicos para un producto
     * Registrados en la tabla ADMI_PARAMETRO_DET con el parametro padre 'NOMBRE_TECNICO_PRODUCTO'
     * 
     * @param integer $idEmpresa 
     * 
     * @return Response.
     * 
     * @author Duval Medina <dmedina@telconet.ec>
     * @version 1.0 2016-04-16 17:00 
     * 
     * @author Modificado: Duval Medina <dmedina@telconet.ec>
     * @version 1.1 2016-05-27 10:49 Consumir la funcion genérica para enviar la opcion new 
     */
    public function ajaxGetNombresTecnicosAction()
    {
        return $this->getNombresTecnicos("new");
    }

    /**
     * 
     * Documentación para el método 'ajaxGetNombresTecnicosAction'.
     * 
     * Método encargado de Consumir la funcion genérica para enviar la opcion index
     * para obtener los nombres técnicos para un producto
     * 
     * @return Response.
     * 
     * @author Duval Medina <dmedina@telconet.ec>
     * @version 1.0 2016-05-28 10:45 
     */
    public function ajaxGetNombresTecnicosIndexAction()
    {
        return $this->getNombresTecnicos("index");
    }
    
    /**
     * 
     * Documentación para el método 'getNombresTecnicos'.
     * 
     * Método encargado de obtener los nombres técnicos para un producto
     * Registrados en la tabla ADMI_PARAMETRO_DET con el parametro padre 'NOMBRE_TECNICO_PRODUCTO'
     * 
     * @param integer $idEmpresa 
     * @param integer $opcion 
     * 
     * @return Response.
     * 
     * @author Duval Medina <dmedina@telconet.ec>
     * @version 1.0 2016-05-27 10:44 Hacerla genérica para poder invocarla incluyendo el parámetro $opcion 
     * 
     */
    public function getNombresTecnicos($opcion)
    {
        $intEmpresaId   = $this->get('request')->getSession()->get('idEmpresa');
        $objManager     = $this->get('doctrine')->getManager('telconet');
        $strParamPadre  = 'NOMBRE_TECNICO_PRODUCTO';
        $strModulo      = 'COMERCIAL';
        $listaNombres   = $objManager->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get($strParamPadre, $strModulo, '', '', '', '', '', '', '', $intEmpresaId);
        $arregloNombres = array();

        if($opcion=="index")
        {
            $arregloNombres[] = array('nombre' => "Todos");
        }
        foreach($listaNombres as $entityNombre)
        {
            $arregloNombres[] = array('nombre' => $entityNombre['valor1']);
        }

        $objResponse = new Response(json_encode(array('nombres' => $arregloNombres)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
     /**
     * 
     * Documentación para el método 'ajaxGetGruposAction'.
     * 
     * Método encargado de obtener los Grupos definidos para un producto
     * Registrados en la tabla ADMI_PARAMETRO_DET con el parametro padre 'GRUPO_PRODUCTO'    
     * 
     * @return Response.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 2016-10-03 
     * 
     */
    public function ajaxGetGruposAction()
    {
        return $this->getNombresGrupos("new");
    }

    /**
     * 
     * Documentación para el método 'ajaxGetGruposIndexAction'.
     * 
     * Método encargado de Consumir la funcion genérica para enviar la opcion index
     * para obtener los nombres de Grupos para un producto
     * 
     * @return Response.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 2016-10-03
     */
    public function ajaxGetGruposIndexAction()
    {
        return $this->getNombresGrupos("index");
    }
    
    /**
     * 
     * Documentación para el método 'getNombresGrupos'.
     * 
     * Método encargado de obtener los nombres de grupos para un producto
     * Registrados en la tabla ADMI_PARAMETRO_DET con el parametro padre 'GRUPO_PRODUCTO'
     * 
     * @param string $strEmpresaId 
     * @param string $opcion 
     * 
     * @return Response.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 2016-10-03
     * 
     */
    public function getNombresGrupos($opcion)
    {
        $strEmpresaId   = $this->get('request')->getSession()->get('idEmpresa');
        $objGeneral     = $this->get('doctrine')->getManager('telconet_general');
        $strParamPadre  = 'GRUPO_PRODUCTO';
        $strModulo      = 'COMERCIAL';
        $arrayGrupos    = $objGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get($strParamPadre, $strModulo, '', '', '', '', '', '', '', $strEmpresaId);
        $arregloNombres = array();

        if($opcion=="index")
        {
            $arregloNombres[] = array('nombre' => "Todos");
        }
        foreach($arrayGrupos as $entityNombre)
        {
            $arregloNombres[] = array('nombre' => $entityNombre['valor1']);
        }

        $objResponse = new Response(json_encode(array('nombres' => $arregloNombres)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    /**
     * 
     * Documentación para el método 'ajaxGetClasificacionesAction'.
     * 
     * Método encargado de obtener las clasificaciones definidas para un producto
     * Registrados en la tabla ADMI_PARAMETRO_DET con el parametro padre 'CLASIFICACION_PRODUCTO'
     *      
     * @return Response.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 2016-10-03 
     * 
     */
    public function ajaxGetClasificacionesAction()
    {
        return $this->getNombresClasificacion("new");
    }

    /**
     * 
     * Documentación para el método 'ajaxGetClasificacionesIndexAction'.
     * 
     * Método encargado de Consumir la funcion genérica para enviar la opcion index
     * para obtener los nombres de las clasificaciones para un producto
     * 
     * @return Response.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 2016-10-03
     */
    public function ajaxGetClasificacionesIndexAction()
    {
        return $this->getNombresClasificacion("index");
    }
    
    /**
     * 
     * Documentación para el método 'getNombresClasificacion'.
     * 
     * Método encargado de obtener los nombres de las clasificaciones para un producto
     * Registrados en la tabla ADMI_PARAMETRO_DET con el parametro padre 'CLASIFICACION_PRODUCTO'
     * 
     * @param string $strEmpresaId 
     * @param string $opcion 
     * 
     * @return Response.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 2016-10-03
     * 
     */
    public function getNombresClasificacion($opcion)
    {
        $strEmpresaId         = $this->get('request')->getSession()->get('idEmpresa');
        $objGeneral           = $this->get('doctrine')->getManager('telconet_general');
        $strParamPadre        = 'CLASIFICACION_PRODUCTO';
        $strModulo            = 'COMERCIAL';
        $arrayClasificacion   = $objGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->get($strParamPadre, $strModulo, '', '', '', '', '', '', '', $strEmpresaId);
        $arregloNombres = array();

        if($opcion=="index")
        {
            $arregloNombres[] = array('nombre' => "Todos");
        }
        foreach($arrayClasificacion as $entityNombre)
        {
            $arregloNombres[] = array('nombre' => $entityNombre['valor1']);
        }

        $objResponse = new Response(json_encode(array('nombres' => $arregloNombres)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**    
    * @Secure(roles="ROLE_41-5237")
    * 
    * Documentación para el método 'gridComisionPlantillaAction'.
    *
    * Funcion devuelve Plantilla de Comisionistas
    * @param  integer $intIdProducto    
    * 
    * @return JsonResponse
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 20-03-2017  
    */
    public function gridComisionPlantillaAction() 
    {
        $arrayParametros  = array();
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();  
        $serviceUtil      = $this->get('schema.Util');
        $strIpClient      = $objRequest->getClientIp();
        $strUsrSesion     = $objSession->get('user'); 
        $strCodEmpresa    = $objSession->get('idEmpresa');        
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $intIdProducto    = $objRequest->get("intIdProducto");        
                          
        $arrayParametros['intIdProducto']    = $intIdProducto;
        $arrayParametros['strCodEmpresa']    = $strCodEmpresa;
        
        $arrayJsonComisionPlantilla = array();
        $objJsonResponse            = new JsonResponse($arrayJsonComisionPlantilla);
             
        try
        {        
            $arrayJsonComisionPlantilla  = $emComercial ->getRepository('schemaBundle:AdmiProducto')
                                                        ->getComisionPlantilla($arrayParametros);
            $objJsonResponse->setData($arrayJsonComisionPlantilla);   
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiProductoController.gridComisionPlantillaAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );                
        }                                  
        return $objJsonResponse;                
    }
    
    /**    
     * @Secure(roles="ROLE_41-5237")
     * 
     * Documentación para el método 'guardaComisionPlantillaAjaxAction'.
     * 
     * Funcion para guardar Plantilla de Comisionistas, individual o masiva
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-03-2017 
     *      
     * @param  integer $intIdProducto  
     * @param  string  $strIdsProductos  
     * @param  string  $strComisionPlantilla  
     * 
     * @return $objJsonResponse
     */
    public function guardaComisionPlantillaAjaxAction()
    {     
        $objJsonResponse      = new JsonResponse();
        //Obtiene parametros enviados desde el ajax
        $objRequest           = $this->get('request');
        $intIdProducto        = $objRequest->get('intIdProducto');        
        $strIdsProductos      = $objRequest->get('strIdsProductos');
        $strComisionPlantilla = $objRequest->get('strComisionPlantilla');
        $strIpClient          = $objRequest->getClientIp();
        $objSession           = $objRequest->getSession();
        $strUsrCreacion       = $objSession->get('user');
        $strCodEmpresa        = $objSession->get('idEmpresa');  
        $serviceUtil          = $this->get('schema.Util');  
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $arrayComision        = array();        
        $arrayIdsProductos    = array();
        $arrayParametros      = array();
        
        if(isset($strComisionPlantilla) && !empty($strComisionPlantilla))
        {
            $arrayExplodeComision = explode(',', $strComisionPlantilla);
            for($intId = 0; $intId < count($arrayExplodeComision); $intId+=5)
            {
                $arrayComision[] = array('intIdComisionDet'  => $arrayExplodeComision[$intId],
                                         'intParametroDetId' => $arrayExplodeComision[$intId + 1],
                                         'fltComisionVenta'  => $arrayExplodeComision[$intId + 4]);
            }
        }
        if(isset($strIdsProductos) && !empty($strIdsProductos))
        {
            $arrayExplodeIdsProductos = explode('|', $strIdsProductos);
            for($intId = 0; $intId < count($arrayExplodeIdsProductos); $intId++)
            {
                $arrayIdsProductos[] = array('intIdProducto' => $arrayExplodeIdsProductos[$intId]);
            }
        }
        
        $emComercial->getConnection()->beginTransaction();
        try
        {   // Si esta guardando plantilla Individual
            if(isset($intIdProducto) && !empty($intIdProducto))
            {
                $objAdmiProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
                if(!is_object($objAdmiProducto))
                {
                    throw new \Exception("No se encontró el Producto definido para el ingreso de la Plantilla de Comisiones");
                }
                $arrayParametros = array('intIdProducto'  => $intIdProducto,
                                         'arrayComision'  => $arrayComision,
                                         'strTipo'        => 'Individual',
                                         'strUsrCreacion' => $strUsrCreacion,
                                         'strIpClient'    => $strIpClient
                                        );
                $this->guardaComisionPlantilla( $arrayParametros );
            }
            else
            {
                // Si esta guardando plantilla Masiva
                for($intId = 0; $intId < count($arrayIdsProductos); $intId++)
                {   
                    $arrayParametros = array('intIdProducto'  => $arrayIdsProductos[$intId]["intIdProducto"],
                                             'arrayComision'  => $arrayComision,
                                             'strTipo'        => 'Masivo',
                                             'strUsrCreacion' => $strUsrCreacion,
                                             'strIpClient'    => $strIpClient
                                            );
                    $this->guardaComisionPlantilla( $arrayParametros );
                }
            }
           
            $emComercial->flush(); 
            $emComercial->getConnection()->commit();           
            $objJsonResponse->setData('OK');  
        } 
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiProductoController.guardaComisionPlantillaAjaxAction',
                                      $e->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpClient
                                     );               
           $objJsonResponse->setData('Error');  
           if ($emComercial->getConnection()->isTransactionActive())
           {
               $emComercial->getConnection()->rollback();
           }                            
           $emComercial->getConnection()->close();            
        }                                                  
        return $objJsonResponse;
    }
    
    /**
     * @Secure(roles="ROLE_41-5237")
     * 
     * Documentación para el método 'guardaComisionPlantilla'.
     * 
     * Funcion para guardar Plantilla de Comisionistas sea individual o masiva
     * Se valida que solo se ingrese Plantilla con porcentaje de comision mayo a cero
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-04-2017 
     *      
     * @param  array $arrayParametros [
     *                                 'intIdProducto'    : Id del Producto
     *                                 'arrayComision'    : array de Plantilla de comisionistas
     *                                 'strTipo'          : Tipo Individual o Masivo
     *                                 'strUsrCreacion'   : Usuario de Creacion
     *                                 'strIpClient'      : Ip de creacion
     *                                ]
     * 
     * 
     */
    public function guardaComisionPlantilla($arrayParametros)
    {
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emComercial        = $this->getDoctrine()->getManager('telconet');        
        $intIdProducto      = $arrayParametros['intIdProducto'];
        $arrayComision      = $arrayParametros['arrayComision'];
        $boolInsertaCab     = true;
        $boolInsertaDet     = true;
        $boolInsertaHist    = false;
        $fltComisionVtaProd = 0;
        
        $objAdmiProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
        if(!is_object($objAdmiProducto))
        {
            throw new \Exception("No se encontró el Producto definido para el ingreso de la Plantilla de Comisiones");
        }
        //Obtengo si que existe cabecera de plantilla para el producto en estado Activo
        $objComisionCab = $emComercial->getRepository('schemaBundle:AdmiComisionCab')
                                      ->findOneBy(array("productoId" => $intIdProducto,
                                                        "estado"     => "Activo"));
        
        if(isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo']))
        {
            if($arrayParametros['strTipo'] == 'Individual')
            {
                if(is_object($objComisionCab))
                {
                    $objAdmiComisionCab = $objComisionCab;
                    $boolInsertaCab     = false;
                }
            }
            else
            {
                if($arrayParametros['strTipo'] == 'Masivo')
                {
                    if(is_object($objComisionCab))
                    {
                        $objComisionCab->setEstado('Inactivo');
                        $emComercial->persist($objComisionCab);
                        $objComisionDet = $emComercial->getRepository('schemaBundle:AdmiComisionDet')
                                                      ->findBy(array("comisionId" => $objComisionCab->getId(),
                                                                     "estado"     => "Activo"));
                        foreach($objComisionDet as $objComisionDet)
                        {                            
                            $objComisionDet->setEstado('Inactivo');
                            $emComercial->persist($objComisionDet);
                            //Inserto Historial de modificacion de Plantilla Masiva
                            $objAdmiComisionHistorial = new AdmiComisionHistorial();
                            $objAdmiComisionHistorial->setComisionDetId($objComisionDet);
                            $objAdmiComisionHistorial->setUsrCreacion($arrayParametros['strUsrCreacion']);
                            $objAdmiComisionHistorial->setFeCreacion(new \DateTime('now'));
                            $objAdmiComisionHistorial->setIpCreacion($arrayParametros['strIpClient']);
                            $objAdmiComisionHistorial->setEstado('Activo');
                            $objAdmiComisionHistorial->setObservacion('Se Inactiva Plantilla de Comisionistas por opcion Masiva');
                            $emComercial->persist($objAdmiComisionHistorial);
                        }                               
                    }
                }
            }
        }

        if( $boolInsertaCab )
        {
            //Inserto Cabecera de Plantilla de Comisiones                
            $objAdmiComisionCab = new AdmiComisionCab();
            $objAdmiComisionCab->setProductoId($objAdmiProducto);
            $objAdmiComisionCab->setFeCreacion(new \DateTime('now'));
            $objAdmiComisionCab->setUsrCreacion($arrayParametros['strUsrCreacion']);
            $objAdmiComisionCab->setIpCreacion($arrayParametros['strIpClient']);
            $objAdmiComisionCab->setEstado('Activo');
            $emComercial->persist($objAdmiComisionCab);
        }
        for($intId = 0; $intId < count($arrayComision); $intId++)
        {
            $boolInsertaDet  = true;
            $boolInsertaHist = false;
            // Se suma al total el valor de comision venta para actualizar al producto
            if(isset($arrayComision[$intId]["fltComisionVenta"]) && !empty($arrayComision[$intId]["fltComisionVenta"]))
            {
                $fltComisionVtaProd = $fltComisionVtaProd + $arrayComision[$intId]["fltComisionVenta"];                
            }
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->find($arrayComision[$intId]["intParametroDetId"]);
            if(!is_object($objAdmiParametroDet))
            {
                throw new \Exception("No se encontró el GRUPO_ROLES_PERSONAL a nivel de Parametros");
            }

            $objComisionDet = $emComercial->getRepository('schemaBundle:AdmiComisionDet')->find($arrayComision[$intId]["intIdComisionDet"]);

            if(isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo']))
            {
                if($arrayParametros['strTipo'] == 'Individual')
                {
                    if(is_object($objComisionDet))
                    {
                        $boolInsertaDet           = false;
                        $fltComisionVentaAnterior = $objComisionDet->getComisionVenta();

                        if(isset($arrayComision[$intId]["fltComisionVenta"]) && !empty($arrayComision[$intId]["fltComisionVenta"]))
                        {
                            if($fltComisionVentaAnterior != $arrayComision[$intId]["fltComisionVenta"])
                            {
                                $boolInsertaHist = true;
                                $objComisionDet->setComisionVenta($arrayComision[$intId]["fltComisionVenta"]);
                                $emComercial->persist($objComisionDet);                                
                            }
                        }
                        else
                        {
                            $boolInsertaHist = true;
                            $objComisionDet->setComisionVenta(0);
                            $objComisionDet->setEstado('Inactivo');
                            $emComercial->persist($objComisionDet);
                        }
                        if($boolInsertaHist)
                        {
                            //Inserto Historial de modificacion de Plantilla Individual
                            $objAdmiComisionHistorial = new AdmiComisionHistorial();
                            $objAdmiComisionHistorial->setComisionDetId($objComisionDet);
                            $objAdmiComisionHistorial->setUsrCreacion($arrayParametros['strUsrCreacion']);
                            $objAdmiComisionHistorial->setFeCreacion(new \DateTime('now'));
                            $objAdmiComisionHistorial->setIpCreacion($arrayParametros['strIpClient']);
                            $objAdmiComisionHistorial->setEstado('Activo');
                            $objAdmiComisionHistorial->setObservacion('Se actualizo Comision en Venta : <br>' .
                                'Valor Anterior:' .
                                '&nbsp;&nbsp;&nbsp;&nbsp;' . $fltComisionVentaAnterior . '<br>' .
                                'Valor Nuevo:' .
                                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $arrayComision[$intId]["fltComisionVenta"] .
                                '<br>');
                            $emComercial->persist($objAdmiComisionHistorial);
                        }
                    }
                }
            }
            if(isset($arrayComision[$intId]["fltComisionVenta"]) && !empty($arrayComision[$intId]["fltComisionVenta"]) 
                && $arrayComision[$intId]["fltComisionVenta"] > 0 && $boolInsertaDet)
            {
                //Inserto Detalle de Plantilla de Comisiones
                $objAdmiComisionDet = new AdmiComisionDet();
                $objAdmiComisionDet->setComisionId($objAdmiComisionCab);
                $objAdmiComisionDet->setParametroDetId($objAdmiParametroDet->getId());
                $objAdmiComisionDet->setComisionVenta($arrayComision[$intId]["fltComisionVenta"]);
                $objAdmiComisionDet->setFeCreacion(new \DateTime('now'));
                $objAdmiComisionDet->setUsrCreacion($arrayParametros['strUsrCreacion']);
                $objAdmiComisionDet->setIpCreacion($arrayParametros['strIpClient']);
                $objAdmiComisionDet->setEstado('Activo');
                $emComercial->persist($objAdmiComisionDet);
            }
        }
        //Actualizo valor de comision en venta en el producto        
        $objAdmiProducto->setComisionVenta($fltComisionVtaProd);
        $emComercial->persist($objAdmiProducto);
        
        //Actualizo estado del producto a Activo
        if($objAdmiProducto->getEstado() == 'Pendiente')
        {
            $objAdmiProducto->setEstado('Activo');
            $emComercial->persist($objAdmiProducto);
        }
        
    }

    /**    
     * @Secure(roles="ROLE_41-5257")
     * 
     * Documentación para el método 'ingresoMasivoComisionistasAction'.
     * 
     * Funcion que obtiene los productos a los cuales se les realizara el ingreso masivo de plantilla de comisionistas
     * Se obtiene por parametro el valor maximo de comision en venta permitido para el producto el cual debe validarse en el ingreso de la plantilla
     * de comisionistas
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-04-2017 
     *           
     * @return Renders a view.
     */
    public function ingresoMasivoComisionistasAction()
    {
        $objRequest          = $this->get('request');        
        $objSession          = $objRequest->getSession();
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');        
        $strIdEmpresa        = $objSession->get('idEmpresa');        
        $objGeneral          = $this->get('doctrine')->getManager('telconet_general');
        $strParamPadre       = 'VALOR_MAXIMO_COMISION_VENTA';
        $strModulo           = 'COMERCIAL';
        $ftlValorMaxComision = 0;
        $arrayParametroDet   = array();
        $arrayParametroDet   = $objGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                          ->getOne($strParamPadre, $strModulo, '', '', '', '', '', '', '', $strIdEmpresa);                            
        if(isset($arrayParametroDet["valor1"]) && !empty($arrayParametroDet["valor1"]))
        {
            $ftlValorMaxComision = $arrayParametroDet["valor1"];
        }
        $emComercial     = $this->getDoctrine()->getManager();
        $objAdmiProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')->findAll();
        
        if(true === $this->get('security.context')->isGranted('ROLE_41-5257'))
        {
            $rolesPermitidos[] = 'ROLE_41-5257';
        }
  
        $arrayParametros                        = array( 'entities' => $objAdmiProducto );        
        $arrayParametros['strPrefijoEmpresa']   = $strPrefijoEmpresa;
        $arrayParametros['rolesPermitidos']     = $rolesPermitidos;
        $arrayParametros['ftlValorMaxComision'] = $ftlValorMaxComision;
        return $this->render('catalogoBundle:AdmiProducto:ingresoMasivoComisionistas.html.twig', $arrayParametros);
    }
    
    /**
     * 
     * Documentación para el método 'getProductosAjaxAction'.
     * 
     * Método encargado de obtener Productos definidos en ADMI_PRODUCTO por empresa en sesion para productos marcados que requiere comisionar (SI)
     * Y solo para Productos en estado Activo, Pendiente, Inactivo.
     * 
     * @return Response.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 12-04-2017 
     * 
     */
    public function getProductosAjaxAction()
    {
        $strEmpresaId   = $this->get('request')->getSession()->get('idEmpresa');
        $emComercial    = $this->get('doctrine')->getManager('telconet');
        
        $objAdmiProducto = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                       ->findBy(array('empresaCod'         => $strEmpresaId,
                                                      'estado'             => array('Pendiente','Activo','Inactivo'),
                                                      'requiereComisionar' => 'SI'));
        $arregloNombres   = array();        
        
        foreach($objAdmiProducto as $objAdmiProducto)
        {
            $arregloNombres[] = array('id'     => $objAdmiProducto->getId(),
                                      'nombre' => $objAdmiProducto->getDescripcionProducto());
        }

        $objResponse = new Response(json_encode(array('nombres' => $arregloNombres)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**    
    * @Secure(roles="ROLE_41-5257")
    * 
    * Documentación para el método 'gridProductosComisionanAction'.
    *
    * Funcion devuelve Listado de productos que comisionan segun criterios    
    * 
    * @param  integer $intIdProducto  
    * @param  string  $strGrupo       
    * @param  string  $strNombreTecnico       
    * @param  string  $strEstado           
    * 
    * @return JsonResponse
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 12-04-2017  
    */
    public function gridProductosComisionanAction() 
    {
        $emComercial      = $this->getDoctrine()->getManager('telconet');        
        $arrayParametros  = array();
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();  
        $serviceUtil      = $this->get('schema.Util');
        $strIpClient      = $objRequest->getClientIp();
        $strUsrSesion     = $objSession->get('user'); 
        $strCodEmpresa    = $objSession->get('idEmpresa');                
        $intIdProducto    = $objRequest->get("intParamIdProducto");   
        $strGrupo         = $objRequest->get("strParamGrupo");
        $strNombreTecnico = $objRequest->get("strParamNombreTecnico");
        $strEstado        = $objRequest->get("strParamEstado");
        $intLimit         = $objRequest->get("limit");
        $intPage          = $objRequest->get("page");
        $intStart         = $objRequest->get("start");
        
        $arrayParametros  = array('intIdProducto'    => $intIdProducto,
                                  'strGrupo'         => $strGrupo,
                                  'strNombreTecnico' => $strNombreTecnico,
                                  'strEstado'        => $strEstado,
                                  'intLimit'         => $intLimit,
                                  'intPage'          => $intPage,
                                  'intStart'         => $intStart,
                                  'strCodEmpresa'    => $strCodEmpresa
                                 );
        $arrayJsonComisionPlantilla = array();
        $objJsonResponse            = new JsonResponse($arrayJsonComisionPlantilla);
             
        try
        {        
            $arrayJsonComisionPlantilla  = $emComercial ->getRepository('schemaBundle:AdmiProducto')
                                                        ->getProductosComisionan($arrayParametros);
            $objJsonResponse->setData($arrayJsonComisionPlantilla);   
        }
        catch (\Exception $e) 
        {                
            $serviceUtil->insertError('Telcos+', 
                                      'AdmiProductoController.gridProductosComisionanAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );                
        }                                  
        return $objJsonResponse;                
    }           

   /**    
    * @Secure(roles="ROLE_41-5297")
    * 
    * Documentación para el método 'gridLogsPlantillaComisionAction'.
    *
    * Funcion devuelve Log o Historial de Plantillas de Comisionistas por producto
    * 
    * @param  integer $intIdProducto          
    * 
    * @return JsonResponse
    *
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 24-04-2017  
    */
    public function gridLogsPlantillaComisionAction()
    {      
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $arrayParametros  = array();
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();
        $serviceUtil      = $this->get('schema.Util');
        $strIpClient      = $objRequest->getClientIp();
        $strUsrSesion     = $objSession->get('user');
        $strCodEmpresa    = $objSession->get('idEmpresa');
        $intIdProducto    = $objRequest->get("intIdProducto");        
        
        $arrayParametros = array('intIdProducto' => $intIdProducto, 
                                 'strCodEmpresa' => $strCodEmpresa
        );
        $arrayJsonLogsPlantillaComision = array();
        $objJsonResponse = new JsonResponse($arrayJsonLogsPlantillaComision);

        try
        {
            $arrayJsonLogsPlantillaComision = $emComercial->getRepository('schemaBundle:AdmiComisionHistorial')
                                                          ->getLogsPlantillaComision($arrayParametros);
            $objJsonResponse->setData($arrayJsonLogsPlantillaComision);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError('Telcos+',
                                      'AdmiProductoController.gridLogsPlantillaComisionAction',
                                      $e->getMessage(),
                                      $strUsrSesion,
                                      $strIpClient);
        }
        return $objJsonResponse;
    }
    
    /**
     * Documentación para el método 'gruposAction'.
     * Método encargado de obtener los grupos de los productos en Base de datos
     * @return Response.
     *
     * @author Creado: Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.0 2017-06-09  Agregar Filtro Grupos
     */
    public function gruposAction()
    {
        $strCodEmpresa  = $this->get('request')->getSession()->get('idEmpresa');
        $emComercial    = $this->get('doctrine')->getManager('telconet');
        $arrayEmpresa[] = array('strCodEmpresa' => $strCodEmpresa);
        $arrayGrupos    = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                    ->findGruposPorEmpresa($arrayEmpresa);

        foreach ($arrayGrupos as $objGrupo)
        {
            $arrayGrupo[] = array('idGrupo' => $objGrupo['grupo'], 'descripcion' => $objGrupo['grupo']);
        }
        
        return new JsonResponse(array('strGrupo' => $arrayGrupo));
    }

    /**
     * Documentación para el método 'listadoGruposByParamsAction'.
     * Método encargado de obtener los grupos de los productos de acuerdo a los parámetros enviados
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 04-09-2017  
     */
    public function listadoGruposByParamsAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strNombre          = $objRequest->get('query');
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $arrayParametros    = array('strNombre'     => strtoupper($strNombre),
                                    'strCodEmpresa' => $strCodEmpresa);
        
        $arrayGrupos        = $emComercial->getRepository('schemaBundle:AdmiProducto')->getGruposByParams($arrayParametros);
        foreach ($arrayGrupos as $objGrupo)
        {
            $arrayGrupo[]   = array('idGrupo' => $objGrupo['grupo'], 'descripcionGrupo' => $objGrupo['grupo']);
        }
        return new JsonResponse(array('grupos' => $arrayGrupo));
    }
    
    /**
     * Documentación para el método 'subgruposByParamsAction'.
     * Método encargado de obtener los subgrupos de los productos de acuerdo a los parámetros enviados
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-09-2017  
     */
    public function listadoSubgruposByParamsAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strNombre          = $objRequest->get('query');
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $arrayParametros    = array('strNombre'     => strtoupper($strNombre),
                                    'strCodEmpresa' => $strCodEmpresa);
        
        $arraySubgrupos     = $emComercial->getRepository('schemaBundle:AdmiProducto')->getSubgruposByParams($arrayParametros);
        foreach ($arraySubgrupos as $objSubgrupo)
        {
            $arraySubgrupo[] = array('idSubgrupo' => $objSubgrupo['subgrupo'], 'descripcionSubgrupo' => $objSubgrupo['subgrupo']);
        }
        return new JsonResponse(array('subgrupos' => $arraySubgrupo));
    }
    
    
    /**
     * 
     * Método encargado de obtener las características necesarias para obtener el precio de un producto
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-11-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 18-06-2018 Se obtiene función precio del producto Ip Small Business
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 18-06-2019 Se obtiene función precio del producto Ip Small Business Centros Comerciales.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 18-07-2019 Se obtiene función precio del producto Ip Small Business Razón Social.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.4 23-04-2020 Se agrega el idProducto a las consultas de los productos con nombre Tecnico INTERNET SMALL BUSINESS.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 05-05-2020 Se consulta el producto Small Business directamente por el id de producto debido a la reestructuración 
     *                          de servicios Small Business
     *
     * @return JsonResponse
     */
    public function getInformacionCaractProdFuncionPrecioAction()
    {
        $objRequest                 = $this->getRequest();
        $objResponse                = new JsonResponse();        
        $intIdServicio              = $objRequest->get('idServicio') ? $objRequest->get('idServicio') : 0;        
        $emComercial                = $this->getDoctrine()->getManager('telconet');
        $intProductoId              = $objRequest->get('productoId');

        $strStatusGetInfo               = "";
        $arrayInformacion               = array();
        $arrayCaracteristicas           = array();
        $arrayCaracteristicasReferencia = array();
        
        $objProducto                    = $emComercial->getRepository("schemaBundle:AdmiProducto")->find($intProductoId);
        if(is_object($objProducto))
        {
            $intIdProductoIsb                       = $objProducto->getId();
            $arrayInformacion['idProducto']         = $intIdProductoIsb;
            $arrayInformacion['descripcion']        = $objProducto->getDescripcionProducto();
            $arrayInformacion['nombreTecnico']      = $objProducto->getNombreTecnico();
            $arrayInformacion['funcionPrecio']      = $objProducto->getFuncionPrecio();
            $arrayInformacion['instalacion']        = $objProducto->getInstalacion();
            if($intIdServicio > 0)
            {
                $objServicioActual              = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                if(is_object($objServicioActual))
                {
                    $arrayInformacion['precioVenta']    = $objServicioActual->getPrecioVenta();
                }
            }
            
            $arrayProdCaracteristica                = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                  ->findByProductoIdyEstado($intIdProductoIsb, "Activo");
            foreach($arrayProdCaracteristica as $objProdCaracteristica)
            {
                $objCaracteristica  = $objProdCaracteristica->getCaracteristicaId();
                if(is_object($objCaracteristica))
                {
                    $strCaracteristica                                  = $objCaracteristica->getDescripcionCaracteristica();
                    $arrayCaracteristicas["[".$strCaracteristica."]"]   = '';
                    $arrayCaracteristicasReferencia[]                   = array('idCaracteristica' => $objProdCaracteristica->getId(),
                                                                                'caracteristica'   => $strCaracteristica                );  
                }
            }
            $arrayInformacion['caracteristicas']    = $arrayCaracteristicas;
            $arrayInformacion['refCaracteristicas'] = $arrayCaracteristicasReferencia;
            $strStatusGetInfo                       = "OK";
        }
        else
        {
            $strStatusGetInfo = "ERROR";
        }
        $arrayInformacion['strStatusGetInfo']   = $strStatusGetInfo;
        $objResponse->setData($arrayInformacion);
        return $objResponse;
    }
    
     /**
     * 
     * Documentación para el método 'getNombresSubGruposAction'.
     * 
     * Método encargado de obtener los Grupos definidos para un producto
     * Registrados en la tabla ADMI_PARAMETRO_DET con el parámetro padre 'SUBGRUPO_PRODUCTO'    
     * 
     * @return Response.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 2019-06-11 
     * 
     */
    public function getNombresSubGruposAction()
    {
        $request        = $this->getRequest();
        $strEmpresaId   = $this->get('request')->getSession()->get('idEmpresa');
        $objGeneral     = $this->get('doctrine')->getManager('telconet_general');
        $strParamPadre  = 'SUBGRUPO_PRODUCTO';
        $strModulo      = 'COMERCIAL';
        $strValor2         = $request->get('strIdGrupo');
        $arraySubGrupos = $objGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get($strParamPadre, $strModulo, '', '', '', $strValor2, '', '', '', $strEmpresaId);
        $arrayNombres = array();

        if (empty($arraySubGrupos))
        {
            $arrayNombres[] = array('nombre' => 'OTROS');
        }
        else
        {
            foreach($arraySubGrupos as $entityNombre)
            {
                $arrayNombres[] = array('nombre' => $entityNombre['valor1']);
            }
        }
        $objResponse = new JsonResponse(array('nombres' => $arrayNombres));
        return $objResponse;
    }   
    
    /**
     * 
     * Documentación para el método 'getNombresTipoNegocioAction'.
     * 
     * Método encargado de obtener los Grupos definidos para un producto
     * Registrados en la tabla ADMI_PARAMETRO_DET con el parámetro padre 'LINEA_NEGOCIO'    
     * 
     * @return Response.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.0 2019-06-11 
     * 
     */
    public function getNombresTipoNegocioAction()
    {
        $strEmpresaId       = $this->get('request')->getSession()->get('idEmpresa');
        $objGeneral         = $this->get('doctrine')->getManager('telconet_general');
        $strParamPadre      = 'LINEA_NEGOCIO';
        $strModulo          = 'COMERCIAL';
        $arrayTiposNegocio  = $objGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get($strParamPadre, $strModulo, '', '', '', '', '', '', '', $strEmpresaId);
        $arrayNegocios    = array();
        foreach($arrayTiposNegocio as $entityNegocio)
        {
            $arrayNegocios[] = array('nombre' => $entityNegocio['valor1']);
        }

        $objResponse = new JsonResponse(array('nombres' => $arrayNegocios));
        return $objResponse;
    }    

    /**
     * 
     * Documentación para el método 'guardarTerminosyCondiciones'.
     * 
     * Método que se encarga de actualizar los términos y condiciones de un producto
     * 
     * @return Response.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 2021-07-14 
     * 
     */    
    public function guardarTerminosyCondicionesAction()
    {
        $emComercial = $this->get('doctrine')->getManager('telconet');
        $emGeneral          = $this->get('doctrine')->getManager('telconet_general');
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');        
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $strUsrCreacion     = $objSession->get('user');
        $intIdProducto      = $objRequest->get('id');
        $strTermino         = $objRequest->get('terminos');
        $strResultado = json_encode(array('success' => true, 'mensaje' => 'Actualizacion realizada con Exito'));
        try 
        {
            $objProducto        = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
            $objProducto->setTerminoCondicion($strTermino);
            $emComercial->persist($objProducto);
            $emComercial->flush();
        } 
        catch (\Exception $ex) 
        {
            $strResultado = json_encode(array('success' => false, 'mensaje' => 'Se presentaron errores al intentar actualizar el producto'));
        }
        $objRespuesta->setContent($strResultado);
        return $objRespuesta;
    }
}

