<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;

 /**
 * Clase InfoPuntoCaracteristicaRepository
 *
 * Clase que contiene los metodos de consulta a la BD para la entidad InfoPuntoCaracteristica
 *
 * @author Juan Lafuente. <jlafuente@telconet.ec>
 * @version 1.0 27-08-2015
 */
class InfoPuntoCaracteristicaRepository extends EntityRepository
{

    /**
     * Funcion getPuntoCaracteristica
     *
     * Funcion para obtener la caracteristica de un punto en el cual podriamos localizarlo por Punto, Caracteristica y Estado
     *
     * @param array $arrayParams[entityPunto             =>    InfoPunto Entity
     *                           strDescripcionCarac     =>    Descripcion de la Caracteristica
     *                           strEstado               =>    Estdo de la Caracteristica del InfoPunto]
     * 
     * @return array $arrayDatos
     *
     * @author Juan Lafuente <jlafuente@telconet.ec>
     * @version 1.0 08-10-2015
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.1 21-12-2015
     * @since 1.0
     * Se separó la obtención del DQL para generar una respuesta de tipo List y otra de tipo OneOrNull
     */
    public function getPuntoCaracteristica($arrayParams)
    {
        $objQuery   = $this->getDQLPuntoCaracteristica($arrayParams);
        $arrayDatos = $objQuery->getResult();
        return $arrayDatos;
    }

    /**
     * Documentación para el método 'getOnePuntoCaracteristica'.
     * 
     * Funcion para obtener la caracteristica de un punto filtrado por Punto, Caracteristica y Estado
     *
     * @param array $arrayParams[entityPunto         =>    InfoPunto Entity
     *                           strDescripcionCarac =>    Descripcion de la Caracteristica
     *                           strEstado           =>    Estdo de la Caracteristica del InfoPunto]
     * 
     * @return Entity InfoPuntoCaracteristica
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    public function getOnePuntoCaracteristica($arrayParams)
    {
        $objQuery = $this->getDQLPuntoCaracteristica($arrayParams);
        return $objQuery->getOneOrNullResult();
    }

    /**
     * Documentación para el método 'getDQLPuntoCaracteristica'.
     * 
     * Funcion para obtener el objeto Query para la consulta de la Característica o Características del punto
     *
     * @param array $arrayParams[entityPunto         =>    InfoPunto Entity
     *                           strDescripcionCarac =>    Descripcion de la Caracteristica
     *                           strEstado           =>    Estdo de la Caracteristica del InfoPunto]
     * 
     * @return Query Objeto-Query
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getDQLPuntoCaracteristica($arrayParams)
    {
        $objQuery = $this->_em->createQuery();

        $strSql = " SELECT 
                        ipc
                    FROM 
                        schemaBundle:InfoPuntoCaracteristica    ipc,
                        schemaBundle:AdmiCaracteristica         ac
                    WHERE 
                        ipc.caracteristicaId                    = ac.id ";

        //Permite la filtrar por el punto 
        if(!empty($arrayParams['entityPunto']))
        {
            $strSql .= " AND ipc.puntoId = :entityPunto ";
            $objQuery->setParameter('entityPunto',$arrayParams['entityPunto']);
        }

        //Permite la filtrar por la catacteristica
        if(!empty($arrayParams['strDescripcionCaracteristica']))
        {
            $strSql .= " AND ac.descripcionCaracteristica = :strDescripcionCaracteristica ";
            $objQuery->setParameter('strDescripcionCaracteristica',$arrayParams['strDescripcionCaracteristica']);
        }

        //Permite la filtrar por el estado
        if(!empty($arrayParams['strEstado']))
        {
            $strSql .= " AND ipc.estado = :strEstado ";
            $objQuery->setParameter('strEstado',$arrayParams['strEstado']);
        }            

        $objQuery -> setDQL($strSql);
        
        return $objQuery;
    }

    /**
     * Función que recupera las coordenadas sugeridas por el tecnico en campo
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 26/02/2018
     * @param type $arrayParametros[
     *                              intIdPunto:             integer:    id del punto,
     *                              intIdCaracteristica:    integer:    id caracteristica de actualizar coordenada,
     *                              strEstado:              string:     estado de la caracteristica
     *                             ]
     */
    public function obtenerCoordenadaSugerida($arrayParametros)
    {
        try
        {
            $objQuery  = $this->_em->createQuery();
            $strSelect = "SELECT CaracteristicaPunto ";
            $strFrom   = "FROM schemaBundle:InfoPuntoCaracteristica CaracteristicaPunto ";
            $strWhere  = "WHERE CaracteristicaPunto.caracteristicaId = :intIdCaracteristica
                          AND CaracteristicaPunto.estado             = :strEstado
                          AND CaracteristicaPunto.id                 = (SELECT MAX(ipca.id)
                                                                        FROM schemaBundle:InfoPuntoCaracteristica ipca
                                                                        WHERE ipca.puntoId         = :intIdPunto
                                                                        AND ipca.caracteristicaId  = :intIdCaracteristica
                                                                        AND ipca.estado            = :strEstado
                                                                        )";

            $objQuery->setParameter('intIdCaracteristica',  $arrayParametros['intIdCaracteristica']);
            $objQuery->setParameter('intIdPunto',  $arrayParametros['intIdPunto']);
            $objQuery->setParameter('strEstado',  $arrayParametros['strEstado']);

            $strSql = $strSelect.$strFrom.$strWhere;
            $objQuery->setDQL($strSql);

            $objInfoPuntoCaracteristica = $objQuery->getOneOrNullResult();
        }
        catch(\Exception $e)
        {
            error_log("Problemas al recuperar la información de la función InfoPuntoRepository:obtenerCoordenadaSugerida ".$e->getMessage());
        }
        return $objInfoPuntoCaracteristica;
    }

