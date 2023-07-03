<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiProcesoEmpresaRepository extends EntityRepository
{
    public function generarJsonTiposElementos($nombre,$estado,$start,$limit,$codEmpresa=""){
        
        $arr_encontrados = array();
        
        $tiposElementosTotal = $this->getTiposElementos($nombre,$estado,'','',$codEmpresa);
        
        $tiposElementos      = $this->getTiposElementos($nombre,$estado,$start,$limit,$codEmpresa);
                
        if ($tiposElementos) {
            
            $num = count($tiposElementosTotal);                        
                                    
            foreach ($tiposElementos as $tipoElemento)
            {            		
            
                $arr_encontrados[]=array(//'idTipoElemento' =>$tipoElemento->getId(),
					 'idTipoElemento' =>$tipoElemento['id'],
                                         //'nombreTipoElemento' =>trim($tipoElemento->getNombreTipoElemento()),
                                         'nombreTipoElemento' =>trim($tipoElemento['nombreTipoElemento']),
                                         'estado' =>(trim($tipoElemento['estado'])=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($tipoElemento['estado'])=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($tipoElemento['estado'])=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno','idConectorInterface' => 0 , 'nombreConectorInterface' => 'Ninguno', 'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);

                return $resultado;
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
            }
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
   
    public function getTiposElementos($nombre,$estado,$start,$limit,$codEmpresa){
        
        $where = "";
        
        if($nombre && $nombre!=""){
	    $where .= " and a.nombreTipoElemento = '".$nombre."' ";
        }
        if($codEmpresa && $codEmpresa!=""){
	    $where .= " and d.empresaCod = '".$codEmpresa."' ";        
        }
        if($estado != 'Todos')
	    $where .= " and d.estado = '".$estado."' ";
	    
	$where .= " GROUP BY a.nombreTipoElemento, a.id, a.estado ";
        
        $select = "select distinct a.nombreTipoElemento , a.id , a.estado from
		   schemaBundle:AdmiTipoElemento a,
		   schemaBundle:AdmiModeloElemento b,
		   schemaBundle:InfoElemento c,
		   schemaBundle:InfoEmpresaElemento d
		   where 
		   a.id = b.tipoElementoId and
		   b.id = c.modeloElementoId and
		   c.id = d.elementoId 
		   ";
		   
	$sql = $select.$where;
	
	$query = $this->_em->createQuery($sql); 		
        
        if($start!='' && $limit!='')
            return $query->setFirstResult($start)->setMaxResults($limit)->getResult();
        else return $query->getResult();
     
    }
    
    
}
