<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Form\InfoDocumentoFinancieroCabType;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroImp;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;

use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
/**
 * InfoNotaCredito controller.
 *
 */
class InfoNotaCreditoInternaController extends Controller
{

    /**
     * Lista todas las notas de creditos internas
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1
     * @since   1.0
     */
    /**
     * @Secure(roles="ROLE_242-1")
    */
    public function indexAction()
    {
		
        $em = $this->getDoctrine()->getManager("telconet_financiero");
        
        return $this->render('financieroBundle:InfoNotaCreditoInterna:index.html.twig');
    }

    /**
     * Documentacion para el método 'showAction'
     * Permite Mostrar el documento Nota de Credito Interna.
     * 
     * @author Unknow     
     * @since   1.0
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 23-11-2016 
     * Se habilita el escenario para generar NCI con la opción "Valor por detalle"
     * Se verifica si el cliente paga iva
     * Se verifica el IVA de la factura con la cual debe aplicar la NCI
     * Se obtiene la descripcion interna para mostrarla solo para TN
     * Muestra Historial del Documento NCI
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.2 26-12-2018 - Se realizan cambios para que la NC considere el impuesto ITBMS para Panama.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.3 28-04-2020 - Se agregan detalles de las características del documento (NCI).
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.4 02-06-2020 - Se realizan cambios para que se visualicen un mejor detalle de las características y los
     * numero_factura_Sri en la columna de valores de características.
     */
    public function showAction($id)
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $emFinanciero      = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral         = $this->getDoctrine()->getManager("telconet_general");
        $emComercial       = $this->getDoctrine()->getManager("telconet");
        
