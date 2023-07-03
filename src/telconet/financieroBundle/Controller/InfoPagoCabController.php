<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoHistorial;
use telconet\schemaBundle\Form\InfoPagoCabType;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoHist;

use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Form\InfoPagoDetType;
use telconet\schemaBundle\Form\InfoRecaudacionType;
use telconet\financieroBundle\Controller\InfoPagoDetController;
use telconet\tecnicoBundle\Controller\InfoServicioController;
use telconet\contabilizacionesBundle\Controller\PagosController;
use telconet\contabilizacionesBundle\Controller\AnticiposController;
use telconet\contabilizacionesBundle\Controller\AnticiposSinClienteController;
use telconet\financieroBundle\Controller\AdmiFormatoDebitoController;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoRecaudacion;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\financieroBundle\Service\InfoPagoService;
use telconet\financieroBundle\Service\InfoPagoDetService;

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
class InfoPagoCabController extends Controller implements TokenAuthenticatedController
{
    /**
     * Lists all InfoPagoCab entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $request = $this->getRequest();
        $session  = $request->getSession();
        $ptoCliente_sesion=$session->get('ptoCliente');
        $cliente_sesion=$session->get('cliente'); 
        //print_r($cliente_sesion);
        //$entities = $em->getRepository('schemaBundle:InfoPagoCab')->findAll();
        
        //Contabilizacion del pago
		/*return $this->forward('contabilizacionesBundle:Pagos:contabilizarPagos', array(
				'id_pago'  => 270,
		));*/
        
