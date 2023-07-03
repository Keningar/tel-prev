<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoAgendaCupoCabRepository extends EntityRepository
{

    /**
    * generarJson
    *
    * Esta funcion retorna en formato JSON la lista de registros de planifición según las plantillas configuradas.
    *
    * @author Juan Romero <jromero@telconet.ec>
    * @version 1.0 06-06-2018
    *
    * @param array  $arrayParametros
    *
    * @return array $resultado
    *
    */
    public function generarJsonAgendaCupo($arrayParametros)
    {
        try
        {
        $arrayRegistros = $this->getRegistros($arrayParametros);
        $objRegistros       = $arrayRegistros['registros'];
        $intRegistrosTotal  = $arrayRegistros['total'];

        if ($intRegistrosTotal)
        {
            $intTotal = count($intRegistrosTotal);
            $arrayFechaLimite = new \DateTime('now');
            $arrayFechaLimite->add(new \DateInterval('P1D'));
            //echo $fecha->format('Y-m-d')          
            foreach ($objRegistros as $objRegistro)
            {

                $arrayFechaPeriodo = new \DateTime(strval(date_format($objRegistro->getFechaPeriodo(),"Y-m-d")));
                $arrayEncontrados[] =array('idAgendaCupos' => $objRegistro->getId(),
                                           'empresaCod'    => trim($objRegistro->getEmpresaCod()),
                                           'fechaPeriodo'  => strval(date_format($objRegistro->getFechaPeriodo(),"d/m/Y")),
                                           'totalCupos'    => trim($objRegistro->getTotalCupos()),
                                           'observacion'    => trim($objRegistro->getObservacion()),
                                           'feCreacion'            => strval(date_format($objRegistro->getFeCreacion(),"d/m/Y G:i")) ,
                                           'usrCreacion'           => trim($objRegistro->getUsrCreacion()),
                                           'nombreJurisdiccion'    => trim($objRegistro->getJurisdiccionId()->getNombreJurisdiccion()),
                                           'nombrePlantilla'       => trim($objRegistro->getPlantillaHorarioId()->getDescripcion()),
                                           'estado'                => (strtolower(trim($objRegistro->getEstadoRegistro()))==strtolower('ELIMINADO') ?
                                                                                     'Eliminado':'Activo'),
                                           'action1'               => 'button-grid-show',
                                           'action2'               => ( $arrayFechaPeriodo < $arrayFechaLimite ?
                                                                                     'icon-invisible':'button-grid-edit'),
                                           'action3'               => (strtolower(trim($objRegistro->getEstadoRegistro()))==strtolower('ELIMINADO') ?
                                                                                     'icon-invisible':'button-grid-delete'));
                
                
            }

            if($intTotal == 0)
            {
                $arrayResultado = array('total' => 1 ,'encontrados' => array('idPlantillaHorarioCab' => 0 , 'empresaCod' => '',
                                                                        'descripcion' => 'Ninguno', 'esDefault'     => "N" , 'estado' => 'Ninguno'));
                $arrayResultado = json_encode( $arrayResultado );
                return $arrayResultado;
            }
            else
            {
                $arrayFinal     = json_encode($arrayEncontrados);
                $arrayResultado = '{"total":"'.$intTotal.'","encontrados":'.$arrayFinal.'}';
                return $arrayResultado;
            }
        }
        else
        {
            $arrayResultado = '{"total":"0","encontrados":[]}';
            return $arrayResultado;
        }            
        }
        catch (\Exception $ex)
        {
            error_log("Error: " . $ex->getMessage());
        } 
    }      

    public function getRegistros($arrayParametros)
    {
        try
        {
        $arrayDatos  = array();
        $strIdAgendaCupos      = $arrayParametros["idAgendaCupos"];		
        $strTotalCupos         = $arrayParametros["totalCupos"];
	$strObservacion        = $arrayParametros["observacion"];
        $arrayFeCreacion       = $arrayParametros["feCreacion"];
	$strIpCreacion         = $arrayParametros["ipCreacion"];
	$intJurisdiccionId     = $arrayParametros["intJurisdiccionId"];
	$strPlantillaHorarioId = $arrayParametros["plantillaHorarioId"];
        $arrayFeModifica       = $arrayParametros["feModifica"];
	$strUsrModifica        = $arrayParametros["usrModifica"];        
	$strEstadoRegistro     = $arrayParametros["estadoRegistro"];
	$strIntStart           = $arrayParametros["intStart"];
	$strIntLimit           = $arrayParametros["intLimit"];
        $strFechaDesde         = $arrayParametros["strFechaDesde"];
        $strFechaHasta         = $arrayParametros["strFechaHasta"];
        $strDescripcion        = $arrayParametros["strDescripcion"];
        
       
        $strSql = "SELECT
                   pho
                   FROM
                   schemaBundle:InfoAgendaCupoCab pho
                   WHERE 1 = 1";
        
        $objQuery = $this->_em->createQuery(null);
        

        if ($strEstadoRegistro && $strEstadoRegistro!="Todos")
        {
            if ($strEstadoRegistro=="Activo")
            {
                $strSql .= " lower(pho.estado) not like lower(:estado) AND";
                $objQuery->setParameter('estado','Inactivo');
            }
            else
            {
                $strSql .= " lower(pho.estado) like lower(:estado) AND";
                $objQuery->setParameter('estado','%'.$strEstado.'%');
            }
        }
        if($strIdAgendaCupos && $strIdAgendaCupos!="")
        {
            $strSql .= " pho.idAgendaCupos = :idAgendaCupos ";
            $objQuery->setParameter('idAgendaCupos',$strIdAgendaCupos);
        }

        if($strFechaDesde && $strFechaDesde!="")
        {
            $arrayDateF = explode("-",$strFechaDesde);
            $strFechaSql = date("Y/m/d", strtotime($arrayDateF[2]."-".$arrayDateF[1]."-".$arrayDateF[0]));
            
            $strSql .= " AND pho.fechaPeriodo >=:fechaDesde ";
            $objQuery->setParameter('fechaDesde',trim($strFechaSql));
        }
        if($strFechaHasta && $strFechaHasta!="")
        {
            $arrayDateF = explode("-",$strFechaHasta);
            $strFechaSql = date("Y/m/d", strtotime($arrayDateF[2]."-".$arrayDateF[1]."-".$arrayDateF[0]));
            
            $strSql .= " AND pho.fechaPeriodo <=:fechaHasta ";
            $objQuery->setParameter('fechaHasta',trim($strFechaSql));
        }
        if($strDescripcion && $strDescripcion!="")
        {
            $strSql .= " AND pho.plantillaHorarioId.descripcion like :descripcion ";
            $objQuery->setParameter('descripcion','%'.$strDescripcion.'%');
        }
        

        if($strTotalCupos && $strTotalCupos!="")
        {
            $strSql .= " AND pho.totalCupos = :totalCupos ";
            $objQuery->setParameter('totalCupos',$strTotalCupos);
        }
        
        if($strObservacion && $strObservacion!="")
        {
            $strSql .= " AND pho.observacion = :observacion ";
            $objQuery->setParameter('observacion',$strObservacion);
        }
		
	if($arrayFeCreacion && $arrayFeCreacion!="")
        {
            $strSql .= " AND pho.feCreacion = :feCreacion ";
            $objQuery->setParameter('feCreacion',$arrayFeCreacion);
        }
		
	if($strIpCreacion && $strIpCreacion!="")
        {
            $strSql .= " AND pho.ipCreacion = :ipCreacion ";
            $objQuery->setParameter('ipCreacion',$strIpCreacion);
        }
		
	if($intJurisdiccionId && $intJurisdiccionId!="")
        {
            $strSql .= " AND pho.jurisdiccionId = :jurisdiccionId ";
            $objQuery->setParameter('jurisdiccionId',$intJurisdiccionId);
        }
		
        if($strPlantillaHorarioId && $strPlantillaHorarioId!="")
        {
            $strSql .= " AND pho.plantillaHorarioId = :plantillaHorarioId ";
            $objQuery->setParameter('plantillaHorarioId',$strPlantillaHorarioId);
        }
		
	if($arrayFeModifica && $arrayFeModifica!="")
        {
            $strSql .= " AND pho.feModifica = :feModifica ";
            $objQuery->setParameter('feModifica',$arrayFeModifica);
        }
		
	if($strUsrModifica && $strUsrModifica!="")
        {
            $strSql .= " AND pho.usrModifica = :usrModifica ";
            $objQuery->setParameter('usrModifica',$strUsrModifica);
        }

        $strSql .= " order by pho.feCreacion DESC";
        error_log("sql: " . $strSql);

        $objQuery->setDQL($strSql);

        $intRegistros = $objQuery->getResult();

        $arrayDatos['registros'] = $objQuery->setFirstResult($strIntStart)->setMaxResults($strIntLimit)->getResult();
        $arrayDatos['total']     = $intRegistros;   
        }        
        catch (\Exception $ex)
        {
            error_log("Error: " . $ex->getMessage());
        } 
        return $arrayDatos;

    }      

    /**
     * @param array $arrayParametros
     * @return array $arrayRespuesta
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 08-06-2018 - Genera los cupos acorde a los parametros enviados
     */
    public function generaCuposPorPeriodo($arrayParametros)
    {
        $strFechaDesde     = $arrayParametros['strFechaDesde'];
        $strFechaHasta     = $arrayParametros['strFechaHasta'];
        $intAgendaId       = $arrayParametros['intAgendaId'];
        $intJurisdiccionId = $arrayParametros['intJurisdiccionId'];
        
        // se debe enviar un string con suficiente espacio para la respuesta
        $strMensaje = str_repeat(' ', 100);
        $strMensaje2 = str_repeat(' ', 100);
        $strSql     = "BEGIN DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS.P_SET_CUPOS_CUADRILLAS(" 
                    . " :fechaDesde,"
                    . " :fechaHasta, :jurisdiccionId, :agendaId, :lnError, :lvError); END;";
        $strStmt    = $this->_em->getConnection()->prepare($strSql);
        $strStmt->bindParam('fechaDesde', $strFechaDesde);
        $strStmt->bindParam('fechaHasta', $strFechaHasta);
        $strStmt->bindParam('jurisdiccionId', $intJurisdiccionId);
        $strStmt->bindParam('agendaId', $intAgendaId);
        $strStmt->bindParam('lnError', $strMensaje);
        $strStmt->bindParam('lvError', $strMensaje2);
 
        $strStmt->execute();
        $arrayRespuesta = array("codigo" =>$strMensaje, "mensaje" => $strMensaje2);
        return $arrayRespuesta;
    }
    
     /**
     * @param array $arrayParametros
     * @return array $arrayRespuesta
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 08-06-2018 - Genera los cupos acorde a los parametros enviados
     */
    public function generaCuposAdicional($arrayParametros)
    {
        $strFechaDesde     = $arrayParametros['strFechaDesde'];
        $strFechaHasta     = $arrayParametros['strFechaHasta'];
        $intJurisdiccionId = $arrayParametros['intJurisdiccionId'];
        $intCupo           = $arrayParametros['cupo'];
        $intCupoTotal      = $arrayParametros['cupoTotal'];
        
        // se debe enviar un string con suficiente espacio para la respuesta
        $strMensaje = str_repeat(' ', 100);
        $strMensaje2 = str_repeat(' ', 100);
        $strSql     = "BEGIN DB_COMERCIAL.CMKG_CUPOS_CUADRILLAS.P_INSERTA_CUPO_X_HORARIO(" 
                    . " :fechaDesde,"
                    . " :fechaHasta, :jurisdiccionId, :cupo, :cupoTotal, :lnError, :lvError); END;";
        $strStmt    = $this->_em->getConnection()->prepare($strSql);
        $strStmt->bindParam('fechaDesde', $strFechaDesde);
        $strStmt->bindParam('fechaHasta', $strFechaHasta);
        $strStmt->bindParam('jurisdiccionId', $intJurisdiccionId);
        $strStmt->bindParam('cupo', $intCupo);
        $strStmt->bindParam('cupoTotal', $intCupoTotal);
        $strStmt->bindParam('lnError', $strMensaje);
        $strStmt->bindParam('lvError', $strMensaje2);
 
        $strStmt->execute();
        $arrayRespuesta = array("codigo" =>$strMensaje, "mensaje" => $strMensaje2);
        return $arrayRespuesta;
    }    
    
}

