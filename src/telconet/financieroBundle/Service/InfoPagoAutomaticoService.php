<?php
namespace telconet\financieroBundle\Service;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoCab;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoDet;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoHist;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\InfoDeposito;
use telconet\schemaBundle\Entity\InfoDepositoHistorial;
use Symfony\Component\HttpFoundation\Response;

class InfoPagoAutomaticoService 
{ 
    private $emComercial;
    private $emFinanciero;
    private $emComunicacion;
    private $emGeneral;
    private $serviceInfoPago;
    private $serviceInfoPagoDet;
    private $serviceProcesoMasivo;
    private $serviceUtil;
    private $serviceInfoCompElectronico;
    private $serviceInfoPunto;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        $this->emComercial                = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emFinanciero               = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emGeneral                  = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emComunicacion             = $objContainer->get('doctrine.orm.telconet_comunicacion_entity_manager');
        $this->serviceUtil                = $objContainer->get('schema.Util');
        $this->serviceInfoPago            = $objContainer->get('financiero.InfoPago'); 
        $this->serviceInfoPagoDet         = $objContainer->get('financiero.InfoPagoDet');
        $this->serviceProcesoMasivo       = $objContainer->get('tecnico.ProcesoMasivo');
        $this->serviceInfoCompElectronico = $objContainer->get('financiero.InfoCompElectronico');
        $this->serviceInfoPunto           = $objContainer->get('comercial.InfoPunto');
    