        $objInfoDocumentoFinancieroCab = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!is_object($objInfoDocumentoFinancieroCab)) 
        {            
            $this->get('session')->getFlashBag()->add('notice', 'No se encontro documento Nota de crédito interna');            
            return $this->render('financieroBundle:InfoNotaCreditoInterna:index.html.twig');
        }

		//Obteniendo la referencia de factura
		$intReferenciaDocumentoId = $objInfoDocumentoFinancieroCab->getReferenciaDocumentoId();
		$objFacturaAplicada       = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intReferenciaDocumentoId);
		
        $deleteForm = $this->createDeleteForm($id);
                
        $objInfoPunto             = $emComercial->getRepository('schemaBundle:InfoPunto')->find($objInfoDocumentoFinancieroCab->getPuntoId());
        $objInfoPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->find($objInfoPunto->getPersonaEmpresaRolId()->getId());
		$objInfoOficina           = $emFinanciero->getRepository('schemaBundle:InfoOficinaGrupo')
                                                 ->find($objInfoDocumentoFinancieroCab->getOficinaId());
        
        $informacion_persona['puntoId'] = $objInfoPunto->getLogin();
        $informacion_persona['cliente'] = sprintf("%s", $objInfoPersonaEmpresaRol->getPersonaId());
        
        if (!is_object($objInfoPersonaEmpresaRol)) 
        {            
            $this->get('session')->getFlashBag()->add('notice', 'No se encontro registro del Cliente.');            
            return $this->render('financieroBundle:InfoNotaCreditoInterna:index.html.twig');
        }
                
       /*
        * Verifica el IVA con el cual se aplica a la NCI
        */
        $strIvaAplicado = '';
        $strPagaIva     = $objInfoPersonaEmpresaRol->getPersonaId() ? ( $objInfoPersonaEmpresaRol->getPersonaId()->getPagaIva() ? 'Si' : 'No' ) 
                          : 'No';
        
        if( $strPagaIva == 'Si' )
        {
            if(is_object($objFacturaAplicada))
            {
                $intFacturaAplicada             = $objFacturaAplicada->getId();
                $objInfoDocumentoFinancieroDet  = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroDet")
                                                               ->findByDocumentoId($intFacturaAplicada);
                                
                if(!empty($objInfoDocumentoFinancieroDet) && isset($objInfoDocumentoFinancieroDet))
                {
                    foreach($objInfoDocumentoFinancieroDet as $objDetFactura)
                    {
                        if( $strIvaAplicado == '' )
                        {
                            $objInfoDocumentoFinancieroImp = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                          ->findByDetalleDocId( $objDetFactura->getId() );

                            if(!empty($objInfoDocumentoFinancieroImp) && isset($objInfoDocumentoFinancieroImp))
                            {
                                foreach($objInfoDocumentoFinancieroImp as $objItemDetalleImpuesto)
                                {
                                    $objAdmiImpuesto = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                                 ->findOneById( $objItemDetalleImpuesto->getImpuestoId() );

                                    if( is_object($objAdmiImpuesto) )
                                    {
                                        if($strPrefijoEmpresa == 'TNP')
                                        {
                                            if($objAdmiImpuesto->getTipoImpuesto() == "ITBMS")
                                            {
                                                $strIvaAplicado = $objItemDetalleImpuesto->getPorcentaje();
                                            }
                                        }
                                        else
                                        {
                                            if($objAdmiImpuesto->getTipoImpuesto() == "IVA")
                                            {
                                                $strIvaAplicado = $objItemDetalleImpuesto->getPorcentaje();
                                                break;
                                            }
                                        }
                                    }//if( is_object($objAdmiImpuesto) )
                                }//foreach($objInfoDocumentoFinancieroImp as $objItemDetalleImpuesto)
                            }//(!empty($objInfoDocumentoFinancieroImp) && isset($objInfoDocumentoFinancieroImp))
                        }//( $strIvaAplicado == '' )
                    }//foreach($objInfoDocumentoFinancieroDet as $objDetFactura)
                }//(!empty($objInfoDocumentoFinancieroDet) && isset($objInfoDocumentoFinancieroDet))
            }//(is_object($objFacturaAplicada))
        }//( $strPagaIva == 'Si' )
        
        /*
         * Se obtiene la descripcion Interna de la nota de credito interna que aplica solo para TN
         */
        $strDescripcionInterna = '';
        
        if( $strPrefijoEmpresa == "TN" )
        {
            $objAdmiCaracteristicaTipoResponsable = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy( array('estado'                    => 'Activo', 
                                                                                    'descripcionCaracteristica' => 'DESCRIPCION_INTERNA_NC') );
            if (!is_object($objAdmiCaracteristicaTipoResponsable))
            {                
                $this->get('session')->getFlashBag()->add('notice', 'No encontro Caracteristica DESCRIPCION_INTERNA_NC.');            
                return $this->render('financieroBundle:InfoNotaCreditoInterna:index.html.twig');
            }
            $objInfoDocumentoCaracteristicaTipo = $emFinanciero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                               ->findOneBy(array
                                                                          ('documentoId'      => $objInfoDocumentoFinancieroCab,
                                                                           'caracteristicaId' => $objAdmiCaracteristicaTipoResponsable->getId()) );

            if(($objInfoDocumentoCaracteristicaTipo))
            {
                $strDescripcionInterna = $objInfoDocumentoCaracteristicaTipo->getValor();
            }
        }
        
        // Obtengo Historial de la NCI generada
        $objInfoDocumentoHistorial = $emFinanciero->getRepository('schemaBundle:InfoDocumentoHistorial')
                                                  ->findByDocumentoId(array('id' => $id), array('feCreacion' => 'ASC'));
        $arrayHistorial = array();
                
        if(!empty($objInfoDocumentoHistorial) && isset($objInfoDocumentoHistorial))    
        {
            $intIndice = 0;
            foreach($objInfoDocumentoHistorial as $objHistorial)
            {

                if($objHistorial->getMotivoId() != null)
                {
                    $objAdmiMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($objHistorial->getMotivoId());

                    if(is_object($objAdmiMotivo))
                    {
                        $strNombreMotivo = $objAdmiMotivo->getNombreMotivo();
                    }
                    else
                    {
                        $strNombreMotivo = "";
                    }
                }
                else
                {
                    $strNombreMotivo = "";
                }
                $arrayHistorial[$intIndice]['motivo']       = $strNombreMotivo;
                $arrayHistorial[$intIndice]['estado']       = $objHistorial->getEstado();
                $arrayHistorial[$intIndice]['fe_creacion']  = strval(date_format($objHistorial->getFeCreacion(), "d/m/Y G:i"));
                $arrayHistorial[$intIndice]['usr_creacion'] = $objHistorial->getUsrCreacion();
                $arrayHistorial[$intIndice]['observacion']  = $objHistorial->getObservacion();

                $intIndice++;
            }
        }
        //Se verifica si el documento posee características asociadas.
        $arrayCaracteristicas                = array();
        $arrayObjInfoDocumentoCaracteristica = $emFinanciero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                            ->findBy(array('documentoId' => $objInfoDocumentoFinancieroCab, 
                                                                           'estado'      => 'Activo'));
        
        if(!empty($arrayObjInfoDocumentoCaracteristica))
        {
            foreach ($arrayObjInfoDocumentoCaracteristica as $objInfoDocumentoCaracteristica):
                if(is_object($objInfoDocumentoCaracteristica))
                {            
                    $objAdmiCaracteristica  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->find($objInfoDocumentoCaracteristica->getCaracteristicaId());
                    if(is_object($objAdmiCaracteristica))
                    {
                        $arrayCaracteristica                   = array();
                        $strDetalleCarac    = $objAdmiCaracteristica->getDetalleCaracteristica();
                        $strDescricionCarac = $objAdmiCaracteristica->getDescripcionCaracteristica();
                        if(!empty($strDetalleCarac))
                        {
                            $arrayCaracteristica['strDescripcion'] = $objAdmiCaracteristica->getDetalleCaracteristica();
                        }
                        else
                        {
                            $arrayCaracteristica['strDescripcion'] = $objAdmiCaracteristica->getDescripcionCaracteristica();
                        }
                        if($strDescricionCarac === 'ES_SOL_FACTURA')
                        {
                            if(is_numeric($objInfoDocumentoCaracteristica->getValor())) 
                            {
                                $objInfoDocumentoFinancieroCabNCI = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                 ->findOneById($objInfoDocumentoCaracteristica->getValor());

                                if( is_object($objInfoDocumentoFinancieroCabNCI) )
                                {
                                    $arrayCaracteristica['strValor'] = $objInfoDocumentoFinancieroCabNCI->getNumeroFacturaSri();
                                }
                                else
                                {
                                    $arrayCaracteristica['strValor'] = $objInfoDocumentoCaracteristica->getValor();
                                }
                            }
                            else
                            {
                                $arrayCaracteristica['strValor'] = $objInfoDocumentoCaracteristica->getValor();

                            }

                        }
                        else
                        {
                            $arrayCaracteristica['strValor'] = $objInfoDocumentoCaracteristica->getValor();
                        }

                        $arrayCaracteristicas[]                = $arrayCaracteristica;
                    }
                }
            endforeach;
        }
        
        return $this->render('financieroBundle:InfoNotaCreditoInterna:show.html.twig', array(
            'entity'                => $objInfoDocumentoFinancieroCab,
            'delete_form'           => $deleteForm->createView(),
            'info_cliente'          => $informacion_persona, 
            'punto'                 => $objInfoPunto,
			'oficina'               => $objInfoOficina,
			'fact_referencia'       => $objFacturaAplicada->getNumeroFacturaSri(),
            'boolTieneSaldo'        => true,
            'strPrefijoEmpresa'     => $strPrefijoEmpresa,
            'strDescripcionInterna' => $strDescripcionInterna,
            'strIvaAplicado'        => $strIvaAplicado,
            'strPagaIva'            => $strPagaIva,
            'arrayHistorial'        => $arrayHistorial,
            'arrayCaracteristicas'    => $arrayCaracteristicas,
            'intCountCaracteristicas' => count($arrayCaracteristicas)
            ));
    }

    /**
     * @Secure(roles="ROLE_242-2")
     * Permite presentar el formulario para crear las notas de creditos internas
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1
     * @since   1.0
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 16-11-2016 
     * Se habilita el escenario para generar NCI con la opción "Valor por detalle"
     * Se verifica si el cliente paga iva
     * Se verifica el IVA de la factura con la cual debe aplicar la NCI
     * Se obtiene el valor total de NC y NCI aplicadas a la factura, tambien el numero y valor total de la factura
     * Pregunta si la factura ya tiene una NC o NCI en estado Pendiente o Aprobada, para no dejar crear otra NCI
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 02-12-2016 - Se verifica si la factura fue compensada, para compensar respectivamente a la nota de crédito interna. 
     *                           Para ello se verifica si el campo 'DESCUENTO_COMPENSACION' tiene un valor mayor a cero. 
     *                           Adicional se verifica si tiene una NCI en estado 'Activo' para no poder dejarla crear otra NCI por valor original
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 09-02-2017 - Se elimina la validación por empresa de la compensación solidaria para que tanto MD y TN compensen al 2% las
     *                           notas de crédito aplicadas a facturas realizadas con compensación solidaria.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.8 29-03-2018 - Se agrega envío de bandera para validar si la factura sobre la que se aplicará la nota de crédito
     *                           es por contrato digital.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.9 10-10-2018 - Se agrega que sea visible el IVA_E 14% ya que no se lo considera en la pantalla de NC en la presentacion y calculos     
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 2.0 26-12-2018 - Se realizan cambios para que la NCI considere el impuesto ITBMS para Panama.
     * 
     */    
    public function newAction($id)
    {
        $emComercial            = $this->getDoctrine()->getManager();
        $emFinanciero           = $this->getDoctrine()->getManager("telconet_financiero");
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strCliente             = $objSession->get('cliente');
        $arrayPtoCliente        = $objSession->get('ptoCliente');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $arrayParametrosSend    = array('intIdDocumento'               => $id, 
                                        'arrayEstadoTipoDocumentos'    => ['Activo'], 
                                        'arrayCodigoTipoDocumento'     => ['NC','NCI'], 
                                        'arrayEstadoNC'                => ['Activo']);

        //Obtiene el valor total de NC y NCI aplicadas a la factura, tambien el numero y valor total de la factura
        $arrayGetValorTotalNcByFactura  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                       ->getValorTotalNcByFactura($arrayParametrosSend);

        $entity = new InfoDocumentoFinancieroCab();
        $form   = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        
        //obtiene la entidad del tipo de documento NC => nota de credito
        $objAdmiTipoDocumentoFinancieroNc  = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                          ->findOneBy(array('codigoTipoDocumento' => 'NC'));
        if(!is_object($objAdmiTipoDocumentoFinancieroNc))
        {            
            $this->get('session')->getFlashBag()->add('notice', 'No se encontro el Codigo de Tipo de Documento para Notas de Crédito');
            return $this->render('financieroBundle:InfoNotaCreditoInterna:index.html.twig');
        }
        //obtiene la entidad del tipo de documento NCI => nota de credito Interna
        $objAdmiTipoDocumentoFinancieroNci = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                          ->findOneBy(array('codigoTipoDocumento' => 'NCI'));        
        if(!is_object($objAdmiTipoDocumentoFinancieroNci))
        {            
            $this->get('session')->getFlashBag()->add('notice', 'No se encontro el Codigo de Tipo de Documento para Notas de Crédito Interna');
            return $this->render('financieroBundle:InfoNotaCreditoInterna:index.html.twig');
        }
        //Obtiene un registro de un documento NC y NCI relacionada a la factura
        $objInfoDocumentoFinancieroCabNc = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                        ->findOneBy(array
                                                                   (
                                                                    'referenciaDocumentoId' => $id,
                                                                    'estadoImpresionFact'   => array('Pendiente', 'Aprobada'),
                                                                    'tipoDocumentoId'       => array($objAdmiTipoDocumentoFinancieroNc->getId(),
                                                                                                     $objAdmiTipoDocumentoFinancieroNci->getId())
                                                                   ));
        
		//carga los motivos de las NCI
		$objAdmiMotivo = $emComercial->getRepository('schemaBundle:AdmiMotivo')
                                     ->findMotivosPorModuloPorItemMenuPorAccion("nota_de_credito_interna","Ver nota de credito","new");
		$arrayParametros = array(
                                    'entity'            => $entity,
                                    'form'              => $form->createView(),
                                    'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                    'intIvaAplicado'    => 0,
                                    'strEsCompensado'   => 'N'
                                );
        
        /**
         * Bloque que verifica si la factura es compensada, para compensar la NC
         */
        $objInfoDocumentoFinancieroCab = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findOneById($id);

        if( is_object($objInfoDocumentoFinancieroCab) )
        {
            $strUsrCreacionFactura  = $objInfoDocumentoFinancieroCab->getUsrCreacion();
            
            $floatValorCompensacion = $objInfoDocumentoFinancieroCab->getDescuentoCompensacion();

            if( !empty($floatValorCompensacion) && floatval($floatValorCompensacion) > 0 )
            {
                $arrayParametros['strEsCompensado'] = 'S';
            }//( !empty($floatValorCompensacion) && floatval($floatValorCompensacion) > 0 )
        }//( is_object($objInfoDocumentoFinancieroCab) )
				
		$arrayParametros['strMensajeError']   = 'No existe Error';
        $arrayParametros['boolTieneNc']       = false;
        $arrayParametros['boolTieneNcActiva'] = false;
        
        //Valida que no tenga errores
        if(!isset($arrayGetValorTotalNcByFactura['strMensajeError']) && empty($arrayGetValorTotalNcByFactura['strMensajeError']))
        {
            $arrayParametros['fltValorFactura']     = round($arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalFac'], 2);
            $arrayParametros['strNumeroFacturaSri'] = $arrayGetValorTotalNcByFactura['arrayResultado'][0]['numeroFacturaSri'];
        }
        else
        {
            $arrayParametros['fltValorFactura']     = 0;
            $arrayParametros['strNumeroFacturaSri'] = $arrayGetValorTotalNcByFactura['strMensajeError'];
        }
        //Verifica si array Puntos viene con datos
        if(isset($arrayPtoCliente) && !empty($arrayPtoCliente))
        {
            $arrayParametros['punto_id']    = $arrayPtoCliente;
            $arrayParametros['cliente']     = $strCliente;
            
            /*
             * Verifico si paga IVA
             */
            $objInfoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($arrayParametros['punto_id']['id_persona']);
            
            if(is_object($objInfoPersona))
            {
                $arrayParametros['strPagaIva'] = ($objInfoPersona->getPagaIva() == 'S') ? 'Si' : 'No';
            }
            /*
             * Fin Verifico si paga IVA
             */
        }
        
		 //Verifica si se envio un id en la URL y el IVA con el cual se aplica a la NCI
        if(isset($id) && !empty($id))
        {
            $arrayParametros['idFactura']   = $id;
            $fltTotalDetalleSinImpuesto     = 0;
            $objInfoDocumentoFinancieroDet  = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroDet")->findByDocumentoId($id);
            
            foreach($objInfoDocumentoFinancieroDet as $objDetFactura)
            {
                if( $arrayParametros['intIvaAplicado'] == 0 && $arrayParametros['strPagaIva'] == 'Si' )
                {
                    $objInfoDocumentoFinancieroImp = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                  ->findByDetalleDocId( $objDetFactura->getId() );

                    if(!empty($objInfoDocumentoFinancieroImp) && isset($objInfoDocumentoFinancieroImp) )                    
                    {
                        foreach($objInfoDocumentoFinancieroImp as $objDetalleImpuesto)
                        {
                            $objAdmiImpuesto = $emComercial->getRepository('schemaBundle:AdmiImpuesto')
                                                           ->findOneById( $objDetalleImpuesto->getImpuestoId() );

                            if(!empty($objAdmiImpuesto) && isset($objAdmiImpuesto))
                            {
                                if( $strPrefijoEmpresa == 'TNP' )
                                {
                                    if( $objAdmiImpuesto->getTipoImpuesto() == "ITBMS" )
                                    {
                                        $arrayParametros['intIvaAplicado'] = $objDetalleImpuesto->getPorcentaje();
                                    }
                                }
                                else
                                {
                                    if ($objAdmiImpuesto->getTipoImpuesto() == "IVA" || $objAdmiImpuesto->getTipoImpuesto() == "IVA_E") 
                                    {                                
                                        $arrayParametros['intIvaAplicado'] = $objDetalleImpuesto->getPorcentaje();
                                        break;  
                                    }
                                }
                            }
                        }
                    }
                }
                    
                $fltTotalDetalleSinImpuesto = $fltTotalDetalleSinImpuesto 
                                            + ($objDetFactura->getPrecioVentaFacproDetalle() * $objDetFactura->getCantidad());
            }//foreach($objInfoDocumentoFinancieroDet as $objDetFactura)
        }        
        $arrayParametros['esElectronica']        = 'No';
        $arrayParametros['valorSubTotalFactura'] = $fltTotalDetalleSinImpuesto;       
					         
        if(!empty($objAdmiMotivo) && isset($objAdmiMotivo))        
        {
            $arrayParametros['listadoMotivos'] = $objAdmiMotivo;
        }
        
        //Pregunta si la factura ya tiene una NC o NCI en estado Pendiente o Aprobada, para no dejar crear otra NCI
        if(is_object($objInfoDocumentoFinancieroCabNc))
        {
            $strEstadoNotaCredito = $objInfoDocumentoFinancieroCabNc->getEstadoImpresionFact()
                                    ? $objInfoDocumentoFinancieroCabNc->getEstadoImpresionFact() : '';
            
            if( empty($strEstadoNotaCredito) )
            {
                $arrayParametros['boolTieneNc'] = true;
                
                $arrayParametros['strObservacionNotaCredito'] = "Esta factura tiene una nota de credito en estado NULL. Por favor, comunicarse con ".
                                                                "Sistemas para revisar el inconveniente.";
            }//( empty($strEstadoNotaCredito) )
            else
            {
                if( $strEstadoNotaCredito == 'Activo')
                {
                    $arrayParametros['boolTieneNcActiva'] = true;
                }
                else
                {
                    $arrayParametros['boolTieneNc'] = true;
                }

                //Pregunta si el estado es Pendiente para cambiar el texto de la presentacion en el twig
                if( $strEstadoNotaCredito == 'Pendiente')
                {
                    $strEstadoNc = 'solicite la aprobación';
                }

                //Pregunta si el estado es Aprobada para cambiar el texto de la presentacion en el twig
                if( $strEstadoNotaCredito == 'Aprobada')
                {
                    $strEstadoNc = 'espere la autorización por parte del SRI';
                }
                
                //Concatena la observacion segun el estado obtenido
                $arrayParametros['strObservacionNotaCredito'] = "Esta factura tiene una nota de credito en estado ".$strEstadoNotaCredito. ", ".
                                                                "por favor " . $strEstadoNc . " para proceder a crear una nueva Nota de Credito ".
                                                                "Interna";
            }//else( empty($strEstadoNotaCredito) )
        }//(is_object($objInfoDocumentoFinancieroCabNc))
        
        //Si existio un error en la consulta del valor total de la nota de credito lo presentara en el twig
        if(isset($arrayGetValorTotalNcByFactura['strMensajeError']) && !empty($arrayGetValorTotalNcByFactura['strMensajeError']))
        {
            $arrayParametros['strMensajeError'] = $arrayGetValorTotalNcByFactura['strMensajeError'];
        }
        //Calcula el saldo disponible FAC - SUM(NC Y NCI)
        $fltSaldo                       = 0;
        $fltSaldo                       = $arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalFac'] 
                                        - $arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalNc'];
        $arrayParametros['fltSaldo']    = round($fltSaldo, 2);
        
        $arrayParametros['strObservacionConSaldo'] = "Usted cuenta con $" . round($fltSaldo, 2) 
                                                    . " de saldo disponible para crear una nueva nota de crédito interna";
        $arrayParametros['boolTieneSaldo']         = true;
        //Si el saldo <= 0 no podra crear una nueva nota de credito y se mostrara un mensaje en el twig
        if($fltSaldo <= 0)
        {
            $arrayParametros['boolTieneSaldo']          = false;
            $arrayParametros['strObservacionSinSaldo']  = "No puede crear notas de crédito internas a ésta factura ya que no cuenta con"
                                                        . " saldo disponible. Saldo: " . round($fltSaldo, 2);
        }
        
        $arrayParametros['boolFacturaContrato'] = false;
        
        if($strUsrCreacionFactura === 'telcos_contrato')
        {
            $arrayParametros['boolFacturaContrato'] = true;
            $arrayParametros['strObservacionFact']  = "No puede crear nota de crédito interna a ésta factura ya que pertenece al"
                                                        . " proceso automático de Contrato Digital ";
        }        
        
        return $this->render('financieroBundle:InfoNotaCreditoInterna:new.html.twig', $arrayParametros);
    }

    /**
     * @Secure(roles="ROLE_242-2")
     * 
     * Crea notas de creditos internas
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1
     * @since   1.0
     * @author Gina Villalba <gvillalba@telconet.ec>
     * Se agregan los roles
     * @version 1.2
     * @since   1.1
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.3 22-11-2016 
     * Se agrega la opción de 'Valor Por Detalle'
     * Se verifica si el cliente paga iva
     * Se verifica Saldo de la Factura     
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 13-12-2016 - Se valida que la NCI no se pueda crear con valor cero.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 21-02-2017 - Se verifica si la NCI debe compensar o no para enviar dicha variable 'strEsCompensado' al twig new.html.twig cuando
     *                           no se pueda crear la nota de crédito interna correspondiente.
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.6 15-10-2020 - Se agrega nuevo parámetro 'intEditValoresNcCaract' al $arrayParametrosNc para el proceso de 
     *                           generar nota de crédito.
     */
    public function createAction(Request $objRequest)
    {
		$emFinanciero           = $this->getDoctrine()->getManager("telconet_financiero");
        $objInformacionGrid     = $objRequest->get('listado_informacion');     
        $intIdFactura           = $objRequest->get('factura_id');
        $arrayInformacionGrid   = json_decode($objInformacionGrid);
        $objSession             = $objRequest->getSession();
        $arrayPtoCliente        = $objSession->get('ptoCliente');
        $intIdOficina           = $objSession->get('idOficina');
        $strIdEmpresa           = $objSession->get('idEmpresa');
        $strUser                = $objSession->get('user');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $intIdPunto             = $arrayPtoCliente['id'];
        $strEstado              = "Pendiente";
        $strMotivo              = $objRequest->get('motivos');
        $arrayMotivo            = explode("-", $strMotivo);
        $strObservacion         = $objRequest->get('observacion');
        $strPagaIva             = $objRequest->get('strPagaIva');
        $strDescripcionInterna  = $objRequest->get('descripcionInterna');
        $strIpCreacion          = $objRequest->getClientIp();;
        $strEsElectronica       = 'N';
        $intVerificacion        = 0;
		$serviceUtil            = $this->get('schema.Util');
        $serviceNotaCredito     = $this->get('financiero.InfoNotaCredito');
        
        $arrayParametrosValidar             = array('intIdDocumento' => $intIdFactura);
        $arrayParametros                    = array();
        $arrayParametros['strEsCompensado'] = $serviceNotaCredito->validarCompensacionNotaCredito($arrayParametrosValidar);
        
        if($arrayInformacionGrid != '')
        {
            switch($arrayInformacionGrid[0]->tipoNC)
            {
                case 'ValorOriginal':
                    $strTipoNotaCredito = 'Valor Original';
                    break;
                case 'PorServicio':
                    $strTipoNotaCredito = 'Porcentaje del Servicio: ' . $arrayInformacionGrid[0]->porcentajeNc;
                    break;
                case 'ValorPorDetalle':
                    $strTipoNotaCredito = 'Valor Por Detalle';
                    break;
                case 'PorDias':
                    $strTipoNotaCredito = 'Proporcional por dias del ' . 
                    date('d-m-Y', strtotime(explode('T', $arrayInformacionGrid[0]->fechaDesdeNc)[0])) . 
                    ' al ' . 
                    date('d-m-Y', strtotime(explode('T', $arrayInformacionGrid[0]->fechaHastaNc)[0]));
                    break;
                default:
                    $strTipoNotaCredito = 'El tipo de nota de crédito interna enviado a crear no existe.';
            }
        }
        //Pregunta si tiene un punto en sesion caso contrario no permitira crear la NCI
        if(isset($intIdPunto) && !empty($intIdPunto))
        {
            //Verificar si el punto en session corresponde al punto de la factura selecionada
            $strCodigoTipoDocumento = array('FAC','FACP');
            $intVerificacion        = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                   ->getResultadoValidacionDocumento($intIdFactura,$intIdPunto,$strCodigoTipoDocumento);
            
            //Si el documento para aplicar nota de credito pertenece al login en sesion
            if($intVerificacion>0)
            {
                $emFinanciero->getConnection()->beginTransaction();
                
                try
                {
                    //Se definen los parametros a enviar al service para la creacion de la nota de credito
                    $arrayParametrosNc["estado"]                = $strEstado;
                    $arrayParametrosNc["codigo"]                = "NCI";
                    $arrayParametrosNc["informacionGrid"]       = $arrayInformacionGrid;
                    $arrayParametrosNc["punto_id"]              = $intIdPunto;
                    $arrayParametrosNc["oficina_id"]            = $intIdOficina;
                    $arrayParametrosNc["observacion"]           = preg_replace('/[^\da-z]/i', ' ', $strObservacion);
                    $arrayParametrosNc["facturaId"]             = $intIdFactura;
                    $arrayParametrosNc["user"]                  = $strUser;
                    $arrayParametrosNc["motivo_id"]             = $arrayMotivo;
                    $arrayParametrosNc["intIdEmpresa"]          = $strIdEmpresa;
                    $arrayParametrosNc["strEselectronica"]      = $strEsElectronica;
                    $arrayParametrosNc["strPrefijoEmpresa"]     = $strPrefijoEmpresa;
                    $arrayParametrosNc["strPagaIva"]            = $strPagaIva;
                    $arrayParametrosNc["strIpCreacion"]         = $strIpCreacion;
                    $arrayParametrosNc["strDescripcionInterna"] = $strDescripcionInterna;
                    $arrayParametrosNc["strTipoNotaCredito"]    = 'El tipo de la nota de crédito interna es '.$strTipoNotaCredito;
                    $arrayParametrosNc["intEditValoresNcCaract"] = 0;
                    
                    //Obtiene le valor total de la nueva nota de credito interna.
                    $arrayValorTotalNcACrear = $serviceNotaCredito->obtieneValorTotalNcACrear($arrayParametrosNc);

                    $arrayParametrosSend     = array('intIdDocumento'               => $intIdFactura, 
                                                     'arrayEstadoTipoDocumentos'    => ['Activo'], 
                                                     'arrayCodigoTipoDocumento'     => ['NC','NCI'], 
                                                     'arrayEstadoNC'                => ['Activo']);
                    //Obtiene la sumatoria de las NC y NCI Activas relacionadas a la factura                    
                    $arrayGetValorTotalNcByFactura = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                  ->getValorTotalNcByFactura($arrayParametrosSend);
                    $arrayParametros['strMensajeError'] = 'No existe Error';
                    $arrayParametros['boolTieneSaldo']  = true;
                    
                    if(!isset($arrayGetValorTotalNcByFactura['strMensajeError']) && empty($arrayGetValorTotalNcByFactura['strMensajeError']))
                    {
                        //Calcula el saldo FAC - SUM(NC Y NCI)
                        $fltSaldo       = 0;
                        $fltSaldo       = round(($arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalFac'] 
                                          - $arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalNc']), 2);
                        $fltNuevoSaldo  = round($arrayValorTotalNcACrear['intValorTotal'], 2);
                        
                        //Pregunta si el valor de la nueva NCI es <= al saldo disponible para permitir crear una nueva NCI y si es mayor que cero
                        if( $fltNuevoSaldo <= $fltSaldo && floatval($fltNuevoSaldo) > 0 )
                        {
                            //Genera la nueva nota de credito interna
                            $objNotaDeCredito = $serviceNotaCredito->generarNotaDeCredito($arrayParametrosNc);                            
                            $emFinanciero->getConnection()->commit();
                            $emFinanciero->getConnection()->close();
                            
                            return $this->redirect($this->generateUrl('infodocumentonotacreditointerna_show',
                                                                      array('id' => $objNotaDeCredito->getId())));
                        }
                        else //Caso contrario muestra un mensaje y no permite crear la nueva nota de credito interna
                        {
                            $objInfoDocumentoFinancieroCab             = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                      ->find($intIdFactura);
                            $arrayParametros                           = array('entity' => $objInfoDocumentoFinancieroCab);
                            $arrayParametros['id']                     = $intIdFactura;
                            $arrayParametros['boolTieneSaldo']         = false;
                            $arrayParametros['strObservacionSinSaldo'] = "No se pudo crear la Nota de Crédito Interna, el valor total de la nueva ".
                                                                         "Nota de Crédito Interna ($".$fltNuevoSaldo .") ";
                            
                            if( floatval($fltNuevoSaldo) > 0 )
                            {
                                $arrayParametros['strObservacionSinSaldo'] .= "supera el saldo disponible ($".$fltSaldo.")";
                            }
                            else
                            {
                                $arrayParametros['strObservacionSinSaldo'] .= "no es válido.";
                            }
                            
                            $emFinanciero->getConnection()->commit();
                            $emFinanciero->getConnection()->close();

                            return $this->render('financieroBundle:InfoNotaCreditoInterna:show.html.twig', $arrayParametros);
                        }
                    }
                    else
                    {
                        $arrayParametros['idFactura']               = 0;
                        $arrayParametros['valorSubTotalFactura']    = 0;
                        $arrayParametros['strMensajeError']         = $arrayGetValorTotalNcByFactura['strMensajeError'];

                        $emFinanciero->getConnection()->rollback();
                        $emFinanciero->getConnection()->close();
                        
                        return $this->render('financieroBundle:InfoNotaCreditoInterna:new.html.twig', $arrayParametros);
                    }
                }
                catch(\Exception $ex)
                {
                    $arrayParametros['idFactura']               = 0;
                    $arrayParametros['valorSubTotalFactura']    = 0;
                    $arrayParametros['strMensajeError']         = 'Existio un error en la creacion de la Nota de Crédito'
                                                                  . ' Interna.';
                    
                    $serviceUtil->insertError('Telcos+', 'createAction', $ex->getMessage(), $strUser, $strIpCreacion);
                    $emFinanciero->getConnection()->rollback();
                    $emFinanciero->getConnection()->close();

                    return $this->render('financieroBundle:InfoNotaCreditoInterna:new.html.twig', $arrayParametros);
                }
            }
            else
            {
                $arrayParametros['idFactura']               = 0;
                $arrayParametros['valorSubTotalFactura']    = 0;
                $arrayParametros['strMensajeError']         = 'El login de la factura a la que esta tratando de '
                                                              . 'aplicar Nota de Crédito interna no corresponde al login que está en sesion'
                                                              . ' actualmente, favor verificar';
                
                return $this->render('financieroBundle:InfoNotaCreditoInterna:new.html.twig', $arrayParametros);
            }  
        }

        $arrayParametros['idFactura']               = 0;
        $arrayParametros['valorSubTotalFactura']    = 0;
        $arrayParametros['strMensajeError']         = 'Debe tener un cliente en sesion para crear la nota de crédito interna.';

        return $this->render('financieroBundle:InfoNotaCreditoInterna:new.html.twig', $arrayParametros);      
    }

    /**
     * Displays a form to edit an existing InfoNotaCredito entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_financiero");

        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoNotaCredito entity.');
        }

        $editForm = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

		//tomar el punto de la session
        $punto="";
        
        return $this->render('financieroBundle:InfoNotaCreditoInterna:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'punto_id'=>$punto
        ));
    }

    /**
     * Edits an existing InfoNotaCredito entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
		$informacionGrid=$request->get('listado_informacion');
		$informacionGrid=json_decode($informacionGrid);
		
		$punto_id=$request->get('punto_id');
		//$empresa_id="10";
		//$oficina_id="1";
		$estado="Activo";
		
		$session=$request->getSession();
		$empresa_id=$session->get('idEmpresa');
		$oficina_id=$session->get('idOficina');
		$user=$session->get('user');
		
        $em = $this->getDoctrine()->getManager("telconet_financiero");

        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoNotaCredito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

			//Guardando el detalle
			if($informacionGrid)
			{
				//busqueda de la persona
				$em_comercial = $this->getDoctrine()->getManager("telconet");
				$persona=$em_comercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin('gvillalba');
				
				foreach($informacionGrid as $info)
				{
					$entitydet  = new InfoDocumentoFinancieroDet();
					
					if($info->tipo=='PR')
					{
						//$informacionCodigo=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find();
						$entitydet->setProductoId($info->codigo);
					}	
					if($info->tipo=='PL')
					{
						//$informacionCodigo=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($info->codigo);
						$entitydet->setPlanId($info->codigo);
					}	
					
					$entitydet->setDocumentoId($entity);
					$entitydet->setPuntoId($punto_id);
					$entitydet->setCantidad($info->cantidad);
					$entitydet->setPersonaId($persona->getId());
					$entitydet->setEmpresaId($empresa_id);
					$entitydet->setOficinaId($oficina_id);
					//El precio ya incluye el descuento... en el caso de los planes
					$entitydet->setPrecioVentaFacproDetalle($info->precio);
					//El descuento debe ser informativo
					$entitydet->setPorcetanjeDescuentoFacpro($info->descuento);
					$entitydet->setFeCreacion(new \DateTime('now'));
					$entitydet->setUsrCreacion($user);
					$em->persist($entitydet);
					$em->flush();
					
				}
			}
            return $this->redirect($this->generateUrl('infodocumentonotacredito_edit', array('id' => $id)));
        }

        return $this->render('financieroBundle:InfoNotaCreditoInterna:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a InfoNotaCredito entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("telconet_financiero");
            $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoNotaCredito entity.');
            }

			$entity->setEstadoImpresionFact("Anulado");
			$em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('InfoNotaCreditoInterna'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    public function infoOrdenesPtoClienteAction()
    {
        $request = $this->getRequest();
        $puntoid=$request->get('puntoid');
        
        //informacion presente en el grid
		$informacionGrid=$request->get('informacionGrid');
		$informacionGrid=json_decode($informacionGrid);
		
        $em = $this->get('doctrine')->getManager('telconet');	
        $estado="Pendiente";
        $session=$request->getSession();
		$id_empresa=$session->get('idEmpresa');
		
        $resultado= $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPuntoPorEstado($id_empresa,$puntoid,$estado);
        $listado_detalles_orden=$resultado['registros'];
        
        if(!$listado_detalles_orden){
                $detalle_orden_l[] = array("codigo"=>"","informacion"=>"","precio"=>"","cantidad"=>"","descuento"=>"","tipo"=>"");
        }else{
                $detalle_orden_l = array();
                foreach($listado_detalles_orden as $ord){
                    if($ord->getProductoId())
                    {
						$tecn['codigo']=$ord->getProductoId()->getId();
                        $tecn['informacion'] = $ord->getProductoId()->getDescripcionProducto();
                        $tecn['tipo'] = 'PR';
                    }
                    if($ord->getPlanId())
                    {
						$tecn['codigo']=$ord->getPlanId()->getId();
                        $tecn['informacion'] = $ord->getPlanId()->getNombrePlan();
                        $tecn['tipo'] = 'PL';
                    }
                    $tecn['precio'] = $ord->getPrecioVenta();
                    $tecn['cantidad'] = $ord->getCantidad();
                    $tecn['descuento'] = $ord->getPorcentajeDescuento();
                    $tecn['puntoId'] = $ord->getPuntoId()->getId();
                    $detalle_orden_l[] = $tecn;
                }
        }
        
        if($informacionGrid)
        {
			foreach($informacionGrid as $info)
			{
				$tecdetalleFacturan['codigo']=$info->codigo;
				$tecn['informacion']=$info->informacion;
				$tecn['precio']=$info->precio;
				$tecn['cantidad']=$info->cantidad;
				$tecn['descuento']=$info->descuento;
				$tecn['tipo'] = $info->tipo;
				$tecn['puntoId'] = $info->puntoId;
				$detalle_orden_l[] = $tecn;
			}
		}
        
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    public function detalleFacturaAction()
    {
		$request = $this->getRequest();
        $facturaid=$request->get('facturaid');
		
        $em = $this->get('doctrine')->getManager('telconet_financiero');	
        $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);
        
        if(!$resultado){
                $detalle_orden_l[] = array("informacion"=>"","precio"=>"","cantidad"=>"","descuento"=>"");
        }else{
				$em_comercial = $this->get('doctrine')->getManager('telconet');	
                $detalle_orden_l = array();
                foreach($resultado as $factdet){
                    if($factdet->getProductoId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
                        $tecn['informacion'] = $informacion->getDescripcionProducto();
                    }
                    if($factdet->getPlanId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
                        $tecn['informacion'] = $informacion->getNombrePlan();
                    }
                    $tecn['precio'] = $factdet->getPrecioVentaFacproDetalle();
                    $tecn['cantidad'] = $factdet->getCantidad();
                    $tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
                    $detalle_orden_l[] = $tecn;
                }
        }
        
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
	public function detalleFacturaEditarAction()
	{
		$request = $this->getRequest();
        $facturaid=$request->get('facturaid');
        $precargado=$request->get('precargado');
		
        $em = $this->get('doctrine')->getManager('telconet_financiero');	
        $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);
        
        if(!$resultado){
                $detalle_orden_l[] = array("codigo"=>"","informacion"=>"","precio"=>"","cantidad"=>"","descuento"=>"","tipo"=>"");
        }else{
				$em_comercial = $this->get('doctrine')->getManager('telconet');	
                $detalle_orden_l = array();
                foreach($resultado as $factdet){
                    if($factdet->getProductoId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
                        $tecn['codigo'] =$informacion->getId();
                        $tecn['informacion'] = $informacion->getDescripcionProducto();
                        $tecn['tipo'] ="PR";
                    }
                    if($factdet->getPlanId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
                        $tecn['codigo'] =$informacion->getId();
                        $tecn['informacion'] = $informacion->getNombrePlan();
                        $tecn['tipo'] ="PL";
                    }
                    $tecn['precio'] = $factdet->getPrecioVentaFacproDetalle();
                    $tecn['cantidad'] = $factdet->getCantidad();
                    $tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
                    $detalle_orden_l[] = $tecn;
                }
        }
        
        if(isset($precargado) && $precargado=="P")
        {
			$puntoid=$request->get('puntoid');
			$em = $this->get('doctrine')->getManager('telconet');	
			$estado="Pendiente";
			$session=$request->getSession();
			$id_empresa=$session->get('idEmpresa');
			
			$resultado= $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPuntoPorEstado($id_empresa,$puntoid,$estado);
			$listado_detalles_orden=$resultado['registros'];
			
			if($listado_detalles_orden){
					$detalle_orden_l = array();
					foreach($listado_detalles_orden as $ord){
						if($ord->getProductoId())
						{
							$tecn['codigo']=$ord->getProductoId()->getId();
							$tecn['informacion'] = $ord->getProductoId()->getDescripcionProducto();
							$tecn['tipo'] = 'PR';
						}
						if($ord->getPlanId())
						{
							$tecn['codigo']=$ord->getPlanId()->getId();
							$tecn['informacion'] = $ord->getPlanId()->getNombrePlan();
							$tecn['tipo'] = 'PL';
						}
						$tecn['precio'] = $ord->getPrecioVenta();
						$tecn['cantidad'] = $ord->getCantidad();
						$tecn['descuento'] = $ord->getPorcentajeDescuento();
						$detalle_orden_l[] = $tecn;
					}
			}
		}
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
	/**
     * El metodo listarTodasNCAction crea la informacion a mostrar en el grid de notas de credito internas 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 10-12-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 04-03-2015
     * @since 1.1
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 11-03-2015
     * @since 1.2
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 31-12-2018 Se realiza cambio para quela consulta de NC se realice a través de la persona en sesion, solo para Telconet
     *                         en caso de ser asistente aparecerá las NC de los vendedores asignados al asistente
     *                         en caso de ser vendedor aparecerá sus NC
     *                         en caso de ser subgerente aparecerá las NC de los vendedores que reportan al subgerente
     *                         en caso de ser gerente aparecerá todos las NC
     * @return Response  Retorna la informacion del grid de notas de credito internas
     */
	public function listarTodasNCAction()
    {
        $objRequest       = $this->getRequest();
        $objSession       = $objRequest->getSession();
        $arrayPunto       = $objSession->get('ptoCliente');
        $intIdEmpresa     = $objSession->get('idEmpresa');
        $intIdOficina     = $objSession->get('idOficina');
        $em               = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayFechaDesde  = explode('T', $objRequest->get("fechaDesde"));
        $arrayFechaHasta  = explode('T', $objRequest->get("fechaHasta"));
        $strEstado        = $objRequest->get("estado");
        $intLimite        = $objRequest->get("limit");
        $intPagina        = $objRequest->get("page");
        $intInicio        = $objRequest->get("start");
        $strUsrCreacion   = $objSession->get('user');
        $strTipoPersonal  = 'Otros';
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');

        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $em->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        if($strEstado)
        {
            $strEstado = $strEstado;
        }
        else
        {
            $strEstado = '';
        }

        $intIdPunto = "";
        if($arrayPunto)
        {
            $intIdPunto = $arrayPunto['id'];
        }

        $arrayParametros['arrayTipoDocumento']      = ['NCI'];
        if($strEstado != '')
        {
            $arrayParametros['arrayEstado']         = array($strEstado);
        }
        $arrayParametros['intLimit']                = $intLimite;
        $arrayParametros['intPage']                 = $intPagina;
        $arrayParametros['intStart']                = $intInicio;
        $arrayParametros['intIdPunto']              = $intIdPunto;
        $arrayParametros['intIdEmpresa']            = $intIdEmpresa;
        $arrayParametros['intIdOficina']            = $intIdOficina;
        $arrayParametros['strFeCreacionDesde']      = $arrayFechaDesde[0];
        $arrayParametros['strFeCreacionHasta']      = $arrayFechaHasta[0];
        $arrayParametros['strPrefijoEmpresa']       = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']         = $strTipoPersonal;
        $arrayParametros['intIdPersonEmpresaRol']   = $intIdPersonEmpresaRol;
        $arrayInfoDocumentoFinancieroCab    = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                 ->findNotasCredito($arrayParametros);
        $arrayDatosInfoDocCab               = $arrayInfoDocumentoFinancieroCab['registros'];
        $intTotalDatos                      = $arrayInfoDocumentoFinancieroCab['total'];
            
        $intCounterCss = 1;
        foreach($arrayDatosInfoDocCab as $objInfoDocFinCab):
            if($intCounterCss % 2 == 0)
            {
                $strClaseCss = 'k-alt';
            }
            else
            {
                $strClaseCss = '';
            }

            $strLinkVer         = $this->generateUrl('infodocumentonotacreditointerna_show', array('id' => $objInfoDocFinCab->getId()));
            $strLinkEliminar    = $this->generateUrl('infodocumentonotacreditointerna_delete_ajax', array('id' => $objInfoDocFinCab->getId()));
            $strLinkImprimir    = $this->generateUrl('infodocumentonotacreditointerna_imprimir', array('id' => $objInfoDocFinCab->getId()));


            $em_comercial                = $this->get('doctrine')->getManager('telconet');
            $entityInfoPunto             = $em_comercial->getRepository('schemaBundle:InfoPunto')->find($objInfoDocFinCab->getPuntoId());
            $entityInfoPersonaEmpresaRol = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                        ->find($entityInfoPunto->getPersonaEmpresaRolId()->getId());

            if($entityInfoPersonaEmpresaRol->getPersonaId()->getNombres() != "" && $entityInfoPersonaEmpresaRol->getPersonaId()->getApellidos() != ""){
                $strNombreCompletoCliente = $entityInfoPersonaEmpresaRol->getPersonaId()->getNombres() . " " . 
                                            $entityInfoPersonaEmpresaRol->getPersonaId()->getApellidos();
            }

            if($entityInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial() != "")
            {
                $strNombreCompletoCliente = $entityInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial();
            }

            if($objInfoDocFinCab->getEsAutomatica() == "S")
            {
                $strAutomatica = "Si";
            }
            else
            {
                $strAutomatica = "No";
            }

            if($objInfoDocFinCab->getNumeroFacturaSri() == null || $objInfoDocFinCab->getNumeroFacturaSri() == "")
            {
                $strNumeroFacturaSri = $objInfoDocFinCab->getNumFactMigracion();
            }
            else
            {
                $strNumeroFacturaSri = $objInfoDocFinCab->getNumeroFacturaSri();
            }

            if($objInfoDocFinCab->getEstadoImpresionFact() != 'Activo')
            {
                $strLinkImprimir = "";
            }

            if($objInfoDocFinCab->getFeEmision() != null)
            {
                $strFeEmision = date_format($objInfoDocFinCab->getFeEmision(), "d/m/Y G:i");
            }
            else
            {
                $strFeEmision = "";
            }
            $arrayResultado[] = array(
                'Numerofacturasri'  => $strNumeroFacturaSri,
                'Punto'             => $entityInfoPunto->getLogin(),
                'Cliente'           => $strNombreCompletoCliente,
                'Esautomatica'      => $strAutomatica,
                'Estado'            => $objInfoDocFinCab->getEstadoImpresionFact(),
                'Fecreacion'        => strval(date_format($objInfoDocFinCab->getFeCreacion(), "d/m/Y G:i")),
                'Feemision'         => $strFeEmision,
                'linkVer'           => $strLinkVer,
                'linkEliminar'      => $strLinkEliminar,
                'linkImprimir'      => $strLinkImprimir,
                'clase'             => $strClaseCss,
                'boton'             => "",
                'id'                => $objInfoDocFinCab->getId(),
                'Total'             => $objInfoDocFinCab->getValorTotal(),
            );
            $intCounterCss++;
        endforeach;

        $objResponse = new Response(json_encode(array('total' => $intTotalDatos, 'documentos' => $arrayResultado)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    public function estadosAction()
    {
        /*Modificacion a utilizacion de estados por modulos*/
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        //$em = $this->get('doctrine')->getManager('telconet');
        //$datos = $em->getRepository('schemaBundle:AdmiEstadoDat')->findEstadosXModulos($modulo_activo,"COM-PROSL");

        $arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
        $arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Inactivo');                
        $arreglo[]= array('idEstado'=>'Pendiente','codigo'=> 'ACT','descripcion'=> 'Pendiente');

        $response = new Response(json_encode(array('estados'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|",$parametro);
        $em = $this->getDoctrine()->getManager();
        foreach($array_valor as $id):
            $entity=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
            if($entity){
                $entity->setEstado("Anulado");
                $em->persist($entity);
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
            else
                $respuesta->setContent("No existe el registro");
        endforeach;
        return $respuesta;
    }
	
	public function restarFechas($fechaDesde,$fechaHasta)
	{
		$fechaHastaFin=explode('-',$fechaHasta);
		$fechaDesdeFin=explode('-',$fechaDesde);
		
		$ano1 = $fechaHastaFin[0]; 
		$mes1 = $fechaHastaFin[1]; 
		$dia1 = $fechaHastaFin[2]; 

		//defino fecha 2 
		$ano2 = $fechaDesdeFin[0]; 
		$mes2 = $fechaDesdeFin[0]; 
		$dia2 = $fechaDesdeFin[0]; 

		//calculo timestam de las dos fechas 
		//$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1); 
		$timestamp1 = strtotime($fechaHasta); 
		$timestamp2 = strtotime($fechaDesde); 
		//$timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2); 

		//resto a una fecha la otra 
		$segundos_diferencia = $timestamp1 - $timestamp2; 
		//echo $segundos_diferencia; 

		//convierto segundos en días 
		$dias_diferencia = $segundos_diferencia / 60 / 60 / 24; 

		//obtengo el valor absoulto de los días (quito el posible signo negativo) 
		$dias_diferencia = abs($dias_diferencia); 

		//quito los decimales a los días de diferencia 
		$dias_diferencia = floor($dias_diferencia); 

		$dias_diferencia++;

		return $dias_diferencia; 
	}
	 /**
     * Documentación para el método 'ajaxGenerarDetallesPorDiasAction'
     * 
     * Obtiene los valores acumulados para visualizar en el detalle de la nota de crédito
     * 
     * @return jsonResponse
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 21-11-2016 - Se modifica para verificar si la persona paga iva o no.
     *                         - Se modifica para crear la opción de 'Valor Por Detalle'.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 03-12-2016 - Se envía el parámetro 'strEsCompensado' para verificar si la nota de crédito debe ser compensada.      
     */
	public function ajaxGenerarDetallesPorDiasAction()
	{
		$request                = $this->getRequest(); 
        $objSession             = $request->getSession();
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');           
        $fechaDesde             = $request->get("fechaDesde");
        $fechaHasta             = $request->get("fechaHasta");
        $idFactura              = $request->get("idFactura");
        $porcentaje             = $request->get("porcentaje");
        $tipo                   = $request->get("tipo");
        $strPagaIva             = $request->get("strPagaIva"); 
        $boolWithoutValues      = $request->get("boolWithoutValues"); 
        $jsonListadoInformacion = $request->get("listado_informacion");
        $strEsCompensado        = $request->get("strEsCompensado") ? $request->get("strEsCompensado") : 'N';
        
        $notaDeCredito                           = $this->get('financiero.InfoNotaCredito'); 
        $parametros_nc["idFactura"]              = $idFactura;
        $parametros_nc["tipo"]                   = $tipo;
        $parametros_nc["fechaDesde"]             = $fechaDesde;
        $parametros_nc["fechaHasta"]             = $fechaHasta;
        $parametros_nc["porcentaje"]             = $porcentaje;
        $parametros_nc["strPagaIva"]             = $strPagaIva;
        $parametros_nc["boolWithoutValues"]      = $boolWithoutValues;
        $parametros_nc["jsonListadoInformacion"] = $jsonListadoInformacion;
        $parametros_nc["strEsCompensado"]        = $strEsCompensado;
        $parametros_nc["strPrefijoEmpresa"]      = $strPrefijoEmpresa;
        
        $arrayDetallesNotaCredito = $notaDeCredito->generarDetallesNotaDeCredito($parametros_nc);
        
        $response = new Response( json_encode( array('listadoInformacion' => $arrayDetallesNotaCredito) ) );
        
        $response->headers->set('Content-type', 'text/json');
        return $response;

	}
	
	public function detalleNCAction()
	{
		$request = $this->getRequest();
        $facturaid=$request->get('facturaid');
		
        $em = $this->get('doctrine')->getManager('telconet_financiero');	
        $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);
        
        if(!$resultado){
                $detalle_orden_l[] = array("informacion"=>"","precio"=>"","cantidad"=>"","descuento"=>"");
        }else{
				$em_comercial = $this->get('doctrine')->getManager('telconet');	
                $detalle_orden_l = array();
                foreach($resultado as $factdet){
					
					if($factdet->getMotivoId())
					{
						$em_general = $this->getDoctrine()->getManager();
						$motivo=$em_general->getRepository('schemaBundle:AdmiMotivo')->find($factdet->getMotivoId());
					}
					
                    if($factdet->getProductoId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
                        $tecn['informacion'] = $informacion->getDescripcionProducto();
                    }
                    if($factdet->getPlanId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
                        $tecn['informacion'] = $informacion->getNombrePlan();
                    }
                    
                    if(isset($motivo))
						$tecn['motivo']=$motivo->getNombreMotivo();
					else
						$tecn['motivo']="";
						
                    $tecn['precio'] = $factdet->getPrecioVentaFacproDetalle();
                    $tecn['cantidad'] = $factdet->getCantidad();
                    $tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
                    $detalle_orden_l[] = $tecn;
                }
        }
        
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
	public function deleteSeleccionadasAjaxAction()
	{
		$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $idfactura = $peticion->get('idfactura');
        $motivos = $peticion->get('motivos');
        
        $session=$peticion->getSession();
		$user=$session->get('user');
		
        $em = $this->getDoctrine()->getManager("telconet_financiero");
		$entity=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idfactura);
		if($entity){
			$entity->setEstadoImpresionFact("Anulado");
			$em->persist($entity);
			$em->flush();
			$entityHistorial  = new InfoDocumentoHistorial();
			$entityHistorial->setDocumentoId($entity);
			$entityHistorial->setMotivoId($motivos);
			$entityHistorial->setFeCreacion(new \DateTime('now'));
			$entityHistorial->setUsrCreacion($user);
			$entityHistorial->setEstado("Anulado");
			$em->persist($entityHistorial);
			$em->flush();
			$response = new Response(json_encode(array('success'=>true)));
		}
		else
			$response = new Response(json_encode(array('success'=>false)));
        $response->headers->set('Content-type', 'text/json');
		return $response;
	}
	
	public function ncPdfAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial =$this->getDoctrine()->getManager('telconet');
        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
        $entityNc=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($entity->getReferenciaDocumentoId());
        $entityDet = $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($entity->getId());
        $punto= $em_comercial->getRepository('schemaBundle:InfoPunto')->find($entity->getPuntoId());
        $datoAdicional= $em_comercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($punto->getId());
        $entityFono=$em_comercial->getRepository('schemaBundle:InfoPersonaFormaContacto')->findOneBy(array("personaId"=>$punto->getPersonaEmpresaRolId()->getPersonaId()->getId(),"formaContactoId"=>"4","estado"=>"Activo"));
        if(!empty($entityFono))
			$telefono=$entityFono->getValor();
		else
			$telefono="";
			
        $oficina= $em_comercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
        }

		$idx=0;
		foreach($entityDet as $det):
//             
            if($det->getPlanId()){
             
             $plan=$em->getRepository('schemaBundle:InfoPlanCab')->find($det->getPlanId());
             $nombreP=$plan->getNombrePlan();
            }else{
              $producto=$em->getRepository('schemaBundle:AdmiProducto')->find($det->getProductoId());  
              $nombreP=$producto->getDescripcionProducto();
            }
                           
//                   
			$arreglo[]= array(
				'cantidad'=>$det->getCantidad(),
                'plan'=>$nombreP,
				'punitario'=>$det->getPrecioVentaFacproDetalle(),
				'ptotal'=>($det->getPrecioVentaFacproDetalle()*$det->getCantidad())
			);
                  
			$idx++;
		endforeach;
		$countBr=170-($idx*10);
        $html = $this->renderView('financieroBundle:InfoNotaCreditoInterna:recibo.html.twig', array(
			'entity'=>$entity,
            'punto' => $punto,
            'oficina' => $oficina,
            'entityNc'=> $entityNc,
            'entityDet'=> $arreglo,
            'telefono'=>$telefono,
            'countBr'=>$countBr,
            'datoAdicional'=> $datoAdicional,
		));
		
		return new Response(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'attachment; filename=recibo-nc-'.trim($punto)."-".trim($entity->getNumeroFacturaSri()).'.pdf',
                )
            );
    }	

}
?>
