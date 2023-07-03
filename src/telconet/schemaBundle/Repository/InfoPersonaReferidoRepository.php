<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoPersonaReferidoRepository extends EntityRepository
{
	public function findPorPersona($idPersona){	
		$query = $this->_em->createQuery("SELECT b
		FROM 
                schemaBundle:InfoPersona a,schemaBundle:InfoPersonaReferido b, schemaBundle:InfoPersonaEmpresaRol c
		WHERE 
                a.id=:idPersona AND
                c.personaId=a.id AND
                c.id=b.personaEmpresaRolId AND b.estado='Activo'");
		$query->setParameter('idPersona', $idPersona);
		$query->setMaxResults(1);
		$datos = $query->getOneOrNullResult();
             
		return $datos;
	}
}
