<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoDebitoRespuestaRepository extends EntityRepository
{

    /**
    * Documentación para la función 'getUltimaRespuestaDebito'.
    * Obtiene la última respuesta de débito cargada en telcos, enviando como parámetro el Id del débito general.
    * 
    * @param $arrayParametros [ 'intIdDebitoGeneral' => Id del débito general]
    *
    * @return object DB_FINANCIERO.INFO_DEBITO_RESPUESTA
    *
    * @author Ricardo Robles <rrobles@telconet.ec>
    * @version 1.0 26-04-2019
    * Costo query: 2
    */
    public function getUltimaRespuestaDebito($arrayParametros)
    {      
        $objRsm      = new ResultSetMappingBuilder($this->_em);      
        $objNtvQuery = $this->_em->createNativeQuery(null,$objRsm);
        $strSql      = "SELECT idr.id_respuesta_debito, 
                          idr.archivo, 
                          idr.archivo_no_encontrados,  
                          idr.banco_tipo_cuenta_id,
                          idr.debito_cab_id, 
                          idr.estado, 
                          idr.nombre_banco,  
                          idr.nombre_tipo_cuenta,  
                          idg.id_debito_general,
                          idr.valor_archivo
                        FROM db_financiero.info_debito_general idg,  
                          db_financiero.info_debito_respuesta idr  
                        WHERE idr.debito_general_id = idg.id_debito_general   
                        AND idg.id_debito_general   = :intIdDebitoGeneral   
                        AND idr.archivo_no_encontrados IS NOT NULL            
                        AND idr.estado              = 'Procesado'             
                        AND idr.id_respuesta_debito = (SELECT max(dr.id_respuesta_debito)  
                                                       FROM db_financiero.info_debito_respuesta dr  
                                                       WHERE dr.debito_general_id = idg.id_debito_general)
                                                       ORDER BY idr.id_respuesta_debito DESC";
        
        $objNtvQuery->setParameter('intIdDebitoGeneral', $arrayParametros['intIdDebitoGeneral']);
        $objNtvQuery->setSql($strSql);

        $objRsm->addScalarResult('ID_RESPUESTA_DEBITO', 'id_respuesta_debito', 'string');
        $objRsm->addScalarResult('ARCHIVO', 'archivo', 'string');
        $objRsm->addScalarResult('ARCHIVO_NO_ENCONTRADOS', 'archivo_no_encontrados', 'string');
        $objRsm->addScalarResult('BANCO_TIPO_CUENTA_ID', 'banco_tipo_cuenta_id', 'string');
        $objRsm->addScalarResult('DEBITO_CAB_ID', 'debito_cab_id', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        $objRsm->addScalarResult('NOMBRE_BANCO', 'nombre_banco', 'string');
        $objRsm->addScalarResult('NOMBRE_TIPO_CUENTA', 'nombre_tipo_cuenta', 'string');
        $objRsm->addScalarResult('ID_DEBITO_GENERAL', 'id_debito_general', 'string');
        $objRsm->addScalarResult('VALOR_ARCHIVO', 'valor_archivo', 'string');

        $arrayResult = $objNtvQuery->getArrayResult();

        return $arrayResult;
    }

}
