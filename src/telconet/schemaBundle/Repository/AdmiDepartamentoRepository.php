<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\Response; 

class AdmiDepartamentoRepository extends EntityRepository
{
    public function generarJson($nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getRegistros($nombre, $estado, '', '');
        $registros = $this->getRegistros($nombre, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {  
				$area = $data->getAreaId() ? $data->getAreaId() : null;
				$nombreArea = (is_object($area) ? ($area->getNombreArea() ? $area->getNombreArea() : '') : '');
				
                $arr_encontrados[]=array('id_departamento' =>$data->getId(),
                                         'nombre_departamento' =>trim($data->getNombreDepartamento()),
                                         'nombre_area' => trim($nombreArea),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_departamento' => 0 , 'nombre_departamento' => 'Ninguno', 'nombre_area' => 'Ninguno',  'departamento_id' => 0 , 'departamento_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getRegistros($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiDepartamento','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreDepartamento) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(sim.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
        
	public function generarJsonAreasXOficina($id_oficina="", $query="",$codEmpresa="")
    {
        $arr_encontrados = array();        
		
		$where = "";
		if($query != "")
			$where = "AND lower(ar.nombreArea) like lower('%$query%')";
			
		$query_string = "
						SELECT DISTINCT ar 
						
						FROM 
						schemaBundle:InfoPersonaEmpresaRol per,
						schemaBundle:AdmiDepartamento d,
						schemaBundle:AdmiArea ar						
						
						WHERE 
						per.departamentoId = d.id 
						AND	d.areaId = ar.id 
						AND per.oficinaId ='$id_oficina' 
						AND LOWER(d.estado) not like LOWER('Eliminado') 
						AND LOWER(ar.estado) not like LOWER('Eliminado') 
						AND LOWER(per.estado) not like LOWER('Eliminado') 
						AND ar.empresaCod='$codEmpresa'
						$where 
						
						ORDER BY ar.nombreArea
						";
		$query = $this->_em->createQuery($query_string);
		$registros = $query->getResult();
		
        if ($registros) {
            $num = count($registros);            
            foreach ($registros as $data)
            {
                if($this->verificarArray($arr_encontrados, $data->getId()))        
                    $arr_encontrados[]=array('id_area' =>$data->getId(),
                                             'nombre_area' => ucwords(strtolower(trim($data->getNombreArea()))) );
            }
            $dataF =json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
	public function verificarArray($array,$id){
        
        foreach($array as $data){
            if($data['id_area']==$id)
                return false;
        }
        return true;
    }
    
	public function generarJsonDepartamentosXArea($id_area="", $id_oficina="", $query="",$codEmpresa="")
    {
        $arr_encontrados = array();
		
		$where = "";
		if($query != "")
			$where = "AND lower(d.nombreDepartamento) like lower('%$query%')";
			
		$query_string = "
						SELECT DISTINCT d 
						
						FROM 
						schemaBundle:InfoPersonaEmpresaRol per,
						schemaBundle:AdmiDepartamento d,
						schemaBundle:AdmiArea ar						
						
						WHERE 
						per.departamentoId = d.id 
						AND	d.areaId = ar.id 
						AND per.oficinaId ='$id_oficina' 
						AND ar.id ='$id_area' 
						AND LOWER(d.estado) not like LOWER('Eliminado') 
						AND LOWER(ar.estado) not like LOWER('Eliminado') 
						AND LOWER(per.estado) not like LOWER('Eliminado')
						AND ar.empresaCod='$codEmpresa'
						$where 
						
						ORDER BY d.nombreDepartamento
						";
		$query = $this->_em->createQuery($query_string);
		$registros = $query->getResult();
		
        if ($registros) {
            $num = count($registros);            
            foreach ($registros as $data)
            {
                $arr_encontrados[]=array('id_departamento' =>$data->getId(),
                                         'nombre_departamento' =>ucwords(strtolower(trim($data->getNombreDepartamento()))) );
            }
            $dataF =json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }   
    
  /**
    * getDepartamentosByEmpresaYNombre
    *
    * Método que ejecuta el query los departamento por empresa y estado diferente a Eliminado e Inactivo
    *
    * @param string $empresa         
    * @param string $nombre
    *
    * @return registros con el resultado de query ejecutado
    *
    * @author Allan Suárez <arsuarez@telconet.ec>
    * @version 1.0 29-05-2014
    */
    public function getDepartamentosByEmpresaYNombre( $empresa, $nombre){
                		
	    $where = "";
	    
	    if($nombre && $nombre != ""){
		    $where = "AND UPPER(d.nombreDepartamento) like UPPER(:nombre)";		    
	    }
		    
	    $sql = "
		    SELECT d						
		    FROM 					
		    schemaBundle:AdmiDepartamento d											
		    WHERE 
		    d.estado not in ('Eliminado','Inactivo') and
		    d.empresaCod = :empresa				
		    $where 											
		    ";
	    $query = $this->_em->createQuery($sql);
	    
	    if($nombre!='') $query->setParameter('nombre', '%' . $nombre . '%');
	    
	    $query->setParameter('empresa',$empresa);
	    
	    $registros = $query->getResult();									
	    
	    return $registros;                           
    }
    
  /**
      * generarJsonDepartamentoByEmpresaYNombre
      *
      * Método que devuelve el json con los departamentos obtenidos con la ejecución del query
      *
      * @param string $empresa         
      * @param string $nombre        
      *
      * @return JSON con valores a mostrar en el combobox
      *
      * @author Allan Suárez <arsuarez@telconet.ec>
      * @version 1.0 29-05-2014
      */
    public function generarJsonDepartamentoByEmpresaYNombre( $empresa, $nombre){
    
	  $registros = $this->getDepartamentosByEmpresaYNombre($empresa, $nombre);
	  
	  if ($registros) {
	  
		$num = count($registros);            
		foreach ($registros as $data)
		{
		    $arr_encontrados[]=array('id_departamento' =>$data->getId(),
					     'nombre_departamento' =>ucwords(strtolower(trim($data->getNombreDepartamento()))) );
		}
		$dataF =json_encode($arr_encontrados);
		$resultado= '{"total":"'.$num.'","encontrados":'.$dataF.'}';
		return $resultado;
	  }
	  else
	  {
		$resultado= '{"total":"0","encontrados":[]}';
		return $resultado;
	  }
      
    
    }
    
    /**
     * getEncontradosDepartamentoByAreaYEmpresaJson, convierte a Json el arreglo de departamentos por area y empresa.
     * @param  type array $arrayParametros
     * @return type array $objRespuesta
     * @author Sofía Fernández <sfernandez@telconet.ec>          
     * @version 1.0 22-12-2017
     */
     public function getEncontradosDepartamentoByAreaYEmpresaJson($arrayParametros)
    {  
        $objRespuesta                  = new JsonResponse();
        $arrayResult                     = array();
        $arrayResultado                = array();
    
        $arrayDeptoAreasEmpresa = $this->getRegistrosByAreaYEmpresa($arrayParametros);

        foreach ($arrayDeptoAreasEmpresa as $arrayDepartamento)
        {
            $arrayItem                = array();
            $arrayItem['id_departamento']     = $arrayDepartamento['intIdDepartamento'];
            $arrayItem['nombre_departamento'] = $arrayDepartamento['strNombreDepartamento'];   
            $arrayResult[]                      = $arrayItem;
        }

        $arrayResultado['total']       = count($arrayResult);
        $arrayResultado['encontrados'] = $arrayResult;

        $objRespuesta->setData($arrayResultado);
        return $objRespuesta;      
        
    }
    /**
    * getRegistrosByAreaYEmpresa, Obtiene los departamentos por area y empresa.
    * @param  type array $arrayParametros
    * @return type array $arrayAreas
    * @author Sofía Fernández <sfernandez@telconet.ec>          
    * @version 1.0 21-12-2017
    */
    public function getRegistrosByAreaYEmpresa($arrayParametros)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null,$objRsm);

        $strSelect = " SELECT ID_DEPARTAMENTO, NOMBRE_DEPARTAMENTO ";
        $strFrom   = "   FROM DB_COMERCIAL.ADMI_DEPARTAMENTO ";
        $strWhere  = "  WHERE ESTADO      = :estado
                          AND EMPRESA_COD = :empresaCod 
                          AND AREA_ID     = :areaId";

        $objRsm->addScalarResult('ID_DEPARTAMENTO'    , 'intIdDepartamento'    , 'integer');
        $objRsm->addScalarResult('NOMBRE_DEPARTAMENTO', 'strNombreDepartamento', 'string');
        
        if(isset($arrayParametros['intIdDepartamento']) && !empty($arrayParametros['intIdDepartamento']))
        {
            $strWhere .= " AND ID_DEPARTAMENTO = :idDepartamento ";
            $objQuery->setParameter('idDepartamento', $arrayParametros['intIdDepartamento']);
        }
        
        $objQuery->setParameter('estado',      $arrayParametros['strEstado']);
        $objQuery->setParameter('empresaCod',  $arrayParametros['strIdEmpresa']);
        $objQuery->setParameter('areaId',      $arrayParametros['intIdArea']);

        $strSql = $strSelect.$strFrom.$strWhere;
        $objQuery->setSQL($strSql);
        $arrayAreas = $objQuery->getArrayResult();

        return $arrayAreas;
    }
 
    /**
     * Método encargado de obtener los departamentos.
     *
     * Costo 15
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 14-12-2018
     *
     * @param Array $arrayParametros [
     *                                  intIdArea             : id del área,
     *                                  intIdDepartamento     : id del departamento,
     *                                  strNombreArea         : nombre del área,
     *                                  strNombreDepartamento : nombre del departamento,
     *                                  strEstadoArea         : estado del área,
     *                                  strEstadoDepartamento : estado del departamento,
     *                                  strPrefijoEmpresa     : prefijo de la empresa
     *                               ]
     * @return Array $arrayResultado
     */
    public function getDepartamentos($arrayParametros)
    {
        try
        {
            $objResultSetMap = new ResultSetMappingBuilder($this->_em);
            $objNativeQuery  = $this->_em->createNativeQuery(null, $objResultSetMap);
            $strWhere        = '';

            if (isset($arrayParametros['intIdArea']) && !empty($arrayParametros['intIdArea']))
            {
                $strWhere .= 'AND ADAR.ID_AREA = :intIdArea ';
                $objNativeQuery->setParameter("intIdArea", $arrayParametros['intIdArea']);
            }

            if (isset($arrayParametros['intIdDepartamento']) && !empty($arrayParametros['intIdDepartamento']))
            {
                $strWhere .= 'AND ADEP.ID_DEPARTAMENTO = :intIdDepartamento ';
                $objNativeQuery->setParameter("intIdDepartamento", $arrayParametros['intIdDepartamento']);
            }

            if (isset($arrayParametros['strNombreArea']) && !empty($arrayParametros['strNombreArea']))
            {
                $strWhere .= 'AND UPPER(ADAR.NOMBRE_AREA) LIKE UPPER((:strNombreArea)) ';
                $objNativeQuery->setParameter("strNombreArea", '%'.$arrayParametros['strNombreArea'].'%');
            }

            if (isset($arrayParametros['strNombreDepartamento']) && !empty($arrayParametros['strNombreDepartamento']))
            {
                $strWhere .= 'AND UPPER(ADEP.NOMBRE_DEPARTAMENTO) LIKE UPPER((:strNombreDepartamento)) ';
                $objNativeQuery->setParameter("strNombreDepartamento", '%'.$arrayParametros['strNombreDepartamento'].'%');
            }

            if (isset($arrayParametros['strEstadoArea']) && !empty($arrayParametros['strEstadoArea']))
            {
                $strWhere .= 'AND UPPER(ADAR.ESTADO) = UPPER(:strEstadoArea) ';
                $objNativeQuery->setParameter("strEstadoArea", $arrayParametros['strEstadoArea']);
            }

            if (isset($arrayParametros['strEstadoDepartamento']) && !empty($arrayParametros['strEstadoDepartamento']))
            {
                $strWhere .= 'AND UPPER(ADEP.ESTADO) = UPPER(:strEstadoDepartamento) ';
                $objNativeQuery->setParameter("strEstadoDepartamento", $arrayParametros['strEstadoDepartamento']);
            }

            if (isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']))
            {
                $strWhere .= 'AND UPPER(IEG.PREFIJO) = UPPER(:strPrefijoEmpresa) ';
                $objNativeQuery->setParameter("strPrefijoEmpresa", $arrayParametros['strPrefijoEmpresa']);
            }

            $strSql = "SELECT ADAR.ID_AREA, ".
                             "ADAR.NOMBRE_AREA, ".
                             "ADAR.ESTADO AS ESTADO_AREA, ".
                             "ADEP.ID_DEPARTAMENTO, ".
                             "ADEP.NOMBRE_DEPARTAMENTO, ".
                             "ADEP.ESTADO AS ESTADO_DEPARTAMENTO, ".
                             "IEG.NOMBRE_EMPRESA ".
                         "FROM DB_GENERAL.ADMI_DEPARTAMENTO    ADEP, ".
                              "DB_GENERAL.ADMI_AREA            ADAR, ".
                              "DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ".
                      "WHERE ADEP.AREA_ID = ADAR.ID_AREA ".
                        "AND IEG.COD_EMPRESA = ADAR.EMPRESA_COD ".
                         $strWhere.
                      "ORDER BY UPPER(ADEP.NOMBRE_DEPARTAMENTO) ";

            $objResultSetMap->addScalarResult('ID_AREA'             , 'idArea'             , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_AREA'         , 'nombreArea'         , 'string');
            $objResultSetMap->addScalarResult('ESTADO_AREA'         , 'estadoArea'         , 'string');
            $objResultSetMap->addScalarResult('ID_DEPARTAMENTO'     , 'idDepartamento'     , 'integer');
            $objResultSetMap->addScalarResult('NOMBRE_DEPARTAMENTO' , 'nombreDepartamento' , 'string');
            $objResultSetMap->addScalarResult('ESTADO_DEPARTAMENTO' , 'estadoDepartamento' , 'string');
            $objResultSetMap->addScalarResult('NOMBRE_EMPRESA'      , 'nombreEmpresa'      , 'string');
            $objNativeQuery->setSQL($strSql);

            $arrayResult = $objNativeQuery->getResult();

            if (count($arrayResult) < 1)
            {
                throw new \Exception('Error : La consulta no retornó datos');
            }

            return array ('data' => array ('status' => 'ok', 'result' => $arrayResult));
        }
        catch (\Exception $objException)
        {
            $strMessage = 'Error al consultar los datos';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode(' : ',$objException->getMessage())[1];
            }
            else
            {
                $arrayException = array ('message' => $objException->getMessage(),
                                         'method'  => 'AdmiDepartamentoRepository->getDepartamentos');
            }

            return array ('data'      => array ('status'  => 'fail','message'   => $strMessage),
                          'exception' => $arrayException);
        }
    }

    /**
     * Documentación para el método 'getDepartamentosPorLogin'.
     *
     * Método encargado de retornar el departamento del login del usuario a buscar.
     *
     * Costo 19
     *
     * @param array $arrayParametros [
     *                                  "strLogin"                => login de la persona a buscar el departamento.
     *                                  "intIdEmpresa"            => código de la empresa.
     *                                  "strEstadoDepartamento"   => estado del departamento.
     *                               ]
     *
     * @return array $arrayResultado arreglo del departamento.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 03-07-2020
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.1 12-07-2021 - Se retorna el Id_Departamento en la busqueda.
     *
     */
    public function getDepartamentosPorLogin($arrayParametros)
    {
        try
        {
            $objRsm    = new ResultSetMappingBuilder($this->_em);
            $objQuery  = $this->_em->createNativeQuery(null,$objRsm);

            $strSelect = " SELECT AD.NOMBRE_DEPARTAMENTO,AD.ID_DEPARTAMENTO ";

            $strFrom   = " FROM INFO_PERSONA                    IPE
                                JOIN INFO_PERSONA_EMPRESA_ROL   IPER ON IPER.PERSONA_ID = IPE.ID_PERSONA
                                JOIN INFO_EMPRESA_ROL           IER ON IER.ID_EMPRESA_ROL = IPER.EMPRESA_ROL_ID
                                JOIN ADMI_DEPARTAMENTO          AD ON AD.ID_DEPARTAMENTO = IPER.DEPARTAMENTO_ID ";

            $strWhere  = " WHERE IPER.ESTADO = :estado " ;
            $objQuery->setParameter("estado", "Activo");
            if (isset($arrayParametros['strLogin']) && !empty($arrayParametros['strLogin']))
            {
                $strWhere .= ' AND IPE.LOGIN = :strLogin ';
                $objQuery->setParameter("strLogin", $arrayParametros['strLogin']);
            }

            if (isset($arrayParametros['strEstadoDepartamento']) && !empty($arrayParametros['strEstadoDepartamento']))
            {
                $strWhere .= ' AND UPPER(AD.ESTADO) = UPPER(:strEstadoDepartamento) ';
                $objQuery->setParameter("strEstadoDepartamento", $arrayParametros['strEstadoDepartamento']);
            }

            if (isset($arrayParametros['intIdEmpresa']) && !empty($arrayParametros['intIdEmpresa']))
            {
                $strWhere .=' AND AD.EMPRESA_COD = :intIdEmpresa ';
                $strWhere .=' AND IER.EMPRESA_COD = :intIdEmpresa ';
                $objQuery->setParameter("intIdEmpresa", $arrayParametros['intIdEmpresa']);
            }
            $strSql = $strSelect.$strFrom.$strWhere;
            $objRsm->addScalarResult('NOMBRE_DEPARTAMENTO' , 'NOMBRE_DEPARTAMENTO' , 'string');
            $objRsm->addScalarResult('ID_DEPARTAMENTO' , 'ID_DEPARTAMENTO' , 'string');
            $objQuery->setSQL($strSql);
            $arrayDatos = $objQuery->getResult();
        }
        catch (\Exception $ex) 
        {
            $strMensajeError = $ex->getMessage();
        }
        $arrayResultado['registros'] = $arrayDatos;
        $arrayResultado['error']     = $strMensajeError;
        return $arrayResultado;
    }
}
