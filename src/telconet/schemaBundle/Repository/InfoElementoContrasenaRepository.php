<?php

namespace telconet\schemaBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Clase que sirve para las funciones que ejecutan sql sobre
 * la entidad InfoElementoContraseña
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 11-02-2015
 */
class InfoElementoContrasenaRepository extends EntityRepository
{
    /**
     * Funcion que genera un json con los elementos filtrados por el nombre y el estado del elemento
     * 
     * @param $nombreElemento   string
     * @param $estado           string
     * @param $start            int
     * @param $limit            int
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-02-2015
     */
    public function generarJsonElementoContrasena($nombreElemento, $estado, $start, $limit)
    {
        $arr_encontrados = array();

        $result = $this->getElementoContrasena($nombreElemento, $estado, $start, $limit);

        $encontrados        = $result['registros'];
        $encontradosTotal   = $result['total'];
        
        if($encontrados)
        {
            $num = $encontradosTotal;
            foreach($encontrados as $arreglo)
            {
                $arr_encontrados[] = array( 'idElementoContrasena'  => $arreglo['id'],
                                            'nombreElemento'        => $arreglo['nombreElemento'],
                                            'nombreModeloElemento'  => $arreglo['nombreModeloElemento'],
                                            'nombreTipoElemento'    => $arreglo['nombreTipoElemento'],
                                            'contrasena'            => $arreglo['contrasena'],
                                            'estado'                => $arreglo['estado'],
                                            'action1'               => 'button-grid-show',
                                            'action2' => ($arreglo['estado'] == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-edit'),
                                            'action3' => ($arreglo['estado'] == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-delete'),
                                            'action4' => ($arreglo['estado'] == 'Eliminado' ? 'button-grid-invisible' : 'button-grid-cambiarEstado')
                );
            }

            if($num == 0)
            {
                $resultado = array('total' => 1,
                    'encontrados' => array('idUsuarioAcceso' => 0,
                        'nombreUsuarioAcceso' => 'Ninguno',
                        'estado' => 'Ninguno'));
                $resultado = json_encode($resultado);

                return $resultado;
            }
            else
            {
                $data = json_encode($arr_encontrados);
                $resultado = '{"total":"' . $num . '","encontrados":' . $data . '}';

                return $resultado;
            }
        }
        else
        {
            $resultado = '{"total":"0","encontrados":[]}';

            return $resultado;
        }
    }

    /**
     * Funcion que genera y ejecuta un sql por los filtros de nombre y el estado del elemento
     * 
     * @param $nombreElemento   string
     * @param $estado           string
     * @param $start            int
     * @param $limit            int
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-02-2015
     */
    public function getElementoContrasena($nombreElemento, $estado, $start, $limit)
    {
        $qb = $this->_em->createQueryBuilder();
        $qbC = $this->_em->createQueryBuilder();
        //query para obtener la informacion
        $qb->select('InfoElemento.nombreElemento,'
                . 'InfoElemento.estado,'
                . 'AdmiModeloElemento.nombreModeloElemento,'
                . 'AdmiTipoElemento.nombreTipoElemento,'
                . 'InfoElementoContrasena.contrasena,'
                . 'InfoElementoContrasena.id')
            ->from('schemaBundle:InfoElemento',             'InfoElemento')
            ->from('schemaBundle:InfoElementoContrasena',   'InfoElementoContrasena')
            ->from('schemaBundle:AdmiModeloElemento',       'AdmiModeloElemento')
            ->from('schemaBundle:AdmiTipoElemento',         'AdmiTipoElemento')
            ->where('InfoElemento = InfoElementoContrasena.elementoId')
            ->andWhere('InfoElemento.modeloElementoId = AdmiModeloElemento')
            ->andWhere('AdmiModeloElemento.tipoElementoId = AdmiTipoElemento')
            ->andWhere("InfoElementoContrasena.estado = 'Activo'");

        //query para obtener la cantidad de registros
        $qbC->select('count(InfoElemento.id)')
            ->from('schemaBundle:InfoElemento',             'InfoElemento')
            ->from('schemaBundle:InfoElementoContrasena',   'InfoElementoContrasena')
            ->from('schemaBundle:AdmiModeloElemento',       'AdmiModeloElemento')
            ->from('schemaBundle:AdmiTipoElemento',         'AdmiTipoElemento')
            ->where('InfoElemento = InfoElementoContrasena.elementoId')
            ->andWhere('InfoElemento.modeloElementoId = AdmiModeloElemento')
            ->andWhere('AdmiModeloElemento.tipoElementoId = AdmiTipoElemento')
            ->andWhere("InfoElementoContrasena.estado = 'Activo'");

        if($nombreElemento != "")
        {
            $qb->where('UPPER(InfoElemento.nombreElemento) like ?1');
            $qb->setParameter(1, "%" . strtoupper($nombreElemento) . "%");
            $qbC->andWhere('UPPER(InfoElemento.nombreElemento) like ?1');
            $qbC->setParameter(1, "%" . strtoupper($nombreElemento) . "%");
        }
        if($estado != "Todos")
        {
            $qb->where('InfoElemento.estado = ?2');
            $qb->setParameter(2, $estado);
            $qbC->andWhere('InfoElemento.estado = ?2');
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
        $resultado['registros'] = $datos;
        $resultado['total'] = $total;

        return $resultado;
    }

}