        $this->serviceTokenCas            = $objContainer->get('seguridad.TokenCas');
        $this->serviceEmpleadoListar      = $objContainer->getParameter('ws_ms_emplados_listar');
        $this->serviceRestClient          = $objContainer->get('schema.RestClient');
        $this->utilService                = $objContainer->get('schema.Util');
  
    }
       
    
     /**
     * getNumDocumentoByNumDocSustento
     * Función que retorna el número de documento asociado al valor enviado como parámetro.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 05-02-2021
     *
     * @param array $arrayParametros [
     *                                strNumDocSustento Referencia al valor en el tag numDocSustento del xml de retención.
     *                                strCodEmpresa     Código de la empresa
     *                               ] 
     * @return $string $strNumeroDocumento
     */
    public function getNumDocumentoByNumDocSustento($arrayParametros)
    {    
        $strNumeroDocumento  = '';
        
        $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                               ->findOneBy(array('nombreParametro' => 'AUTOMATIZACION_RETENCIONES', 
                                                                 'estado'          => 'Activo'));
        
        if(is_object($objAdmiParametroCab))
        {              
            $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->findOneBy(array('parametroId' => $objAdmiParametroCab,
                                                          'descripcion' => 'NUMERO DOCUMENTO',
                                                          'empresaCod'  => $arrayParametros['strCodEmpresa'],
                                                          'estado'      => 'Activo'));           
            if(is_object($objAdmiParametroDet))
            {
                $intPosicionNumeracionA  = intval($objAdmiParametroDet->getValor1());
                $intPosicionNumeracionB  = intval($objAdmiParametroDet->getValor2());
                $intPosicionSecuencia    = intval($objAdmiParametroDet->getValor3());
                $strSeparador            = $objAdmiParametroDet->getValor4();

                $strNumeracionUno = substr($arrayParametros['strNumDocSustento'], $intPosicionNumeracionA,3);
                $strNumeracionDos = substr($arrayParametros['strNumDocSustento'], $intPosicionNumeracionB,3);
                $strSecuencia     = substr($arrayParametros['strNumDocSustento'], $intPosicionSecuencia,20);

                $strNumeroDocumento    = $strNumeracionUno.$strSeparador.$strNumeracionDos.$strSeparador.$strSecuencia;                  
            }
            
        }
        return $strNumeroDocumento;
    }
    
     /**
     * procesarRetenciones
     * 
     * Método que realiza el procesamiento y generación de pagos por retención según los datos enviados como parámetros.
     *         
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 04-04-2019           
     * @param array $arrayParametros[                  
     *              'strCodEmpresa'            => Código de empresa en sesión
     *              'strPrefijoEmpresa'        => Prefijo de empresa en sesión
     *              'strUsrCreacion'           => Usuario de creación 
     *              'strIpCreacion'            => Ip de creación
     *              'arrayIdsPagosAutomaticos' => Array con ids de retenciones seleccionadas
     *              'strIdsRetencionesSelect'  => string con ids de retenciones seleccionadas                                                  
     * @return $strRespuesta
     */
    public function procesarRetenciones($arrayParametros)
    {
        $strUsrCreacion           = $arrayParametros['strUsrCreacion'];        
        $strIpCreacion            = $arrayParametros['strIpCreacion'];
       
        $this->emFinanciero->beginTransaction();
        try
        {
            $arrayRespuesta = $this->emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')->procesarRetenciones($arrayParametros);
            $strRespuesta   = $arrayRespuesta['strStatus'];
        }
        catch(\Exception $e)
        {
            $this->emFinanciero->rollback();
            $this->emFinanciero->close();           
            $strRespuesta = "No se pudo procesar y generar pagos automaticos: ".$e->getMessage(). ". Favor notificar a Sistemas.";            
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoPagoAutomaticoService.procesarPagos',
                                             'Error InfoPagoAutomaticoService.procesarPagos: No se pudo procesar y generar pagos automaticos: '
                                             .$e->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCreacion);
            return $strRespuesta;
        }
        return $strRespuesta;
    }
    
    
   /**
     * 
     * procesarPago
     * Función que realiza el procesamiento y generación de un pago mediante información enviada como parámetro.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 06-04-2021
     * @since 1.0
     * 
     * @return $objResponse 
     */

    public function procesarPago($arrayParametros)
    {
        $intEmpresaId         = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa    = $arrayParametros['strPrefijoEmpresa'];        
        $strUsuarioCreacion   = $arrayParametros['strUsrCreacion'];       
        $intIdPagoAutomatico  = $arrayParametros['intIdPagoAutomatico'];
        $intIdPagoAutDet      = $arrayParametros['intIdPagoAutDet'];       
        $intIdCliente         = $arrayParametros['intIdCliente'];
        $strIpCreacion        = $arrayParametros['strIpCreacion'];
        
        $objServiceInfoPago            = $this->serviceInfoPago; 
        $objServiceInfoPagoDet         = $this->serviceInfoPagoDet;
        $objServiceProcesoMasivo       = $this->serviceProcesoMasivo;        
        $arrayPagosDetIdContabilidad   = array();
        $arrayParametroDet             = array();
        $arrayPtos                     = array();
        $strMsnErrorContabilidad       = '';
        
        $objInfoPersonaEmpRol      = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                          ->find($intIdCliente);
        if(is_object($objInfoPersonaEmpRol))
        {
            $intOficinaId = $objInfoPersonaEmpRol->getOficinaId()->getId();
        }

        $arrayInfoPagoAutomaticoDet  = $this->emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                          ->findBy(array("pagoAutomaticoId" => $intIdPagoAutomatico,
                                                                         "estado"           => "Pendiente"
                                                                   ));
        
        foreach($arrayInfoPagoAutomaticoDet as $objInfoPagoAutomaticoDet)
        {
            
            $arrayParametrosDoc['strNumDocSustento']    = $objInfoPagoAutomaticoDet->getNumeroFactura();
            $arrayParametrosDoc['strCodEmpresa']        = $intEmpresaId;
            
            $strNumeroFacturaSri                        = $this->getNumDocumentoByNumDocSustento($arrayParametrosDoc);      
            
            $arrayParametrosFact                        = array();
            $arrayParametrosFact['strTipoDocumento']    = 'FAC';            
            $arrayParametrosFact['strNumeroFacturaSri'] = $strNumeroFacturaSri;            
            $arrayParametrosFact['strCodEmpresa']       = $intEmpresaId;  

            $arrayDatosFactura = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->getInformacionDocumento($arrayParametrosFact);
            
            if(count($arrayDatosFactura)>0)
            {
                $intIdDocumento    = $arrayDatosFactura[0]['intIdDocumento'];
                $intIdPunto        = $arrayDatosFactura[0]['intIdPunto'];         
            }
            if(!in_array(intval($intIdPunto),$arrayPtos))
            {
                $arrayPtos[] = intval($intIdPunto);
            }
            
        }
        
        foreach($arrayPtos as $intIdPunto)
        {
            $this->emFinanciero->getConnection()->beginTransaction();
            $this->emComercial->getConnection()->beginTransaction();

            try
            {                
                $floatValorCabeceraPago  = 0;
                //CABECERA DEL PAGO-->>*************//
                //**********************************// 
                $entityInfoPagoCab    = new InfoPagoCab();
                $entityInfoPagoCab->setEmpresaId($intEmpresaId);
                $entityInfoPagoCab->setEstadoPago('Cerrado');
                $entityInfoPagoCab->setFeCreacion(new \DateTime('now'));
                //Obtener la numeracion de la tabla Admi_numeracion
                $objDatosNumeracion = $this->emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                           ->findByEmpresaYOficina($intEmpresaId, $intOficinaId, "PAG");
                $strSecuenciaAsig = str_pad($objDatosNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                $strNumeroPago    = $objDatosNumeracion->getNumeracionUno() . "-" .$objDatosNumeracion->getNumeracionDos() . "-" . $strSecuenciaAsig;

                //Actualizo la numeracion en la tabla
                $intSecuencia = ($objDatosNumeracion->getSecuencia() + 1);
                $objDatosNumeracion->setSecuencia($intSecuencia);
                $this->emComercial->persist($objDatosNumeracion);
                $this->emComercial->flush();

                $entityAdmiTipoDocumento = $this->emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                    ->findOneByCodigoTipoDocumento('PAG');
                $entityInfoPagoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                $entityInfoPagoCab->setNumeroPago($strNumeroPago);
                $entityInfoPagoCab->setOficinaId($intOficinaId);
                $entityInfoPagoCab->setPuntoId($intIdPunto);
                $entityInfoPagoCab->setUsrCreacion($strUsuarioCreacion);
                $entityInfoPagoCab->setValorTotal($floatValorCabeceraPago);
                $entityInfoPagoCab->setDetallePagoAutomaticoId($intIdPagoAutDet);
                $this->emFinanciero->persist($entityInfoPagoCab);
                $this->emFinanciero->flush();

                //DETALLES DEL PAGO-->>*************//
                //**********************************//
                $arrayAnticipo = array();

                $objInfoPagoAutomaticoCab  = $this->emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                                ->find($intIdPagoAutomatico);
                
                if(is_object($objInfoPagoAutomaticoCab))
                {
                    $floatValorPago = 0;
                       
                    foreach($arrayInfoPagoAutomaticoDet as $objInfoPagoAutomaticoDet)
                    {

                        $arrayParametrosDoc['strNumDocSustento']    = $objInfoPagoAutomaticoDet->getNumeroFactura();
                        $arrayParametrosDoc['strCodEmpresa']        = $intEmpresaId;

                        $strNumeroFacturaSri                        = $this->getNumDocumentoByNumDocSustento($arrayParametrosDoc);      

                        $arrayParametrosFact                        = array();
                        $arrayParametrosFact['strTipoDocumento']    = 'FAC';            
                        $arrayParametrosFact['strNumeroFacturaSri'] = $strNumeroFacturaSri;            
                        $arrayParametrosFact['strCodEmpresa']       = $intEmpresaId;  

                        $arrayDatosFactura = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                ->getInformacionDocumento($arrayParametrosFact);

                        if(count($arrayDatosFactura)>0)
                        {
                            $intIdPuntoDet     = $arrayDatosFactura[0]['intIdPunto'];         
                        }
                        
                        if($intIdPunto === $intIdPuntoDet)
                        {
                            $intIdFactura            = intval($arrayDatosFactura[0]['intIdDocumento']);

                            $intIdFormaPago          = $objInfoPagoAutomaticoDet->getFormaPagoId();                       

                            $strReferencia           = $objInfoPagoAutomaticoDet->getNumeroReferencia(); 

                            $floatValorDetPago       = floatval($objInfoPagoAutomaticoDet->getMonto());
                            
                            $floatSaldoFactura       = 0;
                            
                            $objInfoDocumentoFinCab  = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                          ->find($intIdFactura);
                            
                            if(is_object($objInfoDocumentoFinCab))
                            {
                                $floatSaldoFactura = $objInfoDocumentoFinCab->getValorTotal();

                                $arrayParametros   = array('intIdDocumento' => $intIdDocumento, 'intReferenciaId' => '');

                                $arrayGetSaldoXFactura = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                            ->getSaldosXFactura($arrayParametros);

                                if(!empty($arrayGetSaldoXFactura['strMessageError']))
                                {
                                    throw new Exception('Error al obtener el saldo de factura: '. $objInfoDocumentoFinCab->getNumeroFacturaSri());
                                }
                                else
                                {
                                    $floatSaldoFactura = floatval($arrayGetSaldoXFactura['intSaldo']);
                                }
                            }                            

                            if($floatValorDetPago === $floatSaldoFactura || $floatValorDetPago > $floatSaldoFactura)
                            {
                                $floatValorPago          += $floatSaldoFactura;
                            }
                            else
                            {
                                $floatValorPago          += $floatValorDetPago;
                            }
                            
                            $objAdmiFormaPago        = $this->emGeneral->getRepository('schemaBundle:AdmiFormaPago')->find($intIdFormaPago);

                            $strDescFormaPago        = $objAdmiFormaPago->getDescripcionFormaPago();
                            $strTipoFormaPago        = $objAdmiFormaPago->getTipoFormaPago();
                            $strComentario           = $objInfoPagoAutomaticoDet->getObservacion();
                            $strFecha                = $objInfoPagoAutomaticoDet->getFecha();
                            $arrayDetallePago = array(  'idFormaPago'              => $intIdFormaPago,
                                                        'descripcionFormaPago'     => $strDescFormaPago,
                                                        'idFactura'                => $intIdFactura,
                                                        'numeroFactura'            => $strNumeroFacturaSri,
                                                        'idBanco'                  => null,
                                                        'descripcionBanco'         => null,
                                                        'idTipoCuenta'             => null,
                                                        'descripcionTipoCuenta'    => null,
                                                        'numeroReferencia'         => $strReferencia,
                                                        'valorPago'                => $floatValorDetPago,
                                                        'comentario'               => $strComentario,
                                                        'fechaDeposito'            => $strFecha,
                                                        'codigoDebito'             => null,
                                                        'cuentaContableId'         => null,
                                                        'descripcionCuentaContable'=> null,
                                                        'numeroDocumento'          => $strReferencia,
                                                        'strTipoFormaPago'         => $strTipoFormaPago); 
                            //Se crea detalle del pago
                            $arrayResultadoIngresoDetallesPago= $objServiceInfoPagoDet->agregarDetallePago(
                                $entityInfoPagoCab,$arrayDetallePago,new \DateTime('now'),$floatValorCabeceraPago);
                            
                            /**
                             * Bloque que verifica si el detalle del pago genera un anticipo
                             */
                            $arrayResultadoIngresoDetallesPago['strGeneraAnticipo'] = 'N';

                            if( isset($arrayResultadoIngresoDetallesPago['arr_anticipo']) && 
                                !empty($arrayResultadoIngresoDetallesPago['arr_anticipo']) )
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
                            }//( isset($arrayResultadoIngresoDetallesPago['arr_anticipo']) && 
                            //!empty($arrayResultadoIngresoDetallesPago['arr_anticipo']) )

                            $arrayResultadoIngresoDetallesPago['strGeneraAnticipo'] = 'N';

                            $floatValorCabeceraPago        = $arrayResultadoIngresoDetallesPago['valorCabeceraPago'];
                            $arrayPagosDetIdContabilidad[] = $arrayResultadoIngresoDetallesPago;
                        }
                        $objInfoPagoAutomaticoDet->setEstado('Procesado');
                        $this->emFinanciero->persist($objInfoPagoAutomaticoDet);
                        $this->emFinanciero->flush();

                        //Graba historial de detalle de estado de cuenta.
                        $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                        $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet);
                        $objInfoPagoAutomaticoHist->setEstado('Procesado');
                        $objInfoPagoAutomaticoHist->setObservacion('Se cambia estado de detalle a Procesado.');
                        $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                        $objInfoPagoAutomaticoHist->setUsrCreacion($strUsuarioCreacion);
                        $this->emFinanciero->persist($objInfoPagoAutomaticoHist);
                        $this->emFinanciero->flush();

                        $intIdPagAutomatico = $objInfoPagoAutomaticoDet->getPagoAutomaticoId();
                        $arrayDetPendientes = $this->emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                                                 ->findBy(array('pagoAutomaticoId' => $intIdPagAutomatico, 
                                                                                'estado'          => 'Pendiente'));

                        if(count($arrayDetPendientes)===0)
                        {
                            $objInfoPagoAutomaticoCab = $this->emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                                                           ->find($intIdPagAutomatico);
                            if(is_object($objInfoPagoAutomaticoCab))
                            {
                                $objInfoPagoAutomaticoCab->setEstado('Procesado'); 
                                $emFinanciero->persist($objInfoPagoAutomaticoCab);
                                $emFinanciero->flush();                                   
                            }
                        }	
                    }
                    //Se setea valor total de cabecera y hago persistencia
                    $entityInfoPagoCab->setValorTotal($floatValorPago);
                    $emFinanciero->persist($entityInfoPagoCab);
                    $emFinanciero->flush();
                    //Ingresa historial para el pago
                    $objServiceInfoPago->ingresaHistorialPago($entityInfoPagoCab, 'Cerrado', 
                        new \DateTime('now'), $strUsuarioCreacion, null, 'pago creado en forma manual');

                    //CONTABILIZA DETALLES DE PAGO
                    $arrayParametroDet= $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");

                    //**ANTICIPOS -->>***********//
                    //***************************// 
                    //Si sobro valor del pago procede a crear anticipo
                    if(count($arrayAnticipo) > 0)
                    {
                        $intTotalAnticipo = 0;
                        //SUMO el arreglo   
                        for($intCont = 0; $intCont < count($arrayAnticipo); $intCont++)
                        {
                            $intTotalAnticipo = $intTotalAnticipo + $arrayAnticipo[$intCont]['valorAnticipo'];
                        }
                        //SOLO SI LA SUMA DEL VALOR DEL ANTICIPO ES MAYOR A 0 SE CREA ANTICIPO.
                        if($intTotalAnticipo>0)
                        {    
                            //SE CREA LA CABECERA DEL ANTICIPO
                            $entityAnticipoCab = new InfoPagoCab();
                            $entityAnticipoCab->setPagoId($entityInfoPagoCab->getId());
                            $entityAnticipoCab->setEmpresaId($intEmpresaId);
                            $entityAnticipoCab->setEstadoPago('Pendiente');
                            $entityAnticipoCab->setFeCreacion(new \DateTime('now'));

                            //Obtener la numeracion de la tabla Admi_numeracion
                            $objDatosNumeracionAnticipo = $this->emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                ->findByEmpresaYOficina($intEmpresaId, $intOficinaId, "ANT");
                            $strSecuenciaAsig = '';
                            $strSecuenciaAsig = str_pad($objDatosNumeracionAnticipo->getSecuencia(), 7, "0", STR_PAD_LEFT);
                            $intSecNumeroAnticipo = $objDatosNumeracionAnticipo->getNumeracionUno() . 
                                "-" . $objDatosNumeracionAnticipo->getNumeracionDos() . "-" . $strSecuenciaAsig;
                            //Actualizo la numeracion en la tabla
                            $intSecuencia = ($objDatosNumeracionAnticipo->getSecuencia() + 1);
                            $objDatosNumeracionAnticipo->setSecuencia($intSecuencia);
                            $this->emComercial->persist($objDatosNumeracionAnticipo);
                            $this->emComercial->flush();

                            $entityAdmiTipoDocumento = $emFinanciero->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                ->findOneByCodigoTipoDocumento('ANT');
                            $entityAnticipoCab->setTipoDocumentoId($entityAdmiTipoDocumento);
                            $entityAnticipoCab->setNumeroPago($intSecNumeroAnticipo);
                            $entityAnticipoCab->setOficinaId($intOficinaId);
                            $entityAnticipoCab->setPuntoId($intIdPunto);
                            $entityAnticipoCab->setUsrCreacion($strUsuarioCreacion);
                            $entityAnticipoCab->setValorTotal($intTotalAnticipo);
                            $entityAnticipoCab->setDetallePagoAutomaticoId($intIdPagoAutDet);
                            $this->emFinanciero->persist($entityAnticipoCab);
                            $this->emFinanciero->flush();
                            for($intCont = 0; $intCont < count($arrayAnticipo); $intCont++)
                            {
                                if ($arrayAnticipo[$intCont]['valorAnticipo']>0)
                                {    
                                    //CREA LOS DETALLES DEL ANTICIPO
                                    $entityAnticipoDet = new InfoPagoDet();
                                    $entityAnticipoDet->setEstado('Pendiente');
                                    $entityAnticipoDet->setFeCreacion(new \DateTime('now'));
                                    $entityAnticipoDet->setUsrCreacion($strUsuarioCreacion);
                                    $entityAnticipoDet->setValorPago($arrayAnticipo[$intCont]['valorAnticipo']);
                                    $entityAnticipoDet->setComentario($arrayAnticipo[$intCont]['comentario'].
                                        '. (Anticipo generado como saldo a favor)');
                                    $entityAnticipoDet->setCuentaContableId($arrayAnticipo[$intCont]['cuentaContableId']);
                                    $entityAnticipoDet->setFeDeposito(new \DateTime($arrayAnticipo[$intCont]['fechaDeposito']));
                                    $entityAnticipoDet->setDepositado('N');
                                    $entityAnticipoDet->setPagoId($entityAnticipoCab);
                                    $entityAnticipoDet->setFormaPagoId($arrayAnticipo[$intCont]['formaPagoId']);
                                    $entityAnticipoDet->setBancoTipoCuentaId($arrayAnticipo[$intCont]['bancoTipoCuentaId']);
                                    $entityAnticipoDet->setNumeroReferencia($arrayAnticipo[$intCont]['numeroReferencia']);
                                    $entityAnticipoDet->setNumeroCuentaBanco($arrayAnticipo[$intCont]['numeroCtaBanco']);
                                    $this->emFinanciero->persist($entityAnticipoDet);
                                    $this->emFinanciero->flush();

                                    $arrayDetalleAnticipo        = array('intIdPagoDet' => $entityAnticipoDet->getId(), 'strGeneraAnticipo' => 'N');
                                    $arrayPagosDetIdContabilidad[] = $arrayDetalleAnticipo;


                                }
                            }
                            //Ingresa historial para el pago
                            $objServiceInfoPago->ingresaHistorialPago($entityAnticipoCab, 'Pendiente', new \DateTime('now'), 
                                $strUsuarioCreacion, null, 'Anticipo generado por pago #' . $entityInfoPagoCab->getNumeroPago() . 
                                ' creado en forma manual.');                     
                        }                            
                    }
                    //<<--FIN ANTICIPOS ***************//

                }
                $this->emFinanciero->getConnection()->commit();
                $this->emComercial->getConnection()->commit();
                                
            }
            catch(\Exception $e)
            {
                if ($this->emFinanciero->getConnection()->isTransactionActive()) 
                {                        
                    $this->emFinanciero->getConnection()->rollback();
                }
                if ($this->emComercial->getConnection()->isTransactionActive()) 
                {                        
                    $this->emComercial->getConnection()->rollback();
                }         
                $this->emFinanciero->getConnection()->close();
                $this->emComercial->getConnection()->close();

                $strResponse = 'Error';
                return $strResponse;
            }
            //REACTIVA SERVICIOS
            try
            {
                $arrayParams=array(
                'puntos'          => array($intIdPunto),
                'prefijoEmpresa'  => $strPrefijoEmpresa,
                'empresaId'       => $intEmpresaId,
                'oficinaId'       => $intOficinaId,
                'usuarioCreacion' => $strUsuarioCreacion,    
                'ip'              => $strIpCreacion,
                'idPago'          => $entityInfoPagoCab->getId(),
                'debitoId'        => null
                );
                $strMsg = '';
                $strMsg = $objServiceProcesoMasivo->reactivarServiciosPuntos($arrayParams);
            }
            catch(\Exception $e)
            {
                error_log('Error al reactivar servicio '.$e->getMessage().' Msj: '.$strMsg);
            }            
        }
        //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
        if ($arrayParametroDet["valor2"]=="S")
        {    
            $objParametros['serviceUtil'] = $this->serviceUtil ;
            $strMsnErrorContabilidad      = $this->emFinanciero->getRepository('schemaBundle:InfoPagoDet')
                                                 ->contabilizarPagosAnticipo($intEmpresaId, $arrayPagosDetIdContabilidad, $objParametros);
        }
        
        $strResponse = 'OK' ;
        return $strResponse;
    }
    
    
     /**
     * ingresarPagoAutomaticoCab, Función que inserta registro a nivel de la tabla DB_FINANCIERO_INFO_PAGO_AUTOMATICO_CAB.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 02-06-2021     
     * @param array arrayParametros[]     
     *              'intCuentaContableId' => Id de la cuenta contable.
     *              'strRutaArchivo'      => Ruta del archivo.
     *              'strNombreArchivo'    => Nombre del archivo.
     *              'strEstado'           => Estado.
     *              'strFeCreacion'       => Fecha de Creación del Registro.
     *              'strUsrCreacion'      => Usuario de Creación del registro.
     *              'strIpCreacion'       => Ip de Creación del registro.   
     *              'strTipoFormaPago'    => Tipo de forma de pago.     
     */
    public function ingresarPagoAutomaticoCab($arrayParametros)
    {
        $objInfoPagoAutomaticoCab = new InfoPagoAutomaticoCab();
        $objInfoPagoAutomaticoCab->setCuentaContableId($arrayParametros['intCuentaContableId']);
        $objInfoPagoAutomaticoCab->setRutaArchivo($arrayParametros['strRutaArchivo']);
        $objInfoPagoAutomaticoCab->setNombreArchivo($arrayParametros['strNombreArchivo']);
        $objInfoPagoAutomaticoCab->setEstado($arrayParametros['strEstado']);
        $objInfoPagoAutomaticoCab->setFeCreacion($arrayParametros['dateFeCreacion']);
        $objInfoPagoAutomaticoCab->setIpCreacion($arrayParametros['strIpCreacion']);
        $objInfoPagoAutomaticoCab->setUsrCreacion($arrayParametros['strUsrCreacion']);
        $objInfoPagoAutomaticoCab->setTipoFormaPago($arrayParametros['strTipoFormaPago']);
            
        $this->emFinanciero->persist($objInfoPagoAutomaticoCab);
        $this->emFinanciero->flush();
        
        return $objInfoPagoAutomaticoCab;
    } 
    
     /**
     * ingresarPagoAutomaticoDet, Función que inserta registro a nivel de la tabla DB_FINANCIERO_INFO_PAGO_AUTOMATICO_DET.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 02-06-2021     
     * @param array arrayParametros[]     
     *              'intPagoAutomaticoId'      => Id de la cabecera asociada.
     *              'intPersonaEmpresaRolId'   => Id del cliente.
     *              'strEstado'                => Estado.
     *              'intIdFormaPagoRetencion'  => Id de la forma de pago.
     *              'strReferencia'            => Número de referencia.
     *              'strObservacion'           => Observación.
     *              'floatMonto'               => Valor a retener.
     *              'intIdDocumento'           => Id de la factura.
     *              'strFecha'                 => Fecha de Creación del Registro.
     *              'dateFeCreacion'           => Fecha de Creación del Registro.
     *              'strUsrCreacion'           => Usuario de Creación del registro.
     *              'strIpCreacion'            => Ip de Creación del registro.   
     *              'strTipoFormaPago'         => Tipo de forma de pago.     
     */
    public function ingresarPagoAutomaticoDet($arrayParametros)
    {
        $objInfoPagoAutomaticoCab = new InfoPagoAutomaticoCab();
        $objInfoPagoAutomaticoDet = new InfoPagoAutomaticoDet();
        $objInfoPagoAutomaticoDet->setPagoAutomaticoId($arrayParametros['intPagoAutomaticoId']);
        $objInfoPagoAutomaticoDet->setPersonaEmpresaRolId($arrayParametros['intPersonaEmpresaRolId']);
        $objInfoPagoAutomaticoDet->setEstado($arrayParametros['strEstado']);
        $objInfoPagoAutomaticoDet->setFormaPagoId($arrayParametros['intIdFormaPagoRetencion']);
        $objInfoPagoAutomaticoDet->setNumeroReferencia($arrayParametros['strReferencia']);
        $objInfoPagoAutomaticoDet->setObservacion($arrayParametros['strObservacion']);
        $objInfoPagoAutomaticoDet->setMonto(round(floatval($arrayParametros['floatMonto']),2));                   
        $objInfoPagoAutomaticoDet->setFeCreacion($arrayParametros['dateFeCreacion']);
        $objInfoPagoAutomaticoDet->setIpCreacion($arrayParametros['strIpCreacion']);
        $objInfoPagoAutomaticoDet->setUsrCreacion($arrayParametros['strUsrCreacion']);
        $objInfoPagoAutomaticoDet->setDocumentoId(intval($arrayParametros['intIdDocumento']));
        $objInfoPagoAutomaticoDet->setFecha($arrayParametros['strFecha']);
        $objInfoPagoAutomaticoDet->setBaseImponible(round(floatval($arrayParametros['floatBaseImponible']),2));
        $objInfoPagoAutomaticoDet->setBaseImponibleCal(round(floatval($arrayParametros['floatBaseImpCalc']),2));
        $objInfoPagoAutomaticoDet->setCodigoImpuesto($arrayParametros['strCodigo']);
        $objInfoPagoAutomaticoDet->setPorcentajeRetencion(floatval($arrayParametros['floatPorcentaje']));                  
        $objInfoPagoAutomaticoDet->setNumeroFactura($arrayParametros['strNumDocSustento']);
        $objInfoPagoAutomaticoDet->setEmpresaCod($arrayParametros['strCodEmpresa']);
       
        $this->emFinanciero->persist($objInfoPagoAutomaticoDet);
        $this->emFinanciero->flush();
        
        return $objInfoPagoAutomaticoDet;
    }
     /**
     * strposRecursive, Función que retorna las posiciones de todas las ocurrencias de una cadena, usando strpos de forma recursiva.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 13-07-2021
     * @param array arrayParametros[]     
     *              'strHaystack'         => Cadena en donde se realizará la búsqueda.
     *              'strNeedle'           => Cadena a ser buscada.
     *              'strOffset'           => Posición inicial. 
     *    
     */    
    public function strposRecursive($arrayParametros, &$arrayResults = array()) 
    {
        $strHaystack = $arrayParametros['strHaystack'];
        $strNeedle   = $arrayParametros['strNeedle'];
        $strOffset   = $arrayParametros['strOffset'];
        $strOffset   = strpos($strHaystack, $strNeedle, $strOffset);
        if($strOffset === false) 
        {
            return $arrayResults;           
        }
        else 
        {
            $arrayResults[] = $strOffset;
            $arrayParametrosRec                 = array();
            $arrayParametrosRec['strHaystack']  = $strHaystack;
            $arrayParametrosRec['strNeedle']    = $strNeedle;            
            $arrayParametrosRec['strOffset']    = $strOffset+1;            
            
            return $this->strposRecursive($arrayParametrosRec, $arrayResults);
        }
    }
    
     /**
     * generarDeposito. Función que genera un depósito asociado al pago enviado como parámetro.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 27-05-2022
     * @param array arrayParametros
     */   
    public function generarDeposito($arrayParametros) 
    {
        $strEmpresaCod     = $arrayParametros['strEmpresaCod'];
        $strEstado         = $arrayParametros['strEstado'];
        $intOficinaId      = $arrayParametros['intOficinaId'];
		$intIdCtaContable  = $arrayParametros['intIdCtaContable'];        
        $strIpCreacion     = $arrayParametros['strIpCreacion'];        
        $strUsrCreacion    = $arrayParametros['strUsrCreacion'];
        $intIdDeposito     = 0;
        
        $boolDepManual     = $arrayParametros['boolDepManual'];
        
        if($boolDepManual && !empty($arrayParametros['arrayIdsDetallesPago']))
        {
            $arrayIdsDetallesPago  = $arrayParametros['arrayIdsDetallesPago'];            
        }
        else
        {
            $intIdPago              =  $arrayParametros['intIdPago'];            
            $intIdPagoAutDet        =  $arrayParametros['intIdPagoAutDet'];
            $arrayIdsPagosDepositar =  $arrayParametros['arrayPagosDepositar'];            
        }       
        
        //CON EL ID CUENTA SE DEBE CONSULTAR EN EL NAF LA CUENTA CONTABLE
		$objCuentaContable = $this->emFinanciero->getRepository('schemaBundle:AdmiCuentaContable')->find($intIdCtaContable);
        
        //cuenta contable es quemada temporalmente
        $strCtaContable    = $objCuentaContable->getCuenta();
        $strNoCta          = $objCuentaContable->getNoCta();
        $this->emFinanciero->getConnection()->beginTransaction();
        try 
        {
            
            if($boolDepManual)
            {
                $floatValorDeposito = $this->emFinanciero->getRepository('schemaBundle:InfoPagoDet')->getSumPorVariosId($arrayIdsDetallesPago);
            }
            else
            {
                $objInfoPagoAutomaticoDet = $this->emFinanciero->getRepository('schemaBundle:InfoPagoAutomaticoDet')->find($intIdPagoAutDet);
                $floatValorDeposito       = $objInfoPagoAutomaticoDet->getMonto();
                foreach($arrayIdsPagosDepositar as $intIdPago):
                    $arrayDetallesPago  = $this->emFinanciero->getRepository('schemaBundle:InfoPagoDet')->findByPagoId($intIdPago);
                    foreach($arrayDetallesPago as $objDetallePago):
                        $arrayIdsDetallesPago[] = $objDetallePago->getId();
                    endforeach;
                endforeach;
            }            
            
            
            if( floatval($floatValorDeposito) > 0 )
            {
                //GRABA DEPOSITO
                $objInfoDeposito = new InfoDeposito();
                $objInfoDeposito->setValor($floatValorDeposito);
                $objInfoDeposito->setFeCreacion(new \DateTime('now'));
                $objInfoDeposito->setUsrCreacion($strUsrCreacion);
                $objInfoDeposito->setCuentaContableId($intIdCtaContable);
                $objInfoDeposito->setEmpresaId($strEmpresaCod);      
                $objInfoDeposito->setOficinaId($intOficinaId);
                $objInfoDeposito->setNoCuentaBancoNaf($strNoCta);
                $objInfoDeposito->setNoCuentaContableNaf($strCtaContable);
                $objInfoDeposito->setEstado($strEstado);
                $this->emFinanciero->persist($objInfoDeposito);
                //GRABA DEPOSITO HISTORIAL
                $objInfoDepositoHistorial = new InfoDepositoHistorial();
                $objInfoDepositoHistorial->setIpCreacion($strIpCreacion);
                $objInfoDepositoHistorial->setFeCreacion(new \DateTime('now'));
                $objInfoDepositoHistorial->setDepositoId($objInfoDeposito);
                $objInfoDepositoHistorial->setUsrCreacion($strUsrCreacion);                
                $objInfoDepositoHistorial->setEstado($strEstado);
                $objInfoDepositoHistorial->setObservacion('Se crea deposito');   
                $this->emFinanciero->persist($objInfoDepositoHistorial);
                               
                
                $floatTotalDetalles = 0;
                
                foreach($arrayIdsDetallesPago as $intIdDetallePago):
                    //MARCA EL PAGO COMO DEPOSITADO
                    $objDetallePago    = $this->emFinanciero->getRepository('schemaBundle:InfoPagoDet')->find($intIdDetallePago);
                
                    if(!is_object($objDetallePago)) 
                    {
                        throw new \Exception("No se puede generar depósito porque no se encuentra el detalle seleccionado");
                    }
                    
                    $intIdDepositoPago = $objDetallePago->getDepositoPagoId() ? $objDetallePago->getDepositoPagoId() : 0;
                    $strDepositado     = $objDetallePago->getDepositado() ? $objDetallePago->getDepositado() : '';
                    
                    if( empty($intIdDepositoPago) && ( empty($strDepositado) || $strDepositado == 'N' ))
                    {
                        $intIdDeposito = $objInfoDeposito->getId();
                        $objDetallePago->setDepositoPagoId($objInfoDeposito->getId());
                        $objDetallePago->setDepositado('S');
                        $this->emFinanciero->persist($objDetallePago);

                        $floatValorDetalle = $objDetallePago->getValorPago();
                        
                        if( !empty($floatValorDetalle) && floatval($floatValorDetalle) > 0 )
                        {
                            $floatTotalDetalles += floatval($floatValorDetalle);
                        }
                    }

                endforeach;
                
                //Función bbcomp usada para obtener la diferencia entre dos valores.
                if( bccomp($floatTotalDetalles, $floatValorDeposito, 2)  == 0  )
                {
                    $this->emFinanciero->flush();
                    $this->emFinanciero->getConnection()->commit();
                    $boolStatus  = true;                  
                }
                else
                {
                    throw new \Exception("El valor total (".$floatValorDeposito.") del depósito a crear no corresponde a la suma total (".
                                          $floatTotalDetalles.") de los detalles seleccionados.");
                }
            }
            else
            {
                throw new \Exception("No se puede generar depósito con valor cero.");
            }
            
        } 
        catch (\Exception $e) 
        {
            $this->emFinanciero->getConnection()->rollback();
            $this->emFinanciero->getConnection()->close();
            $this->serviceUtil->insertError('TELCOS+',
                                            'InfoPagoAutomaticoService->generarDeposito',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            $boolStatus  = false;
        }
        error_log('ID DEPOSITO '.$intIdDeposito);
        $arrayRespuesta = array('boolStatus' => $boolStatus, 'intIdDeposito' => $intIdDeposito);
        return $arrayRespuesta;
    }

     /**
     * procesarDeposito. Función que procesa el depósito del pago enviado como parámetro.
     * 
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.0 30-05-2022
     * @param array arrayParametros[]
     */     
    public function procesarDeposito($arrayParametros) 
    {
        $strEmpresaCod     = $arrayParametros['strEmpresaCod'];
        $intOficinaId      = $arrayParametros['intOficinaId'];
        $strIpCreacion     = $arrayParametros['strIpCreacion'];        
        $strUsrCreacion    = $arrayParametros['strUsrCreacion'];
        $intIdDeposito     = $arrayParametros['intIdDeposito'];
        $strFechaProcesa   = $arrayParametros['strFechaProcesa'];
        $strReferencia     = $arrayParametros['strReferencia'];
        $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'];
        $strEstado         = $arrayParametros['strEstado'];

        $this->emFinanciero->getConnection()->beginTransaction();
        try 
        {
            $entityDeposito = $this->emFinanciero->getRepository('schemaBundle:InfoDeposito')->Find($intIdDeposito);
            $entityDeposito->setFeProcesado(new \DateTime($strFechaProcesa));
            $entityDeposito->setUsrProcesa($strUsrCreacion);
            $entityDeposito->setNoComprobanteDeposito($strReferencia);
            $entityDeposito->setEstado($strEstado);
            $this->emFinanciero->persist($entityDeposito);
            $this->emFinanciero->flush();

            //GRABA DEPOSITO HISTORIAL
            $entityHistorial = new InfoDepositoHistorial();
            $entityHistorial->setIpCreacion($strIpCreacion);
            $entityHistorial->setFeCreacion(new \DateTime('now'));
            $entityHistorial->setDepositoId($entityDeposito);
            $entityHistorial->setUsrCreacion($strUsrCreacion);                
            $entityHistorial->setEstado($strEstado);
            $entityHistorial->setObservacion('Se procesa deposito');            
            $this->emFinanciero->persist($entityHistorial);
            $this->emFinanciero->flush();
            
            //CONTABILIZA DETALLES DE PAGO
            $arrayParametroDet= $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("PROCESO CONTABILIZACION EMPRESA", "FINANCIERO", "", "", $strPrefijoEmpresa, "", "", "");            
            //VERIFICA SI ESTA HABILITADA CONTABILIZACION EN LA EMPRESA
            if ($arrayParametroDet["valor2"]=="S")
            {
                $arrayDepositosIdContabilidad                  = [];
                $objParametros['serviceUtil']                  = $this->serviceUtil;
                $arrayDepositosIdContabilidad[0]['idDeposito'] = $entityDeposito->getId();
                $arrayDepositosIdContabilidad[0]['idOficina']  = $intOficinaId;
                $strMsnErrorContabilidad = $this->emFinanciero->getRepository('schemaBundle:InfoDeposito')
                    ->contabilizarDeposito($strEmpresaCod, $arrayDepositosIdContabilidad, $objParametros);                  
                //GRABA DEPOSITO HISTORIAL
                $objHistorialDepContab = new InfoDepositoHistorial();
                $objHistorialDepContab->setIpCreacion($strIpCreacion);
                $objHistorialDepContab->setFeCreacion(new \DateTime('now'));
                $objHistorialDepContab->setDepositoId($entityDeposito);
                $objHistorialDepContab->setObservacion($strMsnErrorContabilidad);
                $objHistorialDepContab->setUsrCreacion($strUsrCreacion);
                $objHistorialDepContab->setEstado($entityDeposito->getEstado());
                $this->emFinanciero->persist($objHistorialDepContab);
                $this->emFinanciero->flush();
            } 

            $this->emFinanciero->getConnection()->commit();
            return true;            
        } 
        catch (\Exception $e) 
        {
            $this->emFinanciero->getConnection()->rollback();
            $this->emFinanciero->getConnection()->close();
            $this->serviceUtil->insertError('TELCOS+',
                                            'InfoPagoService->procesarDeposito',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);            
            return false;          
        }
    }
    
    /**
     * notificaPagoAutomatico
     *     
     * Método que realiza el envio de un mail según los datos enviados como parámetros.
     *
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 03-06-2022
     * @param   $arrayParametros
     * @return  $strRespuesta
     *
     */
    public function notificaPagoAutomatico($arrayParametros)
    {
        $strModulo              = $arrayParametros['strModulo'];
        $strCodigoPlantilla     = $arrayParametros['strCodigoPlantilla'];
        $strMensaje             = $arrayParametros['strMensaje'];
        $strParametro           = $arrayParametros['strNombreParametro'];
        $strParametroDet        = $arrayParametros['strParametroDet'];
        $strNombreCliente       = $arrayParametros['strNombreCliente'];
        $strSaldoCliente        = $arrayParametros['strSaldoCliente'];
        $boolNotManual          = $arrayParametros['boolNotManual'];
        $intIdPersonaEmpresaRol = $arrayParametros['idPersonaEmpresaRol'];
        
        $strMimeType            = 'text/html; charset=UTF-8';
        $strMsjError            = str_pad(' ', 100);
        $strEmails              = str_pad(' ', 500);
        $strMessage             = str_pad(' ', 100);
        $strRespuesta           = 'OK';
        $intOficina             =$arrayParametros['intOficinaId'];
        $strCodEmpresa          =$arrayParametros['strCodEmpresa'];

        $arrayTokenCas   = $this->serviceTokenCas->generarTokenCas();
        $arrayLogin = array();
        try
        {

            $arrayParamInfoContacto = [];              
            $arrayParamInfoContacto['strEmpresaCod']          = $arrayParametros['strCodEmpresa'];
            $arrayParamInfoContacto['intIdPersonaEmpresaRol'] = $arrayParametros['intIdPersonaEmpresaRol'];
            $arrayParamInfoContacto['strEstado']              = "Activo";
            $arrayParamInfoContacto['strDescFormaContacto']   = "Correo Electronico";
            $arrayParamInfoContacto['strTipoContacto']        = "Contacto Facturacion";
            // Se obtienen contactos de facturación del cliente.
            $arrayContactosFact = $this->emComercial->getRepository("schemaBundle:InfoPersonaContacto")
                                                    ->getContactosClientePorTipoRol($arrayParamInfoContacto);


            foreach($arrayContactosFact as $arrayInfoContacto)
            {
                $strEmails .= trim($arrayInfoContacto['valor']).';';
            }
            
            // Se obtienen contactos de cobranzas del cliente
            $arrayParamInfoContacto['strTipoContacto'] = "Contacto Cobranzas";

            $arrayContactosCob = $this->emComercial->getRepository("schemaBundle:InfoPersonaContacto")
                                                   ->getContactosClientePorTipoRol($arrayParamInfoContacto);


            foreach($arrayContactosCob as $arrayInfoContacto)
            {
                $strEmails .= trim($arrayInfoContacto['valor']).';';
            }

            $objPlantilla = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                 ->findOneBy(array('estado'  => array('Activo','Modificado'),
                                                                   'codigo'  => $strCodigoPlantilla));           
            // Correo de ejecutivo de cobranza
            $objParamDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->findOneBy(array('valor1'     => $intOficina,
                                                             'descripcion'=> 'OFICINAS FACTURACION',
                                                             'empresaCod' => $strCodEmpresa));
            if(is_object($objParamDet))
            {
                $strCorreoCobranza = $objParamDet->getValor2();
                $strTelefonoCobranza = $objParamDet->getValor3();
                $strBaseCobranza = $objParamDet->getValor4();


                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->find($intIdPersonaEmpresaRol);

                if(is_object($objInfoPersonaEmpresaRol))
                {
                    if($objParamDet->getValor5()=="Alias")
                    {
                        $strEmails .= trim($strCorreoCobranza).';';                           
                    }
                    else
                    {
                        $arrayLogin['empleadosLogin'] = 
                                        array(['login'=>$objInfoPersonaEmpresaRol
                                        ->getPersonaId()->getLogin()]); 

                        $objOptions   = array(CURLOPT_SSL_VERIFYPEER => false,   
                                                  CURLOPT_HTTPHEADER     => array('Content-Type: appliction/json',
                                                                                  'tokencas:'.$arrayTokenCas['strToken']));
                            
                                                                                  
                        $strJsonData        = json_encode($arrayLogin);
                        $arrayResponseJson  = $this->serviceRestClient->postJSON($this->serviceEmpleadoListar,$strJsonData, $objOptions);
                        if($arrayResponseJson['status']==200)
                        {
                            $arrayResponse = json_decode($arrayResponseJson['result']);
                            $strEmails .= $arrayResponse->data[0]->mailCia.';';
                        }
                        else
                        {
                            $arrayResponse = json_decode($arrayResponseJson['result']);
                            error_log('no se encontro conexion con ms ws_ms_emplados_listar'.$arrayResponse->message);
                            $this->utilService->insertError('Telcos+', 
                                            'InfoPagoAutomaticoService.notificaPagoAutomatico    ', 
                                            $arrayResponse->message, 
                                            'telcos', 
                                            $arrayParametros['strClientIp']);            
                        }
                        
                    }
                }

            }

            if(is_object($objPlantilla))
            {
                $strPlantilla = $objPlantilla->getPlantilla();
                $arrayAliasPlantilla   = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                              ->getAliasXPlantilla($objPlantilla->getId(), 
                                                                                   $arrayParametros['strCodEmpresa'],"","","NO");
                if(isset($arrayAliasPlantilla) && !empty($arrayAliasPlantilla))
                {
                    foreach($arrayAliasPlantilla as $strAliasPlantilla)
                    {
                        $strEmails .= trim($strAliasPlantilla).';';
                    }
                }
                
                $strMessage   = str_replace('strMessage',$strMensaje,$strPlantilla);
                $strMessage   = str_replace('strNombreCliente',$strNombreCliente,$strMessage);
                $strMessage   = str_replace('strSaldoCliente',$strSaldoCliente,$strMessage);
                $strMessage   = str_replace('strCorreoCobranza',$strCorreoCobranza,$strMessage);
                $strMessage   = str_replace('strTelefonoCobranza',$strTelefonoCobranza,$strMessage);
                $strMessage   = str_replace('strBaseCobranza',$strBaseCobranza,$strMessage);

                if($boolNotManual)
                {
                    $strMessage   = str_replace('strTablaPagos',$arrayParametros['strTablaPagos'],$strMessage);
                    $strMessage   = str_replace('strValorTotal',strval($arrayParametros['floatValorTotal']),$strMessage);                    
                    $strMessage   = str_replace('strCorreoCobranza',$strCorreoCobranza,$strMessage);
                    $strMessage   = str_replace('strTelefonoCobranza',$strTelefonoCobranza,$strMessage);
                    $strMessage   = str_replace('strBaseCobranza',$strBaseCobranza,$strMessage);
                    
                }
                else 
                {
                    
                    $arrayParamDetFormaPag  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                   ->getOne($strParametro,
                                                            $strModulo, 
                                                            '', 'FORMA PAGO', $arrayParametros['strFormaPago'], 
                                                            strval($arrayParametros['intIdFormaPago']), 
                                                            '','', '',$arrayParametros['strCodEmpresa']); 
                    
                    
                    if(isset($arrayParamDetFormaPag['valor5']))
                    {
                        $strMessage   = str_replace('strFormaPago',$arrayParamDetFormaPag['valor5'],$strMessage);
                    }
                    else
                    {
                        $strMessage   = str_replace('strFormaPago',$arrayParametros['strFormaPago'],$strMessage);
                    }
    
                    $strMessage   = str_replace('strValorPago',$arrayParametros['strValorPago'],$strMessage);
                }        
                
                $arrayParametrosDet     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne($strParametro,
                                                                   $strModulo, 
                                                                   '', '', $strParametroDet, '', '', '', '', 
                                                                   $arrayParametros['strCodEmpresa']);
                
                if (isset($arrayParametrosDet['valor2']) && isset($arrayParametrosDet['valor3']))
                {
                    $strRemitente    = $arrayParametrosDet['valor2'];
                    
                    $strSubject      = $arrayParametrosDet['valor3'];
                                        
                    $strSqlEnvioMail = "BEGIN DB_FINANCIERO.FNCK_CONSULTS."
                                     . "P_SEND_MAIL(:Pv_From,:Pv_To,:Pv_Subject,:Pv_Message,:Pv_MimeType,:Pv_MsnError);END;";
                    $objStmtEnvioMail = $this->emFinanciero->getConnection()->prepare($strSqlEnvioMail);
                    $objStmtEnvioMail->bindParam('Pv_From' , $strRemitente);
                    $objStmtEnvioMail->bindParam('Pv_To' , $strEmails);
                    $objStmtEnvioMail->bindParam('Pv_Subject' , $strSubject);
                    $objStmtEnvioMail->bindParam('Pv_Message' , $strMessage);
                    $objStmtEnvioMail->bindParam('Pv_MimeType' , $strMimeType);    
                    $objStmtEnvioMail->bindParam('Pv_MsnError' , $strMsjError);            
                    $objStmtEnvioMail->execute();                
                }                
            }
        } 
        catch (Exception $ex) 
        {
            $strRespuesta = 'ERROR';

            $this->utilService->insertError('Telcos+', 
                                            'InfoPagoAutomaticoService.notificaPagoAutomatico', 
                                            $ex->getMessage(), 
                                            'telcos', 
                                            $arrayParametros['strClientIp']);            
        }
        return $strRespuesta;
    }    
    
    /**
     * notificaClienteInCorte
     *     
     * Método que realiza el envio de notificación de servicios Incorte de los puntos del cliente enviado como parámetro.
     *
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 16-06-2022
     * @param   $arrayParametros
     * @return  $strRespuesta
     *
     * @author  Kevin Villegas <kmvillegas@telconet.ec>
     * @version 1.1 29-08-2022 Se agrega el envío de la notificacion al alias de cobranzas según la oficina de facturación.
     */
    public function notificaClienteInCorte($arrayParametros)
    {      
        $strModulo               = $arrayParametros['strModulo'];
        $strCodigoPlantilla      = $arrayParametros['strCodigoPlantilla'];
        $strMensaje              = $arrayParametros['strMensaje'];
        $strParametro            = $arrayParametros['strNombreParametro'];
        $strParametroDet         = $arrayParametros['strParametroDet'];
        $strNombreCliente        = $arrayParametros['strNombreCliente'];
        $arrayServiciosInCorte   = $arrayParametros['arrayServiciosInCorte'];
        $strCodEmpresa           = $arrayParametros['strCodEmpresa'];
        $intOficina              = $arrayParametros['intOficinaId'];
        $strMimeType             = 'text/html; charset=UTF-8';
        $strTablaInfoPtoServicio = '';
        $strMsjError             = str_pad(' ', 100);
        $strEmails               = str_pad(' ', 500);
        $strMessage              = str_pad(' ', 100);
        $strRespuesta            = 'OK';
               
        try
        {
            foreach($arrayServiciosInCorte as $objServInCorte)
            {
                 $strTablaInfoPtoServicio .= '<tr><td>'.$objServInCorte->getPuntoId()->getLogin().'</td><td>'.
                                                        $objServInCorte->getProductoId()->getDescripcionProducto().'</td></tr>';
            }

            $objPlantilla = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                 ->findOneBy(array('estado'  => array('Activo','Modificado'),
                                                                   'codigo'  => $strCodigoPlantilla));           
            
            // se obtiene el parametro de la oficina de facturacion                
            $arrayParamDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array('valor1'     => $intOficina,
                                                           'descripcion'=> 'OFICINAS FACTURACION',
                                                           'empresaCod' => $strCodEmpresa));
                                                                       

            if(isset($arrayParamDet) && !empty($arrayParamDet))
            {
                $strCorreoCobranza =$arrayParamDet->getValor2();
                $strEmails       .= trim($strCorreoCobranza).';';
            }

            if(is_object($objPlantilla))
            {
                $strPlantilla          = $objPlantilla->getPlantilla();
                $arrayAliasPlantilla   = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                                              ->getAliasXPlantilla($objPlantilla->getId(), 
                                                                                   $strCodEmpresa,"","","NO");

                if(isset($arrayAliasPlantilla) && !empty($arrayAliasPlantilla))
                {
                    foreach($arrayAliasPlantilla as $strAliasPlantilla)
                    {
                        $strEmails .= trim($strAliasPlantilla).';';
                    }
                }
                $strMessage   = str_replace('strMessage',$strMensaje,$strPlantilla);
                $strMessage   = str_replace('strNombreCliente',$strNombreCliente,$strMessage);
                $strMessage   = str_replace('strTablaInfoPtoServicio',$strTablaInfoPtoServicio,$strMessage);
                $strMessage   = str_replace('strCorreoCobranza',$strCorreoCobranza,$strMessage);

                $arrayParametrosDet     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne($strParametro,
                                                                   $strModulo, 
                                                                   '', '', $strParametroDet, '', '', '', '', 
                                                                   $arrayParametros['strCodEmpresa']);

                if (isset($arrayParametrosDet['valor2']) && isset($arrayParametrosDet['valor3']))
                {
                    $strRemitente    = $arrayParametrosDet['valor2'];                    
                    $strSubject      = $arrayParametrosDet['valor3'];
                    $strSqlEnvioMail = "BEGIN DB_FINANCIERO.FNCK_CONSULTS."
                                     . "P_SEND_MAIL(:Pv_From,:Pv_To,:Pv_Subject,:Pv_Message,:Pv_MimeType,:Pv_MsnError);END;";
                    $objStmtEnvioMail = $this->emFinanciero->getConnection()->prepare($strSqlEnvioMail);
                    $objStmtEnvioMail->bindParam('Pv_From' , $strRemitente);
                    $objStmtEnvioMail->bindParam('Pv_To' , $strEmails);
                    $objStmtEnvioMail->bindParam('Pv_Subject' , $strSubject);
                    $objStmtEnvioMail->bindParam('Pv_Message' , $strMessage);
                    $objStmtEnvioMail->bindParam('Pv_MimeType' , $strMimeType);    
                    $objStmtEnvioMail->bindParam('Pv_MsnError' , $strMsjError);            
                    $objStmtEnvioMail->execute();             
                }                
            }
        } 
        catch (Exception $ex) 
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoPagoAutomaticoService.notificaClienteInCorte', 
                                            $ex->getMessage(), 
                                            'telcos', 
                                            $arrayParametros['strClientIp']);            
        }
        return $strRespuesta;
    }    
}
