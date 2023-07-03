<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiTareaInterfaceModeloTrRepository extends EntityRepository
{
    public function generarJsonTareasInterfacesModelosTramosScripts($tareaId,$estado,$start,$limit,$emInfraestructura,$emComunicacion){
        $arr_encontrados = array();
        
        $encontradosTotal = $this->getTareasInterfacesModelosTramosScripts($tareaId,$estado,'','');
        
        $encontrados = $this->getTareasInterfacesModelosTramosScripts($tareaId,$estado,$start,$limit);
        
        if ($encontrados) {
            
            $num = count($encontradosTotal);
                
            foreach ($encontrados as $entidad)
            {
                $interfaceModeloId = $entidad->getInterfaceModeloId();
                $modeloElementoId = $entidad->getModeloElementoId();
                $tramoId = $entidad->getTramoId();                                
                
                if($modeloElementoId!=null || $modeloElementoId!=""){
                
		    $modeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
		    $tipoElemento = $emInfraestructura->find('schemaBundle:AdmiTipoElemento', $modeloElemento->getTipoElementoId());
                
                    //$opcion="Elemento";
                    $opcion = $tipoElemento->getNombreTipoElemento();
                    
                    $modeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
                    $idCombo = $modeloElementoId;
                    $nombreCombo = $modeloElemento->getNombreModeloElemento();
                    
                    if($interfaceModeloId!=null || $interfaceModeloId!=""){
                        $interfaceModelo = $emInfraestructura->find('schemaBundle:AdmiInterfaceModelo', $interfaceModeloId);
                        $tipoInterface = $emInfraestructura->getRepository('schemaBundle:AdmiTipoInterface')->findBy(array( "id" => $interfaceModelo->getTipoInterfaceId()));
                        $nombreTipoInterface = $tipoInterface[0]->getNombreTipoInterface();
                    }
                    else{
                        $interfaceModeloId=0;
                        $nombreTipoInterface="";
                    }
                }
                
                else if($tramoId!=null || $tramoId!=""){
                    $opcion="Tramo";
                    $tramo = $emInfraestructura->find('schemaBundle:InfoTramo', $tramoId);
                    $idCombo = $tramoId;
                    $nombreCombo = $tramo->getElementoAId()." - ".$tramo->getElementoBId();
                }
                else{
                    $interfaceModeloId=0;
                    $nombreTipoInterface="";
                }
                
                $documento = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->findBy(array( "tareaInterfaceModeloTraId" => $entidad->getId()));
                
                $arr_encontrados[]=array('id'                   =>$entidad->getId(),
                                         'opcion'               =>$opcion,
                                         'comboId'              =>$idCombo,
                                         'nombreCombo'          =>$nombreCombo,
                                         'interfaceModeloId'    =>$interfaceModeloId,
                                         'tipoInterfaceNombre'  =>$nombreTipoInterface,
                                         'script'               =>$documento[0]->getMensaje()
                                        );
            }

            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
    public function getTareasInterfacesModelosTramosScripts($tareaId,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('atimt') 
               ->from('schemaBundle:AdmiTareaInterfaceModeloTr','atimt');
            
//            print($tareaId);die();
            
        if($tareaId!=""){
            $qb ->where( 'atimt.tareaId = ?1');
            $qb->setParameter(1, $tareaId);

        }
        if($estado!="Todos"){
            $qb ->andWhere( 'atimt.estado = ?2');
            $qb->setParameter(2, $estado);
            
        }
        
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        
        $query = $qb->getQuery();
        
//            print_r($qb->getSQL());
//            die;
        
        return $query->getResult();
    }
    public function getTareasModulos($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('atimt') 
           ->from('schemaBundle:AdmiTareaInterfaceModeloTr','atimt')
           ->where("atimt.estado not like '%Eliminado%'");

        $query = $qb->getQuery();
        
        return $query->getResult();
    }

    public function getJsonTareasModulo($nombre,$estado,$start,$limit,$emInfraestructura){
        $arr_encontrados = array();
        
        
        
        $encontrados = $this->getTareasModulos($nombre,$estado,$start,$limit);
        
        if ($encontrados) {
            
            $num = count($encontrados);
                
            foreach ($encontrados as $entidad)
            {
                
                $modeloElementoId = $entidad->getModeloElementoId();
                
                
                if($modeloElementoId!=null || $modeloElementoId!=""){
                    
                    $tarea = $this->_em->find('schemaBundle:AdmiTarea', $entidad->getTareaId());
                    $modeloElemento = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $modeloElementoId);
                    
                    
                   $arr_encontrados[]=array('id_tarea'              =>$entidad->getId(),
                                            'nombre_tarea'          =>$tarea->getNombreTarea().'/'.$modeloElemento->getNombreModeloElemento());
                }
                
            }

            $data=json_encode($arr_encontrados);
            $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

            return $resultado;
        }
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
    
    
}
