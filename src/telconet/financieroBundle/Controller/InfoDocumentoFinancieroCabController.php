<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoPlanCab;
use telconet\schemaBundle\Form\InfoDocumentoFinancieroCabType;
use telconet\schemaBundle\Form\InfoDocumentoFacturaCabType;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroImp;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\financieroBundle\Controller\AnticipoController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\financieroBundle\Service\InfoDetalleDocumentoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoDocumentoCaracteristica;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;


/**
 * InfoDocumentoFinancieroCab controller.
 *
 */
class InfoDocumentoFinancieroCabController extends Controller
{
    const ESTADO_ACTIVO = 'Activo';

    /**
     * Muestra los objetos de facturas
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 15-10-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 21-01-2015
     * @since 1.1
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.3 19-10-2017 Se agrega el rol reajustarImpuestos.
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
        //MODULO 67 - FINANCIERO/FACTURAS
        if(true === $this->get('security.context')->isGranted('ROLE_67-1777'))
        {
            $arrayRolesPermitidos[] = 'ROLE_67-1777'; //ENVIO NOTIFICACION COMPROBANTE
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-1778'))
        {
            $arrayRolesPermitidos[] = 'ROLE_67-1778'; //ACTUALIZA COMPROBANTE ELECTRONICO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-1837'))
        {
            $arrayRolesPermitidos[] = 'ROLE_67-1837'; //DESCARGA COMPROBANTE ELECTRONICO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-4897'))
        {
            $arrayRolesPermitidos[] = 'ROLE_67-4897'; //ANULA COMPROBANTE ELECTRONICO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-5517'))
        {
            $arrayRolesPermitidos[] = 'ROLE_67-5517'; //REAJUSTAR IMPUESTOS EN FACTURAS
        }

        return $this->render('financieroBundle:InfoDocumentoFinancieroCab:index.html.twig', array('rolesPermitidos' => $arrayRolesPermitidos,
                                                                                                  'intIdPunto'      => $intIdPunto));
    }    

    /**
     * Muestra la informacion de la factura
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 24-11-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 05-02-2015
     * @since 1.1
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 11-03-2015
     * @since 1.2
     * @author Gina Villalba <gvillalba@telconet.ec>
     * Se agrega el manejo de los ROLES a nivel del twig
     * @version 1.4 24-08-2015
     * @since 1.3
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 - Se agrega el rango de consumo al momento de presentar la factura creada
     * @since 18-06-2016
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.6 09-08-2016 
     * Actualización: Se pone en sesion el login al que pertenece la factura seleccionada
     * @since 1.5
     * 
     * @author Ricardo Coello Quezada. <rcoello@telconet.ec>
     * @version 1.6 04-10-2016
     * Actualización: Se realiza la separacion de impuestos por documento dependiendo del tipo IVA - ICE.
     * @since 1.5
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.7 25-11-2016
     * Actualización: Se agrega variable que será utilizada para visualizar las caracteristicas del documento .
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 28-06-2017 - Se modifica para que consulte los impuestos correspondientes al país del usuario en sessión.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.9 - Se presenta la descripción del ciclo para el parámetro CICLO_FACTURACION
     * @since 22-02-2018
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 2.0 28-02-2019 - Se modifica impuestos para que tome la configuración de Guatemala.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 2.1 27-01-2020 - Se crea el perfil "No Validar 12M De Factura" del módulo "nota_de_credito" y 
     *                           la acción "noValidar12MesesFactura" para permitir generar Nota de crédito.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 2.2 28-10-2020 - Se realizan cambios para visualizar el detalle de las características asociadas al documento.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 2.3 27-01-2021 - Se realizan cambios para modificar los detalles de la factura.
     * Se ordena el historial del documento por id
     * 
     */
    public function showAction($id)
    {
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $strIdEmpresa               = $objRequest->getSession()->get('idEmpresa');
        $strPrefijoEmpresa          = $objRequest->getSession()->get('prefijoEmpresa');
        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');
        $em                         = $this->getDoctrine()->getManager("telconet_financiero");
        $em_comercial               = $this->getDoctrine()->getManager("telconet");
        $em_general                 = $this->getDoctrine()->getManager("telconet_general");
        $floatTotalIva              = 0.00;
        $floatTotalIce              = 0.00;
        $strPaisSession             = $objSession->get('strNombrePais');
        
        $entityInfoDocumentoFinancieroCab   = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        $arrayParametrosSend = array('intIdDocumento'               => $id,
                                     'arrayEstadoTipoDocumentos'    => array('Activo'),
                                     'arrayCodigoTipoDocumento'     => array('NC'),
                                     'arrayEstadoNC'                => array('Activo'));
        //Obtiene el valor de la factura y la sumatoria de las notas de credito relacionadas a la factura
        $arrayGetValorTotalNcByFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->getValorTotalNcByFactura($arrayParametrosSend);

        if(!$entityInfoDocumentoFinancieroCab)
        {
            throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
        }

        $entityHistorial = $em->getRepository('schemaBundle:InfoDocumentoHistorial')
                              ->findBy(array('documentoId' => $entityInfoDocumentoFinancieroCab->getId()),
                                       array('feCreacion' => 'asc', 'id' => 'asc'))
                                    ;
        $arrayHistorial = array();
        if($entityHistorial)
        {
            $intIndice = 0;
            foreach($entityHistorial as $objHistorial)
            {

                if($objHistorial->getMotivoId() != null)
                {
                    $entityMotivo = $em_general->getRepository('schemaBundle:AdmiMotivo')->find($objHistorial->getMotivoId());

                    if($entityMotivo)
                    {
                        $strNombreMotivo = $entityMotivo->getNombreMotivo();
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

        $objDeleteForm = $this->createDeleteForm($id);

        $entityInfoPunto                = $em_comercial->getRepository('schemaBundle:InfoPunto')
                                                       ->find($entityInfoDocumentoFinancieroCab->getPuntoId());
        $entityInfoPersonaEmpresaRol    = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->find($entityInfoPunto->getPersonaEmpresaRolId()->getId());
        $entityOficina                  = $em->getRepository('schemaBundle:InfoOficinaGrupo')
                                             ->find($entityInfoDocumentoFinancieroCab->getOficinaId());

        $arrayInformacionPersona['puntoId'] = $entityInfoPunto->getLogin();
        if($entityInfoPersonaEmpresaRol->getPersonaId()->getNombres() != "" && $entityInfoPersonaEmpresaRol->getPersonaId()->getApellidos() != "")
        {
            $arrayInformacionPersona['cliente'] = $entityInfoPersonaEmpresaRol->getPersonaId()->getNombres() . " " . 
                                                  $entityInfoPersonaEmpresaRol->getPersonaId()->getApellidos();
        }
        if($entityInfoPersonaEmpresaRol->getPersonaId()->getRepresentanteLegal() != "")
        {
            $arrayInformacionPersona['cliente'] = $entityInfoPersonaEmpresaRol->getPersonaId()->getRepresentanteLegal();
        }
        if($entityInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial() != "")
        {
            $arrayInformacionPersona['cliente'] = $entityInfoPersonaEmpresaRol->getPersonaId()->getRazonSocial();
        }
        
        if($entityInfoDocumentoFinancieroCab->getMesConsumo() != "")
        {
            $intMes                                     = intval($entityInfoDocumentoFinancieroCab->getMesConsumo());
            $strMesConsumo                              = $serviceInfoCompElectronico->obtieneNombreMes($intMes);
            $arrayInformacionPersona['strFeConsumo']    = $strMesConsumo.' '.$entityInfoDocumentoFinancieroCab->getAnioConsumo();
        }
        elseif( $entityInfoDocumentoFinancieroCab->getRangoConsumo() )
        {
            $arrayInformacionPersona['strFeConsumo'] = $entityInfoDocumentoFinancieroCab->getRangoConsumo();
        }
        else
        {
            $intMes                                     = $entityInfoDocumentoFinancieroCab->getFeEmision()->format("n");
            $strMesConsumo                              = $serviceInfoCompElectronico->obtieneNombreMes($intMes);
            $arrayInformacionPersona['strFeConsumo']    = $strMesConsumo.' '.$entityInfoDocumentoFinancieroCab->getFeEmision()->format("Y");
        }
        
        //Informacion de paga iva: si | no
        $arrayInformacionPersona['strPagaIva']  = $entityInfoPersonaEmpresaRol->getPersonaId()->getPagaIva();

        //Obtiene el valor total de impuestos de la factura por IVA
        if ( strtoupper($strPaisSession) == 'ECUADOR')
        {
            $strTipoImpuestoIva = 'IVA';
            $strTipoImpuestoIce = 'ICE';
        }
        else if ( strtoupper($strPaisSession) == 'GUATEMALA' )
        {
            $strTipoImpuestoIva = 'IVA_GT';
            $strTipoImpuestoIce = '';
        }
        else
        {
            $strTipoImpuestoIva = 'ITBMS';
            $strTipoImpuestoIce = 'IEC';
        }
        
        $arrayTotalIva = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                            ->getValorImpuesto($entityInfoDocumentoFinancieroCab->getId(), $strTipoImpuestoIva);
        if (!empty($arrayTotalIva)) {

            $floatTotalIva   = $arrayTotalIva[0]['totalImpuesto'];

        }

        $arrayInformacionPersona['floatTotalIva'] = round( $floatTotalIva  , 2);
 
        //Obtiene el valor total de impuestos de la factura por ICE
        if  (!empty($strTipoImpuestoIce)) 
        {
             $arrayTotalIce=  $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                   ->getValorImpuesto($entityInfoDocumentoFinancieroCab->getId(), $strTipoImpuestoIce);
        }
       
        if (!empty($arrayTotalIce)) {

            $floatTotalIce   = $arrayTotalIce[0]['totalImpuesto'];

        }

        $arrayInformacionPersona['floatTotalIce'] = round( $floatTotalIce , 2);

        $floatTotalOtrosImpuestos = floatval($entityInfoDocumentoFinancieroCab->getSubtotalConImpuesto());
        
        if( $floatTotalOtrosImpuestos > 0 )
        {
            $floatTotalOtrosImpuestos = $floatTotalOtrosImpuestos - $arrayInformacionPersona['floatTotalIce']
                                        - $arrayInformacionPersona['floatTotalIva'];
        }//( $floatTotalOtrosImpuestos > 0 )
        
        //No se maneja otros impuestos, enviamos cero por el momento.
        $arrayInformacionPersona['intOtrosImp'] = $floatTotalOtrosImpuestos;

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
        
        /*
         * Bloque valida fecha de creación de la factura contra la fecha actual para verificar si se puede crear N/C
         */
        $datetimeActual            = new \DateTime('now');
        $datetimeActual->sub(new \DateInterval('P12M'));
        $datetimeFeCreacionFactura = $entityInfoDocumentoFinancieroCab->getFeCreacion();
        $boolCrearNotaCredito      = false;
        
        //MODULO 70 - nota_de_credito/noValidar12MesesFactura
        $booleanNotaCreditoXRol = $this->get('security.context')->isGranted('ROLE_70-7077');
        
        if( $strPrefijoEmpresa == 'TN' )
        {
            if( $datetimeActual < $datetimeFeCreacionFactura )
            {
                $boolCrearNotaCredito = true;
            }
            //MODULO 70 - nota_de_credito/noValidar12MesesFactura
            if( $booleanNotaCreditoXRol )
            {
                $boolCrearNotaCredito = true;
            }
        }
        else
        {
            $boolCrearNotaCredito = true;
        }
        /*
         * Fin Bloque valida fecha de creación de la factura contra la fecha actual para verificar si se puede crear N/C
         */
        //Pone en sesion el login al que pertenece la factura seleccionada
        $serviceInfoPunto = $this->get('comercial.InfoPunto');
        $serviceInfoPunto->ponerLoginEnSesion($objSession,$entityInfoPunto->getId(),$strIdEmpresa);
        
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
                                                                           
                        if($objAdmiCaracteristica->getDetalleCaracteristica() != null )
                        {
                            $arrayCaracteristica['strDescripcion'] = $objAdmiCaracteristica->getDetalleCaracteristica();
                        }
                        else
                        {
                            $arrayCaracteristica['strDescripcion'] = $objAdmiCaracteristica->getDescripcionCaracteristica();
                        } 
                        
                        if('CICLO_FACTURACION' == $objAdmiCaracteristica->getDescripcionCaracteristica())
                        {
                            $arrayCaracteristica['strValor'] = $em->getRepository('schemaBundle:AdmiCiclo')
                                    ->find($objInfoDocumentoCaracteristica->getValor())->getNombreCiclo();
                        }
                        else
                        {
                            $arrayCaracteristica['strValor'] = $objInfoDocumentoCaracteristica->getValor();
                        }

                        $arrayCaracteristicas[] = $arrayCaracteristica;
                    }
                }
            endforeach;            
        }
        //Bloque para modificar descripcion de la factura
        $strRol = "ROLE_67-7877";
        $boolModificarDescripcionFactura = (
                                        (true === $this->get('security.context')->isGranted($strRol)) &&
                                        $entityInfoDocumentoFinancieroCab->getEstadoImpresionFact() == "Pendiente" &&
                                        ($entityInfoDocumentoFinancieroCab->getNumeroFacturaSri() == null  || 
                                         $entityInfoDocumentoFinancieroCab->getNumeroFacturaSri()=="" ) &&
                                        ($entityInfoDocumentoFinancieroCab->getFeAutorizacion() == null || 
                                         $entityInfoDocumentoFinancieroCab->getFeAutorizacion() =="") );
        return $this->render('financieroBundle:InfoDocumentoFinancieroCab:show.html.twig',
                             array(
                                    'entity'                    => $entityInfoDocumentoFinancieroCab,
                                    'delete_form'               => $objDeleteForm->createView(),
                                    'info_cliente'              => $arrayInformacionPersona,
                                    'oficina'                   => $entityOficina,
                                    'historial'                 => $arrayHistorial,
                                    'intSaldo'                  => round($intSaldo, 2),
                                    'prefijoEmpresa'            => $strPrefijoEmpresa,
                                    'boolCrearNotaCredito'      => $boolCrearNotaCredito,
                                    'arrayCaracteristicas'      => $arrayCaracteristicas,
                                    'boolModificarDescripcion'  => $boolModificarDescripcionFactura
                                    
                                   )
                            );
    }//showAction

    
    /**
     * @Secure(roles="ROLE_67-2")
     * 
     * newAction, Crea formulario para la entidad info_documento_financiero_cab
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 15-10-2014
     * @since 1.0
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 02-12-2015 - Se modifica para que obtenga la numeración de las facturas dependiendo de la oficina del usuario en sessión
     * @since 1.1
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 20-06-2016 - Se envía el permiso para ver el combo de IMPUESTO IVA y el checkbox del ICE al crear la factura.
     * @since 1.2
     * 
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.4 12-07-2016 - Se agrega parametro que indica si pide o no el campo observacion
     * @since 1.3
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 28-09-2016 - Para TN se valida si el punto padre de facturación debe ser compensado y si el usuario tiene el perfil adecuado para
     *                           realizar dicha acción. Adicional se verifica si tiene el perfil para poder facturar cualquier cliente sin importar
     *                           la oficina de facturación a la que pertenece el cliente.
     *
     * Se agrega validación de rol, y de contacto de facturación para TN
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 1.6 19-12-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 09-02-2017 - Se elimina la validación por empresa de la compensación solidaria para que tanto MD y TN compensen al 2% las
     *                           facturas realizadas al 14%, y que los clientes pertenezcan a los cantones MANTA y PORTOVIEJO.
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.8 20-06-2017 - Se agrega definición de rol para editar precio en detalles de facturas
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.9 28-06-2017 - Se modifica la función para que verifique el impuesto del IVA en estado 'Activo' por país.
     *                           Se valida si la empresa factura de forma electrónica con el parámetro en sessión 'strFacturaElectronico'
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 2.0 28-02-2019 - Se modifica impuestos para que tome la configuración de Guatemala.
     */ 
    public function newAction()
    { 
        $entityInfoDocFinCab     = new InfoDocumentoFinancieroCab();
        $formInfoDocFinCab       = $this->createForm(new InfoDocumentoFinancieroCabType(), $entityInfoDocFinCab);
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $arrayCliente            = $objSession->get('cliente');
        $arrayPtoCliente         = $objSession->get('ptoCliente');
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $strEmpresaCod           = $objSession->get('idEmpresa');
        $emComercial             = $this->getDoctrine()->getManager();
        $intIdOficina            = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;
        $strNombreOficina        = $objSession->get('oficina');
        $emGeneral               = $this->getDoctrine()->getManager("telconet_general");
        $emFinanciero            = $this->getDoctrine()->getManager("telconet_financiero");

        $boolPuedeFacturar       = true;
        $boolDisableComboOficina = false;
        $strUsrSession           = $objSession->get('user');
        $strFacturaElectronica   = $objSession->get('strFacturaElectronico');
        $intIdPaisSession        = $objSession->get('intIdPais');
        $strPaisSession          = $objSession->get('strNombrePais');
        $strIpSession            = $objRequest->getClientIp();
        
        $serviceInfoDocumentoFinancieroCab = $this->get('financiero.InfoDocumentoFinancieroCab');

        $arrayParametros = array( 'entity'                      => $entityInfoDocFinCab, 
                                  'form'                        => $formInfoDocFinCab->createView(), 
                                  'strPagaIva'                  => '',
                                  'nombre_tipo_negocio'         => '',
                                  'nombre_tipo_negocio_no'      => '',
                                  'esCompensado'                => 'N',
                                  'floatPorcentajeCompensacion' => 0 );
        
        
        if(true === $this->get('security.context')->isGranted('ROLE_67-4277'))
        {
            $rolesPermitidos[] = 'ROLE_67-4277'; //ELEGIR IMPUESTO IVA
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_67-4297'))
        {
            $rolesPermitidos[] = 'ROLE_67-4297'; //ELEGIR IMPUESTO ICE
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_67-4777'))
        {
            $rolesPermitidos[] = 'ROLE_67-4777'; //PUEDE COMPENSAR
        }
        
        if(true === $this->get('security.context')->isGranted('ROLE_67-5357'))
        {
            $rolesPermitidos[] = 'ROLE_67-5357'; //PUEDE EDITAR PRECIO EN DETALLE DE FACTURA
        }        
        
        $arrayParametros['rolesPermitidos'] = $rolesPermitidos;

        //valida que un login este en sesion
        if($arrayPtoCliente)
        {
            //Como el punto cliente existe se debe verificar si es pto de facturacion
            $entityPuntoAdicional = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($arrayPtoCliente['id']);

            //verifica que la entidad tenga datos
            if($entityPuntoAdicional)
            {
                //valida que sea el punto sea padre de facturacion
                if($entityPuntoAdicional->getEsPadreFacturacion() == 'S')
                {
                    $arrayParametros['punto_id']          = $arrayPtoCliente;
                    $arrayParametros['cliente']           = $arrayCliente;
                    $strCodigoNumeracion                  = "FAC";
                    $arrayParametros['esElectronica']     = "No";
                    $arrayParametros['numero_de_factura'] = "";

                    //Obtenemos el numero de factura sigt a dar, solo informativo
                    if( $strFacturaElectronica == "S" )
                    {
                        $strCodigoNumeracion              = "FACE";
                        $arrayParametros['esElectronica'] = "Si";
                    }//( $strFacturaElectronica == "S" )
                    
                    $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                    //Se obtiene informacion del cliente si paga iva o no
                    $entitypersona          = $emComercial->getRepository('schemaBundle:InfoPersona')->find($arrayCliente["id_persona"]);
                    $strPagaIva             = $entitypersona->getPagaIva();
                    
                    $arrayParametros['strPagaIva']        = $strPagaIva;
                    $arrayParametros['intIdOficina']      = $intIdOficina;
                    $arrayParametros['strNombreOficina']  = $strNombreOficina;
                    
                    /**
                     * Bloque que verifica si el cliente en sessión pertenece a la oficina del usuario en sessión, caso contrario verifica si el
                     * usuario tiene el perfil adecuado para facturar con cualquier oficina
                     */              
                    $intIdOficinaClienteSession = ( !empty($arrayCliente['id_oficina']) ) ? $arrayCliente['id_oficina'] : 0;

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
                    }//if( $strPrefijoEmpresa == 'TN' )

                    $arrayParametrosService                           = array();
                    $arrayParametrosService['strUsrSession']          = $strUsrSession;
                    $arrayParametrosService['strIpSession']           = $strIpSession;
                    $arrayParametrosService['intIdPersonaEmpresaRol'] = ( !empty($arrayCliente['id_persona_empresa_rol']) ) 
                                                                        ? $arrayCliente['id_persona_empresa_rol'] : 0;
                    $arrayParametrosService['intIdOficina']           = $intIdOficinaClienteSession;
                    $arrayParametrosService['strEmpresaCod']          = $strEmpresaCod;
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
                }//($entityPuntoAdicional->getEsPadreFacturacion() == 'S')

                $tipo_negocio           = $emComercial->getRepository('schemaBundle:AdmiTipoNegocio')->find($arrayPtoCliente['id_tipo_negocio']);
                if (strtoupper(trim($tipo_negocio->getNombreTipoNegocio())) == 'HOME' )
                {
                    $nombre_tipo_negocio    = $tipo_negocio->getNombreTipoNegocio();
                    $nombre_tipo_negocio_no = 'PYME';
                }
                elseif( strtoupper(trim($tipo_negocio->getNombreTipoNegocio())) == 'PYME' )
                {
                    $nombre_tipo_negocio    = $tipo_negocio->getNombreTipoNegocio();
                    $nombre_tipo_negocio_no = 'HOME';
                }
                elseif( strtoupper(trim($tipo_negocio->getNombreTipoNegocio())) == 'ISP' )
                {
                    $nombre_tipo_negocio    = $tipo_negocio->getNombreTipoNegocio();
                    $nombre_tipo_negocio_no = 'UM';
                }
                else
                {
                    $nombre_tipo_negocio    = $tipo_negocio->getNombreTipoNegocio();
                    $nombre_tipo_negocio_no = '';
                }
                
                $arrayParametros['nombre_tipo_negocio']    = $nombre_tipo_negocio;
                $arrayParametros['nombre_tipo_negocio_no'] = $nombre_tipo_negocio_no;
            }
        }//($arrayPtoCliente)
        
        //CONSULTA EN LA TABLA PARAMETROS SI SE MUESTRA CAMPO OBSERVACION SEGUN EMPRESA
        $arrayParametroDet= $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->getOne("OBSERVACION_FACTURA_MANUAL", "FINANCIERO", "", "", "", "", "", "","",$strEmpresaCod);

        $arrayParametros['strMuestraObservacion']='S';
        if ($arrayParametroDet["valor2"])
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
        else if ( strtoupper($strPaisSession) ==  "GUATEMALA")
        {
            $objAdmiPais = $emGeneral->getRepository("schemaBundle:AdmiPais")->findOneById($intIdPaisSession);
            
            if ( is_object($objAdmiPais) )
            {
                $objAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                ->findOneBy( array('tipoImpuesto' => 'IVA_GT', 
                                                                   'estado'        => 'Activo',
                                                                   'paisId'        => $objAdmiPais) );
            }// ( is_object($objAdmiPais) )
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
                                                    'FAC', 
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
        $arrayParametros["boolPuedeFacturar"]                 = $boolPuedeFacturar;
        $arrayParametros["booleanPresentarMensajeValidacion"] = false;
        $arrayParametros["strEmpresaCod"]                     = $strEmpresaCod;
        if($boolPuedeFacturar)
        {
            $arrayParametros = $emFinanciero->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                            ->validaContactoFacturacion($arrayParametros);
        }
       
        return $this->render('financieroBundle:InfoDocumentoFinancieroCab:new.html.twig', $arrayParametros);
    }


    /**
     * @Secure(roles="ROLE_67-3")
     * 
     * createAction, Crea una nueva entidad de info_documento_financiero_cab
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 15-10-2014
     * @since 1.0
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 13-11-2014
     * @since 1.1
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 24-11-2014
     * @since 1.2
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 02-12-2015 - Se modifica para que obtenga la numeración de las facturas dependiendo de la oficina del usuario en sessión
     * @since 1.3
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 05-04-2016 - Se modifica para verificar los meses restantes del servicio y resetearlos al valor de la frecuencia, para que dichos
     *                           servicios no ingresen al proceso de facturación masiva.
     * @since 1.4
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 24-05-2019 - Se activa la opción de 'Precargada Agrupada'
     * 
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.7 21-06-2016 - Se modifica el tema de la descripcion de los items por empresa
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 - Se adapta la función para obtener los valores de los productos y/o planes dependiendo del porcentaje del IVA elegido por el 
     *                usuario, en caso de no elegir ningún impuesto se calculará todo al impuesto del IVA en estado 'Activo'.
     *                Adicional se verifica si el usuario seleccionó que la factura aplique el ICE.
     * @since 21-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.9 22-06-2016 - Se redondea los impuestos por cada producto
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.0 23-06-2016 - Se quita el redondeo en los impuestos porque no se debe realizar dicho proceso por cada producto.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.1 23-06-2016 - Se realiza cálculo de impuesto ICE
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.2 08-07-2016 - Se corrige que permita guardar valores mayores a 0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.3 18-07-2016 - Se corrige que a clientes nuevos (clientes sin facturas) les permita guardar el impuesto seleccionado.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.4 15-08-2016 - Se cambia opción de rango de consumo, se permite ahora seleccionar por dos tipos que son: 'Consumo por rango de días'
     *                           y 'Consumo por rango de meses', para poder tener fechas válidas con las cuales se realizará el respectivo conteo de 
     *                           meses restantes usado en la facturación masiva.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.5 22-09-2016 - Para TN al elegir el rango de consumo por meses se cambia a que hora guarde en el campo 'RANGO_CONSUMO' de la tabla
     *                           'DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB' un texto que indica el Mes y Año de consumo seleccionado por el 
     *                           usuario sin incluir el día.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.6 27-09-2016 - Para TN se valida si el punto padre de facturación debe ser compensado, y se saca el valor correspondiente de
     *                           compensación solidaria que será guardado a nivel de cabecera en la factura. Es decir, en la tabla 
     *                           DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB en el campo DESCUENTO_COMPENSACION.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.7 02-12-2016 - Se elimina la operación de multiplicar el valor compensado del grid por la cantidad del producto y/o plan puesto que
     *                           el valor obtenido desde el grid es el valor total.
     *
     * Se agrega validación de rol, y de contacto de facturación para TN
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 2.8 19-12-2016
     *
     * Se llama a otra función para limpiar los caracteres especiales de la descripcion de las facturas.
     * @author Hector Ortega <haortega@telconet.ec>
     * @version 2.9 22-12-2016 
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 3.0 24-01-2017 - Se realiza una validación adicional cuando el documento a crear tiene ICE, la cual consiste en validar si la suma
     *                           total de los impuestos del documento tal como están guardados en base restados de la suma total de los impuestos
     *                           redondeados genera una diferencia mayor a 0.005 pero menor a 0.01. Si fuese el caso se debe sumar la diferencia
     *                           obtenida al subtotal con impuestos y modificar el valor total de la factura.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 3.1 25-01-2017 - Se multiplica la compensación solidaria por la cantidad del producto y/o plan enviado para crear el documento. 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 3.2 09-02-2017 - Se realiza la modificación para que la empresa MD compense sus planes al 2% de las facturas realizadas al 14%.
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 3.3 04-04-2017 - Se realiza modificación para el ingreso de un nuevo detalle a la factura por cargo de reproceso de débito. 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 3.4 23-05-2017 - Se agrega validación para verificar si existe algún detalle de la factura por cargo de reproceso de débito.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 3.5 28-06-2017 - Se modifica la función para adaptar la obtención y cálculo de los impuestos por país, tanto para planes como para
     *                           productos.
     *                           Se unifican las funciones de crear el documento de Facturas (FAC) y las Facturas Proporcionales (FACP)
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 3.6 11-12-2017 - Se agrega seteo de campo ID_SERVICIO en creación de detalle de la factura.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 3.7 28-02-2019 - Se modifica impuestos para que tome la configuración de Guatemala.
     * 
     * @author Gustavo Nare <gnarea@telconet.ec>
     * @version 3.8 09-02-2021 - Si la facturea es creada por clonacion se pasa a estado eliminada la factura padre,
     *                           se crea un historial de la nueva factura, se inserta la observacion en la cabecera de la factura,
     *                           se copian los campos EsAutomatica,Prorrateo,Reactivacion,Recurrente,Comisiona,EntregoRetencionFte
     *                           caracteristicas de facturacion de mes y anio de factura padre
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 3.9 28-04-2022 - Se crea clonacion a facturas.
     *
     * @author Bryan Fonseca <bfonseca@telconet.ec>
     * @version 4.0 14-04-2023 - Se agrega validación para evitar facturas duplicadas. Se comprueba si se está tratando de crear
     *                           una factura para el mismo punto con el mismo mes de consumo (o rango de consumo), con mismos servicios
     *                           y dichos servicios tienen el mismo valor. Solo aplica para facturación manual normal de TN.
     * 
     */
    public function createAction(Request $objRequest)
    {
        setlocale(LC_TIME, "es_ES.UTF-8");

        $emComercial                = $this->getDoctrine()->getManager();
        $emFinanciero               = $this->getDoctrine()->getManager("telconet_financiero");
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $entityInfoDocumentoFinCab  = new InfoDocumentoFinancieroCab();
        $formInfoDocFinCab          = $this->createForm(new InfoDocumentoFinancieroCabType(), $entityInfoDocumentoFinCab);
        $formInfoDocFinCab->bind($objRequest);

        $objSession                 = $objRequest->getSession();
        $objInformacionGrid         = $objRequest->get('listado_informacion');
        $objDatosInformacionGrid    = json_decode($objInformacionGrid);
        $strAnioConsumo             = $objRequest->get('idTxtAnio');
        $strMesConsumo              = $objRequest->get('txtMes');
        $intIdImpuesto              = $objRequest->get('intTxtIdImpuesto');
        $strPagaIce                 = $objRequest->get('strPagaIce');
        $intIdOficina               = $objRequest->get('intIdOficina') ? $objRequest->get('intIdOficina') : $objSession->get('idOficina');
        $intIdNumeracion            = $objRequest->get('intIdNumeracion') ? $objRequest->get('intIdNumeracion') : 0;
        $strCliente                 = $objSession->get('cliente');
        $strOpcFeConsumoSelected    = $objRequest->get('opcFeConsumoSelected') ? $objRequest->get('opcFeConsumoSelected') : "";
        $strObservacion             = $objRequest->get('observacion');
        $strOpcRangoConsumoSelected = $objRequest->get('opcRangoConsumoSelected') ? $objRequest->get('opcRangoConsumoSelected') : "";
        $strDateDiaInicio           = $objRequest->get('dateDiaInicio') ? $objRequest->get('dateDiaInicio') : "";
        $strDateDiaFin              = $objRequest->get('dateDiaFin') ? $objRequest->get('dateDiaFin') : "";
        $strDateMesInicio           = $objRequest->get('txtFechaMesInicio') ? $objRequest->get('txtFechaMesInicio') : "";
        $strDateMesFin              = $objRequest->get('txtFechaMesFin') ? $objRequest->get('txtFechaMesFin') : "";
        $strFechaInicio             = $objRequest->get('feDesdeFacturaE') ? $objRequest->get('feDesdeFacturaE') : "";
        $strFechaFin                = $objRequest->get('feHastaFacturaE') ? $objRequest->get('feHastaFacturaE') : "";
        $datetimeFechaInicio        = $strFechaInicio ? \DateTime::createFromFormat("Y-m-d", $strFechaInicio) : null;
        $datetimeFechaFin           = $strFechaFin ? \DateTime::createFromFormat("Y-m-d", $strFechaFin) : null;
        $strTipoFacturacion         = $objRequest->get('strTipoFacturacion') ? $objRequest->get('strTipoFacturacion') : 'normal';
        $strObservacionRangoConsumo = "De ";
        $dateFechaInicial           = null;
        $dateFechaFinal             = null;
        $strIpCreacion              = $objRequest->getClientIp();
        $serviceUtil                = $this->get('schema.Util');
        $boolDocumentoTieneIce      = false;
        $boolTieneReprocesoDebito   = false;
        $strCodigoFacturas          = ( $strTipoFacturacion == 'proporcional' ) ? 'FACP' : 'FAC';
        $strUrlRedirectShow         = ( $strTipoFacturacion == 'proporcional' ) ? 'facturasproporcionales_show' : 'infodocumentofinancierocab_show';
        $strUrlRedirectNew          = ( $strTipoFacturacion == 'proporcional' ) ? 'facturasproporcionales_new' : 'infodocumentofinancierocab_new';

        //valida si el mes es mayor a dos digitos, para obtener el numero del mes actual
        if(strlen($strMesConsumo) > 2 )
        {
            $strMesConsumo = date("n");  
        }
    
        //informacion del pto cliente
        $arrayPtoCliente       = $objSession->get('ptoCliente');
        $intCodEmpresa         = $objSession->get('idEmpresa');
        $strUsuario            = $objSession->get('user');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdPunto            = $arrayPtoCliente['id'];
        $strEstado             = "Pendiente";
        $strEsElectronica      = "S";
        $strFacturaElectronica = $objSession->get('strFacturaElectronico');
        $intIdPaisSession      = $objSession->get('intIdPais');
        $strPaisSession        = $objSession->get('strNombrePais');

        $strClonarFactura = $objRequest->get('strClonarFactura');
        $strNecesitaEliminarPrefactura = $objRequest->get('strNecesitaEliminarPrefactura');
        $strDescripcionDetCaractClonar = $objRequest->get('strDescripcionDetCaract');
        $objInfoDocumentoFinancieroCabService  = $this->get('financiero.InfoDocumentoFinancieroCab');

        // Se valida la factura para asegurar que subsecuentes peticiones duplicadas a este endpoint no generen más de una factura.
        if($strPrefijoEmpresa == 'TN' && $strCodigoFacturas != 'FACP')
        {
            $arrayFechas = array(
                'fechaInicio' => date('d-m-Y', strtotime($strOpcRangoConsumoSelected == 'consumoDias' ? $strDateDiaInicio : $strDateMesInicio)),
                'fechaFin' => date('d-m-Y', strtotime($strOpcRangoConsumoSelected == 'consumoDias' ? $strDateDiaFin : $strDateMesFin)),
            );

            $boolEsFacturaDuplicada = false;
            // Esto curiosamente es un arreglo, no un objeto como indica su prefijo
            if (is_array($objDatosInformacionGrid))
            {
                $arrayParametrosDuplicacion = array(
                    'intIdPunto' => $intIdPunto,
                    'arrayServicios' => $objDatosInformacionGrid,
                    'intMesConsumo' => intval($strMesConsumo),
                    'intAnioConsumo' => intval($strAnioConsumo),
                    'arrayFechas' => $arrayFechas,
                    'strSelectedOption' => $strOpcFeConsumoSelected,
                    'strOpcRangoConsumoSelected' => $strOpcRangoConsumoSelected
                );
                $objValidacionFacturaDuplicada = $objInfoDocumentoFinancieroCabService->esFacturaDuplicada($arrayParametrosDuplicacion);
            }

            if ($objValidacionFacturaDuplicada->esDuplicada) 
            {
                $objDocumentoDuplicado = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                        ->findOneById($objValidacionFacturaDuplicada->idDocumentoFinancieroCab);
                
                $strMensajeDuplicacion = '';
                $strNumeroFacturaSri = $objDocumentoDuplicado->getNumeroFacturaSri();
                if ($strNumeroFacturaSri)
                {
                    // Si se tiene número de factura de SRI, es factura
                    $strMensajeDuplicacion = 'Se trató de duplicar la factura '.$strNumeroFacturaSri;
                }
                else
                {
                    // Si no, es prefactura
                    $strMensajeDuplicacion = 'Se trató de duplicar una prefactura.';
                }

                $intIdDocumentoDuplicado = $objValidacionFacturaDuplicada->idDocumentoFinancieroCab;

                $serviceUtil->insertError('Telcos+', 
                                           'Facturación Manual', 
                                           'Se trató de duplicar la factura/prefactura con id '.$intIdDocumentoDuplicado, 
                                           $strUsuario, 
                                           $strIpCreacion );

                $this->get('session')->getFlashBag()->add('facturaDuplicada', [
                    'mensaje' => $strMensajeDuplicacion,
                    'idFactura' => $objValidacionFacturaDuplicada->idDocumentoFinancieroCab
                ]);

                return $this->redirect($this->generateUrl($strUrlRedirectNew));
            }
        }

        if($strClonarFactura=="S")
        {
            $intIdFactura = $objRequest->get('strIdFactura');
        }
        
        if ( $strFacturaElectronica == 'N' )
        {
            $strEstado          = 'Activo';
            $strEsElectronica   = "N";
        }// ( $strFacturaElectronica == 'N' )
        
        //Verificar si paga_iva
        //Se obtiene informacion del cliente si paga iva o no
        $entitypersona = $emComercial->getRepository('schemaBundle:InfoPersona')->find($strCliente["id_persona"]);
        $strPagaIva    = $entitypersona->getPagaIva();
        
        $strEmpresaCod                        = $objSession->get('idEmpresa');
        $arrayCliente                         = $objSession->get('cliente');
        $arrayParametros['cliente']           = $arrayCliente;
        $arrayParametros["strEmpresaCod"]     = $strEmpresaCod;
        $arrayParametros["boolPuedeFacturar"] = true;
        $arrayParametros['punto_id']          = $arrayPtoCliente;
        $arrayParametros                      = $emFinanciero->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                             ->validaContactoFacturacion($arrayParametros);
        
        /**
         * Bloque que retorna las opciones habilitadas para la empresa en sessión
         */
        $strFechaConsumo            = 'N';
        $strOpcionesFechaConsumo    = 'N';
        $arrayOpcionesHabilitadas   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('OPCIONES_HABILITADAS_FINANCIERO', 
                                                      'FINANCIERO',
                                                      'FACTURACION', 
                                                      $strCodigoFacturas, 
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
                    if ( $arrayOpcion['valor1'] == "FECHA_CONSUMO" )
                    {
                        $strFechaConsumo = $arrayOpcion['valor2'];
                    }// ( $arrayOpcion['valor1'] == "FECHA_CONSUMO" )
                    
                    if ( $arrayOpcion['valor1'] == "OPCIONES_FECHA_CONSUMO" )
                    {
                        $strOpcionesFechaConsumo = $arrayOpcion['valor2'];
                    }// ( $arrayOpcion['valor1'] == "OPCIONES_FECHA_CONSUMO" )
                }// ( isset($arrayOpcion['valor1']) && !empty($arrayOpcion['valor1']) && isset($arrayOpcion['valor2'])...
            }//foreach ( $arrayOpcionesHabilitadas as $arrayOpcion )
        }// ( !empty($arrayOpcionesHabilitadas) )

        if ($arrayParametros["boolPuedeFacturar"])
        {
            //valida si el cliente esta en sesion
            if($intIdPunto)
            {
                $boolTieneFacturasCreadas = false;

                /*
                 * Bloque verificación de facturas creadas al cliente
                 */
                $objInfoDocumentoFinancieroCab = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                              ->findBy( array( "puntoId"             => $intIdPunto, 
                                                                               "estadoImpresionFact" => array("Activo", "Cerrado", "Pendiente") ) );

                if( $objInfoDocumentoFinancieroCab )
                {
                    $boolTieneFacturasCreadas = true;
                }//( $objInfoDocumentoFinancieroCab )
                /*
                 * Fin Bloque verificación de facturas creadas al cliente
                 */

                //valida el formulario
                if($formInfoDocFinCab->isValid())
                {
                    $objNumeracion       = null;
                    $strNumeroFacturaSri = "";

                    if ( $strTipoFacturacion == 'proporcional' )
                    {
                        if ( $strFacturaElectronica == "S" )
                        {
                            if ( $strPrefijoEmpresa == 'MD' )
                            {
                                $objNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                             ->findOficinaMatrizYFacturacion($intCodEmpresa, 'FACE');
                            }
                            else
                            {
                                $objNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                             ->findByEmpresaYOficina( $intCodEmpresa,
                                                                                      $intIdOficina,
                                                                                      "FACE" );
                            }// ( $strPrefijoEmpresa == 'MD' )
                        }
                        else
                        {
                            $objNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina( $intCodEmpresa,
                                                                                                                                $intIdOficina,
                                                                                                                                "FAC" );
                        }// ( $strFacturaElectronica == "S" )
                    }
                    else
                    {
                        $objNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                     ->findOneBy( array( 'estado'    => self::ESTADO_ACTIVO,
                                                                         'oficinaId' => $intIdOficina,
                                                                         'empresaId' => $intCodEmpresa,
                                                                         'id'        => $intIdNumeracion ) );
                    }// ( $strTipoFacturacion == 'proporcional' )

                    if ( is_object($objNumeracion) )
                    {
                        /**
                         * Bloque que retorna el máximo tamaño que debe tener el secuencial de la factura de la empresa en sessión
                         */
                        $intTamañoSecuencial      = 0;
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
                            $intTamañoSecuencial = $arraySecuencialDocumento['valor1'];
                        }// ( isset($arraySecuencialDocumento['valor1']) && !empty($arraySecuencialDocumento['valor1']) )
                        
                        $strNumeroFacturaSri   = "";
                        $strSecuencia          = str_pad($objNumeracion->getSecuencia(), $intTamañoSecuencial, "0", STR_PAD_LEFT);
                        $strNumeracionUno      = $objNumeracion->getNumeracionUno();
                        $strNumeracionDos      = $objNumeracion->getNumeracionDos();
                        $strNumeroAutorizacion = $objNumeracion->getNumeroAutorizacion();

                        if( !empty($strNumeracionUno) && !empty($strNumeracionDos) )
                        {
                            $strNumeroFacturaSri = $strNumeracionUno."-".$strNumeracionDos."-".$strSecuencia;
                        }
                        elseif( !empty($strNumeroAutorizacion) )
                        {
                            $strNumeroFacturaSri = $strNumeroAutorizacion."-".$strSecuencia;
                        }
                        else
                        {
                            $strNumeroFacturaSri = "";
                        }

                        $emComercial->getConnection()->beginTransaction();
                        $emFinanciero->getConnection()->beginTransaction();

                        try
                        {
                            if ( empty($strNumeroFacturaSri) )
                            {
                                throw new \Exception("No se ha encontrado número de facturación para numerar el documento.");
                            }// ( empty($strNumeroFacturaSri) )

                            //busqueda del documento
                            $entityTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                ->findOneByCodigoTipoDocumento($strCodigoFacturas);

                            $entityInfoDocumentoFinCab->setTipoDocumentoId($entityTipoDocumento);
                            $entityInfoDocumentoFinCab->setPuntoId($intIdPunto);
                            $entityInfoDocumentoFinCab->setEsAutomatica("N");
                            $entityInfoDocumentoFinCab->setProrrateo("N");
                            $entityInfoDocumentoFinCab->setReactivacion("N");
                            $entityInfoDocumentoFinCab->setRecurrente("N");
                            $entityInfoDocumentoFinCab->setComisiona("S");
                            $entityInfoDocumentoFinCab->setEntregoRetencionFte("N");
                            $entityInfoDocumentoFinCab->setOficinaId($intIdOficina);
                            $entityInfoDocumentoFinCab->setFeCreacion(new \DateTime('now'));
                            $entityInfoDocumentoFinCab->setFeEmision(new \DateTime('now'));
                            $entityInfoDocumentoFinCab->setUsrCreacion($strUsuario);
                            $entityInfoDocumentoFinCab->setEstadoImpresionFact($strEstado);
                            $entityInfoDocumentoFinCab->setEsElectronica($strEsElectronica);
                            $entityInfoDocumentoFinCab->setNumeroFacturaSri($strNumeroFacturaSri);
                            if($strObservacion)
                            {
                                $entityInfoDocumentoFinCab->setObservacion($strObservacion);    
                            }

                            if ( $strFechaConsumo == 'S' && !empty($strMesConsumo) && !empty($strAnioConsumo) )
                            {
                                $entityInfoDocumentoFinCab->setMesConsumo($strMesConsumo);
                                $entityInfoDocumentoFinCab->setAnioConsumo($strAnioConsumo);
                            }// ( $strFechaConsumo == 'S' && !empty($strMesConsumo) && !empty($strAnioConsumo) )
                            elseif ( $strOpcionesFechaConsumo == "S" && !empty($strOpcFeConsumoSelected) )
                            {
                                if( $strOpcFeConsumoSelected == "feConsumo" )
                                {
                                    $entityInfoDocumentoFinCab->setMesConsumo($strMesConsumo);
                                    $entityInfoDocumentoFinCab->setAnioConsumo($strAnioConsumo);
                                }//( $strOpcFeConsumoSelected == "feConsumo" )
                                else
                                {
                                    if( $strOpcRangoConsumoSelected == "consumoDias" )
                                    {
                                        if( !empty($strDateDiaInicio) && !empty($strDateDiaFin) )
                                        {
                                            $timeInicialTmp   = strtotime($strDateDiaInicio);
                                            $dateFechaInicial = date('d-m-Y', $timeInicialTmp);

                                            $timeFinalTmp   = strtotime($strDateDiaFin);
                                            $dateFechaFinal = date('d-m-Y', $timeFinalTmp);
                                        }
                                        else
                                        {
                                            throw new \Exception("Fechas por consumoDias en NULL. Fecha Inicio (".$strDateDiaInicio."), Fecha Fin (".
                                                                 $strDateDiaFin.")");
                                        } 
                                    }//( $strOpcRangoConsumoSelected == "consumoDias" )
                                    else
                                    {
                                        if( !empty($strDateMesInicio) && !empty($strDateMesFin) )
                                        {
                                            $datetimeInicialTmp = new \DateTime($strDateMesInicio);
                                            $dateFechaInicial   = $datetimeInicialTmp->format('d-m-Y');

                                            $datetimeFinalTmp = new \DateTime($strDateMesFin);
                                            $dateFechaFinal   = $datetimeFinalTmp->format('t-m-Y');
                                        }
                                        else
                                        {
                                            throw new \Exception("Fechas por consumoMeses en NULL. Fecha Inicio (".$strDateMesInicio."), Fecha Fin (".
                                                                 $strDateMesFin.")");
                                        }
                                    }//( $strOpcRangoConsumoSelected == "consumoMeses" )

                                    $datetimeFechaInicio = $dateFechaInicial ? \DateTime::createFromFormat("d-m-Y", $dateFechaInicial) : null;
                                    $datetimeFechaFin    = $dateFechaFinal ? \DateTime::createFromFormat("d-m-Y", $dateFechaFinal) : null;

                                    if( $datetimeFechaInicio && $datetimeFechaFin )
                                    {
                                        $strFechaInicialTmp = "";
                                        $strFechaFinalTmp   = "";

                                        if( $strOpcRangoConsumoSelected == "consumoDias" )
                                        {
                                            $strObservacionRangoConsumo = "Del ";

                                            $strFechaInicialTmp .= strftime("%d",$datetimeFechaInicio->getTimestamp())." ";
                                            $strFechaFinalTmp   .= strftime("%d",$datetimeFechaFin->getTimestamp())." ";
                                        }//( $strOpcRangoConsumoSelected == "consumoDias" )

                                        $strFechaInicialTmp .= ucfirst(strtolower(strftime("%B",$datetimeFechaInicio->getTimestamp())))." ".
                                                               strftime("%Y",$datetimeFechaInicio->getTimestamp());

                                        $strFechaFinalTmp .= ucfirst(strtolower(strftime("%B",$datetimeFechaFin->getTimestamp())))." ".
                                                             strftime("%Y",$datetimeFechaFin->getTimestamp());

                                        $strObservacionRangoConsumo .= $strFechaInicialTmp;
                                        $strObservacionRangoConsumo .= ( $strOpcRangoConsumoSelected == "consumoDias" ) ? " al " : " a ";
                                        $strObservacionRangoConsumo .= $strFechaFinalTmp;

                                        // Esto debe setearse por los dos consumos que no son el default
                                        $entityInfoDocumentoFinCab->setRangoConsumo($strObservacionRangoConsumo);    
                                    }//( $datetimeFechaInicio && $datetimeFechaFin )
                                    else
                                    {
                                        throw new \Exception("No se puede guardar la factura puesto que hubo un error al obtener las fechas por "
                                                             ."rango de consumo.");
                                    }//( !$datetimeFechaInicio && !$datetimeFechaFin )
                                }//( $strOpcFeConsumoSelected == "rangoConsumo" )
                            }// ( $strOpcionesFechaConsumo == "S" && !empty($strOpcFeConsumoSelected) )
                            else
                            {
                                if ( $strTipoFacturacion == 'proporcional' )
                                {
                                    $strFechaInicialTmp = strftime("%d",$datetimeFechaInicio->getTimestamp())." ".
                                                          ucfirst(strtolower(strftime("%B",$datetimeFechaInicio->getTimestamp())))." ".
                                                          strftime("%Y",$datetimeFechaInicio->getTimestamp());

                                    $strFechaFinalTmp = strftime("%d",$datetimeFechaFin->getTimestamp())." ".
                                                        ucfirst(strtolower(strftime("%B",$datetimeFechaFin->getTimestamp())))." ".
                                                        strftime("%Y",$datetimeFechaFin->getTimestamp());

                                    $strObservacionRangoConsumo = "Del ".$strFechaInicialTmp." al ".$strFechaFinalTmp;

                                    $entityInfoDocumentoFinCab->setRangoConsumo($strObservacionRangoConsumo);

                                    $dateFechaInicial = $datetimeFechaInicio->format('d-m-Y');
                                    $dateFechaFinal   = $datetimeFechaFin->format('d-m-Y');
                                }// ( $strTipoFacturacion == 'proporcional' )
                            }// ( $strTipoFacturacion == 'proporcional' )

                            $emFinanciero->persist($entityInfoDocumentoFinCab);
                            $emFinanciero->flush();

                            //verifica que la entidad no este vacia
                            if($entityInfoDocumentoFinCab)
                            {
                                /**
                                 * Bloque que guarda como características las fechas de consumo del rango seleccionado por el usuario
                                 */
                                if ( $strOpcFeConsumoSelected == "rangoConsumo" || $strTipoFacturacion == 'proporcional' )
                                {
                                    $objFeRangoInicialCaract = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                           ->findOneBy( array('estado'                    => 'Activo', 
                                                                                              'descripcionCaracteristica' => 'FE_RANGO_INICIAL') );
                                    $objFeRangoFinalCaract   = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                           ->findOneBy( array('estado'                    => 'Activo', 
                                                                                              'descripcionCaracteristica' => 'FE_RANGO_FINAL') );

                                    if( $objFeRangoInicialCaract && $objFeRangoFinalCaract && $dateFechaInicial && $dateFechaFinal )
                                    {
                                        $objFeRangoInicialDocCaract = new InfoDocumentoCaracteristica();
                                        $objFeRangoInicialDocCaract->setCaracteristicaId($objFeRangoInicialCaract->getId());
                                        $objFeRangoInicialDocCaract->setDocumentoId($entityInfoDocumentoFinCab);
                                        $objFeRangoInicialDocCaract->setEstado('Activo');
                                        $objFeRangoInicialDocCaract->setFeCreacion(new \DateTime('now'));
                                        $objFeRangoInicialDocCaract->setIpCreacion($strIpCreacion);
                                        $objFeRangoInicialDocCaract->setUsrCreacion($strUsuario);
                                        $objFeRangoInicialDocCaract->setValor($dateFechaInicial);
                                        $emFinanciero->persist($objFeRangoInicialDocCaract);
                                        $emFinanciero->flush();

                                        $objFeRangoFinalDocCaract = new InfoDocumentoCaracteristica();
                                        $objFeRangoFinalDocCaract->setCaracteristicaId($objFeRangoFinalCaract->getId());
                                        $objFeRangoFinalDocCaract->setDocumentoId($entityInfoDocumentoFinCab);
                                        $objFeRangoFinalDocCaract->setEstado('Activo');
                                        $objFeRangoFinalDocCaract->setFeCreacion(new \DateTime('now'));
                                        $objFeRangoFinalDocCaract->setIpCreacion($strIpCreacion);
                                        $objFeRangoFinalDocCaract->setUsrCreacion($strUsuario);
                                        $objFeRangoFinalDocCaract->setValor($dateFechaFinal);
                                        $emFinanciero->persist($objFeRangoFinalDocCaract);
                                        $emFinanciero->flush();
                                    }//( $objFeRangoInicialCaract && $objFeRangoFinalCaract && $dateFechaInicial && $dateFechaFinal )
                                    else
                                    {
                                        throw new \Exception("No se puede guardar la factura puesto que no se encontraron las caracteristicas de "
                                                            ."'FE_RANGO_INICIAL' y/o 'FE_RANGO_FINAL'");
                                    }
                                }// ( $strOpcFeConsumoSelected == "rangoConsumo" || $strTipoFacturacion == 'proporcional' )


                                //Actualizo la numeracion en la tabla
                                $strNumeroFacturaSriAct = ($objNumeracion->getSecuencia() + 1);

                                $intIdNumeracion   = $objNumeracion->getId();
                                $objAdmiNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findOneById($intIdNumeracion);

                                $objAdmiNumeracion->setSecuencia($strNumeroFacturaSriAct);
                                $objAdmiNumeracion->setUsrUltMod($strUsuario);
                                $objAdmiNumeracion->setFeUltMod(new \DateTime('now'));
                                $emComercial->persist($objAdmiNumeracion);
                                $emComercial->flush();

                                $entityInfoDocumentoHistorial = new InfoDocumentoHistorial();
                                $entityInfoDocumentoHistorial->setDocumentoId($entityInfoDocumentoFinCab);
                                $entityInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                                $entityInfoDocumentoHistorial->setUsrCreacion($strUsuario);
                                $entityInfoDocumentoHistorial->setObservacion($strObservacion);
                                $entityInfoDocumentoHistorial->setEstado($strEstado);
                                $emFinanciero->persist($entityInfoDocumentoHistorial);
                                $emFinanciero->flush();
                            }// ( $entityInfoDocumentoFinCab )
                            //Guardando el detalle


                            //busqueda de la persona
                            //verifica que hayan datos enviados desde el cliente
                            if($objDatosInformacionGrid)
                            {
                                $intImpuestoAcumulado                = 0;
                                $intSubtotalAcumulado                = 0;
                                $intDescuentoAcumulado               = 0;
                                $floatDescuentoCompensacionAcumulado = 0;
                                $entityAdmiImpuestoIva               = null;
                                $strTipoImpuesto                     = 'IVA';
                               
                                $objAdmiPais = $emGeneral->getRepository("schemaBundle:AdmiPais")->findOneById($intIdPaisSession);
           
                                if ( is_object($objAdmiPais) )
                                {
                                    if ( strtoupper($strPaisSession) != 'ECUADOR'  && strtoupper($strPaisSession) != 'GUATEMALA' )
                                    {
                                        $strTipoImpuesto = 'ITBMS';
                                    }
                                    
                                    if ( strtoupper($strPaisSession) == 'GUATEMALA' )
                                    {
                                        $strTipoImpuesto = 'IVA_GT';
                                    }
                                    
                                    $entityAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                                       ->findOneBy( array('tipoImpuesto' => $strTipoImpuesto, 
                                                                                          'estado'       => 'Activo',
                                                                                          'paisId'       => $objAdmiPais) );
                                   
                                }// ( is_object($objAdmiPais) )
                                
                                // Crea los detalles del documento
                                foreach($objDatosInformacionGrid as $objInfoDatosInformacionGrid)
                                {
                                    $strTmpDescripcionDet = $objInfoDatosInformacionGrid->descripcion;
                                    $strTmpDescripcionDet = trim($strTmpDescripcionDet);

                                    if(strcmp($strTmpDescripcionDet,"Cargo por Gestion de Cobranza")== 0)
                                    {
                                        $boolTieneReprocesoDebito = true;
                                    }
                                        
                                    if( floatval($objInfoDatosInformacionGrid->precio) > 0 )
                                    {
                                        $intSubtotalDetalle         = 0;
                                        $intPrecioSinDescuento      = 0;
                                        $entityInfoDocumentoFinDet  = new InfoDocumentoFinancieroDet();
                                        $entityInfoDocumentoFinDet->setDocumentoId($entityInfoDocumentoFinCab);
                                        $entityInfoDocumentoFinDet->setPuntoId($objInfoDatosInformacionGrid->puntoId);
                                        if($objInfoDatosInformacionGrid->idServicio > 0)
                                        {
                                            $entityInfoDocumentoFinDet->setServicioId($objInfoDatosInformacionGrid->idServicio);
                                        }
                                        $entityInfoDocumentoFinDet->setCantidad($objInfoDatosInformacionGrid->cantidad);
                                        $entityInfoDocumentoFinDet->setEmpresaId($intCodEmpresa);
                                        $entityInfoDocumentoFinDet->setOficinaId($intIdOficina);
                                        //El precio ya incluye el descuento... en el caso de los planes
                                        $entityInfoDocumentoFinDet->setPrecioVentaFacproDetalle(round($objInfoDatosInformacionGrid->precio, 2));
                                        //El descuento debe ser informativo
                                        //El descuento se cambio a que sea por valor
                                        $entityInfoDocumentoFinDet->setDescuentoFacproDetalle(round($objInfoDatosInformacionGrid->descuento, 2));
                                        $entityInfoDocumentoFinDet->setFeCreacion(new \DateTime('now'));
                                        $entityInfoDocumentoFinDet->setUsrCreacion($strUsuario);

                                        /*
                                         * Verifica que el tipo de factura sea Precargada, para setear en la descripcion la fecha de
                                         * confirmacion (activacion) del servicio
                                         */
                                        if($objInfoDatosInformacionGrid->tipoOrden == 'PRE')
                                        {
                                            $objInfoServicio = null;
                                            
                                            //verifica que el servicio sea un producto
                                            if($objInfoDatosInformacionGrid->tipo == 'PR')
                                            {
                                                $arrayTmpParametros = array( 'productoId'         => $objInfoDatosInformacionGrid->codigo,
                                                                             'puntoFacturacionId' => $intIdPunto );

                                                $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                               ->findOneBy( $arrayTmpParametros );
                                            }
                                            else
                                            {
                                                //verifica que el servicio sea un plan
                                                $objInfoServicio = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                   ->findOneBy(array('planId'             => $objInfoDatosInformacionGrid->codigo, 
                                                                                     'puntoFacturacionId' => $intIdPunto));
                                            }


                                            /*
                                             * Bloque verificación de frecuencia
                                             * 
                                             * Bloque que verifica si la frecuencia del producto es mayor que cero para resetearle a los meses
                                             * restantes el valor de la frecuencia para que no sea cobrado en la factura masiva. 
                                             */
                                            if( !$boolTieneFacturasCreadas &&  is_object($objInfoServicio)
                                                && $objInfoServicio->getFrecuenciaProducto() > 0 )
                                            {
                                                $objInfoServicio->setMesesRestantes($objInfoServicio->getFrecuenciaProducto());
                                                $emComercial->persist($objInfoServicio);
                                                $emComercial->flush();
                                            }
                                            /*
                                             * Fin Bloque verificación de frecuencia 
                                             */

                                            if ( $strPrefijoEmpresa == 'MD' )
                                            {
                                                if ( $strTipoFacturacion == 'proporcional' )
                                                {
                                                    $strTmpDescripcionDet = trim($objInfoDatosInformacionGrid->descripcion);
                                                    
                                                    $entityInfoDocumentoFinDet->setObservacionesFacturaDetalle($strTmpDescripcionDet);
                                                }
                                                else
                                                {
                                                    $strFechaConfirmacionSrv = '';

                                                    if ( is_object($objInfoServicio) )
                                                    {
                                                        $strFechaConfirmacionSrv = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                               ->obtenerFechaConfirmacion($objInfoServicio->getId());
                                                        if($strFechaConfirmacionSrv)
                                                        {
                                                            $strFechaConfirmacionSrv = 'Fecha de activacion: ' . $strFechaConfirmacionSrv;
                                                        }

                                                        $entityInfoDocumentoFinDet->setObservacionesFacturaDetalle(trim($strFechaConfirmacionSrv));
                                                    }// ( is_object($objInfoServicio) )
                                                }// ( $strTipoFacturacion == 'proporcional' )
                                            }
                                            else
                                            {
                                                $strTmpDescripcionDet = trim($objInfoDatosInformacionGrid->descripcion);
                                                $strTmpDescripcionDet = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                     ->getValidXmlValue($strTmpDescripcionDet);
                                                $entityInfoDocumentoFinDet->setObservacionesFacturaDetalle($strTmpDescripcionDet);
                                            }// ( $strPrefijoEmpresa == 'MD' )
                                        }
                                        else
                                        {
                                            $strTmpDescripcionDet = trim($objInfoDatosInformacionGrid->descripcion);
                                            $strTmpDescripcionDet = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                 ->getValidXmlValue($strTmpDescripcionDet);

                                            //por falso obtiene la descripcion obtenida desde el cliente
                                            $entityInfoDocumentoFinDet->setObservacionesFacturaDetalle($strTmpDescripcionDet);
                                        }

                                        $emFinanciero->persist($entityInfoDocumentoFinDet);
                                        $emFinanciero->flush();


                                        //verifica si es producto
                                        if($objInfoDatosInformacionGrid->tipo == 'PR')
                                        {
                                            $floatImpuestoIceAcumulado = 0;

                                            /* Cuando es producto voy a la tabla AdmiProducto para sacar los impuestos */
                                            $entityInfoDocumentoFinDet->setProductoId($objInfoDatosInformacionGrid->codigo);

                                            $intPrecioNuevo        = ($objInfoDatosInformacionGrid->precio * $objInfoDatosInformacionGrid->cantidad) 
                                                                      - $objInfoDatosInformacionGrid->descuento;
                                            $intPrecioSinDescuento = ($objInfoDatosInformacionGrid->precio * $objInfoDatosInformacionGrid->cantidad);
                                            $intSubtotalDetalle    += $intPrecioNuevo;

                                            $arrayParametrosImpuestosPrioridad  = array( 'intIdProducto' => $objInfoDatosInformacionGrid->codigo,
                                                                                         'strEstado'     => 'Activo',
                                                                                         'intIdPais'     => $intIdPaisSession,
                                                                                         'intPrioridad'  => 1 );
                                            $arrayImpuestosPrioridad1           = $emComercial->getRepository('schemaBundle:InfoProductoImpuesto')
                                                                                  ->getInfoImpuestoByCriterios($arrayParametrosImpuestosPrioridad);
                                            $objInfoProductoImpuestosPrioridad1 = $arrayImpuestosPrioridad1['registros'];

                                            if($objInfoProductoImpuestosPrioridad1)
                                            {
                                                foreach($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImpuesto)
                                                {
                                                    $boolImpuesto     = true;
                                                    $objAdmiImpuesto  = $objInfoProductoImpuesto->getImpuestoId();

                                                    if($objAdmiImpuesto)
                                                    {
                                                        if( $strPagaIce != "SI" && ( $objAdmiImpuesto->getTipoImpuesto() == 'ICE'
                                                                                     || $objAdmiImpuesto->getTipoImpuesto() == 'IEC' ) )
                                                        {
                                                            $boolImpuesto = false;
                                                        }

                                                        if($boolImpuesto)
                                                        {
                                                            //Id del impuesto con el que se va a crear la factura
                                                            $intTmpIdImpuesto = $objAdmiImpuesto->getId();
                                                            $intTmpPorcentaje = $objAdmiImpuesto->getPorcentajeImpuesto();
                                                            $intTmpImpuesto   = (($intPrecioNuevo * $intTmpPorcentaje)/100);

                                                            if ( $objAdmiImpuesto->getTipoImpuesto() == 'ICE' 
                                                                 || $objAdmiImpuesto->getTipoImpuesto() == 'IEC' )
                                                            {
                                                                $boolDocumentoTieneIce     = true;
                                                                $floatImpuestoIceAcumulado += $intTmpImpuesto;
                                                            }

                                                            $entityInfoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                                            $entityInfoDocumentoFinancieroImp->setDetalleDocId($entityInfoDocumentoFinDet->getId());
                                                            $entityInfoDocumentoFinancieroImp->setImpuestoId($intTmpIdImpuesto);
                                                            $entityInfoDocumentoFinancieroImp->setValorImpuesto($intTmpImpuesto);
                                                            $entityInfoDocumentoFinancieroImp->setPorcentaje($intTmpPorcentaje);
                                                            $entityInfoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                                            $entityInfoDocumentoFinancieroImp->setUsrCreacion($strUsuario);
                                                            $emFinanciero->persist($entityInfoDocumentoFinancieroImp);
                                                            $emFinanciero->flush();

                                                            if( $objInfoDatosInformacionGrid->tipoOrden != "PAGR" )
                                                            {
                                                                $intImpuestoAcumulado += $intTmpImpuesto;
                                                            }//( $objInfoDatosInformacionGrid->tipoOrden != "PAGR" )
                                                        }//($boolImpuesto)
                                                    }//($objAdmiImpuesto)
                                                }//($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImpuesto)
                                            }//($objInfoProductoImpuestosPrioridad1)


                                            $arrayParametrosImpuestosPrioridad['intPrioridad'] = 2;

                                            $arrayImpuestosPrioridad2           = $emComercial->getRepository('schemaBundle:InfoProductoImpuesto')
                                                                                  ->getInfoImpuestoByCriterios($arrayParametrosImpuestosPrioridad);
                                            $objInfoProductoImpuestosPrioridad2 = $arrayImpuestosPrioridad2['registros'];

                                            if($objInfoProductoImpuestosPrioridad2)
                                            {
                                                foreach($objInfoProductoImpuestosPrioridad2 as $objInfoProductoImpuesto)
                                                {
                                                    $boolImpuesto     = true;
                                                    $objAdmiImpuesto  = $objInfoProductoImpuesto->getImpuestoId();

                                                    if($objAdmiImpuesto)
                                                    {
                                                        if ( $strPagaIva != "S" && ( $objAdmiImpuesto->getTipoImpuesto() == 'IVA' || $objAdmiImpuesto->getTipoImpuesto() == 'IVA_GT'
                                                                                     || $objAdmiImpuesto->getTipoImpuesto() == 'ITBMS' ) )
                                                        {
                                                            $boolImpuesto = false;
                                                        }

                                                        if($boolImpuesto)
                                                        {
                                                            //Id del impuesto con el que se va a crear la factura
                                                            $intTmpIdImpuesto = $objAdmiImpuesto->getId();
                                                            $intTmpPorcentaje = $objAdmiImpuesto->getPorcentajeImpuesto();

                                                            /*
                                                             * Se verifica si el usuario seleccionó algún impuesto IVA para crear la factura para
                                                             * darle prioridad con el impuesto seleccionado.
                                                             */
                                                            if ( $intIdImpuesto && ( $objAdmiImpuesto->getTipoImpuesto() == 'IVA' 
                                                                                     || $objAdmiImpuesto->getTipoImpuesto() == 'ITBMS' ) )
                                                            {
                                                                $objAdmiImpuestoSelected = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                                                                     ->findOneById($intIdImpuesto);

                                                                if( $objAdmiImpuestoSelected )
                                                                {
                                                                    $intTmpIdImpuesto = $objAdmiImpuestoSelected->getId();
                                                                    $intTmpPorcentaje = $objAdmiImpuestoSelected->getPorcentajeImpuesto();
                                                                }//( $objAdmiImpuestoSelected )
                                                            }// ( $intIdImpuesto && ( $objAdmiImpuesto->getTipoImpuesto() == 'IVA'...

                                                            $intTmpImpuesto   = ( ( ($intPrecioNuevo + $floatImpuestoIceAcumulado) 
                                                                                     * $intTmpPorcentaje ) / 100 );

                                                            $entityInfoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                                            $entityInfoDocumentoFinancieroImp->setDetalleDocId($entityInfoDocumentoFinDet->getId());
                                                            $entityInfoDocumentoFinancieroImp->setImpuestoId($intTmpIdImpuesto);
                                                            $entityInfoDocumentoFinancieroImp->setValorImpuesto($intTmpImpuesto);
                                                            $entityInfoDocumentoFinancieroImp->setPorcentaje($intTmpPorcentaje);
                                                            $entityInfoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                                            $entityInfoDocumentoFinancieroImp->setUsrCreacion($strUsuario);
                                                            $emFinanciero->persist($entityInfoDocumentoFinancieroImp);
                                                            $emFinanciero->flush();

                                                            if( $objInfoDatosInformacionGrid->tipoOrden != "PAGR" )
                                                            {
                                                                $intImpuestoAcumulado += $intTmpImpuesto;
                                                            }//( $objInfoDatosInformacionGrid->tipoOrden != "PAGR" )
                                                        }//($boolImpuesto)
                                                    }//($objAdmiImpuesto)
                                                }//($objInfoProductoImpuestosPrioridad2 as $objInfoProductoImpuesto)
                                            }//($objInfoProductoImpuestosPrioridad2)


                                            if( $objInfoDatosInformacionGrid->tipoOrden != "PAGR" )
                                            {
                                                $intSubtotalAcumulado    +=  $intPrecioSinDescuento;
                                            }
                                            else
                                            {
                                                $intImpuestoAcumulado    +=  $objInfoDatosInformacionGrid->impuesto;
                                                $intSubtotalAcumulado    +=  $objInfoDatosInformacionGrid->precio;
                                            }//( $objInfoDatosInformacionGrid->tipoOrden != "PAGR" )

                                            $floatValorTmpDescuento = $objInfoDatosInformacionGrid->descuento;
                                            $floatValorTmpDescuento = (!empty($floatValorTmpDescuento) ? $floatValorTmpDescuento : 0);
                                            $intDescuentoAcumulado  += $floatValorTmpDescuento;
                                        }//($objInfoDatosInformacionGrid->tipo == 'PR')
                                        else
                                        {
                                            $intTmpImpuesto = 0;

                                            /* Cuando es plan voy a la tabla InfoPlanCab para sacar la bandera del impuesto */
                                            $entityInfoDocumentoFinDet->setPlanId($objInfoDatosInformacionGrid->codigo);
                                            $entityInfoPlanCab = $emComercial->getRepository('schemaBundle:InfoPlanCab')
                                                                             ->find($objInfoDatosInformacionGrid->codigo);
                                            //verifica si la entidad tiene datos
                                            if($entityInfoPlanCab)
                                            {
                                                $intPrecioNuevo        = ($objInfoDatosInformacionGrid->precio 
                                                                          * $objInfoDatosInformacionGrid->cantidad) 
                                                                          - $objInfoDatosInformacionGrid->descuento;
                                                $intPrecioSinDescuento = ($objInfoDatosInformacionGrid->precio
                                                                          * $objInfoDatosInformacionGrid->cantidad);
                                                $intSubtotalDetalle    += $intPrecioNuevo;

                                                if( $entityInfoPlanCab->getIva() == 'S' && $strPagaIva == "S" )
                                                {
                                                    if ( is_object($entityAdmiImpuestoIva) )
                                                    {
                                                        //Id del impuesto con el que se va a crear la factura
                                                        $intTmpIdImpuesto = $entityAdmiImpuestoIva->getId();
                                                        $intTmpPorcentaje = $entityAdmiImpuestoIva->getPorcentajeImpuesto();

                                                        /*
                                                         * Se verifica si el usuario seleccionó algún impuesto IVA para crear la factura para darle 
                                                         * prioridad con el impuesto seleccionado.
                                                         */
                                                        if ( $intIdImpuesto && ( $entityAdmiImpuestoIva->getTipoImpuesto() == 'IVA' 
                                                                                 || $entityAdmiImpuestoIva->getTipoImpuesto() == 'ITBMS' ) )
                                                        {
                                                            $objAdmiImpuestoSelected = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                                                                 ->findOneById($intIdImpuesto);

                                                            if( $objAdmiImpuestoSelected )
                                                            {
                                                                $intTmpIdImpuesto = $intIdImpuesto;
                                                                $intTmpPorcentaje = $objAdmiImpuestoSelected->getPorcentajeImpuesto();
                                                            }//( $objAdmiImpuestoSelected )
                                                        }// ( $intIdImpuesto && ( $entityAdmiImpuestoIva->getTipoImpuesto() == 'IVA'...

                                                        $intTmpImpuesto = (($intPrecioNuevo * $intTmpPorcentaje) / 100);

                                                        //Registro del impuesto
                                                        $entityInfoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                                        $entityInfoDocumentoFinancieroImp->setDetalleDocId($entityInfoDocumentoFinDet->getId());
                                                        $entityInfoDocumentoFinancieroImp->setImpuestoId($intTmpIdImpuesto);
                                                        $entityInfoDocumentoFinancieroImp->setValorImpuesto($intTmpImpuesto);
                                                        $entityInfoDocumentoFinancieroImp->setPorcentaje($intTmpPorcentaje);
                                                        $entityInfoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                                        $entityInfoDocumentoFinancieroImp->setUsrCreacion($strUsuario);
                                                        $emFinanciero->persist($entityInfoDocumentoFinancieroImp);
                                                        $emFinanciero->flush();
                                                    }
                                                    else
                                                    {
                                                        throw new \Exception('No se encontró el valor del IVA activo para guardar la factura.');
                                                    }// ( is_object($entityAdmiImpuestoIva) )
                                                }

                                                if( $objInfoDatosInformacionGrid->tipoOrden != "PAGR" )
                                                {
                                                    $intImpuestoAcumulado    +=  $intTmpImpuesto;
                                                    $intSubtotalAcumulado    +=  $intPrecioSinDescuento;
                                                    $intDescuentoAcumulado   +=  $objInfoDatosInformacionGrid->descuento;
                                                }
                                                else
                                                {
                                                    $intImpuestoAcumulado    +=  $objInfoDatosInformacionGrid->impuesto;
                                                    $intSubtotalAcumulado    +=  $objInfoDatosInformacionGrid->precio;
                                                    $intDescuentoAcumulado   +=  $objInfoDatosInformacionGrid->descuento;
                                                }
                                            }//($entityInfoPlanCab)
                                        }//($objInfoDatosInformacionGrid->tipo != 'PR')
                                        
                                        
                                        /**
                                         * Bloque que obtiene el valor de compensación enviado por el grid para ser acumulado y posteriormente
                                         * guardado en la cabecera
                                         */
                                        $intCantidad               = $objInfoDatosInformacionGrid->cantidad;
                                        $intCantidad               = ( !empty($intCantidad) ) ? $intCantidad : 0;
                                        $floatValorTmpCompensacion = $objInfoDatosInformacionGrid->compensacionSolidaria 
                                                                     ? ($objInfoDatosInformacionGrid->compensacionSolidaria * $intCantidad) 
                                                                     : 0 ;

                                        $floatDescuentoCompensacionAcumulado += $floatValorTmpCompensacion;
                                    }//( floatval($objInfoDatosInformacionGrid->precio) > 0 )
                                }//foreach($objDatosInformacionGrid as $objInfoDatosInformacionGrid)
                            }//($objDatosInformacionGrid)

                            $entityInfoDocumentoFinancieroCabAct = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                ->find($entityInfoDocumentoFinCab->getId());
                            $entityInfoDocumentoFinancieroCabAct->setSubtotal(round($intSubtotalAcumulado, 2));
                            $entityInfoDocumentoFinancieroCabAct->setSubtotalConImpuesto(round($intImpuestoAcumulado, 2));
                            $entityInfoDocumentoFinancieroCabAct->setSubtotalDescuento(round($intDescuentoAcumulado, 2));
                            $entityInfoDocumentoFinancieroCabAct->setDescuentoCompensacion(round($floatDescuentoCompensacionAcumulado, 2));
                            $intValorTotal = ( round($intSubtotalAcumulado, 2) - round($intDescuentoAcumulado, 2) 
                                               - round($floatDescuentoCompensacionAcumulado, 2) ) + round($intImpuestoAcumulado , 2);
                            $entityInfoDocumentoFinancieroCabAct->setValorTotal(round($intValorTotal, 2));
                            $emFinanciero->persist($entityInfoDocumentoFinancieroCabAct);
                            $emFinanciero->flush();
                            
                            
                            /**
                             * Bloque validador de documento
                             * 
                             * Verifica si se debe regularizar el documento que contiene al menos un detalle con impuesto de ICE
                             */
                            if( is_object($entityInfoDocumentoFinCab) && $boolDocumentoTieneIce )
                            {
                                $arrayParametrosValidador = array('intIdDocumento' => $entityInfoDocumentoFinCab->getId());
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
                                                                                   ->find($entityInfoDocumentoFinCab->getId());
                                            
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
                            }//( is_object($entityInfoDocumentoFinCab) && $boolDocumentoTieneIce )
                            /**
                             * Fin Bloque validador de documento
                             */

                            //Genera el desgloce detalle del documento enviado por parametros
                            $arrayParametrosIn["strPrefijoEmpresa"] = $strPrefijoEmpresa;
                            $arrayParametrosIn["id"]                = $entityInfoDocumentoFinancieroCabAct->getId();
                            $arrayParametrosIn["strCodDocumento"]   = $entityTipoDocumento->getCodigoTipoDocumento();
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
                                    $arrayParametros['strUsrCreacion'] = $strUsuario;                                
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
                            //Clonamos las columnas de facturacion de la cabecera
                            if(isset($strClonarFactura))
                            {
                                $entityInfoDocumentoFinCabAnt = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                                                ->find($intIdFactura);
                                if($strClonarFactura=="S")
                                {
                                    //Factura Actual
                                    $entityInfoDocumentoFinCab->setEsAutomatica("S");
                                    $entityInfoDocumentoFinCab->setProrrateo($entityInfoDocumentoFinCabAnt->getProrrateo());
                                    $entityInfoDocumentoFinCab->setReactivacion($entityInfoDocumentoFinCabAnt->getReactivacion());
                                    $entityInfoDocumentoFinCab->setRecurrente($entityInfoDocumentoFinCabAnt->getRecurrente());
                                    $entityInfoDocumentoFinCab->setComisiona($entityInfoDocumentoFinCabAnt->getComisiona());
                                    $entityInfoDocumentoFinCab->setEntregoRetencionFte($entityInfoDocumentoFinCabAnt->getEntregoRetencionFte());
                                    $emFinanciero->persist($entityInfoDocumentoFinCab);
                                    $emFinanciero->flush();
                                    if($emFinanciero->getConnection()->isTransactionActive())
                                    {
                                        $emFinanciero->getConnection()->commit();
                                    }
                                    $arrayParametros["entityInfoDocumentoFinCab"] = $entityInfoDocumentoFinCab;
                                    $arrayParametros["intIdFactura"] = $intIdFactura;
                                    $arrayParametros["strEmpresaCod"] = $strEmpresaCod;
                                    $arrayParametros["strDescripcionDetCaract"] = $strDescripcionDetCaractClonar;
                                    //Al momento de verificar las caracteristicas 
                                    $arrayRpta = $objInfoDocumentoFinancieroCabService->clonarCaracteristicasFactura($arrayParametros);
                                    if($arrayRpta["error"]!=0)
                                    {
                                        throw new \Exception($arrayRpta["mensaje_error"]);
                                    }

                                    //Eliminamos la factura padre si es por prefacturacion-clonacion
                                    if(isset($strClonarFactura)) 
                                    {
                                        $strObservacion = "Creado por proceso de clonación en base a prefactura #".$intIdFactura;
                                        //Si se crea por clonacion, se pregunta si se debe elimina la factura-padre ()
                                        if($strClonarFactura == "S" && $strNecesitaEliminarPrefactura=="true")
                                        {
                                            $entityFacturaCabe = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                ->find($intIdFactura);
                                            $entityFacturaCabe->setEstadoImpresionFact("Eliminado");
                                            $emFinanciero->persist($entityFacturaCabe);
                                            $emFinanciero->flush();
                                            
                                            $entityInfoDocumentoHistorial = new InfoDocumentoHistorial();
                                            $entityInfoDocumentoHistorial->setDocumentoId($entityFacturaCabe);
                                            $entityInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                                            $entityInfoDocumentoHistorial->setUsrCreacion($strUsuario);
                                            $entityInfoDocumentoHistorial->setObservacion("Eliminado por proceso de clonacion de PreFactura");
                                            $entityInfoDocumentoHistorial->setEstado("Eliminado");
                                            $emFinanciero->persist($entityInfoDocumentoHistorial);
                                            $emFinanciero->flush();

                                            if($emFinanciero->getConnection()->isTransactionActive())
                                            {
                                                $emFinanciero->getConnection()->commit();
                                            }                                            
                                        }
                                    }
                                }
                            }
                            return $this->redirect( $this->generateUrl($strUrlRedirectShow, array('id' => $entityInfoDocumentoFinCab->getId())) );
                        }
                        catch(\Exception $e)
                        { 
                            $serviceUtil->insertError( 'Telcos+', 
                                                       'Facturación Manual', 
                                                       'Error al guardar la factura manual. '.$e->getMessage(), 
                                                       $strUsuario, 
                                                       $strIpCreacion );

                            if ($emFinanciero->getConnection()->isTransactionActive())
                            {
                                $emFinanciero->getConnection()->rollback();
                            }

                            if($emComercial->getConnection()->isTransactionActive())
                            {
                               $emComercial->getConnection()->rollback();
                            }

                            return $this->redirect($this->generateUrl($strUrlRedirectNew));
                        }//try

                        $emComercial->getConnection()->close();
                        $emFinanciero->getConnection()->close();

                    }// ( is_object($objNumeracion) )
                }//($formInfoDocFinCab->isValid())
            }//($intIdPunto)
        }
        else
        {
            $this->get('session')->getFlashBag()->add('notice', 'Ocurrio un error: ' . $arrayParametros["strMensajeValidacion"] . '!');
        }//($arrayParametros["boolPuedeFacturar"])

        
        return $this->redirect($this->generateUrl($strUrlRedirectNew));
    }