    /**
     * Función que devuelve la cantidad de coordenada sugerida para un punto
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 26/02/2018
     * @param type $arrayParametros[
     *                              intIdPunto:             integer:    id del punto,
     *                              intIdCaracteristica:    integer:    id caracteristica de actualizar coordenada,
     *                              strEstado:              string:     estado de la caracteristica
     *                             ]
     */
    public function obtenerCantidadCoordenadaSugerida($arrayParametros)
    {
        try
        {
            $objQuery  = $this->_em->createQuery();
            $strSelect = "SELECT COUNT(CaracteristicaPunto) CANTIDAD_COORDENADA ";
            $strFrom   = "FROM schemaBundle:InfoPuntoCaracteristica CaracteristicaPunto ";
            $strWhere  = "WHERE CaracteristicaPunto.caracteristicaId = :intIdCaracteristica
                          AND CaracteristicaPunto.puntoId            = :intIdPunto";

            $objQuery->setParameter('intIdCaracteristica',  $arrayParametros['intIdCaracteristica']);
            $objQuery->setParameter('intIdPunto',  $arrayParametros['intIdPunto']);

            $strSql = $strSelect.$strFrom.$strWhere;
            $objQuery->setDQL($strSql);

            $objInfoPuntoCaracteristica = $objQuery->getSingleScalarResult();
        }
        catch(\Exception $e)
        {
            error_log("Problemas al recuperar la información de la función InfoPuntoRepository:obtenerCantidadCoordenadaSugerida ".$e->getMessage());
        }
        return $objInfoPuntoCaracteristica;
    }


    /**
     * Función que devuelve un registro de InfoPuntoCaracteristica que coincida con los parametros de busqueda,
     * en donde se compara el campo 'Valor' de tipo CLOB.
     *
     * @author Daniel Guzmán <wgaibor@telconet.ec>
     * @version 1.0 16-02-2023
     * @param type $arrayParametros[
     *                              valor:                  integer:    id del punto guardado como valor,
     *                              intIdCaracteristica:    integer:    id caracteristica de actualizar coordenada,
     *                             ]
     */
    public function obtenerCaracteristicaPorValor($arrayParametros)
    {
        try 
        {
            $objQuery = $this->_em->createQuery();

            $strSql = "SELECT ipc
                    FROM schemaBundle:InfoPuntoCaracteristica ipc
                    WHERE ipc.valor LIKE :valor AND ipc.caracteristicaId = :intIdCaracteristica";

            $objQuery->setParameter("valor", '%'.$arrayParametros['valor'].'%');
            $objQuery->setParameter("intIdCaracteristica", $arrayParametros['intIdCaracteristica']);

            $objQuery->setDQL($strSql);
            
            $arrayResultados = $objQuery->getResult();

            return !empty($arrayResultados) ? $arrayResultados[0] : null;
          
        }
        catch(\Exception $e)
        {
            error_log("Problemas al recuperar la informacion ".$e->getMessage());
        }
    }
}
