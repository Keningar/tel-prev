<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SistModuloRepository extends EntityRepository
{
    public function getJsonModulo($nombre,$estado,$start,$limit)
    {        
        $arr_encontrados = array();
        
        $modulosTotal = $this->getModulos($nombre,$estado,'','');
        
        $modulos = $this->getModulos($nombre,$estado,$start,$limit);
        if ($modulos) {
            
            $num = count($modulosTotal);
            
            foreach ($modulos as $modulo)
            {
                $arr_encontrados[]=array('id_modulo' =>$modulo->getId(),
                                         'nombre_modulo' =>trim($modulo->getNombreModulo()),
                                         'estado' =>(trim($modulo->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($modulo->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-edit'),
                                         'action3' => (trim($modulo->getEstado())=='Eliminado' ? 'icon-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                  'encontrados' => array('id_modulo' => 0 , 'nombre_modulo' => 'Ninguno','estado' => 'Ninguno'));
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
    
    public function getModulos($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('n')
               ->from('schemaBundle:SistModulo','n');
               
            
        if($nombre!=""){
            $qb ->where( 'LOWER(n.nombreModulo) like LOWER(?1)');
            $qb->setParameter(1, '%'.$nombre.'%');
        }
        
        if($estado!="Todos"){
            $boolBusqueda = true;
            if($estado=="Activo"){
                $qb ->andWhere("LOWER(n.estado) not like LOWER('Eliminado')");
            }
            else{
                $qb ->andWhere('LOWER(n.estado) = LOWER(?2)');
                $qb->setParameter(2, $estado);
            }
        }
        if($start!='')
            $qb->setFirstResult($start);
        if($limit!='')
            $qb->setMaxResults($limit);
        
        //$qb->orderBy('n.id');
        //se ordenan registros por nombre de modulo para realizar una busqueda mas rapida del modulo deseado
        $qb->orderBy("n.nombreModulo", 'ASC');
        $query = $qb->getQuery();
        return $query->getResult();
    }
    public function  getArrayModulos($estado){
        $arr_encontrados = array();
        
        $modulos = $this->getModulos('',$estado,'','');
 
        foreach ($modulos as $modulo)
            $arr_encontrados[$modulo->getId()]=$modulo->getNombreModulo();
        
        return $arr_encontrados;
    }
        public function getJsonAcciones($id_modulo)
    {
        $arr_encontrados = array();

        
        if($id_modulo)
        {
            
            $rsTotal= $this->getRelacionSistemasPorModulo($id_modulo);
            $rs= $this->getRelacionSistemasPorModulo($id_modulo);
        }

        if(isset($rs))
        {
            $num = count($rsTotal);
            $i=1;
            foreach ($rs as $segu_relacion)
            {
                if($i % 2==0)
                        $clase='k-alt';
                else
                        $clase='';
                $arr_encontrados[]=array('clase'=> $clase,'modulo_id' =>$segu_relacion->getModuloId()->getId(),'modulo_nombre' =>trim($segu_relacion->getModuloId()->getNombreModulo()),'accion_id' =>$segu_relacion->getAccionId()->getId(),'accion_nombre' =>trim($segu_relacion->getAccionId()->getNombreAccion()));
                $i++;
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('modulo_id' => 0 , 'nombre_modulo' => 'Ninguno','accion_id' => 0 , 'nombre_accion' => 'Ninguno'));
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
            $resultado= '{"total":"'.$id_modulo.'","acciones":[]}';

            return $resultado;
        }
    }
    public function getRelacionSistemasPorModulo($id_modulo)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('segu_relacion_sistema')
           ->from('schemaBundle:SeguRelacionSistema','segu_relacion_sistema')
           ->where('segu_relacion_sistema.moduloId = ?1');
        $qb->setParameter(1, $id_modulo);
        
       
        $query = $qb->getQuery();
        
        return $query->getResult();
    }
}