    /**
     * Documentación para el método 'getMensajesCompElectronicoAjaxAction'.
     * Obtiene el historial del comprobante electronico
     *
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 22-04-2014
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 09-02-2015
     */
    public function getMensajesCompElectronicoAction(){
        $objRequest                 = $this->getRequest();
        $intIdDocumento             = $objRequest->get('intIdDocumento');
        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');
        $intLimit                   = $objRequest->get("limit");
        $intStart                   = $objRequest->get("start");
        $arrayParametros = array('intIdDocumento' => $intIdDocumento, 'intStart' => $intStart, 'intLimit' => $intLimit);
        $arrayMensajesCompElec      = $serviceInfoCompElectronico->getMensajesCompElectronico($arrayParametros);
        $response = new Response(json_encode(array('storeMensajesCompElectronico'   => $arrayMensajesCompElec['arrayMensajes'], 
                                                   'intTotalMensajes'               => $arrayMensajesCompElec['intTotalMensajes'])));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }//getMensajesCompElectronicoAjaxAction

        /**
     * Documentación para el método 'getEnvioNotificacionAjaxAction'.
     * Envia notificacion al cliente del comprobante electronico
     *
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 22-04-2014
     */
    /**
     * @Secure(roles="ROLE_67-1777")
     */
    public function getEnvioNotificacionAjaxAction(){
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $arrayPtoCliente            = $objSession->get('ptoCliente');
        $intIdPunto                 = $arrayPtoCliente['id'];
        $intIdDocumento             = $objRequest->get('intIdDocumento');

        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');
        $boolVerficaEnvio     = $serviceInfoCompElectronico->getCompruebaEstadoComprobante($intIdDocumento, 5);
        if($boolVerficaEnvio == true){
            $arrayResult = $serviceInfoCompElectronico->enviaNotificacionClienteComprobante($intIdDocumento, $intIdPunto);
        }else{
            $arrayResult['boolStatus'] = false;
            $arrayResult['strMensaje'] = 'No puede realizar esta accion';
        }
        
        $objResponse = new Response(json_encode(array('boolStatus' => $arrayResult['boolStatus'], 
                                                      'strMensaje' => $arrayResult['strMensaje'])));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }//getEnvioNotificacionAjaxAction

