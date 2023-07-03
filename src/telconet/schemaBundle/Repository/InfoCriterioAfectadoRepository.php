<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoCriterioAfectadoRepository extends EntityRepository
{
    public function deleteCriteriosByDetalle($id_detalle)
    {
        $sql = "DELETE FROM schemaBundle:InfoCriterioAfectado ca       
                WHERE ca.detalleId = '$id_detalle' 
               ";           
        $query = $this->_em->createQuery($sql);   
        $datos = $query->execute();          
        return $datos;                	             
    }
}
