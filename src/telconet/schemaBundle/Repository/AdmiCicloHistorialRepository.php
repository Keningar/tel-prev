<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiCicloHistorialRepository extends EntityRepository
{
    /**
     * getResultadoCiclosFacturacionHist
     * 
     * Obtiene Historico de los ciclos de Facturacion por empresa en sesion.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 31-08-2017
     * costoQuery: 2
     * @param  array $arrayParametros [
     *                                  "strCodEmpresa"            : Codigo de la Empresa en Sesion
     *                                  "intStart"                 : inicio el rownum,
     *                                  "intLimit"                 : fin del rownum                                          
     *                                ]
     * 
     * @return json $arrayResultado
     */
    public function getResultadoCiclosFacturacionHist($arrayParametros)
    {        
        $strSqlDatos      = ' SELECT CIH.id, CIH.nombreCiclo,
                              CIH.feInicio, CIH.feFin, CIH.observacion,
                              CIH.feCreacion, CIH.usrCreacion, CIH.estado '; 
         
        $strSqlCantidad   = ' SELECT count(CIH) '; 
        
        $strSqlFrom       = ' FROM schemaBundle:AdmiCicloHistorial CIH                                  
                              WHERE 
                              CIH.empresaCod = :strCodEmpresa ';
               
        $strSqlOrderBy    = " ORDER BY CIH.id DESC ";
        
        $strQueryDatos   = '';
        $strQueryDatos   = $this->_em->createQuery();
        $strQueryDatos->setParameter('strCodEmpresa', $arrayParametros['strCodEmpresa']);      
        
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
     * getListadoCiclosFacturacionHist
     * 
     * Obtiene Historico de los ciclos de Facturacion por empresa en sesion.
     *      
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 31-08-2017
     * 
     * @param  array $arrayParametros [
     *                                  "strCodEmpresa"            : Codigo de la Empresa en Sesion
     *                                  "intStart"                 : inicio el rownum,
     *                                  "intLimit"                 : fin del rownum                                      
     *                                ]     
     * 
     * @return array $arrayRespuesta
     */
    public function getListadoCiclosFacturacionHist($arrayParametros)
    {
        $arrayEncontrados           = array();
        $arrayResultado             = $this->getResultadoCiclosFacturacionHist($arrayParametros);                
        $objRegistros               = $arrayResultado['objRegistros'];
        $intTotal                   = $arrayResultado['intTotal'];        
        
        if(($objRegistros))
        {
            foreach($objRegistros as $arrayCiclosFacturacionHist)
            {                               
                $arrayEncontrados[] = array('intIdCiclo'           => $arrayCiclosFacturacionHist['id'],
                                            'strNombreCiclo'       => $arrayCiclosFacturacionHist['nombreCiclo'],
                                            'strCicloInicio'       => $arrayCiclosFacturacionHist['feInicio'] ? 
                                                                      strval(date_format($arrayCiclosFacturacionHist['feInicio'],"d")):"",
                                            'strCicloFin'          => $arrayCiclosFacturacionHist['feFin'] ? 
                                                                      strval(date_format($arrayCiclosFacturacionHist['feFin'],"d")):"",
                                            'strObservacion'       => $arrayCiclosFacturacionHist['observacion'],
                                            'strFeCreacion'        => $arrayCiclosFacturacionHist['feCreacion'] ? 
                                                                      strval(date_format($arrayCiclosFacturacionHist['feCreacion'],"d-m-Y H:i")):"",
                                            'strUsrCreacion'       => $arrayCiclosFacturacionHist['usrCreacion'],
                                            'strEstado'            => $arrayCiclosFacturacionHist['estado'],
                );
            }
        }        
        $arrayRespuesta = array('intTotal' => $intTotal, 'arrayResultadoHist' => $arrayEncontrados);
        return $arrayRespuesta;        
    }
    
}
