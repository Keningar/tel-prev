<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiTipoDocumentoGeneralRepository extends EntityRepository
{
    
    /**
     * findByCodigosTipoDocumento, Consulta que el vehículo que se quiere asignar a la cuadrilla no se encuentre ocupado 
     * por otra cuadrilla en un turno que se solape.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 18-12-2015
     * 
     * @param  array $arrayParametros //array con todos los códigos de los documentos que serán obligatorios
     * 
     * @return array $arrayResultado Retorna el array obtenido de la consulta
     */
    public function findByCodigosTipoDocumento($arrayParametros)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb ->select('admi_tipo_documento_general')
            ->from('schemaBundle:AdmiTipoDocumentoGeneral','admi_tipo_documento_general')
            ->where($qb->expr()->in('admi_tipo_documento_general.codigoTipoDocumento', $arrayParametros))
            ->andWhere( "admi_tipo_documento_general.estado = :estado")
            ->setParameter('estado', 'Activo');
        $query = $qb->getQuery();

        $arrayResultado=$query->getResult();

        return $arrayResultado;  

    }
    
    
}
