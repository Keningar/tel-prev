<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoTramoRepository extends EntityRepository
{
    public function generarJsonTramos()
    {
        $arr_encontrados = array();
        
        $qb = $this->_em->createQueryBuilder();
        $qb->select('tramo')
           ->from('schemaBundle:InfoTramo','tramo');
           
        $query = $qb->getQuery();
        $results = $query->getResult();
        
        if ($results) {
            
            $num = count($results);
            
            foreach ($results as $entidad)
            {
                $elementoA = explode(".",$this->_em->getRepository('schemaBundle:InfoElemento')->find($entidad->getElementoAId())->getNombreElemento());
                $elementoB = explode(".",$this->_em->getRepository('schemaBundle:InfoElemento')->find($entidad->getElementoBId())->getNombreElemento());
                $arr_encontrados[]=array('idTipo' =>$entidad->getId(),
                                         'nombreTipo' =>$elementoA[0].'-'.$elementoB[0]);
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }

    public function getTramoXNombre($nombreTramo)
    {
        
        
        $nombre = explode("-",$nombreTramo);
        $elemento1 = $this->_em->getRepository('schemaBundle:InfoElemento')->findByNombreElemento($nombre[0].'.telconet.net');
        $elemento2 = $this->_em->getRepository('schemaBundle:InfoElemento')->findByNombreElemento($nombre[1].'.telconet.net');
        
        $qb = $this->_em->createQueryBuilder();
        $qb->select('tramo')
           ->from('schemaBundle:InfoTramo','tramo')
           ->where('tramo.elementoAId = ?1')
           ->setParameter(1, $elemento1[0]->getId()) 
           ->andWhere('tramo.elementoBId = ?2')
           ->setParameter(2, $elemento2[0]->getId());
        $query = $qb->getQuery();
        $results = $query->getResult();
        
        return $results[0];
        
    }    

    
    public function generarJsonTramosParaTareas()
    {
        $arr_encontrados = array();
        
        $qb = $this->_em->createQueryBuilder();
        $qb->select('tramo')
           ->from('schemaBundle:InfoTramo','tramo');
           
        $query = $qb->getQuery();
        $results = $query->getResult();
        
        if ($results) {
            
            $num = count($results);
            
            foreach ($results as $entidad)
            {
                $elementoA = explode(".",$this->_em->getRepository('schemaBundle:InfoElemento')->find($entidad->getElementoAId())->getNombreElemento());
                $elementoB = explode(".",$this->_em->getRepository('schemaBundle:InfoElemento')->find($entidad->getElementoBId())->getNombreElemento());
                $arr_encontrados[]=array('id' =>$entidad->getId(),
                                         'nombre' =>$elementoA[0].'-'.$elementoB[0]);
            }
            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }   

    
}
