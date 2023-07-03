<?php

namespace telconet\financieroBundle\Service;

use telconet\schemaBundle\Repository\VistaEstadoCuentaResumidoRepository;
use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Repository\InfoContratoRepository;
use telconet\schemaBundle\Entity\InfoPagoLinea;
use telconet\schemaBundle\Entity\InfoPagoLineaHistorial;
use telconet\schemaBundle\Entity\InfoPagoCab;
use telconet\schemaBundle\Entity\InfoPagoDet;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;
use telconet\schemaBundle\Repository\InfoOficinaGrupoRepository;
use telconet\schemaBundle\Repository\AdmiTipoDocumentoFinancieroRepository;
use telconet\schemaBundle\Entity\AdmiFormaPago;
use telconet\schemaBundle\Entity\AdmiTipoDocumentoFinanciero;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\AdmiCanalPagoLinea;

class InfoPagoLineaService {
    /**
     * @var InfoPagoService
     */
    private $serviceInfoPago;
    /**
     * @var \telconet\tecnicoBundle\Service\ProcesoMasivoService
     */
    private $serviceProcesoMasivo;
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

    /**
     * @var EnvioPlantillaService
     */
    private $serviceEnvioPlantilla;

    private $strAsuntoReverso;

    private $strTipoDocMigra;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->serviceInfoPago      = $container->get('financiero.InfoPago');
        $this->serviceProcesoMasivo = $container->get('tecnico.ProcesoMasivo');
        $this->serviceEnvioPlantilla= $container->get('soporte.EnvioPlantilla');
        $this->strAsuntoReverso     = $container->getParameter('financiero.pagoLinea.asunto.reverso');
        $this->strTipoDocMigra      = $container->getParameter('financiero.pagoLinea.tipoDocMigra.cont');
        $this->emcom     = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emNaf     = $container->get('doctrine.orm.telconet_naf_entity_manager');
        $this->emfinan   = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emGeneral = $container->get('doctrine.orm.telconet_general_entity_manager');
    }
    
    /**
     * Consulta el saldo de un cliente correspondiente al empresaCod e identificacionCliente dados.
     * No importa si el cliente no tiene roles activos.
     * Devuelve una estructura con los siguientes campos:
     * identificacionCliente, razonSocial, nombres, apellidos,
     * numeroContrato (maximo numero de contrato activo del cliente),
     * saldo (suma de saldos de todos los puntos del cliente, tomado de VistaEstadoCuentaResumido) 
     */
    public function obtenerConsultaSaldoClientePorIdentificacion($empresaCod, $identificacionCliente)
    {
        /* @var $repoVistaEstadoCuentaResumido VistaEstadoCuentaResumidoRepository */
        $repoVistaEstadoCuentaResumido = $this->emfinan->getRepository('schemaBundle:VistaEstadoCuentaResumido');
        /* @var $repoContrato InfoContratoRepository */
        $repoContrato = $this->emfinan->getRepository('schemaBundle:InfoContrato');
        
        // obtener datos de consulta de saldo del cliente
        $mapSaldo = $repoVistaEstadoCuentaResumido->findSaldoPorEmpresaPorIdentificacion($empresaCod, $identificacionCliente);
        
        // si se ha encontrado datos, obtener razonSocial y numeroContrato
        if (count($mapSaldo) > 0)
        {
            // obtener suma de pago linea pendientes de conciliar del cliente
            /* @var $repoInfoPagoLinea \telconet\schemaBundle\Repository\InfoPagoLineaRepository */
            $repoInfoPagoLinea = $this->emfinan->getRepository('schemaBundle:InfoPagoLinea');
            $sumaValorPendientePagoLinea = $repoInfoPagoLinea->obtenerSumaValorPendiente($empresaCod, $identificacionCliente);
            if (!empty($sumaValorPendientePagoLinea))
            {
                // reducir saldo del cliente con la suma de pago linea pendientes de conciliar
                $mapSaldo['saldo'] = round($mapSaldo['saldo'] - $sumaValorPendientePagoLinea, 4);
            }
            // obtener razonSocial
            $mapSaldo['nombreCliente'] = $mapSaldo['razonSocial'];
            if (strlen($mapSaldo['nombreCliente']) == 0)
            {
                // si no tiene razon social, usar nombres y apellidos
                $mapSaldo['nombreCliente'] = $mapSaldo['nombres'] . ' ' . $mapSaldo['apellidos'];
            }
            // obtener numero contrato Activo del cliente
            $numeroContrato = $repoContrato->findNumeroContratoPorEmpresaPorIdentificacionPorEstado ($empresaCod, $identificacionCliente, 'Activo');
            if (is_null($numeroContrato))
            {
                // si no hay Activo, buscar Cancelado
                $numeroContrato = $repoContrato->findNumeroContratoPorEmpresaPorIdentificacionPorEstado ($empresaCod, $identificacionCliente, 'Cancelado');
                if (is_null($numeroContrato))
                {
                    // si no hay Cancelado, buscar cualquiera sin filtrar estado
                    $numeroContrato = $repoContrato->findNumeroContratoPorEmpresaPorIdentificacionPorEstado ($empresaCod, $identificacionCliente);
                }
            }
            if ($empresaCod == '10')
            {
                $objInfoContrato = $repoContrato->findOneBy(array('numeroContrato' => $numeroContrato));
                if(is_object($objInfoContrato))
                {
                    $mapSaldo['formaPago'] = $objInfoContrato->getFormaPagoId()->getDescripcionFormaPago();
                }
            }
            $mapSaldo['numeroContrato'] = $numeroContrato;
        }

        return $mapSaldo;
    }

    /**
     * Documentacion para el método 'obtenerConsultaSaldoPorIdentificacion'.
     * Metodo que retorna el saldo de un cliente consultando por medio de la cedula de indentidad y codigo de empresa
     *
     * @param  Request   $empresaCod - $identificacionCliente. Recibe parametros obligatorios como identificacionCliente,
     *                                                         y codigo de empresa.
     * @return Response  $mapSaldo.  Retorna un json con parametros como NombreCliente, Saldo, NumeroContrato.
     *
     * @author  Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.0 29-05-2022
     * @since 1.0
     */
    public function obtenerConsultaSaldoPorIdentificacion($arrayRequest)
    {
        $objRepoVistaEstadoCuentaResumido = $this->emfinan->getRepository('schemaBundle:VistaEstadoCuentaResumido');
        $objMapSaldo = $objRepoVistaEstadoCuentaResumido->getSaldoPorEmpresaPorIdentificacion($arrayRequest);
        return $objMapSaldo;
    }
    
    /**
     * 
     * @return \telconet\schemaBundle\Entity\InfoPagoLinea
     */
    public function generarPagoLinea($empresaCod, $identificacionCliente, $numeroContrato, $nombreCanal, $valor, $numeroReferencia, $comentario)
    {
        $user = 'telcos_pal';
        
        // obtener canal pago linea por nombre (magic finder)
        /* @var $entityCanalPagoLinea AdmiCanalPagoLinea */
        $entityCanalPagoLinea = $this->emfinan->getRepository('schemaBundle:AdmiCanalPagoLinea')->findOneByNombreCanalPagoLinea($nombreCanal);

        // validar que no exista un pago linea con el mismo canal y numero referencia
        /* @var $entityPagoLinea InfoPagoLinea */
        $entityPagoLinea = $this->emfinan->getRepository('schemaBundle:InfoPagoLinea')->findOneBy(
                array('canalPagoLinea' => $entityCanalPagoLinea, 'numeroReferencia' => $numeroReferencia));
        if (!is_null($entityPagoLinea))
        {
            // se devuelve el id
            return $entityPagoLinea->getId();
        }
        
        // obtener y validar datos de consulta de saldo del cliente
        $mapSaldo = $this->obtenerConsultaSaldoClientePorIdentificacion($empresaCod, $identificacionCliente);

        if (count($mapSaldo) <= 0 || strcmp($mapSaldo['numeroContrato'], $numeroContrato) != 0 || $mapSaldo['saldo'] <= 0)
        {
            // si no se ha encontrado datos, o el contrato no coincide, o no tiene saldo
            // no se puede generar pago linea y se devuelve null
            return null;
        }

        // pago linea se va a generar en la oficina matriz de la empresa
        /* @var $repoOficinaGrupo InfoOficinaGrupoRepository */
        $repoOficinaGrupo = $this->emfinan->getRepository('schemaBundle:InfoOficinaGrupo');
        $entityOficinaMatriz = $repoOficinaGrupo->getOficinaMatrizPorEmpresa($empresaCod);

        // obtener persona por el id de los datos de consulta de saldo (magic finder)
        /* @var $entityPersona InfoPersona */
        $entityPersona = $this->emfinan->getRepository('schemaBundle:InfoPersona')->findOneById($mapSaldo['id']);

        // generar pago linea para el cliente, con el valor, referencia y comentario indicados, en estado Pendiente
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            $entityPagoLinea = new InfoPagoLinea();
            $entityPagoLinea->setCanalPagoLinea($entityCanalPagoLinea)
                    ->setEmpresaId($empresaCod)
                    ->setOficinaId($entityOficinaMatriz->getId())
                    ->setPersona($entityPersona)
                    ->setValorPagoLinea($valor)
                    ->setNumeroReferencia($numeroReferencia)
                    ->setEstadoPagoLinea('Pendiente')
                    ->setComentarioPagoLinea($comentario)
                    ->setUsrCreacion($user)
                    ->setFeCreacion(new \DateTime('now'))
                    ;
            $this->emfinan->persist($entityPagoLinea);
            $this->emfinan->flush();
            $this->emfinan->commit();
        } 
        catch (\Exception $e)
        {
            $this->emfinan->rollback();
            throw $e;
        }
        // se devuelve el objeto pago linea
        return $entityPagoLinea;
    }
    
    public function obtenerPagoLinea($nombreCanal, $numeroReferencia)
    {
        // obtener canal pago linea por nombre (magic finder)
        /* @var $entityCanalPagoLinea AdmiCanalPagoLinea */
        $entityCanalPagoLinea = $this->emfinan->getRepository('schemaBundle:AdmiCanalPagoLinea')->findOneByNombreCanalPagoLinea($nombreCanal);
        // obtener pago linea por canal y numero referencia
        /* @var $entityPagoLinea InfoPagoLinea */
        $entityPagoLinea = $this->emfinan->getRepository('schemaBundle:InfoPagoLinea')->findOneBy(
                array('canalPagoLinea' => $entityCanalPagoLinea, 'numeroReferencia' => $numeroReferencia));
        return $entityPagoLinea;
    }
    
    private function marcarPagoLineaErroneo(InfoPagoLinea $entityPagoLinea, $user)
    {
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            $entityPagoLinea->setEstadoPagoLinea('Erroneo')
                ->setUsrUltMod($user)
                ->setFeUltMod(new \DateTime('now'))
            ;
            $this->emfinan->persist($entityPagoLinea);
            $this->emfinan->flush();
            $this->emfinan->commit();
        }
        catch (\Exception $e)
        {
            $this->emfinan->rollback();
            throw $e;
        }
    }
    
    /**
     * Marca como Reversado un pago linea existente en estado Pendiente, cuyos datos coincidan con los proporcionados 
     * @return null|string null en caso de exito, caso contrario un mensaje de error
     */
    public function reversarPagoLinea($nombreCanal, $empresaCod, $identificacionCliente, $valor, $numeroReferencia, \DateTime $fecha)
    {
        $user = 'telcos_pal';
        
        // obtener pago linea por canal y numero referencia
        /* @var $entityPagoLinea InfoPagoLinea */
        $entityPagoLinea = $this->obtenerPagoLinea($nombreCanal, $numeroReferencia);
        
        // si el pago linea no existe, no se puede hacer nada
        if (is_null($entityPagoLinea))
        {
            return 'El pago no existe';
        }
        
        // validar que coincida en empresa, identificacion, valor y fecha
        if ($entityPagoLinea->getEmpresaId() != $empresaCod
                || $entityPagoLinea->getPersona()->getIdentificacionCliente() != $identificacionCliente
                || $entityPagoLinea->getValorPagoLinea() != $valor
                || $entityPagoLinea->getFeCreacion()->format('Ymd') != $fecha->format('Ymd'))
        {
            // si hay inconsistencias, no se puede reversar
            return 'El pago tiene datos distintos de los proporcionados';
        }
        
        switch ($entityPagoLinea->getEstadoPagoLinea())
        {
        	case 'Reversado':
        	case 'Eliminado':
        	    return 'El pago ya fue reversado anteriormente';
        	    break;
        	case 'Pendiente':
        	    // solo se puede reversar pago linea Pendiente
        	    // marcar pago linea como Reversado (el proceso batch lo marcara como Eliminado)
        	    $this->emfinan->getConnection()->beginTransaction();
        	    try
        	    {
        	        $entityPagoLinea->setEstadoPagoLinea('Reversado')
        	           ->setUsrUltMod($user)
	                   ->setFeUltMod(new \DateTime('now'))
        	        ;
        	        $this->emfinan->persist($entityPagoLinea);
        	        $this->emfinan->flush();
        	        $this->emfinan->commit();
        	        // devolver null indica exito
        	        return null;
        	    }
        	    catch (\Exception $e)
        	    {
            	    $this->emfinan->rollback();
            	    throw $e;
        	    }
        	    break;
        	case 'Conciliado':
        	    // TODO: implementar reverso de pagos conciliados
    	        return 'El pago ya ha sido conciliado. El cliente debe comunicarse con la empresa.';
    	        break;
        	default:
    	        return 'El pago ya ha sido conciliado. El cliente debe comunicarse con la empresa.';
    	        break;
        }
    }
    
    /**
     * Proceso para ser llamado desde un Command.
     * Elimina un pago linea existente en estado Reversado, cuyos datos coincidan con los proporcionados.
     * @param string $nombreCanal
     * @param string $empresaCod
     * @param string $identificacionCliente
     * @param float $valor
     * @param string $numeroReferencia
     * @param \DateTime $fecha
     * @throws Exception
     * @return string mensaje de exito o error
     */
    public function eliminarPagoLinea($nombreCanal, $empresaCod, $identificacionCliente, $valor, $numeroReferencia, \DateTime $fecha)
    {
        $user = 'telcos_pal';
        
        // obtener pago linea por canal y numero referencia
        /* @var $entityPagoLinea InfoPagoLinea */
        $entityPagoLinea = $this->obtenerPagoLinea($nombreCanal, $numeroReferencia);
        
        // si el pago linea no existe, no se puede hacer nada
        if (is_null($entityPagoLinea))
        {
            return 'Pago Linea no existe';
        }
        
        // si el pago linea no esta Reversado, no se puede hacer nada
        if ($entityPagoLinea->getEstadoPagoLinea() !== 'Reversado')
        {
            return "Pago Linea Id:{$entityPagoLinea->getId()} Estado:{$entityPagoLinea->getEstadoPagoLinea()} no esta Reversado";
        }
        
        // validar que coincida en empresa, identificacion, valor y fecha
        if ($entityPagoLinea->getEmpresaId() != $empresaCod
                || $entityPagoLinea->getPersona()->getIdentificacionCliente() != $identificacionCliente
                || $entityPagoLinea->getValorPagoLinea() != $valor
                || $entityPagoLinea->getFeCreacion()->format('Ymd') != $fecha->format('Ymd'))
        {
            // si hay inconsistencias, marcar pago linea con estado Erroneo
            // $this->marcarPagoLineaErroneo($entityPagoLinea, $user);
            return "Pago Linea Id:{$entityPagoLinea->getId()} Erroneo";
        }
        
        // marcar pago linea como eliminado
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            $entityPagoLinea->setEstadoPagoLinea('Eliminado')
                    ->setUsrUltMod($user)
                    ->setFeUltMod(new \DateTime('now'))
            ;
            $this->emfinan->persist($entityPagoLinea);
            $this->emfinan->flush();
            $this->emfinan->commit();
            return "Pago Linea Id:{$entityPagoLinea->getId()} Eliminado";
        }
        catch (\Exception $e)
        {
            $this->emfinan->rollback();
            throw $e;
        }
    }
    
    /**
     * Proceso para ser llamado desde un Command.
     * Concilia un pago linea existente en estado Pendiente, cuyos datos coincidan con los proporcionados. Genera pagos y anticipos.
     * @param string $nombreCanal
     * @param string $empresaCod
     * @param string $identificacionCliente
     * @param float $valor
     * @param string $numeroReferencia
     * @param \DateTime $fecha
     * @throws Exception
     * @return string mensaje de exito o error
     */
    public function conciliarPagoLinea($nombreCanal, $empresaCod, $identificacionCliente, $valor, $numeroReferencia, \DateTime $fecha)
    {
        $user = 'telcos_pal';
        
        // obtener pago linea por canal y numero referencia
        /* @var $entityPagoLinea InfoPagoLinea */
        $entityPagoLinea = $this->obtenerPagoLinea($nombreCanal, $numeroReferencia);
        
        // si el pago linea no existe, no se puede hacer nada
        if (is_null($entityPagoLinea))
        {
            return 'Pago Linea no existe';
        }

        // si el pago linea no esta Pendiente, no se puede hacer nada
        if ($entityPagoLinea->getEstadoPagoLinea() !== 'Pendiente')
        {
            return "Pago Linea Id:{$entityPagoLinea->getId()} Estado:{$entityPagoLinea->getEstadoPagoLinea()} no esta Pendiente";
        }

        // validar que coincida en empresa, identificacion, valor y fecha
        if ($entityPagoLinea->getEmpresaId() != $empresaCod
                || $entityPagoLinea->getPersona()->getIdentificacionCliente() != $identificacionCliente
                || $entityPagoLinea->getValorPagoLinea() != $valor
                || $entityPagoLinea->getFeCreacion()->format('Ymd') != $fecha->format('Ymd'))
        {
            // si hay inconsistencias, marcar pago linea con estado Erroneo
            // $this->marcarPagoLineaErroneo($entityPagoLinea, $user);
            return "Pago Linea Id:{$entityPagoLinea->getId()} Erroneo";
        }
        
        $result = '';
        // conciliar pago linea y generar pagos y/o anticipos
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            $respuesta = $this->serviceInfoPago->generarPagoAnticipoPagoLinea($entityPagoLinea, $user, $fecha);
            if (empty($respuesta))
            {
                // si no hubo respuesta
                $respuesta = 'No Conciliado';
            }
            else if (is_array($respuesta))
            {
                // si el pago ya fue registrado previamente
                $respuesta = $respuesta['codigoTipoDocumento'] . ' Existente';
            }
            else
            {
                $respuesta .= ' Conciliado';
                $entityPagoLinea->setEstadoPagoLinea('Conciliado')
                        ->setUsrUltMod($user)
                        ->setFeUltMod(new \DateTime('now'))
                        ;
                $this->emfinan->persist($entityPagoLinea);
                $this->emfinan->flush();
                $this->emfinan->commit();
            }
            $result = "Pago Linea Id:{$entityPagoLinea->getId()} {$respuesta}";
        }
        catch (\Exception $e)
        {
            $this->emfinan->rollback();
            throw $e;
        }
        $prefijoEmpresa = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')->getPrefijoByCodigo($entityPagoLinea->getEmpresaId());
        $reactivacion = $this->serviceProcesoMasivo->reactivarServiciosPorPagoLinea($entityPagoLinea, $prefijoEmpresa);
        $result .= ' Reactivacion:' . $reactivacion['isReactivado'] . '/' . $reactivacion['procesoMasivoId'];
        return $result;
    }
    
    
    /**
     * generaPagoLinea, metodo que crea un pago en la entidad InfoPagoLinea y InfoPagoLineaHistorial,
     * para realizar el registro del pago el metodo debe recibir la entidad del canal, secuencial recaudador, identificacion y 
     * codigo de empresa como parametros obligatorios. Retorna un array con la informacion del cliente y del pago
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 21-09-2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 20-10-2015
     * @since 1.0
     * 
     * @param array $arrayRequest[INVALID_PARAMETERS           => Definido en el Controller PagosLineaWSController
     *                            NOT_EXIST_ACCOUNT            => Definido en el Controller PagosLineaWSController 
     *                            PROCESS_ERROR                => Definido en el Controller PagosLineaWSController
     *                            NOT_FOUND_RECORDS            => Definido en el Controller PagosLineaWSController
     *                            DEBT_NOT_FOUND               => Definido en el Controller PagosLineaWSController
     *                            SERVICE_NOT_AVALIABLE        => Definido en el Controller PagosLineaWSController
     *                            entityAdmiCanalPagoLinea     => Recibe la entidad del canal
     *                            strSecuencialRecaudador      => Recibe el secuencial del pago a registrar
     *                            strCodEmpresa                => Recibe la empresa con la que se registrará el pago
     *                            strUsrCreacion               => Recibe el usuario con el cual se registra el pago
     *                            strComentario                => Recibe el comentario generado en el Controller PagosLineaWSController
     *                            intValor                     => Recibe el valor del pago a reversar
     *                            strIdentificacionCliente     => Recibe la identificacion del cliente]
     * 
     * @return array $arrayResponse[boolResponse        => Retorna un true cuando existio un error el cual impidio que el pago sea registrado
     *                              strMensaje          => Retorna un mensaje
     *                              strCodigo           => Retorna el codigo que fue definido en PagosLineaWSController
     *                              arrayInfoCliente    => Retorna la informacion del cliente
     *                              entityPagoLinea     => Retorna informacion sobre el pago]
     * 
     */
    public function generaPagoLinea($arrayRequest)
    {
        $arrayResponse                  = array();
        $arrayResponse['boolResponse']  = false;
        $arrayResponse['strMensaje']    = '';
        $arrayResponse['strCodigo']     = '';
        $arrayRequest['strEstado']      = 'Pendiente';
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            //Verifica que se haya enviado la entidad del canal, caso contrario termina el metodo con un response
            if(!$arrayRequest['entityAdmiCanalPagoLinea'])
            {
                $arrayResponse['strMensaje']   = 'No se esta definiendo canal.';
                $arrayResponse['strCodigo']    = $arrayRequest['INVALID_PARAMETERS'];
                $arrayResponse['boolResponse'] = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Busca el pago en linea por nombre del canal y numero de referencia
            $entityPagoLinea = $this->obtenerPagoLinea($arrayRequest['strCanal'], $arrayRequest['strSecuencialRecaudador']);
            //Si el pago existe termina el metodo con un response
            if($entityPagoLinea)
            {
                $arrayResponse['strMensaje']      = 'El secuencial ya ha sido usado anteriormente .';
                $arrayResponse['strCodigo']       = $arrayRequest['PROCESS_ERROR'];
                $arrayResponse['boolResponse']    = true;
                $arrayResponse['entityPagoLinea'] = $entityPagoLinea;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }

            //Obtiene informacion del cliente saldo, numero de contrato.
            $arrayInfoCliente = $this->obtenerConsultaSaldoClientePorIdentificacion($arrayRequest['strCodEmpresa'],
                                                                                    $arrayRequest['strIdentificacionCliente']);
            //Si no existe informacion termina el metodo con un response
            if(empty($arrayInfoCliente))
            {
                $arrayResponse['strMensaje']      = 'No econtro informacion del cliente.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_EXIST_ACCOUNT'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Si el econtraro enviado es diferente al encontrado termina el metodo con un response
            if($arrayInfoCliente['numeroContrato'] != $arrayRequest['strNumeroContrato'])
            {
                $arrayResponse['strMensaje']        = 'El contrato del cliente no es el correcto.';
                $arrayResponse['strMensaje']       .= ' Contrato correcto: '.$arrayInfoCliente['numeroContrato'];
                $arrayResponse['strCodigo']         = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']      = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Se obtiene el valor de saldo minimo con el cual el cliente puede realizar un pago en linea.
            $arrayPagoSinSaldo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->getOne('PARAMETROS_PAGOS_LINEA',
                                                          'FINANCIERO',
                                                          'PAGOS_LINEA',
                                                          '',
                                                          'PAGO_SIN_SALDO',
                                                          '',
                                                          '',
                                                          '');
            $intPagoSinSaldo = 0;
            if(!empty($arrayPagoSinSaldo['valor2']))
            {
                $intPagoSinSaldo = $arrayPagoSinSaldo['valor2'];
            }

            //Si el saldo es menor al valor permitido a pagar termina el metodo con un response
            if($arrayInfoCliente['saldo'] <= $intPagoSinSaldo)
            {
                $arrayResponse['strMensaje']      = 'Cliente no tiene deuda.';
                $arrayResponse['strCodigo']       = $arrayRequest['DEBT_NOT_FOUND'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Busca la oficia matriz por el codigo de empresa
            $entityInfoOficinaGrupo = $this->emfinan->getRepository('schemaBundle:InfoOficinaGrupo')
                                           ->getOficinaMatrizPorEmpresa($arrayRequest['strCodEmpresa']);
            //Si no existe termina el metodo con un response
            if(!$entityInfoOficinaGrupo)
            {
                $arrayResponse['strMensaje']      = 'No se encontro informacion de la empresa.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Si no existe el id de la persona termina el metodo con un response
            if(!empty($arrayInfoCliente['id']))
            {
                //Busca la persona
                $entityInfoPersona = $this->emfinan->getRepository('schemaBundle:InfoPersona')->findOneById($arrayInfoCliente['id']);
                //Si no existe la persona termina el metodo con un response
                if(!$entityInfoPersona)
                {
                    $arrayResponse['strMensaje']      = 'No se encontro informacion del cliente.';
                    $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                    $arrayResponse['boolResponse']    = true;
                    $this->emfinan->getConnection()->close();
                    return $arrayResponse;
                }
                //Registra un pago con estado Pendiente
                $entityPagoLinea = new InfoPagoLinea();
                $entityPagoLinea->setCanalPagoLinea($arrayRequest['entityAdmiCanalPagoLinea']);
                $entityPagoLinea->setEmpresaId($arrayRequest['strCodEmpresa']);
                $entityPagoLinea->setOficinaId($entityInfoOficinaGrupo->getId());
                $entityPagoLinea->setPersona($entityInfoPersona);
                $entityPagoLinea->setValorPagoLinea($arrayRequest['intValor']);
                $entityPagoLinea->setNumeroReferencia($arrayRequest['strSecuencialRecaudador']);
                $entityPagoLinea->setEstadoPagoLinea($arrayRequest['strEstado']);
                $entityPagoLinea->setComentarioPagoLinea($arrayRequest['strComentario']);
                $entityPagoLinea->setUsrCreacion($arrayRequest['strUsrCreacion']);
                $entityPagoLinea->setFeCreacion(new \DateTime('now'));
                $entityPagoLinea->setFeTransaccion($arrayRequest['dateFechaTransaccion']);

                $this->emfinan->persist($entityPagoLinea);
                $this->emfinan->flush();

                //Crea array para generar el historial del pago
                $arrayRequestPagoLineaHist = array();
                $arrayRequestPagoLineaHist['entityPagoLinea']  = $entityPagoLinea;
                $arrayRequestPagoLineaHist['jsonRequest']      = $arrayRequest['jsonRequest'];
                $arrayRequestPagoLineaHist['strProceso']       = $arrayRequest['strProceso'];
                $arrayRequestPagoLineaHist['strUsrCreacion']   = $arrayRequest['strUsrCreacion'];

                //Crea un historial del pago
                $entityPagoLineaHistorial = $this->creaObjetoHistorialPagoLinea($arrayRequestPagoLineaHist);
                $this->emfinan->persist($entityPagoLineaHistorial);
                $this->emfinan->flush();


                if ($arrayRequest['strCodEmpresa'] == '10')
                {
                    $arrayParametros['entityPagoLinea'] = $entityPagoLinea;
                    $arrayParametros['usuarioCreacion'] = $arrayRequest['strUsrCreacion'];
                    $arrayParametros['fecha'] = $arrayRequest['dateFechaTransaccion'];
                    $this->serviceInfoPago->registraPagoAutomatico($arrayParametros);
                }

                $this->emfinan->commit();
                //Busca informacion del cliente por el codigo de la empresa y la identificacion del cliente para ser devuelta en el response del json
                $arrayInfoCliente                  = $this->obtenerConsultaSaldoClientePorIdentificacion($arrayRequest['strCodEmpresa'],
                                                                                                         $arrayRequest['strIdentificacionCliente']);
                $arrayResponse['arrayInfoCliente'] = $arrayInfoCliente;
                $arrayResponse['entityPagoLinea']  = $entityPagoLinea;
            }
            else
            {
                $arrayResponse['strMensaje']      = 'No se encontro informacion del cliente.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
        }
        catch(\Exception $ex)
        {
            $this->emfinan->getConnection()->rollback();
            $arrayResponse['strMensaje']      = $ex->getMessage();
            $arrayResponse['strCodigo']       = $arrayRequest['SERVICE_NOT_AVALIABLE'];
            $arrayResponse['boolResponse']    = true;
        }
        $this->emfinan->getConnection()->close();
        return $arrayResponse;
    }//generaPagoLinea
    
    /**
     * reversaPagoLinea, metodo que reversa un pago, cambia el pago de estado Pendiete a Reversado en la entidad InfoPagoLinea y crea un registro en
     * la entidad InfoPagoLineaHistorial, el pago no podra ser reversado si no se encuentra en estado Pendiente.
     * Para realizar el reverso del pago se debe recibir la entidad del canal, secuencial recaudador, fecha transaccion, identificacion y 
     * codigo de empresa como parametros obligatorios. Retorna un array con la informacion del cliente y del pago.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 21-09-2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 14-10-2015
     * @since 1.0
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.2 20-10-2015
     * @since 1.1
     * 
     * @param array $arrayRequest[INVALID_PARAMETERS           => Definido en el Controller PagosLineaWSController
     *                            NOT_FOUND_RECORDS            => Definido en el Controller PagosLineaWSController
     *                            SERVICE_NOT_AVALIABLE        => Definido en el Controller PagosLineaWSController
     *                            TRANSACTION_REVERSED         => Definido en el Controller PagosLineaWSController
     *                            RECONCILIED_PAYMENT          => Definido en el Controller PagosLineaWSController
     *                            entityAdmiCanalPagoLinea     => Recibe la entidad del canal
     *                            strSecuencialRecaudador      => Recibe el secuencial del pago a registrar
     *                            strCodEmpresa                => recibe la empresa con la que se registrará el pago
     *                            strIdentificacionCliente     => Recibe la identificacion del cliente
     *                            dateFechaTransaccion         => Recibe la fecha en la que fue realizada la transaccion
     *                            intValor                     => Recibe el valor del pago a reversar
     *                            strUsrUltMod                 => Recibe el usuario el cual modifica el pago
     *                            strCanal                     => Recibe el nombre del canal
     *                            strAccion                    => Recibe la accion desde el json en el request, permite que un pago sea reversado
     *                                                            y eliminado en el mismo metodo]
     * 
     * @return array $arrayResponse[boolResponse        => Retorna un true cuando existio un error el cual impidio que el pago sea registrado
     *                              strMensaje          => Retorna un mensaje
     *                              strCodigo           => Retorna el codigo que fue definido en PagosLineaWSController
     *                              arrayInfoCliente    => Retorna la informacion del cliente
     *                              entityPagoLinea     => Retorna informacion sobre el pago]
     * 
     */
    public function reversaPagoLinea($arrayRequest)
    {
        $arrayResponse                  = array();
        $arrayResponse['boolResponse']  = false;
        $arrayResponse['strMensaje']    = '';
        $arrayResponse['strCodigo']     = '';
        $arrayRequest['strEstado']      = 'Reversado';
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            //Busca el pago en linea por nombre del canal y numero de referencia
            $entityPagoLinea = $this->obtenerPagoLinea($arrayRequest['strCanal'], $arrayRequest['strSecuencialRecaudador']);
            //Si no existe el pago termina el metodo con un response
            if(!$entityPagoLinea)
            {
                $arrayResponse['strMensaje']      = 'Pago a reversar no existe.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Si los datos del cliente son distintos a los enviados termina el metodo con un response
            if ($entityPagoLinea->getEmpresaId() != $arrayRequest['strCodEmpresa']
                || $entityPagoLinea->getPersona()->getIdentificacionCliente() != $arrayRequest['strIdentificacionCliente']
                || round($entityPagoLinea->getValorPagoLinea(), 2) != round($arrayRequest['intValor'], 2)
                || $entityPagoLinea->getFeCreacion()->format('Ymd') != $arrayRequest['dateFechaTransaccion']->format('Ymd'))
            {
                $arrayResponse['strMensaje']      = 'El pago contiene datos distintos a los proporcionados.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            /* Switch que valida el estado pago este en Pendiente para realizar el reverso, con cualquier otro estado
             * terminara el metodo con un response
            */
            switch($entityPagoLinea->getEstadoPagoLinea())
            {
                case 'Reversado':
                    //Valida que la accion enviada sea igual a REVERSAR_ELIMINAR para realizar la eliminacion del pago
                    if("REVERSAR_ELIMINAR" === $arrayRequest['strAccion'])
                    {
                        $this->emfinan->getConnection()->close();
                        $arrayRequest['strEstado'] = 'Eliminado';
                        //Elimina el pago
                        $arrayEliminarPago = $this->eliminaPagoLinea($arrayRequest);
                        $arrayResponse['arrayInfoCliente'] = $arrayEliminarPago['arrayInfoCliente'];
                        $arrayResponse['entityPagoLinea']  = $arrayEliminarPago['entityPagoLinea'];
                        //Si existio un error no termina el metodo y devuelve un codigo y un mensaje, caso contrario crea un historial
                        if(true === $arrayEliminarPago['boolResponse'])
                        {   
                            $arrayResponse['strMensaje']      = $arrayEliminarPago['strMensaje'];
                            $arrayResponse['strCodigo']       = $arrayEliminarPago['strCodigo'];
                            $arrayResponse['boolResponse']    = true;
                            return $arrayResponse;
                        }
                    }
                    else
                    {
                        $arrayResponse['strMensaje']      = 'El pago ya ha sido reversado anteriormente.';
                        $arrayResponse['strCodigo']       = $arrayRequest['TRANSACTION_REVERSED'];
                        $arrayResponse['boolResponse']    = true;
                    }
                    break;
                case 'Eliminado':
                    $arrayResponse['strMensaje']      = 'El pago ya ha sido Eliminado anteriormente.';
                    $arrayResponse['strCodigo']       = $arrayRequest['TRANSACTION_REVERSED'];
                    $arrayResponse['boolResponse']    = true;
                    break;
                case 'Pendiente':
                    //Cambia el estado del pago a Reversado
                    $entityPagoLinea->setEstadoPagoLinea($arrayRequest['strEstado']);
                    $entityPagoLinea->setUsrUltMod($arrayRequest['strUsrUltMod']);
                    $entityPagoLinea->setFeUltMod(new \DateTime('now'));
                    $this->emfinan->persist($entityPagoLinea);
                    $this->emfinan->flush();
                    
                    //Crea array para generar el historial del pago
                    $arrayRequestPagoLineaHist = array();
                    $arrayRequestPagoLineaHist['entityPagoLinea']  = $entityPagoLinea;
                    $arrayRequestPagoLineaHist['jsonRequest']      = $arrayRequest['jsonRequest'].' - '.$arrayRequest['strAccion'];
                    $arrayRequestPagoLineaHist['strProceso']       = $arrayRequest['strProceso'];
                    $arrayRequestPagoLineaHist['strUsrCreacion']   = $arrayRequest['strUsrUltMod'];

                    //Crea un historial del pago
                    $entityPagoLineaHistorial = $this->creaObjetoHistorialPagoLinea($arrayRequestPagoLineaHist);
                    $this->emfinan->persist($entityPagoLineaHistorial);
                    $this->emfinan->flush();

                    $this->emfinan->commit();

                    $arrayInfoCliente                  = $this->obtenerConsultaSaldoClientePorIdentificacion($arrayRequest['strCodEmpresa'],
                                                                                                             $arrayRequest['strIdentificacionCliente']);
                    $arrayResponse['arrayInfoCliente'] = $arrayInfoCliente;
                    $arrayResponse['entityPagoLinea']  = $entityPagoLinea;

                    //Valida que la accion enviada sea igual a REVERSAR_ELIMINAR para realizar la eliminacion del pago
                    if("REVERSAR_ELIMINAR" === $arrayRequest['strAccion'])
                    {
                        $arrayRequest['strEstado'] = 'Eliminado';
                        //Elimina el pago
                        $arrayEliminarPago = $this->eliminaPagoLinea($arrayRequest);
                        $arrayResponse['arrayInfoCliente'] = $arrayEliminarPago['arrayInfoCliente'];
                        $arrayResponse['entityPagoLinea']  = $arrayEliminarPago['entityPagoLinea'];
                        //Si existio un error no termina el metodo y devuelve un codigo y un mensaje, caso contrario crea un historial
                        if(true === $arrayEliminarPago['boolResponse'])
                        {   
                            $arrayResponse['strMensaje']      = $arrayEliminarPago['strMensaje'];
                            $arrayResponse['strCodigo']       = $arrayEliminarPago['strCodigo'];
                            $arrayResponse['boolResponse']    = true;
                            $this->emfinan->getConnection()->close();
                            return $arrayResponse;
                        }
                    }
                    break;
                case 'Conciliado':
                    $arrayResponse['strMensaje']      = 'El pago ya ha sido conciliado, no se puede reversar.';
                    $arrayResponse['strCodigo']       = $arrayRequest['RECONCILIED_PAYMENT'];
                    $arrayResponse['boolResponse']    = true;
                    break;
                default:
                    $arrayResponse['strMensaje']      = 'El pago no tiene estado.';
                    $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                    $arrayResponse['boolResponse']    = true;
                    break;
            }
        }
        catch(\Exception $ex)
        {
            $this->emfinan->getConnection()->rollback();
            $arrayResponse['strMensaje']      = $ex->getMessage();
            $arrayResponse['strCodigo']       = $arrayRequest['SERVICE_NOT_AVALIABLE'];
            $arrayResponse['boolResponse']    = true;
        }
        $this->emfinan->getConnection()->close();
        return $arrayResponse;
    }//reversaPagoLinea

    /**
     * reversaPagoLinea, metodo que reversa un pago, cambia el pago de estado Pendiete a Reversado en la entidad InfoPagoLinea y crea un registro en
     * la entidad InfoPagoLineaHistorial, el pago no podra ser reversado si no se encuentra en estado Pendiente.
     * Para realizar el reverso del pago se debe recibir la entidad del canal, secuencial recaudador, fecha transaccion, identificacion y 
     * codigo de empresa como parametros obligatorios. Retorna un array con la informacion del cliente y del pago.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 21-09-2015
     * 
     * @param array $arrayRequest[INVALID_PARAMETERS           => Definido en el Controller PagosLineaWSController
     *                            NOT_FOUND_RECORDS            => Definido en el Controller PagosLineaWSController
     *                            SERVICE_NOT_AVALIABLE        => Definido en el Controller PagosLineaWSController
     *                            TRANSACTION_REVERSED         => Definido en el Controller PagosLineaWSController
     *                            RECONCILIED_PAYMENT          => Definido en el Controller PagosLineaWSController
     *                            entityAdmiCanalPagoLinea     => Recibe la entidad del canal
     *                            strSecuencialRecaudador      => Recibe el secuencial del pago a registrar
     *                            strCodEmpresa                => recibe la empresa con la que se registrará el pago
     *                            strIdentificacionCliente     => Recibe la identificacion del cliente
     *                            dateFechaTransaccion         => Recibe la fecha en la que fue realizada la transaccion
     *                            intValor                     => Recibe el valor del pago a reversar
     *                            strUsrUltMod                 => Recibe el usuario el cual modifica el pago
     *                            strCanal                     => Recibe el nombre del canal
     *                            strAccion                    => Recibe la accion desde el json en el request, permite que un pago sea reversado
     *                                                            y eliminado en el mismo metodo]
     * 
     * @return array $arrayResponse[boolResponse        => Retorna un true cuando existio un error el cual impidio que el pago sea registrado
     *                              strMensaje          => Retorna un mensaje
     *                              strCodigo           => Retorna el codigo que fue definido en PagosLineaWSController
     *                              arrayInfoCliente    => Retorna la informacion del cliente
     *                              entityPagoLinea     => Retorna informacion sobre el pago]
     * 
     */
    public function reversaConciliadoPagoLinea($arrayRequest)
    {
        ini_set('max_execution_time', 60);
        $arrayResponse                  = array();
        $arrayResponse['boolResponse']  = false;
        $arrayResponse['strMensaje']    = '';
        $arrayResponse['strCodigo']     = '';
        $arrayRequest['strEstado']      = 'Reversado';
        $this->emfinan->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        try
        {
            //Busca el pago en linea por nombre del canal y numero de referencia
            $entityPagoLinea = $this->obtenerPagoLinea($arrayRequest['strCanal'], $arrayRequest['strSecuencialRecaudador']);
            //Si no existe el pago termina el metodo con un response
            if(!$entityPagoLinea)
            {
                $arrayResponse['strMensaje']      = 'Pago a reversar no existe.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                $this->emNaf->getConnection()->close();
                return $arrayResponse;
            }
            //Si los datos del cliente son distintos a los enviados termina el metodo con un response
            if ($entityPagoLinea->getEmpresaId() != $arrayRequest['strCodEmpresa']
                || $entityPagoLinea->getPersona()->getIdentificacionCliente() != $arrayRequest['strIdentificacionCliente']
                || round($entityPagoLinea->getValorPagoLinea(), 2) != round($arrayRequest['intValor'], 2)
                || $entityPagoLinea->getFeCreacion()->format('Ymd') != $arrayRequest['dateFechaTransaccion']->format('Ymd'))
            {
                $arrayResponse['strMensaje']      = 'El pago contiene datos distintos a los proporcionados. ';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                $this->emNaf->getConnection()->close();
                return $arrayResponse;
            }
            /* Switch que valida el estado pago este en Pendiente para realizar el reverso, con cualquier otro estado
             * terminara el metodo con un response
            */
            switch($entityPagoLinea->getEstadoPagoLinea())
            {
                case 'Reversado':
                case 'Eliminado':                    
                    $arrayResponse['strMensaje']      = 'El pago se encuentra en estado ' . $entityPagoLinea->getEstadoPagoLinea();
                    $arrayResponse['strCodigo']       = $arrayRequest['TRANSACTION_REVERSED'];
                    $arrayResponse['boolResponse']    = true;
                    break;
                case 'Pendiente':
                case 'Conciliado':
                    $arrayTipoDocMigra = explode(",", $this->strTipoDocMigra);
                    $arrayResultAnulacionDocs = $this->serviceInfoPago->inactivaPaLRegularizaDocsFinancieros(['pagoLinea'         => $entityPagoLinea, 
                                                                                                              'request'           => $arrayRequest,
                                                                                                              'arrayTipoDocMigra' => $arrayTipoDocMigra]);
                    error_log("[PagoLinea] reversaConciliadoPagoLinea " . json_encode($arrayResultAnulacionDocs));
                    if($arrayRequest['PROCESS_SUCCESS'] !== $arrayResultAnulacionDocs['strCodigo'])
                    {
                        $arrayResponse['strMensaje']      = 'Existio un error al reversar el pago';
                        $this->emfinan->getConnection()->rollback();
                        $this->emNaf->getConnection()->rollback();
                        if($arrayRequest['PROCESS_ERROR'] === $arrayResultAnulacionDocs['strCodigo'])
                        {
                            $this->creaHistorialByReverso(['pagoLinea' => $entityPagoLinea, 'request' => $arrayRequest]);
                            $arrayResponse['strMensaje']      = 'No es posible reversar el pago';
                        }
                        $arrayResponse['strCodigo']       = $arrayRequest['SERVICE_NOT_AVALIABLE'];
                        $arrayResponse['boolResponse']    = true;
                        $arrayRequest['strCodPlantilla']  = 'PAL_NO_REVERSAD';
                        $this->creaArrayNotificacion($arrayRequest);
                        $this->emfinan->getConnection()->close();
                        $this->emNaf->getConnection()->close();
                        return $arrayResponse;
                    }
                    //Cambia el estado del pago a Reversado
                    $entityPagoLinea->setEstadoPagoLinea($arrayRequest['strEstado']);
                    $entityPagoLinea->setUsrUltMod($arrayRequest['strUsrUltMod']);
                    $entityPagoLinea->setFeUltMod(new \DateTime('now'));
                    $entityPagoLinea->setReversado('S');
                    $this->emfinan->persist($entityPagoLinea);
                    $this->emfinan->flush();
                    //Crea array para generar el historial del pago
                    $arrayRequestPagoLineaHist = array();
                    $arrayRequestPagoLineaHist['entityPagoLinea']  = $entityPagoLinea;
                    $arrayRequestPagoLineaHist['jsonRequest']      = $arrayRequest['jsonRequest'].' - '.$arrayRequest['strAccion'];
                    $arrayRequestPagoLineaHist['strProceso']       = $arrayRequest['strProceso'];
                    $arrayRequestPagoLineaHist['strUsrCreacion']   = $arrayRequest['strUsrUltMod'];
                    //Crea un historial del pago
                    $entityPagoLineaHistorial = $this->creaObjetoHistorialPagoLinea($arrayRequestPagoLineaHist);
                    $this->emfinan->persist($entityPagoLineaHistorial);
                    $this->emfinan->flush();
                    $this->emfinan->commit();
                    $this->emNaf->commit();
                    $arrayRequest['strCodPlantilla'] = 'PAL_REVERSADO';
                    $this->creaArrayNotificacion($arrayRequest);
                    $arrayInfoCliente                  = $this->obtenerConsultaSaldoClientePorIdentificacion($arrayRequest['strCodEmpresa'],
                                                                                                             $arrayRequest['strIdentificacionCliente']);
                    $arrayResponse['arrayInfoCliente'] = $arrayInfoCliente;
                    $arrayResponse['entityPagoLinea']  = $entityPagoLinea;
                    //Valida que la accion enviada sea igual a REVERSAR_ELIMINAR para realizar la eliminacion del pago
                    if("REVERSAR_ELIMINAR" === $arrayRequest['strAccion'])
                    {
                        $arrayRequest['strEstado'] = 'Eliminado';
                        //Elimina el pago
                        $arrayEliminarPago = $this->eliminaPagoLinea($arrayRequest);
                        $arrayResponse['arrayInfoCliente'] = $arrayEliminarPago['arrayInfoCliente'];
                        $arrayResponse['entityPagoLinea']  = $arrayEliminarPago['entityPagoLinea'];
                        //Si existio un error no termina el metodo y devuelve un codigo y un mensaje, caso contrario crea un historial
                        if(true === $arrayEliminarPago['boolResponse'])
                        {   
                            $arrayResponse['strMensaje']      = $arrayEliminarPago['strMensaje'];
                            $arrayResponse['strCodigo']       = $arrayEliminarPago['strCodigo'];
                            $arrayResponse['boolResponse']    = true;
                            $this->emfinan->getConnection()->close();
                            $this->emNaf->getConnection()->close();
                            return $arrayResponse;
                        }
                    }
                    $arrayRequest['entityPagoLinea'] = $entityPagoLinea;
                    $this->enviaCortarCliente($arrayRequest);
                    break;
                default:
                    $arrayResponse['strMensaje']      = 'El pago no tiene estado.';
                    $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                    $arrayResponse['boolResponse']    = true;
                    break;
            }
        }
        catch(\Exception $ex)
        {
            $this->emfinan->getConnection()->rollback();
            $this->emNaf->getConnection()->rollback();
            $arrayResponse['strMensaje']      = $ex->getMessage();
            $arrayResponse['strCodigo']       = $arrayRequest['SERVICE_NOT_AVALIABLE'];
            $arrayResponse['boolResponse']    = true;
        }
        $this->emfinan->getConnection()->close();
        $this->emNaf->getConnection()->close();
        return $arrayResponse;
    }//reversaConciliadoPagoLinea

    private function creaArrayNotificacion($arrayRequest)
    {
        error_log("[PagoLinea] creaArrayNotificacion INI");
        $arraySendCorreo = array();
        $arraySendCorreo['strCodigo']     = $arrayRequest['strCodPlantilla'];
        $arraySendCorreo['arrayParams']   = array('strSecuencialRecaudador' => $arrayRequest['strSecuencialRecaudador'],
                                                  'strCanal'                => $arrayRequest['strCanal'],
                                                  'strCodEmpresa'           => $arrayRequest['strCodEmpresa']);
        $arraySendCorreo['strCodEmpresa']           = $arrayRequest['strCodEmpresa'];
        $arraySendCorreo['strSecuencialRecaudador'] = $arrayRequest['strSecuencialRecaudador'];
        error_log("[PagoLinea] creaArrayNotificacion secuencial: " . $arraySendCorreo['strSecuencialRecaudador']);
        $this->enviaNotificacionReverso($arraySendCorreo);
        error_log("[PagoLinea] creaArrayNotificacion FIN");
    }

    private function enviaNotificacionReverso($arrayRequest)
    {
        error_log("[PagoLinea] enviaNotificacionReverso INI");
        $strAsuntoCorreo = 'Notificación de reverso de pago';
        if(!empty($this->strAsuntoReverso))
        {
            $strAsuntoCorreo = str_replace('${strSecuencialRecaudador}', $arrayRequest['strSecuencialRecaudador'], $this->strAsuntoReverso);
            error_log("[PagoLinea] enviaNotificacionReverso asunto: " . $strAsuntoCorreo);
        }
        try
        {
            error_log("[PagoLinea] enviaNotificacionReverso generarEnvioPlantilla ini");
            //Se ejecuta el envio de la notificacion
            $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsuntoCorreo, 
                                                        null, 
                                                        $arrayRequest['strCodigo'], 
                                                        $arrayRequest['arrayParams'], 
                                                        $arrayRequest['strCodEmpresa'], 
                                                        '', 
                                                        '', 
                                                        null, 
                                                        false);
            error_log("[PagoLinea] enviaNotificacionReverso generarEnvioPlantilla fin");
        }
        catch(\Exception $ex)
        {
            error_log("[PagoLinea] enviaNotificacionReverso ERROR no se pudo enviar el correo para " 
                      . $arrayRequest['strSecuencialRecaudador'] . " Error: " . $ex->getMessage());
        }
        error_log("[PagoLinea] enviaNotificacionReverso FIN");
    }

    /**
     * enviaCortarCliente, realiza la logica para enviar a cortar un cliente por reverso de un pago
     * El cliente debio haber estardo
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 18-04-2019
     */
    private function enviaCortarCliente($arrayRequest)
    {
        error_log("[PagoLinea] enviaCortarCliente INI");
        $arrayRequestCortar                       = array();
        $arrayRequestCortar['strIp']              = '127.0.0.1';
        $arrayRequestCortar['strPunto']           = '';
        $arrayRequestCortar['intCantidadPuntos']  = 1;
        $arrayRequestCortar['usrCreacion']        = $arrayRequest['strUsrUltMod'];
        $arrayRequestCortar['intIdEmpresa']       = $arrayRequest['entityPagoLinea']->getEmpresaId();
        $arrayRequestCortar['strPrefijoEmpresa']  = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                         ->getPrefijoByCodigo($arrayRequest['entityPagoLinea']->getEmpresaId());
        $arrayPuntos                              = $this->emfinan->getRepository('schemaBundle:InfoPagoCab')
                                                         ->obtenerPuntosPorPagoRecaudacion('', $arrayRequest['entityPagoLinea']->getId());
        error_log("[PagoLinea] enviaCortarCliente puntos: " . json_encode($arrayPuntos));
        foreach($arrayPuntos as $arrayPunto):
            $arrayServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                  ->getServicioPreferenciaByPunto(['intIdPunto' => $arrayPunto['puntoId']]);
            $intIdServicio = 0;
            if(!empty($arrayServicio))
            {
                $intIdServicio  = $arrayServicio[0]['ID_SERVICIO'];
                $arrayHistorial = $this->getRowsHistorialServicio(['intRow'        => 2,
                                                                   'strOrder'      => 'DESC',
                                                                   'intField'      => 1,
                                                                   'intIdServicio' => $intIdServicio]);
                error_log("[PagoLinea] enviaCortarCliente historial: " . json_encode($arrayHistorial));
                if(!empty($arrayHistorial))
                {
                    $intCountHist = 0;
                    $boolInCorte  = false;
                    $boolActivo   = false;
                    foreach($arrayHistorial as $arrayItemHistorial):
                        if(0 == $intCountHist && 'Activo' == $arrayItemHistorial['ESTADO'])
                        {
                            $boolInCorte = true;
                        }
                        if(1 == $intCountHist && 'In-Corte' == $arrayItemHistorial['ESTADO'])
                        {
                            $boolActivo = true;
                        }
                        $intCountHist = $intCountHist + 1;
                    endforeach;
                    if($boolInCorte && $boolActivo)
                    {
                        error_log("[PagoLinea] enviaCortarCliente punto: " . $arrayPunto['puntoId']);
                        $arrayRequestCortar['strPunto'] .= $arrayPunto['puntoId'] . '|';
                    }
                }
            }
            if(!empty($arrayRequestCortar['strPunto']))
            {
                $this->serviceProcesoMasivo->cortaCliente($arrayRequestCortar);
            }
        endforeach;
        error_log("[PagoLinea] enviaCortarCliente FIN");
    }

    /**
     * creaHistorialByReverso, actualiza el campo REVERSADO en N e inserta un histarial para el pago en linea.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 18-04-2019
     * 
     * @param array $arrayRequest[
     *                          pagoLinea           => La entidad de pago en linea
     *                          strProceso          => Recibe el proceso de donde est invocado
     *                          strUsrUltMod        => Recibe el usuario de creacion
     *                          ]
     * @return entity Retorna el objeto creado de la entidad InfoPagoLineaHistorial
     */
    private function creaHistorialByReverso($arrayRequest)
    {
        $intIdPagoLinea = $arrayRequest['pagoLinea']->getId();
        error_log("[PagoLinea] creaHistorialByReverso PAL ID: $intIdPagoLinea INI");
        //Crea array para generar el historial del pago
        $arrayRequestPagoLineaHist = array();
        $arrayRequestPagoLineaHist['jsonRequest']      = 'No se pudo reversar el pago, ya que se encuentra en estado Asignado';
        $arrayRequestPagoLineaHist['strProceso']       = $arrayRequest['request']['strProceso'];
        $arrayRequestPagoLineaHist['strUsrCreacion']   = $arrayRequest['request']['strUsrUltMod'];
        $this->arrayHistPagoLinea = $arrayRequestPagoLineaHist;
        $this->intPagoLinea = $intIdPagoLinea;
        $this->emfinan->transactional(function($emFinan) 
        {
            $intIdPagoLinea = $this->intPagoLinea;
            $arrayRequestPagoLineaHist = $this->arrayHistPagoLinea;
            error_log("[PagoLinea] creaHistorialByReverso PAL ID: $intIdPagoLinea , dentro del transactional");
            $entityPagoLinea = $emFinan->getRepository('schemaBundle:InfoPagoLinea')->find($intIdPagoLinea);
            $entityPagoLinea->setUsrUltMod($arrayRequest['request']['strUsrUltMod']);
            $entityPagoLinea->setFeUltMod(new \DateTime('now'));
            $entityPagoLinea->setReversado('N');
            $arrayRequestPagoLineaHist['entityPagoLinea']  = $entityPagoLinea;
            $emFinan->persist($entityPagoLinea);
            error_log("[PagoLinea] creaHistorialByReverso PAL ID: $intIdPagoLinea , actualiza el pago en linea");
            //Crea un historial del pago
            $entityPagoLineaHistorial = $this->creaObjetoHistorialPagoLinea($arrayRequestPagoLineaHist);
            $emFinan->persist($entityPagoLineaHistorial);
            error_log("[PagoLinea] creaHistorialByReverso PAL ID: $intIdPagoLinea , crea historial");
        });
        error_log("[PagoLinea] creaHistorialByReverso PAL ID: $intIdPagoLinea END");
    }

    /**
     * conciliaPagoLinea, metodo que concilia un pago, cambia el pago de estado Pendiete a Conciliado en la entidad InfoPagoLinea y crea un registro 
     * en la entidad InfoPagoLineaHistorial, el pago no podra ser conciliado si no esta en estado Pendiente. 
     * Para realizar la conciliacion del pago se debe recibir la entidad del canal, secuencial recaudador, identificacion, fecha transaccion y 
     * codigo de empresa como parametros obligatorios. Retorna un array con la informacion del pago.
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 21-09-2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 20-10-2015
     * @since 1.0
     * 
     * @param array $arrayRequest[INVALID_PARAMETERS           => Definido en el Controller PagosLineaWSController
     *                            NOT_FOUND_RECORDS            => Definido en el Controller PagosLineaWSController
     *                            SERVICE_NOT_AVALIABLE        => Definido en el Controller PagosLineaWSController
     *                            PROCESS_ERROR                => Definido en el Controller PagosLineaWSController
     *                            TRANSACTION_REVERSED         => Definido en el Controller PagosLineaWSController
     *                            RECONCILIED_PAYMENT          => Definido en el Controller PagosLineaWSController
     *                            entityAdmiCanalPagoLinea     => Recibe la entidad del canal
     *                            strSecuencialRecaudador      => Recibe el secuencial del pago a registrar
     *                            strCodEmpresa                => recibe la empresa con la que se registrará el pago
     *                            strIdentificacionCliente     => Recibe la identificacion del cliente
     *                            dateFechaTransaccion         => Recibe la fecha en la que fue realizada la transaccion
     *                            intValor                     => Recibe el valor del pago a reversar
     *                            strUsrUltMod                 => Recibe el usuario el cual modifica el pago
     *                            strCanal                     => Recibe el nombre del canal
     *                            strAccion                    => Recibe la accion desde el json en el request, permite que un pago sea reversado
     *                                                            y eliminado en el mismo metodo]
     * 
     * @return array $arrayResponse[boolResponse        => Retorna un true cuando existio un error el cual impidio que el pago sea registrado
     *                              strMensaje          => Retorna un mensaje
     *                              strCodigo           => Retorna el codigo que fue definido en PagosLineaWSController
     *                              entityPagoLinea     => Retorna informacion sobre el pago]
     * 
     */
    public function conciliaPagoLinea($arrayRequest)
    {
        $arrayResponse                  = array();
        $arrayResponse['boolResponse']  = false;
        $arrayResponse['strMensaje']    = '';
        $arrayResponse['strCodigo']     = '';
        $arrayRequest['strEstado']      = 'Conciliado';
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            //Busca el pago en linea por nombre del canal y numero de referencia
            $entityPagoLinea = $this->obtenerPagoLinea($arrayRequest['strCanal'], $arrayRequest['strSecuencialRecaudador']);
            //Si no existe el pago termina el metodo con un response
            if(!$entityPagoLinea)
            {
                $arrayResponse['strMensaje']      = 'Pago a conciliar no existe.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Si el pago es reversado o eliminado termina el metodo con un response
            if ('Reversado' === $entityPagoLinea->getEstadoPagoLinea() || 'Eliminado' === $entityPagoLinea->getEstadoPagoLinea())
            {
                $arrayResponse['strMensaje']      = 'El pago '.$entityPagoLinea->getNumeroReferencia(). 
                                                    ' se encuentra en Estado: '.$entityPagoLinea->getEstadoPagoLinea();
                $arrayResponse['strCodigo']       = $arrayRequest['TRANSACTION_REVERSED'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            
            //Si el pago es conciliado termina el metodo con un response
            if ('Conciliado' === $entityPagoLinea->getEstadoPagoLinea())
            {
                $arrayResponse['strMensaje']      = 'El pago '.$entityPagoLinea->getNumeroReferencia(). 
                                                    ' se encuentra en Estado: '.$entityPagoLinea->getEstadoPagoLinea();
                $arrayResponse['strCodigo']       = $arrayRequest['RECONCILIED_PAYMENT'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            
            //Si el pago es diferente de Pendiente termina el metodo con un response
            if ('Pendiente' !== $entityPagoLinea->getEstadoPagoLinea())
            {
                $arrayResponse['strMensaje']      = 'El pago '.$entityPagoLinea->getNumeroReferencia(). 
                                                    ' se encuentra en Estado: '.$entityPagoLinea->getEstadoPagoLinea();
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            
            //Si los datos del cliente son distintos a los enviados termina el metodo con un response
            if ($entityPagoLinea->getEmpresaId() != $arrayRequest['strCodEmpresa']
                || $entityPagoLinea->getPersona()->getIdentificacionCliente() != $arrayRequest['strIdentificacionCliente']
                || round($entityPagoLinea->getValorPagoLinea(), 2) != round($arrayRequest['intValor'], 2)
                || $entityPagoLinea->getFeCreacion()->format('Ymd') != $arrayRequest['dateFechaTransaccion']->format('Ymd'))
            {
                $arrayResponse['strMensaje']      = 'El pago contiene datos distintos a los proporcionados.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Genera pagos y/o anticipos
            $arrayRespuesta = $this->serviceInfoPago->generarPagoAnticipoPagoLinea($entityPagoLinea, 
                                                                                    $arrayRequest['strUsrUltMod'], 
                                                                                    $arrayRequest['dateFechaTransaccion']);
            
            //PAG retorna arrayRespuesta por éxito.
            //Si no hay respuesta termina el metodo con un response
            if (empty($arrayRespuesta))
            {
                $arrayResponse['strMensaje']      = 'El pago no pudo ser conciliado.';
                $arrayResponse['strCodigo']       = $arrayRequest['PROCESS_ERROR'];
                $arrayResponse['boolResponse']    = true;
            }
            //Si es un array lo que retorna el pago ya ha sido conciliado
            else if (is_array($arrayRespuesta))
            {
                $arrayResponse['strMensaje']      = 'El pago ya ha sido conciliado anteriormente. '. 
                                                    $arrayRespuesta['codigoTipoDocumento'] . ' Existente';
                $arrayResponse['strCodigo']       = $arrayRequest['RECONCILIED_PAYMENT'];
                $arrayResponse['boolResponse']    = true;
            }
            else
            {
                //Cambia el estado del pago a conciliado
                $entityPagoLinea->setEstadoPagoLinea($arrayRequest['strEstado']);
                $entityPagoLinea->setUsrUltMod($arrayRequest['strUsrUltMod']);
                $entityPagoLinea->setFeUltMod(new \DateTime('now'));
                $this->emfinan->persist($entityPagoLinea);
                $this->emfinan->flush();
                
                //Crea array para generar el historial del pago
                $arrayRequestPagoLineaHist = array();
                $arrayRequestPagoLineaHist['entityPagoLinea']  = $entityPagoLinea;
                $arrayRequestPagoLineaHist['jsonRequest']      = $arrayRequest['jsonRequest'];
                $arrayRequestPagoLineaHist['strProceso']       = $arrayRequest['strProceso'];
                $arrayRequestPagoLineaHist['strUsrCreacion']   = $arrayRequest['strUsrUltMod'];

                //Crea un historial del pago
                $entityPagoLineaHistorial = $this->creaObjetoHistorialPagoLinea($arrayRequestPagoLineaHist);
                $this->emfinan->persist($entityPagoLineaHistorial);
                $this->emfinan->flush();

                $this->emfinan->commit();
                
                $arrayResponse['entityPagoLinea'] = $entityPagoLinea;
            }
            $strPrefijoEmpresa           = $this->emcom->getRepository('schemaBundle:InfoEmpresaGrupo')
                                                       ->getPrefijoByCodigo($entityPagoLinea->getEmpresaId());
            $arrayReactivacion           = $this->serviceProcesoMasivo->reactivarServiciosPorPagoLinea($entityPagoLinea, $strPrefijoEmpresa);
            $arrayResponse['strMensaje'] = ' Reactivacion:' . $arrayReactivacion['isReactivado'] . '/' . $arrayReactivacion['procesoMasivoId'];
        }
        catch(\Exception $ex)
        {
            $this->emfinan->getConnection()->rollback();
            $arrayResponse['strMensaje']      = $ex->getMessage();
            $arrayResponse['strCodigo']       = $arrayRequest['SERVICE_NOT_AVALIABLE'];
            $arrayResponse['boolResponse']    = true;
        }
        $this->emfinan->getConnection()->close();
        return $arrayResponse;
    }//conciliaPagoLinea
    
    /**
     * eliminaPagoLinea, metodo que elimina un pago, cambia el pago de estado Reversado a Eliminado en la entidad InfoPagoLinea y crea un registro 
     * en la entidad InfoPagoLineaHistorial, para realizar la conciliacion del pago se debe recibir la entidad del canal, secuencial recaudador, 
     * identificacion, fecha transaccion y codigo de empresa como parametros obligatorios. El pago no podra ser eliminado si no se encuentra en estado
     * Reversado. Retorna un array con la informacion del cliente y del pago
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 21-09-2015
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 20-10-2015
     * @since 1.0
     * 
     * @param array $arrayRequest[INVALID_PARAMETERS           => Definido en el Controller PagosLineaWSController
     *                            NOT_FOUND_RECORDS            => Definido en el Controller PagosLineaWSController
     *                            SERVICE_NOT_AVALIABLE        => Definido en el Controller PagosLineaWSController
     *                            PROCESS_ERROR                => Definido en el Controller PagosLineaWSController
     *                            TRANSACTION_REVERSED         => Definido en el Controller PagosLineaWSController
     *                            RECONCILIED_PAYMENT          => Definido en el Controller PagosLineaWSController
     *                            entityAdmiCanalPagoLinea     => Recibe la entidad del canal
     *                            strSecuencialRecaudador      => Recibe el secuencial del pago a registrar
     *                            strCodEmpresa                => recibe la empresa con la que se registrará el pago
     *                            strIdentificacionCliente     => Recibe la identificacion del cliente
     *                            dateFechaTransaccion         => Recibe la fecha en la que fue realizada la transaccion
     *                            intValor                     => Recibe el valor del pago a reversar
     *                            strUsrUltMod                 => Recibe el usuario con el cual se modifica el pago
     *                            strCanal                     => Recibe el nombre del canal
     *                            strAccion                    => Recibe la accion desde el json en el request, permite que un pago sea reversado
     *                                                            y eliminado en el mismo metodo]
     * 
     * @return array $arrayResponse[boolResponse        => Retorna un true cuando existio un error el cual impidio que el pago sea registrado
     *                              strMensaje          => Retorna un mensaje
     *                              strCodigo           => Retorna el codigo que fue definido en PagosLineaWSController
     *                              arrayInfoCliente    => Retorna la informacion del cliente
     *                              entityPagoLinea     => Retorna informacion sobre el pago]
     * 
     */
    public function eliminaPagoLinea($arrayRequest)
    {
        $arrayResponse                  = array();
        $arrayResponse['boolResponse']  = false;
        $arrayResponse['strMensaje']    = '';
        $arrayResponse['strCodigo']     = '';
        $arrayRequest['strEstado']      = 'Eliminado';
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            //Busca el pago en linea por nombre del canal y numero de referencia
            $entityPagoLinea = $this->obtenerPagoLinea($arrayRequest['strCanal'], $arrayRequest['strSecuencialRecaudador']);
            //Si no existe el pago termina el metodo con un response
            if(!$entityPagoLinea)
            {
                $arrayResponse['strMensaje']      = 'Pago a Eliminar no existe.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }

            //Si el pago es no reversado o eliminado termina el metodo con un response
            if ('Conciliado' === $entityPagoLinea->getEstadoPagoLinea())
            {
                $arrayResponse['strMensaje']      = 'El pago '.$entityPagoLinea->getNumeroReferencia(). 
                                                    ' se encuentra en Estado: '.$entityPagoLinea->getEstadoPagoLinea();
                $arrayResponse['strCodigo']       = $arrayRequest['RECONCILIED_PAYMENT'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }

            //Si el estado del pago es diferente a Reversado no se podra eliminar, termina el metodo con un response
            if ($entityPagoLinea->getEstadoPagoLinea() !== 'Reversado')
            {
                $arrayResponse['strMensaje']      = 'El pago '.$entityPagoLinea->getNumeroReferencia(). 
                                                    ' se encuentra en Estado: '.$entityPagoLinea->getEstadoPagoLinea();
                $arrayResponse['strCodigo']       = $arrayRequest['TRANSACTION_REVERSED'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Si los datos del cliente son distintos a los enviados termina el metodo con un response
            if ($entityPagoLinea->getEmpresaId() != $arrayRequest['strCodEmpresa']
                || $entityPagoLinea->getPersona()->getIdentificacionCliente() != $arrayRequest['strIdentificacionCliente']
                || round($entityPagoLinea->getValorPagoLinea(), 2) != round($arrayRequest['intValor'], 2)
                || $entityPagoLinea->getFeCreacion()->format('Ymd') != $arrayRequest['dateFechaTransaccion']->format('Ymd'))
            {
                $arrayResponse['strMensaje']      = 'El pago contiene datos distintos a los proporcionados.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Cambia el estado del pago a Eliminado"
            $entityPagoLinea->setEstadoPagoLinea($arrayRequest['strEstado']);
            $entityPagoLinea->setUsrUltMod($arrayRequest['strUsrUltMod']);
            $entityPagoLinea->setFeUltMod(new \DateTime('now'));
            $this->emfinan->persist($entityPagoLinea);
            $this->emfinan->flush();

            //Crea array para generar el historial del pago
            $arrayRequestPagoLineaHist = array();
            $arrayRequestPagoLineaHist['entityPagoLinea']  = $entityPagoLinea;
            $arrayRequestPagoLineaHist['jsonRequest']      = $arrayRequest['jsonRequest'];
            $arrayRequestPagoLineaHist['strProceso']       = $arrayRequest['strProceso'];
            $arrayRequestPagoLineaHist['strUsrCreacion']   = $arrayRequest['strUsrUltMod'];

            //Crea un historial del pago
            $entityPagoLineaHistorial = $this->creaObjetoHistorialPagoLinea($arrayRequestPagoLineaHist);
            $this->emfinan->persist($entityPagoLineaHistorial);
            $this->emfinan->flush();

            $this->emfinan->commit();
            //Obtiene la informacion del cliente
            $arrayInfoCliente                  = $this->obtenerConsultaSaldoClientePorIdentificacion($arrayRequest['strCodEmpresa'],
                                                                                                     $arrayRequest['strIdentificacionCliente']);
            $arrayResponse['arrayInfoCliente'] = $arrayInfoCliente;
            $arrayResponse['entityPagoLinea']  = $entityPagoLinea;
        }
        catch(\Exception $ex)
        {
            $this->emfinan->getConnection()->rollback();
            $arrayResponse['strMensaje']      = $ex->getMessage();
            $arrayResponse['strCodigo']       = $arrayRequest['SERVICE_NOT_AVALIABLE'];
            $arrayResponse['boolResponse']    = true;
        }
        $this->emfinan->getConnection()->close();
        return $arrayResponse;
    }//eliminaPagoLinea
    
    /**
     * anulaPagoLinea, metodo que permite la anulación de un pago en linea unicamente si el estado del pago es Pendiente
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 20-10-2015
     * 
     * @param array $arrayRequest[TRANSACTION_NOT_EXIST        => Definido en el Controller PagosLineaWSController
     *                            CANCEL_PAYMENT               => Definido en el Controller PagosLineaWSController
     *                            SERVICE_NOT_AVALIABLE        => Definido en el Controller PagosLineaWSController
     *                            entityAdmiCanalPagoLinea     => Recibe la entidad del canal
     *                            strSecuencialRecaudador      => Recibe el secuencial del pago a registrar
     *                            strCodEmpresa                => recibe la empresa con la que se registrará el pago
     *                            strIdentificacionCliente     => Recibe la identificacion del cliente
     *                            strUsrUltMod                 => Recibe el usuario con el cual se modifica el pago
     *                            strCanal                     => Recibe el nombre del canal
     *                            strProceso                   => Recibe el proceso de donde es invocado
     *                            jsonRequest                  => Recibe el json enviado en el request
     *                                                            y eliminado en el mismo metodo]
     * @return array $arrayResponse[boolResponse        => Retorna un true cuando existio un error el cual impidio que el pago sea registrado
     *                              strMensaje          => Retorna un mensaje
     *                              strCodigo           => Retorna el codigo que fue definido en PagosLineaWSController
     *                              arrayInfoCliente    => Retorna la informacion del cliente
     *                              entityPagoLinea     => Retorna informacion sobre el pago]
     * 
     */
    public function anulaPagoLinea($arrayRequest){
        $arrayResponse                  = array();
        $arrayResponse['boolResponse']  = false;
        $arrayResponse['strMensaje']    = '';
        $arrayResponse['strCodigo']     = '';
        $arrayRequest['strEstado']      = 'Anulado';
        $this->emfinan->getConnection()->beginTransaction();
        try
        {
            //Busca el pago en linea por nombre del canal y numero de referencia
            $entityPagoLinea = $this->obtenerPagoLinea($arrayRequest['strCanal'], $arrayRequest['strSecuencialRecaudador']);
            //Si no existe el pago termina el metodo con un response
            if(!$entityPagoLinea)
            {
                $arrayResponse['strMensaje']      = 'Pago a Anular no existe.';
                $arrayResponse['strCodigo']       = $arrayRequest['TRANSACTION_NOT_EXIST'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }

            //Si el estado es Anulado termina el metodo con un response
            if ('Anulado' === $entityPagoLinea->getEstadoPagoLinea())
            {
                $arrayResponse['strMensaje']      = 'El pago '.$entityPagoLinea->getNumeroReferencia(). 
                                                    ' se encuentra en Estado: '.$entityPagoLinea->getEstadoPagoLinea();
                $arrayResponse['strCodigo']       = $arrayRequest['CANCEL_PAYMENT'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            
            //Si el estado del pago es diferente a Pendiente termina el metodo con un response
            if ('Pendiente' !== $entityPagoLinea->getEstadoPagoLinea())
            {
                $arrayResponse['strMensaje']      = 'El pago '.$entityPagoLinea->getNumeroReferencia(). 
                                                    ' se encuentra en Estado: '.$entityPagoLinea->getEstadoPagoLinea();
                $arrayResponse['strCodigo']       = $arrayRequest['PROCESS_ERROR'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }
            //Si los datos del cliente son distintos a los enviados termina el metodo con un response
            if ($entityPagoLinea->getEmpresaId() != $arrayRequest['strCodEmpresa']
                || $entityPagoLinea->getPersona()->getIdentificacionCliente() != $arrayRequest['strIdentificacionCliente']
                || round($entityPagoLinea->getValorPagoLinea(), 2) != round($arrayRequest['intValor'], 2))
            {
                $arrayResponse['strMensaje']      = 'El pago contiene datos distintos a los proporcionados.';
                $arrayResponse['strCodigo']       = $arrayRequest['NOT_FOUND_RECORDS'];
                $arrayResponse['boolResponse']    = true;
                $this->emfinan->getConnection()->close();
                return $arrayResponse;
            }

            //Se obtiene los canales validos para actualizar el numero de referencia
            $arrayCanalesValidos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('CANALES_VALIDOS',
                                                            'FINANCIERO',
                                                            'ANULACION',
                                                            '',
                                                            'CANALES',
                                                            '',
                                                            '',
                                                            '');

            $arrayCanales = $arrayCanalesValidos['valor2']; 

            // Si el canal de la transaccion coincide con los canales validos, actualiza el numero de referencia
            if (strpos($arrayCanales, $arrayRequest['strCanal']) !== false )
            {
            $entityPagoLinea->setNumeroReferencia($entityPagoLinea->getNumeroReferencia().'=R');
            } 

            //Cambia el estado del pago a Anulado"
            $entityPagoLinea->setEstadoPagoLinea($arrayRequest['strEstado']);
            $entityPagoLinea->setUsrUltMod($arrayRequest['strUsrUltMod']);
            $entityPagoLinea->setFeUltMod(new \DateTime('now'));
            $this->emfinan->persist($entityPagoLinea);
            $this->emfinan->flush();

            //Crea array para generar el historial del pago
            $arrayRequestPagoLineaHist = array();
            $arrayRequestPagoLineaHist['entityPagoLinea']  = $entityPagoLinea;
            $arrayRequestPagoLineaHist['jsonRequest']      = $arrayRequest['jsonRequest'];
            $arrayRequestPagoLineaHist['strProceso']       = $arrayRequest['strProceso'];
            $arrayRequestPagoLineaHist['strUsrCreacion']   = $arrayRequest['strUsrUltMod'];

            //Crea un historial del pago
            $entityPagoLineaHistorial = $this->creaObjetoHistorialPagoLinea($arrayRequestPagoLineaHist);
            $this->emfinan->persist($entityPagoLineaHistorial);
            $this->emfinan->flush();

            $this->emfinan->commit();

            //Obtiene la informacion del cliente
            $arrayInfoCliente                  = $this->obtenerConsultaSaldoClientePorIdentificacion($arrayRequest['strCodEmpresa'],
                                                                                                     $arrayRequest['strIdentificacionCliente']);
            $arrayResponse['arrayInfoCliente'] = $arrayInfoCliente;
            $arrayResponse['entityPagoLinea']  = $entityPagoLinea;
        }
        catch(\Exception $ex)
        {
            $this->emfinan->getConnection()->rollback();
            $arrayResponse['strMensaje']      = $ex->getMessage();
            $arrayResponse['strCodigo']       = $arrayRequest['SERVICE_NOT_AVALIABLE'];
            $arrayResponse['boolResponse']    = true;
        }
        $this->emfinan->getConnection()->close();
        return $arrayResponse;
    }

    /**
     *
     * getRowsHistorialServicio, obtiene filas del historial de un servicio, dependiendo los parametros enviados
     *
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 26/02/2019
     * Costo query: 15 , Cardinalidad: 2
     * @param mixed arrayRequest => ['intRow'        => Numero de filas a retornar
     *                               'strOrder'      => Orden en que se recuperaran las filas
     *                               'intField'      => Columna que debe ordenar
     *                               'intIdServicio' => Id del servicio]
     * @return array $arraResponse => Array con el resultado del query
     * 
     */    
    private function getRowsHistorialServicio($arrayRequest)
    {
        $arrayResponse = array();
        try
        {
            $arrayResponse = $this->emfinan->getRepository('schemaBundle:InfoServicioHistorial')->getRowsHistorialServicio(['intRow'        => $arrayRequest['intRow'],
                                                                                                                            'strOrder'      => $arrayRequest['strOrder'],
                                                                                                                            'intField'      => $arrayRequest['intField'],
                                                                                                                            'intIdServicio' => $arrayRequest['intIdServicio']]);
        }
        catch(\Exception $ex)
        {
            error_log("InfoPagoLineaService->getRowsHistorialServicio : " . $ex->getMessage());
        }
        return $arrayResponse;
    }

    /**
     * creaObjetoHistorialPagoLinea, crea el objeto InfoPagoLineaHistorial
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 20-10-2015
     * 
     * @param array $arrayRequest[
     *                          entityPagoLinea     => Recibe la entidad InfoPagoLinea
     *                          jsonRequest         => Recibe el json completo enviado en el request
     *                          strProceso          => Recibe el proceso de donde est invocado
     *                          strUsrCreacion      => Recibe el usuario de creacion
     *                          ]
     * @return entity Retorna el objeto creado de la entidad InfoPagoLineaHistorial
     */
    public function creaObjetoHistorialPagoLinea($arrayRequest)
    {
        $entityPagoLineaHistorial = new InfoPagoLineaHistorial();
        $entityPagoLineaHistorial->setPagoLinea($arrayRequest['entityPagoLinea']);
        $entityPagoLineaHistorial->setCanalPagoLinea($arrayRequest['entityPagoLinea']->getCanalPagoLinea());
        $entityPagoLineaHistorial->setEmpresaId($arrayRequest['entityPagoLinea']->getEmpresaId());
        $entityPagoLineaHistorial->setPersona($arrayRequest['entityPagoLinea']->getPersona());
        $entityPagoLineaHistorial->setValorPagoLinea($arrayRequest['entityPagoLinea']->getValorPagoLinea());
        $entityPagoLineaHistorial->setNumeroReferencia($arrayRequest['entityPagoLinea']->getNumeroReferencia());
        $entityPagoLineaHistorial->setEstadoPagoLinea($arrayRequest['entityPagoLinea']->getEstadoPagoLinea());
        $entityPagoLineaHistorial->setObservacion($arrayRequest['jsonRequest']);
        $entityPagoLineaHistorial->setProceso($arrayRequest['strProceso']);
        $entityPagoLineaHistorial->setUsrCreacion($arrayRequest['strUsrCreacion']);
        $entityPagoLineaHistorial->setFeCreacion(new \DateTime('now'));
        return $entityPagoLineaHistorial;
    }


    /**
     * obtieneMailPorIdentificacionCliente, obtiene los correos o telefonos de las entidades InfoPuntoDataAdicional, InfoPuntoFormaContacto
     * InfoPersonaFormaContacto, enviando la identificación de cliente
     * 
     * @param int $arrayRequest[
     *                          strIdentificacionCliente  => Recibe la indentificacion del cliente
     *                          arrayEstadoPersona        => Recibe una array de estado por el cual se quiere buscar a la persona.
     *                          intLimitLengthMail        => Recibe el limite del length para concatenar los correos
     *                          strDatoDefault            => Recibe el dato por defualt en caso de que el cliente no contenga correo
     *                          strTipoDato               => Recibe el tipo de forma de contacto que quiere buscar [MAIL, FONO]
     *                          strTodos                  => Permite que el metodo devuelva los datos del primer punto encontrado o los datos de
     *                                                       de todos los puntos. ['Todos' => Retornara los correos o telefonos de todos los puntos,
     *                                                                            'PrimerPunto' => Retorna los correos o telefonos del primer punto]
     *                         ]
     * @return string $strFormaContacto Retorna el correo del cliente
     */
    public function obtieneMailFonoPorIdentificacionCliente($arrayRequest)
    {
        //Obtiene los idPersona para buscar el correo del cliente
        $entityInfoPersona  = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                          ->findBy(array('identificacionCliente' => $arrayRequest['strIdentificacionCliente'],
                                                         'estado'                => $arrayRequest['arrayEstadoPersona']));
        $arrayParametroGetCorreo                = array();
        $arrayParametroGetCorreo['strTipoDato'] = $arrayRequest['strTipoDato'];
        //Itera las personas para buscar los puntos por cliente
        foreach($entityInfoPersona as $objInfoPersona):
            //Obtiene los puntos por cliente
            $arrayPuntos = $this->emfinan->getRepository('schemaBundle:InfoPunto')->findListarPtosClientes($objInfoPersona->getId());
            //Itera los puntos obtenidos del cliente
            foreach($arrayPuntos['registros'] as $arrayPuntos):
                $arrayParametroGetCorreo['intIdPunto'] = $arrayPuntos['id'];
                $arrayMailFono                         = $this->emcom->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                                     ->getMailTelefonoByPunto($arrayParametroGetCorreo);
                //Entra si el punto tiene correos
                if(!empty($arrayMailFono['strMailFono']))
                {
                    $intSize = strlen($arrayMailFono['strMailFono'].';');
                    
                    //Si intLimitLengthMail no ha sido inicializada se setea un length de 500 por default 
                    if(empty($arrayRequest['intLimitLengthMail']))
                    {
                        $arrayRequest['intLimitLengthMail'] = 500;
                    }
                    $arrayMailFono = explode(";", $arrayMailFono['strMailFono'].';');
                    foreach($arrayMailFono as $strSingleFormaContacto):
                        
                        //Valida que string que se formara sea menor o igual al length permitido
                        if(strlen($strFormaContacto.trim($strSingleFormaContacto).';') <= $arrayRequest['intLimitLengthMail'])
                        {
                            //Si el correo es valido lo concatena
                            if (false === !filter_var($strSingleFormaContacto, FILTER_VALIDATE_EMAIL))
                            {
                                $strFormaContacto .= trim($strSingleFormaContacto).';';
                            }
                        }
                        else
                        {
                            //Hace un break para dejar de concatenar los correos
                            if('Todos' !== $arrayRequest['strTodos'])
                            {
                                //Sale del foreach que itera los correos
                                break;
                            }
                        }
                    endforeach;
                    
                    //Hace un break para dejar de iterar los puntos
                    if('Todos' !== $arrayRequest['strTodos'])
                    {
                        //Sale del foreach que itera los puntos del cliente
                        break;
                    }
                }
            endforeach;
        endforeach;
        $arrayMailFonoExplode       = explode(";", $strFormaContacto);
        $arrayUniqueMailFono        = array_unique($arrayMailFonoExplode);
        $strFormaContactoImplode    = implode(";", $arrayUniqueMailFono);
        $strFormaContacto           = trim(str_replace(" ", "", str_replace(",", ";", $strFormaContactoImplode)));
        //Pregunta si la variable de correo esta vacia para setearle el correo notificaciones_telcos@telconet.ec
        if(empty($strFormaContacto) || strlen($strFormaContacto) < 2 && 'MAIL' === $arrayRequest['strTipoDato'])
        {
            //Setea el valor por default enviado por parametro
            $strFormaContacto = $arrayRequest['strDatoDefault'];
            //Setea $strFormaContacto con el correo enviado or default
            if (false === !filter_var($arrayRequest['strDatoDefault'], FILTER_VALIDATE_EMAIL) && 'MAIL' === $arrayRequest['strTipoDato'])
            {
                $strFormaContacto = $arrayRequest['strDatoDefault'];
            } // Si el tipo de dato a buscar es mail pero no tiene el formato correcto setea un mail por default
            else if('MAIL' === $arrayRequest['strTipoDato'])
            {
                $strFormaContacto = 'notificaciones_telcos@telconet.ec';
            }
        }
        //Si $strFormaContacto esta vacio y el tipo de dato es telefono setea el valor enviado por default
        if('FONO' === $arrayRequest['strTipoDato'] && empty($strFormaContacto))
        {
            //Setea el valor por default enviado por parametro
            $strFormaContacto = $arrayRequest['strDatoDefault'];
        }
        return $strFormaContacto;
    }
}