        return $this->render('financieroBundle:InfoPagoCab:index.html.twig', array(
            //'entities' => $entities,
            'ptoCliente' => $ptoCliente_sesion,
            'cliente' => $cliente_sesion
        ));
    }
    /*combo estado llenado ajax*/
    public function estadosAction()
    {
	$arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
        $arreglo[]= array('idEstado'=>'Anulado','codigo'=> 'ACT','descripcion'=> 'Anulado');
        $arreglo[]= array('idEstado'=>'Asignado','codigo'=> 'ACT','descripcion'=> 'Asignado');
        $arreglo[]= array('idEstado'=>'Cerrado','codigo'=> 'ACT','descripcion'=> 'Cerrado');
        $arreglo[]= array('idEstado'=>'Pendiente','codigo'=> 'ACT','descripcion'=> 'Pendiente');
        
		$response = new Response(json_encode(array('estados'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;
		
    }
    
    /**
     * Documentación para el método 'gridAction'.
     * Este grid de consulta de pagos
     *
     * @return object $response
     *
     * @author amontero@telconet.ec
     * @version 1.1 22-01-2015
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 12-02-2016
     * Se modifica que envie arreglo de parametros a consulta de Puntos (Logines)del repositorio
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 29-11-2016 - Se modifica a que aparezca la opción de editar pagos y anticipos solo a las empresas que NO CONTABILIZAN. Para ello
     *                           se verifica los detalles del parámetro cabecera que es 'PROCESO CONTABILIZACION EMPRESA' en la tabla 
     *                           'DB_GENERAL.ADMI_PARAMETRO_DET' y se verifica la columna 'VALOR2' si está seteado con un valor distinto de 'S'.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.4 02-08-2017 - Se agrega validacion adicional por proceso contable, se desea anular pago que no se encuentre asociado a debito 
     *                           (DebitoDetId), se desea anular pago (detalles) que no se encuentre relacionado a un deposito (depositoPagoId),
     *                           se verifica si el pago tiene dependencia sobre otros documentos en la 'INFO_PAGO_HISTORIAL', se agrega bandera
     *                           $boolDocDependeDePago, en el caso de que sea True cambia el color de fondo de la fila del pago dependiente en el 
     *                           grid, si solo a las empresas que CONTABILIZA. Para ello se verifica los detalles del parámetro cabecera que es 
     *                           'PROCESO CONTABILIZACION EMPRESA' en la tabla 'DB_GENERAL.ADMI_PARAMETRO_DET' y se verifica la columna 'VALOR2' 
     *                           si está seteado con el valor de 'S'.
     */    
    public function gridAction() 
    {
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $cliente_sesion    = $session->get('cliente');
        $ptoCliente_sesion = $session->get('ptoCliente');
        $clienteId         = '';
        $puntoId           = '';
        if($cliente_sesion)
        {    
            $clienteId = $cliente_sesion['id_persona'];
        }    
        if($ptoCliente_sesion)
        {    
            $puntoId = $ptoCliente_sesion['id'];
        }    
        $empresaId         = $request->getSession()->get('idEmpresa'); 
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');    
        $fechaDesde        = explode('T', $request->get("fechaDesde"));
        $fechaHasta        = explode('T', $request->get("fechaHasta"));
        $numeroPago        = $request->get("numeroPago");         
        $estado            = $request->get("estado");
        $limit             = $request->get("limit");
        $page              = $request->get("page");
        $start             = $request->get("start");
        $em                = $this->get('doctrine')->getManager('telconet_financiero');
        $em1               = $this->get('doctrine')->getManager('telconet');
        $strNombreMotivo   = 'ANULACION_DEPENCIA';
        $strEstadoMotivo   = 'Activo';
        $arrayParametrosPagosDepHisto = array();
        
        $parametros = array(
            'estado'               =>$estado,
            'empresaId'            =>$empresaId,    
            'puntoId'              =>$puntoId,                    
            'fechaDesde'           =>$fechaDesde[0],
            'fechaHasta'           =>$fechaHasta[0],
            'limit'                =>$limit,
            'start'                =>$start,
            'numeroPago'           =>$numeroPago,
            'numeroIdentificacion' =>'',
            'numeroReferencia'     =>'');        
        $resultado    = $em->getRepository('schemaBundle:InfoPagoCab')->findPagosPorCriterios($parametros);
        $datos        = $resultado['registros'];
        $total        = $resultado['total'];
        $idper        = $cliente_sesion['id_persona_empresa_rol'];
        $arrayParametros = array('idper'            => $idper,
                                 'rol'              => '',
                                 'strEstadoPunto'   => '',
                                 'strDireccion'     => '',
                                 'strFechaDesde'    => '',
                                 'strFechaHasta'    => '',
                                 'strLogin'         => '',
                                 'strNombrePunto'   => '',                
                                 'strCodEmpresa'    => $empresaId,
                                 'strEsPadre'       => '',
                                 'intStart'         => 0,                                
                                 'intLimit'         => 999999999,
                                 'serviceInfoPunto' => ''                
            );
        $resultadoPts = $em->getRepository('schemaBundle:InfoPunto')->getResultadoFindPtosPorPersonaEmpresaRol($arrayParametros);        
        $datosPts     = $resultadoPts['registros'];
        $countPts     = 0;
        foreach ($datosPts as $pto)
        {    
            if($puntoId != $pto['id'])
            {
                $countPts = $countPts + 1;
            }
        }
	
        foreach ($datos as $datos):
            $boolDepositoAplicaAnulacion   = true;
            $boolDebitoPagoAplicaAnulacion = true;
            $boolDocDependeDePago          = false;
            $intDebitoDetId                = 0;
            $intDepositoPagoId             = 0;
        
            if ($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANT' || $datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANTC')
            {    
                $urlVer = $this->generateUrl('anticipo_show', array('id' => $datos->getId()));
            }    
            elseif($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='PAG' || $datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='PAGC')
            {    
                $urlVer = $this->generateUrl('infopagocab_show', array('id' => $datos->getId()));
            }    
            elseif($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANTS')
            {    
                $urlVer = $this->generateUrl('anticipo_show', array('id' => $datos->getId()));            
            }    
            $linkVer         = $urlVer;
            $linkRecibo      = $this->generateUrl('infopagocab_recibo', array('id' => $datos->getId()));            
            $entityInfoPunto = $em1->getRepository('schemaBundle:InfoPunto')->find($datos->getPuntoId());
            $entityOficina   = $em1->getRepository('schemaBundle:InfoOficinaGrupo')->find($datos->getOficinaId());
			
            //edicion temporal
            $urlEditar         = "";
            $arrayParametroDet = $em1->getRepository('schemaBundle:AdmiParametroDet')
                                     ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
        
            if( !empty($arrayParametroDet) )
            {
                if( isset($arrayParametroDet['valor2']) &&  !empty($arrayParametroDet['valor2']) && $arrayParametroDet['valor2'] != 'S' )
                {
                    $objTipoDocumento = $datos->getTipoDocumentoId();
                
                    if( is_object($objTipoDocumento) )
                    {
                        $strCodigoTipoDocumento = $objTipoDocumento->getCodigoTipoDocumento();

                        if( !empty($strCodigoTipoDocumento) )
                        {
                            if( $strCodigoTipoDocumento == 'PAG' || $strCodigoTipoDocumento == 'ANT' )
                            {
                                $urlEditar = $this->generateUrl('infopagocab_edit', array('id' => $datos->getId()));
                            }//( $strCodigoTipoDocumento == 'PAG' || $strCodigoTipoDocumento == 'ANT' )
                        }//( !empty($strCodigoTipoDocumento) )
                    }//( is_object($objTipoDocumento) )
                }//( isset($arrayParametroDet['valor2']) &&  !empty($arrayParametroDet['valor2']) && $arrayParametroDet['valor2'] != 'S' )
                

                //Validaciones Adicionales por proceso contable. 
                if(isset($arrayParametroDet['valor2']) && !empty($arrayParametroDet['valor2'])  && $arrayParametroDet['valor2'] === 'S' )
                {
                    //No se puede anular el pago Si ya se encuentra asociado a debito (DebitoDetId)
                    $intDebitoDetId =  $datos->getDebitoDetId();
                    if( isset($intDebitoDetId) && $intDebitoDetId>0 )
                    {
                        $boolDebitoPagoAplicaAnulacion = false;
                    }

                    // No se puede anular el pago SI ya se encuentra depositado (depositoPagoId relacionado).
                    $arrayDetallesPago = $em->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($datos->getId());
                    foreach($arrayDetallesPago as $objDetallePago):
                        //Verifico que tenga asociado un DepositoPagoId
                        $intDepositoPagoId = $objDetallePago->getDepositoPagoId();
                        if( isset( $intDepositoPagoId ) && $intDepositoPagoId>0 )
                        {
                            $boolDepositoAplicaAnulacion = false;
                            break;
                        }
                        
                    endforeach;
                    
                    //Verifico si el pago tiene dependencia sobre otros documentos en la 'INFO_PAGO_HISTORIAL'.
                    $arrayParametrosPagosDepHisto['intIdPago']      = $datos->getId();
                    $arrayParametrosPagosDepHisto['intIdPunto']     = $datos->getPuntoId();
                    $arrayParametrosPagosDepHisto['strEmpresaId']   = $datos->getEmpresaId();
                    $arrayParametrosPagosDepHisto['strNombreMotivo']= $strNombreMotivo;
                    $arrayParametrosPagosDepHisto['strEstadoMotivo']= $strEstadoMotivo;
                    
                    $arrayListPagosDepHisto =   $em->getRepository('schemaBundle:InfoPagoCab')
                                                   ->findPagosPorDependenciaHistorial($arrayParametrosPagosDepHisto);
                    
                    if($arrayListPagosDepHisto && count($arrayListPagosDepHisto)>0)
                    {
                        $boolDocDependeDePago = true;
                    }
                    
                }//(isset($arrayParametroDet['valor2']) && !empty($arrayParametroDet['valor2'])  && $arrayParametroDet['valor2'] === 'S' )
            }//( !empty($arrayParametroDet) )

            $pagoAplicaAnulacion=$em->getRepository('schemaBundle:InfoPagoDet')->checkPagostoAnular($datos->getId());
                
            $arreglo[] = array(
                'id'                            => $datos->getId(),
                'tipo'                          => $datos->getTipoDocumentoId()->getNombreTipoDocumento(),
                'oficina'                       => $entityOficina->getNombreOficina(),			
                'numero'                        => $datos->getNumeroPago(),
                'punto'                         => $entityInfoPunto->getLogin(),
                'idpunto'                       => $entityInfoPunto->getId(),
                'total'                         => $datos->getValorTotal(),
                'fechaCreacion'                 => strval(date_format($datos->getFeCreacion(), "d/m/Y G:i")),
                'usuarioCreacion'               => $datos->getUsrCreacion(),
                'estado'                        => $datos->getEstadoPago(),
                'IdTipo'                        => $datos->getTipoDocumentoId()->getId(),
                'countPts'                      => $countPts,
                'comentarioPago'                =>$datos->getComentarioPago(),
                'linkVer'                       => $linkVer,
                'linkRecibo'                    => $linkRecibo,
                'linkEditar'                    => $urlEditar,
                'pagoAplicaAnulacion'           => $pagoAplicaAnulacion ? $pagoAplicaAnulacion : 0,
                'boolDepositoAplicaAnulacion'   => $boolDepositoAplicaAnulacion ? $boolDepositoAplicaAnulacion : 0,
                'boolDebitoPagoAplicaAnulacion' => $boolDebitoPagoAplicaAnulacion ? $boolDebitoPagoAplicaAnulacion : 0,
                'boolDocDependeDePago'          => $boolDocDependeDePago ? $boolDocDependeDePago : 0
            );
        endforeach;
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

    /**
     * Finds and displays a InfoPagoCab entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial =$this->getDoctrine()->getManager('telconet');
        $entity = $em->getRepository('schemaBundle:InfoPagoCab')->find($id);
        $punto= $em_comercial->getRepository('schemaBundle:InfoPunto')->find($entity->getPuntoId());
        $oficina= $em_comercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        if (!$entity) 
        {
            throw $this->createNotFoundException('Unable to find InfoPagoCab entity.');
        }
        $anticipos=$em->getRepository('schemaBundle:InfoPagoCab')->findByPagoId($entity->getId());	
        $deleteForm = $this->createDeleteForm($id);

        //Obtener el historial
        $serviceInfoPago = $this->get('financiero.InfoPago');
        $historial=$serviceInfoPago->obtenerHistorialPago($entity->getId());        
        
        return $this->render('financieroBundle:InfoPagoCab:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'punto' => $punto,
            'anticipos' => $anticipos,
            'oficina' => $oficina,
            'historial'=>$historial
        ));
    }


    /**
     * Documentación para el método 'reciboPdfAction'.
     * Esta funcion imprime el pdf del pago
     *
     * Actualizacion: Se agrega el banco desde AdmiCuentaContable y se presenta nombre en la firma y
     * en campo recibido por
     * 
     * @author amontero@telconet.ec
     * @version 1.1 22-06-2016 
     *     
     * @author amontero@telconet.ec
     * @version 1.2 28-06-2016 
     * Actualizacion: Se corrige que al imprimir el pago debe consultar 
     * en la InfoPersona por login del empleado y no por usrCreacion
     * 
     * @author Germán Valenzuela Franco <gvalenzuela@telconet.ec>
     * @version 1.3 12-07-2017 - Actualizacion: Se añade el prefijo TNP que corresponde a la empresa de Panama, para que al 
     *                           imprimir el pago consulte en la InfoPersona por login del empleado.
     * 
     * @param $id (Id del pago)
     * @return object $response
     */     
    public function reciboPdfAction($id)
    {
        $request        = $this->getRequest();
        $prefijoEmpresa = $request->getSession()->get('prefijoEmpresa'); 
        $em             = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial   = $this->getDoctrine()->getManager('telconet');
        $entity         = $em->getRepository('schemaBundle:InfoPagoCab')->find($id);
        $punto          = $em_comercial->getRepository('schemaBundle:InfoPunto')->find($entity->getPuntoId());
        $oficina        = $em_comercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        if (!$entity) 
        {
            throw $this->createNotFoundException('Unable to find InfoPagoCab entity.');
        }
        $arrayPersona = $em_comercial->getRepository('schemaBundle:InfoPersona')->findByLogin($entity->getUsrCreacion());
        if (count($arrayPersona)>0 && ($prefijoEmpresa=='TN'|| $prefijoEmpresa=='TNP'))
        {
            $recibidoPor = $arrayPersona[0]->getNombres()." ".$arrayPersona[0]->getApellidos();
        }    
        else
        {
            $recibidoPor = $entity->getUsrCreacion();
        }    

        $facturas        = "";
        $objDetallesPago = $em->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($id);
        $i               = 0;
        foreach($objDetallesPago as $detalle)
        {
            $btcta       = "";
            $nombreBanco = "";
            if($detalle->getBancoTipoCuentaId())
            {
                $btcta       = $em_comercial->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($detalle->getBancoTipoCuentaId());
                $nombreBanco = $btcta->getBancoId()->getDescripcionBanco();
            }
            elseif($detalle->getBancoCtaContableId())
            {
                /* me da la entidad admi_banco_cta_contable*/

                $btctaCont=$em_comercial->getRepository('schemaBundle:AdmiBancoCtaContable')->find($detalle->getBancoCtaContableId());

                /*ahora busco AdmiBancoTipoCta con la entidad admi_banco_cta_contable*/

                $btcta=$em_comercial->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($btctaCont->getBancoTipoCuentaId());

                $nombreBanco=$btcta->getBancoId()->getDescripcionBanco();

            }
            elseif($detalle->getCuentaContableId())
            {
                $btctaCont   = $em->getRepository('schemaBundle:AdmiCuentaContable')->find($detalle->getCuentaContableId());;
                $nombreBanco = trim(str_replace(".","",str_replace("Cta", "", str_replace("Cte", "", $btctaCont->getDescripcion()))));
            }    

            $objFact="";
            if($detalle->getReferenciaId())
                $objFact=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($detalle->getReferenciaId());
            if($objFact)
                $detalles[$i]['factura']=$objFact->getNumeroFacturaSri();
            else
                $detalles[$i]['factura']="";
            if($detalle->getFormaPagoId())
            {
                $objFormaPago=$em_comercial->getRepository('schemaBundle:AdmiFormaPago')->find($detalle->getFormaPagoId());
                if($objFormaPago->getCodigoFormaPago()=='CHEQ' && $btcta)
                {
                    $detalles[$i]['formaPago']=$objFormaPago->getDescripcionFormaPago();
                    $detalles[$i]['banco']=$nombreBanco;
                    $detalles[$i]['numero']="No. Cheq:".$detalle->getNumeroCuentaBanco();
                }
                elseif($objFormaPago->getCodigoFormaPago()=='RF8' || 
                $objFormaPago->getCodigoFormaPago()=='RF2' || 
                $objFormaPago->getCodigoFormaPago()=='RTF')
                {
                    $detalles[$i]['formaPago']=$objFormaPago->getDescripcionFormaPago();
                    $detalles[$i]['numero']="No. Ret:".$detalle->getNumeroReferencia();
                    $detalles[$i]['banco']=$nombreBanco;
                }else{
                    $detalles[$i]['formaPago']=$objFormaPago->getDescripcionFormaPago();
                    $detalles[$i]['numero']="";
                    $detalles[$i]['banco']=$nombreBanco;
                }
            }
            else
            {    
                $detalles[$i]['formaPago']="";
            }    
            $detalles[$i]['valor']=$detalle->getValorPago();
            $i++;
        }
        $saldo = $this->obtieneSaldoPorPunto($punto->getId());
        $html  = $this->renderView('financieroBundle:InfoPagoCab:recibo.html.twig', array(
            'entity'         => $entity,
            'punto'          => $punto,
            'oficina'        => $oficina,
            'prefijoEmpresa' => $prefijoEmpresa,
            'recibidoPor'    => $recibidoPor,
            'detalles'       => $detalles,
            'prefijoEmpresa' => strtoupper($prefijoEmpresa),
            'saldo'          => round($saldo,2)
        ));
        return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'attachment; filename=recibo-pago-'.trim($punto)."-".trim($entity->getNumeroPago()).'.pdf',
                )
        );
    }	
	
    /**
     * @Secure(roles="ROLE_66-2")
     * 
     * Documentación para el método 'newAction'.
     * 
     * Este presenta el twig para el ingreso de nuevo pago
     * Actualizacion: Se incluye prefijo de empresa para leerlo en el twig y aplicar validaciones por empresa.
     * 
     * @author amontero@telconet.ec
     * @version 1.1 amontero@telconet.ec 02-06-2016
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 24-03-2017 - Se valida que el usuario en sessión haya procesado todos los depósitos que haya creado anteriormente para poder
     *                           generar nuevos pagos.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 09-08-2017 - Se verifica si la empresa contabiliza para presentar únicamente las formas de pago que tienen asociado una plantilla
     *                           contable.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 06-10-2017 - Se agrega el tipo de documento para la consulta de las plantillas asociadas a la contabilidad.
     * 
     * @return object render financieroBundle:InfoPagoCab:new.html.twig
     */	
    public function newAction()
    {
        
        //obtiene de sesion los datos
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $ptoCliente_sesion = $session->get('ptoCliente');
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');        
	    $cliente_sesion    = $session->get('cliente');
        $em                = $this->getDoctrine()->getManager('telconet');
        $emFinanciero      = $this->getDoctrine()->getManager('telconet_financiero');
        $entity            = new InfoPagoCab();
        $form              = $this->createForm(new InfoPagoCabType(), $entity);
        $facturas          = null;
        $idPunto           = null;
        $punto             = null;
        $tipoRol           = null;
        $idOficina         = $request->getSession()->get('idOficina');
        $usrSession        = $request->getSession()->get('user');
        $strCodEmpresa     = $request->getSession()->get('idEmpresa');
        
        if($ptoCliente_sesion)
        {
            $idPunto  = $ptoCliente_sesion['id'];
            $facturas = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->findFacturasPendientesPorPunto($idPunto);
            $punto    = $em->getRepository('schemaBundle:InfoPunto')->find($idPunto);
        } 
        if($cliente_sesion)
        {
                $tipoRol=$cliente_sesion['nombre_tipo_rol'];
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
                                                'strTipoDocumento' => 'PAG' );
            $arrayFormasPago           = $emFinanciero->getRepository('schemaBundle:AdmiFormaPago')
                                                      ->findFormasPagoContabilizables($arrayParametrosFormasPago);
        }

        $tipoCuenta = $em->getRepository('schemaBundle:AdmiTipoCuenta')->findOneByDescripcionCuenta('CORRIENTE');
        $oficina    = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($idOficina);
        
        /**
         * Bloque que verifica para TN si el usuario en sessión tiene depósitos para que no pueda crear pagos si no ha procesado todos los depósitos
         */
        $strTieneDepositosPendientes = 'N';
        
        if( $strPrefijoEmpresa == "TN" )
        {
            $arrayInfoDepositos = $emFinanciero->getRepository('schemaBundle:InfoDeposito')
                                               ->findBy( array('usrCreacion' => $usrSession, 
                                                               'estado'      => 'Pendiente', 
                                                               'empresaId'   => $strCodEmpresa) );

            if( !empty($arrayInfoDepositos) )
            {
                $strTieneDepositosPendientes = 'S';
            }//( !empty($arrayInfoDepositos) )
        }//( $strPrefijoEmpresa == "TN" )

        return $this->render('financieroBundle:InfoPagoCab:new.html.twig', array( 'entity'                      => $entity,
                                                                                  'form'                        => $form->createView(),
                                                                                  'formasPago'                  => $arrayFormasPago,
                                                                                  'facturas'                    => $facturas,
                                                                                  'tipoCuenta'                  => $tipoCuenta,
                                                                                  'punto'                       => $punto,
                                                                                  'oficina'                     => $oficina,
                                                                                  'tipoRol'                     => $tipoRol,
                                                                                  'strPrefijoEmpresa'           => $strPrefijoEmpresa,
                                                                                  'strTieneDepositosPendientes' => $strTieneDepositosPendientes ));
    }
    
    public function createAction(Request $request)
    {
        //obtiene de sesion los datos
        $request = $this->getRequest();
        $session = $request->getSession();
        $ptoCliente_sesion = $session->get('ptoCliente');
        $cliente_sesion = $session->get('cliente');
        $valorCabeceraPago = 0;
        $entityInfoPagoCab = new InfoPagoCab();
        $empresaId = $request->getSession()->get('idEmpresa');
        $oficinaId = $request->getSession()->get('idOficina');
        $puntoId = $ptoCliente_sesion['id'];
        $usuarioCreacion = $request->getSession()->get('user');
        $datos_form_pagocab = $request->request->get('infopagocabtype');
        $datos_form_pagodet = $request->request->get('infopagodettype');
        $detalles_arr = explode('|', $datos_form_pagodet['detalles']);
        $em = $this->getDoctrine()->getManager('telconet');
        $em1 = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();
        $em1->getConnection()->beginTransaction();
        $em_comercial->getConnection()->beginTransaction();
        try
        {
            //CABECERA DEL PAGO-->>*************//
            //**********************************//            
            $entityInfoPagoCab->setEmpresaId($empresaId);
            $entityInfoPagoCab->setEstadoPago('Cerrado');
            $entityInfoPagoCab->setFeCreacion(new \DateTime('now'));

            //Obtener la numeracion de la tabla Admi_numeracion
            $datosNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')
                ->findByEmpresaYOficina($empresaId, $oficinaId, "PAG");
            $secuencia_asig = str_pad($datosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
            $numero_de_pago = $datosNumeracion->getNumeracionUno() . "-" . 
                $datosNumeracion->getNumeracionDos() . "-" . $secuencia_asig;
            //Actualizo la numeracion en la tabla
            $numero_act = ($datosNumeracion->getSecuencia() + 1);
            $datosNumeracion->setSecuencia($numero_act);
            $em_comercial->persist($datosNumeracion);
            $em_comercial->flush();

            $entityAdmiTipoDocumento = $em1->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                ->findOneByCodigoTipoDocumento('PAG');
            $entityInfoPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
            $entityInfoPagoCab->setNumeroPago($numero_de_pago);
            $entityInfoPagoCab->setOficinaId($oficinaId);
            $entityInfoPagoCab->setPuntoId($puntoId);
            $entityInfoPagoCab->setUsrCreacion($usuarioCreacion);
            $entityInfoPagoCab->setValorTotal($valorCabeceraPago);
            $em1->persist($entityInfoPagoCab);
            $em1->flush();
            //<<--FIN CABECERA DEL PAGO***************//
            //DETALLES DEL PAGO-->>*************//
            //**********************************//
            $arr_anticipo = array();
            for($i = 0; $i < count($detalles_arr); $i++)
            {
                $esCtaBanco = 'N';
                $esReferencia = 'N';
                $entityBancoTipoCuenta = null;
                $cierraFactura = 'N';
                if($detalles_arr[$i])
                {
                    $pos = strpos($detalles_arr[$i], ',');
                    if($pos == 0)
                        $detalles_arr[$i] = substr_replace($detalles_arr[$i], '', $pos, 1);
                    $entityInfoPagoDet = new InfoPagoDet();
                    $detalles = explode(',', $detalles_arr[$i]);
                    $entityInfoPagoDet->setEstado('Cerrado');
                    $entityInfoPagoDet->setFeCreacion(new \DateTime('now'));
                    $entityInfoPagoDet->setFormaPagoId($detalles[0]);
                    if((($detalles[1] == 'Debito Bancario') || ($detalles[1] == 'Cheque')) || 
                        (($detalles[1] == 'DEBITO BANCARIO') || ($detalles[1] == 'CHEQUE')))
                    {
                        $esCtaBanco = 'S';
                        $entityInfoPagoDet->setNumeroCuentaBanco($detalles[8]);
                        $entityBancoTipoCuenta = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                            ->findBancoTipoCuentaPorBancoPorTipoCuenta($detalles[4], $detalles[6]);
                        $entityInfoPagoDet->setBancoTipoCuentaId($entityBancoTipoCuenta->getId());
                    }
                    elseif(
                        ($detalles[1] == 'Deposito') || ($detalles[1] == 'DEPOSITO') ||
                        ($detalles[1] == 'Transferencia') || ($detalles[1] == 'TRANSFERENCIA')
                    )
                    {
                        $esReferencia = 'S';
                        $entityBancoCtaContable = $em->getRepository('schemaBundle:AdmiBancoCtaContable')->find($detalles[6]);
                        $entityInfoPagoDet->setBancoCtaContableId($entityBancoCtaContable->getId());
                        $entityInfoPagoDet->setNumeroReferencia($detalles[8]);
                    }
                    elseif(
                        ($detalles[1] == 'Tarjeta de Credito') || ($detalles[1] == 'TARJETA DE CREDITO')
                    )
                    {
                        $esReferencia = 'S';
                        $entityBancoTipoCuenta = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                            ->findBancoTipoCuentaPorBancoPorTipoCuenta($detalles[4], $detalles[6]);
                        $entityInfoPagoDet->setBancoTipoCuentaId($entityBancoTipoCuenta->getId());
                        $entityInfoPagoDet->setNumeroReferencia($detalles[8]);
                    }
                    elseif((($detalles[1] == 'Retencion Fuente 2%') || ($detalles[1] == 'Retencion Fuente 8%')) || 
                        (($detalles[1] == 'RETENCION FUENTE 2%') || ($detalles[1] == 'RETENCION FUENTE 8%')))
                    {
                        $entityInfoPagoDet->setNumeroReferencia($detalles[8]);
                    }
                    $valorPago = $detalles[9];

                    //SE VERIFICA SI EL PAGO YA CUBRE LA FACTURA y SI ES ASI CREA ARREGLO ANTICIPOs
                    $arr_total_pagos_fact = $em1->getRepository('schemaBundle:InfoPagoCab')
                        ->findTotalPagosPorFactura($detalles[2]);
                    $entityFactura = $em1->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($detalles[2]);
                    $arr_total_pagos_fact['total_pagos'] = $arr_total_pagos_fact['total_pagos'] * 1;

                    if($arr_total_pagos_fact['total_pagos'] >= 0)
                    {
                        $faltaPorPagar = 0;
                        if($entityFactura->getValorTotal() > $arr_total_pagos_fact['total_pagos'])
                        {
                            $faltaPorPagar = $entityFactura->getValorTotal() - $arr_total_pagos_fact['total_pagos'];
                        }
                        if($faltaPorPagar < $detalles[9])
                        {
                            $valorPago = $faltaPorPagar;
                            $numReferencia = '';
                            $numCtaBanco = '';
                            $bancoTipoCuentaId = '';
                            $diferencia = $detalles[9] - $valorPago;
                            if($entityBancoTipoCuenta)
                                $bancoTipoCuentaId = $entityBancoTipoCuenta->getId();
                            if($esCtaBanco == 'S')
                            {
                                $numReferencia = $detalles[8];
                                $numeroCtaBanco = '';
                            }
                            elseif($esReferencia == 'S')
                            {
                                $numReferencia = '';
                                $numeroCtaBanco = $detalles[8];
                            }
                            $arr_anticipo[] = array('valorAnticipo' => $diferencia,
                                'bancoTipoCuentaId' => $bancoTipoCuentaId,
                                'numeroReferencia' => $numReferencia,
                                'numeroCtaBanco' => $numCtaBanco,
                                'formaPagoId' => $detalles[0]);
                            $cierraFactura = 'S';
                        }
                        if($faltaPorPagar == $detalles[9])
                        {
                            $cierraFactura = 'S';
                        }
                        //cierra factura si con el pago se completa la factura
                        if($cierraFactura == 'S')
                        {
                            $entityFactura->setEstadoImpresionFact('Cerrado');
                            $em1->persist($entityFactura);
                            $em1->flush();

                            //Graba historial de la factura
                            $historialFactura = new InfoDocumentoHistorial();
                            $historialFactura->setDocumentoId($entityFactura);
                            $historialFactura->setEstado($entityFactura->getEstadoImpresionFact());
                            $historialFactura->setFeCreacion(new \DateTime('now'));
                            $historialFactura->setUsrCreacion($usuarioCreacion);
                            $em1->persist($historialFactura);
                            $em1->flush();
                        }
                    }
                    $entityInfoPagoDet->setPagoId($entityInfoPagoCab);
                    $entityInfoPagoDet->setReferenciaId($detalles[2]);
                    $entityInfoPagoDet->setUsrCreacion($usuarioCreacion);
                    $valorCabeceraPago = $valorCabeceraPago + $valorPago;
                    $entityInfoPagoDet->setValorPago($valorPago);
                    $entityInfoPagoDet->setComentario($detalles[10]);
                    $entityInfoPagoDet->setDepositado('N');
                    $em1->persist($entityInfoPagoDet);
                    $em1->flush();
                }
            }
            //<<--FIN DETALLES DEL PAGO***************//
            //Se setea valor total de cabecera y hago persistencia
            $entityInfoPagoCab->setValorTotal($valorCabeceraPago);
            $em1->persist($entityInfoPagoCab);
            $em1->flush();

            //**ANTICIPOS -->>***********//
            //***************************// 
            if(count($arr_anticipo) > 0)
            {
                $totalAnticipo = 0;
                //SUMO el arreglo   
                for($i = 0; $i < count($arr_anticipo); $i++)
                {
                    $totalAnticipo = $totalAnticipo + $arr_anticipo[$i]['valorAnticipo'];
                }
                //SE CREA LA CABECERA DEL ANTICIPO
                $entityAnticipoCab = new InfoPagoCab();
                $entityAnticipoCab->setEmpresaId($empresaId);
                $entityAnticipoCab->setEstadoPago('Pendiente');
                $entityAnticipoCab->setFeCreacion(new \DateTime('now'));

                //Obtener la numeracion de la tabla Admi_numeracion
                $datosNumeracionAnticipo = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')
                    ->findByEmpresaYOficina($empresaId, $oficinaId, "ANT");
                $secuencia_asig = '';
                $secuencia_asig = str_pad($datosNumeracionAnticipo->getSecuencia(), 7, "0", STR_PAD_LEFT);
                $numero_de_anticipo = $datosNumeracionAnticipo->getNumeracionUno() . 
                    "-" . $datosNumeracionAnticipo->getNumeracionDos() . "-" . $secuencia_asig;
                //Actualizo la numeracion en la tabla
                $numero_act = ($datosNumeracionAnticipo->getSecuencia() + 1);
                $datosNumeracionAnticipo->setSecuencia($numero_act);
                $em_comercial->persist($datosNumeracionAnticipo);
                $em_comercial->flush();

                $entityAdmiTipoDocumento = $em1->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                    ->findOneByCodigoTipoDocumento('ANT');
                $entityAnticipoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                $entityAnticipoCab->setNumeroPago($numero_de_anticipo);
                $entityAnticipoCab->setOficinaId($oficinaId);
                $entityAnticipoCab->setPuntoId($puntoId);
                $entityAnticipoCab->setUsrCreacion($usuarioCreacion);
                $entityAnticipoCab->setValorTotal($totalAnticipo);
                $em1->persist($entityAnticipoCab);
                $em1->flush();
                for($i = 0; $i < count($arr_anticipo); $i++)
                {
                    //CREA LOS DETALLES DEL ANTICIPO
                    $entityAnticipoDet = new InfoPagoDet();
                    $entityAnticipoDet->setEstado('Pendiente');
                    $entityAnticipoDet->setFeCreacion(new \DateTime('now'));
                    $entityAnticipoDet->setUsrCreacion($usuarioCreacion);
                    $entityAnticipoDet->setValorPago($arr_anticipo[$i]['valorAnticipo']);
                    $entityAnticipoDet->setComentario('Anticipo generado como saldo a favor');
                    $entityAnticipoDet->setDepositado('N');
                    $entityAnticipoDet->setPagoId($entityAnticipoCab);
                    $entityAnticipoDet->setFormaPagoId($arr_anticipo[$i]['formaPagoId']);
                    $entityAnticipoDet->setBancoTipoCuentaId($arr_anticipo[$i]['bancoTipoCuentaId']);
                    $entityAnticipoDet->setNumeroReferencia($arr_anticipo[$i]['numeroReferencia']);
                    $entityAnticipoDet->setNumeroCuentaBanco($arr_anticipo[$i]['numeroCtaBanco']);
                    $em1->persist($entityAnticipoDet);
                    $em1->flush();
                }
            }
            //<<--FIN ANTICIPOS ***************//

            $em->getConnection()->commit();
            $em1->getConnection()->commit();
            $em_comercial->getConnection()->commit();
            //Contabilizacion del pago
            return $this->forward('contabilizacionesBundle:Pagos:contabilizarPagos', array(
                    'id_pago' => $entityInfoPagoCab->getId(),
            ));
        }
        catch(\Exception $e)
        {
            $entity = new InfoPagoCab();
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $em1->getConnection()->rollback();
            $em1->getConnection()->close();
            $em_comercial->getConnection()->rollback();
            $em_comercial->getConnection()->close();
            $form = $this->createForm(new InfoPagoCabType(), $entity);
            $formasPago = $em->getRepository('schemaBundle:AdmiFormaPago')->findFormasPagoActivas();
            $tipoCuenta = $em->getRepository('schemaBundle:AdmiTipoCuenta')->find(2);
            $facturas = $em1->getRepository('schemaBundle:InfoPagoCab')->findFacturasPendientesPorPunto($puntoId);
            $punto = $em->getRepository('schemaBundle:InfoPunto')->find($puntoId);
            $oficina = $em->getRepository('schemaBundle:InfoOficinaGrupo')->find($oficinaId);
            return $this->render('financieroBundle:InfoPagoCab:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'formasPago' => $formasPago,
                    'facturas' => $facturas,
                    'tipoCuenta' => $tipoCuenta,
                    'punto' => $punto,
                    'oficina' => $oficina,
                    'error' => $e->getMessage()
            ));
        }
    }

    /**
     * Documentación para el método 'createAjaxAction'.
     * Este ingresa pago manual para una factura
     * Actualizacion: Se incluye campos para ingresar al detalle del pago la 
     * cuenta bancaria de la empresa para poder obtener la cuenta contable
     * @return object $response retorna ('idpago' | 'msg' | 'servicios' | 'link')
     *
     * @author amontero@telconet.ec
     * @version 1.2 16-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 28-09-2016 - Se obtiene la variable 'strTipoFormaPago' del detalle de pago para guardar el detalle respectivo.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 08-12-2016 - Se crea un bloque que verifica si el detalle del pago genera un anticipo por un valor excedente del saldo de la 
     *                           factura para que sea contabilizado como un solo asiento contable.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 09-08-2017 - Se modifica la función para contabilizar los anticipos y pagos de forma independiente.
     * 
     * Se inicializa $objParametros y se envía como parámetro a la función contabilizarPagosAnticipo()
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.6 07-11-2019
     * @since 1.5
     */   
    public function createAjaxAction()
    {
        //obtiene de sesion los datos
        $request                       = $this->getRequest();
        $valorCabeceraPago             = 0;
        $entityInfoPagoCab             = new InfoPagoCab();
        $intEmpresaId                  = $request->getSession()->get('idEmpresa');
        $intOficinaId                  = $request->getSession()->get('idOficina');
        $strPrefijoEmpresa             = $request->getSession()->get('prefijoEmpresa');
        $intPuntoId                    = $request->request->get('idpunto');
        $strUsuarioCreacion            = $request->getSession()->get('user');
        $arrDetallesPago               = explode('|', $request->request->get('detalles'));
        $emFinanciero                  = $this->getDoctrine()->getManager('telconet_financiero');
        $emComercial                   = $this->getDoctrine()->getManager();
        $serviceInfoPago               = $this->get('financiero.InfoPago'); 
        $serviceInfoPagoDet            = $this->get('financiero.InfoPagoDet');
        $serviceProcesoMasivo          = $this->get('tecnico.ProcesoMasivo');
        $arrPagosDetIdContabilidad     = array();
        $arrayParametroDet             = array();
        $strMsnErrorContabilidad       = ''; 
        $emFinanciero->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();
        try
        {
            //CABECERA DEL PAGO-->>*************//
            //**********************************//            
            $entityInfoPagoCab->setEmpresaId($intEmpresaId);
            $entityInfoPagoCab->setEstadoPago('Cerrado');
            $entityInfoPagoCab->setFeCreacion(new \DateTime('now'));
            //Obtener la numeracion de la tabla Admi_numeracion
            $datosNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                ->findByEmpresaYOficina($intEmpresaId, $intOficinaId, "PAG");
            $secuencia_asig = str_pad($datosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
            $numero_de_pago = $datosNumeracion->getNumeracionUno() . "-" . 
                $datosNumeracion->getNumeracionDos() . "-" . $secuencia_asig;
            //Actualizo la numeracion en la tabla
            $numero_act = ($datosNumeracion->getSecuencia() + 1);
            $datosNumeracion->setSecuencia($numero_act);
            $emComercial->persist($datosNumeracion);
            $emComercial->flush();

            $entityAdmiTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                ->findOneByCodigoTipoDocumento('PAG');
            $entityInfoPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
            $entityInfoPagoCab->setNumeroPago($numero_de_pago);
            $entityInfoPagoCab->setOficinaId($intOficinaId);
            $entityInfoPagoCab->setPuntoId($intPuntoId);
            $entityInfoPagoCab->setUsrCreacion($strUsuarioCreacion);
            $entityInfoPagoCab->setValorTotal($valorCabeceraPago);
            $emFinanciero->persist($entityInfoPagoCab);
            $emFinanciero->flush();
            
            //DETALLES DEL PAGO-->>*************//
            //**********************************//
            $arrayAnticipo = array();

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
                                                'idFactura'                => $detalles[2],
                                                'numeroFactura'            => $detalles[3],
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
                        $entityInfoPagoCab,$arrayDetallePago,new \DateTime('now'),$valorCabeceraPago); 
                    
                    
                    /**
                     * Bloque que verifica si el detalle del pago genera un anticipo
                     */
                    $arrayResultadoIngresoDetallesPago['strGeneraAnticipo'] = 'N';
                    
                    if( isset($arrayResultadoIngresoDetallesPago['arr_anticipo']) && !empty($arrayResultadoIngresoDetallesPago['arr_anticipo']) )
                    {
                        $arrayAnticipoACrear = $arrayResultadoIngresoDetallesPago['arr_anticipo'];
                        
                        if( isset($arrayAnticipoACrear['valorAnticipo']) && !empty($arrayAnticipoACrear['valorAnticipo']) )
                        {
                            $floatValorAnticipo = $arrayAnticipoACrear['valorAnticipo'];
                            
                            if( floatval($floatValorAnticipo) > 0 )
                            {
                                $arrayAnticipo[] = $arrayResultadoIngresoDetallesPago['arr_anticipo'];
                                
                                $arrayResultadoIngresoDetallesPago['strGeneraAnticipo'] = 'S';
                            }//( floatval($floatValorAnticipo) > 0 )
                        }//( isset($arrayAnticipoACrear['valorAnticipo']) && !empty($arrayAnticipoACrear['valorAnticipo']) )
                    }//( isset($arrayResultadoIngresoDetallesPago['arr_anticipo']) && !empty($arrayResultadoIngresoDetallesPago['arr_anticipo']) )

                    $arrayResultadoIngresoDetallesPago['strGeneraAnticipo'] = 'N';

                    $valorCabeceraPago           = $arrayResultadoIngresoDetallesPago['valorCabeceraPago'];
                    $arrPagosDetIdContabilidad[] = $arrayResultadoIngresoDetallesPago;
                }     
            }

            //Se setea valor total de cabecera y hago persistencia
            $entityInfoPagoCab->setValorTotal($valorCabeceraPago);
            $emFinanciero->persist($entityInfoPagoCab);
            $emFinanciero->flush();

            //Ingresa historial para el pago
            $serviceInfoPago->ingresaHistorialPago($entityInfoPagoCab, 'Cerrado', 
                new \DateTime('now'), $strUsuarioCreacion, null, 'pago creado en forma manual');

            
            //CONTABILIZA DETALLES DE PAGO
            $arrayParametroDet= $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
            
            //**ANTICIPOS -->>***********//
            //***************************// 
            //Si sobro valor del pago procede a crear anticipo
            if(count($arrayAnticipo) > 0)
            {
                $totalAnticipo = 0;
                //SUMO el arreglo   
                for($i = 0; $i < count($arrayAnticipo); $i++)
                {
                    $totalAnticipo = $totalAnticipo + $arrayAnticipo[$i]['valorAnticipo'];
                }
                //SOLO SI LA SUMA DEL VALOR DEL ANTICIPO ES MAYOR A 0 SE CREA ANTICIPO.
                if($totalAnticipo>0)
                {    
                    //SE CREA LA CABECERA DEL ANTICIPO
                    $entityAnticipoCab = new InfoPagoCab();
                    $entityAnticipoCab->setPagoId($entityInfoPagoCab->getId());
                    $entityAnticipoCab->setEmpresaId($intEmpresaId);
                    $entityAnticipoCab->setEstadoPago('Pendiente');
                    $entityAnticipoCab->setFeCreacion(new \DateTime('now'));

                    //Obtener la numeracion de la tabla Admi_numeracion
                    $datosNumeracionAnticipo = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                        ->findByEmpresaYOficina($intEmpresaId, $intOficinaId, "ANT");
                    $secuencia_asig = '';
                    $secuencia_asig = str_pad($datosNumeracionAnticipo->getSecuencia(), 7, "0", STR_PAD_LEFT);
                    $numero_de_anticipo = $datosNumeracionAnticipo->getNumeracionUno() . 
                        "-" . $datosNumeracionAnticipo->getNumeracionDos() . "-" . $secuencia_asig;
                    //Actualizo la numeracion en la tabla
                    $numero_act = ($datosNumeracionAnticipo->getSecuencia() + 1);
                    $datosNumeracionAnticipo->setSecuencia($numero_act);
                    $emComercial->persist($datosNumeracionAnticipo);
                    $emComercial->flush();

                    $entityAdmiTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                        ->findOneByCodigoTipoDocumento('ANT');
                    $entityAnticipoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                    $entityAnticipoCab->setNumeroPago($numero_de_anticipo);
                    $entityAnticipoCab->setOficinaId($intOficinaId);
                    $entityAnticipoCab->setPuntoId($intPuntoId);
                    $entityAnticipoCab->setUsrCreacion($strUsuarioCreacion);
                    $entityAnticipoCab->setValorTotal($totalAnticipo);
                    $emFinanciero->persist($entityAnticipoCab);
                    $emFinanciero->flush();
                    for($i = 0; $i < count($arrayAnticipo); $i++)
                    {
                        if ($arrayAnticipo[$i]['valorAnticipo']>0)
                        {    
                            //CREA LOS DETALLES DEL ANTICIPO
                            $entityAnticipoDet = new InfoPagoDet();
                            $entityAnticipoDet->setEstado('Pendiente');
                            $entityAnticipoDet->setFeCreacion(new \DateTime('now'));
                            $entityAnticipoDet->setUsrCreacion($strUsuarioCreacion);
                            $entityAnticipoDet->setValorPago($arrayAnticipo[$i]['valorAnticipo']);
                            $entityAnticipoDet->setComentario($arrayAnticipo[$i]['comentario'].'. (Anticipo generado como saldo a favor)');
                            $entityAnticipoDet->setCuentaContableId($arrayAnticipo[$i]['cuentaContableId']);
                            $entityAnticipoDet->setFeDeposito(new \DateTime($arrayAnticipo[$i]['fechaDeposito']));
                            $entityAnticipoDet->setDepositado('N');
                            $entityAnticipoDet->setPagoId($entityAnticipoCab);
                            $entityAnticipoDet->setFormaPagoId($arrayAnticipo[$i]['formaPagoId']);
                            $entityAnticipoDet->setBancoTipoCuentaId($arrayAnticipo[$i]['bancoTipoCuentaId']);
                            $entityAnticipoDet->setNumeroReferencia($arrayAnticipo[$i]['numeroReferencia']);
                            $entityAnticipoDet->setNumeroCuentaBanco($arrayAnticipo[$i]['numeroCtaBanco']);
                            $emFinanciero->persist($entityAnticipoDet);
                            $emFinanciero->flush();

                            $arrayDetalleAnticipo        = array('intIdPagoDet' => $entityAnticipoDet->getId(), 'strGeneraAnticipo' => 'N');
                            $arrPagosDetIdContabilidad[] = $arrayDetalleAnticipo;
                            
                            
                        }
                    }
                    //Ingresa historial para el pago
                    $serviceInfoPago->ingresaHistorialPago($entityAnticipoCab, 'Pendiente', new \DateTime('now'), 
                        $strUsuarioCreacion, null, 'Anticipo generado por pago #' . $entityInfoPagoCab->getNumeroPago() . 
                        ' creado en forma manual.');                     
                }                            
            }
            //<<--FIN ANTICIPOS ***************//
            
            $emFinanciero->getConnection()->commit();
            $emComercial->getConnection()->commit();

            
            //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
            if ($arrayParametroDet["valor2"]=="S")
            {    
                $objParametros['serviceUtil']=$this->get('schema.Util');
                $strMsnErrorContabilidad=$emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                    ->contabilizarPagosAnticipo($intEmpresaId, $arrPagosDetIdContabilidad, $objParametros);                
            }                                 
            
            //REACTIVA SERVICIOS
            $arrayParams=array(
            'puntos'          => array($intPuntoId),
            'prefijoEmpresa'  => $strPrefijoEmpresa,
            'empresaId'       => $intEmpresaId,
            'oficinaId'       => $intOficinaId,
            'usuarioCreacion' => $strUsuarioCreacion,    
            'ip'              => $request->getClientIp(),
            'idPago'          => $entityInfoPagoCab->getId(),
            'debitoId'        => null      
            );
            
            $string_msg=$serviceProcesoMasivo->reactivarServiciosPuntos($arrayParams);	
            
            $response = new Response(json_encode(
                array('idpago'            => $entityInfoPagoCab->getId(), 
                      'msg'               => $string_msg,  
                      'link'              => $this->generateUrl('infopagocab_show', array('id' => $entityInfoPagoCab->getId())))));
            
            return $response;
        }
        catch(\Exception $e)
        {
            if ($emFinanciero->getConnection()->isTransactionActive()) 
            {                        
                $emFinanciero->getConnection()->rollback();
            }
            if ($emComercial->getConnection()->isTransactionActive()) 
            {                        
                $emComercial->getConnection()->rollback();
            }         
            $emFinanciero->getConnection()->close();
            $emComercial->getConnection()->close();
            error_log($e->getMessage());
            $response = new Response(json_encode(
                    array('idpago' => '', 'msg' => "error", 'servicios' => '',
                        'link' => $this->generateUrl('infopagocab'), 'msgerror' => $e->getMessage())));
            return $response;
        }
    }

    public function obtieneSaldoPorPunto($idPunto)
	{
		$emfn = $this->get('doctrine')->getManager('telconet_financiero');
		$ingresos=0;$egresos=0;
		$arrValorTotalFacturas=array();$arrValorTotalFacturasProporcionales=array();
		$arrValorTotalPagos=array();$arrValorTotalAnticipos=array();$arrValorTotalAnticiposSinCliente=array();
		$arrValorTotalNotasDebito=array();$arrValorTotalNotasCredito=array();
		//CONSULTA FACTURAS DEL CLIENTE Y SUMA AL SALDO
		$arrValorTotalFacturas=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPuntoPorOfiPorTipoDocPorEmp
		($idPunto, 'FAC');	
		$arrValorTotalFacturasProporcionales=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPuntoPorOfiPorTipoDocPorEmp
		($idPunto, 'FACP');
		//CONSULTA LOS PAGOS DEL CLIENTE y RESTAR AL SALDO
		$arrValorTotalPagos=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->findTotalPagosPorPuntoPorTipoDocPorEmpresa($idPunto,'PAG');
		//CONSULTA LOS ANTICIPOS DEL CLIENTE y RESTAR AL SALDO
		$arrValorTotalAnticipos=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->findTotalPagosPorPuntoPorTipoDocPorEmpresa($idPunto,'ANT');									
		//CONSULTA LOS ANTICIPOS SIN CLIENTE ya asignados y RESTAR AL SALDO
		$arrValorTotalAnticiposSinCliente=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->findTotalPagosPorPuntoPorTipoDocPorEmpresa($idPunto,'ANTS');	                
		//CONSULTA LAS NOTAS DE DEBITO Y LAS SUMA AL SALDO
		$arrValorTotalNotasDebito=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPuntoPorOfiPorTipoDocPorEmp
		($idPunto, 'ND');									
		//CONSULTA LAS NOTAS DE CREDITO Y LAS RESTA AL SALDO
		$arrValorTotalNotasCredito=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
		->findValorTotalDocumentoPorPuntoPorOfiPorTipoDocPorEmp
		($idPunto, 'NC');
		//CALCULO EL SALDO Y ENVIO COMO VALOR A DEBITAR
		//echo($arrValorTotalFacturas[0]['valorTotal']);die;
		if(count($arrValorTotalFacturas)>0)
			$ingresos+= $arrValorTotalFacturas[0]['valorTotal'];
		if(count($arrValorTotalFacturasProporcionales)>0)	
			$ingresos+= $arrValorTotalFacturasProporcionales[0]['valorTotal'];
		if(count($arrValorTotalNotasDebito)>0)	
		$ingresos+= $arrValorTotalNotasDebito[0]['valorTotal'];
		if(count($arrValorTotalPagos)>0)
			$egresos+= $arrValorTotalPagos[0]['valorTotal'];
		if(count($arrValorTotalAnticipos)>0)	
			$egresos+= $arrValorTotalAnticipos[0]['valorTotal'];
		if(count($arrValorTotalAnticiposSinCliente)>0)	
			$egresos+= $arrValorTotalAnticiposSinCliente[0]['valorTotal'];                
		if(count($arrValorTotalNotasCredito)>0)	
			$egresos+= $arrValorTotalNotasCredito[0]['valorTotal'];
			//echo "in:".$ingresos." out:".$egresos."<br>";
		return $datoObtenido = $ingresos - $egresos;
	}	
	
    /**
     * Displays a form to edit an existing InfoPagoCab entity.
     *
     */
    public function editAction($id)
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

        return $this->render('financieroBundle:InfoPagoCab:edit.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'punto' => $punto,
            'oficina' => $oficina));
    }

    /**
     * Edits an existing InfoPagoCab entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoPagoCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPagoCab entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoPagoCabType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infopagocab_edit', array('id' => $id)));
        }

        return $this->render('financieroBundle:InfoPagoCab:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
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
    
    /**
     * @since 1.0
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 05-07-2017
     * Se modifica la función para que devuelva los bancos según el país'
     */ 
    public function getListadoBancosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');    
        $peticion   = $this->get('request');        
        $es_tarjeta = $peticion->query->get('es_tarjeta');
        $visibleEn  = $peticion->query->get('visibleEn');
        $em = $this->getDoctrine()->getManager("telconet");
        $intIdPais = $peticion->getSession()->get('intIdPais');
        $arrayBancos = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosActivos($es_tarjeta,$visibleEn,$intIdPais);

        if($arrayBancos && count($arrayBancos)>0)
        {
            $num = count($arrayBancos);
            
            $arr_encontrados[]=array('id_banco' =>0, 'descripcion_banco' =>"Seleccion un Banco");
            foreach($arrayBancos as $arrayBanco)
            {                
                $arr_encontrados[]=array('id_banco' =>$arrayBanco["id"],
                                         'descripcion_banco' =>trim($arrayBanco["descripcionBanco"]));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                    'encontrados' => array(
                        'id_banco' => 0 , 
                        'descripcion_banco' => 'Ninguno',
                        'banco_id' => 0 , 
                        'banco_descripcion' => 'Ninguno', 
                        'estado' => 'Ninguno'
                ));
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
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    /**
     * Funcion que devuelve lista de bancos'     
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.1
     * @since 14-07-2015
     * @return objeto json
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 05-07-2017
     * Se modifica la función para que devuelva los bancos según el país
     */ 
    public function getListadoBancosTarjetaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');    
        $peticion = $this->get('request');        
        $es_tarjeta = $peticion->query->get('es_tarjeta');
        $em = $this->getDoctrine()->getManager("telconet");
        $intIdPais = $peticion->getSession()->get('intIdPais');
        $arrayBancos = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosTarjetaActivos($intIdPais);

        if($arrayBancos && count($arrayBancos)>0)
        {
            $num = count($arrayBancos);
            
            $arr_encontrados[]=array('id_banco' =>0, 'descripcion_banco' =>"Seleccion un Banco");
            foreach($arrayBancos as $arrayBanco)
            {                
                $arr_encontrados[]=array('id_banco' =>$arrayBanco["id"],
                                         'descripcion_banco' =>trim($arrayBanco["descripcionBanco"]));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_banco' => 0 , 'descripcion_banco' => 'Ninguno','banco_id' => 0 , 'banco_descripcion' => 'Ninguno', 'estado' => 'Ninguno'));
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
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }		
	
    /**
     * Funcion que devuelve lista de tipos de cuenta'     
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0
     * @since 13-07-2015    
     * @return objeto json
     */        
    public function getListadoTiposCuentaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion     = $this->get('request');
        $id_banco     = $peticion->query->get('id_banco');
        $es_tarjeta   = $peticion->query->get('es_tarjeta');
        $visibleEn    = $peticion->query->get('visibleEn');
        $arrEsTarjeta = array();
        if($es_tarjeta)
        {
            $arrEsTarjeta=array($es_tarjeta);
        }   
        else
        {
            $arrEsTarjeta=array('S','N');
        }
        $em = $this->getDoctrine()->getManager("telconet");
        $objAdmiBanco = $em->getRepository('schemaBundle:AdmiBanco')->find($id_banco);
        if($objAdmiBanco)
        {
            $items = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findTodosTiposCuentaPorBanco(
                $id_banco,$arrEsTarjeta,array('Activo'),array('Activo','Activo-debitos','Inactivo'),$visibleEn);
            if($items && count($items)>0)
            {
                $num               = count($items);
                $arr_encontrados[] = array('id_cuenta' =>0, 'descripcion_cuenta' =>"Seleccion un Tipo de Cuenta");
                foreach($items as $key => $item)
                {                
                    $arr_encontrados[]=array('id_cuenta' =>$item["id"],'descripcion_cuenta' =>trim($item["descripcionCuenta"]));
                }
                if($num == 0)
                {
                    $resultado= array(
                        'total'       => 1 ,
                        'encontrados' => array(
                            'id_cuenta'          => 0 , 
                            'descripcion_cuenta' => 'Ninguno',
                            'cuenta_id'          => 0 , 
                            'cuenta_descripcion' => 'Ninguno', 
                            'estado'             => 'Ninguno'));
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
	
    public function getListadoBancosContablesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');    
        $peticion = $this->get('request');
        $empresaCod=$peticion->getSession()->get('idEmpresa');
        $es_tarjeta = $peticion->query->get('es_tarjeta');
        $em = $this->getDoctrine()->getManager("telconet");
        $bancos = $em->getRepository('schemaBundle:AdmiBancoCtaContable')->findBancosContables($empresaCod);

        if($bancos && count($bancos)>0)
        {
            $num = count($bancos);
            
            $arr_encontrados[]=array('id_banco' =>0, 'descripcion_banco' =>"Seleccion un Banco");
            foreach($bancos as $key => $banco)
            {                
                $arr_encontrados[]=array('id_banco' =>$banco["id"],
                                         'descripcion_banco' =>trim($banco["descripcionBanco"]));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_banco' => 0 , 'descripcion_banco' => 'Ninguno','banco_id' => 0 , 'banco_descripcion' => 'Ninguno', 'estado' => 'Ninguno'));
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
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }
 

     public function getListadoCuentaBancosContablesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $empresaCod=$peticion->getSession()->get('idEmpresa');
        $id_banco = $peticion->query->get('id_banco');
        $es_tarjeta = $peticion->query->get('es_tarjeta');
        
        $em = $this->getDoctrine()->getManager("telconet");
        
        if($id_banco!=0)
        {
			$items = $em->getRepository('schemaBundle:AdmiBancoCtaContable')->findCuentasByBancosContables($id_banco,$empresaCod);
            if($items && count($items)>0)
            {
                $num = count($items);

                $arr_encontrados[]=array('id_cuenta' =>0, 'descripcion_cuenta' =>"Seleccion un Tipo de Cuenta");
                foreach($items as $key => $item)
                {                
                    $arr_encontrados[]=array('id_cuenta' =>$item["id"],
                                            'descripcion_cuenta' =>trim($item["descripcion"]." ".$item["noCta"]));
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
 
    
    /**
     * Documentacion para funcion getListadoCuentasBancariasEmpresaAction
     * Funcion que retorna las cuentas bancarias de la empresa en sesion'
     * 
     * Actualizacion: Se agrega campo strConsultaPara para consultar cuentas de empresa
     * por tipo de consulta "PAGOS" o "DEPOSITOS", si consulta por PAGOS solo consulta bancos
     * Si consulta por DEPOSITOS consulta todos incluyendo tipo OTROS     
     * @version 1.1 27/06/2016 amontero@telconet.ec
     * 
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 07-09-2017 - Se agrega correcion de la distribucion contable de documentos meses anteriores, se recupera la formas de pago
     *                           configuradas en la tabla de parametros, unicamente para TN.
     *                          
     * @since 07-01-2016    
     * @return objeto json
     */      
    public function getListadoCuentasBancariasEmpresaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');    
        $peticion        = $this->get('request');
        $objEmGeneral    = $this->getDoctrine()->getManager("telconet_general");
        $strEmpresaCod   = $peticion->getSession()->get('idEmpresa');
        //Variable que describe la opcion del modulo desde donde se usara (Nuevo pago, Nuevo anticipo o generar depositos)
        $strOpcionModulo = $peticion->query->get('opcionModulo');
        $strIdFormaPago  = $peticion->query->get('idFormaPago');
        $strNombreParametro = 'FORMA_PAGO_MES_ANTERIOR';
        $strModulo          = 'FINANCIERO';
        $strProceso         = 'CUENTAS_CONTABLE';
        $strDescripcion     = 'FORMA PAGO';
        $strTipo            = 'BANCOS';
        
        if( !empty($strIdFormaPago) )
        {
            $arrayAdmiParametroDet      = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne($strNombreParametro,
                                                                $strModulo,
                                                                $strProceso,
                                                                $strDescripcion,
                                                                $strIdFormaPago,
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $strEmpresaCod);

            if($arrayAdmiParametroDet && $arrayAdmiParametroDet['valor2'] == 'MESES_ANTERIORES')
            {
                    $strTipo            =  $arrayAdmiParametroDet['valor2'];

            }//if( $arrayAdmiParametroDet['valor2'] == 'MESES_ANTERIORES')
        }
        
        if (!$strOpcionModulo)
        {
            $strOpcionModulo='PAGOS';
        }    
        $emFinanciero = $this->getDoctrine()->getManager("telconet_financiero");
        
        $objJson = $emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')
                                ->getJSONListadoCuentasBancariasEmpresa($strTipo,$strEmpresaCod,$strOpcionModulo);
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }    


    /**
     * Documentacion para la función 'ajaxGetDetallesPagoForEditAction'
     * 
     * Funcion que retorna los detalles asociados al pago que se pueden editar en el grid
     * 
     * @version 1.0 Version Inicial
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 30-09-2016 - Se valida que si la forma de pago viene null presente el detalle con los datos correspondientes. Adicional se valida
     *                           que el valor del pago sea mayor que cero.
     * @return objeto json
     */ 
    public function ajaxGetDetallesPagoForEditAction($id) {
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
                $entityBancoTipoCuenta = $em1->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($datos->getBancoTipoCuentaId());
                $entityBancoCtaContable = $em1->getRepository('schemaBundle:AdmiBancoCtaContable')->findOneByBancoTipoCuentaId($datos->getBancoTipoCuentaId());
                
                if ($entityBancoTipoCuenta){
                   $banco=$entityBancoTipoCuenta->getBancoId()->getDescripcionBanco();
                   $tipoCuenta=$entityBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
                }
                
                if($entityBancoCtaContable){
		  $tipoCuenta = $entityBancoCtaContable->getDescripcion()." ".$entityBancoCtaContable->getNoCta();
                }else{
		  $tipoCuenta = "";
                }
            }
            $numeroFactura='';
            if ($datos->getReferenciaId()){
                $entityFactura=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($datos->getReferenciaId());
                $numeroFactura=$entityFactura->getNumeroFacturaSri();
				if(!$numeroFactura){
					$numeroFactura=$entityFactura->getNumFactMigracion();
				}
            }
            
	    if($datos->getFeDeposito())
		$fechaDeposito = strval(date_format($datos->getFeDeposito(), "d/m/Y"));
	    else
		$fechaDeposito = "Sin dato";
		
        
            //Se valida que la forma de pago no venga en null
            $strFormaPago     = "";
            $objAdmiFormaPago = $datos->getFormaPagoId();

            if( $objAdmiFormaPago!= null )
            {
                $entityFormaPago = $em1->getRepository('schemaBundle:AdmiFormaPago')->find($objAdmiFormaPago);

                if( $entityFormaPago != null )
                {
                    $strFormaPago = $entityFormaPago->getDescripcionFormaPago();
                }//( $entityFormaPago != null )
            }//( $objAdmiFormaPago!= null )
            
            
            //Se valida que el detalle del pago no tenga valor en cero
            if( floatval($datos->getValorPago()) > 0 )
            {
                $arreglo[] = array( 'idPago'     => $id,
                                    'idPagoDet'  => $datos->getId(),
                                    'formaPago'  => $strFormaPago,
                                    'factura'    => $numeroFactura,
                                    'banco'      => $banco,
                                    'tipoCuenta' => $tipoCuenta,
                                    'feDeposito' => $fechaDeposito,
                                    'referencia' => $referencia,
                                    'valor'      => $datos->getValorPago(),
                                    'comentario' => $datos->getComentario() );
            }//( floatval($datos->getValorPago()) > 0 )
            
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
    
    
    /**
     * Documentación para el método 'ajaxActualizaDetPagoAction'.
     * Esta funcion permite editar el detalle de un pago
     * Actualizacion: se agrega la nueva forma de pago RETENCION FUENTE 1%
     * @return object $response retorna ('mensaje de ejecucion')
     *
     * @author amontero@telconet.ec
     * @version 1.1 04-05-2015
     * @version 1.2 05-05-2016 amontero@telconet.ec 
     */    
    public function ajaxActualizaDetPagoAction()
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
            if($formaPago == 'RECAUDACION' || $formaPago == 'DEBITO BANCARIO')
            {
                $fechaDeposito = explode('T', $request->get('fechaDeposito'));

                $dateF = explode("-", $fechaDeposito[0]);

                $fechaDepositoSql = date("Y/m/d", strtotime($dateF[2] . "-" . $dateF[1] . "-" . $dateF[0]));
                if($fechaDepositoSql)
                {
                    $infoPagoDet->setFeDeposito(new \DateTime($fechaDepositoSql));
                    $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                    $infoPagoDet->setFeUltMod(new \DateTime('now'));
                    $seEdito = true;
                }

                $numeroReferencia = $request->get('numeroReferencia');
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

            if($formaPago == 'TARJETA DE CREDITO')
            {
                $idBanco = $request->get('idBanco');
                $idTipoCuenta = $request->get('tipoCuenta');
                $numeroReferencia = $request->get('numeroReferencia');
                $admiBancoTipoCuenta  = $emGeneral->getRepository('schemaBundle:AdmiBancoTipoCuenta')
                    ->findBy(array('bancoId'=>$idBanco,'tipoCuentaId'=>$idTipoCuenta));
                $admiBancoCtaContable = $emGeneral->getRepository('schemaBundle:AdmiBancoCtaContable')
                    ->findByBancoTipoCuentaId($admiBancoTipoCuenta[0]->getId());

                if($admiBancoCtaContable)
                {
                    $infoPagoDet->setBancoCtaContableId($admiBancoCtaContable[0]->getId());
                    $infoPagoDet->setBancoTipoCuentaId($admiBancoCtaContable[0]->getBancoTipoCuentaId()->getId());
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

            if($formaPago == 'RETENCION FUENTE 1%' 
               || $formaPago == 'RETENCION FUENTE 2%' || $formaPago == 'RETENCION FUENTE 8%'  
               || $formaPago == 'RETENCION IVA 70%' || $formaPago == 'RETENCION IVA 100%'
               || $formaPago == 'RETENCION IVA 10%' || $formaPago == 'RETENCION IVA 20%')
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
            ////EFECTIVO
            if($formaPago == 'EFECTIVO')
            {
                $infoPagoDet->setUsrUltMod($request->getSession()->get('user'));
                $infoPagoDet->setFeUltMod(new \DateTime('now'));
                $seEdito = true;
            }
            if($seEdito)
            {
                if($comentario)
                    $infoPagoDet->setComentario($comentario);

                $emFinanciero->persist($infoPagoDet);
                $emFinanciero->flush();


                $response->setContent('OK');
            }else
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
            error_log($mensajeError);
            $response->setContent($mensajeError);
        }

        return $response;
    }

    
    /**
     * Documentación para el método 'getDetallesPago_ajaxAction'.
     * Esta funcion permite editar el detalle de un pago
     *
     * @return object $response retorna ('mensaje de ejecucion')
     *
     * @author amontero@telconet.ec
     * @version 1.1 04-05-2015
     * 
     * @version 1.2 28-06-2016
     * Actualizacion: Se llenan los dos numeros del pago : numeroCuentaBanco y NumeroReferencia
     * Esto para mostrar todos los datos completos en el show del pago
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 30-09-2016 - Se valida que si la forma de pago viene null presente el detalle con los datos correspondientes. Adicional se valida
     *                           que el valor del pago sea mayor que cero.
     */       
    public function getDetallesPago_ajaxAction($id) 
    {
        $emFinanciero     = $this->get('doctrine')->getManager('telconet_financiero');
        $strBanco         = '';
        $strTipoCuenta    = '';
        $strReferencia    = '';
        $numeroCta        = '';
        $strCtaEmpresa    = '';
        $strNumeroFactura =''; 
        $strFechaDeposito ='';    
        $arrayResultado   = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($id);
        foreach ($arrayResultado as $datos)
        {    
            $strBanco      = '';
            $strTipoCuenta = '';
            $strReferencia = '';
            $numeroCta  = '';
            $strCtaEmpresa = '';
            if ($datos->getNumeroCuentaBanco())
            {    
                $numeroCta=$datos->getNumeroCuentaBanco();
            }    
            if($datos->getNumeroReferencia())
            {    
                $strReferencia=$datos->getNumeroReferencia();
            }    
            if($datos->getBancoTipoCuentaId())
            {
                $objBancoTipoCuenta=$emFinanciero->getRepository('schemaBundle:AdmiBancoTipoCuenta')->find($datos->getBancoTipoCuentaId());
                if ($objBancoTipoCuenta)
                {
                   $strBanco      = $objBancoTipoCuenta->getBancoId()->getDescripcionBanco();
                   $strTipoCuenta = $objBancoTipoCuenta->getTipoCuentaId()->getDescripcionCuenta();
                }               
            }
            if($datos->getBancoCtaContableId())
            {
                $objBancoCtaContable=$emFinanciero->getRepository('schemaBundle:AdmiBancoCtaContable')->find($datos->getBancoCtaContableId());
                if ($objBancoCtaContable)
                {
                    $strBanco      = "";
                    $strTipoCuenta = "";
                    $strCtaEmpresa = $objBancoCtaContable->getDescripcion()." ".$objBancoCtaContable->getNoCta();
                }                 
            }
            if($datos->getFeDeposito())
            {    
                $strFechaDeposito = strval(date_format($datos->getFeDeposito(), "d/m/Y"));
            }
            else
            {    
                $strFechaDeposito = "Sin dato";   
            }
            $strNumeroFactura='';
            if ($datos->getReferenciaId())
            {
                $objFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($datos->getReferenciaId());
                $strNumeroFactura = $objFactura->getNumeroFacturaSri();
                if(!$strNumeroFactura)
                {
                    $strNumeroFactura=$objFactura->getNumFactMigracion();
                }
            }
            if($datos->getFeDeposito())
            {    
                $strFechaDeposito = strval(date_format($datos->getFeDeposito(), "d/m/Y"));
            }
            else
            {    
                $strFechaDeposito = "Sin dato";  
            }
            if($datos->getCuentaContableId())
            {
                $objCuentaContable=$emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')->find($datos->getCuentaContableId());
                if ($objCuentaContable)
                {
                    $strCtaEmpresa      = $objCuentaContable->getDescripcion()." ".$objCuentaContable->getNoCta();
                }                 
            }
            
            //Se valida que la forma de pago no venga en null
            $strFormaPago     = "";
            $objAdmiFormaPago = $datos->getFormaPagoId();

            if( $objAdmiFormaPago!= null )
            {
                $objFormaPago = $emFinanciero->getRepository('schemaBundle:AdmiFormaPago')->find($objAdmiFormaPago);

                if( $objFormaPago != null )
                {
                    $strFormaPago = $objFormaPago->getDescripcionFormaPago();
                }//( $entityFormaPago != null )
            }//( $objAdmiFormaPago!= null )
            
            //Se valida que el detalle del pago no tenga valor en cero
            if( floatval($datos->getValorPago()) > 0 )
            {
                $arreglo[] = array( 'id'               => $datos->getId(),
                                    'formaPago'        => $strFormaPago,
                                    'factura'          => $strNumeroFactura,
                                    'banco'            => $strBanco,
                                    'tipoCuenta'       => $strTipoCuenta,
                                    'numeroCta'        => $numeroCta,
                                    'referencia'       => $strReferencia,
                                    'numeroCtaEmpresa' => $strCtaEmpresa,
                                    'valor'            => $datos->getValorPago(),
                                    'feDeposito'       => $strFechaDeposito,
                                    'comentario'       => $datos->getComentario() );
            }//( floatval($datos->getValorPago()) > 0 )
        }
        if (!empty($arreglo))
        {    
            $response = new Response(json_encode(array('detalles' => $arreglo)));
        }    
        else 
        {
            $arreglo[] = array();
            $response = new Response(json_encode(array('detalles' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
 
    /**
     * Documentación para el método 'verificaRetencion_ajaxAction'.
     * Función permite verificar si existen pagos con forma de pago tipo retención asociados al id de la factura enviado como parámetro.
     * @return $Response 
     * 
     * @author Edgar Holguín <eholguin@telconet.ec> 
     * @version 1.1 07-06-2017  Se  agrega validación  para verificar si existen notas de débito asociadas a los pagos existentes.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.2 06-09-2021 Se agrega validacion ṕara verificar si existen formas de pago retenciones registradas en la info_pago e info_pago_cab
     * 
     * @author telcos
     * @since 1.0
     */     
 	public function verificaRetencion_ajaxAction()
	{
		$objRequest        = $this->getRequest();
        
        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $intIdFactura = trim($objRequest->request->get("fact"));
        
        $intIdPersona = trim($objRequest->request->get("idPer"));
        $intNumRef = trim($objRequest->request->get("numDoc"));
        $intIdFormaPago = trim($objRequest->request->get("idFormaPago"));
        $strCodEmpresa = trim($objRequest->request->get("codEmpresa"));

        $strRespuesta = "no";
        
        $arrayPagos   = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->findPagoDetRetencionPorPago($intIdFactura);

        if(count($arrayPagos)>0)
        {
            foreach($arrayPagos as $objInfoPagoDet)
            {

                $arrayNotasDebito = array();

                $arrayNotasDebito = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                 ->findNotasDeDebitoPorPagoDetIdPorEstados($objInfoPagoDet->getId(),
                                                                                           array('Activo','Activa','Pendiente','Cerrado'));
                if(count($arrayNotasDebito)< 1) 
                {                    
                    $strRespuesta = "si";
                    return new Response($strRespuesta);
                }
            }            
        }
        //Si es NO entonces seguimos a verificar si es duplicada en pago-cab/det y pago-automatico
        $strRespuesta = "no";
        $serviceInfoPagoDet            = $this->get('financiero.InfoPagoDet');
        $arrayParametros["intIdPersona"] = $intIdPersona;
        $arrayParametros["intNumRef"] = $intNumRef;
        $arrayParametros["intIdFormaPago"] = $intIdFormaPago;
        $arrayParametros["strCodEmpresa"] = $strCodEmpresa;

        $strRpta = $serviceInfoPagoDet->getRetencionesDuplicadas($arrayParametros);
        if($strRpta != "")
        {
            error_log($strRpta);
            $strRespuesta="ret";
        }
        return new Response($strRespuesta);
    }

    /**
     * Documentación para el método 'getValoresFact_ajaxAction'.
     * Este permite obtener el saldo de la factura
     *
     * @return object $response retorna (array(saldo))
     *
     * @author amontero@telconet.ec
     * @version 1.1 11-11-2014
     */    
    public function getValoresFact_ajaxAction()
    {
        $totalPagos=0;
        $valorFactura=0;
        $request = $this->get('request');             
        $idFactura = trim($request->query->get('fact'));  
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $factura=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idFactura);
        if($factura!=null)
        {
            $valorFactura=$factura->getValorTotal();    
            $arrayParametrosSend   = array('intIdDocumento'  => $factura->getId(), 'intReferenciaId' => '');
            //Obtiene el saldo de la factura
            $arrayGetSaldoXFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                ->getSaldosXFactura($arrayParametrosSend);
            if(!empty($arrayGetSaldoXFactura['strMessageError']))
            {
                throw new Exception('Error al calcular el saldo de factura: '. $factura->getNumeroFacturaSri());
            }
            else
            {
                $totalPagos=$arrayGetSaldoXFactura['intSaldo'];
            }
        }
        $arreglo[] = array('saldo' => $totalPagos);
        $response = new Response(json_encode(array('datosFactura' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }


        public function anticipoAction()
        {
            //obtiene de sesion los datos
            $request = $this->getRequest();
            $session  = $request->getSession();
            $ptoCliente_sesion=$session->get('ptoCliente');
            $em = $this->getDoctrine()->getManager('telconet');
            $em1 = $this->getDoctrine()->getManager('telconet_financiero');
            $entity = new InfoPagoCab();
            $form   = $this->createForm(new InfoPagoCabType(), $entity);
            if($ptoCliente_sesion){
                $idPunto=$ptoCliente_sesion['id'];
            } 
            $formasPago = $em->getRepository('schemaBundle:AdmiFormaPago')->findFormasPagoActivas();
            $tipoCuenta =$em->getRepository('schemaBundle:AdmiTipoCuenta')->findOneByDescripcionCuenta('CORRIENTE');
            return $this->render('financieroBundle:InfoPagoCab:anticipo.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'formasPago' => $formasPago,
                'tipoCuenta' => $tipoCuenta
            ));
        } 

    /**    
     * Documentación para el método 'recaudacionBancosAction'.
     *
     * Descripcion: Función que envía los datos para generar formulario de subida de espuesta de recaudación.
     * 
     * @version 1.0 Versión Inicial
     * @author  telcos
     * 
     * @author  Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 14-11-2017 - Se agrega envío de parámetros que contienen los canales de recaudación existentes.
     * 
     */
    public function recaudacionBancosAction()
    {
        $objInfoRecaudacion = new InfoRecaudacion();
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');          
		$objForm            = $this->createForm(new InfoRecaudacionType(array('validaFile'=>true)), $objInfoRecaudacion);     
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strEmpresaCod      = $objSession->get('idEmpresa');
        $boolTieneFormatoRec= false;
        
        $arrayCanalesRecaudacion                      = array();
        
        $arrayFormatosRecaudacion = $emFinanciero->getRepository('schemaBundle:AdmiFormatoRecaudacion')
                                                 ->findBy(array( "empresaCod" => $strEmpresaCod, "estado" => "Activo"));
        if(count($arrayFormatosRecaudacion)>0)
        {
            $arrayCanalesRec                              = array();
            $arrayCanalesRec[0]['id']                     = '';
            $arrayCanalesRec[0]['nombreCanalRecaudacion'] = 'Seleccione';          

            $arrayParametros = array('strEstado' => 'Iniciado', 'strEmpresaCod' => $strEmpresaCod);
        
            $arrayRecaudacionesIniciadas = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                                        ->getRecaudacionesPorParametros($arrayParametros);

            for($intIndice = 0; $intIndice < count($arrayRecaudacionesIniciadas); $intIndice++ )
            {
                $arrayCanalesRecaudacion[$intIndice]['id']                     = $arrayRecaudacionesIniciadas[$intIndice]['id'];
                $arrayCanalesRecaudacion[$intIndice]['nombreCanalRecaudacion'] = 'Rec:'.$arrayRecaudacionesIniciadas[$intIndice]['id'].
                ' - Fecha: '.strval(date_format($arrayRecaudacionesIniciadas[$intIndice]['feCreacion'], "d/m/Y G:i")).
                ' - Canal : '.$arrayRecaudacionesIniciadas[$intIndice]['nombreCanalRecaudacion'];                   
            }

            $arrayCanalesRecaudacion  = array_merge($arrayCanalesRec,$arrayCanalesRecaudacion);
            
            $boolTieneFormatoRec = true;
        }
          

        return $this->render(   'financieroBundle:InfoPagoCab:recaudacionBancos.html.twig', 
                                array(
                                        'entity'                  => $objInfoRecaudacion,
                                        'form'                    => $objForm->createView(),
                                        'arrayCanalesRecaudacion' => $arrayCanalesRecaudacion,
                                        'boolTieneFormatoRec'     => $boolTieneFormatoRec
                                     ));
    }
    /**
     * Documentación para el método 'leeArchivoRecaudacionAction'.
     * 
     * Envia los valores necesarios para el registro de una nueva recaudación.
     * 
     * @return objeto - render (Renderiza una vista)
     * 
     * @author  telcos
     * @version 1.0 Versión Inicial
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 15-11-2017 Se realiza cambio para que función que guarda registro de recaudación reciba arreglo de parámetros.
     *                         Se valida que el título de hoja sea el correspondiente al canal de recaudación a procesar.
     * 
     */        
    public function leeArchivoRecaudacionAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $datos_form_files   = $objRequest->files->get('inforecaudaciontype');
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');  
       
        $arrayParametros = array();
        $arrayParametros['strEmpresaCod']             = $objSession->get('idEmpresa');
        $arrayParametros['intOficinaId']              = $objSession->get('idOficina');
        $arrayParametros['strUsrCreacion']            = $objSession->get('user');
        $arrayParametros['strIpCreacion']             = $objRequest->getClientIp();
        $arrayParametros['strNombreArchivo']          = $datos_form_files['file'];
        $arrayParametros['strEstado']                 = 'Pendiente';
        $arrayParametros['intRecaudacionId']          = $objRequest->get('canalRecaudacion');
        $arrayParametros['strPrefijoEmpresa']         = $strPrefijoEmpresa;
        $arrayParametros['strNombreArchivoEnvio']     = '';
        $arrayParametros['strNombreCanalRecaudacion'] = '';
        
        try
        {

            if("TN" === $strPrefijoEmpresa)
            {
                $objInfoRecIniciada = $emFinanciero->getRepository('schemaBundle:InfoRecaudacion')
                                                   ->find($arrayParametros['intRecaudacionId']);

                if(is_object($objInfoRecIniciada))
                {
                    $objAdmiCanalRecaudacion = $emFinanciero->getRepository('schemaBundle:AdmiCanalRecaudacion')
                                                            ->find($objInfoRecIniciada->getCanalRecaudacionId());

                    if(is_object($objAdmiCanalRecaudacion))
                    {
                        $arrayParametros['strNombreCanalRecaudacion'] = $objAdmiCanalRecaudacion->getNombreCanalRecaudacion();
                    }
                }
            }         
            /* @var $serviceInfoRecaudacion \telconet\financieroBundle\Service\InfoRecaudacionService */
            $serviceInfoRecaudacion = $this->get('financiero.InfoRecaudacion');
            $objInfoRecaudacion     = $serviceInfoRecaudacion->guardarArchivoRecaudacion($arrayParametros);
            
            if( "TN" === $strPrefijoEmpresa && is_object($objInfoRecIniciada) && !is_object($objInfoRecaudacion))
            {

                $this->get('session')->getFlashBag()
                     ->add('notice', 'Título de hoja no corresponde al canal de recaudación a procesar: '
                                    .$arrayParametros['strNombreCanalRecaudacion']);

                return $this->redirect($this->generateUrl('inforecaudacion'));
            }
            
            $objSession->getFlashBag()
                       ->add('exito', 'Recaudación guardada correctamente en estado Pendiente. '
                           . '         Será procesada automáticamente en los próximos minutos.');
            return $this->redirect($this->generateUrl('inforecaudacion_list_pagos_recaudacion', array('idRec' => $objInfoRecaudacion->getId())));
        }
        catch(\Exception $e)
        {
            $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
            return $this->redirect($this->generateUrl('inforecaudacion'));
        }
    }
    
    public function estadosRecaudacionAction()
    {
        /* @var $serviceInfoRecaudacion \telconet\financieroBundle\Service\InfoRecaudacionService */
        $serviceInfoRecaudacion = $this->get('financiero.InfoRecaudacion');
        $arreglo = $serviceInfoRecaudacion->obtenerEstadosRecaudacion();
        $response = new Response(json_encode(array('estados' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    public function listRecaudacionesAction()
    {
        return $this->render('financieroBundle:InfoPagoCab:listRecaudaciones.html.twig', array());
    }	
    
    /*public function logRecaudacionesAction($fecha)
    {
        $filename = 'procesar_recaudaciones_' . $fecha . '.log';
        $path = $this->get('kernel')->getRootDir(). '/../web/public/uploads/recaudacion_pagos/' . $filename;
        $content = file_get_contents($path);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);
        $response->setContent($content);
        return $response;
    }*/
    
    /**
     * Documentación para funcion 'gridRecaudacionesAction'.
     * 
     * Función que envía los datos que se visualizarán en el grid de recaudaciones.
     * 
     * @author telcos
     * @version 1.0 Versión Iniocial.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 17-11-2017 Se agrega envío del canal de recaudación que se visualizará en el grid de recaudaciones.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.2 10-05-2021 Se realizan cambios por el consumo del nuevo NFS.
     *
     * @return objeto - render (Renderiza una vista)
     */      
    public function gridRecaudacionesAction() {
        $objRequest = $this->getRequest();
        $objSession  = $objRequest->getSession();
        $objClienteSesion=$objSession->get('cliente');
        $objPtoClienteSesion=$objSession->get('ptoCliente');
        $intEmpresaCod = $objRequest->getSession ()->get ( 'idEmpresa' );
        $intClienteId='';
        $intPuntoId='';
        $intLimit = $objRequest->get("limit");
        $intPage = $objRequest->get("page");
        $intStart = $objRequest->get("start");
        $objFechaDesde = explode('T', $objRequest->get("fechaDesde"));
        $objFechaHasta = explode('T', $objRequest->get("fechaHasta"));
        $strEstado = $objRequest->get("estado");	
        $objEm = $this->get('doctrine')->getManager('telconet_financiero');
        $objResultado = $objEm->getRepository('schemaBundle:InfoRecaudacion')
                ->findRecaudacionesPorCriterios($intEmpresaCod,$strEstado,$objFechaDesde[0],$objFechaHasta[0],$intLimit,$intPage,$intStart);
        $arrayDatos = $objResultado['registros'];
        $intTotal = $objResultado['total'];

        foreach ($arrayDatos as $objDatos):
            $strUrlVer= $this->generateUrl('inforecaudacion_list_pagos_recaudacion', array('idRec' => $objDatos->getId()));
            $strLinkVer=$strUrlVer;
            if($objDatos->getAbsoluteNoEncontradosPath()!== null)
            {
                $strUrlExcelRecaudacion = $objDatos->getAbsoluteNoEncontradosPath();
            }else
            {
                $strUrlExcelRecaudacion = "";
            }
            
            $strUrlFormatoEnvioRecaudacion = $this->generateUrl('inforecaudacion_download_formato_envio_recaudacion',
                                                                                        array('id' => $objDatos->getId()));
            
            $strCanalRecaudacion = "Rec: #".$objDatos->getId();
            if($objDatos->getCanalRecaudacionId())
            {
                $objAdmiCanalRecaudacion = $objEm->getRepository('schemaBundle:AdmiCanalRecaudacion')->find($objDatos->getCanalRecaudacionId());
                
                if(is_object($objAdmiCanalRecaudacion))
                {
                    $strCanalRecaudacion .= " - Canal: ".$objAdmiCanalRecaudacion->getNombreCanalRecaudacion();
                }
            }
            $arrayArreglo[] = array(
                                'id'               => $objDatos->getId(),
                                'fechaCreacion'    => strval(date_format($objDatos->getFeCreacion(), "d/m/Y G:i")),
                                'usuarioCreacion'  => $objDatos->getUsrCreacion(),
                                'estado'           => $objDatos->getEstado(),
                                'canalRecaudacion' => $strCanalRecaudacion,
                                'linkVer'          => $strLinkVer,
                                'linkExcelRec'     => $strUrlExcelRecaudacion,
                                'linkFormatoRec'   => $strUrlFormatoEnvioRecaudacion
                            );
        endforeach; 
        if (!empty($arrayArreglo))
        {
            $objResponse = new Response(json_encode(array('total' => $intTotal, 'recaudaciones' => $arrayArreglo)));
        }
        else {
            $arrayArreglo[] = array();
            $objResponse = new Response(json_encode(array('total' => $intTotal, 'recaudaciones' => $arrayArreglo)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
	
	
    public function listPagosRecaudacionAction($idRec)
    {
        //echo "r:".round(0.84);
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $request = $this->getRequest();
        $session  = $request->getSession();
        $ptoCliente_sesion=$session->get('ptoCliente');
        $cliente_sesion=$session->get('cliente');
        $entityRecaudacion = $em->getRepository('schemaBundle:InfoRecaudacion')->find($idRec);
        if (!empty($entityRecaudacion) && $entityRecaudacion->getEstado() === 'Activo' && file_exists($entityRecaudacion->getAbsoluteNoEncontradosPath()))
        {
            $linkExcelRecaudacion = $this->generateUrl('inforecaudacion_download_archivo_recaudacion', array('id' => $idRec));
        }
        else
        {
            $linkExcelRecaudacion = '';
        }
        return $this->render('financieroBundle:InfoPagoCab:listPagosRecaudacion.html.twig', array(
            'entityRecaudacion' => $entityRecaudacion,
            'ptoCliente' => $ptoCliente_sesion,
            'cliente' => $cliente_sesion,
            'linkExcelRec' => $linkExcelRecaudacion,
        ));
    }	

    public function gridPagosRecaudacionAction() {
        $request = $this->getRequest();
        $session  = $request->getSession();
        $cliente_sesion=$session->get('cliente');
        $ptoCliente_sesion=$session->get('ptoCliente');
        $clienteId='';$puntoId='';
        $oficinaId=$request->getSession()->get('idOficina');
        if($cliente_sesion)
            $clienteId=$cliente_sesion['id_persona'];
        if($ptoCliente_sesion)
            $puntoId=$ptoCliente_sesion['id'];
        $recaudacionId= $request->get("recaudacionId");
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $estado = $request->get("estado");		
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $em1 = $this->get('doctrine')->getManager('telconet');
        $resultado = $em->getRepository('schemaBundle:InfoPagoCab')
		->findPagosPorRecaudacion($estado, $recaudacionId,$fechaDesde[0],$fechaHasta[0],$limit,$page,$start);
        $datos = $resultado['registros'];
        $total = $resultado['total'];
	
        foreach ($datos as $datos):
            $cliente='';
            $login='';
            if ($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANT')
                $urlVer = $this->generateUrl('anticipo_show', array('id' => $datos->getId()));
            elseif($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='PAG')
                $urlVer = $this->generateUrl('infopagocab_show', array('id' => $datos->getId()));
            elseif($datos->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANTS')
                $urlVer = $this->generateUrl('anticipo_show', array('id' => $datos->getId()));            
            $linkVer = $urlVer;
            if($datos->getPuntoId()){
                    $entityInfoPunto=$em1->getRepository('schemaBundle:InfoPunto')->find($datos->getPuntoId());
                    if($entityInfoPunto){
                            $login=$entityInfoPunto->getLogin();
                            if($entityInfoPunto->getPersonaEmpresaRolId())
                                    $cliente=$entityInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->getInformacionPersona();
                    }
            }         
            $arrDetallesPago=$em->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($datos->getId());
            foreach($arrDetallesPago as $objDetallesPago){
                $fechaDeposito=$objDetallesPago->getFeDeposito();
            }
            $arreglo[] = array(
                'id' => $datos->getId(),
                'tipo' => $datos->getTipoDocumentoId()->getNombreTipoDocumento(),
                'numero' => $datos->getNumeroPago(),
				'cliente'=> $this->sanear_string($cliente),
                'punto' => $login,
                'total' => $datos->getValorTotal(),
                'fechaCreacion' => strval(date_format($datos->getFeCreacion(), "d/m/Y G:i")),
                'fechaDeposito' => strval(date_format($fechaDeposito, "d/m/Y G:i")),
                'usuarioCreacion' => $datos->getUsrCreacion(),
		'comentario' => $datos->getComentarioPago(),
                'estado' => $datos->getEstadoPago(),
                'linkVer' => $linkVer
            );
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'pagos' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    } 	
	
	
	public function downloadArchivoRecaudacionAction($id)
	{
		$em = $this->getDoctrine()->getManager('telconet_financiero');
		
		$entity = $em->getRepository('schemaBundle:InfoRecaudacion')->find($id);	

		$request = $this->get('request');
		$path = $entity->getAbsoluteNoEncontradosPath();
		$content = file_get_contents($path);

		$response = new Response();

		//set headers
		$response->headers->set('Content-Type', 'mime/type');
		$response->headers->set('Content-Disposition', 'attachment;filename="reporte_'.$entity->getPath());

		$response->setContent($content);
		return $response;
	}		
	

	
	public function atsAction(){
        $request = $this->getRequest();
        $session  = $request->getSession();
        return $this->render('financieroBundle:InfoPagoCab:ats.html.twig', array('anioactual'=>date('Y')));	
	}	
	
/*public function generarXmlVentasAtsAction(){

	$emfn = $this->get('doctrine')->getManager('telconet_financiero');
	$em = $this->get('doctrine')->getManager('telconet');
	$request=$this->getRequest();
	$idEmpresa=$request->getSession()->get('idEmpresa');
	$idOficina=$request->getSession()->get('idOficina');		
	$localFilePath='/home/telcos/web/public/uploads/ats/';
	//var_dump($request->getSession());
	//echo "oficina:".$request->getSession()->get('idOficina');die;
	$zip = new \ZipArchive;	
	$zipName="AT".date('mdy').".zip";
	
	//CREA EL ARCHIVO ZIP y SI NO LO CREA NO CONTINUA EL PROCESO
	if ($zip->open($localFilePath.$zipName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) === TRUE) 
	{		
		$entityEmpresa=$em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($idEmpresa);
		$file_name="AT".date('mdy').".xml";
		$file_open = fopen($localFilePath.$file_name,"w+");
		$linea='';
		if($file_open){
		$lineaCabecera=
            '<?xml version="1.0"?>'.
            '<iva>'.
            '<numeroRuc>'.
			$entityEmpresa->getRuc().
            '</numeroRuc>'.
            '<razonSocial>'.
            $entityEmpresa->getRazonSocial().
            '</razonSocial>'.
            '<anio>'.
            date('Y').
            '</anio>'.
            '<mes>'.
            date('m').
            '</mes>'.
            '<compras></compras>';
			$linea=$linea.$lineaCabecera.'<ventas>';
			//OBTIENE FACTURAS Y NOTAS DE CREDITO
			//$facturas=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
			//->findListadoFacturasProcesadasAts($idEmpresa,$fechaDesde,$fechaHasta);
			$facturas=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
			->findListadoFacturasProcesadasAts($idEmpresa,'2013-05-01','2013-05-30');
				
			foreach($facturas as $factura){
				$linea=$linea.'<detalleVentas>';
				if(strtoupper(trim($factura['tipoIdentificacion']))=='RUC'){
						$linea=$linea.'<tpIdCliente>04</tpIdCliente>';
				}elseif(strtoupper(trim($factura['tipoIdentificacion']))=='CED'){
						$linea=$linea.'<tpIdCliente>05</tpIdCliente>';
				}elseif(strtoupper(trim($factura['tipoIdentificacion']))=='PAS'){
						$linea=$linea.'<tpIdCliente>06</tpIdCliente>';
				}			
				$linea=$linea.'<idCliente>'.$factura['identificacionCliente'].'</idCliente> ';
				if(strtoupper(trim($factura['codigoTipoDocumento']))=='NC'){
						$linea=$linea.'<tipoComprobante>04</tipoComprobante>';
				}elseif(strtoupper(trim($factura['codigoTipoDocumento']))=='FAC'){
						$linea=$linea.'<tipoComprobante>18</tipoComprobante>';
				}elseif(strtoupper(trim($factura['codigoTipoDocumento']))=='FACP'){
						$linea=$linea.'<tipoComprobante>18</tipoComprobante>';
				}
				
				$linea=$linea.'<numeroComprobantes>'.$factura['totalRegistros'].'</numeroComprobantes>';
				$linea=$linea.'<baseNoGraIva>0.00</baseNoGraIva> 
				  <baseImponible>0.00</baseImponible> 
				  <baseImpGrav>'.$factura['subtotal'].'</baseImpGrav> 
				  <montoIva>'.$factura['subtotalConImpuesto'].'</montoIva> 
				  <valorRetIva>0.00</valorRetIva> 
				  <valorRetRenta>0.00</valorRetRenta>';
				$linea=$linea.'</detalleVentas>';
			}
			$linea=$linea.'</ventas></iva>';
			
			//OBTIENE RETENCIONES	
			//$retenciones=$emfn->getRepository('schemaBundle:InfoPagoCab')
			//->findListadoRetencionesAts($idEmpresa,$fechaDesde,$fechaHasta);			
			$retenciones=$emfn->getRepository('schemaBundle:InfoPagoCab')
			->findListadoRetencionesAts($idEmpresa,'2013-05-01','2013-05-30');			
			foreach($retenciones as $retencion){
				$linea=$linea.'<detalleVentas>';
				if(strtoupper(trim($retencion['tipoIdentificacion']))=='RUC'){
						$linea=$linea.'<tpIdCliente>04</tpIdCliente>';
				}elseif(strtoupper(trim($retencion['tipoIdentificacion']))=='CED'){
						$linea=$linea.'<tpIdCliente>05</tpIdCliente>';
				 }elseif(strtoupper(trim($retencion['tipoIdentificacion']))=='PAS'){
						$linea=$linea.'<tpIdCliente>06</tpIdCliente>';
				}			
				$linea=$linea.'<idCliente>'.$retencion['identificacionCliente'].'</idCliente> ';
				if(strtoupper(trim($retencion['codigoTipoDocumento']))=='NC'){
						$linea=$linea.'<tipoComprobante>04</tipoComprobante>';
				}elseif(strtoupper(trim($retencion['codigoTipoDocumento']))=='FAC'){
						$linea=$linea.'<tipoComprobante>18</tipoComprobante>';
				}elseif(strtoupper(trim($retencion['codigoTipoDocumento']))=='FACP'){
						$linea=$linea.'<tipoComprobante>18</tipoComprobante>';
				}
				
				$linea=$linea.'<numeroComprobantes>'.$retencion['totalRegistros'].'</numeroComprobantes>';
				$linea=$linea.'<baseNoGraIva>0.00</baseNoGraIva> 
				  <baseImponible>0.00</baseImponible> 
				  <baseImpGrav>0.00</baseImpGrav> 
				  <montoIva>0.00</montoIva> 
				  <valorRetIva>0.00</valorRetIva> 
				  <valorRetRenta>'.$retencion['valorPago'].'</valorRetRenta>';
				$linea=$linea.'</detalleVentas>';
			}
			$linea=$linea.'</ventas></iva>';			
			
		fwrite($file_open, $linea);		
		//QUERY DE FACTURAS
		fclose($file_open);
		}

		$zip->addFile($localFilePath.$file_name,$file_name);		
		$zip->close();

		$response= new Response('', 200, array(
			'X-Sendfile'          => $localFilePath.$zipName,
			'Content-type'        => 'application/octect-stream',
			'Content-Disposition' => sprintf('attachment; filename="%s"', $zipName)));	
		$response->sendHeaders();
		$response->setContent(readfile($localFilePath.$zipName));	
		//$emfn->getConnection()->commit();				
		return $response;
	}
		
}*/


public function generarXmlVentasAtsAction(){

	$emfn = $this->get('doctrine')->getManager('telconet_financiero');
	$em = $this->get('doctrine')->getManager('telconet');
	$request=$this->getRequest();
	$idEmpresa=$request->getSession()->get('idEmpresa');
	$idOficina=$request->getSession()->get('idOficina');		
	$localFilePath='/home/telcos/web/public/uploads/ats/';
	$zip = new \ZipArchive;	
	$zipName="AT".date('mdy').".zip";

	$mes=$request->request->get('mes');
	$anio=$request->request->get('anio');
	$diasmes=date("d",mktime(0,0,0,$mes+1,0,$anio));
	//echo $diasmes."<br>";
	$fechaDesde=$anio."-".$mes."-01";
	$fechaHasta=$anio."-".$mes."-".$diasmes;
	//echo $fechaDesde."  ".$fechaHasta;
	//die;

	//CREA EL ARCHIVO ZIP y SI NO LO CREA NO CONTINUA EL PROCESO
	if ($zip->open($localFilePath.$zipName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) === TRUE) 
	{		
		$entityEmpresa=$em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($idEmpresa);
		$file_name="AT".date('mdy').".xml";
		$file_open = fopen($localFilePath.$file_name,"w+");
		$linea='';
		if($file_open){
		$lineaCabecera=
            '<?xml version="1.0"?>'.
            '<iva>'.
            '<numeroRuc>'.
			$entityEmpresa->getRuc().
            '</numeroRuc>'.
            '<razonSocial>'.
            $entityEmpresa->getRazonSocial().
            '</razonSocial>'.
            '<anio>'.
            date('Y').
            '</anio>'.
            '<mes>'.
            date('m').
            '</mes>'.
            '<compras></compras>';
			$linea=$linea.$lineaCabecera.'<ventas>';
			//OBTIENE FACTURAS Y NOTAS DE CREDITO
			$facturas=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
			->findListadoFacturasProcesadasAts($idEmpresa,$fechaDesde,$fechaHasta);
				
			foreach($facturas as $factura){
				$linea=$linea.'<detalleVentas>';
				if(strtoupper(trim($factura['tipoIdentificacion']))=='RUC'){
						$linea=$linea.'<tpIdCliente>04</tpIdCliente>';
				}elseif(strtoupper(trim($factura['tipoIdentificacion']))=='CED'){
						$linea=$linea.'<tpIdCliente>05</tpIdCliente>';
				}elseif(strtoupper(trim($factura['tipoIdentificacion']))=='PAS'){
						$linea=$linea.'<tpIdCliente>06</tpIdCliente>';
				}			
				$linea=$linea.'<idCliente>'.$factura['identificacionCliente'].'</idCliente> ';
				if(strtoupper(trim($factura['codigoTipoDocumento']))=='NC'){
						$linea=$linea.'<tipoComprobante>04</tipoComprobante>';
				}elseif(strtoupper(trim($factura['codigoTipoDocumento']))=='FAC'){
						$linea=$linea.'<tipoComprobante>18</tipoComprobante>';
				}elseif(strtoupper(trim($factura['codigoTipoDocumento']))=='FACP'){
						$linea=$linea.'<tipoComprobante>18</tipoComprobante>';
				}
				
				$linea=$linea.'<numeroComprobantes>'.$factura['totalRegistros'].'</numeroComprobantes>';
				$linea=$linea.'<baseNoGraIva>0.00</baseNoGraIva> 
				  <baseImponible>0.00</baseImponible> 
				  <baseImpGrav>'.$factura['subtotal'].'</baseImpGrav> 
				  <montoIva>'.$factura['subtotalConImpuesto'].'</montoIva>'; 
				$linea=$linea.'<valorRetIva>0.00</valorRetIva>';
				//OBTIENE LOS VALORES POR RETENCIONES
				$retenciones=$emfn->getRepository('schemaBundle:InfoPagoCab')
			->findTotalRetencionesAtsPorPersonaEmpresaRol($factura['idPersonaEmpresaRol'],$fechaDesde,$fechaHasta);
				//print_r($retenciones);
				if($retenciones){
					//echo "entro a retenciones";
					$linea=$linea.'<valorRetRenta>'.$retenciones[0]['valorPago'].'</valorRetRenta>';
					//echo "asigno retencion";
					//die;
				}else			
					$linea=$linea.'<valorRetRenta>0.00</valorRetRenta>';
				$linea=$linea.'</detalleVentas>';
			}
			$linea=$linea.'</ventas>';
			$linea=$linea.'<anulados>';		
			$facturasAnuladas=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
			->findListadoFacturasAnuladasAts($idEmpresa,$fechaDesde,$fechaHasta);	
			foreach($facturasAnuladas as $facturaAnulada){
				$linea=$linea.'<detalleAnulados>';			
				if(strtoupper(trim($facturaAnulada['codigoTipoDocumento']))=='NC'){
							$linea=$linea.'<tipoComprobante>04</tipoComprobante>';
							$codigo="NC";
				}elseif(strtoupper(trim($facturaAnulada['codigoTipoDocumento']))=='FAC'){
							$linea=$linea.'<tipoComprobante>01</tipoComprobante>';
							$codigo="FAC";
				}elseif(strtoupper(trim($facturaAnulada['codigoTipoDocumento']))=='FACP'){
							$linea=$linea.'<tipoComprobante>01</tipoComprobante>';
							$codigo="FAC";
				}

				$objnumeroAutorizacion=$em->getRepository('schemaBundle:AdmiNumeracion')->findOneBy(
				array( "codigo" => $codigo, "oficinaId" => $facturaAnulada['oficinaId']));

				if($objnumeroAutorizacion)
					$numeroAutorizacion=$objnumeroAutorizacion->getNumeroAutorizacion();
				else
					$numeroAutorizacion="";

				if(strpos($facturaAnulada['numeroFacturaSri'],"-")){
					$arrnumerofacturasri=explode('-',$facturaAnulada['numeroFacturaSri']);
				}else{
					$entityAdmiNumeracion=$em->getRepository("schemaBundle:AdmiNumeracion")
					->findOneBy(array( "empresaId" => $idEmpresa, "oficinaId" => $facturaAnulada['oficinaId'],"codigo"=>$codigo));													
					$comprobante=$entityAdmiNumeracion->getNumeracionUno()."-".
					$entityAdmiNumeracion->getNumeracionDos()."-".$facturaAnulada['numeroFacturaSri'];
					$arrnumerofacturasri=explode('-',$comprobante);
				}

				$linea=$linea.'<establecimiento>'.$arrnumerofacturasri[0].'</establecimiento>';
				$linea=$linea.'<puntoEmision>'.$arrnumerofacturasri[1].'</puntoEmision>';
				$linea=$linea.'<secuencialInicio>'.$arrnumerofacturasri[2].'</secuencialInicio>';
				$linea=$linea.'<secuencialFin>'.$arrnumerofacturasri[2].'</secuencialFin>';
				$linea=$linea.'<autorizacion>'.$numeroAutorizacion.'</autorizacion>';
				$linea=$linea.'</detalleAnulados>';
			}
			$linea=$linea.'</anulados>';
			$linea=$linea.'</iva>';
		fwrite($file_open, $linea);		
		//QUERY DE FACTURAS
		fclose($file_open);
		}

		$zip->addFile($localFilePath.$file_name,$file_name);		
		$zip->close();

		$response= new Response('', 200, array(
			'X-Sendfile'          => $localFilePath.$zipName,
			'Content-type'        => 'application/octect-stream',
			'Content-Disposition' => sprintf('attachment; filename="%s"', $zipName)));	
		$response->sendHeaders();
		$response->setContent(readfile($localFilePath.$zipName));	
		//$emfn->getConnection()->commit();				
		return $response;
	}
		
}


	public function contabilizarRecaudacionPruebaAction($idrec,$idpag){
			$emfn = $this->getDoctrine()->getManager('telconet_financiero');
		$emcom = $this->getDoctrine()->getManager('telconet');
		try{
			if($idrec!=0 && $idrec!='')
				$pagos=$emfn->getRepository('schemaBundle:InfoPagoCab')->findByRecaudacionId($idrec);	
			else
				$pagos=$emfn->getRepository('schemaBundle:InfoPagoCab')->findById($idpag);	
				
			foreach($pagos as $pago){			
//if($pago->getId()!=173946 ){
				echo "pago:".$pago->getId()." doc:".$pago->getTipoDocumentoId()->getCodigoTipoDocumento()."<br>";
				if($pago->getTipoDocumentoId()->getCodigoTipoDocumento()=='PAG'){
				   //Contabilizacion del pago		   
					$pagosController=new PagosController();
					$pagosController->setContainer($this->container);
					$pagosController->contabilizarPagosAction($pago->getId());							
				}elseif($pago->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANT'){
				   //Contabilizacion del anticipo		   
					$pagosAntController=new AnticiposController();
					$pagosAntController->setContainer($this->container);
					$pagosAntController->contabilizarAnticiposAction($pago->getId());
				}elseif($pago->getTipoDocumentoId()->getCodigoTipoDocumento()=='ANTS'){
				   //Contabilizacion del anticipo		   
					$pagosAntsController=new AnticiposSinClienteController();
					$pagosAntsController->setContainer($this->container);
					$pagosAntsController->contabilizarAnticiposSinClientesAction($pago->getId());
				}
				
//				}
			}
die;			
			$this->get('session')->getFlashBag()->add('notice', $e->getMessage());	
			return $this->redirect($this->generateUrl('inforecaudacion_list_pagos_recaudacion', array('idRec' => $idRecaudacion)));		
		}catch(\Exception $e){
			echo $e->getMessage();die;
			$this->get('session')->getFlashBag()->add('notice', $e->getMessage());
			return $this->redirect($this->generateUrl('inforecaudacion'));			
		}	
	}	
	
	//FUNCION PARA CORREGIR ERRORES DE RESPUESTA DE DEBITOS
	public function corregirRespuestasDebitos($idDebGen){	
	$emfn = $this->getDoctrine()->getManager('telconet_financiero');
	$emfn->getConnection()->beginTransaction();
	try{
	$debitos=$emfn->getRepository('schemaBundle:InfoPagoCab')->obtenerDebitoProcesado($idDebGen);	
	$i=0;
	$c=0;
	$s=0;
	$e=0;
	$m=0;
	echo "Pagos Segun Debitos";
	echo "\n";
	echo "========================================";
	echo "\n";
	echo "Fecha:".date('Y-m-d H:m:i');
	echo "\n";
	foreach($debitos as $debito){
			
		$pagos=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->obtenerPagosMalIngresadosEnDebitos($debito['valorTotal'],$debito['numeroTarjetaCuenta'],$debito['bancoTipoCuentaId']);
		$p=0;
		$pagosencontrados="";
		foreach($pagos as $pago){
			$pagosencontrados.= "    PagoId:".$pago->getPagoId()->getId(); 
			$pagosencontrados.= "\n";						
			$pagosencontrados.= "    PuntoId:".$pago->getPagoId()->getPuntoId(); 
			$pagosencontrados.= "\n";									
			$pagosencontrados.= "    valorPago:".$pago->getValorPago();
			$p++;			
		}

		if($p==1){
			
			$encontroFactura=false;
			$facturasencontradas="";
			$cuantasfact=0;
			//Busca factura por punto
			$factura=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
			->findPrimeraFacturaAbiertaPorPersonaEmpresaRolPorOficinaPorValor($debito['personaEmpresaRolId'], 32,$debito['valorTotal']);	
				if($factura){
					$total_pagos=$emfn->getRepository('schemaBundle:InfoPagoCab')
					->findTotalPagosPorFactura($factura->getId());
					$facturasencontradas.="\n";
					$facturasencontradas.= "               factura:".$factura->getId();
					$facturasencontradas.="\n";
					$facturasencontradas.= "               punto:".$factura->getPuntoId();
					$facturasencontradas.="\n";					
					$facturasencontradas.= "               valor:".$factura->getValorTotal();
					$facturasencontradas.="\n";					
					$facturasencontradas.= "               pagos:".$total_pagos['total_pagos'];
					$facturasencontradas.= "\n";
					$encontroFactura=true;
					$cuantasfact++;
				}

				if(!$encontroFactura){
					$e++;
				}else{

					if($cuantasfact==1){	
						echo "IdDetalleDebito:".$debito['id']."\n";
						echo "NumeroCuenta:".$debito['numeroTarjetaCuenta']."\n";
						echo "puntoId:".$debito['puntoId']."\n";
						echo "valorTotal:".$debito['valorTotal']."\n";
						echo "banco:".$debito['bancoTipoCuentaId']."\n";
						echo "personaEmpresaRolId:".$debito['personaEmpresaRolId']."\n";			
						echo $pagosencontrados;
						echo $facturasencontradas;
						echo "\n";
						
						
						/*$entityFactura=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($factura->getId());
						$entityPagoCab=$emfn->getRepository('schemaBundle:InfoPagoCab')->find($pago->getPagoId()->getId());
						$entityAdmiTipoDocumento=$emfn->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
						->findOneByCodigoTipoDocumento('PAG');
						$entityPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
						$entityPagoCab->setPuntoId($debito['puntoId']);
						$entityPagoCab->setEstadoPago('Cerrado');
						$entityPagoCab->setDebitoDetId($debito['id']);
						$entityPagoCab->setComentarioPago('Pago generado por debito '.$debito['id']);
						$emfn->persist($entityPagoCab);
						
						$entityPagoDet=$emfn->getRepository('schemaBundle:InfoPagoDet')->findOneByPagoId($entityPagoCab->getId());
						$entityPagoDet->setEstado('Cerrado');	
						$entityPagoDet->setReferenciaId($factura->getId());	
						$entityPagoDet->setComentario('Pago generado por debito '.$debito['id']);
						$emfn->persist($entityPagoDet);
						
						$entityFactura->setEstadoImpresionFact('Cerrado');
						$emfn->persist($entityFactura);
						
						//Graba historial de la factura
						$historialFactura=new InfoDocumentoHistorial();
						$historialFactura->setDocumentoId($entityFactura);
						$historialFactura->setEstado($entityFactura->getEstadoImpresionFact());
						$historialFactura->setFeCreacion($entityPagoDet->getFeCreacion());
						$historialFactura->setUsrCreacion($entityPagoDet->getUsrCreacion());
						$emfn->persist($historialFactura);
					
						$emfn->flush();*/
						
						$s++;
					}
					else{
						$m++;
					}
				
				}
				
		}else
		{
			//echo "*tiene mas de un pago encontrado";
			//echo "\n";
			$c++;
		}
		echo "\n";			
		$i++;
		
		
		
		
	}
	echo "Total registros:".$i;
	echo "\n";	
	echo "Total pagos sin conflicto:".$s;
	echo "\n";	
	echo "Total pagos en conflicto:".$c;
	echo "\n";
	echo "Total pagos sin factura:".$e;
	echo "\n";
	echo "Total pagos con mas de una factura:".$m;
	echo "\n";

	$emfn->getConnection()->commit();	

	}catch(\Exception $e){
            $emfn->getConnection()->rollback();
            $emfn->getConnection()->close();	
			echo $e->getMessage();	
		}	
	
	}
	

	
	//FUNCION PARA CORREGIR ERRORES DE RESPUESTA DE DEBITOS
	public function corregirRespuestasDebitosConflicto($idDebGen){	
	$emfn = $this->getDoctrine()->getManager('telconet_financiero');
	$emfn->getConnection()->beginTransaction();
	try{
	$debitos=$emfn->getRepository('schemaBundle:InfoPagoCab')->obtenerDebitoProcesado($idDebGen);	
	$i=0;
	$c=0;
	$s=0;
	$e=0;
	$m=0;
	echo "\n";
	echo "\n";	
	echo "Pagos Segun Debitos";
	echo "\n";
	echo "========================================";
	echo "\n";
	echo "Fecha:".date('Y-m-d H:m:i');
	echo "\n";
	echo "Debito General:".$idDebGen;
	echo "\n";
	echo "\n";
	
	foreach($debitos as $debito){
			
		$pago=$emfn->getRepository('schemaBundle:InfoPagoCab')
		->obtenerPagosMalIngresadosEnDebitosConflicto($debito['valorTotal'],$debito['numeroTarjetaCuenta'],$debito['bancoTipoCuentaId']);
		$p=0;
		$pagosencontrados="";
		$facturasencontradas="";
		$cuantasfact=0;
		
		if($pago){
						echo "IdDetalleDebito:".$debito['id']."\n";
						echo "NumeroCuenta:".$debito['numeroTarjetaCuenta']."\n";
						echo "puntoId:".$debito['puntoId']."\n";
						echo "valorTotal:".$debito['valorTotal']."\n";
						echo "banco:".$debito['bancoTipoCuentaId']."\n";
						echo "personaEmpresaRolId:".$debito['personaEmpresaRolId']."\n";		
		echo "\n";		
		//foreach($pagos as $pago){
			$pagosencontrados.= "\n";		
			$pagosencontrados.= "    PagoId:".$pago[0]->getPagoId()->getId(); 
			$pagosencontrados.= "\n";						
			$pagosencontrados.= "    PuntoId:".$pago[0]->getPagoId()->getPuntoId(); 
			$pagosencontrados.= "\n";									
			$pagosencontrados.= "    valorPago:".$pago[0]->getValorPago();
			$p++;
			//echo "obteniendo facturas...\n";
			$factura=$emfn->getRepository('schemaBundle:InfoPagoCab')
			->findPrimeraFacturaAbiertaPorPersonaEmpresaRolPorOficinaPorValor($debito['personaEmpresaRolId'], 32,"");	
			//echo "obtuvo facturas...\n";
			if($factura){
				//echo "obteniendo total pagos...\n";
				$total_pagos=$emfn->getRepository('schemaBundle:InfoPagoCab')
				->findTotalPagosPorFactura($factura->getId());
				//echo "obtuvo total pagos...\n";
				$facturasencontradas.="\n";
				$facturasencontradas.= "               factura:".$factura->getId();
				$facturasencontradas.="\n";
				$facturasencontradas.= "               punto:".$factura->getPuntoId();
				$facturasencontradas.="\n";					
				$facturasencontradas.= "               valor:".$factura->getValorTotal();
				$facturasencontradas.="\n";					
				$facturasencontradas.= "               pagos:".$total_pagos['total_pagos'];
				$facturasencontradas.= "\n";
				$encontroFactura=true;
				$cuantasfact++;
				//echo "obteniendo entity factura...\n";
				$entityFactura=$emfn->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($factura->getId());
				//echo "obtuvo entity factura...\n";
				//echo "obteniendo entity pago...\n";
				$entityPagoCab=$emfn->getRepository('schemaBundle:InfoPagoCab')->find($pago[0]->getPagoId()->getId());
				//echo "obtuvo entity pago...\n";
				if($entityPagoCab->getValorTotal()<=$entityFactura->getValorTotal()){
				
						/*$entityAdmiTipoDocumento=$emfn->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
						->findOneByCodigoTipoDocumento('PAG');
						$entityPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
						$entityPagoCab->setPuntoId($debito['puntoId']);
						$entityPagoCab->setEstadoPago('Cerrado');
						$entityPagoCab->setDebitoDetId($debito['id']);
						$entityPagoCab->setComentarioPago('Pago generado por debito '.$debito['id']);
						$emfn->persist($entityPagoCab);
						
						$entityPagoDet=$emfn->getRepository('schemaBundle:InfoPagoDet')->findOneByPagoId($entityPagoCab->getId());
						$entityPagoDet->setEstado('Cerrado');	
						$entityPagoDet->setReferenciaId($entityFactura->getId());	
						$entityPagoDet->setComentario('Pago generado por debito '.$debito['id']);
						$emfn->persist($entityPagoDet);
						
						echo "GRABA PAGO Y DETALLE\n";
						if($entityPagoCab->getValorTotal()==$entityFactura->getValorTotal()){						
							$entityFactura->setEstadoImpresionFact('Cerrado');
							$emfn->persist($entityFactura);
							
							//Graba historial de la factura
							$historialFactura=new InfoDocumentoHistorial();
							$historialFactura->setDocumentoId($entityFactura);
							$historialFactura->setEstado($entityFactura->getEstadoImpresionFact());
							$historialFactura->setFeCreacion($entityPagoDet->getFeCreacion());
							$historialFactura->setUsrCreacion($entityPagoDet->getUsrCreacion());
							$emfn->persist($historialFactura);
							$emfn->flush();
							echo "CIERRA FACTURA y GRABA HISTORIAL\n";							
						}*/
				}
				/*else{

						$valorAnticipo=$entityPagoCab->getValorTotal()-$entityFactura->getValorTotal();
						$valorPago=$entityFactura->getValorTotal();
						
						$entityAdmiTipoDocumento=$emfn->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
						->findOneByCodigoTipoDocumento('PAG');
						$entityPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
						$entityPagoCab->setPuntoId($debito['puntoId']);
						$entityPagoCab->setEstadoPago('Cerrado');
						$entityPagoCab->setDebitoDetId($debito['id']);
						$entityPagoCab->setComentarioPago('Pago generado por debito '.$debito['id']);
						$entityPagoCab->setValorTotal($valorPago);
						$emfn->persist($entityPagoCab);
						
						$entityPagoDet=$emfn->getRepository('schemaBundle:InfoPagoDet')->findOneByPagoId($entityPagoCab->getId());
						$entityPagoDet->setEstado('Cerrado');	
						$entityPagoDet->setReferenciaId($entityFactura->getId());	
						$entityPagoDet->setComentario('Pago generado por debito '.$debito['id']);
						$entityPagoDet->setValorPago($valorPago);
						$emfn->persist($entityPagoDet);
						
						
						//CREA ANTICIPO
						$entityAnticipoCab  = new InfoPagoCab();
						$entityAnticipoCab->setEmpresaId('09');
						$entityAnticipoCab->setEstadoPago('Pendiente');
						$entityAdmiTipoDocumento=$emfn->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
						->findOneByCodigoTipoDocumento('ANT');
						$entityAnticipoCab->setTipoDocumentoId($entityAdmiTipoDocumento);				
						//Obtener la numeracion de la tabla Admi_numeracion
						$datosNumeracionAnticipo = $emcom->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($empresaId,$oficinaId,'ANT');
						$secuencia_asig='';$secuencia_asig=str_pad($datosNumeracionAnticipo->getSecuencia(),7, "0", STR_PAD_LEFT); 
						$numero_de_anticipo=$datosNumeracionAnticipo->getNumeracionUno()."-".$datosNumeracionAnticipo->getNumeracionDos()."-".$secuencia_asig;
						//Actualizo la numeracion en la tabla
						$numero_act=($datosNumeracionAnticipo->getSecuencia()+1);
						$datosNumeracionAnticipo->setSecuencia($numero_act);
						$emcom->persist($datosNumeracionAnticipo);
						$emcom->flush();
						
						$entityAnticipoCab->setNumeroPago($numero_de_anticipo);
						$entityAnticipoCab->setOficinaId(32);				
						$entityAnticipoCab->setUsrCreacion($entityPagoCab->getUsrCreacion());
						$entityAnticipoCab->setFeCreacion(new \DateTime($entityPagoCab->getFeCreacion()));						
						$entityAnticipoCab->setComentarioPago('Anticipo ('.$tipoDocumentoAnticipo.') generado por debito');	
						$entityAnticipoCab->setPuntoId($entityPagoCab->getPuntoId());
						$entityAnticipoCab->setDebitoDetId($debito['id']);					
						$entityAnticipoCab->setValorTotal($valorAnticipo);
						$emfn->persist($entityAnticipoCab);	
						
						//CREA LOS DETALLES DEL ANTICIPO
						$entityAnticipoDet= new InfoPagoDet();
						$entityAnticipoDet->setEstado('Pendiente');
						$entityAnticipoDet->setFeCreacion(new \DateTime($entityPagoCab->getFeCreacion()));
						$entityAnticipoDet->setUsrCreacion($entityPagoCab->getUsrCreacion());
						$entityAnticipoDet->setValorPago($valorAnticipo);
						$entityAnticipoDet->setComentario('Anticipo ('.$tipoDocumentoAnticipo.') generado por debito');
						$entityAnticipoDet->setDepositado('N');
						$entityAnticipoDet->setBancoTipoCuentaId($debito['bancoTipoCuentaId']);				
						$entityAnticipoDet->setPagoId($entityAnticipoCab);
						$entityAnticipoDet->setFormaPagoId(3);
						$emfn->persist($entityAnticipoDet);
						$emfn->flush();
						



						
						
						//Cierra factura
						$entityFactura->setEstadoImpresionFact('Cerrado');
						$emfn->persist($entityFactura);
						
						//Graba historial de la factura
						$historialFactura=new InfoDocumentoHistorial();
						$historialFactura->setDocumentoId($entityFactura);
						$historialFactura->setEstado($entityFactura->getEstadoImpresionFact());
						$historialFactura->setFeCreacion($entityPagoDet->getFeCreacion());
						$historialFactura->setUsrCreacion($entityPagoDet->getUsrCreacion());
						$emfn->persist($historialFactura);
						$emfn->flush();
						echo "CIERRA FACTURA y GRABA HISTORIAL\n";				
						echo "EL PAGO ES MAYOR A LA FACTURA/n";
					
				}*/				
				
			}			
			
		echo $pagosencontrados;
		echo "\n";
		echo "\n";
		
		echo $facturasencontradas;
		echo "\n";		
		echo "\n";			
		$i++;			
		}			
		//}

		
		
		
		
	}
	echo "Total registros:".$i;
	echo "\n";	
	/*echo "Total pagos sin conflicto:".$s;
	echo "\n";	
	echo "Total pagos en conflicto:".$c;
	echo "\n";
	echo "Total pagos sin factura:".$e;
	echo "\n";
	echo "Total pagos con mas de una factura:".$m;
	echo "\n";*/
	$emfn->flush();
	$emfn->getConnection()->commit();	

	}catch(\Exception $e){
            $emfn->getConnection()->rollback();
            $emfn->getConnection()->close();	
			echo $e->getMessage();	
		}	
	
	}	
	

        
        public function pruebasReactivacionPorPagos($puntoId){
	$em = $this->getDoctrine()->getManager('telconet');
            $tieneServicios=false;		     
            $valor=$this->obtieneSaldoPorPunto($puntoId);
            $serviciosParaReactivar="";
            //SI NO TIENE DEUDA REACTIVAR SERVICIO
            //$valor=$valor+1;
            if($valor<=0){
                    $serviciosInactivos=$em->getRepository('schemaBundle:InfoServicio')->findServiciosCortadosPorPuntos($puntoId);
                    foreach($serviciosInactivos as $servicio){
                            $serviciosParaReactivar=$serviciosParaReactivar.$servicio->getId()."|";
                            $tieneServicios=true;
                    }
                    if($tieneServicios){
                            echo "cerrar-conservicios\n";
                            echo "Servicios:".$serviciosParaReactivar."\n";
                            $comando = "echo '\n\n\n\n\n\nREACTIVACION EN PAGOS\n--------------------------------------------------------------------' >> /home/telcos/src/telconet/tecnicoBundle/batch/reactivacionMasiva";
                            echo $salida= shell_exec($comando);
                            $comando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/ttco_reactivacionMasiva.jar '".$serviciosParaReactivar."' 'amontero' '127.0.0.1' >> /home/telcos/src/telconet/tecnicoBundle/batch/reactivacionMasiva &";               
                            echo $salida= shell_exec($comando);
                            
                    }else
                    {
                        echo "cerrar-sinservicios";				
                    }
            }else
            {
                        echo "cerrar-sinservicios";				

            }            
            
        }
        
        public function pruebasReactivacionPorRecaudacion($idRecaudacion) {
            $em = $this->getDoctrine()->getManager('telconet');
            $emfn = $this->getDoctrine()->getManager('telconet_financiero');
			$pagosPorRecaudacion=$emfn->getRepository('schemaBundle:InfoPagoCab')->findByRecaudacionId($idRecaudacion);
echo "\n\n\n\n\n\n obtuvo pagos para buscar puntos y reactivar\n\n\n\n";			
echo "punto;tipoDoc;valor;servicio;estadoservicio\n";
                        $serviciosParaReactivar="";
			    $tieneServicios=false;                        
			foreach($pagosPorRecaudacion as $pago){

//die;

				$valor=0;
								
				if($pago->getPuntoId()){$valor=$this->obtieneSaldoPorPunto($pago->getPuntoId());}
if (($pago->getTipoDocumentoId()->getNombreTipoDocumento()=='Anticipo')||
        ($pago->getTipoDocumentoId()->getNombreTipoDocumento()=='Pago')){
    $entityPunto=$em->getRepository('schemaBundle:InfoPunto')->find($pago->getPuntoId());
    $entityServicio=$em->getRepository('schemaBundle:InfoServicio')->findByPuntoId($pago->getPuntoId());
    
/*foreach($entityServicio as $serv){
echo "\n\n\n\n";                            
echo "Punto:".$entityPunto->getLogin()."\n";
echo "TipoDoc:".$pago->getTipoDocumentoId()->getNombreTipoDocumento()."\n";                                
echo "valor:".$valor."\n";    
    echo $serv->getId().":".$serv->getEstado()."\n";
}*/
 foreach($entityServicio as $serv){                            
echo $entityPunto->getLogin().";";
echo $pago->getTipoDocumentoId()->getNombreTipoDocumento().";";                                
echo $valor.";";    
    echo $serv->getId().";".$serv->getEstado()."\n";
}   

        }
//die;			
				if($valor<=0 && $pago->getPuntoId()){
//echo "entro a buscar servicios\n";
					$serviciosInactivos=$em->getRepository('schemaBundle:InfoServicio')
					->findServiciosCortadosPorPuntos($pago->getPuntoId());
					foreach($serviciosInactivos as $servicio){
//echo "encontro servicio\n";
						$serviciosParaReactivar=$serviciosParaReactivar.$servicio->getId()."|";
						$tieneServicios=true;
					}
				}

				
				
			}


//$tieneServicios=true;
echo "\n\n\n\nserviciosParaReactivar:".$serviciosParaReactivar."\n\n";
//echo "<br>tieneServicios?<br>";
//die;			
			if($tieneServicios){
//echo "si tiene Servicios";
//echo "servicios:".$serviciosParaReactivar;
//echo "<br><br><br><br>";
//die;
				//$salida= shell_exec($comando);
				//$mensaje="Se realizo el registro de recaudaciones y se reactivaron los servicios";						
			}
			else{
//echo "no tiene servicios";
//echo "<br><br><br><br>";
//die;			
				$mensaje="Se realizo el registro de recaudaciones y no se encontro servicios para reactivar";
			}	
           
            
        }

        
        public function pruebasObtieneServiciosInCorte($idTipoDoc) {
            $em = $this->getDoctrine()->getManager('telconet');
            $emfn = $this->getDoctrine()->getManager('telconet_financiero');
            $pagosPorRecaudacion=$emfn->getRepository('schemaBundle:InfoPagoCab')->findBy(array( "tipoDocumentoId" => $idTipoDoc, "estadoPago" => "Pendiente"));
			
echo "punto;tipoDoc;valor;servicio;estadoservicio\n";
			foreach($pagosPorRecaudacion as $pago){

			    $tieneServicios=false;
				$valor=0;
				$serviciosParaReactivar="";				
				if($pago->getPuntoId()){$valor=$this->obtieneSaldoPorPunto($pago->getPuntoId());}
if (($pago->getTipoDocumentoId()->getNombreTipoDocumento()=='Anticipo')||
        ($pago->getTipoDocumentoId()->getNombreTipoDocumento()=='Pago')){
    $entityPunto=$em->getRepository('schemaBundle:InfoPunto')->find($pago->getPuntoId());
    $entityServicio=$em->getRepository('schemaBundle:InfoServicio')->findByPuntoId($pago->getPuntoId());
    
 foreach($entityServicio as $serv){                            
echo $entityPunto->getLogin().";";
echo $pago->getTipoDocumentoId()->getNombreTipoDocumento().";";                                
echo $valor.";";    
    echo $serv->getId().";".$serv->getEstado()."\n";
}   

        }
//die;			
				if($valor<=0 && $pago->getPuntoId()){
					$serviciosInactivos=$em->getRepository('schemaBundle:InfoServicio')
					->findServiciosCortadosPorPuntos($pago->getPuntoId());
					foreach($serviciosInactivos as $servicio){
						$serviciosParaReactivar=$serviciosParaReactivar.$servicio->getId()."|";
						$tieneServicios=true;
					}
				}
	
			}		
			if($tieneServicios){
						
			}
			else{		
				$mensaje="Se realizo el registro de recaudaciones y no se encontro servicios para reactivar";
			}	
           
            
        }        
	
        public function pruebasReactivacionPorlogins($logins) {
            $em = $this->getDoctrine()->getManager('telconet');
            $emfn = $this->getDoctrine()->getManager('telconet_financiero');
            $arrayLogins=explode("|",$logins);

                        $serviciosParaReactivar="";
			    $tieneServicios=false;                        
			for($i=0;$i<count($arrayLogins);$i++){
//die;
				$valor=0;
								
				$valor=$this->obtieneSaldoPorPunto($arrayLogins[$i]);

    $entityPunto=$em->getRepository('schemaBundle:InfoPunto')->find($arrayLogins[$i]);
    $entityServicio=$em->getRepository('schemaBundle:InfoServicio')->findByPuntoId($arrayLogins[$i]);
			
				if($valor<=0 && $arrayLogins[$i]){
					$serviciosInactivos=$em->getRepository('schemaBundle:InfoServicio')
					->findServiciosCortadosPorPuntos($arrayLogins[$i]);
					foreach($serviciosInactivos as $servicio){
						$serviciosParaReactivar=$serviciosParaReactivar.$servicio->getId()."|";
						$tieneServicios=true;
					}
				}
	
			}


//$tieneServicios=true;
echo "\n\n\n\nserviciosParaReactivar:".$serviciosParaReactivar."\n\n";
//echo "<br>tieneServicios?<br>";
//die;			
			if($tieneServicios){
//echo "si tiene Servicios";
//echo "servicios:".$serviciosParaReactivar;
//echo "<br><br><br><br>";
//die;                      
				//$comando = "nohup java -jar /home/telcos/src/telconet/tecnicoBundle/batch/ttco_reactivacionMasiva.jar '".$serviciosParaReactivar."' 'amontero' '127.0.0.1' >> /home/telcos/src/telconet/tecnicoBundle/batch/reactivacionMasiva &";               
				//$salida= shell_exec($comando);
				//$mensaje="Se realizo el registro de recaudaciones y se reactivaron los servicios";						
			}
			else{
//echo "no tiene servicios";
//echo "<br><br><br><br>";
//die;			
				$mensaje="Se realizo el registro de recaudaciones y no se encontro servicios para reactivar";
			}	
           
            
        }
        
        /*Motivos Cambio Punto Anticipo*/
        /**
        * @Secure(roles="ROLE_246-1397")
        */
        public function motivoCambioAction() {    
        
            $em = $this->get('doctrine')->getManager('telconet');
            
            $datos = $em->getRepository('schemaBundle:AdmiMotivo')
            ->findMotivosPorModuloPorItemMenuPorAccion('CambioPuntoAnticipo','Pagos','cambioPuntoAnticipo');
            
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
         * Documentación para el método 'actualizaPtoClienteAction'.
         * Este método actualiza el punto de un anticipo
         *
         * @return object $response retorna ('success' | 'msg')
         *
         * @author Alexander Samaniego <awsamaniego@telconet.ec>
         * @version 1.1 23-07-2014
         * @author Ricardo Coello Quezada <rcoello@telconet.ec>
         * @version 1.2 05-12-2016 - Se obtiene el login del IdPtoSession y IdPtoCliente por medio de sus id 
         *                           con el objetivo de actualizar la observación del traslado del punto. 
         *
         */
        /*Motivo Cambio Punto Anticipo*/
        /**
        * @Secure(roles="ROLE_246-1397")
        */
        public function actualizaPtoClienteAction() {
            /*Actualiza el punto del anticipo en la estructura info_pago_cab*/
            $arrayInfoPagoCab       =   NULL;
            $arrayInfoPagoHis       =   NULL;
            $arrayInfoPagoCabSend   =   NULL;
            $boolSuccess            =   false;
            $em                     =   $this->get('doctrine')->getManager('telconet_financiero');
            $objPeticion            =   $this->get('request');
            $intIdMotivo            =   $objPeticion->get('idMotivo');	    
            $intIdAnticipo          =   $objPeticion->get('idAnticipo');
            $intIdPtoCliente        =   $objPeticion->get('idPtoCliente');
            $objSession             =   $objPeticion->getSession();
            $arrayClienteSesion     =   $objSession->get('cliente');
            $arrayPtoClienteSesion  =   $objSession->get('ptoCliente');
            $intIdPuntoSession      =   $arrayPtoClienteSesion['id'];
            $objPtoSession          =   NULL;
            $objPtoCliente          =   NULL;
            $strLoginPtoSession     =   '';
            $strLoginPtoCliente     =   '';
            
            $emComercial            =   $this->getDoctrine()->getManager('telconet');
            $objPtoSession          =   $emComercial->getRepository('schemaBundle:InfoPunto')
                                                    ->find($intIdPuntoSession);
            if ( is_object($objPtoSession) )
            {
                 if($objPtoSession->getLogin() != null)
                 {
                     //Obtengo el login del punto de session por medio de su id.
                     $strLoginPtoSession     =   $objPtoSession->getLogin();
                 }
            }
            
            $objPtoCliente          =   $emComercial->getRepository('schemaBundle:InfoPunto')
                                                    ->find($intIdPtoCliente);
            
            if ( is_object($objPtoCliente) )
            {
                if($objPtoCliente->getLogin() != null)
                {
                    //Obtengo el login del punto cliente por medio de su id.
                    $strLoginPtoCliente     =   $objPtoCliente->getLogin();
                }
            }

            $strObsrevacion         =   'Se realiza el cambio del punto '.$strLoginPtoSession.' al punto '.$strLoginPtoCliente;
            
            $serviceInfoPago        = $this->get('financiero.InfoPago');

            $arrayInfoPagoCabSend   = array('intIdAnticipo'             =>  $intIdAnticipo, 
                                            'strObservacion'            => $objPeticion->get('txtObservacion'), 
                                            'strUser'                   => $objPeticion->getSession()->get('user'), 
                                            'intIdPtoCliente'           => $intIdPtoCliente, 
                                            'intIdMotivo'               => $intIdMotivo,
                                            'strObservacionHistorial'   => $strObsrevacion);

            $arrayInfoPagoCab       = $serviceInfoPago->updateInfoPagoCab($arrayInfoPagoCabSend);
            $response               = new Response();
            $response->headers->set('Content-type', 'text/json');
            $response->setContent(json_encode(array('success' => $arrayInfoPagoCab['boolSuccess'], 'msg' => $arrayInfoPagoCab['strMsg'])));

            return $response;
	}//Fin -> actualizaPtoClienteAction
        
        public function sanear_string($string)
		{

			$string = trim($string);

			$string = str_replace(
				array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
				array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
				$string
			);

			$string = str_replace(
				array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
				array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
				$string
			);

			$string = str_replace(
				array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
				array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
				$string
			);

			$string = str_replace(
				array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
				array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
				$string
			);

			$string = str_replace(
				array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
				array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
				$string
			);

			$string = str_replace(
				array('ñ', 'Ñ', 'ç', 'Ç'),
				array('n', 'N', 'c', 'C',),
				$string
			);

			//Esta parte se encarga de eliminar cualquier caracter extraño
			$string = str_replace(
				array("\\", "¨", "º", "-", "~",
					 "#", "@", "|", "!", "\"",
					 "·", "$", "%", "&", "/",
					 "(", ")", "?", "'", "¡",
					 "¿", "[", "^", "`", "]",
					 "+", "}", "{", "¨", "´",
					 ">", "< ", ";", ",", ":",
					 ".", " "),
				'',
				$string
			);


			return $string;
		}
        
    /**
     * Documentación para el método 'anulaPagosAction'.
     * Este método anula un pago
     *
     * @return object $response retorna ('idPago' | 'motivo' | 'statusAnulPago' | 'usuario' | 'statusContable')
     *
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.1 19-06-2016
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.2 11-01-2017 - Se registra la anulacion del pago con valor permisible (configurado mediante parametro) en la InfoPagoHistorial.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.3 14-08-2017 - Se agrega historial de dependiencia a los pagos relacionados al pago que fue anulado, unicamente para las empresas.
     *                           Si solo a las empresas que CONTABILIZA. Para ello se verifica los detalles del parámetro cabecera que es 
     *                           'PROCESO CONTABILIZACION EMPRESA' en la tabla 'DB_GENERAL.ADMI_PARAMETRO_DET' y se verifica la columna 'VALOR2' 
     *                           si está seteado con el valor de 'S'.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 13-10-2020 - Se agrega cambio de estado de detalle de estado de cuenta relacionado y creación de historial respectivo.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.5 27-04-2021 - Se agrega cambio de estado de detalle de pago automático (funcionalidad retenciones).
     */        
    /**
    * @Secure(roles="ROLE_66-1357")
    */
    public function anulaPagosAction()
    {
        $peticion                = $this->get('request');     
        $idPago                  = $peticion->get('idPago');
        $idMotivo                = $peticion->get('idMotivo');
        $Observacion             = $peticion->get('txtObservacion');
        $usuario                 = $peticion->getSession()->get('user');       
        $strPrefijoEmpresa       = $peticion->getSession()->get('prefijoEmpresa');        
        $emFinanciero            = $this->getDoctrine()->getManager('telconet_financiero');
        $emComercial             = $this->getDoctrine()->getManager();
        $serviceInfoPago         = $this->get('financiero.InfoPago'); 
        $statusAnulPago          = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->anulaPagos($idPago,$idMotivo, $usuario, $Observacion);
        $strIpCreacion           = $peticion->getClientIp();
        $strMsnErrorContabilidad = "";
        $strNombreParametroCab   = "NUMERO_DE_DIAS_ANULAR_PAGOS";
        $strProcesoCab           = "NUMERO_DE_DIAS_AP";
        $strVarlor1Det           = "NUMERO_DIAS_AP";
        $strNumeroDeDiasAp       = "";
        $strMsgErrorPagHistoDep  = "";
        $arrayParametroDet       = array();
        $arrayParametrosPagHisto = array();
        $serviceUtil             = $this->get('schema.Util');
        $emFinanciero->getConnection()->beginTransaction();
        try
        {
            //CONTABILIZA DETALLES DE PAGO
            $arrayParametroDet= 
                $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");
            //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
            if ($arrayParametroDet["valor2"]=="S")
            {    
                $arrayDetallesPago = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($idPago);

                foreach($arrayDetallesPago as $objDetallesPago)
                {
                    $strMsnErrorContabilidad = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                            ->anulaMigracion($objDetallesPago->getId(), $usuario);
                    
                    $strMsnErrorContabilidad =  trim($strMsnErrorContabilidad);
                    if( !empty($strMsnErrorContabilidad) )
                    {
                        $strMsnErrorContabilidad .= $objDetallesPago->getId().'|';
                    }
                }
                
                if( !empty($strMsnErrorContabilidad))
                {
                    $strMsnErrorContabilidad = 'Detalles: '. $strMsnErrorContabilidad;   
                }
                
                
                $entityInfoPagoCab = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->find($idPago);

                if ( is_object($entityInfoPagoCab) ) 
                {
                    $arrayParametrosPagHisto['intIdPago']               = $entityInfoPagoCab->getId();
                    $arrayParametrosPagHisto['strCodigoTipoDocumento']  = $entityInfoPagoCab->getTipoDocumentoId()->getCodigoTipoDocumento();
                    $arrayParametrosPagHisto['strNumeroPago']           = $entityInfoPagoCab->getNumeroPago();
                    $arrayParametrosPagHisto['strEmpresaId']            = $entityInfoPagoCab->getEmpresaId();

                    //Se agrega historial por dependiencia a los pagos relacionados al pago que fue anulado
                    $strMsgErrorPagHistoDep =   $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                             ->agregaHistorialPagosDependientes($arrayParametrosPagHisto);
                    
                    if(!empty($strMsgErrorPagHistoDep))
                    {
                        throw new \Exception($strMsgErrorPagHistoDep);   
                    }
                 }
            }

            //Recupero dias permisibles para la anulacion del pago
            $arrayParametroDet = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                             ->getOne($strNombreParametroCab,
                                                      "FINANCIERO", 
                                                      $strProcesoCab, 
                                                      "", 
                                                      $strVarlor1Det, 
                                                      "", 
                                                      "", 
                                                      $strPrefijoEmpresa);

            if(isset($arrayParametroDet))
            {
                if(isset($arrayParametroDet['valor2']) && !empty($arrayParametroDet['valor2']))
                {
                   $strNumeroDeDiasAp = $arrayParametroDet['valor2'];

                   //Ingresa historial para el pago
                   $entityInfoPagoCab        = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->find($idPago);

                   if ( is_object($entityInfoPagoCab) ) 
                   {
                       $serviceInfoPago->ingresaHistorialPago($entityInfoPagoCab, 'Anulado', new \DateTime('now'), $usuario, $idMotivo, 
                                         'Se anula pago mediante parametro de anulacion NUMERO_DE_DIAS_ANULAR_PAGOS con valor de ' . $strNumeroDeDiasAp .
                                         ' dias');
                   }//( is_object($entityInfoPagoCab) ) 
                }//(isset($arrayParametroDet['valor2']) && !empty($arrayParametroDet['valor2']))
            }//(isset($arrayParametroDet))
            
            if( $strPrefijoEmpresa == "TN" )
            {            
                $intIdPagoAutomaticoDet = $entityInfoPagoCab->getDetallePagoAutomaticoId();
                $boolActualizaPagAutDet = false;
                
                if(isset($intIdPagoAutomaticoDet))
                {
                    $floatTotal = 0;

                    $objInfoPagoAutomaticoDet = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')->find($intIdPagoAutomaticoDet);

                    if(is_object($objInfoPagoAutomaticoDet))
                    {
                        $floatTotalNdi =  $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                       ->getTotalNDI($intIdPagoAutomaticoDet);
                        $floatTotalPag =  $emFinanciero->getRepository('schemaBundle:InfoPagoCab')
                                                       ->getTotalPagosAnt($intIdPagoAutomaticoDet);

                        if( isset($floatTotalNdi) && $floatTotalNdi === floatval($objInfoPagoAutomaticoDet->getMonto()) || 
                            isset($floatTotalPag) && $floatTotalPag === floatval($objInfoPagoAutomaticoDet->getMonto()))
                        {
                            $boolActualizaPagAutDet = true;
                        }                        
                        if($boolActualizaPagAutDet)
                        {
                            $objInfoPagoAutomaticoDet->setEstado('Pendiente'); 
                            $emFinanciero->persist($objInfoPagoAutomaticoDet);
                            $emFinanciero->flush();
                            
                            //Graba historial de detalle de estado de cuenta.
                            $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                            $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet);
                            $objInfoPagoAutomaticoHist->setEstado('Pendiente');
                            $objInfoPagoAutomaticoHist->setObservacion('Detalle cambia de estado de Procesado a Pendiente por anulacion de pago ');
                            $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                            $objInfoPagoAutomaticoHist->setUsrCreacion($usuario);
                            $emFinanciero->persist($objInfoPagoAutomaticoHist);
                            $emFinanciero->flush();
                            $intIdPagAutomatico = $objInfoPagoAutomaticoDet->getPagoAutomaticoId();
                            $arrayDetProcesados = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                               ->findBy(array('pagoAutomaticoId' => $intIdPagAutomatico, 
                                                                               'estado'          => 'Procesado'));

                            if(count($arrayDetProcesados)===0)
                            {
                                $objInfoPagoAutomaticoCab = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                                         ->find($intIdPagAutomatico);
                                if(is_object($objInfoPagoAutomaticoCab))
                                {
                                    $objInfoPagoAutomaticoCab->setEstado('Pendiente'); 
                                    $emFinanciero->persist($objInfoPagoAutomaticoCab);
                                    $emFinanciero->flush();                                   
                                }
                            }
                        }
                    }
                }
                else
                {
                    $arrayInfoPagoDet    = $emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                        ->findBy(array("pagoId"=>$idPago) );

                    foreach ($arrayInfoPagoDet as $objInfoPagoDet)
                    {
                        $intReferenciaPagAutDetId = $objInfoPagoDet->getReferenciaDetPagoAutId();
                        if(isset($intReferenciaPagAutDetId))
                        {
                            $objInfoPagoAutDet    = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                                 ->find($intReferenciaPagAutDetId);
                            $objInfoPagoAutDet->setEstado('Pendiente'); 
                            $emFinanciero->persist($objInfoPagoAutDet);
                            $emFinanciero->flush();
                            
                            $intIdPagAutomatico = $objInfoPagoAutDet->getPagoAutomaticoId();
                        }

                    }
                    //Verifico que no existen detalles procesados
                    if(isset($intIdPagAutomatico))
                    {
                        $arrayDetProcesados = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                           ->findBy(array('pagoAutomaticoId' => $intIdPagAutomatico, 
                                                                           'estado'          => 'Procesado'));

                        if(count($arrayDetProcesados)===0)
                        {
                            $objInfoPagoAutomaticoCab = $emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                                     ->find($intIdPagAutomatico);
                            if(is_object($objInfoPagoAutomaticoCab))
                            {
                                $objInfoPagoAutomaticoCab->setEstado('Pendiente'); 
                                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                                $emFinanciero->flush();                                   
                            }
                        }     
                    }
                }
            }
            
            if ($emFinanciero->getConnection()->isTransactionActive())
            {
                $emFinanciero->getConnection()->commit();
            }
        }
        catch(\Exception $e)
        {
            $statusAnulPago = $strMsgErrorPagHistoDep;
            $serviceUtil->insertError( 'Telcos+', 
                                        'Anulacion de Pago', 
                                        'Error al realizar anulacion del pago . '.$e->getMessage(), 
                                        $usuario, 
                                        $strIpCreacion );
            error_log($e->getMessage());
            
            $emFinanciero->getConnection()->rollback();
        }
        
        $response = 
                new Response(
                    json_encode(array(
                        'idPago'         => $idPago, 
                        'motivo'         => $idMotivo, 
                        'statusAnulPago' => $statusAnulPago, 
                        'usuario'        => $usuario,
                        'statusContable' => (empty($strMsnErrorContabilidad)) ? '' : $strMsnErrorContabilidad,
                        )));
         $response->headers->set('Content-type', 'text/json');
         return $response;
    }
    
    /**
    * @Secure(roles="ROLE_66-1357")
    */
    public function motivosAnulacionPagoAction() {    
    
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
     * Documentación para funcion 'validarFechaPagoAction'.
     * 
     * Función que retorna si la fecha del pago ingresada es válida
     * 
     * @return Response $objResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 17-03-2017
     */
    public function validarFechaPagoAction()
    {
        $objResponse             = new Response();
        $emFinanciero            = $this->get('doctrine')->getManager('telconet_financiero');
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $serviceUtil             = $this->get('schema.Util');
        $strIpCreacion           = $objRequest->getClientIp();
        $strUsuario              = $objSession->get('user');
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $strMensajeRespuesta     = "S";
        $strFechaValidar         = $objRequest->query->get('strFechaValidar') ? $objRequest->query->get('strFechaValidar') : '';
        $strParametroValidar     = $objRequest->query->get('strParametroValidar') ? $objRequest->query->get('strParametroValidar') : '';
        $boolContinuarValidacion = true;
        $strRespuestaValidacion  = "N";

        //ROLE_66-5218 - InfoPagoCab_CrearPagoSinRestrinccion
        //Restringue la creación de un pago por su fecha de documento, proceso o depósito que haya sido ingresada por el usuario
        if(true === $this->get('security.context')->isGranted('ROLE_66-5218') && $strParametroValidar == 'CREACION_PAG_ANT')
        {
            $boolContinuarValidacion = false;
        }
        
        //ROLE_66-5217 - InfoPagoCab_ProcesarDepositoSinRestrinccion
        //Restringue el procesamiento de un depósito por su fecha de processamiento que haya sido ingresada por el usuario
        if(true === $this->get('security.context')->isGranted('ROLE_66-5217') && $strParametroValidar == 'PROCESAR_DEPOSITO')
        {
            $boolContinuarValidacion = false;
        }
        
        try
        {
            //Se verifica si al usuario se le debe validar la fecha del documento ingresado.
            if( $boolContinuarValidacion )
            {
                if( !empty($strPrefijoEmpresa) )
                {
                    if( !empty($strFechaValidar) )
                    {
                        $objFechaValidar = new \DateTime($strFechaValidar);

                        if( is_object($objFechaValidar) )
                        {
                            $strFechaValidar              = $objFechaValidar->format('d-m-Y');
                            $arrayParametrosFechaDeposito = array('strFechaValidar'     => $strFechaValidar,
                                                                  'strPrefijoEmpresa'   => $strPrefijoEmpresa,
                                                                  'strParametroValidar' => $strParametroValidar);
                            error_log(print_R($arrayParametrosFechaDeposito,true));

                            $arrayResultados = $emFinanciero->getRepository('schemaBundle:InfoDeposito')
                                                            ->validarFechaDeposito($arrayParametrosFechaDeposito);

                            if( isset($arrayResultados['strMensajeError']) && !empty($arrayResultados['strMensajeError']) )
                            {
                                throw new \Exception($arrayResultados['strMensajeError']);
                            }
                            else
                            {
                                $strRespuestaValidacion = ( isset($arrayResultados['strRespuestaValidacion'])
                                                            && !empty($arrayResultados['strRespuestaValidacion']) )
                                                           ? $arrayResultados['strRespuestaValidacion'] : 'N';

                                if( $strRespuestaValidacion == "N" )
                                {
                                    $strMensajeRespuesta = "La fecha ingresada al detalle del pago ya no es permitida.";
                                }//( $strRespuestaValidacion == "N" )
                            }//( isset($arrayResultados['strMensajeError']) && !empty($arrayResultados['strMensajeError']) )
                        }
                        else
                        {
                            throw new \Exception('La fecha ingresada no es válida.');
                        }//( is_object($objFechaValidar) )
                    }
                    else
                    {
                        throw new \Exception('No ha ingresado una fecha al detalle del pago.');
                    }//( !empty($strFechaValidar) )
                }
                else
                {
                    throw new \Exception('No tiene una empresa en sessión para validar la fecha del pago.');
                }//( !empty($strPrefijoEmpresa) )
            }//( $boolContinuarValidacion )
        }
        catch(\Exception $e)
        {
            $strMensajeRespuesta = $e->getMessage();
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'InfoPagoCabController.validarFechaPagoAction', 
                                       'Error validar la fecha del detalle del pago ingresado. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }

        $objResponse->setContent($strMensajeRespuesta);

        return $objResponse;
    }
    
    /**
    * Documentación para funcion 'generarRecaudacionAction'.
    * Función que envía los datos necesarios generar una recaudación
    * @author <eholguin@telconet.ec>
    * @version 1.0  14-11-2017
    *
    */    
    public function generarRecaudacionAction()
    {
        return $this->render('financieroBundle:InfoPagoCab:generarRecaudacion.html.twig');
    }
    
    /**
    * Documentación para funcion 'gridCanalesRecaudacionAction'.
    * Función que consulta los canales de recaudación a ser visualizados en el grid respectivo. 
    * @author <eholguin@telconet.ec>
    * @version 1.0 15-11-2017
    * @return JsonResponse objJsonResponse
    */     
    public function gridCanalesRecaudacionAction() 
    {
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $strEmpresaCod              = $objSession->get('idEmpresa');
        $emFinanciero               = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayParametros            = array( 'strEmpresaCod' => $strEmpresaCod, 'strEstado' => 'Activo' );
        $objJsonResponse            = new JsonResponse();
        $arrayCanalesRecaudacion    = $emFinanciero->getRepository('schemaBundle:InfoPagoCab')->getCanalesRecaudacion($arrayParametros);
      
        $objJsonResponse->setData( array('canalesRecaudacion' => $arrayCanalesRecaudacion) );
        
        return $objJsonResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_186-5617")
     * 
     * Documentación para funcion 'generarRecaudacionesArchivoAction'.
     * 
     * Función que genera las recaudaciones iniciadas asociadas a los canales de recaudación seleccionados
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 15-11-2017
     * 
     * @return objeto - render (Renderiza una vista)
     * 
     * Se inicializa $objParametros y se envía como parámetro a la función generarFormatoEnvioRecaudacion()
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.1 07-11-2019
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.2 10-05-2021 - Se realizan cambios por el consumo al nuevo NFS.
     * 
     * @since 1.0
     */     
    public function generarRecaudacionesArchivoAction()      
    {  
        $emFinanciero            = $this->get('doctrine')->getManager('telconet_financiero');
        $emComercial             = $this->getDoctrine()->getManager('telconet');
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $strEmpresaCod           = $objSession->get('idEmpresa');
        $intOficinaId            = $objSession->get('idOficina');
        $strUsrSession           = $objSession->get('user');
        $intIdPersRolSession     = $objSession->get('idPersonaEmpresaRol');
        $strClientIp             = $objRequest->getClientIp();
        $strEmailUsrSesion       = '';
        $arrayCanalesRecaudacion = explode("|",$objRequest->get('canalesRecaudacion'));
        
        $serviceInfoRecaudacion = $this->get('financiero.InfoRecaudacion');
        
        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($intIdPersRolSession);

        if(is_object($objInfoPersonaEmpresaRol))
        {
            $strValorFormaContacto = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                 ->getValorFormaContactoPorCodigo($objInfoPersonaEmpresaRol->getPersonaId(),'MAIL');

            if(!is_null($strValorFormaContacto))
            {
                $strEmailUsrSesion = strtolower($strValorFormaContacto);
            }                
        }         

        $arrayParametroRec                           = array();        
        $arrayParametroRec['strEmpresaCod']          = $strEmpresaCod;
        $arrayParametroRec['intOficinaId']           = $intOficinaId;
        $arrayParametroRec['strUsrCreacion']         = $strUsrSession;
        $arrayParametroRec['strIpCreacion']          = $strClientIp;
        $arrayParametroRec['strEmailUsrSesion']      = $strEmailUsrSesion;
        $arrayParametroRec['strNombreArchivo']       = ' ';       
        $arrayParametroRec['strEstado']              = 'Iniciado';

        try
        {            
            foreach( $arrayCanalesRecaudacion as $intCanalRecaudacionId )
            {
                $objParametros['serviceUtil']                = $this->get('schema.Util');

                $arrayParametroRec['intCanalRecaudacionId']     = $intCanalRecaudacionId;
                $arrayParametroRec['strNombreArchivoEnvioTem']  = "formatoEnvioRecaudacion".date("YmdHis").".txt";
                $arrayRespuestaNfs = $emFinanciero->getRepository('schemaBundle:InfoRecaudacion')
                                                  ->generarFormatoEnvioRecaudacion($arrayParametroRec,
                                                                                   $objParametros);
                if ($arrayRespuestaNfs['strError'] === 'OK')
                {
                    $arrayParametroRec['strNombreArchivoEnvio']  = $arrayRespuestaNfs['strPathNfs'];
                }
                else
                {
                    throw new \Exception('Ocurrió un error al consumir el Ws de NFS.');
                }

                $serviceInfoRecaudacion->guardarArchivoRecaudacion($arrayParametroRec); 
                
            }
                      
        }
        catch(\Exception $e)
        {
            $objSession->getFlashBag()->add('notice', $e->getMessage());
        }
        
        return $this->redirect($this->generateUrl('inforecaudacion'));
    }   
        
     /**
     * @Secure(roles="ROLE_186-5618")
     * 
     * Documentación para funcion 'downloadFormatoEnvioRecaudacionAction'.
     * 
     * Función que retorna el path del formato de envio de la recaudación enviada como parámetro.
     * 
     * @param $id Id de la recaudación.
     * @return Response $objResponse
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0  24-11-2017
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.1  10-05-2021 -Se modifica para que se consuma el nuevo NFS
     * 
     */    
	public function downloadFormatoEnvioRecaudacionAction($id)
	{
        $strArchivoEnvio    = '';
        
		$objEmFinanciero       = $this->getDoctrine()->getManager('telconet_financiero');
		
		$objInfoRecaudacion = $objEmFinanciero->getRepository('schemaBundle:InfoRecaudacion')->find($id);
        
        if(is_object($objInfoRecaudacion))
        {
            $strArchivoEnvio = $objInfoRecaudacion->getArchivoEnvio();
            
            if($strArchivoEnvio)
            {
                $strPath       = $strArchivoEnvio; 
        
                $objContent    = file_get_contents($strPath);

                $objResponse   = new Response();

                $objResponse->headers->set('Content-Type', 'mime/type');
                
                $objResponse->headers->set('Content-Disposition', 'attachment;filename = FormatoEnvioRecaudacion.zip');

                $objResponse->setContent($objContent);
                
                return $objResponse;
            }
            else
            {
                throw new \Exception("No se encuentra el archivo");
            }
            
        }
        else
        {
            throw new \Exception("No se encuentra el archivo.");
        }
	}
    
    /**
     * @Secure(roles="ROLE_450-7497")
     * 
     * Documentación para funcion 'indexPreCancelacionAction'.  
     * 
     * Función que renderiza la página principal de Pre-Cancelación de deuda diferida.   
     * que presenta los valores a precancelar.
     * 
     * @author Hector Lozano < hlozano@telconet.ec>
     * @version 1.0 17-08-2020
     * 
     */ 
    public function indexPreCancelacionAction()
    {
        $objRequest      = $this->getRequest();
        $objSesion       = $objRequest->getSession();
        $emComercial     = $this->getDoctrine()->getManager('telconet');
        $strEmpresaCod   = $objSesion->get('idEmpresa');
        $serviceInfoPago = $this->get('financiero.InfoPago');
        
        $intIdServicio             = 0;
        $arrayValoresPreCancelar   = array();
        $arrayPtoClienteSesion     = $objSesion->get('ptoCliente');

        if($arrayPtoClienteSesion)
        {
            $arrayParametros               = array();
            $intIdPunto                    = $arrayPtoClienteSesion['id'];
            $arrayParametros['intIdPunto'] = $intIdPunto;
            $arrayValoresPreCancelar       = $serviceInfoPago->getValoresDiferidosPreCancelar($arrayParametros);   
            
            $arrayServicio = $emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array('puntoId'=>$intIdPunto));
            $intIdServicio = $arrayServicio[0]->getId();
        }
        
        return $this->render('financieroBundle:InfoPagoCab:indexPreCancelacion.html.twig', 
                                array('arrayValoresPreCancelar'   => $arrayValoresPreCancelar,
                                      'arrayPtoClienteSesion'     => $arrayPtoClienteSesion,
                                      'strEmpresaCod'             => $strEmpresaCod,
                                      'intIdServicio'             => $intIdServicio));
    }
    
    
        
    /**
     * Documentación para funcion 'ejecutarNDIPreCancelacionDiferidaAction'.
     * 
     * Función que invoca al proceso de generación de NDI diferidas por Deuda Diferida.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0, 14-08-2020
     * 
     */
    public function ejecutarNDIPreCancelacionDiferidaAction()
    {

        $objRequest              = $this->getRequest();
        $strEmpresaCod           = $objRequest->get('strCodEmpresa');
        $intIdServicio           = $objRequest->get('intIdServicio');
        $strTipoProceso          = "PreCancelacionDiferida";

        $arrayParametrosNDI                   = array();
        $arrayParametrosNDI['intIdServicio']  = intval($intIdServicio);
        $arrayParametrosNDI['strEmpresaCod']  = $strEmpresaCod;
        $arrayParametrosNDI['strTipoProceso'] = $strTipoProceso;
        
        if( $strEmpresaCod != "" && $intIdServicio != "" && $strTipoProceso != "")
        {
            $serviceInfoPago = $this->get('financiero.InfoPago');
            $strRespuesta    = $serviceInfoPago->ejecutarNDIPreCancelacionDiferida($arrayParametrosNDI);
        }
        else
        {
            $strRespuesta = "ERROR: Parametros vacios!";
        }

        $objResponse = new Response(json_encode($strRespuesta));
		$objResponse->headers->set('Content-type', 'text/json');
        
		return $objResponse;


    }
    
    
}
