<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class MigraArcgaeRepository extends EntityRepository
{
	public function getExisteContabilidad($id_pago,$empresa_id)
	{
		$query = $this->_em->createQuery("select ma 
			from schemaBundle:MigraArcgae ma
			where 
			ma.noCia= '".$empresa_id."'
			and ma.noAsiento = '".$id_pago.'77'."'");
			
		$total=count($query->getResult());
		$datos = $query->getResult();
		//echo $query->getSQL();//die;
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
	
	public function getExisteCruceContabilidad($id_pago,$empresa_id)
	{
		$query = $this->_em->createQuery("select ma 
			from schemaBundle:MigraArcgae ma
			where 
			ma.noCia= '".$empresa_id."'
			and ma.noAsiento = '".$id_pago.'30'."'");
			
		$total=count($query->getResult());
		$datos = $query->getResult();
		echo $query->getSQL();//die;
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
	
	public function getExisteAnuladosContabilidad($id_pago,$empresa_id)
	{
		$query = $this->_em->createQuery("select ma 
			from schemaBundle:MigraArcgae ma
			where 
			ma.tipoComprobante='MRVP'
			and ma.noCia= '".$empresa_id."'
			and ma.noAsiento = '".$id_pago.'50'."'");
			
		$total=count($query->getResult());
		$datos = $query->getResult();
		//echo $query->getSQL();//die;
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
	
	
	public function getExisteFacturaContabilidad($id_factura,$empresa_id)
	{
		$query = $this->_em->createQuery("select ma 
			from schemaBundle:MigraArcgae ma
			where 
			ma.noCia= '".$empresa_id."'
			and ma.noAsiento = '".$id_factura."'");
			
		$total=count($query->getResult());
		$datos = $query->getResult();
		//echo $query->getSQL();die;
		$resultado['registros']=$datos;
		$resultado['total']=$total;
		
		return $resultado;
	}
	
	
}
