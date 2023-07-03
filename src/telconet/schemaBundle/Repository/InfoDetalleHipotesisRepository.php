<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoDetalleHipotesisRepository extends EntityRepository
{
    public function getSintomasByCaso($id_caso)
    {
        $sql = "SELECT DISTINCT s.id          
                FROM schemaBundle:InfoDetalleHipotesis d, schemaBundle:AdmiSintoma s            
                WHERE d.casoId = '$id_caso' 
                AND d.sintomaId = s.id 
                AND d.sintomaId is not null        
               ";           
        $query = $this->_em->createQuery($sql);   
        $datos = $query->getResult();          
        return $datos;                	             
    }
	
    public function getOneDetalleByCasoSintoma($id_caso, $id_sintoma)
    {
        $sql = "SELECT d         
                FROM schemaBundle:InfoDetalleHipotesis d        
                WHERE d.casoId = '$id_caso' 
                AND d.sintomaId = '$id_sintoma'               
                ORDER BY d.id ASC
               ";           
        $query = $this->_em->createQuery($sql);   
        $datos = $query->setFirstResult(0)->setMaxResults(1)->getOneOrNullResult();          
        return $datos;                	             
    }
	
    public function getOneDetalleByCasoHipotesis($id_caso, $id_hipotesis)
    {
        $sql = "SELECT d         
                FROM schemaBundle:InfoDetalleHipotesis d        
                WHERE d.casoId = '$id_caso' 
                AND d.hipotesisId = '$id_hipotesis'   
				AND d.sintomaId is null  
                ORDER BY d.id ASC
               ";           
        $query = $this->_em->createQuery($sql);   
        $datos = $query->setFirstResult(0)->setMaxResults(1)->getOneOrNullResult();          
        return $datos;                	             
    }
    
    public function getOneDetalleByIdCaso($id_caso)
    {
        $sql = "SELECT MAX(d.id) as idDetalleHipotesis, c.id as idCaso  ,h.id as hipotesisId
                FROM schemaBundle:InfoDetalleHipotesis d ,schemaBundle:InfoCaso c  , 
                       schemaBundle:AdmiHipotesis h      
                WHERE d.casoId = '$id_caso' 
                AND  d.casoId=c.id 
                AND h.id=d.hipotesisId
                group by c.id, h.id
                
               ";           
        $query = $this->_em->createQuery($sql);   
        //$datos = $query->setFirstResult(0)->setMaxResults(1)->getOneOrNullResult();          
       $datos = $query->getResult();   
       /*echo($query->getSQL());
       die();
         */
        
        return $datos;                	             
    }
    
}