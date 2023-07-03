<?php

namespace telconet\schemaBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiProyectosRepository extends EntityRepository 
{

    /**
     * Documentación para el método 'getProyectos'.
     *
     * Método encargado de retornar los proyectos.
     *
     * Costo 11
     *
     * @param array $arrayParametros [
     *                                  "intIdProyecto"     => Id del proyecto.
     *                                  "strNombreProyecto" => Nombre del proyecto.
     *                                  "strEstado"         => Estado del proyecto.
     *                                  "strFechaIni"       => Fecha inicial.
     *                                  "strFechaFin"       => Fecha fin.
     *                                  "intIdEmpresa"      => Id de la empresa.
     *                               ]
     *
     * @return array $arrayResultado arreglo del subgerente.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 22-06-2021
     *
     */
    public function getProyectos($arrayParametros)
    {
        try
        {
            $intIdProyecto     = $arrayParametros['intIdProyecto']     ? $arrayParametros['intIdProyecto']:"";
            $strNombreProyecto = $arrayParametros['strNombreProyecto'] ? $arrayParametros['strNombreProyecto']:"";
            $strEstado         = $arrayParametros['strEstado']         ? $arrayParametros['strEstado']:"";
            $strFechaIni       = $arrayParametros['strFechaIni']       ? $arrayParametros['strFechaIni']:"";
            $strFechaFin       = $arrayParametros['strFechaFin']       ? $arrayParametros['strFechaFin']:"";
            $intIdEmpresa      = $arrayParametros['intIdEmpresa']      ? $arrayParametros['intIdEmpresa']:"";
            $arrayDatos        = array();
            $intTotal          = 0;
            $strMensajeError   = "";
            $objRsm            = new ResultSetMappingBuilder($this->_em);
            $objQuery          = $this->_em->createNativeQuery(null,$objRsm);
            $objRsmCount       = new ResultSetMappingBuilder($this->_em);
            $objQueryCount     = $this->_em->createNativeQuery(null, $objRsmCount);

            if(empty($intIdEmpresa))
            {
                throw new \Exception('El campo codigo de empresa es obligatorio para realizar la consulta.');
            }
            $strSelectCount   = " SELECT COUNT(*) AS TOTAL ";
            $strSelect        = " SELECT P.ID_PROYECTO,
                                         P.NOMBRE,
                                         P.RESPONSABLE_ID,
                                         P.TIPO_CONTABILIDAD,
                                         P.NO_CIA,
                                         P.CUENTA_ID,
                                         TRUNC(P.FE_INICIO) FE_INICIO,
                                         TRUNC(P.FE_FIN) FE_FIN,
                                         P.ESTADO,
                                         P.FE_CREACION,
                                         P.USR_CREACION ,
                                         PER.APELLIDOS||' ' ||PER.NOMBRES NOMBRE_PERSONA";
            $strFrom   = " FROM NAF47_TNET.ADMI_PROYECTO P, DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PE,
                                DB_COMERCIAL.INFO_PERSONA PER";

            $strWhere  = " WHERE P.RESPONSABLE_ID=PE.ID_PERSONA_ROL
                                 AND PE.PERSONA_ID=PER.ID_PERSONA " ;
            $strWhere  .= " AND P.NO_CIA = :intIdEmpresa " ;
            $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            $objQueryCount->setParameter("intIdEmpresa", $intIdEmpresa);
            if(!empty($intIdProyecto))
            {
                $strWhere .= " AND P.ID_PROYECTO = :intIdProyecto ";
                $objQuery->setParameter("intIdProyecto", $intIdProyecto);
                $objQueryCount->setParameter("intIdProyecto", $intIdProyecto);
            }
            if(!empty($strEstado))
            {
                $strWhere .= " AND P.ESTADO = :strEstado ";
                $objQuery->setParameter("strEstado", $strEstado);
                $objQueryCount->setParameter("strEstado", $strEstado);
            }
            if(!empty($strNombreProyecto))
            {
                $strWhere .= " AND UPPER(NOMBRE)    LIKE (:strNombreProyecto) ";
                $objQuery->setParameter('strNombreProyecto', '%' . strtoupper($strNombreProyecto) . '%');
                $objQueryCount->setParameter('strNombreProyecto', '%' . strtoupper($strNombreProyecto) . '%');
            }
            if(!empty($strFechaIni))
            {
                $strWhere .= " AND to_char(FE_CREACION, 'DD/MM/YYYY') >= :strFechaIni ";
                $objQuery->setParameter('strFechaIni', $strFechaIni);
                $objQueryCount->setParameter('strFechaIni', $strFechaIni);
            }

            if(!empty($strFechaFin))
            {
                $strWhere .= " AND to_char(FE_CREACION, 'DD/MM/YYYY') <= :strFechaFin ";
                $objQuery->setParameter('strFechaFin', $strFechaFin);
                $objQueryCount->setParameter('strFechaFin', $strFechaFin);
            }
            $objRsm->addScalarResult('ID_PROYECTO',       'ID_PROYECTO',       'string');
            $objRsm->addScalarResult('NOMBRE',            'NOMBRE',            'string');
            $objRsm->addScalarResult('RESPONSABLE_ID',    'RESPONSABLE_ID',    'string');
            $objRsm->addScalarResult('TIPO_CONTABILIDAD', 'TIPO_CONTABILIDAD', 'string');
            $objRsm->addScalarResult('NO_CIA',            'NO_CIA',            'string');
            $objRsm->addScalarResult('CUENTA_ID',         'CUENTA_ID',         'string');
            $objRsm->addScalarResult('FE_INICIO',          'FE_INICIO',          'string');
            $objRsm->addScalarResult('FE_FIN',            'FE_FIN',            'string');
            $objRsm->addScalarResult('ESTADO',            'ESTADO',            'string');
            $objRsm->addScalarResult('FE_CREACION',       'FE_CREACION',       'string');
            $objRsm->addScalarResult('USR_CREACION',      'USR_CREACION',      'string');
            $objRsm->addScalarResult('NOMBRE_PERSONA',      'NOMBRE_PERSONA',      'string');
            $objRsmCount->addScalarResult('TOTAL',        'total',             'integer');
            $strSql      = $strSelect.$strFrom.$strWhere;
            $strSqlFinal ='';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio     = $arrayParametros['intStart'];
                    $intFin        = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal   = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM ('.$strSql.') consultaPrincipal 
                                            WHERE rownum<='.$intFin.'
                                        ) WHERE consultaPrincipal_rownum >'.$intInicio;
                }
                else
                {
                    $strSqlFinal   = '  SELECT consultaPrincipal.* 
                                        FROM ('.$strSql.') consultaPrincipal 
                                        WHERE rownum<='.$arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSql;
            }

