<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use telconet\schemaBundle\Entity\InfoRegistroEmpleado;


/*
 * Documentación para la clase InfoPersonaEmpresaRolRepository.
 * La clase InfoPersonaEmpresaRolRepository permite implementar métodos de consulta sobre registros de la tabla INFO_REGISTRO_EMPLEADO
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 11-11-2016
 */
class InfoRegistroEmpleadoRepository extends EntityRepository
{
    /**
     * getRegistrosEmpleado, obtiene los registros de empleados por los parametros enviados
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 09-11-2016
     * @since 1.0
     * @param array  $arrayParametros Obtiene los criterios de busqueda
     * @return array $arrayResultado  Retorna el array de datos y conteo de datos
     * 
     */
    public function getRegistrosEmpleado($arrayParametros)
    {
        $dateFechaDesdeReg = $arrayParametros['dateFechaDesde'];
        $datefechaHastaReg = $arrayParametros['datefechaHasta'];
        $arrayEstado       = $arrayParametros['arrayEstado']       ? $arrayParametros['arrayEstado'] : [];
        $arrayTipoRegistro = $arrayParametros['arrayTipoRegistro'] ? $arrayParametros['arrayTipoRegistro'] : [];        
        
        $whereAdicional   = "";
        $query            = $this->_em->createQuery();
              
        if(isset($dateFechaDesdeReg) && isset($datefechaHastaReg))
        {
            if($dateFechaDesdeReg!="" && $datefechaHastaReg!="" )
            {
                $whereAdicional    = $whereAdicional."AND ire.feRegistro > =  :fechaDesde AND ire.feRegistro<= :fechaHasta ";              
                $datefechaHastaReg = $datefechaHastaReg." 23:59:59";
                $query->setParameter("fechaDesde", date('Y/m/d',strtotime($dateFechaDesdeReg)));
                $query->setParameter("fechaHasta", date('Y/m/d H:i:s',strtotime($datefechaHastaReg)));
            }
        }
       
        if(isset($arrayEstado))
        {
            if(count($arrayEstado)>0)
            {
                $whereAdicional =  $whereAdicional. "AND  ire.estado in (:estado) ";
                $query->setParameter("estado", $arrayEstado);
            }
        }
        if(isset($arrayTipoRegistro))
        {
            if(count($arrayTipoRegistro)>0)
            {
                $whereAdicional =  $whereAdicional. "AND  ire.tipoRegistro in (:tipoRegistro) ";
                $query->setParameter("tipoRegistro", $arrayTipoRegistro);
            }
        }
        $query->setDQL("
                        SELECT ire.id, 
						       ire.tipoRegistro, 
							   ire.feRegistro, 
							   ire.feCreacion, 
							   ire.usrCreacion, 
							   ire.estado,
							   ire.latitud, 
							   ire.longitud, 
							   ire.permiso
                        FROM 
                                schemaBundle:InfoRegistroEmpleado ire
                        WHERE
                                ire.personaEmpresaRolId = :personaEmpresaRolId ".
                                $whereAdicional.
                                " order by ire.feRegistro ASC "  );
                                $query->setParameter("personaEmpresaRolId", $arrayParametros['intPersonaEmpresaRolId']);

        $arrayDatos                       = $query->getResult();
        $arrayResultado['arrayRegistros'] = $arrayDatos;
        $arrayResultado['total']          = count($arrayDatos);
        return $arrayResultado;
    }
    

    /**
     * findMaxRegistro
     *
     * Método que retorna el maximo registro de fin de jornada laboral                                   
     *      
     * @param array $arrayParametros
     * [
     *     intIdPersonaEmpresaRol => id de persona empresa rol del empleado
     * ]
     * 
     * Costo query 6
     * 
     * @return object $objResultado
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-11-2016
     */
    public function getMaxRegistro( $arrayParametros )
    {   
        $strSelect = "SELECT ire ";
        $strFrom   = "FROM schemaBundle:InfoRegistroEmpleado ire ";
        $strWhere  = "WHERE ire.feRegistro = (
                                              SELECT MAX(ire2.feRegistro)
                                              FROM schemaBundle:InfoRegistroEmpleado ire2
                                              WHERE ire2.personaEmpresaRolId = :intIdPersonaEmpresaRol
                                             ) 
                      AND ire.personaEmpresaRolId = :intIdPersonaEmpresaRol";

        $strSql = $strSelect.$strFrom.$strWhere;
        $query  = $this->_em->createQuery($strSql);
        $query->setParameter("intIdPersonaEmpresaRol", $arrayParametros['intPersonaEmpresaRolId']);

        $objResultado = $query->getOneOrNullResult();

        return $objResultado;
    }
   
    
}
