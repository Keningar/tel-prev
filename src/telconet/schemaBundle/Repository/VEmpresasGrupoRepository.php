<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class VEmpresasGrupoRepository extends EntityRepository
{
    public function cargarEmpresas($IdsOcupados)
    {   
	$whereVar = "";
	if($IdsOcupados && count($IdsOcupados)>0)
	{
	    $string_empresas_implode = implode("', '", $IdsOcupados);
	    $string_empresas = "'".$string_empresas_implode."'";
	    $whereVar .= "WHERE e.id NOT IN ($string_empresas) ";
	}

        $query =    "SELECT e ".
                    "FROM schemaBundle:VEmpresasGrupo e ".
                    "$whereVar";

        return $this->_em->createQuery($query)->getResult();
    }
   
}