            $objQuery->setSQL($strSqlFinal);
            $arrayDatos  = $objQuery->getResult();
            $strSqlCount = $strSelectCount." FROM ( ".$strSql." )";
            $objQueryCount->setSQL($strSqlCount);
            $intTotal    = $objQueryCount->getSingleScalarResult();
        } 
        catch (\Exception $ex) 
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total']     = $intTotal;
        $arrayResultado['error']     = $strMensajeError;
        return $arrayResultado; 
    }
    
    /**
     * Documentación para el método 'getListaResponsables'.
     *
     * Método encargado de retornar personas tipo empleado.
     *
     * Costo 11
     *
     * @param array $arrayParametros [
     *                                  "strEstado"         => Estado del proyecto.
     *                                ]
     *
     * @return array $arrayResultado arreglo del subgerente.
     *
     * @author Byron Anton <banton@telconet.ec>
     * @version 1.0 22-06-2021
     *
     */
    public function getListaResponsables($arrayParametros)
    {
        try
        {
            $intIdEmpresa      = $arrayParametros['intIdEmpresa']      ? $arrayParametros['intIdEmpresa']:"";
            $arrayDatos        = array();
            $intTotal          = 0;
            $strMensajeError   = "";
            $objRsm            = new ResultSetMappingBuilder($this->_em);
            $objQuery          = $this->_em->createNativeQuery(null,$objRsm);
            $objRsmCount       = new ResultSetMappingBuilder($this->_em);
            $objQueryCount     = $this->_em->createNativeQuery(null, $objRsmCount);

            if(empty($intIdEmpresa))
            {
                throw new \Exception('El campo codigo de empresa es obligatorio para realizar la consulta.');
            }
            $strSelectCount   = " SELECT COUNT(*) AS TOTAL ";
            $strSelect        = " SELECT  PR.ID_PERSONA_ROL,
                                          APELLIDOS||' '||NOMBRES NOMBRES,
                                          TR.DESCRIPCION_TIPO_ROL";
            $strFrom          = " FROM DB_COMERCIAL.INFO_PERSONA P,
                                   DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL PR,
                                   DB_COMERCIAL.INFO_EMPRESA_ROL ER,
                                   DB_GENERAL.ADMI_ROL R,
                                   DB_GENERAL.ADMI_TIPO_ROL TR";

            $strWhere  = " WHERE P.ID_PERSONA=PR.PERSONA_ID
                            AND PR.EMPRESA_ROL_ID=ER.ID_EMPRESA_ROL
                            AND ER.ROL_ID=R.ID_ROL
                            AND R.TIPO_ROL_ID=TR.ID_TIPO_ROL
                            AND P.ESTADO='Activo'
                            AND PR.ESTADO='Activo'
                            AND TR.ID_TIPO_ROL =1" ;
            $strWhere  .= " AND ER.EMPRESA_COD = :intIdEmpresa " ;
            $strWhere  .= " ORDER BY 2 " ;
            $objQuery->setParameter("intIdEmpresa", $intIdEmpresa);
            $objQueryCount->setParameter("intIdEmpresa", $intIdEmpresa);
           
           
            $objRsm->addScalarResult('ID_PERSONA_ROL',       'ID_PERSONA_ROL',       'string');
            $objRsm->addScalarResult('NOMBRES',              'NOMBRES',              'string');
            $objRsm->addScalarResult('DESCRIPCION_TIPO_ROL', 'DESCRIPCION_TIPO_ROL', 'string');
            $objRsmCount->addScalarResult('TOTAL',        'total',             'integer');
            $strSql      = $strSelect.$strFrom.$strWhere;
            $strSqlFinal ='';

            if(isset($arrayParametros['intStart']) && isset($arrayParametros['intLimit']))
            {
                if($arrayParametros['intStart'] && $arrayParametros['intLimit'])
                {
                    $intInicio     = $arrayParametros['intStart'];
                    $intFin        = $arrayParametros['intStart'] + $arrayParametros['intLimit'];
                    $strSqlFinal   = '  SELECT * FROM 
                                        (
                                            SELECT consultaPrincipal.*,rownum AS consultaPrincipal_rownum 
                                            FROM ('.$strSql.') consultaPrincipal 
                                            WHERE rownum<='.$intFin.'
                                        ) WHERE consultaPrincipal_rownum >'.$intInicio;
                }
                else
                {
                    $strSqlFinal   = '  SELECT consultaPrincipal.* 
                                        FROM ('.$strSql.') consultaPrincipal 
                                        WHERE rownum<='.$arrayParametros['intLimit'];
                }
            }
            else
            {
                $strSqlFinal = $strSql;
            }

            $objQuery->setSQL($strSqlFinal);
            $arrayDatos  = $objQuery->getResult();
            $strSqlCount = $strSelectCount." FROM ( ".$strSql." )";
            $objQueryCount->setSQL($strSqlCount);
            $intTotal    = $objQueryCount->getSingleScalarResult();
        } 
        catch (\Exception $ex) 
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total']     = $intTotal;
        $arrayResultado['error']     = $strMensajeError;
        return $arrayResultado; 
    }
    
    /**
     * Documentación para el método 'getPedidoResponsable'.
     *
     * Método encargado de retornar el idPedidoDetalle.
     *
     * Costo 19
     *
     * @param array $arrayParametros [
     *                                  "intServicio"         => id del servicio.
     *                                  "strDepartNombre"     => nombre del departamento.
     *                                ]
     *
     * @return array $arrayResultado.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 23-03-2023
     *
     */
    public function getPedidoResponsable($arrayParametros)
    {
        try
        {
            $intServicio       = $arrayParametros['intServicio']      ? $arrayParametros['intServicio']:"";
            $strDepartNombre   = $arrayParametros['strDepartNombre']  ? $arrayParametros['strDepartNombre']:"";
            $arrayDatos        = array();
            $intTotal          = 0;
            $strMensajeError   = "";
            $objRsm            = new ResultSetMappingBuilder($this->_em);
            $objQuery          = $this->_em->createNativeQuery(null,$objRsm);
            $objRsmCount       = new ResultSetMappingBuilder($this->_em);
            $objQueryCount     = $this->_em->createNativeQuery(null, $objRsmCount);

            if(empty($intServicio))
            {
                throw new \Exception('El campo servicio es obligatorio para realizar la consulta.');
            }
            if(empty($strDepartNombre))
            {
                throw new \Exception('El campo departamento es obligatorio para realizar la consulta.');
            }
            $strSelectCount   = " SELECT COUNT(*) AS TOTAL ";
            $strSelect        = " SELECT  ipd.id_pedido_detalle as DETALLEID ";
            $strFrom          = " FROM db_compras.info_pedido ip, "
                                . "    db_compras.info_pedido_detalle ipd, "
                                . "    db_compras.admi_departamento ad ";
            $strWhere  = " WHERE ipd.PEDIDO_ID = ip.ID_PEDIDO and
                                    ip.DEPARTAMENTO_ID = ad.ID_DEPARTAMENTO and
                                    ad.EMPRESA_ID = 1 and
                                    ipd.SERVICIO_ID_TELCOS = :intServicio and 
                                    upper(ad.nombre) = upper(:strDepartamentoNombre)" ;

            $objQuery->setParameter("intServicio", $intServicio);
            $objQueryCount->setParameter("intServicio", $intServicio);
            $objQuery->setParameter("strDepartamentoNombre", $strDepartNombre);
            $objQueryCount->setParameter("strDepartamentoNombre", $strDepartNombre);
           
            $objRsm->addScalarResult('DETALLEID',       'detalleId',       'integer');
            $objRsmCount->addScalarResult('TOTAL',        'total',         'integer');
            $strSql      = $strSelect.$strFrom.$strWhere;

            $objQuery->setSQL($strSql);
            $arrayDatos  = $objQuery->getResult();
            $strSqlCount = $strSelectCount." FROM ( ".$strSql." )";
            $objQueryCount->setSQL($strSqlCount);
            $intTotal    = $objQueryCount->getSingleScalarResult();
        } 
        catch (\Exception $ex) 
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total']     = $intTotal;
        $arrayResultado['error']     = $strMensajeError;
        return $arrayResultado; 
    }
    
    
    /**
     * Documentación para el método 'getActualizaResponsable'.
     *
     * Método encargado de actualizar el responsable del pedido.
     *
     * Costo 19
     *
     * @param array $arrayParametros [
     *                                  "intUsuarioId"         => id del usuario.
     *                                  "strLoginUsu"          => login del usuario.
     *                                  "arrayDetalleId"       => arreglo de id detalle.
     *                                ]
     *
     * @return array $arrayResultado.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 23-03-2023
     *
     */
    public function getActualizaResponsable($arrayParametros)
    {
        try
        {
            $intUsuarioId      = $arrayParametros['intUsuarioId']      ? $arrayParametros['intUsuarioId']:"";
            $strLoginUsu       = $arrayParametros['strLoginUsu']  ? $arrayParametros['strLoginUsu']:"";
            $arrayDetalleId    = $arrayParametros['arrayDetalleId']  ? $arrayParametros['arrayDetalleId']:"";
            $arrayDatos        = array();
            $intTotal          = 0;
            $strMensajeError   = "";
            $objRsm            = new ResultSetMappingBuilder($this->_em);
            $objQuery          = $this->_em->createNativeQuery(null,$objRsm);
            $objRsmCount       = new ResultSetMappingBuilder($this->_em);
            $objQueryCount     = $this->_em->createNativeQuery(null, $objRsmCount);

            if(empty($intUsuarioId))
            {
                throw new \Exception('El campo idUsuario es obligatorio para realizar el update.');
            }
            if(empty($strLoginUsu))
            {
                throw new \Exception('El campo Login es obligatorio para realizar el update.');
            }
            if(!is_array($arrayDetalleId))
            {
                throw new \Exception('El campo detalleId es obligatorio para realizar el update.');
            }
            $strSql  = " UPDATE db_compras.info_pedido_detalle "
                     . " SET usr_asignado_id = :intUsuarioId, usr_asignado = :strUsuario "
                     . " WHERE id_pedido_detalle IN (:idDetalle) ";      

            $objQuery->setParameter("intUsuarioId", $intUsuarioId);
            $objQuery->setParameter("strUsuario", $strLoginUsu);
            $objQuery->setParameter("idDetalle", $arrayDetalleId);           
            
            $objQuery->setSQL($strSql);
            $arrayDatos  = $objQuery->getResult();
        } 
        catch (\Exception $ex) 
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['error']     = $strMensajeError;
        return $arrayResultado; 
    }

    /**
     * Documentación para el método 'getPersonaByNaf'.
     *
     * Método encargado de retornar el no_emple,login .
     *
     * Costo 19
     *
     * @param array $arrayParametros [
     *                                  "strLogin"         => login de la persona.
     *                                ]
     *
     * @return array $arrayResultado.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 1.0 23-03-2023
     *
     */
    public function getPersonaByNaf($arrayParametros)
    {
        try
        {
            $strLogin          = $arrayParametros['strLogin']      ? $arrayParametros['strLogin']:"";
            $strEmpresa        = $arrayParametros['strEmpresa']  ? $arrayParametros['strEmpresa']:"10";
            $arrayDatos        = array();
            $intTotal          = 0;
            $strMensajeError   = "";
            $objRsm            = new ResultSetMappingBuilder($this->_em);
            $objQuery          = $this->_em->createNativeQuery(null,$objRsm);
            $objRsmCount       = new ResultSetMappingBuilder($this->_em);
            $objQueryCount     = $this->_em->createNativeQuery(null, $objRsmCount);

            if(empty($strLogin))
            {
                throw new \Exception('El campo login es obligatorio para realizar la consulta.');
            }
            $strSelectCount   = " SELECT COUNT(*) AS TOTAL ";
            $strSelect        = " SELECT  vee.no_emple as EMPLEADO,
                                          vee.login_emple as LOGIN ";
            $strFrom          = " FROM NAF47_TNET.V_empleados_empresas vee ";
            $strWhere  = " WHERE vee.login_emple = :strLogin and
                                    vee.estado='A' and
                                    vee.no_cia = :strEmpresa " ;

            $objQuery->setParameter("strLogin", $strLogin);
            $objQueryCount->setParameter("strLogin", $strLogin);
            $objQuery->setParameter("strEmpresa", $strEmpresa);
            $objQueryCount->setParameter("strEmpresa", $strEmpresa);
           
            $objRsm->addScalarResult('EMPLEADO',       'empleadoid',       'integer');
            $objRsm->addScalarResult('LOGIN',       'login',       'string');
            $objRsmCount->addScalarResult('TOTAL',        'total',         'integer');
            $strSql      = $strSelect.$strFrom.$strWhere;

            $objQuery->setSQL($strSql);
            $arrayDatos  = $objQuery->getResult();
            $strSqlCount = $strSelectCount." FROM ( ".$strSql." )";
            $objQueryCount->setSQL($strSqlCount);
            $intTotal    = $objQueryCount->getSingleScalarResult();
        } 
        catch (\Exception $ex) 
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['total']     = $intTotal;
        $arrayResultado['error']     = $strMensajeError;
        return $arrayResultado; 
    }

      
}
