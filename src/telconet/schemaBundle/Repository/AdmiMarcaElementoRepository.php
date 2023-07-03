<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiMarcaElementoRepository extends EntityRepository
{
    public function generarJsonMarcasElementosPorTipo($tipoElemento,$estado,$start,$limit){
        $arr_encontrados = array();
        $marcasTotal = $this->getMarcasElementosPorTipo($tipoElemento,$estado,'','');
        
        $marcas = $this->getMarcasElementosPorTipo($tipoElemento,$estado,$start,$limit);
//        error_log('entra');
        if ($marcas) {
            
            $num = count($marcasTotal);
            
            foreach ($marcas as $marca)
            {
                $arr_encontrados[]=array('idMarcaElemento' =>$marca->getId(),
                                         'nombreMarcaElemento' =>trim($marca->getNombreMarcaElemento()),
                                         'estado' =>(trim($marca->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($marca->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($marca->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 
                                                        'nombreConectorInterface' => 'Ninguno',
                                                        'idConectorInterface' => 0 , 
                                                        'nombreConectorInterface' => 'Ninguno', 
                                                        'estado' => 'Ninguno'));
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
    
    /**
     * Funcion que obtiene las marcas por los tipos de elementos.
     *
     * @author Luis Farro <lfarro@telconet.ec>
     * @version 1.0 23-01-2023 - Se remueve la validación que define el
     *                           inicio y límite de valores a mostrar
    */
    public function getMarcasElementosPorTipo($tipoElemento,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('admi_marca_elemento')
               ->from('schemaBundle:AdmiModeloElemento','admi_modelo_elemento')
               ->from('schemaBundle:AdmiMarcaElemento','admi_marca_elemento');
             
        if($tipoElemento!=""){
            $qb ->where( 'admi_modelo_elemento.tipoElementoId =?1')
                ->andWhere( 'admi_modelo_elemento.marcaElementoId = admi_marca_elemento');
            $qb->setParameter(1, $tipoElemento);
        }
        if($estado!="Todos"){
            $qb ->andWhere('admi_marca_elemento.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    /**
     * Funcion que genera un Json de las marcas de los elementos
     * consultados por un tipo elemento, para este caso
     * el tipo es CPE
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 27-05-2014
     * @param String    $tipoElemento   tipo de elemento CPE
     * @param String    $estado         estado de las marcas
     * @param int       $start          numero de inicio para el limit
     * @param int       $limit          numero de fin para el limit
    */
    public function generarJsonMarcasElementosPorTipoCpe($tipoElemento, $estado,$start,$limit)
    {
        $arr_encontrados = array();
        $marcasTotal = $this->getMarcasElementosPorTipoCpe($tipoElemento,$estado,'','');
        
        $marcas = $this->getMarcasElementosPorTipoCpe($tipoElemento,$estado,$start,$limit);
//        error_log('entra');
        if ($marcas) 
        {
            
            $num = count($marcasTotal);
            
            foreach ($marcas as $marca)
            {
                $arr_encontrados[]=array('idMarcaElemento' =>$marca->getId(),
                                         'nombreMarcaElemento' =>trim($marca->getNombreMarcaElemento()),
                                         'estado' =>(trim($marca->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($marca->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($marca->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 
                                                        'nombreConectorInterface' => 'Ninguno',
                                                        'idConectorInterface' => 0 , 
                                                        'nombreConectorInterface' => 'Ninguno', 
                                                        'estado' => 'Ninguno'));
                $resultado = json_encode( $resultado);

                return $resultado;
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $resultado= '{"total":"'.$num.'","encontrados":'.$data.'}';

                return $resultado;
            }
        }//if ($marcas) 
        else
        {
            $resultado= '{"total":"0","encontrados":[]}';

            return $resultado;
        }
        
    }
    
    /**
     * Funcion que arma y ejecuta el query para
     * obtener las marcas de los cpes (CPE, CPE WIFI, CPE ONT)
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-05-2014
     * @param String    $tipoElemento   tipo elemento
     * @param String    $estado         estado de la marca
     * @param int       $start          numero de inicio para el limit
     * @param int       $limit          numero de fin para el limit
     */
    public function getMarcasElementosPorTipoCpe($tipoElemento,$estado,$start,$limit)
    {
        $qb = $this->_em->createQueryBuilder();
            $qb->select('admi_marca_elemento')
               ->from('schemaBundle:AdmiModeloElemento','admi_modelo_elemento')
                ->from('schemaBundle:AdmiMarcaElemento','admi_marca_elemento')
                ->from('schemaBundle:AdmiTipoElemento','admiTipoElemento');
        
        if($tipoElemento!="")
        {
            $qb->andWhere( 'admi_modelo_elemento.tipoElementoId = admiTipoElemento')
               ->andWhere( 'admi_modelo_elemento.marcaElementoId = admi_marca_elemento')
               ->andWhere( 'admiTipoElemento.nombreTipoElemento like ?1');
            $qb->setParameter(1, $tipoElemento."%");
        }
        if($estado!="Todos")
        {
            $qb ->andWhere('admi_marca_elemento.estado = ?2');
            $qb->setParameter(2, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function generarJsonMarcasElementos($nombre,$estado,$start,$limit){
        $arr_encontrados = array();
        
        $marcasTotal = $this->getMarcasElementos($nombre,$estado,'','');
        
        $marcas = $this->getMarcasElementos($nombre,$estado,$start,$limit);
//        error_log('entra');
        if ($marcas) {
            
            $num = count($marcasTotal);
            
            foreach ($marcas as $marca)
            {
                $arr_encontrados[]=array('idMarcaElemento' =>$marca->getId(),
                                         'nombreMarcaElemento' =>trim($marca->getNombreMarcaElemento()),
                                         'estado' =>(trim($marca->getEstado())=='Eliminado' ? 'Eliminado':'Activo'),
                                         'action1' => 'button-grid-show',
                                         'action2' => (trim($marca->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3' => (trim($marca->getEstado())=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'));
            }

            if($num == 0)
            {
               $resultado= array('total' => 1 ,
                                 'encontrados' => array('idConectorInterface' => 0 , 
                                                        'nombreConectorInterface' => 'Ninguno',
                                                        'idConectorInterface' => 0 , 
                                                        'nombreConectorInterface' => 'Ninguno', 
                                                        'estado' => 'Ninguno'));
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
   
    public function getMarcasElementos($nombre,$estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiMarcaElemento','e');
               
            
        if($nombre!=""){
            $qb ->where( 'e.nombreMarcaElemento like ?1');
            $qb->setParameter(1, '%'.$nombre.'%');
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
}
