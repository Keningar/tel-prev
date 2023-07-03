<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AdmiParametroHistRepository extends EntityRepository
{
    
    /**
     * getParametrosHist, Crea la sentencia DQL según los parámetros enviados de la entidad AdmiParametroHist
     * @author Edgar Holguin <egholguin@telconet.ec>
     * @version 1.0 18-07-2017
     * 
     * 
     * @param   array $arrayParametros['strEstado'      => Estado del parámetro
     *                                 'strValor1'      => valor1 de la entidad AdmiParametroDet,
     *                                 'strValor2'      => valor2 de la entidad AdmiParametroDet,
     *                                 'strValor3'      => valor3 de la entidad AdmiParametroDet,
     *                                 'strValor4'      => valor4 de la entidad AdmiParametroDet,
     *                                 'strUsrCreacion' => Usuario que creó el parámetro,
     *                                 'strEmpresaCod'  => Código de la Empresa]
     * 
     * @return array $arrayResult['strMensajeError' => 'Mensaje de error en caso de existir',
     *                            'arrayResultado'  => 'Array con la información devuelta por la entidad AdmiParametroDet según los filtros
     *                                                  enviados'
     *                                                  array[  aph.id          => intIdParametroDet, 
     *                                                          aph.descripcion => strDescripcionDet,
     *                                                          aph.valor1      => strValor1,
     *                                                          aph.valor2      => strValor2,
     *                                                          aph.valor3      => strValor3,
     *                                                          aph.valor4      => strValor4,
     *                                                          aph.valor5      => strValor5,
     *                                                          aph.empresaCod  => strEmpresaCod,
     *                                                          aph.estado      => strEstado,
     *                                                          aph.usrCreacion => strUsrCreacion,
     *                                                          aph.feCreacion  => strFeCreacion,
     *                                                          aph.ipCreacion  => stripCreacion,
     *                                                          aph.usrUltMod   => strUsrUltMod,
     *                                                          aph.feUltMod    => strFeUltMod,
     *                                                          aph.ipUltMod    => strIpUltMod  ],
     *                            'intTotal'        => 'Cantidad de registros retornados por el query']
     * 
     * @author Gustavo Narea
     * @version 1.1 28-10-2020 Agregacion de Descripcion del ParametroDet en el query
     */
     public function getParametrosHist($arrayParametros)
    {
        $arrayResult['strMensajeError'] = '';
        try
        {
            $objQueryCount = $this->_em->createQuery();
            $strQueryCount = "SELECT count(aph.id) ";
            $objQuery = $this->_em->createQuery();
            $strQuery = "SELECT    aph.id intIdParametroHist, "
                                . "aph.descripcion strDescripcionDet, "
                                . "aph.valor1 strValor1, "
                                . "aph.valor2 strValor2, "
                                . "aph.valor3 strValor3, "
                                . "aph.valor4 strValor4, "
                                . "aph.valor5 strValor5, "
                                . "aph.empresaCod strEmpresaCod, "
                                . "aph.estado strEstado, "
                                . "aph.usrCreacion strUsrCreacion, "
                                . "aph.feCreacion strFeCreacion, "
                                . "aph.ipCreacion stripCreacion, "
                                . "aph.usrUltMod strUsrUltMod, "
                                . "aph.feUltMod strFeUltMod, "
                                . "aph.ipUltMod strIpUltMod, "
                                . "aph.observacion strObservacion ";            
                                
            $strFromQuery = "FROM schemaBundle:AdmiParametroHist aph ";
            $strWhere     = "WHERE 1 = 1 ";
            $strOrderBy   = "ORDER BY aph.id DESC ";
            
            //Pregunta si $arrayParametros['strNombreParametroCab'] existe y si es diferente de vacío para agregar la condición.
            if( isset($arrayParametros['strNombreParametroCab']) && !empty($arrayParametros['strNombreParametroCab']) )
            {
                $strFromQuery .= ", schemaBundle:AdmiParametroCab apc ";
                $strWhere     .= "AND aph.parametroId = apc.id ".
                                 "AND apc.nombreParametro = :strNombreParametroCab ";
                
                $objQuery->setParameter('strNombreParametroCab',      $arrayParametros['strNombreParametroCab']);
                $objQueryCount->setParameter('strNombreParametroCab', $arrayParametros['strNombreParametroCab']);
            }
            
            //Pregunta si $arrayParametros['strEmpresaCod'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strEmpresaCod']))
            {
                // Una vez que todos los parámetros Detalle dispongan del código de empresa se deberá remover la cláusula "aph.empresaCod is null OR"
                // que devuelve todos los registros en caso de que los detalles del parámetro no dispongan del código de la empresa
                $strWhere .= " AND (aph.empresaCod is null OR aph.empresaCod = :strEmpresaCod)";
                $objQuery->setParameter('strEmpresaCod',      $arrayParametros['strEmpresaCod']);
                $objQueryCount->setParameter('strEmpresaCod', $arrayParametros['strEmpresaCod']);
            }
            //Pregunta si $arrayParametros['strDescripcion'] es diferente de vacío para agregar la condición.
            if(!empty($arrayParametros['strDescripcion']))
            {
                $strWhere .= " AND (aph.descripcion = :strDescripcion)";
                $objQuery->setParameter('strDescripcion',      $arrayParametros['strDescripcion']);
                $objQueryCount->setParameter('strDescripcion', $arrayParametros['strDescripcion']);
            }

            $strSql = $strQuery . $strFromQuery . $strWhere . $strOrderBy; 
            $objQuery->setDQL($strSql);
            
            $arrayResult['arrayResultado'] = $objQuery->setFirstResult($arrayParametros['intStart'])
                                                      ->setMaxResults($arrayParametros['intLimit'])->getResult();
            //Pregunta si $arrayResult['arrayResultado'] es diferente de vacio para realizar el count
            if(!empty($arrayResult['arrayResultado']))
            {
                $strQueryCount = $strQueryCount . $strFromQuery . $strWhere . $strOrderBy;
                $objQueryCount->setDQL($strQueryCount);
                $arrayResult['intTotal'] = $objQueryCount->getSingleScalarResult();
            }
        }
        catch(\Exception $ex)
        {
            $arrayResult['strMensajeError'] = 'Existio un error en getParametrosHist - ' . $ex->getMessage();
        }
        return $arrayResult;
    }//findParametrosHist
 
}
