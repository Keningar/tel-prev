<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDeposito;
use telconet\schemaBundle\Entity\InfoDepositoHistorial;
use telconet\schemaBundle\Form\InfoDepositoType;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\financieroBundle\Service\InfoPagoAutomaticoService;



use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;

/**
 * InfoDeposito controller.
 *
 */
class InfoDepositoController extends Controller implements TokenAuthenticatedController
{

    /**
     * Documentacion para funcion 'indexAction'
     * Funcion que muestra los pagos depositables
     * Actualizacion: Se envia la empresa para habilitar opciones segun empresa
     * @return Objeto response
     * @author amontero@telconet.ec
     * @version 1.1 06-06-2016
     */          
    public function indexAction()
    {
        $session           = $this->getRequest()->getSession();         
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $em                = $this->getDoctrine()->getManager('telconet_financiero');
        $entities          = $em->getRepository('schemaBundle:InfoDeposito')->findAll();
        return $this->render('financieroBundle:InfoDeposito:index.html.twig', array(
            'entities'          => $entities,
            'strPrefijoEmpresa' => $strPrefijoEmpresa
        ));
    }

    public function formasPagoAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $entities = $em->getRepository('schemaBundle:AdmiFormaPago')->findAll();
        foreach($entities as $dato):
            $arreglo[]=array('idformapago'=>$dato->getDescripcionFormaPago(),
            'codigo'=>$dato->getCodigoFormaPago(),'descripcion'=>$dato->getDescripcionFormaPago());
        endforeach;
        $response = new Response(json_encode(array('formas'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
	return $response;
		
    }
    
    public function formasPagoDepositablesAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $entities = $em->getRepository('schemaBundle:AdmiFormaPago')->findBy(array("esDepositable" => 'S'));        
        foreach($entities as $dato):
            $arreglo[]=array('idformapago'=>$dato->getDescripcionFormaPago(),
            'codigo'=>$dato->getCodigoFormaPago(),'descripcion'=>$dato->getDescripcionFormaPago());
        endforeach;
        $response = new Response(json_encode(array('formas'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
	return $response;
		
    }

    /**
     * Documentacion para funcion 'gridPagosNoDepositadosAction'
     * Funcion que muestra los pagos depositables
     * Actualizacion: Los usuarios que tengan el perfil de jefe de cobranzas 
     * se permite ver todos los pagos generados a nivel nacional
     * @return Objeto response
     * @author amontero@telconet.ec
     * @version 1.1 06-06-2016
     */      
    public function gridPagosNoDepositadosAction()
    {
        $request      = $this->getRequest();
        $session      = $request->getSession();        
        $empresaId    = $session->get('idEmpresa');     
        $fechaDesde   = explode('T', $request->get("fechaDesde"));
        $fechaHasta   = explode('T', $request->get("fechaHasta"));
        $formapago    = $request->get("formapago");
        $creadopor    = $request->get("creadopor");
        $limit        = $request->get("limit");
        $page         = $request->get("page");
        $start        = $request->get("start");
        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $emComercial  = $this->get('doctrine')->getManager('telconet');
        $emSeguridad  = $this->get('doctrine')->getManager('telconet_seguridad');        
        //obtener los datos y departamento de la persona por empresa
        $datosUsuario = $emComercial->getRepository('schemaBundle:InfoPersona')
                            ->getPersonaDepartamentoPorUser($request->getSession()->get('user'));        
        $oficinaId    = null;
        
        //verificar si tiene el perfil de jefe de cobranzas
        //Si tiene perfil de cobranzas puede ver todos los depositos generados a nivel nacional
        $arrayPerfil  = $emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                            ->getAccesoPorPerfilPersona("Perfil: JefeCobranzas", $datosUsuario['ID_PERSONA']);        
        if(count($arrayPerfil)<1)
        {
            //VERA POR OFICINA
            $oficinaId= $request->getSession()->get('idOficina');
        }
        
        $resultado = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                        ->findPagosNoDepositados($empresaId,$oficinaId,$fechaDesde[0], $fechaHasta[0],$formapago, $limit, $page, $start,$creadopor);
        $datos     = $resultado['registros'];
        $total     = $resultado['total'];
        foreach ($datos as $datos):
            $entityOficina   = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($datos->getPagoId()->getOficinaId());
            $urlVer          = $this->generateUrl('infopagocab_show', array('id' => $datos->getPagoId()->getId()));
            $linkVer         = $urlVer;
            $entityInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')->find($datos->getPagoId()->getPuntoId());
            if($entityInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
            {
                $cliente = $entityInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
            }
            else
            {
                $cliente = $entityInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->getNombres()." ".
                    $entityInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
            }
            $entityFormaPago = $emComercial->getRepository('schemaBundle:AdmiFormaPago')->find($datos->getFormaPagoId());
            $oficinaArr      = explode("-",$entityOficina->getNombreOficina());
            $arreglo[]       = array
            (
                'id'              => $datos->getId(),
                'numero'          => $datos->getPagoId()->getNumeroPago(),
                'cliente'         => $cliente,
                'punto'           => $entityInfoPunto->getLogin(),
                'formaPago'       => $entityFormaPago->getDescripcionFormaPago(),
                'valor'           => $datos->getValorPago(),
                'comentario'      => $datos->getComentario(),
                'fechaCreacion'   => strval(date_format($datos->getFeCreacion(), "d/m/Y G:i")),
                'usuarioCreacion' => $datos->getUsrCreacion(),
                'estado'          => $datos->getEstado(),
                'oficina'         => $oficinaArr[1],
                'linkVer'         => $linkVer
            );
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        else 
        {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    
    /**
     * Documentacion para funcion 'gridDepositosAction'
     * Funcion que muestra los depositos creados
     * Actualizacion: Se genera asiento contable, 
     * tambien los usuarios que tengan el perfil de jefe de cobranzas 
     * se permite ver todos los depositos generados a nivel nacional
     * @return Objeto response
     * @author amontero@telconet.ec
     * @version 1.2 19-01-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 16-03-2017 - Se envía el parámetro 'strPrefijoEmpresa' a la función 'getJSONDepositos' para poder realizar las validaciones 
     *                           correspondientes con el prefijo de la empresa. Adicional se implementa el manejo de excepciones.
     */   
    public function gridDepositosAction()
    {
        $objJson   = null;
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request       = $this->getRequest();
        $objSession    = $request->getSession();
        $serviceUtil   = $this->get('schema.Util');
        $strIpCreacion = $request->getClientIp();
        $strUsuario    = $objSession->get('user');
        
        try
        { 
            $fechaDesde       = explode('T', $request->get("fechaDesde"));
            $fechaHasta       = explode('T', $request->get("fechaHasta"));
            $emSeguridad      = $this->get('doctrine')->getManager('telconet_seguridad');
            $emFinanciero     = $this->get('doctrine')->getManager('telconet_financiero');
            $emComercial      = $this->get('doctrine')->getManager('telconet');
            //obtener los datos y departamento de la persona por empresa
            $datosUsuario     = $emComercial->getRepository('schemaBundle:InfoPersona')->getPersonaDepartamentoPorUser($strUsuario);
            $oficinaId        = null;
            //verificar si tiene el perfil de jefe de cobranzas
            //Si tiene perfil de cobranzas puede ver todos los depositos generados a nivel nacional
            $arrayPerfil      = $emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                                    ->getAccesoPorPerfilPersona("Perfil: JefeCobranzas", $datosUsuario['ID_PERSONA']);        
            if(count($arrayPerfil)<1)
            {
                //VERA POR OFICINA
                $oficinaId= $objSession->get('idOficina');
            }
            $arrayParametros['fechaDesde']        = $fechaDesde[0];
            $arrayParametros['fechaHasta']        = $fechaHasta[0];
            $arrayParametros['comprobante']       = $request->get("numeroComprobante");
            $arrayParametros['tipoFecha']         = $request->get("tipoFecha");
            $arrayParametros['estado']            = $request->get("estado");
            $arrayParametros['limit']             = $request->get("limit");
            $arrayParametros['start']             = $request->get("start");
            $arrayParametros['empresaId']         = $objSession->get('idEmpresa');
            $arrayParametros['oficinaId']         = $oficinaId;
            $arrayParametros['strPrefijoEmpresa'] = $objSession->get('prefijoEmpresa'); 

            $objJson = $emFinanciero->getRepository('schemaBundle:InfoDeposito')->getJSONDepositos($arrayParametros,$this->container);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'InfoDepositoController.gridDepositosAction', 
                                       'Error al consultar el grid de depositos. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
     
        $respuesta->setContent($objJson);
        return $respuesta;        
    }
    
    /**
     * Documentacion para funcion 'procesarDepositoAction'
     * Funcion que muestra los depositos creados
     * Actualizacion: Se envia la empresa para habilitar opciones segun empresa
     * @return Objeto response
     * @author amontero@telconet.ec
     * @version 1.1 06-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 15-03-2017 - Se envía al twig la variable '$strContabiliza' la cual indica si la empresa en session contabiliza, puesto que se 
     *                           requiere validar que cuando la empresa contabilice no pueda editar el depósito procesado, caso contrario se podrá
     *                           editar el depósito procesado.
     */     
    public function procesarDepositoAction()
    {
        $session           = $this->getRequest()->getSession();         
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');        
        $em                = $this->getDoctrine()->getManager('telconet_financiero');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        $entities          = $em->getRepository('schemaBundle:InfoDeposito')->findAll();
        $rolesPermitidos   = array();
        
        //SE VERIFICA SI LA EMPRESA EN SESSION CONTABILIZA
        $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
        $strContabiliza    = ( isset($arrayParametroDet["valor2"]) && !empty($arrayParametroDet["valor2"]) ) ? $arrayParametroDet["valor2"] : 'N';
        
        if (true === $this->get('security.context')->isGranted('ROLE_86-1066'))
        {
                $rolesPermitidos[] = 'ROLE_86-1066'; //editar depositos en financiero
        }
        
        return $this->render('financieroBundle:InfoDeposito:procesarDeposito.html.twig', array( 'entities'          => $entities,
                                                                                                'rolesPermitidos'   => $rolesPermitidos,
                                                                                                'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                                                                                'strContabiliza'    => $strContabiliza ));
    }

    /**
     * Finds and displays a InfoDeposito entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoDeposito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoDeposito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('schemaBundle:InfoDeposito:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new InfoDeposito entity.
     *
     */
    public function newAction()
    {
        $entity = new InfoDeposito();
        $form   = $this->createForm(new InfoDepositoType(), $entity);

        return $this->render('schemaBundle:InfoDeposito:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new InfoDeposito entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new InfoDeposito();
        $form = $this->createForm(new InfoDepositoType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infodeposito_show', array('id' => $entity->getId())));
        }

        return $this->render('schemaBundle:InfoDeposito:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing InfoDeposito entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoDeposito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoDeposito entity.');
        }

        $editForm = $this->createForm(new InfoDepositoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('schemaBundle:InfoDeposito:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
     public function editardepositoAction()
    { 
         
          $request = $this->getRequest();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $usuario=$request->getSession()->get('user');
        //echo $id;die;
        $respuesta->setContent("error del Form");
        $em = $this->getDoctrine()->getManager('telconet_financiero');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        
         
         $fechaProcesa = $request->get("fechaProcesa");
         //echo($fechaProcesa); die();
         $comprobante=$peticion->get('comprobanteDeposito');
      // echo($fechaProcesa); die();
       
         //print_r($comprobante);  die();
        $idDeposito=$peticion->get('iddeposito');

        $em->getConnection()->beginTransaction();
        try {
                $entityDeposito=$em->getRepository('schemaBundle:InfoDeposito')->Find($idDeposito);
                $fechaDepositoActual=strval(date_format($entityDeposito->getFeProcesado(), "Y-m-d"));
                
                
                $fechaDepositoFinal=$fechaProcesa;
                //print_r($fechaDepositoFinal);  die();
                $comprobanteActual=$entityDeposito->getNoComprobanteDeposito();
                // print_r($comprobanteActual);  die();
                $inserHist=false;
             if(isset($fechaProcesa)){
               if($fechaDepositoFinal!=""){
                if($fechaDepositoFinal!=$fechaDepositoActual){
                    //print_r($fechaDepositoActual);
                   //print_r($fechaDepositoFinal);  die();
                   $entityDeposito->setFeProcesado(new \DateTime($fechaDepositoFinal));
                   $inserHist=true;
               
                }
               }
             }
             if(isset($comprobante)){
                if($comprobante!=""){
                 if($comprobante!=$comprobanteActual){ 
                    // print_r($comprobante);  die();
                  $entityDeposito->setNoComprobanteDeposito($comprobante);
                  $inserHist=true;
                  }
                }
             }
              if($inserHist){
                $entityDeposito->setUsrProcesa($usuario);
                $em->persist($entityDeposito);
                $em->flush();

                //GRABA DEPOSITO HISTORIAL
                $entityHistorial = new InfoDepositoHistorial();
                $entityHistorial->setIpCreacion($request->getClientIp());
                $entityHistorial->setFeCreacion(new \DateTime('now'));
                $entityHistorial->setDepositoId($entityDeposito);
                $entityHistorial->setUsrCreacion($usuario);                
                $entityHistorial->setEstado('Modificado');
                $em->persist($entityHistorial);
                $em->flush();                
            
              $em->getConnection()->commit();
               $respuesta->setContent("Se actualizo el  registro con exito.");
              $response = new Response(json_encode(array('success'=>true)));
              $response->headers->set('Content-type', 'text/json');
               }else{
                   $respuesta->setContent("No se actualizaron los datos.");
                   $response = new Response(json_encode(array('success'=>false)));
                   $response->headers->set('Content-type', 'text/json');
               }
             
             
            return $response; 
           
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            //$respuesta->setContent("error al tratar de eliminar registro. Consulte con el Administrador.");
            $respuesta->setContent($e->getMessage());
            $response = new Response(json_encode(array('success'=>false)));
            $response->headers->set('Content-type', 'text/json');
            return $response;            
        }
     }
    
    

    /**
     * Edits an existing InfoDeposito entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoDeposito')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoDeposito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoDepositoType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infodeposito_edit', array('id' => $id)));
        }

        return $this->render('schemaBundle:InfoDeposito:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a InfoDeposito entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoDeposito')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoDeposito entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infodeposito'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
     * Documentacion para funcion 'depositar_ajaxAction'
     * Funcion que genera deposito de los pagos seleccionados
     * Actualizacion: Se genera asiento contable
     * @version 1.2 19-01-2015
     * @return Objeto response
     * @author amontero@telconet.ec
     * @since 03-02-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 13-12-2016 - Se modifica función para que valide lo siguiente:
     *                           - Que no se puedan crear depósitos con valor cero.
     *                           - Que los detalles seleccionados por el usuario no hayan sido depositados con anterioridad y no tengan asociado un 
     *                             id de depósito.
     *                           - Que el valor total del depósito sea igual a la suma total de los detalles seleccionados por el usuario.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 27-12-2016 - Se modifica la comparativa entre el valor total del deposito obtenido por base y el valor acumulado de la suma de
     *                           los detalles seleccionados por el usuario.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 06-06-2022 - Se modifica para que generación de depósito se realice mediante llamada a función desde un service.
     */      
    public function depositar_ajaxAction() 
    {
        $objRequest   = $this->getRequest();
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objRespuesta->setContent("error del Form");        
        $emFinanciero      = $this->getDoctrine()->getManager('telconet_financiero');         
        //Obtiene parametros enviados desde el ajax
        $strDetallesIds    = $objRequest->get('param');
        $arrayDetPagIds    = \explode("|", $strDetallesIds);
        $serviceInfoPagAut = $this->get('financiero.InfoPagoAutomatico');
        
        $arrayParametros                         = [];
        $arrayParametros['intIdCtaContable']     = $objRequest->get('idcuenta');
        $arrayParametros['strIpCreacion']        = $objRequest->getClientIp();
        $arrayParametros['strUsrCreacion']       = $objRequest->getSession()->get('user');
        $arrayParametros['strEmpresaCod']        = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros['intOficinaId']         = $objRequest->getSession()->get('idOficina');        
        $arrayParametros['strPrefijoEmpresa']    = $objRequest->getSession()->get('prefijoEmpresa');
        $arrayParametros['arrayIdsDetallesPago'] = $arrayDetPagIds;
        $arrayParametros['strEstado']            = 'Pendiente';        
        $arrayParametros['boolDepManual']        = true;
               

        $emFinanciero->getConnection()->beginTransaction();
        try 
        {
            
            $boolRespuesta = $serviceInfoPagAut->generarDeposito($arrayParametros);
            if($boolRespuesta)
            {
                $objRespuesta->setContent("Se depositó los registros con éxito.");
                $emFinanciero->getConnection()->commit();
            }            

        } 
        catch (\Exception $e) 
        {
            $emFinanciero->getConnection()->rollback();
            $emFinanciero->getConnection()->close();
            $objRespuesta->setContent($e->getMessage());
        }
        return $objRespuesta;
    }
    
    /*combo BANCOS NAF llenado ajax*/
    public function listaBancosNafAction()
    {
                $arreglo[]= array('idBanco'=>'1','descripcion'=> 'NACIONAL DE FOMENTO');
                $arreglo[]= array('idBanco'=>'2','descripcion'=> 'PICHINCHA');
                $arreglo[]= array('idBanco'=>'3','descripcion'=> 'GUAYAQUIL');
                $arreglo[]= array('idBanco'=>'4','descripcion'=> 'DE MACHALA');
                
                $response = new Response(json_encode(array('bancosNaf'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;
		
    } 
    /*COMBO CUENTAS CONTABLES SEGUN BANCOS DEL NAF */
    public function listaCuentasBancoNafAction()
    {
        $request = $this->getRequest();
        $idBanco = $request->get("banco");
        $datos[]= array('idBanco'=>1,'idCuenta'=>1,'descripcion'=> '8057768');
        $datos[]= array('idBanco'=>2,'idCuenta'=>2,'descripcion'=> '8057769');
        $datos[]= array('idBanco'=>2,'idCuenta'=>3,'descripcion'=> '8057700');
        $datos[]= array('idBanco'=>3,'idCuenta'=>4,'descripcion'=> '8057701');
        
        $arreglo=array();
        
        foreach($datos as $dato):
            if($dato['idBanco']==$idBanco)
                $arreglo[]= array('idCuenta'=>$dato['idCuenta'],'descripcion'=> $dato['descripcion']);
        endforeach;

        $response = new Response(json_encode(array('cuentasBancosNaf'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
		
    } 
    
    /**
     * Documentación para el método 'procesar_ajaxAction'.
     * Este proceso registra el deposito como procesado y genera asiento contable
     * Actualizacion: Se genera asiento contable
     * @return object $response retorna ('success')
     *
     * @author amontero@telconet.ec
     * @version 1.2 19-01-2015
     * 
     * Se inicializa $objParametros y se envía como parámetro a la función contabilizarDeposito()
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.3 07-11-2019
     * @since 1.2
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 06-06-2022 - Se modifica para que procesamiento de depósito se realice mediante llamada a función desde un service.
    /**
    * @Secure(roles="ROLE_86-4118")
    */      
    public function procesar_ajaxAction() 
    {
        $objRequest   = $this->getRequest();
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objRespuesta->setContent("error del Form");
        $serviceInfoPagAut = $this->get('financiero.InfoPagoAutomatico');
      
        $arrayParametros                         = [];
        $arrayParametros['intIdDeposito']        = $objRequest->get('iddeposito');
        $arrayParametros['strReferencia']        = $objRequest->get('comprobante');
        $arrayParametros['strIpCreacion']        = $objRequest->getClientIp();
        $arrayParametros['strUsrCreacion']       = $objRequest->getSession()->get('user');
        $arrayParametros['strEmpresaCod']        = $objRequest->getSession()->get('idEmpresa');
        $arrayParametros['intOficinaId']         = $objRequest->getSession()->get('idOficina');        
        $arrayParametros['strPrefijoEmpresa']    = $objRequest->getSession()->get('prefijoEmpresa');
        $arrayParametros['strFechaProcesa']      = $objRequest->get("fechaprocesa");
        $arrayParametros['strEstado']            = 'Procesado';       
        $arrayParametros['boolDepManual']        = true;            

        $boolRespuesta = $serviceInfoPagAut->procesarDeposito($arrayParametros);

        if($boolRespuesta)
        {
            $objRespuesta->setContent("Se deposito los registros con exito.");
            $objResponse = new Response(json_encode(array('success'=>true)));
            $objResponse->headers->set('Content-type', 'text/json');               
        }
        else 
        {
            $objResponse = new Response(json_encode(array('success'=>false)));
            $objResponse->headers->set('Content-type', 'text/json');
        }

        return $objResponse;
    }    

    
    /**
     * Documentación para el método 'anular_ajaxAction'.
     * Este proceso permite anular un deposito
     * @return object $response retorna ('success')
     *
     * @author amontero@telconet.ec
     * Actualizacion: Se agrega perfiles a la opcion de anulacion
     * @version 1.1 01-01-2014
     */    
    /**
    * @Secure(roles="ROLE_86-4117")
    */  
    public function anular_ajaxAction() {
        $request = $this->getRequest();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $usuario=$request->getSession()->get('user');
        //echo $id;die;
        $respuesta->setContent("error del Form");
        $em = $this->getDoctrine()->getManager('telconet_financiero');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $idDeposito=$peticion->get('iddeposito');

        $em->getConnection()->beginTransaction();
        try {
            //foreach(){
                $entityDeposito=$em->getRepository('schemaBundle:InfoDeposito')->Find($idDeposito);
                $entityDeposito->setFeAnulado(new \DateTime('now'));
                $entityDeposito->setUsrAnula($usuario);
                $entityDeposito->setEstado('Anulado');
                $em->persist($entityDeposito);
                $em->flush();

                //GRABA DEPOSITO HISTORIAL
                $entityHistorial = new InfoDepositoHistorial();
                $entityHistorial->setIpCreacion($request->getClientIp());
                $entityHistorial->setFeCreacion(new \DateTime('now'));
                $entityHistorial->setDepositoId($entityDeposito);
                $entityHistorial->setUsrCreacion($usuario);                
                $entityHistorial->setEstado('Anulado');
                $em->persist($entityHistorial);
                $em->flush();                
            
                //$entityDeposito = new InfoDeposito();    
                $entityPago=$em->getRepository('schemaBundle:InfoPagoDet')->findByDepositoPagoId($idDeposito);
                foreach($entityPago as $pago){
                    $pago->setDepositado('N');
                    $pago->setDepositoPagoId(null);
                    $em->persist($pago);
                    $em->flush();                  
                }
            //}    
            $em->getConnection()->commit();
            $respuesta->setContent("Se deposito los registros con exito.");
            $response = new Response(json_encode(array('success'=>true)));
            $response->headers->set('Content-type', 'text/json');
            return $response;            
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            //$respuesta->setContent("error al tratar de eliminar registro. Consulte con el Administrador.");
            $respuesta->setContent($e->getMessage());
            $response = new Response(json_encode(array('success'=>false)));
            $response->headers->set('Content-type', 'text/json');
            return $response;            
        }
    }     
    
    public function listaOficinasAjaxAction()
    {
                $request = $this->getRequest();
        $empresaId=$request->getSession()->get('idEmpresa');
        $em = $this->getDoctrine()->getManager('telconet');
        $entities = $em->getRepository('schemaBundle:InfoOficinaGrupo')->findBy(array("estado" => 'Activo','empresaId'=>$empresaId));
        foreach($entities as $dato):
            $arreglo[]=array('id_oficina'=>$dato->getId(),'nombre_oficina'=>$dato->getNombreOficina());
        endforeach;
        $response = new Response(json_encode(array('encontrados'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
	return $response;
    }
    

    /**
    * Documentación para funcion 'excelPagosPorDepositoAction'.
    * genera archivo de excel con los pagos de los depositos generados
    * @param $intIdDeposito - id del deposito
    * @author <amontero@telconet.ec>
    * @since 06/01/2015
    * @return objeto para crear excel
    */ 
    /**
    * @Secure(roles="ROLE_86-4098")
    */    
	public function excelPagosPorDepositoAction()
    {
        
        $request       = $this->getRequest();
        $intIdDeposito = $request->get('intIdDeposito'); 
        $objPHPExcel   = new PHPExcel();
        $cacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Telcos")
            ->setLastModifiedBy("Telcos")
            ->setTitle("Documento Excel de Pagos")
            ->setSubject("Documento Excel de Pagos")
            ->setDescription("")
            ->setKeywords("Excel Office 2007 openxml php")
            ->setCategory("Excel");

        $styleArray = array(
            'font' => array(
                'bold' => true
            )
        );

        $emfn = $this->get('doctrine')->getManager('telconet_financiero');
        $pagos = $emfn->getRepository('schemaBundle:InfoDeposito')->findPagosPorDeposito($intIdDeposito);
        $i = 2;
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'TIPO_PAGO')
                    ->setCellValue('B1', 'CLIENTE')
                    ->setCellValue('C1', 'LOGIN')
                    ->setCellValue('D1', 'NUMERO_PAGO')
                    ->setCellValue('E1', 'VALOR')
                    ->setCellValue('F1', 'FACTURA')
                    ->setCellValue('G1', 'ESTADO_PAGO');
        
        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        foreach($pagos as $pago)
        {
            // Agregar Informacion
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $i, $pago['codigoTipoDocumento']);
            if($pago['puntoId'])
            {    
                $entityPunto = $emfn->getRepository('schemaBundle:InfoPunto')->find($pago['puntoId']);
            }    
            if($pago['referenciaId'])
            {    
                $entityFactura = $emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($pago['referenciaId']);
            }    
            if($entityPunto)
            {
                if($entityPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
                {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, $entityPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial());
                }
                else
                {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('B' . $i, $entityPunto->getPersonaEmpresaRolId()->getPersonaId()->getNombres()
                            . " " . $entityPunto->getPersonaEmpresaRolId()->getPersonaId()->getApellidos());
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, $entityPunto->getLogin());
            }
            //desencripta numero de cuenta
            /* @var $serviceCrypt \telconet\seguridadBundle\Service\CryptService */
            $serviceCrypt                     = $this->get('seguridad.Crypt');
            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, $pago['numeroPago']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $i, $pago['valorTotal']);

            if($entityFactura)
            {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $i, $entityFactura->getNumeroFacturaSri());
            }  
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, $pago['estadoPago']);
            $i++;
        }
        // Renombrar Hoja
        $objPHPExcel->getActiveSheet()->setTitle('Pagos por Deposito');
        // Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
        $objPHPExcel->setActiveSheetIndex(0);
        // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_pagos_por_debito.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }    
    
}
