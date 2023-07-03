<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoPlantillaPreguntaRepository extends EntityRepository
{
       /**
     * getPreguntasPorPlantilla
     *
     * Metodo encargado de obtener todas las preguntas relacionadas a una plantilla
     *         
     * @return $plantilla 
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 28-07-2014
     */
    public function getPreguntasPorPlantilla($plantilla)
    {       	  
	$qb = $this->_em->createQueryBuilder();
	$qb->select('a')
	    ->from('schemaBundle:InfoPlantillaPregunta','a')	   
	    ->where('a.plantillaId = ?1');	                              	
	$qb->setParameter(1, $plantilla);  
	$qb->orderBy("a.id", 'ASC');
                       
        $query = $qb->getQuery();
        
        return $query->getResult();
               
    }

}
