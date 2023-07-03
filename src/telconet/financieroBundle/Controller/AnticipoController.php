<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Form\InfoPagoCabType;

use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Form\InfoPagoDetType;
use telconet\financieroBundle\Controller\InfoPagoDetController;
use telconet\financieroBundle\Controller\InfoPagoCabController;
use telconet\contabilizacionesBundle\Controller\AnticiposController;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell;
/**
 * InfoPagoCab controller.
 *
 */
class AnticipoController extends Controller implements TokenAuthenticatedController
{
    
    public function listAnticipoSinClienteAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $request = $this->getRequest();
        $session  = $request->getSession();
        $ptoCliente_sesion=$session->get('ptoCliente');
        $cliente_sesion=$session->get('cliente'); 
        //print_r($cliente_sesion);
        //$entities = $em->getRepository('schemaBundle:InfoPagoCab')->findAll();
        return $this->render('financieroBundle:Anticipo:listanticiposincliente.html.twig', array(
            //'entities' => $entities,
            'ptoCliente' => $ptoCliente_sesion,
            'cliente' => $cliente_sesion
        ));
    }
    
    /**
     * Documentación para el método 'gridSinClienteAction'.
     * Este grid de consulta de anticipos sin cliente
     *
     * @return object $response
     *
     * @author amontero@telconet.ec
     * @version 1.1 22-01-2015
     * 
     * @author Edson Franco <efrancon@telconet.ec>
     * @version 1.2 19-07-2017 - Se agrega el parámetro 'strTipoDocumento' para buscar los pagos asociados al código del documento financiero
     *                           enviado en la consulta
     */      
    public function gridSinClientesAction() 
    {
        $request              = $this->getRequest();
        $empresaId            = $request->getSession()->get('idEmpresa');        
        $arrayFechaDesde      = $request->get("fechaDesde") ? explode('T', $request->get("fechaDesde")) : array( 0 => date("Y/m/d") );
        $strTipoDocumento     = $request->get("strTipoDocumento") ? $request->get("strTipoDocumento") : "ANTS";
        $fechaHasta           = explode('T', $request->get("fechaHasta"));
        $numeroPago           = $request->get("numeroPago");
        $numeroIdentificacion = $request->get("numeroIdentificacion");
        $numeroReferencia     = $request->get("numeroReferencia");
        $estado               = $request->get("estado");
        $limit                = $request->get("limit");
        $start                = $request->get("start");
        $em                   = $this->get('doctrine')->getManager('telconet_financiero');
        $parametros = array(
            'estado'               =>$estado,
            'empresaId'            =>$empresaId,    
            'puntoId'              => 0,                    
            'fechaDesde'           =>$arrayFechaDesde[0],
            'fechaHasta'           =>$fechaHasta[0],
            'limit'                =>$limit,
            'start'                =>$start,
            'numeroPago'           =>$numeroPago,
            'numeroIdentificacion' =>$numeroIdentificacion,
            'numeroReferencia'     =>$numeroReferencia,
            'strTipoDocumento'     => $strTipoDocumento);
        $resultado  = $em->getRepository('schemaBundle:InfoPagoCab')->findPagosPorCriterios($parametros);
        $datos      = $resultado['registros'];
        $total      = $resultado['total'];

        foreach ($datos as $datos)
        {    
            if ($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANTS')
            {    
                $urlVer = $this->generateUrl('anticipo_showsincliente', array('id' => $datos->getId()));
            }    
            $linkVer    = $urlVer;
            $comentario = $datos->getComentarioPago();            
            if($comentario == "" || $comentario == null)
            {
                $detallesPago = $em->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($datos->getId());
                if($detallesPago)
                {
                    foreach($detallesPago as $detallesPago)
                    {
                        $comentario=$comentario." ".$detallesPago->getComentario();
                    }
                }    
            }    
            $comentario= str_replace("Anticipo (ANTS) generado por ","",$comentario);            
            $urlEditar="";
            if($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANTS')
            {
                $urlEditar = $this->generateUrl('anticipo_edit', array('id' => $datos->getId()));
            }

            $pagoAplicaAnulacion=$em->getRepository('schemaBundle:InfoPagoDet')->checkPagostoAnular($datos->getId());

            $nombreCliente  = "";
            $identificacion = "";
            
            if ($datos->getRecaudacionDetId())
            {
                $nombreCliente  = $datos->getRecaudacionDetId()->getNombre();
                $identificacion = $datos->getRecaudacionDetId()->getIdentificacion();
            }    
            
            $arreglo[] = array(
                'id'                  => $datos->getId(),
                'tipo'                => $datos->getTipoDocumentoId()->getNombreTipoDocumento(),
                'numero'              => $datos->getNumeroPago(),
                'total'               => $datos->getValorTotal(),
                'fechaCreacion'       => strval(date_format($datos->getFeCreacion(), "d/m/Y G:i")),
                'usuarioCreacion'     => $datos->getUsrCreacion(),
                'estado'              => $datos->getEstadoPago(),
                'linkVer'             => $linkVer,
                'linkEditar'          => $urlEditar,
                'observacion'         => $comentario,
                'pagoAplicaAnulacion' => $pagoAplicaAnulacion ? $pagoAplicaAnulacion : 0,
                'cliente'             => $nombreCliente,
                'identificacion'      => $identificacion
            );
        }
        if (!empty($arreglo))
        {    
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }    
        else 
        {
            $arreglo[] = array();
            $response  = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }    
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial =$this->getDoctrine()->getManager('telconet');
        $entity = $em->getRepository('schemaBundle:InfoPagoCab')->find($id);
        
        $oficina= $em_comercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPagoCab entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('financieroBundle:Anticipo:edit.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'oficina' => $oficina));
    }
    
    public function ajaxActualizaDetAnticipoAction()
    {
        $request = $this->getRequest();
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');

        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $emGeneral = $this->get('doctrine')->getManager('telconet_general');
        $formaPago = $request->get('formaPago');
        $idPagoDet = $request->get('idPagoDet');
        $comentario = $request->get('comentario');

        $emFinanciero->getConnection()->beginTransaction();
        $emGeneral->getConnection()->beginTransaction();
        $seEdito = false;

        try
        {

            $infoPagoDet = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->find($idPagoDet);

            if($formaPago == 'CHEQUE')
            {
                $idBanco = $request->get('idBanco');
                $numeroReferencia = $request->get('numeroReferencia');
                $admiTipoCuenta = $emGeneral->getRepository('schemaBundle:AdmiTipoCuenta')
                    ->findOneBy(array('descripcionCuenta' => 'CORRIENTE', 'estado' => 'Activo'));
                $admiBancoTipoCuenta = $emGeneral->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                    ->findOneBy(array('bancoId' => $idBanco, 'tipoCuentaId' => $admiTipoCuenta->getId(), 'estado' => 'Activo'));

                if($idBanco)
                {
                    $infoPagoDet->setBancoTipoCuentaId($admiBancoTipoCuenta->getId());
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
                if($numeroReferencia)
                {
                    $infoPagoDet->setNumeroReferencia($numeroReferencia);
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
            }

            if($formaPago == 'DEPOSITO' || $formaPago == 'TRANSFERENCIA' ||
                $formaPago == 'DEPOSITO MESES ANTERIORES' ||
                $formaPago == 'TRANSFERENCIA MESES ANTERIORES')
            {
                $idBanco = $request->get('idBanco');
                $idTipoCuenta = $request->get('tipoCuenta');
                $numeroReferencia = $request->get('numeroReferencia');
                $fechaDeposito = explode('T', $request->get('fechaDeposito'));

                $dateF = explode("-", $fechaDeposito[0]);

                $fechaDepositoSql = date("Y/m/d", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0]));

                $admiBancoCtaContable = $emGeneral->getRepository('schemaBundle:AdmiBancoCtaContable')->find($idTipoCuenta);

                if($idTipoCuenta)
                {
                    $infoPagoDet->setBancoCtaContableId($admiBancoCtaContable->getId());
                    $infoPagoDet->setBancoTipoCuentaId($admiBancoCtaContable->getBancoTipoCuentaId()->getId());
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
                if($numeroReferencia)
                {
                    $infoPagoDet->setNumeroReferencia($numeroReferencia);
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
                if($fechaDepositoSql)
                {
                    $infoPagoDet->setFeDeposito(new \DateTime($fechaDepositoSql));
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
            }
            if($formaPago == 'RECAUDACION' || $formaPago == 'DEBITO BANCARIO')
            {
                $fechaDeposito = explode('T', $request->get('fechaDeposito'));
                $numeroReferencia = $request->get('numeroReferencia');

                $dateF = explode("-", $fechaDeposito[0]);

                $fechaDepositoSql = date("Y/m/d", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0]));
                if($fechaDepositoSql)
                {
                    $infoPagoDet->setFeDeposito(new \DateTime($fechaDepositoSql));
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
                if($numeroReferencia)
                {
                    $infoPagoDet->setNumeroReferencia($numeroReferencia);
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
            }
            if($formaPago == 'TARJETA DE CREDITO')
            {
                $idBanco = $request->get('idBanco');
                $idTipoCuenta = $request->get('tipoCuenta');
                $numeroReferencia = $request->get('numeroReferencia');

                $admiBancoCtaContable = $emGeneral->getRepository('schemaBundle:AdmiBancoCtaContable')->find($idTipoCuenta);

                if($idTipoCuenta)
                {
                    $infoPagoDet->setBancoCtaContableId($admiBancoCtaContable->getId());
                    $infoPagoDet->setBancoTipoCuentaId($admiBancoCtaContable->getBancoTipoCuentaId()->getId());
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
                if($numeroReferencia)
                {
                    $infoPagoDet->setNumeroReferencia($numeroReferencia);
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
            }

            if($formaPago == 'RETENCION FUENTE 2%' || $formaPago == 'RETENCION FUENTE 8%' 
                || $formaPago == 'RETENCION IVA 70%' || $formaPago == 'RETENCION IVA 100%')
            {
                $numeroReferencia = $request->get('numeroReferencia');

                if($numeroReferencia)
                {
                    $infoPagoDet->setNumeroReferencia($numeroReferencia);
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }
            }
            if($seEdito)
            {
                if($comentario)
                    $infoPagoDet->setComentario($comentario);

                $emFinanciero->persist($infoPagoDet);
                $emFinanciero->flush();


                $response->setContent('OK');
            }
            else
            {
                $response->setContent('No se actualizo el pago debido a que no hubieron cambios.');
            }
            $emFinanciero->getConnection()->commit();
            $emGeneral->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $emFinanciero->getConnection()->rollback();
            $emGeneral->getConnection()->rollback();

            $mensajeError = "Error: " . $e->getMessage();
            $response->setContent($mensajeError);
        }

        return $response;
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial =$this->getDoctrine()->getManager('telconet');
        $entity = $em->getRepository('schemaBundle:InfoPagoCab')->find($id);
        $punto= $em_comercial->getRepository('schemaBundle:InfoPunto')->find($entity->getPuntoId());
        $oficina= $em_comercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPagoCab entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        //Obtener el historial
        $serviceInfoPago = $this->get('financiero.InfoPago');
        $historial=$serviceInfoPago->obtenerHistorialPago($entity->getId()); 
        return $this->render('financieroBundle:Anticipo:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'punto' => $punto,
            'oficina' => $oficina,
            'historial'=>$historial
        ));
    }    


    public function showSinClienteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial =$this->getDoctrine()->getManager('telconet');
        $entity = $em->getRepository('schemaBundle:InfoPagoCab')->find($id);
        $oficina= $em_comercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPagoCab entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        //Obtener el historial
        $serviceInfoPago = $this->get('financiero.InfoPago');
        $historial=$serviceInfoPago->obtenerHistorialPago($entity->getId());

        return $this->render('financieroBundle:Anticipo:showsincliente.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'oficina' => $oficina,
            'historial'=>$historial
        ));
    }     
    
    /**
     * @Secure(roles="ROLE_68-2")
     * 
     * Documentación para el método 'newAction'.
     * 
     * Este presenta el twig para el ingreso de nuevo anticipo
     * 
     * Actualizacion: Se incluye prefijo de empresa para leerlo en el twig y aplicar validaciones por empresa
     * @version 1.1 amontero@telconet.ec 10-06-2016
     * @author amontero@telconet.ec
     * @return object render financieroBundle:Anticipo:new.html.twig 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 09-08-2017 - Se verifica si la empresa contabiliza para presentar únicamente las formas de pago que tienen asociado una plantilla
     *                           contable.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 06-10-2017 - Se agrega el tipo de documento para la consulta de las plantillas asociadas a la contabilidad.
     */
    public function newAction()
    {
        //obtiene de sesion los datos
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $ptoCliente_sesion = $session->get('ptoCliente');
        $em                = $this->getDoctrine()->getManager('telconet');
        $emFinanciero      = $this->getDoctrine()->getManager('telconet_financiero');
        $entity            = new InfoPagoCab();
        $form              = $this->createForm(new InfoPagoCabType(), $entity);
        $idOficina         = $request->getSession()->get('idOficina'); 
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $strCodEmpresa     = $request->getSession()->get('idEmpresa');
        $idPunto           = null;
        $punto             = null;
        if($ptoCliente_sesion)
        {
            $idPunto=$ptoCliente_sesion['id'];
        }

        /**
         * Bloque que verifica si la empresa en sessión contabiliza para cargar las formas de pago asociadas a una plantilla contable
         */
        $arrayParametroDet= $em->getRepository('schemaBundle:AdmiParametroDet')
                               ->getOne( "PROCESO CONTABILIZACION EMPRESA", 
                                         "FINANCIERO",
                                         "",
                                         "",
                                         $strPrefijoEmpresa,
                                         "",
                                         "",
                                         "");

        $arrayFormasPago = $em->getRepository('schemaBundle:AdmiFormaPago')->findFormasPagoActivas();

        if ( isset($arrayParametroDet["valor2"]) && $arrayParametroDet["valor2"] == "S" )
        {
            $arrayParametrosFormasPago = array( 'arrayEstado'      => array('Activo'), 
                                                'strEmpresaCod'    => $strCodEmpresa,
                                                'strTipoDocumento' => 'ANT');
            $arrayFormasPago           = $emFinanciero->getRepository('schemaBundle:AdmiFormaPago')
                                                      ->findFormasPagoContabilizables($arrayParametrosFormasPago);
        }

        $tipoCuenta = $em->getRepository('schemaBundle:AdmiTipoCuenta')->findOneByDescripcionCuenta('CORRIENTE');
        if($idPunto)
        {    
            $punto= $em->getRepository('schemaBundle:InfoPunto')->find($idPunto);
        }    
        $oficina= $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($idOficina);        
        return $this->render('financieroBundle:Anticipo:new.html.twig', array(
            'entity'            => $entity,
            'form'              => $form->createView(),
            'formasPago'        => $arrayFormasPago,
            'tipoCuenta'        => $tipoCuenta,
            'punto'             => $punto,
            'oficina'           => $oficina,
            'strPrefijoEmpresa' => $strPrefijoEmpresa            
        ));
    }
    
    
    /**
     * @Secure(roles="ROLE_68-28")
     * 
     * Documentación para el método 'newSinClienteAction'
     * 
     * Método que renderiza la vista del formulario para crear el anticipo sin cliente.
     * 
     * @version 1.0 Versión Inicial
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 28-09-2016 - Se envía como parámetro el prefijo de la empresa en sessión
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 09-08-2017 - Se verifica si la empresa contabiliza para presentar únicamente las formas de pago que tienen asociado una plantilla
     *                           contable.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 06-10-2017 - Se agrega el tipo de documento para la consulta de las plantillas asociadas a la contabilidad.
     */
    public function newSinClienteAction()
    {
        $request           = $this->getRequest();
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $entity            = new InfoPagoCab();
        $form              = $this->createForm(new InfoPagoCabType(), $entity);
        $tipoCuenta        = $emComercial->getRepository('schemaBundle:AdmiTipoCuenta')->findOneByDescripcionCuenta('CORRIENTE');
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $strCodEmpresa     = $request->getSession()->get('idEmpresa');
        $emFinanciero      = $this->getDoctrine()->getManager('telconet_financiero');

        /**
         * Bloque que verifica si la empresa en sessión contabiliza para cargar las formas de pago asociadas a una plantilla contable
         */
        $arrayParametroDet= $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne( "PROCESO CONTABILIZACION EMPRESA", 
                                                  "FINANCIERO",
                                                  "",
                                                  "",
                                                  $strPrefijoEmpresa,
                                                  "",
                                                  "",
                                                  "");

        $arrayFormasPago = $emComercial->getRepository('schemaBundle:AdmiFormaPago')->findFormasPagoActivas();

        if ( isset($arrayParametroDet["valor2"]) && $arrayParametroDet["valor2"] == "S" )
        {
            $arrayParametrosFormasPago = array( 'arrayEstado'      => array('Activo'), 
                                                'strEmpresaCod'    => $strCodEmpresa,
                                                'strTipoDocumento' => 'ANTS' );
            $arrayFormasPago           = $emFinanciero->getRepository('schemaBundle:AdmiFormaPago')
                                                      ->findFormasPagoContabilizables($arrayParametrosFormasPago);
        }

        return $this->render('financieroBundle:Anticipo:newsincliente.html.twig', array( 'entity'            => $entity,
                                                                                         'form'              => $form->createView(),
                                                                                         'formasPago'        => $arrayFormasPago,
                                                                                         'tipoCuenta'        => $tipoCuenta,
                                                                                         'strPrefijoEmpresa' => $strPrefijoEmpresa ) );
    }
    
    
    /**
     * Documentación para el método 'validacionCreacionAnticipoAction'.
     * 
     * Método que valida si se puede crear un anticipo dependiendo de su forma de pago ingresada.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-03-2017
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 07-04-2017 - Se valida mediante el siguiente parámetro 'VALIDACIONES_PAGOS_ANTICIPOS' si se debe validar la creación de anticipos
     *                           por ciertas formas de pago.
     * 
     * @return Response $objResponse
     */ 
    public function validacionCreacionAnticipoAction()
    {
        $objResponse          = new Response();
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();
        $strUsuarioCreacion   = $objSession->get('user');
        $strIpCreacion        = $objRequest->getClientIp();
        $strCodEmpresa        = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa    = $objRequest->getSession()->get('prefijoEmpresa');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $strTipoDocumento     = $objRequest->query->get('strTipoDocumento') ? $objRequest->query->get('strTipoDocumento') : '';
        $strDatosFormaPagoDet = $objRequest->query->get('strDatosFormaPagoDet') ? $objRequest->query->get('strDatosFormaPagoDet') : '';
        $arrayDetallesPago    = ( !empty($strDatosFormaPagoDet) ) ? explode('|', $strDatosFormaPagoDet) : '';
        $serviceInfoPagoDet   = $this->get('financiero.InfoPagoDet');
        $serviceUtil          = $this->get('schema.Util');
        $strMensaje           = 'OK';
        $arrayParametroDet    = array();

        try
        {
            //SE VERIFICA SI LA EMPRESA TIENE LA RESTRINCCION DE NO CREAR ANTICIPOS POR CIERTAS FORMAS DE PAGO
            $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne( "VALIDACIONES_PAGOS_ANTICIPOS", 
                                                     "FINANCIERO", 
                                                     "PAGOS",
                                                     "",
                                                     $strTipoDocumento, 
                                                     $strPrefijoEmpresa,
                                                     "",
                                                     "",
                                                     "",
                                                     $strCodEmpresa );

            if( !empty($arrayParametroDet) )
            {
                if( isset($arrayParametroDet["valor3"]) && $arrayParametroDet["valor3"] == "S" )
                {
                    /**
                     * Bloque que valida la creación del anticipo
                     */
                    $arrayParametrosValidacion = array( 'strTipoDocumento'  => $strTipoDocumento,
                                                        'arrayDetallesPago' => $arrayDetallesPago );

                    $strMensaje = $serviceInfoPagoDet->validacionPagosAnticipos($arrayParametrosValidacion);
                }//( isset($arrayParametroDet["valor3"]) && $arrayParametroDet["valor3"] == "S" )
            }//( !empty($arrayParametroDet) )
        }
        catch(\Exception $e)
        {
            $strMensaje = "Hubo un problema al validar la creación del anticipo";
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'AnticipoController.validacionCreacionAnticipoAction',
                                       'Error al validar la creación del anticipo. - '.$e->getMessage(),
                                       $strUsuarioCreacion,
                                       $strIpCreacion );
        }
        
        $objResponse->setContent($strMensaje);
        
        return $objResponse;
    }
    
    
    /**
     * Documentación para el método 'createAction'.
     * Este ingresa anticipo manual para un punto cliente
     * Actualizacion: Se incluye campos para ingresar al detalle del pago la 
     * cuenta bancaria de la empresa para poder obtener la cuenta contable
     * @return redireciona a anticipo_show
     *
     * @author amontero@telconet.ec
     * @version 1.2 19-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 28-09-2016 - Se obtiene la variable 'strTipoFormaPago' del detalle de pago para guardar el detalle respectivo.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 08-12-2016 - Se cambia la variable '$arrPagosDetIdContabilidad' para que reciba como arreglo la variable 
     *                           '$arrayResultadoIngresoDetallesPago'
     * 
     * Se inicializa $objParametros y se envía como parámetro a la función contabilizarPagosAnticipo()
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.5 07-11-2019
     * @since 1.4
     */ 
    public function createAction(Request $request)
    {
        //obtiene de sesion los datos
        $objRequest                = $this->getRequest();
        $floatValorCabeceraPago    = 0;
        $objInfoPagoCab            = new InfoPagoCab();
        $intEmpresaId              = $objRequest->getSession()->get('idEmpresa');
        $intOficinaId              = $objRequest->getSession()->get('idOficina');
        $strPrefijoEmpresa         = $request->getSession()->get('prefijoEmpresa');        
        $strUsuarioCreacion        = $objRequest->getSession()->get('user');
        $arrDatosFormPagoCab       = $objRequest->request->get('infopagocabtype');
        $arrDatosFormPagoDet       = $objRequest->request->get('infopagodettype');
        $intPuntoId                = $arrDatosFormPagoCab['idpunto'];
        $arrDetallesPago           = explode('|', $arrDatosFormPagoDet['detalles']);
        $emFinanciero              = $this->getDoctrine()->getManager('telconet_financiero');
        $emComercial               = $this->getDoctrine()->getManager();
        $arrPagosDetIdContabilidad = array();
        $strMsnErrorContabilidad   = '';         
        $emFinanciero->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();
        $serviceInfoPago    = $this->get('financiero.InfoPago');
        $serviceInfoPagoDet = $this->get('financiero.InfoPagoDet');        
        try
        {
            //CABECERA DEL PAGO-->>*************//
            //**********************************//            
            $objInfoPagoCab->setEmpresaId($intEmpresaId);
            $objInfoPagoCab->setEstadoPago('Pendiente');
            $objInfoPagoCab->setFeCreacion(new \DateTime('now'));

            //Obtener la numeracion de la tabla Admi_numeracion
            $datosNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                ->findByEmpresaYOficina($intEmpresaId, $intOficinaId, "ANT");
            $secuencia_asig = str_pad($datosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
            $numero_de_pago = $datosNumeracion->getNumeracionUno() . "-" . 
                $datosNumeracion->getNumeracionDos() . "-" . $secuencia_asig;
            //Actualizo la numeracion en la tabla
            $numero_act = ($datosNumeracion->getSecuencia() + 1);
            $datosNumeracion->setSecuencia($numero_act);
            $emComercial->persist($datosNumeracion);
            $emComercial->flush();

            $entityAdmiTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                ->findOneByCodigoTipoDocumento('ANT');
            $objInfoPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
            $objInfoPagoCab->setNumeroPago($numero_de_pago);
            $objInfoPagoCab->setOficinaId($intOficinaId);
            $objInfoPagoCab->setPuntoId($intPuntoId);
            $objInfoPagoCab->setUsrCreacion($strUsuarioCreacion);
            $objInfoPagoCab->setValorTotal($floatValorCabeceraPago);
            $emFinanciero->persist($objInfoPagoCab);
            $emFinanciero->flush();

            //Ingresa historial para el pago
            $serviceInfoPago->ingresaHistorialPago($objInfoPagoCab, 'Pendiente', 
                new \DateTime('now'), $strUsuarioCreacion, null, 'Anticipo creado en forma manual');
            //<<--FIN CABECERA DEL PAGO***************//

            
            //DETALLES DEL PAGO-->>*************//
            //**********************************//
            $arr_anticipo = array();
            for($i = 0; $i < count($arrDetallesPago); $i++)
            {                               
                if($arrDetallesPago[$i])
                {
                    $pos = strpos($arrDetallesPago[$i], ',');
                    if($pos == 0)
                    {    
                        $arrDetallesPago[$i] = substr_replace($arrDetallesPago[$i], '', $pos, 1);
                    }    
                    $detalles = explode(',', $arrDetallesPago[$i]);
                    
                    $arrayDetallePago = array(  'idFormaPago'               => $detalles[0],
                                                'descripcionFormaPago'      => $detalles[1],
                                                'idFactura'                 => null,
                                                'numeroFactura'             => null,
                                                'idBanco'                   => $detalles[4],
                                                'descripcionBanco'          => $detalles[5],
                                                'idTipoCuenta'              => $detalles[6],
                                                'descripcionTipoCuenta'     => $detalles[7],
                                                'numeroReferencia'          => $detalles[8],
                                                'valorPago'                 => $detalles[9],
                                                'comentario'                => $detalles[10],
                                                'fechaDeposito'             => $detalles[11],
                                                'codigoDebito'              => $detalles[12],
                                                'cuentaContableId'          => $detalles[14],
                                                'descripcionCuentaContable' => $detalles[13],
                                                'numeroDocumento'           => $detalles[15],
                                                'strTipoFormaPago'          => $detalles[16] );   
                    
                    //Se crea detalle del anticipo
                    $arrayResultadoIngresoDetallesPago= $serviceInfoPagoDet->agregarDetallePago(
                        $objInfoPagoCab,$arrayDetallePago,new \DateTime('now'),$floatValorCabeceraPago);
                    $arr_anticipo[]              = $arrayResultadoIngresoDetallesPago['arr_anticipo'];                   
                    $floatValorCabeceraPago      = $arrayResultadoIngresoDetallesPago['valorCabeceraPago'];  
                    $arrPagosDetIdContabilidad[] = $arrayResultadoIngresoDetallesPago;                     
                }
            }
            
            //Se setea valor total de cabecera y hago persistencia
            $objInfoPagoCab->setValorTotal($floatValorCabeceraPago);
            $emFinanciero->persist($objInfoPagoCab);
            $emFinanciero->flush();
            
            
            //<<--FIN DETALLES DEL PAGO***************//
            $emFinanciero->getConnection()->commit();
            $emComercial->getConnection()->commit();
            
            
            //CONTABILIZA DETALLES DE PAGOS Y ANTICIPOS
            $arrayParametroDet= $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
            //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
            if ($arrayParametroDet["valor2"]=="S")
            {      
                $objParametros['serviceUtil'] = $this->get('schema.Util');  
                //contabiliza detalles del pago y anticipos          
                $strMsnErrorContabilidad=
                    $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->contabilizarPagosAnticipo($intEmpresaId, 
                    $arrPagosDetIdContabilidad, 
                    $objParametros);              
                $emFinanciero->getConnection()->close();
                
            }
            
            return $this->redirect($this->generateUrl('anticipo_show', array(
                'id'                     => $objInfoPagoCab->getId())));
        }
        catch(\Exception $e)
        {
            $entity = new InfoPagoCab();
            $emFinanciero->getConnection()->rollback();
            $emFinanciero->getConnection()->close();
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $form       = $this->createForm(new InfoPagoCabType(), $entity);
            $formasPago = $em->getRepository('schemaBundle:AdmiFormaPago')->findFormasPagoActivas();
            $tipoCuenta = $em->getRepository('schemaBundle:AdmiTipoCuenta')->findOneByDescripcionCuenta('CORRIENTE');
            return $this->render('financieroBundle:Anticipo:new.html.twig', array(
                    'entity'      => $entity,
                    'form'        => $form->createView(),
                    'formasPago'  => $formasPago,
                    'tipoCuenta'  => $tipoCuenta,
                    'error'       => $e->getMessage()
            ));
        }
    }

    /**
     * Documentación para el método 'grabaSinClienteAction'.
     * Este ingresa anticipo sin cliente manual
     * Actualizacion: Se incluye campos para ingresar al detalle del pago la 
     * cuenta bancaria de la empresa para poder obtener la cuenta contable 
     * y se usa el service para ingresar detalles
     * @return redireciona a anticipo_showsincliente
     *
     * @author amontero@telconet.ec
     * @version 1.2 26-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 28-09-2016 - Se obtiene la variable 'strTipoFormaPago' del detalle de pago para guardar el detalle respectivo.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 08-12-2016 - Se cambia la variable '$arrPagosDetIdContabilidad' para que reciba como arreglo la variable 
     *                           '$arrayResultadoIngresoDetallesPago'
     * 
     * Se inicializa $objParametros y se envía como parámetro a la función contabilizarPagosAnticipo()
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.5 07-11-2019
     * @since 1.4
     */     
    public function grabaSinClienteAction(Request $request)
    {
        //obtiene de sesion los datos
        $objRequest                = $this->getRequest();
        $floatValorCabeceraPago    = 0;        
        $objInfoPagoCab            = new InfoPagoCab();
        $intEmpresaId              = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa         = $request->getSession()->get('prefijoEmpresa');        
        $intOficinaId              = $objRequest->getSession()->get('idOficina');
        $strUsuarioCreacion        = $objRequest->getSession()->get('user');
        $datos_form_pagocab        = $objRequest->request->get('infopagocabtype');
        $datos_form_pagodet        = $objRequest->request->get('infopagodettype');
        $arrDetallesPago           = explode('|', $datos_form_pagodet['detalles']);

        $emFinanciero              = $this->getDoctrine()->getManager('telconet_financiero');
        $emComercial               = $this->getDoctrine()->getManager();
        $serviceInfoPago           = $this->get('financiero.InfoPago');
        $serviceInfoPagoDet        = $this->get('financiero.InfoPagoDet');
        $arrPagosDetIdContabilidad = array();
        $strMsnErrorContabilidad   = '';           
        
        $emFinanciero->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();
        
        try
        {
            //CABECERA DEL PAGO-->>*************//
            //**********************************//            
            $objInfoPagoCab->setEmpresaId($intEmpresaId);
            $objInfoPagoCab->setEstadoPago('Pendiente');
            $objInfoPagoCab->setFeCreacion(new \DateTime('now'));

            //Obtener la numeracion de la tabla Admi_numeracion
            $datosNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                ->findByEmpresaYOficina($intEmpresaId, $intOficinaId, "ANT");
            $secuencia_asig = str_pad($datosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
            $numero_de_pago = $datosNumeracion->getNumeracionUno() . "-" . 
                $datosNumeracion->getNumeracionDos() . "-" . $secuencia_asig;
            //Actualizo la numeracion en la tabla
            $numero_act = ($datosNumeracion->getSecuencia() + 1);
            $datosNumeracion->setSecuencia($numero_act);
            $emComercial->persist($datosNumeracion);
            $emComercial->flush();

            $entityAdmiTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                ->findOneByCodigoTipoDocumento('ANTS');
            $objInfoPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
            $objInfoPagoCab->setNumeroPago($numero_de_pago);
            $objInfoPagoCab->setOficinaId($intOficinaId);
            $objInfoPagoCab->setUsrCreacion($strUsuarioCreacion);
            $objInfoPagoCab->setValorTotal($datos_form_pagocab['valorTotal']);
            $emFinanciero->persist($objInfoPagoCab);
            $emFinanciero->flush();
            //<<--FIN CABECERA DEL PAGO***************//
            //Ingresa historial para el pago
            $serviceInfoPago->ingresaHistorialPago($objInfoPagoCab, 'Pendiente', new \DateTime('now'), 
                $strUsuarioCreacion, null, 'Anticipo sin cliente creado en forma manual');


            //DETALLES DEL PAGO-->>*************//
            //**********************************//
            $arr_anticipo = array();
            for($i = 0; $i < count($arrDetallesPago); $i++)
            {
                if($arrDetallesPago[$i])
                {
                    $pos = strpos($arrDetallesPago[$i], ',');
                    if($pos == 0)
                    {    
                        $arrDetallesPago[$i] = substr_replace($arrDetallesPago[$i], '', $pos, 1);
                    }    
                    $detalles = explode(',', $arrDetallesPago[$i]);
                    
                    $arrayDetallePago = array(  'idFormaPago'              => $detalles[0],
                                                'descripcionFormaPago'     => $detalles[1],
                                                'idFactura'                => null,
                                                'numeroFactura'            => null,
                                                'idBanco'                  => $detalles[4],
                                                'descripcionBanco'         => $detalles[5],
                                                'idTipoCuenta'             => $detalles[6],
                                                'descripcionTipoCuenta'    => $detalles[7],
                                                'numeroReferencia'         => $detalles[8],
                                                'valorPago'                => $detalles[9],
                                                'comentario'               => $detalles[10],
                                                'fechaDeposito'            => $detalles[11],
                                                'codigoDebito'             => $detalles[12],
                                                'cuentaContableId'         => $detalles[14],
                                                'descripcionCuentaContable'=> $detalles[13],
                                                'numeroDocumento'          => $detalles[15],
                                                'strTipoFormaPago'         => $detalles[16] ); 
                    
                    //Se crea detalle del pago
                    $arrayResultadoIngresoDetallesPago= $serviceInfoPagoDet->agregarDetallePago(
                        $objInfoPagoCab,$arrayDetallePago,new \DateTime('now'),$floatValorCabeceraPago);
                    $arr_anticipo[]              = $arrayResultadoIngresoDetallesPago['arr_anticipo'];                   
                    $floatValorCabeceraPago      = $arrayResultadoIngresoDetallesPago['valorCabeceraPago'];       
                    $arrPagosDetIdContabilidad[] = $arrayResultadoIngresoDetallesPago;                     
                }
            }
            
            //Se setea valor total de cabecera y hago persistencia
            $objInfoPagoCab->setValorTotal($floatValorCabeceraPago);
            $emFinanciero->persist($objInfoPagoCab);
            $emFinanciero->flush();            
            
            //<<--FIN DETALLES DEL PAGO***************//
            $emFinanciero->getConnection()->commit();
            $emComercial->getConnection()->commit();
            
            //CONTABILIZA DETALLES DE PAGOS Y ANTICIPOS 
            $arrayParametroDet= $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
            //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
            if ($arrayParametroDet["valor2"]=="S")
            {   
                $objParametros['serviceUtil'] = $this->get('schema.Util');  
                //contabiliza detalles del pago y anticipos           
                $strMsnErrorContabilidad=
                    $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->contabilizarPagosAnticipo($intEmpresaId, 
                    $arrPagosDetIdContabilidad, 
                    $objParametros);      
            }
            return $this->redirect($this->generateUrl('anticipo_showsincliente', array(
                'id'                     => $objInfoPagoCab->getId(),
                'strMsnErrorContabilidad'=>$strMsnErrorContabilidad)));
        }
        catch(\Exception $e)
        {
            $entity = new InfoPagoCab();
            $emFinanciero->getConnection()->rollback();
            $emFinanciero->getConnection()->close();
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $form       = $this->createForm(new InfoPagoCabType(), $entity);
            $formasPago = $emComercial->getRepository('schemaBundle:AdmiFormaPago')->findFormasPagoActivas();
            $tipoCuenta = $emComercial->getRepository('schemaBundle:AdmiTipoCuenta')->findOneByDescripcionCuenta('CORRIENTE');
            return $this->render('financieroBundle:Anticipo:newsincliente.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'formasPago' => $formasPago,
                    'tipoCuenta' => $tipoCuenta,
                    'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Deletes a InfoPagoCab entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoPagoCab')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoPagoCab entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infopagocab'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    
    public function getDetallesPago_ajaxAction($id) {
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $em1 = $this->get('doctrine')->getManager('telconet');
        $banco='';$tipoCuenta='';$referencia='';
        $resultado = $em->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($id);
        foreach ($resultado as $datos):
            $banco='';$tipoCuenta='';$referencia='';
            if ($datos->getNumeroCuentaBanco())
                $referencia=$datos->getNumeroCuentaBanco();
            elseif($datos->getNumeroReferencia())
                $referencia=$datos->getNumeroReferencia();
            if($datos->getBancoTipoCuentaId()){
                $entityBancoTipoCuenta=$em1->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($datos->getBancoTipoCuentaId());
                if ($entityBancoTipoCuenta){
                   $banco=$entityBancoTipoCuenta->getBancoId()->getDescripcionBanco();
                   $tipoCuenta=$entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
                }
            }
	    if($datos->getFeDeposito())
		$fechaDeposito = strval(date_format($datos->getFeDeposito(), "d/m/Y"));
	    else
		$fechaDeposito = "Sin dato";             
            $entityFactura=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($datos->getReferenciaId());
            $entityFormaPago=$em1->getRepository('schemaBundle:AdmiFormaPago')->find($datos->getFormaPagoId());
            $arreglo[] = array(
                'id' => $datos->getId(),
                'formaPago' => $entityFormaPago->getDescripcionFormaPago(),
                'factura' => $entityFactura->getNumeroFacturaSri(),
                'banco' => $banco,
                'tipoCuenta' => $tipoCuenta,
                'referencia' => $referencia,
		'valor'=> $datos->getValorPago(),
                'feDeposito'=>$fechaDeposito,                
                'comentario' => $datos->getComentario()
                );
        endforeach;
        //print_r($arreglo);die;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('detalles' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('detalles' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
 
 	public function verificaRetencion_ajaxAction()
	{
		$request = $this->getRequest();
        $em = $this->get('doctrine')->getManager('telconet_financiero');	
		$idFactura = trim($request->request->get("fact"));  
	 
		$obj = $em->getRepository('schemaBundle:InfoPagoDet')->findPagoDetRetencionPorPago($idFactura);
	 
		if(!$obj){
			$response = "no";
		}else{
			$response = "si";
		}
	 
		return new Response($response);
	} 

        
    public function getFacturasPendientes_ajaxAction() {
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $request = $this->getRequest();
        $session  = $request->getSession();
        $peticion = $this->get('request');
        $ptoCliente_sesion=$session->get('ptoCliente');
        if($peticion->get('idpto'))
            $idPunto=$peticion->get('idpto');            
        else
            $idPunto=$ptoCliente_sesion['id'];
        
        $facturas=null;
        if($idPunto){
            $facturas = $em->getRepository('schemaBundle:InfoPagoCab')->findFacturasPendientesPorPunto($idPunto);
        }
        
        if($facturas)
        {
			foreach ($facturas as $datos):
				$arreglo[] = array(
					'idfactura' => $datos->getId(),
					'numero' => $datos->getNumeroFacturaSri()
					);
			endforeach;
		}
        if (!empty($arreglo))
            $response = new Response(json_encode(array('facturas' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('facturas' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    

        
    public function cruzarAnticipoUnaFactura_ajaxBackAction() {
        $request=$this->getRequest();    
        $usuario=$request->getSession()->get('user');
        $empresaId=$request->getSession()->get('idEmpresa');
        $oficinaId=$request->getSession()->get('idOficina');
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial= $this->getDoctrine()->getManager('telconet');
        $peticion = $this->get('request');
        $idAnticipo=$peticion->get('idanticipo');
        $idFactura=$peticion->get('idfactura');
        $idPersona=$peticion->get('idcliente');
        $idPunto=$peticion->get('idpunto');
        $em->getConnection()->beginTransaction();
        $em_comercial->getConnection()->beginTransaction();
        try {
                $entityPagoCab=$em->getRepository('schemaBundle:InfoPagoCab')->find($idAnticipo);
                $entityPagoCab->setFeUltMod(new \DateTime());
		$entityPagoCab->setFeCruce(new \DateTime());
                $entityPagoCab->setEstadoPago('Cerrado');
                if($idPunto)
                    $entityPagoCab->setPuntoId($idPunto);   
                $entityPagoCab->setUsrUltMod($usuario);
		$entityPagoCab->setUsrCruce($usuario);
                $valorAnticipo=$entityPagoCab->getValorTotal();
                $em->persist($entityPagoCab);
                $em->flush();

                $entityPagoDet=$em->getRepository('schemaBundle:InfoPagoDet')->findOneByPagoId($idAnticipo);                
                $entityPagoDet->setReferenciaId($idFactura);
                $entityPagoDet->setFeUltMod(new \DateTime());
                $entityPagoDet->setUsrUltMod($usuario);
                $entityPagoDet->setEstado('Cerrado');
                $em->persist($entityPagoDet);
                $em->flush();
                                            
                //SE VERIFICA SI EL PAGO YA CUBRE LA FACTURA y SI ES ASI CREA ARREGLO ANTICIPOs
                //$arr_anticipo=array();
                $arr_total_pagos_fact=array();
                $arr_total_pagos_fact=$em->getRepository('schemaBundle:InfoPagoCab')->findTotalPagosPorFacturaDifAnticipo($idFactura,$idAnticipo);
                $entityFactura=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idFactura);
                $arr_total_pagos_fact['total_pagos']=$arr_total_pagos_fact['total_pagos']*1;
                //print_r($arr_total_pagos_fact);die;            
                $faltaPorPagar=0;
                if($entityFactura->getValorTotal()>$arr_total_pagos_fact['total_pagos']){
                $faltaPorPagar=$entityFactura->getValorTotal()-$arr_total_pagos_fact['total_pagos'];
                }
                //echo $faltaPorPagar;die;
                if($faltaPorPagar<=$valorAnticipo){                        
                    $entityFactura->setEstadoImpresionFact('Cerrado');
                    $em->persist($entityFactura);
                    $em->flush();
                }
                $InfoPagoCabController=new InfoPagoCabController();
                $InfoPagoCabController->setContainer($this->container);
			
            $tieneServicios=false;		 
				//echo "punto".$idPunto;die;
			$valor=$InfoPagoCabController->obtieneSaldoPorPunto($idPunto);
			$serviciosParaReactivar="";
							//echo 'hola11';die;
			//SI NO TIENE DEUDA REACTIVAR SERVICIO
			if($valor<=0){
				//echo $valor;die;
				$serviciosInactivos=$em_comercial->getRepository('schemaBundle:InfoServicio')->findServiciosPorEstadoPorPuntos($idPunto,'Pendiente');
				foreach($serviciosInactivos as $servicio){
					$serviciosParaReactivar=$serviciosParaReactivar.$servicio->getId()."|";
					$tieneServicios=true;
				}
				if($tieneServicios){
					$response=new Response(json_encode(
					array('success'=>true,'idpago'=>$entityPagoCab->getId(),'msg'=>"cerrar-conservicios",'servicios'=>$serviciosParaReactivar,
					'link'=>$this->generateUrl('infopagocab_show', array('id' =>$entityPagoCab->getId())))));
				}else
				{
					$response=new Response(json_encode(
					array('success'=>true,'idpago'=>$entityPagoCab->getId(),'msg'=>"cerrar-sinservicios",'servicios'=>'',
					'link'=>$this->generateUrl('infopagocab_show', array('id' =>$entityPagoCab->getId())))));				
				}
			}else
			{
				$response=new Response(json_encode(
				array('success'=>true,'idpago'=>$entityPagoCab->getId(),'msg'=>"nocerrar",'servicios'=>'',
				'link'=>$this->generateUrl('infopagocab_show', array('id' =>$entityPagoCab->getId())))));	
			}	
   
            $em->getConnection()->commit();
            $em_comercial->getConnection()->commit();
            $response->headers->set('Content-type', 'text/json');
            return $response;            
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $em_comercial->getConnection()->rollback();
            $em_comercial->getConnection()->close();    
            $response = new Response(json_encode(array('success'=>false,    'errors' =>array('error' => $e->getMessage()))));
            $response->headers->set('Content-type', 'text/json');
            return $response;            
        }
    }    


    /**
     * @Secure(roles="ROLE_68-1459")
     * 
     * Actualizacion: Se modifica para el caso de cruce de un anticipo con valor mayor al saldo de una factura
     * Para el caso de Telconet que cree pagos tipo PAGC y ANTC con los mismos detalles que tiene el anticipo original
     * Tambien se registra asiento contable para el tipo PAGC (contabilizarCruceAnticipo)
     *
     * @return object $response retorna ('success' | 'idpago' | 'msg' | 'servicios' | 'link')
     *
     * @author amontero@telconet.ec
     * @version 1.1 11-11-2014
     * @author Andres Montero H. <amontero@telconet.ec>
     * @version 1.2 05/08/2016 - Documentación para el método 'cruzarAnticipoUnaFactura_ajaxAction'.
     *                           Esta funcion permite realizar el cruce de un pago con una factura
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 03-08-2017 - Se envía el parámetro 'strEmpresaCod' al método 'contabilizarCruceAnticipo'.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 04-04-2017 - Se agrega validación permitir el cruce de un anticipo sólo cuando se encuentre en estado Pendiente.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.5
     * @since 19-09-2018
     * Se agrega la creación del historial de la factura cuando cambia a estado Cerrado por un anticipo del mismo valor de la factura.
     * Se agrega la observación en el historial de la factura cuando cambia a estado Cerrado por un anticipo mayor al valor de la factura.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.6
     * @since 12-11-2018
     * Se agrega excepción cuando el proceso de contabilizarCruceAnticipo falla porque no tiene una plantilla contable.
     */
    public function cruzarAnticipoUnaFactura_ajaxAction() 
    {
        $request              = $this->getRequest();    
        $usuario              = $request->getSession()->get('user');
        $strEmpresaId         = $request->getSession()->get('idEmpresa');
        $oficinaId            = $request->getSession()->get('idOficina');
        $prefijoEmpresa       = $request->getSession()->get('prefijoEmpresa');        
        $em                   = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial         = $this->getDoctrine()->getManager('telconet');
        $peticion             = $this->get('request');
        $idAnticipo           = $peticion->get('idanticipo');
        $idFactura            = $peticion->get('idfactura');
        $idPersona            = $peticion->get('idcliente');
        $idPunto              = $peticion->get('idpunto');
        $usuarioCreacion      = $request->getSession()->get('user');
        $serviceInfoPago      = $this->get('financiero.InfoPago');
        $serviceInfoPagoDet   = $this->get('financiero.InfoPagoDet');
        $serviceProcesoMasivo = $this->get('tecnico.ProcesoMasivo');        
        $em->getConnection()->beginTransaction();
        $em_comercial->getConnection()->beginTransaction();
        try 
        {
            $entityPagoCab=$em->getRepository('schemaBundle:InfoPagoCab')->find($idAnticipo);
          
            if( is_object($entityPagoCab) && (strcmp($entityPagoCab->getEstadoPago(),'Pendiente')!== 0))
            {
                $strMsj = 'Documento ya ha sido procesado. Favor verificar.';
                throw new \Exception($strMsj);
            }

            $entityPagoCab->setFeUltMod(new \DateTime());
            $entityPagoCab->setFeCruce(new \DateTime());
            if($idPunto)
            {    
                $entityPagoCab->setPuntoId($idPunto);
            }    
            $entityPagoCab->setUsrUltMod($usuario);
            $entityPagoCab->setUsrCruce($usuario);
            $valorAnticipo = $entityPagoCab->getValorTotal();

            $pagosDet      = $em->getRepository('schemaBundle:InfoPagoDet')->findBy(array('pagoId'=>$idAnticipo),array('valorPago'=>'DESC'));
               
            //SE VERIFICA SI EL PAGO YA CUBRE LA FACTURA y SI ES ASI CREA ARREGLO ANTICIPOs
            $entityFactura         = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idFactura);     
            $arrayParametrosSend   = 
                array('intIdDocumento'  => $entityFactura->getId(), 
                      'intReferenciaId' => ''
                );
            //Obtiene el saldo de la factura
            $arrayGetSaldoXFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                        ->getSaldosXFactura($arrayParametrosSend);
           
            if(!empty($arrayGetSaldoXFactura['strMessageError']))
            {
                throw new Exception('Error al calcular el saldo de factura: '. $entityFactura->getNumeroFacturaSri());
            }
            else
            {
                $saldoFactura=$arrayGetSaldoXFactura['intSaldo'];
            } 
            
            //CONTABILIZA DETALLES DE PAGO
            $arrayParametroDet= $em_comercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $prefijoEmpresa, "", "", "");            
            
            if(round($valorAnticipo,2)==round($saldoFactura,2))
            {
                $entityPagoCab->setEstadoPago('Cerrado');
                //Ingresa historial para el pago
                $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Cerrado',new \DateTime('now'),$usuario,null,
                    'Cierre de Anticipo por cruce con factura #'.$entityFactura->getNumeroFacturaSri().
                    '. Saldo Factura Igual al valor del anticipo.');

                foreach($pagosDet as $entityPagoDet)
                {
                    $entityPagoDet->setEstado('Cerrado');                    
                    $entityPagoDet->setReferenciaId($idFactura);
                    $entityPagoDet->setFeUltMod(new \DateTime());
                    $entityPagoDet->setUsrUltMod($usuario);
                    $em->persist($entityPagoDet);                        
                }
                $entityFactura->setEstadoImpresionFact('Cerrado');
                $em->persist($entityFactura);
                $em->flush();

                //Se crea el historial de la factura.
                $objInfoDocumentoHistorial = new InfoDocumentoHistorial();
                $objInfoDocumentoHistorial->setDocumentoId($entityFactura);
                $objInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoDocumentoHistorial->setUsrCreacion($usuario);
                $objInfoDocumentoHistorial->setEstado($entityFactura->getEstadoImpresionFact());
                $objInfoDocumentoHistorial->setObservacion("Se cierra la factura por cruce de anticipo por el mismo valor del saldo de la factura.");
                $em->persist($objInfoDocumentoHistorial);
                $em->flush();

                //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
                if ($arrayParametroDet["valor2"]=="S")
                {
                    $arrayParametrosCruceAnticipos                  = array();
                    $arrayParametrosCruceAnticipos['intIdPagoCab']  = $entityPagoCab->getId();
                    $arrayParametrosCruceAnticipos['strEmpresaCod'] = $strEmpresaId;

                    $strMsnErrorContabilidad = $em->getRepository('schemaBundle:InfoPagoDet')
                                                  ->contabilizarCruceAnticipo($arrayParametrosCruceAnticipos);
                    
                    if($strMsnErrorContabilidad!='[Proceso contable OK]')
                    {
                        throw new \Exception($strMsnErrorContabilidad);
                    }
                }
            }
            elseif(round($valorAnticipo,2)<=round($saldoFactura,2))
            {
                $entityPagoCab->setEstadoPago('Cerrado');
                //Ingresa historial para el anticipo
                $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Cerrado',new \DateTime('now'),$usuario,null,
                    'Cierre de Anticipo por cruce con factura #'.$entityFactura->getNumeroFacturaSri().
                    '. Saldo Factura mayor al valor del anticipo.');

                foreach($pagosDet as $entityPagoDet)
                {
                    $entityPagoDet->setEstado('Cerrado');                    
                    $entityPagoDet->setReferenciaId($idFactura);
                    $entityPagoDet->setFeUltMod(new \DateTime());
                    $entityPagoDet->setUsrUltMod($usuario);
                    $em->persist($entityPagoDet);                        
                }
                
                //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
                if ($arrayParametroDet["valor2"]=="S")
                {
                    $arrayParametrosCruceAnticipos                  = array();
                    $arrayParametrosCruceAnticipos['intIdPagoCab']  = $entityPagoCab->getId();
                    $arrayParametrosCruceAnticipos['strEmpresaCod'] = $strEmpresaId;

                    $strMsnErrorContabilidad = $em->getRepository('schemaBundle:InfoPagoDet')
                                                  ->contabilizarCruceAnticipo( $arrayParametrosCruceAnticipos );
                    
                    if($strMsnErrorContabilidad!='[Proceso contable OK]')
                    {
                        throw new \Exception($strMsnErrorContabilidad);
                    }
                }
            }
            else
            {
                //Valor anticipo mayor al valor de la factura 
                //crea los pagos por el cruce del anticipo
                $saldoPago=round($valorAnticipo,2)-round($saldoFactura,2);
                $entityPagoCab->setEstadoPago('Asignado');
                $entityPagoCab->setPuntoId($entityFactura->getPuntoId());
                //mantiene el comentario y se modifica comentario si es recaudacion
                if($entityPagoCab->getRecaudacionId())
                {
                    //Ingresa historial para el anticipo
                    $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Asignado',new \DateTime('now'),$usuario,null,
                        "Asignación de ".$entityPagoCab->getComentarioPago());                        
                    $entityPagoCab->setComentarioPago("Asignación de ".$entityPagoCab->getComentarioPago());                        
                }
                else
                {
                    //Ingresa historial para el anticipo
                    $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Asignado',new \DateTime('now'),$usuario,null,
                        'Se asigna Anticipo por cruce con factura #'.$entityFactura->getNumeroFacturaSri().
                        '. Saldo Factura menor al valor del anticipo.');                   
                }                

                foreach($pagosDet as $entityPagoDet)
                {
                    $entityPagoDet->setEstado('Asignado');                    
                    $entityPagoDet->setReferenciaId($idFactura);                   
                    $entityPagoDet->setFeUltMod(new \DateTime());
                    $entityPagoDet->setUsrUltMod($usuario);
                    $em->persist($entityPagoDet);
                }

                //CREA CABECERA PAGO
                $entityAdmiFormaPagoCruce=$em_comercial->getRepository('schemaBundle:AdmiFormaPago')
                    ->findOneByCodigoFormaPago('CR');                    
                $entityPagoCabClonado=new InfoPagoCab();
                $entityPagoCabClonado= clone $entityPagoCab;
                $entityPagoCabClonado->setEstadoPago('Cerrado');
                //asigna tipo de documento al pago (PAG)
                $entityAdmiTipoDocumento=$em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                    ->findOneByCodigoTipoDocumento('PAGC');
                $entityPagoCabClonado->setTipoDocumentoId($entityAdmiTipoDocumento);
                $entityPagoCabClonado->setComentarioPago("Pago creado por cruce con anticipo #".
                    $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());

                //Obtener la numeracion de la tabla Admi_numeracion
                $datosNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')
                                                ->findByEmpresaYOficina($strEmpresaId,$oficinaId,'PAGC');
                $secuencia_asig='';
                $secuencia_asig=str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT); 
                $numero_de_pago=$datosNumeracion->getNumeracionUno()."-".
                    $datosNumeracion->getNumeracionDos()."-".$secuencia_asig;
                //Actualizo la numeracion en la tabla
                $numero_act=($datosNumeracion->getSecuencia()+1);
                $datosNumeracion->setSecuencia($numero_act);
                $em_comercial->persist($datosNumeracion);

                $entityPagoCabClonado->setPuntoId($entityFactura->getPuntoId());
                $entityPagoCabClonado->setValorTotal(round($saldoFactura,2));
                $entityPagoCabClonado->setNumeroPago($numero_de_pago);
                $entityPagoCabClonado->setFeCreacion(new \DateTime());
                $entityPagoCabClonado->setUsrCreacion($usuario);
                $entityPagoCabClonado->setAnticipoId($entityPagoCab->getId());
                $em->persist($entityPagoCabClonado);

                //CREA DETALLE PAGO		
                if ($prefijoEmpresa == 'MD')
                {    
                    $entityPagoDetClonado = new InfoPagoDet();
                    $entityPagoDetClonado = clone $pagosDet[0];
                    $entityPagoDetClonado->setPagoId($entityPagoCabClonado);
                    $entityPagoDetClonado->setFeCreacion(new \DateTime());
                    $entityPagoDetClonado->setUsrCreacion($usuario);
                    $entityPagoDetClonado->setEstado('Cerrado');
                    $entityPagoDetClonado->setComentario("Pago creado por cruce con anticipo #".
                                                         $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());						
                    $entityPagoDetClonado->setReferenciaId($entityFactura->getId());
                    $entityPagoDetClonado->setValorPago(round($saldoFactura,2)); 
                    $entityPagoDetClonado->setFormaPagoId($entityAdmiFormaPagoCruce->getId());
                    $em->persist($entityPagoDetClonado);

                }
                else
                {
                    //Se optiene los detalles del anticipo original que sumados se acercan mas al valor del saldo de la factura
                    //ej: saldo factura: 50 pago 001 => det1:20 det2:10 det3:5 det4:20 
                    //pagos mas cercanos (optimos) => det1:20, det4:20 y det2:10
                    $arrayDetallePagosSegmentados=$serviceInfoPago->buscaValoresOptimos($pagosDet,round($saldoFactura,2));                    
                    //INGRESA LOS DETALLES DEL PAGO
                    $intSumaDetallesPago = 0;
                    foreach($arrayDetallePagosSegmentados['optimos'] as $entityPagoDetOptimo)
                    {
                        $intSumaDetallesPago = $intSumaDetallesPago + $entityPagoDetOptimo->getValorPago();
                        $intValorDiferencia  = $intSumaDetallesPago - $saldoFactura;
                        if (round($intSumaDetallesPago,2) <=  round($saldoFactura,2))
                        {
                            $arrayParametros['intValorPago'] = $entityPagoDetOptimo->getValorPago();
                        }
                        else
                        {
                            $arrayParametros['intValorPago'] = round($entityPagoDetOptimo->getValorPago() - $intValorDiferencia,2);
                        }    

                        $arrayParametros['entityPagoDetClonado'] = $entityPagoDetOptimo;
                        $arrayParametros['strComentario']        = 
                            "Pago creado por cruce con anticipo #".$entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago();
                        $arrayParametros['strUsuario']           = $usuario;
                        $arrayParametros['entityInfoPagoCab']    = $entityPagoCabClonado;
                        $arrayParametros['strEstado']            = 'Cerrado'; 
                        $arrayParametros['strClonar']            = 'S'; 
                        $arrayParametros['intReferenciaId']      = $entityFactura->getId();
                        //CREA LOS DETALLES DEL ANTICIPO
                        $objInfoPagoDet = $serviceInfoPagoDet->ingresaDetalleAnticipoClonado($arrayParametros); 
                        $em->persist($objInfoPagoDet);
                        $em->flush();
                    }
                    //CONTABILIZA PAGO CREADO POR CRUCE
                    $arrayParametrosCruceAnticipos                  = array();
                    $arrayParametrosCruceAnticipos['intIdPagoCab']  = $entityPagoCabClonado->getId();
                    $arrayParametrosCruceAnticipos['strEmpresaCod'] = $strEmpresaId;
                    
                    $strMsnErrorContabilidad = $em->getRepository('schemaBundle:InfoPagoDet')
                                                  ->contabilizarCruceAnticipo( $arrayParametrosCruceAnticipos );
                    
                     if($strMsnErrorContabilidad!='[Proceso contable OK]')
                    {
                        throw new \Exception($strMsnErrorContabilidad);
                    }
                }    
                   
                    
                //Ingresa historial para el anticipo
                $serviceInfoPago->ingresaHistorialPago(
                    $entityPagoCabClonado,'Cerrado',new \DateTime('now'),$usuario,null,
                    "Pago creado por cruce con anticipo No:".$entityPagoCab->getNumeroPago());

                //CAMBIA ESTADO DE LA FACTURA A CERRADO
                $entityFactura->setEstadoImpresionFact('Cerrado');
                $em->persist($entityFactura);
                $em->flush();

                //Graba historial de la factura
                $historialFactura=new InfoDocumentoHistorial();
                $historialFactura->setDocumentoId($entityFactura);
                $historialFactura->setEstado($entityFactura->getEstadoImpresionFact());
                $historialFactura->setFeCreacion(new \DateTime('now'));
                $historialFactura->setObservacion("Se cierra la factura por cruce de anticipo mayor al saldo de la factura");
                $historialFactura->setUsrCreacion($usuario);
                $em->persist($historialFactura);                     
                $em->flush();

                //SE CREA LA CABECERA DEL ANTICIPO
                $entityAnticipoCab  = new InfoPagoCab();			
                $tipoDocumento='ANTC';
                $entityAnticipoCab->setPuntoId($entityPagoCab->getPuntoId());
                //SI SE ENCONTRO PUNTO ENTONCES GRABA ANTICIPO
                $entityAnticipoCab->setEmpresaId($strEmpresaId);
                $entityAnticipoCab->setEstadoPago('Pendiente');
                $entityAnticipoCab->setFeCreacion(new \DateTime('now'));
                $entityAnticipoCab->setAnticipoId($entityPagoCab->getId());
                //Obtener la numeracion de la tabla Admi_numeracion
                $datosNumeracionAnticipo = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')
                                                        ->findByEmpresaYOficina($strEmpresaId,$oficinaId,"ANTC");
                $secuencia_asig='';
                $secuencia_asig=str_pad($datosNumeracionAnticipo->getSecuencia(),7, "0", STR_PAD_LEFT); 
                $numero_de_anticipo=$datosNumeracionAnticipo->getNumeracionUno()."-".
                    $datosNumeracionAnticipo->getNumeracionDos()."-".$secuencia_asig;
                //Actualizo la numeracion en la tabla
                $numero_act=($datosNumeracionAnticipo->getSecuencia()+1);
                $datosNumeracionAnticipo->setSecuencia($numero_act);
                $em_comercial->persist($datosNumeracionAnticipo);
                $em_comercial->flush();

                $entityAdmiTipoDocumento=$em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                ->findOneByCodigoTipoDocumento($tipoDocumento);

                $entityAnticipoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                $entityAnticipoCab->setNumeroPago($numero_de_anticipo);
                $entityAnticipoCab->setOficinaId($oficinaId);
                $entityAnticipoCab->setUsrCreacion($usuario);
                $entityAnticipoCab->setValorTotal($saldoPago);
                if($entityPagoCab->getRecaudacionId()){
                    $entityAnticipoCab->setRecaudacionId($entityPagoCab->getRecaudacionId());
                }
                $entityAnticipoCab->setComentarioPago("Anticipo creado por cruce con anticipo No:".
                    $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());
                $em->persist($entityAnticipoCab);
                    //CREA LOS DETALLES DEL ANTICIPO
                if ($prefijoEmpresa == 'MD')
                {    
                    $entityAnticipoDet = new InfoPagoDet();
                    $entityAnticipoDet->setEstado('Pendiente');
                    if($pagosDet[0]->getFeDeposito()){
                        $entityAnticipoDet->setFeDeposito($pagosDet[0]->getFeDeposito());
                    }
                    $entityAnticipoDet->setFeCreacion(new \DateTime('now'));
                    $entityAnticipoDet->setUsrCreacion($usuario);
                    $entityAnticipoDet->setValorPago($saldoPago);
                    $entityAnticipoDet->setBancoTipoCuentaId($entityPagoDetClonado->getBancoTipoCuentaId());					
                    $entityAnticipoDet->setComentario("Anticipo creado por cruce con anticipo No:".
                                                      $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());
                    $entityAnticipoDet->setDepositado($entityPagoDetClonado->getDepositado());
                    $entityAnticipoDet->setDepositoPagoId($entityPagoDetClonado->getDepositoPagoId());
                    $entityAnticipoDet->setNumeroCuentaBanco($entityPagoDetClonado->getNumeroCuentaBanco());                    
                    $entityAnticipoDet->setPagoId($entityAnticipoCab);
                    $entityAnticipoDet->setFormaPagoId($entityAdmiFormaPagoCruce->getId());
                    $em->persist($entityAnticipoDet);                  
                }
                else
                {
                    if ($intValorDiferencia>0)
                    {                
                        $arrayParametros['entityPagoDetClonado'] = $entityPagoDetOptimo;
                        $arrayParametros['strComentario']        = "Anticipo creado por cruce con anticipo No:".
                                                                   $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago();
                        $arrayParametros['intValorPago']         = $intValorDiferencia;
                        $arrayParametros['strUsuario']           = $usuario;
                        $arrayParametros['entityInfoPagoCab']    = $entityAnticipoCab;
                        $arrayParametros['strEstado']            = 'Pendiente'; 
                        $arrayParametros['strClonar']            = 'N'; 
                        //CREA LOS DETALLES DEL ANTICIPO
                        $objInfoPagoDet = $serviceInfoPagoDet->ingresaDetalleAnticipoClonado($arrayParametros);
                        $em->persist($objInfoPagoDet);
                        $em->flush();
                    }
                    foreach($arrayDetallePagosSegmentados['noOptimos'] as $entityPagoDetNoOptimo)
                    {
                        $arrayParametros['entityPagoDetClonado'] = $entityPagoDetNoOptimo;
                        $arrayParametros['strComentario']        = "Anticipo creado por cruce con anticipo No:".
                                                                   $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago();
                        $arrayParametros['strUsuario']           = $usuario;
                        $arrayParametros['intValorPago']         = null;
                        $arrayParametros['intReferenciaId']      = null;
                        $arrayParametros['entityInfoPagoCab']    = $entityAnticipoCab;
                        $arrayParametros['strEstado']            = 'Pendiente'; 
                        $arrayParametros['strClonar']            = 'S'; 
                        //CREA LOS DETALLES DEL ANTICIPO
                        $objInfoPagoDet = $serviceInfoPagoDet->ingresaDetalleAnticipoClonado($arrayParametros);
                        $em->persist($objInfoPagoDet);
                        $em->flush();                        
                    }                    
                }    
                //Ingresa historial para el anticipo
                $serviceInfoPago->ingresaHistorialPago(
                    $entityAnticipoCab,'Pendiente',new \DateTime('now'),$usuario,null,
                    "Anticipo creado por cruce con anticipo No:".$entityPagoCab->getNumeroPago());

            }
            $em->persist($entityPagoCab);
            $em->flush(); 
            $em_comercial->flush();

            $em->getConnection()->commit();
            $em_comercial->getConnection()->commit();            
            
            //REACTIVA SERVICIOS
            $arrayParams=array(
            'puntos'          => array($idPunto),
            'prefijoEmpresa'  => $prefijoEmpresa,
            'empresaId'       => $strEmpresaId,
            'oficinaId'       => $oficinaId,
            'usuarioCreacion' => $usuarioCreacion,    
            'ip'              => $request->getClientIp(),
            'idPago'          => $entityPagoCab->getId(),
            'debitoId'        => null    
            );
            $string_msg=$serviceProcesoMasivo->reactivarServiciosPuntos($arrayParams);	
            
            $response = new Response(json_encode(array( 'success' => true,
                                                        'idpago'  => $entityPagoCab->getId(),
                                                        'msg'     => $string_msg,
                                                        'link'    => $this->generateUrl( 'infopagocab_show', array('id' => $entityPagoCab->getId()))
                                                      )));

            $response->headers->set('Content-type', 'text/json');
            return $response;            
        } 
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive()) 
            {              
                $em->getConnection()->rollback();
            }    
            if ($em_comercial->getConnection()->isTransactionActive()) 
            {              
                $em_comercial->getConnection()->rollback();
            }
            $em->getConnection()->close();            
            $em_comercial->getConnection()->close(); 
            error_log($e->getMessage());
            $response = new Response(json_encode(array('success'=>false,    'errors' =>array('error' => $e->getMessage()))));
            $response->headers->set('Content-type', 'text/json');
            return $response;            
        }
    }     
    

        
    
    /**
     * Documentación para el método 'cruzarAnticipo_ajaxAction'.
     * Esta funcion permite realizar el cruce de un pago con una factura o mas facturas
     *
     * @return object $response retorna ('success' | 'idpago' | 'msg' | 'servicios' | 'link')
     *
     * @author amontero@telconet.ec
     * @version 1.1 11-11-2014
     * 
     * @author llindao@telconet.ec
     * @version 1.2 06-01-2018 Se agrega llamada procedimiento contabilización Asigna Punto a Pago Anticipo sin cliente
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.3
     * @since 19-09-2018
     * Se agrega observación para el historial del documento.
     *
     */     
    /**
    * @Secure(roles="ROLE_68-1458")
    */
    public function cruzarAnticipo_ajaxAction() 
    {
        $request              = $this->getRequest();    
        $usuario              = $request->getSession()->get('user');
        $empresaId            = $request->getSession()->get('idEmpresa');
        $oficinaId            = $request->getSession()->get('idOficina');
        $em                   = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial         = $this->getDoctrine()->getManager('telconet');
        $peticion             = $this->get('request');
        $idAnticipo           = $peticion->get('idanticipo');
        $prefijoEmpresa       = $request->getSession()->get('prefijoEmpresa');
        $usuarioCreacion      = $request->getSession()->get('user');        
        $idFactura            = $peticion->get('idfactura');
        $facturas             = $peticion->get('idfacturas');
        $arrFacturas          = explode(",",$facturas);
        $idPersona            = $peticion->get('idcliente');
        $idPunto              = $peticion->get('idpunto');
        $serviceInfoPago      = $this->get('financiero.InfoPago');
        $serviceProcesoMasivo = $this->get('tecnico.ProcesoMasivo');      
        $em->getConnection()->beginTransaction();
        $em_comercial->getConnection()->beginTransaction();
        $strPrefijoEmpresa             = $request->getSession()->get('prefijoEmpresa');        
        $arrayParametrosAsignaAntPunto = array();
        try
        {
            $entityPagoCab=$em->getRepository('schemaBundle:InfoPagoCab')->find($idAnticipo);
            $entityPagoCab->setFeUltMod(new \DateTime());
            $entityPagoCab->setUsrUltMod($usuario);
            $entityPagoCab->setFeCruce(new \DateTime());
            $entityPagoCab->setUsrCruce($usuario);				
            $valorAnticipo=$entityPagoCab->getValorTotal();

            $entityPagoDet=$em->getRepository('schemaBundle:InfoPagoDet')->findOneByPagoId($idAnticipo);                 
            $entityPagoDet->setFeUltMod(new \DateTime());
            $entityPagoDet->setUsrUltMod($usuario);

            $arrpuntos=array();				

            //SI SOLO CRUZA UNA FACTURA
            if(count($arrFacturas)==2)
            {
                $entityFactura         = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($arrFacturas[0]);     
                $arrayParametrosSend   = array('intIdDocumento'  => $entityFactura->getId(), 'intReferenciaId' => '');
                //Obtiene el saldo de la factura
                $arrayGetSaldoXFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                    ->getSaldosXFactura($arrayParametrosSend);
                if(!empty($arrayGetSaldoXFactura['strMessageError']))
                {
                    throw new Exception('Error al calcular el saldo de factura: '. $entityFactura->getNumeroFacturaSri());
                }
                else
                {
                    $saldo=$arrayGetSaldoXFactura['intSaldo'];
                }  
                //SI EL PAGO ES MENOR ENTONCES SOLO CIERRA PAGO
                if($entityPagoCab->getValorTotal() < $saldo )
                {                                        
                    $entityPagoCab->setEstadoPago('Cerrado');
                    $entityPagoDet->setEstado('Cerrado');	
                    $entityPagoCab->setPuntoId($entityFactura->getPuntoId());
                    $entityPagoDet->setReferenciaId($entityFactura->getId());
                    //mantiene el comentario actual pero se agrega la fecha solo si es anticipo de recaudacion                    
                    if($entityPagoCab->getRecaudacionId())
                    {    
                        $entityPagoCab->setComentarioPago($entityPagoCab->getComentarioPago().", fecha:".
                            strval(date_format($entityPagoCab->getFeCreacion(),'Y-m-d H:i:s')));
                    }                    
                    //Ingresa historial para en el pago
                    $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Cerrado',new \DateTime('now'),$usuario,null,
                        'Cierre de Anticipo por cruce con factura #'.$entityFactura->getNumeroFacturaSri().
                        '. Saldo Factura mayor al valor del anticipo.');     
                }
                //SI PAGO ES == ENTONCES CIERRA PAGO Y FACTURA
                elseif($entityPagoCab->getValorTotal() == $saldo )
                {        
                    $entityPagoCab->setEstadoPago('Cerrado');
                    $entityPagoDet->setEstado('Cerrado');	
                    $entityPagoCab->setPuntoId($entityFactura->getPuntoId());
                    //si es recaudacion se modifica el comentario
                    if($entityPagoCab->getRecaudacionId())
                    {    
                        $entityPagoCab->setComentarioPago($entityPagoCab->getComentarioPago().", fecha:".
                        strval(date_format($entityPagoCab->getFeCreacion(),'Y-m-d H:i:s')));
                    }                    
                    $entityPagoDet->setReferenciaId($entityFactura->getId());                                        
                    //CAMBIA ESTADO DE LA FACTURA A CERRADO
                    $entityFactura->setEstadoImpresionFact('Cerrado');
                    $em->persist($entityFactura);
                    $em->flush();

                    $arrpuntos[]=	$entityFactura->getPuntoId();				

                    //Graba historial de la factura
                    $historialFactura=new InfoDocumentoHistorial();
                    $historialFactura->setDocumentoId($entityFactura);
                    $historialFactura->setEstado($entityFactura->getEstadoImpresionFact());
                    $historialFactura->setObservacion("Se cierra la factura por cruce de anticipo sin cliente por valor total "
                            .                         " del saldo de la factura.");
                    $historialFactura->setFeCreacion(new \DateTime('now'));
                    $historialFactura->setUsrCreacion($usuario);
                    $em->persist($historialFactura);
                    $em->flush();
                    //Ingresa historial para en el pago
                    $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Cerrado',new \DateTime('now'),$usuario,null,
                        'Cierre de Anticipo por cruce con factura #'.$entityFactura->getNumeroFacturaSri().
                        '. Saldo Factura igual al valor del anticipo.');                    
                }
                //SI EL PAGO ES MAYOR ENTONCES ASIGNA ANTICIPO , CREA PAGO Y CIERRA FACTURA
                elseif ($entityPagoCab->getValorTotal() > $saldo)
                {
                    $saldoPago=$entityPagoCab->getValorTotal() - $saldo;
                    $entityPagoCab->setEstadoPago('Asignado');
                    $entityPagoDet->setEstado('Asignado');
                    $entityPagoCab->setPuntoId($entityFactura->getPuntoId());
                    //si es recaudacion se ingresa historial con comentario actual y se modifica comentario
                    if($entityPagoCab->getRecaudacionId())
                    {    
                        //Ingresa historial para el anticipo
                        $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Asignado',new \DateTime('now'),$usuario,null,
                            "Asignación de ".$entityPagoCab->getComentarioPago().", fecha:".
                            strval(date_format($entityPagoCab->getFeCreacion(),'Y-m-d H:i:s')));                        
                        $entityPagoCab->setComentarioPago("Asignación manual de ".$entityPagoCab->getComentarioPago().", fecha:".
                        strval(date_format($entityPagoCab->getFeCreacion(),'Y-m-d H:i:s')));                        
                    }
                    else
                    {    
                        //Ingresa historial para el anticipo
                        $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Asignado',new \DateTime('now'),$usuario,null,
                            'Se asigna Anticipo por cruce con factura #'.$entityFactura->getNumeroFacturaSri().
                            '. Saldo Factura menor al valor del anticipo.');                    
                    }
                    //CREA CABECERA PAGO
                    $entityAdmiFormaPagoCruce=$em_comercial->getRepository('schemaBundle:AdmiFormaPago')
                        ->findOneByCodigoFormaPago('CR');                    
                    $entityPagoCabClonado=new InfoPagoCab();
                    $entityPagoCabClonado= clone $entityPagoCab;
                    $entityPagoCabClonado->setEstadoPago('Cerrado');
                    //asigna tipo de documento al pago (PAG)
                    $entityAdmiTipoDocumento=$em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                    ->findOneByCodigoTipoDocumento('PAGC');
                    $entityPagoCabClonado->setTipoDocumentoId($entityAdmiTipoDocumento);
                    $entityPagoCabClonado->setComentarioPago("Pago creado por cruce manual con anticipo No:".
                        $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());

                    //Obtener la numeracion de la tabla Admi_numeracion
                    $datosNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')
                    ->findByEmpresaYOficina($empresaId,$oficinaId,'PAGC');
                    $secuencia_asig='';$secuencia_asig=str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT); 
                    $numero_de_pago=$datosNumeracion->getNumeracionUno()."-".
                        $datosNumeracion->getNumeracionDos()."-".$secuencia_asig;
                    //Actualizo la numeracion en la tabla
                    $numero_act=($datosNumeracion->getSecuencia()+1);
                    $datosNumeracion->setSecuencia($numero_act);
                    $em_comercial->persist($datosNumeracion);

                    $entityPagoCabClonado->setPuntoId($entityFactura->getPuntoId());
                    $entityPagoCabClonado->setValorTotal(round($saldo,2));
                    $entityPagoCabClonado->setNumeroPago($numero_de_pago);
                    $entityPagoCabClonado->setFeCreacion(new \DateTime());
                    $entityPagoCabClonado->setUsrCreacion($usuario);
                    $entityPagoCabClonado->setAnticipoId($entityPagoCab->getId());
                    $em->persist($entityPagoCabClonado);

                    //CREA DETALLE PAGO		
                    $entityPagoDetClonado=new InfoPagoDet();
                    $entityPagoDetClonado= clone $entityPagoDet;
                    $entityPagoDetClonado->setPagoId($entityPagoCabClonado);
                    $entityPagoDetClonado->setFeCreacion(new \DateTime());
                    $entityPagoDetClonado->setUsrCreacion($usuario);
                    $entityPagoDetClonado->setEstado('Cerrado');
                    $entityPagoDetClonado->setComentario("Pago creado por cruce manual con anticipo No:".
                        $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());						
                    $entityPagoDetClonado->setReferenciaId($entityFactura->getId());
                    $entityPagoDetClonado->setValorPago(round($saldo,2));
                    $entityPagoDetClonado->setFormaPagoId($entityAdmiFormaPagoCruce->getId());
                    $em->persist($entityPagoDetClonado);                                        

                    //Ingresa historial para el anticipo
                    $serviceInfoPago->ingresaHistorialPago($entityPagoCabClonado,'Cerrado',
                        new \DateTime('now'),$usuario,null,
                        "Pago creado por cruce con anticipo No:".
                        $entityPagoCab->getNumeroPago());                    

                    //CAMBIA ESTADO DE LA FACTURA A CERRADO
                    $entityFactura->setEstadoImpresionFact('Cerrado');
                    $em->persist($entityFactura);
                    $em->flush();

                    $arrpuntos[] = $entityFactura->getPuntoId();				

                    //Graba historial de la factura
                    $historialFactura=new InfoDocumentoHistorial();
                    $historialFactura->setDocumentoId($entityFactura);
                    $historialFactura->setEstado($entityFactura->getEstadoImpresionFact());
                    $historialFactura->setObservacion("Se cierra la factura por cruce de anticipo sin cliente con valor"
                            .                         " total mayor al saldo de la factura.");
                    $historialFactura->setFeCreacion(new \DateTime('now'));
                    $historialFactura->setUsrCreacion($usuario);
                    $em->persist($historialFactura);
                    $em->flush();
                }
            }
            //SI CRUZA MAS DE UNA FACTURA
            else
            {
                $entityPagoCab->setEstadoPago('Asignado');
                $entityPagoDet->setEstado('Asignado');             
                //si es recaudacion se ingresa historial con comentario actual y se modifica comentario         
                if($entityPagoCab->getRecaudacionId())
                {    
                    //Ingresa historial para el anticipo
                    $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Asignado',new \DateTime('now'),$usuario,null,
                        "Asignación manual de ".$entityPagoCab->getComentarioPago().", fecha:".
                        strval(date_format($entityPagoCab->getFeCreacion(),'Y-m-d H:i:s')));                        
                    $entityPagoCab->setComentarioPago("Asignación manual de ".$entityPagoCab->getComentarioPago().", fecha:".
                        strval(date_format($entityPagoCab->getFeCreacion(),'Y-m-d H:i:s')));                        
                }
                else
                {    
                    //Ingresa historial para el anticipo
                    $serviceInfoPago->ingresaHistorialPago($entityPagoCab,'Asignado',new \DateTime('now'),$usuario,null,
                    'Se asigna Anticipo por cruce con mas de una factura.');                   
                }               

                                
                $saldoPago=$entityPagoCab->getValorTotal();
                foreach($arrFacturas as $factura)
                {
                    if($factura && $saldoPago>0)
                    {
                        $saldo=0;
                        //OBTIENE SALDO DE LA FACTURA
                        $entityFactura         = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($factura);                                                  
                        $arrayParametrosSend   = array('intIdDocumento'  => $entityFactura->getId(), 'intReferenciaId' => '');
                        //Obtiene el saldo de la factura
                        $arrayGetSaldoXFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                            ->getSaldosXFactura($arrayParametrosSend);
                        if(!empty($arrayGetSaldoXFactura['strMessageError']))
                        {
                            throw new Exception('Error al calcular el saldo de factura: '. $entityFactura->getNumeroFacturaSri());
                        }
                        else
                        {
                            $saldo=$arrayGetSaldoXFactura['intSaldo'];
                        }    
                        if($saldoPago>=$saldo)
                        {
                            $entityFactura->setEstadoImpresionFact('Cerrado');
                            $em->persist($entityFactura);                                                        
                            //Graba historial de la factura
                            $historialFactura=new InfoDocumentoHistorial();
                            $historialFactura->setDocumentoId($entityFactura);
                            $historialFactura->setEstado($entityFactura->getEstadoImpresionFact());
                            $historialFactura->setFeCreacion(new \DateTime('now'));
                            $historialFactura->setUsrCreacion($usuario);
                            $em->persist($historialFactura);						
                            $arrpuntos[]=	$entityFactura->getPuntoId();                                                        
                        } 

                        //CREA CABECERA PAGO	
                        $entityAdmiFormaPagoCruce=
                            $em_comercial->getRepository('schemaBundle:AdmiFormaPago')->findOneByCodigoFormaPago('CR');                    
                        $entityPagoCabClonado=new InfoPagoCab();
                        $entityPagoCabClonado= clone $entityPagoCab;
                        $entityPagoCabClonado->setEstadoPago('Cerrado');
                        //asigna tipo de documento al pago (PAG)
                        $entityAdmiTipoDocumento=$em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                        ->findOneByCodigoTipoDocumento('PAGC');
                        $entityPagoCabClonado->setTipoDocumentoId($entityAdmiTipoDocumento);
                        $entityPagoCabClonado->setComentarioPago("Pago creado por cruce manual con anticipo #".
                            $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());

                        //Obtener la numeracion de la tabla Admi_numeracion
                        $datosNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')
                        ->findByEmpresaYOficina($empresaId,$oficinaId,'PAGC');
                        $secuencia_asig=
                            str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT); 
                        $numero_de_pago=$datosNumeracion->getNumeracionUno()."-".
                            $datosNumeracion->getNumeracionDos()."-".$secuencia_asig;
                        //Actualizo la numeracion en la tabla
                        $numero_act=($datosNumeracion->getSecuencia()+1);
                        $datosNumeracion->setSecuencia($numero_act);
                        $em_comercial->persist($datosNumeracion);

                        $entityPagoCabClonado->setPuntoId($entityFactura->getPuntoId());
                        if($saldoPago>=$saldo)
                        {    
                            $entityPagoCabClonado->setValorTotal(round($saldo,2));
                        }    
                        else
                        {    
                            $entityPagoCabClonado->setValorTotal(round($saldoPago,2));
                        }    
                        $entityPagoCabClonado->setNumeroPago($numero_de_pago);
                        $entityPagoCabClonado->setFeCreacion(new \DateTime());
                        $entityPagoCabClonado->setUsrCreacion($usuario);
                        $entityPagoCabClonado->setAnticipoId($entityPagoCab->getId());
                        $em->persist($entityPagoCabClonado);

                        //CREA DETALLE PAGO		
                        $entityPagoDetClonado=new InfoPagoDet();
                        $entityPagoDetClonado= clone $entityPagoDet;
                        $entityPagoDetClonado->setPagoId($entityPagoCabClonado);
                        $entityPagoDetClonado->setFeCreacion(new \DateTime());
                        $entityPagoDetClonado->setUsrCreacion($usuario);
                        $entityPagoDetClonado->setEstado('Cerrado');
                        $entityPagoDetClonado->setComentario("Pago creado por cruce con anticipo No:".
                            $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());						
                        $entityPagoDetClonado->setReferenciaId($entityFactura->getId());
                        $entityPagoDetClonado->setFormaPagoId($entityAdmiFormaPagoCruce->getId());
                        if($saldoPago>=$saldo)
                        {    
                            $entityPagoDetClonado->setValorPago(round($saldo,2));
                        }    
                        else
                        {    
                            $entityPagoDetClonado->setValorPago(round($saldoPago,2));
                        }    
                        $em->persist($entityPagoDetClonado);		
                        //Ingresa historial para el anticipo
                        $serviceInfoPago->ingresaHistorialPago($entityPagoCabClonado,'Cerrado',
                            new \DateTime('now'),$usuario,null,
                            "Pago creado por cruce con anticipo No:".$entityPagoCab->getNumeroPago()); 

                        $saldoPago=$saldoPago-$saldo;
                        //asigna punto a anticipo asignado
                        $entityPagoCab->setPuntoId($entityFactura->getPuntoId());                           
                    }					
                }                                        
            }
            $em->persist($entityPagoCab);
            $em->persist($entityPagoDet);                                
            //CREA ANTICIPO SI ES NECESARIO
            //--****************************************************
            if($saldoPago>0)
            {
                //SE CREA LA CABECERA DEL ANTICIPO
                $entityAdmiFormaPagoCruce=
                    $em_comercial->getRepository('schemaBundle:AdmiFormaPago')->findOneByCodigoFormaPago('CR');                    
                $entityAnticipoCab  = new InfoPagoCab();			
                $tipoDocumento='ANTC';
                $entityAnticipoCab->setPuntoId(end($arrpuntos));
                if($entityPagoCab->getRecaudacionId())
                {
                    $entityAnticipoCab->setEmpresaId($entityPagoCab->getRecaudacionId());
                }
                //SI SE ENCONTRO PUNTO ENTONCES GRABA ANTICIPO
                $entityAnticipoCab->setEmpresaId($empresaId);
                $entityAnticipoCab->setEstadoPago('Pendiente');
                $entityAnticipoCab->setFeCreacion(new \DateTime('now'));
                $entityAnticipoCab->setAnticipoId($entityPagoCab->getId());
                //Obtener la numeracion de la tabla Admi_numeracion
                $datosNumeracionAnticipo = 
                    $em_comercial->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina(
                    $empresaId,$oficinaId,"ANTC");
                $secuencia_asig=str_pad($datosNumeracionAnticipo->getSecuencia(),7, "0", STR_PAD_LEFT); 
                $numero_de_anticipo=$datosNumeracionAnticipo->getNumeracionUno()."-".
                    $datosNumeracionAnticipo->getNumeracionDos()."-".$secuencia_asig;
                //Actualizo la numeracion en la tabla
                $numero_act=($datosNumeracionAnticipo->getSecuencia()+1);
                $datosNumeracionAnticipo->setSecuencia($numero_act);
                $em_comercial->persist($datosNumeracionAnticipo);
                $em_comercial->flush();

                $entityAdmiTipoDocumento=$em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                ->findOneByCodigoTipoDocumento($tipoDocumento);

                $entityAnticipoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                $entityAnticipoCab->setNumeroPago($numero_de_anticipo);
                $entityAnticipoCab->setOficinaId($oficinaId);
                $entityAnticipoCab->setUsrCreacion($request->getSession()->get('user'));
                $entityAnticipoCab->setValorTotal($saldoPago);
                $entityAnticipoCab->setComentarioPago("Anticipo creado por cruce manual con anticipo No:".
                    $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());
                $em->persist($entityAnticipoCab);		
                //CREA LOS DETALLES DEL ANTICIPO
                $entityAnticipoDet= new InfoPagoDet();
                $entityAnticipoDet->setEstado('Pendiente');
                $entityAnticipoDet->setFeCreacion(new \DateTime('now'));
                $entityAnticipoDet->setUsrCreacion($request->getSession()->get('user'));
                $entityAnticipoDet->setValorPago($saldoPago);
                $entityAnticipoDet->setBancoTipoCuentaId($entityPagoDet->getBancoTipoCuentaId());					
                $entityAnticipoDet->setComentario("Anticipo creado por cruce manual con anticipo No:".
                    $entityPagoCab->getNumeroPago().". ".$entityPagoCab->getComentarioPago());
                $entityAnticipoDet->setDepositado($entityPagoDet->getDepositado());
                $entityAnticipoDet->setPagoId($entityAnticipoCab);
                $entityAnticipoDet->setFormaPagoId($entityAdmiFormaPagoCruce->getId());
                if($entityPagoDet->getFeDeposito())
                {
                    $entityAnticipoDet->setFeDeposito($entityPagoDet->getFeDeposito());
                }
                $entityAnticipoDet->setDepositoPagoId($entityPagoDet->getDepositoPagoId());
                $entityAnticipoDet->setNumeroCuentaBanco($entityPagoDet->getNumeroCuentaBanco());
                $em->persist($entityAnticipoDet);
                $em->flush(); 		
                //Ingresa historial para el anticipo
                $serviceInfoPago->ingresaHistorialPago($entityAnticipoCab,'Pendiente',
                    new \DateTime('now'),$usuario,null,
                    "Anticipo creado por cruce con anticipo No:".$entityPagoCab->getNumeroPago().
                    ". ".$entityPagoCab->getComentarioPago());                 
            }

            $em->flush();
            $em_comercial->flush();
            //se recupera detalle de paramatro
            $arrayParametroDet= $em_comercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
            //Se verifica si empresa contabiliza
            if ($arrayParametroDet["valor2"]=="S")
                {
                    $arrayParametrosAsignaAntPunto['intIdPagoCab']  = $entityPagoCab->getId();
                    $arrayParametrosAsignaAntPunto['strEmpresaCod'] = $entityPagoCab->getEmpresaId();
                    $strMsnErrorContabilidad = $em->getRepository('schemaBundle:InfoPagoDet')
                                                  ->contabilizarAsignaAnticipoPunto($arrayParametrosAsignaAntPunto);
                }
            $em->getConnection()->commit();
            $em_comercial->getConnection()->commit();

            //REACTIVA SERVICIOS
            $arrayParams=array(
            'puntos'          => $arrpuntos,
            'prefijoEmpresa'  => $prefijoEmpresa,
            'empresaId'       => $empresaId,
            'oficinaId'       => $oficinaId,
            'usuarioCreacion' => $usuarioCreacion,    
            'ip'              => $request->getClientIp(),
            'idPago'          => $entityPagoCab->getId(),
            'debitoId'        => null                  
            );
            $string_msg=$serviceProcesoMasivo->reactivarServiciosPuntos($arrayParams);            
                        
            $response=new Response(json_encode(
            array('success'=>true,'idpago'=>$entityPagoCab->getId(),'msg'=>$string_msg,'servicios'=>'',
            'link'=>$this->generateUrl('infopagocab_show', array('id' =>$entityPagoCab->getId())))));

            $response->headers->set('Content-type', 'text/json');
            return $response;            
        } 
        catch (\Exception $e) 
        {
            if ($em->getConnection()->isTransactionActive()) 
            {                          
                $em->getConnection()->rollback();
            }
            if ($em_comercial->getConnection()->isTransactionActive()) 
            {                          
                $em_comercial->getConnection()->rollback();
            }
            $em->getConnection()->close();            
            $em_comercial->getConnection()->close();    
            $response = new Response(json_encode(array('success'=>false,'errors' =>array('error' => $e->getMessage()))));
            $response->headers->set('Content-type', 'text/json');
            error_log($e->getMessage());
            return $response;            
        }
    }
	
    public function cruzarAnticipoPorFactura($idFactura,$idPunto) {
        $request=$this->getRequest();    
        $usuario=$request->getSession()->get('user');
        $empresaId=$request->getSession()->get('idEmpresa');
        $oficinaId=$request->getSession()->get('idOficina');
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial= $this->getDoctrine()->getManager('telconet');
        $peticion = $this->get('request');
		$entityAnticipo=$em->getRepository('schemaBundle:InfoPagoCab')->findAnticiposPorPunto($idPunto);
		$entityFactura=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idFactura);
		$acumValorAnticipos=0;
		//busca el anticipo mas antiguo para cruzarlo con la factura
		foreach($entityAnticipo as $anticipo){		
			$entityPagoCab=$em->getRepository('schemaBundle:InfoPagoCab')->find($anticipo->getId());
			$acumValorAnticipos=$acumValorAnticipos+$entityPagoCab->getValorTotal();
			//SOLO REALIZA EL CRUCE SI EL VALOR DE LA FACTURA ES MAYOR O IGUAL AL ANTICIPO
			echo "acumAnticipo:".$acumValorAnticipos."<br>";	
			if($entityFactura->getValorTotal()>=$acumValorAnticipos)
			{
				echo "entro al anticipo<br>";
				$entityPagoCab->setFeUltMod(new \DateTime());
				$entityPagoCab->setFeCruce(new \DateTime());
				$entityPagoCab->setEstadoPago('Cerrado');
				//$entityPagoCab->setPuntoId($idPunto);
				$entityPagoCab->setUsrUltMod($usuario);
				$entityPagoCab->setUsrCruce($usuario);
				//$valorAnticipo=$entityPagoCab->getValorTotal();
				$em->persist($entityPagoCab);
				$em->flush();
				echo "pago:".$entityPagoCab->getId()." estado:".$entityPagoCab->getEstadoPago()."<br>";
				$entityPagoDet=$em->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($anticipo->getId());
				foreach($entityPagoDet as $detallePago){	
					$detallePago->setReferenciaId($idFactura);
					$detallePago->setFeUltMod(new \DateTime());
					$detallePago->setUsrUltMod($usuario);
					$detallePago->setEstado('Cerrado');
					$em->persist($detallePago);
					$em->flush();			
					//echo "  pagodet:".$entityPagoCab->getId()." estado:".$entityPagoCab->getEstado()."<br>";
				}
				echo "valorFactura:".$entityFactura->getValorTotal()." acumAnticipo:".$acumValorAnticipos."<br>";
				if(round($entityFactura->getValorTotal(),2)==round($acumValorAnticipos,2)){
					echo "Entro a cambiar estado de factura<br>";
					$entityFactura->setEstadoImpresionFact('Cerrado');
					$em->persist($entityFactura);
					$em->flush();
					
					$entityHistorial  = new InfoDocumentoHistorial();
					$entityHistorial->setDocumentoId($entityFactura);
					$entityHistorial->setFeCreacion(new \DateTime('now'));
					$entityHistorial->setUsrCreacion($usuario);
					$entityHistorial->setEstado("Cerrado");
					$em->persist($entityHistorial);
					$em->flush();					
					
					break;
				}
			}			
			
		}
	}  

    /**
     * Funcion que busca las facturas de un punto con su saldo.
     * @since 10/11/2014
     * @author amontero@telconet.ec
     * @return array
     */
    public function facturasConPuntoAction()
    {
        $em             = $this->getDoctrine()->getManager('telconet_financiero');
        $idEmpresa      = $this->get('request')->getSession()->get('idEmpresa');
        $idPersona      = $this->getRequest()->get("idcliente");
        $entities       = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findFacturasPorCliente($idPersona, $idEmpresa);
        $arreglo        = array();
        foreach($entities as $dato)
        {
            $saldo=0;
            $entityInfoPunto = $em->getRepository('schemaBundle:InfoPunto')->find($dato->getPuntoId());   
            $arrayParametrosSend   = array('intIdDocumento'  => $dato->getId(), 'intReferenciaId' => '');
            //Obtiene el saldo de la factura
            $arrayGetSaldoXFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                ->getSaldosXFactura($arrayParametrosSend);
            if(!empty($arrayGetSaldoXFactura['strMessageError']))
            {
                throw new Exception('Error al calcular el saldo de factura: '. $dato->getNumeroFacturaSri());
            }
            else
            {
                $saldo=$arrayGetSaldoXFactura['intSaldo'];
            }             
            $arreglo[]=array('idFactura'        => $dato->getId(),
                             'valorFactura'     => round($dato->getValorTotal(),2),
                             'saldo'            => $saldo,
                             'numeroFacturaSri' => "Pto:".$entityInfoPunto->getLogin().
                                                   " Fact:".$dato->getNumeroFacturaSri()." Valor: $".
                                                   round($dato->getValorTotal(),2)." Saldo: $".round($saldo,2));
        }
        $response = new Response(json_encode(array('facturas'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
		return $response;	
    }	
    
    
    /**
     * Documentación para el método 'actualizarPagoAntPuntoAction'.
     * Esta función realiza asignación del punto Cliente al pago anticipo sin cliente
     *
     * @param type $id_anticipo
     * @param type $id_punto
     * @return boolean
     * 
     * @author dmontufar@telconet.ec
     * @version 1.1 15/01/2014
     * 
     * @author llindao@telconet.ec
     * @version 1.2 06-01-2018 Se agrega llamada procedimiento contabilización Asigna Punto a Pago Anticipo sin cliente
     */     
    /**
    * @Secure(roles="ROLE_68-1737")
    */    
    public function actualizarPagoAntPuntoAction($id_anticipo, $id_punto) {
	/*Actualiza Puntos de Anticipos Sin Clientes en la estructura info_pago_cab*/
        $request = $this->getRequest();    
        $success = false;
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $info_pago_cab = $em->getRepository('schemaBundle:InfoPagoCab')->find($id_anticipo);
	
	$peticion = $this->get('request');
        $usuario = $peticion->getSession()->get('user');
        $serviceInfoPago = $this->get('financiero.InfoPago');          
        $strPrefijoEmpresa             = $request->getSession()->get('prefijoEmpresa');        
        $em_comercial                  = $this->getDoctrine()->getManager('telconet');
        $arrayParametrosAsignaAntPunto = array();

        /*Valida que exista el punto*/
        if (!$info_pago_cab) {
            $msg = "No fue encontrado el anticipo...";
            $success = false;
        } else {
	    /*Si encuentra el anticipo busca el punto*/
            $entityInfoPunto=$em->getRepository('schemaBundle:InfoPunto')->find($id_punto);
            /*Si encuentra punto realiza la actualizacion*/
            if(!$entityInfoPunto)
            {
                $msg = "No fue encontrado el punto...";
                $success = false;                             
            }
            else
            {    
                $info_pago_cab->setUsrUltMod($usuario);
                $info_pago_cab->setPuntoId($id_punto);
                //mantiene el comentario actual pero se agrega la fecha solo si es anticipo de recaudacion
                if($info_pago_cab->getRecaudacionId())
                {    
                    //Ingresa historial para el anticipo
                    $serviceInfoPago->ingresaHistorialPago($info_pago_cab,$info_pago_cab->getEstadoPago(),new \DateTime('now'),$usuario,null,
                        "Asignación a punto (".$entityInfoPunto->getLogin().") de ".$info_pago_cab->getComentarioPago().", fecha:".
                        strval(date_format($info_pago_cab->getFeCreacion(), "Y-m-d H:i:s")));
                    $info_pago_cab->setComentarioPago("Asignación a punto (".$entityInfoPunto->getLogin().") de ".
                        $info_pago_cab->getComentarioPago().", fecha:".
                        strval(date_format($info_pago_cab->getFeCreacion(), "Y-m-d H:i:s")));
                }
                $em->persist($info_pago_cab);
                $em->flush();
                //Ingresa historial para el anticipo  
                $serviceInfoPago->ingresaHistorialPago($info_pago_cab,$info_pago_cab->getEstadoPago(),new \DateTime('now'),$usuario,null,
                "Anticipo sin cliente se asigna a punto: ".$entityInfoPunto->getLogin());             
                $msg = "Guardado Correctamente";
                //$em->getConnection()->commit();
                //se recupera detalle de parametro
                $arrayParametroDet= $em_comercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");            
                //Se verifica si empresa contabiliza
                if ($arrayParametroDet["valor2"]=="S")
                {
                    $arrayParametrosAsignaAntPunto['intIdPagoCab']  = $info_pago_cab->getId();
                    $arrayParametrosAsignaAntPunto['strEmpresaCod'] = $info_pago_cab->getEmpresaId();
                    $strMsnErrorContabilidad = $em->getRepository('schemaBundle:InfoPagoDet')
                                                  ->contabilizarAsignaAnticipoPunto($arrayParametrosAsignaAntPunto);
                }

                $success = true;   
            }
        }

        $response = new Response();
        $response->headers->set('Content-type', 'text/json');
        $response->setContent(json_encode(array('success' => $success, 'msg' => $msg)));

        return $response;
    }
    
    /**
    * @Secure(roles="ROLE_66-1357")
    */
    public function anulaAnticipoAction(){
	$peticion = $this->get('request');
	$idPago = $peticion->get('idPago');
        $idMotivo = $peticion->get('idMotivo');
        $Observacion = $peticion->get('txtObservacion');
        $usuario = $peticion->getSession()->get('user');
        $em = $this->get('doctrine')->getManager('telconet_financiero');            
	$statusAnulPago = $em->getRepository('schemaBundle:InfoPagoDet')
        ->anulaPagos($idPago, 
		     $idMotivo, 
		     $usuario, 
		     $Observacion);
        $response = new Response(json_encode(array('idPago' => $idPago, 
						   'motivo' => $idMotivo, 
						   'statusAnulPago' => $statusAnulPago, 
						   'usuario' => $usuario)));
	$response->headers->set('Content-type', 'text/json');
	
	return $response;
    }
    /**
    * @Secure(roles="ROLE_66-1357")
    */
    public function motivosAnulacionAnticipoAction() {    
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
        ->findMotivosPorModuloPorItemMenuPorAccion('AnularPago','Pagos','anularPago');
        $arreglo=array();  
	foreach($datos as $valor):        
            $arreglo[] = array(
                'idMotivo' => $valor->getId(),
                'descripcion' => $valor->getNombreMotivo(),
                'idRelacionSistema'=>$valor->getRelacionSistemaId()
            );
	endforeach;
        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    
    /**
    * Documentación para funcion 'excelAnticiposSinClienteAction'.
    * genera archivo de excel de anticipos sin cliente
    * @author <amontero@telconet.ec>
    * @since 02/02/2015
    * @return objeto para crear excel
    */      
    /**
    * @Secure(roles="ROLE_68-2058")
    */      
    public function excelAnticiposSinClienteAction()
    {       
        $objPHPExcel      = new PHPExcel();
        $cacheMethod      = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings    = array(' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        // Establecer propiedades
        $objPHPExcel->getProperties()
            ->setCreator("Telcos")
            ->setLastModifiedBy("Telcos")
            ->setTitle("Documento Excel de Anticipos sin cliente")
            ->setSubject("Documento Excel de Anticipos sin cliente")
            ->setDescription("")
            ->setKeywords("Excel Office 2007 openxml php")
            ->setCategory("Excel");

        $request    = $this->getRequest();
        $empresaId  = $request->getSession()->get('idEmpresa');
        $fechaDesde = null;
        $fechaHasta = null;
        if ($request->get("fechaDesde")!='null'){
            $fechaDesde = date ('Y-m-d',strtotime($request->get("fechaDesde")));
        }
        if($request->get("fechaHasta")!='null'){
            $fechaHasta = date ('Y-m-d',strtotime($request->get("fechaHasta")));
        }
        $numeroPago           = $request->get("numeroPago");
        $numeroIdentificacion = $request->get("numeroIdentificacion");
        $numeroReferencia     = $request->get("numeroReferencia");
        $estado               = $request->get("estado");
        $em                   = $this->get('doctrine')->getManager('telconet_financiero');
        $parametros = array(
            'estado'               =>$estado,
            'empresaId'            =>$empresaId,    
            'puntoId'              =>'',                    
            'fechaDesde'           =>$fechaDesde,
            'fechaHasta'           =>$fechaHasta,
            'limit'                =>999999999999,
            'start'                =>0,
            'numeroPago'           =>$numeroPago,
            'numeroIdentificacion' =>$numeroIdentificacion,
            'numeroReferencia'     =>$numeroReferencia);
        $resultado  = $em->getRepository('schemaBundle:InfoPagoCab')->findPagosPorCriterios($parametros);
        $datos      = $resultado['registros'];
                
        $styleArray = array('font' => array('bold' => true));

        $i = 2;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'TIPO')
            ->setCellValue('B1', 'NUMERO')
            ->setCellValue('C1', 'TOTAL')
            ->setCellValue('D1', 'OBSERVACION')
            ->setCellValue('E1', 'FECHA CREACION')
            ->setCellValue('F1', 'CEDULA')
            ->setCellValue('G1', 'CLIENTE')
            ->setCellValue('H1', 'ESTADO');

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('F1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($styleArray);
        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        
        foreach($datos as $dato)
        {     
            $nombreCliente  = "";
            $identificacion = "";
            
            if ($dato->getRecaudacionDetId())
            {
                $nombreCliente  = $dato->getRecaudacionDetId()->getNombre();
                $identificacion = $dato->getRecaudacionDetId()->getIdentificacion();
            }            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $i, $dato->getTipoDocumentoId()->getNombreTipoDocumento());                    
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $i, $dato->getNumeroPago());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $i, $dato->getValorTotal());                    
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $i, $dato->getComentarioPago());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $i, strval(date_format($dato->getFeCreacion(), "d/m/Y G:i")));            
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $i, $identificacion);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $i, $nombreCliente);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $i, $dato->getEstadoPago());
            $i++;
        }
        // Renombrar Hoja
        $objPHPExcel->getActiveSheet()->setTitle('Anticipos sin cliente');
        // Establecer la hoja activa, para que cuando se abra el documento se muestre primero.
        $objPHPExcel->setActiveSheetIndex(0);
        // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="reporte_debitos_generados.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }   

    /**
    * Documentación para funcion 'validacionRetencionDuplicadaAction'.
    * Funcion que valida si la retencion ha sido ingresada en la tabla info_pago e info_pago_automatico
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.0 06-09-2021
    *
    * @return String si hay retencion guardada en base
    */
    public function validacionRetencionDuplicadaAction()
    {
        $objRequest        = $this->getRequest();
        $strRpta = "no";
        $intIdPersona = trim($objRequest->request->get("idPer"));
        $intNumRef = trim($objRequest->request->get("numDoc"));
        $intIdFormaPago = trim($objRequest->request->get("idFormaPago"));
        $strCodEmpresa = trim($objRequest->request->get("codEmpresa"));

        $serviceInfoPagoDet                 = $this->get('financiero.InfoPagoDet');
        $arrayParametros["intIdPersona"]    = $intIdPersona;
        $arrayParametros["intNumRef"]       = $intNumRef;
        $arrayParametros["intIdFormaPago"]  = $intIdFormaPago;
        $arrayParametros["strCodEmpresa"]   = $strCodEmpresa;
        $strRpta                            = $serviceInfoPagoDet->getRetencionesDuplicadas($arrayParametros);
        if($strRpta == "")
        {
            $strRpta = "no";
        }
        else
        {
            error_log($strRpta);
            $strRpta = "si";
        }
        return  new Response($strRpta);
    }
}