<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoPlanProductoCaractRepository extends EntityRepository
{
    public function getCaracteristicaByParametros($planDetId, $productoId, $caracteristica)
    {   
        $query = "	SELECT 
						ppc.id, ppc.valor  
					FROM 
						schemaBundle:AdmiCaracteristica ac, 
						schemaBundle:AdmiProductoCaracteristica apc,
						schemaBundle:InfoPlanProductoCaract ppc 
						
					WHERE 
						ac.id = apc.caracteristicaId
						AND lower(ac.descripcionCaracteristica) = lower('$caracteristica')
						AND apc.productoId = '$productoId'
						AND ppc.productoCaracterisiticaId = apc.id
						AND ppc.planDetId = '$planDetId' ";

        return $this->_em->createQuery($query)->getResult();
    }
   /**
    * Funcion que devuelve los detalles de las caracteristicas de los productos que forman un plan en estado Activo    
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 23-07-2014     
    * @param string $strEstado     
    * @param integer $intPlanId
    * @see \telconet\schemaBundle\Entity\InfoPlanDet
    * @return $intCantidadplanId
    */
    public function getCaractPlanDetIdYEstado($intPlanDetId,$strEstado)
    {   
        $em = $this->_em; 
        $query = $em->createQuery("SELECT p 
              from                                                           
              schemaBundle:InfoPlanProductoCaract p         
              where                
              p.planDetId =:intPlanDetId                                           
              and p.estado=:strEstado");                
             $query->setParameter( 'intPlanDetId' , $intPlanDetId);                             
             $query->setParameter( 'strEstado' , $strEstado);                 
             
       $datos = $query->getResult();
        
        return $datos;
    }  	    	
}
