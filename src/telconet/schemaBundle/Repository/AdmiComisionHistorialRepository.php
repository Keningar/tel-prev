<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiComisionHistorialRepository extends EntityRepository
{

     /**
     * getResultadoLogsPlantillaComision
     * 
     * Metodo devuelve Log o Historial de Plantillas de Comisionistas para un producto especifico.
     *     
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 25-04-2017
     * costoQuery: 7
     * @param  array $arrayParametros [
     *                                 'intIdProducto' : $intIdProducto, 
                                       'strCodEmpresa' : $strCodEmpresa
     *                                ]     
     * 
     * @return array $arrayResultado
     */
    public function getResultadoLogsPlantillaComision($arrayParametros)
    {        
        $objRsmCount      = new ResultSetMappingBuilder($this->_em);
        $objNtvQueryCount = $this->_em->createNativeQuery(null, $objRsmCount);
        
        $strSqlCantidad   = ' SELECT COUNT(*)  AS TOTAL '; 
                        
        $objRsm           = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery      = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSqlDatos      = ' SELECT COMD.ID_COMISION_DET,PARD.DESCRIPCION,COMH.USR_CREACION,COMH.FE_CREACION,'
                          . ' COMH.IP_CREACION,COMH.ESTADO,COMH.OBSERVACION ';
        
        $strSqlFrom       = ' FROM DB_COMERCIAL.ADMI_COMISION_CAB COMC,
                               DB_COMERCIAL.ADMI_COMISION_DET COMD,
                               DB_COMERCIAL.ADMI_COMISION_HISTORIAL COMH,
                               DB_COMERCIAL.ADMI_PRODUCTO PROD,
                               DB_GENERAL.ADMI_PARAMETRO_CAB PARC,
                               DB_GENERAL.ADMI_PARAMETRO_DET PARD
                              WHERE
                              PROD.ID_PRODUCTO          = :intIdProducto                                                            
                              AND PARC.NOMBRE_PARAMETRO = :strDescripcionParametro 
                              AND PARC.ESTADO           = :strEstadoActivo
                              AND PARD.EMPRESA_COD      = :strCodEmpresa   
                              AND PROD.ID_PRODUCTO      = COMC.PRODUCTO_ID
                              AND COMC.ID_COMISION      = COMD.COMISION_ID
                              AND PARC.ID_PARAMETRO     = PARD.PARAMETRO_ID
                              AND COMD.PARAMETRO_DET_ID = PARD.ID_PARAMETRO_DET
                              AND COMD.ID_COMISION_DET  = COMH.COMISION_DET_ID ';        
        $strSqlOrderBy    = " ORDER BY COMH.FE_CREACION ASC ";
        
        $objRsm->addScalarResult('ID_COMISION_DET','idComisionDet','integer');
        $objRsm->addScalarResult('DESCRIPCION', 'grupoRol','string');
        $objRsm->addScalarResult('USR_CREACION', 'usrCreacion','string');
        $objRsm->addScalarResult('FE_CREACION','feCreacion','datetime');
        $objRsm->addScalarResult('IP_CREACION','ipCreacion','string');
        $objRsm->addScalarResult('ESTADO','estado','string');
        $objRsm->addScalarResult('OBSERVACION','observacion','string');        
       
        $objRsmCount->addScalarResult('TOTAL','total','integer');
        
        $objNtvQuery->setParameter('intIdProducto', $arrayParametros['intIdProducto']);                        
        $objNtvQuery->setParameter('strDescripcionParametro', 'GRUPO_ROLES_PERSONAL');
        $objNtvQuery->setParameter('strEstadoActivo', 'Activo');
        $objNtvQuery->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);
        
        $strSqlDatos .= $strSqlFrom;        
        $strSqlDatos .= $strSqlOrderBy;
        
        $objNtvQuery->setSQL($strSqlDatos);
        $arrayDatos = $objNtvQuery->getResult();
                
        $objNtvQueryCount->setParameter('intIdProducto', $arrayParametros['intIdProducto']);                        
        $objNtvQueryCount->setParameter('strDescripcionParametro', 'GRUPO_ROLES_PERSONAL');
        $objNtvQueryCount->setParameter('strEstadoActivo', 'Activo');
        $objNtvQueryCount->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);       

        $strSqlCantidad .= $strSqlFrom;
        $objNtvQueryCount->setSQL($strSqlCantidad);
        $intTotal        = $objNtvQueryCount->getSingleScalarResult();
                        
        $arrayResultado['arrayRegistrosComision'] = $arrayDatos;
        $arrayResultado['intTotal']               = $intTotal;
       
        return $arrayResultado;
    }
    
    /**
     * getLogsPlantillaComision
     * 
     * Metodo que devuelve Log o Historial de Plantillas de Comisionistas para un producto especifico.
     *     
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 25-04-2017
     * 
     * @param  array $arrayParametros [
     *                                 'intIdProducto' : $intIdProducto, 
                                       'strCodEmpresa' : $strCodEmpresa
     *                                ]     
     * 
     * @return array $arrayRespuesta
     */
    public function getLogsPlantillaComision($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado   = $this->getResultadoLogsPlantillaComision($arrayParametros);

        $arrayRegistrosComision = $arrayResultado['arrayRegistrosComision'];
        $intTotal               = $arrayResultado['intTotal'];
        
        foreach($arrayRegistrosComision as $arrayHisto)
        {
            $arrayEncontrados[] = array(
                                        'intIdComisionDet' => $arrayHisto['idComisionDet'],
                                        'strGrupoRol'      => $arrayHisto['grupoRol'],
                                        'strUsrCreacion'   => $arrayHisto['usrCreacion'],
                                        'strFeCreacion'    => strval(date_format($arrayHisto['feCreacion'], "d/m/Y G:i")),
                                        'strIpCreacion'    => $arrayHisto['ipCreacion'],
                                        'strEstado'        => $arrayHisto['estado'],                                        
                                        'strObservacion'   => $arrayHisto['observacion']
            );
        }

        $arrayRespuesta = array('total' => $intTotal, 'encontrados' => $arrayEncontrados);
        return $arrayRespuesta;
    }

}
