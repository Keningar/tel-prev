<?php

namespace telconet\financieroBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
/**
 * InfoDetalleSolicitud controller.
 *
 */
class AutorizacionNotaCreditoController extends Controller implements TokenAuthenticatedController
{
    
    public function indexAction()
    {
     return $this->render('financieroBundle:Autorizaciones:aprobarNotaCredito.html.twig', array());
    }

    /*
     * gridAprobarNotaCreditoAction, muestra las notas de credito a aprobar
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 10-02-2015
     * @since 1.1
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 11-03-2015
     * @since 1.2
     */
   public function gridAprobarNotaCreditoAction()
    {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $intIdOficina       = $objSession->get('idOficina');
        $arrayFeDesde       = explode('T', $objRequest->get("fechaDesde"));
        $arrayFeHasta       = explode('T', $objRequest->get("fechaHasta"));
        $intLimite          = $objRequest->get("limit");
        $intInicio          = $objRequest->get("start");
        $intPagina          = $objRequest->get("page");
        $strUsuario         = $objRequest->get("strUsrCreacion");
        $strLogin           = $objRequest->get("strLogin");
        $intMontoInicio     = $objRequest->get("intMontoInicio");
        $intMontoFin        = $objRequest->get("intMontoFin");
        $em                 = $this->get('doctrine')->getManager('telconet_financiero');
        $em1                = $this->get('doctrine')->getManager('telconet');
        $em_comercial       = $this->getDoctrine()->getManager('telconet');
        $arrayResultado     = array();
        $arrayBusqueda = array('intIdOficina'           => $intIdOficina,
                                'arrayEstado'           => ['Pendiente'],
                                'intIdPunto'            => '',
                                'arrayTipoDocumento'    => ['NC', 'NCI'],
                                'intLimit'              => $intLimite,
                                'intPagina'             => $intPagina,
                                'intStart'              => $intInicio,
                                'strUsuario'            => $strUsuario,
                                'strLogin'              => $strLogin,
                                'intMontoInicio'        => $intMontoInicio,
                                'intMontoFin'           => $intMontoFin,
                                'intIdEmpresa'          => $intIdEmpresa,
                                'strFeCreacionDesde'    => $arrayFeDesde[0],
                                'strFeCreacionHasta'    => $arrayFeHasta[0]);
        $arrayInfoDocumentoFinancieroCab = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findNotasCredito($arrayBusqueda);
        if($arrayInfoDocumentoFinancieroCab['registros'])
        {
            $objInfoDocumentoFinancieroCab = $arrayInfoDocumentoFinancieroCab['registros'];
            $intTotalRegistros             = $arrayInfoDocumentoFinancieroCab['total'];
            foreach($objInfoDocumentoFinancieroCab as $objInfoDocumentoFinancieroCab):
                $intIdPunto                 = $objInfoDocumentoFinancieroCab->getPuntoId();
                $objInfoPunto               = $em_comercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                $infoDocumentoFinancieroDet = $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                                 ->findByDocumentoId($objInfoDocumentoFinancieroCab->getId());
                if($infoDocumentoFinancieroDet)
                {
                    if($infoDocumentoFinancieroDet[0]->getMotivoId() != '')
                    {
                        $objAdmiMotivo = $em1->getRepository('schemaBundle:AdmiMotivo')->find($infoDocumentoFinancieroDet[0]->getMotivoId());
                    }
                    $strNombreMotivo = '';
                    if($objAdmiMotivo){
                        $strNombreMotivo = $objAdmiMotivo->getNombreMotivo();
                    }
                    $strLinkShow = $this->generateUrl('infodocumentonotacredito_show', array('id' => $objInfoDocumentoFinancieroCab->getId()));
                    //Setea solo NC o NCI en estado Pendiente
                    if($objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Pendiente')
                    {
                        $arrayResultado[] = array('id'                  => $objInfoDocumentoFinancieroCab->getId(),
                                                  'pto'                 => $objInfoPunto->getLogin(),
                                                  'cliente'             => $objInfoPunto 
                                                                           ? $objInfoPunto->getPersonaEmpresaRolId()->getPersonaId()->__toString()
                                                                           : '',
                                                  'numero'              => $objInfoDocumentoFinancieroCab->getNumeroFacturaSri(),
                                                  'valorTotal'          => $objInfoDocumentoFinancieroCab->getValorTotal(),
                                                  'estadoImpresionFact' => $objInfoDocumentoFinancieroCab->getEstadoImpresionFact(),
                                                  'feCreacion'          => strval(date_format($objInfoDocumentoFinancieroCab->getFeCreacion(), "d/m/Y G:i")),
                                                  'usrCreacion'         => $objInfoDocumentoFinancieroCab->getUsrCreacion(),
                                                  'observacion'         => $objInfoDocumentoFinancieroCab->getObservacion(),
                                                  'motivo'              => $strNombreMotivo,
                                                  'strEsElectronica'    => ($objInfoDocumentoFinancieroCab->getEsElectronica() == 'S')? 'Si' : 'No',
                                                  'linkVer'             => $strLinkShow
                        );
                    }
                }
            endforeach;
        }
        if(empty($arrayResultado))
        {
            $arrayResultado[] = array('id'                  => '',
                                      'pto'                 => '',
                                      'numero'              => '',
                                      'valorTotal'          => '',
                                      'estadoImpresionFact' => '',
                                      'feCreacion'          => '',
                                      'usrCreacion'         => '',
                                      'observacion'         => '',
                                      'motivo'              => '',
                                      'strEsElectronica'    => '',
                                      'linkVer'             => ''
                        );
        }
        $objResultado   = json_encode($arrayResultado);
        $resultado      = '{"total":"' . $intTotalRegistros . '","encontrados":' . $objResultado . '}';
        $objResponse    = new Response();
        $objResponse->headers->set('Content-type', 'text/json');

        $objResponse->setContent($resultado);

        return $objResponse;
    }

    /**
     * El metodo aprueba las notas de credito en estado pendientes
     * y si son notas de credito de megadatos les pone numeracion electronica
     * caso contrario las numera de forma normal
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 13-10-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 10-02-2015
     * @since 1.1
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.3 11-03-2015
     * @since 1.2
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 22-05-2016 - Se modifica que se creen notas de crédito electrónicas para TN
     * @since 1.3
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 - Se envia el codEmpresa para enviar el mail a las personas correctas dependiendo de la empresa a la que pertenece el usuario en 
     *                session. 
     * @since 15-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 29-06-2016 - Se verifica que al crear la NC se escoja la oficina, empresa y codigo que corresponde para la numeración de los
     *                           documentos de manera correcta, este cambio solo aplica para TN
     * 
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.7 11-07-2016 - Se debe numerar oficinas solo con numeracion de gye y uio
     * documentos de Telconet Guayaquil -> numera con numeración de Telconet Guayaquil
     * documentos de Telconet Quito -> numera con numeración de Telconet Quito
     * documentos de Telconet Cuenca -> numera con numeración de Telconet Guayaquil
     * documentos de Telconet Salinas -> numera con numeración de Telconet Guayaquil
     * documentos de Telconet Quevedo -> numera con numeración de Telconet Guayaquil
     * documentos de Telconet Manta -> numera con numeración de Telconet Guayaquil
     * documentos de Telconet Loja -> numera con numeración de Telconet Guayaquil
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.8 13-10-2016 
     * Se Habilita generacion de Notas de Credito Interna Para TN
     * Se crea a nivel de ADMI_PARAMETRO las oficinas habilitadas a Numerar NCI en TN y MD     
     * Llamo a funcion aplicarNciInterna que ejecuta el procedimiento P_APLICA_NOTA_CREDITO genera los ANTC y NDI 
     * que se requieran al aplicar la Nota de Credito Interna
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.9 17-11-2016 - Se modifica función para actualizar la fecha de emisión de la NC o NCI con la fecha actual que se desea aprobar el
     *                           documento.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.10 18-15-2018 - Se agrega validación para que no se permita la aprobación de notas de crédito a facturas  que están en estado: 
     *                            Anulado, Rechazado, Pendiente o Eliminado.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.11 26-12-2018 - Se realizan cambios para que la Aprobacion de NC realice la Activacion de las NC de Panama y Aplique la NC
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.12 08-01-2019 - Se agrega validación para que no se permita la aprobación de una nota de crédito cuando exista otra en estado 
     *                            Aprobada sobre la misma factura a la que aplica y que adicional se agregue el respectivo historial.
     * 
     * @author Josselhin Moreira Q. <kjmoreira@telconet.ec>
     * @version 1.13 28-05-2019 - Se agrega logs para monitoreo del proceso de aprobación y control de errores. 
     *  
     * @author Katherine Yager  <kyager@telconet.ec>
     * @version 1.14 13-12-2019 - Se agrega creación de proceso masivo al inicio de la aprobación de NC, una vez culminada 
     * se finaliza el proceso masivo, esto para evitar duplicidad en numeración de NC por ejecución de dos procesos a la vez.
     * 
     * @return String Retorna un string
     * 
     * @author José Candelario  <jcandelario@telconet.ec>
     * @version 1.15 08-07-2020 - Se quita la creación de procesos masivos 'AprobarNC' para ser agregados en el método aprobarNotaCreditoAction
     * 
     * @author José Candelario  <jcandelario@telconet.ec>
     * @version 1.16 29-12-2020 - Se incluye validación que solo considere documentos en estado Pendiente antes de realizar cualquier evaluación 
     *                            con el documento.
     *
     */
   public function aprobarNotaCreditoAjaxAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user');
        $intIdEmpresa           = $objRequest->getSession()->get('idEmpresa');
        $intOficinaId           = $objRequest->getSession()->get('idOficina');
        $em                     = $this->getDoctrine()->getManager('telconet_financiero');
        $emComercial            = $this->get('doctrine')->getManager('telconet');
        $strPrefijoEmp          = $objRequest->getSession()->get('prefijoEmpresa');
        $serviceNotaCredito     = $this->get('financiero.InfoNotaCredito');
        $arrayParametro         = $objRequest->get('param');
        $arrayIdDocumentos      = explode("|", $arrayParametro);
        $intCounter             = 0;
        $strMsnErrorContabiliza = "";
        $serviceUtil            = $this->get('schema.Util');
        $strIpSession           = $objRequest->getClientIp();
        $objRepositoryFinanciero = $em->getRepository("schemaBundle:InfoError");
        $boolExistenNcMismaFact = false;

        //recorremos los id documentos a aprobar.
        $arrayParamDetAprobarNc = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get( "ESTADOS_RECHAZAR_NC", 
                                                      "FINANCIERO", 
                                                      "", 
                                                      "", 
                                                      "", 
                                                      "", 
                                                      "", 
                                                      "",
                                                      "",
                                                      $intIdEmpresa);  
        
        //verificamos los estados permitidos para aprobar NC
        if ( !empty($arrayParamDetAprobarNc) )
        {
            foreach ( $arrayParamDetAprobarNc as $arrayOpcion )
            {
                if (!empty($arrayOpcion['valor2']) )
                {
                    $arrayEstadosNoAprobar[] = $arrayOpcion['valor2'];
                }
            }
        }
        
        //verifica que existan las facturas de las NC que se van a aprobar y las guarda en un arreglo.
        foreach($arrayIdDocumentos as $intIdNotaCredito):
            $objNotaCredito = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIdNotaCredito);
            if(is_object($objNotaCredito))
            {
                $arrayIdsFacturasAplican[] = $objNotaCredito->getReferenciaDocumentoId();
            }
        endforeach;
       
