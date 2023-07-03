<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;


class InfoDebitoGeneralRepository extends EntityRepository
{
    /**
     * Documentación para funcion 'findDebitosGeneralPorCriterios'.
     * busca los debitos generados
     * @version 1.1
     * @author amontero@hotmail.com
     * @param parametros (
     *     estado       => (estado del debito),
     *     fechaDesde   => (fecha inicio), 
     *     fechaHasta   => (fecha fin), 
     *     limit        => (limite de registros en consulta),
     *     page         => (pagina donde se encuentra la consulta),
     *     start        => (registro inicio),
     *     idEmpresa    => (id de la empresa donde se genera debito))
     * @return array $resultado debitos encontrados
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 - Se agrega que se envíe el campo impuestoId
     * @since 16-06-2016
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.3 22-11-2017 - Se modifican los parámetros por un array.
     *                           Se realiza el filtro por cicloId.
     */    
    public function findDebitosGeneralPorCriterios($arrayParametros)
    {        
        $objRsm                = new ResultSetMappingBuilder($this->_em);
        $strCriterioEstado     = '';
        $strCriterioFechaDesde = '';
        $strCriterioFechaHasta = '';
        $strCriterioCicloId    = '';
        $strEstado             = $arrayParametros["strEstado"];
        $strFechaDesde         = $arrayParametros["strFechaDesde"];
        $strFechaHasta         = $arrayParametros["strFechaHasta"];
        $intLimit              = $arrayParametros["intLimit"];
        $intStart              = $arrayParametros["intStart"];
        $strIdEmpresa          = $arrayParametros["strIdEmpresa"];
        $intCicloId            = $arrayParametros["intCicloId"];
        $strSql="SELECT 
            dgen.ip_creacion, dgen.ID_DEBITO_GENERAL, dgen.OFICINA_ID, dgen.ESTADO, dgen.FE_CREACION, 
            dgen.USR_CREACION, dgen.EJECUTANDO,dgen.ARCHIVO, dgen.GRUPO_DEBITO_ID, gdcab.NOMBRE_GRUPO, 
            REPLACE(REPLACE(REPLACE(NVL(ofi.nombre_oficina,'TODAS'),'MEGADATOS','' ),'TELCONET',''),'-','') as NOMBRE_OFICINA,
            dgen.impuesto_id, CICLO.NOMBRE_CICLO, dgen.PLANIFICADO
        FROM 
           Info_Debito_General dgen JOIN Info_Debito_Cab dcab ON dgen.id_debito_general = dcab.debito_General_Id
           LEFT JOIN ADMI_GRUPO_ARCHIVO_DEBITO_CAB gdcab ON dgen.grupo_debito_id = gdcab.id_grupo_debito 
           LEFT JOIN INFO_OFICINA_GRUPO ofi ON ofi.id_oficina=dgen.oficina_id 
           LEFT JOIN ADMI_CICLO CICLO ON CICLO.ID_CICLO = DGEN.CICLO_ID
        WHERE 
             ";
        try
        {
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);
            $objQuery->setParameter('empresaId', $strIdEmpresa);

            if($strFechaDesde)
            {
                $strFechaD             = date("Y/m/d", strtotime($strFechaDesde));
                $strFechaDesde         = $strFechaD;
                $strCriterioFechaDesde ="dgen.fe_Creacion >= :fechaDesde AND ";
                $objQuery->setParameter('fechaDesde',$strFechaDesde);
            }
            if($strFechaHasta)
            {
                $strFechaH             = date("Y/m/d", strtotime($strFechaHasta));
                $strFechaHasta         = $strFechaH;
                $strCriterioFechaHasta ="dgen.fe_Creacion <= :fechaHasta AND ";
                $objQuery->setParameter('fechaHasta', $strFechaHasta);
            }                
            if($strEstado)
            {       
                $strCriterioEstado = "dgen.estado = :estado AND ";
                $objQuery->setParameter('estado', $strEstado);
            } 	
            if($intCicloId)
            {
                $strCriterioCicloId = "dgen.CICLO_ID = :cicloId AND ";
                $objQuery->setParameter('cicloId', $intCicloId);
            }
            $strSql .= $strCriterioEstado . $strCriterioFechaDesde . $strCriterioFechaHasta . $strCriterioCicloId . " dcab.empresa_Id=:empresaId "
                    . "GROUP BY "
                    . " dgen.ip_creacion, dgen.ID_DEBITO_GENERAL, dgen.OFICINA_ID, dgen.ESTADO, dgen.FE_CREACION, dgen.USR_CREACION, dgen.EJECUTANDO,"
                    . " dgen.ARCHIVO, dgen.GRUPO_DEBITO_ID, dgen.PLANIFICADO, gdcab.NOMBRE_GRUPO, NOMBRE_OFICINA, dgen.impuesto_id,"
                    . " CICLO.NOMBRE_CICLO "
                    . "ORDER BY dgen.fe_Creacion DESC";

            $objRsm->addScalarResult('IP_CREACION', 'ipCreacion','integer');
            $objRsm->addScalarResult('ID_DEBITO_GENERAL', 'id', 'string');
            $objRsm->addScalarResult('OFICINA_ID', 'oficinaId','string');
            $objRsm->addScalarResult('ESTADO', 'estado','string');
            $objRsm->addScalarResult('FE_CREACION', 'feCreacion','string');
            $objRsm->addScalarResult('USR_CREACION', 'usrCreacion','string');
            $objRsm->addScalarResult('EJECUTANDO', 'ejecutando','string');
            $objRsm->addScalarResult('PLANIFICADO', 'planificado','string');
            $objRsm->addScalarResult('ARCHIVO', 'archivo','string');
            $objRsm->addScalarResult('GRUPO_DEBITO_ID', 'grupoDebitoId','integer');
            $objRsm->addScalarResult('NOMBRE_GRUPO', 'nombreGrupo','integer');
            $objRsm->addScalarResult('NOMBRE_OFICINA', 'nombreOficina','integer');
            $objRsm->addScalarResult('IMPUESTO_ID', 'impuestoId','integer');
            $objRsm->addScalarResult('NOMBRE_CICLO', 'nombreCiclo','string');
            $objQuery->setSQL($strSql);
            $arrayResultado['total'] = count($objQuery->getScalarResult());
            $objQuery->setParameter('start', $intStart + 1);
            $objQuery->setParameter('limit', ($intStart + $intLimit));

            $strSql = "SELECT a.*, rownum as intDoctrineRowNum FROM (".$strSql.") a WHERE ROWNUM <= :limit";
            if($intStart > 0)
            {
                $strSql="SELECT * FROM (".$strSql.") WHERE intDoctrineRowNum >= :start";
            }
            $objQuery->setSQL($strSql);
            $arrayDatos                  = $objQuery->getScalarResult();
            $arrayResultado['registros'] = $arrayDatos;
        }
        catch (\Exception $ex)
        {
            error_log($ex->getMessage());
        }
        return $arrayResultado;
} 
        
    /**
     * Documentación para funcion 'findDebitosGeneralParaRespuestas'.
     * busca los debitos generados para respuestas
     * @version 1.1
     * @author amontero@hotmail.com
     * @param parametros (
     *     estado       => (estado del debito),
     *     idEmpresa    => (id de la empresa donde se genera debito))
     * @return array $resultado debitos encontrados
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 - Se agrega que se envíe el campo impuestoId
     * @since 16-06-2016
     */   
	public function findDebitosGeneralParaRespuestas($estado,$idEmpresa)
    {
            $rsm             = new ResultSetMappingBuilder($this->_em);        
            $criterio_estado = '';
            $sql="SELECT 
                                  dgen.ip_creacion, dgen.ID_DEBITO_GENERAL, dgen.OFICINA_ID, dgen.ESTADO, dgen.FE_CREACION, 
                                  dgen.USR_CREACION, dgen.EJECUTANDO,dgen.ARCHIVO, dgen.GRUPO_DEBITO_ID, gdcab.NOMBRE_GRUPO,
                                  REPLACE(REPLACE(REPLACE(NVL(ofi.nombre_oficina,'TODAS'),'MEGADATOS','' ),'TELCONET',''),'-','') as NOMBRE_OFICINA,
                      dgen.impuesto_id "
                                ."FROM "
                                . "   INFO_DEBITO_GENERAL dgen "
                                . "   LEFT JOIN INFO_OFICINA_GRUPO ofi ON dgen.oficina_id=ofi.id_oficina "
                                . "   LEFT JOIN ADMI_GRUPO_ARCHIVO_DEBITO_CAB gdcab ON dgen.grupo_debito_id = gdcab.id_grupo_debito "
                . "   JOIN INFO_DEBITO_CAB dcab ON dgen.id_debito_general = dcab.debito_General_Id WHERE ";
            $query = $this->_em->createNativeQuery(null,$rsm);
            $query->setParameter('empresaId',$idEmpresa); 
            if ($estado)
            {       
                $criterio_estado="dgen.estado = :estado AND ";
                $query->setParameter('estado',$estado);                 
            }                
            $sql=$sql.$criterio_estado." dcab.empresa_id=:empresaId "
                                                    ."GROUP BY "
            ."    dgen.ip_creacion, dgen.ID_DEBITO_GENERAL, dgen.OFICINA_ID, dgen.ESTADO, dgen.FE_CREACION, "
            ."    dgen.USR_CREACION, dgen.EJECUTANDO,dgen.ARCHIVO, dgen.GRUPO_DEBITO_ID, gdcab.NOMBRE_GRUPO, "
                . "NOMBRE_OFICINA, dgen.impuesto_id ORDER BY dgen.fe_creacion DESC";  
            $rsm->addScalarResult('IP_CREACION', 'ipCreacion','integer');
            $rsm->addScalarResult('ID_DEBITO_GENERAL', 'id', 'string');
            $rsm->addScalarResult('OFICINA_ID', 'oficinaId','string');
            $rsm->addScalarResult('ESTADO', 'estado','string');
            $rsm->addScalarResult('FE_CREACION', 'feCreacion','string');
            $rsm->addScalarResult('USR_CREACION', 'usrCreacion','string');
            $rsm->addScalarResult('EJECUTANDO', 'ejecutando','string');
            $rsm->addScalarResult('ARCHIVO', 'archivo','string');
            $rsm->addScalarResult('GRUPO_DEBITO_ID', 'grupoDebitoId','integer');  
            $rsm->addScalarResult('NOMBRE_GRUPO', 'nombreGrupo','string');
            $rsm->addScalarResult('NOMBRE_OFICINA', 'nombreOficina','string'); 
            $rsm->addScalarResult('IMPUESTO_ID', 'impuestoId','integer');           
            $query->setSQL($sql);  
            $datos = $query->getResult();
            return $datos;
	}         
    
    /**
    * Documentación para funcion 'findDebitosGeneralProcesandose'.
    * consulta si los debitos se estan procesando o no por empresa.
    * @param ejecutando : indica si el debito esta procesando o no
    * @param estado     : estado del debito
    * @param idEmpresa  : indica la empresa a la que pertenece el debito
    * @author <amontero@telconet.ec>
    * @since 06/01/2015
    * @return objeto para crear excel
    */     
	public function findDebitosGeneralProcesandoseHoy($ejecutando,$estado,$idEmpresa)
    {
               
            $query = $this->_em->createQuery(
            "SELECT a
            FROM 
                schemaBundle:InfoDebitoGeneral a, 
                schemaBundle:InfoOficinaGrupo ofi
            WHERE 
                a.oficinaId=ofi.id AND
                ofi.empresaId=:idEmpresa AND
                a.ejecutando=:ejecutando AND
                a.feCreacion >= :fechaHoy AND
                a.estado=:estado
            ORDER BY 
                a.feCreacion DESC");
            $query->setParameter('idEmpresa' , $idEmpresa );
            $query->setParameter('ejecutando', $ejecutando);
            $query->setParameter('fechaHoy', date('Y-m-d'));
            $query->setParameter('estado', $estado);
            $datos = $query->getResult();
            return $datos;
    } 
}
