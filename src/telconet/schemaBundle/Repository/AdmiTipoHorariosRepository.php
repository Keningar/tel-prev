<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use \telconet\schemaBundle\Entity\ReturnResponse;

use telconet\schemaBundle\DependencyInjection\BaseRepository;

class AdmiTipoHorariosRepository extends EntityRepository
{
    /*
     * Método encargado obtener el listado de tipos de horarios
     *
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 16-03-2023
     *
     * 
     * @return Array $arrayResultado
     */
    public function getTiposHorarios()
    {
        $arrayResultado = array();
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql         = " SELECT ATH.ID_TIPO_HORARIO,ATH.NOMBRE_TIPO_HORARIO 
                              FROM DB_HORAS_EXTRAS.ADMI_TIPO_HORARIOS ATH 
                              WHERE ATH.ESTADO='Activo' ORDER BY ATH.NOMBRE_TIPO_HORARIO ASC";
        $objRsm->addScalarResult('ID_TIPO_HORARIO', 'idTipoHorario', 'string');
        $objRsm->addScalarResult('NOMBRE_TIPO_HORARIO', 'nombreTipoHorario', 'string');
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();

        return $arrayResultado;
        
    }

    /*
     * findOneById
     * Método encargado obtener un tipo de horario
     *
     * @author Katherine Solis <ksolis@telconet.ec>
     * @version 1.0 - 16-03-2023
     * 
     * @param  integer $idTipoHorario
     * 
     * @return Array $arrayResultado
     */
    public function findOneById($intIdTipoHorario)
    {
        $arrayResultado = array();
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null, $objRsm);
        
        $strSql         = " SELECT ATH.ID_TIPO_HORARIO,ATH.NOMBRE_TIPO_HORARIO 
                              FROM DB_HORAS_EXTRAS.ADMI_TIPO_HORARIOS ATH 
                              WHERE ATH.ESTADO ='Activo' 
                                    AND ATH.ID_TIPO_HORARIO = :id_TipoHorario
                              ORDER BY ATH.NOMBRE_TIPO_HORARIO ASC";
        $objRsm->addScalarResult('ID_TIPO_HORARIO', 'idTipoHorario', 'string');
        $objRsm->addScalarResult('NOMBRE_TIPO_HORARIO', 'nombreTipoHorario', 'string');
        $objQuery->setParameter('id_TipoHorario', $intIdTipoHorario);  
        $objQuery->setSQL($strSql);
        $arrayResultado = $objQuery->getResult();

        return $arrayResultado;
        
    }
}