        //verifica que no se dupliquen las facturas.
        //Observación: Pueden existir dos NC para una misma factura y se debe aprobar a las dos (verificar el valor original).
        if(count($arrayIdsFacturasAplican) > count(array_unique($arrayIdsFacturasAplican)))
        {
            $boolExistenNcMismaFact = true;
            $arrayFactFrecuencia    = array_count_values($arrayIdsFacturasAplican);
        }
        
        //Recorremos las NC
        foreach($arrayIdDocumentos as $intIDocumento):
            $em->getConnection()->beginTransaction();
            $entityInfoDocumentoFinancieroCab = null;
            $boolAprobarNC = true;
            $boolExisteNcAprobada      = false;
            $boolExisteNcTotalAplicada = false;
            $boolNcAplicaMismaFactura  = false;
            
            error_log('------------------------------------------------------------------------');
            
            try
            {
                //obtenemos el registro del documento financiero.
                //obtenemos la NC.
                $entityInfoDocumentoFinancieroCab = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIDocumento);
                //si el objeto es null lanza a la excepción.
                if(!$entityInfoDocumentoFinancieroCab)
                {
                    throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                if(is_object($entityInfoDocumentoFinancieroCab) && $entityInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Pendiente')
                {
                    //variable que guarda el id Factura que aplica la NC.
                    $intIdFacturaAplica = $entityInfoDocumentoFinancieroCab->getReferenciaDocumentoId();
                 
                    $objFacturaAplica   = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIdFacturaAplica);
                    if(is_object($objFacturaAplica))
                    {
                        if($boolExistenNcMismaFact)
                        {
                            //Arreglo que guarda cuantas veces se repite la factura.
                            foreach ($arrayFactFrecuencia as $strFactura => $intFrecuencia)
                            {   
                                if((intval($strFactura) === $intIdFacturaAplica) && ($intFrecuencia > 1))
                                {   
                                    $boolNcAplicaMismaFactura = true;
                                }
                            }
                        }
                        
                        //Estado de la factura que se le va a aplicar la NC.
                        $strEstadoFacturaAplica = $objFacturaAplica->getEstadoImpresionFact();
                        
                        $arrayParametroError = array("strAplicacion"     => 'LOG MONITOREO',
                                                     "strProceso"        => 'TELCOS_APROBACION_NC_FACTURAS',
                                                     "strDetalleError"   => 'Factura id desde el objecto: '.$objFacturaAplica->getId().' Estado de la Factura: '.$strEstadoFacturaAplica.' Se repite la factura: '.$boolNcAplicaMismaFactura);
                        
                        
                        $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayParametroError);
                        
                        
                        //Validación de NC aprobadas que ya se le hayan aplicado a la Factura.
                        //Sólo busca NC NCI u otras.
                        $boolExisteNcAprobada = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                   ->hasNcAprobadas($objFacturaAplica->getId());
                        
                        //validación de NC aplicadas a la Factura.
                        //Sólo busca NC no busca NCI u otras.
                        $boolExisteNcTotalAplicada = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                        ->hasNcTotalAplicada($objFacturaAplica->getId());                        
                        
                        $arrayParametrosNc    = array('intIdDocumento'  => $objFacturaAplica->getId(),
                                                      'intReferenciaId' => '');
                        
                        //Obtiene el saldo de la factura.
                        $arrayGetSaldoXFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->getSaldosXFactura($arrayParametrosNc);
                        
                        if(in_array($strEstadoFacturaAplica,$arrayEstadosNoAprobar) || 
                                    $boolExisteNcAprobada      ||
                                    $boolExisteNcTotalAplicada ||
                                    $boolNcAplicaMismaFactura)
                        {
                            $strRespuestaCompleta = "Nota de Crédito no puede ser procesada";
                            $boolAprobarNC = false;
                            $arrayParametroError = array("strAplicacion"     => 'LOG MONITOREO',
                                                         "strProceso"        => 'TELCOS_APROBACION_NC_FACTURAS',
                                                         "strDetalleError"   => 'NC no puede ser procesada, Factura id_documento:'.$objFacturaAplica->getId().' ExisteNcAprobada: '.$boolExisteNcAprobada.' ExisteNcTotalAplicada:'.$boolExisteNcTotalAplicada.'$ NcAplicaMismaFactura:'.$boolNcAplicaMismaFactura.'Prefijo de la empresa: '.$strPrefijoEmp);
                            
                            $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayParametroError);
                            
                        }
                    }
                }
                else
                {
                    $strRespuestaCompleta = "No se encontró la nota de crédito";
                    $arrayParametroError = array("strAplicacion"     => 'LOG MONITOREO',
                                                 "strProceso"        => 'TELCOS_APROBACION_NC_FACTURAS',
                                                 "strDetalleError"   => 'No se encontró la nota de crédito:'.$intIDocumento);
                    
                    $arrayRespuestaError = $objRepositoryFinanciero->setInfoError($arrayParametroError);
                    
                    throw $this->createNotFoundException('No se encontró la solicitud buscada');
                }
                
                //Solo permite aprobar NC o NCI en estado pendientes.
                if($entityInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Pendiente' && $boolAprobarNC)
                {   
                    //obtiene el número de factura SRI.
                    $strNumFacturaSri       = $entityInfoDocumentoFinancieroCab->getNumeroFacturaSri();
                    //obtiene el número SRI de la Factura.
                    $strNumeroFacturaSri= "";
                    //verificamos que no tenga numeración de SRI el documento financiero.
                    if(empty($strNumFacturaSri))
                    {
                        $intCounter     = $intCounter + 1;
                        $strObservacion = '';
                        error_log('preguntamos si es NCI => Nota de credito interna o NC => Nota de Credito');
                        
                        //preguntamos si es NCI => Nota de crédito interna o NC => Nota de Crédito.
                        
                        if($entityInfoDocumentoFinancieroCab->getTipoDocumentoId()->getCodigoTipoDocumento() == "NCI")
                        {
                            $intOficinaParametro = 0;
                            
                            //PARÁMETROS PARA GENERAR SECUENCIA
                            if( $strPrefijoEmp == 'TN' )
                            {   
                                // Si es TN se envía Oficina que generó el documento.
                                $strNombreParametro  = 'NUMERACION_NOTA_CREDITO_INTERNA_TN';
                                $intOficinaParametro = strval($entityInfoDocumentoFinancieroCab->getOficinaId());
                            }
                            elseif( $strPrefijoEmp == 'MD' )
                            {
                                // Si es MD se envia Oficina en sesión.
                                $strNombreParametro   = 'NUMERACION_NOTA_CREDITO_INTERNA_MD';
                                $intOficinaParametro  = $intOficinaId;
                            }
                            elseif( $strPrefijoEmp == 'EN' )
                            {
                                // Si es MD se envia Oficina en sesión.
                                $strNombreParametro   = 'NUMERACION_NOTA_CREDITO_INTERNA_EN';
                                $intOficinaParametro  = $intOficinaId;
                            }
                            
                            //Consulto en la Tabla de PARAMETROS la numeracion de la NCI segun los parametros enviados
                            $arrayParametroDet = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                             ->getOne(
                                                                      $strNombreParametro, 
                                                                     "FINANCIERO", 
                                                                     "", 
                                                                     "", 
                                                                     $intOficinaParametro, 
                                                                     "", 
                                                                     "", 
                                                                     "",
                                                                     "",
                                                                     $intIdEmpresa);
                                
                            // Verifico si se obtiene datos para generar la Numeracion
                            if(isset($arrayParametroDet["valor2"]) && !empty($arrayParametroDet["valor2"]))                            
                            {                                                                            
                                //Se obtiene la secuencia para dar la numeracion
                                $objAdmiNumeracion = $em->getRepository('schemaBundle:AdmiNumeracion')
                                                        ->findByEmpresaYOficina($intIdEmpresa, 
                                                                                intval($arrayParametroDet["valor2"]), 
                                                                                $entityInfoDocumentoFinancieroCab->getTipoDocumentoId()
                                                                                                                 ->getCodigoTipoDocumento());
                                if(!is_object($objAdmiNumeracion))
                                {
                                    throw $this->createNotFoundException('No existe numeración para la Nota de Crédito Interna');
                                }
                            }
                            else
                            {
                                throw $this->createNotFoundException("Falta parámetro para numerar Notas de Crédito Interna");
                            }                                    
                            
                            $strSecuencia           = str_pad($objAdmiNumeracion->getSecuencia(), 9, "0", STR_PAD_LEFT);
                            //Se genera el número de documento NCI
                            $strNumeroFacturaSri    = $objAdmiNumeracion->getNumeracionUno() . "-" . $objAdmiNumeracion->getNumeracionDos() 
                                                    . "-" . $strSecuencia;
                            //Actualizó el Número de NCI
                            $entityInfoDocumentoFinancieroCab->setFeEmision(new \DateTime('now'));
                            $entityInfoDocumentoFinancieroCab->setNumeroFacturaSri($strNumeroFacturaSri);
                            $entityInfoDocumentoFinancieroCab->setEstadoImpresionFact('Activo');
                            $em->persist($entityInfoDocumentoFinancieroCab);
                            $em->flush();
                           
                            //Actualizo la secuencia de la Numeración.
                            $strSecuenciaNumeracion = ($objAdmiNumeracion->getSecuencia() + 1);
                            $objAdmiNumeracion->setSecuencia($strSecuenciaNumeracion);
                            $em->persist($objAdmiNumeracion);
                            $em->flush();
                            $strEstadoDocHst = 'Activo';
                            $strObservacion  = 'Se activó la nota de credito interna';   
                            $strRespuestaCompleta = 'Error en el Proceso de Notas de Créditos Iterna.';
                            $arrayParametroError = array("strAplicacion"     => 'LOG MONITOREO',
                                                        "strProceso"        => 'TELCOS_SECUENCIA_NCI',
                                                        "strDetalleError"   => 'Actualización del Número de NCI interna SRI de '.$strNumeroFacturaSri.' para '.$strSecuenciaNumeracion.' Prefijo de la empresa: '.$strPrefijoEmp);
                            
                            $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayParametroError);
                         
                        }
                        else /*Por falso se refiere a las notas de crédito.*/
                        {
                            //verificar que sólo ingresen NC.
                            /*
                             * Obtiene la secuencia de numeracion segun el prefijo de la empresa, NCE => Nota de credito electronica, 
                             * NC => Nota de Credito
                             */
                            $strTipoDocumentoFinanciero =($strPrefijoEmp=='EN'||$strPrefijoEmp=='MD'||$strPrefijoEmp=='TN') ? 'NCE' : 'NC';
                            
                            if( $strPrefijoEmp == 'TN' )
                            {
                                //CONSULTA EN LA TABLA PARAMETROS LA NUMERACION SEGUN LA OFICINA
                                $arrayParametroDet= $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne(
                                                                    "NUMERACION_NOTA_CREDITO_TN", 
                                                                    "FINANCIERO", 
                                                                    "", 
                                                                    "", 
                                                                    strval($entityInfoDocumentoFinancieroCab->getOficinaId()), 
                                                                    "", 
                                                                    "", 
                                                                    "",
                                                                    "",
                                                                    $intIdEmpresa);
                                 
                                //Crea numeración según la oficina.
                                if ($arrayParametroDet["valor2"])
                                {    
                                    //Obtiene los datos de numeración.
                                    $entityDatosNumeracion  = $em->getRepository('schemaBundle:AdmiNumeracion')
                                                                 ->findOneBy( array( 'empresaId' => $intIdEmpresa, 
                                                                                     'oficinaId' => intval($arrayParametroDet["valor2"]), 
                                                                                     'codigo'    => $strTipoDocumentoFinanciero ) );                                    
                                }
                                else
                                {
                                    $strRespuestaCompleta = 'Faltan datos para numerar';
                                    throw $this->createNotFoundException("faltan datos para numerar");
                                }    
                            }
                            else
                            {  
                                
                                //Obtiene los datos de numeración.
                                $entityDatosNumeracion  = $em->getRepository('schemaBundle:AdmiNumeracion')
                                                             ->findOficinaMatrizYFacturacion($intIdEmpresa, $strTipoDocumentoFinanciero);
                            }
                            
                            $strSecuencia           = str_pad($entityDatosNumeracion->getSecuencia(), 9, "0", STR_PAD_LEFT);
                            //Genera el número de factura
                            $strNumeroFacturaSri    = $entityDatosNumeracion->getNumeracionUno() . "-" . $entityDatosNumeracion->getNumeracionDos() 
                                                      . "-" . $strSecuencia;
                            
                            //Actualiza el número de factura SRI.
                            $entityInfoDocumentoFinancieroCab->setNumeroFacturaSri($strNumeroFacturaSri);
                            $strSecuenciaNumeracion = ($entityDatosNumeracion->getSecuencia() + 1);
                            $entityDatosNumeracion->setSecuencia($strSecuenciaNumeracion);
                            $entityInfoDocumentoFinancieroCab->setFeEmision(new \DateTime('now'));
                            
                            $arrayParametroError = array("strAplicacion"   => 'LOG MONITOREO',
                                                         "strProceso"      => 'TELCOS_SECUENCIA_NC',
                                                         "strDetalleError" => 'Actualización del numero de factura SRI para NC de '.$strNumeroFacturaSri.' para  '.$strSecuenciaNumeracion.' Prefijo de la Empresa: '.$strPrefijoEmp);
                            
                            $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayParametroError);
                                                        
                            if( $strPrefijoEmp == 'TNP'  || $strPrefijoEmp == 'TNG')
                            {
                                $entityInfoDocumentoFinancieroCab->setEstadoImpresionFact('Activo');
                            }
                            else
                            {
                                $entityInfoDocumentoFinancieroCab->setEstadoImpresionFact('Aprobada');   
                            }                            
                            $em->persist($entityInfoDocumentoFinancieroCab);
                            $em->flush();
                            if( $strPrefijoEmp == 'TNP'  || $strPrefijoEmp == 'TNG')
                            {
                                $strEstadoDocHst = 'Activo';
                                $strObservacion  = 'Se Activo la nota de credito ';
                            }
                            else
                            {
                                $strEstadoDocHst = 'Aprobada';
                                $strObservacion  = 'Se aprobo la nota de credito ';   
                            }

                            
                            $arrayError = array("strAplicacion"     => 'LOG MONITOREO',
                                                "strProceso"        => 'TELCOS_APROBACION_NC',
                                                "strDetalleError"   => 'Actualiza el número de factura SRI: '.$strSecuenciaNumeracion.' Id_documento:'.$intIDocumento.' Factura aplica: '.$intIdFacturaAplica.' Estado: '.$strEstadoDocHst.' Prefijo de la empresa: '.$strPrefijoEmp);
                            
                            $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayError);
                           
                        }
                        
                    }
                    else
                    {
                        $strRespuestaCompleta = "La Nota de Crédito ya tiene numeración del SRI. Por favor comuníquese con sistemas."; 
                        $arrayParametroError = array("strAplicacion"     => 'LOG MONITOREO',
                                                     "strProceso"        => 'TELCOS_APROBACION_NC_FACTURAS',
                                                     "strDetalleError"   => 'NC ya tiene número de documento SRI, Facturas  id_documento:');
                        
                        $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayRespuestaError);
                        throw $this->createNotFoundException($strRespuestaCompleta);
                    }
                    
                    //Crea el historial del documento financiero segun el tipo
                    $entityInfoDocumentoHistorial = new InfoDocumentoHistorial();
                    $entityInfoDocumentoHistorial->setDocumentoId($entityInfoDocumentoFinancieroCab);
                    $entityInfoDocumentoHistorial->setUsrCreacion($strUsrCreacion);
                    $entityInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                    $entityInfoDocumentoHistorial->setEstado($strEstadoDocHst);
                    $entityInfoDocumentoHistorial->setObservacion($strObservacion);
                    $em->persist($entityInfoDocumentoHistorial);
                    $em->flush();
                    
                    $arrayParametroError = array("strAplicacion"      => 'LOG MONITOREO',
                                                 "strProceso"         => 'TELCOS_HISTORIAL_NC',
                                                 "strDetalleError"    => 'Historial de la NC: '.$intIDocumento);
                    
                    $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayParametroError);
                   
                    //Si es una nota de credito interna se procede a cerrar la factura en caso de que el saldo de la factura sea <= 0
                    //Si es Panama y es NC, se procede a cerrar la factura en caso de que el saldo de la factura sea <= 0 y se Aplica la NC.
                    if($entityInfoDocumentoFinancieroCab->getTipoDocumentoId()->getCodigoTipoDocumento() == "NCI"
                       || ( $strPrefijoEmp == 'TNP' && $entityInfoDocumentoFinancieroCab->getTipoDocumentoId()->getCodigoTipoDocumento() == "NC" ))
                    {
                         //Obtiene la factura por la referencia documento id.
                        $entityActInfoDocFinanCab = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                       ->find($entityInfoDocumentoFinancieroCab->getReferenciaDocumentoId());
                        
                        $arrayParametrosSend      = array('intIdDocumento'  => $entityActInfoDocFinanCab->getId(), 
                                                          'intReferenciaId' => null);
                        //Obtiene el saldo de la factura
                        $arrayGetSaldoXFactura = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getSaldosXFactura($arrayParametrosSend);
                        //Si existe error enviara un mensaje y no actualizara el estado de la factura a cerrado asi como no creara historial
                        if(!empty($arrayGetSaldoXFactura['strMessageError']))
                        {
                            $strRespuestaCompleta = 'Error en aprobarNotaCreditoAjaxAction, no se cerro la factura # '
                                                    . $entityActInfoDocFinanCab->getNumeroFacturaSri(). ' ' . $arrayGetSaldoXFactura['strMessageError'];
                        }
                        else
                        {
                            $strRespuestaCompleta = "Error al crear el historial de la Nota de Crédito...";
                            /*Si no existe actualizara el estado de la factura a cerrado asi como creara historial*/
                            if($arrayGetSaldoXFactura['intSaldo'] <= 0)
                            {
                                if($strPrefijoEmp == 'TNP')
                                {
                                    $strObservacionFact = 'La factura se cerro por la nota de credito # ';
                                }
                                else
                                {
                                    $strObservacionFact = 'La factura se cerro por la nota de credito interna # ';
                                }
                                $entityActInfoDocFinanCab->setEstadoImpresionFact('Cerrado');
                                $em->persist($entityActInfoDocFinanCab);
                                $em->flush();

                                $entityInfoDocumentoHistorial = new InfoDocumentoHistorial();
                                $entityInfoDocumentoHistorial->setEstado('Cerrado');
                                $entityInfoDocumentoHistorial->setDocumentoId($entityActInfoDocFinanCab);
                                $entityInfoDocumentoHistorial->setUsrCreacion($strUsrCreacion);
                                $entityInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                                $entityInfoDocumentoHistorial->setObservacion($strObservacionFact 
                                                                              . $entityInfoDocumentoFinancieroCab->getNumeroFacturaSri());
                                $em->persist($entityInfoDocumentoHistorial);
                                $em->flush();
                            }
                            
                            $arrayParametroError = array("strAplicacion"      => 'LOG MONITOREO',
                                                         "strProceso"         => 'TELCOS_HISTORIAL_NC',
                                                         "strDetalleError"    => 'Historial de la NC en el proceso: '.$intIDocumento.' Prefijo de la Empresa: '.$strPrefijoEmp);
                    
                            $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayParametroError);
                            
                            
                        }
                        // Llamo a función aplicarNciInterna que ejecuta el procedimiento P_APLICA_NOTA_CREDITO genera los ANTC y NDI 
                        // que se requieran al aplicar la Nota de Crédito Interna o la NC para el caso de Panama.
                        
                        $arrayNciAplica                              = array();
                        $arrayNciAplica["intIdDocumento"]            = $entityInfoDocumentoFinancieroCab->getId();
                        $arrayNciAplica["intRefereneciaDocumentoId"] = $entityInfoDocumentoFinancieroCab->getReferenciaDocumentoId();                        
                        $arrayNciAplica["intOficinaId "]             = $entityInfoDocumentoFinancieroCab->getOficinaId();
                        $strMsnErrorAplicaNci                        = "";
                        
                        $strMsnErrorAplicaNci = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                   ->aplicarNciInterna($arrayNciAplica);    
                        if($strMsnErrorAplicaNci)
                        {   
                            $strRespuestaCompleta = "Existe Error al aplicar la Nota de Crédito Interna.";
                            throw $this->createNotFoundException("Existe Error al aplicar la Nota de Crédito Interna"); 
                        }
                        
                        //Contabilizacion de documento NCI
                        $arrayParamContabiliza = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmp, "", "", "");

                        $arrayContabilidad     = array();
                        //Se verifica si esta habilitada la Contabilizacion para la empresa en sesion
                        if($arrayParamContabiliza["valor2"]=="S")
                        {    
                            $arrayContabilidad["strEmpresaCod"]          = $intIdEmpresa;
                            $arrayContabilidad["strPrefijo"]             = $strPrefijoEmp;
                            $arrayContabilidad["strCodigoTipoDocumento"] = "NCI";
                            $arrayContabilidad["strTipoProceso"]         = "INDIVIDUAL";
                            $arrayContabilidad["intIdDocumento"]         = $entityInfoDocumentoFinancieroCab->getId();
                            
                            $strRespuestaCompleta = "Existe Error al contabilizar la Nota de Crédito Interna.";
                            //Llamo a Funcion que ejecuta paquete que contabiliza NCI
                            $strMsnErrorContabiliza = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                         ->contabilizarDocumentosNCI($arrayContabilidad); 
                        }                        
                    }
                   
                    $intReferenciaDocumentoId = $entityInfoDocumentoFinancieroCab->getReferenciaDocumentoId();
                    
                    //Revisa que la nota de credito tenga un referenciaDocumentoId
                    if(!empty($intReferenciaDocumentoId))
                    {
                        
                    
                        $entityInfoDocumentoFinancieroCabFac    = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                     ->find($entityInfoDocumentoFinancieroCab->getReferenciaDocumentoId());
                        
                        $strNumeroNc                            = ($entityInfoDocumentoFinancieroCab->getNumeroFacturaSri()) ? 
                                                                   $entityInfoDocumentoFinancieroCab->getNumeroFacturaSri() : 'Sin Aprobar';
                        
                        $strLogin   = 'No tiene login';
                                                
                        $intPuntoId = $entityInfoDocumentoFinancieroCab->getPuntoId();
                        
                        $arrayParametroError = array("strAplicacion"      => 'LOG MONITOREO',
                                                     "strProceso"         => 'TELCOS_HISTORIAL_NC',
                                                     "strDetalleError"    => 'Creación de correo para notificación de la NC en el proceso: '.$intIDocumento.' Prefijo de la Empresa: '.$strPrefijoEmp);
                    
                        $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayParametroError);
                        
                        //Verifica si la nota de credito tiene un puntoId
                        if(!empty($intPuntoId)){
                            $entityInfoPunto    = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                              ->find($entityInfoDocumentoFinancieroCab->getPuntoId());
                            $strLogin           = $entityInfoPunto->getLogin();
                        }
                        
                        
                        $strFila    .= '<tr>'
                                        . '<td>'   . $intCounter . '</td>'
                                        . '<td>'   . $strLogin . '</td>'
                                        . '<td>'   . $entityInfoDocumentoFinancieroCabFac->getNumeroFacturaSri() . '</td>'
                                        . '<td> $' . round($entityInfoDocumentoFinancieroCabFac->getValorTotal(), 2) . '</td>'
                                        . '<td>'   . $strNumeroNc . '</td>'
                                        . '<td> $' . $entityInfoDocumentoFinancieroCab->getValorTotal() . '</td>'
                                        . '<td>'   . $strEstadoDocHst . '</td>'
                                    . '</tr>';
                        $arrayCorreosUsrCreacion[]    = array('strUsrCreacionH' => $entityInfoDocumentoFinancieroCab->getUsrCreacion());
                    }
                    else
                    {
                        //La nota de crédito no tiene una referenciaDocumentoId
                        $arrayParametroError = array("strAplicacion"     => 'LOG MONITOREO',
                                                    "strProceso"        => 'TELCOS_RECHAZO_NC',
                                                    "strDetalleError"   => 'La nota de crédito no tiene una referenciaDocumentoId, id NC: '.$intIDocumento);
                        
                        $strRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayParametroError);
                        $strRespuestaCompleta = 'La nota de crédito no tiene una referenciaDocumentoId';
                        
                    }
                    $em->getConnection()->commit();
                }
                elseif ($boolAprobarNC === false) 
                {
                    // Se cambia estado Rechazado la nota de crédito y se crea el historial respectivo.
                    $strEstadoNc = 'Rechazado';
                    
                    if($boolExisteNcAprobada)
                    {
                        $strObservacion  = 'La nota de credito no ha sido aprobada debido a que la factura a la '
                                         . 'que aplica ya posee una nota de credito aprobada';
                        $strEstadoNc     = 'Pendiente';
                        
                    }
                    else if($boolExisteNcTotalAplicada)
                    {
                        $strObservacion  = 'La nota de credito no ha sido aprobada debido a que ya existe una nc aplicada  al valor total de la '
                                         . 'factura ';
                        $strEstadoNc     = 'Pendiente';
                    }     
                    else if($boolNcAplicaMismaFactura)
                    {
                        $strObservacion  = 'La nota de credito no ha sido aprobada debido a que ya existen varias nc por aprobar para una misma '
                                         . 'factura ';
                        $strEstadoNc     = 'Pendiente';
                    }                        
                    else 
                    {
                        $strObservacion  = 'La nota de credito ha sido Rechazada debido a que la factura a la '
                                         . 'que aplica se encuentra en estado: '. $strEstadoFacturaAplica;
                    }
                    
                    $arrayParametroError = array("strAplicacion"     => 'LOG MONITOREO',
                                                 "strProceso"        => 'TELCOS_RECHAZO_NC',
                                                 "strDetalleError"   => 'Se cambia estado '.$strEstadoNc.' la nota de crédito y se crea el historial respectivo, Actualiza el número de factura SRI: '.$strSecuenciaNumeracion.' Id_documento:'.$intIDocumento.' Factura aplica: '.$intIdFacturaAplica.' Estado: '.$strEstadoNc);
                    
                    $arrayRespuestaError = $objRepositoryFinanciero->setInfoError($arrayParametroCabecera);
                    
                    $entityInfoDocumentoFinancieroCab->setEstadoImpresionFact($strEstadoNc);
                    $em->persist($entityInfoDocumentoFinancieroCab);
                    $em->flush();
                    
                    $objInfoDocumentoHistorial = new InfoDocumentoHistorial();
                    
                    if($boolExisteNcAprobada || $boolExisteNcTotalAplicada || $boolNcAplicaMismaFactura)
                    {
                        $objInfoDocumentoHistorial->setEstado($strEstadoNc);
                    }
                    else
                    {
                        $objInfoDocumentoHistorial->setEstado($strEstadoFacturaAplica);
                    }
                    
                    $objInfoDocumentoHistorial->setDocumentoId($entityInfoDocumentoFinancieroCab);
                    $objInfoDocumentoHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoDocumentoHistorial->setObservacion($strObservacion);
                    $em->persist($objInfoDocumentoHistorial);
                    $em->flush();
                    
                    $em->getConnection()->commit();
                }
                else 
                {
                    $strRespuesta  = ""; 
                    $strRespuestaCompleta = 'Error en la aprobación NC';
                    
                }
                  
            }//try
            catch(\Exception $ex)
            {
                $arrayParametroError = array("strAplicacion"     => 'LOG MONITOREO',
                                             "strProceso"        => 'TELCOS_EXCEPTION_NC',
                                             "strDetalleError"   => 'Existió un error al tratar de aprobar la nota de crédito');

                $arrayRespuestaError  = $objRepositoryFinanciero->setInfoError($arrayParametroCabecera);

                    
                $strRespuestaCompleta .= 'Existio un error al tratar de aprobar la nota de credito '.
                                         $entityInfoDocumentoFinancieroCab->getNumeroFacturaSri().' - '.$ex->getMessage().' ';
                $serviceUtil->insertError('Telcos+', 'aprobarNotaCreditoAjaxAction', $ex->getMessage(), $strUsrCreacion, $strIpSession);
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
        endforeach;
        
        error_log($strRespuestaError);
        
        $arrayParametrosEnvio['strCodEmpresa']          = $intIdEmpresa;
        $arrayParametrosEnvio['strNombreParametro']     = 'ENVIO_CORREO';
        $arrayParametrosEnvio['strModulo']              = 'FINANCIERO';
        $arrayParametrosEnvio['strProceso']             = 'NOTAS_CREDITO';
        $arrayParametrosEnvio['strAccionGeneral']       = 'APRUEBA_NC';
        $arrayParametrosEnvio['strAccionUnitaria']      = 'APRUEBA_NC_FROM_SUBJECT';
        $arrayParametrosEnvio['strUser']                = trim($strUsrCreacion);
        $arrayParametrosEnvio['intIdMotivo']            = '';
        $arrayParametrosEnvio['strObservacion']         = '';
        $arrayParametrosEnvio['strFila']                = $strFila;
        $arrayParametrosEnvio['strCodigoPlantilla']     = 'APRUEBA_NC';
        $arrayParametrosEnvio['strProcesoNc']           = 'ncAprobadas';
        $arrayParametrosEnvio['arrayUsrCreacionH']      = $arrayCorreosUsrCreacion;
        
        $serviceNotaCredito->notificaProcesoNotaCredito($arrayParametrosEnvio);
        
        if($boolExistenNcMismaFact)
        {
            $strRespuestaCompleta = 'ExistenNcMismaFact';
        }
        else if($boolExisteNcAprobada || $boolExisteNcTotalAplicada)
        {
            $strRespuestaCompleta = 'Existen documentos que no han sido aprobados. Favor su respectiva revisión.';              
        }
        
        $strRespuesta = new Response();
        $strRespuesta->headers->set('Content-Type', 'text/plain');
        $strRespuesta->setContent($strRespuestaCompleta);

        return $strRespuesta;
    }

    /*
     * rechazarNotaCreditoAjaxAction, realiza el proceso de rechazo de notas de credito
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 16-04-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 22-05-2016 - Se modifica para que envíe notificación cuando rechazan una Nota de crédito
     * @since 1.0
     */
    public function rechazarNotaCreditoAjaxAction()
    {
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $strUsrCreacion          = $objSession->get('user');
        $objResponse             = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objResponse->setContent("error del Form");
        $em                      = $this->getDoctrine()->getManager('telconet_financiero');
        $serviceNotaCredito      = $this->get('financiero.InfoNotaCredito');
        $arrayCorreosUsrCreacion = array();
        $intCounter              = 0;
        $emComercial             = $this->get('doctrine')->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $strParametro       = $objRequest->get('param');
        $intIdMotivo        = $objRequest->get('motivoId');
        $arrayIdDocumento   = explode("|", $strParametro);

        $em->getConnection()->beginTransaction();
        try
        {
            foreach($arrayIdDocumento as $intIdDocumento):
                
                $intCounter ++;
            
                $entityInfoDocumentoFinancieroCab = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($intIdDocumento);
                if(!$entityInfoDocumentoFinancieroCab)
                {
                    throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $entityInfoDocumentoFinancieroCab->setEstadoImpresionFact('Rechazado');
                $em->persist($entityInfoDocumentoFinancieroCab);
                $em->flush();

                //Grabamos en la tabla de historial de la solicitud
                $entityHistorial = new InfoDocumentoHistorial();
                $entityHistorial->setEstado('Rechazado');
                $entityHistorial->setMotivoId($intIdMotivo);
                $entityHistorial->setDocumentoId($entityInfoDocumentoFinancieroCab);
                $entityHistorial->setUsrCreacion($strUsrCreacion);
                $entityHistorial->setFeCreacion(new \DateTime('now'));
                $em->persist($entityHistorial);
                $em->flush();
                
                $intReferenciaDocumentoId = $entityInfoDocumentoFinancieroCab->getReferenciaDocumentoId();
                //Revisa que la nota de credito tenga un referenciaDocumentoId
                if(!empty($intReferenciaDocumentoId))
                {
                    $entityInfoDocumentoFinancieroCabFac = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                              ->find($entityInfoDocumentoFinancieroCab->getReferenciaDocumentoId());
                    $strNumeroNc                         = ($entityInfoDocumentoFinancieroCab->getNumeroFacturaSri()) ? 
                                                            $entityInfoDocumentoFinancieroCab->getNumeroFacturaSri() : 'Sin Aprobar';
                    $strLogin   = 'Notiene login';
                    $intPuntoId = $entityInfoDocumentoFinancieroCab->getPuntoId();
                    
                    //Verifica si la nota de credito tiene un puntoId
                    if(!empty($intPuntoId))
                    {
                        $entityInfoPunto    = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                          ->find($entityInfoDocumentoFinancieroCab->getPuntoId());
                        $strLogin           = $entityInfoPunto->getLogin();
                    }

                    $strFila .= '<tr>'
                                    . '<td>'   . $intCounter . '</td>'
                                    . '<td>'   . $strLogin . '</td>'
                                    . '<td>'   . $entityInfoDocumentoFinancieroCabFac->getNumeroFacturaSri() . '</td>'
                                    . '<td> $' . round($entityInfoDocumentoFinancieroCabFac->getValorTotal(), 2) . '</td>'
                                    . '<td>'   . $strNumeroNc . '</td>'
                                    . '<td> $' . $entityInfoDocumentoFinancieroCab->getValorTotal() . '</td>'
                                    . '<td>Pendiente</td>'
                                . '</tr>';
                    
                    $arrayCorreosUsrCreacion[] = array('strUsrCreacionH' => $entityInfoDocumentoFinancieroCab->getUsrCreacion());
                }//(!empty($intReferenciaDocumentoId))
            endforeach;
            
            $em->getConnection()->commit();
            $objResponse->setContent("Se rechazaron las solicitudes con exito.");
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $objResponse->setContent($e->getMessage());
        }
        
        $arrayParametrosEnvio['strNombreParametro']     = 'ENVIO_CORREO';
        $arrayParametrosEnvio['strModulo']              = 'FINANCIERO';
        $arrayParametrosEnvio['strProceso']             = 'NOTAS_CREDITO';
        $arrayParametrosEnvio['strAccionGeneral']       = 'RECHAZA_NC';
        $arrayParametrosEnvio['strAccionUnitaria']      = 'RECHAZA_NC_FROM_SUBJECT';
        $arrayParametrosEnvio['strUser']                = trim($strUsrCreacion);
        $arrayParametrosEnvio['intIdMotivo']            = '';
        $arrayParametrosEnvio['strObservacion']         = '';
        $arrayParametrosEnvio['strFila']                = $strFila;
        $arrayParametrosEnvio['strCodigoPlantilla']     = 'RECHAZA_NC';
        $arrayParametrosEnvio['strProcesoNc']           = 'ncRechazadas';
        $arrayParametrosEnvio['arrayUsrCreacionH']      = $arrayCorreosUsrCreacion;

        $serviceNotaCredito->notificaProcesoNotaCredito($arrayParametrosEnvio);
        
        return $objResponse;
    } //rechazarNotaCreditoAjaxAction
    

    public function getMotivosRechazoNotaCredito_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobacionnotacredito','AutorizacionNotaCredito','rechazarnotacreditoajax');
		$arreglo=array();
    //print_r($datos);die;
    foreach($datos as $valor):
        //print_r($entityAdmiTipoSolicitud[0]->getId());
            $arreglo[] = array(
                'idMotivo' => $valor->getId(),
                'descripcion' => $valor->getNombreMotivo(),
                'idRelacionSistema'=>$valor->getRelacionSistemaId()
            );
    endforeach;
    //die;

        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /*
     * Documentación para el método 'aprobarNotaCreditoAction'.
     *
     * Se crea método como capa superior para consumir el método aprobarNotaCreditoAjaxAction, con el objetivo que
     * se cree un proceso masivo y se finalice una vez procesadas las nc sea por éxito o por errores controlados y 
     * no controlados para evitar la duplicidad de NUMERO_FACTURA_SRI en los documentos NC.
     * 
     * @author  José Candelario <jcandelario@telconet.ec>
     * @version 1.0 08-07-2020
     */
   public function aprobarNotaCreditoAction()
    {
        $objRequest                = $this->getRequest();
        $objSession                = $objRequest->getSession();
        $strUsrCreacion            = $objSession->get('user');
        $intIdEmpresa              = $objRequest->getSession()->get('idEmpresa');
        $intOficinaId              = $objRequest->getSession()->get('idOficina');
        $strPrefijoEmp             = $objRequest->getSession()->get('prefijoEmpresa');
        $arrayParametro            = $objRequest->get('param');
        $strIpSession              = $objRequest->getClientIp();
        $serviceNotaCredito        = $this->get('financiero.InfoNotaCredito');
        $serviceUtil               = $this->get('schema.Util');
        $emInfraestructura         = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emFinanciero              = $this->getDoctrine()->getManager('telconet_financiero');

        try
        {
            $objInfoProcesoMasivoCab   = $emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoCab')
                                                           ->findBy(array('tipoProceso' => 'AprobarNC',
                                                                          'estado'      => 'Pendiente'));

            if(empty($objInfoProcesoMasivoCab))
            {
                $arrayParametrosCrear['strCodEmpresa']          = $intIdEmpresa;
                $arrayParametrosCrear['strTipoProceso']         = 'AprobarNC';
                $arrayParametrosCrear['strEstado']              = 'Pendiente';
                $arrayParametrosCrear['strUsrCreacion']         = $strUsrCreacion;
                $arrayParametrosCrear['strIpCreacion']          = $strIpSession;

                $serviceNotaCredito->procesoMasivoNC($arrayParametrosCrear);

                $arrayParametrosAprobarNC['strUsrCreacion']     = $strUsrCreacion;
                $arrayParametrosAprobarNC['intIdEmpresa']       = $intIdEmpresa;
                $arrayParametrosAprobarNC['intOficinaId']       = $intOficinaId;
                $arrayParametrosAprobarNC['strPrefijoEmp']      = $strPrefijoEmp;
                $arrayParametrosAprobarNC['arrayParametro']     = $arrayParametro;
                $arrayParametrosAprobarNC['strIpSession']       = $strIpSession;

                $strRespuesta = $this->aprobarNotaCreditoAjaxAction($arrayParametrosAprobarNC);

                $arrayParametrosFinalizar['strCodEmpresa']      = $intIdEmpresa;
                $arrayParametrosFinalizar['strTipoProceso']     = 'AprobarNC';
                $arrayParametrosFinalizar['strEstado']          = 'Finalizado';
                $arrayParametrosFinalizar['strUsrCreacion']     = $strUsrCreacion;
                $arrayParametrosFinalizar['strIpCreacion']      = $strIpSession;

                $serviceNotaCredito->procesoMasivoNC($arrayParametrosFinalizar);
            }
            else
            {
                $strRespuestaCompleta = 'Se encuentra en proceso una Aprobación masiva de NC, por favor espere unos minutos y vuelva a intentar.';
                $strRespuesta = new Response();
                $strRespuesta->headers->set('Content-Type', 'text/plain');
                $strRespuesta->setContent($strRespuestaCompleta);
            }
        }catch(\Exception $e)
        {
            $strRespuestaCompleta = 'Existio un error al tratar de aprobar la(s) nota(s) de credito(s).';
            $serviceUtil->insertError('Telcos+', 'aprobarNotaCreditoAction', $e->getMessage(), $strUsrCreacion, $strIpSession);
            $emFinanciero->getConnection()->rollback();
            $emFinanciero->getConnection()->close();
            $strRespuesta = new Response();
            $strRespuesta->headers->set('Content-Type', 'text/plain');
            $strRespuesta->setContent($strRespuestaCompleta);
        }

        return $strRespuesta;
    }

}
