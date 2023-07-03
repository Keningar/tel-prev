<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use telconet\comercialBundle\Service\InfoServicioService;

class InfoDetalleSolPlanifRepository extends EntityRepository
{





    public function generarJsonAsignadosSolInspeccion($arrayParametros)
    {
        $arrayEncontrados         = array();

        $arrayRegistrosRespuesta = $this->getRegistrosAsignadosSolInspeccion($arrayParametros);

        $intTotal                     =$arrayRegistrosRespuesta['total'] ? $arrayRegistrosRespuesta['total'] : 0;


        if (!empty($arrayRegistrosRespuesta) && $intTotal > 0)
        {
            $strJsonRespuesta   = trim(preg_replace('/\s+/', ' ', $arrayRegistrosRespuesta['objJsonRespuesta']));

                $arrayRespuestaJson = json_decode($strJsonRespuesta);

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
                        'idSol'          => $arrayData["idSol"],
                        'idSolPlanif'    => $arrayData["idSolPlanif"],
                        'idAsignado'     => $arrayData["idAsignado"],
                        'nombreAsignado' => $arrayData["nombreAsignado"],
                        'estado'         => $arrayData["estado"],
                        'observacion'    => $arrayData["observacion"],
                        'estadoTarea'    => $arrayData["estadoTarea"],
                        'numeroTarea'    => $arrayData["numeroTarea"],
                        'fechaInicio'    => $arrayData["feInicio"],
                        'fechaFin'       => $arrayData["feFin"],
                        'origen'         => $arrayData["origen"],
                        'tipoAsignado'   => $arrayData["tipoAsignado"]

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






    public function getRegistrosAsignadosSolInspeccion($arrayParametros)
    {
        $intTotal   = 0;
        $strStatus  = "";
        $strMessage = "";

        try
        {
            $strSql = "BEGIN DB_COMERCIAL.CMKG_SOLICITUD_CONSULTA.P_GET_ASIGNADOS_SOL_INSPECCION(:Pcl_Json,".
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

            $objRespuesta = null;

            if ($intTotal > 0)
            {
                $objRespuesta = $objClobJsonRespuesta->load();
            }

            $arrayRespuesta = array ('status'       => $strStatus,
                                     'message'      => $strMessage,
                                     'total'        => $intTotal,
                                     'objJsonRespuesta'=> $objRespuesta
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
