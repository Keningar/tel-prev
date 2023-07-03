<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiDispositivoAppRepository extends EntityRepository
{
    /**
     * getDispositivoApp
     *
     * Obtiene listado de dispositivo perteneciente a una razon social.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 28-11-2018
     *
     * @param  array $arrayParametros[
     *                                  'codigoDispositivo'     => imei del dispositivo
     *                                  'personaId'             => id persona
     *                               ]
     *
     * @return array $arrayResultado
     */
    public function getDispositivoApp($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $objRsm             = new ResultSetMappingBuilder($this->_em);
            $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);

            $strQuery           = "SELECT ADAP.CODIGO_DISPOSITIVO,
                                          ADAP.DESCRIPCION
                                  FROM ADMI_DISPOSITIVO_APP ADAP
                                  WHERE CODIGO_DISPOSITIVO = :strCodigoDispositivo
                                  AND PERSONA_ID           = :intIdPersona ";

            $objNtvQuery->setParameter('strCodigoDispositivo', $arrayParametros['codigoDispositivo']);
            $objNtvQuery->setParameter('intIdPersona', $arrayParametros['personaId']);

            $objRsm->addScalarResult('CODIGO_DISPOSITIVO', 'codigoDispositivo', 'string');
            $objRsm->addScalarResult('DESCRIPCION', 'descripcion', 'string');
            $objNtvQuery->setSQL($strQuery);
            $arrayResultado = $objNtvQuery->getResult();
        }
        catch(\Exception $e)
        {
            error_log($e->getMessage());
        }
        return $arrayResultado;
    }

    /**
     * getListadoDispRazonSocial
     *
     * Obtiene la cantidad de dispositivo perteneciente a una razon social.
     * Costo: 5
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 20-01-2019
     *
     * @param  array $arrayParametros[
     *                                  'strEstado'     => estado del dispositivo
     *                               ]
     *
     * @return array $arrayResultado
     */
    public function getListadoDispRazonSocial($arrayParametros)
    {
        $arrayResultado     = array();
        $objRsm             = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery        = $this->_em->createNativeQuery(null, $objRsm);

        $strQuery           = "SELECT ADAP.PERSONA_ID PERSONA_ID,
                                      COUNT(*) CONTAR
                              FROM ADMI_DISPOSITIVO_APP ADAP
                              WHERE ADAP.ESTADO = :strEstado
                              GROUP BY ADAP.PERSONA_ID ";

        $objNtvQuery->setParameter('strEstado', $arrayParametros['strEstado']);

        $objRsm->addScalarResult('PERSONA_ID', 'personaId', 'integer');
        $objRsm->addScalarResult('CONTAR',     'cantidad',  'integer');
        $objNtvQuery->setSQL($strQuery);
        $arrayResultado = $objNtvQuery->getResult();
        return $arrayResultado;
    }
}
