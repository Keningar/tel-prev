<?php

    namespace telconet\schemaBundle\Repository;
    use Doctrine\ORM\EntityRepository;
    use Doctrine\ORM\Query\ResultSetMappingBuilder;

    class InfoErrorRepository extends EntityRepository
    {
        /**
         * Método encargado de realizar la consulta de los errores almacenados en la INFO_ERROR de DB_GENERAL.
         *
         * Costo 8
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 13-09-2018
         *
         * @param $arrayParametros [
         *                              intIdError         => Id del error,
         *                              strAplicacion      => Aplicacion,
         *                              strProceso         => Proceso,
         *                              strDetalleError    => Detalle del error,
         *                              strUsuarioCreacion => Usuario de creación,
         *                              strIpCreacion      => Ip de creación,
         *                              strFeCreaIni       => Fecha Inicio de creación,
         *                              strFeCreaFin       => Fecha Fin de creación
         *                         ]
         * @return $arrayResultado
         */
        public function getInfoError($arrayParametros)
        {
            $arrayResultado = array();
            $strWhere       = '';

            try
            {
                $objResultSetMap = new ResultSetMappingBuilder($this->_em);
                $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

                if(isset($arrayParametros['intIdError']) && !empty($arrayParametros['intIdError']))
                {
                    $strWhere .= 'AND ERROR.ID_ERROR = :intIdError ';
                    $objNativeQuery->setParameter("intIdError",  $arrayParametros['intIdError']);
                }

                if(isset($arrayParametros['strAplicacion']) && !empty($arrayParametros['strAplicacion']))
                {
                    $strWhere .= 'AND ERROR.APLICACION like (:strAplicacion) ';
                    $objNativeQuery->setParameter("strAplicacion", "%".$arrayParametros['strAplicacion']."%");
                }

                if(isset($arrayParametros['strProceso']) && !empty($arrayParametros['strProceso']))
                {
                    $strWhere .= 'AND ERROR.PROCESO like (:strProceso) ';
                    $objNativeQuery->setParameter("strProceso", "%".$arrayParametros['strProceso']."%");
                }

                if(isset($arrayParametros['strDetalleError']) && !empty($arrayParametros['strDetalleError']))
                {
                    $strWhere .= 'AND ERROR.DETALLE_ERROR like (:strDetalleError) ';
                    $objNativeQuery->setParameter("strDetalleError", "%".$arrayParametros['strDetalleError']."%");
                }

                if(isset($arrayParametros['strUsuarioCreacion']) && !empty($arrayParametros['strUsuarioCreacion']))
                {
                    $strWhere .= 'AND ERROR.USR_CREACION = :strUsuarioCreacion ';
                    $objNativeQuery->setParameter("strUsuarioCreacion", $arrayParametros['strUsuarioCreacion']);
                }

                if(isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']))
                {
                    $strWhere .= 'AND ERROR.IP_CREACION = :strIpCreacion ';
                    $objNativeQuery->setParameter("strIpCreacion", $arrayParametros['strIpCreacion']);
                }

                if (isset($arrayParametros['strFeCreaIni']) && !empty($arrayParametros['strFeCreaIni']))
                {
                    $strWhere .= " AND TO_CHAR(ERROR.FE_CREACION,'RRRR-MM-DD') >= :strFeCreaIni ";
                    $objNativeQuery->setParameter("strFeCreaIni", $arrayParametros['strFeCreaIni']);
                }

                if (isset($arrayParametros['strFeCreaFin']) && !empty($arrayParametros['strFeCreaFin']))
                {
                    $strWhere .= " AND TO_CHAR(ERROR.FE_CREACION,'RRRR-MM-DD') <= :strFeCreaFin ";
                    $objNativeQuery->setParameter("strFeCreaFin", $arrayParametros['strFeCreaFin']);
                }

                $strSql = "SELECT ERROR.ID_ERROR ID_ERROR, "
                               . "ERROR.APLICACION APLICACION, "
                               . "ERROR.PROCESO PROCESO, "
                               . "ERROR.DETALLE_ERROR DETALLE_ERROR, "
                               . "ERROR.USR_CREACION USR_CREACION, "
                               . "TO_CHAR(ERROR.FE_CREACION,'RRRR-MM-DD HH24:MI:SS') FE_CREACION, "
                               . "ERROR.IP_CREACION IP_CREACION "
                        . "FROM DB_GENERAL.INFO_ERROR ERROR "
                    . "WHERE ERROR.ID_ERROR = ERROR.ID_ERROR "
                    . "$strWhere "
                    . "ORDER BY ID_ERROR DESC ";

                $objResultSetMap->addScalarResult('ID_ERROR'      ,'idError'      , 'integer');
                $objResultSetMap->addScalarResult('APLICACION'    ,'aplicacion'   , 'string');
                $objResultSetMap->addScalarResult('PROCESO'       ,'proceso'      , 'string');
                $objResultSetMap->addScalarResult('DETALLE_ERROR' ,'detalleError' , 'string');
                $objResultSetMap->addScalarResult('USR_CREACION'  ,'usrCreacion'  , 'string');
                $objResultSetMap->addScalarResult('FE_CREACION'   ,'feCreacion'   , 'string');
                $objResultSetMap->addScalarResult('IP_CREACION'   ,'ipCreacion'   , 'string');

                $objNativeQuery->setSQL($strSql);

                $arrayRespuesta["status"] = 'ok';
                $arrayRespuesta["result"] = $objNativeQuery->getResult();
            }
            catch (\Exception $objException)
            {
                $arrayResultado["status"]  = 'fail';
                $arrayResultado["message"] = $objException->getMessage();
            }

            return $arrayRespuesta;
        }
        
        /**
         * Método encargado de realizar la inserción de los errores de monitoreo en la INFO_ERROR de DB_FINANCIERO.
         *
         * @author Josselhin Moreria Q. <kjmoreria@telconet.ec>
         * @version 1.0 17-05-2019
         *
         * @param $arrayParametros [
         *                              strAplicacion      => Aplicación,
         *                              strProceso         => Proceso,
         *                              strDetalleError    => Detalle del error
         *                         ]
         * @return $arrayResultado
         */
        public function setInfoError($arrayParametros)
        {
            try 
            {
                $strAplicacion      = ( isset($arrayParametros['strAplicacion']) && !empty($arrayParametros['strAplicacion']) )
                                               ? $arrayParametros['strAplicacion'] : '';
                
                $strProceso         = ( isset($arrayParametros['strProceso']) && !empty($arrayParametros['strProceso']) )
                                               ? $arrayParametros['strProceso'] : '';
                
                $strDetalleError    = ( isset($arrayParametros['strDetalleError']) && !empty($arrayParametros['strDetalleError']) )
                                               ? $arrayParametros['strDetalleError'] : '';
                
                $strSql     = "BEGIN FNCK_TRANSACTION.INSERT_ERROR(:strAplicacion, 
                                                                   :strProceso,
                                                                   :strDetalleError); END;";
                
                $objStmt    = $this->_em->getConnection()->prepare($strSql);
                $objStmt->bindParam('strAplicacion',$strAplicacion);
                $objStmt->bindParam('strProceso',$strProceso);
                $objStmt->bindParam('strDetalleError',$strDetalleError );
                $objStmt->execute();
                
                $strMensje =  'OK';
                
                return $strMensje;
                 
            } catch (Exception $ex) 
            {
                $strError = 'Mensaje de errror'.$ex;
                return $strError;
            }
        }
    }
