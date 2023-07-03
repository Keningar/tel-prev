<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoEncuestaRepository extends EntityRepository
{    
     /**
     * getCodigoEncuesta
     *
     * Metodo encargado de obtener el codigo de encuesta segun como se hayan ido creando
     *         
     * @return $codigo 
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 25-07-2014
     */
    public function getCodigoEncuesta()
    {       	          
        $em = $this->_em;
        $sql = $em->createQuery("SELECT count(a) as total    
                   from                    
                   schemaBundle:InfoEncuesta a                        
                   where  
                   a.estado = :estado                                                  
                 ");
        $sql->setParameter('estado', "Activo");
        
        $resultado = $sql->getOneOrNullResult();    
        
        if($resultado)
        {
            $total = $resultado['total'];
            $codigo = 'ENC-'.($total+1);
        }
        else
        {
            $codigo = 'ENC';
        }                             
                      
        return $codigo;            
    
    }

    /**
     * getCodigoEncuesta
     *
     * Metodo encargado de obtener el codigo de encuesta segun como se hayan ido creando
     *         
     * @return $codigo 
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 20-06-2015
     */
    public function getCodigoActaEntrega()
    {       	          
        $em = $this->_em;
        $sql = $em->createQuery("SELECT count(a) as total
                   from
                   schemaBundle:InfoEncuesta a
                   where
                   a.estado = :estado
                 ");
        $sql->setParameter('estado', "Activo");
        
        $resultado = $sql->getOneOrNullResult();
        
        if($resultado)
        {
            $total = $resultado['total'];
            $codigo = 'ACT-ENT-'.($total+1);
        }
        else
        {
            $codigo = 'ACT-ENT-';
        }
                      
        return $codigo;            
    
    }
}
