<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiCicloRepository extends EntityRepository
{
    /**
     * getCiclosFactByEmpresaEstado
     * Obtiene la informacion de los ciclos de Facturación por empresa en sesion y estados.   
     * Se obtiene los Ciclos de Facturación según array de estados a consultar y se verifica que existan clientes atados al ciclo.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 08-09-2017
     * Costo: 10
     * @param array $arrayParametros[
     *                                'strEmpresaCod'      => Recibe el codigo de la empresa en sesión.
     *                                'arrayEstadoCiclo'   => Recibe estados de los Ciclos de Facturacion   
     *                                'strTipo'            => recibe Tipo :  Consulta
     *                              ]                                    
     * @return $arrayResultado
     */
    public function getCiclosFactByEmpresaEstado($arrayParametros)
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        
        $strQueryCount = " SELECT COUNT(*) AS CANTIDAD";
        $strQuery      = " SELECT CI.ID_CICLO, CI.NOMBRE_CICLO";
 
        $strFromQuery  = " FROM DB_FINANCIERO.ADMI_CICLO CI                                  
                           WHERE 
                           CI.EMPRESA_COD = :strEmpresaCod
                           AND CI.ESTADO IN (:arrayEstadoCiclo) ";

        if(isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo'])
           && $arrayParametros['strTipo'] == 'Consulta')
        {
            $strQueryTipo  =  " AND EXISTS (SELECT 1
                                      FROM  DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                                      DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC, 
                                      DB_COMERCIAL.ADMI_CARACTERISTICA CA
                                      WHERE IPER.ID_PERSONA_ROL                                    = IPERC.PERSONA_EMPRESA_ROL_ID
                                      AND IPERC.CARACTERISTICA_ID                                  = CA.ID_CARACTERISTICA
                                      AND CA.DESCRIPCION_CARACTERISTICA                            = :strCaracteristica 
                                      AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(IPERC.VALOR,'^\d+')),0) = CI.ID_CICLO
                                      AND IPERC.ESTADO                                             = :strEstadoPerEmpCarac
                                      )
                       ";        
        }
        $strOrderByQuery = " ORDER BY CI.ID_CICLO DESC ";                    

        $objRsmCount->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        $objRsm->addScalarResult('ID_CICLO', 'intIdCiclo', 'integer');
        $objRsm->addScalarResult('NOMBRE_CICLO', 'strNombreCiclo', 'string');
          
        $objNtvQuery->setParameter('arrayEstadoCiclo', $arrayParametros['arrayEstadoCiclo']);
        $objNtvQuery->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
        $objNtvQuery->setParameter('strCaracteristica', 'CICLO_FACTURACION');
        $objNtvQuery->setParameter('strEstadoPerEmpCarac', 'Activo');

        if(isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo'])
           && $arrayParametros['strTipo'] == 'Consulta')
        {
            $objNtvQuery->setSQL($strQuery . $strFromQuery . $strQueryTipo. $strOrderByQuery);
        }
        else
        {
            $objNtvQuery->setSQL($strQuery . $strFromQuery . $strOrderByQuery);
        }       
        $objDatos = $objNtvQuery->getResult();

        $objNtvQueryCount->setParameter('arrayEstadoCiclo', $arrayParametros['arrayEstadoCiclo']);
        $objNtvQueryCount->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
        $objNtvQueryCount->setParameter('strCaracteristica', 'CICLO_FACTURACION');
        $objNtvQueryCount->setParameter('strEstadoPerEmpCarac', 'Activo');

        if(isset($arrayParametros['strTipo']) && !empty($arrayParametros['strTipo'])
           && $arrayParametros['strTipo'] == 'Consulta')
        {
            $strQueryCount = $strQueryCount . $strFromQuery . $strQueryTipo;
        }
        else
        {
            $strQueryCount = $strQueryCount . $strFromQuery;
        }
        $objNtvQueryCount->setSQL($strQueryCount);
        $intTotal      = $objNtvQueryCount->getSingleScalarResult();

        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;

        return $arrayResultado;
    }

    public function generarJson($nombre, $start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, '', '');
        $registros = $this->getRegistros($nombre, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_ciclo' =>$data->getId(),
                                         'nombre_ciclo' =>trim($data->getNombreCiclo()),
                                         'action1' => 'button-grid-show',
                                         'action2' => 'button-grid-edit',
                                         'action3' => 'button-grid-delete');
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_ciclo' => 0 , 'nombre_ciclo' => 'Ninguno', 'ciclo_id' => 0 , 'ciclo_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    public function getRegistros($nombre,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiCiclo','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreCiclo) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }

     /**
     * getResultadoCiclosFacturacion
     * 
     * Obtiene los ciclos de Facturacion por empresa en sesion.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 29-08-2017
     * costoQuery: 2
     * @param  array $arrayParametros [
     *                                  "strCodEmpresa"            : Codigo de la Empresa en Sesion
     *                                  "intStart"                 : inicio el rownum,
     *                                  "intLimit"                 : fin del rownum
     *                                  "serviceRouter"            : $this->container->get('router')
     *                                  "boolEliminacionPermitida" : Campo para indicar si tiene permitido visualizar boton de eliminacion          
     *                                ]
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 13-06-2022 - Se agregan las columnas codigo de ciclo y ciclo especial al Grid del Listado de ciclos de facturacion
     * 
     * @return json $arrayResultado
     */
     public function getResultadoCiclosFacturacion($arrayParametros)
    {        
        $strSqlDatos      = ' SELECT CI.id, CI.nombreCiclo, CI.codigo,
                              CI.feInicio, CI.feFin, CI.observacion,
                              CI.feCreacion, CI.usrCreacion, CI.estado '; 
        
        $strSqlDatos2     = ' , (SELECT 
                               count(DET) AS VALOR
                               FROM schemaBundle:AdmiParametroDet DET,
                               schemaBundle:AdmiParametroCab CAB
                               WHERE CAB.id             = DET.parametroId
                               AND DET.descripcion      = :strDescripcion
                               AND DET.valor1           = CI.codigo
                               AND DET.estado           = :strActivo
                               AND CAB.nombreParametro  = :strNombreParametro
                               AND CAB.estado           = :strActivo)  cicloEspecial ';
        
        $strSqlCantidad   = ' SELECT count(CI) '; 
        
        $strSqlFrom       = ' FROM schemaBundle:AdmiCiclo CI                                  
                              WHERE 
                              CI.empresaCod = :strCodEmpresa ';
               
        $strSqlOrderBy    = " ORDER BY CI.id DESC ";
        
        $strQueryDatos   = '';
        $strQueryDatos   = $this->_em->createQuery();
        $strQueryDatos->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);   
        $strQueryDatos->setParameter('strDescripcion', 'PROM_CICLOS_FACTURACION');  
        $strQueryDatos->setParameter('strActivo', 'Activo');  
        $strQueryDatos->setParameter('strNombreParametro', 'PROM_PARAMETROS');
        
        $strSqlDatos    .= $strSqlDatos2;
        $strSqlDatos    .= $strSqlFrom;        
        $strSqlDatos    .= $strSqlOrderBy;
        $strQueryDatos->setDQL($strSqlDatos);       
        $objDatos        = $strQueryDatos->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();
        
        $strQueryCantidad = '';
        $strQueryCantidad = $this->_em->createQuery();
        $strQueryCantidad->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);  
        
        $strSqlCantidad .= $strSqlFrom;
        $strQueryCantidad->setDQL($strSqlCantidad);
        $intTotal        = $strQueryCantidad->getSingleScalarResult();
        
        $arrayResultado['objRegistros'] = $objDatos;
        $arrayResultado['intTotal']     = $intTotal;
       
        return $arrayResultado;
    }

    
    /**
     * getListadoCiclosFacturacion
     * 
     * Obtiene los ciclos de Facturacion por empresa en sesion.
     * Solo se podra realizar Eliminacion de Ciclo si se tiene la credencial de Eliminacion y si el estado del Ciclo es Inactivo
     * El boton de Eliminacion debera Validar que el Ciclo a Eliminar no este atado a ningun Cliente.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 28-08-2017
     * 
     * @param  array $arrayParametros [
     *                                  "strCodEmpresa"            : Codigo de la Empresa en Sesion
     *                                  "intStart"                 : inicio el rownum,
     *                                  "intLimit"                 : fin del rownum
     *                                  "serviceRouter"            : $this->container->get('router')
     *                                  "boolEliminacionPermitida" : Campo para indicar si tiene permitido visualizar boton de eliminacion
     *                                ]     
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.1 13-06-2022 - Se agregan las columnas codigo de ciclo y ciclo especial al Grid del Listado de ciclos de facturacion.
     * 
     * @return array $arrayRespuesta
     */
    public function getListadoCiclosFacturacion($arrayParametros)
    {
        $arrayEncontrados           = array();
        $arrayResultado             = $this->getResultadoCiclosFacturacion($arrayParametros);        
        $serviceRouter              = $arrayParametros["serviceRouter"];
        $boolEliminacionPermitida   = $arrayParametros['boolEliminacionPermitida'];
        $objRegistros               = $arrayResultado['objRegistros'];
        $intTotal                   = $arrayResultado['intTotal'];        
        
        if(($objRegistros))
        {
            foreach($objRegistros as $arrayCiclosFacturacion)
            {
                if( $boolEliminacionPermitida  && $arrayCiclosFacturacion['estado']=='Inactivo')
                {
                    $strLinkEliminarCiclo = $serviceRouter->generate('admiCiclosFacturacion_eliminarCiclosFacturacionAjax',
                                                                     array('intIdCiclo' => $arrayCiclosFacturacion['id'])
                                                                    ); 
                    $strLinkActivarCiclo = $serviceRouter->generate('admiCiclosFacturacion_activarCiclosFacturacionAjax',
                                                                     array('intIdCiclo' => $arrayCiclosFacturacion['id'])
                                                                    ); 
                }
                else
                {
                    $strLinkEliminarCiclo = '';
                    $strLinkActivarCiclo = '';
                }
                
                $intClientes=$this->getTotalClientesAsignadosACicloFact(array('strPrefijo' => $arrayParametros['strPrefijo'],
                                                                              'intIdCiclo' => $arrayCiclosFacturacion['id'])
                                                                       );
                if( $arrayCiclosFacturacion['cicloEspecial'] =='1')
                {
                    $strCicloEspecial = 'Si';
                }
                else 
                {
                    $strCicloEspecial = 'No';
                }              
                $arrayEncontrados[] = array('intIdCiclo'           => $arrayCiclosFacturacion['id'],
                                            'strNombreCiclo'       => $arrayCiclosFacturacion['nombreCiclo'],
                                            'strCodigoCiclo'       => $arrayCiclosFacturacion['codigo'],
                                            'strCicloEspecial'     => $strCicloEspecial,
                                            'strCicloInicio'       => $arrayCiclosFacturacion['feInicio'] ? 
                                                                      strval(date_format($arrayCiclosFacturacion['feInicio'],"d")):"",
                                            'strCicloFin'          => $arrayCiclosFacturacion['feFin'] ? 
                                                                      strval(date_format($arrayCiclosFacturacion['feFin'],"d")):"",
                                            'strObservacion'       => $arrayCiclosFacturacion['observacion'],
                                            'strFeCreacion'        => $arrayCiclosFacturacion['feCreacion'] ? 
                                                                      strval(date_format($arrayCiclosFacturacion['feCreacion'],"d-m-Y H:i")):"",
                                            'strUsrCreacion'       => $arrayCiclosFacturacion['usrCreacion'],
                                            'intClientes'          => $intClientes,
                                            'strEstado'            => $arrayCiclosFacturacion['estado'],                   
                                            'strLinkEliminarCiclo' => $strLinkEliminarCiclo,
                                            'strLinkActivarCiclo' => $strLinkActivarCiclo,                    
                );
            }
        }        
        $arrayRespuesta = array('intTotal' => $intTotal, 'arrayResultado' => $arrayEncontrados);
        return $arrayRespuesta;        
    }
    /**
     * getTotalClientesAsignadosACicloFact
     *
     * Metodo para obtener total de Clientes que se encuentran asignados a un Ciclo de Facturación especifico
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 05-09-2017
     * costoQuery: 151     
     * @param  array $arrayParametros [
     *                                  "intIdCiclo"  : Id del Ciclo
     *                                  "strPrefijo"  : Prefijo de la Empresa en Sesion
     *                                ]          
     * @return integer
     */   
    public function getTotalClientesAsignadosACicloFact($arrayParametros)
    {
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);

        $strSql= " SELECT COUNT(*) AS CANTIDAD_CLIENTES
                 FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                 DB_COMERCIAL.INFO_EMPRESA_ROL IER,
                 DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG,
                 DB_GENERAL.ADMI_TIPO_ROL ATR,
                 DB_GENERAL.ADMI_ROL AR
                 WHERE IPER.EMPRESA_ROL_ID    = IER.ID_EMPRESA_ROL
                 AND IER.EMPRESA_COD          = IEG.COD_EMPRESA    
                 AND IER.ROL_ID               = AR.ID_ROL
                 AND ATR.ID_TIPO_ROL          = AR.TIPO_ROL_ID
                 AND IEG.PREFIJO              = :strPrefijo
                 AND ATR.DESCRIPCION_TIPO_ROL IN (:arrayRol)
                 AND IPER.ESTADO              NOT IN (:arrayEstado)                 
                 AND EXISTS (SELECT 1
                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC,
                    DB_COMERCIAL.ADMI_CARACTERISTICA CA
                    WHERE IPERC.PERSONA_EMPRESA_ROL_ID                           = IPER.ID_PERSONA_ROL
                    AND IPERC.CARACTERISTICA_ID                                  = CA.ID_CARACTERISTICA
                    AND CA.DESCRIPCION_CARACTERISTICA                            = :strCaracteristica
                    AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(IPERC.VALOR,'^\d+')),0) = :intIdCiclo
                    AND IPERC.ESTADO                                             = :strEstado
                    )";
        $objRsmCount->addScalarResult('CANTIDAD_CLIENTES', 'CantidadClientes', 'integer');
        
        $objNtvQueryCount->setParameter("strPrefijo", $arrayParametros['strPrefijo']);
        $objNtvQueryCount->setParameter("arrayRol",array('Pre-cliente','Cliente'));
        $objNtvQueryCount->setParameter("arrayEstado", array('Inactivo','Eliminado','Anulado'));                
        $objNtvQueryCount->setParameter("strCaracteristica", 'CICLO_FACTURACION');
        $objNtvQueryCount->setParameter("intIdCiclo", $arrayParametros['intIdCiclo']);
        $objNtvQueryCount->setParameter("strEstado", 'Activo');

        $objNtvQueryCount->setSQL($strSql);
        $intTotal = $objNtvQueryCount->getSingleScalarResult();
        return $intTotal;
    }
    
    /**
     * getGridDetClientes
     *
     * Metodo para obtener los datos del Grip del Detalle de Clientes por Ciclos
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.0 19-10-2017
     * costoQuery: 151
     * @param  array $arrayParametros [
     *                                  "intIdCiclo"  : Id del Ciclo
     *                                  "strPrefijo"  : Prefijo de la Empresa en Sesion
     *                                ]
     * @return integer
     */
    public function getGridDetClientes($arrayParametros)
    {
        $objRsm      = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery = $this->_em->createNativeQuery(null, $objRsm);

        $strSql="SELECT IPER.ESTADO,
                 ATR.DESCRIPCION_TIPO_ROL,
                 COUNT(*) AS CANTIDAD
                 FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER,
                 DB_COMERCIAL.INFO_EMPRESA_ROL IER,
                 DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG,
                 DB_GENERAL.ADMI_TIPO_ROL ATR,
                 DB_GENERAL.ADMI_ROL AR
                 WHERE IPER.EMPRESA_ROL_ID    = IER.ID_EMPRESA_ROL
                 AND IER.EMPRESA_COD          = IEG.COD_EMPRESA
                 AND IER.ROL_ID               = AR.ID_ROL
                 AND ATR.ID_TIPO_ROL          = AR.TIPO_ROL_ID
                 AND IEG.PREFIJO              = :strPrefijo
                 AND ATR.DESCRIPCION_TIPO_ROL IN (:arrayRol)
                 AND IPER.ESTADO              NOT IN (:arrayEstado)
                 AND EXISTS (SELECT 1
                    FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC,
                    DB_COMERCIAL.ADMI_CARACTERISTICA CA
                    WHERE IPERC.PERSONA_EMPRESA_ROL_ID                           = IPER.ID_PERSONA_ROL
                    AND IPERC.CARACTERISTICA_ID                                  = CA.ID_CARACTERISTICA
                    AND CA.DESCRIPCION_CARACTERISTICA                            = :strCaracteristica
                    AND COALESCE(TO_NUMBER(REGEXP_SUBSTR(IPERC.VALOR,'^\d+')),0) = :intIdCiclo
                    AND IPERC.ESTADO                                             = :strEstado
                    )
                 GROUP BY IPER.ESTADO,
                   ATR.DESCRIPCION_TIPO_ROL
                 ORDER BY ATR.DESCRIPCION_TIPO_ROL,
                   IPER.ESTADO";

        $objRsm->addScalarResult('ESTADO', 'strEstado', 'string');
        $objRsm->addScalarResult('DESCRIPCION_TIPO_ROL', 'strDescripcionTipoRol', 'string');
        $objRsm->addScalarResult('CANTIDAD', 'intCantidad', 'integer');
        
        $objNtvQuery->setParameter("strPrefijo", $arrayParametros['strPrefijo']);
        $objNtvQuery->setParameter("arrayRol",array('Pre-cliente','Cliente'));
        $objNtvQuery->setParameter("arrayEstado", array('Inactivo','Eliminado','Anulado'));
        $objNtvQuery->setParameter("strCaracteristica", 'CICLO_FACTURACION');
        $objNtvQuery->setParameter("intIdCiclo", $arrayParametros['intIdCiclo']);
        $objNtvQuery->setParameter("strEstado", 'Activo');

        $objNtvQuery->setSQL($strSql);
        $objResult = $objNtvQuery->getResult();
        return $objResult;
    }

     /**
     * isCicloFacturacionExistente
     *
     * Metodo para obtener si el ciclo ya existe, se considera por el rango de Dia Inicio y Dia Fin
     * Empresa en Sesion y estados de ciclos 
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 05-09-2017
     * costoQuery: 1     
     * @param  array $arrayParametros [                                       
     *                                  "strCodEmpresa"     : Codigo de la Empresa en Sesion
     *                                  "strTxCicloInicio"  : Dia Inicio del Ciclo de Facturación
     *                                  "strTxCicloFin"     : Dia Fin del Ciclo de Facturación
     *                                  "arrayEstado"       : Array de Estados de Ciclos de Facturación                 
     *                                ]          
     * @return boolean
     */   
    public function isCicloFacturacionExistente($arrayParametros)
    {
        $boolExisteCicloFacturacion = false;
        
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);

        $strSql= "SELECT COUNT(*) AS CANTIDAD
                  FROM DB_FINANCIERO.ADMI_CICLO WHERE 
                  TO_CHAR(FE_INICIO,'DD')  =:strTxCicloInicio
                  AND TO_CHAR(FE_FIN,'DD') =:strTxCicloFin
                  AND EMPRESA_COD          =:strCodEmpresa
                  AND ESTADO               IN (:arrayEstado) ";
        
        $objRsmCount->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        
        $objNtvQueryCount->setParameter("strCodEmpresa", $arrayParametros['strCodEmpresa']);
        $objNtvQueryCount->setParameter("strTxCicloInicio", $arrayParametros['strTxCicloInicio']);
        $objNtvQueryCount->setParameter("strTxCicloFin", $arrayParametros['strTxCicloFin']);
        $objNtvQueryCount->setParameter("arrayEstado", $arrayParametros['arrayEstado']);        

        $objNtvQueryCount->setSQL($strSql);        
        $intTotal = $objNtvQueryCount->getSingleScalarResult();
        
        if($intTotal >0)
        {
            $boolExisteCicloFacturacion = true;
        }
        return $boolExisteCicloFacturacion;
    }

    /**
     * Método que obtiene la fecha a facturar en base a un ciclo específico.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 07-06-2018
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.2
     * @since 27-09-2018
     * Se modifican los parámetros al llamar a procedimiento que obtiene la siguiente fecha a facturar en base a un ciclo.
     *
     * @param $arrayParametros
     */
    public function obtieneFeSiguienteCicloFacturar($arrayParametros)
    {
        $intIdCiclo       = $arrayParametros["intIdCiclo"];
        $objFeFacturacion = str_pad(' ', 20);
        if (!$intIdCiclo || is_null($intIdCiclo) || $intIdCiclo <= 0)
        {
            throw new \Exception ("Datos del cliente incompletos. Ciclo de Facturación vacío.");
        }
        try
        {
            $strSql            = 'BEGIN '
                               . 'DB_FINANCIERO.FNCK_FACTURACION.P_OBTIENE_FE_SIG_CICLO_FACT(Pn_IdCiclo       => :Pn_IdCiclo, '
                                                                                          . 'Pd_FeAValidar    => SYSDATE, '
                                                                                          . 'Pd_FeFacturacion => :Pd_FeFacturacion); '
                               . 'END;';
            $strStmt           = $this->_em->getConnection()->prepare($strSql);
            $strStmt->bindParam('Pn_IdCiclo', $intIdCiclo);
            $strStmt->bindParam('Pd_FeFacturacion', $objFeFacturacion);
            $strStmt->execute();
        } catch (\Exception $ex)
        {
            $objFeFacturacion = null;
            error_log($ex->getMessage());
        }
        
        return $objFeFacturacion;
    }

    /**
     * getCiclos, obtiene los tipos de Negocio por empresa en estado activo
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 06-04-2020
     *      
     * @param array $arrayParametros [
     *                                "strEmpresaCod" => Empresa en sesión
     *                               ]
     *
     * Costo de query: 2
     * 
     * @return Response lista de Tipos de Negocio
     */
    public function getCiclos($arrayParametros)
    {
        $objRsm          = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery     = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery        = " SELECT DBAC.ID_CICLO,
                               DBAC.CODIGO
                             FROM DB_FINANCIERO.ADMI_CICLO DBAC
                             WHERE DBAC.EMPRESA_COD = :strEmpresaCod ";

        $objRsm->addScalarResult('ID_CICLO', 'id', 'integer');
        $objRsm->addScalarResult('CODIGO', 'nombre', 'string');
                  
        $objNtvQuery->setParameter('strEmpresaCod' ,$arrayParametros['strEmpresaCod']);

        return $objNtvQuery->setSQL($strQuery)->getArrayResult();
    }
}
