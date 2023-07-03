<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiSectorRepository extends EntityRepository
{
    /**
    * generarJson
    *
    * Esta funcion retorna en formato JSON la lista de Sectores a presentarse en el grid
    * @version 1.0
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.1 18-07-2022 Se realizan ajustes para presentar los sectores
    *
    * @return array $resultado
    *
    */
    public function generarJson($parametros, $nombre,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $arrayRegistros = $this->getRegistros($parametros, $nombre, $estado, $start, $limit);
        
        $arrayRegistrosFinal = $arrayRegistros['registros'];
        $arrayRegistrosTotal = $arrayRegistros['total'];
 
        if ($arrayRegistrosFinal) 
        {
            $intNum = count($arrayRegistrosTotal);            
            foreach ($arrayRegistrosFinal as $data)
            {
                        
                $arr_encontrados[]=array('id_sector' =>$data->getId(),
                                         'nombre_sector' =>trim($data->getNombreSector()),
                                         'nombre_parroquia' => trim($data->getParroquiaId() ? $data->getParroquiaId()->getNombreParroquia() : "NA" ),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($intNum == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_sector' => 0 , 'nombre_sector' => 'Ninguno', 'nombre_parroquia' => 'Ninguno', 'nombre_tipo_sector' => 'Ninguno', 'sector_id' => 0 , 'sector_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);
                return $resultado;
            }
            else
            {
                $dataF =json_encode($arr_encontrados);
                $resultado= '{"total":"'.$intNum.'","encontrados":'.$dataF.'}';
                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';
            return $resultado;
        }
        
    }
    
    /**
    * getRegistros
    *
    * Esta funcion retorna la lista de los sectores a presentarse en el grid
    * @version 1.0
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.1 18-07-2022  Se realizan ajustes para presentar las hipotesis por tipo de caso
    *
    * @return array $arrayDatos
    *
    */
    public function getRegistros($parametros, $nombre,$estado,$start,$limit)
	{	
        $boolBusqueda = false; 
        $where = "";  
		
        $objQuery = $this->_em->createQuery(null);
        
        if($nombre!="")
        {
            $boolBusqueda = true;
			$where .= "AND LOWER(se.nombreSector) like LOWER('%".$nombre."%') ";
        }
		
		if(isset($parametros["idPais"]))
		{
	        if($parametros["idPais"] && $parametros["idPais"]!="")
	        {
	            $boolBusqueda = true;
				$where .= "AND pa.id = '".$parametros["idPais"]."' ";
	        }
		}
		if(isset($parametros["idRegion"]))
		{
	        if($parametros["idRegion"] && $parametros["idRegion"]!="")
	        {
	            $boolBusqueda = true;
				$where .= "AND re.id = '".$parametros["idRegion"]."' ";
	        }
		}
		if(isset($parametros["idProvincia"]))
		{
	        if($parametros["idProvincia"] && $parametros["idProvincia"]!="")
	        {
	            $boolBusqueda = true;
				$where .= "AND pr.id = '".$parametros["idProvincia"]."' ";
			}
		}		
		if(isset($parametros["idCanton"]))
		{
	        if($parametros["idCanton"] && $parametros["idCanton"]!="")
	        {
	            $boolBusqueda = true;
				$where .= "AND ca.id = '".$parametros["idCanton"]."' ";
			}
		}		
		if(isset($parametros["idTipoParroquia"]))
		{
	        if($parametros["idTipoParroquia"] && $parametros["idTipoParroquia"]!="")
	        {
	            $boolBusqueda = true;
				$where .= "AND tp.id = '".$parametros["idTipoParroquia"]."' ";
			}
		}	
        
        
		if(isset($parametros["idParroquia"]))
		{
            if( $parametros['idParroquia'] )
            {
                $boolBusqueda = true;
                
                if( is_array ( $parametros['idParroquia'] ) )
                {
                    $where .= 'AND parr.id IN (:idParroquia) ';
                    
                    $query->setParameter('idParroquia' , array_values($parametros['idParroquia']));
                }
                else
                {
                    $where .= "AND parr.id = '".$parametros["idParroquia"]."' ";
                }
            }
		}
		
        
		if(isset($parametros["idEmpresa"]))
		{
		    if($parametros["idEmpresa"] && $parametros["idEmpresa"]!="")
		    {
		        $where .= "AND se.empresaCod = '".$parametros["idEmpresa"]."' ";
		    }
		    
		    $where .= " AND og.empresaId = '". $parametros["idEmpresa"] . "' ";
		}
		
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
				$where .= "AND LOWER(se.estado) not like LOWER('Eliminado') ";
            }
			else if($estado == "Activo-Todos")
			{
				$where .= "AND LOWER(se.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(parr.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(tp.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(ca.estado) not like LOWER('Eliminado') ";		
				$where .= "AND LOWER(pr.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(re.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(pa.estado) not like LOWER('Eliminado') ";			
			}
            else{
				$where .= "AND LOWER(se.estado) like LOWER('".$estado."') ";
            }
        }
        
        $sql = "SELECT DISTINCT se
        
                FROM 
                schemaBundle:AdmiSector se,
                schemaBundle:AdmiParroquia parr, 
                schemaBundle:AdmiTipoParroquia tp, 
                schemaBundle:AdmiCanton ca,

                schemaBundle:AdmiCantonJurisdiccion caju,
                schemaBundle:AdmiJurisdiccion ju,
                schemaBundle:InfoOficinaGrupo og,
                
                schemaBundle:AdmiProvincia pr, 
				schemaBundle:AdmiRegion re, 
                schemaBundle:AdmiPais pa 
        
                WHERE 
                pa.id = re.paisId   
                AND re.id = pr.regionId 
                AND pr.id = ca.provinciaId 
                AND ca.id = parr.cantonId
                AND tp.id = parr.tipoParroquiaId 
                AND parr.id = se.parroquiaId 
				
                AND caju.cantonId = ca.id
                AND ju.id = caju.jurisdiccionId
                AND og.id = ju.oficinaId
                
				$where 
				
				ORDER BY se.nombreSector
               ";  
			   
        $objQuery->setDQL($sql);
        
        $arrayRegistros = $objQuery->getResult();
        
        if($start!='' && $limit!='')  
        {
            $arrayDatos['registros'] = $objQuery->setFirstResult($start)->setMaxResults($limit)->getResult();
        }    
        else if($start!='' && !$boolBusqueda && $limit=='')
        {    
            $arrayDatos['registros'] = $objQuery->setFirstResult($start)->getResult();
        }    
        else if(($start=='' || $boolBusqueda) && $limit!='')
        {
            $arrayDatos['registros'] = $objQuery->setMaxResults($limit)->getResult();
        }
        else
        {
            $arrayDatos['registros'] = $arrayRegistros;
        }    
            
        $arrayDatos['total']     = $arrayRegistros;

        return $arrayDatos;

    }
    
    /**
    * obtenerRegistros
    * 
    * obtiene registros de la tabla de sectores
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 13-07-2017
    * 
    * @param array $arrayParametros[intEmpresa, strNombre, strEstado]
    * @return array $arrayResult
    */    
    public function obtenerRegistros($arrayParametros)
	{	
        $arrayResult = '';
        if(isset($arrayParametros['intEmpresa']) && !empty($arrayParametros['intEmpresa']))
        {
            $objQuery = $this->_em->createQuery();

            $strWhere = "";

            if($arrayParametros['strNombre'])
            {
                $strWhere = "AND UPPER(a.nombreSector) = :nombre ";
                $objQuery->setParameter('nombre', strtoupper($arrayParametros['strNombre']));
            }

            if(isset($arrayParametros['strEstado']) && !empty($arrayParametros['strEstado']))
            {
                $strWhere.= " AND a.estado = :estado ";
                $objQuery->setParameter('estado', $arrayParametros['strEstado']);
            }         

            $strSQL = "Select a
                       FROM  schemaBundle:AdmiSector a
                       where  a.empresaCod = :empresa            
                       $strWhere 
                       order by a.nombreSector";

            $objQuery->setParameter('empresa', $arrayParametros['intEmpresa']);

            $objQuery->setDQL($strSQL);
            
            $arrayResult = $objQuery->getResult();
        }

        return $arrayResult;
	}    

    public function generarJsonSectoresPorParroquia($idParroquia,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getSectoresPorParroquia($idParroquia, $estado, '', '');
        $registros = $this->getSectoresPorParroquia($idParroquia, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_sector' =>$data->getId(),
                                         'nombre_sector' =>trim($data->getNombreSector()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_sector' => 0 , 'nombre_sector' => 'Ninguno', 'nombre_parroquia' => 'Ninguno', 'nombre_tipo_sector' => 'Ninguno', 'sector_id' => 0 , 'sector_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getSectoresPorParroquia($idParroquia,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiSector','sim');
        
        $boolBusqueda = false;
        if($idParroquia!=""){
            $boolBusqueda = true;
            $qb ->where( 'sim.parroquiaId = ?1');
            $qb->setParameter(1, $idParroquia);
        }
        if($estado!="Todos"){
            $boolBusqueda = true;
            $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
            $qb->setParameter(2, $estado);
        }
        
        $qb->orderBy('sim.nombreSector', 'ASC');
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);  
        
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }

    /**
     * Devuelve un query builder para obtener los sectores activos.
     * NOTA: No se filtra por empresa, punto cobertura, canton ni parroquia
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findSectoresActivos()
	{
		return $qb =$this->createQueryBuilder("t")
		->select("a")
		->from('schemaBundle:AdmiSector a','')->where("a.estado='Activo'");
	}
     
    /**
     * Documentación para el método 'getSectoresPorParroquiaPorNombre'.
     * 
     * Método para obtener la lista de Sectores por Parroquia.
     *
     * @param Array $arrayParametros['PARROQUIAID'] id del cantón.
     *                              ['NOMBRE']      Nombre del Sector.
     *                              ['ESTADO']   Estado del registro.
     *                              ['VALUE']       alias del identificador del registro.
     *                              ['DISPLAY']     alias del nombre del Sector.
     * 
     * @return Response Lista de Cantones/CantonesJuridiscción.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 11-04-2016
     * Se retorna listado como arreglo [id-nombreSector] de la entidad AdmiSector.
     * Se modifica los parámetros a un solo arrayParamenters.
     * Renombrado de variables.
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.2 17-01-2017 - Se realiza cambio del filtro de parroquiaId, con el objetivo de obtener 
     *                           todas las parroquias que se encuentren en estado Activo por empresa.
     * 
     */
    public function getSectoresPorParroquiaPorNombre($arrayParametros)
    {
        $query = $this->_em->createQuery();
        
        $strValue    = $arrayParametros['VALUE'];
        $strDisplay  = $arrayParametros['DISPLAY'];
        
        $strNombre = "";
        
        if($arrayParametros['NOMBRE'])
        {
            $strNombre = "AND UPPER(a.nombreSector) like :NOMBRE ";
            $query->setParameter('NOMBRE', '%'.strtoupper($arrayParametros['NOMBRE']).'%');
        }
        
        if(isset($arrayParametros['PARROQUIAID']) && !empty($arrayParametros['PARROQUIAID']))
        {
            $strNombre.= " AND a.parroquiaId = :IDPARROQUIA ";
            $query->setParameter('IDPARROQUIA', $arrayParametros['PARROQUIAID']);
        }    
        
        $strSQL = "Select a.id as $strValue, a.nombreSector as $strDisplay 
                   FROM  schemaBundle:AdmiSector a
                   WHERE a.estado        = :ESTADO
                   AND a.empresaCod      = :EMPRESA
                   $strNombre 
                   order by a.nombreSector";
        
        $query->setDQL($strSQL);
        
        $query->setParameter('EMPRESA',     $arrayParametros['EMPRESA']);
        $query->setParameter('ESTADO',      $arrayParametros['ESTADO']);
        
        return $query->getResult();
    }
    
    public function generarJsonSectoresPorEmpresa($codEmpresa)
    {
        $arr_encontrados = array();
        
        $query = $this->_em->createQuery("
                SELECT a
                FROM
                schemaBundle:AdmiSector a
                WHERE a.estado='Activo' AND a.empresaCod=".$codEmpresa);
       
        $registros = $query->getResult();
 
        if ($registros) {
            $num = count($registros);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_sector' =>$data->getId(),
                                         'nombre_sector' =>trim($data->getNombreSector()));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_sector' => 0 , 'nombre_sector' => 'Ninguno'));
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
}