<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\Response;


class InfoContratoCaracteristicaRepository extends EntityRepository
{
    /**
     * Documentación para el método "getJsonResultadoEntregablesContrato"
     *
     * Método que retorna el objet Json de los documentos entregables relacionados al contrato.
     *
     * @param integer $intIdContrato Código del contrato.
     * @param string  $strCodEmpresa Código de la empresa.
     * @param string  $strFormaPago  Forma de pago del contrato.
     *
     * @return string - Listado de Documentos relacionados en formato JSON.
     *
     * @author Alejandro Dominguez Vargas <adominguez@telconet.ec>
     * @version 1.0 13-09-2016
     */
    public function getJsonResultadoEntregablesContrato($intIdContrato, $strCodEmpresa, $strFormaPago)
    {
        $arrayRespuesta['entregables'] = null;
        $arrayRespuesta['total']       = $this->getResultadoEntregablesContrato($intIdContrato, $strCodEmpresa, $strFormaPago, TRUE);

        if(intval($arrayRespuesta['total']) > 0)
        {
            $arrayRespuesta['entregables'] = $this->getResultadoEntregablesContrato($intIdContrato, $strCodEmpresa, $strFormaPago, FALSE);
        }
        
        return json_encode($arrayRespuesta);
    }
    
    /**
     * Documentación para el método "getResultadoEntregablesContrato"
     *
     * Método que retorna el check list de los documentos entregables relacionados al contrato.
     *
     * @param integer $intIdContrato Código del contrato.
     * @param string  $strCodEmpresa Código de la empresa.
     * @param string  $strFormaPago  Forma de pago del contrato.
     * @param boolean $booleanCount     Indicador booleano que determinar si es conteo u obtención de registros.
     *
     * @return Array  $result Listado de Documentos relacionados
     * 
     * Costo Query: 8
     *
     * @author Alejandro Dominguez Vargas <adominguez@telconet.ec>
     * @version 1.0 15-02-2016
     */
    public function getResultadoEntregablesContrato($intIdContrato, $strCodEmpresa, $strFormaPago, $booleanCount = false)
    {
        $objRsmBuilder = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery   = $this->_em->createNativeQuery(null, $objRsmBuilder);
        $strExcluidos  = '';
        
        if($strFormaPago !== 'DEB')
        {
            $strExcluidos = 'AND (DET.VALOR4 IS NULL OR DET.VALOR4 != :TIPO)';
            $objNtvQuery->setParameter("TIPO", 'DEB');
        }
        
        $strSqlCompleto = " SELECT :CONTRATO CONTRATO,
                                DET.VALOR1 CODIGO, 
                                DET.DESCRIPCION,
                                ( CASE WHEN CC.VALOR2 IS NULL THEN 0 
                                       ELSE ( CASE WHEN CC.VALOR2 = :VALOR2 THEN 1 
                                                   ELSE 0 END ) 
                                  END ) ENTREGO
                            FROM       DB_GENERAL.ADMI_PARAMETRO_CAB CAB  
                            INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET              ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
                            LEFT  JOIN DB_COMERCIAL.INFO_CONTRATO_CARACTERISTICA  CC  ON CC.VALOR1        = DET.VALOR1
                                                                                     AND CC.CONTRATO_ID   = :CONTRATO
                                                                                     AND CC.ESTADO        = :ESTADO
                            LEFT  JOIN DB_COMERCIAL.ADMI_CARACTERISTICA           CAR ON CAR.ID_CARACTERISTICA          = CC.CARACTERISTICA_ID 
                                                                                     AND CAR.DESCRIPCION_CARACTERISTICA = :DESC_ENTREGABLES

                            WHERE 
                                CAB.NOMBRE_PARAMETRO = :DESC_ENTREGABLES
                            AND CAB.ESTADO           = :ESTADO
                            AND DET.ESTADO           = :ESTADO
                            AND CAB.MODULO           = :MODULO
                            $strExcluidos
                            AND DET.EMPRESA_COD      = :EMPRESA
                            ORDER BY DET.VALOR3";
        
        $strSqlCompleto = $booleanCount ? "SELECT COUNT(*) AS TOTAL FROM ($strSqlCompleto)" : $strSqlCompleto;

        $objNtvQuery->setParameter("CONTRATO",         $intIdContrato);
        $objNtvQuery->setParameter("DESC_ENTREGABLES", 'DOCUMENTOS_ENTREGABLES_CONTRATO');
        $objNtvQuery->setParameter("ESTADO",           'Activo');
        $objNtvQuery->setParameter("MODULO",           'COMERCIAL');
        $objNtvQuery->setParameter("EMPRESA",          $strCodEmpresa);
        $objNtvQuery->setParameter("VALOR2",           'S');
        
        $objRsmBuilder->addScalarResult('TOTAL',       'total',         'integer');
        $objRsmBuilder->addScalarResult('CONTRATO',    'idContrato',    'integer');
        $objRsmBuilder->addScalarResult('CODIGO',      'codEntregable', 'string');
        $objRsmBuilder->addScalarResult('DESCRIPCION', 'desEntregable', 'string');
        $objRsmBuilder->addScalarResult('ENTREGO',     'valEntregable', 'integer');
        
        $objNtvQuery->setSQL($strSqlCompleto);
        
        return $booleanCount ? $objNtvQuery->getSingleScalarResult() : $objNtvQuery->getResult();
    }
    
    /**
     * Documentación para el método "getResultadoDocumentoEntregableContrato"
     *
     * Método que retorna un objeto InfoContratoCaracteristica del documento entregable relacionado al contrato.
     *
     * @param integer $intIdContrato
     * @param integer $intIdCaracteristica
     * @param string   $strDocumento
     *
     * @return Array  $result Listado de Documentos relacionados
     *
     * @author Alejandro Dominguez Vargas <adominguez@telconet.ec>
     * @version 1.0 15-02-2016
     */
    public function getResultadoDocumentoEntregableContrato($intIdContrato, $intIdCaracteristica, $strDocumento)
    {
        $strEstado = 'Activo';
        $objQuery  = $this->_em->createQuery();
        $strDQL    = " SELECT cc
                       FROM   schemaBundle:InfoContratoCaracteristica cc 
                       WHERE  cc.contratoId       = :idContrato 
                       AND    cc.caracteristicaId = :idCaracteristica 
                       AND    cc.valor1           = :documento
                       AND    cc.estado           = :estado";
        
        $objQuery->setParameter("idContrato",       $intIdContrato);
        $objQuery->setParameter("idCaracteristica", $intIdCaracteristica);
        $objQuery->setParameter("documento",        $strDocumento);
        $objQuery->setParameter("estado",           $strEstado);
        $objQuery->setDQL($strDQL); 
        
        return $objQuery->getOneOrNullResult();
    }

    public function getObtenerCarateristicaDocumento($intContrato)
    {            
        $objQuery = $this->_em->createQuery("SELECT iAdec.valor1 from        
                                schemaBundle:InfoContratoCaracteristica cont,
                                schemaBundle:AdmiCaracteristica carac                               
                                where cont.contratoId=:intContrato
                                and cont.caracteristicaId =carac.id
                                and carac.descripcionCaracteristica =:descripcionCaracteristica
                                and cont.estado =:strEstado ");
               
        $objQuery->setParameters(array('strEstado'            => 'Activo',
                                    'intContrato'           => $intContrato,
                                    'descripcionCaracteristica'=> 'docFisicoCargado' ));                
        $strCantidadContactos = $objQuery->getOneOrNullResult();
        if(!$strCantidadContactos)
        {
            $strCantidadContactos ='N';
        }
        return $strCantidadContactos;   
    }
    
}
