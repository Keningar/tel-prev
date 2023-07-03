<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\comercialBundle\Service\InfoServicioService;

class InfoDetalleSolPlanifHistRepository extends EntityRepository
{


    public function generarJsonHistorialAsignadosSolInsp($arrayParametros)
    {
        $arrayEncontrados         = array();

        $arrayRegistrosRespuesta = $this->getRegistrosHistorialAsignadosSolInsp($arrayParametros);

        $intTotal                     =$arrayRegistrosRespuesta['total'] ? $arrayRegistrosRespuesta['total'] : 0;

error_log("intTotal =>>>".$intTotal);

        if (!empty($arrayRegistrosRespuesta) && $intTotal > 0)
        {
            $strJsonRespuesta   = trim(preg_replace('/\s+/', ' ', $arrayRegistrosRespuesta['objJsonRespuesta']));
error_log("stringJsonRespuesta=>>>");
error_log($strJsonRespuesta);
                $arrayRespuestaJson = json_decode($strJsonRespuesta);

error_log("COUNT arrayRespuestaJson =>>>".count($arrayRespuestaJson));
            $arrayRegistros = array();
            foreach($arrayRespuestaJson as $objDato)
            {
                $arrayRegistros[] =  (array) $objDato;
            }
        }

        if ($arrayRegistros)
        {
            //seteo el contador para los servicios ocultos
            $intNum = $intTotal;

            foreach ($arrayRegistros as $intKey=>$arrayData)
            {

                $arrayEncontrados[]=array(
                        'idSolPlanifHist' => $arrayData["idSolPlanifHist"],
                        'feCreacion'      => $arrayData["feCreacion"],
                        'usrCreacion'     => $arrayData["usrCreacion"],
                        'nombreAsignado'  => $arrayData["nombreAsignado"],
                        'observacion'     => $arrayData["observacion"],
                        'estado'          => $arrayData["estado"]

                    );
            }

            if($intNum == 0)
            {
                $strResultado= array('total' => 0 ,'encontrados' => array());
                $strResultado = json_encode( $strResultado);
                return $strResultado;
            }
            else
            {
                $objDataF =json_encode($arrayEncontrados);
                $strResultado= '{"total":"'.$intNum.'","encontrados":'.$objDataF.'}';
                return $strResultado;
            }
        }
        else
        {
            
            $strResultado= '{"total":"0","encontrados":[]}';
            return $strResultado;
        }
        
    }



    /**
     * Función que obtiene el historial de un asignado en una solicitud de inspección
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 18-01-2021
     * @return OCI_B_CURSOR
     */
    public function getRegistrosHistorialAsignadosSolInsp($arrayParametros)
    {
        $intTotal   = 0;
        $strStatus  = "";
        $strMessage = "";

        try
        {
            $strSql = "BEGIN DB_COMERCIAL.CMKG_SOLICITUD_CONSULTA.P_GET_HISTORIAL_SOL_INSPECCION(:Pcl_Json,".
                                                                                   ":Pcl_JsonRespuesta,".
                                                                                   ":Pn_Total,".
                                                                                   ":Pv_Status,".
                                                                                   ":Pv_Message); END;";

            $arrayOciCon               = $arrayParametros['ociCon'];
            $objRscCon                 = oci_connect($arrayOciCon['userComercial'], 
                                                     $arrayOciCon['passComercial'], 
                                                     $arrayOciCon['databaseDsn'],'AL32UTF8');
            $objStmt                   = oci_parse($objRscCon,$strSql);
            $arrayParametros['ociCon'] = null;
            $objClobJsonRespuesta      = oci_new_descriptor($objRscCon, OCI_D_LOB);
            oci_bind_by_name($objStmt,':Pcl_Json'   ,json_encode($arrayParametros,JSON_UNESCAPED_UNICODE));
            oci_bind_by_name($objStmt, ':Pcl_JsonRespuesta', $objClobJsonRespuesta, -1, OCI_B_CLOB);
            oci_bind_by_name($objStmt,':Pn_Total'   ,$intTotal,10);
            oci_bind_by_name($objStmt,':Pv_Status'  ,$strStatus,50);
            oci_bind_by_name($objStmt,':Pv_Message' ,$strMessage,4000);

            oci_execute($objStmt);

            $arrayRespuesta = array ('status'       => $strStatus,
                                     'message'      => $strMessage,
                                     'total'        => $intTotal,
                                     'objJsonRespuesta'=> $objClobJsonRespuesta->load()
                                    );
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'  => 'fail',
                                     'message' => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

}
