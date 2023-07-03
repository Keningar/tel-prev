<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class InfoDetalleSolHistRepository extends EntityRepository
{
    public function findDetalleSolicitudHistorial($detalleSolicitudId="", $estado="Activo"){	
        $sql = "SELECT dsh ".
               "FROM schemaBundle:InfoDetalleSolHist dsh ".
               "WHERE dsh.detalleSolicitudId = '$detalleSolicitudId' AND LOWER(dsh.estado) = LOWER('$estado') ";
        
        $query = $this->_em->createQuery($sql);
        $datos = $query->getResult();
        return $datos;
    }
    
    public function findOneDetalleSolicitudHistorial($detalleSolicitudId="", $estado="Planificada"){	       
        $where = "";
        if($estado!="" && $estado){      
            $where .= "AND (LOWER(dsh.estado) = LOWER('$estado'))  ";
        } 
        
        $sql = "SELECT dsh ".
               "FROM schemaBundle:InfoDetalleSolHist dsh ".
               "WHERE dsh.detalleSolicitudId = $detalleSolicitudId $where ".
               "ORDER BY dsh.id DESC ";
        
        $query = $this->_em->createQuery($sql)->setMaxResults(1);
        $datos = $query->getOneOrNullResult();
        return $datos;
    }
    
    public function findLastDetalleSolicitudHistorial($detalleSolicitudId=""){
	$sql = "SELECT max(dsh.id) as id ".
               "FROM schemaBundle:InfoDetalleSolHist dsh ".
               "WHERE dsh.detalleSolicitudId = '$detalleSolicitudId' ";
        
        $query = $this->_em->createQuery($sql);        
        //echo $query->getSQL();
        $datos = $query->getResult();
        return $datos;
    
    }
    
    public function findAllSolicitudHistorial($id=""){
	$sql = "SELECT dsh ".
               "FROM schemaBundle:InfoDetalleSolHist dsh ".
               "WHERE dsh.id = $id ";
        
        $query = $this->_em->createQuery($sql);        
        
        $datos = $query->getOneOrNullResult();
        return $datos;
    
    }
    
    /**
    * getDetalleSolicitudHistorial
    *
    * Método que obtiene el historial de las solicitudes ordenadas descendentemente por fecha
    *      
    * @return json
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * 
    * @version 1.0 10-02-2015       
    */
    public function getDetalleSolicitudHistorial($detalleSolicitudId)
    {
        $query = $this->_em->createQuery();

        $dql = "SELECT dsh " .
               "FROM schemaBundle:InfoDetalleSolHist dsh " .
               "WHERE dsh.detalleSolicitudId = :detalleSolicitud order by dsh.feCreacion DESC ";

        $query->setParameter("detalleSolicitud", $detalleSolicitudId);

        $query->setDQL($dql);

        $datos = $query->getResult();

        return $datos;
    }


        /**
    * getDetalleSolicitudMaterialesExcedHist
    *
    * Método que obtiene el historial de las solicitudes ordenadas descendentemente por fecha
    *      
    * @return json
    *
    * @author Mario Ayerve <mayerve@telconet.ec>
    * 
    * @version 1.0 15-03-2021 
    */
    public function getDetalleSolicitudMaterialesExcedHist($arrayParametros)
    {
        $objRsm           = new ResultSetMappingBuilder($this->_em);
        $objNtvQuery      = $this->_em->createNativeQuery(null, $objRsm);
        $intDetalleSol  = $arrayParametros['idDetalleSolicitud'];

        try
        {
         $strSql       = "  SELECT SOLH.ESTADO
                            FROM DB_COMERCIAL.INFO_DETALLE_SOL_HIST SOLH
                            WHERE SOLH.DETALLE_SOLICITUD_ID  = :detalleSolId";

            $objRsm->addScalarResult('ESTADO'  ,'estado'     ,'string');
            $objNtvQuery->setParameter('detalleSolId'  ,  $intDetalleSol);
            $objNtvQuery->setSQL($strSql);
            $arrayResultado = $objNtvQuery->getResult();
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = $ex->getMessage();
            $arrayResultado ['estado']  = 'ERROR';
            $arrayResultado ['mensaje'] = $strMensajeError;
        }        
        return $arrayResultado;

    }


    
    /**
     * getDetalleSolHist
     *
     * Método que retorna la información del historial de una solicitud dependiendo de los criterios enviados por el usuario                                  
     *
     * @param array  $arrayParametros ['intIdDetalleSolicitud', 'strEstado', 'strUsrCreacion']
     * @return object DetalleSolHist
     *
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 11-04-2016
     */
    public function getDetalleSolHist($arrayParametros)
    {
        //Query que obtiene los Datos
        $query          = $this->_em->createQuery();
        $dqlSelect      = "SELECT dsh ";        
        //Query que obtiene el conteo de resultado de datos
        $queryCount     = $this->_em->createQuery();
        $dqlSelectCount = "SELECT count(dsh.id) ";
        //Cuerpo del Query
        $dqlBody        = " FROM schemaBundle:InfoDetalleSolHist dsh  ";
        $dqlWhere       = " WHERE dsh.detalleSolicitudId = :intIdDetalleSolicitud ";
                
        $query->setParameter('intIdDetalleSolicitud', $arrayParametros['intIdDetalleSolicitud']);
        $queryCount->setParameter('intIdDetalleSolicitud', $arrayParametros['intIdDetalleSolicitud']);
        
        if (!empty($arrayParametros['strEstado'])){            
            //cuerpo del query
            $dqlWhere .= "AND LOWER(dsh.estado) like LOWER(:strEstado) ";
            //query de datos
            $query->setParameter('strEstado', $arrayParametros['strEstado']);
            //query de conteo de datos
            $queryCount->setParameter('strEstado', $arrayParametros['strEstado']);
        }
        
        if (!empty($arrayParametros['strUsrCreacion'])){            
            //cuerpo del query
            $dqlWhere .= "AND LOWER(dsh.usrCreacion) like LOWER(:strUsrCreacion) ";
            //query de datos
            $query->setParameter('strUsrCreacion', $arrayParametros['strUsrCreacion']);
            //query de conteo de datos
            $queryCount->setParameter('strUsrCreacion', $arrayParametros['strUsrCreacion']);
        }
        
        //query de datos
        $dqlCompleto = $dqlSelect.$dqlBody.$dqlWhere." ORDER BY dsh.feCreacion DESC ";
        
        $arrayDatos = array();
        $intTotal = 0;
       
        try
        {           
           //resultado de query de datos
            $query->setDQL($dqlCompleto);
            $arrayDatos = $query->setFirstResult($arrayParametros['intStart'])->setMaxResults($arrayParametros['intLimit'])->getResult();            
        }
        catch(\Exception $e)
        {
            error_log($e);
        }        

        if(!empty($arrayDatos)){
            //query de conteo de datos
            $dqlCompleto    = $dqlSelectCount.$dqlBody.$dqlWhere;
            $queryCount->setDQL($dqlCompleto);
            $intTotal       = $queryCount->getSingleScalarResult();
        }
        $arrayResultado['registros']    = $arrayDatos;
        $arrayResultado['total']        = $intTotal;
        return $arrayResultado;
    }
    
    /**
     * Se obtiene la observación de la solicitud de acuerdo al estado 
     * 
     * @author Libeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 21-05-2016
     * 
     * 
     * @param integer $detalleSolicitudId
     * @param string $estado
     * @return array $datos
     */
    public function findLastDetalleSolHistByIdYEstado($detalleSolicitudId,$estado)
    {
        $query = $this->_em->createQuery();

        $sqlMax=    "SELECT MAX(dshMax.id) ".
                    "FROM schemaBundle:InfoDetalleSolHist dshMax ".
                    "WHERE dshMax.detalleSolicitudId = :detalleSolicitudId ";

        $query->setParameter('detalleSolicitudId', $detalleSolicitudId);
        if($estado)
        {
            if(strtoupper($estado)=="PLANIFICADA")
            {
                $sqlMax.="AND (dshMax.estado = :estadoPlanificada OR dshMax.estado = :estadoReplanificada ) ";
                $query->setParameter('estadoPlanificada', $estado);
                $query->setParameter('estadoReplanificada', 'Replanificada');
            }
            else
            {
                $sqlMax.="AND dshMax.estado = :estado ";
                $query->setParameter('estado', $estado);
            }
        }
        
        $sql =  "SELECT dsh.id, dsh.observacion, dsh.feIniPlan, dsh.feFinPlan, dsh.estado, dsh.motivoId ".
                "FROM schemaBundle:InfoDetalleSolHist dsh ".
                "WHERE dsh.id = (".$sqlMax.")";

        $query->setDQL($sql);

        $datos = $query->getResult();
        return $datos;
    }

}