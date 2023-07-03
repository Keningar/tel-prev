<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoPagoDetRepository extends EntityRepository
{
    /**
     * Función que retorna si la forma de pago es valida para crear un anticipo, para ello retornará el objeto $objAdmiFormaPago si se encuentra
     * dentro de las consideraciones del query.
     * 
     * Costo Query'Es Depositable': 1
     * Costo Query'No Es Depositable': 5
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-03-2017
     * 
     * @param array $arrayParametros['intIdFormaPago'   => 'Id de la forma de pago',
     *                               'strTipoDocumento' => 'Tipo de documento a validar']
     * 
     * @return object $objAdmiFormaPago
     */    
    public function validaFormaPagoAntiipo($arrayParametros)
    {   
        $objAdmiFormaPago = null;
        
        try
        {
            if( isset($arrayParametros['intIdFormaPago']) && !empty($arrayParametros['intIdFormaPago']) 
                && isset($arrayParametros['strTipoDocumento']) && !empty($arrayParametros['strTipoDocumento']) )
            {
                $strSelect                = "SELECT AFP ";
                $strFrom                  = "FROM schemaBundle:AdmiFormaPago AFP ";
                $strWhere                 = "WHERE AFP.id = :intIdFormaPago ";
                $strWhereEsDepositable    = "AND AFP.id IN ( ".
                                            "                SELECT AFP_S.id ".
                                            "                FROM schemaBundle:AdmiFormaPago AFP_S ".
                                            "                WHERE AFP_S.esDepositable = :strEsDepositable ".
                                            "              ) ";
                $strWhereNoEsDepositable  = "AND AFP.id IN ( ".
                                            "                SELECT AFP_S.id ".
                                            "                FROM schemaBundle:AdmiFormaPago AFP_S ".
                                            "                WHERE AFP_S.esDepositable = :strNoEsDepositable ".
                                            "                AND AFP_S.tipoFormaPago IN ( ".
                                            "                                             SELECT APD.valor2 ".
                                            "                                             FROM schemaBundle:AdmiParametroCab APC, ".
                                            "                                                  schemaBundle:AdmiParametroDet APD ".
                                            "                                             WHERE APC.id = APD.parametroId ".
                                            "                                             AND APC.id = APD.parametroId ".
                                            "                                             AND APD.estado = :strEstadoActivo ".
                                            "                                             AND APC.estado = :strEstadoActivo ".
                                            "                                             AND APC.nombreParametro = :strNombreParametro ".
                                            "                                             AND APD.descripcion = :strDescripcion ".
                                            "                                             AND APD.valor1 = :strTipoDocumento ".
                                            "                                           ) ".
                                            "              ) ";
                
                $strSqlEsDepositable = $strSelect.$strFrom.$strWhere.$strWhereEsDepositable;
                $queryEsDepositable  = $this->_em->createQuery($strSqlEsDepositable);
                $queryEsDepositable->setParameter('intIdFormaPago',   $arrayParametros['intIdFormaPago']);
                $queryEsDepositable->setParameter('strEsDepositable', 'S');

                $objAdmiFormaPago = $queryEsDepositable->getOneOrNullResult();
                
                if( !is_object($objAdmiFormaPago) )
                {
                    $strSqlNoEsDepositable = $strSelect.$strFrom.$strWhere.$strWhereNoEsDepositable;
                    $queryNoEsDepositable  = $this->_em->createQuery($strSqlNoEsDepositable);
                    $queryNoEsDepositable->setParameter('intIdFormaPago',     $arrayParametros['intIdFormaPago']);
                    $queryNoEsDepositable->setParameter('strNoEsDepositable', 'N');
                    $queryNoEsDepositable->setParameter('strEstadoActivo',    'Activo');
                    $queryNoEsDepositable->setParameter('strNombreParametro', 'REPORTES_CONTABILIDAD');
                    $queryNoEsDepositable->setParameter('strTipoDocumento',   $arrayParametros['strTipoDocumento']);
                    $queryNoEsDepositable->setParameter('strDescripcion',     'TIPOS_FORMA_PAGO');

                    $objAdmiFormaPago = $queryNoEsDepositable->getOneOrNullResult();
                }//( !is_object($objAdmiFormaPago) )
            }//( isset($arrayParametros['intIdFormaPago']) && !empty($arrayParametros['intIdFormaPago']) )...
            else
            {
                throw new \Exception("Se debe enviar el id forma de pago para validar la creación de un anticipo.");
            }
        }
        catch(\Exception $ex)
        {
            throw($ex);
        }
        
        return $objAdmiFormaPago;
    }
    
    
    /**
     * Devuelve un registro de la INFO_PAGO_CAB el cual cumple con los parámetros de ser un ANTICIPO.
     * 
     * Costo Query: 10
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-09-2016
     * 
     * @param array $arrayParametros['intIdPagoDet', 'strEstadoActivo', 'strNombreParametro']
     * @return $objInfoPagoCab INFO_PAGO_CAB
     */    
    public function findPagoCab($arrayParametros)
    {   
        $objInfoPagoCab = null;
        
        try
        {
            if( !empty($arrayParametros) )
            {
                if( !empty($arrayParametros['intIdPagoDet']) && !empty($arrayParametros['strEstadoActivo']) 
                    && !empty($arrayParametros['strNombreParametro']) )
                {
                    $strSql = "SELECT ipc ".
                              "FROM schemaBundle:InfoPagoDet ipd, ".
                              "     schemaBundle:InfoPagoCab ipc, ".
                              "     schemaBundle:AdmiTipoDocumentoFinanciero atdf ".
                              "WHERE ipd.pagoId = ipc.id ".
                              "  AND ipc.tipoDocumentoId = atdf.id ".
                              "  AND ipd.referenciaId IS NULL ".
                              "  AND atdf.codigoTipoDocumento IN ( ".
                              "                                      SELECT apd.valor1 ".
                              "                                      FROM schemaBundle:AdmiParametroDet apd, ".
                              "                                           schemaBundle:AdmiParametroCab apc ".
                              "                                      WHERE apd.parametroId = apc.id ".
                              "                                        AND apc.nombreParametro = :strNombreParametro ".
                              "                                        AND apc.estado = :strEstadoActivo ".
                              "                                  ) ".
                              "  AND ipd.id = :intIdPagoDet ";


                    $query = $this->_em->createQuery($strSql);

                    $query->setParameter('intIdPagoDet',        $arrayParametros['intIdPagoDet']);
                    $query->setParameter('strEstadoActivo',     $arrayParametros['strEstadoActivo']);
                    $query->setParameter('strNombreParametro',  $arrayParametros['strNombreParametro']); 

                    $objInfoPagoCab = $query->getOneOrNullResult();
                }/*( !empty($arrayParametros['intIdPagoDet']) && !empty($arrayParametros['strEstadoActivo']) 
                    && !empty($arrayParametros['strNombreParametro']) )*/
                else
                {
                    throw new \Exception("Falta uno de los parámetros obligatorios 'intIdPagoDet', 'strEstadoActivo', 'strNombreParametro' para ".
                                         "realizar la consulta del documento");
                }
            }//( !empty($arrayParametros) )
            else
            {
                throw new \Exception("No existen los parámetros adecuados para realizar la consulta del documento");
            }
        }
        catch(\Exception $ex)
        {
            throw($ex);
        }
        
        return $objInfoPagoCab;
    }
    
    
    /**
     * Devuelve los detalles de pagos que sean forma de pago Retencion 8% o 2% y esten en estado Cerrado y Activo
     * @param int $idFactura
     * @return array
     */    
    public function findPagoDetRetencionPorPago($idFactura)
    {   
        $query = $this->_em->createQuery("SELECT pd
        FROM 
                schemaBundle:InfoPagoDet pd, schemaBundle:AdmiFormaPago fp
		WHERE
                pd.referenciaId=:idFactura AND 
                pd.formaPagoId=fp.id AND 
                fp.codigoFormaPago in (:codigoFormaPago) AND
                pd.estado in(:estados)");
        $codigoFormaPago=array('RF8','RF2');
        $estados=array('Cerrado' , 'Activo');   
        $query->setParameter('estados',$estados);
        $query->setParameter('codigoFormaPago',$codigoFormaPago);
        $query->setParameter('idFactura',$idFactura);        
        $datos = $query->getResult();
        return $datos;
    }
    /**
     * Documentacion para funcion 'getSumPorVariosId'
     * Devuelve la suma del valor de los pagos
     * @param int $arr_pagos - ids de pagos
     * @return float
     * @author amontero@telconet.ec
     * @since 03-02-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 13-12-2016 - Se modifica función para que consulte sólo los detalles que correspondan a los id seleccionados por el usuario pero
     *                           no hayan sido depositados con anterioridad y no tengan asociado un id de depósito.
     */            
    public function getSumPorVariosId($arr_pagos)
    {   
        $floatValorTotal = 0;
        
        $strSelect = "SELECT SUM( NVL(IPD.VALOR_PAGO, 0) ) TOTAL ";
        $strFrom   = "FROM DB_FINANCIERO.INFO_PAGO_DET IPD ";
        $strWhere  = "WHERE IPD.DEPOSITADO = :strDepositado
                      AND IPD.DEPOSITO_PAGO_ID IS NULL ";
        
        //debido a que la clausula in de oracle soporta
        //hasta 1000 valores, se parte el arreglo en fragmentos de 1000 
        //para poder obtener la suma de los pagos.
        $arraySplitPagos = array_chunk($arr_pagos,1000);
        
        for ($i=0; $i<count($arraySplitPagos); $i++)
        {
            $strSql = $strSelect.$strFrom.$strWhere."AND IPD.ID_PAGO_DET IN (:arrayPagosDetId) ";
            
            $rsmBuilder = new ResultSetMappingBuilder($this->_em);
            $query      = $this->_em->createNativeQuery(null, $rsmBuilder);
            $query->setSQL($strSql);
            $query->setParameter('arrayPagosDetId', $arraySplitPagos[$i]);
            $query->setParameter('strDepositado',   'N');
            
            $rsmBuilder->addScalarResult('TOTAL', 'total', 'integer');
            
            $floatSumaObtenida = $query->getSingleScalarResult();

            if( floatval($floatSumaObtenida) > 0 )
            {
                $floatValorTotal = $floatValorTotal + floatval($floatSumaObtenida);
            }
        }
        
        return $floatValorTotal; 
    }
    /**       
     * @since 1.0
     * Devuelve los detalles de pagos que no esten anulados de un punto cliente
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 19-09-2016   
     * Se agrega join para obtener el tipo de documento en el query para determinar si es :
     * Pago, Anticipo o Anticipo por cruce
     * 
     * @param int $idPtoCliente
     * @param string $estados
     * @param string $criterio
     * @return array
     */        
    private function listarDetallesDePagoPorPuntoCriterio($idPtoCliente,$estados,$criterio)
    {
        $query = $this->_em->createQuery(
        "SELECT 
            p.numeroPago, 
            pd.valorPago, 
            pd.id, 
            pd.depositado,
            fp.codigoFormaPago,
            fp.esDepositable,
            doc.codigoTipoDocumento,
            doc.nombreTipoDocumento,
            pd.estado
            
        FROM 
            schemaBundle:InfoPagoDet pd,
            schemaBundle:InfoPagoCab p, 
            schemaBundle:AdmiFormaPago fp,
            schemaBundle:AdmiTipoDocumentoFinanciero doc
        WHERE
            pd.formaPagoId=fp.id 
            AND p.tipoDocumentoId=doc.id
            AND pd.estado ".$criterio." (:estados) 
            AND pd.pagoId=p.id 
            AND p.puntoId=:idPtoCliente 
            ORDER BY p.numeroPago DESC");
        $query->setParameter('idPtoCliente',$idPtoCliente);
        $query->setParameter('estados',$estados);
        $datos = $query->getResult();
        return $datos;        
    }
    /**
     * Devuelve si un Anticipo por cruce se origino de un Anticipo al cual le aplicaron NDI por un valor menor 
     * al valor del Anticipo Original y si este se encuentra en estado Cerrado.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 20-09-2016              
     * @param  int    $intPagoDetId      Id de Pago detalle
     * @param  string $strTipoDocumento  Codigo de Documento
     * @param  string $strEstado         Estado
     * @return object
     */    
    public function getAnticipoPorCrucePorPagoDetIdPorEstado($intPagoDetId,$strTipoDocumento, $strEstado)
    {
        $objQueryData = $this->_em->createQuery();
        $strQuery     = "SELECT pc
                         FROM 
                           schemaBundle:InfoPagoCab pc, 
                           schemaBundle:InfoPagoDet pd,
                           schemaBundle:InfoPagoCab ac,
                           schemaBundle:AdmiTipoDocumentoFinanciero td
                         WHERE pc.id=pd.pagoId 
                         AND pd.id=:pagoDetId 
                         AND pc.anticipoId=ac.id
                         AND pc.tipoDocumentoId=td.id 
                         AND td.codigoTipoDocumento=:tipoDocumento
                         AND ac.estadoPago =:estado";
        
        $objQueryData->setParameter('pagoDetId', $intPagoDetId);
        $objQueryData->setParameter('tipoDocumento', $strTipoDocumento);
        $objQueryData->setParameter('estado', $strEstado);
        $objQueryData->setDQL($strQuery);
        $objDatos = $objQueryData->getResult();
        return $objDatos;
    }

    /**
     * Documentación para el método 'listarDetallesDePagoPorPuntoIn'.
     *
     * Sirve para el retorno de los pagos incluyendo el estado enviado
     *
     * @param mixed $idPtoCliente Punto cliente enviado.
     * @param mixed $estados Estado enviado para cunsultar.
     *
     * @return mixed Listado de detalles de pagos segun el criterio.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 18-06-2014
     */
    public function listarDetallesDePagoPorPuntoIn($idPtoCliente,$estados)
    {
        return $this->listarDetallesDePagoPorPuntoCriterio($idPtoCliente,$estados,'IN');
    }

    /**
     * Documentación para el método 'listarDetallesDePagoPorPuntoNotIn'.
     *
     * Sirve para el retorno de los pagos excluyendo los estado enviado
     *
     * @param mixed $idPtoCliente Punto cliente enviado.
     * @param mixed $estados Estado enviado para consultar.
     *
     * @return mixed Listado de detalles de pagos segun el criterio.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 18-06-2014
     */
    public function listarDetallesDePagoPorPuntoNotIn($idPtoCliente,$estados)
    {
        return $this->listarDetallesDePagoPorPuntoCriterio($idPtoCliente,$estados,'NOT IN');
    }
    
    /**
    * Documentación para el método 'checkPagostoAnular'.
    * Este metodo verifica si un pago o naticipo puede ser anulado
    *
    * @param  Integer $IdPago        Obtiene el Id del pago
    * @return Integer $out_Resultado Retorna con 1 si el pago o anticipo puede ser anulado o 0 en caso contrario.
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 22-04-2014
    */
    public function checkPagostoAnular($IdPago)
    {
        $out_msn_Error = null;
        $out_msn_Error = str_pad($out_msn_Error, 1000, " ");
        $sql = "BEGIN FNCK_CONSULTS.FINP_PAGOPORANULAR(:IdPago, :Resultado, :msnError); END;";
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindParam('IdPago', $IdPago);
        $stmt->bindParam('Resultado', $out_Resultado);
        $stmt->bindParam('msnError', $out_msn_Error);
        $stmt->execute();
        return $out_Resultado;
    }

    /**
    * Documentación para el método 'anulaPagos'.
    * Este metodo anula el pago o anticipo
    *
    * @param  Integer $IdPago        Obtiene el Id del pago
    * @param  Integer $Pn_Motivo     Obtiene el Id Motivo
    * @param  Integer $usuario       Obtiene el usuario quien actualiza
    * @param  Integer $Observacion   Obtiene la observacion ingresada por el usuario
    * @return Integer $out_Resultado Retorna un ok si la actualizacion fue correcta y un Error en caso contrario.
    *
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.0 22-04-2014
    */
    public function anulaPagos($IdPago, $Pn_Motivo, $usuario, $Observacion)
    {
        $out_msn_Error = null;
        $out_msn_Error = str_pad($out_msn_Error, 1000, " ");
        //llama al metodo que verifica si el pago o anticipo se puede anular, caso contrario Devuelve como  mensaje "Error"
        if($this->checkPagostoAnular($IdPago) != 0)
        {
            $sql = "BEGIN FNCK_TRANSACTION.FINP_ANULAPAGO(:IdPago, :usuario, :Pn_Motivo, :Observacion, :msnError); END;";
            $stmt = $this->_em->getConnection()->prepare($sql);
            $stmt->bindParam('IdPago', $IdPago);
            $stmt->bindParam('usuario', $usuario);
            $stmt->bindParam('Pn_Motivo', $Pn_Motivo);
            $stmt->bindParam('Observacion', $Observacion);
            $stmt->bindParam('msnError', $out_msn_Error);
            $stmt->execute();
        }
        else
        {
            $out_msn_Error = 'Error';
        }
        if(!$out_msn_Error)
        {
            $out_Resultado = 'OK';
        }
        else
        {
            $out_Resultado = trim($out_msn_Error);
        }
        return $out_Resultado;
    }

    
    
    /**
     * Documentación para el método 'contabilizarPagosAnticipo'.
     * Este metodo contabiliza el pago o anticipo
     *
     * @param  Integer $empresaCod     Obtiene el Id de la empresa
     * @param  Array   $arrayPagosDet  Obtiene un arreglo con los detalles de pago
     * @return Integer $out_Resultado  Retorna un ok si la actualizacion fue correcta y un Error en caso contrario.
     *
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0 26-12-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 08-12-2016 - Se cambia el llamado del procedure 'PROCESAR_PAGO_ANTICIPO_MANUAL' para que reciba como parámetro una bandera que
     *                          indique si el detalle del pago generó un anticipo, para que ambos sean contabilizados como un solo asiento contable.
     * 
     * Se recibe parámetro $objParametros para poder reutilizar la función insertError() en el repositorio
     * y se cambia de nombre la variable $empresaCod por $intEmpresaCod por problemas con SONAR.
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.2 07-11-2019
     * @since 1.1
     */
    public function contabilizarPagosAnticipo($intEmpresaCod, $arrayPagosDet, $objParametros)
    {
        $serviceUtil = $objParametros['serviceUtil'];
        if( !empty($arrayPagosDet) )
        {
            foreach($arrayPagosDet as $arrayDetallePago)
            {
                if( isset($arrayDetallePago['intIdPagoDet']) && !empty($arrayDetallePago['intIdPagoDet']) )
                {
                    $intIdPagoDet      = $arrayDetallePago['intIdPagoDet'];
                    $strGeneraAnticipo = 'N';
                    
                    if( isset($arrayDetallePago['strGeneraAnticipo']) && !empty($arrayDetallePago['strGeneraAnticipo']) )
                    {
                        $strGeneraAnticipo = $arrayDetallePago['strGeneraAnticipo'];
                    }
                        
                    $out_msn_Error = null;
                    $out_msn_Error = str_pad($out_msn_Error, 1000, " ");
                    $out_Resultado = '[Proceso contable OK]';
                   
                    $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoPagoDetRepository/contabilizarPagosAnticipo - '.
                        'FNKG_CONTABILIZAR_PAGO_MANUAL.PROCESAR_PAGO_ANTICIPO_MANUAL '.
                        'con los sgtes parametros... Codigo de empresa: ' . 
                        $intEmpresaCod . ', pagoDetId: ' . $intIdPagoDet . 
                        ', strGeneraAnticipo: ' . $strGeneraAnticipo, 
                        'telcos', 
                        '127.0.0.1' );

                    $sql = "BEGIN DB_FINANCIERO.FNKG_CONTABILIZAR_PAGO_MANUAL.PROCESAR_PAGO_ANTICIPO_MANUAL( :empresaCod, ".
                                                                                                            ":pagoDetId, ".
                                                                                                            ":strGeneraAnticipo, ".
                                                                                                            ":msnError ); END;";
                    $stmt = $this->_em->getConnection()->prepare($sql);
                    $stmt->bindParam('empresaCod',        $intEmpresaCod );
                    $stmt->bindParam('pagoDetId',         $intIdPagoDet);
                    $stmt->bindParam('strGeneraAnticipo', $strGeneraAnticipo);
                    $stmt->bindParam('msnError',          $out_msn_Error);
                    $stmt->execute();

                    $serviceUtil->insertError( 'Telcos+', 
                        'LOG DE EJECUCION DE PAGOS', 
                        'InfoPagoDetRepository/contabilizarPagosAnticipo - DESPUES DE EJECUTAR: '.
                        'FNKG_CONTABILIZAR_PAGO_MANUAL.PROCESAR_PAGO_ANTICIPO_MANUAL '.
                        'con los sgtes parametros... Codigo de empresa: ' . 
                        $intEmpresaCod . ', pagoDetId: ' . $intIdPagoDet . 
                        ', strGeneraAnticipo: ' . $strGeneraAnticipo, 
                        'telcos', 
                        '127.0.0.1' ); 
                    
                    if(strtoupper($out_msn_Error)!='PROCESO OK')
                    {
                        $out_Resultado = '               [Error en proceso contable:'.$out_msn_Error.']';
                    }
                }//( isset($arrPagosDetId['intIdPagoDet']) && !empty($arrPagosDetId['intIdPagoDet']) )
            }//for($i=0; $i < count($arrPagosDetId); $i++)
        }//( !empty($arrPagosDetId) )
        
        return $out_Resultado;
    }    
  
    /**
     * Documentación para el método 'contabilizarCruceAnticipo'.
     * Este metodo contabiliza el cruce de un anticipo
     *
     * @param  array $arrayParametros[ 'intIdPagoCab'  => 'Id del pago cabecera',
     *                                 'strEmpresaCod' => 'Código de la empresa en session' ]
     * 
     * @return Integer $strMensajeResultado Retorna un ok si el asiento fue correcto y un Error en caso contrario.
     *
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0 09-06-2016
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 03-08-2017 - Se agrega el parámetro 'strEmpresaCod' al método para contabilizar los cruces por empresa.
     */
    public function contabilizarCruceAnticipo( $arrayParametros )
    {
        $strMensajeResultado = '[Proceso contable OK]';
        $intIdPagoCab        = ( isset($arrayParametros['intIdPagoCab']) && !empty($arrayParametros['intIdPagoCab']) )
                               ? $arrayParametros['intIdPagoCab'] : 0;
        $strEmpresaCod       = ( isset($arrayParametros['strEmpresaCod']) && !empty($arrayParametros['strEmpresaCod']) )
                               ? $arrayParametros['strEmpresaCod'] : null;

        try
        {
            if ( $intIdPagoCab > 0 && !empty($strEmpresaCod) )
            {
                $strMensajeError = null;
                $strMensajeError = str_pad($strMensajeError, 1000, " ");

                $strSql = "BEGIN FNKG_CONTABILIZAR_CRUCEANT.PROCESA_CRUCE_ANTICIPO( :strEmpresaCod, :intIdPagoCab, :strMensajeError); END;";
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('strEmpresaCod',   $strEmpresaCod);
                $objStmt->bindParam('intIdPagoCab',    $intIdPagoCab);
                $objStmt->bindParam('strMensajeError', $strMensajeError);
                $objStmt->execute();

                if ( !empty($strMensajeError) && strtoupper( $strMensajeError ) != 'PROCESO OK' )
                {
                    $strMensajeResultado = ' [Error en proceso contable:'.$strMensajeError.'] ';
                }
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuado para realizar la contabilización del cruce de anticipos');
            }
        }
        catch (\Exception $e)
        {
            throw($e);
        }

        return $strMensajeResultado;
    }    

    /**
    * Documentación para el método 'contabilizarAnulacion'.
    * Este metodo contabiliza la anulacion de un
    *
    * @param  Integer $pagoId     Es el id del pago que se desea anular
    * @return Integer $out_Resultado Retorna un ok si el asiento fue correcto y un Error en caso contrario.
    *
    * @author Andres Montero <amontero@telconet.ec>
    * @version 1.0 19-06-2016
    */
    public function contabilizarAnulacion($intPagoId)
    {
        $out_msn_Error = null;
        $out_msn_Error = str_pad($out_msn_Error, 1000, " ");
        $out_Resultado = '[Proceso contable para Anulacion: OK]';
        if($intPagoId != null)
        {
            $sql = "BEGIN FNKG_CONTABILIZAR_ANULARPAG.PROCESA_ANULACION(:pagoId, :msnError); END;";
            $stmt = $this->_em->getConnection()->prepare($sql);
            $stmt->bindParam('pagoId', $intPagoId);
            $stmt->bindParam('msnError', $out_msn_Error);
            $stmt->execute();
        }
        if (strtoupper($out_msn_Error)=='NOTDATAFOUND')
        {
            $out_Resultado='';
        }    
        else if(strtoupper($out_msn_Error)=='PROCESO OK')
        {
            $out_Resultado = $out_Resultado;
        }
        else
        {
            $out_Resultado = ' [Error en proceso contable:'.$out_msn_Error.']';
        }
        
        return $out_Resultado;
    }     
    
    /**
    * Documentación para el método 'anulaMigracion'.
    * Este metodo va a reversar o a eliminar el pago en migraciond de NAF.
    *
    * @param  Integer $intPagoId      Es el id del pago que se desea anular
    * @param  String  $strUsuario     Usuario en sesion
    *
    * @return String  $Objout_Resultado Retorna un ok si el asiento fue correcto y un Error en caso contrario.
    *
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.0 09-08-2017
    */
    public function anulaMigracion($intPagoId, $strUsuario)
    {
        $objOutMsnError = null;
        $objOutMsnError = str_pad($objOutMsnError, 1000, " ");
        
        if($intPagoId != null)
        {
            $sql = "BEGIN FNKG_CONTABILIZAR_ANULARPAG.P_ANULA_MIGRACION(
                                                                        :Pn_PagoDetId,
                                                                        :Pv_UsrAnula,
                                                                        :msnError
                                                                        ); 
                     END;";
            
            $stmt = $this->_em->getConnection()->prepare($sql);
            $stmt->bindParam('Pn_PagoDetId',$intPagoId);
            $stmt->bindParam('Pv_UsrAnula',$strUsuario);
            $stmt->bindParam('msnError', $objOutMsnError);
            $stmt->execute();
        }
        
        if ( strlen(trim($objOutMsnError)) > 0 )
        {
            $objOutMsnError = ' [Error en proceso contable:'.$objOutMsnError.']';
        }
        else
        {
            $objOutMsnError = '';
        }
        
        return $objOutMsnError;
    }
    
    /**
    * Documentación para el método 'agregaHistorialPagosDependientes'.
    * Este metodo agrega el historial de dependiencia a los pagos relacionados al pago que fue anulado.
    *
    * @param  array $arrayParametros[ 'intIdPagoCab'           => 'Id del pago cabecera',
    *                                 'strCodigoTipoDocumento' => 'Codigo del documento',
    *                                 'strNumeroPago'          => 'Numero del pago',
    *                                 'strEmpresaId'           => 'Id empresa']
    *
    * @return Integer $Objout_Resultado Retorna vacio si el ingreso del historial fue correcto y un Error en caso contrario.
    *
    * @author Ricardo Coello Quezada <rcoello@telconet.ec>
    * @version 1.0 14-08-2017
    */
    public function agregaHistorialPagosDependientes($arrayParametros)
    {
        $obj_out_msn_Error =  '';
        
        if($arrayParametros && count($arrayParametros)>0)
        {
            $strSql = "BEGIN FNKG_TRANSACTION_CONTABILIZAR.P_MARCA_PAGOS_DEPENDIENTES(
                                                                        :Pn_IdPago,
                                                                        :Pv_CodigoTipoDocumento,
                                                                        :Pv_NumeroPago,
                                                                        :Pn_EmpresaId,
                                                                        :Pv_MsnError
                                                                        ); 
                     END;";
                
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pn_IdPago', $arrayParametros['intIdPago']);
            $objStmt->bindParam('Pv_CodigoTipoDocumento',$arrayParametros['strCodigoTipoDocumento']);
            $objStmt->bindParam('Pv_NumeroPago', $arrayParametros['strNumeroPago']);
            $objStmt->bindParam('Pn_EmpresaId',  $arrayParametros['strEmpresaId']);
            $objStmt->bindParam('Pv_MsnError',   $obj_out_msn_Error);
            $objStmt->execute();
        
            if(!empty($obj_out_msn_Error))
            {
                $obj_out_msn_Error = ' [Error al agregar historial a pagos dependientes: '.$obj_out_msn_Error.']';
            } 
       }    
        return $obj_out_msn_Error;
    }
