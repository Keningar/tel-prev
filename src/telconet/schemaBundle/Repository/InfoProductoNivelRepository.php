<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoProductoNivelRepository extends EntityRepository
{
    public function findPorEstado($productoid,$estado)
    {
        $query = $this->_em->createQuery("SELECT ipn
            FROM 
            schemaBundle:InfoProductoNivel ipn
            WHERE 
            ipn.estado='".$estado."' AND ipn.productoId=".$productoid);
            //echo $query->getSQL(); die;
            $datos = $query->getResult();

            return $datos;
    }
}
