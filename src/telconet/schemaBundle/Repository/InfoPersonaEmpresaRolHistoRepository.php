<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoPersonaEmpresaRolHistoRepository extends EntityRepository
{
	public function findFechaCreacionPorPersona($idPersona){
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersonaHistorial a
		WHERE 
                a.personaId=$idPersona 
                 order by a.feCreacion ASC"
                        )->setMaxResults(1);
		$datos = $query->getOneOrNullResult();
		return $datos;
	}
	public function findUltimaModificacionPorPersona($idPersona){
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersonaHistorial a
		WHERE 
                a.personaId=$idPersona 
                 order by a.feCreacion DESC"
                        );

                if (count($query->getResult())>1)
                    $datos = $query->setMaxResults(1)->getOneOrNullResult();
                else
                    $datos=null;
                return $datos;
	}
	public function findHistorialPorPersonaEmpresaRol($idPersonaEmpresaRol){
		$query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoPersonaEmpresaRolHisto a
		WHERE 
                a.personaEmpresaRolId=$idPersonaEmpresaRol 
                 order by a.feCreacion ASC");
                $datos = $query->getResult();
                return $datos;
	}
    /**
     * Se agrega el orden por id descendiente
     * @author Luis Cabrera
     * @version 1.1
     * @since 08-05-2018
     */
	public function findUltimoEstadoPorPersonaEmpresaRol($idPersonaEmpresaRol){
		$query = $this->_em->createQuery("SELECT a.id as ultimo
		FROM 
                schemaBundle:InfoPersonaEmpresaRolHisto a
		WHERE 
                a.personaEmpresaRolId=$idPersonaEmpresaRol 
                 ORDER BY a.feCreacion DESC, a.id DESC");
				 //echo $query->getSQL();die;
                $datos = $query->setMaxResults(1)->getResult();
				//echo $query->getSQL(); die;
                return $datos;
	}     
     /**
     * Documentación para el método: 'getObtienePersEmpRolHisto'.
     * Busca un persona empresa rol por identificacion, descripcionTipoRol, codEmpresa y estados de persona empresa rol 
     * y obtiene el historial de su cancelacion
     * @param string $identificacion
     * @param array $arrayDescRoles 
     * @param mixed $codEmpresa
     * @param array $arrayEstados
     * @return $objInfoPersonaEmpresaRolHisto
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 
     */
    public function getObtienePersEmpRolHisto($identificacion,$arrayDescRoles,$codEmpresa, $arrayEstados)
    {
        $query_string = "SELECT perh
                        FROM 
                            schemaBundle:InfoPersona ip,
                            schemaBundle:InfoPersonaEmpresaRol per,
                            schemaBundle:InfoEmpresaRol er,
                            schemaBundle:AdmiRol rol,
                            schemaBundle:AdmiTipoRol trol,
                            schemaBundle:InfoPersonaEmpresaRolHisto perh
                        WHERE 
                            per.empresaRolId= er.id AND
                            er.rolId = rol.id AND
                            rol.tipoRolId= trol.id AND
                            per.personaId = ip.id AND
                            ip.identificacionCliente = :identificacion AND
                            trol.descripcionTipoRol in (:descRol) AND
                            er.empresaCod = :codEmpresa 
                            AND per.estado in (:estado) AND perh.estado in (:estado) 
                            AND per.id = perh.personaEmpresaRolId
                            ORDER BY perh.feCreacion DESC"
                        ;
                       
        $query = $this->_em->createQuery($query_string)->setFirstResult(0)->setMaxResults(1);       
        $query->setParameter('identificacion', $identificacion);
        $query->setParameter('descRol', $arrayDescRoles);
        $query->setParameter('codEmpresa', $codEmpresa);
        $query->setParameter('estado', $arrayEstados);               
        $objInfoPersonaEmpresaRolHisto =  $query->getOneOrNullResult();         
  
        return $objInfoPersonaEmpresaRolHisto;
    }
}
