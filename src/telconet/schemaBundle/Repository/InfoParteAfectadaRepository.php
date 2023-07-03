<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoParteAfectadaRepository extends EntityRepository
{
	public function getInfoParteAfectadaExistente($detalleId){
	
	        $query = $this->_em->createQuery("SELECT a
		FROM 
                schemaBundle:InfoParteAfectada a
		WHERE 
                a.detalleId = $detalleId               
                 ");
               
		$datos = $query->getResult();
		return $datos;
	
	}

    /**
     * getDetalleInicialCasoXTipoAfectado
     *
     * Método que obtiene el id_servicio de un id_caso.
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 22-05-2017
     *
     * costoQuery: 17
     * @param array $arrayParametros [
     *                                  intIdCaso       : id_caso,
     *                                  strTipoAfectado : tipo afectado (Cliente, Servicio, Elemento, Proveedor)
     *                               ]
     *
     * @return $arrayDatos
     */
    public function getDetalleInicialCasoXTipoAfectado($arrayParametros)
    {
        $arrayDatos = array();
        try
        {
            $query = $this->_em->createQuery();

            $sql   = " SELECT MIN( infoParteAfectada.afectadoId ) as detalleInicial
                        FROM  schemaBundle:InfoDetalleHipotesis infoDetalleHipotesis,
                              schemaBundle:InfoDetalle          infoDetalle,
                              schemaBundle:InfoParteAfectada    infoParteAfectada
                        WHERE infoDetalleHipotesis.id      = infoDetalle.detalleHipotesisId
                        AND infoDetalle.id                 = infoParteAfectada.detalleId
                        AND infoParteAfectada.tipoAfectado = :strTipoAfectado
                        AND infoDetalleHipotesis.casoId    = :intIdCaso ";

            $query->setParameter('strTipoAfectado',$arrayParametros['strTipoAfectado']);
            $query->setParameter('intIdCaso', $arrayParametros['intIdCaso']);

            $query->setDQL($sql);

            $arrayDatos = $query->getScalarResult();
        }
        catch(\Exception $ex)
        {
            error_log("Problemas al recuperar la información de la función InfoParteAfectadaRepository:getDetalleInicialCasoXTipoAfectado".
                        $ex->getMessage());
        }
        return $arrayDatos;
    }


     /**
     * Costo: 4
     * getAfectadosPorCaso
     *
     * Funcion que retorna los afectados de un caso por tipo
     *
     * @param array $arrayParametros[ 'intDetalleId'       => id detalle del caso
     *                                'arrayTipoAfectados' => tipo Cliente y Elemento ]
     *
     * @return obj $objInfoParteAfectada
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 26-09-2017
     */
    public function getAfectadosPorCaso($arrayParametros)
    {
        $objQuery             = $this->_em->createQuery();
        $objInfoParteAfectada = null;

        $strSql = " SELECT ipa
                        FROM schemaBundle:InfoParteAfectada ipa
                        WHERE ipa.detalleId = :paramDetalleId
                        AND ipa.tipoAfectado IN ( :paramTipoAfectados ) ";

        $objQuery->setParameter('paramDetalleId',$arrayParametros["intDetalleId"]);
        $objQuery->setParameter('paramTipoAfectados',$arrayParametros["arrayTipoAfectados"]);

        $objQuery->setDQL($strSql);

        $objInfoParteAfectada = $objQuery->getResult();

        return $objInfoParteAfectada;
    }

    /**
     * Actualización: Se agrega en el query IN para corregir error que obtiene mas de un afectado en el caso
     * @author Andrés Montero H.<mailto:amontero@telconet.ec>
     * @version 1.1 07-05-2021
     * 
     * Método que retorna información del punto
     * 
     * @author Walther Joao Gaibor C.<mailto:wgaibor@telconet.ec>
     * @version 1.0
     * @since 16-08-2018
     * 
     * @param array $arrayParametros[
     *                                  'intIdCaso'     integer:    Id del caso
     *                              ]
     * @return array arrayResultado
     */
    public function getInfoClienteCaso($arrayParametros)
    {
        $arrayResultado = array();
        try
        {
            $objQuery       = $this->_em->createQuery();

            $strSql         = " SELECT IPER.id idPersona,
                                       IPUN.login login,
                                       ACAN.id idCanton,
                                       ACAN.region regional
                                    FROM schemaBundle:InfoPersona IPER,
                                      schemaBundle:InfoPersonaEmpresaRol IPEM,
                                      schemaBundle:InfoPunto IPUN,
                                      schemaBundle:AdmiJurisdiccion AJUR,
                                      schemaBundle:InfoOficinaGrupo IOGR,
                                      schemaBundle:AdmiCanton ACAN
                                    WHERE IPER.id               = IPEM.personaId
                                    AND IPEM.id                 = IPUN.personaEmpresaRolId
                                    AND IPUN.puntoCoberturaId   = AJUR.id
                                    AND AJUR.oficinaId          = IOGR.id
                                    AND IOGR.cantonId           = ACAN.id
                                    AND IPUN.id                 IN
                                      (SELECT IPAF.afectadoId
                                      FROM schemaBundle:InfoDetalleHipotesis IDHI,
                                        schemaBundle:InfoDetalle IDET,
                                        schemaBundle:InfoParteAfectada IPAF
                                      WHERE IDHI.id                   = IDET.detalleHipotesisId
                                      AND IDET.id                     = IPAF.detalleId
                                      AND IPAF.criterioAfectadoId     = 1
                                      AND IDHI.casoId                 = :intIdCaso
                                      ) ";

            $objQuery->setParameter('intIdCaso', $arrayParametros['intIdCaso']);

            $objQuery->setDQL($strSql);
            $arrayResultado = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            error_log("Problemas al recuperar la información de la función InfoParteAfectadaRepository:getInfoClienteCaso()".
                        $ex->getMessage());
        }
        return $arrayResultado;
    }
    
    /**
     * Costo: 4
     * getAfectadoConcentrador
     *
     * Funcion que retorna servicio concentrador
     *
     * @param array $arrayParametros[ 'paramServicioId'       => id de servicio afectado ]
     *
     * @return array $arrayResultado
     *
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.0 29-12-2020
     */
    public function getAfectadoConcentrador($arrayParametros)
    {
        $objQuery             = $this->_em->createQuery();
        
        try
        {
            $strSql = " SELECT apro.descripcionProducto
                            FROM schemaBundle:InfoServicio iserv,
                                 schemaBundle:AdmiProducto apro
                            WHERE iserv.productoId = apro.id
                            AND   iserv.id = :paramServicioId 
                            AND   upper(apro.descripcionProducto) like upper('%CONCENTRADOR%')";

            $objQuery->setParameter('paramServicioId',$arrayParametros["paramServicioId"]);

            $objQuery->setDQL($strSql);


            $arrayResultado = $objQuery->getResult();
        }
        catch(\Exception $ex)
        {
            error_log("Problemas al recuperar la información de la función InfoParteAfectadaRepository:getInfoClienteCaso()".
                        $ex->getMessage());
        }

        return $arrayResultado;
    }
}