/**
     * Documentación para el método 'contabilizarAsignaAnticipoPunto'.
     * Este metodo contabiliza la asiganción punto cliente al pago anticipo sin cliente
     *
     * @param  array $arrayParametros[ 'intIdPagoCab'  => 'Id del pago cabecera',
     *                                 'strEmpresaCod' => 'Código de la empresa en session' ]
     * 
     * @return Integer $strMensajeResultado Retorna un ok si el asiento fue correcto y un Error en caso contrario.
     *
     * @author Luis Lindao <llindao@telconet.ec>
     * @version 1.0 06-01-2018
     */
    public function contabilizarAsignaAnticipoPunto( $arrayParametros )
    {
        $strMensajeResultado = 'OK';
        $intIdPagoCab        = ( isset($arrayParametros['intIdPagoCab']) && !empty($arrayParametros['intIdPagoCab']) )
                               ? $arrayParametros['intIdPagoCab'] : 0;
        $strEmpresaCod       = ( isset($arrayParametros['strEmpresaCod']) && !empty($arrayParametros['strEmpresaCod']) )
                               ? $arrayParametros['strEmpresaCod'] : null;
        try
        {
            if ( $intIdPagoCab > 0 && !empty($strEmpresaCod) )
            {
                $strMensajeError = null;
                
                $strSql = "BEGIN FNKG_PAGO_LINEA_RECAUDACION.P_CONTABILIZAR_ASIGNA_ANT_PTO( :Pv_Nocia, :Pn_IdPagoCab, :Pv_MensajeError); END;";
                $objStmt = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('Pv_NoCia',   $strEmpresaCod);
                $objStmt->bindParam('Pn_IdPagoCab', $intIdPagoCab);
                $objStmt->bindParam('Pv_MensajeError', $strMensajeError);
                $objStmt->execute();
        
                if ( !empty($strMensajeError) && strtoupper( $strMensajeError ) != 'PROCESO OK' )
                {
                    $strMensajeResultado = ' [Error en proceso contable:'.$strMensajeError.'] ';
                    
                }
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuado para realizar la contabilización de asignación anticipo a Punto');
            }
        }
        
        catch (\Exception $e)
        {
            throw($e);
        }

        return $strMensajeResultado;
    }


    /**
     * 
     * Metodo para verificar si la forma de pago de la retencion ha sido ingresada 
     * 
     * Costo 17
     * 
     * @param  array $arrayParametros[ 'intIdPersona'  => 'Id de la persona',
     *                                 'intNumRef'     => 'Numero de referencia' ]
     *                                 'arrayEstadosDetallePago' => 'Estados del detalle del pago' ]
     * @return String array Array de retenciones
     * 
     * @author Gustavo Narea <gnarea@telconet.ec>
     * @version 1.0 06-09-2021
     */
    public function findRetencion($arrayParametros)
    {  
     try 
     {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        
        $objQuery      = $this->_em->createNativeQuery(null, $objRsm);

        $strSelect      = " SELECT ipd.id_pago_det AS IDPAGODET, ipd.forma_pago_id AS IDFORMAPAGO ";
        $strFrom        = " FROM db_financiero.info_pago_cab ipc,
                            db_financiero.info_pago_det ipd,
                            db_comercial.info_punto ipu,
                            db_comercial.info_persona_empresa_rol iper ";
        $strWhere       = " WHERE ipd.pago_id = ipc.id_pago
                            AND ipc.punto_id = ipu.id_punto
                            AND ipu.persona_empresa_rol_id = iper.id_persona_rol ";
        
        if (isset($arrayParametros["intIdPersona"]))
        {
            $intIdPersona = $arrayParametros["intIdPersona"];
            $strWhere = $strWhere . " AND iper.persona_id = :intIdPersona ";
        }
        
        if (isset($arrayParametros["intNumRef"]))
        {
            $intNumRef = $arrayParametros["intNumRef"];
            $strWhere .= "AND ipd.numero_referencia=:intNumRef ";
        }
        
        if (isset($arrayParametros["arrayEstadosDetallePago"]))
        {
            $arrayEstadosDetallePago = $arrayParametros["arrayEstadosDetallePago"];
            $strWhere .= "AND ipd.estado in (:arrayEstadosDetallePago) ";
        }

       
        $strSql = $strSelect.$strFrom.$strWhere;
        $objQuery->setSQL($strSql);

        if(!is_null($intIdPersona))
        {
            $objQuery->setParameter('intIdPersona',$intIdPersona);
        }
        if(!is_null($intNumRef))
        {
            $objQuery->setParameter('intNumRef',$intNumRef);
        }
        if(!is_null($arrayEstadosDetallePago))
        {
            $objQuery->setParameter('arrayEstadosDetallePago',$arrayEstadosDetallePago);
        }
        
        
        $objRsm->addScalarResult('IDPAGODET', 'id_pago_det', 'integer');
        $objRsm->addScalarResult('IDFORMAPAGO', 'id_forma_pago', 'integer');
        
        $arrayData = $objQuery->getResult();
        return $arrayData;
        
     } catch(\Exception $e)
     {
         return "Errror: ".$e->getMessage();
     }
    }
}