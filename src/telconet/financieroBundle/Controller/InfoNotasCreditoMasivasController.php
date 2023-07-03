<?php

namespace telconet\financieroBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Documentación para el controlador 'InfoNotasCreditoMasivasController'.
 * InfoNotasCreditoMasivasController, Contiene los metodos para la creacion de notas de credito masivas.
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 24-02-2015
 */
class InfoNotasCreditoMasivasController extends Controller
{

    /**
     * indexAction, Redirecciona al index del las notas de credito masivas.
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 24-02-2015
     * @return redirecciona a la pagina del index de las notas de credito masivas
     */
    /**
    * @Secure(roles="ROLE_276-1")
    */
    public function indexAction()
    {
        return $this->render('financieroBundle:InfoNotasCreditoMasivas:index.html.twig');
    }//indexAction

    /**
     * getEstadosDocumentosAction, Obtiene los estados de los documentos financieros
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 24-02-2015
     * @return json Retorna los diferentes estados de los documentos financieros
     */
    public function getEstadosDocumentosAction()
    {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $arrayParametros    = array('arrayEstadoTipoDocumentos'=> ['Activo'],
                                    'arrayCodigoTipoDocumento' => ['FAC', 'FACP'],
                                    'arrayEstadoOficina'       => ['Activo'],
                                    'intIdEmpresa'             => $intIdEmpresa,
                                    'arrayEstados'             => ['Activo', 'Cerrado'],
                                    'strFechaInicio'           => '01-10-2014', 
                                    'strFechaFin'              => '');
        $emFinanciero = $this->get('doctrine')->getManager('telconet_financiero');
        $entityEstados = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getEstadosByTipoDocumentos($arrayParametros);
        $arrayStoreEstados = array();
        if(!empty($entityEstados['arrayResultado'])){
            foreach($entityEstados['arrayResultado'] as $objEstados):
                $arrayStoreEstados[] = array('strEstadoDocumento' => $objEstados['estadoDocumento']);
            endforeach;
        }elseif(!empty($entityEstados['strMensajeError'])){
            $arrayStoreEstados[] = array('strEstadoDocumento' => 'Error - '.$entityEstados['strMensajeError']);
        }
        $objResponse = new Response(json_encode(array('jsonEstadosByTipoDocumento' => $arrayStoreEstados)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //getEstadosDocumentosAction

    /**
     * getPlanesAction, Obtiene los planes en estado Activo y segun la empresa en sesion
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 24-02-2015
     * @return json Retorna un json de los planes en estado Activo y segun la empresa
     */
    public function getPlanesAction()
    {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $emComerial         = $this->getDoctrine()->getManager();
        $entityInfoPlan     = $emComerial->getRepository('schemaBundle:InfoPlanCab')
                                         ->findBy(array('estado'            => 'Activo', 
                                                        'empresaCod'        => $intIdEmpresa), 
                                                        array('nombrePlan'  => 'ASC'));
        $arrayStorePlanes = array();
        foreach($entityInfoPlan as $objInfoPlan):
            $arrayStorePlanes[] = array('intIdPlan'     => $objInfoPlan->getId(), 
                                        'strNombrePlan' => $objInfoPlan->getNombrePlan());
        endforeach;
        $objResponse = new Response(json_encode(array('jsonPlanes' => $arrayStorePlanes)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //getPlanesAction

    /**
     * getProductosAction, Obtiene los productos en estado Activo y segun la empresa en sesion
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 24-02-2015
     * @return json Retorna un json de los productos en estado Activo y segun la empresa
     */
    public function getProductosAction()
    {
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $emComerial         = $this->getDoctrine()->getManager();
        $entityAdmiProducto = $emComerial->getRepository('schemaBundle:AdmiProducto')
                                         ->findBy(array('estado'                    => 'Activo', 
                                                        'empresaCod'                => $intIdEmpresa), 
                                                        array('descripcionProducto' => 'ASC'));
        $arrayStoreProductos = array();
        foreach($entityAdmiProducto as $objAdmiProdcuto):
            $arrayStoreProductos[] = array('intIdProducto'          => $objAdmiProdcuto->getId(),
                                           'strDescripcionProdcuto' => $objAdmiProdcuto->getDescripcionProducto());
        endforeach;
        $objResponse = new Response(json_encode(array('jsonProductos' => $arrayStoreProductos)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //getProductosAction

    /**
     * getMotivosNcAction, Obtiene los motivos de creacion de notas de credito
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 24-02-2015
     * @return json Retorna un json de los motivos para la creacion de nota de credito
     */
    public function getMotivosNcAction()
    {
        $emComerial         = $this->getDoctrine()->getManager();
        $entityAdmiMotivo   = $emComerial->getRepository('schemaBundle:AdmiMotivo')
                                         ->findMotivosPorModuloPorItemMenuPorAccion("nota_de_credito", "Ver nota de credito", "new");
        foreach($entityAdmiMotivo as $objAdmiMotivo):
            $arrayAdmiMotivo[] = array('intIdMotivo'     => $objAdmiMotivo->getId(),
                                       'strNombreMotivo' => $objAdmiMotivo->getNombreMotivo());
        endforeach;
        $objResponse = new Response(json_encode(array('jsonMotivos' => $arrayAdmiMotivo)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //getMotivosNcAction

    /**
     * listarDocumentosFinancieroAction, Lista los documentos financieros, que tengan saldo disponible para generacion de notas de credito, 
     * que no tengan notas credito en estado Pendiente ni Aprobada, para la creacion de notas de credito
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 24-02-2015
     * @return json Retorna un json con la lista de documentos, total de documentos, mensaje de error en caso de existir
     */
    public function listarDocumentosFinancieroAction()
    {
        try
        {
            $strMessageError                                    = '';
            $objRequest                                         = $this->getRequest();
            $objSession                                         = $objRequest->getSession();
            $emFinanciero                                       = $this->get('doctrine')->getManager('telconet_financiero');
            $arrayParametro                                     = array();
            //Filtros tab financiero
            $arrayParametro['emComercial']                      = $this->getDoctrine()->getManager();
            $arrayParametro['strRangoFacturaDesde']             = $objRequest->get('strRangoFacturaDesde');
            $arrayParametro['strRangoFacturaHasta']             = $objRequest->get('strRangoFacturaHasta');
            $arrayParametro['dateFechaDesde']                   = explode('T', $objRequest->get('dateFechaDesde'));
            $arrayParametro['dateFechaHasta']                   = explode('T', $objRequest->get('dateFechaHasta'));
            $arrayParametro['intStart']                         = $objRequest->get('start');
            $arrayParametro['intLimit']                         = $objRequest->get('limit');
            $arrayParametro['intIdOficina']                     = $objRequest->get('intIdOficina');
            $arrayParametro['intIdProducto']                    = $objRequest->get('intIdProducto');
            $arrayParametro['intIdPlan']                        = $objRequest->get('intIdPlan');
            $strEstado                                          = $objRequest->get('strEstado');
            $arrayParametro['arrayEstadoImpresionDocumento']    = (empty($strEstado)) ? 
                                                                        ['Activo', 'Cerrado'] : $objRequest->get('strEstado');
            $arrayParametro['intIdEmpresa']                     = $objSession->get('idEmpresa');
            $arrayParametro['arrayTipoDocumentoFinanciero']     = ['FAC', 'FACP'];
            //Filtros tab comercial
            $arrayParametro['strLogin']                         = $objRequest->get('strLogin');
            $arrayParametro['dateFechaSolicitudDesde']          = explode('T', $objRequest->get('dateFechaSolicitudDesde'));
            $arrayParametro['intIdFormaPago']                   = $objRequest->get('intIdFormaPago');
            $arrayParametro['intIdTipoCuenta']                  = $objRequest->get('intIdTipoCuenta');
            $arrayParametro['intIdTipoSolicitud']               = $objRequest->get('intIdTipoSolicitud');
            $arrayParametro['strIdEstTipoSolicitud']            = $objRequest->get('strIdEstTipoSolicitud');
            //Filtros tecnico
            $arrayParametro['intIdElemento']                    = $objRequest->get('intIdElemento');
            $arrayParametro['intIdInterface']                   = $objRequest->get('intIdInterface');
            $arrayParametro['notExistsDocumento']               = 'Valida que no exista NC in Pendiente or Aprobada';
            $arrayParametro['arrayTipoDocNotExists']            = ['NC'];
            $arrayParametro['arrayEstadoDocNotExists']          = ['Pendiente', 'Aprobada'];
            $arrayParametro['strSaldoDisponible']               = 'Mostrar fact con saldo disponible';
            $arrayParametro['arrayTipoDocumentoSaldoDis']       = ['NC'];
            $arrayParametro['arrayEstadoDocSalDisp']            = ['Activo'];

            //Valida si la fecha de inicio es nula para crear una nueva fecha un año antes a la fecha actual
            if(empty($arrayParametro['dateFechaDesde'][0]) && !empty($arrayParametro['dateFechaHasta'][0]))
            {
                $arrayParametro['dateFechaDesde'] = date('d-M-Y', strtotime('-1 year'));
                $arrayParametro['dateFechaHasta'] = date_format(date_create($arrayParametro['dateFechaHasta'][0]), 'd-M-Y');
            } //Valida si la fecha de inicio no es nula para darle formato y crear la fecha fin
            elseif(!empty($arrayParametro['dateFechaDesde'][0]) && empty($arrayParametro['dateFechaHasta'][0]))
            {
                $arrayParametro['dateFechaDesde'] = date_format(date_create($arrayParametro['dateFechaDesde'][0]), 'd-M-Y');
                $arrayParametro['dateFechaHasta'] = date('d-M-Y');
            } //Valida que las fechas inicio y fin no sean nulas para darle formato
            elseif(!empty($arrayParametro['dateFechaDesde'][0]) && !empty($arrayParametro['dateFechaHasta'][0]))
            {
                $arrayParametro['dateFechaDesde'] = date_format(date_create($arrayParametro['dateFechaDesde'][0]), 'd-M-Y');
                $arrayParametro['dateFechaHasta'] = date_format(date_create($arrayParametro['dateFechaHasta'][0]), 'd-M-Y');
            } //Por falso se crea la fecha unicio y fin
            else
            {
                $arrayParametro['dateFechaDesde'] = date('d-M-Y', strtotime('-1 year'));
                $arrayParametro['dateFechaHasta'] = date('d-M-Y');
            }
            //Obtiene el array de documentos financieros
            $arrayInfoDocumentoFinancieroCab = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                            ->getDocumentosFinanciero($arrayParametro);
            if(empty($arrayInfoDocumentoFinancieroCab['strMensajeError']))
            {
                $arrayStoreDocumentos   = $arrayInfoDocumentoFinancieroCab['arrayStoreDocumentos'];
                $intCounter             = $arrayInfoDocumentoFinancieroCab['intTotalRegistros'];
            }
            else
            {
                $strMessageError = $arrayInfoDocumentoFinancieroCab['strMensajeError'];
            }
        }
        catch(\Exception $ex)
        {
            $strMessageError = 'Existio un error en listarDocumentosFinancieroAction ' . $ex->getMessage();
        }
        $objResponse = new Response(json_encode(array('jsonListaDocumentos' => $arrayStoreDocumentos,
                                                      'intTotalDocumentos'  => $intCounter,
                                                      'strMensajeError'     => $strMessageError)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //listarDocumentosFinancieroAction

    /**
     * creaNotaCreditoMasivaAction, Crea las notas de credito masivas con los id facturas enviados y segun el tipo
     * de nota de credito que se haya seleccionado, hace el llamado al metodo creaNotaCreditoMasiva que llama a un
     * procedimiento que se encarga de hacer las notas de credito y enviar una notifacion por correo
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 24-02-2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 11-03-2015
     * @since 1.0
     * @return string messageStatus Retorna un mensaje devuelto por el proceso de generacion de notas de credito masivas
     */
    /**
    * @Secure(roles="ROLE_276-1")
    */
    public function creaNotaCreditoMasivaAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $emFinanciero           = $this->get('doctrine')->getManager('telconet_financiero');
      
        $arrayParametrosSend    = array('clbDocumentos'      => $objRequest->get('strIdDocumentos'),
                                        'strDelimitador'     => '-',
                                        'strObservacion'     => preg_replace('/[^\da-z]/i', ' ', $objRequest->get('strObservacion')),
                                        'intIdMotivo'        => $objRequest->get('intIdMotivo'),
                                        'strUsrCreacion'     => $objSession->get('user'),
                                        'strTipoNotaCredito' => $objRequest->get('strTipoNotaCredito'),
                                        'strEstadoNc'        => 'Pendiente',
                                        'intIdOficina'       => $objSession->get('idOficina'),
                                        'intIdEmpresa'       => $objSession->get('idEmpresa'),
                                        'intPorcentaje'      => $objRequest->get('intPorcentaje'),
                                        'strFechaInicio'     => date('d-m-Y', strtotime(explode('T', $objRequest->get('dateFechaDesdePro'))[0])),
                                        'strFechaFin'        => date('d-m-Y', strtotime(explode('T', $objRequest->get('dateFechaHastaPro'))[0])));
        $arrayCreaNcValorOriginal = $emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                 ->creaNotaCreditoMasiva($arrayParametrosSend);
        //Valida si strMsnError esta vacia, para setear $strResultado con el resultado devuelto por el proceso
        if(empty($arrayCreaNcValorOriginal['strMsnError']))
        {
            $strResultado = $arrayCreaNcValorOriginal['strMsnResultado'];
        }
        //por false setea $strResultado con strMsnError devuelto por el procedimiento
        else
        {
            $strResultado = $arrayCreaNcValorOriginal['strMsnError'];
        }
        $objResponse = new Response(json_encode(array('messageStatus' => $strResultado)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    } //creaNotaCreditoMasivaAction
    

}
