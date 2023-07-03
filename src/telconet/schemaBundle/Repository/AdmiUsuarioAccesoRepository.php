<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AdmiUsuarioAccesoRepository extends EntityRepository
{
   public function generarJsonUsuarios($estado,$start,$limit){
        $arr_encontrados = array();
        
        $encontradosTotal = $this->getUsuarios($estado,'','');
        
        $encontrados = $this->getUsuarios($estado,$start,$limit);
//        error_log('entra');
        if ($encontrados) {
            
            $num = count($encontradosTotal);
            
            foreach ($encontrados as $entidad)
            {
                $arr_encontrados[]=array('idUsuarioAcceso' =>$entidad->getId(),
                                         'nombreUsuarioAcceso' =>trim($entidad->getNombreUsuarioAcceso()));
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
   
    public function getUsuarios($estado,$start,$limit){
        $qb = $this->_em->createQueryBuilder();
            $qb->select('e')
               ->from('schemaBundle:AdmiUsuarioAcceso','e');
               
        if($estado!="Todos"){
            $qb ->andWhere('e.estado = ?1');
            $qb->setParameter(1, $estado);
        }
        if($start!='')
            $qb->setFirstResult($start);   
        if($limit!='')
            $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    /**
     * Funcion que sirve para generar un json de los usuarios
     * los cuales fueron buscados por filtros
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @param $nombreUsuario    String
     * @param $estado           String
     * @param $start            int
     * @param $limit            int
     */
    public function generarJsonUsuariosAcceso($nombreUsuario,$estado,$start,$limit)
    {
        $arr_encontrados = array();
        
        $result= $this->getUsuariosAcceso($nombreUsuario,$estado,$start,$limit);
        
        $encontrados        = $result['registros'];
        $encontradosTotal   = $result['total'];
        
        if ($encontrados) {
            $num = $encontradosTotal;
            foreach ($encontrados as $arreglo)
            {
                $arr_encontrados[]=array('idUsuarioAcceso'      => $arreglo['id'],
                                         'nombreUsuarioAcceso'  => $arreglo['nombreUsuarioAcceso'],
                                         'estado'               => $arreglo['estado'],
                                         'action1'              => 'button-grid-show',
                                         'action2'              => ($arreglo['estado']=='Eliminado' ? 'button-grid-invisible':'button-grid-edit'),
                                         'action3'              => ($arreglo['estado']=='Eliminado' ? 'button-grid-invisible':'button-grid-delete'),
                                         'action4'              => ($arreglo['estado']=='Eliminado' ? 
                                                                    'button-grid-invisible':'button-grid-cambiarEstado')
                                        );
            }

            if($num == 0)
            {
                $resultado= array('total'      => 1 ,
                                 'encontrados' => array('idUsuarioAcceso'       => 0 , 
                                                        'nombreUsuarioAcceso'   => 'Ninguno',
                                                        'estado'                => 'Ninguno'));
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
     * Funcion que sirve para generar un sql y ejecutarlo para
     * que obtenga la informacion de los usuarios de la base de datos
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @param $nombreUsuario    String
     * @param $estado           String
     * @param $start            int
     * @param $limit            int
     */
    public function getUsuariosAcceso($nombreUsuario,$estado,$start,$limit)
    {
        $qb  = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        
        //query para obtener la informacion
        $qb ->select( 'AdmiUsuarioAcceso.id, '
                    . 'AdmiUsuarioAcceso.nombreUsuarioAcceso,'
                    . 'AdmiUsuarioAcceso.estado')
            ->from('schemaBundle:AdmiUsuarioAcceso', 'AdmiUsuarioAcceso');

        //query para obtener la cantidad de registros
        $qbC->select('count(AdmiUsuarioAcceso.id)')
            ->from('schemaBundle:AdmiUsuarioAcceso', 'AdmiUsuarioAcceso');

        if($nombreUsuario != "")
        {
            $qb->where('UPPER(AdmiUsuarioAcceso.nombreUsuarioAcceso) like ?1');
            $qb->setParameter(1, "%".strtoupper($nombreUsuario)."%");
            
            $qbC->andWhere('UPPER(AdmiUsuarioAcceso.nombreUsuarioAcceso) like ?1');
            $qbC->setParameter(1, "%".strtoupper($nombreUsuario)."%");
        }
        if($estado != "Todos")
        {
            $qb->where('AdmiUsuarioAcceso.estado = ?2');
            $qb->setParameter(2, $estado);
            
            $qbC->andWhere('AdmiUsuarioAcceso.estado = ?2');
            $qbC->setParameter(2, $estado);
        }

        if($start != '')
            $qb->setFirstResult($start);
        if($limit != '')
            $qb->setMaxResults($limit);
        
        //total de objetos
        $total = $qbC->getQuery()->getSingleScalarResult();
        
        //obtener los objetos
        $query = $qb->getQuery();
        $datos = $query->getResult();
        
        $resultado['registros']=$datos;
        $resultado['total']=$total;

        return $resultado;
    }
    
    /**
     * Funcion que sirve para generar un json de la relacion
     * entre un usuario y los modelos a los cuales se los ha
     * asociado
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @param $idUsuario        int
     * @param $start            int
     * @param $limit            int
     */
    public function generarJsonRelacionUsuarioModelo($idUsuario,$start,$limit)
    {
        $arr_encontrados = array();
        
        $result= $this->getRelacionUsuarioModelo($idUsuario,$start,$limit);
        
        $encontrados        = $result['registros'];
        $encontradosTotal   = $result['total'];
        
        if ($encontrados) {
            $num = $encontradosTotal;
            foreach ($encontrados as $arreglo)
            {
                $arr_encontrados[]=array('nombreUsuarioAcceso'  => $arreglo['nombreUsuarioAcceso'],
                                         'nombreModeloElemento' => $arreglo['nombreModeloElemento'],
                                         'nombreTipoElemento'   => $arreglo['nombreTipoElemento']
                                        );
            }

            if($num == 0)
            {
                $resultado= array('total'      => 1 ,
                                 'encontrados' => array('nombreUsuarioAcceso'   => 'Ninguno' , 
                                                        'nombreModeloElemento'  => 'Ninguno',
                                                        'nombreTipoElemento'    => 'Ninguno'));
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
     * Funcion que sirve para gener un sql y ejecutarlo para que
     * obtenga la informacion de la relacion entre un usuario y el modelo
     * 
     * @author creado       Francisco Adum <fadum@telconet.ec>
     * @version 1.0 5-02-2015
     * @param $idUsuario        int
     * @param $start            int
     * @param $limit            int
     */
    public function getRelacionUsuarioModelo($idUsuario,$start,$limit)
    {
        $qb  = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        
        //query para obtener la informacion
        $qb ->select( 'AdmiUsuarioAcceso.id, '
                    . 'AdmiUsuarioAcceso.nombreUsuarioAcceso,'
                    . 'AdmiUsuarioAcceso.estado,'
                    . 'AdmiModeloUsuarioAcceso.esPreferencia,'
                    . 'AdmiModeloElemento.nombreModeloElemento,'
                    . 'AdmiTipoElemento.nombreTipoElemento')
            ->from('schemaBundle:AdmiUsuarioAcceso',        'AdmiUsuarioAcceso')
            ->from('schemaBundle:AdmiModeloUsuarioAcceso',  'AdmiModeloUsuarioAcceso')
            ->from('schemaBundle:AdmiModeloElemento',       'AdmiModeloElemento')
            ->from('schemaBundle:AdmiTipoElemento',         'AdmiTipoElemento')
            ->where('AdmiModeloUsuarioAcceso.usuarioAccesoId        = AdmiUsuarioAcceso')
            ->andWhere('AdmiModeloUsuarioAcceso.modeloElementoId    = AdmiModeloElemento')
            ->andWhere('AdmiModeloElemento.tipoElementoId           = AdmiTipoElemento')
            ->andWhere('AdmiUsuarioAcceso                           = ?1')
            ->setParameter(1, $idUsuario);

        //query para obtener la cantidad de registros
        $qbC->select('count(AdmiUsuarioAcceso.id)')
            ->from('schemaBundle:AdmiUsuarioAcceso',        'AdmiUsuarioAcceso')
            ->from('schemaBundle:AdmiModeloUsuarioAcceso',  'AdmiModeloUsuarioAcceso')
            ->from('schemaBundle:AdmiModeloElemento',       'AdmiModeloElemento')
            ->from('schemaBundle:AdmiTipoElemento',         'AdmiTipoElemento')
            ->where('AdmiModeloUsuarioAcceso.usuarioAccesoId        = AdmiUsuarioAcceso')
            ->andWhere('AdmiModeloUsuarioAcceso.modeloElementoId    = AdmiModeloElemento')
            ->andWhere('AdmiModeloElemento.tipoElementoId           = AdmiTipoElemento')
            ->andWhere('AdmiUsuarioAcceso                           = ?1')
            ->setParameter(1, $idUsuario);

        if($start != '')
            $qb->setFirstResult($start);
        if($limit != '')
            $qb->setMaxResults($limit);
        
        //total de objetos
        $total = $qbC->getQuery()->getSingleScalarResult();
        
        //obtener los objetos
        $query = $qb->getQuery();
        $datos = $query->getResult();
        
        $resultado['registros']=$datos;
        $resultado['total']=$total;

        return $resultado;
    }
}
