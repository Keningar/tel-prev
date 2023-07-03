<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Form\InfoDocumentoFinancieroCabType;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroImp;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;

use telconet\financieroBundle\Service\InfoDocumentoFinancieroCabService;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoDocumentoCaracteristica;
/**
 * FacturasProporcional controller.
 *
 */
class FacturasProporcionalController extends Controller
{
    /**
     * Muestra los objetos de factura proporcianal
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 15-10-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 21-01-2015
     * @since 1.1
     */
    public function indexAction()
    {
		
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $arrayPtoCliente    = $objSession->get('ptoCliente');
        $intIdPunto         = 0;
        if(!empty($arrayPtoCliente['id'])){
            $intIdPunto = $arrayPtoCliente['id'];
        }

        if(true === $this->get('security.context')->isGranted('ROLE_67-1777'))
        {
            $rolesPermitidos[] = 'ROLE_67-1777'; //ENVIO NOTIFICACION COMPROBANTE
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-1778'))
        {
            $rolesPermitidos[] = 'ROLE_67-1778'; //ACTUALIZA COMPROBANTE ELECTRONICO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-1837'))
        {
            $rolesPermitidos[] = 'ROLE_67-1837'; //DESCARGA COMPROBANTE ELECTRONICO
        }
        return $this->render('financieroBundle:FacturasProporcional:index.html.twig', array('rolesPermitidos' => $rolesPermitidos,
                                                                                            'intIdPunto'      => $intIdPunto));
    }

    /**
     * Muestra la informacion de la factura
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 24-11-2014
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 - Se agrega el rango de consumo al momento de presentar la factura creada
     * @since 18-06-2016
     * 
     * Actualizacion: Se agrega historial en el show de la factura proporcional
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.3
     * @since 18-06-2016
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.4 25-11-2016
     * Actualización: Se agrega variable que será utilizada para visualizar las caracteristicas del documento. .  
     *    
     * @author German Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.5 04-08-2017
     * Actualización: Se agrega variable prefijoEmpresa que sera utilziada para identificar la empresa panama.
     * 
     */
    public function showAction($id)
    {
        $em                                 = $this->getDoctrine()->getManager("telconet_financiero");
        $em_comercial                       = $this->getDoctrine()->getManager("telconet");
        $entityInfoDocumentoFinancieroCab   = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
        $serviceInfoCompElectronico         = $this->get('financiero.InfoCompElectronico');
        $serviceInfoDocumentoFinancieroCab  = $this->get('financiero.InfoDocumentoFinancieroCab');
        $objRequest                         = $this->getRequest();
        $strPrefijoEmpresa                  = $objRequest->getSession()->get('prefijoEmpresa');
        if(!$entityInfoDocumentoFinancieroCab)
        {
            throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
        }

        $deleteForm = $this->createDeleteForm($id);


        $entityInfoPunto                = $em_comercial->getRepository('schemaBundle:InfoPunto')
                                                       ->find($entityInfoDocumentoFinancieroCab->getPuntoId());
        $entityInfoPersonaEmpresaRol    = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->find($entityInfoPunto->getPersonaEmpresaRolId()->getId());
        $entityOficina                  = $em->getRepository('schemaBundle:InfoOficinaGrupo')
                                             ->find($entityInfoDocumentoFinancieroCab->getOficinaId());

        $arrayInfoPersona['puntoId'] = $entityInfoPunto->getLogin();
        if($entityInfoPersonaEmpresaRol->getPersonaId()->getNombres() != "" && $entityInfoPersonaEmpresaRol->getPersonaId()->getApellidos() != "")
        {
            $arrayInfoPersona['cliente'] = $entityInfoPersonaEmpresaRol->getPersonaId()->getNombres() . " " . 
                                           $entityInfoPersonaEmpresaRol->getPersonaId()->getApellidos();
        }
        if($entityInfoPersonaEmpresaRol->getPersonaId()->getRepresentanteLegal() != "")
        {
            $arrayInfoPersona['cliente'] = $entityInfoPersonaEmpresaRol->getPersonaId()->getRepresentanteLegal();
        }
        if($entityInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial() != "")
        {
            $arrayInfoPersona['cliente'] = $entityInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial();
        }
        
        if($entityInfoDocumentoFinancieroCab->getMesConsumo() != "")
        {
            $intMes                             = intval($entityInfoDocumentoFinancieroCab->getMesConsumo());
            $strMesConsumo                      = $serviceInfoCompElectronico->obtieneNombreMes($intMes);
            $arrayInfoPersona['strFeConsumo']   = $strMesConsumo.' '.$entityInfoDocumentoFinancieroCab->getAnioConsumo();
        }
        elseif( $entityInfoDocumentoFinancieroCab->getRangoConsumo() )
        {
            $arrayInfoPersona['strFeConsumo'] = $entityInfoDocumentoFinancieroCab->getRangoConsumo();
        }
        else
        {
            $intMes                             = $entityInfoDocumentoFinancieroCab->getFeEmision()->format("n");
            $strMesConsumo                      = $serviceInfoCompElectronico->obtieneNombreMes($intMes);
            $arrayInfoPersona['strFeConsumo']   = $strMesConsumo.' '.$entityInfoDocumentoFinancieroCab->getFeEmision()->format("Y");
        }
        
        //Informacion de paga iva: si | no
        $arrayInfoPersona['strPagaIva']  = $entityInfoPersonaEmpresaRol->getPersonaId()->getPagaIva();
        

        $arrayParametrosSend = array('intIdDocumento'               => $id,
                                     'arrayEstadoTipoDocumentos'    => array('Activo'),
                                     'arrayCodigoTipoDocumento'     => array('NC'),
                                     'arrayEstadoNC'                => array('Activo'));
        //Obtiene el valor de la factura y la sumatoria de las notas de credito relacionadas a la factura
        $arrayGetValorTotalNcByFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->getValorTotalNcByFactura($arrayParametrosSend);

        //Valida que no tenga error
        if(empty($arrayGetValorTotalNcByFactura['strMensajeError']))
        {
            //Se calcula el saldo disponible para presentar la opcion de crear Nota de Credito
            $intSaldo = $arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalFac'] 
                      - $arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalNc'];
        }
        else
        {
            $intSaldo = 0;
        }
        //Se verifica si el documento posee caracteristicas asociada
        
        $arrayCaracteristicas = array();

        $arrayObjInfoDocumentoCaracteristica = $em->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                  ->findBy(array('documentoId' => $entityInfoDocumentoFinancieroCab, 
                                                                 'estado'      => 'Activo'));        
        
        if(!empty($arrayObjInfoDocumentoCaracteristica))
        {
            
            foreach ($arrayObjInfoDocumentoCaracteristica as $objInfoDocumentoCaracteristica):
                if(is_object($objInfoDocumentoCaracteristica))
                {            
                    $objAdmiCaracteristica  = $em_comercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->find($objInfoDocumentoCaracteristica->getCaracteristicaId());
                    if(is_object($objAdmiCaracteristica))
                    {
                        $arrayCaracteristica = array();
                        $arrayCaracteristica['strDescripcion'] = $objAdmiCaracteristica->getDescripcionCaracteristica();
                        $arrayCaracteristica['strValor']       = $objInfoDocumentoCaracteristica->getValor(); 
                        $arrayCaracteristicas[] = $arrayCaracteristica;
                    }
                }
            endforeach;
            
        }        
        
        //Obtener el historial
        $arrHistorial = $serviceInfoDocumentoFinancieroCab->obtenerHistorialDocumento($entityInfoDocumentoFinancieroCab->getId());  

        return $this->render('financieroBundle:FacturasProporcional:show.html.twig', 
                              array(
                                    'entity'                => $entityInfoDocumentoFinancieroCab,
                                    'delete_form'           => $deleteForm->createView(),
                                    'info_cliente'          => $arrayInfoPersona,
                                    'oficina'               => $entityOficina,
                                    'intSaldo'              => round($intSaldo, 2),
                                    'historial'             => $arrHistorial,
                                    'arrayCaracteristicas'  => $arrayCaracteristicas,
                                    'strPrefijoEmpresa'     => $strPrefijoEmpresa
                                   )
                            );
    }

    /**
     * @Secure(roles="ROLE_69-2")
     * 
     * newAction, Crea formulario para la entidad info_documento_financiero_cab
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 15-10-2014
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 - Se envía el permiso para ver el combo de IMPUESTO IVA y el checkbox del ICE al crear la factura.
     * @since 20-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 28-09-2016 - Para TN se valida si el punto padre de facturación debe ser compensado y si el usuario tiene el perfil adecuado para
     *                           realizar dicha acción. Adicional se verifica si tiene el perfil para poder facturar cualquier cliente sin importar
     *                           la oficina de facturación a la que pertenece el cliente.
     *
     * Se agrega validación de rol, y de contacto de facturación para TN
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.4 09-12-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 09-02-2017 - Se elimina la validación por empresa de la compensación solidaria para que tanto MD y TN compensen al 2% las
     *                           facturas realizadas al 14%, y que los clientes pertenezcan a los cantones MANTA y PORTOVIEJO.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.8 20-06-2017 - Se agrega deficición de rol para editar precio en detalles de facturas proporcionales.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.9 28-06-2017 - Se modifica la función para que verifique el impuesto del IVA en estado 'Activo' por país.
     *                           Se valida si la empresa factura de forma electrónica con el parámetro en sessión 'strFacturaElectronico'
     */
    public function newAction()
    {
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $arrayCliente            = $objSession->get('cliente');
        $arrayPtoCliente         = $objSession->get('ptoCliente');
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $intCodEmpresa           = $objSession->get('idEmpresa');
        $intIdOficina            = $objSession->get('idOficina');
        $strNombreOficina        = $objSession->get('oficina');
        $emGeneral               = $this->getDoctrine()->getManager("telconet_general");
        $emFinanciero            = $this->getDoctrine()->getManager("telconet_financiero");
        $strEmpresaCod           = $objSession->get('idEmpresa');
        $boolPuedeFacturar       = true;
        $boolDisableComboOficina = false;
        $strUsrSession           = $objSession->get('user');
        $strIpSession            = $objRequest->getClientIp();
        $strFacturaElectronica   = $objSession->get('strFacturaElectronico');
        $intIdPaisSession        = $objSession->get('intIdPais');
        $strPaisSession          = $objSession->get('strNombrePais');
        $strOficinaDebeCompensar = "N";
        
        $serviceInfoDocumentoFinancieroCab = $this->get('financiero.InfoDocumentoFinancieroCab');

        $entityInfoDocumentoFinancieroCab = new InfoDocumentoFinancieroCab();
        $formInfoDocumentoFinanacieroCab = $this->createForm(new InfoDocumentoFinancieroCabType(), $entityInfoDocumentoFinancieroCab);
        //informacion del pto cliente
        $emComercial         = $this->getDoctrine()->getManager();
        
        $arrayParametros = array( 'entity'                      => $entityInfoDocumentoFinancieroCab,
                                  'form'                        => $formInfoDocumentoFinanacieroCab->createView(),
                                  'esCompensado'                => 'N',
                                  'floatPorcentajeCompensacion' => 0 );
        
        if(true === $this->get('security.context')->isGranted('ROLE_69-4277'))
        {
            $rolesPermitidos[] = 'ROLE_69-4277'; //ELEGIR IMPUESTO IVA
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_69-4297'))
        {
            $rolesPermitidos[] = 'ROLE_69-4297'; //ELEGIR IMPUESTO ICE
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_67-4777'))
        {
            $rolesPermitidos[] = 'ROLE_67-4777'; //PUEDE COMPENSAR
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_69-5357'))
        {
            $rolesPermitidos[] = 'ROLE_69-5357'; //PUEDE EDITAR PRECIO EN DETALLE DE FACTURA
        }         
        
        $arrayParametros['rolesPermitidos'] = $rolesPermitidos;

        if($arrayPtoCliente)
        {
            //Como el punto cliente existe se debe verificar si tiene información adicional
            $objPuntoAdicional = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($arrayPtoCliente['id']);

            //verifica que la entidad tenga datos
            if( $objPuntoAdicional != null )
            {
                //valida que el punto sea padre de facturación
                $strEsPadreFacturacion = $objPuntoAdicional->getEsPadreFacturacion();
                $strEsPadreFacturacion = ( !empty($strEsPadreFacturacion) ? $strEsPadreFacturacion : 'N' );
                
                if( 'S'  == $strEsPadreFacturacion )
                {
                    $arrayParametros['punto_id']      = $arrayPtoCliente;
                    $arrayParametros['cliente']       = $arrayCliente;
                    $arrayParametros['esElectronica'] = "No";

                    //Obtenemos el numero de factura sigt a dar, solo informativo
                    $em_comercial = $this->getDoctrine()->getManager();

                    if ( $strFacturaElectronica == "S" )
                    {
                        $arrayParametros['esElectronica'] = "Si";
                        
                        if ( $strPrefijoEmpresa == 'MD' )
                        {
                            $entityAdmiNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')
                                                                 ->findOficinaMatrizYFacturacion($intCodEmpresa, 'FACE');
                        }
                        else
                        {
                            $entityAdmiNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')
                                                                 ->findByEmpresaYOficina( $intCodEmpresa,
                                                                                          $intIdOficina,
                                                                                          "FACE" );
                        }// ( $strPrefijoEmpresa == 'MD' )
                    }
                    else
                    {
                        $entityAdmiNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina( $intCodEmpresa,
                                                                                                                                    $intIdOficina,
                                                                                                                                    "FAC" );
                    }// ( $strFacturaElectronica == "S" )
                    
                    /**
                     * Bloque que retorna el máximo tamaño que debe tener el secuencial de la factura de la empresa en sessión
                     */
                    $intTamanioSecuencial      = 0;
                    $arraySecuencialDocumento = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('SECUENCIALES_POR_EMPRESA', 
                                                                   'FINANCIERO',
                                                                   'FACTURACION', 
                                                                   'FAC', 
                                                                   '',
                                                                   '',
                                                                   '', 
                                                                   '', 
                                                                   '', 
                                                                   $intCodEmpresa);

                    if ( isset($arraySecuencialDocumento['valor1']) && !empty($arraySecuencialDocumento['valor1']) )
                    {
                        $intTamanioSecuencial = $arraySecuencialDocumento['valor1'];
                    }// ( isset($arraySecuencialDocumento['valor1']) && !empty($arraySecuencialDocumento['valor1']) )

                    $strNumeracionUno      = $entityAdmiNumeracion->getNumeracionUno();
                    $strNumeracionDos      = $entityAdmiNumeracion->getNumeracionDos();
                    $strNumeroAutorizacion = $entityAdmiNumeracion->getNumeroAutorizacion();
                    $strSecuencia          = str_pad($entityAdmiNumeracion->getSecuencia(), $intTamanioSecuencial, "0", STR_PAD_LEFT);
                    $strNumeroFacturaSri   = "";
                    
                    if ( !empty($strNumeracionUno) && !empty($strNumeracionDos) )
                    {
                        $strNumeroFacturaSri = $strNumeracionUno.'-'.$strNumeracionDos;
                    }
                    elseif ( !empty($strNumeroAutorizacion) )
                    {
                        $strNumeroFacturaSri = $strNumeroAutorizacion;
                    }
                    else
                    {
                        $strNumeroFacturaSri = "";
                    }

                    $strNumeroFacturaSri .= "-".$strSecuencia;

                    $arrayParametros['numero_de_factura'] = $strNumeroFacturaSri;
                    $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                    $arrayParametros['strNombreOficina']  = $strNombreOficina;

                    //Se obtiene informacion del cliente si paga iva o no
                    $entitypersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($arrayCliente["id_persona"]);
                    $strPagaIva    = $entitypersona->getPagaIva();

                    $arrayParametros['strPagaIva']   = $strPagaIva;
                    $arrayParametros['intIdOficina'] = $intIdOficina;

                    /**
                     * Bloque que verifica si el cliente en sessión pertenece a la oficina del usuario en sessión, caso contrario verifica si el
                     * usuario tiene el perfil adecuado para facturar con cualquier oficina
                     */
                    $intIdOficinaClienteSession     = ( !empty($arrayCliente['id_oficina']) ) ? $arrayCliente['id_oficina'] : 0;              
                    $strNombreOficinaClienteSession = ( !empty($arrayCliente['nombre_oficina']) ) ? $arrayCliente['nombre_oficina'] : "";

                    if( $strPrefijoEmpresa == 'TN' )
                    {
                        if( $intIdOficina != $intIdOficinaClienteSession )
                        {
                            if(true === $this->get('security.context')->isGranted('ROLE_67-4778'))
                            {
                                $rolesPermitidos[] = 'ROLE_67-4778'; //PERFIL FACTURAR CON CUALQUIER OFICINA
                            }
                            else
                            {
                                $boolPuedeFacturar = false;
                            }
                        }//( $intIdOficina != $intIdOficinaClienteSession )
                        else
                        {
                            if(false === $this->get('security.context')->isGranted('ROLE_67-4778'))
                            {
                                $boolDisableComboOficina = true;
                            }                            
                        }
                    }//( $strPrefijoEmpresa == 'TN' )

                    $arrayParametrosService                           = array();
                    $arrayParametrosService['strUsrSession']          = $strUsrSession;
                    $arrayParametrosService['strIpSession']           = $strIpSession;
                    $arrayParametrosService['intIdPersonaEmpresaRol'] = ( !empty($arrayCliente['id_persona_empresa_rol']) ) 
                                                                        ? $arrayCliente['id_persona_empresa_rol'] : 0;
                    $arrayParametrosService['intIdOficina']           = $intIdOficinaClienteSession;
                    $arrayParametrosService['strEmpresaCod']          = $intCodEmpresa;
                    $arrayParametrosService['intIdSectorPunto']       = ( !empty($arrayPtoCliente['id_sector']) ) ? $arrayPtoCliente['id_sector'] 
                                                                        : 0;
                    $arrayParametrosService['intIdPuntoFacturacion']  = ( !empty($arrayPtoCliente['id']) ) ? $arrayPtoCliente['id'] : 0;

                    $arrayParametros['esCompensado'] = $serviceInfoDocumentoFinancieroCab->verificarClienteCompensado($arrayParametrosService);

                    $objAdmiImpuestoCompensacion = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                             ->findOneBy(array('tipoImpuesto' => 'COM', 'estado' => 'Activo'));

                    if( $objAdmiImpuestoCompensacion != null )
                    {
                        $floatPorcentajeCompensacion = $objAdmiImpuestoCompensacion->getPorcentajeImpuesto();

                        if( !empty($floatPorcentajeCompensacion) )
                        {
                            $arrayParametros['floatPorcentajeCompensacion'] = $floatPorcentajeCompensacion;
                        }//( !empty($floatPorcentajeCompensacion) )
                    }//( $objAdmiImpuestoCompensacion != null )

                    $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                 ->findOneBy( array('estado'          => "Activo", 
                                                                    'nombreParametro' => "CANTONES_OFICINAS_COMPENSADAS") );

                    if( $objParametroCab != null )
                    {
                        $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy( array( 'estado'      => "Activo",
                                                                         'parametroId' => $objParametroCab,
                                                                         'valor1'      => $strNombreOficinaClienteSession ) );

                        if( $objParametroDet != null )
                        {
                            $strOficinaDebeCompensar = 'S';
                        }//( $objParametroDet != null )
                    }//( $objParametroCab != null )
                }//( 'S'  == $strEsPadreFacturacion )
            }//( $objPuntoAdicional != null )
        }
        //CONSULTA EN LA TABLA PARAMETROS SI SE MUESTRA CAMPO OBSERVACION SEGUN EMPRESA
        $arrayParametroDet= $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getOne("OBSERVACION_FACTURA_PROPORCIONAL", "FINANCIERO", "", "", "", "", "", "","",$intCodEmpresa);

        $arrayParametros['strMuestraObservacion']='N';
        if ($arrayParametroDet["valor2"]=='S')
        {
            $arrayParametros['strMuestraObservacion']=$arrayParametroDet["valor2"];
        }

        /**
         * Bloque que obtiene el impuesto del IVA en estado 'Activo' con el cual se validará si el usuario debe compensar o no si el usuario
         * selecciona un impuesto diferente del 'Activo'.
         */
        $objAdmiImpuestoIva = null;
        
        if( strtoupper($strPaisSession) ==  "ECUADOR" )
        {
            $objAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                            ->findOneBy( array('tipoImpuesto' => 'IVA', 
                                                               'estado'       => 'Activo') );
        }
        else
        {
            $objAdmiPais = $emGeneral->getRepository("schemaBundle:AdmiPais")->findOneById($intIdPaisSession);
            
            if ( is_object($objAdmiPais) )
            {
                $objAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                ->findOneBy( array('tipoImpuesto' => 'ITBMS', 
                                                                   'estado'       => 'Activo',
                                                                   'paisId'       => $objAdmiPais) );
            }// ( is_object($objAdmiPais) )
        }

        if( $objAdmiImpuestoIva != null )
        {
            $arrayParametros['intIdImpuestoIvaActivo'] = $objAdmiImpuestoIva->getId();
        }//( $objAdmiImpuestoCompensacion != null )

        /**
         * Bloque que retorna las opciones habilitadas para la empresa en sessión
         */
        $arrayOpcionesHabilitadas = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get('OPCIONES_HABILITADAS_FINANCIERO', 
                                                    'FINANCIERO',
                                                    'FACTURACION', 
                                                    'FACP', 
                                                    '',
                                                    '',
                                                    '', 
                                                    '', 
                                                    '', 
                                                    $strEmpresaCod);

        if ( !empty($arrayOpcionesHabilitadas) )
        {
            foreach ( $arrayOpcionesHabilitadas as $arrayOpcion )
            {
                if ( isset($arrayOpcion['valor1']) && !empty($arrayOpcion['valor1']) && isset($arrayOpcion['valor2'])
                     && !empty($arrayOpcion['valor2']) )
                {
                    $arrayParametros[$arrayOpcion['valor1']] = $arrayOpcion['valor2'];
                }// ( isset($arrayOpcion['valor1']) && !empty($arrayOpcion['valor1']) && isset($arrayOpcion['valor2'])...
            }//foreach ( $arrayOpcionesHabilitadas as $arrayOpcion )
        }// ( !empty($arrayOpcionesHabilitadas) )

        $arrayParametros['boolDisableComboOficina']           = $boolDisableComboOficina;
        $arrayParametros['strOficinaEsCompensado']            = $strOficinaDebeCompensar;
        $arrayParametros["boolPuedeFacturar"]                 = $boolPuedeFacturar;
        $arrayParametros["booleanPresentarMensajeValidacion"] = false;
        $arrayParametros["strEmpresaCod"]                     = $strEmpresaCod;
        if($boolPuedeFacturar)
        {
            $arrayParametros = $emFinanciero->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                            ->validaContactoFacturacion($arrayParametros);
        }

        return $this->render('financieroBundle:FacturasProporcional:new.html.twig', $arrayParametros);
    }

    /**
     * @Secure(roles="ROLE_67-3")
     * 
     * createAction, Crea una nueva entidad de info_documento_financiero_cab
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 15-10-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 24-11-2014
     * @since 1.1
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 24-05-2016 - Se activa la opción de 'Precargada Agrupada'
     * @since 1.2
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 - Se adapta la función para obtener los valores de los productos y/o planes dependiendo del porcentaje del IVA elegido por el 
     *                usuario, en caso de no elegir ningún impuesto se calculará todo al impuesto del IVA en estado 'Activo'.
     *                Adicional se verifica si el usuario seleccionó que la factura aplique el ICE.
     * @since 20-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 22-06-2016 - Se modifica para escribir la descripción de los items que son enviados desde el grid principal de las facturas
     *                           proporcionales.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 23-06-2016 - Se quita el redondeo en los impuestos porque no se debe realizar dicho proceso por cada producto.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 23-06-2016 - Se realiza cálculo de impuesto ICE
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 08-07-2016 - Se corrige que permita guardar valores mayores a 0
     * 
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.9 12-07-2016 - Se modifica solo para empresa TN para que la numeracion sea segun la oficina que tiene el usuario en sesion 
     *                           y se agrega observacion en el historial del documento
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.0 18-07-2016 - Se corrige que a clientes nuevos (clientes sin facturas) les permita guardar el impuesto seleccionado.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.1 15-08-2016 - Se cambia a que los rangos de fecha seleccionados por el usuario sean los rangos de consumo de la factura, es decir
     *                           se eliminan las opciones de fechas de consumo en las facturas.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.2 13-09-2016 - Para TN se valida si el punto padre de facturación debe ser compensado, y se saca el valor correspondiente de
     *                           compensación solidaria que será guardado a nivel de cabecera en la factura. Es decir, en la tabla 
     *                           DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB en el campo DESCUENTO_COMPENSACION.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.3 02-12-2016 - Se elimina la operación de multiplicar el valor compensado del grid por la cantidad del producto y/o plan puesto que
     *                           el valor obtenido desde el grid es el valor total.
     *
     * Se agrega validación de rol, y de contacto de facturación para TN
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 2.4 19-12-2016
     * 
     * Se llama a otra función para limpiar los caracteres especiales de la descripcion de las facturas proporcionales.
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 2.5 22-12-2016
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.6 25-01-2017 - Se realiza una validación adicional cuando el documento a crear tiene ICE, la cual consiste en validar si la suma
     *                           total de los impuestos del documento tal como están guardados en base restados de la suma total de los impuestos
     *                           redondeados genera una diferencia mayor a 0.005 pero menor a 0.01. Si fuese el caso se debe sumar la diferencia
     *                           obtenida al subtotal con impuestos y modificar el valor total de la factura.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.7 27-01-2017 - Se multiplica la compensación solidaria por la cantidad del producto y/o plan enviado para crear el documento.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.8 09-02-2017 - Se realiza la modificación para que la empresa MD compense sus planes al 2% de las facturas realizadas al 14%.
     *  
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 2.9 04-04-2017 - Se realiza modificación para el ingreso de un nuevo detalle a la factura por cargo de reproceso de débito.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 3.0 23-05-2017 - Se agrega validación para verificar si existe algún detalle de la factura por cargo de reproceso de débito. 
     */
    public function createAction(Request $objRequest)
    {
        setlocale(LC_TIME, "es_ES.UTF-8");
        
        $emComercial  = $this->getDoctrine()->getManager();
        $emFinanciero = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral    = $this->getDoctrine()->getManager("telconet_general");
       
        $entityInfoDocumentoFinancieroCab   = new InfoDocumentoFinancieroCab();
        $formInfoDocumentoFinancieroCab     = $this->createForm(new InfoDocumentoFinancieroCabType(), $entityInfoDocumentoFinancieroCab);
        $formInfoDocumentoFinancieroCab->bind($objRequest);

        $objInformacionGrid         = $objRequest->get('listado_informacion');
        $arrayInformacionGrid       = json_decode($objInformacionGrid);
        $strObservacionRangoConsumo = "";
        $strIpCreacion              = $objRequest->getClientIp();
        $intIdImpuesto              = $objRequest->get('intTxtIdImpuesto');
        $strPagaIce                 = $objRequest->get('strPagaIce');
        $strObservacion             = $objRequest->get('observacion');
        $strFechaInicio             = $objRequest->get('feDesdeFacturaE') ? $objRequest->get('feDesdeFacturaE') : "";
        $strFechaFin                = $objRequest->get('feHastaFacturaE') ? $objRequest->get('feHastaFacturaE') : "";
        $datetimeFechaInicio        = $strFechaInicio ? \DateTime::createFromFormat("Y-m-d", $strFechaInicio) : null;
        $datetimeFechaFin           = $strFechaFin ? \DateTime::createFromFormat("Y-m-d", $strFechaFin) : null;
        $serviceUtil                = $this->get('schema.Util');
        $objSession                 = $objRequest->getSession();
        $strUser                    = $objSession->get('user');
        $strEmpresaCod              = $objSession->get('idEmpresa');
        $arrayCliente               = $objSession->get('cliente');
        $boolDocumentoTieneIce      = false;
        $boolTieneReprocesoDebito   = false;

        
        $emFinanciero->getConnection()->beginTransaction();
        $emComercial->getConnection()->beginTransaction();

        try
        {
            if( $datetimeFechaInicio && $datetimeFechaFin)
            {
                //informacion del pto cliente
                $arrayPunto         = $objSession->get('ptoCliente');
                $intIdEmpresa       = $objSession->get('idEmpresa');
                $intIdOficina       = $objSession->get('idOficina');
                $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
                $intIdPunto         = $arrayPunto['id'];
                $strEstado          = "Pendiente";
                $strEsElectronica   = "S";
                $arrayCliente       = $objSession->get('cliente');

                if($strPrefijoEmpresa == 'TTCO')
                {
                    $strEstado          = 'Activo';
                    $strEsElectronica   = "N";
                }

                $arrayParametros['cliente']           = $arrayCliente;
                $arrayParametros["strEmpresaCod"]     = $strEmpresaCod;
                $arrayParametros["boolPuedeFacturar"] = true;
                $arrayParametros['punto_id']          = $arrayPunto;
                $arrayParametros                      = $emFinanciero->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                                     ->validaContactoFacturacion($arrayParametros);
                if (!$arrayParametros["boolPuedeFacturar"]){
                    $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $arrayParametros["strMensajeValidacion"] . '!');
                    throw new \Exception($arrayParametros["strMensajeValidacion"]);
                }

                //Verificar si paga_iva
                //Se obtiene informacion del cliente si paga iva o no
                if( !empty($arrayCliente["id_persona"]) )
                {
                    $entitypersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($arrayCliente["id_persona"]);
                    $strPagaIva    = $entitypersona->getPagaIva();
                }
                else
                {
                    throw new \Exception("No existe cliente en sessión para crear la facturación proporcional");
                }

                if($intIdPunto)
                {
                    if($formInfoDocumentoFinancieroCab->isValid())
                    {
                        if($strPrefijoEmpresa == 'MD')
                        {
                            $entityAdmiNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                                ->findOficinaMatrizYFacturacion($intIdEmpresa, 'FACE');
                        }
                        elseif($strPrefijoEmpresa == 'TN')
                        {
                            $entityAdmiNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina( $intIdEmpresa, 
                                                                                                                                       $intIdOficina, 
                                                                                                                                       "FACE");
                        }
                        else
                        {
                            $entityAdmiNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina( $intIdEmpresa, 
                                                                                                                                       $intIdOficina, 
                                                                                                                                       "FAC");                    
                        }
                        
                        $strNumeroFacturaSri = "";
                        
                        if( $entityAdmiNumeracion != null )
                        {
                            $strSecuencia             = $entityAdmiNumeracion->getSecuencia();
                            $strNumeroEstablecimiento = $entityAdmiNumeracion->getNumeracionUno();
                            $strPuntoEmision          = $entityAdmiNumeracion->getNumeracionDos();
                            
                            if( !empty($strSecuencia) && !empty($strNumeroEstablecimiento) && !empty($strPuntoEmision) )
                            {
                                $strSecuencia        = str_pad($strSecuencia, 9, "0", STR_PAD_LEFT);
                                $strNumeroFacturaSri = $strNumeroEstablecimiento. "-" . $strPuntoEmision . "-" . $strSecuencia;
                            }
                            else
                            {
                                throw new \Exception("No existe secuencia (".$strSecuencia.") o número de establecimiento "
                                                    ."(".$strNumeroEstablecimiento.") o punto de emisión (".$strPuntoEmision.") correspondiente "
                                                    ."para crear la factura proporcional");
                            }
                        }//( $entityAdmiNumeracion != null )
                        else
                        {
                            throw new \Exception("No existe numeración correspondiente para crear la factura proporcional");
                        }//( $entityAdmiNumeracion == null )

                        
                        if( empty($strNumeroFacturaSri) )
                        {
                            throw new \Exception("No existe numeración del SRI requerida para crear la factura proporcional");
                        }//( empty($strNumeroFacturaSri) )
                        
                        
                        //busqueda del documento
                        $entityAdmiTipoDocumentoFinanciero = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                          ->findOneByCodigoTipoDocumento("FACP");
                        $entityInfoDocumentoFinancieroCab->setTipoDocumentoId($entityAdmiTipoDocumentoFinanciero);
                        $entityInfoDocumentoFinancieroCab->setPuntoId($intIdPunto);
                        $entityInfoDocumentoFinancieroCab->setEsAutomatica("N");
                        $entityInfoDocumentoFinancieroCab->setProrrateo("N");
                        $entityInfoDocumentoFinancieroCab->setReactivacion("N");
                        $entityInfoDocumentoFinancieroCab->setRecurrente("N");
                        $entityInfoDocumentoFinancieroCab->setComisiona("S");
                        $entityInfoDocumentoFinancieroCab->setEntregoRetencionFte("N");
                        $entityInfoDocumentoFinancieroCab->setOficinaId($intIdOficina);
                        $entityInfoDocumentoFinancieroCab->setFeCreacion(new \DateTime('now'));
                        $entityInfoDocumentoFinancieroCab->setFeEmision(new \DateTime('now'));
                        $entityInfoDocumentoFinancieroCab->setUsrCreacion($strUser);
                        $entityInfoDocumentoFinancieroCab->setEstadoImpresionFact($strEstado);
                        $entityInfoDocumentoFinancieroCab->setEsElectronica($strEsElectronica);
                        $entityInfoDocumentoFinancieroCab->setNumeroFacturaSri($strNumeroFacturaSri);

                        $strFechaInicialTmp = strftime("%d",$datetimeFechaInicio->getTimestamp())." ".
                                              ucfirst(strtolower(strftime("%B",$datetimeFechaInicio->getTimestamp())))." ".
                                              strftime("%Y",$datetimeFechaInicio->getTimestamp());

                        $strFechaFinalTmp = strftime("%d",$datetimeFechaFin->getTimestamp())." ".
                                            ucfirst(strtolower(strftime("%B",$datetimeFechaFin->getTimestamp())))." ".
                                            strftime("%Y",$datetimeFechaFin->getTimestamp());

                        $strObservacionRangoConsumo = "Del ".$strFechaInicialTmp." al ".$strFechaFinalTmp;
                        
                        $entityInfoDocumentoFinancieroCab->setRangoConsumo($strObservacionRangoConsumo);
                        $emFinanciero->persist($entityInfoDocumentoFinancieroCab);
                        $emFinanciero->flush();

                        if($entityInfoDocumentoFinancieroCab)
                        {
                            /**
                             * Bloque que guarda como características las fechas de consumo del rango seleccionado por el usuario
                             */
                            $strFechaInicialTmp = $datetimeFechaInicio->format('d-m-Y');
                            $strFechaFinalTmp   = $datetimeFechaFin->format('d-m-Y');
                            
                            $objFeRangoInicialCaract = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                   ->findOneBy( array('estado'                    => 'Activo', 
                                                                                      'descripcionCaracteristica' => 'FE_RANGO_INICIAL') );
                            $objFeRangoFinalCaract   = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                   ->findOneBy( array('estado'                    => 'Activo', 
                                                                                      'descripcionCaracteristica' => 'FE_RANGO_FINAL') );

                            if( $objFeRangoInicialCaract && $objFeRangoFinalCaract )
                            {
                                $objFeRangoInicialDocCaract = new InfoDocumentoCaracteristica();
                                $objFeRangoInicialDocCaract->setCaracteristicaId($objFeRangoInicialCaract->getId());
                                $objFeRangoInicialDocCaract->setDocumentoId($entityInfoDocumentoFinancieroCab);
                                $objFeRangoInicialDocCaract->setEstado('Activo');
                                $objFeRangoInicialDocCaract->setFeCreacion(new \DateTime('now'));
                                $objFeRangoInicialDocCaract->setIpCreacion($strIpCreacion);
                                $objFeRangoInicialDocCaract->setUsrCreacion($strUser);
                                $objFeRangoInicialDocCaract->setValor($strFechaInicialTmp);
                                $emFinanciero->persist($objFeRangoInicialDocCaract);
                                $emFinanciero->flush();

                                $objFeRangoFinalDocCaract = new InfoDocumentoCaracteristica();
                                $objFeRangoFinalDocCaract->setCaracteristicaId($objFeRangoFinalCaract->getId());
                                $objFeRangoFinalDocCaract->setDocumentoId($entityInfoDocumentoFinancieroCab);
                                $objFeRangoFinalDocCaract->setEstado('Activo');
                                $objFeRangoFinalDocCaract->setFeCreacion(new \DateTime('now'));
                                $objFeRangoFinalDocCaract->setIpCreacion($strIpCreacion);
                                $objFeRangoFinalDocCaract->setUsrCreacion($strUser);
                                $objFeRangoFinalDocCaract->setValor($strFechaFinalTmp);
                                $emFinanciero->persist($objFeRangoFinalDocCaract);
                                $emFinanciero->flush();
                            }//( $objFeRangoInicialCaract && $objFeRangoFinalCaract && $dateFechaInicial && $dateFechaFinal )
                            else
                            {
                                throw new \Exception("No se puede guardar la factura puesto que no se encontraron las características de "
                                                     ."'FE_RANGO_INICIAL' y/o 'FE_RANGO_FINAL'");
                            }

                            //Actualizo la numeracion en la tabla
                            $intNuevaSecuencia = ($entityAdmiNumeracion->getSecuencia() + 1);
                            $entityAdmiNumeracion->setSecuencia($intNuevaSecuencia);
                            $emComercial->persist($entityAdmiNumeracion);
                            $emComercial->flush();

                            $entityInfoDocumentoHistorial = new InfoDocumentoHistorial();
                            $entityInfoDocumentoHistorial->setDocumentoId($entityInfoDocumentoFinancieroCab);
                            $entityInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                            $entityInfoDocumentoHistorial->setUsrCreacion($strUser);
                            $entityInfoDocumentoHistorial->setEstado($strEstado);
                            $entityInfoDocumentoHistorial->setObservacion($strObservacion);
                            $emFinanciero->persist($entityInfoDocumentoHistorial);
                            $emFinanciero->flush();
                        }//($entityInfoDocumentoFinancieroCab)

                        if($arrayInformacionGrid)
                        {
                            $intImpuesto                         = 0;
                            $intSubtotal                         = 0;
                            $intDescuento                        = 0;
                            $floatDescuentoCompensacionAcumulado = 0;
    
                            $entityAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                               ->findOneBy( array('tipoImpuesto' => 'IVA', 'estado' => 'Activo') );

                            foreach($arrayInformacionGrid as $objInfoGrid)
                            {
                                $strTmpDescripcionDet = $objInfoGrid->descripcion;
                                $strTmpDescripcionDet = trim($strTmpDescripcionDet);

                                if(strcmp($strTmpDescripcionDet,"Cargo por Gestion de Cobranza")== 0)
                                {
                                    $boolTieneReprocesoDebito = true;
                                } 
                                
                                if( floatval($objInfoGrid->precio) > 0 )
                                {
                                    $intSumSubtotal         = 0;
                                    $intPrecioSinDescuento  = 0;

                                    $entityInfoDocumentoFinancieroDet = new InfoDocumentoFinancieroDet();
                                    $entityInfoDocumentoFinancieroDet->setDocumentoId($entityInfoDocumentoFinancieroCab);
                                    $entityInfoDocumentoFinancieroDet->setPuntoId($objInfoGrid->puntoId);
                                    $entityInfoDocumentoFinancieroDet->setCantidad($objInfoGrid->cantidad);
                                    $entityInfoDocumentoFinancieroDet->setEmpresaId($intIdEmpresa);
                                    $entityInfoDocumentoFinancieroDet->setOficinaId($intIdOficina);
                                    $entityInfoDocumentoFinancieroDet->setPrecioVentaFacproDetalle(round($objInfoGrid->precio, 2));
                                    $entityInfoDocumentoFinancieroDet->setDescuentoFacproDetalle(round($objInfoGrid->descuento, 2));
                                    $entityInfoDocumentoFinancieroDet->setFeCreacion(new \DateTime('now'));
                                    $entityInfoDocumentoFinancieroDet->setUsrCreacion($strUser);

                                    if($strPrefijoEmpresa == 'MD')
                                    {
                                        $entityInfoDocumentoFinancieroDet->setObservacionesFacturaDetalle( $objInfoGrid->descripcion );
                                    }
                                    else
                                    {
                                        $strTmpDescripcionDet = $objInfoGrid->descripcion;
                                        $strTmpDescripcionDet = trim($strTmpDescripcionDet);
                                        $strTmpDescripcionDet = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                             ->getValidXmlValue($strTmpDescripcionDet);
                                        $entityInfoDocumentoFinancieroDet->setObservacionesFacturaDetalle($strTmpDescripcionDet);
                                    }

                                    $emFinanciero->persist($entityInfoDocumentoFinancieroDet);
                                    $emFinanciero->flush();


                                    if($objInfoGrid->tipo == 'PR')
                                    {
                                        $floatImpuestoIceAcumulado = 0;

                                        /* Cuando es producto voy a la tabla AdmiProducto para sacar los impuestos */
                                        $entityInfoDocumentoFinancieroDet->setProductoId($objInfoGrid->codigo);

                                        $intPrecioNuevo         =  ($objInfoGrid->precio * $objInfoGrid->cantidad) - $objInfoGrid->descuento;
                                        $intPrecioSinDescuento  =  ($objInfoGrid->precio * $objInfoGrid->cantidad);
                                        $intSumSubtotal         += $intPrecioNuevo;

                                        $arrayParametrosImpuestosPrioridad  = array( 'intIdProducto' => $objInfoGrid->codigo,
                                                                                     'strEstado'     => 'Activo',
                                                                                     'intPrioridad'  => 1 );
                                        $arrayImpuestosPrioridad1           = $emComercial->getRepository('schemaBundle:InfoProductoImpuesto')
                                                                              ->getInfoImpuestoByCriterios( $arrayParametrosImpuestosPrioridad );
                                        $objInfoProductoImpuestosPrioridad1 = $arrayImpuestosPrioridad1['registros'];

                                        if($objInfoProductoImpuestosPrioridad1)
                                        {
                                            foreach($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImpuesto)
                                            {
                                                $boolImpuesto     = true;
                                                $objAdmiImpuesto  = $objInfoProductoImpuesto->getImpuestoId();

                                                if($objAdmiImpuesto)
                                                {
                                                    if( $strPagaIce != "SI" && $objAdmiImpuesto->getTipoImpuesto() == 'ICE' )
                                                    {
                                                        $boolImpuesto = false;
                                                    }

                                                    if($boolImpuesto)
                                                    {
                                                        //Id del impuesto con el que se va a crear la factura
                                                        $intTmpIdImpuesto = $objAdmiImpuesto->getId();
                                                        $intTmpPorcentaje = $objAdmiImpuesto->getPorcentajeImpuesto();
                                                        $intTmpImpuesto   = (($intPrecioNuevo * $intTmpPorcentaje)/100);

                                                        if($objAdmiImpuesto->getTipoImpuesto() == 'ICE')
                                                        {
                                                            $boolDocumentoTieneIce     = true;
                                                            $floatImpuestoIceAcumulado += $intTmpImpuesto;
                                                        }
                                                        
                                                        $intIdTmpDetalle = $entityInfoDocumentoFinancieroDet->getId();
                                                        
                                                        $entityInfoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                                        $entityInfoDocumentoFinancieroImp->setDetalleDocId($intIdTmpDetalle);
                                                        $entityInfoDocumentoFinancieroImp->setImpuestoId($intTmpIdImpuesto);
                                                        $entityInfoDocumentoFinancieroImp->setValorImpuesto($intTmpImpuesto);
                                                        $entityInfoDocumentoFinancieroImp->setPorcentaje($intTmpPorcentaje);
                                                        $entityInfoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                                        $entityInfoDocumentoFinancieroImp->setUsrCreacion($strUser);
                                                        $emFinanciero->persist($entityInfoDocumentoFinancieroImp);
                                                        $emFinanciero->flush();

                                                        if( $objInfoGrid->tipoOrden != "PAGR" )
                                                        {
                                                            $intImpuesto += $intTmpImpuesto;
                                                        }//( $objInfoDatosInformacionGrid->tipoOrden != "PAGR" )
                                                    }//($boolImpuesto)
                                                }//($objAdmiImpuesto)
                                            }//($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImpuesto)
                                        }//($objInfoProductoImpuestosPrioridad1)


                                        $arrayParametrosImpuestosPrioridad['intPrioridad'] = 2;

                                        $arrayImpuestosPrioridad2           = $emComercial->getRepository('schemaBundle:InfoProductoImpuesto')
                                                                              ->getInfoImpuestoByCriterios( $arrayParametrosImpuestosPrioridad );
                                        $objInfoProductoImpuestosPrioridad2 = $arrayImpuestosPrioridad2['registros'];

                                        if($objInfoProductoImpuestosPrioridad2)
                                        {
                                            foreach($objInfoProductoImpuestosPrioridad2 as $objInfoProductoImpuesto)
                                            {
                                                $boolImpuesto     = true;
                                                $objAdmiImpuesto  = $objInfoProductoImpuesto->getImpuestoId();
                                                
                                                if($objAdmiImpuesto)
                                                {
                                                    if( $strPagaIva != "S" && $objAdmiImpuesto->getTipoImpuesto() == 'IVA' )
                                                    {
                                                        $boolImpuesto = false;
                                                    }

                                                    if($boolImpuesto)
                                                    {
                                                        //Id del impuesto con el que se va a crear la factura
                                                        $intTmpIdImpuesto = $objAdmiImpuesto->getId();
                                                        $intTmpPorcentaje = $objAdmiImpuesto->getPorcentajeImpuesto();

                                                        /*
                                                         * Se verifica si el usuario seleccionó algún impuesto IVA para crear la factura para darle 
                                                         * prioridad con el impuesto seleccionado.
                                                         */
                                                        if($intIdImpuesto && $objAdmiImpuesto->getTipoImpuesto() == 'IVA')
                                                        {
                                                            $objAdmiImpuestoSelected = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                                                                 ->findOneById($intIdImpuesto);

                                                            if( $objAdmiImpuestoSelected )
                                                            {
                                                                $intTmpIdImpuesto = $objAdmiImpuestoSelected->getId();
                                                                $intTmpPorcentaje = $objAdmiImpuestoSelected->getPorcentajeImpuesto();
                                                            }//( $objAdmiImpuestoSelected )
                                                        }//($intIdImpuesto)

                                                        $intTmpImpuesto   = ( ( ($intPrecioNuevo + $floatImpuestoIceAcumulado) 
                                                                                 * $intTmpPorcentaje ) / 100 );
                                                        
                                                        $intIdTmpDetalle = $entityInfoDocumentoFinancieroDet->getId();
                                                        
                                                        $entityInfoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                                        $entityInfoDocumentoFinancieroImp->setDetalleDocId($intIdTmpDetalle);
                                                        $entityInfoDocumentoFinancieroImp->setImpuestoId($intTmpIdImpuesto);
                                                        $entityInfoDocumentoFinancieroImp->setValorImpuesto($intTmpImpuesto);
                                                        $entityInfoDocumentoFinancieroImp->setPorcentaje($intTmpPorcentaje);
                                                        $entityInfoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                                        $entityInfoDocumentoFinancieroImp->setUsrCreacion($strUser);
                                                        $emFinanciero->persist($entityInfoDocumentoFinancieroImp);
                                                        $emFinanciero->flush();

                                                        if( $objInfoGrid->tipoOrden != "PAGR" )
                                                        {
                                                            $intImpuesto += $intTmpImpuesto;
                                                        }//( $objInfoDatosInformacionGrid->tipoOrden != "PAGR" )
                                                    }//($boolImpuesto)
                                                }//($objAdmiImpuesto)
                                            }//($objInfoProductoImpuestosPrioridad2 as $objInfoProductoImpuesto)
                                        }//($objInfoProductoImpuestosPrioridad2)

                                        if( $objInfoGrid->tipoOrden != "PAGR" )
                                        {
                                            $intSubtotal += $intPrecioSinDescuento;
                                        }
                                        else
                                        {
                                            $intImpuesto += $objInfoGrid->impuesto;
                                            $intSubtotal += $objInfoGrid->precio;
                                        }

                                        $floatValorTmpDescuento = $objInfoGrid->descuento;
                                        $floatValorTmpDescuento = (!empty($floatValorTmpDescuento) ? $floatValorTmpDescuento : 0);
                                        $intDescuento           += $floatValorTmpDescuento;
                                    }//($objInfoGrid->tipo == 'PR')
                                    else
                                    {
                                        $intTmpImpuesto = 0;

                                        /* Cuando es plan voy a la tabla InfoPlanCab para sacar la bandera del impuesto */
                                        $entityInfoDocumentoFinancieroDet->setPlanId($objInfoGrid->codigo);
                                        $entityAdmiPlanCab = $emComercial->getRepository('schemaBundle:InfoPlanCab')->find($objInfoGrid->codigo);

                                        if($entityAdmiPlanCab)
                                        {
                                            $intPrecioNuevo         =  ($objInfoGrid->precio * $objInfoGrid->cantidad) - $objInfoGrid->descuento;
                                            $intPrecioSinDescuento  =  ($objInfoGrid->precio * $objInfoGrid->cantidad);
                                            $intSumSubtotal         += $intPrecioNuevo;

                                            if( $entityAdmiPlanCab->getIva() == 'S' && $strPagaIva == "S" )
                                            {
                                                if($entityAdmiImpuestoIva)
                                                {
                                                    //Id del impuesto con el que se va a crear la factura
                                                    $intTmpIdImpuesto = $entityAdmiImpuestoIva->getId();
                                                    $intTmpPorcentaje = $entityAdmiImpuestoIva->getPorcentajeImpuesto();

                                                    /*
                                                     * Se verifica si el usuario seleccionó algún impuesto IVA para crear la factura para darle 
                                                     * prioridad con el impuesto seleccionado.
                                                     */
                                                    if($intIdImpuesto && $entityAdmiImpuestoIva->getTipoImpuesto() == 'IVA')
                                                    {
                                                        $objAdmiImpuestoSelected = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                                                             ->findOneById($intIdImpuesto);

                                                        if( $objAdmiImpuestoSelected )
                                                        {
                                                            $intTmpIdImpuesto = $intIdImpuesto;
                                                            $intTmpPorcentaje = $objAdmiImpuestoSelected->getPorcentajeImpuesto();
                                                        }//( $objAdmiImpuestoSelected )
                                                    }//($intIdImpuesto && $entityAdmiImpuestoIva->getTipoImpuesto() == 'IVA')

                                                    $intTmpImpuesto = (($intPrecioNuevo * $intTmpPorcentaje) / 100);

                                                    //Registro del impuesto
                                                    $entityInfoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                                    $entityInfoDocumentoFinancieroImp->setDetalleDocId($entityInfoDocumentoFinancieroDet->getId());
                                                    $entityInfoDocumentoFinancieroImp->setImpuestoId($intTmpIdImpuesto);
                                                    $entityInfoDocumentoFinancieroImp->setValorImpuesto($intTmpImpuesto);
                                                    $entityInfoDocumentoFinancieroImp->setPorcentaje($intTmpPorcentaje);
                                                    $entityInfoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                                    $entityInfoDocumentoFinancieroImp->setUsrCreacion($strUser);
                                                    $emFinanciero->persist($entityInfoDocumentoFinancieroImp);
                                                    $emFinanciero->flush();
                                                }//($entityAdmiImpuestoIva)
                                            }//( $entityAdmiPlanCab->getIva() == 'S' && $strPagaIva == "S" )

                                            if( $objInfoGrid->tipoOrden != "PAGR" )
                                            {
                                                $intImpuesto    +=  $intTmpImpuesto;
                                                $intSubtotal    +=  $intPrecioSinDescuento;
                                                $intDescuento   +=  $objInfoGrid->descuento;
                                            }
                                            else
                                            {
                                                $intImpuesto    +=  $objInfoGrid->impuesto;
                                                $intSubtotal    +=  $objInfoGrid->precio;
                                                $intDescuento   +=  $objInfoGrid->descuento;
                                            }
                                        }//($entityAdmiPlanCab)
                                    }//($objInfoGrid->tipo != 'PR')
                                    
                                    /**
                                     * Bloque que obtiene el valor de compensación enviado por el grid para ser acumulado y posteriormente guardado
                                     * en la cabecera
                                     */
                                    $intCantidad               = $objInfoGrid->cantidad;
                                    $intCantidad               = ( !empty($intCantidad) ) ? $intCantidad : 0;
                                    $floatValorTmpCompensacion = $objInfoGrid->compensacionSolidaria 
                                                                 ? ($objInfoGrid->compensacionSolidaria * $intCantidad) : 0 ;

                                    $floatDescuentoCompensacionAcumulado += $floatValorTmpCompensacion;
                                }//( floatval($objInfoDatosInformacionGrid->precio) > 0 )
                            }//foreach($arrayInformacionGrid as $objInfoGrid)
                        }//($arrayInformacionGrid)
                
                        $entityInfoDocumentoFinancieroCabAct = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                            ->find($entityInfoDocumentoFinancieroCab->getId());
                        $entityInfoDocumentoFinancieroCabAct->setSubtotal(round($intSubtotal, 2));
                        $entityInfoDocumentoFinancieroCabAct->setSubtotalConImpuesto(round($intImpuesto, 2));
                        $entityInfoDocumentoFinancieroCabAct->setSubtotalDescuento(round($intDescuento, 2));
                        $entityInfoDocumentoFinancieroCabAct->setDescuentoCompensacion(round($floatDescuentoCompensacionAcumulado, 2));
                        $intValorTotal = ( round($intSubtotal, 2) - round($intDescuento, 2) - round($floatDescuentoCompensacionAcumulado, 2) ) 
                                           + round($intImpuesto, 2);
                        $entityInfoDocumentoFinancieroCabAct->setValorTotal(round($intValorTotal, 2));
                        $emFinanciero->persist($entityInfoDocumentoFinancieroCabAct);
                        $emFinanciero->flush();
                        
                        /**
                         * Bloque validador de documento
                         * 
                         * Verifica si se debe regularizar el documento que contiene al menos un detalle con impuesto de ICE
                         */
                        if( is_object($entityInfoDocumentoFinancieroCab) && $boolDocumentoTieneIce )
                        {
                            $arrayParametrosValidador = array('intIdDocumento' => $entityInfoDocumentoFinancieroCab->getId());
                            $arrayRespuestaValidador  = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                                     ->getValidadorDocumentosFinancieros($arrayParametrosValidador);

                            if( !empty($arrayRespuestaValidador) )
                            {
                                $strBanderaValidador = ( isset($arrayRespuestaValidador['strValidador']) 
                                                         && !empty($arrayRespuestaValidador['strValidador']) ) 
                                                       ? $arrayRespuestaValidador['strValidador'] : 'N';

                                if( $strBanderaValidador == "S" )
                                {
                                    $floatDiferenciaImpuestos = ( isset($arrayRespuestaValidador['floatDiferenciaImpuestos']) 
                                                                  && !empty($arrayRespuestaValidador['floatDiferenciaImpuestos']) ) 
                                                                 ? $arrayRespuestaValidador['floatDiferenciaImpuestos'] : 0;

                                    if( floatval($floatDiferenciaImpuestos) > 0 )
                                    {
                                        $objDocumentoActualizar = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                               ->find($entityInfoDocumentoFinancieroCab->getId());

                                        if( is_object($objDocumentoActualizar) )
                                        {
                                            $floatSubtotalImpuestos = $objDocumentoActualizar->getSubtotalConImpuesto();
                                            $floatSubtotalImpuestos = floatval($floatSubtotalImpuestos) + floatval($floatDiferenciaImpuestos);
                                            $floatValorSubtotal     = $objDocumentoActualizar->getSubtotal() 
                                                                      ? $objDocumentoActualizar->getSubtotal() : 0;
                                            $floatValorDescuento    = $objDocumentoActualizar->getSubtotalDescuento()
                                                                      ? $objDocumentoActualizar->getSubtotalDescuento() : 0;
                                            $floatValorCompensacion = $objDocumentoActualizar->getDescuentoCompensacion()
                                                                      ? $objDocumentoActualizar->getDescuentoCompensacion() : 0;
                                            $floatValorTotal        = floatval($floatSubtotalImpuestos) + floatval($floatValorSubtotal)
                                                                      - floatval($floatValorCompensacion) - floatval($floatValorDescuento);

                                            $objDocumentoActualizar->setSubtotalConImpuesto(round($floatSubtotalImpuestos, 2));
                                            $objDocumentoActualizar->setValorTotal(round($floatValorTotal, 2));
                                            $emFinanciero->persist($objDocumentoActualizar);
                                            $emFinanciero->flush();
                                        }//( is_object($objDocumentoActualizar) )
                                    }//( floatval($floatDiferenciaImpuestos) > 0 )
                                }//( $strBanderaValidador == "S" )
                            }//( !empty($arrayRespuestaValidador) )
                        }//( is_object($entityInfoDocumentoFinancieroCab) && $boolDocumentoTieneIce )
                        /**
                         * Fin Bloque validador de documento
                         */

                        //Genera el desgloce detalle del documento enviado por parametros
                        $arrayParametrosIn["strPrefijoEmpresa"] = $strPrefijoEmpresa;
                        $arrayParametrosIn["id"]                = $entityInfoDocumentoFinancieroCabAct->getId();
                        $arrayParametrosIn["strCodDocumento"]   = $entityAdmiTipoDocumentoFinanciero->getCodigoTipoDocumento();
                        $arrayParametrosIn["estado"]            = $strEstado;
                        
                        $objParametroCab  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                      ->findOneBy( array('nombreParametro' => 'CARGO REPROCESO DEBITO', 
                                                                         'estado'          => 'Activo') );
                        if(is_object($objParametroCab))
                        {                         
                            // Se verifica si existe solicitud de cargo por reproceso, consultando por id del punto
                            if( $strPrefijoEmpresa == "MD" && $boolTieneReprocesoDebito)
                            {
                                
                                $arrayParametros                   = array();
                                $arrayParametros['strObservacion'] = "Finalizacion de la solicitud";                               
                                $arrayParametros['strEstado']      = 'Finalizada';
                                $arrayParametros['strIpCreacion']  = "127.0.0.1";
                                $arrayParametros['strUsrCreacion'] = $strUser;                                
                                $serviceSolicitudes                = $this->get('comercial.Solicitudes');

                                $arraySolicitudesReproceso = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                         ->getSolicitudPorPunto($intIdPunto, 
                                                                                                'SOLICITUD CARGO REPROCESO DEBITO', 
                                                                                                'Pendiente');                   

                                if(count($arraySolicitudesReproceso)>0)
                                {
                                    foreach($arraySolicitudesReproceso as $arraySolicitudReproceso)
                                    {
                                        $arrayParametros['intIdSolicitud'] = $arraySolicitudReproceso['id'];
                                        
                                        $serviceSolicitudes->actualizaSolicitud($arrayParametros);                            
                                    }
                                }                    

                            } 
                        }
                        
                        if ($emFinanciero->getConnection()->isTransactionActive())
                        {
                            $emFinanciero->getConnection()->commit();
                        }

                        if($emComercial->getConnection()->isTransactionActive())
                        {
                           $emComercial->getConnection()->commit();
                        }

                        return $this->redirect($this->generateUrl('facturasproporcionales_show', 
                                                                  array('id' => $entityInfoDocumentoFinancieroCab->getId())));
                    }//($formInfoDocumentoFinancieroCab->isValid())
                }//($intIdPunto)
            }//( $datetimeFechaInicio && $datetimeFechaFin)
            else
            {
                throw new \Exception("No se han enviado las fechas para obtener el proporcional de la factura");
            }//( !$datetimeFechaInicio && !$datetimeFechaFin)
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'Facturación Proporcional', 
                                       'Error al guardar la factura proporcional. '.$e->getMessage(), 
                                       $strUser, 
                                       $strIpCreacion );
            
            if ($emFinanciero->getConnection()->isTransactionActive())
            {
                $emFinanciero->getConnection()->rollback();
            }

            if($emComercial->getConnection()->isTransactionActive())
            {
               $emComercial->getConnection()->rollback();
            }
        }
        
        $emFinanciero->getConnection()->close();
        $emComercial->getConnection()->close();
        
        return $this->redirect($this->generateUrl('facturasproporcionales_new'));
    }
    

    /**
     * Displays a form to edit an existing InfoDocumentoFinancieroCab entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_financiero");

        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
        }

        $editForm = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

		//tomar el punto de la session
        $punto="28";
        
        return $this->render('financieroBundle:FacturasProporcional:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'punto_id'=>$punto
        ));
    }

    /**
     * Edits an existing InfoDocumentoFinancieroCab entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
		$informacionGrid=$request->get('listado_informacion');
		$informacionGrid=json_decode($informacionGrid);
		
		$punto_id=$request->get('punto_id');		
		$session=$request->getSession();
		$empresa_id=$session->get('idEmpresa');
		$oficina_id=$session->get('idOficina');
		$user=$session->get('user');
		
		//$empresa_id="10";
		//$oficina_id="1";
		$estado="Activo";
		
        $em = $this->getDoctrine()->getManager("telconet_financiero");

        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
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
            return $this->redirect($this->generateUrl('infodocumentofinancierocab_edit', array('id' => $id)));
        }

        return $this->render('financieroBundle:FacturasProporcional:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a InfoDocumentoFinancieroCab entity.
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
                throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
            }

			$entity->setEstadoImpresionFact("Inactivo");
			$em->persist($entity);
            //$em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infodocumentofinancierocab'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }


    /**
     * Documentacion para la función 'getFechasDiasPeriodoAjaxAction'
     * 
     * Método que retorno el período de fechas en la cual corresponde la fecha de activación o rango a comparar.
     * 
     * @return JsonResponse $objJsonResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 22-06-2017
     */
    public function getFechasDiasPeriodoAjaxAction()
    {
        $objJsonResponse      = new JsonResponse();
        $objRequest           = $this->getRequest();
        $objSession           = $objRequest->getSession();
        $strEmpresaCod        = $objSession->get('idEmpresa');
        $strIpCreacion        = $objRequest->getClientIp();
        $serviceUtil          = $this->get('schema.Util');
        $strUsuario           = $objSession->get('user');
        $arrayFechaActivacion = explode('T', $objRequest->get("strFechaActivacion"));
        $emFinanciero         = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayResultados      = array('strFechaInicioPeriodo' => '', 
                                      'strFechaFinPeriodo'    => '', 
                                      'intTotalDiasMes'       => 0,
                                      'intTotalDiasRestantes' => 0);
        
        try
        {
            /**
             * Bloque que obtiene los días del mes en curso entre las fechas de facturación
             */
            $arrayParametrosFechaProporcional = array('strEmpresaCod'      => $strEmpresaCod,
                                                      'strFechaActivacion' => $arrayFechaActivacion[0]);
            $arrayResultados                  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                             ->getFechasDiasPeriodo($arrayParametrosFechaProporcional);
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError( 'Telcos+',
                                       'FacturasPropocionalController.getFechasDiasPeriodoAjaxAction',
                                       'Error al obtener las fechas y el periodo de la fechas seleccionadas por el usuario. '.$e->getMessage(),
                                       $strUsuario,
                                       $strIpCreacion );
        }
        
        $objJsonResponse->setData($arrayResultados);
        
        return $objJsonResponse;
    }
    
    
    /**
     * Metodo infoOrdenesPtoClienteAction, muestra la informacion del grid al crear una nueva factura
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 24-05-2016 - Se adapta la función para obtener los valores de los productos y/o planes al porcentaje del '14%'
     * @since 1.1
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 - Se adapta la función para obtener los valores de los productos y/o planes dependiendo del porcentaje del IVA elegido por el 
     *                usuario, en caso de no elegir ningún impuesto se calculará todo al impuesto del IVA en estado 'Activo'
     * @since 20-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 22-06-2016 - Se modifica la presentacion de la descripcion de los items de la factura por empresa'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 22-06-2016 - Se realiza cálculo de impuesto ICE
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 30-06-2016 - Se verifica si la consulta de los servicios a facturar es con o sin Frecuencia
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 13-09-2016 - Se verifica si el cliente debe ser compensado mediante la variable '$strEsCompensado' y se obtiene los valores 
     *                           correspondientes para ser mostrados en la factura del cliente
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 04-10-2016 - Se corrige que se envíe la variable '$arrayDetalleListadoOrden' vacía cuando no existen servicios asociados al punto
     *                           de facturación, para evitar que se realizar una factura proporcional sin detalles.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.9 09-02-2017 - Se realiza la modificación en la función para que MD compensen al 2% las facturas realizadas al 14% de los clientes
     *                           que pertenecen a los cantones MANTA y PORTOVIEJO.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 2.2 31-03-2017 - Se realiza modificación para el ingreso de un nuevo detalle a la factura por cargo de reproceso de débito.
     * 
     * @author Esson Franco <efranco@telconet.ec>
     * @version 2.3 21-06-2017 - Se realiza modificación para obtener el valor proporcional de una factura cuando el usuario ha seleccionado un rango
     *                           de fechas que abarca a más de un mes.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 2.4 23-06-2017 - Se agrega envío de parámetro porcentaje impuesto a ser utilizado en edición de precio en detalles de facturas.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 2.4 10-07-2017 - Se agrega envío de parámetro porcentaje impuesto ICE a ser utilizado en edición de precio en detalles de facturas.
     * 
     */
    public function infoOrdenesPtoClienteAction()
    {
        $request                  = $this->getRequest();
        $objSession               = $request->getSession();
        $strEmpresaCod            = $objSession->get('idEmpresa');
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa');
        $fechaDesde               = explode('T',$request->get("fechaDesde"));
        $fechaHasta               = explode('T',$request->get("fechaHasta"));
        $strPagaIva               = $request->get("strPagaIva");
        $cantidadDias             = $this->restarFechas($fechaDesde[0],$fechaHasta[0]);
        $strPagaIce               = $request->get('strPagaIce');
        $intIdImpuesto            = $request->get('intImpuestoId');
        $strSinFrecuencia         = $request->get('strSinFrecuencia') ? $request->get('strSinFrecuencia') : 'N';
        $strEsCompensado          = $request->get('strEsCompensado') ? $request->get('strEsCompensado') : 'NO';
        $strTieneCargoRep         = "N";
        $intCantidadSolReproceso  = 0;
        $floatPrecioReproceso     = 0;
        $intDiasTotales           = 0;    
        
        $arrayDetalleListadoOrden = array();
        $arrayListadoOrden        = array();

		$session    = $request->getSession();
		$cliente    = $session->get('cliente');
		$ptocliente = $session->get('ptoCliente');
		$id_empresa = $session->get('idEmpresa');
        
        //informacion presente en el grid
		$informacionGrid = $request->get('informacionGrid');
		$informacionGrid = json_decode($informacionGrid);
		
        $em             = $this->get('doctrine')->getManager('telconet');	
        $emGeneral      = $this->get('doctrine')->getManager('telconet_general');
        $emFinanciero   = $this->get('doctrine')->getManager('telconet_financiero');
        
        /**
         * Bloque que obtiene el porcentaje del impuesto de COMPENSACION
         */
        $objAdmiImpuestoCompensacion = null;
        
        if( $strEsCompensado == "SI" )
        {
            $objAdmiImpuestoCompensacion = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                     ->findOneBy(array('tipoImpuesto' => 'COM', 'estado' => 'Activo'));
        }
        
        $objAdmiImpuestoIce = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                        ->findOneBy(array('tipoImpuesto' => 'ICE', 'estado' => 'Activo')); 
        if(is_object($objAdmiImpuestoIce))
        {
            $floatPorcentajeImpIce = $objAdmiImpuestoIce->getPorcentajeImpuesto();
        }        

        $estado="Activo";


        /**
         * Bloque que obtiene los días del mes en curso entre las fechas de facturación
         */
        $arrayParametrosFechaProporcional = array('strEmpresaCod'      => $strEmpresaCod,
                                                  'strFechaActivacion' => $fechaDesde[0]);
        $arrayResultadoDiasRestantes      = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                         ->getFechasDiasPeriodo($arrayParametrosFechaProporcional);
        
        if( isset($arrayResultadoDiasRestantes['intTotalDiasMes']) && intval($arrayResultadoDiasRestantes['intTotalDiasMes']) > 0 )
        {
            $intDiasTotales = intval($arrayResultadoDiasRestantes['intTotalDiasMes']);
        }

        
        /*
         * Bloque que verifica si se seleccionó la opción de precargar los servicios SIN FRENCUENCIA.
         * SIN FRECUENCIA quiere decir,  el campo FRECUENCIA_PRODUCTO de la tabla DB_COMERCIAL.INFO_SERVICIO en NULL o cero
         */
        if( $strPrefijoEmpresa == "TN" )
        {
            if( $strSinFrecuencia == "S" )
            {
                $resultado = $em->getRepository('schemaBundle:InfoServicio')
                                ->findServiciosPorEmpresaPorPuntoPorEstado($id_empresa, $ptocliente['id'], $estado, 'igualACero');
            }
            else
            {
                $resultado = $em->getRepository('schemaBundle:InfoServicio')
                                ->findServiciosPorEmpresaPorPuntoPorEstado($id_empresa, $ptocliente['id'], $estado, 'mayorIgualQue1');            
            }//( $strSinFrecuencia == "S" )
        }
        else
        {
            $resultado = $em->getRepository('schemaBundle:InfoServicio')
                            ->findServiciosPorEmpresaPorPuntoPorEstado($id_empresa, $ptocliente['id'], $estado); 
        }//( $strPrefijoEmpresa == "TN" )
        
        
        if( isset($resultado['registros']) && !empty($resultado['registros']) )
        {
            $arrayListadoOrden = $resultado['registros'];
        }
        
        if( !empty($arrayListadoOrden) )
        {
            $entityAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                               ->findOneBy(array('tipoImpuesto' => 'IVA', 'estado' => 'Activo'));
            
            foreach($arrayListadoOrden as $ord)
            {
                $floatImpuestoAcumulado      = 0;
                $floatImpuestoIvaAcumulado   = 0;
                $floatImpuestoIceAcumulado   = 0;
                $floatOtrosImpuestoAcumulado = 0;
                $floatDescuento              = 0;
                $floatDescuentoUnitario      = 0;
                $floatCompensacionSolidaria  = 0;
                $arrayItem                   = array();
                $floatValor                  = ( ($ord->getPrecioVenta() * $cantidadDias) / ($intDiasTotales) );
                $arrayItem['precio']         = round($floatValor,2);
                $arrayItem['precio_uni']     = round($ord->getPrecioVenta(),2);
                $arrayItem['preciototal']    = $ord->getPrecioVenta();
                $arrayItem['cantidad']       = $ord->getCantidad();
                
                $arrayItem['porcentajeImpuestoIce'] = $floatPorcentajeImpIce;
                
                // Se verifica si existe solicitud de cargo por reproceso, consultando por id del punto.
                if( $strPrefijoEmpresa == "MD" )
                {
                    $objAdmiParametroCabReproceso  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                               ->findOneBy( array('nombreParametro' => 'CARGO REPROCESO DEBITO', 
                                                                                  'estado'          => 'Activo') );
                    if(is_object($objAdmiParametroCabReproceso))
                    {
                        $arraySolicitudesReproceso = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                        ->getSolicitudPorPunto($ptocliente['id'], 
                                                                             'SOLICITUD CARGO REPROCESO DEBITO', 
                                                                             'Pendiente');                   

                        if(count($arraySolicitudesReproceso)>0)
                        {
                            $strTieneCargoRep   = "S";
                            
                            $intCantidadSolReproceso  = count($arraySolicitudesReproceso); 

                            $objParametroDetReproceso = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->findOneBy( array( 'estado'      => 'Activo',
                                                                                      'parametroId' => $objAdmiParametroCabReproceso) );

                            if(is_object($objParametroDetReproceso))
                            {
                                $strValorCargoReproceso = $objParametroDetReproceso->getValor2();
                                
                                $floatPrecioReproceso   = floatval($strValorCargoReproceso); 
                            }

                        }
                    }

                }                  
                
                
                if( $ord->getPorcentajeDescuento() )
                {
                    $floatDescuento = round( (($floatValor * $ord->getCantidad() * $ord->getPorcentajeDescuento())/100), 2 );
                }
                elseif( $ord->getValorDescuento() )
                {
                    $floatDescuento         = $ord->getValorDescuento();
                    $floatDescuentoUnitario = $floatDescuento / $ord->getCantidad();
                }
                
                
                /**
                 * Bloque que calcula el descuento proporcional para la factura
                 */
                $floatDescuento         = ( ( $floatDescuento * $cantidadDias ) / $intDiasTotales );
                $floatDescuento         = round($floatDescuento, 2);
                $floatDescuentoUnitario = ( ( $floatDescuentoUnitario * $cantidadDias ) / $intDiasTotales );
                $floatDescuentoUnitario = round($floatDescuentoUnitario, 2);
                
                $arrayItem['descuento']             = $floatDescuento;
                $arrayItem['impuesto']              = $floatImpuestoAcumulado;
                $arrayItem['impuestoIva']           = $floatImpuestoIvaAcumulado;
                $arrayItem['impuestoIce']           = $floatImpuestoIceAcumulado;
                $arrayItem['impuestoOtros']         = $floatOtrosImpuestoAcumulado;
                $arrayItem['fechaActivacion']       = "";
                $arrayItem['compensacionSolidaria'] = $floatCompensacionSolidaria;
                
                /*
                 * Verifico el historial en que se activo, si retorna presento la fecha, sinop se encuentra no presento el producto o plan dentro de
                 * la precargada
                 */
                $servicioHistorial = $em->getRepository('schemaBundle:InfoServicio')->findHistorial($ord->getId());
					
                if($ord->getProductoId())
                {
                    $arrayItem['codigo']      = $ord->getProductoId()->getId();
                    $arrayItem['informacion'] = $ord->getProductoId()->getDescripcionProducto();
                    $arrayItem['tipo']        = 'PR';
                    $arrayItem['tipoOrden']   = 'PRE';
                    
                    $arrayParametrosImpuestosPrioridad  = array( 'intIdProducto' => $ord->getProductoId()->getId(),
                                                                 'strEstado'     => 'Activo',
                                                                 'intPrioridad'  => 1 );
                    $arrayImpuestosPrioridad1           = $em->getRepository('schemaBundle:InfoProductoImpuesto')
                                                             ->getInfoImpuestoByCriterios( $arrayParametrosImpuestosPrioridad );
                    $objInfoProductoImpuestosPrioridad1 = $arrayImpuestosPrioridad1['registros'];
                    
                    if($objInfoProductoImpuestosPrioridad1)
                    {
                        foreach($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImpuesto)
                        {
                            $floatTmpImpuesto = 0;
                            $boolImpuesto     = true;
                            $objAdmiImpuesto  = $objInfoProductoImpuesto->getImpuestoId();
                            
                            if($objAdmiImpuesto)
                            {
                                if( $strPagaIce != "SI" && $objAdmiImpuesto->getTipoImpuesto() == 'ICE' )
                                {
                                    $boolImpuesto = false;
                                }

                                if($boolImpuesto)
                                {
                                    $floatTmpImpuesto = ( ($arrayItem['precio'] - $floatDescuentoUnitario) 
                                                           * $objAdmiImpuesto->getPorcentajeImpuesto() )/100;

                                    if($objAdmiImpuesto->getTipoImpuesto() == 'ICE')
                                    {
                                        $floatImpuestoIceAcumulado += $floatTmpImpuesto;
                                    }
                                    else
                                    {
                                        $floatOtrosImpuestoAcumulado += $floatTmpImpuesto;
                                    }

                                    $floatImpuestoAcumulado += $floatTmpImpuesto;
                                }//($boolImpuesto)
                            }//($objAdmiImpuesto)
                        }//($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImpuesto)
                        
                        $arrayItem['impuesto']      = $floatImpuestoAcumulado;
                        $arrayItem['impuestoIce']   = $floatImpuestoIceAcumulado;
                        $arrayItem['impuestoOtros'] = $floatOtrosImpuestoAcumulado;
                    }//($objInfoProductoImpuestosPrioridad1)
                    
                    
                    $arrayParametrosImpuestosPrioridad['intPrioridad'] = 2;
                    $arrayImpuestosPrioridad2                          = $em->getRepository('schemaBundle:InfoProductoImpuesto')
                                                                            ->getInfoImpuestoByCriterios( $arrayParametrosImpuestosPrioridad );
                    $objInfoProductoImpuestosPrioridad2                = $arrayImpuestosPrioridad2['registros'];
                    
                    if($objInfoProductoImpuestosPrioridad2)
                    {
                        foreach($objInfoProductoImpuestosPrioridad2 as $objInfoProductoImpuesto)
                        {
                            $floatTmpImpuesto = 0;
                            $boolImpuesto     = true;
                            $objAdmiImpuesto  = $objInfoProductoImpuesto->getImpuestoId();
                            
                            if($objAdmiImpuesto)
                            {
                                if( $strPagaIva != "S" && $objAdmiImpuesto->getTipoImpuesto() == 'IVA' )
                                {
                                    $boolImpuesto = false;
                                }

                                if($boolImpuesto)
                                {
                                    /*
                                     * Se verifica si el usuario seleccionó algún impuesto IVA para crear la factura para darle prioridad con el
                                     * impuesto seleccionado.
                                     */
                                    if($intIdImpuesto && $objAdmiImpuesto->getTipoImpuesto() == 'IVA')
                                    {
                                        $objAdmiImpuestoSelected = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                                             ->findOneById($intIdImpuesto);

                                        if( $objAdmiImpuestoSelected )
                                        {
                                            $objAdmiImpuesto->setPorcentajeImpuesto($objAdmiImpuestoSelected->getPorcentajeImpuesto());
                                        }//( $objAdmiImpuestoSelected )
                                    }//($intIdImpuesto)


                                    if( $objInfoProductoImpuesto )
                                    {
                                        $floatTmpImpuesto = ( ($arrayItem['precio'] - $floatDescuentoUnitario + $arrayItem['impuestoIce']) 
                                                               * $objAdmiImpuesto->getPorcentajeImpuesto() )/100;

                                        if($objAdmiImpuesto->getTipoImpuesto() == 'IVA')
                                        {
                                            $floatImpuestoIvaAcumulado += $floatTmpImpuesto;
                                            
                                            /**
                                             * Bloque que saca el valor de COMPENSACION del subtotal del detalle de un producto
                                             */
                                            if( $strEsCompensado == "SI" && $objAdmiImpuestoCompensacion != null )
                                            {
                                                $floatPorcentajeCompensacion = $objAdmiImpuestoCompensacion->getPorcentajeImpuesto();
                                                
                                                if( !empty($floatPorcentajeCompensacion) )
                                                {
                                                    $floatCompensacionSolidaria = ( ($arrayItem['precio'] - $floatDescuentoUnitario
                                                                                     + $arrayItem['impuestoIce']) 
                                                                                     * $floatPorcentajeCompensacion )/100;
                                                }//( !empty($floatPorcentajeCompensacion) )
                                            }//( $strEsCompensado == "S" && $objAdmiImpuestoCompensacion != null )
                                        }
                                        else
                                        {
                                            $floatOtrosImpuestoAcumulado += $floatTmpImpuesto;
                                        }

                                        $floatImpuestoAcumulado += $floatTmpImpuesto;
                                    }//( $objInfoProductoImpuesto )
                                }//($boolImpuesto)
                            }//($objAdmiImpuesto)
                        }//($objInfoProductoImpuestosPrioridad2 as $objInfoProductoImpuesto)
                        
                        $arrayItem['impuesto']              = $floatImpuestoAcumulado;
                        $arrayItem['impuestoIva']           = $floatImpuestoIvaAcumulado;
                        $arrayItem['impuestoOtros']         = $floatOtrosImpuestoAcumulado;
                        $arrayItem['compensacionSolidaria'] = $floatCompensacionSolidaria;
                    }//($objInfoProductoImpuestosPrioridad2)
                }//($ord->getProductoId())
                
                if($ord->getPlanId())
                {
                    $arrayItem['codigo']      = $ord->getPlanId()->getId();
                    $arrayItem['informacion'] = $ord->getPlanId()->getNombrePlan();
                    $arrayItem['tipo']        = 'PL';
                    $arrayItem['tipoOrden']   = 'PRE';

                    if( $ord->getPlanId()->getIva() == "S" &&  $strPagaIva == "S" )
                    {
                        /*
                         * Se verifica si el usuario seleccionó algún impuesto IVA para crear la factura para darle prioridad con el impuesto
                         * seleccionado.
                         */
                        if($intIdImpuesto && $entityAdmiImpuestoIva)
                        {
                            $objAdmiImpuestoSelected = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')->findOneById($intIdImpuesto);

                            if( $objAdmiImpuestoSelected )
                            {
                                $entityAdmiImpuestoIva->setPorcentajeImpuesto($objAdmiImpuestoSelected->getPorcentajeImpuesto());
                            }//( $objAdmiImpuestoSelected )
                        }//($intIdImpuesto)
                        
                        $floatTmpImpuesto = $entityAdmiImpuestoIva 
                                            ? ( ( ($arrayItem['precio'] - $floatDescuentoUnitario )
                                                  * $entityAdmiImpuestoIva->getPorcentajeImpuesto() )/100 ) : 0;

                        /**
                         * Bloque que saca el valor de COMPENSACION del subtotal del detalle del plan
                         */
                        if( $strEsCompensado == "SI" && $objAdmiImpuestoCompensacion != null )
                        {
                            $floatPorcentajeCompensacion = $objAdmiImpuestoCompensacion->getPorcentajeImpuesto();

                            if( !empty($floatPorcentajeCompensacion) )
                            {
                                $floatCompensacionSolidaria = ( ($arrayItem['precio'] - $floatDescuentoUnitario) * $floatPorcentajeCompensacion )/100;
                            }//( !empty($floatPorcentajeCompensacion) )
                        }//( $strEsCompensado == "SI" && $objAdmiImpuestoCompensacion != null )
                        
                        $arrayItem['impuesto']              = $floatTmpImpuesto;
                        $arrayItem['impuestoIva']           = $floatTmpImpuesto;
                        $arrayItem['impuestoIce']           = 0;
                        $arrayItem['impuestoOtros']         = 0;
                        $arrayItem['compensacionSolidaria'] = $floatCompensacionSolidaria;
                    }//( $ord->getPlanId()->getIva() == "S" &&  $strPagaIva == "S" )
                }//($ord->getPlanId())
                
                if($servicioHistorial['feCreacion']!=null)
                {
                    $arrayItem['fechaActivacion'] = $servicioHistorial['feCreacion'];
                }
                
                $arrayItem['puntoId']     = $ord->getPuntoId()->getId();
                $arrayItem['fechaDesde']  = $fechaDesde[0];
                $arrayItem['fechaHasta']  = $fechaHasta[0];
                
                
                if ($strPrefijoEmpresa=='MD')
                {
                    $arrayItem['descripcion']  = "Factura proporcional desde: ".$fechaDesde[0]." hasta: ".$fechaHasta[0];
                }
                else
                {
                    //Se debe obtener la informacion referencia a la info_servicio
                    $arrayItem['descripcion']  = $ord->getDescripcionPresentaFactura();
                    $arrayItem['descripcion']  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                              ->getFVarcharClean($arrayItem['descripcion']);
                }
                
                $arrayItem['porcentajeImpuesto'] = $entityAdmiImpuestoIva->getPorcentajeImpuesto();
                
                $arrayDetalleListadoOrden[]      = $arrayItem;
            }//($arrayListadoOrden as $ord)
        }//( !empty($arrayListadoOrden) )
        
        
        if($informacionGrid)
        {
            foreach($informacionGrid as $info)
            {
                $arrayItem                          = array();
                $arrayItem['codigo']                = $info->codigo;
                $arrayItem['informacion']           = $info->informacion;
                $arrayItem['precio']                = $info->precio;
                $arrayItem['cantidad']              = $info->cantidad;
                $arrayItem['descuento']             = $info->descuento;
                $arrayItem['tipo']                  = $info->tipo;
                $arrayItem['tipoOrden']             = $info->tipoOrden;
                $arrayItem['fechaActivacion']       = $info->fechaActivacion;
                $arrayItem['puntoId']               = $info->puntoId;
                $arrayItem['fechaDesde']            = $info->fechaDesde;
                $arrayItem['fechaHasta']            = $info->fechaHasta;
                $arrayItem['precio_uni']            = $info->precio;
                $arrayItem['impuesto']              = $info->impuesto;
                $arrayItem['impuestoIva']           = $info->impuestoIva;
                $arrayItem['impuestoIce']           = $info->impuestoIce;
                $arrayItem['impuestoOtros']         = $info->impuestoOtros;
                $arrayItem['compensacionSolidaria'] = $info->compensacionSolidaria;
                $arrayItem['porcentajeImpuesto']    = $info->porcentajeImpuesto;
                $arrayItem['porcentajeImpuestoIce'] = $info->porcentajeImpuestoIce;

                $arrayDetalleListadoOrden[] = $arrayItem;
            }
        }
        $objParametroCab  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                      ->findOneBy( array('nombreParametro' => 'CARGO REPROCESO DEBITO', 
                                                         'estado'          => 'Activo') );
        if(is_object( $objParametroCab))
        {                 
            // Se agrega detalle por cargo de reproceso de débito
            if("MD"=== $strPrefijoEmpresa && "S"=== $strTieneCargoRep)
            {
                $floatPorcentajeImp        = 0; 
                $objProductoCargoReproceso = $em->getRepository('schemaBundle:AdmiProducto')->findOneBy(array('codigoProducto'=>'CGC'));

                if(is_object($objProductoCargoReproceso))
                {
                    if($intIdImpuesto && $entityAdmiImpuestoIva)
                    {
                        $objAdmiImpuesto = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')->findOneById($intIdImpuesto);

                        if(is_object($objAdmiImpuesto))
                        {
                            $floatPorcentajeImp = $objAdmiImpuesto->getPorcentajeImpuesto();
                        }
                    }              
                    $arrayDatosListadoOrden                          = array();
                    $arrayDatosListadoOrden['precio']                = $floatPrecioReproceso;
                    $arrayDatosListadoOrden['cantidad']              = $intCantidadSolReproceso;
                    $arrayDatosListadoOrden['descuento']             = 0;           
                    $arrayDatosListadoOrden['codigo']                = $objProductoCargoReproceso->getId();
                    $arrayDatosListadoOrden['informacion']           = $objProductoCargoReproceso->getDescripcionProducto();
                    $arrayDatosListadoOrden['tipo']                  = 'PR';
                    $arrayDatosListadoOrden['tipoOrden']             = 'PRE';
                    $arrayDatosListadoOrden['fechaActivacion']       = null;
                    $arrayDatosListadoOrden['puntoId']               = $arrayPtoCliente['id'];
                    $arrayDatosListadoOrden['tieneImpuesto']         = "";
                    $arrayDatosListadoOrden['descripcion']           = $objProductoCargoReproceso->getDescripcionProducto();
                    $arrayDatosListadoOrden['impuesto']              = 0;
                    $arrayDatosListadoOrden['impuestoIva']           = ( ($floatPrecioReproceso) * $floatPorcentajeImp )/100;
                    $arrayDatosListadoOrden['impuestoIce']           = 0;
                    $arrayDatosListadoOrden['impuestoOtros']         = 0;
                    $arrayDatosListadoOrden['compensacionSolidaria'] = 0; 
                    $arrayDatosListadoOrden['porcentajeImpuesto']    = $floatPorcentajeImp;
                    $arrayDatosListadoOrden['porcentajeImpuestoIce'] = $floatPorcentajeImpIce;
                    $arrayDetalleListadoOrden[]                      = $arrayDatosListadoOrden; 
                }
            }
        }        
     
        $response = new Response(json_encode(array('listadoInformacion'=>$arrayDetalleListadoOrden)));
        $response->headers->set('Content-type', 'text/json');

        return $response;
    }
    
    /**    
     * Documentación para el método 'detalleFacturaAction'.
     *
     * Descripcion: Función que permite obtener el detalle de la factura.
     * 
     * version 1.0 Versión Inicial
     * 
     * @author  Edgar Holguin <eholguin@telconet.ec>
     * @version 1.1 18-05-2017 - Se modifica para que el campo descuento a mostrar  en el detalle de la factura se visualice el valor del mismo 
     *                           si existe.
     * 
     * @author  Gustavo Narea <gnarea@telconet.ec>
     * @version 1.2 29-01-2021 - Se agrega envío de parámetro login     
     * 
     * @author  Gustavo Narea <gnarea@telconet.ec>
     * @version 1.3 09-02-2021 - Se controlan los productos adicionales en los detalles de la factura 
     * 
     * @author  Gustavo Narea <gnarea@telconet.ec>
     * @version 1.4 10-03-2021 - Se controla el punto de la factura y se extrae login
     * 
     */    
    public function detalleFacturaAction()
    {
		$objRequest = $this->getRequest();
        $intFacturaid=$objRequest->get('facturaid');
		
        $objEmFinanciero = $this->get('doctrine')->getManager('telconet_financiero');	
        $objResultado= $objEmFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                        ->findByDocumentoId($intFacturaid);
        
        if(!$objResultado)
        {
                //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
                $objDetalleOrden[] = array("informacion"=>"","precio"=>"","cantidad"=>"","descuento"=>"");
        }
        else
        {
				$objEmComercial = $this->get('doctrine')->getManager('telconet');	
                $objDetalleOrden = array();
                foreach($objResultado as $objFactdet)
                {
                    if($objFactdet->getProductoId())
                    {
                        $objInformacion=$objEmComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->find($objFactdet->getProductoId());
                        $objTecn['informacion'] = $objInformacion->getDescripcionProducto();
                    }
                    if($objFactdet->getPlanId())
                    {
						$objInformacion=$objEmComercial->getRepository('schemaBundle:InfoPlanCab')->find($objFactdet->getPlanId());
                        $objTecn['informacion'] = $objInformacion->getNombrePlan();
                    }
                    $objTecn['precio'] = $objFactdet->getPrecioVentaFacproDetalle();
                    $objTecn['cantidad'] = $objFactdet->getCantidad();
                    
                    if($objFactdet->getDescuentoFacproDetalle() > 0)
                    {
                        $objTecn['descuento'] = $objFactdet->getDescuentoFacproDetalle(); 
                    }
                    else if($objFactdet->getPorcetanjeDescuentoFacpro() > 0)
                    {
                        $objTecn['descuento'] = $objFactdet->getPorcetanjeDescuentoFacpro();
                    }                
                    $objTecn['observacion'] = $objFactdet->getObservacionesFacturaDetalle();
                    if($objFactdet->getPuntoId())
                    {
                        $entityPunto = $objEmComercial->getRepository('schemaBundle:InfoPunto')->find($objFactdet->getPuntoId());
                        $objTecn['login'] = $entityPunto->getLogin();
                    }
                    $objDetalleOrden[] = $objTecn;
                }
        }
        
        $objResponse = new Response(json_encode(array('listadoInformacion'=>$objDetalleOrden)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
	}
	
	public function detalleFacturaEditarAction()
	{
		$request = $this->getRequest();
        $facturaid=$request->get('facturaid');
        $precargado=$request->get('precargado');
		
		$session=$request->getSession();
		$id_empresa=$session->get('idEmpresa');
		
        $em = $this->get('doctrine')->getManager('telconet_financiero');	
        $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);
        
        if(!$resultado){
                //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
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
			
			//$idEmpresa = $session->get('idEmpresa');
			$em = $this->get('doctrine')->getManager('telconet');	
			//$idProducto = $request->request->get("idProducto"); 	
			$estado="Pendiente";
			//$id_empresa="10";
			$resultado= $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPuntoPorEstado($id_empresa,$puntoid,$estado);
			$listado_detalles_orden=$resultado['registros'];
			//$listado_detalles_orden = $em->getRepository('schemaBundle:InfoServicio')->findPorEstado($puntoid,$estado);
			//$clientes[] = array("idCl"=>"$telefono","loginClienteSucursal"=>"","descripcion"=>"");
			
			//print_r($listado_detalles_orden);
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
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 15-12-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 02-02-2014
     * @since 1.1
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 03-02-2014
     * @since 1.2
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.4 09-02-2014
     * @since 1.3
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.5 11-03-2014
     * @since 1.4
     * @author Alexander Samaniego
     * @version 1.6 09-09-2015
     * @since 1.5
     *
     * @author Alexander Samaniego
     * @version 1.7 28-06-2016 Se agrega funcionalidad para poder simular un comprobante XML
     * @since 1.6
     * 
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.7 08-08-2017 Se agrega el parametro numero de factura para filtrar en la busqueda
     * @since 1.7
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.8 05-03-2018 - Se cambia Filtro de FeCreacion a FeEmision.
     * @return Response el json para mostrar las facturas en el grid
     *
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.9 22-04-2022 - Se añade verificacion de boton de clonacion
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.10 19-05-2022 - Se remueve link de imprimirFactura Unitaria
     * 
     */
	public function listarTodasFacturasAction()
    {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $arrayPtoCliente    = $objSession->get('ptoCliente');
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $intIdOficina       = $objSession->get('idOficina');
        $em                 = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayFeDesde       = explode('T', $objRequest->get("fechaDesde"));
        $arrayFeHasta       = explode('T', $objRequest->get("fechaHasta"));
        $strEstado          = $objRequest->get("estado");
        $intLimite          = $objRequest->get("limit");
        $intPagina          = $objRequest->get("page");
        $intInicio          = $objRequest->get("start");
        $strNumeroFactura   = $objRequest->get("numeroFactura");
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');

        $objInfoDocumentoFinancieroCabService   = $this->get('financiero.InfoDocumentoFinancieroCab');

        if($strEstado)
        {
            $strEstado = $strEstado;
        }
        else
        {
            $strEstado = '';
        }
        if($arrayPtoCliente)
        {
            $intIdPunto = $arrayPtoCliente['id'];
        }
        else
        {
            $intIdPunto = "";
        }
        $arrayParametros['strEstado']           = $strEstado;
        $arrayParametros['intLimit']            = $intLimite;
        $arrayParametros['intPage']             = $intPagina;
        $arrayParametros['intStart']            = $intInicio;
        $arrayParametros['intIdPunto']          = $intIdPunto;
        $arrayParametros['intIdEmpresa']        = $intIdEmpresa;
        $arrayParametros['strNumeroDocumento']  = $strNumeroFactura;
        $arrayParametros['arrayTipoDoc']    = array('FACP');
        if((!$arrayFeDesde[0]) && (!$arrayFeHasta[0]))
        {
            $arrayInfoDocumentoFinanacieroCab       = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                         ->find30FacturasPorEmpresaPorEstado($arrayParametros);
            $objInfoDocumentoFinancieroCab    = $arrayInfoDocumentoFinanacieroCab['registros'];
            $intTotalRegistros                = $arrayInfoDocumentoFinanacieroCab['total'];
        }
        else
        {
            $arrayParametros['intIdOficina']        = $intIdOficina;
            $arrayParametros['strFeEmisionDesde']   = $arrayFeDesde[0];
            $arrayParametros['strFeEmisionHasta']   = $arrayFeHasta[0];
            $arrayInfoDocumentoFinanacieroCab       = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                         ->find30FacturasPorEmpresaPorEstado($arrayParametros);
            $objInfoDocumentoFinancieroCab          = $arrayInfoDocumentoFinanacieroCab['registros'];
            $intTotalRegistros                      = $arrayInfoDocumentoFinanacieroCab['total'];
        }

        $intCambiaColor = 1;

        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');
        $intMes = intval(date("m"));
        $intAnio = intval(date("Y"));
        foreach($objInfoDocumentoFinancieroCab as $objInfoDocumentoFinancieroCab):
            if($intCambiaColor % 2 == 0)
            {
                $strColorClaseCss = 'k-alt';
            }
            else
            {
                $strColorClaseCss = '';
            }
            $boolVerificaActualiza          = false;
            $boolUrlMensajesCompElectronico = true;
            $boolDocumentoPdf               = false;
            $boolDocumentoXml               = false;
            $boolSimularCompElec            = false;
            $strMsnErrorComprobante         = '';
            $boolVerificaConError           = $serviceInfoCompElectronico->getCompruebaEstadoComprobante($objInfoDocumentoFinancieroCab->getId(), 0);
            $boolVerificaRechazada          = $serviceInfoCompElectronico->getCompruebaEstadoComprobante($objInfoDocumentoFinancieroCab->getId(), 4);
            $boolVerificaEnvioNotificacion  = $serviceInfoCompElectronico->getCompruebaEstadoComprobante($objInfoDocumentoFinancieroCab->getId(), 5);
            if($boolVerificaEnvioNotificacion == 5){
                $boolDocumentoPdf               = true;
                $boolDocumentoXml               = true;
                $strMsnErrorComprobante         = '';
            }
            //verifica que se pueda actualizar el comprobante cuando este con errores o caundo sea rechazado.
            if($boolVerificaConError == true || $boolVerificaRechazada == true)
            {
                $boolVerificaActualiza = true;
            }

            $strDebePintarBotonClonar = "N";
            
            $arrayParametrosVerificacion = array();
            $arrayParametrosVerificacion["intIdPersonEmpresaRol"] = $intIdPersonEmpresaRol;
            $arrayParametrosVerificacion["intIdEmpresa"] = $intIdEmpresa;
            $arrayParametrosVerificacion["objInfoDocumentoFinancieroCab"] = $objInfoDocumentoFinancieroCab;

            $strDescripcionEmpresasPermitidas   = "CHEQUEO_EMPRESA_CLON_FACTURAS";
            $strDescripcionDetEstados           = "ESTADOS_CLONACION_FACTURAS";

            $arrayParametrosVerificacion["strDescripcionEmpresasPermitidas"] = $strDescripcionEmpresasPermitidas;
            $arrayParametrosVerificacion["strDescripcionDetEstados"] = $strDescripcionDetEstados;
            $boolPermiso= $this->get('security.context')->isGranted('ROLE_185-6877');
            $arrayParametrosVerificacion["boolPerfilClonacion"] = $boolPermiso;
            $arrayDatosVerificacionService = $objInfoDocumentoFinancieroCabService->verificarPermisosClonacion($arrayParametrosVerificacion);
        
            $boolPermisoPersonaClonar   = $arrayDatosVerificacionService["boolPermisoPersonaClonar"];
            $boolPermisoEmpresaClonar   = $arrayDatosVerificacionService["boolPermisoEmpresaClonar"];
            $boolPermisoPintarBoton     = $arrayDatosVerificacionService["boolPermisoPintarBotonClonar"];
            
            //Estados Permitidos para mostrar boton de Clonacion
            if($boolPermisoPintarBoton && $boolPermisoPersonaClonar && $boolPermisoEmpresaClonar)
            {
                $strDebePintarBotonClonar = "S";
            }

            //permite ver en el grid de facturas proporcionales el boton que simula el comprobante electronico
            if('Pendiente' === $objInfoDocumentoFinancieroCab->getEstadoImpresionFact())
            {
                $boolSimularCompElec = true;
            }

            $strUrlShow         = $this->generateUrl('infodocumentofinancierocab_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
            $strLinkShow        = $strUrlShow;

            $em_comercial = $this->get('doctrine')->getManager('telconet');
            $objInfoPunto = $em_comercial->getRepository('schemaBundle:InfoPunto')
                ->find($objInfoDocumentoFinancieroCab->getPuntoId());
            $objInfoPersonaEmpresaRol = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->find($objInfoPunto->getPersonaEmpresaRolId()->getId());

            $objAdmiTipoNegocio = $em_comercial->getRepository('schemaBundle:AdmiTipoNegocio')->find($objInfoPunto->getTipoNegocioId());

            if($objAdmiTipoNegocio)
            {
                $strTipoNegocio = $objAdmiTipoNegocio->getCodigoTipoNegocio();
            }
            else
            {
                $strTipoNegocio = '';
            }

            if($objInfoPersonaEmpresaRol->getPersonaId()->getNombres() != "" && $objInfoPersonaEmpresaRol->getPersonaId()->getApellidos() != "")
            {
                $strNombreRazonSocialCliente = $objInfoPersonaEmpresaRol->getPersonaId()->getNombres() . " " .
                    $objInfoPersonaEmpresaRol->getPersonaId()->getApellidos();
            }

            if($objInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial() != "")
            {
                $strNombreRazonSocialCliente = $objInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial();
            }

            if($objInfoDocumentoFinancieroCab->getNumeroFacturaSri() == null || $objInfoDocumentoFinancieroCab->getNumeroFacturaSri() == "")
            {
                $strNumeroFacturaSri = $objInfoDocumentoFinancieroCab->getNumFactMigracion();
            }
            else
            {
                $strNumeroFacturaSri = $objInfoDocumentoFinancieroCab->getNumeroFacturaSri();
            }

            if($objInfoDocumentoFinancieroCab->getFeEmision() != null)
            {
                $strFeEmision = date_format($objInfoDocumentoFinancieroCab->getFeEmision(), "d/m/Y G:i");
            }
            else
            {
                $strFeEmision = "";
            }
            
            if($objInfoDocumentoFinancieroCab->getFeAutorizacion() != null)
            {
                $strFeAutorizacion = date_format($objInfoDocumentoFinancieroCab->getFeAutorizacion(), "d/m/Y G:i");
            }
            else
            {
                $strFeAutorizacion = "";
            }

            $arrayResultado[] = array(
                'Numerofacturasri'          => $strNumeroFacturaSri,
                'Punto'                     => $objInfoPunto->getLogin(),
                'Cliente'                   => $strNombreRazonSocialCliente,
                'Esautomatica'              => ($objInfoDocumentoFinancieroCab->getEsAutomatica()== 'S')? 'Si' : 'No',
                'Estado'                    => $objInfoDocumentoFinancieroCab->getEstadoImpresionFact(),
                'Fecreacion'                => strval(date_format($objInfoDocumentoFinancieroCab->getFeCreacion(), "d/m/Y G:i")),
                'Feemision'                 => $strFeEmision,
                'Feautorizacion'            => $strFeAutorizacion,
                'Total'                     => $objInfoDocumentoFinancieroCab->getValorTotal(),
                'linkVer'                   => $strLinkShow,
                'linkEliminar'              => '',
                'clase'                     => $strColorClaseCss,
                'boton'                     => "",
                'id'                        => $objInfoDocumentoFinancieroCab->getId(),
                'strCodigoDocumento'        => $objInfoDocumentoFinancieroCab->getTipoDocumentoId()->getCodigoTipoDocumento(),
                'intIdTipoDocumento'        => $objInfoDocumentoFinancieroCab->getTipoDocumentoId()->getId(),
                'strLinkClonar'                => $this->generateUrl('facturasproporcionales_clonar',
                                                    array('intId' => $objInfoDocumentoFinancieroCab->getId())),
                'strDebePintarBotonClonar'        => $strDebePintarBotonClonar,    
                'empresa'                   => $strPrefijoEmpresa,
                'boolMensajesCompElectronico'   => $boolUrlMensajesCompElectronico,
                'boolVerificaActualiza'         => $boolVerificaActualiza,
                'boolDocumentoPdf'              => $boolDocumentoPdf,
                'boolDocumentoXml'              => $boolDocumentoXml,
                'boolSimularCompElec'           => $boolSimularCompElec,
                'boolVerificaEnvioNotificacion' => $boolVerificaEnvioNotificacion,
                'strEsElectronica'              => ($objInfoDocumentoFinancieroCab->getEsElectronica() == 'S')? 'Si' : 'No',
                'strMsnErrorComprobante'        => $strMsnErrorComprobante,
                'negocio'                       => $strTipoNegocio
            );
            
            $intCambiaColor++;
        endforeach;

        if(!empty($arrayResultado))
        {
            $objResponse = new Response(json_encode(array('total' => $intTotalRegistros, 'documentos' => $arrayResultado)));
        }
        else
        {
            $arrayResultado[] = array(
                'Numerofacturasri'              => "",
                'Punto'                         => "",
                'Cliente'                       => "",
                'Esautomatica'                  => "",
                'Estado'                        => "",
                'Fecreacion'                    => "",
                'Feemision'                     => "",
                'Total'                         => "",
                'linkVer'                       => "",
                'linkEliminar'                  => "",
                'linkImprimir'                  => "",
                'clase'                         => "",
                'boton'                         => "display:none;",
                'id'                            => "",
                'strCodigoDocumento'            => '',
                'intIdTipoDocumento'            => '',
                'empresa'                       => "",
                'boolMensajesCompElectronico'   => false,
                'boolVerificaActualiza'         => false,
                'boolDocumentoPdf'              => false,
                'boolDocumentoXml'              => false,
                'boolVerificaEnvioNotificacion' => false,
                'boolSimularCompElec'           => false,
                'strEsElectronica'              => '',
                'strMsnErrorComprobante'        => '',
                'negocio'                       => ""
            );
            $objResponse = new Response(json_encode(array('total' => $intTotalRegistros, 'documentos' => $arrayResultado)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * El metodo estadosAction contiene un arreglo con los estados de las facturas
     * @author Alexander Samaniego
     * @version 1.1 14-10-2014
     * @since 1.0
     * @return Response Retorna un arreglo con los estados de las facturas
     */
    public function estadosAction()
    {
        $arrayEstados[] = array('idEstado' => 'Activo', 'codigo' => 'ACT', 'descripcion' => 'Activo');
        $arrayEstados[] = array('idEstado' => 'Anulado', 'codigo' => 'ANU', 'descripcion' => 'Anulado');
        $arrayEstados[] = array('idEstado' => 'Courier', 'codigo' => 'COU', 'descripcion' => 'Courier');
        $arrayEstados[] = array('idEstado' => 'Cerrado', 'codigo' => 'CER', 'descripcion' => 'Cerrado');
        $arrayEstados[] = array('idEstado' => 'Inactivo', 'codigo' => 'ACT', 'descripcion' => 'Inactivo');
        $arrayEstados[] = array('idEstado' => 'Pendiente', 'codigo' => 'PEN', 'descripcion' => 'Pendiente');
        $arrayEstados[] = array('idEstado' => 'Rechazado', 'codigo' => 'REC', 'descripcion' => 'Rechazado');
        $objResponse    = new Response(json_encode(array('estados' => $arrayEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
	
	public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|",$parametro);
        //print_r($array_valor);
        $em = $this->getDoctrine()->getManager();
        foreach($array_valor as $id):
            //echo $id;
            $entity=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
            if($entity){
                $entity->setEstadoImpresionFact("Inactivo");
                $em->persist($entity);
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
            else
                $respuesta->setContent("No existe el registro");
        endforeach;
        return $respuesta;
    }
    
    public function listarMotivosAction()
    {
		$em = $this->getDoctrine()->getManager();
		$relacionsistema_id=217;
		$resultado = $em->getRepository('schemaBundle:AdmiMotivo')->loadMotivos($relacionsistema_id);
		foreach ($resultado as $datos):
			$arreglo[]= array(
                        'id'=> $datos->getId(),
                        'descripcion'=> $datos->getNombreMotivo()
                );
                //$response = new Response(json_encode($arreglo));
                $response = new Response(json_encode(array('documentos' => $arreglo)));
		endforeach;
		 $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
	public function deleteSeleccionadasAjaxAction()
	{
		$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
		$session=$peticion->getSession();
		$user=$session->get('user');
        $idfactura = $peticion->get('idfactura');
        $motivos = $peticion->get('motivos');
        //print_r($array_valor);
        $em = $this->getDoctrine()->getManager("telconet_financiero");
		$entity=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idfactura);
		if($entity){
			$entity->setEstadoImpresionFact("Inactivo");
			$em->persist($entity);
			$em->flush();
			$entityHistorial  = new InfoDocumentoHistorial();
			$entityHistorial->setDocumentoId($entity);
			$entityHistorial->setMotivoId($motivos);
			$entityHistorial->setFeCreacion(new \DateTime('now'));
			$entityHistorial->setUsrCreacion($user);
			$entityHistorial->setEstado("Inactivo");
			$em->persist($entityHistorial);
			$em->flush();
			$response = new Response(json_encode(array('success'=>true)));
		}
		else
			$response = new Response(json_encode(array('success'=>false)));
        $response->headers->set('Content-type', 'text/json');
		return $response;
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
		$mes2 = $fechaDesdeFin[1]; 
		$dia2 = $fechaDesdeFin[2]; 

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
	
	public function tipoOrdenFactProporcionalesAction()
    {
        $request = $this->getRequest();
        $tipo=$request->request->get("tipo");
        //echo $tipo;
        
        $empresa="10";
        $estado="Activo";
        
        if($tipo=="portafolio")
        {
            $em = $this->getDoctrine()->getManager('telconet');
            $listado_planes = $em->getRepository('schemaBundle:InfoPlanCab')->findListarPlanesPorEmpresaYEstado($estado,$empresa);

            if(!$listado_planes){
                    $arreglo=array('msg'=>'No existen datos');
            }else{
                    $formulario_portafolio="";
                    $formulario_portafolio.="<option>Seleccione</option>";
                    foreach($listado_planes as $plan){
                        $formulario_portafolio.="<option value='".$plan->getId()."-".$plan->getNombrePlan()."'>".$plan->getNombrePlan()."</option>";
                    }
                    $arreglo=array('msg'=>'ok','div'=>$formulario_portafolio,'info'=>'portafolio');
            }

            $response = new Response(json_encode($arreglo));
            $response->headers->set('Content-type', 'text/json');		
            return $response;
        }
        
        if($tipo=="catalogo")
        {
            $em = $this->getDoctrine()->getManager('telconet');
            $listado_productos = $em->getRepository('schemaBundle:AdmiProducto')->findPorEmpresaYEstado($empresa,$estado);

            if(!$listado_productos){
                    $arreglo=array('msg'=>'No existen datos');
            }else{
                    $formulario_catalogo="";
                    $formulario_catalogo.="<option>Seleccione</option>";
                    foreach($listado_productos as $producto){
                        $formulario_catalogo.="<option value='".$producto->getId()."-".$producto->getDescripcionProducto()."'>".$producto->getDescripcionProducto()."</option>";
                    }
                    $arreglo=array('msg'=>'ok','div'=>$formulario_catalogo,'info'=>'catalogo');
            }

            $response = new Response(json_encode($arreglo));
            $response->headers->set('Content-type', 'text/json');		
            return $response;
        }
    }
    
    public function informacionPlanFactProporcionalesAction()
    {
        $request = $this->getRequest();
		$session=$request->getSession();
		$empresa=$session->get('idEmpresa');
		
        $plan=$request->request->get("plan");
		$fechaDesde=explode('T',$request->get("fechaDesde"));
        $fechaHasta=explode('T',$request->get("fechaHasta"));
		$cantidadDias=$this->restarFechas($fechaDesde[0],$fechaHasta[0]);
        if($cantidadDias>30)
			$cantidadDias=30;
		//Segun calendario comercial 30 dias
		$diasTotales=30;
        //$empresa="10";
        $estado="Activo";
        
        $em = $this->getDoctrine()->getManager('telconet');
        $detalle_planes = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($plan,$estado);
        
        //Obtener el descuento a nivel de cabecera que es el existente
        $cabecera_plan= $em->getRepository('schemaBundle:InfoPlanCab')->find($plan);
        
        if($detalle_planes)
        {
            $acum_total=0;
            $descuento=0;
            foreach($detalle_planes as $detalle){
                $descuento=(($detalle->getPrecioItem()*$cabecera_plan->getDescuentoPlan())/100);
                //$acum_total+=($detalle->getPrecioItem()-$descuento);
                $valor=(($detalle->getPrecioItem()* $cantidadDias)/$diasTotales);
                $acum_total+=$valor;
                $descuento=0;
            }
            $arreglo=array('msg'=>'ok','precio'=>$acum_total,'descuento'=>$cabecera_plan->getDescuentoPlan(),'tipoOrden'=>'MAN');
        }
        else
             $arreglo=array('msg'=>'No existen datos');
        
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');		
        return $response;
    }
    
    public function esBisiesto($ano) {
		if ($ano % 4 == 0)
		return true;
		/* else */
		return false;
	} 

	public function getDays($month, $year) {
		$ar[0] = 31; // Enero
		
		if($this->esBisiesto(intval($year)))
			$ar[1]=29;
		else
			$ar[1]=28;

		$ar[2] = 31; // Marzo
		$ar[3] = 30; // Abril
		$ar[4] = 31; // Mayo
		$ar[5] = 30; // Junio
		$ar[6] = 31; // Julio
		$ar[7] = 31; // Agosto
		$ar[8] = 30; // Septiembre
		$ar[9] = 31; // Octubre
		$ar[10] = 30; // Noviembre
		$ar[11] = 31; // Diciembre
		
		return $ar[intval($month)-1];
	}

    /**
    * @Secure(roles="ROLE_185-6877")
    * Funcion que clona una prefactura y cambia a estado eliminado en la prefactura padre
    *
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @since 1.0 09-02-2021
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 13-07-2021 - Se agrega parámetro intIdOficinaClonar, para presentar la oficina de la prefactura 
    *                           a clonarse en la interfaz correspondiente.  
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.2 29-04-2022 - Se agrega clonacion a facturas y prefacturas
    *
    */
    public function clonarPrefacturaAction($intId)
    {
        $objSession                             = $this->get('request')->getSession();
        $arrayParametros                        = array();
        $strUrlReferida                         = $this->get('request')->headers->get('referer');
        $strUrlIndexFacturaProporcional         = $this->generateUrl('facturasproporcionales_new');
        $strUrlFacturacionPropAutomatica        = $this->generateUrl('facturacion_proporcional_automatica_list');
        
        $objInfoDocumentoFinanacieroCab         = $this->createForm(new InfoDocumentoFinancieroCabType(), $entityInfoDocFinCab);
        $strIpCreacion                          = $this->getRequest()->getClientIp() ? $this->getRequest()->getClientIp():'127.0.0.1';

        $emFinanciero        = $this->getDoctrine()->getManager("telconet_financiero");
        $emComercial         = $this->getDoctrine()->getManager("telconet");
        
        $arrayParametros["intIdDocumento"]      = $intId;
        $arrayParametros["strTipoFacturacion"]  = "Proporcional";
        $arrayParametros["strUrlReferida"]      = $strUrlReferida;
        $arrayParametros["strUrlIndexFactura"]  = $strUrlIndexFacturaProporcional;
        $arrayParametros["strUrlFacturacionAutomatica"]  = $strUrlFacturacionPropAutomatica;
        $arrayParametros["strIpCreacion"]       = $strIpCreacion;
        $arrayParametros["boolPerfilClonacion"] = $this->get('security.context')->isGranted('ROLE_185-6877');
        $strPrefijoEmpresa                      = $objSession->get('prefijoEmpresa');
        
        $entityInfoDocFinCab        = new InfoDocumentoFinancieroCab();
        $objInfoDocumentoFinanacieroCabForm           = $this->createForm(new InfoDocumentoFinancieroCabType(), $entityInfoDocFinCab);
        $objInfoDocumentoFinanacieroCabView = $objInfoDocumentoFinanacieroCabForm->createView();

        $arrayParametros["objInfoDocumentoFinanacieroCabView"]  = $objInfoDocumentoFinanacieroCabView;

        $objInfoDocumentoFinancieroCabService   = $this->get('financiero.InfoDocumentoFinancieroCab');

        $arrayRolesPermitidos = array();
        if(true === $this->get('security.context')->isGranted('ROLE_67-4778'))
        {
            $arrayRolesPermitidos[] = 'ROLE_67-4778'; //PERFIL FACTURAR CON CUALQUIER OFICINA
        }
        //Bloque de Roles
        if(true === $this->get('security.context')->isGranted('ROLE_67-4277'))
        {
            $arrayRolesPermitidos[] = 'ROLE_67-4277'; //ELEGIR IMPUESTO IVA Mensual
        }
        if(true === $this->get('security.context')->isGranted('ROLE_69-4277'))
        {
            $arrayRolesPermitidos[] = 'ROLE_69-4277'; //ELEGIR IMPUESTO IVA Proporcional
        }   
        if(true === $this->get('security.context')->isGranted('ROLE_69-4297'))
        {
            $arrayRolesPermitidos[] = 'ROLE_69-4297'; //ELEGIR IMPUESTO ICE
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-4777'))
        {
            $arrayRolesPermitidos[] = 'ROLE_67-4777'; //PUEDE COMPENSAR
        }
        if(true === $this->get('security.context')->isGranted('ROLE_69-5357'))
        {
            $arrayRolesPermitidos[] = 'ROLE_69-5357'; //PUEDE EDITAR PRECIO EN DETALLE DE FACTURA
        }
        $arrayParametros["arrayRolesPermitidos"] = $arrayRolesPermitidos;

        $entityFacturaCab = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intId);
        $emComercial->getRepository('schemaBundle:InfoPunto')->setSessionByIdPunto($entityFacturaCab->getPuntoId(), $objSession);
        $arrayParametros["ptoCliente"]              = $objSession->get('ptoCliente');
        $arrayParametros["idOficina"]               = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;
        $arrayParametros["strFacturaElectronico"]   = $objSession->get('strFacturaElectronico');
        $arrayParametros["strNombrePais"]           = $objSession->get('strNombrePais');

        $arrayParametros["idEmpresa"]               = $objSession->get("idEmpresa");
        $arrayParametros["prefijoEmpresa"]          = $objSession->get('prefijoEmpresa');
        $arrayParametros["user"]                    = $objSession->get('user');
        $arrayParametros["oficina"]                 = $objSession->get('oficina');

        $arrayDatosService = $objInfoDocumentoFinancieroCabService->clonarFactura($arrayParametros);
        
        if($arrayDatosService["error"] == 0)
        {
            $arrayParametros = $arrayDatosService["arrayParametros"];

            $arrayParametros['form'] = $objInfoDocumentoFinanacieroCabView;
            $arrayParametros['strDescripcionDetCaract'] = $arrayDatosService["strDescripcionDetCaract"];
            $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
            $arrayParametros["arrayAlertasClonacion"] = $arrayDatosService["arrayAlertasClonacion"];
            $arrayParametros['strNecesitaEliminarPrefactura'] = $arrayDatosService["strNecesitaEliminarPrefactura"];
            $arrayParametros["strClonarFactura"] = "S";
            return $this->render("financieroBundle:FacturasProporcional:new.html.twig", $arrayParametros);
        }
        else if ($arrayDatosService["error"] == 2)
        {
            if(!is_null($arrayDatosService["mensaje_error"]))
            {
                foreach($arrayDatosService["mensaje_error"] as $strMensajeError)
                {
                    $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
                }
            }
            return $this->redirect($this->generateUrl($arrayDatosService["redireccion_url"], $arrayDatosService["arrayParametros"]));
        }
        
        //Si hubo algun error en extraccion de datos
        else
        {
            $objServiceUtil  = $this->get('schema.Util');
            $strMensajeError = $arrayDatosService["mensaje_error"];
            $objServiceUtil->insertError( 'Telcos+', 
                                    'Clonacion Factura', 
                                    'Error al clonar la factura. '.$strMensajeError, 
                                    $strUsuario, 
                                    $strIpCreacion );
            $this->get('session')->getFlashBag()->add('notice', $strMensajeError);
            return $this->redirect($this->generateUrl("facturasproporcionales_new"));
        }
    }
}
