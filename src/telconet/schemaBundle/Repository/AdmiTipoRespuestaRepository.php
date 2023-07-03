<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiAliasRepository extends EntityRepository
{
    public function generarJson($nombre,$estado,$empresa,$ciudad,$departamento,$start,$limit,$em, $emI, $idPlantilla='')
    {
        $arr_encontrados = array();
        
        if($idPlantilla==''){
        
	      $registrosTotal = $this->getRegistros($nombre, $estado, $empresa,$ciudad,$departamento,'', '');
	      $registros = $this->getRegistros($nombre, $estado,$empresa,$ciudad,$departamento, $start, $limit);
        
        }else{
        
	      $registros = $this->getRegistrosPorPlantilla($idPlantilla);
	      $registrosTotal =$registros;		
        }                
 
        if ($registros) {
            $num = count($registrosTotal);            
            foreach ($registros as $data)
            {					
            
		$empresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->find($data->getEmpresaCod());
		
		if($data->getCantonId()){
		    $juris   = $emI->getRepository('schemaBundle:AdmiCanton')->find($data->getCantonId());
		    $jurisdiccion = $juris->getNombreCanton();
		}else $jurisdiccion = 'N/A';
		
		if($data->getDepartamentoId()){
		    $juris   = $emI->getRepository('schemaBundle:AdmiDepartamento')->find($data->getDepartamentoId());
		    $departamento = $juris->getNombreDepartamento();
		}else $departamento = 'N/A';
            
                $arr_encontrados[]=array('id_alias' =>$data->getId(),
                                         'valor' =>trim($data->getValor()),
                                         'empresa' =>trim($empresa->getNombreEmpresa()),
                                         'jurisdiccion'=>$jurisdiccion,
                                         'departamento'=>$departamento,
                                         'estado' =>(strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'Eliminado':trim($data->getEstado())),
                                         'action1' => 'button-grid-show',
                                         'action2' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (strtolower(trim($data->getEstado()))==strtolower('ELIMINADO') ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_alias' => 0 , 'valor' => 'Ninguno', 'estado' => 'Ninguno' , 'empresa' => 'Ninguno'));
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
    
    public function getRegistros($nombre,$estado,$empresa='',$ciudad='',$departamento='',$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('sim')
               ->from('schemaBundle:AdmiAlias','sim');
            
        $boolBusqueda = false; 
        if($nombre!=""){
            $boolBusqueda = true;
            $qb ->where( 'LOWER(sim.valor) like LOWER(?1)');
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
        
        if($empresa!=""){
            $boolBusqueda = true;
            $qb ->andWhere( 'sim.empresaCod = ?3');
            $qb->setParameter(3, $empresa);
        }
        
        if($ciudad!=""){
            $boolBusqueda = true;
            $qb ->andWhere( 'sim.cantonId = ?4');
            $qb->setParameter(4, $ciudad);
        }
        
        if($departamento!=""){
            $boolBusqueda = true;
            $qb ->andWhere( 'sim.departamentoId = ?5');
            $qb->setParameter(5, $departamento);
        }
        
        if($start!='' && !$boolBusqueda)
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
    
    public function getRegistrosPorPlantilla($idPlantilla=''){       
	  
	  $qb = $this->_em->createQueryBuilder();
            $qb->select('a')
               ->from('schemaBundle:AdmiAlias','a')
               ->from('schemaBundle:InfoAliasPlantilla','b')
               ->where('a = b.aliasId')
               ->andWhere("b.estado <> 'Eliminado' ");
                    
        if($idPlantilla!=""){
            
            $qb ->andWhere( 'b.plantillaId = ?1');
            $qb->setParameter(1, $idPlantilla);
                       
            
        }
               
        
        $query = $qb->getQuery();
              
        
        return $query->getResult();            
    
    }

}