    /**
     * Documentación para el método 'actualizaCompElectronicoAjaxAction'.
     * Actualiza el comprobante electronico que se encuentra con estado errado 0.
     *
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 22-04-2014
     * 
     * @author Ricardo Coello Quezada. <rcoello@telconet.ec>
     * @version 1.1 05-07-2017 
     * Actualización: Se agrega funcionalidad que permita consultar si el comprobante rechazado puede ser actualizado,
     * se agrega llamada a tabla de parametros donde se tendra configurado los estados para permitidos para las empresas TN y MD.
     */
    /**
     * @Secure(roles="ROLE_67-1778")
     */
    public function actualizaCompElectronicoAjaxAction()
    {
        $objRequest                 = $this->getRequest();
        $intIdDocumento             = $objRequest->get('intIdDocumento');
        $objSession                 = $objRequest->getSession();
        $intIdEmpresa               = $objSession->get('idEmpresa');
        $strPrefijoEmpresa          = $objSession->get('prefijoEmpresa');
        $strUserModificacion        = $objSession->get('user');
        $intTipoDocumento           = $objRequest->get('intTipoDocumentoId');
        $arrayParamSend             = array('IdDocumento' => $intIdDocumento,
                                        'IdEmpresa' => $intIdEmpresa,
                                        'IdTipoDocumento' => $intTipoDocumento,
                                        'UsrModificacion' => $strUserModificacion,
                                        'TipoTransaccion' => 'UPDATE');
        $strNombreParametro         = 'ESTADO_COMPROBANTE_RECHAZADO';
        $strModulo                  = 'FINANCIERO';
        $strProceso                 = 'FACTURACION';
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');
        $arrayAdmiParametroDet      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get($strNombreParametro, 
                                                      $strModulo, 
                                                      $strProceso, 
                                                      '',
                                                      '',
                                                      '',
                                                      '',
                                                      '',
                                                      $strPrefijoEmpresa,
                                                      $intIdEmpresa);
		
		if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
		{
		    $arrayEstados = array();
		    foreach($arrayAdmiParametroDet as $arrayParametro)
		    {
		        $arrayEstados[] = $arrayParametro['valor1'];
		    }//( $arrayAdmiParametroDet as $arrayParametro )
		}//( $arrayAdmiParametroDet )
        
        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');
        $boolVerificaConError       = $serviceInfoCompElectronico->getCompruebaEstadoComprobante($intIdDocumento, 0);
        $boolVerificaRechazada      = $serviceInfoCompElectronico->getVerificaComprobanteByEstado($intIdDocumento, $arrayEstados);
        //Volvemos a preguntar si ese comprobante se puede actualizar, en prevencion a que nos hayan hecho una inyeccion en el js
        if($boolVerificaConError != true && $boolVerificaRechazada != true)
        {
            $strMensaje = 'No se puede realizar esta accion.';
            $boolConfirmacion = false;
        }
        else
        {
            //Si MessageError trae algo quiere decir que existio un error en la actualizacion
            $arrayActualizaComprobante  = $serviceInfoCompElectronico->actualizaComprobanteElectronico($arrayParamSend);
            $boolConfirmacion           = $arrayActualizaComprobante['boolCheck'];
            $strMensaje                 = $arrayActualizaComprobante['Message'];
        }
        $response = new Response(json_encode(array('boolConfirmacion' => $boolConfirmacion, 'strMensaje' => $strMensaje)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }//actualizaComprobanteElectronicoAction

    /**
     * Documentación para el método 'descargarComprobanteAction'.
     * Este metodo obtiene los documentos PDF y XML de los comprobantes electronicos
     * y los exporta para que puedan ser descargados
     *
     * @param  Integer $intIdDocumento Recibe el ID del Documento
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 22-04-2014
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 09-04-2018 Se setea valor para tiempo de timeout.
     */
    /**
     * @Secure(roles="ROLE_67-1837")
     */
    public function descargarComprobanteAction()
    {
        ini_set('max_execution_time', 400000);
        $objRequest                     = $this->getRequest();
        $strNombre                      = $objRequest->get('strNombre');
        $strExtension                   = $objRequest->get('strExtension');
        $intIdDocumento                 = $objRequest->get('intIdDocumento');
        $serviceInfoCompElectronico     = $this->get('financiero.InfoCompElectronico');
        $arrayDocumentosElectronicos    = $serviceInfoCompElectronico->getCompElectronicosPdfXml($intIdDocumento);

        $objResponse = new Response();

        $objResponse->headers->set('Content-Type', 'mime/type');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="'.$strNombre.'.'.$strExtension);
        $objResponse->setContent($arrayDocumentosElectronicos[$strExtension]);
        return $objResponse;
    }//descargarComprobanteAction

    /**
     * simularCompElectronicoAction, permite simular un comprabante electronico sin guardarlo en la base
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28-06-2016
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_67-1837")
     */
    public function simularCompElectronicoAction()
    {
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $intIdDocumento             = $objRequest->get('intIdDocumento');
        $intIdTipoDocumento         = $objRequest->get('intIdTipoDocumento');
        $intIdEmpresa               = $objSession->get('idEmpresa');
        $strUsuario                 = $objSession->get('user');
        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');

        $arrayParametros                        = array();
        $arrayParametros['intIdDocumento']      = $intIdDocumento;
        $arrayParametros['intIdEmpresa']        = $intIdEmpresa;
        $arrayParametros['intIdTipoDocumento']  = $intIdTipoDocumento;
        $arrayParametros['strUsuario']          = $strUsuario;
        $arrayParametros['strTipoTransaccion']  = 'NINGUNA';

        $objReturnResponse = $serviceInfoCompElectronico->transaccionComprobanteElectronico($arrayParametros);
        $arrayDocumentoXML = $objReturnResponse->getRegistros();
        $objResponse = new Response();

        $objResponse->headers->set('Content-Type', 'mime/type');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="' . 'PRUEBA-' . $arrayDocumentoXML['strNombreComprobante'] . '.xml');
        $objResponse->setContent($arrayDocumentoXML['clobComprobanteElectronico']);
        return $objResponse;
    }//simularCompElectronicoAction

    /**
     * Displays a form to edit an existing InfoDocumentoFinancieroCab entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_financiero");

        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
        }

        $editForm = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        //tomar el punto de la session
        $punto = "28";

        return $this->render('financieroBundle:InfoDocumentoFinancieroCab:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'punto_id' => $punto
        ));
    }

    /**
     * Edits an existing InfoDocumentoFinancieroCab entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $informacionGrid = $request->get('listado_informacion');
        $informacionGrid = json_decode($informacionGrid);

        $punto_id = $request->get('punto_id');
        $empresa_id = "10";
        $oficina_id = "2";
        $estado = "Activo";

        $em = $this->getDoctrine()->getManager("telconet_financiero");

        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $editForm->bind($request);

        if($editForm->isValid())
        {
            $em->persist($entity);
            $em->flush();

            //Guardando el detalle
            if($informacionGrid)
            {
                //busqueda de la persona
                $em_comercial = $this->getDoctrine()->getManager("telconet");
                $persona = $em_comercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin('gvillalba');

                foreach($informacionGrid as $info)
                {
                    $entitydet = new InfoDocumentoFinancieroDet();

                    if($info->tipo == 'PR')
                    {
                        //$informacionCodigo=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find();
                        $entitydet->setProductoId($info->codigo);
                    }
                    if($info->tipo == 'PL')
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
                    $entitydet->setUsrCreacion('gvillalba');
                    $em->persist($entitydet);
                    $em->flush();
                }
            }
            return $this->redirect($this->generateUrl('infodocumentofinancierocab_edit', array('id' => $id)));
        }

        return $this->render('financieroBundle:InfoDocumentoFinancieroCab:edit.html.twig', array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
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

        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager("telconet_financiero");
            $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

            if(!$entity)
            {
                throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
            }

            $entity->setEstadoImpresionFact("Anulado");
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
     * Metodo infoOrdenesPtoClienteAction, muestra la informacion del grid al crear una nueva factura
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 06-10-2014
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 24-05-2016 - Se adapta la función para obtener los valores de los productos y/o planes al porcentaje del '14%'
     * @since 1.1
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.3 21-06-2016 - Se modifica la presentacion de la descripcion de los items de la factura por empresa'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 - Se adapta la función para obtener los valores de los productos y/o planes dependiendo del porcentaje del IVA elegido por el 
     *                usuario, en caso de no elegir ningún impuesto se calculará todo al impuesto del IVA en estado 'Activo'
     * @since 20-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 22-06-2016 - Se redondea los impuestos por cada producto
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 23-06-2016 - Se quita el redondeo en los impuestos porque no se debe realizar dicho proceso por cada producto.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 22-06-2016 - Se realiza cálculo de impuesto ICE
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 30-06-2016 - Se verifica si la consulta de los servicios a facturar es con o sin Frecuencia
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.9 13-09-2016 - Se verifica si el cliente debe ser compensado mediante la variable '$strEsCompensado' y se obtiene los valores 
     *                           correspondientes para ser mostrados en la factura del cliente
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.0 04-10-2016 - Se corrige que se envíe la variable '$arrayDetalleListadoOrden' vacía cuando no existen servicios asociados al punto
     *                           de facturación, para evitar que se realizar una factura sin detalles.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.1 09-02-2017 - Se realiza la modificación en la función para que MD compensen al 2% las facturas realizadas al 14% de los clientes
     *                           que pertenecen a los cantones MANTA y PORTOVIEJO.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 2.2 31-03-2017 - Se realiza modificación para el ingreso de un nuevo detalle a la factura por cargo de reproceso de débito.
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 2.3 20-06-2017 - Se agrega envío de parámetro porcentaje impuesto a ser utilizado en edición de precio en detalles de facturas.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 2.4 10-07-2017 - Se agrega envío de parámetro porcentaje impuesto ICE a ser utilizado en edición de precio en detalles de facturas.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 2.5 13-07-2017 - Se modifica la función para adaptar la obtención y cálculo de los impuestos por país, tanto para planes como para
     *                           productos
     *                           Se unifican las funciones de obtener las ordenes a facturar en los documentod de Facturas (FAC) y las Facturas
     *                           Proporcionales (FACP)
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 2.6 16-11-2017 - Se modifica la lógica que trae los impuestos del producto. Así sólo son cargados una vez por producto.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 3.7 28-02-2019 - Se modifica impuestos para que tome la configuración de Guatemala.
     * 
     * @author Jesus Banchen <jbanchen@telconet.ec>
     * @version 3.8 21-08-2019 - Se agrega una nueva consulta para fact-Manuales- la combinacion de 
     *  consulta prepagada sin frecuencia con prepagana normal.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 3.9  18-09-2020 - Debido a los cambios tributarios estipulados por el ente regulador SRI-servicios digitales(productos tarifa 0% IVA)
     *                            Se modifica en Facturación Manual y Proporcional para el tipo de Orden de Facturación :
     *                            -  Precargada Agrupada
     *                            -  Precargada Agrupada de Precargada Sin Frecuencia y Precargada Normal
     *                            Que se precarguen 2 detalles segmentando los servicios y agrupando los servicios que pagan iva en un único detalle
     *                            , y los que no pagan iva en otro detalle independiente, lo cual va a permitir que los documentos electrónicos sean
     *                            autorizados de forma correcta por el SRI.
     *
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 4.0 29-01-2021 - Se agrega envío de parámetro login a ser utilizado en la vista de detalles de facturas.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 4.1 09-02-2021 - Se agrega envio de detalles de factura padre si la peticion es por clonacion.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 4.2 29-04-2022 - Se modifica envio de detalles de factura padre si la peticion es por clonacion de facturas o prefacturas.
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 4.3 16-08-2022 - Se cambia la busqueda principal por producto, luego por servicio.
     * 
     */
    public function infoOrdenesPtoClienteAction()
    {        
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $arrayPtoCliente    = $objSession->get('ptoCliente');
        $intCodEmpresa      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $intIdPaisSession   = $objSession->get('intIdPais');
        $strPaisSession     = $objSession->get('strNombrePais');
        $em                 = $this->get('doctrine')->getManager('telconet');
        $emGeneral          = $this->get('doctrine')->getManager('telconet_general');
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');

        $objInformacionGrid     = $objRequest->get('informacionGrid');
        $objInformacionDataGrid = json_decode($objInformacionGrid);
        $strPagaIva             = $objRequest->get("strPagaIva");
        $strPagaIce             = $objRequest->get('strPagaIce');
        $intIdImpuesto          = $objRequest->get('intImpuestoId');
        $strSinFrecuencia       = $objRequest->get('strSinFrecuencia') ? $objRequest->get('strSinFrecuencia') : 'N';
        $strEsCompensado        = $objRequest->get('strEsCompensado') ? $objRequest->get('strEsCompensado') : 'NO';
        $strTipoFacturacion     = $objRequest->get('strTipoFacturacion') ? $objRequest->get('strTipoFacturacion') : 'normal';        
        $intCantidadDias        = 0;
        $intDiasTotales         = 0;        
        $boolFactAgrupada       = $objRequest->get('boolFacturacionAgrupada');
       
        if ( $strTipoFacturacion == "proporcional" )
        {
            $arrayFechaDesde        = explode('T',$objRequest->get("fechaDesde"));
            $arrayFechaHasta        = explode('T',$objRequest->get("fechaHasta"));

            $serviceInfoDocumentoFinancieroCab = $this->get('financiero.InfoDocumentoFinancieroCab');
            if($objRequest->get('clonar')!="S")
            {
                $arrayParametrosRestaFechas        = array('strFechaInicio' => $arrayFechaDesde[0], 'strFechaFin' => $arrayFechaHasta[0]);
                $arrayResultadosRestaFechas        = $serviceInfoDocumentoFinancieroCab->restarFechas( $arrayParametrosRestaFechas );
    
                if ( isset($arrayResultadosRestaFechas['intCantidadDiasEntreFechas']) 
                        && $arrayResultadosRestaFechas['intCantidadDiasEntreFechas'] > 0 )
                {
                    $intCantidadDias = $arrayResultadosRestaFechas['intCantidadDiasEntreFechas'];
                }
                /**
                 * Bloque que obtiene los días del mes en curso entre las fechas de facturación
                 */
                $arrayParametrosFechaProporcional = array('strEmpresaCod'      => $intCodEmpresa,
                                                          'strFechaActivacion' => $arrayFechaDesde[0]);
                $arrayResultadoDiasRestantes      = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                ->getFechasDiasPeriodo($arrayParametrosFechaProporcional);

                if ( isset($arrayResultadoDiasRestantes['intTotalDiasMes']) && intval($arrayResultadoDiasRestantes['intTotalDiasMes']) > 0 )
                {
                    $intDiasTotales = intval($arrayResultadoDiasRestantes['intTotalDiasMes']);
                }// ( isset($arrayResultadoDiasRestantes['intTotalDiasMes']) && intval($arrayResultadoDiasRestantes['intTotalDiasMes']) > 0 )
            }
        }// ( $strTipoFacturacion == "proporcional" )
        else
        {
            $arrayFechaDesde[0] = null;
            $arrayFechaHasta[0] = null;
        }


        //VARIABLE QUE VERIFICA SI LA EMPRESA EN SESSION TIENE HABILITADA LA OPCION DE PRECARGADA SIN FRECUENCIA
        $strOpcionPrecargadaSinFrecuencia = $objRequest->get('strOpcionPrecargadaSinFrecuencia')
                                            ? $objRequest->get('strOpcionPrecargadaSinFrecuencia') : 'N';

        $strEstado                = "Activo";
        $arrayDetalleListadoOrden = array();
        $arrayListadoOrden        = array();
        $strTieneCargoReproceso   = "N";
        $intCantidadSolReproceso  = 0;
        $floatPrecioReproceso     = 0;
        $floatPorcentajeImpIce    = 0;
       
        /**
         * Bloque que obtiene el porcentaje del impuesto de COMPENSACION
         */
        $objAdmiImpuestoCompensacion = null;
        
        if( $strEsCompensado == "SI" )
        {
            $objAdmiImpuestoCompensacion = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                     ->findOneBy(array('tipoImpuesto' => 'COM', 'estado' => 'Activo'));
        }

         $strTipoImpuestoIce = 'ICE';

        if ( strtoupper($strPaisSession) != 'ECUADOR' )
        {
            $strTipoImpuestoIce = 'IEC';
        }

        $objAdmiPais = $emGeneral->getRepository("schemaBundle:AdmiPais")->findOneById($intIdPaisSession);
        
        if ( is_object($objAdmiPais) )
        {
            $objAdmiImpuestoIce = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                            ->findOneBy( array('tipoImpuesto' => $strTipoImpuestoIce, 
                                                               'estado'       => 'Activo',
                                                               'paisId'       => $objAdmiPais) );
        }

        if ( is_object($objAdmiImpuestoIce) )
        {
            $floatPorcentajeImpIce = $objAdmiImpuestoIce->getPorcentajeImpuesto();
        }


        //Si la peticion es por clonacion, devolvemos los detalles de la prefactura padre
        if($objRequest->get('clonar')=="S")
        {
            $intFact = $objRequest->get('strIdFactura');
            $entityInfoDocumentoFinancieroCab    = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                ->findOneBy(array("id"=>$intFact) );
            if(!is_null($intFact) && $intFact != "")
            {
                $arrayInfoDocumentoFinancieroDet    = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                ->findBy(array("documentoId"=>$intFact) );
            
            
                $arrayDet                           = array();
                $floatImpuestoIceAcumulado          = 0;
                
                $entityIce = $emFinanciero->getRepository("schemaBundle:AdmiImpuesto")
                                            ->findOneBy(array("descripcionImpuesto"=>"ICE 15%") );
                $entityIvaDoce = $emFinanciero->getRepository("schemaBundle:AdmiImpuesto")
                                            ->findOneBy(array("descripcionImpuesto"=>"IVA 12%") );
                $entityIvaCatorce = $emFinanciero->getRepository("schemaBundle:AdmiImpuesto")
                                            ->findOneBy(array("descripcionImpuesto"=>"IVA 14%") );
                foreach ($arrayInfoDocumentoFinancieroDet as $entityInfoDocumentoFinancieroDet)
                {
                    
                    $entityInfoDocumentoImpuestoIce = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroImp")->findOneBy(
                                                                    array("detalleDocId"=>$entityInfoDocumentoFinancieroDet->getId(),
                                                                    "impuestoId"=>$entityIce->getId() )
                                                                    );
                    $entityInfoDocumentoImpuestoIvaDoce = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                ->findOneBy(
                                                                    array("detalleDocId"=>$entityInfoDocumentoFinancieroDet->getId(), 
                                                                    "impuestoId"=>$entityIvaDoce->getId() ) );
                    $entityInfoDocumentoImpuestoIvaCatorce = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                ->findOneBy(
                                                                    array("detalleDocId"=>$entityInfoDocumentoFinancieroDet->getId(), 
                                                                    "impuestoId"=>$entityIvaCatorce->getId() ) );
                    
                    $entityInfoDocumentoImpuestoIva = $entityInfoDocumentoImpuestoIvaDoce;
                    if($entityInfoDocumentoFinancieroDet->getProductoId())
                    {
                        $entityProducto = $em->getRepository("schemaBundle:AdmiProducto")
                                                ->find($entityInfoDocumentoFinancieroDet->getProductoId());
                        $arrayDet ["codigo"] = $entityInfoDocumentoFinancieroDet->getProductoId();
                        if($entityProducto)
                        {
                            $arrayDet["informacion"] = $entityProducto->getDescripcionProducto();
                        }
                        else
                        {
                            $arrayDet["informacion"] = "";
                        }
                        $arrayDet ["tipo"] = "PR";
                        $arrayDet ["tipoOrden"] = "PRE";
                    }
                    else if ($entityInfoDocumentoFinancieroDet->getPlanId())
                    {
                        $arrayDet ["codigo"] = $entityInfoDocumentoFinancieroDet->getPlanId();
                        $entityPlanCab = $em->getRepository("schemaBundle:InfoPlanCab")->find($entityServicio->getPlanId());
                        if($entityPlanCab)
                        {
                            $arrayDet ["informacion"] = $entityPlanCab->getDescripcionPlan();
                        }
                        else
                        {
                            $arrayDet ["informacion"] = "";
                        }
                        $arrayDet ["tipo"] = "PL";
                        $arrayDet ["tipoOrden"] = "PRE";
                    }
                    if($entityInfoDocumentoFinancieroDet->getServicioId())
                    {
                        $entityServicio = $em->getRepository('schemaBundle:InfoServicio')
                                                                ->find($entityInfoDocumentoFinancieroDet->getServicioId());                        
                        if($entityServicio)
                        {
                            $arrayDet ["idServicio"] = $entityServicio->getId();
                            $arrayServicioHistorial       = $em->getRepository('schemaBundle:InfoServicio')->findHistorial($entityServicio->getId());
                            if($arrayServicioHistorial['feCreacion'] != null)
                            {
                                $arrayDet['fechaActivacion'] = $arrayServicioHistorial['feCreacion'];
                            }
                        }
                    }

                    if($entityInfoDocumentoFinancieroDet->getObservacionesFacturaDetalle())
                    {
                        $strDescripcionDetalle = $entityInfoDocumentoFinancieroDet->getObservacionesFacturaDetalle();
                        $arrayDet ["descripcion"] = $strDescripcionDetalle;
                    }
                    
                    $entityPunto = $em->getRepository("schemaBundle:InfoPunto")->find($entityInfoDocumentoFinancieroDet->getPuntoId());
                    if($entityPunto)
                    {
                        $arrayDet ["login"] = $entityPunto->getLogin();
                        $arrayDet ["puntoId"] = $entityPunto->getId();    
                    }
                    
                    if($entityInfoDocumentoImpuestoIva)
                    {
                        $arrayDet ["impuestoIva"]   = $entityInfoDocumentoImpuestoIva
                                                            ->getValorImpuesto()/$entityInfoDocumentoFinancieroDet->getCantidad();
                        $arrayDet ["impuesto"]      = $entityInfoDocumentoImpuestoIva
                                                            ->getValorImpuesto()/$entityInfoDocumentoFinancieroDet->getCantidad();
                    }
                    else if ($entityInfoDocumentoImpuestoIvaCatorce)
                    {
                        $intIva12 = $entityIvaDoce->getPorcentajeImpuesto()/100;
                        $arrayDet ["impuestoIva"]   = $entityInfoDocumentoFinancieroDet->getPrecioVentaFacproDetalle()*$intIva12;
                        $arrayDet ["impuesto"]      = $entityInfoDocumentoFinancieroDet->getPrecioVentaFacproDetalle()*$intIva12;
                    }
                    else
                    {
                        $arrayDet ["impuestoIva"] = 0;
                        $arrayDet['impuesto'] = 0;
                    }
                    if($entityInfoDocumentoImpuestoIce)
                    {
                        if($entityInfoDocumentoFinancieroDet->getProductoId())
                        {
                            $arrayEntityProductoImpuesto = $em->getRepository("schemaBundle:InfoProductoImpuesto")
                                                                ->findBy(array("productoId"=>$entityInfoDocumentoFinancieroDet->getProductoId()));
                            foreach($arrayEntityProductoImpuesto as $entityProductoImpuesto)
                            {
                                if($entityProductoImpuesto && $entityProductoImpuesto->getImpuestoId()->getId()==$entityIce->getId())
                                {
                                    {
                                        $arrayDet ["impuestoIce"] = $entityInfoDocumentoImpuestoIce->getValorImpuesto();
                                    }
                                }
                            }
                        }
                        else
                        {
                            $arrayDet ["impuestoIce"] = 0;
                        }
                    }
                    else
                    {
                        $arrayDet ["impuestoIce"] = 0;
                    }
                    
                    $arrayDet ["precio"] = $entityInfoDocumentoFinancieroDet->getPrecioVentaFacproDetalle(); //PrecioProporcional en facp
                    $arrayDet ["precio_uni"] = $entityInfoDocumentoFinancieroDet->getPrecioVentaFacproDetalle();//PVP en proporcional
                    if($strTipoFacturacion == "proporcional" &&
                        !is_null($entityInfoDocumentoFinancieroDet->getProductoId()) && 
                        !is_null($entityInfoDocumentoFinancieroDet->getServicioId()))
                    {
                        $entityServicio = $em->getRepository('schemaBundle:InfoServicio')
                                                ->findOneBy(array("productoId"=>$entityInfoDocumentoFinancieroDet->getProductoId(),
                                                                    "id"=>$entityInfoDocumentoFinancieroDet->getServicioId() )  );
                        
                        if($entityServicio)
                        {
                            $arrayDet ["precio_uni"] = $entityServicio->getPrecioVenta();//Precio total de venta
                        }
                    }
                    $arrayDet ["preciototal"] = $entityInfoDocumentoFinancieroDet->getPrecioVentaFacproDetalle();
                    $arrayDet ["cantidad"] = $entityInfoDocumentoFinancieroDet->getCantidad();
                    
                    $arrayDet ["descuento"] = $entityInfoDocumentoFinancieroDet->getDescuentoFacproDetalle();
                    
                    $arrayDet ["compensacionSolidaria"] = 0; //Sin compensacion
                    $arrayDet ["impuestoOtros"] = 0;
                    
                    $arrayDet ["fechaDesde"] = null;//En propoorcionales no manejo de Desde-Hasta
                    $arrayDet ["fechaHasta"] = null;
                    
                    $arrayDetalleListadoOrden2 [] = $arrayDet;

                    $strTipoImpuesto       = 'IVA';

                    if ( is_object($objAdmiPais) )
                    {
                        if ( strtoupper($strPaisSession) != 'ECUADOR' && strtoupper($strPaisSession) != 'GUATEMALA' )
                        {
                            $strTipoImpuesto = 'ITBMS';
                        }
                        if ( strtoupper($strPaisSession) == 'GUATEMALA' )
                        {
                            $strTipoImpuesto = 'IVA_GT';
                        }
        
                        $entityAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                        ->findOneBy( array('tipoImpuesto' => $strTipoImpuesto, 
                                                                            'estado'       => 'Activo',
                                                                            'paisId'       => $objAdmiPais) );
                    
                        $arrayDet ["porcentajeImpuesto"] = $entityAdmiImpuestoIva->getPorcentajeImpuesto();
                    }
                    $arrayDet ["impuesto"] = 1;
                }//fINDE FORACHE
                
                $floatTotalOtrosImpuestos = floatval($entityInfoDocumentoFinancieroCab->getSubtotalConImpuesto());
            }
        }
        //Si los detalles los requirieron por clonacion, devolvemos el listado de detalles de la prefactura padre
        if($objRequest->get('clonar')=="S")
        {
            $objResponse = new Response(json_encode(array('listadoInformacion' => $arrayDetalleListadoOrden2)));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }

        /*
         * Bloque que verifica si se seleccionó la opción de precargar los servicios SIN FRENCUENCIA.
         * SIN FRECUENCIA quiere decir,  el campo FRECUENCIA_PRODUCTO de la tabla DB_COMERCIAL.INFO_SERVICIO en NULL o cero
         */
        if ( $strOpcionPrecargadaSinFrecuencia == "S" )
        {
            if( $strSinFrecuencia == "S" )
            {  
                $strFrecuencia      = 'igualACero';
                $arrayInfoServicio  = $em->getRepository('schemaBundle:InfoServicio')
                                         ->findServiciosPorEmpresaPorPuntoPorEstado( $intCodEmpresa, 
                                                                                     $arrayPtoCliente['id'], 
                                                                                     $strEstado,
                                                                                     $strFrecuencia);
            }
            else
            {
                if ($strSinFrecuencia == "SNEW")
                {
                    $strFrecuencia = 'mayorIgualQue0';
                    $arrayInfoServicio  = $em->getRepository('schemaBundle:InfoServicio')
                                         ->findServiciosPorEmpresaPorPuntoPorEstado( $intCodEmpresa, 
                                                                                     $arrayPtoCliente['id'], 
                                                                                     $strEstado,
                                                                                     $strFrecuencia);
                }
                else
                {
                    $strFrecuencia = 'mayorIgualQue1';
                    $arrayInfoServicio  = $em->getRepository('schemaBundle:InfoServicio')
                                         ->findServiciosPorEmpresaPorPuntoPorEstado( $intCodEmpresa, 
                                                                                     $arrayPtoCliente['id'], 
                                                                                     $strEstado,
                                                                                     $strFrecuencia);
                }
            }//( $strSinFrecuencia == "S" )
        }
        else
        { 
            $strFrecuencia      = 'normal';
            $arrayInfoServicio  = $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPuntoPorEstado( $intCodEmpresa,
                                                                                                                             $arrayPtoCliente['id'],
                                                                                                                             $strEstado );
        }// ( $strOpcionPrecargadaSinFrecuencia == "S" )


        if( isset($arrayInfoServicio['registros']) && !empty($arrayInfoServicio['registros']) )
        {
            $arrayListadoOrden = $arrayInfoServicio['registros'];
        }

        if( !empty($arrayListadoOrden) )
        {
            $entityAdmiImpuestoIva = null;
            $strTipoImpuesto       = 'IVA';

            if ( is_object($objAdmiPais) )
            {
                if ( strtoupper($strPaisSession) != 'ECUADOR' && strtoupper($strPaisSession) != 'GUATEMALA' )
                {
                    $strTipoImpuesto = 'ITBMS';
                }
                 if ( strtoupper($strPaisSession) == 'GUATEMALA' )
                {
                    $strTipoImpuesto = 'IVA_GT';
                }

                $entityAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                   ->findOneBy( array('tipoImpuesto' => $strTipoImpuesto, 
                                                                      'estado'       => 'Activo',
                                                                      'paisId'       => $objAdmiPais) );
            }

            // Se verifica si existe solicitud de cargo por reproceso, consultando por id del punto.
            if( $strPrefijoEmpresa == "MD" )
            {
                $objAdmiParametroCabReproceso  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                           ->findOneBy( array('nombreParametro' => 'CARGO REPROCESO DEBITO', 
                                                                              'estado'          => 'Activo') );
                if(is_object($objAdmiParametroCabReproceso))
                {              

                    $arraySolicitudesReproceso = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->getSolicitudPorPunto($arrayPtoCliente['id'], 
                                                                         'SOLICITUD CARGO REPROCESO DEBITO', 
                                                                         'Pendiente');                   

                    if(count($arraySolicitudesReproceso)>0)
                    {
                        $strTieneCargoReproceso   = "S";
                        
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

            /**
             * Obtengo los productos para ese cliente y posteriormente sus impuestos, almacenándolos en un arreglo.
             */
            $arrayParametros    = array('intIdPunto'        => $arrayPtoCliente['id'],
                                        'strEmpresaCod'     => strval($intCodEmpresa),
                                        'strEstado'         => $strEstado,
                                        'strTipoFrecuencia' => $strFrecuencia);  
            $arrayListProductos = $em->getRepository('schemaBundle:InfoServicio')->findProductoPorEmpresaPorPuntoPorEstado($arrayParametros);
            $arrayProductos     = array();
            foreach($arrayListProductos as $objProducto)
            {
                $arrayParametrosImpuestosPrioridad1 = array('intIdProducto' => $objProducto->getId(),
                                                           'strEstado'      => 'Activo',
                                                           'intIdPais'      => $intIdPaisSession,
                                                           'intPrioridad'   => 1);
                $objProductoImpuesto1 = $em->getRepository('schemaBundle:InfoProductoImpuesto')
                                           ->getInfoImpuestoByCriterios($arrayParametrosImpuestosPrioridad1);
                $arrayParametrosImpuestosPrioridad2 = array('intIdProducto' => $objProducto->getId(),
                                                           'strEstado'      => 'Activo',
                                                           'intIdPais'      => $intIdPaisSession,
                                                           'intPrioridad'   => 2);
                $objProductoImpuesto2 = $em->getRepository('schemaBundle:InfoProductoImpuesto')
                                           ->getInfoImpuestoByCriterios($arrayParametrosImpuestosPrioridad2);
                $arrayProductos[$objProducto->getId()][1] = $objProductoImpuesto1['registros'];
                $arrayProductos[$objProducto->getId()][2] = $objProductoImpuesto2['registros'];
            }

            $arrayListImpuestos = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')->findAll();
            $arrayImpuestos     = array();
            foreach($arrayListImpuestos as $objImpuesto)
            {
                $arrayImpuestos[$objImpuesto->getId()] = $objImpuesto;
            }

            foreach($arrayListadoOrden as $objListadoOrden)
            {
                $floatImpuestoAcumulado       = 0;
                $floatImpuestoIvaAcumulado    = 0;
                $floatImpuestoIceAcumulado    = 0;
                $floatOtrosImpuestoAcumulado  = 0;
                $floatDescuento               = 0;
                $floatCompensacionSolidaria   = 0;
                $floatDescuentoUnitario       = 0;
                $arrayDatosListadoOrden       = array();
                $arrayServicioHistorial       = $em->getRepository('schemaBundle:InfoServicio')->findHistorial($objListadoOrden->getId());
                $floatPrecioVenta             = $objListadoOrden->getPrecioVenta() ? $objListadoOrden->getPrecioVenta() : 0;
                $floatPrecioVentaProporcional = $floatPrecioVenta;

                $arrayDatosListadoOrden['porcentajeImpuestoIce'] = $floatPorcentajeImpIce;

                
                if ( $strTipoFacturacion == "proporcional" )
                {
                    $floatPrecioVentaProporcional = ( $floatPrecioVenta * $intCantidadDias ) / $intDiasTotales;
                }// ( $strTipoFacturacion == "proporcional" )

                if ( $objListadoOrden->getPorcentajeDescuento() )
                {
                    $floatDescuento = round( (($floatPrecioVentaProporcional * $objListadoOrden->getCantidad() 
                                                * $objListadoOrden->getPorcentajeDescuento())/100), 2 );
                }// ( $objListadoOrden->getPorcentajeDescuento() )
                elseif ( $objListadoOrden->getValorDescuento() )
                {
                    $floatDescuento         = $objListadoOrden->getValorDescuento();
                    $floatDescuentoUnitario = $floatDescuento / $objListadoOrden->getCantidad();
                }// ( $objListadoOrden->getValorDescuento() )


                /**
                 * Bloque que calcula el descuento proporcional para la factura
                 */
                if ( $strTipoFacturacion == "proporcional" )
                {
                    $floatDescuento         = ( ( $floatDescuento * $intCantidadDias ) / $intDiasTotales );
                    $floatDescuento         = round($floatDescuento, 2);
                    $floatDescuentoUnitario = ( ( $floatDescuentoUnitario * $intCantidadDias ) / $intDiasTotales );
                    $floatDescuentoUnitario = round($floatDescuentoUnitario, 2);
                }// ( $strTipoFacturacion == "proporcional" )
                $arrayDatosListadoOrden['idServicio']            = $objListadoOrden->getId();
                $arrayDatosListadoOrden['login']                 = $objListadoOrden->getPuntoId()->getLogin();
                $arrayDatosListadoOrden['precio']                = round($floatPrecioVentaProporcional, 2);
                $arrayDatosListadoOrden['precio_uni']            = round($floatPrecioVenta, 2);
                $arrayDatosListadoOrden['preciototal']           = $floatPrecioVenta;
                $arrayDatosListadoOrden['cantidad']              = $objListadoOrden->getCantidad();
                $arrayDatosListadoOrden['descuento']             = $floatDescuento;
                $arrayDatosListadoOrden['impuesto']              = $floatImpuestoAcumulado;
                $arrayDatosListadoOrden['impuestoIva']           = $floatImpuestoIvaAcumulado;
                $arrayDatosListadoOrden['impuestoIce']           = $floatImpuestoIceAcumulado;
                $arrayDatosListadoOrden['impuestoOtros']         = $floatOtrosImpuestoAcumulado;
                $arrayDatosListadoOrden['fechaActivacion']       = "";
                $arrayDatosListadoOrden['compensacionSolidaria'] = $floatCompensacionSolidaria;

                if($objListadoOrden->getProductoId())
                {
                    $arrayDatosListadoOrden['codigo']       = $objListadoOrden->getProductoId()->getId();
                    $arrayDatosListadoOrden['informacion']  = $objListadoOrden->getProductoId()->getDescripcionProducto();
                    $arrayDatosListadoOrden['tipo']         = 'PR';
                    $arrayDatosListadoOrden['tipoOrden']    = 'PRE';
                    $objInfoProductoImpuestosPrioridad1     = $arrayProductos[$objListadoOrden->getProductoId()->getId()][1];
                    if($objInfoProductoImpuestosPrioridad1)
                    {
                        foreach($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImpuesto)
                        {
                            $floatTmpImpuesto = 0;
                            $boolImpuesto     = true;
                            $objAdmiImpuesto  = $arrayImpuestos[$objInfoProductoImpuesto->getImpuestoId()->getId()];

                            if($objAdmiImpuesto)
                            {
                                if ( $strPagaIce != "SI" && ( $objAdmiImpuesto->getTipoImpuesto() == 'ICE'
                                                             || $objAdmiImpuesto->getTipoImpuesto() == 'IEC' ) )
                                {
                                    $boolImpuesto = false;
                                }

                                if($boolImpuesto)
                                {
                                    $floatTmpImpuesto = ( ($arrayDatosListadoOrden['precio'] - $floatDescuentoUnitario) 
                                                           * $objAdmiImpuesto->getPorcentajeImpuesto() )/100;

                                    if ( $objAdmiImpuesto->getTipoImpuesto() == 'ICE' || $objAdmiImpuesto->getTipoImpuesto() == 'IEC' )
                                    {
                                        $floatImpuestoIceAcumulado += $floatTmpImpuesto;
                                    }
                                    else
                                    {
                                        $floatOtrosImpuestoAcumulado += $floatTmpImpuesto;
                                    }// ( $objAdmiImpuesto->getTipoImpuesto() == 'ICE' || $objAdmiImpuesto->getTipoImpuesto() == 'IEC' )

                                    $floatImpuestoAcumulado += $floatTmpImpuesto;
                                }//($boolImpuesto)
                            }//($objAdmiImpuesto)
                        }//($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImpuesto)

                        $arrayDatosListadoOrden['impuesto']      = $floatImpuestoAcumulado;
                        $arrayDatosListadoOrden['impuestoIce']   = $floatImpuestoIceAcumulado;
                        $arrayDatosListadoOrden['impuestoOtros'] = $floatOtrosImpuestoAcumulado;
                    }//($objInfoProductoImpuestoPrioridad1)
                    //PRIORIDAD 2
                    $objInfoProductoImpuestosPrioridad2 = $arrayProductos[$objListadoOrden->getProductoId()->getId()][2];
                    if($objInfoProductoImpuestosPrioridad2)
                    {
                        foreach($objInfoProductoImpuestosPrioridad2 as $objInfoProductoImpuesto)
                        {
                            $floatTmpImpuesto = 0;
                            $boolImpuesto     = true;
                            $objAdmiImpuesto  = $arrayImpuestos[$objInfoProductoImpuesto->getImpuestoId()->getId()];

                            if($objAdmiImpuesto)
                            {
                                if( $strPagaIva != "S" && ( $objAdmiImpuesto->getTipoImpuesto() == 'IVA'
                                                            || $objAdmiImpuesto->getTipoImpuesto() == 'ITBMS' ) )
                                {
                                    $boolImpuesto = false;
                                }//( $strPagaIva != "S" && ( $objAdmiImpuesto->getTipoImpuesto() == 'IVA'...

                                if($boolImpuesto)
                                {
                                    /*
                                     * Se verifica si el usuario seleccionó algún impuesto IVA para crear la factura para darle prioridad con el impuesto
                                     * seleccionado.
                                     */
                                    if ( $intIdImpuesto && ( $objAdmiImpuesto->getTipoImpuesto() == 'IVA' 
                                                             || $objAdmiImpuesto->getTipoImpuesto() == 'ITBMS' ) )
                                    {
                                        $objAdmiImpuestoSelected = $arrayImpuestos[$intIdImpuesto];

                                        if( $objAdmiImpuestoSelected )
                                        {
                                            $objAdmiImpuesto->setPorcentajeImpuesto($objAdmiImpuestoSelected->getPorcentajeImpuesto());
                                        }//( $objAdmiImpuestoSelected )
                                    }


                                    if( $objInfoProductoImpuesto )
                                    {
                                        $floatTmpImpuesto = ( ($arrayDatosListadoOrden['precio'] - $floatDescuentoUnitario
                                                                + $arrayDatosListadoOrden['impuestoIce']) 
                                                               * $objAdmiImpuesto->getPorcentajeImpuesto() )/100;

                                        if ( $objAdmiImpuesto->getTipoImpuesto() == 'IVA' || $objAdmiImpuesto->getTipoImpuesto() == 'ITBMS' )
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
                                                    $floatCompensacionSolidaria = ( ($arrayDatosListadoOrden['precio'] - $floatDescuentoUnitario
                                                                                     + $arrayDatosListadoOrden['impuestoIce']) 
                                                                                     * $floatPorcentajeCompensacion )/100;
                                                }//( !empty($floatPorcentajeCompensacion) )
                                            }//( $strEsCompensado == "S" && $objAdmiImpuestoCompensacion != null )
                                        }
                                        else
                                        {
                                            $floatOtrosImpuestoAcumulado += $floatTmpImpuesto;
                                        }// ( $objAdmiImpuesto->getTipoImpuesto() == 'IVA' || $objAdmiImpuesto->getTipoImpuesto() == 'ITBMS' )

                                        $floatImpuestoAcumulado += $floatTmpImpuesto;
                                    }//( $objInfoProductoImpuesto )
                                }//($boolImpuesto)
                            }//($objAdmiImpuesto)
                        }//($objInfoProductoImpuestosPrioridad2 as $objInfoProductoImpuesto)
                        $arrayDatosListadoOrden['impuesto']              = $floatImpuestoAcumulado;
                        $arrayDatosListadoOrden['impuestoIva']           = $floatImpuestoIvaAcumulado;
                        $arrayDatosListadoOrden['impuestoOtros']         = $floatOtrosImpuestoAcumulado;
                        $arrayDatosListadoOrden['compensacionSolidaria'] = $floatCompensacionSolidaria;
                    }//($objInfoProductoImpuestosPrioridad2)
                }//($objListadoOrden->getProductoId())
                
                if($objListadoOrden->getPlanId())
                {
                    $arrayDatosListadoOrden['codigo']       = $objListadoOrden->getPlanId()->getId();
                    $arrayDatosListadoOrden['informacion']  = $objListadoOrden->getPlanId()->getNombrePlan();
                    $arrayDatosListadoOrden['tipo']         = 'PL';
                    $arrayDatosListadoOrden['tipoOrden']    = 'PRE';

                    if( $objListadoOrden->getPlanId()->getIva() == "S" &&  $strPagaIva == "S" )
                    {
                        $objAdmiProductosByPlan = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanId( $objListadoOrden->getPlanId() );
                        
                        if( $objAdmiProductosByPlan )
                        {
                            foreach( $objAdmiProductosByPlan as $objAdmiProducto )
                            {
                                $arrayParametrosImpuestosPrioridad  = array( 'intIdProducto' => $objAdmiProducto->getProductoId(),
                                                                             'strEstado'     => 'Activo',
                                                                             'intIdPais'     => $intIdPaisSession,
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
                                            if( $strPagaIce != "SI" && ( $objAdmiImpuesto->getTipoImpuesto() == 'ICE'
                                                                         || $objAdmiImpuesto->getTipoImpuesto() == 'IEC' ) )
                                            {
                                                $boolImpuesto = false;
                                            }

                                            if($boolImpuesto)
                                            {
                                                $floatTmpImpuesto = ( ($arrayDatosListadoOrden['precio'] - $floatDescuentoUnitario) 
                                                                       * $objAdmiImpuesto->getPorcentajeImpuesto() )/100;

                                                if ( $objAdmiImpuesto->getTipoImpuesto() == 'ICE' || $objAdmiImpuesto->getTipoImpuesto() == 'IEC' )
                                                {
                                                    $floatImpuestoIceAcumulado += $floatTmpImpuesto;
                                                }
                                                else
                                                {
                                                    $floatOtrosImpuestoAcumulado += $floatTmpImpuesto;
                                                }// ( $objAdmiImpuesto->getTipoImpuesto() == 'ICE' || $objAdmiImpuesto->getTipoImpuesto() == 'IEC' )

                                                $floatImpuestoAcumulado += $floatTmpImpuesto;
                                            }//($boolImpuesto)
                                        }//($objAdmiImpuesto)
                                    }//($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImpuesto)

                                    $arrayDatosListadoOrden['impuesto']      = $floatImpuestoAcumulado;
                                    $arrayDatosListadoOrden['impuestoIce']   = $floatImpuestoIceAcumulado;
                                    $arrayDatosListadoOrden['impuestoOtros'] = $floatOtrosImpuestoAcumulado;
                                }//($objInfoProductoImpuestosPrioridad1)
                            }//( $objAdmiProductosByPlan as $objAdmiProductos )
                        }//( $objAdmiProductosByPlan )
                        
                        
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
                                            ? ( ( ( $arrayDatosListadoOrden['precio'] + $floatImpuestoIceAcumulado - $floatDescuentoUnitario )
                                                    * $entityAdmiImpuestoIva->getPorcentajeImpuesto() )/100 ) : 0;
                        
                        /**
                         * Bloque que saca el valor de COMPENSACION del subtotal del detalle del plan
                         */
                        if( $strEsCompensado == "SI" && $objAdmiImpuestoCompensacion != null )
                        {
                            $floatPorcentajeCompensacion = $objAdmiImpuestoCompensacion->getPorcentajeImpuesto();

                            if( !empty($floatPorcentajeCompensacion) )
                            {
                                $floatCompensacionSolidaria = ( ($arrayDatosListadoOrden['precio'] - $floatDescuentoUnitario + $floatImpuestoIceAcumulado)
                                                                 * $floatPorcentajeCompensacion )/100;
                            }//( !empty($floatPorcentajeCompensacion) )
                        }//( $strEsCompensado == "SI" && $objAdmiImpuestoCompensacion != null )
                        
                        $floatImpuestoAcumulado += $floatTmpImpuesto;
                        
                        $arrayDatosListadoOrden['impuesto']              = $floatImpuestoAcumulado;
                        $arrayDatosListadoOrden['impuestoIva']           = $floatTmpImpuesto;
                        $arrayDatosListadoOrden['compensacionSolidaria'] = $floatCompensacionSolidaria;
                    }//( $objListadoOrden->getPlanId()->getIva() == "S" &&  $strPagaIva == "S" )
                }//($objListadoOrden->getPlanId())

                if($arrayServicioHistorial['feCreacion'] != null)
                {
                    $arrayDatosListadoOrden['fechaActivacion'] = $arrayServicioHistorial['feCreacion'];
                }

                $arrayDatosListadoOrden['login']       = $objListadoOrden->getPuntoId()->getLogin();
                $arrayDatosListadoOrden['puntoId']     = $objListadoOrden->getPuntoId()->getId();
                $arrayDatosListadoOrden['fechaDesde']  = $arrayFechaDesde[0];
                $arrayDatosListadoOrden['fechaHasta']  = $arrayFechaHasta[0];
                
                if($strPrefijoEmpresa=='MD')
                {
                    $arrayDatosListadoOrden['descripcion']  = 'Fecha de activacion: ' . $arrayDatosListadoOrden['fechaActivacion'];
                }
                else
                {
                    //Se debe obtener la informacion referencia a la info_servicio
                    $arrayDatosListadoOrden['descripcion']  = $objListadoOrden->getDescripcionPresentaFactura();
                    $arrayDatosListadoOrden['descripcion']  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                           ->getFVarcharClean($arrayDatosListadoOrden['descripcion']);
                }//($strPrefijoEmpresa=='MD')
                
                $arrayDatosListadoOrden['porcentajeImpuesto']    = $entityAdmiImpuestoIva->getPorcentajeImpuesto();
                
                $arrayDetalleListadoOrden[] = $arrayDatosListadoOrden;
            }//foreach($arrayListadoOrden as $objListadoOrden)
        }//( !empty($arrayListadoOrden) )

