<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use \telconet\schemaBundle\Entity\ReturnResponse;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\schemaBundle\DependencyInjection\BaseRepository;

class AdmiGrupoPromocionRepository extends BaseRepository
{    
    /**
    * guardarProcesoMasivo.
    *
    * Método que genera un Proceso Masivo que puede ser por Inactivación y/o Dado de Baja o Clonación de promociones, en base a parámetros enviados.
    * El método incluirá en el PMA todas las promociones que hayan sido previamente escogidas o  marcadas en el proceso,
    * guardando el motivo y la observación del proceso sea esta por Inactivación yo Dada de baja, Clonación.
    * 
    * @param array $arrayParametros[]                  
    *              'strIdsGrupoPromocion'    => Ids de los grupos de Promociones ADMI_GRUPO_PROMOCION 
    *              'intIdMotivo'             => Motivo del Proceso del PMA
    *              'strObservacion'          => Observación del Proceso del PMA                 
    *              'strUsrCreacion'          => Usuario en sesión
    *              'strCodEmpresa'           => Codigo de empresa en sesión
    *              'strIpCreacion'           => Ip de creación
    *              'strTipoPma'              => Tipo de Proceso Masivo :InactivarPromo y/o DarBajaPromo ,  ClonarPromo, 
    *
    * @return strResultado  Resultado de la ejecución.
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 04-04-2019
    */
    public function guardarProcesoMasivo($arrayParametros)
    {
        $strResultado = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN  DB_COMERCIAL.CMKG_GRUPO_PROMOCIONES.P_CREA_PM_PROMOCIONES
                             ( :Pv_IdsGrupoPromocion, :Pn_IdMotivo, :Pv_Observacion, :Pv_UsrCreacion,
                               :Pv_CodEmpresa, :Pv_IpCreacion, :Pv_TipoPma, :Pv_MsjResultado);
                           END;";

                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");
                $objStmt->bindParam('Pv_IdsGrupoPromocion', $arrayParametros['strIdsGrupoPromocion']);
                $objStmt->bindParam('Pn_IdMotivo', $arrayParametros['intIdMotivo']);
                $objStmt->bindParam('Pv_Observacion', $arrayParametros['strObservacion']);
                $objStmt->bindParam('Pv_UsrCreacion', $arrayParametros['strUsrCreacion']);
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strCodEmpresa']);
                $objStmt->bindParam('Pv_IpCreacion', $arrayParametros['strIpCreacion']);                
                $objStmt->bindParam('Pv_TipoPma', $arrayParametros['strTipoPma']);                
                $objStmt->bindParam('Pv_MsjResultado', $strResultado);

                $objStmt->execute();
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para generar el Proceso Masivo '.$arrayParametros['strTipoPma'];
            }
        }
        catch (\Exception $e)
        {
            $strResultado= 'Ocurrió un error al guardar el Proceso Masivo '.$arrayParametros['strTipoPma'];
            throw($e);
        }
        return $strResultado; 
    }
     /**
     * getMotivos, obtiene la información de las motivos para Inactivar o clonar promociones
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 05-04-2019
     * @param array $arrayParametros[]                  
     *              'arrayEstadoMotivos'  => Estado para los motivos 
     *              'strNombreModulo'     => Nombre del Módulo
     *                    
     * @return Response lista de Motivos
     */
    public function getMotivos($arrayParametros)
    {
        $objRsm        = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery   = $this->_em->createNativeQuery(null, $objRsm);   
        $strQuery      = " SELECT AM.ID_MOTIVO, AM.NOMBRE_MOTIVO "; 
        $strFromQuery  = " FROM DB_SEGURIDAD.SIST_MODULO MODU,
                           DB_SEGURIDAD.SIST_ACCION ACCI, 
                           DB_SEGURIDAD.SEGU_RELACION_SISTEMA RS,
                           DB_GENERAL.ADMI_MOTIVO AM
                           WHERE MODU.ID_MODULO          = RS.MODULO_ID
                           AND ACCI.ID_ACCION            = RS.ACCION_ID
                           AND RS.ID_RELACION_SISTEMA    = AM.RELACION_SISTEMA_ID
                           AND AM.ESTADO                 IN (:arrayEstadoMotivos)
                           AND MODU.NOMBRE_MODULO        =:strNombreModulo";                    
        $strOrderByQuery = " ORDER BY AM.NOMBRE_MOTIVO ASC";                    

        $objRsm->addScalarResult('ID_MOTIVO', 'id', 'integer');
        $objRsm->addScalarResult('NOMBRE_MOTIVO', 'nombre', 'string');
                  
        $objNtvQuery->setParameter('arrayEstadoMotivos'  , $arrayParametros['arrayEstadoMotivos']);
        $objNtvQuery->setParameter('strNombreModulo' ,$arrayParametros['strNombreModulo']);        

        return $objNtvQuery->setSQL($strQuery . $strFromQuery . $strOrderByQuery)->getArrayResult();                 
    }
          
     /**
     * getTiposNegocio, obtiene los tipos de Negocio por empresa en estado activo
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 19-03-2019
     *      
     * @param array $arrayParametros[]                  
     *              'strEstado'          => Estado del tipo de Negocio    
     *              'strEmpresaCod'      => Empresa en sesión
     *                    
     * @return Response lista de Tipos de Negocio
     */
    public function getTiposNegocio($arrayParametros)
    {
        $objRsm          = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery     = $this->_em->createNativeQuery(null, $objRsm);                             
        $strQuery        = " SELECT TP.ID_TIPO_NEGOCIO,TP.NOMBRE_TIPO_NEGOCIO
                             FROM DB_COMERCIAL.ADMI_TIPO_NEGOCIO TP
                             WHERE TP.ESTADO    =:strEstado
                             AND TP.EMPRESA_COD =:strEmpresaCod ";                           
        $strOrderByQuery = " ORDER BY TP.NOMBRE_TIPO_NEGOCIO ASC ";                    

        $objRsm->addScalarResult('ID_TIPO_NEGOCIO', 'id', 'integer');
        $objRsm->addScalarResult('NOMBRE_TIPO_NEGOCIO', 'nombre', 'string');
                  
        $objNtvQuery->setParameter('strEstado'  , $arrayParametros['strEstado']);
        $objNtvQuery->setParameter('strEmpresaCod' ,$arrayParametros['strEmpresaCod']);        

        return $objNtvQuery->setSQL($strQuery . $strOrderByQuery)->getArrayResult();                 
    }  
     /**
     * getGruposPromocionesPma, obtiene lista de los Grupos promocionales que se encuentran en un proceso masivo pendiente.
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 18-07-2022
     *      
     * @param array $arrayParametros[]
     *              'strTipoProceso'              => Tipo de proceso masivo     
     *              'strEstado'                   => Estado del proceso masivo
     *              'arrayIdsGrupoPromocion'      => Array de Ids de grupo Promoción
     *                    
     * @return Response lista los Grupos promocionales que se encuentran en un proceso masivo pendiente.
     */
    public function getGruposPromocionesPma($arrayParametros)
    {
        $objRsm          = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery     = $this->_em->createNativeQuery(null, $objRsm);                             
        $strQuery        = "  SELECT  DISTINCT
        gpro.NOMBRE_GRUPO  AS nombre_grupo
        FROM db_infraestructura.info_proceso_masivo_cab pmc,
        db_infraestructura.info_proceso_masivo_det    pmd,
        db_general.admi_motivo                        mo,
        db_comercial.admi_grupo_promocion             gpro
        WHERE pmc.id_proceso_masivo_cab = pmd.proceso_masivo_cab_id
        AND pmc.solicitud_id            = mo.id_motivo
        AND pmc.tipo_proceso            = :strTipoProceso
        AND pmc.estado                  = :strEstado
        AND pmd.estado                  = :strEstado
        AND pmd.solicitud_id            = gpro.id_grupo_promocion      
        AND gpro.id_grupo_promocion     IN (:arrayIdsGrupoPromocion) ";                    
        
        $objRsm->addScalarResult('NOMBRE_GRUPO', 'nombre_grupo', 'string');
                  
        $objNtvQuery->setParameter('strTipoProceso'         , $arrayParametros['strTipoProceso']);
        $objNtvQuery->setParameter('strEstado'              , $arrayParametros['strEstado']);
        $objNtvQuery->setParameter('arrayIdsGrupoPromocion' , $arrayParametros['arrayIdsGrupoPromocion']);    

        return $objNtvQuery->setSQL($strQuery)->getArrayResult();                 
    }  
     /**
     * validaGruposPromocionActivas, Verifica si existen Grupos de promociones en estado <> Activo
     * 
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 10-04-2019
     *      
     * @param array $arrayParametros[]                  
     *              'arrayIdsGrupoPromocion' => Array de Ids de grupo Promoción
     *              'strEstado'              => Estado       
     *                    
     * @return $intCantidad
     */
    public function validaGruposPromocionActivas($arrayParametros)
    {
        $intCantidad   = 0;
        $objRsm        = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery   = $this->_em->createNativeQuery(null, $objRsm);                             
        $strQuery      = " SELECT COUNT(*) AS CANTIDAD
                           FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION GP
                           WHERE GP.ESTADO    <>:strEstado
                           AND GP.ID_GRUPO_PROMOCION IN (:arrayIdsGrupoPromocion) ";                                           

        $objRsm->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        $objNtvQuery->setParameter('strEstado'              , $arrayParametros['strEstado']);
        $objNtvQuery->setParameter('arrayIdsGrupoPromocion' ,$arrayParametros['arrayIdsGrupoPromocion']);        

        $intCantidad = $objNtvQuery->setSQL($strQuery)->getSingleScalarResult();                 
        return $intCantidad; 
    }  
     /** 
     * Función que obtiene las reglas de un Tipo de Promoción en base a parametros recibidos.       
     *
     * @param $arrayParametros [ 'intTipoPromocionId'  => Id del Tipo de Promoción
     *                           'intCaracteristicaId' => Id de la característica o regla 
     *                           'arrayEstados'        => array de estados del Tipo de promoción a excluir
     *                           'intNumeroSecuencia'  => Id de la secuencia que relaciona la sectorización]
     * 
     * @return object ADMI_TIPO_PROMOCION_REGLA
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 16-04-2019 
     */
    public function getTipoPromocionReglaEstado($arrayParametros)
    {
        $objQuery                    = $this->_em->createQuery();
        $objAdmiTipoPromocionRegla   = null;        
        try
        {
            $strSelect = " select tpr ";
            $strFrom   = " from schemaBundle:AdmiTipoPromocionRegla tpr, schemaBundle:AdmiTipoPromocion pr, schemaBundle:AdmiCaracteristica re ";
            $strWhere  = " where tpr.tipoPromocionId   = pr.id
                           and  tpr.caracteristicaId   = re.id
                           and  pr.id                  =:intTipoPromocionId
                           and  re.id                  =:intCaracteristicaId ";
            if( isset($arrayParametros['arrayEstados']) && !empty($arrayParametros['arrayEstados']) )
            {
                $strWhere .="  and  tpr.estado not in (:arrayEstados)";
                $objQuery->setParameter("arrayEstados",  $arrayParametros['arrayEstados']);
            }
            
            if( isset($arrayParametros['intNumeroSecuencia']) && !empty($arrayParametros['intNumeroSecuencia']) )
            {
                $strWhere .=" and  tpr.secuencia =:intNumeroSecuencia";
                $objQuery->setParameter("intNumeroSecuencia",  $arrayParametros['intNumeroSecuencia']);
            }
            $objQuery->setParameter("intTipoPromocionId",  $arrayParametros['intTipoPromocionId']);
            $objQuery->setParameter("intCaracteristicaId",  $arrayParametros['intCaracteristicaId']);            
            
            $strSql = $strSelect . $strFrom . $strWhere;
            $objQuery->setDQL($strSql);
            $objQuery->setMaxResults(1);
            $objAdmiTipoPromocionRegla  = $objQuery->getOneOrNullResult();               
        } 
        catch (\Exception $e) 
        {
            throw($e);
        }
        return $objAdmiTipoPromocionRegla;
    }
    /**
     * Función que obtiene la secuencia SEQ_ADMI_REGLA_SECUENCIA, para asociar la Sectorización:
     * JURISDICCION, CANTÓN, PARROQUIA, SECTOR, OLT, a nivel de admi_grupo_promocion_regla y admi_tipo_promocion_regla para el módulo
     * de promociones.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-04-2019
     * 
     * return $arrayRespuesta
     */
    public function creaSecuencia()
    {             
        $objRsm         = new ResultSetMappingBuilder($this->_em);	      
        $objQuery       = $this->_em->createNativeQuery(null, $objRsm);        
        $arrayRespuesta = array();        
        try
        {        
            $strSelect = "SELECT DB_COMERCIAL.SEQ_ADMI_REGLA_SECUENCIA.NEXTVAL SECUENCIA FROM DUAL";

            $objRsm->addScalarResult('SECUENCIA', 'secuencia','integer');
            $objQuery->setSQL($strSelect);
            $arrayRespuesta = $objQuery->getOneOrNullResult();               
        }
        catch(\Exception $e)
        {
            throw($e);
        }
        return $arrayRespuesta;
    }
    
    /**
     * getTipoPromocionReglaSectorizacion Función Obtiene las reglas de Sectorización por Tipo de Promoción
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 26-04-2019
     * 
     * @param $arrayParametros [ 'arraySectorizacion'  => Contiene el array de reglas para la sectorización 
     *                                                    PROM_JURISDICCION, PROM_CANTON, PROM_PARROQUIA, PROM_SECTOR, PROM_ELEMENTO,
     *                                                    PROM_EDIFICIO
     *                           'intIdTipoPromocion'  => Id del Tipo de Promoción     
     *                          ]
     * return $objAdmiTipoPromocionRegla
     */
    public function getTipoPromocionReglaSectorizacion($arrayParametros)
    {
        $objQuery                    = $this->_em->createQuery();
        $objAdmiTipoPromocionRegla   = null;
        $strSql                      = "";
        try
        {
            $strSelect = " SELECT ATPR ";
            $strFrom   = " FROM schemaBundle:AdmiTipoPromocionRegla ATPR, schemaBundle:AdmiTipoPromocion ATP, schemaBundle:AdmiCaracteristica AC ";
            $strWhere  = " where ATPR.tipoPromocionId        = ATP.id
                           and  ATPR.caracteristicaId        = AC.id
                           and  ATP.id                       =:intIdTipoPromocion
                           and  AC.descripcionCaracteristica IN (:arraySectorizacion)";
            
            $objQuery->setParameter("intIdTipoPromocion", $arrayParametros['intIdTipoPromocion']);
            $objQuery->setParameter("arraySectorizacion",  $arrayParametros['arraySectorizacion']);
            
            $strSql = $strSelect . $strFrom . $strWhere;
            $objQuery->setDQL($strSql);
            $objAdmiTipoPromocionRegla  = $objQuery->getResult();               
        } 
        catch (\Exception $e) 
        {
            throw($e);
        }
        return $objAdmiTipoPromocionRegla;

    }
    
    /**
     * getPromocionesPorCriterios()
     * Obtiene listado de Promociones, mediante filtros por: fechaVigencia, Estado, Nombre y Empresa
     *
     * costoQuery: 3
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 02-02-2019
     *
     * costoQuery: 10
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 04-10-2019 - Se agrega la unión a las tablas 'ADMI_TIPO_PROMOCION,ADMI_PARAMETRO_CAB y
     *                           ADMI_PARAMETRO_DET' para obtener el tipo de promoción.
     *
     * costoQuery: 10
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.2 01-03-2023 - Se agrega filtro de empresa para la tabla DB_GENERAL.ADMI_PARAMETRO_DET.
     * 
     * @param  array $arrayParametros [
     *                                  "strFechaPromo"  => Fecha de vigencia,
     *                                  "strEstadoPromo" => Estado de la Promoción,
     *                                  "strNombrePromo" => Nombre de la Promoción,
     *                                  "intIdEmpresa"   => Id de la Empresa
     *                                ]
     *
     * @return $objResultado - Listado de Promociones
     */
    public function getPromocionesPorCriterios($arrayParametros)
    {
        $strFechaVigencia = $arrayParametros['strFechaPromo'] ? $arrayParametros['strFechaPromo'] : '';
        $strEstadoPromo   = $arrayParametros['strEstadoPromo'] ? $arrayParametros['strEstadoPromo'] : '';
        $strNombrePromo   = $arrayParametros['strNombrePromo'] ? strtoupper($arrayParametros['strNombrePromo']) : "";
        $intIdEmpresa     = $arrayParametros['intIdEmpresa'] ? $arrayParametros['intIdEmpresa'] : "";
        try
        {
            $objRsmCount    = new ResultSetMappingBuilder($this->_em);
            $objQueryCount  = $this->_em->createNativeQuery(null, $objRsmCount);
            $strSelectCount = " SELECT COUNT(*) AS TOTAL ";
            $objRsmCount->addScalarResult('TOTAL', 'total', 'integer');
            $objRsm   = new ResultSetMappingBuilder($this->_em);
            $objQuery = $this->_em->createNativeQuery(null, $objRsm);

            $objRsm->addScalarResult('ID_GRUPO_PROMOCION' , 'intIdGrupoPromocion'  , 'integer');
            $objRsm->addScalarResult('NOMBRE_GRUPO'       , 'strNombreGrupo'       , 'string');
            $objRsm->addScalarResult('ESTADO'             , 'strEstado'            , 'string');
            $objRsm->addScalarResult('FE_CREACION'        , 'dateFeCreacion'       , 'datetime');
            $objRsm->addScalarResult('FE_INICIO_VIGENCIA' , 'dateFeInicioVigencia' , 'datetime');
            $objRsm->addScalarResult('FE_FIN_VIGENCIA'    , 'dateFeFinVigencia'    , 'datetime');
            $objRsm->addScalarResult('USR_CREACION'       , 'strUsrCreacion'       , 'string');
            $objRsm->addScalarResult('TIPO_PROMOCION'     , 'strTipoPromocion'     , 'string');

            $strSelect  = "SELECT AGP.ID_GRUPO_PROMOCION, AGP.NOMBRE_GRUPO, AGP.ESTADO, AGP.FE_CREACION,
                                  AGP.USR_CREACION, AGP.FE_INICIO_VIGENCIA, AGP.FE_FIN_VIGENCIA, APD.VALOR6 AS TIPO_PROMOCION";
            $strFrom    = " FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION AGP".
                              ", DB_COMERCIAL.ADMI_TIPO_PROMOCION  ATP".
                              ", DB_GENERAL.ADMI_PARAMETRO_CAB     APC".
                              ", DB_GENERAL.ADMI_PARAMETRO_DET     APD";
            $strWhere   = " WHERE AGP.EMPRESA_COD           = :intIdEmpresa".
                            " AND AGP.ID_GRUPO_PROMOCION    = ATP.GRUPO_PROMOCION_ID".
                            " AND APC.ID_PARAMETRO          = APD.PARAMETRO_ID".
                            " AND ATP.CODIGO_TIPO_PROMOCION = APD.VALOR2".
                            " AND APC.NOMBRE_PARAMETRO      = :strNombreParametro".
                            " AND APD.EMPRESA_COD           = :intIdEmpresa";
            $strGroupBy = " GROUP BY AGP.ID_GRUPO_PROMOCION, AGP.NOMBRE_GRUPO, AGP.ESTADO, AGP.FE_CREACION,
                                     AGP.USR_CREACION, AGP.FE_INICIO_VIGENCIA, AGP.FE_FIN_VIGENCIA, APD.VALOR6";
            //Query Promociones
            $objQuery->setParameter('intIdEmpresa'       ,  $intIdEmpresa);
            $objQuery->setParameter('strNombreParametro' , 'PROM_TIPO_PROMOCIONES');
            //Query Count
            $objQueryCount->setParameter('intIdEmpresa'       ,  $intIdEmpresa);
            $objQueryCount->setParameter('strNombreParametro' , 'PROM_TIPO_PROMOCIONES');

            if($strEstadoPromo === "")
            {
                $strWhere .=" AND AGP.ESTADO not in ('Eliminado')";
            }
            else
            {
                $strWhere .=" AND UPPER(AGP.ESTADO) = :strEstado";
                $objQuery->setParameter('strEstado', strtoupper($strEstadoPromo));
                $objQueryCount->setParameter('strEstado', strtoupper($strEstadoPromo));
            }
            if($strNombrePromo !== "")
            {
                $strWhere .=" AND UPPER(AGP.NOMBRE_GRUPO) like :strNombre";
                $objQuery->setParameter('strNombre', "%" . $strNombrePromo . "%");
                $objQueryCount->setParameter('strNombre', "%" . $strNombrePromo . "%");
            }
            if($strFechaVigencia!=="")
            {
                $strFechaVigenciaFormat = strtotime($strFechaVigencia);
                $strWhere .=" AND AGP.FE_INICIO_VIGENCIA = :fecha";
                $objQuery->setParameter('fecha', date("Y/m/d", $strFechaVigenciaFormat));
                $objQueryCount->setParameter('fecha', date("Y/m/d", $strFechaVigenciaFormat));
            }

            $strSqlSub   = $strSelect     .$strFrom.$strWhere.$strGroupBy;
            $strSqlCount = $strSelectCount.$strFrom.$strWhere.$strGroupBy;

            $strSqlTotal = "SELECT COUNT(CANTIDAD.TOTAL) AS TOTAL FROM ($strSqlCount) CANTIDAD";
            $arrayTotalPromociones = $objQueryCount->setSQL($strSqlTotal)->getSingleScalarResult();

            $strSql .= "SELECT PROMOCIONES.* FROM ($strSqlSub) PROMOCIONES ORDER BY PROMOCIONES.FE_CREACION DESC";
            $arrayPromociones = $objQuery->setSQL($strSql)->getArrayResult();

            $objResultado['total']     = $arrayTotalPromociones;
            $objResultado['registros'] = $arrayPromociones;
        }
        catch(\Exception $e)
        {
            $objResultado = array('total' => 0, 'registros' => array());
        }
        return $objResultado;
        
    }//getPromocionesPorCriterios

    /**
     * getOltsEdificios()
     * Obtiene listado de Olt's O Edificios, mediante filtros por: Tipo de Elemento, Parroquia, Empresa, Estado .
     *
     * costoQuery: 6631
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 04-03-2019
     * 
     * @param Array $arrayParametros[
     *                                "intIdEmpresa"    => Id de la Empresa.
     *                                "intIdParroquia"  => Id de la Parroquia.
     *                                "arrayEstados"    => Arreglo de Estados.
     *                                "strTipoElemento" => Tipo de Elemento.]
     * 
     * @return $objQuery Lista de Olt's.
     */
    public function getOltsEdificios($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_ELEMENTO', 'id', 'integer');
        $objResultSet->addScalarResult('NOMBRE_ELEMENTO', 'nombre', 'string');

        $objQuery  = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect = "SELECT DISTINCT ELEM.ID_ELEMENTO,ELEM.NOMBRE_ELEMENTO ";
        $strFrom   = "FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO ELEM,
                        DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO MOELEM, DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO TIPO_ELEM,
                        DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EMPELEUBI, DB_INFRAESTRUCTURA.INFO_UBICACION UBICA";
        $strWhere  = " WHERE ELEM.MODELO_ELEMENTO_ID = MOELEM.ID_MODELO_ELEMENTO
                        AND TIPO_ELEM.ID_TIPO_ELEMENTO = MOELEM.TIPO_ELEMENTO_ID 
                        AND ELEM.ESTADO in (:arrayEstados) 
                        AND TIPO_ELEM.NOMBRE_TIPO_ELEMENTO = :strTipoElemento
                        AND EMPELEUBI.ELEMENTO_ID = ELEM.ID_ELEMENTO
                        AND EMPELEUBI.UBICACION_ID = UBICA.ID_UBICACION
                        AND EMPELEUBI.EMPRESA_COD = :intIdEmpresa AND UBICA.PARROQUIA_ID = :intIdParroquia";
        $objQuery->setParameter('strTipoElemento', $arrayParametros['strTipoElemento']);
        $objQuery->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $objQuery->setParameter('intIdParroquia', $arrayParametros['intIdParroquia']);
        $objQuery->setParameter('arrayEstados', $arrayParametros['arrayEstados']);
        $strSql = $strSelect . $strFrom . $strWhere;

        return $objQuery->setSQL($strSql)->getArrayResult();
    }

    /**
     * getOltEdificioById()
     * Obtiene Olt o Edificio, mediante filtros por: Tipo de Elemento, idElemento, Empresa.
     *
     * costoQuery: 11
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.1 14-10-2019 - Se elimina la relación con la tabla 'INFO_RELACION_ELEMENTO' para poder
     *                           visualizar los OLT en la interfaz WEB.
     *
     * @param Array $arrayParametros[
     *                                "intIdEmpresa"    => Id de la Empresa.
     *                                "intIdElemento"   => Id del Elemento.
     *                                "strTipoElemento" => Tipo de Elemento.]
     * 
     * @return $objQuery Lista de Olt's.
     */
    public function getOltEdificioById($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objQuery     = $this->_em->createNativeQuery(null, $objResultSet);

        $strSelect = "SELECT DISTINCT ELEM.ID_ELEMENTO,ELEM.NOMBRE_ELEMENTO ";
        $strFrom   = "FROM DB_INFRAESTRUCTURA.INFO_ELEMENTO               ELEM,
                           DB_INFRAESTRUCTURA.ADMI_MODELO_ELEMENTO        MOELEM,
                           DB_INFRAESTRUCTURA.ADMI_TIPO_ELEMENTO          TIPO_ELEM,
                           DB_INFRAESTRUCTURA.INFO_EMPRESA_ELEMENTO_UBICA EMPELEUBI,
                           DB_INFRAESTRUCTURA.INFO_UBICACION              UBICA";
        $strWhere  = " WHERE ELEM.MODELO_ELEMENTO_ID        = MOELEM.ID_MODELO_ELEMENTO
                         AND TIPO_ELEM.ID_TIPO_ELEMENTO     = MOELEM.TIPO_ELEMENTO_ID
                         AND TIPO_ELEM.NOMBRE_TIPO_ELEMENTO = :strTipoElemento
                         AND EMPELEUBI.ELEMENTO_ID          = ELEM.ID_ELEMENTO
                         AND EMPELEUBI.UBICACION_ID         = UBICA.ID_UBICACION
                         AND ELEM.ID_ELEMENTO               = :intIdElemento
                         AND EMPELEUBI.EMPRESA_COD          = :intIdEmpresa";

        $objQuery->setParameter('strTipoElemento' , $arrayParametros['strTipoElemento']);
        $objQuery->setParameter('intIdElemento'   , $arrayParametros['intIdElemento']);
        $objQuery->setParameter('intIdEmpresa'    , $arrayParametros['intIdEmpresa']);

        $objResultSet->addScalarResult('ID_ELEMENTO'     , 'id'     , 'integer');
        $objResultSet->addScalarResult('NOMBRE_ELEMENTO' , 'nombre' , 'string');
        $strSql = $strSelect . $strFrom . $strWhere;

        return $objQuery->setSQL($strSql)->getArrayResult();
    }

    /**
     * getEmisores()
     * Obtiene Emisores, mediante filtros por: ID_BANCO_TIPO_CUENTA.
     *
     * costoQuery: 7
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * 
     * @param Array $arrayParametros["arrayBancoTipoCuenta"  => Arreglo de Id's bancoTipoCuenta]
     * 
     * @return $objQuery - Lista de Emisores.
     */
    public function getEmisores($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('DESCRIPCION_CUENTA', 'descripcionCuenta', 'string');
        $objResultSet->addScalarResult('NOMBRE_BANCO', 'nombreBanco', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSql   = " SELECT TCTA.DESCRIPCION_CUENTA, NULL NOMBRE_BANCO
                      FROM DB_GENERAL.ADMI_TIPO_CUENTA TCTA
                      WHERE TCTA.ES_TARJETA = 'S'
                        AND EXISTS (SELECT NULL
                                    FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA BTC, DB_GENERAL.ADMI_BANCO BCO
                                    WHERE BCO.ID_BANCO=BTC.BANCO_ID
                                      AND TCTA.ID_TIPO_CUENTA=BTC.TIPO_CUENTA_ID
                                      AND BTC.ESTADO!='Inactivo'
                                      AND BTC.ID_BANCO_TIPO_CUENTA in (:arrayBancoTipoCuenta)) 
                      UNION 
                      SELECT TCTA.DESCRIPCION_CUENTA, BCO.DESCRIPCION_BANCO
                      FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA BTC, 
                        DB_GENERAL.ADMI_BANCO BCO, DB_GENERAL.ADMI_TIPO_CUENTA TCTA
                      WHERE BCO.ID_BANCO=BTC.BANCO_ID
                        AND TCTA.ID_TIPO_CUENTA=BTC.TIPO_CUENTA_ID
                        AND BTC.ESTADO!='Inactivo'
                        AND BTC.ID_BANCO_TIPO_CUENTA in (:arrayBancoTipoCuenta) 
                        AND TCTA.ES_TARJETA = 'N' ";
        $objQuery->setParameter('arrayBancoTipoCuenta', $arrayParametros['arrayBancoTipoCuenta']);
        return $objQuery->setSQL($strSql)->getArrayResult();
    }
    
    /**
     * getFormasPagos()
     * Obtiene Formas de Pago, mediante filtros por: ID_FORMA_PAGO.
     *
     * costoQuery: 2
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * 
     * @param Array $arrayParametros["arrayFormaPago"  => Arreglo de Id's formaPago]
     * 
     * @return $objQuery - Lista de Formas de Pago.
     */
    public function getFormasPagos($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('DESCRIPCION_FORMA_PAGO', 'descripcionFormaPago', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSql   = " SELECT FP.DESCRIPCION_FORMA_PAGO
                      FROM DB_GENERAL.ADMI_FORMA_PAGO FP
                      WHERE FP.ID_FORMA_PAGO in (:arrayFormaPago) ";

        $objQuery->setParameter('arrayFormaPago', $arrayParametros['arrayFormaPago']);
        return $objQuery->setSQL($strSql)->getArrayResult();
    }

   /**
     * getTipoNegocios()
     * Obtiene Tipos de Negocios, mediante filtros por: ID_TIPO_NEGOCIO.
     *
     * costoQuery: 2
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * 
     * @param Array $arrayParametros["arrayTipoNegocio"  => Arreglo de Id's tipoNegocio]
     * 
     * @return $objQuery - Lista de Tipos de Negocios.
     */
    public function getTipoNegocios($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('NOMBRE_TIPO_NEGOCIO', 'nombreTipoNegocio', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSql   = " SELECT ATN.NOMBRE_TIPO_NEGOCIO
                        FROM DB_COMERCIAL.ADMI_TIPO_NEGOCIO ATN
                      WHERE ATN.ID_TIPO_NEGOCIO in (:arrayTipoNegocio) ";

        $objQuery->setParameter('arrayTipoNegocio', $arrayParametros['arrayTipoNegocio']);
        return $objQuery->setSQL($strSql)->getArrayResult();
    }
   /**
     * getUltimasMillas()
     * Obtiene Ultimas millas, mediante filtros por: ID_TIPO_MEDIO.
     *
     * costoQuery: 2
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * 
     * @param Array $arrayParametros["arrayUltimaMilla"  => Arreglo de Id's tipoMedio]
     * 
     * @return $objQuery - Lista de Últimas Millas.
     */
    public function getUltimasMillas($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('NOMBRE_TIPO_MEDIO', 'nombreTipoMedio', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSql   = " SELECT ATM.NOMBRE_TIPO_MEDIO
                        FROM DB_INFRAESTRUCTURA.ADMI_TIPO_MEDIO ATM
                      WHERE ATM.ID_TIPO_MEDIO in (:arrayUltimaMilla) ";

        $objQuery->setParameter('arrayUltimaMilla', $arrayParametros['arrayUltimaMilla']);
        return $objQuery->setSQL($strSql)->getArrayResult();
    }

    /**
     * getProductos()
     * Obtiene Listado de Productos, mediante filtros por: Nombre Técnico, esConcentrador, idEmpresa, Nombre Producto, visibleComercial, Estado.
     *
     * costoQuery: 26
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * 
     * @param Array $arrayParametros[ "intIdEmpresa"  => Id Empresa,
     *                                "strNombre      => Nombre de Producto,
     *                                "strEstado"     => Estado de Producto]
     * 
     * @return $objQuery - Lista de Productos
     */
    public function getProductos($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_PRODUCTO', 'id', 'integer');
        $objResultSet->addScalarResult('DESCRIPCION_PRODUCTO', 'nombre', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect = "SELECT PROD.ID_PRODUCTO, PROD.DESCRIPCION_PRODUCTO ";
        $strFrom   = "FROM DB_COMERCIAL.ADMI_PRODUCTO PROD ";
        $strWhere  = "WHERE PROD.ESTADO= :strEstado
                        AND PROD.NOMBRE_TECNICO <> :strNombreTecnico
                        AND PROD.ES_CONCENTRADOR <> :strEsConcentrador
                        AND PROD.EMPRESA_COD = :intIdEmpresa
                        AND PROD.ID_PRODUCTO NOT IN (SELECT AP.ID_PRODUCTO
                                                       FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC
                                                       JOIN DB_COMERCIAL.ADMI_PRODUCTO AP on AP.ID_PRODUCTO = APC.PRODUCTO_ID
                                                       JOIN DB_COMERCIAL.ADMI_CARACTERISTICA AC ON AC.ID_CARACTERISTICA = APC.CARACTERISTICA_ID
                                                     WHERE AC.DESCRIPCION_CARACTERISTICA = :strVisibleComercial 
                                                       AND AC.ESTADO = :strEstado)
                      ORDER BY PROD.DESCRIPCION_PRODUCTO ASC";

        $objQuery->setParameter('strNombreTecnico', 'FINANCIERO');
        $objQuery->setParameter('strEsConcentrador', 'SI');
        $objQuery->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $objQuery->setParameter('strVisibleComercial', 'NO_VISIBLE_COMERCIAL');
        $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);
        $strSql = $strSelect . $strFrom . $strWhere;

        return $objQuery->setSQL($strSql)->getArrayResult();
    }

    /**
     * getPlanes()
     * Obtiene Listado de Planes, mediante filtros por: idEmpresa, Nombre Plan, descripcionSoloCambioPlan, Estado.
     *
     * costoQuery: 23
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * 
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.1 14-07-2022 - Se modifica el query que consulta los planes 
     *                           para crear promociones de franja horaria y mensualidad, para franja horaria
     *                           el query se modifico para considerar los planes en estado Activo e Inactivo.
     * 
     * @param Array $arrayParametros[ "intIdEmpresa"  => Id Empresa,
     *                                "strNombre      => Nombre de Plan,
     *                                "strEstado"     => Estado de Plan]
     * 
     * @return $objQuery - Lista de planes.
     */
    public function getPlanes($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_PLAN', 'id', 'integer');
        $objResultSet->addScalarResult('NOMBRE_PLAN', 'nombre', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect = "SELECT IPC.ID_PLAN, IPC.NOMBRE_PLAN ";
        $strFrom   = "FROM DB_COMERCIAL.INFO_PLAN_CAB IPC ";
        $strWhere  = "WHERE ipc.EMPRESA_COD = :intIdEmpresa ";
        
        if (isset($arrayParametros['strFiltraEstProm']) && !empty($arrayParametros['strFiltraEstProm'])
            && $arrayParametros['strFiltraEstProm'] === "SI")
        {
          $strWhere  .= "AND ipc.estado in (SELECT VALOR1 FROM DB_GENERAL.ADMI_PARAMETRO_DET
                                    WHERE ESTADO = :strEstado  
                                    AND PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                                                     WHERE NOMBRE_PARAMETRO = :strNombreParametro AND ESTADO = :strEstado)
                                    AND DESCRIPCION = :strDescripcionEstPermin) ";
          $objQuery->setParameter('strDescripcionEstPermin', 'ESTADOS PERMITIDO PROMO FRANJA HORARIA');
          $objQuery->setParameter('strNombreParametro', 'PARAMETROS_PROMOCIONES_MASIVAS_BW');
        }
        else
        {
          $strWhere  .= "AND ipc.estado = :strEstado ";
        }
        $strWhere  .=  "AND (NOT EXISTS ( SELECT 1 FROM DB_COMERCIAL.INFO_PLAN_CARACTERISTICA PCARACT,
                                        DB_COMERCIAL.ADMI_CARACTERISTICA CARACT WHERE PCARACT.PLAN_ID = ipc.ID_PLAN 
                                        AND CARACT.ID_CARACTERISTICA = PCARACT.CARACTERISTICA_ID
                                        AND CARACT.DESCRIPCION_CARACTERISTICA = :strDescripcionSoloCambioPlan
                                        AND PCARACT.estado = :strEstado ))
                        AND nvl(ipc.tipo,'N') = nvl(:strIdTipoNegocio,nvl(ipc.tipo,'N')) ORDER BY ipc.NOMBRE_PLAN";                               
        $objQuery->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $objQuery->setParameter('strDescripcionSoloCambioPlan', 'PERMITIDO_SOLO_MIGRACION');
        $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);
        $objQuery->setParameter('strIdTipoNegocio', $arrayParametros['strIdTipoNegocio']);
        $strSql = $strSelect . $strFrom . $strWhere;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }

    /**
     * getPlanesAunNoSeleccionados()
     * Obtiene Listado de Planes, que aun no han sido seleccionados .
     *
     * costoQuery: 23
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 26-11-2021
     * 
     * @param Array $arrayParametros["intIdEmpresa"      => Id Empresa,
     *                               "strEstado"         => Estado de Plan,
     *                               "strIdTipoPromocion => Codigo del tipo de empresa"]
     * 
     * @return $objQuery - Lista de planes.
     */
    public function getPlanesNoSeleccionados($arrayParametros)
    {
        $strTipoPromocion   = $arrayParametros['strTipoPromocion'];
        $intIdEmpresa       = $arrayParametros['intIdEmpresa'];
        $strProducto        = $arrayParametros['strProducto'];
        $strCaracteristica  = $arrayParametros['strCaracteristica'];
        $arrayEstadosPromo  = !empty($arrayParametros['strEstadosPromo']) ? explode(",",$arrayParametros['strEstadosPromo']) : "";
        $arrayEstadosPlanes = !empty($arrayParametroDet["valor1"]) ? explode(",",$arrayParametroDet["valor1"]) : "";
        $strEstado          = $arrayParametros['strEstado'];

        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_PLAN', 'id', 'integer');
        $objResultSet->addScalarResult('NOMBRE_PLAN', 'nombre', 'string');
        $objResultSet->addScalarResult('VALOR', 'valor', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect = "SELECT IPC.ID_PLAN, IPC.NOMBRE_PLAN, IPPC.VALOR ";
        $strFrom   = "FROM DB_COMERCIAL.INFO_PLAN_CAB IPC, DB_COMERCIAL.INFO_PLAN_DET IPD,
                      DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT IPPC ";
        $strWhere  = "WHERE IPC.ID_PLAN = IPD.PLAN_ID
                    AND IPD.ID_ITEM = IPPC.PLAN_DET_ID
                    AND IPC.EMPRESA_COD = :intIdEmpresa
                    AND IPPC.PRODUCTO_CARACTERISITICA_ID = (
                        SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                        WHERE PRODUCTO_ID = (
                            SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
                            WHERE DESCRIPCION_PRODUCTO = :strProducto
                            AND EMPRESA_COD = :intIdEmpresa
                            AND ESTADO = :strEstado)
                        AND CARACTERISTICA_ID = (
                            SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
                            WHERE DESCRIPCION_CARACTERISTICA = :strCaracteristica))";
        $strOrden = " ORDER BY IPC.NOMBRE_PLAN";
        $objQuery->setParameter('strTipoPromocion',   $strTipoPromocion);
        $objQuery->setParameter('intIdEmpresa',       $intIdEmpresa);
        $objQuery->setParameter('strProducto',        $strProducto);
        $objQuery->setParameter('strCaracteristica',  $strCaracteristica);
        $objQuery->setParameter('strEstado',          $strEstado);
        $objQuery->setParameter('arrayEstadosPromo',  $arrayEstadosPromo);

        if (!empty($arrayEstadosPlanes))
        {
            $strWhere = $strWhere . "AND IPC.ESTADO IN (:arrayEstadosPlanes)
                                     AND IPD.ESTADO IN (:arrayEstadosPlanes)";
             $objQuery->setParameter('arrayEstadosPlanes', $arrayEstadosPlanes);
        }
        $strSql = $strSelect . $strFrom . $strWhere . $strOrden;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }
       
     /**
     * getPromocionInstalacion()
     * Obtiene el detalle de la Promoción de Instalación, mediante filtros por: idPromocion.
     *
     * costoQuery: 7
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * 
     * @param Array $arrayParametros["intIdPromocion" => Id Promocion]
     * 
     * @return $arrayRespuesta - Detalle de la Promoción .
     */
    public function getPromocionInstalacion($arrayParametros)
    {
        $objResultSet1 = new ResultSetMappingBuilder($this->_em);
        $objResultSet1->addScalarResult('ID_TIPO_PROMOCION', 'idTipoPromo', 'string');
        $objResultSet1->addScalarResult('CODIGO_TIPO_PROMOCION', 'codTipoPromo', 'string');
        $objResultSet1->addScalarResult('TIPO', 'tipoPromo', 'string');
        $objResultSet1->addScalarResult('DESCRIPCION_CARACTERISTICA', 'descCaracteristica', 'string');
        $objResultSet1->addScalarResult('VALOR', 'valorCaracteristica', 'string');

        $objQuery1 = $this->_em->createNativeQuery(null, $objResultSet1);
        $strSelect1 = "SELECT ATP.ID_TIPO_PROMOCION,ATP.CODIGO_TIPO_PROMOCION, ATP.TIPO, AC.DESCRIPCION_CARACTERISTICA, ATPR.VALOR ";
        $strFrom1 = "FROM DB_COMERCIAL.ADMI_TIPO_PROMOCION ATP,
                        DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA ATPR,
                        DB_COMERCIAL.ADMI_CARACTERISTICA AC";
        $strWhere1 = " WHERE ATP.GRUPO_PROMOCION_ID = :intIdGrupoPromocion1
                        AND ATP.ID_TIPO_PROMOCION = ATPR.TIPO_PROMOCION_ID
                        AND AC.ID_CARACTERISTICA = ATPR.CARACTERISTICA_ID
                        AND ATPR.ESTADO NOT IN (:arrayEstados)";

        $objQuery1->setParameter('intIdGrupoPromocion1', $arrayParametros['intIdPromocion']);
        $objQuery1->setParameter('arrayEstados', array('Eliminado'));
        $strSql1 = $strSelect1 . $strFrom1 . $strWhere1;
        $objTipoPromocion = $objQuery1->setSQL($strSql1)->getArrayResult();

        $objResultSetSectorizacion = new ResultSetMappingBuilder($this->_em);
        $objResultSetSectorizacion->addScalarResult('ID_SECTORIZACION', 'idSectorizacion', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_JURISDICCION', 'idJurisdiccion', 'string');
        $objResultSetSectorizacion->addScalarResult('DESCRIPCION_JURISDICCION', 'descJurisdiccion', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_CANTON', 'idCanton', 'string');
        $objResultSetSectorizacion->addScalarResult('NOMBRE_CANTON', 'descCanton', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_PARROQUIA', 'idParroquia', 'string');
        $objResultSetSectorizacion->addScalarResult('NOMBRE_PARROQUIA', 'descParroquia', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_SECTOR', 'idSector', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_ELEMENTO', 'idElemento', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_EDIFICIO', 'idEdificio', 'string');

        $objQuerySectorizacion = $this->_em->createNativeQuery(null, $objResultSetSectorizacion);
        $strSqlSectorizacion = " SELECT NVL(SECTORIZACION.ID_SECTORIZACION,'0')ID_SECTORIZACION,
                                        SECTORIZACION.ID_JURISDICCION,
                                        (SELECT AJ.NOMBRE_JURISDICCION FROM ADMI_JURISDICCION AJ
                                           WHERE AJ.ID_JURISDICCION = SECTORIZACION.ID_JURISDICCION
                                        ) DESCRIPCION_JURISDICCION,
                                        NVL(SECTORIZACION.ID_CANTON,'0')ID_CANTON,
                                        (SELECT AC.NOMBRE_CANTON FROM ADMI_CANTON AC
                                           WHERE AC.ID_CANTON = SECTORIZACION.ID_CANTON
                                        ) NOMBRE_CANTON,
                                        NVL(SECTORIZACION.ID_PARROQUIA,'0')ID_PARROQUIA,
                                        (SELECT AP.NOMBRE_PARROQUIA FROM ADMI_PARROQUIA AP
                                           WHERE AP.ID_PARROQUIA = SECTORIZACION.ID_PARROQUIA
                                        ) NOMBRE_PARROQUIA,
                                        NVL(SECTORIZACION.ID_SECTOR,'0')ID_SECTOR,
                                        NVL(SECTORIZACION.ID_ELEMENTO,'0')ID_ELEMENTO,
                                        NVL(SECTORIZACION.ID_EDIFICIO,'0')ID_EDIFICIO
                                 FROM ( SELECT *
                                          FROM ( SELECT AC.DESCRIPCION_CARACTERISTICA, ATPR.VALOR,
                                                        ATPR.SECUENCIA ID_SECTORIZACION
                                                 FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION        AGP,
                                                      DB_COMERCIAL.ADMI_TIPO_PROMOCION         ATP,
                                                      DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA   ATPR,
                                                      DB_COMERCIAL.ADMI_CARACTERISTICA         AC
                                                 WHERE AGP.ID_GRUPO_PROMOCION      = :intIdGrupoPromocion
                                                 AND AGP.ID_GRUPO_PROMOCION        = ATP.GRUPO_PROMOCION_ID 
                                                 AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                                                 AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                                                 AND AC.DESCRIPCION_CARACTERISTICA IN (:arraySectorizacion)
                                                 AND ATPR.SECUENCIA IS NOT NULL
                                                 AND ATPR.ESTADO not in (:arrayEstados)
                                                ) PIVOT ( MAX ( VALOR )
                                                  FOR DESCRIPCION_CARACTERISTICA
                                                    IN ( 'PROM_JURISDICCION' ID_JURISDICCION, 
                                                         'PROM_CANTON' ID_CANTON, 
                                                         'PROM_PARROQUIA' ID_PARROQUIA, 
                                                         'PROM_SECTOR' ID_SECTOR, 
                                                         'PROM_ELEMENTO' ID_ELEMENTO,
                                                         'PROM_EDIFICIO' ID_EDIFICIO))
                                      ) SECTORIZACION ";

        $objQuerySectorizacion->setParameter('intIdGrupoPromocion', $arrayParametros['intIdPromocion']);
        $objQuerySectorizacion->setParameter('arraySectorizacion', $arrayParametros['arraySectorizacion']);
        $objQuerySectorizacion->setParameter('arrayEstados', array('Eliminado'));        
        $objSectorizacion = $objQuerySectorizacion->setSQL($strSqlSectorizacion)->getArrayResult();
        
        $arrayRespuesta = array('objTipoPromocion' => $objTipoPromocion,
                                'objSectorizacion' => $objSectorizacion
                               );

        return $arrayRespuesta;
    }

    /**
     * getPromocionMensual()
     * Obtiene el detalle de la Promoción de Mensualidad, mediante filtros por: idPromocion.
     *
     * costoQuery: 14
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * 
     * @param Array $arrayParametros["intIdPromocion" => Id Promocion]
     * 
     * @return $arrayRespuesta - Detalle de la Promoción .
     */
    public function getPromocionMensual($arrayParametros)
    {
        $objResultSetCaract = new ResultSetMappingBuilder($this->_em);
        $objResultSetCaract->addScalarResult('VALOR', 'valor', 'string');
        $objResultSetCaract->addScalarResult('DESCRIPCION_CARACTERISTICA', 'caracteristica', 'string');

        $objQueryCaract  = $this->_em->createNativeQuery(null, $objResultSetCaract);
            $strSelectCaract = "SELECT AGPR.VALOR,AC.DESCRIPCION_CARACTERISTICA ";
        $strFromCaract   = "FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION AGP,
                              DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA AGPR,
                              DB_COMERCIAL.ADMI_CARACTERISTICA AC ";
        $strWhereCaract  = "WHERE AGP.ID_GRUPO_PROMOCION = :intIdGrupoPromocion
                              AND AGP.ID_GRUPO_PROMOCION = AGPR.GRUPO_PROMOCION_ID
                              AND AC.ID_CARACTERISTICA = AGPR.CARACTERISTICA_ID
                              AND AGPR.ESTADO not in (:arrayEstados)
                              AND AC.DESCRIPCION_CARACTERISTICA in (:arrayCaracteristicas)";

        $objQueryCaract->setParameter('intIdGrupoPromocion', $arrayParametros['intIdPromocion']);
        $objQueryCaract->setParameter('arrayEstados', $arrayParametros['arrayEstados']);
        $objQueryCaract->setParameter('arrayCaracteristicas', $arrayParametros['arrayCaracteristicas']);
        $strSqlCaract = $strSelectCaract . $strFromCaract . $strWhereCaract;
        $objCaractGenerales = $objQueryCaract->setSQL($strSqlCaract)->getArrayResult();
        
        
        $objResultSetSectorizacion = new ResultSetMappingBuilder($this->_em);
        $objResultSetSectorizacion->addScalarResult('ID_SECTORIZACION', 'idSectorizacion', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_JURISDICCION', 'idJurisdiccion', 'string');
        $objResultSetSectorizacion->addScalarResult('DESCRIPCION_JURISDICCION', 'descJurisdiccion', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_CANTON', 'idCanton', 'string');
        $objResultSetSectorizacion->addScalarResult('NOMBRE_CANTON', 'descCanton', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_PARROQUIA', 'idParroquia', 'string');
        $objResultSetSectorizacion->addScalarResult('NOMBRE_PARROQUIA', 'descParroquia', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_SECTOR', 'idSector', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_ELEMENTO', 'idElemento', 'string');
        $objResultSetSectorizacion->addScalarResult('ID_EDIFICIO', 'idEdificio', 'string');

        $objQuerySectorizacion = $this->_em->createNativeQuery(null, $objResultSetSectorizacion);
        $strSqlSectorizacion   = " SELECT NVL(SECTORIZACION.ID_SECTORIZACION,'0')ID_SECTORIZACION,
                                        SECTORIZACION.ID_JURISDICCION,
                                        (SELECT AJ.NOMBRE_JURISDICCION  FROM ADMI_JURISDICCION AJ
                                           WHERE AJ.ID_JURISDICCION = SECTORIZACION.ID_JURISDICCION
                                        ) DESCRIPCION_JURISDICCION,
                                        NVL(SECTORIZACION.ID_CANTON,'0')ID_CANTON,
                                        (SELECT AC.NOMBRE_CANTON  FROM ADMI_CANTON AC
                                           WHERE AC.ID_CANTON = SECTORIZACION.ID_CANTON
                                        ) NOMBRE_CANTON,
                                        NVL(SECTORIZACION.ID_PARROQUIA,'0')ID_PARROQUIA,
                                        (SELECT AP.NOMBRE_PARROQUIA  FROM ADMI_PARROQUIA AP
                                           WHERE AP.ID_PARROQUIA = SECTORIZACION.ID_PARROQUIA
                                        ) NOMBRE_PARROQUIA,
                                        NVL(SECTORIZACION.ID_SECTOR,'0')ID_SECTOR,
                                        NVL(SECTORIZACION.ID_ELEMENTO,'0')ID_ELEMENTO,
                                        NVL(SECTORIZACION.ID_EDIFICIO,'0')ID_EDIFICIO
                                 FROM ( SELECT *
                                          FROM ( SELECT AC.DESCRIPCION_CARACTERISTICA, AGPR.VALOR,
                                                        AGPR.SECUENCIA ID_SECTORIZACION
                                                 FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION        AGP,
                                                      DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA  AGPR,
                                                      DB_COMERCIAL.ADMI_CARACTERISTICA         AC
                                                 WHERE AGP.ID_GRUPO_PROMOCION = :intIdGrupoPromocion
                                                 AND AGP.ID_GRUPO_PROMOCION = AGPR.GRUPO_PROMOCION_ID
                                                 AND AC.ID_CARACTERISTICA = AGPR.CARACTERISTICA_ID
                                                 AND AC.DESCRIPCION_CARACTERISTICA IN (:arraySectorizacion)
                                                 AND AGPR.SECUENCIA IS NOT NULL
                                                 AND AGPR.ESTADO not in (:arrayEstados)
                                                ) PIVOT ( MAX ( VALOR )
                                                  FOR DESCRIPCION_CARACTERISTICA
                                                    IN ( 'PROM_JURISDICCION' ID_JURISDICCION, 
                                                         'PROM_CANTON' ID_CANTON, 
                                                         'PROM_PARROQUIA' ID_PARROQUIA, 
                                                         'PROM_SECTOR' ID_SECTOR, 
                                                         'PROM_ELEMENTO' ID_ELEMENTO,
                                                         'PROM_EDIFICIO' ID_EDIFICIO))
                                      ) SECTORIZACION ";

        $objQuerySectorizacion->setParameter('intIdGrupoPromocion', $arrayParametros['intIdPromocion']);
        $objQuerySectorizacion->setParameter('arrayEstados', $arrayParametros['arrayEstados']);
        $objQuerySectorizacion->setParameter('arraySectorizacion', $arrayParametros['arraySectorizacion']);
        $objSectorizacion = $objQuerySectorizacion->setSQL($strSqlSectorizacion)->getArrayResult();

        $objResultSetTipoPromo = new ResultSetMappingBuilder($this->_em);
        $objResultSetTipoPromo->addScalarResult('ID_TIPO_PROMOCION', 'idTipoPromo', 'string');
        $objResultSetTipoPromo->addScalarResult('CODIGO_TIPO_PROMOCION', 'codTipoPromo', 'string');
        $objResultSetTipoPromo->addScalarResult('TIPO', 'tipoPromo', 'string');
        $objResultSetTipoPromo->addScalarResult('DESCRIPCION_CARACTERISTICA', 'descCaracteristica', 'string');
        $objResultSetTipoPromo->addScalarResult('VALOR', 'valorCaracteristica', 'string');

        $objQueryTipoPromo  = $this->_em->createNativeQuery(null, $objResultSetTipoPromo);
        $strSelectTipoPromo = "SELECT ATP.ID_TIPO_PROMOCION,ATP.CODIGO_TIPO_PROMOCION, ATP.TIPO, AC.DESCRIPCION_CARACTERISTICA, ATPR.VALOR ";
        $strFromTipoPromo   = "FROM DB_COMERCIAL.ADMI_TIPO_PROMOCION ATP,
                                 DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA ATPR,
                                 DB_COMERCIAL.ADMI_CARACTERISTICA AC ";
        $strWhereTipoPromo  = "WHERE ATP.GRUPO_PROMOCION_ID = :intIdGrupoPromocionTipo
                                 AND ATP.ID_TIPO_PROMOCION = ATPR.TIPO_PROMOCION_ID
                                 AND AC.ID_CARACTERISTICA = ATPR.CARACTERISTICA_ID
                                 AND ATPR.ESTADO <> :strEstado";

        $objQueryTipoPromo->setParameter('intIdGrupoPromocionTipo', $arrayParametros['intIdPromocion']);
        $objQueryTipoPromo->setParameter('strEstado', 'Eliminado');
        $strSqlTipoPromo  = $strSelectTipoPromo . $strFromTipoPromo . $strWhereTipoPromo;
        $objTipoPromocion = $objQueryTipoPromo->setSQL($strSqlTipoPromo)->getArrayResult();

        $arrayRespuesta = array('objCaractGenerales' => $objCaractGenerales,
                                'objSectorizacion'   => $objSectorizacion,
                                'objTipoPromocion'   => $objTipoPromocion);
        return $arrayRespuesta;
    }
    
    /**
     * obtenerPermanenciaMinima()
     * Obtiene el listado de Permanencias Mínimas, mediante filtros por: idEmpresa, nombreParametro, estado.
     *
     * costoQuery: 3
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 05-03-2019
     * 
     * @param Array $arrayParametros[ "intIdEmpresa" => Id de Empresa, "strEstado"    => Estado del parámetro]
     * 
     * @return $objQuery - Listado de Permanencias Mínimas .
     */
    public function obtenerPermanenciaMinima($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('PARAMETRO_ID', 'id', 'integer');
        $objResultSet->addScalarResult('VALOR1', 'nombre', 'string');

        $objQuery  = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect = "SELECT APD.PARAMETRO_ID, APD.VALOR1 ";
        $strFrom   = "FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC "
                   . "JOIN ADMI_PARAMETRO_DET APD ON APC.ID_PARAMETRO = APD.PARAMETRO_ID ";
        $strWhere  = "WHERE APC.NOMBRE_PARAMETRO = :strNombreParametro  AND APD.EMPRESA_COD= :intIdEmpresa
                        AND APC.ESTADO= :strEstado  ORDER BY TO_NUMBER(APD.VALOR1)";

        $objQuery->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $objQuery->setParameter('strNombreParametro', 'PROD_PROM_PERMANENCIA_MINIMA');
        $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);
        $strSql = $strSelect . $strFrom . $strWhere;

        return $objQuery->setSQL($strSql)->getArrayResult();
    }
    
    
    
    /**
     * obtenerPermanenciaMinPromoCancelVol()
     * Obtiene el listado de Permanencias Mínimas, utilizadas para la cancelacion voluntaria.
     *
     * costoQuery: 3
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 28-07-2022
     * 
     * @param Array $arrayParametros[ "intIdEmpresa" => Id de Empresa,  "strEstado"    => Estado del parámetro]
     * 
     * @return $objQuery - Listado de Permanencias Mínimas .
     */
    public function obtenerPermanenciaMinPromoCancelVol($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('PARAMETRO_ID', 'id', 'integer');
        $objResultSet->addScalarResult('VALOR1', 'nombre', 'string');

        $objQuery  = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect = "SELECT APD.PARAMETRO_ID, APD.VALOR1 ";
        $strFrom   = "FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC "
                   . "JOIN ADMI_PARAMETRO_DET APD ON APC.ID_PARAMETRO = APD.PARAMETRO_ID ";
        $strWhere  = "WHERE APC.NOMBRE_PARAMETRO = :strNombreParametroCab 
                        AND APD.DESCRIPCION = :strNombreParametroDet 
                        AND APD.EMPRESA_COD= :intIdEmpresa
                        AND APC.ESTADO= :strEstado  ORDER BY TO_NUMBER(APD.VALOR1)";

        $objQuery->setParameter('intIdEmpresa', $arrayParametros['intIdEmpresa']);
        $objQuery->setParameter('strNombreParametroCab', 'PROD_PROM_PERMANENCIA_MINIMA_CV');
        $objQuery->setParameter('strNombreParametroDet', 'PROD_PROM_PERMANENCIA_MINIMA_CV');
        $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);
        $strSql = $strSelect . $strFrom . $strWhere;

        return $objQuery->setSQL($strSql)->getArrayResult();
    }
    
   /**
    * getGrupoPromocionReglaEstado, obtiene la regla del Grupo Promoción.
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 16-04-2019
    *
    * @param array $arrayParametros [ 'arrayEstados'         => Arreglo de estados.
    *                                 'intNumeroSecuencia'   => Número de secuencia para las reglas de sectorización.
    *                                 'intIdGrupoPromocion'  => Id del Grupo Promoción.]  
    *
    * costoQuery: 3
    * @return Objeto Grupo Promoción Regla.
    */
    public function getGrupoPromocionReglaEstado($arrayParametros)
    {
        $objQuery                    = $this->_em->createQuery();
        $objAdmiGrupoPromocionRegla  = null;
        $strSql                      = "";
        try
        {
            $strSelect = "SELECT AGPR ";
            $strFrom   = "FROM schemaBundle:AdmiGrupoPromocionRegla AGPR, 
                           schemaBundle:AdmiGrupoPromocion AGP,
                           schemaBundle:AdmiCaracteristica AC ";
            $strWhere  = "where AGPR.grupoPromocionId = AGP.id
                            and  AGPR.caracteristicaId  = AC.id
                            and  AGP.id                 =:intIdGrupoPromocion
                            and  AC.id                  =:intCaracteristicaId";
            
            if( isset($arrayParametros['arrayEstados']) && !empty($arrayParametros['arrayEstados']) )
            {
                $strWhere .=" and  AGPR.estado not in (:arrayEstados)";
                $objQuery->setParameter("arrayEstados",  $arrayParametros['arrayEstados']);
            }
            if( isset($arrayParametros['intNumeroSecuencia']) && !empty($arrayParametros['intNumeroSecuencia']) )
            {
                $strWhere .=" and  AGPR.secuencia =:intNumeroSecuencia";
                $objQuery->setParameter("intNumeroSecuencia",  $arrayParametros['intNumeroSecuencia']);
            }
            
            $objQuery->setParameter("intIdGrupoPromocion",  $arrayParametros['intIdGrupoPromocion']);
            $objQuery->setParameter("intCaracteristicaId",  $arrayParametros['intCaracteristicaId']);
            $strSql = $strSelect . $strFrom . $strWhere;
            
            $objQuery->setDQL($strSql);
            $objQuery->setMaxResults(1);
            $objAdmiGrupoPromocionRegla  = $objQuery->getOneOrNullResult();               
        } 
        catch (\Exception $e) 
        {
            throw($e);
        }
        return $objAdmiGrupoPromocionRegla;
    }
   
   /**
    * getGrupoPromocionReglaSectorizacion, obtiene las reglas de sectorización del Grupo Promoción.
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 15-04-2019
    *
    * @param array $arrayParametros [ 'arraySectorizacion'   => Arreglo con reglas de sectorización.
    *                                 'intIdGrupoPromocion'  => Id del Grupo Promoción.]
    * costoQuery: 5
    * @return Response lista de objetos Grupo Promoción Regla.
    */
    public function getGrupoPromocionReglaSectorizacion($arrayParametros)
    {
        $objQuery                    = $this->_em->createQuery();
        $objAdmiGrupoPromocionRegla  = null;
        $strSql                      = "";
        try
        {
            $strSelect = "SELECT AGPR ";
            $strFrom   = "FROM schemaBundle:AdmiGrupoPromocionRegla AGPR, 
                               schemaBundle:AdmiGrupoPromocion AGP,
                               schemaBundle:AdmiCaracteristica AC ";
            $strWhere  = "where AGPR.grupoPromocionId         = AGP.id
                            and  AGPR.caracteristicaId        = AC.id
                            and  AGP.id                       =:intIdGrupoPromocion
                            and  AC.descripcionCaracteristica IN (:arraySectorizacion)";
            
            $objQuery->setParameter("intIdGrupoPromocion", $arrayParametros['intIdGrupoPromocion']);
            $objQuery->setParameter("arraySectorizacion",  $arrayParametros['arraySectorizacion']);
            $strSql = $strSelect . $strFrom . $strWhere;
            
            $objQuery->setDQL($strSql);
            $objAdmiGrupoPromocionRegla  = $objQuery->getResult();               
        } 
        catch (\Exception $e) 
        {
            throw($e);
        }
        return $objAdmiGrupoPromocionRegla;
    }   
   /**
    * getTipoPromocionEstado, obtiene el objeto de un Tipo de Promoción.
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 30-04-2019
    *
    * @param array $arrayParametros [ 'intGrupoPromocionId' => Id del Grupo Promoción.
    *                                 'strTipoPromocion'    => Tipo de Promoción.
    *                                 'arrayEstados'        => Arreglo de Estados.]
    * costoQuery: 2
    * @return Response objeto Tipo Promoción.
    */
    public function getTipoPromocionEstado($arrayParametros)
    {
        $objQuery              = $this->_em->createQuery();
        $objAdmiTipoPromocion  = null;
        $strQuery              = "";
        try
        {
            $strQuery = "Select tp From schemaBundle:AdmiTipoPromocion tp
                         Where tp.grupoPromocionId  =:intGrupoPromocionId
                           and  tp.tipo             =:strTipoPromocion
                           and  tp.estado           not in (:arrayEstados)";
            
            $objQuery->setParameter("intGrupoPromocionId",  $arrayParametros['intGrupoPromocionId']);
            $objQuery->setParameter("strTipoPromocion",  $arrayParametros['strTipoPromocion']);
            $objQuery->setParameter("arrayEstados",  $arrayParametros['arrayEstados']);
            
            $objQuery->setDQL($strQuery);
            $objQuery->setMaxResults(1);
            $objAdmiTipoPromocion  = $objQuery->getOneOrNullResult();               
        } 
        catch (\Exception $e) 
        {
            throw($e);
        }
        return $objAdmiTipoPromocion;
    }
   /**
    * getTipoPlanProdPromoNotNull, obtiene Planes o Productos de la tabla AdmiTipoPlanProdPromocion.
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 18-04-2019
    *
    * @param array $arrayParametros [
    *                                 'intTipoPromocionId' => Id del Tipo Promoción.
    *                                 'strTipoPlanProd'    => Tipo(Plan o Producto).
    *                               ]
    * costoQuery: 2
    * @return Response lista de objetos TipoPlanProdPromocion(Planes o Productos).
    */
    public function getTipoPlanProdPromoNotNull($arrayParametros)
    {
        $objQuery = $this->_em->createQuery();
        $objAdmiTipoPlanProdPromo = null;
        $strSql = "";
        try
        {
            $strSelect = "Select atppp ";
            $strFrom   = "From schemaBundle:AdmiTipoPlanProdPromocion atppp ";
            $strWhere  = "Where atppp.tipoPromocionId =:intTipoPromocionId";
            if($arrayParametros['strTipoPlanProd'] === "PLAN")
            {
                $strWhere .=" and atppp.planId is not null";
            }
            if($arrayParametros['strTipoPlanProd'] === "PROD")
            {
                $strWhere .=" and atppp.productoId is not null";
            }

            $objQuery->setParameter("intTipoPromocionId", $arrayParametros['intTipoPromocionId']);
            $strSql = $strSelect . $strFrom . $strWhere;

            $objQuery->setDQL($strSql);
            $objAdmiTipoPlanProdPromo = $objQuery->getResult();
        }
        catch(\Exception $e) 
        {
            throw($e);
        }
        return $objAdmiTipoPlanProdPromo;
    }
   
   /**
    * getTipoPlanProdPromoNotEliminado, obtiene Planes o Productos de la tabla AdmiTipoPlanProdPromocion no eliminados.
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 18-04-2019
    *
    * @param array $arrayParametros [ 'intTipoPromocionId' => Id del Tipo Promoción.
    *                                 'arrayEstados'       => Arreglo de Estados.] 
    * costoQuery: 2
    * @return Response lista de objetos TipoPlanProdPromocion(Planes o Productos).
    */
    public function getTipoPlanProdPromoNotEliminado($arrayParametros)
    {
        $objQuery                 = $this->_em->createQuery();
        $objAdmiTipoPlanProdPromo = null;
        $strQuery                 = "";
        try
        {
            $strQuery = "Select atppp From schemaBundle:AdmiTipoPlanProdPromocion atppp
                         Where atppp.tipoPromocionId = :intTipoPromocionId
                           and  atppp.estado         not in (:arrayEstados)";
            $objQuery->setParameter("intTipoPromocionId", $arrayParametros['intTipoPromocionId']);
            $objQuery->setParameter("arrayEstados", $arrayParametros['arrayEstados']);

            $objQuery->setDQL($strQuery);
            $objAdmiTipoPlanProdPromo = $objQuery->getResult();
        }
        catch(\Exception $e) 
        {
            throw($e);
        }
        return $objAdmiTipoPlanProdPromo;
    }
    
   /**
    * getEmisorReglaPromoMensualidad, obtiene las reglas promocionales de Emisores.
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 27-09-2021
    *
    * @param array $arrayParametros [ 'intIdPromocion' => Id de la Promoción.
    *                                 'arrayEstado'    => Estado Grupo Promoción y Grupo Promocional Regla] 
    * costoQuery: 5
    * @return Response cadena de emisores.
    */
    public function getEmisorReglaPromoMensualidad($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('VALOR', 'valorRegla', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect  = " SELECT AGPR.VALOR ";
        $strFrom    = " FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION        AGP,
                             DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA AGPR,
                             DB_COMERCIAL.ADMI_CARACTERISTICA         AC ";
        $strWhere   = " WHERE AGPR.GRUPO_PROMOCION_ID = AGP.ID_GRUPO_PROMOCION
                              AND AC.ID_CARACTERISTICA = AGPR.CARACTERISTICA_ID
                              AND AGP.ID_GRUPO_PROMOCION = :intIdPromocion
                              AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_EMISOR'
                              AND AGP.ESTADO  NOT IN (:arrayEstado)
                              AND AGPR.ESTADO NOT IN (:arrayEstado) ";
        $objQuery->setParameter('intIdPromocion', $arrayParametros['intIdPromocion']);
        $objQuery->setParameter('arrayEstado', $arrayParametros['arrayEstado']);

        $strSql = $strSelect . $strFrom . $strWhere;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }
        
   /**
    * getEmisoresPromoMensualidad, obtiene los emisores de la promoción de mensualidad.
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 23-04-2019
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 27-09-2021 - Se agrega parámetro para realizar búsqueda por ID_BANCO_TIPO_CUENTA.
    *
    * @param array $arrayParametros ['arrayEmisorReglaPromo' => Arreglo de Id's Banco Tipo Cuenta ] 
    * 
    * costoQuery: 5
    * @return Response lista de emisores.
    */
    public function getEmisoresPromoMensualidad($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_TIPO_CUENTA', 'idTipoCuenta', 'string');
        $objResultSet->addScalarResult('ID_BANCO', 'idBanco', 'string');
        $objResultSet->addScalarResult('ES_TARJETA', 'esTarjeta', 'string');
        $objResultSet->addScalarResult('DESCRIPCION_CUENTA', 'descCuenta', 'string');
        $objResultSet->addScalarResult('DESCRIPCION_BANCO', 'descBanco', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect  = " SELECT DISTINCT TCTA.ID_TIPO_CUENTA, DECODE(BTC.ES_TARJETA, 'S', 0, BCO.ID_BANCO) ID_BANCO,
                          DECODE(BTC.ES_TARJETA, 'S', 'Tarjeta', 'Cuenta Bancaria') ES_TARJETA, TCTA.DESCRIPCION_CUENTA,
                          DECODE(BTC.ES_TARJETA, 'S', 'N/A', BCO.DESCRIPCION_BANCO) DESCRIPCION_BANCO ";
        $strFrom    = " FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA BTC,
                          ADMI_BANCO BCO, DB_GENERAL.ADMI_TIPO_CUENTA TCTA ";
        $strWhere   = " WHERE BTC.ID_BANCO_TIPO_CUENTA IN ( :arrayEmisorReglaPromo )
                            AND BCO.ID_BANCO = BTC.BANCO_ID
                            AND TCTA.ID_TIPO_CUENTA = BTC.TIPO_CUENTA_ID ";
        $strOrderBy = " ORDER BY 1 ";
        $objQuery->setParameter('arrayEmisorReglaPromo', $arrayParametros['arrayEmisorReglaPromo']);

        $strSql = $strSelect . $strFrom . $strWhere . $strOrderBy;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }
    
    /**
    * getDatosGenePromoAnchoBanda, obtiene información básica por tipo promoción ancho de banda.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *     
    * @param array $arrayParametros []
    *              'strEstado'          => Estado del tipo de Negocio.
    *              'intIdPromocion'     => Id de la promoción.
    *
    * costoQuery: 1
    * @return Response lista de información básica por tipo promoción ancho de banda.
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.1 15-12-2020 - Se modifica función para recibir el parametro de código promociones.
    *
    */
    public function getDatosGenePromoAnchoBanda($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_GRUPO_PROMOCION', 'idGrupoPromocion', 'string');
        $objResultSet->addScalarResult('NOMBRE_GRUPO', 'nombreGrupo', 'string');
        $objResultSet->addScalarResult('FE_INICIO_VIGENCIA', 'feInicioVigencia', 'string');
        $objResultSet->addScalarResult('FE_FIN_VIGENCIA', 'feFinVigencia', 'string');
        $objResultSet->addScalarResult('ANTIGUEDAD', 'antiguedad', 'string');
        $objResultSet->addScalarResult('TIPO_CLIENTE', 'tipoCliente', 'string');
        $objResultSet->addScalarResult('CODIGO_PROM', 'codigoProm', 'string');
        $objResultSet->addScalarResult('ESTADO', 'estado', 'string');

        $objQuery   = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect  = " SELECT AGP.ID_GRUPO_PROMOCION, AGP.NOMBRE_GRUPO,
                          TO_CHAR(AGP.FE_INICIO_VIGENCIA, 'RRRR-MM-DD HH24:MI:SS') FE_INICIO_VIGENCIA,
                          TO_CHAR(AGP.FE_FIN_VIGENCIA, 'RRRR-MM-DD HH24:MI:SS') FE_FIN_VIGENCIA,
                          (SELECT ATPR.VALOR FROM ADMI_TIPO_PROMOCION  ATP,
                             ADMI_TIPO_PROMOCION_REGLA ATPR, ADMI_CARACTERISTICA  AC
                           WHERE ATP.GRUPO_PROMOCION_ID      = AGP.ID_GRUPO_PROMOCION
                           AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                           AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                           AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_ANTIGUEDAD'
                           AND ATPR.ESTADO                   = :strEstado) ANTIGUEDAD,
                          (SELECT ATPR.VALOR FROM ADMI_TIPO_PROMOCION ATP,
                             ADMI_TIPO_PROMOCION_REGLA ATPR, ADMI_CARACTERISTICA AC
                           WHERE ATP.GRUPO_PROMOCION_ID      = AGP.ID_GRUPO_PROMOCION
                           AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                           AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                           AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_TIPO_CLIENTE'
                           AND ATPR.ESTADO                   = :strEstado) TIPO_CLIENTE,
                           (SELECT ATPR.VALOR FROM ADMI_TIPO_PROMOCION  ATP,
                            ADMI_TIPO_PROMOCION_REGLA ATPR, ADMI_CARACTERISTICA AC
                           WHERE ATP.GRUPO_PROMOCION_ID      = AGP.ID_GRUPO_PROMOCION
                           AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                           AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                           AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_CODIGO'
                           AND ATPR.ESTADO                   = :strEstado) CODIGO_PROM,
                           AGP.ESTADO ";
        $strFrom    = " FROM ADMI_GRUPO_PROMOCION AGP ";
        $strWhere   = " WHERE AGP.ID_GRUPO_PROMOCION    = :intIdPromocion ";

        $objQuery->setParameter('intIdPromocion', $arrayParametros['intIdPromocion']);
        $objQuery->setParameter('strEstado', $arrayParametros['strEstado']);
        $strSql     = $strSelect . $strFrom . $strWhere;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }
    
    /**
    * getPlanesPromoAnchoBanda, obtiene los planes por tipo promoción ancho de banda.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 28-10-2019 - Se cambia la validación por estado para que tomes todo los planes diferente de Eliminado.
    *
    * @author Daniel Reyes <gvalenzuela@telconet.ec>
    * @version 1.2 07-01-2020 - Se aumenta el campo de LineProfile para los planes ya seleccionados y poder validar.
    *
    * @param array $arrayParametros []
    *              'strEstado'          => Estado del tipo de Promoción.
    *              'intIdPromocion'     => Id de la promoción.
    *
    * costoQuery: 3
    * @return Response lista de planes por promoción.
    */
    public function getPlanesPromoAnchoBanda($arrayParametros)
    {   
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_TIPO_PLAN_PROD_PROMOCION', 'idTipoPlanProdPromo', 'string');
        $objResultSet->addScalarResult('PLAN_ID', 'idPlan', 'string');
        $objResultSet->addScalarResult('NOMBREPLAN', 'nombrePlan', 'string');
        $objResultSet->addScalarResult('PLAN_ID_SUPERIOR', 'idPlanSuperior', 'string');
        $objResultSet->addScalarResult('NOMBREPLANSUP', 'nombrePlanSuperior', 'string');
        $objResultSet->addScalarResult('LINEPROFILE', 'lineProfile', 'string');

        $objQuery   = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect  = " SELECT ATPL.ID_TIPO_PLAN_PROD_PROMOCION, ATPL.PLAN_ID,
                          (SELECT IPC.NOMBRE_PLAN
                           FROM DB_COMERCIAL.INFO_PLAN_CAB IPC WHERE IPC.ID_PLAN = TO_CHAR(ATPL.PLAN_ID)) NOMBREPLAN,
                          ATPL.PLAN_ID_SUPERIOR,
                          (SELECT IPC.NOMBRE_PLAN
                           FROM DB_COMERCIAL.INFO_PLAN_CAB IPC WHERE IPC.ID_PLAN = TO_CHAR(ATPL.PLAN_ID_SUPERIOR)) NOMBREPLANSUP,
                          (SELECT IPPC.VALOR FROM INFO_PLAN_DET IPD, INFO_PLAN_PRODUCTO_CARACT IPPC
                           WHERE IPD.ID_ITEM = IPPC.PLAN_DET_ID
                           AND PRODUCTO_CARACTERISITICA_ID = :intProdCaracteristica
                           AND IPD.PLAN_ID = TO_CHAR(ATPL.PLAN_ID)
                           AND IPD.PRODUCTO_ID = :intProducto) LINEPROFILE ";
        $strFrom    = " FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION          AGP,
                             DB_COMERCIAL.ADMI_TIPO_PROMOCION           ATP,
                             DB_COMERCIAL.ADMI_TIPO_PLAN_PROD_PROMOCION ATPL ";
        $strWhere   = " WHERE ATP.GRUPO_PROMOCION_ID  = AGP.ID_GRUPO_PROMOCION
                        AND ATP.ID_TIPO_PROMOCION     = ATPL.TIPO_PROMOCION_ID
                        AND AGP.ID_GRUPO_PROMOCION    = :intIdPromocion
                        AND UPPER(ATPL.ESTADO)       != :strEstadoEliminado ";
        $objQuery->setParameter('intIdPromocion'     , $arrayParametros['intIdPromocion']);
        $objQuery->setParameter('intProdCaracteristica' , 808);
        $objQuery->setParameter('intProducto' , 63);
        $objQuery->setParameter('strEstadoEliminado' , 'ELIMINADO');

        $strSql     = $strSelect . $strFrom . $strWhere;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }
    
   /**
    * getEmisorReglaPromoAnchoBanda, obtiene las reglas promocionales de Emisores.
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.0 27-09-2021
    *
    * @param array $arrayParametros ['intIdPromocion' => Id de la Promoción.
    *                                 'arrayEstado'    => Estado Grupo Promoción y Tipo Promoción Regla.] 
    * costoQuery: 5
    * @return Response cadena de emisores.
    */
    public function getEmisorReglaPromoAnchoBanda($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('VALOR', 'valorRegla', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect  = " SELECT ATPR.VALOR ";
        $strFrom    = " FROM ADMI_GRUPO_PROMOCION  AGP, ADMI_TIPO_PROMOCION  ATP,
                             ADMI_TIPO_PROMOCION_REGLA ATPR, ADMI_CARACTERISTICA  AC ";
        $strWhere   = " WHERE ATP.GRUPO_PROMOCION_ID       = AGP.ID_GRUPO_PROMOCION
                            AND ATP.ID_TIPO_PROMOCION          = ATPR.TIPO_PROMOCION_ID
                            AND AC.ID_CARACTERISTICA           = ATPR.CARACTERISTICA_ID
                            AND AGP.ID_GRUPO_PROMOCION         = :intIdPromocion
                            AND AC.DESCRIPCION_CARACTERISTICA  = 'PROM_EMISOR'
                            AND AGP.ESTADO                     NOT IN  (:arrayEstado)
                            AND ATPR.ESTADO                    NOT IN  (:arrayEstado) ";
        $objQuery->setParameter('intIdPromocion', $arrayParametros['intIdPromocion']);
        $objQuery->setParameter('arrayEstado', $arrayParametros['arrayEstado']);

        $strSql = $strSelect . $strFrom . $strWhere;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }

    /**
    * getEmisoresPromoAnchoBanda, obtiene los emisores por tipo promoción ancho de banda.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 27-09-2021 - Se agrega parámetro para realizar búsqueda por ID_BANCO_TIPO_CUENTA.
    * 
    * @param array $arrayParametros ['arrayEmisorReglaPromo' => Arreglo de Id's Banco Tipo Cuenta ]
    *
    * costoQuery: 5
    * @return Response lista de los emisores por tipo promoción ancho de banda.
    */
    public function getEmisoresPromoAnchoBanda($arrayParametros)
    {
        $objResultSet = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_TIPO_CUENTA', 'idTipoCuenta', 'string');
        $objResultSet->addScalarResult('ID_BANCO', 'idBanco', 'string');
        $objResultSet->addScalarResult('ES_TARJETA', 'esTarjeta', 'string');
        $objResultSet->addScalarResult('DESCRIPCION_CUENTA', 'descCuenta', 'string');
        $objResultSet->addScalarResult('DESCRIPCION_BANCO', 'descBanco', 'string');

        $objQuery = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect  = " SELECT DISTINCT TCTA.ID_TIPO_CUENTA,
                          DECODE(BTC.ES_TARJETA, 'S', 0, BCO.ID_BANCO) ID_BANCO,
                          DECODE(BTC.ES_TARJETA, 'S', 'Tarjeta', 'Cuenta Bancaria') ES_TARJETA,
                          TCTA.DESCRIPCION_CUENTA,
                          DECODE(BTC.ES_TARJETA, 'S', 'N/A', BCO.DESCRIPCION_BANCO) DESCRIPCION_BANCO ";
        $strFrom    = " FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA                  BTC,
                          DB_GENERAL.ADMI_BANCO                                 BCO,
                          DB_GENERAL.ADMI_TIPO_CUENTA                           TCTA ";
        $strWhere   = " WHERE BTC.ID_BANCO_TIPO_CUENTA IN ( :arrayEmisorReglaPromo )
                        AND BCO.ID_BANCO          = BTC.BANCO_ID
                        AND TCTA.ID_TIPO_CUENTA   = BTC.TIPO_CUENTA_ID ";
        $strOrderBy = " ORDER BY 1 ";

        $objQuery->setParameter('arrayEmisorReglaPromo', $arrayParametros['arrayEmisorReglaPromo']);
        $strSql     = $strSelect . $strFrom . $strWhere . $strOrderBy;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }

    /**
    * getSelectTiposNegocio, obtiene los tipos de Negocio por promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @param array $arrayParametros []
    *              'strEstado'          => Estado del tipo de Negocio.
    *              'intIdPromocion'     => Id de la promoción.
    *
    * costoQuery: 30
    * @return Response lista de Tipos de Negocio por promoción.
    */
    public function getSelectTiposNegocio($arrayParametros)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery       = " SELECT TP.ID_TIPO_NEGOCIO, TP.NOMBRE_TIPO_NEGOCIO
                            FROM DB_COMERCIAL.ADMI_TIPO_NEGOCIO TP,
                              (SELECT  ATPR.VALOR
                               FROM ADMI_GRUPO_PROMOCION  AGP, ADMI_TIPO_PROMOCION ATP,
                                 ADMI_TIPO_PROMOCION_REGLA ATPR, ADMI_CARACTERISTICA AC
                               WHERE ATP.GRUPO_PROMOCION_ID      = AGP.ID_GRUPO_PROMOCION
                               AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                               AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                               AND AGP.ID_GRUPO_PROMOCION        = :intIdPromocion
                               AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_TIPO_NEGOCIO')  EMI
                            WHERE TP.ID_TIPO_NEGOCIO IN ( SELECT REGEXP_SUBSTR(EMI.VALOR, '[^,]+', 1, LEVEL)
                            FROM DUAL
                            CONNECT BY REGEXP_SUBSTR(EMI.VALOR, '[^,]+', 1, LEVEL) IS NOT NULL) ";
        $objRsm->addScalarResult('ID_TIPO_NEGOCIO', 'id', 'integer');
        $objRsm->addScalarResult('NOMBRE_TIPO_NEGOCIO', 'nombre', 'string');        
        $objNtvQuery->setParameter('intIdPromocion' ,$arrayParametros['intIdPromocion']);
        return $objNtvQuery->setSQL($strQuery)->getArrayResult();
    }

    /**
    * getSelectUltimaMillas, obtiene las últimas millas por promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @param array $arrayParametros []
    *              'strEstado'          => Estado del tipo de Negocio.
    *              'intIdPromocion'     => Id de la promoción.
    *
    * costoQuery: 12
    * @return Response lista de últimas millas por promoción.
    */
    public function getSelectUltimaMillas($arrayParametros)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery       = " SELECT ATM.ID_TIPO_MEDIO, ATM.NOMBRE_TIPO_MEDIO
                            FROM DB_COMERCIAL.ADMI_TIPO_MEDIO ATM,
                              (SELECT ATPR.VALOR
                               FROM ADMI_GRUPO_PROMOCION AGP, ADMI_TIPO_PROMOCION ATP,
                                 ADMI_TIPO_PROMOCION_REGLA ATPR, ADMI_CARACTERISTICA AC
                               WHERE ATP.GRUPO_PROMOCION_ID      = AGP.ID_GRUPO_PROMOCION
                               AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                               AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                               AND AGP.ID_GRUPO_PROMOCION        = :intIdPromocion
                               AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_ULTIMA_MILLA' )  EMI
                            WHERE ATM.ID_TIPO_MEDIO IN ( SELECT REGEXP_SUBSTR(EMI.VALOR, '[^,]+', 1, LEVEL)
                            FROM DUAL
                            CONNECT BY REGEXP_SUBSTR(EMI.VALOR, '[^,]+', 1, LEVEL) IS NOT NULL ) ";
        $objRsm->addScalarResult('ID_TIPO_MEDIO', 'id', 'integer');
        $objRsm->addScalarResult('NOMBRE_TIPO_MEDIO', 'nombre', 'string');        
        $objNtvQuery->setParameter('intIdPromocion' ,$arrayParametros['intIdPromocion']);

        return $objNtvQuery->setSQL($strQuery)->getArrayResult();
    }

    /**
    * getSelectFormaPagos, obtiene las formas de pagos por promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @param array $arrayParametros []
    *              'strEstado'          => Estado del tipo de Negocio.
    *              'intIdPromocion'     => Id de la promoción.
    * 
    * costoQuery: 28
    * @return Response lista de formas de pagos por promoción.
    */
    public function getSelectFormaPagos($arrayParametros)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery       = " SELECT AFP.ID_FORMA_PAGO, AFP.DESCRIPCION_FORMA_PAGO
                            FROM DB_GENERAL.ADMI_FORMA_PAGO AFP,
                              (SELECT ATPR.VALOR
                               FROM ADMI_GRUPO_PROMOCION AGP, ADMI_TIPO_PROMOCION ATP,
                                 ADMI_TIPO_PROMOCION_REGLA ATPR, ADMI_CARACTERISTICA AC
                               WHERE ATP.GRUPO_PROMOCION_ID      = AGP.ID_GRUPO_PROMOCION
                               AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                               AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                               AND AGP.ID_GRUPO_PROMOCION        = :intIdPromocion
                               AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_FORMA_PAGO' )   EMI
                            WHERE AFP.ID_FORMA_PAGO IN ( SELECT REGEXP_SUBSTR(EMI.VALOR, '[^,]+', 1, LEVEL)
                            FROM DUAL
                            CONNECT BY REGEXP_SUBSTR(EMI.VALOR, '[^,]+', 1, LEVEL) IS NOT NULL ) ";
        $objRsm->addScalarResult('ID_FORMA_PAGO', 'id', 'integer');
        $objRsm->addScalarResult('DESCRIPCION_FORMA_PAGO', 'nombre', 'string');        
        $objNtvQuery->setParameter('intIdPromocion' ,$arrayParametros['intIdPromocion']);
        return $objNtvQuery->setSQL($strQuery)->getArrayResult();
    }

    /**
    * getSelectEstados, obtiene los estados que aplica la promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @param array $arrayParametros []
    *              'strEstado'          => Estado del tipo de Negocio.
    *              'intIdPromocion'     => Id de la promoción.
    *
    * costoQuery: 8
    * @return Response lista de estados que aplica la promoción.
    */
    public function getSelectEstados($arrayParametros)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery       = " SELECT REGEXP_SUBSTR(EMI.VALOR, '[^,]+', 1, LEVEL) NOMBRE
                            FROM DUAL,
                              (SELECT ATPR.VALOR
                               FROM ADMI_GRUPO_PROMOCION AGP, ADMI_TIPO_PROMOCION ATP,
                                 ADMI_TIPO_PROMOCION_REGLA ATPR, ADMI_CARACTERISTICA AC
                               WHERE ATP.GRUPO_PROMOCION_ID      = AGP.ID_GRUPO_PROMOCION
                               AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                               AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                               AND AGP.ID_GRUPO_PROMOCION        = :intIdPromocion
                               AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_ESTADO_SERVICIO' ) EMI
                            CONNECT BY REGEXP_SUBSTR(EMI.VALOR, '[^,]+', 1, LEVEL) IS NOT NULL ";
        $objRsm->addScalarResult('NOMBRE', 'id', 'integer');
        $objRsm->addScalarResult('NOMBRE', 'nombre', 'string');        
        $objNtvQuery->setParameter('intIdPromocion' ,$arrayParametros['intIdPromocion']);
        return $objNtvQuery->setSQL($strQuery)->getArrayResult();
    }
    
    /**
    * getSelectPeriodos, obtiene los preiodos que aplica la promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @param array $arrayParametros []
    *              'strEstado'          => Estado del tipo de Negocio.
    *              'intIdPromocion'     => Id de la promoción.
    *
    * costoQuery: 5
    * @return Response lista de periodos que aplica la promoción.
    */
    public function getSelectPeriodos($arrayParametros)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery    = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery       = " SELECT REGEXP_SUBSTR(PER.VALOR, '[^,]+', 1, LEVEL) VALOR
                            FROM DUAL,
                              (SELECT REPLACE(ATPR.VALOR, '|0', '') VALOR
                               FROM ADMI_TIPO_PROMOCION ATP, ADMI_TIPO_PROMOCION_REGLA ATPR, ADMI_CARACTERISTICA AC
                               WHERE ATP.GRUPO_PROMOCION_ID      = :intIdPromocion
                               AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                               AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                               AND AC.DESCRIPCION_CARACTERISTICA = 'PROM_PERIODO' ) PER
                            CONNECT BY REGEXP_SUBSTR(PER.VALOR, '[^,]+', 1, LEVEL) IS NOT NULL ";
        $objRsm->addScalarResult('VALOR', 'id', 'string');        
        $objNtvQuery->setParameter('intIdPromocion' ,$arrayParametros['intIdPromocion']);
        return $objNtvQuery->setSQL($strQuery)->getArrayResult();
    }

    /**
    * getBancoTipoCuenta, obtiene los banco tipo cuenta por emisor.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @param array $arrayParametros []
    *              'strIdCuenta'    => Id de la cuenta.
    *              'strIdBanco'     => Id del banco.
    *              'strEsTarjeta'   => Caracter es tarjeta 'S' ó 'N'.
    *
    * costoQuery: 3
    * @return Response lista de los banco tipo cuenta.
    */
    public function getBancoTipoCuenta($arrayParametros)
    {
        //EMISORES
        $objResultSet   = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_BANCO_TIPO_CUENTA', 'idBancoTipoCuenta', 'string');

        $objQuery       = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect      = " SELECT BTC.ID_BANCO_TIPO_CUENTA ";
        $strFrom        = " FROM DB_GENERAL.ADMI_BANCO_TIPO_CUENTA  BTC,
                              DB_GENERAL.ADMI_BANCO                 BCO,
                              DB_GENERAL.ADMI_TIPO_CUENTA           TCTA ";
        $strWhere       = " WHERE BTC.TIPO_CUENTA_ID  = :strIdCuenta
                            AND BCO.ID_BANCO          = BTC.BANCO_ID
                            AND BTC.BANCO_ID          = NVL(:strIdBanco, BTC.BANCO_ID)
                            AND BTC.ES_TARJETA        = :strEsTarjeta
                            AND BTC.ESTADO            != :strEstado
                            AND TCTA.ID_TIPO_CUENTA   = BTC.TIPO_CUENTA_ID ";

        $objQuery->setParameter('strIdCuenta', $arrayParametros['strIdCuenta']);
        $objQuery->setParameter('strIdBanco', $arrayParametros['strIdBanco']);
        $objQuery->setParameter('strEsTarjeta', $arrayParametros['strEsTarjeta']);
        $objQuery->setParameter('strEstado', 'Inactivo');
        $strSql = $strSelect . $strFrom . $strWhere;
        return $objQuery->setSQL($strSql)->getArrayResult();
    }
    
    /**
    * getSectorizacion, obtiene las sectorizaciones de una promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 24-04-2019
    *
    * @author Germán Valenzuela <gvalenzuela@telconet.ec>
    * @version 1.1 28-10-2019 - Se cambia la validación por estado para que tome todas las reglas diferente de Eliminado.
    *
    * @param array $arrayParametros []
    *              'strEstado'          => Estado del tipo de Negocio.
    *              'intIdPromocion'     => Id de la promoción.
    *
    * costoQuery: 5
    * @return Response lista de las sectorizaciones de una promoción.
    */
    public function getSectorizacion($arrayParametros)
    {
        $objResultSet           = new ResultSetMappingBuilder($this->_em);
        $objResultSet->addScalarResult('ID_SECTORIZACION', 'idSectorizacion', 'string');
        $objResultSet->addScalarResult('ID_JURISDICCION', 'idJurisdiccion', 'string');
        $objResultSet->addScalarResult('DESCRIPCION_JURISDICCION', 'descJurisdiccion', 'string');
        $objResultSet->addScalarResult('ID_CANTON', 'idCanton', 'string');
        $objResultSet->addScalarResult('NOMBRE_CANTON', 'descCanton', 'string');
        $objResultSet->addScalarResult('ID_PARROQUIA', 'idParroquia', 'string');
        $objResultSet->addScalarResult('NOMBRE_PARROQUIA', 'descParroquia', 'string');
        $objResultSet->addScalarResult('ID_SECTOR', 'idSector', 'string');
        $objResultSet->addScalarResult('ID_ELEMENTO', 'idElemento', 'string');
        $objResultSet->addScalarResult('ID_EDIFICIO', 'idEdificio', 'string');

        $objQuery               = $this->_em->createNativeQuery(null, $objResultSet);
        $strSelect              = " SELECT SECTORIZACION.ID_SECTORIZACION, SECTORIZACION.ID_JURISDICCION,
                                      (SELECT AJ.NOMBRE_JURISDICCION FROM DB_INFRAESTRUCTURA.ADMI_JURISDICCION AJ
                                       WHERE AJ.ID_JURISDICCION = SECTORIZACION.ID_JURISDICCION ) DESCRIPCION_JURISDICCION,
                                      NVL(SECTORIZACION.ID_CANTON,'0')ID_CANTON,
                                      (SELECT AC.NOMBRE_CANTON FROM DB_GENERAL.ADMI_CANTON AC
                                       WHERE AC.ID_CANTON = SECTORIZACION.ID_CANTON ) NOMBRE_CANTON,
                                      NVL(SECTORIZACION.ID_PARROQUIA,'0')ID_PARROQUIA,
                                      (SELECT AP.NOMBRE_PARROQUIA FROM DB_GENERAL.ADMI_PARROQUIA AP
                                       WHERE AP.ID_PARROQUIA = SECTORIZACION.ID_PARROQUIA) NOMBRE_PARROQUIA,
                                      NVL(SECTORIZACION.ID_SECTOR,'0')ID_SECTOR,
                                      NVL(SECTORIZACION.ID_ELEMENTO,'0')ID_ELEMENTO,
                                      NVL(SECTORIZACION.ID_EDIFICIO,'0')ID_EDIFICIO
                                    FROM (SELECT *
                                          FROM (SELECT AC.DESCRIPCION_CARACTERISTICA,
                                                  ATPR.VALOR, ATPR.SECUENCIA ID_SECTORIZACION
                                                FROM DB_COMERCIAL.ADMI_TIPO_PROMOCION       ATP,
                                                  DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA    ATPR,
                                                  DB_COMERCIAL.ADMI_CARACTERISTICA          AC
                                                WHERE ATP.GRUPO_PROMOCION_ID      = :intIdPromocion
                                                AND ATP.ID_TIPO_PROMOCION         = ATPR.TIPO_PROMOCION_ID
                                                AND AC.ID_CARACTERISTICA          = ATPR.CARACTERISTICA_ID
                                                AND AC.DESCRIPCION_CARACTERISTICA IN ('PROM_JURISDICCION',
                                                'PROM_CANTON', 'PROM_PARROQUIA', 'PROM_SECTOR',
                                                'PROM_ELEMENTO', 'PROM_EDIFICIO')
                                                AND ATPR.SECUENCIA                IS NOT NULL
                                                AND UPPER(ATPR.ESTADO)           != :strEstadoEliminado) PIVOT ( MAX ( VALOR )
                                            FOR DESCRIPCION_CARACTERISTICA
                                            IN ('PROM_JURISDICCION' ID_JURISDICCION, 'PROM_CANTON' ID_CANTON, 
                                                'PROM_PARROQUIA' ID_PARROQUIA, 'PROM_SECTOR' ID_SECTOR, 
                                                'PROM_ELEMENTO' ID_ELEMENTO, 'PROM_EDIFICIO' ID_EDIFICIO ))) SECTORIZACION ";
        $objQuery->setParameter('strEstadoEliminado', 'ELIMINADO');
        $objQuery->setParameter('intIdPromocion'    , $arrayParametros['intIdPromocion']);

        $strSql                 = $strSelect;
        $objSectorizacion       = $objQuery->setSQL($strSql)->getArrayResult();
        $arrayRespuesta         = array('objSectorizacion' => $objSectorizacion);
        return $arrayRespuesta;
    }    
    /**
    * getTipoPlanProdPromo, obtiene los registros activos por el tipo de promoción.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 19-03-2019
    *
    * @param array $arrayParametros []
    *              'intTipoPromocionId'     => Id del tipo de promoción.
    *              'intTipoPlanPromoId'     => Id plan producto promoción.
    *              'estado'                 => estado del registro.
    *
    * costoQuery: 3
    * @return Response lista de registros activos por el tipo de promoción.
    */
    public function getTipoPlanProdPromo($arrayParametros)
    {   
        $objQuery                    = $this->_em->createQuery();
        $objTipoPlanProdPromo        = null;
        try
        {
            $strSelect = " SELECT ATP ";
            $strFrom   = " FROM schemaBundle:AdmiTipoPlanProdPromocion ATP ";
            $strWhere  = " WHERE ATP.tipoPromocionId = :tipoPromocionId ";

            if( isset($arrayParametros['intTipoPlanPromoId']) && !empty($arrayParametros['intTipoPlanPromoId']) )
            {
                $strWhere .=" and  ATP.id = :tipoPlanPromoId ";
                $objQuery->setParameter("tipoPlanPromoId",  $arrayParametros['intTipoPlanPromoId']);
            }
            
            if( isset($arrayParametros['estado']) && !empty($arrayParametros['estado']) )
            {
                $strWhere .=" and  ATP.estado = :estado ";
                $objQuery->setParameter("estado",  $arrayParametros['estado']);
            }
            
            $objQuery->setParameter("tipoPromocionId", $arrayParametros['intTipoPromocionId']);
            $strSql = $strSelect . $strFrom . $strWhere;
            $objQuery->setDQL($strSql);
            $objTipoPlanProdPromo  = $objQuery->getResult();
        } 
        catch (\Exception $e) 
        {
            throw($e);
        }
        return $objTipoPlanProdPromo;
    }
    
      /**
     * validaGruposPromocionFechaInicio, Verifica si existen Grupos de promociones con viegencia Iniciada
     * 
     * @author Katherine Yager <kyager@telconet.ec>
     * @version 1.0 24-10-2019
     *      
     * @param array $arrayParametros[]                  
     *              'arrayIdsGrupoPromocion' => Array de Ids de grupo Promoción    
     *                    
     * @return $intCantidad
     */
    public function validaGruposPromocionFechaInicio($arrayParametros)
    {
        $intCantidad   = 0;
        $objRsm        = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery   = $this->_em->createNativeQuery(null, $objRsm);                             
        $strQuery      = " SELECT COUNT(*) AS CANTIDAD
                           FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION GP
                           WHERE GP.ID_GRUPO_PROMOCION IN (:arrayIdsGrupoPromocion) 
                           AND GP.FE_INICIO_VIGENCIA <= sysdate";                                           
        $objRsm->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        $objNtvQuery->setParameter('arrayIdsGrupoPromocion' ,$arrayParametros['arrayIdsGrupoPromocion']);        

        $intCantidad = $objNtvQuery->setSQL($strQuery)->getSingleScalarResult();                 
        return $intCantidad; 
    }  
    
    /**
    * ejecutarProcesoMasivo.
    *
    * Método que genera un Ejecuta un proceso Masivo que puede ser por Inactivación y/o Dado de Baja o Clonación de promociones.
    * 
    * @param array $arrayParametros[]                  
     *              'strTipoPma'       => Ids de los grupos de Promociones ADMI_GRUPO_PROMOCION 
     *              'strOrigenPma'     => Motivo del Proceso del PMA
     *              'strCodEmpresa'    => Observación del Proceso del PMA                 
     *              'strUsrCreacion'   => Usuario en sesión
    *
    * @return strResultado  Resultado de la ejecución.
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0 13-10-2020
    */
    public function ejecutarProcesoMasivo($arrayParametros)
    {
        $strResultado = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                             DB_COMERCIAL.CMKG_GRUPO_PROMOCIONES.P_EJECUTA_PM_PROMOCIONES
                             ( :Pv_TipoPma, :Pv_OrigenPma, :Pv_CodEmpresa, :Pv_Estado );
                           END;";
                $objStmt = $this->_em->getConnection()->prepare($strSql);
               
                $strResultado = str_pad($strResultado, 5000, " ");
                $objStmt->bindParam('Pv_TipoPma', $arrayParametros['strTipoPma']);
                $objStmt->bindParam('Pv_OrigenPma', $arrayParametros['strOrigenPma']);
                $objStmt->bindParam('Pv_CodEmpresa', $arrayParametros['strCodEmpresa']);
                $objStmt->bindParam('Pv_Estado', $arrayParametros['strEstado']);
                $objStmt->execute(); 
                $strResultado = "OK";
            }
            else
            {
                $strResultado= 'No se enviaron parámetros para ejecutar el Proceso Masivo '.$arrayParametros['strTipoPma'];
            }
        }
        catch (\Exception $e)
        {
            $strResultado= 'Ocurrió un error al ejecutar el Proceso Masivo '.$arrayParametros['strTipoPma'];
            throw($e);
        }       
        return $strResultado; 
    }
    
    /**
    * validaCodigoPromocion Función que valida los código de promociones ingresados por el Usuario.
    * 
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 15-10-2020
    * 
    * @return JsonResponse
    */
    public function validaCodigoPromocion($arrayParametros)
    {
        $strAplica          = "";
        $strMensaje         = "";
        $strDetalle         = "";
        $strServiciosMix    = "";
        $strIdTipoPromocion = "";
        $strOltEdificio     = "";
        $strTrama           = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {  
                $strTrama     = "CodigoGrupoPromocion:".$arrayParametros['strGrupoPromocion']."|".
                                "TipoPromocion:".$arrayParametros['strTipoPromocion']."|".
                                "TipoProceso:".$arrayParametros['strTipoProceso']."|".
                                "CodEmpresa:".$arrayParametros['strCodEmpresa']."|".
                                "Codigo:".$arrayParametros['strCodigo']."|".
				"EsContrato:".$arrayParametros['strEsContrato']."|";
                $strSql = "BEGIN
                             DB_COMERCIAL.CMKG_PROMOCIONES_UTIL.P_VALIDACIONES_PREVIAS_CODIGO
                             ( :Pv_Trama, :Pn_IdServicio, :Pn_IdPunto, :Pn_IdPlan, :Pn_IdProducto, :Pn_IdUltimaMilla,
                               :Pv_FormaPago, :Pv_Mensaje, :Pv_Detalle, :Pv_ServiciosMix );
                           END;";
                $objStmt            = $this->_em->getConnection()->prepare($strSql);
                $strMensaje         = str_pad($strMensaje, 5000, " ");
                $strAplica          = str_pad($strAplica, 5000, " ");
                $strDetalle         = str_pad($strMensaje, 5000, " ");
                $strServiciosMix    = str_pad($strServiciosMix, 5000, " ");
                $strIdTipoPromocion = str_pad($strIdTipoPromocion, 5000, " ");
                $strOltEdificio     = str_pad($strIdTipoPromocion, 5000, " ");
                $objStmt->bindParam('Pv_Trama', $strTrama);
                $objStmt->bindParam('Pn_IdServicio', $arrayParametros['intIdServicio']);
                $objStmt->bindParam('Pn_IdPunto', $arrayParametros['intIdPunto']);
                $objStmt->bindParam('Pn_IdPlan', $arrayParametros['intIdPlan']);
                $objStmt->bindParam('Pn_IdProducto', $arrayParametros['intIdProducto']);
                $objStmt->bindParam('Pn_IdUltimaMilla', $arrayParametros['intIdUltimaMilla']);
                $objStmt->bindParam('Pv_FormaPago', $arrayParametros['strFormaPago']);
                $objStmt->bindParam('Pv_Mensaje', $strMensaje);
                $objStmt->bindParam('Pv_Detalle', $strDetalle);
                $objStmt->bindParam('Pv_ServiciosMix', $strServiciosMix);
                $objStmt->execute();
            }
            else
            {   
                $strMensaje = 'N,No se enviaron parámetros para validar el código ingresado.';
            }
        }
        catch (\Exception $e)
        {
            $strMensaje = 'N,Ocurrió un error al validar el código ingresado.';
            throw($e);
        }
        $arrayMensaje        = explode(",", $strMensaje);
        $strAplica           = $arrayMensaje[0];
        $strMensaje          = $arrayMensaje[1];
        $strNombrePromocion  = $arrayMensaje[2];
        $strIdTipoPromocion  = $arrayMensaje[3];
        $strOltEdificio      = $arrayMensaje[4];
        $arrayRespuesta      = array('strNombrePromocion' => $strNombrePromocion,'strAplica'          => $strAplica,
                                     'strMensaje'         => $strMensaje,        'strDetalle'         => $strDetalle,
                                     'strServiciosMix'    => $strServiciosMix,   'strIdTipoPromocion' => $strIdTipoPromocion,
                                     'strOltEdificio'     => $strOltEdificio);
        return $arrayRespuesta; 
    }
    
    /**
    * validaCodigoPromocionUnico Función que valida si el código promocional ingresado es único
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0 15-10-2020
    * 
    * @return JsonResponse
    */
    public function validaCodigoPromocionUnico($arrayParametros)
    {
        $strGrupoPromocion   = $arrayParametros['strGrupoPromocion'];
        $strIdGrupoPromocion = $arrayParametros['strIdGrupoPromocion'];
        $intCantidad   = 0;
        $objRsm        = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery   = $this->_em->createNativeQuery(null, $objRsm);
        if($strGrupoPromocion=='PROM_MENS')
        {
            $strQuery      = " SELECT COUNT(*) AS CANTIDAD
                           FROM DB_COMERCIAL.ADMI_GRUPO_PROMOCION_REGLA GP
                           WHERE GP.CARACTERISTICA_ID = :intCaracteristicaId
                           AND GP.VALOR = :strCodigoPromocion
                           AND GP.ESTADO = 'Activo' ";
            if ($strIdGrupoPromocion!='')
            {
                 $strQuery .= " AND GP.GRUPO_PROMOCION_ID!=:strIdGrupoPromocion";
            }
        }
        else
        {
            $strQuery      = " SELECT COUNT(*) AS CANTIDAD
                           FROM DB_COMERCIAL.ADMI_TIPO_PROMOCION_REGLA GP
                           INNER JOIN DB_COMERCIAL.ADMI_TIPO_PROMOCION ATP 
                           ON ATP.ID_TIPO_PROMOCION=GP.TIPO_PROMOCION_ID
                           WHERE GP.CARACTERISTICA_ID = :intCaracteristicaId
                           AND GP.VALOR = :strCodigoPromocion
                           AND GP.ESTADO = 'Activo'"; 
            if ($strIdGrupoPromocion!='')
            {
                 $strQuery .= " AND ATP.GRUPO_PROMOCION_ID!=:strIdGrupoPromocion";
            }
        }      
        $objRsm->addScalarResult('CANTIDAD', 'Cantidad', 'integer');
        $objNtvQuery->setParameter('intCaracteristicaId' ,$arrayParametros['intCaracteristicaId']);      
        $objNtvQuery->setParameter('strCodigoPromocion'  ,$arrayParametros['strCodigoPromocion']); 
        
        if ($strIdGrupoPromocion!='')
        {
            $objNtvQuery->setParameter('strIdGrupoPromocion'  ,$arrayParametros['strIdGrupoPromocion']);
        }
        $intCantidad = $objNtvQuery->setSQL($strQuery)->getSingleScalarResult();
        return $intCantidad; 
    }
    
    /**
    * validaCodigoPromocionUnico Función que valida si el código promocional tiene mapeo.
    * 
    * @author Katherine Yager <kyager@telconet.ec>
    * @version 1.0 20-10-2020
    * 
    * @return JsonResponse
    */
    public function validaCodigoPromocionEstadoMapeo($arrayParametros)
    {
        $intCantidad   = 0;
        $objRsm        = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery   = $this->_em->createNativeQuery(null, $objRsm);                             
        $strQuery      = "  SELECT count (ISE.ID_SERVICIO) AS VALOR  
                            FROM DB_COMERCIAL.INFO_SERVICIO ISE
                            WHERE ISE.ID_SERVICIO = :intIdServicio
                                  AND DB_COMERCIAL.CMKG_PROMOCIONES.F_VALIDA_SERVICIO(ISE.ID_SERVICIO,:strCodigoGrupoPromo,:strCodEmpresa)='S' 
                                  AND  NOT EXISTS (SELECT DBISER.ID_SERVICIO
                                    FROM DB_COMERCIAL.INFO_SERVICIO DBISER,
                                      DB_COMERCIAL.ADMI_CARACTERISTICA DBAC,
                                      DB_COMERCIAL.INFO_SERVICIO_CARACTERISTICA DBISC
                                    WHERE DBISER.ID_SERVICIO        = ISE.ID_SERVICIO
                                    AND DBISC.SERVICIO_ID           = DBISER.ID_SERVICIO
                                    AND DBISC.ESTADO                = :strEstado
                                    AND DBAC.ID_CARACTERISTICA      = DBISC.CARACTERISTICA_ID
                                    AND DBAC.DESCRIPCION_CARACTERISTICA = :strCodigoPromocion)
                                    and ise.estado= :strEstadoServ";
        $objRsm->addScalarResult('VALOR', 'Valor', 'integer');
        $objNtvQuery->setParameter('intIdServicio'     ,$arrayParametros['intIdServicio']);    
        $objNtvQuery->setParameter('strCodigoGrupoPromo',$arrayParametros['strCodigoGrupoPromo']); 
        $objNtvQuery->setParameter('strCodEmpresa'     ,$arrayParametros['strCodEmpresa']); 
        $objNtvQuery->setParameter('strEstado'         ,$arrayParametros['strEstado']);   
        $objNtvQuery->setParameter('strCodigoPromocion',$arrayParametros['strDescripcionCaracteristica']);  
        $objNtvQuery->setParameter('strEstadoServ'     ,$arrayParametros['strEstado']);
        $intCantidad = $objNtvQuery->setSQL($strQuery)->getSingleScalarResult();                 
        return $intCantidad; 
    }

    /**
    * getCodigoPromocion Obtiene el código promocional mediante el idTipoPromocion.
    * 
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 15-10-2020
    * 
    * @return JsonResponse
    */
    public function getCodigoPromocion($arrayParametros)
    {
        $strCodigoPromocion = "";
        $strCodigo          = "";
        $strGrupoPromocion  = "";
        try
        {
            if($arrayParametros && count($arrayParametros)>0)
            {
                $strSql = "BEGIN
                          :strCodigo := DB_COMERCIAL.CMKG_PROMOCIONES_UTIL.F_OBTIENE_PROMOCION_COD
                             ( :Fv_Codigo, :Fv_IdTipoPromocion, :Fv_CodigoGrupoPromocion, :Fv_CodEmpresa );
                           END;";
                $objStmt           = $this->_em->getConnection()->prepare($strSql);
                $strCodigo         = str_pad($strCodigo, 5000, " ");
                $objStmt->bindParam('Fv_Codigo', $strCodigoPromocion);
                $objStmt->bindParam('Fv_IdTipoPromocion', $arrayParametros['strIdTipoPromocion']);
                $objStmt->bindParam('Fv_CodigoGrupoPromocion',$strGrupoPromocion);
                $objStmt->bindParam('Fv_CodEmpresa', $arrayParametros['strCodEmpresa']);
                $objStmt->bindParam('strCodigo',$strCodigo);
                $objStmt->execute();
            }
            else
            {   
                $strCodigo = '*******';
            }
        }
        catch (\Exception $e)
        {
            $strCodigo = '*******';
        }
        return $strCodigo; 
    }
    
    /**
    * validaPuntoAdicional Función que verifica la cantidad de puntos por Cliente
    * 
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 08-03-2021
    * 
    * @return $intCantidad
    */
    public function validaPuntoAdicional($arrayParametros)
    {
        $objRsm        = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery   = $this->_em->createNativeQuery(null, $objRsm);
        $strQuery      = " SELECT COUNT(TABLA.TOTAL) AS TOTAL
                           FROM (SELECT PTO_B.PERSONA_EMPRESA_ROL_ID, 
                            COUNT(*) AS TOTAL
                           FROM DB_COMERCIAL.INFO_PUNTO PTO_A, DB_COMERCIAL.INFO_PUNTO PTO_B
                           WHERE PTO_A.ID_PUNTO              =  :intIdPunto
                           AND PTO_A.PERSONA_EMPRESA_ROL_ID  =  PTO_B.PERSONA_EMPRESA_ROL_ID
                           AND PTO_B.ID_PUNTO                <> PTO_A.ID_PUNTO
                           AND EXISTS (SELECT 1 FROM DB_COMERCIAL.INFO_SERVICIO SERV,
                                        DB_COMERCIAL.INFO_PLAN_CAB IPC, DB_COMERCIAL.INFO_PLAN_DET IPD,
                                        DB_COMERCIAL.ADMI_PRODUCTO AP
                                      WHERE SERV.PUNTO_ID     = PTO_B.ID_PUNTO
                                      AND SERV.PLAN_ID        = IPC.ID_PLAN
                                      AND IPC.ID_PLAN         = IPD.PLAN_ID
                                      AND IPD.PRODUCTO_ID     = AP.ID_PRODUCTO
                                      AND AP.NOMBRE_TECNICO   = 'INTERNET'
                                      AND SERV.ESTADO         IN ('Activo','In-Corte'))
                           GROUP BY PTO_B.PERSONA_EMPRESA_ROL_ID) TABLA ";
        $objRsm->addScalarResult('TOTAL', 'Valor', 'integer');
        $objNtvQuery->setParameter('intIdPunto',$arrayParametros['intIdPunto']);
        $intCantidad = $objNtvQuery->setSQL($strQuery)->getSingleScalarResult();
        return $intCantidad; 
    }

    /**
     * isExistePlanPromocionBw()
     * Compara los planes de la promoción actual con otros planes de otras promociones de ancho de banda en la misma sectorización.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 11-02-2022
     * 
     * @param Array $arrayParametros["intIdPromocion"     => Id de la promoción,
     *                               "arraySectorizacion" => areglo de la sectorización,
     *                               "strFeIniVigencia"   => fecha inicio de la promoción,
     *                               "strFeFinVigencia"   => fecha fin de la promoción,
     *                               "intIdPlan"          => id del plan actual]
     * 
     * @return Boolean $booleanRespuesta 
     */
    public function isExistePlanPromocionBw($arrayParametros)
    {
        try
        {
            //obtengo la sectorización
            $arraySectorString = [];
            foreach($arrayParametros['arraySectorizacion'] as $arraySector)
            {
                $arraySectorString[] = $arraySector['intJurisdiccion'].','.$arraySector['intCanton']
                                       .','.$arraySector['intParroquia'];
            }

            $strResultado = null;
            $strResultado = str_pad($strResultado, 3000, " ");
            $strSql = " BEGIN
                            DB_COMERCIAL.CMKG_PROMOCIONES_BW.P_VERIFICAR_PLAN_PROMO_BW(Pn_IdPromocion    => :Pn_IdPromocion,
                                                                                       Pv_FechaInicio    => :Pv_FechaInicio,
                                                                                       Pv_FechaFin       => :Pv_FechaFin,
                                                                                       Pv_Jurisdicciones => :Pv_Jurisdicciones,
                                                                                       Pn_IdPlan         => :Pn_IdPlan,
                                                                                       Pv_Resultado      => :Pv_Resultado);
                        END; ";
            $objStmt = $this->_em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pn_IdPromocion',    $arrayParametros['intIdPromocion']);
            $objStmt->bindParam('Pv_FechaInicio',    $arrayParametros['strFeIniVigencia']);
            $objStmt->bindParam('Pv_FechaFin',       $arrayParametros['strFeFinVigencia']);
            $objStmt->bindParam('Pv_Jurisdicciones', implode($arraySectorString,';'));
            $objStmt->bindParam('Pn_IdPlan',         $arrayParametros['intIdPlan']);
            $objStmt->bindParam('Pv_Resultado',      $strResultado);
            $objStmt->execute();

            return ($strResultado == 'SI');
        }
        catch (\Exception $e)
        {
            throw($e);
        }
    }
}
