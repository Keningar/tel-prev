<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiParroquiaRepository extends EntityRepository
{
    /**
    * generarJson
    *
    * Esta funcion retorna en formato JSON la lista de Parroquias a presentarse en el grid
    * @version 1.0
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.1 18-07-2022 Se realizan ajustes para presentar las parroquias
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
                $arr_encontrados[]=array('id_parroquia' =>$data->getId(),
                                         'nombre_parroquia' =>trim($data->getNombreParroquia()),
                                         'nombre_canton' => trim($data->getCantonId() ? $data->getCantonId()->getNombreCanton() : "NA" ),
                                         'nombre_tipo_parroquia' => trim($data->getTipoParroquiaId() ? $data->getTipoParroquiaId()->getNombreTipoParroquia() : "NA" ),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($intNum == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_parroquia' => 0 , 'nombre_parroquia' => 'Ninguno', 'nombre_canton' => 'Ninguno', 'nombre_tipo_parroquia' => 'Ninguno', 'parroquia_id' => 0 , 'parroquia_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    * Esta funcion retorna la lista de las parroquias a presentarse en el grid
    * @version 1.0
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.1 18-07-2022  Se realizan ajustes para presentar las parroquias por tipo de caso
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
			$where .= "AND LOWER(parr.nombreParroquia) like LOWER('%".$nombre."%') ";
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
		
		if(isset($parametros["idEmpresa"])) {
                    $where .= " AND og.empresaId = '". $parametros["idEmpresa"] . "' ";
                }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
				$where .= "AND LOWER(parr.estado) not like LOWER('Eliminado') ";
            }
			else if($estado == "Activo-Todos")
			{
				$where .= "AND LOWER(parr.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(tp.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(ca.estado) not like LOWER('Eliminado') ";		
				$where .= "AND LOWER(pr.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(re.estado) not like LOWER('Eliminado') ";
				$where .= "AND LOWER(pa.estado) not like LOWER('Eliminado') ";			
			}
            else{
				$where .= "AND LOWER(parr.estado) like LOWER('".$estado."') ";
            }
        }
        
        $strSql = "SELECT DISTINCT parr
        
                FROM 
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
                
                AND caju.cantonId = ca.id
                AND ju.id = caju.jurisdiccionId
                AND og.id = ju.oficinaId
                
                AND tp.id = parr.tipoParroquiaId 
				
				$where 
				
				ORDER BY parr.nombreParroquia
               ";  
			   
        $objQuery->setDQL($strSql);
                
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

    public function generarJsonParroquiasPorCanton($idCanton,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $registrosTotal = $this->getParroquiasPorCanton($idCanton, $estado, '', '');
        $registros = $this->getParroquiasPorCanton($idCanton, $estado, $start, $limit);
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {
                        
                $arr_encontrados[]=array('id_parroquia' =>$data->getId(),
                                         'nombre_parroquia' =>trim($data->getNombreParroquia()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_parroquia' => 0 , 'nombre_parroquia' => 'Ninguno', 'nombre_canton' => 'Ninguno', 'nombre_tipo_parroquia' => 'Ninguno', 'parroquia_id' => 0 , 'parroquia_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    
    public function getParroquiasPorCanton($idCanton,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiParroquia','sim');
        
        $boolBusqueda = false;
        if($idCanton!=""){
            $boolBusqueda = true;
            $qb ->where( 'sim.cantonId = ?1');
            $qb->setParameter(1, $idCanton);
        }
        if($estado!="Todos"){
            $boolBusqueda = true;
            $qb ->andWhere('LOWER(sim.estado) = LOWER(?2)');
            $qb->setParameter(2, $estado);
        }
        
        $qb->orderBy('sim.cantonId', 'ASC');
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);  
        
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }

    
    /**
     * Documentación para el método 'getParroquiasPorCantonPorNombre'.
     * 
     * Método para obtener la lista de Parroquias por Cantón.
     *
     * @param Array $arrayParametros['CANTONID'] id del cantón.
     *                              ['NOMBRE']   Nombre de la Parroquia.
     *                              ['ESTADO']   Estado del registro.
     *                              ['VALUE']    alias del identificador del registro.
     *                              ['DISPLAY']  alias del nombre del Sector.
     * 
     * @return Response Lista de Parroquias.
     * 
     * @author Unknow
     * @version 1.0 Unknow
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.1 11-04-2016
     * Se cambia listado de resultado de AdmiCantonJurisdiccion a arreglo [id-nombreParroquia] de la entidad AdmiParroquia.
     * Se modifica los parámetros a un solo arrayParamenters.
     * Renombrado de variables.
     */
    public function getParroquiasPorCantonPorNombre($arrayParametros)
    {
        $strValue    = $arrayParametros['VALUE'];
        $strDisplay  = $arrayParametros['DISPLAY'];
        
        $objQueryBuilder = $this->_em->createQueryBuilder();
                            
        $objQueryBuilder->select("a.id as $strValue, a.nombreParroquia as $strDisplay ")
                        ->from('schemaBundle:AdmiParroquia', 'a')
                        ->where('a.cantonId = :CANTONID')
                        ->andWhere("a.estado = :ESTADO")
                        ->orderBy('a.nombreParroquia', 'ASC');
        
        $objQueryBuilder->setParameter('CANTONID', $arrayParametros['CANTONID']);
        $objQueryBuilder->setParameter('ESTADO',   $arrayParametros['ESTADO']);
        
        if($arrayParametros['NOMBRE'] != '')
        {
            $objQueryBuilder->andWhere('UPPER(a.nombreParroquia) like :nombre');
            $objQueryBuilder->setParameter('nombre', '%' . strtoupper($arrayParametros['NOMBRE']) . '%');
        }
        
        return $objQueryBuilder->getQuery()->getResult();
    }
}