        if($objInformacionDataGrid)
        {   
            foreach($objInformacionDataGrid as $objDatosInformacionGrid)
            {
                $arrayDatosListadoOrden                          = array();
                $arrayDatosListadoOrden['codigo']                = $objDatosInformacionGrid->codigo;
                $arrayDatosListadoOrden['informacion']           = $objDatosInformacionGrid->informacion;
                $arrayDatosListadoOrden['precio']                = $objDatosInformacionGrid->precio;
                $arrayDatosListadoOrden['cantidad']              = $objDatosInformacionGrid->cantidad;
                $arrayDatosListadoOrden['descuento']             = $objDatosInformacionGrid->descuento;
                $arrayDatosListadoOrden['tipo']                  = $objDatosInformacionGrid->tipo;
                $arrayDatosListadoOrden['tipoOrden']             = $objDatosInformacionGrid->tipoOrden;
                $arrayDatosListadoOrden['fechaActivacion']       = $objDatosInformacionGrid->fechaActivacion;
                $arrayDatosListadoOrden['puntoId']               = $objDatosInformacionGrid->puntoId;
                $arrayDatosListadoOrden['tieneImpuesto']         = $objDatosInformacionGrid->tieneImpuesto;
                $arrayDatosListadoOrden['descripcion']           = $objDatosInformacionGrid->descripcion;
                $arrayDatosListadoOrden['impuesto']              = $objDatosInformacionGrid->impuesto;
                $arrayDatosListadoOrden['impuestoIva']           = $objDatosInformacionGrid->impuestoIva;
                $arrayDatosListadoOrden['impuestoIce']           = $objDatosInformacionGrid->impuestoIce;
                $arrayDatosListadoOrden['impuestoOtros']         = $objDatosInformacionGrid->impuestoOtros;
                $arrayDatosListadoOrden['compensacionSolidaria'] = $objDatosInformacionGrid->compensacionSolidaria;
                $arrayDatosListadoOrden['porcentajeImpuesto']    = $objDatosInformacionGrid->porcentajeImpuesto;
                $arrayDatosListadoOrden['porcentajeImpuestoIce'] = $objDatosInformacionGrid->porcentajeImpuestoIce;
                $arrayDatosListadoOrden['fechaDesde']            = $objDatosInformacionGrid->fechaDesde;
                $arrayDatosListadoOrden['fechaHasta']            = $objDatosInformacionGrid->fechaHasta;
                $arrayDetalleListadoOrden[]                      = $arrayDatosListadoOrden;
            }
        }
        $objParametroCab  = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                      ->findOneBy( array('nombreParametro' => 'CARGO REPROCESO DEBITO', 
                                                         'estado'          => 'Activo') );
        if(is_object($objParametroCab))
        { 
            // Se agrega detalle por cargo de reproceso de débito
            if("MD"=== $strPrefijoEmpresa && "S"=== $strTieneCargoReproceso)
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
                    $arrayDatosListadoOrden['impuestoIva']           = (($floatPrecioReproceso) * $floatPorcentajeImp )/100;
                    $arrayDatosListadoOrden['impuestoIce']           = 0;
                    $arrayDatosListadoOrden['impuestoOtros']         = 0;
                    $arrayDatosListadoOrden['compensacionSolidaria'] = 0;
                    $arrayDatosListadoOrden['porcentajeImpuesto']    = $floatPorcentajeImp;
                    $arrayDatosListadoOrden['porcentajeImpuestoIce'] = $floatPorcentajeImpIce;
                    $arrayDetalleListadoOrden[]                      = $arrayDatosListadoOrden; 
                }
            }
        }       
        
        if ($boolFactAgrupada =='true')
        {
            $fltAcumDescuento1          = 0;
            $fltAcumPrecioUnitario1     = 0;
            $fltAcumSubtotal1           = 0;
            $fltAcumImpuestoIce1        = 0;
            $fltAcumImpuesto1           = 0;
            $fltAcumImpuestoIva1        = 0;
            $fltAcumImpuestoOtros1      = 0;
            $fltCompensacionSolidaria1  = 0;    
            $fltAcumDescuento2          = 0;
            $fltAcumPrecioUnitario2     = 0;
            $fltAcumSubtotal2           = 0;
            $fltAcumImpuestoIce2        = 0;
            $fltAcumImpuesto2           = 0;
            $fltAcumImpuestoIva2        = 0;
            $fltAcumImpuestoOtros2      = 0;
            $fltCompensacionSolidaria2  = 0;     
            $boolDetalleIva                  = false;
            $boolDetalleSinIva               = false;
            $arrayDatosListadoOrdenAgrupada  = array();
            
            foreach($arrayDetalleListadoOrden as $arrayKey => $arrayDatosListadoOrden)
            {                              
                if ($arrayDatosListadoOrden['impuestoIva']!=0)
                { 
                    if($arrayDatosListadoOrden['descuento'] != null)
                    {
                        $fltAcumDescuento1 += $arrayDatosListadoOrden['descuento'];
                    }
                    $fltAcumPrecioUnitario1        += ($arrayDatosListadoOrden['precio_uni'] * $arrayDatosListadoOrden['cantidad']);       
                    $fltAcumSubtotal1              += ($arrayDatosListadoOrden['precio'] * $arrayDatosListadoOrden['cantidad']);
                    $fltAcumImpuestoIce1           += ($arrayDatosListadoOrden['impuestoIce'] * $arrayDatosListadoOrden['cantidad']);
                    $fltAcumImpuesto1              += ($arrayDatosListadoOrden['impuesto'] * $arrayDatosListadoOrden['cantidad']);
                    $fltAcumImpuestoIva1           += ($arrayDatosListadoOrden['impuestoIva'] * $arrayDatosListadoOrden['cantidad']);
                    $fltAcumImpuestoOtros1         += ($arrayDatosListadoOrden['impuestoOtros'] * $arrayDatosListadoOrden['cantidad']);
                    $fltCompensacionSolidaria1     += ($arrayDatosListadoOrden['compensacionSolidaria'] * $arrayDatosListadoOrden['cantidad']);
                
                    if (!$boolDetalleIva)
                    {
                        $boolDetalleIva = true;                    
                        $arrayDatosListadoOrdenAgrupadaConIva = $arrayDatosListadoOrden;                        
                    }
                } 
                else       
                {
                    if($arrayDatosListadoOrden['descuento'] != null)
                    {
                        $fltAcumDescuento2 += $arrayDatosListadoOrden['descuento'];
                    }
                    $fltAcumPrecioUnitario2        += ($arrayDatosListadoOrden['precio_uni'] * $arrayDatosListadoOrden['cantidad']);       
                    $fltAcumSubtotal2              += ($arrayDatosListadoOrden['precio'] * $arrayDatosListadoOrden['cantidad']);
                    $fltAcumImpuestoIce2           += ($arrayDatosListadoOrden['impuestoIce'] * $arrayDatosListadoOrden['cantidad']);
                    $fltAcumImpuesto2              += ($arrayDatosListadoOrden['impuesto'] * $arrayDatosListadoOrden['cantidad']);
                    $fltAcumImpuestoIva2           += ($arrayDatosListadoOrden['impuestoIva'] * $arrayDatosListadoOrden['cantidad']);
                    $fltAcumImpuestoOtros2         += ($arrayDatosListadoOrden['impuestoOtros'] * $arrayDatosListadoOrden['cantidad']);
                    $fltCompensacionSolidaria2     += ($arrayDatosListadoOrden['compensacionSolidaria'] * $arrayDatosListadoOrden['cantidad']);
                                    
                    if (!$boolDetalleSinIva)
                    {
                        $boolDetalleSinIva = true;                    
                        $arrayDatosListadoOrdenAgrupadaSinIva = $arrayDatosListadoOrden;                        
                    }
                }
            }
            
            $arrayDetalleListadoOrden = array();
            $arrayDatosListadoOrdenAgrupadaConIva['cantidad']  = 1;
            $arrayDatosListadoOrdenAgrupadaConIva['descuento'] = $fltAcumDescuento1;
            $arrayDatosListadoOrdenAgrupadaConIva['precio_uni'] = $fltAcumPrecioUnitario1;
            $arrayDatosListadoOrdenAgrupadaConIva['precio'] = $fltAcumSubtotal1;
            $arrayDatosListadoOrdenAgrupadaConIva['impuestoIce'] = $fltAcumImpuestoIce1;
            $arrayDatosListadoOrdenAgrupadaConIva['impuesto'] = $fltAcumImpuesto1;
            $arrayDatosListadoOrdenAgrupadaConIva['impuestoIva'] = $fltAcumImpuestoIva1;
            $arrayDatosListadoOrdenAgrupadaConIva['impuestoOtros'] = $fltAcumImpuestoOtros1;
            $arrayDatosListadoOrdenAgrupadaConIva['compensacionSolidaria'] = $fltCompensacionSolidaria1;
            $arrayDetalleListadoOrden[] = $arrayDatosListadoOrdenAgrupadaConIva;
            
            $arrayDatosListadoOrdenAgrupadaSinIva['cantidad']  = 1;
            $arrayDatosListadoOrdenAgrupadaSinIva['descuento'] = $fltAcumDescuento2;
            $arrayDatosListadoOrdenAgrupadaSinIva['precio_uni'] = $fltAcumPrecioUnitario2;
            $arrayDatosListadoOrdenAgrupadaSinIva['precio'] = $fltAcumSubtotal2;
            $arrayDatosListadoOrdenAgrupadaSinIva['impuestoIce'] = $fltAcumImpuestoIce2;
            $arrayDatosListadoOrdenAgrupadaSinIva['impuesto'] = $fltAcumImpuesto2;
            $arrayDatosListadoOrdenAgrupadaSinIva['impuestoIva'] = $fltAcumImpuestoIva2;
            $arrayDatosListadoOrdenAgrupadaSinIva['impuestoOtros'] = $fltAcumImpuestoOtros2;
            $arrayDatosListadoOrdenAgrupadaSinIva['compensacionSolidaria'] = $fltCompensacionSolidaria2;
            $arrayDetalleListadoOrden[] = $arrayDatosListadoOrdenAgrupadaSinIva;                       
        }
        $response = new Response(json_encode(array('listadoInformacion' => $arrayDetalleListadoOrden)));        
        $response->headers->set('Content-type', 'text/json');
       
        return $response;
    }

    /**
     * Función que obtiene la diferencia de días restantes para la factura proporcional.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 06-11-2018
     */
    public function getDiasRestantesAction()
    {
        $objRequest      = $this->getRequest();
        $objSession      = $objRequest->getSession();
        $strEmpresaCod   = $objSession->get('idEmpresa');
        $strUsrCreacion  = $objSession->get('user');
        $strIpCreacion   = $objRequest->getClientIp();
        $emFinanciero    = $this->get('doctrine')->getManager('telconet_financiero');
        $serviceUtil     = $this->get('schema.Util');
        $intCantidadDias = 0;
        $intDiasTotales  = 0;
        try
        {
            $arrayFechaDesde = explode('T',$objRequest->get("fechaDesde"));
            $arrayFechaHasta = explode('T',$objRequest->get("fechaHasta"));

            $serviceInfoDocumentoFinancieroCab = $this->get('financiero.InfoDocumentoFinancieroCab');
            $arrayParametrosRestaFechas        = array('strFechaInicio' => $arrayFechaDesde[0], 'strFechaFin' => $arrayFechaHasta[0]);
            $arrayResultadosRestaFechas        = $serviceInfoDocumentoFinancieroCab->restarFechas( $arrayParametrosRestaFechas );

            if ( isset($arrayResultadosRestaFechas['intCantidadDiasEntreFechas']) && $arrayResultadosRestaFechas['intCantidadDiasEntreFechas'] > 0 )
            {
                $intCantidadDias = $arrayResultadosRestaFechas['intCantidadDiasEntreFechas'];
            }

            $arrayParametrosFechaProporcional = array('strEmpresaCod'      => $strEmpresaCod,
                                                      'strFechaActivacion' => $arrayFechaDesde[0]);
            $arrayResultadoDiasRestantes      = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                             ->getFechasDiasPeriodo($arrayParametrosFechaProporcional);

            if ( isset($arrayResultadoDiasRestantes['intTotalDiasMes']) && intval($arrayResultadoDiasRestantes['intTotalDiasMes']) > 0 )
            {
                $intDiasTotales = intval($arrayResultadoDiasRestantes['intTotalDiasMes']);
            }
            $strStatus  = "OK";
            $strMensaje = null;
        }
        catch(\Exception $objException)
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'InfoDocumentoFinancieroController.getDiasRestantesAction', 
                                       'Error al obtener los días para la factura proporcional. '.$objException->getMessage(), 
                                       $strUsrCreacion, 
                                       $strIpCreacion );
            $strStatus  = "ERROR";
            $strMensaje = "Ocurrió un error al obtener la cantidad de días proporcionales.";
            $intCantidadDias = 0;
            $intDiasTotales  = 0;
        }
        $arrayRespuesta = array("intProporcionalDias" => $intCantidadDias,
                                "intTotalPorMesDias"  => $intDiasTotales);
        $objResponse = new Response(json_encode(array("status" => $strStatus , "message" => $strMensaje, "data" => $arrayRespuesta)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
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
     * @version 1.2 27-01-2021 - Se agrega el id a los detalles de la factura enviados
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 15-03-2021 - Se mueve ubicación de variable que obtiene  id de los detalles de la factura por problema de visualización.
     * 
     * @return Response $response
     * 
     */
    public function detalleFacturaAction()
    {
        $request = $this->getRequest();
        $facturaid = $request->get('facturaid');

        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $resultado = $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);

        if(!$resultado)
        {
            //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
            $detalle_orden_l[] = array("informacion" => "", "precio" => "", "cantidad" => "", "descuento" => "", "descripcion" => "");
        }
        else
        {
            
            $em_comercial = $this->get('doctrine')->getManager('telconet');
            $detalle_orden_l = array();
            foreach($resultado as $factdet)
            {
                $objTecn['id'] = $factdet->getId();
                if($factdet->getProductoId())
                {
                    
                    $informacion = $em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
                    if($informacion)
                        $objTecn['informacion'] = $informacion->getDescripcionProducto();
                    else
                        $objTecn['informacion'] = "";
                }
                if($factdet->getPlanId())
                {
                    $informacion = $em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
                    if($informacion)
                        $objTecn['informacion'] = $informacion->getNombrePlan();
                    else
                        $objTecn['informacion'] = "";
                }
                if($factdet->getPrecioVentaFacproDetalle() != "")
                {
                    $objTecn['precio'] = $factdet->getPrecioVentaFacproDetalle() ? $factdet->getPrecioVentaFacproDetalle() : 0;
                }
                else
                {
                    $objTecn['precio'] = $factdet->getValorFacproDetalle() ? $factdet->getValorFacproDetalle() : 0;
                }
                  
                $objTecn['cantidad'] = $factdet->getCantidad();
                //$tecn['cantidad'] = 1;

                if($factdet->getDescuentoFacproDetalle() > 0)
                {
                    $objTecn['descuento'] = $factdet->getDescuentoFacproDetalle();  
                }
                else if($factdet->getPorcetanjeDescuentoFacpro() > 0)
                {
                    $objTecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
                }

                else
                    $objTecn['descuento'] = "";
                $objTecn['descripcion'] = $factdet->getObservacionesFacturaDetalle();

                if($factdet->getPuntoId() != null)
                {
                    $pto = $em_comercial->getRepository('schemaBundle:InfoPunto')->find($factdet->getPuntoId());
                    $objTecn['login'] = $pto->getLogin();
                }
                else
                {
                    $objTecn['login'] = "";
                }
                    

                $detalle_orden_l[] = $objTecn;
            }
        }

        $response = new Response(json_encode(array('listadoInformacion' => $detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function detalleFacturaEditarAction()
    {
        $request = $this->getRequest();
        $request = $this->getRequest();
        $request = $this->get('request');
        $session = $request->getSession();
        $id_empresa = $session->get('idEmpresa');

        $facturaid = $request->get('facturaid');
        $precargado = $request->get('precargado');

        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $resultado = $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);

        if(!$resultado)
        {
            //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
            $detalle_orden_l[] = array("codigo" => "", "informacion" => "", "precio" => "", "cantidad" => "", "descuento" => "", "tipo" => "");
        }
        else
        {
            $em_comercial = $this->get('doctrine')->getManager('telconet');
            $detalle_orden_l = array();
            foreach($resultado as $factdet)
            {
                if($factdet->getProductoId())
                {
                    $informacion = $em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
                    $tecn['codigo'] = $informacion->getId();
                    $tecn['informacion'] = $informacion->getDescripcionProducto();
                    $tecn['tipo'] = "PR";
                }
                if($factdet->getPlanId())
                {
                    $informacion = $em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
                    $tecn['codigo'] = $informacion->getId();
                    $tecn['informacion'] = $informacion->getNombrePlan();
                    $tecn['tipo'] = "PL";
                }
                $tecn['precio'] = $factdet->getPrecioVentaFacproDetalle();
                $tecn['cantidad'] = $factdet->getCantidad();
                $tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
                $detalle_orden_l[] = $tecn;
            }
        }

        if(isset($precargado) && $precargado == "P")
        {
            $puntoid = $request->get('puntoid');

            //$idEmpresa = $session->get('idEmpresa');
            $em = $this->get('doctrine')->getManager('telconet');
            //$idProducto = $request->request->get("idProducto"); 	
            $estado = "Pendiente";
            //$id_empresa="10";
            $resultado = $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPuntoPorEstado($id_empresa, $puntoid, $estado);
            $listado_detalles_orden = $resultado['registros'];
            //$listado_detalles_orden = $em->getRepository('schemaBundle:InfoServicio')->findPorEstado($puntoid,$estado);
            //$clientes[] = array("idCl"=>"$telefono","loginClienteSucursal"=>"","descripcion"=>"");
            //print_r($listado_detalles_orden);
            if($listado_detalles_orden)
            {
                $detalle_orden_l = array();
                foreach($listado_detalles_orden as $ord)
                {
                    if($ord->getProductoId())
                    {
                        $tecn['codigo'] = $ord->getProductoId()->getId();
                        $tecn['informacion'] = $ord->getProductoId()->getDescripcionProducto();
                        $tecn['tipo'] = 'PR';
                    }
                    if($ord->getPlanId())
                    {
                        $tecn['codigo'] = $ord->getPlanId()->getId();
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
        $response = new Response(json_encode(array('listadoInformacion' => $detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * listarTodasFacturasAction crea el objeto que se muestra en el grid de Facturas
     * @author Alexander Samaniego
     * @version 1.1 14-10-2014
     * @since 1.0
     * @author Alexander Samaniego
     * @version 1.2 21-01-2015
     * @since 1.1
     * @author Alexander Samaniego
     * @version 1.3 02-02-2015
     * @since 1.2
     * @author Alexander Samaniego
     * @version 1.4 03-02-2015
     * @since 1.3
     * @author Alexander Samaniego
     * @version 1.5 09-02-2015
     * @since 1.4
     * @author Alexander Samaniego
     * @version 1.5 11-03-2015
     * @since 1.5
     * @author Alexander Samaniego
     * @version 1.6 09-09-2015
     * @since 1.5
     *
     * @author Alexander Samaniego
     * @version 1.7 28-06-2016 Se agrega funcionalidad para poder simular un comprobante XML
     * @since 1.6
     *
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 1.8 09-08-2016 
     * Actualización: Se agrega funcionalidad que permita consultar facturas aun si no tiene punto cliente en sesión.
     * Tambien se agrega permitir buscar por login y por numero de facturaSri
     * @since 1.7
     * 
     * @author Ricardo Coello Quezada. <rcoello@telconet.ec>
     * @version 1.8 30-06-2017 
     * Actualización: Se agrega funcionalidad que permita consultar si el comprobante rechazado puede ser actualizado,
     * se agrega llamada a tabla de parametros donde se tendra configurado los estados para permitidos para las empresas TN y MD.
     * @since 1.8
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.9 05-03-2018 - Se cambia Filtro de FeCreacion a FeEmision.
     * @return Response     Retorna los datos que se muestran el grid de Facturas
     * 
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.9 05-03-2018 - Se cambia Filtro de FeCreacion a FeEmision.
     * @return Response     Retorna los datos que se muestran el grid de Facturas
     * 
     * @author : Kevin Baque <kbaque@telconet.ec>
     * @version 2.0 30-12-2018 Se realiza cambio para quela consulta de factura se realice a través de la persona en sesion, solo para Telconet
     *                         en caso de ser asistente aparecerá las factura de los vendedores asignados al asistente
     *                         en caso de ser vendedor aparecerá sus facturas
     *                         en caso de ser subgerente aparecerá las facturas de los vendedores que reportan al subgerente
     *                         en caso de ser gerente aparecerá todos las facturas
     * 
     * @author : Gustavo Narea <gnarea@telconet.ec>
     * @version 2.1 07-08-2020 Se agrega un parametro al enviar a llamar a la funcion find30FacturasPorEmpresaPorEstado
     *                         Con el fin de mejorar el tiempo de consulta de una factura
     * 
     * @author : Gustavo Narea <gnarea@telconet.ec>
     * @version 2.2 28-04-2022 Se añade si se debe enviar el link del boton-clonacion para las facturas con ciertos estados 
     *                          (Parametrizados en la AdmiParametroCab: CLONACION DE FACTURAS), y requerido el perfil correcto de clonacion.
     * 
     * @author : Gustavo Narea <gnarea@telconet.ec>
     * @version 2.3 19-05-2022 Se remueve link de imprimirFactura Unitaria
     
     */
    public function listarTodasFacturasAction()
    {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $arrayPtoCliente    = $objSession->get('ptoCliente');
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $intIdOficina       = $objSession->get('idOficina');
        $strUsrCreacion     = $objSession->get('user');
        $em                 = $this->get('doctrine')->getManager('telconet_financiero');
        $arrayFeDesde       = explode('T', $objRequest->get("fechaDesde"));
        $arrayFeHasta       = explode('T', $objRequest->get("fechaHasta"));
        $strEstado          = $objRequest->get("estado");
        $strNumeroFactura   = $objRequest->get("numeroFactura");
        $intIdPuntoParam    = $objRequest->get("puntoId");
        $intLimite          = $objRequest->get("limit");
        $intPagina          = $objRequest->get("page");
        $intInicio          = $objRequest->get("start");
        $emGeneral          = $this->get('doctrine')->getManager('telconet_general');
        $emComercial        = $this->getDoctrine()->getManager("telconet");
        $strNombreParametro = 'ESTADO_COMPROBANTE_RECHAZADO';
        $strModulo          = 'FINANCIERO';
        $strProceso         = 'FACTURACION';
        $boolImpresoraPanama =  false;

        $strTipoPersonal       = 'Otros';
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');

        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
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
        if(!empty($arrayPtoCliente))
        {
            $intIdPunto = $arrayPtoCliente['id'];
        }
        else
        {
            $intIdPunto = $intIdPuntoParam;
        }    

        
        $arrayParametros['boolBusqueda']       = true;
        $arrayParametros['strEstado']          = $strEstado;
        $arrayParametros['intLimit']           = $intLimite;
        $arrayParametros['intPage']            = $intPagina;
        $arrayParametros['intStart']           = $intInicio;
        $arrayParametros['intIdPunto']         = $intIdPunto;
        $arrayParametros['intIdEmpresa']       = $intIdEmpresa;
        $arrayParametros['strNumeroDocumento'] = $strNumeroFactura;
        $arrayParametros['arrayTipoDoc']       = array('FAC', 'FACP');

        $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
        $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
        if((!$arrayFeDesde[0]) && (!$arrayFeHasta[0]))
        {           
            $arrayInfoDocumentoFinanacieroCab       = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                         ->find30FacturasPorEmpresaPorEstado($arrayParametros);
            $objInfoDocumentoFinancieroCab          = $arrayInfoDocumentoFinanacieroCab['registros'];
            $intTotalRegistros                      = $arrayInfoDocumentoFinanacieroCab['total'];
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
        
        $arrayAdmiParametroDet                      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get($strNombreParametro, 
                                                                      $strModulo, 
                                                                      $strProceso, 
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      $strPrefijoEmpresa,
                                                                      $intIdEmpresa);
        
        if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
        {
            $arrayEstados = array();
            foreach($arrayAdmiParametroDet as $arrayParametro)
            {
                $arrayEstados[] = $arrayParametro['valor1'];
            }//( $arrayAdmiParametroDet as $arrayParametro )
        }//( $arrayAdmiParametroDet )

        $intCambiaColor = 1;

        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');
        $intMes     = intval(date("m"));
        $intAnio    = intval(date("Y"));
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
            $boolVerificaRechazada          = $serviceInfoCompElectronico->getVerificaComprobanteByEstado($objInfoDocumentoFinancieroCab->getId(), 
                                                                                                          $arrayEstados);
            $boolVerificaEnvioNotificacion  = $serviceInfoCompElectronico->getCompruebaEstadoComprobante($objInfoDocumentoFinancieroCab->getId(), 5);
            if($boolVerificaEnvioNotificacion == 5)
            {
                $boolDocumentoPdf               = true;
                $boolDocumentoXml               = true;
                $strMsnErrorComprobante         = '';
            }
            $strDebePintarBotonClonar = "N";
            $objInfoDocumentoFinancieroCabService   = $this->get('financiero.InfoDocumentoFinancieroCab');
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

            //permite ver en el grid de facturas el boton que simula el comprobante electronico
            if('Pendiente' === $objInfoDocumentoFinancieroCab->getEstadoImpresionFact())
            {
                $boolSimularCompElec = true;
            }
            //verifica que se pueda actualizar el comprobante cuando este con errores o caundo sea rechazado.
            if($boolVerificaConError == true || $boolVerificaRechazada == true)
            {
                $boolVerificaActualiza = true;
            }

            $strUrlShow         = $this->generateUrl('infodocumentofinancierocab_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
            $strLinkShow        = $strUrlShow;

            $em_comercial               = $this->get('doctrine')->getManager('telconet');
            $objInfoPunto               = $em_comercial->getRepository('schemaBundle:InfoPunto')->find($objInfoDocumentoFinancieroCab->getPuntoId());
            $objInfoPersonaEmpresaRol   = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
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
            if($strPrefijoEmpresa == 'TNP')
            {
                $boolImpresoraPanama = true;
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
                'linkImprimirPanama'        => $this->generateUrl('infodocumentofinancierocab_apiInterfazPanama', 
                                                     array('id' => $objInfoDocumentoFinancieroCab->getId())),
                'strLinkClonar'                => $this->generateUrl('infodocumentofinancierocab_clonar',
                                                                array('intId' => $objInfoDocumentoFinancieroCab->getId())),
                'strDebePintarBotonClonar'        => $strDebePintarBotonClonar,    
                'empresa'                   => $strPrefijoEmpresa,
                'boolMensajesCompElectronico'   => $boolUrlMensajesCompElectronico,
                'boolImpresoraPanama'           => $boolImpresoraPanama,
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
            $objResponse = new Response(json_encode(array('total' => $intTotalRegistros, 'documentos' => $arrayResultado)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * Documentación para el método reajustarImpuestosAjaxAction
     *
     * Permite reajustar los valores del IVA e ICE para la cuadratura de las facturas.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 10-10-2017  - Se crea el método.
     *
     * @Secure(roles="ROLE_67-5517")
     **/
    public function reajustarImpuestosAjaxAction()
    {
        $objRequest      = $this->getRequest();
        $objSession      = $objRequest->getSession();
        $strEmpresaCod   = $objSession->get('idEmpresa');
        $strUsuario      = $objSession->get('user');
        $intIdDocumento  = $objRequest->get('intIdDocumento');
        $em              = $this->getDoctrine()->getManager("telconet_financiero");
        $arrayParametros = array("intIdDocumento" => $intIdDocumento,
                                 "strUsuario"     => $strUsuario,
                                 "strEmpresaCod"  => $strEmpresaCod,
                                 "strEstado"      => "Rechazado");
        $strMensaje      = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->reajustarImpuestos($arrayParametros);
        return new Response($strMensaje);
    }

    /**
     * El metodo estadosAction contiene un arreglo con los estados de las facturas
     * @author Alexander Samaniego
     * @version 1.1 14-10-2014
     * @since 1.0
     * 
     * @author Edgar Holguín <eholguín@telconet.ec>
     * @version 1.1 02-07-2020  - Se agrega consulta de parámetro para visualización de filtro por estado estado Eliminado.
     * 
     * @return Response Retorna un arreglo con los estados de las facturas
     */
    public function estadosAction()
    {
        $objRequest          = $this->getRequest();  
        $objSession          = $objRequest->getSession();
        $strCodEmpresa       = $objSession->get('idEmpresa');
        $emGeneral           = $this->getDoctrine()->getManager('telconet_general');
        $objAdmiParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                      ->findOneBy(array('nombreParametro' => 'FILTRO_FACT_ELIMINADAS',
                                                        'estado'          => 'Activo'));
        if(is_object($objAdmiParametroCab))
        {
            $objAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                             ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                               'descripcion' => 'VISUALIZA FILTRO',
                                                               'empresaCod'  => $strCodEmpresa,
                                                               'estado'      => 'Activo'));
            if(is_object($objAdmiParametroDet))
            {
                $strVisualizaFiltro = $objAdmiParametroDet->getValor1();
            }
        }
        $arrayEstados[] = array('idEstado' => 'Activo', 'codigo' => 'ACT', 'descripcion' => 'Activo');
        $arrayEstados[] = array('idEstado' => 'Anulado', 'codigo' => 'ANU', 'descripcion' => 'Anulado');
        $arrayEstados[] = array('idEstado' => 'Courier', 'codigo' => 'COU', 'descripcion' => 'Courier');
        $arrayEstados[] = array('idEstado' => 'Cerrado', 'codigo' => 'CER', 'descripcion' => 'Cerrado');
        $arrayEstados[] = array('idEstado' => 'Inactivo', 'codigo' => 'ACT', 'descripcion' => 'Inactivo');
        $arrayEstados[] = array('idEstado' => 'Pendiente', 'codigo' => 'PEN', 'descripcion' => 'Pendiente');
        $arrayEstados[] = array('idEstado' => 'Rechazado', 'codigo' => 'REC', 'descripcion' => 'Rechazado');
        if($strVisualizaFiltro==='S')
        {
            $arrayEstados[] = array('idEstado' => 'Eliminado', 'codigo' => 'ELI', 'descripcion' => 'Eliminado'); 
        }
        $objResponse    = new Response(json_encode(array('estados' => $arrayEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_260-8")
     */
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|", $parametro);
       
        $em = $this->getDoctrine()->getManager();
        foreach($array_valor as $id):
          
            $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
            if($entity)
            {
                $entity->setEstadoImpresionFact("Anulado");
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
        //modificar la relacion sistema
        $relacionsistema_id = 473;
        $resultado = $em->getRepository('schemaBundle:AdmiMotivo')->loadMotivos($relacionsistema_id);
        foreach($resultado as $datos):
            $arreglo[] = array(
                'id' => $datos->getId(),
                'descripcion' => $datos->getNombreMotivo()
            );
            //$response = new Response(json_encode($arreglo));
            $response = new Response(json_encode(array('documentos' => $arreglo)));
        endforeach;
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * Documentación para el método 'getMotivosCancelacionAdmin'.
     * Función que retorna listado de motivos asociados a cancelación administrativa.
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 26-11-2018
     * 
     * @return object $objResponse
     */    
    public function getMotivosCancelacionAdminAction()
    {
        $emGeneral   = $this->getDoctrine()->getManager();
        
        $emSeguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $objSistModulo = $emSeguridad->getRepository('schemaBundle:SistModulo')
                                     ->findOneBy(array( 'nombreModulo' => 'cancelacionadministrativa','estado' => 'Activo')); 
        if(is_object($objSistModulo))
        {        
            $objSeguRelacionSistema = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                                  ->findOneBy(array( 'moduloId' => $objSistModulo->getId()));

            if(is_object($objSeguRelacionSistema))
            {
                $arrayResultado = $emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                            ->loadMotivos($objSeguRelacionSistema->getId());

                foreach($arrayResultado as $objMotivo):
                    $arrayMotivos[] = array(
                        'id'          => $objMotivo->getId(),
                        'descripcion' => $objMotivo->getNombreMotivo()
                    );
                
                    $objResponse = new Response(json_encode(array('documentos' => $arrayMotivos)));
                endforeach;
            }
        }
        $objResponse->headers->set('Content-type', 'text/json');
        
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'deleteSeleccionadasAjaxAction'.
     * Este metodo anula un comprobante 'Factura' por un idFactura específico
     *
     * @return object $response Retorna un json en caso de que se pudiera anular el comprobante o no
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 17-12-2015
     * 
     * Se añade el llamado al Servicio para la anulacion del comprobante electronico via web service en DB_COMPROBANTES
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 08-08-2016
     * 
     * Se añade el llamado al Servicio que guarda Responsables de la anulacion de la factura en la info_caracteristica.
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 05-10-2016
     *
     * Se agrega la validación para que el motivo sea obligatorio. Se envía notificación al cliente y al alias asignado.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.3
     * @since 17-04-2018
     *
     * @Secure(roles="ROLE_67-4897")
     */
    public function deleteSeleccionadasAjaxAction()
    {

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $objPeticion             = $this->get('request');
        $objSession              = $objPeticion->getSession();
        $intIdfactura            = $objPeticion->get('idfactura');
        $intMotivoId             = $objPeticion->get('motivos');
        $strTipoResponsable      = $objPeticion->get('strTipoResponsable');
        $strClienteResponsable   = $objPeticion->get('strClienteResponsable');
        $strEmpresaResponsable   = $objPeticion->get('strEmpresaResponsable');
        $strEstado               = "Pendiente";
        $strIpCreacion           = $objPeticion->getClientIp();
        $strUser                 = $objSession->get('user');
        $intIdEmpresa            = $objSession->get('idEmpresa');
        $intIdEmpleado           = $objSession->get('id_empleado');
        $serviceInfoDocFinanCab  = $this->get('financiero.InfoDocumentoFinancieroCab');
        
        if(is_null($intMotivoId))
        {
            $objResponse = new Response(json_encode(array('success' => false, 'mensaje' => 'Es obligatorio seleccionar el motivo.')));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;
        }

        $serviceInfoDocumentoFinancieroCab                    = $this->get('financiero.InfoDocumentoFinancieroCab');
        $arrayParametrosAnulacionFac["facturaId"]             = $intIdfactura;
        $arrayParametrosAnulacionFac["user"]                  = $strUser;
        $arrayParametrosAnulacionFac["strTipoResponsable"]    = $strTipoResponsable;
        $arrayParametrosAnulacionFac["strClienteResponsable"] = $strClienteResponsable;
        $arrayParametrosAnulacionFac["strEmpresaResponsable"] = $strEmpresaResponsable;
        $arrayParametrosAnulacionFac["strIpCreacion"]         = $strIpCreacion;

        //Guarda los parametros correspondientes para la anulacion del documento
        $boolStatusGuardaParamAnulacionFac = $serviceInfoDocumentoFinancieroCab->guardaResponsableAnulacionFac($arrayParametrosAnulacionFac); //guardaParamRespAnulacionNC

        if( $boolStatusGuardaParamAnulacionFac )
        {        
            $em                      = $this->getDoctrine()->getManager("telconet_financiero");
            $objInfoDocFinanCabRepos = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab');
            $objInfoDocFinCab        = $objInfoDocFinanCabRepos->find($intIdfactura);

            $intCantidadNCValidas    = $objInfoDocFinanCabRepos->getCantidadNC($intIdfactura);
            $intCantidadPagos        = $objInfoDocFinanCabRepos->getTieneFacturas($intIdfactura);

            //Instancia del Servicio para realizar la Anulacion en DB_COMPROBANTES
            $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');

            if($intCantidadNCValidas == 0)
            {
                if($intCantidadPagos == 0)
                {
                    if($objInfoDocFinCab)
                    {
                        // Llamado al Servicio para la anulacion del comprobante electronico via web service en Comprobantes EC
                        // De no encontrarse en Comprobantes EC, o de no estar Anulado en el SRI, no procede la anulacion en Telcos
                        $arrayDocumentosElectronicos = $serviceInfoCompElectronico->anularComprobanteElectronico($intIdfactura);
                        if(!empty($arrayDocumentosElectronicos))
                        {
                            if($arrayDocumentosElectronicos['estado'] == '8')
                            {
                                $objInfoDocFinCab->setEstadoImpresionFact("Anulado");
                                $em->persist($objInfoDocFinCab);
                                $em->flush();
                                $entityHistorial = new InfoDocumentoHistorial();
                                $entityHistorial->setDocumentoId($objInfoDocFinCab);
                                $entityHistorial->setMotivoId($intMotivoId);
                                $entityHistorial->setFeCreacion(new \DateTime('now'));
                                $entityHistorial->setUsrCreacion($strUser);
                                $entityHistorial->setEstado("Anulado");
                                $em->persist($entityHistorial);
                                $em->flush();
                                $response = new Response(json_encode(array('success' => true, 'mensaje' => 'La factura fue anulada')));

                                /*-------------------------------------------------------------------------------*/
                                /*-SE ENVÍA LA NOTIFICACIÓN AL CLIENTE, AL USUARIO, AL VENDEDOR Y ALIAS ASIGNADO-*/
                                /*-------------------------------------------------------------------------------*/
                                $serviceInfoDocFinanCab->notificaDocumentoAnulado(array("objInfoDocFinCab" => $objInfoDocFinCab,
                                                                                        "intIdEmpleado"    => $intIdEmpleado,
                                                                                        "intIdEmpresa"     => $intIdEmpresa));
                            }
                            else
                            {
                                $response = new Response(json_encode(array(
                                                                            'success' => false, 
                                                                            'mensaje' => "WS Comprobantes: " . $arrayDocumentosElectronicos['txt']
                                                                        )));
                            }
                        }
                        else
                        {
                            $response = new Response(json_encode(array('success' => false, 'mensaje' => 'No se obtuvo respuesta del WS Comprobantes.')));
                        }
                    }
                    else
                    {
                        $response = new Response(json_encode(array('success' => false, 'mensaje' => 'La factura no existe')));
                    }
                    $response->headers->set('Content-type', 'text/json');
                    return $response;
                }
                else
                {
                    $response = new Response(json_encode(array('success' => false, 'mensaje' => 'La factura tiene pagos, no puede ser anulada')));
                    $response->headers->set('Content-type', 'text/json');
                    return $response;
                }
            }
            else
            {
                $response = new Response(json_encode(array('success' => false, 'mensaje' => 'La factura tiene NC, no puede ser anulada')));
                $response->headers->set('Content-type', 'text/json');
                return $response;
            }
       }
       else
       {
                $response = new Response(json_encode(array('success' => false, 'mensaje' => 'Los parametros de anulacion de NC, no han sido guardados')));
                $response->headers->set('Content-type', 'text/json');
                return $response;
        }
    }

    public function editarNumeroSriAjaxAction()
    {
        $request = $this->getRequest();
        $request = $this->get('request');
        $session = $request->getSession();
        $user = $session->get('user');

        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $idfactura = $peticion->get('idfactura');
        $txt_sri = $peticion->get('txt_sri');
        $motivos = $peticion->get('motivos');
        //print_r($array_valor);
        $em = $this->getDoctrine()->getManager("telconet_financiero");
        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idfactura);
        if($entity)
        {
            //Edicion del numero
            $numero_anterior = $entity->getNumeroFacturaSri();
            $observacion = "Modificacion #SRI: #Ant. " . $numero_anterior . " #Act. " . $txt_sri;

            $entity->setNumeroFacturaSri($txt_sri);
            $em->persist($entity);
            $em->flush();

            //Guardando historial de edicion
            $entityHistorial = new InfoDocumentoHistorial();
            $entityHistorial->setDocumentoId($entity);
            $entityHistorial->setMotivoId($motivos);
            $entityHistorial->setFeCreacion(new \DateTime('now'));
            $entityHistorial->setUsrCreacion($user);
            $entityHistorial->setEstado("ModificacionSri");
            $entityHistorial->setObservacion(trim($observacion));
            $em->persist($entityHistorial);
            $em->flush();
            $response = new Response(json_encode(array('success' => true)));
        }
        else
            $response = new Response(json_encode(array('success' => false)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function editarFeEmisionAjaxAction()
    {
        //$request = $this->getRequest();
        $request = $this->get('request');
        $session = $request->getSession();
        $user = $session->get('user');

        //$respuesta = new Response();
        //$respuesta = new Response();
        //$respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $idfactura = $peticion->get('idfactura');
        $fechaEmision = $peticion->get('fechaEmision');
        //echo($fechaEmision); die();
        $motivos = $peticion->get('motivos');
        // print_r($motivos); die();
        if($fechaEmision != "" && $motivos != "")
        {
            $em = $this->getDoctrine()->getManager("telconet_financiero");
            $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idfactura);
            if($entity)
            {
                //Edicion del numero
                $fecha_anterior_ = $entity->getFeEmision();

                $fecha_anterior_1 = explode("/", $fecha_anterior_->format('Y/m/d'));
                $fecha_anterior = $fecha_anterior_1[0] . "/" . $fecha_anterior_1[1] . "/" . $fecha_anterior_1[2];
                // echo($fecha_anterior); die();
                $observacion = "Modificacion #FechaEmision: #Ant. " . $fecha_anterior . " #Act. " . $fechaEmision;


                $entity->setFeEmision(new \DateTime($fechaEmision));
                $em->persist($entity);
                $em->flush();

                //Guardando historial de edicion
                $entityHistorial = new InfoDocumentoHistorial();
                $entityHistorial->setDocumentoId($entity);
                $entityHistorial->setMotivoId($motivos);
                $entityHistorial->setFeCreacion(new \DateTime('now'));
                $entityHistorial->setUsrCreacion($user);
                $entityHistorial->setEstado("ModificadoFeEmi");
                $entityHistorial->setObservacion(trim($observacion));
                $em->persist($entityHistorial);
                $em->flush();
                $response = new Response(json_encode(array('success' => true)));
            }
            else
                $response = new Response(json_encode(array('success' => false)));
            $response->headers->set('Content-type', 'text/json');
            return $response;
        }else
        {

            //$response = new Response(json_encode(array('success'=>false)));
            $response = new Response(json_encode(array('mensaje' => 'Ingresar Fecha o motivo')));
            $response->headers->set('Content-type', 'text/json');
            return $response;
        }
    }

    /*
      GENERACION DE PDF DE FACTURAS Y DE NOTAS DE CREDITO
     */

    /**
     * Muestra el tipo de numeracion SRI dependiendo del tipo FACT o NC	
     * @return twig de busqueda de facturas
     * @author arsuarez
     * @version 1.1 15-11-2014
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @since 1.0
     */
    public function getNumeracionXTipoDocumentoAction()
    {

        $request        = $this->getRequest();
        $session        = $request->getSession();

        $peticion       = $this->get('request');

        $tipoNumeracion = $peticion->query->get('tipo');
        $codEmpresa     = $session->get('idEmpresa');

        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');

        $emComercial = $this->getDoctrine()->getManager();
        $facturas = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findOficinaMatrizYFacturacion($codEmpresa, $tipoNumeracion);

        $facturasArray[] = array('idNumeracion' => $facturas->getId(),
            'numeracion' => $facturas->getNumeracionUno() . '-' . $facturas->getNumeracionDos(),
        );

        $data = '{"total":"' . count($facturas) . '","encontrados":' . json_encode($facturasArray) . '}';

        $response->setContent($data);

        return $response;
    }

    /**
     * Redirecciona a la ventana permite elegir los rangos de las facturas a imprimir masivamente	
     * @return twig de busqueda de facturas
     * @author arsuarez
     */
    public function imprimirFacturasAction()
    {

        $request = $this->getRequest();
        $session = $request->getSession();
        $codEmpresa = $session->get('idEmpresa');

        $form = $this->createForm(new InfoDocumentoFacturaCabType($codEmpresa));

        return $this->render('financieroBundle:InfoDocumentoImprimirFacturasCab:index.html.twig', array(
                'error' => '',
                'form' => $form->createView()
        ));
    }

    /**
     * Redirecciona a la ventana que en lista las facturas generadas masivamente	
     * @return twig de presentacion de facturas generadas en una tabla
     * @author arsuarez
     */
    public function listarFacturasAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        return $this->render('financieroBundle:InfoDocumentoImprimirFacturasCab:show.html.twig', array());
    }

    /**
     * Obtiene la numeracion valida de facturas por empresa   
     * @return La numeracion de las facturas existentes para la empresa en sesion
     * @author arsuarez
     */
    public function getFacturaNumeracionAction()
    {


        $request = $this->getRequest();
        $session = $request->getSession();

        $peticion = $this->get('request');


        $tipoNumeracion = $peticion->query->get('numeracion');
        $codEmpresa = $session->get('idEmpresa');

        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');

        $emComercial = $this->getDoctrine()->getManager();

        $facturas = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findNumeracionXEmpresaYTipo($codEmpresa,"FACT");


        if($facturas)
        {
            $facturasArray = array();

            foreach($facturas as $factura)
            {

                $array = array('id_numeracion' => $factura->getId(),
                    'numeracion' => $factura->getNumeracionUno() . '-' . $factura->getNumeracionDos(),
                );

                $facturasArray[] = $array;
            }

            $data = '{"total":"' . count($facturas) . '","encontrados":' . json_encode($facturasArray) . '}';
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }

        $response->setContent($data);

        return $response;
    }

    /**
     * Documentación para el método 'generarPdfAction'.
     *
     * Funcion que llama al jar que genera las facturas masivamente	                 
     *
     * @return Redirecciona a la ventana de creacion de facturas indicado que ya fueron generadas o no
     *
     * @author Allan Suárez C. <arsuarez@telconet.ec>
     * @version 2.0 02-06-2014
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 2.1 17-11-2014
     * @since 2.0
     */
    public function generarPdfAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $strUsuario         = $objSession->get('user');
        $intIdFactura       = ""; //SOLO APLICABLE PARA LA GENERACION DE NC
        $strPathTelcos      = $this->container->getParameter('path_telcos');

        switch($strPrefijoEmpresa)
        {
            case 'TTCO':
                $strRutaImagen = $strPathTelcos . "telcos/web/public/images/firma_ttco.jpg";
                break;
            case 'TN':
                $strRutaImagen = $strPathTelcos . "telcos/web/public/images/firma_tn.jpg";
                break;
            default:
                $strRutaImagen = $strPathTelcos . "telcos/web/public/images/firma_md.jpg";
        }

        try
        {

            $em_comercial       = $this->getDoctrine()->getManager('telconet');
            $em                 = $this->getDoctrine()->getManager('telconet_financiero');
            
            $arrayParametros    = $objRequest->get('infodocumentofacturacabtype');
            $intIdNumeracion    = $objRequest->get('numeracionHD');
            $strTipoHd          = $objRequest->get('tipoHD');
            $intInicio          = $arrayParametros['inicio'];
            $intFinal           = $arrayParametros['fin'];

            $entityAdmiNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')->find($intIdNumeracion);

            if($entityAdmiNumeracion)
            {
                $strNumeroFacturaSri = $entityAdmiNumeracion->getNumeracionUno() . '-' . $entityAdmiNumeracion->getNumeracionDos();
            }

            switch($strTipoHd)
            {
                case 'FACT':
                    $entityInfoDocFinancieroCab = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->existenFacturas($strNumeroFacturaSri, 
                                                                                                                                 $intInicio, 
                                                                                                                                 $intFinal, 
                                                                                                                                 $strTipoHd, 
                                                                                                                                 $intIdEmpresa);
                    break;
                case 'NCE':
                    $entityInfoDocFinancieroCabNc = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->existenFacturas($strNumeroFacturaSri, 
                                                                                                                                   $intInicio, 
                                                                                                                                   $intFinal, 
                                                                                                                                   'NC', 
                                                                                                                                   $intIdEmpresa);
                    break;
                default:
                    $entityInfoDocFinancieroCabNc = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->existenNC($intIdEmpresa);
            }



            $strLogin = ""; //Login solo es necesario cuando son impresiones manuales
            //Obtengo el usario en sesion y la oficina del mismo
            $entityInfoPersona              = $em_comercial->getRepository('schemaBundle:InfoPersona')
                                                           ->findOneBy(array('login' => $objSession->get('user')));
            $entityInfoPersonaEmpRol    = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->getOficinaXEmpresaYUser($intIdEmpresa, $entityInfoPersona->getId());
            $entityInfoPersonaEmpresaRol    = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                           ->find($entityInfoPersonaEmpRol[0]['id']);
            if($entityInfoPersonaEmpresaRol)
            {
                $intIdOficina = $entityInfoPersonaEmpresaRol->getOficinaId()->getId();
            }
            else
            {
                $intIdOficina = '';
            }

            if($strTipoHd != 'FACT')
            {
                $intTotalNc = $strTipoHd == 'NCE' ? count($entityInfoDocFinancieroCabNc) : $entityInfoDocFinancieroCabNc[0]['num'];
            }

            if($strTipoHd == 'FACT')//Para Factura
            {
                $strDescripcion = $arrayParametros['descripcion'];
                $strObservacion = $objRequest->get('observacion');
                $strFechaLimite = $objRequest->query->get('fechaLimite');
            }
            else if($strTipoHd == 'NC') //Para NC sin Numeracion
            {
                $strDescripcion = "";
                $strObservacion = "";
                $strFechaLimite = "";
                $strRutaImagen  = "";
                $strNumeroFacturaSri    = "";
                $intInicio              = "";
                $intFinal               = "";
            }
            else if($strTipoHd == 'NCE')//Para NC por Rango
            {
                $strDescripcion = "";
                $strObservacion = "";
                $strFechaLimite = "";
                $strRutaImagen  = "";
                $strTipoHd      = 'NC';
            }

            $strHostScripts = $this->container->getParameter('host_scripts');
            $strAplicacion  = 'FACTPDF'; //Tipo de Script que se va a ejecutar
            // Cadena de parametros a enviarse 
            $arrayParametros = $strRutaImagen . "|" . $strDescripcion . "|" . 
                               $intIdEmpresa . "|" . $strNumeroFacturaSri . "|" . $intInicio . "|" . $intFinal . "|" . $strLogin . "|" . 
                               $strObservacion . "|" . $strFechaLimite . "|" . $strTipoHd . "|" . $intIdOficina . "|" . $intIdFactura . "|" . 
                               $strUsuario . "|" . $strHostScripts;

            if($strTipoHd == 'FACT')
            {

                if($entityInfoDocFinancieroCab && count($entityInfoDocFinancieroCab) > 0)
                {

                    $strCommand = "nohup java -jar -Djava.security.egd=file:/dev/./urandom " . $strPathTelcos . "telcos/src/telconet/financieroBundle/batch/TelcosGestionScripts.jar '" . 
                                  $strAplicacion . "' '" . $arrayParametros . "' 'NO' '" . $strHostScripts . "' '" . $strPathTelcos . "'> " . 
                                  $strPathTelcos . "telcos/src/telconet/financieroBundle/batch/ejecucionApp.txt &";

                    shell_exec($strCommand);

                    $strMensaje = "La facturas serán generadas";
                }
                else
                {

                    $strMensaje = "Las Facturas Seleccionadas no existen";
                }
            }
            else if($strTipoHd == 'NC')
            {
                if($intTotalNc)
                {

                    $strCommand = "nohup java -jar -Djava.security.egd=file:/dev/./urandom " . $strPathTelcos . "telcos/src/telconet/financieroBundle/batch/TelcosGestionScripts.jar '" . 
                                  $strAplicacion . "' '" . $arrayParametros . "' 'NO' '" . $strHostScripts . "' '" . $strPathTelcos . "'> " . 
                                  $strPathTelcos . "telcos/src/telconet/financieroBundle/batch/ejecucionApp.txt &";

                    shell_exec($strCommand);

                    $strMensaje = "Las Notas de Crédito serán generadas";
                }
                else
                {

                    $strMensaje = "No existen Notas de Credito a Procesar";
                }
            }
            $form = $this->createForm(new InfoDocumentoFacturaCabType($intIdEmpresa));

            return $this->render('financieroBundle:InfoDocumentoImprimirFacturasCab:index.html.twig', array(
                    'error' => $strMensaje,
                    'form' => $form->createView()
            ));
        }
        catch(Exception $e)
        {
            return $this->render('financieroBundle:InfoDocumentoImprimirFacturasCab:index.html.twig', array(
                    'error' => 'Existio un error - ' . $e->getMessage(),
                    'form' => $form->createView()
            ));
        }
    }

    /**
     * Genera el grid que muestra las facturas generadas masivamente    
     * @return Devuelve a la pagina dond ese muestran las facturas generas para ser descargadas como PDF o ZIP
     * @author arsuarez
     */
    public function gridFacturasAction()
    {
        $request = $this->getRequest();
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");

        $path_telcos = $this->container->getParameter('path_telcos');

        $mes = $request->get('mes');
        $anio = $request->get('anio');

        $tipo = $request->get('tipo');

        if($mes && $anio)
        {
            if(strlen($mes) == 1)
                $mes = "0" . $mes;
            else
                $mes = $mes;
            $criterioFecha = $anio . "-" . $mes;
        }
        else
        {
            $criterioFecha = date('Y-m');
        }
        //$user = $this->get('security.context')->getToken()->getUser();
        $idEmpresa = $request->getSession()->get('idEmpresa');

        $finder = new Finder();
        $em = $this->get('doctrine')->getManager('telconet');
        $entityEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($idEmpresa);

        $prefijo = strtoupper($entityEmpresa->getPrefijo());

        if($tipo)
        {
            if($tipo == 'FACT')
                $finder->name($prefijo . "-facturas-*" . $criterioFecha . "*.pdf")->files()->in($path_telcos . "telcos/web/public/uploads/Facturas/");
            else
                $finder->name($prefijo . "-notasCredito-*" . $criterioFecha . "*.pdf")->files()->in($path_telcos . "telcos/web/public/uploads/NotasCredito/");
            $finder->sortByChangedTime();
        }

        if($finder)
        {
            foreach($finder as $file)
            {

                $zip = substr($file->getRelativePathname(), 0, -3) . 'zip';

                if($tipo == 'FACT')
                {
                    $urlArchivo = '/public/uploads/Facturas/' . $file->getRelativePathname();
                    $urlArchivoZip = '/public/uploads/Facturas/' . $zip;
                }
                else
                {
                    $urlArchivo = '/public/uploads/NotasCredito/' . $file->getRelativePathname();
                    $urlArchivoZip = '/public/uploads/NotasCredito/' . $zip;
                }

                $arreglo[] = array(
                    'linkVer' => $file->getRelativePathname(),
                    // 'linkFile' => $urlArchivo,
                    'linkFileZip' => $urlArchivoZip,
                    'size' => (round(filesize($file->getRealpath()) / 1024 / 1024, 2)) . ' Mb'
                );
            }
        }

        $response = new Response(json_encode(array('clientes' => $arreglo)));

        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /*     * Descarga archivo PDF para impresion
     * @param integer $archivo   
     * @return Archivo generado para la descarga
     * @author arsuarez
     */

    public function downloadArchivoImpresionAction($archivo)
    {
        $path_telcos = $this->container->getParameter('path_telcos');

        $path = $path_telcos . "telcos/web/public/uploads/Facturas/" . $archivo;

        $content = file_get_contents($path);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $archivo);

        $response->setContent($content);
        return $response;
    }
    
    
    /**
     * Documentacion para el método 'getOficinasFacturacionAction'
     * 
     * El metodo getOficinasFacturacionAction retorna las oficinas de facturación por empresa en formato json
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 17-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-09-2016 - Se actualiza la función para que nos retorne si la oficina de facturación debe compensar
     * 
     * @return json $jsonOficinas Retorna el json de las oficinas
     */
    public function getOficinasFacturacionAction()
    {
        $jsonResultado      = new JsonResponse();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $emComercial        = $this->getDoctrine()->getManager();
        $emGeneral          = $this->getDoctrine()->getManager();
        $arrayOficinas      = array();
        $intIdTotal         = 0;
        $strNombreParametro = "CANTONES_OFICINAS_COMPENSADAS";
        $objEmpresa         = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($intIdEmpresa);
        $objOficinas        = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                          ->findBy( array( 'estado'               => self::ESTADO_ACTIVO,
                                                           'esOficinaFacturacion' => 'S',
                                                           'empresaId'            => $objEmpresa ) );
        
        $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy( array('estado'          => self::ESTADO_ACTIVO,
                                                                                                        'nombreParametro' => $strNombreParametro) );
        
        if( $objOficinas )
        {
            foreach( $objOficinas as $objOficina )
            {
                $arrayItem                     = array();
                $arrayItem['intIdOficina']     = $objOficina->getId();
                $arrayItem['strNombreOficina'] = $objOficina->getNombreOficina();
                $arrayItem['strEsCompensado']  = "N";
                
                if( $objParametroCab != null )
                {
                    $objParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findOneBy( array( 'estado'      => self::ESTADO_ACTIVO,
                                                                     'parametroId' => $objParametroCab,
                                                                     'valor1'      => $arrayItem['strNombreOficina'] ) );
                    
                    if( $objParametroDet != null )
                    {
                        $arrayItem['strEsCompensado'] = 'S';
                    }//( $objParametroDet != null )
                }//( $objParametroCab != null )
                
                $arrayOficinas[] = $arrayItem;
                
                $intIdTotal++;
            }
        }
        
        $jsonResultado->setData( array('total' => $intIdTotal, 'encontrados' => $arrayOficinas) );
        
        return $jsonResultado;
    }
    
    
    /**
     * Documentacion para el método 'getNumeracionesFacturacionAction'
     * 
     * El metodo getNumeracionesFacturacionAction retorna las numeraciones de facturación por oficina en formato json
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 30-12-2015
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-06-2017 - Se valida que si los secuenciales uno y dos están llenos se tome la numeración de dichos campos, caso contrario se
     *                           tome la numeración del numero de autorización.
     * 
     * @return json $jsonOficinas Retorna el json de las numeraciones
     */
    public function getNumeracionesFacturacionAction()
    {
        $jsonResultado     = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $intIdEmpresa      = $objSession->get('idEmpresa');
        $emComercial       = $this->getDoctrine()->getManager();
        $arrayNumeraciones = array();
        $intTotal          = 0;
        $intIdOficina      = $objRequest->query->get('oficina') ? $objRequest->query->get('oficina') : 0;
        $objNumeraciones   = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findBy( array( 'estado'    => self::ESTADO_ACTIVO,
                                                                                                        'oficinaId' => $intIdOficina,
                                                                                                        'empresaId' => $intIdEmpresa,
                                                                                                        'codigo'    => array('FAC', 'FACE') ) 
                                                                                               );
        
        if ( $objNumeraciones )
        {
            foreach ( $objNumeraciones as $objNumeracion )
            {
                if ( is_object($objNumeracion) )
                {
                    $strNumeracionUno      = $objNumeracion->getNumeracionUno();
                    $strNumeracionDos      = $objNumeracion->getNumeracionDos();
                    $strNumeroAutorizacion = $objNumeracion->getNumeroAutorizacion();

                    $arrayItem                    = array();
                    $arrayItem['intIdNumeracion'] = $objNumeracion->getId();
                    $arrayItem['strNumeracion']   = '';
                    
                    if( !empty($strNumeracionUno) && !empty($strNumeracionDos) )
                    {
                        $arrayItem['strNumeracion'] = $strNumeracionUno.'-'.$strNumeracionDos;
                    }
                    elseif( !empty($strNumeroAutorizacion) )
                    {
                        $arrayItem['strNumeracion'] = $strNumeroAutorizacion;
                    }
                    else
                    {
                        $arrayItem['strNumeracion'] = '';
                    }

                    $arrayNumeraciones[] = $arrayItem;

                    $intTotal++;
                }// ( is_object($objNumeracion) )
            }//foreach( $objNumeraciones as $objNumeracion )
        }// ( $objNumeraciones )
        
        $jsonResultado->setData( array('total' => $intTotal, 'encontrados' => $arrayNumeraciones) );
        
        return $jsonResultado;
    }
    
    
    /**
     * Documentacion para el método 'getNumeroFacturaAction'
     * 
     * Método que retorna el número de factura dependiendo de la oficina seleccionada
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 17-12-2015
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 27-06-2017 - Se valida que si los secuenciales uno y dos están llenos se tome la numeración de dichos campos, caso contrario se
     *                           tome la numeración del numero de autorización.
     *                           Se implementa por parámetros 'SECUENCIALES_POR_EMPRESA' obtener la cantidad máxima por la cual debe estar conformada
     *                           el secuencial de la factura.
     */
    public function getNumeroFacturaAction()
    {
        $jsonResultado       = new JsonResponse();
        $objRequest          = $this->getRequest();
        $intIdNumeracion     = $objRequest->request->get('intIdNumeracion') ? $objRequest->request->get('intIdNumeracion') : 0;
        $objSession          = $objRequest->getSession();
        $strPtoCliente       = $objSession->get('ptoCliente');
        $strCodEmpresa       = $objSession->get('idEmpresa');
        $emComercial         = $this->getDoctrine()->getManager();
        $emGeneral           = $this->getDoctrine()->getManager();
        $boolError           = true;
        $strMensaje          = 'No se encontro Número de Facturación para la oficina seleccionada';
        $strNumeroFactura    = '';
        
        /**
         * Bloque que retorna el máximo tamaño que debe tener el secuencial de la factura de la empresa en sessión
         */
        $intTamañoSecuencial = 0;

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
                                                       $strCodEmpresa);

        if ( isset($arraySecuencialDocumento['valor1']) && !empty($arraySecuencialDocumento['valor1']) )
        {
            $intTamañoSecuencial = $arraySecuencialDocumento['valor1'];
        }// ( isset($arraySecuencialDocumento['valor1']) && !empty($arraySecuencialDocumento['valor1']) )

        //valida que un login este en sesion
        if($strPtoCliente)
        {
            //Como el punto cliente existe se debe verificar si es pto de facturacion
            $entityPuntoAdicional = $emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($strPtoCliente['id']);

            //verifica que la entidad tenga datos
            if($entityPuntoAdicional)
            {
                //valida que sea el punto sea padre de facturacion
                if($entityPuntoAdicional->getEsPadreFacturacion() == 'S')
                {
                    if( $intIdNumeracion )
                    {
                        $objNumeracion = $emComercial->getRepository('schemaBundle:AdmiNumeracion')->findOneById($intIdNumeracion);
                        
                        if ( is_object($objNumeracion) )
                        {
                            $strSecuencia          = str_pad($objNumeracion->getSecuencia(), $intTamañoSecuencial, "0", STR_PAD_LEFT);
                            $strNumeracionUno      = $objNumeracion->getNumeracionUno();
                            $strNumeracionDos      = $objNumeracion->getNumeracionDos();
                            $strNumeroAutorizacion = $objNumeracion->getNumeroAutorizacion();

                            if( !empty($strNumeracionUno) && !empty($strNumeracionDos) )
                            {
                                $strNumeroFactura = $strNumeracionUno."-".$strNumeracionDos."-".$strSecuencia;
                            }
                            elseif( !empty($strNumeroAutorizacion) )
                            {
                                $strNumeroFactura = $strNumeroAutorizacion."-".$strSecuencia;
                            }
                            else
                            {
                                $strNumeroFactura = "";
                            }

                            $boolError  = false;
                            $strMensaje = '';
                        }// ( is_object($objNumeracion) )
                    }//( $intIdNumeracion )
                }//($entityPuntoAdicional->getEsPadreFacturacion() == 'S')
            }//($entityPuntoAdicional)
        }//($strPtoCliente)
        
        $jsonResultado->setData( array('error' => $boolError, 'mensaje' => $strMensaje, 'numeroFactura' => $strNumeroFactura) );
        
        return $jsonResultado;
    }

    
    /**
     * Documentación para funcion 'getImpuestos'.
     * 
     * Funcion que retorna los impuestos dependiendo de los criterios enviados por el usuario en formato JSON
     * 
     * @return Response $objResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0
     * @since 20-06-2016
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 28-06-2017 - Se valida por país el impuesto del IVA que se desea obtener.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.2 28-02-2019 - Se modifica impuestos para que tome la configuración de Guatemala.
     */     
    public function getImpuestosAction() 
    {
        $objResponse      = new Response();
        $emGeneral        = $this->get('doctrine')->getManager('telconet_general');
        $objRequest       = $this->get('request');
        $objSession       = $objRequest->getSession();
        $intIdPaisSession = $objSession->get('intIdPais');
        $strPaisSession   = $objSession->get('strNombrePais');
        $strTipoImpuesto  = 'IVA';


        if ( strtoupper($strPaisSession) !=  "ECUADOR" && strtoupper($strPaisSession) !=  "GUATEMALA")
        {
            $strTipoImpuesto = 'ITBMS';
        }

        if ( strtoupper($strPaisSession) ==  "GUATEMALA")
        {
            $strTipoImpuesto = 'IVA_GT';
        }
        
        $jsonImpuestos = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                   ->getJSONImpuestosByCriterios( array('strTipoImpuesto' => $strTipoImpuesto, 'intIdPais' => $intIdPaisSession) );
    
        $objResponse->setContent($jsonImpuestos);
        
        return $objResponse;
    }
    
    
    /**
     * Documentación para funcion 'getSolicitudReprocesoAjax'.
     * 
     * Funcion que retorna la solicitud de cargo por reproceso del punto en sesión en formato JSON
     * 
     * @return Response $objResponse
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0
     * @since 03-03-2017
     */     
    public function getSolicitudReprocesoAjaxAction() 
    {
        $objResponse = new Response();
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $arrayPtoCliente    = $objSession->get('ptoCliente');
        $intIdPtoSesion     = $arrayPtoCliente['id'];
        
        $em                 = $this->get('doctrine')->getManager('telconet');
        
        $jsonSolicitudReproceso = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->getJsonSolicitudReproceso($intIdPtoSesion);
    
        $objResponse->setContent($jsonSolicitudReproceso);
        
        return $objResponse;
    }
    
        /**
     * Documentación para funcion 'apiInterfazPanama'.
     * 
     * Funcion que retorna la solicitud de cargo por reproceso del punto en sesión en formato JSON
     * 
     * @return Response $objResponse
     * 
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.0
     * @since 07-05-2018
     */     
    public function apiInterfazPanamaAction() 
    {
        
        $arrayParametros = array();
        $objRequest      = $this->getRequest();
        $intIdDocumento  = $objRequest->get('id');
        $objSession      = $objRequest->getSession();
        $strCodEmpresa   = $objSession->get('idEmpresa');
        $objResponse     = new Response();
        $arrayParametros['intIdDocumento']= $intIdDocumento;
        $arrayParametros['strCodEmpresa'] = $strCodEmpresa;
        
        $objEmFinanciero = $this->get('doctrine')->getManager('telconet');
        
        if( isset($arrayParametros) && !empty($arrayParametros))
        {
            $arrayDatos= $objEmFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                          ->consumeApiInterfazPanama($arrayParametros);
            $objResponse->setContent(json_encode(array('strCodError'  => $arrayDatos['strCodError'],
                                                          'strMensaje'   => $arrayDatos['strMensaje'])));
            $objResponse->headers->set('Content-type', 'text/json');
            return $objResponse;            
        }
    }
    
     /**
     * Documentación para funcion 'facturaElectronicaGt'.
     * 
     * Funcion para realizar conversion a moneda quetzal y facturacion electronica.
     * 
     * @return Response $objResponse
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0
     * @since 28-02-2019
     */     
    public function facturaElectronicaGtAction() 
    {        
        $arrayParametrosTipoCambio = array();
        $objRequest                = $this->getRequest();
        $objServiceUtil            = $this->get('schema.Util');
        $strIpCreacion             = $objRequest->getClientIp();
        $emFinanciero              = $this->getDoctrine()->getManager("telconet_financiero");
        $emComercial               = $this->getDoctrine()->getManager();
        $intIdDocumento            = $objRequest->get('intIdDocumento');
        $strTipoDocumento          = $objRequest->get('strTipoDocumento');
        $emGeneral                 = $this->getDoctrine()->getManager("telconet_general");
        $objSession                = $objRequest->getSession();
        $strEmpresaCod             = $objSession->get('idEmpresa');
        $arrayCliente              = $objSession->get('cliente');
        $arrayPtoCliente           = $objSession->get('ptoCliente');
        $intIdPaisSession          = $objSession->get('intIdPais');
        $strUsrSession             = $objSession->get('user');
        $strContactoCorreo         = "N/A";
        $strContactoTelefono       = "N/A";
        $arrayDatosFace            = array();
        $strNoAplica               = 'N/A';
        $strSinValor               = '0.00';
        $boolMessaje               = false;
        $boolPagaIva                = false;
        
        try
        {    
            /**
            * Bloque que retorna el tipo de cambio según la fecha de emisión de la factura,
            * para conversión de dolar a Quetzales.
            */
            $objResponse                    = new Response();
            $objInfoDocumentoFinancieroCab  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                        ->find($intIdDocumento);

            if(!$objInfoDocumentoFinancieroCab)
            {
                throw $this->createNotFoundException('No se puede encontrar la entidad InfoDocumentoFinancieroCab.');
            }

            $objFeEmision = $objInfoDocumentoFinancieroCab->getFeEmision();
            $strFeEmision = date_format($objFeEmision, "d/m/Y");

            $arrayParametrosTipoCambio = array('intIdDocumento'      => $intIdDocumento,
                                                'strFechaEmision'     => $strFeEmision,
                                                'strUsrSession'       =>  $strUsrSession);

            $arrayResultadoTipoCambio  = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                      ->getTipoCambio($arrayParametrosTipoCambio);


            if($arrayResultadoTipoCambio['strCodError']=='OK')
            {
                 /*
                  * Bloque que retorna los parametros configurados para la facturación electrónica.
                  */
                 $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get("WEB_SERVICE_VARIABLES", "FINANCIERO", "", "", "", "", "", "","",$strEmpresaCod);

                 if ( !empty($arrayParametroDet) )
                 {
                     foreach ( $arrayParametroDet as $arrayOpcion )
                     {
                         if ( isset($arrayOpcion['valor1']) && !empty($arrayOpcion['valor1']) && isset($arrayOpcion['descripcion'])
                              && !empty($arrayOpcion['descripcion']) )
                         {
                              if($arrayOpcion['descripcion']=='fechaResolucion')
                              {
                                 $strFechaResolucionDocXml                    = date("c", strtotime($arrayOpcion['valor1']));

                                 $arrayDatosFace[$arrayOpcion['descripcion']] = $strFechaResolucionDocXml;
                              }
                              else
                              { 
                                  if($arrayOpcion['valor2']==$strTipoDocumento || empty($arrayOpcion['valor2']))
                                  {
                                       $arrayDatosFace[$arrayOpcion['descripcion']] = $arrayOpcion['valor1'];
                                  }

                              }
                         }// ( isset($arrayOpcion['valor1']) && !empty($arrayOpcion['valor1']) && isset($arrayOpcion['descripcion'])...
                     }//foreach ( $arrayParametroDet as $arrayOpcion )
                 }// ( !empty($arrayParametroDet) )

                 /**
                  * Bloque que retorna los datos del cliente.
                 */
                 $arrayClienteContactos = $emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                      ->getFormasContactoParaSession($arrayCliente['id_persona']);   

                 if ( !empty($arrayClienteContactos) )
                 {
                     foreach ( $arrayClienteContactos as $arrayContacto )
                     {
                         if ( $arrayContacto['formaContacto']=='Correo Electronico')
                         { 
                             $strContactoCorreo   = $arrayContacto['valor'];
                         }
                         else if ( $arrayContacto['formaContacto']=='Telefono Fijo')
                         {
                             $strContactoTelefono = $arrayContacto['valor'];
                         }// ( $arrayContacto['formaContacto']=='Correo Electronico')
                     }
                        $arrayDatosFace['correoComprador']   = $strContactoCorreo;
                        $arrayDatosFace['telefonoComprador'] = $strContactoTelefono;
                 }// ( !empty($arrayClienteContactos) )

                 if ( !empty($arrayCliente) )
                 {
                          $arrayDatosFace['nitComprador']                = $arrayCliente['identificacion'];
                          $arrayDatosFace['nombreComercialComprador']    = (!empty( $arrayCliente['razon_social'])) ?  $arrayCliente['razon_social'] :  $arrayCliente['nombres']." ".$arrayCliente['nombres'];
                          $arrayDatosFace['direccionComercialComprador'] = $arrayCliente['direccion'];
                 }// ( !empty($arrayCliente) )

                 $objPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                         ->findOneById($arrayPtoCliente['id']);

                 if($objPunto && $objPunto->getSectorId() != null)
                 {
                            $objSector                                  = $objPunto->getSectorId();
                            if ( is_object($objSector) )
                            {
                                $objParroquia                           = $objSector->getParroquiaId();
                            }
                            if ( is_object($objParroquia) )
                            {
                               $objCanton                               = $objParroquia->getCantonId();
                            }
                            if ( is_object($objCanton) )
                            {
                               $strCiudad                               = $objCanton->getNombreCanton();
                            }
                            $strParroquia                               = $objParroquia->getNombreParroquia();
                            $arrayDatosFace['departamentoComprador']    = $strCiudad;
                            $arrayDatosFace['municipioComprador']       = $strParroquia;

                 }

                 /**
                 * Bloque que retorna los datos de la empresa TNG.
                 */
                 $objEmpresaGrupo = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                ->find($strEmpresaCod);

                 if($objEmpresaGrupo)
                 {
                      $arrayDatosFace['nombreCompletoVendedor']             = $objEmpresaGrupo->getNombreEmpresa();
                      $arrayDatosFace['nombreComercialRazonSocialVendedor'] = $objEmpresaGrupo->getRazonSocial();
                      $arrayDatosFace['nitVendedor']                        = $objEmpresaGrupo->getRuc();
                 }// ($objEmpresaGrupo)

                 $objOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                 ->findOneById($strEmpresaCod);

                 if($objOficinaGrupo)
                 {
                     $arrayDatosFace['direccionComercialVendedor'] = $objOficinaGrupo->getDireccionOficina();
                     $strCantonId                                  = $objOficinaGrupo->getCantonId();
                     $objCanton                                    = $emGeneral->getRepository('schemaBundle:AdmiCanton')
                                                                               ->findOneById($strCantonId);
                     $objParroquia                                 = $emGeneral->getRepository('schemaBundle:AdmiParroquia')
                                                                               ->findOneById($strCantonId);
                     if($objCanton)
                     {
                         $arrayDatosFace['departamentoVendedor']   = $objCanton->getNombreCanton();
                     }
                     if($objParroquia)
                     {
                         $arrayDatosFace['municipioVendedor']      = $objParroquia->getNombreParroquia();
                     }
                 }// ($objOficinaGrupo)

                 /**
                 * Bloque que retorna los datos de la cabecera del Documento.
                 */
                 $objAdmiCaracteristica              = $emGeneral->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy(array('estado'=> 'Activo', 
                                                                                    'descripcionCaracteristica' => 'VALOR DOC QUETZALES'));

                 if($objAdmiCaracteristica)
                 {
                     $objInfoDocumentoCaracteristica = $emFinanciero->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                                    ->findOneBy(array('documentoId'     => $intIdDocumento, 
                                                                                      'caracteristicaId' => $objAdmiCaracteristica->getId()));

                     if( is_object($objInfoDocumentoCaracteristica) )
                     {
                         if($objInfoDocumentoCaracteristica->getValor())
                         {
                             $strQuetzalValor = $objInfoDocumentoCaracteristica->getValor();
                         }
                         else 
                         {
                             $boolMessaje  = true;
                             $strMensaje   = 'El campo quezal se encuentra vacio';
                         }// ($objInfoDocumentoCaracteristica->getValor())
                     }
                     else 
                     {
                         $boolMessaje = true;
                         $strMensaje  = 'No existe el valor del documento en quetzales';
                     }// ($objInfoDocumentoCaracteristica)
                 }// ($objAdmiCaracteristica)

                 $objAdmiPais = $emGeneral->getRepository("schemaBundle:AdmiPais")->findOneById($intIdPaisSession);

                 if ( is_object($objAdmiPais) )
                 {
                     $objAdmiImpuestoIva = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                     ->findOneBy( array('tipoImpuesto' => 'IVA_GT', 
                                                                         'estado'       => 'Activo',
                                                                         'paisId'       => $objAdmiPais) );
                 }// ( is_object($objAdmiPais) )

                 $strIva                        = $objAdmiImpuestoIva->getPorcentajeImpuesto();
                 $strQuetzalIva                 = $strQuetzalValor * "0.{$strIva}";
                 $strQuetzalValorIva            = $strQuetzalValor * "1.{$strIva}";
                 $objInfoPersonaEmpresaRol      = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->find($arrayCliente['id_persona_empresa_rol']);
                 if($objInfoPersonaEmpresaRol->getPersonaId()->getPagaIva()=='S')
                 {
                    $boolPagaIva                = true;
                 }

                 $arrayDatosFace['regimen2989'] = $boolPagaIva;

                 if($objInfoDocumentoFinancieroCab)
                 {      
                        $strNumeroFactSriCompleto                = $objInfoDocumentoFinancieroCab->getNumeroFacturaSri();
                        $arrayNumeroFactSri                      = explode("-",$strNumeroFactSriCompleto);
                        $strSecuencial                           = ltrim($arrayNumeroFactSri[2],"0");
                        $strTipoCambio                           = $strQuetzalValor/$objInfoDocumentoFinancieroCab->getSubtotal();
                        $strRetencion                            = $objInfoDocumentoFinancieroCab->getEntregoRetencionFte()== 'N' ? 'NO_RET_DEFINITIVA' : 'RET_DEFINITIVA';
                        $strFeEmisionDoc                         = date_format($objFeEmision, "Y-m-d H:i:s");
                        $strFeEmisionDocXml                      = date("Y-m-d H:i:s", strtotime($strFeEmisionDoc)); 
                        $strFeEmisionDocXml                      = date("c", strtotime($strFeEmisionDocXml));
                        $strImporteDescuento                     = ($objInfoDocumentoFinancieroCab->getDescuentoCompensacion() * $strTipoCambio);
                        $arrayDatosFace['regimenISR']            = $strRetencion;
                        $arrayDatosFace['numeroDocumento']       = $strSecuencial;
                        $arrayDatosFace['montoTotalOperacion']   = round( $strQuetzalValorIva  , 2);
                        $arrayDatosFace['importeTotalExento']    = $strSinValor;
                        $arrayDatosFace['importeOtrosImpuestos'] = $strSinValor;
                        $arrayDatosFace['importeNetoGravado']    = round( $strQuetzalValorIva  , 2);
                        $arrayDatosFace['importeDescuento']      = round( $strImporteDescuento , 2);
                        $arrayDatosFace['importeBruto']          = round( $strQuetzalValor  , 2);
                        $arrayDatosFace['fechaDocumento']        = $strFeEmisionDocXml;
                        $arrayDatosFace['fechaAnulacion']        = $strFeEmisionDocXml;
                        $arrayDatosFace['estadoDocumento']       = 'ACTIVO';
                        $arrayDatosFace['detalleImpuestosIva']   = round( $strQuetzalIva  , 2);
                 }// ($objInfoDocumentoFinancieroCab)

                 /**
                 * Bloque que retorna los datos del detalle del Documento.
                 */
                 $objInfoDocumentoFinancieroDet   = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                                 ->findByDocumentoId($intIdDocumento);

                 if($objInfoDocumentoFinancieroDet)
                 {  
                     $intCont = 1;
                     foreach($objInfoDocumentoFinancieroDet as $objFactDetalle)
                     {
                         $intCantidad             = $objFactDetalle->getCantidad();
                         $strTotalProducto        = $strTipoCambio * $objFactDetalle->getPrecioVentaFacproDetalle();
                         $strTotalProductoCant    = $strTotalProducto * $intCantidad;
                         $strDetalleImpuestosIva  = $strTotalProductoCant* "0.{$strIva}";
                         $strImporteNetoGravado   = $strTotalProductoCant* "1.{$strIva}";
                         $strMontoDescuento       = ($objFactDetalle->getDescuentoFacproDetalle() * $strTipoCambio);
                         $strPrecioUnitario       = ($objFactDetalle->getPrecioVentaFacproDetalle()* $strTipoCambio);
                         $objProducto             = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                             ->findOneById($objFactDetalle->getProductoId());

                         if( is_object($objProducto) )
                         { 
                             $strDetalleProducto  = $objProducto->getDescripcionProducto();
                         }
                         $arrayDatosFace['detalleDte'][$intCont]['cantidad']              = $intCantidad;
                         $arrayDatosFace['detalleDte'][$intCont]['codigoProducto']        = $objFactDetalle->getProductoId();
                         $arrayDatosFace['detalleDte'][$intCont]['descripcionProducto']   = $strDetalleProducto;
                         $arrayDatosFace['detalleDte'][$intCont]['detalleImpuestosIva']   = round( $strDetalleImpuestosIva  , 2);
                         $arrayDatosFace['detalleDte'][$intCont]['importeExento']         = $strSinValor;
                         $arrayDatosFace['detalleDte'][$intCont]['importeNetoGravado']    = round( $strImporteNetoGravado  , 2);
                         $arrayDatosFace['detalleDte'][$intCont]['importeOtrosImpuestos'] = $strSinValor;
                         $arrayDatosFace['detalleDte'][$intCont]['importeTotalOperacion'] = round( $strImporteNetoGravado  , 2);
                         $arrayDatosFace['detalleDte'][$intCont]['montoBruto']            = round( $strTotalProductoCant  , 2);
                         $arrayDatosFace['detalleDte'][$intCont]['montoDescuento']        = round( $strMontoDescuento  , 2);
                         $arrayDatosFace['detalleDte'][$intCont]['personalizado_01']      = $strNoAplica;
                         $arrayDatosFace['detalleDte'][$intCont]['personalizado_02']      = $strNoAplica;
                         $arrayDatosFace['detalleDte'][$intCont]['personalizado_03']      = $strNoAplica;
                         $arrayDatosFace['detalleDte'][$intCont]['personalizado_04']      = $strNoAplica;
                         $arrayDatosFace['detalleDte'][$intCont]['personalizado_05']      = $strNoAplica;
                         $arrayDatosFace['detalleDte'][$intCont]['personalizado_06']      = $strNoAplica;
                         $arrayDatosFace['detalleDte'][$intCont]['precioUnitario']        = round( $strPrecioUnitario  , 2);
                         $arrayDatosFace['detalleDte'][$intCont]['tipoProducto']          = 'S';
                         $arrayDatosFace['detalleDte'][$intCont]['unidadMedida']          = 'UND';

                         $intCont++; 
                     }
                 }// ($objInfoDocumentoFinancieroDet)

                 /**
                  * Bloque que realiza la facturación electrónica de Guatemala.
                  */

                 if (!$boolMessaje)
                 {

                  /**
                  * Bloque que guarda los datos en la tabla INFO_COMPROBANTE_ELECTRONICO
                  */
                    $objInfoDocumentoFinancieroCab = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIdDocumento);
                     if($objInfoDocumentoFinancieroCab)
                     {
                         $objInfoDocumentoFinancieroCab->setEstadoImpresionFact("Activo");
                         $emFinanciero->persist($objInfoDocumentoFinancieroCab);
                         $emFinanciero->flush();
                     }
                         $strMensaje='Documento procesado';

                 }// (!$boolMessaje)

                 $objResponse->setContent(json_encode(array('boolCodError'  =>  $boolMessaje,
                                                             'strMensaje'    =>  $strMensaje)));

            }
            else
            {
                 $objResponse->setContent(json_encode(array('strCodError'  => 'error',
                                                     'strMensaje'   => $arrayResultadoTipoCambio['strMensaje'])));
               
            }// ($arrayResultadoTipoCambio['strMensaje']=='OK')

           
        
        }
        catch( \Exception $ex )
        {
            $objResponse->setContent(json_encode(array('boolCodError'  =>  'error',
                                                        'strMensaje'    =>  'Error Interno. Favor notificar a Sistemas.'.$ex->getMessage())));
            
            $objServiceUtil->insertError( 'Telcos+', 
                                                       'Facturación Electrónica Guatemala', 
                                                       'Error al procesar la facturación electrónica. '.$ex->getMessage(), 
                                                        $strUsrSession, 
                                                        $strIpCreacion );
            
        }
        
        $objResponse->headers->set('Content-type', 'text/json');        
        return $objResponse;    
    }

    /**
     * @Secure(roles="ROLE_67-7877")
     * Documentación para funcion 'detalleFacturaModificarAction'.
     * 
     * Funcion usada para modificar los detalles de una factura
     * 
     * @return Response $objResponse
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.0
     * @since 27-01-2021
     *  
     */
    public function detalleFacturaModificarAction()
    {
        $objRequest                 = $this->getRequest();   
        $objResponse                = new Response();
        $objServiceUtil             = $this->get('schema.Util');
        $arrayObjFac                = $objRequest->get("arrayDetallesFactura");
        $arrayDetFac                = json_decode($arrayObjFac,true);
        $intFactura                 = $arrayDetFac["idFactura"];
        $boolCambio                 = false;
        $objSession                 = $objRequest->getSession();
        $strIpCreacion              = $objRequest->getClientIp();
        $strUsrSession              = $objSession->get('user');
        $strCambiosDet = "";
        
        try
        {
            $emFinanciero              = $this->getDoctrine()->getManager("telconet_financiero");
            $entityFac = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                            ->find($intFactura);
            $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                            ->findByDocumentoId($intFactura);
            $strCambiosDet = "Se modificó el detalle de la prefactura ";
            //Recorremos los detalles de la factura y comparamos con los recibidos
            foreach($arrayResultado as $entityInfoDocumentoFinDet)
            {
                foreach($arrayDetFac["arrayData"] as $arrayFac)
                {
                    if($entityInfoDocumentoFinDet->getId() == $arrayFac["id"] && 
                        $entityInfoDocumentoFinDet->getObservacionesFacturaDetalle() != $arrayFac["descripcion"])
                    {
                        $strCambiosDet .=   " De: \"" . $entityInfoDocumentoFinDet->getObservacionesFacturaDetalle() .
                                            "\" Por: \"" .$arrayFac["descripcion"]."\""; 
                        $entityInfoDocumentoFinDet->setObservacionesFacturaDetalle($arrayFac["descripcion"]);
                        $entityInfoDocumentoFinDet->setUsrUltMod($strUsrSession);
                        $entityInfoDocumentoFinDet->setFeUltMod(new \DateTime('now'));
                        $emFinanciero->persist($entityInfoDocumentoFinDet);
                        $emFinanciero->flush();
                        
                        $boolCambio = true;
                    }
                }
                
            }

            if($boolCambio)
            {
                $intIteraciones = ceil(strlen($strCambiosDet)/1000);
                for($intI=0;$intI<$intIteraciones;$intI++)
                {
                    $intInicio =$intI*1000;
                    $strSubDet = substr($strCambiosDet,$intInicio,1000);
                    $entityInfoDocumentoHistorial = new InfoDocumentoHistorial();
                    $entityInfoDocumentoHistorial->setDocumentoId($entityFac);
                    $entityInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                    $entityInfoDocumentoHistorial->setUsrCreacion($strUsrSession);
                    $entityInfoDocumentoHistorial->setObservacion($strSubDet);
                    $entityInfoDocumentoHistorial->setEstado("Pendiente");
                    $emFinanciero->persist($entityInfoDocumentoHistorial);
                    $emFinanciero->flush();
                }
            }

           
            if($boolCambio)
            {
                if ($emFinanciero->getConnection()->isTransactionActive())
                {
                    $emFinanciero->getConnection()->commit();
                }
                $objResponse->setContent(json_encode(array('boolCodError'  =>  'sucess',
                                                        'strMensaje'   =>  "Cambios guardados exitosamente"))); 
            }
            else
            {
                $objResponse->setContent(json_encode(array('boolCodError'  =>  'sucess',
                                                        'strMensaje'   =>  "No hubo cambios que realizar"))); 
            }
            
        }
        catch(\Exception $ex)
        {
            $objResponse->setContent(json_encode(array('boolCodError'  =>  'error',
                                                        'strMensaje'   =>  'Error Interno. Favor notificar a Sistemas.'.$ex->getMessage())));
            
            $objServiceUtil->insertError( 'Telcos+', 
                                        'Editar Detalle Factura', 
                                        "Error al editar los detalles de factura $intFactura. ".$ex->getMessage(), 
                                        $strUsrSession, 
                                        $strIpCreacion );

        }


        $objResponse->headers->set('Content-type', 'text/json');        
        return $objResponse;    
    }
    
   /**
	* Documentación para el método 'indexFactRechazadasAction'.
	*
	* Función que renderiza la página del listado de facturas rechazadas
	*
    * @return $objResponse.
    * 
	* @author Hector Lozano <hlozano@telconet.ec>
	* @version 1.0 22-01-2021
	*/

    public function indexFactRechazadasAction()
    {
        return $this->render('financieroBundle:InfoDocumentoFinancieroCab:indexFactRechazadas.html.twig',array());
    }
    
   /**
	* Documentación para el método 'gridFactRechazadasAction'.
	*
	* Función que obtiene el listado de facturas rechazadas
	*
    * @return $this->render().
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
	* @version 1.0 22-01-2021
	*/
    public function gridFactRechazadasAction()
    {
        $objRequest         = $this->getRequest();
        $strFeEmisionDesde  = $objRequest->get("strFeEmisionDesde");
        $strFeEmisionHasta  = $objRequest->get("strFeEmisionHasta");
        $arrayTipoRechazo   = $objRequest->get("arrayTipoRechazo");
        $strIdentificacion  = $objRequest->get("strIdentificacion");
        $strLogin           = $objRequest->get("strLogin");
        $intIdEmpresa       = $objRequest->getSession()->get('idEmpresa'); 
        $emFinanciero       = $this->getDoctrine()->getManager("telconet_financiero");       
        
        $arrayParametros                       = array();
        $arrayParametros['intIdEmpresa']       = $intIdEmpresa;
        $arrayParametros['strFeEmisionDesde']  = $strFeEmisionDesde;
        $arrayParametros['strFeEmisionHasta']  = $strFeEmisionHasta;
        $arrayParametros['arrayTipoRechazo']   = $arrayTipoRechazo;
        $arrayParametros['strIdentificacion']  = $strIdentificacion;
        $arrayParametros['strLogin']           = $strLogin;
        
        $arrayResultado   = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getFactRechazadasPorCriterios($arrayParametros);
        $arrayRegistros   = $arrayResultado['registros'];
        $intTotal         = $arrayResultado['total'];
        $arrayPromociones = array();
        
        foreach($arrayRegistros as $arrayDatos):    
            
            $intIdDocumento   = $arrayDatos['intIdDocumento'];
            $strNumeroFactSri = $arrayDatos['strNumeroFactSri'];
            $strLogin         = $arrayDatos['strLogin'];
            $strNombreCliente = $arrayDatos['strNombreCliente'];
            $strEstado        = $arrayDatos['strEstado'];
            $strIdentificacion= $arrayDatos['strIdentificacion'];
            $strFeCreacion    = strval(date_format($arrayDatos['dateFeCreacion'], "Y-m-d"));
            $strFeEmision     = strval(date_format($arrayDatos['dateFeEmision'], "Y-m-d"));
            $strValorTotal    = $arrayDatos['strValorTotal'];
            $strMensajeError  = $arrayDatos['strMensajeError'];
           
            $arrayPromociones[] = array('intIdDocumento'     => $intIdDocumento,
                                        'strLogin'           => $strLogin,
                                        'strNombreCliente'   => $strNombreCliente,               
                                        'strIdentificacion'  => $strIdentificacion,
                                        'strEstado'          => $strEstado,
                                        'strFeCreacion'      => $strFeCreacion,
                                        'strFeEmision'       => $strFeEmision,
                                        'strValorTotal'      => $strValorTotal,
                                        'strNumeroFactSri'   => $strNumeroFactSri,
                                        'strMensajeError'   => $strMensajeError
                                       );
        endforeach;

        if(empty($arrayPromociones))
        {
            $arrayPromociones[] = array('intIdDocumento'     => "",
                                        'strLogin'           => "",
                                        'strNombreCliente'   => "",
                                        'strIdentificacion'  => "",
                                        'strEstado'          => "",  
                                        'strFeCreacion'      => "",
                                        'strFeEmision'       => "",
                                        'strValorTotal'      => "",
                                        'strNumeroFactSri'   => "",
                                        'strMensajeError'    => ""
                                       );  
        }

        $objResponse = new Response(json_encode(array('intTotal' => $intTotal, 'data' => $arrayPromociones)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
   /**
	* Documentación para el método 'getErroresFactRechazadasAction'.
	*
	* Función que obtiene los tipos de mensajes de errores de las facturas rechazadas.
	*
    * @return $objResponse.
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
	* @version 1.0 22-01-2021
	*/
    public function getErroresFactRechazadasAction()
    {   
        $strParametroCab    = 'FACTURACION_OFFLINE';
        $strDescripcionDet  = 'MENSAJE_ERROR_FACTURACION_OFFLINE';
        $strEmpresaCod      = $this->get('request')->getSession()->get('idEmpresa');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');
               
        try
        {
            
            $arrayParamPersonalAut = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get($strParametroCab, 'FINANCIERO', '', $strDescripcionDet, '', '', '', '', '', $strEmpresaCod, '');
            $arrayParametros                       = array();
            $arrayParametros['strSqlTipoError']    = $arrayParamPersonalAut[0]['valor1'];

            $arrayResultado = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getTipoErrorFactRechazadas($arrayParametros);

            $objResponse = new Response(json_encode(array('strTipoError' => $arrayResultado)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
        catch(\Exception $e)
        {
            $arrayResultado = array();
            $objResponse = new Response(json_encode(array('personalAutorizadoNc' => $arrayResultado)));
            $objResponse->headers->set('Content-type', 'text/json');

            return $objResponse;
        }
    }
    
   /**
	* Documentación para el método 'validaAutorizadoSriAction'.
	*
	* Función que valida si un documento financiero tiene número de autorización.
	*
    * @return $objResponse.
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
	* @version 1.0 22-01-2021
	*/
    public function validaAutorizadoSriAction()
    {
        $objRequest      = $this->getRequest();
        $emFinanciero    = $this->get('doctrine')->getManager('telconet_financiero');

        try
        {
            $arrayParametros                   = array();
            $arrayParametros['intIdDocumento'] = $objRequest->get("intIdDocumento");
            $arrayNumAutorizacion = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getNumeroAutorizacion($arrayParametros);

            $strAutorizaSri = $arrayNumAutorizacion[0]["numeroAutorizacion"] ? "S" : "N";
        }
        catch(\Exception $objException)
        {
            $strAutorizaSri = 'N';
        }        
        
        $objResponse = new Response(json_encode(array("strAutorizaSri" => $strAutorizaSri)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    
   /**
	* Documentación para el método 'ejecutarReprocesoFactRechazadasAction'.
	*
	* Función que ejecuta el reproceso de facturas rechazadas para enviarlas al SRI.
	*
    * @return $objResponse.
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
	* @version 1.0 22-01-2021
	*/
    public function ejecutarReprocesoFactRechazadasAction()
    {
        $objRequest         = $this->getRequest();
        $emFinanciero       = $this->get('doctrine')->getManager('telconet_financiero');
        $strEmpresaCod      = $this->get('request')->getSession()->get('idEmpresa');
        $strIpCreacion      = $objRequest->getClientIp();
        $strUsrCreacion     = $this->get('request')->getSession()->get('user');
        $strTipoTransaccion = $objRequest->get("strTipoTransaccion"); 
        $strIdsDocumento    = $objRequest->get("strIdsDocumento");

        try
        {        
            $arrayParametros                       = array();
            $arrayParametros['strUsrCreacion']     = $strUsrCreacion;
            $arrayParametros['strCodEmpresa']      = $strEmpresaCod;
            $arrayParametros['strIpCreacion']      = $strIpCreacion;
            $arrayParametros['strTipoPma']         = 'ReproFactRechazo';
            $arrayParametros['strTipoTransaccion'] = $strTipoTransaccion;
            $arrayParametros['strIdsDocumento']    = $strIdsDocumento;
          
            $intCantProcMasivo = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getCountProcesoMasivo($arrayParametros);
                  
            if($intCantProcMasivo[0]['intCantidad'] == 0)
            {
                $strRespuestaProcMasivo = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                       ->creaProcesoMasivoFactRechazadas($arrayParametros);
            
                if($strRespuestaProcMasivo == "OK")
                {
                    $strRespuesta = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                 ->ejecutarReprocesoFactRechazadas($arrayParametros);
                    $strMensaje   = 'El reproceso de facturas rechazadas se está ejecutando correctamente';
                }
                else
                {
                    $strRespuesta = 'ERROR';
                    $strMensaje   ='No se pudo ejecutar reproceso de facturas rechazadas, existe un error';
                }
            }
            else
            {
                $strRespuesta = 'ERROR';
                $strMensaje   = 'Existe otro proceso masivo en ejecución.';
            }
 
        }
        catch(\Exception $objException)
        {
            $strRespuesta = 'ERROR';
            $strMensaje   ='No se pudo ejecutar reproceso de facturas rechazadas, existe un error';
        }        
        
        $objResponse = new Response(json_encode(array("strRespuesta" => $strRespuesta,"strMensaje"=>$strMensaje)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
    * @Secure(roles="ROLE_185-6877")
    * Funcion que clona una prefactura proporcional o mensual y cambia a eliminado la prefactura padre
    * 
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @since 1.0 09-02-2021
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 02-07-2021 - Se agrega parámetro intIdOficinaClonar, para presentar la oficina de la prefactura 
    *                           a clonarse en la interfaz correspondiente. 
    * @author Gustavo Narea <gnarea@telconet.ec>
    * @version 1.2 29-04-2022 - Se agrega clonacion a facturas y prefacturas.
    *
    */
    public function clonarPrefacturaAction($intId)
    {
        $objSession                             = $this->get('request')->getSession();
        $arrayParametros                        = array();
        $strUrlReferida                         = $this->get('request')->headers->get('referer');
        $strUrlIndexFactura                     = $this->generateUrl('infodocumentofinancierocab');
        $strUrlFacturacionAutomatica            = $this->generateUrl('facturacion_mensual_automatica_list');
        
        $emFinanciero        = $this->getDoctrine()->getManager("telconet_financiero");
        $emComercial         = $this->getDoctrine()->getManager("telconet");

        $objInfoDocumentoFinanacieroCab         = $this->createForm(new InfoDocumentoFinancieroCabType(), $entityInfoDocFinCab);
        $strIpCreacion                          = $this->getRequest()->getClientIp() ? $this->getRequest()->getClientIp():'127.0.0.1';

        $arrayParametros["intIdDocumento"]      = $intId;
        $arrayParametros["strTipoFacturacion"]  = "Mensual";
        $arrayParametros["strUrlReferida"]      = $strUrlReferida;
        $arrayParametros["strUrlIndexFactura"]  = $strUrlIndexFactura;
        $arrayParametros["strUrlFacturacionAutomatica"]  = $strUrlFacturacionAutomatica;
        $arrayParametros["boolPerfilClonacion"] = $this->get('security.context')->isGranted('ROLE_185-6877');
        $arrayParametros["strIpCreacion"]       = $strIpCreacion;
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
            return $this->render("financieroBundle:InfoDocumentoFinancieroCab:new.html.twig", $arrayParametros);
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
            return $this->redirect($this->generateUrl("infodocumentofinancierocab_new"));
        }
    }

    
}
