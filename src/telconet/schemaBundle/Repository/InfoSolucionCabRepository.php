<?php

    namespace telconet\schemaBundle\Repository;

    use Doctrine\ORM\EntityRepository;

    class InfoSolucionCabRepository extends EntityRepository
    {

        /**
         * Método encargado de realizar el reporte de tareas.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 - 30-05-2019
         *
         * @param  Array $arrayParametros => Se convierte en Json y se envía al procedimiento.
         * @return Array $arrayRespuesta
        */
       public function listarDetalleSolucion($arrayParametros)
       {
           $strStatus  = "";
           $strMessage = "";

           try
           {
               $strSql = "BEGIN DB_COMERCIAL.CMKG_SOLUCIONES_CONSULTA.P_OBTENER_DETALLE_SOLUCION(:Pcl_Request,".
                                                                                                ":Pv_Status,".
                                                                                                ":Pv_Mensaje,".
                                                                                                ":Pcl_Response); END;";

               $arrayOciCon  = $arrayParametros['ociCon'];
               $objRscCon    = oci_connect($arrayOciCon['userCom'], $arrayOciCon['passCom'], $arrayOciCon['databaseDsn'],'AL32UTF8');
               $objCsrResult = oci_new_cursor($objRscCon);
               $objStmt      = oci_parse($objRscCon,$strSql);

               oci_bind_by_name($objStmt,':Pcl_Request'  , json_encode($arrayParametros['arrayRequest']));
               oci_bind_by_name($objStmt,':Pv_Status'    , $strStatus,50);
               oci_bind_by_name($objStmt,':Pv_Mensaje'   , $strMessage,4000);
               oci_bind_by_name($objStmt,':Pcl_Response' , $objCsrResult,-1,OCI_B_CURSOR);

               oci_execute($objStmt);
               oci_execute($objCsrResult);

               $arrayRespuesta = array ('status'       => $strStatus,
                                        'message'      => $strMessage,
                                        'objCsrResult' => $objCsrResult);
           }
           catch (\Exception $objException)
           {
               $arrayRespuesta = array ('status'  => 'ERROR',
                                        'message' => $objException->getMessage());
           }
           return $arrayRespuesta;
       }
    }
