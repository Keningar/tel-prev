<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiBancoCtaContableRepository extends EntityRepository
{

    
    public function findBancosContables($empresaCod)
    {   
        $query = $this->_em->createQuery("SELECT DISTINCT bco.id, bco.descripcionBanco
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, 
				schemaBundle:AdmiBancoCtaContable bcoCont,
				schemaBundle:AdmiBanco bco
		WHERE 
				abtc.bancoId=bco.id AND
                abtc.id=bcoCont.bancoTipoCuentaId AND bcoCont.estado='Activo' AND bcoCont.empresaCod='".$empresaCod."'");

        $datos = $query->getResult();
        return $datos;
    }
	
    public function findCuentasByBancosContables($idBanco,$empresaCod)
    {   
        $query = $this->_em->createQuery("SELECT bcoCont.id, bcoCont.descripcion, 
		bcoCont.noCta, bcoCont.ctaContable
		FROM 
                schemaBundle:AdmiBancoTipoCuenta abtc, 
				schemaBundle:AdmiBancoCtaContable bcoCont,
				schemaBundle:AdmiBanco bco
		WHERE 
				abtc.bancoId=bco.id AND abtc.bancoId=$idBanco AND
                abtc.id=bcoCont.bancoTipoCuentaId AND bcoCont.estado='Activo' AND bcoCont.empresaCod='".$empresaCod."'");

        $datos = $query->getResult();
        return $datos;
    }	
    

}