<?php

namespace telconet\financieroBundle\Service;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoCaracteristica;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoAutomaticoHist;

class InfoDevolucionService
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emfinan;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emfinan = $container->get('doctrine.orm.telconet_financiero_entity_manager');
    }

    /**
     * Genera la devolucion
     * 
     * @version 1.0 Version Inicial
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 06-09-2016 - Se añade validación que cuando la cabecera del pago seleccionado sea un Anticipo la NDI pase a estado 'Cerrado'
     *                           directamente. Para ello se verifica en la tabla DB_FINANCIERO.ADMI_TIPO_DOCUMENTO el campo CODIGO_TIPO_DOCUMENTO
     *                           que sea igual a 'ANT', 'ANTC' o 'ANTS', y en la tabla DB_FINANCIERO.INFO_PAGO_DET que su campo REFERENCIA_ID sea
     *                           NULL
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.2 20-09-2016 - Para el caso de Anticipos(ANT) o Anticipos por Cruce (ANTC) que esten en estado Pendiente se permite
     * generar la NDI por un valor menor al Anticipo, para el caso Pagos Cerrados se generara la ndi por el valor total del Pago, cambio
     * aplica para TN y MD.
     * 
     * Para el caso de NDI que incluyen multa por devolucion de cheque, Si posee multa mayor a cero debo sumar al total de la NDI 
     * el valor de la multa, cambio aplica solo para MD.
     * Se formatea 2 decimales el valor de la NDI.
     * Valido que no se permita generar el documento sobre un valor menor al pago.
     * La función createNotFoundException no está declarada dentro de la misma clase, ni se hereda de otra clase. Se modifica en el codigo
     * de la funcion incluyendo en donde no corresponde al Merge o desarrollo solicitado.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.3 11-11-2016 
     * Se modifica para que siempre que la NDI posea multa mayor a cero se sume al total de la NDI el valor de la multa.
     * 
     * @param parametros variables del controlador
     * @return entity Retorna la devolucion creada
     */
    function generarDevolucion($parametros)
    {
        //Parametros iniciales
        $empresa_id       = $parametros['empresa_id'];
        $oficina_id       = $parametros['oficina_id'];
        $codigo           = $parametros['codigoTipoDocumento'];
        $informacionGrid  = $parametros['informacionGrid'];
        $user             = $parametros['user'];
        $punto_id         = $parametros['punto_id'];
        $estado           = $parametros['estado'];
        $objNotaDebitoDet = null;
        $entity           = null;

        //Verificar que los pagos_det_id que estoy enviando no se encuentre ya en la tabla de InfoDocumentoFinancieroDet
        
        if($informacionGrid)
        {
            $i=0;
            foreach($informacionGrid as $info)
            {
                $arreglo[$i]=$info->idpagodet;
                $i++;
                
                //consulta si el detalle del pago ya tiene una nota de debito asignada, se agrega estado Cerrado
                $objNotaDebitoDet=$this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                       ->findNotasDeDebitoPorPagoDetIdPorEstados($info->idpagodet,array('Activo','Activa','Pendiente','Cerrado'));
                if ($objNotaDebitoDet) 
                {                    
                    throw new \Exception('El detalle del pago seleccionado ya esta asignado a una Nota de debito');                    
                } 
            }
        }        
        
        if(!$objNotaDebitoDet)
        {
            $this->emcom->getConnection()->beginTransaction();
            $this->emfinan->getConnection()->beginTransaction();
            
            try
            {
                //Numeracion del documento
                $datosNumeracion = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($empresa_id,$oficina_id,$codigo);
                $secuencia_asig  = str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT);
                $numero_de_nota  = $datosNumeracion->getNumeracionUno()."-".$datosNumeracion->getNumeracionDos()."-".$secuencia_asig;
                
                //busqueda del tipo de documento
                $documento=$this->emfinan->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')->findOneByCodigoTipoDocumento($codigo);
                $entity  = new InfoDocumentoFinancieroCab();
                $entity->setTipoDocumentoId($documento);
                $entity->setPuntoId($punto_id);
                $entity->setEsAutomatica("N");
                $entity->setProrrateo("N");
                $entity->setReactivacion("N");
                $entity->setRecurrente("N");
                $entity->setComisiona("N");
                $entity->setOficinaId($oficina_id);
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setFeEmision(new \DateTime('now'));
                $entity->setUsrCreacion($user);
                $entity->setEstadoImpresionFact($estado);
                $entity->setNumeroFacturaSri($numero_de_nota);
                $this->emfinan->persist($entity);
                $this->emfinan->flush();

                if($entity)
                {
                    //Actualizo la numeracion en la tabla
                    $numero_act=($datosNumeracion->getSecuencia()+1);
                    $datosNumeracion->setSecuencia($numero_act);
                    $this->emcom->persist($datosNumeracion);
                    $this->emcom->flush();
                }
                
                //Guardando el detalle
                $floatValorAcumulado = 0;

                if($informacionGrid)
                {
                    foreach($informacionGrid as $info)
                    {
                        /**
                         * Bloque que verifica si el CODIGO_TIPO_DOCUMENTO del pago seleccionado es un ANTICIPO, en caso de existir un registro se
                         * actualiza el estado de NDI a 'Cerrado'
                         */
                        $arrayParametros = array( 'intIdPagoDet'       => $info->idpagodet, 
                                                  'strEstadoActivo'    => 'Activo', 
                                                  'strNombreParametro' => 'CODIGO_ANTICIPOS' );
                        $objInfoPagoCab  = $this->emfinan->getRepository("schemaBundle:InfoPagoDet")->findPagoCab($arrayParametros);

                        if( $objInfoPagoCab != null )
                        {
                            $entity->setEstadoImpresionFact("Cerrado");
                            $this->emfinan->persist($entity);
                            $this->emfinan->flush();
                        }//( $objInfoPagoCab != null )

                        $entitydet  = new InfoDocumentoFinancieroDet();
                        $entitydet->setDocumentoId($entity);
                        $entitydet->setPuntoId($punto_id);
                        $entitydet->setCantidad(1);
                        $entitydet->setEmpresaId($empresa_id);
                        $entitydet->setOficinaId($oficina_id);                        
                        
                        //Se modifica para que siempre que la NDI posea multa mayor a cero se sume al total de la NDI el valor de la multa                        
                        if($info->multa>0)
                        {
                            $fltValorNdi = $info->valor + $info->multa;  
                        }
                        else
                        {
                          $fltValorNdi = $info->valor;
                        }
                        //El precio ya incluye el descuento... en el caso de los planes
                        $entitydet->setPrecioVentaFacproDetalle(round($fltValorNdi,2));
                        //El descuento debe ser informativo
                        $entitydet->setPorcetanjeDescuentoFacpro(0);
                        $entitydet->setFeCreacion(new \DateTime('now'));
                        $entitydet->setObservacionesFacturaDetalle($info->observacion);
                        $entitydet->setMotivoId($info->id);
                        $entitydet->setPagoDetId($info->idpagodet);
                        $entitydet->setUsrCreacion($user);
                        $this->emfinan->persist($entitydet);
                        $this->emfinan->flush();
                        $floatValorAcumulado+=$entitydet->getPrecioVentaFacproDetalle();     

                        //Si posee multa mayor a cero debo agregar un registro
                        if($info->multa>0)
                        {
                            $objAdmiCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                ->findOneBy( array('estado' => 'Activo', 'descripcionCaracteristica' => 'CHEQUE_PROTESTADO'));

                            $entityDocCaracteristica = new InfoDocumentoCaracteristica();
                            $entityDocCaracteristica->setDocumentoId($entity);
                            $entityDocCaracteristica->setValor($info->multa);
                            $entityDocCaracteristica->setUsrCreacion($user);
                            $entityDocCaracteristica->setFeCreacion(new \DateTime('now'));                      
                            $entityDocCaracteristica->setCaracteristicaId($objAdmiCaracteristica->getId());
                            $this->emfinan->persist($entityDocCaracteristica);
                            $this->emfinan->flush();
                        }
                        /* Si pago seleccionado es un ANTICIPO, obtengo Detalle de Anticipo para verificar si la NDI se esta relizando sobre
                         * el valor total del Anticipo o sobre un valor menor, en caso de realizarse sobre un valor menor se generara un 
                         * ANTC (Anticipo por Cruce) en estado 'Pendiente' por la diferencia.                         
                         */
                        $objInfoPagoDet = $this->emfinan->getRepository('schemaBundle:InfoPagoDet')->find($info->idpagodet);
                        if( $objInfoPagoCab != null  && $objInfoPagoDet != null )
                        {
                            // Valido que no se permita generar el documento sobre un valor menor al pago.
                            if((empty($parametros['strPermiteEdicionNdi']) || $parametros['strPermiteEdicionNdi']!='S')
                                && ($info->valor < $objInfoPagoDet->getValorPago()))
                            {                                
                                throw new \Exception('No es permitido generar el documento por un Valor menor al Pago');                             
                            }
                            if( $info->valor < $objInfoPagoDet->getValorPago())
                            {
                                $objInfoPagoCabClonado = new InfoPagoCab();
                                $objInfoPagoCabClonado = clone $objInfoPagoCab;                                                                
                                $objInfoPagoCabClonado->setEstadoPago('Pendiente');
                                $objInfoPagoCabClonado->setFeCreacion(new \DateTime('now'));
                                $objInfoPagoCabClonado->setAnticipoId($objInfoPagoCab->getId());
                                //Obtener la numeracion de la tabla Admi_numeracion
                                $objAdmiNumeracion = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                                          ->findByEmpresaYOficina($empresa_id,$oficina_id, "ANTC");
                                
                                if(empty($objAdmiNumeracion))
                                {                                    
                                    throw new \Exception('No existe numeracion para el Anticipo por Cruce');
                                }
                                $strSecuencia = str_pad($objAdmiNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
                                $strAnticipoC = $objAdmiNumeracion->getNumeracionUno() . "-" .$objAdmiNumeracion->getNumeracionDos() . 
                                                "-" . $strSecuencia;
                                
                                $objAdmiTipoDocumentoFinanciero = $this->emfinan->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')
                                                                       ->findOneByCodigoTipoDocumento("ANTC");

                                $objInfoPagoCabClonado->setTipoDocumentoId($objAdmiTipoDocumentoFinanciero);
                                $objInfoPagoCabClonado->setNumeroPago($strAnticipoC);
                                $objInfoPagoCabClonado->setOficinaId($oficina_id);
                                $objInfoPagoCabClonado->setEmpresaId($empresa_id);
                                $objInfoPagoCabClonado->setUsrCreacion($user);
                                $fltSaldoAnticipo = $objInfoPagoDet->getValorPago() - $info->valor;
                                // Se crea ANTC por la diferencia entre el Valor del Anticipo Original y el Valor de la NDI
                                $objInfoPagoCabClonado->setValorTotal(round($fltSaldoAnticipo,2));                                
                                $objInfoPagoCabClonado->setComentarioPago("Anticipo creado por cruce con anticipo No:" .
                                                                           $objInfoPagoCab->getNumeroPago() .
                                                                           ". " . $objInfoPagoCab->getComentarioPago());
                                $this->emfinan->persist($objInfoPagoCabClonado);
                                                                
                                $objInfoPagoDetClonado = new InfoPagoDet();
                                $objInfoPagoDetClonado = clone $objInfoPagoDet;          
                                $objInfoPagoDetClonado->setPagoId($objInfoPagoCabClonado);
                                $objInfoPagoDetClonado->setEstado('Pendiente');                                
                                $objInfoPagoDetClonado->setFeCreacion(new \DateTime('now'));
                                $objInfoPagoDetClonado->setUsrCreacion($user);
                                $objInfoPagoDetClonado->setValorPago(round($fltSaldoAnticipo,2));                                
                                $objInfoPagoDetClonado->setComentario("Anticipo creado por cruce con anticipo No:" .
                                                                       $objInfoPagoCab->getNumeroPago() .
                                                                       ". " . $objInfoPagoCab->getComentarioPago());
                                $this->emfinan->persist($objInfoPagoDetClonado);  
                                if($objInfoPagoCabClonado)
                                {
                                    //Actualizo la numeracion en la tabla
                                    $intNumeroAct = ($objAdmiNumeracion->getSecuencia() + 1);
                                    $objAdmiNumeracion->setSecuencia($intNumeroAct);
                                    $this->emcom->persist($objAdmiNumeracion);                                   
                                }
                            }
                        }
                        //Marco los detalle de pagos para saber que estados deben ser cambiados
                        $pa_actualizar[] = array("pago_det_id"=>$info->idpagodet);
                    }//foreach($informacionGrid as $info)
                }//($informacionGrid)
                
                $entity_act=$this->emfinan->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($entity->getId());
                $entity_act->setSubtotal($floatValorAcumulado);
                $entity_act->setSubtotalConImpuesto(0);
                $entity_act->setSubtotalDescuento(0);
                $entity_act->setValorTotal($floatValorAcumulado);
                $this->emfinan->persist($entity_act);
                $this->emfinan->flush();

                //Cambia los estados a los pagos
                $this->cambiarEstadoDocumento($pa_actualizar);
                
                if( $this->emcom->getConnection()->isTransactionActive() )
                {
                    $this->emcom->getConnection()->commit();
                    $this->emcom->getConnection()->close();
                }
                
                if( $this->emfinan->getConnection()->isTransactionActive() )
                {
                    $this->emfinan->getConnection()->commit();
                    $this->emfinan->getConnection()->close();
                }
            }
            catch(\Exception $ex)
            {
                if( $this->emcom->getConnection()->isTransactionActive() )
                {
                    $this->emcom->getConnection()->rollback();
                    $this->emcom->getConnection()->close();
                }
                
                if( $this->emfinan->getConnection()->isTransactionActive() )
                {
                    $this->emfinan->getConnection()->rollback();
                    $this->emfinan->getConnection()->close();
                }
                
                throw ($ex);
            }//try/catch
                
            return $entity;
        }//(!$objNotaDebitoDet)
    }
    
    /**
     * Genera el cambio de estado a los Anticipos asociados
     * @param parametros variables del controlador
     */
    function cambiarEstadoDocumento($pa_actualizar)
    {
        //print_r($pa_actualizar);
        foreach($pa_actualizar as $detId)
        {
            $pago_det=$this->emfinan->getRepository('schemaBundle:InfoPagoDet')->find($detId["pago_det_id"]);
            $pago_det->setEstado("Cerrado");
            //Seteo la variable para el pago
            $idPago=$pago_det->getPagoId()->getId();
            
            $this->emfinan->persist($pago_det);
            $this->emfinan->flush();
            
            //Actualizo la cabecera
            $datos=$this->emfinan->getRepository('schemaBundle:InfoPagoCab')->findCantidadDetalle($idPago);
            if($datos["totalDetalles"]==$datos["totalConEstado"])
            {
                //Actualizo el estado de la cabecera, solo si el total de detalles es igual al total con estado
                $pago_cab=$this->emfinan->getRepository('schemaBundle:InfoPagoCab')->find($idPago);
                $pago_cab->setEstadoPago("Cerrado");
                $this->emfinan->persist($pago_cab);
                $this->emfinan->flush();
                
            }
        }
    }
    
    /**
     * actualizaDetEstadoCta
     * Función que cambia a estado Pendiente el detalle de estado de cuenta con id enviado como parámetro.
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 20-10-2020 
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 02-08-2021 Se agrega envío de parámetro para seteo de nuevo estado.
     * 
     * @since 1.0
     * 
     * @param $intIdInfoPagoAutomaticoDet
     * 
     */
    public function actualizaDetEstadoCta($arrayParametros)
    {
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            $objInfoPagoAutomaticoDet = $this->emfinan->getRepository('schemaBundle:InfoPagoAutomaticoDet')
                                             ->find($arrayParametros['intIdPagoAutomaticoDet']);
            if(is_object($objInfoPagoAutomaticoDet))
            {
                $objInfoPagoAutomaticoDet->setEstado($arrayParametros['strNuevoEstado']); 
                $this->emfinan->persist($objInfoPagoAutomaticoDet);
                $this->emfinan->flush();

                $strObservacionHist = 'Detalle cambia de estado de Procesado a '.$arrayParametros['strNuevoEstado'].' por creacion de NDI ';
                //Graba historial de detalle de estado de cuenta.
                $objInfoPagoAutomaticoHist = new InfoPagoAutomaticoHist();
                $objInfoPagoAutomaticoHist->setDetallePagoAutomaticoId($objInfoPagoAutomaticoDet);
                $objInfoPagoAutomaticoHist->setEstado($arrayParametros['strNuevoEstado']);
                $objInfoPagoAutomaticoHist->setObservacion($strObservacionHist);
                $objInfoPagoAutomaticoHist->setFeCreacion(new \DateTime('now'));
                $objInfoPagoAutomaticoHist->setUsrCreacion($arrayParametros['strUsrCreacion']);
                $this->emfinan->persist($objInfoPagoAutomaticoHist);
                $this->emfinan->flush();                            
                $this->emfinan->getConnection()->commit();
                $this->emfinan->getConnection()->close();                
            }

        }
        catch(\Exception $ex)
        {
            if( $this->emfinan->getConnection()->isTransactionActive() )
            {
                $this->emfinan->getConnection()->rollback();
                $this->emfinan->getConnection()->close();
            }

            throw ($ex);
        }        
    }

    /**
     * actualizaDetEstadoCta
     * Función que cambia a estado Pendiente la cabecera del  pago automático con id enviado como parámetro.
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 11-06-2021 
     * 
     * @author : Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 02-08-2021 Se agrega envío de parámetro para seteo de nuevo estado.
     * 
     * @since 1.0
     * 
     * @param $arrayParametros
     * 
     */
    public function actualizaCabEstadoCta($arrayParametros)
    {
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            $objInfoPagoAutomaticoCab = $this->emfinan->getRepository('schemaBundle:InfoPagoAutomaticoCab')
                                             ->find($arrayParametros['intIdPagoAutomaticoCab']);
            if(is_object($objInfoPagoAutomaticoCab))
            {
                $objInfoPagoAutomaticoCab->setEstado($arrayParametros['strNuevoEstado']); 
                $this->emfinan->persist($objInfoPagoAutomaticoCab);
                $this->emfinan->flush();
                           
                $this->emfinan->getConnection()->commit();
                $this->emfinan->getConnection()->close();                
            }

        }
        catch(\Exception $ex)
        {
            if( $this->emfinan->getConnection()->isTransactionActive() )
            {
                $this->emfinan->getConnection()->rollback();
                $this->emfinan->getConnection()->close();
            }

            throw ($ex);
        }        
    }     
}
