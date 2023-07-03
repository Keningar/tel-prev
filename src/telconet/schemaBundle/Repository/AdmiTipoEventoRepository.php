<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiTipoEventoRepository extends EntityRepository
{
    
    
    /**
    * 
    * getTipoEvento
    * obtiene el tipo de evento
    * cost 3
    * 
    * @param array $arrayParametros      
    * 
    * @return json $array
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 18-12-2017
     *
     * Se cambio la validacion de los parámetros y se aumento el campo codigo
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.1 24-03-2018
	*/  
    
    public function getArrayTipoEvento($arrayParametros)
    {

        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $strWhere = "";
        
        if(isset($arrayParametros['intId']) && $arrayParametros['intId'] > 0)
        {
            $strWhere = " AND TE.ID_TIPO_EVENTO = :id " ;
            
            $objQuery->setParameter("id", $arrayParametros['intId']);
        }
        else
        {
            
            if(isset($arrayParametros['strNombre']) && !empty($arrayParametros['strNombre']))
            {
                $strWhere .= " AND UPPER(TE.NOMBRE) like :nombre " ;
                $objQuery->setParameter("nombre", '%'.strtoupper($arrayParametros['strNombre'].'%'));
                
            }

            if(isset($arrayParametros['strCodigo']) && !empty($arrayParametros['strCodigo']))
            {
                $strWhere .= " AND TE.CODIGO = :codigo " ;
                $objQuery->setParameter("codigo", $arrayParametros['strCodigo']);

            }

            if(isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']))
            {
                $strWhere .= " AND TE.ESTADO = :estado " ;

                $objQuery->setParameter("estado", $arrayParametros['strEstado']);
            }
            
        }        

        $strSql = "SELECT TE.ID_TIPO_EVENTO,
                          TE.CODIGO,
                          TE.NOMBRE,
                          TE.ESTADO
                     FROM ADMI_TIPO_EVENTO TE
                    WHERE 1 = 1 ".$strWhere;

        $objRsm->addScalarResult('ID_TIPO_EVENTO', 'id', 'integer');
        $objRsm->addScalarResult('CODIGO', 'codigo', 'string');
        $objRsm->addScalarResult('NOMBRE', 'nombre', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        
        $objQuery->setSQL($strSql);

        return $objQuery->getResult();

    }        
    
    
    
    /**
    * 
    * getTipoEvento
    * obtiene los eventos segun el usuario y la fecha actual
    * cost 9
    * 
    * @param array $arrayParametros      
    * 
    * @return json $array
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 18-12-2017
    * 
    * Se aumentaron campos y se validaron tipos de datos y nulls.
    * @author Wilmer Vera <wvera@telconet.ec>
    * @version 1.1 18-12-2017
    * 
    * Se añadio filtro por usuario si el id de la cuadrilla es cero
    * @author Robinson Salgado <rsalgado@telconet.ec>
    * @version 1.2 02-04-2018
    * 
    * se añadio el ultimo usuario asignado y ultimo estado tarea
    * @author Nestor Naula <nnaulal@telconet.ec>
    * @version 1.3 05-04-2018
    * 
    * Se añadio la comparacion entre la fecha de inicio de jornada y la fecha actual
    * @author Nestor Naula <nnaulal@telconet.ec>
    * @version 1.4 05-07-2018
    * Se modifica filtro para que traiga los eventos de 12 horas atras
    * @author Carlos Caguana <ccaguana@telconet.ec>
    * @version 1.5 02-02-2021
    */  
    
    public function getArrayEventosUser($arrayParametros)
    {

        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $strWhere = "";       
            
        if(isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']))
        {
            $strWhere .= " AND e.ESTADO  = :estado ";
            $objQuery->setParameter("estado", $arrayParametros['strEstado']);
        }
        
        if(isset($arrayParametros['intCuadrilla']) && $arrayParametros['intCuadrilla'] == '0' && 
           isset($arrayParametros['strUser']) && !empty($arrayParametros['strUser']))
        {
            $strWhere .= " AND E.USR_CREACION  = :strUser ";
            $objQuery->setParameter("strUser", $arrayParametros['strUser']);
        }

        $serviceUtils=$arrayParametros['objUtilService'];
        $intValorHorasEventos= $serviceUtils->getAdminParametroDet('HORAS_VALIDACION_EVENTOS_ACTUALES',12);

        $strSql = "SELECT E.OBSERVACION, 
                          NVL(E.DETALLE_ID,0) AS DETALLE_ID, 
                          E.ID_EVENTO,
                          NVL(E.VALOR_TIEMPO,0) AS VALOR_TIEMPO, 
                          E.FECHA_INICIO, 
                          E.FECHA_FIN, 
                          E.USR_CREACION,
                          E.CUADRILLA_ID,
                          TE.NOMBRE,
                          TE.CODIGO,
                          (SELECT EMPRESA_COD
                             FROM INFO_COMUNICACION
                            WHERE ID_COMUNICACION =
                             (SELECT MIN(CO.ID_COMUNICACION)
                                FROM INFO_COMUNICACION CO
                               WHERE CO.DETALLE_ID = e.DETALLE_ID
                             )) AS EMPRESA_ID,
                            (SELECT info.ESTADO
                             FROM INFO_DETALLE_HISTORIAL info
                             WHERE info.ID_DETALLE_HISTORIAL
                             =(SELECT MAX(CO.ID_DETALLE_HISTORIAL)
                             FROM INFO_DETALLE_HISTORIAL CO
                             WHERE CO.DETALLE_ID = e.DETALLE_ID 
                            ))AS ESTADO_ID,
                            (SELECT persona.LOGIN
                            FROM INFO_DETALLE_ASIGNACION asignacion
                            INNER JOIN INFO_PERSONA persona 
                            ON persona.ID_PERSONA=asignacion.REF_ASIGNADO_ID
                            WHERE asignacion.ID_DETALLE_ASIGNACION =(
                            SELECT MAX(ID_DETALLE_ASIGNACION)
                            FROM INFO_DETALLE_ASIGNACION CO
                            WHERE CO.DETALLE_ID = e.DETALLE_ID 
                             ))AS USR_CREACION,
                            extract( day FROM (NVL(e.FECHA_FIN,:idFecha) - e.FECHA_INICIO)*24*60*60) TIEMPO_TRASCURRIDO,
                            TRUNC(e.FECHA_INICIO)- TRUNC(SYSDATE) AS DIAS_TRASNSCURRIDO
                     FROM INFO_EVENTO e,
                          ADMI_TIPO_EVENTO TE
                    WHERE e.TIPO_EVENTO_ID = TE.ID_TIPO_EVENTO
                    AND e.FECHA_INICIO >= SYSDATE-$intValorHorasEventos/24 
                      AND e.CUADRILLA_ID = :idCuadrilla
                      ".$strWhere."
                    ORDER BY e.FECHA_INICIO ASC ";
         
        $objQuery->setParameter("idCuadrilla", $arrayParametros['intCuadrilla']);
        $strFecha=new \DateTime('now');
        $arrayFecha= $strFecha->format("Y-m-d  H:i:s");
        $objQuery->setParameter("idFecha", $arrayFecha);
        $objRsm->addScalarResult('ID_EVENTO', 'idEvento', 'integer');
        $objRsm->addScalarResult('USR_CREACION', 'userCreacion', 'string');
        $objRsm->addScalarResult('CUADRILLA_ID', 'cuadrillaId', 'integer');
        $objRsm->addScalarResult('OBSERVACION', 'observacion', 'string');
        $objRsm->addScalarResult('DETALLE_ID', 'detalleId', 'integer');
        $objRsm->addScalarResult('VALOR_TIEMPO', 'valorTiempo', 'integer');
        $objRsm->addScalarResult('FECHA_INICIO', 'fechaInicio', 'string');
        $objRsm->addScalarResult('FECHA_FIN', 'fechaFin', 'string');
        $objRsm->addScalarResult('NOMBRE', 'nombre', 'string');
        $objRsm->addScalarResult('CODIGO', 'codigo', 'string');
        $objRsm->addScalarResult('EMPRESA_ID', 'idEmpresa', 'string');
        $objRsm->addScalarResult('TIEMPO_TRASCURRIDO', 'tiempoTranscurrido', 'string');
        $objRsm->addScalarResult('ESTADO_ID', 'estado', 'string');
        $objRsm->addScalarResult('USR_CREACION', 'usrCreacion', 'string');
        $objRsm->addScalarResult('DIAS_TRASNSCURRIDO', 'diasTranscurrido', 'integer');

        $objQuery->setSQL($strSql);

        return $objQuery->getResult();

    }    

}
