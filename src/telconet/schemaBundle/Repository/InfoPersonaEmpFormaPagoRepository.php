<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoPersonaEmpFormaPagoRepository extends EntityRepository
{
     /**
     * Documentación para el método 'findDatosPersonaEmpFormaPago'.
     * Obtiene los datos de la forma de Pago ingresada a nivel de Persona Empresa Rol
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>       
     * @version 1.1  11-05-2015      
     * @param integer $id_persona
     * @param string  $id_empresa
     * @return \telconet\schemaBundle\Entity\InfoPersonaEmpFormaPago
     */
    public function findDatosPersonaEmpFormaPago($id_persona, $id_empresa)
    {

        $query = $this->_em->createQuery("SELECT e
		FROM 
                schemaBundle:InfoPersona a, schemaBundle:InfoPersonaEmpresaRol b,
                schemaBundle:InfoEmpresaRol c, schemaBundle:AdmiRol d, schemaBundle:InfoPersonaEmpFormaPago e
		WHERE 
                a.id=:id_persona and 
                a.id=b.personaId AND               
                b.empresaRolId=c.id AND                
                c.rolId=d.id AND                
                d.descripcionRol='Pre-cliente' and                
                c.empresaCod=:id_empresa and 
                e.personaEmpresaRolId=b.id  and
                e.estado=:strEstado
                and b.estado in (:strEstadoPerEmRol)");
        
        $arrayEstados = array('Pendiente','Activo');        
        $query->setParameters(array('id_persona' => $id_persona, 'id_empresa' => $id_empresa, 'strEstado' =>'Activo',
            'strEstadoPerEmRol'=>$arrayEstados));
        $datos = $query->getOneOrNullResult();

        return $datos;
    }

}
