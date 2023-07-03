<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Form\InfoDocumentoFinancieroCabType;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * InfoNotaCredito controller.
 *
 */
class InfoNotaCreditoController extends Controller implements TokenAuthenticatedController
{
    /**
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 21-01-2015
     * @since 1.0
     * @return Render al index de las notas de credito
     */
    /**
     * @Secure(roles="ROLE_70-1")
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
            $rolesPermitidos[] = 'ROLE_67-1777'; //ENVIO NOTIFICACION COMPROBANTE
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-1778'))
        {
            $rolesPermitidos[] = 'ROLE_67-1778'; //ACTULIZA COMPROBANTE ELECTRONICO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-1837'))
        {
            $rolesPermitidos[] = 'ROLE_67-1837'; //DESCARGA COMPROBANTE ELECTRONICO
        }
        if(true === $this->get('security.context')->isGranted('ROLE_67-4897'))
        {
            $rolesPermitidos[] = 'ROLE_67-4897'; //ANULA COMPROBANTE ELECTRONICO
        }

        return $this->render('financieroBundle:InfoNotaCredito:index.html.twig', array('rolesPermitidos' => $rolesPermitidos,
                                                                                       'intIdPunto'      => $intIdPunto));
    }

    /**
     * @Secure(roles="ROLE_70-6")
     * 
     * showAction, muestra la informacion individual de una nota de credito
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 07-01-2015
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 19-02-2015
     * @since 1.1
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 23-05-2016 - Se añade si el cliente paga iva, y quien es el responsable de la N/C
     * @since 1.2
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 02-06-2016 - Se verifica el IVA de la factura con la cual debe aplicar la NC
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.5 26-12-2018 - Se realizan cambios para que la NC considere el impuesto ITBMS para Panama.
     * 
     * @since 1.3
     * @param type $id recibe el id de la nota de credito
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.4 15-03-2019 - Se modifica impuestos para que tome la configuración de Guatemala.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.5 28-10-2020 - Se realizan cambios para visualizar el detalle de las características asociadas al documento.
     */
    public function showAction($id)
    {
        $em                                 = $this->getDoctrine()->getManager("telconet_financiero");
        $em_general                         = $this->getDoctrine()->getManager("telconet_general");
        $entityInfoDocumentoFinancieroCab   = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
        $objRequest                         = $this->get('request');
        $objSession                         = $objRequest->getSession();
        $strPrefijoEmpresa                  = $objSession->get('prefijoEmpresa');

        //Obteniendo la referencia de factura
        $intReferenciaDocumentoId       = $entityInfoDocumentoFinancieroCab->getReferenciaDocumentoId();
        $entityInfoDocFinancieroCabFAC  = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intReferenciaDocumentoId);

        $objDelteForm                   = $this->createDeleteForm($id);

        $em_comercial                   = $this->getDoctrine()->getManager("telconet");
        $entityInfoPunto                = $em_comercial->getRepository('schemaBundle:InfoPunto')
                                                       ->find($entityInfoDocumentoFinancieroCab->getPuntoId());
        $entityInfoPersonaEmpresaRol    = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                       ->find($entityInfoPunto->getPersonaEmpresaRolId()->getId());
        $entityOficina                  = $em->getRepository('schemaBundle:InfoOficinaGrupo')
                                             ->find($entityInfoDocumentoFinancieroCab->getOficinaId());

        $arrayInformacionPersona['puntoId'] = $entityInfoPunto->getLogin();
        $arrayInformacionPersona['cliente'] = sprintf("%s", $entityInfoPersonaEmpresaRol->getPersonaId());


        $entityHistorial = $em->getRepository('schemaBundle:InfoDocumentoHistorial')
                              ->findByDocumentoId(array('id' => $id), array('feCreacion' => 'ASC'));
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
        
        /*
         * Se obtiene el responsable de la N/C
         */
        $strTipoResponsable = 'N/A';
        $strResponsable     = 'N/A';
        
        $objAdmiCaracteristicaTipoResponsable = $em_comercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneBy( array('estado'                    => 'Activo', 
                                                                                'descripcionCaracteristica' => 'TIPO_RESPONSABLE_NC') );
        
        $objInfoDocumentoCaracteristicaTipo = $em->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                 ->findOneBy( array( 'documentoId'      => $entityInfoDocumentoFinancieroCab, 
                                                                     'caracteristicaId' => $objAdmiCaracteristicaTipoResponsable->getId()) );
        
        if($objInfoDocumentoCaracteristicaTipo)
        {
            $strTipoResponsable = $objInfoDocumentoCaracteristicaTipo->getValor();
        }
        
        $objAdmiCaracteristicaResponsable = $em_comercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy( array('estado'                    => 'Activo', 
                                                                            'descripcionCaracteristica' => 'RESPONSABLE_NC') );
        
        $objInfoDocumentoCaracteristicaResponsable = $em->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                        ->findOneBy( array( 'documentoId'      => $entityInfoDocumentoFinancieroCab, 
                                                                            'caracteristicaId' => $objAdmiCaracteristicaResponsable->getId()) );
        
        if($objInfoDocumentoCaracteristicaResponsable)
        {
            $strResponsable = $objInfoDocumentoCaracteristicaResponsable->getValor();
        }
        /*
         * Fin Se obtiene el responsable de la N/C
         */
        
        
        
        
        /*
         * Se obtiene la descripcion Interna de la nota de credito
         */
        $strDescripcionInterna = '';
        
        if( $strPrefijoEmpresa == "TN" )
        {
            $objAdmiCaracteristicaTipoResponsable = $em_comercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                 ->findOneBy( array('estado'                    => 'Activo', 
                                                                                    'descripcionCaracteristica' => 'DESCRIPCION_INTERNA_NC') );

            $objInfoDocumentoCaracteristicaTipo = $em->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                     ->findOneBy( array( 'documentoId'      => $entityInfoDocumentoFinancieroCab, 
                                                                         'caracteristicaId' => $objAdmiCaracteristicaTipoResponsable->getId()) );

            if($objInfoDocumentoCaracteristicaTipo)
            {
                $strDescripcionInterna = $objInfoDocumentoCaracteristicaTipo->getValor();
            }
        }
        /*
         * Fin Se obtiene la descripcion Interna de la nota de credito
         */
        
        
        /*
         * Verifica el IVA con el cual se aplica a la NC
         */
        $strIvaAplicado = '';
        $strPagaIva     = $entityInfoPersonaEmpresaRol->getPersonaId() ? ( $entityInfoPersonaEmpresaRol->getPersonaId()->getPagaIva() ? 'Si' : 'No' ) 
                          : 'No';
        
        if( $strPagaIva == 'Si' )
        {
            if(!empty($entityInfoDocFinancieroCabFAC))
            {
                $objInfoDocumentoFinancieroDet  = $em->getRepository("schemaBundle:InfoDocumentoFinancieroDet")
                                                     ->findByDocumentoId($entityInfoDocFinancieroCabFAC->getId());

                if($objInfoDocumentoFinancieroDet)
                {
                    foreach($objInfoDocumentoFinancieroDet as $entityDetFactura)
                    {
                        if( $strIvaAplicado == '' )
                        {
                            $objInfoDocumentoFinancieroImp = $em->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                ->findByDetalleDocId( $entityDetFactura->getId() );

                            if( $objInfoDocumentoFinancieroImp )
                            {
                                foreach($objInfoDocumentoFinancieroImp as $entityDetalleImpuesto)
                                {
                                    $objAdmiImpuesto = $em_general->getRepository('schemaBundle:AdmiImpuesto')
                                                                  ->findOneById( $entityDetalleImpuesto->getImpuestoId() );

                                    if( $objAdmiImpuesto )
                                    {
                                        
                                        if($strPrefijoEmpresa == 'TNP')
                                        {
                                            if($objAdmiImpuesto->getTipoImpuesto() == "ITBMS")
                                            {
                                                $strIvaAplicado = $entityDetalleImpuesto->getPorcentaje();
                                            }
                                        }
                                        else if($strPrefijoEmpresa == 'TNG')
                                        {
                                            if($objAdmiImpuesto->getTipoImpuesto() == "IVA_GT")
                                            {
                                                $strIvaAplicado = $entityDetalleImpuesto->getPorcentaje();
                                            }//( $objAdmiImpuesto->getDescripcionImpuesto() )
                                        }
                                        else
                                        {
                                            if($objAdmiImpuesto->getTipoImpuesto() == "IVA")
                                            {
                                                $strIvaAplicado = $entityDetalleImpuesto->getPorcentaje();
                                            }//( $objAdmiImpuesto->getDescripcionImpuesto() )
                                        }
                                    }//( $objAdmiImpuesto )
                                }//foreach($objInfoDocumentoFinancieroImp as $entityDetalleImpuestos)
                            }//( $objInfoDocumentoFinancieroImp )
                        }//( $arrayParametros['strIvaAplicado'] == '' )
                    }//foreach($objInfoDocumentoFinancieroDet as $entityDetFactura)
                }//($objInfoDocumentoFinancieroDet)
            }//(!empty($entityInfoDocFinancieroCabFAC))
        }//( $strPagaIva == 'Si' )
        /*
         * Fin Verifica el IVA con el cual se aplica a la NC
         */
        
        
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
                        
