<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiInterfaceModeloRepository extends EntityRepository
{
    public function generarJsonInterfacesModelos($modeloElemento,$tipoInterface,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $interfacesModelosTotal = $this->getInterfacesModelos($modeloElemento,$tipoInterface,$estado,'','');
        
        $interfacesModelos = $this->getInterfacesModelos($modeloElemento,$tipoInterface,$estado,$start,$limit);
//        error_log('entra');
        if ($interfacesModelos) {
            
            $num = count($interfacesModelosTotal);
            
            foreach ($interfacesModelos as $interfaceModelo)
            {
                $arr_encontrados[]=array('idInterfaceModelo' =>$interfaceModelo->getId(),
                                         'modeloElemento' =>trim($interfaceModelo->getModeloElementoId()->getNombreModeloElemento()),
                                         'tipoInterface' =>trim($interfaceModelo->getTipoInterfaceId()->getNombreTipoInterface()),
                                         'estado' =>(trim($interfaceModelo->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($interfaceModelo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($interfaceModelo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getInterfacesModelos($modeloElemento,$tipoInterface,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiInterfaceModelo','e');
               
            
        if($modeloElemento!=""){
            $qb ->where( 'e.modeloElementoId=?1');
            $qb->setParameter(1, $modeloElemento);
        }
        if($tipoInterface!=""){
            $qb ->where( 'e.tipoInterfaceId=?3');
            $qb->setParameter(3, $tipoInterface);
        }
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function generarJsonInterfacesModelosPorModelo($modeloElemento,$estado,$start,$limit,$em){
        $arr_encontrados = array();
        //$em = $this->getEntityManager('telconet_infraestructura');
        $interfacesModelosTotal = $this->getInterfacesModelosPorModelo($modeloElemento,$estado,'','');
        
        $interfacesModelos = $this->getInterfacesModelosPorModelo($modeloElemento,$estado,$start,$limit);
//        error_log('entra');
        if ($interfacesModelos) {
            
            $num = count($interfacesModelosTotal);
            
            foreach ($interfacesModelos as $interfaceModelo)
            {
                $idTipoInterface = $interfaceModelo->getTipoInterfaceId();
                $tipoInterface = $em->find('schemaBundle:AdmiTipoInterface', $idTipoInterface);
                
                
                $arr_encontrados[]=array('idInterfaceModelo' =>$interfaceModelo->getId(),
                                         'tipoInterfaceId' =>trim($tipoInterface->getId()),
                                         'nombreTipoInterface' =>trim($tipoInterface->getNombreTipoInterface()),
                                         'cantidadInterface' =>trim($tipoInterface->getCantidadInterface()),
                                         'claseInterface' =>trim($tipoInterface->getClaseInterface()),
                                         'formatoInterface' =>trim($tipoInterface->getFormatoInterface()),
                                         'estado' =>(trim($interfaceModelo->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($interfaceModelo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($interfaceModelo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getInterfacesModelosPorModelo($modeloElemento,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiInterfaceModelo','e');
               
            
        if($modeloElemento!=""){
            $qb ->where( 'e.modeloElementoId=?1');
            $qb->setParameter(1, $modeloElemento);
        }
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function generarJsonInterfacesModelosPorModeloElemento($modeloElemento,$estado,$start,$limit){
        $arr_encontrados = array();
        $em = $this->getEntityManager('telconet_infraestructura');
        $interfacesModelosTotal = $this->getInterfacesModelosPorModelo($modeloElemento,$estado,'','');
        
        $interfacesModelos = $this->getInterfacesModelosPorModelo($modeloElemento,$estado,$start,$limit);
//        error_log('entra');
        if ($interfacesModelos) {
            
            $num = count($interfacesModelosTotal);
            
            foreach ($interfacesModelos as $interfaceModelo)
            {
                //tipo de interface
                $idTipoInterface = $interfaceModelo->getTipoInterfaceId();
                $tipoInterface = $em->find('schemaBundle:AdmiTipoInterface', $idTipoInterface);
                
                
                //detalle interface
                $detalles = $em->getRepository('schemaBundle:AdmiDetalleInterface')->findBy(array( "interfaceModeloId" => $interfaceModelo->getId()));
                
                $numDetalles=0;
                
                foreach($detalles as $caracteristicas){
                    $idDetalle = $caracteristicas->getDetalleId();
                    $detalle = $em->find('schemaBundle:AdmiDetalle', $idDetalle);
                    
                    $arr_detalles[]=array('idDetalleInterface'   =>  $caracteristicas->getId(),
                                          'idDetalle'   =>  $detalle->getId(),
                                          'nombreDetalle' => $detalle->getNombreDetalle());
                    
                    $numDetalles =count($arr_detalles);
                }
                if($numDetalles>0){
                    $resultadoDetalles= '{"total":"'.$numDetalles.'","detalles":'.json_encode( $arr_detalles).'}';
                }
                else{
                    $resultadoDetalles= '{"total":"0","detalles":"0"}';
                }
                
                $arr_encontrados[]=array('idInterfaceModelo' =>$interfaceModelo->getId(),
                                         'tipoInterfaceId' =>($tipoInterface->getId()),
                                         'nombreTipoInterface' =>trim($tipoInterface->getNombreTipoInterface()),
                                         'cantidadInterface' =>trim($interfaceModelo->getCantidadInterface()),
                                         'claseInterface' =>trim($interfaceModelo->getClaseInterface()),
                                         'formatoInterface' =>trim($interfaceModelo->getFormatoInterface()),
                                         'caracteristicasInterface' => $resultadoDetalles,
                                         'estado' =>(trim($interfaceModelo->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($interfaceModelo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($interfaceModelo->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
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
   
    public function getInterfacesModelosPorModeloElemento($modeloElemento,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiInterfaceModelo','e');
               
            
        if($modeloElemento!=""){
            $qb ->where( 'e.modeloElementoId=?1');
            $qb->setParameter(1, $modeloElemento);
        }
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function generarJsonInterfacesModelosParaCpe($modeloElemento,$estado,$start,$limit){
        $arr_encontrados = array();
        $em = $this->getEntityManager('telconet_infraestructura');
        $interfacesModelosTotal = $this->getInterfacesModelosPorModelo($modeloElemento,$estado,'','');
        
        $interfacesModelos = $this->getInterfacesModelosPorModelo($modeloElemento,$estado,$start,$limit);
//        error_log('entra');
        if ($interfacesModelos) {
            
            $num = 0;
            
            foreach ($interfacesModelos as $interfaceModelo)
            {
                $cantidadInterfaces = $interfaceModelo->getCantidadInterface();
                $formato = $interfaceModelo->getFormatoInterface();
                //tipo de interface
                $idTipoInterface = $interfaceModelo->getTipoInterfaceId();
                $tipoInterface = $em->find('schemaBundle:AdmiTipoInterface', $idTipoInterface);
                
                for($i=0;$i<$cantidadInterfaces;$i++){
                    
                    $format = explode("?", $formato);
                    $nombreInterfaceElemento = $format[0].($i+1);
                    
                    
                    $arr_encontrados[]=array('idInterfaceModelo' =>$interfaceModelo->getId(),
                                         'nombreInterfaceElemento' =>trim($nombreInterfaceElemento),
                                         'vci' => '',
                                         'macCliente' => '',
                                         'ipCliente' => '',
                                         'tipoMedioId' => '',
                                         'tipoMedioNombre' => '',
                                         'idElementoDslam' => '',
                                         'nombreElementoDslam' => '',
                                         'idInterfaceElementoDslam' => '',
                                         'interfaceElementoDslam' => '',
                                         'vlanDslam' => '1',
                                         'loginCliente' => ''
                        );
                    
                    $num++;
                }
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
   
    
}
