<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class MigraArckmmRepository extends EntityRepository
{
	public function getExisteContabilidad($id_pago_det,$empresa_id)
	{
		$query = $this->_em->createQuery("select ma 
			from schemaBundle:MigraArckmm ma
			where 
			ma.noCia= '".$empresa_id."'
			and ma.noDocu = ".$id_pago_det);
			
		$total=count($query->getResult());
		$datos = $query->getResult();
		echo $query->getSQL();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
	
	public function getExisteCruceContabilidad($id_pago_det,$empresa_id)
	{
		$query = $this->_em->createQuery("select ma 
			from schemaBundle:MigraArckmm ma
			where 
			ma.noCia= '".$empresa_id."'
			and ma.noDocu = ".$id_pago_det.'30');
			
		$total=count($query->getResult());
		$datos = $query->getResult();
		echo $query->getSQL();
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
	
	public function getExisteAnuladosContabilidad($id_pago_det,$empresa_id)
	{
		$query = $this->_em->createQuery("select ma 
			from schemaBundle:MigraArckmm ma
			where 
			ma.tipoDoc='ND'
			and ma.noCia= '".$empresa_id."'
			and ma.noDocu = ".$id_pago_det);
			
		$total=count($query->getResult());
		$datos = $query->getResult();
		//echo $query->getSQL();//die;
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
}
