<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoHistorialIngresoAppRepository extends EntityRepository
{
    /**
     * Función que obtiene el último estado de sessión
     * @param array $arrayParametros [  'strLogin'      => login de cliente,
     *                                  'strCodEmpresa' => código de la empresa,
     *                                  'strEsIsb'      => es un servicio Internet Small Business]
     *
     * @return object Retorna el estado de sessión de los dispositivo en la app.
     *
     * @author Creado: Ronny Moran <rmoranc@telconet.ec>
     * @version 1.0 15-07-2018
     */
    public function getUltimoEstadoSession($arrayParametros)
    {
        $objQuery = $this->_em->createQuery();
        $strDql   = "SELECT MAX(IHIA)
                    FROM schemaBundle:InfoHistorialIngresoApp IHIA
                    WHERE IHIA.codigoDispositivo = :codigoDispositivo
                    AND IHIA.personaId = :personaId";

        $objQuery->setParameter('codigoDispositivo', $arrayParametros['codigoDispositivo']);
        $objQuery->setParameter('personaId', $arrayParametros['personaId']);

        $objQuery->setDQL($strDql);
        $objQuery->getMaxResults(1);
        return $objQuery->getSingleScalarResult();
    }
}
