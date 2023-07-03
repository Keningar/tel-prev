<?php

namespace telconet\financieroBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroImp;
use telconet\schemaBundle\Entity\InfoProcesoMasivoCab;
use telconet\schemaBundle\Entity\InfoProcesoMasivoDet;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\financieroBundle\Service\InfoDetalleDocumentoService;
use telconet\soporteBundle\Service\EnvioPlantillaService;
use telconet\schemaBundle\Entity\InfoDocumentoCaracteristica;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;

class InfoNotaCreditoService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emgene;
    
    private $InfoDetalleDocumento;
    
    /**
     * @var EnvioPlantillaService
     */
    private $envioPlantilla;
    
    /*
     * @var String
     */
    private $tipoImpuesto;
    
    private $utilService; 
    
    private $serviceDocFinancieroCab;
      
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container,InfoDetalleDocumentoService $InfoDetalleDocumento)
    {
        $this->emcom                   = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emfinan                 = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emgene                  = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->envioPlantilla          = $container->get('soporte.EnvioPlantilla');
        $this->InfoDetalleDocumento    = $InfoDetalleDocumento;
        $this->tipoImpuesto            = "IVA";
        $this->utilService             = $container->get('schema.Util');
        $this->serviceDocFinancieroCab = $container->get('financiero.InfoDocumentoFinancieroCab');
        $this->emInfraestructura       = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        
    }
    
    /**
     * Documentación para la función 'validarCompensacionNotaCredito'
     * 
     * Valida si la nota de crédito o nota de crédito interna debe compensar
     * 
     * @param array $arrayParametros ['intIdDocumento' => 'Id de la factura a verificar si ha compensado']
     * @return String $strEsCompensado   Indica si la NC o NCI debe ser compensada.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 21-02-2017
     */
    function validarCompensacionNotaCredito($arrayParametros)
    {
        $strEsCompensado = 'N';
        $intIdDocumento  = ( isset($arrayParametros['intIdDocumento']) && !empty($arrayParametros['intIdDocumento']) ) 
                           ? $arrayParametros['intIdDocumento'] : 0;
        
        try
        {
            if( !empty($intIdDocumento) && $intIdDocumento > 0 )
            {
                $objInfoDocumentoFinancieroCab = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                      ->findOneById($intIdDocumento);

                if( is_object($objInfoDocumentoFinancieroCab) )
                {
                    $floatValorCompensacion = $objInfoDocumentoFinancieroCab->getDescuentoCompensacion();

                    if( !empty($floatValorCompensacion) && floatval($floatValorCompensacion) > 0 )
                    {
                        $strEsCompensado = 'S';
                    }//( !empty($floatValorCompensacion) && floatval($floatValorCompensacion) > 0 )
                }//( is_object($objInfoDocumentoFinancieroCab) )
            }
            else
            {
                throw new \Exception('Debe enviar un documento válido para verificar si la nota de crédito debe compensar');
            }//( !empty($intIdDocumento) && $intIdDocumento > 0 )
        }
        catch(\Exception $e)
        {
            throw ($e);
        }
        
        return $strEsCompensado;
    }

    /**
     * Genera la nota de credito o nota de credito interna
     * @param parametros variables del controlador
     * @return entity Retorna la nota de credito o nota de credito interna creada
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 22-07-2014
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 02-10-2014
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 24-02-2015
     * @since 1.1
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 21-05-2016 - Se guarda el tipo de nota de crédito como observacion en el historial, y se verifica si el cliente paga iva.
     * @since 1.2
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 28-06-2016 - Se valida si la Factura fue hecha con el impuesto del ICE para poder crear la NC con los impuestos correctos. 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 15-07-2016 - Se quita la validacion para verificar si el cliente paga IVA en los planes. Puesto que los planes son usados
     *                           únicamente por MD y todos los clientes pagan IVA, y esa validación está afectando a las NCI. 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 08-10-2016 - Se guarda la compensación solidaria cuando la NC deba ser compensada.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 16-12-2016 - Al guardar una NC o NCI con valor original se debe tomar los impuestos guardados en base por la factura, los mismos
     *                           que son enviados desde el grid de las NC o NCI.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 25-01-2017 - Se realiza una validación adicional cuando el documento a crear tiene ICE, la cual consiste en validar si la suma
     *                           total de los impuestos del documento tal como están guardados en base restados de la suma total de los impuestos
     *                           redondeados genera una diferencia mayor a 0.005 pero menor a 0.01. Si fuese el caso se debe sumar la diferencia
     *                           obtenida al subtotal con impuestos y modificar el valor total de la nota de crédito.
     * 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.9 12-12-2017 - Se agrega setedo del campo SERVICIO_ID en creación de detalles del documento.
     *  
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 2.0 10-02-2020 - Se agrega validación para insertar registro por característica 'EDICION_VALORES_NC', debido a la edición   
     *                           de valores en el grid al crear nota de crédito. 
     */
    function generarNotaDeCredito($arrayParametrosIn)
    {
        $strEstado              = $arrayParametrosIn["estado"];
        $strCodigo              = $arrayParametrosIn["codigo"];
        $objInformacionGrid     = $arrayParametrosIn["informacionGrid"];
        $intIdPunto             = $arrayParametrosIn["punto_id"];
        $intIdOficina           = $arrayParametrosIn["oficina_id"];
        $strObservacion         = $arrayParametrosIn["observacion"];
        $intIdDocumento         = $arrayParametrosIn["facturaId"];
        $strUser                = $arrayParametrosIn["user"];
        $arrayMotivo            = $arrayParametrosIn["motivo_id"];
        $strEsElectronica       = $arrayParametrosIn["strEselectronica"];
        $intIdEmpresa           = $arrayParametrosIn["intIdEmpresa"];
        $strPrefijoEmpresa      = $arrayParametrosIn["strPrefijoEmpresa"];
        $strTipoNotaCredito     = $arrayParametrosIn["strTipoNotaCredito"];
        $strTipoResponsable     = $arrayParametrosIn["strTipoResponsable"];
        $strClienteResponsable  = $arrayParametrosIn["strClienteResponsable"];
        $strEmpresaResponsable  = $arrayParametrosIn["strEmpresaResponsable"];
        $strIpCreacion          = $arrayParametrosIn["strIpCreacion"];
        $strDescripcionInterna  = trim($arrayParametrosIn["strDescripcionInterna"]);
        $boolDocumentoTieneIce  = false;
        $intEditValoresNcCaract = $arrayParametrosIn["intEditValoresNcCaract"];


        //busqueda del documento
        $entityAdmiTipoDocumentoFinanciero = $this->emfinan->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                  ->findOneByCodigoTipoDocumento($strCodigo);
        $entityInfoDocumentoFinancieroCab = new InfoDocumentoFinancieroCab();
        $entityInfoDocumentoFinancieroCab->setTipoDocumentoId($entityAdmiTipoDocumentoFinanciero);
        $entityInfoDocumentoFinancieroCab->setPuntoId($intIdPunto);
        $entityInfoDocumentoFinancieroCab->setEsAutomatica("N");
        $entityInfoDocumentoFinancieroCab->setProrrateo("N");
        $entityInfoDocumentoFinancieroCab->setReactivacion("N");
        $entityInfoDocumentoFinancieroCab->setRecurrente("N");
        $entityInfoDocumentoFinancieroCab->setComisiona("N");
        $entityInfoDocumentoFinancieroCab->setOficinaId($intIdOficina);
        $entityInfoDocumentoFinancieroCab->setFeCreacion(new \DateTime('now'));
        $entityInfoDocumentoFinancieroCab->setFeEmision(new \DateTime('now'));
        $entityInfoDocumentoFinancieroCab->setUsrCreacion($strUser);
        $entityInfoDocumentoFinancieroCab->setEstadoImpresionFact($strEstado);
        $entityInfoDocumentoFinancieroCab->setObservacion(trim($strObservacion));
        $entityInfoDocumentoFinancieroCab->setReferenciaDocumentoId($intIdDocumento);
        $entityInfoDocumentoFinancieroCab->setEsElectronica($strEsElectronica);
        $this->emfinan->persist($entityInfoDocumentoFinancieroCab);
        $this->emfinan->flush();
        
        
        if( $strTipoResponsable == 'Cliente' || $strTipoResponsable == 'Empresa' )
        {
            $objAdmiCaracteristicaTipoResponsable = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneBy( array('estado'                    => 'Activo', 
                                                                            'descripcionCaracteristica' => 'TIPO_RESPONSABLE_NC') );
            
            $objAdmiCaracteristicaResponsable = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy( array('estado'                    => 'Activo', 
                                                                        'descripcionCaracteristica' => 'RESPONSABLE_NC') );
                
            $objInfoDocumentoCaracteristicaTipo = new InfoDocumentoCaracteristica();
            $objInfoDocumentoCaracteristicaTipo->setCaracteristicaId($objAdmiCaracteristicaTipoResponsable->getId());
            $objInfoDocumentoCaracteristicaTipo->setDocumentoId($entityInfoDocumentoFinancieroCab);
            $objInfoDocumentoCaracteristicaTipo->setEstado('Activo');
            $objInfoDocumentoCaracteristicaTipo->setFeCreacion(new \DateTime('now'));
            $objInfoDocumentoCaracteristicaTipo->setIpCreacion($strIpCreacion);
            $objInfoDocumentoCaracteristicaTipo->setUsrCreacion($strUser);
            $objInfoDocumentoCaracteristicaTipo->setValor($strTipoResponsable);
            $this->emfinan->persist($objInfoDocumentoCaracteristicaTipo);
            $this->emfinan->flush();
            
            $objInfoDocumentoCaracteristicaResponsable = new InfoDocumentoCaracteristica();
            $objInfoDocumentoCaracteristicaResponsable->setCaracteristicaId($objAdmiCaracteristicaResponsable->getId());
            $objInfoDocumentoCaracteristicaResponsable->setDocumentoId($entityInfoDocumentoFinancieroCab);
            $objInfoDocumentoCaracteristicaResponsable->setEstado('Activo');
            $objInfoDocumentoCaracteristicaResponsable->setFeCreacion(new \DateTime('now'));
            $objInfoDocumentoCaracteristicaResponsable->setIpCreacion($strIpCreacion);
            $objInfoDocumentoCaracteristicaResponsable->setUsrCreacion($strUser);
            
            if( $strTipoResponsable == 'Cliente' )
            {
                $objInfoDocumentoCaracteristicaResponsable->setValor($strClienteResponsable);
            }
            else
            {
                $objInfoDocumentoCaracteristicaResponsable->setValor($strEmpresaResponsable);
            }
            
            $this->emfinan->persist($objInfoDocumentoCaracteristicaResponsable);
            $this->emfinan->flush();
        }//( $strTipoResponsable == 'Cliente' || $strTipoResponsable == 'Empresa' )
        
        if($intEditValoresNcCaract > 0 )
        {
            $objAdmiCaracteristicaEditNc = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                     ->findOneBy( array('estado'                    => 'Activo', 
                                                                        'descripcionCaracteristica' => 'EDICION_VALORES_NC') );
            $objInfoDocumentoCaracteristicaEditNc = new InfoDocumentoCaracteristica();
            $objInfoDocumentoCaracteristicaEditNc->setCaracteristicaId($objAdmiCaracteristicaEditNc->getId());
            $objInfoDocumentoCaracteristicaEditNc->setDocumentoId($entityInfoDocumentoFinancieroCab);
            $objInfoDocumentoCaracteristicaEditNc->setEstado('Activo');
            $objInfoDocumentoCaracteristicaEditNc->setFeCreacion(new \DateTime('now'));
            $objInfoDocumentoCaracteristicaEditNc->setIpCreacion($strIpCreacion);
            $objInfoDocumentoCaracteristicaEditNc->setUsrCreacion($strUser);
            $objInfoDocumentoCaracteristicaEditNc->setValor("EDICION_VALORES_NC");
            $this->emfinan->persist($objInfoDocumentoCaracteristicaEditNc);
            $this->emfinan->flush();
        }
        
        if( !empty($strDescripcionInterna) )
        {
            $objAdmiCaracteristicaDescripcionInterna = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy( array( 'estado'                    => 'Activo', 
                                                                                'descripcionCaracteristica' => 'DESCRIPCION_INTERNA_NC') );
                
            $objInfoDocumentoCaracteristica = new InfoDocumentoCaracteristica();
            $objInfoDocumentoCaracteristica->setCaracteristicaId($objAdmiCaracteristicaDescripcionInterna->getId());
            $objInfoDocumentoCaracteristica->setDocumentoId($entityInfoDocumentoFinancieroCab);
            $objInfoDocumentoCaracteristica->setEstado('Activo');
            $objInfoDocumentoCaracteristica->setFeCreacion(new \DateTime('now'));
            $objInfoDocumentoCaracteristica->setIpCreacion($strIpCreacion);
            $objInfoDocumentoCaracteristica->setUsrCreacion($strUser);
            $objInfoDocumentoCaracteristica->setValor(trim($strDescripcionInterna));
            $this->emfinan->persist($objInfoDocumentoCaracteristica);
            $this->emfinan->flush();
        }//( !empty(trim($strDescripcionInterna)) )
        

        if($entityInfoDocumentoFinancieroCab)
        {
            $entityInfoDocumentoHistorial = new InfoDocumentoHistorial();
            $entityInfoDocumentoHistorial->setDocumentoId($entityInfoDocumentoFinancieroCab);
            $entityInfoDocumentoHistorial->setFeCreacion(new \DateTime('now'));
            $entityInfoDocumentoHistorial->setUsrCreacion($strUser);
            $entityInfoDocumentoHistorial->setEstado($strEstado);
            $entityInfoDocumentoHistorial->setObservacion($strTipoNotaCredito);
            $this->emfinan->persist($entityInfoDocumentoHistorial);
            $this->emfinan->flush();
        }

        if($objInformacionGrid)
        {
            $entityAdmiImpuestoIva = $this->emgene->getRepository('schemaBundle:AdmiImpuesto')
                                          ->findOneBy( array('tipoImpuesto' => $this->tipoImpuesto, 'estado' => 'Activo') );

            $intSumTotalImpuesto           = 0;
            $intSumSubtotal                = 0;
            $intSumDescuento               = 0;
            $floatSumCompensacionSolidaria = 0;

            foreach($objInformacionGrid as $objInfoGrid)
            {
                $intImpuesto    = 0;
                $intSubtotal    = 0;
                $intImpuestoIce = 0;
                
                $floatSumCompensacionSolidaria += ($objInfoGrid->floatCompensacionSolidaria ? $objInfoGrid->floatCompensacionSolidaria : 0);

                $entityInfoDocumentoFinancieroDet = new InfoDocumentoFinancieroDet();
                $entityInfoDocumentoFinancieroDet->setDocumentoId($entityInfoDocumentoFinancieroCab);
                $entityInfoDocumentoFinancieroDet->setPuntoId($objInfoGrid->intPuntoId);
                if($objInfoGrid->idServicio > 0)
                {
                    $entityInfoDocumentoFinancieroDet->setServicioId($objInfoGrid->idServicio);
                }
                $entityInfoDocumentoFinancieroDet->setCantidad($objInfoGrid->cantidad);
                $entityInfoDocumentoFinancieroDet->setEmpresaId($intIdEmpresa);
                $entityInfoDocumentoFinancieroDet->setOficinaId($intIdOficina);
                $entityInfoDocumentoFinancieroDet->setPrecioVentaFacproDetalle(round($objInfoGrid->valor, 2));
                $entityInfoDocumentoFinancieroDet->setDescuentoFacproDetalle(round($objInfoGrid->descuento, 2));
                $entityInfoDocumentoFinancieroDet->setFeCreacion(new \DateTime('now'));
                $entityInfoDocumentoFinancieroDet->setUsrCreacion($strUser);
                $entityInfoDocumentoFinancieroDet->setMotivoId($arrayMotivo[0]);
                $this->emfinan->persist($entityInfoDocumentoFinancieroDet);
                $this->emfinan->flush();
                
                    
                $intPrecioNuevo        = ($objInfoGrid->valor * $objInfoGrid->cantidad) - $objInfoGrid->descuento;
                $intPrecioSinDescuento = ($objInfoGrid->valor * $objInfoGrid->cantidad);
                $intSubtotal           += $intPrecioNuevo;

                if($objInfoGrid->tipo == 'PR')
                {
                    /* Cuando es producto voy a la tabla AdmiProducto para sacar los impuestos */
                    $entityInfoDocumentoFinancieroDet->setProductoId($objInfoGrid->codigo);
                    
                    $intIdDetalleFactura = $objInfoGrid->intIdDetalleFactura;
                    
                    /*
                     * Se calcula el valor de los impuestos de prioridad 1
                     */
                    $arrayParametrosImpuestos  = array( 'boolDocumentoFinancieroImp' => 'S',
                                                        'intDetalleDocId'            => $intIdDetalleFactura,
                                                        'intPrioridad'               => 1 );
                    $arrayImpuestosResultados  = $this->emfinan->getRepository('schemaBundle:AdmiImpuesto')
                                                               ->getImpuestosByCriterios( $arrayParametrosImpuestos );
                    $arrayImpuestosPrioridad1  = $arrayImpuestosResultados['registros'];

                    if( $arrayImpuestosPrioridad1 )
                    {
                        foreach($arrayImpuestosPrioridad1 as $objAdmiImpuesto)
                        {
                            $intTmpImpuesto = 0;
                            
                            if( $objInfoGrid->tipoNC == "ValorOriginal" )
                            {
                                if($objAdmiImpuesto->getTipoImpuesto() == 'ICE')
                                {
                                    $intTmpImpuesto += $objInfoGrid->impuestoIce;
                                }
                                else
                                {
                                    $intTmpImpuesto += $objInfoGrid->impuesto;
                                }
                            }
                            else
                            {
                                $intTmpImpuesto = (($intPrecioNuevo * $objAdmiImpuesto->getPorcentajeImpuesto())/100);
                            }
                            
                            if($objAdmiImpuesto->getTipoImpuesto() == 'ICE')
                            {
                                $boolDocumentoTieneIce = true;
                                $intImpuestoIce        += $intTmpImpuesto;
                            }
                            
                            $intImpuesto += $intTmpImpuesto;

                            if( floatval($intTmpImpuesto) > 0 )
                            {
                                //Registro del impuesto
                                $entityInfoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                $entityInfoDocumentoFinancieroImp->setDetalleDocId($entityInfoDocumentoFinancieroDet->getId());
                                $entityInfoDocumentoFinancieroImp->setImpuestoId($objAdmiImpuesto->getId());
                                $entityInfoDocumentoFinancieroImp->setValorImpuesto($intTmpImpuesto);
                                $entityInfoDocumentoFinancieroImp->setPorcentaje($objAdmiImpuesto->getPorcentajeImpuesto());
                                $entityInfoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                $entityInfoDocumentoFinancieroImp->setUsrCreacion($strUser);
                                $this->emfinan->persist($entityInfoDocumentoFinancieroImp);
                                $this->emfinan->flush();
                            }//( floatval($intTmpImpuesto) > 0 )
                        }//foreach($arrayImpuestosPrioridad1 as $objAdmiImpuesto)
                    }//( $arrayImpuestosPrioridad1 )
                    /*
                     * Fin Se calcula el valor de los impuestos de prioridad 1
                     */
                    
                    /*
                     * Se calcula el valor de los impuestos de prioridad 2
                     */
                    $arrayParametrosImpuestos['intPrioridad'] = 2;
                    $arrayImpuestosResultados                 = $this->emfinan->getRepository('schemaBundle:AdmiImpuesto')
                                                                     ->getImpuestosByCriterios( $arrayParametrosImpuestos );
                    $arrayImpuestosPrioridad2  = $arrayImpuestosResultados['registros'];

                    if( $arrayImpuestosPrioridad2 )
                    {
                        foreach($arrayImpuestosPrioridad2 as $objAdmiImpuesto)
                        {
                            $intTmpImpuesto = 0;
                            
                            if( $objInfoGrid->tipoNC == "ValorOriginal" )
                            {
                                if($objAdmiImpuesto->getTipoImpuesto() == 'IVA')
                                {
                                    $intTmpImpuesto += $objInfoGrid->impuestoIva;
                                }
                                else
                                {
                                    $intTmpImpuesto += $objInfoGrid->impuesto;
                                }
                            }
                            else
                            {
                                $intTmpImpuesto = ((($intPrecioNuevo + $intImpuestoIce) * $objAdmiImpuesto->getPorcentajeImpuesto())/100);
                            }
                            
                            $intImpuesto += $intTmpImpuesto;
                            
                            if( floatval($intTmpImpuesto) > 0 )
                            {
                                //Registro del impuesto
                                $entityInfoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                $entityInfoDocumentoFinancieroImp->setDetalleDocId($entityInfoDocumentoFinancieroDet->getId());
                                $entityInfoDocumentoFinancieroImp->setImpuestoId($objAdmiImpuesto->getId());
                                $entityInfoDocumentoFinancieroImp->setValorImpuesto($intTmpImpuesto);
                                $entityInfoDocumentoFinancieroImp->setPorcentaje($objAdmiImpuesto->getPorcentajeImpuesto());
                                $entityInfoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                $entityInfoDocumentoFinancieroImp->setUsrCreacion($strUser);
                                $this->emfinan->persist($entityInfoDocumentoFinancieroImp);
                                $this->emfinan->flush();
                            }//( floatval($intTmpImpuesto) > 0 )
                        }//foreach($arrayImpuestosPrioridad2 as $objAdmiImpuesto)
                    }//( $arrayImpuestosPrioridad2 )
                    /*
                     * Fin Se calcula el valor de los impuestos de prioridad 2
                     */
                }
                else
                {
                    /* Cuando es plan voy a la tabla InfoPlanCab para sacar la bandera del impuesto */
                    $entityInfoDocumentoFinancieroDet->setPlanId($objInfoGrid->codigo);
                    
                    $entityInfoPlan = $this->emcom->getRepository('schemaBundle:InfoPlanCab')->find($objInfoGrid->codigo);

                    if($entityInfoPlan)
                    {
                        if($entityInfoPlan->getIva() == 'S')
                        {
                            if($entityAdmiImpuestoIva)
                            {
                                $intIdDetalleFactura   = $objInfoGrid->intIdDetalleFactura;
                                $intIdImpuestoAplicado = $entityAdmiImpuestoIva->getId();
                                   
                                $objInfoDocumentoFinancieroImp = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                      ->findOneByDetalleDocId( $intIdDetalleFactura );

                                if( $objInfoDocumentoFinancieroImp )
                                {
                                    $intIdImpuestoAplicado = $objInfoDocumentoFinancieroImp->getImpuestoId();
                                    $entityAdmiImpuestoIva->setPorcentajeImpuesto($objInfoDocumentoFinancieroImp->getPorcentaje());
                                }
                                 
                                if( $objInfoGrid->tipoNC == "ValorOriginal" )
                                {
                                    $intImpuesto += $objInfoGrid->impuesto;
                                }
                                else
                                {
                                    $intImpuesto += (($intPrecioNuevo * $entityAdmiImpuestoIva->getPorcentajeImpuesto()) / 100);
                                }
                                
                                if( floatval($intImpuesto) > 0 )
                                {
                                    //Registro del impuesto
                                    $entityInfoDocumentoFinancieroImp = new InfoDocumentoFinancieroImp();
                                    $entityInfoDocumentoFinancieroImp->setDetalleDocId($entityInfoDocumentoFinancieroDet->getId());
                                    $entityInfoDocumentoFinancieroImp->setImpuestoId($intIdImpuestoAplicado);
                                    $entityInfoDocumentoFinancieroImp->setValorImpuesto($intImpuesto);
                                    $entityInfoDocumentoFinancieroImp->setPorcentaje($entityAdmiImpuestoIva->getPorcentajeImpuesto());
                                    $entityInfoDocumentoFinancieroImp->setFeCreacion(new \DateTime('now'));
                                    $entityInfoDocumentoFinancieroImp->setUsrCreacion($strUser);
                                    $this->emfinan->persist($entityInfoDocumentoFinancieroImp);
                                    $this->emfinan->flush();
                                }//( floatval($intImpuesto) > 0 )
                            }//($entityAdmiImpuestoIva)
                        }//($entityInfoPlan->getIva() == 'S')
                    }//($entityInfoPlan)
                }//($objInfoGrid->tipo == 'PR')

                $intSumTotalImpuesto +=  $intImpuesto;
                $intSumSubtotal      +=  $intPrecioSinDescuento;
                $intSumDescuento     +=  $objInfoGrid->descuento;
            }
        }
        
        $entityInfoDocumentoFinancieroCabAct = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                    ->find($entityInfoDocumentoFinancieroCab->getId());
        $entityInfoDocumentoFinancieroCabAct->setSubtotal(round($intSumSubtotal,2));
        $entityInfoDocumentoFinancieroCabAct->setSubtotalConImpuesto(round($intSumTotalImpuesto,2));
        $entityInfoDocumentoFinancieroCabAct->setSubtotalDescuento(round($intSumDescuento,2));
        $entityInfoDocumentoFinancieroCabAct->setDescuentoCompensacion(round($floatSumCompensacionSolidaria,2));

        $intValorTotal = round($intSumSubtotal, 2) - round($intSumDescuento, 2) + round($intSumTotalImpuesto, 2) 
                         - round($floatSumCompensacionSolidaria, 2);
        $entityInfoDocumentoFinancieroCabAct->setValorTotal(round($intValorTotal, 2));
        
        $this->emfinan->persist($entityInfoDocumentoFinancieroCabAct);
        $this->emfinan->flush();
        
        /**
         * Bloque validador de documento
         * 
         * Verifica si se debe regularizar el documento que contiene al menos un detalle con impuesto de ICE
         */
        if( is_object($entityInfoDocumentoFinancieroCab) && $boolDocumentoTieneIce )
        {
            $arrayParametrosValidador = array('intIdDocumento' => $entityInfoDocumentoFinancieroCab->getId());
            $arrayRespuestaValidador  = $this->emfinan->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                      ->getValidadorDocumentosFinancieros($arrayParametrosValidador);

            if( !empty($arrayRespuestaValidador) )
            {
                $strBanderaValidador = ( isset($arrayRespuestaValidador['strValidador']) && !empty($arrayRespuestaValidador['strValidador']) ) 
                                        ? $arrayRespuestaValidador['strValidador'] : 'N';

                if( $strBanderaValidador == "S" )
                {
                    $floatDiferenciaImpuestos = ( isset($arrayRespuestaValidador['floatDiferenciaImpuestos']) 
                                                  && !empty($arrayRespuestaValidador['floatDiferenciaImpuestos']) ) 
                                                 ? $arrayRespuestaValidador['floatDiferenciaImpuestos'] : 0;

                    if( floatval($floatDiferenciaImpuestos) > 0 )
                    {
                        $objDocumentoActualizar = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
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
                            $this->emfinan->persist($objDocumentoActualizar);
                            $this->emfinan->flush();
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

        return $entityInfoDocumentoFinancieroCabAct;
    }
    /**
     * Funcion que realiza el proceso de reverso de la factura de contrato digital, se debe validar que se genere NC 
     * de Reverso solo si no existe ya asociada una NC Activa.
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 05-12-2018
     * 
     * @param  Array $arrayParametros 
     *                               [
     *                                  strPrefijoEmpresa       => prefijo empresa,
     *                                  strEmpresaCod           => Codigo empresa,
     *                                  strUsrCreacion          => Usuario Creación
     *                                  strIpCreacion           => Ip de Creacion     
     *                                  objInfoPunto            => Objeto de Punto
     *                                  objInfoServicio         => Objeto del Servicio
     *                               ]
     * @throws \Exception
     * @return string $strMensajeError
     * 
     * @author Madeline Haz <mhaz@telconet.ec>
     * @version 1.1 11-03-2019
     * Uso de $strUsrCreacion en $arrayParametrosNc, array que contiene la información que genera la NC.
     * Se insertan parámetro con estados Activo, Pendiente y Aprobada para validar La creación de la NC.  
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 1.2 15-10-2020 - Se agrega nuevo parámetro 'intEditValoresNcCaract' al $arrayParametrosNc para el proceso de 
     *                           generar nota de crédito.   
     * 
     */
    public function generarReversoFacturasContratoFisicoDigital($arrayParametros)
    {        
        $strPrefijoEmpresa       = $arrayParametros['strPrefijoEmpresa'];        
        $strEmpresaCod           = $arrayParametros['strEmpresaCod'];
        $strUsrCreacion          = $arrayParametros['strUsrCreacion'];
        $strIpCreacion           = $arrayParametros['strIpCreacion'];        
        $strMotivo               = $arrayParametros['strMotivo'];
        $objInfoPunto            = $arrayParametros['objInfoPunto'];
        $objInfoServicio         = $arrayParametros['objInfoServicio'];              
        
        $strMensajeError         = "";      
        $strMensajeErrorUsuario  = null;
        try 
        {                       

            if(!is_object($objInfoServicio))
            { 
                throw new \Exception('No encontro el Servicio que se desea Eliminar');
            }           
            if(!is_object($objInfoPunto))
            { 
                throw new \Exception('No encontro el Punto al cual pertenece el servicio que desea eliminar');
            }          
        
            $objMotivoRechazo = $this->emgene->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivo);
            if(!is_object($objMotivoRechazo))
            { 
                throw new \Exception('No se encontro el Motivo de Rechazo');
            }    
            $intTmpMotivoRechazoId = $objMotivoRechazo ? $objMotivoRechazo->getId() : 0;
          
             //Se obtienen las facturas generadas por contrato asociadas al punto y al servicio para posteriormente aplicarles NC.
            $arrayRespuesta = $this->serviceDocFinancieroCab->obtieneFacturasAGenerarNCxEliminarOS(array("strEmpresaCod" => $strEmpresaCod,
                                                                                                   "intIdPunto"    => $objInfoPunto->getId(),
                                                                                                   "intIdServicio" => $objInfoServicio->getId()));
            if ("OK" != $arrayRespuesta["strEstado"])
            {
                throw new \Exception($arrayRespuesta["strMensaje"]);
            }
            /* Si existen Facturas de Contrato, realiza el reverso generando NC siempre y cuando la factura no tenga asociada una NC. */
            $arrayFacturas = $arrayRespuesta["arrayListFacturas"];
            // Parámetro Valida los estados  Activo,Pendiente,Aprobada de la Nota de Crédito
            $objParametroCab = $this->emgene->getRepository('schemaBundle:AdmiParametroCab')
                                            ->findOneBy(array("nombreParametro" => "ESTADOS_NOTA_CREDITO",
                                                              "estado"          => "Activo"));
            
            if (is_object($objParametroCab))
            {
                $arrayParametroDet = $this->emgene->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->findBy(array ("parametroId" => $objParametroCab->getId(),
                                                                  "estado"      => "Activo" ));                                                             
                if ($arrayParametroDet)
                { 
                    foreach( $arrayParametroDet as $parametroDet)
                    {
                        $arrayCaracteristicas[] = $parametroDet->getValor2();
                    }
                }
            }
            /* Si existen Facturas de Contrato Digital realiza el reverso generando NC, solo de no existir ya Aplicada una NC. */
            if(!empty($arrayFacturas))
            {
                foreach($arrayFacturas as $objInfoDocumentoFinancieroCab)
                {
                    //En caso que se tenga una factura en estado pendiente, se debe esperar a su autorización en el SRI.
                    if ("Pendiente" == $objInfoDocumentoFinancieroCab->getEstadoImpresionFact())
                    {
                        $strMensajeErrorUsuario = "El punto tiene una factura por autorizarse, favor intentar nuevamente en un momento.";
                        throw new \Exception ($strMensajeErrorUsuario . " | idDocumento:" . $objInfoDocumentoFinancieroCab->getId() .
                                              " | puntoId:" . $objInfoPunto->getId() . " | servicioId:" . $objInfoServicio->getId());
                    }                   

                    $objNotaCreditoActivas = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                           ->getNotasDeCreditoActivas(array(
                                                                                     'intIdDocumento' => $objInfoDocumentoFinancieroCab->getId(),
                                                                                     'arrayInEstados' => $arrayCaracteristicas));

                    
                    if( (empty($objNotaCreditoActivas) || !isset($objNotaCreditoActivas)) &&
                        ($objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Activo' || 
                         $objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Cerrado'))
                    {                        
                        $arrayInformacionGrid                         = array();
                        $arrayParametrosDet                           = array();
                        $arrayParametrosDet["idFactura"]              = $objInfoDocumentoFinancieroCab->getId();
                        $arrayParametrosDet["tipo"]                   = "VO";
                        $arrayParametrosDet["fechaDesde"]             = null;
                        $arrayParametrosDet["fechaHasta"]             = null;
                        $arrayParametrosDet["porcentaje"]             = null;
                        $arrayParametrosDet["strPagaIva"]             = null;
                        $arrayParametrosDet["boolWithoutValues"]      = 'N';
                        $arrayParametrosDet["jsonListadoInformacion"] = null;

                        $arrayTmpDetallesNc = $this->generarDetallesNotaDeCredito($arrayParametrosDet);

                        if(!empty($arrayTmpDetallesNc))
                        {
                            foreach($arrayTmpDetallesNc as $arrayItem)
                            {
                                $arrayItem["tipoNC"]    = "ValorOriginal";
                                $objItem                = (object) $arrayItem;
                                $arrayInformacionGrid[] = $objItem;
                            }//foreach($arrayTmpDetallesNc as $arrayItem)
                        }//( !empty($arrayTmpDetallesNc) )

                        if(!empty($arrayInformacionGrid))
                        {
                            $intTmpIdOficina = $objInfoDocumentoFinancieroCab->getOficinaId();
                            $arrayParametrosNc                          = array();
                            $arrayParametrosNc["estado"]                = "Aprobada";
                            $arrayParametrosNc["codigo"]                = "NC";
                            $arrayParametrosNc["informacionGrid"]       = $arrayInformacionGrid;
                            $arrayParametrosNc["punto_id"]              = $objInfoPunto->getId();
                            $arrayParametrosNc["oficina_id"]            = $intTmpIdOficina;
                            $arrayParametrosNc["observacion"]           = $strMotivo;
                            $arrayParametrosNc["facturaId"]             = $objInfoDocumentoFinancieroCab->getId();                            
                            $arrayParametrosNc["user"]                  = $strUsrCreacion;
                            $arrayParametrosNc["motivo_id"]             = array($intTmpMotivoRechazoId);
                            $arrayParametrosNc["intIdEmpresa"]          = $strEmpresaCod;
                            $arrayParametrosNc["strEselectronica"]      = 'S';
                            $arrayParametrosNc["strPrefijoEmpresa"]     = $strPrefijoEmpresa;
                            $arrayParametrosNc["strPagaIva"]            = '';
                            $arrayParametrosNc["strTipoResponsable"]    = '';
                            $arrayParametrosNc["strClienteResponsable"] = '';
                            $arrayParametrosNc["strEmpresaResponsable"] = '';
                            $arrayParametrosNc["strIpCreacion"]         = $strIpCreacion;
                            $arrayParametrosNc["strDescripcionInterna"] = '';
                            $arrayParametrosNc["strTipoNotaCredito"]    = 'El tipo de la nota de crédito es Valor Original';
                            $arrayParametrosNc["intEditValoresNcCaract"] = 0;
                            $objNotaDeCredito = $this->generarNotaDeCredito($arrayParametrosNc);
                            if($objNotaDeCredito && $objNotaDeCredito->getId() > 0)
                            {                              
                                //Obtiene los datos de numeracion
                                $objNumeracion = $this->emfinan->getRepository('schemaBundle:AdmiNumeracion')
                                                               ->findOficinaMatrizYFacturacion($strEmpresaCod, 'NCE');
                                if(!is_object($objNumeracion))
                                {
                                    throw new \Exception('No encontro Numeracion para generar la Nota de Credito');
                                }
                                $strSecuencia = str_pad($objNumeracion->getSecuencia(), 9, "0", STR_PAD_LEFT);
                                //Genera el numero de NC
                                $strNumeroFacturaSri = $objNumeracion->getNumeracionUno() . "-" .
                                $objNumeracion->getNumeracionDos() . "-" . $strSecuencia;
                                //Actualiza el numero de NC del SRI
                                $objNotaDeCredito->setNumeroFacturaSri($strNumeroFacturaSri);
                                $this->emfinan->persist($objNotaDeCredito);
                                $this->emfinan->flush();
                                //Actualizo la secuencia de la Numeracion de la NC
                                $strSecuenciaNumeracion = ($objNumeracion->getSecuencia() + 1);
                                $objNumeracion->setSecuencia($strSecuenciaNumeracion);
                                $this->emfinan->persist($objNumeracion);
                                $this->emfinan->flush();                                                              
                            }                            
                        }//( !empty($arrayInformacionGrid) )                                                
                    }
                    else
                    {
                        foreach($objNotaCreditoActivas as $objNotaCreditoActivase)
                        {                            
                            $entityServicioHistorial = new InfoServicioHistorial();          
                            $entityServicioHistorial->setServicioId($objInfoServicio);                        
                            $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $entityServicioHistorial->setIpCreacion($strIpCreacion);
                            $entityServicioHistorial->setObservacion('Se intentó crear NC automática por valor original,<br> 
                                                                      pero existe una Nota de Crédito manual en estado: '.
                                                                      $objNotaCreditoActivase->getEstadoImpresionFact());
                            $entityServicioHistorial->setEstado("Activo");
                            $this->emcom->persist($entityServicioHistorial);
                            $this->emcom->flush();
                            
                            //NOTIFICACIÓN DEL INTENTO DE CREACIÓN DE NC                                                     
                                 
                            $arrayEmpresaCod = array($strEmpresaCod); 
                            
                            $this->envioPlantilla->generarEnvioPlantilla(  'NOTIFICACION NOTA DE CREDITO',
                                                                           null,
                                                                           'NOTIFICA_NC', 
                                                                           array("strUsrCreacion" => $strUsrCreacion,
                                                                                 "strNumFactura"  => $objInfoDocumentoFinancieroCab->getNumeroFacturaSri(),
                                                                                 "strEstadoNC"    => $objNotaCreditoActivase->getEstadoImpresionFact()
                                                                           ),
                                                                           $arrayEmpresaCod,
                                                                           '',
                                                                           '',
                                                                           null,
                                                                           false
                                                                        );                                                                                                                               
                        }/* ( (empty($objNotaCreditoActivas) || !isset($objNotaCreditoActivas)) &&
                          * ($objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Activo'
                         || $objInfoDocumentoFinancieroCab->getEstadoImpresionFact() == 'Cerrado') ) */     
                    } //Else  
                }//foreach($arrayFacturas as $objInfoDocumentoFinancieroCab)
            }//( !empty($arrayFacturas) )     
        }
        catch(\Exception $e)
        {            
            $strMensajeError = "No se generó Reverso de la Factura de contrato Digital
                                <br> Favor notificar a Sistemas";
            $this->utilService->insertError('Telcos+', 
                                            'generarReversoFacturasContratoDigital', 
                                            'No se genero Reverso de la Factura de contrato Digital - '.$e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
            );
        }
        return $strMensajeError;
    }

    /**
      * Documentación de método 'generarDetallesNotaDeCredito'
      * 
      * Genera los detalles para la nota de credito
      * 
      * @param   parametros        Variables del controlador
      * @return  detalle_orden_l   Retorna los detalles de las nota de credito o nota de credito interna creada
      * 
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.0 23-05-2016
      * 
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.1 01-06-2016 - Se modifica para agregar la opción de 'Valor por detalle'
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.2 10-08-2016 - Se debe calcular por el valor total la NC a los productos o planes que no son prorreateables. 
      *                           Para ello se corrige durante el loop que siempre se setee en 'S' al inicio de cada recorrido  la variable 
      *                           '$strProrratea'
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.3 15-11-2016 - Se envía el parámetro 'consultaPor' a la función 'getValoresAcumulados' para poder obtener los impuestos
      *                           correspondientes del valor original de la factura a la cual se le aplicar la NC
      * @author Edson Franco <efranco@telconet.ec>
      * @version 1.4 01-12-2016 - Se obtienen los parámetros de 'strPrefijoEmpresa' y 'strEsCompensado' para ser enviados a la función 
      *                          'getValoresAcumulados' para poder calcular el valor de compensación solidaria de la NC para la empresa TN
      *
      * @author Ricardo Coello Quezada <rcoello@telconet.ec>
      * @version 1.5 27-07-2017 - Se modifica arreglo $arrayValorDetallesNc y $arrayValorDescuentoDetalleNc agregandole nuevo indice para segregar
      *                           cada items de la nc, con la finalidad de que cada elemento del array corresponda a cada detalle de la NC.
      * 
      * @author Katherine Yager <kyager@telconet.ec>
      * @version 1.6 15-03-2019 - Se modifica condición para que cuando nombre técnico sea otro se realice prorrateo en Guatemala..
      */
    function generarDetallesNotaDeCredito($parametros)
    {
        $arrayDetalles                  = array();
        $arrayDetallesNc                = array();
        $arrayDetalleDescuentoNc        = array();
        $intIdFactura                   = $parametros["idFactura"];
        $strTipo                        = $parametros["tipo"];
        $strFechaDesde                  = $parametros["fechaDesde"];
        $strFechaHasta                  = $parametros["fechaHasta"];
        $intPorcentaje                  = $parametros["porcentaje"];
        $strPagaIva                     = $parametros["strPagaIva"];
        $boolWithoutValues              = $parametros["boolWithoutValues"] ? $parametros["boolWithoutValues"] : 'N';
        $jsonListadoInformacion         = $parametros["jsonListadoInformacion"];
        $strPrefijoEmpresa              = isset($parametros["strPrefijoEmpresa"]) ? $parametros["strPrefijoEmpresa"] : '';
        $strEsCompensado                = isset($parametros["strEsCompensado"]) ? $parametros["strEsCompensado"] : 'N';
        $strEmpresaCod                  = isset($parametros["strEmpresaCod"]) ? $parametros["strEmpresaCod"] : 'N';
        $arrayValorDetallesNc           = array();
        $arrayValorDescuentoDetalleNc   = array();
        $arrayIndicesValorNc            = array();
        $arrayResultados                = array();
        $intContadorDetallesNC          = 1;
        $strAlerta                      = "N";
        $strMensaje                     = "";
        
        $arrayDetalleFactura   = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                               ->getDetallesDelDocumento(array('intIdDocumento' => $intIdFactura));
                                                                                        
		$strProrratea          = "S";
        $objNotaCreditoActivas = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                               ->getNotasDeCreditoActivas(array('intIdDocumento' => $intIdFactura,
                                                                                'arrayInEstados' => array('Activo')));
        if($objNotaCreditoActivas)
        {
            foreach($objNotaCreditoActivas as $objNotaCreditoActiva)
            {
                $objDetallesNc = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                               ->getDetallesDelDocumento(array('intIdDocumento' => $objNotaCreditoActiva->getId()));
                
                if($objDetallesNc)
                {
                    foreach($objDetallesNc as $objDetalleNc)
                    {
                        if($objDetalleNc->getProductoId())
                        {
                            //Recupero el precio del item PR para la NC - Producto
                            $floatTmpValor = round($objDetalleNc->getPrecioVentaFacproDetalle(), 2);
                            
                            $arrayValorDetallesNc[$objDetalleNc->getPuntoId()]["PR"][$objDetalleNc->getProductoId()][$intContadorDetallesNC] = 
                                                                                                                                      $floatTmpValor;
                            //Recupero el descuento del item PR de la NC.
                            $floatTmpValorDescuentoDetalle      = round($objDetalleNc->getDescuentoFacproDetalle(), 2);
                            
                            $floatTmpValorDescuentoDetalle      = empty($floatTmpValorDescuentoDetalle) ? 0 : $floatTmpValorDescuentoDetalle;
                            $arrayValorDescuentoDetalleNc[$objDetalleNc->getPuntoId()]["PR"][$objDetalleNc->getProductoId()][$intContadorDetallesNC]=
                                                                                                                      $floatTmpValorDescuentoDetalle;
                            $intContadorDetallesNC = $intContadorDetallesNC + 1;
                            
                        }//($objDetalleNc->getProductoId())
                            
                        if($objDetalleNc->getPlanId())
                        {
                            //Recupero el precio del item PL para la NC - Plan
                            $floatTmpValor = round($objDetalleNc->getPrecioVentaFacproDetalle(), 2);
                            
                            $arrayValorDetallesNc[$objDetalleNc->getPuntoId()]["PL"][$objDetalleNc->getPlanId()][$intContadorDetallesNC]  =
                                                                                                                                      $floatTmpValor;
                            
                            //Recupero el descuento del item PL de la NC.
                            $floatTmpValorDescuentoDetalle      = round($objDetalleNc->getDescuentoFacproDetalle(), 2);
                            
                            $floatTmpValorDescuentoDetalle      = empty($floatTmpValorDescuentoDetalle) ? 0 : $floatTmpValorDescuentoDetalle;
                            $arrayValorDescuentoDetalleNc[$objDetalleNc->getPuntoId()]["PL"][$objDetalleNc->getPlanId()][$intContadorDetallesNC] = 
                                                                                                                       $floatTmpValorDescuentoDetalle;
                            
                            $intContadorDetallesNC = $intContadorDetallesNC + 1;
                            
                        }//($objDetalleNc->getPlanId())
                    }//foreach($objDetallesNc as $objDetalleNc)
                    
                    $intContadorDetallesNC      = 1;
                    $arrayDetallesNc[]          = $arrayValorDetallesNc;
                    $arrayDetalleDescuentoNc[]  = $arrayValorDescuentoDetalleNc;
                }//($objDetallesNc)
            }//foreach($objNotaCreditoActivas as $objNotaCreditoActiva)
        }//($objNotaCreditoActivas)
        
        if( $strTipo == "PD" )
        {
            $intDiasTotales  = 0;  
			$intCantidadDias = $this->restarFechas($strFechaDesde, $strFechaHasta);
            
            /**
            * Bloque que obtiene los días del mes en curso entre las fechas de facturación
            */
            
            $arrayParametrosFechaProporcional = array('strEmpresaCod'      => $strEmpresaCod,
                                                      'strFechaActivacion' => $strFechaDesde);
            
            $arrayResultadoDiasRestantes      = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                               ->getFechasDiasPeriodo($arrayParametrosFechaProporcional);
            
            if( isset($arrayResultadoDiasRestantes['intTotalDiasMes']) && intval($arrayResultadoDiasRestantes['intTotalDiasMes']) > 0 )
            {
                $intDiasTotales = intval($arrayResultadoDiasRestantes['intTotalDiasMes']);
            }
            
			if(!$arrayDetalleFactura)
			{
                $arrayDetalles[] = array( "codigo" => "", "informacion" => "", "valor" => "", "cantidad" => "", "tipo" => "" );
			}
            else
            {
                foreach($arrayDetalleFactura as $objFacturaDet)
                { 
                    $arrayTmpAcumulacion   = array();
                    $arrayItem             = array();
                    $strProrratea          = "S";
                    
                    $arrayItem['idServicio'] = $objFacturaDet->getServicioId();
                    
                    if($objFacturaDet->getProductoId())
                    {
                        $objAdmiProducto            = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($objFacturaDet->getProductoId());
                        $arrayItem['codigo']        = $objAdmiProducto->getId();
                        $arrayItem['informacion']   = $objAdmiProducto->getDescripcionProducto();
                        $arrayItem['tipo']          = "PR";
                        $arrayItem['nombreTecnico'] = $objAdmiProducto->getNombreTecnico();
                        
                        if($objAdmiProducto->getNombreTecnico()=="OTROS" && $strPrefijoEmpresa!='TNG')
                        {
                            $strProrratea = "N";
                        }
                     }
                    
                    if($objFacturaDet->getPlanId())
                    {
                        $objInfoPlanCab           = $this->emcom->getRepository('schemaBundle:InfoPlanCab')->find($objFacturaDet->getPlanId());
                        $arrayItem['codigo']      = $objInfoPlanCab->getId();
                        $arrayItem['informacion'] = $objInfoPlanCab->getNombrePlan();
                        $arrayItem['tipo']        = "PL";
                    }
                    
                    if($arrayDetallesNc && count($arrayDetallesNc) > 0)
                    {
                        $arrayIndicesValorNc['intPuntoId']  = $objFacturaDet->getPuntoId() ;
                        $arrayIndicesValorNc['strTipo']     = $arrayItem['tipo'];
                        $arrayIndicesValorNc['intCodigo']   = $arrayItem['codigo'];
                        
                        $arrayResultados = $this->getValorNcAplicadasPorDetalleIndice($arrayDetallesNc, 
                                                                                      $arrayIndicesValorNc,
                                                                                      $objFacturaDet->getPrecioVentaFacproDetalle());
                        
                        $arrayDetallesNc          = $arrayResultados['arrayNuevoItemsNc'];
                        $floatValorTotalDetalleNc = $arrayResultados['floatAcumulador'];
                        
                        $floatValorNuevo = $objFacturaDet->getPrecioVentaFacproDetalle() - $floatValorTotalDetalleNc;
                        $objFacturaDet->setPrecioVentaFacproDetalle($floatValorNuevo);
                    }
                    
                    if ($arrayDetalleDescuentoNc && count($arrayDetalleDescuentoNc)>0)
                    {
                        $arrayIndicesValorNc['intPuntoId']  = $objFacturaDet->getPuntoId() ;
                        $arrayIndicesValorNc['strTipo']     = $arrayItem['tipo'];
                        $arrayIndicesValorNc['intCodigo']   = $arrayItem['codigo'];
                        
                        
                        $arrayResultados = $this->getValorNcAplicadasPorDetalleIndice($arrayDetalleDescuentoNc, 
                                                                                      $arrayIndicesValorNc,
                                                                                      $objFacturaDet->getPrecioVentaFacproDetalle());
                        
                        $arrayDetalleDescuentoNc            = $arrayResultados['arrayNuevoItemsNc'];
                        $floatValorTotalDetalleDescuentoNc  = $arrayResultados['floatAcumulador'];
                        
                        $floatValorNuevoDescuento           = $objFacturaDet->getDescuentoFacproDetalle() - $floatValorTotalDetalleDescuentoNc;
                        
                        $objFacturaDet->setDescuentoFacproDetalle($floatValorNuevoDescuento);
                    }
                    
                    if( $strProrratea == "N" )
                    {
                        $floatValor     = round($objFacturaDet->getPrecioVentaFacproDetalle(),2);
                        $floatDescuento = round($objFacturaDet->getDescuentoFacproDetalle(),2);
                    }
                    else
                    {   
                        $floatValor     = ((round($objFacturaDet->getPrecioVentaFacproDetalle(),2)* $intCantidadDias)/$intDiasTotales);
                        $floatDescuento = ((round($objFacturaDet->getDescuentoFacproDetalle(),2)* $intCantidadDias)/$intDiasTotales);
                    }
                    
                    $arrayItem['intIdDetalleFactura']   = $objFacturaDet->getId();
                    $arrayItem['valor']                 = round($floatValor,2);
                    $arrayItem['cantidad']              = $objFacturaDet->getCantidad();
                    $arrayItem['descuento']             = round($floatDescuento,2);
                    $arrayItem['strPagaIva']            = $strPagaIva;
                    $arrayItem['intPuntoId']            = $objFacturaDet->getPuntoId();
                    $arrayItem['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                    $arrayItem['strEsCompensado']       = $strEsCompensado;
                    $arrayItem['intIdFactura']          = $intIdFactura;
                    
                    $arrayTmpAcumulacion = $this->getValoresAcumulados($arrayItem);
                    
                    $arrayItem['subtotal']      = $arrayTmpAcumulacion['subtotal'];
                    $arrayItem['impuesto']      = $arrayTmpAcumulacion['impuesto'];
                    $arrayItem['impuestoIce']   = $arrayTmpAcumulacion['impuestoIce'];
                    $arrayItem['impuestoIva']   = $arrayTmpAcumulacion['impuestoIva'];
                    $arrayItem['impuestoOtros'] = $arrayTmpAcumulacion['impuestoOtros'];
                    
                    $arrayItem['floatCompensacionSolidaria'] = $arrayTmpAcumulacion['floatCompensacionSolidaria'];
                    
                    $arrayDetalles[] = $arrayItem;
                }//foreach($arrayDetalleFactura as $objFacturaDet)
			}//(!$arrayDetalleFactura)
        }//( $strTipo == "PD" )
        
        
        if( $strTipo == "PS" )
        {
			if(!$arrayDetalleFactura)
			{
                $arrayDetalles[] = array("codigo"=>"","informacion"=>"","valor"=>"","cantidad"=>"","tipo"=>"");
			}
            else
            {
                foreach($arrayDetalleFactura as $objFacturaDet)
                {
                    $arrayTmpAcumulacion = array();
                    $arrayItem           = array();
                    $strProrratea        = "S";
                    
                    $arrayItem['idServicio'] = $objFacturaDet->getServicioId();
                    
                    if($objFacturaDet->getProductoId())
                    {
                        $objAdmiProducto            = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($objFacturaDet->getProductoId());
                        $arrayItem['codigo']        = $objAdmiProducto->getId();
                        $arrayItem['informacion']   = $objAdmiProducto->getDescripcionProducto();
                        $arrayItem['tipo']          = "PR";
                        $arrayItem['nombreTecnico'] = $objAdmiProducto->getNombreTecnico();
                        
                        if($objAdmiProducto->getNombreTecnico()=="OTROS" && $strPrefijoEmpresa!='TNG')
                        {
                            $strProrratea = "N";
                            $strAlerta    = "S";
                            $arrayMsg = $this->emgene->getRepository('schemaBundle:AdmiParametroDet')
                                             ->get('MENSAJES_NC', 
                                                   'FINANCIERO', 
                                                   NULL, 
                                                   NULL, 
                                                   'OTROS', 
                                                   NULL, 
                                                   'PS', 
                                                   NULL);
                            $strMensaje=  $arrayMsg[0]['valor2'];                     
                        } 
                    }
                    
                    if($objFacturaDet->getPlanId())
                    {
                        $objInfoPlanCab           = $this->emcom->getRepository('schemaBundle:InfoPlanCab')->find($objFacturaDet->getPlanId());
                        $arrayItem['codigo']      = $objInfoPlanCab->getId();
                        $arrayItem['informacion'] = $objInfoPlanCab->getNombrePlan();
                        $arrayItem['tipo']        = "PL";
                    }
                    
                    if($arrayDetallesNc && count($arrayDetallesNc) > 0)
                    {
                        $arrayIndicesValorNc['intPuntoId']  = $objFacturaDet->getPuntoId() ;
                        $arrayIndicesValorNc['strTipo']     = $arrayItem['tipo'];
                        $arrayIndicesValorNc['intCodigo']   = $arrayItem['codigo'];
                        
                        
                        $arrayResultados = $this->getValorNcAplicadasPorDetalleIndice($arrayDetallesNc, 
                                                                                      $arrayIndicesValorNc,
                                                                                      $objFacturaDet->getPrecioVentaFacproDetalle());
                        
                        
                        $arrayDetallesNc          = $arrayResultados['arrayNuevoItemsNc'];
                        $floatValorTotalDetalleNc = $arrayResultados['floatAcumulador'] ;
                        
                        $floatValorNuevo          = $objFacturaDet->getPrecioVentaFacproDetalle() - $floatValorTotalDetalleNc;
                        $objFacturaDet->setPrecioVentaFacproDetalle($floatValorNuevo);
                    }
                    
                    if ($arrayDetalleDescuentoNc && count($arrayDetalleDescuentoNc)>0)
                    {
                        $arrayIndicesValorNc['intPuntoId']  = $objFacturaDet->getPuntoId() ;
                        $arrayIndicesValorNc['strTipo']     = $arrayItem['tipo'];
                        $arrayIndicesValorNc['intCodigo']   = $arrayItem['codigo'];
                        
                        $arrayResultados = $this->getValorNcAplicadasPorDetalleIndice($arrayDetalleDescuentoNc, 
                                                                                      $arrayIndicesValorNc,
                                                                                      $objFacturaDet->getDescuentoFacproDetalle());
                        
                        $arrayDetalleDescuentoNc           = $arrayResultados['arrayNuevoItemsNc'];
                        $floatValorTotalDetalleDescuentoNc = $arrayResultados['floatAcumulador'] ;
                        
                        $floatValorNuevoDescuento          = $objFacturaDet->getDescuentoFacproDetalle() - $floatValorTotalDetalleDescuentoNc;
                        
                        $objFacturaDet->setDescuentoFacproDetalle($floatValorNuevoDescuento);
                    }
                    
                    if( $strProrratea == "N" )
                    {
                        $floatValor     = round($objFacturaDet->getPrecioVentaFacproDetalle(),2);
                        $floatDescuento = round($objFacturaDet->getDescuentoFacproDetalle(),2);
                    }
                    else
                    {
                        $floatValor     = ((round($objFacturaDet->getPrecioVentaFacproDetalle(),2)* $intPorcentaje)/100);
                        $floatDescuento = ((round($objFacturaDet->getDescuentoFacproDetalle(),2)* $intPorcentaje)/100);
                    }
                    
                    $arrayItem['intIdDetalleFactura']   = $objFacturaDet->getId();
                    $arrayItem['valor']                 = round($floatValor,2);
                    $arrayItem['cantidad']              = $objFacturaDet->getCantidad();
                    $arrayItem['descuento']             = round($floatDescuento,2);
                    $arrayItem['strPagaIva']            = $strPagaIva;
                    $arrayItem['intPuntoId']            = $objFacturaDet->getPuntoId();
                    $arrayItem['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                    $arrayItem['strEsCompensado']       = $strEsCompensado;
                    $arrayItem['intIdFactura']          = $intIdFactura;
                    
                    $arrayTmpAcumulacion = $this->getValoresAcumulados($arrayItem);
                    
                    $arrayItem['subtotal']      = $arrayTmpAcumulacion['subtotal'];
                    $arrayItem['impuesto']      = $arrayTmpAcumulacion['impuesto'];
                    $arrayItem['impuestoIce']   = $arrayTmpAcumulacion['impuestoIce'];
                    $arrayItem['impuestoIva']   = $arrayTmpAcumulacion['impuestoIva'];
                    $arrayItem['impuestoOtros'] = $arrayTmpAcumulacion['impuestoOtros'];
                    
                    $arrayItem['floatCompensacionSolidaria'] = $arrayTmpAcumulacion['floatCompensacionSolidaria'];
                    $arrayItem['strAlerta']         = $strAlerta;
                    $arrayItem['strMensaje']        = $strMensaje;
                    $arrayDetalles[] = $arrayItem;
                }//foreach($arrayDetalleFactura as $objFacturaDet)
			}//(!$arrayDetalleFactura)
		}//( $strTipo == "PS" )
        

		if( $strTipo == "VO" )
		{
			if(!$arrayDetalleFactura)
			{
                $arrayDetalles[] = array("codigo"=>"","informacion"=>"","valor"=>"","cantidad"=>"","tipo"=>"");
			}
            else
            {
                foreach($arrayDetalleFactura as $objFacturaDet)
                {
                    $arrayTmpAcumulacion = array();
                    $arrayItem           = array('consultaPor' => $strTipo);
                    
                    $arrayItem['idServicio'] = $objFacturaDet->getServicioId();
                    
                    if($objFacturaDet->getProductoId())
                    {
                        $objAdmiProducto          = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($objFacturaDet->getProductoId());
                        $arrayItem['codigo']      = $objAdmiProducto->getId();
                        $arrayItem['informacion'] = $objAdmiProducto->getDescripcionProducto();
                        $arrayItem['tipo']        = "PR";
                    }
                    
                    if($objFacturaDet->getPlanId())
                    {
                        $objInfoPlanCab           = $this->emcom->getRepository('schemaBundle:InfoPlanCab')->find($objFacturaDet->getPlanId());
                        $arrayItem['codigo']      = $objInfoPlanCab->getId();
                        $arrayItem['informacion'] = $objInfoPlanCab->getNombrePlan();
                        $arrayItem['tipo']        = "PL";
                    }
                    
                    $floatValor = round($objFacturaDet->getPrecioVentaFacproDetalle(), 2);
                    
                    if( $boolWithoutValues == 'S')
                    {
                        $arrayItem['valor'] = 0;
                    }
                    else
                    {
                        $arrayItem['valor'] = $floatValor;
                    }
                        
                    $arrayItem['intIdDetalleFactura']   = $objFacturaDet->getId();
                    $arrayItem['descuento']             = $objFacturaDet->getDescuentoFacproDetalle();
                    $arrayItem['cantidad']              = $objFacturaDet->getCantidad();
                    $arrayItem['strPagaIva']            = $strPagaIva;
                    $arrayItem['intPuntoId']            = $objFacturaDet->getPuntoId();
                    $arrayItem['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                    $arrayItem['strEsCompensado']       = $strEsCompensado;
                    $arrayItem['intIdFactura']          = $intIdFactura;
                    
                    $arrayTmpAcumulacion = $this->getValoresAcumulados($arrayItem);
                    
                    $arrayItem['subtotal']      = $arrayTmpAcumulacion['subtotal'];
                    $arrayItem['impuesto']      = $arrayTmpAcumulacion['impuesto'];
                    $arrayItem['impuestoIce']   = $arrayTmpAcumulacion['impuestoIce'];
                    $arrayItem['impuestoIva']   = $arrayTmpAcumulacion['impuestoIva'];
                    $arrayItem['impuestoOtros'] = $arrayTmpAcumulacion['impuestoOtros'];
                    
                    $arrayItem['floatCompensacionSolidaria'] = $arrayTmpAcumulacion['floatCompensacionSolidaria'];
                    
                    $arrayDetalles[] = $arrayItem;
                }//foreach($arrayDetalleFactura as $objFacturaDet)
			}//(!$arrayDetalleFactura)
		}//( $strTipo == "VO" )
        
        
        if( $strTipo == "VPD" )
		{
            $objListadoInformacion = json_decode($jsonListadoInformacion);
            
            if( $objListadoInformacion )
            {
                $arrayItems = $objListadoInformacion->items;
                
                if( $arrayItems )
                {
                    foreach($arrayItems as $objFacturaDet)
                    {
                        $arrayItem = array();

                        if($objFacturaDet->tipo == "PR")
                        {
                            $objAdmiProducto          = $this->emcom->getRepository('schemaBundle:AdmiProducto')->find($objFacturaDet->codigo);
                            $arrayItem['codigo']      = $objAdmiProducto->getId();
                            $arrayItem['informacion'] = $objAdmiProducto->getDescripcionProducto();
                            $arrayItem['tipo']        = "PR";
                        }

                        if($objFacturaDet->tipo == "PL")
                        {
                            $objInfoPlanCab           = $this->emcom->getRepository('schemaBundle:InfoPlanCab')->find($objFacturaDet->codigo);
                            $arrayItem['codigo']      = $objInfoPlanCab->getId();
                            $arrayItem['informacion'] = $objInfoPlanCab->getNombrePlan();
                            $arrayItem['tipo']        = "PL";
                        }

                        $arrayItem['idServicio']            = $objFacturaDet->idServicio;
                        $arrayItem['intIdDetalleFactura']   = $objFacturaDet->intIdDetalleFactura;
                        $arrayItem['valor']                 = $objFacturaDet->valor;
                        $arrayItem['descuento']             = $objFacturaDet->descuento;
                        $arrayItem['cantidad']              = $objFacturaDet->cantidad;
                        $arrayItem['strPagaIva']            = $strPagaIva;
                        $arrayItem['intPuntoId']            = $objFacturaDet->intPuntoId;
                        $arrayItem['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                        $arrayItem['strEsCompensado']       = $strEsCompensado;
                        $arrayItem['intIdFactura']          = $intIdFactura;

                        $arrayTmpAcumulacion = $this->getValoresAcumulados($arrayItem);

                        $arrayItem['subtotal']      = $arrayTmpAcumulacion['subtotal'];
                        $arrayItem['impuesto']      = $arrayTmpAcumulacion['impuesto'];
                        $arrayItem['impuestoIce']   = $arrayTmpAcumulacion['impuestoIce'];
                        $arrayItem['impuestoIva']   = $arrayTmpAcumulacion['impuestoIva'];
                        $arrayItem['impuestoOtros'] = $arrayTmpAcumulacion['impuestoOtros'];
                        
                        $arrayItem['floatCompensacionSolidaria'] = $arrayTmpAcumulacion['floatCompensacionSolidaria'];
                        $arrayDetalles[] = $arrayItem;
                    }//foreach($arrayDetalleFactura as $objFacturaDet)
                }//(!$arrayDetalleFactura)
            }//( $objListadoInformacion )
            else
            {
                $arrayDetalles[] = array( "codigo" => "", "informacion" => "", "valor" => "", "cantidad" => "", "tipo" => "" );
            }//( !$objListadoInformacion )
		}//( $strTipo == "VPD" )

        return $arrayDetalles;
    }
    
    
    /**
     * Obtiene los valores acumulados para visualizar en la nota de crédito
     * 
     * @param array $arrayParametros  ['valor', 'cantidad', 'descuento', 'tipo']
     * @return array $arrayResultados ['impuesto', 'subtotal', 'descuento']
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 21-05-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 15-06-2016 - Se corrige que ya no salga en el log que los índices que no existen del array que se retorna. 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 28-06-2016 - Se valida si la Factura fue hecha con el impuesto del ICE para poder crear la NC correctamente.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 15-07-2016 - Se quita la validacion para verificar si el cliente paga IVA en los planes. Puesto que los planes son usados
     *                           únicamente por MD y todos los clientes pagan IVA, y esa validación está afectando a las NCI.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 15-11-2016 - Se obtiene el parámetro 'consultaPor' para poder obtener los impuestos correspondientes del valor original de la
     *                           factura a la cual se le aplicar la NC
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 01-12-2016 - Se verifica para TN si debe o no obtener la compensación solidaria de la NC. 
     *                           Adicional para MD o TN se obtiene el valor de compensación por la forma de pago de la factura.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.7 25-01-2017 - Se corrige el cálculo de compensación solidaria
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 09-02-2017 - Se elimina la validación por empresa de la compensación solidaria para que tanto MD y TN compensen al 2% las
     *                           notas de crédito aplicadas a facturas realizadas con compensación solidaria.
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.9 10-10-2018 - Se agrega que sea visible el IVA_E 14% ya que no se lo considera en la pantalla de NC en la presentacion y calculos         
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 2.0 26-12-2018 - Se realizan cambios para que se considere el impuesto ITBMS para Panama.
     * 
     */
    public function getValoresAcumulados($arrayParametros)
	{
        $arrayResultados       = array();
        $booleanValorOriginal  = ( isset($arrayParametros['consultaPor']) 
                                   ? ((!empty($arrayParametros['consultaPor']) && $arrayParametros['consultaPor'] == "VO") ? true : false ) :false);
        $intIdDetalleFactura   = $arrayParametros['intIdDetalleFactura'];
        $intImpuesto           = 0;
        $intImpuestoIva        = 0;
        $intImpuestoIce        = 0;
        $intImpuestoOtros      = 0;
        $intPrecioNuevo        = ($arrayParametros['valor'] * $arrayParametros['cantidad']) - $arrayParametros['descuento'];
        $intPrecioSinDescuento = ($arrayParametros['valor'] * $arrayParametros['cantidad']);
        $strPrefijoEmpresa     = isset($arrayParametros['strPrefijoEmpresa']) ? $arrayParametros['strPrefijoEmpresa'] : '';
        $strEsCompensado       = isset($arrayParametros['strEsCompensado']) ? $arrayParametros['strEsCompensado'] : 'N';
        $entityAdmiImpuestoIva = $this->emgene->getRepository('schemaBundle:AdmiImpuesto')
                                      ->findOneBy( array('tipoImpuesto' => $this->tipoImpuesto, 'estado' => 'Activo') );

        if($arrayParametros['tipo'] == 'PR')
        {
            /*
             * Se calcula el valor de los impuestos de prioridad 1
             */
            $arrayParametrosImpuestos  = array( 'boolDocumentoFinancieroImp' => 'S',
                                                'intDetalleDocId'            => $intIdDetalleFactura,
                                                'intPrioridad'               => 1,
                                                'booleanValorOriginal'       => $booleanValorOriginal );
            $arrayImpuestosResultados  = $this->emfinan->getRepository('schemaBundle:AdmiImpuesto')
                                                       ->getImpuestosByCriterios( $arrayParametrosImpuestos );
            $arrayImpuestosPrioridad1  = $arrayImpuestosResultados['registros'];

            if( $arrayImpuestosPrioridad1 )
            {
                foreach($arrayImpuestosPrioridad1 as $objAdmiImpuesto)
                {
                    $strTipoImpuesto = "";
                    
                    if( is_object($objAdmiImpuesto) )
                    {
                        //Validación que verifica si se debe recalcular el impuesto de la factura o si se debe tomar el valor guardado en base
                        if( $booleanValorOriginal )
                        {
                            $intValorImpuesto = $objAdmiImpuesto->getValorImpuesto() ? $objAdmiImpuesto->getValorImpuesto() : 0;
                            $intTmpImpuesto   = $intValorImpuesto;
                            
                            $intIdImpuestoTmp   = $objAdmiImpuesto->getImpuestoId() ? $objAdmiImpuesto->getImpuestoId() : 0;
                            $objTmpAdmiImpuesto = $this->emfinan->getRepository('schemaBundle:AdmiImpuesto')->findOneById($intIdImpuestoTmp);
                            
                            if( is_object($objTmpAdmiImpuesto) )
                            {
                                $strTipoImpuesto = $objTmpAdmiImpuesto->getTipoImpuesto();
                            }
                        }
                        else
                        {
                            $intPorcentajeImpuesto = $objAdmiImpuesto->getPorcentajeImpuesto() ? $objAdmiImpuesto->getPorcentajeImpuesto() : 0;
                            $intTmpImpuesto        = (($intPrecioNuevo * $intPorcentajeImpuesto)/100);
                            $strTipoImpuesto       = $objAdmiImpuesto->getTipoImpuesto();
                        }

                        $intImpuesto += $intTmpImpuesto;

                        if( $strTipoImpuesto == 'ICE' )
                        {
                            $intImpuestoIce += $intTmpImpuesto;
                        }
                        else
                        {
                            $intImpuestoOtros += $intTmpImpuesto;
                        }
                    }//( is_object($objAdmiImpuesto) )
                }//foreach($arrayImpuestosPrioridad1 as $objAdmiImpuesto)
            }//( $arrayImpuestosPrioridad1 )
            /*
             * Fin Se calcula el valor de los impuestos de prioridad 1
             */
            
            
            /*
             * Se calcula el valor de los impuestos de prioridad 2
             */
            $arrayParametrosImpuestos['intPrioridad'] = 2;
            $arrayImpuestosResultados                 = $this->emfinan->getRepository('schemaBundle:AdmiImpuesto')
                                                             ->getImpuestosByCriterios( $arrayParametrosImpuestos );
            $arrayImpuestosPrioridad2  = $arrayImpuestosResultados['registros'];

            if( $arrayImpuestosPrioridad2 )
            {
                foreach($arrayImpuestosPrioridad2 as $objAdmiImpuesto)
                {
                    $strTipoImpuesto = "";
                    
                    if( is_object($objAdmiImpuesto) )
                    {
                        //Validación que verifica si se debe recalcular el impuesto de la factura o si se debe tomar el valor guardado en base
                        if( $booleanValorOriginal )
                        {
                            $intValorImpuesto = $objAdmiImpuesto->getValorImpuesto() ? $objAdmiImpuesto->getValorImpuesto() : 0;
                            $intTmpImpuesto   = $intValorImpuesto;
                            
                            $intIdImpuestoTmp   = $objAdmiImpuesto->getImpuestoId() ? $objAdmiImpuesto->getImpuestoId() : 0;
                            $objTmpAdmiImpuesto = $this->emfinan->getRepository('schemaBundle:AdmiImpuesto')->findOneById($intIdImpuestoTmp);
                            
                            if( is_object($objTmpAdmiImpuesto) )
                            {
                                $strTipoImpuesto = $objTmpAdmiImpuesto->getTipoImpuesto();
                            }//( is_object($objTmpAdmiImpuesto) )
                        }//( $booleanValorOriginal )
                        else
                        {
                            $intPorcentajeImpuesto = $objAdmiImpuesto->getPorcentajeImpuesto() ? $objAdmiImpuesto->getPorcentajeImpuesto() : 0;         
                            $intTmpImpuesto        = ((($intPrecioNuevo + $intImpuestoIce) * $intPorcentajeImpuesto)/100);
                            $strTipoImpuesto       = $objAdmiImpuesto->getTipoImpuesto();
                        }
                    
                        $intImpuesto += $intTmpImpuesto;

                        if( $strTipoImpuesto == 'IVA'  ||$strTipoImpuesto == 'IVA_GT'  || $strTipoImpuesto == 'IVA_E' || $strTipoImpuesto =='ITBMS')
                        {
                            $intImpuestoIva += $intTmpImpuesto;
                        }
                        else
                        {
                            $intImpuestoOtros += $intTmpImpuesto;
                        }
                    }//( is_object($objAdmiImpuesto) )
                }//foreach($arrayImpuestosPrioridad2 as $objAdmiImpuesto)
            }//( $arrayImpuestosPrioridad2 )
            /*
             * Fin Se calcula el valor de los impuestos de prioridad 2
             */
        }
        else
        {
            /* Cuando es plan voy a la tabla InfoPlanCab para sacar la bandera del impuesto */
            $entityInfoPlan = $this->emcom->getRepository('schemaBundle:InfoPlanCab')->find($arrayParametros['codigo']);
            
            if($entityInfoPlan)
            {
                if($entityInfoPlan->getIva() == 'S')
                {
                    if( is_object($entityAdmiImpuestoIva) )
                    {
                        $intValorPorcentaje = $entityAdmiImpuestoIva->getPorcentajeImpuesto() ? $entityAdmiImpuestoIva->getPorcentajeImpuesto() : 0;
                        $intValorImpuesto   = 0;
                        $intTmpImpuesto     = 0;

                        $objInfoDocumentoFinancieroImp = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                              ->findOneByDetalleDocId( $intIdDetalleFactura );

                        if( is_object($objInfoDocumentoFinancieroImp) )
                        {
                            $intValorPorcentaje = $objInfoDocumentoFinancieroImp->getPorcentaje() 
                                                  ? $objInfoDocumentoFinancieroImp->getPorcentaje() : 0;
                            $intValorImpuesto   = $objInfoDocumentoFinancieroImp->getValorImpuesto()
                                                  ? $objInfoDocumentoFinancieroImp->getValorImpuesto() : 0;
                        }
                        
                        //Validación que verifica si se debe recalcular el impuesto de la factura o si se debe tomar el valor guardado en base
                        if( $booleanValorOriginal )
                        {
                            $intTmpImpuesto = $intValorImpuesto;
                        }
                        else
                        {
                            $intTmpImpuesto = ( ($intPrecioNuevo * $intValorPorcentaje) / 100 );
                        }
                        
                        $intImpuestoIva     += $intTmpImpuesto;
                        $intImpuesto        += $intTmpImpuesto;
                    }//( is_object($entityAdmiImpuestoIva) )
                }//($entityInfoPlan->getIva() == 'S')
            }//$entityInfoPlan)
        }//($arrayParametros['tipo'] == 'PR')
        

        $arrayResultados['impuesto']                   = $intImpuesto;
        $arrayResultados['impuestoIva']                = $intImpuestoIva;
        $arrayResultados['impuestoIce']                = $intImpuestoIce;
        $arrayResultados['impuestoOtros']              = $intImpuestoOtros;
        $arrayResultados['subtotal']                   = round($intPrecioSinDescuento, 2);
        $arrayResultados['descuento']                  = round($arrayParametros['descuento'], 2);
        $arrayResultados['floatCompensacionSolidaria'] = 0;
        
        
        /**
         * Bloque que obtiene el valor de compensación solidaria cuando la factura ha sido compensada
         */
        if( $strEsCompensado == "S" && floatval($intImpuestoIva) > 0 )
        {
            $floatPorcentajeCompensacion = 2;//Por defecto el valor de compensación inicial dado por el SRI es 2
            $objAdmiImpuestoCompensacion = $this->emgene->getRepository('schemaBundle:AdmiImpuesto')
                                                ->findOneBy(array('tipoImpuesto' => 'COM', 'estado' => 'Activo'));
            
            if(is_object($objAdmiImpuestoCompensacion))
            {
                $floatPorcentajeCompensacion = $objAdmiImpuestoCompensacion->getPorcentajeImpuesto();
            }//(is_object($objAdmiImpuestoCompensacion))
            
            $floatSubtotalACompensar = ( ($intPrecioNuevo + $intImpuestoIce) * $floatPorcentajeCompensacion )/100;
            
            $arrayResultados['floatCompensacionSolidaria'] = $floatSubtotalACompensar;
        }//( $strEsCompensado == "S" && floatval($intImpuestoIva) > 0 )
        
        return $arrayResultados;
    }
    
    
    /**
     * Obtener la diferencias entres fechas para la nota de credito y la nota de credito interna
     * @param fechaDesde Fecha desde para la resta
     * @param fechaHasta Fecha hasta para la resta
     * @return dias_diferencia Retorna los dias de las nota de credito o nota de credito interna creada
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 22-07-2014
     */
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
     * El metodo obtieneValorTotalNcACrear, obtiene el valor total de la nota de credito a crear
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 05-02-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 13-06-2016 - Se valida que las notas e crédito que realicen con el impuesto de la factura seleccionada.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 17-11-2016 - Se valida que cuando la NC sea por 'Valor Original' se tome el impuesto calculado enviado desde el grid, es decir ya
     *                           no se recalcula dicho valor ni se lo redondea a dos decimales.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 30-11-2016 - Se realiza modificación para que calcule el valor del detalle en la variable '$intPrecioSinDescuento' sin importar
     *                           si el cliente paga iva.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 01-12-2016 - Se restan los valores totales de compensación solidaria y compensación por forma de pago al valor total, para
     *                           obtener el valor final de la NC a crear.
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 25-01-2017 - Se quita el redondeo por detalle al crear la NC para realizar el redondeo de los valores en la cabecera del 
     *                           documento a crear.   
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.6 07-09-2018 - Se agrega funcion para obtener impuesto a Calcularse por detalle del documento en base a la prioridad del Impuesto.
     * 
     * @param type $arrayParametrosIn
     * @return type
     */
    function obtieneValorTotalNcACrear($arrayParametrosIn)
    {
        try
        {
            $objInformacionGrid     = $arrayParametrosIn["informacionGrid"];
            $intSumTotalImpuesto    = 0;
            $intSumSubtotal         = 0;
            $intSumDescuento        = 0;
            $arrayResultadoCalculos = array();
            $strPagaIva             = $arrayParametrosIn["strPagaIva"];
            $entityAdmiImpuestoIva  = $this->emgene->getRepository('schemaBundle:AdmiImpuesto')
                                           ->findOneBy( array('tipoImpuesto' => $this->tipoImpuesto, 'estado' => 'Activo') );
            
            //Verifica que la NC tenga detalles
            if(!empty($objInformacionGrid))
            {
                $floatSumCompensacionSolidaria = 0;

                //Se recorren los detalles
                foreach($objInformacionGrid as $objInfoGrid)
                {
                    $intImpuesto            = 0;
                    $intSubtotal            = 0;
                    $intPrecioSinDescuento  = 0;
                    
                    $floatSumCompensacionSolidaria += ($objInfoGrid->floatCompensacionSolidaria ? $objInfoGrid->floatCompensacionSolidaria : 0);

                    //Pregunta si es PR => Producto
                    if($objInfoGrid->tipo == 'PR')
                    { 
                        $intPrecioNuevo        = ($objInfoGrid->valor * $objInfoGrid->cantidad) - $objInfoGrid->descuento;
                        $intPrecioSinDescuento = ($objInfoGrid->valor * $objInfoGrid->cantidad);
                                
                        $arrayInfoDocumentoFinancieroImp = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                ->findByDetalleDocIdPrioridad( $objInfoGrid->intIdDetalleFactura );

                        if( $arrayInfoDocumentoFinancieroImp )
                        {
                            $floatImpuestoIce = 0;
                                
                            foreach($arrayInfoDocumentoFinancieroImp as $objInfoDocumentoFinancieroImp)
                            {
                                $boolImpuesto    = true;
                                $objAdmiImpuesto = $this->emgene->getRepository('schemaBundle:AdmiImpuesto')
                                                        ->findOneById($objInfoDocumentoFinancieroImp->getImpuestoId());

                                if( $strPagaIva != "Si" )
                                {
                                    if( $objAdmiImpuesto )
                                    {
                                        if( $objAdmiImpuesto->getTipoImpuesto() == 'IVA' )
                                        {
                                            $boolImpuesto = false;
                                        }//( $objAdmiImpuesto->getTipoImpuesto() == 'IVA' )
                                    }//( $objAdmiImpuesto )
                                }//( $strPagaIva != "Si" )


                                if($boolImpuesto)
                                {
                                    if( $objAdmiImpuesto )
                                    {
                                        if( $objInfoGrid->tipoNC == "ValorOriginal" )
                                        {
                                            $intImpuesto += $objInfoGrid->impuesto;
                                            
                                            break;
                                        }
                                        else
                                        {
                                            $intImpuesto += (( ($intPrecioNuevo + $floatImpuestoIce) * $objAdmiImpuesto->getPorcentajeImpuesto()) 
                                                             /100);
                                            
                                            if( $objAdmiImpuesto->getTipoImpuesto() == 'ICE' )
                                            {
                                                $floatImpuestoIce = $intImpuesto;
                                            }                                            
                                        }
                                    }//( $objAdmiImpuesto )
                                }//($boolImpuesto)
                            }//foreach($arrayInfoDocumentoFinancieroImp as $objInfoDocumentoFinancieroImp)
                        }//( $arrayInfoDocumentoFinancieroImp )
                        
                        $intSumTotalImpuesto += $intImpuesto;
                        $intSumSubtotal      += $intPrecioSinDescuento;
                        $intSumDescuento     += $objInfoGrid->descuento;
                    }
                    else // Caso contrario es un Plan
                    {
                        $entityInfoPlan = $this->emcom->getRepository('schemaBundle:InfoPlanCab')->find($objInfoGrid->codigo);
                        //Pregunta que exista un plan
                        if(!empty($entityInfoPlan))
                        {
                            //Realiza el calculo por plan
                            $intPrecioNuevo         = ($objInfoGrid->valor * $objInfoGrid->cantidad) - $objInfoGrid->descuento;
                            $intPrecioSinDescuento  = ($objInfoGrid->valor * $objInfoGrid->cantidad);
                            $intSubtotal            += $intPrecioNuevo;
                            
                            if($entityInfoPlan->getIva() == 'S' && $strPagaIva == "Si")
                            {
                                if($entityAdmiImpuestoIva)
                                {
                                    $intValorPorcentaje = $entityAdmiImpuestoIva->getPorcentajeImpuesto();

                                    $objInfoDocumentoFinancieroImp = $this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroImp')
                                                                          ->findOneByDetalleDocId( $objInfoGrid->intIdDetalleFactura );

                                    if( $objInfoDocumentoFinancieroImp )
                                    {
                                        $intValorPorcentaje = $objInfoDocumentoFinancieroImp->getPorcentaje();
                                    }
                                    
                                    if( $objInfoGrid->tipoNC == "ValorOriginal" )
                                    {
                                        $intImpuesto += $objInfoGrid->impuesto;
                                    }
                                    else
                                    {
                                        $intImpuesto += (($intPrecioNuevo * $intValorPorcentaje) / 100);
                                    }
                                }//($entityAdmiImpuesto)
                            }//($entityInfoPlan->getIva() == 'S')

                            $intSumTotalImpuesto    +=  $intImpuesto;
                            $intSumSubtotal         +=  $intPrecioSinDescuento;
                            $intSumDescuento        +=  $objInfoGrid->descuento;
                        }
                    }
                }
               
                $arrayResultadoCalculos['intSumSubtotal']       = round($intSumSubtotal, 2);
                $arrayResultadoCalculos['intSumTotalImpuesto']  = round($intSumTotalImpuesto, 2);
                $arrayResultadoCalculos['intSumDescuento']      = round($intSumDescuento, 2);
                $arrayResultadoCalculos['intValorTotal']        = round($intSumSubtotal, 2) - round($intSumDescuento, 2) 
                                                                  + round($intSumTotalImpuesto, 2) - round($floatSumCompensacionSolidaria, 2);
            }//(!empty($objInformacionGrid))
        }
        catch(\Exception $ex)
        {
            $arrayResultadoCalculos['strMensajeError'] = 'Existion un error en obtieneValorTotalNcACrear - '.$ex->getMessage();
        }
        return $arrayResultadoCalculos;
    }//obtieneValorTotalNcACrear
    
    /**
     * notificaProcesoNotaCredito, realiza el envio de notificacion en los procesos de la nota de credito
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 19-01-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 - Se envia el codEmpresa para enviar el mail a las personas correctas dependiendo de la empresa a la que pertenece el usuario en 
     *                session. 
     * @since 17-06-2016
     * @param type $arrayParametros Recibe los parametros de configuracion para el envio de la notificacion
     * @return string   Retorna un Ok en caso de éxito, caso contrario retorna el mensaje de error
     */
    public function notificaProcesoNotaCredito($arrayParametros)
    {        
        try{
            //Obtiene los parametros segun el strNombreParametro => Nombre de Parametro
            
            $arrayPermiteEnviarCorreo = $this->emgene->getRepository('schemaBundle:AdmiParametroDet')
                                             ->get($arrayParametros['strNombreParametro'], 
                                                   $arrayParametros['strModulo'], 
                                                   $arrayParametros['strProceso'], 
                                                   NULL, 
                                                   $arrayParametros['strAccionGeneral'], 
                                                   NULL, 
                                                   NULL, 
                                                   NULL);
                        
            //Valida que hayan datos
            if(!empty($arrayPermiteEnviarCorreo))
            {
                //Pregunta si esta permitido enviar correo
                if(strtoupper($arrayPermiteEnviarCorreo[0]['valor2']) == 'SI')
                {
                    //Obtiene el usuario que envia y asunto del correo
                    $arrayGetFromSubject = $this->emgene->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get($arrayParametros['strNombreParametro'], 
                                                      $arrayParametros['strModulo'], 
                                                      $arrayParametros['strProceso'],
                                                      NULL, 
                                                      $arrayParametros['strAccionUnitaria'], 
                                                      NULL, 
                                                      NULL, 
                                                      NULL);

                    $arrayCorreos = NULL;
                    //verifica si se notifica a otro usuario, que no este relacionado a la pantilla o que realice la accion
                    if(strtoupper($arrayPermiteEnviarCorreo[0]['valor3']) == 'SI')
                    {
                        //Recorre los usuarios
                        foreach($arrayParametros['arrayUsrCreacionH'] as $arrayUsrCreacionH):
                            //Obtiene el registro del usuario
                            $entityInfoPersona = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                             ->getContactosByLoginPersonaAndFormaContacto($arrayUsrCreacionH['strUsrCreacionH'],
                                                                                                          'Correo Electronico');
                            //Itera los correos de los usuarios para almacenarlos en $arrayCorreos
                            foreach($entityInfoPersona as $arrayPersonaFormaContato):
                                if(!empty($arrayPersonaFormaContato['valor'])){
                                    $arrayCorreos[] = $arrayPersonaFormaContato['valor'];
                                }
                            endforeach;
                        endforeach;
                    }

                    //verifica si se notifica al usuario que esta realizando la accion
                    if(strtoupper($arrayPermiteEnviarCorreo[0]['valor4']) == 'SI')
                    {
                        //Obtiene el registro del usuario
                        $entityInfoPersona = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                         ->getContactosByLoginPersonaAndFormaContacto(trim($arrayParametros['strUser']),
                                                                                                          'Correo Electronico');
                        //Itera los correos de los usuarios para almacenarlos en $arrayCorreos
                        foreach($entityInfoPersona as $arrayPersonaFormaContato):
                            if(!empty($arrayPersonaFormaContato['valor'])){
                                $arrayCorreos[] = $arrayPersonaFormaContato['valor'];
                            }
                        endforeach;
                    }
                    $strNombreMotivo = '';
                    //Pregunta si existe un id motivo para realizar la busqueda
                    if(!empty($arrayParametros['intIdMotivo']))
                    {
                        //Obtiene el registro del motivo por el id motivo para obtener el nombre del motivo
                        $entityAdmiMotivo   = $this->emgene->getRepository('schemaBundle:AdmiMotivo')->find($arrayParametros['intIdMotivo']);
                        $strNombreMotivo    = $entityAdmiMotivo->getNombreMotivo();
                    }                    
                    //Se definen los parametros del cuerpo del correo
                    $arrayParametroEnvioMail = array('strObservacion'                   => $arrayParametros['strObservacion'],
                                                     'strMotivo'                        => $strNombreMotivo,
                                                     'notificaProcesoNotaCredito'       => '',
                                                     'strTipoNotaCredito'               => $arrayParametros['strTipoNotaCredito'],
                                                     $arrayParametros['strProcesoNc']   => $arrayParametros['strFila']);
                    $strAsuntoCorreo         = $arrayGetFromSubject[0]['valor3'];                    
                    if(empty($arrayGetFromSubject[0]['valor3']))
                    {
                        $strAsuntoCorreo = 'Notificacion Nota de Credito';
                    }
                    //Se ejecuta el envio de la notificacion
                    $this->envioPlantilla->generarEnvioPlantilla($strAsuntoCorreo, 
                                                                 $arrayCorreos, 
                                                                 $arrayParametros['strCodigoPlantilla'], 
                                                                 $arrayParametroEnvioMail, 
                                                                 $arrayParametros['strCodEmpresa'], 
                                                                 '', 
                                                                 '', 
                                                                 NULL, 
                                                                 FALSE);
                }
            }//$arrayPermiteEnviarCorreo
            $strStatus  = 'Ok';
        }catch(\Exception $ex){
            $strStatus = 'Existió un error en notificaProcesoNotaCredito - '.$ex->getMessage();
        }
        return $strStatus;
    }//notificaProcesoNotaCredito
    
    
     /**
     * getValorNcAplicadasPorDetalleIndice, realiza la sumatoria por cada detalle de la nota de credito
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 21-07-2017
     *
     * @param type  $arrayParametros              Recibe los parametors del detalle de la nc
     * @param type  $arrayIndices                 Recibe los indices para posicionarse en el detalle correspondiente a la NC
     * @param float $floatValorItemDetalleFactura Recibe el valor de la NC
     * 
     * @return type $arrayResultados   Retorna el array modificado con los nuevos detalle de las NC y el valor total por detalle de la NC.
     */
    public function getValorNcAplicadasPorDetalleIndice($arrayDetalles, $arrayIndices, $floatValorItemDetalleFactura)
    {   
        $floatAcumuladorNc = 0;
        $arrayNuevoItemsNc = array();
        $arrayNuevoItemsNc = $arrayDetalles; 
        $arrayResultados   = array();
        
        for ($intIndexNc = 0; $intIndexNc < count($arrayNuevoItemsNc); $intIndexNc++) 
        {
            $arrayItemsNc = $arrayNuevoItemsNc[$intIndexNc];
            
            if($arrayItemsNc && count($arrayItemsNc) > 0)
            {
                $intPuntoId = $arrayIndices['intPuntoId'];
                $strTipo    = $arrayIndices['strTipo'];
                $intCodigo  = $arrayIndices['intCodigo'];
                
                //Si contiene mas de una vez el mismo servicio
                if ( count($arrayItemsNc[$intPuntoId][$strTipo][$intCodigo]) > 1 ) 
                {
                    foreach ($arrayItemsNc[$intPuntoId][$strTipo][$intCodigo] as $intIndiceItem => $floatValorItemNc)
                    {
                       $floatValorActualNc    = 0;
                       //Si el valor de NC es MENOR al valor del detalle de la factura, agrego el valor a $floatValorActualNc y lo elimino del array
                       if ( $floatValorItemNc <= $floatValorItemDetalleFactura ) 
                       {
                           $floatValorActualNc = $floatValorItemNc;
                           unset($arrayNuevoItemsNc[$intIndexNc][$intPuntoId][$strTipo][$intCodigo][$intIndiceItem]); //Elimino items de la copia
                           break;
                       }
                    }
                }
                else
                {
                    $floatValorActualNc    = 0;
                    $floatValorItemNc      = floatval( implode( $arrayItemsNc[$intPuntoId][$strTipo][$intCodigo] ) );
                    $intIndiceItem         = key($arrayItemsNc[$intPuntoId][$strTipo][$intCodigo]);

                    if ( $floatValorItemNc <= $floatValorItemDetalleFactura  )
                    {
                       $floatValorActualNc = $floatValorItemNc;
                       unset($arrayNuevoItemsNc[$intPuntoId][$strTipo][$intCodigo][$intIndiceItem]); //Elimino los valores de la copia
                    }
                }//if ( count($arrayItemsNc[$intPuntoId][$strTipo][$intCodigo]) > 1 ) 

                $floatAcumuladorNc = $floatAcumuladorNc + $floatValorActualNc;
                
            }//if($arrayItemsNc && count($arrayItemsNc) > 0)
       }//($intIndexNc = 0; $intIndexNc < count($arrayNuevoItemsNc); $intIndexNc++) 
       
       $arrayResultados['arrayNuevoItemsNc'] = $arrayNuevoItemsNc;
       $arrayResultados['floatAcumulador']   = $floatAcumuladorNc;
       
       return $arrayResultados;
    }
    
    
     /**
     * procesoMasivoNC
     * Función que inserta en las estructuras INFO_PROCESO_MASIVO_DET , INFO_PROCESO_MASIVO_CAB, 
     * un regsitro de inicio o fin del proceso masivo de creación de NC.
     *
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 13-12-2019
     *
     * @param array $arrayParametros [strCodEmpresa      código de la empresa
     *                                 strTipoProceso     tipo de proceso - "AprobarNC"
     *                                 strEstado          estado del proceso - "Pendiente", "Finalizado"
     *                                 strUsrCreacion     usuario de creación
     *                                 strIpCreacion      ip de creación strRutaServer ]
     */
    public function procesoMasivoNC($arrayParametros)
    {

       $this->emInfraestructura->getConnection()->beginTransaction();
       $strPuntoAdmin   ='50418';
       $strServicioAdmin='39889';

        try
        {
            if ($arrayParametros["strEstado"]=='Pendiente')
            {
                //Se registra en la INFO_PROCESO_MASIVO_CAB
                $objInfoProcesoMasivoCab = new InfoProcesoMasivoCab();
                $objInfoProcesoMasivoCab->setTipoProceso($arrayParametros["strTipoProceso"]);
                $objInfoProcesoMasivoCab->setEmpresaCod($arrayParametros["strCodEmpresa"]);
                $objInfoProcesoMasivoCab->setEstado($arrayParametros["strEstado"]);
                $objInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
                $objInfoProcesoMasivoCab->setUsrCreacion($arrayParametros["strUsrCreacion"]);
                $objInfoProcesoMasivoCab->setIpCreacion($arrayParametros["strIpCreacion"]);
                $this->emInfraestructura->persist($objInfoProcesoMasivoCab);

                //Se registra en la INFO_PROCESO_MASIVO_DET
                $objInfoProcesoMasivoDet = new InfoProcesoMasivoDet();
                $objInfoProcesoMasivoDet->setProcesoMasivoCabId($objInfoProcesoMasivoCab);
                $objInfoProcesoMasivoDet->setPuntoId($strPuntoAdmin);
                $objInfoProcesoMasivoDet->setServicioId($strServicioAdmin);
                $objInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                $objInfoProcesoMasivoDet->setUsrCreacion($arrayParametros["strUsrCreacion"]);
                $objInfoProcesoMasivoDet->setIpCreacion($arrayParametros["strIpCreacion"]);
                $objInfoProcesoMasivoDet->setEstado($arrayParametros["strEstado"]);
                $this->emInfraestructura->persist($objInfoProcesoMasivoDet);
                $this->emInfraestructura->flush();
                $this->emInfraestructura->getConnection()->commit();

                return true;
            }
            else
            {
                $arrayProcesoMasivoDet = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                           ->getObtenerUltimoProcesoMasivoDet('AprobarNC', 
                                                                                              $arrayParametros["strCodEmpresa"],
                                                                                              $strPuntoAdmin, 
                                                                                              $strServicioAdmin);
                  
                if ($arrayProcesoMasivoDet)
                {  
                    $entityInfoProcesoMasivoDet = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                    ->find($arrayProcesoMasivoDet[0]['ID_PROCESO_MASIVO_DET']);
                }
                else
                { 
                    $entityInfoProcesoMasivoDet = null;
                }

                if ($entityInfoProcesoMasivoDet)
                {
                    $entityInfoProcesoMasivoDet->setEstado("Finalizado");
                    $entityInfoProcesoMasivoDet->setFeUltMod(new \DateTime('now'));
                    $entityInfoProcesoMasivoDet->setUsrUltMod($arrayParametros["strUsrCreacion"]);
                    $entityInfoProcesoMasivoCab = $entityInfoProcesoMasivoDet->getProcesoMasivoCabId();
                    $entityInfoProcesoMasivoCab->setEstado("Finalizado");
                    $entityInfoProcesoMasivoCab->setFeUltMod(new \DateTime('now'));
                    $entityInfoProcesoMasivoCab->setUsrUltMod($arrayParametros["strUsrCreacion"]);
                    $this->emInfraestructura->persist($entityInfoProcesoMasivoDet);
                    $this->emInfraestructura->persist($entityInfoProcesoMasivoCab);
                    $this->emInfraestructura->flush();
                    $this->emInfraestructura->getConnection()->commit();
                    return true;
                }
                
               
            }
          
        }
        catch(\Exception $e)
        {
          
            error_log('exception'. $e->getMessage());
            $this->utilService->insertError('TELCOS+',
                                            'InfoElementoService->procesarSeriesElementos',
                                            $e->getMessage(),
                                            $arrayParametros["strUsrCreacion"],
                                            $arrayParametros["strIpCreacion"]);

            $this->emInfraestructura->close();

            return false;
        }
    }
}
