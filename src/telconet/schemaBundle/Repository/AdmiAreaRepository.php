<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\Response; 

class AdmiAreaRepository extends EntityRepository
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
                $arr_encontrados[]=array('id_area' =>$data->getId(),
                                         'nombre_area' =>trim($data->getNombreArea()),
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_area' => 0 , 'nombre_area' => 'Ninguno', 'area_id' => 0 , 'area_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
               ->from('schemaBundle:AdmiArea','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.nombreArea) like LOWER(?1)');
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

    
    /**
     * getEncontradosAreaByEmpresaAction, Obtiene las areas por empresa.
     * @param  type array $arrayParametros
     * @return type array $objRespuesta
     * @author Sofía Fernández <sfernandez@telconet.ec>          
     * @version 1.0 21-12-2017
     */
     public function getEncontradosAreaByEmpresaJson($arrayParametros)
    {  
        $objRespuesta                  = new JsonResponse();
        $arrayResult                     = array();
        $arrayResultado                = array();
    
        $arrayAreasEmpresa = $this->getRegistrosByEmpresa($arrayParametros);

        foreach ($arrayAreasEmpresa as $arryAreas)
        {
            $arrayItem                = array();
            $arrayItem['id_area']     = $arryAreas['intIdArea'];
            $arrayItem['nombre_area'] = $arryAreas['strNombreArea'];   
            $arrayResult[]              = $arrayItem;
        }

        $arrayResultado['total']       = count($arrayResult);
        $arrayResultado['encontrados'] = $arrayResult;

        $objRespuesta->setData($arrayResultado);
        return $objRespuesta;      
        
    }
    /**
    * getRegistrosByEmpresa, Obtiene las areas por empresa.
    * @param  type array $arrayParametros
    * @return type array $arrayAreas
    * @author Sofía Fernández <sfernandez@telconet.ec>          
    * @version 1.0 21-12-2017
    */
    public function getRegistrosByEmpresa($arrayParametros)
    {
        $objRsm         = new ResultSetMappingBuilder($this->_em);
        $objQuery       = $this->_em->createNativeQuery(null,$objRsm);

        $strSelect = "SELECT ID_AREA, NOMBRE_AREA";
        $strFrom   = "  FROM DB_COMERCIAL.ADMI_AREA  ";        
        $strWhere  = " WHERE ESTADO      = :estado
                         AND EMPRESA_COD = :empresaCod ";
        
        $objRsm->addScalarResult('ID_AREA'    , 'intIdArea'    , 'integer');
        $objRsm->addScalarResult('NOMBRE_AREA', 'strNombreArea', 'string');
        
        if(isset($arrayParametros['intIdArea']) && !empty($arrayParametros['intIdArea']))
        {
            $strWhere .= " AND ID_AREA = :idArea ";
            $objQuery->setParameter('idArea',  $arrayParametros['intIdArea']);
        }
        $objQuery->setParameter('estado',      $arrayParametros['strEstado']);
        $objQuery->setParameter('empresaCod',  $arrayParametros['strIdEmpresa']);
        $strSql = $strSelect.$strFrom.$strWhere;
        $objQuery->setSQL($strSql);
        $arrayAreas = $objQuery->getArrayResult();

        return $arrayAreas;
    }
}
