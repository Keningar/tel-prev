<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiBancoTipoCuenta;
use telconet\schemaBundle\Form\AdmiBancoTipoCuentaType;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdmiBancoTipoCuenta controller.
 *
 */
class AdmiBancoTipoCuentaController extends Controller implements TokenAuthenticatedController
{
    /**
     * Lists all AdmiBancoTipoCuenta entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findAll();

        return $this->render('financieroBundle:AdmiBancoTipoCuenta:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    
    public function gridAction() {
        $request = $this->getRequest();
        $estado = '';
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $estado = $request->get("estado");
        $nombre = $request->get("nombre");
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        //$user = $this->get('security.context')->getToken()->getUser();
        $idEmpresa = $request->getSession()->get('idEmpresa');

        $em = $this->get('doctrine')->getManager('telconet_general');
        if ((!$fechaDesde[0]) && (!$fechaHasta[0]) && !$estado && !$nombre ){
            //Cuando sea inicio puedo sacar los 30 registros
            $estado = 'Activo';
            $resultado = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosTipoCuentaParaGrid($limit,$page,$start);
            $datos = $resultado['registros'];
            $total = $resultado['total'];
        } else {
            $resultado = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosTipoCuentaParaGrid($limit,$page,$start);
            $datos = $resultado['registros'];
            $total = $resultado['total'];
        }
        foreach ($datos as $datos):
            $urlVer = $this->generateUrl('admibancotipocuenta_show', array('id' => $datos->getId()));
            $urlEditar = $this->generateUrl('admibancotipocuenta_edit', array('id' => $datos->getId()));
            $urlEliminar = $this->generateUrl('admibancotipocuenta_delete', array('id' => $datos->getId()));
            $linkVer = $urlVer;
            if ($datos->getEstado() != "Inactivo")
                $linkEditar = $urlEditar;
            else
                $linkEditar = "#";
            $linkEliminar = $urlEliminar;
            $arreglo[] = array(
                'idBancoTipoCuenta' => $datos->getId(),
                'banco' => $datos->getBancoId()->getDescripcionBanco(),
                'tipoCuenta' => $datos->getTipoCuentaId()->getDescripcionCuenta(),
                'fechaCreacion' => strval(date_format($datos->getFeCreacion(), "d/m/Y G:i")),
                'usuarioCreacion' => $datos->getUsrCreacion(),
                'estado' => $datos->getEstado(),
                'linkVer' => $linkVer,
                'linkEditar' => $linkEditar,
                'linkEliminar' => $linkEliminar
            );
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'clientes' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'clientes' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }    
    
    /**
     * Finds and displays a AdmiBancoTipoCuenta entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiBancoTipoCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('financieroBundle:AdmiBancoTipoCuenta:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new AdmiBancoTipoCuenta entity.
     *
     */
    public function newAction()
    {
        $entity = new AdmiBancoTipoCuenta();
        $form   = $this->createForm(new AdmiBancoTipoCuentaType(), $entity);

        return $this->render('financieroBundle:AdmiBancoTipoCuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new AdmiBancoTipoCuenta entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new AdmiBancoTipoCuenta();
        $form = $this->createForm(new AdmiBancoTipoCuentaType(), $entity);
        $form->bind($request);
        $datos_form_extra=$request->request->get('admibancotipocuentaextratype');
        var_dump($form->getErrors());
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity->setEstado('Activo');
            $entityAdmiTipoCuenta=$em->getRepository('schemaBundle:AdmiTipoCuenta')->find($datos_form_extra['tipoCuentaId']);
            $entity->setTipoCuentaId($entityAdmiTipoCuenta);
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admibancotipocuenta_show', array('id' => $entity->getId())));
        }

        return $this->render('financieroBundle:AdmiBancoTipoCuenta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AdmiBancoTipoCuenta entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiBancoTipoCuenta entity.');
        }

        $editForm = $this->createForm(new AdmiBancoTipoCuentaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('financieroBundle:AdmiBancoTipoCuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing AdmiBancoTipoCuenta entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiBancoTipoCuenta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AdmiBancoTipoCuentaType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admibancotipocuenta_edit', array('id' => $id)));
        }

        return $this->render('financieroBundle:AdmiBancoTipoCuenta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a AdmiBancoTipoCuenta entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiBancoTipoCuenta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admibancotipocuenta'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    
    public function ajaxGetListadoTiposCuentaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $es_tarjeta = $peticion->query->get('es_tarjeta');  
        $em = $this->getDoctrine()->getManager("telconet");
        if($es_tarjeta!='')
        {
            $items = $em->getRepository('schemaBundle:AdmiTipoCuenta')->findTiposCuentaPorEsTarjetaActivos($es_tarjeta);
            if($items && count($items)>0)
            {
                $num = count($items);

                $arr_encontrados[]=array('id_cuenta' =>0, 'descripcion_cuenta' =>"Seleccion un Tipo de Cuenta");
                foreach($items as $key => $item)
                {                
                    $arr_encontrados[]=array('id_cuenta' =>$item["id"],
                                            'descripcion_cuenta' =>trim($item["descripcionCuenta"]));
                }

                if($num == 0)
                {
                    $resultado= array('total' => 1 ,
                                    'encontrados' => array('id_cuenta' => 0 , 'descripcion_cuenta' => 'Ninguno','cuenta_id' => 0 , 'cuenta_descripcion' => 'Ninguno', 'estado' => 'Ninguno'));
                    $objJson = json_encode( $resultado);
                }
                else
                {
                    $data=json_encode($arr_encontrados);
                    $objJson= '{"total":"'.$num.'","encontrados":'.$data.'}';
                }
            }
            else
            {
                $objJson= '{"total":"0","encontrados":[]}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
}
