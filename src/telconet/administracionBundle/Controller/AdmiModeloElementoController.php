<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiModeloElemento;
use telconet\schemaBundle\Entity\AdmiInterfaceModelo;
use telconet\schemaBundle\Entity\AdmiDetalleInterface;
use telconet\schemaBundle\Entity\AdmiModeloUsuarioAcceso;
use telconet\schemaBundle\Entity\AdmiModeloProtocolo;
use telconet\schemaBundle\Entity\AdmiModeloTecnologia;
use telconet\schemaBundle\Entity\AdmiDetalleModelo;
use telconet\schemaBundle\Form\AdmiModeloElementoType;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use telconet\schemaBundle\Entity\AdmiParametroDet;

class AdmiModeloElementoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_119-1")
    */
    public function indexAction()
    {
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entities = $em->getRepository('schemaBundle:AdmiModeloElemento')->findAll();

        return $this->render('administracionBundle:AdmiModeloElemento:index.html.twig', array(
            'entities' => $entities
        ));
    }
    
    public function ajaxListAllAction()
    {
        $request = $this->getRequest();
        $em = $this->get('doctrine')->getManager('telconet_infraestructura');

        $modelos = $em->getRepository('schemaBundle:AdmiModeloElemento')->findAll();
        $i=1;
        foreach ($modelos as $modelo){
            if($i % 2==0)
                    $class='k-alt';
            else
                    $class='';
            
            $urlVer = $this->generateUrl('admimodeloelemento_show', array('id' => $modelo->getId()));
            $urlEditar = $this->generateUrl('admimodeloelemento_edit', array('id' => $modelo->getId()));

            $arreglo[]= array(
                'id'=> $modelo->getId(),
                'nombreModeloElemento'=> $modelo->getNombreModeloElemento(),
                'descripcionModeloElemento'=> $modelo->getDescripcionModeloElemento(),
                'estado' => $modelo->getEstado(),
                'fechaCreacion'=> strval(date_format($modelo->getFeCreacion(),"d/m/Y G:i")),
                'usuarioCreacion'=> $modelo->getUsrCreacion(),
                'urlVer'=> $urlVer,
                'urlEditar'=> $urlEditar,
                'clase'=> $class
            );  
            $i++;
        }

        if (empty($arreglo)){
            $arreglo[]= array(
                    'id'=> "",
                    'nombreModeloElemento'=> "",
                    'descripcionModeloElemento'=> "",
                    'estado' => "",
                    'fechaCreacion'=> "",
                    'usuarioCreacion'=> "",
                    'urlVer'=> "",
                    'urlEditar'=> "",
                    'clase'=> ""
                    );
        }		
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');		
        return $response;	
    }
    
    /**
    * @Secure(roles="ROLE_119-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $modeloElemento = $em->find('schemaBundle:AdmiModeloElemento', $id)) {
            throw new NotFoundHttpException('No existe el modelo que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiModeloElemento:show.html.twig', array(
            'modeloelemento'   => $modeloElemento,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_119-2")
    */
    public function newAction()
    {
        $entity = new AdmiModeloElemento();
        $form   = $this->createForm(new AdmiModeloElementoType(), $entity);

        return $this->render('administracionBundle:AdmiModeloElemento:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    
    /**
     * @Secure(roles="ROLE_119-3")
     * 
     * Documentación para el método 'createAction'.
     *
     * Crea el modelo de un elemento asociado a su marca y tipo.
     * 
     * @return view 
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 05-11-2015 - Se modifica el usuario de creacion y modificacion
     *
     * @version 1.0 Version Inicial
     * 
     *  @author Leonardo Mero <lemero@telconet.ec>
     *  @version 1.2 /20-09-2022 Se registra el tipo de camara y resolucion 
     */
    public function createAction()
    {
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $arrayParametros       = $objRequest->request->get('telconet_schemabundle_admimodeloelementotype');
        $emInfraestructura     = $this->get('doctrine')->getManager('telconet_infraestructura');
        $objAdmiModeloElemento = new AdmiModeloElemento();
        $form                  = $this->createForm(new AdmiModeloElementoType(), $objAdmiModeloElemento);
        
        $objMarcaElemento       = $emInfraestructura->getRepository('schemaBundle:AdmiMarcaElemento')->find($arrayParametros['marcaElementoId']);
        $strNombreMarcaElemento = str_replace( ' ','', strtolower($objMarcaElemento->getNombreMarcaElemento())); 
        
        $objAdmiModeloElemento->setEstado('Activo');
        $objAdmiModeloElemento->setFeCreacion(new \DateTime('now'));
        $objAdmiModeloElemento->setUsrCreacion($objSession->get('user'));
        $objAdmiModeloElemento->setFeUltMod(new \DateTime('now'));
        $objAdmiModeloElemento->setUsrUltMod($objSession->get('user'));
        
        //requiere aprovisionamiento
        if($strNombreMarcaElemento == 'nettonet')
        {// si es net to net ->  no  requiere aprovisionamiento
            $objAdmiModeloElemento->setReqAprovisionamiento('NO');
        }
        else
        { // sino si requiere aprovisionamiento
             $objAdmiModeloElemento->setReqAprovisionamiento('SI');
        }
        
        $form->bind($objRequest);
        
        if ($form->isValid())
        {
            $emInfraestructura->getConnection()->beginTransaction();
            $emInfraestructura->persist($objAdmiModeloElemento);
            
            $json_interfaceModelo  = json_decode($arrayParametros['interfacesModelos']);
            $array_interfaceModelo = $json_interfaceModelo->interfacesModelos;
            
            foreach($array_interfaceModelo as $interface)
            {
                if($interface->tipoInterfaceId)
                {
                    $tipoInterfaceId   = $interface->tipoInterfaceId;
                    $tipoInterface     = $emInfraestructura->getRepository('schemaBundle:AdmiTipoInterface')->find($tipoInterfaceId);
                    $cantidadInterface = $interface->cantidadInterface;
                    $formatoInterface  = $interface->formatoInterface;
                    $claseInterface    = $interface->claseInterface;
                    
                    //grabar interfaceModelo
                    $interfaceModelo = new AdmiInterfaceModelo();
                    $interfaceModelo->setCantidadInterface($cantidadInterface);
                    $interfaceModelo->setModeloElementoId($objAdmiModeloElemento);
                    $interfaceModelo->setTipoInterfaceId($tipoInterface);
                    $interfaceModelo->setClaseInterface($claseInterface);
                    $interfaceModelo->setFormatoInterface($formatoInterface);
                    $interfaceModelo->setEstado("Activo");
                    $interfaceModelo->setUsrCreacion($objSession->get('user'));
                    $interfaceModelo->setFeCreacion(new \DateTime('now'));
                    $interfaceModelo->setUsrUltMod($objSession->get('user'));
                    $interfaceModelo->setFeUltMod(new \DateTime('now'));
                    $emInfraestructura->persist($interfaceModelo);
                    
                    if($interface->caracteristicasInterface)
                    {
                        $json_caracteristicaInterface   = json_decode($interface->caracteristicasInterface);
                        $array_caracteristicasInterface = $json_caracteristicaInterface->detalles;

                        foreach($array_caracteristicasInterface as $caracteristica)
                        {
                            if($caracteristica->detalleId)
                            {
                                $detalleId = $caracteristica->detalleId;
                                $detalle   = $emInfraestructura->getRepository('schemaBundle:AdmiDetalle')->find($detalleId);

                                //grabar detalleInterface
                                $detalleInterface = new AdmiDetalleInterface();
                                $detalleInterface->setDetalleId($detalle);
                                $detalleInterface->setInterfaceModeloId($interfaceModelo);
                                $detalleInterface->setEstado("Activo");
                                $detalleInterface->setUsrCreacion($objSession->get('user'));
                                $detalleInterface->setFeCreacion(new \DateTime('now'));
                                $detalleInterface->setUsrUltMod($objSession->get('user'));
                                $detalleInterface->setFeUltMod(new \DateTime('now'));
                                $emInfraestructura->persist($detalleInterface);
                            }
                        }//cierre foreach
                    }//cierre if caracteristicasInterface
                }//cierre if interfaces   
            }//cierre for
            
            //ModeloUsuarioAcceso
            $json_usuariosAcceso  = json_decode($arrayParametros['usuariosAcceso']);
            $array_usuariosAcceso = $json_usuariosAcceso->usuariosAcceso;
            
            foreach($array_usuariosAcceso as $usuario)
            {
                $usuarioAcceso = $emInfraestructura->getRepository('schemaBundle:AdmiUsuarioAcceso')->find($usuario->usuarioAccesoId);
                
                $modeloUsuarioAcceso = new AdmiModeloUsuarioAcceso();
                $modeloUsuarioAcceso->setModeloElementoId($objAdmiModeloElemento);
                $modeloUsuarioAcceso->setUsuarioAccesoId($usuarioAcceso);
                $modeloUsuarioAcceso->setEsPreferencia($usuario->esPreferencia);
                $modeloUsuarioAcceso->setEstado("Activo");
                $modeloUsuarioAcceso->setUsrCreacion($objSession->get('user'));
                $modeloUsuarioAcceso->setFeCreacion(new \DateTime('now'));
                $modeloUsuarioAcceso->setUsrUltMod($objSession->get('user'));
                $modeloUsuarioAcceso->setFeUltMod(new \DateTime('now'));
                $emInfraestructura->persist($modeloUsuarioAcceso);
            }
            
            //ModeloProtocolo
            $json_protocolos  = json_decode($arrayParametros['protocolos']);
            $array_protocolos = $json_protocolos->protocolos;
            
            foreach($array_protocolos as $protocolo)
            {
                $protocoloObj = $emInfraestructura->getRepository('schemaBundle:AdmiProtocolo')->find($protocolo->protocoloId);
                
                $modeloProtocolo = new AdmiModeloProtocolo();
                $modeloProtocolo->setModeloElementoId($objAdmiModeloElemento);
                $modeloProtocolo->setProtocoloId($protocoloObj);
                $modeloProtocolo->setEsPreferido($protocolo->esPreferenciaProtocolo);
                $modeloProtocolo->setEstado("Activo");
                $modeloProtocolo->setUsrCreacion($objSession->get('user'));
                $modeloProtocolo->setFeCreacion(new \DateTime('now'));
                $modeloProtocolo->setUsrUltMod($objSession->get('user'));
                $modeloProtocolo->setFeUltMod(new \DateTime('now'));
                $emInfraestructura->persist($modeloProtocolo);
            }
            

            $json_tecnologias  = json_decode($arrayParametros['tecnologias']);
            $array_tecnologias = $json_tecnologias->tecnologias;
            
            foreach($array_tecnologias as $tecnologia)
            {
                $tecnologiaObj = $emInfraestructura->getRepository('schemaBundle:AdmiTecnologia')->find($tecnologia->tecnologiaId);
                
                $modeloTecnologia = new AdmiModeloTecnologia();
                $modeloTecnologia->setModeloElementoId($objAdmiModeloElemento);
                $modeloTecnologia->setTecnologiaId($tecnologiaObj);
                $modeloTecnologia->setEstado("Activo");
                $modeloTecnologia->setUsrCreacion($objSession->get('user'));
                $modeloTecnologia->setFeCreacion(new \DateTime('now'));
                $modeloTecnologia->setUsrUltMod($objSession->get('user'));
                $modeloTecnologia->setFeUltMod(new \DateTime('now'));
                $emInfraestructura->persist($modeloTecnologia);
            }
            
            //DetalleModelo
            $json_detallesModelo  = json_decode($arrayParametros['detallesModelo']);
            $array_detallesModelo = $json_detallesModelo->detallesModelo;
            
            foreach($array_detallesModelo as $detalleModelo)
            {
                $detalle = $emInfraestructura->getRepository('schemaBundle:AdmiDetalle')->find($detalleModelo->detalleModeloId);
                
                $detalleModeloObj = new AdmiDetalleModelo();
                $detalleModeloObj->setDetalleId($detalle);
                $detalleModeloObj->setModeloElementoId($objAdmiModeloElemento);
                $detalleModeloObj->setEstado("Activo");
                $detalleModeloObj->setUsrCreacion($objSession->get('user'));
                $detalleModeloObj->setFeCreacion(new \DateTime('now'));
                $detalleModeloObj->setUsrUltMod($objSession->get('user'));
                $detalleModeloObj->setFeUltMod(new \DateTime('now'));
                $emInfraestructura->persist($detalleModeloObj);
            }
            //Actualizar los parametros solo en el caso de las camaras
            
            $objTipoElemento       = $emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')->find($arrayParametros['tipoElementoId']);
            $emGeneral = $this->get('doctrine')->getManager('telconet_general');
            $arrayCamarasSafeCity = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->findBy(array(
                                            'descripcion'   => 'MAPEO TIPOS ELEMENTOS CAMARA',
                                            'estado'            => 'Activo'));
            $strTipoElemento = $objTipoElemento->getNombreTipoElemento();
            //Verificamos que el tipo de elemento coincida con el tipo de camaras habilitadas para anadir estas caracteristicas
            $boolCamaraValida = false;
            foreach ($arrayCamarasSafeCity as $indice=>$valor)
            {
                $arrayCamarasSafeCity[$indice] = $valor;
                if($valor->getValor1()==$strTipoElemento)
                {
                    $boolCamaraValida=true;
                    break;
                }
            }
            if($boolCamaraValida)
            {
                //Solo en el caso de camaras, guardamos el tipo al que pertenece
                $strTipoCamara            = $objRequest->request->get('tipoCamara');
                $strResolucionCamara      = $objRequest->request->get('resolucionCamara');
                $emGeneral = $this->get('doctrine')->getManager('telconet_general');
                $objPrametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array( 'nombreParametro'   => 'PARAMETROS PROYECTO GPON SAFECITY',
                                                            'estado'            => 'Activo'));

                $objAdmiParametro = new AdmiParametroDet();

                $objAdmiParametro->setParametroId($objPrametroCab);
                $objAdmiParametro->setDescripcion('MODELOS_CAMARAS');
                $objAdmiParametro->setValor1($arrayParametros['nombreModeloElemento']);
                $objAdmiParametro->setValor2($strTipoCamara);
                $objAdmiParametro->setValor3($strResolucionCamara);
                $objAdmiParametro->setEstado('Activo');
                $objAdmiParametro->setUsrCreacion($objSession->get('user'));
                $objAdmiParametro->setFeCreacion(new \DateTime('now'));
                $objAdmiParametro->setIpCreacion('127.0.0.1');
                $objAdmiParametro->setEmpresaCod('10');
                
                $emGeneral->getConnection()->beginTransaction();
                $emGeneral->persist($objAdmiParametro);
                $emGeneral->flush();
                $emGeneral->getConnection()->commit();
            }
            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admimodeloelemento_show', array('id' => $objAdmiModeloElemento->getId())));
        }
        
        return $this->render('administracionBundle:AdmiModeloElemento:new.html.twig', array(
            'entity' => $objAdmiModeloElemento,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_119-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");

        if (null == $modelo = $em->find('schemaBundle:AdmiModeloElemento', $id)) {
            throw new NotFoundHttpException('No existe el Modelo que se quiere modificar');
        }
//
//        print($modelo->getId());
//        die();
        
        $formulario =$this->createForm(new AdmiModeloElementoType(), $modelo);
//        $formulario->setData($modelo);

        return $this->render('administracionBundle:AdmiModeloElemento:edit.html.twig', array(
                                    'edit_form' => $formulario->createView(),
                                    'modelo'   => $modelo));
    }
    
    /**
     * @Secure(roles="ROLE_119-5")
     * 
     * Documentación para el método 'updateAction'.
     *
     * Crea el modelo de un elemento asociado a su marca y tipo.
     * 
     * @return view 
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 05-11-2015 - Se modifica el usuario de creacion y modificación
     * 
     * @author John Vera <javera@telconet.ec 
     * @version 1.1 17-10-2016 Se setea el user de la session
     * 
     * @version 1.0 Version Inicial
     */
    public function updateAction($id)
    {
        $objRequest = $this->get('request');
        $objSession = $objRequest->getSession();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');

        $entity = $em->getRepository('schemaBundle:AdmiModeloElemento')->find($id);
        
        if (!$entity) 
        {
            throw $this->createNotFoundException('Unable to find AdmiModeloElemento entity.');
        }
        $editForm   = $this->createForm(new AdmiModeloElementoType(), $entity);

        $request    = $this->getRequest();
        $parametros = $request->request->get('telconet_schemabundle_admimodeloelementotype');
        
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($objSession->get('user'));

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            
            $json_interfaceModelo = json_decode($parametros['interfacesModelos']);
            $array_interfaceModelo = $json_interfaceModelo->interfacesModelos;
            
            foreach($array_interfaceModelo as $interface)
            {
                if($interface->idInterfaceModelo)
                {
                    $tipoInterfaceId = $interface->tipoInterfaceId;
                    $tipoInterface = $em->getRepository('schemaBundle:AdmiTipoInterface')->find($tipoInterfaceId);
                    $cantidadInterface = $interface->cantidadInterface;
                    $formatoInterface = $interface->formatoInterface;
                    $claseInterface = $interface->claseInterface;
                    
                    //grabar interfaceModelo
                    $interfaceModelo = $em->getRepository('schemaBundle:AdmiInterfaceModelo')->find($interface->idInterfaceModelo);
                    $interfaceModelo->setCantidadInterface($cantidadInterface);
                    $interfaceModelo->setModeloElementoId($entity);
                    $interfaceModelo->setTipoInterfaceId($tipoInterface);
                    $interfaceModelo->setClaseInterface($claseInterface);
                    $interfaceModelo->setFormatoInterface($formatoInterface);
                    $interfaceModelo->setEstado("Activo");
                    $interfaceModelo->setUsrCreacion($objSession->get('user'));
                    $interfaceModelo->setFeCreacion(new \DateTime('now'));
                    $interfaceModelo->setUsrUltMod($objSession->get('user'));
                    $interfaceModelo->setFeUltMod(new \DateTime('now'));
                    $em->persist($interfaceModelo);
                }
                else{
                    $tipoInterfaceId = $interface->tipoInterfaceId;
                    $tipoInterface = $em->getRepository('schemaBundle:AdmiTipoInterface')->find($tipoInterfaceId);
                    $cantidadInterface = $interface->cantidadInterface;
                    $formatoInterface = $interface->formatoInterface;
                    $claseInterface = $interface->claseInterface;
                    
                    //grabar interfaceModelo
                    $interfaceModelo = new AdmiInterfaceModelo();
                    $interfaceModelo->setCantidadInterface($cantidadInterface);
                    $interfaceModelo->setModeloElementoId($entity);
                    $interfaceModelo->setTipoInterfaceId($tipoInterface);
                    $interfaceModelo->setClaseInterface($claseInterface);
                    $interfaceModelo->setFormatoInterface($formatoInterface);
                    $interfaceModelo->setEstado("Activo");
                    $interfaceModelo->setUsrCreacion($objSession->get('user'));
                    $interfaceModelo->setFeCreacion(new \DateTime('now'));
                    $interfaceModelo->setUsrUltMod($objSession->get('user'));
                    $interfaceModelo->setFeUltMod(new \DateTime('now'));
                    $em->persist($interfaceModelo);
                    
                    
                }
                
                if($interface->caracteristicasInterface){
                    $json_caracteristicaInterface = json_decode($interface->caracteristicasInterface);
                    $array_caracteristicasInterface = $json_caracteristicaInterface->detalles;
                    foreach($array_caracteristicasInterface as $caracteristica){
                        $detalleId = $caracteristica->idDetalle;
                        $detalle = $em->getRepository('schemaBundle:AdmiDetalle')->find($detalleId);
                        if($caracteristica->idDetalleInterface){
                            //grabar detalleInterface
                            $detalleInterface = $em->getRepository('schemaBundle:AdmiDetalleInterface')->find($caracteristica->idDetalleInterface);
                            $detalleInterface->setDetalleId($detalle);
                            $detalleInterface->setInterfaceModeloId($interfaceModelo);
                            $detalleInterface->setEstado("Activo");
                            $detalleInterface->setUsrUltMod($objSession->get('user'));
                            $detalleInterface->setFeUltMod(new \DateTime('now'));
                            $em->persist($detalleInterface);
                        }
                        else{
                            //grabar detalleInterface
                            $detalleInterface = new AdmiDetalleInterface();
                            $detalleInterface->setDetalleId($detalle);
                            $detalleInterface->setInterfaceModeloId($interfaceModelo);
                            $detalleInterface->setEstado("Activo");
                            $detalleInterface->setUsrCreacion($objSession->get('user'));
                            $detalleInterface->setFeCreacion(new \DateTime('now'));
                            $detalleInterface->setUsrUltMod($objSession->get('user'));
                            $detalleInterface->setFeUltMod(new \DateTime('now'));
                            $em->persist($detalleInterface);
                        }
                    }
                }//cierre if caracteristicasInterface
            }//cierre for interfaces
            
            //ModeloUsuarioAcceso
            $json_usuariosAcceso = json_decode($parametros['usuariosAcceso']);
            $array_usuariosAcceso = $json_usuariosAcceso->usuariosAcceso;
            foreach($array_usuariosAcceso as $usuario){
                $usuarioAcceso = $em->getRepository('schemaBundle:AdmiUsuarioAcceso')->find($usuario->usuarioAccesoId);
                
                if($usuario->idModeloUsuarioAcceso){
                    $modeloUsuarioAcceso = $em->getRepository('schemaBundle:AdmiModeloUsuarioAcceso')->find($usuario->idModeloUsuarioAcceso);
                    $modeloUsuarioAcceso->setModeloElementoId($entity);
                    $modeloUsuarioAcceso->setUsuarioAccesoId($usuarioAcceso);
                    $modeloUsuarioAcceso->setEsPreferencia($usuario->esPreferencia);
                    $modeloUsuarioAcceso->setEstado("Activo");
                    $modeloUsuarioAcceso->setUsrUltMod($objSession->get('user'));
                    $modeloUsuarioAcceso->setFeUltMod(new \DateTime('now'));
                    $em->persist($modeloUsuarioAcceso);
                }
                else{
                    $modeloUsuarioAcceso = new AdmiModeloUsuarioAcceso();
                    $modeloUsuarioAcceso->setModeloElementoId($entity);
                    $modeloUsuarioAcceso->setUsuarioAccesoId($usuarioAcceso);
                    $modeloUsuarioAcceso->setEsPreferencia($usuario->esPreferencia);
                    $modeloUsuarioAcceso->setEstado("Activo");
                    $modeloUsuarioAcceso->setUsrCreacion($objSession->get('user'));
                    $modeloUsuarioAcceso->setFeCreacion(new \DateTime('now'));
                    $modeloUsuarioAcceso->setUsrUltMod($objSession->get('user'));
                    $modeloUsuarioAcceso->setFeUltMod(new \DateTime('now'));
                    $em->persist($modeloUsuarioAcceso);
                }
                
            }
            
            //ModeloProtocolo
            $json_protocolos = json_decode($parametros['protocolos']);
            $array_protocolos = $json_protocolos->protocolos;
            foreach($array_protocolos as $protocolo){
                $protocoloObj = $em->getRepository('schemaBundle:AdmiProtocolo')->find($protocolo->protocoloId);
                
                if($protocolo->idModeloProtocolo){
                    $modeloProtocolo = $em->getRepository('schemaBundle:AdmiModeloProtocolo')->find($protocolo->idModeloProtocolo);
                    $modeloProtocolo->setModeloElementoId($entity);
                    $modeloProtocolo->setProtocoloId($protocoloObj);
                    $modeloProtocolo->setEsPreferido($protocolo->esPreferenciaProtocolo);
                    $modeloProtocolo->setEstado("Activo");
                    $modeloProtocolo->setUsrUltMod($objSession->get('user'));
                    $modeloProtocolo->setFeUltMod(new \DateTime('now'));
                    $em->persist($modeloProtocolo);
                }
                else{
                    $modeloProtocolo = new AdmiModeloProtocolo();
                    $modeloProtocolo->setModeloElementoId($entity);
                    $modeloProtocolo->setProtocoloId($protocoloObj);
                    $modeloProtocolo->setEsPreferido($protocolo->esPreferenciaProtocolo);
                    $modeloProtocolo->setEstado("Activo");
                    $modeloProtocolo->setUsrCreacion($objSession->get('user'));
                    $modeloProtocolo->setFeCreacion(new \DateTime('now'));
                    $modeloProtocolo->setUsrUltMod($objSession->get('user'));
                    $modeloProtocolo->setFeUltMod(new \DateTime('now'));
                    $em->persist($modeloProtocolo);
                }
                
            }
            
            //ModeloTecnologia
            $json_tecnologias = json_decode($parametros['tecnologias']);
            $array_tecnologias = $json_tecnologias->tecnologias;
            foreach($array_tecnologias as $tecnologia){
                $tecnologiaObj = $em->getRepository('schemaBundle:AdmiTecnologia')->find($tecnologia->tecnologiaId);
                
                if($tecnologia->idModeloTecnologia){
                    $modeloTecnologia = $em->getRepository('schemaBundle:AdmiModeloTecnologia')->find($tecnologia->idModeloTecnologia);
                    $modeloTecnologia->setModeloElementoId($entity);
                    $modeloTecnologia->setTecnologiaId($tecnologiaObj);
                    $modeloTecnologia->setEstado("Activo");
                    $modeloTecnologia->setUsrUltMod($objSession->get('user'));
                    $modeloTecnologia->setFeUltMod(new \DateTime('now'));
                    $em->persist($modeloTecnologia);
                }
                else{
                    $modeloTecnologia = new AdmiModeloTecnologia();
                    $modeloTecnologia->setModeloElementoId($entity);
                    $modeloTecnologia->setTecnologiaId($tecnologiaObj);
                    $modeloTecnologia->setEstado("Activo");
                    $modeloTecnologia->setUsrCreacion($objSession->get('user'));
                    $modeloTecnologia->setFeCreacion(new \DateTime('now'));
                    $modeloTecnologia->setUsrUltMod($objSession->get('user'));
                    $modeloTecnologia->setFeUltMod(new \DateTime('now'));
                    $em->persist($modeloTecnologia);
                }
                
            }
            
            //DetalleModelo
            $json_detallesModelo = json_decode($parametros['detallesModelo']);
            $array_detallesModelo = $json_detallesModelo->detallesModelo;
            foreach($array_detallesModelo as $detalleModelo){
                $detalle = $em->getRepository('schemaBundle:AdmiDetalle')->find($detalleModelo->detalleModeloId);
                
                if($detalleModelo->idDetalleModelo){
                    $detalleModeloObj = $em->getRepository('schemaBundle:AdmiDetalleModelo')->find($detalleModelo->idDetalleModelo);
                    $detalleModeloObj->setDetalleId($detalle);
                    $detalleModeloObj->setModeloElementoId($entity);
                    $detalleModeloObj->setEstado("Activo");
                    $detalleModeloObj->setUsrUltMod($objSession->get('user'));
                    $detalleModeloObj->setFeUltMod(new \DateTime('now'));
                    $em->persist($detalleModeloObj);
                }
                else{
                    $detalleModeloObj = new AdmiDetalleModelo();
                    $detalleModeloObj->setDetalleId($detalle);
                    $detalleModeloObj->setModeloElementoId($entity);
                    $detalleModeloObj->setEstado("Activo");
                    $detalleModeloObj->setUsrCreacion($objSession->get('user'));
                    $detalleModeloObj->setFeCreacion(new \DateTime('now'));
                    $detalleModeloObj->setUsrUltMod($objSession->get('user'));
                    $detalleModeloObj->setFeUltMod(new \DateTime('now'));
                    $em->persist($detalleModeloObj);
                }
                
            }
            //Actualizar los parametros solo en el caso de las camaras
           $objTipoElemento       = $em->getRepository('schemaBundle:AdmiTipoElemento')->find($parametros['tipoElementoId']);
            $emGeneral = $this->get('doctrine')->getManager('telconet_general');
            $arrayCamarasSafeCity = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->findBy(array(
                                            'descripcion'   => 'MAPEO TIPOS ELEMENTOS CAMARA',
                                            'estado'            => 'Activo'));
            
            $strTipoElemento = $objTipoElemento->getNombreTipoElemento();
            //Verificamos que el tipo de elemento coincida con el tipo de camaras habilitadas para anadir estas caracteristicas
            $boolCamaraValida = false;
            foreach ($arrayCamarasSafeCity as $indice=>$valor)
            {
                $arrayCamarasSafeCity[$indice] = $valor;
                if($valor->getValor1()==$strTipoElemento)
                {
                    $boolCamaraValida=true;
                    break;
               }
            }
            //Solo en el caso de camaras, guardamos el tipo al que pertenece
            $strTipoCamara            = $objRequest->request->get('tipoCamara');
            $strResolucionCamara      = $objRequest->request->get('resolucionCamara');
            
            if($boolCamaraValida && ($strTipoCamara!=="Seleccione"||$strResolucionCamara!=="Seleccione") )
            {   
                $emGeneral->getConnection()->beginTransaction();
                $objModeloCamara = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->findOneBy(array(
                                        'descripcion' => 'MODELOS_CAMARAS',
                                        'valor1'      =>$parametros['nombreModeloElemento'],
                                        'estado'      => 'Activo'));
                //Si el modelo seleccionado no cuenta con estas caracteristicas se las agregamos
                if(!$objModeloCamara)
                {
                    $objPrametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array( 'nombreParametro'   => 'PARAMETROS PROYECTO GPON SAFECITY',
                                                                'estado'            => 'Activo'));
                    $objAdmiParametro = new AdmiParametroDet();
                    $objAdmiParametro->setParametroId($objPrametroCab);
                    $objAdmiParametro->setDescripcion('MODELOS_CAMARAS');
                    $objAdmiParametro->setValor1($parametros['nombreModeloElemento']);
                    $objAdmiParametro->setValor2($strTipoCamara);
                    $objAdmiParametro->setValor3($strResolucionCamara);
                    $objAdmiParametro->setEstado('Activo');
                    $objAdmiParametro->setUsrCreacion($objSession->get('user'));
                    $objAdmiParametro->setFeCreacion(new \DateTime('now'));
                    $objAdmiParametro->setIpCreacion('127.0.0.1');
                    $objAdmiParametro->setEmpresaCod('10');

                    $emGeneral->persist($objAdmiParametro);

                }
                //Si solo es actualizacion
                if($objModeloCamara) 
                {
                    $objModeloCamara->setValor1($parametros['nombreModeloElemento']);
                    $objModeloCamara->setValor2($strTipoCamara);
                    $objModeloCamara->setValor3($strResolucionCamara);
                    $objModeloCamara->setUsrUltMod($objSession->get('user'));
                    $objModeloCamara->setFeUltMod(new \DateTime('now'));
            
                    $emGeneral->persist($objModeloCamara);
                    
                }
                $emGeneral->flush();
                $emGeneral->getConnection()->commit();
            }
            
            $em->flush();
            $em->getConnection()->commit();

            return $this->redirect($this->generateUrl('admimodeloelemento_show', array('id' => $id)));
        }
        else{
            print_r($editForm->getErrors());
            die();
        }

        return $this->render('administracionBundle:AdmiModeloElemento:edit.html.twig',array(
            'modelo'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
     * @Secure(roles="ROLE_119-8")
     * 
     * Documentación para el método 'createAction'.
     *
     * Crea el modelo de un elemento asociado a su marca y tipo.
     * 
     * @return view 
     *
     * @author Modificado: Edson Franco <efranco@telconet.ec>
     * @version 1.1 05-11-2015 - Se modifica el usuario de modificación
     *
     * @version 1.0 Version Inicial
     */
    public function deleteAction($id)
    {
        $objRequest = $this->get('request');
        $objSession = $objRequest->getSession();

        $emInfraestructura     = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objAdmiModeloElemento = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->find($id);

        if (!$objAdmiModeloElemento) 
        {
            throw $this->createNotFoundException('No se encontro el objeto AdmiModeloElemento.');
        }
        
        $strEstado = 'Eliminado';
        
        $objAdmiModeloElemento->setEstado($strEstado);
        /*Para que guarde la fecha y el usuario correspondiente*/
        $objAdmiModeloElemento->setFeUltMod(new \DateTime('now'));
        $objAdmiModeloElemento->setUsrUltMod($objSession->get('user'));
        /*Para que guarde la fecha y el usuario correspondiente*/

        $emInfraestructura->persist($objAdmiModeloElemento);	
        $emInfraestructura->flush();
        
        return $this->redirect($this->generateUrl('admimodeloelemento'));
    }

    /**
    * @Secure(roles="ROLE_119-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        
        $parametro = $peticion->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiModeloElemento', $id)) {
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
    
    /**
    * @Secure(roles="ROLE_119-46")
    *
    * Documentación para el método 'createAction'.
    *
    * Consulta el modelo de un elemento asociado a su marca y tipo.
    * 
    * @return view 
    *
    * @author Modificado: Antonio Ayala <afayala@telconet.ec>
    * @version 1.1 16-03-2020 - Se obtiene el id del modelo del ont
    *
    * @author Modificado: Antonio Ayala <afayala@telconet.ec>
    * @version 1.2 15-04-2020 - Se agrega validación del objeto $objTipoElemento
    *
    * @version 1.0 Version Inicial
    */
    public function getEncontradosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $session = $this->get('session');
//        $session->save();
//        session_write_close();
        
        $peticion = $this->get('request');
        
        $nombre = $peticion->query->get('nombre');
        $marca = $peticion->query->get('marcaElemento');
        $tipoElemento = $peticion->query->get('tipoElemento');
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        
        $objEm          = $this->getDoctrine()->getManager('telconet_infraestructura');
               
        $objTipoElemento    = $objEm->getRepository('schemaBundle:AdmiTipoElemento')->findOneBy(array("nombreTipoElemento" => $tipoElemento));
        if(is_object($objTipoElemento))
        {
            $tipoElemento       = $objTipoElemento->getId();
        }
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonModelosElementos($nombre,$marca,$tipoElemento,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getModelosElementosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonModelosElementos("","","","Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getModelosElementosPorMarcaAction(){
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $marca = $peticion->query->get('idMarca');
        $tipoElemento = $peticion->query->get('tipoElemento');
        $tipoElementoId = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>$tipoElemento));
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonModelosElementos("",$marca,$tipoElementoId[0]->getId(),"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getModelosElementosDslamAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"DSLAM"));
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
//        $objJson = $this->getDoctrine()
//            ->getManager("telconet_infraestructura")
//            ->getRepository('schemaBundle:AdmiModeloElemento')
//            ->generarJsonModelosElementosPorTipo($tipoElemento,"Activo",$start,$limit);
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonModelosElementos("","",$tipoElemento[0]->getId(),"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getModelosElementosNodoAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->findBy(array( "nombreTipoElemento" =>"NODO"));
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
//        $objJson = $this->getDoctrine()
//            ->getManager("telconet_infraestructura")
//            ->getRepository('schemaBundle:AdmiModeloElemento')
//            ->generarJsonModelosElementosPorTipo($tipoElemento,"Activo",$start,$limit);
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonModelosElementos("","",$tipoElemento[0]->getId(),"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que obtiene los modelos
     * por tipo elemento CPE
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-05-2014
     */
    public function getModelosElementosCpeAction(){
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $marca = $peticion->query->get('idMarca');
        $tipoElemento = $peticion->query->get('tipoElemento');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonModelosElementosCpe("",$marca,$tipoElemento,"Activo",$start,100);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getInterfacesModeloAction($id){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $peticion = $this->get('request');
        
        $estado = "Todos";
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiInterfaceModelo')
            ->generarJsonInterfacesModelosPorModeloElemento($id,$estado,$start,$limit, $emInfraestructura);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getInterfacesParaCpeAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $estado = "Todos";
        $idModelo = $peticion->query->get('idModelo');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiInterfaceModelo')
            ->generarJsonInterfacesModelosParaCpe($idModelo,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getModeloUsuariosAccesoAction($id){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $peticion = $this->get('request');
        
        $estado = "Todos";
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloUsuarioAcceso')
            ->generarJsonModeloUsuariosAcceso($id,$estado,$start,$limit, $emInfraestructura);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getModeloProtocolosAction($id){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $peticion = $this->get('request');
        
        $estado = "Todos";
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloProtocolo')
            ->generarJsonModeloUsuariosAcceso($id,$estado,$start,$limit,$emInfraestructura);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getModeloTecnologiasAction($id){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $peticion = $this->get('request');
        
        $estado = "Todos";
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloTecnologia')
            ->generarJsonModeloTecnologia($id,$estado,$start,$limit,$emInfraestructura);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getDetallesModeloAction($id){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $peticion = $this->get('request');
        
        $estado = "Todos";
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiDetalleModelo')
            ->generarJsonDetallesModelo($id,$estado,$start,$limit,$emInfraestructura);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function getAllDetallesAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $peticion = $this->get('request');
        
        $id = $peticion->query->get('idModelo');
        $estado = "Todos";
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiDetalleModelo')
            ->generarJsonAllDetalles($id,$estado,$start,$limit,$emInfraestructura);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * Funcion que sirve para buscar los usuarios relacionados con el
     * modelo del elementos
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-02-2015
     */
    public function buscarUsuariosPorModeloAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');

        $modeloId = $peticion->get('modeloId');
        $estado = $peticion->get('estado');
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');

        $objJson = $this->getDoctrine()->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloUsuarioAcceso')
            ->generarJsonModeloUsuariosAcceso($modeloId, $estado, $start, $limit, $emInfraestructura);
        $respuesta->setContent($objJson);

        return $respuesta;
    }

    /**
     * Funcion que sirve para buscar los modelos por tipo de elemento
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-02-2015
     */
    public function buscarModeloPorTipoElementoAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        
        $tipoElemento = $peticion->get('tipoElemento');
        $estado = $peticion->get('estado');
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');        

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonModelosElementos('','',$tipoElemento,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * Funcion que sirve para buscar elementos filtrados por modelo 
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-02-2015
     */
    public function buscarElementoPorModeloElementoAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        
        $modeloElementoId = $peticion->get('modeloElemento');
        $nombreElemento = $peticion->query->get('query');
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
        
        $session = $this->get('session');
        $empresa = $session->get('idEmpresa');

        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiModeloElemento')
            ->generarJsonElementosPorModeloElemento($nombreElemento,$modeloElementoId,$empresa,"Activo",$start, $limit);
        $respuesta->setContent($objJson);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * getModelosPorTipoElementoAction
     * 
     * Función que obtiene los modelos por el tipo de elemento
     *
     * @return json
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 02-12-2016
     */
    public function getModelosPorTipoElementoAction()
    {
        $objResponse            = new JsonResponse();
        $emInfraestructura      = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest             = $this->getRequest();
        $strTipoElemento        = $objRequest->get('strTipoElemento');
        $strEstadoModelo        = $objRequest->get('strEstadoModelo');
        $arrayTmpParametros     = array( 'estadoActivo' => $strEstadoModelo, 'tipoElemento' => array($strTipoElemento) );
        
        $strResultado           = $emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                    ->getJsonModeloElementosByCriterios( $arrayTmpParametros );
        $objResponse->setContent($strResultado);
        return $objResponse;
    }

    /**
     * Metodo para obtener los tipos de camaras de safecity
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.0 20-09-2022 Version inicial
     * @return Object Tipos de camara
     */
    public function getTiposCamaraAction()
    {
        $objRespuesta         = new Response();
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
      
        $arrayResult    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                               ->get('PARAMETROS PROYECTO GPON SAFECITY',
                                     'INFRAESTRUCTURA',
                                     'PARAMETROS',
                                     'TIPO_CAMARA',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     10);

        $objRespuesta->setContent(json_encode($arrayResult));
        return $objRespuesta;
    }
    
    /**
     * Metodo para obtener las resoluciones de camaras de safecity
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.0 20-09-2022 Version inicial
     * @return Object  de camaras
     */
    public function getResolucionCamaraAction()
    {
        $objRespuesta         = new Response();
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
      
        $arrayResult    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                               ->get('PARAMETROS PROYECTO GPON SAFECITY',
                                     'INFRAESTRUCTURA',
                                     'PARAMETROS',
                                     'RESOLUCION_CAMARA',
                                     '',
                                     '',
                                     '',
                                     '',
                                     '',
                                     10);

        $objRespuesta->setContent(json_encode($arrayResult));
        return $objRespuesta;
    }
    /**
     * Metodo para obtener los demas parametros de las camaras de safecity
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.0 20-09-2022 Version inicial
     * @return Object Tipo y resolucion de la camara
     */
    public function getParametrosDetCamaraAction()
    {
        $objRequest            = $this->get('request');
        $strModeloElemento = $objRequest->query->get('modeloElemento');

        $objRespuesta = new Response();
        $emGeneral = $this->getDoctrine()->getManager('telconet_general');

        $arrayResult    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                               ->get('PARAMETROS PROYECTO GPON SAFECITY',
                                     'INFRAESTRUCTURA',
                                     'PARAMETROS',
                                     'MODELOS_CAMARAS',
                                     $strModeloElemento,
                                     '',
                                     '',
                                     '',
                                     '',
                                     10);
        $arrayRespuesta = array();
        if($arrayResult[0]['valor3']!==null)
        {
            $objResolucionCamara = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(array('valor1'=>$arrayResult[0]['valor3']));
            $arrayRespuesta['tipo']=$arrayResult[0]['valor2'];
            $arrayRespuesta['resolucion'] = $arrayResult[0]['valor3'];
            $arrayRespuesta['resolucionAlterna']=$objResolucionCamara->getValor2().'x'.$objResolucionCamara->getValor3();
        }

        $objRespuesta->setContent(json_encode($arrayRespuesta));
        return $objRespuesta;
    }

    /**
     * 
     * Metodo para obtener la camaras validas de safecity
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.0 20-09-2022 Version inicial
     * @return Object Camaras validas
     */
    public function getCamarasValidasSafecityAction()
    {
        $objRespuesta         = new Response();
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $arrayResult    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
        ->get('PARAMETROS PROYECTO GPON SAFECITY',
              'INFRAESTRUCTURA',
              'PARAMETROS',
              'MAPEO TIPOS ELEMENTOS CAMARA',
              '',
              '',
              '',
              '',
              '',
              10);

        $objRespuesta->setContent(json_encode($arrayResult));
        return $objRespuesta;
    }
}