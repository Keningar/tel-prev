<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoContactoServicioRepository extends EntityRepository
{
        public function findPorServicioPorPersonaContacto($idServicio,$idPersonaContacto){            
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoContactoServicio a
		WHERE 
                a.personaContactoId=$idPersonaContacto AND
                a.servicioId=$idServicio AND
                a.estado='Activo'");
		$datos = $query->getResult();
		return $datos;
	}
        public function findPorServicio($idServicio){            
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoContactoServicio a
		WHERE 
                a.servicioId=$idServicio AND
                a.estado='Activo'");
		$datos = $query->getResult();
		return $datos;
	}        
}
