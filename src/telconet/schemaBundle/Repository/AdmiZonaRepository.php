<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * AdmiZonaRepository.
 *
 * Repositorio que se encargará de administrar las funcionalidades adicionales que se relacionen con la entidad AdmiZona
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 13-10-2015
 */
class AdmiZonaRepository extends EntityRepository
{
    public function getJSONZonasByParametros($arrayParametros)
    {
        
        $arrayEncontrados   = array();
        $arrayResultado     = $this->getResultadoZonasByParametros($arrayParametros);
        $resultado          = $arrayResultado['resultado'];
        $intTotal           = $arrayResultado['total'];
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $zona)
            {
                $item              = array();
                $item['strValue']  = $zona->getId();
                $item['strNombre'] = $zona->getNombreZona();
                
                $arrayEncontrados[] = $item;
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }

    /*
    * Costo: 3
    *
    * getZonas
    * Obtiene las Zonas
    *
    * @param array $arrayParametros[ "strEstado"     => estado de la zona,
    *                                "strNombreZona" => nombre de Zona,
    *                                "strOpcion"     => Tipo de accion a realizar (consultar,actualizar,nuevo)
    *                                "intIdZona"     => Id de la Zona
    *                              ]
    *
    * @return array $arrayRespuesta
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 04-04-2018
    *
    * Costo: 8
    *
    * Modificado: Se realiza ajustes en el metodo agregando el filtro intIdZona para el consumo dal webService de Zonas.
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 19-06-2018
	*/
    public function getZonas($arrayParametros)
    {
        $strEstado     = $arrayParametros['strEstado'];
        $strNombreZona = $arrayParametros['strNombreZona'];
        $strOpcion     = $arrayParametros['strOpcion'];
        $intIdZona     = $arrayParametros['intIdZona'];

        try
        {
            $objQuery = $this->_em->createQuery();

            $strSql = "SELECT azo "
                      ."FROM schemaBundle:AdmiZona azo "
                     ."WHERE azo.id = azo.id ";

            if ($strEstado != 'Todos' && !is_null($strEstado))
            {
                $strSql  .= "AND upper(azo.estado) = upper(:strEstado) ";
                $objQuery->setParameter("strEstado", $strEstado);
            }

            if (!is_null($strNombreZona))
            {
                if ($strOpcion === 'consultar')
                {
                    $strSql .= "AND upper(azo.nombreZona) like upper(:strNombreZona) ";
                    $strNombreZona = "%".$strNombreZona."%";
                }
                else
                {
                    $strSql .= "AND upper(azo.nombreZona) = upper(:strNombreZona) ";
                }
                $objQuery->setParameter("strNombreZona", $strNombreZona);
            }

            if (!is_null($intIdZona))
            {
                if ($strOpcion === 'actualizar')
                {
                    $strSql .= "AND azo.id <> :intIdZona ";
                }
                else
                {
                    $strSql .= "AND azo.id = :intIdZona ";
                }
                $objQuery->setParameter("intIdZona", $intIdZona);
            }

            $strSql .= "order by azo.nombreZona ASC";

            $objQuery->setDQL($strSql);

            $arrayRespuesta = $objQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error en el metodo AdmiZonaRepository.getZonas -> ".$objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
    * getResultadoZonasByParametros
    *
    * Método que consulta las zonas por los criterios enviados como parámetros
    *     
    * @param string $nombre
    *
    * @return $arrayResultado con el resultado de query ejecutado
    *
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.0 
    */
    public function getResultadoZonasByParametros($arrayParametros)
    {
        $arrayResultado['resultado']    = "";
        $arrayResultado['total']        = "";
        try
        {
            $query      = $this->_em->createQuery();
            $queryCount = $this->_em->createQuery();
            $strWhere="";
            $strSelect      = "SELECT z	";
            $strSelectCount = "SELECT COUNT(z) ";
            $strFrom        = " FROM 					
                                schemaBundle:AdmiZona z											
                                WHERE 
                                z.estado not in (:estados) ";
            
            $query->setParameter('estados', array_values($arrayParametros['estados']));
            $queryCount->setParameter('estados', array_values($arrayParametros['estados']));
            
            if(isset($arrayParametros["nombre"]))
            {
                if($arrayParametros["nombre"])
                {
                    $strWhere.=" AND UPPER(z.nombreZona) like UPPER(:nombre) ";	
                    $query->setParameter('nombre', '%' . $arrayParametros["nombre"] . '%');
                    $queryCount->setParameter('nombre', '%' . $arrayParametros["nombre"] . '%');
                }
                
            }

            $strQuery=$strSelect.$strFrom.$strWhere;
            $query->setDQL($strQuery);
            $arrayRegistros = $query->getResult();
            
            
            $strQueryCount=$strSelectCount.$strFrom.$strWhere;
            $queryCount->setDQL($strQueryCount);
            $intTotal       = $queryCount->getSingleScalarResult();

            $arrayResultado['resultado']    = $arrayRegistros;
            $arrayResultado['total']        = $intTotal;
            
        } 
        catch (Exception $e) 
        {
            error_log($e);		    
        }
        return $arrayResultado;                         
    }

    /**
     * Método que se encarga de obtener todas las zonas con sus respectivos responsables.
     *
     * costo 5
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 - 10-09-2018
     *
     * @param $arrayParametros [
     *                              "strCaracteristica" => Descripción de la característica,
     *                              "strEstadoAc"       => Estado de la tabla ADMI_CARACTERISTICA,
     *                              "strEstadoIperc"    => Estado de la INFO_PERSONA_EMPRESA_ROL_CARAC,
     *                              "strEstadoIper"     => Estado de la INFO_PERSONA_EMPRESA_ROL,
     *                              "strEstadoIp"       => Estado de la INFO_PERSONA,
     *                              "boolSubQuery"      => Valor booleano de para agregar el sub-query,
     *                              "strEstadoZona"     => estado de la ADMI_ZONA,
     *                              "strNombreZona"     => nombre de la Zona,
     *                              "intIdZona"         => Id de la Zona
     *                          ]
     *
     * @return $arrayResultado
     */
    public function getZonasNativeQuery($arrayParametros)
    {
        $arrayResultado = array();

        try
        {
            $objResultSetMap   = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery    = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strWhere          = '';
            $strSubQueryWhere  = '';
            $strSubQuery       = '';

            if(isset($arrayParametros['boolSubQuery']) && $arrayParametros['boolSubQuery'])
            {
                if(isset($arrayParametros['strCaracteristica']) && !empty($arrayParametros['strCaracteristica']))
                {
                    $strSubQueryWhere .= " AND AC.DESCRIPCION_CARACTERISTICA = :strCaracteristica ";
                    $objNativeQuery->setParameter("strCaracteristica", $arrayParametros['strCaracteristica']);
                }

                if(isset($arrayParametros['strEstadoAc']) && !empty($arrayParametros['strEstadoAc']))
                {
                    $strSubQueryWhere .= " AND AC.ESTADO = :strEstadoAc ";
                    $objNativeQuery->setParameter("strEstadoAc", $arrayParametros['strEstadoAc']);
                }

                if(isset($arrayParametros['strEstadoIperc']) && !empty($arrayParametros['strEstadoIperc']))
                {
                    $strSubQueryWhere .= " AND IPERC.ESTADO = :strEstadoIperc ";
                    $objNativeQuery->setParameter("strEstadoIperc", $arrayParametros['strEstadoIperc']);
                }

                if(isset($arrayParametros['strEstadoIper']) && !empty($arrayParametros['strEstadoIper']))
                {
                    $strSubQueryWhere .= " AND IPER.ESTADO = :strEstadoIper ";
                    $objNativeQuery->setParameter("strEstadoIper", $arrayParametros['strEstadoIper']);
                }

                if(isset($arrayParametros['strEstadoIp']) && !empty($arrayParametros['strEstadoIp']))
                {
                    $strSubQueryWhere .= " AND IP.ESTADO = :strEstadoIp ";
                    $objNativeQuery->setParameter("strEstadoIp", $arrayParametros['strEstadoIp']);
                }

                $strSubQuery = ",(SELECT IP.NOMBRES ||' '||IP.APELLIDOS ||'@@'||IP.ID_PERSONA ||'@@'||IPER.ID_PERSONA_ROL
                                    FROM DB_COMERCIAL.ADMI_CARACTERISTICA            AC,
                                         DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC IPERC,
                                         DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL       IPER,
                                         DB_COMERCIAL.INFO_PERSONA                   IP
                                    WHERE AC.ID_CARACTERISTICA          = IPERC.CARACTERISTICA_ID
                                      AND IPERC.PERSONA_EMPRESA_ROL_ID  = IPER.ID_PERSONA_ROL
                                      AND IPER.PERSONA_ID               = IP.ID_PERSONA
                                      AND IPERC.VALOR                   = ZONA.ID_ZONA
                                      $strSubQueryWhere
                                      AND ROWNUM = 1) RESPONSABLE_ZONA";

                $objResultSetMap->addScalarResult('RESPONSABLE_ZONA', 'responsableZona', 'string');
            }

            if(isset($arrayParametros['strEstadoZona']) && !empty($arrayParametros['strEstadoZona'])
                && strtoupper($arrayParametros['strEstadoZona']) !== 'TODOS')
            {
                $strWhere .= " AND UPPER(ZONA.ESTADO) = :strEstadoZona ";
                $objNativeQuery->setParameter("strEstadoZona", strtoupper($arrayParametros['strEstadoZona']));
            }

            if(isset($arrayParametros['strNombreZona']) && !empty($arrayParametros['strNombreZona']))
            {
                $strWhere .= " AND UPPER(ZONA.NOMBRE_ZONA) LIKE :strNombreZona ";
                $objNativeQuery->setParameter("strNombreZona", '%'.strtoupper($arrayParametros['strNombreZona']).'%');
            }

            if(isset($arrayParametros['intIdZona']) && !empty($arrayParametros['intIdZona']))
            {
                $strWhere .= " AND ZONA.ID_ZONA = :intIdZona ";
                $objNativeQuery->setParameter("intIdZona", strtoupper($arrayParametros['intIdZona']));
            }

            $strSql = "SELECT ZONA.ID_ZONA     ID_ZONA,
                              ZONA.NOMBRE_ZONA NOMBRE_ZONA,
                              ZONA.ESTADO      ESTADO
                              $strSubQuery
                         FROM DB_GENERAL.ADMI_ZONA ZONA
                       WHERE ZONA.ID_ZONA = ZONA.ID_ZONA
                        $strWhere
                       ORDER BY ZONA.NOMBRE_ZONA";

            $objResultSetMap->addScalarResult('ID_ZONA'     , 'id_zona'     , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_ZONA' , 'nombre_zona' , 'string');
            $objResultSetMap->addScalarResult('ESTADO'      , 'estado'      , 'string');

            $objNativeQuery->setSQL($strSql);

            $arrayResultado['status'] = 'ok';
            $arrayResultado['result'] = $objNativeQuery->getResult();
        }
        catch (\Exception $objException)
        {
            error_log("Error AdmiZonaRepository.getZonasNativeQuery -> mensaje: ".$objException->getMessage()." detalle: ".$objException);
            $arrayResultado["status"]  = 'fail';
            $arrayResultado["message"] = $objException->getMessage();
        }
        return $arrayResultado;
    }
}
