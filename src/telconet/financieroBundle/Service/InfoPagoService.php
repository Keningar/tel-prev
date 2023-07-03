<?php

namespace telconet\financieroBundle\Service;

use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\InfoRecaudacion;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoPagoLineaHistorial;
use telconet\schemaBundle\Repository\InfoOficinaGrupoRepository;
use telconet\schemaBundle\Repository\AdmiTipoDocumentoFinancieroRepository;
use telconet\schemaBundle\Entity\AdmiFormaPago;
use telconet\schemaBundle\Entity\AdmiBancoTipoCuenta;
use telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\AdmiCanalPagoLinea;
use telconet\schemaBundle\Entity\InfoPagoLinea;
use telconet\schemaBundle\Entity\InfoPagoHistorial;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoCab;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoDet;

class InfoPagoService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emNaf;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;    
    
    private $session;

    private $arrayPagos;        
    
    private $serviceUtil;
    private $serviceEnvioPlantilla;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom     = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emNaf     = $container->get('doctrine.orm.telconet_naf_entity_manager');
        $this->emfinan   = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emGeneral = $container->get('doctrine.orm.telconet_general_entity_manager');  
        $this->session   = $container->get('session');
        $this->arrayPagos = array();
        $this->serviceUtil= $container->get('schema.Util');     
        $this->serviceEnvioPlantilla = $container->get('soporte.EnvioPlantilla');   
    }

    /**
    * Documentación para el método 'inactivaPaLRegularizaDocsFinancieros'
    * Lista los pagos generados por un pago en linea, envia a inactivar estos pagos
    * y regulariza los documentos financieros asociados a estos pagos.
    *
    * @author  Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 26/02/2019
    * 
    */      
    public function inactivaPaLRegularizaDocsFinancieros($arrayRequest)
    {
        ini_set('max_execution_time', 60);
        error_log("[PagoLinea] inactivaPaLRegularizaDocsFinancieros INI");
        $arrayResponse = ['strMensaje'   => '', 
                          'strCodigo'    => '006', 
                          'boolResponse' => true];
        $strCodEmpresa = $arrayRequest['request']['strCodEmpresa'];
        try
        {
            error_log("[PagoLinea] inactivaPaLRegularizaDocsFinancieros Itera pago CAB de pagoLinea: " . $arrayRequest['pagoLinea']->getId());
            //se debe aniador a flat para identificar el pago en linea y pago normal
            $arrayInfoPagoCab = $this->emfinan->getRepository ( 'schemaBundle:InfoPagoCab' )
                                     ->findBy(['pagoLinea' => $arrayRequest['pagoLinea']->getId(),
                                               'usrCreacion' => 'telcos_pal']);
            foreach($arrayInfoPagoCab as $objInfoPagoCab):
                $arrayResponse = $this->regularizaDocsByInactivacionPagoCab(['objPagoCab' => $objInfoPagoCab]);
                if('000' != $arrayResponse['strCodigo'])
                {
                    return $arrayResponse;
                }
            endforeach;
            $arrayContPagos = $this->arrayPagos;
            $strIdPagDet = implode(',', $arrayContPagos);
            error_log("[PagoLinea] inactivaPaLRegularizaDocsFinancieros pagosDet para CONT " . json_encode($arrayContPagos) . ' ' . $strIdPagDet);
            $arrayResCont  = $this->emfinan->getRepository ( 'schemaBundle:MigraDocumentoAsociado' )
                                           ->reversaContPAL(['strIdPagoDet'   => $strIdPagDet,
                                                             'strCodEmpresa'  => $strCodEmpresa,
                                                             'strUsrCreacion' => 'telcos_pal']);
            error_log("[PagoLinea] inactivaPaLRegularizaDocsFinancieros " . json_encode($arrayResCont));
            if($arrayResCont['strCode'] != '100')
            {
                $arrayResponse = ['strMensaje'   => 'No se pudo reversar la contabilidad del pago', 
                                  'strCodigo'    => '003', 
                                  'boolResponse' => true];
                error_log("[PagoLinea] inactivaPaLRegularizaDocsFinancieros " . json_encode($arrayResponse));
                return $arrayResponse;
            }
            $arrayResponse = ['strMensaje'   => 'Pagos anulados correctamente', 
                              'strCodigo'    => '000', 
                              'boolResponse' => false];
        }
        catch(\Exception $ex)
        {
            $arrayResponse = ['strMensaje'   => 'Existio un error: ' . $ex->getMessage(), 
                              'strCodigo'    => '003', 
                              'boolResponse' => true];
            error_log("[PagoLinea] Error inactivaPaLRegularizaDocsFinancieros " . json_encode($arrayResponse));
        }
        error_log("[PagoLinea] inactivaPaLRegularizaDocsFinancieros FIN");
        return $arrayResponse;
    }

    /**
    * Documentación para el método 'regularizaDocsByInactivacionPagoCab'
    * Regulariza un documento que este relacionado al pago anulado.
    *
    * @author  Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 26/02/2019
    * 
    * Se agrega fecha de última modificación en la InfoPagoCab y InfoPagoDet, cuando se anulan los pagos. 
    * Adicional se crea historial y se actualizan los detalles del pago a Anulado, cuando el estado inicial de la cabecera del pago es Asignado. 
    * 
    * @author  Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 27/11/2019   
    */   
    private function regularizaDocsByInactivacionPagoCab($arrayRequest)
    {
        ini_set('max_execution_time', 60);
        $intIdPagoCab = $arrayRequest['objPagoCab']->getId();
        $strEstadoPagoCab = $arrayRequest['objPagoCab']->getEstadoPago();
        error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab Estado: $strEstadoPagoCab INI");
        $arrayResponse = ['strMensaje'   => '', 
                          'strCodigo'    => '006', 
                          'boolResponse' => true];
        if("Asignado" === $strEstadoPagoCab)
        {
            error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, pago $strEstadoPagoCab no se puede reversar");
            $arrayResponse = ['strMensaje'   => 'No se puede realizar el reverso del pago, porque tiene un detalle Asignado', 
                              'strCodigo'    => '010', 
                              'boolResponse' => true];
            return $arrayResponse;
        }
        try
        {
            switch ($strEstadoPagoCab) 
            {
                case "Cerrado":
                case "Pendiente":
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, anula pago");
                    $arrayRequest['objPagoCab']->setEstadoPago('Anulado');
                    $arrayRequest['objPagoCab']->setFeUltMod(new \DateTime ( 'now' ));
                    $this->emfinan->persist ( $arrayRequest['objPagoCab'] );
                    $this->emfinan->flush ();
                    $this->ingresaHistorialPago($arrayRequest['objPagoCab'], 
                                                'Anulado', 
                                                new \DateTime ( 'now' ), 
                                                'telcos_pal', 
                                                null, 
                                                'Reverso de pago en linea');
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, busca detalles PAGO_DET");
                    //Busca los detalles de ese pago
                    $arrayInfoPagoDet = $this->emfinan->getRepository ( 'schemaBundle:InfoPagoDet' )
                                                ->findBy(array('pagoId' => $intIdPagoCab));
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, itera los detalles");
                    //Itera los detalles del pago
                    foreach($arrayInfoPagoDet as $objItemInfoPagoDet):
                        //Obtiene la referencia para anular la factura
                        $intReferenciaId = $objItemInfoPagoDet->getReferenciaId();
                        error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab," .
                                  " referenciaID: " . $intReferenciaId . " ID_DET: " . $objItemInfoPagoDet->getId());
                        if(!empty($intReferenciaId))
                        {
                            error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab envia anular IDFC REF_ID: $intReferenciaId");
                            //Busca la factura y la anula
                            $this->cambiaEstadoDocumento(['intIdDocumento' => $intReferenciaId, 'intIdPagoCab' => $intIdPagoCab]);
                        }
                        //Busca NDI por pagoDetId en InfoDocumentoFinancieroDet
                        $objDocuemntoFinancierDet = $this->emfinan->getRepository ( 'schemaBundle:InfoDocumentoFinancieroDet' )
                                                         ->findOneBy(array('pagoDetId' => $objItemInfoPagoDet->getId()));
                        //Si existe documento usa la misma funcion
                        if(is_object($objDocuemntoFinancierDet))
                        {
                            $intIdDocumento = $objDocuemntoFinancierDet->getDocumentoId()->getId();
                            error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab , envia anular ND - IDFC_ID: $intIdDocumento");
                            //Anula la NDI
                            $this->cambiaEstadoDocumento(['intIdDocumento' => $intIdDocumento, 'intIdPagoCab' => $intIdPagoCab]);
                            $arrayInfoPagoDet = $this->emfinan->getRepository ( 'schemaBundle:InfoPagoDet' )
                                                     ->findBy(array('referenciaId' => $intIdDocumento));
                            error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab , verifica si el DOC: $intIdDocumento esta como referencia en un PAGO_DET");
                            foreach($arrayInfoPagoDet as $objInfoPagoDet):
                                $this->regularizaDocsByInactivacionPagoCab(['objPagoCab' => $objInfoPagoDet->getPagoId()]);
                            endforeach;
                        }
                        $objItemInfoPagoDet->setEstado('Anulado');
                        $objItemInfoPagoDet->setFeUltMod(new \DateTime ( 'now' ));
                        $this->emfinan->persist ( $objItemInfoPagoDet );
                        $this->emfinan->flush ();
                        $this->arrayPagos[$objItemInfoPagoDet->getId()] = $objItemInfoPagoDet->getId();
                    endforeach;
                    break;
                case "Asignado":
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, anula anticipo");
                    $arrayRequest['objPagoCab']->setEstadoPago('Anulado');
                    $arrayRequest['objPagoCab']->setFeUltMod(new \DateTime ( 'now' ));
                    $this->emfinan->persist ( $arrayRequest['objPagoCab'] );
                    $this->emfinan->flush ();
                    $arrayInfoPagoCab = $this->emfinan->getRepository ( 'schemaBundle:InfoPagoCab' )
                                             ->findBy(['anticipoId' => $intIdPagoCab]);
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, itera los pagos del anticipo INI");
                    foreach($arrayInfoPagoCab as $objInfoPagoCab):
                        $this->regularizaDocsByInactivacionPagoCab(['objPagoCab' => $objInfoPagoCab]);
                    endforeach;
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, itera los pagos del anticipo FIN");
                    
                    //Inserta historial de InfoPagoCab.
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, inserta historial de cabecera PagoCAB");
                    $this->ingresaHistorialPago($arrayRequest['objPagoCab'], 
                                                'Anulado', 
                                                new \DateTime ( 'now' ), 
                                                'telcos_pal', 
                                                null, 
                                                'Reverso de pago en linea');
                    //Busca los detalles de ese pago.
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, busca detalles PAGO_DET");
                    $arrayInfoPagoDetalle = $this->emfinan->getRepository ( 'schemaBundle:InfoPagoDet' )
                                                 ->findBy(array('pagoId' => $arrayRequest['objPagoCab']->getId()));
                    
                    //Itera los detalles del pago y anula.
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, itera detalles de cabecera PagoCAB INI" );
                    foreach($arrayInfoPagoDetalle as $objItemInfoPagoDetalle):
                        $objItemInfoPagoDetalle->setEstado('Anulado');
                        $objItemInfoPagoDetalle->setFeUltMod(new \DateTime ( 'now' ));
                        $this->emfinan->persist ( $objItemInfoPagoDetalle );
                        $this->emfinan->flush ();
                    endforeach;
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab, itera detalles de cabecera PagoCAB FIN" );
                    
                    break;
                default:
                    error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab, default no existe el estado " . $strEstadoPagoCab);
            }
            $arrayResponse = ['strMensaje'   => 'Pagos anulados', 
                              'strCodigo'    => '000', 
                              'boolResponse' => false];
        }
        catch(\Exception $ex)
        {
            error_log("[PagoLinea] Error regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab : " . $ex->getMessage());
            $arrayResponse = ['strMensaje'   => 'Existio un error: ' . $ex->getMessage(), 
                              'strCodigo'    => '003', 
                              'boolResponse' => true];
        }
        error_log("[PagoLinea] regularizaDocsByInactivacionPagoCab PagoCAB ID: $intIdPagoCab Estado: $strEstadoPagoCab FIN");
        return $arrayResponse;
    }

    /**
    * Documentación para el método 'cambiaEstadoDocumento'
    * Cambia el estado del documento financiero
    *
    * @author  Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 26/02/2019
    *
    * @author  Alex Arreaga <atarreaga@telconet.ec>
    * @version 1.1 05/01/2021 - Se agrega validación en el caso de que el documento (NDI) posea la característica de proceso diferido, 
    *                           se cambie a estado Activo el documento.  
    * 
    */   
    private function cambiaEstadoDocumento($arrayRequest)
    {
        ini_set('max_execution_time', 60);
        $boolInsertaHistorial = false;
        $strEstadoDocCambio   = 'Activo';
        $objDocuemntoFinancierCab = $this->emfinan->getRepository ( 'schemaBundle:InfoDocumentoFinancieroCab' )
                                         ->find($arrayRequest['intIdDocumento']);
        if(is_object($objDocuemntoFinancierCab))
        { 
            $strEstadoDoc = $objDocuemntoFinancierCab->getEstadoImpresionFact();
            $objAdmiTipoDocuementoFinan = $objDocuemntoFinancierCab->getTipoDocumentoId();
            if(is_object($objAdmiTipoDocuementoFinan))
            {
                $strCodigoTipoDoc = $objAdmiTipoDocuementoFinan->getCodigoTipoDocumento();
                error_log("[PagoLinea] --> cambiaEstadoDocumento PagoCAB ID: " . $arrayRequest['intIdPagoCab'] . 
                          " INI ID_DOC: " . $arrayRequest['intIdDocumento'] . " TIPO_DOC: $strCodigoTipoDoc");
                switch($strCodigoTipoDoc)
                {
                    case 'FACP':
                    case 'FAC':
                        if("Cerrado" === $strEstadoDoc)
                        {
                            $objDocuemntoFinancierCab->setEstadoImpresionFact($strEstadoDocCambio);
                        }
                        $boolInsertaHistorial = true;
                        break;
                    case 'NDI':
                    case 'ND':
                        //Se obtiene la característica 'PROCESO_DIFERIDO' para validar si el documento tiene característica de diferido.
                        $objCaractDiferido = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                         ->findOneBy( array('descripcionCaracteristica' => 'PROCESO_DIFERIDO') );
                        
                        $intCaractDiferido = is_object($objCaractDiferido) ? $objCaractDiferido->getId() : null;
                        
                        $objDocumentoCaract = $this->emfinan->getRepository('schemaBundle:InfoDocumentoCaracteristica')
                                                            ->findOneBy( array('documentoId'      => $objDocuemntoFinancierCab->getId(),
                                                                               'caracteristicaId' => $intCaractDiferido,
                                                                               'estado'           => 'Activo') );
                        
                        //Se valida si el documento posee característica de diferido activa el documento, de lo contrario el documento es anulado.
                        if(is_object($objDocumentoCaract))
                        {
                            $strEstadoDocCambio = 'Activo';
                        }
                        else 
                        {
                            $strEstadoDocCambio = 'Anulado';
                        }
                        
                        if("Activo" === $strEstadoDoc || "Cerrado" === $strEstadoDoc )
                        {
                            $objDocuemntoFinancierCab->setEstadoImpresionFact($strEstadoDocCambio);
                        }
                        $boolInsertaHistorial = true;
                        break;
                    default:
                        error_log("[PagoLinea] --> cambiaEstadoDocumento, default no existe el estado " . $strCodigoTipoDoc . " para el documento " . $arrayRequest['intIdDocumento']);
                }
            }
            try
            {
                if($boolInsertaHistorial)
                {
                    $this->emfinan->persist ( $objDocuemntoFinancierCab );
                    $this->emfinan->flush ();
                    $objInfoHistorialDoc = new InfoDocumentoHistorial();
                    $objInfoHistorialDoc->setDocumentoId ( $objDocuemntoFinancierCab );
                    $objInfoHistorialDoc->setEstado ( $strEstadoDocCambio );
                    $objInfoHistorialDoc->setObservacion ( "Documento actualizado a estado " . $strEstadoDocCambio . " por reverso de pago en linea" );
                    $objInfoHistorialDoc->setFeCreacion ( new \DateTime ( 'now' ) );
                    $objInfoHistorialDoc->setUsrCreacion ( 'telcos_pal' );
                    $this->emfinan->persist ( $objInfoHistorialDoc );
                    $this->emfinan->flush ();
                }
            }
            catch(\Exception $ex)
            {
                error_log("[PagoLinea] Error --> cambiaEstadoDocumento : " . $ex->getMessage());
                throw new \Exception("[PagoLinea] Error --> cambiaEstadoDocumento : " . $ex->getMessage());
            }
        }
        error_log("[PagoLinea] --> cambiaEstadoDocumento PagoCAB ID: " . $arrayRequest['intIdPagoCab'] . 
                  " INI ID_DOC: " . $arrayRequest['intIdDocumento'] . " FIN");
    }



    /**
    * Documentación para el método 'generarPagoAnticipoRecaudacion'
    * Genera pagos o anticipos por recaudacion
    * 
    * @param AdmiFormaPago $entityFormaPago
    * @param string $empresaCod
    * @param integer $oficinaId
    * @param string $identificacionCliente
    * @param string $usrCreacion
    * @param float $valorPagado
    * @param InfoRecaudacion $entityRecaudacion
    * @param integer $idBancoTipoCuenta
    * @param string $numeroReferencia
    * @param string $banco
    * @param \DateTime $fechaProceso
    * @param integer $idBancoCtaContable
    * @return string codigo del tipo documento del pago generado
    * 
    * @version 1.0 Versión inicial
    * 
    * @author  Edgar Holguin <eholguin@telconet.ec>
    * @version 1.2  21-10-2016 Se incluye búsqueda de cliente por campo contrapartida.
    * 
    * @author  Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.3  03-10-2017 Se modifica funcion para que realize la busqueda de la siguiente manera, detallo: 
    *                        - Si la contrapartida SI pertenece a la identificación de un cliente Netlife, se procede a realizar el pago.
    *                        - Si la contrapartida NO pertenece a la identificación de un cliente Netlife, se procede a validar el campo "idCliente".
    *                        - Si el campo "idCliente" SI pertenece a la identificación de un cliente Netlife, se procede a realizar el pago. 
    *                        - Si el campo "idCliente" NO pertenece a la identificación de un cliente Netlife, NO se procede a realizar el pago.
    * 
    * 
    * @author  Edgar Holguin <eholguin@telconet.ec>
    * @version 1.4  11-10-2017 Se agrega validación para verificar el campo contrapartida cuando éste es alfanumérico
    * 
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.5 19-01-2018 Se obtiene de la sesión el idPais. En caso que no exista sesión, por defecto se asigna el idPais de Ecuador.
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.6 07-05-2019 - Se agrega busqueda de intIdPais segun el nombre pais ECUADOR
    *
    * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
    * @version 1.6 10-01-2022 - Se agrega busqueda de codEmpresa al momento de validar la identificación
    */      

    function generarPagoAnticipoRecaudacion($arrayParametros)
    {
        $entityPersona                  = null;
        $strMensaje                     = null;
        $boolClienteEncontrado          = false;
        $arrayInfoPersonaEmpresaRol     = array();
        $strContrapartida               = str_replace(" ","",$arrayParametros['strContrapartidaCliente']);
        $boolAlfanumerico               = false;
                    
        //Por Defecto el NombrePais de ECUADOR
        $strNombrePais = 'ECUADOR';
        $objAdmiPais   = $this->emGeneral->getRepository("schemaBundle:AdmiPais")->findOneBy(array('nombrePais' => $strNombrePais,
                                                                                                    'estado'     => 'ACTIVO'));

        if ( is_object($objAdmiPais) )
        {
            $intIdPais=$objAdmiPais->getId();
          
        }// ( is_object($objAdmiPais) ) 
        
        //1ero, se realiza busqueda de la identificacion del cliente Netlife por la CONTRAPARTIDA
        if ($arrayParametros['strContrapartidaCliente']!="" && !ctype_alpha($strContrapartida))
        {
            if(is_numeric($arrayParametros['strContrapartidaCliente']))
            {
                // Se valida si campo contrapartida tiene formato de identificación válido (CED ó RUC)
                $arrayParamValidaIdentifica = array(
                                                        'strTipoIdentificacion'     => 'CED',
                                                        'strIdentificacionCliente'  => $arrayParametros['strContrapartidaCliente'],
                                                        'intIdPais'                 => $intIdPais,
                                                        'strCodEmpresa'             => $arrayParametros['empresaCod']
                                                   );

                $strMensaje = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                          ->validarIdentificacionTipo($arrayParamValidaIdentifica);

                if($strMensaje != '')
                {
                    $arrayParamValidaIdentifica = array(
                                                            'strTipoIdentificacion'     => 'RUC',
                                                            'strIdentificacionCliente'  => $arrayParametros['strContrapartidaCliente'],
                                                            'intIdPais'                 => $intIdPais,
                                                            'strCodEmpresa'             => $arrayParametros['empresaCod']
                                                    );
                    $strMensaje = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                              ->validarIdentificacionTipo($arrayParamValidaIdentifica);
                }                
            }
            else if(ctype_alnum($strContrapartida))
            {
                $boolAlfanumerico = true;
            }
            else
            {
                $boolAlfanumerico = false;                
            }
            
            if($strMensaje == '' || $boolAlfanumerico)
            {
                $arrayInfoPersonaEmpresaRol=$this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                 ->buscaClientesPorIdentificacionTipoRolEmpresaEstados(
                                                                                                       $arrayParametros['strContrapartidaCliente'],
                                                                                                       array("Cliente","Pre-cliente"),
                                                                                                       $arrayParametros['empresaCod'], 
                                                                                                       array("Activo","Cancelado","Pendiente")); 

                if (count($arrayInfoPersonaEmpresaRol) >= 1)
                {
                    $entityPersona = $arrayInfoPersonaEmpresaRol[0]->getPersonaId();
                    $boolClienteEncontrado = true;
                }
            }            
        }
        
        //En el caso de que no se encuentre la identificacion del cliente Netlife por la CONTRAPARTIDA, se realiza nueva busqueda por IdCliente.
        //Busca persona por identificacion y si encuentra lo graba como cliente
        if  (!$boolClienteEncontrado)
        {
            $arrayInfoPersonaEmpresaRol=$this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                             ->buscaClientesPorIdentificacionTipoRolEmpresaEstados(
                                                                                                   trim($arrayParametros['identificacionCliente']),
                                                                                                   array("Cliente","Pre-cliente"),
                                                                                                   $arrayParametros['empresaCod'], 
                                                                                                   array("Activo","Cancelado","Pendiente"));        
            if (count($arrayInfoPersonaEmpresaRol) >= 1)
            {
                $entityPersona = $arrayInfoPersonaEmpresaRol[0]->getPersonaId();
                $boolClienteEncontrado = true;
            }
        }
        
        if ($boolClienteEncontrado)
        {
            $arrayParametros['entityPersona']= $entityPersona;
        }
        else
        {
            $arrayParametros['entityPersona']= null;
        }
        
        $strRespuesta = $this->generarPagoAnticipoPrv($arrayParametros);
        
        return $strRespuesta;
    }
    /**
     * Genera pagos o anticipos por pago en linea
     * @param InfoPagoLinea $entityPagoLinea
     * @param string $usrCreacion
     * @param \DateTime $fecha
     * @return string codigo del tipo documento del pago generado
     */
    function generarPagoAnticipoPagoLinea(InfoPagoLinea $entityPagoLinea, $usrCreacion, \DateTime $fecha)
    {
        $arrayParametros=array();
        $arrayParametros['entityFormaPago']      = $entityPagoLinea->getCanalPagoLinea()->getFormaPago();
        $arrayParametros['empresaCod']           = $entityPagoLinea->getEmpresaId();
        $arrayParametros['oficinaId']            = $entityPagoLinea->getOficinaId();
        $arrayParametros['entityPersona']        = $entityPagoLinea->getPersona();
        $arrayParametros['usrCreacion']          = $usrCreacion;
        $arrayParametros['valorPagado']          = $entityPagoLinea->getValorPagoLinea();
        $arrayParametros['entityRecaudacion']    = null;
        $arrayParametros['entityRecaudacionDet'] = null;
        $arrayParametros['entityPagoLinea']      = $entityPagoLinea;
        $arrayParametros['bancoTipoCuentaId']    = $entityPagoLinea->getCanalPagoLinea()->getBancoTipoCuentaId();
        $arrayParametros['numeroReferencia']     = $entityPagoLinea->getNumeroReferencia();
        $arrayParametros['origenPago']           = $entityPagoLinea->getCanalPagoLinea()->getDescripcionCanalPagoLinea();
        $arrayParametros['fechaProceso']         = $fecha;
        $arrayParametros['bancoCtaContableId']   = $entityPagoLinea->getCanalPagoLinea()->getBancoCtaContableId();
        $arrayParametros['intIdCanalRecaudacion']= $entityPagoLinea->getCanalPagoLinea()->getId();
        
        return $this->generarPagoAnticipoPrv($arrayParametros);
    }
    
    /**
     * Genera pagos o anticipos por recaudacion o pago en linea
     * 
     * Actualizacion: Se corrige error en pagos en linea, se esta haciendo persist y flush de $entityRecaudacionDet
     * sin verificar el tipo de pago (recaudacion o pago en linea)
     * @version 1.2 22-07-2016 
     * @author amontero@telconet.ec
     * 
     * Actualizacion: Se corrige que los detalles de recaudacion solo se graben como Asignado:S, es_cliente:S y se grabe personaEmpresaRolId
     * Solo si el pago es un PAG o ANT
     * @version 1.1 20-07-2016 
     * @author amontero@telconet.ec
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 13-10-2016 - Se ordena las facturas en orden ascedente de los puntos con deuda, para que cancele con las recaudaciones desde las
     *                           las facturas más antiguas a las más nuevas. Para ello se envía como parámetro el 'strCodEmpresa' a la consulta de
     *                           las facturas para retornar SOLO las facturas pertenecientes a la empresa en sessión y cuando los puntos tengan deuda
     *                           para ello se verifica si la variable $arrayIdPuntoConSaldo está vacía.
     * 
     * @version 1.3 03-03-2017 
     * @author  Edgar Holguín <eholguin@telconet.ec> Se agrega envío de array de parámetros en consulta de pagos existentes, 
     *                                               filtrando por fecha parametrizable.
     * 
     * @version 1.4 25-10-2017 
     * @author  Edgar Holguín <eholguin@telconet.ec> Se da de baja el uso del parámetro 'NUMERO DIAS RECAUDACION EXISTENTE', y se reemplaza por el 
     *                                               de la característica del mismo nombre asociada al canal recaudación respectivo.Se agrega seteo 
     *                                               del campo CUENTA_CONTABLE_ID con el mismo valor del campo BANCO_CTA_CONTABLE_ID del canal 
     *                                               de recaudación.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.5 19-12-2019- Se agregan Logs en INFO_ERROR para monitorear el proceso debido a que los pagos no estan cerrando las Facturas
     *                          con saldo cero. 
     *                          Se modifica para que se obtenga el saldo de la Factura en base a la funcion F_SALDO_X_FACTURA
     *                          Se redondea a dos decimales el saldo de la factura y el valor pagado para corregir el error que se genera al  
     *                          verificar si el saldo ya cubre el valor de la factura, => if ($saldoFactura == $valorPagado), el cual deja las 
     *                          Facturas Abiertas con Saldo Cero.
     * 
     * @author Jose Bedon Sanchez <jobedon@telconet.ec>
     * @version 1.6 13-03-2020 - Se parametriza el comentario que se muestra en telcos en los pagos del cliente
     *                           Se parametriza el usuario que se muestra en telcos en los pagos del cliente
     *                           Para extranet es el usuario telcos_bp
     * 
     * @param AdmiFormaPago $entityFormaPago debe ser Recaudacion (REC) o Pago en Linea (PAL)
     * @param string $empresaCod
     * @param integer $oficinaId
     * @param InfoPersona $entityPersona
     * @param string $usrCreacion nombre del usuario que crea los registros
     * @param float $valorPagado
     * @param InfoRecaudacion $entityRecaudacion (nullable)
     * @param InfoPagoLinea $entityPagoLinea (nullable)
     * @param integer $idBancoTipoCuenta
     * @param string $numeroReferencia
     * @param string $origenPago nombre del banco o del canal de pago en linea
     * @param \DateTime $fechaProceso
     * @param integer $idBancoCtaContable
     * @return string codigo del tipo documento del pago generado o InfoPagoCab existente si se encuentra
     */
    private function generarPagoAnticipoPrv($arrayParametros)
    {     
        $entityFormaPago      = $arrayParametros['entityFormaPago'];
        $empresaCod           = $arrayParametros['empresaCod'];
        $oficinaId            = $arrayParametros['oficinaId'];
        $entityPersona        = $arrayParametros['entityPersona'];
        $usrCreacion          = $arrayParametros['usrCreacion'];
        $valorPagado          = $arrayParametros['valorPagado'];
        $entityRecaudacion    = $arrayParametros['entityRecaudacion'];
        $entityRecaudacionDet = $arrayParametros['entityRecaudacionDet'];        
        $entityPagoLinea      = $arrayParametros['entityPagoLinea'];
        $idBancoTipoCuenta    = $arrayParametros['bancoTipoCuentaId'];
        $numeroReferencia     = $arrayParametros['numeroReferencia'];
        $origenPago           = $arrayParametros['origenPago'];
        $fechaProceso         = $arrayParametros['fechaProceso'];
        $idBancoCtaContable   = $arrayParametros['bancoCtaContableId'];
        $intIdCanalRecaudacion= $arrayParametros['intIdCanalRecaudacion'];
        
        $strDiasRecaudados    = '0';
        
        // validar forma pago
        if ($entityFormaPago->getCodigoFormaPago() === 'REC')
        {
            // Recaudacion (REC)
            $tipoTransaccion = 'Recaudacion';
            $codigoNumeracionPago = 'PREC';
            $codigoNumeracionAnticipo = 'AREC';
        }
        else if ($entityFormaPago->getCodigoFormaPago() === 'PAL')
        {
            // Pago en Linea (PAL)
            $tipoTransaccion = 'Pago en Linea';
            $codigoNumeracionPago = 'PPAL';
            $codigoNumeracionAnticipo = 'APAL';
        }
        else
        {
            // solo se admite forma pago especificadas
            throw new \Exception("No se puede generar pagos o anticipos de la forma de pago {$entityFormaPago->getCodigoFormaPago()}");
        }
     
        $arrayParametros = array('strEmpresaCod'                => $empresaCod,
                                 'intIdCanalRecaudacion'        => $intIdCanalRecaudacion,
                                 'strDescricionCaracteristica'  => 'NUMERO DIAS RECAUDACION EXISTENTE',
                                 'strEstadoCaracteristica'      => 'Activo');        
                         
        $objAdmiCanalRecaudacionCaract = $this->emfinan->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->getCanalRecaudacionCaracteristica($arrayParametros);
        
        if(is_object($objAdmiCanalRecaudacionCaract))
        {
            $strDiasRecaudados = $objAdmiCanalRecaudacionCaract->getValor();
        }
           
        
        $strFechaActual                                 = date("Y-m-d"); 
        $arrayParamRecaudacion = array();
        $arrayParamRecaudacion['strEmpresaCod']         = $empresaCod;
        $arrayParamRecaudacion['strFechaDesde']         = date('Y-m-d', strtotime("-$strDiasRecaudados day $strFechaActual"));
        $arrayParamRecaudacion['strfechaHasta']         = $strFechaActual; 
        $arrayParamRecaudacion['intIdFormaPago']        = $entityFormaPago->getId(); 
        $arrayParamRecaudacion['strNumeroReferencia']   = $numeroReferencia; 
        $arrayParamRecaudacion['intIdBancoTipoCuenta']  = $idBancoTipoCuenta; 
        $arrayParamRecaudacion['intIdBancoCtaContable'] = $idBancoCtaContable; 
        
    
        $arrayPagoExistente = $this->emfinan->getRepository('schemaBundle:InfoPagoCab')
                                            ->findPagoExistenteRecaudacionPagoLinea($arrayParamRecaudacion);
        if (!empty($arrayPagoExistente))
        {
            // retornar pago existente encontrado
            return $arrayPagoExistente;
        }
        
        $respuesta = '';
        $clientearray = array();
        $entityPunto = null;
        
        if (!is_null ( $entityPersona )) 
        {
            // obtener clientes de la persona
            $clientearray = $this->emcom->getRepository ( 'schemaBundle:InfoPersonaEmpresaRol' )
                    ->getPersonaEmpresaRolPorPersonaPorTipoRolEstados ( $entityPersona->getId (), 
                      array('Cliente','Pre-cliente'), $empresaCod, array (
                      'Activo','Activa','Cancelado','Cancelada','PendAprobSolctd','Pend-convertir' ) );          
            if (count ( $clientearray ) > 0) 
            {
                // obtener id de puntos con saldo de cada cliente
                // echo "Encontro Cliente :".$entityPersona->getId()."<br>"; die;
                $arrayIdPuntoConSaldo = array ();
                $i = 0;
                foreach ( $clientearray as $cliente ) 
                {
                    $puntos = $this->emcom->getRepository ( 'schemaBundle:InfoPunto' )
                                   ->findBy( array('personaEmpresaRolId' => $cliente ["id"]), array('id' => 'ASC') );
                    
                    foreach ( $puntos as $punto ) 
                    {
                        $arraySaldo = $this->emfinan->getRepository ( 'schemaBundle:InfoPagoCab' )
                                           ->obtieneSaldoPorPunto ( $punto->getId () );
                        
                        if( !empty($arraySaldo) )
                        {
                            $arrayTmpSaldo = ( !empty($arraySaldo[0]) ? $arraySaldo[0] : array() );
                            $floatSaldo    = ( isset($arrayTmpSaldo['saldo']) ? $arrayTmpSaldo['saldo'] : 0 );
                            $floatSaldo    = ( !empty($floatSaldo) ? round($floatSaldo, 2) : 0 );

                            if( floatval($floatSaldo) > 0 ) 
                            {
                                $arrayIdPuntoConSaldo[$i] = $punto->getId ();
                                $i ++;
                            }//( floatval($floatSaldo) > 0 ) 
                        }//( !empty($saldoarr) )
                    }//foreach ( $puntos as $punto ) 
                }
                
                if( !empty($arrayIdPuntoConSaldo) )
                {
                    $arrayFacturas = array();
                    
                    //Bloque que obtiene las facturas abiertas ordenadas de la más antigua a la actual de los puntos con deuda pendiente
                    $arrayParametrosFacturas                       = array();
                    $arrayParametrosFacturas["strCodEmpresa"]      = $empresaCod;
                    $arrayParametrosFacturas["arrayPuntos"]        = $arrayIdPuntoConSaldo;
                    $arrayParametrosFacturas["arrayTipoDocumento"] = array('FAC','FACP');
                    $arrayParametrosFacturas["arrayInEstados"]     = array('Activo', 'Activa', 'Courier');
                    $arrayParametrosFacturas["orderBy"]            = "feCreacionAsc";

                    $arrayFacturas = $this->emfinan->getRepository ( 'schemaBundle:InfoDocumentoFinancieroCab' )
                                          ->findDocumentosFinancieros($arrayParametrosFacturas);
                
                    if( !empty($arrayFacturas) )
                    {
                        foreach ( $arrayFacturas as $entityFacturaAbierta )
                        {
                            //Se modifica para que obtenga el saldo de la Factura en base a la funcion F_SALDO_X_FACTURA
                            $saldoFactura = 0;                        
                            $arrayParametrosSaldo = array();
                            $arrayParametrosSaldo = array('intIdDocumento'     => $entityFacturaAbierta->getId (),
                                                          'strFeConsultaHasta' => '',
                                                          'strTipoConsulta'    => 'saldo');
                            $saldoFactura         = $this->emfinan->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                                  ->getSaldoXFactura($arrayParametrosSaldo);
                            
                            $saldoFactura = round ( $saldoFactura,2 );     
                            $valorPagado  = round ( $valorPagado, 2 );
                            
                            // CREA PAGO DEL DEBITO PROCESADO
                            if (round ( $valorPagado, 2 ) > 0 && round($saldoFactura,2)>0) 
                            {
                                $valorCabeceraPago = 0;
                                $tipoDoc = 'PAG';
                                $estadoPag = 'Cerrado';
                                $entityPunto = $this->emcom->getRepository ( 'schemaBundle:InfoPunto' )
                                    ->find ( $entityFacturaAbierta->getPuntoId () );

                                if ($entityFormaPago->getCodigoFormaPago() === 'REC')
                                {
                                    // Recaudacion (REC)
                                    $strComentario = "{$tipoTransaccion} de {$origenPago}, referencia: {$numeroReferencia}";
                                }
                                else if ($entityFormaPago->getCodigoFormaPago() === 'PAL')
                                {
                                    // Pago en Linea (PAL)
                                    $strTipoTc   = $this->getValorTrama($entityPagoLinea->getComentarioPagoLinea(), "Terminal");
                                    $arrayParams = $this->getComentarioParam($origenPago, $usrCreacion);
                                    $strComentario = $arrayParams["comentario"];
                                    $usrCreacion   = $arrayParams["usuario"];

                                    $strComentario = str_replace("{{tipoTransaccion}}", $tipoTransaccion, $strComentario);
                                    $strComentario = str_replace("{{origenPago}}", $origenPago, $strComentario);
                                    $strComentario = str_replace("{{numeroReferencia}}", $numeroReferencia, $strComentario);
                                    $strComentario = str_replace("{{tipoTc}}", $strTipoTc, $strComentario);
                                }

                                // CREA CABECERA DEL PAGO
                                // --*************************
                                $entityPagoCab = new InfoPagoCab();
                                $entityPagoCab->setPuntoId ( $entityPunto->getId () );
                                $entityPagoCab->setOficinaId ( $oficinaId );
                                $entityPagoCab->setEmpresaId ( $empresaCod );
                                // Obtener la numeracion de la tabla Admi_numeracion
                                $datosNumeracion = $this->emcom->getRepository ( 'schemaBundle:AdmiNumeracion' )
                                    ->findByEmpresaYOficina ( $empresaCod, $oficinaId, $codigoNumeracionPago );
                                $secuencia_asig = str_pad ( $datosNumeracion->getSecuencia (), 7, '0', STR_PAD_LEFT );
                                $numero_de_pago = $datosNumeracion->getNumeracionUno () . '-' . 
                                    $datosNumeracion->getNumeracionDos () . '-' . $secuencia_asig;
                                // Actualizo la numeracion en la tabla
                                $numero_act = ($datosNumeracion->getSecuencia () + 1);
                                $datosNumeracion->setSecuencia ( $numero_act );
                                $this->emcom->persist ( $datosNumeracion );
                                $this->emcom->flush ();

                                $entityPagoCab->setNumeroPago ( $numero_de_pago );
                                $entityPagoCab->setValorTotal ( $valorPagado );
                                $entityPagoCab->setEstadoPago ( $estadoPag );
                                $entityPagoCab->setComentarioPago ( $strComentario );
                                $entityPagoCab->setFeCreacion ( new \DateTime ( 'now' ) );
                                $entityPagoCab->setUsrCreacion ( $usrCreacion );
                                $entityAdmiTipoDocumento = $this->emfinan->getRepository ( 'schemaBundle:AdmiTipoDocumentoFinanciero' )
                                    ->findOneByCodigoTipoDocumento ( $tipoDoc );
                                $entityPagoCab->setTipoDocumentoId ( $entityAdmiTipoDocumento );
                                $entityPagoCab->setRecaudacionId ( $entityRecaudacion );
                                $entityPagoCab->setRecaudacionDetId($entityRecaudacionDet);
                                $entityPagoCab->setPagoLinea( $entityPagoLinea );
                                $this->emfinan->persist ( $entityPagoCab );
                                                                
                                $intBandera=0;
                                //SE VERIFICA SI EL PAGO YA CUBRE LA FACTURA
                                if ($saldoFactura == $valorPagado) 
                                {
                                    $entityFacturaAbierta->setEstadoImpresionFact ( 'Cerrado' );
                                    $this->emfinan->persist ( $entityFacturaAbierta );
                                    $valorPago = $valorPagado;
                                    $valorPagado = $valorPagado - $saldoFactura;
                                    $intBandera  = 1;
                                } 
                                elseif ($saldoFactura < $valorPagado) 
                                {
                                    $entityFacturaAbierta->setEstadoImpresionFact ( 'Cerrado' );
                                    $this->emfinan->persist ( $entityFacturaAbierta );
                                    $valorPago = $saldoFactura;
                                    $valorPagado = $valorPagado - $saldoFactura;
                                    $intBandera  = 2;
                                } 
                                else 
                                {
                                    $valorPago = $valorPagado;
                                    $valorPagado = $valorPago - $valorPago;
                                    $intBandera  = 3;
                                }
                                // Graba historial de la factura                                 
                                 $this->serviceUtil->insertError(
                                                                'Telcos+',
                                                                'InfoPagoService->generarPagoAnticipoPrv', 
                                                                'Se ingresa Historial por cierre de Documento ID_DOCUMENTO='.
                                                                $entityFacturaAbierta->getId (). '  ESTADO_IMPRESION_FACT='.
                                                                $entityFacturaAbierta->getEstadoImpresionFact ().
                                                                '  SALDO_FACTURA='.$saldoFactura.
                                                                '  VALOR_PAGO='. $valorPago.
                                                                '  SALDO_VALOR_PAGADO='.$valorPagado.
                                                                '  BANDERA='.$intBandera,
                                                                $usrCreacion, 
                                                                '127.0.0.1'
                                                               );

                                $historialFactura = new InfoDocumentoHistorial();
                                $historialFactura->setDocumentoId ( $entityFacturaAbierta );
                                $historialFactura->setEstado ( $entityFacturaAbierta->getEstadoImpresionFact () );
                                $historialFactura->setFeCreacion ( new \DateTime ( 'now' ) );
                                $historialFactura->setUsrCreacion ( $usrCreacion );
                                $this->emfinan->persist ( $historialFactura );
                               
                                // CREA DETALLES DEL PAGO
                                $entityPagoDet = new InfoPagoDet();
                                $valorCabeceraPago = $valorCabeceraPago + $valorPago;
                                $entityPagoDet->setPagoId ( $entityPagoCab );
                                $entityPagoDet->setDepositado ( 'N' );
                                $entityPagoDet->setFeDeposito ( $fechaProceso );
                                $entityPagoDet->setFeCreacion ( new \DateTime ( 'now' ) );
                                $entityPagoDet->setUsrCreacion ( $usrCreacion );
                                $entityPagoDet->setFormaPagoId ( $entityFormaPago->getId() );
                                $entityPagoDet->setValorPago ( $valorPago );
                                $entityPagoDet->setBancoTipoCuentaId ( $idBancoTipoCuenta );
                                $entityPagoDet->setBancoCtaContableId ( $idBancoCtaContable );
                                $entityPagoDet->setCuentaContableId ( $idBancoCtaContable );
                                $entityPagoDet->setNumeroReferencia ( $numeroReferencia );
                                $entityPagoDet->setComentario ( $strComentario );
                                $entityPagoDet->setEstado ( $estadoPag );
                                $entityPagoDet->setReferenciaId ( $entityFacturaAbierta->getId () );
                                $this->emfinan->persist ( $entityPagoDet );
                                // Se setea valor total de cabecera y hago persistencia
                                $entityPagoCab->setValorTotal ( $valorCabeceraPago );
                                $this->emfinan->persist ( $entityPagoCab );
                                $this->emfinan->flush ();
                                $respuesta = $tipoDoc;
                                //INGRESA HISTORIAL PARA EL ANTICIPO
                                $this->ingresaHistorialPago($entityPagoCab, $estadoPag, new \DateTime ( 'now' ), 
                                    $usrCreacion, null, $strComentario);                            
                            }//(round ( $valorPagado, 2 ) > 0 && round($saldoFactura,2)>0)
                        }//foreach ( $arrayFacturas as $entityFacturaAbierta )
                    }//( !empty($arrayFacturas) ) 
                }//( !empty($arrayIdPuntoConSaldo) )
            }//(count ( $clientearray ) > 0)
        }//(!is_null ( $entityPersona )) 
            
        
        // CREA ANTICIPO SI ES NECESARIO y SI ENCONTRO EL CLIENTE
        if (round ( $valorPagado, 2 ) > 0) 
        {
            // Si no encontro factura no tendra punto para asignar
            // por lo tanto se busca el primer punto padre activo y se le asigna el pago
            if (! $entityPunto && count ( $clientearray ) > 0) 
            {
                $entityPunto = $this->emcom->getRepository ( 'schemaBundle:InfoPunto' )
                    ->findPrimerPtoClientePadreActivoPorPersonaEmpresaRolId ( $clientearray [0] ["id"] );
            }
            // SI SE ENCONTRO PUNTO ENTONCES GRABA ANTICIPO
            // SE CREA LA CABECERA DEL ANTICIPO
            $entityAnticipoCab = new InfoPagoCab ();
            $entityAnticipoCab->setEmpresaId ( $empresaCod );
            $entityAnticipoCab->setEstadoPago ( 'Pendiente' );
            $entityAnticipoCab->setFeCreacion ( new \DateTime ( 'now' ) );
            $tipoDocumentoAnticipo = 'ANTS';
            if ($entityPunto)
            {
                $entityAnticipoCab->setPuntoId ( $entityPunto->getId () );
                $tipoDocumentoAnticipo = 'ANT';
            }
            $comentarioAnticipo = "Anticipo ({$tipoDocumentoAnticipo}) generado ".
                "por {$tipoTransaccion} de {$origenPago}, referencia: {$numeroReferencia}";
            $entityAdmiTipoDocumento = $this->emfinan->getRepository ( 'schemaBundle:AdmiTipoDocumentoFinanciero' )
                ->findOneByCodigoTipoDocumento ( $tipoDocumentoAnticipo );
            $entityAnticipoCab->setTipoDocumentoId ( $entityAdmiTipoDocumento );
            // Obtener la numeracion de la tabla Admi_numeracion
            $datosNumeracionAnticipo = $this->emcom->getRepository ( 'schemaBundle:AdmiNumeracion' )
                ->findByEmpresaYOficina ( $empresaCod, $oficinaId, $codigoNumeracionAnticipo );
            $secuencia_asig = '';
            $secuencia_asig = str_pad ( $datosNumeracionAnticipo->getSecuencia (), 7, '0', STR_PAD_LEFT );
            $numero_de_anticipo = $datosNumeracionAnticipo->getNumeracionUno () . '-' . 
                $datosNumeracionAnticipo->getNumeracionDos () . '-' . $secuencia_asig;
            // Actualizo la numeracion en la tabla
            $numero_act = ($datosNumeracionAnticipo->getSecuencia () + 1);
            $datosNumeracionAnticipo->setSecuencia ( $numero_act );
            $this->emcom->persist ( $datosNumeracionAnticipo );
            $this->emcom->flush ();
            
            $entityAnticipoCab->setNumeroPago ( $numero_de_anticipo );
            $entityAnticipoCab->setOficinaId ( $oficinaId );
            $entityAnticipoCab->setUsrCreacion ( $usrCreacion );
            $entityAnticipoCab->setComentarioPago ( $comentarioAnticipo );
            $entityAnticipoCab->setRecaudacionId ( $entityRecaudacion );
            $entityAnticipoCab->setRecaudacionDetId ( $entityRecaudacionDet );
            $entityAnticipoCab->setPagoLinea ( $entityPagoLinea );
            $entityAnticipoCab->setValorTotal ( $valorPagado );
            $this->emfinan->persist ( $entityAnticipoCab );
            
            // CREA LOS DETALLES DEL ANTICIPO
            $entityAnticipoDet = new InfoPagoDet ();
            $entityAnticipoDet->setEstado ( 'Pendiente' );
            $entityAnticipoDet->setFeDeposito ( $fechaProceso );
            $entityAnticipoDet->setFeCreacion ( new \DateTime ( 'now' ) );
            $entityAnticipoDet->setUsrCreacion ( $usrCreacion );
            $entityAnticipoDet->setValorPago ( $valorPagado );
            $entityAnticipoDet->setComentario ( $comentarioAnticipo );
            $entityAnticipoDet->setNumeroReferencia ( $numeroReferencia );
            $entityAnticipoDet->setDepositado ( 'N' );
            $entityAnticipoDet->setBancoTipoCuentaId ( $idBancoTipoCuenta );
            $entityAnticipoDet->setBancoCtaContableId($idBancoCtaContable);
            $entityAnticipoDet->setCuentaContableId ( $idBancoCtaContable );
            $entityAnticipoDet->setPagoId ( $entityAnticipoCab );
            $entityAnticipoDet->setFormaPagoId ( $entityFormaPago->getId() );
            $this->emfinan->persist ( $entityAnticipoDet );
            $this->emfinan->flush ();
            $respuesta = $tipoDocumentoAnticipo;
            //INGRESA HISTORIAL PARA EL ANTICIPO
            $this->ingresaHistorialPago($entityAnticipoCab, 'Pendiente', new \DateTime ( 'now' ), 
                $usrCreacion, null, $comentarioAnticipo);
        } 
        //Si es un pago o anticipo de algun cliente se marca como asignado el detalle de retencion
        if ($entityRecaudacionDet && ($respuesta==='PAG' || $respuesta==='ANT'))
        {
            $entityRecaudacionDet->setEsCliente("S");       
            $entityRecaudacionDet->setAsignado("S");
            $entityRecaudacionDet->setPersonaEmpresaRolId($entityPunto->getPersonaEmpresaRolId()->getId());
            $this->emfinan->persist ( $entityRecaudacionDet );
            $this->emfinan->flush ();             
        }    
       
        return $respuesta;
    }

    /**
     * Método que obtiene la configuración de comentario y usuario
     * para el respectivo banco configurado
     * 
     * @param $origenPago nombre del banco
     * @param $usuario Usuario por defecto del banco
     * 
     * @return array [
     *          'comentario' => Comentario de la trama para el banco configurado
     *          'usuario     => Usuario para el banco configurado
     * ]
     * 
     * @author Jose Bedon Sanchez <jobedon@telconet.ec>
     * @version 1.0 13-03-2020
     * 
     */
    public function getComentarioParam($strOrigenPago, $strUsuario)
    {
        $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array(
                                                                'nombreParametro' => 'BUSPAGOS',
                                                                'estado'          => 'Activo'
                                                ));
        if (is_object($objAdmiParametroCab))
        {
            $objAdmiParametroDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy(array(
                                                                    'parametroId' => $objAdmiParametroCab,
                                                                    'valor1'      => $strOrigenPago,
                                                                    'estado'      => 'Activo'
                                                    ));
            if (is_object($objAdmiParametroDet))
            {
                return array(
                    "comentario" => $objAdmiParametroDet->getValor2(),
                    "usuario"    => $objAdmiParametroDet->getValor3()
                );
            }
        }
        return array(
            "comentario" => "{{tipoTransaccion}} de {{origenPago}}, referencia: {{numeroReferencia}}",
            "usuario"    => $strUsuario
        );
    }

    /**
     * Método que obtiene el valor requerido de una trama dada
     * 
     * @param $strComentarioPagoLinea comentario a analizar
     * @param $clave Clave que se requiere obtener
     * 
     * @return String valor de la clave a buscar en la trama
     * 
     * @author Jose Bedon Sanchez <jobedon@telconet.ec>
     * @version 1.0 13-03-2020
     */
    public function getValorTrama($strComentarioPagoLinea, $strClave)
    {
        $intPos = strpos($strComentarioPagoLinea, $strClave);
        $intLengthClave = strlen($strClave);
        $intInitSearch = $intPos + $intLengthClave + 1;
        $strComentario = substr($strComentarioPagoLinea, $intInitSearch);
        $intPos = strpos($strComentario, " - ");
        if ($intPos !== false)
        {
            $strComentario = substr($strComentario, 0, $intPos);
        }
        return $strComentario;
    }
    
    /**
     * Crea historial en la tabla de pagos (InfoPagoHistorial)
     * @param InfoPagoCab $InfopagoPagoCab objeto de la cabecera del pago o anticipo
     * @param string $estado
     * @param \DateTime $feCreacion
     * @param string $usrCreacion
     * @param integer $motivoId
     * @param string $observacion
     */    
    function ingresaHistorialPago(InfoPagoCab $InfopagoPagoCab,$estado,$feCreacion,$usrCreacion,$motivoId,$observacion){
        $infoPagoHistorial=new InfoPagoHistorial();
        $infoPagoHistorial->setEstado($estado);
        $infoPagoHistorial->setFeCreacion($feCreacion);
        if($motivoId)
        {
            $infoPagoHistorial->setMotivoId($motivoId);
        }    
        $infoPagoHistorial->setObservacion($observacion);
        $infoPagoHistorial->setPagoId($InfopagoPagoCab);
        $infoPagoHistorial->setUsrCreacion($usrCreacion);
        $this->emfinan->persist ( $infoPagoHistorial );
        $this->emfinan->flush ();     
    }
    /**
     * obtiene historial segun id de pago
     * @param integer $motivoId
     *  return array (arreglo con el historial de pagos)
     */     
    function obtenerHistorialPago($idPago)
    {
        //Obtener el historial
        $entityHistorial=$this->emfinan->getRepository('schemaBundle:InfoPagoHistorial')->findByPagoId($idPago);
        $historial=null;
        if($entityHistorial)
        {
            $i=0;
            foreach($entityHistorial as $histo)
            {
                $motivo_descri=null;
                if($histo->getMotivoId()!=null)
                {
                    $motivo=$this->emcom->getRepository('schemaBundle:AdmiMotivo')->find($histo->getMotivoId());
                    if($motivo)
                    {    
                        $motivo_descri=$motivo->getNombreMotivo();
                    }        
                }
                $historial[$i]['motivo']=$motivo_descri;
                $historial[$i]['estado']=$histo->getEstado();
                $historial[$i]['fe_creacion']=strval(date_format($histo->getFeCreacion(),"d/m/Y G:i"));
                $historial[$i]['usr_creacion']=$histo->getUsrCreacion();
                $historial[$i]['observacion']=$histo->getObservacion();
                $i++;
            }
        }
        return $historial;
    }
    
    /**
    * Documentación para el método 'updateInfoPagoCab'.
    * Este método actualiza el punto de un anticipo en la estructura
    * INFO_PAGO_CAB
    *
    * @return array in  $arrayInfoPagoCabParamIn    : recibe  ('intIdAnticipo' | 'strObservacion' | 'strUser' | 'intIdMotivo' | 'intIdPtoCliente')
    * @return array out $arrayResult                : retorna ('success' | 'msg')
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 29-07-2014
    */
    public function updateInfoPagoCab($arrayInfoPagoCabParamIn) {

        $strMsg                 =   NULL;
        $boolSuccess            =   TRUE;
        $arrayResult            =   NULL;
        $objInfoPagoCab         =   NULL;

        $objInfoPagoCab         =   $this->emfinan->getRepository('schemaBundle:InfoPagoCab')->find($arrayInfoPagoCabParamIn['intIdAnticipo']);
        $this->emfinan->getConnection()->beginTransaction();
        $strObservacion         =   $objInfoPagoCab->getComentarioPago() ? $objInfoPagoCab->getComentarioPago().'; ': '';

        /*Concatena el motivo de cambio de punto.*/
        $strObservacion         =   trim($strObservacion).'Motivo Cambio Punto: '.trim($arrayInfoPagoCabParamIn['strObservacion']);

        /*Valida que exista el punto*/
        if (!$objInfoPagoCab) {
            $strMsg      = "No fue encontrado el punto...";
            $boolSuccess = false;
        } else {
            /*Valida tipo de documento financiero: 3 => Anticipo y que el anticipo se encuentre en estado Pendiente
             * Se agrega anticipos por cruce y anticipos sin cliente temporalmente
             */
            if(($objInfoPagoCab->getTipoDocumentoId()->getId() == 3 || $objInfoPagoCab->getTipoDocumentoId()->getId() == 4 || $objInfoPagoCab->getTipoDocumentoId()->getId() == 10) && $objInfoPagoCab->getEstadoPago() == 'Pendiente'){
            /*Si cumple con la condicion se raliza la actualizacion*/
                try{
                    $objInfoPagoCab->setMotivoId($arrayInfoPagoCabParamIn['intIdMotivo']);
                    $objInfoPagoCab->setComentarioPago(trim($strObservacion));
                    $objInfoPagoCab->setUsrUltMod($strUsuario);
                    $objInfoPagoCab->setFeUltMod(new \DateTime('now'));
                    $objInfoPagoCab->setPuntoId($arrayInfoPagoCabParamIn['intIdPtoCliente']);
                    $this->emfinan->persist($objInfoPagoCab);
                    $this->emfinan->flush ();

                    $this->ingresaHistorialPago($objInfoPagoCab,
                                                $objInfoPagoCab->getEstadoPago(),
                                                new \DateTime('now'),
                                                $arrayInfoPagoCabParamIn['strUser'],
                                                $arrayInfoPagoCabParamIn['intIdMotivo'],
                                                $arrayInfoPagoCabParamIn['strObservacionHistorial']);
                    $this->emfinan->getConnection()->commit();
                    $strMsg         = "Guardado Correctamente";
                    $boolSuccess    = true;
                } catch (Exception $e) {
                    $strMsg         = 'Error en updateInfoPagoCab - '.$e->getMessage();
                    $boolSuccess    = false;
                    $this->emfinan->getConnection()->rollback();
                }
            }else{
            /*Caso contrario se devuele la variable success = false*/
                $strMsg         = "Existio un error al guardar.";
                $boolSuccess    = false;
            }
        }
        $arrayResult = array('boolSuccess' => $boolSuccess, 'strMsg' => $strMsg, 'objInfoPagoCab' => $objInfoPagoCab);
        return $arrayResult;
    }//Fin -> updateInfoPagoCab

 
    /**
    * Documentación para el método 'buscaValoresOptimos'.
    * Busca los detalles del anticipo original que sumados se acercan mas al valor del saldo de la factura
    * Ej: saldo factura:50 pago 001 => det1:20 det2:10 det3:5 det4:20 
    * Los valores de detalles mas cercanos (optimos) al saldo de la factura (50) serian => det1:20, det4:20 y det2:10
    * 
    * @param array  $arrayPagosDet    : detalles de pagos de donde seran escogidos los que sumados sean el valor mas cercanos al saldo de factura
    * @return integer $intValor       : el valor meta al cual la suma de los valores optimos deben aproximarse (saldo factura)
    *
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.0 22-07-2016
    */
    
    public function buscaValoresOptimos($arrayPagosDet,$intValor)
    {
        $arrayOptimos       = array();
        $arrayTodosLosPagos = array();     

        //BUSCAMOS SI UN DETALLE YA CUBRE EL SALDO
        foreach($arrayPagosDet as $entityPagoDet)
        {
            if(round($entityPagoDet->getValorPago(),2)==round($intValor,2))
            {
                $arrayOptimos[]=$entityPagoDet;
                break;
            }       
            $arrayTodosLosPagos = $entityPagoDet;
        }
        //SI AUN NO ENCUENTRA ANTICIPOS OPTIMOS ENTONCES BUSCA LOS ANTICIPOS MAS PROXIMOS QUE SUMADOS CUBRAN EL SALDO
        if(count($arrayOptimos)<=0)
        {
            $indOpt         = 0;
            $resumenOptimos = array(); 
            //SUMAMOS CADA VALOR EN FORMA ASCENDENTE A PARTIR DE LA POSICION DE ITERACION
            for($i=0;$i<count($arrayPagosDet);$i++)
            {   
                $intSumaOptimos = 0;
                $arrayOptimos   = array();
                for($j=$i;$j<count($arrayPagosDet);$j++)
                {
                    $intSumaOptimos=$intSumaOptimos+$arrayPagosDet[$j]->getValorPago();
                    $arrayOptimos[]=$arrayPagosDet[$j];
                    //SE PREGUNTA SI LA SUMA DE LOS ANTICIPOS ES MENOR O IGUAL A SALDO DE LA FACTURA
                    if( (round($intValor,2) - round($intSumaOptimos,2))  <= 0 )
                    {
                        $resumenOptimos[$indOpt]['valor']        = round($intSumaOptimos,2);
                        $resumenOptimos[$indOpt]['arrayPagoDet'] = $arrayOptimos;
                        $indOpt++;
                    }                    
                }                
            }
            //SUMA CADA UNO CON TODOS
            for($i=0;$i<count($arrayPagosDet);$i++)
            {   
                $intSumaOptimos = 0;
                $arrayOptimos   = array();
                for($j=0;$j<count($arrayPagosDet);$j++)
                {
                    $arrayOptimos   = array();                    
                    if ($i!=$j)
                    {    
                        $intSumaOptimos=$arrayPagosDet[$i]->getValorPago()+$arrayPagosDet[$j]->getValorPago();
                        $arrayOptimos[]=$arrayPagosDet[$i];
                        $arrayOptimos[]=$arrayPagosDet[$j];
                        //SE PREGUNTA SI LA SUMA DE LOS ANTICIPOS ES MENOR O IGUAL A SALDO DE LA FACTURA
                        if( (round($intValor,2) - round($intSumaOptimos,2))  <= 0 )
                        {
                            $resumenOptimos[$indOpt]['valor']        = round($intSumaOptimos,2);
                            $resumenOptimos[$indOpt]['arrayPagoDet'] = $arrayOptimos;
                            $indOpt++;
                        }
                    }
                }                
            }
            
            //ORDENAMOS ARREGLO EN FORMA DESCENDENTE
            foreach ($resumenOptimos as $claveOptimos => $filaOptimos) {
                $arrayAuxiliarOptimos[$claveOptimos] = $filaOptimos['valor'];

            }
            
            array_multisort($arrayAuxiliarOptimos,SORT_ASC,$resumenOptimos);

            $arrayOptimos = array();
            //SELECCIONAMOS EL MENOR VALOR QUE ES EL MAS APROXIMADO AL SALDO DE LA FACTURA
            if (count($resumenOptimos)>0)
            {                    
                foreach($resumenOptimos[0]['arrayPagoDet'] as $entityPagosSeleccionados)
                {    
                    $arrayOptimos[] = $entityPagosSeleccionados;
                }
            }
            else
            {
                $arrayOptimos = $arrayTodosLosPagos;
            }  
        } 

        $arrayPagosNoOptimos=array();
        foreach ($arrayPagosDet as $entityDetallePagoOriginal) 
        {
            $booleanEsOptimo=false;
            foreach($arrayOptimos as $entityDetallePago)
            {
                if($entityDetallePago->getId()==$entityDetallePagoOriginal->getId())
                {
                    $booleanEsOptimo=true;
                }    
            }
            if($booleanEsOptimo==false)
            {
                $arrayPagosNoOptimos[]=$entityDetallePagoOriginal;
            }
        }        
             
        $arrayPagos['optimos']= $arrayOptimos;
        $arrayPagos['noOptimos']= $arrayPagosNoOptimos;
        return $arrayPagos;
    }    
    
    
    /**
     * Documentación para el método 'getValoresDiferidosPreCancelar'.
     * 
     * Función que obtiene los valores diferidos para realizar la cancelación de la deuda diferida.
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 17-08-2020     
     * @param array $arrayParametros [
     *                                "intIdPunto" => id del punto en sesión
     *                               ]
     *
     * @return Arreglo de valores de deuda diferida.
     */
    public function getValoresDiferidosPreCancelar($arrayParametros)
    { 
        return $this->emfinan->getRepository('schemaBundle:InfoPagoCab')->getValoresDiferidosPreCancelar($arrayParametros);
    }

    
    /**
     *  Documentación para el método 'ejecutarNDIPreCancelacionDiferida'.
     * 
     * Función que invoca al proceso de generación de NDI diferidas por Deuda Diferida.
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0, 14-08-2020
     * 
     */
    public function ejecutarNDIPreCancelacionDiferida($arrayParametros)
    {

        return $this->emfinan->getRepository('schemaBundle:InfoPagoCab')->ejecutarNDIPreCancelacionDiferida($arrayParametros);

    }

    public function registraPagoAutomatico($arrayParametros)
    {   
        $entityPagoLinea = $arrayParametros['entityPagoLinea'];
        $strUsrCreacion = $arrayParametros['usuarioCreacion'];
        $strFecha = $arrayParametros['fecha'];
        $intOficinaId= $entityPagoLinea->getOficinaId();
        $arrayDepartamento = $this->emcom->getRepository ( 'schemaBundle:InfoOficinaGrupo' )
        ->findOneBy(array('id' => $intOficinaId )); 
        $strNombreOficina =  $arrayDepartamento->getNombreOficina();
        $arrayCorreroDepartamento= $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
        ->getOne("CORREO_POR_DEPARTAMENTO",  "FINANCIERO", "PAGOS", "", $strNombreOficina, 
                "COBRANZAS",  "",  "","", "", "", "", "");
        $strCorreroDepartamento = $arrayCorreroDepartamento['valor3'];
        $objInfoPagoAutomaticoCab = $this->emfinan->getRepository ( 'schemaBundle:InfoPagoAutomaticoCab' )
                                     ->findOneBy(
                                         array('nombreArchivo' => 'Pagos Extranet con Datafast - '.$strFecha->format('Y-m-d'),
                                               'usrCreacion' => $strUsrCreacion));

        if (!$objInfoPagoAutomaticoCab)
        {
            $objInfoPagoAutomaticoCab = new InfoPagoAutomaticoCab();
            $objInfoPagoAutomaticoCab->setCuentaContableId($entityPagoLinea->getCanalPagoLinea()->getBancoCtaContableId());
            $objInfoPagoAutomaticoCab->setBancoTipoCuentaId($entityPagoLinea->getCanalPagoLinea()->getBancoTipoCuentaId());
            $objInfoPagoAutomaticoCab->setRutaArchivo('Pagos en linea por canal Extranet '.$strFecha->format('Y-m-d'));
            $objInfoPagoAutomaticoCab->setNombreArchivo('Pagos Extranet con Datafast - '.$strFecha->format('Y-m-d'));   
            $objInfoPagoAutomaticoCab->setEstado('Pendiente');     
            $objInfoPagoAutomaticoCab->setFeCreacion($strFecha);
            $objInfoPagoAutomaticoCab->setIpCreacion('127.0.0.1');
            $objInfoPagoAutomaticoCab->setUsrCreacion($strUsrCreacion);
            $this->emfinan->persist($objInfoPagoAutomaticoCab);
            $this->emfinan->flush();            
        }

        if ($objInfoPagoAutomaticoCab->getEstado() != 'Pendiente')
        {
            $objInfoPagoAutomaticoCab->setEstado('Pendiente');
            $this->emfinan->persist($objInfoPagoAutomaticoCab);
            $this->emfinan->flush();     
        }

        $objInfoPagoAutomaticoDet = new InfoPagoAutomaticoDet();
        $objInfoPagoAutomaticoDet->setPagoAutomaticoId($objInfoPagoAutomaticoCab->getId());
        $objInfoPagoAutomaticoDet->setFormaPagoId($entityPagoLinea->getCanalPagoLinea()->getFormaPago()->getId());
        $objInfoPagoAutomaticoDet->setObservacion('Pago en linea canal Extranet - Datafast');
        $objInfoPagoAutomaticoDet->setNumeroReferencia($entityPagoLinea->getNumeroReferencia());
        $objInfoPagoAutomaticoDet->setMonto($entityPagoLinea->getValorPagoLinea());
        $objInfoPagoAutomaticoDet->setEstado('Pendiente');
        $objInfoPagoAutomaticoDet->setIpCreacion('127.0.0.1');
        $objInfoPagoAutomaticoDet->setUsrCreacion($strUsrCreacion);
        $objInfoPagoAutomaticoDet->setFeCreacion($strFecha);
        $objInfoPagoAutomaticoDet->setFecha($strFecha->format('Y-m-d'));
        $this->emfinan->persist($objInfoPagoAutomaticoDet);
        $this->emfinan->flush();

        $arrayParametros = array();
        if ($entityPagoLinea->getPersona()->getRazonSocial() === null)
        {
            $arrayParametros['nombreCliente'] = $entityPagoLinea->getPersona()->getNombres().' '.$entityPagoLinea->getPersona()->getApellidos();
        }
        else
        {
            $arrayParametros['nombreCliente'] = $entityPagoLinea->getPersona()->getRazonSocial();
        }

        $arrayParametros['secuencialRecaudador'] = $entityPagoLinea->getNumeroReferencia();
        $arrayParametros['valorPago'] = $entityPagoLinea->getValorPagoLinea();

        $this->serviceEnvioPlantilla->generarEnvioPlantilla('Notificacion de Pagos en Linea Telconet', 
                                                            array($strCorreroDepartamento), 'PAL-EXTRANET', 
                                                                $arrayParametros, '', '', '');
       
        return 'PAG';
    }

}
