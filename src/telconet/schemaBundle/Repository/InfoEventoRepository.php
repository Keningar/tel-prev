<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class InfoEventoRepository extends EntityRepository
{     
    /**
    * 
    * getEventos
    * obtiene los eventos segun los parametros
    * 
    * costo = 4 
    * 
    * @param array $arrayParametros      
    * 
    * @return json $array
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 18-12-2017
    *
    * Mejoras en validacion y aumento de filtros  
    * @author Robinson Salgado <rsalgado@telconet.ec>
    * @version 1.1 03-04-2018
    *
    * Se agrega validación para la fecha de 12 horas y 15 días para cerrar eventos
    * @author Carlos Caguana <ccaguana@telconet.ec>
    * @version 1.2 02-02-2021
    */  
  
    
    public function getArrayEventos($arrayParametros)
    {

        $objRsm = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsm);
        
        $strWhere = "";
        
        if(isset($arrayParametros['intId']) && $arrayParametros['intId'] > 0)
        {
            $strWhere = " AND IE.ID_EVENTO = :id " ;            
            $objQuery->setParameter("id", $arrayParametros['intId']);
        }
        else
        {
            
            if(isset($arrayParametros['intCuadrillaId']) && $arrayParametros['intCuadrillaId']!= '')
            {
                $strWhere .= " AND IE.CUADRILLA_ID =  :cuadrillaId " ;
                $objQuery->setParameter("cuadrillaId", $arrayParametros['intCuadrillaId']);                
            }
            
            if(isset($arrayParametros['intDetalleId']) && $arrayParametros['intDetalleId'] != '')
            {
                $strWhere .= " AND IE.DETALLE_ID =  :detalleId " ;
                $objQuery->setParameter("detalleId", $arrayParametros['intDetalleId']);                
            }
            
            if(isset($arrayParametros['intPersonaEmpresaRolId']) && $arrayParametros['intPersonaEmpresaRolId'] != '')
            {
                $strWhere .= " AND IE.PERSONA_EMPRESA_ROL_ID =  :intPersonaEmpresaRolId " ;
                $objQuery->setParameter("intPersonaEmpresaRolId", $arrayParametros['intPersonaEmpresaRolId']);                
            }         

            if(isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']))
            {
                $strWhere .= " AND IE.ESTADO = :estado " ;
                $objQuery->setParameter("estado", $arrayParametros['strEstado']);
            }
            
            if(isset($arrayParametros['intTipoEventoId']) && $arrayParametros['intTipoEventoId'] != '')
            {
                $strWhere .= " AND IE.TIPO_EVENTO_ID = :intTipoEventoId " ;
                $objQuery->setParameter("intTipoEventoId", $arrayParametros['intTipoEventoId']);
            }
            
            if(isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']))
            {
                $strWhere .= " AND IE.USR_CREACION = :strUsrCreacion " ;
                $objQuery->setParameter("strUsrCreacion", $arrayParametros['strUsrCreacion']);
            }
            

            if(isset($arrayParametros['objUtilService']))
            {
                $serviceUtils=$arrayParametros['objUtilService'];
                $intValorDiasCierre= $serviceUtils->getAdminParametroDet('DIAS_VALIDACION_EVENTOS_CIERRE',15);
                $intValorHorasEventos= $serviceUtils->getAdminParametroDet('HORAS_VALIDACION_EVENTOS_ACTUALES',12);
            }

         
            if(isset($arrayParametros['boolRevision']) && $arrayParametros['boolRevision'] && isset($arrayParametros['objUtilService']))
            {
            
                $strFecha=$arrayParametros['fechaInicio']; 


                if(isset($arrayParametros['tipoFecha']) &&  $arrayParametros['tipoFecha']!=null &&  !empty($arrayParametros['tipoFecha']))
                {

                        $strFecha = str_replace("-", "/", $strFecha);
                        $strWhere .= " AND IE.FECHA_INICIO >= SYSDATE-$intValorDiasCierre  and  IE.FECHA_INICIO < '$strFecha' ";

                }else
                {
                $strWhere .= " AND IE.FECHA_INICIO >= SYSDATE-$intValorDiasCierre  and  IE.FECHA_INICIO < SYSDATE ";
                }

                }


             else if(isset($arrayParametros['boolToday']) && $arrayParametros['boolToday'] && isset($arrayParametros['objUtilService']))
            {
                $strWhere .= "AND IE.FECHA_INICIO >= SYSDATE-$intValorHorasEventos/24 ORDER BY IE.FECHA_INICIO DESC  " ;
            } 
            


        }        

        $strSql = "SELECT 
                    AC.NOMBRE_CUADRILLA,
                    ATE.NOMBRE NOMBRE_EVENTO,
                    (SELECT IP.NOMBRES || ' ' || IP.APELLIDOS 
                     FROM DB_COMERCIAL.INFO_PERSONA IP 
                     WHERE IP.ID_PERSONA = IPER.PERSONA_ID
                    ) NOMBRE_PERSONA,
                    IE.*
                    FROM DB_SOPORTE.INFO_EVENTO IE 
                    JOIN DB_SOPORTE.ADMI_TIPO_EVENTO ATE ON ATE.ID_TIPO_EVENTO = IE.TIPO_EVENTO_ID
                    LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER ON IPER.ID_PERSONA_ROL = IE.PERSONA_EMPRESA_ROL_ID
                    LEFT JOIN DB_COMERCIAL.ADMI_CUADRILLA AC ON IE.CUADRILLA_ID = AC.ID_CUADRILLA 
                     WHERE 1=1 ".$strWhere;

        $objRsm->addScalarResult('NOMBRE_CUADRILLA', 'nombreCuadrilla', 'string');
        $objRsm->addScalarResult('NOMBRE_EVENTO', 'nombreEvento', 'string');
        $objRsm->addScalarResult('NOMBRE_PERSONA', 'nombrePersona', 'string');
        $objRsm->addScalarResult('ID_EVENTO', 'id', 'integer');
        $objRsm->addScalarResult('CUADRILLA_ID', 'cuadrillaId', 'integer');
        $objRsm->addScalarResult('TIPO_EVENTO_ID', 'tipoEventoId', 'integer');
        $objRsm->addScalarResult('DETALLE_ID', 'detalleId', 'integer');
        $objRsm->addScalarResult('FECHA_INICIO', 'fechaInicio', 'string');
        $objRsm->addScalarResult('FECHA_FIN', 'fechaFin', 'string');
        $objRsm->addScalarResult('VALOR_TIEMPO', 'valorTiempo', 'integer');
        $objRsm->addScalarResult('VALOR_TIEMPO_PAUSA', 'valorTiempoPausa', 'integer');
        $objRsm->addScalarResult('PERSONA_EMPRESA_ROL_ID', 'personaEmpresaRolId', 'integer');
        $objRsm->addScalarResult('ACCION', 'accion', 'string');
        $objRsm->addScalarResult('ESTADO', 'estado', 'string');
        
        $objQuery->setSQL($strSql);

        return $objQuery->getResult();

    }   
   


	/**
     * Función que obtiene el resumen de eventos por persona
     * 
     * @author: Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 19-12-2018  
     * 
	 * Costo=796
	 *
     * @param array $arrayParametros
     * @return string $strResultado
     */   
	public function getResumenEventosPersona($arrayRequest)
    {        
        $objRsmb  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);
		
		$objReturnResponse 			    = [];
		$objReturnResponse['registros'] = [];
		$objReturnResponse['total'] 	= 0;

        $objRsmb->addScalarResult('ID_PERSONA', 'intIdPersona','integer');
		$objRsmb->addScalarResult('TIPO_EVENTO_ID', 'intTipoEventoId','integer');
        $objRsmb->addScalarResult('NOMBRE_EVENTO', 'strNombreEvento','string');
		$objRsmb->addScalarResult('CODIGO_EVENTO', 'strCodigoEvento','string');
		$objRsmb->addScalarResult('TIEMPO', 'intTiempo','float');
		$objRsmb->addScalarResult('HORAS', 'intHoras','integer');
        $objRsmb->addScalarResult('MINUTOS', 'intMinutos','integer');
			
        $strSQL = "SELECT "
                        . "    T1.ID_PERSONA, "
						. "    T1.TIPO_EVENTO_ID, "
						. "    T1.NOMBRE_EVENTO, "
						. "    T1.CODIGO_EVENTO, "
						. "    ROUND(T1.HORAS,2) AS TIEMPO, "
						. "    FLOOR(ABS(T1.HORAS)) AS HORAS, "
						. "    ROUND((ABS(T1.HORAS)-FLOOR(ABS(T1.HORAS)))*60,0) AS MINUTOS "
                        . "   FROM "
                        . "     (SELECT "
                        . "   		IP.ID_PERSONA, "
						. "   		ATE.NOMBRE NOMBRE_EVENTO, "
						. "   		ATE.CODIGO CODIGO_EVENTO, "
    					. "   		IE.TIPO_EVENTO_ID, "
    					. "   		ROUND(SUM(extract(hour from (NVL(IE.FECHA_FIN, SYSDATE) - IE.FECHA_INICIO))) + "
    					. "   		(SUM(extract(minute from (NVL(IE.FECHA_FIN, SYSDATE) - IE.FECHA_INICIO)))/60),5) AS HORAS "
    					. "   FROM DB_SOPORTE.INFO_EVENTO IE "
    					. "   JOIN DB_SOPORTE.ADMI_TIPO_EVENTO ATE ON ATE.ID_TIPO_EVENTO = IE.TIPO_EVENTO_ID "
						. "   LEFT JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL IPER ON IPER.ID_PERSONA_ROL = IE.PERSONA_EMPRESA_ROL_ID "
						. "   LEFT JOIN DB_COMERCIAL.INFO_PERSONA IP ON IP.ID_PERSONA = IPER.PERSONA_ID "						
                        . " WHERE 1=1 ";
		
		if(isset($arrayRequest['intPersonaId']) && !empty($arrayRequest['intPersonaId']))
		{
			$strSQL .= ' AND IP.ID_PERSONA = :intPersonaId ';
			$objQuery->setParameter('intPersonaId', $arrayRequest['intPersonaId']);
		}
		
		if(isset($arrayRequest['strFechaInicio']) && !empty($arrayRequest['strFechaInicio']) && 
		   isset($arrayRequest['strFechaFin']) && !empty($arrayRequest['strFechaFin']))
		{
            if(isset($arrayRequest['objUtilService']))
            {
                $serviceUtils=$arrayRequest['objUtilService'];
                $intValorDiasCierre= $serviceUtils->getAdminParametroDet('DIAS_VALIDACION_EVENTOS_CIERRE',15);
                $intValorHorasEventos= $serviceUtils->getAdminParametroDet('HORAS_VALIDACION_EVENTOS_ACTUALES',12);
                $strSQL .= "AND IE.FECHA_INICIO >= SYSDATE-$intValorHorasEventos/24 " ;
            }else
            {
        $strSQL .= ' AND TRUNC(IE.FECHA_INICIO)  BETWEEN TO_DATE(:strFechaInicio, :strFormatoFecha) AND TO_DATE(:strFechaFin, :strFormatoFecha) ';
    
            }
                $objQuery->setParameter('strFormatoFecha', 'yyyy-mm-dd');
			$objQuery->setParameter('strFechaInicio', $arrayRequest['strFechaInicio']);
			$objQuery->setParameter('strFechaFin', $arrayRequest['strFechaFin']);
		}
		
		$strSQL .= ' GROUP BY ATE.NOMBRE, ATE.CODIGO, IP.ID_PERSONA,IE.TIPO_EVENTO_ID ';
		$strSQL .= ' ) T1 ';
		
		$objQuery->setSQL($strSQL);
		$intTotal   = count($objQuery->getResult());
		if(isset($arrayRequest['intStart']) && isset($arrayRequest['intLimit']) && $arrayRequest['intLimit'] > 0)
		{
			$objQuery->setParameter('intStart', intval($arrayRequest['intStart']));
			$objQuery->setParameter('intLimit', intval($arrayRequest['intLimit']));
			$strSQL .= ' LIMIT :intStart, :intLimit ';
		}
		$objQuery->setSQL($strSQL);
		$objReturnResponse['registros'] = $objQuery->getResult();
		$objReturnResponse['total'] 	= $intTotal;

        return $objReturnResponse;
    }
    /**
     * Funcion que sirve para ejecutar un query que obtiene los eventos según parámetros enviados
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 28-08-2020
     * @param int $arrayParametros => [ intIdCuadrilla => id de la cuadrilla]
     * @return array $arrayDatos
     */
	public function getDetalleEventos($arrayRequest)
    {
        $objRsmb  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);
		
		$objReturnResponse 			    = [];
        $objRsmb->addScalarResult('ID_EVENTO', 'intIdEvento','integer');
        $objRsmb->addScalarResult('CUADRILLA_ID', 'intCuadrillaId','integer');
        $objRsmb->addScalarResult('TIPO_EVENTO_ID', 'intTipoEventoId','integer');
        $objRsmb->addScalarResult('CODIGO', 'strCodigoEvento','string');
        $objRsmb->addScalarResult('DETALLE_ID', 'intDetalleId','integer');
        $objRsmb->addScalarResult('FECHA_INICIO', 'strFechaInicio','string');
        $objRsmb->addScalarResult('FECHA_FIN', 'strFechaFin','string');
        $objRsmb->addScalarResult('VALOR_TIEMPO', 'intValorTiempo','integer');
        $objRsmb->addScalarResult('NOMBRE', 'strNombreEvento','string');
        $objRsmb->addScalarResult('PUBLISH_ID', 'strSerieLogica','string');
        $objRsmb->addScalarResult('PERSONA_EMPRESA_ROL_ID', 'intPersonaEmpresaRolId','integer');
        $objRsmb->addScalarResult('OBSERVACION', 'strObservacion','string');
        $objRsmb->addScalarResult('ESTADO', 'strEstado','string');
        $objRsmb->addScalarResult('USR_CREACION', 'strUsrCreacion','string');
        $objRsmb->addScalarResult('FE_CREACION', 'strFeCreacion','string');
        $objRsmb->addScalarResult('IP_CREACION', 'strIpCreacion','string');
        $objRsmb->addScalarResult('USR_ULT_MOD', 'strUsrUltMod','string');
        $objRsmb->addScalarResult('FE_ULT_MOD', 'strFeUltMod','string');
        $objRsmb->addScalarResult('IP_ULT_MOD', 'strIpUltMod','string');
        $objRsmb->addScalarResult('VERSION', 'strVersion','string');

        $strSQL = "SELECT " 
        ." evento.ID_EVENTO,"
        ." evento.CUADRILLA_ID,"
        ." evento.TIPO_EVENTO_ID,"
        ." tipo.CODIGO,"
        ." evento.DETALLE_ID,"
        ." evento.FECHA_INICIO,"
        ." evento.FECHA_FIN,"
        ." evento.TIPO_EVENTO_ID,"
        ." tipo.NOMBRE,"
        ." evento.FECHA_INICIO,"
        ." evento.FECHA_FIN,"
        ." evento.VALOR_TIEMPO,"
        ." evento.OBSERVACION,"
        ." evento.PERSONA_EMPRESA_ROL_ID,"
        ." evento.DETALLE_ID, "
        ." evento.PUBLISH_ID, "
        ." evento.ESTADO, "
        ." evento.USR_CREACION, "
        ." evento.USR_ULT_MOD, "
        ." evento.FE_CREACION, "
        ." evento.FE_ULT_MOD, "
        ." evento.IP_CREACION, "
        ." evento.IP_ULT_MOD, "
        ." evento.VERSION "
        ." FROM "
        ."   DB_SOPORTE.INFO_EVENTO evento "
        ."   JOIN DB_SOPORTE.ADMI_TIPO_EVENTO tipo ON tipo.ID_TIPO_EVENTO = evento.TIPO_EVENTO_ID "
        ." WHERE evento.ESTADO = :strEstado " ;

		if(isset($arrayRequest['intEventoId']) && !empty($arrayRequest['intEventoId']))
		{
			$strSQL .= ' AND evento.ID_EVENTO >= :intEventoId ';
			$objQuery->setParameter('intEventoId', $arrayRequest['intEventoId']);
		}
		if(isset($arrayRequest['intCuadrillaId']) && !empty($arrayRequest['intCuadrillaId']))
		{
            $strSQL .= ' AND evento.ID_EVENTO = '
                       .'(SELECT MAX(EVE.ID_EVENTO) FROM DB_SOPORTE.INFO_EVENTO EVE '
                       .' WHERE EVE.ESTADO= :strEstadoEveCua AND EVE.CUADRILLA_ID = :intCuadrillaId )';
            $objQuery->setParameter('intCuadrillaId', $arrayRequest['intCuadrillaId']);
            $objQuery->setParameter('strEstadoEveCua', 'Activo');
        }
		if(isset($arrayRequest['strFeCreacion']) && !empty($arrayRequest['strFeCreacion']))
		{
			$strSQL .= ' AND evento.FE_CREACION >= :strFeCreacion ';
			$objQuery->setParameter('strFeCreacion', $arrayRequest['strFeCreacion']);
        }
		if(isset($arrayRequest['strFeUltMod']) && !empty($arrayRequest['strFeUltMod']))
		{
			$strSQL .= ' AND evento.FE_ULT_MOD >= :strFeUltMod ';
			$objQuery->setParameter('strFeUltMod', $arrayRequest['strFeUltMod']);
        }
		if(isset($arrayRequest['strFechaFin']) && !empty($arrayRequest['strFechaFin']))
		{
			$strSQL .= ' AND evento.FECHA_FIN >= :strFechaFin ';
			$objQuery->setParameter('strFechaFin', $arrayRequest['strFechaFin']);
        }

        $objQuery->setParameter('strEstado', 'Activo');

		$strSQL .= ' ORDER BY evento.FE_CREACION DESC ';

		$objQuery->setSQL($strSQL);
        $objReturnResponse = $objQuery->getResult();

        return $objReturnResponse;
    }

    /**
     * Funcion que retorna el último evento activo
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 28-08-2020
     * @return array objReturnResponse
     */
	public function getUltimoEvento()
    {
        $objRsmb  = new ResultSetMappingBuilder($this->_em);
        $objQuery = $this->_em->createNativeQuery(null, $objRsmb);
		
		$objReturnResponse 			    = [];
        $objRsmb->addScalarResult('ID_EVENTO', 'intIdEvento','integer');

        $strSQL = "SELECT MAX(evento.ID_EVENTO) ID_EVENTO FROM  DB_SOPORTE.INFO_EVENTO evento WHERE ESTADO = :strEstado " ;
        $objQuery->setParameter('strEstado', 'Activo');

		$objQuery->setSQL($strSQL);
        $objReturnResponse = $objQuery->getResult();

        return $objReturnResponse;
    }
}
