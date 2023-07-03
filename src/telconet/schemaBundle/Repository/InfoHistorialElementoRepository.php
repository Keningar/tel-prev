<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoHistorialElementoRepository extends EntityRepository
{
    /**
     * Documentación para el método 'getMaxEstadoHistorialElemento'.
     *
     * Método utilizado para obtener el máximo estado del elemento, según los criterios enviados por el usuario
     *
     * @param string  $strObservacion
     * @return object $objResult
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 23-02-2016
    */
    public function getMaxEstadoHistorialElemento($strObservacion)
    {
        $objResult           = null;
        $query               = $this->_em->createQuery();
        $strWhereObservacion = "";
        
        if( $strObservacion )
        {
            $strWhereObservacion = "WHERE ihe2.observacion LIKE :observacion";
            
            $query->setParameter('observacion', "%".$strObservacion."%");
        }

        $dql = "SELECT ihe
                FROM schemaBundle:InfoElemento ie,
                     schemaBundle:InfoHistorialElemento ihe
                WHERE ie.id = ihe.elementoId
                  AND ie.estado = :estado
                  AND ihe.id = (
                                    SELECT MAX(ihe2.id)
                                    FROM schemaBundle:InfoHistorialElemento ihe2
                                    ".$strWhereObservacion."
                               )";

        $query->setParameter('estado', "Activo");
        $query->setDQL($dql);              

        $objResult = $query->getOneOrNullResult();
            
        return $objResult;
    }
    
}
