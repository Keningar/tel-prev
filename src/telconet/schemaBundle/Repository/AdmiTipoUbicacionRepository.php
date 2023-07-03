<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTipoUbicacionRepository extends EntityRepository
{
    /**
     * Devuelve un query builder para obtener los tipos de ubicacion activos
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findTiposUbicacionActivos()
    {
        return $qb =$this->createQueryBuilder("t")
                ->select("a")
                ->from('schemaBundle:AdmiTipoUbicacion a','')
                ->where("a.estado='Activo'");
    }

}
