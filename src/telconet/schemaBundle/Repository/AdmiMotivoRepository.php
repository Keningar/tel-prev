<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AdmiMotivoRepository extends EntityRepository
{
     public function generarJson2($em_seguridad, $start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros2('', '');
        $registros = $this->getRegistros2($start, $limit);
//         print_r($registros);die;
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                if($data["relacionSistemaId"]){        
                $RelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneById($data["relacionSistemaId"]);
                $id_relacionsistema = $data["relacionSistemaId"];
				
				$motivos =  $this->loadMotivos($id_relacionsistema);
				$estado = "";
				if($motivos && count($motivos)>0) $estado = "Activo";
				else $estado = "Eliminado";
				
                $id_modulo = ($RelacionSistema->getModuloId() ? ($RelacionSistema->getModuloId()->getId() ? $RelacionSistema->getModuloId()->getId() : 0) : 0);
                $nombre_modulo = ($RelacionSistema->getModuloId() ? ($RelacionSistema->getModuloId()->getNombreModulo() ? $RelacionSistema->getModuloId()->getNombreModulo() : "") : "");
                $id_itemmenu = ($RelacionSistema->getItemMenuId() ? ($RelacionSistema->getItemMenuId()->getId() ? $RelacionSistema->getItemMenuId()->getId() :  0)  : 0);
                $nombre_itemmenu = ($RelacionSistema->getItemMenuId() ? ($RelacionSistema->getItemMenuId()->getNombreItemMenu() ? $RelacionSistema->getItemMenuId()->getNombreItemMenu() : "") : "");
                $id_accion = ($RelacionSistema->getAccionId() ? ($RelacionSistema->getAccionId()->getId() ? $RelacionSistema->getAccionId()->getId() : 0) : 0);
                $nombre_accion = ($RelacionSistema->getAccionId() ? ($RelacionSistema->getAccionId()->getNombreAccion() ? $RelacionSistema->getAccionId()->getNombreAccion() : "") : "");

                $arr_encontrados[]=array('id_relacionsistema' =>$id_relacionsistema,
                                         'id_modulo' =>$id_modulo,
                                         'id_itemmenu' =>$id_itemmenu,
                                         'id_accion' =>$id_accion,
                                         'nombres_modulo' =>trim($nombre_modulo),
                                         'nombres_itemmenu' =>trim($nombre_itemmenu),
                                         'nombres_accion' =>trim($nombre_accion),
                                         'estado' =>(strtolower($estado)==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => (strtolower($estado)==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-show'),
                                         'action2' => (strtolower($estado)==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower($estado)==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
                                         }
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_relacionsistema' => 0 , 'id_modulo' => 0 ,'id_itemmenu' => 0 ,'id_accion' => 0 ,
                                                        'nombres_modulo' => 'Ninguno', 'nombres_itemmenu' => 'Ninguno', 'nombres_accion' => 'Ninguno',
                                                        'modulo_id' => 0 , 'modulo_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }

    public function getRegistros2($start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('DISTINCT m.relacionSistemaId')
               ->from('schemaBundle:AdmiMotivo','m');
            
        $boolBusqueda = false; 
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    /**
     * Se realiza modificacion para el tamaño de elemento que pueda mostrar la lista de motivos en panatalla.
     * 
     * @author Jesus Banchen <jbanchen@telconet.ec>
     * @version 1.0
     * 
     * 
     */
    public function generarJson($nombre,$estado,$start,$limit, $relacionSistemaId="")
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '', $relacionSistemaId);
        
        if (count($registrosTotal) > $limit)
        {
            $limit = count($registrosTotal);
        }
        
        $registros = $this->getRegistros($nombre, $estado, $start, $limit, $relacionSistemaId);
        

        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_motivo' =>$data->getId(),
                                         'nombre_motivo' =>trim($data->getNombreMotivo()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_motivo' => 0 , 'nombre_motivo' => 'Ninguno', 'motivo_id' => 0 , 'motivo_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    public function getRegistros($nombre,$estado,$start,$limit, $relacionSistemaId=''){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiMotivo','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreMotivo) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        } 
        if($relacionSistemaId!=""){
            $boolBusqueda = true;                      
            $qb ->where( 'sim.relacionSistemaId = ?2 ');            
            $qb->setParameter(2, $relacionSistemaId);
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?3)');
                $qb->setParameter(3, $estado);
            }
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();                
        
        return $query->getResult();
    }

    public function loadMotivos($relacionsistema_id)
    {   
        $query =    "SELECT m ".
                    "FROM schemaBundle:AdmiMotivo m ".
                    "WHERE m.relacionSistemaId = '$relacionsistema_id' and LOWER(m.estado) != LOWER('Eliminado') ".
                    "ORDER BY m.nombreMotivo ";
        
        return $this->_em->createQuery($query)->getResult();
    }
    
    public function retornaDistintosEleccion($arreglo_motivos, $relacionsistema_id){        
        $qb = $this->_em->createQueryBuilder();
        $qb->select('m')
           ->from('schemaBundle:AdmiMotivo','m')
           ->where( 'm.relacionSistemaId = ?1')
           ->andWhere('LOWER(m.estado) != LOWER(?2)')
           ->setParameter(1, $relacionsistema_id)
           ->setParameter(2, "Eliminado")
           ->andWhere($qb->expr()->NotIn('m.id',$arreglo_motivos));
        
        $query = $qb->getQuery();
        $distintos = $query->getResult();
        return $distintos;
    }
    
    public function findMotivosPorDescripcionTipoSolicitud($descripcionTipoSolicitud){
        $query =    "SELECT am ".
                    "FROM schemaBundle:AdmiTipoSolicitud ts, schemaBundle:SistItemMenu im, 
                     schemaBundle:SeguRelacionSistema rs, schemaBundle:AdmiMotivo am ".
                    "WHERE ts.itemMenuId=im.id AND im.id=rs.itemMenuId AND rs.id=am.relacionSistemaId 
                     
                     AND UPPER(ts.descripcionSolicitud)= '".strtoupper($descripcionTipoSolicitud)."' 
                     AND ts.estado='Activo'  AND am.estado!='Eliminado'
                     order by ts.descripcionSolicitud 
                     DESC";  
        //echo $this->_em->createQuery($query)->getSQL();die;
        return $this->_em->createQuery($query)->getResult();
    }


    public function findMotivosPorModuloPorItemMenuPorAccion($modulo,$itemMenu,$accion){
        $query =    "SELECT am FROM ".
					"schemaBundle:SistModulo mod,	
					schemaBundle:SistAccion acc,	
                     schemaBundle:SeguRelacionSistema rs, schemaBundle:AdmiMotivo am ".
                    "WHERE 
					mod.id=rs.moduloId AND	
					acc.id=rs.accionId AND	
					rs.id=am.relacionSistemaId AND
					 UPPER(mod.nombreModulo)='".strtoupper($modulo)."' AND
					 UPPER(acc.nombreAccion)='".strtoupper($accion)."' AND
                     am.estado in ('Activo','Modificado') order by am.nombreMotivo DESC";  
        return $this->_em->createQuery($query)->getResult();
    }
    
     public function generarJsonMotivos($relacionSistemaId="")
    {
        $arr_encontrados = array();                   
        
        $registrosTotal = $this->getRegistrosMotivos($relacionSistemaId);
        $registros = $this->getRegistrosMotivos($relacionSistemaId);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_motivo' =>$data->getId(),
                                         'nombre_motivo' =>trim($data->getNombreMotivo())
                                         );
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_motivo' => 0 , 'nombre_motivo' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    public function getRegistrosMotivos($relacionSistemaId=''){
        
        $query =    "SELECT am FROM ".
		    " schemaBundle:AdmiMotivo am ".
                    "WHERE am.relacionSistemaId in $relacionSistemaId "; 
        
        return $this->_em->createQuery($query)->getResult();
    }
    
    
    /**
     * Documentación para el método 'getResultadoMotivosPorNombreModuloYPorNombreAccion'.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 12-10-2016 Se agrega la consulta por nombre de motivo
     * 
     */
    public function getResultadoMotivosPorNombreModuloYPorNombreAccion($arrayParametros)
    {
        $arrayRespuesta['total'] = 0;
        $arrayRespuesta['resultado'] = "";
        try
        {
            $query      = $this->_em->createQuery();
            $queryCount = $this->_em->createQuery();
            
            $strSelectCount = "SELECT COUNT ( am.id ) ";
            $strSelect      = "SELECT am ";
            $strFrom        =  "FROM 
                                schemaBundle:SistModulo mod,	
                                schemaBundle:SistAccion acc,	
                                schemaBundle:SeguRelacionSistema rs, 
                                schemaBundle:AdmiMotivo am 
                                WHERE 
                                mod.id=rs.moduloId AND	
                                acc.id=rs.accionId AND	
                                rs.id=am.relacionSistemaId AND
                                mod.nombreModulo = :nombreModulo AND
                                acc.nombreAccion = :nombreAccion AND
                                am.estado in (:estados) ";
            
            $strOrderBy     = " order by am.nombreMotivo DESC";
            $strWhere       = "";
            
            if(isset($arrayParametros['nombreMotivo']) && !empty($arrayParametros['nombreMotivo']))
            {
                $strWhere = " AND am.nombreMotivo like :nombreMotivo ";
                $query->setParameter('nombreMotivo', '%'.$arrayParametros['nombreMotivo'].'%');
                $queryCount->setParameter('nombreMotivo', '%'.$arrayParametros['nombreMotivo'].'%');
            }
            
            $query->setParameter('nombreModulo', $arrayParametros['nombreModulo']);
            $queryCount->setParameter('nombreModulo', $arrayParametros['nombreModulo']);

            $query->setParameter('nombreAccion', $arrayParametros['nombreAccion']);
            $queryCount->setParameter('nombreAccion', $arrayParametros['nombreAccion']);
            
            $query->setParameter('estados', array_values($arrayParametros["estados"]));
            $queryCount->setParameter('estados', array_values($arrayParametros["estados"]));
            
            $strQuery       = $strSelect.$strFrom.$strWhere.$strOrderBy;
            $strQueryCount  = $strSelectCount.$strFrom.$strWhere.$strOrderBy;
            
            $query->setDQL($strQuery);
            $queryCount->setDQL($strQueryCount);
            
            $arrayResultado = $query->getResult();
            $intTotal = $queryCount->getSingleScalarResult();

            $arrayRespuesta['resultado']    = $arrayResultado;
            $arrayRespuesta['total']        = $intTotal;

        } 
        catch (\Exception $e) 
        {
            error_log($e->getMessage());
        }
        
        return $arrayRespuesta;
    }
    
    public function getJSONMotivosPorModuloYPorAccion($arrayParametros)
    {
        $arrayEncontrados = array();
        $arrayResultado = $this->getResultadoMotivosPorNombreModuloYPorNombreAccion($arrayParametros);
        
        $resultado  = $arrayResultado['resultado'];
        $intTotal   = $arrayResultado['total'];
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $objMotivo)
            {
                $arrayEncontrados[] = array(
                    'intIdMotivo'   => $objMotivo->getId(),
                    'strMotivo'     => $objMotivo->getNombreMotivo(),
                );
            }
        }

        $arrayRespuesta = array('total' => $total, 'encontrados' => $arrayEncontrados);
        $jsonData = json_encode($arrayRespuesta);
        return $jsonData;
    }

    /**
     * Método encargado de obtener los motivos parametrizados.
     *
     * Costo 10
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 08-05-2019
     *
     * @param Array $arrayParametros [
     *                                  strIdDepartamento  : Id del departamento
     *                                  strNombreParametro : Nombre del parámetro
     *                                  strModulo          : Módulo
     *                                  strEstado          : Estado
     *                               ]
     * @return Array $arrayRespuesta
     */
    public function getMotivosParametros($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSql = "SELECT AMOT.ID_MOTIVO      AS ID_MOTIVO,
                              AMOT.NOMBRE_MOTIVO  AS NOMBRE_MOTIVO,
                              APDET.VALOR4        AS TIPO,
                              APDET.VALOR5        AS TIEMPO
                         FROM DB_GENERAL.ADMI_PARAMETRO_CAB APCAB,
                              DB_GENERAL.ADMI_PARAMETRO_DET APDET,
                              DB_GENERAL.ADMI_MOTIVO        AMOT
                     WHERE APCAB.ID_PARAMETRO     = APDET.PARAMETRO_ID
                       AND APDET.VALOR1           = TO_CHAR(AMOT.ID_MOTIVO)
                       AND APDET.VALOR3           = :strIdDepartamento
                       AND APCAB.NOMBRE_PARAMETRO = :strNombreParametro
                       AND APCAB.MODULO           = :strModulo
                       AND APCAB.ESTADO           = :strEstado
                       AND APDET.ESTADO           = :strEstado
                       AND AMOT.ESTADO            = :strEstado ";

            $objNativeQuery->setParameter("strIdDepartamento"  , $arrayParametros['strIdDepartamento']);
            $objNativeQuery->setParameter("strNombreParametro" , $arrayParametros['strNombreParametro']);
            $objNativeQuery->setParameter("strModulo"          , $arrayParametros['strModulo']);
            $objNativeQuery->setParameter("strEstado"          , $arrayParametros['strEstado']);

            $objResultSetMap->addScalarResult('ID_MOTIVO'     , 'id_motivo'     , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_MOTIVO' , 'nombre_motivo' , 'string');
            $objResultSetMap->addScalarResult('TIPO'          , 'tipo'          , 'string');
            $objResultSetMap->addScalarResult('TIEMPO'        , 'tiempo'        , 'integer');
            $objNativeQuery->setSQL($strSql);

            $arrayResult = $objNativeQuery->getResult();

            if (count($arrayResult) < 1)
            {
                throw new \Exception('Error : La consulta no retornó datos con el departamento ('.
                                              $arrayParametros['strIdDepartamento'].')');
            }

            $arrayRespuesta = array ('status' => 'ok',
                                     'result' => $arrayResult);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'    => 'fail',
                                     'message'   => $objException->getMessage());
        }
        return $arrayRespuesta;
    }

    /**
     * Método encargado de obtener los motivos de no planificacion.
     *
     * Costo 10
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 08-05-2019
     *
     * @param Array $arrayParametros [
     *                                  strNombreParametro : Nombre del parámetro  
     *                                  strEstado          : Estado
     *                               ]
     * @return Array $arrayRespuesta
     */
    public function getMotivosNoPlanificacion($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);

            $strSql = "SELECT ID_MOTIVO, NOMBRE_MOTIVO
                       FROM DB_GENERAL.ADMI_MOTIVO
                       WHERE ID_MOTIVO IN (
                             SELECT REGEXP_SUBSTR(T1.VALOR1,'[^,]+', 1, LEVEL) AS VALOR
                             FROM(
                                   SELECT 
                                   VALOR1
                                   FROM DB_GENERAL.ADMI_PARAMETRO_DET where PARAMETRO_ID = (SELECT ID_PARAMETRO 
                                                                                            FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
                                                                                            WHERE NOMBRE_PARAMETRO = :strNombreParametro 
                                                                                            AND ESTADO = :strEstado)) T1
                                   CONNECT BY REGEXP_SUBSTR(T1.VALOR1, '[^,]+', 1, LEVEL) IS NOT NULL )";
             

            $objNativeQuery->setParameter("strNombreParametro"  , $arrayParametros['strNombreParametro']);
            $objNativeQuery->setParameter("strEstado" , $arrayParametros['strEstado']);


            $objResultSetMap->addScalarResult('ID_MOTIVO'     , 'id_motivo'     , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_MOTIVO' , 'nombre_motivo' , 'string');

            $objNativeQuery->setSQL($strSql);

            $arrayResult = $objNativeQuery->getResult();

            if (count($arrayResult) < 1)
            {
                throw new \Exception('Error : La consulta no retornó datos con el departamento ('.
                                              $arrayParametros['strIdDepartamento'].')');
            }

            $arrayRespuesta = array ('status' => 'ok',
                                     'result' => $arrayResult);
        }
        catch (\Exception $objException)
        {
            $arrayRespuesta = array ('status'    => 'fail',
                                     'message'   => $objException->getMessage());
        }
        return $arrayRespuesta;
    }    
}