                        $arrayCaracteristica['strValor'] = $objInfoDocumentoCaracteristica->getValor();
                        
                        $arrayCaracteristicas[] = $arrayCaracteristica;
                    }
                }
            endforeach;            
        }
        
        
        return $this->render('financieroBundle:InfoNotaCredito:show.html.twig', array(
                'entity'                    => $entityInfoDocumentoFinancieroCab,
                'delete_form'               => $objDelteForm->createView(),
                'info_cliente'              => $arrayInformacionPersona,
                'punto'                     => $entityInfoPunto,
                'oficina'                   => $entityOficina,
                'historial'                 => $arrayHistorial,
                'fact_referencia'           => $entityInfoDocFinancieroCabFAC->getNumeroFacturaSri(),
                'boolTieneSaldo'            => true,
                'strObservacionSinSaldo'    => '',
                'strDescripcionInterna'     => $strDescripcionInterna,
                'strPrefijoEmpresa'         => $strPrefijoEmpresa,
                'strResponsable'            => $strResponsable,
                'strTipoResponsable'        => $strTipoResponsable,
                'strIvaAplicado'            => $strIvaAplicado,
                'strPagaIva'                => $strPagaIva,
                'arrayCaracteristicas'      => $arrayCaracteristicas 
        ));
    }

    /**
     * @Secure(roles="ROLE_70-2")
     * 
     * Metodo newAction construye el formulario para crear la nota de credito
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 09-10-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 19-02-2015
     * @since 1.1
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 21-05-2016 - Se verifica si el cliente paga iva
     * @since 1.2
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 02-06-2016 - Se verifica el IVA de la factura con la cual debe aplicar la NC
     * @since 1.3
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 16-11-2016 - Se verifica si tiene una NC aplicada en estado 'Activo' para deshabilitar la opción 'Por Valor Original'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 02-12-2016 - Se verifica si la factura fue compensada, para compensar respectivamente a la nota de crédito. 
     *                           Para ello se verifica si el campo 'DESCUENTO_COMPENSACION' tiene un valor mayor a cero.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 09-02-2017 - Se elimina la validación por empresa de la compensación solidaria para que tanto MD y TN compensen al 2% las
     *                           notas de crédito aplicadas a facturas realizadas con compensación solidaria.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.8 29-03-2018 - Se agrega envío de bandera para validar si la factura sobre la que se aplicará la nota de crédito
     *                           es por contrato digital.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.9 05-04-2018 - Se agrega validación para permitir crear NC a facturas de contrato digital a aquellos usuarios que tengan 
     *                           asignado el perfil respectivo.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 2.0 26-12-2018 - Se realizan cambios para que la NC considere el impuesto ITBMS para Panama.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 2.1 15-03-2019 - Se modifica impuestos para que tome la configuración de Guatemala.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 2.2 07-02-2020 - Se valida y guarda el rol 'ROLE_70-7157' en arrayParametros, por motivo 
     *                           de edición de valor y cantidad al crear NC.
     *                         - Se agrega validación para retornar el porcentaje de ICE ó IVA_E si existiera.  
     */
    public function newAction($id)
    {
        $em_general             = $this->getDoctrine()->getManager();
        $em_financiero          = $this->getDoctrine()->getManager("telconet_financiero");
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $strCliente             = $objSession->get('cliente');
        $arrayPtoCliente        = $objSession->get('ptoCliente');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $arrayParametrosSend    = array('intIdDocumento'               => $id, 
                                        'arrayEstadoTipoDocumentos'    => ['Activo'], 
                                        'arrayCodigoTipoDocumento'     => ['NC'], 
                                        'arrayEstadoNC'                => ['Activo']);

        //Obtiene el valor total de Nc aplicadas a la factura, tambien el numero y valor total de la factura
        $arrayGetValorTotalNcByFactura      = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                            ->getValorTotalNcByFactura($arrayParametrosSend);

        $entityInfoDocumentoFinancieroCab   = new InfoDocumentoFinancieroCab();
        $formInfoDocumentoFinancieroCab     = $this->createForm(new InfoDocumentoFinancieroCabType(), $entityInfoDocumentoFinancieroCab);
        
        //obtiene la entidad del tipo de documento NC => nota de credito
        $entityAdmiTipoDocumentoFinanciero  = $em_financiero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                            ->findOneBy(array('codigoTipoDocumento' => 'NC'));
        //Obtiene un registro de un documento NC relacionada a la factura
        $entityInfoDocumentoFinancieroCabNc = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                            ->findOneBy(
                                                                array('referenciaDocumentoId'   => $id,
                                                                      'estadoImpresionFact'     => array('Pendiente', 'Aprobada', 'Activo'),
                                                                      'tipoDocumentoId'         => $entityAdmiTipoDocumentoFinanciero->getId()));

        //Obtiene los motivos
        $objAdmiMotivo = $em_general->getRepository('schemaBundle:AdmiMotivo')
                                    ->findMotivosPorModuloPorItemMenuPorAccion("nota_de_credito", "Ver nota de credito", "new");

        $arrayParametros = array(
                                    'entity'            => $entityInfoDocumentoFinancieroCab,
                                    'form'              => $formInfoDocumentoFinancieroCab->createView(),
                                    'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                    'strIvaAplicado'    => 0,
                                    'strEsCompensado'   => 'N',
                                    'intPorcentajeImpuestoIce' => 0,
                                    'intEditValoresNcCaract'   => 0,
                                    'intPorcentajeIvaE'        => 0
                                );

        if(true === $this->get('security.context')->isGranted('ROLE_70-7157'))
        {
            $arrayRolPermitido[] = 'ROLE_70-7157'; 
        }
        
        $arrayParametros['rolesPermitidos'] = $arrayRolPermitido;
        
        /**
         * Bloque que verifica si la factura es compensada, para compensar la NC
         */
        $objInfoDocumentoFinancieroCab = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findOneById($id);

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
        if(empty($arrayGetValorTotalNcByFactura['strMensajeError']))
        {
            $arrayParametros['intValorFactura']     = round($arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalFac'], 2);
            $arrayParametros['strNumeroFacturaSri'] = $arrayGetValorTotalNcByFactura['arrayResultado'][0]['numeroFacturaSri'];
        }
        else
        {
            $arrayParametros['intValorFactura']     = 0;
            $arrayParametros['strNumeroFacturaSri'] = $arrayGetValorTotalNcByFactura['strMensajeError'];
        }
        //Verifica si array Puntos viene con datos
        if(!empty($arrayPtoCliente))
        {
            $arrayParametros['punto_id']    = $arrayPtoCliente;
            $arrayParametros['cliente']     = $strCliente;
            
            /*
             * Verifico si paga IVA
             */
            $objInfoPersona = $em_general->getRepository('schemaBundle:InfoPersona')->findOneById($arrayParametros['punto_id']['id_persona']);
            
            if($objInfoPersona)
            {
                $arrayParametros['strPagaIva'] = ($objInfoPersona->getPagaIva() == 'S') ? 'Si' : 'No';
            }
            /*
             * Fin Verifico si paga IVA
             */
        }
        //Verifica si se envio un id en la URL y el IVA con el cual se aplica a la NC
        if(!empty($id))
        {
            $arrayParametros['idFactura']   = $id;
            $intTotalDetalleSinImpuesto     = 0;
            $objInfoDocumentoFinancieroDet  = $em_financiero->getRepository("schemaBundle:InfoDocumentoFinancieroDet")->findByDocumentoId($id);
            
            foreach($objInfoDocumentoFinancieroDet as $entityDetFactura)
            {
                if( $arrayParametros['strIvaAplicado'] == 0 && $arrayParametros['strPagaIva'] == 'Si' )
                {
                    $objInfoDocumentoFinancieroImp = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                   ->findByDetalleDocId( $entityDetFactura->getId() );

                    if( $objInfoDocumentoFinancieroImp )
                    {
                        foreach($objInfoDocumentoFinancieroImp as $entityDetalleImpuesto)
                        {
                            $objAdmiImpuesto = $em_general->getRepository('schemaBundle:AdmiImpuesto')
                                                          ->findOneById( $entityDetalleImpuesto->getImpuestoId() );

                            if( $objAdmiImpuesto )
                            {                               
                                if( $strPrefijoEmpresa == 'TNP' )
                                {
                                    if( $objAdmiImpuesto->getTipoImpuesto() == "ITBMS" )
                                    {
                                        $arrayParametros['strIvaAplicado'] = $entityDetalleImpuesto->getPorcentaje();
                                    }
                                }
                                else if( $strPrefijoEmpresa == 'TNG' )
                                {
                                    if( $objAdmiImpuesto->getTipoImpuesto() == "IVA_GT" )
                                    {
                                        $arrayParametros['strIvaAplicado'] = $entityDetalleImpuesto->getPorcentaje();
                                    }//( $objAdmiImpuesto->getDescripcionImpuesto() )                                
                                }
                                else
                                {
                                    if( $objAdmiImpuesto->getTipoImpuesto() == "IVA" )
                                    {
                                        $arrayParametros['strIvaAplicado'] = $entityDetalleImpuesto->getPorcentaje();
                                    }//( $objAdmiImpuesto->getDescripcionImpuesto() )                                
                                }
                            }//( $objAdmiImpuesto )
                            if( $objAdmiImpuesto )
                            {                               
                                if( $strPrefijoEmpresa == 'TNP' )
                                {
                                    if( $objAdmiImpuesto->getTipoImpuesto() == "IEC" )
                                    {
                                        $arrayParametros['intPorcentajeImpuestoIce'] = $entityDetalleImpuesto->getPorcentaje();
                                    }
                                }
                                else
                                {
                                    if( $objAdmiImpuesto->getTipoImpuesto() == "ICE" )
                                    {
                                        $arrayParametros['intPorcentajeImpuestoIce'] = $entityDetalleImpuesto->getPorcentaje();
                                    } 
                                    if( $objAdmiImpuesto->getTipoImpuesto() == "IVA_E" )
                                    {
                                        $arrayParametros['intPorcentajeIvaE'] = $entityDetalleImpuesto->getPorcentaje();
                                    }
                                }
                            }
                        }//foreach($objInfoDocumentoFinancieroImp as $entityDetalleImpuestos)
                    }//( $objInfoDocumentoFinancieroImp )
                }//( $arrayParametros['strIvaAplicado'] == 0 && $arrayParametros['strPagaIva'] == 'Si' )
                    
                $intTotalDetalleSinImpuesto = $intTotalDetalleSinImpuesto 
                                            + ($entityDetFactura->getPrecioVentaFacproDetalle() * $entityDetFactura->getCantidad());
            }//foreach($objInfoDocumentoFinancieroDet as $entityDetFactura)
        }
        //Setea (Si) si es una nota de credito electronica de magadatos o telconet caso contrario no es electronica
        $arrayParametros['esElectronica']        = ($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'TN'  || $strPrefijoEmpresa == 'TNG') ? 'Si' : 'No';
        $arrayParametros['valorSubTotalFactura'] = $intTotalDetalleSinImpuesto;
        
        //Verifica que se hayan obtenido los motivos
        if(!empty($objAdmiMotivo))
        {
            $arrayParametros['listadoMotivos'] = $objAdmiMotivo;
        }
        //Pregunta si la factura ya tiene una nota de credito en estado Pendiente o Aprobada, para no dejar crear otra NC
        if(!empty($entityInfoDocumentoFinancieroCabNc))
        {
            $strEstadoNotaCredito = $entityInfoDocumentoFinancieroCabNc->getEstadoImpresionFact()
                                    ? $entityInfoDocumentoFinancieroCabNc->getEstadoImpresionFact() : '';
            
            if( empty($strEstadoNotaCredito) )
            {
                $arrayParametros['boolTieneNc'] = true;
                
                $arrayParametros['strObservacionNotaCredito'] = "Esta factura tiene una nota de credito en estado NULL. Por favor, comunicarse con ".
                                                                "Sistemas para revisar el inconveniente.";
            }
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
                $arrayParametros['strObservacionNotaCredito'] = "Esta factura tiene una nota de credito en estado ".$strEstadoNotaCredito. ", " 
                                                                ."por favor " . $strEstadoNc . " para proceder a crear una nueva Nota de Credito";
            }//( empty($strEstadoNotaCredito) )
        }
        //Si existio un error en la consulta del valor total de la nota de credito lo presentara en el twig
        if(!empty($arrayGetValorTotalNcByFactura['strMensajeError']))
        {
            $arrayParametros['strMensajeError'] = $arrayGetValorTotalNcByFactura['strMensajeError'];
        }
        //Calcula el saldo disponible FAC - SUM(NC)
        $intSaldo                       = $arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalFac'] 
                                        - $arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalNc'];
        $arrayParametros['intSaldo']    = round($intSaldo, 2);
        $arrayParametros['strObservacionConSaldo'] = "Usted cuenta con $" . round($intSaldo, 2) 
                                                    . " de saldo disponible para crear una nueva nota de credito";
        $arrayParametros['boolTieneSaldo']         = true;
        //Si el saldo <= 0 no podra crear una nueva nota de credito y se mostrara un mensaje en el twig
        if($intSaldo <= 0)
        {
            $arrayParametros['boolTieneSaldo']          = false;
            $arrayParametros['strObservacionSinSaldo']  = "No puede crear notas de credito a ésta factura ya que no cuenta con"
                                                        . " saldo disponible. Saldo: " . round($intSaldo, 2);
        }
        
        $arrayParametros['boolFacturaContrato'] = false;
        
        if($strUsrCreacionFactura === 'telcos_contrato' && !($this->get('security.context')->isGranted('ROLE_70-5777')))
        {
            $arrayParametros['boolFacturaContrato'] = true;
            $arrayParametros['strObservacionFact']  = "No puede crear notas de crédito a ésta factura ya que pertenece al"
                                                        . " proceso automático de Contrato Digital ";
        } 
        
        return $this->render('financieroBundle:InfoNotaCredito:new.html.twig', $arrayParametros);
    }//newAction

    
    /**
     * @Secure(roles="ROLE_70-3")
     * 
     * Metodo createAction crea Notas de Credito
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 09-10-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 10-02-2015
     * @since 1.1
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 11-03-2015
     * @since 1.2
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 21-05-2016 - Se verifica si el cliente paga iva
     * @since 1.3
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 01-06-2016 - Se agrega la opción de 'Valor Por Detalle'
     * @since 1.4
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 15-06-2016 - Se envia el codEmpresa para enviar el mail a las personas correctas dependiendo de la empresa a la que pertenece el
     *                           usuario en session. 
     * @since 1.5
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 13-12-2016 - Se valida que la NC no se pueda crear con valor cero.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 21-02-2017 - Se verifica si la NC debe compensar o no para enviar dicha variable 'strEsCompensado' al twig new.html.twig cuando
     *                           no se pueda crear la nota de crédito correspondiente.
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.9 15-03-2019 - Se modifica validación es Electronica para incluir a Guatemala.
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 2.0 10-02-2020 - Se agrega variable 'intEditValoresNcCaract' al array de parámetros para verificar si ha 
     *                           sido editado el grid al crear nota de crédito.
     * 
     * @param Request $request obtiene los datos enviados desde el javascript
     * @return type Redirect Redirecciona al formulario de creacion de nota de credito
     */
    public function createAction(Request $ObjRequest)
    {        
        $em_financiero          = $this->getDoctrine()->getManager("telconet_financiero");
        $objInformacionGrid     = $ObjRequest->get('listado_informacion');     
        $intIdFactura           = $ObjRequest->get('factura_id');
        $arrayInformacionGrid   = json_decode($objInformacionGrid);
        $objSession             = $ObjRequest->getSession();
        $arrayPtoCliente        = $objSession->get('ptoCliente');
        $intIdOficina           = $objSession->get('idOficina');
        $intIdEmpresa           = $objSession->get('idEmpresa');
        $strUser                = $objSession->get('user');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $intIdPunto             = $arrayPtoCliente['id'];
        $strEstado              = "Pendiente";
        $strMotivo              = $ObjRequest->get('motivos');
        $arrayMotivo            = explode("-", $strMotivo);
        $strObservacion         = $ObjRequest->get('observacion');
        $strPagaIva             = $ObjRequest->get('strPagaIva');
        $strTipoResponsable     = $ObjRequest->get('checkTipoResponsable');
        $strClienteResponsable  = $ObjRequest->get('checkClienteResponsable');
        $strEmpresaResponsable  = $ObjRequest->get('checkEmpresaResponsable');
        $strDescripcionInterna  = $ObjRequest->get('descripcionInterna');
        $strIpCreacion          = $ObjRequest->getClientIp();
        $strEsElectronica       = ($strPrefijoEmpresa == 'EN' || $strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'TN') ? 'S' : 'N';
        $intVerificacion        = 0;
        $serviceNotaCredito     = $this->get('financiero.InfoNotaCredito');
        $intEditValoresNcCaract = $ObjRequest->get('intEditValoresNcCaract');
       
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
                    $strTipoNotaCredito = 'El tipo de nota de credito enviado a crear no existe.';
            }
        }
        //Pregunta su tiene un punto en sesion caso contrario no permitira crear la NC
        if(!empty($intIdPunto))
        {
            //Verificar si el punto en session corresponde al punto de la factura selecionada
            $strCodigoTipoDocumento= array('FAC','FACP');
            $intVerificacion=$em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                ->getResultadoValidacionDocumento($intIdFactura,$intIdPunto,$strCodigoTipoDocumento);
            
            //Quiere decir que el documento para aplicar nota de credito pertenece al login en sesion
            if($intVerificacion>0)
            {
                $em_financiero->getConnection()->beginTransaction();
                
                try
                {
                    //Se definen los parametros a enviar al service para la creacion de la nota de credito
                    $arrayParametrosNc["estado"]                = $strEstado;
                    $arrayParametrosNc["codigo"]                = "NC";
                    $arrayParametrosNc["informacionGrid"]       = $arrayInformacionGrid;
                    $arrayParametrosNc["punto_id"]              = $intIdPunto;
                    $arrayParametrosNc["oficina_id"]            = $intIdOficina;
                    $arrayParametrosNc["observacion"]           = preg_replace('/[^\da-z]/i', ' ', $strObservacion);
                    $arrayParametrosNc["facturaId"]             = $intIdFactura;
                    $arrayParametrosNc["user"]                  = $strUser;
                    $arrayParametrosNc["motivo_id"]             = $arrayMotivo;
                    $arrayParametrosNc["intIdEmpresa"]          = $intIdEmpresa;
                    $arrayParametrosNc["strEselectronica"]      = $strEsElectronica;
                    $arrayParametrosNc["strPrefijoEmpresa"]     = $strPrefijoEmpresa;
                    $arrayParametrosNc["strPagaIva"]            = $strPagaIva;
                    $arrayParametrosNc["strTipoResponsable"]    = $strTipoResponsable;
                    $arrayParametrosNc["strClienteResponsable"] = $strClienteResponsable;
                    $arrayParametrosNc["strEmpresaResponsable"] = $strEmpresaResponsable;
                    $arrayParametrosNc["strIpCreacion"]         = $strIpCreacion;
                    $arrayParametrosNc["strDescripcionInterna"] = $strDescripcionInterna;
                    $arrayParametrosNc["strTipoNotaCredito"]    = 'El tipo de la nota de crédito es '.$strTipoNotaCredito;
                    $arrayParametrosNc["intEditValoresNcCaract"] = $intEditValoresNcCaract;

                    //Obtiene le valor total de la nueva nota de  credito
                    $arrayValorTotalNcACrear = $serviceNotaCredito->obtieneValorTotalNcACrear($arrayParametrosNc);
               
                    $arrayParametrosSend     = array('intIdDocumento'               => $intIdFactura, 
                                                     'arrayEstadoTipoDocumentos'    => ['Activo'], 
                                                     'arrayCodigoTipoDocumento'     => ['NC'], 
                                                     'arrayEstadoNC'                => ['Activo']);
                    //Obtiene la sumatoria de las notas de credito Activas relacionadas a la factura
                    $arrayGetValorTotalNcByFactura = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                   ->getValorTotalNcByFactura($arrayParametrosSend);
                    $arrayParametros['strMensajeError'] = 'No existe Error';
                    $arrayParametros['boolTieneSaldo']  = true;
                    if(empty($arrayGetValorTotalNcByFactura['strMensajeError']))
                    {
                        //Calcula el saldo FAC - SUM(NC)
                        $intSaldo       = round(($arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalFac'] 
                                          - $arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalNc']), 2);
                        $intNuevoSaldo  = round($arrayValorTotalNcACrear['intValorTotal'], 2);
                        
                        //Pregunta si el valor de la nueva NC es <= al saldo disponible para permitir crear una nueva NC y si es mayor que cero
                        if( $intNuevoSaldo <= $intSaldo && floatval($intNuevoSaldo) > 0 )
                        {
                            //Genera la nueva nota de credito
                            $entityNotaDeCredito = $serviceNotaCredito->generarNotaDeCredito($arrayParametrosNc);
                            //Define los parametros para el envio de notificacion
                            $arrayParametrosEnvio['strCodEmpresa']          = $intIdEmpresa;
                            $arrayParametrosEnvio['strNombreParametro']     = 'ENVIO_CORREO';
                            $arrayParametrosEnvio['strModulo']              = 'FINANCIERO';
                            $arrayParametrosEnvio['strProceso']             = 'NOTAS_CREDITO';
                            $arrayParametrosEnvio['strAccionGeneral']       = 'CREAR_NC';
                            $arrayParametrosEnvio['strAccionUnitaria']      = 'CREAR_NC_FROM_SUBJECT';
                            $arrayParametrosEnvio['strUser']                = trim($strUser);
                            $arrayParametrosEnvio['intIdMotivo']            = $arrayMotivo[0];
                            $arrayParametrosEnvio['strObservacion']         = $strObservacion;
                            $arrayParametrosEnvio['strCodigoPlantilla']     = 'CREA_NC';
                            $arrayParametrosEnvio['strProcesoNc']           = 'ncCreadas';
                            $arrayParametrosEnvio['strTipoNotaCredito']     = $strTipoNotaCredito;
                            $arrayParametrosEnvio['strFila']                = 
                                '<tr>'
                                . '<td>1</td>'
                                . '<td>'   . $arrayPtoCliente['login'] . '</td>'
                                . '<td>'   . $arrayGetValorTotalNcByFactura['arrayResultado'][0]['numeroFacturaSri'] . '</td>'
                                . '<td> $' . round($arrayGetValorTotalNcByFactura['arrayResultado'][0]['valorTotalFac'], 2) . '</td>'
                                . '<td> Pendiente hasta su aprobacion </td>'
                                . '<td> $' . $entityNotaDeCredito->getValorTotal() . '</td>'
                                . '<td> Pendiente </td>'
                                . '<td> La nota nota de credito fue creada con exito </td>'
                               . '</tr>';
                            //Se envia los parametros para el envio de notificacion
                            $serviceNotaCredito->notificaProcesoNotaCredito($arrayParametrosEnvio);
                            
                            $em_financiero->getConnection()->commit();
                            $em_financiero->getConnection()->close();

                            return $this->redirect($this->generateUrl('infodocumentonotacredito_show', array('id' => $entityNotaDeCredito->getId())));
                        }
                        else //Caso contrario muestra un mensaje y no permite crear la nueva nota  de credito
                        {
                            $entityInfoDocumentoFinancieroCab          = $em_financiero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                                       ->find($intIdFactura);
                            $arrayParametros                           = array('entity' => $entityInfoDocumentoFinancieroCab);
                            $arrayParametros['id']                     = $intIdFactura;
                            $arrayParametros['boolTieneSaldo']         = false;
                            $arrayParametros['strObservacionSinSaldo'] = "No se pudo crear la nota de credito, el valor total de la nueva nota de ".
                                                                         "credito ($".$intNuevoSaldo.") ";
                            
                            if( floatval($intNuevoSaldo) > 0 )
                            {
                                $arrayParametros['strObservacionSinSaldo'] .= "supera el saldo disponible ($".$intSaldo.")";
                            }
                            else
                            {
                                $arrayParametros['strObservacionSinSaldo'] .= "no es válido.";
                            }
                            
                            $em_financiero->getConnection()->commit();
                            $em_financiero->getConnection()->close();

                            return $this->render('financieroBundle:InfoNotaCredito:show.html.twig', $arrayParametros);
                        }
                    }
                    else
                    {
                        $arrayParametros['idFactura']               = 0;
                        $arrayParametros['valorSubTotalFactura']    = 0;
                        $arrayParametros['strMensajeError']         = $arrayGetValorTotalNcByFactura['strMensajeError'];

                        $em_financiero->getConnection()->rollback();
                        $em_financiero->getConnection()->close();
                        
                        return $this->render('financieroBundle:InfoNotaCredito:new.html.twig', $arrayParametros);
                    }
                }
                catch(\Exception $ex)
                {
                    $arrayParametros['idFactura']               = 0;
                    $arrayParametros['valorSubTotalFactura']    = 0;
                    $arrayParametros['strMensajeError']         = 'Existio un error en createAction - '.$ex->getMessage();

                    $em_financiero->getConnection()->rollback();
                    $em_financiero->getConnection()->close();

                    return $this->render('financieroBundle:InfoNotaCredito:new.html.twig', $arrayParametros);
                }
            }
            else
            {
                $arrayParametros['idFactura']               = 0;
                $arrayParametros['valorSubTotalFactura']    = 0;
                $arrayParametros['strMensajeError']         = 'El login de la factura a la que esta tratando de '
                                                                . 'aplicar Nota de Crédito no corresponde al login que está en sesion'
                                                                . ' actualmente, favor verificar';
                
                return $this->render('financieroBundle:InfoNotaCredito:new.html.twig', $arrayParametros);
            }  
        }

        $arrayParametros['idFactura']               = 0;
        $arrayParametros['valorSubTotalFactura']    = 0;
        $arrayParametros['strMensajeError']         = 'Debe tener un cliente en sesion para crear la nota de crédito.';

        return $this->render('financieroBundle:InfoNotaCredito:new.html.twig', $arrayParametros);
    }//createAction

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
        
        return $this->render('financieroBundle:InfoNotaCredito:edit.html.twig', array(
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
                        $entitydet->setProductoId($info->codigo);
                    }    
                    if($info->tipo=='PL')
                    {
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

        return $this->render('financieroBundle:InfoNotaCredito:edit.html.twig', array(
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
            //$em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('InfoNotaCredito'));
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
                //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
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
    
    /**
     * El metodo listarTodasNCAction crea la informacion a mostrar en el grid de notas de credito 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 14-10-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 02-02-2015
     * @since 1.1
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 03-02-2015
     * @since 1.2
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.4 09-02-2015
     * @since 1.3
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.5 04-03-2015
     * @since 1.4
     * @author Alexander Samaniego
     * @version 1.6 09-09-2015
     * @since 1.5
     * 
     * @author Alexander Samaniego
     * @version 1.7 28-06-2016 Se agrega funcionalidad para poder simular un comprobante XML
     * @since 1.6
     * 
     * @author Anabelle Penaherrera. <apenaherrera@telconet.ec>
     * @version 1.8 06-02-2018 
     * Actualización: Se agrega funcionalidad que permita consultar si el comprobante rechazado NC puede ser actualizado,
     * se agrega llamada a la tabla de parametros 'ESTADO_COMPROBANTE_RECHAZADO' donde se tendra configurado los estados permitidos
     * para las empresas TN y MD.
     * 
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.10 17-05-2018 - Se agrega parametro de impresora fiscal.
     *
     * @author Luis Lindao <llindao@telconet.ec>
     * @version 1.11 17-02-2019 - Se agrega agrega parametro Prefijo empresa
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.12 15-03-2019 - Se agrega parametro de factura electrónica GT.
     *
     * @author : Gustavo Narea <gnarea@telconet.ec>
     * @version 1.13 19-05-2022 Se remueve link de imprimirFactura Unitaria
     * @return Response  Retorna la informacion del grid de notas de credito
     */
    public function listarTodasNCAction()
    {

        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $arrayPtoCliente    = $objSession->get('ptoCliente');
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $intIdOficina       = $objSession->get('idOficina');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $arrayFeDesde       = explode('T', $objRequest->get("fechaDesde"));
        $arrayFeHasta       = explode('T', $objRequest->get("fechaHasta"));
        $strEstado          = $objRequest->get("estado");
        $intLimite          = $objRequest->get("limit");
        $intPagina          = $objRequest->get("page");
        $intInicio          = $objRequest->get("start");
        $intIdPunto         = "";
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');
        $emGeneral          = $this->get('doctrine')->getManager('telconet_general');
        $strNombreParametro = 'ESTADO_COMPROBANTE_RECHAZADO';
        $strModulo          = 'FINANCIERO';
        $strProceso         = 'FACTURACION';
        $boolImpresoraPanama =  false;
        $boolFaceGuatemala  =  false;
        
        $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
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

        $arrayParametros['arrayTipoDocumento']      = ['NC'];
        $arrayParametros['arrayEstado']             = '';
        if($strEstado != '')
        {
            $arrayParametros['arrayEstado']             = array($strEstado);
        }
        $arrayParametros['intLimit']                = $intLimite;
        $arrayParametros['intPage']                 = $intPagina;
        $arrayParametros['intStart']                = $intInicio;
        $arrayParametros['intIdPunto']              = $intIdPunto;
        $arrayParametros['intIdEmpresa']            = $intIdEmpresa;
        $arrayParametros['intIdOficina']            = $intIdOficina;
        $arrayParametros['strFeCreacionDesde']      = $arrayFeDesde[0];
        $arrayParametros['strFeCreacionHasta']      = $arrayFeHasta[0];
        $arrayInfoDocumentoFinancieroCab            = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                         ->findNotasCredito($arrayParametros);
        $objInfoDocumentoFinancieroCab              = $arrayInfoDocumentoFinancieroCab['registros'];
        $intTotalRegistros                          = $arrayInfoDocumentoFinancieroCab['total'];

        $intCambiaColor = 1;
        foreach($objInfoDocumentoFinancieroCab as $objInfoDocumentoFinancieroCab):
            $strColorClaseCss = '';
            if($intCambiaColor % 2 == 0)
            {
                $strColorClaseCss = 'k-alt';
            }

            $boolVerificaActualiza          = false;
            $boolUrlMensajesCompElectronico = true;
            $boolSimularCompElec            = false;
            $boolVerificaConError           = $serviceInfoCompElectronico->getCompruebaEstadoComprobante($objInfoDocumentoFinancieroCab->getId(), 0);
            $boolVerificaRechazada          = $serviceInfoCompElectronico->getVerificaComprobanteByEstado($objInfoDocumentoFinancieroCab->getId(), 
                                                                                                         $arrayEstados);
            $boolVerificaEnvioNotificacion  = $serviceInfoCompElectronico->getCompruebaEstadoComprobante($objInfoDocumentoFinancieroCab->getId(), 5);
            if($boolVerificaEnvioNotificacion == 5)
            {
                $boolDocumentoPdf               = true;
                $boolDocumentoXml               = true;
            }
            //permite ver en el grid de notas de credito el boton que simula el comprobante electronico
            if('Aprobada' === $objInfoDocumentoFinancieroCab->getEstadoImpresionFact())
            {
                $boolSimularCompElec = true;
            }
            //verifica que se pueda actualizar el comprobante cuando este con errores o caundo sea rechazado.
            if($boolVerificaConError == true || $boolVerificaRechazada == true)
            {
                $boolVerificaActualiza = true;
            }

            $strLinkShow        = $this->generateUrl('infodocumentonotacredito_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));

            //Imprimir NC para TTCO
            if($strPrefijoEmpresa == 'TTCO')
            {
                $strLinkImprimir = $this->generateUrl('infodocumentonotacredito_imprimir', array('id' => $objInfoDocumentoFinancieroCab->getId()));
            }
            
            $em_comercial = $this->get('doctrine')->getManager('telconet');
            $objInfoPunto = $em_comercial->getRepository('schemaBundle:InfoPunto')->find($objInfoDocumentoFinancieroCab->getPuntoId());
            $objInfoPersonaEmpresaRol = $em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                     ->find($objInfoPunto->getPersonaEmpresaRolId()->getId());

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

            if($objInfoDocumentoFinancieroCab->getEstadoImpresionFact() != 'Activo' && 
                $objInfoDocumentoFinancieroCab->getEstadoImpresionFact() != 'Aprobada')
            {
                $strLinkImprimir = "";
            }

            $strFeEmision = "";
            if($objInfoDocumentoFinancieroCab->getFeEmision() != null)
            {
                $strFeEmision = date_format($objInfoDocumentoFinancieroCab->getFeEmision(), "d/m/Y G:i");
            }
            if($strPrefijoEmpresa == 'TNP')
            {
                $boolImpresoraPanama = true;
            }
            if($strPrefijoEmpresa == 'TNG')
            {
                $boolFaceGuatemala = true;
            }

            $arrayResultado[] = array(
                'Numerofacturasri'              => $strNumeroFacturaSri,
                'Punto'                         => $objInfoPunto->getLogin(),
                'Cliente'                       => $strNombreRazonSocialCliente,
                'Esautomatica'                  => ($objInfoDocumentoFinancieroCab->getEsAutomatica() == 'S') ? 'Si' : 'No',
                'strEsElectronica'              => ($objInfoDocumentoFinancieroCab->getEsElectronica() == 'S') ? 'Si' : 'No',
                'Estado'                        => $objInfoDocumentoFinancieroCab->getEstadoImpresionFact(),
                'Fecreacion'                    => strval(date_format($objInfoDocumentoFinancieroCab->getFeCreacion(), "d/m/Y G:i")),
                'Feemision'                     => $strFeEmision,
                'linkVer'                       => $strLinkShow,
                'linkEliminar'                  => '',
                'linkImprimir'                  => $strLinkImprimir,
                'clase'                         => $strColorClaseCss,
                'boton'                         => "",
                'id'                            => $objInfoDocumentoFinancieroCab->getId(),
                'intIdTipoDocumento'            => $objInfoDocumentoFinancieroCab->getTipoDocumentoId()->getId(),
                'boolMensajesCompElectronico'   => $boolUrlMensajesCompElectronico,
                'boolImpresoraPanama'           => $boolImpresoraPanama,
                'boolFaceGuatemala'             => $boolFaceGuatemala,
                'boolVerificaActualiza'         => $boolVerificaActualiza,
                'boolDocumentoPdf'              => $boolDocumentoPdf,
                'boolDocumentoXml'              => $boolDocumentoXml,
                'boolSimularCompElec'           => $boolSimularCompElec,
                'boolVerificaEnvioNotificacion' => $boolVerificaEnvioNotificacion,
                'Total'                         => $objInfoDocumentoFinancieroCab->getValorTotal(),
                'prefijoEmpresa'                => $strPrefijoEmpresa,
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
                'Numerofacturasri'          => "",
                'Punto'                     => "",
                'Cliente'                   => "",
                'Esautomatica'              => "",
                'strEsElectronica'          => "",
                'Estado'                    => "",
                'Fecreacion'                => "",
                'linkVer'                   => "",
                'linkEliminar'              => "",
                'linkImprimir'              => "",
                'clase'                     => "",
                'boton'                     => "display:none;",
                'id'                        => "",
                'intIdTipoDocumento'        => "",
                'boolMensajesCompElectronico'   => false,
                'boolVerificaActualiza'         => false,
                'boolDocumentoPdf'              => false,
                'boolDocumentoXml'              => false,
                'boolVerificaEnvioNotificacion' => false,
                'boolSimularCompElec'           => false,
                'Total'                         => "",
                'prefijoEmpresa'                => $strPrefijoEmpresa,
            );
            $objResponse = new Response(json_encode(array('total' => $intTotalRegistros, 'documentos' => $arrayResultado)));
        }
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
        
        /*
         *  Activo
            Anulado
            Aprobada
            Cerrado
            Pendiente
            Rechazada
            Rechazado*/

        $arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
        $arreglo[]= array('idEstado'=>'Anulado','codigo'=> 'ACT','descripcion'=> 'Anulado');     
        $arreglo[]= array('idEstado'=>'Aprobada','codigo'=> 'ACT','descripcion'=> 'Aprobada');
        $arreglo[]= array('idEstado'=>'Cerrado','codigo'=> 'ACT','descripcion'=> 'Cerrado');
        $arreglo[]= array('idEstado'=>'Pendiente','codigo'=> 'ACT','descripcion'=> 'Pendiente');
        $arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Rechazada');                
        $arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Rechazado');                
                   
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
        //print_r($array_valor);
        $em = $this->getDoctrine()->getManager();
        foreach($array_valor as $id):
            //echo $id;
            $entity=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
            if($entity){
                //$entity->setEstado("Inactivo");
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
    
    
    /**
     * Documentación para el método 'ajaxGenerarDetallesPorDiasAction'
     * 
     * Obtiene los valores acumulados para visualizar en el detalle de la nota d crédito
     * 
     * @return jsonResponse
     * 
     * @version 1.0 Version Inicial
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 21-05-2016 - Se modifica para verificar si la persona paga iva o no.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 01-06-2016 - Se modifica para crear la opción de 'Valor Por Detalle'.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 02-12-2016 - Se envía el parámetro 'strEsCompensado' para verificar si la nota de crédito debe ser compensada.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.4 05-07-2017 - Se envía el parámetro 'strEmpresaCod' para obtener los dias del mes en curso en base a las fechas de facturacion.
     */
    public function ajaxGenerarDetallesPorDiasAction()
    {
        $request                = $this->getRequest(); 
        $objSession             = $request->getSession();
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strEmpresaCod          = $objSession->get('idEmpresa');
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
        $parametros_nc["strEmpresaCod"]          = $strEmpresaCod;
        
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
                //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
                $detalle_orden_l[] = array("informacion"=>"","precio"=>"","cantidad"=>"","descuento"=>"");
        }else{
                $em_comercial = $this->get('doctrine')->getManager('telconet');    
                $detalle_orden_l = array();
                foreach($resultado as $factdet){
                    //carga los motivos de las NC
                    
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
                    $tecn['precio'] = $factdet->getPrecioVentaFacproDetalle();
                    $tecn['cantidad'] = $factdet->getCantidad();
                    $tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
                    
                    if(isset($motivo))
                        $tecn['motivo']=$motivo->getNombreMotivo();
                    else
                        $tecn['motivo']="";
                        
                    $detalle_orden_l[] = $tecn;
                }
        }
        
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /**
    * Documentación para el método 'deleteSeleccionadasAjaxAction'.
    * Este metodo anula un comprobante 'Nota de Credito' por un idFactura específico
    * 
    * Se añade el llamado al Servicio para la anulacion del comprobante electronico via web service en DB_COMPROBANTES
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.1 10-10-2016
    * @Secure(roles="ROLE_67-4897")
    */
    public function deleteSeleccionadasAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $em             = $this->getDoctrine()->getManager("telconet_financiero");
        $peticion       = $this->get('request');
        $session        = $peticion->getSession();
        $idfactura      = $peticion->get('idfactura');
        $motivos        = $peticion->get('motivos');
        $user           = $session->get('user');
        $strIpCreacion  = $peticion->getClientIp();
        $entity                     = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idfactura);
        $serviceInfoCompElectronico = $this->get('financiero.InfoCompElectronico');

        if($entity)
        {
            // Llamado al Servicio para la anulacion del comprobante electronico via web service en Comprobantes EC
            // De no encontrarse en Comprobantes EC, o de no estar Anulado en el SRI, no procede la anulacion en Telcos
            $arrayDocumentosElectronicos = $serviceInfoCompElectronico->anularComprobanteElectronico($idfactura);

            if(!empty($arrayDocumentosElectronicos))
            {
                if($arrayDocumentosElectronicos['estado'] == '8')
                {
                    $entity->setEstadoImpresionFact("Anulado");
                    $em->persist($entity);
                    $em->flush();
                    $entityHistorial = new InfoDocumentoHistorial();
                    $entityHistorial->setDocumentoId($entity);
                    $entityHistorial->setMotivoId($motivos);
                    $entityHistorial->setFeCreacion(new \DateTime('now'));
                    $entityHistorial->setUsrCreacion($user);
                    $entityHistorial->setEstado("Anulado");
                    $em->persist($entityHistorial);
                    $em->flush();

                    $arrayParametrosActivarFactura["strfacturaId"] = $idfactura;
                    $arrayParametrosActivarFactura["strUser"] = $user;

                    //Se envía a Activar la factura asociada a la Notade Credito
                    $statusActivarFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                               ->activaFactura($arrayParametrosActivarFactura);

                    if($statusActivarFactura == '00')
                    {

                        $response = new Response(json_encode(array('success' => true, 'mensaje' => 'La Nota de Credito fue anulada. ')));
                    }
                    elseif($statusActivarFactura == '02')
                    {
                        $response = new Response(json_encode(
                                        array('success' => true, 
                                              'mensaje' => 'EL saldo de la factura asociada a nota de credito insuficiente para su activación. ')));
                    }
                    else
                    {
                        $response = new Response(json_encode(array('success' => true, 
                                                                   'mensaje' => 'Ocurrió un error al tratar de anular la Nota de Credito. ')));
                    }
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
            $response = new Response(json_encode(array('success' => false)));
        }

        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * ncPdfAction, Activa las notas de credito solo para TTCO
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 22-10-2014
     * @since 1.0
     * @param type $id  Recibe el ID del Documento
     * @return Response
     */
    public function ncPdfAction($id)
    {
        /* Como es NC se debe:
         * - Preguntar si es NC
         * - Dar numeracion
         * - Cambiar el estado Activo
         * - Cerrar la factura
         */
        $em             = $this->getDoctrine()->getManager('telconet_financiero');
        $em_comercial   = $this->getDoctrine()->getManager('telconet');
        $entity         = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
        $entityNc       = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($entity->getReferenciaDocumentoId());
        $entityDet      = $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($entity->getId());
        $punto          = $em_comercial->getRepository('schemaBundle:InfoPunto')->find($entity->getPuntoId());
        $datoAdicional  = $em_comercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')->findOneByPuntoId($punto->getId());
        $entityFono     = $em_comercial->getRepository('schemaBundle:InfoPersonaFormaContacto')->findOneBy(array
            ("personaId" => $punto->getPersonaEmpresaRolId()->getPersonaId()->getId(),
            "formaContactoId" => "4",
            "estado" => "Activo"));

        $request        = $this->getRequest();
        $idEmpresa      = $request->getSession()->get('idEmpresa');
        $oficina_id     = $request->getSession()->get('idOficina');
        $session        = $request->getSession();
        $usrCreacion    = $session->get('user');
        //Obtener prefijo de la empresa
        $prefijo        = $em_comercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($idEmpresa);

        $verificar_sri  = $entity->getNumeroFacturaSri();
        $estado         = $entity->getEstadoImpresionFact();

        if($estado == 'Aprobada')
        {
            if($verificar_sri == null || $verificar_sri == "")
            {
                /*
                 * TTCO puede tener numeracion por oficina
                 * MD funciona con una numeracion unicade NC
                 * */
                if($prefijo->getPrefijo() == "TTCO")
                {
                    $datosNumeracion = $em->getRepository('schemaBundle:AdmiNumeracion')
                                        ->findOficinaMatrizYFacturacion($idEmpresa, 'NC');
                }
                else
                {
                    $datosNumeracion = $em->getRepository('schemaBundle:AdmiNumeracion')
                                        ->findOficinaMatrizYFacturacion($idEmpresa, 'NCE');
                }

                $secuencia_asig     = str_pad($datosNumeracion->getSecuencia(), 9, "0", STR_PAD_LEFT);
                $numero_de_factura  = $datosNumeracion->getNumeracionUno() . "-" . $datosNumeracion->getNumeracionDos() . "-" . $secuencia_asig;

                //Pongo la informacion necesaria del historial
                $entity->setNumeroFacturaSri($numero_de_factura);
                $entity->setEstadoImpresionFact('Activo');
                $entity->setFeEmision(new \DateTime('now'));
                $em->persist($entity);
                $em->flush();

                if($entity)
                {
                    //Actualizo la numeracion en la tabla
                    $numero_act = ($datosNumeracion->getSecuencia() + 1);
                    $datosNumeracion->setSecuencia($numero_act);
                    $em->persist($datosNumeracion);
                    $em->flush();
                }

                $entityHistorial = new InfoDocumentoHistorial();
                $entityHistorial->setEstado('Activo');
                $entityHistorial->setDocumentoId($entity);
                $entityHistorial->setUsrCreacion($usrCreacion);
                $entityHistorial->setFeCreacion(new \DateTime('now'));
                $em->persist($entityHistorial);
                $em->flush();

                //Verifico el saldo para cerrar
                    $entity_actCab  = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($entity->getReferenciaDocumentoId());
                    $out_var        = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                        ->getSaldosPorFactura($entity_actCab->getNumeroFacturaSri(), $prefijo->getPrefijo());
                    if($out_var)
                    {
                        //echo $out_var;
                        //anibmolamaru-undefined-isla-ui,004-001-0044626,Activo,100.22
                        $resultado = explode("|", $out_var);

                        /*
                         * Proceso:
                         * - Si el saldo es menor o igual a cero, cierro la factura xq esta saldada
                         * */

                        if($resultado[1] <= 0)
                        {
                            $entity_actCab->setEstadoImpresionFact('Cerrado');
                            $em->persist($entity_actCab);
                            $em->flush();

                            $entityHistorial = new InfoDocumentoHistorial();
                            $entityHistorial->setEstado('Cerrado');
                            $entityHistorial->setObservacion('CerradoNc');
                            $entityHistorial->setDocumentoId($entity_actCab);
                            $entityHistorial->setUsrCreacion($usrCreacion);
                            $entityHistorial->setFeCreacion(new \DateTime('now'));
                            $em->persist($entityHistorial);
                            $em->flush();
                        }

                        if($resultado[1] < 0)
                        {
                            /* Verifico:
                             * - Si el saldo es negativo, debe coincidir con uno o varios detalles de pagos realizados
                             * - Debo buscar ese detalle de pago y clonarlo, pero crearlo como anticipo
                             * */
                            $listadoDetPagos = $em->getRepository('schemaBundle:InfoPagoCab')->findDetalleDePagosPorFactura($entity_actCab->getId());

                            //print_r($listadoDetPagos);
                            $nc_acum         = abs($resultado[1]);
                            $bandera_break   = 0;

                            //busqueda del tipo de documento
                            $documento = $em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')->findOneByCodigoTipoDocumento("NDI");

                            /* Con el listado de pagos voy viendo el valor o los valores que se generan como anticipos */
                            foreach($listadoDetPagos as $ldet):
                                if(($ldet['refencia_nd'] != null && ($ldet['estadoImpresionFact'] != 'Activo')) || 
                                    ($ldet['refencia_nd'] == null && $ldet['estadoImpresionFact'] == null))
                                {
                                    //Obtengo el pago informacion de cabecera y detalle
                                    $origDetalleValor = $em->getRepository('schemaBundle:InfoPagoDet')->find($ldet['id']);

                                    if($nc_acum <= $origDetalleValor->getValorPago())
                                    {
                                        $valor_ant_nuevo = $nc_acum;
                                        $bandera_break   = 1;
                                    }
                                    else
                                    {
                                        $valor_ant_nuevo = $origDetalleValor->getValorPago();
                                        $nc_acum        -= $origDetalleValor->getValorPago();
                                        $bandera_break   = 0;
                                    }
                                    $entityAdmiFormaPagoCruce = $em_comercial->getRepository('schemaBundle:AdmiFormaPago')
                                        ->findOneByCodigoFormaPago('CR');
                                    $origCabecera = $em->getRepository('schemaBundle:InfoPagoCab')->find($ldet['pagoId']);
                                    $entityCabeceraPagoClonado = new InfoPagoCab();
                                    $entityCabeceraPagoClonado = clone $origCabecera;
                                    $entityCabeceraPagoClonado->setEstadoPago('Pendiente');
                                    $entityCabeceraPagoClonado->setFeCreacion(new \DateTime('now'));
                                    //Obtener la numeracion de la tabla Admi_numeracion
                                    $datosNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($idEmpresa, 
                                                                                                                                          $oficina_id,
                                                                                                                                          "ANTC");
                                    $secuencia_asig  = str_pad($datosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                                    $numero_de_pago  = $datosNumeracion->getNumeracionUno() . "-" . $datosNumeracion->getNumeracionDos() . "-" . 
                                        $secuencia_asig;
                                    //Actualizo la numeracion en la tabla
                                    $numero_act      = ($datosNumeracion->getSecuencia() + 1);
                                    $datosNumeracion->setSecuencia($numero_act);
                                    $em_comercial->persist($datosNumeracion);
                                    $em_comercial->flush();

                                    $entityAdmiTipoDocumento = $em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                        ->findOneByCodigoTipoDocumento('ANTC');
                                    $entityCabeceraPagoClonado->setTipoDocumentoId($entityAdmiTipoDocumento);
                                    $entityCabeceraPagoClonado->setValorTotal($valor_ant_nuevo);
                                    $entityCabeceraPagoClonado->setNumeroPago($numero_de_pago);
                                    $entityCabeceraPagoClonado->setAnticipoId(null);
                                    $observacion = "Generado por N/C " . $entity->getNumeroFacturaSri() . ". " . $origCabecera->getComentarioPago();
                                    $entityCabeceraPagoClonado->setComentarioPago($observacion);
                                    $em->persist($entityCabeceraPagoClonado);
                                    $em->flush();

                                    $origDetalle              = $em->getRepository('schemaBundle:InfoPagoDet')->find($ldet['id']);
                                    $entityDetallePagoClonado = new InfoPagoDet();
                                    $entityDetallePagoClonado = clone $origDetalle;
                                    $entityDetallePagoClonado->setEstado('Pendiente');
                                    $entityDetallePagoClonado->setFeCreacion(new \DateTime('now'));
                                    $entityDetallePagoClonado->setValorPago($valor_ant_nuevo);
                                    $entityDetallePagoClonado->setDepositado('S');
                                    $entityDetallePagoClonado->setReferenciaId(null);
                                    $entityDetallePagoClonado->setPagoId($entityCabeceraPagoClonado);
                                    $observacion_pago = "Generado por N/C " . $entity->getNumeroFacturaSri() . ". " . $origDetalle->getComentario();
                                    $entityDetallePagoClonado->setComentario($observacion_pago);
                                    $entityDetallePagoClonado->setFormaPagoId($entityAdmiFormaPagoCruce->getId());
                                    $em->persist($entityDetallePagoClonado);
                                    $em->flush();

                                    /* Por cada anticipo que se va a generar, genero la NDI automaticamente,
                                     * ya que este movimiento termina el proceso
                                     */

                                    //Numeracion del documento
                                    $datosNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($idEmpresa, 
                                                                                                                                          $oficina_id, 
                                                                                                                                          "NDI");
                                    $secuencia_asig = str_pad($datosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                                    $numero_de_nota = $datosNumeracion->getNumeracionUno() . "-" . $datosNumeracion->getNumeracionDos() . "-" . 
                                                      $secuencia_asig;

                                    $entity_ndi = new InfoDocumentoFinancieroCab();
                                    $entity_ndi->setTipoDocumentoId($documento);
                                    $entity_ndi->setPuntoId($entityCabeceraPagoClonado->getPuntoId());
                                    $entity_ndi->setEsAutomatica("S");
                                    $entity_ndi->setProrrateo("N");
                                    $entity_ndi->setReactivacion("N");
                                    $entity_ndi->setRecurrente("N");
                                    $entity_ndi->setComisiona("N");
                                    $entity_ndi->setOficinaId($entityCabeceraPagoClonado->getOficinaId());
                                    $entity_ndi->setFeCreacion(new \DateTime('now'));
                                    $entity_ndi->setFeEmision(new \DateTime('now'));
                                    $entity_ndi->setUsrCreacion($usrCreacion);
                                    $entity_ndi->setEstadoImpresionFact("Cerrado");
                                    $entity_ndi->setNumeroFacturaSri($numero_de_nota);
                                    $entity_ndi->setObservacion("Generado por NDI automatica");
                                    $entity_ndi->setSubtotal($entityDetallePagoClonado->getValorPago());
                                    $entity_ndi->setSubtotalConImpuesto(0);
                                    $entity_ndi->setSubtotalDescuento(0);
                                    $entity_ndi->setValorTotal($entityDetallePagoClonado->getValorPago());
                                    $em->persist($entity_ndi);
                                    $em->flush();

                                    //Detalle
                                    if($entity)
                                    {
                                        //Actualizo la numeracion en la tabla
                                        $numero_act = ($datosNumeracion->getSecuencia() + 1);
                                        $datosNumeracion->setSecuencia($numero_act);
                                        $em_comercial->persist($datosNumeracion);
                                        $em_comercial->flush();
                                    }

                                    $entity_ndi_det = new InfoDocumentoFinancieroDet();
                                    $entity_ndi_det->setDocumentoId($entity_ndi);
                                    $entity_ndi_det->setPuntoId($entityCabeceraPagoClonado->getPuntoId());
                                    $entity_ndi_det->setCantidad(1);
                                    $entity_ndi_det->setEmpresaId($entityCabeceraPagoClonado->getEmpresaId());
                                    $entity_ndi_det->setOficinaId($entityCabeceraPagoClonado->getOficinaId());
                                    //El precio ya incluye el descuento... en el caso de los planes
                                    $entity_ndi_det->setPrecioVentaFacproDetalle($entityDetallePagoClonado->getValorPago());
                                    //El descuento debe ser informativo
                                    $entity_ndi_det->setPorcetanjeDescuentoFacpro(0);
                                    $entity_ndi_det->setFeCreacion(new \DateTime('now'));
                                    $observacion_fact_Detalle = "Generado por NDI automatica: " . $entityDetallePagoClonado->getComentario();
                                    $entity_ndi_det->setObservacionesFacturaDetalle($observacion_fact_Detalle);
                                    $entity_ndi_det->setPagoDetId($origDetalle->getId());
                                    $entity_ndi_det->setUsrCreacion($usrCreacion);
                                    $em->persist($entity_ndi_det);
                                    $em->flush();

                                    if($bandera_break == 1)
                                    {
                                        break;
                                    }
                                }
                            endforeach;
                        }
                    }
            }
        }
        if(!empty($entityFono))
        {
            $telefono = $entityFono->getValor();
        }
        else
        {
            $telefono = "";
        }
        $oficina = $em_comercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        if(!$entity)
        {
            throw $this->createNotFoundException('Unable to find InfoDocumentoFinancieroCab entity.');
        }

        $idx = 0;
        foreach($entityDet as $det):
            //$plan=$em->getRepository('schemaBundle:InfoPlanCab')->find($det->getPlanId());    
            //             
            if($det->getPlanId())
            {

                $plan = $em->getRepository('schemaBundle:InfoPlanCab')->find($det->getPlanId());
                $nombreP = $plan->getNombrePlan();
            }
            else
            {
                $producto = $em->getRepository('schemaBundle:AdmiProducto')->find($det->getProductoId());
                $nombreP = $producto->getDescripcionProducto();
            }

            //                   
            $arreglo[] = array(
                'cantidad' => $det->getCantidad(),
                'plan' => $nombreP,
                'punitario' => $det->getPrecioVentaFacproDetalle(),
                'ptotal' => ($det->getPrecioVentaFacproDetalle() * $det->getCantidad())
            );

            $idx++;
        endforeach;
        //antes 179
        $countBr = 170 - ($idx * 10);
        $html    = $this->renderView('financieroBundle:InfoNotaCredito:recibo.html.twig', array(
            'entity' => $entity,
            'punto' => $punto,
            'oficina' => $oficina,
            'entityNc' => $entityNc,
            'entityDet' => $arreglo,
            'telefono' => $telefono,
            'countBr' => $countBr,
            'datoAdicional' => $datoAdicional,
        ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=recibo-nc-' . trim($punto) . "-" . trim($entity->getNumeroFacturaSri()) . '.pdf',
            )
        );
    }
    
}
?>
